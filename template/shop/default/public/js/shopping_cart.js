/**
 * 右侧购物
 */
//是否选择过规格
var select_specifications = false;
var cart_id_arr = new Array();//购物车中的商品Id array
var cart_num = new Array(); //商品的数量 array

//是否登录
function isLogin(){
	if($("#hidden_uid").val()==null||$("#hidden_uid").val()==""){
		//去登录吧
		$(".js-tip-box").show();
		var str = '您还没有登录哦<br/>';
		str += '<a class="color ajax-login" href="javascript:showPopLogin();" title="去登录" target="_blank">去登录</a>';
		$(".js-tip-text").html(str);
		return false;
	}
	return true;
}

//刷新购物车
function refreshShopCart(){
	$(".js-cart-list").html("");
	cart_id_arr = new Array();
	cart_num = new Array();
	$.ajax({
		url : __URL(SHOPMAIN+"/goods/getshoppingcart"),
		type : "POST",
		success : function(data){
			var str = "";
			var total = 0;
			if(data.length>0){//没登录会返回首页html代码
				for(var i=0;i<data.length;i++){
					cart_id_arr.push(data[i].goods_id);
					cart_num.push(data[i].num);
					var delete_cart_id = 0;
					str += '<div class="cart-item">';
					str += '<div class="item-goods">';
					str += '<span class="p-img">';
					str += '<a href="'+__URL(SHOPMAIN+'/goods/goodsinfo?goodsid='+data[i].goods_id)+'">';
					if(data[i]["picture_info"] != null){
						str += '<img src="'+__IMG(data[i]["picture_info"]["pic_cover_big"])+'" width="50" height="50" alt="'+data[i].goods_name+'"></a></span>';
					}else{
						str += '<img src="'+TEMP_IMG+'/goods/default_goods_img.png" width="50" height="50" alt="'+data[i].goods_name+'"></a></span>';
					}
					str += '<div class="p-name">';
					str += '<a href="'+__URL(SHOPMAIN+'/goods/goodsinfo.html?goodsid='+data[i].goods_id)+'" title="'+data[i].goods_name+'">'+data[i].goods_name+'&nbsp;'+data[i].sku_name+'</a></div>';
					str += '<div class="p-price">';
					if(data[i].point_exchange_type==1){
						str += '<strong>¥'+data[i].price+'+积分'+data[i].point_exchange+'</strong>x'+data[i].num+'</div>';
					}else{
						str += '<strong>¥'+data[i].price+'</strong>×'+data[i].num+'</div>';
					}
						str += '<a href="javascript:;" class="p-del" onclick="deleteShoppingCartById('+data[i].cart_id+')">删除</a>';
						str += '</div></div>';
					total += data[i].price * data[i].num;
				}
				$(".cart-panel-footer").show();
				$(".js-tip-box").hide();
				$('.js-footer-cart').show();
			}else{
				//再次查询
				$(".cart-panel-footer").hide();
				$(".js-tip-box").show();
				var tip_str = '您的购物车里什么都没有哦<br>';
				tip_str += '<a class="color" href="'+SHOPMAIN+'" title="去逛逛" target="_blank">去逛逛</a>';
				$(".js-tip-text").html(tip_str);
				$('.js-footer-cart').hide();
			}
			if(data.length>100){
				$(".js-cart-count").text("99+");//购物车中的数量
			}else{
				$(".js-cart-count").text(data.length);//购物车中的数量
			}
			$(".js-count").text(data.length);
			$(".js-total").text("￥"+total);
			$(".js-cart-list").html(str);
		}
	});
}

//右侧边栏-->的店铺收藏、商品收藏
function refreshShopOrGoodsCollections(type){
	init();
	//是否登录
	if(isLogin()){
		$.ajax({
			url : __URL(SHOPMAIN+"/member/queryshoporgoodscollections"),
			type : "POST",
			data : {"type" : type},
			success : function(res){
				var str = "";
				if(res.length>0){
					for(var i=0;i<res.length;i++){
						if(type == "shop"){
							str += '<div class="cart-item"><div class="item-goods"><span class="p-img">';
							str += '<a href="'+__URL(SHOPMAIN+'/shop/shopindex?shop_id='+res[i].shop_id)+'">';
							str += '<img src="'+__IMG(res[i].shop_avatar)+'" width="50" height="50" alt="'+res[i].shop_name+'"></a></span>';
							
							str += '<div class="p-name">';
							str += '<a href="'+__URL(SHOPMAIN+'/shop/shopindex?shop_id='+res[i].shop_id)+'">'+res[i].shop_name+'</a></div>';
							
							str += '<div class="p-price"><strong>'+res[i].shop_company_name+'</strong></div>';
							str += '<a href="javascript:cancelCollection('+res[i].shop_id+',&#39;shop&#39;,false);" class="p-del" style="width:62px;">取消收藏</a></div></div></div>';
						}else{
							str += '<div class="cart-item"><div class="item-goods"><span class="p-img">';
							str += '<a href="'+__URL(SHOPMAIN+'/goods/goodsinfo?goodsid='+res[i].goods_id)+'">';
							str += '<img src="'+__IMG(res[i].pic_cover_mid)+'" width="50" height="50" alt="'+res[i].goods_name+'"></a></span>';
							
							str += '<div class="p-name">';
							str += '<a href="'+__URL(SHOPMAIN+'/goods/goodsinfo?goodsid='+res[i].goods_id)+'">'+res[i].goods_name+'</a></div>';
							
							str += '<div class="p-price"><strong>¥'+res[i].price+'</strong></div>';
							str += '<a href="javascript:cancelCollection('+res[i].goods_id+',&#39;goods&#39;,false);" class="p-del" style="width:62px;">取消收藏</a></div></div></div>';
						}
					}
					$("#refreshMore").show();
					$(".js-tip-box").hide();
				}else{
					var str_type = type == "shop"?"店铺":"商品";
					$("#refreshMore").hide();
					$(".js-tip-box").show();
					var tip_str = '这里空空的，赶快去收藏'+str_type+'吧！<br/>';
					tip_str += '<a class="color" href="'+__URL(SHOPMAIN)+'" title="去逛逛" target="_blank">去逛逛</a>';
					$(".js-tip-text").html(tip_str);
				}
				$(".js-cart-list").html(str);
			}
		});
	}
}

function init(){
	$("#refreshMore").hide();
	$('.js-footer-cart').hide();
	$(".js-cart-list").html("");
}

//右侧边栏-->"我看过的"
function refreshHistory(){
	init();
	//是否登录
	if(isLogin()){
		$.ajax({
			url : __URL(SHOPMAIN+"/goods/getmemberhistories"),
			type : "POST",
			success : function(res){
				var str = "";
				if(res.length>0){
					for(var i=0;i<res.length;i++){
						str += '<div class="cart-item"><div class="item-goods"><span class="p-img">';
						str += '<a href="'+__URL(SHOPMAIN+'/goods/goodsinfo?goodsid='+res[i].goods_id)+'">';
						str += '<img src="'+__IMG(res[i]["picture_info"].pic_cover_mid)+'" width="50" height="50" alt="'+res[i].goods_name+'"></a></span>';
						
						str += '<div class="p-name">';
						str += '<a href="'+__URL(SHOPMAIN+'/goods/goodsinfo?goodsid='+res[i].goods_id)+'">'+res[i].goods_name+'</a></div>';
						
						str += '<div class="p-price"><strong>¥'+res[i].price+'</strong></div>';
						str += '<a href="javascript:cancelCollection('+res[i].goods_id+',&#39;goods&#39;,false);" class="p-del" style="width:62px;"></a></div></div></div>';
					}

					$(".js-tip-box").hide();
				}else{
					$(".js-tip-box").show();
					var tip_str = '您还没有浏览历史，去逛逛吧！<br/>';
					tip_str += '<a class="color" href="'+__URL(SHOPMAIN)+'" title="去逛逛" target="_blank">去逛逛</a>';
					$(".js-tip-text").html(tip_str);
				}
				$(".js-cart-list").html(str);
			}
		});
	}
}

function showPopLogin(){
	$('#mask-layer-login').show();
	$('#layui-layer').show();
}

//验证用户选择商品规格信息
function validationInfo(goodsid,flag){
	var goods_sku = $("#goods_sku0");
	var sku = goods_sku.val();
	if(!select_specifications){
		//没有SKU，直接取第一个
		if(sku == ";"){
			$("#hidden_skuid").val(goods_sku.attr("skuid"));
			$("#hidden_skuname").val(goods_sku.attr("skuname"));
			$("#hidden_sku_price").val( goods_sku.attr("price"));
		}else if($("#hidden_skuid").val() == ""){
			$(".js-skulist").css("border","2px solid #0689e1");
			$(".js-skulist .choose-title").show();
			$("html,body").animate({
				scrollTop: $(".js-skulist").offset().top-200 },
				{ duration: 400,easing: "swing" }
			);
			return false;
		}
	}else{
		//成功
		$(".js-skulist").css("border","0px");
		$(".js-skulist .choose-title").hide();
	}

	var num = 0;
	for(var i=0;i<cart_id_arr.length;i++){
		
		if(cart_id_arr[i]==goodsid) num ++;
		
		if(cart_id_arr[i]==goodsid && cart_num[i]>1 && cart_num[i]==$("#hidden_max_buy").val()){
			if(flag == "to_cart") $.msg("该商品每人限购"+cart_num[i]+"件");
			else $.msg("该商品每人限购"+cart_num[i]+"件，购物车中已存在该商品");
			return false;
		}
	}
	//再次检查购物车中的商品，是否有同一件商品，不同的SKU
	if(num>0 && num == $("#hidden_max_buy").val()){
		$.msg("购物车中已存在该商品");
		return false;
	}
	//如果该商品是积分商品，则判断当前用户的积分是否足够
	if($("#hidden_point_exchange_type").val()==1){
		if(parseInt($("#hidden_point_exchange").val())>parseInt($("#hidden_member_point").val())){
			$.msg("亲，您的积分不足");
			return false;
		}
	}
	return true;
}

//删除购物车中的商品  flag:是否刷新当前页面，
function deleteShoppingCartById(id,flag){
	if (confirm("您确实要把该商品移出购物车吗？")){
		$.ajax({
			url : __URL(SHOPMAIN+"/goods/deleteshoppingcartbyid"),
			type : "POST",
			data : {"cart_id_array": id},
			success : function(data){
				if(data["code"]>0){
					$.msg("操作成功");
					refreshShopCart();//刷新购物车
					if(flag) location.reload();
				}
			}
		});
	}
}

//没有选择商品规格时，标红框，右上角的x关闭事件
function closePrompt(obj){
	$(obj).parent().hide();
	$(".js-skulist").css("border","0px");
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