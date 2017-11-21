<?php
/**
 * Login.php
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

use data\extend\ThinkOauth as ThinkOauth;
use data\extend\WchatOauth;
use data\service\Config as WebConfig;
use data\service\Goods as GoodsService;
use data\service\Member as Member;
use data\service\Shop;
use data\service\User;
use data\service\WebSite as WebSite;
use data\service\Weixin;
use think\Controller;
use think\Session;
use think\Cookie;
\think\Loader::addNamespace('data', 'data/');

/**
 * 前台用户登录
 *
 * @author Administrator
 *        
 */
class Login extends Controller
{

    public $user;

    public $web_site;

    public $style;

    public $logo;

    protected $instance_id;

    protected $shop_name;
    
    // 验证码配置
    public $login_verify_code;

    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

    public function init()
    {
        $this->user = new Member();
        $this->web_site = new WebSite();
        $web_info = $this->web_site->getWebSiteInfo();
        
        $this->assign("platform_shopname", $this->user->getInstanceName()); // 平台店铺名称
        $this->assign("title", $web_info['title']);
        $this->logo = $web_info['logo'];
        $this->shop_name = $this->user->getInstanceName();
        $this->instance_id = 0;
        
        // 是否开启验证码
        $web_config = new WebConfig();
        $this->login_verify_code = $web_config->getLoginVerifyCodeConfig($this->instance_id);
        $this->assign("login_verify_code", $this->login_verify_code["value"]);
        
        // 是否开启qq跟微信
        $qq_info = $web_config->getQQConfig($this->instance_id);
        $Wchat_info = $web_config->getWchatConfig($this->instance_id);
        $this->assign("qq_info", $qq_info);
        $this->assign("Wchat_info", $Wchat_info);
        
        $seoconfig = $web_config->getSeoConfig($this->instance_id);
        $this->assign("seoconfig", $seoconfig);
        
        // 使用那个手机模板
        $use_wap_template = $web_config->getUseWapTemplate($this->instance_id);
        if (empty($use_wap_template)) {
            $use_wap_template['value'] = 'default';
        }
        if (! checkTemplateIsExists("wap", $use_wap_template['value'])) {
            $this->error("模板配置有误，请联系商城管理员");
        }
        $this->style = "wap/" . $use_wap_template['value']."/";
        $this->assign("style", "wap/" . $use_wap_template['value']);
    }
    
    /**
     * 判断wap端是否开启
     */
    public function determineWapWhetherToOpen(){
        $this->web_site = new WebSite();
        $web_info = $this->web_site->getWebSiteInfo();
        if($web_info['wap_status'] == 3 && $web_info['web_status'] == 1){
            Cookie::set("default_client", "shop");
            $this->redirect(__URL(\think\Config::get('view_replace_str.SHOP_MAIN')."/shop"));
        }else if ($web_info['wap_status'] == 2) {
            webClose($web_info['close_reason']);
        }else if(($web_info['wap_status'] == 3 && $web_info['web_status'] == 3)||($web_info['wap_status'] == 3 && $web_info['web_status'] == 2)){
            webClose($web_info['close_reason']);
        }
    }
    
    /**
     * 检测微信浏览器并且自动登录
     */
    public function wchatLogin()
    {
        $this->determineWapWhetherToOpen();
        // 微信浏览器自动登录
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            
            if (empty($_SESSION['request_url'])) {
                $_SESSION['request_url'] = request()->url(true);
            }
            $domain_name = \think\Request::instance()->domain();
            if (! empty($_COOKIE[$domain_name . "member_access_token"])) {
                $token = json_decode($_COOKIE[$domain_name . "member_access_token"], true);
            } else {
                $wchat_oauth = new WchatOauth();
                $token = $wchat_oauth->get_member_access_token();
                if (! empty($token['access_token'])) {
                    setcookie($domain_name . "member_access_token", json_encode($token));
                }
            }
            $wchat_oauth = new WchatOauth();
            if (! empty($token['openid'])) {
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
                    } elseif ($retval == USER_LOCK) {
                        // 锁定跳转
                        $redirect = __URL(__URL__ . "/wap/login/userlock");
                        $this->redirect($redirect);
                    }
                }
                
                if (! empty($_SESSION['login_pre_url'])) {
                    $this->redirect($_SESSION['login_pre_url']);
                } else {
                    $redirect = __URL(__URL__ . "/wap/member");
                    $this->redirect($redirect);
                }
            }
        }
    }

    public function index()
    {
        $this->determineWapWhetherToOpen();
        if (request()->isAjax()) {
            $bind_message_info = json_decode(Session::get("bind_message_info"), true);
            $user_name = request()->post('username', '');
            $password = request()->post('password', '');
            $mobile = request()->post('mobile', '');
            $sms_captcha = request()->post('sms_captcha', '');
            if (! empty($user_name)) {
                $retval = $this->user->login($user_name, $password);
            } else {
                $sms_captcha_code = Session::get('mobileVerificationCode');
                $sendMobile = Session::get('sendMobile');
                // if ($sms_captcha == $sms_captcha_code && $sendMobile == $mobile && ! empty($sms_captcha_code)) {
                if ($sms_captcha == $sms_captcha_code && ! empty($sms_captcha_code)) {
                    $retval = $this->user->login($mobile, '');
                } else {
                    $retval = - 10;
                }
            }
            if ($retval == 1) {
                if (! empty($_SESSION['login_pre_url'])) {
                    $retval = [
                        'code' => 1,
                        'url' => $_SESSION['login_pre_url']
                    ];
                } else {
                    $retval = [
                        'code' => 2,
                        'url' => 'index/index'
                    ];
                }
                if (empty($user_name)) {
                    $user_name = $mobile;
                    $password = "";
                }
                // 微信会员绑定
                $this->wchatBindMember($user_name, $password, $bind_message_info);
            } else {
                $retval = AjaxReturn($retval);
            }
            return $retval;
        }
        $this->getWchatBindMemberInfo();
        // 没有登录首先要获取上一页
        $pre_url = '';
        $_SESSION['bund_pre_url'] = '';
        if (! empty($_SERVER['HTTP_REFERER'])) {
            $pre_url = $_SERVER['HTTP_REFERER'];
            if (strpos($pre_url, 'login')) {
                $pre_url = '';
            }
            $_SESSION['login_pre_url'] = $pre_url;
        }
        $config = new WebConfig();
        $instanceid = 0;
        // 登录配置
        $web_config = new WebConfig();
        $loginConfig = $web_config->getLoginConfig();
        
        $loginCount = 0;
        if ($loginConfig['wchat_login_config']['is_use'] == 1) {
            $loginCount ++;
        }
        if ($loginConfig['qq_login_config']['is_use'] == 1) {
            $loginCount ++;
        }
        $this->assign("loginCount", $loginCount);
        $this->assign("loginConfig", $loginConfig);
        return view($this->style . 'Login/login');
    }

    /**
     * 微信绑定用户
     */
    public function wchatBindMember($user_name, $password, $bind_message_info)
    {
        session::set("member_bind_first", null);
        if (! empty($bind_message_info)) {
            $config = new WebConfig();
            $register_and_visit = $config->getRegisterAndVisit(0);
            $register_config = json_decode($register_and_visit['value'], true);
            if (! empty($register_config) && $register_config["is_requiretel"] == 1 && $bind_message_info["is_bind"] == 1 && ! empty($bind_message_info["token"])) {
                $token = $bind_message_info["token"];
                if (! empty($token['openid'])) {
                    $this->user->updateUserWchat($user_name, $password, $token['openid'], $bind_message_info['info'], $bind_message_info['wx_unionid']);
                }
            }
        }
    }

    /**
     * 获取需要绑定的信息放到session中
     */
    public function getWchatBindMemberInfo()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            $config = new WebConfig();
            $wchat_config = $config->getInstanceWchatConfig(0);
            $register_and_visit = $config->getRegisterAndVisit(0);
            $register_config = json_decode($register_and_visit['value'], true);
            // 当前openid 没有在数据库中存在 并且 后台没有开启 强制绑定会员
            $token = "";
            $is_bind = 1;
            $info = "";
            $wx_unionid = "";
            $domain_name = \think\Request::instance()->domain();
            if (! empty($wchat_config['value']['appid']) && $register_config["is_requiretel"] == 1) {
                $wchat_oauth = new WchatOauth();
                $member_access_token = Session::get($domain_name . "member_access_token");
                if (! empty($member_access_token)) {
                    $token = json_decode($member_access_token, true);
                } else {
                    $token = $wchat_oauth->get_member_access_token();
                    if (! empty($token['access_token'])) {
                        Session::set($domain_name . "member_access_token", json_encode($token));
                    }
                }
                if (! empty($token['openid'])) {
                    $user_count = $this->user->getUserCountByOpenid($token['openid']);
                    if ($user_count == 0) {
                        // 更新会员的微信信息
                        $info = $wchat_oauth->get_oauth_member_info($token);
                        if (! empty($token['unionid'])) {
                            $wx_unionid = $token['unionid'];
                        }
                    } else {
                        $is_bind = 0;
                    }
                }
            }
            $bind_message = array(
                "token" => $token,
                "is_bind" => $is_bind,
                "info" => $info,
                "wx_unionid" => $wx_unionid
            );
            Session::set("bind_message_info", json_encode($bind_message));
        }
    }

    /**
     * 第三方登录登录
     */
    public function oauthLogin()
    {
        $config = new WebConfig();
        $type = request()->get('type', '');
        if ($type == "WCHAT") {
            $config_info = $config->getWchatConfig($this->instance_id);
            if (empty($config_info["value"]["APP_KEY"]) || empty($config_info["value"]["APP_SECRET"])) {
                $this->error("当前系统未设置微信第三方登录!");
            }
            if (isWeixin()) {
                $this->wchatLogin();
                if (! empty($_SESSION['login_pre_url'])) {
                    $this->redirect($_SESSION['login_pre_url']);
                } else {
                    $redirect = __URL(__URL__ . "/wap/member/index");
                    $this->redirect($redirect);
                    $this->wchatbinding();
                }
            }
        } else 
            if ($type == "QQLOGIN") {
                $config_info = $config->getQQConfig($this->instance_id);
                if (empty($config_info["value"]["APP_KEY"]) || empty($config_info["value"]["APP_SECRET"])) {
                    $this->error("当前系统未设置QQ第三方登录!");
                }
            }
        $_SESSION['login_type'] = $type;
        
        $test = ThinkOauth::getInstance($type);
        $this->redirect($test->getRequestCodeURL());
    }

    /**
     * 微信登录后绑定手机号
     */
    public function modifybindingmobile()
    {
        $user = new User();
        if (request()->isAjax()) {
            $user = new User();
            $userid = request()->post('uid', '');
            $user_tel = request()->post('mobile', '');
            $sms_captcha = request()->post('sms_captcha', '');
            $sms_captcha_code = Session::get('mobileVerificationCode');
            $sendMobile = Session::get('sendMobile');
            // if ($sms_captcha == $sms_captcha_code && $sendMobile == $mobile && ! empty($sms_captcha_code)) {
            if (($sms_captcha == $sms_captcha_code && ! empty($sms_captcha_code)) || ! empty($user_tel)) {
                $result = $user->updateUsertelByUserid($userid, $user_tel);
            } else {
                $result = - 10;
            }
            
            return AjaxReturn($result);
        }
    }

    /**
     * qq登录返回
     */
    public function callback()
    {
        $code = request()->get('code', '');
        if (empty($code))
            die();
        if ($_SESSION['login_type'] == 'QQLOGIN') {
            $qq = ThinkOauth::getInstance('QQLOGIN');
            $token = $qq->getAccessToken($code);
            if (! empty($token['openid'])) {
                if (! empty($_SESSION['bund_pre_url'])) {
                    // 1.检测当前qqopenid是否已经绑定，如果已经绑定直接返回绑定失败
                    $bund_pre_url = $_SESSION['bund_pre_url'];
                    $_SESSION['bund_pre_url'] = '';
                    $is_bund = $this->user->checkUserQQopenid($token['openid']);
                    if ($is_bund == 0) {
                        // 2.绑定操作
                        $qq = ThinkOauth::getInstance('QQLOGIN', $token);
                        $data = $qq->call('user/get_user_info');
                        $_SESSION['qq_info'] = json_encode($data);
                        // 执行用户信息更新user服务层添加更新绑定qq函数（绑定，解绑）
                        $res = $this->user->bindQQ($token['openid'], json_encode($data));
                        // 如果执行成功执行跳转
                        
                        if ($res) {
                            $this->success('绑定成功', $bund_pre_url);
                        } else {
                            $this->error('绑定失败', $bund_pre_url);
                        }
                    } else {
                        $this->error('该qq已经绑定', $bund_pre_url);
                    }
                } else {
                    $retval = $this->user->qqLogin($token['openid']);
                    // 已经绑定
                    if ($retval == 1) {
                        if (! empty($_SESSION['login_pre_url'])) {
                            $this->redirect($_SESSION['login_pre_url']);
                        } else {
                            $redirect = __URL(__URL__ . "/member/index");
                            $this->redirect($redirect);
                        }
                        // $this->success("登录成功", "Index/index");
                    }
                    if ($retval == USER_NBUND) {
                        $qq = ThinkOauth::getInstance('QQLOGIN', $token);
                        $data = $qq->call('user/get_user_info');
                        $_SESSION['qq_info'] = json_encode($data);
                        $result = $this->user->registerMember('', '123456', '', '', $token['openid'], json_encode($data), '', '', '');
                        if ($result > 0) {
                            if (! empty($_SESSION['login_pre_url'])) {
                                $this->redirect($_SESSION['login_pre_url']);
                            } else
                                $redirect = __URL(__URL__ . "/member/index");
                            $this->redirect($redirect);
                        }
                    }
                }
            }
        } elseif ($_SESSION['login_type'] == 'WCHAT') {
            $wchat = ThinkOauth::getInstance('WCHAT');
            $token = $wchat->getAccessToken($code);
            if (! empty($token['unionid'])) {
                $retval = $this->user->wchatUnionLogin($token['unionid']);
                
                // 已经绑定
                if ($retval == 1) {
                    if (! empty($_SESSION['login_pre_url'])) {
                        $this->redirect($_SESSION['login_pre_url']);
                    } else {
                        $redirect = __URL(__URL__ . "/member/index");
                        $this->redirect($redirect);
                    }
                    
                    // $this->success("登录成功", "Index/index");
                }
            }
            if ($retval == USER_NBUND) {
                // 2.绑定操作
                $wchat = ThinkOauth::getInstance('WCHAT', $token);
                $data = $wchat->call('sns/userinfo');
                $_SESSION['wx_info'] = json_encode($data);
                $result = $this->user->registerMember('', '123456', '', '', '', '', '', json_encode($data), $token['unionid']);
                if ($result > 0) {
                    if (! empty($_SESSION['login_pre_url'])) {
                        $this->redirect($_SESSION['login_pre_url']);
                    } else {
                        $redirect = __URL(__URL__ . "/member/index");
                        $this->redirect($redirect);
                    }
                }
            }
        }
    }

    /**
     * 微信授权登录返回
     */
    public function wchatCallBack()
    {
        $code = request()->get('code', '');
        if (empty($code))
            die();
        $wchat = ThinkOauth::getInstance('WCHATLOGIN');
        $token = $wchat->getAccessToken($code);
        $wchat = ThinkOauth::getInstance('WCHATLOGIN', $token);
        $data = $wchat->call('/sns/userinfo');
        var_dump($data);
    }

    /**
     * 注册用户
     */
    public function addUser()
    {
        $user_name = request()->post('username', '');
        $password = request()->post('password', '');
        $email = request()->post('email', '');
        $mobile = request()->post('mobile', '');
        $is_system = 0;
        $is_member = 1;
        $qq_openid = request()->post('qq_openid', '');
        $qq_info = isset($_SESSION['qq_info']) ? $_SESSION['qq_info'] : '';
        $wx_openid = request()->post('wx_openid', '');
        $wx_info = request()->post('wx_info', '');
        if (empty($user_name)) {
            return AjaxReturn(0);
        } else {
            $result = $this->user->registerMember($user_name, $password, $email, $mobile, $qq_openid, $qq_info, $wx_openid, $wx_info);
            if ($result > 0) {
                $this->user->qqLogin($qq_openid);
            }
        }
        
        return AjaxReturn($result);
    }

    /**
     * 注册账户
     */
    public function register()
    {
        $this->determineWapWhetherToOpen();
        if (request()->isAjax()) {
            $bind_message_info = json_decode(Session::get("bind_message_info"), true);
            $member = new Member();
            $user_name = request()->post('username', '');
            $password = request()->post('password', '');
            $email = request()->post('email', '');
            $mobile = request()->post('mobile', '');
            $sendMobile = Session::get('sendMobile');
            if (empty($mobile)) {
                $retval = $member->registerMember($user_name, $password, $email, $mobile, '', '', '', '', '');
            } else {
                if ($sendMobile == $mobile) {
                    $retval = $member->registerMember($user_name, $password, $email, $mobile, '', '', '', '', '');
                } else 
                    if (empty($user_name)) {
                        $retval = $member->registerMember($user_name, $password, $email, $mobile, '', '', '', '', '');
                    }
            }
            if ($retval > 0) {
                // 微信的会员绑定
                if (empty($user_name)) {
                    $user_name = $mobile;
                }
                $this->wchatBindMember($user_name, $password, $bind_message_info);
            }
            
            return AjaxReturn($retval);
        } else {
            $this->getWchatBindMemberInfo();
            $config = new WebConfig();
            $instanceid = 0;
            // 判断是否开启邮箱注册
            $reg_config_info = $config->getRegisterAndVisit($instanceid);
            $reg_config = json_decode($reg_config_info["value"], true);
            if ($reg_config["is_register"] != 1) {
                $this->error("抱歉,商城暂未开放注册!", __URL__ . "/login/index");
            }
            if (strpos($reg_config['register_info'], "plain") === false && strpos($reg_config['register_info'], "mobile") === false) {
                $this->error("抱歉,商城暂未开放注册!", __URL__ . "/login/index");
            }
            $this->assign("reg_config", $reg_config);
            // 登录配置
            $web_config = new WebConfig();
            $loginConfig = $web_config->getLoginConfig();
            
            $loginCount = 0;
            if ($loginConfig['wchat_login_config']['is_use'] == 1) {
                $loginCount ++;
            }
            if ($loginConfig['qq_login_config']['is_use'] == 1) {
                $loginCount ++;
            }
            
            $code_config = $web_config->getLoginVerifyCodeConfig($this->instance_id);
            $this->assign('code_config', $code_config["value"]);
            
            $this->assign("loginCount", $loginCount);
            $this->assign("loginConfig", $loginConfig);
            
            return view($this->style . 'Login/register');
        }
        /*
         * if ($_POST) {
         * $member = new Member();
         * $user_name = isset($_POST['username']) ? $_POST['username'] : '';
         * $password = isset($_POST['password']) ? $_POST['password'] : '';
         * $email = isset($_POST['email']) ? $_POST['email'] : '';
         * $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
         * $retval = $member->registerMember($user_name, $password, $email, $mobile, '', '', '', '');
         * if ($retval > 0) {
         * $this->success("注册成功", "Login/index");
         * } else {
         * $res = AjaxReturn($retval);
         * $this->success($res['message'], "Login/register");
         *
         * }
         * }
         * return view($this->style . 'Login/register');
         */
    }
    // 判断手机号存在不
    public function mobile()
    {
        if (request()->isAjax()) {
            // 获取数据库中的用户列表
            $user_mobile = request()->post('mobile', '');
            $member = new Member();
            $exist = $member->memberIsMobile($user_mobile);
            return $exist;
        }
    }

    /**
     * 判断邮箱是否存在
     */
    public function email()
    {
        if (request()->isAjax()) {
            // 获取数据库中的用户列表
            $user_email = request()->post('email', '');
            $member = new Member();
            $exist = $member->memberIsEmail($user_email);
            return $exist;
        }
    }

    /**
     * 注册手机号验证码验证
     * 任鹏强
     * 2017年6月17日16:26:46
     *
     * @return multitype:number string
     */
    public function register_check_code()
    {
        $send_param = request()->post('send_param', '');
        // $mobile = request()->post('mobile', '');
        $param = session::get('mobileVerificationCode');
        
        if ($send_param == $param && $send_param != '') {
            $retval = [
                'code' => 0,
                'message' => "验证码一致"
            ];
        } else {
            $retval = [
                'code' => 1,
                'message' => "验证码不一致"
            ];
        }
        return $retval;
    }

    /**
     * 注册后登陆
     */
    public function register_login()
    {
        if (request()->isAjax()) {
            $username = request()->post('username', '');
            $mobile = request()->post('mobile', '');
            $password = request()->post('password', '');
            if (! empty($username)) {
                $res = $this->user->login($username, $password);
            } else {
                $res = $this->user->login($mobile, $password);
            }
            /* return AjaxReturn($res); */
            $_SESSION['order_tag'] = ""; // 清空订单
            if ($res['code'] == 1) {
                if (! empty($_SESSION['login_pre_url'])) {
                    $this->redirect($_SESSION['login_pre_url']);
                } else {
                    
                    $redirect = __URL(__URL__ . "/member/index");
                    $this->redirect($redirect);
                }
            }
        }
    }

    /**
     * 制作推广二维码
     */
    function showUserQrcode()
    {
        $uid = request()->get('uid', 0);
        if (! is_numeric($uid)) {
            $this->error('无法获取到会员信息');
        }
        $instance_id = $this->instance_id;
        // 读取生成图片的位置配置
        $weixin = new Weixin();
        $data = $weixin->getWeixinQrcodeConfig($instance_id, $uid);
        $member_info = $this->user->getUserInfoByUid($uid);
        // 获取所在店铺信息
        $web = new WebSite();
        $shop_info = $web->getWebDetail();
        $shop_logo = $shop_info["logo"];
        
        // 查询并生成二维码
        $path = 'upload/qrcode/' . 'qrcode_' . $uid . '_' . $instance_id . '.png';
        
        if (! file_exists($path)) {
            $weixin = new Weixin();
            $url = $weixin->getUserWchatQrcode($uid, $instance_id);
            if ($url == WEIXIN_AUTH_ERROR) {
                exit();
            } else {
                getQRcode($url, 'upload/qrcode', "qrcode_" . $uid . '_' . $instance_id);
            }
        }
        // 定义中继二维码地址
        $thumb_qrcode = 'upload/qrcode/thumb_' . 'qrcode_' . $uid . '_' . $instance_id . '.png';
        $image = \think\Image::open($path);
        // 生成一个固定大小为360*360的缩略图并保存为thumb_....jpg
        $image->thumb(288, 288, \think\Image::THUMB_CENTER)->save($thumb_qrcode);
        // 背景图片
        $dst = $data["background"];
        if (! file_exists($dst)) {
            $dst = "public/static/images/qrcode_bg/qrcode_user_bg.png";
        }
        // 生成画布
        list ($max_width, $max_height) = getimagesize($dst);
        $dests = imagecreatetruecolor($max_width, $max_height);
        $dst_im = getImgCreateFrom($dst);
        imagecopy($dests, $dst_im, 0, 0, 0, 0, $max_width, $max_height);
        imagedestroy($dst_im);
        // 并入二维码
        // $src_im = imagecreatefrompng($thumb_qrcode);
        $src_im = getImgCreateFrom($thumb_qrcode);
        $src_info = getimagesize($thumb_qrcode);
        imagecopy($dests, $src_im, $data["code_left"] * 2, $data["code_top"] * 2, 0, 0, $src_info[0], $src_info[1]);
        imagedestroy($src_im);
        // 并入用户头像
        $user_headimg = $member_info["user_headimg"];
        // $user_headimg = "upload/user/1493363991571.png";
        if (! file_exists($user_headimg)) {
            $user_headimg = "public/static/images/qrcode_bg/head_img.png";
        }
        $src_im_1 = getImgCreateFrom($user_headimg);
        $src_info_1 = getimagesize($user_headimg);
        // imagecopy($dests, $src_im_1, $data['header_left'] * 2, $data['header_top'] * 2, 0, 0, $src_info_1[0], $src_info_1[1]);
        imagecopyresampled($dests, $src_im_1, $data['header_left'] * 2, $data['header_top'] * 2, 0, 0, 80, 80, $src_info_1[0], $src_info_1[1]);
        // imagecopy($dests, $src_im_1, $data['header_left'] * 2, $data['header_top'] * 2, 0, 0, $src_info_1[0], $src_info_1[1]);
        imagedestroy($src_im_1);
        
        // 并入网站logo
        if ($data['is_logo_show'] == '1') {
            // $shop_logo = $shop_logo;
            if (! file_exists($shop_logo)) {
                $shop_logo = "public/static/images/logo.png";
            }
            $src_im_2 = getImgCreateFrom($shop_logo);
            $src_info_2 = getimagesize($shop_logo);
            imagecopy($dests, $src_im_2, $data['logo_left'] * 2, $data['logo_top'] * 2, 0, 0, $src_info_2[0], $src_info_2[1]);
            imagedestroy($src_im_2);
        }
        // 并入用户姓名
        $rgb = hColor2RGB($data['nick_font_color']);
        $bg = imagecolorallocate($dests, $rgb['r'], $rgb['g'], $rgb['b']);
        $name_top_size = $data['name_top'] * 2 + $data['nick_font_size'];
        @imagefttext($dests, $data['nick_font_size'], 0, $data['name_left'] * 2, $name_top_size, $bg, "public/static/font/Microsoft.ttf", $member_info["nick_name"]);
        header("Content-type: image/jpeg");
        imagejpeg($dests);
    }

    /**
     * 制作店铺二维码
     */
    function showShopQecode()
    {
        $uid = request()->get('uid', 0);
        if (! is_numeric($uid)) {
            $this->error('无法获取到会员信息');
        }
        $instance_id = $this->instance_id;
        if ($instance_id == 0) {
            $url = __URL(__URL__ . '/wap?source_uid=' . $uid);
        } else {
            $url = __URL(__URL__ . '/wap/shop/index?shop_id=' . $instance_id . '&source_uid=' . $uid);
        }
        // 查询并生成二维码
        $path = 'upload/qrcode/' . 'shop_' . $uid . '_' . $instance_id . '.png';
        if (! file_exists($path)) {
            getQRcode($url, 'upload/qrcode', "shop_" . $uid . '_' . $instance_id);
        }
        
        // 定义中继二维码地址
        $thumb_qrcode = 'upload/qrcode/thumb_shop_' . 'qrcode_' . $uid . '_' . $instance_id . '.png';
        $image = \think\Image::open($path);
        // 生成一个固定大小为360*360的缩略图并保存为thumb_....jpg
        $image->thumb(260, 260, \think\Image::THUMB_CENTER)->save($thumb_qrcode);
        // 背景图片
        $dst = "public/static/images/qrcode_bg/shop_qrcode_bg.png";
        
        // $dst = "http://pic107.nipic.com/file/20160819/22733065_150621981000_2.jpg";
        // 生成画布
        list ($max_width, $max_height) = getimagesize($dst);
        $dests = imagecreatetruecolor($max_width, $max_height);
        $dst_im = getImgCreateFrom($dst);
        // if (substr($dst, - 3) == 'png') {
        // $dst_im = imagecreatefrompng($dst);
        // } elseif (substr($dst, - 3) == 'jpg') {
        // $dst_im = imagecreatefromjpeg($dst);
        // }
        imagecopy($dests, $dst_im, 0, 0, 0, 0, $max_width, $max_height);
        imagedestroy($dst_im);
        // 并入二维码
        // $src_im = imagecreatefrompng($thumb_qrcode);
        $src_im = getImgCreateFrom($thumb_qrcode);
        $src_info = getimagesize($thumb_qrcode);
        imagecopy($dests, $src_im, "94px" * 2, "170px" * 2, 0, 0, $src_info[0], $src_info[1]);
        imagedestroy($src_im);
        // 获取所在店铺信息
        
        $web = new WebSite();
        $shop_info = $web->getWebDetail();
        $shop_logo = $shop_info["logo"];
        $shop_name = $shop_info["title"];
        $shop_phone = $shop_info["web_phone"];
        $live_store_address = $shop_info["web_address"];
        
        // logo
        if (! file_exists($shop_logo)) {
            $shop_logo = "public/static/images/logo.png";
        }
        // if (substr($shop_logo, - 3) == 'png') {
        // $src_im_2 = imagecreatefrompng($shop_logo);
        // } elseif (substr($shop_logo, - 3) == 'jpg') {
        // $src_im_2 = imagecreatefromjpeg($shop_logo);
        // }
        $src_im_2 = getImgCreateFrom($shop_logo);
        $src_info_2 = getimagesize($shop_logo);
        imagecopy($dests, $src_im_2, "10px" * 2, "380px" * 2, 0, 0, $src_info_2[0], $src_info_2[1]);
        imagedestroy($src_im_2);
        // 并入用户姓名
        $rgb = hColor2RGB("#333333");
        $bg = imagecolorallocate($dests, $rgb['r'], $rgb['g'], $rgb['b']);
        $name_top_size = "430px" * 2 + "23";
        @imagefttext($dests, 23, 0, "10px" * 2, $name_top_size, $bg, "public/static/font/Microsoft.ttf", "店铺名称：" . $shop_name);
        @imagefttext($dests, 23, 0, "10px" * 2, $name_top_size + 50, $bg, "public/static/font/Microsoft.ttf", "电话号码：" . $shop_phone);
        @imagefttext($dests, 23, 0, "10px" * 2, $name_top_size + 100, $bg, "public/static/font/Microsoft.ttf", "店铺地址：" . $live_store_address);
        header("Content-type: image/jpeg");
        imagejpeg($dests);
    }

    /**
     * 获取微信推广二维码
     */
    public function getWchatQrcode()
    {
        $this->determineWapWhetherToOpen();
        $uid = request()->get('source_uid', 0);
        $this->assign('source_uid', $uid);
        if (! is_numeric($uid)) {
            $this->error('无法获取到会员信息');
        }
        $instance_id = 0;
        $this->assign("shop_id", $instance_id);
        $share_contents = $this->getShareContents($uid, 0, 'qrcode_my', '');
        $this->assign("share_contents", $share_contents);
        // 分享
        $ticket = $this->getShareTicket();
        $this->assign("signPackage", $ticket);
        return view($this->style . "Login/myqrcode");
    }

    /**
     * 生成个人店铺二维码
     */
    public function getShopQrcode()
    {
        $this->determineWapWhetherToOpen();
        $uid = request()->get('source_uid', 0);
        $this->assign('source_uid', $uid);
        if (! is_numeric($uid)) {
            $this->error('无法获取到会员信息');
        }
        $share_contents = $this->getShareContents($uid, 0, 'qrcode_shop', '');
        $weisite = new WebSite();
        $weisite_info = $weisite->getWebSiteInfo();
        $info["logo"] = $weisite_info["logo"];
        $info["shop_name"] = $weisite_info["title"];
        $info["phone"] = $weisite_info["web_phone"];
        $info["address"] = $weisite_info["web_address"];
        $this->assign("info", $info);
        // 分享
        $ticket = $this->getShareTicket();
        $this->assign("share_contents", $share_contents);
        $this->assign("signPackage", $ticket);
        return view($this->style . "Login/shopqrcode");
    }

    /**
     * 获取分享相关信息
     * 首页、商品详情、推广二维码、店铺二维码
     *
     * @return multitype:string unknown
     */
    public function getShareContents($uid, $shop_id, $flag, $goods_id)
    {
        $this->uid = $uid;
        // 标识当前分享的类型[shop、goods、qrcode_shop、qrcode_my]
        $flag = isset($flag) ? $flag : "shop";
        $goods_id = isset($goods_id) ? $goods_id : "";
        
        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $this->logo; // 分享时，用到的logo，默认是平台logo
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
                        $share_url = __URL__ . '/wap/index';
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
                $share_url = __URL__ . '/wap/Goods/goodsDetail?id=' . $goods_id;
            }
        }
        
        // 店铺分享
        if ($shop_id != 0) {
            $shop_info = $shop->getShopInfo($shop_id);
            $shop_name = $shop_info['shop_name'];
        } else {
            $weisite = new WebSite();
            $weisite_info = $weisite->getWebSiteInfo();
            $shop_name = $weisite_info["title"];
        }
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
                    $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $goods_detail["img_list"][0]["pic_cover_mid"]; // 用商品的第一个图片
                }
                break;
            case "qrcode_shop":
                // 二维码分享
                if (! empty($user_info)) {
                    $share_content["share_title"] = $shop_name . "二维码分享";
                    $share_content["share_contents"] = $config["qrcode_param_1"] . ";" . $config["qrcode_param_2"];
                    $share_content['share_nick_name'] = '分享人：' . $user_info["nick_name"];
                    if (! empty($user_info['user_headimg'])) {
                        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $user_info['user_headimg'];
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
                        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $user_info['user_headimg'];
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
        // 获取票据
        if (isWeixin()) {
            // 针对单店版获取微信票据
            $config = new WebConfig();
            $auth_info = $config->getInstanceWchatConfig(0);
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
     * 用户锁定界面
     *
     * @return \think\response\View
     */
    public function userLock()
    {
        return view($this->style . "Login/userLock");
    }

    /**
     * 检测手机号是否已经注册
     *
     * @return Ambigous <number, \data\model\unknown>
     */
    public function checkMobileIsHas()
    {
        $mobile = request()->post('mobile', '');
        if (! empty($mobile)) {
            $count = $this->user->checkMobileIsHas($mobile);
        } else {
            $count = 0;
        }
        return $count;
    }

    /**
     * 发送注册短信验证码
     *
     * @return boolean
     */
    public function sendSmsRegisterCode()
    {
        $params['mobile'] = request()->post('mobile', '');
        $vertification = request()->post('vertification', '');
        
        $web_config = new WebConfig();
        $code_config = $web_config->getLoginVerifyCodeConfig($this->instance_id);
        
        if ($code_config["value"]['pc'] == 1 && ! captcha_check($vertification)) {
            $result = [
                'code' => - 1,
                'message' => "验证码错误"
            ];
        } else {
            $params['shop_id'] = 0;
            $result = runhook('Notify', 'registBefor', $params);
            Session::set('mobileVerificationCode', $result['param']);
            Session::set('sendMobile', $params['mobile']);
        }
        
        if (empty($result)) {
            return $result = [
                'code' => - 1,
                'message' => "发送失败"
            ];
        }else if($result["code"] != 0) {
            return $result = [
                'code' => $result["code"],
                'message' => $result["message"]
            ];
        }else if($result["code"] == 0) {
            return $result = [
                'code' => 0,
                'message' => "发送成功"
            ];
        }
    }

    /**
     * 用户绑定手机号
     *
     * @return Ambigous <string, mixed>
     */
    public function sendSmsBindMobile()
    {
        $params['mobile'] = request()->post('mobile', '');
        $params['user_id'] = request()->post('user_id', '');
        $params['shop_id'] = 0;
        $result = runhook('Notify', 'bindMobile', $params);
        Session::set('mobileVerificationCode', $result['param']);
        Session::set('sendMobile', $params['mobile']);
        
        if (empty($result)) {
            return $result = [
                'code' => - 1,
                'message' => "发送失败"
            ];
        }else if($result["code"] != 0) {
            return $result = [
                'code' => $result["code"],
                'message' => $result["message"]
            ];
        }else if($result["code"] == 0) {
            return $result = [
                'code' => 0,
                'message' => "发送成功"
            ];
        }
    }

    /**
     * http://b2c.niushop.com.cn/wap/login/appLogin?user_name=admin2017&password=123456789
     *
     * app 登陆
     */
    public function appLogin()
    {
        $username = request()->post('user_name', '');
        $password = request()->post('password', '');
        $res = $this->user->login($username, $password);
        if ($res['code'] == 1) {
            if (! empty($_SESSION['login_pre_url'])) {
                $this->redirect($_SESSION['login_pre_url']);
            } else {
                $redirect = __URL(__URL__ . "/member/index");
                $this->redirect($redirect);
            }
        }
    }
}