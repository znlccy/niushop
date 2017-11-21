<?php
/**
 * NfxUserCommissionWithdrawModel.php
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
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 会员余额提现记录表
 *
 * id int(11) NOT NULL AUTO_INCREMENT,
  shop_id int(11) NOT NULL COMMENT '店铺编号',
  withdraw_no varchar(255) NOT NULL DEFAULT '' COMMENT '提现流水号',
  uid int(11) NOT NULL COMMENT '会员id',
  bank_name varchar(50) NOT NULL COMMENT '提现银行名称',
  account_number varchar(50) NOT NULL COMMENT '提现银行账号',
  realname varchar(10) NOT NULL COMMENT '提现账户姓名',
  mobile varchar(20) NOT NULL COMMENT '手机',
  cash decimal(10, 2) NOT NULL COMMENT '提现金额',
  ask_for_date datetime NOT NULL COMMENT '提现日期',
  payment_date datetime DEFAULT NULL COMMENT '到账日期',
  status smallint(6) NOT NULL DEFAULT 0 COMMENT '当前状态 0已申请(等待处理) 1已同意 -1 已拒绝',
  memo varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  modify_date datetime DEFAULT NULL COMMENT '修改日期',
  PRIMARY KEY (Id)
 */
class NsMemberBalanceWithdrawModel extends BaseModel {

    protected $table = 'ns_member_balance_withdraw';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];

}