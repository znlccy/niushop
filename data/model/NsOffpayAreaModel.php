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
 * 货到付款支持地区表
 *    
 * shop_id int(11) DEFAULT NULL COMMENT '店铺Id',
   area_id text DEFAULT NULL COMMENT '省市县Id组合'
 * @author Administrator
 *
 */

class NsOffpayAreaModel extends BaseModel
{
    protected $table = 'ns_offpay_area';
    protected $rule = [
        'shop_id'  =>  '',
    ];
    protected $msg = [
        'shop_id'  =>  '',
    ];
}