<?php
/**
 * Address.php
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
 * @date : 2015.4.24
 * @version : v1.0.0.0
 */
namespace data\service;
use data\api\IDbQuery as IDbQuery;
use think\Db;
/**
 * sql 执行表
 */
class DbQuery extends BaseService implements IDbQuery
{
	/* (non-PHPdoc)
     * @see \data\api\IDbQuery::repair()
     */
    public function repair($tables)
    {
        // TODO Auto-generated method stub
        if($tables) {
            Db::startTrans();
            try{
                if(is_array($tables)){
                    $tables = implode('`,`', $tables);
                    $list = Db::query("REPAIR TABLE `{$tables}`");
                } else {
                    $list = Db::query("REPAIR TABLE `{$tables}`");
                }
                Db::commit();
                return showMessage("数据表修复完成", 1);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return showMessage("数据表修复失败");
            }
        } else {
            return showMessage("请指定要修复的表");
        }
    }
	/* (non-PHPdoc)
     * @see \data\api\IDbQuery::optimize()
     */
    public function optimize($tables)
    {
        // TODO Auto-generated method stub
        if($tables) {
            if(is_array($tables)){
                $tables = implode('`,`', $tables);
                $list = Db::query("OPTIMIZE TABLE `{$tables}`");
                if($list){
                    
                    return showMessage("数据表优化完成", 1);
                } else {
                    return showMessage("数据表优化失败");
                }
            } else {
                $list = Db::query("OPTIMIZE TABLE `{$tables}`");
                if($list){
                    return showMessage("数据表优化完成", 1);
                } else {
                    return showMessage("数据表优化失败");
                }
                }
        } else {
            return showMessage("请指定要优化的表");
        }
    }
    
    private function sql_execute($sql, $is_debug){
        
        if(trim($sql) != ''){
            $sql = str_replace("\r\n", "\n", $sql);
            $sql = str_replace("\r", "\n", $sql);
            
            $sql_array = explode(";\n", $sql);
            if(!$is_debug){
                Db::startTrans();
            }
            try {
                foreach ($sql_array as $item) {
                    if($is_debug){
                        Db::startTrans();
                    }
                    $querySql = trim($item);
                    if($querySql != ''){
                        $result = @Db::execute($querySql.";");
                        if($is_debug){
                            Db::rollback();
                        }
                    }
                }
                if(!$is_debug){
                    Db::commit();
                }
                return showMessage("执行完毕", 1);
            } catch (\Exception $e) {
                Db::rollback();
               return showMessage($e->getMessage());
            }
        }else{
            return showMessage("请填写要执行的sql语句！");
        }
    }
    /**
     * sql 执行
     * @param unknown $sql
     * @return mixed|multitype:integer string
     */
    public function sqlQuery($sql){
        $result=$this->sql_execute($sql, true);
        if($result["status"]==1){
            $result=$this->sql_execute($sql, false);
        }
        return $result;
    }
    public function yujjia($sql){
        Db::startTrans();
        try {
            $result = Db::query($sql);
            Db::rollback();
            return "1";
        } catch (\Exception $e) {
            Db::rollback();
            return showMessage($e->getMessage());;
        }
        
    }
    
    /**
     * 查询所有表
     * (non-PHPdoc)
     * @see \data\api\IDbQuery::getDatabaseList()
     */
    public function getDatabaseList()
    {
        // TODO Auto-generated method stub
        $databaseList = Db::query("SHOW TABLE STATUS");
        return $databaseList;
    }


     
}