<?php
/**
 * IWebSite.php
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
namespace data\api;

/**
 * 系统网站基本信息     
 */
interface IWebsite
{

    /**
     * 获取版本号
     */
    function getVersion();

    /**
     * 获取网站信息
     * 
     * @param string $field            
     */
    function getWebSiteInfo();

    /**
     * 修改网站信息
     * 
     * @param unknown $title            
     * @param unknown $logo            
     * @param unknown $web_desc            
     * @param unknown $key_words            
     * @param unknown $web_icp            
     * @param unknown $web_style            
     * @param unknown $web_qrcode            
     */
    function updateWebSite($title, $logo, $web_desc, $key_words, $web_icp, $web_style_pc,$web_style_admin, $visit_pattern, $web_qrcode, $web_url, $web_phone, $web_email, $web_qq, $web_weixin, $web_address,$web_status,$wap_status,$third_count,$close_reason, $web_popup_title, $web_wechat_share_logo, $web_gov_record, $web_gov_record_url);

    /**
     * 添加系统模块
     * 
     * @param unknown $module_id            
     * @param unknown $module_name            
     * @param unknown $controller
     *            控制器名
     * @param unknown $method
     *            方法名
     * @param unknown $pid
     *            上级模块ID
     * @param unknown $url
     *            链接url
     * @param unknown $is_menu
     *            是否是菜单
     * @param unknown $is_dev
     *            是否开发者模式可见
     * @param unknown $sort
     *            排序号
     * @param unknown $desc
     *            备注
     */
    function addSytemModule($module_name, $controller, $method, $pid, $url, $is_menu, $is_dev, $sort, $module_picture, $desc, $icon_class, $is_control_auth);

    /**
     * 修改系统模块
     * 
     * @param unknown $module_name            
     * @param unknown $controller            
     * @param unknown $method            
     * @param unknown $pid            
     * @param unknown $url            
     * @param unknown $is_menu            
     * @param unknown $is_dev            
     * @param unknown $sort            
     * @param unknown $desc            
     */
    function updateSystemModule($module_id, $module_name, $controller, $method, $pid, $url, $is_menu, $is_dev, $sort, $module_picture, $desc, $icon_class, $is_control_auth);

    /**
     * 删除系统模块
     * 
     * @param unknown $module_id            
     */
    function deleteSystemModule($module_id);

    /**
     * 获取系统模块
     * 
     * @param unknown $module_id            
     */
    function getSystemModuleInfo($module_id, $field = '*');

    /**
     * 获取系统模块列表
     * 
     * @param unknown $where            
     * @param unknown $order            
     * @param unknown $page_size            
     * @param unknown $page_index            
     */
    function getSystemModuleList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*');

    /**
     * 根据当前实例查询权限列表
     */
    function getInstanceModuleQuery();

    /**
     * 添加用户实例
     * 
     * @param unknown $uid            
     * @param unknown $type            
     */
    function addSystemInstance($uid, $instance_name, $type);

    /**
     * 修改系统实例
     */
    function updateSystemInstance();

    /**
     * 获取系统实例
     * 
     * @param unknown $instance_id            
     */
    function getSystemInstance($instance_id);

    /**
     * 查询系统实例列表
     * 
     * @param unknown $where            
     * @param unknown $order            
     * @param unknown $page_size            
     * @param unknown $page_index            
     */
    function getSystemInstanceList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*');

    /**
     * 根据模块和方案查询权限
     * 
     * @param unknown $controller            
     * @param unknown $action            
     */
    function getModuleIdByModule($controller, $action);

    /**
     * 获取下级列表子项
     * 
     * @param unknown $pid            
     */
    function getModuleListByParentId($pid);

    /**
     * 获取当前module的根节点以及二级节点
     * 
     * @param unknown $module_id            
     */
    function getModuleRootAndSecondMenu($module_id);

    /**
     * 修改模块单个字段 根据主键id
     * 
     * @param unknown $module_id
     *            主键
     * @param unknown $field_name
     *            字段名称
     * @param unknown $field_value
     *            字段值
     */
    function ModifyModuleField($module_id, $field_name, $field_value);

    /**
     * 获取模板样式
     */
    function getWebStyle();

    /**
     * 获取模板列表
     */
    function getWebStyleList($condition);

    /**
     * 获取平台信息
     */
    function getWebDetail();
    /**
     * 获取伪静态配置列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    function getUrlRouteList($page_index, $page_size, $condition, $order);
    /**
     * 添加伪静态规则
     * @param unknown $rule
     * @param unknown $route
     * @param unknown $is_open
     */
    function addUrlRoute($rule, $route, $is_open, $route_model=1, $remark);
    /**
     * 修改伪静态规则
     * @param unknown $routeid
     * @param unknown $rule
     * @param unknown $route
     * @param unknown $is_open
     * @param number $route_model
     */
    function updateUrlRoute($routeid, $rule, $route, $is_open,$route_model=1, $remark);
    
    /**
     * 获取伪静态规则详情
     * @param unknown $routeid
     */
    function getUrlRouteDetail($routeid);
    
    /**
     * 判断路由规则或者路由地址是否存在
     */
    function url_route_if_exists($type, $value);
    
    /**
     * 删除路由规则
     * @param unknown $routeid
     */
    function delete_url_route($routeid);

}

