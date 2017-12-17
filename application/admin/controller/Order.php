<?php
/**
 * Order.php
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
namespace app\admin\controller;

use data\service\Address;
use data\service\Address as AddressService;
use data\service\Config;
use data\service\Express;
use data\service\Express as ExpressService;
use data\service\Order\OrderGoods;
use data\service\Order\OrderStatus;
use data\service\Order as OrderService;
use data\service\Pay\AliPay;
use data\service\Pay\WeiXinPay;

/**
 * 订单控制器
 *
 * @author Administrator
 *        
 */
class Order extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 订单列表
     */
    public function orderList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $start_date = request()->post('start_date') == "" ? 0 : getTimeTurnTimeStamp(request()->post('start_date'));
            $end_date = request()->post('end_date') == "" ? 0 : getTimeTurnTimeStamp(request()->post('end_date'));
            $user_name = request()->post('user_name', '');
            $order_no = request()->post('order_no', '');
            $order_status = request()->post('order_status', '');
            $receiver_mobile = request()->post('receiver_mobile', '');
            $payment_type = request()->post('payment_type', 1);
            $condition['order_type'] = 1; // 订单类型
            $condition['is_deleted'] = 0; // 未删除订单
            if ($start_date != 0 && $end_date != 0) {
                $condition["create_time"] = [
                    [
                        ">",
                        $start_date
                    ],
                    [
                        "<",
                        $end_date
                    ]
                ];
            } elseif ($start_date != 0 && $end_date == 0) {
                $condition["create_time"] = [
                    [
                        ">",
                        $start_date
                    ]
                ];
            } elseif ($start_date == 0 && $end_date != 0) {
                $condition["create_time"] = [
                    [
                        "<",
                        $end_date
                    ]
                ];
            }
            if ($order_status != '') {
                // $order_status 1 待发货
                if ($order_status == 1) {
                    // 订单状态为待发货实际为已经支付未完成还未发货的订单
                    $condition['shipping_status'] = 0; // 0 待发货
                    $condition['pay_status'] = 2; // 2 已支付
                    $condition['order_status'] = array(
                        'neq',
                        4
                    ); // 4 已完成
                    $condition['order_status'] = array(
                        'neq',
                        5
                    ); // 5 关闭订单
                } else
                    $condition['order_status'] = $order_status;
            }
            if (! empty($payment_type)) {
                $condition['payment_type'] = $payment_type;
            }
            if (! empty($user_name)) {
                $condition['receiver_name'] = $user_name;
            }
            if (! empty($order_no)) {
                $condition['order_no'] = $order_no;
            }
            if (! empty($receiver_mobile)) {
                $condition['receiver_mobile'] = $receiver_mobile;
            }
            $condition['shop_id'] = $this->instance_id;
            $order_service = new OrderService();
            $list = $order_service->getOrderList($page_index, $page_size, $condition, 'create_time desc');
            return $list;
        } else {
            $status = request()->get('status', '');
            $this->assign("status", $status);
            $all_status = OrderStatus::getOrderCommonStatus();
            $child_menu_list = array();
            $child_menu_list[] = array(
                'url' => "Order/orderList",
                'menu_name' => '全部',
                "active" => $status == '' ? 1 : 0
            );
            foreach ($all_status as $k => $v) {
                // 针对发货与提货状态名称进行特殊修改
                /*
                 * if($v['status_id'] == 1)
                 * {
                 * $status_name = '待发货/待提货';
                 * }elseif($v['status_id'] == 3){
                 * $status_name = '已收货/已提货';
                 * }else{
                 * $status_name = $v['status_name'];
                 * }
                 */
                $child_menu_list[] = array(
                    'url' => "order/orderlist?status=" . $v['status_id'],
                    'menu_name' => $v['status_name'],
                    "active" => $status == $v['status_id'] ? 1 : 0
                );
            }
            $this->assign('child_menu_list', $child_menu_list);
            // 获取物流公司
            $express = new ExpressService();
            $expressList = $express->expressCompanyQuery();
            $this->assign('expressList', $expressList);
            return view($this->style . "Order/orderList");
        }
    }

    /**
     * 虚拟订单列表
     */
    public function virtualOrderList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $start_date = request()->post('start_date') == "" ? 0 : getTimeTurnTimeStamp(request()->post('start_date'));
            $end_date = request()->post('end_date') == "" ? 0 : getTimeTurnTimeStamp(request()->post('end_date'));
            $order_no = request()->post('order_no', '');
            $order_status = request()->post('order_status', '');
            $receiver_mobile = request()->post('receiver_mobile', '');
            $payment_type = request()->post('payment_type', 1);
            $condition['order_type'] = 2; // 订单类型
            $condition['is_deleted'] = 0; // 未删除订单
            if ($start_date != 0 && $end_date != 0) {
                $condition["create_time"] = [
                    [
                        ">",
                        $start_date
                    ],
                    [
                        "<",
                        $end_date
                    ]
                ];
            } elseif ($start_date != 0 && $end_date == 0) {
                $condition["create_time"] = [
                    [
                        ">",
                        $start_date
                    ]
                ];
            } elseif ($start_date == 0 && $end_date != 0) {
                $condition["create_time"] = [
                    [
                        "<",
                        $end_date
                    ]
                ];
            }
            if ($order_status != '') {
                $condition['order_status'] = $order_status;
            }
            if (! empty($payment_type)) {
                $condition['payment_type'] = $payment_type;
            }
            if (! empty($order_no)) {
                $condition['order_no'] = $order_no;
            }
            if (! empty($receiver_mobile)) {
                $condition['receiver_mobile'] = $receiver_mobile;
            }
            $condition['shop_id'] = $this->instance_id;
            $order_service = new OrderService();
            $list = $order_service->getOrderList($page_index, $page_size, $condition, 'create_time desc');
            return $list;
        } else {
            $status = request()->get('status', '');
            $this->assign("status", $status);
            $all_status = OrderStatus::getVirtualOrderCommonStatus();
            $child_menu_list = array();
            $child_menu_list[] = array(
                'url' => "order/virtualorderlist",
                'menu_name' => '全部',
                "active" => $status == '' ? 1 : 0
            );
            foreach ($all_status as $k => $v) {
                $child_menu_list[] = array(
                    'url' => "order/virtualorderlist?status=" . $v['status_id'],
                    'menu_name' => $v['status_name'],
                    "active" => $status == $v['status_id'] ? 1 : 0
                );
            }
            $this->assign('child_menu_list', $child_menu_list);
            return view($this->style . "Order/virtualOrderList");
        }
    }

    /**
     * 功能说明：获取店铺信息
     */
    public function getShopInfo()
    {
        // 获取信息
        $shopInfo['shopId'] = $this->instance_id;
        $shopInfo['shopName'] = $this->instance_name;
        // 返回信息
        return $shopInfo;
    }

    /**
     * 功能说明：获取打印出货单预览信息
     */
    public function getOrderInvoiceView()
    {
        $shop_id = $this->instance_id;
        // 获取值
        $orderIdArray = request()->get('ids', '');
        // 操作
        $order_service = new OrderService();
        $goods_express_list = $order_service->getOrderGoodsExpressDetail($orderIdArray, $shop_id);
        // 返回信息
        return $goods_express_list;
    }

    /**
     * 功能说明：获取打印订单项预览信息
     */
    public function getOrderExpressPreview()
    {
        $shop_id = $this->instance_id;
        // 获取值
        $orderIdArray = request()->get('ids', '');
        // 操作
        $order_service = new OrderService();
        $goods_express_list = $order_service->getOrderPrint($orderIdArray, $shop_id);
        // 返回信息
        return $goods_express_list;
    }

    /**
     * 功能说明：打印预览 发货单
     */
    public function printDeliveryPreview()
    {
        // 获取值
        $order_service = new OrderService();
        $order_ids = request()->get('order_ids', '');
        $ShopName = request()->get('ShopName', '');
        $shop_id = $this->instance_id;
        $order_str = explode(",", $order_ids);
        $order_array = array();
        foreach ($order_str as $order_id) {
            $detail = array();
            $detail = $order_service->getOrderDetail($order_id);
            if (empty($detail)) {
                $this->error("没有获取到订单信息");
            }
            $order_array[] = $detail;
        }
        $receive_address = $order_service->getShopReturnSet($shop_id);
        $this->assign("order_print", $order_array);
        $this->assign("ShopName", $ShopName);
        $this->assign("receive_address", $receive_address);
        return view($this->style . 'Order/printDeliveryPreview');
    }

    /**
     * 打印快递单
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    // public function printExpressPreview()
    // {
    // $order_service = new OrderService();
    // $address_service = new AddressService();
    
    // $order_ids = request()->get('order_ids', '');
    // $ShopName = request()->get('ShopName', '');
    // $co_id = request()->get('co_id', '');
    
    // $shop_id = $this->instance_id;
    // $order_str = explode(",", $order_ids);
    // $order_array = array();
    // foreach ($order_str as $order_id) {
    // $detail = array();
    // $detail = $order_service->getOrderDetail($order_id);
    // if (empty($detail)) {
    // $this->error("没有获取到订单信息");
    // }
    // // $detail['address'] = $address_service->getAddress($detail['receiver_province'], $detail['receiver_city'], $detail['receiver_district']);
    // $order_array[] = $detail;
    // }
    // $express_server = new ExpressService();
    // // 物流模板信息
    // $express_shipping = $express_server->getExpressShipping($co_id);
    // // 物流打印信息
    // $express_shipping_item = $express_server->getExpressShippingItems($express_shipping["sid"]);
    // $receive_address = $order_service->getShopReturnSet($shop_id);
    // $this->assign("order_print", $order_array);
    // $this->assign("ShopName", $ShopName);
    // $this->assign("express_ship", $express_shipping);
    // $this->assign("express_item_list", $express_shipping_item);
    // $this->assign("receive_address", $receive_address);
    // return view($this->style . 'Order/printExpressPreview');
    // }
    public function printExpressPreview()
    {
        $print_order_ids = request()->get('print_order_ids', '');
        
        $express_server = new ExpressService();
        $order_service = new OrderService();
        $address_service = new AddressService();
        
        $print_order_id_array = explode(";", $print_order_ids);
        if (! empty($print_order_id_array) && count($print_order_id_array) > 0) {
            $order_list = "";
            foreach ($print_order_id_array as $print_order_id) {
                $print_order_list = explode(":", $print_order_id);
                $detail = $order_service->getOrderDetail($print_order_list[0]); // 获取订单详情
                $detail['address'] = $address_service->getAddress($detail['receiver_province'], $detail['receiver_city'], $detail['receiver_district']);
                $express_id_list = explode(",", $print_order_list[1]); // 获取订单下包裹数
                $express_shipping_list = array();
                foreach ($express_id_list as $co_id) {
                    $express_shipping = $express_server->getExpressShipping($co_id); // 物流模板信息
                    $express_shipping["express_shipping_item"] = $express_shipping_item = $express_server->getExpressShippingItems($express_shipping["sid"]); // 物流打印信息
                    $express_shipping_list[] = $express_shipping;
                }
                $detail["express_id_list"] = $express_shipping_list;
                $order_list[] = $detail;
            }
        }
        $receive_address = $order_service->getShopReturnSet($this->instance_id);
        $this->assign("receive_address", $receive_address);
        $this->assign("order_print", $order_list);
        return view($this->style . 'Order/printExpressPreview');
    }

    /**
     * 订单详情
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function orderDetail()
    {
        $order_id = request()->get('order_id', 0);
        if ($order_id == 0) {
            $this->error("没有获取到订单信息");
        }
        $order_service = new OrderService();
        $detail = $order_service->getOrderDetail($order_id);
        if (empty($detail)) {
            $this->error("没有获取到订单信息");
        }
        if (! empty($detail['operation'])) {
            $operation_array = $detail['operation'];
            foreach ($operation_array as $k => $v) {
                if ($v["no"] == 'logistics') {
                    unset($operation_array[$k]);
                }
            }
            $detail['operation'] = $operation_array;
        }
        $this->assign("order", $detail);
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "order/orderlist",
                    'menu_name' => "订单列表",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "Order/orderDetail");
    }

    /**
     * 虚拟订单详情
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function virtualOrderDetail()
    {
        $order_id = request()->get('order_id', 0);
        if ($order_id == 0) {
            $this->error("没有获取到订单信息");
        }
        $order_service = new OrderService();
        $detail = $order_service->getOrderDetail($order_id);
        if (empty($detail)) {
            $this->error("没有获取到订单信息");
        }
        if (! empty($detail['operation'])) {
            $operation_array = $detail['operation'];
            foreach ($operation_array as $k => $v) {
                if ($v["no"] == 'logistics') {
                    unset($operation_array[$k]);
                }
            }
            $detail['operation'] = $operation_array;
        }
        $this->assign("order", $detail);
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "order/virtualorderlist",
                    'menu_name' => "虚拟订单",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "Order/virtualOrderDetail");
    }

    /**
     * 订单退款详情
     */
    public function orderRefundDetail()
    {
        $order_goods_id = request()->get('itemid', 0);
        if ($order_goods_id == 0) {
            $this->error("没有获取到退款信息");
        }
        $order_service = new OrderService();
        $info = $order_service->getOrderGoodsRefundInfo($order_goods_id);
        $refund_account_records = $order_service->getOrderRefundAccountRecordsByOrderGoodsId($order_goods_id);
        $remark = ""; // 退款备注，只有在退款成功的状态下显示
        if (! empty($refund_account_records)) {
            if (! empty($refund_account_records['remark'])) {
                
                $remark = $refund_account_records['remark'];
            }
        }
        $order_goods = new OrderGoods();
        // 退款余额
        $refund_balance = $order_goods->orderGoodsRefundBalance($order_goods_id);
        $this->assign("refund_balance", sprintf("%.2f", $refund_balance));
        $this->assign('order_goods', $info);
        $this->assign("remark", $remark);
        
        return view($this->style . "Order/orderRefundDetail");
    }

    /**
     * 线下支付
     */
    public function orderOffLinePay()
    {
        $order_service = new OrderService();
        $order_id = request()->post('order_id', '');
        $order_info = $order_service->getOrderInfo($order_id);
        if ($order_info['payment_type'] == 6) {
            $res = $order_service->orderOffLinePay($order_id, 6, 0);
        } else {
            $res = $order_service->orderOffLinePay($order_id, 10, 0);
        }
        
        return AjaxReturn($res);
    }

    /**
     * 交易完成
     *
     * @param unknown $orderid            
     * @return Exception
     */
    public function orderComplete()
    {
        $order_service = new OrderService();
        $order_id = request()->post('order_id', '');
        $res = $order_service->orderComplete($order_id);
        return AjaxReturn($res);
    }

    /**
     * 交易关闭
     */
    public function orderClose()
    {
        $order_service = new OrderService();
        $order_id = request()->post('order_id', '');
        $res = $order_service->orderClose($order_id);
        return AjaxReturn($res);
    }

    /**
     * 订单发货 所需数据
     */
    public function orderDeliveryData()
    {
        $order_service = new OrderService();
        $express_service = new ExpressService();
        $address_service = new AddressService();
        $order_id = request()->post('order_id', '');
        $order_info = $order_service->getOrderDetail($order_id);
        $order_info['address'] = $address_service->getAddress($order_info['receiver_province'], $order_info['receiver_city'], $order_info['receiver_district']);
        $shopId = $this->instance_id;
        // 快递公司列表
        $express_company_list = $express_service->expressCompanyQuery('shop_id = ' . $shopId, "*");
        // 订单商品项
        $order_goods_list = $order_service->getOrderGoods($order_id);
        $data['order_info'] = $order_info;
        $data['express_company_list'] = $express_company_list;
        $data['order_goods_list'] = $order_goods_list;
        return $data;
    }

    /**
     * 订单发货
     */
    public function orderDelivery()
    {
        $order_service = new OrderService();
        $order_id = request()->post('order_id', '');
        $order_goods_id_array = request()->post('order_goods_id_array', '');
        $express_name = request()->post('express_name', '');
        $shipping_type = request()->post('shipping_type', '');
        $express_company_id = request()->post('express_company_id', '');
        $express_no = request()->post('express_no', '');
        if ($shipping_type == 1) {
            $res = $order_service->orderDelivery($order_id, $order_goods_id_array, $express_name, $shipping_type, $express_company_id, $express_no);
        } else {
            $res = $order_service->orderGoodsDelivery($order_id, $order_goods_id_array);
        }
        return AjaxReturn($res);
    }

    /**
     * 获取订单大订单项
     */
    public function getOrderGoods()
    {
        $order_id = request()->post('order_id', '');
        $order_service = new OrderService();
        $order_goods_list = $order_service->getOrderGoods($order_id);
        $order_info = $order_service->getOrderInfo($order_id);
        $list[0] = $order_goods_list;
        $list[1] = $order_info;
        return $list;
    }

    /**
     * 订单价格调整
     */
    public function orderAdjustMoney($order_id, $order_goods_id_adjust_array, $shipping_fee)
    {
        $order_id = request()->post('order_id', '');
        $order_goods_id_adjust_array = request()->post('order_goods_id_adjust_array', '');
        $shipping_fee = request()->post('shipping_fee', '');
        $order_service = new OrderService();
        $res = $order_service->orderMoneyAdjust($order_id, $order_goods_id_adjust_array, $shipping_fee);
        return AjaxReturn($res);
    }

    public function test()
    {
        $order_service = new OrderService();
        $list = $order_service->test();
        var_dump($list);
    }

    public function orderGoodsOpertion()
    {
        $order_goods = new OrderGoods();
        $order_id = 14;
        $order_goods_id = 35;
        
        // 申请退款
        $refund_type = 2;
        $refund_require_money = 202;
        $refund_reason = '不想买了';
        $retval = $order_goods->orderGoodsRefundAskfor($order_id, $order_goods_id, $refund_type, $refund_require_money, $refund_reason);
        
        // 卖家同意退款
        // $retval = $order_goods->orderGoodsRefundAgree($order_id, $order_goods_id);
        
        // 卖家确认退款
        // $refund_real_money = 10;
        // $retval = $order_goods->orderGoodsConfirmRefund($order_id, $order_goods_id, $refund_real_money,0);
        
        // 买家退货
        // $refund_shipping_company = 8;
        // $refund_shipping_code = '545654465';
        // $retval = $order_goods->orderGoodsReturnGoods($order_id ,$order_goods_id, $refund_shipping_company, $refund_shipping_code);
        
        // 卖家确认收货
        // $retval = $order_goods->orderGoodsConfirmRecieve($order_id, $order_goods_id);
        
        // 买家取消订单
        // $retval = $order_goods->orderGoodsCancel($order_id ,$order_goods_id);
        
        // 卖家拒绝退款
        // $retval = $order_goods->orderGoodsRefuseForever($order_id, $order_goods_id);
        
        // 卖家拒绝本次退款
        // $retval = $order_goods->orderGoodsRefuseOnce($order_id, $order_goods_id);
        // $orderGoodsList = NsOrderGoodsModel::where("order_id=$order_id AND refund_status<>0 AND refund_status<>5")->select();
        // $map = array("order_id"=>$order_id, "refund_status"=>array("neq", 0), "refund_status"=>array("neq", 5));
        // $orderGoodsList = NsOrderGoodsModel::all($map);
        // $refund_count = count($orderGoodsList);
        // $orderGoodsListTotal = NsOrderGoodsModel::where("order_id=$order_id AND refund_status=5")->count();
        // $total_count = count($orderGoodsListTotal);
        // $retval = $orderGoodsListTotal;
        var_dump($retval);
    }

    /**
     * 买家申请退款
     *
     * @return Ambigous <number, \data\service\niushop\Order\Exception, \data\service\niushop\Order\Ambigous>
     */
    public function orderGoodsRefundAskfor()
    {
        $order_id = request()->post('order_id', '');
        $order_goods_id = request()->post('order_goods_id', '');
        $refund_type = request()->post('refund_type', '');
        $refund_require_money = request()->post('refund_require_money', 0);
        $refund_reason = request()->post('refund_reason', '');
        if (empty($order_id) || empty($order_goods_id) || empty($refund_type) || empty($refund_require_money) || empty($refund_reason)) {
            $this->error('缺少必需参数');
        }
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsRefundAskfor($order_id, $order_goods_id, $refund_type, $refund_require_money, $refund_reason);
        return AjaxReturn($retval);
    }

    /**
     * 买家取消退款
     *
     * @return number
     */
    public function orderGoodsCancel()
    {
        $order_id = request()->post('order_id', '');
        $order_goods_id = request()->post('order_goods_id', '');
        if (empty($order_id) || empty($order_goods_id)) {
            $this->error('缺少必需参数');
        }
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsCancel($order_id, $order_goods_id);
        return AjaxReturn($retval);
    }

    /**
     * 买家退货
     *
     * @return Ambigous <number, \think\false, boolean, string>
     */
    public function orderGoodsReturnGoods()
    {
        $order_id = request()->post('order_id', '');
        $order_goods_id = request()->post('order_goods_id', '');
        if (empty($order_id) || empty($order_goods_id)) {
            $this->error('缺少必需参数');
        }
        $refund_shipping_company = request()->post('refund_shipping_company', '');
        $refund_shipping_code = request()->post('refund_shipping_code', '');
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsReturnGoods($order_id, $order_goods_id, $refund_shipping_company, $refund_shipping_code);
        return AjaxReturn($retval);
    }

    /**
     * 买家同意买家退款申请
     *
     * @return number
     */
    public function orderGoodsRefundAgree()
    {
        $order_id = request()->post('order_id', '');
        $order_goods_id = request()->post('order_goods_id', '');
        if (empty($order_id) || empty($order_goods_id)) {
            $this->error('缺少必需参数');
        }
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsRefundAgree($order_id, $order_goods_id);
        return AjaxReturn($retval);
    }

    /**
     * 买家永久拒绝本次退款
     *
     * @return Ambigous <number, Exception>
     */
    public function orderGoodsRefuseForever()
    {
        $order_id = request()->post('order_id', '');
        $order_goods_id = request()->post('order_goods_id', '');
        if (empty($order_id) || empty($order_goods_id)) {
            $this->error('缺少必需参数');
        }
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsRefuseForever($order_id, $order_goods_id);
        return AjaxReturn($retval);
    }

    /**
     * 卖家拒绝本次退款
     *
     * @return Ambigous <number, Exception>
     */
    public function orderGoodsRefuseOnce()
    {
        $order_id = request()->post('order_id', '');
        $order_goods_id = request()->post('order_goods_id', '');
        if (empty($order_id) || empty($order_goods_id)) {
            $this->error('缺少必需参数');
        }
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsRefuseOnce($order_id, $order_goods_id);
        return AjaxReturn($retval);
    }

    /**
     * 卖家确认收货
     *
     * @return Ambigous <number, Exception>
     */
    public function orderGoodsConfirmRecieve()
    {
        $order_id = request()->post('order_id', '');
        $order_goods_id = request()->post('order_goods_id', '');
        if (empty($order_id) || empty($order_goods_id)) {
            $this->error('缺少必需参数');
        }
        $storage_num = request()->post("storage_num", "");
        $isStorage = request()->post("isStorage", "");
        $goods_id = request()->post("goods_id", '');
        $sku_id = request()->post('sku_id', '');
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsConfirmRecieve($order_id, $order_goods_id, $storage_num, $isStorage, $goods_id, $sku_id);
        return AjaxReturn($retval);
    }

    /**
     * 卖家确认退款
     *
     * @return Ambigous <Exception, unknown>
     */
    public function orderGoodsConfirmRefund()
    {
        $order_id = request()->post('order_id', '');
        $order_goods_id = request()->post('order_goods_id', '');
        $refund_real_money = request()->post('refund_real_money', 0); // 退款金额
        $refund_balance_money = request()->post("refund_balance_money", 0); // 退款余额
        $refund_way = request()->post("refund_way", ""); // 退款方式
        $refund_remark = request()->post("refund_remark", ""); // 退款备注
        if (empty($order_id) || empty($order_goods_id) || $refund_real_money === '' || empty($refund_way)) {
            $this->error('缺少必需参数');
        }
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsConfirmRefund($order_id, $order_goods_id, $refund_real_money, $refund_balance_money, $refund_way, $refund_remark);
        if (is_numeric($retval)) {
            return AjaxReturn($retval);
        } else {
            return array(
                "code" => 0,
                "message" => $retval
            );
        }
    }

    /**
     * 确认退款时，查询买家实际付款金额
     */
    public function orderGoodsRefundMoney()
    {
        $order_service = new OrderService();
        $order_goods_id = request()->post('order_goods_id', '');
        $res = 0;
        if ($order_goods_id != '') {
            $res = $order_service->orderGoodsRefundMoney($order_goods_id);
        }
        return $res;
    }

    /**
     * 获取订单销售统计
     */
    public function getOrderAccount()
    {
        $order_service = new OrderService();
        // 获取日销售统计
        $account = $order_service->getShopOrderAccountDetail($this->instance_id);
        var_dump($account);
    }

    /**
     * 退货设置
     * 任鹏强
     */
    public function returnSetting()
    {
        $child_menu_list = array(
            array(
                'url' => "express/expresscompany",
                'menu_name' => "物流公司",
                "active" => 0
            ),
            array(
                'url' => "config/areamanagement",
                'menu_name' => "地区管理",
                "active" => 0
            ),
            array(
                'url' => "order/returnsetting",
                'menu_name' => "商家地址",
                "active" => 1
            ),
            array(
                'url' => "shop/pickuppointlist",
                'menu_name' => "自提点管理",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointfreight",
                'menu_name' => "自提点运费菜单",
                "active" => 0
            ),
            array(
                'url' => "config/distributionareamanagement",
                'menu_name' => "货到付款地区管理",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        $order_service = new OrderService();
        $shop_id = $this->instance_id;
        if (request()->isAjax()) {
            $address = request()->post('address', '');
            $real_name = request()->post('real_name', '');
            $mobile = request()->post('mobile', '');
            $zipcode = request()->post('zipcode', '');
            $retval = $order_service->updateShopReturnSet($shop_id, $address, $real_name, $mobile, $zipcode);
            return AjaxReturn($retval);
        } else {
            $info = $order_service->getShopReturnSet($shop_id);
            $this->assign('info', $info);
            return view($this->style . "Order/returnSetting");
        }
    }

    /**
     * 提货
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function pickupOrder()
    {
        $order_id = request()->post('order_id', '');
        if (empty($order_id)) {
            $this->error('缺少必需参数');
        }
        $buyer_name = request()->post('buyer_name', '');
        $buyer_phone = request()->post('buyer_phone', '');
        $remark = request()->post('remark', '');
        $order_service = new OrderService();
        $retval = $order_service->pickupOrder($order_id, $buyer_name, $buyer_phone, $remark);
        return AjaxReturn($retval);
    }

    /**
     * 获取物流跟踪信息
     */
    public function getExpressInfo()
    {
        $order_service = new OrderService();
        $order_goods_id = request()->post('order_goods_id');
        $expressinfo = $order_service->getOrderGoodsExpressMessage($order_goods_id);
        return $expressinfo;
    }

    /**
     * 添加备注
     */
    public function addMemo()
    {
        $order_service = new OrderService();
        $order_id = request()->post('order_id');
        $memo = request()->post('memo');
        $result = $order_service->addOrderSellerMemo($order_id, $memo);
        return AjaxReturn($result);
    }

    /**
     * 获取订单备注信息
     *
     * @return unknown
     */
    public function getOrderSellerMemo()
    {
        $order_service = new OrderService();
        $order_id = request()->post('order_id');
        $res = $order_service->getOrderSellerMemo($order_id);
        return $res;
    }

    /**
     * 获取修改收货地址的信息
     *
     * @return string
     */
    public function getOrderUpdateAddress()
    {
        $order_service = new OrderService();
        $order_id = request()->post('order_id');
        $res = $order_service->getOrderReceiveDetail($order_id);
        return $res;
    }

    /**
     * 修改收货地址的信息
     *
     * @return string
     */
    public function updateOrderAddress()
    {
        $order_service = new OrderService();
        $order_id = request()->post('order_id', '');
        $receiver_name = request()->post('receiver_name', '');
        $receiver_mobile = request()->post('receiver_mobile', '');
        $receiver_zip = request()->post('receiver_zip', '');
        $receiver_province = request()->post('seleAreaNext', '');
        $receiver_city = request()->post('seleAreaThird', '');
        $receiver_district = request()->post('seleAreaFouth', '');
        $receiver_address = request()->post('address_detail', '');
        $fixed_telephone = request()->post("fixed_telephone", "");
        $res = $order_service->updateOrderReceiveDetail($order_id, $receiver_mobile, $receiver_province, $receiver_city, $receiver_district, $receiver_address, $receiver_zip, $receiver_name, $fixed_telephone);
        return $res;
    }

    /**
     * 获取省列表
     */
    public function getProvince()
    {
        $address = new Address();
        $province_list = $address->getProvinceList();
        return $province_list;
    }

    /**
     * 获取城市列表
     *
     * @return Ambigous <multitype:\think\static , \think\false, \think\Collection, \think\db\false, PDOStatement, string, \PDOStatement, \think\db\mixed, boolean, unknown, \think\mixed, multitype:, array>
     */
    public function getCity()
    {
        $address = new Address();
        $province_id = request()->post('province_id', 0);
        $city_list = $address->getCityList($province_id);
        return $city_list;
    }

    /**
     * 获取区域地址
     */
    public function getDistrict()
    {
        $address = new Address();
        $city_id = request()->post('city_id', 0);
        $district_list = $address->getDistrictList($city_id);
        return $district_list;
    }

    /**
     * 导出粉丝列表到excal
     */
    public function testExcel()
    {
        // 导出Excel
        $xlsName = "开门记录列表";
        $xlsCell = array(
            array(
                'userid',
                '用户id'
            ),
            array(
                'use_name',
                '使用者姓名'
            )
        );
        $list = array(
            array(
                "userid" => "55",
                "use_name" => "王二小"
            ),
            array(
                "userid" => "56",
                "use_name" => "王二大"
            )
        );
        dataExcel($xlsName, $xlsCell, $list);
    }

    /**
     * 订单数据excel导出
     */
    public function orderDataExcel()
    {
        $xlsName = "订单数据列表";
        $xlsCell = array(
            array(
                'order_no',
                '订单编号'
            ),
            array(
                'create_date',
                '日期'
            ),
            array(
                'receiver_info',
                '收货人信息'
            ),
            array(
                'order_money',
                '订单金额'
            ),
            array(
                'pay_money',
                '实际支付'
            ),
            array(
                'pay_type_name',
                '支付方式'
            ),
            array(
                'shipping_type_name',
                '配送方式'
            ),
            array(
                'pay_status_name',
                '支付状态'
            ),
            array(
                'status_name',
                '发货状态'
            ),
            array(
                'goods_info',
                '商品信息'
            ),
            array(
                'buyer_message',
                '买家留言'
            ),
            array(
                'seller_memo',
                '卖家备注'
            )
        );
        $start_date = request()->get('start_date') == "" ? 0 : getTimeTurnTimeStamp(request()->get('start_date'));
        $end_date = request()->get('end_date') == "" ? 0 : getTimeTurnTimeStamp(request()->get('end_date'));
        $user_name = request()->get('user_name', '');
        $order_no = request()->get('order_no', '');
        $order_status = request()->get('order_status', '');
        $receiver_mobile = request()->get('receiver_mobile', '');
        $payment_type = request()->get('payment_type', '');
        $order_ids = request()->get("order_ids", "");
        
        if ($order_ids != "") {
            $condition["order_id"] = [
                "in",
                $order_ids
            ];
        }
        
        if ($start_date != 0 && $end_date != 0) {
            $condition["create_time"] = [
                [
                    ">",
                    $start_date
                ],
                [
                    "<",
                    $end_date
                ]
            ];
        } elseif ($start_date != 0 && $end_date == 0) {
            $condition["create_time"] = [
                [
                    ">",
                    $start_date
                ]
            ];
        } elseif ($start_date == 0 && $end_date != 0) {
            $condition["create_time"] = [
                [
                    "<",
                    $end_date
                ]
            ];
        }
        if ($order_status != '') {
            // $order_status 1 待发货
            if ($order_status == 1) {
                // 订单状态为待发货实际为已经支付未完成还未发货的订单
                $condition['shipping_status'] = 0; // 0 待发货
                $condition['pay_status'] = 2; // 2 已支付
                $condition['order_status'] = array(
                    'neq',
                    4
                ); // 4 已完成
                $condition['order_status'] = array(
                    'neq',
                    5
                ); // 5 关闭订单
            } else
                $condition['order_status'] = $order_status;
        }
        if (! empty($payment_type)) {
            $condition['payment_type'] = $payment_type;
        }
        if (! empty($user_name)) {
            $condition['receiver_name'] = $user_name;
        }
        if (! empty($order_no)) {
            $condition['order_no'] = $order_no;
        }
        if (! empty($receiver_mobile)) {
            $condition['receiver_mobile'] = $receiver_mobile;
        }
        $condition['shop_id'] = $this->instance_id;
        $condition['order_type'] = 1; // 普通订单
        $order_service = new OrderService();
        $list = $order_service->getOrderList(1, 0, $condition, 'create_time desc');
        $list = $list["data"];
        foreach ($list as $k => $v) {
            $list[$k]["create_date"] = getTimeStampTurnTime($v["create_time"]); // 创建时间
            $list[$k]["receiver_info"] = $v["receiver_name"] . "  " . $v["receiver_mobile"] . "  ".$v["fixed_telephone"]." " . $v["receiver_province_name"] . $v["receiver_city_name"] . $v["receiver_district_name"] . $v["receiver_address"] . "  " . $v["receiver_zip"]; // 创建时间
            if ($v['shipping_type'] == 1) {
                $list[$k]["shipping_type_name"] = '商家配送';
            } elseif ($v['shipping_type'] == 2) {
                $list[$k]["shipping_type_name"] = '门店自提';
            } else {
                $list[$k]["shipping_type_name"] = '';
            }
            if ($v['pay_status'] == 0) {
                $list[$k]["pay_status_name"] = '待付款';
            } elseif ($v['pay_status'] == 2) {
                $list[$k]["pay_status_name"] = '已付款';
            } elseif ($v['pay_status'] == 1) {
                $list[$k]["pay_status_name"] = '支付中';
            }
            $goods_info = "";
            foreach ($v["order_item_list"] as $t => $m) {
                $goods_info .= "商品名称:" . $m["goods_name"] . "  规格:" . $m["sku_name"] . "  商品价格:" . $m["price"] . "  购买数量:" . $m["num"] . "  ";
            }
            $list[$k]["goods_info"] = $goods_info;
        }
        dataExcel($xlsName, $xlsCell, $list);
    }

    /**
     * 订单数据excel导出
     */
    public function virtualOrderDataExcel()
    {
        $xlsName = "订单数据列表";
        $xlsCell = array(
            array(
                'order_no',
                '订单编号'
            ),
            array(
                'create_date',
                '日期'
            ),
            array(
                'receiver_info',
                '收货人信息'
            ),
            array(
                'order_money',
                '订单金额'
            ),
            array(
                'pay_money',
                '实际支付'
            ),
            array(
                'pay_type_name',
                '支付方式'
            ),
            array(
                'pay_status_name',
                '支付状态'
            ),
            array(
                'goods_info',
                '商品信息'
            ),
            array(
                'buyer_message',
                '买家留言'
            ),
            array(
                'seller_memo',
                '卖家备注'
            )
        );
        $start_date = request()->get('start_date') == "" ? 0 : getTimeTurnTimeStamp(request()->get('start_date'));
        $end_date = request()->get('end_date') == "" ? 0 : getTimeTurnTimeStamp(request()->get('end_date'));
        $user_name = request()->get('user_name', '');
        $order_no = request()->get('order_no', '');
        $order_status = request()->get('order_status', '');
        $receiver_mobile = request()->get('receiver_mobile', '');
        $payment_type = request()->get('payment_type', '');
        $order_ids = request()->get("order_ids", "");
        
        if ($order_ids != "") {
            $condition["order_id"] = [
                "in",
                $order_ids
            ];
        }
        
        if ($start_date != 0 && $end_date != 0) {
            $condition["create_time"] = [
                [
                    ">",
                    $start_date
                ],
                [
                    "<",
                    $end_date
                ]
            ];
        } elseif ($start_date != 0 && $end_date == 0) {
            $condition["create_time"] = [
                [
                    ">",
                    $start_date
                ]
            ];
        } elseif ($start_date == 0 && $end_date != 0) {
            $condition["create_time"] = [
                [
                    "<",
                    $end_date
                ]
            ];
        }
        if ($order_status != '') {
            $condition['order_status'] = $order_status;
        }
        if (! empty($payment_type)) {
            $condition['payment_type'] = $payment_type;
        }
        if (! empty($user_name)) {
            $condition['receiver_name'] = $user_name;
        }
        if (! empty($order_no)) {
            $condition['order_no'] = $order_no;
        }
        if (! empty($receiver_mobile)) {
            $condition['receiver_mobile'] = $receiver_mobile;
        }
        $condition['shop_id'] = $this->instance_id;
        $condition['order_type'] = 2; // 虚拟订单
        $order_service = new OrderService();
        $list = $order_service->getOrderList(1, 0, $condition, 'create_time desc');
        $list = $list["data"];
        foreach ($list as $k => $v) {
            $list[$k]["create_date"] = getTimeStampTurnTime($v["create_time"]); // 创建时间
            $list[$k]["receiver_info"] = $v["user_name"] . "  " . $v["receiver_mobile"]; // 创建时间
            if ($v['pay_status'] == 0) {
                $list[$k]["pay_status_name"] = '待付款';
            } elseif ($v['pay_status'] == 2) {
                $list[$k]["pay_status_name"] = '已付款';
            }
            $goods_info = "";
            foreach ($v["order_item_list"] as $t => $m) {
                $goods_info .= "商品名称:" . $m["goods_name"] . "  规格:" . $m["sku_name"] . "  商品价格:" . $m["price"] . "  购买数量:" . $m["num"] . "  ";
            }
            $list[$k]["goods_info"] = $goods_info;
        }
        dataExcel($xlsName, $xlsCell, $list);
    }

    public function getOrderGoodsDetialAjax()
    {
        if (request()->isAjax()) {
            $order_goods_id = request()->post("order_goods_id", '');
            $order_goods = new OrderGoods();
            $res = $order_goods->getOrderGoodsRefundDetail($order_goods_id);
            return $res;
        }
    }

    /**
     * 收货
     */
    public function orderTakeDelivery()
    {
        $order_service = new OrderService();
        $order_id = request()->post('order_id', '');
        $res = $order_service->OrderTakeDelivery($order_id);
        return AjaxReturn($res);
    }

    /**
     * 删除订单
     */
    public function deleteOrder()
    {
        if (request()->isAjax()) {
            $order_service = new OrderService();
            $order_id = request()->post("order_id", "");
            $res = $order_service->deleteOrder($order_id, 1, $this->instance_id);
            return AjaxReturn($res);
        }
    }

    /**
     * 订单退款（测试）
     */
    public function orderrefundtest()
    {
        $weixin_pay = new WeiXinPay();
        $retval = $weixin_pay->setWeiXinRefund($refund_no, $out_trade_no, $refund_fee, $total_fee);
        var_dump($retval);
    }

    /**
     * 支付宝退款（测试）
     * 创建时间：2017年10月17日 10:26:05
     */
    public function aliPayRefundtest()
    {
        $ali_pay = new AliPay();
        $retval = $ali_pay->aliPayRefund(date("YmdHis", time()) . rand(100000, 999999), $out_trade_no, $refund_fee);
        $this->redirect($retval);
    }

    public function aliPayTransfer()
    {
        $ali_pay = new AliPay();
        $retval = $ali_pay->aliPayTransfer(date("YmdHis", time()) . rand(100000, 999999), '595566388@qq.com', 1);
        $this->redirect($retval);
    }

    /**
     * 查询订单项实际可退款余额
     * 创建时间：2017年10月16日 09:57:56 王永杰
     */
    public function getOrderGoodsRefundBalance()
    {
        $order_goods_id = request()->post("order_goods_id", "");
        if (! empty($order_goods_id)) {
            $order_goods = new OrderGoods();
            $refund_balance = $order_goods->orderGoodsRefundBalance($order_goods_id);
            return $refund_balance;
        }
        return 0;
    }

    /**
     * 查询当前订单的付款方式，用于进行退款操作时，选择退款方式
     * 创建时间：2017年10月16日 10:01:55 王永杰
     */
    public function getOrderTermsOfPayment()
    {
        $order_id = request()->post("order_id", "");
        if (! empty($order_id)) {
            $order = new OrderService();
            $payment_type = $order->getTermsOfPaymentByOrderId($order_id);
            $type = OrderStatus::getPayType($payment_type);
            $json = array();
            if ($type == "微信支付") {
                $temp['type_id'] = 1;
                $temp['type_name'] = "微信";
                array_push($json, $temp);
                $temp['type_id'] = 10;
                $temp['type_name'] = "线下";
                array_push($json, $temp);
            } elseif ($type == "支付宝") {
                $temp['type_id'] = 2;
                $temp['type_name'] = "支付宝";
                array_push($json, $temp);
                $temp['type_id'] = 10;
                $temp['type_name'] = "线下";
                array_push($json, $temp);
            } else {
                $temp['type_id'] = 10;
                $temp['type_name'] = "线下";
                array_push($json, $temp);
            }
            return json_encode($json);
        }
        return "";
    }

    /**
     * 检测支付配置是否开启，支付配置和原路退款配置都要开启才行（配置信息也要填写）
     * 创建时间：2017年10月17日 15:00:29 王永杰
     *
     * @return boolean
     */
    public function checkPayConfigEnabled()
    {
        $type = request()->post("type", "");
        if (! empty($type)) {
            $config = new Config();
            $enabled = $config->checkPayConfigEnabled($this->instance_id, $type);
            return $enabled;
        }
        return "";
    }

    /**
     * 获取出货商品列表
     */
    public function getShippingList()
    {
        if (request()->isAjax()) {
            $order_ids = request()->post("order_ids", "");
            $order_goods = new OrderGoods();
            $list = $order_goods->getShippingList($order_ids);
            return $list;
        }
    }

    /**
     * 出货单打印页面
     */
    public function printpreviewOfInvoice()
    {
        $order_ids = request()->get("order_ids", "");
        $order_goods = new OrderGoods();
        $list = $order_goods->getShippingList($order_ids);
        $this->assign("list", $list);
        $webSiteInfo = $this->website->getWebSiteInfo();
        if (empty($webSiteInfo["title"])) {
            $ShopName = "Niushop开源商城";
        } else {
            $ShopName = $webSiteInfo["title"];
        }
        $this->assign("ShopName", $ShopName);
        $this->assign("now_time", time());
        return view($this->style . "Order/printpreviewOfInvoice");
    }

    /**
     * 添加临时物流信息
     */
    public function addTmpExpressInformation()
    {
        $order_goods = new OrderGoods();
        $print_order_arr = request()->post("print_order_arr", "");
        $deliver_goods = request()->post("deliver_goods", 0);
        $print_order_arr = json_decode($print_order_arr, true);
        $res = $order_goods->addTmpExpressInformation($print_order_arr, $deliver_goods);
        return $res;
    }

    /**
     * 获取未发货的订单
     */
    public function getNotshippedOrderList()
    {
        $order_ids = request()->post("ids", "");
        $order = new OrderService();
        $list = $order->getNotshippedOrderByOrderId($order_ids);
        return $list;
    }

    /**
     * 打印订单
     */
    public function printOrder()
    {
        // 网站信息
        $web_info = $webSiteInfo = $this->website->getWebSiteInfo();
        $this->assign("web_info", $web_info);
        $order_ids = request()->get("print_order_ids", "");
        $order_service = new OrderService();
        $condition = array(
            "order_id" => array(
                "in",
                $order_ids
            ),
            "shop_id" => $this->instance_id,
            'order_type' => 1
        );
        $list = $order_service->getOrderList(1, 0, $condition, '');
        foreach ($list["data"] as $k => $v) {
            $order_detail = $order_service->getOrderDetail($v["order_id"]);
            $list["data"][$k]["goods_packet_list"] = $order_detail["goods_packet_list"];
        }
        $this->assign("order_list", $list['data']);
        return view($this->style . "Order/printOrder");
    }

    /**
     * 打印虚拟订单
     */
    public function printVirtualOrder()
    {
        // 网站信息
        $web_info = $webSiteInfo = $this->website->getWebSiteInfo();
        $this->assign("web_info", $web_info);
        $order_ids = request()->get("print_order_ids", "");
        $order_service = new OrderService();
        $condition = array(
            "order_id" => array(
                "in",
                $order_ids
            ),
            "shop_id" => $this->instance_id,
            'order_type' => 2
        );
        $list = $order_service->getOrderList(1, 0, $condition, '');
        $this->assign("order_list", $list['data']);
        return view($this->style . "Order/printVirtualOrder");
    }
}