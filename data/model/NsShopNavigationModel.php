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
 * 店铺导航
 *  nav_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  shop_id int(11) NOT NULL COMMENT '店铺ID',
  nav_title varchar(255) NOT NULL DEFAULT '' COMMENT '导航名称',
  nav_url varchar(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  type int(11) NOT NULL DEFAULT 3 COMMENT '所在位置1.头部2.中部3底部',
  sort int(11) NOT NULL COMMENT '排序号',
  create_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  modify_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  PRIMARY KEY (nav_id)
 * @author Administrator
 *
 */
class NsShopNavigationModel extends BaseModel {

    protected $table = 'ns_shop_navigation';
    protected $rule = [
        'nav_id'  =>  '',
    ];
    protected $msg = [
        'nav_id'  =>  '',
    ];
}