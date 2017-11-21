/**
 * 省市县级联 2017年2月20日 15:05:46
 */

//加载省
function initProvince(obj,callBack){
	$.ajax({
		type : "post",
		url : __URL(ADMINMAIN+"/goods/getprovince"),
		dataType : "json",
		success : function(data) {
			if (data != null && data.length > 0) {
				var str = "";
				for (var i = 0; i < data.length; i++) {
					str += '<option value="'+data[i].province_id+'">'+data[i].province_name+'</option>';
				}
				$(obj).append(str);
				if(callBack != undefined){
					callBack();
				}
				
			}
		}
	});
}

//选择省份弹出市区	index:根据index选择市
function getProvince(obj,second,index) {
	var id = $(obj).find("option:selected").val();
	var str = "<option value='0'>请选择市</option>";
	if(id!=0){
		$.ajax({
			type : "post",
			url : __URL(ADMINMAIN+"/goods/getcity"),
			dataType : "json",
			data : {
				"province_id" : id
			},
			success : function(data) {
				if (data != null && data.length > 0) {
					for (var i = 0; i < data.length; i++) {
						if(index != -1 && data[i].city_id == index){
							str += '<option value="'+data[i].city_id+'" selected="selected">'+data[i].city_name+'</option>';
						}else{
							str += '<option value="'+data[i].city_id+'">'+data[i].city_name+'</option>';
						}
					}
					$(second).html(str);
				}
			}
		});
	}else{
		$(second).html(str);
	}
};

//选择市区弹出区域
function getSelCity(obj,second) {
	var id = $(obj).find("option:selected").val();
	$.ajax({
		type : "post",
		url : __URL(ADMINMAIN+"/goods/getdistrict"),
		dataType : "json",
		data : {
			"city_id" : id
		},
		success : function(data) {
			if (data != null && data.length > 0) {
				var str = "<option value='0'>请选择区</option>";
				for (var i = 0; i < data.length; i++) {
					str += '<option value="'+data[i].district_id+'">'+data[i].district_name+'</option>';
				}
				$(second).html(str);
			}
		}
	});
}