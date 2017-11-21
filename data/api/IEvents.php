<?php
/**
 * IEvents.php
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
 * 计划任务接口
 *
 */
interface IEvents{
    /**
     * 订单长时间未付款自动交易关闭
     */
    function ordersClose();
    /**
     * 订单收货后7天自动交易完成
     */
    function ordersComplete();
    /**
     * 满减送超过期限自动关闭, 进入时间自动开始
     */
    function mansongOperation();
    /**
     * 赠品超过有效期限自动取消
     */
    function giftClose();
    /**
     * 限时折扣自动开始以及自动关闭
     */
    function discountOperation();
    /**
     * 自动收货
     */
    function autoDeilvery();
    /**
     * 优惠券自动过期
     */
    function autoCouponClose();
}