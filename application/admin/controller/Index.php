<?php
/**
 * Index.php
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

use data\service\Goods as GoodsService;
use data\service\Order as OrderService;
use data\service\User as User;
use data\service\Weixin;
use think\helper\Time;
use data\service\Member;

/**
 * 后台主界面
 *
 * @author Administrator
 *        
 */
class Index extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $debug = config('app_debug') == true ? '开发者模式' : '部署模式';
        $this->assign('debug', $debug);
        $main = \think\Request::instance()->domain();
        $this->assign('main', $main);
        // 销售排行
        $goods_rank = $this->getGoodsRealSalesRank();
        $this->assign("goods_list", $goods_rank);
        return view($this->style . 'Index/index');
    }

    /**
     * ajax 加载 店铺 会员 信息
     */
    public function getUserInfo()
    {
        $auth = new User();
        $user_info = $auth->getUserDetail($this->uid);
        return $user_info;
    }

    /**
     * 获取会员提现审核中的数量
     * 2017年7月10日 12:06:01 王永杰
     */
    public function getMemberBalanceWithdrawCount()
    {
        $member = new Member();
        $count = $member->getMemberBalanceWithdrawCount([
            "status" => 0
        ]);
        return $count;
    }

    /**
     * 获取 商品 数量 全部 出售中 已审核 已下架 库存预警数
     */
    public function getGoodsCount()
    {
        $goods_count = new GoodsService();
        $goods_count_array = array();
        // 全部
        $goods_count_array['all'] = $goods_count->getGoodsCount([
            'shop_id' => $this->instance_id
        ]);
        // 出售中
        $goods_count_array['sale'] = $goods_count->getGoodsCount([
            'shop_id' => $this->instance_id,
            'state' => 1
        ]);
        // 仓库中已审核
        $goods_count_array['audit'] = $goods_count->getGoodsCount([
            'shop_id' => $this->instance_id,
            'state' => 0
        ]);
        // 下架
        $goods_count_array['shelf'] = $goods_count->getGoodsCount([
            'shop_id' => $this->instance_id,
            'state' => 10
        ]);
        //库存低于预警值的商品数
        $goods_count_array['warning'] = $goods_count->getGoodsCount([
            'shop_id' => $this->instance_id,
            'min_stock_alarm' => array("neq", 0),
            'stock' => array("exp", "<= min_stock_alarm")
        ]);
        return $goods_count_array;
    }

    /**
     * 获取 订单数量 代付款 待发货 已发货 已收货 已完成 已关闭 退款中 已退款
     */
    public function getOrderCount()
    {
        $order = new OrderService();
        $order_count_array = array();
        $order_count_array['daifukuan'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => 0
        ]); // 代付款
        $order_count_array['daifahuo'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => 1
        ]); // 代发货
        $order_count_array['yifahuo'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => 2
        ]); // 已发货
        $order_count_array['yishouhuo'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => 3
        ]); // 已收货
        $order_count_array['yiwancheng'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => 4
        ]); // 已完成
        $order_count_array['yiguanbi'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => 5,
            'is_deleted' => 0
        ]); // 已关闭
        $order_count_array['tuikuanzhong'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => - 1,
        ]); // 退款中
        $order_count_array['yituikuan'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => - 2
        ]); // 已退款
        $order_count_array['all'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'is_deleted' => 0
        ]); // 全部订单数量，排除已删除的
        return $order_count_array;
    }

    /**
     * 获取销售统计
     *
     * @return unknown
     */
    public function getSalesStatistics()
    {
        $order = new OrderService();
        $condition['shop_id'] = $this->instance_id;
        //[待发货、已发货、已收货、已完成]
        $condition['order_status'] = [
            'in',
            "1,2,3,4"
        ];
        $tmp_condition = $condition;
        
        // 查询今天
        $start = strtotime(date('Y-m-d 00:00:00'));
        $end = strtotime(date('Y-m-d H:i:s'));
        $tmp_condition["create_time"] = [
            'between',
            [
                $start,
                $end
            ]
        ];
        $data['curr_day_money'] = $order->getPayMoneySum($tmp_condition); // 查询今天的订单总金额
        $tmp_condition = $condition;
        $start = getTimeTurnTimeStamp(0, 0, 0, date("m", strtotime("-1 day")), date("d", strtotime("-1 day")), date("Y", strtotime("-1 day")));
        $end = getTimeTurnTimeStamp(23, 59, 59, date("m", strtotime("-1 day")), date("d", strtotime("-1 day")), date("Y", strtotime("-1 day")));
        $tmp_condition["create_time"] = [
            'between',
            [
                $start,
                $end
            ]
        ];
        $data['yesterday_money'] = $order->getPayMoneySum($tmp_condition);
        $data['yesterday_goods'] = $order->getGoodsNumSum($tmp_condition);
        // 查看本月
        $tmp_condition = $condition;
        $start = mktime(0, 0, 0, date("m", time()), 1, date("Y", time()));
        $end = mktime(23, 59, 59, date("m", time()), date("t"), date("Y", time()));
        $tmp_condition["create_time"] = [
            'between',
            [
                $start,
                $end
            ]
        ];
        $data['month_money'] = $order->getPayMoneySum($tmp_condition);
        $data['month_goods'] = $order->getGoodsNumSum($tmp_condition);
        return $data;
    }

    /**
     * 订单 图表 数据
     */
    public function getOrderChartCount()
    {
        $type = request()->post('date',4);
        $order = new OrderService();
        $data = array();
        if ($type == 1) {
            list ($start, $end) = Time::today();
            for ($i = 0; $i < 24; $i ++) {
                $date_start = date("Y-m-d H:i:s", $start + 3600 * $i);
                $date_end = date("Y-m-d H:i:s", $start + 3600 * ($i + 1));
                $count = $order->getOrderCount([
                    'shop_id' => $this->instance_id,
                    'create_time' => [
                        'between',
                        [
                            getTimeTurnTimeStamp($date_start),
                            getTimeTurnTimeStamp($date_end)
                        ]
                    ]
                ]);
                $data[$i] = array(
                    $i . ':00',
                    $count
                );
            }
        } elseif ($type == 2) {
            list ($start, $end) = Time::yesterday();
            for ($j = 0; $j < 24; $j ++) {
                $date_start = date("Y-m-d H:i:s", $start + 3600 * $j);
                $date_end = date("Y-m-d H:i:s", $start + 3600 * ($j + 1));
                $count = $order->getOrderCount([
                    'shop_id' => $this->instance_id,
                    'create_time' => [
                        'between',
                        [
                            getTimeTurnTimeStamp($date_start),
                            getTimeTurnTimeStamp($date_end)
                        ]
                    ]
                ]);
                $data[$j] = array(
                    $j . ':00',
                    $count
                );
            }
        } elseif ($type == 3) {
            list ($start, $end) = Time::week();
            $start = $start - 604800;
            for ($j = 0; $j < 7; $j ++) {
                $date_start = date("Y-m-d H:i:s", $start + 86400 * $j);
                $date_end = date("Y-m-d H:i:s", $start + 86400 * ($j + 1));
                $count = $order->getOrderCount([
                    'shop_id' => $this->instance_id,
                    'create_time' => [
                        'between',
                        [
                            getTimeTurnTimeStamp($date_start),
                            getTimeTurnTimeStamp($date_end)
                        ]
                    ]
                ]);
                $data[$j] = array(
                    '星期' . ($j + 1),
                    $count
                );
            }
        } elseif ($type == 4) {
            list ($start, $end) = Time::month();
            for ($j = 0; $j < ($end + 1 - $start) / 86400; $j ++) {
                $date_start = date("Y-m-d H:i:s", $start + 86400 * $j);
                $date_end = date("Y-m-d H:i:s", $start + 86400 * ($j + 1));
                $count = $order->getOrderCount([
                    'shop_id' => $this->instance_id,
                    'create_time' => [
                        'between',
                        [
                            getTimeTurnTimeStamp($date_start),
                            getTimeTurnTimeStamp($date_end)
                        ]
                    ]
                ]);
                $data[$j] = array(
                    (1 + $j) . '日',
                    $count
                );
            }
        }
        return $data;
    }

    /**
     * 商品销售排行
     *
     * @return unknown
     */
    public function getGoodsRealSalesRank()
    {
        $goods = new GoodsService();
        $goods_list = $goods->getGoodsRank(array(
            "shop_id" => $this->instance_id
        ));
        return $goods_list;
    }

    /**
     * 咨询个数
     *
     * @param unknown $condition            
     * @return unknown
     */
    public function getConsultCount()
    {
        $goods = new GoodsService();
        $good_count = $goods->getConsultCount(array(
            "shop_id" => $this->instance_id,
            "consult_reply" => ""
        ));
        return $good_count;
    }

    /**
     * 获取全部关注人数
     * 2017年7月20日 15:25:31 王永杰
     */
    public function getWeiXinFansCount()
    {
        $weixin = new Weixin();
        $count = $weixin->getWeixinFansCount([
            'instance_id' => $this->instance_id
        ]);
        return $count;
    }

    /**
     * 订单 图表 数据
     */
    public function getWeiXinFansChartCount()
    {
        $type = request()->post('date',4);
        $weixin = new Weixin();
        $data = array();
        if ($type == 1) {
            list ($start, $end) = Time::today();
            for ($i = 0; $i < 24; $i ++) {
                $date_start = date("Y-m-d H:i:s", $start + 3600 * $i);
                $date_end = date("Y-m-d H:i:s", $start + 3600 * ($i + 1));
                $count = $weixin->getWeixinFansCount([
                    'instance_id' => $this->instance_id,
                    'subscribe_date' => [
                        'between',
                        [
                            getTimeTurnTimeStamp($date_start),
                            getTimeTurnTimeStamp($date_end)
                        ]
                    ]
                ]);
                $data[0][$i] = $i . ':00';
                $data[1][$i] = $count;
            }
        } elseif ($type == 2) {
            list ($start, $end) = Time::yesterday();
            for ($j = 0; $j < 24; $j ++) {
                $date_start = date("Y-m-d H:i:s", $start + 3600 * $j);
                $date_end = date("Y-m-d H:i:s", $start + 3600 * ($j + 1));
                $count = $weixin->getWeixinFansCount([
                    'instance_id' => $this->instance_id,
                    'subscribe_date' => [
                        'between',
                        [
                            getTimeTurnTimeStamp($date_start),
                            getTimeTurnTimeStamp($date_end)
                        ]
                    ]
                ]);
                $data[0][$j] = $j . ':00';
                $data[1][$j] = $count;
            }
        } elseif ($type == 3) {
            list ($start, $end) = Time::week();
            $start = $start - 604800;
            for ($j = 0; $j < 7; $j ++) {
                $date_start = date("Y-m-d H:i:s", $start + 86400 * $j);
                $date_end = date("Y-m-d H:i:s", $start + 86400 * ($j + 1));
                $count = $weixin->getWeixinFansCount([
                    'instance_id' => $this->instance_id,
                    'subscribe_date' => [
                        'between',
                        [
                            getTimeTurnTimeStamp($date_start),
                            getTimeTurnTimeStamp($date_end)
                        ]
                    ]
                ]);
                $data[0][$j] = '星期' . ($j + 1);
                $data[1][$j] = $count;
            }
        } elseif ($type == 4) {
            list ($start, $end) = Time::month();
            for ($j = 0; $j < ($end + 1 - $start) / 86400; $j ++) {
                $date_start = date("Y-m-d H:i:s", $start + 86400 * $j);
                $date_end = date("Y-m-d H:i:s", $start + 86400 * ($j + 1));
                $count = $weixin->getWeixinFansCount([
                    'instance_id' => $this->instance_id,
                    'subscribe_date' => [
                        'between',
                        [
                            getTimeTurnTimeStamp($date_start),
                            getTimeTurnTimeStamp($date_end)
                        ]
                    ]
                ]);
                $data[0][$j] = (1 + $j) . '日';
                $data[1][$j] = $count;
            }
        }
        return $data;
    }

    /**
     * 设置操作提示是否显示
     * 保存7天
     */
    public function setWarmPromptIsShow()
    {
        $value = request()->post("value", "show");
        $res = cookie("warm_promt_is_show", $value, 60 * 60 * 24 * 7);
        return $this->getWarmPromptIsShow();
    }
}
