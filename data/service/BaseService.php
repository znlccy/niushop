<?php
/**
 * BaseService.php
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
use \think\Session as Session;
use think\Cache;
use think\Request;
class BaseService
{
    protected $uid;
    protected $instance_id;  //店铺id
    protected $is_admin;
    protected $module_id_array;
    protected $instance_name;
    public function __construct(){
        $this->init();
    }
    
 /**
     * 初始化数据
     */
    private function init(){
        $model = $this->getRequestModel();
        $this->uid = Session::get($model.'uid');
        $this->instance_id = 0;
        $this->is_admin = Session::get($model.'is_admin');
        $this->module_id_array = Session::get($model.'module_id_array');
        $this->instance_name = Session::get($model.'instance_name');
        $this->is_member = Session::get($model.'is_member');
        $this->is_system = Session::get($model.'is_system');
    }
    /**
     * 把返回的数据集转换成Tree
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     * @return array
     */
    function listToTree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
          for($k = 0;$k<count($list);$k++)
          {
              $list[$k][$child] = array();
          }
        // 创建Tree  
        for($i = count($list)-1;$i>=0;$i--)
        {
        for($j = 0;$j<count($list);$j++)
        {
            if($list[$j][$pk] == $list[$i][$pid])
                {
                    if(empty($list[$j][$child]))
                    {
                        $list[$j][$child][0] = $list[$i];
                    }else{
                        $list[$j][$child] = array_push($list[$j][$child], $list[$i]);
                    }
                    
               
            }
        }
        
        }
        return $list;
    }
    /**
     * 添加缓存key值
     * @param unknown $key
     * @param unknown $value
     */
    public function addCacheKeyTag($key,$tag)
    {
        $key_list = Cache::get($key);
        if(empty($key_list))
        {
            $key_list = array();
         
        }
        if(!in_array($tag, $key_list))
        {
            $key_list[] = $tag;
            Cache::set($key, $key_list);
        }
    }
    /**
     * 清除对应key相关tag的所有缓存
     * @param unknown $key
     */
    public function clearKeyCache($key)
    {
        $key_list = Cache::get($key);
        if(!empty($key_list))
        {
            foreach ($key_list as $k => $v)
            {
                Cache::set($v, '');
            }
        }
        
    }
    /**
     * 获取model
     * @return Ambigous <string, \think\Request>
     */
    public function getRequestModel(){
        $model = Request::instance()->module();
        if($model == 'shop'||$model == 'wap')
        {
            $model = 'app';
        }
        return $model;
    }
    
}