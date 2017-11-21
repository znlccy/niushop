/**
 * 商品列表，点击购物车，弹出商品属性 -wyj
 * 2017年3月3日 10:12:27
 */
function getAttribute(goods_id){
	var sku_attribute = new Array();
	$("input[name='goods_sku"+goods_id+"']").each(function(){
		var obj = new Object();
		obj.sku = $(this).val();
		obj.stock = $(this).attr("stock");
		obj.price = $(this).attr("price");
		obj.skuid = $(this).attr("skuid");
		obj.skuname = $(this).attr("skuname");
		sku_attribute.push(obj);
	});
	return sku_attribute;
}

//加入购物车 state ：'商品状态 0下架，1正常
function ShowGoodsAttribute(goods_id,goods_name,pic_id,obj,max_buy,state){
	if(state == 1){
		$("#hidden_goodsid").val(goods_id);
		$("#hidden_goods_name").val(goods_name);
		$("#hidden_default_img_id").val(pic_id);
		$("#hidden_max_buy").val(max_buy);//存储当前选中商品的最大限购数量
		var sku_attribute = getAttribute(goods_id);
		$.ajax({
			url : __URL(SHOPMAIN+"/goods/getgoodsskuinfo"),
			type : "post",
			data : { "goods_id" : goods_id },
			success : function(res){
				var str = "";
				if(res.length>0){
					for(var i=0;i<res.length;i++){
						
						var spec_value_list = res[i]["value"];
						str += '<div class="dt">'+res[i]["spec_name"]+'</div>';
						str += '<div class="dd radio-dd">';
						var index = 0;
						
						for(var j=0;j<spec_value_list.length;j++){
							
							var value = spec_value_list[j]["spec_id"]+':'+spec_value_list[j]["spec_value_id"];
							var picture = parseInt(spec_value_list[j]['picture']);
							if(index==0){
								str += '<span class="attr-radio curr">';
								//存在SKU商品，就用。否则用商品主图
								if(picture != 0) $("#hidden_default_img_id").val(picture);
							}else{
								str += '<span class="attr-radio">';
							}
							index++;
							str += '<label onclick="selectAttr(this,'+i+','+goods_id+',' + picture + ')" name="attribute_'+i+'" value="'+value+'" >';
							str += '<font>'+spec_value_list[j]["spec_value_name"]+'</font></label></span>';
						}
						str += '</div><div class="blank"></div>';
						
					}
					$(".js-sku-list").html(str);
					$('#speDiv').css({'top':($(window).height()-$('#speDiv').outerHeight())/2,"display":"block"});
					$("#mask").show();
					setSelectAttr(goods_id);
					
				}else{
					
					//没有SKU直接取
					$("#hidden_skuname").val(sku_attribute[0].skuname);
					$("#hidden_sku_price").val(sku_attribute[0].price);
					$("#hidden_skuid").val(sku_attribute[0].skuid);
					addToCart();
				}
			}
		});
	}else{
		var state_msg_arr = "";//'商品状态 0下架，1正常
		switch(state){
		case 0:
			state_msg = "该商品已下架";
			break;
		}
		$.msg(state_msg);
	}
}

//弹出框，选择商品属性加入购物车
function addToCart(){
	if(!is_post){
		return false;
	}
	var num = 0;
	if(cart_id_arr != null){
		for(var i=0;i<cart_id_arr.length;i++){
			if(cart_id_arr[i]==parseInt($("#hidden_goodsid").val())){
				num ++;
			}
			if(cart_id_arr[i]==parseInt($("#hidden_goodsid").val()) && cart_num[i]>1 && cart_num[i]==$("#hidden_max_buy").val()){
				$.msg("该商品限购"+cart_num[i]+"件");
				return false;
			}
		}
		//再次检查购物车中的商品，是否有同一件商品，不同的SKU
		if(num>0 && num == $("#hidden_max_buy").val()){
			$.msg("购物车中已存在该商品");
			return false;
		}
		setSelectAttr($("#hidden_goodsid").val());
		var cart_detail = new Object();
		cart_detail.goods_id = $("#hidden_goodsid").val();
		cart_detail.count = 1;//$("#num").val();
		cart_detail.goods_name = $("#hidden_goods_name").val();
		cart_detail.sku_id = $("#hidden_skuid").val();
		cart_detail.sku_name = $("#hidden_skuname").val();
		cart_detail.price = $("#hidden_sku_price").val();
		cart_detail.picture_id = $("#hidden_default_img_id").val();
		cart_detail.cost_price = $("#hidden_sku_price").val();//成本价
		var cart_tag = "addCart";//暂时没用，保留。
		$.ajax({
			url : __URL(SHOPMAIN+"/goods/addcart"),
			type : "post",
			data : { "cart_detail" : JSON.stringify(cart_detail), "cart_tag" : cart_tag },
			success : function(res){
				if(res.code > 0){
					$(".add-cart").removeClass("js-disabled");
					refreshShopCart();//里边会加载购物车中的数量
					refreshShopCartBlue();
				}
				$.msg(res.message);
				closeBuy();
			}
		});
	}
}
	
//关闭sku弹出框
function closeBuy(){
	$("#mask").hide();
	$('#speDiv').hide();
}
var is_post = true;

/**
 * 选择对应的属性进行匹配
 * 修改时间：2017年9月19日 17:49:30 王永杰
 * @param goods_id
 */
function setSelectAttr(goods_id){
	var arr = new Array();
	$("span[class='attr-radio curr'").each(function(){
		arr.push($(this).find("label").attr("value"));
	});
	arr.sort();
	$("input[name='goods_sku"+goods_id+"'").each(function(){
		var curr = $(this).val().split(";");
		var goods_sku_arr = new Array();
		for(var j=0;j<curr.length;j++){
			if(curr[j]!=''){
				goods_sku_arr.push(curr[j]);
			}
		}
		goods_sku_arr.sort();
		if(goods_sku_arr.toString()==arr.toString()){
			$("#hidden_skuid").val($(this).attr("skuid"));
			$("#hidden_skuname").val($(this).attr("skuname"));
			$("#hidden_sku_price").val($(this).attr("price"));
			if($(this).attr("stock") == 0){
				is_post = false;
				$(".spe-btn .sure-btn").css("background-color","#d8d8d8");
				$(".spe-btn .sure-btn").css("border","1px solid #d8d8d8");
			}else{
				is_post = true;
				$(".spe-btn .sure-btn").css("background-color","#0689e1");
				$(".spe-btn .sure-btn").css("border","1px solid #0689e1");
			}
		}
	});
}

/**
 * 选择sku对应的属性。同时判断是否有SKU主图，有就用
 * 修改时间：2017年9月19日 17:48:57
 */
function selectAttr(obj,i,goods_id,picture){
	$("label[name='attribute_"+i+"']").each(function(){
		$(this).parent().removeClass("curr");
	});
	$(obj).parent().addClass("curr");
	
	//如果有SKU主图，用就用。
	if(picture!=0) $("#hidden_default_img_id").val(picture);
	setSelectAttr(goods_id);
}