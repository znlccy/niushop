<?php
namespace data\extend;
use think\Cache as cache;
use data\extend\wchat\WxBizMsgCrypt as WxBizMsgCrypt;
use data\service\Weixin;
use data\service\Config;
/**
 * 功能说明：微信第三方平台接口（用于提供微信公众号应用）
 * 创建人：李广
 * 创建时间：2016-2-26
 */
class WchatOpen{
    public $ticket;//推送component_verify_ticket，开放平台设置后每隔10分钟发一次，但要解密
    public $appId;//开放平台appid，在开放平台中
    public $appsecret;//开放平台app密码，在开放平台中
    public $tk;//通过开放平台设置的开放平台的token
    public $encodingAesKey;//开放平台的加密解密秘钥,在开放平台中
    public $component_token;//通过ticket等获取到的开放平台的access_token
    public $pre_auth_code;//预授权码
    public $author_appid;//获取的公众平台的appid
    public  $token = '';//获取公众账号的token
    public $weixin_service;
    function __construct(){
        $this->weixin_service = new Weixin();
        $this->init();
    
    
    }
    /**
     * 微信开放平台初始化
     */
    private function init()
    {
        $config = new Config();
        $config_info = $config->getWchatConfig(0);
        $this->appId = 'wx3324d78d0669d241';
        $this->appsecret = 'a0a15e02a15068886a5637e15ddee70c';
        $this->encodingAesKey = 'shinianmoyijian1234shijianmoyijianniukukeji';
        $this->tk = 'niuku123';
        /*  $this->appId = $config_info['value']['appId'];
         $this->appsecret = $config_info['value']['appsecret'];
         $this->encodingAesKey = $config_info['value']['encodingAesKey'];
         $this->tk = $config_info['value']['tk'];  */
        //$this->ticket =cache::get('ComponentVerifyTicket');
        $this->ticket = \file_get_contents('data/extend/ticket.txt');
        //获取第三方token
        if(cache::get('component_access_token') == false)
        {
            $this->getCommonAccessToken();
        }
        $this->component_token = cache::get('component_access_token');
        //获取预授权码
        if(cache::get('pre_auth_code') == false)
        {
            $this->getPreAuthCode();
             
        }
        // \file_put_contents("ss.txt", $code_arr);
        $this->pre_auth_code = cache::get('pre_auth_code');
        //获取微信支付接口
    
        
    }
    /**
     * 获取第三方token,需要第三方的appid，密码，ticket     $component_token
     */
    private function getCommonAccessToken()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";
        $data = array('component_appid'=>$this->appId,'component_appsecret'=>$this->appsecret,'component_verify_ticket'=>$this->ticket);
    
        $data = json_encode($data);
    
        $curl = curl_init();  //创建一个新url资源
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $AjaxReturn = curl_exec($curl);
        $token_arr = json_decode($AjaxReturn);
    
        if(!empty($token_arr->component_access_token)){
    
            cache::set('component_access_token',$token_arr->component_access_token,5400);
        }
    }
    /**
     * 获取第三方平台的预授权码    需要第三方token，以及第三方appid
     */
    private function getPreAuthCode()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=".$this->component_token;
        $data = array('component_appid'=>$this->appId);
        $data = json_encode($data);
        $curl = curl_init();  //创建一个新url资源
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $AjaxReturn = curl_exec($curl);
        $code_arr = json_decode($AjaxReturn);
        if(!empty($code_arr->pre_auth_code)){
            cache::set('pre_auth_code',$code_arr->pre_auth_code,500);
        }
    }
    /**
     * 获取ticket
     * @param unknown $msg_sign
     * @param unknown $timeStamp
     * @param unknown $nonce
     * @param unknown $from_xml
     * @return string
     */
    public function getTicket($msg_sign, $timeStamp, $nonce, $from_xml)
    {
        // 第三方发送消息给公众平台
        $encodingAesKey = $this->encodingAesKey;
        $token = $this->tk;
        $appId = $this->appId;
        $pc = new WxBizMsgCrypt($token, $encodingAesKey, $appId);
        // 第三方收到公众号平台发送的消息
        $msg = '';
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
        return $msg;
    }
    /**
     * 通过此方法入口获取ComponentVerifyTicket
     * 微信授权使用
     */
    public function getComponentVerifyTicket($signature, $timestamp, $nonce, $from_xml){
        $ticket_xml = $this->getTicket($signature, $timestamp, $nonce, $from_xml);
        $postObj = \simplexml_load_string($ticket_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    
        switch ($postObj->InfoType)
        {
            case "component_verify_ticket":
                //cache::set('ComponentVerifyTicket',$postObj->ComponentVerifyTicket,5400);
                \file_put_contents('data/extend/ticket.txt',$postObj->ComponentVerifyTicket);
                echo "success";
                break;
            case "unauthorized":
                //当用户取消授权的时候，微信服务器也会向这个页面发送信息
                break;
            default:
                break;
        }
    }
    /**
     * 微信开放平台数据加密
     * @param unknown $msg_sign
     * @param unknown $timeStamp
     * @param unknown $nonce
     * @param unknown $from_xml
     * @return string
     */
    public function enc_msg($timeStamp, $nonce, $from_xml)
    {
        // 第三方发送消息给公众平台
        $encodingAesKey = $this->encodingAesKey;
        $token = $this->tk;
        $appId = $this->appId;
    
        $pc = new WxBizMsgCrypt($token, $encodingAesKey, $appId);
        // 第三方收到公众号平台发送的消息
        $msg = '';
        $errCode = $pc->encryptMsg($from_xml, $timeStamp, $nonce, $msg);
    
        return $msg;
    }

    /**
     * 授权入口，注意授权之后的auth_code要存入库中 （用预授权码以及开放平台的appid，授权成功后会给回调网址发送授权的auth_code，用于获取授权公众号基本信息）
     */
    public function auth($url)
    {
        $redurl = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid='.$this->appId.'&pre_auth_code='.$this->pre_auth_code.'&redirect_uri='.$url;
        return $redurl;
    
    }
    /**
     * 使用授权码换取公众号的接口调用凭据和授权信息,得到之后要存入数据库中，尤其是authorizer_appid,authorizer_refresh_token  只提供一次
     */
    public function get_query_auth($author_code)
    {
        //此页面可以是授权的回调地址通过get方法获取到authorization_code
        $url = "https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=".$this->component_token;
        $data = array('component_appid'=>$this->appId,'authorization_code'=>$author_code);
        $data = json_encode($data);
        $curl = curl_init();  //创建一个新url资源
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $AjaxReturn = curl_exec($curl);
        $code_arr = json_decode($AjaxReturn);
        return $code_arr;
    }
    /**
     * 通过上述方法获取的公众号access_token可能会过期，因此需要定时获取access_token
     */
    public function get_access_token($appid)
    {
        //获取公众号token
        $shopinfo = $this->weixin_service->getWeixinInfoByAppid($appid);
        $url = "https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=".$this->component_token;
        $data = array('component_appid'=>$this->appId,'authorizer_appid'=>$appid,'authorizer_refresh_token'=>$shopinfo['authorizer_refresh_token']);
        $data = json_encode($data);
        $curl = curl_init();  //创建一个新url资源
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $AjaxReturn = curl_exec($curl);
        $token_arr = json_decode($AjaxReturn);
        if(!empty($token_arr->authorizer_access_token))
        {
            return $token_arr->authorizer_access_token;
           
        }else{
            return '';
        }
    }

    /**
     * 该API用于获取授权方的公众号基本信息，包括头像、昵称、帐号类型、认证类型、微信号、原始ID和二维码图片URL
     */
    public function get_authorizer_info($appid)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=".$this->component_token;
        $data = array('component_appid'=>$this->appId,'authorizer_appid'=>$appid);
        $data = json_encode($data);
        $curl = curl_init();  //创建一个新url资源
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $AjaxReturn = curl_exec($curl);
        $code_arr = json_decode($AjaxReturn);
        return $code_arr;
    }

    /**
     * 设置授权方的选项信息应用较少，在公众号设置就可以
     该API用于设置授权方的公众号的选项信息，如：地理位置上报，语音识别开关，多客服开关。注意，设置各项选项设置信息，需要有授权方的授权，详见权限集说明
     */
    public function set_authorizer_option(){
        //调用网址 https://api.weixin.qq.com/cgi-bin/component/ api_set_authorizer_option?component_access_token=xxxx
        //post数据:
        /*
         {
         "component_appid":"appid_value",
         "authorizer_appid": " auth_appid_value ",
         "option_name": "option_name_value",
         "option_value":"option_value_value"
         }*/
    }

    /**
     * 对象转化为数组
     * @param unknown $array
     * @return array
     */
    public function object_array($array) {
        if(is_object($array)) {
            $array = (array)$array;
        } if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }
    /**
     * 开放平台代替公众号实现获取前台会员会话
     * @param unknown $appid
     * @param string $code
     * @return mixed
     */
    public function get_access_token_member($appid, $code = ''){
        $token_url = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid={$appid}&code={$code}&grant_type=authorization_code&component_appid={$this->appId}&component_access_token=".$this->component_token;
        $result = $this->get_url_return($token_url);
        return $result;
    }
    /**
     * 微信数据获取
     * @param unknown $url
     * @param unknown $data
     * @param string $needToken
     * @return string|unknown
     */
    private function get_url_return($url, $data=''){
        $curl = curl_init();  //创建一个新url资源
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $AjaxReturn = curl_exec($curl);
        //curl_close();
        $strjson=json_decode($AjaxReturn);
        if (!empty($strjson-> errcode)){
            switch ($strjson-> errcode){
                case 40001:
                    return $this->get_url_return($url, $data); //获取access_token时AppSecret错误，或者access_token无效
                    break;
                case 40014:
                    return $this->get_url_return($url, $data); //不合法的access_token
                    break;
                case 42001:
                    return $this->get_url_return($url, $data); //access_token超时
                    break;
                case 45009:
                    return "接口调用超过限制：".$strjson->errmsg;
                    break;
                case 41001:
                    return "缺少access_token参数：".$strjson->errmsg;
                    break;
                default:
                    return $strjson->errmsg; //其他错误，抛出
                    break;
            }
        }else{
            return $AjaxReturn;
        }
    }
    /**
     * 获取微信OAuth2授权链接snsapi_base,snsapi_userinfo
     * @param string $redirect_uri 跳转地址
     * @param mixed $state 参数
     * 不弹出授权页面，直接跳转，只能获取用户openid
     */
    public function get_authorize_url_info($appid, $redirect_uri = '', $state = ''){
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base,snsapi_userinfo&state={$state}&component_appid={$this->appId}#wechat_redirect";
    }
        /**
        * 获取微信OAuth2授权链接snsapi_base
         * @param string $redirect_uri 跳转地址
         * @param mixed $state 参数
         * 不弹出授权页面，直接跳转，只能获取用户openid
         */
         public function get_authorize_url_base($appid, $redirect_uri = '', $state = ''){
         return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state={$state}&component_appid={$this->appId}#wechat_redirect";
         }
          
  
    
    
    
    
}