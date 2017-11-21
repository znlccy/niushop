<?php
/**
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
namespace data\model;

use data\model\BaseModel as BaseModel;
use data\model\NsGoodsGroupModel as NsGoodsGroupModel;
use data\model\NsGoodsSkuModel as NsGoodsSkuModel;
/**
 * 商品表视图
 * @author Administrator
 *
 */
class NsGoodsViewModel extends BaseModel {

    protected $table = 'ns_goods';
    
    /**
     * 获取列表返回数据格式
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @return unknown
     */
    public function getGoodsViewList($page_index, $page_size, $condition, $order){
    
        $queryList = $this->getGoodsViewQuery($page_index, $page_size, $condition, $order);
        $queryCount = $this->getGoodsrViewCount($condition);
        $list = $this->setReturnList($queryList, $queryCount, $page_size);
        return $list;
    }
    /**
     * 查询商品的视图
     * @param unknown $condition
     * @param unknown $field
     * @param unknown $order
     * @return unknown
     */
    public function getGoodsViewQueryField($condition, $field, $order=""){
        $viewObj = $this->alias('ng')
            ->join('ns_goods_category ngc','ng.category_id = ngc.category_id','left')
            ->join('ns_goods_brand ngb','ng.brand_id = ngb.brand_id','left')
            ->join('sys_album_picture sap','ng.picture = sap.pic_id', 'left')
            ->field($field);
        $list = $viewObj->where($condition)
        ->order($order)
        ->select();
        return $list;
    }
    /**
     * 获取列表
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @return \data\model\multitype:number
     */
    public function getGoodsViewQuery($page_index, $page_size, $condition, $order)
    {
        $viewObj = $this->alias('ng')
        ->join('ns_goods_category ngc','ng.category_id = ngc.category_id','left')
        ->join('ns_goods_brand ngb','ng.brand_id = ngb.brand_id','left')
        ->join('sys_album_picture sap','ng.picture = sap.pic_id', 'left')
        ->join('ns_shop nss','ng.shop_id = nss.shop_id','left')
        ->field('ng.goods_id, ng.goods_name, ng.shop_id, ng.category_id, ng.brand_id, ng.group_id_array,
             ng.promotion_type, ng.goods_type, ng.market_price, ng.price, ng.promotion_price, 
            ng.cost_price, ng.point_exchange_type, ng.point_exchange, ng.give_point, 
            ng.is_member_discount, ng.shipping_fee, ng.shipping_fee_id, ng.stock, ng.max_buy, 
            ng.min_stock_alarm, ng.clicks, ng.sales, ng.collects, ng.star, ng.evaluates, 
            ng.shares, ng.province_id, ng.city_id, ng.picture, ng.keywords, ng.introduction, 
            ng.description, ng.QRcode, ng.code, ng.is_stock_visible, ng.is_hot, ng.is_recommend, 
            ng.is_new, ng.is_pre_sale, ng.is_bill, ng.state, ng.sale_date, ng.create_time, 
            ng.update_time, ng.sort, ng.real_sales, ngb.brand_name, ngb.brand_pic, ngc.category_id, ngc.category_name, sap.pic_cover_micro,sap.pic_cover_mid,sap.pic_cover_small,nss.shop_name,nss.shop_type,sap.pic_id,sap.upload_type, sap.domain, sap.bucket ');
        $list = $this->viewPageQuery($viewObj, $page_index, $page_size, $condition, $order);
        if(!empty($list))
        {
            $goods_group_model = new NsGoodsGroupModel();
            $goods_sku = new NsGoodsSkuModel();
            foreach ($list as $k=>$v)
            {
               
                //获取group列表
                $group_name_query = $goods_group_model->all($v['group_id_array']);
               
                $list[$k]['group_query'] = $group_name_query;
                //获取sku列表
                $sku_list = $goods_sku->where(['goods_id'=>$v['goods_id']])->select();
                
                $list[$k]['sku_list'] = $sku_list;
            }
        }
        return $list;
    }
    /**
     * 获取列表数量
     * @param unknown $condition
     * @return \data\model\unknown
     */
    public function getGoodsrViewCount($condition)
    {
        $viewObj = $this->alias('ng')
        ->join('ns_goods_category ngc','ng.category_id = ngc.category_id','left')
        ->join('ns_goods_brand ngb','ng.brand_id = ngb.brand_id','left')
        ->join('sys_album_picture sap','ng.picture = sap.pic_id', 'left')
        ->field('ng.goods_id');
        $count = $this->viewCount($viewObj,$condition);
        return $count;
    }
}