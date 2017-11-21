<?php
/**
 * IUser.php
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
namespace data\api;

/**
 * 系统用户业务接口
 */
interface IUser
{
    
    /**
     * 获取当前登录用户的uid
     */
    function getSessionUid();

    /**
     * 获取当前登录用户的实例ID
     */
    function getSessionInstanceId();
    /**
     * 根据用户名获取用户信息
     * @param unknown $username
     */
    public function getUserInfoByUsername($username);
    /**
     * 根据用户名修改密码
     * @param unknown $username
     * @param unknown $password
     */
    public function  updateUserInfoByUsername($username,$password);
    /**
     * 根据用户id修改密码
     */
    public function  updateUserInfoByUserid($userid,$password);
    /**
     * 获取当前登录用户是否是总系统管理员
     */
    function getSessionUserIsAdmin();

    /**
     * 获取当前登录会员是否是前台会员
     */
    function getSessionUserIsMember();

    /**
     * 获取当前登录会员是否是系统会员
     */
    function getSessionUserIsSystem();

    /**
     * 获取当前登录用户的权限列
     */
    function getSessionModuleIdArray();

    /**
     * 获取当前实例
     */
    function getInstanceName();

    /**
     * qq登录
     * 
     * @param unknown $qq            
     */
    function qqLogin($qq);

    /**
     * 微信登录
     * 
     * @param unknown $openid            
     */
    function wchatLogin($openid);
    /**
     * 微信unionid登录
     * @param unknown $unionid
     */
    function wchatUnionLogin($unionid);
    /**
     * 更新会员openid与unionid
     * @param unknown $wx_openid
     * @param unknown $wx_unionid
     */
    function modifyUserWxhatLogin($wx_openid, $wx_unionid);

    /**
     * 系统添加会员
     * 
     * @param unknown $user_name            
     * @param unknown $password            
     * @param unknown $email            
     * @param unknown $mobile            
     * @param unknown $is_system            
     * @param unknown $user_qq_id            
     * @param unknown $qq_info            
     * @param unknown $wx_openid            
     * @param unknown $wx_info            
     */
    function add($user_name, $password, $email, $mobile, $is_system, $qq_openid, $qq_info, $wx_openid, $wx_info, $wx_unionid, $is_member, $instance_id);

    /**
     * 系统用户登录
     * 
     * @param unknown $user_name            
     * @param unknown $password            
     */
    function login($user_name, $password = '');

    /**
     * 检测用户是否具有打开权限
     * 
     * @param unknown $module_id            
     */
    function checkAuth($module_id);

    /**
     * 系统用户修改密码
     * 
     * @param unknown $uid            
     * @param unknown $old_password            
     * @param unknown $new_password            
     */
    function ModifyUserPassword($uid, $old_password, $new_password);

    /**
     * 添加用户日志
     * 
     * @param unknown $uid            
     * @param unknown $is_system            
     * @param unknown $controller            
     * @param unknown $method            
     * @param unknown $ip            
     * @param unknown $get_data            
     */
    function addUserLog($uid, $is_system, $controller, $method, $ip, $get_data);

    /**
     * 获取用户详细信息
     */
    function getUserDetail();

    /**
     * 用户锁定
     * 
     * @param unknown $uid            
     */
    function userLock($uid);

    /**
     * 用户解锁
     * 
     * @param unknown $uid            
     */
    function userUnlock($uid);

    /**
     * 用户退出
     */
    function Logout();

    /**
     * 修改手机
     * 
     * @param unknown $mobile            
     */
    function modifyMobile($uid, $mobile);

    /**
     * 修改昵称
     * 
     * @param unknown $uid            
     * @param unknown $nickname            
     */
    function modifyNickName($uid, $nickname);

    /**
     * 修改邮箱
     * 
     * @param unknown $email            
     */
    function modifyEmail($uid, $email);

    /**
     * 修改qq
     * 
     * @param unknown $uid            
     * @param unknown $qq            
     */
    function modifyQQ($uid, $qq);

    /**
     * 重置密码 123456
     * 
     * @param unknown $uid            
     */
    function resetUserPassword($uid);

    /**
     * 修改头像
     * 
     * @param unknown $uid            
     * @param unknown $user_headimg            
     */
    function ModifyUserHeadimg($uid, $user_headimg);

    /**
     * 会员手机号 绑定
     * 
     * @param unknown $uid            
     */
    function userTelBind($uid);

    /**
     * 会员手机号 解除绑定
     * 
     * @param unknown $uid            
     */
    function removeUserTelBind($uid);

    /**
     * 会员邮箱 绑定
     * 
     * @param unknown $uid            
     */
    function userEmailBind($uid);

    /**
     * 会员邮箱 解除绑定
     * 
     * @param unknown $uid            
     */
    function removeUserEmailBind($uid);

    /**
     * 判断 某qq 是否 已经绑定
     * 
     * @param unknown $qq_openid            
     */
    function checkUserQQopenid($qq_openid);

    /**
     * 绑定 qq
     * 
     * @param unknown $uid            
     * @param unknown $qq_openid            
     * @param unknown $qq_info            
     */
    function bindQQ($qq_openid, $qq_info);

    /**
     * 解除 会员 绑定
     */
    function removeBindQQ();

    /**
     * 查询手机号是否已被绑定
     * 
     * @param unknown $mobile            
     */
    function memberIsMobile($mobile);

    /**
     * 查询邮箱是否已被绑定
     * 
     * @param unknown $email            
     */
    function memberIsEmail($email);

    /**
     * 根据uid查询用户信息
     * 
     * @param unknown $uid            
     */
    function getUserInfoByUid($uid);
    /**
     * 获取用户信息
     * @param unknown $uid
     */
    function getUserInfoDetail($uid);
    /**
     * 检测用户是否关注了实例公众号(应用多用户版)
     * @param unknown $uid
     * @param unknown $instance_id
     */
    function checkUserIsSubscribeInstance($uid, $instance_id);
    /**
     * 检测用户是否关注了当前实例（单用户版）
     * @param unknown $uid
     */
    function checkUserIsSubscribe($uid);
    /**
     * 获取用户
     * @param unknown $condition
     */
    function getUserCount($condition);
    /**
     * 获取用户详细详情
     * @param unknown $uid
     */
    function getUserInfo();
    /**
     * 修改会员信息
     * @param unknown $uid
     * @param unknown $user_name
     * @param unknown $email
     * @param unknown $mobile
     * @param unknown $nick_name
     */
    function updateUserInfo($uid, $user_name, $email, $sex, $status, $mobile, $nick_name);
    
    /**
     * 检测手机号是否存在
     * @param unknown $mobile
     */
    function checkMobileIsHas($mobile);
    
    /**
     * 根据用户邮箱更改密码
     */
    function updateUserPasswordByEmail($userInfo,$password);
    /**
     * 根据用户邮箱更改密码
     */
    function updateUserPasswordByMobile($userInfo,$password);
    /**
     * 自定更新用户的会员等级
     * @param unknown $shop_id
     * @param unknown $user_id
     */
    function updateUserLevel($shop_id, $user_id);
    /**
     * 微信登录绑定手机号
     * @param unknown $userid
     * @param unknown $user_tel
     */
    function  updateUsertelByUserid($userid,$user_tel);
}

