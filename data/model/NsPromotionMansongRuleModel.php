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
 * 满减送规则表
 * rule_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '规则编号',
  mansong_id int(10) UNSIGNED NOT NULL COMMENT '活动编号',
  price decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '级别价格(满多少)',
  discount decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '减现金优惠金额（减多少金额）',
  free_shipping tinyint(4) NOT NULL DEFAULT 0 COMMENT '免邮费',
  give_point int(11) NOT NULL DEFAULT 0 COMMENT '送积分数量（0表示不送）',
  give_coupon int(11) NOT NULL DEFAULT 0 COMMENT '送优惠券的id（0表示不送）',
  gift_id int(11) NOT NULL COMMENT '礼品(赠品)id',
  PRIMARY KEY (rule_id)
 */
class NsPromotionMansongRuleModel extends BaseModel {

    protected $table = 'ns_promotion_mansong_rule';
    protected $rule = [
        'rule_id'  =>  '',
    ];
    protected $msg = [
        'rule_id'  =>  '',
    ];

}