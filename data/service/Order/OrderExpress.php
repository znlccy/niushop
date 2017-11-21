<?php
/**
 * OrderExpress.php
 *
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace data\service\Order;

use data\model\NsOrderGoodsExpressModel;
use data\model\UserModel;
use data\model\NsOrderExpressCompanyModel;
use data\service\BaseService;

/**
 * 订单项物流操作类
 */
class OrderExpress extends BaseService
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 物流公司发货
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id_array
     *            订单项数组
     * @param unknown $express_name
     *            包裹名称
     * @param unknown $shipping_type
     *            发货方式1 需要物流 0无需物流
     * @param unknown $express_company_id
     *            物流公司ID
     * @param unknown $express_no
     *            物流单号
     */
    public function delivey($order_id, $order_goods_id_array, $express_name, $shipping_type, $express_company_id, $express_no)
    {
        $user = new UserModel();
        $user_name = $user->getInfo([
            'uid' => $this->uid
        ], 'user_name');
        $order_express = new NsOrderGoodsExpressModel();
        $order_express->startTrans();
        try {
            $count = $order_express->getCount([
                'order_goods_id_array' => $order_goods_id_array
            ]);
            if ($count == 0) {
                
                $express_company = new NsOrderExpressCompanyModel();
                $express_company_info = $express_company->getInfo([
                    'co_id' => $express_company_id
                ], 'company_name');
                $data_goods_delivery = array(
                    'order_id' => $order_id,
                    'order_goods_id_array' => $order_goods_id_array,
                    'express_name' => $express_name,
                    'shipping_type' => $shipping_type,
                    'express_company' => $express_company_info['company_name'],
                    'express_company_id' => $express_company_id,
                    'express_no' => $express_no,
                    'shipping_time' => time(),
                    'uid' => $this->uid,
                    'user_name' => $user_name['user_name']
                );
                $order_express->save($data_goods_delivery);
                // 循环添加到订单商品项
                $order_goods = new OrderGoods();
                $order_goods->orderGoodsDelivery($order_id, $order_goods_id_array);
                runhook("Notify", "orderDelivery", array(
                    "order_goods_ids" => $order_goods_id_array
                ));
                $order_express->commit();
            }
            return 1;
        } catch (\Exception $e) {
            $order_express->rollback();
            return $e->getMessage();
        }
    }
}