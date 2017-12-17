<?php
/**
 * IPlatform.php
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
 * 平台商城相关接口
 *
 */
interface IPlatform
{
    /**
     * 获取友情链接
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
     */
    function getLinkList($page_index=1, $page_size=0, $where='', $order='', $field='*' );
    /**
     * 获取友情链接详情
     * @param unknown $link_id
     */
    function getLinkDetail($link_id);
    /**
     * 添加友情链接
     * @param unknown $link_title
     * @param unknown $link_url
     * @param unknown $link_pic
     * @param unknown $link_sort
     */
    function addLink($link_title, $link_url, $link_pic, $link_sort,$is_blank,$is_show);
    /**
     * 修改友情链接
     * @param unknown $link_id
     * @param unknown $link_title
     * @param unknown $link_url
     * @param unknown $link_pic
     * @param unknown $link_sort
     */
    function updateLink($link_id, $link_title, $link_url, $link_pic, $link_sort,$is_blank,$is_show);
    /**
     * 删除友情链接
     * @param unknown $link_id
     */
    function deleteLink($link_id);
    /**
     * 获取系统广告
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
     */
    function getAdList($page_index=1, $page_size=0, $where='', $order='', $field='*' );
    /**
     * 添加系统广告
     * @param unknown $ad_image
     * @param unknown $link_url
     * @param unknown $sort
     */
    function addAd($ad_image, $link_url, $sort);
    /**
     * 修改商城广告
     * @param unknown $id
     * @param unknown $ad_image
     * @param unknown $link_url
     * @param unknown $sort
     */
    function updateAd($id, $ad_image, $link_url, $sort);
    /**
     * 获取商城广告详情
     * @param unknown $id
     */
    function getAdDetail($id);
    /**
     * 删除商城广告
     * @param unknown $id
     */
    function delAd($id);
    /**
     * 首页板块列表（不含详情）
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
     */
    function webBlockList($page_index=1, $page_size=0, $where='', $order='', $field='*' );
    /**
     * 版块列表 （包含详情）
     */
    function getWebBlockListDetail();
    /**
     * 添加首页板块
     */
    function addWebBlock($is_display, $block_color, $sort, $block_name, $block_short_name, 
          $recommend_ad_image_name, $recommend_ad_image, $recommend_ad_slide_name, $recommend_ad_slide, $recommend_ad_images_name, $recommend_ad_images, $recommend_brands, $recommend_categorys, 
        $recommend_goods_category_name_1, $recommend_goods_category_1, $recommend_goods_category_name_2, $recommend_goods_category_2, $recommend_goods_category_name_3, $recommend_goods_category_3);
    /**
     * 修改首页板块
     */
    function updateWebBlock($block_id, $is_display, $block_color, $sort, $block_name, $block_short_name, 
          $recommend_ad_image_name, $recommend_ad_image, $recommend_ad_slide_name, $recommend_ad_slide, $recommend_ad_images_name, $recommend_ad_images, $recommend_brands, $recommend_categorys, 
        $recommend_goods_category_name_1, $recommend_goods_category_1, $recommend_goods_category_name_2, $recommend_goods_category_2, $recommend_goods_category_name_3, $recommend_goods_category_3);
    /**
     * 删除首页板块
     */
    function deleteWebBlock($block_id);
    /**
     * 获取板块详情
     */
    function getWebBlockDetail($block_id);
    /**
     * 获取广告位列表
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
     */
    function getPlatformAdvPositionList($page_index=1, $page_size=0, $where='', $order='', $field='*' );
    /**
     * 获取广告位详情
     * @param unknown $ap_id
     */
    function getPlatformAdvPositionDetail($ap_id);
    
    /**
     * 删除广告位
     * @param unknown $ap_id
     */
    function delPlatfromAdvPosition($ap_id);
    
    /**
     * 添加广告位
     * @param unknown $ap_name
     * @param unknown $ap_intro
     * @param unknown $ap_class
     * @param unknown $ap_display
     * @param unknown $is_use
     * @param unknown $ap_height
     * @param unknown $ap_width
     * @param unknown $default_content
     */
    function addPlatformAdvPosition($instance_id, $ap_name, $ap_intro, $ap_class, $ap_display, $is_use, $ap_height, $ap_width, $default_content, $ap_background_color, $type, $ap_keyword);
    /**
     * 修改广告位
     * @param unknown $ap_id
     * @param unknown $ap_name
     * @param unknown $ap_intro
     * @param unknown $ap_class
     * @param unknown $ap_display
     * @param unknown $is_use
     * @param unknown $ap_height
     * @param unknown $ap_width
     * @param unknown $default_content
     */
    function updatePlatformAdvPosition($ap_id, $instance_id, $ap_name, $ap_intro, $ap_class, $ap_display, $is_use, $ap_height, $ap_width, $default_content, $ap_background_color, $type, $ap_keyword);
    /**
     * 获取平台广告列表
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
     */
    function getPlatformAdvList($page_index=1, $page_size=0, $where='', $order='', $field='*' );
    /**
     * 获取广告详情
     * @param unknown $adv_id
     */
    function getPlatformAdDetail($adv_id);
    /**
     * 添加平台广告
     * @param unknown $ap_id
     * @param unknown $adv_title
     * @param unknown $adv_url
     * @param unknown $adv_image
     * @param unknown $slide_sort
     */
    function addPlatformAdv($ap_id, $adv_title, $adv_url, $adv_image, $slide_sort, $background, $adv_code);
    /**
     * 修改平台广告
     * @param unknown $adv_id
     * @param unknown $ap_id
     * @param unknown $adv_title
     * @param unknown $adv_url
     * @param unknown $adv_image
     * @param unknown $slide_sort
     */
    function updatePlatformAdv($adv_id, $ap_id, $adv_title, $adv_url, $adv_image, $slide_sort, $background, $adv_code);
    /**
     * 删除平台广告
     * @param unknown $adv_id
     */
    function deletePlatformAdv($adv_id);
    /**
     * 帮助中心类别列表
     * @param number $page_index
     * @param number $page_size
     * @param string $where
     * @param string $order
     * @param string $field
     */
    function getPlatformHelpClassList($page_index=1, $page_size=0, $where='', $order='', $field='*' );
    /**
     * 帮助中心内容列表
     * @param number $page_index
     * @param number $page_size
     * @param string $where
     * @param string $order
     * @param string $field
     */
    function getPlatformHelpDocumentList($page_index=1, $page_size=0, $where='', $order='', $field='*' );
    /**
     * 删除帮助分类
     * @param unknown $class_id
     */
    public function deleteHelpClass($class_id);
    /**
     * 添加帮助中心分类
     * @param unknown $type
     * @param unknown $class_name
     * @param unknown $parent_class_id
     * @param unknown $sort
     */
    function addPlatformHelpClass($type, $class_name, $parent_class_id, $sort);
    /**
     * 修改帮助中心分类
     * @param unknown $class_id
     * @param unknown $type
     * @param unknown $class_name
     * @param unknown $parent_class
     * @param unknown $sort
     */
    function updatePlatformClass($class_id, $type, $class_name, $parent_class_id, $sort);
    /**
     * 根据class_id删除内容
     * @param unknown $class_id
     */
    public function deleteHelpClassTitle($class_id);
    /**
     * 添加帮助中心内容
     * @param unknown $uid
     * @param unknown $class_id
     * @param unknown $title
     * @param unknown $link_url
     * @param unknown $sort
     * @param unknown $content
     * @param unknown $image
     */
    function addPlatformDocument($uid, $class_id, $title, $link_url, $sort, $content, $image);
    /**
     * 删除帮助中心标题
     * @param unknown $id
     */
    public function deleteHelpTitle($id);
    /**
     * 修改帮助中心内容
     * @param unknown $id
     * @param unknown $uid
     * @param unknown $class_id
     * @param unknown $title
     * @param unknown $link_url
     * @param unknown $sort
     * @param unknown $content
     * @param unknown $image
     */
    function updatePlatformDocument($id, $uid, $class_id, $title, $link_url, $sort, $content, $image);
    
    /**
     *  修改帮助中心内容的标题与排序
     * @param unknown $id
     * @param unknown $title
     * @param unknown $sort
     */
    function updatePlatformDocumentTitleAndSort($id, $title, $sort);
    
    /**
     * 获取帮助中心内容详情
     * @param unknown $id
     */
    function getPlatformDocumentDetail($id);
    /**
     * 获取帮助类型细节
     * @param unknown $class_id
     */
    function getPlatformClassDetail($class_id);
    /**
     * 获取平台商品
     * @param number $page_index
     * @param number $page_size
     * @param string $where
     * @param string $order
     */
    function getPlatformGoodsList($page_index=1, $page_size=0, $where='', $order='');
    /**
     * 获取平台商品推荐
     * @param unknown $type 类型1.新品2.精品3.特卖（其他为用户自定义类型）
     */
    function getPlatformGoodsRecommend($type);
    /**
     * 查询商品是否热卖（平台）
     * @param unknown $goods_id
     */
    function getGoodsIshot($goods_id);
    /**
     * 查询商品是否是新品（平台）
     * @param unknown $goods_id
     */
    function getGoodsIsnew($goods_id);
    /**
     * 查询商品是否是精品
     * @param unknown $goods_id
     */
    function getGoodsIsBest($goods_id);
    /**
     * 修改商品推荐类型
     * @param unknown $goods_id
     * @param unknown $type  1.新品2.精品3.热卖  （其他推荐类型根据设定）
     * @param unknown $is_recommend
     */
    function modifyGoodsRecommend($goods_id, $type,$is_recommend);
    /**
     * 获取平台促销板块信息
     */
    function getPlatformGoodsRecommendClass($condition);
    /**
     * 添加促销板块
     * @param unknown $class_name
     * @param unknown $sort
     */
    function addPlatformGoodsRecommendClass($class_name, $sort);
    /**
     * 修改商品促销板块
     * @param unknown $class_id
     * @param unknown $class_name
     * @param unknown $sort
     */
    function updatePlatformGoodsRecommendClass($class_id, $class_name, $sort, $goods_id_array, $show_type);
    /**
     * 修改促销板块排序
     * @param unknown $class_id
     * @param unknown $sort
     */
    function modifyPlatformGoodsRecommendClassSort($class_id,$sort);
    /**
     * 修改促销板块名称
     * @param unknown $class_id
     * @param unknown $class_name
     */
    function modifyPlatformGoodsRecommendClassName($class_id,$class_name);
    /**
     * 获取平台促销板块信息单条信息
     * @param unknown $class_id
     */
    function getPlatformGoodsRecommendClassDetail($class_id);
    /**
     * 删除平台促销板块
     * @param unknown $class_id
     */
    function deletePlatformGoodsRecommendClass($class_id);
    /**
     * 平台统计
     */
    function getAccountCount();
    /**
     * 平台收入日统计
     */
    function  getPlatformAccountMonthRecord();
    /**
     * 
     */
    function getPlatformAccountRecordsList($page_index, $page_size = 0, $condition = '', $order = '');
    /**
     * 平台统计
     * @param unknown $start_date
     * @param unknown $end_date
     */
    function getPlatformCount($start_date, $end_date);
    /**
     * 店铺销售排行
     */
    function getShopSalesVolume($condition);
    /**
     * 商品销售排行
     */
    function getGoodsSalesVolume($condition);
    /**
     * 修改   广告排序
     * @param unknown $adv_id
     * @param unknown $slide_sort
     */
    function updateAdvSlideSort($adv_id, $slide_sort);
    
    /**
     * 设置广告位的启用和禁用
     * @param unknown $ap_id
     */
    function setPlatformAdvPositionUse($ap_id,$is_use);
    
    /**
     * 设置首页版本的显示与不显示
     * @param unknown $block_id
     * @param unknown $is_use
     */
    function setWebBlockIsBlock($block_id,$is_display);
    /**
     * 商品促销
     * @param unknown $shop_id
     */
    function getGoodsGroupRecommend($shop_id);
    /**
     * 修改商品促销
     * @param unknown $shop_id
    */
    function setGoodsGroupRecommend($shop_id, $is_show, $group_id_array);
    /**
     * 首页促销商品列表(pc)
     * @param unknown $shop_id
     */
    function getRecommendGoodsQuery($shop_id);
    /**
     * 首页促销商品列表(web)
     * @param unknown $shop_id
     */
    function getRecommendGoodsList($shop_id, $show_num);
    /**
     * 分页获取公告列表
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @param unknown $field
     */
    function getNoticeList($page_index, $page_size, $condition, $order, $field);
    /**
     * 获取公告详情
     * @param unknown $id
     * @param unknown $shop_id
     */
    function getNoticeDetail($id);
    /**
     * 添加或修改公告
     * @param unknown $notice_title
     * @param unknown $notice_content
     * @param unknown $shop_id
     * @param unknown $sort
     * @param unknown $id
     */
    function addOrModifyNotice($notice_title, $notice_content, $shop_id, $sort, $id);
    /**
     * 删除公告
     * @param unknown $id
     */
    function deleteNotice($id);
    /**
     * 更改公告排序
     * @param unknown $sort
     * @param unknown $id
     */
    function updateNoticeSort($sort, $id);
    
    /**
     * 检测广告位关键字是否存在
     * @param unknown $ap_keyword
     */
    function check_apKeyword_is_exists($ap_keyword);
    
    /**
     * 通过广告位关键字获取广告位详情
     * @param unknown $ap_keyword
     */
    function getPlatformAdvPositionDetailByApKeyword($ap_keyword);
    
    /**
     * 后台获取广告列表
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     */
    function adminGetAdvList($page_index, $page_size, $condition, $order);
}