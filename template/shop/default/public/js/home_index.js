function takeCount() {
	    setTimeout("takeCount()", 1000);
	    $(".time-remain").each(function(){
	        var obj = $(this);
	        var tms = obj.attr("count_down");
	        if (tms>0) {
	            tms = parseInt(tms)-1;
                var days = Math.floor(tms / (1 * 60 * 60 * 24));
                var hours = Math.floor(tms / (1 * 60 * 60)) % 24;
                var minutes = Math.floor(tms / (1 * 60)) % 60;
                var seconds = Math.floor(tms / 1) % 60;

                if (days < 0) days = 0;
                if (hours < 0) hours = 0;
                if (minutes < 0) minutes = 0;
                if (seconds < 0) seconds = 0;
                obj.find("[time_id='d']").html(days);
                obj.find("[time_id='h']").html(hours);
                obj.find("[time_id='m']").html(minutes);
                obj.find("[time_id='s']").html(seconds);
                obj.attr("count_down",tms);
	        }
	    });
	}
$(function(){
	setTimeout("takeCount()", 1000);
    //首页Tab标签卡滑门切换
    $(".tabs-nav > li > h3").bind('mouseover', (function(e) {
    	if (e.target == this) {
    		var tabs = $(this).parent().parent().children("li");
    		var panels = $(this).parent().parent().parent().children(".tabs-panel");
    		var index = $.inArray(this, $(this).parent().parent().find("h3"));
    		if (panels.eq(index)[0]) {
    			tabs.removeClass("tabs-selected").eq(index).addClass("tabs-selected");
    			var color = $(this).parents(".floor:first").attr("color");
    			$(this).parents(".tabs-nav").find("h3").css({"border-color": "", "color": ""});
    			$(this).css({"border-color": color + " " + color + " #fff", "color": color});
    			panels.addClass("tabs-hide").eq(index).removeClass("tabs-hide");
    		}
    	}
    }));
	
	//首页楼层Tab标签卡滑门切换
    $(".floor-tabs-nav > li").bind('mouseover', (function(e) {
		var color = $(this).parents(".floor").attr("color");
    	$(this).addClass('floor-tabs-selected').siblings().removeClass('floor-tabs-selected');
		$(this).find('h3').css({'border-color': color + ' ' + color + ' #fff', 'color': color}).parents('li').siblings('li').find('h3').css({'border-color':'','color':''});
		$(this).parents('.floor-con').find('.floor-tabs-panel').eq($(this).index()).removeClass('floor-tabs-hide').siblings().addClass('floor-tabs-hide');
    }));

	$('.jfocus-trigeminy > ul > li > a').jfade({
		start_opacity: "1",
		high_opacity: "1",
		low_opacity: ".5",
		timing: "200"
	});
	$('.fade-img > a').jfade({
		start_opacity: "1",
		high_opacity: "1",
		low_opacity: ".5",
		timing: "500"
	});
	$('.middle-goods-list > ul > li').jfade({
		start_opacity: "0.9",
		high_opacity: "1",
		low_opacity: ".25",
		timing: "500"
	});
	$('.recommend-brand > ul > li').jfade({
		start_opacity: "1",
		high_opacity: "1",
		low_opacity: ".5",
		timing: "500"
	});
	$(".full-screen-slides").fullScreen();
	$(".jfocus-trigeminy").jfocus();
	$(".right-side-focus").jfocus();
	$(".groupbuy").jfocus({time:8000});
	$("#saleDiscount").jfocus({time:8000});
});
/*首页左侧楼层定位*/
$(function() {	
		var conTop = $(".floor-list").offset().top;
		$(window).scroll(function() {
			var scrt = $(window).scrollTop();
			if (scrt > conTop) {
				
				$(".elevator").show("fast", function() {
					$(".elevator-floor").css({
						
						"-webkit-transform": "scale(1)",
						"-moz-transform": "scale(1)",
						"transform": "scale(1)",
						"opacity": "1"
					})
				}).css({
					"visibility": "visible"
				})
			} else {
				$(".elevator-floor").css({
					"-webkit-transform": "scale(1.2)",
					"-moz-transform": "scale(1.2)",
					"transform": "scale(1.2)",
					"opacity": "0"
				});
				$(".elevator").css({
					"visibility": "hidden"
				})
			}
			setTab()
		});
		var arr = [],
			fsOffset = 0;
		for (var i = 1; i < $(".floor").length; i++) {
			arr.push(parseInt($(".floor").eq(i).offset().top) + 30)
		}
		$(".elevator-floor a.smooth").on("click", function() {
			var _th = $(this);
			_th.blur();
			var index = $(".elevator-floor a.smooth").index(this);
			if (index > 0) {
				fsOffset = 50
			}
			var hh = arr[index];
			$("html,body").stop().animate({
				scrollTop: hh - fsOffset + "px"
			}, 400)
		});
		$(".elevator-floor a.fsbacktotop").click(function() {
			$("html,body").stop().animate({
				scrollTop: 0
			}, 400)
		})

	function setTab() {
		var Objs = $(".floor:gt(0)");
		var textSt = $(window).scrollTop();
		
		for (var i = Objs.length - 1; i >= 0; i--) {

			if (textSt >= $(Objs[i]).offset().top - 300) {
				$(".elevator-floor a").eq(i).addClass("active").siblings().removeClass("active");
				return
			}
		}
	}
});
