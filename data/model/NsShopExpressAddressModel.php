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
 * 店铺物流公司地址表
 *    express_address_id int(11) NOT NULL AUTO_INCREMENT COMMENT '物流地址id',
      shop_id int(11) NOT NULL COMMENT '商铺id',
      contact varchar(50) NOT NULL DEFAULT '' COMMENT '联系人',
      mobile varchar(50) NOT NULL DEFAULT '' COMMENT '手机',
      phone varchar(50) NOT NULL DEFAULT '' COMMENT '电话',
      company_name varchar(100) NOT NULL DEFAULT '' COMMENT '公司名称',
      province smallint(6) NOT NULL DEFAULT 0 COMMENT '所在地省',
      city smallint(6) NOT NULL DEFAULT 0 COMMENT '所在地市',
      district smallint(6) NOT NULL DEFAULT 0 COMMENT '所在地区县',
      zipcode varchar(6) NOT NULL DEFAULT '' COMMENT '邮编',
      address varchar(100) NOT NULL DEFAULT '' COMMENT '详细地址',
      is_consigner tinyint(2) NOT NULL DEFAULT 0 COMMENT '发货地址标记',
      is_receiver tinyint(2) NOT NULL DEFAULT 0 COMMENT '收货地址标记',
      create_date datetime DEFAULT NULL COMMENT '创建日期',
      modify_date datetime DEFAULT NULL COMMENT '修改日期',
 * @author Administrator
 *
 */
class NsShopExpressAddressModel extends BaseModel {

    protected $table = 'ns_shop_express_address';
    protected $rule = [
        'express_address_id'  =>  '',
    ];
    protected $msg = [
        'express_address_id'  =>  '',
    ];

}