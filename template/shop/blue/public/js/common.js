$().ready(function() {

	try {

		$('.menu-item .menu').hover(function(){
			//判断当前浮上去的是不是login
			if($(this).attr("data-flag")){
				if($("#hidden_uid").val() != undefined && $("#hidden_uid").val() != "") $(this).find('.menu-bd').show();
			}else{
				$(this).find('.menu-bd').show();
			}
		},function(){
			if($(this).attr("data-flag")){
				if($("#hidden_uid").val() != undefined && $("#hidden_uid").val() != "") $(this).find('.menu-bd').hide();
			}else{
				$(this).find('.menu-bd').hide();
			}
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
				$(".category-layer").css({"width" : "959px"});
				$(this).find('.categorys').show();
			}else{
				$(".category-layer").css({"width" : "210px"});
				$(this).find(".cat i").hide();
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
		// 点击购物车、用户信息以及浏览历史事件
		$('.sidebar-tabs').click(function() {
			switch($(this).attr("data-ns-flag")){
				case "shopping_cart":
					//点击了购物车
					location.href = __URL(SHOPMAIN+"/goods/cart");
					break;
				case "collections_goods":
					//点击了我收藏的商品
					location.href = __URL(SHOPMAIN+"/member/goodscollectionlist");
					break;
			}
		});
	} catch (e) {
	}

});
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