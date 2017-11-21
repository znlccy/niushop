<?php
/**
 * GoodsDiscount.php
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

use data\model\NsPromotionDiscountGoodsModel;
use data\model\NsPromotionDiscountGoodsViewModel;
use data\service\BaseService;
use data\model\AlbumPictureModel;
use data\model\NsPromotionDiscountModel;
use data\model\NsShopModel;
use think\Model;

/**
 * 商品显示折扣活动
 * 
 * @author Administrator
 *        
 */
class GoodsDiscount extends BaseService
{

    /**
     * 查询商品在某一时间段是否有限时折扣活动
     * 
     * @param unknown $goods_id            
     */
    public function getGoodsIsDiscount($goods_id, $start_time, $end_time)
    {
        $discount_goods = new NsPromotionDiscountGoodsModel();
        $condition_1 = array(
            'start_time' => array(
                'ELT',
                $end_time
            ),
            'end_time' => array(
                'EGT',
                $end_time
            ),
            'status' => array(
                'NEQ',
                3
            ),
            'goods_id' => $goods_id
        );
        $condition_2 = array(
            'start_time' => array(
                'ELT',
                $start_time
            ),
            'end_time' => array(
                'EGT',
                $start_time
            ),
            'status' => array(
                'NEQ',
                3
            ),
            'goods_id' => $goods_id
        );
        $condition_3 = array(
            'start_time' => array(
                'EGT',
                $start_time
            ),
            'end_time' => array(
                'ELT',
                $end_time
            ),
            'status' => array(
                'NEQ',
                3
            ),
            'goods_id' => $goods_id
        );
        $count_1 = $discount_goods->where($condition_1)->count();
        $count_2 = $discount_goods->where($condition_2)->count();
        $count_3 = $discount_goods->where($condition_3)->count();
        $count = $count_1 + $count_2 + $count_3;
        return $count;
    }

    /**
     * 查询限时折扣的商品
     * 
     * @param number $page_index            
     * @param number $page_size            
     * @param array $condition
     *            注意传入数组
     * @param string $order            
     */
    public function getDiscountGoodsList($page_index = 1, $page_size = 0, $condition = array(), $order = '')
    {
        $discount_goods = new NsPromotionDiscountGoodsViewModel();
        $goods_list = $discount_goods->getViewList($page_index, $page_size, $condition, $order);
        if (! empty($goods_list['data'])) {
            $discount = new NsPromotionDiscountModel();
            $shop = new NsShopModel();
            $picture = new AlbumPictureModel();
            foreach ($goods_list['data'] as $k => $v) {
                $discount_info = $discount->getInfo([
                    'discount_id' => $v['discount_id']
                ], 'shop_id, shop_name, discount_name');
                $goods_list['data'][$k]['shop_name'] = $discount_info['shop_name'];
                $goods_list['data'][$k]['discount_name'] = $discount_info['discount_name'];
                $shop_info = $shop->getInfo([
                    'shop_id' => $discount_info['shop_id']
                ], 'shop_credit');
                $goods_list['data'][$k]['shop_credit'] = $shop_info['shop_credit'];
                $picture_info = $picture->get($v['goods_picture']);
                $goods_list['data'][$k]['picture'] = $picture_info;
            }
        }
        return $goods_list;
    }

    /**
     * 获取 一个商品的 限时折扣信息
     * 
     * @param unknown $goods_id            
     */
    public function getDiscountByGoodsid($goods_id)
    {
        $discount_goods = new NsPromotionDiscountGoodsModel();
        $discount = $discount_goods->getInfo([
            'goods_id' => $goods_id,
            'status' => 1
        ], 'discount');
        if (empty($discount)) {
            return - 1;
        } else {
            return $discount['discount'];
        }
    }
}