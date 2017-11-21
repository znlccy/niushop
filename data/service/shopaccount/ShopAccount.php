<?php
/**
 * ShopAccount.php
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
namespace data\service\shopaccount;

use data\service\BaseService;
use data\model\NsShopCoinRecordsModel;
use data\model\NsShopModel;
use data\model\NsShopAccountModel;
use data\model\NsShopAccountProfitRecordsModel;
use data\model\NsShopAccountMoneyRecordsModel;
use data\model\NsShopAccountProceedsRecordsModel;
use data\model\NsShopAccountReturnRecordsModel;
use data\model\NsShopAccountWithdrawRecordsModel;
use data\model\NsShopAccountRecordsModel;
use data\model\NsShopOrderReturnModel;
use data\model\NsOrderGoodsModel;
use data\model\NsOrderGoodsPromotionDetailsModel;
use data\model\NsShopOrderGoodsReturnModel;
use data\model\NsAccountModel;
use data\model\NsAccountOrderRecordsModel;
use data\model\NsAccountReturnRecordsModel;
use data\model\NsAccountWithdrawRecordsModel;
use data\model\NsAccountRecordsModel;
use data\model\NsAccountAssistantRecordsModel;
use data\model\NsAccountWithdrawUserRecordsModel;

/**
 * 店铺账户管理
 */
class ShopAccount extends BaseService
{

    /**
     * **************************************************店铺账户计算--Start****************************************************************
     */
    /**
     * 添加店铺的营业总额记录表
     * 
     * @param unknown $serial_no            
     * @param unknown $shop_id            
     * @param unknown $money            
     * @param unknown $account_type            
     * @param unknown $type_alis_id            
     * @param unknown $remark            
     */
    public function addShopAccountProfitRecords($serial_no, $shop_id, $money, $account_type, $type_alis_id, $remark)
    {
        $model = new NsShopAccountProfitRecordsModel();
        $records_list = $model->getInfo([
            "type_alis_id" => $type_alis_id,
            "account_type" => $account_type
        ], "shop_id");
        if (empty($records_list)) {
            $model->startTrans();
            try {
                $data = array(
                    'shop_id' => $shop_id,
                    'serial_no' => $serial_no,
                    'account_type' => $account_type,
                    'money' => $money,
                    'type_alis_id' => $type_alis_id,
                    'remark' => $remark,
                    'create_time' => time()
                );
                $records_id = $model->save($data);
                // 更新营业总额字段
                $this->updateShopAccountProfit($shop_id, $money);
                $model->commit();
                return $model->id;
            } catch (\Exception $e) {
                $model->rollback();
                return $e->getMessage();
            }
        }
    }

    /**
     * 更新店铺的营业总额字段
     * 
     * @param unknown $shop_id            
     * @param unknown $money            
     */
    private function updateShopAccountProfit($shop_id, $money)
    {
        $account_model = new NsShopAccountModel();
        $account_info = $account_model->get($shop_id);
        // 没有的话新建账户
        if (empty($account_info)) {
            $data = array(
                'shop_id' => $shop_id
            );
            $account_model->save($data);
            $account_info = $account_model->get($shop_id);
        }
        $data = array(
            "shop_profit" => $account_info["shop_profit"] + $money
        );
        $retval = $account_model->save($data, [
            'shop_id' => $shop_id
        ]);
        return $retval;
    }

    /**
     * 添加店铺的总的入账记录表
     * 
     * @param unknown $serial_no            
     * @param unknown $shop_id            
     * @param unknown $money            
     * @param unknown $account_type            
     * @param unknown $type_alis_id            
     * @param unknown $remark            
     */
    public function addShopAccountMoneyRecords($serial_no, $shop_id, $money, $account_type, $type_alis_id, $remark)
    {
        $model = new NsShopAccountMoneyRecordsModel();
        $records_list = $model->getInfo([
            "type_alis_id" => $type_alis_id,
            "account_type" => $account_type
        ], "shop_id");
        if (empty($records_list)) {
            $model->startTrans();
            try {
                $data = array(
                    'shop_id' => $shop_id,
                    'serial_no' => $serial_no,
                    'account_type' => $account_type,
                    'money' => $money,
                    'type_alis_id' => $type_alis_id,
                    'remark' => $remark,
                    'create_time' => time()
                );
                $records_id = $model->save($data);
                // 更新营业总额字段
                $this->updateShopAccountTotalMoney($shop_id, $money);
                $model->commit();
                return $model->id;
            } catch (\Exception $e) {
                $model->rollback();
                return $e->getMessage();
            }
        }
    }

    /**
     * 更新店铺的入账总额
     * 
     * @param unknown $shop_id            
     * @param unknown $money            
     */
    private function updateShopAccountTotalMoney($shop_id, $money)
    {
        $account_model = new NsShopAccountModel();
        $account_info = $account_model->get($shop_id);
        // 没有的话新建账户
        if (empty($account_info)) {
            $data = array(
                'shop_id' => $shop_id
            );
            $account_model->save($data);
            $account_info = $account_model->get($shop_id);
        }
        $data = array(
            "shop_total_money" => $account_info["shop_total_money"] + $money
        );
        $retval = $account_model->save($data, [
            'shop_id' => $shop_id
        ]);
        return $retval;
    }

    /**
     * 添加店铺的总的收益记录表
     * 
     * @param unknown $serial_no            
     * @param unknown $shop_id            
     * @param unknown $money            
     * @param unknown $account_type            
     * @param unknown $type_alis_id            
     * @param unknown $remark            
     */
    public function addShopAccountProceedsRecords($serial_no, $shop_id, $money, $account_type, $type_alis_id, $remark)
    {
        $model = new NsShopAccountProceedsRecordsModel();
        $records_list = $model->getInfo([
            "type_alis_id" => $type_alis_id,
            "account_type" => $account_type
        ], "shop_id");
        if (empty($records_list)) {
            $model->startTrans();
            try {
                $data = array(
                    'shop_id' => $shop_id,
                    'serial_no' => $serial_no,
                    'account_type' => $account_type,
                    'money' => $money,
                    'type_alis_id' => $type_alis_id,
                    'remark' => $remark,
                    'create_time' => time()
                );
                $records_id = $model->save($data);
                // 更新店铺总收益字段
                $this->updateShopAccountProceeds($shop_id, $money);
                // 添加店铺的整体资金流水
                $this->addShopAccountRecords(getSerialNo(), $shop_id, $money, $account_type, $type_alis_id, $remark, "订单完成，资金入账");
                $model->commit();
                return $model->id;
            } catch (\Exception $e) {
                $model->rollback();
                return $e->getMessage();
            }
        }
    }

    /**
     * 更新店铺的总收益
     * 
     * @param unknown $shop_id            
     * @param unknown $money            
     */
    private function updateShopAccountProceeds($shop_id, $money)
    {
        $account_model = new NsShopAccountModel();
        $account_info = $account_model->get($shop_id);
        // 没有的话新建账户
        if (empty($account_info)) {
            $data = array(
                'shop_id' => $shop_id
            );
            $account_model->save($data);
            $account_info = $account_model->get($shop_id);
        }
        $data = array(
            "shop_proceeds" => $account_info["shop_proceeds"] + $money
        );
        $retval = $account_model->save($data, [
            'shop_id' => $shop_id
        ]);
        return $retval;
    }

    /**
     * 添加店铺的总的收益记录表
     * 
     * @param unknown $serial_no            
     * @param unknown $shop_id            
     * @param unknown $money            
     * @param unknown $account_type            
     * @param unknown $type_alis_id            
     * @param unknown $remark            
     */
    private function addShopAccountReturnRecords($serial_no, $shop_id, $money, $account_type, $type_alis_id, $remark)
    {
        $model = new NsShopAccountReturnRecordsModel();
        $records_list = $model->getInfo([
            "type_alis_id" => $type_alis_id,
            "account_type" => $account_type
        ], "shop_id");
        if (empty($records_list)) {
            $model->startTrans();
            try {
                $data = array(
                    'shop_id' => $shop_id,
                    'serial_no' => $serial_no,
                    'account_type' => $account_type,
                    'money' => $money,
                    'type_alis_id' => $type_alis_id,
                    'remark' => $remark,
                    'create_time' => time()
                );
                $records_id = $model->save($data);
                // 更新店铺总收益字段
                $this->updateShopAccountReturn($shop_id, $money);
                // 添加店铺的整体资金流水
                $this->addShopAccountRecords(getSerialNo(), $shop_id, - $money, $account_type, $type_alis_id, $remark, "平台抽取店铺利润。");
                $model->commit();
                return $model->id;
            } catch (\Exception $e) {
                $model->rollback();
                return $e->getMessage();
            }
        }
    }

    /**
     * 更新店铺的平台利益抽取字段
     * 
     * @param unknown $shop_id            
     * @param unknown $money            
     */
    private function updateShopAccountReturn($shop_id, $money)
    {
        $account_model = new NsShopAccountModel();
        $account_info = $account_model->get($shop_id);
        // 没有的话新建账户
        if (empty($account_info)) {
            $data = array(
                'shop_id' => $shop_id
            );
            $account_model->save($data);
            $account_info = $account_model->get($shop_id);
        }
        $data = array(
            "shop_platform_commission" => $account_info["shop_platform_commission"] + $money
        );
        $retval = $account_model->save($data, [
            'shop_id' => $shop_id
        ]);
        return $retval;
    }

    /**
     * 添加店铺的总的收益记录表
     * 
     * @param unknown $serial_no            
     * @param unknown $shop_id            
     * @param unknown $money            
     * @param unknown $account_type            
     * @param unknown $type_alis_id            
     * @param unknown $remark            
     */
    public function addShopAccountWithdrawRecords($serial_no, $shop_id, $money, $account_type, $type_alis_id, $remark)
    {
        $model = new NsShopAccountWithdrawRecordsModel();
        $records_list = $model->getInfo([
            "type_alis_id" => $type_alis_id,
            "account_type" => $account_type
        ], "shop_id");
        if (empty($records_list)) {
            $model->startTrans();
            try {
                $data = array(
                    'shop_id' => $shop_id,
                    'serial_no' => $serial_no,
                    'account_type' => $account_type,
                    'money' => $money,
                    'type_alis_id' => $type_alis_id,
                    'remark' => $remark,
                    'create_time' => time()
                );
                $records_id = $model->save($data);
                // 更新店铺提现金额
                $this->updateShopAccountWithdraw($shop_id, $money);
                // 添加店铺的整体资金流水
                $this->addShopAccountRecords(getSerialNo(), $shop_id, - $money, $account_type, $type_alis_id, $remark, "店铺资金提现。");
                $model->commit();
                return $model->id;
            } catch (\Exception $e) {
                $model->rollback();
                return $e->getMessage();
            }
        }
    }

    /**
     * 更新店铺的平台提现金额
     * 
     * @param unknown $shop_id            
     * @param unknown $money            
     */
    private function updateShopAccountWithdraw($shop_id, $money)
    {
        $account_model = new NsShopAccountModel();
        $account_info = $account_model->get($shop_id);
        // 没有的话新建账户
        if (empty($account_info)) {
            $data = array(
                'shop_id' => $shop_id
            );
            $account_model->save($data);
            $account_info = $account_model->get($shop_id);
        }
        $data = array(
            "shop_withdraw" => $account_info["shop_withdraw"] + $money
        );
        $retval = $account_model->save($data, [
            'shop_id' => $shop_id
        ]);
        return $retval;
    }

    /**
     * 添加店铺的整体的资金流水
     * 
     * @param unknown $serial_no            
     * @param unknown $shop_id            
     * @param unknown $money            
     * @param unknown $account_type            
     * @param unknown $type_alis_id            
     * @param unknown $remark            
     * @param unknown $title            
     */
    private function addShopAccountRecords($serial_no, $shop_id, $money, $account_type, $type_alis_id, $remark, $title)
    {
        $model = new NsShopAccountRecordsModel();
        $shop_account = $this->getShopAccount($shop_id);
        $data = array(
            'shop_id' => $shop_id,
            'serial_no' => $serial_no,
            'account_type' => $account_type,
            'money' => $money,
            'type_alis_id' => $type_alis_id,
            'balance' => $shop_account["shop_balance"],
            'remark' => $remark,
            'title' => $title,
            'create_time' => time()
        );
        $records_id = $model->save($data);
    }

    /**
     * 计算某个订单针对平台的利润 店铺独立分销
     *
     * @param unknown $order_id            
     * @param unknown $order_no            
     * @param unknown $shop_id            
     * @param unknown $real_pay            
     * @return unknown
     */
    public function addShopOrderAccountRecords_FX1($order_id, $order_no, $shop_id, $real_pay)
    {
        $shop_order_account_model = new NsShopOrderReturnModel();
        $order_goods_model = new NsOrderGoodsModel();
        $order_account_list = $shop_order_account_model->getInfo([
            "order_id" => $order_id
        ]);
        if (empty($order_account_list)) {
            $shop_order_account_model->startTrans();
            try {
                $rate = $this->getShopAccountRate(0, $shop_id);
                // 查询订单项的信息
                $condition["order_id"] = $order_id;
                $order_goods_list = $order_goods_model->getQuery($condition, '*', '');
                // 订单的实际付款金额
                $order_real_money = 0;
                // 订单抽取的总额
                $order_retuan_money = 0;
                if (! empty($order_goods_list) && $rate > 0) {
                    $order_goods_promotion = new NsOrderGoodsPromotionDetailsModel();
                    $order_goods_return_model = new NsShopOrderGoodsReturnModel();
                    foreach ($order_goods_list as $k => $order_goods) {
                        $promotion_money = $order_goods_promotion->where([
                            'order_id' => $order_id,
                            'sku_id' => $order_goods['sku_id']
                        ])->sum('discount_money');
                        if (empty($promotion_money)) {
                            $promotion_money = 0;
                        }
                        // 订单项的实际付款金额
                        $order_goods_real_money = $order_goods['goods_money'] + $order_goods['adjust_money'] - $order_goods['refund_real_money'] - $promotion_money;
                        // 累加订单的实际付款金额
                        $order_real_money = $order_real_money + $order_goods_real_money;
                        // 订单项的抽取的总额
                        $order_goods_return_money = $order_goods_real_money * $rate / 100;
                        // 计算订单的抽取总额
                        $order_retuan_money = $order_retuan_money + $order_goods_return_money;
                        $goods_data = array(
                            "shop_id" => $shop_id,
                            "order_id" => $order_id,
                            "order_goods_id" => $order_goods["order_goods_id"],
                            "goods_pay_money" => $order_goods_real_money,
                            "rate" => $rate,
                            "return_money" => $order_goods_return_money,
                            "create_time" => time()
                        );
                        $order_goods_return_model->save($goods_data);
                    }
                }
                $data = array(
                    "shop_id" => $shop_id,
                    "order_id" => $order_id,
                    "order_no" => $order_no,
                    "order_pay_money" => $order_real_money,
                    "platform_money" => $order_retuan_money,
                    "create_time" => time()
                );
                $accountid = $shop_order_account_model->save($data);
                $shop_order_account_model->commit();
            } catch (\Exception $e) {
                $shop_order_account_model->rollback();
            }
        }
    }

    /**
     * 订单退款 更新平台抽取金额 店铺独立分销版
     * 
     * @param unknown $order_id            
     * @param unknown $order_goods_id            
     * @param unknown $shop_id            
     */
    public function updateShopOrderGoodsReturnRecords_FX1($order_id, $order_goods_id, $shop_id)
    {
        $order_goods_promotion = new NsOrderGoodsPromotionDetailsModel();
        $order_goods_return_model = new NsShopOrderGoodsReturnModel();
        $order_goods_model = new NsOrderGoodsModel();
        $order_goods_count = $order_goods_return_model->getCount([
            "order_goods_id" => $order_goods_id
        ]);
        if ($order_goods_count > 0) {
            try {
                $order_goods_return_model->startTrans();
                // 平台的抽成比率
                $rate_obj = $order_goods_return_model->getInfo([
                    "order_goods_id" => $order_goods_id
                ], "rate");
                $rate = $rate_obj["rate"];
                // 得到订单项的基本信息
                $order_goods = $order_goods_model->get($order_goods_id);
                $promotion_money = $order_goods_promotion->where([
                    'order_id' => $order_id,
                    'sku_id' => $order_goods['sku_id']
                ])->sum('discount_money');
                if (empty($promotion_money)) {
                    $promotion_money = 0;
                }
                // 订单项的实际付款金额
                $order_goods_real_money = $order_goods['goods_money'] + $order_goods['adjust_money'] - $order_goods['refund_real_money'] - $promotion_money;
                // 订单项的抽取的总额
                $order_goods_return_money = $order_goods_real_money * $rate / 100;
                $goods_data = array(
                    "goods_pay_money" => $order_goods_real_money,
                    "return_money" => $order_goods_return_money
                );
                $order_goods_return_model->save($goods_data, [
                    "order_id" => $order_id,
                    "order_goods_id" => $order_goods_id
                ]);
                // 订单总支付金额
                $total_pay_money = $order_goods_return_model->getSum([
                    "order_id" => $order_id
                ], "goods_pay_money");
                // 订单总利润金额
                $total_return_money = $order_goods_return_model->getSum([
                    "order_id" => $order_id
                ], "return_money");
                $return_data = array(
                    "order_pay_money" => $total_pay_money,
                    "platform_money" => $total_return_money
                );
                $order_return_model = new NsShopOrderReturnModel();
                $order_return_model->save($return_data, [
                    "order_id" => $order_id
                ]);
                $order_goods_return_model->commit();
            } catch (\Exception $e) {
                $order_goods_return_model->rollback();
            }
        }
    }

    /**
     * 计算某个订单针对平台的利润 平台分销版
     * 
     * @param unknown $order_id            
     * @param unknown $order_no            
     * @param unknown $shop_id            
     * @param unknown $real_pay            
     */
    public function addShopOrderAccountRecords_FX2($order_id, $order_no, $shop_id, $real_pay)
    {
        $shop_order_account_model = new NsShopOrderReturnModel();
        $order_goods_model = new NsOrderGoodsModel();
        $order_account_list = $shop_order_account_model->getInfo([
            "order_id" => $order_id
        ]);
        if (empty($order_account_list)) {
            $shop_order_account_model->startTrans();
            try {
                //平台分销版  分成比率为0
                $rate = 0;
                // 查询订单项的信息
                $condition["order_id"] = $order_id;
                $order_goods_list = $order_goods_model->getQuery($condition, '*', '');
                // 订单的实际付款金额
                $order_real_money = 0;
                // 订单抽取的总额
                $order_retuan_money = 0;
                if (! empty($order_goods_list)) {
                    $order_goods_return_model = new NsShopOrderGoodsReturnModel();
                    foreach ($order_goods_list as $k => $order_goods) {
                        $order_goods_promotion = new NsOrderGoodsPromotionDetailsModel();
                        $promotion_money = $order_goods_promotion->where([
                            'order_id' => $order_id,
                            'sku_id' => $order_goods['sku_id']
                        ])->sum('discount_money');
                        if (empty($promotion_money)) {
                            $promotion_money = 0;
                        }
                        
                        // 订单项的实际付款金额
                        $order_goods_real_money = $order_goods['goods_money'] + $order_goods['adjust_money'] - $order_goods['refund_real_money'] - $promotion_money;
                        // 订单项的成本价                        
                        $order_goods_cost_price = $order_goods['cost_price']*$order_goods['num'];
                        // 累加订单的实际付款金额
                        $order_real_money = $order_real_money + $order_goods_real_money;
                        // 平台分销版 平台抽取金额为订单的总利润
                        $order_goods_return_money = $order_goods_real_money-$order_goods_cost_price;
                        if($order_goods_return_money<0){
                            $order_goods_return_money=0;
                        }
                        // 计算订单的抽取总额
                        $order_retuan_money = $order_retuan_money + $order_goods_return_money;
                        $goods_data = array(
                            "shop_id" => $shop_id,
                            "order_id" => $order_id,
                            "order_goods_id" => $order_goods["order_goods_id"],
                            "goods_pay_money" => $order_goods_real_money,
                            "rate" => $rate,
                            "return_money" => $order_goods_return_money,
                            "create_time" => time()
                        );
                        $order_goods_return_model->save($goods_data);
                    }
                }
                $data = array(
                    "shop_id" => $shop_id,
                    "order_id" => $order_id,
                    "order_no" => $order_no,
                    "order_pay_money" => $order_real_money,
                    "platform_money" => $order_retuan_money,
                    "create_time" => time()
                );
                $accountid = $shop_order_account_model->save($data);
                $shop_order_account_model->commit();
            } catch (\Exception $e) {
                $shop_order_account_model->rollback();
            }
        }
    }

    /**
     * 订单退款 更新平台抽取金额 店铺独立分销
     * @param unknown $order_id
     * @param unknown $order_goods_id
     * @param unknown $shop_id
     */
    public function updateShopOrderGoodsReturnRecords_FX2($order_id, $order_goods_id, $shop_id)
    {
        $order_goods_promotion = new NsOrderGoodsPromotionDetailsModel();
        $order_goods_return_model = new NsShopOrderGoodsReturnModel();
        $order_goods_model = new NsOrderGoodsModel();
        $order_goods_count = $order_goods_return_model->getCount([
            "order_goods_id" => $order_goods_id
        ]);
        if ($order_goods_count > 0) {
            try {
                $order_goods_return_model->startTrans();
                // 平台的抽成比率
                $rate_obj = $order_goods_return_model->getInfo([
                    "order_goods_id" => $order_goods_id
                ], "rate");
                $rate = $rate_obj["rate"];
                // 得到订单项的基本信息
                $order_goods = $order_goods_model->get($order_goods_id);
                $promotion_money = $order_goods_promotion->where([
                    'order_id' => $order_id,
                    'sku_id' => $order_goods['sku_id']
                ])->sum('discount_money');
                if (empty($promotion_money)) {
                    $promotion_money = 0;
                }
                // 订单项的实际付款金额
                $order_goods_real_money = $order_goods['goods_money'] + $order_goods['adjust_money'] - $order_goods['refund_real_money'] - $promotion_money;
                // 订单项的成本价
                $order_goods_cost_price = $order_goods['cost_price']*$order_goods['num'];
                // 订单项的抽取的总额
                $order_goods_return_money = $order_goods_real_money-$order_goods_cost_price;
                if($order_goods_return_money<0){
                    $order_goods_return_money=0;
                }
                $goods_data = array(
                    "goods_pay_money" => $order_goods_real_money,
                    "return_money" => $order_goods_return_money
                );
                $order_goods_return_model->save($goods_data, [
                    "order_id" => $order_id,
                    "order_goods_id" => $order_goods_id
                ]);
                // 订单总支付金额
                $total_pay_money = $order_goods_return_model->getSum([
                    "order_id" => $order_id
                ], "goods_pay_money");
                // 订单总利润金额
                $total_return_money = $order_goods_return_model->getSum([
                    "order_id" => $order_id
                ], "return_money");
                $return_data = array(
                    "order_pay_money" => $total_pay_money,
                    "platform_money" => $total_return_money
                );
                $order_return_model = new NsShopOrderReturnModel();
                $order_return_model->save($return_data, [
                    "order_id" => $order_id
                ]);
                $order_goods_return_model->commit();
            } catch (\Exception $e) {
                $order_goods_return_model->rollback();
            }
        }
    }

    /**
     * 更新平台抽取店铺的利润 发放
     * 
     * @param unknown $order_id            
     */
    public function updateShopOrderAccountRecord($order_id)
    {
        $order_return_model = new NsShopOrderReturnModel();
        $order_return_obj = $order_return_model->getInfo([
            "order_id" => $order_id
        ]);
        if (! empty($order_return_obj)) {
            $order_return_model->startTrans();
            try {
                $order_retuan_money = $order_return_obj["platform_money"];
                $real_pay = $order_return_obj["order_pay_money"];
                $shop_id = $order_return_obj["shop_id"];
                if ($order_retuan_money > 0) {
                    $this->addShopAccountReturnRecords(getSerialNo(), $shop_id, $order_retuan_money, 1, $order_id, "店铺订单完成，订单金额：" . $real_pay . "元, 平台抽取" . $order_retuan_money . "。");
                }
                $order_return_model->save([
                    "is_issue" => 1
                ], [
                    "order_id" => $order_id
                ]);
                $order_return_model->commit();
            } catch (\Exception $e) {
                $order_return_model->rollback();
            }
        }
    }

    /**
     * 得到订单项的的对平台的提成比率
     * 
     * @param unknown $shop_id            
     */
    private function getShopAccountRate($order_goods_id, $shop_id)
    {
        $shop_model = new NsShopModel();
        // 得到店铺的信息
        $shop_obj = $shop_model->get($shop_id);
        if (empty($shop_obj)) {
            return 0;
        } else {
            return $shop_obj["shop_platform_commission_rate"];
        }
    }

    /**
     * 得到店铺的账户情况
     * 
     * @param unknown $shop_id            
     * @return \think\static
     */
    public function getShopAccount($shop_id)
    {
        // TODO Auto-generated method stub
        $shop_account = new NsShopAccountModel();
        $account_obj = $shop_account->get($shop_id);
        if (empty($account_obj)) {
            // 默认添加
            $data = array(
                "shop_id" => $shop_id
            );
            $shop_account->save($data);
            $account_obj = $shop_account->get($shop_id);
        }
        // 店铺收益总额
        $shop_proceeds = $account_obj["shop_proceeds"];
        // 平台抽取利润总额
        $shop_platform_commission = $account_obj["shop_platform_commission"];
        // 店铺提现总额
        $shop_withdraw = $account_obj["shop_withdraw"];
        // 店铺可用总额
        $shop_balance = $shop_proceeds - $shop_platform_commission - $shop_withdraw;
        $account_obj["shop_balance"] = $shop_balance;
        return $account_obj;
    }

    /**
     * **************************************************店铺账户计算--End****************************************************************
     */
    
    /**
     * **************************************************平台账户--Start****************************************************************
     */
    /**
     * 添加平台的订单入帐记录
     * 
     * @param unknown $shop_id            
     * @param unknown $money            
     * @param unknown $account_type            
     * @param unknown $type_alis_id            
     * @param unknown $remark            
     */
    public function addAccountOrderRecords($shop_id, $money, $account_type, $type_alis_id, $remark)
    {
        $order_model = new NsAccountOrderRecordsModel();
        $order_model->startTrans();
        try {
            $data = array(
                'serial_no' => getSerialNo(),
                'shop_id' => $shop_id,
                'money' => $money,
                'account_type' => $account_type,
                'type_alis_id' => $type_alis_id,
                'create_time' => time(),
                'remark' => $remark
            );
            $order_model->save($data);
            // 更新订单的总金额字段
            $this->updateAccountOrderMoney($money);
            // 添加平台的整体资金流水
            $this->addAccountRecords($shop_id, 0, "商场订单支付成功!", $money, 1, $type_alis_id, "商场订单在线支付!");
            $order_model->commit();
        } catch (\Exception $e) {
            $order_model->rollback();
        }
    }

    /**
     * 更新平台账户的订单总额
     * 
     * @param unknown $money            
     */
    private function updateAccountOrderMoney($money)
    {
        $account_model = new NsAccountModel();
        $account_obj = $account_model->getInfo([
            'account_id' => 1
        ]);
        $data = array(
            "account_order_money" => $account_obj["account_order_money"] + $money
        );
        $account_model->save($data, [
            'account_id' => 1
        ]);
    }

    /**
     * 添加平台抽取利润的记录
     * 
     * @param unknown $shop_id            
     * @param unknown $money            
     * @param unknown $account_type            
     * @param unknown $type_alis_id            
     * @param unknown $remark            
     */
    public function addAccountReturnRecords($shop_id, $money, $account_type, $type_alis_id, $remark)
    {
        $return_model = new NsAccountReturnRecordsModel();
        $return_model->startTrans();
        try {
            $data = array(
                'serial_no' => getSerialNo(),
                'shop_id' => $shop_id,
                'money' => $money,
                'account_type' => $account_type,
                'type_alis_id' => $type_alis_id,
                'create_time' => time(),
                'remark' => $remark
            );
            $return_model->save($data);
            // 更新平台抽取利润的总额
            $this->updateAccountReturn($money);
            $return_model->commit();
        } catch (\Exception $e) {
            $return_model->rollback();
        }
    }

    /**
     * 更新平台的抽取例利润的总额
     * 
     * @param unknown $money            
     */
    private function updateAccountReturn($money)
    {
        $account_model = new NsAccountModel();
        $account_obj = $account_model->getInfo([
            'account_id' => 1
        ]);
        $data = array(
            "account_return" => $account_obj["account_return"] + $money
        );
        $account_model->save($data, [
            'account_id' => 1
        ]);
    }

    /**
     * 添加平台 店铺提现的记录
     * 
     * @param unknown $shop_id            
     * @param unknown $money            
     * @param unknown $account_type            
     * @param unknown $type_alis_id            
     * @param unknown $remark            
     */
    public function addAccountWithdrawRecords($shop_id, $money, $account_type, $type_alis_id, $remark)
    {
        $withdraw_model = new NsAccountWithdrawRecordsModel();
        $withdraw_model->startTrans();
        try {
            $data = array(
                'serial_no' => getSerialNo(),
                'shop_id' => $shop_id,
                'money' => $money,
                'account_type' => $account_type,
                'type_alis_id' => $type_alis_id,
                'create_time' => time(),
                'remark' => $remark
            );
            $withdraw_model->save($data);
            // 更新提现总额的字段
            $this->updateAccountWithdraw($money);
            // 添加平台的整体资金流水
            $this->addAccountRecords($shop_id, 0, "商场提现成功!", - $money, 2, $type_alis_id, "商场申请提现，平台审核通过!");
            $withdraw_model->commit();
        } catch (\Exception $e) {
            $withdraw_model->rollback();
        }
    }

    /**
     * 更新店铺在平台端的提现字段
     * 
     * @param unknown $money            
     */
    private function updateAccountWithdraw($money)
    {
        $account_model = new NsAccountModel();
        $account_obj = $account_model->getInfo([
            'account_id' => 1
        ]);
        $data = array(
            "account_withdraw" => $account_obj["account_withdraw"] + $money
        );
        $account_model->save($data, [
            'account_id' => 1
        ]);
    }

    /**
     * 招商员支付金额记录添加
     * 
     * @param unknown $shop_id            
     * @param unknown $money            
     * @param unknown $account_type            
     * @param unknown $type_alis_id            
     * @param unknown $remark            
     */
    public function addAccountAssistantRecords($shop_id, $money, $account_type, $type_alis_id, $remark)
    {
        $assistant_model = new NsAccountAssistantRecordsModel();
        $assistant_model->startTrans();
        try {
            $data = array(
                'serial_no' => getSerialNo(),
                'shop_id' => $shop_id,
                'money' => $money,
                'account_type' => $account_type,
                'type_alis_id' => $type_alis_id,
                'create_time' => time(),
                'remark' => $remark
            );
            $assistant_model->save($data);
            // 更新提现总额的字段
            $this->updateAccountAssistant($money);
            // 添加平台的整体资金流水
            $this->addAccountRecords($shop_id, 0, "会员支付申请招商员!", $money, 3, $type_alis_id, "会员支付金额，申请成为招商员!");
            $assistant_model->commit();
        } catch (\Exception $e) {
            $assistant_model->rollback();
        }
    }

    /**
     * 更新平台的招商员支付金额
     * 
     * @param unknown $money            
     */
    private function updateAccountAssistant($money)
    {
        $account_model = new NsAccountModel();
        $account_obj = $account_model->getInfo([
            'account_id' => 1
        ]);
        $data = array(
            "account_assistant" => $account_obj["account_assistant"] + $money
        );
        $account_model->save($data, [
            'account_id' => 1
        ]);
    }

    /**
     * 针对平台 会员的提现金额
     * 
     * @param unknown $shop_id            
     * @param unknown $money            
     * @param unknown $account_type            
     * @param unknown $type_alis_id            
     * @param unknown $remark            
     */
    public function addAccountWithdrawUserRecords($shop_id, $money, $account_type, $type_alis_id, $remark)
    {
        $withdraw_model = new NsAccountWithdrawUserRecordsModel();
        $withdraw_model->startTrans();
        try {
            $data = array(
                'serial_no' => getSerialNo(),
                'shop_id' => $shop_id,
                'money' => $money,
                'account_type' => $account_type,
                'type_alis_id' => $type_alis_id,
                'create_time' => time(),
                'remark' => $remark
            );
            $withdraw_model->save($data);
            // 更新提现总额的字段
            $this->updateAccountUserWithdraw($money);
            // 添加平台的整体资金流水
            $this->addAccountRecords($shop_id, 0, "会员提现成功!", - $money, 4, $type_alis_id, "会员提现申请提现，平台审核通过!");
            $withdraw_model->commit();
        } catch (\Exception $e) {
            $withdraw_model->rollback();
        }
    }

    /**
     * 更新平台的 会员提现金额
     * 
     * @param unknown $money            
     */
    private function updateAccountUserWithdraw($money)
    {
        $account_model = new NsAccountModel();
        $account_obj = $account_model->getInfo([
            'account_id' => 1
        ]);
        $data = array(
            "account_user_withdraw" => $account_obj["account_user_withdraw"] + $money
        );
        $account_model->save($data, [
            'account_id' => 1
        ]);
    }

    /**
     * 添加平台的整体资金流水
     * 
     * @param unknown $shop_id            
     * @param unknown $user_id            
     * @param unknown $title            
     * @param unknown $money            
     * @param unknown $account_type            
     * @param unknown $type_alis_id
     *            1订单支付 2店铺提现 3招商员支付
     * @param unknown $remark            
     */
    private function addAccountRecords($shop_id, $user_id, $title, $money, $account_type, $type_alis_id, $remark)
    {
        $account_model = new NsAccountRecordsModel();
        $plat_obj = $this->getPlatformAccount();
        $balance = $plat_obj["balance"];
        $data = array(
            "serial_no" => getSerialNo(),
            "shop_id" => $shop_id,
            "user_id" => $user_id,
            "title" => $title,
            "money" => $money,
            "account_type" => $account_type,
            "type_alis_id" => $type_alis_id,
            "balance" => $balance,
            "create_time" => time(),
            "remark" => $remark
        );
        $account_model->save($data);
    }

    /**
     * 查询平台账户的资金情况
     * 
     * @return unknown
     */
    public function getPlatformAccount()
    {
        $plat_model = new NsAccountModel();
        $plat_obj = $plat_model->getInfo([
            "account_id" => 1
        ]);
        $balance = $plat_obj["account_order_money"] + $plat_obj["account_deposit"] + $plat_obj["account_assistant"] - $plat_obj["account_withdraw"] - $plat_obj["account_user_withdraw"];
        $plat_obj["balance"] = $balance;
        return $plat_obj;
    }

    /**
     * **************************************************平台账户--End****************************************************************
     */
    
    /**
     * 添加店铺购物币记录
     * 
     * @param unknown $shop_id            
     * @param unknown $account_type
     *            1. 充值 2. 赠送会员
     * @param unknown $num            
     * @param unknown $type_alis_id            
     * @param unknown $remark            
     */
    public function addShopCoinAccountRecords($shop_id, $account_type, $num, $type_alis_id, $remark)
    {
        $shop_coin_records = new NsShopCoinRecordsModel();
        $shop_coin_records->startTrans();
        try {
            $data = array(
                'shop_id' => $shop_id,
                'num' => $num,
                'account_type' => $account_type,
                'type_alis_id' => $type_alis_id,
                'text' => $remark,
                'create_time' => time()
            );
            $shop_coin_records->save($data);
            $shop_account = new NsShopAccountModel();
            $all_coin = $shop_account->getInfo([
                'shop_id' => $shop_id
            ], 'shop_coin_account');
            $data_coin = array(
                'shop_coin_account' => $all_coin + $num
            );
            $shop_account->save($data_coin, [
                'shop_id' => $shop_id
            ]);
            return $shop_coin_records->id;
        } catch (\Exception $e) {
            $shop_coin_records->rollback();
            return $e->getMessage();
        }
    }
}