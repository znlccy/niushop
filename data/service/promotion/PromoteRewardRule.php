<?php
/**
 * RewardRule.php
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
namespace data\service\promotion;
use data\service\BaseService;
use data\model\NsRewardRuleModel;
use data\service\Member\MemberAccount;
use data\service\NfxUser;
use data\service\NfxPromoter;
use data\service\NfxPartner;
use data\service\Member;
use data\service\Config;
use think\Controller;
/**
 * 奖励规则类
 */
class PromoteRewardRule extends BaseService{
    
    /**
     * 获取 某店铺  奖励规则详情
     * @param unknown $shop_id
     * @return unknown
     */
    public function getRewardRuleDetail($shop_id){
        $reward_rule = new NsRewardRuleModel();
        $detail = $reward_rule->get($shop_id);
        if(empty($detail)){
            $data = array(
                'shop_id'                   => $shop_id,
                'sign_point'                => 0,
                'share_point'               => 0,
                'reg_member_self_point'     => 0,
                'reg_member_one_point'      => 0,
                'reg_member_two_point'      => 0,
                'reg_member_three_point'    => 0,
                'reg_promoter_self_point'   => 0,
                'reg_promoter_one_point'    => 0,
                'reg_promoter_two_point'    => 0,
                'reg_promoter_three_point'  => 0,
                'reg_partner_self_point'    => 0,
                'reg_partner_one_point'     => 0,
                'reg_partner_two_point'     => 0,
                'reg_partner_three_point'   => 0,
                'into_store_coupon'         => 0,
                'share_coupon'              => 0,
                'click_point'               => 0,
                'comment_point'             => 0
            );
            $reward_rule->save($data);
            $detail = $reward_rule->get($shop_id);
        }
        return $detail;
    }
    
    /**
     * 设置 某店铺  积分奖励规则
     * @param unknown $shop_id
     * @param unknown $sign_point
     * @param unknown $share_point
     * @param unknown $reg_member_self_point
     * @param unknown $reg_member_one_point
     * @param unknown $reg_member_two_point
     * @param unknown $reg_member_three_point
     * @param unknown $reg_promoter_self_point
     * @param unknown $reg_promoter_one_point
     * @param unknown $reg_promoter_two_point
     * @param unknown $reg_promoter_three_point
     * @param unknown $reg_partner_self_point
     * @param unknown $reg_partner_one_point
     * @param unknown $reg_partner_two_point
     * @param unknown $reg_partner_three_point
     * @return unknown
     */
    public function setPointRewardRule($shop_id, $sign_point, $share_point, $reg_member_self_point, $reg_member_one_point, $reg_member_two_point, $reg_member_three_point, $reg_promoter_self_point, $reg_promoter_one_point, $reg_promoter_two_point, $reg_promoter_three_point, $reg_partner_self_point, $reg_partner_one_point, $reg_partner_two_point, $reg_partner_three_point,$click_point,$comment_point){
        $reward_rule = new NsRewardRuleModel();
        $data = array(
            'sign_point'                => $sign_point,
            'share_point'               => $share_point,
            'reg_member_self_point'     => $reg_member_self_point,
            'reg_member_one_point'      => $reg_member_one_point,
            'reg_member_two_point'      => $reg_member_two_point,
            'reg_member_three_point'    => $reg_member_three_point,
            'reg_promoter_self_point'   => $reg_promoter_self_point,
            'reg_promoter_one_point'    => $reg_promoter_one_point,
            'reg_promoter_two_point'    => $reg_promoter_two_point,
            'reg_promoter_three_point'  => $reg_promoter_three_point,
            'reg_partner_self_point'    => $reg_partner_self_point,
            'reg_partner_one_point'     => $reg_partner_one_point,
            'reg_partner_two_point'     => $reg_partner_two_point,
            'reg_partner_three_point'   => $reg_partner_three_point,
            'click_point'               => $click_point ,
            'comment_point'             => $comment_point
        );
        $res = $reward_rule->save($data, ['shop_id' => $shop_id]);
        return $res;
    }
    
    /**
     * 设置  某店铺   优惠券 奖励规则
     * @param unknown $shop_id
     * @param unknown $into_store_coupon
     * @param unknown $share_coupon
     * @return Ambigous <number, \think\false>
     */
    public function setCouponRewardRule($shop_id, $into_store_coupon, $share_coupon){
        $reward_rule = new NsRewardRuleModel();
        $data = array(
            'into_store_coupon'         => $into_store_coupon,
            'share_coupon'              => $share_coupon,
        );
        $res = $reward_rule->save($data, ['shop_id' => $shop_id]);
        return $res;
    }
    
    /**
     * 添加积分公用函数
     * @param unknown $shop_id
     * @param unknown $uid
     * @param unknown $number           积分数量
     * @param unknown $from_type        发生类型
     * @param unknown $text             备注
     * @return Ambigous <string, number>|number
     */
    public function addMemberPointData($shop_id, $uid, $number, $from_type, $text){
        if($number  >= 0){
            $member_account = new MemberAccount();
            $res = $member_account->addMemberAccountData($shop_id, 1, $uid, 1, $number, $from_type, 0, $text);
            return $res;
        }else {
            return 1;
        }
    }
    
    /**
     * 会员签到 操作  送积分
     */
    public function memberSign($uid, $shop_id){
        $member = new Member();
        //检测是否开启签到送积分的功能
        $config = new Config();
        $config_info = $config->getIntegralConfig($shop_id);
        if($config_info['sign_integral'] > 0){
            //判断今天是否已经签到过
            $count = $member->getIsMemberSign($uid, $shop_id);
            if($count <= 0){
                //查询 当前店铺签到赠送积分数量
                $info = $this->getRewardRuleDetail($shop_id);
                $number = $info['sign_point'];
                $res = $this->addMemberPointData($shop_id, $uid, $number, 5, '签到赠送积分');
                return $res;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 会员分享 送积分
     */
    public function memberShareSendPoint($shop_id, $uid){
        $member = new Member();
        //检测是否开启分享送积分的功能
        $config = new Config();
        $config_info = $config->getIntegralConfig($shop_id);
        if($config_info['share_integral'] > 0){
            //判断今天是否已经分享过
            $count = $member->getIsMemberShare($uid, $shop_id);
            if($count <= 0){
                //查询 当前店铺分享赠送积分数量
                $info = $this->getRewardRuleDetail($shop_id);
                $res = $this->addMemberPointData($shop_id, $uid, $info['share_point'], 6, '分享赠送积分');
                return $res;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     *  注册会员 送积分
     */
    public function RegisterMemberSendPoint($shop_id, $uid){
        //检测是否开启注册送积分的功能
        $config = new Config();
        $config_info = $config->getIntegralConfig($shop_id);
        if($config_info['register_integral'] > 0){
            $info = $this->getRewardRuleDetail($shop_id);
            $this->addMemberPointData($shop_id, $uid, $info['reg_member_self_point'], 7, '注册会员赠送积分');
            switch (NS_VERSION) {
                case NS_VER_B2C_FX:
                    //单店分销版本  上级添加积分
                    $this->SendPointMemberUpperThree($shop_id, $uid);
                    break;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 成为推广员  送积分
     * @param unknown $shop_id
     * @param unknown $uid
     */
    public function RegisterPromoterSendPoint($shop_id, $uid){
        //查询 当前店铺成为推广员赠送积分数量
        $info = $this->getRewardRuleDetail($shop_id);
        $this->addMemberPointData($shop_id, $uid, $info['reg_promoter_self_point'], 11, '成为推广员赠送积分');
        $this->SendPointPromoterUpperThree($shop_id, $uid);
    }
    
    /**
     * 成为股东  送积分
     * @param unknown $shop_id
     * @param unknown $uid
     */
    public function RegisterPartnerSendPoint($shop_id, $uid){
        //查询 当前店铺成为股东赠送积分数量
        $info = $this->getRewardRuleDetail($shop_id);
        $this->addMemberPointData($shop_id, $uid, $info['reg_promoter_self_point'], 15, '成为股东赠送积分');
        $this->SendPointPartnerUpperThree($shop_id, $uid);
    }
    
    /**
     * 会员分享 送 优惠券
     */
    public function memberShareSendCoupon($shop_id, $uid){
        
    }
    
    /**
     * 会员进店 送 优惠券
     */
    public function memberIntoStoreSendCoupon(){
        
    }
    
    /**
     * 给 会员的  上级 上上级 上上上级 加积分  
     * @param unknown $shop_id
     * @param unknown $uid
     */
    public function SendPointMemberUpperThree($shop_id, $uid){
        $info = $this->getRewardRuleDetail($shop_id);
        $array = $this->getUpperThreeLevelUidByUid($shop_id, $uid);
        if($array['user_one'] > 0){
            $this->addMemberPointData($shop_id, $array['user_one'], $info['reg_member_one_point'], 8, '推荐下级会员赠送积分');
        }
        if($array['user_two'] > 0){
            $this->addMemberPointData($shop_id, $array['user_two'], $info['reg_member_two_point'], 9, '推荐下下级会员赠送积分');
        }
        if($array['user_three'] > 0){
            $this->addMemberPointData($shop_id, $array['user_three'], $info['reg_member_three_point'], 10, '推荐下下下级会员赠送积分');
        }
    }
    
    /**
     * 给 推广员的  上级 上上级 上上上级 加积分
     * @param unknown $shop_id
     * @param unknown $uid
     */
    public function SendPointPromoterUpperThree($shop_id, $uid){
        $info = $this->getRewardRuleDetail($shop_id);
        $array = $this->getUpperPromoterThreeLevelUidByUid($shop_id, $uid);
        if($array['promoter_one'] > 0){
            $this->addMemberPointData($shop_id, $array['promoter_one'], $info['reg_promoter_one_point'], 12, '推荐下级推广员赠送积分');
        }
        if($array['promoter_two'] > 0){
            $this->addMemberPointData($shop_id, $array['promoter_two'], $info['reg_promoter_two_point'], 13, '推荐下下级推广员赠送积分');
        }
        if($array['promoter_three'] > 0){
            $this->addMemberPointData($shop_id, $array['promoter_three'], $info['reg_promoter_three_point'], 14, '推荐下下下级推广员赠送积分');
        }
    }
    
    /**
     * 给 股东的  上级 上上级 上上上级 加积分
     * @param unknown $shop_id
     * @param unknown $uid
     */
    public function SendPointPartnerUpperThree($shop_id, $uid){
        $info = $this->getRewardRuleDetail($shop_id);
        $array = $this->getUpperPartnerThreeLevelUidByUid($shop_id, $uid);
        if($array['partner_one'] > 0){
            $this->addMemberPointData($shop_id, $array['partner_one'], $info['reg_partner_one_point'], 16, '推荐下级股东赠送积分');
        }
        if($array['partner_two'] > 0){
            $this->addMemberPointData($shop_id, $array['partner_two'], $info['reg_partner_two_point'], 17, '推荐下下级股东赠送积分');
        }
        if($array['partner_three'] > 0){
            $this->addMemberPointData($shop_id, $array['partner_three'], $info['reg_partner_three_point'], 18, '推荐下下下级股东赠送积分');
        }
    }
    /**
     * 根据 uid 查询 会员  上级  上上级  上上上级 uid
     * 返回 array
     * @param unknown $uid
     */
    private function getUpperThreeLevelUidByUid($shop_id, $uid){
        $nfx_user = new NfxUser();
        $array = array(
            'user_one' => 0,
            'user_two' => 0,
            'user_three' => 0,
        );
        if($uid > 0){
            $data_one = $nfx_user->getUserParent($shop_id, $uid);
            $array['user_one'] = $data_one['source_uid'] > 0 ? $data_one['source_uid'] : 0;
            if($data_one['source_uid'] > 0){
                $data_two = $nfx_user->getUserParent($shop_id, $data_one['source_uid']);
                $array['user_two'] = $data_two['source_uid'] > 0 ? $data_two['source_uid'] : 0;
                if($data_two['source_uid'] > 0){
                    $data_three = $nfx_user->getUserParent($shop_id, $data_two['source_uid']);
                    $array['user_three'] = $data_three['source_uid'] > 0 ? $data_three['source_uid'] : 0;
                }
            }
        }
        return $array;
    }
    
    /**
     * 根据 uid 查询 推广员 上级 上上级 上上上级 uid
     */
    private function getUpperPromoterThreeLevelUidByUid($shop_id, $uid){
        $nfx_promoter = new NfxPromoter();
        $array = array(
            'promoter_one' => 0,
            'promoter_two' => 0,
            'promoter_three' => 0,
        );
        if($uid > 0){
            $data_one = $nfx_promoter->getPromoterParentByUidAndShopid($shop_id, $uid);
            $array['promoter_one'] = $data_one['parent_uid'] > 0 ? $data_one['parent_uid'] : 0;
            if($data_one['parent_uid'] > 0){
                $data_two = $nfx_promoter->getPromoterParentByUidAndShopid($shop_id, $data_one['parent_uid']);
                $array['promoter_two'] = $data_two['parent_uid'] > 0 ? $data_two['parent_uid'] : 0;
                if($data_two['parent_uid'] > 0){
                    $data_three = $nfx_promoter->getPromoterParentByUidAndShopid($shop_id, $data_two['parent_uid']);
                    $array['promoter_three'] = $data_three['parent_uid'] > 0 ? $data_three['parent_uid'] : 0;
                }
            }
        }
        return $array;
    }
    
    /**
     * 根据 uid 查询  股东  上级 上上级 上上上级 uid
     * @param unknown $shop_id
     * @param unknown $uid
     */
    private function getUpperPartnerThreeLevelUidByUid($shop_id, $uid){
        $nfx_partner = new NfxPartner();
        $array = array(
            'partner_one' => 0,
            'partner_two' => 0,
            'partner_three' => 0,
        );
        if($uid > 0){
            $data_one = $nfx_partner->getPartnerParentByUidAndShopid($shop_id, $uid);
            $array['partner_one'] = $data_one['parent_uid'] > 0 ? $data_one['parent_uid'] : 0;
            if($data_one['parent_uid'] > 0){
                $data_two = $nfx_partner->getPartnerParentByUidAndShopid($shop_id, $data_one['parent_uid']);
                $array['partner_two'] = $data_two['parent_uid'] > 0 ? $data_two['parent_uid'] : 0;
                if($data_two['parent_uid'] > 0){
                    $data_three = $nfx_promoter->getPartnerParentByUidAndShopid($shop_id, $data_two['parent_uid']);
                    $array['partner_three'] = $data_three['parent_uid'] > 0 ? $data_three['parent_uid'] : 0;
                }
            }
        }
        return $array;
    }
}