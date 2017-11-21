/**
 * 验证码发送
 */

//倒计时函数
function updateEndTime() {
	var sendOutCode=$('#sendOutCode').val();
	sendOutCode=sendOutCode.replace('秒','');
	if(Number(sendOutCode)==0){
		$('#sendOutCode').val($('#sendOutCode').attr('bvalue'));
		$('#sendOutCode').removeAttr('disabled',true);
		return false;
	}else{
		$('#sendOutCode').val((Number(sendOutCode)-1)+"秒");	
	}
	
	setTimeout("updateEndTime()", 1000);
}
//发送手机验证码
$(function(){
	$('#sendOutCode').click(function(){
		if($(this).attr('name')=='code_mobile'){
			var mobile=$('#mobile').val();
			if(mobile==''){
				$('#mobile').parent().children('.error').text('手机号不可为空').show();;
				return false;
			}else{
				$('#mobile').parent().children('.error').text('').hide();;
			}
			$.ajax({
		         url: __URL(SHOPMAIN+"/Components/mobileVerificationCode"),
		         data: {"mobile": mobile },
		         type: "post",
		         success: function (res) {
		        	//alert(JSON.stringify(res));
		        	if(res['code']>0){
		        		$('#sendOutCode').val(res['time']);
		        		$('#sendOutCode').attr('disabled',true);
		        		updateEndTime();
		        	}else{
		        		$.msg(res['message']);
		        	}
		           
		         }
		     })

		}else if($(this).attr('name')=='code_email'){		     //邮箱验证
			var email = $('#email').val();
			if(email == ''){
				$('#email').parent().children('.error').text('邮箱不可为空').show();;
				return false;
			}else{
				$('#email').parent().children('.error').text('').hide();;
			}
			 $.ajax({
		         url:  __URL(SHOPMAIN+"/Components/emailVerificationCode"),
		         data: {"email": email },
		         type: "post",
		         success: function (res) {
		        	//alert(JSON.stringify(res));
		        	if(res['code']>0){
		        		$('#sendOutCode').val('60秒');
		        		$('#sendOutCode').attr('disabled',true);
		        		updateEndTime();
		        	}else{
		        		$.msg(res['message']);
		        	}
		           
		         }
		     })
		}
		
	})
})

