<?php
/**
 * Express.php
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
namespace data\service;

/**
 * 扩展（插件与钩子）
 */
use data\service\BaseService as BaseService;
use data\api\IExtend as IExtend;
use data\model\SysAddonsModel;
use data\model\SysHooksModel;
use data\model\BaseModel;
use think\Db;


class Extend extends BaseService implements IExtend
{

    /**
     * (non-PHPdoc)
     * @see \data\api\IExtend::getAddonsList()
     */
    public function getAddonsList($page_index = 1, $page_size = PAGESIZE, $condition = '', $order = '', $field = '*'){
        $sys_addons = new SysAddonsModel();
        if($page_size == 0){
            $page_size = PAGESIZE;
        }
        if (!$addon_dir)
            $addon_dir = ADDON_PATH;
        $dirs = array_map ( 'basename', glob ( $addon_dir . '*', GLOB_ONLYDIR ) );
        if ($dirs === FALSE || ! file_exists ( $addon_dir )) {
            $this->error = '插件目录不可读或者不存在';
            return FALSE;
        }
        $addons = array ();
        
        $where ['name'] = array ('in', $dirs);
        $list = $sys_addons->getQuery($where, '*', 'create_time desc');
        foreach ( $list as $key => $value ) {
            $list [$key] = $value->toArray ();  //对象转数组
        }
        foreach ( $list as $addon ) {
            $addon ['uninstall'] = 0;
            $addons [$addon ['name']] = $addon;
        }
        
        foreach ( $dirs as $value ) {
            if (! isset ( $addons [$value] )) {
                $class = get_addon_class ( $value );
                if (! class_exists ( $class )) { // 实例化插件失败忽略执行
                    trace($class);
                    \think\Log::record ( '插件' . $value . '的入口文件不存在！' );
                    continue;
                }
                $obj = new $class ();
                $addons [$value] = $obj->info;
                if ($addons [$value]) {
                    $addons [$value] ['uninstall'] = 1;
                    unset ( $addons [$value] ['status'] );
                }
            }
        }
        $addons = $this->list_sort_by ( $addons, 'uninstall', 'desc' );

        $new_array = [];
        //总条数
        $total_count = count($addons);
        //总页数
        $page_count = ceil($total_count/$page_size);
        //获取当前数组键值开始与结束
        $key_start = ($page_index-1) * $page_size;
        $key_end = $page_index * $page_size - 1;
        for ($i = $key_start; $i <= $key_end; $i++){
            if(!empty($addons[$i])){
                $data[$i] = $addons[$i];
            }
        }
        $new_array['data'] = $data;
        $new_array['total_count'] = $total_count;
        $new_array['page_count'] = $page_count;
        return $new_array;
    }
    
    public function addAddons($name, $title, $description, $status, $config, $author, $version, $has_adminlist, $has_addonslist, $config_hook, $content){
        $sys_addons = new SysAddonsModel();
        $data = array(
            'name' => $name, 
            'title' => $title, 
            'description' => $description, 
            'status' => $status, 
            'config' => $config, 
            'author' => $author, 
            'version' => $version, 
            'has_adminlist' => $has_adminlist, 
            'has_addonslist' => $has_addonslist,
            'config_hook' => $config_hook,
            'content' => $content,
            'create_time' => time(),
        );
        $res = $sys_addons->save($data);
        return $sys_addons->id;
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IExtend::updateHooks()
     */
    public function updateHooks($addons_name){
        $sys_hooks = new SysHooksModel();
        $addons_class = get_addon_class($addons_name);//获取插件名
        if(!class_exists($addons_class)){
            $this->error = "未实现{$addons_name}插件的入口文件";
            return false;
        }
        $methods = get_class_methods($addons_class);
        $hooks = $sys_hooks->column('name');
        $common = array_intersect($hooks, $methods);//对比返回交集
        if(!empty($common)){
            foreach ($common as $hook) {
                $flag = $this->updateAddons($hook, array($addons_name));
                if(false === $flag){
                    $this->removeHooks($addons_name);
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IExtend::updateAddons()
     */
    public function updateAddons($hook_name, $addons_name){
        $sys_hooks = new SysHooksModel();
        $hooks_info = $sys_hooks->getInfo(['name' => $hook_name], 'addons');
        $o_addons = $hooks_info['addons'];
        if($o_addons){
            $o_addons = explode(',', $o_addons);
        }
        if($o_addons){
            $addons = array_merge($o_addons, $addons_name);
            $addons = array_unique($addons);
        }else{
            $addons = $addons_name;
        }
        $addons = implode(',', $addons);
        if($o_addons){
            $o_addons = implode(',', $o_addons);
        }
        $res = $sys_hooks->save(['addons' => $addons], ['name' => $hook_name]);
        if(false === $res){
            $sys_hooks->save(['addons' => $o_addons], ['name' => $hook_name]);
        }
        return $res;
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IExtend::removeHooks()
     */
    public function removeHooks($addons_name){
        $sys_hooks = new SysHooksModel();
        $addons_class = get_addon_class($addons_name);
        if(!class_exists($addons_class)){
            return false;
        }
        $methods = get_class_methods($addons_class);
        $hooks = $sys_hooks->column('name');
        $common = array_intersect($hooks, $methods);
        if($common){
            foreach ($common as $hook) {
                $flag = $this->removeAddons($hook, array($addons_name));
                if(false === $flag){
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IExtend::removeAddons()
     */
    public function removeAddons($hook_name, $addons_name){
        $sys_hooks = new SysHooksModel();
        $hooks_info = $sys_hooks->getInfo(['name' => $hook_name], 'addons');
        $o_addons = explode(',', $hooks_info['addons']);
        if($o_addons){
            $addons = array_diff($o_addons, $addons_name);
        }else{
            return true;
        }
        $addons = implode(',', $addons);
        $o_addons = implode(',', $o_addons);
        $flag = $sys_hooks->save(['addons' => $addons], ['name' => $hook_name]);
        if(false === $flag){
            $sys_hooks->save(['addons' => $o_addons], ['name' => $hook_name]);
        }
        return $flag;
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IExtend::deleteAddons()
     */
    public function deleteAddons($condition){
        $sys_addons = new SysAddonsModel();
        return $sys_addons->destroy($condition);
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IExtend::getAddonsInfo()
     */
    public function getAddonsInfo($condition, $field = '*'){
        $sys_addons = new SysAddonsModel();
        return $sys_addons->getInfo($condition, $field);
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IExtend::updateAddonsStatus()
     */
    public function updateAddonsStatus($id, $status){
        $sys_addons = new SysAddonsModel();
        return $sys_addons->save(['status' => $status], ['id' => $id]);
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IExtend::getPluginList()
     */
    public function getPluginList($id){
        $sys_addons = new SysAddonsModel();
        $addons_info = $sys_addons->getInfo(['id' => $id], 'name');
        
        $addon_name = $addons_info['name'];
        
        $addon_dir = ADDON_PATH . $addon_name . '/';
        
        $dirs = array_map ( 'basename', glob ( $addon_dir . '*', GLOB_ONLYDIR ) );
        if ($dirs === FALSE || ! file_exists ( $addon_dir )) {
            $this->error = '插件目录不可读或者不存在';
            return FALSE;
        }
        $addon_type_class = get_addon_class($addon_name);
        if (! class_exists ( $addon_type_class )) { // 实例化插件失败忽略执行
            trace($addon_type_class);
            \think\Log::record ( '插件' . $value . '的入口文件不存在！' );
            return false;
//             continue;
        }
        $obj = new $addon_type_class ();
        $table = $obj->table;
        
        $addons = array (); //已安装的数组
//         var_dump($dirs);
        $where ['name'] = array ('in', $dirs);
        $list = Db::table("$table")->where($where)->select();
        foreach ( $list as $addon ) {
            $addon ['uninstall'] = 0;
            $addons [$addon ['name']] = $addon;
        }
        
        foreach ( $dirs as $value ) {
            if (! isset ( $addons [$value] ) && ($value != 'core')) {
                //不在已安装插件数组中
                //读取配置文件
                $temp_arr = array();
                if (is_file($addon_dir.$value.'/config.php')) {
                    $temp_arr = include $addon_dir.$value.'/config.php';
                }
                $addons [$value] = $temp_arr;
            }
        }
        $addons = $this->list_sort_by($addons, 'id');
        return $addons;
    }
    
    
    
    
    
    
    
    
    
    
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IExtend::getHooksList()
     */
    public function getHooksList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*'){
        $sys_hooks = new SysHooksModel();
        return $sys_hooks->pageQuery($page_index, $page_size, $condition, $order, $field);
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IExtend::getHoodsInfo()
     */
    public function getHoodsInfo($condition, $field = '*'){
        $sys_hooks = new SysHooksModel();
        $info = $sys_hooks->getInfo($condition, $field);
        return $info;
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IExtend::addHooks()
     */
    public function addHooks($name, $description, $type){
        $sys_hooks = new SysHooksModel();
        $data = array(
            'name' => $name,
            'description' => $description,
            'type' => $type,
            'update_time' => time(),
        );
        $sys_hooks->save($data);
        return $sys_hooks->id;
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IExtend::editHooks()
     */
    public function editHooks($id, $name, $description, $type, $addons){
        $sys_hooks = new SysHooksModel();
        $data = array(
            'name' => $name,
            'description' => $description,
            'type' => $type,
//             'addons' => $addons,
            'update_time' => time(),
        );
        $res = $sys_hooks->save($data, ['id' => $id]);
        return $res;
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IExtend::deleteHooks()
     */
    public function deleteHooks($id){
        $sys_hooks = new SysHooksModel();
        return $sys_hooks->destroy($id);
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IExtend::updateAddonsConfig()
     */
    public function updateAddonsConfig($condition, $config){
        $sys_addons = new SysAddonsModel();
        return $sys_addons->save(['config' => $config], $condition);
    }
    
    
    
    
    
    
    /**
     * 重新排序
     * @param unknown $list
     * @param unknown $field
     * @param string $sortby
     */
    protected function list_sort_by($list,$field, $sortby='asc') {
        if(is_array($list)){
            $refer = $resultSet = array();
            foreach ($list as $i => $data)
                $refer[$i] = &$data[$field];
            switch ($sortby) {
                case 'asc': // 正向排序
                    asort($refer);
                    break;
                case 'desc':// 逆向排序
                    arsort($refer);
                    break;
                case 'nat': // 自然排序
                    natcasesort($refer);
                    break;
            }
            foreach ( $refer as $key=> $val)
                $resultSet[] = &$list[$key];
            return $resultSet;
        }
        return false;
    }
}