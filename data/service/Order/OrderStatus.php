<?php
/**
 * OrderStatus.php
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

use data\service\BaseService as BaseService;

/**
 * 订单调度类
 */
class OrderStatus extends BaseService
{

    /**
     * 获取实物订单所有可能的订单状态
     */
    public static function getOrderCommonStatus()
    {
        $status = array(
            array(
                'status_id' => '0',
                'status_name' => '待付款',
                'is_refund' => 0, // 是否可以申请退款
                'operation' => array(
                    '0' => array(
                        'no' => 'pay',
                        'name' => '线下支付',
                        'color' => '#FF9800'
                    ),
                    '1' => array(
                        'no' => 'close',
                        'color' => '#E61D1D',
                        'name' => '交易关闭'
                    ),
                    '2' => array(
                        'no' => 'adjust_price',
                        'color' => '#4CAF50',
                        'name' => '修改价格'
                    ),
                    '3' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    )
                ),
                'member_operation' => array(
                    '0' => array(
                        'no' => 'pay',
                        'name' => '去支付',
                        'color' => '#F15050'
                    ),
                    
                    '1' => array(
                        'no' => 'close',
                        'name' => '关闭订单',
                        'color' => '#999999'
                    )
                )
            ),
            array(
                'status_id' => '1',
                'status_name' => '待发货',
                'is_refund' => 1,
                'operation' => array(
                    '0' => array(
                        'no' => 'delivery',
                        'color' => 'green',
                        'name' => '发货'
                    ),
                    '1' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    ),
                    '2' => array(
                        'no' => 'update_address',
                        'color' => '#51A351',
                        'name' => '修改地址'
                    )
                ),
                'member_operation' => array()
            ),
            array(
                'status_id' => '2',
                'status_name' => '已发货',
                'is_refund' => 1,
                'operation' => array(
                    '0' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    ),
                    '1' => array(
                        'no' => 'logistics',
                        'color' => '#ccc',
                        'name' => '查看物流'
                    ),
                    '2' => array(
                        'no' => 'getdelivery',
                        'name' => '确认收货',
                        'color' => '#FF6600'
                    )
                ),
                
                'member_operation' => array(
                    '0' => array(
                        'no' => 'getdelivery',
                        'name' => '确认收货',
                        'color' => '#FF6600'
                    ),
                    '1' => array(
                        'no' => 'logistics',
                        'color' => '#ccc',
                        'name' => '查看物流'
                    )
                )
            ),
            array(
                'status_id' => '3',
                'status_name' => '已收货',
                'is_refund' => 0,
                'operation' => array(
                    '0' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    ),
                    '1' => array(
                        'no' => 'logistics',
                        'color' => '#ccc',
                        'name' => '查看物流'
                    )
                ),
                'member_operation' => array(
                    '0' => array(
                        'no' => 'logistics',
                        'color' => '#ccc',
                        'name' => '查看物流'
                    )
                )
            ),
            array(
                'status_id' => '4',
                'status_name' => '已完成',
                'is_refund' => 0,
                'operation' => array(
                    '0' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    ),
                    '1' => array(
                        'no' => 'logistics',
                        'color' => '#ccc',
                        'name' => '查看物流'
                    )
                ),
                'member_operation' => array(
                    '0' => array(
                        'no' => 'logistics',
                        'color' => '#ccc',
                        'name' => '查看物流'
                    )
                )
            ),
            array(
                'status_id' => '5',
                'status_name' => '已关闭',
                'is_refund' => 0,
                'operation' => array(
                    '0' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    ),
                    '1' => array(
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    )
                ),
                'member_operation' => array(
                    '0' => array(
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    )
                )
            ),
            array(
                'status_id' => '-1',
                'status_name' => '退款中',
                'is_refund' => 1,
                'operation' => array(
                    '0' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    )
                ),
                'member_operation' => array()
            )
        );
        return $status;
    }

    /**
     * 获取虚拟订单所有可能的订单状态
     */
    public static function getVirtualOrderCommonStatus()
    {
        $status = array(
            array(
                'status_id' => '0',
                'status_name' => '待付款',
                'is_refund' => 0, // 是否可以申请退款
                'operation' => array(
                    '0' => array(
                        'no' => 'pay',
                        'name' => '线下支付',
                        'color' => '#FF9800'
                    ),
                    '1' => array(
                        'no' => 'close',
                        'color' => '#E61D1D',
                        'name' => '交易关闭'
                    ),
                    '2' => array(
                        'no' => 'adjust_price',
                        'color' => '#4CAF50',
                        'name' => '修改价格'
                    ),
                    '3' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    )
                ),
                'member_operation' => array(
                    '0' => array(
                        'no' => 'pay',
                        'name' => '去支付',
                        'color' => '#F15050'
                    ),
                    
                    '1' => array(
                        'no' => 'close',
                        'name' => '关闭订单',
                        'color' => '#999999'
                    )
                )
            ),
            array(
                'status_id' => '6',
                'status_name' => '已付款',
                'is_refund' => 0,
                'operation' => array(
                    '0' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    )
                ),
                'member_operation' => array()

                
            ),
            array(
                'status_id' => '4',
                'status_name' => '已完成',
                'is_refund' => 0,
                'operation' => array(
                    '0' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    )
                ),
                'member_operation' => array()
            ),
            array(
                'status_id' => '5',
                'status_name' => '已关闭',
                'is_refund' => 0,
                'operation' => array(
                    '0' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    ),
                    '1' => array(
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    )
                ),
                'member_operation' => array(
                    '0' => array(
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    )
                )
            )
        );
        return $status;
    }

    /**
     * 获取自提订单相关状态
     */
    public static function getSinceOrderStatus()
    {
        $status = array(
            array(
                'status_id' => '0',
                'status_name' => '待付款',
                'is_refund' => 0, // 是否可以申请退款
                'operation' => array(
                    '0' => array(
                        'no' => 'pay',
                        'name' => '线下支付',
                        'color' => '#FF9800'
                    ),
                    '1' => array(
                        'no' => 'close',
                        'color' => '#E61D1D',
                        'name' => '交易关闭'
                    ),
                    '2' => array(
                        'no' => 'adjust_price',
                        'color' => '#4CAF50',
                        'name' => '修改价格'
                    ),
                    '3' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    )
                ),
                'member_operation' => array(
                    '0' => array(
                        'no' => 'pay',
                        'name' => '去支付',
                        'color' => '#F15050'
                    ),
                    
                    '1' => array(
                        'no' => 'close',
                        'name' => '关闭订单',
                        'color' => '#999999'
                    )
                )
            ),
            array(
                'status_id' => '1',
                'status_name' => '待提货',
                'is_refund' => 1,
                'operation' => array(
                    '0' => array(
                        'no' => 'pickup',
                        'color' => '#FF9800',
                        'name' => '提货'
                    ),
                    '1' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    )
                ),
                'member_operation' => array()
            ),
            array(
                'status_id' => '3',
                'status_name' => '已提货',
                'is_refund' => 0,
                'operation' => array(
                    
                    '0' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    ),
                    '1' => array(
                        'no' => 'logistics',
                        'color' => '#51A351',
                        'name' => '查看物流'
                    )
                ),
                
                'member_operation' => array()
            ),
            array(
                'status_id' => '4',
                'status_name' => '已完成',
                'is_refund' => 0,
                'operation' => array(
                    '0' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    ),
                    '1' => array(
                        'no' => 'logistics',
                        'color' => '#51A351',
                        'name' => '查看物流'
                    )
                ),
                
                'member_operation' => array()
            ),
            array(
                'status_id' => '5',
                'status_name' => '已关闭',
                'is_refund' => 0,
                'operation' => array(
                    '0' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    ),
                    '1' => array(
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    )
                ),
                
                'member_operation' => array(
                    '0' => array(
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    )
                )
            ),
            array(
                'status_id' => '-1',
                'status_name' => '退款中',
                'is_refund' => 1,
                'operation' => array(
                    '0' => array(
                        'no' => 'seller_memo',
                        'color' => '#8e8c8c',
                        'name' => '添加备注'
                    )
                ),
                'member_operation' => array()
            )
        );
        return $status;
    }

    /**
     * 获取发货操作状态
     */
    public static function getShippingStatus()
    {
        $shipping_status = array(
            array(
                'shipping_status' => '0',
                'status_name' => '待发货'
            ),
            array(
                'shipping_status' => '1',
                'status_name' => '已发货'
            ),
            array(
                'shipping_status' => '2',
                'status_name' => '已收货'
            ),
            array(
                'shipping_status' => '3',
                'status_name' => '备货中'
            )
        );
        return $shipping_status;
    }

    /**
     * 获取发货方式
     *
     * @param unknown $type_id            
     */
    public static function getShoppingType($type_id)
    {
        $shipping_type = array(
            array(
                'type_id' => '1',
                'type_name' => '商家快递'
            ),
            array(
                'type_id' => '2',
                'type_name' => '到店自提'
            )
        );
        $type_name = '';
        foreach ($shipping_type as $k => $v) {
            if ($v['type_id'] == $type_id) {
                $type_name = $v['type_name'];
            }
        }
        return $type_name;
    }

    /**
     * 获取订单支付操作状态
     */
    public static function getPayStatus($pay_status_id = -100)
    {
        $pay_status = array(
            array(
                'pay_status' => '0',
                'status_name' => '待支付'
            ),
            array(
                'pay_status' => '1',
                'status_name' => '支付中'
            ),
            array(
                'pay_status' => '2',
                'status_name' => '已支付'
            )
        );
        return $pay_status;
    }

    /**
     * 获取订单退款操作状态
     */
    public static function getRefundStatus()
    {
        $refund_status = array(
            '0' => array(
                'status_id' => '1',
                'status_name' => '买家申请退款',
                'status_desc' => '发起了退款申请,等待卖家处理',
                'refund_operation' => array(
                    '0' => array(
                        'no' => 'agree',
                        'name' => '同意',
                        'color' => '#4CAF50'
                    ),
                    '1' => array(
                        'no' => 'refuse',
                        'name' => '拒绝',
                        'color' => 'rgb(232, 80, 69)'
                    )
                )
            ),
            '1' => array(
                'status_id' => '2',
                'status_name' => '等待买家退货',
                'status_desc' => '卖家已同意退款申请,等待买家退货',
                'refund_operation' => array()
            ),
            '2' => array(
                'status_id' => '3',
                'status_name' => '等待卖家确认收货',
                'status_desc' => '买家已退货,等待卖家确认收货',
                'refund_operation' => array(
                    '0' => array(
                        'no' => 'confirm_receipt',
                        'name' => '确认收货',
                        'color' => '#4CAF50'
                    )
                )
            ),
            '3' => array(
                'status_id' => '4',
                'status_name' => '等待卖家确认退款',
                'status_desc' => '卖家同意退款',
                'refund_operation' => array(
                    '0' => array(
                        'no' => 'confirm_refund',
                        'name' => '确认退款',
                        'color' => '#4CAF50'
                    )
                )
            ),
            '4' => array(
                'status_id' => '5',
                'status_name' => '退款已成功',
                'status_desc' => '卖家退款给买家，本次维权结束',
                'refund_operation' => array()
            ),
            '5' => array(
                'status_id' => '-1',
                'status_name' => '退款已拒绝',
                'status_desc' => '卖家拒绝本次退款，本次维权结束',
                'refund_operation' => array()
            ),
            '6' => array(
                'status_id' => '-2',
                'status_name' => '退款已关闭',
                'status_desc' => '主动撤销退款，退款关闭',
                'refund_operation' => array()
            ),
            '7' => array(
                'status_id' => '-3',
                'status_name' => '退款申请不通过',
                'status_desc' => '拒绝了本次退款申请,等待买家修改',
                'refund_operation' => array()
            )
        );
        return $refund_status;
    }

    /**
     * 获取订单所有的操作
     */
    public static function getOrderOperation()
    {
        $operation = array(
            '0' => array(
                'no' => 'pay',
                'name' => '线下支付'
            ),
            '1' => array(
                'no' => 'complete',
                'name' => '交易完成'
            ),
            '2' => array(
                'no' => 'delivery',
                'name' => '发货'
            )
        );
    }

    /**
     * 获取支付方式
     *
     * @param unknown $type_id            
     * @return string
     */
    public static function getPayType($type_id)
    {
        $pay_type = array(
            array(
                'type_id' => '0',
                'type_name' => '在线支付'
            ),
            array(
                'type_id' => '1',
                'type_name' => '微信支付'
            ),
            array(
                'type_id' => '2',
                'type_name' => '支付宝'
            ),
            array(
                'type_id' => '3',
                'type_name' => '银联卡'
            ),
            array(
                'type_id' => '4',
                'type_name' => '货到付款'
            ),
            array(
                'type_id' => '5',
                'type_name' => '余额支付'
            ),
            array(
                'type_id' => '6',
                'type_name' => '到店支付'
            ),
            array(
                'type_id' => '10',
                'type_name' => '线下支付'
            )
        );
        $type_name = '';
        foreach ($pay_type as $k => $v) {
            if ($v['type_id'] == $type_id) {
                $type_name = $v['type_name'];
            }
        }
        return $type_name;
    }

    /**
     * 获取订单来源
     *
     * @param unknown $order_from            
     */
    public static function getOrderFrom($type_id)
    {
        $order_grom_type = array(
            array(
                'type_id' => '1',
                'type_name' => '微信端',
                'tag' => 'fa fa-weixin'
            ),
            array(
                'type_id' => '2',
                'type_name' => '手机端',
                'tag' => 'fa fa-mobile fa-2x'
            ),
            array(
                'type_id' => '3',
                'type_name' => 'pc端',
                'tag' => 'fa fa-television'
            )
        );
        $type_name = array();
        foreach ($order_grom_type as $k => $v) {
            if ($v['type_id'] == $type_id) {
                $type_name = $v;
            }
        }
        return $type_name;
    }
}