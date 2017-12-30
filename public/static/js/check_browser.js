function getOs() {
    var uAgent = navigator.userAgent;
    if (uAgent.indexOf("MSIE") > 0) {
        return "MSIE";
    }
    if (uAgent.indexOf("Firefox") > 0) {
        return "Firefox";
    }
    if (uAgent.indexOf("Safari") > 0) {
        return "Safari";
    }
    if (uAgent.indexOf("Camino") > 0) {
        return "Camino";
    }
    if (uAgent.indexOf("Gecko/") > 0) {
        return "Gecko";
    }

}

function GetIEVersion() {
    var uAgent = navigator.userAgent;
    var browserInfo = uAgent.substring(uAgent.indexOf('('), uAgent.lastIndexOf(')'));
    var browserInfos = browserInfo.split(';');
    var reg = /MSIE/g;
    var ieInfo = "";
    for (var i = 0; i < browserInfos.length; i++) {
        reg.lastIndex = 0;
        if (reg.test(browserInfos[i])) {
            ieInfo = browserInfos[i];
        }
    }
    if (ieInfo != "") {
        ieInfo = ieInfo.replace(/^\s*/g, "").replace(/\s*$/g, "");
        if (ieInfo == "MSIE 8.0") {
            return "IE8";
        } else if (ieInfo == "MSIE 7.0") {
            return "IE7";
        } else if (ieInfo == "MSIE 6.0") {
            return "IE6";
        } else {
            return "";
        }
    }
}  