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
 * 虚拟商品接口
 * 创建时间：2017年11月20日 19:31:30 王永杰
 */
interface IVirtualGoods
{

    /**
     * 编辑虚拟商品分组表
     *
     * @param 虚拟商品分组名称 $virtual_goods_group_name            
     * @param 接口调用地址（JSON） $interfaces            
     * @param 创建时间 $create_time            
     */
    function editVirtualGoodsGroup($virtual_goods_group_name, $interfaces, $create_time);

    /**
     * 获取虚拟商品类型列表
     *
     * @param 当前页 $page_index            
     * @param 显示页数 $page_size            
     * @param 条件 $condition            
     * @param 排序 $order            
     * @param 字段 $field            
     */
    function getVirtualGoodsTypeList($page_index, $page_size, $condition, $order, $field);

    /**
     * 根据id查询虚拟商品类型
     *
     * @param unknown $virtual_goods_type_id            
     */
    function getVirtualGoodsTypeById($virtual_goods_type_id);

    /**
     * 编辑虚拟商品类型
     *
     * @param 虚拟商品类型id，0表示添加 $virtual_goods_type_id            
     * @param 关联虚拟商品分组id $virtual_goods_group_id            
     * @param 虚拟商品类型名称 $virtual_goods_type_name            
     * @param 有效期/天(0表示不限制) $validity_period            
     * @param 是否启用（禁用后要查询关联的虚拟商品给予弹出确认提示框） $is_enabled            
     * @param 金额 $money            
     * @param 配置信息(API接口、参数等) $config_info            
     * @param 限制使用次数 $confine_use_number            
     */
    function editVirtualGoodsType($virtual_goods_type_id, $virtual_goods_group_id, $virtual_goods_type_name, $validity_period, $is_enabled, $money, $config_info, $confine_use_number);

    /**
     * 设置虚拟商品类型是否启用（禁用后要查询关联的虚拟商品给予弹出确认提示框，确认后将商品下架）
     *
     * @param 虚拟商品类型id $virtual_goods_type_id            
     * @param 是否启用0/1 $is_enabled            
     */
    function setVirtualGoodsTypeIsEnabled($virtual_goods_type_id, $is_enabled);

    /**
     * 根据id删除虚拟商品类型
     *
     * @param 虚拟商品类型id $virtual_goods_type_id            
     */
    function deleteVirtualGoodsType($virtual_goods_type_id);

    /**
     * 添加虚拟商品
     *
     * @param 虚拟码 $virtual_code            
     * @param 虚拟商品名称 $virtual_goods_name            
     * @param 金额 $money            
     * @param 买家id $buyer_id            
     * @param 买家昵称 $buyer_nickname            
     * @param 关联订单项id $order_goods_id            
     * @param 订单编号 $order_no            
     * @param 有效期/天(0表示不限制) $validity_period            
     * @param 有效期开始时间 $start_time            
     * @param 有效期结束时间 $end_time            
     * @param 使用次数 $use_number            
     * @param 限制使用次数 $confine_use_number            
     * @param 使用状态 $use_status            
     * @param 创建时间 $create_time            
     */
    function addVirtualGoods($shop_id, $virtual_goods_name, $money, $buyer_id, $buyer_nickname, $order_goods_id, $order_no, $validity_period, $start_time, $end_time, $use_number, $confine_use_number, $use_status);

    /**
     * 根据虚拟码删除虚拟商品
     *
     * @param 虚拟码 $virtual_code            
     */
    function deleteVirtualGoods($virtual_code);

    /**
     * 根据订单编号查询虚拟商品列表
     * 
     * @param unknown $order_no            
     */
    function getVirtualGoodsListByOrderNo($order_no);
}