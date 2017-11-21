<?php
/**
 * PayParam.php
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
namespace data\service\Upload;
use data\service\BaseService;
use data\service\Config;
/**
 * 功能说明：第三方支付接口
 */
class UploadParam extends BaseService
{
    //实例ID
    protected $instance;
    /***********************************************七牛云存储参数*******************************************/
    protected $Accesskey;          //用于签名的公钥
    protected $Secretkey;     //用于签名的私钥
    protected $Bucket;          //存储空间
    protected $Url;     //七牛用户自定义访问域名
    
    //构造函数如果是多用户系统需要传入对应的实例ID
    function __construct($instance = 0){
        $this->getParam($instance);
    }
    //获取支付所需参数
    protected function getParam($instance){
        $config = new Config();
        //获取微信支付参数(统一支付到平台)
        $qiniu_config = $config->getQiniuConfig($instance); 
        $this->Accesskey = $qiniu_config["Accesskey"];          //用于签名的公钥
        $this->Secretkey = $qiniu_config["Secretkey"];     //用于签名的私钥
        $this->Bucket = $qiniu_config["Bucket"];          //存储空间
        $this->QiniuUrl = $qiniu_config["QiniuUrl"];     //七牛用户自定义访问域名
      
    }
    
    /***************************************************获取七牛云存储参数*************************************/
    public function getAccesskey(){
        return $this->Accesskey;
    }
    public function getSecretkey(){
        return $this->Secretkey;
    }
    public function getBucket(){
        return $this->Bucket;
    }
    public function getQiniuUrl(){
        return $this->QiniuUrl;
    }
    /*************************************************获取七牛云存储参数************************************/
    
  
}
