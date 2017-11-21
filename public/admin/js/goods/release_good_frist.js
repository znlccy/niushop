/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : 小学生wyj
 * @date : 2016年12月16日 16:17:13
 * @version : v1.0.0.0
 * 商品发布中的第一步，选择商品分类
 */
var goods_category_quick = new Array();// 快速选择商品分类集合
$(function() {
	
	$("#next_Page").addClass("disabled");
	$("#next_Page").attr("disabled", true);
	$select_ids=$("#category_select_ids").val();
	$select_names=$("#category_select_names").val();
	if($select_ids!=""){
		quick($select_ids, $select_names);
	}
//	var tempCids = $("#categoryHistoryList").find("option:selected").attr('cid');
//	if (tempCids != undefined) {
//		var tempArr = tempCids.split(',');
//		for (var i = 0; i < tempArr.length; i++) {
//			/**
//			 * 生成div框 以及选中数据
//			 */
//			ClickHasSubCategory($("#" + tempArr[i]));
//		}
//	}

	// 查询当前用户最近一天是否发布过商品
	$.ajax({
		url :  __URL(ADMINMAIN+"/goods/getquickgoods"),
		type : "post",
		success : function(res) {
			var str = "<ul>";
			if (eval(res) != -1 && eval(res).length > 0) {
				goods_category_quick = eval(res);// 将Cookie中的数据取出来
				for (var i = 0; i < goods_category_quick.length; i++) {
					var quick_id = goods_category_quick[i]["quick_id"];
					var quick_name = goods_category_quick[i]["quick_name"];
					str += "<li onclick='quick(&#39;" + quick_id + "&#39;,&#39;" + quick_name + "&#39;)'>";
					str += "<span>" + quick_name.replace(/:/g, "&nbsp;&gt;") + "</span>";
					str += "</li>";
				}
			} else {
				$("#commListArea").prev().text("请选择");
				str += '<li><span class="title">您还没有添加过常用的分类</span></li>';
			}
			str += "</ul>";
			$("#commListArea").html(str);
		}
	});
	$('#commSelect').hover(function() {
		$('#commListArea').show();
	}, function() {
		$('#commListArea').hide();
	});
})
// 快速选择商品类目
function quick(quick_id, quick_name) {
	quick_name = quick_name.replace(/:/g, "&nbsp;&gt;");// 处理格式
	$('#commListArea').hide().prev().html(quick_name);
	var $selectedCategory = "<span class='hasSelectedCategoryDivText'>您当前选择的是：</span>";
	if (quick_id != -1) {
		$(".hasSelectedCategoryDiv").html($selectedCategory);
		var arr = quick_id.split(",");
		$("#selectCategoryDiv2").remove();// 删除子类目
		$("#selectCategoryDiv3").remove();// 删除子类目
		for (var i = 0; i < arr.length; i++) {
			loadingQuick(arr[i], (i + 1));
		}
	}
}

function loadingQuick(categoryID, index) {
	var event = $(".categoryItem[id=" + categoryID + "]");
	var $selectedCategory = "";
	event.siblings().removeClass("categoryItemClick");
	event.addClass("categoryItemClick");
	$.ajax({
		url : __URL(ADMINMAIN+"/goods/getchildcategory"),
		type : "post",
		async : false,
		data : {
			"categoryID" : categoryID
		},
		success : function(res) {
			if (res != null && res.length != 0) {
				var $categoryDiv = CreateSelectCatgoryDiv(230, 300, (parseInt(index) + 1));
				$("#categoryDivContainer").append($categoryDiv);
				var $data = GetProductCategoryData(res);
				$categoryDiv.append($data);
				$("#next_Page").addClass("disabled");
				$("#next_Page").attr("disabled", true);
			} else {
				$("#next_Page").removeClass("disabled");
				$("#next_Page").attr("disabled", false);
			}
			$selectedCategory += '<span id="' + (index) + '" cid="'
					+ categoryID + '" data-attr-id="'+event.attr("data-attr-id")+'" >';
			$selectedCategory += event.text() + '</span>';
			$(".hasSelectedCategoryDiv").append($selectedCategory);
		}
	});
}
//
//function nextPage() {
//	var goods_id=$("#update_goods_id").val();
//	var dis = $("#next_Page").attr("disabled");
//	if (dis == "disabled") {
//		return;
//	}
//	var goods_category_name = "";
//	var selectSpan = $(".hasSelectedCategoryDiv span").last();
//	var spanList = $(".hasSelectedCategoryDiv span");
//	var count = spanList.length;
//	var quick_id = "";// 所选择的商品分类
//	for (var i = 1; i < count; i++) {
//		var span = $(spanList[i]);
//		var html = span.html();
//		goods_category_name += html;
//		quick_id += span.attr("cid") + ",";// 记录用户所选择的商品类目Id，用与在快速选择商品类目中显示
//	}
//	var goods_category_id = selectSpan.attr("cid");
//	var goods_attr_id = selectSpan.attr("data-attr-id");//属性关联id
//	quick_id = quick_id.substr(0, quick_id.length - 1);
//	// 判断当前所选择的商品分类与Cookie中的进行查询，是否存在，不存在则添加，
//	var flag = true;// 标识，是否允许添加到Cookie中（防止出现重复数据）true:允许；flase：不允许
//	if (goods_category_quick.length > 0) {
//		for (var k = 0; k < goods_category_quick.length; k++) {
//			if (quick_id == goods_category_quick[k]["quick_id"]) {
//				flag = false;
//				break;
//			} else {
//				flag = true;
//			}
//		}
//	}
//	goods_category_name = goods_category_name.replace(/\s/g, "");
//	goods_category_name = goods_category_name.replace(/&gt;/g, ":");
//	// 允许添加到到Cookie中
//	if (flag) {
//		var json = {
//			quick_name : goods_category_name.trim(),
//			quick_id : quick_id,
//		};
//		goods_category_quick.push(json);
//		// alert("Cookie中没有，开始添加");
//	} else {
//		// alert("Cookie中已有，不进行重复添加操作");
//	}
//	$.ajax({
//		url : "SelectCateGetData",
//		type : "post",
//		asysc : false,
//		data : {
//			"goods_category_id" : goods_category_id,
//			"goods_category_name" : goods_category_name,
//			"goodsId" : goods_id,
//			"goods_category_quick" : JSON.stringify(goods_category_quick),
//			"goods_attr_id" : goods_attr_id
//		},
//		success : function(res) {
//			if(goods_id!=0){
//				window.location.href = "addGoods?step=2&goodsId="+goods_id;
//			}else{
//				window.location.href = "addGoods?step=2";
//			}
//		}
//	});
//}

$('input[name="search_category"]').live('keyup', function() {
	var $this = $(this).parent().next();
	var text = $(this).val();
	var $divs = $this.children();
	if (text == "") {
		// 全部显示出来
		for (var i = 0; i < $divs.length; i++) {
			$($divs[i]).css("display", "block");
		}
	} else {
		// 检索显示
		for (var i = 0; i < $divs.length; i++) {
			var $span = $($divs[i]).find("span:first");
			var $val = $span.text();
			if ($val.indexOf(text) == -1) {
				$($divs[i]).css("display", "none");
			} else {
				$($divs[i]).css("display", "block");
			}
		}
	}
});

// 摆放数据
function GetProductCategoryData(categoryData) {
	// 定义一个容器，来装生成的根类目数据
	var categorySet = "<div id='categorySet' class='categorySet'>";
	// 暂且认为parentID为0的数据项是根数据项
	for (var i = 0; i < categoryData.length; i++) {
		
		var cate = categoryData[i];
		var leaf = 0;
		var name = cate["category_name"];
		var cateId = cate["category_id"];
		var attrId = cate["attr_id"];//关联商品类型ID
		var $rootCategory = "";
		var is_parent = cate['is_parent'];
		if (leaf == 0) {
			var nbsp = '';
			if(is_parent > 0){
				nbsp = '&nbsp;&gt;&nbsp;';
			}
			$rootCategory = "<div id='"
					+ cateId
					+ "' data-attr-id='"+attrId+"' class='categoryItem' onclick='ClickHasSubCategory(this)'><span style='width: 170px;'>"
					+ name + "</span><span>"+nbsp+"</span></div>";
		} else {
			$rootCategory = "<div id='"
					+ cateId
					+ "' data-attr-id='"+attrId+"' class='categoryItem' onclick='ClickHasSubCategory(this)'><span style='width: 170px;'>"
					+ name + "</span></div>";
		}
		categorySet = categorySet + $rootCategory;
	}
	categorySet = categorySet + "</div>";
	return categorySet;
}

// 点击节点 走的js函数
var $currentSelectCategoryDivIdNum;
function ClickHasSubCategory(event) {
	var $currentCategoryID = $(event).attr("id");
	$currentSelectCategoryDivIdNum = RemoveDiv($(event));
	$("#quick_select_shoptype option[value=-1]").attr("selected", true);// 还原快速选择
	// 根据触发获取子类目事件的id请求parentID为该id的类目数据
	$.ajax({
		url : __URL(ADMINMAIN+"/goods/getchildcategory"),
		type : "post",
		async : false,
		data : {
			"categoryID" : $currentCategoryID
		},
		success : function(res) {
			if (res != null && res.length != 0) {
				var $categoryDiv = CreateSelectCatgoryDiv(230, 300, (parseInt($currentSelectCategoryDivIdNum) + 1));
				$("#categoryDivContainer").append($categoryDiv);
				var $data = GetProductCategoryData(res);
				$categoryDiv.append($data);
				// 点击某一个父类目之后，将其categoryName记录在类样式名为hasSelectedCategoryDiv的div中
				var $selectedCategory = "<span id="
						+ $currentSelectCategoryDivIdNum + " cid="
						+ $(event).attr("id") + " data-attr-id='"+$(event).attr("data-attr-id")+"' > " + $(event).text()
						+ "</span>";
				$(".hasSelectedCategoryDiv").append($selectedCategory);
				// 点击有子类目的项之后将“已选好类目，进入下一步”按钮隐藏
				$("#next_Page").addClass("disabled");
				$("#next_Page").attr("disabled", true);
			} else {
				var $selectedCategory = "<span id="
						+ $currentSelectCategoryDivIdNum + " cid="
						+ $(event).attr("id") + " data-attr-id='"+$(event).attr("data-attr-id")+"'> " + $(event).text()
						+ "</span>";
				$(".hasSelectedCategoryDiv").append($selectedCategory);
				var category_extend_id = $("#category_extend_id").val();
				if(category_extend_id.indexOf($currentCategoryID) == -1){
					$("#next_Page").removeClass("disabled");
					$("#next_Page").attr("disabled", false);	
				}
			}

		}
	});
}

function RemoveDiv($eventSrc) {
	// 给事件源所在的div加上高亮效果，并移除其它兄弟项的高亮效果
	$eventSrc.siblings().removeClass("categoryItemClick");
	$eventSrc.addClass("categoryItemClick");
	// 找到要删除的对象
	var $currentSelectCategoryDiv = $eventSrc.parent().parent();
	var $currentSelectCategoryDivId = $currentSelectCategoryDiv.attr("id");
	var $currentSelectCategoryDivIdNum = $currentSelectCategoryDivId
			.substring($currentSelectCategoryDivId.lastIndexOf('v') + 1); // 截取出数字num
	var $allSelectCategoryDiv = $("div[id^=selectCategoryDiv]");
	for (var i = 0; i < $allSelectCategoryDiv.length; i++) {
		var $thisSelectCategoryDivId = $allSelectCategoryDiv[i].id;
		var $thisSelectCategoryDivIdNum = $thisSelectCategoryDivId
				.substring($thisSelectCategoryDivId.lastIndexOf('v') + 1);
		if ($thisSelectCategoryDivIdNum > $currentSelectCategoryDivIdNum) {
			$("#selectCategoryDiv" + $thisSelectCategoryDivIdNum + "").remove();
		}
	}
	// 找到已经记录的类目，将其后面的，包括自己删除
	var $allSelectedCategory = $(".hasSelectedCategoryDiv span");
	if ($allSelectedCategory.length > 0) {
		for (var i = 0; i < $allSelectedCategory.length; i++) {

			if ($allSelectedCategory[i].id >= $currentSelectCategoryDivIdNum) {
				$(".hasSelectedCategoryDiv span[id=" + $allSelectedCategory[i].id + "]").remove();
			}
		}
	}
	return $currentSelectCategoryDivIdNum; // 返回当前div的ID的num，用于定位当前div，方便删除后面的div
}

// 添加商品分类的下一级（div）
function CreateSelectCatgoryDiv(w, h, num) {
	var $selectCategoryDiv = $("<div id='selectCategoryDiv" + num + "' class='selectCategoryDiv'></div>");
	$selectCategoryDiv.css("width", w + "px");
	$selectCategoryDiv.css("height", h + "px");
	$selectCategoryDiv.append('<div class="category-search"><i class="icon-search-tabao" style="background: url('+ADMINIMG+'/SelectCategory_Search.png) no-repeat -4px -2px;"></i><input type="text" name="search_category" placeholder="输入名称"></div>')
	return $selectCategoryDiv;
}