<?php
/**
 * OrderAccount.php
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
namespace data\service\Order;

use data\model\AlbumPictureModel;
use data\model\ConfigModel;
use data\model\NsGoodsModel;
use data\model\NsGoodsSkuModel;
use data\model\NsOrderActionModel as NsOrderActionModel;
use data\model\NsOrderExpressCompanyModel;
use data\model\NsOrderGoodsExpressModel;
use data\model\NsOrderGoodsModel;
use data\model\NsOrderGoodsPromotionDetailsModel;
use data\model\NsOrderModel;
use data\model\NsOrderPickupModel;
use data\model\NsOrderPromotionDetailsModel;
use data\model\NsOrderRefundAccountRecordsModel;
use data\model\NsPickupPointModel;
use data\model\NsPromotionFullMailModel;
use data\model\NsPromotionMansongRuleModel;
use data\model\UserModel as UserModel;
use data\service\Address;
use data\service\BaseService;
use data\service\Config;
use data\service\Member\MemberAccount;
use data\service\Member\MemberCoupon;
use data\service\Order\OrderStatus;
use data\service\promotion\GoodsExpress;
use data\service\promotion\GoodsMansong;
use data\service\promotion\GoodsPreference;
use data\service\UnifyPay;
use data\service\WebSite;
use think\Log;
use data\service\VirtualGoods;

/**
 * 订单操作类
 */
class Order extends BaseService
{

    public $order;
    // 订单主表
    function __construct()
    {
        parent::__construct();
        $this->order = new NsOrderModel();
    }

    /**
     * 订单创建
     * （订单传入积分系统默认为使用积分兑换商品）
     *
     * @param unknown $order_type            
     * @param unknown $out_trade_no            
     * @param unknown $pay_type            
     * @param unknown $shipping_type            
     * @param unknown $order_from            
     * @param unknown $buyer_ip            
     * @param unknown $buyer_message            
     * @param unknown $buyer_invoice            
     * @param unknown $shipping_time            
     * @param unknown $receiver_mobile            
     * @param unknown $receiver_province            
     * @param unknown $receiver_city            
     * @param unknown $receiver_district            
     * @param unknown $receiver_address            
     * @param unknown $receiver_zip            
     * @param unknown $receiver_name            
     * @param unknown $point            
     * @param unknown $point_money            
     * @param unknown $coupon_money            
     * @param unknown $coupon_id            
     * @param unknown $user_money            
     * @param unknown $promotion_money            
     * @param unknown $shipping_money            
     * @param unknown $pay_money            
     * @param unknown $give_point            
     * @param unknown $goods_sku_list            
     * @return number|Exception
     */
    public function orderCreate($order_type, $out_trade_no, $pay_type, $shipping_type, $order_from, $buyer_ip, $buyer_message, $buyer_invoice, $shipping_time, $receiver_mobile, $receiver_province, $receiver_city, $receiver_district, $receiver_address, $receiver_zip, $receiver_name, $point, $coupon_id, $user_money, $goods_sku_list, $platform_money, $pick_up_id, $shipping_company_id, $coin, $fixed_telephone = "")
    {
        $this->order->startTrans();
        
        try {
            // 设定不使用会员余额支付
            $user_money = 0;
            // 查询商品对应的店铺ID
            $order_goods_preference = new GoodsPreference();
            $shop_id = $order_goods_preference->getGoodsSkuListShop($goods_sku_list);
            // 单店版查询网站内容
            $web_site = new WebSite();
            $web_info = $web_site->getWebSiteInfo();
            $shop_name = $web_info['title'];
            // 获取优惠券金额
            $coupon = new MemberCoupon();
            $coupon_money = $coupon->getCouponMoney($coupon_id);
            
            // 获取购买人信息
            $buyer = new UserModel();
            $buyer_info = $buyer->getInfo([
                'uid' => $this->uid
            ], 'nick_name');
            // 订单商品费用
            
            $goods_money = $order_goods_preference->getGoodsSkuListPrice($goods_sku_list);
            $point = $order_goods_preference->getGoodsListExchangePoint($goods_sku_list);
            // 获取订单邮费,订单自提免除运费
            if ($shipping_type == 1) {
                $order_goods_express = new GoodsExpress();
                $deliver_price = $order_goods_express->getSkuListExpressFee($goods_sku_list, $shipping_company_id, $receiver_province, $receiver_city, $receiver_district);
                if ($deliver_price < 0) {
                    $this->order->rollback();
                    return $deliver_price;
                }
            } else {
                // 根据自提点服务费用计算
                $deliver_price = $order_goods_preference->getPickupMoney($goods_money);
            }
            
            // 积分兑换抵用金额
            $account_flow = new MemberAccount();
            /*
             * $point_money = $order_goods_preference->getPointMoney($point, $shop_id);
             */
            $point_money = 0;
            /*
             * if($point > 0)
             * {
             * //积分兑换抵用商品金额+邮费
             * $point_money = $goods_money;
             * //订单为已支付
             * if($deliver_price == 0)
             * {
             * $order_status = 1;
             * }else
             * {
             * $order_status = 0;
             * }
             *
             * //赠送积分为0
             * $give_point = 0;
             * //不享受满减送优惠
             * $promotion_money = 0;
             *
             * }else{
             */
            // 订单来源
            if (isWeixin()) {
                $order_from = 1; // 微信
            } elseif (request()->isMobile()) {
                $order_from = 2; // 手机
            } else {
                $order_from = 3; // 电脑
            }
            // 订单支付方式
            
            // 订单待支付
            $order_status = 0;
            // 购买商品获取积分数
            $give_point = $order_goods_preference->getGoodsSkuListGivePoint($goods_sku_list);
            // 订单满减送活动优惠
            $goods_mansong = new GoodsMansong();
            $mansong_array = $goods_mansong->getGoodsSkuListMansong($goods_sku_list);
            $promotion_money = 0;
            $mansong_rule_array = array();
            $mansong_discount_array = array();
            if (! empty($mansong_array)) {
                foreach ($mansong_array as $k_mansong => $v_mansong) {
                    foreach ($v_mansong['discount_detail'] as $k_rule => $v_rule) {
                        $rule = $v_rule[1];
                        $discount_money_detail = explode(':', $rule);
                        $mansong_discount_array[] = array(
                            $discount_money_detail[0],
                            $discount_money_detail[1],
                            $v_rule[0]['rule_id']
                        );
                        $promotion_money += $discount_money_detail[1]; // round($discount_money_detail[1],2);
                                                                       // 添加优惠活动信息
                        $mansong_rule_array[] = $v_rule[0];
                    }
                }
                $promotion_money = round($promotion_money, 2);
            }
            $full_mail_array = array();
            // 计算订单的满额包邮
            $full_mail_model = new NsPromotionFullMailModel();
            // 店铺的满额包邮
            $full_mail_obj = $full_mail_model->getInfo([
                "shop_id" => $shop_id
            ], "*");
            $no_mail = checkIdIsinIdArr($receiver_city, $full_mail_obj['no_mail_city_id_array']);
            if ($no_mail) {
                $full_mail_obj['is_open'] = 0;
            }
            if (! empty($full_mail_obj)) {
                $is_open = $full_mail_obj["is_open"];
                $full_mail_money = $full_mail_obj["full_mail_money"];
                $order_real_money = $goods_money - $promotion_money - $coupon_money - $point_money;
                if ($is_open == 1 && $order_real_money >= $full_mail_money && $deliver_price > 0) {
                    // 符合满额包邮 邮费设置为0
                    $full_mail_array["promotion_id"] = $full_mail_obj["mail_id"];
                    $full_mail_array["promotion_type"] = 'MANEBAOYOU';
                    $full_mail_array["promotion_name"] = '满额包邮';
                    $full_mail_array["promotion_condition"] = '满' . $full_mail_money . '元,包邮!';
                    $full_mail_array["discount_money"] = $deliver_price;
                    $deliver_price = 0;
                }
            }
            
            // 订单费用(具体计算)
            $order_money = $goods_money + $deliver_price - $promotion_money - $coupon_money - $point_money;
            
            if ($order_money < 0) {
                $order_money = 0;
                $user_money = 0;
                $platform_money = 0;
            }
            
            if (! empty($buyer_invoice)) {
                // 添加税费
                $config = new Config();
                $tax_value = $config->getConfig(0, 'ORDER_INVOICE_TAX');
                if (empty($tax_value['value'])) {
                    $tax = 0;
                } else {
                    $tax = $tax_value['value'];
                }
                $tax_money = $order_money * $tax / 100;
            } else {
                $tax_money = 0;
            }
            $order_money = $order_money + $tax_money;
            
            if ($order_money < $platform_money) {
                $platform_money = $order_money;
            }
            
            $pay_money = $order_money - $user_money - $platform_money;
            if ($pay_money <= 0) {
                $pay_money = 0;
                $order_status = 0;
                $pay_status = 0;
            } else {
                $order_status = 0;
                $pay_status = 0;
            }
            
            // 积分返还类型
            $config = new ConfigModel();
            $config_info = $config->getInfo([
                "instance_id" => $shop_id,
                "key" => "SHOPPING_BACK_POINTS"
            ], "value");
            $give_point_type = $config_info["value"];
            
            // 店铺名称
            
            $data_order = array(
                'order_type' => $order_type,
                'order_no' => $this->createOrderNo($shop_id),
                'out_trade_no' => $out_trade_no,
                'payment_type' => $pay_type,
                'shipping_type' => $shipping_type,
                'order_from' => $order_from,
                'buyer_id' => $this->uid,
                'user_name' => $buyer_info['nick_name'],
                'buyer_ip' => $buyer_ip,
                'buyer_message' => $buyer_message,
                'buyer_invoice' => $buyer_invoice,
                'shipping_time' => getTimeTurnTimeStamp($shipping_time), // datetime NOT NULL COMMENT '买家要求配送时间',
                'receiver_mobile' => $receiver_mobile, // varchar(11) NOT NULL DEFAULT '' COMMENT '收货人的手机号码',
                'receiver_province' => $receiver_province, // int(11) NOT NULL COMMENT '收货人所在省',
                'receiver_city' => $receiver_city, // int(11) NOT NULL COMMENT '收货人所在城市',
                'receiver_district' => $receiver_district, // int(11) NOT NULL COMMENT '收货人所在街道',
                'receiver_address' => $receiver_address, // varchar(255) NOT NULL DEFAULT '' COMMENT '收货人详细地址',
                'receiver_zip' => $receiver_zip, // varchar(6) NOT NULL DEFAULT '' COMMENT '收货人邮编',
                'receiver_name' => $receiver_name, // varchar(50) NOT NULL DEFAULT '' COMMENT '收货人姓名',
                'shop_id' => $shop_id, // int(11) NOT NULL COMMENT '卖家店铺id',
                'shop_name' => $shop_name, // varchar(100) NOT NULL DEFAULT '' COMMENT '卖家店铺名称',
                'goods_money' => $goods_money, // decimal(19, 2) NOT NULL COMMENT '商品总价',
                'tax_money' => $tax_money, // 税费
                'order_money' => $order_money, // decimal(10, 2) NOT NULL COMMENT '订单总价',
                'point' => $point, // int(11) NOT NULL COMMENT '订单消耗积分',
                'point_money' => $point_money, // decimal(10, 2) NOT NULL COMMENT '订单消耗积分抵多少钱',
                'coupon_money' => $coupon_money, // _money decimal(10, 2) NOT NULL COMMENT '订单代金券支付金额',
                'coupon_id' => $coupon_id, // int(11) NOT NULL COMMENT '订单代金券id',
                'user_money' => $user_money, // decimal(10, 2) NOT NULL COMMENT '订单预存款支付金额',
                'promotion_money' => $promotion_money, // decimal(10, 2) NOT NULL COMMENT '订单优惠活动金额',
                'shipping_money' => $deliver_price, // decimal(10, 2) NOT NULL COMMENT '订单运费',
                'pay_money' => $pay_money, // decimal(10, 2) NOT NULL COMMENT '订单实付金额',
                'refund_money' => 0, // decimal(10, 2) NOT NULL COMMENT '订单退款金额',
                'give_point' => $give_point, // int(11) NOT NULL COMMENT '订单赠送积分',
                'order_status' => $order_status, // tinyint(4) NOT NULL COMMENT '订单状态',
                'pay_status' => $pay_status, // tinyint(4) NOT NULL COMMENT '订单付款状态',
                'shipping_status' => 0, // tinyint(4) NOT NULL COMMENT '订单配送状态',
                'review_status' => 0, // tinyint(4) NOT NULL COMMENT '订单评价状态',
                'feedback_status' => 0, // tinyint(4) NOT NULL COMMENT '订单维权状态',
                'user_platform_money' => $platform_money, // 平台余额支付
                'coin_money' => $coin,
                'create_time' => time(),
                "give_point_type" => $give_point_type,
                'shipping_company_id' => $shipping_company_id,
                'fixed_telephone' => $fixed_telephone //固定电话
            ); // datetime NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '订单创建时间',
            if ($pay_status == 2) {
                $data_order["pay_time"] = time();
            }
            $order = new NsOrderModel();
            $order->save($data_order);
            $order_id = $order->order_id;
            $pay = new UnifyPay();
            $pay->createPayment($shop_id, $out_trade_no, $shop_name . "订单", $shop_name . "订单", $pay_money, 1, $order_id);
            // 如果是订单自提需要添加自提相关信息
            if ($shipping_type == 2) {
                if (! empty($pick_up_id)) {
                    $pickup_model = new NsPickupPointModel();
                    $pickup_point_info = $pickup_model->getInfo([
                        'id' => $pick_up_id
                    ], '*');
                    $order_pick_up_model = new NsOrderPickupModel();
                    $data_pickup = array(
                        'order_id' => $order_id,
                        'name' => $pickup_point_info['name'],
                        'address' => $pickup_point_info['address'],
                        'contact' => $pickup_point_info['address'],
                        'phone' => $pickup_point_info['phone'],
                        'city_id' => $pickup_point_info['city_id'],
                        'province_id' => $pickup_point_info['province_id'],
                        'district_id' => $pickup_point_info['district_id'],
                        'supplier_id' => $pickup_point_info['supplier_id'],
                        'longitude' => $pickup_point_info['longitude'],
                        'latitude' => $pickup_point_info['latitude'],
                        'create_time' => time()
                    );
                    $order_pick_up_model->save($data_pickup);
                }
            }
            // 满额包邮活动
            if (! empty($full_mail_array)) {
                $order_promotion_details = new NsOrderPromotionDetailsModel();
                $data_promotion_details = array(
                    'order_id' => $order_id,
                    'promotion_id' => $full_mail_array["promotion_id"],
                    'promotion_type_id' => 2,
                    'promotion_type' => $full_mail_array["promotion_type"],
                    'promotion_name' => $full_mail_array["promotion_name"],
                    'promotion_condition' => $full_mail_array["promotion_condition"],
                    'discount_money' => $full_mail_array["discount_money"],
                    'used_time' => time()
                );
                $order_promotion_details->save($data_promotion_details);
            }
            // 满减送详情，添加满减送活动优惠情况
            if (! empty($mansong_rule_array)) {
                
                $mansong_rule_array = array_unique($mansong_rule_array);
                foreach ($mansong_rule_array as $k_mansong_rule => $v_mansong_rule) {
                    $order_promotion_details = new NsOrderPromotionDetailsModel();
                    $data_promotion_details = array(
                        'order_id' => $order_id,
                        'promotion_id' => $v_mansong_rule['rule_id'],
                        'promotion_type_id' => 1,
                        'promotion_type' => 'MANJIAN',
                        'promotion_name' => '满减送活动',
                        'promotion_condition' => '满' . $v_mansong_rule['price'] . '元，减' . $v_mansong_rule['discount'],
                        'discount_money' => $v_mansong_rule['discount'],
                        'used_time' => time()
                    );
                    $order_promotion_details->save($data_promotion_details);
                }
                // 添加到对应商品项优惠满减
                if (! empty($mansong_discount_array)) {
                    foreach ($mansong_discount_array as $k => $v) {
                        $order_goods_promotion_details = new NsOrderGoodsPromotionDetailsModel();
                        $data_details = array(
                            'order_id' => $order_id,
                            'promotion_id' => $v[2],
                            'sku_id' => $v[0],
                            'promotion_type' => 'MANJIAN',
                            'discount_money' => $v[1],
                            'used_time' => time()
                        );
                        $order_goods_promotion_details->save($data_details);
                    }
                }
            }
            // 添加到对应商品项优惠优惠券使用详情
            if ($coupon_id > 0) {
                $coupon_details_array = $order_goods_preference->getGoodsCouponPromoteDetail($coupon_id, $coupon_money, $goods_sku_list);
                foreach ($coupon_details_array as $k => $v) {
                    $order_goods_promotion_details = new NsOrderGoodsPromotionDetailsModel();
                    $data_details = array(
                        'order_id' => $order_id,
                        'promotion_id' => $coupon_id,
                        'sku_id' => $v['sku_id'],
                        'promotion_type' => 'COUPON',
                        'discount_money' => $v['money'],
                        'used_time' => time()
                    );
                    $order_goods_promotion_details->save($data_details);
                }
            }
            // 使用积分
            if ($point > 0) {
                $retval_point = $account_flow->addMemberAccountData($shop_id, 1, $this->uid, 0, $point * (- 1), 1, $order_id, '商城订单');
                if ($retval_point < 0) {
                    $this->order->rollback();
                    return ORDER_CREATE_LOW_POINT;
                }
            }
            if ($coin > 0) {
                $retval_point = $account_flow->addMemberAccountData($shop_id, 3, $this->uid, 0, $coin * (- 1), 1, $order_id, '商城订单');
                if ($retval_point < 0) {
                    $this->order->rollback();
                    return LOW_COIN;
                }
            }
            if ($user_money > 0) {
                $retval_user_money = $account_flow->addMemberAccountData($shop_id, 2, $this->uid, 0, $user_money * (- 1), 1, $order_id, '商城订单');
                if ($retval_user_money < 0) {
                    $this->order->rollback();
                    return ORDER_CREATE_LOW_USER_MONEY;
                }
            }
            if ($platform_money > 0) {
                $retval_platform_money = $account_flow->addMemberAccountData(0, 2, $this->uid, 0, $platform_money * (- 1), 1, $order_id, '商城订单');
                if ($retval_platform_money < 0) {
                    $this->order->rollback();
                    return ORDER_CREATE_LOW_PLATFORM_MONEY;
                }
            }
            // 使用优惠券
            if ($coupon_id > 0) {
                $retval = $coupon->useCoupon($this->uid, $coupon_id, $order_id);
                if (! ($retval > 0)) {
                    $this->order->rollback();
                    return $retval;
                }
            }
            // 添加订单项
            $order_goods = new OrderGoods();
            $res_order_goods = $order_goods->addOrderGoods($order_id, $goods_sku_list);
            if (! ($res_order_goods > 0)) {
                $this->order->rollback();
                return $res_order_goods;
            }
            $this->addOrderAction($order_id, $this->uid, '创建订单');
            
            $this->order->commit();
            return $order_id;
        } catch (\Exception $e) {
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 订单创建（虚拟商品）
     */
    public function orderCreateVirtual($order_type, $out_trade_no, $pay_type, $shipping_type, $order_from, $buyer_ip, $buyer_message, $buyer_invoice, $shipping_time, $point, $coupon_id, $user_money, $goods_sku_list, $platform_money, $pick_up_id, $shipping_company_id, $user_telephone, $coin)
    {
        $this->order->startTrans();
        
        try {
            // 设定不使用会员余额支付
            $user_money = 0;
            // 查询商品对应的店铺ID
            $order_goods_preference = new GoodsPreference();
            $shop_id = $order_goods_preference->getGoodsSkuListShop($goods_sku_list);
            // 单店版查询网站内容
            $web_site = new WebSite();
            $web_info = $web_site->getWebSiteInfo();
            $shop_name = $web_info['title'];
            // 获取优惠券金额
            $coupon = new MemberCoupon();
            $coupon_money = $coupon->getCouponMoney($coupon_id);
            
            // 获取购买人信息
            $buyer = new UserModel();
            $buyer_info = $buyer->getInfo([
                'uid' => $this->uid
            ], 'nick_name');
            // 订单商品费用
            
            $goods_money = $order_goods_preference->getGoodsSkuListPrice($goods_sku_list);
            $point = $order_goods_preference->getGoodsListExchangePoint($goods_sku_list);
            
            // 积分兑换抵用金额
            $account_flow = new MemberAccount();
            $point_money = 0;
            // 订单来源
            if (isWeixin()) {
                $order_from = 1; // 微信
            } elseif (request()->isMobile()) {
                $order_from = 2; // 手机
            } else {
                $order_from = 3; // 电脑
            }
            // 订单待支付
            $order_status = 0;
            // 购买商品获取积分数
            $give_point = $order_goods_preference->getGoodsSkuListGivePoint($goods_sku_list);
            // 订单满减送活动优惠
            $goods_mansong = new GoodsMansong();
            $mansong_array = $goods_mansong->getGoodsSkuListMansong($goods_sku_list);
            $promotion_money = 0;
            $mansong_rule_array = array();
            $mansong_discount_array = array();
            if (! empty($mansong_array)) {
                foreach ($mansong_array as $k_mansong => $v_mansong) {
                    foreach ($v_mansong['discount_detail'] as $k_rule => $v_rule) {
                        $rule = $v_rule[1];
                        $discount_money_detail = explode(':', $rule);
                        $mansong_discount_array[] = array(
                            $discount_money_detail[0],
                            $discount_money_detail[1],
                            $v_rule[0]['rule_id']
                        );
                        $promotion_money += $discount_money_detail[1]; // round($discount_money_detail[1],2);
                        $mansong_rule_array[] = $v_rule[0];
                    }
                }
                $promotion_money = round($promotion_money, 2);
            }
            
            // 订单费用(具体计算)
            $order_money = $goods_money - $promotion_money - $coupon_money - $point_money;
            
            if ($order_money < 0) {
                $order_money = 0;
                $user_money = 0;
                $platform_money = 0;
            }
            
            if (! empty($buyer_invoice)) {
                // 添加税费
                $config = new Config();
                $tax_value = $config->getConfig(0, 'ORDER_INVOICE_TAX');
                if (empty($tax_value['value'])) {
                    $tax = 0;
                } else {
                    $tax = $tax_value['value'];
                }
                $tax_money = $order_money * $tax / 100;
            } else {
                $tax_money = 0;
            }
            $order_money = $order_money + $tax_money;
            
            if ($order_money < $platform_money) {
                $platform_money = $order_money;
            }
            
            $pay_money = $order_money - $user_money - $platform_money;
            if ($pay_money <= 0) {
                $pay_money = 0;
                $order_status = 0;
                $pay_status = 0;
            } else {
                $order_status = 0;
                $pay_status = 0;
            }
            
            // 积分返还类型
            $config = new ConfigModel();
            $config_info = $config->getInfo([
                "instance_id" => $shop_id,
                "key" => "SHOPPING_BACK_POINTS"
            ], "value");
            $give_point_type = $config_info["value"];
            
            $data_order = array(
                'order_type' => $order_type,
                'order_no' => $this->createOrderNo($shop_id),
                'out_trade_no' => $out_trade_no,
                'payment_type' => $pay_type,
                'shipping_type' => $shipping_type,
                'order_from' => $order_from,
                'buyer_id' => $this->uid,
                'user_name' => $buyer_info['nick_name'],
                'buyer_ip' => $buyer_ip,
                'buyer_message' => $buyer_message,
                'buyer_invoice' => $buyer_invoice,
                'shipping_time' => getTimeTurnTimeStamp($shipping_time), // datetime NOT NULL COMMENT '买家要求配送时间',
                'receiver_mobile' => $user_telephone, // varchar(11) NOT NULL DEFAULT '' COMMENT '收货人的手机号码',
                'receiver_province' => '', // int(11) NOT NULL COMMENT '收货人所在省',
                'receiver_city' => '', // int(11) NOT NULL COMMENT '收货人所在城市',
                'receiver_district' => '', // int(11) NOT NULL COMMENT '收货人所在街道',
                'receiver_address' => '', // varchar(255) NOT NULL DEFAULT '' COMMENT '收货人详细地址',
                'receiver_zip' => '', // varchar(6) NOT NULL DEFAULT '' COMMENT '收货人邮编',
                'receiver_name' => '', // varchar(50) NOT NULL DEFAULT '' COMMENT '收货人姓名',
                'shop_id' => $shop_id, // int(11) NOT NULL COMMENT '卖家店铺id',
                'shop_name' => $shop_name, // varchar(100) NOT NULL DEFAULT '' COMMENT '卖家店铺名称',
                'goods_money' => $goods_money, // decimal(19, 2) NOT NULL COMMENT '商品总价',
                'tax_money' => $tax_money, // 税费
                'order_money' => $order_money, // decimal(10, 2) NOT NULL COMMENT '订单总价',
                'point' => $point, // int(11) NOT NULL COMMENT '订单消耗积分',
                'point_money' => $point_money, // decimal(10, 2) NOT NULL COMMENT '订单消耗积分抵多少钱',
                'coupon_money' => $coupon_money, // _money decimal(10, 2) NOT NULL COMMENT '订单代金券支付金额',
                'coupon_id' => $coupon_id, // int(11) NOT NULL COMMENT '订单代金券id',
                'user_money' => $user_money, // decimal(10, 2) NOT NULL COMMENT '订单预存款支付金额',
                'promotion_money' => $promotion_money, // decimal(10, 2) NOT NULL COMMENT '订单优惠活动金额',
                'shipping_money' => 0, // decimal(10, 2) NOT NULL COMMENT '订单运费',
                'pay_money' => $pay_money, // decimal(10, 2) NOT NULL COMMENT '订单实付金额',
                'refund_money' => 0, // decimal(10, 2) NOT NULL COMMENT '订单退款金额',
                'give_point' => $give_point, // int(11) NOT NULL COMMENT '订单赠送积分',
                'order_status' => $order_status, // tinyint(4) NOT NULL COMMENT '订单状态',
                'pay_status' => $pay_status, // tinyint(4) NOT NULL COMMENT '订单付款状态',
                'shipping_status' => 0, // tinyint(4) NOT NULL COMMENT '订单配送状态',
                'review_status' => 0, // tinyint(4) NOT NULL COMMENT '订单评价状态',
                'feedback_status' => 0, // tinyint(4) NOT NULL COMMENT '订单维权状态',
                'user_platform_money' => $platform_money, // 平台余额支付
                'coin_money' => $coin,
                'create_time' => time(),
                "give_point_type" => $give_point_type,
                'shipping_company_id' => $shipping_company_id,
                'fixed_telephone' => "" //固定电话
            ); // datetime NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '订单创建时间',
            if ($pay_status == 2) {
                $data_order["pay_time"] = time();
            }
            $order = new NsOrderModel();
            $order->save($data_order);
            $order_id = $order->order_id;
            $pay = new UnifyPay();
            $pay->createPayment($shop_id, $out_trade_no, $shop_name . "虚拟订单", $shop_name . "虚拟订单", $pay_money, 1, $order_id);
            // 满减送详情，添加满减送活动优惠情况
            if (! empty($mansong_rule_array)) {
                
                $mansong_rule_array = array_unique($mansong_rule_array);
                foreach ($mansong_rule_array as $k_mansong_rule => $v_mansong_rule) {
                    $order_promotion_details = new NsOrderPromotionDetailsModel();
                    $data_promotion_details = array(
                        'order_id' => $order_id,
                        'promotion_id' => $v_mansong_rule['rule_id'],
                        'promotion_type_id' => 1,
                        'promotion_type' => 'MANJIAN',
                        'promotion_name' => '满减送活动',
                        'promotion_condition' => '满' . $v_mansong_rule['price'] . '元，减' . $v_mansong_rule['discount'],
                        'discount_money' => $v_mansong_rule['discount'],
                        'used_time' => time()
                    );
                    $order_promotion_details->save($data_promotion_details);
                }
                // 添加到对应商品项优惠满减
                if (! empty($mansong_discount_array)) {
                    foreach ($mansong_discount_array as $k => $v) {
                        $order_goods_promotion_details = new NsOrderGoodsPromotionDetailsModel();
                        $data_details = array(
                            'order_id' => $order_id,
                            'promotion_id' => $v[2],
                            'sku_id' => $v[0],
                            'promotion_type' => 'MANJIAN',
                            'discount_money' => $v[1],
                            'used_time' => time()
                        );
                        $order_goods_promotion_details->save($data_details);
                    }
                }
            }
            // 添加到对应商品项优惠优惠券使用详情
            if ($coupon_id > 0) {
                $coupon_details_array = $order_goods_preference->getGoodsCouponPromoteDetail($coupon_id, $coupon_money, $goods_sku_list);
                foreach ($coupon_details_array as $k => $v) {
                    $order_goods_promotion_details = new NsOrderGoodsPromotionDetailsModel();
                    $data_details = array(
                        'order_id' => $order_id,
                        'promotion_id' => $coupon_id,
                        'sku_id' => $v['sku_id'],
                        'promotion_type' => 'COUPON',
                        'discount_money' => $v['money'],
                        'used_time' => time()
                    );
                    $order_goods_promotion_details->save($data_details);
                }
            }
            // 使用积分
            if ($point > 0) {
                $retval_point = $account_flow->addMemberAccountData($shop_id, 1, $this->uid, 0, $point * (- 1), 1, $order_id, '商城虚拟订单');
                if ($retval_point < 0) {
                    $this->order->rollback();
                    return ORDER_CREATE_LOW_POINT;
                }
            }
            if ($coin > 0) {
                $retval_point = $account_flow->addMemberAccountData($shop_id, 3, $this->uid, 0, $coin * (- 1), 1, $order_id, '商城虚拟订单');
                if ($retval_point < 0) {
                    $this->order->rollback();
                    return LOW_COIN;
                }
            }
            if ($user_money > 0) {
                $retval_user_money = $account_flow->addMemberAccountData($shop_id, 2, $this->uid, 0, $user_money * (- 1), 1, $order_id, '商城虚拟订单');
                if ($retval_user_money < 0) {
                    $this->order->rollback();
                    return ORDER_CREATE_LOW_USER_MONEY;
                }
            }
            if ($platform_money > 0) {
                $retval_platform_money = $account_flow->addMemberAccountData(0, 2, $this->uid, 0, $platform_money * (- 1), 1, $order_id, '商城虚拟订单');
                if ($retval_platform_money < 0) {
                    $this->order->rollback();
                    return ORDER_CREATE_LOW_PLATFORM_MONEY;
                }
            }
            // 使用优惠券
            if ($coupon_id > 0) {
                $retval = $coupon->useCoupon($this->uid, $coupon_id, $order_id);
                if (! ($retval > 0)) {
                    $this->order->rollback();
                    return $retval;
                }
            }
            // 添加订单项
            $order_goods = new OrderGoods();
            $res_order_goods = $order_goods->addOrderGoods($order_id, $goods_sku_list);
            if (! ($res_order_goods > 0)) {
                $this->order->rollback();
                return $res_order_goods;
            }
            $this->addOrderAction($order_id, $this->uid, '创建虚拟订单');
            
            $this->order->commit();
            return $order_id;
        } catch (\Exception $e) {
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 订单支付
     *
     * @param unknown $order_pay_no            
     * @param unknown $pay_type(10:线下支付)            
     * @param unknown $status
     *            0:订单支付完成 1：订单交易完成
     * @return Exception
     */
    public function OrderPay($order_pay_no, $pay_type, $status)
    {
        $this->order->startTrans();
        try {
            // 改变订单状态
            $this->order->where([
                'out_trade_no' => $order_pay_no
            ])->select();
            
            // 添加订单日志
            // 可能是多个订单
            $order_id_array = $this->order->where([
                'out_trade_no' => $order_pay_no
            ])->column('order_id');
            
            $account = new MemberAccount();
            foreach ($order_id_array as $k => $order_id) {
                // 赠送赠品
                $order_info = $this->order->getInfo([
                    'order_id' => $order_id
                ], 'buyer_id,pay_money,order_type,order_no');
                if ($pay_type == 10) {
                    // 线下支付
                    $this->addOrderAction($order_id, $this->uid, '线下支付');
                } else {
                    // 查询订单购买人ID
                    
                    $this->addOrderAction($order_id, $order_info['buyer_id'], '订单支付');
                }
                // 增加会员累计消费
                $account->addMmemberConsum(0, $order_info['buyer_id'], $order_info['pay_money']);
                // 修改订单状态
                $data = array(
                    'payment_type' => $pay_type,
                    'pay_status' => 2,
                    'pay_time' => time(),
                    'order_status' => 1
                ); // 订单转为待发货状态
                
                $order = new NsOrderModel();
                $order->save($data, [
                    'order_id' => $order_id
                ]);
                if ($order_info['order_type'] == 2) {
                    // 虚拟商品，订单自动完成
                    $this->virtualOrderOperation($order_id, user_name, $order_info['order_no']);
                    $res = $this->orderComplete($order_id);
                    if (! ($res > 0)) {
                        $this->order->rollback();
                        return $res;
                    }
                } else {
                    
                    if ($status == 1) {
                        // 执行订单交易完成
                        $res = $this->orderComplete($order_id);
                        if (! ($res > 0)) {
                            $this->order->rollback();
                            return $res;
                        }
                    }
                }
            }
            $this->order->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order->rollback();
            Log::write("订单支付出错" . $e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * 虚拟订单，生成虚拟商品
     * 1、根据订单id查询订单项(虚拟订单项只会有一条数据)
     * 2、根据购买的商品获取虚拟商品类型信息
     * 3、根据购买的商品数量添加相应的虚拟商品数量
     */
    public function virtualOrderOperation($order_id, $buyer_nickname, $order_no)
    {
        $order_goods_model = new NsOrderGoodsModel();
        // 查询订单项信息
        $order_goods_items = $order_goods_model->getInfo([
            'order_id' => $order_id
        ], 'order_goods_id,goods_id,goods_name,buyer_id,num');
        $res = 0;
        if (! empty($order_goods_items)) {
            $virtual_goods = new VirtualGoods();
            $goods_model = new NsGoodsModel();
            // 根据goods_id查询虚拟商品类型
            $virtual_goods_type_id = $goods_model->getInfo([
                'goods_id' => $order_goods_items['goods_id']
            ], 'virtual_goods_type_id');
            if (! empty($virtual_goods_type_id)) {
                
                // 生成虚拟商品
                for ($i = 0; $i < $order_goods_items['num']; $i ++) {
                    $virtual_goods_type_info = $virtual_goods->getVirtualGoodsTypeById($virtual_goods_type_id['virtual_goods_type_id']);
                    $virtual_goods_name = $virtual_goods_type_info['virtual_goods_type_name']; // 虚拟商品名称
                    $money = $virtual_goods_type_info['money']; // 虚拟商品金额
                    $buyer_id = $order_goods_items['buyer_id']; // 买家id
                    $order_goods_id = $order_goods_items['order_goods_id']; // 关联订单项id
                    $validity_period = $virtual_goods_type_info['validity_period']; // 有效期/天(0表示不限制)
                    $start_time = time();
                    $end_time = strtotime("+$validity_period days");
                    $use_number = 0; // 使用次数，刚添加的默认0
                    $confine_use_number = $virtual_goods_type_info['confine_use_number'];
                    $use_status = 0; // (-1:已失效,0:未使用,1:已使用)
                    $res = $virtual_goods->addVirtualGoods($this->instance_id, $virtual_goods_name, $money, $buyer_id, $buyer_nickname, $order_goods_id, $order_no, $validity_period, $start_time, $end_time, $use_number, $confine_use_number, $use_status);
                }
            }
        }
        return $res;
    }

    /**
     * 添加订单操作日志
     * order_id int(11) NOT NULL COMMENT '订单id',
     * action varchar(255) NOT NULL DEFAULT '' COMMENT '动作内容',
     * uid int(11) NOT NULL DEFAULT 0 COMMENT '操作人id',
     * user_name varchar(50) NOT NULL DEFAULT '' COMMENT '操作人',
     * order_status int(11) NOT NULL COMMENT '订单大状态',
     * order_status_text varchar(255) NOT NULL DEFAULT '' COMMENT '订单状态名称',
     * action_time datetime NOT NULL COMMENT '操作时间',
     * PRIMARY KEY (action_id)
     *
     * @param unknown $order_id            
     * @param unknown $uid            
     * @param unknown $action_text            
     */
    public function addOrderAction($order_id, $uid, $action_text)
    {
        $this->order->startTrans();
        try {
            $order_status = $this->order->getInfo([
                'order_id' => $order_id
            ], 'order_status');
            if ($uid != 0) {
                $user = new UserModel();
                $user_name = $user->getInfo([
                    'uid' => $uid
                ], 'nick_name');
                $action_name = $user_name['nick_name'];
            } else {
                $action_name = 'system';
            }
            
            $data_log = array(
                'order_id' => $order_id,
                'action' => $action_text,
                'uid' => $uid,
                'user_name' => $action_name,
                'order_status' => $order_status['order_status'],
                'order_status_text' => $this->getOrderStatusName($order_id),
                'action_time' => time()
            );
            $order_action = new NsOrderActionModel();
            $order_action->save($data_log);
            $this->order->commit();
            return $order_action->action_id;
        } catch (\Exception $e) {
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 获取订单当前状态 名称
     *
     * @param unknown $order_id            
     */
    public function getOrderStatusName($order_id)
    {
        $order_status = $this->order->getInfo([
            'order_id' => $order_id
        ], 'order_status');
        $status_array = OrderStatus::getOrderCommonStatus();
        foreach ($status_array as $k => $v) {
            if ($v['status_id'] == $order_status['order_status']) {
                return $v['status_name'];
            }
        }
        return false;
    }

    /**
     * 通过店铺id 得到订单的订单号
     *
     * @param unknown $shop_id            
     */
    public function createOrderNo($shop_id)
    {
        $time_str = date('YmdHs');
        $order_model = new NsOrderModel();
        $order_obj = $order_model->getFirstData([
            "shop_id" => $shop_id
        ], "order_id DESC");
        $num = 0;
        if (! empty($order_obj)) {
            $order_no_max = $order_obj["order_no"];
            if (empty($order_no_max)) {
                $num = 1;
            } else {
                if (substr($time_str, 0, 12) == substr($order_no_max, 0, 12)) {
                    $max_no = substr($order_no_max, 12, 4);
                    $num = $max_no * 1 + 1;
                } else {
                    $num = 1;
                }
            }
        } else {
            $num = 1;
        }
        $order_no = $time_str . sprintf("%04d", $num);
        $count = $order_model->getCount(['order_no'=>$order_no]);
        if($count>0){
            return $this->createOrderNo($shop_id);
        }
        return $order_no;
    }

    /**
     * 创建订单支付编号
     *
     * @param unknown $order_id            
     */
    public function createOutTradeNo()
    {
        $pay_no = new UnifyPay();
        return $pay_no->createOutTradeNo();
    }

    /**
     * 订单重新生成订单号
     *
     * @param unknown $orderid            
     */
    public function createNewOutTradeNo($orderid)
    {
        $order = new NsOrderModel();
        $new_no = $this->createOutTradeNo();
        $data = array(
            'out_trade_no' => $new_no
        );
        $retval = $order->save($data, [
            'order_id' => $orderid
        ]);
        if ($retval) {
            return $new_no;
        } else {
            return '';
        }
    }

    /**
     * 订单发货(整体发货)(不考虑订单项)
     *
     * @param unknown $orderid            
     */
    public function orderDoDelivery($orderid)
    {
        $this->order->startTrans();
        try {
            $order_item = new NsOrderGoodsModel();
            $count = $order_item->getCount([
                'order_id' => $orderid,
                'shipping_status' => 0,
                'refund_status' => array(
                    'ELT',
                    0
                )
            ]);
            if ($count == 0) {
                $data_delivery = array(
                    'shipping_status' => 1,
                    'order_status' => 2,
                    'consign_time' => time()
                );
                $order_model = new NsOrderModel();
                $order_model->save($data_delivery, [
                    'order_id' => $orderid
                ]);
                $this->addOrderAction($orderid, $this->uid, '订单发货');
            }
            
            $this->order->commit();
            return 1;
        } catch (\Exception $e) {
            
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 订单收货
     *
     * @param unknown $orderid            
     */
    public function OrderTakeDelivery($orderid)
    {
        $this->order->startTrans();
        try {
            $data_take_delivery = array(
                'shipping_status' => 2,
                'order_status' => 3,
                'sign_time' => time()
            );
            $order_model = new NsOrderModel();
            $order_model->save($data_take_delivery, [
                'order_id' => $orderid
            ]);
            $this->addOrderAction($orderid, $this->uid, '订单收货');
            // 判断是否需要在本阶段赠送积分
            $this->giveGoodsOrderPoint($orderid, 2);
            $this->order->commit();
            return 1;
        } catch (\Exception $e) {
            
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 订单自动收货
     *
     * @param unknown $orderid            
     */
    public function orderAutoDelivery($orderid)
    {
        $this->order->startTrans();
        try {
            $data_take_delivery = array(
                'shipping_status' => 2,
                'order_status' => 3,
                'sign_time' => time()
            );
            $order_model = new NsOrderModel();
            $order_model->save($data_take_delivery, [
                'order_id' => $orderid
            ]);
            
            $this->addOrderAction($orderid, 0, '订单自动收货');
            // 判断是否需要在本阶段赠送积分
            $this->giveGoodsOrderPoint($orderid, 2);
            $this->order->commit();
            return 1;
        } catch (\Exception $e) {
            
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 执行订单交易完成
     *
     * @param unknown $orderid            
     */
    public function orderComplete($orderid)
    {
        $this->order->startTrans();
        try {
            $data_complete = array(
                'order_status' => 4,
                "finish_time" => time()
            );
            $order_model = new NsOrderModel();
            $order_model->save($data_complete, [
                'order_id' => $orderid
            ]);
            $this->addOrderAction($orderid, $this->uid, '交易完成');
            $this->calculateOrderGivePoint($orderid);
            $this->calculateOrderMansong($orderid);
            // 判断是否需要在本阶段赠送积分
            $this->giveGoodsOrderPoint($orderid, 1);
            $this->order->commit();
            return 1;
        } catch (\Exception $e) {
            
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 统计订单完成后赠送用户积分
     *
     * @param unknown $order_id            
     */
    private function calculateOrderGivePoint($order_id)
    {
        $point = $this->order->getInfo([
            'order_id' => $order_id
        ], 'shop_id, give_point,buyer_id');
        $member_account = new MemberAccount();
        $member_account->addMemberAccountData($point['shop_id'], 1, $point['buyer_id'], 1, $point['give_point'], 1, $order_id, '订单商品赠送积分');
    }

    /**
     * 订单完成后统计满减送赠送
     *
     * @param unknown $order_id            
     */
    private function calculateOrderMansong($order_id)
    {
        $order_info = $this->order->getInfo([
            'order_id' => $order_id
        ], 'shop_id, buyer_id');
        $order_promotion_details = new NsOrderPromotionDetailsModel();
        // 查询满减送活动规则
        $list = $order_promotion_details->getQuery([
            'order_id' => $order_id,
            'promotion_type_id' => 1
        ], 'promotion_id', '');
        if (! empty($list)) {
            $promotion_mansong_rule = new NsPromotionMansongRuleModel();
            foreach ($list as $k => $v) {
                $mansong_data = $promotion_mansong_rule->getInfo([
                    'rule_id' => $v['promotion_id']
                ], 'give_coupon,give_point');
                if (! empty($mansong_data)) {
                    // 满减送赠送积分
                    if ($mansong_data['give_point'] != 0) {
                        $member_account = new MemberAccount();
                        $member_account->addMemberAccountData($order_info['shop_id'], 1, $order_info['buyer_id'], 1, $mansong_data['give_point'], 1, $order_id, '订单满减送赠送积分');
                    }
                    // 满减送赠送优惠券
                    if ($mansong_data['give_coupon'] != 0) {
                        $member_coupon = new MemberCoupon();
                        $member_coupon->UserAchieveCoupon($order_info['buyer_id'], $mansong_data['give_coupon'], 1);
                    }
                }
            }
        }
    }

    /**
     * 订单执行交易关闭
     *
     * @param unknown $orderid            
     * @return Exception
     */
    public function orderClose($orderid)
    {
        $this->order->startTrans();
        try {
            $order_info = $this->order->getInfo([
                'order_id' => $orderid
            ], 'order_status,pay_status,point, coupon_id, user_money, buyer_id,shop_id,user_platform_money, coin_money');
            $data_close = array(
                'order_status' => 5
            );
            $order_model = new NsOrderModel();
            $order_model->save($data_close, [
                'order_id' => $orderid
            ]);
            $account_flow = new MemberAccount();
            if ($order_info['order_status'] == 0) {
                // 会员余额返还
                if ($order_info['user_money'] > 0) {
                    $account_flow->addMemberAccountData($order_info['shop_id'], 2, $order_info['buyer_id'], 1, $order_info['user_money'], 2, $orderid, '订单关闭返还用户余额');
                }
                // 平台余额返还
                
                if ($order_info['user_platform_money'] > 0) {
                    $account_flow->addMemberAccountData(0, 2, $order_info['buyer_id'], 1, $order_info['user_platform_money'], 2, $orderid, '商城订单关闭返还平台余额');
                }
            }
            // 积分返还
            
            if ($order_info['point'] > 0) {
                $account_flow->addMemberAccountData($order_info['shop_id'], 1, $order_info['buyer_id'], 1, $order_info['point'], 2, $orderid, '订单关闭返还积分');
            }
            if ($order_info['coin_money'] > 0) {
                $coin_convert_rate = $account_flow->getCoinConvertRate();
                $account_flow->addMemberAccountData($order_info['shop_id'], 3, $order_info['buyer_id'], 1, $order_info['coin_money'] / $coin_convert_rate, 2, $orderid, '订单关闭返还购物币');
            }
            
            // 优惠券返还
            $coupon = new MemberCoupon();
            if ($order_info['coupon_id'] > 0) {
                $coupon->UserReturnCoupon($order_info['coupon_id']);
            }
            // 退回库存
            $order_goods = new NsOrderGoodsModel();
            $order_goods_list = $order_goods->getQuery([
                'order_id' => $orderid
            ], '*', '');
            foreach ($order_goods_list as $k => $v) {
                $return_stock = 0;
                $goods_sku_model = new NsGoodsSkuModel();
                $goods_sku_info = $goods_sku_model->getInfo([
                    'sku_id' => $v['sku_id']
                ], 'goods_id, stock');
                if ($v['shipping_status'] != 1) {
                    // 卖家未发货
                    $return_stock = 1;
                } else {
                    // 卖家已发货,买家不退货
                    if ($v['refund_type'] == 1) {
                        $return_stock = 0;
                    } else {
                        $return_stock = 1;
                    }
                }
                // 退货返回库存
                if ($return_stock == 1) {
                    $data_goods_sku = array(
                        'stock' => $goods_sku_info['stock'] + $v['num']
                    );
                    $goods_sku_model->save($data_goods_sku, [
                        'sku_id' => $v['sku_id']
                    ]);
                    $count = $goods_sku_model->getSum([
                        'goods_id' => $goods_sku_info['goods_id']
                    ], 'stock');
                    // 商品库存增加
                    $goods_model = new NsGoodsModel();
                    $goods_model->save([
                        'stock' => $count
                    ], [
                        "goods_id" => $goods_sku_info['goods_id']
                    ]);
                }
            }
            $this->addOrderAction($orderid, $this->uid, '交易关闭');
            $this->order->commit();
            return 1;
        } catch (\Exception $e) {
            Log::write($e->getMessage());
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 订单状态变更
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id            
     */
    public function orderGoodsRefundFinish($order_id)
    {
        $orderInfo = NsOrderModel::get($order_id);
        $orderInfo->startTrans();
        try {
            $order_goods_model = new NsOrderGoodsModel();
            $total_count = $order_goods_model->where("order_id=$order_id")->count();
            $refunding_count = $order_goods_model->where("order_id=$order_id AND refund_status<>0 AND refund_status<>5 AND refund_status>0")->count();
            $refunded_count = $order_goods_model->where("order_id=$order_id AND refund_status=5")->count();
            $shipping_status = $orderInfo->shipping_status;
            $all_refund = 0;
            if ($refunding_count > 0) {
                
                $orderInfo->order_status = OrderStatus::getOrderCommonStatus()[6]['status_id']; // 退款中
            } elseif ($refunded_count == $total_count) {
                
                $all_refund = 1;
            } elseif ($shipping_status == OrderStatus::getShippingStatus()[0]['shipping_status']) {
                
                $orderInfo->order_status = OrderStatus::getOrderCommonStatus()[1]['status_id']; // 待发货
            } elseif ($shipping_status == OrderStatus::getShippingStatus()[1]['shipping_status']) {
                
                $orderInfo->order_status = OrderStatus::getOrderCommonStatus()[2]['status_id']; // 已发货
            } elseif ($shipping_status == OrderStatus::getShippingStatus()[2]['shipping_status']) {
                
                $orderInfo->order_status = OrderStatus::getOrderCommonStatus()[3]['status_id']; // 已收货
            }
            
            // 订单恢复正常操作
            if ($all_refund == 0) {
                $retval = $orderInfo->save();
                if ($refunding_count == 0) {
                    $this->orderDoDelivery($order_id);
                }
            } else {
                // 全部退款订单转化为交易关闭
                $retval = $this->orderClose($order_id);
            }
            
            $orderInfo->commit();
            return $retval;
        } catch (\Exception $e) {
            $orderInfo->rollback();
            return $e->getMessage();
        }
        
        return $retval;
    }

    /**
     * 获取订单详情
     *
     * @param unknown $order_id            
     */
    public function getDetail($order_id)
    {
        // 查询主表
        $order_detail = $this->order->getInfo([
            "order_id" => $order_id,
            "is_deleted" => 0
        ]);
        if (empty($order_detail)) {
            return array();
        }
        // 发票信息
        $temp_array = array();
        if ($order_detail["buyer_invoice"] != "") {
            $temp_array = explode("$", $order_detail["buyer_invoice"]);
        }
        $order_detail["buyer_invoice_info"] = $temp_array;
        if (empty($order_detail)) {
            return '';
        }
        $order_detail['payment_type_name'] = OrderStatus::getPayType($order_detail['payment_type']);
        $express_company_name = "";
        if ($order_detail['shipping_type'] == 1) {
            $order_detail['shipping_type_name'] = '商家配送';
            $express_company = new NsOrderExpressCompanyModel();
            
            $express_obj = $express_company->getInfo([
                "co_id" => $order_detail["shipping_company_id"]
            ], "company_name");
            if (! empty($express_obj["company_name"])) {
                $express_company_name = $express_obj["company_name"];
            }
        } elseif ($order_detail['shipping_type'] == 2) {
            $order_detail['shipping_type_name'] = '门店自提';
        } else {
            $order_detail['shipping_type_name'] = '';
        }
        $order_detail["shipping_company_name"] = $express_company_name;
        // 查询订单项表
        $order_detail['order_goods'] = $this->getOrderGoods($order_id);
        if ($order_detail['payment_type'] == 6 || $order_detail['shipping_type'] == 2) {
            $order_status = OrderStatus::getSinceOrderStatus();
        } else {
            // 查询操作项
            $order_status = OrderStatus::getOrderCommonStatus();
        }
        // 查询订单提货信息表
        if ($order_detail['shipping_type'] == 2) {
            $order_pickup_model = new NsOrderPickupModel();
            $order_pickup_info = $order_pickup_model->getInfo([
                'order_id' => $order_id
            ], '*');
            $address = new Address();
            $order_pickup_info['province_name'] = $address->getProvinceName($order_pickup_info['province_id']);
            $order_pickup_info['city_name'] = $address->getCityName($order_pickup_info['city_id']);
            $order_pickup_info['dictrict_name'] = $address->getDistrictName($order_pickup_info['district_id']);
            $order_detail['order_pickup'] = $order_pickup_info;
        } else {
            $order_detail['order_pickup'] = '';
        }
        // 查询订单操作
        foreach ($order_status as $k_status => $v_status) {
            
            if ($v_status['status_id'] == $order_detail['order_status']) {
                $order_detail['operation'] = $v_status['operation'];
                $order_detail['status_name'] = $v_status['status_name'];
            }
        }
        // 查询订单操作日志
        $order_action = new NsOrderActionModel();
        $order_action_log = $order_action->getQuery([
            'order_id' => $order_id
        ], '*', 'action_time desc');
        $order_detail['order_action'] = $order_action_log;
        
        $address_service = new Address();
        $order_detail['address'] = $address_service->getAddress($order_detail['receiver_province'], $order_detail['receiver_city'], $order_detail['receiver_district']);
        $order_detail['address'] .= $order_detail["receiver_address"];
        return $order_detail;
    }

    /**
     * 查询订单的订单项列表
     *
     * @param unknown $order_id            
     */
    public function getOrderGoods($order_id)
    {
        $order_goods = new NsOrderGoodsModel();
        $order_goods_list = $order_goods->all([
            'order_id' => $order_id
        ]);
        foreach ($order_goods_list as $k => $v) {
            $order_goods_list[$k]['express_info'] = $this->getOrderGoodsExpress($v['order_goods_id']);
            $shipping_status_array = OrderStatus::getShippingStatus();
            foreach ($shipping_status_array as $k_status => $v_status) {
                if ($v['shipping_status'] == $v_status['shipping_status']) {
                    $order_goods_list[$k]['shipping_status_name'] = $v_status['status_name'];
                }
            }
            // 商品图片
            $picture = new AlbumPictureModel();
            $picture_info = $picture->get($v['goods_picture']);
            $order_goods_list[$k]['picture_info'] = $picture_info;
            if ($v['refund_status'] != 0) {
                $order_refund_status = OrderStatus::getRefundStatus();
                foreach ($order_refund_status as $k_status => $v_status) {
                    
                    if ($v_status['status_id'] == $v['refund_status']) {
                        $order_goods_list[$k]['refund_operation'] = $v_status['refund_operation'];
                        $order_goods_list[$k]['status_name'] = $v_status['status_name'];
                    }
                }
            } else {
                $order_goods_list[$k]['refund_operation'] = '';
                $order_goods_list[$k]['status_name'] = '';
            }
        }
        return $order_goods_list;
    }

    /**
     * 获取订单的物流信息
     *
     * @param unknown $order_id            
     */
    public function getOrderExpress($order_id)
    {
        $order_goods_express = new NsOrderGoodsExpressModel();
        $order_express_list = $order_goods_express->all([
            'order_id' => $order_id
        ]);
        return $order_express_list;
    }

    /**
     * 获取订单项的物流信息
     *
     * @param unknown $order_goods_id            
     * @return multitype:|Ambigous <multitype:\think\static , \think\false, \think\Collection, \think\db\false, PDOStatement, string, \PDOStatement, \think\db\mixed, boolean, unknown, \think\mixed, multitype:, array>
     */
    private function getOrderGoodsExpress($order_goods_id)
    {
        $order_goods = new NsOrderGoodsModel();
        $order_goods_info = $order_goods->getInfo([
            'order_goods_id' => $order_goods_id
        ], 'order_id,shipping_status');
        if ($order_goods_info['shipping_status'] == 0) {
            return array();
        } else {
            $order_express_list = $this->getOrderExpress($order_goods_info['order_id']);
            foreach ($order_express_list as $k => $v) {
                $order_goods_id_array = explode(",", $v['order_goods_id_array']);
                if (in_array($order_goods_id, $order_goods_id_array)) {
                    return $v;
                }
            }
            return array();
        }
    }

    /**
     * 订单价格调整
     *
     * @param unknown $order_id            
     * @param unknown $goods_money
     *            调整后的商品总价
     * @param unknown $shipping_fee
     *            调整后的运费
     */
    public function orderAdjustMoney($order_id, $goods_money, $shipping_fee)
    {
        $this->order->startTrans();
        try {
            $order_model = new NsOrderModel();
            $order_info = $order_model->getInfo([
                'order_id' => $order_id
            ], 'goods_money,shipping_money,order_money,pay_money');
            // 商品金额差额
            $goods_money_adjust = $goods_money - $order_info['goods_money'];
            $shipping_fee_adjust = $shipping_fee - $order_info['shipping_money'];
            $order_money = $order_info['order_money'] + $goods_money_adjust + $shipping_fee_adjust;
            $pay_money = $order_info['pay_money'] + $goods_money_adjust + $shipping_fee_adjust;
            $data = array(
                'goods_money' => $goods_money,
                'order_money' => $order_money,
                'shipping_money' => $shipping_fee,
                'pay_money' => $pay_money
            );
            $retval = $order_model->save($data, [
                'order_id' => $order_id
            ]);
            $this->addOrderAction($order_id, $this->uid, '调整金额');
            $this->order->commit();
            return $retval;
        } catch (\Exception $e) {
            $this->order->rollback();
            return $e;
        }
    }

    /**
     * 获取订单整体商品金额(根据订单项)
     *
     * @param unknown $order_id            
     */
    public function getOrderGoodsMoney($order_id)
    {
        $order_goods = new NsOrderGoodsModel();
        $money = $order_goods->getSum([
            'order_id' => $order_id
        ], 'goods_money');
        if (empty($money)) {
            $money = 0;
        }
        return $money;
    }

    /**
     * 获取订单赠品
     *
     * @param unknown $order_id            
     */
    public function getOrderPromotionGift($order_id)
    {
        $gift_list = array();
        $order_promotion_details = new NsOrderPromotionDetailsModel();
        $promotion_list = $order_promotion_details->getQuery([
            'order_id' => $order_id,
            'promotion_type_id' => 1
        ], 'promotion_id', '');
        if (! empty($promotion_list)) {
            foreach ($promotion_list as $k => $v) {
                $rule = new NsPromotionMansongRuleModel();
                $gift = $rule->getInfo([
                    'rule_id' => $v['promotion_id']
                ], 'gift_id');
                $gift_list[] = $gift['gift_id'];
            }
        }
        return $gift_list;
    }

    /**
     * 获取具体订单项信息
     *
     * @param unknown $order_goods_id
     *            订单项ID
     */
    public function getOrderGoodsInfo($order_goods_id)
    {
        $order_goods = new NsOrderGoodsModel();
        return $order_goods->getInfo([
            'order_goods_id' => $order_goods_id
        ], 'goods_id,goods_name,goods_money,goods_picture,shop_id');
    }

    /**
     * 通过订单id 得到该订单的世纪支付金额
     *
     * @param unknown $order_id            
     */
    public function getOrderRealPayMoney($order_id)
    {
        $order_goods_model = new NsOrderGoodsModel();
        // 查询订单的所有的订单项
        $order_goods_list = $order_goods_model->getQuery([
            "order_id" => $order_id
        ], "goods_money,adjust_moneyrefund_real_money", "");
        $order_real_money = 0;
        if (! empty($order_goods_list)) {
            $order_goods_promotion = new NsOrderGoodsPromotionDetailsModel();
            foreach ($order_goods_list as $k => $order_goods) {
                $promotion_money = $order_goods_promotion->getSum([
                    'order_id' => $order_id,
                    'sku_id' => $order_goods['sku_id']
                ], 'discount_money');
                if (empty($promotion_money)) {
                    $promotion_money = 0;
                }
                // 订单项的真实付款金额
                $order_goods_real_money = $order_goods['goods_money'] + $order_goods['adjust_money'] - $order_goods['refund_real_money'] - $promotion_money;
                // 订单付款金额
                $order_real_money = $order_real_money + $order_goods_real_money;
            }
        }
        return $order_real_money;
    }

    /**
     * 订单提货
     *
     * @param unknown $order_id            
     */
    public function pickupOrder($order_id, $buyer_name, $buyer_phone, $remark)
    {
        // 订单转为已收货状态
        $this->order->startTrans();
        try {
            $data_take_delivery = array(
                'shipping_status' => 2,
                'order_status' => 3,
                'sign_time' => time()
            );
            $order_model = new NsOrderModel();
            $order_model->save($data_take_delivery, [
                'order_id' => $order_id
            ]);
            $this->addOrderAction($order_id, $this->uid, '订单提货' . '提货人：' . $buyer_name . ' ' . $buyer_phone);
            // 记录提货信息
            $order_pickup_model = new NsOrderPickupModel();
            $data_pickup = array(
                'buyer_name' => $buyer_name,
                'buyer_mobile' => $buyer_phone,
                'remark' => $remark
            );
            $order_pickup_model->save($data_pickup, [
                'order_id' => $order_id
            ]);
            $order_goods_model = new NsOrderGoodsModel();
            $order_goods_model->save([
                'shipping_status' => 2
            ], [
                'order_id' => $order_id
            ]);
            $this->giveGoodsOrderPoint($order_id, 2);
            $this->order->commit();
            return 1;
        } catch (\Exception $e) {
            
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 订单发放
     *
     * @param unknown $order_id            
     */
    public function giveGoodsOrderPoint($order_id, $type)
    {
        // 判断是否需要在本阶段赠送积分
        $order_model = new NsOrderModel();
        $order_info = $order_model->getInfo([
            "order_id" => $order_id
        ], "give_point_type,shop_id,buyer_id,give_point");
        if ($order_info["give_point_type"] == $type) {
            if ($order_info["give_point"] > 0) {
                $member_account = new MemberAccount();
                $text = "";
                if ($order_info["give_point_type"] == 1) {
                    $text = "商城订单完成赠送积分";
                } elseif ($order_info["give_point_type"] == 2) {
                    $text = "商城订单完成收货赠送积分";
                } elseif ($order_info["give_point_type"] == 3) {
                    $text = "商城订单完成支付赠送积分";
                }
                $member_account->addMemberAccountData($order_info['shop_id'], 1, $order_info['buyer_id'], 1, $order_info['give_point'], 1, $order_id, $text);
            }
        }
    }

    /**
     * 添加订单退款账号记录
     * 创建时间：2017年10月18日 10:03:37 王永杰
     *
     * {@inheritdoc}
     *
     * @see \data\api\IOrder::addOrderRefundAccountRecords()
     */
    public function addOrderRefundAccountRecords($order_goods_id, $refund_trade_no, $refund_money, $refund_way, $buyer_id, $remark)
    {
        $model = new NsOrderRefundAccountRecordsModel();
        
        $data = array(
            'order_goods_id' => $order_goods_id,
            'refund_trade_no' => $refund_trade_no,
            'refund_money' => $refund_money,
            'refund_way' => $refund_way,
            'buyer_id' => $buyer_id,
            'refund_time' => time(),
            'remark' => $remark
        );
        $res = $model->save($data);
        return $res;
    }
}