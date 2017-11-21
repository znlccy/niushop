/**
 * 购物车相关
 */
(function($) {
	$.extend($.fn, {
		enable : function() {
			if (this[0])
				this[0].disabled = false;
		},
		disable : function() {
			if (this[0])
				this[0].disabled = true;
		}
	});

})(jQuery);

$(function() {
	updateMoney(true);
	$(".num").blur("input propertychange", function() {
		$cart = $(this);
		var num = $cart.val() * 1;// 购买数量
		var default_num = $cart.attr("data-default-num");
		var max_buy = $cart.attr('max_buy') * 1;// 限购数量
		var min_buy = $cart.attr("min_buy") * 1;//最少购买数量
		var nummax = $cart.attr('max') * 1;// 库存数量
		var cartid = $cart.attr('data-cartid');
		if(isNaN(num) || $cart.val().indexOf(".") != -1){
			showBox("格式错误");
			$cart.val(default_num);
			return;
		}
		if(min_buy != 0 && min_buy>num){
			showBox("该商品最少购买"+min_buy+"件");
			$cart.val(min_buy);
			return;
		}else if (num == 0||num<0) {
			$cart.val(1);
			return;
		}

		if (max_buy != 0 && num > max_buy) {
			// 限购
			$cart.val(max_buy);
			showBox("每个用户限购" + max_buy + "件");
			return;
		}

		if (num > nummax) {
			$cart.val(nummax);
			showBox("已达到最大库存");
			return;
		}
		$.ajax({
			url : __URL(APPMAIN + "/goods/cartadjustnum/"),
			type : "post",
			data : {
				"cartid" : cartid,
				"num" : num
			},
			success : function(res) {
				showBox(res.message);
				$cart.val(num);
			}
		});
	});

	// 选择按钮触发事件
	$(".checkbox").click(function() {
		var is_hide = false;
		var shop_id = 0;
		$("[id^=cart_edit]").each(function() {
			if ($(this).is(":hidden")) {
				is_hide = true;// //只要发现有一个编辑按钮隐藏，则是删除操作，就不能走结算操作
				shop_id = $(this).attr("data-shopid");
			}
		})
		
		if (is_hide) {
			if ($("#cart_edit"+ $(this).parent().attr("data-shopid")).is(":hidden")) {
			// 删除操作
				if ($(this).attr("is_del") == 'no') {
					$(this).css("background-image","url(" + cart2 + ")");
					$(this).attr("is_del", "yes");
					$(".btn.btn_buy").css("background","#F15353");
					var count = 0;
					var select_count = 0;
					$(".checkbox").each(function() {
						if ($(this).parent().attr("data-shopid") == shop_id) {
							count++;
						}
						if ($(this).parent().attr("data-shopid") == shop_id&& $(this).attr("is_del") == 'yes') {
							select_count++;
						}
					});
					if (count == select_count) {
						$("#select_all").attr("src", cart2);
						$("#select_all").attr("is_del", "yes");
						$(".btn.btn_buy").css("background","#F15353");
					}
				} else {
					$(this).css("background-image","url(" + cart1 + ")");
					$(this).attr("is_del", "no");
					var is_dis = 'no';// 是否选中
					var count = 0;
					var select_count = 0;
					$(".checkbox").each(function() {
						if ($(this).parent().attr("data-shopid") == shop_id) {
							count++;
						}
						if ($(this).parent().attr("data-shopid") == shop_id&& $(this).attr("is_del") == 'yes') {
							is_dis = 'yes';
							select_count++;
						}
					});
					if (is_dis == 'no') {
						$(".btn.btn_buy").css("background","#CCCCCC");
					} else {
						$(".btn.btn_buy").css("background","#F15353");
					}
					if(count == select_count){
						$("#select_all").attr("src", cart2);
						$("#select_all").attr("is_del", "yes");
					}else{
						$("#select_all").attr("src", cart1);
						$("#select_all").attr("is_del", "no");
					}
				}
			} else {
				showBox("请先完成之前的操作");
			}
		} else {
			// 结算操作
			if ($(this).attr("is_check") == 'no') {
				$(this).css("background-image","url(" + cart2 + ")");
				$(this).attr("is_check", "yes");
			} else {
				$(this).css("background-image","url(" + cart1 + ")");
				$(this).attr("is_check", "no");
			}
			var check_count = 0;//总数量
			var select_check_count = 0;//所选数量
			$(".checkbox").each(function(){
				check_count++;
				if($(this).attr("is_check") == "yes"){
					is_check = true;
					select_check_count++;
				}
			})
			if(check_count == select_check_count){
				$("#select_all").attr("src", cart2);
				$("#select_all").attr("is_check", "yes");
				$("#select_all").attr("is_del", "no");
			}else{
				$("#select_all").attr("src", cart1);
				$("#select_all").attr("is_check", "no");
				$("#select_all").attr("is_del", "no");
			}
			updateMoney(true);
		}
	});

	// 点击全选触发事件
	$("#div_selected").click(function() {
		var flag = false;
		var arr = new Array();
		$("[id^=cart_edit]").each(function() {
			if ($(this).is(":hidden")) {
				flag = true;//只要发现有一个编辑按钮隐藏，则是删除操作，就不能走结算操作
				arr.push($(this).attr("data-shopid"));
			}
		})
		var select_all = $("#select_all");
		var is_check = select_all.attr("is_check");
		var is_del = select_all.attr("is_del");
		var sel_text = $("#sel_text");//全选文本
		
		if (flag) {
			// 删除
			if (is_del == 'no') {
				select_all.attr("src", cart2);
				select_all.attr("is_del", "yes");
				sel_text.css("color", "#333");
				$(".checkbox").each(function() {
					var temp = false;
					for (var i = 0; i < arr.length; i++) {
						if (arr[i] == $(this).parent().attr("data-shopid")) {
							temp = true;// 只选中编辑的商品
							break;
						}
					}
					if (temp) {
						$(this).attr("is_del", "yes");
						$(this).css("background-image", cart2);
					}
				});
				$(".btn.btn_buy").css("background", "#F15353");
			} else {
				select_all.attr("src", cart1);
				select_all.attr("is_del", "no");
				sel_text.css("color", "#CCCCCC");
				$(".checkbox").each(function() {
					$(this).attr("is_del", "no");
					$(this).css("background-image", "url(" + cart1 + ")");
				});
				$(".btn.btn_buy").css("background", "#CCCCCC");
			}
		} else {
			// 结算
			var temp_src = "";//存放图片路径
			var temp_is_check_value = "";
			if (is_check == 'no') {
				temp_is_check_value = "yes";
				temp_src = cart2;
				sel_text.css("color", "#333");
			} else {
				temp_is_check_value = "no";
				temp_src = cart1;
				sel_text.css("color", "#CCCCCC");
			}
			select_all.attr("src", temp_src);
			select_all.attr("is_check", temp_is_check_value);
			$(".checkbox").each(function() {
				$(this).attr("is_check", temp_is_check_value);
				$(this).css("background-image", "url(" + temp_src + ")");
			});
			updateMoney(true);
		}
		
	});

})


/**
 * 获取去重后的数组
 */
function getHeavyArray(arr){
	var hash = {},
	len = arr.length,
	result = [];
	for (var i = 0; i < len; i++){
		if (!hash[arr[i]]){
			hash[arr[i]] = true;
			result.push(arr[i]);
		} 
	}
	return result;
}


// 点击结算或者删除触发事件
function settlement() {
	var count = 0;
	//是否处于编辑状态
	$("[id^=cart_edit]").each(function() {
		if ($(this).is(":hidden")) {
			count++;
		}
	})
	if (count == 0 && sum_num()>0) {
		// 结算
		var money = $("#orderprice").text() * 1;
//		if (money != 0) {
			var i = 0;
			var cart_id_arr = new Array();
			var shop_id = 0;
			var shop_arr = new Array();
			$(".cart-list-li").each(function() {
				if ($(this).find(".checkbox").attr("is_check") == 'yes') {
					var data_shopid = $(this).find("input[name='quantity']").attr("data-shopid");
					if(shop_id == 0){
						shop_id = data_shopid;
					}
					shop_arr.push(data_shopid);
					var temp = $(this).find("input[name='quantity']").attr("data-cartid");
					cart_id_arr.push(temp);
				}
			});
			if(getHeavyArray(shop_arr).length>1){
				showBox("目前只支持单店铺生成订单");
			}else{
				$.ajax({
					url : __URL(APPMAIN + "/order/ordercreatesession"),
					type : "post",
					data : { "tag" : "cart", "cart_id" : cart_id_arr.toString()},
					success : function(res){
						window.location.href = __URL(APPMAIN+"/order/paymentorder");
					}
				});
			}
//		}
	} else {
		// 删除
		var del_id_array = '';
		var flag = false;
		$(".cart-list-li").each(function() {
			var is_check = $(this).find(".checkbox").attr("is_del");
			// 计算每家店铺中购物车的商品数量
			if (is_check == 'yes') {
				var shopid = $(this).attr("data-shopid");
				$(this).find(".checkbox").attr("is_check", "no");
				var del_id = $(this).find("input[name='quantity']").attr("data-cartid");
				del_id_array += del_id + ',';
				$(this).remove();
				if ($(".cart-prolist-ul li[data-shopid='" + shopid+ "']").length == 0) {
					// alert("我这家店的商品都没了，还不快删除我"+shopid);
					$(".cart-prolist-ul li[data-parent-shopid='"+ shopid + "']").remove();
					flag = true;
				}
			}
		});
		if (flag) {
			updateMoney(true);
		}
		$(".btn.btn_buy").css("background", "#CCCCCC");
		$("#select_all").attr("src", cart1);
		$("#select_all").attr("is_del", "no");
		$("#select_all").attr("is_check", "no");
		if (del_id_array != "") {
			del_id_array = del_id_array.substring(0, del_id_array.length - 1);
			del_goods(del_id_array);
		} else {
			showBox("请选择要删除的商品");
		}
	}
}
// 删除按钮
function del_goods(del_id) {
	$.ajax({
		url : __URL(APPMAIN + "/goods/cartdelete/"),
		type : "post",
		asysc : false,
		data : {
			"del_id" : del_id
		},
		success : function(res) {
			showBox(res.message);
			count = $("#countlist").val();
			$("#countlist").val(parseInt(count) - 1);
			if (parseInt($("#countlist").val()) == 0) {
				$(".cart-prolist").hide();
				$("#cart-none").show();
				$(".fixed.bottom").hide();
			}
		}
	});
}
// 点击编辑触发事件
function cart_edit(obj, shop_id) {
	var count = 0;
	$("[id^=cart_edit]").each(function() {
		if ($(this).is(":hidden")) {
			count++;
		}
	})
	if (count > 0) {
		showBox("请先完成之前的操作");
	} else {
		$(obj).hide();
		$(obj).next().show();
		$("span[name='succ_num" + shop_id + "']").hide();
		$("div[name='edit_num" + shop_id + "']").show();
		$(".checkbox").css("background-image", "url(" + cart1 + ")");
		$("#select_all").attr("src", cart1);
		$("#select_all").attr("is_check", "no");
		$("#select_all").attr("is_del", "no");
		$(".btn.btn_buy").css("background", "#CCCCCC");
		$("#sel_text").css("color", "#CCCCCC");
		$("#settlement").text("删除");
		//初始化
		$(".checkbox").each(function() {
			$(this).attr("is_del", "no");
			$(this).attr("is_check", "no");
		})
		updateMoney(false);
	}
}

// 点击完成触发事件
function cart_succ(obj, shop_id) {
	$(obj).hide();
	$(obj).prev().show();
	$(".btn.btn_buy").css("background", "#F15353");
	$(".cart-prolist-ul").find("input[name='quantity']").each(function() {
		var value = $(this).val();
		$(this).parent().parent().parent().find("span[name='succ_amount']").text(value);// 重新计算数量
	});
	$("span[name='succ_num" + shop_id + "']").show();
	$("div[name='edit_num" + shop_id + "']").hide();
	$("#select_all").attr("src", cart2);
	$("#select_all").attr("is_check", "yes");
	$("#select_all").attr("is_del", "no");
	$(".checkbox").each(function() {
		$(this).attr("is_del", "no");
		$(this).attr("is_check", "yes");
		$(this).css("background-image", "url(" + cart2 + ")");
	})
	updateMoney(true);
}

// 更新价格,flag：true，编辑操作，显示价格信息，false：删除操作，隐藏价格信息
function updateMoney(flag) {
	var vis = flag ? "visible" : "hidden";
	$("#price_info").css("visibility", vis);
	var count = 0;
	$("[id^=cart_edit]").each(function() {
		if ($(this).is(":hidden")) {
			count++;// 没有选择编辑
		}
	})
	if (flag && count == 0) {
		var money = sum_money();//金额
		var num_count = sum_num();//数量
		var num = "结算(" + num_count + ")";
		var integral = get_integral();//积分
		$("#orderprice").text(money);
		$("#settlement").text(num);
		//$("#orderintegral").text("+"+integral+"积分");
		if (num_count > 0) {
			$(".btn.btn_buy").css("background", "#F15353");
		} else {
			$(".btn.btn_buy").css("background", "#CCCCCC");
		}
	}
}

//计算积分
function get_integral(){
	var integral = 0;
	$(".cart-list-li").each(function() {
		var is_check = $(this).find(".checkbox").attr("is_check");
		if (is_check == 'yes') {
			var temp = $(this).find("span[name='goods_integral']").attr("data-point");
			if(temp != undefined &&temp　!= ""){
				integral += parseInt(temp);
			}
		}
	});
	return integral;
}


// 计算合计金额
function sum_money() {
	var summoney = 0;
	$(".cart-list-li").each(function() {
		var is_check = $(this).find(".checkbox").attr("is_check");
		if (is_check == 'yes') {
			var amount = $(this).find("span[name='succ_amount']").text() * 1;
			var price = $(this).find("span[name='goods_price']").text() * 1;
			summoney = summoney + amount * price;
		}
	});
	return summoney.toFixed(2);
}
// 计算合计数量
function sum_num() {
	var sumnum = 0;
	$(".cart-list-li").each(function() {
		var is_check = $(this).find(".checkbox").attr("is_check");
		if (is_check == 'yes') {
			var amount = $(this).find("span[name='succ_amount']").text() * 1;
			sumnum = sumnum + amount;
		}
	});
	return sumnum;
}

//检测商品限购，是否允许购买
function getGoodsPurchaseRestrictionForCurrentUser(goods_id,num,callBack){
	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN+"/goods/getGoodsPurchaseRestrictionForCurrentUser"),
		async : false,
		data : { "goods_id" : goods_id, "num" : num },
		success : function(res){
			if(callBack) callBack(res);
		}
	});
}

var delSku = null;
var reAddSku = null;

// difine the cart class
var Cart = {
	// add the product to the cart
	AddProduct : function(productId, trueProductId, obj, type, num, isRestore) {
		var urlStr, count;
		var fittings;
		var fitproducts = "0";
		fittings = $("input[name^='fitting_']");
		for (var fit = 0; fit < fittings.length; fit++) {
			if (fittings[fit].checked) {
				fitproducts += "," + fittings[fit].value;
			}
		}
		if (num && num != "0")
			count = num;
		else
			count = "1";

	},
	ShowShoppingCart : function() {

	},
	changeBar : function(type, skuId, obj,goods_id) {

		var txtC = null;
		var change = 0;
		var default_num = 0;
		if (type == '+') {
			txtC = $(obj).prev();
			default_num = $(obj).prev().attr("data-default-num");
			change = 1;
		}
		if (type == '-') {
			txtC = $(obj).next();
			default_num = $(obj).next().attr("data-default-num");
			change = -1;
		}
		var num = parseInt(txtC.val());
		if (num + change < 0) {
			art.dialog({
				time : 3000,
				lock : true,
				title : '提示消息',
				content : '您输入的数字已经超出的最小值！'
			});
			return;
		}
		var nummax = txtC.attr('max') * 1;
		var max_buy = txtC.attr('max_buy') * 1;
		var min_buy = txtC.attr("min_buy") *1;
		num = num + change;
		if(min_buy != 0 && min_buy>num){
			num = min_buy;
			showBox("该商品最少购买"+min_buy+"件");
			return;
		}
		else if (num == 0) {
			num = 1;
			showBox("最小数量为1");
			return;
		}

		if (max_buy != 0 && num > max_buy) {
			showBox("该商品每人限购" + max_buy + "件");
			return;
		}

		if (num > nummax) {
			num = nummax;
			showBox("已达到最大库存");
			return;
		}

		var is_allow = true;
		
		if (type == '+') {
			getGoodsPurchaseRestrictionForCurrentUser(goods_id,num,function(call){

				if(call.code == 0){
					is_allow = false;
					num = default_num;
					showBox(call.message);
				}
			});
		}

		txtC.val(num);
		if(is_allow) this.changeProductCount(skuId,txtC[0], change);
	},
	changeProductCount : function(cartid,tmpObj, change) {
		var obj = $(tmpObj);
		$.ajax({
			url : __URL(APPMAIN + "/goods/cartadjustnum/"),
			type : "post",
			data : {
				"cartid" : cartid,
				"num" : obj.val()
			},
			success : function(res) {
				if(res.code == 0){
					showBox(res.message);
				}
			}
		});
	}

}