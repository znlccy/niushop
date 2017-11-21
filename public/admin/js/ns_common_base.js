// 模态框拖动
$(function() {
	$(".checkbox").simpleSwitch({
		"theme": "FlatRadius"
	});
	
	$(".modal-header").mousedown(function(){
		$("div[role='dialog']").draggable();
	})

	$.ajax({
		type : "post",
		url : __URL(ADMINMAIN+"/upgrade/isneedtoupgrade"),
		success : function(data) {
			if (data['code'] == 0) {
				$(".is-upgrade").show();
			}
		}
	});
});

// 弹出修改密码的弹窗
function editpassword() {
	setObjectVerticalCenter($('#edit-password'));
	$('#edit-password').modal('show');
}

// 保存修改密码的按钮
function submitPassword() {
	var pwd0 = $("#pwd0").val();
	var pwd1 = $("#pwd1").val();
	var pwd2 = $("#pwd2").val();
	if (pwd0 == '') {
		$("#pwd0").focus();
		$("#pwd0").siblings("span").html("原密码不能为空");
		return;
	} else {
		$("#pwd0").siblings("span").html("");
	}

	if (pwd1 == '') {
		$("#pwd1").focus();
		$("#pwd1").siblings("span").html("密码不能为空");
		return;
	} else if ($("#pwd1").val().length < 6) {
		$("#pwd1").focus();
		$("#pwd1").siblings("span").html("密码不能少于6位数");
		return;
	} else {
		$("#pwd1").siblings("span").html("");
	}
	if (pwd2 == '') {
		$("#pwd2").focus();
		$("#pwd2").siblings("span").html("密码不能为空");
		return;
	} else if ($("#pwd2").val().length < 6) {
		$("#pwd2").focus();
		$("#pwd2").siblings("span").html("密码不能少于6位数");
		return;
	} else {
		$("#pwd2").siblings("span").html("");
	}
	if (pwd1 != pwd2) {
		$("#pwd2").focus();
		$("#pwd2").siblings("span").html("两次密码输入不一样，请重新输入");
		return;
	}
	$.ajax({
		url : __URL(ADMINMAIN+"/login/modifypassword"),
		type : "post",
		data : {
			"old_pass" : $("#pwd0").val(),
			"new_pass" : $("#pwd1").val()
		},
		dataType : "json",
		success : function(data) {
			if (data['code'] > 0) {
				$("#show").html('<span style="color:green">密码修改成功</span>');
				location.reload();
			} else {
				$("#show").html( '<span style="color:red">' + data['message'] + '</span>');
			}
		}
	});
}

function delcache() {
	$.ajax({
		url : __URL(ADMINMAIN+"/system/deletecache"),
		type : "post",
		data : {},
		dataType : "json",
		success : function(data) {
			if (data) {
				showMessage('success', '缓存更新成功');
			} else {
				showMessage('error', '更新失败，请检查文件权限');
			}
		}
	});
}

// 查询
function search() {
	var search_info = $("#search_goods").val();
	window.location.href = __URL(ADMINMAIN+"/goods/goodslist?search_info=" + search_info);
}

//左右去空格
function trim(str){
//	return str.replace(/(^\s*)|(\s*$)/g, "");
	return str;
}
$("#search_goods").keyup(function(event){
	if(event.keyCode == 13) search();
})