<?php
/**
 * AuthGroup.php
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
 * @date : 2015.4.24
 * @version : v1.0.0.0
 */
namespace data\service;

use data\api\IVirtualGoods;
use data\model\NsVirtualGoodsModel;
use data\model\NsVirtualGoodsTypeModel;
use data\service\BaseService as BaseService;

class VirtualGoods extends BaseService implements IVirtualGoods
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IVirtualGoods::editVirtualGoodsGroup()
     */
    public function editVirtualGoodsGroup($virtual_goods_group_name, $interfaces, $create_time)
    {
        // TODO Auto-generated method stub
    }

    /**
     * 获取虚拟商品类型列表
     *
     * @param 当前页 $page_index            
     * @param 显示页数 $page_size            
     * @param 条件 $condition            
     * @param 排序 $order            
     * @param 字段 $field            
     */
    function getVirtualGoodsTypeList($page_index, $page_size = 0, $condition = array(), $order = "virtual_goods_type_id desc", $field = "*")
    {
        $virtual_goods_type_model = new NsVirtualGoodsTypeModel();
        $res = $virtual_goods_type_model->pageQuery($page_index, $page_size, $condition, $order, $field);
        return $res;
    }

    /**
     * 根据id查询虚拟商品类型
     *
     * {@inheritdoc}
     *
     * @see \data\api\IVirtualGoods::getVirtualGoodsTypeById()
     */
    function getVirtualGoodsTypeById($virtual_goods_type_id)
    {
        $virtual_goods_type_model = new NsVirtualGoodsTypeModel();
        $res = $virtual_goods_type_model->getInfo([
            'virtual_goods_type_id' => $virtual_goods_type_id
        ], "*");
        return $res;
    }

    /**
     * 编辑虚拟商品类型
     *
     * {@inheritdoc}
     *
     * @see \data\api\IVirtualGoods::editVirtualGoodsType()
     */
    public function editVirtualGoodsType($virtual_goods_type_id, $virtual_goods_group_id, $virtual_goods_type_name, $validity_period, $is_enabled, $money, $config_info, $confine_use_number)
    {
        $virtual_goods_type_model = new NsVirtualGoodsTypeModel();
        $res = 0;
        if ($virtual_goods_type_id == 0) {
            
            // 添加
            $data = array(
                'virtual_goods_group_id' => $virtual_goods_group_id,
                'virtual_goods_type_name' => $virtual_goods_type_name,
                'validity_period' => $validity_period,
                'is_enabled' => $is_enabled,
                'config_info' => $config_info,
                'money' => $money,
                'confine_use_number' => $confine_use_number,
                'shop_id' => $this->instance_id,
                'create_time' => time()
            );
            $res = $virtual_goods_type_model->save($data);
        } else {
            
            // 修改
            $data = array(
                'virtual_goods_group_id' => $virtual_goods_group_id,
                'virtual_goods_type_name' => $virtual_goods_type_name,
                'validity_period' => $validity_period,
                'is_enabled' => $is_enabled,
                'config_info' => $config_info,
                'money' => $money,
                'confine_use_number' => $confine_use_number
            );
            $res = $virtual_goods_type_model->save($data, [
                'virtual_goods_type_id' => $virtual_goods_type_id
            ]);
        }
        return $res;
    }

    /**
     * 设置虚拟商品类型启用禁用
     * 创建时间：2017年11月23日 19:37:28 王永杰
     *
     * {@inheritdoc}
     *
     * @see \data\api\IVirtualGoods::setVirtualGoodsTypeIsEnabled()
     */
    public function setVirtualGoodsTypeIsEnabled($virtual_goods_type_id, $is_enabled)
    {
        $virtual_goods_type_model = new NsVirtualGoodsTypeModel();
        $data['is_enabled'] = $is_enabled;
        $res = $virtual_goods_type_model->save($data, [
            'virtual_goods_type_id' => $virtual_goods_type_id
        ]);
        return $res;
    }

    /**
     * 根据id删除虚拟商品类型
     * 创建时间：2017年11月23日 19:37:19 王永杰
     *
     * {@inheritdoc}
     *
     * @see \data\api\IVirtualGoods::deleteVirtualGoodsType()
     */
    public function deleteVirtualGoodsType($virtual_goods_type_id)
    {
        $virtual_goods_type_model = new NsVirtualGoodsTypeModel();
        $res = $virtual_goods_type_model->destroy([
            'virtual_goods_type_id' => [
                'in',
                $virtual_goods_type_id
            ]
        ]);
        return $res;
    }

    /**
     * 添加虚拟商品
     * 创建时间：2017年11月23日 19:37:08 王永杰
     *
     * {@inheritdoc}
     *
     * @see \data\api\IVirtualGoods::addVirtualGoods()
     */
    public function addVirtualGoods($shop_id, $virtual_goods_name, $money, $buyer_id, $buyer_nickname, $order_goods_id, $order_no, $validity_period, $start_time, $end_time, $use_number, $confine_use_number, $use_status)
    {
        $virtual_goods_model = new NsVirtualGoodsModel();
        
        $data = array(
            'virtual_code' => $this->generateVirtualCode($shop_id),
            'virtual_goods_name' => $virtual_goods_name,
            'money' => $money,
            'buyer_id' => $buyer_id,
            'buyer_nickname' => $buyer_nickname,
            'order_goods_id' => $order_goods_id,
            'order_no' => $order_no,
            'validity_period' => $validity_period,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'use_number' => $use_number,
            'confine_use_number' => $confine_use_number,
            'use_status' => $use_status,
            'shop_id' => $shop_id,
            'create_time' => time()
        );
        
        $res = $virtual_goods_model->save($data);
        return $res;
    }

    /**
     * 生成虚拟码
     * 创建时间：2017年11月23日 19:37:03 王永杰
     */
    public function generateVirtualCode($shop_id)
    {
        $code = '';
        $time_str = date('YmdHs');
        $virtual_goods_model = new NsVirtualGoodsModel();
        $order_obj = $virtual_goods_model->getFirstData([
            "shop_id" => $shop_id
        ], "virtual_goods_id DESC");
        $num = 0;
        if (! empty($order_obj)) {
            $order_no_max = $order_obj["virtual_code"];
            if (empty($order_no_max)) {
                $num = 1;
            } else {
                if (substr($time_str, 0, 12) == substr($order_no_max, 0, 12)) {
                    $max_no = substr($order_no_max, 12, 4);
                    $num = $max_no * 1 + 1;
                } else {
                    $num = 1;
                }
            }
        } else {
            $num = 1;
        }
        $virtual_code = $time_str . sprintf("%04d", $num);
        $count = $virtual_goods_model->getCount(['virtual_code'=>$virtual_code]);
        if($count>0){
            return $this->generateVirtualCode($shop_id);
        }
        return $virtual_code;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IVirtualGoods::deleteVirtualGoods()
     */
    public function deleteVirtualGoods($virtual_code)
    {
        // TODO Auto-generated method stub
    }

    /**
     * 根据订单编号查询虚拟商品列表
     *
     * {@inheritdoc}
     *
     * @see \data\api\IVirtualGoods::getVirtualGoodsListByOrderNo()
     */
    function getVirtualGoodsListByOrderNo($order_no)
    {
        $virtual_goods_model = new NsVirtualGoodsModel();
        $list = $virtual_goods_model->getQuery([
            "order_no" => $order_no
        ], "*", "virtual_goods_id asc");
        if (! empty($list)) {
            
            foreach ($list as $k => $v) {
                if ($v['use_status'] == - 1) {
                    $list[$k]['use_status_msg'] = '已过期';
                } elseif ($v['use_status'] == 0) {
                    $list[$k]['use_status_msg'] = '未使用';
                } elseif ($v['use_status'] == 1) {
                    $list[$k]['use_status_msg'] = '已使用';
                }
            }
            return $list;
        }
        return '';
    }
}