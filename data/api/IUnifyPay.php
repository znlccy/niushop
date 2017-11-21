<?php
/**
 * IUnifyPay.php
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
 * 统一支付接口
 *
 */
interface IUnifyPay{

    /**
     * 创建订单支付编号
     * @param unknown $order_id
     */
    function createOutTradeNo();
    /**
     * 创建待支付单据
     * @param unknown $pay_no
     * @param unknown $pay_body
     * @param unknown $pay_detail
     * @param unknown $pay_money
     * @param unknown $type  订单类型  1. 商城订单  2.
     * @param unknown $pay_money
     */
    function createPayment($shop_id, $out_trade_no, $pay_body, $pay_detail, $pay_money, $type, $type_alis_id);
    
    /**
     * 根据支付编号修改待支付单据
     * @param unknown $out_trade_no
     * @param unknown $shop_id
     * @param unknown $pay_body
     * @param unknown $pay_detail
     * @param unknown $pay_money
     * @param unknown $type 订单类型  1. 商城订单  2.
     * @param unknown $type_alis_id
     */
    function updatePayment($out_trade_no,$shop_id, $pay_body, $pay_detail, $pay_money, $type, $type_alis_id);
    
    /**
     * 删除待支付单据 
     * @param unknown $out_trade_no
     */
    function delPayment($out_trade_no);
    
    /**
     * 线上支付主动根据支付方式执行支付成功的通知
     * @param unknown $out_trade_no
     */
    function onlinePay($out_trade_no, $pay_type, $trade_no);
    
    /**
     * 只是执行单据支付，不进行任何处理用于执行支付后被动调用
     * @param unknown $out_trade_no
     * @param unknown $pay_type
     */
    function offLinePay($out_trade_no, $pay_type);
    /**
     * 获取支付信息
     * @param unknown $out_trade_no
     */
    function getPayInfo($out_trade_no);
    /**
     * 重新设置编号，用于修改价格订单
     * @param unknown $out_trade_no
     * @param unknown $new_no
     * @return Ambigous <number, \think\false, boolean, string>
     */
    function modifyNo($out_trade_no, $new_no);
    /**
     * 修改支付价格
     * @param unknown $out_trade_no
     */
    function modifyPayMoney($out_trade_no, $pay_money);
    /**
     * 执行微信支付
     * @param unknown $out_trade_no
     * @param unknown $trade_type
     * @param unknown $red_url
     */
    function wchatPay($out_trade_no, $trade_type, $red_url);
    /**
     * 执行支付宝支付
     * @param unknown $out_trade_no
     * @param unknown $notify_url
     * @param unknown $return_url
     * @param unknown $show_url
     */
    function aliPay($out_trade_no, $notify_url, $return_url, $show_url);
    /**
     * 获取微信jsapi
     * @param unknown $UnifiedOrderResult
     */
    function getWxJsApi($UnifiedOrderResult);
    /**
     * 获取支付宝配置参数是否正确,支付成功后使用
     */
    function getVerifyResult($type);
    /**
     * 获取支付配置
     */
    function getPayConfig();
    
}