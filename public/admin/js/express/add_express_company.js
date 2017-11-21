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
 * @date : 2017年5月24日 11:51:24
 * @version : v1.0.0.0
 * 添加物流公司
 */

//模块输入信息验证
function verify(company_name,express_no,phone) {
	if (company_name == '') {
		$("#company_name").parent().next().show();
		$("#company_name").focus();
		return false;
	}else{
		$("#company_name").parent().next().hide();
	}
	
	if (express_no == '') {
		$("#express_no").parent().next().show();
		$("#express_no").focus();
		return false;
	} else {
		$("#express_no").parent().next().hide();
	}
	
	return true;
}
var flag = false;//防止重复提交
//添加物流公司
function addExpressCompanyAjax() {
	var company_name = $("#company_name").val();
	var express_no = $("#express_no").val();
	var company_logo = $("#logo").val();
	var phone = $("#phone").val();
	var orders = $("#orders").val();
	var is_default = 0;
	if($("#is_enabled").prop("checked")){
		var is_enabled = 1;
	}else{
		var is_enabled = 0;
	}
	
	if($("#is_default").prop("checked")){
		 is_default = 1;
	}
	if(verify(company_name, express_no,phone)){
		if(flag){
			return;
		}
		flag = true;
		
		$.ajax({
			type : "post",
			url : __URL(ADMINMAIN+"/express/addexpresscompany"),
			data : {
				'company_name' : company_name,
				'express_no' : express_no,
				'express_logo' : company_logo,
				'phone' : phone,
				'is_enabled' : is_enabled,
				'orders':orders,
				'is_default':is_default
			},
			success : function(data) {
				if(data['code']>0){

					$("#dialog").dialog({
						buttons : {
							"确定" : function() {
								$(this).dialog('close');

								$("#dialog").dialog({
									buttons: {
										"设置运费模板": function() {
											$(this).dialog('close');
											location.href = __URL(ADMINMAIN + '/express/freighttemplatelist?co_id='+data['code']);
										},
										"取消,#e57373": function() {
											$(this).dialog('close');
											location.href = __URL(ADMINMAIN + '/express/expresscompany');
										}
									},
									contentText:"物流公司需要设置运费模板才能配送，是否前往设置？",
								});
							}
						},
						contentText : data["message"]
					});
				}else{
					$("#dialog").dialog({
						buttons : {
							"确定" : function() {
								$(this).dialog('close');
								flag = false;
							}
						},
						contentText : data["message"]
					});
				}
			}
		});
	}
}