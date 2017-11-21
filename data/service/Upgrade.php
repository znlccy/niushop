<?php
/**
 * User.php
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
 * @date : 2015.4.24
 * @version : v1.0.0.0
 */
namespace data\service;

use data\service\BaseService as BaseService;
use data\api\IUpgrade;
use data\model\VersionPatchModel;
use data\model\VersionDevolutionModel;
class Upgrade extends BaseService implements IUpgrade
{

    /**
     * 接口域名
     * @var unknown
     */
    private $service_Url="http://www.niushop.com.cn";
    private $host_url="";
    function __construct()
    {
        parent::__construct();
        $this->host_url=$_SERVER['HTTP_HOST'];        
    }
    
    /**
     * 得到当前域名的授权信息
     */
    public function getUserDevolution($user_name, $password){
        $post_url=$this->service_Url."/api/Version/getUserDevolution";
        $post_data=array(
            "user_name"=>$user_name,
            "password"=>$password
        );
        $result=$this->doPost($post_url, $post_data);
        return $result;
    }
    /**
     * 得到服务器的最新版本
     * @return mixed
     */
    public function getLatestVersion(){
        $post_url=$this->service_Url."/api/Version/getLatestVersion";
        $post_data=array();
        $result=$this->doPost($post_url, $post_data);
        return $result;
    }


    /**
     *  判断当前用户是否需要升级
     * {@inheritDoc}
     * @see \data\api\IUpgrade::devolutionUpdate()
     */
    public function devolutionUpdate(){
        $post_url=$this->service_Url."/api/Version/DevolutionUpdate";
        @include ROOT_PATH . 'version.php';
        $post_data=array(
            "patch_release"=>NIU_RELEASE
        );
        $result=$this->doPost($post_url, $post_data);
        return $result;
    }
    /**
     * 
     * 是否加载版权
     * (non-PHPdoc)
     * @see \data\api\IUpgrade::isLoadCopyRight()
     */
    public function isLoadCopyRight(){
        $is_load=1;
        $product_devolution = new VersionDevolutionModel();
        $product_info=$product_devolution->getInfo(" 1=1 ", "*");
        if(!empty($product_info)){
            $username=$product_info["devolution_username"];
            $password=$product_info["devolution_password"];
            /**
             * 授权信息
             * @var unknown
             */
            $devolution_info=$this->getUserDevolution($username, $password);
            $devolution_info=json_decode($devolution_info, true);
            if(isset($devolution_info) && !empty($devolution_info)){
                $code=$devolution_info["code"];
                if($code==0){
                    $is_load=0;
                }
            }else{
                $is_load=0;
            }
        }
        return $is_load;
    }
    /**
     * 获取官网的更新数据
     * @param unknown $patch_release
     * @param unknown $devolution_version
     * @param unknown $devolution_code
     */
    public function getVersionPatchList($user_name, $password){
        $post_url=$this->service_Url."/api/Version/getPatchPacket";
        @include ROOT_PATH . 'version.php';
        $post_data=array(
            "patch_release"=>NIU_RELEASE,
            "user_name"=>$user_name,
            "password"=>$password,
            "ns_version"=>NS_VERSION
        );
        $result=$this->doPost($post_url, $post_data);
        return $result;
//         $this->updateVersionPatchList($result);
    }
    /**
     * 将官网数据 拉取到本地
     * @param unknown $patch_list
     */
    private function updateVersionPatchList($patch_result){
        $patch_result=json_decode($patch_result, true);
        if(!empty($patch_result) && $patch_result["code"]==0){
            $patch_list=$patch_result["data"];
            if(!empty($patch_list) && count($patch_list)>0){
                foreach ($patch_list as $patch_obj){
                    if(!$this->getVersionPatchIsUse($patch_obj["patch_release"])){
                        $version_model=new VersionPatchModel();
                        $data=array(
                            "patch_type"=>$patch_obj["patch_type"],
                            "patch_type_name"=>$patch_obj["patch_type_name"],
                            "patch_release"=>$patch_obj["patch_release"],
                            "patch_name"=> $patch_obj["patch_name"],
                            "patch_no"=>  $patch_obj["patch_no"],
                            "patch_file_name"=> $patch_obj["patch_file_name"],
                            "patch_log"=>  $patch_obj["patch_log"],
                            "patch_file_size"=>  $patch_obj["patch_file_size"],
                            "is_up"=>  0,
                            "modify_date"=>time()
                        );
                        $version_model->save($data);
                    }
                }
            }   
        }
    }
    /**
     * 通过补丁编号 判断本地是否需要添加
     * @param unknown $patch_release
     */
    private function getVersionPatchIsUse($patch_release){
        $is_have=false;
        $version_model=new VersionPatchModel();
        $patch_list=$version_model->getQuery(["patch_release"=>$patch_release], "*", "");
        if(!empty($patch_list) && count($patch_list)>0){
            $is_have=true;
        }
        return $is_have;
    }
    /**
     * 得到当前用户应该升级的版本
     * (non-PHPdoc)
     * @see \data\api\system\IUpgrade::getVersionPatch()
     */
    public function getVersionPatch(){
        $post_data=array(
          "patch_no"=>$this->current_version  
        );
        $result=$this->doPost($post_data);
        return $result;
    }
    /**
     * post 服务器请求
     * @param unknown $post_data
     * @return mixed
     */
    private function doPost($post_url, $post_data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $post_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        
        if( $post_data != '' && !empty( $post_data ) ){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
//             curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($post_data)));
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 版本补丁列表
     * (non-PHPdoc)
     * @see \data\api\niushop\IProduct::getProductVersionList()
     */
    public function getProductPatchList($page_index = 1, $page_size = 0,  $condition = '', $order = '')
    {
        $product_patch = new VersionPatchModel();
        $list = $product_patch->pageQuery($page_index, $page_size, $condition, $order, '*');
        return $list;
    }
    /**
     * 判断是否能够升级，条件允许就升级，否则返回错误
     */
     public function getProductPatch($patch_type,$is_up,$patch_release){
         $product_patch = new VersionPatchModel();
         //升级条件is_up=0 并且在 patch_type 中patch_release 最小 
         $patch_list = $product_patch->getQuery("patch_type=$patch_type and patch_release<$patch_release and is_up=0 ", "*", "");

         return   count($patch_list);
         
     }
    /**
     * 得到补丁的具体信息
     * (non-PHPdoc)
     * @see \data\api\IUpgrade::getVersionPatchDetail()
     */
    public function getVersionPatchDetail($patch_release, $user_name, $password){
        $post_url=$this->service_Url."/api/Version/getVersionPatchDetail";
        $post_data=array(
            "patch_release"=>$patch_release,
            "user_name"=>$user_name,
            "password"=>$password
        );
        $result=$this->doPost($post_url, $post_data);
        return json_decode($result, true);
    }
    /**
     * 修改更新状态
     */
    public function updateVersionPatchState($patch_release){
        $product_patch = new VersionPatchModel();
        $data=array(
            "is_up"=>1
        );
        $product_patch->save($data,['patch_release'=>$patch_release]);
    }
    /**
     * 得到所有需要升级的补丁
     */
    public function getUpgradePatchList(){
        $product_patch = new VersionPatchModel();
        $data=array(
          "is_up"=>0  
        );
        $patch_list=$product_patch->getQuery($data, "*", "patch_release");
        return $patch_list;
    }
    /**
     *  查询授权账户表是否有数据
     * (non-PHPdoc)
     * @see \data\api\niushop\IProduct::getProductVersionList()
     */
    public function getVersionDevolution()
    {
        $product_devolution = new VersionDevolutionModel();
        $res = $product_devolution->getQuery("1=1", "*", "");
        return $res;
    }
    /**
     * 给授权账户添加一条数据
     * @param unknown $user_name
     * @param unknown $password
     */
    public function addVersionDevolution($user_name, $password){
        $product_devolution = new VersionDevolutionModel();
        $data=array(
            "devolution_username"=>$user_name,
            "devolution_password"=>$password,
            "create_date"=> time()
        );
        $devolution_list = $this->getVersionDevolution();
        if(count($devolution_list)>0){
            foreach ($devolution_list as $devolution_obj){
                $product_devolution->destroy($devolution_obj["id"]);
            }
        }
        $product_devolution = new VersionDevolutionModel();
        $revel = $product_devolution->save($data);
       return $revel;
    }
    
    
}

