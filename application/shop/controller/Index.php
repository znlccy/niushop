<?php
/**
 * Index.php
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
namespace app\shop\controller;

use data\model\Send;
use data\service\Article;
use data\service\Goods;
use data\service\GoodsCategory;
use data\service\Platform;
use data\service\Shop;
use think\Cache;
use think\Cookie;

/**
 * 首页控制器
 * 创建人：王永杰
 * 创建时间：2017年2月6日 11:01:19
 */
class Index extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function _empty($name)
    {}

    /*
     * 平台首页
     * 创建人：王永杰
     * 创建时间：2017年2月7日 15:46:26
     *
     * @return \think\response\View
     */
    public function index()
    {
        $default_client = request()->cookie("default_client", "");
        $web_info = $this->web_site->getWebSiteInfo();
        if ($default_client == "shop") {} elseif (request()->isMobile() && $web_info['wap_status'] != 2) {
            $redirect = __URL(__URL__ . "/wap");
            $this->redirect($redirect);
            exit();
        }
        if ($web_info['web_status'] == 2) {
            webClose($web_info['close_reason']);
        }
        
        // 当切换到PC端时，隐藏右下角返回手机端按钮
        if (! request()->isMobile() && $default_client == "shop") {
            $default_client = "";
        }
        // 公告
        $platform = new Platform();
        $notice = $platform->getNoticeList(1, 10, [
            "shop_id" => $this->instance_id
        ], "sort");
        $this->assign("notice", $notice["data"]);
        
        // 文章列表
        $article = new Article();
        $article_list = $article->getArticleList(1, 11, [
            "status" => 2
        ], 'public_time desc');
        $this->assign("article_list", $article_list['data']);
        
        // 楼层版块
        // $this->controlCommendBlock();
        // $web_block_list = $platform->getWebBlockListDetail();
        $this->assign('is_head_goods_nav', 1); // 代表默认显示以及分类
                                               // $this->assign('web_block_list', $web_block_list);
                                               // 楼层版块新
        $good_category = new GoodsCategory();
        $shop_id = $this->instance_id;
        $block_list = $good_category->getGoodsCategoryBlockList($shop_id);
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
        $goods_brand_list = $good_category->getGoodsBrandList(1, 5, [
            "brand_recommend" => 1
        ], '', '');
        $this->assign('block_list', $block_list);
        $this->assign('goods_brand_list', $goods_brand_list);
        
        // 限时折扣
        $goods = new Goods();
        $page = request()->get('page', 1);
        $category_id = request()->get('category_id', 0);
        
        // 获取当前时间
        $current_time = $this->getCurrentTime();
        $this->assign('ms_time', $current_time);
        
        $condition['ng.state'] = 1;
        $condition['status'] = 1;
        if (! empty($category_id)) {
            $condition['category_id_1'] = $category_id;
        }
        $discount_list = $goods->getDiscountGoodsList($page, 5, $condition, 'end_time');
        $assign_get_list = array(
            'page' => $page,
            'page_count' => $discount_list['page_count'], // 总页数
            'total_count' => $discount_list['total_count'], // 总条数
            'discount_list' => $discount_list['data'], // 店铺分页
            'category_id' => $category_id
        ); // 已选中商品分类一级
        foreach ($assign_get_list as $key => $value) {
            $this->assign($key, $value);
        }
        // 商品促销
        $this->getRecommendGoodsList();
        // 友情链接
        $link_list = $platform->getLinkList(1, 0, [
            "is_show" => 1
        ], 'link_sort desc');
        $this->assign("link_list", $link_list["data"]);
        
        return view($this->style . 'Index/index');
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
     * 限时折扣(单独界面)
     * 创建人：王永杰
     * 创建时间：2017年2月7日 17:28:58
     *
     * @return \think\response\View
     */
    public function discount()
    {
        $goods = new Goods();
        $page = request()->get('page', 1);
        $category_id = request()->get('category_id', 0);
        $condition['ng.state'] = 1;
        $condition['status'] = 1;
        if (! empty($category_id)) {
            $condition['category_id_1'] = $category_id;
        }
        $discount_list = $goods->getDiscountGoodsList($page, 20, $condition, 'end_time');
        $assign_get_list = array(
            'page' => $page,
            'page_count' => $discount_list['page_count'], // 总页数
            'total_count' => $discount_list['total_count'], // 总条数
            'discount_list' => $discount_list['data'], // 店铺分页
            'category_id' => $category_id
        ); // 已选中商品分类一级
        foreach ($discount_list['data'] as $k => $v) {
            $sale_down = $v['price'] - $v['promotion_price'];
            // 四舍五入取小数点后两位有效数字
            $sale_price = round($sale_down, 2);
            $discount_list['data'][$k]['sale_down'] = $sale_price;
        }
        
        foreach ($assign_get_list as $key => $value) {
            $this->assign($key, $value);
        }
        $this->assign('is_head_goods_nav', 1); // 代表默认显示以及分类
        $this->assign("title_before", "限时折扣");
        return view($this->style . 'Index/discount');
    }

    /**
     * 平台促销板块信息
     * 任鹏强
     * 2017年2月22日17:56:03
     */
    public function controlCommendBlock()
    {
        $Platform = new Platform();
        $condition = [
            'class_type' => 2,
            'is_use' => 1,
            'show_type' => 0
        ];
        $recommend_block = $Platform->getPlatformGoodsRecommendClass($condition);
        foreach ($recommend_block as $k => $v) {
            // 获取模块下商品
            $goods_list = $Platform->getPlatformGoodsRecommend($v['class_id']);
            if (empty($goods_list)) {
                unset($recommend_block[$k]);
            }
        }
        $this->assign("recommend_block", $recommend_block);
    }

    /**
     * 发送短信
     */
    public function sms($mobile = '18649313172')
    {
        // if(request()->isPost()){
        $Send = new \data\extend\Send();
        $result = $Send->sms([
            'param' => [
                'code' => '123456',
                'time' => '60秒'
            ],
            'mobile' => $mobile,
            'template' => 'SMS_43210099'
        ]);
        if ($result !== true) {
            return $this->error($result);
        }
        return $this->success('短信下发成功！');
        // }
        // return $this->fetch();
    }

    /**
     * 商品促销
     */
    public function getRecommendGoodsList()
    {
        $recommend_goods_list = Cache::get("shop_recommend_goods_list" . $this->instance_id);
        
        if (empty($recommend_goods_list)) {
            $Platform = new Platform();
            $recommend_goods_list = $Platform->getRecommendGoodsQuery($this->instance_id);
            Cache::set("shop_recommend_goods_list" . $this->instance_id, $recommend_goods_list, '300');
        }
        $this->assign("recommend_goods_list", $recommend_goods_list);
    }

    /**
     * 删除设置页面打开cookie
     * 创建时间：2017年10月9日 15:36:11 王永杰
     */
    public function deleteClientCookie()
    {
        Cookie::delete("default_client");
    }

    public function testTag()
    {
        return view($this->style . "Index/testTag");
    }
}