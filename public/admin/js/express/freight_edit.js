/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : 王永杰
 * @date : 2017年6月26日 14:23:24
 * @version : v1.0.0.0
 * 运费模板编辑
 * 修改时间：2017年8月12日 10:13:43 新增区县地区
 * 
 */
$(function(){
	
	//设置弹出地区的位置和宽度
	var top = ($(window).height()-$('#select-region').outerHeight())/2;
	$('#select-region').css({'top': top});
	
	/**
	 * 设置为默认模板
	 * 创建时间：2017年6月27日 20:20:37
	 * 修改时间：
	 * 王永杰
	 */
	$("#is_default").change(function(){
		$("#hidden_is_default").val($(this).val());
		if(parseInt($(this).val())){
			//默认地区不需要选中地区
			$(".js-select-city").text("默认地区(全国)");
			$(".js-select-city").attr("data-flag",1);
			$(".js-select-city").addClass("btn-gradient-default").removeClass("btn-gradient-blue");
			$("#hidden_province_id_array").val("");
			$("#hidden_city_id_array").val("");
			$("#hidden_district_id_array").val("");
			$(".js-region-info").text("");
			$(".js-regions input[type='checkbox']").attr("checked",false);
		}else{
			$(".js-select-city").addClass("btn-gradient-blue").removeClass("btn-gradient-default");
			$(".js-select-city").text("指定地区城市");
			$(".js-select-city").attr("data-flag",0);
		}
	});
	
	
	/**
	 * 修改运费模板时，把弹出框的地区选择选中
	 * 
	 * 2017年8月12日 10:11:34  新增区县地区  王永杰
	 * 
	 */
	if(parseInt($("#hidden_shipping_fee_id").val())){
		//省id组
		if($("#hidden_province_id_array").val()){
			
			var province_id_array = $("#hidden_province_id_array").val().split(",");
			
			for(var i=0;i<province_id_array.length;i++){
				
				if(province_id_array[i]){
					$("input[data-second-parent-index][value='"+province_id_array[i]+"']").attr("checked",true);
				}
				
			}
		}
		
		//市id组
		if($("#hidden_city_id_array").val()){
			
			var city_id_array = $("#hidden_city_id_array").val().split(",");
			for(var i=0;i<city_id_array.length;i++){
				if(city_id_array[i]){
					$("input[data-third-parent-index][value='"+city_id_array[i]+"']").attr("checked",true);
				}
			}
		}

		//区县id组
		if($("#hidden_district_id_array").val()){
			
			var district_id_array = $("#hidden_district_id_array").val().split(",");
			for(var i=0;i<district_id_array.length;i++){
				if(district_id_array[i]){
					$("input[data-four-parent-index][value='"+district_id_array[i]+"']").attr("checked",true);
				}
			}
		}
		
	}
	
	
	/**
	 * 指定地区城市[打开城市弹出框，进行选中]
	 * 2017年6月26日 15:59:32
	 */
	$(".js-select-city").click(function(){
		if(!parseInt($(this).attr("data-flag"))){
			$(".mask-layer").fadeIn(300);
			$('#select-region').fadeIn(300);
		}
	});
	
	
	/**
	 * 一级地区（大类）例如：华北、华东、东北、西北、港澳台等
	 * 根据当前地区的选中状态对应的改变它的子地区
	 * 2017年6月26日 15:29:55 王永杰
	 */
	$("input[data-first-index]").change(function(){
		
		if(!$(this).is(":disabled") && !$(this).attr("data-is-disabled")){
			
			var curr = $(this);//当前对象
			var index = curr.attr("data-first-index");//索引
			var checked = curr.is(":checked");//选中状态

			//省
			if($("input[data-second-parent-index='" + index + "']").length){
				
				$("input[data-second-parent-index='" + index + "']").each(function(){
					if(!$(this).is(":disabled") && !$(this).attr("data-is-disabled")){
						$(this).attr("checked",checked);
					}
				});
				
				//市
				if($("input[data-third-parent-index='" + index + "']").length){
					
					$("input[data-third-parent-index='" + index + "']").each(function(){
						if(!$(this).is(":disabled") && !$(this).attr("data-is-disabled")){
							$(this).attr("checked",checked);
						}
					});
					
					//区县
					if($("input[data-four-parent-index='" + index + "']").length){

						$("input[data-four-parent-index='" + index + "']").each(function(){
							if(!$(this).is(":disabled") && !$(this).attr("data-is-disabled")){
								$(this).attr("checked",checked);
							}
						});
					}
					
				}
			}
		}
	});
	
	
	/**
	 * 二级地区（省）例如：山西省、山东省、河北省等
	 * 根据当前地区的选中状态对应的改变它的子地区
	 * 2017年6月26日 15:46:29 王永杰
	 */
	$("input[data-second-parent-index]").change(function(){
		
		var curr = $(this);//当前对象
		var checked = curr.is(":checked");//选中状态
		
		if(curr.parent().find("div input[type='checkbox']").length){
			
			curr.parent().find("div input[type='checkbox']").each(function(){
				if(!$(this).is(":disabled") && !$(this).attr("data-is-disabled")){
					$(this).attr("checked",checked);
				}
			});
			
		}
		
	});
	
	
	/**
	 * 三级地区（市区）例如：太原市、运城市等
	 * 只要改变了三级地区那它的上一级为不选中状态
	 * 2017年6月26日 16:23:15 王永杰
	 */
	$("input[data-third-parent-index]").change(function(){
		
		var curr = $(this);//当前对象
		var checked = curr.is(":checked");//选中状态
		if(curr.parent().find("div input[type='checkbox']").length){
			
			curr.parent().find("div input[type='checkbox']").each(function(){
				if(!$(this).is(":disabled") && !$(this).attr("data-is-disabled")){
					$(this).attr("checked",checked);
				}
			});
			
		}

		//一个没有选择，父级则不选中
		if(curr.parent().parent().children("span").children("input[type='checkbox']:checked").length == 0){
			curr.parent().parent().parent().children("input").attr("checked",false);
		}
		//选中一个，父类则选中
		if(checked) curr.parent().parent().parent().children("input").attr("checked",true);
	});

	/**
	 * 四级地区（区县）选择一个区县，父类（省市就选中）
	 * 创建时间：2017年8月11日 21:47:05
	 * 
	 */
	$("input[data-four-parent-index]").change(function(){
		
		var curr = $(this);
		var checked = curr.is(":checked");//选中状态
		var index = curr.attr("data-four-parent-index");//下标
		var province_id = curr.attr("data-province-id");//父级，省id
		var city_id = curr.attr("data-city-id");//父级,市id
		
		//一个没有选择，父级则不选中
		if(curr.parent().parent().children("span").children("input[type='checkbox']:checked").length == 0){
			curr.parent().parent().parent().children("input").attr("checked",false);
		}
		
		//选中一个，父类则选中
		if(checked){
			$("[data-second-parent-index='" + index + "'][data-province-id='" + province_id + "']").attr("checked",true);
			$("[data-third-parent-index='" + index + "'][data-city-id='" + city_id + "']").attr("checked",true);
		}
	});
	
	
	/**
	 * 确定选择地区
	 * 2017年6月26日 17:14:59 王永杰
	 */
	$("#select-region .js-confirm").click(function(){
		setProvinceIdArray();
		setCityIdArray();
		setDistrictIdArray();
		$(".js-region-info").html(getRegions());
		$(".mask-layer").fadeOut(300);
		$('#select-region').fadeOut(300);
	});
	
	
	/**
	 * 取消选择地区
	 * 关闭选择地区弹出框
	 * 2017年6月26日 17:09:50 王永杰
	 */
	$("#select-region .js-cancle").click(function(){
		$(".mask-layer").fadeOut(300);
		$('#select-region').fadeOut(300);
	})
	
	
	//判断是否显示
	$(".drop-down").click(function (){
		var self = $(this);
		var is_visible = self.next().is(":visible");
		var level = $(this).attr("data-level");
		$(".drop-down[data-level='" + level + "']").parent().parent().removeClass("open");
		$(".drop-down[data-level='" + level + "']").next().hide();
		if(!is_visible){
			self.parent().parent().addClass("open").attr("data-open",1);
			self.next().show().attr("data-open-children",1);
		}else{
			self.next().hide().removeAttr("data-open-children");
			self.parent().parent().removeClass("open").removeAttr("data-open");
		}
		
	});
	
	//关闭按钮
	$(".close_button").click(function () {
		$(this).parent().parent().css("display", "none");
		$(this).parent().parent().parent().removeClass("open");
	});
	
	
	var flag = false;
	/**
	 * 保存运费模板
	 * edit_button为旧界面
	 * btn-common为新界面
	 * 2017年6月26日 19:03:02 王永杰
	 */
	$(".edit_button,.btn-common").click(function(){
		if(validation()){
			if(flag){
				return;
			}
			flag = true;
			$.ajax({
				url : __URL(ADMINMAIN + "/express/freighttemplateedit"),
				type : "post",
				data : { "data" : getData() },
				success : function(res){
					if (parseInt(res.code)) {
						showMessage('success', res.message,__URL(ADMINMAIN+'/Express/freightTemplateList?co_id='+$("#hidden_co_id").val()));
					}else{
						showMessage('error',  res.message);
						flag = false;
					}
				}
			});
		}
	});
	
});


/**
 * 获取选中的地区（只显示省），逗号隔开
 * 2017年6月26日 18:09:55 王永杰
 */
function getRegions(){
	var regions_arr = new Array();
	if($(".js-regions input[data-second-parent-index]:checked").length){
		$(".js-regions input[data-second-parent-index]:checked").each(function(){
			regions_arr.push($(this).attr("data-province-name"));
		});
	}
	
	return regions_arr.toString();//.replace(",","&nbsp;,&nbsp;");
}

/**
 * 保存选中的省id组
 * @param id_arr 省id组
 */
function setProvinceIdArray(){
	
	var id_arr = new Array();

	if($(".js-regions input[data-second-parent-index]:checked").length){
		$(".js-regions input[data-second-parent-index]:checked").each(function(){
			if(!$(this).is(":disabled") && !$(this).attr("data-is-disabled")){
				id_arr.push($(this).val());
			}
		});
	}
	$("#hidden_province_id_array").val(id_arr.toString());
}

/**
 * 保存选中的市id组
 * @param id_arr 
 */
function setCityIdArray(){
	
	var id_arr = new Array();
	if($(".js-regions input[data-third-parent-index]:checked").length){
		$(".js-regions input[data-third-parent-index]:checked").each(function(){
			if(!$(this).is(":disabled") && !$(this).attr("data-is-disabled")){
				id_arr.push($(this).val());
			}
		});
	}
	$("#hidden_city_id_array").val(id_arr);// 市id
}

/**
 * 保存选中的区县id组
 * 创建时间：2017年8月12日 09:22:40
 * @param id_arr 
 */
function setDistrictIdArray(){
	
	var id_arr = new Array();
	if($(".js-regions input[data-four-parent-index]:checked").length){
		$(".js-regions input[data-four-parent-index]:checked").each(function(){
			if(!$(this).is(":disabled") && !$(this).attr("data-is-disabled")){
				id_arr.push($(this).val());
			}
		});
	}
	$("#hidden_district_id_array").val(id_arr);// 区县id
}


/**
 * 验证文本框输入是否合法
 * 
 * 验证规则：
 * 1、模板名称必填
 * 2、选择地区必须选择一个城市
 * 3、按重量、按体积、按件数，必须设置一个，并且启用
 * 4、按重量、按体积、按件数启用后，才验证对应的输入信息（都不能输入负数）
 * 
 * 2017年6月26日 19:01:40 王永杰
 */
function validation(){

	if (!$("#shipping_fee_name").val()) {
		showTip("请输入模板名称","warning");
		$("#shipping_fee_name").focus();
		return false;
	}
	
	if(!parseInt($("#hidden_is_default").val())){
	
		if(!$("#hidden_province_id_array").val().length){
			showTip("请选择地区",'warning');
			return false;
		}
	}

	var reg = /^\d+(.{0,1})\d{0,2}$/;//默认规则：不能为负数，保留两位小数点
	var reg_int = /^\d+$/;//整数
	var reg_greater_int = /^[1-9]\d+$/;//用于续件，必须是大于0的整数 
	var reg_greater_double = /^([1-9])[0-9]{0,}(.{0,1})[0-9]{0,2}$/;//用于续运费，不能为负数，并且保留两位小数点
	var flag = true;//验证输入信息是否合法，默认true：合法，false：不合法
	
	$(".input-info-table input[type='text']").each(function(){
		var rule = reg;
		if($(this).val().length){
			if(isNaN($(this).val())){
				flag = false;
			}else{
//				if($(this).attr("data-rule")){
//					switch($(this).attr("data-rule")){
//					case "int":
//						//首件
//						rule = reg_int;
//						break;
//						
//					case "greater_int":
//						//续件
//						rule = reg_greater_int;
//						break;
//						
//					case "greater_double":
//						//续运费
//						rule = reg_greater_double;
//						break;
//					default:
//						rule = reg;
//						break;
//					}
//				}else{
//					rule = reg;
//				}
				rule = reg;
				if(!rule.test($(this).val())){
					flag = false;
				}
			}
			if(!flag){
				showTip($(this).attr("data-msg"),"warning");
				$(this).select();
				return false;
			}
		}
	});

	if(!flag){
		return false;
	}
	return true;
}

function getData(){
	
	var data = {
		
		shipping_fee_id : parseInt($("#hidden_shipping_fee_id").val()), //修改时用到
		co_id : parseInt($("#hidden_co_id").val()), // 物流公司id
		is_default : $("#hidden_is_default").val(), // 是否默认
		shipping_fee_name : $("#shipping_fee_name").val(), // 运费模板名称
		province_id_array : $("#hidden_province_id_array").val(), // 省id组
		city_id_array : $("#hidden_city_id_array").val(), // 市id组
		district_id_array: $("#hidden_district_id_array").val(),//区县id组
		weight_is_use : $("#weight_is_use").is(":checked") ? 1 : 0, // 是否启用重量运费，0：不启用，1：启用
		weight_snum : $("#weight_snum").val(), // 首重
		weight_sprice : $("#weight_sprice").val(), // 首重运费
		weight_xnum : $("#weight_xnum").val(), // 续重
		weight_xprice : $("#weight_xprice").val(), // 续重运费
		
		volume_is_use : $("#volume_is_use").is(":checked") ? 1 : 0, // 是否启用体积计算运费，0：不启用，1：启用
		volume_snum : $("#volume_snum").val(), // 首体积量
		volume_sprice : $("#volume_sprice").val(), // 首体积运费
		volume_xnum : $("#volume_xnum").val(), // 续体积量
		volume_xprice : $("#volume_xprice").val(), // 续体积运费
		
		bynum_is_use : $("#bynum_is_use").is(":checked") ? 1 : 0, // 是否启用计件方式运费，0：不启用，1：启用
		bynum_snum : $("#bynum_snum").val(), // 首件
		bynum_sprice : $("#bynum_sprice").val(), // 首件运费
		bynum_xnum : $("#bynum_xnum").val(), // 续件
		bynum_xprice : $("#bynum_xprice").val() // 续件运费
		
	};
	return JSON.stringify(data);
}