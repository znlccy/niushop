<?php
/**
 * Config.php
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
namespace app\admin\controller;

use data\model\ModuleModel;
use data\service\Extend as ExtendService;
use data\service\WebSite;
use think\Db;
use think\Request;

/**
 * 扩展模块控制器
 *
 * @author Administrator
 *        
 */
class Extend extends BaseController
{

    protected $extend;

    public function __construct()
    {
        $this->extend = new ExtendService();
        parent::__construct();
    }

    /**
     * 插件管理
     */
    public function addonsList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $list = $this->extend->getAddonsList($page_index, $page_size);
            return $list;
        }
        return view($this->style . "Extend/addonsList");
    }

    /**
     * 添加插件
     */
    public function addAddons()
    {
        return view($this->style . "Extend/addAddons");
    }

    /**
     * 钩子管理
     */
    public function hooksList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', 0);
            $list = $this->extend->getHooksList($page_index, $page_size);
            return $list;
        }
        return view($this->style . "Extend/hooksList");
    }

    /**
     * 添加钩子
     */
    public function addHooks()
    {
        if (request()->isAjax()) {
            $name = request()->post('name', '');
            $desc = request()->post('desc', '');
            $type = request()->post('type', 1);
            $res = $this->extend->addHooks($name, $desc, $type);
            return Ajaxreturn($res);
        }
        return view($this->style . "Extend/addHooks");
    }

    /**
     * 修改钩子
     */
    public function updateHooks()
    {
        $id = request()->get('id', 0);
        $info = $this->extend->getHoodsInfo([
            'id' => $id
        ]);
        if (! empty($info['addons'])) {
            $info['addons'] = explode(',', $info['addons']);
        }
        $this->assign('info', $info);
        if (request()->isAjax()) {
            $id = request()->post('id', '');
            $name = request()->post('name', '');
            $desc = request()->post('desc', '');
            $type = request()->post('type', 1);
            $addons = request()->post('addons', '');
            $res = $this->extend->editHooks($id, $name, $desc, $type, $addons);
            return Ajaxreturn($res);
        }
        return view($this->style . "Extend/updateHooks");
    }

    /**
     * 删除 钩子
     */
    public function deleteHooks()
    {
        $id = request()->post('id', 0);
        $res = $this->extend->deleteHooks($id);
        return AjaxReturn($res);
    }

    /**
     * 插件列表（某插件类型下的）
     */
    public function pluginList()
    {
        $id = request()->get('id', 0);
        $this->assign('id', $id);
        if (request()->isAjax()) {
            $id = request()->post('id', 0);
            $list = $this->extend->getPluginList($id);
            return $list;
        }
        return view($this->style . "Extend/pluginList");
    }

    /**
     * 安装插件
     */
    public function install()
    {
        $addon_name = trim(request()->get('addon_name', ''));
        $class = get_addon_class($addon_name);
        if (! class_exists($class))
            $this->error('插件不存在');
        $addons = new $class();
        $info = $addons->info;
        
        if (! $info) // 检测信息的正确性
            $this->error('插件信息缺失');
        session('addons_install_error', null);
        $install_flag = $addons->install();
        if (! $install_flag) {
            $this->error('执行插件预安装操作失败' . session('addons_install_error'));
        }
        
        // 判断是否有后台列表
        if (is_array($addons->admin_list) && $addons->admin_list !== array()) {
            $info['has_adminlist'] = 1;
        } else {
            $info['has_adminlist'] = 0;
        }
        // 获取菜单 如果有菜单配置则安装菜单
        if (is_array($addons->menu_info) && $addons->menu_info !== array()) {
            $menu = $addons->menu_info;
            $website = new WebSite();
            $module_model = new ModuleModel();
            foreach ($menu as $k => $v) {
                $parent_module_info = $module_model->getInfo([
                    'module_name' => $v['parent_module_name']
                ], 'module_id, controller');
                if (empty($parent_module_info)) {
                    // $addons->uninstall();
                    // $this->error('上级菜单不存在，请检查菜单配置');
                    // break;
                    $controller = $v['parent_module_name'];
                } else {
                    $controller = $parent_module_info['controller'];
                }
                
                $method = 'addonmenu';
                $url = $controller . '/menu_' . $controller . '?addons=' . $v['hook_name'];
                $last_module_id = $module_model->getInfo([
                    'module_name' => $v['last_module_name']
                ], 'module_id, sort');
                $res = $website->addSytemModule($v['module_name'], $controller, $method, $parent_module_info['module_id'], $url, $v['is_menu'], $v['is_dev'], $last_module_id['sort'], $v['module_picture'], $v['desc'], $v['icon_class'], $v['is_control_auth']);
                if (! $res) {
                    $addons->uninstall();
                    $this->error('安装菜单操作失败，请检查菜单配置');
                    break;
                }
            }
        }
        // 获取配置文件
        $info['config'] = json_encode($addons->getOneConfig());
        // 插件添加到库
        $res = $this->extend->addAddons($info['name'], $info['title'], $info['description'], $info['status'], $info['config'], $info['author'], $info['version'], $info['has_adminlist'], $info['has_addonslist'], $info['config_hook'], $info['content']);
        if ($res) {
            $hooks_update = $this->extend->updateHooks($addon_name);
            if ($hooks_update) {
                cache('hooks', null);
                $this->success('安装成功');
            } else {
                $this->extend->deleteAddons([
                    'name' => $addon_name
                ]);
                $this->error('更新钩子处插件失败,请卸载后尝试重新安装');
            }
        } else {
            $this->error('写入插件数据失败');
        }
    }

    /**
     * 卸载插件
     */
    public function uninstall()
    {
        $id = trim(request()->get('id', 0));
        $db_addons = $this->extend->getAddonsInfo([
            'id' => $id
        ], '*');
        $class = get_addon_class($db_addons['name']);
        // $this->assign ( 'jumpUrl', url ( 'index' ) );
        if (! $db_addons || ! class_exists($class))
            $this->error('插件不存在');
        session('addons_uninstall_error', null);
        $addons = new $class();
        $uninstall_flag = $addons->uninstall();
        if (! $uninstall_flag)
            $this->error('执行插件预卸载操作失败' . session('addons_uninstall_error'));
            // 判断是否有菜单，有的话需要删除
        if (is_array($addons->menu_info) && $addons->menu_info !== array()) {
            $menu = $addons->menu_info;
            $module_model = new ModuleModel();
            foreach ($menu as $k => $v) {
                $method = 'addonmenu';
                $module_model->destroy([
                    'module_name' => $v['module_name'],
                    'method' => $method
                ]);
            }
        }
        $hooks_update = $this->extend->removeHooks($db_addons['name']);
        if ($hooks_update === false) {
            $this->error('卸载插件所挂载的钩子数据失败');
        }
        cache('hooks', null);
        $delete = $this->extend->deleteAddons([
            'name' => $db_addons['name']
        ]);
        if ($delete === false) {
            $this->error('卸载插件失败');
        } else {
            // 删除移动的资源文件
            // $File = new \com\File();
            // $File->del_dir('./static/addons/'.$db_addons ['name']);
            $this->success('卸载成功');
        }
    }

    /**
     * 启用插件
     */
    public function enable()
    {
        $id = request()->get('id', 0);
        cache('hooks', null);
        $res = $this->extend->updateAddonsStatus($id, 1);
        if ($res) {
            $this->success('启用成功');
        } else {
            $this->error('启用失败');
        }
    }

    /**
     * 禁用插件
     */
    public function disable()
    {
        $id = request()->get('id', 0);
        cache('hooks', null);
        $res = $this->extend->updateAddonsStatus($id, 0);
        if ($res) {
            $this->success('禁用成功');
        } else {
            $this->error('禁用失败');
        }
    }

    /**
     * 安装插件 （某插件类型下）
     */
    public function installPlugin()
    {
        $id = request()->get('id', 0);
        $addon_name = $this->extend->getAddonsInfo([
            'id' => $id
        ], 'name');
        $addon_name = $addon_name['name'];
        $plugin_name = trim(request()->get('plugin_name', ''));
        if ($id == 0 || empty($addon_name) || $plugin_name == '') {
            $this->error('安装失败，参数错误');
        }
        $class = get_addon_class($addon_name);
        if (! class_exists($class))
            $this->error('插件不存在');
        $addons = new $class();
        $table = $addons->table;
        $config_file = ADDON_PATH . $addon_name . '/' . $plugin_name . '/config.php';
        if (is_file($config_file)) {
            $temp_arr = include $config_file;
            $config_arr = array();
            foreach ($temp_arr['config'] as $key => $value) {
                $config_arr[$key] = $value['value'];
            }
            $data = [
                'name' => $temp_arr['name'],
                'title' => $temp_arr['title'],
                'config' => json_encode($config_arr),
                'status' => 1,
                'desc' => $temp_arr['desc'],
                'author' => $temp_arr['author'],
                'version' => $temp_arr['version'],
                'create_time' => time()
            ];
            $res = Db::table("$table")->insert($data);
            if ($res) {
                $this->success('安装成功');
            } else {
                $this->error('安装失败');
            }
        } else {
            $this->error('配置文件不存在');
        }
    }

    /**
     * 卸载插件 （某插件类型下）
     */
    public function uninstallPlugin()
    {
        $addons_id = trim(request()->get('addons_id', 0));
        $plugin_id = trim(request()->get('plugin_id', 0));
        $addon_name = $this->extend->getAddonsInfo([
            'id' => $addons_id
        ], 'name');
        $addon_name = $addon_name['name'];
        
        if ($addons_id == 0 || empty($addon_name) || $plugin_id == 0) {
            $this->error('卸载失败，参数错误');
        }
        $class = get_addon_class($addon_name);
        if (! class_exists($class))
            $this->error('插件不存在');
        $addons = new $class();
        $table = $addons->table;
        $res = Db::table("$table")->where('id', $plugin_id)->delete();
        if ($res) {
            $this->success('卸载成功');
        } else {
            $this->error('卸载失败');
        }
    }

    /**
     * 启用插件 （某插件类型下）
     */
    public function enablePlugin()
    {
        $addons_id = trim(request()->get('addons_id', 0));
        $plugin_id = trim(request()->get('plugin_id', 0));
        $addon_name = $this->extend->getAddonsInfo([
            'id' => $addons_id
        ], 'name');
        $addon_name = $addon_name['name'];
        
        if ($addons_id == 0 || empty($addon_name) || $plugin_id == 0) {
            $this->error('启用失败，参数错误');
        }
        $class = get_addon_class($addon_name);
        if (! class_exists($class))
            $this->error('插件不存在');
        $addons = new $class();
        $table = $addons->table;
        $res = Db::table("$table")->where('id', $plugin_id)->update([
            'status' => 1
        ]);
        if ($res) {
            $this->success('启用成功');
        } else {
            $this->error('启用失败');
        }
    }

    /**
     * 禁用插件 （某插件类型下）
     */
    public function disablePlugin()
    {
        $addons_id = trim(request()->get('addons_id', 0));
        $plugin_id = trim(request()->get('plugin_id', 0));
        $addon_name = $this->extend->getAddonsInfo([
            'id' => $addons_id
        ], 'name');
        $addon_name = $addon_name['name'];
        
        if ($addons_id == 0 || empty($addon_name) || $plugin_id == 0) {
            $this->error('禁用失败，参数错误');
        }
        $class = get_addon_class($addon_name);
        if (! class_exists($class))
            $this->error('插件不存在');
        $addons = new $class();
        $table = $addons->table;
        $res = Db::table("$table")->where('id', $plugin_id)->update([
            'status' => 0
        ]);
        if ($res) {
            $this->success('禁用成功');
        } else {
            $this->error('禁用失败');
        }
    }

    public function addonsConfig()
    {
        $addons_id = trim(request()->get('id', 0));
        $addon_info = $this->extend->getAddonsInfo([
            'id' => $addons_id
        ], '*');
        
        $addon_title = $addon_info['title'];
        $addon_name = $addon_info['name'];
        $this->assign('addons_id', $addons_id);
        $this->assign('addon_title', $addon_title);
        $this->assign('config_hook', $addon_info['config_hook']);
        if ($addons_id == 0 || empty($addon_name)) {
            $this->error('参数错误');
        }
        $config_file = ADDON_PATH . $addon_name . '/config.php';
        if (! is_file($config_file)) {
            $this->error('配置文件不存在');
        }
        $temp_arr = include $config_file;
        $db_config = $addon_info['config'];
        if ($db_config) {
            $db_config = json_decode($db_config, true);
            if ($db_config) {
                foreach ($temp_arr['config'] as $key => $value) {
                    $temp_arr['config'][$key]['value'] = $db_config[$key];
                }
            }
        }
        $this->assign('data', $temp_arr);
        return view($this->style . "Extend/addonsConfig");
    }

    /**
     * 插件配置 （某插件类型下）
     */
    public function pluginConfig()
    {
        $addons_id = trim(request()->get('addons_id', 0));
        $plugin_id = trim(request()->get('plugin_id', 0));
        $addon_info = $this->extend->getAddonsInfo([
            'id' => $addons_id
        ], 'name, title');
        $addon_title = $addon_info['title'];
        $addon_name = $addon_info['name'];
        if ($addons_id == 0 || empty($addon_name) || $plugin_id == 0) {
            $this->error('参数错误');
        }
        $this->assign('addons_id', $addons_id);
        $this->assign('plugin_id', $plugin_id);
        $this->assign('addon_title', $addon_title);
        $class = get_addon_class($addon_name);
        if (! class_exists($class))
            $this->error('插件不存在');
        $addons = new $class();
        $table = $addons->table;
        $config_info = Db::table("$table")->where('id', $plugin_id)->find();
        if (empty($config_info)) {
            $this->error('该插件失效，请重新安装');
        }
        $this->assign('plugin_title', $config_info['title']);
        $config_file = ADDON_PATH . $addon_name . '/' . $config_info['name'] . '/config.php';
        $temp_arr = include $config_file;
        $db_config = $config_info['config'];
        if ($db_config) {
            $db_config = json_decode($db_config, true);
            foreach ($temp_arr['config'] as $key => $value) {
                $temp_arr['config'][$key]['value'] = $db_config[$key];
            }
        }
        $this->assign('data', $temp_arr);
        
        return view($this->style . "Extend/pluginConfig");
    }

    /**
     * 修改插件配置 表单提交
     */
    public function saveAddonsConfig()
    {
        $post = request()->post();
        $addons_id = $post['addons_id'];
        $config = json_encode($post['config']);
        $res = $this->extend->updateAddonsConfig([
            'id' => $addons_id
        ], $config);
        if ($res) {
            $this->success('保存成功');
        } else {
            $this->error('保存失败');
        }
    }

    /**
     * 修改插件配置 (某插件类型下) 表单提交
     */
    public function savePluginConfig()
    {
        $post = request()->post();
        $addons_id = $post['addons_id'];
        $plugin_id = $post['plugin_id'];
        $config = json_encode($post['config']);
        $addon_info = $this->extend->getAddonsInfo([
            'id' => $addons_id
        ], 'name, title');
        $addon_name = $addon_info['name'];
        if ($addons_id == 0 || empty($addon_name) || $plugin_id == 0) {
            $this->error('参数错误');
        }
        $class = get_addon_class($addon_name);
        if (! class_exists($class))
            $this->error('插件不存在');
        $addons = new $class();
        $table = $addons->table;
        $res = Db::table("$table")->where('id', $plugin_id)->update([
            'config' => $config
        ]);
        if ($res) {
            $this->success('保存成功');
        } else {
            $this->error('保存失败');
        }
    }

    /**
     * 获取插件详情
     */
    public function getAddonsDetail()
    {
        $id = request()->post('id', 0);
        $extend = new ExtendService();
        $info = $extend->getAddonsInfo([
            'id' => $id
        ], '*');
        return $info;
    }
}