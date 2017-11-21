<?php
/**
 * IGoodsBrand.php
 *
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
namespace data\api;
/**
 * 商品品牌接口
 */
interface IGoodsBrand
{
    /**
     * 获取商品品牌列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
     */
    function getGoodsBrandList($page_index=1, $page_size=0, $condition = '', $order = '', $field = '*');
    
    /**
     * 添加或修改品牌
     * @param unknown $brand_id
     * @param unknown $shop_id
     * @param unknown $brand_name
     * @param unknown $brand_initial
     * @param unknown $brand_class
     * @param unknown $brand_pic
     * @param unknown $brand_recommend
     * @param unknown $sort
     * @param unknown $brand_category_name
     */
    function addOrUpdateGoodsBrand($brand_id, $shop_id, $brand_name, $brand_initial, $brand_class, $brand_pic, $brand_recommend, $sort, $brand_category_name = '', $category_id_array = '', $brand_ads, $category_name, $category_id_1, $category_id_2, $category_id_3);
    
    /**
     * 修改品牌排序号
     * @param unknown $brand_id
     * @param unknown $sort
     */
    function ModifyGoodsBrandSort($brand_id, $sort);
    
    /**
     * 修改品牌推荐
     * @param unknown $brand_id
     * @param unknown $brand_recommend
     */
    function ModifyGoodsBrandRecomend($brand_id, $brand_recommend);
    
    /**
     * 删除品牌
     * @param unknown $brand_id_array
     */
    function deleteGoodsBrand($brand_id_array);
}

?>