
//查看详细和编辑链接的跳转
function GoToLinkUrl(event, url) {
    var linkToUrl = "";
    if (event != undefined && event!="") {
        event = event ? event : window.event;
        var eventSrc = event.srcElement ? event.srcElement : event.target;
    }
    if (url !== "" && url !== undefined) {
        linkToUrl = url;
    }
    else {
        linkToUrl = $(eventSrc).attr("linkUrl");

    }
    var linkUrl = linkToUrl;
    var linkUrlParts = linkUrl.split('?');
    if (linkUrlParts.length > 1) {//原本已经带参数
        linkUrl = linkUrlParts[0] + "?appCode=" + appCode + "&" + "MenuCode=" + menuCode + "&" + linkUrlParts[1];
    } else {
        linkUrl = linkUrl + "?appCode=" + appCode + "&" + "MenuCode=" + menuCode;
    }

    window.location.href = linkUrl;
}