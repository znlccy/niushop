<?php
/**
 * IExpress.php
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
namespace data\api;

/**
 * 物流
 */
interface IExpress
{

    /**
     * 获取物流模板列表
     *
     * @param number $page_index            
     * @param number $page_size            
     * @param string $condition            
     * @param string $order            
     */
    function getShippingFeeList($page_index = 1, $page_size = 0, $condition = '', $order = '');

    /**
     * 添加物流模板
     * 修改时间：2017年8月12日 09:26:57
     *
     * @param unknown $co_id            
     * @param unknown $is_default            
     * @param unknown $shipping_fee_name            
     * @param unknown $province_id_array            
     * @param unknown $city_id_array            
     * @param unknown $district_id_array            
     * @param unknown $weight_is_use            
     * @param unknown $weight_snum            
     * @param unknown $weight_sprice            
     * @param unknown $weight_xnum            
     * @param unknown $weight_xprice            
     * @param unknown $volume_is_use            
     * @param unknown $volume_snum            
     * @param unknown $volume_sprice            
     * @param unknown $volume_xnum            
     * @param unknown $volume_xprice            
     * @param unknown $bynum_is_use            
     * @param unknown $bynum_snum            
     * @param unknown $bynum_sprice            
     * @param unknown $bynum_xnum            
     * @param unknown $bynum_xprice            
     */
    function addShippingFee($co_id, $is_default, $shipping_fee_name, $province_id_array, $city_id_array, $district_id_array, $weight_is_use, $weight_snum, $weight_sprice, $weight_xnum, $weight_xprice, $volume_is_use, $volume_snum, $volume_sprice, $volume_xnum, $volume_xprice, $bynum_is_use, $bynum_snum, $bynum_sprice, $bynum_xnum, $bynum_xprice);

    /**
     * 修改物流模板
     * 修改时间：2017年8月12日 09:26:42
     * 
     * @param unknown $shipping_fee_id            
     * @param unknown $is_default            
     * @param unknown $shipping_fee_name            
     * @param unknown $province_id_array            
     * @param unknown $city_id_array            
     * @param unknown $district_id_array            
     * @param unknown $weight_is_use            
     * @param unknown $weight_snum            
     * @param unknown $weight_sprice            
     * @param unknown $weight_xnum            
     * @param unknown $weight_xprice            
     * @param unknown $volume_is_use            
     * @param unknown $volume_snum            
     * @param unknown $volume_sprice            
     * @param unknown $volume_xnum            
     * @param unknown $volume_xprice            
     * @param unknown $bynum_is_use            
     * @param unknown $bynum_snum            
     * @param unknown $bynum_sprice            
     * @param unknown $bynum_xnum            
     * @param unknown $bynum_xprice            
     */
    function updateShippingFee($shipping_fee_id, $is_default, $shipping_fee_name, $province_id_array, $city_id_array, $district_id_array, $weight_is_use, $weight_snum, $weight_sprice, $weight_xnum, $weight_xprice, $volume_is_use, $volume_snum, $volume_sprice, $volume_xnum, $volume_xprice, $bynum_is_use, $bynum_snum, $bynum_sprice, $bynum_xnum, $bynum_xprice);

    /**
     * 运费模板详情
     *
     * @param unknown $shipping_fee_id            
     */
    function shippingFeeDetail($shipping_fee_id);

    /**
     * 运费模板删除
     *
     * @param unknown $shipping_fee_id            
     */
    function shippingFeeDelete($shipping_fee_id);

    /**
     * 运费模板列表
     *
     * @param unknown $where            
     * @param string $fields            
     */
    function shippingFeeQuery($where, $fields = "*");

    /**
     * 获取物流公司
     *
     * @param number $page_index            
     * @param number $page_size            
     * @param string $condition            
     * @param string $order            
     */
    function getExpressCompanyList($page_index = 1, $page_size = 0, $condition = '', $order = '');

    /**
     * 添加物流公司
     *
     * @param unknown $shopId            
     * @param unknown $company_name            
     * @param unknown $express_no            
     * @param unknown $is_enabled            
     * @param unknown $image            
     * @param unknown $phone            
     * @param unknown $orders            
     */
    function addExpressCompany($shopId, $company_name, $express_logo, $express_no, $is_enabled, $image, $phone, $orders, $is_default);

    /**
     * 把别的改为未默认,把当前设置为默认
     */
    public function defaultExpressCompany();

    /**
     * 修改物流公司
     *
     * @param unknown $co_id            
     * @param unknown $shopId            
     * @param unknown $company_name            
     * @param unknown $express_no            
     * @param unknown $is_enabled            
     * @param unknown $image            
     * @param unknown $phone            
     * @param unknown $orders            
     */
    function updateExpressCompany($co_id, $shopId, $company_name, $express_logo, $express_no, $is_enabled, $image, $phone, $orders, $is_default);

    /**
     * 物流公司详情
     *
     * @param unknown $co_id            
     */
    function expressCompanyDetail($co_id);

    /**
     * 删除物流公司
     *
     * @param unknown $co_id            
     */
    function expressCompanyDelete($co_id);

    /**
     * 物流公司列表
     *
     * @param unknown $where            
     * @param string $fields            
     */
    function expressCompanyQuery($where, $fields = "*");

    /**
     * 添加物流公司地址
     *
     * @param unknown $contact            
     * @param unknown $mobile            
     * @param unknown $phone            
     * @param unknown $company_name            
     * @param unknown $province            
     * @param unknown $city            
     * @param unknown $district            
     * @param unknown $zipcode            
     * @param unknown $address            
     */
    function addShopExpressAddress($contact, $mobile, $phone, $company_name, $province, $city, $district, $zipcode, $address);

    /**
     * 修改物流地址
     *
     * @param unknown $express_address_id            
     * @param unknown $contact            
     * @param unknown $mobile            
     * @param unknown $phone            
     * @param unknown $company_name            
     * @param unknown $province            
     * @param unknown $city            
     * @param unknown $district            
     * @param unknown $zipcode            
     * @param unknown $address            
     */
    function updateShopExpressAddress($express_address_id, $contact, $mobile, $phone, $company_name, $province, $city, $district, $zipcode, $address);

    /**
     * 修改公司发货标记
     *
     * @param unknown $express_address_id            
     * @param unknown $is_consigner            
     */
    function modifyShopExpressAddressConsigner($express_address_id, $is_consigner);

    /**
     * 修改公司收货标记
     *
     * @param unknown $express_address_id            
     * @param unknown $receiver            
     */
    function modifyShopExpressAddressReceiver($express_address_id, $is_receiver);

    /**
     * 获取公司物流地址
     *
     * @param number $page_index            
     * @param number $page_size            
     * @param string $condition            
     * @param string $order            
     */
    function getShopExpressAddressList($page_index = 1, $page_size = 0, $condition = '', $order = '');

    /**
     * 获取公司默认收货地址
     */
    function getDefaultShopExpressAddress($shop_id);

    /**
     * 删除物流地址
     *
     * @param unknown $express_address_id_array
     *            ','隔开
     */
    function deleteShopExpressAddress($express_address_id_array);

    /**
     * 查询单条物流地址详情
     *
     * @param unknown $express_address_id            
     */
    function selectShopExpressAddressInfo($express_address_id);

    /**
     * 获取物流模板内容
     *
     * @param unknown $shop_id            
     */
    function getExpressShippingItemsLibrary($shop_id);

    /**
     * 得到物流模板
     *
     * @param unknown $co_id            
     */
    function getExpressShipping($co_id);

    /**
     * 得到物流模板的打印项
     *
     * @param unknown $sid            
     */
    function getExpressShippingItems($sid);

    /**
     * 得到物流模板的打印项信息
     *
     * @param unknown $sid            
     * @param unknown $itemsArray            
     */
    function updateExpressShippingItem($sid, $itemsArray);

    /**
     * 更新物流模板的具体信息
     *
     * @param unknown $template_id            
     * @param unknown $width            
     * @param unknown $height            
     * @param unknown $imgUrl            
     * @param unknown $itemsArray            
     */
    function updateExpressShipping($template_id, $width, $height, $imgUrl, $itemsArray);

    /**
     * 根据物流公司id查询是否有默认地区
     *
     * @param unknown $co_id
     *            物流公司id
     */
    function isHasExpressCompanyDefaultTemplate($co_id);

    /**
     *
     * 获取物流公司的省市id组，排除默认地区
     * 创建时间：2017年6月29日 11:07:40
     * 修改时间：2017年8月11日 16:36:13
     * 王永杰
     *
     * @param 物流公司id $co_id            
     * @param 排除当前编辑的省id组 $province_id_array            
     * @param 排除当前编辑的市id组 $city_id_array            
     * @param 排序当前编辑的区县id组 $current_district_id_array            
     */
    function getExpressCompanyProvincesAndCitiesById($co_id, $current_province_id_array, $current_city_id_array, $current_district_id_array);
}