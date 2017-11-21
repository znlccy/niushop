<?php
/**
 * ParamModel.php
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

namespace data\model;
use think\Db;
use data\model\BaseModel as BaseModel;
/**
 * 功能说明：第三方接口类数据获取
 */
class ParamModel extends BaseModel
{
    //实例ID
    protected $instance;
    /***********************************************微信公众平台参数*******************************************/
    protected $appid;          //用于微信公众号appid
    protected $appsecret;     //用于微信支公众号appkey
    /**********************************************微信公众平台结束*******************************************/
    
    /***********************************************微信支付参数*******************************************/
    protected $pay_appid;          //用于微信支付的公众号appid
    protected $pay_appsecret;     //用于微信支付的公众号appkey（在jsapi支付中使用获取openid，扫码支付不使用）
    protected $pay_mchid;          //用于微信支付的商户号
    protected $pay_mchkey;        //用于微信支付的商户秘钥
    /**********************************************微信支付参数结束*****************************************/
    
    /**********************************************支付宝支付参数*******************************************/
    protected $ali_partnerid;  //支付宝商户id  以2088开始的纯数字
    protected $ali_seller;     //支付宝商户账号(邮箱)
    protected $ali_key;        //支付宝商户秘钥
    /**********************************************支付宝支付参数结束*****************************************/
    
    //构造函数如果是多用户系统需要传入对应的实例ID
    function __construct($instance = 1){
        $this->getParam($instance);
    }
    //获取支付所需参数
    protected function getParam($instance){
        $this->instance;
        $this->appid = '';
        $this->appsecret = '';
        $this->pay_appid = '';
        $this->pay_appsecret = '';
        $this->pay_mchid = '';
        $this->pay_mchkey = '';
        $this->ali_partnerid = '';
        $this->ali_seller = '';
        $this->ali_key = '';
    }
    
    /***************************************************获取微信公众号参数*************************************/
    public function getAppid(){
        return $this->appid;
    }
    public function getAppsecret(){
        return $this->appsecret;
    }
    /*************************************************获取微信公众号参数结束*************************************/
    
    
    /***************************************************获取微信支付参数*************************************/
    public function getPayAppid(){
        return $this->pay_appid;
    }
    public function getPayAppSecret(){
        return $this->pay_appsecret;
    }
    public function getPayMchid(){
        return $this->pay_mchid;
    }
    public function getPayMchkey(){
        return $this->pay_mchkey;
    }
    /*************************************************获取微信支付参数结束*************************************/
    
    
    /*************************************************获取支付宝支付参数开始************************************/
    public function getAliPartnerid(){
        return $this->ali_partnerid;
    }
    public function getAliSeller(){
        return $this->ali_seller;
    }
    public function getAliKey(){
        return $this->ali_key;
    }
    /*************************************************获取支付宝支付参数结束************************************/
    
  
}
