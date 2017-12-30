//数字跳转页面 2016年11月16日 16:07:24
var jumpNumber = 1;
// 样式处理
function changeClass(flag) {
	switch (flag) {
	case "begin":
		$("#beginPage").addClass("page-disable");
		$("#prevPage").addClass("page-disable");
		$("#lastPage").removeClass("page-disable");
		$("#nextPage").removeClass("page-disable");
		break;
	case "prev":
		$("#lastPage").removeClass("page-disable");
		$("#nextPage").removeClass("page-disable");
		$("#beginPage").addClass("page-disable");
		$("#prevPage").addClass("page-disable");
		break;
	case "next":
		$("#lastPage").addClass("page-disable");
		$("#nextPage").addClass("page-disable");
		$("#beginPage").removeClass("page-disable");
		$("#prevPage").removeClass("page-disable");
		break;
	case "all":
		$("#lastPage").addClass("page-disable");
		$("#nextPage").addClass("page-disable");
		$("#beginPage").addClass("page-disable");
		$("#prevPage").addClass("page-disable");
		break;
	default:
		$("#lastPage").removeClass("page-disable");
		$("#nextPage").removeClass("page-disable");
		$("#beginPage").removeClass("page-disable");
		$("#prevPage").removeClass("page-disable");
		break;
	}
}

/**
 * wyj 2017年6月1日 11:48:31
 * @param page_count 页数
 * @param page_size 每页显示的条数
 * @param total_data 共多少条数据
 */
function initPageData(page_count,page_size,total_count){
	$("#page_count").val(page_count);
	$("#page_size").val(page_size);
	$(".total-data").text("共"+total_count+"条数据");
	$(".total-data").attr("data-total-count",total_count);
	$(".page-count").text("共"+page_count+"页");
}


function pagenumShow(pageindex,pagecount,pageshow){
	var $html='';
	var pageindex = parseInt(pageindex);//当前页 
	var pagecount = parseInt(pagecount);//总页数
	if(pageindex>pagecount){
		pageindex = pagecount;
	}
	if((pagecount == 0 || pagecount == 1) || (pageindex == pagecount && pageindex ==1)){
		changeClass("all");//只有一页
	}else if(pageindex == 1){
		changeClass("prev");//第一页
	}else if(pageindex == pagecount){
		changeClass("next");//最后一页
	}else if(pageindex < pagecount){
		//如果当前页小于总页数
		changeClass();//最后一页
	}
	
	
	var pageshow = parseInt(pageshow);
	if(pagecount<=pageshow){
		var i = 0;
		for (i = 1; i <= pagecount; i++) {
			if (pageindex == i) {
				$html += "<a onclick='JumpForPage(this)' class='currentPage'>" + i + "</a>";
			} else {
				$html += "<a onclick='JumpForPage(this)' >" + i + "</a>";
			}
		}
	}else{
		if((pageshow%2) ==1){
			var pagehalf = (pageshow-1)/2;
			if (pageindex <= (pagehalf + 1)) {
				for (i = 1; i <= pageshow; i++) {
					if (pageindex == i) {
						$html += "<a onclick='JumpForPage(this)' class='currentPage'>" + i + "</a>";
					} else {
						$html += "<a onclick='JumpForPage(this)' >" + i + "</a>";
					}
				}
			} else {
				if ((pagecount - pageindex) < pagehalf) {
					var start = pagecount - pageshow+1;
					for (i = start; i <= pagecount; i++) {
						if (pageindex == i) {
							$html += "<a onclick='JumpForPage(this)' class='currentPage'>" + i + "</a>";
						} else {
							$html += "<a onclick='JumpForPage(this)' >" + i + "</a>";
						}
					}
				} else {
					var start = pageindex - pagehalf;
					var end = pageindex + pagehalf;
					for (i = start; i <= end; i++) {
						if (pageindex == i) {
							$html += "<a onclick='JumpForPage(this)' class='currentPage'>" + i + "</a>";
						} else {
							$html += "<a onclick='JumpForPage(this)' >" + i + "</a>";
						}
					}
				}
			}
		}else{
			var pagehalf = pageshow/2;
			if (pageindex <= pagehalf) {
				for (i = 1; i <= pageshow; i++) {
					if (pageindex == i) {
						$html += "<a onclick='JumpForPage(this)' class='currentPage'>" + i + "</a>";
					} else {
						$html += "<a onclick='JumpForPage(this)' >" + i + "</a>";
					}
				}
			} else {
				if ((pagecount - pageindex) < pagehalf) {
					var start = pagecount - pageshow+1;
					for (i = start; i <= pagecount; i++) {
						if (pageindex == i) {
							$html += "<a onclick='JumpForPage(this)' class='currentPage'>" + i + "</a>";
						} else {
							$html += "<a onclick='JumpForPage(this)' >" + i + "</a>";
						}
					}
				} else {
					var start = pageindex - pagehalf+1;
					var end = pageindex + pagehalf;
					for (i = start; i <= end; i++) {
						if (pageindex == i) {
							$html += "<a onclick='JumpForPage(this)' class='currentPage'>" + i + "</a>";
						} else {
							$html += "<a onclick='JumpForPage(this)' >" + i + "</a>";
						}
					}
				}
			}
		}
	}
	return $html;
}


 
/**
 * 返回当前是第几页，该方法适用于列表中的：删除、修改等操作
 * 2017年6月1日 18:44:43 wyj
 * @param currentObj 当前数据中的表格
 * @param operationId 要操作的id集合，用于判断是单个操作还是批量操作
 */
function getCurrentIndex(operationId,currentObj,conditions){
	if(conditions == undefined){
		conditions = "";
	}
	var currentPage = $("#pageNumber a[class='currentPage']").text();//当前页
	var currentDataLength = $(currentObj).children(conditions).length;//当前页共显示多少条数据
	var currentIndex = currentPage;//查询当前第几页

	//单个删除
	if(operationId.toString().indexOf(",") == -1){
		if(currentDataLength == 1 && parseInt(currentPage) == parseInt($("#page_count").val()) ){
			//当前页只有一个,并且是最后一页
			currentIndex--;
		}
	}else{
		var currentSelectLength = $(currentObj).children(conditions).find("input:checked").length;;//当前页选择的数据
		//批量删除
		//如果是最后一页，并且数据等于一个
		if(parseInt(currentPage) == parseInt($("#page_count").val()) && currentDataLength == currentSelectLength){
			currentIndex--;
		}
	}
	if(currentIndex == 0){
		currentIndex = 1;
	}
//	alert(currentIndex+","+currentPage+","+currentDataLength);
	return currentIndex;
}