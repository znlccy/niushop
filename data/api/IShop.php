<?php
/**
 * IShop.php
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
 * 商品接口  
 */
interface IShop
{

    /**
     * 获取店铺轮播图列表
     *
     * @param unknown $page_index            
     * @param number $page_size            
     * @param string $order            
     * @param string $where            
     */
    function getShopAdList($page_index, $page_size = 0, $where = '', $order = '');

    /**
     * 添加店铺轮播图
     *
     * @param unknown $ad_image            
     * @param unknown $link_url            
     * @param unknown $sort            
     */
    function addShopAd($ad_image, $link_url, $sort, $type, $background);

    /**
     * 修改店铺轮播图
     *
     * @param unknown $id            
     * @param unknown $ad_image            
     * @param unknown $link_url            
     * @param unknown $sort            
     */
    function updateShopAd($id, $ad_image, $link_url, $sort, $type, $background);

    /**
     * 获取店铺轮播图详情
     *
     * @param unknown $id            
     */
    function getShopAdDetail($id);

    /**
     * 删除店铺轮播图
     *
     * @param unknown $id            
     */
    function delShopAd($id);

    /**
     * 导航列表
     *
     * @param unknown $page_index            
     * @param number $page_size            
     * @param string $order            
     * @param string $where            
     */
    function ShopNavigationList($page_index = 1, $page_size = 0, $condition = '', $order = '');

    /**
     * 店铺导航添加
     *
     * @param unknown $shop_id            
     * @param unknown $nav_title            
     * @param unknown $nav_url            
     * @param unknown $type            
     * @param unknown $sort            
     */
    function addShopNavigation($nav_title, $nav_url, $type, $sort, $align, $nav_type, $is_blank, $template_name);

    /**
     * 店铺导航修改
     *
     * @param unknown $shop_id            
     * @param unknown $nav_title            
     * @param unknown $nav_url            
     * @param unknown $type            
     * @param unknown $sort            
     */
    function updateShopNavigation($nav_id, $nav_title, $nav_url, $type, $sort, $align, $nav_type, $is_blank, $template_name);

    /**
     * 店铺导航删除
     *
     * @param unknown $nav_id            
     */
    function delShopNavigation($nav_id);

    /**
     * 查询店铺导航详情
     *
     * @param unknown $nav_id            
     */
    function shopNavigationDetail($nav_id);

    /**
     * 修改导航排序
     *
     * @param unknown $nav_id            
     * @param unknown $sort            
     */
    function modifyShopNavigationSort($nav_id, $sort);

    /**
     * 获取店铺列表
     *
     * @param unknown $page_index            
     * @param number $page_size            
     * @param string $order            
     * @param string $where            
     */
    function getShopList($page_index = 1, $page_size = 0, $where = '', $order = '');

    /**
     * 获取店铺分类
     *
     * @param unknown $page_index            
     * @param number $page_size            
     * @param string $order            
     * @param string $where            
     */
    function getShopGroup($page_index = 1, $page_size = 0, $where = '', $order = '');

    /**
     * 店铺申请
     *
     * @param unknown $apply_type            
     * @param unknown $uid            
     * @param unknown $company_name            
     * @param unknown $company_province_id            
     * @param unknown $company_city_id            
     * @param unknown $company_address            
     * @param unknown $company_address_detail            
     * @param unknown $company_phone            
     * @param unknown $company_employee_count            
     * @param unknown $company_registered_capital            
     * @param unknown $contacts_name            
     * @param unknown $contacts_phone            
     * @param unknown $contacts_email            
     * @param unknown $contacts_card_no            
     * @param unknown $contacts_card_electronic_1            
     * @param unknown $contacts_card_electronic_2            
     * @param unknown $contacts_card_electronic_3            
     * @param unknown $business_licence_number            
     * @param unknown $business_licence_address            
     * @param unknown $business_licence_start            
     * @param unknown $business_licence_start            
     * @param unknown $business_licence_end            
     * @param unknown $business_sphere            
     * @param unknown $business_licence_number_electronic            
     * @param unknown $organization_code            
     * @param unknown $organization_code_electronic            
     * @param unknown $general_taxpayer            
     * @param unknown $bank_account_name            
     * @param unknown $bank_account_number            
     * @param unknown $bank_name            
     * @param unknown $bank_code            
     * @param unknown $bank_address            
     * @param unknown $bank_licence_electronic            
     * @param unknown $is_settlement_account            
     * @param unknown $settlement_bank_account_name            
     * @param unknown $settlement_bank_account_number            
     * @param unknown $settlement_bank_name            
     * @param unknown $settlement_bank_code            
     * @param unknown $settlement_bank_address            
     * @param unknown $tax_registration_certificate            
     * @param unknown $taxpayer_id            
     * @param unknown $tax_registration_certificate_electronic            
     * @param unknown $shop_name            
     * @param unknown $apply_state            
     * @param unknown $apply_message            
     * @param unknown $apply_year            
     * @param unknown $shop_type_name            
     * @param unknown $shop_type_id            
     * @param unknown $shop_group_name            
     * @param unknown $shop_group_id            
     * @param unknown $paying_money_certificate            
     * @param unknown $paying_money_certificate_explain            
     * @param unknown $paying_amount            
     */
    function addShopApply($apply_type, $uid, $company_name, $company_province_id, $company_city_id, $company_district_id, $company_address_detail, $company_phone, $company_type, $company_employee_count, $company_registered_capital, $contacts_name, $contacts_phone, $contacts_email, $contacts_card_no, $contacts_card_electronic_1, $contacts_card_electronic_2, $contacts_card_electronic_3, $business_licence_number, $business_sphere, $business_licence_number_electronic, $organization_code, $organization_code_electronic, $general_taxpayer, $bank_account_name, $bank_account_number, $bank_name, $bank_code, $bank_address, $bank_licence_electronic, $is_settlement_account, $settlement_bank_account_name, $settlement_bank_account_number, $settlement_bank_name, $settlement_bank_code, $settlement_bank_address, $tax_registration_certificate, $taxpayer_id, $tax_registration_certificate_electronic, $shop_name, $apply_state, $apply_message, $apply_year, $shop_type_name, $shop_type_id, $shop_group_name, $shop_group_id, $paying_money_certificate, $paying_money_certificate_explain, $paying_amount, $recommend_uid);

    /**
     * 获取店铺详情
     *
     * @param unknown $shop_id            
     */
    function getShopDetail($shop_id);

    function getShopInfo($shop_id, $field = '*');

    /**
     * 添加店铺
     *
     * @param unknown $shop_name            
     * @param unknown $shop_type            
     * @param unknown $uid            
     * @param unknown $shop_group_id            
     * @param unknown $shop_company_name            
     * @param unknown $province_id            
     * @param unknown $city_id            
     * @param unknown $shop_address            
     * @param unknown $shop_zip            
     * @param unknown $shop_sort            
     */
    function addshop($shop_name, $shop_type, $uid, $shop_group_id, $shop_company_name, $province_id, $city_id, $shop_address, $shop_zip, $shop_sort, $recommend_uid);

    /**
     * 处理店铺申请请求
     *
     * @param unknown $shop_apply_id            
     * @param unknown $type
     *            'agree,disagree'
     */
    function dealwithShopApply($shop_apply_id, $type);

    /**
     * 店铺申请列表
     *
     * @param number $page_index            
     * @param number $page_size            
     * @param string $where            
     * @param string $order            
     */
    function getShopApplyList($page_index = 1, $page_size = 0, $where = '', $order = '');

    /**
     * 获取店铺等级类型列表
     *
     * @param number $page_index            
     * @param number $page_size            
     * @param string $where            
     * @param string $order            
     */
    function getShopTypeList($page_index = 1, $page_size = 0, $where = '', $order = '');

    /**
     * 添加店铺分组/分类
     *
     * @param unknown $group_name            
     * @param unknown $group_sort            
     */
    function addShopGroup($group_name, $group_sort);

    /**
     * 修改店铺分组/分类
     *
     * @param unknown $shop_group_id            
     * @param unknown $group_name            
     * @param unknown $group_sort            
     */
    function updateShopGroup($shop_group_id, $group_name, $group_sort);

    /**
     * 获取店铺分组/分类详情
     *
     * @param unknown $shop_id            
     */
    function getShopGroupDetail($shop_group_id);

    /**
     * 删除店铺分组/分类详情
     *
     * @param unknown $shop_group_id            
     */
    function delShopGroup($shop_group_id);

    /**
     * 获取 店铺申请的 详细信息
     *
     * @param unknown $apply_id            
     */
    function getShopApplyDetail($apply_id);

    /**
     * 添加店铺等级
     *
     * @param unknown $type_name            
     * @param unknown $type_module_array            
     * @param unknown $type_desc            
     * @param unknown $type_sort            
     */
    function addShopType($type_name, $type_module_array, $type_desc, $type_sort);

    /**
     * 修改店铺等级
     *
     * @param unknown $instance_typeid            
     * @param unknown $type_name            
     * @param unknown $type_module_array            
     * @param unknown $type_desc            
     * @param unknown $type_sort            
     */
    function updateShopType($instance_typeid, $type_name, $type_module_array, $type_desc, $type_sort);

    /**
     * 店铺等级详情
     *
     * @param unknown $instance_typeid            
     */
    function getShopTypeDetail($instance_typeid);

    /**
     * 修改店铺 （店铺后台端）
     *
     * @param unknown $shop_id            
     * @param unknown $shop_logo            
     * @param unknown $shop_banner            
     * @param unknown $shop_avatar            
     * @param unknown $shop_qq            
     * @param unknown $shop_ww            
     * @param unknown $shop_phone            
     * @param unknown $shop_keywords            
     * @param unknown $shop_description            
     */
    function updateShopConfigByshop($shop_id, $shop_logo, $shop_banner, $shop_avatar, $shop_qrcode, $shop_qq, $shop_ww, $shop_phone, $shop_keywords, $shop_description);

    /**
     * 修改店铺 （平台对店铺的修改）
     *
     * @param unknown $shop_id            
     * @param unknown $shop_name            
     * @param unknown $shop_group_id            
     * @param unknown $shop_type            
     * @param unknown $shop_credit            
     * @param unknown $shop_desccredit            
     * @param unknown $shop_servicecredit            
     * @param unknown $shop_deliverycredit            
     * @param unknown $store_qtian            
     * @param unknown $shop_zhping            
     * @param unknown $shop_erxiaoshi            
     * @param unknown $shop_tuihuo            
     * @param unknown $shop_shiyong            
     * @param unknown $shop_xiaoxie            
     * @param unknown $shop_huodaofk            
     * @param unknown $shop_state            
     * @param unknown $shop_close_info            
     */
    function updateShopConfigByPlatform($shop_id, $shop_name, $shop_group_id, $shop_type, $shop_credit, $shop_desccredit, $shop_servicecredit, $shop_deliverycredit, $store_qtian, $shop_zhping, $shop_erxiaoshi, $shop_tuihuo, $shop_shiyong, $shop_shiti, $shop_xiaoxie, $shop_huodaofk, $shop_state, $shop_close_info);

    /**
     *
     * @param unknown $apply_id            
     * @param unknown $company_name            
     * @param unknown $company_province_id            
     * @param unknown $company_city_id            
     * @param unknown $company_district_id            
     * @param unknown $company_address_detail            
     * @param unknown $company_phone            
     * @param unknown $company_employee_count            
     * @param unknown $company_registered_capital            
     * @param unknown $contacts_name            
     * @param unknown $contacts_phone            
     * @param unknown $contacts_email            
     * @param unknown $business_licence_number            
     * @param unknown $business_sphere            
     * @param unknown $business_licence_number_electronic            
     * @param unknown $organization_code            
     * @param unknown $organization_code_electronic            
     * @param unknown $general_taxpayer            
     * @param unknown $bank_account_name            
     * @param unknown $bank_account_number            
     * @param unknown $bank_name            
     * @param unknown $bank_code            
     * @param unknown $bank_address            
     * @param unknown $bank_licence_electronic            
     * @param unknown $is_settlement_account            
     * @param unknown $settlement_bank_account_name            
     * @param unknown $settlement_bank_account_number            
     * @param unknown $settlement_bank_name            
     * @param unknown $settlement_bank_code            
     * @param unknown $settlement_bank_address            
     * @param unknown $tax_registration_certificate            
     * @param unknown $taxpayer_id            
     * @param unknown $tax_registration_certificate_electronic            
     */
    function updateShopApply($apply_id, $company_name, $company_province_id, $company_city_id, $company_district_id, $company_address_detail, $company_phone, $company_employee_count, $company_registered_capital, $contacts_name, $contacts_phone, $contacts_email, $business_licence_number, $business_sphere, $business_licence_number_electronic, $organization_code, $organization_code_electronic, $general_taxpayer, $bank_account_name, $bank_account_number, $bank_name, $bank_code, $bank_address, $bank_licence_electronic, $is_settlement_account, $settlement_bank_account_name, $settlement_bank_account_number, $settlement_bank_name, $settlement_bank_code, $settlement_bank_address, $tax_registration_certificate, $taxpayer_id, $tax_registration_certificate_electronic);

    /**
     * 获取用户在本店铺的消费
     *
     * @param unknown $shop_id            
     * @param unknown $uid            
     */
    function getShopUserConsume($shop_id, $uid);

    /**
     * 获取店铺分享设置
     *
     * @param unknown $shop_id            
     */
    function getShopShareConfig($shop_id);

    /**
     * 修改店铺分享设置
     *
     * @param unknown $shop_id            
     */
    function updateShopShareCinfig($shop_id, $goods_param_1, $goods_param_2, $shop_param_1, $shop_param_2, $shop_param_3, $qrcode_param_1, $qrcode_param_2);

    /**
     * 店铺收益账单列表
     *
     * @param unknown $page_index            
     * @param number $page_size            
     * @param string $where            
     * @param string $order            
     */
    function getShopAccountList($page_index, $page_size = 0, $where = '', $order = '');

    /**
     * 店铺提现列表
     *
     * @param unknown $page_index            
     * @param number $page_size            
     * @param string $where            
     * @param string $order            
     */
    function getShopAccountWithdrawList($page_index, $page_size = 0, $where = '', $order = '');

    /**
     * 店铺提现账户
     *
     * @param unknown $condition            
     */
    function getShopBankAccountAll($condition);

    /**
     * 添加店铺银行账户
     *
     * @param unknown $shop            
     * @param unknown $bank_type            
     * @param unknown $branch_bank_name            
     * @param unknown $realname            
     * @param unknown $account_number            
     * @param unknown $mobile            
     */
    function addShopBankAccount($shop_id, $bank_type, $branch_bank_name, $realname, $account_number, $mobile);

    /**
     * 修改店铺银行账户
     *
     * @param unknown $shop            
     * @param unknown $bank_type            
     * @param unknown $branch_bank_name            
     * @param unknown $realname            
     * @param unknown $account_number            
     * @param unknown $mobile            
     * @param unknown $id            
     */
    function updateShopBankAccount($shop_id, $bank_type, $branch_bank_name, $realname, $account_number, $mobile, $id);

    /**
     * 修改银行账户是否默认
     *
     * @param unknown $shop            
     * @param unknown $id            
     * @param unknown $is_default            
     */
    function modifyShopBankAccountIsdefault($shop_id, $id);

    /**
     * 删除店铺银行账户
     *
     * @param unknown $shop_id            
     * @param unknown $condition            
     */
    function deleteShopBankAccouht($condition);

    /**
     * 店铺账户统计
     *
     * @param unknown $shop_id            
     */
    function getShopAccount($shop_id);

    /**
     * 店铺申请提现
     *
     * @param unknown $shop_id            
     * @param unknown $withdraw_no            
     * @param unknown $bank_account_id            
     * @param unknown $cash            
     */
    function applyShopAccountWithdraw($shop_id, $bank_account_id, $cash);

    /**
     * 店铺提现 审核
     *
     * @param unknown $shop_id            
     * @param unknown $id            
     * @param unknown $status            
     */
    function shopAccountWithdrawAudit($shop_id, $id, $status);

    /**
     * 获取银行账户详情
     *
     * @param unknown $shop_id            
     * @param unknown $id            
     */
    function getShopBankAccountDetail($shop_id, $id);

    /**
     * 店铺 月 账户记录
     *
     * @param unknown $shop_id            
     */
    function getShopAccountMonthRecord($shop_id);

    /**
     * 店铺账户统计列表
     *
     * @param unknown $page_index            
     * @param number $page_size            
     * @param string $condition            
     * @param string $order            
     */
    function getShopAccountCountList($page_index, $page_size = 0, $condition = '', $order = '');

    /**
     * 店铺账户明细
     *
     * @param unknown $page_index            
     * @param number $page_size            
     * @param string $condition            
     * @param string $order            
     */
    function getShopAccountRecordsList($page_index, $page_size = 0, $condition = '', $order = '');

    /**
     * 店铺销售订单列表
     *
     * @param unknown $page_index            
     * @param number $page_size            
     * @param string $condition            
     * @param string $order            
     */
    function getShopOrderAccountRecordsList($page_index, $page_size = 0, $condition = '', $order = '');

    /**
     * 获取所有店铺
     *
     * @param unknown $condition            
     */
    function getShopAll($condition);

    /**
     * 获取店铺账户记录统计
     *
     * @param unknown $condition            
     */
    function getShopAccountRecordCount($start_date, $end_date, $shop_id);

    /**
     * 获取店商品销售
     *
     * @param unknown $start_date            
     * @param unknown $end_date            
     */
    function getShopAccountSales($condition);

    /**
     * 商品销售统计
     */
    function getShopGoodsSales($condition);

    /**
     * 修改平台提出比率
     *
     * @param unknown $shop_id            
     */
    function updateShopPlatformCommissionRate($shop_id, $shop_platform_commission_rate);
    /**
     * 获取店铺总数
     * @param unknown $condition
     */
    function getShopCount($condition);
    /**
     * 添加提现设置
     *
     * @param unknown $ad_image
     * @param unknown $link_url
     * @param unknown $sort
     */
    function addMemberWithdrawSetting($shop_id, $withdraw_cash_min, $withdraw_multiple, $withdraw_poundage, $withdraw_message, $withdraw_account_type);
    /**
     * 店铺提现金额
     * @param unknown $condition
     */
    function getShopWithdrawCount($condition);    
    /**
     * 修改提现设置
     *
     * @param unknown $id
     * @param unknown $ad_image
     * @param unknown $link_url
     * @param unknown $sort
    */
    function updateMemberWithdrawSetting($shop_id, $withdraw_cash_min, $withdraw_multiple, $withdraw_poundage, $withdraw_message, $withdraw_account_type, $id);
    
    /**
     * 获取提现设置信息
     * @param unknown $shop_id
     */
    function getWithdrawInfo($shop_id);
    /**
     * 修改店铺列表排序
     * @param unknown $shop_id
     */
    function updateShopSort($shop_id,$shop_sort);
    /**
     * 设置店铺推荐
     * @param unknown $shop_id
     */
    function setRecomment($shop_id,$shop_recommend);
    /**
     * 查询导航的商城模块
     */
    function getShopNavigationTemplate($use_type);
    /**
     * 自提点管理
     * @param unknown $shop_id
     * @param unknown $name
     * @param unknown $address
     * @param unknown $contact
     * @param unknown $phone
     * @param unknown $province_id
     * @param unknown $city_id
     * @param unknown $district_id
     * @param unknown $longitude
     * @param unknown $latitude
     */
    function addPickupPoint($shop_id, $name, $address, $contact, $phone, $province_id, $city_id, $district_id, $longitude, $latitude);
    /**
     * 修改自提点
     * @param unknown $id
     * @param unknown $shop_id
     * @param unknown $name
     * @param unknown $address
     * @param unknown $contact
     * @param unknown $phone
     * @param unknown $province_id
     * @param unknown $city_id
     * @param unknown $district_id
     * @param unknown $longitude
     * @param unknown $latitude
     */
    function updatePickupPoint($id, $shop_id, $name, $address, $contact, $phone, $province_id, $city_id, $district_id, $longitude, $latitude);
    /**
     * 获取自提点列表
     * @param unknown $page_index
     * @param number $page_size
     * @param string $where
     * @param string $order
     */
    function getPickupPointList($page_index, $page_size = 0, $where = '', $order = '');
    /**
     * 删除自提点
     * @param unknown $pickip_id
     */
    function deletePickupPoint($pickip_id);
    /**
     * 获取自提点详情
     * @param unknown $id
     */
    function getPickupPointDetail($pickip_id);
    
}

