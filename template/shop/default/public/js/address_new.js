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
	if(address_id <= 0 &&　address_id　!= ''){
		$.ajax({
			type: "POST",
			url : __URL(SHOPMAIN+"/member/addressList"),
			data: {"consigner":name,"mobile":mobile,"province":province,"city":city,"district":district,"address":addressinfo},
			success: function (txt) {
				if (txt["code"] >0) {
				   window.location.href ="addressTotal";
				} else {
					alert(txt);
				}
			}
		});
	}else{
		$.ajax({
			type: "POST",
			url : __URL(SHOPMAIN+"/member/updatememberaddress"),
			data: {"id":address_id,"consigner":name,"mobile":mobile,"province":province,"city":city,"district":district,"address":addressinfo},
			success: function (txt) {
				if (txt["code"] > 0) {
					 window.location.href ="addressTotal";
				} else {
					 alert(txt);
				}
			}
		});
	}
}

function Check_Consignee() {
	var reg = /^\d{11}$/;
	if ($("#seleAreaFouth").val() < 0 || $("#seleAreaFouth").val() == "") {
		if ($("#seleAreaNext").val() == "") {
			alert("请选择省份");
			$("#seleAreaNext").focus();
			return false;
		}
		if ($("#seleAreaThird").val() == "") {
			alert("请选择市");
			$("#seleAreaThird").focus();
			return false;
		}
		if ($("#seleAreaFouth")[0].length == 1 && $("#seleAreaThird")[0].length > 1 && $("#seleAreaThird").val() > -1) {
			return true;
		} else {
			alert("请选择区/县");
			$("#seleAreaFouth").focus();
			return false;
		}
	}
	if ($("#Name").val() == "") {
		alert("收货人姓名不能为空");
		$("#Name").focus();
		return false;
	} 
	if ($("#AddressInfo").val() == "") {
		alert("详细地址不能为空");
		$("#AddressInfo").focus();
		return false;
	} 
	if ($("#Moblie").val() == "") {
		alert("手机号码不能为空");
		$("#Moblie").focus();
		return false;
	} 
	if (!reg.test($("#Moblie").val())) {
		alert("请输入正确的手机号码");
		$("#Moblie").focus();
		return false;
	} 
	
	
	return true;
}

// 选择省份弹出市区
function GetProvince(obj,sencond) {
	
	var id = $(obj).find("option:selected").val();
	var selCity = $(sencond)[0];
	for (var i = selCity.length - 1; i >= 0; i--) {
		selCity.options[i] = null;
	}
	var opt = new Option("请选择市", "-1");
	selCity.options.add(opt);
	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN+"/member/getcity"),
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
function getSelCity(obj,sencond) {
	var id = $(obj).find("option:selected").val();
	var selArea = $(sencond)[0];
	for (var i = selArea.length - 1; i >= 0; i--) {
		selArea.options[i] = null;
	}
	var opt = new Option("请选择市", "-1");
	selArea.options.add(opt);
	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN+"/member/getdistrict"),
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