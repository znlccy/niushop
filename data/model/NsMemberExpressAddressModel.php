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
 * 会员物流地址管理
 *  id int(11) NOT NULL AUTO_INCREMENT,
  uid int(11) NOT NULL COMMENT '会员基本资料表ID',
  consigner varchar(255) NOT NULL DEFAULT '' COMMENT '收件人',
  mobile varchar(11) NOT NULL DEFAULT '' COMMENT '手机',
  phone varchar(20) NOT NULL DEFAULT '' COMMENT '固定电话',
  province int(11) NOT NULL DEFAULT 0 COMMENT '省',
  city int(11) NOT NULL DEFAULT 0 COMMENT '市',
  district int(11) NOT NULL DEFAULT 0 COMMENT '区县',
  address varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  zip_code varchar(6) NOT NULL DEFAULT '' COMMENT '邮编',
  alias varchar(50) NOT NULL DEFAULT '' COMMENT '地址别名',
  is_default bit(1) NOT NULL DEFAULT b'0' COMMENT '默认收货地址',
 * @author Administrator
 *
 */
class NsMemberExpressAddressModel extends BaseModel {
    protected $table = 'ns_member_express_address';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
}