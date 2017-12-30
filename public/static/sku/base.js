
function layer_tips(msg_type,msg_content){
	layer.closeAll();
	var time = msg_type==0 ? 3 : 4;
	var type = msg_type==0 ? 1 : (msg_type != -1 ? 0 : -1);
	if(type == 0){
		msg_content = '<font color="red">'+msg_content+'</font>';
	}
	$.layer({
		title: false,
		offset: ['80px',''],
		closeBtn:false,
		shade:[0],
		time:time,
		dialog:{
			type:type,
			msg:msg_content
		}
	});
}

function golbal_tips(msg, status){
	//status 1:error, 0:success
	var type = status == 1 ? 'error' : 'success';
	if($("#infotips").length > 0) $("#infotips").remove();
	var html = '<div class="js-notifications notifications" id="infotips"><div class="alert in fade alert-'+type+'"><a href="javascript:;" class="close pull-right" onclick="$(\'#infotips\').remove();">×</a>' + msg + '</div></div>';
	$('body').append(html);
	$('#infotips').delay(1000).fadeOut(2000);
}


var load_page_cache = [];
function load_page(dom,url,param,cache,obj){
	if(cache!='' && load_page_cache[cache]){
		$(dom).html(load_page_cache[cache]);
		if(obj) obj();
	}else{
		$(dom).html('<div class="loading-more"><span></span></div>');
		$(dom).load(url+'&t='+Math.random(),param,function(response,status,xhr){
			if(cache!='') load_page_cache[cache]=response;
			if(obj) obj();
		});
	}
}



function login_box_close(){
	$('.widget_link_box').animate({'margin-top': '-' + ($(window).scrollTop() + $(window).height()) + 'px'}, "slow",function(){
		$('.widget_link_back,.widget_link_box').remove();
	});
}


function widget_box_after(number,data){
	widget_link_save_box[number](data);
	login_box_close();
}

function check_url(url){
    var reg = new RegExp();
    reg.compile("^(http|https)://.*?$");
    if(!reg.test(url)){
        return false;
    }
    return true;
}
/**
 * 得到对象的长度
 */
function getObjLength(obj){
	var number = 0;
	for(var i in obj){
		number++;
	}
	return number;
}
/**
 * 得到文件的大小
 */
function getSize(size){
	var kb = 1024;
    var mb = 1024*kb;
    var gb = 1024*mb;
    var tb = 1024*gb;
	if(size<mb){
        return (size/kb).toFixed(2)+" KB";
    }else if(size<gb){
        return (size/mb).toFixed(2)+" MB";
    }else if(size<tb){
        return (size/gb).toFixed(2)+" GB";
    }else{
        return (size/tb).toFixed(2)+" TB";
    }
}
/**
 * 生成一个唯一数
 */
function getRandNumber(){
	var myDate=new Date();
	return myDate.getTime() + '' + Math.floor(Math.random()*10000);
}