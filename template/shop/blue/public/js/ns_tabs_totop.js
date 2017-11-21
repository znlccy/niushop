//商品详情右侧商品信息等定位切换效果
var navH = $("#main-nav-holder").offset().top;
$(window).scroll(function() {
	var scroH = $(this).scrollTop(); // 获取滚动条的滑动距离
	if (scroH >= navH) {
		$("#main-nav-holder").addClass('fixed');// 滚动条的滑动距离大于等于定位元素距离浏览器顶部的距离，就固定，反之就不固定
	} else if (scroH < navH) {
		$("#main-nav-holder").removeClass('fixed');
	}
})
// $('.goods-detail .title-list').click(function() {
// 	alert();
// 	$('.right-side-ul li[data-index="'+$(this).attr('data-index')+'"]').addClass("abs-active").siblings().removeClass("abs-active");
// 	$(this).addClass('current').siblings('.title-list').removeClass('current');
// 	$("html,body").scrollTop($('.goods-detail-tabs').eq($(this).index()).offset().top - 50);
// })
$(document).ready(function() {
	var curr_scroll_top = 0;
	window.onscroll = function() {
		curr_scroll_top = $(this).scrollTop();
		/*
		 * 811		700
		 * 889
		 * 17556
		 * 18078
		 */
		$("#main_widget .goods-detail-con").each(function(i){
			var next_top = $("#main_widget .goods-detail-con").eq((i+1)).height();
			//console.log("next:"+next_top);
			var top = $(this).offset().top+$(this).height()-next_top;
			if(top > curr_scroll_top){
				$('.right-side-ul li').eq(i).addClass('abs-active').siblings().removeClass('abs-active');
				//console.log("end:"+$(this).offset().top+"("+(i)+")");
				return false;
			}
		});
		
	}
	$(".right-side-ul li").hover(function() {
		$(this).addClass("abs-hot").siblings().removeClass("abs-hot");
	}, function() {
		$(".right-side-ul li").removeClass("abs-hot");
	});
	$(".right-side-ul li").click(function() {
		$(this).addClass("abs-active").siblings().removeClass("abs-active");
		$('.goods-detail .title-list[data-index="'+$(this).attr('data-index')+'"]').addClass("current").siblings().removeClass("current");
		$('html,body').animate({
			scrollTop: $('.goods-detail-con').eq($(this).index()).offset().top - 50
		}, 300);
	});
});