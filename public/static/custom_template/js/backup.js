/**
 * 打开商品列表(弹出框形式)
 * 创建时间：2017年8月10日 15:36:57
 * 更新时间：2017年8月16日 18:37:17
 * 1、暂时不使用
 */
function showGoodsList(){
	
	if(empty($("#showGoodsList").html())){
		var html = '<div id="showGoodsList" class="modal hide fade" tabindex="-1" data-backdrop="static" role="dialog" aria-hidden="true">';
				html += '<div class="modal-header">';
					html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
					html += '<h3>已上架商品<span style="padding:0 5px;color:#333333">|</span><a href="' + ADMINMAIN +'/goods/addgoods" target="_blank" style="font-size:14px;">新建商品</a></h3>';
				html += '</div>';
				html += '<div class="modal-body">';
				
					html += '<table class="goods-list-table">';
						html += '<thead>';
							html += '<tr>';
								html += '<th align="left" style="text-indent:10px;">';
										html += '<span>标题</span>&nbsp;<a class="js-update" href="javascript:void(0);">刷新</a>';
								html += '</th>';
								html += '<th align="right">';
								
									html += '<div class="input-append">';
										html += '<input type="text" class="input-common js-search-goods-name-input">';
										html += '<span class="add-on js-search-goods-name">搜</span>';
									html += '</div>';
								html += '</th>';
							html += '</tr>';
						html += '</thead>';
					html += '</table>';
					
					html += '<table class="goods-list-table">';
						html += '<colgroup>';
							html += '<col width="12%">';
							html += '<col width="58%">';
							html += '<col width="20%">';
							html += '<col width="15%">';
						html += '</colgroup>';
						html += '<tbody class="goods-list-tbody">';
							html += getGoodsList(1);
						html += '</tbody>';
					html += '</table>';
				html += '</div>';
				
				html += '<div class="modal-footer">';
					html += '<div class="selected">';
						html += '<button class="btn-common js-sure-use">确定使用</button>';
						html += '<span class="js-use-count" data-use-count="0">已选取0个</span>';
					html += '</div>';
					html += '<div class="paging-info">' + $(".show-goods-list").attr("data-footer-html") + '</div>';
				html += '</div>';
			html += '</div>';
		$("body").append(html);
	}else{
		$("#showGoodsList .goods-list-tbody").html(getGoodsList(parseInt($(".curr-page").attr("data-page-index"))));
		//重新计算选取商品数量
		if(parseInt($(".js-use-count").attr("data-use-count")) == 0) $("#showGoodsList .modal-footer .selected").hide();
//		$("#showGoodsList .modal-footer .paging-info").html($(".show-goods-list").attr("data-footer-html"));
	}
	setObjectVerticalCenter($("#showGoodsList"));
	$("#showGoodsList").modal("show");
}


/**
 * 获取商品列表
 * 创建时间：2017年8月10日 16:35:39
 */
function getGoodsList(page_index,goods_name){
	var html = "";
	var footer_html = "";
	$.ajax({
		type : "post",
		url : __URL(ADMINMAIN + '/goods/goodslist'),
		async : false,
		data : {
			"page_index" : page_index,
			"page_size" : 5,
			"state":1, //上架状态
			"goods_name":goods_name
		},
		success : function(data){

			footer_html += '<span>共&nbsp;' + data.total_count + '&nbsp;条，每页显示&nbsp;' + data.page_count + '&nbsp;条</span>';
			if(data['data'].length>0){

				for (var i = 0; i < data["data"].length; i++) {
					html += '<tr>';
						html += '<td class="image" align="center">';
							html += '<img src="' + UPLOAD +  "/" + data["data"][i]["pic_cover_micro"] + '">';
						html += '</td>';
						
						html += '<td class="title">';
							html += '<a target="_blank" href="' + __URL(SHOPMAIN + '/goods/goodsinfo?goodsid='+data["data"][i]["goods_id"]) + '">' + data["data"][i]["goods_name"] + '</a>';
						html += '</td>';
						
						html += '<td class="time" align="center">';
							html += '<span>' + timeStampTurnTime(data["data"][i]["create_time"]) + '</span>';
						html == '</td>';
						
						var goods_id_arr = $(".js-sure-use").attr("data-goods-array");
						html += '<td align="center">';
							if(!empty(goods_id_arr)){
								goods_id_arr = eval(goods_id_arr);
								var temp_count = 0;
								for(var j=0;j<goods_id_arr.length;j++){
									if(eval("(" + goods_id_arr[j] + ")").goods_id == data['data'][i]["goods_id"]){
										html += '<button class="btn js-select-goods selected" href="javascript:void(0);" data-goods-id="' + data['data'][i]['goods_id'] + '" data-goods-name="' + data['data'][i]["goods_name"] + '">取消</button>';
										temp_count++;
										break;
									}
								}
								//如果没有找到已选取的商品，则添加默认按钮
								if(!temp_count) html += '<button class="btn js-select-goods" href="javascript:void(0);" data-goods-id="' + data['data'][i]['goods_id'] + '" data-goods-name="' + data['data'][i]["goods_name"] + '">选取</button>';
							}else{
								html += '<button class="btn js-select-goods" href="javascript:void(0);" data-goods-id="' + data['data'][i]['goods_id'] + '" data-goods-name="' + data['data'][i]["goods_name"] + '">选取</button>';
							}
						html += '</td>';
					html += '</tr>';
				}
				footer_html += '<a href="javascript:;" class="js-prev-goods-list" data-flag="prev">上一页</a>';
				footer_html += '<span class="curr-page" data-total-count="' + data.total_count + '" data-page-count="' + data.page_count + '" data-page-index="' + page_index + '">' + page_index + '</span>';
				footer_html += '<a href="javascript:;" class="js-next-goods-list" data-flag="next">下一页</a>';
			}else{
				html += '<tr>';
					html += '<td colspan="3" style="text-align: center;color: #999;">暂无符合条件的数据记录</td>';
				html += '</tr>';
			}

			$(".show-goods-list").attr("data-footer-html",footer_html);
		}
	});
	return html;
}


/**
 * 商品列表(弹出框)上一页、下一页
 * 创建时间：2017年8月10日 17:05:21
 */
$(".js-prev-goods-list,.js-next-goods-list").live("click",function(){
	var flag = $(this).attr("data-flag");
	var page_index = parseInt($(".curr-page").attr("data-page-index"));
	var total_count = parseInt($(".curr-page").attr("data-total-count"));
	var page_count = parseInt($(".curr-page").attr("data-page-count"));
	var max_page = Math.ceil(total_count/page_count);
	if(flag == "prev") page_index = parseInt(page_index) - 1;
	else if(flag == "next") page_index = parseInt(page_index) + 1;
	if(page_index == 0) page_index = 1;
	if(page_index >=max_page) page_index = max_page;
	$(".goods-list-tbody").html(getGoodsList(page_index,$(".js-search-goods-name-input").val()));
	$("#showGoodsList .modal-footer .paging-info").html($(".show-goods-list").attr("data-footer-html"));
});

/**
 * 商品列表(弹出框）->跟商品名称进行搜索
 * 创建时间：2017年8月10日 17:28:30
 */
$(".js-search-goods-name").live("click",function(){
	$(".goods-list-tbody").html(getGoodsList(1,$(".js-search-goods-name-input").val()));
	$("#showGoodsList .modal-footer .paging-info").html($(".show-goods-list").attr("data-footer-html"));
});

/**
 * 商品列表(弹出框）->刷新
 * 创建时间：2017年8月10日 17:28:17
 */
$(".js-update").live('click',function(){
	$(".goods-list-tbody").html(getGoodsList(1));
	$("#showGoodsList .modal-footer .paging-info").html($(".show-goods-list").attr("data-footer-html"));
});

/**
 * 商品列表(弹出框)->选择商品
 * 创建时间：2017年8月10日 17:29:57
 */
$(".js-select-goods").live("click",function(){
	
	var count = $(".goods-list-tbody .js-select-goods").length;
	var goods_arr = new Array();//选取的商品信息(goods_id，goods_name)
	var curr_goods_id = 0;//当前点击的商品id，取消
	
	//取出其他页选取的商品
	if(!empty($(".js-sure-use").attr("data-goods-array"))){
		var array = eval($(".js-sure-use").attr("data-goods-array"));
		for(var i=0;i<array.length;i++) goods_arr.push(array[i]);
	}
	
	if($(this).hasClass("selected")){
		$(this).removeClass("selected").text("选取");
		curr_goods_id = $(this).attr("data-goods-id");
	}else{
		$(this).addClass('selected').text("取消");
		$("#showGoodsList .modal-footer .selected").fadeIn();
	}
	//计算没有选取的数量，排除取消状态的商品
	$(".goods-list-tbody .js-select-goods").each(function(i){
		if($(this).hasClass("selected") && curr_goods_id == 0){
			goods_arr.push(JSON.stringify({goods_id : $(this).attr("data-goods-id"),goods_name : $(this).attr("data-goods-name")}));
		}
	});
	//去重
	for(var i=0;i<goods_arr.length;i++){
		if(eval("("+goods_arr[i]+")").goods_id == curr_goods_id) goods_arr.splice(i,1);
		for(var j=(i+1);j<goods_arr.length;j++){
			if(eval("("+goods_arr[i]+")").goods_id == eval("("+goods_arr[j]+")").goods_id) goods_arr.splice(j,1);
		}
	}
	//一个也没有选取隐藏
	if(goods_arr.length == 0) $("#showGoodsList .modal-footer .selected").fadeOut();
	else $(".js-use-count").text("已选取" + goods_arr.length + "个");
	$(".js-use-count").attr("data-use-count",goods_arr.length);
	$(".js-sure-use").attr("data-goods-array",JSON.stringify(goods_arr));
	
});

/**
 * 商品列表(弹出框)->确定使用
 * 创建时间：2017年8月10日 17:49:07
 */
$(".js-sure-use").live("click",function(){
	$("input[name='show-goods-list'][value='2']").attr("data-goods-array",$(this).attr("data-goods-array"));
	var goods_arr = eval($(this).attr("data-goods-array"));
	bindCouponsData();
	var	html = '<colgroup>';
			html += '<col width="80%">';
			html += '<col width="20%">';
		html += '</colgroup>';
		html += '<tr>';
			html += '<th>商品名称</th>';
			html += '<th>操作</th>';
		html += '</tr>';
		for(var i=0;i<goods_arr.length;i++){
			var goods = eval("(" + goods_arr[i] + ")");
			html += '<tr>';
				html += '<td title="' + goods.goods_name + '">' + goods.goods_name + '</td>';
				html += '<td><a href="javascript:;" data-goods-id="' + goods.goods_id + '" class="js-del-goods">删除</a>';
			html += '</tr>';
		}
	$(".show-goods-list table").html(html);
	$("#showGoodsList").modal("hide");
});

/**
 * [优惠券组件]->可使用的商品(删除商品）
 * 创建时间：2017年8月10日 20:54:30
 * 更新时间：2017年8月11日 14:29:04
 */
$(".js-del-goods").live("click",function(){
	var goods_id = $(this).attr("data-goods-id");
	var goods_array = $("input[name='show-goods-list'][value='2']").attr("data-goods-array");
	if(!empty(goods_array)){
		goods_array = eval(goods_array);
		for(var i=0;i<goods_array.length;i++){
			var goods = eval("(" + goods_array[i] + ")");
			if(goods.goods_id == goods_id){
				goods_array.splice(i,1);
				break;
			}
		};
		$("input[name='show-goods-list'][value='2']").attr("data-goods-array",JSON.stringify(goods_array));
		$(".js-sure-use").attr("data-goods-array",JSON.stringify(goods_array));
		$(".js-use-count").attr("data-use-count",goods_array.length);
		bindCouponsData();
		if(goods_array.length == 0) $(this).parent().parent().parent().parent().find("tr").remove();
		else $(this).parent().parent().remove();
	}
});