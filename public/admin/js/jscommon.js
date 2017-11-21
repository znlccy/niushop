// js去除前后端空白字符
String.prototype.trim = function () {
    // 用正则表达式将前后空格  
    // 用空字符串替代。  
    return this.replace(/(^\s*)|(\s*$)/g, "");
}
/*==========一些常用函数================*/
function isbool(obj) {
    var val = $.trim($(obj).val());
    $(obj).val(val);
    val = val.toLowerCase();
    if (val != "0" && val != "1" && val != "false" && val != "true") {
        $(".Loading").removeClass("style0red style0green").addClass("style0yellow");
        $("#operateTip").html("您输入的类型不正确，应该为布尔型 true/false 1/0").change();
        obj.focus();
        return false;
    }
    return true;
}
//判断是否为数字（整数、小数）
function IsNum(obj) {
    var val = $.trim($(obj).val());
    var r = /^\d+(\.\d+|\d*)$/g;
    if (r.test(val) == false) {
        $(obj).focus();
        return false;
    }
    return true;
}

//判断是否为正整数
function IsPositiveNum(obj) {
    var val = $.trim($(obj).val());
    var r = /^\d+$/g;
    if (r.test(val) == false) {
        $(obj).focus();
        return false;
    }
    return true;
}



function isint1(obj) {
    var val = $.trim($(obj).val());
    $(obj).val(val);
    var r = /^(-)?[0-9]*[0-9][0-9]*$/;
    if (r.test(val) == false) {
        obj.focus();
        return false;
    }
    return true;
}
function isvalint1(intval) {
    var r = /^(-)?[0-9]*[0-9][0-9]*$/;
    if (r.test(intval) == false) {
        return false;
    }
    return true;
}

function isnumeric(obj) {
    var val = $.trim($(obj).val());
    $(obj).val(val);
    var r = /^(-)?\d*(\.\d*)?$/;
    if (r.test(val) == false) {
    $(".Loading").removeClass("style0red style0green").addClass("style0yellow");
    $("#operateTip").html("您输入的类型不正确，应该为数字类型").change();
        obj.focus();
        return false;
    }
    return true;
}
function isnumeric2(obj) {
    var val = $.trim($(obj).val());
    $(obj).val(val);
    var r = /^(-)?\d*(\.\d*)?$/;
    if (r.test(val) == false) {
        obj.focus();
        return false;
    }
    return true;
}

function isnumeric1(obj, dec) {
    var val = $.trim($(obj).val());
    $(obj).val(val);
    var intlen = $(obj).attr("MaxLength") - 1 - dec;
    var r = new RegExp("^(-)?\\d{1," + intlen + "}(\\.\\d{1," + dec + "})?$");
    if (r.test(val) == false) {
        obj.focus();
        return false;
    }
    return true;
}

//只能为正的数字类型
function isnumeric3(obj, dec) {
    var val = $.trim(obj.val());
    obj.val(val);
    var intlen = obj.attr("maxlength") - 1 - dec;
    var decstrlen = 0;
    var intstrlen = 0;
    if (val.indexOf('.') != -1) {
        decstrlen = val.substr(val.indexOf('.') + 1).length;
        intstrlen = val.substr(0, val.indexOf('.')).length;
    }
    else {
        intstrlen = val.length;
    }
    if (intstrlen > intlen || decstrlen > dec) {
        obj.focus();
        return false;
    }
    return true;
}


function isdatetime(obj) {
    var val = $.trim($(obj).val());
    $(obj).val(val);
    var reg = /^(\d{4})-(\d{1,2})-(\d{1,2})((\s+)(\d{1,2}):(\d{1,2})(:(\d{1,2})(.(\d{1,3}))?)?)?$/;
    var r = val.match(reg);
    if (r != null) {
        r[2] = r[2] - 1;
        var d;
        if (r[4] == "" || r[4] == null) {
            d = new Date(r[1], r[2], r[3]);
            if (d.getFullYear() == r[1] && d.getMonth() == r[2] && d.getDate() == r[3])
                return true;
        } else {
            if (r[8] == "" || r[8] == null) {
                d = new Date(r[1], r[2], r[3], r[6], r[7]);
                if (d.getFullYear() == r[1] && d.getMonth() == r[2] && d.getDate() == r[3] && d.getHours() == r[6] && d.getMinutes() == r[7])
                    return true;
            } else {
                if (r[10] == "" || r[10] == null) {
                    d = new Date(r[1], r[2], r[3], r[6], r[7], r[9]);
                    if (d.getFullYear() == r[1] && d.getMonth() == r[2] && d.getDate() == r[3] && d.getHours() == r[6] && d.getMinutes() == r[7] && d.getSeconds() == r[9])
                        return true;
                } else {
                    d = new Date(r[1], r[2], r[3], r[6], r[7], r[9], r[11]);
                    if (d.getFullYear() == r[1] && d.getMonth() == r[2] && d.getDate() == r[3] && d.getHours() == r[6] && d.getMinutes() == r[7] && d.getSeconds() == r[9] && d.getMilliseconds() == r[11])
                        return true;
                }
            }
        }
    } 
    $(".Loading").removeClass("style0red style0green").addClass("style0yellow");
    $("#operateTip").html("您输入的类型不正确，应该为日期类型").change();
    obj.focus();
    return false;
}

function isvaldatetime(valdate) {
    var val = $.trim(valdate);
    var reg = /^(\d{4})-(\d{1,2})-(\d{1,2})((\s+)(\d{1,2}):(\d{1,2})(:(\d{1,2})(.(\d{1,3}))?)?)?$/;
    var r = val.match(reg);
    if (r != null) {
        r[2] = r[2] - 1;
        var d;
        if (r[4] == "" || r[4] == null) {
            d = new Date(r[1], r[2], r[3]);
            if (d.getFullYear() == r[1] && d.getMonth() == r[2] && d.getDate() == r[3])
                return true;
        } else {
            if (r[8] == "" || r[8] == null) {
                d = new Date(r[1], r[2], r[3], r[6], r[7]);
                if (d.getFullYear() == r[1] && d.getMonth() == r[2] && d.getDate() == r[3] && d.getHours() == r[6] && d.getMinutes() == r[7])
                    return true;
            } else {
                if (r[10] == "" || r[10] == null) {
                    d = new Date(r[1], r[2], r[3], r[6], r[7], r[9]);
                    if (d.getFullYear() == r[1] && d.getMonth() == r[2] && d.getDate() == r[3] && d.getHours() == r[6] && d.getMinutes() == r[7] && d.getSeconds() == r[9])
                        return true;
                } else {
                    d = new Date(r[1], r[2], r[3], r[6], r[7], r[9], r[11]);
                    if (d.getFullYear() == r[1] && d.getMonth() == r[2] && d.getDate() == r[3] && d.getHours() == r[6] && d.getMinutes() == r[7] && d.getSeconds() == r[9] && d.getMilliseconds() == r[11])
                        return true;
                }
            }
        }
    }
    return false;
}

//判定val1与val2比较，-1小于 0等于 1大于 -2有错误
function datecompareVal(val1, val2) {
    var arr1 = val1.split("-");
    var arr2 = val2.split("-");
    if (arr1.length != 3 || arr2.length != 3) {
        $(".Loading").removeClass("style0red style0green").addClass("style0yellow");
        $("#operateTip").html("您输入的不是有效的日期类型").change();
        return -2;
    } else {
        var oldtime1 = new Date(arr1[0], arr1[1], arr1[2]);
        var oldtime2 = new Date(arr2[0], arr2[1], arr2[2]);
        //判定obj1和obj2的大小
        if (oldtime1 > oldtime2)
            return 1;
        else if (oldtime1 < oldtime2)
            return -1;
        else
            return 0;
    }
}

//判定obj1的value与obj2的value比较，-1小于 0等于 1大于 -2有错误
function datecompare(obj1, obj2) {
    var d1, d2;
    var reg = /^(\d{4})-(\d{1,2})-(\d{1,2})$/;
    //判定obj1是否为短日期型
    var b1 = false;
    var val1 = $.trim($(obj1).val());
    $(obj1).val(val1);
    var r1 = val1.match(reg);
    if (r1 != null && r1.length == 4) {
        r1[2] = r1[2] - 1;
        d1 = new Date(r1[1], r1[2], r1[3]);
        if (d1.getFullYear() == r1[1] && d1.getMonth() == r1[2] && d1.getDate() == r1[3])
            b1 = true;
    }
    if (b1 == false) {
        $(".Loading").removeClass("style0red style0green").addClass("style0yellow");
        $("#operateTip").html("您输入的不是有效的日期类型").change();
        obj1.focus();
        return -2;
    }
    //判定obj2是否为短日期型
    var b2 = false;
    var val2 = $.trim($(obj2).val());
    $(obj2).val(val2);
    var r2 = val2.match(reg);
    if (r2 != null && r2.length == 4) {
        r2[2] = r2[2] - 1;
        d2 = new Date(r2[1], r2[2], r2[3]);
        if (d2.getFullYear() == r2[1] && d2.getMonth() == r2[2] && d2.getDate() == r2[3])
            b2 = true;
    }
    if (b2 == false) {
        $(".Loading").removeClass("style0red style0green").addClass("style0yellow");
        $("#operateTip").html("您输入的不是有效的日期类型").change();
        obj2.focus();
        return -2;
    }
    //判定obj1和obj2的大小
    if (d1 > d2)
        return 1;
    else if (d1 < d2)
        return -1;
    else
        return 0;
}

// 验证文本框内的字符是否超长
function OverMaxLength(obj, item) {
    var reg = /[\u4E00-\u9FA5]|[\uFE30-\uFFA0]/g;        //[\u4E00-\u9FA5]為漢字﹐[\uFE30-\uFFA0]為全角符號
    obj = "#" + obj;
    if ($.trim($(obj).val()).replace(reg, "aa").length > $(obj).attr("MaxLength")) {
        $(".Loading").removeClass("style0red style0green").addClass("style0yellow");
        $("#operateTip").html(item + "最多" + $(obj).attr("MaxLength") + "个字符(1个汉字为2个字符)!").change();
        $(obj).focus();
        return true;
    }
    return false;
}
//取得iframe的高度
function getFrameHeight(mFrame) {
    var height = null;
    try {
        if (document.getElementById) {
            if (mFrame && !window.opera) {
                if (mFrame.contentDocument && mFrame.contentDocument.body.offsetHeight)
                    height = mFrame.contentDocument.body.offsetHeight;
                else if (mFrame.Document && mFrame.Document.body.scrollHeight)
                    height = mFrame.Document.body.scrollHeight;
            }
        }
    } catch (e) { }
    return height;
}
//取得url参数
function request(paras) {
    var url = location.href;
    var paraString = url.substring(url.indexOf("?") + 1, url.length).split("&");
    var paraObj = {}
    for (i = 0; j = paraString[i]; i++) {
        paraObj[j.substring(0, j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=") + 1, j.length);
    }
    var returnValue = paraObj[paras.toLowerCase()];
    if (typeof (returnValue) == "undefined") {
        return "";
    } else {
        return returnValue;
    }
}
//关闭窗体
function closeWindow() {
    window.opener = null; window.open('', '_self'); window.close();
}


//检查用户的输入（检查Sql注入攻击）
/* 返回空字符串:合法输入  返回非空字符串:非法输入 */
function isLegalInput(val) {
    var result = '';
    var keywords = "select|insert|delete|from|count|drop|update|truncate|asc(|mid|char|chr|xp_cmdshell|exec|master|netlocalgroup administrators|net user| or | and |declare|--|@@|;|'|\"|*|/|%|!";
    var arrwords = keywords.split('|');
    val = val.toLowerCase();
    for (var i = 0; i < arrwords.length; i++) {
        if (val.indexOf(arrwords[i]) >= 0) {
            result = arrwords[i];
            break;
        }
    }
    return result;
}

//小数位四舍六入五留双的算法
function toNewFixed(str, len) {
    var r = /^([0|2|4|6|8])$/g;
    var r1 = /^([1|3|5|7|9])$/g;
    var decstr = str.substr(str.indexOf('.') + 1);
    var left = str.substr(0, str.indexOf('.') + 1);
    if (decstr.length > len) {
        if (decstr.substr(len, 1) == "5") {
            if (decstr.length > len + 1) {
                str = left + decstr.substr(0, len) + "6";
            }
            else {
                if (r.test(decstr.substr(len - 1, 1))) {
                    str = left + decstr.substr(0, len) + "1";
                }
                else if (r1.test(decstr.substr(len - 1, 1))) {
                    str = left + decstr.substr(0, len) + "6";
                }
            }
        }
    }
    str = parseFloat(str).toFixed(len);
    return str;
}

//////////////////////////////////////Input录入回车换行//////
//author:du
//date:20110908
///////////////////////////////////////////////////////////

var tbxArray = new Array();
function GetAllTextBox(idInput) {
    var inputs = document.getElementsByTagName("input");
    //var inputs = document.getElementById("textconcentration");

    var len = inputs.length;

    for (var i = 0; i < len; i++) {

        if (inputs[i].type == "text") {
            if (idInput == "") {
                tbxArray.push(inputs[i]);
            }
            else if (inputs[i].id == idInput) {
                tbxArray.push(inputs[i]);
            }
        }
    }
    return tbxArray;
}

function DoExcFocus(idInput) {
    tbxArray = GetAllTextBox(idInput);
    var len = tbxArray.length - 1;
    for (var i = 0; i < len; i++) {
        tbxArray[i].NextTextBox = tbxArray[i + 1];
        tbxArray[i].onkeyup = function () {
            if (((this.value.length == this.maxLength) || (event.keyCode == 13)) && this.NextTextBox) {
                this.NextTextBox.focus();
            }
        };
    }
}

function IsEmpty(obj) {
    var val = $.trim($(obj).val());
    if (val == "") {
        $(obj).focus();
        return false;
    }
    return true;
}
//验证手机号码
function IsCellphone(obj) {

    var reg = /^0{0,1}(13[0-9]|15[7-9]|153|156|18[7-9])[0-9]{8}$/;
    if (!reg.test(obj.val())) {
        $(obj).focus();
        return false;
    }
    return true;
}
//判断下拉列表
function IsSelect(obj) {
    if (obj.val() == "" || obj.val() == "-1") {
        $(obj).focus();
        return false;
    }
    return true;
}

//判断邮编
function IsZip(obj) {
    var tel = /^[0-9]{6}$/;
    if (!tel.test(obj.val())) {
        $(obj).focus();
        return false;
    }
    return true;
}


//验证电话号码
function IsTelePhone(obj){
     var TelePhone =/^(\(\d{3,4}\)|\d{3,4}-)?\d{7,8}$/;
      if (!TelePhone.test(obj.val())) {
        $(obj).focus();
        return false;
    }
    return true;
}
//验证网站域名
function IsWebUrl(obj) {
    var WebUrl
    if (obj.val().indexOf('http://') <0) {
        $(obj).focus();
        return false;
    } else {
        return true;
    }
   
   
}

function IsNumber(obj) {
    if (isNaN(obj.val())) {

        $(obj).focus();
        return false;
    }
    return true;
}
///////////////////////////////////////////////////////////////
 