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
 * 虚拟商品类型表
 * virtual_goods_type_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '虚拟商品类型id',
 * virtual_goods_group_id int(11) NOT NULL COMMENT '关联虚拟商品分组id',
 * virtual_goods_type_name varchar(255) NOT NULL COMMENT '虚拟商品类型名称',
 * validity_period int(11) NOT NULL DEFAULT 0 COMMENT '有效期/天(0表示不限制)',
 * is_enabled tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否启用（禁用后要查询关联的虚拟商品给予弹出确认提示框）',
 * config_info varchar(1000) NOT NULL COMMENT '配置信息(JSON)示例：金额',
 * interfaces varchar(1000) NOT NULL COMMENT '接口调用地址(JSON)',
 * confine_use_number int(11) NOT NULL DEFAULT 0 COMMENT '限制使用次数',
 * create_time int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
 *
 * @author Administrator
 *        
 */
class NsVirtualGoodsTypeModel extends BaseModel
{

    protected $table = 'ns_virtual_goods_type';

    protected $rule = [];

    protected $msg = [];
}