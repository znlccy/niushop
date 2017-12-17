<?php
/**
 * Shop.php
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

use data\service\Address;
use data\service\Shop as ShopService;
use data\service\Config;

/**
 * 店铺设置控制器
 *
 * @author Administrator
 *        
 */
class Shop extends BaseController
{

    /**
     * 店铺基础设置
     */
    public function shopConfig()
    {
        $child_menu_list = array(
            array(
                'url' => "Shop/shopConfig",
                'menu_name' => "店铺设置",
                "active" => 1
            ),
            array(
                'url' => "Shop/shopStyle",
                'menu_name' => "PC端主题",
                "active" => 0
            ),
            array(
                'url' => "Shop/shopWchatStyle",
                'menu_name' => "微信端主题",
                "active" => 0
            )
        );
        $shop = new ShopService();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $shop_logo = request()->post('shop_logo', '');
            $shop_banner = request()->post('shop_banner', '');
            $shop_avatar = request()->post('shop_avatar', '');
            $shop_qq = request()->post('shop_qq', '');
            $shop_ww = request()->post('shop_ww', '');
            $shop_phone = request()->post('shop_phone', '');
            $shop_keywords = request()->post('shop_keywords', '');
            $shop_description = request()->post('shop_description', '');
            $res = $shop->updateShopConfigByshop($shop_id, $shop_logo, $shop_banner, $shop_avatar, $shop_qq, $shop_ww, $shop_phone, $shop_keywords, $shop_description);
            return AjaxReturn($res);
        }
        $shop_info = $shop->getShopDetail($this->instance_id);
        $this->assign('shop_info', $shop_info);
        $this->assign('child_menu_list', $child_menu_list);
        return view($this->style . "Shop/shopConfig");
    }

    /**
     * 店铺幻灯设置
     */
    public function shopAd()
    {
        $child_menu_list = array(
            array(
                'url' => "Shop/shopConfig",
                'menu_name' => "店铺设置",
                "active" => 0
            ),
            array(
                'url' => "Shop/shopAd",
                'menu_name' => "幻灯设置",
                "active" => 1
            ),
            array(
                'url' => "Shop/shopStyle",
                'menu_name' => "PC端主题",
                "active" => 0
            ),
            array(
                'url' => "Shop/shopWchatStyle",
                'menu_name' => "微信端主题",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        return view($this->style . "Shop/shopAd");
    }

    /**
     * 店铺主题
     */
    public function shopStyle()
    {
        $child_menu_list = array(
            array(
                'url' => "Shop/shopConfig",
                'menu_name' => "店铺设置",
                "active" => 0
            ),
            array(
                'url' => "Shop/shopStyle",
                'menu_name' => "PC端主题",
                "active" => 1
            ),
            array(
                'url' => "Shop/shopWchatStyle",
                'menu_name' => "微信端主题",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        return view($this->style . "Shop/shopStyle");
    }

    /**
     * 微信端样式
     */
    public function shopWchatStyle()
    {
        $child_menu_list = array(
            array(
                'url' => "Shop/shopConfig",
                'menu_name' => "店铺设置",
                "active" => 0
            ),
            array(
                'url' => "Shop/shopStyle",
                'menu_name' => "PC端主题",
                "active" => 0
            ),
            array(
                'url' => "Shop/shopWchatStyle",
                'menu_name' => "微信端主题",
                "active" => 1
            )
        );
        $this->assign('child_menu_list', $child_menu_list);
        return view($this->style . "Shop/shopWchatStyle");
    }

    /**
     * 自提点列表
     */
    public function pickupPointList()
    {
        $child_menu_list = array(
            array(
                'url' => "express/expresscompany",
                'menu_name' => "物流公司",
                "active" => 0
            ),
            array(
                'url' => "config/areamanagement",
                'menu_name' => "地区管理",
                "active" => 0
            ),
            array(
                'url' => "order/returnsetting",
                'menu_name' => "商家地址",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointlist",
                'menu_name' => "自提点管理",
                "active" => 1
            ),
            array(
                'url' => "shop/pickuppointfreight",
                'menu_name' => "自提点运费",
                "active" => 0
            ),
            array(
                'url' => "config/distributionareamanagement",
                'menu_name' => "货到付款地区管理",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        if (request()->isAjax()) {
            $shop = new ShopService();
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $condition = array(
                'name' => array(
                    'like',
                    '%' . $search_text . '%'
                )
            );
            $result = $shop->getPickupPointList($page_index, $page_size, $condition, 'create_time asc');
            return $result;
        } else {
            return view($this->style . "Shop/sinceList");
        }
    }

    /**
     * 自提点运费菜单
     * 
     * @return \think\response\View
     */
    public function pickuppointfreight()
    {
        $child_menu_list = array(
            array(
                'url' => "express/expresscompany",
                'menu_name' => "物流公司",
                "active" => 0
            ),
            array(
                'url' => "config/areamanagement",
                'menu_name' => "地区管理",
                "active" => 0
            ),
            array(
                'url' => "order/returnsetting",
                'menu_name' => "商家地址",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointlist",
                'menu_name' => "自提点管理",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointfreight",
                'menu_name' => "自提点运费",
                "active" => 1
            ),
            array(
                'url' => "config/distributionareamanagement",
                'menu_name' => "货到付款地区管理",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        
        $config_service = new Config();
        $config_info = $config_service->getConfig($this->instance_id, 'PICKUPPOINT_FREIGHT');
        $this->assign('config', json_decode($config_info['value']));
        return view($this->style . "Shop/pickupPointFreight");
    }

    /**
     * 修改自提点运费菜单
     */
    public function pickupPointFreightAjax()
    {
        if (request()->isAjax()) {
            $is_enable = request()->post('is_enable', '');
            $pickup_freight = request()->post('pickup_freight', '');
            $manjian_freight = request()->post('manjian_freight', '');
            $config_service = new Config();
            $res = $config_service->setPickupPointFreight($is_enable, $pickup_freight, $manjian_freight);
            return AjaxReturn($res);
        }
    }

    /**
     * 添加自提点
     */
    public function addPickupPoint()
    {
        if (request()->isAjax()) {
            $shop = new ShopService();
            $shop_id = $this->instance_id;
            $name = request()->post('name');
            $address = request()->post('address');
            $contact = request()->post('contact');
            $phone = request()->post('phone');
            $province_id = request()->post('province_id');
            $city_id = request()->post('city_id');
            $district_id = request()->post('district_id');
            $res = $shop->addPickupPoint($shop_id, $name, $address, $contact, $phone, $province_id, $city_id, $district_id, '', '');
            return AjaxReturn($res);
        }
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "shop/pickuppointlist",
                    'menu_name' => "自提点管理",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "Shop/addSince");
    }

    /**
     * 修改自提点
     */
    public function updatePickupPoint()
    {
        $pickip_id = request()->get('id', '');
        $shop = new ShopService();
        if (request()->isAjax()) {
            $id = request()->post('id');
            $shop_id = $this->instance_id;
            $name = request()->post('name');
            $address = request()->post('address');
            $contact = request()->post('contact');
            $phone = request()->post('phone');
            $province_id = request()->post('province_id');
            $city_id = request()->post('city_id');
            $district_id = request()->post('district_id');
            $res = $shop->updatePickupPoint($id, $shop_id, $name, $address, $contact, $phone, $province_id, $city_id, $district_id, '', '');
            return AjaxReturn($res);
        }
        $pickupPoint_detail = $shop->getPickupPointDetail($pickip_id);
        $this->assign('pickupPoint_detail', $pickupPoint_detail);
        $this->assign('pickip_id', $pickip_id);
        $child_menu_list = array(
            array(
                'url' => "javascript:;",
                'menu_name' => $this->module_info['module_name'],
                'active' => 1,
                "superior_menu" => array(
                    'url' => "shop/pickuppointlist",
                    'menu_name' => "自提点管理",
                    'active' => 1,
                )
            )
        );
        $this->assign("child_menu_list", $child_menu_list);
        return view($this->style . "Shop/updatePickupPoint");
    }

    /**
     * 删除自提点
     */
    public function deletepickupPoint()
    {
        if (request()->isAjax()) {
            $pickip_id = request()->post('pickupPoint_id');
            $shop = new ShopService();
            $res = $shop->deletePickupPoint($pickip_id);
            return AjaxReturn($res);
        }
    }

    /**
     * 获取省列表
     */
    public function getProvince()
    {
        $address = new Address();
        $province_list = $address->getProvinceList();
        return $province_list;
    }

    /**
     * 获取城市列表
     *
     * @return Ambigous <multitype:\think\static , \think\false, \think\Collection, \think\db\false, PDOStatement, string, \PDOStatement, \think\db\mixed, boolean, unknown, \think\mixed, multitype:, array>
     */
    public function getCity()
    {
        $address = new Address();
        $province_id = request()->post('province_id', 0);
        $city_list = $address->getCityList($province_id);
        return $city_list;
    }

    /**
     * 获取区域地址
     */
    public function getDistrict()
    {
        $address = new Address();
        $city_id = request()->post('city_id', 0);
        $district_list = $address->getDistrictList($city_id);
        return $district_list;
    }

    /**
     * 获取选择地址
     *
     * @return unknown
     */
    public function getSelectAddress()
    {
        $address = new Address();
        $province_list = $address->getProvinceList();
        $province_id = request()->post('province_id', 0);
        $city_id = request()->post('city_id', 0);
        $city_list = $address->getCityList($province_id);
        $district_list = $address->getDistrictList($city_id);
        $data["province_list"] = $province_list;
        $data["city_list"] = $city_list;
        $data["district_list"] = $district_list;
        return $data;
    }
}
