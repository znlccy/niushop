<?php
/**
 * IExtend.php
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
 * 扩展接口 （插件与钩子）
 */
interface IExtend
{
    /**
     * 获取插件列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
     */
    function getAddonsList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*');
    /**
     * 安装插件
     * @param unknown $name
     * @param unknown $title
     * @param unknown $description
     * @param unknown $status
     * @param unknown $config
     * @param unknown $author
     * @param unknown $version
     * @param unknown $has_adminlist
     * @param unknown $has_addonslist
     */
    function addAddons($name, $title, $description, $status, $config, $author, $version, $has_adminlist, $has_addonslist, $config_hook, $content);
    /**
     * 更新插件里的所有钩子对应的插件
     * @param unknown $addons_name
     */
    function updateHooks($addons_name);
    /**
     * 更新单个钩子处的插件
     * @param unknown $hook_name
     * @param unknown $addons_name
     */
    function updateAddons($hook_name, $addons_name);
    /**
     * 去除插件所有钩子里对应的插件数据
     * @param unknown $addons_name
     */
    function removeHooks($addons_name);
    /**
     * 去除单个钩子里对应的插件数据
     * @param unknown $hook_name
     * @param unknown $addons_name
     */
    function removeAddons($hook_name, $addons_name);
    /**
     * 删除插件
     * @param unknown $condition
     */
    function deleteAddons($condition);
    /**
     * 获取单条插件信息
     * @param unknown $condition
     */
    function getAddonsInfo($condition, $field = '*');
    /**
     * 修改插件状态
     * @param unknown $id
     * @param unknown $status
     */
    function updateAddonsStatus($id, $status);
    /**
     * 获取某插件类型下 的插件列表
     */
    function getPluginList($id);
    /**
     * 获取钩子列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
     */
    function getHooksList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*');
    /**
     * 添加钩子
     * @param unknown $name
     * @param unknown $description
     * @param unknown $type
     */
    function addHooks($name, $description, $type);
    /**
     * 修改钩子
     * @param unknown $id
     * @param unknown $name
     * @param unknown $description
     * @param unknown $type
     * @param unknown $addons
     */
    function editHooks($id, $name, $description, $type, $addons);
    /**
     * 删除钩子
     * @param unknown $id
     */
    function deleteHooks($id);
    /**
     * 获取钩子详情
     * @param unknown $condition
     * @param string $field
     */
    function getHoodsInfo($condition, $field = '*');
    /**
     * 修改插件配置
     * @param unknown $condition
     * @param unknown $config
     */
    function updateAddonsConfig($condition, $config);
}
