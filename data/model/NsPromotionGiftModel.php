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
 * 赠品活动表
 *  gift_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '赠品活动id ',
      start_time datetime NOT NULL COMMENT '赠品有效期开始时间',
      days int(10) UNSIGNED NOT NULL COMMENT '领取有效期(多少天)',
      end_time datetime NOT NULL COMMENT '赠品有效期结束时间',
      max_num varchar(50) NOT NULL COMMENT '领取限制(次/人 (0表示不限领取次数))',
      shop_id varchar(100) NOT NULL COMMENT '店铺id',
      shop_name varchar(255) NOT NULL COMMENT '店铺名称',
      create_time tinyint(3) UNSIGNED NOT NULL COMMENT '创建时间',
 */
class NsPromotionGiftModel extends BaseModel {

    protected $table = 'ns_promotion_gift';
    protected $rule = [
        'gift_id'  =>  '',
    ];
    protected $msg = [
        'gift_id'  =>  '',
    ];

}