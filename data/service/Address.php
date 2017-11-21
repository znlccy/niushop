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

/**
 * 区域地址
 */
use data\api\IAddress as IAddress;
use data\model\AreaModel as Area;
use data\model\CityModel as City;
use data\model\DistrictModel;
use data\model\DistrictModel as District;
use data\model\NsOffpayAreaModel;
use data\model\ProvinceModel as Province;
use data\service\BaseService as BaseService;

class Address extends BaseService implements IAddress
{

    /*
     * (non-PHPdoc)
     * @see \ata\api\IAddress::getAreaList()
     */
    public function getAreaList()
    {
        $area = new Area();
        $list = $area->getQuery('', 'area_id,area_name', '');
        return $list;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IAddress::getProvinceList()
     */
    public function getProvinceList($area_id = 0)
    {
        $province = new Province();
        if ($area_id == - 1) {
            $list = array();
        } elseif ($area_id == 0) {
            $list = $province->getQuery('', 'province_id,area_id,province_name,sort', 'sort asc');
        } else {
            $list = $province->getQuery([
                'area_id' => $area_id
            ], 'province_id,area_id,province_name,sort', 'sort asc');
        }
        return $list;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IAddress::getProvinceListById()
     */
    public function getProvinceListById($province_id)
    {
        $province = new Province();
        
        $condition = array(
            'province_id' => array(
                'in',
                $province_id
            )
        );
        $list = $province->getQuery($condition, 'province_id,area_id,province_name,sort', 'sort asc');
        return $list;
    }

    public function getAddressListById($province_id_arr, $city_id_arr)
    {
        $province = new Province();
        $city = new City();
        
        $province_condition = array(
            'province_id' => array(
                'in',
                $province_id_arr
            )
        );
        $city_condition = array(
            'city_id' => array(
                'in',
                $city_id_arr
            )
        );
        $province_list = $province->getQuery($province_condition, 'province_id,province_name', 'sort asc');
        $city_list = $city->getQuery($city_condition, 'province_id,city_name,city_id', 'sort asc');
        foreach ($province_list as $k => $v) {
            $list['province_list'][$k] = $v;
            $children_list = array();
            foreach ($city_list as $city_k => $city_v) {
                if ($v['province_id'] == $city_v['province_id']) {
                    $children_list[$city_k] = $city_v;
                }
            }
            $list['province_list'][$k]['city_list'] = $children_list;
        }
        
        return $list;
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IAddress::getCityList()
     */
    public function getCityList($province_id = 0)
    {
        $city = new City();
        if ($province_id == 0) {
            $list = $city->getQuery('', 'city_id,province_id,city_name,zipcode,sort', 'sort asc');
        } else {
            $list = $city->getQuery([
                'province_id' => $province_id
            ], 'city_id,province_id,city_name,zipcode,sort', 'sort asc');
        }
        return $list;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IAddress::getDistrictList()
     */
    public function getDistrictList($city_id = 0)
    {
        $district = new District();
        if ($city_id == 0) {
            $list = $district->getQuery('', 'district_id,city_id,district_name,sort', 'sort asc');
        } else {
            $list = $district->getQuery([
                'city_id' => $city_id
            ], 'district_id,city_id,district_name,sort', 'sort asc');
        }
        return $list;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IAddress::getProvinceName()
     */
    public function getProvinceName($province_id)
    {
        $province = new Province();
        
        if (! empty($province_id)) {
            $condition = array(
                'province_id' => array(
                    'in',
                    $province_id
                )
            );
            $list = $province->getQuery($condition, 'province_name', '');
        }
        $name = '';
        if (! empty($list)) {
            foreach ($list as $k => $v) {
                $name .= $v['province_name'] . ',';
            }
            $name = substr($name, 0, strlen($name) - 1);
        }
        return $name;
        
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IAddress::getCityName()
     */
    public function getCityName($city_id)
    {
        $city = new City();
        if (! empty($city_id)) {
            $condition = array(
                'city_id' => array(
                    'in',
                    $city_id
                )
            );
            $list = $city->getQuery($condition, 'city_name', '');
        }
        
        $name = '';
        if (! empty($list)) {
            foreach ($list as $k => $v) {
                $name .= $v['city_name'] . ',';
            }
            $name = substr($name, 0, strlen($name) - 1);
        }
        return $name;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IAddress::getDistrictName()
     */
    public function getDistrictName($district_id)
    {
        $dictrict = new DistrictModel();
        
        if (! empty($district_id)) {
            $condition = array(
                'district_id' => array(
                    'in',
                    $district_id
                )
            );
            $list = $dictrict->getQuery($condition, 'district_name', '');
        }
        
        $name = '';
        if (! empty($list)) {
            foreach ($list as $k => $v) {
                $name .= $v['district_name'] . ',';
            }
            $name = substr($name, 0, strlen($name) - 1);
        }
        return $name;
    }

    /**
     * 获取地区树(non-PHPdoc)
     * 目前满额包邮（省市）、物流模板（省市区）在用这个函数 2017年9月14日 18:56:08 王永杰
     *
     * @see \ata\api\IAddress::getAreaTree()
     */
    public function getAreaTree($existing_address_list)
    {
        $list = array();
        $area_list = $this->getAreaList();
        $list = $area_list;
        // 地区
        foreach ($area_list as $k_area => $v_area) {
            
            // 省
            $province_list = $this->getProvinceList($v_area['area_id'] == 0 ? - 1 : $v_area['area_id']);
            foreach ($province_list as $key_province => $v_province) {
                
                $province_list[$key_province]['is_disabled'] = 0; // 是否可用，0：可用，1：不可用
                
                if (! empty($existing_address_list) && count($existing_address_list['province_id_array'])) {
                    foreach ($existing_address_list['province_id_array'] as $province_id) {
                        if ($province_id == $v_province['province_id']) {
                            $province_list[$key_province]['is_disabled'] = 1;
                        }
                    }
                }
                
                $city_disabled_count = 0; // 市禁用的数量
                $city_list = $this->getCityList($v_province['province_id']); // 市地区的禁用条件是，区县地区都禁用了，市才禁用
                foreach ($city_list as $k_city => $city) {
                    
                    $city_list[$k_city]['is_disabled'] = 0; // 是否可用，0：可用，1：不可用
                    $city_list[$k_city]['district_list_count'] = 0;
                    
                    if (! empty($existing_address_list) && count($existing_address_list['city_id_array'])) {
                        
                        foreach ($existing_address_list['city_id_array'] as $city_id) {
                            if ($city_id == $city['city_id']) {
                                $city_list[$k_city]['is_disabled'] = 1;
                                $city_disabled_count ++;
                            }
                        }
                    }
                    
                    // 这个判断主要考虑到“满意包邮”功能不使用区县加的。可以提高速度
                    if (! empty($existing_address_list['district_id_array'])) {
                        $district_disabled_count = 0; // 区县禁用的数量
                        $district_list = $this->getDistrictList($city['city_id']);
                        foreach ($district_list as $k_district => $district) {
                            $district_list[$k_district]['is_disabled'] = 0; // 是否可用，0：可用，1：不可用
                            if (! empty($existing_address_list) && count($existing_address_list['district_id_array'])) {
                                foreach ($existing_address_list['district_id_array'] as $district_id) {
                                    if ($district_id == $district['district_id']) {
                                        $district_list[$k_district]['is_disabled'] = 1;
                                        $district_disabled_count ++;
                                    }
                                }
                            }
                        }
                        
                        // 判断区县有没有全部禁用，有的话将父亲(省市)设置为不禁用
                        if (! empty($existing_address_list['district_id_array']) && count($district_list) != $district_disabled_count && $city_list[$k_city]['is_disabled'] == 1) {
                            $city_list[$k_city]['is_disabled'] = 0;
                            $province_list[$key_province]['is_disabled'] = 0;
                        }
                        // $city_list[$k_city]['district_disabled_count'] = $district_disabled_count;
                        $city_list[$k_city]['district_list'] = $district_list;
                        $city_list[$k_city]['district_list_count'] = count($district_list);
                    }
                }
                
                $province_list[$key_province]['city_disabled_count'] = $city_disabled_count;
                $province_list[$key_province]['city_list'] = $city_list;
                $province_list[$key_province]["city_count"] = count($city_list);
            }
            
            $list[$k_area]['province_list'] = $province_list;
            $list[$k_area]['province_list_count'] = count($province_list);
        }
        return $list;
    }
    /**
     * 运费模板的数据整理
     * @param unknown $existing_address_list
     */
    public function getAreaTree_ext($existing_address_list)
    {
        $list = array();
        $select_district_id_array=[];
        if(!empty($existing_address_list)){
            $select_district_id_array=$existing_address_list["district_id_array"];
        }
        //查询所有的地区信息
        $area_list = $this->getAreaList();
        //查询所有的省信息
        $province_list = $this->getProvinceList();
        //查询所有的市信息
        $city_list = $this->getCityList();
        //查询所有的区县的信息
        $district_list = $this->getDistrictList();
        
        $district_id_deal_array=[];
        //先整理所有区县的是否禁用的整理
        foreach ($district_list as $k_district=>$v_district){
            $is_set=false;
            $is_disabled=0;
            $district_id=$v_district["district_id"];
            $district_id_deal_array[$district_id]=$k_district;
            $is_set=in_array($district_id, $select_district_id_array);
            if($is_set){
                $is_disabled=1;
            }
            $district_list[$k_district]["is_disabled"]=$is_disabled;
        }
        //整理市的集合
        foreach ($city_list as $k_city=>$v_city){
            $deal_array=$this->dealCityDistrictData($v_city["city_id"], $district_list, $district_id_deal_array, $existing_address_list["city_id_array"]);
            $child_district_array=$deal_array["child_district"];
            $is_disabled=$deal_array["is_disabled"];
            $city_list[$k_city]["district_list"]=$child_district_array;
            $city_list[$k_city]["is_disabled"]=$is_disabled;
            $city_list[$k_city]["district_list_count"]=count($child_district_array);
        }
        //整理省的集合
        foreach ($province_list as $k_province=>$v_province){
            $deal_array=$this->dealProvinceCityData($v_province["province_id"], $city_list, $existing_address_list["province_id_array"]);
            $child_city_array=$deal_array["child_city"];
            $is_disabled=$deal_array["is_disabled"];
            $province_list[$k_province]["city_list"]=$child_city_array;
            $province_list[$k_province]["is_disabled"]=$is_disabled;
            $province_list[$k_province]["city_count"]=count($child_city_array);
            $province_list[$k_province]["city_disabled_count"]=0;
        }
        //整理地区的集合
        foreach ($area_list as $k_area => $v_area){
            $deal_array=$this->dealAreaProvinceData($v_area["area_id"], $province_list);
            $child_province_array=$deal_array["child_province"];
            $is_disabled=$deal_array["is_disabled"];
            $area_list[$k_area]["province_list"]=$child_province_array;
            $area_list[$k_area]["is_disabled"]=$is_disabled;
            $area_list[$k_area]["province_list_count"]=count($child_province_array);
        }
        return $area_list;
        
    }
    /**
     * 处理市和 地区的信息
     */
    private function dealCityDistrictData($city_id, $district_list, $district_id_deal_array, $select_city_ids){
        $is_disabled=1;
        $district_child_list = $this->getDistrictList($city_id);
        foreach ($district_child_list as $k=>$district_obj){
            $dis_id=$district_obj["district_id"];
            $k_num=$district_id_deal_array[$dis_id];
            $district_child_list[$k]["is_disabled"]=$district_list[$k_num]["is_disabled"];
            if($district_list[$k_num]["is_disabled"]==0){
                $is_disabled=0;
            }
        }
        if(empty($district_child_list)){
            $is_set=in_array($city_id, $select_city_ids);
            if($is_set){
                $is_disabled=1;
            }else{
                $is_disabled=0;
            }
        }
        return array(
          "child_district"=>$district_child_list,
          "is_disabled"=>$is_disabled
        );
    }

    /**
     * 处理省和市的信息
     * @param unknown $province_id
     * @param unknown $city_list
     */
    private function dealProvinceCityData($province_id, $city_list, $province_id_array){
        $city_child_array=[];
        $is_disabled=1;
        foreach ($city_list as $city_obj){
            if($city_obj["province_id"]==$province_id){
                $city_child_array[]=$city_obj;
                if($city_obj["is_disabled"]==0){
                    $is_disabled=0;
                }
            }
        }
        if(empty($city_child_array)){
            $is_set=in_array($province_id, $province_id_array);
            if($is_set){
                $is_disabled=1;
            }else{
                $is_disabled=0;
            }
        }
        return array(
            "child_city"=>$city_child_array,
            "is_disabled"=>$is_disabled
        );
    }
    /**
     * 处理区域的信息
     * @param unknown $area_id
     * @param unknown $province_list
     */
    private function dealAreaProvinceData($area_id, $province_list){
        $province_child_array=[];
        $is_disabled=1;
        foreach ($province_list as $province_obj){
            if($province_obj["area_id"]==$area_id){
                $province_child_array[]=$province_obj;
                if($province_obj["is_disabled"]==0){
                    $is_disabled=0;
                }
            }
        }
        return array(
            "child_province"=>$province_child_array,
            "is_disabled"=>$is_disabled
        );
    }
    /**
     * 获取地址 返回（例如： 山西省 太原市 小店区）
     *
     * @param unknown $province_id            
     * @param unknown $city_id            
     * @param unknown $dictrict_id            
     */
    public function getAddress($province_id, $city_id, $district_id)
    {
        $province = new Province();
        $city = new City();
        $district = new District();
        $province_name = $province->getInfo('province_id = ' . $province_id, 'province_name');
        $city_name = $city->getInfo('city_id = ' . $city_id, 'city_name');
        $district_name = $district->getInfo('district_id = ' . $district_id, 'district_name');
        $address = $province_name['province_name'] . '&nbsp;' . $city_name['city_name'] . '&nbsp;' . $district_name['district_name'];
        return $address;
    }

    /**
     * 获取省id
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IAddress::getProvinceId()
     */
    public function getProvinceId($province_name)
    {
        $province = new Province();
        $province_id = $province->getInfo([
            'province_name' => $province_name
        ], 'province_id');
        return $province_id;
    }

    /**
     * 获取市id
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IAddress::getCityId()
     */
    public function getCityId($city_name)
    {
        $city = new City();
        $city_id = $city->getInfo([
            'city_name' => $city_name
        ], 'city_id');
        return $city_id;
    }

    public function addOrupdateCity($city_id, $province_id, $city_name, $zipcode = '', $sort = '')
    {
        $city = new City();
        $data = array(
            "province_id" => $province_id,
            "city_name" => $city_name,
            "zipcode" => $zipcode,
            "sort" => $sort
        );
        if ($city_id > 0 && $city_id != 0) {
            $res = $city->save($data, [
                'city_id' => $city_id
            ]);
            return $res;
        } else {
            $city->save($data);
            return $city->city_id;
        }
    }

    public function addOrupdateDistrict($district_id, $city_id, $district_name, $sort = '')
    {
        $district = new District();
        $data = array(
            "city_id" => $city_id,
            "district_name" => $district_name,
            "sort" => $sort
        );
        if ($district_id > 0 && $district_id != 0) {
            return $district->save($data, [
                "district_id" => $district_id
            ]);
        } else {
            $district->save($data);
            return $district->district_id;
        }
    }

    public function updateProvince($province_id, $province_name, $sort, $area_id)
    {
        $province = new Province();
        $data = array(
            "province_name" => $province_name,
            "sort" => $sort,
            "area_id" => $area_id
        );
        return $province->save($data, [
            "province_id" => $province_id
        ]);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IAddress::updateProvince()
     */
    public function addProvince($province_name, $sort, $area_id)
    {
        $province = new Province();
        $data = array(
            "province_name" => $province_name,
            "sort" => $sort,
            "area_id" => $area_id
        );
        $province->save($data);
        return $province->province_id;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IAddress::deleteProvince()
     */
    public function deleteProvince($province_id)
    {
        $province = new Province();
        $city = new City();
        $province->startTrans();
        try {
            $city_list = $city->getQuery([
                'province_id' => $province_id
            ], 'city_id', '');
            foreach ($city_list as $k => $v) {
                $this->deleteCity($v['city_id']);
            }
            $province->destroy($province_id);
            $province->commit();
            return 1;
        } catch (\Exception $e) {
            $province->rollback();
            return $e->getMessage();
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IAddress::deleteCity()
     */
    public function deleteCity($city_id)
    {
        $city = new City();
        $district = new District();
        $city->startTrans();
        try {
            $district->destroy([
                'city_id' => $city_id
            ]);
            $city->destroy($city_id);
            $city->commit();
            return 1;
        } catch (\Exception $e) {
            $city->rollback();
            return $e->getMessage();
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IAddress::deleteDistrict()
     */
    public function deleteDistrict($district_id)
    {
        $district = new District();
        return $district->destroy($district_id);
    }

    /**
     * $upType 修改类型 1排序 2名称
     * $regionType 修改地区类型 1省 2市 3县
     */
    public function updateRegionNameAndRegionSort($upType, $regionType, $regionName, $regionSort, $regionId)
    {
        if ($regionType == 1) {
            $province = new Province();
            if ($upType == 1) {
                $res = $province->save([
                    'sort' => $regionSort
                ], [
                    'province_id' => $regionId
                ]);
                return $res;
            }
            if ($upType == 2) {
                $res = $province->save([
                    'province_name' => $regionName
                ], [
                    'province_id' => $regionId
                ]);
                return $res;
            }
        }
        if ($regionType == 2) {
            $city = new City();
            if ($upType == 1) {
                $res = $city->save([
                    'sort' => $regionSort
                ], [
                    'city_id' => $regionId
                ]);
                return $res;
            }
            if ($upType == 2) {
                $res = $city->save([
                    'city_name' => $regionName
                ], [
                    'city_id' => $regionId
                ]);
                return $res;
            }
        }
        if ($regionType == 3) {
            $district = new District();
            if ($upType == 1) {
                $res = $district->save([
                    'sort' => $regionSort
                ], [
                    'district_id' => $regionId
                ]);
                return $res;
            }
            if ($upType == 2) {
                $res = $district->save([
                    'district_name' => $regionName
                ], [
                    'district_id' => $regionId
                ]);
                return $res;
            }
        }
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IAddress::getCityCountByProvinceId()
     */
    public function getCityCountByProvinceId($province_id)
    {
        $city = new City();
        $count = $city->getCount([
            'province_id' => $province_id
        ]);
        return $count;
    }

    /**
     * 通过市级id获取其下级的数量
     *
     * {@inheritdoc}
     *
     * @see \data\api\IAddress::getDistrictCountByCityId()
     */
    public function getDistrictCountByCityId($city_id)
    {
        $district = new District();
        $count = $district->getCount([
            'city_id' => $city_id
        ]);
        return $count;
    }

    /**
     * 添加配送地区
     */
    public function addOrUpdateDistributionArea($shop_id, $province_id, $city_id, $district_id)
    {
        $offpayArea = new NsOffpayAreaModel();
        $res = $this->getDistributionAreaInfo($shop_id);
        if ($res == '') {
            $data = array(
                "shop_id" => $shop_id,
                "province_id" => $province_id,
                "city_id" => $city_id,
                "district_id" => $district_id
            );
            return $offpayArea->save($data);
        } else {
            $data = array(
                "province_id" => $province_id,
                "city_id" => $city_id,
                "district_id" => $district_id
            );
            return $offpayArea->save($data, [
                'shop_id' => $shop_id
            ]);
        }
    }

    /**
     * 获取配送地区
     */
    public function getDistributionAreaInfo($shop_id)
    {
        $offpayArea = new NsOffpayAreaModel();
        $res = $offpayArea->getInfo([
            'shop_id' => $shop_id
        ], "province_id,city_id,district_id");
        return $res;
    }

    /**
     * 检测某个地址是否可以 货到付款
     *
     * @param unknown $shop_id            
     * @param unknown $province_id            
     * @param unknown $city_id            
     * @param unknown $district_id            
     */
    public function getDistributionAreaIsUser($shop_id, $province_id, $city_id, $district_id)
    {
        $offpayArea = new NsOffpayAreaModel();
        $is_use = false;
        $off_list = $offpayArea->where(" FIND_IN_SET(" . $province_id . ", province_id) AND FIND_IN_SET( " . $city_id . ", city_id) AND FIND_IN_SET(" . $district_id . ", district_id) ")->select();
        if (! empty($off_list) && count($off_list) > 0) {
            $is_use = true;
        } else {
            $is_use = false;
        }
        return $is_use;
    }

    /**
     * 获取市的第一个区
     *
     * @param unknown $city_id            
     */
    public function getCityFirstDistrict($city_id)
    {
        $district_model = new DistrictModel();
        $data = $district_model->getFirstData([
            'city_id' => $city_id
        ], '');
        if (! empty($data)) {
            return $data['district_id'];
        } else {
            return 0;
        }
    }
}