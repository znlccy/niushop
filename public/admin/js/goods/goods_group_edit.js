/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : 小学生wyj
 * @date : 2017年5月24日 12:13:28
 * @version : v1.0.0.0
 * 添加商品标签
 */

//模块输入信息验证
function verify(group_name){
	if(group_name == ''){
		$("#group_name").parent().next().show();
		$("#group_name").focus();
		return false;
	}else{
		$(".error").hide();
	}
	return true;
}

var flag = false;//防止重复提交
//添加模块
function addGoodsGroupAjax() {
	var group_name = $("#group_name").val();
	var sort = $("#sort").val();
//	if($("#is_visible").prop("checked")){
//		var is_visible = 1;
//	}else{
//		var is_visible = 0;
//	}
	var is_visible = 1;
	var group_pic = $("#group_pic").val();
	if(verify(group_name)){
		if(flag){
			return false;
		}
		flag = true;
		$.ajax({
			type : "post",
			url : __URL(ADMINMAIN+"/goods/addgoodsgroup"),
			data : {
				'group_name' : group_name,
				'sort' : sort,
				'is_visible' : 1,
				'group_pic' : group_pic
			},
			success : function(data) {
				if (data["code"] > 0) {
					showMessage('success', "商品标签添加成功",__URL(ADMINMAIN+'/goods/goodsgrouplist'));
				}else{
					showMessage('error', "商品标签添加失败");
					flag = false;
				}
			}
		});
	}
}
//修改模块
function updateGoodsCategoryAjax() {
	var group_id = $("#group_id").val();
	var group_name = $("#group_name").val();
	var sort = $("#sort").val();
	var group_pic = $("#group_pic").val();
//	if($("#is_visible").prop("checked")){
//		var is_visible = 1;
//	}else{
//		var is_visible = 0;
//	}
	var is_visible = 1;
	if(verify(group_name)){
		if(flag){
			return false;
		}
		flag =true;
		$.ajax({
			type : "post",
			url : __URL(ADMINMAIN+"/goods/updategoodsgroup"),
			data : {
				'group_id' : group_id,
				'group_name' : group_name,
				'sort' : sort,
				'is_visible' : 1,
				'group_pic' : group_pic
			},
			success : function(data) {
				if (data["code"] > 0) {
					showMessage('success', "商品标签修改成功",__URL(ADMINMAIN+'/goods/goodsgrouplist'));
				}else{
					showMessage('error', "商品标签修改失败");
					flag = false;
				}	
			}
		});
	}
}

//图片上传
function imgUpload(){
	OpenPricureDialog("PopPicure", ADMINMAIN,1);
}

function PopUpCallBack(img_id,img_src){
	$("#imgLogo").attr("src",__IMG(img_src));
	$("#group_pic").val(img_id);
}