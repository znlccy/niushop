// JavaScript Document
$(function(){
	
	// 优惠券弹框
	$("body").on('click', '.bonus', function() {
		$(this).siblings('.coupon-popup').toggle();
	})
	
	// 优惠券弹框关闭
	$("body").on('click', '.coupon-popup .close', function() {
		$(this).parent('.coupon-popup').hide();
	})
	
	$(document).on("click",function(e){ 
		var target = $(e.target); 
		if(target.closest(".shop-coupon").length == 0){ 
			$('.coupon-popup').hide();
		} 
	}) 
	
	//会员等级显示下拉
	$('.rank-prices').hover(function(){
		$(".vip1").hide();
		$(".vip2").show();
	},function(){
		$(".vip2").hide(); 
		$(".vip1").show();
	});
	
	//本店活动下拉效果
//	$('.shop-prom-title').click(function(){
//		$(this).hide();
//		$(".shop-prom-Box").show();
//	});
//	
//	$('.shop-prom-Box .hd').click(function(){
//		$('.shop-prom-Box').hide();
//		$(".shop-prom-title").show();
//	});
	
	//去手机购买二维码弹框
	$('.btn-phone').hover(function(){
		$("#phone-tan").toggle();
	});
	
	//套餐tab切换 注意：依赖于 js/tabs.js
	$(".package-view").rTabs({
		bind : 'click',
		animation : 'left'
	});
	
	//店内排行切换 注意：依赖于 js/tabs.js
	$(".rank-list").rTabs();
	
	//商品评论回复点击效果
	$('.goods-comment .reply').click(function(){
		$(this).parents('dd').siblings('dd').find('li.reply-box').toggle();
	});
	
	//商品评论管理员回复点击效果
	$('.business-box').hover(function(){
		$(this).find(".btn-reply").show().click(function(){
			$(this).show().parents('.business-box').siblings('.reply-box').toggle();
		});
	});
	$('.business-box').mouseleave(function(){		
		if ($(this).siblings('.reply-box').css("display") == "none"){
			$(this).find(".btn-reply").hide();
			}
	});
	
	//左侧分类树点击展开收缩效果
	$('.tree li:has(ul)').addClass('parent_li');
    $('.tree li.parent_li > span').on('click', function (e) {
        var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(":visible")) {
            children.hide('fast');
            $(this).find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
        } else {
            children.show('fast');
            $(this).find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
        }
        e.stopPropagation();
    });
	
	//将商品详情左侧内容的高度给右侧定位元素模块
//	$(".right-side-con").height($("#main_widget_1").height());
	
});

//套餐tab切换内容商品上下切换效果   注意：目前存在问题，需动态获取到底存在几个套餐，目前为静态js(请看当前js底部最后两行)
function packageView(a){
	var click_num = Math.ceil($(a+' .tab-content-groups .goods-list li').length/5);
	var num = 0;
	if(click_num == 1){
		$(a+'.next').addClass('disabled');
	}
	if(click_num > 1){
		$(a+' .prev').click(function(){
			num--;
			if(num==0){
				$(this).addClass('disabled').siblings('.next').removeClass('disabled');
			}
			if(num<0){
				num=0;	
			}
			$(a+' .goods-list').animate({
				top:-num*190
			},500);
		})
		$(a+' .next').click(function(){
			num++;
			if(num>(click_num-1)){
				num=1;
			}
			if(num==(click_num-1)){
				$(this).addClass('disabled').siblings('.prev').removeClass('disabled');
			}
			$(a+' .goods-list').animate({
				top:-num*190
			},500);
		})
	}
}
packageView('.tab-content1');
packageView('.tab-content2');


