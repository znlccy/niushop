<?php
/**
 * BaseModel.php
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

use think\Model;
use think\Db;
use think\Validate;
use think\Loader;

class BaseModel extends Model
{
    protected $error = 0;

    protected $table;
    
    protected $rule = [];
    
    protected $msg = [];
    
    protected $Validate;
    
    public function __construct($data = []){
        parent::__construct($data);
        $this->Validate = new Validate($this->rule, $this->msg);
        $this->Validate->extend('no_html_parse', function ($value, $rule) {
            return true;
        });  
    } 
    /**
     * 获取空模型
     */
    public function getEModel($tables)
    {
        $rs = Db::query('show columns FROM `' . config('database.prefix') . $tables . "`");
        $obj = [];
        if ($rs) {
            foreach ($rs as $key => $v) {
                $obj[$v['Field']] = $v['Default'];
                if ($v['Key'] == 'PRI')
                    $obj[$v['Field']] = 0;
            }
        }
        return $obj;
    }
    
    /**
     * 自动验证数据
     * @access protected
     * @param array $data 验证数据
     * @param mixed $rule 验证规则
     * @param bool  $batch 批量验证
     * @return bool
     */
//     protected function validateData($data, $rule = null, $batch = null)
//     {
//         $info = is_null($rule) ? $this->Validate : $rule;
    
//         if (!empty($info)) {
//             if (is_array($info)) {
//                 $validate = $this->Validate;// Loader::validate();
//                 $validate->rule($info['rule']);
//                 $validate->message($info['msg']);
               
//             } else {
//                 $name = is_string($info) ? $info : $this->name;
//                 if (strpos($name, '.')) {
//                     list($name, $scene) = explode('.', $name);
//                 }
//                 $validate = $this->Validate;// Loader::validate($name);
//                 if (!empty($scene)) {
//                     $validate->scene($scene);
//                 } 
//             }
//             $batch = is_null($batch) ? $this->batchValidate : $batch;
    
//             if (!$validate->batch($batch)->check($data)) {
//                 $this->error = $validate->getError();              
//                 if ($this->failException) {
//                     throw new ValidateException($this->error);
//                 } else {
//                     return false;
//                 }
//             }
//             $this->validate = null;
//         }
//         return true;
//     }

    public function save($data = [], $where = [], $sequence = null){
        $data = $this->htmlClear($data);
        $retval = parent::save($data, $where, $sequence);
        if(!empty($where))
        {
            //表示更新数据
            if($retval == 0)
            {
                if($retval !== false)
                {
                    $retval = 1;
                }
            }
        } 
//         $retval = ['code' => $code, 'message' => $this->getError()];
        return $retval;
    }
    
    public function ihtmlspecialchars($string) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = $this->ihtmlspecialchars($val);
            }
        } else {
            $string = preg_replace('/&amp;((#(d{3,5}|x[a-fa-f0-9]{4})|[a-za-z][a-z0-9]{2,5});)/', '&\1',
                str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
        }
        return $string;
    }
    
    protected function htmlClear($data){
        $rule =  $this->rule;
        $info = empty($rule) ? $this->Validate : $rule;
        foreach ($data as $k=>$v){
            if (!empty($info)) {
                if (is_array($info)) {
                    $is_Specialchars=$this->is_Specialchars($info, $k);
                    // 数据对象赋值
                    if($is_Specialchars){
                        $data[$k] = $this->ihtmlspecialchars($v);
                    }else{
                        $data[$k] = $v;
                    }
//                     foreach ($rule as $key => $value) {
//                         if(strcasecmp($value,"no_html_parse")!= 0){
//                             $data[$k] = $this->ihtmlspecialchars($v);
//                         }else{
//                             $data[$k] = $v;
//                         }
//                     }
                } else {
                    ;
                }
            }            
        }
        return $data;
    }
    
    /**
     * 判断当前k 是否在数组的k值中
     * @param unknown $rule
     * @param unknown $k
     */
    protected function is_Specialchars($rule, $k){
        $is_have=true;
        foreach ($rule as $key => $value) {
            if($key==$k){
                if(strcasecmp($value,"no_html_parse")!= 0){
                    $is_have=true;
                }else{
                    $is_have=false;
                }
            }
        }
        return $is_have;
    }
    
    /**
     * 数据库开启事务
     */
    public function startTrans()
    {
        Db::startTrans();
    }

    /**
     * 数据库事务提交
     */
    public function commit()
    {
        Db::commit();
    }

    /**
     * 数据库事务回滚
     */
    public function rollback()
    {
        Db::rollback();
    }

    /**
     * 列表查询
     *
     * @param unknown $page_index            
     * @param number $page_size            
     * @param string $order            
     * @param string $where            
     * @param string $field            
     */
    public function pageQuery($page_index, $page_size, $condition, $order, $field)
    {
        $count = $this->where($condition)->count();
        if ($page_size == 0) {
            $list = $this->field($field)
                ->where($condition)
                ->order($order)
                ->select();
            $page_count = 1;
        } else {
            $start_row = $page_size * ($page_index - 1);
            $list = $this->field($field)
                ->where($condition)
                ->order($order)
                ->limit($start_row . "," . $page_size)
                ->select();
            if ($count % $page_size == 0) {
                $page_count = $count / $page_size;
            } else {
                $page_count = (int) ($count / $page_size) + 1;
            }
        }
        return array(
            'data' => $list,
            'total_count' => $count,
            'page_count' => $page_count
        );
    }
    /**
     * 获取一定条件下的列表
     * @param unknown $condition
     * @param unknown $field
     */
    public function getQuery($condition, $field, $order)
    {
        $list = $this->field($field)->where($condition)->order($order)->select();
        return $list;
    }

    /**
     * 获取关联查询列表
     *
     * @param unknown $viewObj
     *            对应view对象
     * @param unknown $page_index            
     * @param unknown $page_size            
     * @param unknown $condition            
     * @param unknown $order            
     * @return multitype:number unknown
     */
    public function viewPageQuery($viewObj, $page_index, $page_size, $condition, $order)
    {
        if ($page_size == 0) {
            $list = $viewObj->where($condition)
                ->order($order)
                ->select();
        } else {
            $start_row = $page_size * ($page_index - 1);
            
            $list = $viewObj->where($condition)
                ->order($order)
                ->limit($start_row . "," . $page_size)
                ->select();
        }
        return $list;
    }

    /**
     * 获取关联查询数量
     *
     * @param unknown $viewObj
     *            视图对象
     * @param unknown $condition
     *            下旬条件
     * @return unknown
     */
    public function viewCount($viewObj, $condition)
    {
        $count = $viewObj->where($condition)->count();
        return $count;
    }

    /**
     * 设置关联查询返回数据格式
     *
     * @param unknown $list
     *            查询数据列表
     * @param unknown $count
     *            查询数据数量
     * @param unknown $page_size
     *            每页显示条数
     * @return multitype:unknown number
     */
    public function setReturnList($list, $count, $page_size)
    {
        if($page_size == 0)
        {
            $page_count = 1;
        }else{
            if ($count % $page_size == 0) {
                $page_count = $count / $page_size;
            } else {
                $page_count = (int) ($count / $page_size) + 1;
            }
        }
        return array(
            'data' => $list,
            'total_count' => $count,
            'page_count' => $page_count
        );
    }

    /**
     * 获取单条记录的基本信息
     *
     * @param unknown $condition            
     * @param string $field            
     */
    public function getInfo($condition = '', $field = '*')
    {
        $info = Db::table($this->table)->where($condition)
            ->field($field)
            ->find();
        return $info;
    }
    /**
     * 查询数据的数量
     * @param unknown $condition
     * @return unknown
     */
    public function getCount($condition)
    {
        $count = Db::table($this->table)->where($condition)
        ->count();
        return $count;
    }
    /**
     * 查询条件数量
     * @param unknown $condition
     * @param unknown $field
     * @return number|unknown
     */
    public function getSum($condition, $field)
    {
        $sum = Db::table($this->table)->where($condition)
        ->sum($field);
        if(empty($sum))
        {
            return 0;
        }else
        return $sum;
    }
    /**
     * 查询数据最大值
     * @param unknown $condition
     * @param unknown $field
     * @return number|unknown
     */
    public function getMax($condition, $field)
    {
        $max = Db::table($this->table)->where($condition)
        ->max($field);
        if(empty($max))
        {
            return 0;
        }else
            return $max;
    }
    /**
     * 查询数据最小值
     * @param unknown $condition
     * @param unknown $field
     * @return number|unknown
     */
    public function getMin($condition, $field)
    {
        $min = Db::table($this->table)->where($condition)
        ->min($field);
        if(empty($min))
        {
            return 0;
        }else
            return $min;
    }
    /**
     * 查询数据均值
     * @param unknown $condition
     * @param unknown $field
     */
    public function getAvg($condition, $field)
    {
        $avg = Db::table($this->table)->where($condition)
        ->avg($field);
        if(empty($avg))
        {
            return 0;
        }else
            return $avg;
    }
    /**
     * 查询第一条数据
     * @param unknown $condition
     */
    public function getFirstData($condition, $order)
    {
        $data = Db::table($this->table)->where($condition)->order($order)
        ->limit(1)->select();
        if(!empty($data))
        {
            return $data[0];
        }else
            return '';
    }
    /**
     * 修改表单个字段值
     * @param unknown $pk_id
     * @param unknown $field_name
     * @param unknown $field_value
     */
    public function ModifyTableField($pk_name, $pk_id, $field_name, $field_value)
    {
        $data = array(
            $field_name => $field_value
        );
        $res = $this->save($data,[$pk_name => $pk_id]);
        return $res;
    }
}
