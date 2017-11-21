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
 * 限时折扣商品表
 *  discount_goods_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  discount_id int(11) NOT NULL COMMENT '对应活动',
  start_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间',
  end_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间',
  goods_id int(11) NOT NULL COMMENT '商品ID',
  status tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态',
  discount tinyint(1) NOT NULL COMMENT '活动折扣或者减现信息',
  PRIMARY KEY (discount_goods_id)
 */
class NsPromotionDiscountGoodsViewModel extends BaseModel {

    protected $table = 'ns_promotion_discount_goods';
    
    /**
     * 获取列表返回数据格式
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @return unknown
     */
    public function getViewList($page_index, $page_size, $condition, $order){
    
        $queryList = $this->getViewQuery($page_index, $page_size, $condition, $order);
        $queryCount = $this->getViewCount($condition);
        $list = $this->setReturnList($queryList, $queryCount, $page_size);
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
    public function getViewQuery($page_index, $page_size, $condition, $order)
    {
        //设置查询视图
        $viewObj = $this->alias('npdg')
        ->join('ns_goods ng','ng.goods_id = npdg.goods_id','inner')
        ->field('npdg.discount_goods_id,npdg.discount_id,npdg.start_time,npdg.end_time,npdg.goods_id,npdg.status,npdg.discount,npdg.goods_name,npdg.goods_picture,ng.category_id_1,ng.price,ng.promotion_price,ng.market_price');
        $list = $this->viewPageQuery($viewObj, $page_index, $page_size, $condition, $order);
        return $list;
    }
    /**
     * 获取列表数量
     * @param unknown $condition
     * @return \data\model\unknown
     */
    public function getViewCount($condition)
    {
        $viewObj = $this->alias('npdg')
        ->join('ns_goods ng','ng.goods_id = npdg.goods_id','inner')
        ->field('npdg.discount_goods_id');
        $count = $this->viewCount($viewObj,$condition);
        return $count;
    }

}