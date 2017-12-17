/**
 * 积分中心 2017年2月17日 16:09:13
 */
function getMemberInfo() {

	$.ajax({
		type : "post",
		url : __URL(SHOPMAIN + "/components/getlogininfo"),
		success : function(data) {
			$(".js-membername").text("Hi," + data['member_name']);
			$(".js-member-point").text(data["member_point"]);
		}
	});
}
// 加载数据