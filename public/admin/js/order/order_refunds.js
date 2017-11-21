/**
 * 订单退款功能
 * 创建时间：2017年10月16日 10:18:56 王永杰
 */

$(function(){
	
	//选择退款方式时触发事件
	$("#refund_way_select").change(function(){
		checkPayConfigEnabled();
	});
	
	//第一次退款确认
	$(".js-confirm-refund-ok").click(function(){
		
		var order_id = $("#confirm_order_id").val();
		var order_goods_id = $("#confirm_order_goods_id").val();
		var refund_money = $("#refund_money_input").val();
		var time = 5;
		if(validation()){
			var html = "";
			var html_prompt = "";//温馨提示
			var type = "";
			switch(parseInt($("#refund_way_select").val())){
			case 1:
				html += "<p>当前退款方式为<strong style='color: #FF5722;'>微信退款</strong></p>";
				type = "wechat";
				break;
			case 2:
				html += "<p>当前退款方式为<strong style='color: #FF5722;'>支付宝退款</strong></p>";
				type = "alipay";
				break;
			case 10:
				html += '<p>当前退款方式为<strong style="color: #FF5722;">线下退款(需要商家手动转账给买家)</strong></p>';
				html_prompt += '<p style="color:red;font-weight:bold;">点击确定后，系统将视为您已经给买家手动转账</p>';
				break;
			}

			if(parseFloat($("#refund_money_input").val()) != parseFloat($("#pay_money").attr("data-pay-money"))){
				html_prompt = '<p>注意:您输入的退款金额与买家实际付款金额不一致,可能会导致退款失败,请核对好退款信息！</p>';
			}
			
			$(".js-confirmation").html(html);
			$(".js-confirmation-prompt").html(html_prompt).show();
			$("#confirmRefund").modal('hide');
			$("#refundOperationReminder").css("margin-top","-" + parseFloat($("#refundOperationReminder").outerHeight()/2) + "px").modal("show");
			$("#countdown_refund_confirm").attr("data-order-id",order_id);
			$("#countdown_refund_confirm").attr("data-order-goods-id",order_goods_id);
			$("#countdown_refund_confirm").attr("data-refund-money",refund_money);
			$("#countdown_refund_confirm").text("确定(" + time + ")").addClass("disabled");
			if(countdown_btn == null){
				countdown_btn = setInterval(function(){
					time--;
					$("#countdown_refund_confirm").text("确定(" + time + ")");
					if(time<=0){
						clearInterval(countdown_btn);
						countdown_btn = null;
						$("#countdown_refund_confirm").text("确定").removeClass("disabled");
					}
				},1000);
			}else{
				clearInterval(countdown_btn);
				countdown_btn = null;
			}
		}
	});
	
	//再次确认退款操作后，开始退款
	$("#countdown_refund_confirm").click(function(){
		if(!$(this).hasClass("disabled") && $(this).attr("data-order-id") && $(this).attr("data-order-goods-id")){
			orderGoodsConfirmRefund($(this).attr("data-order-id"),$(this).attr("data-order-goods-id"),$(this).attr("data-refund-money"),$("#balance_refund").attr("data-refund-balance-money"));
		}
	});
})
/**
 * 查询订单项实际可退款的余额
 * 创建时间：2017年10月16日 09:56:26 王永杰
 */
function getOrderGoodsRefundBalance(order_goods_id){
	if(order_goods_id != ""){
		$.ajax({
			type : "post",
			async : false,
			url : __URL(ADMINMAIN + "/order/getordergoodsrefundbalance"),
			data : { "order_goods_id" : order_goods_id },
			success : function(res){
				if(res>0){
					$("#balance_refund").text(parseFloat(res).toFixed(2) + "元").attr("data-refund-balance-money",res);
					$("#balance_refund").parent().show();
				}else{
					$("#balance_refund").parent().hide().attr("data-refund-balance-money","0");;
				}
			}
			
		});
	}
}

//查询买家实际支付金额
function orderGoodsRefundMoney(order_goods_id){
	$.ajax({
		url : __URL(ADMINMAIN + "/order/ordergoodsrefundmoney"),
		type : "post",
		data : { "order_goods_id" : order_goods_id},
		success : function(data){
			$("#pay_money").text(parseFloat(data).toFixed(2) + "元").attr("data-pay-money",data);
			if(data == 0) $("#refund_money_input").attr("readonly","readonly").val(parseFloat(data).toFixed(2));
			else $("#refund_money_input").removeAttr("readonly value");
		}
	})
}

/**
 * 查询当前订单的付款方式
 * 创建时间：2017年10月16日 10:15:34 王永杰
 */
function getOrderTermsOfPayment(order_id){
	if(order_id != ""){
		$.ajax({
			type : "post",
			async : false,
			url : __URL(ADMINMAIN + "/order/getordertermsofpayment"),
			data : { "order_id" : order_id },
			success : function(res){
				if(res != ""){
					var list = eval("(" + res + ")");
					if(list != undefined){
						var html = "";
						for(var i=0;i<list.length;i++){
							html += "<option value='" + list[i].type_id + "'>" + list[i].type_name + "</option>";
						}
						$("#refund_way_select").html(html);
						checkPayConfigEnabled();
					}
				}
			}
		})
	}
}

var confirm_refund_flag = false;//防止重复提交
//退款
function orderGoodsConfirmRefund(order_id,order_goods_id,refund_money,refund_balance_money){

//	console.log(order_id+","+order_goods_id+","+refund_money+","+refund_balance_money);
	if(confirm_refund_flag) return;
	confirm_refund_flag = true;
	if($("#refund-remark").val().length>200){
		showTip("退款备注，最多可输入200个字符","warning");
		confirm_refund_flag = false;
		return;
	}
	$.ajax({
		type : "post",
		url : __URL(ADMINMAIN + "/order/ordergoodsconfirmrefund"),
		data : {'order_id':order_id,"order_goods_id":order_goods_id, "refund_real_money":refund_money, "refund_balance_money" : refund_balance_money, "refund_way" : $("#refund_way_select").val(), "refund_remark" : $("#refund-remark").val() },
		success : function(data) {
			
			if (data['code'] > 0) {
				$("#refundOperationReminder").modal("hide")
				showTip("退款已成功，请注意查看","success");
				setTimeout(function(){
					window.location.reload()
				},2000);
			} else {
				showTip(data['message'],"error");
				confirm_refund_flag = false;
			}
		}
	});
}

//检测支付配置是否开启，支付配置和原路退款配置都要开启才行
function checkPayConfigEnabled(){

	var type = "";
	switch(parseInt($("#refund_way_select").val())){
	case 1:
		type = "wechat";
		break;
	case 2:
		type = "alipay";
		break;
	}
	if(type != ""){
		$.ajax({
			type : "post",
			url : __URL(ADMINMAIN + "/order/checkpayconfigenabled"),
			data : { "type" : type },
			success : function(res){
				$(".js-not-configured-prompt").html(res);
				if(res != ""){
					$(".js-not-configured-prompt").attr("data-Whether-through",0).show();
					$(".js-confirm-refund-ok").addClass("disabled");
				}else{
					$(".js-not-configured-prompt").attr("data-Whether-through",1).hide();
					$(".js-confirm-refund-ok").removeClass("disabled");
				}
			}
		});
	}else{
		$(".js-not-configured-prompt").empty().attr("data-Whether-through",1);
		$(".js-confirm-refund-ok").removeClass("disabled");
	}
}

//验证用户输入的退款金额是否合法
function validation(){
	var refund_money = $("#refund_money_input").val();
	var pay_money = $("#pay_money").attr("data-pay-money");
	if($(".js-confirm-refund-ok").hasClass("disabled")){
		return false;
	}
	if(refund_money == ""){
		$("#refund_money_input").next().css("display","inline-block").text("请输入退款金额");
		$("#refund_money_input").focus();
		return false;
	}else{
		$("#refund_money_input").next().css("display","none");
	}
	
	if(isNaN(refund_money)){
		$("#refund_money_input").next().css("display","inline-block").text("请输入数字");
		$("#refund_money_input").focus();
		return false;
	}
	
	if(parseFloat(refund_money) < 0 || parseFloat(refund_money)>parseFloat(pay_money)){
		$("#refund_money_input").next().css("display","inline-block").text("退款金额必须大于等于0元小于"+parseFloat(pay_money).toFixed(2)+"元");
		$("#refund_money_input").focus();
		return false;
	}
	
	if(parseInt($(".js-not-configured-prompt").attr("data-Whether-through")) == 0){
		return false;
	}
	
	return true;
}

var countdown_btn = null;//退款按钮倒计时，5秒

//同意退款
function agreeRefund(){
	var order_id = $("#agreee_order_id").val();
	var order_goods_id = $("#agree_order_goods_id").val();
	$.ajax({
		type : "post",
		url : __URL(ADMINMAIN + "/order/ordergoodsrefundagree"),
		data : {'order_id':order_id,"order_goods_id":order_goods_id},
		success : function(data) {
			if (data['code'] > 0) {
				showMessage('success', data["message"],window.location.reload());
			} else {
				showMessage('error', data["code"]);
			}
		}
	});
}

/**
 * 确认退款界面显示
 * refund_require_money 退款金额
 */
function confirmRefund(order_id,order_goods_id,refund_require_money){
	if(countdown_btn != null){
		clearInterval(countdown_btn);
		countdown_btn = null;//重新倒计时
	}
	$("#confirm_order_id").val(order_id);
	$("#confirm_order_goods_id").val(order_goods_id);
	$("#apply_money").text(parseFloat(refund_require_money).toFixed(2) + "元");
	$("#refund_money_input").next().css("display","none");
	orderGoodsRefundMoney(order_goods_id);
	getOrderGoodsRefundBalance(order_goods_id);
	getOrderTermsOfPayment(order_id);
	$("#confirmRefund").css("margin-top","-" + parseFloat($("#confirmRefund").outerHeight()/2) + "px").modal('show');
}