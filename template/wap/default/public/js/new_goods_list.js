$(function() {
	//判断是否存在筛选条件
	if($(".screening_condition .conditions li").length == 0){
		$(".data-screen").hide();
		$(".filtrate-term li.drop_down").css("width","45%");
	}

	var data_pid = $(".two_stage_classification li.active").attr("data-pid");
	$(".two_stage_classification li[data-pid='"+data_pid+"']").show();
	$(".primary_classification li[data-category-id='"+data_pid+"']").addClass("active");

	// 排序
	$(".filtrate-more span.filtrate-sort").click(function() {
		$(".filtrate-more span.filtrate-sort a").removeClass("current");
		$(this).children("a").addClass("current");
		var order_type = $(this).find("a").attr("data-order-type");
		var data_sort = $(this).find("a").attr("data-sort");
		var $this = $(this);
		//$(".filtrate-term ul li.filtrate-sort i").css("color","#aaa");
		if(order_type != undefined && sort != undefined){
			if(data_sort == "desc"){
				sort = "asc";
				$(this).find("a").attr("data-sort","asc");
				// if(order_type == "ng.promotion_price" || order_type == "ng.sales"){
				// 	$this.find(".fa-angle-up").css("color","red");
				// 	$this.find(".fa-angle-down").css("color","#aaa");
				// }
			}else if(data_sort == "asc"){
				sort = "desc";
				$(this).find("a").attr("data-sort","desc");
				// if(order_type == "ng.promotion_price" || order_type == "ng.sales"){
				// 	$this.find(".fa-angle-down").css("color","red");
				// 	$this.find(".fa-angle-up").css("color","#aaa");
				// }
			}
			$("#order").val(order_type);
			$("#sort").val(sort);
		}else{
			$("#order").val("");
			$("#sort").val("");
		}
		$(".mask-div").click();
		getgoodlist(1);
	});

	//点击筛选弹出筛选框
	$(".data-screen").click(function(){
		$(".screening_condition").animate({left:"15%"},{speed : "800"});
		$(".shade-div").css("display","block");
		$(".mask-div").click();
		$('body').height("100%").css("overflow", "hidden");
	})
	//点击取消隐藏筛选框
	$(".screening_condition .bottom-area .cancle,.shade-div").click(function(){
		$(".screening_condition").animate({left:"100%"},{speed : "1000"});
		$(".shade-div").css("display","none");
		$("body").css("overflow","inherit");
	})

	$("ul.conditions li div.click-down").click(function(){
		var is_open = $(this).attr("is_open");
		if(is_open == 0){
			$(this).parent("li").css("height","auto");
		 	$(this).attr("is_open",1);
		}else{
			$(this).parent("li").css("height","75px");
		 	$(this).attr("is_open",0);
		}	
	})

	$("ul.conditions li div.condition_value a").click(function(){
		$(this).addClass("selected").siblings().removeClass("selected");
	})
	$(".reset").click(function(){
		$("ul.conditions li div.condition_value a").removeClass("selected");
		$("ul.conditions li div.condition_value a.all").addClass("selected");
	})

	//点击确定按钮进行筛选
	$(".screening_condition .bottom-area .confirm_screen").click(function(){
		var attr_array = new Array();
		var spec_array = new Array();
		$(".screening_condition .conditions li div.condition_value").each(function(i,e){
			var screen_type = $(e).attr("data-screen-type");
			var $this = $(e).children("a.selected");
			//筛选品牌
			if(screen_type == "brand"){
				if($this.attr("data-brand-id") != "" && $this.attr("data-brand-id") != undefined){
					$("#brand_id").val($this.attr("data-brand-id"));
				}else{
					$("#brand_id").val("");
				}
			}
			//筛选属性
			if(screen_type == "attr"){
				if($this.attr("data-attr-value") != "" && $this.attr("data-attr-value") != undefined){
					attr_array[i] = $this.attr("data-attr-value");
				}else{
					attr_array[i] = "";
				}
			}
			//筛选规格
			if(screen_type == "spec"){
				if($this.attr("data-spec-value") != "" && $this.attr("data-spec-value") != undefined){
					spec_array[i] = $this.attr("data-spec-value");
				}else{
					spec_array[i] = "";
				}
			}
			if(screen_type == "price"){
				if($this.attr("data-min-price") != "" && $this.attr("data-min-price") != undefined){
					$("#min_price").val($this.attr("data-min-price"));
					$("#max_price").val($this.attr("data-max-price"));
				}else{
					$("#min_price").val("");
					$("#max_price").val("");
				}
			}
		})
		//数组去空
		new_attr_array = $.grep(attr_array, function(n) {
			return $.trim(n).length > 0;
		})
		attr = new_attr_array.join(";");
		$("#attr").val(attr);
		//数组去空
		new_spec_array = $.grep(spec_array, function(n) {
			return $.trim(n).length > 0;
		})
		spec = new_spec_array.join(";");
		$("#spec").val(spec);
		getgoodlist(1);	
		//隐藏筛选弹出框
		$(".screening_condition").animate({left:"100%"},{speed : "1000"});
		$(".shade-div").css("display","none");
		$("body").css("overflow","inherit");
	})


	//弹出分类选择框
	var category_is_show = 0;
	$(".data-category").click(function(){
		if($(".primary_classification li").length == 0){
			return false;
		}
		if(category_is_show == 0){
			if(sort_is_show == 1){
				$(".filtrate-more").hide();
				sort_is_show = 0;
			}
			$(".data-category-select-layer").show();
			$(".mask-div").show();
			$('body').height("100%").css("overflow", "hidden");
			category_is_show = 1;
		}else{
			$(".data-category-select-layer").hide();
			$(".mask-div").hide();
			$('body').css("overflow", "inherit");
			category_is_show = 0;
		}
	})

	var sort_is_show = 0;
	//弹出排序框
	$(".data-ordrt-sort").click(function(){
		if(sort_is_show == 0){
			if(category_is_show == 1){
				$(".data-category-select-layer").hide();
				category_is_show = 0;
			}
			$(".filtrate-more").show();
			$(".mask-div").show();
			$('body').height("100%").css("overflow", "hidden");
			sort_is_show = 1;
		}else{
			$(".filtrate-more").hide();
			$(".mask-div").hide();
			$('body').css("overflow", "inherit");
			sort_is_show = 0;
		}
	})

	$(".mask-div").click(function(){
		$(this).hide();
		$('body').css("overflow", "inherit");
		$(".data-category-select-layer").hide();
		$(".filtrate-more").hide();
		category_is_show = 0;
		sort_is_show = 0;
	})

	//点击一级分类
	$(".primary_classification li[data-category-id]").click(function(){
		$(".primary_classification li").removeClass("active");
		$(this).addClass("active");
		var category_id = $(this).attr('data-category-id');
		if($(".two_stage_classification li[data-pid='"+category_id+"']").length > 0){
			$(".two_stage_classification li").hide();
			$(".two_stage_classification li[data-pid='"+category_id+"']").show();
		}else{
			location.href=__URL(APPMAIN+"/goods/goodsList?category_id="+category_id);
		}
	})
	//切换展示方式
	$(".data-display-mode").click(function(){
		var current_display_type = $(this).find("i").attr("class");
		if(current_display_type == "fa fa-th-large fa-lg"){
			$(this).find("i").attr("class","fa fa-list-ul fa-lg");
			$.cookie('goods_list_display_mode', "list", { expires: 7, path: '/' });		
			getgoodlist(1);	
		}else if(current_display_type == "fa fa-list-ul fa-lg"){
			$(this).find("i").attr("class","fa fa-th-large fa-lg");
			$.cookie('goods_list_display_mode', "big_img", { expires: 7, path: '/' });
			getgoodlist(1);	
		}
	})
});