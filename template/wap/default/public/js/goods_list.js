$(function() {
	//判断是否存在筛选条件
	if($(".screening_condition .conditions li").length == 0){
		$(".data-screen").hide();
		$(".filtrate-term li").css("width","25%");
	}
	// 排序
	$(".filtrate-term ul li.filtrate-sort").click(function() {
		$(this).addClass("cur").siblings().removeClass("cur");
		var order_type = $(this).find("a").attr("data-order-type");
		var data_sort = $(this).find("a").attr("data-sort");
		var $this = $(this);
		$(".filtrate-term ul li.filtrate-sort i").css("color","#aaa");
		if(order_type != undefined && sort != undefined){
			if(data_sort == "desc"){
				sort = "asc";
				$(this).find("a").attr("data-sort","asc");
				if(order_type == "ng.promotion_price" || order_type == "ng.sales"){
					$this.find(".fa-angle-up").css("color","red");
					$this.find(".fa-angle-down").css("color","#aaa");
				}
			}else if(data_sort == "asc"){
				sort = "desc";
				$(this).find("a").attr("data-sort","desc");
				if(order_type == "ng.promotion_price" || order_type == "ng.sales"){
					$this.find(".fa-angle-down").css("color","red");
					$this.find(".fa-angle-up").css("color","#aaa");
				}
			}
			$("#order").val(order_type);
			$("#sort").val(sort);
		}else{
			$("#order").val("");
			$("#sort").val("");
		}
		getgoodlist(1);
	});
});