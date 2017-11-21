<?php
/**
 * IAuthGroup.php
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
 * 系统用户组以及权限管理
 */
interface IAuthGroup
{
    /**
     * 获取系统用户组
     * @param unknown $where
     * @param unknown $order
     * @param unknown $page_size
     * @param unknown $page_index
     */
    function getSystemUserGroupList($page_index, $page_size=0, $order='', $where='' );
    
    /**
     * 添加系统用户组
     * @param unknown $group_id
     * @param unknown $group_name
     * @param unknown $is_system
     * @param unknown $module_id_array
     * @param unknown $desc
     */
    function addSystemUserGroup($group_id, $group_name, $is_system, $module_id_array, $desc);
    
    /**
     * 修改系统用户组
     * @param unknown $group_id
     * @param unknown $group_name
     * @param unknown $is_system
     * @param unknown $module_id_array
     * @param unknown $desc
     */
    function updateSystemUserGroup($group_id, $group_name, $group_status, $is_system, $module_id_array, $desc);
    
    /**
     * 修改用户组的状态
     * @param unknown $group_id
     * @param unknown $group_status
     */
    function ModifyUserGroupStatus($group_id, $group_status);
    /**
     * 删除用户组
     * @param unknown $group_id
     */
    function deleteSystemUserGroup($group_id);
    /**
     * 获取用户组详情
     * @param unknown $group_id
     */
    function getSystemUserGroupDetail($group_id);
    /**
     * 查询所有用户组
     */
    function getSystemUserGroupAll($where);
}