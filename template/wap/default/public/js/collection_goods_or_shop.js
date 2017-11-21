/**
 * 商品、店铺收藏 2017年3月10日 14:17:30
 */
function collectionGoodsOrShop(obj, fav_id, fav_type, log_msg) {
	is_member_fav_shop = parseInt(is_member_fav_shop);
	if (!isNaN(is_member_fav_shop)) {
		if (is_member_fav_shop == 0) {
			$.ajax({
				url : __URL(APPMAIN + "/components/collectiongoodsorshop"),
				type : "post",
				data : {
					"fav_id" : fav_id,
					"fav_type" : fav_type,
					"log_msg" : log_msg
				},
				success : function(res) {
					showBox(res.message);
					if (res.code > 0) {
						is_member_fav_shop = 1;
						$(obj).text("已收藏");
					}
				}
			})
		} else {
			$.ajax({
				url : __URL(APPMAIN + "/components/cancelcollgoodsorshop"),
				type : "post",
				data : {
					"fav_id" : fav_id,
					"fav_type" : fav_type
				},
				success : function(res) {
					showBox(res.message);
					if (res.code > 0) {
						is_member_fav_shop = 0;
						$(obj).text("收藏店铺");
					}
				}
			});
		}
	}
}
/**
 * 关注店铺
 * @param shop_id
 */
function userAssociateShop(shop_id,even){
	$.ajax({
		url : "userassociateshop",
		type : "post",
		data : {"shop_id" : shop_id},
		success : function(res) {
			if (res.code > 0) {
				layer.msg('申请店铺会员成功');
//				layer.msg('关注店铺成功');
				$(even).css('color','red');
				$(even).html('<i class="fa fa-heart"></i>会员中心');
			}else{
				location.href= __URL(APPMAIN + "/login/index")
				//layer.msg('申请店铺会员失败');
//				layer.msg('关注店铺失败');
			}
		}
	});
}