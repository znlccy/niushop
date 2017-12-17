$().ready(function() {

	try {
		// 头部导航下拉菜单
		//$('.menu-item .menu').hover(function() {
//			$(this).find('.menu-bd').toggle();
//		})

		$('.menu-item .menu').hover(function(){
			$(this).find('.menu-bd').show();
		},function(){
			$(this).find('.menu-bd').hide();
		});
	} catch (e) {
	}

	try {
		// 头部搜索 店铺、宝贝选择切换
		 $('.search-type li').click(function() {
		 $(this).addClass('cur').siblings().removeClass('cur');
		 $('#searchtype').val($(this).attr('num'));
		 });
		$('.search-type').hover(function() {
			$(this).css({
				"height": "auto",
				"overflow": "visible"
			});
		}, function() {
			$(this).css({
				"height": 36,
				"overflow": "hidden"
			});
		});
		var cur_value = $(".NS-SEARCH-BOX-KEYWORD").attr('placeholder');
		$('.search-type li:not(".curr")').click(function() {
			var this_text = $(this).text();
			var this_num = $(this).attr('num');
			var curr_text = $(this).siblings('.curr').text();
			var curr_num = $(this).siblings('.curr').attr('num');
			if( this_num==1 ){
				$(".NS-SEARCH-BOX-KEYWORD").attr('placeholder','');
				
			}else{
				$(".NS-SEARCH-BOX-KEYWORD").attr('placeholder',cur_value);
			}
			
			$(this).text(curr_text).attr('num', curr_num).siblings('.curr').text(this_text).attr('num', this_num);
			$('.searchtype').val(this_num);
			$('.search-type').css({
				"height": 36,
				"overflow": "hidden"
			});
		})
	} catch (e) {
	}

	try {
		// 全部分类鼠标经过展开收缩效果
		$('.category-box-border .home-category').hover((function() {
			$('.expand-menu').css('display', 'inline-block');
		}), (function() {
			$('.expand-menu').css("display", "none");
		}));
	} catch (e) {
	}

	try {
		// 当前位置下拉弹框
		$('.breadcrumb .crumbs-nav').hover(function() {
			$(this).toggleClass('curr');
		})
	} catch (e) {
	}

	try {
		// 左侧分类弹框
		$('.list').each(function(){
			var all_width = [];
			var num = $(this).find('.subitems dl').length;
			for(var i=0 ; i< num ; i++){
				all_width.push(parseInt($(this).find('.subitems dl').eq(i).find('dt').find('em').text().length));
				$(this).find('.subitems dl').eq(i).find('dt').find('a').outerWidth()
			}
			$(this).find('.subitems dl dt').width(Math.max.apply(null,all_width)+'em');
		})

		$('.list').hover(function() {
			if($(this).find(".categorys .subitems dl").length>0){
				$(this).find('.categorys').show();
				$(".category-layer").css({"width" : "961px"});
			}else{
				$(this).find(".cat i").hide();
				$(".category-layer").css({"width" : "210px"});
			}
		}, function() {
			$(this).find('.categorys').hide();
			$(".category-layer").css({"width" : "210px"});
		});
	} catch (e) {
	}

	try {
		// 右侧边栏
		$(window).scroll(function() {
			if ($(this).scrollTop() > $(window).height()) {
				$('.returnTop').show();
			} else {
				$('.returnTop').hide();
			}
		})

		$(".returnTop").click(function() {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});

		// 点击用户图标弹出登录框
		$('.quick-login .quick-links-a,.quick-login .quick-login-a,.customer-service-online a').click(function() {
			$('.pop-login,.pop-mask').show();
		})
		$('.quick-area').mouseover(function() {
			$(this).find('.quick-sidebar').show();
		});
		$('.quick-area').mouseout(function() {
			$(this).find('.quick-sidebar').hide();
		})
		// 移动图标出现文字
		$(".right-sidebar-panel li").mouseenter(function() {
			$(this).children(".popup").stop().animate({
				left: -92,
				queue: true
			});
			$(this).children(".popup").css("visibility", "visible");
			$(this).children(".ibar_login_box").css("display", "block");
		});
		$(".right-sidebar-panel li").mouseleave(function() {
			$(this).children(".popup").css("visibility", "hidden");
			$(this).children(".popup").stop().animate({
				left: -121,
				queue: true
			});
			$(this).children(".ibar_login_box").css("display", "none");
		});
		// 点击购物车、用户信息以及浏览历史事件
		$('.sidebar-tabs').click(function() {
			//$(this).find("div[class='span']").text() 购物车
			var title = "";
			switch($(this).attr("data-ns-flag")){
				case "shopping_cart":
					//点击了购物车
					title = "购物车";
					$(".js-sidebar-title").prev().css("visibility","visible");
					refreshShopCart();
					break;
				case "collections_goods":
					//点击了我收藏的商品
					title = "我收藏的商品";
					$(".js-sidebar-title").prev().css("visibility","hidden");
					$("#refreshMore").text("查看更多收藏商品");
					$("#refreshMore").attr("href",__URL(SHOPMAIN+"/member/goodscollectionlist"));
					refreshShopOrGoodsCollections("goods");
					break;
				case "love_history":
					//我看过的，浏览历史
					title = "我看过的";
					$(".js-sidebar-title").prev().css("visibility","hidden");
					refreshHistory();
					break;
			}
			$(".sidebar-cartbox").find('.cart-panel-content').height($(window).height() - 90);
			$(".sidebar-cartbox").find('.bonus-panel-content').height($(window).height() - 40);
			$(".js-sidebar-title").text(title);
			if ($('.right-sidebar-main').hasClass('right-sidebar-main-open') && $(this).hasClass('current')) {
				$('.right-sidebar-main').removeClass('right-sidebar-main-open');
				$(this).removeClass('current');
			} else {
				$(this).addClass('current').siblings('.sidebar-tabs').removeClass('current');
				$('.right-sidebar-main').addClass('right-sidebar-main-open');
				if( title== "购物车")
				{
					if(parseInt($(".js-cart-count").text())>0 ){
						$(".cart-panel-footer").show();
					}else{
						$(".cart-panel-footer").hide();
					}
				}else{
					$(".cart-panel-footer").show();
				}
			}
		});
		$(".right-sidebar-panels").on('click', '.close-panel', function() {
			$('.sidebar-tabs').removeClass('current');
			$('.right-sidebar-main').removeClass('right-sidebar-main-open');
			$('.right-sidebar-panels').removeClass('animate-out');
		});
		$(document).click(function(e) {
			var target = $(e.target);
			if (target.closest('.right-sidebar-con').length == 0) {
				$('.right-sidebar-main').removeClass('right-sidebar-main-open');
				$('.sidebar-tabs').removeClass('current');
//				$('.right-sidebar-panels').removeClass('animate-in').addClass('animate-out').css('z-index', 1);
				$('.right-sidebar-panels').removeClass('animate-in').css('z-index', 1);
			}
		})
	} catch (e) {
	}
	// 底部二维码切换
	$(".QR-code li").hover(function() {
		var index = $(this).index();
		$(this).addClass("current").siblings().removeClass("current");
		$(".QR-code .code").eq(index).removeClass("hide").siblings().addClass("hide");
	})

	// 在线客服
	/*$(".service-online").click(function() {
		var goods_id = $(this).data("goods_id");
		var shop_id = $(this).data("shop_id");
		var order_id = $(this).data("order_id");
		
		$.openim({goods_id:goods_id,shop_id:shop_id,order_id:order_id});
	})*/
	


});
function serviceOnLine(shop_id)
{
	$.openim({shop_id:shop_id});
}
// 动态、普通登录切换
function setTab(name, cursel, n) {
	for (i = 1; i <= n; i++) {
		var menu = $("#" + name + i);
		var con = $("#con_" + name + "_" + i);

		if (i == cursel) {
			$(con).show();
			$(menu).addClass("active");
		} else {
			$(con).hide();
			$(menu).removeClass("active");
		}
	}
}
