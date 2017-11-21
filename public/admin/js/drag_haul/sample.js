$(function() {
	
	$('.gridly .brick').live('click',function(e){
		var js_img = $(this).attr("js-img");
		shopImageFlag = js_img;//所点击的商品图片标识
		speciFicationsFlag = 0;
		OpenPricureDialog("PopPicure", ADMINMAIN, 5);
	});
	
	$('.gridly .close-modal').click(function(){
		var curr = $(this);
		if (null != curr.prev().attr("data-id") && "" != curr.prev().attr("data-id")) {
			curr.prev().removeAttr("data-id");
			curr.prev().attr("src", ADMINIMG + "/Default.png");
		}
		return false;//防止事件冒泡
	})
	$('#img_box').live('click',function(e){
		var js_img = $(this).attr("js-img");
		shopImageFlag = js_img;//所点击的商品图片标识
		speciFicationsFlag = 0;
		OpenPricureDialog("PopPicure", ADMINMAIN, 0, 1);
	});
	$('#sku_img_box').live('click',function(e){
		var js_img = $(this).attr("js-img");
		var spec_id = $(this).attr("spec_id");
		var spec_value_id = $(this).attr("spec_value_id");
		shopImageFlag = js_img;//所点击的商品图片标识
		speciFicationsFlag = 0;
		OpenPricureDialog("PopPicure", ADMINMAIN, 0, 2, spec_id, spec_value_id);
	});
	$('.sku-img-check').live('click',function(e){
		var js_img = $(this).attr("js-img");
		var spec_id = $(this).prev().prev().attr("data-spec-id");
		var spec_value_id = $(this).prev().prev().attr("data-spec-value-id");
		shopImageFlag = js_img;//所点击的商品图片标识
		speciFicationsFlag = 0;
		OpenPricureDialog("PopPicure", ADMINMAIN, 1, 3,spec_id, spec_value_id);
	});
	
});