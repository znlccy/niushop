$(function() {
	if ($('#nav li').length > 0) {

		var current = $('#nav li').find(".current").parents("li");

		if ($(current).size() == 0) {
			current = $('#nav li').eq(0);
		}

		// 导航滑动效果
		$('#nav .wrap-line').css({
			left: $(current).position().left,
			width: $(current).outerWidth()
		});

		$('#nav li').hover(function() {
			$('#nav .wrap-line').stop().animate({
				left: $(this).position().left,
				width: $(this).outerWidth()
			});
		}, function() {
			$('#nav .wrap-line').stop().animate({
				left: $(current).position().left,
				width: $(current).outerWidth()
			});
		});
	}
	// 首页Tab标签卡滑门切换
	if ($(".tabs-nav > li > h3")) {
		$(".tabs-nav > li > h3").bind('mouseover', (function(e) {
			if (e.target == this) {
				var tabs = $(this).parent().parent().children("li");
				var panels = $(this).parent().parent().parent().children(".tabs-panel");
				var index = $.inArray(this, $(this).parent().parent().find("h3"));
				if (panels.eq(index)[0]) {
					tabs.removeClass("tabs-selected").eq(index).addClass("tabs-selected");
					var color = $(this).parents(".floor:first").attr("color");
					$(this).parents(".tabs-nav").find("h3").css({
						"border-color": "",
						"color": ""
					});
					$(this).css({
						"border-color": color + " " + color + " #fff",
						"color": color
					});
					panels.addClass("tabs-hide").eq(index).removeClass("tabs-hide");
				}
			}
		}));
	}

	if ($(".floor-tabs-nav > li")) {
		// 首页楼层Tab标签卡滑门切换
		$(".floor-tabs-nav > li").bind('mouseover', (function(e) {
			var color = $(this).parents(".floor").attr("color");
			$(this).addClass('floor-tabs-selected').siblings().removeClass('floor-tabs-selected');
			$(this).find('h3').css({
				'border-color': color + ' ' + color + ' #fff',
				'color': color
			}).parents('li').siblings('li').find('h3').css({
				'border-color': '',
				'color': ''
			});
			$(this).parents('.floor-con').find('.floor-tabs-panel').eq($(this).index()).removeClass('floor-tabs-hide').siblings().addClass('floor-tabs-hide');
		}));
	}
	// 店铺1街logo鼠标经过抖动效果 注意：依赖于 js/jump.js
	if ($(".store-wall1 .store-con img")) {
		$(".store-wall1 .store-con img").each(function(k, img) {
			new JumpObj(img, 10);
		});
	}
	
	//店铺街2logo鼠标经过效果
	$('body').find(".store-wall2-list li").hover(function(){
		$(this).find('.black-cover').css('display', 'block');
		$(this).find('.cover-content').css('display', 'block');
	},
	function(){
		$(this).find('.black-cover').css('display', 'none');
		$(this).find('.cover-content').css('display', 'none');
	});

	if ($(".brand-con")) {
		// 楼层品牌切换效果 注意：依赖于 js/index_tab.js
		$(".brand-con").hover(function() {
			var num = $(this).find("li").length;
			if (num > 10) {
				$(this).find(".brand-btn").fadeTo('fast', 0.4);
			}
		}, function() {
			$(this).find(".brand-btn").fadeTo('fast', 0);
		});
	}
	// 楼层轮播图
	if ($('.NS-FLOOR-FOCUS')) {
		$.each($('.NS-FLOOR-FOCUS'), function(i, val) {
			var sWidth = $(val).width();
			var len = $(val).find("ul li").length; // 获取焦点图个数
			var index = 0;
			var picTimer;
			// 以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
			var btn = "<div class='focus-btn'>";

			for (var i = 0; i < len; i++) {
				btn += "<span></span>";
			}
			btn += "</div>";
			$(val).append(btn);
			$(val).find(".btnBg").css("opacity", 0.5);

			// 为小按钮添加鼠标滑入事件，以显示相应的内容
			$(val).find(".focus-btn span").css("opacity", 0.3).mouseover(function() {
				index = $(val).find(".focus-btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");

			// 本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
			$(val).find("ul").css("width", sWidth * (len));

			// 鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			$(val).hover(function() {
				clearInterval(picTimer);
			}, function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if (index == len) {
						index = 0;
					}
				}, 3000); // 此4000代表自动播放的间隔，单位：毫秒
			}).trigger("mouseleave");

			// 显示图片函数，根据接收的index值显示相应的内容
			function showPics(index) { // 普通切换
				var nowLeft = -index * sWidth; // 根据index值计算ul元素的left值
				$(val).find("ul").stop(true, false).animate({
					"left": nowLeft
				}, 300);
				$(val).find(".focus-btn span").stop(true, false).animate({
					"opacity": "0.3"
				}, 300).eq(index).stop(true, false).animate({
					"opacity": "0.7"
				}, 300); // 为当前的按钮切换到选中的效果
			}
		});
	}

	// 首页banner图轮播
	function banner_play(a, b, c, d) {
		var blength = $(a).length;
		if (blength > 1) {
			$(b).mouseover(function() {
				$(this).addClass(c).siblings().removeClass(c);
				$(a).eq($(this).index()).hide().fadeIn().siblings().fadeOut();

				num = $(this).index();
				clearInterval(bannerTime);
			});
			var num = 0;
			function bannerPlay() {
				num++;
				if (num > blength - 1) {
					num = 0;
				}
				$(b).eq(num).addClass(c).siblings().removeClass(c);
				$(a).eq(num).hide().fadeIn().siblings().fadeOut();
			}
			var bannerTime = setInterval(bannerPlay, 6000);
			$(d).hover(function() {
				clearInterval(bannerTime);
			}, function() {
				bannerTime = setInterval(bannerPlay, 6000);
			})
		}
	}
	banner_play('.full-screen-slides li', '.full-screen-slides-pagination li', 'current', '#fullScreenSlides');// 首页主广告轮播

});

//首页左侧楼层定位
$(function() {
	if ($(".floor-list")) {
		var elevatorfloor = $(".elevator-floor");
		$.each($('.floor-list'), function(i, v) {
			var fnum = $.trim($(v).find('.NS-FLOOR-NAME').text());
			var short_name = $.trim($(v).find('.NS-SHORT-NAME').val());
			if (short_name == '')
				short_name = fnum;
			var $el = $("<a class='smooth' href='javascript:;'><em class='fs-name'>" + short_name + "</em></a>")
			var $i = $("<i class='fs-line'></i>");
			if (i < $('.floor-list').length - 1) {
				$el.append($i);
			}
			elevatorfloor.append($el);
		});


		var conTop = 0;
		var conTopEnd = 0;
		var floor_top_array = new Array();//记录楼层位置
		var floor_height_array = new Array();//记录楼层的高度
		$(".floor-list").each(function(){
			floor_top_array.push($(this).offset().top);
			var floor_height = 0;
			if($(this).height()>400){
				floor_height = parseFloat($(this).height()/2);
			}else if($(this).height()>=266){
				floor_height = parseFloat($(this).height()-30);
			}else{
				floor_height = parseFloat($(this).height());
			}
			floor_height_array.push(floor_height);
		});
//		console.log(floor_top_array);
//		console.log(floor_height_array);
		if ($(".floor-list").length > 0) {
			var smooth_height = $(".elevator-floor .smooth").height() + 30;//30是补差
			conTop = $(".floor-list:eq(0)").offset().top - $(".floor-list:eq(0)").height() - smooth_height - 150;//150是补差
			conTopEnd = $(".floor-list:eq(" + ($(".floor-list").length-1) + ")").offset().top;// - $(".floor-list:eq(" + ($(".floor-list").length-1) + ")").height() - smooth_height - 110;
		}

		$(window).scroll(function() {
			var scrt = $(window).scrollTop();
//			console.log("当前滚动的位置：" + scrt);
//			console.log("楼层位置：" + conTop);
//			console.log("楼层结束位置：" + conTopEnd);
			if (scrt > conTop){

				$(".elevator").show("fast", function() {
					$(".elevator-floor").css({
						"-webkit-transform": "scale(1)",
						"-moz-transform": "scale(1)",
						"transform": "scale(1)",
						"opacity": "1"
					})
				}).css({
					"visibility": "visible"
				});
				
			}
			else {

				$(".elevator-floor").css({
					"-webkit-transform": "scale(1.2)",
					"-moz-transform": "scale(1.2)",
					"transform": "scale(1.2)",
					"opacity": "0"
				});
				$(".elevator").css({
					"visibility": "hidden"
				});
			}

			if (conTopEnd !=0 && scrt >=conTopEnd) {
				$(".elevator-floor").css({
					"-webkit-transform": "scale(1.2)",
					"-moz-transform": "scale(1.2)",
					"transform": "scale(1.2)",
					"opacity": "0"
				});
				$(".elevator").css({
					"visibility": "hidden"
				});
			}
			setTab();
		});
		
		$(".elevator-floor a.smooth").on("click", function() {
			var index = $(".elevator-floor a.smooth").index(this);
//			var smooth_height = $(".elevator-floor .smooth").height() + 30;//30是补差
//			console.log("滚动到：" + (floor_top_array[index] - floor_height_array[index]));
			$("html,body").stop().animate({
				scrollTop: (floor_top_array[index] - floor_height_array[index] - ($(".elevator-floor a").height() * (index + 1))) + "px"
			}, 400);
		});
		
		$(".elevator-floor a.fsbacktotop").click(function() {
			$("html,body").stop().animate({
				scrollTop: 0
			}, 400)
		});

		function setTab() {
			
			var scrollTop = $(window).scrollTop();
			var temp_arr = new Array();
			var smooth_height = $(".elevator-floor .smooth").height() + 30;
			
			for(var i=floor_top_array.length-1;i>=0;i--){
				
				//条件是：当前滚动相当于顶部的偏移值 + 当前循环的楼层高度 + 左侧定位楼层高度  >= 当前循环楼层的顶部偏移值 - 当前循环的楼层高度
				if(scrollTop + floor_height_array[i] + smooth_height >= floor_top_array[i] - floor_height_array[i]){
					$(".elevator-floor a").eq(i).addClass("active").siblings().removeClass("active");
					break;
				}

			}
		}
	}
});
//头部广告
$(".top-active-close").click(function(){
	$(".top-active").slideUp();
	$.cookie('1051_close', 1, { expires: 7, path: '/' });
});

//处理轮播图片
$(function() {
if($('.NS-FLOOR-HISLIDER').length >0)
{
	$.each($('.NS-FLOOR-HISLIDER'),function(i,v){
		$(v).hiSlider();
	});
}	
});

//头部滚动通栏悬浮框
$(document).ready(function(){ 
	var headHeight=820;  //这个高度其实有更好的办法的。使用者根据自己的需要可以手工调整。
	var nav=$(".as-shelter"); 
	var nav2=$(".follow-box");//要悬浮的容器的id
	$(window).scroll(function(){ 
	 
	if($(this).scrollTop()>headHeight){ 
		nav.addClass("show");   //悬浮的样式
		nav2.addClass("show");
	} 
	else{ 
		nav.removeClass("show"); 
		nav2.removeClass("show"); 
		} 
	}) 
})