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
 * 购物车
 *  cart_id int(11) NOT NULL AUTO_INCREMENT COMMENT '购物车id',
  buyer_id int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '买家id',
  shop_id int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '店铺id',
  shop_name varchar(100) NOT NULL DEFAULT '' COMMENT '店铺名称',
  goods_id int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品id',
  goods_name varchar(200) NOT NULL COMMENT '商品名称',
  sku_id int(11) NOT NULL DEFAULT 0 COMMENT '商品的skuid',
  sku_name varchar(200) NOT NULL DEFAULT '' COMMENT '商品的sku名称',
  price decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '商品价格',
  num smallint(5) UNSIGNED NOT NULL DEFAULT 1 COMMENT '购买商品数量',
  goods_picture int(11) NOT NULL DEFAULT 0 COMMENT '商品图片',
  bl_id mediumint(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '组合套装ID',
  PRIMARY KEY (cart_id),
 * @author Administrator
 *
 */
class NsCartModel extends BaseModel {

    protected $table = 'ns_cart';
    protected $rule = [
        'cart_id'  =>  '',
    ];
    protected $msg = [
        'cart_id'  =>  '',
    ];

}