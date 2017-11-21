<?php
/**
 * IAddress.php
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
 * 数据库执行接口
 */
interface IDbQuery
{
    /**
     * 修复表
     * @param unknown $tables
     */
    function repair($tables);
    
    /**
     * 优化表
     */
    function optimize($tables);
    /**
     * 执行sql
     * @param unknown $sql
     */
    function sqlQuery($sql);
    /**
     * 查询所有表
     */
    function getDatabaseList();
}
