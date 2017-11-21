<?php
/**
 * Event.php
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
namespace app\wap\controller;

use data\service\Events;
use think\Controller;
\think\Loader::addNamespace('data', 'data/');

class Event extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 自动执行函数
     */
    public function index()
    {
        $event = new Events();
        $retval_order_close = $event->ordersClose();
        echo "订单关闭";
        var_dump($retval_order_close);
        $retval_mansong_operation = $event->mansongOperation();
        echo "满减送操作";
        var_dump($retval_mansong_operation);
        $retval_discount_operation = $event->discountOperation();
        echo "限时折扣操作";
        var_dump($retval_discount_operation);
        $retval_order_complete = $event->ordersComplete();
        echo "订单完成";
        var_dump($retval_order_complete);
    }
}