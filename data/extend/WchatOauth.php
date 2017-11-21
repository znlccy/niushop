<?php
namespace data\extend;

use think\Cache as cache;
use data\service\Config;
use think;
use think\Log;

/**
 * 功能说明：微信基本功能测试编码，通过此页面可以获取通过开放平台得到的公众号会话（token）以及公众号对应的appid
 * 创建人：李广
 * 创建时间：2016-2-26
 */
class WchatOauth
{

    public $author_appid;

    public $token;

    /**
     * 构造函数
     *
     * @param unknown $shop_id            
     */
    public function __construct($appid = '')
    {
        $this->author_appid = 'instanceid_0';
    }

    /**
     * ***********************************************************************基础信息*************************************************
     */
    /**
     * 公众号获取access_token
     *
     * @return unknown
     */
    private function get_access_token()
    {
        // 公众平台模式获取token
        $token = $this->single_get_access_token();
        return $token;
    }

    /**
     * 公众平台账户获取token
     */
    private function single_get_access_token()
    {
        $config = new Config();
        $wchat_config = $config->getInstanceWchatConfig(0);
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $wchat_config['value']['appid'] . '&secret=' . $wchat_config['value']['appsecret'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $a = curl_exec($ch);
        $strjson = json_decode($a);
        if($strjson == false || empty($strjson))
        {
            return '';
        }else{
            $token = $strjson->access_token;
            if (empty($token)) {} else {
                // 注意如果是多用户需要
                cache::set('token-' . $this->author_appid, $token, 3600);
            }
            return $token;
        }
       
    }

    /**
     * 微信数据获取
     *
     * @param unknown $url            
     * @param unknown $data            
     * @param string $needToken            
     * @return string|unknown
     */
    private function get_url_return($url, $data = '', $needToken = false)
    {
        // 第一次为空，则从文件中读取
        if (empty($this->token)) {
            $this->token = cache::get('token-' . $this->author_appid);
        }
        // 为空则重新取值
        if (empty($this->token) or $needToken) {
            
            $this->get_access_token();
            $this->token = cache::get('token-' . $this->author_appid);
        }
        $newurl = sprintf($url, $this->token);
        $curl = curl_init(); // 创建一个新url资源
        curl_setopt($curl, CURLOPT_URL, $newurl);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (! empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $AjaxReturn = curl_exec($curl);
        // curl_close();
        $strjson = json_decode($AjaxReturn);
        if (! empty($strjson->errcode)) {
            switch ($strjson->errcode) {
                case 40001:
                    return $this->get_url_return($url, $data, true); // 获取access_token时AppSecret错误，或者access_token无效
                    break;
                case 40014:
                    return $this->get_url_return($url, $data, true); // 不合法的access_token
                    break;
                case 42001:
                    return $this->get_url_return($url, $data, true); // access_token超时
                    break;
                case 45009:
                    return json_encode(array(
                        "errcode" => - 45009,
                        "errmsg" => "接口调用超过限制：" . $strjson->errmsg
                    ));
                    break;
                case 41001:
                    return json_encode(array(
                        "errcode" => - 41001,
                        "errmsg" => "缺少access_token参数：" . $strjson->errmsg
                    ));
                    break;
                default:
                    return json_encode(array(
                        "errcode" => - 41000,
                        "errmsg" => $strjson->errmsg
                    )); // 其他错误，抛出
                    break;
            }
        } else {
            return $AjaxReturn;
        }
    }

    /**
     * ***********************************************************************基础信息*************************************************
     */
    /**
     * *************************************************微信回复消息部分 开始**************************************
     */
    /**
     * 返回文本消息组装xml
     *
     * @param unknown $postObj            
     * @param unknown $content            
     * @param number $funcFlag            
     * @return string
     */
    public function event_key_text($postObj, $content, $funcFlag = 0)
    {
        if (! empty($content)) {
            $xmlTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>%d</FuncFlag>
                        </xml>";
            $resultStr = sprintf($xmlTpl, $postObj->FromUserName, $postObj->ToUserName, time(), $content, $funcFlag);
            return $resultStr;
        }else{
            return '';
        }
    }

    /**
     * 返回图文消息组装xml
     *
     * @param unknown $postObj            
     * @param unknown $arr_item            
     * @param number $funcFlag            
     * @return void|string
     */
    public function event_key_news($postObj, $arr_item, $funcFlag = 0)
    {
        // 首条标题28字，其他标题39字
        if (! is_array($arr_item)) {
            return;
        }
        $itemTpl = "<item>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                    </item>
                ";
        $item_str = "";
        foreach ($arr_item as $item) {
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $newsTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[news]]></MsgType>
        <Content><![CDATA[]]></Content>
        <ArticleCount>%s</ArticleCount>
        <Articles>
        $item_str</Articles>
        <FuncFlag>%s</FuncFlag>
        </xml>";
        $resultStr = sprintf($newsTpl, $postObj->FromUserName, $postObj->ToUserName, time(), count($arr_item), $funcFlag);
        return $resultStr;
    }

    /**
     * *************************************************微信回复消息部分 结束******************************************************************************
     */
    
    /**
     * **********************************************************************************微信获取粉丝信息 开始*********************************************
     */
    
    /**
     * 微信公众号拉取粉丝信息
     *
     * @param unknown $next_openid            
     * @return mixed
     */
    public function get_fans_list($next_openid)
    {
        $strjson = $this->get_url_return("https://api.weixin.qq.com/cgi-bin/user/get?access_token=%s&next_openid={$next_openid}");
        $strarray = json_decode($strjson, true);
        return $strarray;
    }
    /**
     * 批量获取用户粉丝信息
     * @return mixed
     */
    public function get_fans_info_list($openids)
    {
        $strjson = $this->get_url_return("https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=%s", $openids);
        $strarray = json_decode($strjson, true);
        return $strarray;
    }

    /**
     * 获取粉丝信息（通过openID）
     *
     * @param unknown $openid            
     * @return Ambigous <string, \data\extend\unknown, mixed>
     */
    public function get_fans_info($openid)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid={$openid}";
        return $this->get_url_return($url);
    }

    /**
     * 获取openid(前台会员)
     *
     * @return unknown
     */
    public function get_member_access_token()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            // 通过code获得openid
            if (empty($_GET['code'])) {
                // 触发微信返回code码
                $baseUrl = request()->url(true);
                $url = $this->get_single_authorize_url($baseUrl, "123");
                
                Header("Location: $url");
                exit();
            } else {
                // 获取code码，以获取openid
                $code = $_GET['code'];
                
                $data = $this->get_single_access_token($code);
                return $data;
            }
        }
    }

    /**
     * 获取OAuth2授权access_token(微信公众平台模式)
     *
     * @param string $code
     *            通过get_authorize_url获取到的code
     */
    public function get_single_access_token($code = '')
    {
        $config = new Config();
        $wchat_config = $config->getInstanceWchatConfig(0);
        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $wchat_config['value']['appid'] . '&secret=' . $wchat_config['value']['appsecret'] . '&code=' . $code . '&grant_type=authorization_code';
        $data = $this->get_url_return($token_url);
        $token_data = json_decode($data, true);
        return $token_data;
    }

    /**
     * 获取微信OAuth2授权链接snsapi_base
     *
     * @param string $redirect_uri
     *            跳转地址
     * @param mixed $state
     *            参数
     *            不弹出授权页面，直接跳转，只能获取用户openid
     */
    public function get_single_authorize_url($redirect_url = '', $state = '')
    {
        $redirect_url = urlencode($redirect_url);
        $config = new Config();
        $wchat_config = $config->getInstanceWchatConfig(0);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $wchat_config['value']['appid'] . "&redirect_uri=" . $redirect_url . "&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect";
    }

    /**
     * 获取会员对于公众号信息
     *
     * @param unknown $appid            
     */
    public function get_oauth_member_info($token)
    {
        $token_url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $token['access_token'] . "&openid=" . $token['openid'] . "&lang=zh_CN";
        $data = $this->get_url_return($token_url);
        return $data;
    }
    // 单发消息
    public function send_message($openid, $msgtype, $content)
    {
        $json = '{"touser":"%s","msgtype":"%s","text":{"content":"%s"}}';
        $jsondata = sprintf($json, $openid, $msgtype, $content);
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s";
        return $this->get_url_return($url, $jsondata);
    }
    // 群发消息
    public function send_group_message($jsondata)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=%s";
        return $this->get_url_return($url, $jsondata);
    }
    
    // 基础支持: 多媒体文件上传接口 /media/upload媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
    // form-data中媒体文件标识，有filename、filelength、content-type等信息
    public function upload_media($type)
    {
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=%s&type=" . $type;
        return $this->get_url_return($url);
    }
    // 基础支持: 下载多媒体文件接口 /media/get
    public function get_modia($media_id)
    {
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=%s&media_id=" . $media_id;
        return $this->get_url_return($url);
    }

    /**
     * **********************************************************************************微信获取粉丝信息 结束*********************************************
     */
    /**
     * 微信公众号自定义菜单
     *
     * @param unknown $appid            
     * @param unknown $jsonmenu            
     * @return Ambigous <string, \data\extend\unknown, mixed>
     */
    public function menu_create($jsonmenu)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s";
        $result = $this->get_url_return($url, $jsonmenu);
        return $result;
    }

    /**
     * **********************************************************************************微信推广二维码 开始*********************************************
     */
    // 生成永久二维码图片地址
    public function ever_qrcode($data_id)
    {
        if (! empty($data_id)) {
            $data_array = array(
                'action_name' => 'QR_LIMIT_STR_SCENE',
                'action_info' => array(
                    'scene' => array(
                        'scene_str' => $data_id
                    )
                )
            );
            
            $json = json_encode($data_array);
            return $this->qrcode_create($json);
        }
    }

    /**
     * 推广支持: 创建二维码ticket接口 /qrcode/create && 换取二维码 /showqrcode
     *
     * @return src [二维码图片地址]
     */
    // 生成二维码基类函数
    public function qrcode_create($json)
    {
        // 临时二维码请求说明POST-json：{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "123"}}}
        // 永久二维码请求说明POST-json：POST数据例子：{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}
        // action_name 二维码类型，QR_SCENE为临时,QR_LIMIT_SCENE为永久,QR_LIMIT_STR_SCENE为永久的字符串参数值
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s";
        
        $jsonReturn = $this->get_url_return($url, $json);
        $jsonReturn = json_decode($jsonReturn);
        if (! empty($jsonReturn->ticket)) {
            $ticket = $jsonReturn->ticket;
            // $QrCode = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
            $QrCode = $jsonReturn->url;
        } else {
            $QrCode = '';
        }
        
        return $QrCode;
    }

    /**
     * 把微信生成的图片存入本地
     *
     * @param [type] $username
     *            [用户名]
     * @param [string] $LocalPath
     *            [要存入的本地图片地址]
     * @param [type] $weixinPath
     *            [微信图片地址]
     *            
     * @return [string] [$LocalPath]失败时返回 FALSE
     */
    public function save_weixin_img($local_path, $weixin_path)
    {
        $weixin_path_a = str_replace("https://", "http://", $weixin_path);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $weixin_path_a);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
        curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $r = curl_exec($ch);
        curl_close($ch);
        if (! empty($local_path) && ! empty($weixin_path_a)) {
            $msg = file_put_contents($local_path, $r);
        }
        // 执行图片压缩
        $image = think\Image::open($local_path);
        $image->thumb(120, 120, \think\Image::THUMB_CENTER)->save($local_path);
        return $local_path;
    }

    /**
     * **********************************************************************************微信推广二维码 结束*********************************************
     */
    /**
     * 功能说明：从微信选择地址 - 创建签名SHA1
     *
     * @param array $Parameters
     *            string1加密
     */
    public function sha1_sign($Parameters)
    {
        $signPars = '';
        ksort($Parameters);
        foreach ($Parameters as $k => $v) {
            if ("" != $v && "sign" != $k) {
                if ($signPars == '')
                    $signPars .= $k . "=" . $v;
                else
                    $signPars .= "&" . $k . "=" . $v;
            }
        }
        $sign = sha1($signPars);
        return $sign;
    }

    /**
     * 产生随机字符串，不长于32位
     *
     * @param int $length            
     * @return 产生的随机字符串
     */
    public function get_nonce_str($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i ++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 格式化参数格式化成url参数
     */
    public function to_url_param()
    {
        $buff = "";
        foreach ($this->values as $k => $v) {
            if ($k != "sign" && $v != "" && ! is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * *************分享接口*********************************************************************************************
     */
    // jsapi_ticket JS接口的临时票据
    public function jsapi_ticket()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi";
        $ticket = json_decode($this->get_url_return($url), true);
        return $ticket['ticket'];
    }

    /**
     * *************模板消息接口*********************************************************************************************
     */
    // 获取模板ID POST请求
    public function templateID($templateno)
    {
        $templateno_array = array(
            "template_id_short" => $templateno
        );
        $json = json_encode($templateno_array);
        $url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=%s";
        return $this->get_url_return($url, $json);
    }
    
    // 模版消息发送
    public function templateMessageSend($openid, $templateId, $url, $first, $keyword1, $keyword2, $keyword3, $keyword4, $remark)
    {
        $array = array(
            'touser' => $openid,
            'template_id' => $templateId,
            'url' => $url,
            'topcolor' => '#FF0000',
            'data' => array(
                'first' => array(
                    'value' => $first,
                    'color' => '#173177'
                ),
                'keyword1' => array(
                    'value' => $keyword1,
                    'color' => '#173177'
                ),
                'keyword2' => array(
                    'value' => $keyword2,
                    'color' => '#173177'
                ),
                'keyword3' => array(
                    'value' => $keyword3,
                    'color' => '#173177'
                ),
                'keyword4' => array(
                    'value' => $keyword4,
                    'color' => '#173177'
                ),
                'remark' => array(
                    'value' => $remark,
                    'color' => '#173177'
                )
            )
        );
        $json = json_encode($array);
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s";
        return $this->get_url_return($url, $json);
    }

    public function MessageSendToUser($openid, $msg_type, $content)
    {
        $array = array(
            'touser' => $openid
        );
        switch ($msg_type) {
            case "text":
                $array['msgtype'] = 'text';
                $array['text'] = array(
                    'content' => $content
                );
                break;
        }
        $json = json_encode($array);
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s";
        return $this->get_url_return($url, $json);
    }
}