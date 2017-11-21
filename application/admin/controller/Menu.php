<?php
/**
 * Auth.php
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
namespace app\admin\controller;

use think\Controller;

/**
 * 菜单
 */
class Menu extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function addonmenu()
    {
        $this->getThreeLevelModule();//三级菜单
        $addons = request()->param('addons'); // 插件名称
        $params = request()->get('params', ''); // 插件参数
        $this->assign('params', json_decode($params, true));
        $this->assign('hook_name', $addons);
        return view($this->style . 'Menu/addonmenu');
    }
}