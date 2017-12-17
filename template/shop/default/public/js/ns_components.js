/**
 * 组件条用js 李志伟 2017年2月10日10:17:00
 */

// 组件根据广告位查询广告 ap_id广告位id
function platformAdvLoad(ap_id) {
	var result = '';
	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN + "/components/platformadvlist"),
		async : false,
		data : {
			'ap_id' : ap_id
		},
		dataType : 'json',
		success : function(data) {
			// alert(JSON.stringify(data));
			result = data;
			return result;
		}
	});
	return result;
}

// 组件根据广告位查询广告 ap_id广告位id
function platformAdvLoadNew(ap_id) {
	var result = '';
	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN + "/components/platformadvlistnew"),
		async : false,
		data : {
			'ap_id' : ap_id
		},
		dataType : 'json',
		success : function(data) {
			result = data;
			return result;
		}
	});
	return result;
}

// 通过关键字获取广告位 ap_keyword 广告位关键字
function platformAdvByApkeyword(ap_keyword){
	var result = '';
	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN + "/components/getPlatformAdvListByKeyword"),
		async : false,
		data : {
			'ap_keyword' : ap_keyword
		},
		dataType : 'json',
		success : function(data) {
			result = data;
			return result;
		}
	});
	return result;
}

//通过广告位关键字获取广告代码
function getAdvCodebyApKeyword(ap_keyword){
	var result = '';
	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN + "/components/getAdvCode"),
		async : false,
		data : {
			'ap_keyword' : ap_keyword
		},
		dataType : 'json',
		success : function(data) {
			result = data;
			return result;
		}
	});
	return result;
}