<?php
/**
 * Promotion.php
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

use data\service\Address;
use data\service\Config;
use data\service\promotion\PromoteRewardRule;
use data\service\Promotion as PromotionService;

/**
 * 营销控制器
 *
 * @author Administrator
 *        
 */
class Promotion extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 优惠券类型列表
     *
     * @return multitype:number unknown |Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function couponTypeList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $search_text = request()->post('search_text', '');
            $coupon = new PromotionService();
            $condition = array(
                'shop_id' => $this->instance_id,
                'coupon_name' => array(
                    'like',
                    '%' . $search_text . '%'
                )
            );
            $list = $coupon->getCouponTypeList($page_index, $page_size, $condition, 'start_time desc');
            return $list;
        } else {
            return view($this->style . "Promotion/couponTypeList");
        }
    }

    /**
     * 删除优惠券类型
     */
    public function delcoupontype()
    {
        $coupon_type_id = request()->post('coupon_type_id', '');
        if (empty($coupon_type_id)) {
            $this->error("没有获取到优惠券信息");
        }
        $coupon = new PromotionService();
        $res = $coupon->deletecouponType($coupon_type_id);
        return AjaxReturn($res);
    }

    /**
     * 添加优惠券类型
     */
    public function addCouponType()
    {
        if (request()->isAjax()) {
            $coupon_name = request()->post('coupon_name', '');
            $money = request()->post('money', '');
            $count = request()->post('count', '');
            $max_fetch = request()->post('max_fetch', '');
            $at_least = request()->post('at_least', '');
            $need_user_level = request()->post('need_user_level', '');
            $range_type = request()->post('range_type', '');
            $start_time = request()->post('start_time', '');
            $end_time = request()->post('end_time', '');
            $is_show = request()->post('is_show', '');
            $goods_list = request()->post('goods_list', '');
            $coupon = new PromotionService();
            $retval = $coupon->addCouponType($coupon_name, $money, $count, $max_fetch, $at_least, $need_user_level, $range_type, $start_time, $end_time, $is_show, $goods_list);
            return AjaxReturn($retval);
        } else {
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "promotion/coupontypelist",
                        'menu_name' => "优惠券",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            return view($this->style . "Promotion/addCouponType");
        }
    }

    public function updateCouponType()
    {
        $coupon = new PromotionService();
        if (request()->isAjax()) {
            $coupon_type_id = request()->post('coupon_type_id', '');
            $coupon_name = request()->post('coupon_name', '');
            $money = request()->post('money', '');
            $count = request()->post('count', '');
            $repair_count = request()->post('repair_count', '');
            $max_fetch = request()->post('max_fetch', '');
            $at_least = request()->post('at_least', '');
            $need_user_level = request()->post('need_user_level', '');
            $range_type = request()->post('range_type', '');
            $start_time = request()->post('start_time', '');
            $end_time = request()->post('end_time', '');
            $is_show = request()->post('is_show', '');
            $goods_list = request()->post('goods_list', '');
            $retval = $coupon->updateCouponType($coupon_type_id, $coupon_name, $money, $count, $repair_count, $max_fetch, $at_least, $need_user_level, $range_type, $start_time, $end_time, $is_show, $goods_list);
            return AjaxReturn($retval);
        } else {
            $coupon_type_id = request()->get('coupon_type_id', 0);
            if ($coupon_type_id == 0) {
                $this->error("没有获取到类型");
            }
            $coupon_type_data = $coupon->getCouponTypeDetail($coupon_type_id);
            $goods_id_array = array();
            foreach ($coupon_type_data['goods_list'] as $k => $v) {
                $goods_id_array[] = $v['goods_id'];
            }
            $coupon_type_data['goods_id_array'] = $goods_id_array;
            $this->assign("coupon_type_info", $coupon_type_data);
            
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "promotion/coupontypelist",
                        'menu_name' => "优惠券",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            
            return view($this->style . "Promotion/updateCouponType");
        }
    }

    /**
     * 获取优惠券详情
     */
    public function getCouponTypeInfo()
    {
        $coupon = new PromotionService();
        $coupon_type_id = request()->post('coupon_type_id', '');
        $coupon_type_data = $coupon->getCouponTypeDetail($coupon_type_id);
        return $coupon_type_data;
    }

    /**
     * 功能：积分管理
     * 创建：左骐羽
     * 时间：2016年12月8日15:02:16
     */
    public function pointConfig()
    {
        $pointConfig = new PromotionService();
        if (request()->isAjax()) {
            $convert_rate = request()->post('convert_rate', '');
            $is_open = request()->post('is_open', 0);
            $desc = request()->post('desc', 0);
            $retval = $pointConfig->setPointConfig($convert_rate, $is_open, $desc);
            return AjaxReturn($retval);
        }
        $child_menu_list = array(
            array(
                'url' => "promotion/pointconfig",
                'menu_name' => "积分管理",
                "active" => 1
            ),
            array(
                'url' => "promotion/integral",
                'menu_name' => "积分奖励",
                "active" => 0
            )
        );
        $this->assign('child_menu_list', $child_menu_list);
        $pointconfiginfo = $pointConfig->getPointConfig();
        $this->assign("pointconfiginfo", $pointconfiginfo);
        return view($this->style . "Promotion/pointConfig");
    }

    /**
     * 赠品列表
     * wzy
     */
    public function giftList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $search_text = request()->post('search_text');
            $gift = new PromotionService();
            $condition = array(
                'shop_id' => $this->instance_id,
                'gift_name' => array(
                    'like',
                    '%' . $search_text . '%'
                )
            );
            $list = $gift->getPromotionGiftList($page_index, $page_size, $condition);
            return $list;
        }
        return view($this->style . "Promotion/giftList");
    }

    /**
     * 添加赠品
     *
     * @return \think\response\View
     */
    public function addGift()
    {
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $gift_name = request()->post('gift_name', '');
            $start_time = request()->post('start_time', '');
            $end_time = request()->post('end_time', '');
            $days = request()->post('days', '');
            $max_num = request()->post('max_num', '');
            $goods_id_array = request()->post('goods_id_array', '');
            $gift = new PromotionService();
            $res = $gift->addPromotionGift($shop_id, $gift_name, $start_time, $end_time, $days, $max_num, $goods_id_array);
            return AjaxReturn($res);
        }
        return view($this->style . "Promotion/addGift");
    }

    /**
     * 修改赠品
     *
     * @return \think\response\View
     */
    public function updateGift()
    {
        $gift = new PromotionService();
        if (request()->isAjax()) {
            $gift_id = request()->post('gift_id', '');
            $shop_id = $this->instance_id;
            $gift_name = request()->post('gift_name', '');
            $start_time = request()->post('start_time', '');
            $end_time = request()->post('end_time', '');
            $days = request()->post('days', '');
            $max_num = request()->post('max_num', '');
            $goods_id_array = request()->post('goods_id_array', '');
            $res = $gift->updatePromotionGift($gift_id, $shop_id, $gift_name, $start_time, $end_time, $days, $max_num, $goods_id_array);
            return AjaxReturn($res);
        } else {
            $gift_id = request()->get('gift_id', 0);
            if (! is_numeric($gift_id)) {
                $this->error('未获取到信息');
            }
            $info = $gift->getPromotionGiftDetail($gift_id);
            $this->assign('info', $info);
            return view($this->style . "Promotion/updateGift");
        }
    }

    /**
     * 获取赠品 详情
     *
     * @param unknown $gift_id            
     */
    public function getGiftInfo($gift_id)
    {
        $gift = new PromotionService();
        $info = $gift->getPromotionGiftDetail($gift_id);
        return $info;
    }

    /**
     * 满减送 列表
     */
    public function mansongList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $status = request()->post('status', '');
            $condition = array(
                'shop_id' => $this->instance_id,
                'mansong_name' => array(
                    'like',
                    '%' . $search_text . '%'
                )
            );
            $mansong = new PromotionService();
            if ($status !== '-1') {
                $condition['status'] = $status;
                $list = $mansong->getPromotionMansongList($page_index, $page_size, $condition);
            } else {
                $list = $mansong->getPromotionMansongList($page_index, $page_size, $condition);
            }
            return $list;
        }
        
        $status = request()->get('status', - 1);
        $this->assign("status", $status);
        $child_menu_list = array(
            array(
                'url' => "promotion/mansonglist",
                'menu_name' => "全部",
                "active" => $status == '-1' ? 1 : 0
            ),
            array(
                'url' => "promotion/mansonglist?status=0",
                'menu_name' => "未发布",
                "active" => $status == 0 ? 1 : 0
            ),
            array(
                'url' => "promotion/mansonglist?status=1",
                'menu_name' => "进行中",
                "active" => $status == 1 ? 1 : 0
            ),
            array(
                'url' => "promotion/mansonglist?status=3",
                'menu_name' => "已关闭",
                "active" => $status == 3 ? 1 : 0
            ),
            array(
                'url' => "promotion/mansonglist?status=4",
                'menu_name' => "已结束",
                "active" => $status == 4 ? 1 : 0
            )
        );
        $this->assign('child_menu_list', $child_menu_list);
        return view($this->style . "Promotion/mansongList");
    }

    /**
     * 添加满减送活动
     *
     * @return \think\response\View
     */
    public function addMansong()
    {
        $mansong = new PromotionService();
        if (request()->isAjax()) {
            $mansong_name = request()->post('mansong_name', '');
            $start_time = request()->post('start_time', '');
            $end_time = request()->post('end_time', '');
            $shop_id = $this->instance_id;
            $type = request()->post('type', '');
            $range_type = request()->post('range_type', '');
            $rule = request()->post('rule', '');
            $goods_id_array = request()->post('goods_id_array', '');
            $res = $mansong->addPromotionMansong($mansong_name, $start_time, $end_time, $shop_id, '', $type, $range_type, $rule, $goods_id_array);
            return AjaxReturn($res);
        } else {
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "promotion/mansonglist",
                        'menu_name' => "满减送",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            return view($this->style . "Promotion/addMansong");
        }
    }

    /**
     * 修改 满减送活动
     */
    public function updateMansong()
    {
        $mansong = new PromotionService();
        if (request()->isAjax()) {
            $mansong_id = request()->post('mansong_id', '');
            $mansong_name = request()->post('mansong_name', '');
            $start_time = request()->post('start_time', '');
            $end_time = request()->post('end_time', '');
            $shop_id = $this->instance_id;
            $type = request()->post('type', '');
            $range_type = request()->post('range_type', '');
            $rule = request()->post('rule', '');
            $goods_id_array = request()->post('goods_id_array', '');
            $res = $mansong->updatePromotionMansong($mansong_id, $mansong_name, $start_time, $end_time, $shop_id, '', $type, $range_type, $rule, $goods_id_array);
            return AjaxReturn($res);
        } else {
            $mansong_id = request()->get('mansong_id', '');
            if (! is_numeric($mansong_id)) {
                $this->error('未获取到信息');
            }
            $info = $mansong->getPromotionMansongDetail($mansong_id);
            $condition = array(
                'shop_id' => $this->instance_id
            );
            $coupon_type_list = $mansong->getCouponTypeList(1, 0, $condition);
            $gift_list = $mansong->getPromotionGiftList(1, 0, $condition);
            $this->assign('coupon_type_list', $coupon_type_list);
            $this->assign('gift_list', $gift_list);
            $this->assign('mansong_info', $info);
            $child_menu_list = array(
                array(
                    'url' => "javascript:;",
                    'menu_name' => $this->module_info['module_name'],
                    'active' => 1,
                    "superior_menu" => array(
                        'url' => "promotion/mansonglist",
                        'menu_name' => "满减送",
                        'active' => 1,
                    )
                )
            );
            $this->assign("child_menu_list", $child_menu_list);
            return view($this->style . "Promotion/updateMansong");
        }
    }

    /**
     * 获取限时折扣；列表
     */
    public function getDiscountList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $status = request()->post('status', '');
            $discount = new PromotionService();
            
            $condition = array(
                'shop_id' => $this->instance_id,
                'discount_name' => array(
                    'like',
                    '%' . $search_text . '%'
                )
            );
            if ($status !== '-1') {
                $condition['status'] = $status;
                $list = $discount->getPromotionDiscountList($page_index, $page_size, $condition);
            } else {
                $list = $discount->getPromotionDiscountList($page_index, $page_size, $condition);
            }
            
            return $list;
        }
        
        $status = request()->get('status', - 1);
        $this->assign("status", $status);
        $child_menu_list = array(
            array(
                'url' => "promotion/getdiscountList",
                'menu_name' => "全部",
                "active" => $status == '-1' ? 1 : 0
            ),
            array(
                'url' => "promotion/getdiscountList?status=0",
                'menu_name' => "未发布",
                "active" => $status == 0 ? 1 : 0
            ),
            array(
                'url' => "promotion/getdiscountList?status=1",
                'menu_name' => "进行中",
                "active" => $status == 1 ? 1 : 0
            ),
            array(
                'url' => "promotion/getdiscountList?status=3",
                'menu_name' => "已关闭",
                "active" => $status == 3 ? 1 : 0
            ),
            array(
                'url' => "promotion/getdiscountList?status=4",
                'menu_name' => "已结束",
                "active" => $status == 4 ? 1 : 0
            )
        );
        $this->assign('child_menu_list', $child_menu_list);
        
        return view($this->style . "Promotion/getDiscountList");
    }

    /**
     * 添加限时折扣
     */
    public function addDiscount()
    {
        if (request()->isAjax()) {
            $discount = new PromotionService();
            $discount_name = request()->post('discount_name', '');
            $start_time = request()->post('start_time', '');
            $end_time = request()->post('end_time', '');
            $remark = '';
            $goods_id_array = request()->post('goods_id_array', '');
            $retval = $discount->addPromotiondiscount($discount_name, $start_time, $end_time, $remark, $goods_id_array);
            return AjaxReturn($retval);
        }
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "promotion/getdiscountlist",
                    'menu_name' => "限时折扣",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "Promotion/addDiscount");
    }

    /**
     * 修改限时折扣
     */
    public function updateDiscount()
    {
        if (request()->isAjax()) {
            $discount = new PromotionService();
            $discount_id = request()->post('discount_id', '');
            $discount_name = request()->post('discount_name', '');
            $start_time = request()->post('start_time', '');
            $end_time = request()->post('end_time', '');
            $remark = '';
            $goods_id_array = request()->post('goods_id_array', '');
            $retval = $discount->updatePromotionDiscount($discount_id, $discount_name, $start_time, $end_time, $remark, $goods_id_array);
            return AjaxReturn($retval);
        }
        $info = $this->getDiscountDetail();
        if (! empty($info['goods_list'])) {
            foreach ($info['goods_list'] as $k => $v) {
                $goods_id_array[] = $v['goods_id'];
            }
        }
        $info['goods_id_array'] = $goods_id_array;
        $this->assign("info", $info);
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "promotion/getdiscountlist",
                    'menu_name' => "限时折扣",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "Promotion/updateDiscount");
    }

    /**
     * 获取限时折扣详情
     */
    public function getDiscountDetail()
    {
        $discount_id = request()->get('discount_id', '');
        if (! is_numeric($discount_id)) {
            $this->error("没有获取到折扣信息");
        }
        $discount = new PromotionService();
        $detail = $discount->getPromotionDiscountDetail($discount_id);
        return $detail;
    }

    /**
     * 获取满减送详情
     */
    public function getMansongDetail()
    {
        $mansong_id = request()->get('mansong_id', '');
        if (! is_numeric($mansong_id)) {
            $this->error("没有获取到满减送信息");
        }
        $mansong = new PromotionService();
        $detail = $mansong->getPromotionMansongDetail($mansong_id);
        return $detail;
    }

    /**
     * 删除限时折扣
     */
    public function delDiscount()
    {
        $discount_id = request()->post('discount_id', '');
        if (empty($discount_id)) {
            $this->error("没有获取到折扣信息");
        }
        $discount = new PromotionService();
        $res = $discount->delPromotionDiscount($discount_id);
        return AjaxReturn($res);
    }

    /**
     * 关闭正在进行的限时折扣
     */
    public function closeDiscount()
    {
        $discount_id = request()->post('discount_id', '');
        if (! is_numeric($discount_id)) {
            $this->error("没有获取到折扣信息");
        }
        $discount = new PromotionService();
        $res = $discount->closePromotionDiscount($discount_id);
        return AjaxReturn($res);
    }

    /**
     * 删除满减送活动
     *
     * @return unknown[]
     */
    public function delMansong()
    {
        $mansong_id = request()->post('mansong_id', '');
        if (empty($mansong_id)) {
            $this->error("没有获取到满减送信息");
        }
        $mansong = new PromotionService();
        $res = $mansong->delPromotionMansong($mansong_id);
        return AjaxReturn($res);
    }

    /**
     * 关闭满减送活动
     *
     * @return unknown[]
     */
    public function closeMansong()
    {
        $mansong_id = request()->post('mansong_id', '');
        if (! is_numeric($mansong_id)) {
            $this->error("没有获取到满减送信息");
        }
        $mansong = new PromotionService();
        $res = $mansong->closePromotionMansong($mansong_id);
        return AjaxReturn($res);
    }

    /**
     * 满额包邮
     */
    public function fullShipping()
    {
        $full = new PromotionService();
        if (request()->isAjax()) {
            $is_open = request()->post('is_open', '');
            $full_mail_money = request()->post('full_mail_money', '');
            $no_mail_province_id_array = request()->post('no_mail_province_id_array', '');
            $no_mail_city_id_array = request()->post("no_mail_city_id_array", '');
            $res = $full->updatePromotionFullMail(0, $is_open, $full_mail_money, $no_mail_province_id_array, $no_mail_city_id_array);
            return AjaxReturn($res);
        } else {
            $info = $full->getPromotionFullMail($this->instance_id);
            $this->assign("info", $info);
            $existing_address_list['province_id_array'] = explode(',', $info['no_mail_province_id_array']);
            $existing_address_list['city_id_array'] = explode(',', $info['no_mail_city_id_array']);
            $address = new Address();
            // 目前只支持省市，不支持区县，在页面上不会体现 2017年9月14日 19:18:08 王永杰
            $address_list = $address->getAreaTree($existing_address_list);
            $this->assign("address_list", $address_list);
            $no_mail_province_id_array = '';
            if (! empty($existing_address_list['province_id_array'])) {
                foreach ($existing_address_list['province_id_array'] as $v) {
                    $no_mail_province_id_array[] = $address->getProvinceName($v);
                }
            }
            $no_mail_province = implode(',', $no_mail_province_id_array);
            $this->assign("no_mail_province", $no_mail_province);
            return view($this->style . "Promotion/fullShipping");
        }
    }

    /**
     * 单店基础版积分奖励
     */
    public function integral()
    {
        $child_menu_list = array(
            array(
                'url' => "promotion/pointconfig",
                'menu_name' => "积分管理",
                "active" => 0
            ),
            array(
                'url' => "promotion/integral",
                'menu_name' => "积分奖励",
                "active" => 1
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
            $click_point = request()->post("click_point", 0);
            $comment_point = request()->post("comment_point", 0);
            $res = $rewardRule->setPointRewardRule($shop_id, $sign_point, $share_point, $reg_member_self_point, $reg_member_one_point, $reg_member_two_point, $reg_member_three_point, $reg_promoter_self_point, $reg_promoter_one_point, $reg_promoter_two_point, $reg_promoter_three_point, $reg_partner_self_point, $reg_partner_one_point, $reg_partner_two_point, $reg_partner_three_point, $click_point, $comment_point);
            return AjaxReturn($res);
        }
        $res = $rewardRule->getRewardRuleDetail($this->instance_id);
        $Config = new Config();
        $integralConfig = $Config->getIntegralConfig($this->instance_id);
        $this->assign("res", $res);
        $this->assign("integralConfig", $integralConfig);
        return view($this->style . "Promotion/integral");
    }

    /**
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function setIntegralAjax()
    {
        $register = request()->post('register', 0);
        $sign = request()->post('sign', 0);
        $share = request()->post('share', 0);
        $Config = new Config();
        $retval = $Config->SetIntegralConfig($this->instance_id, $register, $sign, $share);
        return AjaxReturn($retval);
    }
}