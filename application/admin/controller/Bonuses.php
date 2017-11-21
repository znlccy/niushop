<?php
/**
 * Cms.php
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

use app\admin\controller\BaseController;
use data\service\promotion\PromoteRewardRule;
use data\service\Promotion;

/**
 *
 * @author Administrator
 *         奖励规则
 *        
 */
class Bonuses extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        switch (NS_VERSION) {
            case NS_VER_B2C:
                $retval = $this->IndexFx1(); // 单店基础版
                break;
            case NS_VER_B2C_FX:
                $retval = $this->IndexFx2(); // 单店分销版
                break;
        }
        return $retval;
    }

    /**
     * 单店基础版积分奖励
     */
    public function IndexFx1()
    {
        $child_menu_list = array(
            array(
                'url' => "Bonuses/index",
                'menu_name' => "积分奖励",
                "active" => 1
            ),
            array(
                'url' => "Bonuses/coupon",
                'menu_name' => "优惠劵奖励",
                "active" => 0
            )
        );
        $this->assign('child_menu_list', $child_menu_list);
        $rewardRule = new PromoteRewardRule();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $sign_point = request()->post('sign_point', 0);
            $share_point = request()->post('share_point', 0);
            $reg_member_self_point = request()->post('reg_member_self_point', 0);
            $reg_member_one_point = 0;
            $reg_member_two_point = 0;
            $reg_member_three_point = 0;
            $reg_promoter_self_point = 0;
            $reg_promoter_one_point = 0;
            $reg_promoter_two_point = 0;
            $reg_promoter_three_point = 0;
            $reg_partner_self_point = 0;
            $reg_partner_one_point = 0;
            $reg_partner_two_point = 0;
            $reg_partner_three_point = 0;
            $res = $rewardRule->setPointRewardRule($shop_id, $sign_point, $share_point, $reg_member_self_point, $reg_member_one_point, $reg_member_two_point, $reg_member_three_point, $reg_promoter_self_point, $reg_promoter_one_point, $reg_promoter_two_point, $reg_promoter_three_point, $reg_partner_self_point, $reg_partner_one_point, $reg_partner_two_point, $reg_partner_three_point);
            return AjaxReturn($res);
        }
        $res = $rewardRule->getRewardRuleDetail($this->instance_id);
        $this->assign("res", $res);
        return view($this->style . "Bonuses/index");
    }

    /**
     * 单店带分销版
     *
     * @return unknown[]
     */
    public function IndexFx2()
    {
        $child_menu_list = array(
            array(
                'url' => "Bonuses/index",
                'menu_name' => "积分奖励",
                "active" => 1
            ),
            array(
                'url' => "Bonuses/coupon",
                'menu_name' => "优惠劵奖励",
                "active" => 0
            )
        );
        $this->assign('child_menu_list', $child_menu_list);
        $rewardRule = new PromoteRewardRule();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $sign_point = request()->post('sign_point', 0);
            $share_point = request()->post('share_point', 0);
            $reg_member_self_point = request()->post('reg_member_self_point', 0);
            $reg_member_one_point = request()->post('reg_member_one_point', 0);
            $reg_member_two_point = request()->post('$reg_member_two_point', 0);
            $reg_member_three_point = request()->post('reg_member_three_point', 0);
            $reg_promoter_self_point = request()->post('reg_promoter_self_point', 0);
            $reg_promoter_one_point = request()->post('reg_promoter_one_point', 0);
            $reg_promoter_two_point = request()->post('reg_promoter_two_point', 0);
            $reg_promoter_three_point = request()->post('reg_promoter_three_point', 0);
            $reg_partner_self_point = request()->post('reg_partner_self_point', 0);
            $reg_partner_one_point = request()->post('reg_partner_one_point', 0);
            $reg_partner_two_point = request()->post('reg_partner_two_point', 0);
            $reg_partner_three_point = request()->post('reg_partner_three_point', 0);
            
            $res = $rewardRule->setPointRewardRule($shop_id, $sign_point, $share_point, $reg_member_self_point, $reg_member_one_point, $reg_member_two_point, $reg_member_three_point, $reg_promoter_self_point, $reg_promoter_one_point, $reg_promoter_two_point, $reg_promoter_three_point, $reg_partner_self_point, $reg_partner_one_point, $reg_partner_two_point, $reg_partner_three_point);
            return AjaxReturn($res);
        }
        $res = $rewardRule->getRewardRuleDetail($this->instance_id);
        $this->assign("res", $res);
        return view($this->style . "Bonuses/indexFx");
    }

    /**
     * 优惠劵奖励
     */
    public function coupon()
    {
        $rewardRule = new PromoteRewardRule();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $into_store_coupon = request()->post('into_store_coupon', 0);
            $share_coupon = request()->post('share_coupon', 0);
            $res = $rewardRule->setCouponRewardRule($shop_id, $into_store_coupon, $share_coupon);
            return AjaxReturn($res);
        }
        $child_menu_list = array(
            array(
                'url' => "Bonuses/index",
                'menu_name' => "积分奖励",
                "active" => 0
            ),
            array(
                'url' => "Bonuses/coupon",
                'menu_name' => "优惠劵奖励",
                "active" => 1
            )
        );
        // 查询该店奖励规则
        $res = $rewardRule->getRewardRuleDetail($this->instance_id);
        $this->assign("res", $res);
        // 获取未过期的优惠劵
        $coupon = new Promotion();
        $condition['shop_id'] = $this->instance_id;
        $nowTime = date("Y-m-d H:i:s");
        $condition['end_time'] = array(
            ">",
            getTimeTurnTimeStamp($nowTime)
        );
        $list = $coupon->getCouponTypeList(1, 0, $condition);
        $this->assign("coupon", $list['data']);
        // 侧边导航
        $this->assign('child_menu_list', $child_menu_list);
        return view($this->style . "Bonuses/coupon");
    }
}
