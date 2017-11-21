<?php
/**
 * Shop.php
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
namespace data\service;

/**
 * 店铺服务层
 */
use data\service\BaseService as BaseService;
use data\api\IShop;
use data\model\NsShopAdModel as NsShopAdModel;
use data\model\NsShopNavigationModel as NsShopNavigationModel;
use data\model\NsShopModel as NsShopModel;
use data\model\NsShopGroupModel as NsShopGroupModel;
use data\model\NsShopApplyModel;
use data\model\UserModel;
use data\model\InstanceTypeModel;
use data\service\WebSite;
use data\model\InstanceModel;
use data\model\AuthGroupModel;
use data\model\NsOrderModel;
use think;
use data\model\NsShopWeixinShareModel;
use data\model\NsShopAccountModel;
use data\model\NsShopAccountRecordsModel;
use data\service\Order as OrderService;
use data\model\NsShopWithdrawModel;
use data\model\NsShopBankAccountModel;
use data\service\shopaccount\ShopAccount as ShopAccountService;
use data\model\NsShopOrderGoodsAccountViewModel;
use data\model\NsGoodsModel;
use data\model\niufenxiao\NfxShopRegionAgentConfigModel;
use data\service\shopaccount\ShopAccount;
use data\model\NsShopOrderReturnModel;
use data\model\NsOrderGoodsModel;
use data\model\NsShopOrderGoodsReturnModel;
use data\model\NsMemberWithdrawSettingModel;
use data\model\NsRewardRuleModel;
use data\model\ProvinceModel;
use data\model\CityModel;
use data\model\DistrictModel;
use data\service\Album;
use data\model\NsShopReturnSetModel;
use data\model\NsOrderGoodsViewModel;
use data\model\BaseModel;
use data\model\NsShopNavigationTemplateModel;
use data\model\NsPickupPointModel;
use data\model\NsMemberAccountModel;

class Shop extends BaseService implements IShop
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IConfig::getShopAdList()
     */
    public function getShopAdList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $shop_ad = new NsShopAdModel();
        $list = $shop_ad->pageQuery($page_index, $page_size, $condition, $order, '*');
        return $list;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IConfig::addShopAd()
     */
    public function addShopAd($ad_image, $link_url, $sort, $type, $background)
    {
        $data['shop_id'] = $this->instance_id;
        $data['ad_image'] = $ad_image;
        $data['link_url'] = $link_url;
        $data['sort'] = $sort;
        $data['type'] = $type;
        $data['background'] = $background;
        $shop_ad = new NsShopAdModel();
        $res = $shop_ad->save($data);
        $id = $shop_ad->id;
        return $id;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IConfig::updateShopAd()
     */
    public function updateShopAd($id, $ad_image, $link_url, $sort, $type, $background)
    {
        $data['shop_id'] = $this->instance_id;
        $data['ad_image'] = $ad_image;
        $data['link_url'] = $link_url;
        $data['sort'] = $sort;
        $data['type'] = $type;
        $data['background'] = $background;
        $shop_ad = new NsShopAdModel();
        $res = $shop_ad->save($data, [
            'id' => $id
        ]);
        return $res;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IShop::getShopAdDetail()
     */
    public function getShopAdDetail($id)
    {
        $shop_ad = new NsShopAdModel();
        $info = $shop_ad->get($id);
        return $info;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IShop::delShopAd()
     */
    public function delShopAd($id)
    {
        $shop_ad = new NsShopAdModel();
        $res = $shop_ad->destroy([
            'id' => $id,
            'shop_id' => $this->instance_id
        ]);
        return $res;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IShop::addShopNavigation()
     */
    public function addShopNavigation($nav_title, $nav_url, $type, $sort, $align, $nav_type, $is_blank, $template_name)
    {
        $shop_navigation = new NsShopNavigationModel();
        $data = array(
            'shop_id' => $this->instance_id,
            'nav_title' => $nav_title,
            'nav_url' => $nav_url,
            'type' => $type,
            'align' => $align,
            'sort' => $sort,
            'nav_type' => $nav_type,
            'is_blank' => $is_blank,
            'template_name' => $template_name,
            'create_time' => time(),
            'modify_time' => time()
        );
        $shop_navigation->save($data);
        $retval = $shop_navigation->nav_id;
        return $retval;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IShop::addShopNavigation()
     */
    public function updateShopNavigation($nav_id, $nav_title, $nav_url, $type, $sort, $align, $nav_type, $is_blank, $template_name)
    {
        $shop_navigation = new NsShopNavigationModel();
        $data = array(
            'nav_title' => $nav_title,
            'nav_url' => $nav_url,
            'type' => $type,
            'align' => $align,
            'sort' => $sort,
            'nav_type' => $nav_type,
            'is_blank' => $is_blank,
            'template_name' => $template_name,
            'modify_time' => time()
        );
        $shop_navigation->save($data, [
            'nav_id' => $nav_id
        ]);
        return $nav_id;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \data\api\IShop::updateShopSort()
     */
    public function updateShopSort($shop_id, $shop_sort)
    {
        $shop = new NsShopModel();
        $data = array(
            'shop_sort' => $shop_sort
        );
        $shop->save($data, [
            'shop_id' => $shop_id
        ]);
        
        return $shop_id;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \data\api\IShop::setRecomment()
     */
    public function setRecomment($shop_id, $shop_recommend)
    {
        $shop = new NsShopModel();
        $data = array(
            'shop_recommend' => $shop_recommend
        );
        $shop->save($data, [
            'shop_id' => $shop_id
        ]);
        
        return $shop_id;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IShop::delShopNavigation()
     */
    public function delShopNavigation($nav_id)
    {
        $shop_navigation = new NsShopNavigationModel();
        $retval = $shop_navigation->destroy($nav_id);
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::ShopNavigationList()
     */
    public function ShopNavigationList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $shop_navigation = new NsShopNavigationModel();
        $list = $shop_navigation->pageQuery($page_index, $page_size, $condition, $order, '*');
        return $list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::shopNavigationDetail()
     */
    public function shopNavigationDetail($nav_id)
    {
        $shop_navigation = new NsShopNavigationModel();
        $info = $shop_navigation->get($nav_id);
        return $info;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::modifyShopNavigationSort()
     */
    public function modifyShopNavigationSort($nav_id, $sort)
    {
        $shop_navigation = new NsShopNavigationModel();
        $retval = $shop_navigation->save([
            'sort' => $sort
        ], [
            'nav_id' => $nav_id
        ]);
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::getShopList()
     */
    public function getShopList($page_index = 1, $page_size = 0, $where = '', $order = '')
    {
        $shop = new NsShopModel();
        $shop_type = new InstanceTypeModel();
        $shop_group = new NsShopGroupModel();
        $list = $shop->pageQuery($page_index, $page_size, $where, $order, '*');
        foreach ($list['data'] as $k => $v) {
            $list['data'][$k]['shop_type_name'] = $shop_type->getInfo([
                'instance_typeid' => $v['shop_type']
            ], 'type_name')['type_name'];
            
            $list['data'][$k]['grou_name'] = $shop_group->getInfo([
                'shop_group_id' => $v['shop_group_id']
            ], 'group_name')["group_name"];
        }
        return $list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::getShopGroup()
     */
    public function getShopGroup($page_index = 1, $page_size = 0, $where = '', $order = '')
    {
        $shop_group = new NsShopGroupModel();
        $list = $shop_group->pageQuery($page_index, $page_size, $where, $order, '*');
        return $list;
    }

    /**
     * 申请店铺
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::addShopApply()
     */
    public function addShopApply($apply_type, $uid, $company_name, $company_province_id, $company_city_id, $company_district_id, $company_address_detail, $company_phone, $company_type, $company_employee_count, $company_registered_capital, $contacts_name, $contacts_phone, $contacts_email, $contacts_card_no, $contacts_card_electronic_1, $contacts_card_electronic_2, $contacts_card_electronic_3, $business_licence_number, $business_sphere, $business_licence_number_electronic, $organization_code, $organization_code_electronic, $general_taxpayer, $bank_account_name, $bank_account_number, $bank_name, $bank_code, $bank_address, $bank_licence_electronic, $is_settlement_account, $settlement_bank_account_name, $settlement_bank_account_number, $settlement_bank_name, $settlement_bank_code, $settlement_bank_address, $tax_registration_certificate, $taxpayer_id, $tax_registration_certificate_electronic, $shop_name, $apply_state, $apply_message, $apply_year, $shop_type_name, $shop_type_id, $shop_group_name, $shop_group_id, $paying_money_certificate, $paying_money_certificate_explain, $paying_amount, $recommend_uid)
    {
        $user = new UserModel();
        // 得到当前会员的信息
        $user_info = $user->get($uid);
        $shop_apply = new NsShopApplyModel();
        $condition['uid'] = $uid;
        $condition['apply_state'] = array(
            "in",
            '1,2'
        );
        $count = $shop_apply->getCount($condition);
        if ($count > 0) {
            return 0;
        }
        $data = array(
            "apply_type" => $apply_type,
            "uid" => $uid,
            "company_name" => $company_name,
            "company_province_id" => $company_province_id,
            "company_city_id" => $company_city_id,
            "company_district_id" => $company_district_id,
            "company_address_detail" => $company_address_detail,
            "company_phone" => $company_phone,
            "company_type" => $company_type,
            "company_employee_count" => $company_employee_count,
            "company_registered_capital" => $company_registered_capital,
            "contacts_name" => $contacts_name,
            "contacts_phone" => $contacts_phone,
            "contacts_email" => $contacts_email,
            "contacts_card_no" => $contacts_card_no,
            "contacts_card_electronic_1" => $contacts_card_electronic_1,
            "contacts_card_electronic_2" => $contacts_card_electronic_2,
            "contacts_card_electronic_3" => $contacts_card_electronic_3,
            "business_licence_number" => $business_licence_number,
            "business_sphere" => $business_sphere,
            "business_licence_number_electronic" => $business_licence_number_electronic,
            "organization_code" => $organization_code,
            "organization_code_electronic" => $organization_code_electronic,
            "general_taxpayer" => $general_taxpayer,
            "bank_account_name" => $bank_account_name,
            "bank_account_number" => $bank_account_number,
            "bank_name" => $bank_name,
            "bank_code" => $bank_code,
            "bank_address" => $bank_address, // 默认输入''
            "bank_licence_electronic" => $bank_licence_electronic, // 默认输入''
            "is_settlement_account" => $is_settlement_account,
            "settlement_bank_account_name" => $settlement_bank_account_name,
            "settlement_bank_account_number" => $settlement_bank_account_number,
            "settlement_bank_name" => $settlement_bank_name,
            "settlement_bank_code" => $settlement_bank_code,
            "settlement_bank_address" => $settlement_bank_address,
            "tax_registration_certificate" => $tax_registration_certificate,
            "taxpayer_id" => $taxpayer_id,
            "tax_registration_certificate_electronic" => $tax_registration_certificate_electronic,
            "shop_name" => $shop_name,
            "apply_state" => $apply_state, // 默认输入1
            "apply_message" => $apply_message,
            "apply_year" => $apply_year, // 默认1
            "shop_type_name" => $shop_type_name,
            "shop_type_id" => $shop_type_id,
            "shop_group_name" => $shop_group_name,
            "shop_group_id" => $shop_group_id,
            "paying_money_certificate" => $paying_money_certificate,
            "paying_money_certificate_explain" => $paying_money_certificate_explain,
            "paying_amount" => $paying_amount,
            "recommend_uid" => $recommend_uid
        );
        $shop_apply->save($data);
        $retval = $shop_apply->apply_id;
        
        // 如果用户是被拒绝过的重新申请的就删除了以前的拒绝信息
        if (! empty($shop_apply->apply_id)) {
            $shop_apply->destroy([
                'uid' => $this->uid,
                'apply_state' => - 1
            ]);
        }
        
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::getShopDetail()
     */
    public function getShopDetail($shop_id)
    {
        $shop = new NsShopModel();
        $shop_group = new NsShopGroupModel();
        $instance_type = new InstanceTypeModel();
        $shop_info = array();
        $base_info = $shop->get($shop_id);
        $shop_info['base_info'] = $base_info;
        if (! empty($base_info)) {
            $group_info = $shop_group->get($base_info['shop_group_id']);
            $shop_info['group_info'] = $group_info;
            $instance_type_info = $instance_type->get($base_info['shop_type']);
            $shop_info['instance_type_info'] = $instance_type_info;
        }
        return $shop_info;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::getShopInfo()
     */
    public function getShopInfo($shop_id, $field = '*')
    {
        $shop = new NsShopModel();
        $info = $shop->getInfo([
            'shop_id' => $shop_id
        ], $field);
        return $info;
    }

    /**
     * (non-PHPdoc)
     * shop_id int(11) NOT NULL COMMENT '店铺索引id',
     * shop_name varchar(50) NOT NULL COMMENT '店铺名称',
     * shop_type int(11) NOT NULL COMMENT '店铺类型等级',
     * uid int(11) NOT NULL COMMENT '会员id',
     * shop_group_id int(11) NOT NULL COMMENT '店铺分类',
     * shop_company_name varchar(50) DEFAULT NULL COMMENT '店铺公司名称',
     * province_id mediumint(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '店铺所在省份ID',
     * city_id mediumint(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '店铺所在市ID',
     * shop_address varchar(100) NOT NULL DEFAULT '' COMMENT '详细地区',
     * shop_zip varchar(10) NOT NULL DEFAULT '' COMMENT '邮政编码',
     * shop_state tinyint(1) NOT NULL DEFAULT 2 COMMENT '店铺状态，0关闭，1开启，2审核中',
     * shop_close_info varchar(255) DEFAULT NULL COMMENT '店铺关闭原因',
     * shop_sort int(11) NOT NULL DEFAULT 0 COMMENT '店铺排序',
     * shop_create_time varchar(10) NOT NULL DEFAULT '0' COMMENT '店铺时间',
     * shop_end_time varchar(10) DEFAULT NULL COMMENT '店铺关闭时间',
     * shop_logo varchar(255) DEFAULT NULL COMMENT '店铺logo',
     * shop_banner varchar(255) DEFAULT NULL COMMENT '店铺横幅',
     * shop_avatar varchar(150) DEFAULT NULL COMMENT '店铺头像',
     * shop_keywords varchar(255) NOT NULL DEFAULT '' COMMENT '店铺seo关键字',
     * shop_description varchar(255) NOT NULL DEFAULT '' COMMENT '店铺seo描述',
     * shop_qq varchar(50) DEFAULT NULL COMMENT 'QQ',
     * shop_ww varchar(50) DEFAULT NULL COMMENT '阿里旺旺',
     * shop_phone varchar(20) DEFAULT NULL COMMENT '商家电话',
     * shop_domain varchar(50) DEFAULT NULL COMMENT '店铺二级域名',
     * shop_domain_times tinyint(1) UNSIGNED DEFAULT 0 COMMENT '二级域名修改次数',
     * shop_recommend tinyint(1) NOT NULL DEFAULT 0 COMMENT '推荐，0为否，1为是，默认为0',
     * shop_credit int(10) NOT NULL DEFAULT 0 COMMENT '店铺信用',
     * shop_desccredit float NOT NULL DEFAULT 0 COMMENT '描述相符度分数',
     * shop_servicecredit float NOT NULL DEFAULT 0 COMMENT '服务态度分数',
     * shop_deliverycredit float NOT NULL DEFAULT 0 COMMENT '发货速度分数',
     * shop_collect int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '店铺收藏数量',
     * shop_stamp varchar(200) DEFAULT NULL COMMENT '店铺印章',
     * shop_printdesc varchar(500) DEFAULT NULL COMMENT '打印订单页面下方说明文字',
     * shop_sales int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '店铺销量',
     * shop_workingtime varchar(100) DEFAULT NULL COMMENT '工作时间',
     * live_store_name varchar(255) DEFAULT NULL COMMENT '商铺名称',
     * live_store_address varchar(255) DEFAULT NULL COMMENT '商家地址',
     * live_store_tel varchar(255) DEFAULT NULL COMMENT '商铺电话',
     * live_store_bus varchar(255) DEFAULT NULL COMMENT '公交线路',
     * shop_vrcode_prefix char(3) DEFAULT NULL COMMENT '商家兑换码前缀',
     * store_qtian tinyint(1) DEFAULT 0 COMMENT '7天退换',
     * shop_zhping tinyint(1) DEFAULT 0 COMMENT '正品保障',
     * shop_erxiaoshi tinyint(1) DEFAULT 0 COMMENT '两小时发货',
     * shop_tuihuo tinyint(1) DEFAULT 0 COMMENT '退货承诺',
     * shop_shiyong tinyint(1) DEFAULT 0 COMMENT '试用中心',
     * shop_shiti tinyint(1) DEFAULT 0 COMMENT '实体验证',
     * shop_xiaoxie tinyint(1) DEFAULT 0 COMMENT '消协保证',
     * shop_huodaofk tinyint(1) DEFAULT 0 COMMENT '货到付款',
     * shop_free_time varchar(10) DEFAULT NULL COMMENT '商家配送时间',
     * shop_region varchar(50) DEFAULT NULL COMMENT '店铺默认配送区域',
     *
     * @see \data\api\IShop::addshop()
     */
    public function addshop($shop_name, $shop_type, $uid, $shop_group_id, $shop_company_name, $province_id, $city_id, $shop_address, $shop_zip, $shop_sort, $recommend_uid = 0)
    {
        $shop = new NsShopModel();
        $condition = array(
            "uid" => $uid
        );
        $count = $shop->getCount($condition);
        // 防止出现重复店铺、重复提交问题
        if ($count > 0) {
            return - 1;
        }
        $shop->startTrans();
        try {
            $website = new WebSite();
            $shop_id = $website->addSystemInstance($uid, $shop_name, $shop_type);
            $data = array(
                'shop_id' => $shop_id,
                'shop_name' => $shop_name,
                'shop_type' => $shop_type,
                'uid' => $uid,
                'shop_group_id' => $shop_group_id,
                'shop_company_name' => $shop_company_name,
                'province_id' => $province_id,
                'city_id' => $city_id,
                'shop_address' => $shop_address,
                'shop_zip' => $shop_zip,
                'shop_sort' => $shop_sort,
                'recommend_uid' => $recommend_uid
            );
            // 添加店铺
            $retval = $shop->save($data);
            $this->addShopConfig($shop_id);
            // 添加店铺账户
            $shop_account = new NsShopAccountModel();
            $data_account = array(
                'shop_id' => $shop_id
            );
            $shop_account->save($data_account);
            $shop->commit();
            return $shop_id;
        } catch (\Exception $e) {
            $shop->rollback();
            return $e->getMessage();
        }
    }
    // 店铺创建后续操作
    private function addShopConfig($shop_id)
    {
        $shop_region_agent = new NfxShopRegionAgentConfigModel();
        $count = $shop_region_agent->where([
            "shop_id" => $shop_id
        ])->count();
        if ($count == 0) {
            // 默认添加
            $shop_region_agent = new NfxShopRegionAgentConfigModel();
            $data = array(
                "shop_id" => $shop_id,
                "create_time" => time()
            );
            $shop_region_agent->save($data);
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::dealwithShopApply()
     */
    public function dealwithShopApply($shop_apply_id, $type)
    {
        $shop_apply = new NsShopApplyModel();
        if ($type == 'disagree') {
            $retval = $shop_apply->save([
                'apply_state' => - 1
            ], [
                'apply_id' => $shop_apply_id
            ]);
            return $retval;
            // 拒绝审核通过
        } elseif ($type == 'agree') {
            $shop_apply = new NsShopApplyModel();
            // 审核通过
            $shop_apply->startTrans();
            try {
                $shop_apply->save([
                    'apply_state' => 2
                ], [
                    'apply_id' => $shop_apply_id
                ]);
                $apply_data = $shop_apply->get($shop_apply_id);
                $res_data = $this->addshop($apply_data['shop_name'], $apply_data['shop_type_id'], $apply_data['uid'], $apply_data['shop_group_id'], $apply_data['company_name'], $apply_data['company_province_id'], $apply_data['company_city_id'], $apply_data['company_address_detail'], '', '1999', $apply_data["recommend_uid"]);
                
                if ($res_data > 0) {
                    
                    $album_name = "默认相册";
                    $sort = 0;
                    $album = new Album();
                    $add_album = $album->addAlbumClass($album_name, $sort, 0, '', 1, $res_data);
                    
                    $shop_apply->save([
                        'shop_id' => $res_data
                    ], [
                        'apply_id' => $shop_apply_id
                    ]);
                    $shop_apply->commit();
                    return 1;
                } else {
                    $shop_apply->rollback();
                    return $res_data;
                }
            } catch (\Exception $e) {
                $shop_apply->rollback();
                return $e;
            }
        } else {
            return - 1;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::getShopApplyList()
     */
    public function getShopApplyList($page_index = 1, $page_size = 0, $where = '', $order = 'apply_id DESC')
    {
        $shop_apply = new NsShopApplyModel();
        $list = $shop_apply->pageQuery($page_index, $page_size, $where, $order, '*');
        
        if (! empty($list['data'])) {
            foreach ($list['data'] as $k => $v) {
                $user = new UserModel();
                $userinfo = $user->getInfo([
                    'uid' => $v['uid']
                ], "*");
                $user_name = "";
                $user_tel = "";
                $user_headimg = '';
                if (count($userinfo) > 0) {
                    $user_name = $userinfo["nick_name"];
                    $user_tel = $userinfo["user_tel"];
                    $user_headimg = $userinfo["user_headimg"];
                }
                $list['data'][$k]['real_name'] = $user_name;
                $list['data'][$k]['user_tel'] = $user_tel;
                $list['data'][$k]['user_headimg'] = $user_headimg;
            }
        }
        
        return $list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::getShopTypeList()
     */
    public function getShopTypeList($page_index = 1, $page_size = 0, $where = '', $order = '')
    {
        $instance_type = new InstanceTypeModel();
        $list = $instance_type->pageQuery($page_index, $page_size, $where, $order, '*');
        return $list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::addShopGroup()
     */
    public function addShopGroup($group_name, $group_sort)
    {
        $shop_group = new NsShopGroupModel();
        $data = array(
            'group_name' => $group_name,
            'group_sort' => $group_sort,
            'create_time' => time(),
            'modify_time' => time()
        );
        $retval = $shop_group->save($data);
        return $shop_group->shop_group_id;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::updateShopGroup()
     */
    public function updateShopGroup($shop_group_id, $group_name, $group_sort)
    {
        $shop_group = new NsShopGroupModel();
        $data = array(
            'group_name' => $group_name,
            'group_sort' => $group_sort,
            'modify_time' => time()
        );
        $shop_group->save($data, [
            'shop_group_id' => $shop_group_id
        ]);
        return $shop_group_id;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::getShopGroupDetail()
     */
    public function getShopGroupDetail($shop_group_id)
    {
        $shop_group = new NsShopGroupModel();
        $info = $shop_group->get($shop_group_id);
        return $info;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::delShopGroup()
     */
    public function delShopGroup($shop_group_id)
    {
        $retval = '';
        $shop = new NsShopModel();
        $shop_list = $shop->getQuery([
            'shop_group_id' => $shop_group_id
        ], 'shop_id', '');
        if (! count($shop_list)) {
            $shop_group = new NsShopGroupModel();
            $retval = $shop_group->destroy([
                'shop_group_id' => $shop_group_id
            ]);
        }
        return $retval;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IShop::getShopApplyDetail()
     */
    public function getShopApplyDetail($apply_id)
    {
        $shop_apply = new NsShopApplyModel();
        $shop_apply_info = $shop_apply->get($apply_id);
        if (! empty($shop_apply_info)) {
            $recommend_name = "--";
            $user = new UserModel();
            $user_info = $user->getInfo(array(
                "uid" => $shop_apply_info["recommend_uid"]
            ));
            if (! empty($user_info)) {
                $recommend_name = $user_info["nick_name"];
            }
            $shop_apply_info["recommend_name"] = $recommend_name;
            // 区域解释
            $province_name = "";
            $city_name = "";
            $district_name = "";
            $province = new ProvinceModel();
            $province_info = $province->getInfo(array(
                "province_id" => $shop_apply_info["company_province_id"]
            ), "*");
            if (count($province_info) > 0) {
                $province_name = $province_info["province_name"];
            }
            $shop_apply_info['province_name'] = $province_name;
            $city = new CityModel();
            $city_info = $city->getInfo(array(
                "city_id" => $shop_apply_info["company_city_id"]
            ), "*");
            if (count($city_info) > 0) {
                $city_name = $city_info["city_name"];
            }
            $shop_apply_info['city_name'] = $city_name;
            $district = new DistrictModel();
            $district_info = $district->getInfo(array(
                "district_id" => $shop_apply_info["company_district_id"]
            ), "*");
            if (count($district_info) > 0) {
                $district_name = $district_info["district_name"];
            }
            $shop_apply_info['district_name'] = $district_name;
        }
        return $shop_apply_info;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::addShopType()
     */
    public function addShopType($type_name, $type_module_array, $type_desc, $type_sort)
    {
        $instance_type = new InstanceTypeModel();
        $data = array(
            'type_name' => $type_name,
            'type_module_array' => $type_module_array,
            'type_desc' => $type_desc,
            'type_sort' => $type_sort,
            'create_time' => time(),
            'modify_time' => time()
        );
        $instance_type->save($data);
        return $instance_type->instance_typeid;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::updateShopType()
     */
    public function updateShopType($instance_typeid, $type_name, $type_module_array, $type_desc, $type_sort)
    {
        try {
            $instance_type = new InstanceTypeModel();
            $instance_type->startTrans();
            $data = array(
                'instance_typeid' => $instance_typeid,
                'type_name' => $type_name,
                'type_module_array' => $type_module_array,
                'type_desc' => $type_desc,
                'type_sort' => $type_sort,
                'modify_time' => time()
            );
            $instance_type->save($data, [
                'instance_typeid' => $instance_typeid
            ]);
            
            $instance = new InstanceModel();
            $instance_list = $instance->getQuery([
                'instance_typeid' => $instance_typeid
            ], 'instance_id', '');
            $instance_arr = '';
            foreach ($instance_list as $item) {
                $instance_arr .= $item['instance_id'] . ',';
            }
            
            $instance_arr = rtrim($instance_arr, ",");
            $auth_group = new AuthGroupModel();
            $retval = $auth_group->update([
                'module_id_array' => $type_module_array
            ], [
                'instance_id' => array(
                    "IN",
                    $instance_arr
                ),
                'is_system' => 1
            ]);
            $instance_type->commit();
        } catch (\Exception $e) {
            $instance_type->rollback();
            $retval = $e->getMessage();
        }
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::getShopTypeDetail()
     */
    public function getShopTypeDetail($instance_typeid)
    {
        $instance_type = new InstanceTypeModel();
        $shop_type_info = $instance_type->get($instance_typeid);
        return $shop_type_info;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IShop::updateShopConfigByshop()
     */
    public function updateShopConfigByshop($shop_id, $shop_logo, $shop_banner, $shop_avatar, $shop_qrcode, $shop_qq, $shop_ww, $shop_phone, $shop_keywords, $shop_description)
    {
        $data = array(
            'shop_logo' => $shop_logo,
            'shop_banner' => $shop_banner,
            'shop_avatar' => $shop_avatar,
            'shop_qrcode' => $shop_qrcode,
            'shop_qq' => $shop_qq,
            'shop_ww' => $shop_ww,
            'shop_phone' => $shop_phone,
            'shop_keywords' => $shop_keywords,
            'shop_description' => $shop_description
        );
        $shop = new NsShopModel();
        $res = $shop->save($data, [
            'shop_id' => $shop_id
        ]);
        return $res;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IShop::updateShopConfigByPlatform()
     */
    public function updateShopConfigByPlatform($shop_id, $shop_name, $shop_group_id, $shop_type, $shop_credit, $shop_desccredit, $shop_servicecredit, $shop_deliverycredit, $store_qtian, $shop_zhping, $shop_erxiaoshi, $shop_tuihuo, $shop_shiyong, $shop_shiti, $shop_xiaoxie, $shop_huodaofk, $shop_state, $shop_close_info)
    {
        $data = array(
            'shop_name' => $shop_name,
            'shop_group_id' => $shop_group_id,
            'shop_type' => $shop_type,
            'shop_credit' => $shop_credit,
            'shop_desccredit' => $shop_desccredit,
            'shop_servicecredit' => $shop_servicecredit,
            'shop_deliverycredit' => $shop_deliverycredit,
            'store_qtian' => $store_qtian,
            'shop_zhping' => $shop_zhping,
            'shop_erxiaoshi' => $shop_erxiaoshi,
            'shop_tuihuo' => $shop_tuihuo,
            'shop_shiyong' => $shop_shiyong,
            'shop_shiti' => $shop_shiti,
            'shop_xiaoxie' => $shop_xiaoxie,
            'shop_huodaofk' => $shop_huodaofk,
            'shop_state' => $shop_state,
            'shop_close_info' => $shop_close_info
        );
        $shop = new NsShopModel();
        $res = $shop->save($data, [
            'shop_id' => $shop_id
        ]);
        return $res;
    }

    public function updateShopApply($apply_id, $company_name, $company_province_id, $company_city_id, $company_district_id, $company_address_detail, $company_phone, $company_employee_count, $company_registered_capital, $contacts_name, $contacts_phone, $contacts_email, $business_licence_number, $business_sphere, $business_licence_number_electronic, $organization_code, $organization_code_electronic, $general_taxpayer, $bank_account_name, $bank_account_number, $bank_name, $bank_code, $bank_address, $bank_licence_electronic, $is_settlement_account, $settlement_bank_account_name, $settlement_bank_account_number, $settlement_bank_name, $settlement_bank_code, $settlement_bank_address, $tax_registration_certificate, $taxpayer_id, $tax_registration_certificate_electronic)
    {
        $data = array(
            'company_name' => $company_name,
            'company_province_id' => $company_province_id,
            'company_city_id' => $company_city_id,
            'company_district_id' => $company_district_id,
            'company_address_detail' => $company_address_detail,
            'company_phone' => $company_phone,
            'company_employee_count' => $company_employee_count,
            'company_registered_capital' => $company_registered_capital,
            'contacts_name' => $contacts_name,
            'contacts_phone' => $contacts_phone,
            'contacts_email' => $contacts_email,
            'business_licence_number' => $business_licence_number,
            'business_sphere' => $business_sphere,
            'business_licence_number_electronic' => $business_licence_number_electronic,
            'organization_code' => $organization_code,
            'organization_code_electronic' => $organization_code_electronic,
            'general_taxpayer' => $general_taxpayer,
            'bank_account_name' => $bank_account_name,
            'bank_account_number' => $bank_account_number,
            'bank_name' => $bank_name,
            'bank_code' => $bank_code,
            'bank_address' => $bank_address,
            'bank_licence_electronic' => $bank_licence_electronic,
            'is_settlement_account' => $is_settlement_account,
            'settlement_bank_account_name' => $settlement_bank_account_name,
            'settlement_bank_account_number' => $settlement_bank_account_number,
            'settlement_bank_name' => $settlement_bank_name,
            'settlement_bank_code' => $settlement_bank_code,
            'settlement_bank_address' => $settlement_bank_address,
            'tax_registration_certificate' => $tax_registration_certificate,
            'taxpayer_id' => $taxpayer_id,
            'tax_registration_certificate_electronic' => $tax_registration_certificate_electronic
        );
        $shop_apply = new NsShopApplyModel();
        $res = $shop_apply->save($data, [
            'apply_id' => $apply_id
        ]);
        return $res;
    }

    /**
     * 用户店铺消费(non-PHPdoc)
     *
     * @see \data\api\IOrder::getShopUserConsume()
     */
    public function getShopUserConsume($shop_id, $uid)
    {
        $member_account = new NsMemberAccountModel();
        $money = $member_account->getInfo([
            "shop_id" => $shop_id,
            'uid'     => $uid
        ]);
        if(!empty($money))
        {
            return 0;
        }else
        return $money['member_cunsum'];
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::updateShopShareCinfig()
     */
    public function updateShopShareCinfig($shop_id, $goods_param_1, $goods_param_2, $shop_param_1, $shop_param_2, $shop_param_3, $qrcode_param_1, $qrcode_param_2)
    {
        $shop_share = new NsShopWeixinShareModel();
        $data = array(
            'goods_param_1' => $goods_param_1,
            'goods_param_2' => $goods_param_2,
            'shop_param_1' => $shop_param_1,
            'shop_param_2' => $shop_param_2,
            'shop_param_3' => $shop_param_3,
            'qrcode_param_1' => $qrcode_param_1,
            'qrcode_param_2' => $qrcode_param_2
        );
        $retval = $shop_share->save($data, [
            'shop_id' => $shop_id
        ]);
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::getShopShareConfig()
     */
    public function getShopShareConfig($shop_id)
    {
        $shop_share = new NsShopWeixinShareModel();
        $count = $shop_share->getCount([
            'shop_id' => $shop_id
        ]);
        if ($count > 0) {
            $info = $shop_share->get($shop_id);
        } else {
            $data = array(
                'shop_id' => $shop_id,
                'goods_param_1' => '优惠价：',
                'goods_param_2' => '全场正品',
                'shop_param_1' => '欢迎打开',
                'shop_param_2' => '分享赚佣金',
                'shop_param_3' => '',
                'qrcode_param_1' => '向您推荐',
                'qrcode_param_2' => '注册有优惠'
            );
            $shop_share->save($data);
            $info = $shop_share->get($shop_id);
        }
        return $info;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::getShopAccountList()
     */
    public function getShopAccountList($page_index, $page_size = 0, $condition = '', $order = '')
    {
        // TODO Auto-generated method stub
        $order_service = new OrderService();
        $order_list = $order_service->getOrderList($page_index, $page_size, $condition, $order);
        foreach ($order_list["data"] as $k => $v) {
            foreach ($v["order_item_list"] as $l => $b) {
                $order_item = $v["order_item_list"][$l];
                $shop_all = array();
                $shop_order_account_records = new NsShopOrderReturnModel();
                $shop_account_condition = array(
                    "order_id" => $b["order_id"],
                    "order_goods_id" => $b["order_goods_id"]
                );
                $shop_all = $shop_order_account_records->all($shop_account_condition);
                
                // var_dump($commission_distribution_list);
                $order_item["shop_account_list"] = $shop_all;
            }
        }
        return $order_list;
        
        // $shop_order_account_records = new NsShopOrderAccountRecordsModel();
        // $list = $shop_order_account_records->pageQuery($page_index, $page_size, $condition, $order, '*');
        // foreach($list as $k=>$v){
        // $shop_all = array();
        // $shop_account_records = new NsShopAccountRecordsModel();
        // $shop_all = $shop_account_records->all(array("type_alis_id"=>$v["id"]));
        // $list["shop_account_records"] = $shop_all;
        // }
        // return $list;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::getShopCommissionWithdrawList()
     */
    public function getShopAccountWithdrawList($page_index, $page_size = 0, $condition = '', $order = '')
    {
        // TODO Auto-generated method stub
        $shop_account_withdraw = new NsShopWithdrawModel();
        $list = $shop_account_withdraw->pageQuery($page_index, $page_size, $condition, $order, '*');
        foreach ($list["data"] as $k => $v) {
            // var_dump($v["shop_id"]);
            $shop = new NsShopModel();
            $shop_info = $shop->getInfo([
                "shop_id" => $v["shop_id"]
            ], "shop_name,shop_logo");
            $shop_logo = $shop_info["shop_logo"];
            $shop_name = $shop_info["shop_name"];
            $list["data"][$k]["shop_logo"] = $shop_logo;
            $list["data"][$k]["shop_name"] = $shop_name;
        }
        return $list;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::getShopBankAccountList()
     */
    public function getShopBankAccountAll($condition)
    {
        // TODO Auto-generated method stub
        $shop_bank_account = new NsShopBankAccountModel();
        $all = $shop_bank_account->getQuery($condition, "*", "");
        return $all;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::addShopBankAccount()
     */
    public function addShopBankAccount($shop_id, $bank_type, $branch_bank_name, $realname, $account_number, $mobile)
    {
        // TODO Auto-generated method stub
        $shop_bank_account = new NsShopBankAccountModel();
        $data = array(
            "shop_id" => $shop_id,
            "bank_type" => $bank_type,
            "branch_bank_name" => $branch_bank_name,
            "realname" => $realname,
            "account_number" => $account_number,
            "mobile" => $mobile,
            "create_date" => time()
        );
        $shop_bank_account->save($data);
        return $shop_bank_account->id;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::updateShopBankAccount()
     */
    public function updateShopBankAccount($shop_id, $bank_type, $branch_bank_name, $realname, $account_number, $mobile, $id)
    {
        // TODO Auto-generated method stub
        $shop_bank_account = new NsShopBankAccountModel();
        $data = array(
            "bank_type" => $bank_type,
            "branch_bank_name" => $branch_bank_name,
            "realname" => $realname,
            "account_number" => $account_number,
            "mobile" => $mobile,
            "modify_date" => time()
        );
        $retval = $shop_bank_account->where(array(
            "shop_id" => $shop_id,
            "id" => $id
        ))->update($data);
        return $retval;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::modifyShopBankAccountIsdefault()
     */
    public function modifyShopBankAccountIsdefault($shop_id, $id)
    {
        // TODO Auto-generated method stub
        $shop_bank_account = new NsShopBankAccountModel();
        $retval = $shop_bank_account->where(array(
            "shop_id" => $shop_id
        ))->update(array(
            "is_default" => 0
        ));
        $retval = $shop_bank_account->where(array(
            "shop_id" => $shop_id,
            "id" => $id
        ))->update(array(
            "is_default" => 1
        ));
        return $retval;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::deleteShopBankAccouht()
     */
    public function deleteShopBankAccouht($condition)
    {
        // TODO Auto-generated method stub
        $shop_bank_account = new NsShopBankAccountModel();
        $retval = $shop_bank_account->destroy($condition);
        return $retval;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::getShopAccount()
     */
    public function getShopAccount($shop_id)
    {
        // TODO Auto-generated method stub
        $shop_account = new ShopAccount();
        $account_obj = $shop_account->getShopAccount($shop_id);
        return $account_obj;
    }

    /*
     * 店铺申请提现
     * (non-PHPdoc)
     * @see \data\api\IShop::applyShopCommissionWithdraw()
     */
    public function applyShopAccountWithdraw($shop_id, $bank_account_id, $cash)
    {
        // 查询店铺的账户情况
        $shop_account_model = new NsShopAccountModel();
        $shop_account_obj = $this->getShopAccount($shop_id);
        // 判断店铺金额是否够
        if ($shop_account_obj["shop_balance"] >= $cash) {
            $withdraw_no = $this->getWithdrawNo();
            $shop_bank_account = new NsShopBankAccountModel();
            $shop_bank_account_info = $shop_bank_account->getInfo(array(
                "shop_id" => $shop_id,
                "id" => $bank_account_id
            ), "*");
            $shop_account_withdraw = new NsShopWithdrawModel();
            $data = array(
                "shop_id" => $shop_id,
                "withdraw_no" => $withdraw_no,
                "bank_name" => $shop_bank_account_info["branch_bank_name"],
                "account_number" => $shop_bank_account_info["account_number"],
                "realname" => $shop_bank_account_info["realname"],
                "mobile" => $shop_bank_account_info["mobile"],
                "cash" => $cash,
                "ask_for_date" => time()
            );
            $res = $shop_account_withdraw->save($data);
            if ($shop_account_withdraw->id > 0) {
                $shop_account_service = new ShopAccount();
                $retval = $shop_account_service->addShopAccountWithdrawRecords(getSerialNo(), $shop_id, $cash, 1, $shop_account_withdraw->id, "店铺提现，扣除" . $shop_bank_account_info["realname"] . "余额" . $cash);
            }
            return $shop_account_withdraw->id;
        } else {
            // 店铺账户可提现资金不足
            return - 1;
        }
    }

    /*
     * 店铺提现审核
     * (non-PHPdoc)
     * @see \data\api\IShop::shopAccountWithdrawAudit()
     */
    public function shopAccountWithdrawAudit($shop_id, $id, $status)
    {
        // TODO Auto-generated method stub
        $shop_account_withdraw = new NsShopWithdrawModel();
        $shop_account_service = new ShopAccountService();
        $shop_account_withdraw->startTrans();
        try {
            // 更新提现数据的状态
            $data = array(
                "status" => $status,
                "modify_date" => time()
            );
            $retval = $shop_account_withdraw->save($data, array(
                "id" => $id,
                "shop_id" => $shop_id
            ));
            // 得到当前提现的具体信息
            $shop_account_withdraw = new NsShopWithdrawModel();
            $shop_account_withdraw_info = $shop_account_withdraw->get($id);
            if ($status == - 1) {
                // 平台拒绝提现，给店铺打回一笔金额
                $retval = $shop_account_service->addShopAccountWithdrawRecords(getSerialNo(), $shop_id, - $shop_account_withdraw_info["cash"], 2, $id, "店铺申请提现, 平台拒绝提现。");
            } else {
                // 平台审核提现通过，更新平台的账户情况
                $shop_account_service->addAccountWithdrawRecords($shop_id, $shop_account_withdraw_info["cash"], 1, $id, "店铺申请提现，平台审核通过。");
            }
            $shop_account_withdraw->commit();
            return $retval;
        } catch (\Exception $e) {
            $shop_account_withdraw->rollback();
            return $e->getCode();
        }
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::getKeyReplyDetail($id)
     */
    public function getShopBankAccountDetail($shop_id, $id)
    {
        $shop_bank_account = new NsShopBankAccountModel();
        $info = $shop_bank_account->get($id);
        
        return $info;
    }

    public function getShopAccountMonthRecord($shop_id)
    {
        $begin = getTimeTurnTimeStamp(date('Y-m-01', strtotime(date("Y-m-d"))));
        $end = getTimeTurnTimeStamp(date('Y-m-d', strtotime("$begin +1 month -1 day")));
        $shop_account_records = new NsShopOrderReturnModel();
        $condition["create_time"] = [
            [
                ">",
                $begin
            ],
            [
                "<",
                $end
            ]
        ];
        $condition["shop_id"] = $shop_id;
        $shop_account_records_list = $shop_account_records->all($condition);
        $begintime = strtotime($begin);
        $endtime = strtotime($end);
        $list = array();
        for ($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
            $list[date("d", $start)] = array();
            $use_money = 0;
            $no_use_money = 0;
            foreach ($shop_account_records_list as $v) {
                if (strtotime(date("Y-m-d", $v["create_time"])) == strtotime(date("Y-m-d", $start))) {
                    if ($v["is_issue"] == 0) {
                        $use_money = $use_money + $v["shop_money"];
                    } else {
                        $no_use_money = $no_use_money + $v["shop_money"];
                    }
                }
            }
            $list[date("d", $start)]["no_use"] = $use_money;
            $list[date("d", $start)]["use"] = $no_use_money;
        }
        return $list;
    }

    /**
     * 生成佣金流水号
     */
    private function getWithdrawNo()
    {
        $no_base = date("ymdhis", time());
        $withdraw_no = $no_base . rand(111, 999);
        return $withdraw_no;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::getShopAccountCountList()
     */
    public function getShopAccountCountList($page_index, $page_size = 0, $condition = '', $order = '')
    {
        // TODO Auto-generated method stub
        $shop_account = new NsShopAccountModel();
        $list = $shop_account->pageQuery($page_index, $page_size, $condition, $order, '*');
        foreach ($list["data"] as $k => $v) {
            // var_dump($v["shop_id"]);
            $shop = new NsShopModel();
            $shop_info = $shop->getInfo([
                "shop_id" => $v["shop_id"]
            ], "shop_name,shop_logo");
            $shop_logo = $shop_info["shop_logo"];
            $shop_name = $shop_info["shop_name"];
            $list["data"][$k]["shop_logo"] = $shop_logo;
            $list["data"][$k]["shop_name"] = $shop_name;
        }
        return $list;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::getShopAccountRecordsList()
     */
    public function getShopAccountRecordsList($page_index, $page_size = 0, $condition = '', $order = '')
    {
        // TODO Auto-generated method stub
        $shop_account_records = new NsShopAccountRecordsModel();
        $list = $shop_account_records->pageQuery($page_index, $page_size, $condition, $order, '*');
        foreach ($list["data"] as $k => $v) {
            // var_dump($v["shop_id"]);
            $shop = new NsShopModel();
            $shop_info = $shop->getInfo([
                "shop_id" => $v["shop_id"]
            ], "shop_name,shop_logo");
            $shop_logo = $shop_info["shop_logo"];
            $shop_name = $shop_info["shop_name"];
            $list["data"][$k]["shop_logo"] = $shop_logo;
            $list["data"][$k]["shop_name"] = $shop_name;
        }
        return $list;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::getShopOrderAccountRecordsList()
     */
    public function getShopOrderAccountRecordsList($page_index, $page_size = 0, $condition = '', $order = '')
    {
        $order_goods = new NsOrderGoodsViewModel();
        $return = $order_goods->getOrderGoodsViewList($page_index, $page_size, $condition, $order);
        return $return;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::getShopAll()
     */
    public function getShopAll($condition)
    {
        // TODO Auto-generated method stub
        $shop = new NsShopModel();
        $shop_all = $shop->where($condition)
            ->order(" shop_sales desc ")
            ->limit(10)
            ->select();
        return $shop_all;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::getShopAccountRecordCount()
     */
    public function getShopAccountRecordCount($start_date, $end_date, $shop_id)
    {
        // TODO Auto-generated method stub
        // 可提现余额
        $shop_account_record = new NsShopAccountRecordsModel();
        $shop_account_withdraw = new NsShopWithdrawModel();
        $withdraw_condition["shop_id"] = $shop_id;
        $money_condition["shop_id"] = $shop_id;
        if ($start_date != "") {
            $withdraw_condition["ask_for_date"][] = [
                ">",
                getTimeTurnTimeStamp($start_date)
            ];
            $money_condition["create_time"][] = [
                ">",
                getTimeTurnTimeStamp($start_date)
            ];
        }
        if ($end_date != "") {
            $withdraw_condition["ask_for_date"][] = [
                "<",
                getTimeTurnTimeStamp($end_date)
            ];
            $money_condition["create_time"][] = [
                "<",
                getTimeTurnTimeStamp($end_date)
            ];
        }
        // 已提现
        $withdraw_condition["status"] = 1;
        $withdraw_cash = $shop_account_withdraw->where($withdraw_condition)->sum("cash");
        // 提现审核中
        $withdraw_condition["status"] = 0;
        $withdraw_isaudit = $shop_account_withdraw->where($withdraw_condition)->sum("cash");
        $shop_order_account_record = new NsShopOrderReturnModel();
        // 店铺营业额
        $shop_order_money = $shop_order_account_record->where($money_condition)->sum("order_pay_money");
        $array = array(
            "withdraw_cash" => $withdraw_cash,
            "withdraw_isaudit" => $withdraw_isaudit,
            "shop_order_money" => $shop_order_money
        );
        return $array;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::getShopAccountSales()
     */
    public function getShopAccountSales($condition)
    {
        // TODO Auto-generated method stub
        $shop_order_account_records = new NsShopOrderReturnModel();
        // 店铺销售额
        $shop_sales = $shop_order_account_records->where($condition)->sum("order_pay_money");
        
        // 平台金额
        $platform_money = $shop_order_account_records->where($condition)->sum("platform_money");
        
        // 店铺金额
        $shop_money = $shop_sales - $platform_money;
        return [
            "shop_sale" => $shop_sales,
            "platform_money" => $platform_money,
            "shop_money" => $shop_money
        ];
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::getShopGoodsSales()
     */
    public function getShopGoodsSales($condition)
    {
        // TODO Auto-generated method stub
        $shop_order_account_records_view = new NsShopOrderGoodsAccountViewModel();
        $sales_num = $shop_order_account_records_view->getShopsGoodsSalesNum($condition);
        $platform_money = $shop_order_account_records_view->getShopGoodsPlatformMoney($condition);
        $platform_refund_money = $shop_order_account_records_view->getShopGoodsPlatformRefundMoney($condition);
        $platform_money = $platform_money - $platform_refund_money;
        return [
            "goods_sale_num" => $sales_num,
            "platform_money" => $platform_money
        ];
    }

    public function updateShopPlatformCommissionRate($shop_id, $shop_platform_commission_rate)
    {
        $shop_account = new NsShopAccountModel();
        $res = $shop_account->save([
            "shop_platform_commission_rate" => $shop_platform_commission_rate
        ], [
            'shop_id' => $shop_id
        ]);
        return $res;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::getShopCount()
     */
    public function getShopCount($condition)
    {
        // TODO Auto-generated method stub
        $shop = new NsShopModel();
        $shop_list = $shop->getQuery($condition, "count(shop_id) as count", "");
        return $shop_list[0]["count"];
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::getShopWithdrawCount()
     */
    public function getShopWithdrawCount($condition)
    {
        // TODO Auto-generated method stub
        $shop_account_withdraw = new NsShopWithdrawModel();
        $withdraw_isaudit = $shop_account_withdraw->getQuery($condition, "sum(cash) as sum", '');
        return $withdraw_isaudit[0]["sum"];
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::addShopBankAccount()
     */
    public function addMemberWithdrawSetting($shop_id, $withdraw_cash_min, $withdraw_multiple, $withdraw_poundage, $withdraw_message, $withdraw_account_type)
    {
        // TODO Auto-generated method stub
        $member_withdraw_setting = new NsMemberWithdrawSettingModel();
        $data = array(
            "shop_id" => $shop_id,
            "withdraw_cash_min" => $withdraw_cash_min,
            "withdraw_multiple" => $withdraw_multiple,
            "withdraw_poundage" => $withdraw_poundage,
            "withdraw_message" => $withdraw_message,
            "withdraw_account_type" => $withdraw_account_type,
            "create_time" => time()
        );
        $member_withdraw_setting->save($data);
        return $member_withdraw_setting->id;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IShop::updateShopBankAccount()
     */
    public function updateMemberWithdrawSetting($shop_id, $withdraw_cash_min, $withdraw_multiple, $withdraw_poundage, $withdraw_message, $withdraw_account_type, $id)
    {
        // TODO Auto-generated method stub
        $member_withdraw_setting = new NsMemberWithdrawSettingModel();
        $data = array(
            "withdraw_cash_min" => $withdraw_cash_min,
            "withdraw_multiple" => $withdraw_multiple,
            "withdraw_poundage" => $withdraw_poundage,
            "withdraw_message" => $withdraw_message,
            "withdraw_account_type" => $withdraw_account_type,
            "modify_time" => time()
        );
        $retval = $member_withdraw_setting->where(array(
            "shop_id" => $shop_id,
            "id" => $id
        ))->update($data);
        return $retval;
    }

    /**
     * 获取提现设置信息
     *
     * @param string $field            
     */
    public function getWithdrawInfo($shop_id)
    {
        $member_withdraw_setting = new NsMemberWithdrawSettingModel();
        $info = $member_withdraw_setting->getInfo([
            "shop_id" => $shop_id
        ]);
        
        return $info;
    }

    /**
     * 得到导航的商城模块
     * (non-PHPdoc)
     * 
     * @see \data\api\IShop::getShopNavigationTemplate()
     */
    public function getShopNavigationTemplate($use_type)
    {
        $template_model = new NsShopNavigationTemplateModel();
        $template_list = $template_model->getQuery([
            "is_use" => 1,
            "use_type" => $use_type
        ], "*", "");
        return $template_list;
    }

    /**
     * 自提点添加
     * 
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
    public function addPickupPoint($shop_id, $name, $address, $contact, $phone, $province_id, $city_id, $district_id, $longitude, $latitude)
    {
        $pickup_point_model = new NsPickupPointModel();
        $data = array(
            "shop_id" => $shop_id,
            "name" => $name,
            "address" => $address,
            "contact" => $contact,
            "phone" => $phone,
            "province_id" => $province_id,
            'city_id' => $city_id,
            'district_id' => $district_id,
            "create_time" => time()
        );
        $pickup_point_model->save($data);
        return $pickup_point_model->id;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \data\api\IShop::updatePickupPoint()
     */
    public function updatePickupPoint($id, $shop_id, $name, $address, $contact, $phone, $province_id, $city_id, $district_id, $longitude, $latitude)
    {
        $pickup_point_model = new NsPickupPointModel();
        $data = array(
            "shop_id" => $shop_id,
            "name" => $name,
            "address" => $address,
            "contact" => $contact,
            "phone" => $phone,
            "province_id" => $province_id,
            'city_id' => $city_id,
            'district_id' => $district_id
        );
        $retval = $pickup_point_model->save($data, [
            'id' => $id
        ]);
        return $retval;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \data\api\IShop::getPickupPointList()
     */
    public function getPickupPointList($page_index = 1, $page_size = 0, $where = '', $order = '')
    {
        $pickup_point_model = new NsPickupPointModel();
        $list = $pickup_point_model->pageQuery($page_index, $page_size, $where, $order, '*');
        if (! empty($list)) {
            $address = new Address();
            foreach ($list['data'] as $k => $v) {
                $list['data'][$k]['province_name'] = $address->getProvinceName($v['province_id']);
                $list['data'][$k]['city_name'] = $address->getCityName($v['city_id']);
                $list['data'][$k]['dictrict_name'] = $address->getDistrictName($v['district_id']);
            }
        }
        return $list;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \data\api\IShop::deletePickupPoint()
     */
    public function deletePickupPoint($pickip_id)
    {
        $pickup_point_model = new NsPickupPointModel();
        $retval = $pickup_point_model->destroy($pickip_id);
        return $retval;
    }
    public function getPickupPointDetail($pickip_id){
        $pickup_point_model = new NsPickupPointModel();
        $pickup_point_detail = $pickup_point_model->get($pickip_id);
        return $pickup_point_detail;
    }
    
}
