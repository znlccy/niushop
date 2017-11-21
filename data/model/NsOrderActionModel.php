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
 * 订单操作表
 *  action_id int(11) NOT NULL AUTO_INCREMENT COMMENT '动作id',
 order_id int(11) NOT NULL COMMENT '订单id',
 action varchar(255) NOT NULL DEFAULT '' COMMENT '动作内容',
 uid int(11) NOT NULL DEFAULT 0 COMMENT '操作人id',
 user_name varchar(50) NOT NULL DEFAULT '' COMMENT '操作人',
 order_status int(11) NOT NULL COMMENT '订单大状态',
 order_status_text varchar(255) NOT NULL DEFAULT '' COMMENT '订单状态名称',
 action_time datetime NOT NULL COMMENT '操作时间',
 PRIMARY KEY (action_id)
 */
class NsOrderActionModel extends BaseModel {

    protected $table = 'ns_order_action';
    protected $rule = [
        'action_id'  =>  '',
    ];
    protected $msg = [
        'action_id'  =>  '',
    ];

}