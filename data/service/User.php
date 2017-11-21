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

use \think\Session as Session;
use data\service\BaseService as BaseService;
use data\api\IUser as IUser;
use data\model\UserModel as UserModel;
use data\model\AdminUserModel as AdminUserModel;
use data\model\AuthGroupModel as AuthGroupModel;
use data\model\UserLogModel as UserLogModel;
use data\model\InstanceModel;
use data\model\WebSiteModel;
use data\model\ModuleModel;
use data\model\WeixinFansModel;
use data\model\NsMemberAccountModel;
use data\model\NsMemberLevelModel;
use data\model\NsShopModel;
use data\model\NsMemberModel;
use data\model\BaseModel;

class User extends BaseService implements IUser
{

    function __construct()
    {
        parent::__construct();
        $this->user = new UserModel();
    }
    /**
     * 
     * @return unknown
     */
    public function getUserInfo()
    {
        $res = $this->user->getInfo('uid=' . $this->uid, '*');
        return $res;
    }
    /**
     * (non-PHPdoc)
     * @see \ata\api\IUser::getUserInfoByUid()
     */
    public function getUserInfoByUid($uid)
    {
        $res = $this->user->getInfo('uid=' . $uid, '*');
         
        return $res;
    }
    /**
     * 根据用户名获取用户信息
     */
    public function getUserInfoByUsername($username)
    {
        $res = $this->user->getInfo(['user_name'=>$username],'*');
         
        return $res;
    }
    /**
     * 根据用户名修改密码
     */
     public function  updateUserInfoByUsername($username,$password){
         
         $data = array(
             'user_password' => md5($password)
         );
         $retval = $this->user->save($data, ['user_name' => $username]);
         return $retval;
     }
     /**
      * (non-PHPdoc)
      * @see \data\api\IUser::updateUserInfoByUserid()
      */
     public function  updateUserInfoByUserid($userid,$password){
          
         $data = array(
             'user_password' => md5($password)
         );
         $retval = $this->user->save($data, ['uid' => $userid]);
         return $retval;
     }
     /**
      * (non-PHPdoc)
      * @see \data\api\IUser::updateUsertelByUserid()
      */
     public function  updateUsertelByUserid($userid,$user_tel){
     
         $data = array(
             'user_tel' => $user_tel,
             'user_tel_bind'=>1
         );
         $retval = $this->user->save($data, ['uid' => $userid]);
         return $retval;
     }
    /**
     * 获取当前登录用户的uid
     */
    public function getSessionUid()
    {
        return $this->uid;
    }

    /**
     * 获取当前登录用户的实例ID
     */
    public function getSessionInstanceId()
    {
        return $this->instance_id;
    }

    /**
     * 获取当前登录用户是否是总系统管理员
     */
    public function getSessionUserIsAdmin()
    {
        return $this->is_admin;
    }

    /**
     * 获取当前登录用户是否是前台会员
     */
    public function getSessionUserIsMember()
    {
        return $this->is_member;
    }

    public function getSessionUserIsSystem()
    {
        return $this->is_system;
    }

    /**
     * 获取当前登录用户的权限列
     */
    public function getSessionModuleIdArray()
    {
        return $this->module_id_array;
    }

    public function getInstanceName()
    {
        if(empty($this->instance_name))
        {
            $web_site = new WebSiteModel();
            $info = $web_site->getInfo('', 'title');
            return $info['title'];
        }else{
            return $this->instance_name;
        }
     
    }
/**
     * 用户登录之后初始化数据
     * @param unknown $user_info
     */
    private function initLoginInfo($user_info)
    {
        $model = $this->getRequestModel();
        Session::set($model.'uid', $user_info['uid']);
        Session::set($model.'is_system', $user_info['is_system']);
        Session::set($model.'is_member', $user_info['is_member']);
        Session::set($model.'instance_id', $user_info['instance_id']);
        //单店版本
        $website = new WebSiteModel();
        $instance_name = $website->getInfo('', 'title');
        Session::set($model.'instance_name', $instance_name['title']);
        if ($user_info['is_system']) {
            $admin_info = new AdminUserModel();
            $admin_info = $admin_info->getInfo('uid=' . $user_info['uid'], 'is_admin,group_id_array');
            Session::set($model.'is_admin', $admin_info['is_admin']);
            $auth_group = new AuthGroupModel();
            $auth = $auth_group->get($admin_info['group_id_array']);
            $no_control = $this->getNoControlAuth();
            Session::set($model.'module_id_array', $no_control.$auth['module_id_array']);
        }
        $data = array(
            'last_login_time' => $user_info['current_login_time'],
            'last_login_ip' => $user_info['current_login_ip'],
            'last_login_type' => $user_info['current_login_type'],
            'current_login_ip' => request()->ip(),
            'current_login_time' => time(),
            'current_login_type'  => 1
        );
        //离线购物车同步
        $goods = new Goods();
        $goods->syncUserCart($user_info['uid']);
        $retval = $this->user->save($data,['uid' => $user_info['uid']]);
        //用户登录成功钩子
        hook("userLoginSuccess", $user_info);
        return $retval;
    }
    /**
     * 系统用户登录
     *
     * @param unknown $user_name            
     * @param unknown $password            
     */
    public function login($user_name, $password = '')
    {
        $this->Logout();
        $condition = array(
            'user_name' => $user_name,
            'user_password' => md5($password)
        );
        $user_info = $this->user->getInfo($condition, $field = 'uid,user_status,user_name,is_system,instance_id, is_member, current_login_ip, current_login_time, current_login_type');
        
        if (empty($user_info)) {
            if(empty($password)){
                $condition = array(
                    'user_tel' => $user_name
                );
            }else{
                $condition = array(
                    'user_tel' => $user_name,
                    'user_password' => md5($password)
                );
            }
            
            $user_info = $this->user->getInfo($condition, $field = 'uid,user_status,user_name,is_system,instance_id, is_member, current_login_ip, current_login_time, current_login_type');
        }
        if (empty($user_info)) {
            $condition = array(
                'user_email' => $user_name,
                'user_password' => md5($password)
            );
            $user_info = $this->user->getInfo($condition, $field = 'uid,user_status,user_name,is_system,instance_id, is_member, current_login_ip, current_login_time, current_login_type');
        }
        if (! empty($user_info)) {
            if ($user_info['user_status'] == 0) {
                return USER_LOCK;
           
            } else {
                //登录成功后增加用户的登录次数
                $this->user->where("user_name|user_tel|user_email", "eq", $user_name)
                           ->setInc('login_num', 1);
                $this->initLoginInfo($user_info);
                return 1;
            }
        } else
            return USER_ERROR;
    }
    /**
     * uid登录
     * @param unknown $uid
     */
    public function UidLogin($uid)
    {
        $user_info = $this->user->getInfo('uid = '.$uid, $field = 'uid,user_status,user_name,is_system,instance_id, is_member, current_login_ip, current_login_time, current_login_type');
        if (! empty($user_info)) {
            if ($user_info['user_status'] == 0) {
                return USER_LOCK;
                 
            } else {
                $this->initLoginInfo($user_info);
                return 1;
            }
        }
    }
    /**
     * 通过账号密码 来更新会员的微信信息
     * @param unknown $user_name
     * @param string $password
     */
    public function updateUserWchat($user_name, $password, $wx_openid, $wx_info, $wx_unionid)
    {
        $condition = array(
            'user_name' => $user_name,
            'user_password' => md5($password)
        );
        $user_info = $this->user->getInfo($condition, $field = 'uid,user_status,user_name,is_system,instance_id, is_member, current_login_ip, current_login_time, current_login_type');
        
        if (empty($user_info)) {
            if(empty($password)){
                $condition = array(
                    'user_tel' => $user_name
                );
            }else{
                $condition = array(
                    'user_tel' => $user_name,
                    'user_password' => md5($password)
                );
            }
        
            $user_info = $this->user->getInfo($condition, $field = 'uid,user_status,user_name,is_system,instance_id, is_member, current_login_ip, current_login_time, current_login_type');
        }
        if (empty($user_info)) {
            $condition = array(
                'user_email' => $user_name,
                'user_password' => md5($password)
            );
            $user_info = $this->user->getInfo($condition, $field = 'uid,user_status,user_name,is_system,instance_id, is_member, current_login_ip, current_login_time, current_login_type');
        }
        
        if (!empty($user_info)) {
            if (! empty($wx_openid) || !empty($wx_unionid)) {
                $wx_info_array = json_decode($wx_info);
                $nick_name = $this->filterStr($wx_info_array->nickname);
                $user_head_img = $wx_info_array->headimgurl;
                $wx_info = $this->filterStr($wx_info);
            } else {
                $user_head_img = '';
            }
            
            $local_path = '';
            if(!empty($user_head_img))
            {
                if(!file_exists('upload/user')){
                    $mode = intval('0777',8);
                    mkdir('upload/user',$mode,true);
                    if(!file_exists('upload/user'))
                    {
                        die('upload/user不可写，请检验读写权限!');
                    }
                }
                $local_path = 'upload/user/'.time().rand(111,999).'.png';
                save_weixin_img($local_path, $user_head_img);
            }
            $data = array(
                'user_headimg' => $local_path,
                'nick_name' => $nick_name,
                'wx_openid' => $wx_openid,
                'wx_info' => $wx_info,
                'wx_unionid'  => $wx_unionid
            );
            $user_model=new UserModel();
            $user_model->save($data, ["uid"=>$user_info['uid']]);
        }
    }

    /**
     * 获取不控制权限模块组
     */
    private function getNoControlAuth()
    {
        $moudle = new ModuleModel();
        $list = $moudle->getQuery([
            "is_control_auth" => 0
        ], "module_id",'');
        $str = "";
        foreach ($list as $v) {
            $str .= $v["module_id"] . ",";
        }
        return $str;
    }

    /*
     * qq登录(non-PHPdoc)
     * @see \data\api\IMember::qqLogin()
     */
    public function qqLogin($qq)
    {
        $this->Logout();
        $condition = array(
            'qq_openid' => $qq
        );
        $user_info = $this->user->getInfo($condition, $field = 'uid,user_status,user_name,is_system,instance_id,is_member, current_login_ip, current_login_time, current_login_type');
        if (! empty($user_info)) {
            if ($user_info['user_status'] == 0) {
                return USER_LOCK;
            } else {
               $this->initLoginInfo($user_info);
                return 1;
            }
        } else
            return USER_NBUND;
        // TODO Auto-generated method stub
    }

    /*
     * 微信第三方登录(non-PHPdoc)
     * @see \data\api\IMember::wchatLogin()
     */
    public function wchatLogin($openid)
    {
        $this->Logout();
        $condition = array(
            'wx_openid' => $openid
        );
        $user_info = $this->user->getInfo($condition, $field = 'uid,user_status,user_name,is_system,instance_id,is_member, current_login_ip, current_login_time, current_login_type');
        if (! empty($user_info)) {
            if ($user_info['user_status'] == 0) {
                return USER_LOCK;
            } else {
               $this->initLoginInfo($user_info);
                return 1;
            }
        } else
            return USER_NBUND;
        // TODO Auto-generated method stub
    }
    /**
     * 判断openid 在数据库中存不存在
     * @param unknown $openid
     */
    public function getUserCountByOpenid($openid){
        $condition = array(
            'wx_openid' => $openid
        );
        $user_count = $this->user->getCount($condition);
        return $user_count;
    }
    /**
     * 微信unionid登录(non-PHPdoc)
     * @see \ata\api\IUser::wchatUnionLogin()
     */
    public function wchatUnionLogin($unionid)
    {
        $this->Logout();
        $condition = array(
            'wx_unionid' => $unionid
        );
        $user_info = $this->user->getInfo($condition, $field = 'uid,user_status,user_name,is_system,instance_id,is_member, current_login_ip, current_login_time, current_login_type');
        if (! empty($user_info)) {
            if ($user_info['user_status'] == 0) {
                return USER_LOCK;
            } else {
                $this->initLoginInfo($user_info);
                return 1;
            }
        } else
            return USER_NBUND;
    }
    /**
     * 当前只针对存在unionid不存在openid(non-PHPdoc)
     * @see \ata\api\IUser::modifyUserWxhatLogin()
     */
    public function modifyUserWxhatLogin($wx_openid, $wx_unionid)
    {
        $user_info = $this->user->getInfo(['wx_unionid' => $wx_unionid], 'wx_openid,wx_unionid');
        if(!empty($user_info))
        {
            if(empty($user_info['wx_openid']))
            {
                $data = array(
                    'wx_openid' => $wx_openid
                );
                $retval = $this->user->save($data, ['wx_unionid' => $wx_unionid]);
            }else{
                $retval = 1;
            }
          
        }else{
            $retval = 1;
        }
    }
    /**
     * 检测用户是否具有打开权限(non-PHPdoc)
     *
     * @see \data\api\IAdmin::checkAuth()
     */
    public function checkAuth($module_id)
    {
        if ($this->is_admin) {
            return 1;
        } else {
            $module_id_array = explode(',', $this->module_id_array);
            if (in_array($module_id, $module_id_array)) {
                return 1;
            } else {
                return 0;
            }
        }
    }
    

    /**
     * 系统用户基础添加方式
     *
     * @param unknown $user_name            
     * @param unknown $password            
     * @param unknown $email            
     * @param unknown $mobile            
     */
    public function add($user_name, $password, $email, $mobile, $is_system, $qq_openid, $qq_info, $wx_openid, $wx_info,$wx_unionid, $is_member, $instance_id = 0)
    {
        if (! empty($user_name)) {
           
            $count = $this->user->where([
                'user_name' => $user_name
            ])->count();
            if ($count > 0) {
                return USER_REPEAT;
            }
            $nick_name = $user_name;
        }elseif(! empty($mobile))
        {
            $count = $this->user->where([
                'user_tel' => $mobile
            ])->count();
            if ($count > 0) {
                return USER_REPEAT;
            }
            $nick_name = $mobile;
        }elseif(!empty($email))
        {
            $count = $this->user->where([
                'user_email' => $email
            ])->count();
            if ($count > 0) {
                return USER_REPEAT;
            }
            $nick_name = $email;
        }
        
   
        if (! empty($qq_openid)) {
            $qq_info_array = json_decode($qq_info);
            $nick_name = $this->filterStr($qq_info_array->nickname);
            $user_head_img = $qq_info_array->figureurl_qq_2;
            $qq_info = $this->filterStr($qq_info);
        } elseif (! empty($wx_openid) || !empty($wx_unionid)) {
            $wx_info_array = json_decode($wx_info);
            $nick_name = $this->filterStr($wx_info_array->nickname);
            $user_head_img = $wx_info_array->headimgurl;
            $wx_info = $this->filterStr($wx_info);
        } else {
            $user_head_img = '';
        }
        $local_path = '';
         if(!empty($user_head_img))
        {
            if(!file_exists('upload/user')){
                $mode = intval('0777',8);
                mkdir('upload/user',$mode,true);
                if(!file_exists('upload/user'))
                {
                    die('upload/user不可写，请检验读写权限!');
                }
            }
            $local_path = 'upload/user/'.time().rand(111,999).'.png';
            save_weixin_img($local_path, $user_head_img);
        } 
        
        /*
         * if(empty($user_name))
         * {
         * $user_name = $this->createUserName();
         * }
         */
        $data = array(
            'user_name' => $user_name,
            /* 'real_password' => $password, */
            'user_password' => md5($password),
            'user_status' => 1,
            'user_headimg' => $local_path,
            'nick_name' => $nick_name,
            'is_system' => (bool) $is_system,
            'is_member' => (bool) $is_member,
            'user_tel' => $mobile,
            'user_tel_bind' => 0,
            'user_qq' => '',
            'qq_openid' => $qq_openid,
            'qq_info' => $qq_info,
            'reg_time' => time(),
            'login_num' => 0,
            'user_email' => $email,
            'user_email_bind' => 0,
            'wx_openid' => $wx_openid,
            'wx_sub_time' => '0',
            'wx_notsub_time' => '0',
            'wx_is_sub' => 0,
            'wx_info' => $wx_info,
            'other_info' => '',
            'instance_id' => $instance_id,
            'wx_unionid'  => $wx_unionid
        );
        $this->user->save($data);
        $uid = $this->user->uid;
        //用户添加成功后
        $data['uid'] = $uid;
        hook("userAddSuccess", $data);
        return $uid;
    }
    /**
     * 过滤特殊字符
     * @param unknown $str
     */
    private function filterStr($str)
    {
        if($str){
            $name = $str;
            $name = preg_replace_callback('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/',function ($matches) { return '';}, $name);
            $name = preg_replace_callback('/xE0[x80-x9F][x80-xBF]‘.‘|xED[xA0-xBF][x80-xBF]/S',function ($matches) { return '';}, $name);
            // 汉字不编码
            $name = json_encode($name);
            $name = preg_replace_callback("/\\\ud[0-9a-f]{3}/i", function ($matches) { return '';}, $name);
            if(!empty($name))
            {
                $name = json_decode($name);
                return $name;
            }else{
                return '';
            }
             
        }else{
            return '';
        }
    }
    public function updateUserInfo($uid, $user_name, $email, $sex, $status, $mobile, $nick_name)
    {
        $user_info = $this->user->getInfo(['uid' => $uid], '*');
        //前期判断
        if (! empty($user_name)) {
             if($user_info['user_name'] != $user_name)
             {
                 $count = $this->user->where([
                     'user_name' => $user_name
                 ])->count();
                 if ($count > 0) {
                     return USER_REPEAT;
                 }
             }
           
        }
        if(! empty($mobile))
        {
            if($user_info['user_tel'] != $mobile)
            {
                $count = $this->user->where([
                    'user_tel' => $mobile
                ])->count();
                if ($count > 0) {
                    return USER_MOBILE_REPEAT;
                }
            }
        }
        if(!empty($email))
        {
            if($user_info['user_email'] != $email)
            {
                $count = $this->user->where([
                    'user_email' => $email
                ])->count();
                if ($count > 0) {
                    return USER_EMAIL_REPEAT;
                }
            }
        }
        if(empty($nick_name))
        {
            $nick_name = $user_name;
        }
        $data = array(
            'user_name' => $user_name,
            'user_tel'  => $mobile,
            'user_email'=> $email,
            'sex' => $sex,
            'user_status' => $status,
            'nick_name' => $nick_name
        );
        $retval = $this->user->save($data, ['uid' => $uid]);
        return $retval;
        
    }

    /**
     * 创建生成用户名
     *
     * @return string
     */
    protected function createUserName()
    {
        $user_name = "n" . date("ymdh" . rand(1111, 9999));
        return $user_name;
    }

    /**
     * 系统用户修改密码
     *
     * @param unknown $uid            
     * @param unknown $old_password            
     * @param unknown $new_password            
     */
    public function ModifyUserPassword($uid, $old_password, $new_password)
    {
        $condition = array(
            'uid' => $uid,
            'user_password' => md5($old_password)
        );
        $res = $this->user->getInfo($condition, $field = "uid");
        if (! empty($res['uid'])) {
            $data = array(
                'user_password' => md5($new_password)
            );
            $res = $this->user->save($data, [
                'uid' => $uid
            ]);
            return $res;
        } else
            return PASSWORD_ERROR;
    }
    /**
     * 
     * @param unknown $uid
     * @param unknown $user_name
     * @return number|string|Ambigous <number, \think\false, boolean, string>
     */
    public function ModifyUserName($uid, $user_name)
    
    {
        $info = $this->user->get($uid);
        if ($info['user_name'] == $user_name) {
            return 1;
        }
        $count = $this->user->where([
            'user_name' => $user_name
        ])->count();
        if ($count > 0) {
            return USER_REPEAT;
        }
        $data = array(
            'user_name' => $user_name
        );
        $res = $this->user->save($data, [
            'uid' => $uid
        ]);
        return $res;
    }

    /**
     * 添加用户登录日志
     *
     * @param unknown $uid            
     * @param unknown $url            
     * @param unknown $desc            
     */
    public function addUserLog($uid, $is_system, $controller, $method, $ip, $get_data)
    {
        $data = array(
            'uid' => $uid,
            'is_system' => $is_system,
            'controller' => $controller,
            'method' => $method,
            'ip' => $ip,
            'data' => $get_data,
            'create_time' => time()
        );
        $user_log = new UserLogModel();
        $res = $user_log->save($data);
        return $res;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IUser::getUserDetail()
     */
    public function getUserDetail()
    {
        $user_info = $this->user->get($this->uid);
        if (! empty($user_info['qq_openid'])) {
            $qq_info = json_decode($user_info['qq_info'], true);
            $user_info['qq_info_array'] = $qq_info;
        }
        if (! empty($user_info['wx_openid'])) {
            $wx_info = json_decode($user_info['wx_info'], true);
        }
        return $user_info;
    }

    /**
     * 会员锁定
     * (non-PHPdoc)
     *
     * @see \ata\api\IUser::userLock()
     */
    public function userLock($uid)
    {
        $retval = $this->user->save([
            'user_status' => 0
        ], [
            'uid' => $uid
        ]);
        return $retval;
    }

    /**
     * 会员解锁
     *
     * @param unknown $uid            
     * @return number|\think\false
     */
    public function userUnlock($uid)
    {
        $retval = $this->user->save([
            'user_status' => 1
        ], [
            'uid' => $uid
        ]);
        return $retval;
    }

 /**
     * 用户退出
     */
    public function Logout()
    {
        $model = $this->getRequestModel();
        Session::set($model.'uid', '');
        Session::set($model.'is_admin', 0);
        Session::set($model.'module_id_array', '');
        Session::set($model.'instance_name', '');
        Session::set($model.'is_member', '');
        Session::set($model.'is_system', '');
        Session::set('module_list', []);
        $_SESSION["user_cart"] = '';
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IUser::modifyMobile()
     */
    public function modifyMobile($uid, $mobile)
    {
        $retval = $this->user->save([
            'user_tel' => $mobile
        ], [
            'uid' => $uid
        ]);
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IUser::modifyMobile()
     */
    public function modifyNickName($uid, $nickname)
    {
        $retval = $this->user->save([
            'nick_name' => $nickname
        ], [
            'uid' => $uid
        ]);
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IUser::modifyEmail()
     */
    public function modifyEmail($uid, $email)
    {
        $retval = $this->user->save([
            'user_email' => $email
        ], [
            'uid' => $uid
        ]);
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IUser::modifyQQ()
     */
    public function modifyQQ($uid, $qq)
    {
        $retval = $this->user->save([
            'user_qq' => $qq
        ], [
            'uid' => $uid
        ]);
        return $retval;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IUser::resetUserPassword()
     */
    public function resetUserPassword($uid)
    {
        $retval = $this->user->save([
            'user_password' => md5(123456)
        ], [
            'uid' => $uid
        ]);
        return 1;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IUser::ModifyUserHeadimg()
     */
    public function ModifyUserHeadimg($uid, $user_headimg)
    {
        $info = $this->user->get($uid);
        if ($info['user_headimg'] == $user_headimg) {
            return 1;
        }
        $data = array(
            'user_headimg' => $user_headimg
        );
        $res = $this->user->save($data, [
            'uid' => $uid
        ]);
        return $res;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IUser::userTelBind()
     */
    public function userTelBind($uid)
    {
        return $this->user->save([
            'user_tel_bind' => 1
        ], [
            'uid' => $uid
        ]);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IUser::removeUserTelBind()
     */
    public function removeUserTelBind($uid)
    {
        return $this->user->save([
            'user_tel_bind' => 0
        ], [
            'uid' => $uid
        ]);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IUser::userTelBind()
     */
    public function userEmailBind($uid)
    {
        return $this->user->save([
            'user_email_bind' => 1
        ], [
            'uid' => $uid
        ]);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IUser::removeUserTelBind()
     */
    public function removeUserEmailBind($uid)
    {
        return $this->user->save([
            'user_email_bind' => 0
        ], [
            'uid' => $uid
        ]);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IUser::checkUserQQopenid()
     */
    public function checkUserQQopenid($qq_openid)
    {
        $user = new UserModel();
        return $user->where([
            'qq_openid' => $qq_openid
        ])->count();
    }

    public function checkUserWchatopenid($openid)
    {
        $user = new UserModel();
        return $user->where([
            'wx_openid' => $openid
        ])->count();
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IUser::bindQQ()
     */
    public function bindQQ($qq_openid, $qq_info)
    {
        $data = array(
            'qq_openid' => $qq_openid,
            'qq_info' => $qq_info
        );
        $res = $this->user->save($data, [
            'uid' => $this->uid
        ]);
        return $res;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IUser::removeBindQQ()
     */
    public function removeBindQQ()
    {
        $data = array(
            'qq_openid' => '',
            'qq_info' => ''
        );
        $res = $this->user->save($data, [
            'uid' => $this->uid
        ]);
        return $res;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IMember::memberIsMobile()
     */
    public function memberIsMobile($mobile)
    {
        $mobile_info = $this->user->get([
            'user_tel' => $mobile
        ]);
        return ! empty($mobile_info);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IMember::memberIsEmail()
     */
    public function memberIsEmail($email)
    {
        $email_info = $this->user->get([
            'user_email' => $email
        ]);
        return ! empty($email_info);
    }
    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IUser::getUserInfoDetail()
     */
    public function getUserInfoDetail($uid){
        $user_info = $this->user->getInfo(array("uid"=>$uid));
        $member = new NsMemberModel();
        $user_info['member'] = $member->getInfo(array("uid"=>$uid));
        return $user_info;
    }
    
    /**
     * (non-PHPdoc)
     * @see \ata\api\IUser::checkUserIsSubscribe()
     */
    public function checkUserIsSubscribe($uid)
    {
        $user_info = $this->user->getInfo(['uid' => $uid], 'openid');
        if(!empty($user_info['openid']))
        {
            $weixin_fans = new WeixinFansModel();
            $count = $weixin_fans->where(['openid' => $user_info['openid'],'is_subscribe' => 1])->count();
            if($count > 0)
            {
                return 1;
            }else{
                return 0;
            }
        } else{
            return 0;
        }
    }
    /**
     * (non-PHPdoc)
     * @see \ata\api\IUser::checkUserIsSubscribeInstance()
     */
    public function checkUserIsSubscribeInstance($uid, $instance_id)
    {
        $user_info = $this->user->getInfo(['uid' => $uid], 'wx_openid');
        if(!empty($user_info['wx_openid']))
        {
            $weixin_fans = new WeixinFansModel();
            $count = $weixin_fans->where(['openid' => $user_info['wx_openid'],'is_subscribe' => 1])->count();
            if($count > 0)
            {
                return 1;
            }else{
                return 0;
            }
        } else{
            return 0;
        }
    
        
    }
	/* (non-PHPdoc)
     * @see \ata\api\IUser::getUserCount()
     */
    public function getUserCount($condition)
    {
        // TODO Auto-generated method stub
        $user= new UserModel();
        $user_list = $user->getQuery($condition, "count(*) as count", '');
        return $user_list[0]["count"];
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IUser::checkMobileIsHas()
     */
    public function checkMobileIsHas($mobile){
        $user= new UserModel();
        $count = $user->getCount(['user_tel' => $mobile]);
        return $count;
    }
    /**
     * 根据用户邮箱更改密码
     */
    public function updateUserPasswordByEmail($userInfo, $password){
        $data = array(
            'user_password' => md5($password)
        );
        $retval = $this->user->save($data, ['user_email' => $userInfo]);
        return $retval;
    }
    /**
     * 根据用户邮箱更改密码
     */
    public function updateUserPasswordByMobile($userInfo, $password){
        $data = array(
            'user_password' => md5($password)
        );
        $retval = $this->user->save($data, ['user_tel' => $userInfo]);
        return $retval;
    }
    /**
     * 会员等级自动升级
     * (non-PHPdoc)
     * @see \data\api\IUser::updateUserLevel()
     */
    public function updateUserLevel($shop_id, $user_id){
        $member_model=new NsMemberModel();
        $count = $member_model->getCount(['uid' => $user_id]);
        if($count == 0)
        {
            return;
        }
        $shop_server=new Shop();
        #得到会员的消费金额
        $money=$shop_server->getShopUserConsume($shop_id, $user_id);
        #得到会员的累计积分
        $member_account_model=new NsMemberAccountModel();
        $member_account_obj=$member_account_model->getInfo(["uid"=>$user_id], "member_sum_point");
        $member_sum_point=0;
        if(!empty($member_account_obj)){
            $member_sum_point=$member_account_obj["member_sum_point"];
        }
        #得到会员的信息
        $member_obj=$member_model->get($user_id);
        if(!empty($member_obj)){
            $level_id=$member_obj["member_level"];
            $member_level_model=new NsMemberLevelModel();
            $level_list=$member_level_model->getQuery("(upgrade=3 and relation=1 and (min_integral<=".$member_sum_point." or quota<=".$money.")) or (upgrade=3 and relation=2 and min_integral<=".$member_sum_point." and quota<=".$money.") or (upgrade=1 and min_integral<=".$member_sum_point." ) or (upgrade=2 and quota<=".$money.")", "*", "goods_discount");
            $member_level_model=new NsMemberLevelModel();
            $member_level_obj=$member_level_model->get($level_id);
            if(!empty($level_list) && count($level_list)>0){
                $update_level_obj=$level_list[0];
                $update_goods_discount=$update_level_obj["goods_discount"];
                $goods_discount=1;
                if(!empty($member_level_obj)){
                    $goods_discount=$member_level_obj["goods_discount"];
                }
                if($update_goods_discount<$goods_discount){
                    $member_model->save(["member_level"=>$update_level_obj["level_id"]], ["uid"=>$user_id]);
                }
            }
        }
    }
}

