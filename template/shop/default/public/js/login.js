// JavaScript Document
$(function(){

	//¶þÎ¬Âë¡¢PCµÇÂ¼ÇÐ»»
	$('.qrcode-target').click(function(){
		if($(this).hasClass('btn-qrcode')){
			$(this).removeClass('btn-qrcode').addClass('btn-login').attr('title','È¥µçÄÔµÇÂ¼');
			$('.login-wrap').hide();
			$('.login-mobile').show();
			return;
		}
		if($(this).hasClass('btn-login')){
			$(this).removeClass('btn-login').addClass('btn-qrcode').attr('title','È¥ÊÖ»úÉ¨ÂëµÇÂ¼');
			$('.login-wrap').show();
			$('.login-mobile').hide();
		}
	});
	
});