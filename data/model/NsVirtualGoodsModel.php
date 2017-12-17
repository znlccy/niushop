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
 * 虚拟商品列表(用户下单支成功后存放)
 *
 * virtual_code varbinary(255) NOT NULL COMMENT '虚拟码',
 * virtual_goods_name varchar(255) NOT NULL COMMENT '虚拟商品名称',
 * money decimal(10, 2) NOT NULL COMMENT '金额',
 * buyer_id int(11) NOT NULL COMMENT '买家id',
 * buyer_name varchar(255) NOT NULL COMMENT '买家名称',
 * order_id int(11) NOT NULL COMMENT '关联订单id',
 * order_no varchar(255) NOT NULL COMMENT '订单编号',
 * validity_period int(11) NOT NULL COMMENT '有效期/天(0表示不限制)',
 * start_time int(11) NOT NULL COMMENT '有效期开始时间',
 * end_time int(11) NOT NULL COMMENT '有效期结束时间',
 * use_number int(11) NOT NULL COMMENT '使用次数',
 * confine_use_number int(11) NOT NULL COMMENT '限制使用次数',
 *
 * @author Administrator
 *        
 */
class NsVirtualGoodsModel extends BaseModel
{

    protected $table = 'ns_virtual_goods';

    protected $rule = [];

    protected $msg = [];
}