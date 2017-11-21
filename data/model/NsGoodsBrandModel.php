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
 * 商品品牌表
 *    brand_id bigint(20) NOT NULL COMMENT '索引ID',
      shop_id int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '店铺ID',
      brand_name varchar(100) NOT NULL COMMENT '品牌名称',
      brand_initial varchar(1) NOT NULL COMMENT '品牌首字母',
      brand_pic varchar(100) NOT NULL DEFAULT '' COMMENT '图片',
      brand_recommend tinyint(1) NOT NULL DEFAULT 0 COMMENT '推荐，0为否，1为是，默认为0',
      sort tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
      brand_category_name varchar(50) NOT NULL DEFAULT '' COMMENT '类别名称',
      category_id_array varchar(1000) NOT NULL DEFAULT '' COMMENT '所属分类id',
      PRIMARY KEY (brand_id)
 * @author Administrator
 *
 */
class NsGoodsBrandModel extends BaseModel {

    protected $table = 'ns_goods_brand';
    protected $rule = [
        'brand_id'  =>  '',
    ];
    protected $msg = [
        'brand_id'  =>  '',
    ];

}