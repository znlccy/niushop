//左侧分类弹框
$('.list').hover(function(){
	if($(this).find(".categorys .subitems dl").length>0){
		$(this).find('.categorys').show();
	}else{
		$(this).find(".cat i").hide();
	}
},function(){
	$(this).find('.categorys').hide(); 
});

/*向上广告轮播*/
function slider_top(){
	var num=0;
	var blength= $('#focus .slider-panel a').length;
	function bannerplay(){
		num++;
		if(num>blength-1){
			num=0;	
		}
		$('#focus .slider-panel').stop().animate({'top':-num*454},500);
		$('#focus .slider-extra a').eq(num).addClass('curr').siblings().removeClass('curr');
	};
	var bannertime = setInterval(bannerplay,6000);
	$('#focus').hover(function(){
		clearInterval(bannertime);	
	},function(){
		bannertime = setInterval(bannerplay,6000);	
	});
	$('#focus .slider-extra a').mouseover(function(){
		var thisindex = $(this).index();
		$(this).addClass('curr').siblings().removeClass('curr');
		$('#focus .slider-panel').stop().animate({'top':-thisindex*454});
		num=$(this).index();
		clearInterval(bannerTime_category);	
	});
}

slider_top();
function ad_slide(){
	var num_ad = 0;
	var adlength = $('.home-ad .ad-list a').length;
	$('.home-ad .ad-list').append($('.home-ad .ad-list').html());
	function adplay(){
		num_ad++;
		if(num_ad > adlength){
			num_ad = 1;	
			$('.home-ad .ad-list').css('top',0);
		}
		$('.home-ad .ad-list').stop().animate({'top':-num_ad*100},500);
	}	
	var adtime = setInterval(adplay,3000);
	$('.home-ad').hover(function(){
		clearInterval(adtime);
	},function(){
		adtime = setInterval(adplay,3000);	
	})
}
ad_slide();
//帮助中心点击效果
$('.article-menu h4').click(function(){
	if($(this).parents('.article-menu-list').find('ul li').length > 0){
		$(this).parents('.article-menu-list').toggleClass('curr').find('ul').slideToggle();
		$(this).parents('.article-menu-list').siblings('.article-menu-list').removeClass('curr').find('ul').slideUp();
	}
})
//文章列表页左侧菜单
$('.sidebar-article-menu li.curr').parents('ul').addClass('curr').parents('.sidebar-article-menu').addClass('curr');
//频道页左侧菜单
function category_index_nav(){
	$('.float-list dl').hover(function(){
		$(this).find('dt').addClass('hover');
		$(this).find('dd').show();	
	},function(){
		$(this).find('dt').removeClass('hover');
		$(this).find('dd').hide();		
	})
}

category_index_nav();
//banner图轮播
function banner_play(a,b,c,d){
	var blength = $(a).length;
	if(blength > 1){
		$(b).mouseover(function(){
			$(this).addClass(c).siblings().removeClass(c);
			$(a).eq($(this).index()).hide().fadeIn().siblings().fadeOut();
			
			num=$(this).index();
			clearInterval(bannerTime);	
		});
		var num=0;
		function bannerPlay(){
			num++;
			if(num>blength-1){
				num=0;	
			}
			$(b).eq(num).addClass(c).siblings().removeClass(c);
			$(a).eq(num).hide().fadeIn().siblings().fadeOut();	
		}
		var bannerTime = setInterval(bannerPlay,6000);
		$(d).hover(function(){
			clearInterval(bannerTime);	
		},function(){
			bannerTime = setInterval(bannerPlay,6000);	
		})
	}
}
banner_play('.full-screen-slides li','.full-screen-slides-pagination li','current','#fullScreenSlides');//首页主广告轮播
banner_play('.category-banner .banner-panel li','.category-banner .banner-extra span','curr','.category-banner');//频道页广告轮播
//频道模板category_index1
function brand_reco(){
	var a = $(".mc ul").children("li");
	var b = $(".brand-reco"); 
	if($(a).length > 0){ 
		b.removeClass('none');
    }else{ 
		b.addClass('none');
	} 		
}
brand_reco();

//登陆页面 鼠标滑过微信图标显示二维码
$('.user-weixin').hover(function(){
	$(this).find('.erweima').toggle();	
})
$(function(){
	//文字弹出层关闭
$('.pop-sure,.sure-btn,.pop-close,.cancel-btn').click(function(){
	$('.cencel-btn').addClass('none');
	$(this).parents('.pop-main').hide();
	$('.pop-mask').hide();
	
});	
})

//列表页对比展示位置
if($('.main').hasClass('main1210')){
	$('#compareBox').css({'left':Math.ceil(($(window).width()-990)/2)})	
}else{
	$('#compareBox').css({'left':225+Math.ceil(($(window).width()-1210)/2)})		
}
//商品列表 收缩展开侧边
$('.category-wrap .slide-aside').click(function(){
	if($('.category-wrap .aside').width() == 0){
		$('.category-wrap .aside').animate({'width':210},500);
		$('.category-wrap .aside-inner').show().animate({'width':210},500);
		$('.category-wrap .main').removeClass('main1210').animate({'padding-left':225},500);
		$('.category-wrap .main').find('.item:nth-child(4n)').addClass('last');
		$('.category-wrap .main').find('.item:nth-child(5n)').removeClass('last');
	}else{
		$('.category-wrap .aside').animate({'width':0},500);
		$('.category-wrap .aside-inner').animate({'width':0},500,function(){
			$(this).hide();	
		});
		$('.category-wrap .main').addClass('main1210').animate({'padding-left':0},500);
		$('.category-wrap .main').find('.item:nth-child(4n)').removeClass('last');
		$('.category-wrap .main').find('.item:nth-child(5n)').addClass('last');
	}
	if($('.main').hasClass('main1210')){
		$('#compareBox').css({'left':Math.ceil(($(window).width()-990)/2)})	
	}else{
		$('#compareBox').css({'left':225+Math.ceil(($(window).width()-1210)/2)})		
	}
})
function tab_change(tab,cur,con){
	$(tab).click(function(){
		$(this).addClass(cur).siblings().removeClass(cur);
		$(con).eq($(this).index()).show().siblings(con).hide();	
	})
};
tab_change('#b-allbrand .tab li','curr','#b-allbrand .tabcon');//品牌列表页 切换分类下的品牌

