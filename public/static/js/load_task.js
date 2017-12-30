$(function(){
	$.ajax({
		url : __URL(APPMAIN+"/task/load_task"),
		type : "post",
		dataType : "json",
		success : function(data) {
		}
	});
});