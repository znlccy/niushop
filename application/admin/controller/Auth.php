<?php
/**
 * Auth.php
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
namespace app\admin\controller;

/**
 * 用户权限控制器
 */
use data\service\AuthGroup as AuthGroup;

class Auth extends BaseController
{

    private $auth_group;

    public function __construct()
    {
        parent::__construct();
        $this->auth_group = new AuthGroup();
    }

    /**
     * 用户列表
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function userList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text','');
            $condition = [];
            if(!empty($search_text)){
                $condition = array(
                    'sur.instance_id' => $this->instance_id,
                    'sur.user_name|sua.uid' => array(
                        'like',
                        '%' . $search_text . '%'
                    )
                );
            }
            $user_list = $this->user->adminUserList($page_index, $page_size, $condition);
            return $user_list;
        } else {
            return view($this->style . 'Auth/userList');
        }
    }

    /**
     * 用户日志，Hidden
     *
     * @return unknown|\think\response\View
     */
    public function userLoglist()
    {
        if (request()->isAjax()) {
            $page_index = request()->post('pageIndex',1);
            $condition = "";
            $list = $this->user->getUserLogList($page_index, PAGESIZE, $condition, "create_time desc");
            return $list;
        } else {
            return view($this->style . "Auth/userLoglist");
        }
    }

    /**
     * 用户组列表
     */
    public function authGroupList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $list = $this->auth_group->getSystemUserGroupList($page_index, $page_size, [
                'instance_id' => $this->instance_id
            ]);
            return $list;
        } else {
            $permissionList = $this->website->getInstanceModuleQuery();
            $firstArray = array();
            $p = array();
            for ($i = 0; $i < count($permissionList); $i ++) {
                $per = $permissionList[$i];
                if ($per["pid"] == 0 && $per["module_name"] != null) {
                    $firstArray[] = $per;
                }
            }
            for ($i = 0; $i < count($firstArray); $i ++) {
                $first_per = $firstArray[$i];
                $secondArray = array();
                for ($y = 0; $y < count($permissionList); $y ++) {
                    $childPer = $permissionList[$y];
                    if ($childPer["pid"] == $first_per["module_id"]) {
                        $secondArray[] = $childPer;
                    }
                }
                $first_per['child'] = $secondArray;
                for ($j = 0; $j < count($secondArray); $j ++) {
                    $second_per = $secondArray[$j];
                    $threeArray = array();
                    for ($z = 0; $z < count($permissionList); $z ++) {
                        $three_per = $permissionList[$z];
                        if ($three_per["pid"] == $second_per["module_id"]) {
                            $threeArray[] = $three_per;
                        }
                    }
                    $second_per['child'] = $threeArray;
                }
                $p[] = $first_per;
                $first_per = array();
            }
            $this->assign("list", $p);
            return view($this->style . 'Auth/authGroupList');
        }
    }

    /**
     * 添加或者编辑用户组
     */
    public function addUserGroup()
    {
        $group_id = request()->post('roleId',0);
        $module_id_array = request()->post('array','');
        $role_name = request()->post('roleName','');
        if ($group_id != 0) {
            $retval = $this->auth_group->updateSystemUserGroup($group_id, $role_name, 1, false, $module_id_array, '');
        } else {
            $retval = $this->auth_group->addSystemUserGroup(0, $role_name, false, $module_id_array, '');
        }
        return AjaxReturn($retval);
    }

    public function editUserGroup()
    {
        $group_id = request()->post('group_id',0);
        return view($this->style . "Auth/addUserGroup");
    }

    /**
     * 添加 后台用户
     */
    public function addUser()
    {
        if (request()->isAjax()) {
            $admin_name = request()->post('admin_name', '');
            $group_id = request()->post('group_id', '');
            $user_password = request()->post('user_password', '123456');
            $desc = request()->post('desc', '');
            $retval = $this->user->addAdminUser($admin_name, $group_id, $user_password, $desc, $this->instance_id);
            return AjaxReturn($retval);
        } else {
            $condition["instance_id"] = $this->instance_id;
            $list = $this->auth_group->getSystemUserGroupAll($condition);
            $this->assign('auth_group', $list);
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "auth/userlist",
                        'menu_name' => "用户列表",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            return view($this->style . 'Auth/addUser');
        }
    }

    /**
     * 修改后台用户
     */
    public function editUser()
    {
        if (request()->isAjax()) {
            $uid = request()->post('uid','');
            $admin_name = request()->post('admin_name','');
            $group_id = request()->post('group_id','');
            $desc = request()->post('desc','');
            if($uid == '' || $admin_name == '' || $group_id == ''){
                $this->error('未获取到信息');
            }
            $retval = $this->user->editAdminUser($uid, $admin_name, $group_id, $desc);
            return AjaxReturn($retval);
        } else {
            $uid = request()->get('uid',0);
            if ($uid == 0) {
                $this->error("没有获取到用户信息");
            }
            $ua_info = $this->user->getAdminUserInfo("uid = " . $uid, $field = "*");
            $this->assign("ua_info", $ua_info);
            $condition["instance_id"] = $this->instance_id;
            $list = $this->auth_group->getSystemUserGroupAll($condition);
            $this->assign('auth_group', $list);
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "auth/userlist",
                        'menu_name' => "用户列表",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            return view($this->style . 'Auth/editUser');
        }
    }

    /**
     * 删除系统用户组
     */
    public function deleteSystemUserGroup()
    {
        $group_id = request()->post('group_id','');
        if(!is_numeric($group_id)){
            $this->error("请传入正确参数");
        }
        $retval = $this->auth_group->deleteSystemUserGroup($group_id);
        return AjaxReturn($retval);
    }

    /**
     * 用户的 锁定
     */
    public function userLock()
    {
        $uid = request()->post('uid',0);
        if ($uid > 0) {
            $res = $this->user->userLock($uid);
        }
        return AjaxReturn($res);
    }

    /**
     * 解锁
     */
    public function userUnlock()
    {
        $uid = request()->post('uid',0);
        if ($uid > 0) {
            $res = $this->user->userUnlock($uid);
        }
        return AjaxReturn($res);
    }

    /**
     * 重置密码
     *
     * @return unknown[]
     */
    public function resetUserPassword()
    {
        $uid = request()->post('uid',0);
        if ($uid > 0) {
            $res = $this->user->resetUserPassword($uid);
        }
        return AjaxReturn($res);
    }

    /**
     * 个人资料页面
     */
    public function userDetail()
    {
        if (request()->isAjax()) {
            $user_headimg = request()->post('user_headimg','');
            $user_qq = request()->post('user_qq','');
            $res1 = $this->user->ModifyUserHeadimg($this->uid, $user_headimg);
            $res2 = $this->user->modifyQQ($this->uid, $user_qq);
            return AjaxReturn(1);
        }
        $_SESSION['bund_pre_url'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $info = $this->user->getUserDetail();
        $this->assign('info', $info);
        return view($this->style . "Auth/userDetail");
    }

    /**
     * 修改会员 用户名
     *
     * @return unknown[]
     */
    public function modifyUserName()
    {
        $user_name = request()->post('user_name','');
        if(!empty($user_name)){
            $res = $this->user->ModifyUserName($this->uid, $user_name);
        }
        return AjaxReturn($res);
    }

    /**
     * 会员手机号绑定
     */
    public function userTelBind()
    {
        $res = $this->user->userTelBind($this->uid);
        return AjaxReturn($res);
    }

    /**
     * 解除 会员手机号 绑定
     */
    public function removeUserTelBind()
    {
        $res = $this->user->removeUserTelBind($this->uid);
        return AjaxReturn($res);
    }

    /**
     * 修改 会员 手机号
     */
    public function modifyUserTel()
    {
        $user_tel = request()->post('user_tel','');
        if(!empty($user_tel)){
            $res = $this->user->modifyMobile($this->uid, $user_tel);
        }
        return AjaxReturn($res);
    }

    /**
     * 会员邮箱 绑定
     */
    public function userEmailBind()
    {
        $res = $this->user->userEmailBind($this->uid);
        return AjaxReturn($res);
    }

    /**
     * 解除 会员邮箱 绑定
     */
    public function removeUserEmailBind()
    {
        $res = $this->user->removeUserEmailBind($this->uid);
        return AjaxReturn($res);
    }

    /**
     * 修改 会员 邮箱
     */
    public function modifyUserEmail()
    {
        $user_email = request()->post('user_email','');
        if(!empty($user_email)){
            $res = $this->user->modifyEmail($this->uid, $user_email);
        }
        return AjaxReturn($res);
    }

    /**
     * 解除 会员 qq 绑定
     *
     * @return unknown
     */
    public function removeUserQQBind()
    {
        $res = $this->user->removeBindQQ();
        return AjaxReturn($res);
    }

    /**
     * 删除 后台会员
     */
    public function deleteAdminUserAjax()
    {
        if (request()->isAjax()) {
            $uid = request()->post("uid", "");
            if(!empty($uid)){
                $res = $this->user->deleteAdminUser($uid);
            }
            return AjaxReturn($res);
        }
    }
}