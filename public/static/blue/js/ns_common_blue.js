/**
 * 后台新界面
 * 2017年7月26日 12:02:39 王永杰
 */

$(function() {
	
	isShowAsideFooter();
	
	//控制操作提示按鈕是否显示
	if($(".right-side-operation li").length>1){
		$(".js-open-warmp-prompt").show();
	}
	
	//打开操作提示
	$(".js-open-warmp-prompt").click(function(){
		setWarmPrompt("show",function(){
			$(".ns-warm-prompt").show(400);
			$(".js-open-warmp-prompt").parent().fadeOut(400);
		});
		return false;
	});
	
	//关闭操作提示
	$(".ns-warm-prompt .alert .close").click(function() {
		setWarmPrompt("hidden",function(){
			$(".ns-warm-prompt").hide(400);
			$(".js-open-warmp-prompt").parent().fadeIn(400);
		});
		return false;
	});
	
})
window.onresize = function() {

	isShowAsideFooter();
	
};

// 控制左侧边栏的底部是否显示
function isShowAsideFooter() {
	if ($(".ns-base-aside nav li").length >= 20) {
		$(".ns-base-aside footer").hide();
	} else if ($(window).height() <= 530) {
		$(".ns-base-aside footer").hide();
	} else if ($(".ns-base-aside nav li").length > 8 && $(window).height() < 820) {
		$(".ns-base-aside footer").hide();
	}else if ($(".ns-base-aside nav li").length > 5 && $(window).height() < 700) {
		$(".ns-base-aside footer").hide();
	} else {
		$(".ns-base-aside footer").show();
	}
}

// 设置是否显示提示
function setWarmPrompt(value,fn){
	$.ajax({
		type : 'post',
		url : __URL(ADMINMAIN + "/Index/setWarmPromptIsShow"),
		data : { "value" : value },
		success : function(res){
			if(fn != undefined){
				fn.call(this);
			}
		}
	});
}
