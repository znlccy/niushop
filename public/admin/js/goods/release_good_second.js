/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ---------------------------------------------- 官方网址:
 * http://www.niushop.com.cn 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * 
 * @author : 小学生王永杰
 * @date : 2016年12月16日 16:17:13
 * @version : v1.0.0.0 商品发布中的第二步，编辑商品信息
 */
$(function() {
	if(parseInt($("#goodsId").val()) > 0){
		//初始化规格图片记录数组
		if($.trim(sku_picture_array_str) != "" && $.trim(sku_picture_array_str) != undefined){
			$sku_goods_picture = eval(sku_picture_array_str);
		}
		if(parseInt($("#goodsType").val()) == 0 ){
			//编辑商品时，有些商品没有选择商品类型
			$("#goodsType").attr("data-flag",1);//标识 0：表示添加商品，1：表示编辑商品，商品分类为0,2：表示编辑商品，商品分类不为0
			getGoodsSpecListNotAttrId();
		}else{
			$("#goodsType").attr("data-flag",2);//标识 0：表示添加商品，1：表示编辑商品，商品分类为0,2：表示编辑商品，商品分类不为0
			/*getGoodsSpecListNotAttrId();
			getGoodsAttributeListByAttrId($("#goodsType").val(), function(){
				$(".js-goods-sku-attribute tr").each(function(){
					var value = $(this).children("td:first").attr("data-value");//商品属性名称
					var value_name = $(this).children("td:last");//具体的属性值
					if(value != undefined && value != ""){
						for(var i=0;i<goods_attribute_list.length;i++){
							var curr = goods_attribute_list[i];
							if(curr['attr_value'] == value){
								switch(value_name.find("input").attr("type")){
									case "text":
										value_name.find("input").val(curr['attr_value_name']);
										break;
									case "radio":
										value_name.find("input").each(function(){
											if($.trim($(this).val()) == $.trim(curr['attr_value_name'])){
												$(this).attr("checked","checked");
												return false;
											}
										})
										break;
									case "checkbox":
										value_name.find("input").each(function(){
											if($.trim($(this).val()) == $.trim(curr['attr_value_name'])){
												$(this).attr("checked","checked");
											}
										})
										break;
								}
								if(value_name.find("input").attr("type") != "checkbox"){
									break;
								}
							}
						}
					}
				});
			});*/
			
			getGoodsSpecListByAttrId($("#goodsType").val(),function(){
				editSkuData(goods_spec_format,sku_list);
				//加载属性
				$(".js-goods-sku-attribute tr").each(function(){
					var value = $(this).children("td:first").attr("data-value");//商品属性名称
					var value_name = $(this).children("td:last");//具体的属性值
					if(value != undefined && value != ""){
						for(var i=0;i<goods_attribute_list.length;i++){
							var curr = goods_attribute_list[i];
							if(curr['attr_value'] == value){
								switch(value_name.find("input").attr("type")){
									case "text":
										value_name.find("input").val(curr['attr_value_name']);
										break;
									case "radio":
										value_name.find("input").each(function(){
											if($.trim($(this).val()) == $.trim(curr['attr_value_name'])){
												$(this).attr("checked","checked");
												return false;
											}
										})
										break;
									case "checkbox":
										value_name.find("input").each(function(){
											if($.trim($(this).val()) == $.trim(curr['attr_value_name'])){
												$(this).attr("checked","checked");
											}
										})
										break;
								}
								if(value_name.find("input").attr("type") != "checkbox"){
									break;
								}
							}
						}
					}
				});
			});
		}
	}else{
		$("#goodsType").attr("data-flag",0);//标识 0：表示添加商品，1：表示编辑商品，商品分类为0,2：表示编辑商品，商品分类不为0
		getGoodsSpecListByAttrId($("#goodsType").val());
	}
	
	/**
	 * 根据选择的商品类型，查询规格属性
	 * 2017年6月6日 11:46:45 王永杰
	 */
	$("#goodsType").change(function(){
		goodsTypeChangeData();
		getGoodsSpecListByAttrId($(this).val());
		removeSpecPictureBox();
		if(parseInt($(this).val()) == 0){
//			//如果没有选择商品类型，则清空属性信息
			$(".js-goods-attribute-block").hide();
			$(".js-goods-sku-attribute").html("");
		}
	});
	
	/**
	 * 添加商品规格属性
	 * 2017年6月6日 19:39:33 王永杰
	 */
	$(".js-goods-spec-add").live("click",function(){
//		if(parseInt($("#goodsType").val())>0){
			OpenSkuDialog(ADMINMAIN,parseInt($("#goodsType").val()));
			//回调函数：addGoodsSpecCallBack
//		}
	});
	
	
	/**
	 * 规格值添加，生成规格值输入框，进行添加操作
	 * 2017年6月6日 09:46:46 王永杰
	 */
	$(".js-goods-spec-value-add").live("click",function(){
		if($(this).attr("data-flag") == undefined){
			$(".js-goods-spec-value-add").html("添加规格值").removeAttr("data-flag style");
			
			var spec_id = $(this).attr("data-spec-id");
			var show_type = $(this).attr("data-show-type");//显示方式
			var html = '<input type="text" placeholder="请输入规格值" style="margin-bottom:0px;">';
			var length = $(this).parent().children("article").length;//当前规格的规格值数量，用于设置图片上传的id，不冲突
			switch(parseInt(show_type)){
				case 1:
					//文字
					break;
				case 2:
					//颜色
					html += '<input type="color" style="width: 20px; margin-bottom:0px;" >';
					break;
				case 3:
					//图片
					var time = spec_id+getDate();
					html += '<div class="js-goods-spec-value-img dynamic-goods-sku-item" style="margin:0 5px;">';
					html += '<input id="goods_spec_value'+time+'_add" type="hidden" style="margin-bottom:0px;">';
					html += '<img src="'+ADMINIMG+'/goods/goods_sku_add.png" id="imggoods_spec_value'+time+'_add">';
					html += '</div>';
					break;
			}
			html += '<span class="goods-sku-add" style="margin:0 10px;">确定</span>';
			html += '<span class="goods-sku-cancle">取消</span>';
			$(this).css("background","#DBDBDB");
			$(this).attr("data-flag",1);
			$(this).html(html);
			$(this).children("input[type='text']").focus();
		}
	});
	
	/**
	 * 规格值添加
	 * 2017年6月6日 10:20:31 王永杰
	 */
	$(".js-goods-spec-value-add>input").live("keyup",function(event){
		var curr_obj = $(this).parent();
		var spec_value_name = curr_obj.children("input[type='text']").val();
		if(event.keyCode == 13){
			
			if(spec_value_name.length != 0){
				
				var show_type = curr_obj.attr("data-show-type");
				var spec_value_data = "";//附加值
				switch(parseInt(show_type)){
					case 1:
						//文字
						break;
					case 2:
						//获取颜色
						spec_value_data = curr_obj.children("input[type='color']").val();
						break;
					case 3:
						//获取图片路径
						spec_value_data = curr_obj.children(".js-goods-spec-value-img").children("input[type='hidden']").val();
						break;
				}
				var spec_value = { 
					spec_id : curr_obj.attr("data-spec-id"), //规格id
					spec_name : curr_obj.attr("data-spec-name"),//规格名称
					show_type : show_type,//展示方式
					spec_value_name : spec_value_name, //规格值 
					spec_value_data : spec_value_data  //附加值
				};
				addGoodsSpecValue(spec_value,function(){
					curr_obj.parent().append(getCurrentSpecValueHTML(spec_value));//加载当前添加的规格值、以及最后那个添加按钮
					curr_obj.remove();//删除当前的添加按钮
				});
				
			}else{
				showTip("请输入规格值","warning");
			}
		}
		return false;//防止事件冒泡
	});
	
	/**
	 * 添加规格值：确定操作
	 * 2017年6月6日 11:37:56 王永杰
	 */
	$(".js-goods-spec-value-add>span:first").live("click",function(){
		var curr_obj = $(this).parent();
		var spec_value_name =  curr_obj.children("input[type='text']").val();
		if(spec_value_name.length!=0){
			
			var show_type = curr_obj.attr("data-show-type");
			var spec_value_data = "";//附加值
			switch(parseInt(show_type)){
				case 1:
					//文字
					break;
				case 2:
					//获取颜色
					spec_value_data = curr_obj.children("input[type='color']").val();
					break;
				case 3:
					//获取图片路径
					spec_value_data = curr_obj.children(".js-goods-spec-value-img").children("input[type='hidden']").val();
					break;
			}
			var spec_value = { 
				spec_id : curr_obj.attr("data-spec-id"), //规格id
				spec_name : curr_obj.attr("data-spec-name"),//规格名称
				show_type : show_type,//展示方式
				spec_value_name : spec_value_name, //规格值 
				spec_value_data : spec_value_data  //附加值
			};
			addGoodsSpecValue(spec_value,function(){
				curr_obj.parent().append(getCurrentSpecValueHTML(spec_value));//加载当前添加的规格值、以及最后那个添加按钮
				curr_obj.remove();//删除当前的添加按钮
			});
			
		}else{
			showTip("请输入规格值","warning");
		}
		return false;//防止事件冒泡
	})
	
	/**
	 * 添加规格值：取消操作
	 * 2017年6月6日 11:34:19 王永杰
	 */
	$(".js-goods-spec-value-add>span:last").live("click",function(){
		$(this).parent().removeAttr("style data-flag").html("添加规格值");
		return false;//防止事件冒泡
	})
	
	/**
	 * 修改商品规格信息
	 * 2017年6月6日 11:34:10 王永杰
	 * 
	 */
	$(".goods-sku-item span").live("dblclick",function(){
		var text = $(this).text();
		if(text != ""){
			$(this).empty();//清空当前规格值的文本内容
			var html = '<input type="text" value="'+text+'" data-flag="update_sku_text" data-old-html="'+text+'" />';
			$(html).appendTo($(this));//添加输入框
			$(this).css("padding","7px 10px");//调整样式
			$(this).children("input[type='text']").focus();
		}

		if(timeoutID != null){
			clearTimeout(timeoutID);
		}
	});

	/**
	 * 更新规格值
	 * 2017年6月6日 11:34:13 王永杰
	 */
	$("input[data-flag='update_sku_text']").live("keyup",function(event){
		var curr_obj = $(this);
		var spec_value_name = $.trim(curr_obj.val());
		if(event.keyCode == 13 ){
			
			if(spec_value_name.length !=0){
				var spec_value_id = curr_obj.parent().attr("data-spec-value-id");
				//输入框的内容与之间的规格值不一等，进行修改，否则关闭输入框
				if(spec_value_name != curr_obj.attr("data-old-html")){
					
//					showTip("修改成功","success");
					var spec={
							flag : curr_obj.parent().hasClass("selected"),
							spec_id : curr_obj.parent().attr("data-spec-id"),
							spec_name : curr_obj.parent().attr("data-spec-name"),
							spec_value_id : spec_value_id,
							spec_value_name : spec_value_name,
							spec_show_type : curr_obj.parent().attr("data-spec-show-type") 
					};
					curr_obj.parent().html(spec_value_name).css("padding","7px 20px");//给规格值文本赋值
					editSpecValueName(spec);
					
//					$.ajax({
//						url : "modifyGoodsSpecValueField",
//						type : "post",
//						data : { "spec_value_id" :spec_value_id, "field_name" : "spec_value_name", "field_value" : spec_value_name },
//						success : function(res){
//							if(res.code>0){
//								showTip(res.message,"success");
//								var spec={
//										flag : curr_obj.parent().hasClass("selected"),
//										spec_id : curr_obj.parent().attr("data-spec-id"),
//										spec_name : curr_obj.parent().attr("data-spec-name"),
//										spec_value_id : spec_value_id,
//										spec_value_name : spec_value_name,
//										spec_show_type : curr_obj.parent().attr("data-spec-show-type") 
//								};
//								editSpecValueName(spec);
//								curr_obj.parent().text(spec_value_name).css("padding","7px 20px");//给规格值文本赋值
//								curr_obj.remove();//删除当前的输入框
//							}else{
//								showTip(res.message,"error");
//							}
//						}
//					});
					
				}else{
					curr_obj.parent().html(spec_value_name).css("padding","7px 20px");//给规格值文本赋值
				}
				
			}else{
				showTip("请输入规格值","warning");
			}
		}
		return false;//防止重复提交
	}).live("click",function(){
		return false;//防止重复提交
	}).live("blur",function(){
		var curr_obj = $(this);
		var spec_value_name = $.trim(curr_obj.val());
		var spec_value_id = curr_obj.parent().attr("data-spec-value-id");
		if(spec_value_name.length == 0){
			showTip("请输入规格值","warning");
			return false;
		}
		if(spec_value_name != curr_obj.attr("data-old-html")){
			var spec={
					flag : curr_obj.parent().hasClass("selected"),
					spec_id : curr_obj.parent().attr("data-spec-id"),
					spec_name : curr_obj.parent().attr("data-spec-name"),
					spec_value_id : spec_value_id,
					spec_value_name : spec_value_name,
					spec_show_type : curr_obj.parent().attr("data-spec-show-type") 
			};
			curr_obj.parent().html(spec_value_name).css("padding","7px 20px");//给规格值文本赋值
			editSpecValueName(spec);
		}else{
			curr_obj.parent().html(spec_value_name).css("padding","7px 20px");//给规格值文本赋值
		}
	});
	
	/**
	 * 修改颜色对应的修改SKU数据
	 */
	$(".js-goods-sku .goods-sku-item input[type='color']").live("change",function(){
		var span = $(this).parent().parent().children("span");
		var spec = {
			flag : span.hasClass("selected"),
			spec_id : span.attr("data-spec-id"),
			spec_name : span.attr("data-spec-name"),
			spec_value_id : span.attr("data-spec-value-id"),
			spec_value_data : $(this).val()
		};
		editSpecValueData(spec);
	});
	
	/**
	 * 鼠标浮上图片，显示
	 * 2017年6月6日 19:01:13
	 */
	$(".goods-sku-item .js-goods-spec-value-img").live("mouseenter",function(){
		var curr = $(this);
		if(curr.children("input[type='hidden']").val() != ""){
			var src = curr.children("img").attr("src");
			
			var contents = '<img src="'+src+'" style="width: 100%;height: auto;display:block;margin:0 auto;">';
			//鼠标每次浮上图片时，要销毁之前的事件绑定
			curr.popover("destroy");
			
			//重新配置弹出框内容
			curr.popover({ content : contents });
			
			//显示
			curr.popover("show");
		}
	});
	
	/**
	 * 鼠标离开图片时，隐藏
	 * 2017年6月6日 19:01:16 王永杰
	 */
	$(".goods-sku-item").live("mouseleave",function(){
		var curr = $(this).children(".js-goods-spec-value-img");
		if(curr.children("input[type='hidden']").val() != ""){
			
			curr.popover("hide");
		}
	});
	
	//***********************************选择运费方式***********************************
	$("input[name='fare']").change(function() {
		if ($("input[name='fare']:checked").val() == 1) {
			//$("#deliveryDiv").show();
			$("#commodity-weight").show();
			$("#commodity-volume").show();
			$("#valuation-method").show();
			$("#express_Company").show();
		} else {
			//$("#deliveryDiv").show();
			$("#commodity-weight").hide();
			$("#commodity-volume").hide();
			$("#valuation-method").hide();
			$("#express_Company").hide();
		}
	});
	//***********************************选择运费方式***********************************
	
	//***********************************选择积分兑换***********************************
	$("input[name='integralSelect']").change(function() {
		if ($("input[name='integralSelect']:checked").val() == 1) {
			$("#integral-exchange").show();
		} else {
			$("#integral-exchange").hide();
		}
	});
	//***********************************选择积分兑换***********************************
	
	/**
	 * 循环处理价格 不让价格为空
	 */
	$('input[name="sku_price"],input[name="market_price"],input[name="cost_price"],input[name="stock_num"],input[name="code"]').live('keyup',function() {
		var $this = $(this);
		var reg = /^\d+(.{0,1})\d{0,2}$/;
		if($this.attr("name") == "sku_price" || $this.attr("name") == "market_price" || $this.attr("name") == "cost_price" || $this.attr("name") == "stock_num"){
			if($this.val().length>0){
				if(reg.test($this.val())){
					if ($this.val().replace(/(^\s*)|(\s*$)/g, "") == "" || $this.val().replace(/(^\s*)|(\s*$)/g, "") == "0.00") {
						if($this.attr("name") == "stock_num"){
							$this.val("0");
						}else{
							$this.val("0.00");
						}
						$this.css("border-color", "#b94a48");
						$this.parent().find(".help-inline").show();
					} else {
						num = parseInt($this.val());
						$this.css("border-color", "");
						$this.parent().find(".help-inline").hide();
					}
					switch($this.attr("name")){
					case "sku_price":
						eachPrice();
						break;
					case "market_price":
						eachMarketPrice();
						break;
					case "cost_price":
						eachCostPrice();
						break;
					case "stock_num":
						eachInput();
						break;
					}
				}else{
					if($this.attr("name") == "stock_num"){
						$this.val("0");
					}else{
						$this.val("0.00");
					}
				}
			}else{
				if($this.attr("name") == "stock_num"){
					$this.val("0");
				}else{
					$this.val("0.00");
				}
			}
		}else{
			//商家编码处理
			if($this.val().length>20) $this.val($this.val().substr(0,20));
			eachMerchantCode();
		}
	});
	
	/**
	 * 离开焦点事件也要进行处理
	 */
	$('input[name="sku_price"],input[name="market_price"],input[name="cost_price"],input[name="stock_num"],input[name="code"]').live("blur",function(){
		$(this).keyup();
	});
	
	/**
	 * 循环 处理库存
	 */
	$('input[name="stock_num"]').live('keyup', function() {
		$stock = $(this);
		if ($stock.val().replace(/(^\s*)|(\s*$)/g, "") == "") {
			$stock.css("border-color", "#b94a48");
			$stock.parent().find(".help-inline").show();
		} else {
			$stock.css("border-color", "");
			$stock.parent().find(".help-inline").hide();
		}
		eachInput();
	});

	$(".brick.small").live('mouseover', function() {
		$(this).children().next().show();
	}).live("mouseout", function() {
		$(this).children().next().hide();
	});

	// 批量设置
	var js_batch_type = '';
	var shop_type = $("#shop_type").val();
	$('.js-batch-price').live('click', function() {
		if (shop_type == 2 || (shop_type == 1 && goodsid == 0)) {
			js_batch_type = 'price';
			$('.js-batch-form').show();
			$('.js-batch-type').hide();
			$('.js-batch-txt').attr('placeholder', '请输入价格');
			$('.js-batch-txt').focus();
		}
	});

	$(".js-batch-market_price").live("click", function() {
		if (shop_type == 2 || (shop_type == 1 && goodsid == 0)) {
			js_batch_type = 'market_price';
			$('.js-batch-form').show();
			$('.js-batch-type').hide();
			$('.js-batch-txt').attr('placeholder', '请输入市场价');
			$('.js-batch-txt').focus();
		}
	});

	$(".js-batch-cost_price").live("click", function() {
		if (shop_type == 2 || (shop_type == 1 && goodsid == 0)) {
			js_batch_type = 'cost_price';
			$('.js-batch-form').show();
			$('.js-batch-type').hide();
			$('.js-batch-txt').attr('placeholder', '请输入成本价');
			$('.js-batch-txt').focus();
		}
	});

	$('.js-batch-stock').live('click', function() {
		if (shop_type == 2 || (shop_type == 1 && goodsid == 0)) {
			js_batch_type = 'stock';
			$('.js-batch-form').show();
			$('.js-batch-type').hide();
			$('.js-batch-txt').attr('placeholder', '请输入库存');
			$('.js-batch-txt').focus();
		}
	});
	
	/**
	 * 批量设置商家编码
	 * 创建时间：2017年9月29日 10:09:40
	 */
	$(".js-batch-merchant-code").live("click",function(){

		if (shop_type == 2 || (shop_type == 1 && goodsid == 0)) {
			js_batch_type = 'code';
			$('.js-batch-form').show();
			$('.js-batch-type').hide();
			$('.js-batch-txt').attr('placeholder', '请输入商家编码');
			$('.js-batch-txt').focus();
		}
	});
	
	/**
	 * 批量设置
	 */
	$('.js-batch-save').live('click',function() {
		var batch_txt = $('.js-batch-txt');
		if (batch_txt.val() != null && batch_txt.val() != '') {
			var float_val = parseFloat(batch_txt.val());
			if (js_batch_type == 'price') {
				if (float_val > 9999999.99) {
					showTip('价格最大为 9999999.99','warning');
					batch_txt.focus();
					return false;
				} else if (!/^\d+(\.\d+)?$/.test(batch_txt.val())) {
					showTip('请输入合法的价格',"warning");
					batch_txt.focus();
					return false;
				} else {
					batch_txt.val(float_val.toFixed(2));
				}
				$('.js-goods-stock .js-price').val(batch_txt.val());
				batch_txt.val('');
				// 商品价格
				$("input[name='price']").val(float_val.toFixed(2));
				$.each($temp_Obj,function(c,v){			
					v["sku_price"] =float_val.toFixed(2);
				});
				$("input[name='price']").attr('readonly', true);
				eachPrice();
			} else if (js_batch_type == 'market_price') {// 市场价
				if (float_val > 9999999.99) {
					showTip('价格最大为 9999999.99','warning');
					batch_txt.focus();
					return false;
				} else if (!/^\d+(\.\d+)?$/.test(batch_txt.val())) {
					showTip('请输入合法的价格','warning');
					batch_txt.focus();
					return false;
				} else {
					batch_txt.val(float_val.toFixed(2));
				}
				$('.js-goods-stock .js-market-price').val(batch_txt.val());
				$.each($temp_Obj,function(c,v){
					v["market_price"] =float_val.toFixed(2);
				});
				batch_txt.val('');
				eachMarketPrice();
			} else if (js_batch_type == 'cost_price') {// 成本价
				if (float_val > 9999999.99) {
					showTip('价格最大为 9999999.99','warning');
					batch_txt.focus();
					return false;
				} else if (!/^\d+(\.\d+)?$/.test(batch_txt.val())) {
					showTip('请输入合法的价格','warning');
					batch_txt.focus();
					return false;
				} else {
					batch_txt.val(float_val.toFixed(2));
				}
				$('.js-goods-stock .js-cost-price').val(batch_txt.val());
				batch_txt.val('');
				// 商品价格
				$("input[name='price']").val(float_val.toFixed(2));
				$("input[name='price']").attr('readonly', true);
				$.each($temp_Obj,function(c,v){			
					v["cost_price"] =float_val.toFixed(2);
				});
				eachCostPrice();
			}else if (js_batch_type == 'code') {// 商家编码
				$('.js-goods-stock .js-code').val(batch_txt.val());
				// 商品价格
				$("input[name='code']").val(batch_txt.val());
				$.each($temp_Obj,function(c,v){
					v["code"] =batch_txt.val();
				});
				batch_txt.val('');
				eachMerchantCode();
			} else {
				if (!/^\d+$/.test(batch_txt.val())) {
					showTip('请输入合法的数字',"warning");
					batch_txt.focus();
					return false;
				}
				$('.js-goods-stock .js-stock-num').val(batch_txt.val());
				eachInput();
				$.each($temp_Obj,function(c,v){
					v["stock_num"] =float_val.toFixed(2);
				});
				$('input[name="total_stock"]').val(parseInt(batch_txt.val())* $('.js-stock-num').size());
				batch_txt.val('');
			}
			$('.js-batch-form').hide();
			$('.js-batch-type').show();
		} else {
			showTip(batch_txt.attr("placeholder"),'warning');
			batch_txt.focus();
		}
	});
	
	$('.js-batch-cancel').live('click', function() {
		$('.js-batch-form').hide();
		$('.js-batch-type').show();
	});
	
	//***********************************选择商品分组***********************************
	$("#area-select,#procategory").on("mouseover", function() {
		$("#procategory").show();
	})

	$("#area-select,#procategory").on("mouseout", function() {
		$("#procategory").hide();
	})

	$(".input-checked").each(function(index, element) {
		if ($(this).prop("checked")) {
			$("#productcategory-selected").append("<span class='label'>" + $(this).val() + "<i class='categoryclose'></i></span>");
		}
	});
	$(".input-checked").live("change",function() {
		var $this = $(this);
		if ($this.prop("checked")) {
			$("#productcategory-selected").append("<span class='label' id=" + $(this).attr("id") + ">" + $this.val() + "<i class='categoryclose'></i></span>");
		} else {
			$("#productcategory-selected span").each(function() {
				if ($this.val() == $(this).text()) {
					$(this).remove();
				}
			});
		}
	});
	
	$("#productcategory-selected").delegate(".categoryclose","click",function() {
		var $this = $(this);
		$(this).parentsUntil("#productcategory-selected").remove();
		/*$("#procategory li").each(function(index, element) {
			if ($this.parent().text() == $(this).find(".input-checked").val()) {
				$(this).find(".input-checked").prop("checked",false);
			}
		});*/
		//移除分组本标签,并还原本分组
		var group_id = $this.parent().attr("id");
		var group_name = $this.parent().attr("group_name");
		$(".goods-gruop-select").append("<option value='"+ group_id +"'>"+ group_name +"</option>");
	});
	//***********************************选择商品分组***********************************
	
	/**
	 * 选择类目、扩展类目
	 * 2017年6月30日 15:15:20 王永杰
	 */
	$("#tbcNameCategory,#tbcExtendNameCategory").live("click",function(){
		var goodsid = $(this).attr("data-goods-id");
		var category_id = $(this).attr("cid");
		var flag = $(this).attr("data-flag");
		OpenCategoryDialog(ADMINMAIN,category_id,goodsid,flag);
	});
	
	/**
	 * 页面导航
	 * 2017年6月30日 16:11:50 王永杰
	 */
	$("#fixedNavBar li").click(function(){
		var obj = "."+$(this).attr("data-floor");
		var top = $(obj).offset().top;
		$("html, body").animate({ scrollTop: top }, {duration: 500,easing: "swing"});
	});

	/**
	 * 商品图片：从图片空间选择
	 */
	$('#img_box').live('click',function(e){
		var js_img = $(this).attr("js-img");
		shopImageFlag = js_img;//所点击的商品图片标识
		speciFicationsFlag = 0;
		OpenPricureDialog("PopPicure", ADMINMAIN, 0, 1);
	});
	
	/**
	 * 规格图片，从图片空间选择
	 */
	$('#sku_img_box').live('click',function(e){
		var js_img = $(this).attr("js-img");
		var spec_id = $(this).attr("spec_id");
		var spec_value_id = $(this).attr("spec_value_id");
		shopImageFlag = js_img;//所点击的商品图片标识
		speciFicationsFlag = 0;
		OpenPricureDialog("PopPicure", ADMINMAIN, 0, 2, spec_id, spec_value_id);
	});
	
	/**
	 * 商品类型，SKU缩略图
	 */
	$('.sku-img-check').live('click',function(e){
		var js_img = $(this).attr("js-img");
		var spec_id = $(this).prev().prev().attr("data-spec-id");
		var spec_value_id = $(this).prev().prev().attr("data-spec-value-id");
		shopImageFlag = js_img;//所点击的商品图片标识
		speciFicationsFlag = 0;
		OpenPricureDialog("PopPicure", ADMINMAIN, 1, 3,spec_id, spec_value_id);
	});
	
	/**
	 * 实物类别选择
	 * 创建时间：2017年11月21日 17:23:53 王永杰
	 */
	$("input[name='goods_type']").live("click",function(){
		if($(this).val() == 0){
			$(".js-virtual-goods-type-block").show();
		}else{
			$(".js-virtual-goods-type-block").hide();
		}
	});

});


/**
 * 添加规格属性，回调函数
 * 2017年6月6日 12:10:37 王永杰
 */
function addGoodsSpecCallBack(spec_id,spec_name,show_type){
	showTip("操作成功","success");
	var spec = {
		spec_id : spec_id,
		spec_name : spec_name,
		show_type : show_type
	}
	var html = '<tr class="goods-sku-block-'+ spec_id +'">';
		html += '<td width="10%">'+spec_name+'</td>';
		html += '<td width="85%">';
			html += getAddSpecValueHtml(spec);
		html += '</td>';
	html += '</tr>';
	html += '<tr><td>'+getAddSpecHtml()+'</td></tr>';
	$(".js-goods-sku tbody tr:last").remove();
	$(".js-goods-sku tbody").append(html);
}

/**
 * 返回当前添加完成后，生成的规格值HTML代码
 * 2017年6月7日 14:48:27
 */
function getCurrentSpecValueHTML(spec_value){
	var html = '<article class="goods-sku-item">';
			html += '<span data-spec-name="'+spec_value.spec_name+'"';
			html += 'data-spec-id="'+spec_value.spec_id+'" ';
			if(parseInt(spec_value.show_type) == 2 && spec_value.spec_value_data == ""){
				spec_value.spec_value_data = "#000000";
			}
			html += ' data-spec-value-data="' + spec_value.spec_value_data + '"';
			html += ' data-spec-show-type="' + spec_value.show_type + '"';
			html += 'data-spec-value-id="-1">';
			html += spec_value.spec_value_name+'</span>';
	switch(parseInt(spec_value.show_type)){
		case 1:
			//文字
			break;
		case 2:
			//颜色
			html += '&nbsp;<i></i>&nbsp;';
			html += '<div>';
				html += '<input type="color" value="'+spec_value.spec_value_data+'" >';
			html += '</div>';
			break;
		case 3:
			//图片
			var time = spec_value.spec_id + getDate();
			html += '&nbsp;<i></i>&nbsp;';
			html += '<div class="js-goods-spec-value-img sku-img-check" data-html="true" data-container="body" data-placement="top" data-trigger="manual">';
				if(spec_value.spec_value_data != ""){
					html += '<input type="hidden" id="spec_value'+time+'_add" value="'+spec_value.spec_value_data+'" >';
					html += '<img src="'+__IMG(spec_value.spec_value_data)+'" id="imgspec_value'+time+'_add">';
				}else{
					html += '<input type="hidden" id="spec_value'+time+'_add" >';
					html += '<img src="'+ADMINIMG+'/goods/goods_sku_add.png" id="imgspec_value'+time+'_add">';
				}
			html += '</div>';
			break;
	}
	html += '</article>';
	html += getAddSpecValueHtml(spec_value);
	return html;
}


/**
 * 返回添加规格值THML代码
 * 2017年6月7日 14:26:31 王永杰
 */
function getAddSpecValueHtml(spec){
	var html = '<a href="javascript:;" data-spec-name="'+spec.spec_name+'" data-spec-id="'+spec.spec_id+'" data-show-type="'+spec.show_type+'" class="js-goods-spec-value-add goods-sku-add-text">添加规格值</a>';
	return html;
}

/**
 * 返回添加规格HTML代码
 * 2017年6月7日 14:25:10 王永杰
 */
function getAddSpecHtml(){
	var html ='<a href="javascript:;" class="js-goods-spec-add goods-sku-add-text" style="padding:0;">添加规格</a>';
	return html;
}

/**
 * 添加商品规格值
 * 2017年6月6日 11:39:16 王永杰
 * @param spec 规格对象
 * @param callBack 回调函数
 */
function addGoodsSpecValue(spec,callBack){
	$.ajax({
		url : __URL(ADMINMAIN+"/goods/addGoodsSpecValue"),
		type : "post",
		data : { "spec_id" : spec.spec_id, "spec_value_name" : spec.spec_value_name, "spec_value_data" : spec.spec_value_data },
		success : function(res){
			if(res.code>0){
				showTip(res.message,"success");
				callBack();//执行回调函数
				$("span[data-spec-value-id='-1']").attr("data-spec-value-id",res.code);
			}else{
				showTip(res.message,"error");
			}
		}
	});
}

/**
 * 获取规格表头提示
 * 2017年6月14日 09:22:46
 * @returns {String}
 */
function getGoodsSpecHeaderHtml(){
	var html = '<tr>';
		html += '<td colspan="2">';
			html += '<div style="text-align:left;">';
				html += '<h5 style="margin:0;padding:0;font-weight: normal;color: #FF8400;">操作提示</h5>';
				html += '<p style="color:#FF8400;font-size:12px;">1、双击规格值进行编辑操作(回车按钮保存)。</p>';
				html += '<p style="color:#FF8400;font-size:12px;">2、鼠标浮上图片时，可以进行预览。</p>';
			html += '</div>';
		html += '</td>';
	html += '</tr>';
	return html;
}

function getGoodsSpecListNotAttrId(){
	if(goods_spec_format == "") return;

	goods_spec_format = eval(goods_spec_format);
	var html = getGoodsSpecHeaderHtml();
	var spec_length = goods_spec_format.length;
	var spec_list = goods_spec_format;
	for(var i=0;i<spec_length;i++){
		
		var curr_spec = spec_list[i];
		html += '<tr class="js-spec-item goods-sku-block-'+curr_spec.spec_id+'">';
			html += '<td width="10%">' + curr_spec.spec_name + "</td>";
			html += '<td width="85%">';
		
			for(var j=0;j<curr_spec.value.length;j++){
				var curr_spec_value = curr_spec.value[j];
				html += '<article class="goods-sku-item">';
				
					html += '<span data-spec-name="'+curr_spec.spec_name+'"';
					html += ' data-spec-id="'+curr_spec.spec_id+'"';
					if(parseInt(curr_spec.show_type) == 2 && curr_spec_value.spec_value_data == ""){
						curr_spec_value.spec_value_data = "#000000";
					}
					html += ' data-spec-value-data="' + curr_spec_value.spec_value_data + '"';
					html += ' data-spec-show-type="' + curr_spec_value.spec_show_type + '"';
					if(curr_spec_value.spec_show_type == 3){
						html += ' data-spec-value-data-src="'+curr_spec_value.spec_value_data_src+'"';
					}
					html += ' data-spec-value-id="'+curr_spec_value.spec_value_id+'">';
					html += curr_spec_value.spec_value_name + "</span>";
					
					//显示方式
					switch(parseInt(curr_spec_value.spec_show_type)){
						case 1:
							//文字
							break;
						case 2:
							//颜色
							html += '&nbsp;<i></i>&nbsp;';
							html += '<div>';
							html += '<input type="color" name="goods_spec_value'+(i+j)+'" value="'+curr_spec_value.spec_value_data+'">';
							html += '</div>';
							break;
						case 3:
							//图片
							var index = curr_spec.spec_id + curr_spec_value.spec_value_id;
							html += '&nbsp;<i></i>&nbsp;';
							html += '<div class="js-goods-spec-value-img sku-img-check" data-html="true" data-container="body" data-placement="top" data-trigger="manual">';
							html += '<input type="hidden" id="goods_sku'+index+'" value="'+curr_spec_value.spec_value_data+'" >';
							if(curr_spec_value.spec_value_data != ""){
								html += '<img src="'+__IMG(curr_spec_value.spec_value_data_src)+'" id="imggoods_sku'+index+'"/>';
							}else{
								html += '<img src="'+ADMINIMG+'/goods/goods_sku_add.png"  id="imggoods_sku'+index+'"/>';
							}
							html += '</div>';
							break;
					}
				
				html += '</article>';
				
			}
			var spec = {
				spec_id : curr_spec.spec_id,
				spec_name : curr_spec.spec_name,
				show_type : curr_spec.value[0]["spec_show_type"]
			};
			html += getAddSpecValueHtml(spec);//添加规格值按钮
			html += '</td>';
		html += '</tr>';
	}
	
	html += '<tr>';
	if(spec_length == 0){
		html += '<td class="js-spec-add"  style="text-align:left;">'+getAddSpecHtml()+'</td>';//规格添加
	}else{
		html += '<td class="js-spec-add">'+getAddSpecHtml()+'</td>';//规格添加
	}
		html += '<td></td>';
	html += '</tr>';
	
	$(".js-goods-spec-block").show();
	$(".js-goods-sku").html(html);
	editSkuData(goods_spec_format,sku_list);
}

/**
 * 根据商品类型id，查询商品规格信息
 * 2017年6月6日 11:38:47 王永杰
 * @param attr_id 规格属性id
 */ 
function getGoodsSpecListByAttrId(attr_id,callBack){
	if(!isNaN(attr_id) && attr_id > 0){
		$.ajax({
			url : __URL(ADMINMAIN+"/goods/getGoodsSpecListByAttrId"),
			type : "post",
			data : { "attr_id" : parseInt(attr_id)},
			success : function(res){
				if(res !=-1){
					var html = getGoodsSpecHeaderHtml();
					var spec_length = res.spec_list.length;
					var attribute_length = res.attribute_list.length;
					//商品规格集合
					if(spec_length>0){
						
						for(var i=0;i<spec_length;i++){
							
							var curr_spec = res.spec_list[i];
							html += '<tr class="js-spec-item goods-sku-block-'+curr_spec.spec_id+'">';
								html += '<td width="10%">' + curr_spec.spec_name + "</td>";
								html += '<td width="85%">';
							
								for(var j=0;j<curr_spec.values.length;j++){
									var curr_spec_value = curr_spec.values[j];
									html += '<article class="goods-sku-item">';
										html += '<span data-spec-name="'+curr_spec.spec_name+'"';
										html += ' data-spec-id="'+curr_spec.spec_id+'"';
										if(parseInt(curr_spec.show_type) == 2 && curr_spec_value.spec_value_data == ""){
											curr_spec_value.spec_value_data = "#000000";
										}
										html += ' data-spec-value-data="' + curr_spec_value.spec_value_data + '"';
										html += ' data-spec-show-type="' + curr_spec.show_type + '"';
										html += ' data-spec-value-id="'+curr_spec_value.spec_value_id+'">';
										html += curr_spec_value.spec_value_name + "</span>";
										
										//显示方式
										switch(parseInt(curr_spec.show_type)){
											case 1:
												//文字
												break;
											case 2:
												//颜色
												html += '&nbsp;<i></i>&nbsp;';
												html += '<div>';
												html += '<input type="color" name="goods_spec_value'+(i+j)+'" value="'+curr_spec_value.spec_value_data+'">';
												html += '</div>';
												break;
											case 3:
												//图片
												var index = curr_spec.spec_id + curr_spec_value.spec_value_id;
												html += '&nbsp;<i></i>&nbsp;';
												html += '<div class="js-goods-spec-value-img sku-img-check" data-html="true" data-container="body" data-placement="top" data-trigger="manual">';
												html += '<input type="hidden" id="goods_sku'+index+'" value="'+curr_spec_value.spec_value_data+'" >';
												if(curr_spec_value.spec_value_data != ""){
													html += '<img src="'+__IMG(curr_spec_value.spec_value_data)+'" id="imggoods_sku'+index+'"/>';
												}else{
													html += '<img src="'+ADMINIMG+'/goods/goods_sku_add.png"  id="imggoods_sku'+index+'"/>';
												}
												html += '</div>';
												break;
										}
									
									html += '</article>';
									
								}
								var spec = {
									spec_id : curr_spec.spec_id,
									spec_name : curr_spec.spec_name,
									show_type : curr_spec.show_type
								};
								html += getAddSpecValueHtml(spec);//添加规格值按钮
								html += '</td>';
							html += '</tr>';
						}
						
						html += '<tr>';
							html += '<td class="js-spec-add">'+getAddSpecHtml()+'</td>';//规格添加
							html += '<td></td>';
						html += '</tr>';
						$(".js-goods-sku").html(html);
					}else{
						$(".js-goods-sku tr.js-spec-item").remove();
						$(".js-goods-sku tr .js-spec-add").css("text-align","left");
					}
					//商品属性集合
					if(attribute_length>0){
						var html ="";
						for(var i=0;i<attribute_length;i++){
							var curr = res.attribute_list[i];
							if($.trim(curr.value_items) == "" && parseInt(curr.type) !=1) continue;
							if($.trim(curr.attr_value_name) != ""){
								
							
							html += '<tr style="padding-top:15px;padding-bottom:15px;">';
								html += '<td width="10%" style="border:1px solid #E9E9E9;"align="right" class="txt12" data-value="'+curr.attr_value_name+'">'+curr.attr_value_name+'</td>';
								html += '<td width="80%" style="border:1px solid #E9E9E9;">';
									switch(parseInt(curr.type)){
										case 1:
											//输入框
											html += '<input type="text" class="js-attribute-text" id="input-text-'+curr.attr_value_id+'-'+curr.attr_value_id+'"data-attribute-value-id="'+curr.attr_value_id+'" data-attribute-value="'+curr.attr_value_name+'" data-attribute-sort="'+curr.sort+'"/>';
											break;
										case 2:
											//单选框
											for(var j=0;j<curr.value_items.length;j++){
												var value = curr.value_items[j];
												if($.trim(value) != ""){
													html += '<div class="goods-sku-attribute-item-radio">';
														html += '<input type="radio" value="'+value+'" class="js-attribute-radio" id="radio_value_item'+curr.attr_value_id+'-'+j+'" data-attribute-value-id="'+curr.attr_value_id+'" data-attribute-value="'+curr.attr_value_name+'"  name="radio_value'+i+'" data-attribute-sort="'+curr.sort+'"/>&nbsp;';
														html += '<label for="radio_value_item'+curr.attr_value_id+'-'+j+'">'+value+'</label>';
													html += '</div>';
												}
											}
											break;
										case 3:
											//复选框
											for(var j=0;j<curr.value_items.length;j++){
												var value = curr.value_items[j];
												if($.trim(value) != ""){
													html += '<div class="goods-sku-attribute-item-checkbox">';
														html += '<input type="checkbox" value="'+value+'" class="js-attribute-checkbox" id="checkbox_value_item'+curr.attr_value_id+'-'+j+'" data-attribute-value-id="'+curr.attr_value_id+'" data-attribute-value="'+curr.attr_value_name+'"  name="checkbox_value_item'+i+'" data-attribute-sort="'+curr.sort+'"/>&nbsp;';
														html += '<label for="checkbox_value_item'+curr.attr_value_id+'-'+j+'">'+value+'</label>';
													html += '</div>';
												}
											}
											break;
									}
								html += '</td>';
							html += '</tr>';
							}
						}
						$(".js-goods-sku-attribute").html(html);
					}
					if(callBack != undefined) callBack();
					$(".js-goods-spec-block").show();
					$(".js-goods-attribute-block").show();

				}
			}
		});
	}else{
		//标识 0：表示添加商品，1：表示编辑商品，商品分类为0,2：表示编辑商品，商品分类不为0
		switch(parseInt($("#goodsType").attr("data-flag"))){
		case 0:
			var html = getGoodsSpecHeaderHtml();
			html += '<tr>';
			html += '<td class="js-spec-add" style="text-align:left;">'+getAddSpecHtml()+'</td>';//规格添加
			html += '<td></td>';
			html += '</tr>';
			$(".js-goods-sku").html(html);
			$(".js-goods-spec-block").show();
			break;
		case 1:
			//如果当前商品的商品类型为0，则不根据商品类型id加载数据
			getGoodsSpecListNotAttrId();
			break;
		case 2:
			$(".js-goods-sku tr.js-spec-item").remove();
			$(".js-goods-sku tr .js-spec-add").css("text-align","left").next().remove();
			break;
		}
	}
}

//验证
function ValidateUserInput() {
	var shop_type = $("#shop_type").val();
	var isflag = 0;
	if($("#tbcNameCategory").attr("cid") == undefined  || $("#tbcNameCategory").attr("cid")==""){
		$("#tbcNameCategory .help-inline").show();
		$('html,body').animate({scrollTop : 0 }, 200);
		return false;
	}else{
		$("#tbcNameCategory .help-inline").hide();
	}
	var id_extend_null = false;
	$(".extend-name-category").each(function() {
		if(!($(this).attr("cid") > 0)){
			$(this).find(".help-inline").show();
			id_extend_null = true;
			return false;
		}
	});
	
	if(id_extend_null){
		$('html,body').animate({scrollTop : 0 }, 200);
		return false;
	}else{
		$(".extend_name_category_box .help-inline").show();
	}
	
	if($("input[name='goods_type']:checked").val() == 0){
		if($("#virtual_goods_type_id").val() == 0){
			showTip("请选择虚拟商品类型","warning");
			$('html,body').animate({scrollTop : 100 }, 200);
			return false;
		}
	}
	
	// 商品标题
	if (!IsEmpty("#txtProductTitle")) {
		$("#txtProductTitle").next("span").show();
		$("#txtProductTitle").focus();
		return false;
	}else if($("#txtProductTitle").val().length>60){
		//$("#txtProductTitle").next("span").show();
		$("#txtProductTitle").nextAll("span:last").text("商品标题不能大于60个字").css("color","red");
		$("#txtProductTitle").nextAll("span:last").show();
		$("#txtProductTitle").focus();
		return false;
	} else {
		$("#txtProductTitle").next("span").hide();
	}
	
	// 副标题
	if($("#txtIntroduction").val().length>60){
		$("#txtIntroduction").focus();
		$("#txtIntroduction").next("span").show();
		return false;
	} else{
		$("#txtIntroduction").next("span").hide();
	}
	
	if($("#txtKeyWords").val().length>0 && $("#txtKeyWords").val().length>40){
		$("#txtKeyWords").focus();
		$("#txtKeyWords").next("span").show();
		return false;
	}else{
		$("#txtKeyWords").next("span").hide();
	}
	
	// 商品原价
	/*if (!IsNum("#txtProductSalePrice") || parseFloat($("#txtProductSalePrice").val()) < 0.01) {
		$("#txtProductSalePrice").nextAll("span:last").text("商品销售价不能为空，且大于0").css("color","red");
		$("#txtProductSalePrice").nextAll("span:last").show();
		$("#txtProductSalePrice").focus();
		return false;
	} else {
		var price_s = $("#txtProductSalePrice").val();
		var c_price = parseFloat(price_s);
		$("#txtProductSalePrice").nextAll("span:last").hide();
	}
	// 总库存
	if (!IsPositiveNum("#txtProductCount")) {
		$("#txtProductCount").nextAll("span:last").show();
		$("#txtProductCount").focus();
		return false;
	} else {
		$("#txtProductCount").nextAll("span:last").hide();
	}
	if (parseInt($("#txtProductCount").val()) <= 0) {
		$("#txtProductCount").nextAll("span:last").show();
		$("#txtProductCount").focus();
		return false;
	} else {
		$("#txtProductCount").nextAll("span:last").hide();
	}*/
	// 市场价
//	if (!IsNum("#txtProductMarketPrice") || parseFloat($("#txtProductMarketPrice").val()) < 0.01) {
//		$("#txtProductMarketPrice").nextAll("span:last").text("商品市场价不能为空，且大于0");
//		$("#txtProductMarketPrice").nextAll("span:last").show();
//		$("#txtProductMarketPrice").focus();
//		return false;
//	} else {
//		var price_s = $("#txtProductMarketPrice").val();
//		var c_price = parseFloat(price_s);
//		$("#txtProductMarketPrice").nextAll("span:last").hide();
//	}
	
	// 商品成本价
//	if (!IsNum("#txtProductCostPrice") || parseFloat($("#txtProductCostPrice").val()) < 0.01) {
//		$("#txtProductCostPrice").nextAll("span:last").text("商品成本价不能为空，且大于0");
//		$("#txtProductCostPrice").nextAll("span:last").show();
//		$("#txtProductCostPrice").focus();
//		return false;
//	} else {
//		var price_s = $("#txtProductCostPrice").val();
//		var c_price = parseFloat(price_s);
//		$("#txtProductCostPrice").nextAll("span:last").hide();
//	}
//	
	if($("#txtProductCodeA").val().length>0 && $("#txtProductCodeA").val().length>40){
		$("#txtProductCodeA").focus();
		$("#txtProductCodeA").next("span").show();
		return false;
	}else{
		$("#txtProductCodeA").next("span").hide();
	}
	
	if($("#goodsType").val()>0){
		//验证SKU输入是否正确 isflag: 0：成功，1：失败
		//var isflag = 0;
		//sku价格
		$.each($('input[name="sku_price"]'), function(i, item) {
			var $this = $(item);
			if (parseFloat($this.val()) < 0.01 || $.trim($this.val()) == "") {
				$this.val("0.00");
//				$this.css("border-color", "#b94a48");
//				$this.parent().find(".help-inline").text("价格最小为 0");
//				$this.parent().find(".help-inline").show();
//				isflag = 1;
			} else {
				num = parseInt($this.val());
				$this.css("border-color", "");
				$this.parent().find(".help-inline").hide();
			}
		});
	
		$.each($('input[name="market_price"]'), function(i, item) {
			var $this = $(item);
			if (parseFloat($this.val()) < 0.01 || $.trim($this.val()) == "") {
				$this.val("0.00");
//				$this.css("border-color", "#b94a48");
//				$this.parent().find(".help-inline").text("价格不能为0");
//				$this.parent().find(".help-inline").show();
//				isflag = 1;
			} else {
				num = parseInt($this.val());
				$this.css("border-color", "");
				$this.parent().find(".help-inline").hide();
			}
		});
	
		$.each($('input[name="cost_price"]'), function(i, item) {
			var $this = $(item);
			if (parseFloat($this.val()) < 0.01 || $.trim($this.val()) == "") {
				$this.val("0.00");
//				$this.css("border-color", "#b94a48");
//				$this.parent().find(".help-inline").text("价格不能为0");
//				$this.parent().find(".help-inline").show();
//				isflag = 1;
			} else {
				num = parseInt($this.val());
				$this.css("border-color", "");
				$this.parent().find(".help-inline").hide();
			}
		});
		
		// 库存
		$.each($('input[name="stock_num"]'), function(i, item) {
			var $this = $(item);
			if ($.trim($this.val()) == "" || $.trim($this.val()) < 0) {
				$this.css("border-color", "#b94a48");
				$this.parent().find(".help-inline").text("总库存不能为0");
				$this.parent().find(".help-inline").show();
				isflag = 1;
			} else {
				num = parseInt($this.val());
				$this.css("border-color", "");
				$this.parent().find(".help-inline").hide();
			}
		});
		//验证SKU输入是否正确 isflag: 0：成功，1：失败
		if (isflag == 1) {
			$("body").scrollTop($("#txtProductCount").offset().top+100);
			//$("body").scrollTop($("#txtProductCount").offset().top-300);
			return false;
		}
		
//		if($(".js-attribute-text").length>0){
//			var attribute_flag = false;
//			$(".js-attribute-text").each(function(){
//				if($(this).val() == ""){
//					attribute_flag = true;
//				}
//			});
//			if(attribute_flag){
//				showTip("请输入商品属性","warning");
//				return false;
//			}
//		}
		
//		if($(".js-attribute-radio").length>0){
//			var curr_attribute_radio_length = 0;
//			$(".js-attribute-radio").each(function(){
//				
//				if($(this).is(":checked")){
//					curr_attribute_radio_length++;
//				}
//			});
//			if(curr_attribute_radio_length = 0){
//				showTip("请选择商品属性","warning");
//				return false;
//			}
//		}
	
//		if($(".js-attribute-checkbox").length>0){
//			var curr_attribute_checkbox_length = 0;
//			$(".js-attribute-checkbox").each(function(){
//				if($(this).is(":checked")){
//					curr_attribute_checkbox_length++;
//				}
//			});
//			if(curr_attribute_checkbox_length == 0){
//				showTip("请选择商品属性","warning");
//				return false;
//			}
//		}
	
	}
	
	if(isflag == 0){
		// 商品原价
		if (!IsNum("#txtProductSalePrice") || parseFloat($("#txtProductSalePrice").val()) < 0) {
			$("#txtProductSalePrice").nextAll("span:last").text("商品销售价不能为空，且不能为负数").css("color","red");
			$("#txtProductSalePrice").nextAll("span:last").show();
			$("#txtProductSalePrice").focus();
			return false;
		} else {
			var price_s = $("#txtProductSalePrice").val();
			var c_price = parseFloat(price_s);
			$("#txtProductSalePrice").nextAll("span:last").hide();
		}
		// 总库存
		if (!IsPositiveNum("#txtProductCount")) {
			$("#txtProductCount").nextAll("span:last").show();
			$("#txtProductCount").focus();
			return false;
		} else {
			$("#txtProductCount").nextAll("span:last").hide();
		}
		if (parseInt($("#txtProductCount").val()) < 0) {
			$("#txtProductCount").nextAll("span:last").show();
			$("#txtProductCount").focus();
			return false;
		} else {
			$("#txtProductCount").nextAll("span:last").hide();
		}
	}
	
	// 库存预警
	if (!IsPositiveNum("#txtMinStockLaram")) {
		$("#txtMinStockLaram").nextAll("span:last").show();
		$("#txtMinStockLaram").focus();
		return false;
	} else {
		$("#txtMinStockLaram").nextAll("span:last").hide();
	}
	//最少购买数
	if ($("#minBuy").val() < 0) {
		$("#minBuy").nextAll("span:last").show();
		$("#minBuy").focus();
		return false;
	} else {
		$("#minBuy").nextAll("span:last").hide();
	}

	if (parseInt($("#txtMinStockLaram").val()) < 0) {
		$("#txtMinStockLaram").nextAll("span:last").show();
		$("#txtMinStockLaram").focus();
		return false;
	} else {
		$("#txtMinStockLaram").nextAll("span:last").hide();
	}

	var imgflag = false;// 默认：false。
	var imgtop = 0;// 如果没有商品图片，就定位到这个位置
	
	if($(".upload_img_id").length == 0){
		imgtop = $(".ncsc-goods-default-pic").offset().top - 200;
		$("body").scrollTop(imgtop);
		$(".img-error").text("最少需要一张图片作为商品主图").show();
		return false;
	}else{
		$(".img-error").hide();
	}
//	for (var i = 0; i < 5; i++) {
//		if ($("#file_upload_img_" + (i + 1)).attr("data-id") == null
//				|| $("#file_upload_img_" + (i + 1)).attr("data-id") == "") {
//			imgflag = true;
//			imgtop = $("#file_upload_img_" + (i + 1)).offset().top - 200;
//		} else {
//			imgflag = false;
//			break;
//		}
//	}
	// 商品图
//	if (imgflag) {
//		$(".example span").text('需要设置第一张图片为商品主图').show();
//		$(".example span").addClass('error');
//		$("body").scrollTop($("#file_upload_img_1").offset().top-300);
//		return false;
//	} else {
//		$("#upImgDiv").nextAll("span:last").hide();
//		$("#upImgDiv").parent().parent().removeClass('error');
//	}
//	var temp_arr = img_id_arr.split(",");
//	for (var k = 0; k < temp_arr.length; k++) {
//		// 取出每一个跟全部进行比较
//		var id = temp_arr[k];
//		for (var j = (k + 1); j < temp_arr.length; j++) {
//			// console.log("id:"+id+",j:"+temp_arr[j]);
//			if (id == temp_arr[j]) {
//				$(".example span").text('商品图片存在重复').show();
//				$(".example span").addClass('error');
//				$("body").scrollTop($("#file_upload_img_1").offset().top-300);
//				return false;
//			}
//		}
//	}
	// 商品描述
	var description = UE.getEditor('editor').getContent();

	description = description.replace(/(\n)/g, "");
	description = description.replace(/(\t)/g, "");
	description = description.replace(/(\r)/g, "");
	description = description.replace(/\s*/g, "");
	if (description == "") {
		$("#tareaProductDiscrip").nextAll("span:last").text("商品描述不能为空");
		$("#tareaProductDiscrip").nextAll("span:last").show();
		$("body").scrollTop($("#discripContainer").offset().top-100);
		return false;
	} else if (description.length < 5 || description.length > 25000) {
		$("#tareaProductDiscrip").nextAll("span:last").text("商品描述字符数应在5～25000之间");
		$("#tareaProductDiscrip").nextAll("span:last").show();
		$("body").scrollTop($("#discripContainer").offset().top-100);
		return false;
	} else {
		$("#tareaProductDiscrip").nextAll("span:last").hide();
	}
	
	var reg_integral = /^\+?[1-9][0-9]*$/;
	//如果是积分商品，则必须设置积分
	if($("input[name='integralSelect']:checked").val() == 1){
		if($("#integration_available_use").val()=="" || $("#integration_available_use").val()==0){
			$("#integration_available_use").nextAll("span:last").text("必须设置积分");
			$("#integration_available_use").nextAll("span:last").show();
			/*$("#integration_available_use").focus();
			$("#integration_available_use").next().show();*/
			return false;
		}else if(!reg_integral.test($("#integration_available_use").val())){
			$("#integration_available_use").nextAll("span:last").text("积分必须为整数");
			$("#integration_available_use").nextAll("span:last").show();
			return false;
		}else{
			$("#integration_available_use").next().hide();
		}
	}
	
	// 运费设置
	if ($("input[name='fare']:checked").val() == 1) {
		if($("input[name='shipping_fee_type']:checked").val() == 2){
			var goods_volume = parseFloat($("#goods_volume").val()).toFixed(2);
			if(goods_volume == '' || goods_volume <= 0){
				$("#goods_volume").focus();
				$("#goods_volume").next("span").show();
				$("#goods_weight").next("span").hide();
				return false;
			}else{
				$("#goods_volume").next("span").hide();
			}
		}else if($("input[name='shipping_fee_type']:checked").val() == 1){
			var goods_weight = parseFloat($("#goods_weight").val()).toFixed(2);
			if(goods_weight == '' || goods_weight <= 0){
				$("#goods_weight").focus();
				$("#goods_weight").next("span").show();
				$("#goods_volume").next("span").hide();
				return false;
			}else{
				$("#goods_weight").next("span").hide();
			}
		}
	}
	// 商品分组
	/*if ($("#productcategory-selected span").length == 0 && (shop_type == 2 || shop_type == 4)) {
		$("#productcategory-selected").nextAll('span').show();
		$('html,body').animate({scrollTop : 2500 }, 200);
		//$('html,body').animate({scrollTop : 0 }, 200);
		return false;
	} else {
		$("#productcategory-selected").nextAll("span").hide();
	}*/
	//最小购买数限制
	if(!(parseInt($("#PurchaseSum").val()) >= parseInt($("#minBuy").val())) && (parseInt($("#PurchaseSum").val()) > 0)){
		imgtop = $("#PurchaseSum").offset().top - 200;
		$("body").scrollTop(imgtop);
		$("#minBuy").next("span").text("限购数不为0时,最小购买数必须小于等于限购数量").show();
		return false;
	}else{
		$("#minBuy").next("span").hide();
	}
	var shopCategoryText = "";
	// 商品分组
	/*$("#productcategory-selected span").each(function() {
		shopCategoryText += $(this).attr("id") + ",";
	})
	if (shopCategoryText == null || shopCategoryText == '') {
		$("#productcategory-selected").nextAll("span:last").show();
		document.documentElement.scrollTop = document.body.scrollTop = 0;
		return false;
	} else {
		$("#productcategory-selected").nextAll("span:last").hide();
	}*/
	return true;
}

var flag = false;//防止重复提交
//保存商品
function SubmitProductInfo(type, ADMIN_MAIN,SHOP_MAIN) {
	img_id_arr = "";// 商品主图
	//var leftCss = new Array();
	// 第一个循环对商品图片进行排序
//	for (var j = 0; j < 5; j++) {
//		var left = $("#file_upload_img_" + (j + 1)).parent().css("left")
//				.replace("px", "");// 获取每个图片对应的坐标位置
//		var imgid = $("#file_upload_img_" + (j + 1)).parent().attr("js-img");
//		leftCss.push(left + ":" + imgid);
//	}
//	leftCss.sort();// 对数据进行排序
//	for (var i = 0; i < leftCss.length; i++) {
//		var index = leftCss[i].split(":")[1];
//		if ($("#file_upload_img_" + (index)).attr("data-id") != null
//				&& $("#file_upload_img_" + (index)).attr("data-id") != '') {
//			img_id_arr += $("#file_upload_img_" + (index)).attr("data-id")
//					+ ",";
//		}
//	}
	var img_obj = $(".upload_img_id");
	for( var $i=0; $i<img_obj.length;$i++){
		var $checkObj=$(img_obj[$i]);
		if(img_id_arr == ""){
			img_id_arr = $checkObj.val();
		}else{
			img_id_arr +=","+ $checkObj.val();
		}
	}
	//img_id_arr = img_id_arr.substr(0, img_id_arr.length - 1);
	// 禁用按钮
	var validateResult = ValidateUserInput(); // 验证用户输
	if (validateResult) {
		$("#btnSave,#btnSavePreview").attr("disabled", "disabled");
		var productViewObj = PackageProductInfo();
		var $qrcode = $("#hidQRcode").val();
		if(flag) return;
		flag = true;
//		 var asd = JSON.stringify(productViewObj);
//		return;
		$.ajax({
			url : __URL(ADMINMAIN + "/goods/GoodsCreateOrUpdate"),
			type : "post",
			async : false,
			data : { "product" : JSON.stringify(productViewObj) , "is_qrcode" : $qrcode},
			dateType : "json",
			success : function(res) {
				var url = __URL(ADMIN_MAIN + "/goods/goodslist");
				var goodsId = parseInt($("#goodsId").val());

				var text = "";
				if (res != null) {
					if (type == 1) {
						var parameter_goodsid = goodsId;
						if(parameter_goodsid==0 || typeof(parameter_goodsid) == 'undefined'){
							parameter_goodsid = res;
						}
						url = __URL(SHOP_MAIN + "/goods/goodsinfo?goodsid="+parameter_goodsid);// 跳转到前台
						window.open(url);
					}
					showMessage('success', "商品发布成功",__URL(ADMIN_MAIN +'/goods/goodslist'));
				} else {
					showMessage('error', "商品发布失败",url);
					flag = false;
					$("#btnSave,#btnSavePreview").removeAttr("disabled")
				}
			}
		});
	}
}

/**
 * 创建时间：2015年6月11日18:07:10 创建人：高伟 功能说明：获取数据已对象方式存储
 */
function PackageProductInfo() {
	// 初始化一个实体 将页面所需的数据存放到对象中
	var shop_type = $("#shop_type").val();
	var productViewObj = new Object();
	productViewObj.goodsId = $("#goodsId").val();// 商品id 11号目前为死值 0
	productViewObj.title = $("#txtProductTitle").val().replace(/^\s*/g, "").replace(/\s*$/g, "");// 商品标题
	productViewObj.goods_type = $("input[name='goods_type']:checked").val();
	productViewObj.introduction = $("#txtIntroduction").val().replace(/^\s*/g, "").replace(/\s*$/g, "");// 商品简介，促销语
	productViewObj.categoryId = $("#tbcNameCategory").attr("cid");// 商品类目 
	var category_extend_id ="";
	$(".extend-name-category").each(function() {
		if(category_extend_id == ""){
			category_extend_id = $(this).attr("cid");
		}else{
			category_extend_id += "," + $(this).attr("cid");
		}
	})
	productViewObj.categoryExtendId = category_extend_id;// 商品扩展类目
	// 12号 商品类目；
	productViewObj.market_price = $("#txtProductMarketPrice").val().replace(/^\s*/g, "").replace(/\s*$/g, "") == "" ? 0 : $("#txtProductMarketPrice").val().replace(/^\s*/g, "").replace(/\s*$/g, "");// 市场价
	productViewObj.price = $("#txtProductSalePrice").val().replace(/^\s*/g, "").replace(/\s*$/g, "") == "" ? 0 : $("#txtProductSalePrice").val().replace(/^\s*/g, "").replace(/\s*$/g, "");// 销售价
	productViewObj.cost_price = $("#txtProductCostPrice").val().replace(/^\s*/g, "").replace(/\s*$/g, "") == "" ? 0 : $("#txtProductCostPrice").val().replace(/^\s*/g, "").replace(/\s*$/g,"");// 成本价
	productViewObj.libiary_goodsid = $("#libiary_goodsid").val(); // 商品库id
	productViewObj.base_sales = $("#BasicSales").val() == '' ? 0 : $("#BasicSales").val();// 基础销量
	productViewObj.base_good = $("#BasicPraise").val() == '' ? 0 : $("#BasicPraise").val();// 基础点赞数
	productViewObj.base_share = $("#BasicShare").val() == '' ? 0 : $("#BasicShare").val();// 基础分享数
	productViewObj.code = $("#txtProductCodeA").val();// 商品编码
	productViewObj.is_sale = $("input[name='shelves']:checked").val();// 上下架标记
	productViewObj.display_stock = $('.controls input[name="stock"]:checked ').val();// 是否显示库存
	productViewObj.stock = $("#txtProductCount").val();// 总库存
	productViewObj.minstock = $("#txtMinStockLaram").val();// 库存预警数
	productViewObj.max_buy = $("#PurchaseSum").val().replace(/^\s*/g, "").replace(/\s*$/g, "") == "" ? 0 : $("#PurchaseSum").val().replace(/^\s*/g, "").replace(/\s*$/g, "");// 每人限购
	productViewObj.min_buy = $("#minBuy").val().replace(/^\s*/g, "").replace(/\s*$/g, "") == "" ? 0 : $("#minBuy").val().replace(/^\s*/g, "").replace(/\s*$/g, "");// 最少购买数
	productViewObj.key_words = $("#txtKeyWords").val().replace(/^\s*/g, "").replace(/\s*$/g, "");//商品关键词
	productViewObj.description = UE.getEditor('editor').getContent().replace(/\n*/g, "").replace(/\r*/g, "");// 商品详情描述
	productViewObj.shipping_fee = $("input[name='fare']:checked").val();// 运费方式
	productViewObj.shipping_fee_id = $("#expressCompany").val();
	/*productViewObj.shipping_fee_id = $("input[name='fare']:checked").val() != 1 ? 0 : $("#deliverySelect").val();*/// 运费模板编号
	//alert(JSON.stringify(productViewObj));
	var shopCategoryText = "";
	$(".goods-group-line .goods-gruop-select").each(function() {
		if($(this).val() > 0){
			shopCategoryText += $(this).val() + ",";
		}
	})
	if (shopCategoryText != "") {
		shopCategoryText = shopCategoryText.substring(0,shopCategoryText.length - 1);
		 var goodsgroup_array = shopCategoryText.split(",");
		 var goodsgroup_array = undulpicate(goodsgroup_array);
		 shopCategoryText = goodsgroup_array.join(",");
	}
	productViewObj.groupArray = shopCategoryText;
	productViewObj.supplierId = $("#supplierSelect").val();//供货商
	productViewObj.brandId = $("#brand_id").val();//品牌id
	productViewObj.picture = img_id_arr.split(",")[0];
	var imageVals = img_id_arr;// 在页面中获取的
	productViewObj.imageArray = imageVals;// 商品图片分组
	//sku规格图片
	var sku_img_obj = $(".sku_upload_img_id");
	var sku_picture_obj = new Array();
	for( var $i=0; $i<sku_img_obj.length;$i++){
		var $checkObj = $(sku_img_obj[$i]);
		var spec_id = $checkObj.attr("spec_id");
		var spec_value_id = $checkObj.attr("spec_value_id");
		var img_id = $checkObj.val();
		var is_have = 0;
		for(var i = 0; i < sku_picture_obj.length ; i ++ ){
			if(sku_picture_obj[i].spec_id == spec_id && sku_picture_obj[i].spec_value_id == spec_value_id){
				sku_picture_obj[i]["img_ids"] = sku_picture_obj[i]["img_ids"]+","+img_id;
				is_have = 1;
			}
		}
		if(is_have == 0){
			//给此规格添加对象内部空间 并添加此属性
			var obj_length = sku_picture_obj.length;
			sku_picture_obj[obj_length] = new Object();
			sku_picture_obj[obj_length].spec_id = spec_id;
			sku_picture_obj[obj_length].spec_value_id = spec_value_id;
			sku_picture_obj[obj_length]["img_ids"] = img_id;
		
		}
	}
	productViewObj.sku_picture_vlaues = JSON.stringify(sku_picture_obj);
	productViewObj.skuArray = synchroSkuValueData();
	productViewObj.goods_spec_format = JSON.stringify($specObj);
	productViewObj.goods_attribute_id= $("#goodsType").val();
	productViewObj.sort = $("#hidden_sort").val();
	var goods_attribute_arr = new Array();
	$(".js-attribute-text").each(function(){
		var goods_attribute = {
			attr_value_id :$(this).attr("data-attribute-value-id"),
			attr_value : $(this).attr("data-attribute-value"),
			attr_value_name : $(this).val(),
			sort : $(this).attr("data-attribute-sort")
		};
		goods_attribute_arr.push(goods_attribute);
	});

	$(".js-attribute-radio").each(function(){
		if($(this).is(":checked")){
			var goods_attribute = {
				attr_value_id :$(this).attr("data-attribute-value-id"),
				attr_value : $(this).attr("data-attribute-value"),
				attr_value_name : $(this).val(),
				sort : $(this).attr("data-attribute-sort")
			};
			goods_attribute_arr.push(goods_attribute);
		}
	});

	$(".js-attribute-checkbox").each(function(){

		if($(this).is(":checked")){
			var goods_attribute = {
				attr_value_id :$(this).attr("data-attribute-value-id"),
				attr_value : $(this).attr("data-attribute-value"),
				attr_value_name : $(this).val(),
				sort : $(this).attr("data-attribute-sort")
			};
			goods_attribute_arr.push(goods_attribute);
		}
	});
	productViewObj.goods_attribute = "";
	if(goods_attribute_arr.length>0){
		productViewObj.goods_attribute = JSON.stringify(goods_attribute_arr);
	}
	// 积分购买设置
	productViewObj.integration_available_use = $("#integration_available_use").val() == '' ? 0 : $("#integration_available_use").val();
	productViewObj.integration_available_give = $("#integration_available_give").val() == '' ? 0 : $("#integration_available_give").val();
	productViewObj.goods_class = $("#class_tbname").attr("cid") == '' ? 0 : $("#class_tbname").attr("cid");
	productViewObj.goods_returnRate = $("#txtGoodsReturnRate").val() == '' ? 0 : $("#txtGoodsReturnRate").val();
	if (shop_type == 1) {
		productViewObj.sup_shopid = $("#sup_shopidselect").val();
		productViewObj.sale_area = $("#txtGoodsAreasid").val();
		productViewObj.sup_price = $("#txtProductSupplyPrice").val();
		productViewObj.cb_cost_price = $("#txtProductCBCostPrice").val();
	} else {
		productViewObj.sup_shopid = 0;
		productViewObj.sale_area = "";
		productViewObj.sup_price = 0;
		productViewObj.cb_cost_price = 0;
	}
	//productViewObj.point_exchange_type = $("#integralSelect").val();
	productViewObj.point_exchange_type = $("input[name='integralSelect']:checked").val();
	productViewObj.province_id = $("#provinceSelect").val();// 商品所在地：省
	productViewObj.city_id = $("#citySelect").val();// 商品所在地：市
	productViewObj.qrcode  = $("#hidden_qrcode").val();
	//物流信息
	productViewObj.goods_weight = $("#goods_weight").val();
	productViewObj.goods_volume = $("#goods_volume").val();
	productViewObj.shipping_fee_type = $("input[name='shipping_fee_type']:checked").val();;
	productViewObj.virtual_goods_type_id = $("#virtual_goods_type_id").val();//虚拟商品类型id
	productViewObj.production_date = $("#production_date").val(); //生产日期
	productViewObj.shelf_life = $("#shelf_life").val(); // 保质期
	return productViewObj;
}

 //上传图片，可以多图一起，也可以单图  调用
function UploadImage(event, flag) {
	/*shopImageFlag = flag;//所点击的商品图片标识
	speciFicationsFlag = 0;
	OpenPricureDialog("PopPicure", ADMINMAIN, 5);*/
}

//处理积分非法输入
function integrationChange(event) {
	$integration_val = $(event).val();
 	if ($integration_val < 0) {
		$(event).val(0);
	}
}

//非空判断
function IsEmpty(obj) {
	var val = $.trim($(obj).val());
	if (val == "") {
		$(obj).focus();
		return false;
	}
	return true;
}

/**
 * 获取当前时间随机数
 * @returns
 */
function getDate(){
	var date = new Date();
	var time = date.getSeconds().toString()+date.getMilliseconds().toString();
	return time;
}

/**
 * 循环价格
 */
function eachPrice() {
	var $price = 0;
	$.each($('input[name="sku_price"]'), function(i, item) {
		var $this = $(item);
		var num = $this.val();
		var numint = parseFloat(num);
		var priceint = parseFloat($price);
		if ($price == 0 || numint < priceint) $price = num;
	});
	$("#txtProductSalePrice").val($price);
}
/**
 * 循环市场价 2016年12月2日 11:55:30
 */
function eachMarketPrice() {
	var $price = 0;
	$.each($('input[name="market_price"]'), function(i, item) {
		var $this = $(item);
		var num = $this.val();
		var numint = parseFloat(num);
		var priceint = parseFloat($price);
		if ($price == 0 || numint < priceint) $price = num;
	});
	$("#txtProductMarketPrice").val($price);
}
/**
 * 循环成本价 2016年12月2日 12:14:27
 */
function eachCostPrice() {
	var $price = 0;
	$.each($('input[name="cost_price"]'), function(i, item) {
		var $this = $(item);
		var num = $this.val();
		var numint = parseFloat(num);
		var priceint = parseFloat($price);
		if ($price == 0 || numint < priceint) $price = num;
	});
	$("#txtProductCostPrice").val($price);
}

/**
 * 循环商家编码，取第一个
 * 创建时间：2017年9月29日 11:44:05
 */
function eachMerchantCode(){
//	if($('input[name="code"]:last').val() != undefined && $('input[name="code"]:last').val() != ""){
//		$("#txtProductCodeA").val($('input[name="code"]:last').val());
//	}
}

/**
 * 循环库存
 */
function eachInput() {
	var $stockTotal = 0;
	$.each($('input[name="stock_num"]'), function(i, item) {
		var $this = $(item);
		var num = 0;
		num = parseInt($this.val());
		$stockTotal = $stockTotal + num;
	});
	$("#txtProductCount").val($stockTotal);
}


//选择商品类目后回到函数
function addGoodsCallBack(goods_category_id ,goods_category_name ,goods_attr_id , goodsid, dialog_flag, box_id){
	switch(dialog_flag){
	case "category":

		$("#tbcNameCategory .category-text").html(goods_category_name);
		$("#tbcNameCategory").attr("cid",goods_category_id);
		$("#tbcNameCategory").attr("data-attr-id",goods_attr_id);
		$("#tbcNameCategory").attr("cname",goods_category_name);
		if(goodsid == 0){
			$("#goodsType").val(goods_attr_id);
			goodsTypeChangeData();
			getGoodsSpecListByAttrId($("#goodsType").val());
			removeSpecPictureBox();
			if(parseInt($("#goodsType").val()) == 0){
//				//如果没有选择商品类型，则清空属性信息
				$(".js-goods-attribute-block").hide();
				$(".js-goods-sku-attribute").html("");
			}
		}
		break;
	case "extend_category":
		$("#"+box_id+" .category-text").html($.trim(goods_category_name));
		$("#"+box_id).attr("cid",goods_category_id);
		$("#"+box_id).attr("data-attr-id",goods_attr_id);
		$("#"+box_id).attr("cname",goods_category_name);
		break;
	}
}
/**
 * 添加扩展分类
 */
function addExtentCategoryBox(){
	var html = '<div class="extend-name-category" id="extend_name_category'+extent_sort+'" data-flag="extend_category" data-goods-id="0" cid="" data-attr-id="" cname="">';
	html += '<span class="category-text"onclick="editCategory(this);"></span>';
	html += '&nbsp;&nbsp;<span class="do-style" onclick="editCategory(this);"><i class="fa fa-edit"></i>&nbsp;编辑</span>&nbsp;&nbsp;';
	html += '<span class="do-style" onclick="removeParentBox(this);"><i class="fa fa-trash-o"></i>&nbsp;删除</span>';
	html += '<span class="help-inline" style="vertical-align: top;">已添加的商品扩展分类不能为空</span>';
	$(".extend-name-category-box").append(html);
	extent_sort++;
}
/**
 * 编辑扩展分类
 */
function editCategory(obj){
	var goodsid = $(obj).parent().attr("data-goods-id");
	var category_id = $(obj).parent().attr("cid");
	var flag = $(obj).parent().attr("data-flag");
	var box_id = $(obj).parent().attr("id");
	var category_extend_id = "";
	$(".extend-name-category").each(function() {
		if(category_extend_id == ""){
			category_extend_id = $(this).attr("cid");
		}else{
			category_extend_id += "," + $(this).attr("cid");
		}
	});
	OpenCategoryDialog(ADMINMAIN,category_id,goodsid,flag, box_id, category_extend_id);
}
/**
 * 删除本条扩展分类
 * @param obj
 */
function removeParentBox(obj){
	$(obj).parent().remove();
}

//导航分组
function changeGoodsGroup(obj){
	if($(obj).val() > 0){
		var exist_num = 0;
		$(".goods-group-div .goods-group-line .goods-gruop-select").each(function() {
			if($(this).val() == 0) exist_num++;
		})
		if(exist_num < 1){
			if($.trim(group_str) != "" && $.trim(group_str) != undefined){
				var html = "<div class='goods-group-line'><select class='goods-gruop-select' onchange='changeGoodsGroup(this);'>";
				html +="<option value='0'></option>"
				var group_array = eval(group_str);
				
				for(var i = 0; i < group_array.length ; i++ ){
					html +="<option value='"+ group_array[i]["group_id"] +"'>"+ group_array[i]["group_name"] +"</option>"
				}
				html +="</select></div>";
				$(".goods-group-div").append(html);
			}else{
				$(".span-error").show();
			}
		}
	}else{
		if($(".goods-group-div .goods-group-line .goods-gruop-select").length > 1) $(obj).parent().remove();
	}
}

//数组去重
function undulpicate(array){
	for(var i=0;i<array.length;i++) {
		for(var j=i+1;j<array.length;j++) {
		//注意 ===
			if(array[i]===array[j]) {
				array.splice(j,1);
				j--;
			}
		}
	}
	return array;
}

/**
 * 根据商品类型id，查询商品规格信息
 * 2017年6月6日 11:38:47 王永杰
 * @param attr_id 规格属性id
 */ 
function getGoodsAttributeListByAttrId(attr_id, callBack){
	if(!isNaN(attr_id) && attr_id > 0){
		$.ajax({
			url : __URL(ADMINMAIN+"/goods/getGoodsSpecListByAttrId"),
			type : "post",
			data : { "attr_id" : parseInt(attr_id)},
			success : function(res){
				if(res !=-1){
					var attribute_length = res.attribute_list.length;
				
					//商品属性集合
					if(attribute_length>0){
						var html ="";
						for(var i=0;i<attribute_length;i++){
							var curr = res.attribute_list[i];
							if($.trim(curr.value_items) == "" && parseInt(curr.type) !=1) continue;
							if($.trim(curr.attr_value_name) != ""){
							
							html += '<tr style="padding-top:15px;padding-bottom:15px;">';
								html += '<td width="10%" style="border:1px solid #E9E9E9;"align="right" class="txt12" data-value="'+curr.attr_value_name+'">'+curr.attr_value_name+'</td>';
								html += '<td width="80%" style="border:1px solid #E9E9E9;">';
									switch(parseInt(curr.type)){
										case 1:
											//输入框
											html += '<input type="text" class="js-attribute-text" id="input-text-'+curr.attr_value_id+'-'+curr.attr_value_id+'"data-attribute-value-id="'+curr.attr_value_id+'" data-attribute-value="'+curr.attr_value_name+'" />';
											break;
										case 2:
											//单选框
											for(var j=0;j<curr.value_items.length;j++){
												var value = curr.value_items[j];
												if($.trim(value) != ""){
													html += '<div class="goods-sku-attribute-item-radio">';
														html += '<input type="radio" value="'+value+'" class="js-attribute-radio" id="radio_value_item'+curr.attr_value_id+'-'+j+'" data-attribute-value-id="'+curr.attr_value_id+'" data-attribute-value="'+curr.attr_value_name+'"  name="radio_value'+i+'" />&nbsp;';
														html += '<label for="radio_value_item'+curr.attr_value_id+'-'+j+'">'+value+'</label>';
													html += '</div>';
												}
											}
											break;
										case 3:
											//复选框
											for(var j=0;j<curr.value_items.length;j++){
												var value = curr.value_items[j];
												if($.trim(value) != ""){
													html += '<div class="goods-sku-attribute-item-checkbox">';
														html += '<input type="checkbox" value="'+value+'" class="js-attribute-checkbox" id="checkbox_value_item'+curr.attr_value_id+'-'+j+'" data-attribute-value-id="'+curr.attr_value_id+'" data-attribute-value="'+curr.attr_value_name+'"  name="checkbox_value_item'+i+'" />&nbsp;';
														html += '<label for="checkbox_value_item'+curr.attr_value_id+'-'+j+'">'+value+'</label>';
													html += '</div>';
												}
											}
											break;
									}
								html += '</td>';
							html += '</tr>';
							}
							
						}
						$(".js-goods-sku-attribute").html(html);
						if(callBack != undefined) callBack();
					}
					
					$(".js-goods-attribute-block").show();

				}
			}
		});
	}
}