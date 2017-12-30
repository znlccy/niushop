/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * ========================================================= Copy right
 * 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ---------------------------------------------- 官方网址:
 * http://www.niushop.com.cn 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * 
 * @author : 小学生王永杰
 * @date : 2017年6月6日 10:35:22 
 * @version : v1.0.0.0 后台公共函数类
 */

/**
 * 操作提示框
 * 2017年6月6日 10:42:22 王永杰
 * 
 * @param msg 提示内容
 * @param flag success：成功，warning：警告,error：失败
 */
function showTip(msg,flag){
	var curr_class = "common-"+flag;
	if($(".js-common-tip").is(":hidden")){
		$(".js-common-tip").removeClass("common-success common-warning common-error")
		.addClass(curr_class)
		.fadeIn(400)
		.children().text(msg);
		setTimeout("$('.js-common-tip').fadeOut()",2000);
	}
}

function SetPriceFormat(price) {
    var r = /\d+/g;
    if (r.test(price)) {
        var reg = /\./g;
        if (!reg.test(price)) {
            price	 = price + ".00";
            return price;
        } else {
            return price;
        }
    }
}

$(function () {
    $("#pageDiv").mouseover(function () {
        if (!$(this).is(":animated")) {
            $(this).animate({ "margin-bottom": "0px" }, 300);
        }
    }).mouseleave(function () {
        if (!$(this).is(":animated")) {
            $(this).animate({ "margin-bottom": "-35px" }, 300);
        }
    })
})

//$(function () {
//    var scroll_top = WhichBro();
//    var tipsWidth = $(".Loading").css("width").replace("px", "");
//    var windowWidth = $(window).width();
//    if (scroll_top < 41) {
//        $(".Loading").css({ "position": "fixed", "top": "41px", "left": (windowWidth - tipsWidth) / 2 + "px", "z-index": "99999", "width": "auto", "min-width": "350px", "height": "37px", "line-height": "37px" });
//    } else {
//        $(".Loading").css({ "position": "fixed", "top": "0px", "left": (windowWidth - tipsWidth) / 2 + "px", "z-index": "99999", "width": "auto", "min-width": "350px", "height": "37px", "line-height": "37px" });
//    }
//    $(window).scroll(function () {
//        var scroll_top = WhichBro();
//        if (scroll_top < 41) {
//            $(".Loading").css({ "position": "fixed", "top": (41 - document.documentElement.scrollTop) + "px" });
//        } else {
//            $(".Loading").css({ "position": "fixed", "top": "0px" });
//        }
//    })
//
//    $(window).resize(function () {
//        var tipsWidth = $(".Loading").css("width").replace("px", "");
//        var windowWidth = $(window).width();
//        scroll_top = WhichBro();
//        $(".Loading").css({ "position": "fixed", "left": (windowWidth - tipsWidth) / 2 + "px" });
//    })
//
//    $(".Loading i").click(function () {
//        $(".Loading").css("display", "none");
//    })
//})
function NotVisible() {
    $(".Loading").css("display", "none");
}

function RedirectTo(url) {
    setTimeout("window.location.href='" + url + "'", 10000);
}

function RedirectToUrlByTime(url, time) {
    setTimeout("window.location.href='" + url + "'", time);
}

function WhichBro() {
    var scroll_top;
    if (document.documentElement && document.documentElement.scrollTop) {
        scroll_top = document.documentElement.scrollTop;
    }
    else if (document.body) {
        scroll_top = document.body.scrollTop; /*某些情况下Chrome不认document.documentElement.scrollTop则对于Chrome的处理。*/
    }
    return scroll_top;
}

var isDelTrue = false;
function confirmThis(event, tips) {
    event = event ? event : window.event;
    var eventSrc = event.srcElement ? event.srcElement : event.target;
    if (isDelTrue == false) {
        var confirmDiv = $("#dvConfirm").html();
        var flag = 0;
        art.dialog({
            okVal: "确定",
            ok: function () {
                isDelTrue = true;
                $(eventSrc).click();
            },
            cancel: true,
            lock: true,
            background: '#000', // 背景色
            opacity: 0.4, // 透明度
            content: confirmDiv,
            drag: false,
            resize: false

        });
        $("#confirmMessage").text(tips);
    } else {
        isDelTrue = false;
        return true;
    }
}


function change() {
    scroll_top = WhichBro();
    $(".Loading").css({ "display": "block" });
    var tipsWidth = $(".Loading")[0].clientWidth;
    tipsWidth = tipsWidth < 350 ? 350 : tipsWidth;
    tipsWidth = tipsWidth > 1027 ? 1027 : tipsWidth;
    var windowWidth = $(window).width();
    $(".Loading").css({ "left": (windowWidth - tipsWidth) / 2 + "px" });
    setTimeout('NotVisible()', 5000);

    //tips全站公用提示信息最多70个字，最长1027像素
    var tips = $("#operateTip").text();
    var tipsLength = tips.length;
    if (tipsLength > 70) {
        tips = tips.substring(0, 70) + "...";
        $("#operateTip").text(tips);
    }
}

function displayNone() {
    $(".Loading").css("display", "none");
}

function GetUrlAppCode() {
    var url = window.location.href;
    var parameter = url.substring(url.indexOf('?') + 1);
    parameter = parameter.split('&');
    var reg = /appCode=/g;
    var appCode = "";
    for (var i = 0; i < parameter.length; i++) {
        reg.lastIndex = 0;
        if (reg.test(parameter[i])) {
            appCode = parameter[i].replace("appCode=", "").replace("#", "");
            break;
        }
    }
    return appCode;
}

function GetUrlMenuCode() {
    var url = window.location.href;
    var parameter = url.substring(url.indexOf('?') + 1);
    parameter = parameter.split('&');
    var reg = /MenuCode=/g;
    var menuCode = "";
    for (var i = 0; i < parameter.length; i++) {
        reg.lastIndex = 0;
        if (reg.test(parameter[i])) {
            menuCode = parameter[i].replace("MenuCode=", "").split('#')[0];
            break;
        }
    }
    return menuCode;
}

function TrimSpace(obj) {
    return obj.replace(/^\s*/g, "").replace(/\s*$/g, "");
}

//function alignCenter(ID) {
//    var divWidth = $("#" + ID + "").width();
//    var windowWidth = $(window).width();
//    $("#" + ID + "").css({"left": (windowWidth - divWidth) / 2 + "px" });
//}
// 对Date的扩展，将 Date 转化为指定格式的String 
// 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符， 
// 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字) 
// 例子： 
// (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423 
// (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18 
Date.prototype.Format = function (fmt) { //author: meizz 
    var o = {
        "M+": this.getMonth() + 1,                 //月份 
        "d+": this.getDate(),                    //日 
        "H+": this.getHours(),                   //小时 
        "m+": this.getMinutes(),                 //分 
        "s+": this.getSeconds(),                 //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds()             //毫秒 
    };
    if (/(y+)/.test(fmt))
        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt))
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}




function popupOperate(popDiv, title, id) {
    var dialog = document.getElementById(popDiv);

    art.dialog({
        id: id,
        title: title,
        lock: true,
        background: '#000', // 背景色
        opacity: 0.4, // 透明度
        content: dialog,
        drag: false,
        resize: false
    });
    // $(".aui_footer").parent().remove();
};

function popupClose(id) {
    art.dialog.list[id].close();
    //  art.dialog.get(id).close();
}



function Show(text, type) {
    if (type == "success") {
        $(".alert-success").append('<button data-dismiss="alert" class="close" type="button">×</button>' + text + '！');
        $(".alert-success").Show();
        setTimeout(hide(type), 3000);
    }

}

function hide(type) {
    if (type == "success") {
        $('.alert-success').popover('hide');
    }

}

//时间戳转时间类型
function timeStampTurnTime(timeStamp){
	if(timeStamp > 0){
		var date = new Date();  
		date.setTime(timeStamp * 1000);  
		var y = date.getFullYear();      
		var m = date.getMonth() + 1;      
		m = m < 10 ? ('0' + m) : m;      
		var d = date.getDate();      
		d = d < 10 ? ('0' + d) : d;      
		var h = date.getHours();    
		h = h < 10 ? ('0' + h) : h;    
		var minute = date.getMinutes();    
		var second = date.getSeconds();    
		minute = minute < 10 ? ('0' + minute) : minute;      
		second = second < 10 ? ('0' + second) : second;     
		return y + '-' + m + '-' + d+' '+h+':'+minute+':'+second;  		
	}else{
		return "";
	}
	    
	//return new Date(parseInt(time_stamp) * 1000).toLocaleString().replace(/年|月/g, "/").replace(/日/g, " ");
}

//函数名：CheckDateTime
//功能介绍：检查是否为日期时间
function CheckDateTime(str){
	var reg = /^(\d+)-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$/;
	var r = str.match(reg);
	if(r==null)return false;
	r[2]=r[2]-1;
	var d= new Date(r[1], r[2],r[3], r[4],r[5], r[6]);
	if(d.getFullYear()!=r[1]) return false;
	if(d.getMonth()!=r[2]) return false;
	if(d.getDate()!=r[3]) return false;
	if(d.getHours()!=r[4]) return false;
	if(d.getMinutes()!=r[5]) return false;
	if(d.getSeconds()!=r[6]) return false;
	return true;
}