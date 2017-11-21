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
use data\service\system\User  as userservice;

/**
 * 后台主界面
 * 
 * @author Administrator
 *        
 */
class Member extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    function personaldata(){
        $uid = $this->user->uid;
       // dump($this->user->uid);
        $shop_id = isset($this->user->instance_id) ? $this->user->instance_id : 0;
        $member = new userservice;
        $member_info = $member->getUserInfoByUid($uid);
        //var_dump($member_info);
        $this->assign('member_info',$member_info);
        //dump($shop_id);
        $this->assign('shop_id',$shop_id);
        return view($this->style . "/Member/personalData");
    }    
}
