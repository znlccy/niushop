<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : NiuTeam
 * @date : 2017年11月13日 16:03:16
 * @version : v1.0.0.0
 */
namespace addons;
use think\Config;
use think\View;
use think\Db;
use think\exception\HttpResponseException;
use think\response\Redirect;
use think\Url;
/**
 * 插件基类
 * Class Addons
 * @author Byron Sampson <xiaobo.sun@qq.com>
 * @package think\addons
 */
abstract class Addons 
{
    /**
     * 视图实例对象
     * @var view
     * @access protected
     */
    protected $view = null;
    // 当前错误信息
    protected $error;
    /**
     * $info = [
     *  'name'          => 'Test',
     *  'title'         => '测试插件',
     *  'description'   => '用于thinkphp5的插件扩展演示',
     *  'status'        => 1,
     *  'author'        => 'byron sampson',
     *  'version'       => '0.1'
     * ]
     */
    public $info = [];
    public $addons_path = '';
    public $config_file = '';
    /**
     * 架构函数
     * @access public
     */
    public function __construct()
    {
        // 获取当前插件目录
        $this->addons_path = ADDON_PATH . $this->getName() . DS;
        // 读取当前插件配置信息   判断是否有下级插件列表
        if($this->info['has_addonslist'] == 1)
        {
            if(is_file($this->addons_path . 'core/config.php'))
            {
                $this->config_file = $this->addons_path . 'core/config.php';
            }
        }
        else
        {
            if (is_file($this->addons_path . 'config.php'))
            {
                $this->config_file = $this->addons_path . 'config.php';
            }
        }
        // 初始化视图模型
        $config = ['view_path' => $this->addons_path];

        $config = array_merge(Config::get('template'), $config);

        $this->view = new View($config, Config::get('view_replace_str'));
        // 控制器初始化
        if (method_exists($this, '_initialize')) {
            $this->_initialize();
        }
    }
    /**
     * 获取插件的配置数组
     * @param string $name 可选模块名
     * @return array|mixed|null
     */
    final public function getOneConfig($name = '')
    {
        static $_config = array();
        if (empty($name)) {
            $name = $this->getName();
        }
        if (isset($_config[$name])) {
            return $_config[$name];
        }
        $config = [];
        if (is_file($this->config_file)) {
            $temp_arr = include $this->config_file;
            foreach ($temp_arr as $key => $value) {
                $config[$key] = $value;
//                 if ($value['type'] == 'group') {
//                     foreach ($value['options'] as $gkey => $gvalue) {
//                         foreach ($gvalue['options'] as $ikey => $ivalue) {
//                             $config[$ikey] = $ivalue['value'];
//                         }
//                     }
//                 } else {
//                     $config[$key] = $temp_arr[$key]['value'];
//                 }
            }
            unset($temp_arr);
        }
        $_config[$name] = $config;
        return $config;
    }
    
    /**
     * 获取插件的配置数组
     * @param string $name 可选模块名
     * @return array|mixed|null
     */
    final public function getAllConfig($name = '')
    {
        static $_config = array();
        if (empty($name)) {
            $name = $this->getName();
        }
        if (isset($_config[$name])) {
            return $_config[$name];
        }
        $config = [];

        $handler = opendir($this->addons_path);
        while (($filename = readdir($handler)) !== false) {//务必使用!==，防止目录下出现类似文件名“0”等情况
            if ($filename != "." && $filename != ".." && $filename != "core")
            {
                if(is_file($this->addons_path.'/'.$filename.'/config.php'))
                {
                    $temp_arr = include $this->addons_path.'/'.$filename.'/config.php';
                    $config[] = $temp_arr;
                }
            }
        }
        closedir($handler);
        return $config;
    }
    
    /**
     * 获取当前模块名
     * @return string
     */
    final public function getName()
    {
        $data = explode('\\', get_class($this));
        return strtolower(array_pop($data));
    }
    /**
     * 检查配置信息是否完整
     * @return bool
     */
    final public function checkInfo()
    {
        $info_check_keys = ['name', 'title', 'description', 'status', 'author', 'version'];
        foreach ($info_check_keys as $value) {
            if (!array_key_exists($value, $this->info)) {
                return false;
            }
        }
        return true;
    }
    /**
     * 加载模板和页面输出 可以返回输出内容
     * @access public
     * @param string $template 模板文件名或者内容
     * @param array $vars 模板输出变量
     * @param array $replace 替换内容
     * @param array $config 模板参数
     * @return mixed
     * @throws \Exception
     */
    public function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        if (!is_file($template)) {
            $template = '/' . $template;
        }
        // 关闭模板布局
        $this->view->engine->layout(false);
        echo $this->view->fetch($template, $vars, $replace, $config);
    }
    /**
     * 渲染内容输出
     * @access public
     * @param string $content 内容
     * @param array $vars 模板输出变量
     * @param array $replace 替换内容
     * @param array $config 模板参数
     * @return mixed
     */
    public function display($content, $vars = [], $replace = [], $config = [])
    {
        // 关闭模板布局
        $this->view->engine->layout(false);
        echo $this->view->display($content, $vars, $replace, $config);
    }
    /**
     * 渲染内容输出
     * @access public
     * @param string $content 内容
     * @param array $vars 模板输出变量
     * @return mixed
     */
    public function show($content, $vars = [])
    {
        // 关闭模板布局
        $this->view->engine->layout(false);
        echo $this->view->fetch($content, $vars, [], [], true);
    }
    /**
     * 模板变量赋值
     * @access protected
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     * @return void
     */
    public function assign($name, $value = '')
    {
        $this->view->assign($name, $value);
    }
    
    /**
     * 获取当前错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
    
    public function redirect($url, $params = [], $code = 302, $with = [])
    {
        $response = new Redirect($url);
        if (is_integer($params)) {
            $code   = $params;
            $params = [];
        }
        $response->code($code)->params($params)->with($with);
        throw new HttpResponseException($response);
    }
    
    //必须实现安装
    abstract public function install();
    //必须卸载插件方法
    abstract public function uninstall();
}