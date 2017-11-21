/*全选*/
$(".mod-operate-detail").css("display", "none"); //加载的时候先让div隐藏
$(".mod-operate .ico20").css("display", "none"); //加载的时候先让小三角隐藏

function CheckAll(event) {
    event = event ? event : window.event;
    var eventSrc = event.srcElement ? event.srcElement : event.target;
    $("input[name='sub']").prop("checked", $(eventSrc).attr("checked") == "checked" ? "checked" : false);
    var $subs = $("input[name='sub']");
    $subs.parents("tr").removeClass("tr-select");
    $("input[name='sub']:checked").parents("tr").addClass("tr-select");
}
function CheckThis() {
    var $subs = $("input[name='sub']");
    $("#ckall").prop("checked", $subs.length == $subs.filter(":checked").length ? true : false);
    $subs.parents("tr").removeClass("tr-select");
    $("input[name='sub']:checked").parents("tr").addClass("tr-select");

}

function LayerCheckAll(event) {
    event = event ? event : window.event;
    var eventSrc = event.srcElement ? event.srcElement : event.target;
    $("input[name='layersub']").prop("checked", $(eventSrc).attr("checked") == "checked" ? "checked" : false);
    var $layersub = $("input[name='layersub']");
    $layersub.parents("tr").removeClass("tr-select");
    $("input[name='layersub']:checked").parents("tr").addClass("tr-select");
}
function LayerCheckThis() {
    var $layersub = $("input[name='layersub']");
    $("#layerckall").prop("checked", $layersub.length == $layersub.filter(":checked").length ? true : false);
    $layersub.parents("tr").removeClass("tr-select");
    $("input[name='layersub']:checked").parents("tr").addClass("tr-select");
}