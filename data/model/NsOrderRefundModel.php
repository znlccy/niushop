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
 * 订单退款记录表
 *  id int(11) NOT NULL DEFAULT 0 COMMENT 'id',
 order_goods_id int(11) NOT NULL COMMENT '订单商品表id',
 refund_status varchar(255) NOT NULL COMMENT '操作状态

 流程状态(refund_status)	状态名称(refund_status_name)	操作时间
 1	买家申请	发起了退款申请,等待卖家处理
 2	等待买家退货	卖家已同意退款申请,等待买家退货
 3	等待卖家确认收货	买家已退货,等待卖家确认收货
 4	等待卖家确认退款	卖家同意退款
 0	退款已成功	卖家退款给买家，本次维权结束
 -1	退款已拒绝	卖家拒绝本次退款，本次维权结束
 -2	退款已关闭	主动撤销退款，退款关闭
 -3	退款申请不通过	拒绝了本次退款申请,等待买家修改
 ',
 action varchar(255) NOT NULL COMMENT '退款操作内容描述',
 action_way tinyint(4) NOT NULL DEFAULT 0 COMMENT '操作方 1 买家 2 卖家',
 action_userid varchar(255) NOT NULL DEFAULT '0' COMMENT '操作人id',
 action_username varchar(255) NOT NULL DEFAULT '' COMMENT '操作人姓名',
 action_time datetime DEFAULT NULL COMMENT '操作时间',
 */
class NsOrderRefundModel extends BaseModel {

    protected $table = 'ns_order_refund';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];

}