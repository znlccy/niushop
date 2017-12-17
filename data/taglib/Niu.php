<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace data\taglib;

use think\template\TagLib;
use data\service\Goods;
use data\service\GoodsCategory;
use data\service\GoodsBrand;
use data\service\Platform;
use data\service\Article;
use data\service\WebSite;
use data\service\Config;
use data\service\Shop;
use think\Log;

class Niu extends TagLib
{
    /**
     * 定义标签列表
     */
    protected $tags = [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        //专题
        'topicinfo'           => ['attr' => 'id,cache,name', 'close' => 1],//专题详情
        //文章中心
        'articleclasslist'    => ['attr' => 'page,num,where,order,field,cache', 'close' => 1],//分类列表
        'articlelist'         => ['attr' => 'page,num,where,order,field,cache', 'close' => 1],//列表
        'articleinfo'         => ['attr' => 'id,field,cache,name', 'close' => 1],//详情
        //帮助中心
        'helpclasslist'       => ['attr' => 'page,num,where,order,field,cache', 'close' => 1],//帮助中心分类列表
        'helpdocumentlist'    => ['attr' => 'page,num,where,order,field,cache', 'close' => 1],//帮助中心内容列表
        'helpdocumentinfo'    => ['attr' => 'id,field,cache,name', 'close' => 1],//帮助中心内容详情
        //公告
        'noticelist'          => ['attr' => 'page,num,where,order,field,cache', 'close' => 1],//公告列表
        'noticeinfo'          => ['attr' => 'id,field,cache,name', 'close' => 1],//公告详情
        //网站基础信息
        'defaultsearch'       => ['attr' => '', 'close' => 0],//默认搜索
        'hotsearch'           => ['attr' => 'name', 'close' => 1],//热门搜索
        'webname'             => ['attr' => '', 'close' => 0],//网站名称
        'weburl'              => ['attr' => '', 'close' => 0],//官方网址
        'webaddress'          => ['attr' => '', 'close' => 0],//联系地址
        'webqrcode'           => ['attr' => '', 'close' => 0],//网站二维码
        'webdesc'             => ['attr' => '', 'close' => 0],//网站描述
        'weblogo'             => ['attr' => '', 'close' => 0],//网站logo
        'webwechatqrcode'     => ['attr' => '', 'close' => 0],//网站公众号二维码
        'webkeywords'         => ['attr' => '', 'close' => 0],//网站关键字
        'webphone'            => ['attr' => '', 'close' => 0],//网站联系电话
        'webemail'            => ['attr' => '', 'close' => 0],//网站邮箱
        'webqq'               => ['attr' => '', 'close' => 0],//网站qq
        'webwechat'           => ['attr' => '', 'close' => 0],//网站微信号
        'webicp'              => ['attr' => '', 'close' => 0],//网站备案号
        'webclosereason'      => ['attr' => '', 'close' => 0],//网站关闭原因
        'webcount'            => ['attr' => '', 'close' => 0],//网站第三方统计代码
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        'goodslist'           => ['attr' => 'page,num,where,order,field,cache,name', 'close' => 1],//
        'goodsviewlist'       => ['attr' => 'page,num,where,order,field,cache,name', 'close' => 1],//直接查询商品列表
        'memberhistory'       => ['attr' => 'cache,name', 'close' => 1],//会员浏览历史
        'memberlikes'         => ['attr' => 'cache,name', 'close' => 1],//猜你喜欢
        
        
        
        'goodsinfo'           => ['attr' => 'id,field,cache,name', 'close' => 1],
        'categorylist'        => ['attr' => 'page,num,where,order,field,cache', 'close' => 1],
        'categorytree'        => ['attr' => 'page,num,where,order,field,cache', 'close' => 1],
        'brandlist'           => ['attr' => 'page,num,where,order,field,cache', 'close' => 1],
        'brandinfo'           => ['attr' => 'id,field,cache,name', 'close' => 1],

        
        'adv'                 => ['attr' => 'id,field,cache,name', 'close' => 1],
        'blocklist'           => ['attr' => 'page,num,where,order,field,cache', 'close' => 1],//首页版块列表
        'blockinfo'           => ['attr' => 'id,field,cache,name', 'close' => 1],//首页版块详情
        
        'linklist'            => ['attr' => 'page,num,where,order,field,cache', 'close' => 1],//友情链接列表
        'categoryblock'       => ['attr' => 'name,cache,key,item', 'close' => 1],//分类楼层
        'navigation'          => ['attr' => 'page,num,where,order,field,cache', 'close' => 1],
        
        
        //商品列表筛选条件
        'categorypricegrades' => ['attr' => 'id,cache,name', 'close' => 1],//价格区间
        'categorybrands'      => ['attr' => 'id,cache,name', 'close' => 1],//品牌
        
        
    ];
    
    

    
    
    /**
     * 筛选条件-价格区间标签
     */
    public function tagCategorypricegrades($tag, $content)
    {
        $id    = isset($tag['id'])    ? $tag['id']    : '';
        $cache = isset($tag['cache']) ? $tag['cache'] : 0;
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_price_grades';
        
        $service_name = "GoodsCategory";
        $function_array = ['getGoodsCategoryPriceGrades', $id];
        $function = 'getGoodsCategoryPriceGrades("'.$id.'")';
        
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 筛选条件-品牌标签
     */
    public function tagCategorybrands($tag, $content)
    {
        $id    = isset($tag['id'])    ? $tag['id']    : '';
        $cache = isset($tag['cache']) ? $tag['cache'] : 0;
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_price_grades';
        
        $service_name = "GoodsCategory";
        $function_array = ['getGoodsCategoryBrands', $id];
        $function = 'getGoodsCategoryBrands("'.$id.'")';
        
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    
    
    
    
    
    
    
    

    
    
    
    /**
     * 导航标签
     */
    public function tagNavigation($tag, $content)
    {
        $page  = isset($tag['page'])  ? $tag['page']  : '1';
        $num   = isset($tag['num'])   ? $tag['num']   : PAGESIZE;
        $where = isset($tag['where']) ? $tag['where'] : '';
        $order = isset($tag['order']) ? $tag['order'] : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_navigation_list';
        
        $service_name = "Shop";
        $function_array = ['ShopNavigationList', $page, $num, $where, $order];
        $function = 'ShopNavigationList("'.$page.'","'. $num.'", "'.$where.'","'.$order.'")';
        
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }

    /**
     * 商品详情标签
     */
    public function tagGoodsinfo($tag, $content)
    {
        $id    = isset($tag['id'])    ? $tag['id']    : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : 0;
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_goods_info';
        
        $service_name = "Goods";
        $function_array = ['getGoodsDetail', $id, $field];
        $function = 'getGoodsDetail("'.$id.'", "'.$field.'")';
        
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 分类列表标签
     */
    public function tagCategorylist($tag, $content)
    {
        $page  = isset($tag['page'])  ? $tag['page']  : '1';
        $num   = isset($tag['num'])   ? $tag['num']   : PAGESIZE;
        $where = isset($tag['where']) ? $tag['where'] : '';
        $order = isset($tag['order']) ? $tag['order'] : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_category_list';
        
        $service_name = "GoodsCategory";
        $function_array = ['getGoodsCategoryList', $page, $num, $where, $order, $field];
        $function = 'getGoodsCategoryList("'.$page.'","'. $num.'", "'.$where.'","'.$order.'","'.$field.'")';
        
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 分类树标签（最多3级）
     */
    public function tagCategorytree($tag, $content)
    {
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_category_tree';
        
        $service_name = "GoodsCategory";
        $function_array = ['getCategoryTreeUseInShopIndex'];
        $function = 'getCategoryTreeUseInShopIndex()';
        
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }

    
    /**
     * 友情链接列表标签
     */
    public function tagLinklist($tag, $content)
    {
        $page  = isset($tag['page'])  ? $tag['page']  : 1;
        $num   = isset($tag['num'])   ? $tag['num']   : PAGESIZE;
        $where = isset($tag['where']) ? $tag['where'] : '';
        $order = isset($tag['order']) ? $tag['order'] : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_link_list';
        
        $service_name = "Platform";
        $function_array = ['getLinkList', $page, $num, $where, $order, $field];
        $function = 'getLinkList("'.$page.'","'. $num.'", "'.$where.'","'.$order.'","'.$field.'")';
        
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 广告标签
     */
    public function tagAdv($tag, $content)
    {
        $id    = isset($tag['id'])    ? $tag['id']    : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_adv';
        
        $service_name = "Platform";
        $function_array = ['getPlatformAdvPositionDetail', $id, $field];
        $function = 'getPlatformAdvPositionDetail("'.$id.'", "'.$field.'")';
        
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 首页列表标签
     */
    public function tagBlocklist($tag, $content)
    {
        $page  = isset($tag['page'])  ? $tag['page']  : '1';
        $num   = isset($tag['num'])   ? $tag['num']   : PAGESIZE;
        $where = isset($tag['where']) ? $tag['where'] : '';
        $order = isset($tag['order']) ? $tag['order'] : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_block_list';
        
        $service_name = "Platform";
        $function_array = ['webBlockList', $page, $num, $where, $order, $field];
        $function = 'webBlockList("'.$page.'","'. $num.'", "'.$where.'","'.$order.'","'.$field.'")';
        
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 首页版块详情标签
     */
    public function tagBlockinfo($tag, $content)
    {
        $id    = isset($tag['id'])    ? $tag['id']    : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_block';
        
        $service_name = "Platform";
        $function_array = ['getWebBlockDetail', $id, $field];
        $function = 'getWebBlockDetail("'.$id.'", "'.$field.'")';
        
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }

   
    /**
     * 网站名称标签
     */
    public function tagWebname($tag){
        $web_site = new WebSite();
        $data = $web_site->getWebSiteInfo();
        return $data['title'];
    }
    /**
     * 官方网址标签
     */
    public function tagWeburl($tag, $content){
        $web_site = new WebSite();
        $data = $web_site->getWebSiteInfo();
        return $data['web_url'];
    }
    /**
     * 联系地址标签
     */
    public function tagWebaddress($tag, $content){
        $web_site = new WebSite();
        $data = $web_site->getWebSiteInfo();
        return $data['web_address'];
    }
    /**
     * 网站二维码标签
     */
    public function tagWebqrcode($tag, $content){
        
    }
    /**
     * 网站描述标签
     */
    public function tagWebdesc($tag, $content){
        $web_site = new WebSite();
        $data = $web_site->getWebSiteInfo();
        return $data['web_desc'];
    }
    /**
     * 网站logo标签
     */
    public function tagWeblogo($tag, $content){
        $web_site = new WebSite();
        $data = $web_site->getWebSiteInfo();
        return $data['logo'];
    }
    /**
     * 网站公众号二维码标签
     */
    public function tagWebwechatqrcode($tag, $content){
        $web_site = new WebSite();
        $data = $web_site->getWebSiteInfo();
        return $data['web_qrcode'];
    }
    /**
     * 网站关键字
     */
    public function tagWebkeywords($tag, $content){
        $web_site = new WebSite();
        $data = $web_site->getWebSiteInfo();
        return $data['key_words'];
    }
    /**
     * 网站联系电话
     */
    public function tagWebphone($tag, $content){
        $web_site = new WebSite();
        $data = $web_site->getWebSiteInfo();
        return $data['web_phone'];
    }
    /**
     * 网站邮箱
     */
    public function tagWebemail($tag, $content){
        $web_site = new WebSite();
        $data = $web_site->getWebSiteInfo();
        return $data['web_email'];
    }
    /**
     * 网站qq
     */
    public function tagWebqq($tag, $content){
        $web_site = new WebSite();
        $data = $web_site->getWebSiteInfo();
        return $data['web_qq'];
    }
    /**
     * 网站微信号
     */
    public function tagWebwechat($tag, $content){
        $web_site = new WebSite();
        $data = $web_site->getWebSiteInfo();
        return $data['web_weixin'];
    }
    /**
     * 网站备案号
     */
    public function tagWebicp($tag, $content){
        $web_site = new WebSite();
        $data = $web_site->getWebSiteInfo();
        return $data['web_icp'];
    }
    /**
     * 网站关闭原因
     */
    public function tagWebclosereason($tag, $content){
        $web_site = new WebSite();
        $data = $web_site->getWebSiteInfo();
        return $data['close_reason'];
    }
    /**
     * 网站第三方统计代码
     */
    public function tagWebcount($tag, $content){
        $web_site = new WebSite();
        $data = $web_site->getWebSiteInfo();
        return $data['third_count'];
    }
    /**
     * 默认搜索
     */
    public function tagDefaultsearch($tag, $content){
        $config = new Config();
        $default_keywords = $config->getDefaultSearchConfig(0);
        return $default_keywords;
    }
    /**
     * 热门搜索
     */
    public function tagHotsearch($tag, $content){
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_hot_search';
        $config = new Config();
        $hot_keys = $config->getHotsearchConfig(0);
        return $this->loadContent($hot_keys, $name, $content);
    }
    /**
     * 分类楼层
     */
    public function tagCategoryblock($tag, $content)
    {
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_category_block';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        if($cache !== ''){
            $goods_category = new GoodsCategory();
            $block_list = $goods_category->getGoodsCategoryBlockList(0);
            if (! empty($block_list)) {
                foreach ($block_list as $k => $v) {
                    if (! empty($v['ad_picture'])) {
                        $block_list[$k]['ad_list'] = json_decode($v['ad_picture'], true);
                    }
                    if ($v['ad_picture'] == "" && empty($v['brand_list'])) {
                        $block_list[$k]["block_width"] = 100;
                        $block_list[$k]["goods_block_width"] = 19.80;
                        $block_list[$k]["goods_num"] = 10;
                    } elseif (($v['ad_picture'] != "" && empty($v['brand_list'])) || ($v['ad_picture'] == "" && ! empty($v['brand_list']))) {
                        $block_list[$k]["block_width"] = 80;
                        $block_list[$k]["goods_block_width"] = 24.85;
                        $block_list[$k]["goods_num"] = 8;
                    } else {
                        $block_list[$k]["block_width"] = 60;
                        $block_list[$k]["goods_block_width"] = 33.15;
                        $block_list[$k]["goods_num"] = 6;
                    }
                }
            }
        }else{
            $cache_data = cache("TAG_CATRGORYBLOCK");
            if($cache_data){
                $data = $cache_data;
            }else{
                $goods_category = new GoodsCategory();
                $block_list = $goods_category->getGoodsCategoryBlockList(0);
                if (! empty($block_list)) {
                    foreach ($block_list as $k => $v) {
                        if (! empty($v['ad_picture'])) {
                            $block_list[$k]['ad_list'] = json_decode($v['ad_picture'], true);
                        }
                        if ($v['ad_picture'] == "" && empty($v['brand_list'])) {
                            $block_list[$k]["block_width"] = 100;
                            $block_list[$k]["goods_block_width"] = 19.80;
                            $block_list[$k]["goods_num"] = 10;
                        } elseif (($v['ad_picture'] != "" && empty($v['brand_list'])) || ($v['ad_picture'] == "" && ! empty($v['brand_list']))) {
                            $block_list[$k]["block_width"] = 80;
                            $block_list[$k]["goods_block_width"] = 24.85;
                            $block_list[$k]["goods_num"] = 8;
                        } else {
                            $block_list[$k]["block_width"] = 60;
                            $block_list[$k]["goods_block_width"] = 33.15;
                            $block_list[$k]["goods_num"] = 6;
                        }
                    }
                }
                cache("TAG_CATRGORYBLOCK", $block_list, $cache);
            }
        }
        return $this->loadContent($block_list, $name, $content);
    }
    
    /**
     * 组装非封闭标签数据
     * @param unknown $data
     * @param unknown $name
     * @param unknown $content
     */
    protected function loadContent($data, $name, $content)
    {
        $parse  = '<?php ';
        $parse .= "\${$name} ='".json_encode($data)."';";
        $parse .= "\${$name} =json_decode(\${$name}, ture);";
        $parse .= '?>';
        $parse .= $content;
        return $parse;
    }
    
    
    
/***************分界线*********分界线**********分界线******分界线********分界线****分界线******分界线*****分界线*********分界线**************************/
    
    /**
     * 帮助中心分类列表标签
     */
    public function tagHelpclasslist($tag, $content)
    {
        $page  = isset($tag['page'])  ? $tag['page']  : '1';
        $num   = isset($tag['num'])   ? $tag['num']   : PAGESIZE;
        $where = isset($tag['where']) ? $tag['where'] : '';
        $order = isset($tag['order']) ? $tag['order'] : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_helpclass_list';
    
        $service_name = "Platform";
        $function_array = ['getPlatformHelpClassList', $page, $num, $where, $order, $field];
        $function = 'getPlatformHelpClassList("'.$page.'","'. $num.'", "'.$where.'","'.$order.'", "'.$field.'")';
    
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 帮助中心内容列表标签
     */
    public function tagHelpdocumentlist($tag, $content)
    {
        $page  = isset($tag['page'])  ? $tag['page']  : 1;
        $num   = isset($tag['num'])   ? $tag['num']   : PAGESIZE;
        $where = isset($tag['where']) ? $tag['where'] : '';
        $order = isset($tag['order']) ? $tag['order'] : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_helpdocument_list';
    
        $service_name = "Platform";
        $function_array = ['getPlatformHelpDocumentList', $page, $num, $where, $order, $field];
        $function = 'getPlatformHelpDocumentList("'.$page.'","'. $num.'", "'.$where.'","'.$order.'", "'.$field.'")';
    
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 帮助中心详情标签
     */
    public function tagHelpdocumentinfo($tag, $content)
    {
        $id    = isset($tag['id'])    ? $tag['id']    : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_document';
    
        $service_name = "Platform";
        $function_array = ['getPlatformDocumentDetail', $id, $field];
        $function = 'getPlatformDocumentDetail("'.$id.'", "'.$field.'")';
    
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 公告列表标签
     */
    public function tagNoticelist($tag, $content)
    {
        $page  = isset($tag['page'])  ? $tag['page']  : '1';
        $num   = isset($tag['num'])   ? $tag['num']   : PAGESIZE;
        $where = isset($tag['where']) ? $tag['where'] : '';
        $order = isset($tag['order']) ? $tag['order'] : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_notice_list';
    
        $service_name = "Platform";
        $function_array = ['getNoticeList', $page, $num, $where, $order, $field];
        $function = 'getNoticeList("'.$page.'","'. $num.'", "'.$where.'","'.$order.'", "'.$field.'")';
    
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 公告详情标签
     */
    public function tagNoticeinfo($tag, $content)
    {
        $id    = isset($tag['id'])    ? $tag['id']    : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_document';
    
        $service_name = "Platform";
        $function_array = ['getNoticeList', $id, $field];
        $function = 'getNoticeDetail("'.$id.'", "'.$field.'")';
    
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 文章分类列表标签
     */
    public function tagArticleclasslist($tag, $content)
    {
        $page  = isset($tag['page'])  ? $tag['page']  : '1';
        $num   = isset($tag['num'])   ? $tag['num']   : PAGESIZE;
        $where = isset($tag['where']) ? $tag['where'] : '';
        $order = isset($tag['order']) ? $tag['order'] : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_articleclass_list';
    
        $service_name = "Article";
        $function_array = ['getArticleClass', $page, $num, $where, $order];
        $function = 'getArticleClass("'.$page.'","'. $num.'", "'.$where.'","'.$order.'")';
    
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 文章列表标签
     */
    public function tagArticlelist($tag, $content)
    {
        $page  = isset($tag['page'])  ? $tag['page']  : '1';
        $num   = isset($tag['num'])   ? $tag['num']   : PAGESIZE;
        $where = isset($tag['where'])   ? $tag['where']   : '';
        $order = isset($tag['order']) ? $tag['order'] : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_article_list';
        $service_name = "Article";
        $function_array = ['getArticleList', $page, $num, $where, $order];
        $function = 'getArticleList("'.$page.'","'. $num.'", "'.$where.'","'.$order.'")';
    
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 文章中心详情
     */
    public function tagArticleinfo($tag, $content)
    {
        $id    = isset($tag['id'])    ? $tag['id']    : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_document';
    
        $service_name = "Article";
        $function_array = ['getArticleDetail', $id, $field];
        $function = 'getArticleDetail("'.$id.'", "'.$field.'")';
    
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 专题详情
     */
    public function tagTopicinfo($tag, $content)
    {
        $id    = isset($tag['id'])    ? $tag['id']    : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_document';
    
        $service_name = "Article";
        $function_array = ['getTopicDetail', $id, $field];
        $function = 'getTopicDetail("'.$id.'", "'.$field.'")';
    
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 猜你喜欢标签
     */
    public function tagmemberlikes($tag, $content)
    {
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_member_history';
    
        $service_name = "Member";
        $function_array = ['getGuessMemberLikes'];
        $function = 'getGuessMemberLikes()';
    
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 浏览历史标签
     */
    public function tagMemberhistory($tag, $content)
    {
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_member_history';
    
        $service_name = "Member";
        $function_array = ['getMemberViewHistory'];
        $function = 'getMemberViewHistory()';
    
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 商品列表标签
     */
    public function tagGoodslist($tag, $content)
    {
        $page  = isset($tag['page'])  ? $tag['page']  : '1';
        $num   = isset($tag['num'])   ? $tag['num']   : PAGESIZE;
        $where = isset($tag['where']) ? $tag['where'] : '';
        $order = isset($tag['order']) ? $tag['order'] : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_goods_list';
    
        $service_name = "Goods";
        $function_array = ['getGoodsList', $page, $num, $where, $order];
        $function = 'getGoodsList("'.$page.'","'. $num.'", "'.$where.'","'.$order.'")';
    
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 品牌列表标签
     */
    public function tagBrandlist($tag, $content)
    {
        $page  = isset($tag['page'])  ? $tag['page']  : '1';
        $num   = isset($tag['num'])   ? $tag['num']   : PAGESIZE;
        $where = isset($tag['where']) ? $tag['where'] : '';
        $order = isset($tag['order']) ? $tag['order'] : '';
        $field = isset($tag['field']) ? $tag['field'] : '*';
        $cache = isset($tag['cache']) ? $tag['cache'] : '';
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_brand_list';
    
        $service_name = "GoodsBrand";
        $function_array = ['getGoodsBrandList', $page, $num, $where, $order, $field];
        $function = 'getGoodsBrandList("'.$page.'","'. $num.'", "'.$where.'","'.$order.'","'.$field.'")';
    
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 品牌详情标签
     */
    public function tagBrandinfo($tag, $content)
    {
        $id    = isset($tag['id'])    ? $tag['id']    : '';
        $cache = isset($tag['cache']) ? $tag['cache'] : 0;
        $name  = isset($tag['name'])  ? $tag['name']  : 'niu_price_grades';
    
        $service_name = "GoodsBrand";
        $function_array = ['getGoodsBrandInfo', $id];
        $function = 'getGoodsBrandInfo("'.$id.'")';
    
        return $this->loadPageListContent($name, $content, $cache, $service_name, $function, $function_array);
    }
    /**
     * 组装返回代码
     */
    protected function loadPageListContent($name, $content, $cache, $service_name, $function, $function_array){
        $parse .= '<?php ';
        if($cache === ''){
            $parse .= '$service = new data\service\\'.$service_name.';';
            $parse .= '$'.$name.' = $service->'.$function.';';
            $parse .= '$'.$name.' = json_encode($'.$name.');';
            $parse .= '$'.$name.' = json_decode($'.$name.', ture);';
        }else{
            $parse .= '$tag_md5 = json_encode('.json_encode($function_array).');';
            $parse .= 'if(cache("TAG_".md5($tag_md5))):';
            $parse .= '$'.$name.' = cache("TAG_".md5($tag_md5));';
            $parse .= 'else:';
            $parse .= '$service = new data\service\\'.$service_name.';';
            $parse .= '$'.$name.' = $service->'.$function.';';
            $parse .= '$'.$name.' = json_encode($'.$name.');';
            $parse .= '$'.$name.' = json_decode($'.$name.', ture);';
            $parse .= 'cache("TAG_".md5($tag_md5), $'.$name.', '.$cache.');';
            $parse .= 'endif;';
        }
        $parse .= ' ?>';
        $parse .= $content;
        return $parse;
    }
}