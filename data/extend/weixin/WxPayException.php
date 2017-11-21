<?php
namespace data\extend\weixin;
/**
 * 
 * 微信支付API异常类
 * @author widyhu
 *
 */
class WxPayException extends \Exception {
	public function __construct($message)
	{
	    parent::__construct();
		\Think\Log::record("微信支付报错".$message,'WARN');;
	}
}
