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
namespace app\wap\controller;

use data\extend\WchatOauth;
use data\service\Address;
use data\service\Config;
use data\service\Goods as GoodsService;
use data\service\Member as Member;
use data\service\Shop;
use data\service\Config as WebConfig;
use data\service\User;
use data\service\WebSite as WebSite;
use think\Controller;
use think\Cookie;
use think\Session;
\think\Loader::addNamespace('data', 'data/');

class BaseController extends Controller
{

    public $user;

    protected $uid;

    protected $instance_id;

    protected $is_member;

    protected $shop_name;

    protected $user_name;

    protected $shop_id;

    /**
     * 平台logo
     *
     * @var unknown
     */
    protected $logo;

    protected $share_icon;

    public $web_site;

    public $style;

    public $app_login_name;

    public $app_login_password;
    
    // 自定义模板开启状态 0 不启用 1 启用
    public $custom_template_is_enable;

    public function __construct()
    {
        Cookie::delete("default_client"); // 还原手机端访问
        $this->app_login_name = request()->get('login_name', '');
        $this->app_login_password = request()->get('login_password', '');
        if (! empty($this->app_login_name) && ! empty($this->app_login_password)) {
            $this->user = new Member();
            $retval = $this->user->login($this->app_login_name, $this->app_login_password);
        }
        
        // getWapCache();//开启缓存
        parent::__construct();
        $this->initInfo();
    }

    public function initInfo()
    {
        $this->user = new Member();
        $this->web_site = new WebSite();
        $config = new Config();
        
        $web_info = $this->web_site->getWebSiteInfo();
        
        // wap端关闭后
        if ($web_info['wap_status'] == 3 && $web_info['web_status'] == 1) {
            Cookie::set("default_client", "shop");
            $this->redirect(__URL(\think\Config::get('view_replace_str.SHOP_MAIN') . "/shop"));
        } else 
            if ($web_info['wap_status'] == 2) {
                webClose($web_info['close_reason']);
            } else 
                if (($web_info['wap_status'] == 3 && $web_info['web_status'] == 3) || ($web_info['wap_status'] == 3 && $web_info['web_status'] == 2)) {
                    webClose($web_info['close_reason']);
                }
        
        $this->uid = $this->user->getSessionUid();
        $this->instance_id = $this->user->getSessionInstanceId();
        $this->shop_id = 0;
        $this->shop_name = $this->user->getInstanceName();
        $this->logo = $web_info['logo'];
        $this->custom_template_is_enable = $config->getIsEnableCustomTemplate($this->instance_id);
        $this->assign("custom_template_is_enable", $this->custom_template_is_enable);
        
        $this->share_icon = $web_info['web_wechat_share_logo'];
        // 使用那个手机模板
        $use_wap_template = $config->getUseWapTemplate($this->instance_id);
        
        if (empty($use_wap_template)) {
            $use_wap_template['value'] = 'default';
        }
        // 检查模版是否存在
        if (! checkTemplateIsExists("wap", $use_wap_template['value'])) {
            $this->error("模板配置有误，请联系商城管理员");
        }
        
        $this->style = "wap/" . $use_wap_template['value'] . "/";
        $this->assign("style", "wap/" . $use_wap_template['value']);
        
        if (! request()->isAjax()) {
            if (empty($this->uid)) {
                $this->wchatLogin();
            }
            $this->assign("uid", $this->uid);
            $this->assign("shop_id", $this->instance_id);
            $this->assign("title", $web_info['title']);
            $this->assign("platform_shopname", $this->shop_name); // 平台店铺名称
            $seoconfig = $config->getSeoConfig($this->instance_id);
            $this->assign("seoconfig", $seoconfig);
        }
    }

    /**
     * 判断当前用户是否需要绑定
     */
    public function memberTelIsBind()
    {
        $bing_status = session::get("member_bind_first");
        $webconfig = new Config();
        $register_and_visit = $webconfig->getRegisterAndVisit($this->shop_id);
        $register_config = json_decode($register_and_visit['value'], true);
        if (! empty($bing_status) && $bing_status == 1 && $register_config["is_requiretel"] == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检测微信浏览器并且自动登录
     */
    public function wchatLogin()
    {
        $bind_status = $this->memberTelIsBind();
        // 微信浏览器自动登录
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') && $bind_status == false) {
            // 针对微信分销系统获取上级
            if (NS_VERSION == NS_VER_B2C_FX) {
                $source_uid = request()->get('source_uid', '');
                $source_shop_id = request()->get('shop_id', 0);
                if (! empty($source_uid)) {
                    $_SESSION['source_uid'] = $source_uid;
                    $_SESSION['source_shop_id'] = $source_shop_id;
                }
            }
            if (empty($_SESSION['request_url'])) {
                $_SESSION['request_url'] = request()->url(true);
            }
            $config = new Config();
            $wchat_config = $config->getInstanceWchatConfig(0);
            if (empty($wchat_config['value']['appid'])) {
                return;
            }
            $wchat_oauth = new WchatOauth();
            $domain_name = \think\Request::instance()->domain();
            if (! empty($_COOKIE[$domain_name . "member_access_token"])) {
                $token = json_decode($_COOKIE[$domain_name . "member_access_token"], true);
            } else {
                $token = $wchat_oauth->get_member_access_token();
                if (! empty($token['access_token'])) {
                    setcookie($domain_name . "member_access_token", json_encode($token));
                    Session::set($domain_name . "member_access_token", json_encode($token));
                }
            }
            if (! empty($token['openid'])) {
                $webconfig = new Config();
                $register_and_visit = $webconfig->getRegisterAndVisit($this->shop_id);
                $register_config = json_decode($register_and_visit['value'], true);
                $retval = $this->user->wchatLogin($token['openid']);
                // 当前openid 没有在数据库中存在 并且 后台没有开启 强制绑定会员
                if ($retval == USER_NBUND && $register_config["is_requiretel"] == 1) {
                    session::set("member_bind_first", 1);
                    return;
                }
                if (! empty($token['unionid'])) {
                    $wx_unionid = $token['unionid'];
                    $retval = $this->user->wchatUnionLogin($wx_unionid);
                    if ($retval == 1) {
                        $this->user->modifyUserWxhatLogin($token['openid'], $wx_unionid);
                    } elseif ($retval == USER_LOCK) {
                        $redirect = __URL(__URL__ . "/wap/login/userlock");
                        $this->redirect($redirect);
                    } else {
                        $retval = $this->user->wchatLogin($token['openid']);
                        if ($retval == USER_NBUND) {
                            $info = $wchat_oauth->get_oauth_member_info($token);
                            
                            $result = $this->user->registerMember('', '123456', '', '', '', '', $token['openid'], $info, $wx_unionid);
                        } elseif ($retval == USER_LOCK) {
                            // 锁定跳转
                            $redirect = __URL(__URL__ . "/wap/login/userlock");
                            $this->redirect($redirect);
                        }
                    }
                } else {
                    $wx_unionid = '';
                    $retval = $this->user->wchatLogin($token['openid']);
                    if ($retval == USER_NBUND) {
                        $info = $wchat_oauth->get_oauth_member_info($token);
                        
                        $result = $this->user->registerMember('', '123456', '', '', '', '', $token['openid'], $info, $wx_unionid);
                        // $this->memberTelIsBind();
                    } elseif ($retval == USER_LOCK) {
                        // 锁定跳转
                        $redirect = __URL(__URL__ . "/wap/login/userlock");
                        $this->redirect($redirect);
                    }
                }
                
                echo "<script language=JavaScript> window.location.href='" . $_SESSION['request_url'] . "'</script>";
                exit();
            }
        }
    }

    /**
     * 获取分享相关信息
     * 首页、商品详情、推广二维码、店铺二维码
     *
     * @return multitype:string unknown
     */
    public function getShareContents()
    {
        $shop_id = $this->shop_id;
        // 标识当前分享的类型[shop、goods、qrcode_shop、qrcode_my]
        $flag = request()->post('flag', 'shop');
        $goods_id = request()->post('goods_id', '');
        
        if (strstr($this->share_icon, "http")) {
            $share_logo = $this->share_icon;
        } else {
            $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $this->share_icon; // 分享时，用到的logo，默认是平台分享图标
        }
        $shop = new Shop();
        $config = $shop->getShopShareConfig($shop_id);
        
        // 当前用户名称
        $current_user = "";
        $user_info = null;
        if (empty($goods_id)) {
            switch ($flag) {
                case "shop":
                    if (! empty($this->uid)) {
                        
                        $user = new User();
                        $user_info = $user->getUserInfoByUid($this->uid);
                        $share_url = __URL(__URL__ . '/wap/index?source_uid=' . $this->uid);
                        $current_user = "分享人：" . $user_info["nick_name"];
                    } else {
                        $share_url = __URL(__URL__ . '/wap/index');
                    }
                    break;
                case "qrcode_shop":
                    
                    $user = new User();
                    $user_info = $user->getUserInfoByUid($this->uid);
                    $share_url = __URL(__URL__ . '/wap/Login/getshopqrcode?source_uid=' . $this->uid);
                    $current_user = "分享人：" . $user_info["nick_name"];
                    break;
                case "qrcode_my":
                    
                    $user = new User();
                    $user_info = $user->getUserInfoByUid($this->uid);
                    $share_url = __URL(__URL__ . '/wap/Login/getWchatQrcode?source_uid=' . $this->uid);
                    $current_user = "分享人：" . $user_info["nick_name"];
                    break;
            }
        } else {
            if (! empty($this->uid)) {
                $user = new User();
                $user_info = $user->getUserInfoByUid($this->uid);
                $share_url = __URL(__URL__ . '/wap/Goods/goodsDetail?id=' . $goods_id . '&source_uid=' . $this->uid);
                $current_user = "分享人：" . $user_info["nick_name"];
            } else {
                $share_url = __URL(__URL__ . '/wap/Goods/goodsDetail?id=' . $goods_id);
            }
        }
        
        // 店铺分享
        $shop_name = $this->shop_name;
        $share_content = array();
        switch ($flag) {
            case "shop":
                $share_content["share_title"] = $config["shop_param_1"] . $shop_name;
                $share_content["share_contents"] = $config["shop_param_2"] . " " . $config["shop_param_3"];
                $share_content['share_nick_name'] = $current_user;
                break;
            case "goods":
                // 商品分享
                $goods = new GoodsService();
                $goods_detail = $goods->getGoodsDetail($goods_id);
                $share_content["share_title"] = $goods_detail["goods_name"];
                $share_content["share_contents"] = $config["goods_param_1"] . "￥" . $goods_detail["price"] . ";" . $config["goods_param_2"];
                $share_content['share_nick_name'] = $current_user;
                if (count($goods_detail["img_list"]) > 0) {
                    if (strstr($goods_detail["img_list"][0]["pic_cover_mid"], "http")) {
                        $share_logo = $goods_detail["img_list"][0]["pic_cover_mid"];
                    } else {
                        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $goods_detail["img_list"][0]["pic_cover_mid"]; // 用商品的第一个图片
                    }
                }
                break;
            case "qrcode_shop":
                // 二维码分享
                if (! empty($user_info)) {
                    $share_content["share_title"] = $config["shop_param_1"] . '分享'; // $shop_name . "二维码分享";
                    $share_content["share_contents"] = $config["shop_param_2"] . ";" . $config["shop_param_3"];
                    $share_content['share_nick_name'] = '分享人：' . $user_info["nick_name"];
                    if (! empty($user_info['user_headimg'])) {
                        if (strstr($user_info['user_headimg'], "http")) {
                            $share_logo = $user_info['user_headimg'];
                        } else {
                            $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $user_info['user_headimg'];
                        }
                    } else {
                        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__TEMP__') . '/wap/' . NS_TEMPLATE . '/public/images/member_default.png';
                    }
                }
                break;
            case "qrcode_my":
                // 二维码分享
                if (! empty($user_info)) {
                    $share_content["share_title"] = $shop_name . "二维码分享";
                    $share_content["share_contents"] = $config["qrcode_param_1"] . ";" . $config["qrcode_param_2"];
                    $share_content['share_nick_name'] = '分享人：' . $user_info["nick_name"];
                    if (! empty($user_info['user_headimg'])) {
                        if (strstr($user_info['user_headimg'], "http")) {
                            $share_logo = $user_info['user_headimg'];
                        } else {
                            $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $user_info['user_headimg'];
                        }
                    } else {
                        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__TEMP__') . '/wap/' . NS_TEMPLATE . '/public/images/member_default.png';
                    }
                }
                break;
        }
        $share_content["share_url"] = $share_url;
        $share_content["share_img"] = $share_logo;
        return $share_content;
    }

    /**
     * 获取分享相关票据
     */
    public function getShareTicket()
    {
        $config = new Config();
        $auth_info = $config->getInstanceWchatConfig($this->instance_id);
        // 获取票据
        if (isWeixin() && ! empty($auth_info['value']['appid'])) {
            // 针对单店版获取微信票据
            $wexin_auth = new WchatOauth();
            $signPackage['appId'] = $auth_info['value']['appid'];
            $signPackage['jsTimesTamp'] = time();
            $signPackage['jsNonceStr'] = $wexin_auth->get_nonce_str();
            $jsapi_ticket = $wexin_auth->jsapi_ticket();
            $signPackage['ticket'] = $jsapi_ticket;
            $url = request()->url(true);
            $Parameters = "jsapi_ticket=" . $signPackage['ticket'] . "&noncestr=" . $signPackage['jsNonceStr'] . "&timestamp=" . $signPackage['jsTimesTamp'] . "&url=" . $url;
            $signPackage['jsSignature'] = sha1($Parameters);
            return $signPackage;
        } else {
            $signPackage = array(
                'appId' => '',
                'jsTimesTamp' => '',
                'jsNonceStr' => '',
                'ticket' => '',
                'jsSignature' => ''
            );
            return $signPackage;
        }
    }

    /**
     * 获取省列表
     */
    public function getProvince()
    {
        $address = new Address();
        $province_list = $address->getProvinceList();
        return $province_list;
    }

    /**
     * 获取城市列表
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
     * 获取区域地址
     */
    public function getDistrict()
    {
        $address = new Address();
        $city_id = request()->post('city_id', 0);
        $district_list = $address->getDistrictList($city_id);
        return $district_list;
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