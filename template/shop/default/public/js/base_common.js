var domain_url = 'http://'+document.domain+'/';
function reg_package() {
	var pal = document.getElementById("package_tit").getElementsByTagName("h2");
	var pal_count = pal.length;
	for (var i = 0; i < pal.length; i++) {
		pal[i].pai = i;
		pal[i].style.cursor = "pointer";
		pal[i].onclick = function() {
			for (var j = 0; j < pal_count; j++) {
				var _pal = document.getElementById("package_tit").getElementsByTagName("h2")[j];
				var ison = j == this.pai;
				_pal.className = (ison ? "current" : "");
			}
			for (var j = 0; j < pal_count; j++) {
				var _palb = document.getElementById("package_box_" + j);
				var ison = j == this.pai;
				_palb.className = (ison ? "" : "none");
			}
		}
	}

}

function get_packcheck_count(pid) {
	var result = 1;
	var fid = document.getElementById('package_box_' + pid);
	var box = fid.getElementsByTagName('input');
	for (var i = 0; i < box.length; i++) {
		if (box[i].type == 'checkbox' && box[i].checked) {
			result = result + 1;
		}
	}
	return result;
}

function get_packcheck_list(indexid) {
	var result = '';
	var fid = document.getElementById('package_box_' + indexid);
	var box = fid.getElementsByTagName('input');
	for (var i = 0; i < box.length; i++) {
		if (box[i].type == 'checkbox' && box[i].checked) {
			result = result + box[i].value + ',';
		}
	}
	return result;
}


function isSelectAttr(spec_arr) {
	var ret = true;
	var num = $("div[class='catt']").length;
	var par = document.getElementById("choose");
	if (num > 0) {
		$("div[class='catt']").each(function() {
			$("#choose").removeClass("catt_photo");
			if ($(this).children("a[class='cattsel']").length <= 0) {
				$("#choose").addClass("catt_photo");
				ret = false;
			} else {
				$("#choose").removeClass("catt_photo");
			}
		})
		if ($('#choose').is('.catt_photo')) {
			alert("��һ��Ҫѡ����Ʒ���ԣ�");
		}
	}
	return ret;
}

/*******************************************************************************
 * �����Ʒ�����ﳵ
 * @param extCode ��չ���룬�����Զ���һЩ��չ���ԣ����磺'pre_sale'
 */

function addToCart(goodsId, parentId, isnowbuy, extCode) {

	var goods = new Object();
	var spec_arr = new Array();
	var fittings_arr = new Array();
	var number = 1;
	var formBuy = document.forms['ECS_FORMBUY'];
	var quick = 0;

	var one_buy = (typeof (isnowbuy) == "undefined") ? 0 : parseInt(isnowbuy);
	document.cookie = "one_step_buy=" + one_buy + ";path=/";// ���ʶ

	// ����Ƿ�����Ʒ���
	if (formBuy) {
		spec_arr = getSelectedAttributes(formBuy);

		if (formBuy.elements['number']) {
			number = formBuy.elements['number'].value;
		}
		quick = 1;
	}else{
            var arrChk = $("#spe_radio"+goodsId+" input[type='radio']:checked");
             $(arrChk).each(function(){     
                spec_arr.push(this.value);
            }); 
            //quick = 1;
        }
	if (isSelectAttr(spec_arr)) {
		goods.quick = quick;
		goods.spec = spec_arr;
		goods.goods_id = goodsId;
		goods.number = number;
		goods.parent = (typeof (parentId) == "undefined") ? 0 : parseInt(parentId);
		goods.extCode = extCode;
		//Ajax.call('flow.php?step=add_to_cart', 'goods=' + $.toJSON(goods), addToCartResponse, 'POST', 'JSON');
        Ajax.call('flow.php?step=add_to_cart', 'goods=' + JSON.stringify(goods), addToCartResponse, 'POST', 'JSON');
	}

}



/**
 * ���ѡ������Ʒ����
 */
function getSelectedAttributes(formBuy) {
	var spec_arr = new Array();
	var j = 0;

	for (i = 0; i < formBuy.elements.length; i++) {
		var prefix = formBuy.elements[i].name.substr(0, 5);

		if (prefix == 'spec_' && (((formBuy.elements[i].type == 'radio' || formBuy.elements[i].type == 'checkbox') && formBuy.elements[i].checked) || formBuy.elements[i].tagName == 'SELECT')) {
			spec_arr[j] = formBuy.elements[i].value;
			j++;
		}
	}

	return spec_arr;
}

function addToCartResponse(result) {
	
	if (result.error > 0) {
		
		if (result.error == 1){
			alert(result.message);
			
		}else if (result.error == 2) {
			if(document.getElementById('g_id')){
				document.getElementById('g_id').value = result.goods_id;
			}
			if(document.getElementById('rgoods_name')){
				document.getElementById('rgoods_name').innerHTML = result.goods_name;
			}
			document.getElementById('tell-me-table').style.display = document.getElementById('tell-me-table').style.display=='none'?'block':'none';
			$('.pop-mask').toggle();

		}
		else if (result.error == 6) {
			openSpeDiv(result.message, result.goods_id, result.parent);
		} else if (result.error == 999) {
			/*if (confirm(result.message)) {
				location.href = 'user.php';
			}*/
			$('.pop-mask,.pop-login').show();	
		} else if (result.error == 888) {
			$('.pop-mask,.pop-compare').show();	
			$('.pop-compare .pop-text').html(result.message).css('padding-top',10);
			$('.pop-compare').css({'top':($(window).height()-$('.pop-compare').outerHeight())/2});	
			//alert(result.message);
		} else if (result.error == 777) {
			$('.pop-mask,.pop-compare').show();	
			$('.pop-compare .pop-text').html(result.message).css('padding-top',10);
			$('.pop-compare').css({'top':($(window).height()-$('.pop-compare').outerHeight())/2});	
			$('.cancel-btn').removeClass('none').siblings('.pop-sure').click(function(){
					window.location.href = result.uri;
			})
		} else {
			//alert(result.message);
			$('.pop-mask,.pop-compare').show();	
			$('.pop-compare .pop-text').html(result.message).css('padding-top',10);
			$('.pop-compare').css({'top':($(window).height()-$('.pop-compare').outerHeight())/2});
		}
	} else {
		var cartInfo = $('.ECS_CARTINFO');
		//var cart_url = domain_url+'flow.php?step=cart';
		var cart_url = 'flow.php?step=cart';
		if (cartInfo) {
			cartInfo.html(result.content);
			$('.cart-panel-content').height($(window).height()-90);
		}

		if (result.one_step_buy == '1') {
			location.href = cart_url;
		} else {
			MoveBox(result.goods_id);
			/*
			 * switch(result.confirm_type) { case '1' :
			 * opencartDiv(result.shop_price,result.goods_name,result.goods_thumb,result.goods_brief,result.goods_id,result.goods_price,result.goods_number);
			 * 
			 * break; case '2' : if (!confirm(result.message)) location.href =
			 * cart_url; break; case '3' : location.href = cart_url; break;
			 * default : break; }
			 */
		}
	}
}

function MoveBox(gid) {
	var obj1 = $('#li_' + gid);
	if (obj1.length > 0) {
		flyCollect(gid, 'collectBox');
	} else {
		location.href = domain_url+'flow.php?step=cart';
	}
}


function collect(goodsId) {
	Ajax.call('user.php?act=collect', 'id=' + goodsId, collectResponse, 'GET', 'JSON');
}


function collectResponse(result) {
	if (result.error > 0) {
		var text='����Ʒ�Ѿ�����������ղؼ��С�';
		//alert(result.message);
		if(result.message == text){
			$('.pop-mask,.pop-compare').show();	
			$('.pop-compare .pop-text').html(text).css('padding-top',10);
			$('.pop-compare').css({'top':($(window).height()-$('.pop-compare').outerHeight())/2});	
		}
		//$('.pop-login,.pop-mask').show();
	} else {
		flyCollect(result.goods_id, 'collectGoods');// �����ղ�
	}
}
/*******************************************************************************
 * �����ղؼ�
 */
function flyCollect(gid, mudidi) {
	var gid = ".pic_img_" + gid;
	var cart = $('#' + mudidi);
	if(cart.length>0){
		var flyElm = $(gid).clone().css('opacity', '0.7');
		flyElm.css({
			'z-index' : 9000,
			'display' : 'block',
			'position' : 'absolute',
			'top' : $(gid).offset().top + 'px',
			'left' : $(gid).offset().left + 'px',
			'width' : $(gid).width() + 'px',
			'height' : $(gid).height() + 'px',
			'-moz-border-radius' : 100 + 'px',
			'border-radius' : 100 + 'px',
			'border-width' : 1 + 'px',
			'border-color' : '#333',
			'border-style' : 'solid',
			'text-align' : 'center'
		});
		$('body').append(flyElm);
		flyElm.animate({
			top : $(cart).offset().top,
			left : $(cart).offset().left,
			width : 20,
			height : 20
		}, 'slow');
	}else{
		$('.pop-mask,.pop-compare').show();	
		$('.pop-compare .pop-text').html('�ղسɹ�').css('padding-top',20);
		$('.pop-compare').css({'top':($(window).height()-$('.pop-compare').outerHeight())/2});	
		//alert("�ղسɹ�");
	}
}

function signInResponse(result) {
	toggleLoader(false);

	var done = result.substr(0, 1);
	var content = result.substr(2);

	if (done == 1) {
		document.getElementById('member-zone').innerHTML = content;
	} else {
		alert(content);
	}
}


function question_type_curr(page, id, question_type) {
	document.getElementById('question_li_0').className = '';
	document.getElementById('question_li_1').className = '';
	document.getElementById('question_li_2').className = '';
	document.getElementById('question_li_3').className = '';
	document.getElementById('question_li_' + question_type).className = 'curr';
	Ajax.call('question.php?act=gotopage', 'page=' + page + '&id=' + id + '&question_type=' + question_type, gotoPageResponse_question, 'GET', 'JSON');
}
function gotoPage_question(page, id, question_type) {
	Ajax.call('question.php?act=gotopage', 'page=' + page + '&id=' + id + '&question_type=' + question_type, gotoPageResponse_question, 'GET', 'JSON');
}

function gotoPageResponse_question(result) {
	document.getElementById("ECS_QUESTION").innerHTML = result.content;
}

function comment_type_curr(page, id, type, comment_level) {
	document.getElementById('comment_li_0').className = '';
	document.getElementById('comment_li_1').className = '';
	document.getElementById('comment_li_2').className = '';
	document.getElementById('comment_li_3').className = '';
	document.getElementById('comment_li_' + comment_level).className = 'curr';
	Ajax.call('comment.php?act=gotopage', 'page=' + page + '&id=' + id + '&type=' + type + '&comment_level=' + comment_level, gotoPageResponse, 'GET', 'JSON');
}


function gotoPage(page, id, type, comment_level) {
	Ajax.call('comment.php?act=gotopage', 'page=' + page + '&id=' + id + '&type=' + type + '&comment_level=' + comment_level, gotoPageResponse, 'GET', 'JSON');
}

function gotoPageResponse(result) {
	document.getElementById("ECS_COMMENT").innerHTML = result.content;
}

function gotoBuyPage(page, id) {
	Ajax.call('goods.php?act=gotopage', 'page=' + page + '&id=' + id, gotoBuyPageResponse, 'GET', 'JSON');
}

function gotoBuyPageResponse(result) {
	document.getElementById("ECS_BOUGHT").innerHTML = result.result;
}

function getFormatedPrice(price) {
	if (currencyFormat.indexOf("%s") > -1) {
		return currencyFormat.replace('%s', advFormatNumber(price, 2));
	} else if (currencyFormat.indexOf("%d") > -1) {
		return currencyFormat.replace('%d', advFormatNumber(price, 0));
	} else {
		return price;
	}
}


function bid(step) {
	var price = '';
	var msg = '';
	if (step != -1) {
		var frm = document.forms['formBid'];
		price = frm.elements['price'].value;
		id = frm.elements['snatch_id'].value;
		if (price.length == 0) {
			msg += price_not_null + '\n';
		} else {
			var reg = /^[\.0-9]+/;
			if (!reg.test(price)) {
				msg += price_not_number + '\n';
			}
		}
	} else {
		price = step;
	}

	if (msg.length > 0) {
		alert(msg);
		return;
	}

	Ajax.call('snatch.php?act=bid&id=' + id, 'price=' + price, bidResponse, 'POST', 'JSON')
}


function bidResponse(result) {
	if (result.error == 0) {
		document.getElementById('ECS_SNATCH').innerHTML = result.content;
		if (document.forms['formBid']) {
			document.forms['formBid'].elements['price'].focus();
		}
		newPrice(); // ˢ�¼۸��б�
	} else {
		alert(result.content);
	}
}

function newPrice(id) {
	Ajax.call('snatch.php?act=new_price_list&id=' + id, '', newPriceResponse, 'GET', 'TEXT');
}


function newPriceResponse(result) {
	document.getElementById('ECS_PRICE_LIST').innerHTML = result;
}

function getAttr(cat_id) {
	var tbodies = document.getElementsByTagName('tbody');
	for (i = 0; i < tbodies.length; i++) {
		if (tbodies[i].id.substr(0, 10) == 'goods_type')
			tbodies[i].style.display = 'none';
	}

	var type_body = 'goods_type_' + cat_id;
	try {
		document.getElementById(type_body).style.display = '';
	} catch (e) {
	}
}

function advFormatNumber(value, num) // ��������
{
	var a_str = formatNumber(value, num);
	var a_int = parseFloat(a_str);
	if (value.toString().length > a_str.length) {
		var b_str = value.toString().substring(a_str.length, a_str.length + 1);
		var b_int = parseFloat(b_str);
		if (b_int < 5) {
			return a_str;
		} else {
			var bonus_str, bonus_int;
			if (num == 0) {
				bonus_int = 1;
			} else {
				bonus_str = "0."
				for (var i = 1; i < num; i++)
					bonus_str += "0";
				bonus_str += "1";
				bonus_int = parseFloat(bonus_str);
			}
			a_str = formatNumber(a_int + bonus_int, num)
		}
	}
	return a_str;
}

function formatNumber(value, num) // ֱ��ȥβ
{
	var a, b, c, i;
	a = value.toString();
	b = a.indexOf('.');
	c = a.length;
	if (num == 0) {
		if (b != -1) {
			a = a.substring(0, b);
		}
	} else {
		if (b == -1) {
			a = a + ".";
			for (i = 1; i <= num; i++) {
				a = a + "0";
			}
		} else {
			a = a.substring(0, b + num + 1);
			for (i = c; i <= b + num; i++) {
				a = a + "0";
			}
		}
	}
	return a;
}

function changePayment(pay_id) {
	// ���㶩������
	calculateOrderFee();
}

function getCoordinate(obj) {
	var pos = {
		"x" : 0,
		"y" : 0
	}

	pos.x = document.body.offsetLeft;
	pos.y = document.body.offsetTop;

	do {
		pos.x += obj.offsetLeft;
		pos.y += obj.offsetTop;

		obj = obj.offsetParent;
	} while (obj.tagName.toUpperCase() != 'BODY')

	return pos;
}

function showCatalog(obj) {
	var pos = getCoordinate(obj);
	var div = document.getElementById('ECS_CATALOG');

	if (div && div.style.display != 'block') {
		div.style.display = 'block';
		div.style.left = pos.x + "px";
		div.style.top = (pos.y + obj.offsetHeight - 1) + "px";
	}
}

function hideCatalog(obj) {
	var div = document.getElementById('ECS_CATALOG');

	if (div && div.style.display != 'none')
		div.style.display = "none";
}

function sendHashMail() {
	Ajax.call('user.php?act=send_hash_mail', '', sendHashMailResponse, 'GET', 'JSON')
}

function sendHashMailResponse(result) {
	alert(result.message);
}

/* ������ѯ */
function orderQuery() {
	var order_sn = document.forms['ecsOrderQuery']['order_sn'].value;

	var reg = /^[\.0-9]+/;
	if (order_sn.length < 10 || !reg.test(order_sn)) {
		alert(invalid_order_sn);
		return;
	}
	Ajax.call('user.php?act=order_query&order_sn=s' + order_sn, '', orderQueryResponse, 'GET', 'JSON');
}

function orderQueryResponse(result) {
	if (result.message.length > 0) {
		alert(result.message);
	}
	if (result.error == 0) {
		var div = document.getElementById('ECS_ORDER_QUERY');
		div.innerHTML = result.content;
	}
}

function display_mode(str) {
	document.getElementById('display').value = str;
	setTimeout(doSubmit, 0);
	function doSubmit() {
		document.forms['listform'].submit();
	}
}

function display_mode_wholesale(str) {
	document.getElementById('display').value = str;
	setTimeout(doSubmit, 0);
	function doSubmit() {
		document.forms['wholesale_goods'].action = "wholesale.php";
		document.forms['wholesale_goods'].submit();
	}
}

function fixpng() {
	var arVersion = navigator.appVersion.split("MSIE")
	var version = parseFloat(arVersion[1])

	if ((version >= 5.5) && (document.body.filters)) {
		for (var i = 0; i < document.images.length; i++) {
			var img = document.images[i]
			var imgName = img.src.toUpperCase()
			if (imgName.substring(imgName.length - 3, imgName.length) == "PNG") {
				var imgID = (img.id) ? "id='" + img.id + "' " : ""
				var imgClass = (img.className) ? "class='" + img.className + "' " : ""
				var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' "
				var imgStyle = "display:inline-block;" + img.style.cssText
				if (img.align == "left")
					imgStyle = "float:left;" + imgStyle
				if (img.align == "right")
					imgStyle = "float:right;" + imgStyle
				if (img.parentElement.href)
					imgStyle = "cursor:hand;" + imgStyle
				var strNewHTML = "<span " + imgID + imgClass + imgTitle + " style=\"" + "width:" + img.width + "px; height:" + img.height + "px;" + imgStyle + ";" + "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader" + "(src=\'" + img.src + "\', sizingMethod='scale');\"></span>"
				img.outerHTML = strNewHTML
				i = i - 1
			}
		}
	}
}

function hash(string, length) {
	var length = length ? length : 32;
	var start = 0;
	var i = 0;
	var result = '';
	filllen = length - string.length % length;
	for (i = 0; i < filllen; i++) {
		string += "0";
	}
	while (start < string.length) {
		result = stringxor(result, string.substr(start, length));
		start += length;
	}
	return result;
}

function stringxor(s1, s2) {
	var s = '';
	var hash = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var max = Math.max(s1.length, s2.length);
	for (var i = 0; i < max; i++) {
		var k = s1.charCodeAt(i) ^ s2.charCodeAt(i);
		s += hash.charAt(k % 52);
	}
	return s;
}

var evalscripts = new Array();
function evalscript(s) {
	if (s.indexOf('<script') == -1)
		return s;
	var p = /<script[^\>]*?src=\"([^\>]*?)\"[^\>]*?(reload=\"1\")?(?:charset=\"([\w\-]+?)\")?><\/script>/ig;
	var arr = new Array();
	while (arr = p.exec(s))
		appendscript(arr[1], '', arr[2], arr[3]);
	return s;
}

function $$(id) {
	return document.getElementById(id);
}

function appendscript(src, text, reload, charset) {
	var id = hash(src + text);
	if (!reload && in_array(id, evalscripts))
		return;
	if (reload && $$(id)) {
		$$(id).parentNode.removeChild($$(id));
	}
	evalscripts.push(id);
	var scriptNode = document.createElement("script");
	scriptNode.type = "text/javascript";
	scriptNode.id = id;
	// scriptNode.charset = charset;
	try {
		if (src) {
			scriptNode.src = src;
		} else if (text) {
			scriptNode.text = text;
		}
		$$('append_parent').appendChild(scriptNode);
	} catch (e) {
	}
}

function in_array(needle, haystack) {
	if (typeof needle == 'string' || typeof needle == 'number') {
		for ( var i in haystack) {
			if (haystack[i] == needle) {
				return true;
			}
		}
	}
	return false;
}

var pmwinposition = new Array();

var userAgent = navigator.userAgent.toLowerCase();
var is_opera = userAgent.indexOf('opera') != -1 && opera.version();
var is_moz = (navigator.product == 'Gecko') && userAgent.substr(userAgent.indexOf('firefox') + 8, 3);
var is_ie = (userAgent.indexOf('msie') != -1 && !is_opera) && userAgent.substr(userAgent.indexOf('msie') + 5, 3);
function pmwin(action, param) {
	var objs = document.getElementsByTagName("OBJECT");
	if (action == 'open') {
		for (i = 0; i < objs.length; i++) {
			if (objs[i].style.visibility != 'hidden') {
				objs[i].setAttribute("oldvisibility", objs[i].style.visibility);
				objs[i].style.visibility = 'hidden';
			}
		}
		var clientWidth = document.body.clientWidth;
		var clientHeight = document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight;
		var scrollTop = document.body.scrollTop ? document.body.scrollTop : document.documentElement.scrollTop;
		var pmwidth = 800;
		var pmheight = clientHeight * 0.9;
		if (!$$('pmlayer')) {
			div = document.createElement('div');
			div.id = 'pmlayer';
			div.style.width = pmwidth + 'px';
			div.style.height = pmheight + 'px';
			div.style.left = ((clientWidth - pmwidth) / 2) + 'px';
			div.style.position = 'absolute';
			div.style.zIndex = '999';
			$$('append_parent').appendChild(div);
			$$('pmlayer').innerHTML = '<div style="width: 800px; background: #666666; margin: 5px auto; text-align: left">' + '<div style="width: 800px; height: ' + pmheight + 'px; padding: 1px; background: #FFFFFF; border: 1px solid #7597B8; position: relative; left: -6px; top: -3px">' + '<div onmousedown="pmwindrag(event, 1)" onmousemove="pmwindrag(event, 2)" onmouseup="pmwindrag(event, 3)" style="cursor: move; position: relative; left: 0px; top: 0px; width: 800px; height: 30px; margin-bottom: -30px;"></div>' + '<a href="###" onclick="pmwin(\'close\')"><img style="position: absolute; right: 20px; top: 15px" src="images/close.gif" title="�ر�" /></a>' + '<iframe id="pmframe" name="pmframe" style="width:' + pmwidth
					+ 'px;height:100%" allowTransparency="true" frameborder="0"></iframe></div></div>';
		}
		$$('pmlayer').style.display = '';
		$$('pmlayer').style.top = ((clientHeight - pmheight) / 2 + scrollTop) + 'px';
		if (!param) {
			pmframe.location = 'pm.php';
		} else {
			pmframe.location = 'pm.php?' + param;
		}
	} else if (action == 'close') {
		for (i = 0; i < objs.length; i++) {
			if (objs[i].attributes['oldvisibility']) {
				objs[i].style.visibility = objs[i].attributes['oldvisibility'].nodeValue;
				objs[i].removeAttribute('oldvisibility');
			}
		}
		hiddenobj = new Array();
		$$('pmlayer').style.display = 'none';
	}
}

var pmwindragstart = new Array();
function pmwindrag(e, op) {
	if (op == 1) {
		pmwindragstart = is_ie ? [ event.clientX, event.clientY ] : [ e.clientX, e.clientY ];
		pmwindragstart[2] = parseInt($$('pmlayer').style.left);
		pmwindragstart[3] = parseInt($$('pmlayer').style.top);
		doane(e);
	} else if (op == 2 && pmwindragstart[0]) {
		var pmwindragnow = is_ie ? [ event.clientX, event.clientY ] : [ e.clientX, e.clientY ];
		$$('pmlayer').style.left = (pmwindragstart[2] + pmwindragnow[0] - pmwindragstart[0]) + 'px';
		$$('pmlayer').style.top = (pmwindragstart[3] + pmwindragnow[1] - pmwindragstart[1]) + 'px';
		doane(e);
	} else if (op == 3) {
		pmwindragstart = [];
		doane(e);
	}
}

function doane(event) {
	e = event ? event : window.event;
	if (is_ie) {
		e.returnValue = false;
		e.cancelBubble = true;
	} else if (e) {
		e.stopPropagation();
		e.preventDefault();
	}
}


function addPackageToCart(packageId, indexId) {
	var package_info = new Object();
	var number = 1;

	package_info.package_id = packageId
	package_info.number = number;
	if (typeof (indexId) != "undefined") {
		goods_id_list = get_packcheck_list(indexId);
		id_re = /[,]$/g;
		goods_id_list = goods_id_list.replace(id_re, '');
		var price_pack = 0;
		var market_pack = 0;
		var fid = document.getElementById('package_box_' + indexId);
		var box = fid.getElementsByTagName('input');
		for (var i = 0; i < box.length; i++) {
			if (box[i].type == 'checkbox' && box[i].checked) {
				// �ײͼ�
				var p_pack = box[i].id;
				price_pack = Number(price_pack) + Number(p_pack);
				// �г���
				var p_market = box[i].name;
				market_pack = Number(market_pack) + Number(p_market);
			}
		}
		price_pack = Math.round(price_pack);
		package_info.package_attr_id = goods_id_list;
		package_info.package_prices = market_pack + '-' + price_pack;

	}
	Ajax.call('flow.php?step=add_package_to_cart', 'package_info=' + $.toJSON(package_info), addPackageToCartResponse, 'POST', 'JSON');
}

function addPackageToCartResponse(result) {
	if (result.error > 0) {
		if (result.error == 2) {
			if (confirm(result.message)) {
				location.href = 'user.php?act=add_booking&id=' + result.goods_id;
			}
		} else {
			alert(result.message);
		}
	} else {
		var cartInfo = $('.ECS_CARTINFO');
		var cart_url = 'flow.php?step=cart';
		if (cartInfo) {
			cartInfo.html(result.content);
		}

		if (result.one_step_buy == '1') {
			location.href = cart_url;
		} else {
			switch (result.confirm_type) {
			case '1':
				$('.pop-mask,.pop-compare').show();	
				$('.pop-compare .pop-text').html(result.message).css('padding-top',10);
				$('.pop-compare').css({'top':($(window).height()-$('.pop-compare').outerHeight())/2});	
				$('.cancel-btn').removeClass('none').siblings('.pop-sure').click(function(){
						location.href = cart_url;
				})
				/*if (confirm(result.message))
					location.href = cart_url;*/
				break;
			case '2':
				if (!confirm(result.message))
					location.href = cart_url;
				break;
			case '3':
				location.href = cart_url;
				break;
			default:
				break;
			}
		}
	}
}

function setSuitShow(suitId) {
	var suit = document.getElementById('suit_' + suitId);

	if (suit == null) {
		return;
	}
	if (suit.style.display == 'none') {
		suit.style.display = '';
	} else {
		suit.style.display = 'none';
	}
}

function docEle() {
	return document.getElementById(arguments[0]) || false;
}

function openSpeDiv(message, goods_id, parent) {
	var _id = "speDiv";
	var m = "mask";
	if (docEle(_id))
		document.removeChild(docEle(_id));
	if (docEle(m))
		document.removeChild(docEle(m));
	var scrollPos;
	if (typeof window.pageYOffset != 'undefined') {
		scrollPos = window.pageYOffset;
	} else if (typeof document.compatMode != 'undefined' && document.compatMode != 'BackCompat') {
		scrollPos = document.documentElement.scrollTop;
	} else if (typeof document.body != 'undefined') {
		scrollPos = document.body.scrollTop;
	}

	var i = 0;
	var sel_obj = document.getElementsByTagName('select');
	while (sel_obj[i]) {
		sel_obj[i].style.visibility = "hidden";
		i++;
	}

	var newDiv = document.createElement("div");
	var speAttr = document.createElement("div");
	newDiv.id = _id;
	speAttr.className = 'attr-list';
	newDiv.innerHTML = '<div class="pop-header"><span>' + select_spe + '</span><a href="javascript:void(0);" onclick="javascript:cancel_div()" title="�ر�"  class="spe-close"></a></div>';
	for (var spec = 0; spec < message.length; spec++) {
		speAttr.innerHTML += '<div class="dt">' + message[spec]['name'] + '��</div>';
		
		if (message[spec]['attr_type'] == 1) {
			var speDD = document.createElement("div");
			speDD.className ='dd radio-dd';
			for (var val_arr = 0; val_arr < message[spec]['values'].length; val_arr++) {
				if (val_arr == 0) {
					speDD.innerHTML += "<span class='attr-radio curr'><label for='spec_value_"+message[spec]['values'][val_arr]['id']+"'><input type='radio' name='spec_" + message[spec]['attr_id'] + "' value='" + message[spec]['values'][val_arr]['id'] + "' id='spec_value_" + message[spec]['values'][val_arr]['id'] + "' checked />&nbsp;<font>" + message[spec]['values'][val_arr]['label'] + '</font> [' + message[spec]['values'][val_arr]['format_price'] + ']</font></label></span>';
				} else {
					speDD.innerHTML += "<span class='attr-radio'><label for='spec_value_"+message[spec]['values'][val_arr]['id']+"'><input type='radio' name='spec_" + message[spec]['attr_id'] + "' value='" + message[spec]['values'][val_arr]['id'] + "' id='spec_value_" + message[spec]['values'][val_arr]['id'] + "' />&nbsp;<font>" + message[spec]['values'][val_arr]['label'] + '</font> [' + message[spec]['values'][val_arr]['format_price'] + ']</font></label></span>';
				}
			}
			speDD.innerHTML += "<input type='hidden' name='spec_list' value='" + val_arr + "' />";
		} else {
			var speDD = document.createElement("div");
			speDD.className = 'dd checkbox-dd';
			for (var val_arr = 0; val_arr < message[spec]['values'].length; val_arr++) {
				speDD.innerHTML += "<span class='attr-radio'><label for='spec_value_"+message[spec]['values'][val_arr]['id']+"'><input type='checkbox' name='spec_" + message[spec]['attr_id'] + "' value='" + message[spec]['values'][val_arr]['id'] + "' id='spec_value_" + message[spec]['values'][val_arr]['id'] + "' />&nbsp;<font>" + message[spec]['values'][val_arr]['label'] + ' [' + message[spec]['values'][val_arr]['format_price'] + ']</font></label></span>';
			}
			
			speDD.innerHTML += "<input type='hidden' name='spec_list' value='" + val_arr + "' />";
		}
		speAttr.appendChild(speDD);
		speAttr.innerHTML+="<div class='blank'></div>"
	}
	newDiv.appendChild(speAttr);
	newDiv.innerHTML += "<div class='spe-btn'><a href='javascript:submit_div(" + goods_id + "," + parent + ")' class='sure-btn' >" + btn_buy + "</a><a href='javascript:cancel_div()' class='cancel-btn'>" + is_cancel + "</a></div>";
	//alert(newDiv.innerHTML);
	document.body.appendChild(newDiv);
	
	$('#speDiv').css('top',($(window).height()-$('#speDiv').height())/2);
	var newMask = document.createElement("div");
	newMask.id = m;
	newMask.style.position = "fixed";
	newMask.style.zIndex = "9999";
	newMask.style.width = document.body.scrollWidth + "px";
	newMask.style.height = document.body.scrollHeight + "px";
	newMask.style.top = "0px";
	newMask.style.left = "0px";
	newMask.style.background = "#000";
	newMask.style.filter = "alpha(opacity=15)";
	newMask.style.opacity = "0.15";
	document.body.appendChild(newMask);
	$('#speDiv .radio-dd').on('click','.attr-radio',function(){
		$(this).addClass('curr').siblings('.attr-radio').removeClass('curr');
	});
	$('#speDiv .checkbox-dd').on('click','.attr-radio',function(){
		$(this).toggleClass('curr');
	});
}

function submit_div(goods_id, parentId) {
	var goods = new Object();
	var spec_arr = new Array();
	var fittings_arr = new Array();
	var number = 1;
	var input_arr = document.getElementsByTagName('input');
	var quick = 1;

	var spec_arr = new Array();
	var j = 0;

	for (i = 0; i < input_arr.length; i++) {
		var prefix = input_arr[i].name.substr(0, 5);

		if (prefix == 'spec_' && (((input_arr[i].type == 'radio' || input_arr[i].type == 'checkbox') && input_arr[i].checked))) {
			spec_arr[j] = input_arr[i].value;
			j++;
		}
	}

	goods.quick = quick;
	goods.spec = spec_arr;
	goods.goods_id = goods_id;
	goods.number = number;
	goods.parent = (typeof (parentId) == "undefined") ? 0 : parseInt(parentId);

	Ajax.call('flow.php?step=add_to_cart', 'goods=' + $.toJSON(goods), addToCartResponse, 'POST', 'JSON');

	document.body.removeChild(docEle('speDiv'));
	document.body.removeChild(docEle('mask'));

	var i = 0;
	var sel_obj = document.getElementsByTagName('select');
	while (sel_obj[i]) {
		sel_obj[i].style.visibility = "";
		i++;
	}

}
function cancel_div() {
	document.body.removeChild(docEle('speDiv'));
	document.body.removeChild(docEle('mask'));

	var i = 0;
	var sel_obj = document.getElementsByTagName('select');
	while (sel_obj[i]) {
		sel_obj[i].style.visibility = "";
		i++;
	}
}
function opencartDiv(price, name, pic, goods_brief, goods_id, total, number) {
	var _id = "speDiv";
	var m = "mask";

	if (docEle(_id))
		document.removeChild(docEle(_id));
	if (docEle(m))
		document.removeChild(docEle(m));
	var scrollPos;
	if (typeof window.pageYOffset != 'undefined') {
		scrollPos = window.pageYOffset;
	} else if (typeof document.compatMode != 'undefined' && document.compatMode != 'BackCompat') {
		scrollPos = document.documentElement.scrollTop;
	} else if (typeof document.body != 'undefined') {
		scrollPos = document.body.scrollTop;
	}

	var i = 0;
	var sel_obj = document.getElementsByTagName('select');
	while (sel_obj[i]) {
		sel_obj[i].style.visibility = "hidden";
		i++;
	}

	var newDiv = document.createElement("div");
	newDiv.id = _id;
	newDiv.style.position = "absolute";
	newDiv.style.zIndex = "10000";
	newDiv.style.width = "500px";
	newDiv.style.height = "270px";
	newDiv.style.top = (parseInt(scrollPos + 200)) + "px";
	newDiv.style.left = (parseInt(document.body.offsetWidth) - 400) / 2 + "px"; // ��Ļ����
	newDiv.style.background = "#fff";
	newDiv.style.border = "1px solid #cccccc";
	var html = '';

	html = '<div class=cardivfloat><span class=cartdivfloattitle>��Ʒ�ѳɹ���ӵ����ﳵ��</span><a href=\'javascript:cancel_div()\' style="float:right;padding:0 26px 0 0;background:url(themes/ecshop_jdbuy/images/ico_closebig1.gif) right center no-repeat;cursor:pointer;color:#ffffff;font-size:12px;" >�ر�</a></div><div class="cartpopDiv"><div class="toptitle"><a href="goods.php?id=' + goods_id + '" class="pic"><img src=' + pic + ' width="98" height="98" style="border:#dddddd 1px solid; display:block;"/></a><p><font style="font-weight:bold">' + name + '</font>  <br>' + goods_brief + '<br>����۸�<font style="color:#cc0000">' + price + '</font><br></p></div>';

	html += '<div class="coninfo">';
	html += '<table cellpadding="0" height="30"><tr><td align="center" >���ﳵ����<font style="color:#ff6701;"><strong>' + number + '</strong></font>����Ʒ  �ϼƣ�<font style="color:#cc0000;"><strong>' + total + '</strong></font></td></tr>';
	html += '</table>';
	html += '</div>';

	html += "<div class=cartbntfloat ><a href='flow.php'><img src='themes/ecshop_jdbuy/images/jsico1.gif'></a><a href=\'javascript:cancel_div()\'><img src='themes/ecshop_jdbuy/images/goon_ico1.gif'></a></div>";
	html += '</div></div>';
	newDiv.innerHTML = html;
	document.body.appendChild(newDiv);
	var newMask = document.createElement("div");
	newMask.id = m;
	newMask.style.position = "absolute";
	newMask.style.zIndex = "9999";
	newMask.style.width = document.body.scrollWidth + "px";
	newMask.style.height = document.body.scrollHeight + "px";
	newMask.style.top = "0px";
	newMask.style.left = "0px";
	newMask.style.background = "#000000";
	newMask.style.filter = "alpha(opacity=30)";
	newMask.style.opacity = "0.40";
	document.body.appendChild(newMask);

}


function chat_online(data){
	
	$.post('chat.php', {act: 'check_login'}, function(result){
		if(result == 'false'){
			window.location.href = "chat.php";
		}else{
			var param = "";
			
			if(data != undefined && data != null && $.isPlainObject(data)){
				var settings = {chat_goods_id: null, chat_order_id: null, chat_supp_id: null};
				data = $.extend(settings, data);
				
				if(data.chat_goods_id != null){
					param = param + "&chat_goods_id="+data.chat_goods_id;
				}
				if(data.chat_order_id != null){
					param = param + "&chat_order_id="+data.chat_order_id;
				}
				if(data.chat_supp_id != null){
					param = param + "&chat_supp_id="+data.chat_supp_id;
				}
			}else{
				if($("#chat_goods_id").size() > 0){
					param = param + "&chat_goods_id="+$("#chat_goods_id").val();
				}
				if($("#chat_order_id").size() > 0){
					param = param + "&chat_order_id="+$("#chat_order_id").val();
				}
				if($("#chat_supp_id").size() > 0){
					param = param + "&chat_supp_id="+$("#chat_supp_id").val();
				}
			}
			
			var top_=($(window).height()-500)/2;
			var left=($(window).width()-700)/2;
			//&XDEBUG_SESSION_START=ECLIPSE_DBGP
			//window.open ('chat.php?act=chat' + param,'newwindow','height=460,width=685,top='+top_+',left='+left+',toolbar=no,menubar=no,scrollbars=no, resizable=no,location=no, status=no');
			window.open ('chat.php?act=chat' + param);
		}
	}, 'text');
	
	  	
}