<?php
/**
 * Config.php
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

use app\api\controller\User;
use data\extend\Send;
use data\service\Address as DataAddress;
use data\service\Config as WebConfig;
use data\service\GoodsCategory;
use data\service\Platform;
use data\service\Promotion;
use data\service\Shop as Shop;
use data\service\Upgrade;
use Qiniu\json_decode;
use data\service\WebSite;

/**
 * 网站设置模块控制器
 *
 * @author Administrator
 *        
 */
class Config extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 网站设置
     */
    public function webConfig()
    {
        if (request()->isPost()) {
            // 网站设置
            $title = request()->post('title', ''); // 网站标题
            $logo = request()->post('logo', ''); // 网站logo
            $web_desc = request()->post('web_desc', ''); // 网站描述
            $key_words = request()->post('key_words', ''); // 网站关键字
            $web_icp = request()->post('web_icp', ''); // 网站备案号
            $web_style_pc = 1; // request()->post('web_style_pc', ''); // 前台网站风格 已废弃，改为读取配置
            $web_style_admin = request()->post('web_style_admin', ''); // 后台网站风格
            $visit_pattern = request()->post('visit_pattern', '');
            $web_qrcode = request()->post('web_qrcode', ''); // 网站公众号二维码
            $web_url = request()->post('web_url', ''); // 店铺网址
            $web_phone = request()->post('web_phone', ''); // 网站联系方式
            $web_email = request()->post('web_email', ''); // 网站邮箱
            $web_qq = request()->post('web_qq', ''); // 网站qq号
            $web_weixin = request()->post('web_weixin', ''); // 网站微信号
            $web_address = request()->post('web_address', ''); // 网站联系地址
            $web_popup_title = request()->post("web_popup_title", ""); // 网站弹出框标题
            
            $web_status = request()->post("web_status", ''); // 网站运营状态
            $wap_status = request()->post("wap_status", ''); // 手机端网站运营状态
            $third_count = request()->post("third_count", ''); // 第三方统计
            $close_reason = request()->post("close_reason", ''); // 站点关闭原因
            $web_wechat_share_logo = request()->post("web_wechat_share_logo", ""); // 网站微信分享logo
            $web_gov_record = request()->post("web_gov_record", ""); // 公安网备信息
            $web_gov_record_url = request()->post("web_gov_record_url", ""); // 公安网备链接
            
            $retval = $this->website->updateWebSite($title, $logo, $web_desc, $key_words, $web_icp, $web_style_pc, $web_style_admin, $visit_pattern, $web_qrcode, $web_url, $web_phone, $web_email, $web_qq, $web_weixin, $web_address, $web_status, $wap_status, $third_count, $close_reason, $web_popup_title, $web_wechat_share_logo, $web_gov_record, $web_gov_record_url);
            return AjaxReturn($retval);
        } else {
            
            $child_menu_list = array(
                array(
                    'url' => "config/webconfig",
                    'menu_name' => "网站设置",
                    "active" => 1
                ),
                array(
                    'url' => "config/seoConfig",
                    'menu_name' => "SEO设置",
                    "active" => 0
                ),
                array(
                    'url' => "config/codeconfig",
                    'menu_name' => "验证码设置",
                    "active" => 0
                ),
                array(
                    'url' => "config/shopset",
                    'menu_name' => "购物设置",
                    "active" => 0
                ),
                array(
                    'url' => "config/expressmessage",
                    'menu_name' => "物流跟踪设置",
                    "active" => 0
                ),
                array(
                    'url' => "config/copyrightinfo",
                    'menu_name' => "版权设置",
                    "active" => 0
                )
            );
            
            $this->assign('child_menu_list', $child_menu_list);
            $list = $this->website->getWebSiteInfo();
            $style_list_pc = $this->website->getWebStyleList([
                'type' => 1
            ]); // 前台网站风格
            $style_list_admin = $this->website->getWebStyleList([
                'type' => 2
            ]); // 后台网站风格
            $path = "";
            $path = getQRcode(__URL(__URL__), 'upload/qrcode', 'url');
            $this->assign('style_list_pc', $style_list_pc);
            $this->assign('style_list_admin', $style_list_admin);
            $this->assign("website", $list);
            $this->assign("qrcode_path", $path);
            return view($this->style . "Config/webConfig");
        }
    }

    /**
     * seo设置
     */
    public function seoConfig()
    {
        $child_menu_list = array(
            array(
                'url' => "config/webconfig",
                'menu_name' => "网站设置",
                "active" => 0
            ),
            array(
                'url' => "config/seoConfig",
                'menu_name' => "SEO设置",
                "active" => 1
            ),
            array(
                'url' => "config/codeconfig",
                'menu_name' => "验证码设置",
                "active" => 0
            ),
            array(
                'url' => "config/shopset",
                'menu_name' => "购物设置",
                "active" => 0
            ),
            array(
                'url' => "config/expressmessage",
                'menu_name' => "物流跟踪设置",
                "active" => 0
            ),
            array(
                'url' => "config/copyrightinfo",
                'menu_name' => "版权设置",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        
        $Config = new WebConfig();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $seo_title = request()->post("seo_title", '');
            $seo_meta = request()->post("seo_meta", '');
            $seo_desc = request()->post("seo_desc", '');
            $seo_other = request()->post("seo_other", '');
            $retval = $Config->SetSeoConfig($shop_id, $seo_title, $seo_meta, $seo_desc, $seo_other);
            return AjaxReturn($retval);
        } else {
            $shop_id = $this->instance_id;
            $shopSet = $Config->getSeoConfig($shop_id);
            $this->assign("info", $shopSet);
        }
        return view($this->style . "Config/seoConfig");
    }

    /**
     * 版权设置
     */
    public function copyrightinfo()
    {
        $child_menu_list = array(
            array(
                'url' => "config/webconfig",
                'menu_name' => "网站设置",
                "active" => 0
            ),
            array(
                'url' => "config/seoConfig",
                'menu_name' => "SEO设置",
                "active" => 0
            ),
            array(
                'url' => "config/codeconfig",
                'menu_name' => "验证码设置",
                "active" => 0
            ),
            array(
                'url' => "config/shopset",
                'menu_name' => "购物设置",
                "active" => 0
            ),
            array(
                'url' => "config/expressmessage",
                'menu_name' => "物流跟踪设置",
                "active" => 0
            ),
            array(
                'url' => "config/copyrightinfo",
                'menu_name' => "版权设置",
                "active" => 1
            )
        );
        $this->assign('child_menu_list', $child_menu_list);
        $Config = new WebConfig();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $copyright_logo = request()->post("copyright_logo", '');
            $copyright_meta = request()->post("copyright_meta", '');
            $copyright_link = request()->post("copyright_link", '');
            $copyright_desc = request()->post("copyright_desc", '');
            $copyright_companyname = request()->post("copyright_companyname", '');
            
            $upgrade = new Upgrade();
            $is_load = $upgrade->isLoadCopyRight();
            if ($is_load == 0) {
                
                $retval = $Config->SetCopyrightConfig($shop_id, $copyright_logo, $copyright_meta, $copyright_link, $copyright_desc, $copyright_companyname);
                return AjaxReturn($retval);
            } else {
                $rs = [
                    'code' => 0,
                    'message' => "未授权用户无法编辑版权信息"
                ];
                return $rs;
            }
        } else {
            $shop_id = $this->instance_id;
            $shopSet = $Config->getCopyrightConfig($shop_id);
            $this->assign("info", $shopSet);
        }
        
        return view($this->style . "Config/copyrightinfo");
    }

    /**
     * qq登录配置
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function loginQQConfig()
    {
        $appkey = request()->post('appkey', '');
        $appsecret = request()->post('appsecret', '');
        $url = request()->post('url', '');
        $call_back_url = request()->post('call_back_url', '');
        $is_use = request()->post('is_use', 0);
        $web_config = new WebConfig();
        // 获取数据
        $retval = $web_config->setQQConfig($this->instance_id, $appkey, $appsecret, $url, $call_back_url, $is_use);
        return AjaxReturn($retval);
    }

    /**
     * 微信登录配置
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function loginWeixinConfig()
    {
        $appid = request()->post('appkey', '');
        $appsecret = request()->post('appsecret', '');
        $url = request()->post('url', '');
        $call_back_url = request()->post('call_back_url', '');
        $is_use = request()->post('is_use', 0);
        $web_config = new WebConfig();
        // 获取数据
        $retval = $web_config->setWchatConfig($this->instance_id, $appid, $appsecret, $url, $call_back_url, $is_use);
        return AjaxReturn($retval);
    }

    /**
     * 第三方登录 页面显示
     */
    public function loginConfig()
    {
        $type = request()->get('type', 'qq');
        if ($type == "qq") {
            $child_menu_list = array(
                array(
                    'url' => "config/loginconfig?type=qq",
                    'menu_name' => "QQ登录",
                    "active" => 1
                )
            );
        } else {
            $child_menu_list = array(
                array(
                    'url' => "config/loginconfig?type=wchat",
                    'menu_name' => "微信登录",
                    "active" => 1
                )
            );
        }
        $this->assign('child_menu_list', $child_menu_list);
        $this->assign("type", $type);
        $web_config = new WebConfig();
        // qq登录配置
        // 获取当前域名
        $domain_name = \think\Request::instance()->domain();
        // 获取回调域名qq回调域名
        $qq_call_back = __URL(__URL__ . '/wap/login/callback');
        // 获取qq配置信息
        $qq_config = $web_config->getQQConfig($this->instance_id);
        $qq_config['value']["AUTHORIZE"] = $domain_name;
        $qq_config['value']["CALLBACK"] = $qq_call_back;
        $this->assign("qq_config", $qq_config);
        // 微信登录配置
        // 微信登录返回
        $wchat_call_back = __URL(__URL__ . '/wap/login/callback');
        $wchat_config = $web_config->getWchatConfig($this->instance_id);
        $wchat_config['value']["AUTHORIZE"] = $domain_name;
        $wchat_config['value']["CALLBACK"] = $wchat_call_back;
        $this->assign("wchat_config", $wchat_config);
        
        return view($this->style . "Config/loginConfig");
    }

    /**
     * 支付配置--微信
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function payConfig()
    {
        $web_config = new WebConfig();
        if (request()->isAjax()) {
            $type = request()->post('type', '');
            if ($type == 'wchat') {
                // 微信支付
                $appkey = str_replace(' ', '', request()->post('appkey', ''));
                $appsecret = str_replace(' ', '', request()->post('appsecret', ''));
                $paySignKey = str_replace(' ', '', request()->post('paySignKey', ''));
                $MCHID = str_replace(' ', '', request()->post('MCHID', ''));
                $is_use = request()->post('is_use', 0);
                // 获取数据
                $retval = $web_config->setWpayConfig($this->instance_id, $appkey, $appsecret, $MCHID, $paySignKey, $is_use);
                return AjaxReturn($retval);
            }
        } else {
            $type = request()->get('type', 'wchat');
            if ($type == 'wchat') {
                $data = $web_config->getWpayConfig($this->instance_id);
                $this->assign("config", $data);
                // 获取当前域名
                $this->assign('root_url', __URL(__URL__ . "/wap/pay"));
                return view($this->style . "Config/payConfig");
            }
        }
    }

    /**
     * 支付宝配置
     */
    public function payAliConfig()
    {
        $web_config = new WebConfig();
        if (request()->isAjax()) {
            // 支付宝
            $partnerid = str_replace(' ', '', request()->post('partnerid', ''));
            $seller = str_replace(' ', '', request()->post('seller', ''));
            $ali_key = str_replace(' ', '', request()->post('ali_key', ''));
            $is_use = request()->post('is_use', 0);
            // 获取数据
            $retval = $web_config->setAlipayConfig($this->instance_id, $partnerid, $seller, $ali_key, $is_use);
            return AjaxReturn($retval);
        }
        $data = $web_config->getAlipayConfig($this->instance_id);
        $this->assign("config", $data);
        return view($this->style . "Config/payAliConfig");
    }

    /**
     * 设置微信和支付宝开关状态是否启用
     */
    public function setStatus()
    {
        $web_config = new WebConfig();
        if (request()->isAjax()) {
            $is_use = request()->post("is_use", '');
            $type = request()->post("type", '');
            $retval = $web_config->setWpayStatusConfig($this->instance_id, $is_use, $type);
            return AjaxReturn($retval);
        }
    }

    /**
     * 广告列表
     */
    public function shopAdList()
    {
        if (request()->isAjax()) {
            $shop_ad = new Shop();
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $list = $shop_ad->getShopAdList($page_index, $page_size, [
                'shop_id' => $this->instance_id
            ], 'sort');
            return $list;
        }
        return view($this->style . "Config/shopAdList");
    }

    /**
     * 添加店铺广告
     *
     * @return \think\response\View
     */
    public function addShopAd()
    {
        if (request()->isAjax()) {
            $ad_image = request()->post('ad_image', '');
            $link_url = request()->post('link_url', '');
            $sort = request()->post('sort', 0);
            $type = request()->post('type', 0);
            $background = request()->post('background', '#FFFFFF');
            $shop_ad = new Shop();
            $res = $shop_ad->addShopAd($ad_image, $link_url, $sort, $type, $background);
            return AjaxReturn($res);
        }
        return view($this->style . "Config/addShopAd");
    }

    /**
     * 修改店铺广告
     */
    public function updateShopAd()
    {
        $shop_ad = new Shop();
        if (request()->isAjax()) {
            $id = request()->post('id', '');
            $ad_image = request()->post('ad_image', '');
            $link_url = request()->post('link_url', '');
            $sort = request()->post('sort', 0);
            $type = request()->post('type', 0);
            $background = request()->post('background', '#FFFFFF');
            $res = $shop_ad->updateShopAd($id, $ad_image, $link_url, $sort, $type, $background);
            return AjaxReturn($res);
        }
        $id = request()->get('id', '');
        if (! is_numeric($id)) {
            $this->error('未获取到信息');
        }
        $info = $shop_ad->getShopAdDetail($id);
        $this->assign('info', $info);
        return view($this->style . "Config/updateShopAd");
    }

    public function delShopAd()
    {
        $id = request()->post('id', '');
        $res = 0;
        if (! empty($id)) {
            $shop_ad = new Shop();
            $res = $shop_ad->delShopAd($id);
        }
        return AjaxReturn($res);
    }

    /**
     * 店铺导航列表
     */
    public function shopNavigationList()
    {
        if (request()->isAjax()) {
            $shop = new Shop();
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $list = $shop->ShopNavigationList($page_index, $page_size, '', 'sort');
            return $list;
        } else {
            $this->pcConfigChildMenuList(1);
            return view($this->style . "Config/shopNavigationList");
        }
    }

    /**
     * PC端模板列表
     * 创建时间：2017年9月1日 16:47:22
     * 王永杰
     */
    public function pcTemplate()
    {
        $this->pcConfigChildMenuList(8);
        $this->getCollatingTemplateList("shop");
        $config = new WebConfig();
        $use_template = $config->getUsePCTemplate($this->instance_id);
        $value = "blue";
        if (! empty($use_template)) {
            $value = $use_template['value'];
        } else {
            $config->setUsePCTemplate($this->instance_id, "blue");
            $this->updateTemplateUse("shop", "blue");
        }
        $this->assign("use_template", $value);
        return view($this->style . "Config/pcTemplate");
    }

    /**
     * 根据文件夹选择xml配置文件集合
     * 创建时间：2017年9月4日 19:34:34
     *
     * @param unknown $folder
     *            文件夹：shop(pc端),wap(手机端)
     */
    public function getTemplateXmlList($folder)
    {
        $file_path = str_replace("\\", "/", ROOT_PATH . 'template/' . $folder);
        $config_list = $this->getfiles($file_path);
        return $config_list;
    }

    /**
     * 根据文件夹获取整理后的模板集合
     * 创建时间：2017年9月4日 19:36:20
     * 王永杰
     *
     * @param unknown $folder
     *            文件夹：shop(pc端),wap(手机端)
     */
    public function getCollatingTemplateList($folder)
    {
        $config_list = $this->getTemplateXmlList($folder);
        
        $xmlTag = array(
            'folder',
            'theme',
            'preview',
            'introduce'
        );
        switch ($folder) {
            case "shop":
                // XML标签配置，PC端专属属性
                array_push($xmlTag, "bgcolor");
                break;
            case "wap":
                break;
        }
        $xml = new \DOMDocument();
        $template_list = array();
        $template_count = count($config_list); // 模板数量
                                               
        // $not_readable_list = array(); // 文件不可读数量
                                               
        // $not_writeable_list = array(); // 文件不可写数量
        
        foreach ($config_list as $k => $config) {
            if ($config['is_readable']) {
                
                // 获取xml文件内容
                $xml_txt = fopen($config['xml_path'], "r,w");
                $xml_str = fread($xml_txt, filesize($config['xml_path'])); // 指定读取大小，这里把整个文件内容读取出来
                $xml_text = str_replace("\r\n", "<br />", $xml_str);
                $xml->loadXML($xml_text);
                $template = $xml->getElementsByTagName('template'); // 最外层节点
                foreach ($template as $p) {
                    foreach ($xmlTag as $x) {
                        $node = $p->getElementsByTagName($x);
                        $template_list[$k][$x] = $node->item(0)->nodeValue;
                    }
                }
            }
            // if (! $config['is_readable']) {
            // $not_readable_list[] = $config['xml_path'];
            // }
            
            // if (! $config['is_writable']) {
            // $not_writeable_list[] = $config['xml_path'];
            // }
        }
        // 文件不可读数量及文件路径
        // $this->assign("not_readable_count", count($not_readable_list));
        // $this->assign("not_readable_list", $not_readable_list);
        
        // 文件不可写数量及文件路径
        // $this->assign("not_writable_count", count($not_writeable_list));
        // $this->assign("not_writeable_list", $not_writeable_list);
        
        $this->assign("template_count", $template_count);
        $this->assign("template_list", $template_list);
    }

    /**
     * 更新当前选中的模板,修改对应的XML文件，存到数据库中
     * 创建时间：2017年9月4日 19:23:06
     * 王永杰
     *
     * @param post提交或传参 $type
     *            类型：shop、wap
     * @param post提交或传参 $folder
     *            文件夹：shop、wap
     * @return unknown[]
     */
    public function updateTemplateUse($type, $folder)
    {
        $res = 0; // 返回值
        if (empty($type) || empty($folder)) {
            return AjaxReturn($res);
        }
        /*
         * 修改XML
         * 1.找到要修改的XML
         * 2.修改use字段为“1”
         */
        // $xml = new \DOMDocument();
        // $template_list = array();
        // $config_list = $this->getTemplateXmlList($type);
        // foreach ($config_list as $k => $config) {
        // if ($config['is_readable'] && $config['is_writable']) {
        // // 获取xml文件内容
        // $xml_txt = fopen($config['xml_path'], "r,w");
        // $xml_str = fread($xml_txt, filesize($config['xml_path'])); // 指定读取大小，这里把整个文件内容读取出来
        // $xml_text = str_replace("\r\n", "<br />", $xml_str);
        // $xml->loadXML($xml_text);
        // $template = $xml->getElementsByTagName('template');
        
        // foreach ($template as $list) {
        // $folder_xml = $list->getElementsByTagName("folder");
        // $use = $list->getElementsByTagName("use");
        // if ($folder_xml->item(0)->nodeValue == $folder) {
        // $use->item(0)->nodeValue = 1;
        // } else {
        // $use->item(0)->nodeValue = 0;
        // }
        // $res = $xml->save($config['xml_path']);
        // }
        // }
        // }
        // if ($res > 0) {
        $config = new WebConfig();
        if ($type == "shop") {
            $res = $config->setUsePCTemplate($this->instance_id, $folder);
        } elseif ($type == "wap") {
            $res = $config->setUseWapTemplate($this->instance_id, $folder);
        }
        // }
        return AjaxReturn($res);
    }

    /**
     * 店铺导航添加
     *
     * @return multitype:unknown
     */
    public function addShopNavigation()
    {
        $shop = new Shop();
        if (request()->isAjax()) {
            $nav_title = request()->post('nav_title', '');
            $nav_url = request()->post('nav_url', '');
            $type = request()->post('type', '');
            $sort = request()->post('sort', '');
            $align = request()->post('align', '');
            $nav_type = request()->post('nav_type', '');
            $is_blank = request()->post('is_blank', '');
            $template_name = request()->post("template_name", '');
            $retval = $shop->addShopNavigation($nav_title, $nav_url, $type, $sort, $align, $nav_type, $is_blank, $template_name);
            return AjaxReturn($retval);
        } else {
            $shopNavTemplate = $shop->getShopNavigationTemplate(1);
            $this->assign("shopNavTemplate", $shopNavTemplate);
            
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "config/shopnavigationlist",
                        'menu_name' => "导航管理",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            
            return view($this->style . "Config/addShopNavigation");
        }
    }

    /**
     * 修改店铺导航
     *
     * @return multitype:unknown
     */
    public function updateShopNavigation()
    {
        $shop = new Shop();
        if (request()->isAjax()) {
            $nav_id = request()->post('nav_id', '');
            $nav_title = request()->post('nav_title', '');
            $nav_url = request()->post('nav_url', '');
            $type = request()->post('type', '');
            $sort = request()->post('sort', '');
            $align = request()->post('align', '');
            $nav_type = request()->post('nav_type', '');
            $is_blank = request()->post('is_blank', '');
            $template_name = request()->post("template_name", '');
            $retval = $shop->updateShopNavigation($nav_id, $nav_title, $nav_url, $type, $sort, $align, $nav_type, $is_blank, $template_name);
            return AjaxReturn($retval);
        } else {
            $nav_id = request()->get('nav_id', '');
            if (! is_numeric($nav_id)) {
                $this->error('未获取到信息');
            }
            $data = $shop->shopNavigationDetail($nav_id);
            $this->assign('data', $data);
            $shopNavTemplate = $shop->getShopNavigationTemplate(1);
            $this->assign("shopNavTemplate", $shopNavTemplate);
            
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "config/shopnavigationlist",
                        'menu_name' => "导航管理",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            
            return view($this->style . "Config/updateShopNavigation");
        }
    }

    /**
     * 删除店铺导航
     *
     * @return multitype:unknown
     */
    public function delShopNavigation()
    {
        if (request()->isAjax()) {
            $shop = new Shop();
            $nav_id = request()->post('nav_id', '');
            if (empty($nav_id)) {
                $this->error('未获取到信息');
            }
            $retval = $shop->delShopNavigation($nav_id);
            return AjaxReturn($retval);
        }
    }

    /**
     * 修改店铺导航排序
     *
     * @return multitype:unknown
     */
    public function modifyShopNavigationSort()
    {
        if (request()->isAjax()) {
            $shop = new Shop();
            $nav_id = request()->post('nav_id', '');
            $sort = request()->post('sort', '');
            $retval = $shop->modifyShopNavigationSort($nav_id, $sort);
            return AjaxReturn($retval);
        }
    }

    /**
     * 友情链接列表
     *
     * @return unknown[]
     */
    public function linkList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $platform = new Platform();
            $list = $platform->getLinkList($page_index, $page_size, [
                'link_title' => array(
                    'like',
                    '%' . $search_text . '%'
                )
            ], 'link_sort ASC');
            return $list;
        }
        $this->pcConfigChildMenuList(5);
        return view($this->style . "Config/linkList");
    }

    /**
     * 添加友情链接
     *
     * @return unknown[]
     */
    public function addLink()
    {
        if (request()->isAjax()) {
            $link_title = request()->post('link_title', '');
            $link_url = request()->post('link_url', '');
            $link_pic = request()->post('link_pic', '');
            $link_sort = request()->post('link_sort', 0);
            $is_blank = request()->post('is_blank', '');
            $is_show = request()->post("is_show", '');
            $platform = new Platform();
            $res = $platform->addLink($link_title, $link_url, $link_pic, $link_sort, $is_blank, $is_show);
            return AjaxReturn($res);
        }
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "config/linklist",
                    'menu_name' => "友情链接",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "Config/addLink");
    }

    /**
     * 修改友情链接
     */
    public function updateLink()
    {
        $platform = new Platform();
        if (request()->isAjax()) {
            $link_id = request()->post('link_id', '');
            $link_title = request()->post('link_title', '');
            $link_url = request()->post('link_url', '');
            $link_pic = request()->post('link_pic', '');
            $link_sort = request()->post('link_sort', 0);
            $is_blank = request()->post("is_blank", '');
            $is_show = request()->post("is_show", '');
            $res = $platform->updateLink($link_id, $link_title, $link_url, $link_pic, $link_sort, $is_blank, $is_show);
            return AjaxReturn($res);
        }
        $link_id = request()->get('link_id', '');
        if (empty($link_id)) {
            $this->error('未获取到信息');
        }
        $link_info = $platform->getLinkDetail($link_id);
        $this->assign('link_info', $link_info);
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "config/linklist",
                    'menu_name' => "友情链接",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "Config/updateLink");
    }

    /**
     * 删除友情链接
     *
     * @return unknown[]
     */
    public function delLink()
    {
        $link_id = request()->post('link_id', '');
        $platform = new Platform();
        if (empty($link_id)) {
            $this->error('未获取到信息');
        }
        $res = $platform->deleteLink($link_id);
        return AjaxReturn($res);
    }

    /**
     * 搜索设置
     */
    public function searchConfig()
    {
        $type = request()->get('type', 'hot');
        if ($type == "hot") {
            $this->pcConfigChildMenuList(6);
        } else {
            $this->pcConfigChildMenuList(7);
        }
        $web_config = new WebConfig();
        // 热门搜索
        $keywords_array = $web_config->getHotsearchConfig($this->instance_id);
        if (! empty($keywords_array)) {
            $keywords = implode(",", $keywords_array);
        } else {
            $keywords = '';
        }
        $this->assign('hot_keywords', $keywords);
        // 默认搜索
        $default_keywords = $web_config->getDefaultSearchConfig($this->instance_id);
        $this->assign('default_keywords', $default_keywords);
        $this->assign('type', $type);
        return view($this->style . "Config/searchConfig");
    }

    /**
     * 热门搜索 提交修改
     */
    public function hotSearchConfig()
    {
        $keywords = request()->post('keywords', '');
        if (! empty($keywords)) {
            $keywords_array = explode(",", $keywords);
        } else {
            $keywords_array = array();
        }
        $web_config = new WebConfig();
        $res = $web_config->setHotsearchConfig($this->instance_id, $keywords_array, 1);
        return AjaxReturn($res);
    }

    /**
     * 默认搜索 提交修改
     */
    public function defaultSearchConfig()
    {
        $keywords = request()->post('default_keywords', '');
        $web_config = new WebConfig();
        $res = $web_config->setDefaultSearchConfig($this->instance_id, $keywords, 1);
        return AjaxReturn($res);
    }

    /**
     * 验证码设置
     *
     * @return \think\response\View
     */
    public function codeConfig()
    {
        $child_menu_list = array(
            array(
                'url' => "config/webconfig",
                'menu_name' => "网站设置",
                "active" => 0
            ),
            array(
                'url' => "config/seoConfig",
                'menu_name' => "SEO设置",
                "active" => 0
            ),
            array(
                'url' => "config/codeconfig",
                'menu_name' => "验证码设置",
                "active" => 1
            ),
            array(
                'url' => "config/shopset",
                'menu_name' => "购物设置",
                "active" => 0
            ),
            array(
                'url' => "config/expressmessage",
                'menu_name' => "物流跟踪设置",
                "active" => 0
            ),
            array(
                'url' => "config/copyrightinfo",
                'menu_name' => "版权设置",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        
        $webConfig = new WebConfig();
        if (request()->isAjax()) {
            $platform = 0;
            $admin = request()->post('adminCode', 0);
            $pc = request()->post('pcCode', 0);
            $res = $webConfig->setLoginVerifyCodeConfig($this->instance_id, $platform, $admin, $pc);
            return AjaxReturn($res);
        }
        $code_config = $webConfig->getLoginVerifyCodeConfig($this->instance_id);
        $this->assign('code_config', $code_config["value"]);
        return view($this->style . 'Config/codeConfig');
    }

    /**
     * 邮件短信接口设置
     */
    public function messageConfig()
    {
        $type = request()->get('type', 'email');
        if ($type == 'email') {
            $child_menu_list = array(
                array(
                    'url' => "Config/messageConfig?type=email",
                    'menu_name' => "邮箱设置",
                    "active" => 1
                ),
                array(
                    'url' => "Config/messageConfig?type=sms",
                    'menu_name' => "短信设置",
                    "active" => 0
                )
            );
        } else {
            $child_menu_list = array(
                array(
                    'url' => "Config/messageConfig?type=email",
                    'menu_name' => "邮箱设置",
                    "active" => 0
                ),
                array(
                    'url' => "Config/messageConfig?type=sms",
                    'menu_name' => "短信设置",
                    "active" => 1
                )
            );
        }
        $config = new WebConfig();
        $email_message = $config->getEmailMessage($this->instance_id);
        $this->assign('email_message', $email_message);
        $mobile_message = $config->getMobileMessage($this->instance_id);
        $this->assign('mobile_message', $mobile_message);
        $this->assign('child_menu_list', $child_menu_list);
        $this->assign('type', $type);
        return view($this->style . 'Config/messageConfig');
    }

    /**
     * ajax 邮件接口
     */
    public function setEmailMessage()
    {
        $email_host = request()->post('email_host', '');
        $email_port = request()->post('email_port', '');
        $email_addr = request()->post('email_addr', '');
        $email_id = request()->post('email_id', '');
        $email_pass = request()->post('email_pass', '');
        $is_use = request()->post('is_use', 0);
        $email_is_security = request()->post('email_is_security', false);
        $config = new WebConfig();
        $res = $config->setEmailMessage($this->instance_id, $email_host, $email_port, $email_addr, $email_id, $email_pass, $is_use, $email_is_security);
        return AjaxReturn($res);
    }

    /**
     * ajax 短信接口
     *
     * @return unknown[]
     */
    public function setMobileMessage()
    {
        $app_key = request()->post('app_key', '');
        $secret_key = request()->post('secret_key', '');
        $free_sign_name = request()->post('free_sign_name', '');
        $is_use = request()->post('is_use', '');
        $user_type = request()->post('user_type', 0); // 用户类型 0:旧用户，1：新用户 默认是旧用户
        $config = new WebConfig();
        $res = $config->setMobileMessage($this->instance_id, $app_key, $secret_key, $free_sign_name, $is_use, $user_type);
        return AjaxReturn($res);
    }

    /**
     * 邮件发送测试接口
     *
     * @return unknown[]
     */
    public function testSend()
    {
        $is_socket = extension_loaded('sockets');
        $is_connect = function_exists("socket_connect");
        if ($is_socket && $is_connect) {
            $send = new Send();
            // $toemail = "854991437@qq.com";//$_POST['email_test'];
            $title = 'Niushop测试邮箱发送';
            $content = '测试邮箱发送成功不成功？';
            $email_host = request()->post('email_host', '');
            $email_port = request()->post('email_port', '');
            $email_addr = request()->post('email_addr', '');
            $email_id = request()->post('email_id', '');
            $email_pass = request()->post('email_pass', '');
            $email_is_security = request()->post('email_is_security', '');
            $toemail = request()->post('email_test', '');
            $res = emailSend($email_host, $email_id, $email_pass, $email_port, $email_is_security, $email_addr, $toemail, $title, $content, $this->instance_name);
            // $config = new WebConfig();
            // $email_message = $config->getEmailMessage($this->instance_id);
            // $email_value = $email_message["value"];
            // $res = emailSend($email_value['email_host'], $email_value['email_id'], $email_value['email_pass'], $email_value['email_addr'], $toemail, $title, $content);
            // var_dump($res);
            // exit;
            if ($res) {
                return AjaxReturn(1);
            } else {
                return AjaxReturn(- 1);
            }
        } else {
            return AjaxReturn(EMAIL_SENDERROR);
        }
    }

    /**
     * 帮助类型
     *
     * @return unknown
     */
    public function helpclass()
    {
        $child_menu_list = array(
            array(
                'url' => "config/helpdocument",
                'menu_name' => "帮助内容",
                "active" => 0
            ),
            array(
                'url' => "config/helpclass",
                'menu_name' => "帮助类型",
                "active" => 1
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $platform = new Platform();
            $list = $platform->getPlatformHelpClassList($page_index, $page_size, [
                'type' => 1
            ], 'sort');
            return $list;
        }
        return view($this->style . "Config/helpClass");
    }

    /**
     * 修改帮助类型
     * 任鹏强
     * 2017年2月18日14:26:20
     */
    public function updateClass()
    {
        if (request()->isAjax()) {
            $class_id = request()->post('class_id', '');
            $type = request()->post('type', 1);
            $class_name = request()->post('class_name', '');
            $parent_class_id = request()->post('parent_class_id', 0);
            $sort = request()->post('sort', '');
            $platform = new Platform();
            $res = $platform->updatePlatformClass($class_id, $type, $class_name, $parent_class_id, $sort);
            return AjaxReturn($res);
        }
    }

    /**
     * 删除帮助类型
     */
    public function classDelete()
    {
        $class_id = request()->post('class_id', '');
        $platform = new Platform();
        $retval = $platform->deleteHelpClass($class_id);
        return AjaxReturn($retval);
    }

    /**
     * 添加 帮助类型
     */
    public function addHelpClass()
    {
        if (request()->isAjax()) {
            $class_name = request()->post('class_name', '');
            $sort = request()->post('sort', '');
            $platform = new Platform();
            $res = $platform->addPlatformHelpClass(1, $class_name, 0, $sort);
            return AjaxReturn($res);
        }
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "config/helpclass",
                    'menu_name' => "帮助类型",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . 'Config/addHelpClass');
    }

    /**
     * 删除帮助内容标题
     *
     * @return unknown[]
     */
    public function titleDelete()
    {
        $id = request()->post('id', '');
        $platform = new Platform();
        $res = $platform->deleteHelpTitle($id);
        return AjaxReturn($res);
    }

    /**
     * 帮助内容
     *
     * @return multitype:number unknown |Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function helpDocument()
    {
        $child_menu_list = array(
            array(
                'url' => "config/helpdocument",
                'menu_name' => "帮助内容",
                "active" => 1
            ),
            array(
                'url' => "config/helpclass",
                'menu_name' => "帮助类型",
                "active" => 0
            )
        );
        $this->assign('child_menu_list', $child_menu_list);
        
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $platform = new Platform();
            $list = $platform->getPlatformHelpDocumentList($page_index, $page_size, '', 'sort desc');
            return $list;
        }
        return view($this->style . "Config/helpDocument");
    }

    /**
     * 修改内容
     */
    public function updateDocument()
    {
        $platform = new Platform();
        if (request()->isAjax()) {
            $uid = $this->user->getSessionUid();
            $id = request()->post('id', '');
            $title = request()->post('title', '');
            $class_id = request()->post('class_id', '');
            $link_url = request()->post('link_url', '');
            $content = request()->post('content', '');
            $image = request()->post('image', '');
            $sort = request()->post('sort', 0);
            $revle = $platform->updatePlatformDocument($id, $uid, $class_id, $title, $link_url, $sort, $content, $image);
            return AjaxReturn($revle);
        } else {
            $id = request()->get('id', '');
            $this->assign('id', $id);
            $document_detail = $platform->getPlatformDocumentDetail($id);
            $document_detail["content"] = htmlspecialchars($document_detail["content"]);
            $this->assign('document_detail', $document_detail);
            $help_class_list = $platform->getPlatformHelpClassList();
            $this->assign('help_class_list', $help_class_list['data']);
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "config/helpdocument",
                        'menu_name' => "帮助内容",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            return view($this->style . 'Config/updateDocument');
        }
    }

    /**
     * 修改帮助中心内容的标题与排序
     */
    public function updateHelpContentTitleAndSort()
    {
        if (request()->isAjax()) {
            $platform = new Platform();
            $id = request()->post('id', '');
            $title = request()->post('title', '');
            $sort = request()->post('sort', 0);
            $retval = $platform->updatePlatformDocumentTitleAndSort($id, $title, $sort);
            return AjaxReturn($retval);
        }
    }

    /**
     * 添加内容
     */
    public function addDocument()
    {
        $platform = new Platform();
        if (request()->isAjax()) {
            $uid = $this->user->getSessionUid();
            $title = request()->post('title', '');
            $class_id = request()->post('class_id', '');
            $link_url = request()->post('link_url', '');
            $content = request()->post('content', '');
            $image = request()->post('image', '');
            $sort = request()->post('sort', '');
            $result = $platform->addPlatformDocument($uid, $class_id, $title, $link_url, $sort, $content, $image);
            return AjaxReturn($result);
        } else {
            $help_class_list = $platform->getPlatformHelpClassList();
            $this->assign('help_class_list', $help_class_list['data']);
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "config/helpdocument",
                        'menu_name' => "帮助内容",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            return view($this->style . 'Config/addDocument');
        }
    }

    /**
     * pc端子菜单列表
     * 2017年7月24日 14:25:56 王永杰
     *
     * @param $flag 1:导航管理，2：促销板块，3：首页楼层，4：首页公告，5：友情链接，6：热门搜索，7：默认搜索，8：手机模板，9：自定义模板            
     */
    public function pcConfigChildMenuList($flag)
    {
        $child_menu_list = array(
            array(
                'url' => "config/shopnavigationlist",
                'menu_name' => "导航管理",
                "active" => 0,
                'flag' => 1
            ),
            array(
                'url' => "system/goodscategoryblock",
                'menu_name' => "商品楼层",
                "active" => 0,
                'flag' => 3
            ),
            array(
                'url' => "config/usernotice",
                'menu_name' => "首页公告",
                "active" => 0,
                'flag' => 4
            ),
            
            array(
                'url' => "config/linklist",
                'menu_name' => "友情链接",
                "active" => 0,
                'flag' => 5
            ),
            array(
                'url' => "config/searchConfig?type=hot",
                'menu_name' => "热门搜索",
                "active" => 0,
                'flag' => 6
            ),
            array(
                'url' => "config/searchConfig?type=default",
                'menu_name' => "默认搜索",
                "active" => 0,
                'flag' => 7
            ),
            array(
                'url' => "config/pcTemplate",
                'menu_name' => "模板",
                "active" => 0,
                'flag' => 8
            )
        );
        
        foreach ($child_menu_list as $k => $v) {
            if ($v['flag'] == $flag) {
                $child_menu_list[$k]['active'] = 1;
            }
        }
        $this->assign('child_menu_list', $child_menu_list);
    }

    /**
     * 根据路径查询配置文件集合
     * 创建时间：2017年9月4日 09:52:21
     * 修改时间：2017年9月4日 14:47:47
     * 王永杰
     *
     * @param unknown $path            
     */
    function getfiles($path)
    {
        try {
            
            $config_list = array();
            
            $k = 0;
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    if ((is_dir($path . "/" . $file)) && $file != "." && $file != "..") {
                        // 当前目录问文件夹
                        $xml_path = $path . '/' . $file . '/config.xml';
                        $xml_path = str_replace("\\", "/", $xml_path);
                        $config_list[$k]['xml_path'] = $xml_path; // XML文件路径
                        $config_list[$k]['is_readable'] = is_readable($xml_path); // 是否可读
                                                                                  
                        // $config_list[$k]['is_writable'] = is_writable($xml_path); // 是否可写
                        $k ++;
                    }
                }
                closedir($dh);
            }
            $config_list = array_merge($config_list);
        } catch (\Exception $e) {
            echo $e;
        }
        return $config_list;
    }

    /**
     * 固定模板
     */
    public function fixedtemplate()
    {
        $web_config = new WebConfig();
        $platform = new Platform();
        $goods_category = new GoodsCategory();
        $this->getThreeLevelModule();
        $shop_id = $this->instance_id;
        $condition = [
            'class_type' => 2,
            'is_use' => 1,
            'show_type' => 1
        ];
        $goods_recommend_class = $platform->getPlatformGoodsRecommendClass($condition);
        $this->assign('goods_recommend_class', $goods_recommend_class);
        $category_list_1 = $goods_category->getGoodsCategoryList(1, 0, [
            'is_visible' => 1,
            'level' => 1
        ]);
        $this->assign("show_type", 1);
        $this->assign('category_list_1', $category_list_1['data']);
        
        // 手机模板
        $this->getCollatingTemplateList("wap");
        $use_wap_template = $web_config->getUseWapTemplate($shop_id);
        $value = "default";
        if (! empty($use_wap_template)) {
            $value = $use_wap_template['value'];
        } else {
            // 使用默认模板
            $web_config->setUseWapTemplate($this->instance_id, "default");
            $this->updateTemplateUse("wap", "default");
        }
        $this->assign("use_template", $value);
        
        // 首页公告
        if (request()->isAjax()) {
            $notice_message = request()->post('notice_message', '');
            $is_enable = request()->post('is_enable', '');
            $res = $web_config->setNotice($shop_id, $notice_message, $is_enable);
            return AjaxReturn($res);
        }
        
        $info = $web_config->getNotice($shop_id);
        $this->assign('info', $info);
        // 查询商品促销板块是否显示配置
        $list = $web_config->getrecommendConfig($shop_id);
        if (empty($list)) {
            $list['id'] = '';
            $list['value']['is_recommend'] = '';
        }
        $lists = $web_config->getcategoryConfig($shop_id);
        $this->assign("list", $list);
        $this->assign("lists", $lists);
        return view($this->style . 'Config/fixedtemplate');
    }

    /**
     * 编辑促销版块
     */
    public function updateGoodsRecommendClass()
    {
        $class_id = request()->post('class_id', 0);
        $class_name = request()->post('class_name', '');
        $goods_id_array = request()->post('goods_id_array', '');
        $sort = request()->post('sort', '');
        $show_type = request()->post('show_type', '');
        $platform = new Platform();
        $res = $platform->updatePlatformGoodsRecommendClass($class_id, $class_name, $sort, $goods_id_array, $show_type);
        return AjaxReturn($res);
    }

    /**
     * 删除 促销版块
     *
     * @return unknown[]
     */
    public function delGoodsRecommendClass()
    {
        $class_id = request()->post('class_id', 0);
        if ($class_id > 0) {
            $platform = new Platform();
            $res = $platform->deletePlatformGoodsRecommendClass($class_id);
            return AjaxReturn($res);
        } else {
            return AjaxReturn(0);
        }
    }

    /**
     * 首页公告 设置
     *
     * @return \think\response\View
     */
    public function userNotice()
    {
        $platform = new Platform();
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $list = $platform->getNoticeList($page_index, $page_size, "", "create_time desc");
            return $list;
        }
        $this->pcConfigChildMenuList(4);
        return view($this->style . 'Config/userNotice');
    }

    /**
     * 奖励管理
     */
    public function bonuses()
    {
        $dataShop = new Shop();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $sign_point = request()->post('sign_point', 0);
            $share_point = request()->post('share_point', 0);
            $reg_member_self_point = request()->post('reg_member_self_point', 0);
            $reg_member_one_point = 0;
            $reg_member_two_point = 0;
            $reg_member_three_point = 0;
            $reg_promoter_self_point = 0;
            $reg_promoter_one_point = 0;
            $reg_promoter_two_point = 0;
            $reg_promoter_three_point = 0;
            $reg_partner_self_point = 0;
            $reg_partner_one_point = 0;
            $reg_partner_two_point = 0;
            $reg_partner_three_point = 0;
            $into_store_coupon = request()->post('into_store_coupon', 0);
            $share_coupon = request()->post('share_coupon', 0);
            $res = $dataShop->setRewardRule($shop_id, $sign_point, $share_point, $reg_member_self_point, $reg_member_one_point, $reg_member_two_point, $reg_member_three_point, $reg_promoter_self_point, $reg_promoter_one_point, $reg_promoter_two_point, $reg_promoter_three_point, $reg_partner_self_point, $reg_partner_one_point, $reg_partner_two_point, $reg_partner_three_point, $into_store_coupon, $share_coupon);
            return AjaxReturn($res);
        }
        $res = $dataShop->getRewardRuleDetail($this->instance_id); // 此处有问题 2017年7月17日 14:35:24
        $this->assign("res", $res);
        // 查询未过期的优惠劵
        $coupon = new Promotion();
        $condition['shop_id'] = $this->instance_id;
        $nowTime = date("Y-m-d H:i:s");
        $condition['end_time'] = array(
            ">",
            getTimeTurnTimeStamp($nowTime)
        );
        $list = $coupon->getCouponTypeList(1, 0, $condition);
        $this->assign("coupon", $list['data']);
        return view($this->style . 'Config/bonuses');
    }

    /**
     * 修改公告
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >|Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function updateNotice()
    {
        $web_config = new WebConfig();
        $shopid = $this->instance_id;
        if (request()->isAjax()) {
            $notice_message = request()->post('notice_message', '');
            $is_enable = request()->post('is_enable', '');
            $res = $web_config->setNotice($shopid, $notice_message, $is_enable);
            return AjaxReturn($res);
        }
    }

    public function areaManagement()
    {
        $child_menu_list = array(
            array(
                'url' => "express/expresscompany",
                'menu_name' => "物流公司",
                "active" => 0
            ),
            array(
                'url' => "config/areamanagement",
                'menu_name' => "地区管理",
                "active" => 1
            ),
            array(
                'url' => "order/returnsetting",
                'menu_name' => "商家地址",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointlist",
                'menu_name' => "自提点管理",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointfreight",
                'menu_name' => "自提点运费",
                "active" => 0
            ),
            array(
                'url' => "config/distributionareamanagement",
                'menu_name' => "货到付款地区管理",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        $dataAddress = new DataAddress();
        $area_list = $dataAddress->getAreaList(); // 区域地址
        $list = $dataAddress->getProvinceList();
        foreach ($list as $k => $v) {
            if ($dataAddress->getCityCountByProvinceId($v['province_id']) > 0) {
                $v['issetLowerLevel'] = 1;
            } else {
                $v['issetLowerLevel'] = 0;
            }
            if (! empty($area_list)) {
                foreach ($area_list as $area) {
                    if ($area['area_id'] == $v['area_id']) {
                        $list[$k]['area_name'] = $area['area_name'];
                        break;
                    } else {
                        $list[$k]['area_name'] = "-";
                    }
                }
            }
        }
        $this->assign("area_list", $area_list);
        $this->assign("list", $list);
        return view($this->style . 'Config/areaManagement');
    }

    public function selectCityListAjax()
    {
        if (request()->isAjax()) {
            $province_id = request()->post('province_id', '');
            $dataAddress = new DataAddress();
            $list = $dataAddress->getCityList($province_id);
            foreach ($list as $v) {
                if ($dataAddress->getDistrictCountByCityId($v['city_id']) > 0) {
                    $v['issetLowerLevel'] = 1;
                } else {
                    $v['issetLowerLevel'] = 0;
                }
            }
            return $list;
        }
    }

    public function selectDistrictListAjax()
    {
        if (request()->isAjax()) {
            $city_id = request()->post('city_id', '');
            $dataAddress = new DataAddress();
            $list = $dataAddress->getDistrictList($city_id);
            return $list;
        }
    }

    public function addCityAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $city_id = 0;
            $province_id = request()->post('superiorRegionId', '');
            $city_name = request()->post('regionName', '');
            $zipcode = request()->post('zipcode', '');
            $sort = request()->post('regionSort', '');
            $res = $dataAddress->addOrupdateCity($city_id, $province_id, $city_name, $zipcode, $sort);
            return AjaxReturn($res);
        }
    }

    public function updateCityAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $city_id = request()->post('eventId', '');
            $province_id = request()->post('superiorRegionId', '');
            $city_name = request()->post('regionName', '');
            $zipcode = request()->post('zipcode', '');
            $sort = request()->post('regionSort', '');
            $res = $dataAddress->addOrupdateCity($city_id, $province_id, $city_name, $zipcode, $sort);
            return AjaxReturn($res);
        }
    }

    public function addDistrictAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $district_id = 0;
            $city_id = request()->post('superiorRegionId', '');
            $district_name = request()->post('regionName', '');
            $sort = request()->post('regionSort', '');
            $res = $dataAddress->addOrupdateDistrict($district_id, $city_id, $district_name, $sort);
            return AjaxReturn($res);
        }
    }

    public function updateDistrictAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $district_id = request()->post('eventId', '');
            $city_id = request()->post('superiorRegionId', '');
            $district_name = request()->post('regionName', '');
            $sort = request()->post('regionSort', '');
            $res = $dataAddress->addOrupdateDistrict($district_id, $city_id, $district_name, $sort);
            return AjaxReturn($res);
        }
    }

    public function updateProvinceAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $province_id = request()->post('eventId', '');
            $province_name = request()->post('regionName', '');
            $sort = request()->post('regionSort', '');
            $area_id = request()->post('area_id', '');
            $res = $dataAddress->updateProvince($province_id, $province_name, $sort, $area_id);
            return AjaxReturn($res);
        }
    }

    public function addProvinceAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $province_name = request()->post('regionName', ''); // 区域名称
            $sort = request()->post('regionSort', ''); // 排序
            $area_id = request()->post('area_id', 0); // 区域id
            $res = $dataAddress->addProvince($province_name, $sort, $area_id);
            return AjaxReturn($res);
        }
    }

    public function deleteRegion()
    {
        if (request()->isAjax()) {
            $type = request()->post('type', '');
            $regionId = request()->post('regionId', '');
            $dataAddress = new DataAddress();
            if ($type == 1) {
                $res = $dataAddress->deleteProvince($regionId);
                return AjaxReturn($res);
            }
            if ($type == 2) {
                $res = $dataAddress->deleteCity($regionId);
                return AjaxReturn($res);
            }
            if ($type == 3) {
                $res = $dataAddress->deleteDistrict($regionId);
                return AjaxReturn($res);
            }
        }
    }

    public function updateRegionAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $upType = request()->post('upType', '');
            $regionType = request()->post('regionType', '');
            $regionName = request()->post('regionName', '');
            $regionSort = request()->post('regionSort', '');
            $regionId = request()->post('regionId', '');
            $res = $dataAddress->updateRegionNameAndRegionSort($upType, $regionType, $regionName, $regionSort, $regionId);
            return AjaxReturn($res);
        }
    }

    /**
     * 购物设置
     */
    public function shopSet()
    {
        $child_menu_list = array(
            array(
                'url' => "config/webconfig",
                'menu_name' => "网站设置",
                "active" => 0
            ),
            array(
                'url' => "config/seoConfig",
                'menu_name' => "SEO设置",
                "active" => 0
            ),
            array(
                'url' => "config/codeconfig",
                'menu_name' => "验证码设置",
                "active" => 0
            ),
            array(
                'url' => "config/shopset",
                'menu_name' => "购物设置",
                "active" => 1
            ),
            array(
                'url' => "config/expressmessage",
                'menu_name' => "物流跟踪设置",
                "active" => 0
            ),
            array(
                'url' => "config/copyrightinfo",
                'menu_name' => "版权设置",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        $Config = new WebConfig();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $order_auto_delinery = request()->post("order_auto_delinery", 0);
            $order_balance_pay = request()->post("order_balance_pay", 0);
            $order_delivery_complete_time = request()->post("order_delivery_complete_time", 0);
            $order_show_buy_record = request()->post("order_show_buy_record", 0);
            $order_invoice_tax = request()->post("order_invoice_tax", 0);
            $order_invoice_content = request()->post("order_invoice_content", '');
            $order_delivery_pay = request()->post("order_delivery_pay", 0);
            $order_buy_close_time = request()->post("order_buy_close_time", 0);
            $buyer_self_lifting = request()->post("buyer_self_lifting", 0);
            $seller_dispatching = request()->post("seller_dispatching", '1');
            $is_logistics = request()->post("is_logistics", '1');
            $shopping_back_points = request()->post("shopping_back_points", 0);
            $is_open_virtual_goods = request()->post("is_open_virtual_goods", 0); // 是否开启虚拟商品
            $retval = $Config->SetShopConfig($shop_id, $order_auto_delinery, $order_balance_pay, $order_delivery_complete_time, $order_show_buy_record, $order_invoice_tax, $order_invoice_content, $order_delivery_pay, $order_buy_close_time, $buyer_self_lifting, $seller_dispatching, $is_logistics, $shopping_back_points, $is_open_virtual_goods);
            return AjaxReturn($retval);
        } else {
            // 订单收货之后多长时间自动完成
            $shop_id = $this->instance_id;
            $shopSet = $Config->getShopConfig($shop_id);
            
            $this->assign("shopSet", $shopSet);
            return view($this->style . "Config/shopSet");
        }
    }

    /**
     * 通知系统
     */
    public function notifyIndex()
    {
        $config_service = new WebConfig();
        $shop_id = $this->instance_id;
        $notify_list = $config_service->getNoticeConfig($shop_id);
        $this->assign("notify_list", $notify_list);
        return view($this->style . 'Config/notifyConfig');
    }

    /**
     * 开启和关闭 邮件 和短信的开启和 关闭
     */
    public function updateNotifyEnable()
    {
        $id = request()->post('id', '');
        $is_use = request()->post('is_use', '');
        $config_service = new WebConfig();
        $retval = $config_service->updateConfigEnable($id, $is_use);
        return AjaxReturn($retval);
    }

    /**
     * 修改模板
     *
     * @return \think\response\View
     */
    public function notifyTemplate()
    {
        $type = request()->get('type', 'email');
        $config_service = new WebConfig();
        $shop_id = $this->instance_id;
        $template_detail = $config_service->getNoticeTemplateDetail($shop_id, $type);
        $template_type_list = $config_service->getNoticeTemplateType($type, "user");
        for ($i = 0; $i < count($template_type_list); $i ++) {
            $template_code = $template_type_list[$i]["template_code"];
            $is_enable = 0;
            $template_title = "";
            $template_content = "";
            $sign_name = "";
            foreach ($template_detail as $template_obj) {
                if ($template_obj["template_code"] == $template_code) {
                    $is_enable = $template_obj["is_enable"];
                    $template_title = $template_obj["template_title"];
                    $template_content = str_replace(PHP_EOL, '', $template_obj["template_content"]);
                    $sign_name = $template_obj["sign_name"];
                    break;
                }
            }
            $template_type_list[$i]["is_enable"] = $is_enable;
            $template_type_list[$i]["template_title"] = $template_title;
            $template_type_list[$i]["template_content"] = $template_content;
            $template_type_list[$i]["sign_name"] = $sign_name;
        }
        $template_item_list = $config_service->getNoticeTemplateItem($template_type_list[0]["template_code"]);
        $this->assign("template_type_list", $template_type_list);
        $this->assign("template_json", json_encode($template_type_list));
        $this->assign("template_select", $template_type_list[0]);
        $this->assign("template_item_list", $template_item_list);
        $this->assign("template_send_item_json", json_encode($template_item_list));
        if ($type == "email") {
            return view($this->style . 'Config/notifyEmailTemplate');
        } else {
            return view($this->style . 'Config/notifySmsTemplate');
        }
    }

    /**
     * 得到可用的变量
     *
     * @return unknown
     */
    public function getTemplateItem()
    {
        $template_code = request()->post('template_code', '');
        $config_service = new WebConfig();
        $template_item_list = $config_service->getNoticeTemplateItem($template_code);
        return $template_item_list;
    }

    /**
     * 更新通知模板
     *
     * @return multitype:unknown
     */
    public function updateNotifyTemplate()
    {
        $template_code = request()->post('type', 'email');
        $template_data = request()->post('template_data', '');
        $notify_type = request()->post("notify_type", "user");
        $shop_id = $this->instance_id;
        $config_service = new WebConfig();
        $retval = $config_service->updateNoticeTemplate($shop_id, $template_code, $template_data, $notify_type);
        return AjaxReturn($retval);
    }

    /**
     * 会员提现设置
     *
     * @return multitype:number unknown |Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function memberWithdrawSetting()
    {
        $config_service = new WebConfig();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $key = 'WITHDRAW_BALANCE';
            $value = array(
                'withdraw_cash_min' => request()->post('cash_min', 0),
                'withdraw_multiple' => request()->post('multiple', 1),
                'withdraw_poundage' => request()->post('poundage', 0),
                'withdraw_message' => request()->post('message', '')
            );
            $is_use = request()->post('is_use', '');
            $retval = $config_service->setBalanceWithdrawConfig($shop_id, $key, $value, $is_use);
            return AjaxReturn($retval);
        } else {
            $shop_id = $this->instance_id;
            $list = $config_service->getBalanceWithdrawConfig($shop_id);
            if (empty($list)) {
                $list['id'] = '';
                $list['value']['withdraw_cash_min'] = '';
                $list['value']['withdraw_multiple'] = '';
                $list['value']['withdraw_poundage'] = '';
                $list['value']['withdraw_message'] = '';
            }
            $this->assign("list", $list);
            return view($this->style . "Config/memberWithdrawSetting");
        }
    }

    public function customservice()
    {
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $key = 'SERVICE_ADDR';
            $value = array(
                'service_addr' => request()->post('service_addr', '')
            );
            $config_service = new WebConfig();
            $retval = $config_service->setcustomserviceConfig($shop_id, $key, $value);
            return AjaxReturn($retval);
        } else {
            $shop_id = $this->instance_id;
            $config_service = new WebConfig();
            $list = $config_service->getcustomserviceConfig($shop_id);
            if (empty($list)) {
                $list['id'] = '';
                $list['value']['service_addr'] = '';
            }
            $this->assign("list", $list);
            return view($this->style . "Config/customservice");
        }
    }

    /**
     * 首页商品促销板块是否开启设置
     */
    public function isrecommend()
    {
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $key = 'IS_RECOMMEND';
            $value = array(
                'is_recommend' => request()->post('is_recommend', '0')
            );
            $config_service = new WebConfig();
            $retval = $config_service->setisrecommendConfig($shop_id, $key, $value);
            return AjaxReturn($retval);
        }
    }

    /**
     * 首页商品分类是否显示设置
     */
    public function iscategory()
    {
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $key = 'IS_CATEGORY';
            $value = array(
                'is_category' => request()->post('is_category', '0')
            );
            $config_service = new WebConfig();
            $retval = $config_service->setiscategoryConfig($shop_id, $key, $value);
            return AjaxReturn($retval);
        }
    }

    /**
     * 用户提现审核
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function userCommissionWithdrawAudit()
    {
        $id = request()->post('id', '');
        $status = request()->post('status', '');
        $user = new User();
        $retval = $user->UserCommissionWithdrawAudit($this->instance_id, $id, $status);
        return AjaxReturn($retval);
    }

    /**
     * 支付
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function paymentConfig()
    {
        $config_service = new WebConfig();
        $shop_id = $this->instance_id;
        $pay_list = $config_service->getPayConfig($shop_id);
        $this->assign("pay_list", $pay_list);
        return view($this->style . 'Config/paymentConfig');
    }

    /**
     * 第三方登录页面
     */
    public function partyLogin()
    {
        $web_config = new WebConfig();
        // qq登录配置
        // 获取当前域名
        $domain_name = \think\Request::instance()->domain();
        // 获取回调域名qq回调域名
        $qq_call_back = $domain_name . \think\Request::instance()->root() . '/wap/login/callback';
        // 获取qq配置信息
        $qq_config = $web_config->getQQConfig($this->instance_id);
        // dump($qq_config);
        $qq_config['value']["AUTHORIZE"] = $domain_name;
        $qq_config['value']["CALLBACK"] = $qq_call_back;
        $qq_config['name'] = 'qq登录';
        $this->assign("qq_config", $qq_config);
        // 微信登录配置
        // 微信登录返回
        $wchat_call_back = $domain_name . \think\Request::instance()->root() . '/wap/Login/callback';
        $wchat_config = $web_config->getWchatConfig($this->instance_id);
        $wchat_config['value']["AUTHORIZE"] = $domain_name;
        $wchat_config['value']["CALLBACK"] = $wchat_call_back;
        $wchat_config['name'] = '微信登录';
        $this->assign("wchat_config", $wchat_config);
        return view($this->style . 'Config/partyLogin');
    }

    /**
     * 配送地区管理
     */
    public function distributionAreaManagement()
    {
        $child_menu_list = array(
            array(
                'url' => "express/expresscompany",
                'menu_name' => "物流公司",
                "active" => 0
            ),
            array(
                'url' => "config/areamanagement",
                'menu_name' => "地区管理",
                "active" => 0
            ),
            array(
                'url' => "order/returnsetting",
                'menu_name' => "商家地址",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointlist",
                'menu_name' => "自提点管理",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointfreight",
                'menu_name' => "自提点运费",
                "active" => 0
            ),
            array(
                'url' => "config/distributionareamanagement",
                'menu_name' => "货到付款地区管理",
                "active" => 1
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        
        $dataAddress = new DataAddress();
        $provinceList = $dataAddress->getProvinceList();
        $cityList = $dataAddress->getCityList();
        foreach ($provinceList as $k => $v) {
            $arr = array();
            foreach ($cityList as $c => $co) {
                if ($co["province_id"] == $v['province_id']) {
                    $arr[] = $co;
                    unset($cityList[$c]);
                }
            }
            $provinceList[$k]['city_list'] = $arr;
        }
        $this->assign("list", $provinceList);
        $districtList = $dataAddress->getDistrictList();
        $this->assign("districtList", $districtList);
        $this->getDistributionArea();
        return view($this->style . "Config/distributionAreaManagement");
    }

    /**
     * 注册与访问
     */
    public function registerAndVisit()
    {
        $config_service = new WebConfig();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $is_register = request()->post('is_register', '');
            $register_info = request()->post('register_info', '');
            $register_info = empty($register_info) ? '' : rtrim($register_info, ',');
            $name_keyword = request()->post('name_keyword', '');
            $pwd_len = request()->post('pwd_len', '');
            $pwd_complexity = request()->post('pwd_complexity', '');
            $pwd_complexity = empty($pwd_complexity) ? '' : rtrim($pwd_complexity, ',');
            $terms_of_service = request()->post('terms_of_service', '');
            $is_requiretel = request()->post('is_requiretel', '');
            $is_use = request()->post('is_use', '1');
            $res = $config_service->setRegisterAndVisit($shop_id, $is_register, $register_info, $name_keyword, $pwd_len, $pwd_complexity, $terms_of_service, $is_requiretel, $is_use);
            return AjaxReturn($res);
        } else {
            $register_and_visit = $config_service->getRegisterAndVisit($this->instance_id);
            $this->assign('register_and_visit', json_decode($register_and_visit['value'], true));
            return view($this->style . "Config/registerAndVisit");
        }
    }

    /**
     * 获取配送地区设置
     */
    public function getDistributionArea()
    {
        $dataAddress = new DataAddress();
        $res = $dataAddress->getDistributionAreaInfo($this->instance_id);
        if ($res != '') {
            $this->assign("provinces", explode(',', $res['province_id']));
            $this->assign("citys", explode(',', $res['city_id']));
            $this->assign("districts", $res["district_id"]);
        }
    }

    /**
     * 通过ajax添加或编辑配送区域
     */
    public function addOrUpdateDistributionAreaAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $shop_id = $this->instance_id;
            $province_id = request()->post("province_id", "");
            $city_id = request()->post("city_id", "");
            $district_id = request()->post("district_id", "");
            $res = $dataAddress->addOrUpdateDistributionArea($shop_id, $province_id, $city_id, $district_id);
            return AjaxReturn($res);
        }
    }

    public function expressMessage()
    {
        $child_menu_list = array(
            array(
                'url' => "config/webconfig",
                'menu_name' => "网站设置",
                "active" => 0
            ),
            array(
                'url' => "config/seoConfig",
                'menu_name' => "SEO设置",
                "active" => 0
            ),
            array(
                'url' => "config/codeconfig",
                'menu_name' => "验证码设置",
                "active" => 0
            ),
            array(
                'url' => "config/shopset",
                'menu_name' => "购物设置",
                "active" => 0
            ),
            array(
                'url' => "config/expressmessage",
                'menu_name' => "物流跟踪设置",
                "active" => 1
            ),
            array(
                'url' => "config/copyrightinfo",
                'menu_name' => "版权设置",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        $config_service = new WebConfig();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $appid = request()->post("appid", "");
            $appkey = request()->post("appkey", "");
            $back_url = request()->post('back_url', "");
            $is_use = request()->post("is_use", "");
            $res = $config_service->updateOrderExpressMessageConfig($shop_id, $appid, $appkey, $back_url, $is_use);
            return AjaxReturn($res);
        } else {
            $shop_id = $this->instance_id;
            $expressMessageConfig = $config_service->getOrderExpressMessageConfig($shop_id);
            $this->assign('emconfig', $expressMessageConfig);
            return view($this->style . "Config/expressMessage");
        }
    }

    /**
     * 上传方式
     */
    public function uploadType()
    {
        $config_data = array();
        $web_config = new WebConfig();
        $upload_type = $web_config->getUploadType($this->instance_id);
        $config_data["type"] = $upload_type;
        // 获取七牛参数
        $config_qiniu_info = $web_config->getQiniuConfig($this->instance_id);
        $config_data["data"]["qiniu"] = $config_qiniu_info;
        $this->assign("config_data", $config_data);
        return view($this->style . "Config/uploadType");
    }

    /**
     * 修改上传类型
     */
    public function setUploadType()
    {
        $config_service = new WebConfig();
        $shop_id = $this->instance_id;
        $type = request()->post("type", "1");
        $result = $config_service->setUploadType($shop_id, $type);
        return AjaxReturn($result);
    }

    /**
     * 修改七牛配置
     */
    public function setQiniuConfig()
    {
        $config_service = new WebConfig();
        $shop_id = $this->instance_id;
        $Accesskey = request()->post("Accesskey", "");
        $Secretkey = request()->post("Secretkey", "");
        $Bucket = request()->post("Bucket", "");
        $QiniuUrl = request()->post("QiniuUrl", "");
        $value = array(
            "Accesskey" => trim($Accesskey),
            "Secretkey" => trim($Secretkey),
            "Bucket" => trim($Bucket),
            "QiniuUrl" => trim($QiniuUrl)
        );
        $value = json_encode($value);
        $result = $config_service->setQiniuConfig($shop_id, $value);
        return AjaxReturn($result);
    }

    /**
     * 商家通知
     */
    public function businessNotifyTemplate()
    {
        $type = request()->get("type", "email");
        $config_service = new WebConfig();
        $shop_id = $this->instance_id;
        $template_detail = $config_service->getNoticeTemplateDetail($shop_id, $type);
        $template_type_list = $config_service->getNoticeTemplateType("", "business");
        for ($i = 0; $i < count($template_type_list); $i ++) {
            $template_code = $template_type_list[$i]["template_code"];
            $notify_type = $template_type_list[$i]["notify_type"];
            $is_enable = 0;
            $template_title = "";
            $template_content = "";
            $sign_name = "";
            foreach ($template_detail as $template_obj) {
                if ($template_obj["template_code"] == $template_code && $template_obj["notify_type"] == $notify_type) {
                    $is_enable = $template_obj["is_enable"];
                    $template_title = $template_obj["template_title"];
                    $template_content = str_replace(PHP_EOL, '', $template_obj["template_content"]);
                    $sign_name = $template_obj["sign_name"];
                    $notification_mode = $template_obj["notification_mode"];
                    break;
                }
            }
            $template_type_list[$i]["is_enable"] = $is_enable;
            $template_type_list[$i]["template_title"] = $template_title;
            $template_type_list[$i]["template_content"] = $template_content;
            $template_type_list[$i]["sign_name"] = $sign_name;
            $template_type_list[$i]["notification_mode"] = $notification_mode;
        }
        $template_item_list = $config_service->getNoticeTemplateItem($template_type_list[0]["template_code"]);
        $this->assign("template_type_list", $template_type_list);
        $this->assign("template_json", json_encode($template_type_list));
        $this->assign("template_select", $template_type_list[0]);
        $this->assign("template_item_list", $template_item_list);
        $this->assign("template_send_item_json", json_encode($template_item_list));
        if ($type == "email") {
            return view($this->style . 'Config/businessNotifyEmailTemplate');
        } else {
            return view($this->style . 'Config/businessNotifySmsTemplate');
        }
    }

    /**
     * 图片生成配置$this->
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function pictureUploadSetting()
    {
        $config_service = new WebConfig();
        if (request()->isAjax()) {
            $thumb_type = request()->post("thumb_type", "1");
            $upload_size = request()->post("upload_size", "0");
            $upload_ext = request()->post("upload_ext", "gif,jpg,jpeg,bmp,png");
            $data = array(
                "thumb_type" => $thumb_type,
                "upload_size" => $upload_size,
                "upload_ext" => $upload_ext
            );
            $retval = $config_service->setPictureUploadSetting($this->instance_id, json_encode($data));
            return AjaxReturn($retval);
        } else {
            $info = $config_service->getPictureUploadSetting($this->instance_id);
            $this->assign("info", $info);
            return view($this->style . 'Config/pictureUploadSetting');
        }
    }

    /**
     * 原路退款设置
     * 创建时间：2017年10月13日 16:16:41 王永杰
     */
    public function originalRoadRefundSetting()
    {
        $config_service = new WebConfig();
        $type = request()->get("type", "wechat"); // 默认微信
        if (empty($type)) {
            $type = "wechat"; // （type=），这种情况下获取到的值为空
        }
        
        // 设置默认值
        if ($type == 'wechat') {
            $original_road_refund_setting_info = [
                "is_use" => 0,
                "apiclient_cert" => "",
                "apiclient_key" => ""
            ];
        } elseif ($type == "alipay") {
            $original_road_refund_setting_info = [
                "is_use" => 0
            ];
        }
        
        $pay_list = $config_service->getPayConfig($this->instance_id);
        
        $wechat_is_use = 0; // 微信支付开启标识
        foreach ($pay_list as $v) {
            if ($v['key'] == "ALIPAY") {
                $alipay_is_use = $v['is_use'];
            } elseif ($v['key'] == 'WPAY') {
                $wechat_is_use = $v['is_use'];
            }
        }
        $this->assign("alipay_is_use", $alipay_is_use);
        $this->assign("wechat_is_use", $wechat_is_use);
        
        $data = $config_service->getOriginalRoadRefundSetting($this->instance_id, $type);
        if (! empty($data)) {
            $original_road_refund_setting_info = json_decode($data['value'], true);
        }
        $this->assign("original_road_refund_setting_info", $original_road_refund_setting_info);
        
        if ($type == "alipay") {
            
            $child_menu_list = array(
                array(
                    'url' => "config/originalroadrefundsetting?type=wechat",
                    'menu_name' => "微信配置",
                    "active" => 0
                ),
                array(
                    'url' => "config/originalroadrefundsetting?type=alipay",
                    'menu_name' => "支付宝配置",
                    "active" => 1
                )
            );
        } else {
            $child_menu_list = array(
                array(
                    'url' => "config/originalroadrefundsetting?type=wechat",
                    'menu_name' => "微信配置",
                    "active" => 1
                ),
                array(
                    'url' => "config/originalroadrefundsetting?type=alipay",
                    'menu_name' => "支付宝配置",
                    "active" => 0
                )
            );
        }
        $this->assign('child_menu_list', $child_menu_list);
        $this->assign("type", $type);
        return view($this->style . "Config/originalRoadRefundSetting");
    }

    /**
     * 设置原路退款信息 ajax
     * 创建时间：2017年10月13日 18:12:25 王永杰
     *
     * @return number|boolean
     */
    public function setOriginalRoadRefundSetting()
    {
        $type = request()->post("type", "");
        $value = request()->post("value", "");
        $res = 0;
        
        if (! empty($type) && ! empty($value)) {
            $config_service = new WebConfig();
            $res = $config_service->setOriginalRoadRefundSetting($this->instance_id, $type, $value);
        }
        
        return $res;
    }

    /**
     * 添加首页公告
     */
    public function addHomeNotice()
    {
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "config/usernotice",
                    'menu_name' => "首页公告",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "Config/addHomeNotice");
    }

    /**
     * 编辑公告
     */
    public function updateHomeNotice()
    {
        $id = request()->get("id", 0);
        $platform = new Platform();
        $info = $platform->getNoticeDetail($id);
        if (empty($info)) {
            $this->error("没有获取到公告信息");
        } else {
            $this->assign("info", $info);
        }
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "config/usernotice",
                    'menu_name' => "首页公告",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "Config/updateHomeNotice");
    }

    /**
     * 删除公告
     */
    public function deleteNotice()
    {
        if (request()->isAjax()) {
            $platform = new Platform();
            $id = request()->post('id', '');
            if (empty($id)) {
                $this->error('未获取到信息');
            }
            $retval = $platform->deleteNotice($id);
            return AjaxReturn($retval);
        }
    }

    /**
     * 添加或修改首页公告
     */
    public function addOrModifyHomeNotice()
    {
        if (request()->isAjax()) {
            $id = request()->post("id", 0);
            $title = request()->post("title", "");
            $content = request()->post("content", "");
            $sort = request()->post("sort", 0);
            $platform = new Platform();
            $res = $platform->addOrModifyNotice($title, $content, $this->instance_id, $sort, $id);
            return AjaxReturn($res);
        }
    }

    /**
     * 修改公告排序
     *
     * @return multitype:unknown
     */
    public function modifyNoticeSort()
    {
        if (request()->isAjax()) {
            $platform = new Platform();
            $id = request()->post('id', '');
            $sort = request()->post('sort', '');
            $retval = $platform->updateNoticeSort($sort, $id);
            return AjaxReturn($retval);
        }
    }

    /**
     * 配置伪静态路由规则
     */
    public function customPseudoStaticRule()
    {
        if (request()->isAjax()) {
            $webSite = new WebSite();
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $rule_list = $webSite->getUrlRouteList($page_index, $page_size);
            return $rule_list;
        }
        return view($this->style . "Config/customPseudoStaticRule");
    }

    /**
     * 添加路由规则
     */
    public function addRoutingRules()
    {
        if (request()->isAjax()) {
            $rule = request()->post("rule", "");
            $route = request()->post("route", "");
            $is_open = request()->post("is_open", 1);
            $route_model = request()->post("route_model", 1);
            $remark = request()->post("remark", "");
            $webSite = new WebSite();
            $res = $webSite->addUrlRoute($rule, $route, $is_open, $route_model, $remark);
            return AjaxReturn($res);
        }
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "config/customPseudoStaticRule",
                    'menu_name' => "伪静态路由设置",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "Config/addRoutingRules");
    }

    /**
     * 编辑路由规则
     */
    public function updateRoutingRule()
    {
        $webSite = new WebSite();
        if (request()->isAjax()) {
            $routeid = request()->post("routeid", "");
            $rule = request()->post("rule", "");
            $route = request()->post("route", "");
            $is_open = request()->post("is_open", 1);
            $route_model = request()->post("route_model", 1);
            $remark = request()->post("remark", "");
            $res = $webSite->updateUrlRoute($routeid, $rule, $route, $is_open, $route_model, $remark);
            return AjaxReturn($res);
        }
        $routeid = request()->get("routeid", "");
        $routeDetail = $webSite->getUrlRouteDetail($routeid);
        if (empty($routeDetail)) {
            $this->error("未获取路由规则信息");
        } else {
            $this->assign("routeDetail", $routeDetail);
        }
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "config/customPseudoStaticRule",
                    'menu_name' => "伪静态路由设置",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "Config/updateRoutingRules");
    }

    /**
     * 判断路由规则或者路由地址是否存在
     */
    public function url_route_if_exists()
    {
        if (request()->isAjax()) {
            $type = request()->post("type", "");
            $value = request()->post("value", "");
            $webSite = new WebSite();
            $res = $webSite->url_route_if_exists($type, $value);
            return $res;
        }
    }

    /**
     * 删除伪静态路由规则
     */
    public function delete_url_route()
    {
        if (request()->isAjax()) {
            $routeid = request()->post("routeid", "");
            $webSite = new WebSite();
            $res = $webSite->delete_url_route($routeid);
            return AjaxReturn($res);
        }
    }

    /**
     * 修改虚拟商品配置信息
     * 开启后，显示虚拟商品相关的系统菜单
     * 禁用后，隐藏虚拟商品相关的系统菜单
     */
    public function settingVirtualGoodsConfigInfo()
    {
        $config = new WebConfig();
        $is_enabled = $config->settingVirtualGoodsConfigInfo($this->instance_id);
    
        return $is_enabled;
    }
}