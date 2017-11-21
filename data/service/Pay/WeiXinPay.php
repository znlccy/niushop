<?php
/**
 * WeiXinPay.php
 *
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
namespace data\service\Pay;

use data\extend\weixin\WxPayApi as WxPayApi;
use data\extend\weixin\WxPayData\WxPayUnifiedOrder;
use data\service\Pay\PayParam;
use data\extend\weixin\WxPayData\WxPayJsApiPay;
use think\Log;
use data\extend\weixin\WxPayData\WxPayRefund;

/**
 * 功能说明：微信支付接口(应用于微信公众平台)
 */
class WeiXinPay extends PayParam
{

    private $token;
    // access_token
    private $values;

    function __construct($instance = 0)
    {
        parent::__construct($instance);
    }

    public function index()
    {
        // 防止默认目录错误
    }

    /**
     * 功能说明：请求与返回万能函数,防token过期,$needToken默认为false,
     *
     * @param string $url
     *            跳转地址
     * @param json[Post] $data            
     */
    private function GetUrlReturn($url, $data = '', $needToken = false)
    {
        $newurl = sprintf($url);
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
        // curl_close($AjaxReturn);
        $strjson = json_decode($AjaxReturn);
        // var_dump($strjson); //开启可调试
        if (! empty($strjson->errcode)) {
            switch ($strjson->errcode) {
                case 40001:
                    return $this->GetUrlReturn($url, $data, true); // 获取access_token时AppSecret错误，或者access_token无效
                    break;
                case 40014:
                    return $this->GetUrlReturn($url, $data, true); // 不合法的access_token
                    break;
                case 42001:
                    return $this->GetUrlReturn($url, $data, true); // access_token超时
                    break;
                case 45009:
                    return "接口调用超过限制：" . $strjson->errmsg;
                    break;
                case 41001:
                    return "缺少access_token参数：" . $strjson->errmsg;
                    break;
                default:
                    return $strjson->errmsg; // 其他错误，抛出
                    break;
            }
        } else {
            return $AjaxReturn;
        }
    }

    /**
     * ***********认证接口******************************************************************************************
     */
    /**
     * 获取用户的openid
     *
     * @return 用户的openid
     */
    public function get_openid()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            // 通过code获得openid
            if (empty($_GET['code'])) {
                // 触发微信返回code码
                $baseUrl = request()->url(true);
                $url = $this->get_authorize_url_base($baseUrl, "123");
                Header("Location: $url");
                exit();
            } else {
                $baseUrl = request()->url(true);
                // 获取code码，以获取openid
                $code = $_GET['code'];
                $data = $this->get_access_token($code);
                
                $openid = $data['openid'];
                // session('openid', $openid); //写入本地SESSION
            }
        }
        return $openid;
    }

    /**
     * 获取OAuth2授权access_token
     *
     * @param string $code
     *            通过get_authorize_url获取到的code
     */
    public function get_access_token($code = '')
    {
        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->pay_appid}&secret={$this->pay_appsecret}&code={$code}&grant_type=authorization_code";
        $data = $this->GetUrlReturn($token_url);
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
    public function get_authorize_url_base($redirect_uri = '', $state = '')
    {
        $redirect_uri = urlencode($redirect_uri);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->pay_appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state={$state}#wechat_redirect";
    }

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
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i ++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 检测签名串
     *
     * @param unknown $postObj            
     */
    public function checkSign($postObj, $sign)
    {
        $this->values = json_decode(json_encode($postObj), true);
        $make_sign = $this->MakeSign();
        if ($make_sign == $sign) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 生成签名
     *
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function MakeSign()
    {
        // 签名步骤一：按字典序排序参数
        ksort($this->values);
        $string = $this->ToUrlParams();
        // 签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $this->pay_mchkey;
        
        // 签名步骤三：MD5加密
        $string = md5($string);
        // 签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     */
    public function ToUrlParams()
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
    // 企业付款API
    public function EnterprisePayment($openid, $msgtype, $content)
    {
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

    /**
     * 设置微信支付参数
     *
     * @param unknown $body
     *            订单描述
     * @param unknown $detail
     *            订单详情
     * @param unknown $total_fee
     *            订单金额
     * @param unknown $orderNumber
     *            订单编号
     * @param unknown $red_url
     *            异步回调域名
     * @param unknown $trade_type
     *            交易类型JSAPI、NATIVE、APP
     * @param unknown $openid
     *            支付人openid（jsapi支付必填）
     * @param unknown $product_id
     *            商品id(扫码支付必填)
     * @return unknown
     */
    public function setWeiXinPay($body, $detail, $total_fee, $orderNumber, $red_url, $trade_type, $openid, $product_id)
    {
        $WxPayApi = new WxPayApi();
        // ②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody($body); // 订单项描述
        $input->SetDetail($detail);
        $input->SetTotal_fee($total_fee); // 总金额
        $input->SetAttach(1); // 附加数据orderId
        $input->SetOut_trade_no($orderNumber); // 商户订单流水号
        $input->SetTime_start(date("YmdHis")); // 交易起始时间
        $input->SetTime_expire(date("YmdHis", time() + 6000)); // 交易结束时间
        $input->SetGoods_tag("商品标记"); // 商品标记
        $input->SetNotify_url($red_url); // 接收微信支付成功通知地址
        $input->SetTrade_type($trade_type); // 交易类型JSAPI、NATIVE、APP
        $input->SetOpenid($openid); // 用户标识
        $input->SetProduct_id($product_id); // 用户标识
        $input->SetSpbill_create_ip($this->getIp());
        $order = $WxPayApi->unifiedOrder($input, 30);
        return $order;
    }

    public function get_client_ip()
    {
        $cip = "unknown";
        if ($_SERVER['REMOTE_ADDR']) {
            $cip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv('REMOTE_ADDR')) {
            $cip = getenv('REMOTE_ADDR');
        }
        return $cip;
    }

    public function getIp()
    {
        $ip = '';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $ip_arr = explode(',', $ip);
        return $ip_arr[0];
    }

    /**
     *
     * 获取jsapi支付的参数
     *
     * @param array $UnifiedOrderResult
     *            统一支付接口返回的数据
     * @throws WxPayException
     * @return json数据，可直接填入js函数作为参数
     */
    public function GetJsApiParameters($UnifiedOrderResult)
    {
        if (! array_key_exists("appid", $UnifiedOrderResult) || ! array_key_exists("prepay_id", $UnifiedOrderResult) || $UnifiedOrderResult['prepay_id'] == "") {
            return json_encode($UnifiedOrderResult);
        }
        $jsapi = new WxPayJsApiPay();
        $jsapi->SetAppid($this->pay_appid);
        $jsapi->SetTimeStamp(date("YmdHis"));
        $jsapi->SetNonceStr($this->getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign());
        $parameters = json_encode($jsapi->GetValues());
        return $parameters;
    }

    /**
     * 订单项目退款
     *
     * @param unknown $refund_no            
     * @param unknown $out_trade_no            
     * @param unknown $refund_fee            
     * @param unknown $total_fee            
     * @param unknown $transaction_id            
     * @return \data\extend\weixin\成功时返回，其他抛异常
     */
    public function setWeiXinRefund($refund_no, $out_trade_no, $refund_fee, $total_fee)
    {
        $WxPayApi = new WxPayApi();
        $input = new WxPayRefund();
        $input->SetOut_refund_no($refund_no);
        $input->SetOut_trade_no($out_trade_no);
        $input->SetRefund_fee($refund_fee);
        $input->SetTotal_fee($total_fee);
        // $input->SetTransaction_id($transaction_id);
        Log::write("refund_fee:" . $refund_fee);
        Log::write("total_fee:" . $total_fee);
        try {
            $order = $WxPayApi->refund($input, 30);
            $msg = '操作成功';
            // 检测签名配置是否正确
            if ($order['return_code'] == "FAIL") {
                
                $is_success = 0;
                $msg = $order['return_msg'];
            } else {
                // 检查退款业务是否正确
                if ($order['result_code'] == "FAIL") {
                    
                    $is_success = 0;
                    $msg = $order['err_code_des'];
                } else {
                    $is_success = 1;
                }
            }
            
            return array(
                'is_success' => $is_success,
                'msg' => $msg
            );
        } catch (\Exception $e) {
            return array(
                'is_success' => 0,
                'msg' => $e->getMessage()
            );
        }
        // {"appid":"wx4c2b4d7f4d3963d7","cash_fee":"1","cash_refund_fee":"1","coupon_refund_count":"0","coupon_refund_fee":"0","mch_id":"1455459702","nonce_str":"6mdlt8uvgl34XD7f","out_refund_no":"20171018110321829363","out_trade_no":"150829145755801000","refund_channel":[],"refund_fee":"1","refund_id":"50000604632017101802054708425","result_code":"SUCCESS","return_code":"SUCCESS","return_msg":"OK","sign":"18958094225D196E1547A179D579F68C","total_fee":"1","transaction_id":"4200000006201710188754922710"}
        // {"return_code":"FAIL","return_msg":"invalid total_fee"}
        // {"return_code":"FAIL","return_msg":"\u5546\u6237\u53f7mch_id\u6216sub_mch_id\u4e0d\u5b58\u5728"}
    }
}