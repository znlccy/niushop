<?php
/**
 * IGoodsGroup.php
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
 * 商品店铺分组管理（用于前台显示）
 */
interface IGoodsGroup
{
    /**
     * 获取商品分组列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
     */
    function getGoodsGroupList($page_index=1, $page_size=0, $condition = '', $order = '', $field = '*');
    
    /**
     * 添加或修改商品分组
     *  group_id int(11) NOT NULL AUTO_INCREMENT,
          shop_id int(11) NOT NULL,
          group_name varchar(50) NOT NULL DEFAULT '' COMMENT '分类名称',
          pid int(11) NOT NULL DEFAULT 0 COMMENT '上级id 最多2级',
          level tinyint(4) NOT NULL DEFAULT 0 COMMENT '级别',
          is_visible bit(1) NOT NULL DEFAULT b'1' COMMENT '是否可视',
          sort tinyint(4) NOT NULL DEFAULT 0 COMMENT '排序',
     */
    function addOrEditGoodsGroup($group_id, $shop_id, $group_name, $pid, $is_visible, $sort, $group_pic);
    
    /**
     * 删除商品分组
     * @param unknown $goods_group_id_array
     */
    function deleteGoodsGroup($goods_group_id_array, $shop_id);
	
	/**
	 * 获取商品分组的子分类(一级)
	 */
	function getGoodsGroupListByParentId($shop_id, $pid);
	/**
	 * 获取分组详情
	 * @param unknown $group_id
	 */
	function getGoodsGroupDetail($group_id);
	/**
	 * 修改 商品分组 单个字段
	 */
	function ModifyGoodsGroupField($group_id, $field_name, $field_value);
	/**
	 * 返回二级 分组 列表
	 * @param unknown $shop_id
	 */
	function getGoodsGroupQuery($shop_id);
	/**
	 * 查询分组商品列表数据结构
	 * @param unknown $shop_id
	 */
	function getGroupGoodsTree($shop_id);
	/**
	 *查询商品分组
	 * @param unknown $condition
	 */
	function getGoodsGroupQueryList($condition);
}