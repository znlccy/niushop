<?php
/**
 * Promote.php
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
namespace data\service;

/**
 * 营销
 */
use data\api\IPromotion;
use data\model\AlbumPictureModel as AlbumPictureModel;
use data\model\NsCouponGoodsModel as NsCouponGoodsModel;
use data\model\NsCouponModel as NsCouponModel;
use data\model\NsCouponTypeModel as NsCouponTypeModel;
use data\model\NsGoodsModel;
use data\model\NsGoodsSkuModel;
use data\model\NsPointConfigModel;
use data\model\NsPromotionDiscountGoodsModel;
use data\model\NsPromotionDiscountModel;
use data\model\NsPromotionFullMailModel;
use data\model\NsPromotionGiftGoodsModel;
use data\model\NsPromotionGiftModel;
use data\model\NsPromotionMansongGoodsModel;
use data\model\NsPromotionMansongModel;
use data\model\NsPromotionMansongRuleModel;
use data\service\BaseService as BaseService;
use data\service\promotion\GoodsDiscount;
use data\service\promotion\GoodsMansong;

class Promotion extends BaseService implements IPromotion
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IPromotion::getCouponTypeList()
     */
    public function getCouponTypeList($page_index = 1, $page_size = 0, $condition = '', $order = 'create_time asc')
    {
        $coupon_type = new NsCouponTypeModel();
        $coupon_type_list = $coupon_type->pageQuery($page_index, $page_size, $condition, $order, 'coupon_type_id, coupon_name, money, count, max_fetch, at_least, need_user_level, range_type, start_time, end_time, create_time, update_time,is_show');
        /*
         * if(!empty($coupon_type_list['data']))
         * foreach ($coupon_type_list['data'] as $k => $v)
         * {
         * if($v['range_type'] == 0) //部分产品
         * {
         * $coupon_goods = new NsCouponGoodsModel();
         * $goods_list = $coupon_goods->getCouponTypeGoodsList($v['coupon_type_id']);
         * $coupon_type_list['data'][$k]['goods_list'] = $goods_list;
         * }
         * }
         */
        //
        return $coupon_type_list;
        // TODO Auto-generated method stub
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IPromotion::deletecouponType()
     */
    public function deletecouponType($coupon_type_id)
    {
        $coupon = new NsCouponModel();
        $coupon_type = new NsCouponTypeModel();
        $coupon_type->startTrans();
        try {
            $condition['coupon_type_id'] = $coupon_type_id;
            $condition['state'] = 1;
            $coupon_count = $coupon->getcount($condition);
            if ($coupon_count > 0) {
                $coupon_type->rollback();
                return - 1;
            }
            $coupon_type->destroy($coupon_type_id);
            $coupon_type->commit();
            return 1;
        } catch (\Exception $e) {
            $coupon_type->rollback();
            return $e->getMessage();
        }
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\ICoupon::getCouponTypeDetail()
     */
    public function getCouponTypeDetail($coupon_type_id)
    {
        $coupon_type = new NsCouponTypeModel();
        $data = $coupon_type->get($coupon_type_id);
        $coupon_goods = new NsCouponGoodsModel();
        $goods_list = $coupon_goods->getCouponTypeGoodsList($coupon_type_id);
        foreach ($goods_list as $k => $v) {
            $picture = new AlbumPictureModel();
            $pic_info = array();
            $pic_info['pic_cover'] = '';
            if (! empty($v['picture'])) {
                $pic_info = $picture->get($v['picture']);
            }
            $goods_list[$k]['picture_info'] = $pic_info;
        }
        $data['goods_list'] = $goods_list;
        return $data;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     *
     * @see \data\api\ICoupon::addCouponType()
     */
    public function addCouponType($coupon_name, $money, $count, $max_fetch, $at_least, $need_user_level, $range_type, $start_time, $end_time, $is_show, $goods_list)
    {
        $coupon_type = new NsCouponTypeModel();
        $error = 0;
        $coupon_type->startTrans();
        try {
            // 添加优惠券类型表
            /**
             * coupon_type_id int(11) NOT NULL AUTO_INCREMENT COMMENT '优惠券类型Id',
             * shop_id int(11) NOT NULL DEFAULT 1 COMMENT '店铺ID',
             * coupon_name varchar(50) NOT NULL DEFAULT '' COMMENT '优惠券名称',
             * money decimal(10, 2) NOT NULL COMMENT '发放面额',
             * count int(11) NOT NULL COMMENT '发放数量',
             * max_fetch int(11) NOT NULL DEFAULT 0 COMMENT '每人最大领取个数 0无限制',
             * at_least decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '满多少元使用 0代表无限制',
             * need_user_level tinyint(4) NOT NULL DEFAULT 0 COMMENT '领取人会员等级',
             * range_type tinyint(4) NOT NULL DEFAULT 1 COMMENT '使用范围0部分产品使用 1全场产品使用',
             * start_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '有效日期开始时间',
             * end_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '有效日期结束时间',
             * create_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
             */
            $data = array(
                'shop_id' => $this->instance_id,
                'coupon_name' => $coupon_name,
                'money' => $money,
                'count' => $count,
                'max_fetch' => $max_fetch,
                'at_least' => $at_least,
                'need_user_level' => $need_user_level,
                'range_type' => $range_type,
                'start_time' => getTimeTurnTimeStamp($start_time),
                'end_time' => getTimeTurnTimeStamp($end_time),
                'is_show' => $is_show
            );
            $coupon_type->save($data);
            $coupon_type_id = $coupon_type->coupon_type_id;
            // 添加类型商品表
            if ($range_type == 0 && ! empty($goods_list)) {
                $goods_list_array = explode(',', $goods_list);
                foreach ($goods_list_array as $k => $v) {
                    $data_coupon_goods = array(
                        'coupon_type_id' => $coupon_type_id,
                        'goods_id' => $v
                    );
                    $coupon_goods = new NsCouponGoodsModel();
                    $retval = $coupon_goods->save($data_coupon_goods);
                }
            }
            // 添加优惠券表
            if ($count > 0) {
                for ($i = 0; $i < $count; $i ++) {
                    /**
                     * coupon_id int(11) NOT NULL AUTO_INCREMENT COMMENT '优惠券id',
                     * coupon_type_id int(11) NOT NULL COMMENT '优惠券类型id',
                     * shop_id int(11) NOT NULL COMMENT '店铺Id',
                     * coupon_code varchar(255) NOT NULL DEFAULT '' COMMENT '优惠券编码',
                     * uid int(11) NOT NULL COMMENT '领用人',
                     * use_order_id int(11) NOT NULL COMMENT '优惠券使用订单id',
                     * create_order_id int(11) NOT NULL DEFAULT 0 COMMENT '创建订单id(优惠券只有是完成订单发放的优惠券时才有值)',
                     * money decimal(10, 2) NOT NULL COMMENT '面额',
                     * fetch_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '领取时间',
                     * use_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '使用时间',
                     * state tinyint(4) NOT NULL DEFAULT 0 COMMENT '优惠券状态 0未领用 1已领用（未使用） 2已使用 3已过期',
                     */
                    $data_coupon = array(
                        'coupon_type_id' => $coupon_type_id,
                        'shop_id' => $this->instance_id,
                        'coupon_code' => time() . rand(111, 999),
                        'uid' => 0,
                        'create_order_id' => 0,
                        'money' => $money,
                        'state' => 0,
                        "start_time" => getTimeTurnTimeStamp($start_time),
                        "end_time" => getTimeTurnTimeStamp($end_time)
                    );
                    $coupon = new NsCouponModel();
                    $retval = $coupon->save($data_coupon);
                }
            }
            $coupon_type->commit();
            return 1;
        } catch (\Exception $e) {
            $coupon_type->rollback();
            return $e->getMessage();
        }
        return 0;
        // TODO Auto-generated method stub
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IPromotion::updateCouponType()
     */
    public function updateCouponType($coupon_type_id, $coupon_name, $money, $count, $repair_count, $max_fetch, $at_least, $need_user_level, $range_type, $start_time, $end_time, $is_show, $goods_list)
    {
        $coupon_type = new NsCouponTypeModel();
        $error = 0;
        $coupon_type->startTrans();
        try {
            // 更新优惠券类型表
            /**
             * coupon_type_id int(11) NOT NULL AUTO_INCREMENT COMMENT '优惠券类型Id',
             * shop_id int(11) NOT NULL DEFAULT 1 COMMENT '店铺ID',
             * coupon_name varchar(50) NOT NULL DEFAULT '' COMMENT '优惠券名称',
             * money decimal(10, 2) NOT NULL COMMENT '发放面额',
             * count int(11) NOT NULL COMMENT '发放数量',
             * max_fetch int(11) NOT NULL DEFAULT 0 COMMENT '每人最大领取个数 0无限制',
             * at_least decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '满多少元使用 0代表无限制',
             * need_user_level tinyint(4) NOT NULL DEFAULT 0 COMMENT '领取人会员等级',
             * range_type tinyint(4) NOT NULL DEFAULT 1 COMMENT '使用范围0部分产品使用 1全场产品使用',
             * start_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '有效日期开始时间',
             * end_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '有效日期结束时间',
             * create_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
             */
            $data = array(
                'shop_id' => $this->instance_id,
                'coupon_name' => $coupon_name,
                'money' => $money,
                'count' => $count + $repair_count,
                'max_fetch' => $max_fetch,
                'at_least' => $at_least,
                'need_user_level' => $need_user_level,
                'range_type' => $range_type,
                'start_time' => getTimeTurnTimeStamp($start_time),
                'end_time' => getTimeTurnTimeStamp($end_time),
                'is_show' => $is_show
            );
            $coupon_type->save($data, [
                'coupon_type_id' => $coupon_type_id
            ]);
            // 更新类型商品表
            $coupon_goods = new NsCouponGoodsModel();
            $coupon_goods->destroy([
                'coupon_type_id' => $coupon_type_id
            ]);
            if ($range_type == 0 && ! empty($goods_list)) {
                $goods_list_array = explode(',', $goods_list);
                foreach ($goods_list_array as $k => $v) {
                    $data_coupon_goods = array(
                        'coupon_type_id' => $coupon_type_id,
                        'goods_id' => $v
                    );
                    $coupon_goods = new NsCouponGoodsModel();
                    $retval = $coupon_goods->save($data_coupon_goods);
                }
            }
            // 添加优惠券表
            if ($repair_count > 0) {
                for ($i = 0; $i < $repair_count; $i ++) {
                    $data_coupon = array(
                        'coupon_type_id' => $coupon_type_id,
                        'shop_id' => $this->instance_id,
                        'coupon_code' => time() . rand(111, 999),
                        'uid' => 0,
                        'create_order_id' => 0,
                        'money' => $money,
                        'state' => 0,
                        'start_time' => getTimeTurnTimeStamp($start_time),
                        'end_time' => getTimeTurnTimeStamp($end_time)
                    );
                    $coupon = new NsCouponModel();
                    $retval = $coupon->save($data_coupon);
                }
            }
            // 修改优惠券时，更新优惠券的使用状态
            $coupon = new NsCouponModel();
            $coupon_condition['state'] = array(
                'in',
                [
                    0,
                    3
                ]
            ); // 未领用或者已过期的优惠券
            $coupon_condition['coupon_type_id'] = $coupon_type_id;
            $coupon->save([
                'end_time' => getTimeTurnTimeStamp($end_time),
                'start_time' => getTimeTurnTimeStamp($start_time),
                'state' => 0
            ], $coupon_condition);
            $coupon_type->commit();
            return 1;
        } catch (\Exception $e) {
            $coupon_type->rollback();
            return 0;
        }
        return 0;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\ICoupon::getTypeCouponList()
     */
    public function getTypeCouponList($coupon_type_id, $get_type = 0, $use_type = 0)
    {
        $coupon = new NsCouponModel();
        $condition = array(
            'coupon_type_id' => $coupon_type_id,
            'state' => $use_type
        );
        $list = $coupon->pageQuery(1, 0, $condition, '', '*');
        return $list['data'];
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\ICoupon::useCoupon()
     */
    public function useCoupon($uid, $coupon_id, $order_id)
    {
        $coupon = new NsCouponModel();
        $data = array(
            'use_order_id' => $order_id,
            'state' => 2
        );
        $res = $coupon->save($data, [
            'coupon_id' => $coupon_id
        ]);
        return $res;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\ICoupon::getCouponDetail()
     */
    public function getCouponDetail($coupon_id)
    {
        // TODO Auto-generated method stub
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IMemberAccountFlow::getPointConfig()
     */
    public function getPointConfig()
    {
        $point_model = new NsPointConfigModel();
        $count = $point_model->where([
            'shop_id' => $this->instance_id
        ])->count();
        if ($count > 0) {
            $info = $point_model->get([
                'shop_id' => $this->instance_id
            ]);
        } else {
            $data = array(
                'shop_id' => $this->instance_id,
                'is_open' => 0,
                'desc' => '',
                'create_time' => time()
            );
            $point_model = new NsPointConfigModel();
            $res = $point_model->save($data);
            $info = $point_model->get([
                'shop_id' => $this->instance_id
            ]);
        }
        
        return $info;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IMemberAccountFlow::setPointConfig()
     */
    public function setPointConfig($convert_rate, $is_open, $desc)
    {
        $point_model = new NsPointConfigModel();
        $data = array(
            'convert_rate' => $convert_rate,
            'is_open' => $is_open,
            'desc' => $desc,
            'modify_time' => time()
        );
        $retval = $point_model->save($data, [
            'shop_id' => $this->instance_id
        ]);
        return $retval;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromote::getPromotionGiftList()
     */
    public function getPromotionGiftList($page_index = 1, $page_size = 0, $condition = '', $order = 'create_time desc')
    {
        $promotion_gift = new NsPromotionGiftModel();
        $list = $promotion_gift->pageQuery($page_index, $page_size, $condition, $order, '*');
        if (! empty($list['data'])) {
            foreach ($list['data'] as $k => $v) {
                $start_time = $v['start_time'];
                $end_time = $v['end_time'];
                if ($end_time < time()) {
                    $list['data'][$k]['type'] = 2;
                    $list['data'][$k]['type_name'] = '已结束';
                } elseif ($start_time > time()) {
                    $list['data'][$k]['type'] = 0;
                    $list['data'][$k]['type_name'] = '未开始';
                } elseif ($start_time <= time() && time() <= $end_time) {
                    $list['data'][$k]['type'] = 1;
                    $list['data'][$k]['type_name'] = '进行中';
                }
            }
        }
        return $list;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromote::addPromotionGift()
     */
    public function addPromotionGift($shop_id, $gift_name, $start_time, $end_time, $days, $max_num, $goods_id_array)
    {
        $promotion_gift = new NsPromotionGiftModel();
        $promotion_gift->startTrans();
        try {
            $data_gift = array(
                'gift_name' => $gift_name,
                'shop_id' => $shop_id,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'days' => $days,
                'max_num' => $max_num,
                'create_time' => time()
            );
            $promotion_gift->save($data_gift);
            $gift_id = $promotion_gift->gift_id;
            // 当前功能只能选择一种商品
            $promotion_gift_goods = new NsPromotionGiftGoodsModel();
            // 查询商品名称图片
            $goods = new NsGoodsModel();
            $goods_info = $goods->getInfo([
                'goods_id' => $goods_id_array
            ], 'goods_name,picture');
            $data_goods = array(
                'gift_id' => $gift_id,
                'goods_id' => $goods_id_array,
                'goods_name' => $goods_info['goods_name'],
                'goods_picture' => $goods_info['picture']
            );
            $promotion_gift_goods->save($data_goods);
            $promotion_gift->commit();
            return $gift_id;
        } catch (\Exception $e) {
            $promotion_gift->rollback();
            return $e->getMessage();
        }
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IPromote::updatePromotionGift()
     */
    public function updatePromotionGift($gift_id, $shop_id, $gift_name, $start_time, $end_time, $days, $max_num, $goods_id_array)
    {
        $promotion_gift = new NsPromotionGiftModel();
        $promotion_gift->startTrans();
        try {
            $data_gift = array(
                'gift_name' => $gift_name,
                'shop_id' => $shop_id,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'days' => $days,
                'max_num' => $max_num,
                'modify_time' => time()
            );
            $promotion_gift->save($data_gift, [
                'gift_id' => $gift_id
            ]);
            // 当前功能只能选择一种商品
            $promotion_gift_goods = new NsPromotionGiftGoodsModel();
            $promotion_gift_goods->destroy([
                'gift_id' => $gift_id
            ]);
            // 查询商品名称图片
            $goods = new NsGoodsModel();
            $goods_info = $goods->getInfo([
                'goods_id' => $goods_id_array
            ], 'goods_name,picture');
            $data_goods = array(
                'gift_id' => $gift_id,
                'goods_id' => $goods_id_array,
                'goods_name' => $goods_info['goods_name'],
                'goods_picture' => $goods_info['picture']
            );
            $promotion_gift_goods = new NsPromotionGiftGoodsModel();
            $promotion_gift_goods->save($data_goods);
            $promotion_gift->commit();
            return 1;
        } catch (\Exception $e) {
            $promotion_gift->rollback();
            return $e->getMessage();
        }
        
        // TODO Auto-generated method stub
    }

    /**
     * 获取 赠品详情
     *
     * @param unknown $gift_id            
     */
    public function getPromotionGiftDetail($gift_id)
    {
        $promotion_gift = new NsPromotionGiftModel();
        $data = $promotion_gift->get($gift_id);
        $promotion_gift_goods = new NsPromotionGiftGoodsModel();
        $gift_goods = $promotion_gift_goods->get([
            'gift_id' => $gift_id
        ]);
        $picture = new AlbumPictureModel();
        $pic_info = array();
        $pic_info['pic_cover'] = '';
        if (! empty($gift_goods['goods_picture'])) {
            $pic_info = $picture->get($gift_goods['goods_picture']);
        }
        $gift_goods['picture'] = $pic_info;
        $data['gift_goods'] = $gift_goods;
        return $data;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IPromote::getPromotionMansongList()
     */
    public function getPromotionMansongList($page_index = 1, $page_size = 0, $condition = '', $order = 'create_time desc')
    {
        $promotion_mansong = new NsPromotionMansongModel();
        $list = $promotion_mansong->pageQuery($page_index, $page_size, $condition, $order, '*');
        if (! empty($list['data'])) {
            foreach ($list['data'] as $k => $v) {
                if ($v['status'] == 0) {
                    $list['data'][$k]['status_name'] = '未开始';
                }
                if ($v['status'] == 1) {
                    $list['data'][$k]['status_name'] = '进行中';
                }
                if ($v['status'] == 2) {
                    $list['data'][$k]['status_name'] = '已取消';
                }
                if ($v['status'] == 3) {
                    $list['data'][$k]['status_name'] = '已失效';
                }
                if ($v['status'] == 4) {
                    $list['data'][$k]['status_name'] = '已结束';
                }
            }
        }
        return $list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IPromote::addPromotionMansong()
     */
    public function addPromotionMansong($mansong_name, $start_time, $end_time, $shop_id, $remark, $type, $range_type, $rule, $goods_id_array)
    {
        $promot_mansong = new NsPromotionMansongModel();
        $goods_mansong = new GoodsMansong();
        $promot_mansong->startTrans();
        try {
            $err = 0;
            $count_quan = $goods_mansong->getQuanmansong($start_time, $end_time);
            if ($count_quan > 0 && $range_type == 1) {
                $err = 1;
            }
            $shop_name = $this->instance_name;
            $data = array(
                'mansong_name' => $mansong_name,
                'start_time' => getTimeTurnTimeStamp($start_time),
                'end_time' => getTimeTurnTimeStamp($end_time),
                'shop_id' => $shop_id,
                'shop_name' => $shop_name,
                'status' => 0, // 状态重新设置
                'remark' => $remark,
                'type' => $type,
                'range_type' => $range_type,
                'create_time' => time()
            );
            $promot_mansong->save($data);
            $mansong_id = $promot_mansong->mansong_id;
            // 添加活动规则表
            $rule_array = explode(';', $rule);
            foreach ($rule_array as $k => $v) {
                $get_rule = explode(',', $v);
                $data_rule = array(
                    'mansong_id' => $mansong_id,
                    'price' => $get_rule[0],
                    'discount' => $get_rule[1],
                    'free_shipping' => $get_rule[2],
                    'give_point' => $get_rule[3],
                    'give_coupon' => $get_rule[4],
                    'gift_id' => $get_rule[5]
                );
                $promot_mansong_rule = new NsPromotionMansongRuleModel();
                $promot_mansong_rule->save($data_rule);
            }
            
            // 满减送商品表
            if ($range_type == 0 && ! empty($goods_id_array)) {
                // 部分商品
                $goods_id_array = explode(',', $goods_id_array);
                foreach ($goods_id_array as $k => $v) {
                    $promotion_mansong_goods = new NsPromotionMansongGoodsModel();
                    // 查询商品名称图片
                    $goods = new NsGoodsModel();
                    $goods_info = $goods->getInfo([
                        'goods_id' => $v
                    ], 'goods_name,picture');
                    $data_goods = array(
                        'mansong_id' => $mansong_id,
                        'goods_id' => $v,
                        'goods_name' => $goods_info['goods_name'],
                        'goods_picture' => $goods_info['picture'],
                        'status' => 0, // 状态重新设置
                        'start_time' => getTimeTurnTimeStamp($start_time),
                        'end_time' => getTimeTurnTimeStamp($end_time)
                    );
                    $count = $goods_mansong->getGoodsIsMansong($v, $start_time, $end_time);
                    if ($count > 0) {
                        $err = 1;
                    }
                    $promotion_mansong_goods->save($data_goods);
                }
            }
            if ($err > 0) {
                $promot_mansong->rollback();
                return ACTIVE_REPRET;
            } else {
                $promot_mansong->commit();
                return $mansong_id;
            }
        } catch (\Exception $e) {
            $promot_mansong->rollback();
            return $e->getMessage();
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IPromote::updatePromotionMansong()
     */
    public function updatePromotionMansong($mansong_id, $mansong_name, $start_time, $end_time, $shop_id, $remark, $type, $range_type, $rule, $goods_id_array)
    {
        $promot_mansong = new NsPromotionMansongModel();
        $promot_mansong->startTrans();
        try {
            $err = 0;
            $shop_name = $this->instance_name;
            $data = array(
                'mansong_name' => $mansong_name,
                'start_time' => getTimeTurnTimeStamp($start_time),
                'end_time' => getTimeTurnTimeStamp($end_time),
                'shop_id' => $this->instance_id,
                'shop_name' => $shop_name,
                'status' => 0, // 状态重新设置
                'remark' => $remark,
                'type' => $type,
                'range_type' => $range_type,
                'create_time' => time()
            );
            $promot_mansong->save($data, [
                'mansong_id' => $mansong_id
            ]);
            // 添加活动规则表
            $promot_mansong_rule = new NsPromotionMansongRuleModel();
            $promot_mansong_rule->destroy([
                'mansong_id' => $mansong_id
            ]);
            $rule_array = explode(';', $rule);
            foreach ($rule_array as $k => $v) {
                $promot_mansong_rule = new NsPromotionMansongRuleModel();
                $get_rule = explode(',', $v);
                $data_rule = array(
                    'mansong_id' => $mansong_id,
                    'price' => $get_rule[0],
                    'discount' => $get_rule[1],
                    'free_shipping' => $get_rule[2],
                    'give_point' => $get_rule[3],
                    'give_coupon' => $get_rule[4],
                    'gift_id' => $get_rule[5]
                );
                $promot_mansong_rule->save($data_rule);
            }
            
            // 满减送商品表
            if ($range_type == 0 && ! empty($goods_id_array)) {
                // 部分商品
                $goods_id_array = explode(',', $goods_id_array);
                $promotion_mansong_goods = new NsPromotionMansongGoodsModel();
                $promotion_mansong_goods->destroy([
                    'mansong_id' => $mansong_id
                ]);
                foreach ($goods_id_array as $k => $v) {
                    // 查询商品名称图片
                    $goods_mansong = new GoodsMansong();
                    $count = $goods_mansong->getGoodsIsMansong($v, $start_time, $end_time);
                    if ($count > 0) {
                        $err = 1;
                    }
                    $promotion_mansong_goods = new NsPromotionMansongGoodsModel();
                    $goods = new NsGoodsModel();
                    $goods_info = $goods->getInfo([
                        'goods_id' => $v
                    ], 'goods_name,picture');
                    $data_goods = array(
                        'mansong_id' => $mansong_id,
                        'goods_id' => $v,
                        'goods_name' => $goods_info['goods_name'],
                        'goods_picture' => $goods_info['picture'],
                        'status' => 0, // 状态重新设置
                        'start_time' => getTimeTurnTimeStamp($start_time),
                        'end_time' => getTimeTurnTimeStamp($end_time)
                    );
                    $promotion_mansong_goods->save($data_goods);
                }
            }
            if ($err > 0) {
                $promot_mansong->rollback();
                return ACTIVE_REPRET;
            } else {
                
                $promot_mansong->commit();
                return 1;
            }
        } catch (\Exception $e) {
            $promot_mansong->rollback();
            return $e->getMessage();
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IPromote::getPromotionMansongDetail()
     */
    public function getPromotionMansongDetail($mansong_id)
    {
        $promotion_mansong = new NsPromotionMansongModel();
        $data = $promotion_mansong->get($mansong_id);
        $promot_mansong_rule = new NsPromotionMansongRuleModel();
        $rule_list = $promot_mansong_rule->pageQuery(1, 0, 'mansong_id = ' . $mansong_id, '', '*');
        foreach ($rule_list['data'] as $k => $v) {
            if ($v['free_shipping'] == 1) {
                $rule_list['data'][$k]['free_shipping_name'] = "是";
            } else {
                $rule_list['data'][$k]['free_shipping_name'] = "否";
            }
            if ($v['give_coupon'] == 0) {
                $rule_list['data'][$k]['coupon_name'] = '';
            } else {
                $coupon_type = new NsCouponTypeModel();
                $coupon_name = $coupon_type->getInfo([
                    'coupon_type_id' => $v['give_coupon']
                ], 'coupon_name');
                $rule_list['data'][$k]['coupon_name'] = $coupon_name['coupon_name'];
            }
            if ($v['gift_id'] == 0) {
                $rule_list['data'][$k]['gift_name'] = '';
            } else {
                $gift = new NsPromotionGiftModel();
                $gift_name = $gift->getInfo([
                    'gift_id' => $v['gift_id']
                ], 'gift_name');
                $rule_list['data'][$k]['gift_name'] = $gift_name['gift_name'];
            }
        }
        $data['rule'] = $rule_list['data'];
        if ($data['range_type'] == 0) {
            $mansong_goods = new NsPromotionMansongGoodsModel();
            $list = $mansong_goods->getQuery([
                'mansong_id' => $mansong_id
            ], '*', '');
            if (! empty($list)) {
                foreach ($list as $k => $v) {
                    $goods = new NsGoodsModel();
                    $goods_info = $goods->getInfo([
                        'goods_id' => $v['goods_id']
                    ], 'price, stock');
                    $picture = new AlbumPictureModel();
                    $pic_info = array();
                    $pic_info['pic_cover'] = '';
                    if (! empty($v['goods_picture'])) {
                        $pic_info = $picture->get($v['goods_picture']);
                    }
                    $v['picture_info'] = $pic_info;
                    $v['price'] = $goods_info['price'];
                    $v['stock'] = $goods_info['stock'];
                }
            }
            $data['goods_list'] = $list;
            $goods_id_array = array();
            foreach ($list as $k => $v) {
                $goods_id_array[] = $v['goods_id'];
            }
            $data['goods_id_array'] = $goods_id_array;
        }
        return $data;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IPromote::addPromotiondiscount()
     */
    public function addPromotiondiscount($discount_name, $start_time, $end_time, $remark, $goods_id_array)
    {
        $promotion_discount = new NsPromotionDiscountModel();
        $promotion_discount->startTrans();
        try {
            
            $shop_name = $this->instance_name;
            $data = array(
                'discount_name' => $discount_name,
                'start_time' => getTimeTurnTimeStamp($start_time),
                'end_time' => getTimeTurnTimeStamp($end_time),
                'shop_id' => $this->instance_id,
                'shop_name' => $shop_name,
                'status' => 0,
                'remark' => $remark,
                'create_time' => time()
            );
            $promotion_discount->save($data);
            $discount_id = $promotion_discount->discount_id;
            $goods_id_array = explode(',', $goods_id_array);
            $promotion_discount_goods = new NsPromotionDiscountGoodsModel();
            $promotion_discount_goods->destroy([
                'discount_id' => $discount_id
            ]);
            foreach ($goods_id_array as $k => $v) {
                // 添加检测考虑商品在一个时间段内只能有一种活动
                
                $promotion_discount_goods = new NsPromotionDiscountGoodsModel();
                $discount_info = explode(':', $v);
                $goods_discount = new GoodsDiscount();
                $count = $goods_discount->getGoodsIsDiscount($discount_info[0], $start_time, $end_time);
                // 查询商品名称图片
                if ($count > 0) {
                    $promotion_discount->rollback();
                    return ACTIVE_REPRET;
                }
                $goods = new NsGoodsModel();
                $goods_info = $goods->getInfo([
                    'goods_id' => $discount_info[0]
                ], 'goods_name,picture');
                $data_goods = array(
                    'discount_id' => $discount_id,
                    'goods_id' => $discount_info[0],
                    'discount' => $discount_info[1],
                    'status' => 0,
                    'start_time' => getTimeTurnTimeStamp($start_time),
                    'end_time' => getTimeTurnTimeStamp($end_time),
                    'goods_name' => $goods_info['goods_name'],
                    'goods_picture' => $goods_info['picture']
                );
                $promotion_discount_goods->save($data_goods);
            }
            $promotion_discount->commit();
            return $discount_id;
        } catch (\Exception $e) {
            $promotion_discount->rollback();
            return $e->getMessage();
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IPromote::updatePromotionDiscount()
     */
    public function updatePromotionDiscount($discount_id, $discount_name, $start_time, $end_time, $remark, $goods_id_array)
    {
        $promotion_discount = new NsPromotionDiscountModel();
        $promotion_discount->startTrans();
        try {
            
            $shop_name = $this->instance_name;
            $data = array(
                'discount_name' => $discount_name,
                'start_time' => getTimeTurnTimeStamp($start_time),
                'end_time' => getTimeTurnTimeStamp($end_time),
                'shop_id' => $this->instance_id,
                'shop_name' => $shop_name,
                'status' => 0,
                'remark' => $remark,
                'create_time' => time()
            );
            $promotion_discount->save($data, [
                'discount_id' => $discount_id
            ]);
            $goods_id_array = explode(',', $goods_id_array);
            $promotion_discount_goods = new NsPromotionDiscountGoodsModel();
            $promotion_discount_goods->destroy([
                'discount_id' => $discount_id
            ]);
            foreach ($goods_id_array as $k => $v) {
                $promotion_discount_goods = new NsPromotionDiscountGoodsModel();
                $discount_info = explode(':', $v);
                $goods_discount = new GoodsDiscount();
                $count = $goods_discount->getGoodsIsDiscount($discount_info[0], $start_time, $end_time);
                // 查询商品名称图片
                if ($count > 0) {
                    $promotion_discount->rollback();
                    return ACTIVE_REPRET;
                }
                // 查询商品名称图片
                $goods = new NsGoodsModel();
                $goods_info = $goods->getInfo([
                    'goods_id' => $discount_info[0]
                ], 'goods_name,picture');
                $data_goods = array(
                    'discount_id' => $discount_id,
                    'goods_id' => $discount_info[0],
                    'discount' => $discount_info[1],
                    'status' => 0,
                    'start_time' => getTimeTurnTimeStamp($start_time),
                    'end_time' => getTimeTurnTimeStamp($end_time),
                    'goods_name' => $goods_info['goods_name'],
                    'goods_picture' => $goods_info['picture']
                );
                $promotion_discount_goods->save($data_goods);
            }
            $promotion_discount->commit();
            return $discount_id;
        } catch (\Exception $e) {
            $promotion_discount->rollback();
            return $e->getMessage();
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IPromote::closePromotionDiscount()
     */
    public function closePromotionDiscount($discount_id)
    {
        $promotion_discount = new NsPromotionDiscountModel();
        $promotion_discount->startTrans();
        try {
            $retval = $promotion_discount->save([
                'status' => 3
            ], [
                'discount_id' => $discount_id
            ]);
            if ($retval == 1) {
                $goods = new NsGoodsModel();
                
                $data_goods = array(
                    'promotion_type' => 2,
                    'promote_id' => $discount_id
                );
                $goods_id_list = $goods->getQuery($data_goods, 'goods_id', '');
                if (! empty($goods_id_list)) {
                    
                    foreach ($goods_id_list as $k => $goods_id) {
                        $goods_info = $goods->getInfo([
                            'goods_id' => $goods_id['goods_id']
                        ], 'promotion_type,price');
                        $goods->save([
                            'promotion_price' => $goods_info['price']
                        ], [
                            'goods_id' => $goods_id['goods_id']
                        ]);
                        $goods_sku = new NsGoodsSkuModel();
                        $goods_sku_list = $goods_sku->getQuery([
                            'goods_id' => $goods_id['goods_id']
                        ], 'price,sku_id', '');
                        foreach ($goods_sku_list as $k_sku => $sku) {
                            $goods_sku = new NsGoodsSkuModel();
                            $data_goods_sku = array(
                                'promote_price' => $sku['price']
                            );
                            $goods_sku->save($data_goods_sku, [
                                'sku_id' => $sku['sku_id']
                            ]);
                        }
                    }
                }
                $goods->save([
                    'promotion_type' => 0,
                    'promote_id' => 0
                ], $data_goods);
                $promotion_discount_goods = new NsPromotionDiscountGoodsModel();
                $retval = $promotion_discount_goods->save([
                    'status' => 3
                ], [
                    'discount_id' => $discount_id
                ]);
            }
            $promotion_discount->commit();
            return $retval;
        } catch (\Exception $e) {
            $promotion_discount->rollback();
            return $e->getMessage();
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IPromote::getPromotionDiscountList()
     */
    public function getPromotionDiscountList($page_index = 1, $page_size = 0, $condition = '', $order = 'create_time desc')
    {
        $promotion_discount = new NsPromotionDiscountModel();
        $list = $promotion_discount->pageQuery($page_index, $page_size, $condition, $order, '*');
        return $list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IPromote::getPromotionDiscountDetail()
     */
    public function getPromotionDiscountDetail($discount_id)
    {
        $promotion_discount = new NsPromotionDiscountModel();
        $promotion_detail = $promotion_discount->get($discount_id);
        $promotion_discount_goods = new NsPromotionDiscountGoodsModel();
        $promotion_goods_list = $promotion_discount_goods->getQuery([
            'discount_id' => $discount_id
        ], '*', '');
        if (! empty($promotion_goods_list)) {
            foreach ($promotion_goods_list as $k => $v) {
                $goods = new NsGoodsModel();
                $goods_info = $goods->getInfo([
                    'goods_id' => $v['goods_id']
                ], 'price, stock');
                $picture = new AlbumPictureModel();
                $pic_info = array();
                $pic_info['pic_cover'] = '';
                if (! empty($v['goods_picture'])) {
                    $pic_info = $picture->get($v['goods_picture']);
                }
                $v['picture_info'] = $pic_info;
                $v['price'] = $goods_info['price'];
                $v['stock'] = $goods_info['stock'];
            }
        }
        $promotion_detail['goods_list'] = $promotion_goods_list;
        return $promotion_detail;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IPromote::delPromotionDiscount()
     */
    public function delPromotionDiscount($discount_id)
    {
        $promotion_discount = new NsPromotionDiscountModel();
        $promotion_discount_goods = new NsPromotionDiscountGoodsModel();
        $promotion_discount->startTrans();
        try {
            $discount_id_array = explode(',', $discount_id);
            foreach ($discount_id_array as $k => $v) {
                $promotion_detail = $promotion_discount->get($discount_id);
                if ($promotion_detail['status'] == 1) {
                    $promotion_discount->rollback();
                    return - 1;
                }
                $promotion_discount->destroy($v);
                $promotion_discount_goods->destroy([
                    'discount_id' => $v
                ]);
            }
            $promotion_discount->commit();
            return 1;
        } catch (\Exception $e) {
            $promotion_discount->rollback();
            return $e->getMessage();
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IPromote::closePromotionDiscount()
     */
    public function closePromotionMansong($mansong_id)
    {
        $promotion_mansong = new NsPromotionMansongModel();
        $retval = $promotion_mansong->save([
            'status' => 3
        ], [
            'mansong_id' => $mansong_id,
            'shop_id' => $this->instance_id
        ]);
        if ($retval == 1) {
            $promotion_mansong_goods = new NsPromotionMansongGoodsModel();
            
            $retval = $promotion_mansong_goods->save([
                'status' => 3
            ], [
                'mansong_id' => $mansong_id
            ]);
        }
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IPromote::closePromotionDiscount()
     */
    public function delPromotionMansong($mansong_id)
    {
        $promotion_mansong = new NsPromotionMansongModel();
        $promotion_mansong_goods = new NsPromotionMansongGoodsModel();
        $promot_mansong_rule = new NsPromotionMansongRuleModel();
        $promotion_mansong->startTrans();
        try {
            $mansong_id_array = explode(',', $mansong_id);
            foreach ($mansong_id_array as $k => $v) {
                $status = $promotion_mansong->getInfo([
                    'mansong_id' => $v
                ], 'status');
                if ($status['status'] == 1) {
                    $promotion_mansong->rollback();
                    return - 1;
                }
                $promotion_mansong->destroy($v);
                $promotion_mansong_goods->destroy([
                    'mansong_id' => $v
                ]);
                $promot_mansong_rule->destroy([
                    'mansong_id' => $v
                ]);
            }
            $promotion_mansong->commit();
            return 1;
        } catch (Exception $e) {
            $promotion_mansong->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 得到店铺的满额包邮信息
     * (non-PHPdoc)
     *
     * @see \data\api\IPromote::getPromotionFullMail()
     */
    public function getPromotionFullMail($shop_id)
    {
        $promotion_fullmail = new NsPromotionFullMailModel();
        $mail_count = $promotion_fullmail->getCount([
            "shop_id" => $shop_id
        ]);
        if ($mail_count == 0) {
            $data = array(
                'shop_id' => $shop_id,
                'is_open' => 0,
                'full_mail_money' => 0,
                'no_mail_province_id_array' => '',
                'no_mail_city_id_array' => '',
                'create_time' => time()
            );
            $promotion_fullmail->save($data);
        }
        $mail_obj = $promotion_fullmail->getInfo([
            "shop_id" => $shop_id
        ]);
        return $mail_obj;
    }

    /**
     * 更新或添加满额包邮的信息
     * (non-PHPdoc)
     *
     * @see \data\api\IPromote::updatePromotionFullMail()
     */
    public function updatePromotionFullMail($shop_id, $is_open, $full_mail_money, $no_mail_province_id_array, $no_mail_city_id_array)
    {
        $full_mail_model = new NsPromotionFullMailModel();
        $data = array(
            'is_open' => $is_open,
            'full_mail_money' => $full_mail_money,
            'modify_time' => time(),
            'no_mail_province_id_array' => $no_mail_province_id_array,
            'no_mail_city_id_array' => $no_mail_city_id_array
        );
        $full_mail_model->save($data, [
            "shop_id" => $shop_id
        ]);
        return 1;
    }
}