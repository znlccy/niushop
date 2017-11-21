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
 * 满减送活动表
 *  mansong_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '满送活动编号',
  mansong_name varchar(50) NOT NULL COMMENT '活动名称',
  start_time datetime NOT NULL COMMENT '活动开始时间',
  end_time datetime NOT NULL COMMENT '活动结束时间',
  shop_id int(10) UNSIGNED NOT NULL COMMENT '店铺编号',
  shop_name varchar(50) NOT NULL COMMENT '店铺名称',
  status tinyint(1) UNSIGNED NOT NULL COMMENT '活动状态(0-未发布/1-正常/2-取消/3-失效/4-结束)',
  remark varchar(200) NOT NULL COMMENT '备注',
  type tinyint(1) NOT NULL DEFAULT 1 COMMENT '1.普通优惠  2.多级优惠',
  range_type tinyint(1) NOT NULL DEFAULT 0 COMMENT '0全部商品 1.部分商品',
  create_time datetime NOT NULL COMMENT '创建时间',
  modify_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
 */
class NsPromotionMansongModel extends BaseModel {

    protected $table = 'ns_promotion_mansong';
    protected $rule = [
        'mansong_id'  =>  '',
    ];
    protected $msg = [
        'mansong_id'  =>  '',
    ];
}