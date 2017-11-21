<?php
namespace data\extend;
use data\model\Param as Param;
/**
 * 功能说明：微信公众平台接口类(微信支付不用此接口)
 * 创建人：李广
 * 创建时间：2016-10-24
 *
 */

class WeiXin extends Param {
    
    function __construct(){
        parent::__construct();
    }
    /**
     * 默认测试函数
     */
    public function index(){
        $this->InitToken();
        $str = $this->get_openid();
        var_dump($str);
     
        
    }
    //获取access_token对象并写入到配置文件access_token.txt
    private function InitToken(){
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->appsecret;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $a = curl_exec($ch);
        $strjson=json_decode($a);
        $token = $strjson->access_token;
        if (empty($token)){
            echo "无法取得token，请与管理员联系！";    //$strjson
        }else{
            //注意如果是多用户需要
            S('token-'.$this->instance,$token,7200);
            $this->token = S('token-'.$this->instance);
        }
    }
    
    /**
     * 功能说明：请求与返回万能函数,防token过期,$needToken默认为false,
     * @param string $url 跳转地址
     * @param json[Post] $data
     */
    private function GetUrlReturn($url, $data, $needToken = false){
        //第一次为空，则从文件中读取
        if (empty($this->token)){
            $this->token = S('token-'.$this->instance);
        }
        //为空则重新取值
        if (empty($this->token) or $needToken){
            $this->InitToken();
        }
        $newurl = sprintf($url, $this->token);
        $curl = curl_init();  //创建一个新url资源
        curl_setopt($curl, CURLOPT_URL, $newurl);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $AjaxReturn = curl_exec($curl);
        curl_close($AjaxReturn);
        $strjson=json_decode($AjaxReturn);
        //var_dump($strjson);  //开启可调试
        if (!empty($strjson-> errcode)){
            switch ($strjson-> errcode){
                case 40001:
                    return $this->GetUrlReturn($url, $data, true); //获取access_token时AppSecret错误，或者access_token无效
                    break;
                case 40014:
                    return $this->GetUrlReturn($url, $data, true); //不合法的access_token
                    break;
                case 42001:
                    return $this->GetUrlReturn($url, $data, true); //access_token超时
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
    
    /*************认证接口*******************************************************************************************/
    /**
     * 获取用户的openid
     * @return 用户的openid
     */
    public function get_openid(){
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            //通过code获得openid
            if (empty($_GET['code'])){
                //触发微信返回code码
                if (!empty($_SERVER['QUERY_STRING'])) {
                    $query_string = "?".$_SERVER['QUERY_STRING'];
                }
                $baseUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$query_string;
                $url = $this->get_authorize_url_base($baseUrl, "123");
                Header("Location: $url");
                exit();
            } else {
                //获取code码，以获取openid
                $code = $_GET['code'];
                $data = $this->get_access_token($code);
                $openid = $data['openid'];
                //session('openid', $openid); //写入本地SESSION
            }
        }
        return $openid;
    }
    /**
     * 获取OAuth2授权access_token
     * @param string $code 通过get_authorize_url获取到的code
     */
    public function get_access_token($code = ''){
        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->secret}&code={$code}&grant_type=authorization_code";
        $data = $this->GetUrlReturn($token_url);
        $token_data = json_decode($data, true);
        return $token_data;
    }
    /**
     * 获取微信OAuth2授权链接snsapi_base
     * @param string $redirect_uri 跳转地址
     * @param mixed $state 参数
     * 不弹出授权页面，直接跳转，只能获取用户openid
     */
    public function get_authorize_url_base($redirect_uri = '', $state = ''){
        $redirect_uri = urlencode($redirect_uri);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state={$state}#wechat_redirect";
    }
    /**
    * 获取微信OAuth2授权链接snsapi_userinfo
     * @param string $redirect_uri 跳转地址
     * @param mixed $state 参数
     * 弹出授权页面，可通过openid拿到昵称、性别、所在地。并且，即使在未关注的情况下，只要用户授权，也能获取其信息
     */
     public function get_authorize_url_userinfo($redirect_uri = '', $state = ''){
     $redirect_uri = urlencode($redirect_uri);
         return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect";
     }
    
     /**
     * 拉取OAuth2授权用户信息(需scope为 snsapi_userinfo)
         * @param string $access_token 通过OAuth2授权get_access_token获取到的access_token
          * @param string $openid 用户openid
          */
          public function get_access_token_userinfo($access_token, $openid){
          $token_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
          $userinfo_data = $this->GetUrlReturn($token_url);
          return $userinfo_data;
          }
    
          /**
          * 功能说明：从微信选择地址 - 创建签名SHA1
          * @param array $Parameters string1加密
          */
          public function sha1_sign($Parameters){
          $signPars = '';
          ksort($Parameters);
              foreach($Parameters as $k => $v) {
              if("" != $v && "sign" != $k) {
              if($signPars == '')
                  $signPars .= $k . "=" . $v;
                  else
                      $signPars .=  "&". $k . "=" . $v;
                  }
                  }
                  $sign = sha1($signPars);
                  return $sign;
    }
    /***************支付接口**********************************************************************************************/
                  /**
                  *
                  * 获取jsapi支付的参数
                  * @param array $UnifiedOrderResult 统一支付接口返回的数据
                  * @throws WxPayException
                  * @return json数据，可直接填入js函数作为参数
                  */
                  public function GetJsApiParameters($UnifiedOrderResult)
                  {
                  if(!array_key_exists("appid", $UnifiedOrderResult) || !array_key_exists("prepay_id", $UnifiedOrderResult) || $UnifiedOrderResult['prepay_id'] == ""){
                      return "参数错误";
                  }
                  $jsapi = new \framework\weixin\lib\WxPayJsApiPay();
                  $jsapi->SetAppid($this->appid);
                  $jsapi->SetTimeStamp(date("YmdHis"));
                  $jsapi->SetNonceStr($this->getNonceStr());
                      $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
                      $jsapi->SetSignType("MD5");
                      $jsapi->SetPaySign($jsapi->MakeSign());
                      $parameters = json_encode($jsapi->GetValues());
                      return $parameters;
                      }
                      /**
                      * 产生随机字符串，不长于32位
                      * @param int $length
                      * @return 产生的随机字符串
                      */
                      public static function getNonceStr($length = 32) {
                      $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
                      $str ="";
                      for ( $i = 0; $i < $length; $i++ ) {
                      $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
                      }
                      return $str;
                      }
                      /**
                      * 生成签名
                      * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
                      */
                      public function MakeSign() {
                      //签名步骤一：按字典序排序参数
                      ksort($this->values);
                      $string = $this->ToUrlParams();
                      //签名步骤二：在string后加入KEY
                      $string = $string . "&key=".$this->KEY;
    
                      //签名步骤三：MD5加密
                      $string = md5($string);
                      //签名步骤四：所有字符转为大写
                          $result = strtoupper($string);
                          return $result;
    }
    /**
    * 格式化参数格式化成url参数
        */
            public function ToUrlParams(){
            $buff = "";
            foreach ($this->values as $k => $v)
            {
            if($k != "sign" && $v != "" && !is_array($v)){
            $buff .= $k . "=" . $v . "&";
    }
    }
    $buff = trim($buff, "&");
    return $buff;
    }
    //企业付款API
    public function EnterprisePayment($openid, $msgtype, $content){
    $xml = "<xml>
    <mch_appid>wxe062425f740c30d8</mch_appid>
    <mchid>10000098</mchid>
    <nonce_str>3PG2J4ILTKCH16CQ2502SI8ZNMTM67VS</nonce_str>
    <partner_trade_no>100000982014120919616</partner_trade_no>
    <openid>ohO4Gt7wVPxIT1A9GjFaMYMiZY1s</openid>
    <check_name>OPTION_CHECK</check_name>
    <re_user_name>张三</re_user_name>
    <amount>100</amount>
    <desc>节日快乐!</desc>
            <spbill_create_ip>10.2.3.10</spbill_create_ip>
        <sign>{$this->MakeSign()}</sign>
            </xml>";
            $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
            return $this->GetUrlReturn($url, $xml);
    }
    /***************基础接口**********************************************************************************************/
    //单发消息
    public function SendMessage($openid, $msgtype, $content){
        $json = '{"touser":"%s","msgtype":"%s","text":{"content":"%s"}}';
        $jsondata = sprintf($json, $openid, $msgtype, $content);
            $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s";
            return $this->GetUrlReturn($url, $jsondata);
    }
    //群发消息
    public function GroupSendMessage($jsondata){
        $url = "https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=%s";
            return $this->GetUrlReturn($url, $jsondata);
    }
    
    //基础支持: 多媒体文件上传接口 /media/upload媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
    //form-data中媒体文件标识，有filename、filelength、content-type等信息
    public function MediaUpload($type){
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=%s&type=".$type;
        return $this->GetUrlReturn($url);
    }
    //基础支持: 下载多媒体文件接口 /media/get
    public function MediaGet($media_id){
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=%s&media_id=".$media_id;
        return $this->GetUrlReturn($url);
        }
    
    
        //用户管理: 获取关注者列表接口 /user/get
        public function GetUserList($next_openid){
        $strjson = $this -> GetUrlReturn("https://api.weixin.qq.com/cgi-bin/user/get?access_token=%s&next_openid={$next_openid}");
            $strarray = json_decode($strjson,true);
            //return $strarray['data']['openid'];
            return $strarray;
            }
    
            //用户管理: 获取用户基本信息接口 /user/info
            public function UserInfo ($openid){
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid={$openid}";
            return $this->GetUrlReturn($url);
            }
    
    //用户管理: 查询分组接口 /groups/get
    public function GroupsGet (){
            return $this->GetUrlReturn("https://api.weixin.qq.com/cgi-bin/groups/get?access_token=%s");
    }
    //用户管理: 创建分组接口 /groups/create
                public function GroupsCreate (){
                $url = "https://api.weixin.qq.com/cgi-bin/groups/create?access_token=%s";
                    return $this->GetUrlReturn($url);
    }
    //用户管理: 修改分组名接口 /groups/update
                    public function GroupsUpdate (){
                    $url = "https://api.weixin.qq.com/cgi-bin/groups/update?access_token=%s";
        return $this->GetUrlReturn($url);
    }
    //用户管理: 移动用户分组接口 /groups/members/update
        public function GroupsMove (){
                    $url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=%s";
        return $this->GetUrlReturn($url);
                }
                //用户管理: 查询用户分组id接口 /groups/getid
                public function GroupsGetId (){
                $url = "https://api.weixin.qq.com/cgi-bin/groups/getid?access_token=%s";
        return $this->GetUrlReturn($url);
                }
    
                //自定义菜单: 自定义菜单创建接口 /menu/create
                public function MenuCreate ($jsonmenu){
                $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s";
        $result = $this->GetUrlReturn($url, $jsonmenu);
            return $result;
    }
    //自定义菜单: 自定义菜单查询接口 /menu/get
    public function MenuGet (){
        $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=%s";
        return $this->GetUrlReturn($url);
    }
        //自定义菜单: 自定义菜单删除接口 /menu/delete
        public function MenuDelete (){
        $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=%s";
        return $this->GetUrlReturn($url);
        }
    
        /**
        * 推广支持: 创建二维码ticket接口 /qrcode/create && 换取二维码 /showqrcode
            * @return src [二维码图片地址]
            */
            //生成二维码基类函数
            public function QrcodeCreate ($json){
            //临时二维码请求说明POST-json：{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "123"}}}
            //永久二维码请求说明POST-json：POST数据例子：{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}
            //action_name  二维码类型，QR_SCENE为临时,QR_LIMIT_SCENE为永久,QR_LIMIT_STR_SCENE为永久的字符串参数值
    
            $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s";
        $jsonReturn = $this->GetUrlReturn($url, $json);
            $jsonReturn = json_decode($jsonReturn);
            $ticket = $jsonReturn->ticket;
            $QrCode = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
                return $QrCode;
            }
    
            //生成永久二维码图片地址
                public function EverQrcode ($dataID){
                if (!empty($dataID)) {
                $data_array = array (
                    'action_name' => 'QR_LIMIT_STR_SCENE',
                    'action_info' =>
                    array (
                    'scene' =>
                array (
                'scene_str' => $dataID,
                ),
                ),
                );
    
                $json = json_encode($data_array);
                var_dump($json);
                return $this->QrcodeCreate($json);
                }
                }
                /**
                * 把微信生成的图片存入本地
                * @param [type] $username   [用户名]
                * @param [string] $LocalPath  [要存入的本地图片地址]
                * @param [type] $weixinPath [微信图片地址]
                    *
                    * @return [string] [$LocalPath]失败时返回 FALSE
                    */
                    public function SaveWeixinImg ($LocalPath, $weixinPath){
                    $weixinPath_a = str_replace("https://", "http://", $weixinPath);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $weixinPath_a);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                    curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
                    curl_setopt($ch, CURLOPT_REFERER,_REFERER_);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $r = curl_exec($ch);
                    curl_close($ch);
                    if (!empty($LocalPath) && !empty($weixinPath_a)) {
                    $msg = file_put_contents($LocalPath, $r);
                    }
                    return $LocalPath;
                    }
    
                    /***************订单接口**********************************************************************************************/
                    //获取模板ID POST请求
                    public function TemplateID ($templateno){
                    $templateno_array =  array("template_id_short" => $templateno);
                    $json = json_encode($templateno_array);
                        $url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=%s";
                            return $this->GetUrlReturn($url, $json);
                    }
    
        //模版消息发送
        public function TemplateMessageSend ($openid, $templateId, $url, $first, $keyword1, $keyword2, $keyword3, $keyword4, $remake){
                    $array = array('touser'=>$openid,
                        'template_id'=>$templateId,
                        'url'=>$url,
                        'topcolor'=>'#FF0000',
                        'data'=>array('first'=>array('value'=>$first,'color'=>'#ccc'),
                        'keyword1'=>array('value'=>$keyword1,'color'=>'#ccc'),
                          'keyword2'=>array('value'=>$keyword2,'color'=>'#ccc'),
                              'keyword3'=>array('value'=>$keyword3,'color'=>'#ccc'),
                              'keyword4'=>array('value'=>$keyword4,'color'=>'#ccc'),
                              'remark'  =>array('value'=>$remake,  'color'=>'#ccc')
                )
            );
        $json = json_encode($array);
                              $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s";
            return $this->GetUrlReturn($url, $json);
        }
    
        /***************分享接口**********************************************************************************************/
        //jsapi_ticket   JS接口的临时票据
        public function jsapi_ticket (){
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi";
            $ticket = json_decode($this->GetUrlReturn($url),true);
            return $ticket['ticket'];
        }
    


}