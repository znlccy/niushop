/**
 * 2017年2月22日 12:13:44 wyj
 */
$(function() {
	$(".js-filter-type").on(
			"click",
			function() {
				switch (parseInt($(this).attr("data-type"))) {
				case 0:
					$('.goodsList').hide();
					$('.list-grid').show();
					$(this).find("span").css("background-position",
							"-48px -30px");
					$(this).prev().find("span").css("background-position",
							"-70px -30px");
					break;
				case 1:
					$('.list-grid').hide();
					$('.goodsList').show();
					$(this).find("span").css("background-position",
							"-92px -30px");
					$(this).next().find("span").css("background-position",
							"-26px -30px");
					break;
				}
			});

	$(".nch-sortbar-location .select-layer,.options").mouseover(function() {
		$(".select-layer .selected").show();
		$("ul[class='options']").show();
	}).mouseout(function() {
		$(".select-layer .selected").hide();
		$("ul[class='options']").hide();
	});

})