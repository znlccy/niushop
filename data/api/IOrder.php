<?php
/**
 * IOrder.php
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
namespace data\api;

/**
 * 订单接口
 */
interface IOrder
{

    /**
     * 添加订单
     *
     * @param unknown $data            
     */
    function addOrder($data);

    /**
     * 获取订单详情
     *
     * @param unknown $order_id            
     */
    function getOrderDetail($order_id);

    /**
     * 获取订单列表
     *
     * @param number $page_index            
     * @param number $page_size            
     * @param string $condition            
     * @param string $order            
     */
    function getOrderList($page_index = 1, $page_size = 0, $condition = '', $order = '');

    /**
     * 订单创建
     *
     * @param unknown $order_type            
     * @param unknown $out_trade_no            
     * @param unknown $pay_type            
     * @param unknown $shipping_type            
     * @param unknown $order_from            
     * @param unknown $buyer_ip            
     * @param unknown $buyer_message            
     * @param unknown $buyer_invoice            
     * @param unknown $shipping_time            
     * @param unknown $receiver_mobile            
     * @param unknown $receiver_province            
     * @param unknown $receiver_city            
     * @param unknown $receiver_district            
     * @param unknown $receiver_address            
     * @param unknown $receiver_zip            
     * @param unknown $receiver_name            
     * @param unknown $point            
     * @param unknown $point_money            
     * @param unknown $coupon_money            
     * @param unknown $coupon_id            
     * @param unknown $user_money            
     * @param unknown $promotion_money            
     * @param unknown $shipping_money            
     * @param unknown $pay_money            
     * @param unknown $give_point            
     * @param unknown $goods_sku_list            
     * @param unknown $platform_money            
     */
    function orderCreate($order_type, $out_trade_no, $pay_type, $shipping_type, $order_from, $buyer_ip, $buyer_message, $buyer_invoice, $shipping_time, $receiver_mobile, $receiver_province, $receiver_city, $receiver_district, $receiver_address, $receiver_zip, $receiver_name, $point, $coupon_id, $user_money, $goods_sku_list, $platform_money, $pick_up_id, $shipping_company_id, $coin = 0, $fixed_telephone = "");

    /**
     * 订单物流发货
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id_array
     *            //订单项ID列 ','隔开
     * @param unknown $express_name
     *            //物流公司名称
     * @param unknown $shipping_type
     *            //物流方式
     * @param unknown $express_company_id
     *            //物流公司ID
     * @param unknown $express_no
     *            //运单编号
     */
    function orderDelivery($order_id, $order_goods_id_array, $express_name, $shipping_type, $express_company_id, $express_no);

    /**
     * 订单不执行物流发货
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id_array            
     */
    function orderGoodsDelivery($order_id, $order_goods_id_array);

    /**
     * 订单执行交易关闭
     *
     * @param unknown $order_id            
     */
    function orderClose($order_id);

    /**
     * 订单执行交易完成
     *
     * @param unknown $orderid            
     */
    function orderComplete($orderid);

    /**
     * 订单线上支付完成
     *
     * @param unknown $order_pay_no            
     * @param unknown $pay_type            
     */
    function orderOnLinePay($order_pay_no, $pay_type);

    /**
     * 订单线下支付或后期支付
     *
     * @param unknown $order_id            
     * @param unknown $status
     *            0:订单支付 1：交易完成
     */
    function orderOffLinePay($order_id, $pay_type, $status);

    /**
     * 查询订单
     *
     * @param unknown $where            
     * @param string $fields            
     */
    function orderQuery($where, $fields = "*");

    /**
     * 订单金额调整
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id_adjust_array
     *            订单项数列 order_goods_id,adjust_money;order_goods_id,adjust_money
     * @param unknown $shipping_fee            
     */
    function orderMoneyAdjust($order_id, $order_goods_id_adjust_array, $shipping_fee);

    /**
     * 查询订单项退款信息
     *
     * @param unknown $order_goods_id            
     */
    function getOrderGoodsRefundInfo($order_goods_id);

    /**
     * 查询订单的订单项列表
     *
     * @param unknown $order_id            
     */
    function getOrderGoods($order_id);

    /**
     * 查询订单的订单项列表
     *
     * @param unknown $order_id            
     */
    function getOrderGoodsInfo($order_goods_id);

    /**
     * 买家退款申请
     *
     * @param unknown $order_id
     *            订单ID
     * @param unknown $order_goods_id_array
     *            订单项ID (','隔开)
     * @param unknown $refund_type            
     * @param unknown $refund_require_money
     *            //需要退款金额
     * @param unknown $refund_reason
     *            //退款原因
     * @return number|Exception|Ambigous <number, \think\false>
     */
    function orderGoodsRefundAskfor($order_id, $order_goods_id, $refund_type, $refund_require_money, $refund_reason);

    /**
     * 买家取消退款
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id            
     */
    function orderGoodsCancel($order_id, $order_goods_id);

    /**
     * 买家退货
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id            
     * @param unknown $refund_shipping_company
     *            //退货物流公司名称
     * @param unknown $refund_shipping_code
     *            //退货物流运单号
     */
    function orderGoodsReturnGoods($order_id, $order_goods_id, $refund_shipping_company, $refund_shipping_code);

    /**
     * 卖家同意买家退款申请
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id            
     */
    function orderGoodsRefundAgree($order_id, $order_goods_id);

    /**
     * 卖家永久决绝退款
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id            
     */
    function orderGoodsRefuseForever($order_id, $order_goods_id);

    /**
     * 卖家拒绝本次退款
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id            
     */
    function orderGoodsRefuseOnce($order_id, $order_goods_id);

    /**
     * 卖家确认收货
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id            
     */
    function orderGoodsConfirmRecieve($order_id, $order_goods_id, $storage_num, $isStorage, $goods_id, $sku_id);

    /**
     * 卖家确认退款
     *
     * @param 订单id $order_id            
     * @param 订单项id $order_goods_id            
     * @param 退款金额 $refund_real_money            
     * @param 退款余额 $refund_balance_money            
     * @param 退款方式 $refund_way            
     * @param 退款备注 $remark            
     *
     */
    function orderGoodsConfirmRefund($order_id, $order_goods_id, $refund_real_money, $refund_balance_money, $refund_way, $remark);

    /**
     * 获取对应sku列表价格
     *
     * @param unknown $goods_sku_list            
     */
    function getGoodsSkuListPrice($goods_sku_list);

    /**
     * 获取邮费
     *
     * @param unknown $goods_sku_list            
     * @param unknown $province            
     * @param unknown $city            
     * @return Ambigous <unknown, number>
     */
    function getExpressFee($goods_sku_list, $express_company_id, $province, $city, $district);

    /**
     * 获取支付编号
     */
    function getOrderTradeNo();

    /**
     * 订单实际退款金额
     *
     * @param unknown $order_goods_id
     *            //订单商品ID（订单项）
     */
    function orderGoodsRefundMoney($order_goods_id);

    /**
     * 获取用户可使用优惠券
     *
     * @param unknown $goods_sku_list
     *            商品sku列表 skuid:num,skuid:num
     */
    function getMemberCouponList($goods_sku_list);

    /**
     * 获取订单新的支付流水号
     *
     * @param unknown $order_id            
     */
    function getOrderNewOutTradeNo($order_id);

    /**
     * 获取购买商品可用积分
     *
     * @param unknown $goods_sku_list            
     */
    function getGoodsSkuListUsePoint($goods_sku_list);

    /**
     * 订单收货
     *
     * @param unknown $order_id            
     */
    function OrderTakeDelivery($order_id);

    /**
     * 删除购物车
     *
     * @param unknown $gooods_sku_list            
     * @param unknown $uid            
     */
    function deleteCart($gooods_sku_list, $uid);

    /**
     * 获取某种条件下订单数量
     *
     */
    function getOrderCount($condition);

    /**
     * 获取某种条件 订单总金额（元）
     */
    function getPayMoneySum($condition);

    /**
     * 获取某种条件 订单量（件）
     *
     * @param unknown $condition            
     */
    function getGoodsNumSum($condition);

    /**
     * 获取具体配送状态信息
     *
     * @param unknown $shipping_status_id            
     * @return Ambigous <NULL, multitype:string >
     */
    static function getShippingInfo($shipping_status_id);

    /**
     * 获取具体支付状态信息
     *
     * @param unknown $pay_status_id            
     * @return multitype:multitype:string |string
     */
    static function getPayStatusInfo($pay_status_id);

    /**
     * 获取订单各状态数量
     */
    static function getOrderStatusNum($condition = '');

    /**
     * 商品评价-添加
     *
     * @param unknown $dataList
     *            评价内容的 数组
     * @return Ambigous <multitype:, \think\false>
     */
    function addGoodsEvaluate($dataArr, $order_id);

    /**
     * 商品评价-回复
     *
     * @param unknown $explain_first
     *            评价内容
     * @param unknown $ordergoodsid
     *            订单项ID
     * @return Ambigous <number, \think\false>
     */
    function addGoodsEvaluateExplain($explain_first, $order_goods_id);

    /**
     * 商品评价-追评
     *
     * @param unknown $again_content
     *            追评内容
     * @param unknown $againImageList
     *            传入追评图片的 数组
     * @param unknown $ordergoodsid
     *            订单项ID
     * @return Ambigous <number, \think\false>
     */
    function addGoodsEvaluateAgain($again_content, $againImageList, $order_goods_id);

    /**
     * 商品评价-追评回复
     *
     * @param unknown $again_explain
     *            追评的 回复内容
     * @param unknown $ordergoodsid
     *            订单项ID
     * @return Ambigous <number, \think\false>
     */
    function addGoodsEvaluateAgainExplain($again_explain, $order_goods_id);

    /**
     * 获取指定订单的评价信息
     *
     * @param unknown $orderid
     *            订单ID
     */
    function getOrderEvaluateByOrder($order_id);

    /**
     * 获取指定会员的评价信息
     *
     * @param unknown $uid
     *            会员ID
     */
    function getOrderEvaluateByMember($uid);

    /**
     * 评价信息 分页
     *
     * @param unknown $page_index            
     * @param unknown $page_size            
     * @param unknown $condition            
     * @param unknown $order            
     * @return number
     */
    function getOrderEvaluateDataList($page_index, $page_size, $condition, $order);

    /**
     * 获取评价列表
     *
     * @param unknown $page_index
     *            页码
     * @param unknown $page_size
     *            页大小
     * @param unknown $condition
     *            条件
     * @param unknown $order
     *            排序
     * @return multitype:number unknown
     */
    function getOrderEvaluateList($page_index, $page_size, $condition, $order);

    /**
     * 修改订单数据
     *
     * @param unknown $order_id            
     * @param unknown $data            
     */
    function modifyOrderInfo($data, $order_id);

    /**
     * 获取店铺订单销售统计（统计店铺订单账户）
     *
     * @param unknown $shop_id            
     */
    function getShopOrderStatics($shop_id, $start_time, $end_time);

    /**
     * 获取店铺在一段时间之内账户列表
     *
     * @param unknown $shop_id            
     * @param unknown $start_time            
     * @param unknown $end_time            
     * @param unknown $page_index            
     * @param unknown $page_size            
     */
    function getShopOrderAccountList($shop_id, $start_time, $end_time, $page_index, $page_size);

    /**
     * 获取店铺在一段时间之内订单退款列表
     *
     * @param unknown $shop_id            
     * @param unknown $start_time            
     * @param unknown $end_time            
     * @param unknown $page_index            
     * @param unknown $page_size            
     */
    function getShopOrderRefundList($shop_id, $start_time, $end_time, $page_index, $page_size);

    /**
     * 获取店铺订单账户详情
     *
     * @param unknown $shop_id            
     */
    function getShopOrderAccountDetail($shop_id);

    /**
     * 订单销售概况
     *
     * @param unknown $shop_id            
     * @param unknown $start_date            
     * @param unknown $end_date            
     */
    function getShopAccountCountInfo($shop_id);

    /**
     * 商品销售列表
     *
     * @param unknown $page_index            
     * @param unknown $page_size            
     * @param unknown $condition            
     * @param unknown $order            
     */
    function getShopGoodsSalesList($page_index, $page_size, $condition, $order);

    /**
     * 所有商品销售情况
     *
     * @param unknown $condition            
     */
    function getShopGoodsSalesQuery($shop_id, $start_date, $end_date, $condition);

    /**
     * 查询时间内的下单金额
     */
    function getShopSaleSum($condition);

    /**
     * 查询时间内的下单量
     */
    function getShopSaleNumSum($condition);

    /**
     * 查询店铺的退货设置
     *
     * @param unknown $shop_id            
     */
    function getShopReturnSet($shop_id);

    /**
     * 修改店铺的退货这是
     *
     * @param unknown $shop_id            
     * @param unknown $address            
     * @param unknown $real_name            
     * @param unknown $mobile            
     * @param unknown $zipcode            
     */
    function updateShopReturnSet($shop_id, $address, $real_name, $mobile, $zipcode);

    /**
     * 查询订单的物流信息
     *
     * @param unknown $order_ids            
     * @param unknown $shop_id            
     */
    function getOrderGoodsExpressDetail($order_ids, $shop_id);

    /**
     * 订单提货
     *
     * @param unknown $order_id            
     */
    function pickupOrder($order_id, $buyer_name, $buyer_phone, $remark);

    /**
     * 物流跟踪信息查询
     *
     * @param unknown $order_goods_id            
     */
    function getOrderGoodsExpressMessage($express_id);

    /**
     * 查询订单的发货物流信息
     *
     * @param unknown $order_id            
     */
    function getOrderGoodsExpressList($order_id);

    /**
     * 添加卖家对订单的备注
     *
     * @param unknown $order_id            
     */
    function addOrderSellerMemo($order_id, $memo);

    /**
     * 获取卖家对订单的备注
     *
     * @param unknown $order_id            
     */
    function getOrderSellerMemo($order_id);

    /**
     * 通过订单id 得到收货地址信息
     *
     * @param unknown $order_id            
     */
    function getOrderReceiveDetail($order_id);

    /**
     * 更新订单的收货地址信息
     *
     * @param unknown $order_id            
     * @param unknown $receiver_mobile            
     * @param unknown $receiver_province            
     * @param unknown $receiver_city            
     * @param unknown $receiver_district            
     * @param unknown $receiver_address            
     * @param unknown $receiver_zip            
     * @param unknown $receiver_name            
     */
    function updateOrderReceiveDetail($order_id, $receiver_mobile, $receiver_province, $receiver_city, $receiver_district, $receiver_address, $receiver_zip, $receiver_name,$fixed_telephone);

    /**
     * 获取某个订单状态下订单数目
     *
     * @param unknown $condition            
     */
    function getOrderNumByOrderStatu($condition);

    /**
     * 评论送积分
     */
    function commentPoint($order_id);

    /**
     * 查询用户的某个订单条数
     *
     * @param unknown $user_id            
     * @param unknown $order_id            
     */
    function getUserOrderDetailCount($user_id, $order_id);

    /**
     * 删除订单
     *
     * @param unknown $order_id
     *            订单id
     * @param unknown $operator_type
     *            操作人类型 1商家 2用户
     * @param unknown $operator_id
     *            操作人id
     */
    function deleteOrder($order_id, $operator_type, $operator_id);

    /**
     * 根据外部交易号查询订单编号
     * 创建时间：2017年10月9日 18:26:35 王永杰
     *
     * @param unknown $out_trade_no            
     */
    function getOrderNoByOutTradeNo($out_trade_no);

    /**
     * 根据外部交易号查询订单状态
     * 创建时间：2017年10月13日 14:30:42 王永杰
     *
     * @param unknown $out_trade_no            
     */
    function getOrderStatusByOutTradeNo($out_trade_no);

    /**
     * 根据订单查询付款方式
     * 创建时间：2017年10月16日 10:09:02 王永杰
     *
     * @param 订单id $order_id            
     */
    function getTermsOfPaymentByOrderId($order_id);

    /**
     * 根据订单项id查询订单退款账户记录
     * 创建时间：2017年10月18日 17:29:43
     *
     * @param 订单项id $order_goods_id            
     */
    function getOrderRefundAccountRecordsByOrderGoodsId($order_goods_id);
    
    /**
     * 获取订单打印列表
     * @param unknown $order_ids
     * @param unknown $shop_id
     */
    function getOrderPrint($order_ids, $shop_id);
    
    /**
     * 处理订单打印数据
     * @param unknown $order_id
     * @param unknown $order_goods_list_print
     * @param unknown $is_express
     * @param unknown $express_company_id
     * @param unknown $express_company_name
     * @param unknown $express_no
     * @param unknown $express_id
     */
    function dealPrintOrderGoodsList($order_id, $order_goods_list_print, $is_express, $express_company_id, $express_company_name, $express_no, $express_id);
    
    /**
     * 通过订单id获取未发货订单项
     * @param unknown $order_ids
     */
    function getNotshippedOrderByOrderId($order_ids);
}