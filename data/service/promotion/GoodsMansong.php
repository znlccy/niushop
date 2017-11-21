<?php
/**
 * GoodsMansong.php
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
use data\model\NsPromotionMansongModel;
use data\model\NsPromotionMansongGoodsModel;
use data\service\BaseService;
use data\service\Goods;
use data\model\NsPromotionMansongRuleModel;
use data\model\NsCouponModel;
use data\model\NsPromotionGiftModel;
/**
 * 商品满减送活动操作类
 */
class GoodsMansong extends BaseService{
    function __construct(){
        parent::__construct();
    }
    /**
     * 查询商品在某一时间段是否有限时折扣活动
     * @param unknown $goods_id
     */
    public function getGoodsIsMansong($goods_id, $start_time, $end_time)
    {
        $mansong_goods = new NsPromotionMansongGoodsModel();
        $mansong_model = new NsPromotionMansongModel();
        $condition_1 = array(
            'start_time'=> array('ELT', $end_time),
            'end_time'  => array('EGT', $end_time),
            'status'     => array('NEQ', 3),
            'goods_id'  => $goods_id
        );
        $condition_1_1 = array(
            'start_time'=> array('ELT', $end_time),
            'end_time'  => array('EGT', $end_time),
            'status'     => array('NEQ', 3),
            'range_type' => 1
        );
        $condition_2 = array(
            'start_time'=> array('ELT', $start_time),
            'end_time'  => array('EGT', $start_time),
            'status'     => array('NEQ', 3),
            'goods_id'  => $goods_id
        );
        $condition_2_1 = array(
            'start_time'=> array('ELT', $start_time),
            'end_time'  => array('EGT', $start_time),
            'status'     => array('NEQ', 3),
            'range_type' => 1
        );
        $condition_3 = array(
            'start_time'=> array('EGT', $start_time),
            'end_time'  => array('ELT', $end_time),
            'status'     => array('NEQ', 3),
            'goods_id'  => $goods_id
        );
        $condition_3_1 = array(
            'start_time'=> array('ELT', $start_time),
            'end_time'  => array('EGT', $end_time),
            'status'     => array('NEQ', 3),
            'range_type' => 1
        );
        
        $count_1 = $mansong_goods->where($condition_1)->count();
        $count_1_1 = $mansong_model->where($condition_1_1)->count();
        $count_2 = $mansong_goods->where($condition_2)->count();
        $count_2_1 = $mansong_model->where($condition_2_1)->count();
        $count_3 = $mansong_goods->where($condition_3)->count();
        $count_3_1 = $mansong_model->where($condition_3_1)->count();
        $count = $count_1 + $count_2 + $count_3+$count_1_1+$count_2_1+$count_3_1;
        return $count;
    }
    /**
     * 获取在一段时间之内是否存在全场满减(全场活动检测同时存在部分商品活动)
     * @param unknown $start_time
     * @param unknown $end_time
     */
    public function getQuanmansong($start_time, $end_time)
    {
        $mansong_model = new NsPromotionMansongModel();
        $condition_1_1 = array(
            'start_time'=> array('ELT', $end_time),
            'end_time'  => array('EGT', $end_time),
            'status'     => array('NEQ', 3)
        );
        $condition_2_1 = array(
            'start_time'=> array('ELT', $start_time),
            'end_time'  => array('EGT', $start_time),
            'status'     => array('NEQ', 3)
        );
        $condition_3_1 = array(
            'start_time'=> array('ELT', $start_time),
            'end_time'  => array('EGT', $end_time),
            'status'     => array('NEQ', 3)
        );
        $count_1_1 = $mansong_model->where($condition_1_1)->count();
        $count_2_1 = $mansong_model->where($condition_2_1)->count();
        $count_3_1 = $mansong_model->where($condition_3_1)->count();
        $count = $count_1_1+$count_2_1+$count_3_1;
        return $count;
        
    }
    /**
     * 获取商品满减送优惠(核心函数)
     * @param unknown $goods_sku_list
     */
    public function getGoodsSkuListMansong($goods_sku_list)
    {
        
        $discount_info = array();
        $goods_preference = new GoodsPreference();
        $goods_sku_list_price = $goods_preference->getGoodsSkuListPrice($goods_sku_list);
        
        if(!empty($goods_sku_list))
        {
            $time = date("Y-m-d H:i:s", time());
            //检测店铺是否存在正在进行的全场满减送活动
            $condition = array(
                'status'     => 1,
                'range_type' => 1,
                'shop_id'    => $this->instance_id
            );
            $promotion_mansong = new NsPromotionMansongModel();
            $list_quan = $promotion_mansong -> getQuery($condition, '*', 'create_time desc');
            if(!empty($list_quan[0]))
            {
                //存在全场满减送
                $goods_sku_list_array = explode( ",", $goods_sku_list);
                $rule_list = $this->getMansongRule($list_quan[0]['mansong_id']);//得到满减规则
                $discount = array();
                    //获取订单项减现金额
                    foreach ($rule_list as $k_rule=>$rule)
                    {
                        if($rule['price'] <= $goods_sku_list_price){
                            foreach ($goods_sku_list_array as $k_goods_sku => $v_goods_sku)
                            {
                                $sku_data_goods = explode(":", $v_goods_sku);
                                $sku_id_goods = $sku_data_goods[0];
                                $sku_count_goods = $sku_data_goods[1];
                                $goods_preference = new GoodsPreference();
                                $goods_sku_price = $goods_preference->getGoodsSkuListPrice($v_goods_sku);
                                $goods_sku_promote_price = $rule['discount'] * $goods_sku_price/$goods_sku_list_price;
                                $discount[] = array($rule, $sku_id_goods.":".$goods_sku_promote_price);
                            
                            }
                            break;
                        }
                    }
                  
                $discount_info[0] = array(
                    'rule' => $list_quan[0],
                    'discount_detail' => $discount
                );
            }else{
                //存在部分商品满减送活动(只可能存在部分商品满减送)
                //1.查询商品列表可能的满减送活动列表
                $mansong_list = $this->getGoodsSkuMansongList($goods_sku_list);
                
                if(!empty($mansong_list))
                {
                    //循环满减送活动
                    foreach($mansong_list as $k => $v)
                    {
                        $discount_info_detail = $this->getMansongGoodsSkuListPromotion($v, $goods_sku_list);
                        $discount_info[] = $discount_info_detail;
                    }
                    
                }
                
                
            }
           
        }
        return $discount_info;
    
    }
    /**
     * 获取免邮商品列表(由于满减送产生)
     * @param unknown $goods_sku_list
     */
    public function getFreeExpressGoodsSkuList($goods_sku_list)
    {
        $goods_sku_array = array();
        $mansong_array = $this->getGoodsSkuListMansong($goods_sku_list);
        if(!empty($mansong_array))
        {
            foreach ($mansong_array as $k_mansong => $v_mansong)
            {
               
                    //存在免邮活动
                    foreach($v_mansong['discount_detail'] as $k_rule => $v_rule)
                    {
                        $mansong_rule = $v_rule[0];
                        if($mansong_rule['free_shipping'] == 1)
                        {
                            $rule = $v_rule[1];
                            $discount_money_detail = explode(':',$rule);
                            $goods_sku_array[] = $discount_money_detail[0];
                        }
                      
                    
                    }
               
            }
        }
        return $goods_sku_array;
    }
    /**
     * 获取满减送金额
     * @param unknown $goods_sku_list
     */
    public function getGoodsMansongMoney($goods_sku_list)
    {
        $mansong_array = $this->getGoodsSkuListMansong($goods_sku_list);
        $promotion_money = 0;
        if(!empty($mansong_array))
        {
            foreach ($mansong_array as $k_mansong => $v_mansong)
            {
                foreach($v_mansong['discount_detail'] as $k_rule => $v_rule)
                {
                    $rule = $v_rule[1];
                    $discount_money_detail = explode(':',$rule);
                    $promotion_money += round($discount_money_detail[1],2);
              
                }
            }
        }
        return $promotion_money;
    }
    /**
     * 获取当前商品满减送活动(只查询部分商品的满减送活动)
     * @param unknown $goods_id
     */
    public function getGoodsMansongPromotion($goods_id)
    {
        $time = date("Y-m-d H:i:s", time());

            //查询当前部分商品活动
            $condition = array(
                'status'     => 1,
                'range_type' => 0,
                'shop_id'    => $this->instance_id
    
            );
            $promotion_mansong = new NsPromotionMansongModel();
            $list = $promotion_mansong -> getQuery($condition, '*', 'create_time desc');
            foreach($list as $k => $v)
            {
                //检测当前满减送或送是否与此商品有关
                $promotion_mansong_goods = new NsPromotionMansongGoodsModel();
                $info = $promotion_mansong_goods->getInfo(['mansong_id' => $v['mansong_id'],'goods_id' => $goods_id], '*');
                if(!empty($info))
                {
                    return $v;
                }
    
            }
            return '';
    }
    /**
     * 获取商品sku的满减送活动列表
     * @param unknown $goods_sku_list
     */
    public function getGoodsSkuMansongList($goods_sku_list)
    {
        $promotion_array = array();
        if(!empty($goods_sku_list))
        {
            $goods_sku_list_array = explode( ",", $goods_sku_list);
            foreach ($goods_sku_list_array as $k => $v)
            {
                $sku_data = explode(":", $v);
                $sku_id = $sku_data[0];
                $sku_count = $sku_data[1];
                //查询商品的goodsid
                $goods = new Goods();
                $goods_id = $goods->getGoodsId($sku_id);
                $promotion = $this->getGoodsMansongPromotion($goods_id);
                if(!empty($promotion))
                {
                    $promotion_array[] = $promotion;
                }
            }
                
        }
        
       /*   if(!empty($promotion_array))
        {
            foreach ($promotion_array as $k => $v)
            {
                
            }
        }  */
        $array = array_unique($promotion_array);
        return $array;
        
    }
    /**
     * 获取满减送规则
     * @param unknown $mansong_id
     */
    public function getMansongRule($mansong_id)
    {
        $mansong_rule = new NsPromotionMansongRuleModel();
        $rule_list = $mansong_rule->getQuery(['mansong_id' => $mansong_id], '*','price desc');
        return $rule_list;
    }
    /**
     * 查询满减送商品列表
     * @param unknown $mansong_id
     */
    public function getMansongGoods($mansong_id)
    {
        $mansong_goods = new NsPromotionMansongGoodsModel();
        $list = $mansong_goods->getQuery(['mansong_id' => $mansong_id], '*', '');
        return $list;
    }
    /**
     * 查询商品的满减送详情(应用商品详情)
     * @param unknown $goods_id
     */
    public function getGoodsMansongDetail($goods_id)
    {
        //查询全场满减送活动
        //检测店铺是否存在正在进行的全场满减送活动
        $condition = array(
            'status'     => 1,
            'range_type' => 1,
            'shop_id'    => $this->instance_id
        );
        $promotion_mansong = new NsPromotionMansongModel();
        $list_quan = $promotion_mansong -> getQuery($condition, '*', 'create_time desc');
        if(!empty($list_quan[0]))
        {
            $mansong_promotion = $list_quan[0];
        }
        //1. 查询商品满减送活动
        if(empty($mansong_promotion))
        {
            $mansong_promotion = $this->getGoodsMansongPromotion($goods_id);
        }
      
        if(!empty($mansong_promotion))
        {
            $rule = $this->getMansongRule($mansong_promotion['mansong_id']);
            $mansong_promotion['rule'] = $rule;
        
        }
        return $mansong_promotion;
    }
    /**
     * 查询商品满减送活动名称
     * @param unknown $goods_id
     */
    public function getGoodsMansongName($goods_id)
    {
        //查询满减送活动详情
        $mansong_detail = $this->getGoodsMansongDetail($goods_id);
        $mansong_name = '';
        if(!empty($mansong_detail))
        {
            foreach ($mansong_detail['rule'] as $k => $v)
            {
                $mansong_name .= '满'.$v['price'].'减'.$v['discount'].' ';
                if($v['free_shipping'] == 1)
                {
                    $mansong_name.='免邮'.' ';
                }
                if($v['give_point'] != 0)
                {
                    $mansong_name.='赠送'.$v['give_point'].'积分'.' ';
                }
                if($v['give_coupon'] != 0)
                {
                    $coupon = new NsCouponModel();
                    $coupon_name = $coupon->getInfo(['coupon_type_id' => $v['give_coupon']], 'money');
                    $mansong_name.='赠送'.$coupon_name['money'].'元优惠券'.' ';
                }
                if($v['gift_id'] != 0)
                {
                    $gift = new NsPromotionGiftModel();
                    $gift_name = $gift->getInfo(['gift_id' => $v['gift_id']], 'gift_name');
                    $mansong_name.='赠送'.$gift_name['gift_name'];
                }
                $mansong_name.='; ';
            }
        }
        return $mansong_name;
    }
    /**
     * 查询对应满减送活动的商品列表的优惠情况
     * @param unknown $mansong_obj(只针对部分商品满减)
     * @param unknown $goods_sku_list
     */
    public function getMansongGoodsSkuListPromotion($mansong_obj, $goods_sku_list)
    {
        $new_sku_list = '';
        $new_sku_list_array = array();
        $goods_sku_list_array = $this->getGoodsSkuListGoods($goods_sku_list);
        //查询组装新的sku列表
        $mansong_goods = $this->getMansongGoods($mansong_obj['mansong_id']);
        foreach ($goods_sku_list_array as $k => $v)
        {
            foreach ($mansong_goods as $k_mansong => $v_mansong)
            {
                if($v[2] == $v_mansong['goods_id'])
                {
                    $new_sku_list = $new_sku_list.$v[0].':'.$v[1].',';
                    $new_sku_list_array[] = $v;
                }
            }
        }
        if(!empty($new_sku_list))
        {
            $new_sku_list = substr($new_sku_list, 0, strlen($new_sku_list)-1);
            //获取总价
            $goods_preference = new GoodsPreference();
            $new_sku_list_price = $goods_preference->getGoodsSkuListPrice($new_sku_list);
            $rule_list = $this->getMansongRule($mansong_obj['mansong_id']);//得到满减规则
            $discount = array();
            //获取订单项减现金额
            foreach ($rule_list as $k_rule=>$rule)
            {
                if($rule['price'] <= $new_sku_list_price){
                    foreach ($new_sku_list_array as $k_goods_sku => $v_goods_sku)
                    {
                       
                        $sku_id_goods = $v_goods_sku[0];
                        $sku_count_goods = $v_goods_sku[1];
                        $goods_preference = new GoodsPreference();
                        $goods_sku_price = $goods_preference->getGoodsSkuListPrice($sku_id_goods.':'.$sku_count_goods);
                        $goods_sku_promote_price = $rule['discount'] * $goods_sku_price/$new_sku_list_price;
                        $discount[] = array($rule, $sku_id_goods.":".$goods_sku_promote_price);
            
                    }
                    break;
                }
            }
            
            return array(
                'rule' => $mansong_obj,
                'discount_detail' => $discount
            );
        }
        else 
            return array();
        
        
    }
    /**
     * 查询商品水库列表的商品列表情况(返回数组)
     * @param unknown $goods_sku_list
     */
    public function getGoodsSkuListGoods($goods_sku_list)
    {
        $list = array();
        $goods_sku_list_array = explode( ",", $goods_sku_list);
        foreach ($goods_sku_list_array as $k => $v)
        {
            $sku_data = explode(":", $v);
            $sku_id = $sku_data[0];
            $sku_count = $sku_data[1];
            //查询商品的goodsid
            $goods = new Goods();
            $goods_id = $goods->getGoodsId($sku_id);
            $sku_data[2] = $goods_id;
            $list[] = $sku_data;
        }
        return $list;
    }
}