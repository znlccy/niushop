/**
 * 公用组件js
 * 李志伟
 * 2017年1月19日11:58:45
 * 现有功能：加入购物车、搜索
 */
$(function(){
	$('html').append('<div id="search_goods"></div>');
	$('html').append('<div id="detail"></div>');
	$('.custom-search-input').keyup(function(){
		var search = $(this).val();

	})
	$('.custom-search-button').click(function(){
		var search = $('.custom-search-input').val();
		var shop_id = $('#hidden_shop_id').val();
		location.href= __URL(APPMAIN+"/goods/goodssearchlist?sear_name="+search+"&shop_id="+shop_id);
	})
})

document.onkeydown=function(event){ 
	e = event ? event :(window.event ? window.event : null);
	if(e.keyCode==13){
		var search = $('.custom-search-input').val();
		var shop_id = $('#hidden_shop_id').val();
		location.href= __URL(APPMAIN+"/goods/goodssearchlist?sear_name="+search+"&shop_id="+shop_id);
	} 
}

//加入购物车
function CartGoodsInfo(goodid,state){
	var uid=$('#uid').val();
	if(uid == undefined || uid == ''){
		window.location.href=__URL(APPMAIN+"/login/index");
	}else{
		if(state == 1){
			$.ajax({
				type:"post",
				url: __URL(APPMAIN+"/goods/joincartinfo"),
				async:false,
				data:{'goods_id':goodid},
				dataType:'html',
				success:function(data){
					$('#detail').html(data);
					$("#s_buy").slideDown(300);
					$(".motify").css("opacity",0);
					$(".motify").fadeIn();
					$(".motify").fadeOut();
				}
			});
		}else{
			var state_msg_arr = "";//'商品状态 0下架，1正常，10违规（禁售）'
			switch(state){
			case 0:
				state_msg = "该商品已下架";
				break;
			case 10:
				state_msg = "该商品违规（禁售）";
				break;
			}
			showBox(state_msg);
		}
	}
}

//搜索商品
function GoodsSearch(){
	$('.head').css('z-index','0');
	$.ajax({
		type:"post",
		url: __URL(APPMAIN+"/goods/goodssearch"),
		async:false,
		dataType:'html',
		success:function(data){
			$('#search_goods').html(data);
			$('#search_goods').show();
			$('.fixed-focus-on').hide();
		}
	});
}