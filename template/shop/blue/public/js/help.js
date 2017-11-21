$(function() {
	
	//默认进入帮助中心，打开第一个分类下的第一个文章
	if($("#document_id").val() == ''){
		if($(".left-content li:first ul li").length>0){
			$(".left-content li:first ul").show();
			$(".left-content li:first ul li:first a").addClass("curr"); 
		}
	}
	
	$('.tree li > h4').click(function() {
		var children = $(this).parent('li').find(' > ul > li');
		if (children.is(":visible")) {
			$(this).find('i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
			$(this).parent('li').find(' > ul').slideUp('fast');
		} else {
			children.slideDown('fast');
			$(this).parent('li').siblings().find('i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
			$(this).parent('li').siblings().find(' > ul').slideUp('fast');
			$(this).find('i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
			$(this).parent('li').find(' > ul').slideDown('fast');
		}
	});

});