/**
 * 可视化手机端模板插件[添加组件]
 * 备注：自定义模板尽量只控制样式、不要控制数据的CURD！
 * 创建时间：2017年7月27日 15:17:35	王永杰
 * 更新时间：2017年8月9日 11:10:09	王永杰
 * --------------------------------------------------------------
 * 更新时间：2017年8月16日 10:24:18	王永杰	Line:653
 * 1、每个组件最多可以出现99999次，基本上可以满足实际需求，如果需要更改，修改$limit配置即可
 * 2、添加每个组件时都会添加默认值，供用户参考
 * 3、新增自定义链接功能
 * 4、将原来的自定义模块组件更改为富文本组件
 * 5、重做自定义模块，可以调用其他自定义页面
 * 6、新增富文本组件、优惠券组件
 * 7、商品列表组件新增“购买按钮”编辑、数据结构调整
 * 8、完善代码注释体系，更加详细
 * --------------------------------------------------------------
 * 更新时间：2017年8月17日 20:23:30	王永杰	Line:758
 * 1、优惠券组件可以编辑背景颜色、样式选择功能
 * 2、视频组件
 * 3、新增橱窗组件
 * --------------------------------------------------------------
 * 更新时间：2017年8月18日 17:05:49 王永杰		Line:877
 * 1、完成橱窗组件
 * 2、整理优化代码
 * --------------------------------------------------------------
 * 更新时间：2017年8月19日 17:19:56 王永杰		Line:882
 * 1、商品列表组件新增购买按钮样式调整
 * --------------------------------------------------------------
 * 更新时间：2017年8月22日 19:49:50	王永杰	Line:888
 * 1、解决跳转链接加载问题
 * 2、优惠券布局完成
 * 3、轮播图注释掉了
 * 4、解决富文本加载时，出现多个
 * 5、上传的图片可以删除了
 * 6、底部菜单可以出现多次，依次从下至上排列
 * 7、自定义模块已完成
 * --------------------------------------------------------------
 * 更新时间：2017年8月23日 15:29:47	王永杰	Line:939
 * 1、优化富文本组件
 * 2、整理代码
 * 
 */


/**
 * 各个组件的最多可以出现的次数
 * 创建时间：2017年8月3日 19:48:59
 * 更新时间：2017年8月3日 20:20:32
 * --------------------------------------------------------------
 * 更新时间：2017年8月18日 16:49:38 王永杰
 * 1、新增[视频组件、橱窗组件]
 * 2、各个组件最大出现次数调整为99999次，基本上可以满足实际需求，如果需求量比较大，则修改对应的值即可
 * 
 */
var $limit = {
	GoodsSearchMaxCount : 99999,			//[商品搜索]
	CarouselMaxCount : 99999,				//[轮播图]
	NoticeMaxCount : 99999,					//[公告]
	FooterMaxCount : 99999,					//[底部]
	CouponsMaxCount : 99999,				//[优惠券]
	NavHybridMaxCount : 99999,				//[混合导航]
	GoodsClassifyMaxCount : 99999,			//[商品分类]
	GoodsListMaxCount : 99999,				//[商品列表]
	TitleMaxCount : 99999,					//[标题]
	ImgAdMaxCount : 99999,					//[图片广告]
	NavTextMaxCount : 99999,				//[文本导航]
	RichTextMaxCount : 99999,				//[富文本]
	CustomModuleMaxCount : 99999,			//[自定义模块]
	AuxiliaryLinelMaxCount : 99999,			//[辅助线]
	AuxiliaryBlankMaxCount : 99999,			//[辅助空白]
	VideoMaxCount : 99999,					//[视频]
	ShowCaseMaxCount : 99999,				//[橱窗]
};

$(function() {
	
	/**
	 * 添加组件
	 * 创建时间：2017年7月31日 15:04:28
	 * 更新时间：2017年8月9日 10:53:55
	 */
	$(".plug-in li").click(function() {
		addControl(this);
	});
	
});

/**
 * 添加组件
 * 创建时间：2017年8月8日 10:33:59
 * 更新时间：2017年8月9日 10:50:50
 * --------------------------------------------------------------
 * 2017年8月18日 16:55:06 王永杰
 * 1、新增[视频组件、优惠券组件、橱窗组件、富文本组件]
 * 
 * @param self：当前选择的组件，control_data：数据库中返回的数据
 */
function addControl(self,control_data){

	//获取当前控件
	var control_name = $(self).attr("data-control-name");
	
	//组件类名，多个逗号隔开
	var class_name = $(self).attr("data-class-name");
	
	//组件附加属性（部分组件用到id属性，例如：轮播图）
	var additional_attr = empty($(self).attr("data-additional-attr")) ? "" : $(self).attr("data-additional-attr");
	
	//要添加的DOM对象
	var custom_main = $(".custom-main");
	
	var html = '<div class="' + class_name+ '" ' + ' data-custom-flag="' + control_name + '" ' + additional_attr + '>';
	
	switch(control_name) {
	
		case controlList.GoodsSearch:
			
			//商品搜索组件
			var style = "";
			var placeholder = $Default.searchPlaceholder;
			if(!empty(control_data)){
				style = 'style = "color:' + control_data.text_color + ';';
				style += 'background-color:' + control_data.input_bg_color + ';';
				style += 'font-size:' + control_data.font_size + "px;";
				placeholder = control_data.placeholder;
			}
			html += '<input type="text" placeholder="' + placeholder + '" data-editable="1" ' + style + '>';
			html += '<button class="control-btn-search"></button>';
			
			break;
			
		case controlList.Carousel:
	
			//轮播图组件
			html += '<ol class="carousel-indicators">';
				html += '<li data-target="#carouselControl" data-slide-to="0" class="active"></li>';
				html += '<li data-target="#carouselControl" data-slide-to="1"></li>';
			html += '</ol>';
			
			html += '<div class="carousel-inner">';
				
				html += '<div class="active item">';
					html += '<img draggable="false" src="' + _STATIC + '/custom_template/img/control_img_ad_single_default.png">';
				html += '</div>';
				
				html += '<div class="item">';
					html += '<img draggable="false" src="' + _STATIC + '/custom_template/img/control_img_ad_single_default.png">';
				html += '</div>';
				
			html += '</div>';
			
			html += '<a class="carousel-control left" href="#carouselControl" data-slide="prev">&lsaquo;</a>';
			html += '<a class="carousel-control right" href="#carouselControl" data-slide="next">&rsaquo;</a>';
			
			break;
			
		case controlList.GoodsList:
			
			//商品列表组件：大图样式、小图样式。默认大图样式
			if(!empty(control_data)){
				var goods_list = eval("(" + control_data.goods_list + ")");
				if(goods_list.goods_list_type == 1) html += getGoodsListBigStyleHTML(goods_list);
				else if(goods_list.goods_list_type == 2) html += getGoodsListSmallStyleHTML(goods_list);
			}else{
				html += getGoodsListBigStyleHTML();
			}
			
			break;
			
		case controlList.Title:
			
			//标题组件
			var title = '『标题名』';
			var subtitle = '';
			var style = "";
			if(!empty(control_data)){
				title = control_data.title;
				subtitle = control_data.subtitle
				style = 'style="color:' + control_data.text_color + ';text-align:' + align_array[control_data.text_align-1] + '"';
			}
			html += '<h4 data-editable="1" ' + style + '>' + title + '</h4>';
			html += '<p data-editable="1" ' + style + '>' + subtitle + '</p>';
			
			break;
			
		case controlList.AuxiliaryLine:
			
			//辅助线组件
			if(!empty(control_data)) html += '<hr style="border-top-color: ' + control_data.border_color +';" />';
			else html += '<hr/>';
			
			break;
			
		case controlList.AuxiliaryBlank:
			
			//辅助空白组件
			var line_height = $Default.auxiliaryBlankHeightMin;
			if(!empty(control_data)) line_height = control_data.height;
			html += '<p style="margin:0;color:#999999;text-align:center;font-size:12px;line-height:' + line_height + 'px">『辅助空白』</p>';
			
			break;
			
		case controlList.Notice:
			
			//公告组件
			var style = '';
			if(!empty(control_data)) style = 'style="color:' + control_data.text_color + '"';
			html += '<marquee data-editable="1" ' + style + '>' + $Default.noticeText + '</marquee>';
			
			break;
			
		case controlList.ImgAd:
			
			//图片广告组件[单图广告(1),轮播广告(2)]
			if(!empty(control_data)){
				var img_ad = eval(control_data.img_ad);
				if(img_ad[0]["adv_show_type"] == 1) html += getImgAdvSingleHTML(img_ad);
				else if(img_ad[0]["adv_show_type"] == 2) html += getImgAdvCarouselHTML(img_ad);
			}else{
				html += getImgAdvSingleHTML();
			}
			
			break;
			
		case controlList.NavText:
			
			//文本导航组件
			var nav_text = "『文本导航』";
			var style = "";
			if(!empty(control_data)){
				nav_text = control_data.nav_text;
				style = 'style="color:' +control_data.text_color + ';font-size:' + control_data.font_size + 'px;"';
			}
			html += '<h5>';
				html += '<span data-editable="1" ' + style + '>' + nav_text + '</span>';
				html += '<i class="fa fa-angle-right" data-editable="1" ' + style + '></i>';
			html += '</h5>';
			
			break;
			
		case controlList.NavHybrid:
			
			//图文混合导航组件
			html += '<ul>';
				if(!empty(control_data)){
					var nav_hybrid = eval(control_data.nav_hybrid);
					if(!empty(nav_hybrid)){
						for(var i=0;i<nav_hybrid.length;i++){
							html += "<li>";
								if(!empty(nav_hybrid[i].src)) html += '<img draggable="false" src="' + __IMG(nav_hybrid[i].src) + '">';
								if(!empty(nav_hybrid[i].text)) html += '<label>' + nav_hybrid[i].text + '</label>';
							html += "</li>";
						}
					}
				}else{
					for(var i=0;i<$Default.navHyBridItemCount;i++) html += '<li></li>';
				}
			html += '</ul>';
			
			break;
			
		case controlList.GoodsClassify:
			
			//商品分类组件
			var temp_aside_count = 3;
			var temp_section_count = 4;
			var temp_common_html = '<div>';
			temp_common_html += '<span>此处是商品名称</span>';
			temp_common_html += '<em>￥' + getRandomPrice() + '</em>';
			temp_common_html += '<button class="control-goods-buy-style">';
				temp_common_html += '<img src="' + _STATIC + "/custom_template/img/goods_buy_button_style1.png" + '"/>';
			temp_common_html += '</button>';
			temp_common_html += '</div>';
			html += '<aside>';
				html += '<ul>';
				if(!empty(control_data)){
					goods_classify = eval(control_data.goods_classify);
					for(var i=0;i<goods_classify.length;i++){
						if(i==0) html += '<li class="selected" title="' + goods_classify[i].name +'">' + goods_classify[i].name +'</li>';
						else html += '<li title="' + goods_classify[i].name +'">' + goods_classify[i].name +'</li>';
					}
				}else{
					for(var i=0;i<temp_aside_count;i++){
						if(i==0) html += '<li class="selected">商品分类一</li>';
						else if(i%3==1) html += '<li>商品分类二</li>';
						else if(i%3==2) html += '<li>商品分类N</li>';
					}
				}
				html += '</ul>';
			html += '</aside>';
			
			html += '<section>';
				html += '<ul>';
					for(var i=0;i<temp_section_count;i++){
						html += '<li>';
						if(i==0) html += '<div class="blue-bg">第一个商品</div>';
						else if(i%4==1) html += '<div class="pink-bg">第二个商品</div>';
						else if(i%4==2) html += '<div class="green-bg">第三个商品</div>';
						else if(i%4==3) html += '<div class="orange-bg">第N个商品</div>';
						html += temp_common_html;
						html += '</li>';
					}
				html += '</ul>';
			html += '</section>';
			
			break;
			
		case controlList.Footer:
			
			//底部菜单组件
			html += '<ul>';
				if(!empty(control_data)){
					var footer = eval(control_data.footer);
					if(!empty(footer)){
						for(var i=0;i<footer.length;i++){
							html += "<li data-index=" + i + ">";
							if(!empty(footer[i].img_src)) html += '<img draggable="false" src="' + __IMG(footer[i].img_src) + '">';
							if(!empty(footer[i].menu_name)) html += '<label>' + footer[i].menu_name + '</label>';
							html += "</li>";
						}
					}
				}else{

					var footer_data = [{
						menu_name : "首页",
						color : $Default.textColor,
						color_hover : $Default.textColorHover,
						href : __URL(APPMAIN + '/index/index'),
						img_src : "upload/custom_template/control_footer_home.png",
						img_src_hover : "upload/custom_template/control_footer_home_selected.png"
					},{
						menu_name : "商品分类",
						color : $Default.textColor,
						color_hover : $Default.textColorHover,
						href : __URL(APPMAIN + '/goods/goodsclassificationlist'),
						img_src : "upload/custom_template/control_footer_classify.png",
						img_src_hover : "upload/custom_template/control_footer_classify_selected.png"
					},{
						menu_name : "购物车",
						color : $Default.textColor,
						color_hover : $Default.textColorHover,
						href : __URL(APPMAIN + '/goods/cart'),
						img_src : "upload/custom_template/control_footer_cart.png",
						img_src_hover : "upload/custom_template/control_footer_cart_selected.png"
					},{

						menu_name : "会员中心",
						color : $Default.textColor,
						color_hover : $Default.textColorHover,
						href : __URL(APPMAIN + '/member/index'),
						img_src : "upload/custom_template/control_footer_user.png",
						img_src_hover : "upload/custom_template/control_footer_user_selected.png"
					}
					];
					for(var i=0;i<$Default.footerItemCount;i++){
						var curr = footer_data[i];
						html += '<li data-index=' + i + '>';
							html += '<img draggable="false" src="' + __IMG(curr.img_src) + '">';
							html += '<label data-editable="1">' + curr.menu_name + '</label>';
						html += '</li>';
					}
				}
			html += '</ul>';
			
			break;
			
		case controlList.Coupons:

			//优惠券组件
			var style = "style1";
//			var bg_color = "";
			if(!empty(control_data)){
				var coupons = eval("(" + control_data.coupons + ")");
				style = "style" + coupons.style;
//				bg_color = coupons.bg_color;
			}
			//style="background-color:' + bg_color + ';"
			html += '<ul>';
				html += '<li><div class="' + style + '"></div></li>';
				html += '<li><div class="' + style + '"></div></li>';
			html += '</ul>';
			
			break;
			
		case controlList.RichText:
			
			//富文本组件
			var content = '<p>『富文本』</p>';
			if(!empty(control_data) && !empty(control_data.rich_text)) content = control_data.rich_text;
			html += '<article style="overflow:hidden;">' + content + '</article>';
			
			break;
			
		case controlList.CustomModule:
			
			//自定义模块组件
			var content = '『自定义模块』';
			
			if(!empty(control_data)){
				
				var custom_module = eval("(" + control_data.custom_module + ")");
				content = custom_module.module_name;
			}
			
			html += '<article><p>' + content + '</p></article>';
			
			break;
		case controlList.Video:
			
			//视频组件
			html += '<video id="my-video" class="video-js vjs-big-play-centered" controls style="width:100%;height:232px;" ';
			html += 'poster="' + _STATIC + '/custom_template/img/video.png">';
				html += '<p class="vjs-no-js">';
					html += 'To view this video please enable JavaScript, and consider upgrading to a web browser that';
				html += '</p>';
			html += '</video>';
			
			break;
			
		case controlList.Audio:
			
			//音频组件[待完善]

			break;
			
		case controlList.ShowCase:
			
			//橱窗组件
			if(!empty(control_data)){
				
				var show_case = eval("(" + control_data.show_case + ")");
				
				if(show_case.layout == 2) html += getShowCaseDefaultHTML(show_case);
				
				else html += getShowCaseMultipleColumnsHTML(show_case);
				
			}else{
				
				html += getShowCaseDefaultHTML();
			}
		
			break;
	}
	
	if(!$(self).hasClass("disabled")){
		
			html += getCommonHTML(control_name);
		html += '</div>';
		
		if(!empty(control_data)){
			
			//从数据库中查询出的数据，用完删除
			$(self).removeAttr("data-additional-attr");
			if(control_name == "Carousel"){
				$(self).attr("data-additional-attr","id='carouselControl'");
			}else if(control_name == "ImgAd"){
				$(self).attr("data-additional-attr","id='carouselImgAd'");
			}
			
		}
		
		custom_main.append(html);
		custom_main.children(".draggable-element:last").attr("data-scroll-top",parseFloat($(".draggable-element:last").height() + $(window).scrollTop()).toFixed(2));
		$('.carousel').carousel({ interval : $Default.carouselInterval });//轮播停留时间
		
		if(control_name == controlList.Carousel){
			
			//重置轮播图的id无法轮播
			var id = custom_main.children(".draggable-element:last").attr("id");
			var new_id = id + custom_main.children("[data-custom-flag='" + controlList.Carousel + "']").length;
			custom_main.children(".draggable-element:last").attr("id",new_id).find("a").attr("href","#" + new_id);

			//数据库
			if(!empty(control_data)){
				carouselTime = control_data.carousel_interval;
				$('.carousel').carousel("updateInterval");
			}
			
		}else if(control_name == controlList.Footer){
			
			var bottom = 76 * $("[data-custom-flag='" + controlList.Footer + "']").length;
			$("[data-custom-flag='Footer']").each(function(i){
				//从第二个开始，每个底部菜单组件靠上排列
				
				if(i>0){
					var prev = $("[data-custom-flag='Footer']").eq(i-1);
					var height = parseFloat(prev.outerHeight()) + parseFloat(prev.css("bottom").replace("px",""));
					$(this).css("bottom",height+"px");
				}
			});
			$(".custom-main").css("padding-bottom", bottom + "px");
			if(empty(control_data)){
				custom_main.children(".draggable-element:last").attr("data-footer",JSON.stringify(footer_data));
			}
			
		}else if(control_name == controlList.Video){
			
			//重置视频组件id，不然无法播放
			var id = custom_main.children(".draggable-element:last").children("video").attr("id");
			var new_id = id + custom_main.children("[data-custom-flag='" + controlList.Video + "']").length;
			custom_main.children(".draggable-element:last").children("video").attr("id",new_id);

			if(!empty(control_data)){
				var video_data = eval("(" + control_data.video + ")");
				var myPlayer = videojs(new_id);
				var videoUrl = video_data.url;
				videojs(new_id).ready(function(){
					var myPlayer = this;
			        myPlayer.src(videoUrl);
			        myPlayer.load(videoUrl);
			        myPlayer.play();
			        myPlayer.pause();
				});
			}
		}

		draggableElementClick($(".draggable-element:last"),"select");//打开右侧编辑
		
		//绑定拖拽控件
		$('.draggable-element').arrangeable('',{"border":"3px dashed rgba(255, 0, 0, 0.5)"},function(){
			
			//拖拽时回调函数
			$(".draggable-element").removeAttr("data-is-show").removeClass("selected");
			$(".pt.pt-left").hide();
			
		});
		
		setControlIsDisabled(self,control_name);
	}
}

/**
 * 公共操作HTML（删除）
 * 创建时间：2017年7月31日 15:06:01
 * 更新时间：2017年8月3日 20:18:09
 * @returns html
 */
function getCommonHTML(control_name){
	
	var html = '<div class="control-actions-wrap">';
			html += '<span class="control-delete" data-control-name="' + control_name + '">删除</span>';
		html += '</div>';
	return html;
}

/**
 * 获取随机价格，演示用
 * 创建时间：2017年7月31日 15:48:52
 * @returns int
 */
function getRandomPrice(){
	return (Math.random()*9999).toFixed(2);
}

/**
 * 获取商品列表大图样式
 * 创建时间：2017年7月31日 17:15:50
 * 更新时间：2017年8月9日 10:38:42
 * 更新时间：2017年8月16日 10:09:00 购买按钮可以自定义设置
 * @param control_data 数据库中返回的数据
 * @returns html
 */
function getGoodsListBigStyleHTML(control_data){
	
	var demo = [{ color : "blue-bg", name  : "第一个商品" },{ color : "pink-bg", name  : "第二个商品" },{ color : "green-bg", name  : "第三个商品" },{ color : "orange-bg", name  : "第N个商品" }];
	var html = '<div class="control-goods-list-big">';
		html += '<ul>';
		
		for(var i=0;i<demo.length;i++){
			
			html += '<li>';
				html += '<div class="control-thumbnail ' + demo[i].color + '">' + demo[i].name + '</div>';
				if(!empty(control_data) && control_data.goods_show_goods_name == 0) html += '<h5 class="control-goods-name" style="display:none;">商品名称</h5>';
				else html += '<h5 class="control-goods-name">商品名称</h5>';
				
				if(!empty(control_data) && control_data.goods_show_goods_price == 0 && control_data.goods_show_buy_button == 0){
					html += '<div class="control-goods-price" style="display:none;">';
				}else if(!empty(control_data) && (control_data.goods_show_goods_price == 0 && control_data.goods_show_buy_button == 1)){
					html += '<div class="control-goods-price position">';
				}else{
					html += '<div class="control-goods-price">';
				}
				
				if(!empty(control_data) && control_data.goods_show_goods_price == 0) html += '<em style="display:none;">￥' + getRandomPrice() + '</em>';
				else html += '<em>￥' + getRandomPrice() + '</em>';
				
				var buy_img = _STATIC + "/custom_template/img/goods_buy_button_style1.png";
				if(!empty(control_data) && !empty(control_data.goods_buy_button_src)){
					buy_img = __IMG(control_data.goods_buy_button_src);
				}
				
				if(!empty(control_data) && control_data.goods_show_buy_button == 0) html += '<button class="control-goods-buy-style" style="display:none;">';
				else html += '<button class="control-goods-buy-style">';
				
					html += '<img src="' + buy_img + '"/>';
				html += '</button>';
				html += '</div>';
			html += '</li>';
			
		}
		
		html += '</ul>';
	html += '</div>';
	return html;
}

/**
 * 获取商品列表小图样式，示例代码
 * 创建时间：2017年7月31日 17:16:12
 * 更新时间：2017年8月9日 10:38:48
 * 更新时间：2017年8月16日 10:08:33 购买按钮可以自定义设置
 * @param control_data 数据库中返回的数据
 * @returns html
 */
function getGoodsListSmallStyleHTML(control_data){
	
	var demo = [{ color : "blue-bg", name  : "第一个商品" },{ color : "pink-bg", name  : "第二个商品" },{ color : "green-bg", name  : "第三个商品" },{ color : "orange-bg", name  : "第N个商品" }];
	var html = '<div class="control-goods-list-small">';
		html += '<ul>';
	
		for(var i=0;i<demo.length;i++){
			
			html += '<li>';
				html += '<div class="control-thumbnail ' + demo[i].color + '">' + demo[i].name + '</div>';
				
				if(!empty(control_data) && control_data.goods_show_goods_name == 0) html += '<h5 class="control-goods-name" style="display:none;">商品名称</h5>';
				else html += '<h5 class="control-goods-name">商品名称</h5>';
				
				if(!empty(control_data) && control_data.goods_show_goods_price == 0 && control_data.goods_show_buy_button == 0){
					html += '<div class="control-goods-price" style="display:none;">';
				}else if(!empty(control_data) && (control_data.goods_show_goods_price == 0 && control_data.goods_show_buy_button == 1)){
					html += '<div class="control-goods-price position">';
				}else{
					html += '<div class="control-goods-price">';
				}

					if(!empty(control_data) && control_data.goods_show_goods_price == 0) html += '<em style="display:none;">￥' + getRandomPrice() + '</em>';
					else html += '<em>￥' + getRandomPrice() + '</em>';
					
					var buy_img = _STATIC + "/custom_template/img/goods_buy_button_style1.png";
					if(!empty(control_data) && !empty(control_data.goods_buy_button_src)){
						buy_img = __IMG(control_data.goods_buy_button_src);
					}
					
					if(!empty(control_data) && control_data.goods_show_buy_button == 0) html += '<button class="control-goods-buy-style" style="display:none;">';
					else html += '<button class="control-goods-buy-style">';
					
						html += '<img src="' + buy_img + '"/>';
					html += '</button>';
				html += '</div>';
				
			html += '</li>';
			
		}
		
		html += '</ul>';
	html += '</div>';
	return html;
}

/**
 * 根据组件的限制条件进行禁用启用
 * 创建时间：2017年8月1日 09:15:27
 * 更新时间：2017年8月3日 20:08:13
 */
function setControlIsDisabled(curr,control){
	$("[data-custom-flag='" + control + "']").length >= eval("$limit." + control + "MaxCount") ? $(curr).addClass("disabled") : $(curr).removeClass("disabled");
}

/**
 * 获取图片广告显示方式：单图广告
 * 创建时间：2017年8月3日 20:24:57
 * 更新时间：2017年8月9日 10:51:35
 * @param img_ad 从数据库中返回的数据
 * @returns html
 */
function getImgAdvSingleHTML(img_ad){
	var html = '<img draggable="false" src="' + _STATIC + '/custom_template/img/control_img_ad_single_default.png">';
	if(!empty(img_ad)) html = '<img draggable="false" src="' + __IMG(img_ad[0].src) + '">';
	return html;
}

/**
 * 获取图片广告显示方式：多图广告
 * 创建时间：2017年8月3日 20:25:05
 * 更新时间：2017年8月9日 10:52:01
 * @param img_ad 数据库中返回的数据
 * @returns html
 */
function getImgAdvCarouselHTML(img_ad){
	
	var html = '<div class="carousel-inner">';
	if(!empty(img_ad)){
		
		for(var i=0;i<img_ad.length;i++){
		
			if(i==0) html += '<div class="active item">';
			else html += '<div class="item">';
			
			if(!empty(img_ad[i].src)) html += '<img draggable="false" src="' + __IMG(img_ad[i].src) + '">';
			else html += '<img draggable="false" src="' + _STATIC + '/custom_template/img/control_img_ad_carousel_default.png">';
			html += '</div>';
		}
	}else{
		
		for(var i=0;i<4;i++){
			
			if(i==0) html += '<div class="active item">';
			else html += '<div class="item">';
					html += '<img draggable="false" src="' + _STATIC + '/custom_template/img/control_img_ad_carousel_default.png">';
			html += '</div>';
		}
	}
	html += '</div>';
	html += '<a class="carousel-control left" href="#carouselImgAd" data-slide="prev">&lsaquo;</a>';
	html += '<a class="carousel-control right" href="#carouselImgAd" data-slide="next">&rsaquo;</a>';
	
	return html;
}

/**
 * 更新当前图片导航组件代码
 * 创建时间：2017年8月3日 09:48:52
 * 更新时间：2017年8月9日 10:52:39
 * @param index 下标
 * @param type 类型：[文本,图片]
 * @param value 值
 */
function updateNavHybridHTML(index,type,value){
	var html = "";
	switch(type){
	case "label":
		
		if(!getCustom().find("ul li:eq(" + index + ") label").length){
			//没有文字的情况下添加<label>
			var li = getCustom().find("ul li:eq(" + index + ")");
			html = '<label>' + value + '</label>';
			li.append(html);
		}else{
			//有文字的情况下修改
			var li = getCustom().find("ul li:eq(" + index + ")");
			li.find("label").text(value);
		}
		
		break;
		
	case "img":
		
		if(!getCustom().find("ul li:eq(" + index + ") img").length){
			
			//没有图片的情况下添加<label>
			var li = getCustom().find("ul li:eq(" + index + ")");
			html = '<img draggable="false" src="' + value + '">';
			
			if(li.find("label").length) li.prepend(html);
			else li.append(html);
			
		}else{
			
			//有图片的情况下修改
			var li = getCustom().find("ul li:eq(" + index + ")");
			li.find("img").attr("src",value);
			
		}
		
		break;
	}
}

/**
 * 更新底部菜单组件代码
 * 创建时间：2017年8月3日 09:48:52
 * 更新时间：2017年8月9日 10:53:39
 * @param index 下标
 * @param type 类型：[文本,图片]
 * @param value 值
 */
function updateFooterHTML(index,type,value){
	var html = "";
	switch(type){
	case "label":
		
		if(!getCustom().find("ul li:eq(" + index + ") label").length){
			
			//没有文字的情况下添加<label>
			var li = getCustom().find("ul li:eq(" + index + ")");
			html = '<label data-editable="1">' + value + '</label>';
			li.append(html);
			
		}else{
			
			//有文字的情况下修改
			var li = getCustom().find("ul li:eq(" + index + ")");
			li.find("label").text(value);
			
		}
		
		break;
		
	case "img":
		
		if(!getCustom().find("ul li:eq(" + index + ") img").length){
			
			//没有图片的情况下添加<label>
			var li = getCustom().find("ul li:eq(" + index + ")");
			if(!empty(value)) html = '<img draggable="false" src="' + value + '">';
			else html = '<img draggable="false" style="display:none;">';
			
			if(li.find("label").length) li.prepend(html);
			else li.append(html);
			
		}else{
			
			//有图片的情况下修改
			var li = getCustom().find("ul li:eq(" + index + ")");
			if(!empty(value)) li.find("img").attr("src",value);
			else li.find("img").hide().removeAttr("src");
			
		}
		
		break;
	}
}

/**
 * 橱窗默认风格代码
 * 创建时间：2017年8月17日 18:08:07 王永杰
 * 1、默认风格，一大（左）两小（右）
 */
function getShowCaseDefaultHTML(show_case){

	var layout_array = [
		{ layout : "big",   bgcolor : "blue-bg" },
		{ layout : "small", bgcolor : "pink-bg" },
		{ layout : "small", bgcolor : "green-bg" }
	];
	var html = '<div class="show-case-default">';
		html += '<ul>';
		for(var i=0;i<layout_array.length;i++){
			var curr = layout_array[i];
			
			if(!empty(show_case)){
				
				var item = show_case.itemList[i];
				var background = "";
				var clear = "";
				
				if(i!=0 && show_case.clearance == 0) clear = "clear";
				
				if(!empty(item.src)) background = 'style="background: url(' + __IMG(item.src) + ') 50% 50% / 100% no-repeat;"';
				
				html += '<li class="' + curr.layout + ' ' + curr.bgcolor + ' ' + clear + '" ' + background + '>';
				
					if(!empty(background)) html += '<div style="visibility:hidden;">橱窗</div>';
					else html += '<div>橱窗</div>';
					
					if(item.show_text == 0) html += '<p style="display:none;">' + item.text + '</p>';
					else html += '<p>' + item.text + '</p>';
				html += '</li>';
				
			}else{

				html += '<li class="' + curr.layout + ' ' + curr.bgcolor + '">';
					html += '<div>橱窗</div>';
					html += '<p>橱窗文字</p>';
				html += '</li>';
			}
		}
			
		html += '</ul>';
	html += '</div>';
	
	return html;
}

/**
 * 橱窗（3列）风格代码
 * 创建时间：2017年8月17日 19:04:45
 */
function getShowCaseMultipleColumnsHTML(show_case){
	
	var count = 3;
	var color = ["blue-bg","pink-bg","green-bg"];
	var html = '<div class="show-case-multiple-columns">';
		html += '<ul>';
		for(var i=0;i<count;i++){
			if(!empty(show_case)){
				
				var item = show_case.itemList[i];
				var background = "";
				if(!empty(item.src)) background = 'style="background: url(' + __IMG(item.src) + ') 50% 50% / 100% no-repeat;"';
				
				if(show_case.clearance == 0) html += '<li class="clear" ' + background + '>';
				else html += '<li ' + background + '>';
				
				if(!empty(item.src)) html += '<div class="' + color[i] + '" style="visibility:hidden;">多列橱窗</div>';
				else html += '<div class="blue-bg">多列橱窗</div>';
				
				if(item.show_text == 0) html += '<p style="display:none;">' + item.text + '</p>';
				else html += '<p>' + item.text + '</p>';
				
			}else{
				
				html += '<li>';
					html += '<div class="' + color[i] + '">多列橱窗</div>';
					html += '<p>橱窗文字</p>';
				html += '</li>';
				
			}
		}
		html += '</ul>';
	html += '</div>';
	return html;
}

/**
 * 预加载一些百度编辑器
 * 创建时间：2017年8月22日 20:17:39
 */
function preloadBaiDuEditor(){
	var html = '<script id="richText" type="text/plain" style="width: 500px; height: 500px;display:none;"></script>';
	html += '<script>var ue = UE.getEditor("richText");';
	
	html += 'ue.ready(function() {';
		html += 'if(empty(getCustom().attr("data-rich-text"))){ ue.setContent("『富文本』");';
		html += ' getCustom().attr("data-rich-text","『富文本』"); }';
	html += '});'
	
	html += 'ue.addListener("mouseover",function(){';
		html += 'getCustom().attr("data-rich-text",ue.getContent()).find("article").html(ue.getContent());';
	html += '});';
	
	html += 'ue.addListener("keyup",function(){';
		html += 'getCustom().attr("data-rich-text",ue.getContent()).find("article").html(ue.getContent());';
	html += '});';
	
	html += '</script>';
	$("body").append(html);
}