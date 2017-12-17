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
 * @date : 2017年6月14日 12:01:47
 * @version : v1.0.0.0
 * PC端待付款订单
 * 更新时间：2017年6月22日 14:19:56
 */
$(function(){
	
	//初始化
	init();
	
	/**
	 * 选择物流公司
	 * 2017年6月28日 09:57:40 王永杰
	 */
	$("#express_company").change(function(){
		$("#hidden_express").val($(this).children("option:selected").attr("data-express-fee"));
		calculateTotalAmount();
	});
	
	
	/**
	 * 提交订单
	 */
	var flag = false;//防止重复提交
	$(".btn-jiesuan").click(function(){
		if(validationOrder()){
			if(flag){
				return;
			}
			flag = true;
			var goods_sku_list = $("#goods_sku_list").val();// 商品Skulist
			var leavemessage = $("#leavemessage").val();// 订单留言
			var use_coupon = getUseCoupon();//优惠券id
			var account_balance = 0;//可用余额
			if($("#account_balance").val() != undefined){
				account_balance = $("#account_balance").val() == "" ? 0 : $("#account_balance").val();
			}
			var integral = $("#count_point_exchange").val() == ""? 0 : $("#count_point_exchange").val();//积分
			var pay_type = parseInt($("#paylist li a[class='selected']").attr("data-select"));//支付方式 0：在线支付，4：货到付款
			var buyer_invoice = getInvoiceContent();//发票
			$.ajax({
				url : __URL(SHOPMAIN + "/order/ordercreate"),
				type : "post",
				data : {
					'goods_sku_list' : goods_sku_list,
					'leavemessage' : leavemessage,
					'use_coupon' : use_coupon.id,
					'integral' : integral,
					'account_balance' : account_balance,
					'pay_type' : pay_type,
					'buyer_invoice' : buyer_invoice,
					'pick_up_id' : getPickupId(),
					'express_company_id' : $("#express_company").val()
				},
				success : function(res) {
					if (res.code > 0) {
						$(".btn-jiesuan").css("background-color","#ccc");
						//如果实际付款金额为0，跳转到个人中心的订单界面中
						if(parseFloat($("#realprice").attr("data-total-money")) == 0){
							location.href = __URL(APPMAIN + '/pay/paycallback?msg=1&out_trade_no=' + res.code);
						}else if(pay_type == 4){
							location.href = __URL(SHOPMAIN + '/member/orderlist');
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
	 * 选择配送方式
	 * 2017年6月20日 10:53:18 王永杰
	 */
	$("#shipping_method .js-select li").click(function(){
		if($(this).attr("data-code") == "no_config"){
			return;
		}
		$("#shipping_method .js-select li i").hide();
		$("#shipping_method .js-select li a").removeClass("selected");
		$(this).children("i").show();
		$(this).children("a").addClass("selected");
		switch($(this).attr("data-code")){
			case "merchant_distribution":
				//商家配送
				$(".js-pickup-point-list").hide();//隐藏自提列表
				//如果后台开启了选择物流公司，则显示
				if(parseInt($("#hidden_is_logistice").val()) === 1) $("#select_express_company").slideDown(300);
				else $("#select_express_company").slideUp(300);//物流公司显示
			break;
			case "afhalen":
				//门店自提
				$(".js-pickup-point-list").show();//显示自提列表
				$("#select_express_company").hide();//隐藏物流公司
			break;
		}
		calculateTotalAmount();
	})
	
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
	 * 选择支付方式
	 * 2017年6月14日 18:19:56 王永杰
	 */
	$("#paylist li").click(function(){
		$("#paylist li").children("i").hide();
		$("#paylist li").children("a").removeClass("selected");
		$("#shipping_method .js-select li i").hide();
		$("#shipping_method .js-select li a").removeClass("selected");
		$(this).children("i").show();
		$(this).children("a").addClass("selected");
		var merchant_distribution = $("#shipping_method .js-select li[data-code='merchant_distribution']");//商家配送
		var afhalen = $("#shipping_method .js-select li[data-code='afhalen']");//门店自提
		merchant_distribution.children("i").show();
		merchant_distribution.children("a").addClass("selected");
		merchant_distribution.show();
		//如果后台开启了选择物流公司，则显示
		if(parseInt($("#hidden_is_logistice").val()) === 1) $("#select_express_company").slideDown(300);
		else $("#select_express_company").slideUp(300);//物流公司隐藏
		switch(parseInt($(this).children("a").attr("data-select"))){
		case 0:
			//在线支付
			if(!merchant_distribution.children("a").hasClass("selected")){
				//没有商家配送的话，选中门店自提
				afhalen.children("i").show();
				afhalen.children("a").addClass("selected");
				$(".js-pickup-point-list").show();//显示自提列表
			}
			afhalen.show();//显示配送方式，门店自提
			$(this).children("i").show();
			$(this).children("a").addClass("selected");
			break;
		case 4:
			//货到付款
			afhalen.hide();//隐藏配送方式，门店自提
			$(".js-pickup-point-list").hide();
			break;
		}
		calculateTotalAmount();
		
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
	})
	
	/**
	 * 设置支付密码
	 * 2017年6月14日 15:26:43 王永杰
	 */
	$(".js-sett-pay-pwd").click(function(){
		$("#edit-paypwd").show();
	});
	
	/**
	 * 关闭设置支付密码界面
	 * 2017年6月14日 15:29:02 王永杰
	 */
	$("#edit-paypwd i").click(function(){
		$("#edit-paypwd").hide();
	})
	
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
	 * 返回购物车修改（需要将待付款订单的商品保存到购物车中，跳转到购物车）
	 * 根据
	 * 2017年6月22日 19:43:10 王永杰
	 */
	$(".js-goback-cart").click(function(){
		var order_tag = $("#hidden_order_tag").val();//标识：立即购买还是购物车中进来的
		if(order_tag == "buy_now"){
			//立即购买进入的待付款订单，只会有一种商品
			var goods_sku_list_arr = $("#goods_sku_list").val().split(":");//0：skuid，1：数量
			var cart_detail = new Object();
			cart_detail.goods_id = $(".goodinfo").attr("data-goods-id");
			cart_detail.goods_name = $(".goodinfo").attr("data-goods-name");
			cart_detail.count = goods_sku_list_arr[1];
			cart_detail.sku_id = goods_sku_list_arr[0];
			cart_detail.sku_name = $(".goodinfo").attr("data-sku-name");
			cart_detail.price = $(".goodinfo").attr("data-price");
			cart_detail.picture_id = $(".goodinfo").attr("data-img-id");
			cart_detail.cost_price = $(".goodinfo").attr("data-price");//成本价
			$.ajax({
				url : __URL(SHOPMAIN+"/goods/addcart"),
				type : "post",
				data : { "cart_detail" : JSON.stringify(cart_detail) },
				success : function(res){
					location.href = __URL(SHOPMAIN + "/goods/cart");
				}
			});
		}else{
			location.href = __URL(SHOPMAIN + "/goods/cart");
		}
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
	
	/**
	 * 选中第一个配送方式对应的更新数据
	 * 2017年6月28日 17:33:19
	 */
	if($("#shipping_method .js-select li").length){
		$("#shipping_method .js-select li").each(function(i){
			if(i==0){
				switch($(this).attr("data-code")){
				case 'merchant_distribution':
					//商家配送
					//如果后台开启了选择物流公司，则显示
					if(parseInt($("#hidden_is_logistice").val()) === 1) $("#select_express_company").slideDown(300);
					else $("#select_express_company").slideUp(300);//物流公司显示
					break;
				case 'afhalen':
					
					$(".js-pickup-point-list").slideDown(300);//自提列表显示
					
					break;
				}
				$(this).children("i").show();
				$(this).children("a").addClass("selected");
			}
			return false;
		});
	}else{
		$("#shipping_method .js-select").html('<p class="label fl">商家未配置配送方式</p>');
	}
	
	//初始化合计
	var init_total_money = parseFloat($("#hidden_count_money").val());//商品金额
	//有运费
	if($("#hidden_full_mail_is_open").val()==1){
		//满额包邮开启
		if(init_total_money>=parseFloat($("#hidden_full_mail_money").val())){
			$("#hidden_express").val(0);
		}
	}
	
	$("#express").text(parseFloat($("#hidden_express").val()).toFixed(2));//设置运费
	init_total_money += parseFloat($("#hidden_express").val());//运费
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

/**
 * 验证
 * @param is_show
 * @returns {Boolean}
 */
function validationOrder(){

	//验证可用余额
	if(validationMemberBalance()){
		return false;
	}
	
	if ($("#address_id").val() == undefined ||$("#address_id").val() == '' || $("#address_id").val() == 0) {
		$.msg("请先选择收货地址");
		return false;
	}
	
	if($("#paylist li a[class='selected']").length == 0){
		$.msg("请选择支付方式");
		return false;
	}
	
	if($("#shipping_method li[data-code='no_config']").attr("data-code")){
		$.msg("商家未配置配送方式");
		return false;
	}
	
	var is_selected_shipping = 0;
	$("#shipping_method .js-select li a").each(function(){
		if($(this).hasClass("selected")){
			is_selected_shipping ++;
		}
	});
	
	//防止手动取消配送方式
	if(!is_selected_shipping){
		$.msg("商家未配置配送方式");
		return false;
	}

	var merchant_distribution = $("#shipping_method .js-select li[data-code='merchant_distribution']");//商家配送
	if(merchant_distribution.children("a").hasClass("selected")){
		if(parseInt($("#express_company").val()) == -1){
			$.msg("商家未设置物流公司");
			return false;
		}else if(parseInt($("#express_company").val()) == -2){
			$.msg("商家未配置物流公司运费模板");
			return false;
		}else if(parseInt($("#express_company").val()) == 0){
			$.msg("未知错误");
			return false;
		}
	}
	
	
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
	
	//选择门店自提
	if($("#shipping_method li[data-code='afhalen'] a").hasClass("selected")){
		//如果没有自提列表，后台需要编辑
		if($("#pickup_address").val() == undefined){
			$.msg("商家未配置自提点，请选择其他配送方式");
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
 * 获取自提地址id
 * 2017年6月20日 17:13:25 王永杰
 */
function getPickupId(){
	var id = 0;
	if($("#shipping_method li[data-code='afhalen'] a").hasClass("selected")){
		id = parseInt($("#pickup_address").val());
	}
	return id;
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
	// 运费
	//如果选择的是门店自提，则不计算运费
	if(getPickupId()>0){
		money += parseFloat($("#hidden_pick_up_money").val());
		tax_sum += parseFloat($("#hidden_pick_up_money").val());
		$("#express").text(parseFloat($("#hidden_pick_up_money").val()).toFixed(2));
	}else{
		money += parseFloat($("#hidden_express").val());
		tax_sum += parseFloat($("#hidden_express").val());
		$("#express").text(parseFloat($("#hidden_express").val()).toFixed(2));
	}
	//有运费
	var init_total_money = parseFloat($("#hidden_count_money").val());//商品金额
	if($("#hidden_full_mail_is_open").val()==1){
		//满额包邮开启
		if(init_total_money>=parseFloat($("#hidden_full_mail_money").val())){
			$("#hidden_express").val(0);
		}
		$("#express").text(parseFloat($("#hidden_express").val()).toFixed(2));//设置运费
	}
	
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