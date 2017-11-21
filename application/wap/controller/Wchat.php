<?php
/**
 * Wchat.php
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

use think\Controller;
\think\Loader::addNamespace('data', 'data/');
use data\extend\WchatOauth;
use data\service\Config;
use data\service\WebSite;
use data\service\Weixin;

class Wchat extends Controller
{

    public $wchat;

    public $weixin_service;

    public $author_appid;

    public $instance_id;

    public $style;

    public function __construct()
    {
        parent::__construct();
        $this->wchat = new WchatOauth(); // 微信公众号相关类
        
        $this->weixin_service = new Weixin();
        // 使用那个手机模板
        $config = new Config();
        $use_wap_template = $config->getUseWapTemplate(0);
        
        if (empty($use_wap_template)) {
            $use_wap_template['value'] = 'default';
        }
        $this->style = "wap/" . $use_wap_template['value'] . "/";
        $this->assign("style", "wap/" . $use_wap_template['value']);
        
        $this->getMessage();
    }

    /**
     * ************************************************************************微信公众号消息相关方法 开始******************************************************
     */
    /**
     * 关联公众号微信
     */
    public function relateWeixin()
    {
        $sign = request()->get('signature', '');
        if (isset($sign)) {
            $signature = $sign;
            $timestamp = request()->get('timestamp');
            $nonce = request()->get('nonce');
            
            $token = "TOKEN";
            $config = new Config();
            $this->instance_id = 0;
            $wchat_config = $config->getInstanceWchatConfig($this->instance_id);
            if (! empty($wchat_config["value"]["token"])) {
                $token = $wchat_config["value"]["token"];
            }
            
            $tmpArr = array(
                $token,
                $timestamp,
                $nonce
            );
            
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode($tmpArr);
            $tmpStr = sha1($tmpStr);
            
            if ($tmpStr == $signature) {
                $echostr = request()->get('echostr', '');
                if (! empty($echostr)) {
                    echo $echostr;
                }
                
                return 1;
            } else {
                return 0;
            }
        }
    }

    public function templateMessage()
    {
        $media_id = request()->get('media_id', 0);
        $weixin = new Weixin();
        $info = $weixin->getWeixinMediaDetailByMediaId($media_id);
        if (! empty($info["media_parent"])) {
            $website = new WebSite();
            $website_info = $website->getWebSiteInfo();
            $this->assign("website_info", $website_info);
            $this->assign("info", $info);
            return view($this->style . 'Wchat/templateMessage');
        } else {
            echo "图文消息没有查询到";
        }
    }

    /**
     * 微信开放平台模式(需要对消息进行加密和解密)
     * 微信获取消息以及返回接口
     */
    public function getMessage()
    {
        $from_xml = file_get_contents('php://input');
        if (empty($from_xml)) {
            return;
        }
        $signature = request()->get('msg_signature', '');
        $signature = request()->get('timestamp', '');
        $nonce = request()->get('nonce', '');
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
        $ticket_xml = $from_xml;
        $postObj = simplexml_load_string($ticket_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        $this->instance_id = 0;
        if (! empty($postObj->MsgType)) {
            switch ($postObj->MsgType) {
                case "text":
                    $weixin = new Weixin();
                    // 用户发的消息 存入表中
                    $weixin->addUserMessage((string) $postObj->FromUserName, (string) $postObj->Content, (string) $postObj->MsgType);
                    $resultStr = $this->MsgTypeText($postObj);
                    break;
                case "event":
                    $resultStr = $this->MsgTypeEvent($postObj);
                    break;
                default:
                    $resultStr = "";
                    break;
            }
        }
        if (! empty($resultStr)) {
            echo $resultStr;
        } else {
            echo '';
        }
        exit();
    }

    /**
     * 文本消息回复格式
     *
     * @param unknown $postObj            
     * @return Ambigous <void, string>
     */
    private function MsgTypeText($postObj)
    {
        $funcFlag = 0; // 星标
        $wchat_replay = $this->weixin_service->getWhatReplay($this->instance_id, (string) $postObj->Content);
        // 判断用户输入text
        if (! empty($wchat_replay)) { // 关键词匹配回复
            $contentStr = $wchat_replay; // 构造media数据并返回
        } elseif ($postObj->Content == "uu") {
            $contentStr = "shopId：" . $this->instance_id;
        } elseif ($postObj->Content == "TESTCOMPONENT_MSG_TYPE_TEXT") {
            $contentStr = "TESTCOMPONENT_MSG_TYPE_TEXT_callback"; // 微店插件功能 关键词，预留口
        } elseif (strpos($postObj->Content, "QUERY_AUTH_CODE") !== false) {
            $get_str = str_replace("QUERY_AUTH_CODE:", "", $postObj->Content);
            $contentStr = $get_str . "_from_api"; // 微店插件功能 关键词，预留口
        } else {
            $content = $this->weixin_service->getDefaultReplay($this->instance_id);
            if (! empty($content)) {
                $contentStr = $content;
            } else {
                $contentStr = '';
            }
        }
        if (is_array($contentStr)) {
            $resultStr = $this->wchat->event_key_news($postObj, $contentStr);
        } elseif (! empty($contentStr)) {
            $resultStr = $this->wchat->event_key_text($postObj, $contentStr);
        } else {
            $resultStr = '';
        }
        return $resultStr;
    }

    /**
     * 事件消息回复机制
     */
    // 事件自动回复 MsgType = Event
    private function MsgTypeEvent($postObj)
    {
        $contentStr = "";
        switch ($postObj->Event) {
            case "subscribe": // 关注公众号
                $str = $this->wchat->get_fans_info($postObj->FromUserName);
                if (preg_match("/^qrscene_/", $postObj->EventKey)) {
                    $source_uid = substr($postObj->EventKey, 8);
                    $_SESSION['source_shop_id'] = $this->instance_id;
                    $_SESSION['source_uid'] = $source_uid;
                } elseif (! empty($_SESSION['source_uid'])) {
                    $source_uid = $_SESSION['source_uid'];
                    $_SESSION['source_shop_id'] = $this->instance_id;
                } else {
                    $source_uid = 0;
                }
                $Userstr = json_decode($str);
                $nickname = base64_encode($Userstr->nickname);
                $nickname_decode = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $Userstr->nickname);
                $headimgurl = $Userstr->headimgurl;
                $sex = $Userstr->sex;
                $language = $Userstr->language;
                $country = $Userstr->country;
                $province = $Userstr->province;
                $city = $Userstr->city;
                $district = "无";
                $openid = $Userstr->openid;
                if (! empty($Userstr->unionid)) {
                    $unionid = $Userstr->unionid;
                } else {
                    $unionid = '';
                }
                $subscribe_date = date('Y/n/j G:i:s', (int) $postObj->CreateTime);
                $memo = $Userstr->remark;
                $weichat_subscribe = $this->weixin_service->addWeixinFans((int) $source_uid, $this->instance_id, $nickname, $nickname_decode, $headimgurl, $sex, $language, $country, $province, $city, $district, $openid, '', 1, $memo, $unionid); // 关注
                                                                                                                                                                                                                                                     // 添加关注回复
                $content = $this->weixin_service->getSubscribeReplay($this->instance_id);
                if (! empty($content)) {
                    $contentStr = $content;
                }
                // 构造media数据并返回 */
                break;
            case "unsubscribe": // 取消关注公众号
                $openid = $postObj->FromUserName;
                $weichat_unsubscribe = $this->weixin_service->WeixinUserUnsubscribe($this->instance_id, (string) $openid);
                break;
            case "VIEW": // VIEW事件 - 点击菜单跳转链接时的事件推送
                /* $this->wchat->weichat_menu_hits_view($postObj->EventKey); //菜单计数 */
                $contentStr = "";
                break;
            case "SCAN": // SCAN事件 - 用户已关注时的事件推送 - 扫描带参数二维码事件
                $contentStr = "";
                // $contentStr = "shop_url：".$this->shop_url." uid：".$postObj->EventKey; //二维码推广
                
                break;
            case "CLICK": // CLICK事件 - 自定义菜单事件
                $menu_detail = $this->weixin_service->getWeixinMenuDetail($postObj->EventKey);
                $media_info = $this->weixin_service->getWeixinMediaDetail($menu_detail['media_id']);
                $contentStr = $this->weixin_service->getMediaWchatStruct($media_info); // 构造media数据并返回 */
                break;
            default:
                break;
        }
        // $contentStr = $postObj->Event."from_callback";//测试接口正式部署之后注释不要删除
        if (is_array($contentStr)) {
            $resultStr = $this->wchat->event_key_news($postObj, $contentStr);
        } else {
            $resultStr = $this->wchat->event_key_text($postObj, $contentStr);
        }
        return $resultStr;
    }

/**
 * ************************************************************************微信公众号消息相关方法 结束******************************************************
 */
}