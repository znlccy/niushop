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
 * @date : 2017年11月25日 10:18:41
 * @version : v1.0.0.0
 * 手机端待付款虚拟订单
 * 
 */
$(function() {
	
	//初始化数据
	init();

	/**
	 * 弹出框，该方法只负责界面展示，不负责计算
	 * 选择优惠券 use-coupon
	 * 选择发票信息 invoice
	 * 选择发票内容 invoice-content
	 * 
	 * 2017年6月21日 14:16:57 王永杰
	 */
	$(".item-options[data-flag]").click(function() {
		var curr_options = $(this);//当前点击的项
		var flag = curr_options.attr('data-flag');
		if(flag != undefined){
			$(".mask-layer").fadeIn(300);
			$(".mask-layer-control[data-flag='"+flag+"']").slideDown(300);
			if(getCurrMaskLayer() != null){
				getCurrMaskLayer().find("li").click(function(){
					var curr_li = $(this);
					getCurrMaskLayer().find("li").removeClass("active");
					curr_li.addClass("active");
					var msg = curr_li.children("div:last").text();//内容
					switch(flag){
					
						case "use-coupon":
							//当前打开的是优惠券
							updateUseCoupon(curr_li,curr_options);
							msg = "不使用优惠券";
							var money = 0;
							if(curr_li.attr("data-id") != undefined && curr_li.attr("data-money") != undefined){
								msg = "￥"+parseFloat(curr_li.attr("data-money")).toFixed(2);
							}
							break;
							
						case "invoice":
							//当前打开的是发票信息
							updateInvoice(curr_li);
							break;
							
						case "invoice-content":
							//当前打开的是选择发票内容
							break;
					}

					curr_options.children("span").text(msg);
					getCurrMaskLayer().slideUp(300);
					$(".mask-layer").fadeOut(300);
					calculateTotalAmount();
					
				});
			}
		}
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
	 * 关闭弹出框（包括点击遮罩层、确定按钮、右上角X按钮）
	 * 2017年6月21日 14:18:15 王永杰
	 */
	$(".mask-layer,.btn-green,.mask-layer-control .close").click(function() {
		getCurrMaskLayer().slideUp(300);
		$(".mask-layer").fadeOut(300);
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
 * 2017年6月22日 14:59:33 王永杰
 */
function init(){
	
	//商品数量
	$(".js-goods-num").text($("div[data-subtotal]").length);
	
	//商品总计
	var total_money = 0;
	$("div[data-subtotal]").each(function(){
		//循环小计
		total_money += parseFloat($(this).attr('data-subtotal'));
	})
	//商品总计
	$(".js-total-money").text(total_money.toFixed(2));
	
	//初始化合计
	var init_total_money = parseFloat($("#hidden_count_money").val());//商品金额
	$("#realprice").attr("data-old-total-money",init_total_money.toFixed(2));//原合计（不包含优惠）
	$("#realprice").attr("data-old-keep-total-money",init_total_money.toFixed(2));//保持原合计

	calculateTotalAmount();
}

/**
 * 获取当前打开的弹出框对象
 * 2017年6月21日 14:19:20 王永杰
 */
function getCurrMaskLayer(){
	return $(".mask-layer-control:visible");
}

/**
 * 更新优惠券数据
 * 使用优惠券的同时，要更新余额的最大输入限制
 * 创建时间：2017年6月21日 16:55:15 王永杰
 * 更新时间：2017年6月22日 15:05:03 王永杰
 * @param curr_li 当前选择的优惠券
 * @param curr_options 当前的优惠券项
 */
function updateUseCoupon(curr_li,curr_options){
	var id = 0;
	var money = 0;
	if(curr_li.attr("data-id") != undefined && curr_li.attr("data-money") != undefined){
		id = curr_li.attr("data-id");
		money = curr_li.attr("data-money");
	}
	curr_options.attr("data-id",id);
	curr_options.attr("data-money",money);
}

/**
 * 获取优惠券
 * 2017年6月21日 17:20:27 王永杰
 */
function getUseCoupon(){
	var coupon = { id : 0, money : 0 };
	var obj = $(".item-options[data-flag='use-coupon']");
	if(obj.attr("data-id") != undefined && obj.attr("data-money") != undefined){
		coupon.id= obj.attr("data-id");
		coupon.money = parseFloat(obj.attr("data-money"));
	}
	return coupon;
}

/**
 * 更新发票
 * 2017年6月21日 18:12:19 王永杰
 * 
 * @param curr_li
 */
function updateInvoice(curr_li){
	var invoice = $(".item-options[data-flag='invoice']");//发票选项
	var invoice_content = $(".item-options[data-flag='invoice-content']");//发票内容选项
	//弹出框
	var invoice_content_mask = $(".mask-layer-control[data-flag='invoice-content']");//发票内容弹出框
	var msg = curr_li.children("div:last").text();//内容
	var text = "选择发票内容";
	switch(msg){
		case "不需要发票":
			invoice.attr("data-select",0);
			$(".order .invoice").slideUp(300);
			break;
		case "需要发票":
			invoice.attr("data-select",1);
			$(".order .invoice").slideDown(300);
			text = invoice_content_mask.find("li.active").children("div:last").text();
			break;
	}
	invoice_content.children("span").text(text);
}


/**
 * 获取选择的发票内容，返回拼装好的格式
 * 2017年6月14日 19:39:56 王永杰
 */
function getInvoiceContent(){
	var content = "";
	if(parseInt($(".item-options[data-flag='invoice']").attr("data-select")) == 1){
		//如果选择需要发票，则发票抬头必填、发票内容必选
		content = $("#invoice-title").val()+"$"+$(".item-options[data-flag='invoice-content']").children("span").text()+"$"+$("#taxpayer-identification-number").val();
	}
	return content;
}

/**
 * 验证可用余额输入是否正确，并矫正数据
 * 2017年6月22日 12:13:14 星期四
 * @returns {Boolean}
 */
function validationMemberBalance(){
	if($("#account_balance").val() != undefined){
		if(isNaN($("#account_balance").val())){
			showBox("余额输入错误");
			$("#account_balance").val("");
			calculateTotalAmount();
			return true;
		}
		var r = /^\d+(\.\d{1,2})?$/;
		var account_balance = $("#account_balance").val() == "" ? 0 : parseFloat($("#account_balance").val());//可用余额
		var max_total = parseFloat($("#realprice").attr("data-old-total-money")).toFixed(2);//总计
		if(!r.test(account_balance)){
			showBox("余额输入错误");
			$("#account_balance").val(account_balance.toString().substr(0,account_balance.toString().length-1));
			return true;
		}
		
		var user_money = $("#account_balance").attr("data-max");// 最大可用余额
		if (account_balance > user_money) {
			showBox("不能超过可用余额！");
			$("#account_balance").val($("#account_balance").attr("data-max"));
			calculateTotalAmount();
			return true;
		}
		
		//可用余额不能超过订单总计
		if(account_balance>max_total){
			$("#account_balance").val(max_total);
			calculateTotalAmount();
			return true;
		}
		
	}
	
	return false;
}

function validationTelephone(){
	var reg = /^1[34578]\d{9}$/;
	if($("#user_telephone").val().length == 0){
		showBox("请填写手机号");
		$("#user_telephone").focus();
		return true;
	}
	if(!reg.test($("#user_telephone").val())){
		showBox("手机号格式错误");
		$("#user_telephone").focus();
		return true;
	}
	return false;
}


/**
 * 验证订单数据
 * 2017年6月22日 15:08:10 王永杰
 * 
 * @returns true:验证成功，false：验证失败
 */
function validationOrder(){
	if(validationMemberBalance()) return false;

	if(validationTelephone()) return false;
	
	if(parseInt($(".item-options[data-flag='invoice']").attr("data-select")) == 1){
		//如果选择需要发票，则发票抬头必填、发票内容必选
		if($("#invoice-title").val().length == 0){
			showBox("请输入个人或公司发票抬头");
			$("#invoice-title").focus();
			return false;
		}
		
		if($(".item-options[data-flag='invoice-content']").children("span").text().length == 0){
			showBox("请选择发票内容");
			return false;
		}
	}
	return true;
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
	var account_balance = 0;//可用余额
	var old_total_money = parseFloat($("#realprice").attr("data-old-keep-total-money"));//原合计

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
			total_discount += parseFloat(user_coupon.money) + parseFloat(money);
		}
	}

	//发票税额
	if(parseInt($(".item-options[data-flag='invoice']").attr("data-select")) == 1){
		order_invoice_tax_money = tax_sum * (parseFloat($("#hidden_order_invoice_tax").val())/100);
		money += order_invoice_tax_money;
		if(order_invoice_tax_money<0){
			order_invoice_tax_money = 0;
		}
	}
	//可用余额
	if($("#account_balance").val() != undefined){
		account_balance = $("#account_balance").val() == "" ? 0 : parseFloat($("#account_balance").val());
		if(account_balance>0){
			money -= account_balance;
		}
	}
	
	//应付金额
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
	old_total_money += parseFloat(order_invoice_tax_money);
	$("#realprice").attr("data-old-total-money",old_total_money.toFixed(2));//原合计（包括税额,不包含优惠）
	$("#realprice").text(money.toFixed(2));//合计
	$("#realprice").attr("data-total-money",money.toFixed(2));//合计[实际付款金额]（包含优惠券、运费）
	$("#discount_money").text(total_discount.toFixed(2))//总优惠
	if($("#account_balance").val() != undefined){
		$("#use_balance").text(account_balance.toFixed(2));//使用余额，显示
	}
	$("#invoice_tax_money").text(order_invoice_tax_money.toFixed(2));//税率
	validationMemberBalance();
}

/**
 * 提交订单
 * 2017年6月22日 15:09:08 王永杰
 */
var flag = false;//防止重复提交
function submitOrder() {
	if(validationOrder()){
		if(flag){
			return;
		}
		flag = true;
		var goods_sku_list = $("#goods_sku_list").val();// 商品Skulist
		var leavemessage = $("#leavemessage").val();// 买家留言
		var use_coupon = getUseCoupon();//优惠券id
		var account_balance = 0;//可用余额
		if($("#account_balance").val() != undefined){
			account_balance = $("#account_balance").val() == "" ? 0 : $("#account_balance").val();
		}
		var integral = $("#hidden_count_point_exchange").val() == "" ? 0 : $("#hidden_count_point_exchange").val();//积分
		var pay_type = 0;//支付方式 0：在线支付，4：货到付款
		var buyer_invoice = getInvoiceContent();//发票
		$.ajax({
			url : __URL(APPMAIN + "/order/virtualOrderCreate"),
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
					//如果实际付款金额为0，跳转到个人中心的订单界面中
					if(parseFloat($("#realprice").attr("data-total-money")) == 0){
						location.href = __URL(APPMAIN + '/pay/paycallback?msg=1&out_trade_no=' + res.code);
					}else{
						location.href = __URL(APPMAIN + '/pay/getpayvalue?out_trade_no=' + res.code);
					}
				} else {
					showBox(res.message);
					flag = false;
				}
			}
		});
	}
}