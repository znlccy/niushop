<?php
/**
 * Wchat.php
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

use data\extend\WchatOauth;
use data\service\Config;
use data\service\Shop;
use data\service\Weixin;
use data\service\WeixinMessage;
use Qiniu\json_decode;

/**
 * 微信管理
 *
 * @author Administrator
 *        
 */
class Wchat extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 微信账户设置
     */
    public function config()
    {
        $config = new Config();
        $wchat_config = $config->getInstanceWchatConfig($this->instance_id);
        // 获取当前域名
        $domain_name = \think\Request::instance()->domain();
        $url = $domain_name . \think\Request::instance()->root();
        // 去除链接的http://头部
        $url_top = substr($url, 7);
        // 去除链接的尾部index.php
        $url_top = str_replace('/index.php', '', $url_top);
        $call_back_url = __URL(__URL__ . '/wap/wchat/relateWeixin');
        // $call_back_url = str_replace('/index.php', '', $call_back_url);
        $this->assign("url", $url_top);
        $this->assign("call_back_url", $call_back_url);
        $this->assign('wchat_config', $wchat_config["value"]);
        return view($this->style . 'Wchat/config');
    }

    /**
     * 修改微信配置
     * 2017年4月27日 11:03:30
     *
     * @return unknown
     */
    public function setInstanceWchatConfig()
    {
        $config = new Config();
        $appid = str_replace(' ', '', request()->post('appid', ''));
        $appsecret = str_replace(' ', '', request()->post('appsecret', ''));
        $token = request()->post('token', '');
        $res = $config->setInstanceWchatConfig($this->instance_id, $appid, $appsecret, $token);
        return AjaxReturn($res);
    }

    /**
     * 微信菜单
     */
    public function menu()
    {
        $weixin = new Weixin();
        $menu_list = $weixin->getInstanceWchatMenu($this->instance_id);
        $default_menu_info = array(); // 默认显示菜单
        $menu_list_count = count($menu_list);
        $class_index = count($menu_list);
        if ($class_index > 0) {
            if ($class_index == MAX_MENU_LENGTH) {
                $class_index = MAX_MENU_LENGTH - 1;
            }
        }
        if ($menu_list_count > 0) {
            $default_menu_info = $menu_list[$menu_list_count - 1];
        } else {
            $default_menu_info["menu_name"] = "";
            $default_menu_info["menu_id"] = 0;
            $default_menu_info["child_count"] = 0;
            $default_menu_info["media_id"] = 0;
            $default_menu_info["menu_event_url"] = "";
            $default_menu_info["menu_event_type"] = 1;
        }
        $media_detail = array();
        if ($default_menu_info["media_id"]) {
            // 查询图文消息
            $media_detail = $weixin->getWeixinMediaDetail($default_menu_info["media_id"]);
            $media_detail["item_list_count"] = count($media_detail["item_list"]);
        } else {
            $media_detail["create_time"] = "";
            $media_detail["title"] = "";
            $media_detail["item_list_count"] = 0;
        }
        $default_menu_info["media_list"] = $media_detail;
        $this->assign("wx_name", $this->instance_name);
        $this->assign("menu_list", $menu_list);
        $this->assign("MAX_MENU_LENGTH", MAX_MENU_LENGTH); // 一级菜单数量
        $this->assign("MAX_SUB_MENU_LENGTH", MAX_SUB_MENU_LENGTH); // 二级菜单数量
        $this->assign("menu_list_count", $menu_list_count);
        $this->assign("default_menu_info", $default_menu_info);
        $this->assign("class_index", $class_index);
        return view($this->style . 'Wchat/wxMenu');
    }

    /**
     * 更新菜单到微信,保存并发布
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function updateMenuToWeixin()
    {
        $weixin = new Weixin();
        $result = $weixin->updateInstanceMenuToWeixin($this->instance_id);
        $config = new Config();
        $auth_info = $config->getInstanceWchatConfig($this->instance_id);
        
        if (! empty($auth_info['value']['appid']) && ! empty($auth_info['value']['appsecret'])) {
            $wchat_auth = new WchatOauth();
            
            $res = $wchat_auth->menu_create($result);
            if (! empty($res)) {
                $res = json_decode($res, true);
                if ($res['errcode'] == 0) {
                    $retval = 1;
                } else {
                    $retval = $res['errmsg'];
                }
            } else {
                $retval = 0;
            }
        } else {
            $retval = "当前未配置微信授权";
        }
        return AjaxReturn($retval);
    }

    /**
     * 添加微信自定义菜单
     * 2017年3月15日 18:26:46
     * wyj
     *
     * @return unknown
     */
    public function addWeixinMenu()
    {
        $menu = request()->post('menu', '');
        if (! empty($menu)) {
            $menu = json_decode($menu, true);
            $weixin = new Weixin();
            $instance_id = $this->instance_id;
            $menu_name = $menu["menu_name"]; // 菜单名称
            $ico = ""; // 菜图标单
            $pid = $menu["pid"]; // 父级菜单（一级菜单）
            $menu_event_type = $menu["menu_event_type"]; // '1普通url 2 图文素材 3 功能',
            $menu_event_url = $menu["menu_event_url"]; // '菜单url',
            $media_id = $menu["media_id"]; // '图文消息ID',
            $sort = $menu["sort"]; // 排序
            $res = $weixin->addWeixinMenu($instance_id, $menu_name, $ico, $pid, $menu_event_type, $menu_event_url, $media_id, $sort);
            return $res;
        }
        return - 1;
    }

    /**
     * 修改微信自定义菜单
     *
     * @return unknown
     */
    public function updateWeixinMenu()
    {
        $menu = request()->post('menu', '');
        if (! empty($menu)) {
            $weixin = new Weixin();
            $instance_id = $this->instance_id;
            $menu_name = $menu["menu_name"]; // 菜单名称
            $menu_id = $menu["menu_id"];
            $ico = ""; // 菜图标单
            $pid = $menu["pid"]; // 父级菜单（一级菜单）
            $menu_event_type = $menu["menu_event_type"]; // '1普通url 2 图文素材 3 功能',
            $menu_event_url = $menu["menu_event_url"]; // '菜单url',
            $media_id = $menu["media_id"]; // '图文消息ID',
            $res = $weixin->updateWeixinMenu($menu_id, $instance_id, $menu_name, $ico, $pid, $menu_event_type, $menu_event_url, $media_id);
            return $res;
        }
        return - 1;
    }

    /**
     * 修改排序
     *
     * @return number
     */
    public function updateWeixinMenuSort()
    {
        $menu_id_arr = request()->post('menu_id_arr', '');
        if (! empty($menu_id_arr)) {
            $menu_id_arr = explode(",", $menu_id_arr);
            $weixin = new Weixin();
            $res = $weixin->updateWeixinMenuSort($menu_id_arr);
            return $res;
        }
        return - 1;
    }

    /**
     * 修改微信菜单名称
     * 2017年4月6日 14:42:54
     */
    public function updateWeixinMenuName()
    {
        $menu_name = request()->post('menu_name', '');
        $menu_id = request()->post('menu_id', '');
        if (! empty($menu_name)) {
            $weixin = new Weixin();
            $res = $weixin->updateWeixinMenuName($menu_id, $menu_name);
            return $res;
        }
        return - 1;
    }

    /**
     * 修改跳转链接地址
     * 2017年4月6日 14:59:30
     *
     * @return unknown|number
     */
    public function updateWeixinMenuUrl()
    {
        $menu_event_url = request()->post('menu_event_url', '');
        $menu_id = request()->post('menu_id', '');
        if (! empty($menu_event_url)) {
            $weixin = new Weixin();
            $res = $weixin->updateWeixinMenuUrl($menu_id, $menu_event_url);
            return $res;
        }
        return - 1;
    }

    /**
     * 修改菜单类型，1：文本，2：单图文，3：多图文
     *
     * @return unknown|number
     */
    public function updateWeixinMenuEventType()
    {
        $menu_event_type = request()->post('menu_event_type', '');
        $menu_id = request()->post('menu_id', '');
        if (! empty($menu_event_type)) {
            $weixin = new Weixin();
            $res = $weixin->updateWeixinMenuEventType($menu_id, $menu_event_type);
            return $res;
        }
        return - 1;
    }

    /**
     * 修改图文消息
     *
     * @return unknown|number
     */
    public function updateWeiXinMenuMessage()
    {
        $menu_event_type = request()->post('menu_event_type', '');
        $menu_id = request()->post('menu_id', '');
        $media_id = request()->post('media_id', '');
        if (! empty($menu_event_type)) {
            $weixin = new Weixin();
            $res = $weixin->updateWeiXinMenuMessage($menu_id, $media_id, $menu_event_type);
            return $res;
        }
        return - 1;
    }

    /**
     * 删除微信自定义菜单
     *
     * @return unknown|number
     */
    public function deleteWeixinMenu()
    {
        $menu_id = request()->post('menu_id', '');
        if (! empty($menu_id)) {
            $weixin = new Weixin();
            $res = $weixin->deleteWeixinMenu($menu_id);
            return $res;
        }
        return - 1;
    }

    /**
     * 获取图文素材
     */
    public function getWeixinMediaDetail()
    {
        $media_id = request()->post('media_id', '');
        $weixin = new Weixin();
        $res = $weixin->getWeixinMediaDetail($media_id);
        return $res;
    }

    /**
     * 微信推广二维码
     */
    public function qrcode()
    {
        $shop = new Shop();
        $weixin = new Weixin();
        $web_info = $this->website->getWebSiteInfo();
        if (request()->isAjax()) {
            $id = request()->post('id', 0);
            $background = request()->post('background', '');
            $nick_font_color = request()->post('nick_font_color', '#000');
            $nick_font_size = request()->post('nick_font_size', '12');
            $is_logo_show = request()->post('is_logo_show', '1');
            $header_left = request()->post('header_left', '59');
            $header_top = request()->post('header_top', '15');
            $name_left = request()->post('name_left', '128');
            $name_top = request()->post('name_top', '23');
            $logo_left = request()->post('logo_left', '60');
            $logo_top = request()->post('logo_top', '200');
            $code_left = request()->post('code_left', '70');
            $code_top = request()->post('code_top', '300');
            $upload_path = "upload/qrcode/promote_qrcode_template"; // 后台推广二维码模版
            $template_url = $upload_path . '/qrcode_template_' . $id . '_' . $this->instance_id . '.png';
            if ($id == 0) {
                $res = $weixin->addWeixinQrcodeTemplate($this->instance_id, $background, $nick_font_color, $nick_font_size, $is_logo_show, $header_left, $header_top, $name_left, $name_top, $logo_left, $logo_top, $code_left, $code_top, $template_url);
                showUserQecode($upload_path, '', $upload_path . '/thumb_template' . 'qrcode_' . $res . '_' . $this->instance_id . '.png', '', $web_info['logo'], '', request()->post(), $upload_path . '/qrcode_template_' . $res . '_' . $this->instance_id . '.png');
                $res = $weixin->updateWeixinQrcodeTemplate($res, $this->instance_id, $background, $nick_font_color, $nick_font_size, $is_logo_show, $header_left, $header_top, $name_left, $name_top, $logo_left, $logo_top, $code_left, $code_top, $upload_path . '/qrcode_template_' . $res . '_' . $this->instance_id . '.png');
            } else {
                $res = $weixin->updateWeixinQrcodeTemplate($id, $this->instance_id, $background, $nick_font_color, $nick_font_size, $is_logo_show, $header_left, $header_top, $name_left, $name_top, $logo_left, $logo_top, $code_left, $code_top, $template_url);
                showUserQecode($upload_path, '', $upload_path . '/thumb_template' . 'qrcode_' . $id . '_' . $this->instance_id . '.png', '', $web_info['logo'], '', request()->post(), $upload_path . '/qrcode_template_' . $id . '_' . $this->instance_id . '.png');
            }
            return AjaxReturn($res);
        } else {
            $id = request()->get('id', 0);
            if (empty($id)) {
                $info = $weixin->getDetailWeixinQrcodeTemplate(0);
            } else {
                $info = $weixin->getDetailWeixinQrcodeTemplate($id);
            }
            $this->assign('id', $id);
            $this->assign("info", $info);
            $this->assign('web_info', $web_info);
            return view($this->style . 'Wchat/qrcode');
        }
    }

    /**
     * 回复设置
     */
    public function replayConfig()
    {
        $type = request()->get('type', 1);
        $child_menu_list = array(
            array(
                'url' => "wchat/replayConfig?type=1",
                'menu_name' => "关注时回复",
                "active" => $type == 1 ? 1 : 0
            ),
            array(
                'url' => "wchat/replayConfig?type=2",
                'menu_name' => "关键字回复",
                "active" => $type == 2 ? 1 : 0
            ),
            array(
                'url' => "wchat/replayConfig?type=3",
                'menu_name' => "默认回复",
                "active" => $type == 3 ? 1 : 0
            )
        );
        $this->assign('child_menu_list', $child_menu_list);
        $this->assign('type', $type);
        if ($type == 1) {
            $weixin = new Weixin();
            $info = $weixin->getFollowReplayDetail([
                'instance_id' => $this->instance_id
            ]);
            $this->assign('info', $info);
        } else 
            if ($type == 2) {} else 
                if ($type == 3) {
                    $weixin = new Weixin();
                    $info = $weixin->getDefaultReplayDetail([
                        'instance_id' => $this->instance_id
                    ]);
                    $this->assign('info', $info);
                }
        return view($this->style . 'Wchat/replayConfig');
    }

    /**
     * 添加 或 修改 关注时回复
     */
    public function addOrUpdateFollowReply()
    {
        $weixin = new Weixin();
        $id = request()->post('id', - 1);
        $replay_media_id = request()->post('media_id', 0);
        $res = - 1;
        if ($id < 0) {
            $res = - 1;
        } else 
            if ($id == 0) {
                if ($replay_media_id > 0) {
                    $res = $weixin->addFollowReplay($this->instance_id, $replay_media_id, 0);
                } else {
                    $res = - 1;
                }
            } else 
                if ($id > 0) {
                    if ($replay_media_id > 0) {
                        $res = $weixin->updateFollowReplay($id, $this->instance_id, $replay_media_id, 0);
                    } else {
                        $res = - 1;
                    }
                }
        return AjaxReturn($res);
    }

    /**
     * 添加 或 修改 关注时回复
     */
    public function addOrUpdateDefaultReply()
    {
        $weixin = new Weixin();
        $id = request()->post('id', - 1);
        $replay_media_id = request()->post('media_id', 0);
        $res = - 1;
        if ($id < 0) {
            $res = - 1;
        } else 
            if ($id == 0) {
                if ($replay_media_id > 0) {
                    $res = $weixin->addDefaultReplay($this->instance_id, $replay_media_id, 0);
                } else {
                    $res = - 1;
                }
            } else 
                if ($id > 0) {
                    if ($replay_media_id > 0) {
                        $res = $weixin->updateDefaultReplay($id, $this->instance_id, $replay_media_id, 0);
                    } else {
                        $res = - 1;
                    }
                }
        return AjaxReturn($res);
    }

    /**
     * 删除图文消息
     *
     * @return number
     */
    public function deleteWeixinMedia()
    {
        $media_id = request()->post('media_id', '');
        $res = 0;
        if (! empty($media_id)) {
            $weixin = new Weixin();
            $res = $weixin->deleteWeixinMedia($media_id);
        }
        return $res;
    }

    /**
     * 删除图文详情页列表
     */
    public function deleteWeixinMediaDetail()
    {
        $id = request()->post('id', '');
        $res = 0;
        if (! empty($id)) {
            $weixin = new Weixin();
            $res = $weixin->deleteWeixinMediaDetail($id);
        }
        return $res;
    }

    /**
     */
    public function materialMessage()
    {
        $type = request()->get('type', 0);
        $child_menu_list = array(
            array(
                'url' => "wchat/materialMessage",
                'menu_name' => "全部",
                "active" => $type == 0 ? 1 : 0
            ),
            array(
                'url' => "wchat/materialMessage?type=1",
                'menu_name' => "文本",
                "active" => $type == 1 ? 1 : 0
            ),
            array(
                'url' => "wchat/materialMessage?type=2",
                'menu_name' => "单图文",
                "active" => $type == 2 ? 1 : 0
            ),
            array(
                'url' => "wchat/materialMessage?type=3",
                'menu_name' => "多图文",
                "active" => $type == 3 ? 1 : 0
            )
        );
        if (request()->isAjax()) {
            $type = request()->post('type', 0);
            $search_text = request()->post('search_text', '');
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $weixin = new Weixin();
            $condition = array();
            if ($type != 0) {
                $condition['type'] = $type;
            }
            $condition['title'] = array(
                'like',
                '%' . $search_text . '%'
            );
            $condition = array_filter($condition);
            $list = $weixin->getWeixinMediaList($page_index, $page_size, $condition, 'create_time desc');
            return $list;
        }
        $this->assign('type', $type);
        $this->assign('child_menu_list', $child_menu_list);
        return view($this->style . 'Wchat/materialMessage');
    }

    /**
     * 分享内容设置
     */
    public function shareConfig()
    {
        $shop = new Shop();
        if (request()->isAjax()) {
            $goods_param_1 = request()->post('goods_param_1', '');
            $goods_param_2 = request()->post('goods_param_2', '');
            $shop_param_1 = request()->post('shop_param_1', '');
            $shop_param_2 = request()->post('shop_param_2', '');
            $shop_param_3 = request()->post('shop_param_3', '');
            $qrcode_param_1 = request()->post('qrcode_param_1', '');
            $qrcode_param_2 = request()->post('qrcode_param_2', '');
            $res = $shop->updateShopShareCinfig($this->instance_id, $goods_param_1, $goods_param_2, $shop_param_1, $shop_param_2, $shop_param_3, $qrcode_param_1, $qrcode_param_2);
            return AjaxReturn($res);
        }
        $config = $shop->getShopShareConfig($this->instance_id);
        $this->assign("config", $config);
        return view($this->style . 'Wchat/shareConfig');
    }

    /**
     * 模板消息设置
     */
    public function templateMessage()
    {
        return view($this->style . 'Wchat/templateMessage');
    }

    /**
     * 一键关注设置
     */
    public function oneKeySubscribe()
    {
        $weixin = new Weixin();
        if (request()->isAjax()) {
            $url = request()->post('url', '');
            $res = $weixin->setInsanceOneKeySubscribe($this->instance_id, $url);
            return AjaxReturn($res);
        }
        $data = $weixin->getInstanceOneKeySubscribe($this->instance_id);
        $this->assign('one_key_url', $data);
        return view($this->style . 'Wchat/oneKeySubscribe');
    }

    /**
     * 添加 消息
     */
    public function addMedia()
    {
        if (request()->isAjax()) {
            $type = request()->post('type', '');
            $title = request()->post('title', '');
            $content = request()->post('content', '');
            $sort = 0;
            $weixin = new Weixin();
            $res = $weixin->addWeixinMedia($title, $this->instance_id, $type, $sort, $content);
            return AjaxReturn($res);
        }
        return view($this->style . 'Wchat/addMedia');
    }

    /**
     * 修改消息素材
     */
    public function updateMedia()
    {
        $weixin = new Weixin();
        if (request()->isAjax()) {
            $media_id = request()->post('media_id', 0);
            $type = request()->post('type', '');
            $title = request()->post('title', '');
            $content = request()->post('content', '');
            $sort = 0;
            $res = $weixin->updateWeixinMedia($media_id, $title, $this->instance_id, $type, $sort, $content);
            return AjaxReturn($res);
        }
        $media_id = request()->get('media_id', 0);
        $info = $weixin->getWeixinMediaDetail($media_id);
        $this->assign('info', $info);
        return view($this->style . 'Wchat/updateMedia');
    }

    /**
     * ajax 加载 选择素材 弹框数据
     */
    public function onloadMaterial()
    {
        $type = request()->post('type', 0);
        $search_text = request()->post('search_text', '');
        $page_index = request()->post("page_index", 1);
        $page_size = request()->post("page_size", PAGESIZE);
        $weixin = new Weixin();
        $condition = array();
        if ($type != 0) {
            $condition['type'] = $type;
        }
        $condition['title'] = array(
            'like',
            '%' . $search_text . '%'
        );
        $condition = array_filter($condition);
        $list = $weixin->getWeixinMediaList($page_index, $page_size, $condition, 'create_time desc');
        return $list;
    }

    /**
     * 删除 回复
     *
     * @return unknown[]
     */
    public function delReply()
    {
        $type = request()->post('type', '');
        if ($type == '') {
            return AjaxReturn(- 1);
        } else {
            if ($type == 1) {
                // 删除 关注时回复
                $weixin = new Weixin();
                $res = $weixin->deleteFollowReplay($this->instance_id);
                return AjaxReturn($res);
            } else 
                if ($type == 3) {
                    // 删除 关注时回复
                    $weixin = new Weixin();
                    $res = $weixin->deleteDefaultReplay($this->instance_id);
                    return AjaxReturn($res);
                }
        }
    }

    /**
     * 关键字 回复
     */
    public function keyReplayList()
    {
        $weixin = new Weixin();
        $list = $weixin->getKeyReplayList(1, 0, [
            'instance_id' => $this->instance_id
        ]);
        return $list;
    }

    /**
     * 添加 或 修改 关键字 回复
     */
    public function addOrUpdateKeyReplay()
    {
        $weixin = new Weixin();
        if (request()->isAjax()) {
            $id = request()->post('id', - 1);
            $key = request()->post('key', '');
            $match_type = request()->post('match_type', 1);
            $replay_media_id = request()->post('media_id', 0);
            $sort = 0;
            if ($id > 0) {
                $res = $weixin->updateKeyReplay($id, $this->instance_id, $key, $match_type, $replay_media_id, $sort);
            } else 
                if ($id == 0) {
                    $res = $weixin->addKeyReplay($this->instance_id, $key, $match_type, $replay_media_id, $sort);
                } else 
                    if ($id < 0) {
                        $res = - 1;
                    }
            return AjaxReturn($res);
        }
        $id = request()->get('id', 0);
        $this->assign('id', $id);
        $info = array(
            'key' => '',
            'match_type' => 1,
            'reply_media_id' => 0,
            'madie_info' => array()
        );
        if ($id > 0) {
            $info = $weixin->getKeyReplyDetail($id);
        }
        $secend_menu['module_name'] = "编辑回复";
        $child_menu_list = array(
            array(
                'url' => "Wchat/addOrUpdateKeyReplay.html?id=" . $id,
                'menu_name' => "编辑回复",
                "active" => 1
            )
        );
        
        if (! empty($id)) {
            $this->assign("secend_menu", $secend_menu);
            $this->assign('child_menu_list', $child_menu_list);
        }
        $this->assign('info', $info);
        return view($this->style . 'Wchat/addOrUpdateKeyReplay');
    }

    /**
     * 删除 回复
     *
     * @return unknown[]
     */
    public function delKeyReply()
    {
        $id = request()->post('id', '');
        if ($id == '') {
            return AjaxReturn(- 1);
        } else {
            // 删除 关注时回复
            $weixin = new Weixin();
            $res = $weixin->deleteKeyReplay($id);
            return AjaxReturn($res);
        }
    }

    public function saveQrcodeConfig()
    {}

    /**
     * 模板列表
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function weixinQrcodeTemplate()
    {
        $weixin = new Weixin();
        $template_list = $weixin->getWeixinQrcodeTemplate($this->instance_id);
        $this->assign("template_list", $template_list);
        return view($this->style . 'Wchat/weixinQrcodeTemplate');
    }

    /**
     * 修改模板是否被使用
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function modifyWeixinQrcodeTemplateValid()
    {
        $id = request()->post('id', '');
        $weixin = new Weixin();
        $retval = $weixin->modifyWeixinQrcodeTemplateCheck($this->instance_id, $id);
        return AjaxReturn($retval);
    }

    /**
     * 删除模板
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function deleteWeixinQrcodeTemplateValid()
    {
        $id = request()->post('id', '');
        $weixin = new Weixin();
        $retval = $weixin->deleteWeixinQrcodeTemplate($id, $this->instance_id);
        return AjaxReturn($retval);
    }

    /**
     * 模板消息列表
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function messageTemplate()
    {
        $WeixinMessage = new WeixinMessage();
        $message = $WeixinMessage->getWeixinInstanceMsg($this->instance_id);
        $this->assign("message", $message);
        return view($this->style . 'Wchat/messageTemplate');
    }

    public function testSend()
    {
        $weixin_message = new WeixinMessage();
        $weixin = new Weixin();
        // $res = $weixin_message->sendWeixinOrderCreateMessage(1);
        $weixin->addUserMessageReplay(1, 1, 'text', 'this is kefu replay message!');
        $res = $weixin_message->sendMessageToUser('oXTarwCCbPb9eouZmwCr6CHtNI0I', 'text', 'this is kefu replay message!');
        var_dump($res);
    }
}   
