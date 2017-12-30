
//限制文本框只能输入数字或浮点数  例:<input type="text" id="input1" name="input1" onkeyup="javascript:CheckInputIntFloat(this);" />
function CheckInputIntFloat(oInput)
	{
	    if('' != oInput.value.replace(/\d{1,}\.{0,1}\d{0,}/,''))
	    {
	        oInput.value = oInput.value.match(/\d{1,}\.{0,1}\d{0,}/) == null ? '' :oInput.value.match(/\d{1,}\.{0,1}\d{0,}/);
	    }
	}