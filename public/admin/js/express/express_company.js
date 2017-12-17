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
 * @date : 2017年5月24日 11:55:19
 * @version : v1.0.0.0
 * 物流公司列表
 */

function LoadingInfo(page_index) {
	var search_text = $("#search_text").val();
	$.ajax({
		type : "post",
		url : __URL(ADMINMAIN+"/express/expresscompany"),
		data : {
			"page_index" : page_index,
			"page_size" : $("#showNumber").val(),
			"search_text" : search_text
		},
		success : function(data) {
			var html = '';
			if (data["data"].length > 0) {
				for (var i = 0; i < data["data"].length; i++) {
					html += '<tr align="center">';
						html += '<td><div class="cell"><label><input name="sub" type="checkbox" value="'+ data['data'][i]['co_id']+'" ></label></div></td>';
						
					if(data["data"][i]["express_logo"] != null &&data["data"][i]["express_logo"] !=''){
						html += '<td class="tal"><img src="'+__IMG(data["data"][i]["express_logo"]) +'" style="margin-right:10px;max-width:60px;max-height:60px;"/>' + data["data"][i]["company_name"] + '</td>';
					}else{
						html += '<td class="tal">' + data["data"][i]["company_name"] + '</td>';
					}
					
						html += '<td class="tal">' + data["data"][i]["express_no"] + '</td>';
						html += '<td class="tal">' + data["data"][i]["phone"] + '</td>';
						if(data["data"][i]["is_default"]==1){
							html += '<td class="tal">是</td>';
						}else{
							html += '<td class="tal">否</td>';
						}

						html += '<td>' + data["data"][i]["orders"] + '</td>';
						html += data["data"][i]["is_enabled"] == 0 ? '<td style="color:red;">未启用</td>' : '<td style="color:green;">启用</td>';
						
						html += '<td><a href="'+__URL(ADMINMAIN+'/express/expresstemplate?co_id='+ data["data"][i]["co_id"])+'">打印模板</a>&nbsp;&nbsp;';
							html += '<a href="'+__URL(ADMINMAIN+'/express/updateexpresscompany?co_id='+ data["data"][i]["co_id"])+ '">修改</a><br/>';
							html += '<a href="'+__URL(ADMINMAIN+'/express/freighttemplatelist?co_id='+ data["data"][i]["co_id"])+'">运费模板</a>&nbsp;&nbsp;';
							html += '<a style="cursor: pointer;" onclick="DelExpressCompany('+data["data"][i]["co_id"]+')">删除</a></td>';
							
					html += '</tr>';
				}
			} else {
				html += '<tr align="center"><td colspan="8">暂无符合条件的数据记录</td></tr>';
			}
			$(".style0list tbody").html(html);
			initPageData(data["page_count"],data['data'].length,data['total_count']);
			$("#pageNumber").html(pagenumShow(jumpNumber,$("#page_count").val(),pageshow));
		}
	});
}

//全选
function CheckAll(event){
	var checked = event.checked;
	$(".style0list tbody input[type = 'checkbox']").prop("checked",checked);
}

function searchData(){
	LoadingInfo(1);
}

function batchDelete() {
	var co_id= new Array();
	$("#productTbody input[type='checkbox']:checked").each(function() {
		if (!isNaN($(this).val())) {
			co_id.push($(this).val());
		}
	});
	if(co_id.length ==0){
		$( "#dialog" ).dialog({
			buttons: {
				"确定,#e57373": function() {
					$(this).dialog('close');
				}
			},
			contentText:"请选择需要操作的记录",
			title:"消息提醒",
		});
		return false;
	}
	DelExpressCompany(co_id);
}

function DelExpressCompany(co_id){
	$( "#dialog" ).dialog({
		buttons: {
			"确定": function() {
				$(this).dialog('close');
				$.ajax({
					type : "post",
					url : __URL(ADMINMAIN+"/express/expresscompanydelete"),
					data : { "co_id" : co_id.toString() },
					dataType : "json",
					success : function(data) {
						if (data["code"] > 0) {
							LoadingInfo(getCurrentIndex(co_id,'#productTbody'));
							showMessage('success', data["message"]);
						}else{
							showMessage('error', data["message"]);
						}
					}
				})
			},
			"取消,#e57373": function() {
				$(this).dialog('close');
			}
		},
		contentText:"删除物流公司，将同时删除该物流公司下的运费模板与打印模板，是否删除物流公司？",
	});
}