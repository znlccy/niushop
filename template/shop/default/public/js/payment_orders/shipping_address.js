/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : 小学生王永杰
 * @date : 2017年6月14日 14:13:33
 * @version : v1.0.0.0
 * PC端待付款订单收货地址
 */

$(function(){
	
	$("#address_id").val($(".js-shipping-address li[class*='default-add']").attr('data-id'));
	
	/**
	 * 修改收货地址
	 * 2017年6月14日 15:05:59 王永杰
	 */
	$(".js-update-shipping-address").bind("click",function(){
		var id = $(this).attr("data-id");
		$("#address_id").val(id);
		clearAddress();
		$.ajax({
			url : __URL(SHOPMAIN + "/member/getmemberexpressaddress"),
			type : "post",
			data : { "id" : id},
			success : function(res){
				if(res != null && res != ""){
					$("#consigner").val(res.consigner);
					getSelectAddress(res.province,res.city,res.district);
					$("#detailed_address").val(res.address);
					$("#zipcode").val(res.zip_code);
					$("#mobile").val(res.mobile);
					$("#phone").val(res.phone);
					$("#mask").show();
					$(".edit-address").show();
				}
			}
		});
		return false;
	});

	
	
	/**
	 * 删除收货地址
	 * 2017年6月14日 14:45:20 王永杰
	 */
	$(".js-shipping-address-remove").bind("click",function(){
		var curr = $(this);
		var id = curr.attr("data-id");
		$("#mask").show();
		$('.pop-compare,.pop-mask').show();
		$('.pop-compare .pop-text').html('您确认要删除吗？');
		$('.pop-compare').css({'top':($(window).height()-$('.pop-compare').outerHeight())/2});
		$('.cancel-btn').removeClass('none');
		$('.pop-sure').click(function(){
			$.ajax({
				url : __URL(SHOPMAIN + "/member/memberaddressdelete"),
				type : "post",
				data : {"id" :id},
				success : function(res){
					$.msg(res.message);
					if(res.code>0){
						location.reload();
					}
				}
			});
		});
		$(".cancel-btn,.pop-close").click(function(){
			$("#mask").hide();
			$('.pop-compare,.pop-mask').hide();
		});
		return false;
	});
	
	/**
	 * 打开收货地址
	 * 2017年6月14日 14:50:44 王永杰
	 */
	$(".js-add-shipping-address").bind("click",function(){
		clearAddress();
		$("#address_id").val(0);
		$("#mask").show();
		$(".edit-address").show();
	});
	
	/**
	 * 关闭收货地址编辑
	 * 2017年6月14日 14:51:24
	 */
	$(".edit-address i").bind("click",function(){
		$("#mask").hide();
		$(".edit-address").hide();
	})

	/**
	 * 编辑收货地址
	 * 2017年6月14日 14:28:52
	 */
	var flag = false;
	$("#save_shipping_address").bind("click",function(){
		if(validationAddress()){
			var address_id = $("#address_id").val();
			var consigner = $("#consigner").val();//收件人
			var detailed_address = $("#detailed_address").val();//详细地址
			var zipcode = $("#zipcode").val();//邮编
			var mobile = $("#mobile").val();//手机
			var phone = $("#phone").val();//固定电话
			var selProvinces = $("#selProvinces").val();//省
			var selCities = $("#selCities").val();//市
			var selDistricts = $("#selDistricts").val();//区
			if(flag){
				return;
			}
			flag = true;
			$.ajax({
				url : __URL(SHOPMAIN + "/member/operationaddress"),
				type : "post",
				data : { 
					"id" : address_id,
					"consigner" : consigner,
					"mobile" : mobile, 
					"phone" : phone,
					"zipcode" : zipcode,
					"address" : detailed_address,
					"province" : selProvinces,
					"city" : selCities,
					"district" : selDistricts
					},
				success : function(res){
					$.msg(res.message);
					if(res.code>0){
						location.reload();
					}else{
						flag = false;
					}
				}
			});
		}
	})
	
	/**
	 * 设置默认收货地址
	 */
	$(".js-shipping-address li[class*='other-add']").bind("click",function(){
		var $curr = $(this);
		var id = $curr.attr("data-id");
		if($(this).hasClass("default-add")){
			return;
		}
		$.ajax({
			url : __URL(SHOPMAIN+"/member/updateaddressdefault"),
			type : "post",
			data : {"id" : id},
			success : function(data){
				if(data.code>0){
					$(".js-shipping-address li.other-add").removeClass('default-add');
					$curr.addClass("default-add");
					$("#address_id").val(id);
					location.reload();
				}else{
					$.msg(data.message);
				}
			}
		});
	});
});


/**
 * 清空收货地址输入框
 * 2017年6月14日 15:14:08 王永杰
 */
function clearAddress(){
	$("#consigner").val("");
	$("#detailed_address").val("");
	$("#zipcode").val("");
	$("#mobile").val("");
	$("#phone").val("");
	$("#selProvinces").find("option[value='-1']").attr('selected',"selected");
	$("#selCities").find("option[value='-1']").attr('selected',"selected");
	$("#selDistricts").find("option[value='-1']").attr('selected',"selected");
}


/**
 * 验证收货地址
 * 2017年6月14日 14:26:57 王永杰
 * @returns {Boolean}
 */
function validationAddress(){
	var consigner = $("#consigner");//收件人
	var mobile = $("#mobile");//手机
	var phone = $("#phone").val();//固定电话
	var detailed_address = $("#detailed_address");//详细地址
	var selProvinces = $("#selProvinces");//省
	var selCities = $("#selCities");//市
	var selDistricts = $("#selDistricts");//区
	if(consigner.val() == ""){
		consigner.focus();
		$.msg("收件人不能为空");
		return false;
	}
	
	if(mobile.val() == "" ){
		mobile.focus();
		$.msg("手机号码不能为空");
		return false;
	}
	var reg = /^1[34578][0-9]\d{8}$/;
	if(!reg.test(mobile.val())){
		mobile.focus();
		$.msg("手机号码格式错误");
		return false;
	}

	if(phone.length > 0){
		var pattern=/(^[0-9]{3,4}\-[0-9]{3,8}$)|(^[0-9]{3,8}$)|(^\([0-9]{3,4}\)[0-9]{3,8}$)|(^0{0,1}13[0-9]{9}$)/; 
		if(!pattern.test(phone)) { 
			$.msg("请输入正确的固定电话");
			$("#phone").focus();
			return false; 
		} 
	}
	
	if(parseInt(selProvinces.val()) == -1 || parseInt(selCities.val()) == -1 || (selDistricts.find("option").length>1 && parseInt(selDistricts.val()) == -1)){
		selProvinces.focus();
		$.msg('所在地区不完善');
		return false;
	}
	
	if(detailed_address.val() == ""){
		detailed_address.focus();
		$.msg('详细地址不能为空');
		return false;
	}
	
	return true;
}