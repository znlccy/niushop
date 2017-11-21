/**
 * 倒计时 2017年2月16日 11:54:23
 */
function countDown() {
	if ($("#end_time").val() != null && $("#end_time").val() != '') {
		var day_elem = $(".js-day");
		var hour_elem = $(".js-hour");
		var minute_elem = $(".js-min");
		var second_elem = $(".js-sec");
		// if(typeof end_time == "string")
		var end_time = new Date($("#end_time").val()).getTime(), // 月份是实际月份-1
		sys_second = (end_time - new Date().getTime()) / 1000;
		if(sys_second>1){
			sys_second -= 1;
			var day = Math.floor((sys_second / 3600) / 24);
			var hour = Math.floor((sys_second / 3600) % 24);
			var minute = Math.floor((sys_second / 60) % 60);
			var second = Math.floor(sys_second % 60);
			day_elem && $(day_elem).text(day);// 计算天
			$(hour_elem).text(hour < 10 ? "0" + hour : hour);// 计算小时
			$(minute_elem).text(minute < 10 ? "0" + minute : minute);// 计算分钟
			$(second_elem).text(second < 10 ? "0" + second : second);// 计算秒杀
		}
		var timer = setInterval(function() {
			if (sys_second > 1) {
				sys_second -= 1;
				var day = Math.floor((sys_second / 3600) / 24);
				var hour = Math.floor((sys_second / 3600) % 24);
				var minute = Math.floor((sys_second / 60) % 60);
				var second = Math.floor(sys_second % 60);
				day_elem && $(day_elem).text(day);// 计算天
				$(hour_elem).text(hour < 10 ? "0" + hour : hour);// 计算小时
				$(minute_elem).text(minute < 10 ? "0" + minute : minute);// 计算分钟
				$(second_elem).text(second < 10 ? "0" + second : second);// 计算秒杀
			} else {
				clearInterval(timer);
			}
		}, 1000);
	}
}