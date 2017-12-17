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
namespace app\admin\controller;

use data\service\Address;
use data\service\Album;
use data\service\Express as Express;
use data\service\Goods as GoodsService;
use data\service\GoodsBrand as GoodsBrand;
use data\service\GoodsCategory as GoodsCategory;
use data\service\GoodsGroup as GoodsGroup;
use data\service\Supplier;
use Qiniu\json_decode;
use think\Config;
use data\service\VirtualGoods;

/**
 * 商品控制器
 */
class Goods extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据商品ID查询单个商品，然后进行编辑操作
     *
     * 2016年11月25日 09:42:40
     *
     * @return \data\model\niushop\NsGoodsModel
     */
    public function GoodsSelect()
    {
        $goods_detail = new GoodsService();
        $goods = $goods_detail->getGoodsDetail(request()->get('goodsId'));
        return $goods;
    }

    /**
     * 商品列表
     */
    public function goodsList()
    {
        $goodservice = new GoodsService();
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $start_date = request()->post('start_date') == '' ? 0 : getTimeTurnTimeStamp(request()->post('start_date'));
            $end_date = request()->post('end_date') == '' ? 0 : getTimeTurnTimeStamp(request()->post('end_date'));
            $goods_name = request()->post('goods_name', '');
            $goods_code = request()->post('code', '');
            $state = request()->post('state', '');
            $category_id_1 = request()->post('category_id_1', '');
            $category_id_2 = request()->post('category_id_2', '');
            $category_id_3 = request()->post('category_id_3', '');
            $selectGoodsLabelId = request()->post('selectGoodsLabelId', '');
            $supplier_id = request()->post('supplier_id', '');
            $stock_warning = request()->post("stock_warning", 0); // 库存预警
            
            if (! empty($selectGoodsLabelId)) {
                $selectGoodsLabelIdArray = explode(',', $selectGoodsLabelId);
                $selectGoodsLabelIdArray = array_filter($selectGoodsLabelIdArray);
                $str = "FIND_IN_SET(" . $selectGoodsLabelIdArray[0] . ",ng.group_id_array)";
                for ($i = 1; $i < count($selectGoodsLabelIdArray); $i ++) {
                    $str .= "AND FIND_IN_SET(" . $selectGoodsLabelIdArray[$i] . ",ng.group_id_array)";
                }
                $condition[""] = [
                    [
                        "EXP",
                        $str
                    ]
                ];
            }
            
            if ($start_date != 0 && $end_date != 0) {
                $condition["ng.create_time"] = [
                    [
                        ">",
                        $start_date
                    ],
                    [
                        "<",
                        $end_date
                    ]
                ];
            } elseif ($start_date != 0 && $end_date == 0) {
                $condition["ng.create_time"] = [
                    [
                        ">",
                        $start_date
                    ]
                ];
            } elseif ($start_date == 0 && $end_date != 0) {
                $condition["ng.create_time"] = [
                    [
                        "<",
                        $end_date
                    ]
                ];
            }
            
            if ($state != "") {
                $condition["ng.state"] = $state;
            }
            if (! empty($goods_name)) {
                $condition["ng.goods_name"] = array(
                    "like",
                    "%" . $goods_name . "%"
                );
            }
            if (! empty($goods_code)) {
                $condition["ng.code"] = array(
                    "like",
                    "%" . $goods_code . "%"
                );
            }
            if ($category_id_3 != "") {
                $condition["ng.category_id_3"] = $category_id_3;
            } elseif ($category_id_2 != "") {
                $condition["ng.category_id_2"] = $category_id_2;
            } elseif ($category_id_1 != "") {
                $condition["ng.category_id_1"] = $category_id_1;
            }
            
            if ($supplier_id != '') {
                $condition['ng.supplier_id'] = $supplier_id;
            }
            
            $condition["ng.shop_id"] = $this->instance_id;
            
            // 库存预警
            if ($stock_warning == 1) {
                $condition['ng.min_stock_alarm'] = array(
                    "neq",
                    0
                );
                $condition['ng.stock'] = array(
                    "exp",
                    "<= ng.min_stock_alarm"
                );
            }
            
            $result = $goodservice->getGoodsList($page_index, $page_size, $condition, [
                'ng.create_time' => 'desc'
            ]);
            // 'ng.sort' => 'desc',
            
            // 根据商品分组id，查询标签名称
            foreach ($result['data'] as $k => $v) {
                if (! empty($v['group_id_array'])) {
                    $goods_group_id = explode(',', $v['group_id_array']);
                    $goods_group_name = '';
                    foreach ($goods_group_id as $key => $val) {
                        $goods_group = new GoodsGroup();
                        $goods_group_info = $goods_group->getGoodsGroupDetail($val);
                        if (! empty($goods_group_info)) {
                            $goods_group_name .= $goods_group_info['group_name'] . ',';
                        }
                    }
                    $goods_group_name = rtrim($goods_group_name, ',');
                    $result["data"][$k]['goods_group_name'] = $goods_group_name;
                }
            }
            return $result;
        } else {
            $goods_group = new GoodsGroup();
            $groupList = $goods_group->getGoodsGroupList(1, 0, [
                'shop_id' => $this->instance_id,
                'pid' => 0
            ]);
            if (! empty($groupList['data'])) {
                foreach ($groupList['data'] as $k => $v) {
                    $v['sub_list'] = $goods_group->getGoodsGroupList(1, 0, 'pid = ' . $v['group_id']);
                }
            }
            $this->assign("goods_group", $groupList['data']);
            $search_info = request()->get('search_info', '');
            $this->assign("search_info", $search_info);
            // 查找一级商品分类
            $goodsCategory = new GoodsCategory();
            $oneGoodsCategory = $goodsCategory->getGoodsCategoryListByParentId(0);
            $this->assign("oneGoodsCategory", $oneGoodsCategory);
            // 供货商列表
            $supplier = new Supplier();
            $supplier_list = $supplier->getSupplierList();
            $this->assign("supplier_list", $supplier_list['data']);
            // 上下架
            $state = request()->get("state", "");
            $this->assign("state", $state);
            // 库存预警
            $stock_warning = request()->get("stock_warning", 0);
            $this->assign("stock_warning", $stock_warning);
            return view($this->style . "Goods/goodsList");
        }
    }

    public function getCategoryByParentAjax()
    {
        if (request()->isAjax()) {
            $parentId = request()->post("parentId", '');
            $goodsCategory = new GoodsCategory();
            $res = $goodsCategory->getGoodsCategoryListByParentId($parentId);
            return $res;
        }
    }

    /**
     * 创建时间：2015年6月1日09:40:10 创建人：高伟
     * 功能说明：通过ajax来的得到页面的数据
     */
    public function SelectCateGetData()
    {
        $goods_category_id = request()->post("goods_category_id", ''); // 商品类目用
        $goods_category_name = request()->post("goods_category_name", ''); // 商品类目名称显示用
        $goods_attr_id = request()->post("goods_attr_id", ''); // 关联商品类型ID
        $quick = request()->post("goods_category_quick", ''); // JSON格式
        setcookie("goods_category_id", $goods_category_id, time() + 3600 * 24);
        setcookie("goods_category_name", $goods_category_name, time() + 3600 * 24);
        setcookie("goods_attr_id", $goods_attr_id, time() + 3600 * 24);
        setcookie("goods_category_quick", $quick, time() + 3600 * 24);
    }

    /**
     * 获取用户快速选择商品
     */
    public function getQuickGoods()
    {
        if (isset($_COOKIE["goods_category_quick"])) {
            return $_COOKIE["goods_category_quick"];
        } else {
            return - 1;
        }
    }

    public function getGoodsGroupList()
    {
        $goods_group = new GoodsGroup();
        return $goods_group->getGroupGroup();
    }

    /**
     * 添加商品
     */
    public function addGoods()
    {
        $goods_group = new GoodsGroup();
        $express = new Express();
        $goods = new GoodsService();
        $supplier = new Supplier();
        $goodsbrand = new GoodsBrand();
        
        $goodsId = request()->get('goodsId', 0);
        $groupList = $goods_group->getGoodsGroupList(1, 0, [
            'shop_id' => $this->instance_id
        ]);
        
        $supplier_list = $supplier->getSupplierList();
        $this->assign("supplier_list", $supplier_list['data']);
        
        $goods_attr_id = 0; // 商品类目关联id
        if (isset($_COOKIE["goods_category_id"])) {
            $this->assign("goods_category_id", $_COOKIE["goods_category_id"]);
            $name = str_replace(":", "&gt;", $_COOKIE["goods_category_name"]);
            $this->assign("goods_category_name", $name);
            $goods_attr_id = $_COOKIE["goods_attr_id"];
        } else {
            $this->assign("goods_category_id", 0); // 修改商品时，会进行查询赋值 2016年12月9日 10:54:07
            $this->assign("goods_category_name", "");
        }
        $this->assign("goods_attr_id", $goods_attr_id);
        $goods_attribute_list = $goods->getAttributeServiceList(1, 0);
        $this->assign("goods_attribute_list", $goods_attribute_list['data']); // 商品类型
        $this->assign("shipping_list", $express->shippingFeeQuery("")); // 物流
        $this->assign("group_list", $groupList['data']); // 分组
        if (empty($groupList['data'])) {
            $this->assign("group_str", '');
        } else {
            $this->assign("group_str", json_encode($groupList['data']));
        }
        $this->assign("goods_id", $goodsId);
        $this->assign("shop_type", 2);
        // 相册列表
        $album = new Album();
        $detault_album_detail = $album->getDefaultAlbumDetail();
        $this->assign('detault_album_id', $detault_album_detail['album_id']);
        // 物流公司
        $expressCompanyList = $express->getExpressCompanyList(1, 0, [
            'shop_id' => $this->instance_id
        ]);
        $this->assign("expressCompanyList", $expressCompanyList['data']);
        
        //虚拟商品类型
        $virtual_goods = new VirtualGoods();
        $virtual_goods_type_list = $virtual_goods->getVirtualGoodsTypeList(1, 0);
        $this->assign("virtual_goods_type_list", $virtual_goods_type_list['data']);
        
        if ($goodsId > 0) {
            if (! is_numeric($goodsId)) {
                $this->error("参数错误");
            }
            $this->assign("goodsid", $goodsId);
            $goods_info = $goods->getGoodsDetail($goodsId);
            if (! empty($goods_info)) {
                $goods_info['sku_list'] = json_encode($goods_info['sku_list']);
                $goods_info['goods_group_list'] = json_encode($goods_info['goods_group_list']);
                $goods_info['img_list'] = json_encode($goods_info['img_list']);
                $goods_info['goods_attribute_list'] = json_encode($goods_info['goods_attribute_list']);
                // 判断规格数组中图片路径是id还是路径
                if (trim($goods_info['goods_spec_format']) != "") {
                    $album = new Album();
                    $goods_spec_array = json_decode($goods_info['goods_spec_format'], true);
                    foreach ($goods_spec_array as $k => $v) {
                        foreach ($v["value"] as $t => $m) {
                            if (is_numeric($m["spec_value_data"]) && $m["spec_show_type"] == 3) {
                                $picture_detail = $album->getAlubmPictureDetail([
                                    "pic_id" => $m["spec_value_data"]
                                ]);
                                if (! empty($picture_detail)) {
                                    $goods_spec_array[$k]["value"][$t]["spec_value_data_src"] = $picture_detail["pic_cover_micro"];
                                }
                            } else 
                                if (! is_numeric($m["spec_value_data"]) && $m["spec_show_type"] == 3) {
                                    $goods_spec_array[$k]["value"][$t]["spec_value_data_src"] = $m["spec_value_data"];
                                }
                        }
                    }
                    $goods_spec_format = json_encode($goods_spec_array, JSON_UNESCAPED_UNICODE);
                    $goods_info['goods_spec_format'] = $goods_spec_format;
                }
                $extent_sort = count($goods_info["extend_category"]);
                $this->assign("extent_sort", $extent_sort);
                if ($goods_info["group_id_array"] == "") {
                    $this->assign("edit_group_array", array());
                } else {
                    $this->assign("edit_group_array", explode(",", $goods_info["group_id_array"]));
                }
                /**
                 * 当前cookie中存的goodsid
                 */
                $update_goods_id = isset($_COOKIE["goods_update_goodsid"]) ? $_COOKIE["goods_update_goodsid"] : 0;
                if ($update_goods_id == $goodsId) {
                    // $category_name = str_replace(":", "&gt;", $_COOKIE["goods_category_name"]);
                    $category_name = str_replace(":", "", $_COOKIE["goods_category_name"]);
                    $goods_info["category_id"] = $_COOKIE["goods_category_id"];
                    $goods_info["category_name"] = $category_name;
                }
                $goods_info['description'] = str_replace(PHP_EOL, '', $goods_info['description']);
                $this->assign("goods_info", $goods_info);
                // 规格数据转json
                if (! empty($goods_info["sku_picture_array"])) {
                    $sku_picture_array_str = json_encode($goods_info["sku_picture_array"]);
                } else {
                    $sku_picture_array_str = '';
                }
                $this->assign("sku_picture_array_str", $sku_picture_array_str);
                
                $brand_info = $goodsbrand->getGoodsBrandInfo($goods_info['brand_id'], 'brand_id,brand_name');
                $goods_info['brand_info'] = $brand_info;
                return view($this->style . "Goods/selectCategoryNextUpdate");
            } else {
                $this->error("商品不存在");
            }
        } else {
            return view($this->style . 'Goods/selectCategoryNext');
        }
    }

    /**
     * 获取商品品牌列表，商品编辑时用到
     * 创建时间：2017年11月11日 09:59:06 王永杰
     */
    public function getGoodsBrandList()
    {
        $page_index = request()->post("page_index", 1);
        $page_size = request()->post('page_size', PAGESIZE);
        $brand_name = request()->post("brand_name", "");
        $search_name = request()->post("search_name", "");
        $brand_id = request()->post("brand_id", "");
        // 排除当前选中的品牌，然后模糊查询
        $condition = array(
            'shop_id' => $this->instance_id,
            'brand_name|brand_initial' => array(
                [
                    "like",
                    "%$search_name%"
                ],
                [
                    'eq',
                    $brand_name
                ],
                'or'
            )
        );
        // 判断当时编辑商品还是添加商品，如果存在品牌id，则排除该品牌，防止搜索结果出现重复数据
        if (! empty($brand_id)) {
            $condition['brand_id'] = [
                'neq',
                $brand_id
            ];
        }
        $goodsbrand = new GoodsBrand();
        $goods_brand_list = $goodsbrand->getGoodsBrandList($page_index, $page_size, $condition, '', 'brand_id,brand_name');
        return $goods_brand_list;
    }

    /**
     * 根据商品类型id查询，商品规格信息
     * 2017年6月5日 17:36:09 wyj
     */
    public function getGoodsSpecListByAttrId()
    {
        $goods = new GoodsService();
        $condition["attr_id"] = request()->post("attr_id", 0);
        $list = $goods->getGoodsAttrSpecQuery($condition);
        return $list;
    }

    /**
     * 创建时间：2015年5月28日11:19:30 创建人：高伟
     * 功能说明：通过节点的ID查询得到某个节点下的子集
     */
    public function getChildCateGory()
    {
        $categoryID = request()->post('categoryID', '');
        $goods_category = new GoodsCategory();
        $list = $goods_category->getGoodsCategoryListByParentId($categoryID);
        return $list;
    }

    /**
     * 修改商品
     */
    public function updataGoods()
    {
        return view($this->style . "Goods/addGoods");
    }

    /**
     * 删除商品
     */
    public function deleteGoods()
    {
        $goods_ids = request()->post('goods_ids');
        $goodservice = new GoodsService();
        $retval = $goodservice->deleteGoods($goods_ids);
        return AjaxReturn($retval);
    }

    /**
     * 删除回收站商品
     */
    public function emptyDeleteGoods()
    {
        $goods_ids = request()->post('goods_ids');
        $goodsservice = new GoodsService();
        $res = $goodsservice->deleteRecycleGoods($goods_ids);
        return AjaxReturn($res);
    }

    /**
     * 商品品牌列表
     */
    public function goodsBrandList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $search_text = request()->post("search_text", "");
            $goodsbrand = new GoodsBrand();
            $result = $goodsbrand->getGoodsBrandList($page_index, $page_size, [
                'shop_id' => $this->instance_id,
                'brand_name' => array(
                    "like",
                    "%" . $search_text . "%"
                )
            ], "brand_initial asc");
            $goodsCatefory = new GoodsCategory();
            foreach ($result['data'] as $v) {
                $v['category_id_1_name'] = ! empty($goodsCatefory->getName($v['category_id_1'])['category_name']) ? $goodsCatefory->getName($v['category_id_1'])['category_name'] : "";
                $v['category_id_2_name'] = ! empty($goodsCatefory->getName($v['category_id_2'])['category_name']) ? $goodsCatefory->getName($v['category_id_2'])['category_name'] : "";
                $v['category_id_3_name'] = ! empty($goodsCatefory->getName($v['category_id_3'])['category_name']) ? $goodsCatefory->getName($v['category_id_3'])['category_name'] : "";
            }
            return $result;
        } else {
            return view($this->style . "Goods/goodsBrandList");
        }
    }

    /**
     * 添加商品品牌
     */
    public function addGoodsBrand()
    {
        if (request()->isAjax()) {
            $goodsbrand = new GoodsBrand();
            $shop_id = $this->instance_id;
            $brand_name = request()->post('brand_name', '');
            $brand_initial = request()->post('brand_initial', '');
            $brand_pic = request()->post('brand_pic', '');
            $brand_recommend = request()->post('brand_recommend', '');
            $category_name = request()->post('category_name', '');
            $category_id_1 = request()->post('category_id_1', 0);
            $category_id_2 = request()->post('category_id_2', 0);
            $category_id_3 = request()->post('category_id_3', 0);
            $sort = 1;
            $brand_category_name = '';
            $category_id_array = 1;
            $brand_ads = request()->post('brand_ads', '');
            $res = $goodsbrand->addOrUpdateGoodsBrand('', $shop_id, $brand_name, $brand_initial, '', $brand_pic, $brand_recommend, $sort, $brand_category_name, $category_id_array, $brand_ads, $category_name, $category_id_1, $category_id_2, $category_id_3);
            return AjaxReturn($res);
        } else {
            $goodscategory = new GoodsCategory();
            $list = $goodscategory->getGoodsCategoryListByParentId(0);
            $this->assign('goods_category_list', $list);
            
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "goods/goodsbrandlist",
                        'menu_name' => "商品品牌",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            
            return view($this->style . "Goods/addGoodsBrand");
        }
    }

    /**
     * 选择商品分类
     */
    function changeCategory()
    {
        $pid = request()->post('pid', 0);
        $list = array();
        if ($pid > 0) {
            $goodscategory = new GoodsCategory();
            $list = $goodscategory->getGoodsCategoryListByParentId($pid);
        }
        return $list;
    }

    /**
     * 修改商品品牌
     */
    public function updateGoodsBrand()
    {
        $goodsbrand = new GoodsBrand();
        if (request()->isAjax()) {
            $brand_id = request()->post('brand_id', '');
            $brand_name = request()->post('brand_name', '');
            $brand_initial = request()->post('brand_initial', '');
            $brand_pic = request()->post('brand_pic', '');
            $brand_recommend = request()->post('brand_recommend', 0);
            $category_name = request()->post('category_name', '');
            $category_id_1 = request()->post('category_id_1', 0);
            $category_id_2 = request()->post('category_id_2', 0);
            $category_id_3 = request()->post('category_id_3', 0);
            $sort = 1;
            $brand_category_name = '';
            $category_id_array = 1;
            $shopid = $this->instance_id;
            $brand_ads = request()->post('brand_ads', '');
            $res = $goodsbrand->addOrUpdateGoodsBrand($brand_id, $shopid, $brand_name, $brand_initial, '', $brand_pic, $brand_recommend, $sort, $brand_category_name, $category_id_array, $brand_ads, $category_name, $category_id_1, $category_id_2, $category_id_3);
            return AjaxReturn($res);
        } else {
            $brand_id = request()->get('brand_id', '');
            if (! is_numeric($brand_id)) {
                $this->error('未获取到信息');
            }
            $brand_info = $goodsbrand->getGoodsBrandInfo($brand_id);
            if (empty($brand_info)) {
                return $this->error("没有查询到商品品牌信息");
            }
            $this->assign('brand_info', $brand_info);
            $goodscategory = new GoodsCategory();
            $list = $goodscategory->getGoodsCategoryListByParentId(0);
            $this->assign('goods_category_list', $list);
            
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "goods/goodsbrandlist",
                        'menu_name' => "商品品牌",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            
            return view($this->style . "Goods/editGoodsBrand");
        }
    }

    /**
     * 删除商品品牌
     */
    public function deleteGoodsBrand()
    {
        $brand_id = request()->post('brand_id', '');
        $goodsbrand = new GoodsBrand();
        $res = $goodsbrand->deleteGoodsBrand($brand_id);
        return AjaxReturn($res);
    }

    /**
     * 商品分类列表
     */
    public function goodsCategoryList()
    {
        $goods_category = new GoodsCategory();
        $one_list = $goods_category->getCategoryTreeUseInAdmin();
        $this->assign("category_list", $one_list);
        return view($this->style . "Goods/goodsCategoryList");
    }

    /**
     * 添加商品分类
     */
    public function addGoodsCategory()
    {
        $goodscate = new GoodsCategory();
        if (request()->isAjax()) {
            $category_name = request()->post("category_name", '');
            $pid = request()->post("pid", '');
            $is_visible = request()->post('is_visible', '');
            $keywords = request()->post("keywords", '');
            $description = request()->post("description", '');
            $sort = request()->post("sort", '');
            $category_pic = request()->post('category_pic', '');
            $attr_id = request()->post("attr_id", 0);
            $attr_name = request()->post("attr_name", '');
            $short_name = request()->post("short_name", '');
            $result = $goodscate->addOrEditGoodsCategory(0, $category_name, $short_name, $pid, $is_visible, $keywords, $description, $sort, $category_pic, $attr_id, $attr_name);
            return AjaxReturn($result);
        } else {
            $category_list = $goodscate->getGoodsCategoryTree(0);
            $this->assign('category_list', $category_list);
            $goods = new GoodsService();
            $goodsAttributeList = $goods->getAttributeServiceList(1, 0);
            $this->assign("goodsAttributeList", $goodsAttributeList['data']);
            
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "goods/goodscategorylist",
                        'menu_name' => "商品分类",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            
            return view($this->style . "Goods/addGoodsCategory");
        }
    }

    /**
     * 修改商品分类
     */
    public function updateGoodsCategory()
    {
        $goodscate = new GoodsCategory();
        if (request()->isAjax()) {
            $category_id = request()->post("category_id", '');
            $category_name = request()->post("category_name", '');
            $short_name = request()->post("short_name", '');
            $pid = request()->post("pid", '');
            $is_visible = request()->post('is_visible', '');
            $keywords = request()->post("keywords", '');
            $description = request()->post("description", '');
            $sort = request()->post("sort", '');
            $attr_id = request()->post("attr_id", 0);
            $attr_name = request()->post("attr_name", '');
            $category_pic = request()->post('category_pic', '');
            $goods_category_quick = request()->post("goods_category_quick", '');
            if ($goods_category_quick != '') {
                setcookie("goods_category_quick", $goods_category_quick, time() + 3600 * 24);
            }
            $result = $goodscate->addOrEditGoodsCategory($category_id, $category_name, $short_name, $pid, $is_visible, $keywords, $description, $sort, $category_pic, $attr_id, $attr_name);
            return AjaxReturn($result);
        } else {
            $category_id = request()->get('category_id', '');
            $result = $goodscate->getGoodsCategoryDetail($category_id);
            $this->assign("data", $result);
            // 查询比当前等级高的 分类
            if ($result['level'] == 1) {
                $chile_list = $goodscate->getGoodsCategoryTree($category_id);
                if (empty($chile_list)) {
                    $category_list = $goodscate->getGoodsCategoryTree(0);
                } else {
                    $is_have = false;
                    foreach ($chile_list as $k => $v) {
                        if ($v["level"] == 3) {
                            $is_have = true;
                        }
                    }
                    if ($is_have) {
                        $category_list = array();
                    } else {
                        $category_list = $goodscate->getGoodsCategoryListByParentId(0);
                    }
                }
            } else 
                if ($result['level'] == 2) {
                    $chile_list = $goodscate->getGoodsCategoryListByParentId($category_id);
                    if (empty($chile_list)) {
                        $category_list = $goodscate->getGoodsCategoryTree(0);
                    } else {
                        $category_list = $goodscate->getGoodsCategoryListByParentId(0);
                    }
                } else 
                    if ($result['level'] == 3) {
                        $category_list = $goodscate->getGoodsCategoryTree(0);
                    }
            foreach ($category_list as $k => $v) {
                if ($v["category_id"] == $category_id && $category_id !== 0) {
                    unset($category_list[$k]);
                } else {
                    if (isset($v["child_list"])) {
                        $temp_array = $v["child_list"];
                        foreach ($temp_array as $t => $m) {
                            if ($m["category_id"] == $category_id && $category_id !== 0) {
                                unset($temp_array[$t]);
                            }
                        }
                        sort($temp_array);
                        $category_list[$k]["child_list"] = $temp_array;
                    }
                }
            }
            sort($category_list);
            $this->assign('category_list', $category_list);
            $goods = new GoodsService();
            $goodsAttributeList = $goods->getAttributeServiceList(1, 0);
            $this->assign("goodsAttributeList", $goodsAttributeList['data']);
            
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "goods/goodscategorylist",
                        'menu_name' => "商品分类",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            
            return view($this->style . "Goods/updateGoodsCategory");
        }
    }

    /**
     * 删除商品分类
     */
    public function deleteGoodsCategory()
    {
        $goodscate = new GoodsCategory();
        $category_id = request()->post('category_id', '');
        $res = $goodscate->deleteGoodsCategory($category_id);
        if ($res > 0) {
            $goods_category_quick = request()->post("goods_category_quick", '');
            if ($goods_category_quick != '') {
                setcookie("goods_category_quick", $goods_category_quick, time() + 3600 * 24);
            }
        }
        return AjaxReturn($res);
    }

    /**
     * 创建时间：2015年6月10日15:25:14 创建人：高伟
     * 修改时间：2017年5月24日 15:49:10 王永杰
     * 功能说明：查询商品属性
     */
    public function getGoodsAttributeList()
    {
        $goods = new GoodsService();
        $condition['shop_id'] = $this->instance_id;
        $provList = $goods->getGoodsAttributeList($condition, '*', 'create_time desc');
        return $provList;
    }

    /**
     * 创建时间：2015年6月1日17:17:53 创建人：高伟
     * 功能说明：商品属性规格获取
     */
    public function CateGoryPropsGet()
    {
        $name = request()->post('name', '');
        $goodservice = new GoodsService();
        $res = $goodservice->addGoodsSpec($name);
        return $res;
    }

    /**
     * 创建时间：2015年6月1日17:17:53 创建人：高伟
     * 功能说明：商品属性规格值获取
     */
    public function CateGoryPropvaluesGet()
    {
        $propId = request()->post('propId', '');
        $value = request()->post('value', '');
        $goodservice = new GoodsService();
        $res = $goodservice->addGoodsSpecValue($propId, $value);
        return $res;
    }

    /**
     * 设置规格属性是否启用
     */
    public function setIsvisible()
    {
        if (request()->isAjax()) {
            $spec_id = request()->post('spec_id', '');
            $is_visible = request()->post('is_visible', '');
            $goodservice = new GoodsService();
            $retval = $goodservice->updateGoodsSpecIsVisible($spec_id, $is_visible);
            return AjaxReturn($retval);
        }
    }

    /**
     * 创建时间：2015年6月12日09:50:07 创建人：高伟
     * 功能说明：添加或更新商品时 ajax调用的函数
     */
    public function GoodsCreateOrUpdate()
    {
        $res = 0;
        $product = request()->post('product', '');
        $qrcode = request()->post('is_qrcode', ''); // 1代表 需要创建 二维码 0代表不需要
        if (! empty($product)) {
            $product = json_decode($product, true);
            
            $shopId = $this->instance_id;
            $goodservice = new GoodsService();
            $res = $goodservice->addOrEditGoods(
                $product["goodsId"], // 商品Id
                $product["title"], // 商品标题
                $shopId, 
                $product["categoryId"], // 商品类目
                $category_id_1 = 0,
                $category_id_2 = 0, 
                $category_id_3 = 0, 
                $product["supplierId"], 
                $product["brandId"], 
                $product["groupArray"], // 商品分组
                $product['goods_type'], 
                $product["market_price"], 
                $product["price"], // 商品现价
                $product["cost_price"], 
                $product["point_exchange_type"], 
                $product['integration_available_use'],
                $product['integration_available_give'], 
                $is_member_discount = 0, 
                $product["shipping_fee"], 
                $product["shipping_fee_id"], 
                $product["stock"], 
                $product['max_buy'], 
                $product['min_buy'], 
                $product["minstock"], 
                $product["base_good"], 
                $product["base_sales"], 
                $collects = 0, 
                $star = 0, 
                $evaluates = 0, 
                $product["base_share"], 
                $product["province_id"],
                $product["city_id"], 
                $product["picture"], 
                $product['key_words'], 
                $product["introduction"], // 商品简介，促销语
                $product["description"],
                $product['qrcode'], // 商品二维码
                $product["code"], 
                $product["display_stock"], 
                $is_hot = 0, 
                $is_recommend = 0, 
                $is_new = 0, 
                $sort = $product['sort'], 
                $product["imageArray"], 
                $product["skuArray"], 
                $product["is_sale"], '', // $product["sku_img_array"]
                $product['goods_attribute_id'], 
                $product['goods_attribute'], 
                $product['goods_spec_format'], 
                $product['goods_weight'], 
                $product['goods_volume'], 
                $product['shipping_fee_type'], 
                $product['categoryExtendId'], 
                $product["sku_picture_vlaues"], 
                $product['virtual_goods_type_id'],
                $product['production_date'], 
                $product['shelf_life']
            );
            
            // sku编码分组
            
            if ($res > 0 && $qrcode == 1) {
                $goodsId = $res;
                
                $url = __URL(Config::get('view_replace_str.APP_MAIN') . '/goods/goodsdetail?id=' . $goodsId);
                $pay_qrcode = getQRcode($url, 'upload/goods_qrcode', 'goods_qrcode_' . $goodsId);
                
                $goodservice->goods_QRcode_make($goodsId, $pay_qrcode);
            }
        }
        
        return $res;
    }

    /**
     * 获取省列表，商品添加时用户可以设置商品所在地
     * 创建人：王永杰
     * 创建时间：2017年2月22日 16:01:26
     */
    public function getProvince()
    {
        $address = new Address();
        $province_list = $address->getProvinceList();
        return $province_list;
    }

    /**
     * 获取城市列表
     * 创建人：王永杰
     * 创建时间：2017年2月22日 16:01:56
     *
     * @return Ambigous <multitype:\think\static , \think\false, \think\Collection, \think\db\false, PDOStatement, string, \PDOStatement, \think\db\mixed, boolean, unknown, \think\mixed, multitype:, array>
     */
    public function getCity()
    {
        $address = new Address();
        $province_id = request()->post('province_id', 0);
        $city_list = $address->getCityList($province_id);
        return $city_list;
    }

    /**
     * 商品分组列表
     */
    public function goodsGroupList()
    {
        if (request()->isAjax()) {
            $goodsgroup = new GoodsGroup();
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $condition = array();
            $list = $goodsgroup->getGoodsGroupList($page_index, $page_size, $condition, "pid, sort");
            return $list;
        } else {
            
            return view($this->style . "Goods/goodsGroupList");
        }
    }

    /**
     * 添加商品分组
     */
    public function addGoodsGroup()
    {
        $goodsgroup = new GoodsGroup();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $group_name = request()->post('group_name', '');
            $pid = request()->post('pid', 0);
            $is_visible = request()->post('is_visible', '');
            $sort = request()->post('sort', '');
            $group_pic = request()->post('group_pic', '');
            $result = $goodsgroup->addOrEditGoodsGroup(0, $shop_id, $group_name, $pid, $is_visible, $sort, $group_pic);
            return AjaxReturn($result);
        } else {
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "goods/goodsgrouplist",
                        'menu_name' => "商品标签",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            
            return view($this->style . "Goods/addGoodsGroup");
        }
    }

    /**
     * 修改商品分类
     */
    public function updateGoodsGroup()
    {
        $goodsgroup = new GoodsGroup();
        if (request()->isAjax()) {
            $group_id = request()->post('group_id', '');
            $shop_id = $this->instance_id;
            $group_name = request()->post('group_name', '');
            $pid = request()->post('pid', '');
            $is_visible = request()->post('is_visible', '');
            $sort = request()->post('sort', '');
            $group_pic = request()->post('group_pic', '');
            $result = $goodsgroup->addOrEditGoodsGroup($group_id, $shop_id, $group_name, $pid, $is_visible, $sort, $group_pic);
            return AjaxReturn($result);
        } else {
            $group_id = request()->get('group_id', '');
            $result = $goodsgroup->getGoodsGroupDetail($group_id);
            $this->assign("data", $result);
            
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "goods/goodsgrouplist",
                        'menu_name' => "商品标签",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            
            return view($this->style . "Goods/updateGoodsGroup");
        }
    }

    /**
     * 删除商品分类
     */
    public function deleteGoodsGroup()
    {
        $goodsgroup = new GoodsGroup();
        $group_id = request()->post('group_id', '');
        if (! is_numeric($group_id)) {
            $this->error('未获取到信息');
        }
        $res = $goodsgroup->deleteGoodsGroup($group_id, $this->instance_id);
        return AjaxReturn($res);
    }

    /**
     * 修改 商品 分类 单个字段
     */
    public function modifyGoodsCategoryField()
    {
        $goodscate = new GoodsCategory();
        $fieldid = request()->post('fieldid', '');
        $fieldname = request()->post('fieldname', '');
        $fieldvalue = request()->post('fieldvalue', '');
        $res = $goodscate->ModifyGoodsCategoryField($fieldid, $fieldname, $fieldvalue);
        return $res;
    }

    /**
     * 修改 商品 分组 单个字段
     */
    public function modifyGoodsGroupField()
    {
        $goodsgroup = new GoodsGroup();
        $fieldid = request()->post('fieldid', '');
        $fieldname = request()->post('fieldname', '');
        $fieldvalue = request()->post('fieldvalue', '');
        $res = $goodsgroup->ModifyGoodsGroupField($fieldid, $fieldname, $fieldvalue);
        return $res;
    }

    /**
     * 商品上架
     */
    public function ModifyGoodsOnline()
    {
        $condition = request()->post('goods_ids', '');
        $goods_detail = new GoodsService();
        $result = $goods_detail->ModifyGoodsOnline($condition);
        return AjaxReturn($result);
    }

    /**
     * 商品下架
     */
    public function ModifyGoodsOffline()
    {
        $condition = request()->post('goods_ids', '');
        $goods_detail = new GoodsService();
        $result = $goods_detail->ModifyGoodsOffline($condition);
        return AjaxReturn($result);
    }

    /**
     * 获取筛选后的商品
     *
     * @return unknown
     */
    public function getSearchGoodsList()
    {
        $page_index = request()->post("page_index", 1);
        $page_size = request()->post("page_size", PAGESIZE);
        $search_text = request()->post("search_text","");
        $condition = array(
            "goods_name" => ["like", "%$search_text%"]
        );
        $goods_detail = new GoodsService();
        $result = $goods_detail->getSearchGoodsList($page_index, $page_size, $condition);
        return $result;
    }

    /**
     * 获取 商品分组一级分类
     *
     * @return Ambigous <number, unknown>
     */
    public function getGoodsGroupFristLevel()
    {
        $goods_group = new GoodsGroup();
        $list = $goods_group->getGoodsGroupListByParentId($this->instance_id, 0);
        return $list;
    }

    /**
     * 修改分组
     */
    public function ModifyGoodsGroup()
    {
        $goods_id = request()->post('goods_id', '');
        $goods_type = request()->post('goods_type', '');
        $goods_detail = new GoodsService();
        $result = $goods_detail->ModifyGoodsGroup($goods_id, $goods_type);
        return AjaxReturn($result);
    }

    /**
     * 修改推荐商品
     */
    public function ModifyGoodsRecommend()
    {
        $goods_ids = request()->post('goods_id', '');
        $recommend_type = request()->post('recommend_type', '');
        $goods_detail = new GoodsService();
        $result = $goods_detail->ModifyGoodsRecommend($goods_ids, $recommend_type);
        return AjaxReturn($result);
    }

    /**
     * 商品属性
     */
    public function goodsSpecList()
    {
        $goods = new GoodsService();
        if (request()->isAjax()) {
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $list = $goods->getGoodsSpecList($page_index, $page_size, '', 'sort asc, create_time desc');
            return $list;
        }
        return view($this->style . 'Goods/goodsSpecList');
    }

    /**
     * 修改商品规格单个属性值
     */
    public function setGoodsSpecField()
    {
        $goods = new GoodsService();
        $spec_id = request()->post("id", '');
        $field_name = request()->post("name", '');
        $field_value = request()->post("value", '');
        $retval = $goods->modifyGoodsSpecField($spec_id, $field_name, $field_value);
        return AjaxReturn($retval);
    }

    /**
     * 添加规格
     */
    public function addGoodsSpec()
    {
        $goods = new GoodsService();
        if (request()->isAjax()) {
            $spec_name = request()->post('spec_name', '');
            $is_visible = request()->post('is_visible', '');
            $sort = request()->post('sort', '');
            $show_type = request()->post('show_type', '');
            $spec_value_str = request()->post('spec_value_str', '');
            $attr_id = request()->post('attr_id', 0);
            $is_screen = request()->post('is_screen', 0);
            $res = $goods->addGoodsSpecService($this->instance_id, $spec_name, $show_type, $is_visible, $sort, $spec_value_str, $attr_id, $is_screen);
            return AjaxReturn($res);
        }
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "goods/goodsspeclist",
                    'menu_name' => "商品规格",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . 'Goods/addGoodsSpec');
    }

    /**
     * 修改规格
     *
     * @return multitype:unknown
     */
    public function updateGoodsSpec()
    {
        $goods = new GoodsService();
        $spec_id = request()->get('spec_id', '');
        if (request()->isAjax()) {
            $spec_id = request()->post('spec_id', '');
            $spec_name = request()->post('spec_name', '');
            $is_visible = request()->post('is_visible', '');
            $show_type = request()->post('show_type', '');
            $sort = request()->post('sort', '');
            $spec_value_str = request()->post('spec_value_str', '');
            $is_screen = request()->post('is_screen', 0);
            $res = $goods->updateGoodsSpecService($spec_id, $this->instance_id, $spec_name, $show_type, $is_visible, $sort, $spec_value_str, $is_screen);
            return AjaxReturn($res);
        }
        $detail = $goods->getGoodsSpecDetail($spec_id);
        $this->assign('info', $detail);
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "goods/goodsspeclist",
                    'menu_name' => "商品规格",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . 'Goods/updateGoodsSpec');
    }

    /**
     * 修改商品规格属性
     * 备注：编辑商品时，也用到了这个方法，公共的啊 2017年6月5日 19:39:35 王永杰
     */
    public function modifyGoodsSpecValueField()
    {
        $goods = new GoodsService();
        $spec_value_id = request()->post("spec_value_id", '');
        $field_name = request()->post('field_name', '');
        $field_value = request()->post('field_value', '');
        $retval = $goods->modifyGoodsSpecValueField($spec_value_id, $field_name, $field_value);
        return AjaxReturn($retval);
    }

    /**
     * 删除商品规格
     */
    public function deleteGoodsSpec()
    {
        $spec_id = request()->post('spec_id', 0);
        $goods = new GoodsService();
        $res = $goods->deleteGoodsSpec($spec_id);
        return AjaxReturn($res);
    }

    /**
     * 删除商品规格属性
     */
    public function deleteGoodsSpecValue()
    {
        $goods = new GoodsService();
        $spec_id = request()->post('spec_id', 0);
        $spec_value_id = request()->post('spec_value_id', 0);
        
        $res = $goods->deleteGoodsSpecValue($spec_id, $spec_value_id);
        return AjaxReturn($res);
    }

    /**
     * 商品类型
     */
    public function attributelist()
    {
        if (request()->isAjax()) {
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', 0);
            $goods = new GoodsService();
            $goodsEvaluateList = $goods->getAttributeServiceList($page_index, $page_size, '', 'sort');
            return $goodsEvaluateList;
        }
        return view($this->style . "Goods/attributelist");
    }

    /**
     * 添加一条商品属性值
     */
    public function addAttributeServiceValue()
    {
        $goods = new GoodsService();
        $attr_id = request()->post('attr_id', '');
        
        $res = $goods->addAttributeValueService($attr_id, '', 1, 255, 1, '');
        return AjaxReturn($res);
    }

    /**
     * 添加商品类型
     */
    public function addAttributeService()
    {
        $goods = new GoodsService();
        $goodsguige = $goods->getGoodsSpecList(1, 0, '', 'sort desc');
        $this->assign('goodsguige', $goodsguige);
        if (request()->isAjax()) {
            $attr_name = request()->post('attr_name', '');
            $is_use = request()->post('is_visible', '');
            $sort = request()->post('sort', '');
            $spec_id_array = request()->post('select_box', '');
            $value_string = request()->post('data_obj_str', '');
            $goodsAttribute = $goods->addAttributeService($attr_name, $is_use, $spec_id_array, $sort, $value_string);
            return AjaxReturn($goodsAttribute);
        }
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "goods/attributelist",
                    'menu_name' => "商品类型",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . 'Goods/addGoodsAttribute');
    }

    /**
     * 删除一条商品类型属性
     */
    public function deleteAttributeValue()
    {
        $goods = new GoodsService();
        $attr_id = request()->post('attr_id', 0);
        $attr_value_id = request()->post('attr_value_id', 0);
        $res = $goods->deleteAttributeValueService($attr_id, $attr_value_id);
        return AjaxReturn($res);
    }

    /**
     * 修改商品类型
     */
    public function updateGoodsAttribute()
    {
        $goods = new GoodsService();
        $attr_id = request()->get('attr_id', '');
        if (request()->isAjax()) {
            $attr_id = request()->post('attr_id', '');
            $attr_name = request()->post('attr_name', '');
            $is_use = request()->post('is_visible', '');
            $sort = request()->post('sort', '');
            $spec_id_array = request()->post('select_box', '');
            $value_string = request()->post('data_obj_str', '');
            $res = $goods->updateAttributeService($attr_id, $attr_name, $is_use, $spec_id_array, $sort, $value_string);
            return AjaxReturn($res);
        }
        $attribute_detail = $goods->getAttributeServiceDetail($attr_id);
        $this->assign('info', $attribute_detail);
        $goodsguige = $goods->getGoodsSpecList(1, 0, '', 'sort desc');
        $this->assign('goodsguige', $goodsguige);
        $this->assign('attr_id', $attr_id);
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "goods/attributelist",
                    'menu_name' => "商品类型",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . 'Goods/updateGoodsAttribute');
    }

    /**
     * 修改商品类型单个属性
     */
    public function setAttributeField()
    {
        $goods = new GoodsService();
        $attr_id = request()->post("id");
        $field_name = request()->post("name");
        $field_value = request()->post("value");
        $reval = $goods->modifyAttributeFieldService($attr_id, $field_name, $field_value);
        return AjaxReturn($reval);
    }

    /**
     * 实时更新属性值
     */
    public function modifyAttributeValueService()
    {
        $goodsattribute = new GoodsService();
        $attr_value_id = request()->post('attr_value_id');
        $field_name = request()->post('field_name');
        $field_value = request()->post('field_value');
        $res = $goodsattribute->modifyAttributeValueService($attr_value_id, $field_name, $field_value);
        // 修改成功后修改商品属性表属性排序
        if ($res) {
            if ($field_name == "sort") {
                $res = $goodsattribute->updateGoodsAttributeSort($attr_value_id, $field_value, $this->instance_id);
            }
        }
        return $res;
    }

    /**
     * 删除商品类型
     */
    public function deleteAttr()
    {
        $attr_id = request()->post('attr_id');
        $goods = new GoodsService();
        $res = $goods->deleteAttributeService($attr_id);
        return AjaxReturn($res);
    }

    /**
     * 商品评论
     */
    public function goodscomment()
    {
        if (request()->isAjax()) {
            $page_index = request()->post('page_index');
            $page_size = request()->post('page_size');
            
            $search = request()->post('search');
            $condition['goods_name'] = array(
                'like',
                "%" . $search . "%"
            );
            
            $member_name = request()->post('member_name', '');
            $start_date = request()->post('start_date') == '' ? 0 : getTimeTurnTimeStamp(request()->post('start_date'));
            $end_date = request()->post('end_date') == '' ? 0 : getTimeTurnTimeStamp(request()->post('end_date'));
            $explain_type = request()->post('explain_type', '');
            if ($start_date != 0 && $end_date != 0) {
                $condition["addtime"] = [
                    [
                        ">",
                        $start_date
                    ],
                    [
                        "<",
                        $end_date
                    ]
                ];
            } elseif ($start_date != 0 && $end_date == 0) {
                $condition["addtime"] = [
                    [
                        ">",
                        $start_date
                    ]
                ];
            } elseif ($start_date == 0 && $end_date != 0) {
                $condition["addtime"] = [
                    [
                        "<",
                        $end_date
                    ]
                ];
            }
            if ($explain_type != "") {
                $condition["explain_type"] = $explain_type;
            }
            if (! empty($member_name)) {
                $condition["member_name"] = array(
                    "like",
                    "%" . $member_name . "%"
                );
            }
            
            $goods = new GoodsService();
            $goodsEvaluateList = $goods->getGoodsEvaluateList($page_index, $page_size, $condition, 'addtime desc');
            return $goodsEvaluateList;
        }
        // $goods = new GoodsService();
        // $goodsEvaluateList = $goods->getGoodsEvaluateList($page_index = 1, $page_size = 0);
        // //var_dump($goodsEvaluateList['data']);
        return view($this->style . "Goods/goodsComment");
    }

    /**
     * 添加商品评价回复
     */
    public function replyEvaluateAjax()
    {
        if (request()->isAjax()) {
            $id = request()->post('evaluate_id');
            $replyType = request()->post('replyType');
            $replyContent = request()->post('evaluate_reply');
            $goods = new GoodsService();
            $res = $goods->addGoodsEvaluateReply($id, $replyContent, $replyType);
            return AjaxReturn($res);
        }
    }

    /**
     * 设置评价的显示状态
     */
    public function setEvaluteShowStatuAjax()
    {
        if (request()->isAjax()) {
            $id = request()->post('evaluate_id');
            $goods = new GoodsService();
            $res = $goods->setEvaluateShowStatu($id);
            return AjaxReturn($res);
        }
    }

    /**
     * 删除评价
     */
    public function deleteEvaluateAjax()
    {
        if (request()->isAjax()) {
            $id = request()->post('evaluate_id');
            $goods = new GoodsService();
            $res = $goods->deleteEvaluate($id);
            return AjaxReturn($res);
        }
    }

    /**
     * 添加 一条商品规格属性
     * 备注：编辑商品的时候也需要添加规格值，方法不能限制死，要共用 2017年6月6日 10:13:30 王永杰
     */
    public function addGoodsSpecValue()
    {
        $goods = new GoodsService();
        $spec_id = request()->post("spec_id", 0); // 规格id
        $spec_value_name = request()->post("spec_value_name", ""); // 规则值
        $spec_value_data = request()->post("spec_value_data", ""); // 规格值对应的颜色值、图片路径
        $is_visible = 1; // 是否可见，第一次添加，默认可见
        $res = $goods->addGoodsSpecValueService($spec_id, $spec_value_name, $spec_value_data, $is_visible, '');
        return AjaxReturn($res);
    }

    /**
     * 商品规格dialog插件
     */
    public function controlDialogSku()
    {
        $attr_id = request()->get("attr_id", 0);
        $this->assign("attr_id", $attr_id);
        return view($this->style . 'Goods/controlDialogSku');
    }

    /**
     * 商品回收站列表
     */
    public function recycleList()
    {
        if (request()->isAjax()) {
            $goodservice = new GoodsService();
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $start_date = request()->post('start_date') == '' ? 0 : getTimeTurnTimeStamp(request()->post('start_date'));
            $end_date = request()->post('end_date') == '' ? 0 : getTimeTurnTimeStamp(request()->post('end_date'));
            $goods_name = request()->post('goods_name', '');
            $category_id_1 = request()->post('category_id_1', '');
            $category_id_2 = request()->post('category_id_2', '');
            $category_id_3 = request()->post('category_id_3', '');
            if ($start_date != 0 && $end_date != 0) {
                $condition["ng.create_time"] = [
                    [
                        ">",
                        $start_date
                    ],
                    [
                        "<",
                        $end_date
                    ]
                ];
            } elseif ($start_date != 0 && $end_date == 0) {
                $condition["ng.create_time"] = [
                    [
                        ">",
                        $start_date
                    ]
                ];
            } elseif ($start_date == 0 && $end_date != 0) {
                $condition["ng.create_time"] = [
                    [
                        "<",
                        $end_date
                    ]
                ];
            }
            if (! empty($goods_name)) {
                $condition["ng.goods_name"] = array(
                    "like",
                    "%" . $goods_name . "%"
                );
            }
            if ($category_id_3 != "") {
                $condition["ng.category_id_3"] = $category_id_3;
            } else 
                if ($category_id_2 != "") {
                    $condition["ng.category_id_2"] = $category_id_2;
                } else 
                    if ($category_id_1 != "") {
                        $condition["ng.category_id_1"] = $category_id_1;
                    }
            $condition["ng.shop_id"] = $this->instance_id;
            $result = $goodservice->getGoodsDeletedList($page_index, $page_size, $condition, "ng.create_time desc");
            return $result;
        } else {
            $search_info = request()->post('search_info', '');
            $this->assign("search_info", $search_info);
            // 查找一级商品分类
            $goodsCategory = new GoodsCategory();
            $oneGoodsCategory = $goodsCategory->getGoodsCategoryListByParentId(0);
            $this->assign("oneGoodsCategory", $oneGoodsCategory);
            return view($this->style . 'Goods/recycleList');
        }
    }

    /**
     * 回收站商品恢复
     */
    public function regainGoodsDeleted()
    {
        if (request()->isAjax()) {
            $goods_ids = request()->post('goods_ids');
            $goods = new GoodsService();
            $res = $goods->regainGoodsDeleted($goods_ids);
            return AjaxReturn($res);
        }
    }

    /**
     * 拷贝商品
     */
    public function copyGoods()
    {
        $goods_id = request()->post('goods_id', '');
        $goodservice = new GoodsService();
        $res = $goodservice->copyGoodsInfo($goods_id);
        if ($res > 0) {
            $goodsId = $res;
            
            $url = Config::get('view_replace_str.APP_MAIN') . '/Goods/goodsDetail?id=' . $goodsId;
            $pay_qrcode = getQRcode($url, 'upload/goods_qrcode', 'goods_qrcode_' . $goodsId);
            
            $goodservice->goods_QRcode_make($goodsId, $pay_qrcode);
        }
        return AjaxReturn($res);
    }

    /**
     * 商品分类选择
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function dialogSelectCategory()
    {
        $category_id = request()->get("category_id", 0);
        $goodsid = request()->get("goodsid", 0);
        $flag = request()->get("flag", 'category');
        // 扩展分类标签id,用户回调方法
        $box_id = request()->get("box_id", '');
        // 已选择扩展分类(用于控制重复选择)
        $category_extend_id = request()->get("category_extend_id", '');
        if (! empty($category_extend_id) && $category_id != 0) {
            $category_extend_id = explode(",", $category_extend_id);
            foreach ($category_extend_id as $k => $v) {
                if ($v == $category_id) {
                    unset($category_extend_id[$k]);
                }
            }
            sort($category_extend_id);
            $category_extend_id = implode(',', $category_extend_id);
        }
        $this->assign("flag", $flag);
        $this->assign("goodsid", $goodsid);
        $this->assign("box_id", $box_id);
        $this->assign("category_extend_id", $category_extend_id);
        
        $goods_category = new GoodsCategory();
        $list = $goods_category->getGoodsCategoryListByParentId(0);
        $this->assign("cateGoryList", $list);
        $category_select_ids = "";
        $category_select_names = "";
        if ($category_id != 0) {
            $category_select_result = $goods_category->getParentCategory($category_id);
            $category_select_ids = $category_select_result["category_ids"];
            $category_select_names = $category_select_result["category_names"];
        }
        $this->assign("category_select_ids", $category_select_ids);
        $this->assign("category_select_names", $category_select_names);
        return view($this->style . 'Goods/dialogSelectCategory');
    }

    /**
     * 更改商品排序
     */
    public function updateGoodsSortAjax()
    {
        if (request()->isAjax()) {
            $goods_id = request()->post("goods_id", "");
            $sort = request()->post("sort", "");
            $goods = new GoodsService();
            $res = $goods->updateGoodsSort($goods_id, $sort);
            return AjaxReturn($res);
        }
    }

    /**
     * 生成商品二维码
     */
    public function updateGoodsQrcode()
    {
        $goods_ids = request()->post('goods_id', '');
        $goods_ids = explode(',', $goods_ids);
        if (! empty($goods_ids) && is_array($goods_ids)) {
            foreach ($goods_ids as $v) {
                $url = __URL(Config::get('view_replace_str.APP_MAIN') . '/goods/goodsdetail?id=' . $v);
                try {
                    $pay_qrcode = getQRcode($url, 'upload/goods_qrcode', 'goods_qrcode_' . $v);
                } catch (\Exception $e) {
                    return AjaxReturn(UPLOAD_FILE_ERROR);
                }
                $goods = new GoodsService();
                $result = $goods->goods_QRcode_make($v, $pay_qrcode);
            }
        }
        return AjaxReturn($result);
    }

    /**
     * 查询条件下的商品分组列表
     *
     * @return unknown
     */
    public function getGoodsGroupQuery()
    {
        $goodsgroup = new GoodsGroup();
        $text = request()->post("search", "");
        $condition["group_name"] = array(
            'like',
            "%{$text}%"
        );
        $list = $goodsgroup->getGoodsGroupQueryList($condition);
        return $list;
    }

    /**
     * 修改商品名称或促销语
     */
    public function ajaxEditGoodsNameOrIntroduction()
    {
        if (request()->isAjax()) {
            $goods = new GoodsService();
            $goods_id = request()->post("goods_id", "");
            $up_type = request()->post("up_type", "");
            $up_content = request()->post("up_content", "");
            $res = $goods->updateGoodsNameOrIntroduction($goods_id, $up_type, $up_content);
            return AjaxReturn($res);
        }
    }

    /**
     * 虚拟商品类型列表
     */
    public function virtualGoodsTypeList()
    {
        if (request()->isAjax()) {
            
            $virtual_goods = new VirtualGoods();
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $search_name = request()->post("search_name", "");
            $condition = array();
            if (! empty($search_name)) {
                $condition['virtual_goods_type_name'] = array(
                    "like",
                    "%$search_name%"
                );
            }
            $res = $virtual_goods->getVirtualGoodsTypeList($page_index, $page_size, $condition);
            return $res;
        }
        return view($this->style . 'Goods/virtualGoodsTypeList');
    }

    /**
     * 编辑虚拟商品类型
     */
    public function editVirtualGoodsType()
    {
        $virtual_goods = new VirtualGoods();
        $virtual_goods_type_id = request()->get("virtual_goods_type_id", 0);
        if (request()->isAjax()) {
            $virtual_goods_type_id = request()->post("virtual_goods_type_id", 0); // 虚拟商品类型id
            $virtual_goods_group_id = request()->post("virtual_goods_group_id", ""); // 关联虚拟商品分组id
            $virtual_goods_type_name = request()->post("virtual_goods_type_name", ""); // 虚拟商品类型名称
            $validity_period = request()->post("validity_period", 0); // 有效期/天(0表示不限制)
            $is_enabled = request()->post("is_enabled", 1); // 是否开启
            $money = request()->post("money", ""); // 金额
            $config_info = request()->post("config_info", ""); // 配置信息JSON（API接口，参数）
            $confine_use_number = request()->post("confine_use_number", 0); // 限制使用次数
            $res = $virtual_goods->editVirtualGoodsType($virtual_goods_type_id, $virtual_goods_group_id, $virtual_goods_type_name, $validity_period, $is_enabled, $money, $config_info, $confine_use_number);
            return AjaxReturn($res);
        }
        $virtual_goods_type = $virtual_goods->getVirtualGoodsTypeById($virtual_goods_type_id);
        if (! empty($virtual_goods_type)) {
            $virtual_goods_type['config_info'] = json_decode($virtual_goods_type['config_info'], true);
        }
        $this->assign("virtual_goods_type", $virtual_goods_type);
        $this->assign("virtual_goods_type_id", $virtual_goods_type_id);
        
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "goods/virtualgoodstypelist",
                    'menu_name' => "虚拟商品类型",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        
        return view($this->style . "Goods/editVirtualGoodsType");
    }

    /**
     * 删除虚拟商品类型
     */
    public function deleteVirtualGoodsType()
    {
        $virtual_goods = new VirtualGoods();
        $virtual_goods_type_id = request()->post("virtual_goods_type_id", "");
        $res = $virtual_goods->deleteVirtualGoodsType($virtual_goods_type_id);
        return AjaxReturn($res);
    }

    /**
     * 设置虚拟商品类型启用禁用
     *
     * @return boolean
     */
    public function setVirtualGoodsTypeIsEnabled()
    {
        $virtual_goods = new VirtualGoods();
        $virtual_goods_type_id = request()->post("virtual_goods_type_id", "");
        $is_enabled = request()->post("is_enabled", 1);
        $res = $virtual_goods->setVirtualGoodsTypeIsEnabled($virtual_goods_type_id, $is_enabled);
        return AjaxReturn($res);
    }
}