<?php
/**
 * Index.php
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
namespace app\api\controller;
use data\service\Pay\WeiXinPay;
use data\service\UnifyPay;
/**
 * 后台主界面
 * 
 * @author Administrator
 *        
 */
class Pay extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        //获取信息
        $out_trade_no = !empty($_POST['out_trade_no'])? $_POST['out_trade_no']:'';
        $weixin_pay = new WeiXinPay();
        //随机字符串
        $res['string'] = $weixin_pay->getNonceStr();
        $res['time'] = time();
        //dump($res);
        //返回信息
        if($res){
            return $this->outMessage('niu_index_response', $res);
        }else{
            return $this->outMessage('niu_index_response', $res, -50, '失败！');
        }
    }   
    
    public function backPrivateKey(){
        $out_trade_no = !empty($_POST['out_trade_no'])? $_POST['out_trade_no']:'';
        $pay = new UnifyPay();
        $pay_info = $pay->getPayInfo($out_trade_no);
        $biz_content = '{"body":"'.$pay_info['pay_body'].'","subject":"'.$pay_info['pay_detail'].'","out_trade_no":"'.$pay_info['out_trade_no'].'","total_amount":"'.$pay_info['pay_money'].'","product_code":"QUICK_MSECURITY_PAY"}';
        $privateKey = "MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQC/IPoU5LGXIyx0Pat9sHEqf+y0Q2hOacqMhJswLGPjikA0HLeZRZ+hCSbqhcI5YMdb+m8HaB1ogIC2bXI0GkXtQhD1ojKXh4xH15p3xfbJE+VJNSQIcG34X+wv+H33hVXCoF2CuzdQfvfo4MmPJ1h67GALemLPhqRNAH/p2PFIIXOvQPTp1zfREaQkCT1CVaAsYqVP/gRoS0Z4PrQzYkE5fosyU24VGnMOfiRMHGeZwXZ6loguyVGM6IsGAS9aPigyT+/AewovHJHmTOjya9LSSRyt5m6idrLyfa7lMwufLh9ty0i2MfH9eFZNTm6LCDVI+BFy+lSn3g1N3Zwix6R7AgMBAAECggEBAKDc6X7iSJIzOXwQV6Du+yBREPFbdpPguGhyR4dRJTViq8zwipqHHeZUpJtovuK6ELDaY85xE6db2HkyPpeZsVcSbuG+hSRCIpBo54p9ZMsyYp2uC0jLD9OnMEvjsmm8oESx4mJgnhqy8obfguVosTCsd93RQQ73fJuOmxo/zDvu66BMOCMi3CO0eV6PIJkxBvffiH8mJWdoejpZxGxRGEyqWTY+mXR3iOSwapok/Tey6wJP2XabSzUjdCylqTk2Xc2Fe4eeTwIj+r16wngVlUbBSnZvrrJqOvMrBPiExwpfiD2YafTZeBX8Vqpn4mZeLEu5rHAgWYvyac2o1jgXv2ECgYEA/yJdV8axcJZbnDPtiSdVHrPIQoyTdRjDS138dDHPe43Pnkj7vX1xl9VGmwh4T9OlxbLWd6chlYKXttu0taBcjXOfe2DsN+0Gm0TMhjHzd6B75lHvQJNtrJAJAoxlB96FwNBvFeu4KEI+5dNVVXJyRjsAjfRrTaMAoBzpOtx8d0sCgYEAv8cCvDZvpSksjRisjDtod14qNT/QZvQXm17tz3VZOCP6dKqYaWgPa0FXlPKu2816RzGLoVErY+mcfKuSz/kHxeYLcsRYa3R/bwBEMfg7qZrWRVQBXdfk89BHZyxK+EUMQMt7C6C7QF2iUQnkUhLxrTKKAWZbMAIRCEryVEQNWZECgYEAlij3ny3GSWl1SZ4Wc96+bm+JsgFi2ExWxQjwCMM4RpPUWmjCIWivbpmMELsPdIwwyQbUy1+GmKRf/rOYzD0xu98NQmRtIw1SEhiP93t2vDWsNoaKtgsPRb0QUHupCtShDDag6tntbWRv+HxFgSD2uPcJAIOWk/8X4ySg8I/MgbkCgYAszEMyThEEZrrWdtYPp+z+PUvkxnRbKFe/Xox0srfVqmneCN+zd7BqYIgh7hK0m6odqVAxrbFSFIcOhFG2LTkInHU+KOlyqHMALfLALXlaZCX7aeUr07vSzGtxiWI13oM/O4kRrUkwfu7kuUfReEVmLPEZ5JwWedUat88lqgPoUQKBgQCZFo+2dcKyAV4EF8ihwQ3HSgcbi9NMTw42toejvSDXXOtexMKCjkANsbIwmazGpcVtfNFpIfxyezmwAus5gDjd+sBaKMYPEZXXze8V/uHj8IcyXnt1E/kP9ogF4J5vt5x56vzWCYV1NtWgXVXS4mVRGtPPN0WoFp7urZJ6Jo8Zuw==";
        $notify_url=str_replace("index.php", '', __URL__);
        $notify_url=str_replace("/index.php", '', $notify_url);
        $notify_url=$notify_url."/alipay.php";
        $alipay=array(
            "app_id" => "2017062007532353",
            "method" => "alipay.trade.app.pay",
            "charset" => "utf-8",
            "sign_type" => "RSA2",
            "sign" => $privateKey,
            "timestamp" => date("Y-m-d H:i:s",time()),
            "version" => "1.0",
            "notify_url" => $notify_url,
            "biz_content" => $biz_content 
        );
        $alipay = json_encode($alipay);
        return $this->outMessage("alipay", $alipay);
    }
}
