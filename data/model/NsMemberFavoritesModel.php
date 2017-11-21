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
/**
 * 会员收藏表
 * @author Administrator
 *
 */
class NsMemberFavoritesModel extends BaseModel {
    protected $table = 'ns_member_favorites';
    protected $rule = [
        'log_id'  =>  '',
    ];
    protected $msg = [
        'log_id'  =>  '',
    ];
    /**
     * 获取列表返回数据格式
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @return unknown
     */
    public function getGoodsFavouitesViewList($page_index, $page_size, $condition, $order){
    
        $queryList = $this->getGoodsFavouitesViewQuery($page_index, $page_size, $condition, $order);
        $queryCount = $this->getGoodsFavouitesViewCount($condition);
        $list = $this->setReturnList($queryList, $queryCount, $page_size);
        return $list;
    }
    
    /**
     * 获取商品收藏列表
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @return \data\model\multitype:number
     */
    public function getGoodsFavouitesViewQuery($page_index, $page_size, $condition, $order)
    {
        $viewObj = $this->alias('nmf')
        ->join('ns_goods ng',"nmf.fav_id = ng.goods_id","left")
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
            ng.update_time, ng.sort, ngb.brand_name, ngb.brand_pic, ngc.category_id, ngc.category_name, sap.pic_cover as pic_cover_micro,sap.pic_cover_mid,nss.shop_name,nmf.*');
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
    public function getGoodsFavouitesViewCount($condition)
    {
        $viewObj = $this->alias('nmf')
       ->join('ns_goods ng',"nmf.fav_id = ng.goods_id","left")
        ->join('ns_goods_category ngc','ng.category_id = ngc.category_id','left')
        ->join('ns_goods_brand ngb','ng.brand_id = ngb.brand_id','left')
        ->join('sys_album_picture sap','ng.picture = sap.pic_id', 'left')
        ->join('ns_shop nss','ng.shop_id = nss.shop_id','left')
        ->field('nmf.log_id');
        $count = $this->viewCount($viewObj,$condition);
        return $count;
    }
    /**
     * 获取列表返回数据格式
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @return unknown
     */
    public function getShopsFavouitesViewList($page_index, $page_size, $condition, $order){
    
        $queryList = $this->getShopsFavouitesViewQuery($page_index, $page_size, $condition, $order);
        $queryCount = $this->getShopsFavouitesViewCount($condition);
        $list = $this->setReturnList($queryList, $queryCount, $page_size);
        return $list;
    }
    
    /**
     * 获取店铺收藏列表
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @return \data\model\multitype:number
     */
    public function getShopsFavouitesViewQuery($page_index, $page_size, $condition, $order)
    {
        $viewObj = $this->alias('nmf')
        ->join('ns_shop ns',"nmf.fav_id = ns.shop_id","left")       
        ->field('ns.shop_id,ns.shop_name,ns.shop_type,ns.shop_group_id,ns.shop_company_name,ns.province_id,ns.city_id,
            ns.shop_address,ns.shop_zip,ns.shop_state,ns.shop_close_info,ns.shop_sort,ns.shop_create_time,ns.shop_end_time,
            ns.shop_logo,ns.shop_banner,ns.shop_avatar,ns.shop_keywords,ns.shop_description,ns.shop_qq,ns.shop_ww,ns.shop_phone,ns.shop_domain,ns.shop_domain_times,
            ns.shop_recommend,ns.shop_credit,ns.shop_desccredit,ns.shop_servicecredit,ns.shop_deliverycredit,
            ns.shop_collect,ns.shop_stamp,ns.shop_printdesc,ns.shop_sales,ns.shop_workingtime,ns.live_store_name,ns.live_store_address,
            ns.live_store_tel,ns.live_store_bus,ns.shop_vrcode_prefix,ns.store_qtian,ns.shop_zhping,ns.shop_erxiaoshi,ns.shop_tuihuo,
            ns.shop_shiyong,ns.shop_shiti,ns.shop_xiaoxie,ns.shop_huodaofk,ns.shop_free_time,ns.shop_region,nmf.*');
        $list = $this->viewPageQuery($viewObj, $page_index, $page_size, $condition, $order);          
        return $list;
    }
    /**
     * 获取列表数量
     * @param unknown $condition
     * @return \data\model\unknown
     */
    public function getShopsFavouitesViewCount($condition)
    {
        $viewObj = $this->alias('nmf')
        ->join('ns_shop ns',"nmf.fav_id = ns.shop_id","left")         
        ->field('nmf.log_id');
        $count = $this->viewCount($viewObj,$condition);
        return $count;
    }
}