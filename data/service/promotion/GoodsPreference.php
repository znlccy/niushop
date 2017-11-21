<?php
/**
 * GoodsPreference.php
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
use data\model\NsGoodsModel;
use data\model\NsGoodsSkuModel;
use data\model\NsCouponModel;
use data\service\BaseService;
use data\service\Goods;
use data\service\Member\MemberCoupon;
use data\model\NsCouponGoodsModel;
use data\model\NsCouponTypeModel;
use data\model\NsPromotionDiscountModel;
use data\model\NsPointConfigModel;
use data\service\Promotion;
use data\service\Member;
use data\model\NsMemberModel;
use data\model\NsMemberLevelModel;
use data\service\Config;
/**
 * 商品优惠价格操作类(运费，商品优惠)(没有考虑订单优惠活动例如满减送)
 *
 */
class GoodsPreference extends BaseService{
    function __construct(){
        parent::__construct();
    }
    /*****************************************************************************************订单商品管理开始***************************************************/
    /**
     * 获取商品sku列表价格
     * @param unknown $goods_sku_list skuid:1,skuid:2,skuid:3
     */
    public function getGoodsSkuListPrice($goods_sku_list)
    {
        $price = 0;
        if(!empty($goods_sku_list))
        {

            $goods_sku_list_array = explode( ",", $goods_sku_list);
            foreach ($goods_sku_list_array as $k => $v)
            {
                $sku_data = explode(":", $v);
                $sku_id = $sku_data[0];
                $sku_count = $sku_data[1];
                $sku_price = $this->getGoodsSkuPrice($sku_id);
                $price = $price + $sku_price * $sku_count;
            }
            return $price;
    
        }else{
            return $price;
        }
    
    }
    /**
     * 获取商品sku列表购买后可得积分
     * @param unknown $goods_sku_list
     */
    public function getGoodsSkuListGivePoint($goods_sku_list)
    {
        $point = 0;
        if(!empty($goods_sku_list))
        {
            $goods_sku_list_array = explode( ",", $goods_sku_list);
            foreach ($goods_sku_list_array as $k => $v)
            {
                $sku_data = explode(":", $v);
                $sku_id = $sku_data[0];
                $sku_count = $sku_data[1];
                $goods = new Goods();
                $goods_id = $goods->getGoodsId($sku_id);
                $give_point = $goods->getGoodsGivePoint($goods_id);
                $point += $give_point*$sku_count;
                
            }
            return $point;
        
        }else{
            return $point;
        }
    }
    
    /**
     * 获取商品sku列表使用优惠券详情
     * @param unknown $coupon_id
     * @param unknown $coupon_money
     * @param unknown $goods_sku_list
     */
    public function getGoodsCouponPromoteDetail($coupon_id, $coupon_money, $goods_sku_list)
    {
        $promote_coupon_detail = array();
        //获取商品总价
        $all_goods_money = $this->getGoodsSkuListPrice($goods_sku_list);
        //获取优惠券详情
        $coupon = new NsCouponModel();
        $coupon_type_id = $coupon->getInfo(['coupon_id' => $coupon_id],'coupon_type_id');
        $promote = new Promotion();
        $coupon_type_detail = $promote->getCouponTypeDetail($coupon_type_id['coupon_type_id']);
        //拆分sku
        $goods_sku_list_array = explode( ",", $goods_sku_list);
        
        if($coupon_type_detail['range_type'] == 1)
        {
            
            //优惠券全场使用
            foreach ($goods_sku_list_array as $k => $v)
            {
                //获取sku总价
                $sku_data = explode(':', $v);
                $goods_money = $this->getGoodsSkuListPrice($v);
                $sku_id = $sku_data[0];
                $promote_item = array(
                    'sku_id' => $sku_id,
                    'money'  => round($coupon_money*$goods_money/$all_goods_money,2)
                );
                $promote_coupon_detail[] = $promote_item;
            
            }
        }else{
            //优惠券部分商品使用
            $coupon_goods_money = 0;
            $goods_list = $coupon_type_detail['goods_list'];
            
            $list = array();//整理后的商品数组
            foreach($goods_list as $k_goods => $v_goods)
            {
              
                foreach ($goods_sku_list_array as $k => $v)
                {
                    
                    $sku_data = explode(':', $v);
                    $sku_id = $sku_data[0];
                    $goods = new Goods(); 
                    $goods_id = $goods->getGoodsId($sku_id);
                    if($goods_id == $v_goods['goods_id'])
                    {
                        $goods_money = $this->getGoodsSkuListPrice($v);
                        $coupon_goods_money+=$goods_money;
                        $list[] = $v;
                    }
                    
                }
              
            }
            if($coupon_goods_money == 0)
            {
                $coupon_goods_money = $all_goods_money;
            }
            foreach ($list as $k => $v)
            {
                $goods_money = $this->getGoodsSkuListPrice($v);
                $sku_data = explode(':', $v);
                $sku_id = $sku_data[0];
                $promote_item = array(
                    'sku_id' => $sku_id,
                    'money'  => round($coupon_money*$goods_money/$coupon_goods_money,2)
                );
                $promote_coupon_detail[] = $promote_item;
                
            }
        }
        return $promote_coupon_detail;
        
    }
    /**
     * 获取商品对应sku的价格
     * @param unknown $sku_id
     */
    public function getGoodsSkuPrice($sku_id)
    {
        $goods_sku = new NsGoodsSkuModel();
        $goods_sku_info = $goods_sku->getInfo(['sku_id' => $sku_id], 'goods_id,price,promote_price');
        if(!empty($this->uid))
        {
            $member_price = $this->getGoodsSkuMemberPrice($sku_id, $this->uid);
            if($member_price < $goods_sku_info['promote_price'])
            {
                return $member_price;
            }else{
                return $goods_sku_info['promote_price'];
            }
        }else{
            return $goods_sku_info['promote_price'];
        }
     
    }
    /**
     * 获取商品sku的积分兑换值
     * @param unknown $sku_id
     * @return Ambigous <number, unknown>
     */
    public function getGoodsSkuExchangePoint($sku_id)
    {
        $goods_sku = new NsGoodsSkuModel();
        $goods_sku_info = $goods_sku->getInfo(['sku_id' => $sku_id], 'goods_id');
        $goods = new Goods();
        $point = $goods->getGoodsPointExchange($goods_sku_info['goods_id']);
        return $point;
    }
    /**
     * 获取商品列表总积分
     * @param unknown $goods_sku_list
     * @return Ambigous <\data\service\promotion\Ambigous, number, unknown>
     */
    public function getGoodsListExchangePoint($goods_sku_list)
    {
        $goods_sku_list_array = explode( ",", $goods_sku_list);
        $point = 0;
        foreach ($goods_sku_list_array as $k => $v)
        {
            //获取sku总价
            $sku_data = explode(':', $v);
            $sku_id = $sku_data[0];
            $sku_point = $this->getGoodsSkuExchangePoint($sku_id);
            $point += $sku_point*$sku_data[1];
          
        }
        return $point;
    }
    /**
     * 获取积分对应金额
     * @param unknown $point
     * @param unknown $shop_id
     */
  /*   public function getPointMoney($point, $shop_id)
    {
        $point_config = new NsPointConfigModel();
        $config = $point_config->getInfo(['shop_id'=> $shop_id], 'is_open, convert_rate');
        if(!empty($config))
        {
            $money = $point*$config['convert_rate'];
        }else{
            $money = 0;
        }
        return $money;
    } */
    /**
     * 获取商品当前单品优惠活动
     * @param unknown $goods_id
     */
    public function getGoodsPromote($goods_id)
    {
        $goods = new NsGoodsModel();
        $promote_info = $goods->getInfo(['goods_id'=>$goods_id], 'promotion_type,promote_id');
        if($promote_info['promotion_type'] == 0)
        {
            //无促销活动
            return '';
        }else if($promote_info['promotion_type'] == 1){
            //团购(注意查询活动时间)
            return '团购';
        }else if($promote_info['promotion_type'] == 2)
        {
            //限时折扣(注意查询活动时间)
            return  '限时折扣';
        }
    }
    /**
     * 获取商品sku列表的商品列表形式
     * @param unknown $goods_sku_list array(array(goods_id,skuid,num))
     */
    public function getGoodsSkuListGoods($goods_sku_list){
        $array = array();
        if(!empty($goods_sku_list)){
            $goods_sku_list_array = explode(',', $goods_sku_list);
            foreach ($goods_sku_list_array as $k => $v)
            {
                $sku_item = explode(":", $v);
                //获取商品goods_id
                $goods = new Goods();
                $goods_id = $goods->getGoodsId($sku_item[0]);
                $array[] = array($goods_id, $sku_item[0], $sku_item[1]);
            }
       
        }
        return $array;
       
    }
    /**
     * 获取商品列表所属店铺(只针对单店)
     * @param unknown $goods_sku_list
     */
    public function getGoodsSkuListShop($goods_sku_list)
    {
        if(!empty($goods_sku_list)){
            $goods_sku_list_array = explode(',', $goods_sku_list);
                $v = $goods_sku_list_array[0];
                $sku_item = explode(":", $v);
                //获取商品goods_id
                $goods = new Goods();
                $goods_id = $goods->getGoodsId($sku_item[0]);
                $shop_id = $goods->getGoodsShopid($goods_id);
                return $shop_id;
               // $array[] = array($goods_id, $sku_item[0], $sku_item[1]);
          
        }else{
            return 0;
        }
    }
    /**
     * 获取当前会员可用优惠券
     * @param unknown $goods_sku_list
     */
    public function getMemberCouponList($goods_sku_list)
    {
        //1.获取当前会员所有优惠券
        $coupon_list = array();
        $goods_sku_list_array = $this->getGoodsSkuListGoods($goods_sku_list);
        $member_coupon = new MemberCoupon();
        $member_coupon_list = $member_coupon->getUserCouponList(1);
        //2.获取当前优惠券是否可用
        if(!empty($member_coupon_list)){
            foreach ($member_coupon_list as $k => $coupon)
            {
                //查询优惠券类型的情况
                $coupon_type = new NsCouponTypeModel();
                $type_info = $coupon_type->getInfo(['coupon_type_id' => $coupon['coupon_type_id']], 'range_type,at_least');
                if($type_info['range_type'] == 1)
                {
                    //全场商品使用的优惠券
                    $price = $this->getGoodsSkuListPrice($goods_sku_list);
                    if($type_info['at_least'] <= $price)
                    {
                        $coupon_list[] = $coupon;
                    }
                }else{
                    //部分商品使用的优惠券
                    $coupon_goods = new NsCouponGoodsModel();
                    $coupon_goods_list = $coupon_goods->getQuery(['coupon_type_id' => $coupon['coupon_type_id']], '*', '');
                    $new_sku_list = '';
                    foreach ($coupon_goods_list as $k_coupon => $goods)
                    {
                        foreach ($goods_sku_list_array as $k_goods_sku => $v_goods_sku)
                        {
                            if($goods['goods_id'] == $v_goods_sku[0])
                            {
                                $new_sku_list = $new_sku_list.$v_goods_sku[1].':'.$v_goods_sku[2].',';
                            }
                        }
                    }
                   
                    
                    if(!empty($new_sku_list))
                    {
                        $new_sku_list = substr($new_sku_list, 0, strlen($new_sku_list)-1);
                    }
                    $price = $this->getGoodsSkuListPrice($new_sku_list);
                    if(!empty($new_sku_list) && $type_info['at_least'] <= $price)
                    {
                        $coupon_list[] = $coupon;
                    }
                }
            
                
            }
        }
        return $coupon_list;
    }
    /**
     * 查询会员等级折扣
     * @param unknown $uid
     */
    public function getMemberLevelDiscount($uid)
    {
        //查询会员等级
        $member = new NsMemberModel();
        $member_info = $member->getInfo(['uid' => $uid], 'member_level');
        if(!empty($member_info))
        {
           $member_level = new NsMemberLevelModel();
           $level_info = $member_level->getInfo(['level_id' => $member_info['member_level']],'goods_discount'); 
           if(!empty($level_info))
           {
               return $level_info['goods_discount'];
           }else{
               return 1;
           }
        }else{
            return 1;
        }
    }
    /**
     * 获取商品会员价
     * @param unknown $goods_sku_id
     * @param unknown $uid
     */
    public function getGoodsSkuMemberPrice($goods_sku_id, $uid){
        //查询sku相关信息
        $goods_sku = new NsGoodsSkuModel();
        $sku_info = $goods_sku->getInfo(['sku_id'=> $goods_sku_id], 'price');
        $member_level_discount = $this->getMemberLevelDiscount($uid);
        return $sku_info['price']*$member_level_discount;
    }
    /**
     * 获取自提点运费
     * @param unknown $goods_sku_list
     */
    public function getPickupMoney($goods_sku_list_price)
    {
        $config_service = new Config();
        $config_info = $config_service->getConfig($this->instance_id, 'PICKUPPOINT_FREIGHT');
        if(!empty($config_info))
        {
            $pick_up_info = json_decode($config_info['value'], true);
            if($pick_up_info['is_enable'] == 1&&$goods_sku_list_price <= $pick_up_info['manjian_freight'])
            {
                $pick_money = $pick_up_info['pickup_freight'];
            }else{
                $pick_money = 0;
            }
        }else{
            $pick_money = 0;
        }
        return $pick_money;
    
    }
  
    /*****************************************************************************************订单商品管理结束***************************************************/
    
}