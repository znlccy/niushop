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
 * 商品分类表
 *    category_id int(11) NOT NULL AUTO_INCREMENT,
      category_name varchar(50) NOT NULL DEFAULT '',
      pid int(11) NOT NULL DEFAULT 0,
      level tinyint(4) NOT NULL DEFAULT 0,
      is_visible bit(1) NOT NULL DEFAULT b'1',
      keywords varchar(255) NOT NULL DEFAULT '',
      description varchar(255) NOT NULL DEFAULT '',
      sort tinyint(4) NOT NULL DEFAULT 0,
      PRIMARY KEY (category_id)
 * @author Administrator
 *
 */
class NsGoodsCategoryModel extends BaseModel {

    protected $table = 'ns_goods_category';
    protected $rule = [
        'category_id'  =>  '',
    ];
    protected $msg = [
        'category_id'  =>  '',
    ];
}