<?php
/**
 * IWeixinMessage.php
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
 * 微信消息接口       
 */
interface IWeixinMessage
{
    /**
     * 获取微信模板消息
     */
    function getWeixinInstanceMsg($instance_id);
    /**
     * 更新微信模板消息内容
     * @param unknown $instance_id
     */
    function updateWeixinInstanceMessage($instance_id);
    /**
     * 获取微信消息模板
     */
    function getWeixinMsgTemplate();
    /**
     * 发送订单提交创建消息
     * @param unknown $order_id
     */
    function sendWeixinOrderCreateMessage($order_id);
    /**
     * 发送订单支付消息
     * @param unknown $order_id
     */
    function sendWeixinOrderPayMessage($order_id);
    /**
     * 发送订单退款申请
     * @param unknown $order_id
     * @param unknown $order_goods_id
     */
    function sendWeixinOrderRefundApply($order_id, $order_goods_id);
    /**
     * 发送订单退款结果通知
     * @param unknown $order_id
     * @param unknown $order_goods_id
     */
    function sendWeixinOrderRefundMessage($order_id, $order_goods_id);
    /**
     * 发送订单发货通知
     * @param unknown $order_id
     */
    function sendWeixinOrderDeliverMessage($order_id);
    /**
     * 给用户发送消息
     * @param unknown $openid
     */
    function sendMessageToUser($openid, $msg_type, $content);
}