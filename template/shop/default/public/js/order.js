/**
 * 订单操作中转流程相关操作
 * 修改时间：2017年9月21日 14:32:21 王永杰
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
	case 'delete_order'://删除订单
		delete_order(order_id);
		break;
	case 'logistics' ://查看物流
		logistics(order_id);
		break;
	}
}
/**
 * 微信支付
 * @param order_id
 */
function pay(order_id){
	//去支付
	window.location.href = __URL(SHOPMAIN+"/order/orderPay?id="+order_id);
}

/**
 * 查看物流
 */
function logistics(order_id){
	window.location.href = __URL(SHOPMAIN+ "/member/orderdetail?orderid="+order_id);
}

/**
 * 订单交易关闭
 * @param order_id
 */
function orderClose(order_id){
	$( "#dialog" ).dialog({
		buttons: {
			"确定": function() {
				$.ajax({
					type : "post",
					url : __URL(SHOPMAIN+"/order/orderClose"),
					data : { "order_id" : order_id },
					success : function(data) {
						if(data["code"] > 0 ){
							$.msg("操作成功");
							location.href=__URL(SHOPMAIN+"/member/orderlist?status=0");
						}
					}
				})
				$(this).dialog('close');
			},
			"取消,#e57373": function() {
				$(this).dialog('close');
			},
		},
	contentText:"确定关闭订单吗？",
	});
}

/**
 * 订单收货
 * @param order_id
 */
function getdelivery(order_id){
	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN+"/order/orderTakeDelivery"),
		data : { "order_id" : order_id },
		success : function(data) {
			if(data["code"] > 0 ){
				 $.msg("收货成功");
				location.href=__URL(SHOPMAIN+"/member/orderlist?status=3");
			}
		}
	})
}

//删除订单
function delete_order(order_id){
	$( "#dialog" ).dialog({
		buttons: {
			"确定": function() {
				$.ajax({
					type : "post",
					url : __URL(SHOPMAIN+"/order/deleteOrder"),
					data : {"order_id" : order_id},
					success : function(data) {
						if(data["code"] > 0 ){
							showMessage('success', data["message"],window.location.reload());
						}
					}
				});
				$(this).dialog('close');
			},
			"取消,#e57373": function() {
				$(this).dialog('close');
			},
		},
		contentText:"确定要删除订单吗？",
	});
}