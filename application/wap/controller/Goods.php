<?php
/**
 * Goods.php
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace app\wap\controller;

use data\service\Config as WebConfig;
use data\service\Goods as GoodsService;
use data\service\GoodsBrand as GoodsBrand;
use data\service\GoodsCategory;
use data\service\GoodsGroup;
use data\service\Member;
use data\service\Order as OrderService;
use data\service\Platform;
use data\service\promotion\GoodsExpress;
use data\service\Address;
use data\service\WebSite;

/**
 * 商品相关
 *
 * @author Administrator
 *        
 */
class Goods extends BaseController
{

    /**
     * 商品详情
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function goodsDetail()
    {
        $goods_id = request()->get('id', 0);
        if ($goods_id == 0) {
            $this->error("没有获取到商品信息");
        }
        
        $this->web_site = new WebSite();
        $goods = new GoodsService();
        $config_service = new WebConfig();
        $member = new Member();
        $shop_id = $this->instance_id;
        $uid = $this->uid;
        
        $web_info = $this->web_site->getWebSiteInfo();
        
        // 切换到PC端
        if (! request()->isMobile() && $web_info['web_status'] != 2 && $web_info['web_status'] != 3) {
            $redirect = __URL(__URL__ . "/goods/goodsinfo?goodsid=" . $goods_id);
            $this->redirect($redirect);
            exit();
        }
        
        $goods_detail = $goods->getGoodsDetail($goods_id);
        if (empty($goods_detail)) {
            $this->error("没有获取到商品信息");
        }
        if ($this->getIsOpenVirtualGoodsConfig() == 0 && $goods_detail['goods_type'] == 0) {
            $this->error("未开启虚拟商品功能");
        }
        
        // 把属性值相同的合并
        $goods_attribute_list = $goods_detail['goods_attribute_list'];
        $goods_attribute_list_new = array();
        foreach ($goods_attribute_list as $item) {
            $attr_value_name = '';
            foreach ($goods_attribute_list as $key => $item_v) {
                if ($item_v['attr_value_id'] == $item['attr_value_id']) {
                    $attr_value_name .= $item_v['attr_value_name'] . ',';
                    unset($goods_attribute_list[$key]);
                }
            }
            if (! empty($attr_value_name)) {
                array_push($goods_attribute_list_new, array(
                    'attr_value_id' => $item['attr_value_id'],
                    'attr_value' => $item['attr_value'],
                    'attr_value_name' => rtrim($attr_value_name, ',')
                ));
            }
        }
        $goods_detail['goods_attribute_list'] = $goods_attribute_list_new;
        
        // 获取当前时间
        $current_time = $this->getCurrentTime();
        $this->assign('ms_time', $current_time);
        $this->assign("goods_detail", $goods_detail);
        $this->assign("shopname", $this->shop_name);
        $this->assign("price", intval($goods_detail["promotion_price"]));
        $this->assign("goods_id", $goods_id);
        $this->assign("title_before", $goods_detail['goods_name']);
        
        // 返回商品数量和当前商品的限购
        $this->getCartInfo($goods_id);
        
        // 分享
        $ticket = $this->getShareTicket();
        $this->assign("signPackage", $ticket);
        
        // 评价数量
        $evaluates_count = $goods->getGoodsEvaluateCount($goods_id);
        $this->assign('evaluates_count', $evaluates_count);
        
        // 美洽客服
        $list = $config_service->getcustomserviceConfig($shop_id);
        if (empty($list)) {
            $list['id'] = '';
            $list['value']['service_addr'] = '';
        }
        $this->assign("list", $list);
        
        // 查询点赞记录表，获取详情再判断当天该店铺下该商品该会员是否已点赞
        $click_detail = $goods->getGoodsSpotFabulous($shop_id, $uid, $goods_id);
        $this->assign('click_detail', $click_detail);
        
        // 当前用户是否收藏了该商品
        if (isset($uid)) {
            $is_member_fav_goods = $member->getIsMemberFavorites($uid, $goods_id, 'goods');
        }
        $this->assign("is_member_fav_goods", $is_member_fav_goods);
        
        // 获取商品的优惠劵
        $goods_coupon_list = $goods->getGoodsCoupon($goods_id, $this->uid);
        $this->assign("goods_coupon_list", $goods_coupon_list);
        
        return view($this->style . 'Goods/goodsDetail');
    }

    /**
     * 根据定位查询当前商品的运费
     * 创建时间：2017年9月29日 15:12:55 王永杰
     */
    public function getShippingFeeNameByLocation()
    {
        $goods_id = request()->post("goods_id", "");
        $express = "";
        if (! empty($goods_id)) {
            
            $user_location = get_city_by_ip();
            if ($user_location['status'] == 1) {
                // 定位成功，查询当前城市的运费
                $goods_express = new GoodsExpress();
                $address = new Address();
                $province = $address->getProvinceId($user_location["province"]);
                $city = $address->getCityId($user_location["city"]);
                $district = $address->getCityFirstDistrict($city['city_id']);
                $express = $goods_express->getGoodsExpressTemplate($goods_id, $province['province_id'], $city['city_id'], $district);
            }
        }
        return $express;
    }

    /**
     * 得到当前时间戳的毫秒数
     *
     * @return number
     */
    public function getCurrentTime()
    {
        $time = time();
        $time = $time * 1000;
        return $time;
    }

    /**
     * 功能：商品评论
     * 创建人：李志伟
     * 创建时间：2017年2月23日11:12:57
     */
    public function getGoodsComments()
    {
        $comments_type = request()->post('comments_type', '');
        $order = new OrderService();
        $condition['goods_id'] = request()->post('goods_id', '');
        switch ($comments_type) {
            case 1:
                $condition['explain_type'] = 1;
                break;
            case 2:
                $condition['explain_type'] = 2;
                break;
            case 3:
                $condition['explain_type'] = 3;
                break;
            case 4:
                $condition['image|again_image'] = array(
                    'NEQ',
                    ''
                );
                break;
        }
        $condition['is_show'] = 1;
        $goodsEvaluationList = $order->getOrderEvaluateDataList(1, PAGESIZE, $condition, 'addtime desc');
        // 查询评价用户的头像
        $memberService = new Member();
        foreach ($goodsEvaluationList['data'] as $v) {
            $v["user_img"] = $memberService->getMemberImage($v["uid"]);
        }
        return $goodsEvaluationList;
    }

    /**
     * 返回商品数量和当前商品的限购
     *
     * @param unknown $goods_id            
     */
    public function getCartInfo($goods_id)
    {
        $goods = new GoodsService();
        $cartlist = $goods->getCart($this->uid);
        $num = 0;
        foreach ($cartlist as $v) {
            if ($v["goods_id"] == $goods_id) {
                $num = $v["num"];
            }
        }
        $this->assign("carcount", count($cartlist)); // 购物车商品数量
        $this->assign("num", $num); // 购物车已购买商品数量
    }

    /**
     * 购物车页面
     */
    public function cart()
    {
        $this->is_member = $this->user->getSessionUserIsMember();
        if ($this->is_member == 0) {
            $redirect = __URL(__URL__ . "/wap/login");
            $this->redirect($redirect);
        }
        $this->assign("shopname", $this->shop_name);
        $goods = new GoodsService();
        
        $cartlist = $goods->getCart($this->uid, $this->instance_id);
        // 店铺，店铺中的商品
        $list = Array();
        for ($i = 0; $i < count($cartlist); $i ++) {
            // $cartlist[$i]["goods_name"] = mb_substr($cartlist[$i]["goods_name"], 0,20,"utf-8");
            // $cartlist[$i]["sku_name"] = mb_substr($cartlist[$i]["goods_name"], 0,20,"utf-8");
            $list[$cartlist[$i]["shop_id"] . ',' . $cartlist[$i]["shop_name"]][] = $cartlist[$i];
        }
        $this->assign("list", $list);
        $this->assign("countlist", count($cartlist));
        $this->assign("title_before", "购物车");
        return view($this->style . 'Goods/cart');
    }

    /**
     * 添加购物车
     * 创建人：李广
     */
    public function addCart()
    {
        $cart_detail = request()->post('cart_detail', '');
        if (! empty($cart_detail)) {
            $cart_detail = json_decode($cart_detail, true);
        }
        $cart_tag = request()->post('cart_tag', '');
        $uid = $this->uid;
        $shop_id = $cart_detail["shop_id"];
        $shop_name = $cart_detail["shop_name"];
        $goods_id = $cart_detail['trueId'];
        $goods_name = $cart_detail['goods_name'];
        $num = $cart_detail['count'];
        $sku_id = $cart_detail['select_skuid'];
        $sku_name = $cart_detail['select_skuName'];
        $price = $cart_detail['price'];
        $cost_price = $cart_detail['cost_price'];
        $picture = $cart_detail['picture'];
        $this->is_member = $this->user->getSessionUserIsMember();
        if (! empty($this->uid) && $this->is_member == 1) {
            $goods = new GoodsService();
            $retval = $goods->addCart($uid, $shop_id, $shop_name, $goods_id, $goods_name, $sku_id, $sku_name, $price, $num, $picture, 0);
        } else {
            $retval = array(
                "code" => - 1,
                "message" => ""
            );
        }
        return $retval;
    }

    /**
     * 购物车修改数量
     */
    public function cartAdjustNum()
    {
        if (request()->isAjax()) {
            $cart_id = request()->post('cartid', '');
            $num = request()->post('num', '');
            $goods = new GoodsService();
            $retval = $goods->cartAdjustNum($cart_id, $num);
            return AjaxReturn($retval);
        } else
            return AjaxReturn(- 1);
    }

    /**
     * 购物车项目删除
     */
    public function cartDelete()
    {
        if (request()->isAjax()) {
            $cart_id_array = request()->post('del_id', '');
            $goods = new GoodsService();
            $retval = $goods->cartDelete($cart_id_array);
            return AjaxReturn($retval);
        } else
            return AjaxReturn(- 1);
    }

    /**
     * 平台商品分类列表
     */
    public function goodsClassificationList()
    {
        $uid = $this->uid;
        $goods_category = new GoodsCategory();
        $goods_category_list = $goods_category->getFormatGoodsCategoryList();
        $this->assign("goods_category_list", $goods_category_list);
        // 计算补足数量
        foreach ($goods_category_list as $k => $v) {
            $num = 0;
            if (count($v["child_list"]) < 3) {
                $num = 3 - count($v["child_list"]);
            }
            if (count($v["child_list"]) > 3) {
                $max_row = (count($v["child_list"]) + 1) / 4;
                $max_row = ceil($max_row);
                $num = $max_row * 4 - (count($v["child_list"]) + 1);
            }
            $goods_category_list[$k]['num'] = $num;
        }
        $this->assign("title_before", "商品分类");
        return view($this->style . 'Goods/goodsClassificationList');
    }

    /**
     * 店铺商品分组列表
     */
    public function goodsGroupList()
    {
        // 查询购物车中商品的数量
        $uid = $this->uid;
        $goods = new GoodsService();
        $cartlist = $goods->getCart($uid);
        $this->assign('uid', $uid);
        $this->assign("carcount", count($cartlist));
        
        $components = new Components();
        $grouplist = $components->goodsGroupList($this->shop_id);
        $group_frist_list = null;
        $group_second_list = null;
        foreach ($grouplist as $group) {
            if ($group["pid"] == 0) {
                $group_frist_list[] = $group;
            } else {
                $group_second_list[] = $group;
            }
        }
        $this->assign("group_frist_list", $group_frist_list);
        $this->assign("group_second_list", $group_second_list);
        
        $group_goods = new GoodsGroup();
        $tree_list = $group_goods->getGroupGoodsTree($this->shop_id);
        $this->assign("tree_list", $tree_list);
        return view($this->style . 'Goods/goodsGroupList');
    }

    /**
     * 商品分类列表
     */
    public function goodsCategoryList()
    {
        $goodscate = new GoodsCategory();
        $one_list = $goodscate->getGoodsCategoryListByParentId(0);
        if (! empty($one_list)) {
            foreach ($one_list as $k => $v) {
                $two_list = array();
                $two_list = $goodscate->getGoodsCategoryListByParentId($v['category_id']);
                $v['child_list'] = $two_list;
                if (! empty($two_list)) {
                    foreach ($two_list as $k1 => $v1) {
                        $three_list = array();
                        $three_list = $goodscate->getGoodsCategoryListByParentId($v1['category_id']);
                        $v1['child_list'] = $three_list;
                    }
                }
            }
        }
        return $one_list;
    }

    /**
     * 加入购物车前显示商品规格
     */
    public function joinCartInfo()
    {
        $goods = new GoodsService();
        $goods_id = request()->post('goods_id', '');
        $goods_detail = $goods->getGoodsDetail($goods_id);
        $this->assign("goods_detail", $goods_detail);
        $this->assign("shopname", $this->shop_name);
        // $this->assign("style", $this->style);
        return view($this->style . 'joinCart');
    }

    /**
     * 搜索商品显示
     */
    public function goodsSearchList()
    {
        if (request()->isAjax()) {
            $sear_name = request()->post('sear_name', '');
            $sear_type = request()->post('sear_type', '');
            $order = request()->post('order', '');
            $sort = request()->post('sort', 'desc');
            $controlType = request()->post('controlType', '');
            $shop_id = request()->post('shop_id', '');
            $page = request()->post("page", 1);
            $goods = new GoodsService();
            $condition['goods_name'] = [
                'like',
                '%' . $sear_name . '%'
            ];
            // 排序类型
            $orderby = ""; // 排序方式 默认按排序号升序，创建时间倒序排列
            if ($order != "") {
                $orderby = $order . " " . $sort;
            } else {
                $orderby = "ng.sort asc,ng.create_time desc";
            }
            switch ($controlType) {
                case 1:
                    $condition = [
                        'is_new' => 1
                    ];
                    break;
                case 2:
                    $condition = [
                        'is_hot' => 1
                    ];
                    break;
                case 3:
                    $condition = [
                        'is_recommend' => 1
                    ];
                    break;
                default:
                    break;
            }
            if (! empty($shop_id)) {
                $condition['ng.shop_id'] = $shop_id;
            }
            $condition['state'] = 1;
            $search_good_list = $goods->getGoodsList($page, PAGESIZE, $condition, $orderby);
            return $search_good_list;
        } else {
            $sear_name = request()->get('sear_name', '');
            $controlType = request()->get('controlType', ''); // 什么类型 1最新 2精品 3推荐
            $controlTypeName = request()->get('controlTypeName', ''); // 什么类型 1最新 2精品 3推荐
            
            if (! empty($sear_name)) {
                $search_title = $sear_name;
            } else {
                $search_title = $controlTypeName;
            }
            if (mb_strlen($sear_name) > 10) {
                $sear_name = mb_substr($sear_name, 0, 7, 'utf-8') . '...';
            }
            $shop_id = $this->shop_id;
            $this->assign('controlType', $controlType);
            $this->assign('wherename', 'sear_name');
            $this->assign('sear_name', $sear_name);
            $this->assign('shop_id', $shop_id);
            $this->assign('search_title', $search_title);
            return view($this->style . 'Goods/goodsSearchList');
        }
    }

    /**
     * 品牌专区
     */
    public function brandlist()
    {
        $platform = new Platform();
        $goods = new GoodsService();
        // 品牌专区广告位
        $brand_adv = $platform->getPlatformAdvPositionDetail(1162);
        $this->assign('brand_adv', $brand_adv);
        
        if (request()->isAjax()) {
            $brand_id = request()->get("brand_id", "");
            $page_index = request()->get("page", 1);
            if (! empty($brand_id)) {
                $condition['ng.brand_id'] = $brand_id;
            }
            $condition['ng.state'] = 1;
            $list = $goods->getGoodsList($page_index, PAGESIZE, $condition, "ng.sort asc,ng.create_time desc");
            return $list;
        } else {
            $goods_category = new GoodsCategory();
            $goods_category_list_1 = $goods_category->getGoodsCategoryList(1, 0, [
                "is_visible" => 1,
                "level" => 1
            ]);
            $goods_brand = new GoodsBrand();
            $goods_brand_list = $goods_brand->getGoodsBrandList(1, 0, '', 'brand_initial asc');
            // print_r(json_encode($goods_brand_list));
            // return;
            // var_dump($goods_brand_list);
            $this->assign("goods_brand_list", $goods_brand_list['data']);
            $this->assign("goods_category_list_1", $goods_category_list_1["data"]);
            $this->assign("title_before", "品牌专区");
            return view($this->style . 'Goods/brandlist');
        }
    }

    /**
     * 商品列表
     */
    public function goodsList()
    {
        // 查询购物车中商品的数量
        $uid = $this->uid;
        $goods = new GoodsService();
        $cartlist = $goods->getCart($uid);
        $this->assign('uid', $uid);
        $this->assign("carcount", count($cartlist));
        
        if (request()->isAjax()) {
            $category_id = request()->post('category_id', ''); // 商品分类
            $brand_id = request()->post('brand_id', ''); // 品牌
            $order = request()->post('order', ''); // 商品排序分类
            $sort = request()->post('sort', 'desc'); // 商品排序分类
            $page = request()->post('page', 1);
            $min_price = request()->post('min_price', ''); // 价格区间,最小
            $max_price = request()->post('max_price', ''); // 最大
            $attr = request()->post('attr', ''); // 属性值
            $spec = request()->post('spec', ''); // 规格值
                                                 // 将属性条件字符串转化为数组
            $attr_array = $this->stringChangeArray($attr);
            // 规格转化为数组
            if ($spec != "") {
                $spec_array = explode(";", $spec);
            } else {
                $spec_array = array();
            }
            $orderby = ""; // 排序方式
            if ($order != "") {
                $orderby = $order . " " . $sort;
            } else {
                $orderby = "ng.sort asc,ng.create_time desc";
            }
            
            $goods = new GoodsService();
            $goods_list = $this->getGoodsListByConditions($category_id, $brand_id, $min_price, $max_price, $page, PAGESIZE, $orderby, $attr_array, $spec_array);
            return $goods_list;
        } else {
            $category_id = request()->get('category_id', ''); // 商品分类
            $brand_id = request()->get('brand_id', ''); // 品牌
            $this->assign('brand_id', $brand_id);
            $this->assign('category_id', $category_id);
            // 筛选条件
            if ($category_id != "") {
                // 获取商品分类下的品牌列表、价格区间
                $category_brands = null;
                $category_price_grades = [];
                
                // 查询品牌列表，用于筛选
                $goods_category_service = new GoodsCategory();
                $category_brands = $goods_category_service->getGoodsCategoryBrands($category_id);
                
                // 查询价格区间，用于筛选
                $category_price_grades = $goods_category_service->getGoodsCategoryPriceGrades($category_id);
                foreach ($category_price_grades as $k => $v) {
                    $category_price_grades[$k]['price_str'] = $v[0] . '-' . $v[1];
                }
                $category_count = 0; // 默认没有数据
                if ($category_brands != "") {
                    $category_count = 1; // 有数据
                }
                $goodsService = new GoodsService();
                $goods_category_info = $goods_category_service->getGoodsCategoryDetail($category_id);
                
                $attr_id = $goods_category_info["attr_id"];
                // 查询商品分类下的属性和规格集合
                $goods_attribute = $goodsService->getAttributeInfo([
                    "attr_id" => $attr_id
                ]);
                $attribute_detail = $goodsService->getAttributeServiceDetail($attr_id, [
                    'is_search' => 1
                ]);
                $attribute_list = array();
                if (! empty($attribute_detail['value_list']['data'])) {
                    $attribute_list = $attribute_detail['value_list']['data'];
                    foreach ($attribute_list as $k => $v) {
                        $value_items = explode(",", $v['value']);
                        $new_value_items = array();
                        foreach ($value_items as $ka => $va) {
                            $new_value_items[$ka]['value'] = $va;
                            $new_value_items[$ka]['value_str'] = $attribute_list[$k]['attr_value_name'] . ',' . $va . ',' . $attribute_list[$k]['attr_value_id'];
                        }
                        $attribute_list[$k]['value'] = trim($v["value"]);
                        $attribute_list[$k]['value_items'] = $new_value_items;
                    }
                }
                $attr_list = $attribute_list;
                // 查询本商品类型下的关联规格
                $goods_spec_array = array();
                if ($goods_attribute["spec_id_array"] != "") {
                    $goods_spec_array = $goodsService->getGoodsSpecQuery([
                        "spec_id" => [
                            "in",
                            $goods_attribute["spec_id_array"]
                        ]
                    ]);
                    foreach ($goods_spec_array as $k => $v) {
                        foreach ($v["values"] as $z => $c) {
                            $c["value_str"] = $c['spec_id'] . ':' . $c['spec_value_id'];
                        }
                    }
                    sort($goods_spec_array);
                }
                $this->assign("attr_or_spec", $attr_list);
                $this->assign("category_brands", $category_brands);
                $this->assign("category_count", $category_count);
                $this->assign("category_price_grades", $category_price_grades);
                $this->assign("category_price_grades_count", count($category_price_grades));
                $this->assign("goods_spec_array", $goods_spec_array); // 分类下的规格
                $this->assign("title_before", $goods_category_info['category_name']);
            }
            // 获取分类列表
            $goodsCategory = new GoodsCategory();
            $goodsCategoryList = $goodsCategory->getFormatGoodsCategoryList();
            $this->assign("goodsCategoryList", $goodsCategoryList);
            return view($this->style . 'Goods/goodsList');
        }
    }

    /**
     * 将属性字符串转化为数组
     *
     * @param unknown $string            
     * @return multitype:multitype: |multitype:
     */
    private function stringChangeArray($string)
    {
        if (trim($string) != "") {
            $temp_array = explode(";", $string);
            $attr_array = array();
            foreach ($temp_array as $k => $v) {
                $v_array = array();
                if (strpos($v, ",") === false) {
                    $attr_array = array();
                    break;
                } else {
                    $v_array = explode(",", $v);
                    if (count($v_array) != 3) {
                        $attr_array = array();
                        break;
                    } else {
                        $attr_array[] = $v_array;
                    }
                }
            }
            return $attr_array;
        } else {
            return array();
        }
    }

    /**
     * 根据条件查询商品列表：商品分类查询，关键词查询，价格区间查询，品牌查询
     * 创建人：王永杰
     * 创建时间：2017年2月24日 16:55:05
     */
    public function getGoodsListByConditions($category_id, $brand_id, $min_price, $max_price, $page, $page_size, $order, $attr_array, $spec_array)
    {
        $goods = new GoodsService();
        $condition = null;
        if ($category_id != "") {
            // 商品分类Id
            $condition["ng.category_id"] = $category_id;
        }
        // 品牌Id
        if ($brand_id != "") {
            $condition["ng.brand_id"] = array(
                "in",
                $brand_id
            );
        }
        
        // 价格区间
        if ($max_price != "") {
            $condition["ng.promotion_price"] = [
                [
                    ">=",
                    $min_price
                ],
                [
                    "<=",
                    $max_price
                ]
            ];
        }
        
        // 属性 (条件拼装)
        $array_count = count($attr_array);
        $goodsid_str = "";
        $attr_str_where = "";
        if (! empty($attr_array)) {
            // 循环拼装sql属性条件
            foreach ($attr_array as $k => $v) {
                if ($attr_str_where == "") {
                    $attr_str_where = "(attr_value_id = '$v[2]' and attr_value_name='$v[1]')";
                } else {
                    $attr_str_where = $attr_str_where . " or " . "(attr_value_id = '$v[2]' and attr_value_name='$v[1]')";
                }
            }
            if ($attr_str_where != "") {
                $attr_query = $goods->getGoodsAttributeQuery($attr_str_where);
                
                $attr_array = array();
                foreach ($attr_query as $t => $b) {
                    $attr_array[$b["goods_id"]][] = $b;
                }
                $goodsid_str = "0";
                foreach ($attr_array as $z => $x) {
                    if (count($x) == $array_count) {
                        if ($goodsid_str == "") {
                            $goodsid_str = $z;
                        } else {
                            $goodsid_str = $goodsid_str . "," . $z;
                        }
                    }
                }
            }
        }
        
        // 规格条件拼装
        $spec_count = count($spec_array);
        $spec_where = "";
        if ($spec_count > 0) {
            foreach ($spec_array as $k => $v) {
                if ($spec_where == "") {
                    $spec_where = " attr_value_items_format like '%{$v}%' ";
                } else {
                    $spec_where = $spec_where . " or " . " attr_value_items_format like '%{$v}%' ";
                }
            }
            
            if ($spec_where != "") {
                
                $goods_query = $goods->getGoodsSkuQuery($spec_where);
                $temp_array = array();
                foreach ($goods_query as $k => $v) {
                    $temp_array[] = $v["goods_id"];
                }
                $goods_query = array_unique($temp_array);
                if (! empty($goods_query)) {
                    if ($goodsid_str != "") {
                        $attr_con_array = explode(",", $goodsid_str);
                        $goods_query = array_intersect($attr_con_array, $goods_query);
                        $goods_query = array_unique($goods_query);
                        $goodsid_str = "0," . implode(",", $goods_query);
                    } else {
                        $goodsid_str = "0,";
                        $goodsid_str .= implode(",", $goods_query);
                    }
                } else {
                    $goodsid_str = "0";
                }
            }
        }
        if ($goodsid_str != "") {
            $condition["goods_id"] = [
                "in",
                $goodsid_str
            ];
        }
        
        $condition['ng.state'] = 1;
        
        $list = $goods->getGoodsList($page, $page_size, $condition, $order);
        
        return $list;
    }

    /**
     * 积分中心
     *
     * @return \think\response\View
     */
    public function integralCenter()
    {
        $platform = new Platform();
        // 积分中心广告位
        $discount_adv = $platform->getPlatformAdvPositionDetail(1165);
        $this->assign('discount_adv', $discount_adv);
        // 积分中心商品
        $this->goods = new GoodsService();
        $order = "";
        // 排序
        $id = request()->get('id', '');
        if ($id) {
            if ($id == 1) {
                $order = "sales desc";
            } else 
                if ($id == 2) {
                    $order = "collects desc";
                } else 
                    if ($id == 3) {
                        $order = "evaluates desc";
                    } else 
                        if ($id == 4) {
                            $order = "shares desc";
                        } else {
                            $id = 0;
                            $order = "";
                        }
        } else {
            $id = 0;
        }
        
        $page_index = request()->get('page', 1);
        $condition = array(
            "ng.state" => 1,
            "ng.point_exchange_type" => array(
                'NEQ',
                0
            )
        );
        $page_count = 25;
        $hotGoods = $this->goods->getGoodsList(1, 4, $condition, $order);
        $allGoods = $this->goods->getGoodsList($page_index, $page_count, $condition, $order);
        if ($page_index) {
            if (($page_index > 1 && $page_index <= $allGoods["page_count"])) {
                $page_index = 1;
            }
        }
        $this->assign("id", $id);
        $this->assign('page', $page_index);
        $this->assign("allGoods", $allGoods);
        $this->assign("hotGoods", $hotGoods);
        $this->assign('page_count', $allGoods['page_count']);
        $this->assign('total_count', $allGoods['total_count']);
        return view($this->style . 'Goods/integralCenter');
    }

    /**
     * 积分中心 全部积分商品
     *
     * @return \think\response\View
     */
    public function integralCenterList()
    {
        return view($this->style . 'Goods/integralCenterList');
    }

    /**
     * 积分中心全部商品Ajax
     */
    public function integralCenterListAjax()
    {
        $platform = new Platform();
        if (request()->isAjax()) {
            // 积分中心商品
            $this->goods = new GoodsService();
            $order = "";
            // 排序
            $id = request()->post('id', '');
            if ($id) {
                if ($id == 1) {
                    $order = "sales desc";
                } else 
                    if ($id == 2) {
                        $order = "collects desc";
                    } else 
                        if ($id == 3) {
                            $order = "evaluates desc";
                        } else 
                            if ($id == 4) {
                                $order = "shares desc";
                            } else {
                                $id = 0;
                                $order = "";
                            }
            } else {
                $id = 0;
            }
            
            $page_index = request()->post('page', '1');
            $condition = array(
                "ng.state" => 1,
                "ng.point_exchange_type" => array(
                    'NEQ',
                    0
                )
            );
            $page_count = 25;
            $allGoods = $this->goods->getGoodsList($page_index, $page_count, $condition, $order);
            return $allGoods['data'];
        }
    }

    /**
     * 设置点赞送积分
     */
    public function getClickPoint()
    {
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $uid = $this->uid;
            $goods_id = request()->post('goods_id', '');
            $goods = new GoodsService();
            $click_detail = $goods->getGoodsSpotFabulous($shop_id, $uid, $goods_id);
            if (empty($click_detail)) {
                $retval = $goods->setGoodsSpotFabulous($shop_id, $uid, $goods_id);
                return AjaxReturn($retval);
            } else {
                return $retval = array(
                    "code" => - 1,
                    "message" => "您今天已经赞过该商品了"
                );
            }
        }
    }

    /**
     * 获取商品分类下的商品
     */
    public function getCategoryChildGoods()
    {
        if (request()->isAjax()) {
            $page = request()->post("page", 1);
            $category_id = request()->post("category_id", 0);
            $goods = new GoodsService();
            if ($category_id == 0) {
                $condition['ng.state'] = 1;
                $res = $goods->getGoodsList($page, PAGESIZE, $condition, "ng.sort asc,ng.create_time desc");
            } else {
                $condition['ng.category_id'] = $category_id;
                $condition['ng.state'] = 1;
                $res = $goods->getGoodsList($page, PAGESIZE, $condition, "ng.sort asc,ng.create_time desc");
            }
            return $res;
        }
    }

    /**
     * 查询商品的sku信息
     */
    public function getGoodsSkuInfo()
    {
        $goods_id = request()->post('goods_id', '');
        $this->goods = new GoodsService();
        return $this->goods->getGoodsAttribute($goods_id);
    }

    /**
     * 领取商品优惠劵
     */
    public function receiveGoodsCoupon()
    {
        if (request()->isAjax()) {
            $member = new Member();
            $coupon_type_id = request()->post("coupon_type_id", '');
            $res = $member->memberGetCoupon($this->uid, $coupon_type_id, 3);
            return AjaxReturn($res);
        }
    }
}