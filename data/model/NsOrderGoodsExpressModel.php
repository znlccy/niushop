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
 * 商品订单物流信息表（多次发货）
 *  id int(11) NOT NULL AUTO_INCREMENT,
 order_id int(11) NOT NULL COMMENT '订单id',
 order_goods_id_array varchar(255) NOT NULL COMMENT '订单项商品组合列表',
 express_name varchar(50) NOT NULL DEFAULT '' COMMENT '包裹名称  （包裹- 1 包裹 - 2）',
 shipping_type tinyint(4) NOT NULL COMMENT '发货方式1 需要物流 0无需物流',
 express_company_id int(11) NOT NULL COMMENT '快递公司id',
 express_no varchar(50) NOT NULL COMMENT '运单编号',
 shipping_time datetime NOT NULL COMMENT '发货时间',
 uid int(11) NOT NULL COMMENT '用户id',
 user_name varchar(50) NOT NULL COMMENT '用户名',
 memo varchar(255) NOT NULL COMMENT '备注',
 */
class NsOrderGoodsExpressModel extends BaseModel {

    protected $table = 'ns_order_goods_express';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];

}