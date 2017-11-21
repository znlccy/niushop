<?php
/**
 * BaseController.php
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

\think\Loader::addNamespace('data', 'data/');
use data\service\AdminUser as User;
use data\service\Shop;
use data\service\WebSite as WebSite;
use think\Controller;
use data\service\AdminUser;
use data\service\AuthGroup as AuthGroup;

class BaseController extends Controller
{

    protected $user = null;

    protected $website = null;

    protected $uid;

    protected $instance_id;

    protected $instance_name;

    protected $user_name;

    protected $user_headimg;

    protected $module = null;

    protected $controller = null;

    protected $action = null;

    protected $module_info = null;

    protected $rootid = null;

    protected $moduleid = null;

    protected $second_menu_id = null;
    // 二级菜单module_id 手机自定义模板临时添加，用来查询三级菜单
    
    /**
     * 当前版本的路径
     *
     * @var string
     */
    protected $style = null;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
        $this->website = new WebSite();
        $this->init();
        $this->assign("pageshow", PAGESHOW);
        $this->assign("pagesize", PAGESIZE);
    }

    /**
     * 创建时间：2016-10-27
     * 功能说明：action基类 调用 加载头部数据的方法
     */
    public function init()
    {
        $this->uid = $this->user->getSessionUid();
        $is_system = $this->user->getSessionUserIsSystem();
        
        if (empty($this->uid)) {
            if (request()->isAjax()) {
                echo json_encode(AjaxReturn(NO_LOGIN));
                exit();
            } else {
                $redirect = __URL(__URL__ . '/' . ADMIN_MODULE . "/login");
                $this->redirect($redirect);
            }
        }
        if (empty($is_system)) {
            $redirect = __URL(__URL__ . '/' . ADMIN_MODULE . "/login");
            $this->redirect($redirect);
        }
        $this->instance_id = $this->user->getSessionInstanceId();
        $this->instance_name = $this->user->getInstanceName();
        $this->module = \think\Request::instance()->module();
        $this->controller = \think\Request::instance()->controller();
        if ($this->controller == 'Menu') {
            $this->controller = session('controller');
        }
        $this->action = \think\Request::instance()->action();
        
        // 判断是否是插件菜单
        if (strpos($this->action, 'menu_') !== false) {
            $action_array = explode('_', $this->action);
            session('controller', $action_array[1]);
            $addons = request()->get('addons', '');
            $method = request()->get('method','');
            $redirect = __URL(__URL__ . '/' . ADMIN_MODULE . "/Menu/addonmenu", 'addons=' . $addons.'&method='.$method);
            $this->redirect($redirect);
        }
        $this->module_info = $this->website->getModuleIdByModule($this->controller, $this->action);
        // 过滤控制权限 为0
        if (empty($this->module_info)) {
            $this->moduleid = 0;
            $check_auth = 1;
        } elseif ($this->module_info["is_control_auth"] == 0) {
            $this->moduleid = $this->module_info['module_id'];
            $check_auth = 1;
        } else {
            $this->moduleid = $this->module_info['module_id'];
            $check_auth = $this->user->checkAuth($this->moduleid);
        }
        if ($check_auth) {
            
            // 网站信息
            $web_info = $this->website->getWebSiteInfo();
            switch ($web_info['style_id_admin']) {
                case 3:
                    $this->style = STYLE_DEFAULT_ADMIN . '/';
                    $this->assign("style", STYLE_DEFAULT_ADMIN);
                    break;
                case 4:
                    $this->style = STYLE_BLUE_ADMIN . '/';
                    $this->assign("style", STYLE_BLUE_ADMIN);
                    break;
                default:
                    $this->style = STYLE_BLUE_ADMIN . '/';
                    $this->assign("style", STYLE_BLUE_ADMIN);
                    break;
            }
            $this->assign('web_phone', $web_info['web_phone']);
            $this->assign('web_email', $web_info['web_email']);
            //弹出框标题
            if (empty($web_info['web_popup_title'])){
                $this->assign("web_popup_title", "Niushop开源商城");
            }else{
                $this->assign("web_popup_title", $web_info['web_popup_title']); 
            }
           
            // 版本
            @include ROOT_PATH . 'version.php';
            $this->assign('niu_version', NIU_VERSION);
            $this->assign('niu_ver_date', NIU_VER_DATE);
            $warm_prompt_is_show = $this->getWarmPromptIsShow();
            $this->assign('warm_prompt_is_show', $warm_prompt_is_show);
            $this->getSystemConfig();
            
            $this->addUserLog();
            $this->assign("instance_id", $this->instance_id);
            
            if (! request()->isAjax()) {
                /* 店铺导航 */
                $shop = new Shop();
                $ShopNavigationData = $shop->ShopNavigationList(1, 6, [
                    "type" => 3
                ], 'sort');
                
                // 用户信息
                $user_info = $this->user->getUserInfo();
                if ($user_info['last_login_time'] == "0000-00-00 00:00:00") {
                    $user_info['last_login_time'] = "--";
                }
                if ($user_info['last_login_ip'] == "0.0.0.0") {
                    $user_info['last_login_ip'] = "--";
                }
                $this->assign("user_info", $user_info);
                // 后台用户所属用户组信息
                if ($user_info['is_system'] == 1) {
                    $admin_user = new AdminUser();
                    // 根据用户uid获取所属用户组id
                    $admin_user_group = $admin_user->getAdminUserInfo([
                        'uid' => $user_info['uid']
                    ]);
                    // 根据用户组id获取用户组名称
                    $user_group = new AuthGroup();
                    $group_id = $admin_user_group['group_id_array'];
                    $groupinfo = $user_group->getSystemUserGroupDetail($group_id);
                    $group_name = $groupinfo['group_name'];
                    $this->assign('group_name', $group_name);
                }
                $root_array = $this->website->getModuleRootAndSecondMenu($this->moduleid);
                $this->rootid = $root_array[0];
                $second_menu_id = $root_array[1];
                $root_module_info = $this->website->getSystemModuleInfo($this->rootid, 'module_name,url,module_picture');
                $first_menu_list = $this->user->getchildModuleQuery(0);
                if ($this->rootid != 0) {
                    $second_menu_list = $this->user->getchildModuleQuery($this->rootid);
                } else {
                    $second_menu_list = '';
                }
                $this->user_name = $user_info['user_name'];
                $this->user_headimg = $user_info['user_headimg'];
                $this->assign("headid", $this->rootid);
                $this->assign("second_menu_id", $second_menu_id);
                $this->assign("moduleid", $this->moduleid);
                $this->assign("title_name", $this->instance_name);
                $this->assign("user_name", $this->user_name);
                $this->assign("user_headimg", $this->user_headimg);
                $this->assign("headlist", $first_menu_list);
                $this->assign("leftlist", $second_menu_list);
                $this->assign("frist_menu", $root_module_info); // 当前选中的导航菜单
                $this->assign("secend_menu", $this->module_info);
                $path_info_url = request()->url();
                $replace_url = str_replace(request()->root() . '/admin/', '', $path_info_url);
                $child_menu_list = array(
                    array(
                        'url' => $replace_url,
                        'menu_name' => $this->module_info['module_name'],
                        'active' => 1
                    )
                );
                $this->assign('child_menu_list', $child_menu_list);
                $this->assign('ShopNavigationData', $ShopNavigationData['data']);
                $this->assign('first_menu_list', $first_menu_list);
                $this->assign('second_menu_list', $second_menu_list);
                $this->second_menu_id = $second_menu_id; // 临时添加，用来查询3级菜单 手机端自定义模板
                $this->getNavigation();
            }
        } else {
            if (request()->isAjax()) {
                echo json_encode(AjaxReturn(NO_AITHORITY));
                exit();
            } else {
                $this->error("当前用户没有操作权限");
            }
        }
    }

    /**
     * 添加操作日志（当前考虑所有操作），
     */
    private function addUserLog()
    {
        $get_data = '';
        if (request()->isGet()) {
            $res = \think\Request::instance()->get();
            $get_data = json_encode($res);
        }
        if (request()->isPost()) {
            $res = \think\Request::instance()->post();
            if (empty($get_data)) {
                $get_data = json_encode($res);
            } else {
                $get_data = $get_data . ',' . json_encode($res);
            }
        }
        // 建议，调试模式，用于
        // $res = $this->user->addUserLog($this->uid, 1, $this->controller, $this->action, \think\Request::instance()->ip(), $get_data);
    }

    /**
     * 获取导航
     */
    public function getNavigation()
    {
        $first_list = $this->user->getchildModuleQuery(0);
        $list = array();
        foreach ($first_list as $k => $v) {
            $submenu = $this->user->getchildModuleQuery($v['module_id']);
            $list[$k]['data'] = $v;
            $list[$k]['sub_menu'] = $submenu;
        }
        $this->assign("nav_list", $list);
    }

    /**
     * 获取操作提示是否显示
     *
     * @return mixed|boolean|void
     */
    public function getWarmPromptIsShow()
    {
        $is_show = cookie("warm_promt_is_show");
        if ($is_show == null) {
            $is_show = 'show';
        }
        return $is_show;
    }

    /**
     * 获取系统信息
     */
    public function getSystemConfig()
    {
        $system_config['os'] = php_uname(); // 服务器操作系统
        $system_config['server_software'] = $_SERVER['SERVER_SOFTWARE']; // 服务器环境
        $system_config['upload_max_filesize'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow'; // 文件上传限制
        $system_config['gd_version'] = gd_info()['GD Version']; // GD（图形处理）版本
        $system_config['max_execution_time'] = ini_get("max_execution_time") . "秒"; // 最大执行时间
        $system_config['port'] = $_SERVER['SERVER_PORT']; // 端口
        $system_config['dns'] = $_SERVER['HTTP_HOST']; // 服务器域名
        $system_config['php_version'] = PHP_VERSION; // php版本
        $system_config['ip'] = $_SERVER['SERVER_ADDR']; // 服务器ip
        $this->assign("system_config", $system_config);
    }

    /**
     * 获取三级菜单
     * 创建时间：2017年8月25日 17:49:07 王永杰
     * 目前只有固定模板和自定义模板用
     */
    public function getThreeLevelModule()
    {
        $child_menu_list_old = $this->user->getchildModuleQuery($this->second_menu_id);
        $child_menu_list = [];
        foreach ($child_menu_list_old as $k => $v) {
            $active = 0;
            $param = request()->param();
            if (strpos(strtolower(request()->pathinfo()), strtolower($v['url']))) {
                $active = 1;
            } else 
                if (! empty($param['addons']) && strpos(strtolower($v['url']), strtolower($param['addons'])) !== false) {
                    $active = 1;
                }
            $child_menu_list[] = array(
                'url' => $v['url'],
                'menu_name' => $v['module_name'],
                'active' => $active
            );
        }
        
        $this->assign('child_menu_list', $child_menu_list);
    }
}
