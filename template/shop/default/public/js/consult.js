

var submiting = false;

//操作处理开始
var OperateHandle = function () {
    function _bindEvent() {
        //咨询列表切换卡

        //显示隐藏咨询类型信息
        $("[nc_type='consultClassRadio']").first().attr("checked","checked");
        $("[nc_type='consultClassIntroduce']").hide();
        $("[nc_type='consultClassIntroduce']").first().show();

        $("[nc_type='consultClassRadio']").click(function () {
            $("[nc_type='consultClassIntroduce']").hide();
            $("#consultClassIntroduce"+$(this).val()).show();
        });

        //验证码
        $("#consultCaptchaHide").click(function(){
            $(".code").fadeOut("slow");
        });
        $("#consultCaptcha").focus(function(){
            $(".code").fadeIn("fast");
        });

        $("#consultSubmit").click(function(){
        	var goods_id=$('#goods_id').val();
        	var goods_name=$('#goods_name').val();
        	var ct_id=$('[name="classId"]:checked').val();
        	var consult_content=$('#consultContent').val();
        	var shop_id=$('#shop_id').val();
        	var randomCode=$('#consultCaptcha').val();
        	if(consult_content==""){
        		$.msg('咨询信息不可为空!');
        		return false;
        	}
        	if(randomCode==""){
        		$.msg('验证码不可为空!');
        		return false;
        	}
            if($("#isSub").val()=='true'){
                $("#isSub").val("false");
                $("#consultSubmit").css({"background-color":"#ddd", "border-color":"#ddd"});
                $.post(__URL(SHOPMAIN+"/goods/goodsconsultinsert"),
            		{"goods_id":goods_id,
            		 "goods_name":goods_name,
            		 "ct_id":ct_id,
            		 "shop_id":shop_id,
            		 "consult_content":consult_content,
            		 "randomCode":randomCode
            		 },
            		 function(data){
            			 if(data['code']>0){
            				 $.msg('咨询信息发布成功！');
            				 location.href=__URL(SHOPMAIN+"/goods/goodsconsult?goodsid="+goods_id);
            			 }else if(data['code']==-1){
            				 $.msg(data['message']);
            				 changeCaptcha();
            			 }
            		},
                "json")
            }
        });
        
        //更换验证码
        $('#consultCaptchaImage').click(function(){
            changeCaptcha();
        });
        $('[nc_type="consultCaptchaChange"]').click(function(){
            changeCaptcha();
        });
        
        $('#consultCaptcha').keyup(function(){
        	if($(this).val().length==4){
        		$("#isSub").val("true");
        		$("#consultSubmit").css({"background-color":"#F59C1A", "border-color":"#F59C1A"});
        	}
        })

        //字符个数动态计算
        $("#consultContent").charCount({
            allowed: 200,
            warning: 10,
            counterContainerID:'consultCharCount',
            firstCounterText:'还可以输入',
            endCounterText:'字',
            errorCounterText:'已经超出'
        });
    }

    //外部可调用
    return {
        bindEvent: _bindEvent
    }
}();
//操作处理结束

//更换验证码
function changeCaptcha() {
    $('#consultCaptchaImage').attr('src', SHOPMAIN + '/components/random?t=' + Math.random());
    $('#consultCaptcha').select();
}

$(function () {
	changeCaptcha();
    //页面绑定事件
    OperateHandle.bindEvent();

});