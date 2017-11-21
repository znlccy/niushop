var editor;
var moreText;
var radiosChecked ="";
//初始化
function InitRadios() {
    //初始化赋值
    var radios = document.getElementsByName("ImageText");
    for (var i = 0; i < radios.length; i++) {
        if (radios[i].checked) {
            radiosChecked = radios[i].value;
            if (radiosChecked == "1") {
                $("#text").show();                
                $("#simpleText").hide();
                $("#moreText").hide();
            }
            else if (radiosChecked == "2") {
                $("#simpleText").show();
                $("#text").hide();
                $("#moreText").hide();
            } else if (radiosChecked == "3") {
                $("#moreText").show();
                $("#text").hide();
                $("#simpleText").hide();
            }
        }
    }
}
//克隆实体类
function clone(myObj) {
    if (typeof (myObj) != 'object') return myObj;
    if (myObj == null) return myObj;
    var myNewObj = new Object();
    for (var i in myObj) myNewObj[i] = clone(myObj[i]);
    return myNewObj;
}
//单图文上传
function FileUpload(event) {
    event = event ? event : window.event;
    var eventSrc = event.srcElement ? event.srcElement : event.target;
    var file_upload = "";
    var file_upload_img = "";
    if ($(eventSrc).attr("id") == "file_upload") {
        file_upload = "file_upload";
        file_upload_img = "file_upload_img_1";
    }    
   $.ajaxFileUpload({
       url: 'File_Upload', //用于文件上传的服务器端请求地址
       secureuri: false, //一般设置为false
       fileElementId: file_upload, //文件上传空间的id属性  <input type="file" id="file" name="file" />
       dataType: 'text', //返回值类型 一般设置为json
       success: function (res) //服务器成功响应处理函数
       {
    	   var $arry=res.split(',');
           $("#spanImg").text("");
           var img = $("<img class='nopic' style='line-height: 150px;'>").attr("src", root+"/" + $arry[0] + "").attr("id", "MoreImage_file_upload_img");
           $("#spanImg").append(img);
           $("#hidCoverUrl").val($arry[1]);
           if (tag == undefined || tag == "0") {
               $("#hidCoverUrl0").val($("#MoreImage_file_upload_img").attr("src").replace(root+"/", ""));
               //显示封面图片
               $("#coverImg").text("");
               var coverimg = $("<img class='simpletext-emulation-img-text'>").attr("src", root+"/" + $arry[0] + "").attr("id", "coverimgs");
               $("#coverImg").append(coverimg);
           }         
       }
   });
}
//多图文上传
function MoreImageFileUpload() {
    //图片上传
    $.ajaxFileUpload({
        url: 'file_uploadmany', //用于文件上传的服务器端请求地址
        secureuri: false, //一般设置为false
        fileElementId: "MoreImage_file_upload", //文件上传空间的id属性  <input type="file" id="file" name="file" />
        dataType: 'text', //返回值类型 一般设置为json
        success: function (res) //服务器成功响应处理函数
        {
            var reg = /\.[a-z]{3,4}/g;
            if (reg.test(res)) {
                //清除原有的
                $("#MorespanImg").text("");
                var img = $("<img class='nopic' style='line-height: 150px;'>").attr("src", root+"/" + res + "").attr("id", "MoreImage_file_upload_img");
                $("#MorespanImg").append(img);
                if (tag == undefined || tag == "0") {
                    $("#hidCoverUrl0").val($("#MoreImage_file_upload_img").attr("src").replace(root+"/", ""));
                    //显示封面图片
                    $("#MoreCoverImg").text("");
                    var coverimg = $("<img class='simpletext-emulation-img-text'>").attr("src", root+"/" + res + "").attr("id", "coverimgs");
                    $("#MoreCoverImg").append(coverimg);
                }
                else {
                    $("#hidCoverUrl" + tag + "").val($("#MoreImage_file_upload_img").attr("src").replace(root+"/", ""));
                    $("#thumbnail" + tag + "").text("");
                    var thumbnail = $("<img style='width:78px;height:78px;'>").attr("src", root+"/" + res + "").attr("id", "thumbnailimgs");
                    $("#thumbnail" + tag + "").append(thumbnail);
                }
            }
        }
    });
}
$(function () {
    //点击 封面图片显示在正文
    $("#showmaintext").click(function () {
        if (tag == undefined || tag == "0") {
            if ($(this).attr("checked") == "checked") {
                $("#hidIsImg0").val("true");
            } else {
                $("#hidIsImg0").val("false");
            }
        } else {
            if ($(this).attr("checked") == "checked") {
                $("#hidIsImg"+tag+"").val("true");
            } else {
                $("#hidIsImg"+tag+"").val("false");
            }
        }
    });
    //创建图文
    $("#AddCover").click(function () {
        var index = $("#MoreVessel .moretext-emulation02:last-child").attr("dir");//lastChild                
        if ($("#MoreVessel").children().length + 1 > 8) {
            $(".Loading").removeClass("style03 style01").addClass("style02");
            $("#operateTip").html("你最多只可以加入8条图文消息").change();
            return;
        }
        index++;
        tagcount=tagcount+1;
        var str = '<div class="moretext-emulation02" dir=' + index + '>';
        str += '<i style="display: none;" class="arrow"></i>';
        str += '<div class="moretext-emulation02-mask">';
        str += '<a data-id="' + index + '" class="moretext-emulation02-operate-edit" onclick="Editing(event)" title="编辑" href="javascript:void(0)">编辑</a>';
        str += '<a class="moretext-emulation02-operate-del l10" onclick="DelectImageText(event)" title="删除" href="javascript:void(0)">删除</a></div>';
        str += '<div class="moretext-emulation02-box">';
        str += '<div class="moretext-emulation02-img" id="thumbnail' + index + '">缩略图</div>';
        str += '<div class="moretext-emulation02-text">标题</div>  </div>';
        str += '<input type="hidden" id="hidTitle' + index + '"><input type="hidden" id="hidAuthor' + index + '"><input type="hidden" id="hidCoverUrl' + index + '"><input type="hidden" id="hidText' + index + '"> <input type="hidden" id="hidTheOriginalLink' + index + '"><input type="hidden" id="hidIsImg' + index + '" /></div>';
        $("#MoreVessel .moretext-emulation02:last-child").after(str);
    });
    $("#simpleText  [name='rdUrl']").change(function () {
        rdUrl = $(this).val();
        if ($(this).val() == "1") {
            $("#divSimpleJump").show();
        } else {
            $("#divSimpleJump").hide();
        }
    });
});
//记录点击的是哪一个编辑
var tag;
var tagcount=2;
function eventEditor(oneself) {
    //编辑显示箭头
    $("#MoreVessel i").css("display", "none");
    $("#MoreVessel>div a:contains('编辑')").each(function () {
        if ($(this).attr("data-id") == $(oneself).attr("data-id")) {
            if ($(this).parent().parent().index() == 0) {
                $(".moretext-form").css("top", "0");
                $(oneself).parent().siblings("i").css("display", "block");
            }
            else if ($(this).parent().parent().index() == 1) {
                $(".moretext-form").css("top", "184px");
                $(oneself).parent().siblings("i").css("display", "block");
            }
            else if ($(this).parent().parent().index() == 2) {
                $(".moretext-form").css("top", "287px");
                $(oneself).parent().siblings("i").css("display", "block");
            }
            else if ($(this).parent().parent().index() == 3) {
                $(".moretext-form").css("top", "390px");
                $(oneself).parent().siblings("i").css("display", "block");
            }
            else if ($(this).parent().parent().index() == 4) {
                $(".moretext-form").css("top", "16px");
                $(oneself).parent().siblings("i").css("display", "block");
            }
            else if ($(this).parent().parent().index() == 5) {
                $(".moretext-form").css("top", "119px");
                $(oneself).parent().siblings("i").css("display", "block");
            }
            else if ($(this).parent().parent().index() == 6) {
                $(".moretext-form").css("top", "222px");
                $(oneself).parent().siblings("i").css("display", "block");
            }
            else if ($(this).parent().parent().index() == 7) {
                $(".moretext-form").css("top", "325px");
                $(oneself).parent().siblings("i").css("display", "block");
            }
        }
    });
}