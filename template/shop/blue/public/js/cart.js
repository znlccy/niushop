/**
 * 购物车wyj 2017年2月15日 16:12:55
 */
$(function() {
	getCopeSum();
});
// 商品全选
function chkAll_onclick() {
	var obj = document.getElementById('chkAll');
	var obj_cartgoods = document.getElementsByName("sel_cartgoods[]");
	for (var i = 0; i < obj_cartgoods.length; i++) {
		if (!obj_cartgoods[i].disabled) {
			var e = obj_cartgoods[i];
			if (e.name != 'chkAll') {
				e.checked = obj.checked;
			}
		}
	}
	select_cart_goods();
}

// 清空购物车
$(".js-clear-cart").click(function() {
	var sel_goods = new Array();// 保存选中要购买的商品
	var obj_cartgoods = document.getElementsByName("sel_cartgoods[]");
	var j = 0;
	for (i = 0; i < obj_cartgoods.length; i++) {
		sel_goods[j] = obj_cartgoods[i].value;
		j++;
	}
	var goods_id_arr = "";
	for (var k = 0; k < sel_goods.length; k++) {
		goods_id_arr += sel_goods[k] + ",";
	}
	goods_id_arr = goods_id_arr.substr(0, goods_id_arr.length - 1);
	deleteShoppingCartById(goods_id_arr, true);//shopping_cart.js
})

// 选中的商品
function select_cart_goods() {
	var sel_goods = new Array();// 保存选中要购买的商品
	var obj_cartgoods = document.getElementsByName("sel_cartgoods[]");
	var j = 0;
	var c = true;// 是否全选要购买的商品：true:全选，false:反之
	for (i = 0; i < obj_cartgoods.length; i++) {
		if (obj_cartgoods[i].checked == true) {
			sel_goods[j] = obj_cartgoods[i].value;
			j++;
		} else {
			c = false;
		}
	}
	document.getElementById('chkAll').checked = c;
	getCopeSum();
}

/**
 * 去结算，2017年2月23日 11:32:42
 */
function selcart_submit() {
	var shop_id = new Array();
	var cart_id_arr = new Array();
	var obj_cartgoods = $("input[name='sel_cartgoods[]']");
	var formobj = document.getElementById('formCart');
	var j = 0;
	$("input[name='sel_cartgoods[]']").each(function() {
		if ($(this).get(0).checked) {
			j++;
			var flag = false;
			for (var i = 0; i < shop_id.length; i++) {
				if ($(this).attr("data-shop-id") == shop_id[i]) {
					flag = true;
					break;
				}
			}
			if (!flag) {
				shop_id.push($(this).attr("data-shop-id"));
			}
			cart_id_arr.push($(this).val());
		}
	});
	if (shop_id.length > 0 && shop_id.length != 1) {
		$.msg("目前只支持单店铺生成订单");
		return;
	}
	// 目前只支持单店铺
	if (j > 0) {
		$.ajax({
			url : __URL(SHOPMAIN + "/member/ordercreatesession"),
			type : "post",
			data : { "tag" : "cart", "cart_id" : cart_id_arr.toString() },
			success : function(res){
				location.href= __URL(SHOPMAIN + "/member/paymentorder");
			}
		});
	}
}

// 更新商品数量
function updateGoodsNumber(cart_id, num,obj) {
	if (null != cart_id && null != num) {
		$.ajax({
			url : __URL(SHOPMAIN + "/goods/updatecartgoodsnumber"),
			type : "post",
			data : { "cart_id" : cart_id, "num" : num },
			success : function(res) {
				if (res > 0) {
					refreshShopCart();// 刷新购物车
					refreshShopCartBlue();
					obj.attr("data-default-num",num);
				}
			}
		});
	} else {
		$.msg("数据错误");
	}
}

// 获取所选中的商品，计算应付总额
function getCopeSum() {
	var sum = 0.00// 应付总额
	var integral = 0;// 积分
	var sel_goods = new Array();
	var obj_cartgoods = document.getElementsByName("sel_cartgoods[]");
	var j = 0;
	for (i = 0; i < obj_cartgoods.length; i++) {
		if (obj_cartgoods[i].checked == true) {
			sel_goods[j] = obj_cartgoods[i].value;
			j++;
		}
	}
	for (j = 0; j < sel_goods.length; j++) {
		sum += Number($("#subtotal_" + sel_goods[j]).attr("data-total"));
		integral += parseInt($("#subtotal_" + sel_goods[j]).attr("data-integral"));
	}
	sum=sum.toFixed(2);
	if (j == 0) {
		$("#cart_money_info").html("您一个商品都没选，这怎么行捏！！真的不行哦！");
		$(".js-settlement").addClass("disabled");

	} else {
		var integral_text = "<span>+" + integral + "积分</span>";
		$("#cart_money_info").html("应付总额：<span>¥" + sum + "</span>" + (integral > 0 ? integral_text : ""));// 总额
		$(".js-settlement").removeClass("disabled");
	}
}

// 添加数量，需要判断当前购物车中的相同商品数量与限购数量，其次再判断商品的限购数量
/**
 * cart_id :购物车Id max_buy ：限购数量 0 不限购 goodsid ： 商品ID obj : 当前对象 stock ：
 * 对应商品的sku库存 wyj,2017年2月16日 10:48:55
 */
function add_num(cart_id, max_buy, goodsid, obj, stock) {
	// 获取到当前商品，然后判断数量
	var count = 0;
	if (goodsid == $(obj).attr("data-goods-id")) {
		count = $("span[data-goods-id='" + goodsid + "']").length;
	}

	var goods_obj = $("#goods_number_" + cart_id);
	var number = parseInt(goods_obj.val());
	var temp_num = 0;// 要修改的数量
	var is_update = true;// 是否更新，true：更新，false：不更新
	var price = $("#subtotal_" + cart_id).attr("data-price");// 商品单价

	if (max_buy == 0) {// 不限购

		if (number < stock) {
			number++;
			temp_num = number;
		} else {
			$.msg("该商品最大库存" + stock + "件");
			temp_num = stock;
			is_update = false;
		}
	} else {
		//限购
		temp_num = ++number;
		//减去已有的商品数量就是要购买的商品数量
		var purchase_restriction = temp_num - goods_obj.attr("data-default-num");
		console.log(goods_obj.attr("data-default-num"));
		getGoodsPurchaseRestrictionForCurrentUser(goodsid,purchase_restriction,function(purchase){
			if(purchase.code == 0){
				temp_num = temp_num-purchase.value;//当前商品数量 - 还能购买的商品数量 = 可购买的商品数量
				$.msg(purchase.message);
				is_update = false;
			}
		});
	}
	goods_obj.val(temp_num);
	if (is_update) {
		var total_price = temp_num * price;
		total_price = total_price.toFixed(2);
		$("#subtotal_" + cart_id).text("￥" + total_price);
		$("#subtotal_" + cart_id).attr("data-total", total_price);
		getCopeSum();// 刷新应付总额
		updateGoodsNumber(cart_id, temp_num,goods_obj);// 更新商品数量
	}
}

// 数量减少
function minus_num(cart_id, max_buy, stock, min_buy) {
	var obj = $("#goods_number_" + cart_id);
	var number = parseInt(obj.val());
	var price = $("#subtotal_" + cart_id).attr("data-price");// 商品单价

	if (number > 1) {
		if(min_buy >= number){
			$.msg("该商品最少购买" + min_buy + "件");
			number = min_buy;
		}else{
			number--;
			obj.val(number);
			var total_price = number * price;
			total_price = total_price.toFixed(2);
			$("#subtotal_" + cart_id).text("￥" + total_price);
			$("#subtotal_" + cart_id).attr("data-total", total_price);
			getCopeSum();// 刷新应付总额
			updateGoodsNumber(cart_id, number,obj);// 更新商品数量
		}
	}
}

// 用户自定义数量
function change_price(cart_id, max_buy, goodsid, obj, stock, min) {
	var r = /^[1-9]+[0-9]*]*$/;
	// 提示弹出框
	var number = $("#goods_number_" + cart_id).val();
	if (!r.test(number)) {
		$.msg("您输入的格式不正确！");// 把数量调整到最初
		$(obj).val($(obj).attr("data-default-num"));
	} else {
		// 获取到当前商品，然后判断数量
		var count = 0;
		var temp_num = 0;// 要改变的数量

		if (goodsid == $(obj).attr("data-goods-id")) {
			count = $("span[data-goods-id='" + goodsid + "']").length;
		}

		var is_update = true;// 是否更新，true：更新，false：不更新
		var price = $("#subtotal_" + cart_id).attr("data-price");// 商品单价

		if (max_buy == 0) {// 不限购
			if (number < stock) {
				// 正常情况
				temp_num = number;
			} else {
				temp_num = stock;// 最大库存
				$.msg("该商品最大库存" + temp_num + "件");
				is_update = false;
			}
		} else {
			// 限购
			temp_num = number;
			
			//减去已有的商品数量就是要购买的商品数量
			var purchase_restriction = temp_num - $(obj).attr("data-default-num");
			getGoodsPurchaseRestrictionForCurrentUser(goodsid,purchase_restriction,function(purchase){
				if(purchase.code == 0){
					temp_num = temp_num-purchase.value;//当前商品数量 - 已购买的商品数量
					$.msg(purchase.message);
					is_update = false;
				}
			});
		}
		
		if(min > temp_num){
			$.msg("该商品最少购买" + min + "件");
			temp_num = min;
			is_update = false;
		}
		
		$(obj).val(temp_num);
		if (is_update) {
			$("#subtotal_" + cart_id).text("￥" + temp_num * price);
			$("#subtotal_" + cart_id).attr("data-total", temp_num * price);
			getCopeSum();// 刷新应付总额
			updateGoodsNumber(cart_id, temp_num,$(obj));// 更新商品数量
		}
	}
}