//鼠标经过、离开地区文字，显示、隐藏地区选择层，点击关闭按钮隐藏地区选择层
$(function() {
	var city_time = null;
	$('.region,.region-chooser-box').mouseover(function() {
		clearTimeout(city_time);
		$('.region-chooser-box').show();
		$('.region').addClass('active');
	});
	$('.region,.region-chooser-box').mouseout(function() {
		city_time = setTimeout(function() {
			$('.region-chooser-box').hide();
			$('.region').removeClass('active');
		}, 200);
	});
	$('.region-chooser-close').click(function() {
		$('.region-chooser-box').hide();
		$('.region').removeClass('active');
	})
	
	$(".region-tab").click(function(){
		$(".region-items[data-region-level='1']").hide();
		$(".region-items[data-region-level='2']").hide();
		$(".region-items[data-region-level='3']").hide();
		$(".region-tab").removeClass("selected");
		$(this).addClass("selected");
		switch (parseInt($(this).attr("data-region-level"))) {
		case 1:
			//省
			$(".region-items[data-region-level='1']").show();
			break;
		case 2:
			//市
			$(".region-items[data-region-level='2']").show();
			break;
		case 3:
			//区县
			$(".region-items[data-region-level='3']").show();
			break;
		}
	});
	initAddress();
})

var province_list = null;//省
var city_list = null;//市
var district_list =null;//区县

// 加载省市县
function initAddress() {
	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN + "/goods/getaddress"),
		dataType : "json",
		success : function(data) {
			if (data != null) {
				province_list = data["province_list"];
				city_list = data["city_list"];
				district_list = data["district_list"];
				var str_province = "";
				for (var i = 0; i < province_list.length; i++) {
					str_province += '<a href="javascript:;" onclick="selectProvince(this)" data-region-level="1" ';
					str_province += ' data-region-province-id='+province_list[i].province_id;
					str_province += ' data-region-name="'+province_list[i].province_name+'">'+province_list[i].province_name+'</a>';
					if($("#hidden_province").val() == province_list[i].province_name){
						$("#hidden_province").attr("data-province-id",province_list[i].province_id);
						$(".js-region").attr("data-province",province_list[i].province_id);
					}
				}
				$(".region-items[data-region-level='1']").html(str_province);
				$(".region-items[data-region-level='2']").html(getCity($("#hidden_province").attr("data-province-id")));
				$(".region-items[data-region-level='3']").html(getDistrict($("#hidden_city").attr("data-city-id")));
			}
		}
	});
}

//获取市
function getCity(province_id){
	var str_city = "";
	for(var j = 0; j < city_list.length; j++){
		if(city_list[j].province_id == province_id){
			str_city += '<a href="javascript:;" onclick="selectCity(this)" data-region-level="2" ';
			str_city += ' data-region-city-id='+city_list[j].city_id;
			str_city += ' data-region-name="'+city_list[j].city_name+'">'+city_list[j].city_name+'</a>';
			//第一次定位设置城市的Id，为了下一级
			if($("#hidden_city").val() == city_list[j].city_name){
				$("#hidden_city").attr("data-city-id",city_list[j].city_id);
				$(".js-region").attr("data-city",city_list[j].city_id);
			}
		}
	}
	return str_city;
}

//选择区县
function getDistrict(city_id){
	var str_district = "";
	var count = 0;
	for(var k = 0;k < district_list.length;k++){
		if(district_list[k].city_id == city_id){
			str_district += '<a href="javascript:;" onclick="selectDistrict(this)" data-region-level="3" ';
			str_district += ' data-region-district-id='+district_list[k].district_id;
			str_district += ' data-region-name="'+district_list[k].district_name+'">'+district_list[k].district_name+'</a>';
			count ++;
		}
	}
	
	if(count != 0){
		$(".region-items[data-region-level='1']").hide();
		$(".region-items[data-region-level='2']").hide();
		$(".region-items[data-region-level='3']").show();
		$(".region-tab[data-region-level='3']").show();
		$(".region-tab").removeClass("selected");
		$(".region-tab[data-region-level='3']").addClass("selected");
	}
	
	return str_district;
}


function clearAddressId(){
	$(".region-tab[data-region-level='1']").attr("data-province-id","");
	$(".region-tab[data-region-level='2']").attr("data-city-id","");
	$(".region-tab[data-region-level='3']").attr("data-district-id","");
}

//选择省
function selectProvince(obj){
	$(".region-items[data-region-level='1']").hide();
	$(".region-items[data-region-level='2']").show();
	$(".region-items[data-region-level='3']").hide();
	
	$(".region-tab[data-region-level='1']").html($(obj).text()+"<i></i>");
	$(".region-tab[data-region-level='1']").attr("data-province-id",$(obj).attr("data-region-province-id"));
	$(".js-region").attr("data-province",$(obj).attr("data-region-province-id"));
	
	$(".region-tab").removeClass("selected");
	$(".region-tab[data-region-level='2']").addClass("selected");
	
	$(".region-items[data-region-level='2']").html(getCity($(obj).attr("data-region-province-id")));//该省下的市
	$(".region-tab[data-region-level='2']").html("请选择市<i></i>");
	$(".region-tab[data-region-level='3']").html("请选择区/县<i></i>");
	$(".region-items[data-region-level='3']").html("");
}

//选择市
function selectCity(obj){
	$(".region-items[data-region-level='1']").hide();
	$(".region-items[data-region-level='2']").hide();
	$(".region-items[data-region-level='3']").show();
	
	$(".region-tab[data-region-level='2']").html($(obj).text()+"<i></i>");
	$(".region-tab[data-region-level='2']").attr("data-city-id",$(obj).attr("data-region-city-id"));
	$(".js-region").attr("data-city",$(obj).attr("data-region-city-id"));
	$(".region-tab").removeClass("selected");
	var html = getDistrict($(obj).attr("data-region-city-id"));
	if(html != ""){//是否有区县分类
		$(".region-items[data-region-level='3']").html(html);//该省下的区县
		$(".region-tab[data-region-level='3']").show();
		$(".region-tab[data-region-level='3']").addClass("selected");
	}else{
		$(".region-items[data-region-level='3']").hide();
		$(".region-items[data-region-level='2']").show();
		$(".region-tab[data-region-level='3']").hide();
		$(".region-tab[data-region-level='2']").addClass("selected");
		$(".region-tab[data-region-level='3']").attr("data-district-id",0);
		setRegion();
	}

}

function setRegion(){
	var region = "";
	var province = "";
	var city = "";
	var district = "";
	$(".region-tab").each(function(){
		region +=$(this).text();
		if($(this).attr("data-province-id") != null){
			province = $(this).attr("data-province-id");
		}
		if($(this).attr("data-city-id") != null){
			city = $(this).attr("data-city-id");
		}
		if($(this).attr("data-district-id") != null){
			district = $(this).attr("data-district-id");
		}
		
	});
	region = region.replace("请选择区/县","");
	$(".js-region").text(region);
	if(province != ""){
		$(".js-region").attr("data-province",province);
	}
	if(city != ""){
		$(".js-region").attr("data-city",city);
	}
	if(district != ""){
		$(".js-region").attr("data-district",district);
	}
	//根据地区id，查询物流公司及运费
	var provice_id = $(".js-region").attr('data-province');
	var city_id = $(".js-region").attr('data-city');
	var disctrict_id = $(".js-region").attr("data-district");
	var goods_id = $("#hidden_goodsid").val();
	var goods_sku_list = $("#hidden_skuid").val() + ":1";
	$.ajax({
		url: __URL(SHOPMAIN+"/goods/selcectexpress"),
		type: "post",
		data: { "goods_id":goods_id,"provice_id":provice_id,"city_id":city_id ,"disctrict_id" : disctrict_id, "goods_sku_list" : goods_sku_list },
		success: function (data) {
//			console.log(data);
			var html = '';
			if (data.length > 0) {
				if(data[0]['co_id'] > 0){
					html += '<select style="padding:3px 0;" class="express-list">';
					for(var j = 0;j<data.length; j++){
						html += '<option value="'+data[j]["company_name"]+'">'+data[j]["company_name"]+'&nbsp;&nbsp;&nbsp;¥'+data[j]["express_fee"]+'</option>';
					}
					html += '</select>';
				}else{
					html += '<div>' + data + '</div>';
				}
			}else{
				html += '<div></div>';
			}
			$(".post-age-info").html(html);
		}
	});
	
}


//选择区县
function selectDistrict(obj){
	$(".region-tab[data-region-level='3']").html($(obj).text()+"<i></i>");
	$(".region-tab[data-region-level='3']").attr("data-district-id",$(obj).attr("data-region-district-id"));
	setRegion();
}