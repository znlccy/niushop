/**
 * 订单操作js
 * 作用：订单流程相关操作
 * 2017-01-07
 */

/**
 * 订单操作中转
 * @param no
 * @param order_id
 */
function operation(no,order_id){
	switch(no){
	case 'pay'://支付
		pay(order_id);
		break;
	case 'close'://订单关闭
		orderClose(order_id);
		break;
	case 'getdelivery'://订单收货
		getdelivery(order_id);
		break;
	case 'refund'://申请退款
		orderRefund(order_id);
		break;
	case 'logistics' ://查看物流
		logistics(order_id);
		break;
	case 'delete_order'://删除订单
		delete_order(order_id);
		break;
	default:
		break;
	}
}
/**
 * 微信支付
 * @param order_id
 */
function pay(order_id){
	window.location.href = __URL(APPMAIN+ "/order/orderpay?id="+order_id);
}

/**
 * 查看物流
 */
function logistics(order_id){
	window.location.href = __URL(APPMAIN+ "/order/orderexpress?orderId="+order_id);
}
/**
 * 订单交易关闭
 * @param order_id
 */
function orderClose(order_id){
	$.ajax({
		type : "post",
		url : __URL(APPMAIN+ "/order/orderclose"),
		data : {
			"order_id" : order_id
		},
		success : function(data) {
			if(data["code"] > 0 ){
				showBox("关闭成功");
				window.location.reload();
			}
		}
	})
}

/**
 * 订单收货
 * @param order_id
 */
function getdelivery(order_id){
	$.ajax({
		type : "post",
		url : __URL(APPMAIN+ "/order/ordertakedelivery"),
		data : { "order_id" : order_id },
		success : function(data) {
			if(data["code"] > 0 ){
				showBox("收货成功");
				window.location = __URL(APPMAIN+ "/order/myorderlist?shop=0");
			}
		}
	})
}

/**
 * 删除订单
 * @param order_id
 */
function delete_order(order_id){
	$.ajax({
		type : "post",
		url : __URL(APPMAIN+ "/order/deleteOrder"),
		data : {
			"order_id" : order_id
		},
		success : function(data) {
			if(data["code"] > 0 ){
				showBox("订单删除成功");
				window.location.reload();
			}
		}
	})
}