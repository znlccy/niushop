var orderChartsTitle = '<span class="charts-title order" onclick="onloadOrderChart(1)">今日</span>';
orderChartsTitle += '<span class="charts-title order black" onclick="onloadOrderChart(2)">昨日</span>';
orderChartsTitle += '<span class="charts-title order black" onclick="onloadOrderChart(3)">本周</span>';
orderChartsTitle += '<span class="charts-title order black" onclick="onloadOrderChart(4)">本月</span>';
//orderChartsTitle += '<span class="charts-title-ordernum">订单总数<i>0</i></span>';

var weixinChartsTitle = '<span class="charts-title fans" onclick="onloadWeiXinFansChart(1)">今日</span>';
weixinChartsTitle += '<span class="charts-title fans black" onclick="onloadWeiXinFansChart(2)">昨日</span>';
weixinChartsTitle += '<span class="charts-title fans black" onclick="onloadWeiXinFansChart(3)">本周</span>';
weixinChartsTitle += '<span class="charts-title fans black" onclick="onloadWeiXinFansChart(4)">本月</span>';
var chart,salesStatistical,weixinChart,weixinStatistical,weixinTimeStatistical,weixinOption;
$(function() {
	// 加载页面Title以及用户名称
	// 1--获取当前时间
	/*
	 * var title; var myDate = new Date(); var dayHour = myDate.getHours(); if
	 * (parseInt(dayHour) >= 0 && parseInt(dayHour) < 12) { title = "早上好，"; }
	 * else if (parseInt(dayHour) >= 12 && parseInt(dayHour) < 18) { title =
	 * "下午好，"; } else if (parseInt(dayHour) >= 18 && parseInt(dayHour) < 24) {
	 * title = "晚上好，"; } $("#Stitle").text(title);
	 */
	// 临时隐藏面包屑和选项卡，后期会对后台首页进行整体界面修改 2016年11月15日 09:32:21 王永杰
	// 销售统计
	onloadOrderChart(1);
	onloadWeiXinFansChart(1);
	// 关注人数统计
	var focusNumberStatistical = [ 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
			0, 0, 0, 0, 0, 0, 0, 0, 0, 0 ];
	chart = new Highcharts.Chart({
		chart : {
			type : 'column',
			renderTo : 'orderCharts'
		},
		title : {
			text : orderChartsTitle,
			align : "left",
			useHTML : true
		},
		lang : {
			printChart : "打印",
			downloadPNG : "导出PNG格式图片",
			downloadJPEG : "导出JPEG格式图片",
			downloadPDF : "导出PDF格式图片",
			downloadSVG : "导出SVG格式图片"
		},
		xAxis : {
			type : 'category',
			labels : {
				rotation : -45,
				style : {
					fontSize : '13px',
					fontFamily : 'Verdana, sans-serif'
				}
			}
		},
		yAxis : {
			min : 0,
			title : {
				text : '订单统计'
			}
		},
		legend : {
			enabled : false
		},
		credits : {
			enabled : false,// 默认值，如果想去掉版权信息，设置为false即可
			text : PLATFORM_NAME, // 显示的文字
			href : '#', // 链接地址
		},
		series : [ {
			name : '订单统计',
			data : salesStatistical,
			dataLabels : {
				enabled : false,
				color : '#FFFFFF',
				align : 'right',
				format : '{point.y:.1f}', // one decimal
				y : 0, // 10 pixels down from the top
				style : {
					fontSize : '12px',
					fontFamily : 'Verdana, sans-serif'
				}
			}
		} ]
	});

	var focusChartsTitle = '<span class="charts-title">今日</span>';
	focusChartsTitle += '<span class="charts-title black">昨日</span>';
	focusChartsTitle += '<span class="charts-title black">本周</span>';
	focusChartsTitle += '<span class="charts-title black">本月</span>';
	/*focusChartsTitle += '<span class="charts-title-ordernum">关注用户总数<i>0</i></span>';
	focusChartsTitle += '<span class="charts-title-ordernum">浏览用户总数<i>0</i></span>';*/
	weixinTimeStatistical = [ '00:00', '01:00', '02:00', '03:00', '04:00',
								'05:00', '06:00', '07:00', '08:00', '09:00',
								'10:00', '11:00', '12:00', '13:00', '14:00',
								'15:00', '16:00', '17:00', '18:00', '19:00',
								'20:00', '21:00', '22:00', '23:00' ];
	weixinChart = new Highcharts.Chart("focusCharts",{
	//$('#focusCharts').highcharts({
				/*chart : {
					type : 'spline',
					renderTo : 'focusCharts'
				},*/
				title : {
					text : weixinChartsTitle,
					align : "left",
					useHTML : true
				},
				xAxis : {
					categories : weixinTimeStatistical
				},
				lang : {
					printChart : "打印",
					downloadPNG : "导出PNG格式图片",
					downloadJPEG : "导出JPEG格式图片",
					downloadPDF : "导出PDF格式图片",
					downloadSVG : "导出SVG格式图片"
				},
				yAxis : {
					title : {
						text : ''
					},
					plotLines : [ {
						value : 0,
						width : 1,
						color : '#808080'
					} ]
				},
				tooltip : {
				// valueSuffix : '°C'
				},
				legend : {
					enabled : false
				},
				credits : {
					enabled : false,// 默认值，如果想去掉版权信息，设置为false即可
					text : '牛酷商城', // 显示的文字
					href : '#', // 链接地址
				},
				exporting : {
					allowHTML : true,
					buttons : {
						contextButton : {
						// text : '导出'
						}
					}
				},
				series : [ {
					name : '今日',
					data : weixinStatistical
				}
				/*
				 * , { name : '昨日', data : [ -0.2, 0.8, 5.7, 11.3, 17.0, 22.0,
				 * 24.8, 24.1, 20.1, 14.1, 8.6, 2.5 ] }, { name : '本周', data : [
				 * -0.9, 0.6, 3.5, 8.4, 13.5, 17.0, 18.6, 17.9, 14.3, 9.0, 3.9,
				 * 1.0 ] }, { name : '本月', data : [ 3.9, 4.2, 5.7, 8.5, 11.9,
				 * 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8 ] }
				 */
				]
			});
});
$(function(){
	onloadGoodsCount();//商品数量
	onloadOrderCount();//订单数量
	onloadSalesStatistics();//销售统计
	onloadConsultCount();//咨询回复
	onloadMemberBalanceWithdrawCount();//会员提现审核中
	onloadWeiXinFansCount();//加载关注人数，新界面
});

/**
 * 加载会员提现审核中的数量
 * 2017年7月10日 12:12:51 王永杰
 */
function onloadMemberBalanceWithdrawCount(){
	$.ajax({
		type : 'post',
		url : __URL(ADMINMAIN + "/index/getMemberBalanceWithdrawCount"),
		success : function(data){
			exeryTimePut("member_balance_withdraw", data);
		}
	});
}

function onloadGoodsCount(){
	$.ajax({
		type : "post",
		url : __URL(ADMINMAIN+"/index/getgoodscount"),
		success : function(data) {
			if(data['all']!=null){
				$(".goods_all_count").html(data['all']+'/不限');
				exeryTimePut("js-goods-release-count", parseInt(data['all']));
			}
			//$(".goods_audit_count").html();
			//$(".goods_sale_count").html(data['sale']);
			//$(".goods_shelf_count").html(data['shelf']);
			exeryTimePut("goods_sale_count", parseInt(data['sale']));
			exeryTimePut("goods_audit_count", parseInt(data['audit']));
			exeryTimePut("goods_shelf_count", parseInt(data['shelf']));
			exeryTimePut("stock_early_warning", parseInt(data['warning']));
		}
	});
}
function onloadOrderCount(){
	$.ajax({
		type : "post",
		url : __URL(ADMINMAIN+"/index/getordercount"),
		success : function(data) {
			/*$(".daifukuan").html(data['daifukuan']);
			$(".daifahuo").html(data['daifahuo']);
			$(".yifahuo").html(data['yifahuo']);
			$(".yishouhuo").html(data['yishouhuo']);
			$(".yiwancheng").html(data['yiwancheng']);
			$(".yiguanbi").html(data['yiguanbi']);
			$(".tuikuanzhong").html(data['tuikuanzhong']);
			$(".yituikuan").html(data['yituikuan']);
			$(".charts-title-ordernum i").html(data['all']);*/
			
			exeryTimePut("daifukuan",data['daifukuan'])
			exeryTimePut("daifahuo",data['daifahuo']);
			exeryTimePut("yifahuo",data['yifahuo']);
			exeryTimePut("yishouhuo",data['yishouhuo']);
			exeryTimePut("yiwancheng",data['yiwancheng']);
			exeryTimePut("js-order-finish-count",data['yiwancheng'])
			exeryTimePut("yiguanbi",data['yiguanbi']);
			exeryTimePut("tuikuanzhong",data['tuikuanzhong']);
			exeryTimePut("yituikuan",data['yituikuan']);
			exeryTimePut("charts-title-ordernum",data['all']);
			exeryTimePut("js-order-total",data['all']);
			
		}
	});
}
function onloadSalesStatistics(){
	$.ajax({
		type : "post",
		url : __URL(ADMINMAIN+"/index/getsalesstatistics"),
		success : function(data) {
//			console.log(data);
			var curr_day_money = 0;//今天订单总金额
			var yesterday_money = 0;//昨日订单金额（元）
			var month_money = 0;//本月订单金额（元）
			
			if(data['curr_day_money'] != undefined && data['curr_day_money'] != "") curr_day_money = data['curr_day_money'];
			if(data['yesterday_money'] != undefined && data['yesterday_money'] != "") yesterday_money = data['yesterday_money'];
			if(data['month_money'] != undefined && data['month_money'] !="") month_money = data['month_money'];
			
			$(".js-order-amount").html(parseFloat(curr_day_money).toFixed(2));//今天订单总金额
			$(".yesterday_goods").html(data['yesterday_goods']);//昨日销量（订单量[件]）
			
			$(".yesterday_money").html(parseFloat(yesterday_money).toFixed(2));//昨日订单金额（元）
			
			$(".month_goods").html(data['month_goods']);//本月销量（订单量[件]）
			$(".month_money").html(parseFloat(month_money).toFixed(2));//本月订单金额（元）
			
			exeryTimePut("js-month-sales",parseInt(data['month_goods']));//本月销量（订单量[件]）
		}
	});
}


function onloadOrderChart(e) {
	
	$("#orderCharts .order").addClass('black');
	$("#orderCharts .order").eq(e-1).removeClass('black');
	$.ajax({
		type : "post",
		url : __URL(ADMINMAIN+"/index/getorderchartcount"),
		data : {'date':e},
		success : function(data) {
			
//			salesStatistical = [ [ '00:00', 10 ], [ '01:00', 20 ], [ '02:00', 30 ],
//				         			[ '03:00', 40 ], [ '04:00', 50 ], [ '05:00', 60 ], [ '06:00', 70 ],
//				         			[ '07:00', 80 ], [ '08:00', 90 ], [ '09:00', 100 ],
//				         			[ '10:00', 110 ], [ '11:00', 120 ], [ '12:00', 130 ] ];
			salesStatistical = data;
			var series = chart.series;
			while (series.length > 0) {
				series[0].remove(false);
			}
			chart.addSeries({
				name : "订单数量",
				data : salesStatistical
			}, false);
			chart.redraw();
		}
	});
}
	
//定时器
function exeryTimePut(tag_name, num){
	if(num > 0){
		var number = 1;
		$('body').everyTime('0.01s','B',function(){
			$("."+tag_name).text(number);
			number++;
			if(number > 100){
				$("."+tag_name).text(num);
				return false;
			}
		},parseInt(num));
	}
}
//咨询回复
function onloadConsultCount(){
	$.ajax({
		type : "post",
		url : __URL(ADMINMAIN+"/index/getconsultcount"),
		success : function(data) {
			exeryTimePut("goods_consult_count",data);
		}
	});
}

/**
 * 加载关注人数新界面
 */
function onloadWeiXinFansCount(){
	$.ajax({
		type : "post",
		url : __URL(ADMINMAIN + "/index/getWeiXinFansCount"),
		success : function(count){
			if(count>0){
				$(".js-weixin-fans-count").text(count);
				exeryTimePut("js-weixin-fans-count",count);
			}
		}
	});
}

/**
 * fans关注图标数据
 * @param e
 */
function onloadWeiXinFansChart(e) {
	
	$(".fans").addClass('black');
	$(".fans").eq(e-1).removeClass('black');
	$.ajax({
		type : "post",
		url : __URL(ADMINMAIN+"/index/getweixinfanschartcount"),
		data : {'date':e},
		async : true,
		success : function(data) {
			var timeOut = "关注人数";
			if(e == 1){
				timeOut = "今日";
			}else if(e == 2){
				timeOut = "昨日";
			}else if(e == 3){
				timeOut = "本周";
			}else if(e == 4){
				timeOut = "本月";
			}
			
			weixinTimeStatistical = data[0];
			weixinChart.update({
				xAxis : {
					categories : weixinTimeStatistical
				},
				series : [ {
					name : timeOut,
					data : data[1]
				}]
			})
		}
	});
}