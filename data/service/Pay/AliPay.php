<?php
/**
 * AliPay.php
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

use data\service\Pay\PayParam;
use data\extend\alipay\AlipaySubmit as AlipaySubmit;
use data\extend\alipay\AlipayNotify as AlipayNotify;
use think\Cookie;
use think\Request;
use think\Log;

/**
 * 功能说明：自定义支付宝支付接入类(应用于商户立即转账create_direct_pay_by_user)
 */
class AliPay extends PayParam
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // 防止默认目录错误
    }

    /**
     * 支付宝基本设置
     *
     * @return unknown
     */
    public function getAlipayConfig()
    {
        // 合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner'] = $this->ali_partnerid;
        
        // 收款支付宝账号
        $alipay_config['seller_email'] = $this->ali_seller;
        
        // 安全检验码，以数字和字母组成的32位字符
        $alipay_config['key'] = $this->ali_key;
        // 签名方式 不需修改
        $alipay_config['sign_type'] = strtoupper('MD5');
        
        // 字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset'] = strtolower('utf-8');
        
        // ca证书路径地址，用于curl中ssl校验
        // 请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert'] = getcwd() . '\\cacert.pem';
        
        // 访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport'] = 'http';
        return $alipay_config;
    }

    /**
     * 设置支付宝支付传入参数
     *
     * @param unknown $orderNumber            
     * @param unknown $body            
     * @param unknown $detail            
     * @param unknown $total_fee            
     * @param unknown $payment_type            
     * @param unknown $notify_url            
     * @param unknown $return_url            
     * @param unknown $show_ur            
     * @return unknown
     */
    public function setAliPay($orderNumber, $body, $detail, $total_fee, $payment_type, $notify_url, $return_url, $show_url)
    {
        $alipay_config = $this->getAlipayConfig();
        /**
         * ************************请求参数*************************
         */
        
        // 支付类型
        $payment_type = $payment_type;
        // 必填，不能修改
        // 服务器异步通知页面路径
        $notify_url = $notify_url;
        // 需http://格式的完整路径，不能加?id=123这类自定义参数
        
        // 页面跳转同步通知页面路径
        $return_url = $return_url;
        // 需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
        
        // 商户订单号
        $out_trade_no = $orderNumber;
        // 商户网站订单系统中唯一订单号，必填
        
        // 订单名称
        $subject = $body;
        // 必填
        
        // 付款金额
        $total_fee = $total_fee;
        // 必填
        
        // 订单描述
        // $body = $body;
        // 商品展示地址
        $show_url = $show_url;
        // 需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html
        
        // 防钓鱼时间戳-安全
        $anti_phishing_key = "";
        // 若要使用请调用类文件submit中的query_timestamp函数
        
        // 客户端的IP地址-
        $exter_invoke_ip = "";
        // 非局域网的外网IP地址，如：221.0.0.1
        
        /**
         * *********************************************************
         */
        $is_mobile = Request::instance()->isMobile();
        $parArr = [];
        if ($is_mobile == true) {
            $service = 'alipay.wap.create.direct.pay.by.user';
            $payment_type = 1;
            $parArr['seller_id'] = trim($alipay_config['partner']);
        } else {
            $service = 'create_direct_pay_by_user';
        }
        // 构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => $service,
            "partner" => trim($alipay_config['partner']),
            "seller_email" => trim($alipay_config['seller_email']),
            "payment_type" => $payment_type,
            "notify_url" => $notify_url,
            "return_url" => $return_url,
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "total_fee" => $total_fee,
            "body" => $body,
            "show_url" => $show_url,
            "anti_phishing_key" => $anti_phishing_key,
            "exter_invoke_ip" => $exter_invoke_ip,
            "_input_charset" => trim(strtolower($alipay_config['input_charset']))
        );
        $parameter = array_merge($parArr, $parameter);
        // 建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        
        $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
        // echo $html_text;
        return $html_text;
    }

    /**
     * 获取配置参数是否正确
     *
     * @return unknown
     */
    public function getVerifyResult($type)
    {
        $alipay_config = $this->getAlipayConfig();
        $alipayNotify = new AlipayNotify($alipay_config);
        if ($type == 'return') {
            $verify_result = $alipayNotify->verifyReturn();
        } else {
            $verify_result = $alipayNotify->verifyNotify();
        }
        
        return $verify_result;
    }

    /**
     * 支付宝支付原路返回
     *
     * @param unknown $refund_no            
     * @param unknown $out_trade_no商户订单号不是支付流水号            
     * @param unknown $refund_fee            
     */
    public function aliPayRefund($refund_no, $out_trade_no, $refund_fee)
    {
        $alipay_config = $this->getAlipayConfig();
        $service = 'refund_fastpay_by_platform_nopwd';
        // 防钓鱼时间戳-安全
        $anti_phishing_key = "";
        // 若要使用请调用类文件submit中的query_timestamp函数
        
        // 客户端的IP地址-
        $exter_invoke_ip = "";
        // 非局域网的外网IP地址，如：221.0.0.1
        // 构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => $service,
            "partner" => trim($alipay_config['partner']),
            "seller_email" => trim($alipay_config['seller_email']),
            "_input_charset" => trim(strtolower($alipay_config['input_charset'])),
            "batch_no" => $refund_no,
            "batch_num" => 1,
            "refund_date" => date("Y-m-d H:i:s", time()),
            "detail_data" => $out_trade_no . '^' . $refund_fee . '^' . '协商退款'
        );
        // 建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        
        $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
        $test = $this->getHttpResponse($html_text);
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($test, 'SimpleXMLElement', LIBXML_NOCDATA);
        $retval = json_decode(json_encode($xmlstring), true);
//         Log::write("json:" . json_encode($xmlstring));
        if ($retval['is_success'] == "T") {
            return array(
                "is_success" => 1,
                'msg' => "success"
            );
        } else {
            return array(
                "is_success" => 0,
                'msg' => $retval['error']
            );
        }
    }

    /**
     * 远程获取数据
     * $url 指定URL完整路径地址
     *
     * @param $time_out 超时时间。默认值：60
     *            return 远程输出的数据
     */
    private function getHttpResponse($url, $time_out = "60")
    {
        $urlarr = parse_url($url);
        $errno = "";
        $errstr = "";
        $transports = "";
        $responseText = "";
        if ($urlarr["scheme"] == "https") {
            $transports = "ssl://";
            $urlarr["port"] = "443";
        } else {
            $transports = "tcp://";
            $urlarr["port"] = "80";
        }
        $fp = @fsockopen($transports . $urlarr['host'], $urlarr['port'], $errno, $errstr, $time_out);
        if (! $fp) {
            die("ERROR: $errno - $errstr<br />\n");
        } else {
            if (trim('utf-8') == '') {
                fputs($fp, "POST " . $urlarr["path"] . " HTTP/1.1\r\n");
            } else {
                fputs($fp, "POST " . $urlarr["path"] . '?_input_charset=' . 'utf-8' . " HTTP/1.1\r\n");
            }
            fputs($fp, "Host: " . $urlarr["host"] . "\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: " . strlen($urlarr["query"]) . "\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $urlarr["query"] . "\r\n\r\n");
            while (! feof($fp)) {
                $responseText .= @fgets($fp, 1024);
            }
            fclose($fp);
            $responseText = trim(stristr($responseText, "\r\n\r\n"), "\r\n");
            return $responseText;
        }
    }

    /**
     * 支付宝转账
     *
     * @param unknown $out_biz_no订单编号            
     * @param unknown $ali_account转账账户            
     * @param unknown $money转账金额            
     * @return \data\extend\alipay\提交表单HTML文本
     */
    public function aliPayTransfer($out_biz_no, $ali_account, $money)
    {
        $alipay_config = $this->getAlipayConfig();
        $service = 'alipay.fund.trans.toacc';
        // 防钓鱼时间戳-安全
        $anti_phishing_key = "";
        // 若要使用请调用类文件submit中的query_timestamp函数
        
        // 客户端的IP地址-
        $exter_invoke_ip = "";
        // 非局域网的外网IP地址，如：221.0.0.1
        // 构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => $service,
            "partner" => trim($alipay_config['partner']),
            "seller_email" => trim($alipay_config['seller_email']),
            "_input_charset" => trim(strtolower($alipay_config['input_charset'])),
            "payee_type" => 'ALIPAY_LOGONID',
            "payee_account" => $ali_account,
            "payer_account" => trim($alipay_config['seller_email']),
            "payer_type" => 'ALIPAY_LOGONID',
            "amount" => $money,
            "out_biz_no" => $out_biz_no
        );
        // 建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        
        $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
        // echo $html_text;
        return $html_text;
    }
}