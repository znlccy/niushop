<?php
/**
 * ISupplier.php
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
 * 供货商接口
 */
interface ISupplier
{
    /**
     * 供货商列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
     */
    function getSupplierList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '');
    /**
     * 添加供货商
     * @param unknown $uid
     * @param unknown $supplier_name
     * @param unknown $desc
     */
    function addSupplier($uid, $supplier_name, $desc);
    /**
     * 修改供货商
     * @param unknown $supplier_id
     * @param unknown $supplier_name
     * @param unknown $desc
     */
    function updateSupplier($supplier_id, $supplier_name, $desc);
    /**
     * 删除供货商
     * @param unknown $supplier_id
     */
    function deleteSupplier($supplier_id);
    /**
     * 获取单条供货商详情
     * @param unknown $supplier_id
     */
    function getSupplierInfo($supplier_id);
}
