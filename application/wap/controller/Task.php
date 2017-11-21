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
namespace app\wap\controller;

use data\service\Config;
use data\service\Events;
use data\service\Upgrade;
use data\service\WebSite;
use think\Cache;
use think\Controller;
\think\Loader::addNamespace('data', 'data/');

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
        $this->autoTask();
        $this->minutesTask();
        $this->hoursTask();
    }

    /**
     * 立即执行事件
     */
    public function autoTask()
    {
        $event = new Events();
        $retval_mansong_operation = $event->mansongOperation();
        $retval_discount_operation = $event->discountOperation();
        $retval_auto_coupon_close = $event->autoCouponClose();
    }

    /**
     * 每分钟执行事件
     */
    public function minutesTask()
    {
        $time = time() - 60;
        $cache = Cache::get("niushop_minutes_task");
        if (! empty($cache) && $time < $cache) {
            return 1;
        } else {
            $event = new Events();
            $retval_order_close = $event->ordersClose();
            $retval_order_complete = $event->ordersComplete();
            Cache::set("niushop_minutes_task", time());
            return 1;
        }
    }

    /**
     * 每小时执行事件
     */
    public function hoursTask()
    {
        $time = time() - 60;
        $cache = Cache::get("niushop_hours_task");
        if (! empty($cache) && $time < $cache) {
            return 1;
        } else {
            $event = new Events();
            $retval_order_autodeilvery = $event->autoDeilvery();
            Cache::set("niushop_hours_task", time());
            return 1;
        }
    }

    /**
     * 当前用户是否授权
     */
    public function copyRightIsLoad()
    {
        $upgrade = new Upgrade();
        $is_load = $upgrade->isLoadCopyRight();
        $website = new WebSite();
        $web_site_info = $website->getWebSiteInfo();
        $result = array(
            "is_load" => $is_load
        );
        $bottom_info = array();
        if ($is_load == 0) {
            $config = new Config();
            $bottom_info = $config->getCopyrightConfig(0);
            $bottom_info["copyright_logo"] = $bottom_info["copyright_logo"];
        }
        if (! empty($web_site_info["web_icp"])) {
            $bottom_info['copyright_meta'] = $web_site_info["web_icp"];
        } else {
            $bottom_info['copyright_meta'] = '';
        }
        
        $result["bottom_info"] = $bottom_info;
        $result["default_logo"] = "/blue/img/logo.png";
        return $result;
    }
}
