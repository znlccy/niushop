<?php
/**
 * ModuleModel.php
 *
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
 * 系统模块表
 * 
 * @author Administrator
 *        
 */
class ModuleModel extends BaseModel
{

    protected $table = 'sys_module';

    protected $rule = [
        'module_id' => '',
        'url' => 'no_html_parse'
    ];

    protected $msg = [
        'module_id' => '',
        'url' => ''
    ];

    /**
     * 通过模块方法查询权限id
     * 
     * @param unknown $controller            
     * @param unknown $action            
     * @return unknown
     */
    public function getModuleIdByModule($controller, $action)
    {
        $condition = array(
            'controller' => $controller,
            'method' => $action,
            'module' => \think\Request::instance()->module()
        );
        $count = $this->where($condition)->count('module_id');
        if ($count > 1) {
            $condition = array(
                'module' => \think\Request::instance()->module(),
                'controller' => $controller,
                'method' => $action,
                'pid' => array(
                    '<>',
                    0
                )
            );
        }
        $res = $this->where($condition)->find();
        return $res;
    }

    /**
     * 查询权限节点的根节点
     * 
     * @param unknown $module_id            
     */
    public function getModuleRoot($module_id)
    {
        $root_id = $module_id;
        $pid = $this->getInfo([
            'module_id' => $module_id
        ], 'pid');
        $pid = $pid['pid'];
        if (empty($pid)) {
            return 0;
        }
        while ($pid != 0) {
            $module = $this->getInfo([
                'module_id' => $pid
            ], 'pid, module_id');
            $root_id = $module['module_id'];
            $pid = $module['pid'];
        }
        return $root_id;
    }

    /**
     * 通过权限id组查询权限列表
     * 
     * @param unknown $list_id_arr            
     */
    public function getAuthList($pid)
    {
        $contdition = array(
            'pid' => $pid,
            'is_menu' => 1,
            'module' => \think\Request::instance()->module()
        );
        $list = $this->where($contdition)
            ->order("sort")
            ->column('module_id,module_name,controller,method,pid,url,is_menu,is_dev,icon_class,is_control_auth');
        return $list;
    }

    /**
     * 查询当前模块的上级ID
     * 
     * @param unknown $module_id            
     */
    public function getModulePid($module_id)
    {
        $pid = $this->get($module_id);
        return $pid['pid'];
    }
}