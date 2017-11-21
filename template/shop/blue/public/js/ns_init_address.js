/**
 * 省市县级联 2017年2月20日 15:05:46
 */

//加载省
function initProvince(obj){
	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN + "/member/getprovince"),
		dataType : "json",
		success : function(data) {
			if (data != null && data.length > 0) {
				var str = "";
				for (var i = 0; i < data.length; i++) {
					str += '<option value="'+data[i].province_id+'">'+data[i].province_name+'</option>';
				}
				$(obj).append(str);
			}
		}
	});
}

//选择省份弹出市区
function getProvince(obj,second,city_id) {
	var id = $(obj).find("option:selected").val();
	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN + "/member/getcity"),
		dataType : "json",
		data : {
			"province_id" : id
		},
		success : function(data) {
			if (data != null && data.length > 0) {
				var str = "<option value='-1'>请选择市</option>";
				for (var i = 0; i < data.length; i++) {
					if(city_id ==data[i].city_id){
						str += '<option value="'+data[i].city_id+'" selected="selected">'+data[i].city_name+'</option>';
					}else{
						str += '<option value="'+data[i].city_id+'">'+data[i].city_name+'</option>';
					}
				}
				$(second).html(str);
				$("#selDistricts").html('<option value="-1" selected="selected">请选择区</option>');
			}
		}
	});
};

function getSelectAddress(province_id,city_id,district_id){
	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN + "/member/getselectaddress"),
		dataType : "json",
		data : { "province_id" : province_id, "city_id" :city_id },
		success : function(data){
			if (data != null ) {
				//省
				if(data["province_list"].length>0){
					var str = '<option value="-1">请选择省</option>';
					for (var i = 0; i < data["province_list"].length; i++) {
						if(province_id == data["province_list"][i].province_id){
							str += '<option value="'+data["province_list"][i].province_id+'" selected="selected">'+data["province_list"][i].province_name+'</option>';
						}else{
							str += '<option value="'+data["province_list"][i].province_id+'">'+data["province_list"][i].province_name+'</option>';
						}
					}
					$("#selProvinces").html(str);
				}
				
				//市
				if(data["city_list"].length>0){
					var str = "<option value='-1'>请选择市</option>";
					for (var i = 0; i < data["city_list"].length; i++) {
						if(city_id ==data["city_list"][i].city_id){
							str += '<option value="'+data["city_list"][i].city_id+'" selected="selected">'+data["city_list"][i].city_name+'</option>';
						}else{
							str += '<option value="'+data["city_list"][i].city_id+'">'+data["city_list"][i].city_name+'</option>';
						}
					}
					$("#selCities").html(str);
				}
				
				//区县
				if(data["district_list"].length>0){
					var str = "<option value='-1'>请选择区</option>";
					for (var i = 0; i < data["district_list"].length; i++) {
						if(district_id == data["district_list"][i].district_id){
							str += '<option value="'+data["district_list"][i].district_id+'" selected="selected">'+data["district_list"][i].district_name+'</option>';
						}else{
							str += '<option value="'+data["district_list"][i].district_id+'">'+data["district_list"][i].district_name+'</option>';
						}
					}
					$("#selDistricts").html(str);
				}
				
			}
		}
	})
}


//选择市区弹出区域
function getSelCity(obj,second,district_id) {
	var id = $(obj).find("option:selected").val();
	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN + "/member/getdistrict"),
		dataType : "json",
		data : {
			"city_id" : id
		},
		success : function(data) {
//			alert(JSON.stringify(data));
			if (data != null && data.length > 0) {
				var str = "<option value='-1'>请选择区</option>";
				for (var i = 0; i < data.length; i++) {
					if(district_id == data[i].district_id){
						str += '<option value="'+data[i].district_id+'" selected="selected">'+data[i].district_name+'</option>';
					}else{
						str += '<option value="'+data[i].district_id+'">'+data[i].district_name+'</option>';
					}
				}
				$(second).html(str);
			}
		}
	});
}

function validationAddress(){
	var consigner = $("#consigner");//收件人
	var detailed_address = $("#detailed_address");//详细地址
	var mobile = $("#mobile");//手机
	var phone_1 = $("#phone_1").val();//固定电话
	var phone_2 = $("#phone_2").val();//固定电话
	var phone_3 = $("#phone_3").val();//固定电话
	var selProvinces = $("#selProvinces");//省
	var selCities = $("#selCities");//市
	var selDistricts = $("#selDistricts");//区
	if(consigner.val() == ""){
		consigner.next().find('span').text('收件人不能为空').css('visibility','visible');
		consigner.focus();
		return false;
	}else{
		consigner.next().find('span').css('visibility','hidden');
	}
	
	if(mobile.val() == "" ){
		mobile.focus();
		$(".phone-notice").text("手机号码不能为空").css('visibility','visible');
		return false;
	}else{
		$(".phone-notice").css('visibility','hidden');
	}

	var reg = /^1[34578][0-9]\d{8}$/;
	if(!reg.test(mobile.val())){
		mobile.focus();
		$(".phone-notice").text("手机号码格式错误").css('visibility','visible');
		return false;
	}else{
		$(".phone-notice").css('visibility','hidden');
	}
	
	if(parseInt(selProvinces.val()) == -1 || parseInt(selCities.val()) == -1 || parseInt(selDistricts.val()) == -1){
		selProvinces.focus();
		$(".address-notice").text('所在地区不完善').css('visibility','visible');
		return false;
	}else{
		$(".address-notice").css('visibility','hidden');
	}
	
	if(detailed_address.val() == ""){
		detailed_address.focus();
		detailed_address.next().find('span').text('详细地址不能为空').css('visibility','visible');
		return false;
	}else{
		detailed_address.next().find('span').css('visibility','hidden');
	}
	
	return true;
}


//编辑地址
var flag = false;
function submitAddress(){
	if(validationAddress()){
		var address_id = $("#address_id").val();
		var consigner = $("#consigner").val();//收件人
		var detailed_address = $("#detailed_address").val();//详细地址
		var zipcode = $("#zipcode").val();//邮编
		var mobile = $("#mobile").val();//手机
		var phone_1 = $("#phone_1").val();//固定电话
		var phone_2 = $("#phone_2").val();//固定电话
		var phone_3 = $("#phone_3").val();//固定电话
		var phone = phone_1+"-"+phone_2+"-"+phone_3;//固定电话
		var selProvinces = $("#selProvinces").val();//省
		var selCities = $("#selCities").val();//市
		var selDistricts = $("#selDistricts").val();//区
		if(flag){
			return;
		}
		flag = true;
		$.ajax({
			url: __URL(SHOPMAIN + "/member/operationaddress"),
			type : "post",
			data : { "id" : address_id,"consigner" : consigner, "mobile" : mobile, "phone" : phone, "zipcode" : zipcode, "address" : detailed_address, "province" : selProvinces, "city" : selCities, "district" : selDistricts},
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
}