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



use data\service\User as userservice;
use data\service\Member\MemberAccount;
use data\service\Member;
use data\service\promotion\PromoteRewardRule;

/**
 * 后台主界面
 *
 * @author Administrator
 *        
 */
class User extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // 获取信息
        $uid = ! empty($_POST['uid']) ? $_POST['uid'] : '';
        $instance_id = ! empty($_POST['instance_id']) ? $_POST['instance_id'] : '';
        // 处理信息
        $user = new userservice();
        $res = $user->getUserInfoByUid($uid);
        $member_account = new MemberAccount();
        // 积分
        $res['point'] = $member_account->getMemberPoint($uid, '');
        // 余额
        $res['balance'] = $member_account->getMemberBalance($uid);
        // 购物币
        $res['coin'] = $member_account->getMemberCoin($uid);
        // 是否签到
        $rewardRule = new Member();
        $is_signIn = $rewardRule->getIsMemberSign($uid, $instance_id);
        $res['is_signIn'] = $is_signIn;
        // dump($res);
        // $this->outMessage($user);
        // 返回信息
        if ($res) {
            return $this->outMessage('niu_index_response', $res);
        } else {
            return $this->outMessage('niu_index_response', $res, - 50, '失败！');
        }
    }

    public function signIn()
    {
        // 接收信息
        $uid = ! empty($_POST['uid']) ? $_POST['uid'] : '';
        $instance_id = ! empty($_POST['instance_id']) ? $_POST['instance_id'] : '';
        // 处理信息
        $rewardRule = new PromoteRewardRule();
        $res = $rewardRule->memberSign($uid, $instance_id);
        // 返回信息
        if ($res != 1) {
            return $this->outMessage('niu_index_response', $res);
        } else {
            return $this->outMessage('niu_index_response', $res, - 50, '失败！');
        }
    }
}
