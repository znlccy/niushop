var flag = false;
function saveAddress() {
	if (!Check_Consignee()) {
		return false;
	}
	var url = "";
/*	var ref_url = $("#ref_url").val();*/
	var addressID = $("#AddressID").val();
	var tempSeleAreaFouth = $("#seleAreaFouth").find("option:selected").text();
	// 表示没有区县
	if (tempSeleAreaFouth == "选择区/县") {
		tempSeleAreaFouth = "";
	}
	var addressinfo = $("#AddressInfo").val();
	var province = $("#seleAreaNext").val();
	var city = $("#seleAreaThird").val();
	var district = $("#seleAreaFouth").val();
	var name=$("#Name").val();
	var mobile=$("#Moblie").val();
	var $remark=$("#AddressInfo").val();
	var address_id=$("#adressid").val();
	var phone = $("#phone").val();
	if(flag){
		return;
	}
	flag = true;
	if(address_id<=0 || address_id==''){
		$.ajax({
			type: "POST",
			url: __URL(SHOPMAIN+"/member/addressInsert"),
			data: {"consigner":name,"mobile":mobile,"province":province,"city":city,"district":district,"address":addressinfo,"phone":phone},
			success: function (txt) {
				if (txt["code"] >0) {
				   window.location.href =__URL(SHOPMAIN+"/member/addressList");
				} else {
					$.msg(txt, {
						time: 2000
					});
					flag = false;
				}
			}
		});
	}else{
		$.ajax({
			type: "POST",
			url: __URL(SHOPMAIN+"/member/updateMemberAddress"),
			data: {"id":address_id,"consigner":name,"mobile":mobile,"province":province,"city":city,"district":district,"address":addressinfo,"phone":phone},
			success: function (txt) {
				if (txt["code"] > 0) {
					 window.location.href =__URL(SHOPMAIN+"/member/addressList");
				} else {
					$.msg(txt, {
						time: 2000
					});
					flag = false;
				}
			}
		});
	}
}

function Check_Consignee() {
	var reg = /^\d{11}$/;
	if ($("#seleAreaFouth").val() < 0 || $("#seleAreaFouth").val() == "") {
		if ($("#seleAreaNext").val() == "" || $("#seleAreaNext").val() == -1) {
			$.msg("请选择省份", {
				time: 2000
			});
			$("#seleAreaNext").focus();
			return false;
		}
		if ($("#seleAreaThird").val() == "" || $("#seleAreaThird").val() == -1) {
			$.msg("请选择市", {
				time: 2000
			});
			$("#seleAreaThird").focus();
			return false;
		}
		if($("#seleAreaFouth").find("option").length>1 && $("#seleAreaFouth").val() == -1){
			$.msg("请选择区/县", {
				time: 2000
			});
			$("#seleAreaFouth").focus();
			return false;
		}
	}

	if ($("#Name").val() == "") {
		$.msg("收货人姓名不能为空", {
			time: 2000
		});
		$("#Name").focus();
		return false;
	} 
	if ($("#AddressInfo").val() == "") {
		$.msg("详细地址不能为空", {
			time: 2000
		});
		$("#AddressInfo").focus();
		return false;
	} 
	if ($("#Moblie").val() == "") {
		$.msg("手机号码不能为空", {
			time: 2000
		});
		$("#Moblie").focus();
		return false;
	} 
	if (!reg.test($("#Moblie").val())) {
		$.msg("请输入正确的手机号码", {
			time: 2000
		});
		$("#Moblie").focus();
		return false;
	} 
	
	var phone = $("#phone").val();
	if(phone.length > 0){
		var pattern =/^0\d{2,3}-?\d{7,8}$/; 
		if(!pattern.test(phone)) { 
			$.msg("请输入正确的固定电话", {
				time: 2000
			});
			$("#phone").focus();
			return false; 
		} 
	}
	
	return true;
}

// 选择省份弹出市区
function GetProvince() {
	var id = $("#seleAreaNext").find("option:selected").val();
	var selCity = $("#seleAreaThird")[0];
	for (var i = selCity.length - 1; i >= 0; i--) {
		selCity.options[i] = null;
	}
	var opt = new Option("请选择市", "-1");
	selCity.options.add(opt);
	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN+"/member/getCity"),
		dataType : "json",
		data : {
			"province_id" : id
		},
		success : function(data) {
			if (data != null && data.length > 0) {
				for (var i = 0; i < data.length; i++) {
					var opt = new Option(data[i].city_name,data[i].city_id);
					selCity.options.add(opt);
				}
			}
		}
	});
};
// 选择市区弹出区域
function getSelCity() {
	var id = $("#seleAreaThird").find("option:selected").val();
	var selArea = $("#seleAreaFouth")[0];
	for (var i = selArea.length - 1; i >= 0; i--) {
		selArea.options[i] = null;
	}
	var opt = new Option("请选择区县", "-1");
	selArea.options.add(opt);
	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN+"/member/getDistrict"),
		dataType : "json",
		data : {
			"city_id" : id
		},
		success : function(data) {
			if (data != null && data.length > 0) {
				for (var i = 0; i < data.length; i++) {
					var opt = new Option(data[i].district_name,data[i].district_id);
					selArea.options.add(opt);
				}
			}
		}
	});
}