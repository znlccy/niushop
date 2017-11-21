function roleClose() {
    popupClose('gray-add-role');
};
function btn() {
	$("#btn").attr("disabled",true);
    sendDatas();
    $("#addSubmit").submit();
}
$(function () {
    $("#addrole").click(function () {
        popupOperate("gray-add-role", "用户组设置", "gray-add-role");
        $("[name = add_per]:checkbox").attr("checked", false);
    })
});
// 点击提交时将选中的数据提交到后台
function sendDatas() {
    // 方案
    // ①：要确定是哪个 title
    // ②：取到当前 son 的id
    // ③:往后台传递的数据为 title+ids
    // 截取后是0_8 就是平台  是9_77 就是其他
    var SendDatas = "";
    var checks=$("input[name=add_per]");
    // 遍历所有的checkbox
    for (var i = 0; i < checks.length; i++) {
        // 首先必须是选中的
        if ($(checks[i]).attr("checked") == "checked") {
            // 里面的字符必须包含 ‘|’的
            var strCheckID = $(checks[i]).attr("id");
            if(strCheckID!='Add01')
            {	
                SendDatas += strCheckID + ",";
            }
        }
    };
    SendDatas=SendDatas.substring(0,SendDatas.length-1);
        // 并放到隐藏域中
        $("#sendCheckDatas").val(SendDatas);   
        add_RoleManage();
};
// 注册"所有"复选框点击的时候
function AllCheckBoxClick(event) {
    event = event ? event : window.event;
    var eventSrc = event.srcElement ? event.srcElement : event.target;
    // 当选中的是title
    if ($(eventSrc).attr("dir") == "title") {
        // 控制旗下的所有复选框
        // 选中旗下所有的checkBox
        if ($(eventSrc).attr("checked") == "checked") {

            $("input[type=checkbox]", $(eventSrc).parent("li")).not($(eventSrc)).attr("checked", "checked");
        } else {
            $("input[type=checkbox]", $(eventSrc).parent("li")).not($(eventSrc)).attr("checked", false);
        }
    }
    // 当选中的是parent
    if ($(eventSrc).attr("dir") == "parent") {
        // 选中旗下所有的checkBox
        if ($(eventSrc).attr("checked") == "checked") {
            $("input[type=checkbox]", $(eventSrc).parents("tr")[0]).not($(eventSrc)).attr("checked", "checked");
        } else {
            $("input[type=checkbox]", $(eventSrc).parents("tr")[0]).not($(eventSrc)).attr("checked", false);
        }
    }
    // 当选中的是son
    if ($(eventSrc).attr("dir") == "son") {
    	if ($(eventSrc).attr("checked") == "checked") {
    		$(eventSrc).parent().parent().parent().parent().find("[dir='son']").prop("checked",true);
    		$(eventSrc).parent().parent().parent().parent().parent().parent().find("[dir='parent']").prop("checked",true);
            $("input[type=checkbox]", $(eventSrc).parents("ul.second")[0]).not($(eventSrc)).attr("checked", "checked");
        } else {
            $("input[type=checkbox]", $(eventSrc).parents("ul.second")[0]).not($(eventSrc)).attr("checked", false);
        }
    	// 选中旗下所有的checkBox
        
    }
    if ($(eventSrc).attr("dir") == "sonson") {   	
    	if ($(eventSrc).attr("checked") == "checked") {
    		$(eventSrc).parent().parent().parent().parent().find("[dir='son']").prop("checked",true);
    		$(eventSrc).parent().parent().parent().parent().parent().parent().find("[dir='parent']").prop("checked",true);
           // $("input[type=checkbox]", $(eventSrc).parents("ul.second")[0]).not($(eventSrc)).attr("checked", "checked");
        } else {
        	var parentObj=$(eventSrc).parent().parent().parent().parent().find("[dir='sonson']:checked");
        	var parentsObj=$(eventSrc).parent().parent().parent().parent().parent().parent().find("[dir='parent']:checked");
        	var num = parentObj.length;
        	var nums = parentsObj.length;
//        	alert(num);
//        	alert(nums);
        	if(num ==0){
        		$(eventSrc).parent().parent().parent().parent().find("[dir='son']").prop("checked",false);
        	}
        	if(nums ==0){
        		$(eventSrc).parent().parent().parent().parent().parent().parent().find("[dir='parent']").prop("checked",false);
        	}
        	//$(eventSrc).parent().parent().parent().find("[dir='son']").prop("checked",true);
            //$("input[type=checkbox]", $(eventSrc).parents("ul.second")[0]).not($(eventSrc)).attr("checked", false);
        }
    	// 选中旗下所有的checkBox
        
    }
};
//-------------------------------------------------------------------修改----------------------------------------------------------

// 点击提交时将选中的数据提交到后台
function EditsendDatas() {
    // 方案
    // ①：要确定是哪个 title
    // ②：取到当前 son 的id
    // ③:往后台传递的数据为 title+ids
    // 截取后是0_8 就是平台  是9_77 就是其他
    var SendDatas = "";
    var checks = $("input[name=permiss]");
    // 遍历所有的checkbox
    for (var i = 0; i < checks.length; i++) {
        // 首先必须是选中的
        if ($(checks[i]).attr("checked") == "checked") {
            // 里面的字符必须包含 ‘|’的
        	
            var strCheckID = $(checks[i]).attr("id");
            if(strCheckID!='Edit01')
            {	
                SendDatas += strCheckID + ",";
            }
        }
    };
    SendDatas=SendDatas.substring(0,SendDatas.length-1);
        // 并放到隐藏域中
        $("#EditsendCheckDatas").val(SendDatas);
        update_RoleManage();
};
// 点击取消的时候
function btnCancel() {
    popupClose('gray-edit-role');
};
// 注册"所有"复选框点击的时候
function EditAllCheckBoxClick(event) {
    event = event ? event : window.event;
    var eventSrc = event.srcElement ? event.srcElement : event.target;
    // 当选中的是title
    if ($(eventSrc).attr("dir") == "top") {
        // 控制旗下的所有复选框
        // 选中旗下所有的checkBox
        if ($(eventSrc).attr("checked") == "checked") {
            $("input[type=checkbox]", $(eventSrc).parent("li")).not($(eventSrc)).attr("checked", "checked");
        } else {
            $("input[type=checkbox]", $(eventSrc).parent("li")).not($(eventSrc)).attr("checked", false);
        }

    }
    // 当选中的是parent
    if ($(eventSrc).attr("dir") == "parent") {
        // 选中旗下所有的checkBox
        if ($(eventSrc).attr("checked") == "checked") {
            $("input[type=checkbox]", $(eventSrc).parents("tr")[0]).not($(eventSrc)).attr("checked", "checked");
        } else {
            $("input[type=checkbox]", $(eventSrc).parents("tr")[0]).not($(eventSrc)).attr("checked", false);
        }
    }
    // 当选中的是son
    if ($(eventSrc).attr("dir") == "son") {
        //alert('son');
    }
    if ($(eventSrc).attr("dir") == "sonson") {   	
    	if ($(eventSrc).attr("checked") == "checked") {
    		$(eventSrc).parent().parent().parent().parent().find("[dir='son']").prop("checked",true);
    		$(eventSrc).parent().parent().parent().parent().parent().parent().find("[dir='parent']").prop("checked",true);
           // $("input[type=checkbox]", $(eventSrc).parents("ul.second")[0]).not($(eventSrc)).attr("checked", "checked");
        }else {
        	var parentObj=$(eventSrc).parent().parent().parent().parent().find("[dir='sonson']:checked");
        	var parentsObj=$(eventSrc).parent().parent().parent().parent().parent().parent().find("[dir='parent']:checked");
        	var num = parentObj.length;
        	var nums = parentsObj.length;
        	if(num ==0){
        		$(eventSrc).parent().parent().parent().parent().find("[dir='son']").prop("checked",false);
        	}
        	if(nums ==0){
        		$(eventSrc).parent().parent().parent().parent().parent().parent().find("[dir='parent']").prop("checked",false);
        	}
        	
        	//$(eventSrc).parent().parent().parent().find("[dir='son']").prop("checked",true);
            //$("input[type=checkbox]", $(eventSrc).parents("ul.second")[0]).not($(eventSrc)).attr("checked", false);
        }
    }
};
function Editbtn() {
    EditsendDatas();
    $("#EditSubmit").submit();
}