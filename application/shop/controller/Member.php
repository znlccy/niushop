<?php
/**
 * Member.php
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
namespace app\shop\controller;

use data\model\AlbumPictureModel;
use data\model\NsCartModel;
use data\model\NsGoodsModel;
use data\model\NsGoodsSkuModel;
use data\model\NsMemberBankAccountModel;
use data\service\Address;
use data\service\Config;
use data\service\Express;
use data\service\Goods as Goods;
use data\service\Member\MemberAccount as MemberAccount;
use data\service\Member as MemberService;
use data\service\Order\Order;
use data\service\Order\OrderGoods;
use data\service\Order as OrderService;
use data\service\promotion\GoodsExpress as GoodsExpressService;
use data\service\promotion\GoodsMansong;
use data\service\Promotion;
use data\service\promotion\GoodsPreference;
use data\service\Shop;
use data\service\UnifyPay;
use data\service\Upload\QiNiu;
use think\Session;

/**
 * 会员控制器
 * 创建人：李吉
 * 创建时间：2017-02-06 10:59:23
 */
class Member extends BaseController
{

    public $notice;

    public function __construct()
    {
        parent::__construct();
        // 如果没有登录的话让其先登录
        $this->checkLogin();
        // 查询登陆用户信息
        if (! request()->isAjax()) {
            $member = new MemberService();
            $member_info = $member->getMemberDetail($this->instance_id);
            if (! empty($member_info['user_info']['user_headimg'])) {
                $member_img = $member_info['user_info']['user_headimg'];
            } else {
                $member_img = '0';
            }
            $cart_list = $this->getShoppingCart(); // 购物车列表
                                                   // 选中id
            $curs = request()->get('curs', '1');
            $this->assign('curs', $curs);
            
            $this->assign('member_img', $member_img);
            $this->assign('member_info', $member_info);
            $this->assign("cart_list", $cart_list);
        }
        // 是否开启验证码
        $web_config = new Config();
        $this->login_verify_code = $web_config->getLoginVerifyCodeConfig($this->instance_id);
        $this->assign("login_verify_code", $this->login_verify_code["value"]);
        // 是否开启通知
        $instance_id = 0;
        $noticeMobile = $web_config->getNoticeMobileConfig($instance_id);
        $noticeEmail = $web_config->getNoticeEmailConfig($instance_id);
        $this->notice['noticeEmail'] = $noticeEmail[0]['is_use'];
        $this->notice['noticeMobile'] = $noticeMobile[0]['is_use'];
        $this->assign("notice", $this->notice);
        $is_open_virtual_goods = $this->getIsOpenVirtualGoodsConfig($this->instance_id);
        $this->assign("is_open_virtual_goods",$is_open_virtual_goods);
    }

    public function _empty($name)
    {}

    /**
     * 检测用户
     */
    private function checkLogin()
    {
        $uid = $this->user->getSessionUid();
        if (empty($uid)) {
            
            $_SESSION['login_pre_url'] = __URL(__URL__ . $_SERVER['PATH_INFO']);
            $redirect = __URL(__URL__ . "/login");
            $this->redirect($redirect);
        }
        $is_member = $this->user->getSessionUserIsMember();
        if (empty($is_member)) {
            $redirect = __URL(__URL__ . "/login");
            $this->redirect($redirect);
        }
    }

    /**
     * 收货地址列表
     * 创建人：任鹏强
     * 创建时间：2017年2月7日12:26:53
     */
    public function addressList()
    {
        $member = new MemberService();
        $page_index = request()->get('page', '1');
        $addresslist = $member->getMemberExpressAddressList(1, 5, '', '');
        $member_detail = $this->user->getMemberDetail($this->instance_id);
        $this->assign("member_detail", $member_detail);
        
        $this->assign('page_count', $addresslist['page_count']);
        $this->assign('total_count', $addresslist['total_count']);
        $this->assign('page', $page_index);
        $this->assign('list', $addresslist);
        return view($this->style . "Member/addressList");
    }

    /**
     * 会员地址管理
     * 添加地址
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function addressInsert()
    {
        if (request()->isAjax()) {
            $member = new MemberService();
            $consigner = request()->post('consigner', '');
            $mobile = request()->post('mobile', '');
            $phone = request()->post('phone', '');
            $province = request()->post('province', '');
            $city = request()->post('city', '');
            $district = request()->post('district', '');
            $address = request()->post('address', '');
            $zip_code = request()->post('zip_code', '');
            $alias = request()->post('alias', '');
            $retval = $member->addMemberExpressAddress($consigner, $mobile, $phone, $province, $city, $district, $address, $zip_code, $alias);
            return AjaxReturn($retval);
        } else {
            $member_detail = $this->user->getMemberDetail($this->instance_id);
            $this->assign("member_detail", $member_detail);
            $address_id = request()->get('addressid', 0);
            $this->assign("address_id", $address_id);
            
            return view($this->style . "Member/addressInsert");
        }
    }

    /**
     * 编辑收货地址：
     */
    public function operationAddress()
    {
        $id = request()->post('id', '');
        $consigner = request()->post('consigner', ''); // 收件人
        $mobile = request()->post('mobile', ''); // 电话
        $phone = request()->post('phone', ''); // 固定电话
        $province = request()->post('province', ''); // 省
        $city = request()->post('city', ''); // 市
        $district = request()->post('district', ''); // 区县
        $address = request()->post('address', ''); // 详细地址
        $zip_code = request()->post('zipcode', ''); // 邮编
        $alias = ""; // 城市别名
        $member = new MemberService();
        $res = null;
        if ($id == 0) {
            // 添加
            $res = $member->addMemberExpressAddress($consigner, $mobile, $phone, $province, $city, $district, $address, $zip_code, $alias);
        } else {
            // 修改
            $res = $member->updateMemberExpressAddress($id, $consigner, $mobile, $phone, $province, $city, $district, $address, $zip_code, $alias);
        }
        return AjaxReturn($res);
    }

    /**
     * 获取地址
     */
    public function getMemberExpressAddress()
    {
        $id = request()->post('id', '');
        $member = new MemberService();
        $info = $member->getMemberExpressAddressDetail($id);
        return $info;
    }

    /**
     * 修改会员地址
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >|Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function updateMemberAddress()
    {
        $member = new MemberService();
        if (request()->isAjax()) {
            $id = request()->post('id', '');
            $consigner = request()->post('consigner', '');
            $mobile = request()->post('mobile', '');
            $phone = request()->post('phone', '');
            $province = request()->post('province', '');
            $city = request()->post('city', '');
            $district = request()->post('district', '');
            $address = request()->post('address', '');
            $zip_code = request()->post('zip_code', '');
            $alias = request()->post('alias', '');
            $retval = $member->updateMemberExpressAddress($id, $consigner, $mobile, $phone, $province, $city, $district, $address, $zip_code, $alias);
            return AjaxReturn($retval);
        } else {
            $id = request()->get('id', '');
            $info = $member->getMemberExpressAddressDetail($id);
            if (empty($info)) {
                $this->error("当前地址不存在或者当前会员无权查看");
            }
            $member_detail = $this->user->getMemberDetail($this->instance_id);
            $this->assign("member_detail", $member_detail);
            $this->assign("address_info", $info);
            return view($this->style . "Member/updateMemberAddress");
        }
    }

    /**
     * 获取用户地址详情
     *
     * @return Ambigous <\think\static, multitype:, \think\db\false, PDOStatement, string, \think\Model, \PDOStatement, \think\db\mixed, multitype:a r y s t i n g Q u e \ C l o , \think\db\Query, NULL>
     */
    public function getMemberAddressDetail()
    {
        $address_id = request()->post('id', 0);
        $member = new MemberService();
        $info = $member->getMemberExpressAddressDetail($address_id);
        return $info;
    }

    /**
     * 会员地址删除
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function memberAddressDelete()
    {
        $id = request()->post('id', '');
        $member = new MemberService();
        $res = $member->memberAddressDelete($id);
        return AjaxReturn($res);
    }

    /**
     * 修改会员默认地址
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function updateAddressDefault()
    {
        $id = request()->post('id', '');
        $member = new MemberService();
        $res = $member->updateAddressDefault($id);
        return AjaxReturn($res);
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

    /**
     * 我的订单
     * 创建人：任鹏强
     * 创建时间：2017年2月7日12:26:55
     */
    public function orderList($page = 1, $page_size = 10)
    {
        $status = request()->get('status', 'all');
        $condition['order_type'] = 1; // 订单类型
        $condition['buyer_id'] = $this->uid;
        $condition["is_deleted"] = 0; // 未删除的订单
        $orderService = new OrderService();
        // 查询个人用户的订单数量
        $orderStatusNum = $orderService->getOrderStatusNum($condition);
        $this->assign("statusNum", $orderStatusNum);
        // 查询订单状态的数量
        if ($status != 'all') {
            switch ($status) {
                case 0:
                    $condition['order_status'] = 0;
                    break;
                case 1:
                    $condition['order_status'] = 1;
                    // 订单状态为待发货实际为已经支付未完成还未发货的订单
                    $condition['shipping_status'] = 0; // 0 待发货
                    $condition['pay_status'] = 2; // 2 已支付
                    $condition['order_status'] = array(
                        'neq',
                        4
                    ); // 4 已完成
                    $condition['order_status'] = array(
                        'neq',
                        5
                    ); // 5 关闭订单
                    break;
                case 2:
                    $condition['order_status'] = 2;
                    break;
                case 3:
                    $condition['order_status'] = 3;
                    break;
                case 4:
                    $condition['order_status'] = array(
                        'in',
                        '-1,-2'
                    );
                    break;
                case 5:
                    $condition['order_status'] = array(
                        'in',
                        '3,4'
                    );
                    $condition['is_evaluate'] = 0;
                    break;
                default:
                    break;
            }
            if ($condition['order_status'] == array(
                'in',
                '-1,-2'
            )) {
                $orderList = $orderService->getOrderList($page, $page_size, $condition, 'create_time desc');
                foreach ($orderList['data'] as $key => $item) {
                    $order_item_list = array();
                    $order_item_list = $orderList['data'][$key]['order_item_list'];
                    foreach ($order_item_list as $k => $value) {
                        if ($value['refund_status'] == 0 || $value['refund_status'] == - 2) {
                            unset($order_item_list[$k]);
                        }
                    }
                    $orderList['data'][$key]['order_item_list'] = $order_item_list;
                }
            } else {
                $orderList = $orderService->getOrderList($page, $page_size, $condition, 'create_time desc');
            }
        } else {
            
            $orderList = $orderService->getOrderList($page, $page_size, $condition, 'create_time desc');
        }
        $this->assign("orderList", $orderList['data']);
        $this->assign("page_count", $orderList['page_count']);
        $this->assign("total_count", $orderList['total_count']);
        $this->assign("page", $page);
        $this->assign("status", $status);
        
        $member_detail = $this->user->getMemberDetail($this->instance_id);
        $this->assign("member_detail", $member_detail);
        return view($this->style . 'Member/orderList');
    }

    /**
     * 我的虚拟订单
     * 创建人：王永杰
     * 创建时间：2017年11月23日 19:59:21 王永杰
     */
    public function virtualOrderList($page = 1, $page_size = 10)
    {
        if ($this->getIsOpenVirtualGoodsConfig() == 0) {
            $this->error("未开启虚拟商品功能");
        }
        $status = request()->get('status', 'all');
        $condition['order_type'] = 2; // 订单类型（虚拟订单）
        $condition['buyer_id'] = $this->uid;
        $condition["is_deleted"] = 0; // 未删除的订单
        $orderService = new OrderService();
        // 查询个人用户的订单数量
        $orderStatusNum = $orderService->getOrderStatusNum($condition);
        $this->assign("statusNum", $orderStatusNum);
        // 查询订单状态的数量
        
        if ($status != 'all') {
            $condition['order_status'] = $status;
            $orderList = $orderService->getOrderList($page, $page_size, $condition, 'create_time desc');
        } else {
            
            $orderList = $orderService->getOrderList($page, $page_size, $condition, 'create_time desc');
        }
        $this->assign("orderList", $orderList['data']);
        $this->assign("page_count", $orderList['page_count']);
        $this->assign("total_count", $orderList['total_count']);
        $this->assign("page", $page);
        $this->assign("status", $status);
        
        $member_detail = $this->user->getMemberDetail($this->instance_id);
        $this->assign("member_detail", $member_detail);
        return view($this->style . 'Member/virtualOrderList');
    }

    /**
     * 我的收藏-->商品收藏
     * 创建人：任鹏强
     * 创建时间：2017年2月7日12:26:58
     */
    public function goodsCollectionList()
    {
        $member = new MemberService();
        $page = request()->get('page', '1');
        $data = array(
            "nmf.fav_type" => 'goods',
            "nmf.uid" => $this->uid
        );
        $goods_collection_list = $member->getMemberGoodsFavoritesList($page, 12, $data);
        $this->assign("goods_collection_list", $goods_collection_list["data"]);
        $this->assign('page', $page);
        $this->assign("goods_list", $goods_collection_list);
        $this->assign('page_count', $goods_collection_list['page_count']);
        $this->assign('total_count', $goods_collection_list['total_count']);
        
        $member_detail = $this->user->getMemberDetail($this->instance_id);
        $this->assign("member_detail", $member_detail);
        return view($this->style . 'Member/goodsCollectionList');
    }

    /**
     * 查询右侧边栏的店铺收藏
     * 创建人：王永杰
     * 创建时间：2017年2月27日 10:18:14
     *
     * @return unknown
     */
    public function queryShopOrGoodsCollections()
    {
        $member = new MemberService();
        $type = $_POST["type"];
        $data = array(
            "nmf.fav_type" => $type,
            "nmf.uid" => $this->uid
        );
        $list = null;
        if ($type == "shop") {
            $list = $member->getMemberShopsFavoritesList(1, 50, $data);
        } else {
            $list = $member->getMemberGoodsFavoritesList(1, 50, $data);
        }
        return $list["data"];
    }

    /**
     * 订单详情
     * 创建人：任鹏强
     * 创建时间:2017年2月7日14:49:01
     */
    public function orderDetail()
    {
        $order_id = request()->get('orderid', 0);
        if ($order_id == 0) {
            $this->error("没有获取到订单信息");
        }
        $order_service = new OrderService();
        $order_count = 0;
        $order_count = $order_service->getUserOrderDetailCount($this->uid, $order_id);
        if ($order_count == 0) {
            $this->error("没有获取到订单信息");
        }
        $detail = $order_service->getOrderDetail($order_id);
        if (empty($detail)) {
            $this->error("没有获取到订单信息");
        }
        $this->assign("order", $detail);
        
        $config = new Config();
        $shopSet = $config->getShopConfig($this->instance_id);
        $this->assign("order_buy_close_time", $shopSet['order_buy_close_time']);
        
        return view($this->style . 'Member/orderDetail');
    }

    /**
     * 虛擬订单详情
     * 创建人：王永杰
     * 创建时间:2017年11月24日 11:16:15
     */
    public function virtualOrderDetail()
    {
        if ($this->getIsOpenVirtualGoodsConfig() == 0) {
            $this->error("未开启虚拟商品功能");
        }
        $order_id = request()->get('orderid', 0);
        if ($order_id == 0) {
            $this->error("没有获取到订单信息");
        }
        $order_service = new OrderService();
        $order_count = 0;
        $order_count = $order_service->getUserOrderDetailCount($this->uid, $order_id);
        if ($order_count == 0) {
            $this->error("没有获取到订单信息");
        }
        $detail = $order_service->getOrderDetail($order_id);
        if (empty($detail)) {
            $this->error("没有获取到订单信息");
        }
        $this->assign("order", $detail);
        
        $config = new Config();
        $shopSet = $config->getShopConfig($this->instance_id);
        $this->assign("order_buy_close_time", $shopSet['order_buy_close_time']);
        
        return view($this->style . 'Member/virtualOrderDetail');
    }

    /**
     * 查询包裹物流信息
     * 2017年6月24日 10:42:34 王永杰
     */
    public function getOrderGoodsExpressMessage()
    {
        $express_id = request()->post("express_id", 0); // 物流包裹id
        $res = - 1;
        if ($express_id) {
            $order_service = new OrderService();
            $res = $order_service->getOrderGoodsExpressMessage($express_id);
        }
        return $res;
    }

    public function index()
    {
        // 可用积分和余额,显示的是用户在店铺中的积分和余额
        $point = 0;
        $balance = 0;
        $member_detail = $this->user->getMemberDetail($this->instance_id);
        if (! empty($member_detail)) {
            $point = $member_detail['point'];
            $balance = $member_detail['balance'];
        }
        if (isset($point)) {
            $this->assign(array(
                'point' => $point,
                'balance' => $balance
            ));
        } else {
            $this->assign(array(
                'point' => '0',
                'balance' => '0.00'
            ));
        }
        // 优惠券
        $vouchers = $this->user->getMemberCounponList(1);
        if ($vouchers != "") {
            $vouchersCount = count($vouchers);
        } else {
            $vouchersCount = 0;
        }
        $this->assign("vouchersCount", $vouchersCount);
        
        $member = new MemberService();
        // 商品收藏
        $data_goods = array(
            "nmf.fav_type" => "goods",
            "nmf.uid" => $this->uid
        );
        $goods_collection_list = $member->getMemberGoodsFavoritesList(1, 6, $data_goods);
        $this->assign("goods_collection_list", $goods_collection_list["data"]);
        $this->assign("goods_collection_list_count", count($goods_collection_list["data"]));
        
        // 交易提醒 商品列表 商品数量
        $orderService = new OrderService();
        $condition = null;
        $condition['buyer_id'] = $this->uid;
        $order_status_num = $orderService->getOrderStatusNum($condition);
        $condition = null;
        $condition['order_status'] = 0;
        $condition['buyer_id'] = $this->uid;
        $orderList = $orderService->getOrderList(1, 4, $condition, 'create_time desc');
        
        // 用户公告！
        $config = new Config();
        $user_notice = $config->getUserNotice($this->instance_id);
        $this->assign('user_notice', $user_notice);
        
        $this->assign("order_status_num", $order_status_num);
        
        $this->assign("orderList", $orderList['data']);
        $this->assign("member_detail", $member_detail);
        return view($this->style . 'Member/index');
    }

    /**
     * 取消订单
     * 创建人：任鹏强
     * 创建时间：2017年3月3日09:18:35
     */
    public function orderClose()
    {
        $orderService = new OrderService();
        $order_id = request()->post('order_id', '');
        $order = $orderService->orderClose($order_id);
        return AjaxReturn($order);
    }

    /**
     * 获取购物车信息
     * 创建人：王永杰
     * 创建时间：2017年2月15日 14:34:54
     *
     * {@inheritdoc}
     *
     * @see \app\shop\controller\BaseController::getShoppingCart()
     */
    public function getShoppingCart()
    {
        $goods = new Goods();
        $cart_list = $goods->getCart($this->uid);
        return $cart_list;
    }

    /**
     * 立即购买
     */
    public function buyNowSession()
    {
        $order_sku_list = isset($_SESSION["order_sku_list"]) ? $_SESSION["order_sku_list"] : "";
        if (empty($order_sku_list)) {
            $redirect = __URL(__URL__ . "/index");
            $this->redirect($redirect);
        }
        
        $cart_list = array();
        $order_sku_list = explode(":", $_SESSION["order_sku_list"]);
        $sku_id = $order_sku_list[0];
        $num = $order_sku_list[1];
        
        // 获取商品sku信息
        $goods_sku = new NsGoodsSkuModel();
        $sku_info = $goods_sku->getInfo([
            'sku_id' => $sku_id
        ], '*');
        
        // 查询当前商品是否有SKU主图
        $order_goods_service = new OrderGoods();
        $picture = $order_goods_service->getSkuPictureBySkuId($sku_info);
        
        // 清除非法错误数据
        $cart = new NsCartModel();
        if (empty($sku_info)) {
            $cart->destroy([
                'buyer_id' => $this->uid,
                'sku_id' => $sku_id
            ]);
            $redirect = __URL(__URL__ . "/index");
            $this->redirect($redirect);
        }
        
        $goods = new NsGoodsModel();
        $goods_info = $goods->getInfo([
            'goods_id' => $sku_info["goods_id"]
        ], 'max_buy,state,point_exchange_type,point_exchange,picture,goods_id,goods_name');
        
        $cart_list["stock"] = $sku_info['stock']; // 库存
        $cart_list["sku_name"] = $sku_info["sku_name"];
        
        $goods_preference = new GoodsPreference();
        $member_price = $goods_preference->getGoodsSkuMemberPrice($sku_info['sku_id'], $this->uid);
        $cart_list["price"] = $member_price < $sku_info['promote_price'] ? $member_price : $sku_info['promote_price'];
        $cart_list["goods_id"] = $goods_info["goods_id"];
        $cart_list["goods_name"] = $goods_info["goods_name"];
        $cart_list["max_buy"] = $goods_info['max_buy']; // 限购数量
        $cart_list['point_exchange_type'] = $goods_info['point_exchange_type']; // 积分兑换类型 0 非积分兑换 1 只能积分兑换
        $cart_list['point_exchange'] = $goods_info['point_exchange']; // 积分兑换
        if ($goods_info['state'] != 1) {
            $redirect = __URL(__URL__ . "/index");
            $this->redirect($redirect);
        }
        $cart_list["num"] = $num;
        // 如果购买的数量超过限购，则取限购数量
        if ($goods_info['max_buy'] != 0 && $goods_info['max_buy'] < $num) {
            $num = $goods_info['max_buy'];
        }
        // 如果购买的数量超过库存，则取库存数量
        if ($sku_info['stock'] < $num) {
            $num = $sku_info['stock'];
        }
        // 获取图片信息，如果该商品有SKU主图，就用。否则用商品主图
        $album_picture_model = new AlbumPictureModel();
        $picture_info = $album_picture_model->get($picture == 0 ? $goods_info['picture'] : $picture);
        $cart_list['picture_info'] = $picture_info;
        
        if (count($cart_list) == 0) {
            $redirect = __URL(__URL__ . "/index");
            $this->redirect($redirect);
        }
        $list[] = $cart_list;
        $goods_sku_list = $sku_id . ":" . $num; // 商品skuid集合
        $res["list"] = $list;
        $res["goods_sku_list"] = $goods_sku_list;
        return $res;
    }

    /**
     * 加入购物车
     *
     * @return unknown
     */
    public function addShoppingCartSession()
    {
        // 加入购物车
        $cart_list = isset($_SESSION["cart_list"]) ? $_SESSION["cart_list"] : ""; // 用户所选择的商品
        if (empty($cart_list)) {
            $redirect = __URL(__URL__ . "/index");
            $this->redirect($redirect);
        }
        
        $cart_id_arr = explode(",", $cart_list);
        $goods = new Goods();
        $cart_list = $goods->getCartList($cart_list);
        if (count($cart_list) == 0) {
            $redirect = __URL(__URL__ . "/index");
            $this->redirect($redirect);
        }
        $list = Array();
        $str_cart_id = ""; // 购物车id
        $goods_sku_list = ''; // 商品skuid集合
        for ($i = 0; $i < count($cart_list); $i ++) {
            if ($cart_id_arr[$i] == $cart_list[$i]["cart_id"]) {
                $list[] = $cart_list[$i];
                $str_cart_id .= "," . $cart_list[$i]["cart_id"];
                $goods_sku_list .= "," . $cart_list[$i]['sku_id'] . ':' . $cart_list[$i]['num'];
            }
        }
        $goods_sku_list = substr($goods_sku_list, 1); // 商品sku列表
        $res["list"] = $list;
        $res["goods_sku_list"] = $goods_sku_list;
        return $res;
    }

    /**
     * 购买流程：查看购物车，待付款订单 第一步
     * 创建人：王永杰
     * 创建时间：2017年2月10日 08:49:34
     *
     * @return \think\response\View
     */
    public function paymentOrder()
    {
        // 判断实物类型：实物商品，虚拟商品
        $order_tag = isset($_SESSION['order_tag']) ? $_SESSION['order_tag'] : "";
        if (empty($order_tag)) {
            $redirect = __URL(__URL__ . "/index");
            $this->redirect($redirect);
        }
        $this->assign("order_tag", $order_tag); // 标识：立即购买还是购物车中进来的
        $order_goods_type = isset($_SESSION['order_goods_type']) ? $_SESSION['order_goods_type'] : "";
        if ($order_tag == "buy_now" && $order_goods_type === "0") {
            // 虚拟商品
            $this->virtualOrderInfo();
            return view($this->style . 'Member/paymentVirtualOrder');
        } else {
            // 实物商品
            $this->orderInfo();
            return view($this->style . 'Member/paymentOrder');
        }
    }

    /**
     * 待付款订单需要的数据
     * 2017年6月28日 15:00:54 王永杰
     */
    public function orderInfo()
    {
        $member = new MemberService();
        $order = new OrderService();
        $goods_mansong = new GoodsMansong();
        $Config = new Config();
        $promotion = new Promotion();
        $shop_service = new Shop();
        $goods_express_service = new GoodsExpressService();
        $order_tag = $_SESSION['order_tag'];
        
        switch ($order_tag) {
            // 立即购买
            case "buy_now":
                $res = $this->buyNowSession();
                $goods_sku_list = $res["goods_sku_list"];
                $list = $res["list"];
                break;
            case "cart":
                // 加入购物车
                $res = $this->addShoppingCartSession();
                $goods_sku_list = $res["goods_sku_list"];
                $list = $res["list"];
                break;
        }
        $goods_sku_list = trim($goods_sku_list);
        if (empty($goods_sku_list)) {
            $this->error("待支付订单中商品不可为空");
        }
        $this->assign('goods_sku_list', $goods_sku_list); // 商品sku列表
        $addresslist = $member->getMemberExpressAddressList(1, 0, '', ' is_default DESC'); // 地址查询
        if (empty($addresslist["data"])) {
            $this->assign("address_list", 0);
        } else {
            $this->assign("address_list", $addresslist["data"]); // 选择收货地址
        }
        
        $address = $member->getDefaultExpressAddress(); // 查询默认收货地址
        $express = 0;
        $express_company_list = array();
        if (! empty($address)) {
            // 物流公司
            $express_company_list = $goods_express_service->getExpressCompany($this->instance_id, $goods_sku_list, $address['province'], $address['city'], $address['district']);
            if (! empty($express_company_list)) {
                foreach ($express_company_list as $v) {
                    $express = $v['express_fee']; // 取第一个运费，初始化加载运费
                    break;
                }
            }
            $this->assign("address_is_have", 1);
        } else {
            $this->assign("address_is_have", 0);
        }
        $count = $goods_express_service->getExpressCompanyCount($this->instance_id);
        $this->assign("express_company_count", $count); // 物流公司数量
        $this->assign("express", sprintf("%.2f", $express)); // 运费
        $this->assign("express_company_list", $express_company_list); // 物流公司
        
        $discount_money = $goods_mansong->getGoodsMansongMoney($goods_sku_list); // 计算优惠金额
        $this->assign("discount_money", sprintf("%.2f", $discount_money)); // 总优惠
        
        $count_money = $order->getGoodsSkuListPrice($goods_sku_list); // 商品金额
        $this->assign("count_money", sprintf("%.2f", $count_money)); // 商品金额
                                                                     // 计算自提点运费
        $pick_up_money = $order->getPickupMoney($count_money);
        if (empty($pick_up_money)) {
            $pick_up_money = 0;
        }
        $this->assign("pick_up_money", $pick_up_money);
        $count_point_exchange = 0;
        foreach ($list as $k => $v) {
            $list[$k]['price'] = sprintf("%.2f", $list[$k]['price']);
            $list[$k]['subtotal'] = sprintf("%.2f", $list[$k]['price'] * $list[$k]['num']);
            if ($v["point_exchange_type"] == 1) {
                if ($v["point_exchange"] > 0) {
                    $count_point_exchange += $v["point_exchange"] * $v["num"];
                }
            }
        }
        $this->assign("list", $list); // 格式化后的列表
        $this->assign("count_point_exchange", $count_point_exchange); // 总积分
        
        $shop_id = $this->instance_id;
        $shop_config = $Config->getShopConfig($shop_id);
        $order_invoice_content = explode(",", $shop_config['order_invoice_content']);
        $shop_config['order_invoice_content_list'] = array();
        foreach ($order_invoice_content as $v) {
            if (! empty($v)) {
                array_push($shop_config['order_invoice_content_list'], $v);
            }
        }
        
        $this->assign("shop_config", $shop_config); // 后台配置
        
        $member_account = $this->getMemberAccount($this->instance_id); // 用户余额
        $this->assign("member_account", $member_account); // 用户余额、积分
        
        $coupon_list = $order->getMemberCouponList($goods_sku_list); // 获取优惠券
        foreach ($coupon_list as $k => $v) {
            $coupon_list[$k]['start_time'] = substr($v['start_time'], 0, stripos($v['start_time'], " ") + 1);
            $coupon_list[$k]['end_time'] = substr($v['end_time'], 0, stripos($v['end_time'], " ") + 1);
        }
        $this->assign("coupon_list", $coupon_list); // 优惠卷
        
        $promotion_full_mail = $promotion->getPromotionFullMail($this->instance_id);
        if (! empty($address)) {
            $no_mail = checkIdIsinIdArr($address['city'], $promotion_full_mail['no_mail_city_id_array']);
            if ($no_mail) {
                $promotion_full_mail['is_open'] = 0;
            }
        }
        $this->assign("promotion_full_mail", $promotion_full_mail); // 满额包邮
        
        $pickup_point_list = $shop_service->getPickupPointList();
        $this->assign("pickup_point_list", $pickup_point_list); // 自提地址列表
    }

    /**
     * 待付款订单需要的数据 虚拟商品
     * 2017年11月22日 10:07:26 王永杰
     */
    public function virtualOrderInfo()
    {
        if ($this->getIsOpenVirtualGoodsConfig() == 0) {
            $this->error("未开启虚拟商品功能");
        }
        $member = new MemberService();
        $order = new OrderService();
        $goods_mansong = new GoodsMansong();
        $Config = new Config();
        $promotion = new Promotion();
        $shop_service = new Shop();
        $goods_express_service = new GoodsExpressService();
        $shop_id = $this->instance_id;
        $order_tag = $_SESSION['order_tag'];
        $res = $this->buyNowSession();
        $goods_sku_list = $res["goods_sku_list"];
        $list = $res["list"];
        $goods_sku_list = trim($goods_sku_list);
        if (empty($goods_sku_list)) {
            $this->error("待支付订单中商品不可为空");
        }
        $this->assign('goods_sku_list', $goods_sku_list); // 商品sku列表
        
        $discount_money = $goods_mansong->getGoodsMansongMoney($goods_sku_list); // 计算优惠金额
        $this->assign("discount_money", sprintf("%.2f", $discount_money)); // 总优惠
        
        $count_money = $order->getGoodsSkuListPrice($goods_sku_list); // 商品金额
        $this->assign("count_money", sprintf("%.2f", $count_money)); // 商品金额
        $count_point_exchange = 0;
        foreach ($list as $k => $v) {
            $list[$k]['price'] = sprintf("%.2f", $list[$k]['price']);
            $list[$k]['subtotal'] = sprintf("%.2f", $list[$k]['price'] * $list[$k]['num']);
            if ($v["point_exchange_type"] == 1) {
                if ($v["point_exchange"] > 0) {
                    $count_point_exchange += $v["point_exchange"] * $v["num"];
                }
            }
        }
        $this->assign("list", $list); // 格式化后的列表
        $this->assign("count_point_exchange", $count_point_exchange); // 总积分
        
        $shop_config = $Config->getShopConfig($shop_id);
        $order_invoice_content = explode(",", $shop_config['order_invoice_content']);
        $shop_config['order_invoice_content_list'] = array();
        foreach ($order_invoice_content as $v) {
            if (! empty($v)) {
                array_push($shop_config['order_invoice_content_list'], $v);
            }
        }
        
        $this->assign("shop_config", $shop_config); // 后台配置
        
        $member_account = $this->getMemberAccount($shop_id); // 用户余额
        $this->assign("member_account", $member_account); // 用户余额、积分
        
        $coupon_list = $order->getMemberCouponList($goods_sku_list); // 获取优惠券
        foreach ($coupon_list as $k => $v) {
            $coupon_list[$k]['start_time'] = substr($v['start_time'], 0, stripos($v['start_time'], " ") + 1);
            $coupon_list[$k]['end_time'] = substr($v['end_time'], 0, stripos($v['end_time'], " ") + 1);
        }
        $this->assign("coupon_list", $coupon_list); // 优惠卷
        
        $user_telephone = $this->user->getUserTelephone();
        $this->assign("user_telephone", $user_telephone);
    }

    /**
     * 立即购买、加入购物车都存入session中，
     *
     * @return number
     */
    public function orderCreateSession()
    {
        $tag = request()->post('tag', '');
        if (empty($tag)) {
            return - 1;
        }
        switch ($tag) {
            case 'buy_now':
                // 立即购买
                $_SESSION['order_tag'] = 'buy_now';
                $_SESSION['order_sku_list'] = request()->post('sku_id') . ':' . request()->post('num');
                $_SESSION['order_goods_type'] = request()->post("goods_type"); // 实物类型标识
                break;
            case 'cart':
                // 加入购物车
                $_SESSION['order_tag'] = 'cart';
                $_SESSION['cart_list'] = request()->post('cart_id');
                break;
        }
        return 1;
    }

    /**
     * 获取用户余额
     * 2017年3月1日 10:50:45
     *
     * @param unknown $shop_id            
     * @return unknown
     */
    public function getMemberAccount($shop_id)
    {
        $member = new MemberService();
        $member_account = $member->getMemberAccount($this->uid, $shop_id);
        return $member_account;
    }

    /**
     * 退款/退货/维修订单列表
     * 创建人：周学勇
     * 创建时间：2017年2月7日 16:13:04
     *
     * @return \think\response\View
     */
    public function backList()
    {
        $orderService = new OrderService();
        $page = request()->get('page', '1');
        // 查询订单状态的数量
        $condition['buyer_id'] = $this->uid;
        $condition['order_type'] = 1;
        $condition['order_status'] = array(
            'in',
            '-1,-2'
        );
        $orderList = $orderService->getOrderList($page, 10, $condition, 'create_time desc');
        
        foreach ($orderList['data'] as $key => $item) {
            $order_item_list = array();
            $order_item_list = $orderList['data'][$key]['order_item_list'];
            foreach ($order_item_list as $k => $value) {
                if ($value['refund_status'] == 0 || $value['refund_status'] == - 2) {
                    unset($order_item_list[$k]);
                }
            }
            $orderList['data'][$key]['order_item_list'] = $order_item_list;
        }
        $this->assign("orderList", $orderList['data']);
        $this->assign("page_count", $orderList['page_count']);
        $this->assign("total_count", $orderList['total_count']);
        $this->assign("page", $page);
        
        $member_detail = $this->user->getMemberDetail($this->instance_id);
        $this->assign("member_detail", $member_detail);
        return view($this->style . 'Member/backList');
    }

    /**
     * 取消退款
     * 任鹏强
     * 2017年3月1日15:30:51
     */
    public function cancleOrder()
    {
        if (request()->isAjax()) {
            $orderService = new OrderService();
            $order_id = request()->post('order_id', '');
            $order_goods_id = request()->post('order_goods_id', '');
            $cancle_order = $orderService->orderGoodsCancel($order_id, $order_goods_id);
            return AjaxReturn($cancle_order);
        }
    }

    /**
     * 商品评价/晒单
     * 创建人：周学勇
     * 创建时间：2017年2月7日 16:14:00
     *
     * @return \think\response\View
     */
    public function goodsEvaluationList($page = 1, $page_size = 10)
    {
        $order = new OrderService();
        $condition['uid'] = $this->uid;
        $goodsEvaluationList = $order->getOrderEvaluateDataList($page, $page_size, $condition, 'addtime desc');
        foreach ($goodsEvaluationList['data'] as $k => $v) {
            $goodsEvaluationList['data'][$k]['evaluationImg'] = (empty($v['image'])) ? '' : explode(',', $v['image']);
            
            $goodsEvaluationList['data'][$k]['againEvaluationImg'] = (empty($v['again_image'])) ? '' : explode(',', $v['again_image']);
        }
        
        $this->assign("goodsEvaluationList", $goodsEvaluationList['data']);
        $this->assign("page_count", $goodsEvaluationList['page_count']);
        $this->assign("total_count", $goodsEvaluationList['total_count']);
        $this->assign("page", $page);
        
        $member_detail = $this->user->getMemberDetail($this->instance_id);
        $this->assign("member_detail", $member_detail);
        return view($this->style . 'Member/goodsEvaluationList');
    }

    /**
     * 图片上传
     *
     * @param unknown $file_path            
     * @param unknown $key            
     */
    public function moveUploadFile($file_path, $key)
    {
        $config = new Config();
        $upload_type = $config->getUploadType(0);
        if ($upload_type == 1) {
            $ok = @move_uploaded_file($file_path, $key);
            $result = [
                "code" => $ok,
                "path" => $key,
                "domain" => '',
                "bucket" => ''
            ];
        } elseif ($upload_type == 2) {
            $qiniu = new QiNiu();
            $result = $qiniu->setQiniuUplaod($file_path, $key);
        }
        return $result;
    }

    /**
     * 用户信息
     * 创建人:吴奇
     * 创建时间： 2017年2月7日 16:36：00
     */
    public function person()
    {
        $update_info_status = ""; // 修改信息状态 2017年7月10日 10:50:03
        $upload_headimg_status = ""; // 上传头像状态 2017年7月10日 10:49:00
        if (request()->post('submit')) {
            $user_name = request()->post('user_name', '');
            $user_qq = request()->post('user_qq', '');
            $real_name = request()->post('real_name', '');
            $sex = request()->post('sex', '');
            $birthday = request()->post('birthday', '');
            $location = request()->post('location', '');
            $birthday = date('Y-m-d', strtotime($birthday));
            // 把从前台显示的内容转变为可以存储到数据库中的数据
            $update_info_status = $this->user->updateMemberInformation($user_name, $user_qq, $real_name, $sex, $birthday, $location, "");
        }
        if ($_FILES && request()->post("submit2")) {
            // var_dump($_FILES["user_headimg"]);
            if ((($_FILES["user_headimg"]["type"] == "image/gif") || ($_FILES["user_headimg"]["type"] == "image/jpeg") || ($_FILES["user_headimg"]["type"] == "image/pjpeg") || ($_FILES["user_headimg"]["type"] == "image/png")) && ($_FILES["user_headimg"]["size"] < 10000000)) {
                if ($_FILES["user_headimg"]["error"] > 0) {
                    // echo "错误： " . $_FILES["user_headimg"]["error"] . "<br />";
                }
                $file_name = date("YmdHis") . rand(0, date("is")); // 文件名
                $ext = explode(".", $_FILES["user_headimg"]["name"]);
                $file_name .= "." . $ext[1];
                // 检测文件夹是否存在，不存在则创建文件夹
                $path = 'upload/avator/';
                if (! file_exists($path)) {
                    $mode = intval('0777', 8);
                    mkdir($path, $mode, true);
                }
                $img_result = $this->moveUploadFile($_FILES["user_headimg"]["tmp_name"], $path . $file_name);
                if ($img_result["code"]) {
                    // $user_headimg = $path . $file_name;
                    $user_headimg = $img_result["path"];
                    $upload_headimg_status = $this->user->updateMemberInformation("", "", "", "", "", "", $user_headimg);
                } else {
                    $this->error("头像上传失败!");
                }
                // move_uploaded_file($_FILES["user_headimg"]["tmp_name"], $path . $file_name);
            } else {
                $this->error("请上传图片");
            }
        }
        
        // $this->assign("update_info_status", $update_info_status);
        // $this->assign('upload_headimg_status', $upload_headimg_status);
        $member_detail = $this->user->getMemberDetail($this->instance_id);
        $member_info = $this->user->getMemberDetail();
        if ($member_info['user_info']['birthday'] == 0 || $member_info['user_info']['birthday'] == "") {
            $member_info['user_info']['birthday'] = "";
        } else {
            $member_info['user_info']['birthday'] = date('Y-m-d', $member_info['user_info']['birthday']);
        }
        $this->assign('member_info', $member_info);
        if (! empty($member_info['user_info']['user_headimg'])) {
            $member_img = $member_info['user_info']['user_headimg'];
        } elseif (! empty($member_info['user_info']['qq_openid'])) {
            $member_img = $member_info['user_info']['qq_info_array']['figureurl_qq_1'];
        } elseif (! empty($member_info['user_info']['wx_openid'])) {
            $member_img = '0';
        } else {
            $member_img = '0';
        }
        // 处理状态信息
        if ($member_info["user_info"]["user_status"] == 0) {
            $member_info["user_info"]["user_status"] = "锁定";
        } else {
            $member_info["user_info"]["user_status"] = "正常";
        }
        
        $this->assign('qq_openid', $member_info['user_info']['qq_openid']);
        $this->assign('member_img', $member_img);
        $this->assign('member_detail', $member_detail);
        return view($this->style . 'Member/personInformation');
    }

    /**
     * 优惠券
     * 创建人:吴奇
     * 创建时间： 2017年2月7日 16:36：00
     */
    public function vouchers()
    {
        // 获取该用户的所有已领取未使用的优惠券列表
        $list = $this->user->getMemberCounponList(1);
        foreach ($list as $list2) {
            $list2["shop_id"] = $this->user->getShopNameByShopId($list2["shop_id"]);
            $list2["state"] = "未使用";
        }
        
        $member_detail = $this->user->getMemberDetail($this->instance_id);
        $this->assign("member_detail", $member_detail);
        $this->assign("list", $list);
        return view($this->style . 'Member/vouchers');
    }

    /**
     * 会员积分流水
     * 创建人:吴奇
     * 创建时间：2017年3月1日 17:00
     */
    public function integrallist()
    {
        $shop_id = $this->instance_id;
        $conponAccount = new MemberAccount();
        $start_time = request()->post('start_time', '2016-01-01');
        $end_time = request()->post('end_time', '2099-01-01');
        $page_index = request()->get('page', '1');
        // 每页显示几个
        $page_size = 10;
        $condition['nmar.uid'] = $this->uid;
        $condition['nmar.shop_id'] = $shop_id;
        $condition['nmar.account_type'] = 1;
        // 查看用户在该商铺下的积分消费流水
        $list = $this->user->getAccountList($page_index, $page_size, $condition);
        // $list = $this->user->getPageMemberPointList($start_time, $end_time, $page_index, $page_count, $shop_id);
        foreach ($list["data"] as $list2) {
            // if ($list2["number"] < 0) {
            // $list2["number"] = 0 - $list2["number"];
            // }
            $list2["number"] = (int) $list2["number"];
            $list2["data_id"] = $this->user->getOrderNumber($list2["data_id"])["out_trade_no"];
        }
        // 获取兑换比例
        $account = new MemberAccount();
        $accounts = $account->getConvertRate($shop_id);
        // 查看积分总数
        $account_type = 1;
        
        $conponSum = $conponAccount->getMemberAccount($shop_id, $this->uid, $account_type);
        // 店铺名称
        $shop_name = $this->user->getWebSiteInfo();
        $this->assign([
            'account' => $accounts['convert_rate'],
            "sum" => (int) $conponSum,
            "shopname" => $shop_name['title'],
            "shop_id" => $shop_id,
            'page_count' => $list['page_count'],
            'total_count' => $list['total_count'],
            "balances" => $list,
            'page' => $page_index
        ]);
        
        $member_detail = $this->user->getMemberDetail($this->instance_id);
        $this->assign("member_detail", $member_detail);
        return view($this->style . 'Member/integral');
    }

    /**
     * 会员余额流水
     * 创建人:吴奇
     * 创建时间： 2017年3月1日 17:00
     */
    public function balancelist()
    {
        $start_time = request()->post('start_time', '2016-01-01');
        $end_time = request()->post('end_time', '2099-01-01');
        $page_index = request()->get('page', '1');
        $shop_id = $this->instance_id;
        $page_size = 10;
        $condition['nmar.uid'] = $this->uid;
        $condition['nmar.shop_id'] = $shop_id;
        $condition['nmar.account_type'] = 2;
        // 该店铺下的余额流水
        $list = $this->user->getAccountList($page_index, $page_size, $condition);
        // $list = $this->user->getPageMemberBalanceList($start_time, $end_time, $page_index, $page_count, $shop_id);
        // 对获取的数据进行处理
        foreach ($list["data"] as $list2) {
            // if ($list2["number"] < 0) {
            // $list2["number"] = number_format(0 - $list2["number"], 2);
            // }
            $list2["data_id"] = $this->user->getOrderNumber($list2["data_id"])["out_trade_no"];
        }
        // 用户在该店铺的账户余额总数
        $account_type = 2;
        $accountAccount = new MemberAccount();
        $accountSum = $accountAccount->getMemberAccount($shop_id, $this->uid, $account_type);
        $this->assign("sum", number_format($accountSum, 2));
        // 店铺名称
        // $shop_name = $this->user->getShopNameByShopId($shop_id);
        $shop_name = $this->user->getWebSiteInfo();
        // 余额充值
        $pay = new UnifyPay();
        $pay_no = $pay->createOutTradeNo();
        $this->assign("pay_no", $pay_no);
        
        $this->assign("shopname", $shop_name['title']);
        $this->assign('page_count', $list['page_count']);
        $this->assign('total_count', $list['total_count']);
        $this->assign("balances", $list);
        $this->assign('page', $page_index);
        
        $member_detail = $this->user->getMemberDetail($this->instance_id);
        $this->assign("member_detail", $member_detail);
        return view($this->style . 'Member/balance');
    }

    /**
     * 提现记录
     */
    public function balanceWithdrawList()
    {
        $page_index = request()->get('page', '1');
        $shop_id = $this->instance_id;
        $page_size = 10;
        $condition['uid'] = $this->uid;
        $condition['shop_id'] = $shop_id;
        /* $condition['status'] = 1; */
        // 该店铺下的余额流水
        $list = $this->user->getMemberBalanceWithdraw($page_index, $page_size, $condition, 'ask_for_date desc');
        foreach ($list['data'] as $k => $v) {
            if ($v['status'] == 1) {
                $list['data'][$k]['status'] = '已同意';
            } elseif ($v['status'] == 0) {
                $list['data'][$k]['status'] = '已申请';
            } else {
                $list['data'][$k]['status'] = '已拒绝';
            }
        }
        // 用户在该店铺的账户余额总数
        $account_type = 2;
        $accountAccount = new MemberAccount();
        $accountSum = $accountAccount->getMemberAccount($shop_id, $this->uid, $account_type);
        $this->assign("sum", number_format($accountSum, 2));
        // 店铺名称
        $shop_name = $this->user->getWebSiteInfo();
        // 余额充值
        $pay = new UnifyPay();
        $pay_no = $pay->createOutTradeNo();
        $this->assign("pay_no", $pay_no);
        
        $this->assign("shopname", $shop_name['title']);
        $this->assign('page_count', $list['page_count']);
        $this->assign('total_count', $list['total_count']);
        $this->assign("balances", $list);
        $this->assign('page', $page_index);
        
        $member_detail = $this->user->getMemberDetail($this->instance_id);
        $this->assign("member_detail", $member_detail);
        return view($this->style . 'Member/balanceWithdrawList');
    }

    /**
     * 余额提现
     */
    public function balanceWithdrawals()
    {
        if (request()->isAjax()) {
            // 提现
            $uid = $this->uid;
            $withdraw_no = time() . rand(111, 999);
            $bank_account_id = request()->post('bank_id', '');
            $cash = request()->post('cash', '');
            $shop_id = $this->instance_id;
            $member = new MemberService();
            $retval = $member->addMemberBalanceWithdraw($shop_id, $withdraw_no, $uid, $bank_account_id, $cash);
            return AjaxReturn($retval);
        } else {
            $member = new MemberService();
            $account_list = $member->getMemberBankAccount();
            // 获取会员余额
            $uid = $this->uid;
            $shop_id = $this->instance_id;
            $members = new MemberAccount();
            $account = $members->getMemberBalance($uid);
            $instance_id = $this->instance_id;
            $this->assign('shop_id', $instance_id);
            $this->assign('account', $account);
            $config = new Config();
            $balanceConfig = $config->getBalanceWithdrawConfig($shop_id);
            $cash = $balanceConfig['value']["withdraw_cash_min"];
            $this->assign('cash', $cash);
            $poundage = $balanceConfig['value']["withdraw_multiple"];
            $this->assign('poundage', $poundage);
            $withdraw_message = $balanceConfig['value']["withdraw_message"];
            $this->assign('withdraw_message', $withdraw_message);
            
            $this->assign('account_list', $account_list);
            
            $member_detail = $this->user->getMemberDetail($this->instance_id);
            $this->assign("member_detail", $member_detail);
            return view($this->style . "Member/balanceWithdrawals");
        }
    }

    /**
     * 添加银行账户
     */
    public function addAccount()
    {
        $member = new MemberService();
        $uid = $this->uid;
        $realname = request()->post('realname', '');
        $mobile = request()->post('mobile', '');
        $bank_type = request()->post('bank_type', '');
        $account_number = request()->post('account_number', '');
        $branch_bank_name = request()->post('branch_bank_name', '');
        $retval = $member->addMemberBankAccount($uid, $bank_type, $branch_bank_name, $realname, $account_number, $mobile);
        return AjaxReturn($retval);
    }

    /**
     * 删除账户信息
     */
    public function delAccount()
    {
        if (request()->isAjax()) {
            $member = new MemberService();
            $uid = $this->uid;
            $account_id = request()->post('id', '');
            $retval = $member->delMemberBankAccount($account_id);
            return AjaxReturn($retval);
        }
    }

    /**
     * 获取要修改的银行账户信息
     */
    public function getbankinfo()
    {
        $member = new MemberService();
        $id = request()->post('id', '');
        $result = $member->getMemberBankAccountDetail($id);
        return $result;
    }

    /**
     * 修改会员提现银行账户信息
     */
    public function updateBanckAccount()
    {
        if (request()->isAjax()) {
            $member = new MemberService();
            $account_id = request()->post('id', '');
            $member_bank_account = new NsMemberBankAccountModel();
            $result = $member_bank_account->getCount([
                'uid' => $this->uid,
                'id' => $account_id
            ]);
            if ($result == 0) {
                $retval = - 1;
            }
            $realname = request()->post('realname', '');
            $mobile = request()->post('mobile', '');
            $bank_type = request()->post('bank_type', '');
            $account_number = request()->post('account_number', '');
            $branch_bank_name = request()->post('branch_bank_name', '');
            $retval = $member->updateMemberBankAccount($account_id, $branch_bank_name, $realname, $account_number, $mobile);
            return AjaxReturn($retval);
        }
    }

    /**
     * 创建充值订单
     */
    public function createRechargeOrder()
    {
        $recharge_money = request()->post('recharge_money', 0);
        $out_trade_no = request()->post('out_trade_no', '');
        if (empty($recharge_money) || empty($out_trade_no)) {
            return AjaxReturn(0);
        } else {
            $member = new MemberService();
            $retval = $member->createMemberRecharge($recharge_money, $this->uid, $out_trade_no);
            return AjaxReturn($retval);
        }
    }

    /**
     * 余额积分相互兑换
     * 吴奇
     * 2017/3/1 17:57
     */
    public function exchange()
    {
        $point = request()->post('amount', '');
        $point = (float) $point;
        $shop_id = request()->post('shopid', '');
        $shop_id = intval($shop_id);
        $result = $this->user->memberPointToBalance($this->uid, $shop_id, $point);
        if ($result == 1) {
            $this->assign("shop_id", $shop_id);
            return view($this->style . 'Member/exchangeSuccess');
        }
    }

    /**
     * 退出登录
     * 吴奇
     * 2017/2/15 16:08
     */
    public function logOut()
    {
        $member = new MemberService();
        $member->Logout();
        return AjaxReturn(1);
    }

    /**
     * 账号安全
     */
    public function userSecurity()
    {
        if (request()->isGet()) {
            $atc = request()->get('atc', '');
            $this->assign('atc', $atc);
        }
        $member_detail = $this->user->getMemberDetail($this->instance_id);
        $this->assign("member_detail", $member_detail);
        return view($this->style . "Member/userSecurity");
    }

    /**
     * 吴奇
     * 商品评价
     * 2017/2/16 16:08
     */
    public function reviewCommodity()
    {
        // 先考虑显示的样式
        if (request()->isGet()) {
            $order_id = request()->get('orderid', '');
            // 判断该订单是否是属于该用户的
            $order_service = new OrderService();
            $condition['order_id'] = $order_id;
            $condition['buyer_id'] = $this->uid;
            $condition['review_status'] = 0;
            $condition['order_status'] = array(
                'in',
                '3,4'
            );
            $order_count = $order_service->getUserOrderCountByCondition($condition);
            if ($order_count == 0) {
                $this->error("对不起,您无权进行此操作");
            }
            
            $order = new Order();
            $list = $order->getOrderGoods($order_id);
            $orderDetail = $order->getDetail($order_id);
            $this->assign("order_no", $orderDetail['order_no']);
            $this->assign("order_id", $order_id);
            $this->assign("list", $list);
            return view($this->style . 'Member/reviewCommodity');
            if (($orderDetail['order_status'] == 3 || $orderDetail['order_status'] == 4) && $orderDetail['is_evaluate'] == 0) {} else {
                $redirect = __URL(__URL__ . "/member/index");
                $this->redirect($redirect);
            }
        } else {
            return view($this->style . "Member/orderList");
        }
    }

    /**
     * 追评
     * 李吉
     * 2017-02-17 14:12:15
     */
    public function reviewAgain()
    {
        // 先考虑显示的样式
        if (request()->isGet()) {
            $order_id = request()->get('orderid', '');
            // 判断该订单是否是属于该用户的
            $order_service = new OrderService();
            $condition['order_id'] = $order_id;
            $condition['buyer_id'] = $this->uid;
            $condition['is_evaluate'] = 1;
            $order_count = $order_service->getUserOrderCountByCondition($condition);
            if ($order_count == 0) {
                $this->error("对不起,您无权进行此操作");
            }
            
            $order = new Order();
            $list = $order->getOrderGoods($order_id);
            $orderDetail = $order->getDetail($order_id);
            $this->assign("order_no", $orderDetail['order_no']);
            $this->assign("order_id", $order_id);
            $this->assign("list", $list);
            if (($orderDetail['order_status'] == 3 || $orderDetail['order_status'] == 4) && $orderDetail['is_evaluate'] == 1) {
                return view($this->style . 'Member/reviewAgain');
            } else {
                
                $redirect = __URL(__URL__ . "/member/index");
                $this->redirect($redirect);
            }
        } else {
            return view($this->style . "Member/orderList");
        }
    }

    /**
     * 增加商品评价
     */
    public function modityCommodity()
    {
        return 1;
    }

    /**
     * 功能：绑定手机
     * 创建人：李志伟
     * 创建时间：2017年2月16日17:17:43
     */
    public function modifyMobile()
    {
        $member = new MemberService();
        if (request()->isAjax()) {
            $uid = $this->user->getSessionUid();
            $mobile = request()->post('mobile', '');
            $mobile_code = request()->post('mobile_code', '');
            if ($this->notice['noticeMobile'] == 1) {
                $verification_code = Session::get('mobileVerificationCode');
                if ($mobile_code == $verification_code && ! empty($verification_code)) {
                    $retval = $member->modifyMobile($uid, $mobile);
                    if ($retval == 1)
                        Session::delete('mobileVerificationCode');
                    return AjaxReturn($retval);
                } else {
                    return array(
                        'code' => 0,
                        'message' => '手机验证码输入错误'
                    );
                }
            } else {
                // 获取手机是否被绑定
                $is_bin_mobile = $member->memberIsMobile($mobile);
                if ($is_bin_mobile) {
                    return array(
                        'code' => 0,
                        'message' => '该手机号已存在'
                    );
                } else {
                    $retval = $member->modifyMobile($uid, $mobile);
                    return AjaxReturn($retval);
                }
            }
        }
    }

    /**
     * 功能：绑定邮箱
     * 创建人：李志伟
     * 创建时间：2017年2月16日17:17:43
     */
    public function modifyEmail()
    {
        $member = new MemberService();
        $uid = $this->user->getSessionUid();
        $email = request()->post('email', '');
        $email_code = request()->post('email_code', '');
        if ($this->notice['noticeEmail'] == 1) {
            $verification_code = Session::get('emailVerificationCode');
            if ($email_code == $verification_code && ! empty($verification_code)) {
                $retval = $member->modifyEmail($uid, $email);
                if ($retval == 1)
                    Session::delete('emailVerificationCode');
                return AjaxReturn($retval);
            } else {
                return array(
                    'code' => 0,
                    'message' => '邮箱验证码输入错误'
                );
            }
        } else {
            // 获取邮箱是否被绑定
            $is_bin_email = $member->memberIsEmail($email);
            if ($is_bin_email) {
                return array(
                    'code' => 0,
                    'message' => '该邮箱已存在'
                );
            } else {
                $retval = $member->modifyEmail($uid, $email);
                return AjaxReturn($retval);
            }
        }
    }

    /**
     * 功能：修改密码
     * 创建人：李志伟
     * 创建时间：2017年2月16日17:58:06
     */
    public function modifyPassword()
    {
        $member = new MemberService();
        $uid = $this->user->getSessionUid();
        $old_password = request()->post('old_password', '');
        $new_password = request()->post('new_password', '');
        $retval = $member->ModifyUserPassword($uid, $old_password, $new_password);
        return AjaxReturn($retval);
    }

    /**
     * 申请退款
     *
     * @return \think\response\View
     */
    public function refundDetail()
    {
        $order_goods_id = request()->get('order_goods_id', 0);
        if (! is_numeric($order_goods_id) || $order_goods_id == 0) {
            $this->error("没有获取到退款信息");
        }
        $order_service = new OrderService();
        $detail = $order_service->getOrderGoodsRefundInfo($order_goods_id);
        $this->assign("detail", $detail);
        
        $condition['order_goods_id'] = $order_goods_id;
        $condition['buyer_id'] = $this->uid;
        $count = $order_service->getUserOrderGoodsCountByCondition($condition);
        if ($count == 0) {
            $this->error("对不起,您无权进行此操作");
        }
        
        // 实际可退款金额
        $refund_money = $order_service->orderGoodsRefundMoney($order_goods_id);
        $this->assign('refund_money', sprintf("%.2f", $refund_money));
        
        // 余额退款
        $order_goods_service = new OrderGoods();
        $refund_balance = $order_goods_service->orderGoodsRefundBalance($order_goods_id);
        $this->assign("refund_balance", sprintf("%.2f", $refund_balance));
        
        // 查询店铺默认物流地址
        $express = new Express();
        $address = $express->getDefaultShopExpressAddress($this->instance_id);
        $this->assign("address_info", $address);
        // 查询商家地址
        $shop_info = $order_service->getShopReturnSet($this->instance_id);
        $this->assign("shop_info", $shop_info);
        $member_detail = $this->user->getMemberDetail($this->instance_id);
        $this->assign("member_detail", $member_detail);
        
        return view($this->style . "Member/refundDetail");
    }

    /**
     * 申请退款
     */
    public function orderGoodsRefundAskfor()
    {
        $order_id = request()->post('order_id', 0);
        $order_goods_id = request()->post('order_goods_id', 0);
        $refund_type = request()->post('refund_type', 1);
        $refund_require_money = request()->post('refund_require_money', 0);
        $refund_reason = request()->post('refund_reason', '');
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsRefundAskfor($order_id, $order_goods_id, $refund_type, $refund_require_money, $refund_reason);
        return AjaxReturn($retval);
    }

    /**
     * 买家退货
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function orderGoodsRefundExpress()
    {
        $order_id = request()->post('order_id', 0);
        $order_goods_id = request()->post('order_goods_id', 0);
        $refund_express_company = request()->post('refund_express_company', '');
        $refund_shipping_no = request()->post('refund_shipping_no', 0);
        $refund_reason = request()->post('refund_reason', '');
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsReturnGoods($order_id, $order_goods_id, $refund_express_company, $refund_shipping_no);
        return AjaxReturn($retval);
    }

    /**
     * 设置用户支付密码
     */
    public function setUserPaymentPassword()
    {
        if (request()->isAjax()) {
            $uid = $this->uid;
            $payment_password = request()->post("payment_password", '');
            $member = new MemberService();
            $res = $member->setUserPaymentPassword($uid, $payment_password);
            return AjaxReturn($res);
        }
    }

    /**
     * 修改用户支付密码
     */
    public function updateUserPaymentPassword()
    {
        if (request()->isAjax()) {
            $uid = $this->uid;
            $old_payment_password = request()->post("old_payment_password", '');
            $new_payment_password = request()->post("new_payment_password", '');
            $member = new MemberService();
            $res = $member->updateUserPaymentPassword($uid, $old_payment_password, $new_payment_password);
            return AjaxReturn($res);
        }
    }

    /**
     * 验证码
     *
     * @return multitype:number string
     */
    public function vertify()
    {
        $vertification = request()->post('vertification', '');
        if (! captcha_check($vertification)) {
            $retval = [
                'code' => 0,
                'message' => "验证码错误"
            ];
        } else {
            $retval = [
                'code' => 1,
                'message' => "验证码正确"
            ];
        }
        return $retval;
    }
}
