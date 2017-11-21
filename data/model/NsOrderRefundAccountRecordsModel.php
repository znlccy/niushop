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
 * 订单退款账户记录
   id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  refund_trade_no varchar(55) NOT NULL,
  refund_money decimal(10, 2) NOT NULL COMMENT '退款金额',
  refund_way int(11) NOT NULL COMMENT '退款方式（1：微信，2：支付宝，10：线下）',
  buyer_id int(11) NOT NULL COMMENT '买家id',
  refund_time int(11) NOT NULL COMMENT '退款时间',
  remark varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (id)
 */
class NsOrderRefundAccountRecordsModel extends BaseModel {

    protected $table = 'ns_order_refund_account_records';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];

}