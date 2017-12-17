<?php
/**
 * BaseController.php
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

\think\Loader::addNamespace('data', 'data/');
use data\service\AdminUser as User;
use data\service\Config;
use data\service\GoodsCategory;
use data\service\Member as Member;
use data\service\Platform;
use data\service\Config as WebConfig;
use data\service\Shop as ShopService;
use data\service\WebSite as WebSite;
use think\Controller;
use think\Cookie;

class BaseController extends Controller
{

    public $user;

    protected $uid;

    protected $instance_id;

    protected $is_member;

    protected $shop_name;

    protected $user_name;

    public $web_site;

    public $style;
    
    // 验证码配置
    public $login_verify_code;

    public function __construct()
    {
        
        // 请求端（PC端、手机端）
        // getShopCache();//开启缓存
        parent::__construct();
        $this->init();
        
        $default_client = request()->cookie("default_client", "");
        $this->assign("default_client", $default_client);
    }

    /**
     * 功能说明：action基类
     */
    public function init()
    {
        $config = new Config();
        $this->user = new Member();
        $this->web_site = new WebSite();
        $web_info = $this->web_site->getWebSiteInfo();
        
        $this->uid = $this->user->getSessionUid();
        $this->instance_id = $this->user->getSessionInstanceId();
        $this->shop_name = $this->user->getInstanceName();
        $this->assign("uid", $this->uid);
        $this->assign("title", $web_info['title']);
        $this->assign("web_info", $web_info);
        $this->assign("title_before", '');
        
        if (! request()->isAjax()) {
            
            // 弹出框标题
            if (empty($web_info['web_popup_title'])) {
                $this->assign("web_popup_title", "Niushop开源商城");
            } else {
                $this->assign("web_popup_title", $web_info['web_popup_title']);
            }
            
            if ($web_info['web_status'] == 3 && $web_info['wap_status'] == 1) {
                Cookie::delete("default_client");
                $this->redirect(__URL(\think\Config::get('view_replace_str.APP_MAIN')));
            } elseif ($web_info['web_status'] == 2) {
                Cookie::delete("default_client");
                // 首页特殊处理
                $controller = \think\Request::instance()->controller();
                $action = \think\Request::instance()->action();
                if ($controller != 'Index' || $action != 'index') {
                    webClose($web_info['close_reason']);
                }
            } elseif (($web_info['web_status'] == 3 && $web_info['wap_status'] == 3) || ($web_info['web_status'] == 3 && $web_info['wap_status'] == 2)) {
                Cookie::delete("default_client");
                webClose($web_info['close_reason']);
            }
            
            $seoconfig = $config->getSeoConfig($this->instance_id);
            $this->assign("seoconfig", $seoconfig);
            
            $custom_service = $config->getcustomserviceConfig($this->instance_id);
            if (empty($custom_service)) {
                $custom_service['id'] = '';
                $custom_service['value']['service_addr'] = '';
            }
            $this->assign("custom_service", $custom_service);
            // 是否开启验证码
            $this->login_verify_code = $config->getLoginVerifyCodeConfig($this->instance_id);
            $this->assign("login_verify_code", $this->login_verify_code["value"]);
            
            $qq_info = $config->getQQConfig($this->instance_id);
            $Wchat_info = $config->getWchatConfig($this->instance_id);
            $this->assign("qq_info", $qq_info);
            $this->assign("Wchat_info", $Wchat_info);
            $keyword = request()->get('keyword', '');
            $this->assign("keyword", $keyword);
            /* 商品分类查询 */
            $goodsCategory = new GoodsCategory();
            $goods_category_tree = $goodsCategory->getCategoryTreeUseInShopIndex();
            $this->assign('goods_category_one', $goods_category_tree); // 商品分类一级
            
            $this->getCms(); // 底部文章分类列表
            $this->assign("platform_shopname", $this->shop_name);
            
            // 导航
            $nav = new ShopService();
            $navigation_list = $nav->ShopNavigationList(1, 10, [
                'type' => 1
            ], 'sort');
            $this->assign("navigation_list", $navigation_list["data"]);
            $this->getHotkeys(); // 热搜关键词
            
            $this->getPageUrl(); // 分页url拼接
            
            $this->assign('page_num', 5); // 分页显示的页码个数 注：误删不然所有分页都报错必须为奇数
            
            $this->assign('is_head_goods_nav', 0); // 商品分类是否显示样式
        }
        // 获取当前使用的PC端模板
        $use_pc_template = $config->getUsePCTemplate($this->instance_id);
        if (empty($use_pc_template)) {
            $use_pc_template['value'] = 'blue';
        }
        if (! checkTemplateIsExists("shop", $use_pc_template['value'])) {
            $this->error("模板配置有误，请联系商城管理员");
        }
        $this->style = "shop/" . $use_pc_template['value'] . "/";
        $this->assign("style", "shop/" . $use_pc_template['value']);
        $this->getDropDownMenu();
    }

    public function _empty($name)
    {}

    /**
     * 获取导航
     */
    public function getNavigation()
    {
        $nav = new ShopService();
        $list = $nav->ShopNavigationList(1, 0, '', 'sort');
        return $list;
    }

    /**
     * 拼接共用的分页中的url
     */
    public function getPageUrl()
    {
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : ""; // 地址
                                                                                // $path_info = substr($path_info, 6, strlen($path_info));
        $path_info = substr($path_info, 1);
        $get_array = request()->get();
        $query_string = '';
        if (array_key_exists('page', $get_array)) {
            $tag = '&';
        } else {
            if (! empty($get_array)) {
                $tag = '&';
            } else
                $tag = '?';
        }
        foreach ($get_array as $k => $v) {
            if ($k != 'page') {
                $query_string .= $tag . $k . '=' . $v;
            }
        }
        $this->assign('path_info', $path_info);
        $this->assign('query_string', $query_string);
    }

    /**
     * 底部信息
     */
    public function getCms()
    {
        $platform = new Platform();
        $platform_help_class = $platform->getPlatformHelpClassList(1, 5, '', 'sort');
        $this->assign('platform_help_class', $platform_help_class['data']); // 帮助中心分类列表
        
        $platform_help_Document = $platform->getPlatformHelpDocumentList(1, 0, '', 'sort');
        $this->assign('platform_help_document', $platform_help_Document['data']); // 帮助中心列表
    }

    /**
     * 热搜关键词
     */
    public function getHotkeys()
    {
        $config = new Config();
        $hot_keys = $config->getHotsearchConfig($this->instance_id);
        $this->assign("hot_keys", $hot_keys);
        $default_keywords = $config->getDefaultSearchConfig($this->instance_id);
        $this->assign('default_keywords', $default_keywords);
    }

    /**
     * 获取下拉菜单内容
     */
    public function getDropDownMenu()
    {
        $drop_down_menu = array();
        $this->assign("drop_down_menu", $drop_down_menu);
    }

    /**
     * 是否开启虚拟商品功能，0：禁用，1：开启
     */
    public function getIsOpenVirtualGoodsConfig()
    {
        $config = new WebConfig();
        $res = $config->getIsOpenVirtualGoodsConfig($this->instance_id);
        return $res;
    }
}
