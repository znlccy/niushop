/**
 * 可视化手机端模板插件[添加修改模板，公共核心函数]
 * 创建时间：2017年7月27日 15:17:35	王永杰
 * --------------------------------------------------------------
 * 更新时间：2017年8月9日 11:10:06	王永杰	Line:819
 * --------------------------------------------------------------
 * 更新时间：2017年8月17日 20:23:09	王永杰	Line:852
 * 1、新增视频组件、富文本组件
 * 2、优化优惠券编辑功能
 * 3、新增橱窗组件
 * 4、重做自定义模块
 * --------------------------------------------------------------
 * 更新时间：2017年8月18日 16:49:13	王永杰	Line:943
 * 1、完成橱窗组件
 * 2、整理代码
 * --------------------------------------------------------------
 * 更新时间：2017年8月19日 17:19:44	王永杰	Line:957
 * 1、商品列表组件新增购买按钮样式调整
 * --------------------------------------------------------------
 * 更新时间：2017年8月22日 19:49:50	王永杰	Line:1014
 * 1、解决跳转链接加载问题
 * 2、优惠券布局完成
 * 3、轮播图注释掉了
 * 4、解决富文本加载时，出现多个
 * 5、上传的图片可以删除了
 * 6、底部菜单可以出现多次，依次从下至上排列
 * 7、自定义模块已完成
 * --------------------------------------------------------------
 * 更新时间：2017年8月23日 09:39:35	王永杰	Line:1042
 * 1、优化富文本组件
 * 2、整理代码
 */


var align_array = ["left","center","right"];//居中方式数据

var link_arr = new Array();
link_arr[__URL(APPMAIN + '/index/index')] = '店铺首页';
link_arr[__URL(APPMAIN + '/goods/cart')] = '购物车';
link_arr[__URL(APPMAIN + '/member/index')] = '会员中心';
link_arr[__URL(APPMAIN + '/goods/goodsclassificationlist')] = '商品分类';
link_arr[__URL(APPMAIN + '/goods/integralcenter')] = '积分中心';
link_arr[__URL(APPMAIN + '/index/discount')] = '限时折扣';
link_arr[__URL(APPMAIN + '/goods/brandlist')] = '品牌专区';
/**
 * 组件集合
 * 创建时间：2017年8月7日 18:08:27
 * 更新时间：2017年8月7日 18:08:31
 * [标题、轮播广告、文本导航、图片导航、搜索、商品列表、商品分类、公告、图片广告、底部、富文本、辅助线、辅助空白]
 * --------------------------------------------------------------
 * 更新时间：2017年8月18日 16:42:20 王永杰
 * 1、新增[视频组件、富文本组件、橱窗组件]
 * 2、重做自定义模块组件
 * --------------------------------------------------------------
 */
var controlList = new Array();
$(".plug-in li").each(function(){
	controlList[$(this).attr("data-control-name")] = $(this).attr("data-control-name");
});
//console.log(controlList);

/**
 * 默认数据
 * 创建时间：2017年8月4日 10:09:29
 * 更新时间：2017年8月7日 17:56:48
 */
var $Default = {

	searchTextColor : "#333333",							//搜索框文字颜色
	searchPlaceholder : "商品搜索",							//搜索框输入框提示内容
	searchBgColor : "#ffffff",								//搜索框背景颜色
	searchInputBgColor : "#f4f4f4",							//搜索框输入框背景颜色
	carouselInterval : 2000,								//轮播停留间隔时间
	
	goodsLimitCount :[6,12,18],								//商品显示个数
	goodsListType : [1,2],									//列表样式 大图：1,小图：2
	
	titleTextColor : "#333333",								//标题文字颜色
	titleBgColor : "#ffffff",								//标题背景颜色
	
	textSize : [12,13,14,15,16,17,18,19,20],				//导航文字大小
	navTextColor : "#333333",								//导航文字颜色
	
	noticeText : "预览暂不支持显示公告内容数据",						//公告内容
	noticeTextColor : "#ff9900",							//公告文字颜色
	noticeBgColor : "#FFFFCC",								//公告文字颜色
	
	advShowType :[1,2],										//显示方式  1 单图广告
	
	footerItemCount : 4,									//底部菜单个数
	navHyBridItemCount : 4,									//图片导航项的数量
	
	auxiliaryLineBorderColor:"#e5e5e5",						//辅助线颜色
	href : "javascript:;",									//跳转链接,
	textColor : "#333333",									//公共文字颜色
	textAlign : [1,2,3], 									//显示位置 居左1  居中2 居右 3
	textColorHover : "#0072D2",								//搜索框文字选中颜色
	borderColor : "#e5e5e5",								//边框颜色
	auxiliaryBlankHeightMin : 10,							//辅助空白最小高度
	auxiliaryBlankHeightMax : 100,							//辅助空白最大高度
	couponsBgColor : "#FF8040",								//优惠券默认背景颜色
};

window.onresize = function(){
	//动态改变右侧编辑信息的位置
	draggableElementClick(getCustom().removeAttr("data-is-show"),"select");
}

$(function() {

	//预先加载百度编辑器
	preloadBaiDuEditor();
	
	/**
	 * 编辑模板名称
	 * 创建时间：2017年8月14日 12:21:09
	 * 1、根据模板名称的长度调整位置（居中）
	 */
	$(".custom-template header").click(function(){
		draggableElementClick(this,"select");
		var h4 = $(".custom-template header>h4");
		var width = h4.width()>170 ? 170 : h4.width();
		h4.css("margin","0 0 0 -" + (width/2) + "px");
	});
	
	//初始化时打开模块名称，后边如果有组件，则会自动隐藏
	$(".custom-template header").click();
	
	//初始化分辨率数据
	initCustomResolution();
	
	//从数据库中加载数据
	loadData();
	
	/**
	 * 分辨率选择[预览]
	 * 创建时间：2017年7月31日 11:34:30
	 * 更新时间：2017年8月4日 20:48:13
	 * 更新时间：2017年8月18日 16:44:38 王永杰
	 * 1、功能暂时隐藏
	 */
	$(".custom-resolution button").click(function(){
		var width = parseInt($(this).attr("data-width"));
		$(".custom-template").removeClass().addClass("custom-template").addClass("w"+width);
		if(getCustom().attr("data-is-show")) draggableElementClick(getCustom().removeAttr("data-is-show"),"select");
	});
	
	/**
	 * 绑定右侧弹出层
	 * 创建时间：2017年8月7日 17:54:52
	 * 更新时间：2017年8月7日 17:54:54
	 */
	$(".draggable-element").live("click",function(){
		draggableElementClick(this,"select");
	});
	
	/**
	 * 保存数据
	 * 创建时间：2017年7月31日 11:34:30
	 * 更新时间：2017年8月4日 20:48:31
	 * 更新时间：2017年8月18日 16:37:35 王永杰
	 * 1、完全自定义模板，可以设置模板名称
	 */
	$(".js-save").click(function(){
		if(validation()){
			var request_url = ADMINMAIN + "/config/addcustomtemplate";
			if(parseInt($("#hidden_id").val()) > 0) request_url = ADMINMAIN + "/config/updatecustomtemplate";
			$.ajax({
				type : "post",
				url : __URL(request_url),
				data : { "id" : $("#hidden_id").val(), "template_name" : $(".custom-template header>h4").attr("data-custom-template-name"), "template_data" : getTemplateData() },
				success : function(res){
					if(res.code>0){
						showTip(res.message,"success");
						if(parseInt($("#hidden_id").val()) == 0){
							setTimeout(function(){
								location.href = __URL(ADMINMAIN + "/config/customtemplateList");
							},1500);
						}
					}else{
						showTip(res.message,"error");
					}
				}
			});
		}
	});
});

/**
 * 从数据库中加载数据
 * 创建时间：2017年8月8日 10:35:38
 * 更新时间：2017年8月16日 10:07:31
 * --------------------------------------------------------------
 * 更新时间：2017年8月18日 16:45:05 王永杰
 * 1、新增[视频组件、富文本组件、橱窗组件]
 */
function loadData(){
	
	if(!empty(template_data)){
		
		for(var i=0;i<template_data.length;i++){
			
			var control_name = template_data[i].control_name;//组件名称
			var control_data = eval("(" + template_data[i].control_data + ")");//数据
			var self = null;//当前组件
			var additional_attr = "";//附加属性
			if(control_name == controlList.GoodsSearch){
				
				//商品搜索
				additional_attr = 'data-font-size="' + control_data.font_size + '"';
				additional_attr += ' data-text-color="' + control_data.text_color + '"';
				additional_attr += ' data-bg-color="' + control_data.bg_color + '"';
				additional_attr += ' data-input-bg-color="' + control_data.input_bg_color + '"';
				additional_attr += ' data-placeholder="' + control_data.placeholder + '"';
				additional_attr += ' style="background-color:' + control_data.bg_color + ';"';
				self = $(".plug-in li[data-control-name='" + controlList.GoodsSearch + "']").attr("data-additional-attr",additional_attr);
				
			}else if(control_name == controlList.Carousel){
				
				//轮播图
				additional_attr = 'data-carousel-interval="' + control_data.carousel_interval + '"';
				additional_attr += ' id="carouselControl"';//附加属性
				self = $(".plug-in li[data-control-name='" + controlList.Carousel + "']").attr("data-additional-attr",additional_attr);
				
			}else if(control_name == controlList.GoodsList){
				
				//商品列表
				additional_attr = "data-goods-list='" + control_data.goods_list + "'";
				self = $(".plug-in li[data-control-name='" + controlList.GoodsList + "']");
				
			}else if(control_name == controlList.Title){
				
				//标题
				additional_attr = 'data-title="' + control_data.title + '"';
				additional_attr += ' data-subtitle="' + control_data.subtitle + '"';
				additional_attr += ' data-text-align="' + control_data.text_align + '"';
				additional_attr += ' data-text-color="' + control_data.text_color + '"';
				additional_attr += ' data-bg-color="' + control_data.bg_color + '"';
				additional_attr += ' data-href="' + control_data.href + '"';
				additional_attr += ' style="background-color:' + control_data.bg_color + '"';
				self = $(".plug-in li[data-control-name='" + controlList.Title + "']");
				
			}else if(control_name == controlList.AuxiliaryLine){
				
				//辅助线
				additional_attr = 'data-border-color="' + control_data.border_color + '"';
				self = $(".plug-in li[data-control-name='" + controlList.AuxiliaryLine + "']");
				
			}else if(control_name == controlList.AuxiliaryBlank){

				//辅助空白
				additional_attr = 'data-height="' + control_data.height + '"';
				additional_attr += ' style="height:' + control_data.height + 'px;"';
				self = $(".plug-in li[data-control-name='" + controlList.AuxiliaryBlank + "']");
				
			}else if(control_name == controlList.Notice){
				
				//公告
				additional_attr = 'data-bg-color="' + control_data.bg_color + '"';
				additional_attr += ' data-text-color="' + control_data.text_color + '"';
				additional_attr += ' style="background-color:' + control_data.bg_color + '"';
				self = $(".plug-in li[data-control-name='" + controlList.Notice + "']");
				
			}else if(control_name == controlList.ImgAd){
				
				//图片广告
				additional_attr = 'id="carouselImgAd"';
				additional_attr += " data-img-ad='" + control_data.img_ad + "'";
				self = $(".plug-in li[data-control-name='" + controlList.ImgAd + "']");
				
			}else if(control_name == controlList.NavText){
				
				//文本导航
				additional_attr = 'data-nav-text="' + control_data.nav_text + '"';
				additional_attr += ' data-font-size="' + control_data.font_size + '"';
				additional_attr += ' data-text-color="' + control_data.text_color + '"';
				additional_attr += ' data-href="' + control_data.href + '"';
				self = $(".plug-in li[data-control-name='" + controlList.NavText + "']");
				
			}else if(control_name == controlList.NavHybrid){
				
				//图片导航
				additional_attr = "data-nav-hybrid='" + control_data.nav_hybrid + "'";
				self = $(".plug-in li[data-control-name='" + controlList.NavHybrid + "']");
				
			}else if(control_name == controlList.GoodsClassify){
				
				//商品分类
				additional_attr = "data-goods-classify='" + control_data.goods_classify + "'";
				self = $(".plug-in li[data-control-name='" + controlList.GoodsClassify + "']");
				
			}else if(control_name == controlList.Footer){
				
				//底部菜单
				additional_attr = "data-footer='" + control_data.footer + "'";
				self = $(".plug-in li[data-control-name='" + controlList.Footer + "']");
				
			}else if(control_name == controlList.RichText){
				
				//富文本
				additional_attr = "data-rich-text='" + control_data.rich_text + "'";
				self = $(".plug-in li[data-control-name='" + controlList.RichText + "']");
				
			}else if(control_name == controlList.CustomModule){
				
				//自定义模块
				additional_attr = "data-custom-module='" + control_data.custom_module + "'";
				self = $(".plug-in li[data-control-name='" + controlList.CustomModule + "']");
				
			}else if(control_name == controlList.Coupons){
				
				//优惠券
				additional_attr = "data-coupons='" + control_data.coupons + "'";
				self = $(".plug-in li[data-control-name='" + controlList.Coupons + "']");
				
			}else if(control_name == controlList.Video){
				
				//视频
				additional_attr = "data-video='" + control_data.video + "'";
				var video = eval("(" + control_data.video + ")");
				additional_attr += " style='padding:" + video.padding + "px 0;'";
				self = $(".plug-in li[data-control-name='" + controlList.Video + "']");
				
			}else if(control_name == controlList.ShowCase){
				
				//橱窗
				additional_attr = "data-show-case='" + control_data.show_case + "'";
				var show_case = eval("(" + control_data.show_case + ")");
				additional_attr += " style='padding:" + show_case.padding + "px 0;'";
				self = $(".plug-in li[data-control-name='" + controlList.ShowCase + "']");
				
			}
			
			if(!empty(self)){
				
				additional_attr += " data-sort=" + template_data[i].sort;
				addControl(self.attr("data-additional-attr",additional_attr),control_data);
			}
		}
	}
}

/**
 * 初始化分辨率数据
 * 创建时间：2017年7月27日 11:40:39
 * 更新时间：2017年8月4日 20:47:59
 */
function initCustomResolution(){
	$(".custom-resolution button").each(function(){
		$(this).text($(this).attr("data-width") + "*" + $(".custom-template").height());
	})
}

/**
 * 获取当前编辑的组件
 * 创建时间：2017年7月28日 14:24:34
 * 更新时间：2017年8月4日 20:48:44
 */
function getCustom(){
	return $(".custom-main>.selected");
}

/**
 * 非空判断
 * 创建时间：2017年7月28日 18:05:26
 * 更新时间：2017年8月7日 18:40:43
 * @param s
 * @returns {Boolean}
 */
function empty(s){
	return (s == undefined || s == "") ? 1 : 0;
}

/**
 * 验证域名
 * 创建时间：2017年7月31日 19:47:09
 * 更新时间：2017年8月4日 20:49:55
 * @param str
 * @returns boolean
 */
function validateDomainName(str){
	var strRegex = "^((https|http|ftp|rtsp|mms)?://)"
	+ "?(([0-9a-z_!~*'().&=+$%-]+: )?[0-9a-z_!~*'().&=+$%-]+@)?"
	+ "(([0-9]{1,3}\.){3}[0-9]{1,3}"
	+ "|"
	+ "([0-9a-z_!~*'()-]+\.)*"
	+ "([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\."
	+ "[a-z]{2,6})"
	+ "(:[0-9]{1,4})?"
	+ "((/?)|"
	+ "(/[0-9a-z_!~*'().;?:@&=+$,%#-]+)+/?)$";
	return new RegExp(strRegex).test(str);
}

/**
 * 获取橱窗数据
 * 创建时间：2017年8月18日 16:36:22
 * @param event_obj
 * @returns
 */
function getShowCaseData(event_obj){
	var obj = { show_case : event_obj.attr("data-show-case") };
	return JSON.stringify(obj);
}

/**
 * 获取视频组件数据
 * 创建时间：2017年8月17日 15:06:45
 * @param event_obj
 */
function getVideoData(event_obj){
	var obj = { video : event_obj.attr("data-video") };
	return JSON.stringify(obj);
}

/**
 * 获取模板数据(json)
 * 搜索框、轮播图、商品列表、标题、辅助线、辅助空白、公告、图片广告、文本导航、图片导航、商品分类、底部菜单。每个组件都需要要排序
 * 创建时间：2017年7月31日 12:06:49
 * 更新时间：2017年8月7日 17:51:49
 */
function getTemplateData(){
	var data = new Array();
	$(".custom-main .draggable-element").each(function(i){
		
		var obj = new Object();
		obj.sort = (i+1);
		//如果有底部菜单，则永远在最后
		obj.control_name = $(this).attr("data-custom-flag");
		obj.control_data = eval("get" + obj.control_name + "Data($(this))");
		data.push(obj);
		
	})
//	console.log(data);
	return JSON.stringify(data);
}

/**
 * 优惠券
 */
function getCouponsData(event_obj){
	var obj = { coupons : event_obj.attr("data-coupons") };
	return JSON.stringify(obj);
}

/**
 * 辅助空白
 * 创建时间：2017年8月7日 17:51:15
 * 更新时间：2017年8月7日 17:51:20
 */
function getAuxiliaryBlankData(event_obj){
	var obj = { height : $Default.auxiliaryBlankHeightMin };
	if(!empty(event_obj.attr("data-height"))) obj.height = event_obj.attr("data-height");
	return JSON.stringify(obj);
}

/**
 * 辅助线
 * 创建时间：2017年8月5日 14:25:55
 * 更新时间：2017年8月5日 14:47:18
 */
function getAuxiliaryLineData(event_obj){
	var obj = { border_color : $Default.auxiliaryLineBorderColor };
	if(!empty(event_obj.attr("data-border-color"))) obj.border_color = event_obj.attr("data-border-color");
	return JSON.stringify(obj);
}

/**
 * 富文本
 * [数据不能为空]
 * 创建时间：2017年8月7日 17:47:27
 * 更新时间：2017年8月7日 17:50:03
 */
function getRichTextData(event_obj){
	var rich_text = event_obj.attr("data-rich-text");
	if(!empty(rich_text)) rich_text = rich_text.toString().replace("'",'"');//replace(/(\n)/g, "").replace(/(\t)/g, "").replace(/(\r)/g, "").replace(/\s*/g, "")
	var obj = { rich_text : rich_text };
	return JSON.stringify(obj);
}

/**
 * 自定义模块
 * 创建时间：2017年8月16日 12:00:36
 *
 */
function getCustomModuleData (event_obj){
	var obj = { custom_module : event_obj.attr("data-custom-module") };
	return JSON.stringify(obj);
}

/**
 * 底部菜单
 * [数据不能为空]
 * 创建时间：2017年8月5日 10:52:04
 * 更新时间：2017年8月7日 17:44:59
 * menu_name : 菜单名称,color:文字颜色,color_hover:文字选中颜色,href:链接地址,img_url:未选中的图片，img_url_hover:选中后的图片
 * json格式：[{"menu_name":"1","color":"#333333","color_hover":"#0072d2","href":"javascript:;","img_url":"","img_url_hover":""}]
 */
function getFooterData(event_obj){
	var obj = { footer : event_obj.attr("data-footer") };
	return JSON.stringify(obj);
}

/**
 * 商品分类
 * [数据不能为空]
 * 创建时间：2017年8月3日 17:16:55
 * 更新时间：2017年8月7日 17:44:23
 * id：分类id，show_count：显示数量
 * json格式：[{"id":"8","name":"C","show_count":"10"},{"id":"4","name":"A","show_count":"10"}]
 */
function getGoodsClassifyData(event_obj){
	var obj = { goods_classify : event_obj.attr("data-goods-classify") };
	return JSON.stringify(obj);
}

/**
 * 图片导航
 * [数据不能为空]
 * 创建时间：2017年8月3日 17:15:57
 * 更新时间：2017年8月4日 20:42:15
 * text:文字,src:图片路径，href:链接地址
 * json格式：[{"text":"","src":"","href":"javascript:;"},{"text":"","src":"","href":"javascript:;"}]
 */
function getNavHybridData(event_obj){
	var obj = { nav_hybrid : event_obj.attr("data-nav-hybrid") };
	return JSON.stringify(obj);
}

/**
 * 图片广告数据
 * [数据不能为空]
 * 创建时间：2017年8月4日 15:51:11
 * 更新时间：2017年8月4日 20:40:27
 * src:图片路径，adv_show_type：显示方式:单图 1,多图 2，href:链接地址
 * json格式：[{"src":"","adv_show_type":"2","href":"www.baidu.com"},{"src":"","adv_show_type":"2","href":"javascript:;"}]
 */
function getImgAdData(event_obj){
	var obj = { img_ad : event_obj.attr("data-img-ad") };
	return JSON.stringify(obj);
}

/**
 * 公告组件数据
 * 创建时间：2017年8月4日 15:51:16
 * 更新时间：2017年8月4日 20:33:11
 */
function getNoticeData(event_obj){
	var obj = {
		text_color : $Default.noticeTextColor,			//文字颜色
		bg_color : $Default.noticeBgColor				//背景颜色
	};
	if(!empty(event_obj.attr("data-text-color"))) obj.text_color = event_obj.attr("data-text-color");
	if(!empty(event_obj.attr("data-bg-color"))) obj.bg_color = event_obj.attr("data-bg-color");
	return JSON.stringify(obj);
}

/**
 * 文本导航组件数据
 * 创建时间：2017年8月4日 15:51:19
 * 更新时间：2017年8月4日 20:33:08
 */
function getNavTextData(event_obj){
	var obj = {
		nav_text : event_obj.attr("data-nav-text"),		//导航名称
		font_size : $Default.textSize[2],				//文字大小
		text_color : $Default.navTextColor,				//文字颜色
		href : $Default.href							//跳转链接
	};
	if(!empty(event_obj.attr("data-font-size"))) obj.font_size = event_obj.attr("data-font-size");
	if(!empty(event_obj.attr("data-text-color"))) obj.text_color = event_obj.attr("data-text-color");
	if(!empty(event_obj.attr("data-href"))) obj.href  = event_obj.attr("data-href");
	return JSON.stringify(obj);
}

/**
 * 标题组件数据
 * 创建时间：2017年8月4日 10:43:52
 * 更新时间：2017年8月4日 20:18:48
 */
function getTitleData(event_obj){
	var obj = {
		title : event_obj.attr("data-title"),			//标题
		subtitle : "",									//副标题
		text_align : $Default.textAlign[0],				//显示方式，左中右
		href : $Default.href,							//链接地址
		text_color : $Default.titleTextColor,			//文字颜色
		bg_color : $Default.titleBgColor				//背景颜色
	};
	if(!empty(event_obj.attr("data-subtitle"))) obj.subtitle = event_obj.attr("data-subtitle");
	if(!empty(event_obj.attr("data-text-align"))) obj.text_align = event_obj.attr("data-text-align");
	if(!empty(event_obj.attr("data-href"))) obj.href  = event_obj.attr("data-href");
	if(!empty(event_obj.attr("data-text-color"))) obj.text_color = event_obj.attr("data-text-color");
	if(!empty(event_obj.attr("data-bg-color"))) obj.bg_color = event_obj.attr("data-bg-color");
	return JSON.stringify(obj);
}

/**
 * 获取当前商品列表组件数据
 * 创建时间：2017年8月4日 15:51:24
 * 更新时间：2017年8月4日 20:14:52
 * 更新时间：2017年8月16日 10:06:02 数据结构重新调整，更容易后期维护
 * 备注：商品简介暂时取消配置，先将其注释掉
 * @param event_obj
 */
function getGoodsListData(event_obj){
	var obj = { goods_list : event_obj.attr("data-goods-list") };
	return JSON.stringify(obj);
}

/**
 * 轮播图数据
 * 创建时间：2017年8月2日 16:02:24
 * 更新时间：2017年8月4日 20:14:44
 * @returns
 */
function getCarouselData(event_obj){
	var obj = { carousel_interval : $Default.carouselInterval };//轮播图间隔停留时间
	if(!empty(event_obj.attr("data-carousel-interval"))) obj.carousel_interval = event_obj.attr("data-carousel-interval");
	return JSON.stringify(obj);
}

/**
 * 获取搜索组件的数据
 * 文字大小、文字颜色、默认提示内容、背景颜色、输入框背景颜色（json）
 * 创建时间：2017年7月31日 14:06:27
 * 更新时间：2017年8月4日 20:14:26
 */
function getGoodsSearchData(event_obj){
	var obj = {
		font_size : $Default.textSize[2],					//文字大小
		text_color : $Default.searchTextColor,				//文字颜色
		placeholder : $Default.searchPlaceholder,			//默认提示内容
		bg_color : $Default.searchBgColor,					//背景颜色
		input_bg_color : $Default.searchInputBgColor		//输入框背景颜色
	};
	if(!empty(event_obj.attr("data-font-size"))) obj.font_size = event_obj.attr("data-font-size");
	if(!empty(event_obj.attr("data-text-color"))) obj.text_color = event_obj.attr("data-text-color");
	if(!empty(event_obj.attr("data-placeholder"))) obj.placeholder = event_obj.attr("data-placeholder");
	if(!empty(event_obj.attr("data-bg-color"))) obj.bg_color = event_obj.attr("data-bg-color");
	if(!empty(event_obj.attr("data-input-bg-color"))) obj.input_bg_color = event_obj.attr("data-input-bg-color");
	return JSON.stringify(obj);
}

/**
 * 绑定右侧弹出层
 * 创建时间：2017年8月1日 16:32:10
 * 更新时间：2017年8月9日 11:01:13
 * --------------------------------------------------------------
 * 更新时间：2017年8月18日 16:46:46 王永杰
 * 1、部分组件由于不需要验证，所以每次打开都要进行绑定数据
 * 
 * @param obj 当前拖拽对象,status ：add:添加,select：选择,validation：验证
 */
function draggableElementClick(obj,status){
	var self = $(obj);
	if(!empty(self.attr("data-custom-flag"))){
		//选中当前点击的组件，清除其他组件的选中样式及状态
		$(".custom-template header").removeAttr("data-is-show");
		if(self.attr("data-custom-flag") == "CustomTemplateName"){
			$(".custom-main>div").removeAttr("data-is-show").removeClass("selected");
		}else{
			self.addClass("selected").siblings().removeAttr("data-is-show").removeClass("selected");
		}
		switch(status){
		case "add":
			//每次添加跳转到末尾
//			$("html,body").animate({
//				scrollTop : self.attr("data-scroll-top")
//			},300);
			break;
		case "select":
			//不进行任何操作
			break;
		case "validation":
			$(window).scrollTop(self.attr("data-scroll-top") - self.height());
			break;
		}
		//如果选择的组件已经打开，无需重新创建
		if($(self).attr("data-is-show") == 1) return;
		
		$.pt({
			target : self,
			position : 'r',
			align : 't',
			width : 500,
			autoClose : false,
			content : $edit.init(self.attr("data-custom-flag")),
			open : function(r){
				self.attr("data-is-show",1);//显示标识
				$("input[type='file']").attr("title"," ");//清空文件上传的提示信息
				$("#richText").hide();//隐藏百度编辑器
				$(".pt-left .cont").css("min-height","");//还原右侧编辑栏的高度
				$(".pt-left").css("left",($(".pt-left").offset().left + 10));
				switch(self.attr("data-custom-flag")){
					
					case controlList.ImgAd:
						
						//图片广告
						//从数据库中读取
						if(!empty(template_data)){
							//重置id，不然无法进行轮播
							var img_ad = eval(self.attr("data-img-ad"));
							if(!empty(img_ad)){
								var new_id = self.attr("id") + $(".custom-main [data-custom-flag='" + controlList.ImgAd + "']").length;
								self.attr("id",new_id).find("a").attr("href","#" + new_id);
								if(img_ad[0].adv_show_type == 2){
									
									self.addClass("slide");//多图广告添加轮播
									$('.carousel').carousel();//轮播停留时间
									
								}else{
									
									//需要满足两个组件条件
									if(template_data.length>1 && empty(self.attr("data-is-update"))){
										//单图广告高度不固定，导致右侧编辑栏位置会计算错误，需要重新计算，只执行一次（取最后一个）
										self.find("img").load(function(){
											var top = $(".draggable-element:last").offset().top;
											$(".pt-left").css("top",top);
											self.attr("data-is-update",1);
										});
									}
									
								}
							}
						}
						
						break;
						
					case controlList.NavHybrid:
						
						//图片导航：调整宽度
						self.find("li").css("width",parseInt(100/$Default.navHyBridItemCount) + "%");
						
						break;
						
					case controlList.Footer:
						
						//底部菜单：调整宽度
						self.find("li").css("width",parseInt(100/$Default.footerItemCount) + "%");
						
						break;
						
					case controlList.RichText :
						
						//调整百度编辑器的位置、以及显示
						$(".pt.pt-left .cont").css("min-height","604px");//字数统计23px，边框2px，工具栏79，正文500px
						var l = $(".pt.pt-left .cont").offset().left + 10;
						var t = self.offset().top + 10;
						$("#richText").css({ position : "absolute", top : t, left : l, display : "block", zIndex : 1001 });
						break;
						
					case controlList.GoodsList:
						
						//第一次打开商品列表，默认加载数据
						bindGoodsListData();
						
						break;
						
					case controlList.Title:
						
						getCustom().attr("data-title",$(".js-title").val());
						//副标题可以为空，可以不用设置
						
						break;
						
					case controlList.NavText:
						
						getCustom().attr("data-nav-text",$(".js-nav-text").val());
						
						break;
						
					case controlList.CustomModule:
						
						//自定义模块
						bindCustomModuleData();

						//如果自定义模块只有一个，则左侧实时预览界面，要更新文字内容
						if($(".js-select-custom-module option").length == 1) getCustom().find("article>p").text($(".js-select-custom-module option").text());
						
						break;
						
					case controlList.Coupons:
						
						//优惠券
						bindCouponsData();
						
						break;
						
					case controlList.Video:
						
						bindVideoData();
						
						break;
						
					case controlList.ShowCase:
						
						bindShowCaseData();
						
						break;
				}
			}
		});
	}
}

/**
 * 验证组件
 * 创建时间：2017年8月7日 10:41:06
 * 更新时间：2017年8月7日 17:23:17
 * --------------------------------------------------------------
 * 更新时间：2017年8月18日 16:47:48 王永杰
 * 1、新增[视频组件、富文本组件、优惠券组件、橱窗组件]的验证
 * 
 */
function validation(){
	var control = eval(getTemplateData());
	var flag = false;//验证标识：true，失败，false：成功
	
	if(empty($(".custom-template header>h4").attr("data-custom-template-name"))){
		showTip("模板名称不能为空","warning");
		draggableElementClick($(".custom-template header"),"validation");
		$(".js-custom-template-name").focus();
		return false;
	}
	
	if(!empty(control) && control.length){
		
		for(var i=0;i<control.length;i++){
			
			var data = eval("(" + control[i].control_data + ")");
			if(control[i].control_name == controlList.GoodsList){
				
				//商品列表
				var goods_list = eval("(" + data.goods_list + ")");
				if(goods_list.goods_source == 0){
					flag = true;
					$(".js-goods-source").focus();
					showTip("没有发现商品来源，请先去添加商品分类","warning");
				}
				if(goods_list.goods_buy_button_style == 4){
					if(empty(goods_list.goods_buy_button_src)){
						flag = true;
						showTip("请上传自定义的购买按钮图片","warning");
					}
				}
				
			}else if(control[i].control_name == controlList.Title){
				
				//标题
				if(empty(data.title)){
					flag = true;
					$("." + $cValue.title.input_class).focus();
					showTip($cValue.title.name + "不能为空","warning");
				}
				
			}else if(control[i].control_name == controlList.NavText){
				
				//文本导航
				if(empty(data.nav_text)){
					flag = true;
					$(".js-nav-text").focus();
					showTip($cValue.navText.name + "不能为空","warning");
				}
				
			}else if(control[i].control_name == controlList.ImgAd){
				
				//图片广告
				var img_ad = eval(data.img_ad);
				var adv_show_type = 1;
				var img_count = 0;
				if(img_ad && img_ad.length>0){
					
					adv_show_type = img_ad[0].adv_show_type;
					for(var j=0;j<img_ad.length;j++){
						if(!empty(img_ad[j].src)) img_count++;
					}
					if(adv_show_type == 1 && img_count < 1){
						flag = true;
						showTip("至少上传一张图片","warning");
					}else if(adv_show_type == 2 && img_count < 2){
						flag = true;
						showTip("至少上传两张图片","warning");
					}
					
				}else{
					
					flag = true;
					showTip("至少上传一张图片","warning");
					
				}
				
			}else if(control[i].control_name == controlList.NavHybrid){
				
				//图片导航
				var nav_hybrid = eval(data.nav_hybrid);
				var nav_hybrid_text_count = 0;
				var nav_hybrid_src_count = 0;
				if(!empty(nav_hybrid) && nav_hybrid.length>0){
				
					for(var j=0;j<nav_hybrid.length;j++){
						if(!empty(nav_hybrid[j].text)) nav_hybrid_text_count++;
						if(!empty(nav_hybrid[j].src)) nav_hybrid_src_count++;
					}
					if(nav_hybrid_text_count < nav_hybrid.length && nav_hybrid_src_count < nav_hybrid.length){
						flag = true;
						showTip("图片导航不能为空","warning");
					}
					
				}else{
					
					flag = true;
					showTip("图片导航不能为空","warning");
					
				}
				
			}else if(control[i].control_name == controlList.GoodsClassify){
				
				//商品分类
				var goods_classify = eval(data.goods_classify);
				if(!empty(goods_classify)){
					
					if(goods_classify[0].goods_buy_button_style == 4){
						if(empty(goods_classify[0].goods_buy_button_src)){
							flag = true;
							showTip("请上传自定义的购买按钮图片","warning");
						}
					}
					
				}else{

					flag = true;
					showTip("至少选择一个商品分类","warning");
					
				}
				
			}else if(control[i].control_name == controlList.Footer){
				
				//底部菜单
				var footer = eval(data.footer);
				var footer_menu_name_count = 0;
				var footer_menu_src_count = 0;
				if(!empty(footer) && footer.length>0){
					
					for(var j=0;j<footer.length;j++){
						if(!empty(footer[j].menu_name)) footer_menu_name_count++;
						if(!empty(footer[j].img_src)) footer_menu_src_count++;
					}
					if(footer_menu_name_count < footer.length && footer_menu_src_count < footer.length){
						flag = true;
						showTip("底部菜单不能为空","warning");
					}
					
				}else{
					
					flag = true;
					showTip("底部菜单不能为空","warning");
					
				}
				
			}else if(control[i].control_name == controlList.RichText){
				
				//富文本
				if(!empty(data.rich_text)){
					
					if(data.rich_text.length>280000){
						flag = true;
						showTip("字数超出最大允许值！","warning");
					}
					
				}else{

					flag = true;
					showTip("富文本内容不能为空","warning");
					
				}
				
			}else if(control[i].control_name == controlList.CustomModule){
				
				var custom_module = eval("(" + data.custom_module + ")");
				if(!empty(custom_module)){
					if(custom_module.module_id == 0){
						flag = true;
						showTip("没有发现自定义模块","warning");
					}
				}
				
			}else if(control[i].control_name == controlList.Coupons){
				
//				优惠券
				
			}else if(control[i].control_name == controlList.Video){
				
//				视频
				var video = eval("(" + data.video + ")");
				if(!empty(video)){
					if(empty(video.url)){
						flag = true;
						showTip("请检查上传的视频文件是否正确，文件大小不能超过500MB！","warning");
					}
				}
				
			}else if(control[i].control_name == controlList.ShowCase){
				
//				橱窗
				var show_case = eval("(" + data.show_case + ")");
				if(!empty(show_case)){
					for(var j=0;j<show_case.itemList.length;j++){
						var curr = show_case.itemList[j];
						if(empty(curr.src)){
							flag = true;
							showTip("请上传图片","warning");
							break;
						}
					}
				}
				
			}
			
			if(flag){
				//发现错误，跳转到错误组件位置
				draggableElementClick($(".custom-main .draggable-element:eq(" + (control[i].sort-1) + ")"),"validation");
				break;
			}
		}
	}else{
		showTip("您还没有添加自定义模板哦","warning");
		return false;
	}
	if(flag) return false;
	return true;
}

/**
 * 验证汉字长度
 * @param str 要验证的字符串
 * @returns
 */
function testChinese(str) {
	return /^[a-zA-Z-0-9]{1,10}$/.test((str + '').replace(/[\u4e00-\u9fa5]/g, 'aa'));
}