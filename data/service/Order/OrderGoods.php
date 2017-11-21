<?php
/**
 * OrderGoods.php
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

use data\model\AlbumPictureModel;
use data\model\NsGoodsModel;
use data\model\NsGoodsSkuModel;
use data\model\NsGoodsSkuPictureModel;
use data\model\NsOrderGoodsModel;
use data\model\NsOrderGoodsPromotionDetailsModel;
use data\model\NsOrderModel;
use data\model\NsOrderRefundModel;
use data\model\UserModel;
use data\service\BaseService;
use data\service\GoodsCalculate\GoodsCalculate;
use data\service\Member\MemberAccount;
use data\service\Order\OrderStatus;
use data\service\promotion\GoodsPreference;
use data\model\NsOrderGoodsViewModel;
use data\model\NsOrderGoodsExpressModel;
use data\service\Order as OrderService;
// use think\Model;
/**
 * 订单商品操作类
 */
class OrderGoods extends BaseService
{

    public $order_goods;
    // 订单主表
    function __construct()
    {
        parent::__construct();
        $this->order_goods = new NsOrderGoodsModel();
    }

    /**
     * 订单创建添加订单项
     * order_goods_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '订单项ID',
     * order_id int(11) NOT NULL COMMENT '订单ID',
     * goods_id int(11) NOT NULL COMMENT '商品ID',
     * goods_name varchar(50) NOT NULL COMMENT '商品名称',
     * sku_id int(11) NOT NULL COMMENT 'skuID',
     * sku_name varchar(50) NOT NULL COMMENT 'sku名称',
     * price decimal(19, 2) NOT NULL DEFAULT 0.00 COMMENT '商品价格',
     * num varchar(255) NOT NULL DEFAULT '0' COMMENT '购买数量',
     * adjust_money varchar(255) NOT NULL DEFAULT '0' COMMENT '调整金额',
     * goods_money varchar(255) NOT NULL DEFAULT '0' COMMENT '商品总价',
     * goods_picture int(11) NOT NULL DEFAULT 0 COMMENT '商品图片',
     * shop_id int(11) NOT NULL DEFAULT 1 COMMENT '店铺ID',
     * buyer_id int(11) NOT NULL DEFAULT 0 COMMENT '购买人ID',
     * goods_type varchar(255) NOT NULL DEFAULT '1' COMMENT '商品类型',
     * promotion_id int(11) NOT NULL DEFAULT 0 COMMENT '促销ID',
     * promotion_type_id int(11) NOT NULL DEFAULT 0 COMMENT '促销类型',
     * order_type int(11) NOT NULL DEFAULT 1 COMMENT '订单类型',
     * order_status int(2) NOT NULL DEFAULT 0 COMMENT '订单状态',
     * give_point int(2) NOT NULL DEFAULT 0 COMMENT '积分数量',
     * shipping_status int(2) NOT NULL DEFAULT 0 COMMENT '物流状态',
     * refund_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '退款时间',
     * refund_type int(11) NOT NULL DEFAULT 1 COMMENT '退款方式',
     * refund_require_money decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '退款金额',
     * refund_reason varchar(255) NOT NULL DEFAULT '' COMMENT '退款原因',
     * refund_shipping_code varchar(255) NOT NULL DEFAULT '' COMMENT '退款物流单号',
     * refund_shipping_company int(11) NOT NULL DEFAULT 0 COMMENT '退款物流公司名称',
     * refund_real_money decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '实际退款金额',
     * refund_status int(1) NOT NULL DEFAULT 0 COMMENT '退款状态',
     * memo varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
     * PRIMARY KEY (order_goods_id)
     *
     * @param unknown $goods_sku_list            
     */
    public function addOrderGoods($order_id, $goods_sku_list, $adjust_money = 0)
    {
        $this->order_goods->startTrans();
        try {
            $err = 0;
            $goods_sku_list_array = explode(",", $goods_sku_list);
            foreach ($goods_sku_list_array as $k => $goods_sku_array) {
                
                $goods_sku = explode(':', $goods_sku_array);
                $goods_sku_model = new NsGoodsSkuModel();
                $goods_sku_info = $goods_sku_model->getInfo([
                    'sku_id' => $goods_sku[0]
                ], 'sku_id,goods_id,cost_price,stock,sku_name,attr_value_items');
                
                // 如果当前商品有SKU图片，就用SKU图片。没有则用商品主图 2017年9月19日 15:46:38（王永杰）
                $picture = $this->getSkuPictureBySkuId($goods_sku_info);
                
                $goods_model = new NsGoodsModel();
                $goods_info = $goods_model->getInfo([
                    'goods_id' => $goods_sku_info['goods_id']
                ], 'goods_name,price,goods_type,picture,promotion_type,promote_id,point_exchange_type,give_point');
                
                $goods_promote = new GoodsPreference();
                $sku_price = $goods_promote->getGoodsSkuPrice($goods_sku_info['sku_id']);
                $goods_promote_info = $goods_promote->getGoodsPromote($goods_sku_info['goods_id']);
                if (empty($goods_promote_info)) {
                    $goods_info['promotion_type'] = 0;
                    $goods_info['promote_id'] = 0;
                }
                if ($goods_sku_info['stock'] < $goods_sku[1] || $goods_sku[1] <= 0) {
                    $this->order_goods->rollback();
                    return LOW_STOCKS;
                }
                $give_point = $goods_sku[1] * $goods_info["give_point"];
                
                // 库存减少销量增加
                $goods_calculate = new GoodsCalculate();
                $goods_calculate->subGoodsStock($goods_sku_info['goods_id'], $goods_sku_info['sku_id'], $goods_sku[1], '');
                $goods_calculate->addGoodsSales($goods_sku_info['goods_id'], $goods_sku_info['sku_id'], $goods_sku[1]);
                $data_order_sku = array(
                    'order_id' => $order_id,
                    'goods_id' => $goods_sku_info['goods_id'],
                    'goods_name' => $goods_info['goods_name'],
                    'sku_id' => $goods_sku_info['sku_id'],
                    'sku_name' => $goods_sku_info['sku_name'],
                    'price' => $sku_price,
                    'num' => $goods_sku[1],
                    'adjust_money' => $adjust_money,
                    'cost_price' => $goods_sku_info['cost_price'],
                    'goods_money' => $sku_price * $goods_sku[1] - $adjust_money,
                    'goods_picture' => $picture != 0 ? $picture : $goods_info['picture'], // 如果当前商品有SKU图片，就用SKU图片。没有则用商品主图
                    'shop_id' => $this->instance_id,
                    'buyer_id' => $this->uid,
                    'goods_type' => $goods_info['goods_type'],
                    'promotion_id' => $goods_info['promote_id'],
                    'promotion_type_id' => $goods_info['promotion_type'],
                    'point_exchange_type' => $goods_info['point_exchange_type'],
                    'order_type' => 1, // 订单类型默认1
                    'give_point' => $give_point
                ); // 积分数量默认0
                
                if ($goods_sku[1] == 0) {
                    $err = 1;
                }
                $order_goods = new NsOrderGoodsModel();
                
                $order_goods->save($data_order_sku);
            }
            if ($err == 0) {
                $this->order_goods->commit();
                return 1;
            } elseif ($err == 1) {
                $this->order_goods->rollback();
                return ORDER_GOODS_ZERO;
            }
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 根据商品规格信息查询SKU主图片
     * 创建时间：2017年9月19日 15:43:06
     * 王永杰
     *
     * @param 商品规格信息 $goods_sku_info            
     * @return 0：没有查询到商品SKU图片，!0:查询到了商品SKU图片
     */
    public function getSkuPictureBySkuId($goods_sku_info)
    {
        $picture = 0;
        $attr_value_items = $goods_sku_info['attr_value_items'];
        if (! empty($attr_value_items)) {
            $attr_value_items_array = explode(";", $attr_value_items);
            foreach ($attr_value_items_array as $k => $v) {
                $temp_array = explode(":", $v); // 规格：规格值
                $condition['goods_id'] = $goods_sku_info['goods_id'];
                $condition['spec_id'] = $temp_array[0]; // 规格
                $condition['spec_value_id'] = $temp_array[1]; // 规格值
                $condition['shop_id'] = $this->instance_id;
                $goods_sku_picture_model = new NsGoodsSkuPictureModel();
                $sku_img_array = $goods_sku_picture_model->getInfo($condition, 'sku_img_array');
                if (! empty($sku_img_array['sku_img_array'])) {
                    $temp = explode(",", $sku_img_array['sku_img_array']);
                    $picture = $temp[0];
                    break;
                }
            }
        }
        
        return $picture;
    }

    /**
     * 订单项发货
     *
     * @param unknown $order_goods_id_array
     *            ','隔开
     */
    public function orderGoodsDelivery($order_id, $order_goods_id_array)
    {
        $this->order_goods->startTrans();
        try {
            $order_goods_id_array = explode(',', $order_goods_id_array);
            foreach ($order_goods_id_array as $k => $order_goods_id) {
                $order_goods_id = (int) $order_goods_id;
                $data = array(
                    'shipping_status' => 1
                );
                $order_goods = new NsOrderGoodsModel();
                $retval = $order_goods->save($data, [
                    'order_goods_id' => $order_goods_id
                ]);
            }
            
            $order = new Order();
            $order->orderDoDelivery($order_id);
            $this->order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e->getMessage();
        }
        
        return $retval;
    }

    /**
     * 买家退款申请
     *
     * @param unknown $order_id
     *            订单ID
     * @param unknown $order_goods_id_array
     *            订单项ID (','隔开)
     * @param unknown $refund_type            
     * @param unknown $refund_require_money            
     * @param unknown $refund_reason            
     * @return number|Exception|Ambigous <number, \think\false>
     */
    public function orderGoodsRefundAskfor($order_id, $order_goods_id, $refund_type, $refund_require_money, $refund_reason)
    {
        $this->order_goods->startTrans();
        try {
            $status_id = OrderStatus::getRefundStatus()[0]['status_id'];
            // 订单项退款操作
            $order_goods = new NsOrderGoodsModel();
            $order_goods_data = array(
                'refund_status' => $status_id,
                'refund_time' => time(),
                'refund_type' => $refund_type,
                'refund_require_money' => $refund_require_money,
                'refund_reason' => $refund_reason
            );
            $res = $order_goods->save($order_goods_data, [
                'order_goods_id' => $order_goods_id
            ]);
            
            // 退款记录
            $this->addOrderRefundAction($order_goods_id, $status_id, 1, $this->uid);
            // 订单退款操作
            $order = new Order();
            $res = $order->orderGoodsRefundFinish($order_id);
            
            $this->order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 买家取消退款
     */
    public function orderGoodsCancel($order_id, $order_goods_id)
    {
        $this->order_goods->startTrans();
        try {
            $status_id = OrderStatus::getRefundStatus()[6]['status_id'];
            
            // 订单项退款操作
            $order_goods = new NsOrderGoodsModel();
            $order_goods_data = array(
                'refund_status' => $status_id
            );
            $res = $order_goods->save($order_goods_data, [
                'order_goods_id' => $order_goods_id,
                'buyer_id' => $this->uid
            ]);
            
            // 退款记录
            $this->addOrderRefundAction($order_goods_id, $status_id, 1, $this->uid);
            // 订单退款操作
            $order = new Order();
            $res = $order->orderGoodsRefundFinish($order_id);
            
            $this->order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 买家退货
     */
    public function orderGoodsReturnGoods($order_id, $order_goods_id, $refund_shipping_company, $refund_shipping_code)
    {
        $order_goods = NsOrderGoodsModel::get($order_goods_id);
        $order_goods->startTrans();
        try {
            $status_id = OrderStatus::getRefundStatus()[2]['status_id'];
            
            // 订单项退款操作
            $order_goods->refund_status = $status_id;
            $order_goods->refund_shipping_company = $refund_shipping_company;
            $order_goods->refund_shipping_code = $refund_shipping_code;
            $retval = $order_goods->save();
            
            // 退款记录
            $this->addOrderRefundAction($order_goods_id, $status_id, 1, $this->uid);
            
            $order_goods->commit();
            return $retval;
        } catch (\Exception $e) {
            $order_goods->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 卖家同意买家退款申请
     */
    public function orderGoodsRefundAgree($order_id, $order_goods_id)
    {
        $this->order_goods->startTrans();
        try {
            
            // 退款信息
            $refund_status = OrderStatus::getRefundStatus();
            $orderGoodsInfo = NsOrderGoodsModel::get($order_goods_id);
            $refund_type = $orderGoodsInfo->refund_type;
            if ($refund_type == 1) { // 仅退款
                $status_id = OrderStatus::getRefundStatus()[3]['status_id'];
            } else { // 退货退款
                $status_id = OrderStatus::getRefundStatus()[1]['status_id'];
            }
            
            // 订单项退款操作
            $order_goods = new NsOrderGoodsModel();
            $order_goods_data = array(
                'refund_status' => $status_id
            );
            $res = $order_goods->save($order_goods_data, [
                'order_goods_id' => $order_goods_id
            ]);
            
            // 退款记录
            
            $this->addOrderRefundAction($order_goods_id, $status_id, 2, $this->uid);
            $this->order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 卖家永久拒绝本退款
     */
    public function orderGoodsRefuseForever($order_id, $order_goods_id)
    {
        $this->order_goods->startTrans();
        try {
            
            $status_id = OrderStatus::getRefundStatus()[5]['status_id'];
            // 订单项退款操作
            $order_goods = new NsOrderGoodsModel();
            $order_goods_data = array(
                'refund_status' => $status_id
            );
            $res = $order_goods->save($order_goods_data, [
                'order_goods_id' => $order_goods_id
            ]);
            
            // 退款记录
            
            $this->addOrderRefundAction($order_goods_id, $status_id, 2, $this->uid);
            // 订单恢复正常操作
            $order = new Order();
            $res = $order->orderGoodsRefundFinish($order_id);
            
            $this->order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e;
        }
    }

    /**
     * 卖家拒绝本次退款
     */
    public function orderGoodsRefuseOnce($order_id, $order_goods_id)
    {
        $this->order_goods->startTrans();
        try {
            $status_id = OrderStatus::getRefundStatus()[7]['status_id'];
            
            // 订单项退款操作
            $order_goods = new NsOrderGoodsModel();
            $order_goods_data = array(
                'refund_status' => $status_id
            );
            $res = $order_goods->save($order_goods_data, [
                'order_goods_id' => $order_goods_id
            ]);
            
            // 退款日志
            $this->addOrderRefundAction($order_goods_id, $status_id, 2, $this->uid);
            // 订单恢复正常操作
            $order = new Order();
            $res = $order->orderGoodsRefundFinish($order_id);
            
            $this->order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e;
        }
    }

    /**
     * 卖家确认收货
     */
    public function orderGoodsConfirmRecieve($order_id, $order_goods_id, $storage_num, $isStorage, $goods_id, $sku_id)
    {
        $this->order_goods->startTrans();
        try {
            $status_id = OrderStatus::getRefundStatus()[3]['status_id'];
            
            // 订单项退款操作
            $order_goods = new NsOrderGoodsModel();
            $order_goods_data = array(
                'refund_status' => $status_id
            );
            $res = $order_goods->save($order_goods_data, [
                'order_goods_id' => $order_goods_id
            ]);
            
            // 退款记录
            $this->addOrderRefundAction($order_goods_id, $status_id, 2, $this->uid);
            if ($isStorage > 0) {
                $goods_sku = new NsGoodsSkuModel();
                $goods = new NsGoodsModel();
                //商品sku表入库
                $goods_sku->where([
                    "goods_id" => $goods_id,
                    "sku_id" => $sku_id
                ])->setInc('stock', $storage_num);
                //商品表入库
                $goods ->where([
                    "goods_id" => $goods_id
                ])->setInc('stock', $storage_num);
            }
            $this->order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e;
        }
    }

    /**
     * 卖家确认退款
     *
     * @param 订单id $order_id            
     * @param 订单项id $order_goods_id            
     * @param 实际退款金额 $refund_real_money            
     * @param 退款余额 $refund_balance_money            
     * @param 退款交易号 $refund_trade_no            
     * @param 退款方式（1：微信，2：支付宝，10：线下） $refund_way            
     * @param 备注 $refund_remark            
     * @return number
     */
    public function orderGoodsConfirmRefund($order_id, $order_goods_id, $refund_real_money, $refund_balance_money, $refund_trade_no, $refund_way, $refund_remark)
    {
        $order_goods = NsOrderGoodsModel::get($order_goods_id);
        $order_goods->startTrans();
        try {
            $status_id = OrderStatus::getRefundStatus()[4]['status_id'];
            
            // 订单项退款操作
            $order_goods->refund_status = $status_id;
            $order_goods->refund_real_money = $refund_real_money; // 退款金额
            $order_goods->refund_balance_money = $refund_balance_money; // 退款余额
            $res = $order_goods->save();
            // 执行余额账户修正
            // 退款记录
            $this->addOrderRefundAction($order_goods_id, $status_id, 2, $this->uid);
            $order_model = new NsOrderModel();
            
            // 订单添加退款金额、余额
            $order_info = $order_model->getInfo([
                'order_id' => $order_id
            ], '*');
            
            $order = new Order();
            // 添加退款帐户记录
            if (empty($refund_remark)) {
                $remark = "订单编号:" . $order_info['order_no'] . "，退款方式为:[" . OrderStatus::getPayType($refund_way) . "]，退款金额:" . $refund_real_money . "元，退款余额：" . $refund_balance_money . "元";
            } else {
                $remark = $refund_remark;
            }
            $order->addOrderRefundAccountRecords($order_goods_id, $refund_trade_no, $refund_real_money, $refund_way, $order_info['buyer_id'], $remark);
            
            $order_model->save([
                'refund_money' => $order_info['refund_money'] + $refund_real_money,
                'refund_balance_money' => $order_info['refund_balance_money'] + $refund_balance_money
            ], [
                'order_id' => $order_id
            ]);
            $this->orderGoodsRefundExt($order_id, $order_goods_id, $refund_balance_money);
            
            // 订单恢复正常操作
            $retval = $order->orderGoodsRefundFinish($order_id);
            
            // 退款是 扣除已发放的积分
            $give_point = $order_goods["give_point"];
            if ($order_info["give_point_type"] == 3) {
                $member_account = new MemberAccount();
                $text = "退款成功,扣除已发放的积分";
                $member_account->addMemberAccountData($order_info['shop_id'], 1, $order_info['buyer_id'], 0, - $give_point, 1, $order_id, $text);
            }
            
            $total_point = $order_info["give_point"] - $give_point;
            
            $order_model->save([
                "give_point" => $total_point
            ], [
                'order_id' => $order_id
            ]);
            
            $order_goods->commit();
            
            return 1;
        } catch (\Exception $e) {
            $order_goods->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 订单项目退款处理
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id            
     * @param unknown $refund_balance_money            
     */
    private function orderGoodsRefundExt($order_id, $order_goods_id, $refund_balance_money)
    {
        $order_model = new NsOrderModel();
        $order_info = $order_model->getInfo([
            'order_id' => $order_id
        ], '*');
        $member_account = new MemberAccount();
        if ($refund_balance_money > 0) {
            $member_account->addMemberAccountData($order_info['shop_id'], 2, $order_info['buyer_id'], 1, $refund_balance_money, 2, $order_id, '订单退款');
        }
    }

    /**
     * 添加订单退款日志
     *
     * @param unknown $order_goods_id            
     * @param unknown $refund_status            
     * @param unknown $action            
     * @param unknown $action_way            
     * @param unknown $uid            
     * @param unknown $user_name            
     */
    public function addOrderRefundAction($order_goods_id, $refund_status_id, $action_way, $uid)
    {
        $refund_status = OrderStatus::getRefundStatus();
        foreach ($refund_status as $k => $v) {
            if ($v['status_id'] == $refund_status_id) {
                $refund_status_name = $v['status_name'];
            }
        }
        $user = new UserModel();
        $user_name = $user->getInfo([
            'uid' => $uid
        ], 'user_name');
        $order_refund = new NsOrderRefundModel();
        $data_refund = array(
            'order_goods_id' => $order_goods_id,
            'refund_status' => $refund_status_id,
            'action' => $refund_status_name,
            'action_way' => $action_way,
            'action_userid' => $uid,
            'action_username' => $user_name['user_name'],
            'action_time' => time()
        );
        $retval = $order_refund->save($data_refund);
        return $retval;
    }

    /**
     * 订单项商品价格调整
     *
     * @param unknown $order_goods_id_adjust_array
     *            订单项数列 order_goods_id,adjust_money;order_goods_id,adjust_money
     */
    public function orderGoodsAdjustMoney($order_goods_id_adjust_array)
    {
        $this->order_goods->startTrans();
        try {
            $order_goods_id_adjust_array = explode(';', $order_goods_id_adjust_array);
            if (! empty($order_goods_id_adjust_array)) {
                foreach ($order_goods_id_adjust_array as $k => $order_goods_id_adjust) {
                    $order_goods_adjust_array = explode(',', $order_goods_id_adjust);
                    $order_goods_id = $order_goods_adjust_array[0];
                    $adjust_money = $order_goods_adjust_array[1];
                    $order_goods_info = $this->order_goods->get($order_goods_id);
                    // 调整金额
                    $adjust_money_adjust = $adjust_money - $order_goods_info['adjust_money'];
                    $data = array(
                        'adjust_money' => $adjust_money,
                        'goods_money' => $order_goods_info['goods_money'] + $adjust_money_adjust
                    );
                    $order_goods = new NsOrderGoodsModel();
                    $order_goods->save($data, [
                        'order_goods_id' => $order_goods_id
                    ]);
                }
            }
            
            $this->order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e;
        }
    }

    /**
     * 获取订单项实际可退款金额
     *
     * @param unknown $order_goods_id            
     */
    public function orderGoodsRefundMoney($order_goods_id)
    {
        $order_goods = new NsOrderGoodsModel();
        $order_goods_info = $order_goods->getInfo([
            'order_goods_id' => $order_goods_id
        ], 'order_id,sku_id,goods_money');
        $order_goods_promotion = new NsOrderGoodsPromotionDetailsModel();
        $promotion_money = $order_goods_promotion->where([
            'order_id' => $order_goods_info['order_id'],
            'sku_id' => $order_goods_info['sku_id']
        ])->sum('discount_money');
        if (empty($promotion_money)) {
            $promotion_money = 0;
        }
        $money = $order_goods_info['goods_money'] - $promotion_money;
        // 计算其他方式支付金额
        $order = new NsOrderModel();
        $order_other_pay_money = $order->getInfo([
            'order_id' => $order_goods_info['order_id']
        ], 'order_money,point_money,user_money,coin_money,user_platform_money,tax_money,shipping_money');
        $all_other_pay_money = $order_other_pay_money['point_money'] + $order_other_pay_money['user_money'] + $order_other_pay_money['coin_money'] + $order_other_pay_money['user_platform_money'] - $order_other_pay_money['tax_money'];
        if ($all_other_pay_money != 0) {
            $other_pay = $money / ($order_other_pay_money['order_money'] - $order_other_pay_money['shipping_money'] - $order_other_pay_money['tax_money']) * $all_other_pay_money;
            $money = $money - round($other_pay, 2);
        }
        if ($money < 0) {
            $money = 0;
        }
        
        return $money;
    }

    /**
     * 获取订单项实际可退款余额
     *
     * @param unknown $order_goods_id            
     */
    public function orderGoodsRefundBalance($order_goods_id)
    {
        $order_goods = new NsOrderGoodsModel();
        $order_goods_info = $order_goods->getInfo([
            'order_goods_id' => $order_goods_id
        ], 'order_id,sku_id,goods_money');
        $order_goods_promotion = new NsOrderGoodsPromotionDetailsModel();
        $promotion_money = $order_goods_promotion->where([
            'order_id' => $order_goods_info['order_id'],
            'sku_id' => $order_goods_info['sku_id']
        ])->sum('discount_money');
        if (empty($promotion_money)) {
            $promotion_money = 0;
        }
        $money = $order_goods_info['goods_money'] - $promotion_money;
        // 计算其他方式支付金额
        $order = new NsOrderModel();
        $order_other_pay_money = $order->getInfo([
            'order_id' => $order_goods_info['order_id']
        ], 'order_money,point_money,user_money,coin_money,user_platform_money,tax_money,shipping_money');
        $order_goods_real_money = $order_other_pay_money['order_money'] - $order_other_pay_money['shipping_money'] - $order_other_pay_money['tax_money'];
        if ($order_goods_real_money != 0) {
            $refund_balance = $money / $order_goods_real_money * $order_other_pay_money['user_platform_money'];
            if ($refund_balance < 0) {
                $refund_balance = 0;
            }
        } else {
            $refund_balance = 0;
        }
        
        return $refund_balance;
    }

    /**
     * 查询订单项退款
     *
     * @param unknown $order_goods_id            
     */
    public function getOrderGoodsRefundDetail($order_goods_id)
    {
        // 查询基础信息
        $order_goods_info = $this->order_goods->get($order_goods_id);
        
        // 商品图片
        $picture = new AlbumPictureModel();
        $picture_info = $picture->get($order_goods_info['goods_picture']);
        $order_goods_info['picture_info'] = $picture_info;
        if ($order_goods_info['refund_status'] != 0) {
            $order_refund_status = OrderStatus::getRefundStatus();
            foreach ($order_refund_status as $k_status => $v_status) {
                
                if ($v_status['status_id'] == $order_goods_info['refund_status']) {
                    $order_goods_info['refund_operation'] = $v_status['refund_operation'];
                    $order_goods_info['status_name'] = $v_status['status_name'];
                }
            }
            // 查询订单项的操作日志
            $order_refund = new NsOrderRefundModel();
            $refund_info = $order_refund->all([
                'order_goods_id' => $order_goods_id
            ]);
            $order_goods_info['refund_info'] = $refund_info;
        } else {
            $order_goods_info['refund_operation'] = '';
            $order_goods_info['status_name'] = '';
            $order_goods_info['refund_info'] = '';
        }
        return $order_goods_info;
    }
    /**
     * 获取出库单列表
     */
    public function getShippingList($order_ids){
        $order_goods_view = new NsOrderGoodsViewModel();
        $condition = array(
            'nog.order_id' => array("in",$order_ids),
            'no.order_status' => array("neq",0)
        );
        $list = $order_goods_view -> getShippingList(1, 0, $condition, "");
        foreach ($list as $v){
            $res = $order_goods_view -> getOrderGoodsViewQuery(1, 0, [
                'no.order_id' => array("in",$order_ids),
                'nog.sku_id' => $v["sku_id"]
            ], "");
            $v["order_list"] = $res;
        }
        return $list;
    }
    /**
     * 添加打印时临时物流信息
     */
    public function addTmpExpressInformation($print_order_arr,$deliver_goods){
        if(!empty($print_order_arr) && count($print_order_arr) > 0){
            $ns_order_goods = new NsOrderGoodsModel();
            $order_goods_express = new NsOrderGoodsExpressModel();
            $order = new OrderService();
            $ns_order_goods->startTrans();
            try {
                foreach($print_order_arr as $order_print_info){
                    $ns_order_goods->update([
                        "tmp_express_company" => $order_print_info["tmp_express_company_name"],
                        "tmp_express_company_id" => $order_print_info["tmp_express_company_id"],
                        "tmp_express_no" => $order_print_info["tmp_express_no"]
                    ],[
                        "order_id" => $order_print_info['order_id'],
                        "order_goods_id" => array("in", explode(",",$order_print_info["order_goods_ids"]))
                    ]);
                    //订单物流表
                    if($order_print_info['is_devlier'] == 1){
                        $order_goods_express -> update([
                            "express_company_id" => $order_print_info["tmp_express_company_id"],
                            "express_company" => $order_print_info["tmp_express_company_name"],
                            "express_name" => $order_print_info["tmp_express_company_name"],
                            "express_no" => $order_print_info["tmp_express_no"]
                        ],[
                            "order_id" => $order_print_info['order_id'],
                            "id" => $order_print_info['express_id']
                        ]);
                    }
                    //订单发货
                    if($order_print_info['is_devlier'] == 0 && $deliver_goods == 1){
                        $order -> orderDelivery($order_print_info['order_id'], $order_print_info['order_goods_ids'], $order_print_info["tmp_express_company_name"], 1, $order_print_info['tmp_express_company_id'],  $order_print_info["tmp_express_no"]);
                    }
                }
                $ns_order_goods->commit();
                 return $retval = array(
                    "code" => 1,
                    "message" => "操作成功"
                );
            } catch (\Exception $e) {
                $ns_order_goods->rollback();
                return $e->getMessage();
            }
        }else{
            return $retval = array(
                "code" => 0,
                "message" => "操作失败"
            );
        }
        
    }
    
}