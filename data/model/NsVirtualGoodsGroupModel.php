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
 * 虚拟商品分组表
 * virtual_goods_group_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '虚拟商品分组id',
 * virtual_goods_group_name varchar(255) NOT NULL DEFAULT '' COMMENT '虚拟商品分组名称',
 * interfaces varchar(1000) NOT NULL COMMENT '接口调用地址（JSON）',
 * create_time int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
 *
 * @author Administrator
 *        
 */
class NsVirtualGoodsGroupModel extends BaseModel
{

    protected $table = 'ns_virtual_goods_group';

    protected $rule = [];

    protected $msg = [];
}