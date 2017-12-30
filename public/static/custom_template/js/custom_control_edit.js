/**
 * 可视化手机端模板插件[编辑]
 * 创建时间：2017年7月27日 15:17:35	王永杰
 * 更新时间：2017年8月16日 19:02:29	王永杰	Line:1761
 * --------------------------------------------------------------
 * 更新时间：2017年8月17日 20:22:46	王永杰	Line:2085
 * 1、新增视频组件
 * 2、优化优惠券编辑功能
 * 3、新增橱窗组件
 * --------------------------------------------------------------
 * 更新时间：2017年8月17日 20:22:46	王永杰	Line:2399
 * 1、整理代码
 * 2、完善橱窗组件
 * 3、解决视频组件中，输入非法链接地址，报错问题
 * --------------------------------------------------------------
 * 更新时间：2017年8月19日 17:19:38	王永杰	Line:2497
 * 1、商品列表组件新增购买按钮样式调整
 * 2、处理其他小问题
 * --------------------------------------------------------------
 * 更新时间：2017年8月22日 19:49:50	王永杰	Line:2616
 * 1、解决跳转链接加载问题
 * 2、优惠券布局完成
 * 3、轮播图注释掉了
 * 4、解决富文本加载时，出现多个
 * 5、上传的图片可以删除了
 * 6、底部菜单可以出现多次，依次从下至上排列
 * 7、自定义模块已完成
 * --------------------------------------------------------------
 * 更新时间：2017年8月23日 09:39:35	王永杰	Line:2639
 * 1、优化富文本组件
 * 2、整理代码
 */

/**
 * 组件值对象
 */
var $cValue = {
	
	//----------------自定义模板名称----------------
	customTemplateName : {
		name : "模板名称",
		class_name : "text-custom-template-name",
		input_class : "js-custom-template-name",
		value : "",
		default_value : "",
		placeholder : "不能超过30个字符"
	},
	
	//----------------商品搜索----------------
	searchTextColor : {
		name : "文字颜色",
		class_name : "text-color",
		input_class : "js-text-color",
		value : $Default.searchTextColor,
		default_value : $Default.searchTextColor,
		placeholder : $Default.searchTextColor
	},
	searchBgColor : {
		name : "背景颜色", 
		class_name : "text-color", 
		input_class : "js-bg-color",
		value : $Default.searchBgColor, 
		default_value : $Default.searchBgColor,
		placeholder : $Default.searchBgColor
	},
	searchInputBgColor : {
		name : "输入框背景颜色",
		class_name : "text-color",
		input_class : "js-input-bg-color",
		value : $Default.searchInputBgColor,
		default_value : $Default.searchInputBgColor,
		placeholder : $Default.searchInputBgColor
	},
	searchPlaceholder : {
		name : "默认提示内容",
		class_name : "goods-search-placeholder",
		input_class : "js-placeholder",
		value : "",
		default_value : $Default.searchPlaceholder,
		placeholder : $Default.searchPlaceholder
	},
	
	//----------------标题----------------
	title : {
		name : "标题名",
		class_name : "title",
		input_class : "js-title",
		value : "『标题名』",
		default_value : "",
		placeholder : "",
	},
	subTitle : {
		name : "副标题",
		class_name : "subtitle",
		input_class : "js-subtitle",
		value : "",
		default_value : "",
		placeholder : ""
	},
	titleTextColor : {
		name : "文字颜色", 
		class_name : "text-color",
		input_class : "js-text-color",
		value : $Default.titleTextColor,
		default_value : $Default.titleTextColor,
		placeholder : $Default.titleTextColor
	},
	titleBgColor : {
		name : "背景颜色", 
		class_name : "text-color",
		input_class : "js-bg-color",
		value : $Default.titleBgColor,
		default_value : $Default.titleBgColor,
		placeholder :  $Default.titleBgColor
	},
	
	//----------------文字标题----------------
	navText : {
		name : "导航名称",
		class_name : "nav-text",
		input_class : "js-nav-text",
		value : "『文本导航』",
		default_value : "",
		placeholder : "请输入导航名称"
	},
	navTextColor :{
		name : "文字颜色", 
		class_name : "text-color", 
		input_class : "js-text-color",
		value : $Default.navTextColor,
		default_value : $Default.navTextColor,
		placeholder : $Default.navTextColor
	},
	
	//----------------公告样式----------------
	noticeText :{
		name : "公告", 
		class_name : "notice",
		input_class : "js-notice",
		value : "",
		default_value : "",
		placeholder :  ""
	},
	noticeTextColor :{
		name : "文字颜色", 
		class_name : "text-color", 
		input_class : "js-text-color",
		value : $Default.noticeTextColor,
		default_value : $Default.noticeTextColor,
		placeholder :  $Default.noticeTextColor
	},
	noticeBgColor :{
		name : "背景颜色",
		class_name : "text-color",
		input_class : "js-bg-color",
		value : $Default.noticeBgColor,
		default_value : $Default.noticeBgColor,
		placeholder :  $Default.noticeBgColor
	},
	
	//----------------图片导航样式----------------
	navHyBridText : {
		name : "文字",
		class_name : "nav-hybrid-text",
		input_class : "js-nav-hybrid-text",
		value : "",
		default_value : "",
		placeholder : ""
	},
	
	//----------------底部菜单样式样式----------------
	footerTextColor : {
		name : "文字颜色",
		class_name : "text-color",
		input_class : "js-text-color",
		value : $Default.textColor,
		default_value : $Default.textColor,
		placeholder : $Default.textColor
	},
	textColorHover : {
		name : "选中颜色",
		class_name : "text-color",
		input_class : "js-text-color-hover",
		value : $Default.textColorHover,
		default_value : $Default.textColorHover,
		placeholder : $Default.textColorHover
	},
	
	footerMenuName : {
		name : "菜单名称",
		class_name : "footer-menu-name",
		input_class : "js-footer-menu-name",
		value : "",
		default_value : "",
		placeholder : "请输入菜单名称"
	},
	borderColor : {
		name : "边框颜色",
		class_name : "border-color",
		input_class : "js-border-color",
		value : $Default.auxiliaryLineBorderColor,
		default_value : $Default.auxiliaryLineBorderColor,
		placeholder :  $Default.auxiliaryLineBorderColor
	},
	
	//----------------优惠券编辑----------------

	//----------------视频地址编辑----------------
	videoUrl : {
		name : "视频地址",
		class_name : "video-url",
		input_class : "js-video-url",
		value : "",
		default_value : "",
		placeholder :  ""
	}
};

var carouselTime = $Default.carouselInterval;//轮播间隔时间

$(function() {
	
	/**
	 * 自定义模板名称编辑
	 * 创建时间：2017年8月14日 14:03:27
	 */
	$(".js-custom-template-name").live("keyup",function(){
		var value = $(this).val();
		if(value.length>30){
			value = value.substr(0,30);
			$(this).val(value);
			showTip("模板名称不能超过30个字符,超出的将会被截取","warning");
		}
		var h4 = $(".custom-template header>h4");
		h4.text(value).attr('title',value).attr("data-custom-template-name",value);
		var width = h4.width()>170 ? 170 : h4.width();
		h4.css("margin","0 0 0 -" + (width/2) + "px");
	});
	
	/**
	 * [公共]->设置文字大小
	 * 创建时间：2017年7月28日 15:37:50
	 * 更新时间：2017年8月16日 18:56:15
	 * 1、整理代码，去除无用注释
	 */
	$(".js-select-font-size").live("change",function(){
		var value = $(this).val();
		getCustom().attr("data-font-size",value).find("[data-editable]").css("font-size",value + "px");
	});
	
	/**
	 * [公共]->设置文字颜色
	 * 创建时间：2017年7月28日 15:57:59
	 * 更新时间：2017年8月7日 18:50:20
	 * 更新时间：2017年8月16日 18:56:42
	 * 1、整理代码、去除无用注释 
	 */
	$(".js-text-color").live("change",function(){
		var value = $(this).val();
		getCustom().find("[data-editable]").css("color",value);
		switch(getCustom().attr("data-custom-flag")){
		case controlList.Footer:
				bindFooterData();
			break;
			default:
				getCustom().attr("data-text-color",value);
			break;
		}
	});
	
	/**
	 * [商品搜索组件]->设置输入框默认提示内容
	 * 创建时间：2017年7月28日 16:19:30
	 * 更新时间：2017年8月7日 18:51:37
	 */
	$(".js-placeholder").live("keyup",function(){
		var value = $(this).val();
		if($(this).val().length>10){
			value = value.substr(0,10);
			$(this).val(value);
		}
		if(empty(value)) value = $(this).attr("data-default-value");
		getCustom().attr("data-placeholder",value).find("[data-editable]").attr("placeholder",value);
	});

	/**
	 * [商品搜索组件]->设置背景颜色
	 * 创建时间：2017年7月28日 16:03:46
	 * 更新时间：2017年8月7日 18:53:14 
	 */
	$(".js-bg-color").live("change",function(){
		var value = $(this).val();
		getCustom().css("background-color",value).attr("data-bg-color",value);
	});
	
	/**
	 * [商品搜索组件]->设置输入框背景颜色
	 * 创建时间：2017年7月28日 16:41:19
	 * 更新时间：2017年8月7日 18:53:22
	 */
	$(".js-input-bg-color").live("change",function(){
		var value = $(this).val();
		getCustom().attr("data-input-bg-color",value).find("[data-editable]").css("background-color",value);
	});
	
	/**
	 * [轮播图组件]->设置轮播间隔时间
	 * 创建时间：2017年7月31日 14:37:15
	 * 更新时间：2017年8月7日 18:53:28
	 */
	$(".js-carousel-interval").live("change",function(){
		var time = $(this).val()*1000;
		carouselTime = time;
		$('.carousel').carousel("updateInterval");
		getCustom().attr("data-carousel-interval",time);
	});
	
	/**
	 * [商品列表组件]->商品显示个数
	 * 创建时间：2017年8月2日 16:14:51
	 * 更新时间：2017年8月7日 18:53:43
	 */
	$("input[name='showcount']").live("click",function(){
		bindGoodsListData();
	});
	
	/**
	 * [商品列表组件]->列表样式选择
	 * 创建时间：2017年7月31日 17:13:11
	 * 更新时间：2017年8月7日 18:53:50
	 */
	$("input[name='list_type']").live("click",function(){
		var control_name = getCustom().attr("data-custom-flag");
		var goods_list = getCustom().attr("data-goods-list");
		if(!empty(goods_list)) goods_list = eval("(" + goods_list + ")");
		switch(parseInt($(this).val())){
		case $Default.goodsListType[0]:
			//大图:1
			getCustom().html(getGoodsListBigStyleHTML(goods_list) + getCommonHTML(control_name)).attr("data-list-type",$Default.goodsListType[0]);
			break;
		case $Default.goodsListType[1]:
			//小图:2
			getCustom().html(getGoodsListSmallStyleHTML(goods_list) + getCommonHTML(control_name)).attr("data-list-type",$Default.goodsListType[1]);
			break;
		}
		
		if($("#show_buy_button").is(":checked")) $(".control-goods-list .control-goods-price>button").show();
		else $(".control-goods-list .control-goods-price>button").hide();
		
		if($("#show_goods_name").is(":checked")) $(".control-goods-list .control-goods-name").show();
		else $(".control-goods-list .control-goods-name").hide();
		
		if($("#show_goods_price").is(":checked")) $(".control-goods-list .control-goods-price>em").show();
		else $(".control-goods-list .control-goods-price>em").hide();
		
		bindGoodsListData();
	});
	
	/**
	 * [商品列表组件,商品分类组件组件]->显示购买按钮
	 * 创建时间：2017年8月15日 12:30:49
	 * 更新时间：2017年8月19日 17:05:37 王永杰
	 * 1、商品分类也可以操作购买按钮
	 */
	$("#show_buy_button").live("click",function(){
		var checked = $(this).is(":checked");
		var custom_flag = getCustom().attr("data-custom-flag");
		if(checked){
			
			if($("input[name='buy_button_style']:checked").attr("data-buy-button-style") == 4) $(".js-show-buy-button-style").show().next().show();
			else $(".js-show-buy-button-style").show();
			
			if(custom_flag == controlList.GoodsList) getCustom().find(".control-goods-price").show().find(".control-goods-buy-style").show();
			else if(custom_flag == controlList.GoodsClassify) getCustom().find(".control-goods-buy-style").show();
			
		}else{
			
			if($("input[name='buy_button_style']:checked").attr("data-buy-button-style") == 4) $(".js-show-buy-button-style").hide().next().hide();
			else $(".js-show-buy-button-style").hide();
			
			if(custom_flag == controlList.GoodsList){
				//如果价格也隐藏了，那整个块都隐藏
				if(getCustom().find(".control-goods-price").children("em").is(":hidden")) getCustom().find(".control-goods-price").hide();
				else getCustom().find(".control-goods-buy-style").hide();
				
			}else if(custom_flag == controlList.GoodsClassify){
				getCustom().find(".control-goods-buy-style").hide();
			}
			
		}

		if(custom_flag == controlList.GoodsList) bindGoodsListData();
		else if(custom_flag == controlList.GoodsClassify) bindGoodsClassifyData();
	});
	
	/**
	 * [商品列表组件,商品分类组件]->购买按钮样式
	 * 创建时间：2017年8月15日 18:21:59
	 * 更新时间：2017年8月19日 17:12:14
	 * 1、商品分类也可以操作购买按钮
	 */
	$("input[name='buy_button_style']").live("click",function(){
		
		var custom_flag = getCustom().attr("data-custom-flag");
		var img = getCustom().find(".control-goods-buy-style>img");
		var style = $(this).attr("data-buy-button-style");//样式选择
		var value = $(this).val();//图片路径
		if(parseInt(style) != 4){
			
			img.attr("src",__IMG(value));
			$(".custom-buy-style").hide();
			
		}else{
			//自定义购买按钮
			if(!empty(value)) img.attr("src",__IMG(value));
			else img.removeAttr("src");
			$(".custom-buy-style").show();
		}

		if(custom_flag == controlList.GoodsList) bindGoodsListData();
		else if(custom_flag == controlList.GoodsClassify) bindGoodsClassifyData();
		
	});
	
	/**
	 * [商品列表组件]->是否显示商品名称(checkbox)
	 * 创建时间：2017年7月31日 16:51:43
	 * 更新时间：2017年8月7日 18:54:27
	 */
	$("#show_goods_name").live("click",function(){
		getCustom().find(".control-goods-name").fadeToggle();//是否显示商品名称
		bindGoodsListData();
	});
	
	/**
	 * [商品列表组件]->是否显示价格(checkbox)
	 * 创建时间：2017年7月31日 16:57:19
	 * 更新时间：2017年8月7日 18:54:46
	 */
	$("#show_goods_price").live("click",function(){
		
		var checked = $("#show_goods_price").is(":checked");
		if(checked){
			getCustom().find(".control-goods-price").removeClass("position").show().children("em").show();
		}else{
			//如果购买按钮也隐藏了，那整个块都隐藏
			if(getCustom().find(".control-goods-price").children("button").is(":hidden")) getCustom().find(".control-goods-price").hide();
			else getCustom().find(".control-goods-price").addClass("position").children("em").hide();
		}
		bindGoodsListData();
		
	});
	
	/**
	 * [选择商品来源公共]->商品分类
	 * 创建时间：2017年8月2日 18:01:32
	 */
	$(".js-goods-source").live("change",function(){
		bindGoodsListData();
	})
	
	/**
	 * [标题组件]->标题名编辑
	 * 创建时间：2017年8月3日 17:43:10
	 * 更新时间：2017年8月7日 18:54:52
	 */
	$(".js-title").live("keyup",function(){
		var value = $(this).val();
		getCustom().attr("data-title",value).find("h4").text(value);
	});
	
	/**
	 * [标题组件]->副标题编辑
	 * 创建时间：2017年8月3日 17:43:04
	 * 更新时间：2017年8月7日 18:54:58
	 */
	$(".js-subtitle").live("keyup",function(){
		var value = $(this).val();
		getCustom().attr("data-subtitle",value).find("p").text(value);
	});
	
	/**
	 * [显示方式公共]，居左、居中、居右
	 * 创建时间：2017年8月2日 18:19:00
	 * 更新时间：2017年8月7日 18:55:07
	 */
	$("input[name='text_align']").live("click",function(){
		var value = $(this).val();
		getCustom().attr("data-text-align",value).find("[data-editable]").css("text-align",align_array[value-1]);
	});
	
	/**
	 * [自定义链接公共]
	 * 创建时间：2017年7月31日 19:02:06
	 * 更新时间：2017年8月7日 18:55:21
	 */
	$(".js-custom-link").live("click",function(){
		setLinkCustomMarginLeft();
		$(this).parent().parent().parent().find(".float-link-custom").show();
	});
	
	/**
	 * [设置链接地址公共]
	 * 创建时间：2017年7月31日 19:02:08
	 * 更新时间：2017年8月7日 18:55:31
	 */
	$(".js-link li[class!='js-custom-link']").live("click",function(){
		var text = $(this).text();
		var href = $(this).attr("data-href");
		$(this).parent().parent().parent().find(".selected").text(text).attr("data-href",href).css("display","inline-block");
		bindLink();//绑定当前组件所需要的全部链接地址数据
	});
	
	/**
	 * [自定义链接公共]->确定（可能会有多个需要进行拼装）
	 * 创建时间：2017年7月31日 19:02:59
	 * 更新时间：2017年8月7日 18:55:57
	 */
	$(".float-link-custom .btn-common").live("click",function(){
		var value = $(this).prev().val();
		if(value.length){
			
			if(validateDomainName(value)){
				$(this).parent().parent().parent().find(".selected").text(value).attr("data-href",value).css("display","inline-block");
				$(this).parent().parent().parent().find(".float-link-custom").hide();
				bindLink();//绑定当前组件所需要的全部链接地址数据
			}else{
				showTip("链接地址错误","warning");
			}
			
		}else{
			showTip("请输入链接地址","warning");
		}
	});
	
	/**
	 * [自定义链接公共]->键盘事件（可能会有多个需要进行拼装）
	 * 创建时间：2017年8月2日 18:27:47
	 * 更新时间：2017年8月7日 18:56:04
	 */
	$(".float-link-custom input").live("keyup",function(event){
		var value = $(this).val();
		if(value.length){
			
			if(validateDomainName(value)){
				if( event.keyCode == 13){
					$(this).parent().parent().parent().find(".selected").text(value).attr("data-href",value).css("display","inline-block");
					$(this).parent().parent().parent().find(".float-link-custom").hide();
					bindLink();//绑定当前组件所需要的全部链接地址数据
				}
			}else{
				showTip("链接地址错误","warning");
			}
			
		}else{
			showTip("请输入链接地址","warning");
		}
	});
	
	/**
	 * [自定义链接公共]->取消编辑
	 * 创建时间：2017年7月31日 19:24:55
	 * 更新时间：2017年8月7日 18:56:16
	 */
	$(".float-link-custom .btn-common-cancle").live("click",function(){
		$(this).parent().parent().parent().find(".float-link-custom").hide();
	});
	
	/**
	 * [文本导航组件]->导航名称编辑
	 * 创建时间：2017年8月2日 18:48:08
	 * 更新时间：2017年8月7日 18:56:51
	 */
	$(".js-nav-text").live("keyup",function(){
		var value = $(this).val();
		if(empty(value)) getCustom().find("h5 i").hide();
		else getCustom().find("h5 i").css("display","inline-block");
		getCustom().attr("data-nav-text",value).find("span[data-editable]").text(value);
	});
	
	/**
	 * [图片广告组件]->显示方式
	 * 创建时间：2017年8月3日 09:27:27
	 * 更新时间：2017年8月7日 19:02:07
	 */
	$("input[name='show_img_ad_type']").live("click",function(){
		var control_name = getCustom().attr("data-custom-flag");
		if($(this).val() == $Default.advShowType[0]){
			getCustom().removeClass("slide").html(getImgAdvSingleHTML() + getCommonHTML(control_name));
			$(this).parent().parent().children(".control-edit.img-ad:first").show().siblings(".control-edit.img-ad").hide();
		}else if($(this).val() == $Default.advShowType[1]){
			getCustom().addClass("slide").html(getImgAdvCarouselHTML() + getCommonHTML(control_name));
			var new_id = getCustom().attr("id") + $(".custom-main [data-custom-flag='" + controlList.ImgAd + "']").length;
			getCustom().attr("id",new_id).find("a").attr("href","#" + new_id);
			$(this).parent().parent().children(".control-edit.img-ad").show();
			$('.carousel').carousel();//轮播停留时间
		}
		bindImgAdData();
	});
	
	/**
	 * [图片导航组件]->文字编辑
	 * 创建时间：2017年8月3日 17:40:53
	 * 更新时间：2017年8月7日 19:02:13
	 */
	$(".js-nav-hybrid-text").live("keyup",function(){
		var index = $(this).parent().parent().attr("data-index");
		updateNavHybridHTML(index,"label",$(this).val());// 更新当前图片导航组件代码
		bindNavHybridData();//设置图片导航数据json
	});
	
	/**
	 * [商品分类组件]->添加商品来源(商品分类)
	 * 创建时间：2017年8月7日 19:02:28
	 * 更新时间：2017年8月7日 19:02:53
	 */
	$("input[name='goods_classify']").live("change",function(){
		var goods_classify_list = $(this).parent().parent().parent().parent().find(".goods-classify-list>ul");
		var goods_classify_name = $(this).next().text();
		var goods_classify_id = $(this).val();
		var html = '';
		if($(this).is(":checked")){
			html += '<li data-classify-id="' + goods_classify_id + '" data-classify-name="' + goods_classify_name + '" data-show-count="10">';
				html += '<span>商品来源：<em>' + goods_classify_name + '</em></span>';
				html += '<div>';
					html += '<span>显示数量</span>';
					html += '<div class="dropdown">';
						html += '<a class="dropdown-toggle" data-toggle="dropdown" href="#"><span>10</span><b class="caret"></b></a>';
						html += '<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">';
							html += '<li>5</li>';
							html += '<li>10</li>';
							html += '<li>15</li>';
							html += '<li>30</li>';
							html += '<li>50</li>';
							html += '<li>100</li>';
						html += '</ul>';
					html += '</div>';
				html += '</div>';
			html += '</li>';
			goods_classify_list.append(html);
		}else{
			goods_classify_list.find('li[data-classify-id="' + goods_classify_id +'"]').remove();
		}
		bindGoodsClassifyData();
	});
	
	/**
	 * [商品分类组件]->商品来源设置显示数量
	 * 创建时间2017年8月3日 15:02:55
	 * 更新时间：2017年8月7日 19:02:59
	 */
	$(".goods-classify-list .dropdown-menu li").live("click",function(){
		$(this).parent().parent().find("a>span").text($(this).text());
		$(this).parent().parent().parent().parent().attr("data-show-count",$(this).text());
		bindGoodsClassifyData();
	});
	
	/**
	 * [底部菜单组件]->菜单名称编辑
	 * 创建时间：2017年8月3日 17:39:25
	 * 更新时间：2017年8月7日 19:03:05
	 */
	$(".js-footer-menu-name").live("keyup",function(){
		var index = $(this).parent().parent().attr("data-index");
		updateFooterHTML(index,"label",$(this).val());
		bindFooterData();
	});
	
	/**
	 * [辅助线组件]->边框颜色编辑
	 * 创建时间：2017年8月3日 19:11:42
	 * 更新时间：2017年8月7日 19:03:11
	 */
	$(".js-border-color").live("change",function(){
		getCustom().attr("data-border-color",$(this).val()).children("hr").css("border-top-color",$(this).val());
	});
	
	/**
	 * [辅助空白组件]->空白高度编辑
	 * 创建时间：2017年8月3日 19:15:34
	 * 更新时间：2017年8月7日 19:04:11
	 */
	$(".js-blank-height").live("change",function(){
		var value = $(this).val();
		$(this).next().text(value + "px");
		getCustom().attr("data-height",value).css("height",value).children("p").css("line-height",value + "px");
	});
	
	/**
	 * [自定义模块组件]->
	 * 创建时间：2017年8月16日 11:47:33
	 */
	$(".js-select-custom-module").live("change",function(){
		var module_name= $(this).find("option:checked").attr("data-module-name");
		getCustom().find("article>p").text(module_name);
		bindCustomModuleData();
	});
	
	/**
	 * 删除组件
	 * 创建时间：2017年7月31日 15:36:08
	 * 更新时间：2017年8月3日 19:49:35
	 */
	$(".control-delete").live("click",function(){
		var control_name = $(this).attr("data-control-name");
		$(this).parent().parent().remove();
		setControlIsDisabled($("li[data-control-name='" + control_name + "']"),control_name);
		$(".pt.pt-left").hide();
		$(".custom-main .draggable-element").removeAttr("data-is-show").removeClass("selected");
		draggableElementClick($(".custom-main .draggable-element:last"),"select");
		return false;//防止事件冒泡
	});
	
	/**
	 * 重置编辑，同时还原当前编辑组件的样式
	 * 目前只对颜色(包括文字颜色、背景颜色、边框颜色、输入框背景颜色)控件、空白高度控件、文字大小控件进行还原
	 * 创建时间：2017年7月28日 15:49:10
	 * 更新时间：2017年8月7日 19:04:23
	 */
	$(".fa-refresh").live("click",function(){
		var control = $(this).parent().children("[data-jsclass]");
		var jsclass = control.attr("data-jsclass");//要还原的对象
		var value = control.attr("data-default-value");//默认值
		switch(jsclass){
			//文字大小还原
			case "js-select-font-size":
				$(".js-select-font-size").find("option[value='" + value + "']").attr("selected",true).siblings().removeAttr("selected");
				$(".js-select-font-size").change();//调用文字大小改变事件
				break;
			//文字颜色还原，背景颜色，输入框背景颜色，空白高度
			default:
				$("." + jsclass).val(value).change();
				break;
		}
	});
	
	/**
	 * [优惠券组件]->样式选择
	 * 创建时间：2017年8月16日 19:20:34
	 */
	$("input[name='coupons_style']").live("click",function(){
		var value = $(this).val();
		getCustom().find("li>div").removeAttr("class").addClass("style" + value);
		bindCouponsData();
	});
	
	/**
	 * [优惠券组件]->修改背景颜色
	 * 创建时间:2017年8月17日 10:01:40
	 */
	$(".js-coupons-bg-color").live("change",function(){
		var value = $(this).val();
		getCustom().find("li>div").css("background-color",value);
		bindCouponsData();
	});
	
	/**
	 * [视频组件]->视频地址编辑
	 * 创建时间：2017年8月17日 14:54:53
	 * 更新时间：2017年8月18日 18:05:34 王永杰
	 * 1、解决输入链接地址，播放报错问题
	 */
	$(".js-video-url").live("blur",function(){

		var value = $(this).val();
		var video = getCustom().find("video").attr("id");
		if(value.length>0){
			
			if(validateDomainName(value)){

				$("#video_url").val(value);
				$(".video-url-info span").text(value);
				var myPlayer = videojs(video);
				videojs(video).ready(function(){
					var myPlayer = this;
					myPlayer.src(value);
					myPlayer.load(value);
					myPlayer.play();
					setTimeout(function(){
						if(!getCustom().find(".vjs-error-display").hasClass("vjs-hidden")){
							$("#video_url").val("");//video.js Line:7873
							showTip("媒体不能加载，要么是因为服务器或网络失败，要么是因为格式不受支持。","error");
						}
						bindVideoData();
					},1000);
				});
				
			}else{
				showTip("链接地址错误","warning");
			}
		}
	});
	
	/**
	 * [橱窗组件]->布局方式
	 * 
	 * 创建时间：2017年8月18日 09:15:30 王永杰
	 * 1、[2列、3列]
	 */
	$("input[name='show-case-layout']").live("click",function(){
		
		var control_name = getCustom().attr("data-custom-flag");
		var clearance_checked = parseInt($("input[name='show-case-clearance']:checked").val());//图片间隙
		var show_case = getCustom().attr("data-show-case");
		if(!empty(show_case)) show_case = eval("(" + show_case + ")");
		var value = parseInt($(this).val());
		var html = "";
		if(value == 2){
			
			html = getShowCaseDefaultHTML(show_case);
			$(".pt-left .cont .show-case-pre").remove();
			$(".pt-left .cont").prepend(getShowCasePreHTML(2));
			
		}else if(value == 3){
			
			html = getShowCaseMultipleColumnsHTML(show_case);
			$(".pt-left .cont .show-case-pre").remove()
			$(".pt-left .cont").prepend(getShowCasePreHTML(3));
			
		}
		
		html += getCommonHTML(control_name);
		getCustom().html(html);
		bindShowCaseData();
		
	});
	
	/**
	 * [橱窗组件]->图片间隙
	 * 
	 * 创建时间：2017年8月18日 09:26:05
	 */
	$("input[name='show-case-clearance']").live("click",function(){
		
		var value = parseInt($(this).val());
		var layout_checked = $("input[name='show-case-layout']:checked").val();//当前选中的布局方式
		if(value){
			
			if(layout_checked == 2) getCustom().find(".small").removeClass("clear");
			else if(parseInt(layout_checked) == 3) getCustom().find("li").removeClass("clear");
			
		}else{

			if(layout_checked == 2) getCustom().find(".small").addClass("clear");
			else if(parseInt(layout_checked) == 3) getCustom().find("li").addClass("clear");
		}
		
		bindShowCaseData();
	
	});
	
	
	/**
	 * 上下边距
	 * 创建时间：2017年8月18日 10:12:22
	 */
	$(".js-padding").live("change",function(){

		var value = $(this).val();
		$(this).next().text(value + "px");
		getCustom().css("padding",value + "px 0");
		switch(getCustom().attr("data-custom-flag")){
		case controlList.Video:
			bindVideoData();
			break;
		case controlList.ShowCase:
			bindShowCaseData();
			break;
		}
	});
	
	/**
	 * [橱窗组件]->是否显示文字
	 * 创建时间：2017年8月18日 10:12:53
	 */
	$("input[name^='show-case-show-text']").live("click",function(){
		
		var value = $(this).val();
		var index = $(this).parent().parent().parent().attr("data-index");//当前编辑的下标
		if(value == 1){
			
			$(this).parent().parent().next().css("visibility","visible");
			getCustom().find("li:eq(" + index + ") p").show();
			
		}else{
			
			$(this).parent().parent().next().css("visibility","hidden");
			getCustom().find("li:eq(" + index + ") p").hide();
			
		}
		
		bindShowCaseData();
	});
	
	/**
	 * [橱窗组件]->文字内容编辑
	 * 创建时间：2017年8月18日 10:25:09
	 */
	$(".js-show-case-text").live("keyup",function(){
		var index = $(this).parent().parent().attr("data-index");
		var value = $(this).val();
		getCustom().find("li:eq(" + index + ") p").text(value);
		bindShowCaseData();
	});
	
	/**
	 * 图片删除
	 * 创建时间：2017年8月22日 14:16:40
	 */
	$(".js-del-img").live("click",function(){
		
		var index = $(this).parent().find("span>input[type='file']").attr("data-index");
		//删除显示的图片
		$(this).parent().children(".img-block").hide().children("img").removeAttr("src");
		$(this).parent().children("p").show();
		$(this).parent().find("span>input[type='hidden']").val("");
		switch(getCustom().attr("data-custom-flag")){
		
			case controlList.NavHybrid:
				
				//图片导航
				updateNavHybridHTML(index,"img","");//更新当前图片导航组件代码
				bindNavHybridData();//设置图片导航数据json
				
				break;
				
			case controlList.Footer:
				
				//底部
				updateFooterHTML(index,"img","");
				bindFooterData();
				
				break;
				
			case controlList.ImgAd:
			
				//图片广告
				bindImgAdData();
				
				break;
				
			case controlList.GoodsList:
			
				//商品列表中的购买按钮样式
				$("#show_buy_button_style4").val("");
				getCustom().find(".control-goods-buy-style>img").attr("src","");
				bindGoodsListData();
				
				break;
				
			case controlList.GoodsClassify:
				
				$("#show_buy_button_style4").val("");
				getCustom().find(".control-goods-buy-style>img").attr("src","");
				bindGoodsClassifyData();
				break;
				
			case controlList.ShowCase:
				
				//橱窗
				getCustom().find("li:eq(" + index + ")").css({"background" : "url(" + __IMG(res.data) + ") no-repeat center/100% "}).children("div").css("visibility","hidden");
				bindShowCaseData();
				
				break;
		}
		$(this).hide();
		return false;
	});
	
	/**
	 * 底部菜单进入离开切换图片
	 * 创建时间：2017年8月24日 09:06:06
	 */
	$(".control-footer ul li").live("mouseover",function(){
		var footer = $(this).parent().parent().attr("data-footer");
		var index = $(this).attr("data-index");
		if(!empty(footer)){
			footer = eval(footer);
			$(this).children("img").attr("src",footer[index].img_src_hover);
		}
	}).live("mouseout",function(){
		var footer = $(this).parent().parent().attr("data-footer");
		var index = $(this).attr("data-index");
		if(!empty(footer)){
			footer = eval(footer);
			$(this).children("img").attr("src",footer[index].img_src);
		}
	});
	
});

/**
 * 点击组件打开右侧编辑栏
 * 创建时间：2017年8月7日 19:05:36
 */
var $edit = {
	
	/**
	 * 点击组件打开右侧编辑栏
	 * 创建时间：2017年8月1日 14:42:05
	 * 更新时间：2017年8月7日 19:06:30
	 * @param customFlag 组件标识
	 */
	init : function(customFlag){
		try{
			if(customFlag != undefined && $.inArray(controlList,customFlag)){
				return eval("this."+customFlag+"()");
			}else{
				showTip("非法操作","error,"+customFlag);
			}
		}catch(e){
			showTip("非法操作(" + e + ")","error");
			console.log("erorr:" + e);
		}
	},
	
	/**
	 * 自定义模板名称编辑
	 */
	CustomTemplateName : function(){
		var html = getInputHTML("text:required",$cValue.customTemplateName,$(".custom-template>header>h4").attr("data-custom-template-name"));
		return html;
	},
	
	/**
	 * 标题编辑
	 * 可以编辑以下属性：
	 * 1、标题名
	 * 2、副标题
	 * 3、显示方式（居左、居中、居右）
	 * 4、链接地址
	 * 创建时间：2017年7月31日 17:38:00
	 * 更新时间：2017年8月7日 19:06:36
	 */
	Title : function(){
		var html = getInputHTML("text:required",$cValue.title) + getInputHTML("text",$cValue.subTitle) + getTextAlignHTML() + getInputHTML("color",$cValue.titleTextColor,$cValue.titleBgColor) + getLinkHTML();
		return html;
	},
	
	/**
	 * 轮播广告编辑
	 * 可以编辑以下属性：
	 * 1、轮播间隔时间（2s、3s、4s、5s、6s)
	 * 2、样式选择（默认、没有左右箭头、没有点）
	 * 3、显示出来手机端首页轮播图的图片，用户可以进行修改(待做)
	 * 创建时间：2017年7月31日 17:37:59
	 * 更新时间：2017年8月7日 19:06:50
	 */
	Carousel : function(){
		var html = getCarouselIntervalHTML();
		return html;
	},
	
	/**
	 * 文本导航编辑
	 * 可以编辑以下属性：
	 * 1、文字内容
	 * 2、文字颜色
	 * 3、背景色
	 * 4、链接地址
	 * 创建时间：2017年8月1日 14:41:54
	 * 更新时间：2017年8月7日 19:07:10
	 */
	NavText : function(){
		var html = getInputHTML("text:required",$cValue.navText) + getTextSizeHTML() + getInputHTML("color",$cValue.navTextColor) + getLinkHTML();
		return html;
	},
	
	/**
	 * 图片文字混合导航编辑
	 * 可以编辑以下属性：
	 * 1、添加图片
	 * 2、文字
	 * 3、链接
	 * 创建时间：2017年8月1日 14:41:52
	 * 更新时间：2017年8月7日 19:07:20
	 */
	NavHybrid : function(){
		var html = getNavHyBridHTML();
		return html;
	},
	
	/**
	 * 商品搜索编辑
	 * 可以编辑以下属性：
	 * 1、文字大小
	 * 2、文字颜色
	 * 3、默认提示内容
	 * 4、输入框背景颜色
	 * 5、背景颜色
	 * 创建时间：2017年7月28日 16:13:21
	 * 更新时间：2017年8月7日 19:07:36
	 */
	GoodsSearch : function(){
		var html = getTextSizeHTML() + getInputHTML("color",$cValue.searchTextColor,$cValue.searchBgColor,$cValue.searchInputBgColor) + getInputHTML("text",$cValue.searchPlaceholder);
		return html;
	},
	
	/**
	 * 商品列表编辑
	 * 可以编辑以下属性：
	 * 1、商品来源，商品分类
	 * 2、显示个数
	 * 3、列表样式：大图、小图
	 * 极简样式|卡片样式
	 * 显示商品名
	 * 显示商品简介
	 * 显示价格
	 * 创建时间：2017年7月31日 17:37:55
	 * 更新时间：2017年8月7日 19:11:17
	 */
	GoodsList : function (){
		var html = getGoodsListHTML();
		return html;
	},
	
	/**
	 * 商品分类编辑
	 * 可以编辑以下属性：
	 * 1、添加商品分类
	 * 创建时间：2017年8月1日 16:34:30
	 * 更新时间：2017年8月7日 19:11:25
	 */
	GoodsClassify : function(){
		var html = getGoodsClassifyHTML();
		return html;
	},
	
	/**
	 * 公告编辑
	 * 可以编辑以下属性：
	 * 1、公告内容
	 * 2、背景颜色
	 * 3、文字颜色
	 * 创建时间：2017年8月1日 16:41:34
	 * 更新时间：2017年8月7日 19:11:34
	 */
	Notice : function(){
		var html = getInputHTML("color",$cValue.noticeBgColor,$cValue.noticeTextColor);
		return html;
	},
	
	/**
	 * 图片广告编辑
	 * 1、单图广告
	 * 2、轮播广告
	 * 创建时间：2017年8月1日 16:41:36
	 * 更新时间：2017年8月7日 19:11:41
	 */
	ImgAd : function(){
		var html = getImgAdHTML();
		return html;
	},
	
	/**
	 * 2017年8月1日 17:10:30
	 * 底部菜单编辑（共4个）
	 * 1、文字内容
	 * 2、文字颜色，未选中、选中时
	 * 3、图片设置，未选中、选择时的图
	 * 4、
	 */
	Footer : function(){
		var html = getFooterHTML();
		return html;
	},
	
	/**
	 * 辅助线编辑
	 * 1、线的颜色
	 * 2、样式：实线、虚线、点线
	 * 3、左右留白
	 * 创建时间：2017年8月1日 18:18:30
	 * 更新时间：2017年8月7日 19:11:47
	 */
	AuxiliaryLine : function(){
		var html = getInputHTML("color",$cValue.borderColor);
		return html;
	},
	
	/**
	 * 辅助空白编辑
	 * 1、空白高度
	 * 创建时间：2017年8月1日 18:21:55
	 * 更新时间：2017年8月7日 19:11:53
	 */
	AuxiliaryBlank : function(){
		var html = getAuxiliaryBlankHTML();
		return html;
	},
	
	/**
	 * 富文本编辑
	 * 1、百度编辑器
	 * 创建时间：2017年8月2日 11:05:38
	 * 更新时间：2017年8月7日 19:12:35
	 * 更新时间：2017年8月16日 11:30:30
	 * 更新时间：2017年8月23日 09:48:37 王永杰
	 * 1、优化整体结构
	 */
	RichText : function(){
		//处理异步请求，导致的错误显示问题
		setTimeout(function(){
			var content = "『富文本』";
			if(!empty(getCustom().attr("data-rich-text"))){
				content = getCustom().attr("data-rich-text");
			}
			ue.ready(function() {
				ue.setContent(content);
			});
		},1);
		return "";
	},
	
	/**
	 * 自定义模块
	 * 创建时间：2017年8月16日 11:30:25
	 * 1、可以选择其他自定义页面
	 */
	CustomModule : function(){
		var html = getCustomModuleHTML();
		return html;
	},
	
	/**
	 * 优惠券编辑
	 * 创建时间：2017年8月10日 10:59:49
	 */
	Coupons : function(){
		var html = getCouponsHTML();
		
		return html;
	},
	
	/**
	 * 视频编辑
	 * 创建时间：2017年8月16日 20:03:09
	 */
	Video : function(){
		var html = getVideoHTML();
		return html;
	},
	
	/**
	 * 音频编辑
	 * 创建时间：2017年8月17日 18:12:58 王永杰
	 * 1、待做
	 */
	Audio : function(){
		var html = '';
		return html;
	},
	
	/**
	 * 橱窗编辑
	 * 创建时间：2017年8月17日 18:13:17
	 * 1、显示方式：默认，3列
	 * 2、图片间隙：保留，消除
	 * 3、添加图片、链接地址
	 * 4、是否显示文字
	 * 5、文字内容
	 * 6、上下边距
	 */
	ShowCase : function(){
		var html = getShowCaseHTML();
		return html;
	}
};

/**
 * 获取文字大小代码
 * 默认文字大小14px
 * 创建时间：2017年7月28日 15:48:32
 * 更新时间：2017年8月7日 19:13:57
 * @returns html
 */
function getTextSizeHTML(){
	
	var sizeArr = $Default.textSize;
	var value = $Default.textSize[2];//默认字体大小14
	//当前组件设置过文字大小，则进行赋值
	if(getCustom().attr("data-font-size")) value = getCustom().attr("data-font-size");
	var html = '<div class="control-edit font-size">';
		html += '<label>文字大小：</label>';
		html += '<select class="select-common js-select-font-size" data-jsclass="js-select-font-size" data-default-value="' + $Default.textSize[2] + '">';
		for(i in sizeArr){
			
			if(sizeArr[i] == value){
				html += '<option value="' + sizeArr[i] + '" selected="selected">' + sizeArr[i] + 'px</option>';
				continue;
			}
			html += '<option value="' + sizeArr[i] + '">' + sizeArr[i] + 'px</option>';
			
		}
		html += '</select>';
		html += '<i class="fa fa-refresh fr"></i>';
	html += '</div>';
	
	return html;
}

/**
 * 获取输入框代码
 * 第一个参数是type：[color,input]输入框类型:是否必填标识，最后一个参数是要赋给前边的值
 * 创建时间：2017年8月7日 10:02:32
 * 更新时间：2017年8月7日 19:14:01
 */
function getInputHTML(){
	
	var html = '';
	var type = arguments[0];//输入框类型:是否必填标识
	
	//从第二个开始循环
	for(var i =1;i<arguments.length;i++){
		var obj = arguments[i];
		
		if(typeof obj == "string") break;
		
		var value = obj.value;//值传递，不修改引用对象
		
		if((i+1) == (arguments.length-1) && typeof arguments[arguments.length-1] == "string"){
			value = arguments[i+1];
		}else{
			var data = getCustom().attr(obj.input_class.replace("js-","data-"));
			//存在赋值，条件是：不能与默认值相同
			if(!empty(data) && data != obj.default_value) value = data;
		}
		
		//当前组件如果有颜色，则进行赋值
		var type_arr = type.split(":");
		var required = "";
		if(!empty(type_arr[1])) required = "<span>*</span>";
		
		html += '<div class="control-edit ' + obj.class_name + '">';
			html += '<label>' + required + obj.name + '：</label>';
			html += '<input type="' + type_arr[0] + '" class="input-common ' + obj.input_class + '" value="' + value + '" data-jsclass="' + obj.input_class + '" data-default-value="' + obj.default_value + '" placeholder="' + obj.placeholder + '" >';
			if(type_arr[0] == "color") html += '<i class="fa fa-refresh fr"></i>';
			
		html += '</div>';
	}
	
	return html;
}

/**
 * 获取轮播间隔代码[2-6秒]
 * 创建时间：2017年7月28日 18:41:01
 * 更新时间：2017年8月7日 19:14:33
 */
function getCarouselIntervalHTML(){
	
	var interval_arr = [2,3,4,5,6];
	var value = $Default.carouselInterval/1000;
	var data = getCustom().attr("data-carousel-interval");
	
	if(data) value = parseInt(data)/1000;
	
	var html = '<div class="control-edit carousel-interval">';
			html += '<label>轮播间隔时间(秒)：</label>';
			html += '<select class="select-common js-carousel-interval">';
			for(i in interval_arr){
				if(value == interval_arr[i]){
					html += '<option value=' + interval_arr[i] + ' selected="selected">' + interval_arr[i] + 's</option>';
					continue;
				}
				html += '<option value=' + interval_arr[i] + '>' + interval_arr[i] + 's</option>';
			}
			html += '</select>';
		html += '</div>';
		
	return html;
}

/**
 * 获取商品列表代码
 * 创建时间：2017年7月31日 15:55:59
 * 更新时间：2017年8月7日 19:15:11
 */
function getGoodsListHTML(){
	
	var goods_list = getCustom().attr("data-goods-list");//存在则赋值
	if(!empty(goods_list)) goods_list = eval("(" + goods_list + ")");
	var html = '<div class="control-edit goods-source">';
		html += '<label><span>*</span>商品来源：</label>';
		html += '<div>';
			html += '<select class="select-common js-goods-source">';
			if(!empty(goods_category_list) && goods_category_list.length>0){

				for(var i=0;i<goods_category_list.length;i++){
				
					var goods_category = goods_category_list[i];
					
					if(!empty(goods_list) && goods_list.goods_source == goods_category.category_id){
						html += '<option value="' + goods_category.category_id + '" selected="selected">' + goods_category.category_name + "</option>";
						continue;
					}
					html += '<option value="' + goods_category.category_id + '">' + goods_category.category_name + "</option>";
				}
			}else{
				html += '<option value="0">没有发现商品来源</option>';
			}
			
			html += '</select>';
			html += '<p class="description">选择商品来源后，左侧实时预览暂不支持显示其包含的商品数据</p>';
		html += '</div>';
	html += '</div>';
	
	html += '<div class="control-edit goods-count">';
		html += '<label>显示个数：</label>';
		html += '<div>';
		for(var i=0;i<$Default.goodsLimitCount.length;i++){
			
			var curr = $Default.goodsLimitCount[i];
			var checked = '';
			if(!empty(goods_list) && goods_list.goods_limit_count == curr) checked = 'checked="checked"';
			else if(i == 0) checked = 'checked="checked"';
			
			html += '<input type="radio" ' + checked + ' value="' + curr + '" id="show_count' + curr + '" name="showcount">&nbsp;';
			html += '<label for="show_count' + curr + '" class="label-for">' + curr + '</label>';
			
		}
		html += '</div>';
		
	html += '</div>';
	
	html += '<div class="control-edit list-style">';
		html += '<label>列表样式：</label>';
		
		if(!empty(goods_list) && goods_list.goods_list_type == $Default.goodsListType[1]){
			
			html += '<input type="radio" value="' + $Default.goodsListType[0] + '" id="list_type1" name="list_type">&nbsp;';
			html += '<label for="list_type1" class="label-for">大图</label>';
			html += '<input type="radio" value="' + $Default.goodsListType[1] + '" id="list_type2" name="list_type" checked="checked">&nbsp;';
			html += '<label for="list_type2" class="label-for">小图</label>';
			
		}else{
			
			html += '<input type="radio" value="' + $Default.goodsListType[0] + '" id="list_type1" name="list_type" checked="checked">&nbsp;';
			html += '<label for="list_type1" class="label-for">大图</label>';
			html += '<input type="radio" value="' + $Default.goodsListType[1] + '" id="list_type2" name="list_type">&nbsp;';
			html += '<label for="list_type2" class="label-for">小图</label>';
			
		}
		
	html += '</div>';
	
	html += '<div class="control-edit-attribute">';

		html += '<div class="js-show-buy-button">';
			var show_buy_button_checked = "checked='checked'";
			if(!empty(goods_list) && parseInt(goods_list.show_buy_button) == 0) show_buy_button_checked = "";
			html += '<input type="checkbox" ' + show_buy_button_checked + ' value="0" id="show_buy_button">&nbsp;';
			html += '<label for="show_buy_button" class="label-for">显示购买按钮</label>';
		html += '</div>';

		html += '<div class="js-show-buy-button-style show-buy-button-style">';
		
			var buy_button_style1_checked = "";
			var buy_button_style2_checked = "";
			var buy_button_style3_checked = "";
			var buy_button_style4_checked = "";
			var buy_button_src = "";//购买按钮图片路径
			
			if(!empty(goods_list) && goods_list.goods_buy_button_style == 1){
				buy_button_style1_checked = "checked='checked'";
			}else if(!empty(goods_list) && goods_list.goods_buy_button_style == 2){
				buy_button_style2_checked = "checked='checked'";
			}else if(!empty(goods_list) && goods_list.goods_buy_button_style == 3){
				buy_button_style3_checked = "checked='checked'";
			}else if(!empty(goods_list) && goods_list.goods_buy_button_style == 4) {
				buy_button_style4_checked = "checked='checked'";
				buy_button_src = goods_list.goods_buy_button_src;
			}else {
				buy_button_style1_checked = "checked='checked'";
			}
			
			html += '<input type="radio" ' + buy_button_style1_checked + ' value="upload/custom_template/goods_buy_button_style1.png" id="buy_button_style1" name="buy_button_style" data-buy-button-style="1">&nbsp;';
			html += '<label for="buy_button_style1" class="label-for">样式1</label>';
			html += '<input type="radio" ' + buy_button_style2_checked + ' value="upload/custom_template/goods_buy_button_style2.png" id="buy_button_style2" name="buy_button_style" data-buy-button-style="2">&nbsp;';
			html += '<label for="buy_button_style2" class="label-for">样式2</label>';
			html += '<input type="radio" ' + buy_button_style3_checked + ' value="upload/custom_template/goods_buy_button_style3.png" id="buy_button_style3" name="buy_button_style" data-buy-button-style="3">&nbsp;';
			html += '<label for="buy_button_style3" class="label-for">样式3</label>';
			html += '<input type="radio" ' + buy_button_style4_checked + ' value="'  + buy_button_src + '" id="show_buy_button_style4" name="buy_button_style" data-buy-button-style="4">&nbsp;';
			html += '<label for="show_buy_button_style4" class="label-for">自定义样式</label>';
			
		html += '</div>';
		
		if(!empty(goods_list) && goods_list.goods_buy_button_style == 4) html += '<div class="control-edit custom-buy-style" style="display:block;">';
		else html += '<div class="control-edit custom-buy-style">';
		
			html += '<div class="add-img">';
				if(!empty(goods_list) && goods_list.goods_buy_button_style == 4 && !empty(buy_button_src)){
		
					html += '<div class="img-block" style="display:block;"><img id="img_custom_buy_style" style="max-height:100%;" src="' + __IMG(buy_button_src) + '"></div>';
					html += '<span>';
					html += '<input class="input-file" name="file_upload" id="upload_img_custom_buy_style" type="file" onchange="imgUpload(this);">';
					html += '<input type="hidden" id="custom_buy_style" value="' + buy_button_src + '">';
					html += '</span>';
					html += '<p id="text_custom_buy_style" style="display:none;">添加图片<br><span>建议尺寸40*30</span></p>';
					html += '<i class="fa fa-close js-del-img" style="display:block;"></i>';
					
				}else{
					
					html += '<div class="img-block"><img id="img_custom_buy_style"></div>';
					html += '<span>';
					html += '<input class="input-file" name="file_upload" id="upload_img_custom_buy_style" type="file" onchange="imgUpload(this);">';
					html += '<input type="hidden" id="custom_buy_style">';
					html += '</span>';
					html += '<p id="text_custom_buy_style">添加图片<br><span>建议尺寸40*30</span></p>';
					html += '<i class="fa fa-close js-del-img"></i>';
					
				}
			html +='</div>';
		html += '</div>';
		
		html += '<div class="js-show-goods-name">';
			var show_goods_name_checked = "checked='checked'";
			
			if(!empty(goods_list) && parseInt(goods_list.goods_show_goods_name) == 0) show_goods_name_checked = "";
			
			html += '<input type="checkbox" ' + show_goods_name_checked + ' value="0" id="show_goods_name">&nbsp;';
			html += '<label for="show_goods_name" class="label-for">显示商品名称</label>';
		html += '</div>';
		
		html += '<div class="js-show-goods-price">';
		var show_goods_price_checked = "checked='checked'";
		
		if(!empty(goods_list) && parseInt(goods_list.goods_show_goods_price) == 0) show_goods_price_checked = "";
		
		html += '<input type="checkbox" ' + show_goods_price_checked + ' value="0" id="show_goods_price">&nbsp;';
		html += '<label for="show_goods_price" class="label-for">显示价格</label>';
		html += '</div>';
		
	html += '</div>';
	
	return html;
}

/**
 * 文字居中代码
 * 创建时间：2017年7月31日 17:34:46
 * 更新时间：2017年8月7日 19:15:22
 */
function getTextAlignHTML(){
	
	var text_align = getCustom().attr("data-text-align");//存在则进行赋值
	var left = "",center = "",right = "";
	var html = '<div class="control-edit text-align">';
		html += '<label>显示：</label>';
		
		if(text_align && text_align == $Default.textAlign[0]) left = 'checked="checked"';
		else if(text_align && text_align == $Default.textAlign[1]) center = 'checked="checked"';
		else if(text_align && text_align == $Default.textAlign[2]) right = 'checked="checked"';
		else left = 'checked="checked"';
		
		html += '<input type="radio" value="' + $Default.textAlign[0] + '" ' + left + ' id="text_align_left" name="text_align">&nbsp;';
		html += '<label for="text_align_left" class="label-for">居左</label>';
		html += '<input type="radio" value="' + $Default.textAlign[1] + '" ' + center + ' id="text_align_center" name="text_align">&nbsp;';
		html += '<label for="text_align_center" class="label-for">居中</label>';
		html += '<input type="radio" value="' + $Default.textAlign[2] + '" ' + right + ' id="text_align_right" name="text_align">&nbsp;';
		html += '<label for="text_align_right" class="label-for">居右</label>';
	html += '</div>';
	
	return html;
}

/**
 * 获取链接代码，后期需要查询动态数据
 * 创建时间：2017年7月31日 17:39:19
 * 更新时间：2017年8月8日 19:32:39
 * @param 链接地址，存在则赋值
 */
function getLinkHTML(href){
	
	var href_name = "";
	
	if(!empty(template_list) && template_list.length>0){
		for(var i=0;i<template_list.length;i++){
			var curr = template_list[i];
			link_arr[__URL(APPMAIN + '/index/customtemplatecontrol?id=' +curr.id)] = curr.template_name;
		}
	}
	
	if(href == $Default.href) href = "";
	
	if(!empty(href)) href_name = link_arr[href];
	
	if(!empty(getCustom().attr("data-href")) && getCustom().attr("data-href") != $Default.href && empty(href)) href = link_arr[getCustom().attr("data-href")];
	
	if(empty(href_name)) href_name = href;
	var html = '<div class="control-edit link">';
		html += '<label>链接地址：</label>';
		if(href) html += '<span class="selected" style="display:inline-block;" title="' + href + '" data-href="' + href + '">' + href_name + '</span>';
		else html += '<span class="selected"></span>';
		
		html += '<div class="custom-input">';
			html += '<div class="float-link-custom">';
			html += '<div class="arrow"></div>';
				html += '<input type="text" class="input-common" placeholder="链接地址：http://example.com" />';
				html += '<button class="btn-common">确定</button>';
				html += '<button class="btn-common-cancle">取消</button>';
			html += '</div>';
		html += '</div>';
		html += '<div class="dropdown">';
			html += '<a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="javascript:;">';
				html += '设置链接地址';
				html += '<b class="caret"></b>';
			html += '</a>';
			
			html += '<ul class="dropdown-menu js-link" role="menu" aria-labelledby="dLabel">';
				for(link in link_arr){
					html += '<li data-href="' + link + '">' + link_arr[link] + '</li>';
				}
				html += '<li class="js-custom-link">自定义链接</li>';
			html += '</ul>';
		html += '</div>';
	html += '</div>';
	
	return html;
}

/**
 * 设置自定义链接编辑框的位置
 * 创建时间：2017年7月31日 19:55:12
 * 更新时间：2017年8月7日 19:15:45
 */
function setLinkCustomMarginLeft(){
	var margin_left = ($(".control-edit.link .selected").outerWidth() + $(".control-edit.link .dropdown").outerWidth()+100)/2;
	$(".float-link-custom").css("margin-left",-margin_left);
}

/**
 * 商品分类代码
 * 创建时间：2017年8月1日 15:43:34
 * 更新时间：2017年8月7日 19:17:53
 * 更新时间：2017年8月19日 16:56:01 王永杰
 * 1、新增购买按钮编辑功能
 */
function getGoodsClassifyHTML(){
	
	var goods_classify = getCustom().attr("data-goods-classify");//存在则赋值
	if(!empty(goods_classify)){
		goods_classify = eval(goods_classify);
	}
	
	var html = '<div class="control-edit-attribute" style="padding:10px;">';
		html += '<div class="js-show-buy-button" style="margin-left:0;">';
			html += '<input type="checkbox" checked="checked" value="0" id="show_buy_button">&nbsp;';
			html += '<label for="show_buy_button" class="label-for">显示购买按钮</label>';
		html += '</div>';
		
		html += '<div class="js-show-buy-button-style show-buy-button-style" style="margin-left:10px;">';

			var buy_button_style1_checked = "";
			var buy_button_style2_checked = "";
			var buy_button_style3_checked = "";
			var buy_button_style4_checked = "";
			var buy_button_src = "";//购买按钮图片路径
			
			if(!empty(goods_classify) && goods_classify[0].goods_buy_button_style == 1){
				
				buy_button_style1_checked = "checked='checked'";
				
			}else if(!empty(goods_classify) && goods_classify[0].goods_buy_button_style == 2){
				
				buy_button_style2_checked = "checked='checked'";
				
			}else if(!empty(goods_classify) && goods_classify[0].goods_buy_button_style == 3){
				
				buy_button_style3_checked = "checked='checked'";
				
			}else if(!empty(goods_classify) && goods_classify[0].goods_buy_button_style == 4) {
				
				buy_button_style4_checked = "checked='checked'";
				buy_button_src = goods_classify[0].goods_buy_button_src;
				
			}else {
				
				buy_button_style1_checked = "checked='checked'";
				
			}
			
			html += '<input type="radio" ' + buy_button_style1_checked + ' value="upload/custom_template/goods_buy_button_style1.png" id="buy_button_style1" name="buy_button_style" data-buy-button-style="1">&nbsp;';
			html += '<label for="buy_button_style1" class="label-for">样式1</label>';
			html += '<input type="radio" ' + buy_button_style2_checked + ' value="upload/custom_template/goods_buy_button_style2.png" id="buy_button_style2" name="buy_button_style" data-buy-button-style="2">&nbsp;';
			html += '<label for="buy_button_style2" class="label-for">样式2</label>';
			html += '<input type="radio" ' + buy_button_style3_checked + ' value="upload/custom_template/goods_buy_button_style3.png" id="buy_button_style3" name="buy_button_style" data-buy-button-style="3">&nbsp;';
			html += '<label for="buy_button_style3" class="label-for">样式3</label>';
			html += '<input type="radio" ' + buy_button_style4_checked + ' value="' + buy_button_src + '" id="show_buy_button_style4" name="buy_button_style" data-buy-button-style="4">&nbsp;';
			html += '<label for="show_buy_button_style4" class="label-for">自定义样式</label>';
			
		html += '</div>';
		
		if(!empty(buy_button_src)) html += '<div class="control-edit custom-buy-style" style="margin-left:10px;display:block;">';
		else html += '<div class="control-edit custom-buy-style" style="margin-left:10px;">';
			html += '<div class="add-img">';
			
				if(!empty(buy_button_src)) html += '<div class="img-block" style="display: block;"><img id="img_custom_buy_style" src="' + __IMG(buy_button_src) + '" style="max-height: 100%;"></div>';
				else html += '<div class="img-block"><img id="img_custom_buy_style"></div>';
			
				html += '<span>';
					html += '<input class="input-file" name="file_upload" id="upload_img_custom_buy_style" type="file" onchange="imgUpload(this);">';
					html += '<input type="hidden" id="custom_buy_style" value="' + buy_button_src + '">';
				html += '</span>';
				if(!empty(buy_button_src)){
					html += '<p id="text_custom_buy_style" style="display:none;">添加图片<br><span>建议尺寸40*30</span></p>';
					html += '<i class="fa fa-close js-del-img" style="display:block;"></i>';
				}else{
					html += '<p id="text_custom_buy_style">添加图片<br><span>建议尺寸40*30</span></p>';
					html += '<i class="fa fa-close js-del-img"></i>';
				}

			html += '</div>';
		html += '</div>';
		
	html += '</div>';
	html += '<div class="control-edit goods-classify">';
		html += '<div class="goods-classify-list">';
			html += '<ul>';
				if(!empty(goods_classify)){
					for(var i=0;i<goods_classify.length;i++){
						if(!empty(goods_classify[i].name)){
							html += '<li data-classify-id="' + goods_classify[i].id + '" data-classify-name="' + goods_classify[i].name + '" data-show-count="' + goods_classify[i].show_count + '">';
								html += '<span>商品来源：<em>' + goods_classify[i].name + '</em></span>';
								html += '<div>';
									html += '<span>显示数量</span>';
									html += '<div class="dropdown">';
										html += '<a class="dropdown-toggle" data-toggle="dropdown" href="#"><span>' + goods_classify[i].show_count +'</span><b class="caret"></b></a>';
										html += '<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">';
											html += '<li>5</li>';
											html += '<li>10</li>';
											html += '<li>15</li>';
											html += '<li>30</li>';
											html += '<li>50</li>';
											html += '<li>100</li>';
										html += '</ul>';
									html += '</div>';
								html += '</div>';
							html += '</li>';
						}
					}
				}
			html += '</ul>';
		html += '</div>';
		if(goods_category_list != null && goods_category_list.length>0){
			html += '<div class="add-goods-classify">选择商品分类';
				html += "<ul>";
					for(var i=0;i<goods_category_list.length;i++){
						var goods_category = goods_category_list[i];
						var checkbox = '';
						if(!empty(goods_classify)){
							for(var j=0;j<goods_classify.length;j++){
								if(parseInt(goods_classify[j].id) == parseInt(goods_category.category_id)){
									checkbox = 'checked="checked"';
									break;
								}
							}
						}
						html += '<li><input value="' + goods_category.category_id + '" ' + checkbox + ' type="checkbox" id="goods_classify'+ goods_category.category_id +'" name="goods_classify"><label class="label-for" for="goods_classify'+ goods_category.category_id +'">' + goods_category.category_name + "</label></li>";
					}
				html += "</ul>";
		}else{
			html += '<div class="add-goods-classify" style="color:#999999;">暂无商品分类';
		}
		html += '</div>';
		html += '<p class="description">选择商品来源后，左侧实时预览暂不支持显示其包含的商品数据</p>';
	html += '</div>';

	return html;
}

/**
 * 获取图片广告代码
 * 创建时间：2017年8月1日 16:44:06
 * 更新时间：2017年8月7日 19:20:27
 */
function getImgAdHTML(){
	
	var single = "";
	var multiple = "";
	var count = 4;//图片个数
	var adv_show_type = $Default.advShowType[0];//默认是单图广告
	var img_ad = getCustom().attr("data-img-ad");//存在则赋值
	if(!empty(img_ad)){
		img_ad = eval(img_ad);
		adv_show_type = img_ad[0].adv_show_type;
	}
	
	if(adv_show_type == $Default.advShowType[0]) single = 'checked="checked"';
	else if(adv_show_type == $Default.advShowType[1]) multiple = 'checked="checked"';
	else single = 'checked="checked"';
	
	var html = '<div class="control-edit img-ad-align">';
		html += '<label>显示方式：</label>';
		html += '<input type="radio" value="' + $Default.advShowType[0] + '" id="show_img_ad_type_single" name="show_img_ad_type" ' + single + '>&nbsp;';
		html += '<label for="show_img_ad_type_single" class="label-for">单图广告</label>';
		html += '<input type="radio" value="' + $Default.advShowType[1] + '" id="show_img_ad_type_multiple" name="show_img_ad_type" ' + multiple + '>&nbsp;';
		html += '<label for="show_img_ad_type_multiple" class="label-for">多图轮播广告</label>';
	html += '</div>';
	
	for(var i=0;i<count;i++){
		if(i!=0 && adv_show_type == $Default.advShowType[0]){
			html += '<div class="control-edit img-ad" style="display:none;">';
		}else{
			html += '<div class="control-edit img-ad">';
		}
			if(!empty(img_ad) && !empty(img_ad[i]) && !empty(img_ad[i].src)){
				html += '<div class="add-img">';
					html += '<div class="img-block" style="display:block;"><img id="img_imgad' + i + '" style="max-height:100%;" src="' + __IMG(img_ad[i].src) + '"></div>';
					html += '<span>';
						html += '<input class="input-file" name="file_upload" id="upload_imgad' + i + '" type="file" onchange="imgUpload(this);">';
						html += '<input type="hidden" id="imgad' + i + '" value="' + img_ad[i].src + '">';
					html += '</span>';
					html += '<p id="text_imgad' + i + '" style="display:none;">添加图片<br><span>建议尺寸320*80</span></p>';
					html += '<i class="fa fa-close js-del-img" style="display:block;"></i>';
				}else{
					html += '<div class="add-img">';
					html += '<div class="img-block"><img id="img_imgad' + i + '"></div>';
					html += '<span>';
					html += '<input class="input-file" name="file_upload" id="upload_imgad' + i + '" type="file" onchange="imgUpload(this);">';
					html += '<input type="hidden" id="imgad' + i + '">';
					html += '</span>';
					html += '<p id="text_imgad' + i + '">添加图片<br><span>建议尺寸320*80</span></p>';
					html += '<i class="fa fa-close js-del-img"></i>';
				}
			html +='</div>';
			html += '<div class="info">';
			
				if(!empty(img_ad) && !empty(img_ad[i]) && !empty(img_ad[i].href)) html += getLinkHTML(img_ad[i].href);
				else html += getLinkHTML();
				
			html += '</div>';
		html += '</div>';
	}
	return html;
}

/**
 * 图片导航代码
 * 创建时间：2017年8月1日 14:44:30
 * 更新时间：2017年8月5日 09:52:39
 * 
 * @returns {String} html
 */
function getNavHyBridHTML(){
	
	var html = '';
	var nav_hybrid = getCustom().attr("data-nav-hybrid");//存在则进行赋值
	if(!empty(nav_hybrid)) nav_hybrid = eval(nav_hybrid);
	for(var i=0;i<$Default.navHyBridItemCount;i++){
		html += '<div class="control-edit nav-hy-brid">';
			if(!empty(nav_hybrid) && !empty(nav_hybrid[i]) && !empty(nav_hybrid[i].src)){
				html += '<div class="add-img">';
					html += '<div class="img-block" style="display:block;"><img id="img_imgad' + i + '" style="max-height:100%;" src="' + __IMG(nav_hybrid[i].src) + '"></div>';
					html += '<span>';
						html += '<input class="input-file" name="file_upload" id="upload_imgad' + i + '" type="file" onchange="imgUpload(this);" data-index="' + i + '">';
						html += '<input type="hidden" id="imgad' + i + '" value="' + nav_hybrid[i].src + '">';
					html += '</span>';
					html += '<p id="text_nav_hybrid_img' + i + '" style="display:none;">添加图片<br><span>建议尺寸40*40</span></p>';
					html += '<i class="fa fa-close js-del-img" style="display:block;"></i>';
			}else{
				html += '<div class="add-img">';
					html += '<div class="img-block"><img id="img_nav_hybrid_img' + i + '"></div>';
					html += '<span>';
						html += '<input class="input-file" name="file_upload" id="upload_nav_hybrid_img' + i + '" type="file" onchange="imgUpload(this);" data-index="' + i + '">';
						html += '<input type="hidden" id="nav_hybrid_img' + i + '">';
					html += '</span>';
					html += '<p id="text_nav_hybrid_img' + i + '">添加图片<br><span>建议尺寸40*40</span></p>';
					html += '<i class="fa fa-close js-del-img"></i>';
			}
			html +='</div>';
			html += '<div class="info" data-index="' + i + '">';
				if(!empty(nav_hybrid) && !empty(nav_hybrid[i]) && !empty(nav_hybrid[i].text)) html += getInputHTML("text",$cValue.navHyBridText,nav_hybrid[i].text);
				else html += getInputHTML("text",$cValue.navHyBridText);
				
				if(!empty(nav_hybrid) && !empty(nav_hybrid[i]) && !empty(nav_hybrid[i].href)) html += getLinkHTML(nav_hybrid[i].href);
				else html += getLinkHTML();
			html += '</div>';
		html += '</div>';
	}
	
	return html;
}

/**
 * 底部菜单代码
 * 创建时间：2017年8月1日 17:21:54
 * 更新时间：2017年8月7日 19:22:51
 */
function getFooterHTML(){
	
	var footer = getCustom().attr("data-footer");//存在则赋值
	if(!empty(footer)) footer = eval(footer);
	
	var html = '';
	if(!empty(footer) && !empty(footer[0]) && !empty(footer[0].color)) html += getInputHTML("color",$cValue.footerTextColor,footer[0].color);
	else html += getInputHTML("color",$cValue.footerTextColor);
	
	if(!empty(footer) && !empty(footer[0]) && !empty(footer[0].color_hover)) html += getInputHTML("color",$cValue.textColorHover,footer[0].color_hover);
	else html += getInputHTML("color",$cValue.textColorHover);
	
	for(var i=0;i<$Default.footerItemCount;i++){
		html += '<div class="control-edit footer">';
			html += '<div class="imglist">';
				if(!empty(footer) && !empty(footer[i]) && !empty(footer[i].img_src)){
					html += '<div class="add-img js-img-footer">';
						html += '<div class="img-block" style="display:block;"><img id="img_footer' + i + '" style="max-height:100%;" src="' + __IMG(footer[i].img_src) + '"></div>';
						html += '<span>';
							html += '<input class="input-file" name="file_upload" id="upload_footer_img' + i + '" type="file" onchange="imgUpload(this);" data-index="' + i + '">';
							html += '<input type="hidden" id="footer' + i + '" value="' + footer[i].img_src + '">';
						html += '</span>';
						html += '<p id="text_footer' + i + '" style="display:none;">未选中的图片<br><span>建议尺寸30*30</span></p>';
						html += '<i class="fa fa-close js-del-img" style="display:block;"></i>';
					html +='</div>';
				}else{
					html += '<div class="add-img js-img-footer">';
						html += '<div class="img-block"><img id="img_footer' + i + '"></div>';
						html += '<span>';
							html += '<input class="input-file" name="file_upload" id="upload_footer_img' + i + '" type="file" onchange="imgUpload(this);" data-index="' + i + '">';
							html += '<input type="hidden" id="footer' + i + '">';
						html += '</span>';
						html += '<p id="text_footer' + i + '">未选中的图片<br><span>建议尺寸30*30</span></p>';
						html += '<i class="fa fa-close js-del-img"></i>';
					html +='</div>';
				}

				if(!empty(footer) && !empty(footer[i]) && !empty(footer[i].img_src_hover)){

					html += '<div class="add-img js-img-footer-hover">';
						html += '<div class="img-block" style="display:block;"><img id="img_footer_hover' + i + '" style="max-height:100%;" src="' + __IMG(footer[i].img_src_hover) + '"></div>';
						html += '<span>';
							html += '<input class="input-file" name="file_upload" id="upload_footer_hover_img' + i + '" type="file" onchange="imgUpload(this);" data-index="' + i + '">';
							html += '<input type="hidden" id="footer_hover' + i + '" value="' + footer[i].img_src_hover + '">';
						html += '</span>';
						html += '<p id="text_footer_hover' + i + '" style="display:none;">选中后的图片<br><span>建议尺寸30*30</span></p>';
						html += '<i class="fa fa-close js-del-img" style="display:block;"></i>';
					html +='</div>';
					
				}else{

					html += '<div class="add-img js-img-footer-hover">';
						html += '<div class="img-block"><img id="img_footer_hover' + i + '"></div>';
						html += '<span>';
							html += '<input class="input-file" name="file_upload" id="upload_footer_hover_img' + i + '" type="file" onchange="imgUpload(this);" data-index="' + i + '">';
							html += '<input type="hidden" id="footer_hover' + i + '">';
						html += '</span>';
						html += '<p id="text_footer_hover' + i + '">选中后的图片<br><span>建议尺寸30*30</span></p>';
						html += '<i class="fa fa-close js-del-img"></i>';
					html +='</div>';
					
				}
			html += '</div>';
			html += '<div class="info" data-index="' + i + '">';
					
			if(!empty(footer) && !empty(footer[i]) && !empty(footer[i].menu_name)) html += getInputHTML("text",$cValue.footerMenuName,footer[i].menu_name);
			else html += getInputHTML("text",$cValue.footerMenuName);
			
			if(!empty(footer) && !empty(footer[i]) && !empty(footer[i].href)) html += getLinkHTML(footer[i].href);
			else html += getLinkHTML();
			html += '</div>';
		html += '</div>';
	}
	
	return html;
}

/**
 * 辅助空白高度代码
 * 创建时间：2017年8月1日 18:23:56
 * 更新时间：2017年8月7日 19:23:31
 */
function getAuxiliaryBlankHTML(){
	
	var height = getCustom().attr("data-height");//存在则赋值
	var value = $Default.auxiliaryBlankHeightMin;
	var html = '<div class="control-edit auxiliary-blank">';
		html += '<label>空白高度</label>';
		if(!empty(height)) value = height;
		html += '<input type="range" min="' + $Default.auxiliaryBlankHeightMin + '" max="' + $Default.auxiliaryBlankHeightMax + '" class="input-common js-blank-height" data-jsclass="js-blank-height" value="' + value + '" data-default-value="' + $Default.auxiliaryBlankHeightMin + '">';
		html += '<span>' + value + 'px</span>';
		html += '<i class="fa fa-refresh fr"></i>';
	html += '</div>';
	
	return html;
}

/**
 * 图片上传
 * 创建时间：2017年8月7日 19:23:28
 * 更新时间：2017年8月7日 19:25:02
 */
function imgUpload(event) {
	
	var fileid = $(event).attr("id");
	var index = $(event).attr("data-index");
	var data = { 'file_path' : UPLOADCOMMON };
	var id = $(event).next().attr("id");
	var del_img = $(event).parent().parent().find("i");
	uploadFile(fileid,data,function(res){
		
		if(res.code){
			$("#" + id).val(res.data);
			$("#img_" + id).attr("src",__IMG(res.data)).css("max-height","100%").parent().show();
			$("#text_" + id).hide();
			del_img.show();
			switch(getCustom().attr("data-custom-flag")){
			
				case controlList.NavHybrid:
					
					//图片导航
					updateNavHybridHTML(index,"img",__IMG(res.data));//更新当前图片导航组件代码
					bindNavHybridData();//设置图片导航数据json
					
					break;
					
				case controlList.Footer:
					
					//底部
					updateFooterHTML(index,"img",__IMG(res.data));
					bindFooterData();
					
					break;
					
				case controlList.ImgAd:
				
					//图片广告
					bindImgAdData();
					
					break;
					
				case controlList.GoodsList:
				
					//商品列表中的购买按钮样式
					$("#show_buy_button_style4").val(res.data);
					getCustom().find(".control-goods-buy-style>img").attr("src",__IMG(res.data));
					bindGoodsListData();
					
					break;
					
				case controlList.GoodsClassify:
					$("#show_buy_button_style4").val(res.data);
					getCustom().find(".control-goods-buy-style>img").attr("src",__IMG(res.data));
					bindGoodsClassifyData();
					break;
					
				case controlList.ShowCase:
					
					//橱窗
					getCustom().find("li:eq(" + index + ")").css({"background" : "url(" + __IMG(res.data) + ") no-repeat center/100% "}).children("div").css("visibility","hidden");
					bindShowCaseData();
					
					break;
			}
			showTip(res.message,"success");
		}else{
			showTip(res.message,"error");
		}
	});
}

/**
 * 绑定当前组件所需要的超链接数据，个别组件需要进行转换成json特殊处理
 * 创建时间：2017年8月3日 18:06:16
 * 更新时间：2017年8月7日 19:25:49
 */
function bindLink(){
	
	switch(getCustom().attr("data-custom-flag")){
	case controlList.NavHybrid:
		bindNavHybridData();
		break;
	case controlList.Footer:
		bindFooterData();
		break;
	case controlList.ImgAd:
		bindImgAdData();
		break;
	case controlList.ShowCase:
		bindShowCaseData();
		break;
	default:
		var href = new Array();
		$(".control-edit.link .selected").each(function(){
			href.push($(this).attr("data-href"));
		});
		getCustom().attr("data-href",href.toString());
		break;
	}
	
}

/**
 * 绑定商品分类json格式
 * 创建时间：2017年8月3日 14:59:50
 * 更新时间：2017年8月7日 19:26:40
 */
function bindGoodsClassifyData(){
	
	var html = "";
	var json = new Array();
	if($(".goods-classify-list>ul>li").length){
		
		getCustom().find("aside>ul").html("");
		$(".goods-classify-list>ul>li").each(function(i){
			var id = $(this).attr("data-classify-id");
			var name = $(this).attr("data-classify-name");
			var show_count = $(this).attr("data-show-count");
			html = "<li title='" + name + "'>" + name + "</li>";
			if(i==0) html = "<li class='selected' title='" + name + "'>" + name + "</li>";
			json.push({
				id : id,
				name : name,
				show_count : show_count,
				goods_show_buy_button : Number($("#show_buy_button").is(":checked")),
				goods_buy_button_style : $("input[name='buy_button_style']:checked").attr("data-buy-button-style"),
				goods_buy_button_src : $("input[name='buy_button_style']:checked").val()
			});
			getCustom().find("aside ul").append(html);
		});
		
	}else{
		
		//还原
		var temp_aside_count = 3;
		for(var i=0;i<temp_aside_count;i++){
			if(i==0) html += '<li class="selected">商品分类一</li>';
			else if(i%3==1) html += '<li>商品分类二</li>';
			else if(i%3==2) html += '<li>商品分类N</li>';
		}
		getCustom().find("aside ul").html(html);
		
	}
	getCustom().attr("data-goods-classify",JSON.stringify(json));
}

/**
 * 绑定图片广告数据
 * 创建时间：2017年8月4日 15:34:46
 * 更新时间：2017年8月7日 19:28:06
 */
function bindImgAdData(){
	
	var json = new Array();
	var adv_show_type = $("input[name='show_img_ad_type']:checked").val();
	$(".control-edit .add-img input[type='hidden']").each(function(i){
		json.push({ src : $(this).val(), adv_show_type : adv_show_type });
		if(adv_show_type == $Default.advShowType[0] && i == 0) return false;//单图只循环一次
	});
	$(".control-edit.link>.selected").each(function(i){
		json[i].href = !empty($(this).attr("data-href")) ? $(this).attr("data-href") : $Default.href;
		if(adv_show_type == $Default.advShowType[0] && i == 0) return false;//单图只循环一次
	});
	getCustom().attr("data-img-ad",JSON.stringify(json));
	//加载图片
	for(var i=0;i<json.length;i++){
		var src = _STATIC + "/custom_template/img/control_img_ad_single_default.png";
		if(!empty(json[i].src)) src = __IMG(json[i].src);
		getCustom().find("img:eq(" + i + ")").attr("src",src);
		getCustom().find("img").unbind("load");
	}
}

/**
 * 绑定图片导航数据json
 * 创建时间：2017年8月3日 16:48:25
 * 更新时间：2017年8月7日 19:28:11
 */
function bindNavHybridData(){

	var json = new Array();
	$(".js-nav-hybrid-text").each(function(){
		json.push({ text : $(this).val() });
	});

	$(".control-edit .add-img input[type='hidden']").each(function(i){
		json[i].src = $(this).val();
	});
	
	$(".control-edit.link>.selected").each(function(i){
		json[i].href = !empty($(this).attr("data-href")) ? $(this).attr("data-href") : $Default.href;
	});
	
	getCustom().attr("data-nav-hybrid",JSON.stringify(json));
}

/**
 * 绑定底部菜单数据json
 * 创建时间：2017年8月3日 17:49:16
 * 更新时间：2017年8月7日 19:28:21
 */
function bindFooterData(){
	var json = new Array();
	
	$(".js-footer-menu-name").each(function(){
		//菜单名称，颜色，选中颜色
		json.push({ menu_name : $(this).val(), color : $(".js-text-color").val(), color_hover : $(".js-text-color-hover").val() });
	});
	
	$(".control-edit.link>.selected").each(function(i){
		json[i].href = !empty($(this).attr("data-href")) ? $(this).attr("data-href") : $Default.href;
	});
	
	$(".js-img-footer input[type='hidden']").each(function(i){
		json[i].img_src = $(this).val();//未选择的图片
	});
	
	$(".js-img-footer-hover input[type='hidden']").each(function(i){
		json[i].img_src_hover = $(this).val();//选中的图片
	});
	getCustom().attr("data-footer",JSON.stringify(json));
}

/**
 * 获取优惠券右侧编辑栏代码
 * 创建时间：2017年8月10日 11:32:23 王永杰
 * 更新时间：2017年8月17日 10:54:55 王永杰
 * 1、样式选择、背景颜色编辑等功能
 */
function getCouponsHTML(){
	var coupons = getCustom().attr("data-coupons");
//	var color = $Default.couponsBgColor;
	var coupons_style1_checked = "";
	var coupons_style2_checked = "";
	var coupons_style3_checked = "";
	if(!empty(coupons)){
		coupons = eval("(" + coupons + ")");
//		color = coupons.bg_color;
		switch(parseInt(coupons.style)){
		case 1:
			coupons_style1_checked = 'checked="checked"';
			break;
		case 2:
			coupons_style2_checked = 'checked="checked"';
			break;
		case 3:
			coupons_style3_checked = 'checked="checked"';
			break;
		}
	}else{
		coupons_style1_checked = 'checked="checked"';
	}
	var html = '<div class="control-edit coupons">';
			html += '<label>样式：</label>';
			html += '<div>';
				html += '<input type="radio" value="1" id="coupons_style1" name="coupons_style" ' + coupons_style1_checked + '>&nbsp;';
				html += '<label for="coupons_style1" class="label-for">样式一</label>';
				html += '<input type="radio" value="2" id="coupons_style2" name="coupons_style" ' + coupons_style2_checked + '>&nbsp;';
				html += '<label for="coupons_style2" class="label-for">样式二</label>';
				html += '<input type="radio" value="3" id="coupons_style3" name="coupons_style" ' + coupons_style3_checked + '>&nbsp;';
				html += '<label for="coupons_style3" class="label-for">样式三</label>';
			html += '<p class="description" style="margin:10px 0 0 140px;">根据优惠券的数量，手机端显示样式也会进行调整。例如</p>';
			html += '</div>';
		html += '</div>';
		
//		html += '<div class="control-edit coupons-bg-color">';
//			html += '<label>背景颜色：</label>';
//			html += '<input type="color" class="input-common js-coupons-bg-color" value="' + color + '" data-jsclass="js-coupons-bg-color" data-default-value="' + $Default.couponsBgColor + '">';
//			html += '<i class="fa fa-refresh fr"></i>';
//		html += '</div>';
	return html;
}

/**
 * 绑定优惠券组件数据
 * 创建时间：2017年8月11日 09:14:37 王永杰
 * 更新时间：2017年8月17日 10:54:19 王永杰
 * 1、数据结构调整
 */
function bindCouponsData(){
	
	var json = {
		style : $("input[name='coupons_style']:checked").val()
//		bg_color : $(".js-coupons-bg-color").val()
	};
	getCustom().attr("data-coupons",JSON.stringify(json));
}


/**
 * 获取自定义模块代码
 * 创建时间：2017年8月16日 11:31:28
 * 1、选择自定义页面，排除当前自己的
 * 更新时间：2017年8月22日 18:39:01 王永杰
 * 1、排除当前自己的同时，排除自己被引用的
 * 2、如果当前编辑的模板被其他模板所引用，则不会出现
 */
function getCustomModuleHTML(){
	var custom_module = getCustom().attr("data-custom-module");
	if(!empty(custom_module)) custom_module = eval("(" + custom_module + ")");
	var html = '<div class="control-edit custom-module">';
			html += '<label>自定义模块：</label>';
			html += '<select class="select-common js-select-custom-module">';
			if(!empty(template_list) && template_list.length>0){
				var count = 0;
				for(var i=0;i<template_list.length;i++){
					var curr = template_list[i];
					var template_data = eval(curr.template_data);
					var flag = false;
					
					//如果当前模板被其他模板所引用了，则不能出现。防止递归出现的死循环
					for(var j=0;j<template_data.length;j++){
						
						if(template_data[j].control_name == controlList.CustomModule){
							var control_data = eval("(" + template_data[j].control_data +")");
							var data = eval("(" + control_data.custom_module + ")");
							if($("#hidden_id").val() == data.module_id){
								flag = true;
								break;
							}
						}
					}
					
					if(flag) continue;
					count++;
					
					if(!empty(custom_module) && custom_module.module_id == curr.id){
						html += '<option value="' + curr.id + '" data-module-name="' + curr.template_name + '" selected="selected">' + curr.template_name + "</option>";
					}else{
						html += '<option value="' + curr.id + '" data-module-name="' + curr.template_name + '">' + curr.template_name + "</option>";
					}
				}
				if(count == 0) html += '<option value="0">暂无可用的模块</option>';

			}else{
				html += '<option value="0">暂无可用的模块</option>';
			}
			html += '</select>';
			html += '<p class="description" style="margin:10px 0 0 140px;">选择自定义模块后，左侧实时预览暂不支持显示其包含的自定义模块数据（查询数据排除当前自定义模板）。<br/>';
				html += '<strong style="color:#FF5722;">备注：<br/>1、不能选择当前编辑的模块。</br>2、如果当前编辑的模板被其他模板所引用，则不会出现</strong>';
			html += '</p>';
		html += '</div>';
	return html;
}

/**
 * 绑定商品列表数据
 * 创建时间：2017年8月15日 19:19:01
 */
function bindGoodsListData(){
	
	var json = {
		goods_source : $(".js-goods-source").val(),																//商品来源
		goods_limit_count : $("input[name='showcount']:checked").val(),											//显示个数
		goods_list_type : $("input[name='list_type']:checked").val(),											//列表样式
		goods_show_goods_name : Number($("#show_goods_name").is(":checked")),									//显示商品名称
		goods_show_goods_price : Number($("#show_goods_price").is(":checked")),									//显示价格
		goods_show_buy_button : Number($("#show_buy_button").is(":checked")),									//显示购买按钮
		goods_buy_button_style : $("input[name='buy_button_style']:checked").attr("data-buy-button-style"),		//购买按钮样式选择
		goods_buy_button_src : $("input[name='buy_button_style']:checked").val()								//购买按钮图片路径
	};
	getCustom().attr("data-goods-list",JSON.stringify(json));
}

/**
 * 绑定自定义模块数据
 * 创建时间：2017年8月16日 12:20:46
 */
function bindCustomModuleData(){

	var json = {
		module_id : $(".js-select-custom-module").val(),
		module_name : $(".js-select-custom-module").find("option:checked").attr("data-module-name")
	};
	getCustom().attr("data-custom-module",JSON.stringify(json));
}

/**
 * 获取视频播放代码
 * 创建时间：2017年8月16日 20:23:00
 */
function getVideoHTML(){
	
	var video = getCustom().attr("data-video");
	var url = "";
	var padding = 0;
	if(!empty(video)){
		video = eval("(" + video + ")");
		url = video.url;
		padding = video.padding;
	}

	var html = '<div class="video-upload-pre">';
			html += '<pre>PHP默认上传限制为2MB，需要在php.ini配置文件中修改“post_max_size”和“upload_max_filesize”的大小。<br><b>(注：视频地址、视频上传文件填写一个即可！)</b></pre>';
		html += '</div>';
		html += getInputHTML("text",$cValue.videoUrl,url);
		html += '<div class="control-edit video-upload">';
			html += '<label>视频上传：</label>';
			html += '<div class="add-img">';
				html += '<span>';
					html += '<input class="input-file" name="file_upload" id="videoupload" type="file" onchange="fileUpload(this);">';
					html += '<input type="hidden" id="video_url" value="' + url + '">';
				html += '</span>';
				html += '<p id="text_video_url"><button>上传文件</button></p>';
				
			html += '</div>';
		html += '</div>';
	
		html += '<div class="control-edit video-url-info">';
			html += '<label>文件地址：</label>';
			html += '<span>' + url + "</span>";
		html += '</div>';
	
		html += getPaddingHTML(padding);
	return html;
}

/**
 * 文件上传（视频、音频）
 * 创建时间：2017年8月17日 11:26:27 王永杰
 */
function fileUpload(event) {
	
	var fileid = $(event).attr("id");
	var index = $(event).attr("data-index");
	var data = { 'file_path' : UPLOADVIDEO };
	var id = $(event).next().attr("id");
	var video = getCustom().find("video").attr("id");
	var dom = document.getElementById(fileid);
	var file =  dom.files[0];//File对象;
	var fileTypeArr = ['image/png','image/jpeg'];
	var flag = false;
	if(!empty(file)){
		for(var i=0;i<fileTypeArr.length;i++){
			if(file.type == fileTypeArr[i]){
				flag = true;
				break;
			}
		}
	}
	if(flag){
		showTip("文件类型不合法","warning");
	}else{
		uploadFile(fileid,data,function(res){
			if(res.code){
				$("#" + id).val(__IMG(res.data));
				$(".video-url-info span").text(res.data);
				var myPlayer = videojs(video);
				var videoUrl = __IMG(res.data);
				videojs(video).ready(function(){
					var myPlayer = this;
					myPlayer.src(videoUrl);
					myPlayer.load(videoUrl);
					myPlayer.play();
					bindVideoData();
				});
				showTip(res.message,"success");
			}else{
				showTip(res.message,"error");
			}
		});
	}
}

/**
 * 绑定视频组件数据
 * 创建时间：2017年8月17日 14:55:27
 */
function bindVideoData(){
	
	var json = {
		url : $("#video_url").val(),
		padding : $(".js-padding").val(),
		id : getCustom().find("video").attr("id")
	};
	getCustom().attr("data-video",JSON.stringify(json));
}

/**
 * 上下边距代码
 * 创建时间：2017年8月18日 09:51:19 王永杰
 * @returns {String} html
 */
function getPaddingHTML(value){

	var padding = 0;
	if(!empty(value)) padding = value;
	
	var html = '<div class="control-edit padding">';
		html += '<label>上下边距：</label>';
		html += '<input type="range" min="0" max="100" class="input-common js-padding" data-jsclass="js-padding" value="' + padding + '" data-default-value="0">';
		html += '<span>' + padding + 'px</span>';
		html += '<i class="fa fa-refresh fr"></i>';
	html += '</div>';
	return html;
}

/**
 * 橱窗编辑代码
 * 创建时间：2017年8月17日 19:21:17 王永杰
 * 
 * 1、显示方式：默认，3列
 * 2、图片间隙：保留，消除
 * 3、添加图片、链接地址
 * 4、是否显示文字
 * 5、文字内容
 * 6、上下边距
 */
function getShowCaseHTML(){

	//布局方式
	var two_column_checked = "";
	var three_column_checked = "";
	
	//图片间隙
	var keep_checked = "";
	var clear_checked = "";
	
	var padding = 0;
	
	//提示信息
	var pre_html = getShowCasePreHTML(2);
	
	var show_case = getCustom().attr("data-show-case");
	if(!empty(show_case)){
		
		show_case = eval("(" + show_case + ")");
		if(show_case.layout == 2){
			two_column_checked = 'checked="checked"';
		}else if(show_case.layout == 3){
			three_column_checked = 'checked="checked"';
			pre_html = getShowCasePreHTML(3);
		}
		
		if(show_case.clearance == 0) clear_checked = 'checked="checked"';
		else keep_checked = 'checked="checked"';
		
		padding = show_case.padding;
		
	}else{
		
		two_column_checked = 'checked="checked"';
		keep_checked = 'checked="checked"';
		
	}
	
	var html = pre_html;
		html += '<div class="control-edit show-case-layout">';
			html += '<label>布局方式：</label>';
			html += '<div>';
				html += '<input type="radio" value="2" id="show-case-two-column" name="show-case-layout" ' + two_column_checked + '>&nbsp;';
				html += '<label for="show-case-two-column" class="label-for">2列</label>';
				
				html += '<input type="radio" value="3" id="show-case-three-column" name="show-case-layout" ' + three_column_checked + '>&nbsp;';
				html += '<label for="show-case-three-column" class="label-for">3列</label>';
			html += '</div>';
		html += '</div>';

		html += '<div class="control-edit show-case-clearance">';
			html += '<label>图片间隙：</label>';
			html += '<div>';
				html += '<input type="radio" value="1" id="show-case-keep" name="show-case-clearance" ' + keep_checked + '>&nbsp;';
				html += '<label for="show-case-keep" class="label-for">保留</label>';
				
				html += '<input type="radio" value="0" id="show-case-clear" name="show-case-clearance" ' + clear_checked + '>&nbsp;';
				html += '<label for="show-case-clear" class="label-for">消除</label>';
			html += '</div>';
		html += '</div>';
		
		html += getPaddingHTML(padding);
		
		for(var i=0;i<3;i++){
			
			var show_text_checked = "";
			var hidden_text_checked = "";
			var text = "";
			var src = "";
			if(!empty(show_case)){
				var item = show_case.itemList[i];
				if(item.show_text == 1) show_text_checked = 'checked="checked"';
				else if(item.show_text == 0) hidden_text_checked = 'checked="checked"';
				text = item.text;
				src = item.src;
			}else{
				show_text_checked = 'checked="checked"';
			}
			
			html += '<div class="control-edit show-case-info">';
				html += '<div class="add-img">';
					if(!empty(src)){

						html += '<div class="img-block" style="display:block;"><img id="img_show_case_img' + i + '" src="' + __IMG(src) + '" style="max-height:100%;"></div>';
						html += '<span>';
							html += '<input class="input-file" name="file_upload" id="upload_show_case_img' + i + '" type="file" onchange="imgUpload(this);" data-index="' + i + '">';
							html += '<input type="hidden" id="show_case_img' + i + '" value = ' + src + '>';
						html += '</span>';
						
					}else{
						
						html += '<div class="img-block"><img id="img_show_case_img' + i + '"></div>';
						html += '<span>';
							html += '<input class="input-file" name="file_upload" id="upload_show_case_img' + i + '" type="file" onchange="imgUpload(this);" data-index="' + i + '">';
							html += '<input type="hidden" id="show_case_img' + i + '">';
						html += '</span>';
						html += '<p id="text_show_case_img' + i + '">添加图片<br><span>建议尺寸40*40</span></p>';
						
					}
				html += '</div>';
				
				html += '<div class="info" data-index="' + i + '">';
					html += getLinkHTML();

					html += '<div class="control-edit show-case-show-text">';
						html += '<label>是否显示文字：</label>';
						html += '<div>';
							html += '<input type="radio" ' + show_text_checked + ' value="1" id="show-case-show-text' + i + '" name="show-case-show-text' + i + '">&nbsp;';
							html += '<label for="show-case-show-text' + i + '" class="label-for">显示</label>';
							
							html += '<input type="radio" ' + hidden_text_checked + ' value="0" id="show-case-hidden-text' + i + '" name="show-case-show-text' + i + '">&nbsp;';
							html += '<label for="show-case-hidden-text' + i + '" class="label-for">隐藏</label>';
						html += '</div>';
					html += '</div>';
					
					if(!empty(hidden_text_checked)) html += '<div class="control-edit show-case-text" style="visibility:hidden;">';
					else html += '<div class="control-edit show-case-text">';
						html += '<label>显示文字：</label>';
						html += '<input type="text" class="input-common js-show-case-text" value="' + text + '">';
					html += '</div>';
				
				html += '</div>';
			html += '</div>';
		}
	return html;
}

/**
 * 绑定橱窗组件数据
 * 创建时间：2017年8月18日 09:41:31 王永杰
 */
function bindShowCaseData(){
	
	var itemList = new Array();
	$(".show-case-info").each(function(){
		var obj = {
			src : $(this).find("input[type='hidden']").val(),
			show_text : $(this).find("input[name^='show-case-show-text']:checked").val(),
			text : $(this).find(".js-show-case-text").val(),
			href : !empty($(this).find(".control-edit.link>.selected").attr("data-href")) ? $(this).find(".control-edit.link>.selected").attr("data-href") :  $Default.href
		};
		itemList.push(obj);
	});
	var json = {
		layout : $("input[name='show-case-layout']:checked").val(),
		clearance : $("input[name='show-case-clearance']:checked").val(),
		padding : $(".js-padding").val(),
		itemList : itemList
	};
	getCustom().attr("data-show-case",JSON.stringify(json));
	
}

/**
 * 根据所选择的布局方式，返回不同的图片尺寸提示信息
 * 创建时间：2017年8月18日 16:24:37
 * @param layout [2列,3列]
 */
function getShowCasePreHTML(layout){
	if(layout == 2) return '<div class="show-case-pre"><pre>长方形图片建议尺寸：<b style="font-size: 14px;">450px*580px</b><br/>正方形图片建议尺寸：<b style="font-size: 14px;">290px*280px</b></pre></div>';
	else if(layout == 3) return '<div class="show-case-pre"><pre>图片建议尺寸：<b style="font-size: 14px;">240px*420px</b></pre></div>';
}