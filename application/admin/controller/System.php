<?php
/**
 * System.php
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

use data\service\Album as Album;
use data\service\Goods as Goods;
use data\service\GoodsBrand as GoodsBrand;
use data\service\GoodsCategory as GoodsCategory;
use data\service\GoodsGroup;
use data\service\Platform;

/**
 * 系统模块控制器
 *
 * @author Administrator
 *        
 */
class System extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 更新缓存
     */
    public function deleteCache()
    {
        $retval = NiuDelDir('./runtime/cache');
        if($retval){
            $retval = NiuDelDir('./runtime/temp');
        }
        return $retval;
    }

    /**
     * 图片选择
     * 2016年11月21日 16:23:35
     */
    public function dialogAlbumList()
    {
        $number = request()->get('number', 1);
        $spec_id = request()->get('spec_id', 0);
        $spec_value_id = request()->get('spec_value_id', 0);
        $upload_type = request()->get('upload_type', 1);
        $this->assign("number", $number);
        $this->assign("spec_id", $spec_id);
        $this->assign("spec_value_id", $spec_value_id);
        $this->assign("upload_type", $upload_type);
        $album = new Album();
        $default_album_detail = $album->getDefaultAlbumDetail();
        $this->assign('default_album_id', $default_album_detail['album_id']);
        return view($this->style . "System/dialogAlbumList");
    }

    /**
     * 模块列表
     */
    public function moduleList()
    {
        $condition = array(
            'pid' => 0,
            'module' => $this->module
        );
        $frist_list = $this->website->getSystemModuleList(1, 0, $condition, 'pid,sort');
        $frist_list = $frist_list['data'];
        $list = array();
        foreach ($frist_list as $k => $v) {
            $submenu = $this->website->getSystemModuleList(1, 0, 'pid=' . $v['module_id'], 'pid,sort');
            $v['sub_menu'] = $submenu['data'];
            if (! empty($submenu['data'])) {
                foreach ($submenu['data'] as $ks => $vs) {
                    $sub_sub_menu = $this->website->getSystemModuleList(1, 0, 'pid=' . $vs['module_id'], 'pid,sort');
                    $vs['sub_menu'] = $sub_sub_menu['data'];
                    if (! empty($sub_sub_menu['data'])) {
                        foreach ($sub_sub_menu['data'] as $kss => $vss) {
                            $sub_sub_sub_menu = $this->website->getSystemModuleList(1, 0, 'pid=' . $vss['module_id'], 'pid,sort');
                            $vss['sub_menu'] = $sub_sub_sub_menu['data'];
                            if (! empty($sub_sub_sub_menu['data'])) {
                                foreach ($sub_sub_sub_menu['data'] as $ksss => $vsss) {
                                    $sub_sub_sub_sub_menu = $this->website->getSystemModuleList(1, 0, 'pid=' . $vsss['module_id'], 'pid,sort');
                                    $vsss['sub_menu'] = $sub_sub_sub_sub_menu['data'];
                                }
                            }
                        }
                    }
                }
            }
        }
        $list = $frist_list;
        $this->assign("list", $list);
        return view($this->style . 'System/moduleList');
    }

    /**
     * 添加模块
     */
    public function addModule()
    {
        if (request()->isAjax()) {
            $module_id = 0;
            $module_name = request()->post('module_name', '');
            $controller = request()->post('controller', '');
            $method = request()->post('method', '');
            $pid = request()->post('pid', '');
            $url = request()->post('url', '');
            $is_menu = request()->post('is_menu', '');
            $is_control_auth = request()->post('is_control_auth', '');
            $is_dev = request()->post('id', '');
            $sort = request()->post('sort', '');
            $module_picture = request()->post('module_picture', '');
            $desc = request()->post('desc', '');
            $icon_class = '';
            $retval = $this->website->addSytemModule($module_name, $controller, $method, $pid, $url, $is_menu, $is_dev, $sort, $module_picture, $desc, $icon_class, $is_control_auth);
            return AjaxReturn($retval, $retval);
        } else {
            $condition = array(
                'pid' => 0,
                'module' => $this->module
            );
            $frist_list = $this->website->getSystemModuleList(1, 100, $condition, 'pid,sort');
            $frist_list = $frist_list['data'];
            $list = array();
            foreach ($frist_list as $k => $v) {
                $submenu = $this->website->getSystemModuleList(1, 100, 'pid=' . $v['module_id'], 'pid,sort');
                $list[$k]['data'] = $v;
                $list[$k]['sub_menu'] = $submenu['data'];
            }
            $this->assign("list", $list);
            $pid = request()->get('pid', '');
            $this->assign("pid", $pid);
            return view($this->style . 'System/addModule');
        }
    }

    /**
     * 修改模块
     */
    public function editModule()
    {
        if (request()->isAjax()) {
            $module_id = request()->post('module_id', '');
            $module_name = request()->post('module_name', '');
            $controller = request()->post('controller', '');
            $method = request()->post('method', '');
            $pid = request()->post('pid', '');
            $url = request()->post('url', '');
            $is_menu = request()->post('is_menu', '');
            $is_control_auth = request()->post('is_control_auth', '');
            $is_dev = request()->post('id', '');
            $sort = request()->post('sort', '');
            $module_picture = request()->post('module_picture', '');
            $desc = request()->post('desc', '');
            $icon_class = '';
            $retval = $this->website->updateSystemModule($module_id, $module_name, $controller, $method, $pid, $url, $is_menu, $is_dev, $sort, $module_picture, $desc, $icon_class, $is_control_auth);
            return AjaxReturn($retval);
        } else {
            $module_id = request()->get('module_id', '');
            if (! is_numeric($module_id)) {
                $this->error('未获取到信息');
            }
            $module_info = $this->website->getSystemModuleInfo($module_id);
            $condition = array(
                'pid' => 0,
                'module' => $this->module
            );
            if ($module_info['level'] == 1) {
                $list = array();
            } else 
                if ($module_info['level'] == 2) {
                    $frist_list = $this->website->getSystemModuleList(1, 100, $condition, 'pid,sort');
                    $list = array();
                    foreach ($frist_list['data'] as $k => $v) {
                        $list[$k]['data'] = $v;
                        $list[$k]['sub_menu'] = array();
                    }
                } else 
                    if ($module_info['level'] == 3) {
                        $frist_list = $this->website->getSystemModuleList(1, 100, $condition, 'pid,sort');
                        $frist_list = $frist_list['data'];
                        $list = array();
                        foreach ($frist_list as $k => $v) {
                            $submenu = $this->website->getSystemModuleList(1, 100, 'pid=' . $v['module_id'], 'pid,sort');
                            $list[$k]['data'] = $v;
                            $list[$k]['sub_menu'] = $submenu['data'];
                        }
                    }
            $this->assign('module_info', $module_info);
            $this->assign("list", $list);
            return view($this->style . 'System/editModule');
        }
    }

    /**
     * 删除模块
     */
    public function delModule()
    {
        $module_id = request()->post('module_id', '');
        $retval = $this->website->deleteSystemModule($module_id);
        return AjaxReturn($retval);
    }

    /**
     * 获取图片分组
     */
    public function albumList()
    {
        $album = new Album();
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $condition = array(
                'shop_id' => $this->instance_id,
                'album_name' => array(
                    'like',
                    '%' . $search_text . '%'
                )
            );
            $order = " create_time desc";
            $retval = $album->getAlbumClassList($page_index, $page_size, $condition, $order);
            return $retval;
        } else {
            $default_album_detail = $album->getDefaultAlbumDetail();
            $this->assign('default_album_id', $default_album_detail['album_id']);
            return view($this->style . "System/albumList");
        }
    }

    /**
     * 创建相册
     */
    public function addAlbumClass()
    {
        $album_name = request()->post('album_name', '');
        $sort = request()->post('sort', 0);
        $album = new Album();
        $retval = $album->addAlbumClass($album_name, $sort, 0, '', 0, $this->instance_id);
        return AjaxReturn($retval);
    }

    /**
     * 删除相册
     */
    public function deleteAlbumClass()
    {
        $aclass_id_array = request()->post('aclass_id_array', '');
        $album = new Album();
        $retval = $album->deleteAlbumClass($aclass_id_array);
        return AjaxReturn($retval);
    }

    /**
     * 相册图片列表
     */
    public function albumPictureList()
    {
        $album = new Album();
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $album_id = request()->post("album_id", 0);
            $is_use = request()->post("is_use", 0);
            $condition = array();
            $condition["album_id"] = $album_id;
            if ($is_use > 0) {
                $img_array = $album->getGoodsAlbumUsePictureQuery([
                    "shop_id" => $this->instance_id
                ]);
                if (! empty($img_array)) {
                    $img_string = implode(",", $img_array);
                    $condition["pic_id"] = [
                        "not in",
                        $img_string
                    ];
                }
            }
            $list = $album->getPictureList($page_index, $page_size, $condition);
            foreach ($list["data"] as $k => $v) {
                $list["data"][$k]["upload_time"] = date("Y-m-d", $v["upload_time"]);
            }
            return $list;
        } else {
            $album_class_list = $album->getAlbumClassList(1, 10, "", "create_time desc");
            $this->assign("album_class_list", $album_class_list['data']);
            $album_id = request()->get('album_id', 0);
            $url = "System/albumPictureList";
            if ($album_id > 0) {
                $url .= "?album_id=" . $album_id;
            }
            $child_menu_list = array(
                array(
                    'url' => "System/albumList",
                    'menu_name' => "相册",
                    "active" => 0
                ),
                array(
                    'url' => $url,
                    'menu_name' => "图片",
                    "active" => 1
                )
            );
            $album_detial = $album->getAlbumClassDetail($album_id);
            $this->assign('child_menu_list', $child_menu_list);
            $this->assign("album_name", $album_detial['album_name']);
            $this->assign("album_id", $album_id);
            $this->assign("album_cover", $album_detial['album_cover']);
            return view($this->style . "System/albumPictureList");
        }
    }

    /**
     * 相册图片列表
     */
    public function dialogAlbumPictureList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post('pageIndex', '');
            $album_id = request()->post('album_id', '');
            $condition = array(
                'album_id' => $album_id
            );
            $album = new Album();
            $list = $album->getPictureList($page_index, 10, $condition);
            foreach ($list["data"] as $k => $v) {
                $list["data"][$k]["upload_time"] = date("Y-m-d", $v["upload_time"]);
            }
            return $list;
        } else {
            return view($this->style . "System/dialogAlbumPictureList");
        }
    }

    /**
     * 删除图片
     *
     * @param unknown $pic_id_array            
     * @return unknown
     */
    public function deletePicture()
    {
        $pic_id_array = request()->post('pic_id_array', '');
        $album = new Album();
        $retval = $album->deletePicture($pic_id_array);
        return AjaxReturn($retval);
    }

    /**
     * 获取相册详情
     *
     * @return Ambigous <\think\static, multitype:, \think\db\false, PDOStatement, string, \think\Model, \PDOStatement, \think\db\mixed, multitype:a r y s t i n g Q u e \ C l o , \think\db\Query, NULL>
     */
    public function getAlbumClassDetail()
    {
        $album_id = request()->post('album_id', '');
        $album = new Album();
        $retval = $album->getAlbumClassDetail($album_id);
        return $retval;
    }

    /**
     * 修改相册
     */
    public function updateAlbumClass()
    {
        $album_id = request()->post('album_id', '');
        $aclass_name = request()->post('album_name', '');
        $aclass_sort = request()->post('sort', '');
        $album_cover = request()->post('album_cover', '');
        
        $album = new Album();
        $retval = $album->updateAlbumClass($album_id, $aclass_name, $aclass_sort, 0, $album_cover);
        return AjaxReturn($retval);
    }

    /**
     * 删除制定路径文件
     */
    function delete_file()
    {
        $file_url = request()->post('file_url', '');
        if (file_exists($file_url)) {
            @unlink($file_url);
            $retval = array(
                'code' => 1,
                'message' => '文件删除成功'
            );
        } else {
            $retval = array(
                'code' => 0,
                'message' => '文件不存在'
            );
        }
        return $retval;
    }

    /**
     * 查询相册列表发布商品弹出相册框用到了
     * 创建时间：2017年11月10日 15:16:54 王永杰
     *
     * @return number[]|unknown[]|unknown|string
     */
    public function getAlbumClassList()
    {
        $page_index = request()->post("page_index", 1);
        $page_size = request()->post('page_size', PAGESIZE);
        $condition = array(
            'shop_id' => $this->instance_id
        );
        $album = new Album();
        $retval = $album->getAlbumClassList($page_index, $page_size, $condition, "is_default desc,create_time desc");
        return $retval;
    }

    /**
     * 查询相册列表，相册列表界面用
     * 创建时间：2017年11月11日 09:58:58
     *
     * @return number[]|unknown[]|unknown|string
     */
    public function getAlbumClassListByAlbumPicture()
    {
        $page_index = request()->post("page_index", 1);
        $page_size = request()->post('page_size', PAGESIZE);
        $album_name = request()->post("album_name", "");
        $search_name = request()->post("search_name", "");
        //排除当前选中的相册，然后模糊查询
        $condition = array(
            'shop_id' => $this->instance_id,
            'album_name' => array(
                [
                    "like",
                    "%$search_name%"
                ],
                [
                    'eq',
                    $album_name
                ],
                'or'
            )
        );
        
        $album = new Album();
        $retval = $album->getAlbumClassList($page_index, $page_size, $condition, "create_time desc");
        return $retval;
    }

    /**
     * 修改单个字段
     */
    public function modifyField()
    {
        $fieldid = request()->post('fieldid', '');
        $fieldname = request()->post('fieldname', '');
        $fieldvalue = request()->post('fieldvalue', '');
        $retval = $this->website->ModifyModuleField($fieldid, $fieldname, $fieldvalue);
        return AjaxReturn($retval);
    }

    /**
     * 图片名称修改
     */
    public function modifyAlbumPictureName()
    {
        $pic_id = request()->post('pic_id', '');
        $pic_name = request()->post('pic_name', '');
        $album = new Album();
        $retval = $album->ModifyAlbumPictureName($pic_id, $pic_name);
        return AjaxReturn($retval);
    }

    /**
     * 转移图片所在相册
     */
    public function modifyAlbumPictureClass()
    {
        $pic_id = request()->post('pic_id', '');
        $album_id = request()->post('album_id', '');
        $album = new Album();
        $retval = $album->ModifyAlbumPictureClass($pic_id, $album_id);
        return AjaxReturn($retval);
    }

    /**
     * 设此图片为本相册的封面
     */
    function modifyAlbumClassCover()
    {
        $pic_id = request()->post('pic_id', '');
        $album_id = request()->post('album_id', '');
        $album = new Album();
        $retval = $album->ModifyAlbumClassCover($pic_id, $album_id);
        return AjaxReturn($retval);
    }

    /**
     * 广告位列表
     */
    public function shopAdvPositionList()
    {
        $terminal = request()->get("terminal", 1); // PC端或手机端（终端）
        $child_menu_list = array(
            array(
                'url' => "system/shopadvlist",
                'menu_name' => "广告列表",
                "active" => 0
            ),
            array(
                'url' => "system/shopadvpositionlist",
                'menu_name' => "广告位管理",
                "active" => 1
            )
        );
        $this->assign("terminal", $terminal);
        $this->assign('child_menu_list', $child_menu_list);
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $ap_name = request()->post('ap_name', '');
            $ap_dis = request()->post('ap_display', '');
            $condition['type'] = request()->post("type", "1");
            $platform = new Platform();
            if ($ap_dis != "") {
                $condition['ap_display'] = $ap_dis;
            }
            if (! empty($ap_name)) {
                $condition["ap_name"] = array(
                    "like",
                    "%" . $ap_name . "%"
                );
            }
            $condition['instance_id'] = $this->instance_id;
            $list = $platform->getPlatformAdvPositionList($page_index, $page_size, $condition);
            return $list;
        }
        return view($this->style . "System/shopAdvPositionList");
    }

    /**
     * 添加广告位
     *
     * @return \think\response\View
     */
    public function addShopAdvPosition()
    {
        if (request()->isAjax()) {
            $ap_name = request()->post('ap_name', '');
            $ap_intro = request()->post('ap_intro', '');
            $ap_class = request()->post('ap_class', 0);
            $ap_dis = request()->post('ap_display', 2);
            $is_use = request()->post('is_use', 0);
            $ap_height = request()->post('ap_height', '');
            $ap_width = request()->post('ap_width', '');
            $default_content = request()->post('default_content', '');
            $ap_background_color = request()->post('ap_background_color', '');
            $type = request()->post('type', '');
            $ap_keyword = request()->post("ap_keyword", "");
            $platform = new Platform();
            $res = $platform->addPlatformAdvPosition($this->instance_id, $ap_name, $ap_intro, $ap_class, $ap_dis, $is_use, $ap_height, $ap_width, $default_content, $ap_background_color, $type, $ap_keyword);
            return AjaxReturn($res);
        }
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "system/shopadvpositionlist",
                    'menu_name' => "广告位管理",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "System/addShopAdvPosition");
    }

    /**
     * 修改广告位
     */
    public function updateShopAdvPosition()
    {
        $platform = new Platform();
        if (request()->isAjax()) {
            $ap_id = request()->post('ap_id', '');
            $ap_name = request()->post('ap_name', '');
            $ap_intro = request()->post('ap_intro', '');
            $ap_class = request()->post('ap_class', 0);
            $ap_dis = request()->post('ap_display', 2);
            $is_use = request()->post('is_use', 0);
            $ap_height = request()->post('ap_height', '');
            $ap_width = request()->post('ap_width', '');
            $default_content = request()->post('default_content', '');
            $ap_background_color = request()->post('ap_background_color', '');
            $type = request()->post('type', '');
            $ap_keyword = request()->post("ap_keyword", '');
            $res = $platform->updatePlatformAdvPosition($ap_id, $this->instance_id, $ap_name, $ap_intro, $ap_class, $ap_dis, $is_use, $ap_height, $ap_width, $default_content, $ap_background_color, $type, $ap_keyword);
            return AjaxReturn($res);
        }
        $id = request()->get('ap_id', '');
        if (! is_numeric($id)) {
            $this->error('未获取到信息');
        }
        $info = $platform->getPlatformAdvPositionDetail($id);
        $this->assign('info', $info);
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "system/shopadvpositionlist",
                    'menu_name' => "广告位管理",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "System/updateShopAdvPosition");
    }
    
    /**
     * 检测广告位关键字是否存在
     */
    public function check_apkeyword(){
        $platform = new Platform();
        if (request()->isAjax()) {
            $ap_keyword = request()->post("ap_keyword", '');
            $res = 0;
            if($ap_keyword != ""){
               $res = $platform -> check_apKeyword_is_exists($ap_keyword);
            }
            return $res;
        }
    }
    
    /**
     * 广告列表 （广告位下级）
     *
     * @return number[]|unknown[]|\think\response\View
     */
    public function shopAdvList()
    {
        $child_menu_list = array(
            array(
                'url' => "system/shopadvlist",
                'menu_name' => "广告列表",
                "active" => 1
            ),
            array(
                'url' => "system/shopadvpositionlist",
                'menu_name' => "广告位管理",
                "active" => 0
            )       
        );
        $this->assign('child_menu_list', $child_menu_list);
        $ap_id = request()->get('ap_id', '');
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $search_text = request()->post('search_text', '');
            $type = request()->post("type" ,1);
            $platform = new Platform();
            $list = $platform->adminGetAdvList($page_index, $page_size, [
                'adv_title' => array('like','%' . $search_text . '%'),
                'npap.instance_id' => $this->instance_id,
                'npap.type' => $type
            ], 'slide_sort desc');
            return $list;
        }
        return view($this->style . "System/shopAdvList");
    }

    /**
     * 修改广告排序
     */
    public function modifyAdvSort()
    {
        if (request()->isAjax()) {
            $adv_id = request()->post('fieldid', '');
            $slide_sort = request()->post('fieldvalue', '');
            $platform = new Platform();
            $res = $platform->updateAdvSlideSort($adv_id, $slide_sort);
            return AjaxReturn($res);
        }
    }

    /**
     * 添加广告
     */
    public function addShopAdv()
    {
        $ap_id = request()->get('ap_id', '');
        $this->assign("ap_id", $ap_id);
        $platform = new Platform();
        if (request()->isAjax()) {
            $ap_id = request()->post('ap_id', '');
            $adv_title = request()->post('adv_title', '');
            $adv_url = request()->post('adv_url', '');
            $adv_image = request()->post('adv_image', '');
            $slide_sort = request()->post('slide_sort', '');
            $background = request()->post('background', '');
            $adv_code = request()->post("adv_code", "");
            $res = $platform->addPlatformAdv($ap_id, $adv_title, $adv_url, $adv_image, $slide_sort, $background, $adv_code);
            return AjaxReturn($res);
        }
        $type = request()->get("type",1);
        $platform = new Platform();
        $list = $platform->getPlatformAdvPositionList(1, 0, ["instance_id" => $this->instance_id, "type"=>$type], '', 'ap_id,ap_name,ap_class,ap_display');
        $this->assign('platform_adv_position_list', $list['data']);
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "system/shopadvlist",
                    'menu_name' => "广告列表",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "System/addShopAdv");
    }

    /**
     * 修改广告
     */
    public function updateShopAdv()
    {
        $platform = new Platform();
        if (request()->isAjax()) {
            $adv_id = request()->post('adv_id', '');
            $ap_id = request()->post('ap_id', '');
            $adv_title = request()->post('adv_title', '');
            $adv_url = request()->post('adv_url', '');
            $adv_image = request()->post('adv_image', '');
            $slide_sort = request()->post('slide_sort', '');
            $background = request()->post('background', '');
            $adv_code = request()->post("adv_code", "");
            $res = $platform->updatePlatformAdv($adv_id, $ap_id, $adv_title, $adv_url, $adv_image, $slide_sort, $background, $adv_code);
            return AjaxReturn($res);
        }
        $adv_id = request()->get('adv_id', '');
        if (! is_numeric($adv_id)) {
            $this->error('未获取信息');
        }
        $adv_info = $platform->getPlatformAdDetail($adv_id);
        $this->assign('adv_info', $adv_info);
        $type = request()->get("type",1);
        $platform = new Platform();
        $list = $platform->getPlatformAdvPositionList(1, 0, ["instance_id" => $this->instance_id, "type"=>$type], '', 'ap_id,ap_name,ap_class,ap_display');
        $this->assign('platform_adv_position_list', $list['data']);
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "system/shopadvlist",
                    'menu_name' => "广告列表",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "System/updateShopAdv");
    }

    /**
     * 删除广告
     */
    public function delShopAdv()
    {
        $adv_id = request()->post('adv_id', '');
        $platform = new Platform();
        $res = $platform->deletePlatformAdv($adv_id);
        return AjaxReturn($res);
    }

    /**
     * pc端促销 版块
     */
    public function goodsRecommendClass()
    {
        $this->pcConfigChildMenuList(2);
        $platform = new Platform();
        $condition = [
            'class_type' => 2,
            // 'is_use' => 1,
            'show_type' => 0
        ];
        $goods_recommend_class = $platform->getPlatformGoodsRecommendClass($condition);
        $this->assign('goods_recommend_class', $goods_recommend_class);
        $goods_category = new GoodsCategory();
        $category_list_1 = $goods_category->getGoodsCategoryList(1, 0, [
            'is_visible' => 1,
            'level' => 1
        ]);
        $this->assign("show_type", 0);
        $this->assign('category_list_1', $category_list_1['data']);
        return view($this->style . "System/goodsRecommendClass");
    }

    /**
     * 手机端促销板块
     */
    public function goodsRecommendClassMobile()
    {
        $child_menu_list = array(
            array(
                'url' => "config/updatenotice",
                'menu_name' => "首页公告",
                "active" => 0
            ),
            array(
                'url' => "system/goodsrecommendclassmobile",
                'menu_name' => "促销版块",
                "active" => 1
            ),
            array(
                'url' => 'config/waptemplate',
                'menu_name' => '手机模板',
                'active' => 0,
                'flag' => 8
            ),
            array(
                'url' => 'config/fixedtemplate',
                'menu_name' => '固定模板',
                'active' => 0
            ),
            array(
                'url' => 'config/customtemplatelist',
                'menu_name' => '自定义模板',
                'active' => 0,
                'flag' => 9
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        $platform = new Platform();
        $condition = [
            'class_type' => 2,
            'is_use' => 1,
            'show_type' => 1
        ];
        $goods_recommend_class = $platform->getPlatformGoodsRecommendClass($condition);
        $this->assign('goods_recommend_class', $goods_recommend_class);
        $goods_category = new GoodsCategory();
        $category_list_1 = $goods_category->getGoodsCategoryList(1, 0, [
            'is_visible' => 1,
            'level' => 1
        ]);
        $this->assign("show_type", 1);
        $this->assign('category_list_1', $category_list_1['data']);
        return view($this->style . "System/goodsRecommendClass");
    }

    /**
     * 获取促销版块 单个详情
     */
    public function getGoodsRecommendClass()
    {
        $class_id = request()->post('class_id', '');
        $platform = new Platform();
        $info = $platform->getPlatformGoodsRecommendClassDetail($class_id);
        return $info;
    }

    /**
     * 搜索商品
     */
    public function searchGoods()
    {
        $goods_name = request()->post('goods_name', '');
        $category_id = request()->post('category_id', '');
        $category_level = request()->post('category_level', '');
        $where['ng.goods_name'] = array(
            'like',
            '%' . $goods_name . '%'
        );
        $where['ng.category_id_' . $category_level] = $category_id;
        $where['ng.state'] = 1;
        $where = array_filter($where);
        $goods = new Goods();
        $list = $goods->getGoodsList(1, 0, $where);
        return $list;
    }

    /**
     * 编辑促销版块
     */
    public function updateGoodsRecommendClass()
    {
        $class_id = request()->post('class_id', 0);
        $class_name = request()->post('class_name', '');
        $goods_id_array = request()->post('goods_id_array', '');
        $sort = request()->post('sort', '');
        $show_type = request()->post('show_type', '');
        $platform = new Platform();
        $res = $platform->updatePlatformGoodsRecommendClass($class_id, $class_name, $sort, $goods_id_array, $show_type);
        return AjaxReturn($res);
    }

    /**
     * 输入框直接编辑促销版块名称
     */
    public function updaterecommendclass()
    {
        $class_id = request()->post('class_id', '0');
        $class_name = request()->post('class_name', '');
        $sort = request()->post('sort', '');
        $show_type = request()->post('show_type', '');
        $platform = new Platform();
        $res = $platform->updateGoodsRecommend($class_id, $class_name, $sort, $show_type);
        return AjaxReturn($res);
    }

    /**
     * 编辑促销版块是否启用
     */
    public function updaterecommendclassisuse()
    {
        $class_id = request()->post('class_id', '0');
        $is_use = request()->post('is_use', '');
        $show_type = request()->post('show_type', '');
        $platform = new Platform();
        $res = $platform->updatePlatformGoodsRecommendClassIsuse($class_id, $is_use, $show_type);
        return AjaxReturn($res);
    }

    /**
     * 重新编辑促销版块（通过更改商品来更改促销版块）
     */
    public function updateGoodsRecommend()
    {
        $class_id = request()->post('class_id', '0');
        $class_name = request()->post('class_name', '');
        $goods_id = request()->post('goods_id', '');
        $sort = request()->post('sort', '');
        $show_type = request()->post('show_type', '');
        $goods_id_old = request()->post('goods_id_old', '');
        $platform = new Platform();
        $res = $platform->updatePlatformGoodsRecommend($class_id, $class_name, $sort, $goods_id, $show_type, $goods_id_old);
        return AjaxReturn($res);
    }

    /**
     * 删除促销版块中商品
     */
    public function deleterecommend()
    {
        $class_id = request()->post('class_id', '');
        $goods_id = request()->post('goods_id', '');
        if ($class_id > 0 && $goods_id > 0) {
            $platform = new Platform();
            $res = $platform->deletePlatformGoodsRecommend($class_id, $goods_id);
            return AjaxReturn($res);
        } else {
            return AjaxReturn(0);
        }
    }

    /**
     * 删除 促销版块
     *
     * @return unknown[]
     */
    public function delGoodsRecommendClass()
    {
        $class_id = request()->post('class_id', 0);
        if ($class_id > 0) {
            $platform = new Platform();
            $res = $platform->deletePlatformGoodsRecommendClass($class_id);
            return AjaxReturn($res);
        } else {
            return AjaxReturn(0);
        }
    }

    /**
     * 首页版块 列表
     */
    public function blockList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $platform_block = new Platform();
            $block_list = $platform_block->webBlockList($page_index, $page_size, [
                'block_name' => array(
                    'like',
                    '%' . $search_text . '%'
                )
            ], 'sort', 'block_id, is_display, block_color, sort, block_name, create_time, modify_time');
            return $block_list;
        }
        $this->pcConfigChildMenuList(3);
        return view($this->style . "System/blockList");
    }

    /**
     * pc端子菜单列表
     * 2017年7月24日 14:25:56 王永杰
     *
     * @param $flag 1:导航管理，2：促销板块，3：首页楼层，4：首页公告，5：友情链接，6：热门搜索，7：默认搜索，8：手机模板，9：自定义模板            
     */
    public function pcConfigChildMenuList($flag)
    {
        $child_menu_list = array(
            array(
                'url' => "config/shopnavigationlist",
                'menu_name' => "导航管理",
                "active" => 0,
                'flag' => 1
            ),
            // array(
            // 'url' => "system/goodsrecommendclass",
            // 'menu_name' => "促销版块",
            // "active" => 0,
            // 'flag' => 2
            // ),
            // array(
            // 'url' => "system/blocklist",
            // 'menu_name' => "首页楼层",
            // "active" => 0,
            // 'flag' => 3
            // ),
            array(
                'url' => "system/goodscategoryblock",
                'menu_name' => "商品楼层",
                "active" => 0,
                'flag' => 3
            ),
            array(
                'url' => "config/usernotice",
                'menu_name' => "首页公告",
                "active" => 0,
                'flag' => 4
            ),
            
            array(
                'url' => "config/linklist",
                'menu_name' => "友情链接",
                "active" => 0,
                'flag' => 5
            ),
            array(
                'url' => "config/searchConfig?type=hot",
                'menu_name' => "热门搜索",
                "active" => 0,
                'flag' => 6
            ),
            array(
                'url' => "config/searchConfig?type=default",
                'menu_name' => "默认搜索",
                "active" => 0,
                'flag' => 7
            ),
            array(
                'url' => "config/pcTemplate",
                'menu_name' => "模板",
                "active" => 0,
                'flag' => 8
            )
        );
        foreach ($child_menu_list as $k => $v) {
            if ($v['flag'] == $flag) {
                $child_menu_list[$k]['active'] = 1;
            }
        }
        $this->assign('child_menu_list', $child_menu_list);
    }

    /**
     * 添加楼层
     */
    public function addBlock()
    {
        $platform = new Platform();
        if (request()->isAjax()) {
            $block_name = request()->post('block_name', '');
            $block_short_name = request()->post('block_short_name', '');
            $block_color = request()->post('block_color', '');
            $is_dis = request()->post('is_display', 1);
            $sort = request()->post('sort', 0);
            $recommend_ad_image_name = request()->post('recommend_ad_image_name', '');
            $recommend_ad_image = request()->post('recommend_ad_image', '');
            $recommend_ad_slide_name = request()->post('recommend_ad_slide_name', '');
            $recommend_ad_slide = $this->request->post('recommend_ad_slide', '');
            $recommend_ad_images_name = request()->post('recommend_ad_images_name', '');
            $recommend_ad_images = request()->post('recommend_ad_images', '');
            $recommend_brands = request()->post('recommend_brands', '');
            $recommend_categorys = request()->post('recommend_categorys', '');
            $recommend_goods_category_name_1 = request()->post('recommend_goods_category_name_1', '');
            $recommend_goods_category_1 = request()->post('recommend_goods_category_1', '');
            $recommend_goods_category_name_2 = request()->post('recommend_goods_category_name_2', '');
            $recommend_goods_category_2 = request()->post('recommend_goods_category_2', '');
            $recommend_goods_category_name_3 = request()->post('recommend_goods_category_name_3', '');
            $recommend_goods_category_3 = request()->post('recommend_goods_category_3', '');
            $res = $platform->addWebBlock($is_dis, $block_color, $sort, $block_name, $block_short_name, $recommend_ad_image_name, $recommend_ad_image, $recommend_ad_slide_name, $recommend_ad_slide, $recommend_ad_images_name, $recommend_ad_images, $recommend_brands, $recommend_categorys, $recommend_goods_category_name_1, $recommend_goods_category_1, $recommend_goods_category_name_2, $recommend_goods_category_2, $recommend_goods_category_name_3, $recommend_goods_category_3);
            return AjaxReturn($res);
        }
        // 获取所有品牌
        $goods_brand = new GoodsBrand();
        $goods_brand_list = $goods_brand->getGoodsBrandList(1, 0);
        $this->assign('goods_brand_list', $goods_brand_list['data']);
        
        // 获取商品分类
        $goods_category = new GoodsCategory();
        $category_list = $goods_category->getGoodsCategoryList(1, 0, [
            'is_visible' => 1
        ]);
        $this->assign('goods_category_list', $category_list['data']);
        
        // 获取单图 $recommend_ad_image_list， 多图$recommend_ad_images_list， 幻灯片$recommend_ad_slide_list 广告位
        $recommend_ad_image_list = $platform->getPlatformAdvPositionList(1, 0, [
            'ap_display' => 2,
            'is_use' => 1
        ]);
        $this->assign('recommend_ad_image_list', $recommend_ad_image_list['data']);
        $recommend_ad_images_list = $platform->getPlatformAdvPositionList(1, 0, [
            'ap_display' => 1,
            'is_use' => 1
        ]);
        $this->assign('recommend_ad_images_list', $recommend_ad_images_list['data']);
        $recommend_ad_slide_list = $platform->getPlatformAdvPositionList(1, 0, [
            'ap_display' => 0,
            'is_use' => 1
        ]);
        $this->assign('recommend_ad_slide_list', $recommend_ad_slide_list['data']);
        
        return view($this->style . "System/addBlock");
    }

    /**
     * 修改楼层
     */
    public function updateBlock()
    {
        $platform = new Platform();
        if (request()->isAjax()) {
            $block_id = request()->post('block_id', '');
            $block_name = request()->post('block_name', '');
            $block_short_name = request()->post('block_short_name', '');
            $block_color = request()->post('block_color', '');
            $is_dis = request()->post('is_display', 1);
            $sort = request()->post('sort', 0);
            $recommend_ad_image_name = request()->post('recommend_ad_image_name', '');
            $recommend_ad_image = request()->post('recommend_ad_image', '');
            $recommend_ad_slide_name = request()->post('recommend_ad_slide_name', '');
            $recommend_ad_slide = $this->request->post('recommend_ad_slide', '');
            $recommend_ad_images_name = request()->post('recommend_ad_images_name', '');
            $recommend_ad_images = request()->post('recommend_ad_images', '');
            $recommend_brands = request()->post('recommend_brands', '');
            $recommend_categorys = request()->post('recommend_categorys', '');
            $recommend_goods_category_name_1 = request()->post('recommend_goods_category_name_1', '');
            $recommend_goods_category_1 = request()->post('recommend_goods_category_1', '');
            $recommend_goods_category_name_2 = request()->post('recommend_goods_category_name_2', '');
            $recommend_goods_category_2 = request()->post('recommend_goods_category_2', '');
            $recommend_goods_category_name_3 = request()->post('recommend_goods_category_name_3', '');
            $recommend_goods_category_3 = request()->post('recommend_goods_category_3', '');
            $res = $platform->updateWebBlock($block_id, $is_dis, $block_color, $sort, $block_name, $block_short_name, $recommend_ad_image_name, $recommend_ad_image, $recommend_ad_slide_name, $recommend_ad_slide, $recommend_ad_images_name, $recommend_ad_images, $recommend_brands, $recommend_categorys, $recommend_goods_category_name_1, $recommend_goods_category_1, $recommend_goods_category_name_2, $recommend_goods_category_2, $recommend_goods_category_name_3, $recommend_goods_category_3);
            
            return AjaxReturn($res);
        }
        $block_id = request()->get('block_id', '');
        // 获取所有品牌
        $goods_brand = new GoodsBrand();
        $goods_brand_list = $goods_brand->getGoodsBrandList(1, 0);
        $this->assign('goods_brand_list', $goods_brand_list['data']);
        
        // 获取商品分类
        $goods_category = new GoodsCategory();
        $category_list = $goods_category->getGoodsCategoryList(1, 0, [
            'is_visible' => 1
        ]);
        $this->assign('goods_category_list', $category_list['data']);
        
        // 获取单图 $recommend_ad_image_list， 多图$recommend_ad_images_list， 幻灯片$recommend_ad_slide_list 广告位
        $recommend_ad_image_list = $platform->getPlatformAdvPositionList(1, 0, [
            'ap_display' => 2,
            'is_use' => 1
        ]);
        $this->assign('recommend_ad_image_list', $recommend_ad_image_list['data']);
        $recommend_ad_images_list = $platform->getPlatformAdvPositionList(1, 0, [
            'ap_display' => 1,
            'is_use' => 1
        ]);
        $this->assign('recommend_ad_images_list', $recommend_ad_images_list['data']);
        $recommend_ad_slide_list = $platform->getPlatformAdvPositionList(1, 0, [
            'ap_display' => 0,
            'is_use' => 1
        ]);
        $this->assign('recommend_ad_slide_list', $recommend_ad_slide_list['data']);
        // 获取详情
        $block_info = $platform->getWebBlockDetail($block_id);
        $block_info['base_info']['goods_category_name_1'] = $goods_category->getName($block_info['base_info']['recommend_goods_category_1'])['category_name'];
        $block_info['base_info']['goods_category_name_2'] = $goods_category->getName($block_info['base_info']['recommend_goods_category_2'])['category_name'];
        $block_info['base_info']['goods_category_name_3'] = $goods_category->getName($block_info['base_info']['recommend_goods_category_3'])['category_name'];
        // var_dump($block_info);
        $this->assign('block_info', $block_info['base_info']);
        return view($this->style . "System/updateBlock");
    }

    /**
     * 查询 商品分类 列表 通过 ajax
     */
    public function getGoodsCategoryListAjax()
    {
        $goods_category = new GoodsCategory();
        $goods_category_id = request()->post('category_id', 0);
        $list = $goods_category->getGoodsCategoryList(1, 0, [
            'pid' => $goods_category_id,
            'is_visible' => 1
        ], 'sort', 'category_id, category_name');
        return $list['data'];
    }

    /**
     * 删除 首页楼层
     */
    public function delBlock()
    {
        $platform = new Platform();
        $block_id = request()->post('block_id', 0);
        $res = $platform->deleteWebBlock($block_id);
        return AjaxReturn($res);
    }

    /**
     * 删除广告位
     */
    public function delPlatfromAdvPosition()
    {
        $ap_id = request()->post('ap_id', '');
        $platform = new Platform();
        $res = $platform->delPlatfromAdvPosition($ap_id);
        return AjaxReturn($res);
    }

    /**
     * 广告位的禁用和启用
     */
    public function setPlatformAdvPositionUse()
    {
        if (request()->isAjax()) {
            $ap_id = request()->post('ap_id', '');
            $is_use = request()->post('is_use', '');
            $platform = new Platform();
            $res = $platform->setPlatformAdvPositionUse($ap_id, $is_use);
            return AjaxReturn($res);
        }
    }

    /**
     * 首页板块的显示与不显示
     */
    public function setWebBlockIsBlock()
    {
        if (request()->isAjax()) {
            $block_id = request()->post('block_id', '');
            $is_dis = request()->post('is_display', '');
            $platform = new Platform();
            $res = $platform->setWebBlockIsBlock($block_id, $is_dis);
            return AjaxReturn($res);
        }
    }

    /**
     * 获取促销模块
     *
     * @return Ambigous <\think\static, multitype:, \think\db\false, PDOStatement, string, \think\Model, \PDOStatement, \think\db\mixed, multitype:a r y s t i n g Q u e \ C l o , \think\db\Query, NULL>
     */
    public function getPlatformGoodsRecommendClass()
    {
        $platform = new Platform();
        $condition = [
            'class_type' => 2,
            'is_use' => 1,
            'show_type' => 1
        ];
        $goods_recommend_class = $platform->getPlatformGoodsRecommendClass($condition);
        return $goods_recommend_class;
    }

    /**
     * 获取首页商品分类楼层
     *
     * @return unknown
     */
    public function getGoodsCategoryBlock()
    {
        $category = new GoodsCategory();
        $category_block = $category->getGoodsCategoryBlock($this->instance_id);
        return $category_block;
    }

    /**
     * 首页分类楼层
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function goodsCategoryBlock()
    {
        $this->pcConfigChildMenuList(3);
        return view($this->style . "System/goodsCategoryBlock");
    }

    /**
     * 设置首页商品分类楼层
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function setGoodsCategoryBlock()
    {
        $id = request()->post('id', 0);
        $category_alias = request()->post('category_alias', '');
        $is_show = request()->post('is_show', 1);
        $color = request()->post('color', '');
        $is_show_lower_category = request()->post('is_show_lower_category', 0);
        $is_show_brand = request()->post('is_show_brand', 0);
        $sort = request()->post('sort', 0);
        $short_name = request()->post('short_name', '');
        $goods_sort_type = request()->post("goods_sort_type", 1);
        $data = array(
            "category_alias" => $category_alias,
            "is_show" => $is_show,
            "color" => $color,
            "is_show_lower_category" => $is_show_lower_category,
            "is_show_brand" => $is_show_brand,
            "sort" => $sort,
            "short_name" => $short_name,
            "goods_sort_type" => $goods_sort_type
        );
        $category = new GoodsCategory();
        $retval = $category->setGoodsCategoryBlock($id, $this->instance_id, $data);
        return AjaxReturn($retval);
    }

    /**
     * 设置商品分类广告
     *
     * @return Ambigous <boolean, number, \think\false, string>
     */
    public function setGoodsCategoryAdv()
    {
        $id = request()->post('id', 0);
        $ad_picture = request()->post('ad_picture', '');
        $data = array(
            "ad_picture" => $ad_picture
        );
        $category = new GoodsCategory();
        $retval = $category->setGoodsCategoryBlock($id, $this->instance_id, $data);
        return AjaxReturn($retval);
    }

    /**
     * 商品促销
     */
    public function goodsGroupRecommend()
    {
        $platform = new Platform();
        $goods_group_recommend = $platform->getGoodsGroupRecommend($this->instance_id);
        if ($goods_group_recommend["group_id_array"] == "") {
            $this->assign("group_id_array", array());
        } else {
            $this->assign("group_id_array", explode(",", $goods_group_recommend["group_id_array"]));
        }
        $goods_group = new GoodsGroup();
        $groupList = $goods_group->getGoodsGroupList(1, 0, [
            'shop_id' => $this->instance_id
        ]);
        if (empty($groupList['data'])) {
            $this->assign("group_str", '');
        } else {
            $this->assign("group_str", json_encode($groupList['data']));
        }
        $this->assign("group_list", $groupList['data']); // 标签
        $this->assign("goods_group_recommend", $goods_group_recommend);
        return view($this->style . "System/goodsGroupRecommend");
    }

    /**
     * 编辑商品促销
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown Ambigous <string, unknown> >
     */
    public function setGoodsGroupRecommend()
    {
        $is_show = request()->post('is_show', 1);
        $group_id_array = request()->post('group_id_array', '');
        $platform = new Platform();
        $retval = $platform->setGoodsGroupRecommend($this->instance_id, $group_id_array, $is_show);
        return AjaxReturn($retval);
    }
}   