/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : 王永杰
 * @date : 2017年11月22日 10:28:15
 * @version : v1.0.0.0
 * PC端待付款虚拟商品订单
 */
$(function(){
	
	//初始化
	init();
	
	/**
	 * 提交订单
	 */
	var flag = false;//防止重复提交
	$(".btn-jiesuan").click(function(){
		if(validationOrder()){
			if(flag) return;
			flag = true;
			var goods_sku_list = $("#goods_sku_list").val();// 商品Skulist
			var leavemessage = $("#leavemessage").val();// 订单留言
			var use_coupon = getUseCoupon();//优惠券id
			var account_balance = 0;//可用余额
			if($("#account_balance").val() != undefined){
				account_balance = $("#account_balance").val() == "" ? 0 : $("#account_balance").val();
			}
			var integral = $("#count_point_exchange").val() == "" ? 0 : $("#count_point_exchange").val();//积分
			var pay_type = parseInt($("#paylist li a[class='selected']").attr("data-select"));//支付方式 0：在线支付
			var buyer_invoice = getInvoiceContent();//发票
//			return;
			$.ajax({
				url : __URL(SHOPMAIN + "/order/ordercreatevirtual"),
				type : "post",
				data : {
					'goods_sku_list' : goods_sku_list,
					'leavemessage' : leavemessage,
					'use_coupon' : use_coupon.id,
					'integral' : integral,
					'account_balance' : account_balance,
					'pay_type' : pay_type,
					'buyer_invoice' : buyer_invoice,
					'user_telephone' : $("#user_telephone").val()
				},
				success : function(res) {
					if (res.code > 0) {
						$(".btn-jiesuan").css("background-color","#ccc");
						//如果实际付款金额为0，跳转到个人中心的订单界面中
						if(parseFloat($("#realprice").attr("data-total-money")) == 0){
							location.href = __URL(APPMAIN + '/pay/paycallback?msg=1&out_trade_no=' + res.code);
						}else{
							window.location.href = __URL(APPMAIN + '/pay/getpayvalue?out_trade_no=' + res.code);
						}
					}else{
						$.msg(res.message,{time : 5000});
						flag = false;
					}
				}
			});
		}
	});
	
	/**
	 * 选择发票内容
	 * 2017年6月14日 19:41:31 王永杰
	 */
	$("#invoice_con li").click(function(){
		$("#invoice_con li").children("i").hide();
		$("#invoice_con li").children("a").removeClass("selected");
		$(this).children("i").show();
		$(this).children("a").addClass("selected");
	});
	
	/**
	 * 用户输入可用余额，进行验证并矫正，同时更新总优惠、应付金额等数据
	 * 规则：
	 * 1、可用余额，不可超过订单总计
	 * 2、不可超过用户最大可用余额
	 * 3、只能输入数字
	 * 2017年6月22日 15:00:14 王永杰
	 */
	$("#account_balance").keyup(function(){
		if(!validationMemberBalance()){
			calculateTotalAmount();
		}
	});
	
	/**
	 * 选择优惠券
	 * 2017年6月19日 18:45:58 王永杰
	 */
	$("#coupon").change(function(){
		calculateTotalAmount();
	});
	
	/**
	 * 发票选择
	 * 2017年6月14日 15:19:30 王永杰
	 */
	$("#is_invoice li").click(function(){
		$("#is_invoice li").children("i").hide();
		$("#is_invoice li").children("a").removeClass("selected");
		$(this).children("i").show();
		$(this).children("a").addClass("selected");
		switch($(this).children("a").attr("data-flag")){
		case "need-invoice":
			$("#invoiceinfo").slideDown(300);
			break;
		case "not-need-invoice":
			$("#invoiceinfo").slideUp(300);
			break;
		}
		calculateTotalAmount();
	});
	
	/**
	 * 验证手机号码
	 * 2017年11月23日 10:27:38 王永杰
	 */
	$("#user_telephone").keyup(function(){
		validationTelephone();
	});
	
});

/**
 * 初始化数据，仅在第一次加载时使用
 * 2017年6月22日 15:10:02 王永杰
 */
function init(){
	$(".js-goods-num").text($(".goodinfo").length);//商品数量
	var total_money = 0;//总计
	$("div[data-subtotal]").each(function(){
		//循环小计
		total_money += parseFloat($(this).attr('data-subtotal'));
	})
	
	$(".js-total-money").text(total_money.toFixed(2));//总计
	
	//初始化合计
	var init_total_money = parseFloat($("#hidden_count_money").val());//商品金额
	$("#realprice").attr("data-old-total-money",init_total_money.toFixed(2));//原合计（不包含优惠）
	$("#realprice").attr("data-old-keep-total-money",init_total_money.toFixed(2));//保持原合计
	
	calculateTotalAmount();
}

/**
 * 获取选择的发票内容
 * 2017年6月14日 19:39:56 王永杰
 */
function getInvoiceContent(){
	var content = "";
	if($("#is_invoice li a[data-flag='need-invoice']").hasClass("selected")){
		//如果选择需要发票，则发票抬头必填、发票内容必选
		var temp = new Array();
		$("#invoice_con li a[class*='selected']").each(function(){
			temp.push($(this).text());
		});
		content = $("#invoice-title").val()+"$"+temp.toString()+"$"+$("#taxpayer-identification-number").val();
	}
	return content;
}

/**
 * 验证可用余额输入是否正确
 * 2017年6月15日 17:15:45 王永杰
 * @returns {Boolean}
 */
function validationMemberBalance(){
	if($("#account_balance").val() != undefined){
		if(isNaN($("#account_balance").val())){
			$.msg("余额输入错误");
			$("#account_balance").val("");
			calculateTotalAmount();
			return true;
		}
		var r = /^\d+(\.\d{1,2})?$/;
		var account_balance = $("#account_balance").val() == "" ? 0 : parseFloat($("#account_balance").val());//可用余额
		var max_total = parseFloat($("#realprice").attr("data-old-total-money"));//总计
		if(!r.test(account_balance)){
			$.msg("余额输入错误");
			$("#account_balance").val(account_balance.toString().substr(0,account_balance.toString().length-1));
			return true;
		}
		
		var user_money = $("#account_balance").attr("data-max");// 最大可用余额
		if (account_balance > user_money) {
			$.msg("不能超过可用余额！");
			$("#account_balance").val($("#account_balance").attr("data-max"));
			calculateTotalAmount();
			return true;
		}
		
		//可用余额不能超过订单总计
		if(account_balance>max_total){
			$("#account_balance").val(max_total.toFixed(2));
			calculateTotalAmount();
			return true;
		}
	}
	
	return false;
}

function validationTelephone(){
	var reg = /^(((13[0-9]{1})|(15[0-9]{1}))+\d{8})$/;
	if(!reg.test($("#user_telephone").val())){
		$.msg("手机号格式错误");
		$("#user_telephone").focus();
		return true;
	}
	return false;
}

/**
 * 验证
 * @param is_show
 * @returns {Boolean}
 */
function validationOrder(){

	//验证可用余额
	if(validationMemberBalance()) return false;
	
	if(validationTelephone()) return false;
	
	if($("#is_invoice li a[data-flag='need-invoice']").hasClass("selected")){
		//如果选择需要发票，则发票抬头必填、发票内容必选
		if($("#invoice-title").val().length == 0){
			$.msg("请输入个人或公司发票抬头");
			$("#invoice-title").focus();
			return false;
		}
		
		if($("#taxpayer-identification-number").val().length == 0){
			$.msg("请输入纳税人识别号");
			$("#taxpayer-identification-number").focus();
			return false;
		}
		
		if($("#invoice_con li a[class*='selected']").length == 0){
			$.msg("请选择发票内容");
			return false;
		}
	}

	return true;
}

/**
 * 获取优惠券信息
 * 2017年6月14日 16:13:17 王永杰
 */
function getUseCoupon(){
	var coupon = {
		id : 0,
		money : 0
	};
	if(parseInt($("#coupon").val()) > 0){
		coupon.id = $("#coupon").val();
		coupon.money = parseFloat($("#coupon").find("option[value='"+coupon.id+"']").attr("data-money"));
	}
	return coupon;
}

/**
 * 计算总金额
 * 2017年5月8日 13:55:48
 */
function calculateTotalAmount(){
	var money = parseFloat($("#hidden_count_money").val());// 商品总价
	var total_discount = 0;//总优惠
	var order_invoice_tax_money = 0;//发票税额 显示
	var tax_sum = parseFloat($("#hidden_count_money").val());//计算发票税额计算：（商品总计+运-优惠活动-优惠券）*发票税率
	
	//满减送活动
	if(parseFloat($("#hidden_discount_money").val())>0){
		total_discount+= parseFloat($("#hidden_discount_money").val());
		money-= parseFloat($("#hidden_discount_money").val());
		tax_sum -= parseFloat($("#hidden_discount_money").val());
	}

	// 优惠券
	var user_coupon = getUseCoupon();
	if(user_coupon.money > 0){
		// 使用优惠券
		money -= parseFloat(user_coupon.money);
		tax_sum -= parseFloat(user_coupon.money);
		if(money>0){
			total_discount += parseFloat(user_coupon.money);
		}else{
			//如果应付金额为负数，则计算出剩余的金额
			total_discount += parseFloat(user_coupon.money)+parseFloat(money);
		}
	}
	
	//发票税额
	if($("#is_invoice li a[data-flag='need-invoice']").hasClass("selected")){
		order_invoice_tax_money = tax_sum * (parseFloat($("#hidden_order_invoice_tax").val())/100);
		money += order_invoice_tax_money;
		if(order_invoice_tax_money<0){
			order_invoice_tax_money = 0;
		}
	}
	
	//可用余额
	if($("#account_balance").val() != undefined){
		var account_balance = $("#account_balance").val() == "" ? 0:parseFloat($("#account_balance").val());
		if(account_balance>0){
			money -= account_balance;
		}
	}
	if(money<0){
		if($("#account_balance").val() != undefined){
			var balance = parseFloat($("#account_balance").val()) + parseFloat(money);
			account_balance = 0;//使用余额，显示
			//矫正使用余额（不能超出应付金额）
			if(balance>0){
				$("#account_balance").val(balance.toFixed(2));
			}else{
				$("#account_balance").val("");
			}
		}
		money = 0;
	}
	var old_total_money = parseFloat($("#realprice").attr("data-old-keep-total-money"))+parseFloat(order_invoice_tax_money);
	$("#realprice").attr("data-old-total-money",old_total_money.toFixed(2));//原合计（不包含优惠,但包括税额）
	$("#realprice").text(money.toFixed(2));//合计
	$("#realprice").attr("data-total-money",money.toFixed(2));//合计[实际付款金额]（包含优惠券、运费）
	$("#discount_money").text(total_discount.toFixed(2))//总优惠
	if($("#account_balance").val() != undefined){
		$("#use_balance").text(account_balance.toFixed(2));//使用余额
	}
	$("#invoice_tax_money").text(order_invoice_tax_money.toFixed(2));//税率
	validationMemberBalance();
}