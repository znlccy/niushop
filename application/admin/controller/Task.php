<?php
/**
 * Login.php
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
/**
 * 后台登录控制器
 */
namespace app\admin\controller;

\think\Loader::addNamespace('data', 'data/');
use data\service\Events;
use think\Controller;
use think\Log;
use data\service\Upgrade;
use data\service\Config;

/**
 * 执行定时任务
 * 
 * @author Administrator
 *        
 */
class Task extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 加载执行任务
     */
    public function load_task()
    {
        $event = new Events();
        $retval_order_close = $event->ordersClose();
        $retval_mansong_operation = $event->mansongOperation();
        $retval_discount_operation = $event->discountOperation();
        $retval_order_complete = $event->ordersComplete();
        $retval_order_autodeilvery = $event->autoDeilvery();
        $retval_auto_coupon_close = $event->autoCouponClose();
        Log::write('检测自动收货' . $retval_order_autodeilvery);
        Log::write($retval_auto_coupon_close.'个优惠券已过期');
    }
    /**
     * 当前用户是否授权
     */
    public function copyRightIsLoad(){
        $upgrade=new Upgrade();
        $is_load=$upgrade->isLoadCopyRight();
        $result=array(
            "is_load"=>$is_load,
        );
        $bottom_info=array();
        if($is_load==0){
            $config=new Config();
            $bottom_info=$config->getCopyrightConfig(0);
            $bottom_info["copyright_logo"]=__ROOT__.'/'.$bottom_info["copyright_logo"];
        }
        $result["bottom_info"]=$bottom_info;
        $result["default_logo"]=__ROOT__."/public/static/blue/img/logo.png";
        return $result;
    }
}
