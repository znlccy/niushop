<?php
/**
 * IAlbum.php
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
 * 相册以及图片接口
 */
interface IAlbum
{

    /**
     * 获取相册列表
     * 
     * @param number $page_index            
     * @param number $page_size            
     * @param string $condition            
     * @param string $order            
     * @param string $field            
     */
    function getAlbumClassList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*');

    /**
     * 创建相册
     * 
     * @param unknown $aclass_name            
     * @param unknown $aclass_sort            
     * @param number $pid            
     * @param string $aclass_cover            
     * @param number $is_default            
     * @param number $instance_id            
     */
    function addAlbumClass($aclass_name, $aclass_sort, $pid = 0, $aclass_cover = '', $is_default = 0, $instance_id = 1);

    /**
     * 编辑相册
     * 
     * @param unknown $aclass_name            
     * @param unknown $aclass_sort            
     * @param number $pid            
     * @param string $aclass_cover            
     * @param number $is_default            
     */
    function updateAlbumClass($aclass_id, $aclass_name, $aclass_sort, $pid = 0, $aclass_cover = '', $is_default = 0);

    /**
     * 改变相册排序
     * 
     * @param unknown $aclass_id            
     * @param unknown $aclass_sort            
     */
    function ModifyAlbumSort($aclass_id, $aclass_sort);

    /**
     * 改变相册上级
     * 
     * @param unknown $aclass_id            
     * @param unknown $pid            
     */
    function ModifyAlbumPid($aclass_id, $pid);

    /**
     * 删除相册
     * 
     * @param unknown $aclass_id            
     */
    function deleteAlbumClass($aclass_id_arrray);

    /**
     * 获取相册图片列表
     * 
     * @param number $page_index            
     * @param number $page_size            
     * @param string $condition            
     * @param string $order            
     * @param string $field            
     */
    function getPictureList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*');

    /**
     * 图片增加
     * 
     * @param unknown $pic_name
     *            名称
     * @param unknown $pic_tag
     *            标签
     * @param unknown $aclass_id
     *            相册ID
     * @param unknown $pic_cover
     *            图片路径
     * @param unknown $pic_size
     *            大小
     * @param unknown $pic_spec
     *            规格
     * @param unknown $pic_cover_big            
     * @param unknown $pic_size_big            
     * @param unknown $pic_spec_big            
     * @param unknown $pic_cover_mid            
     * @param unknown $pic_size_mid            
     * @param unknown $pic_spec_mid            
     * @param unknown $pic_cover_small            
     * @param unknown $pic_size_small            
     * @param unknown $pic_spec_small            
     * @param unknown $pic_cover_micro            
     * @param unknown $pic_size_micro            
     * @param unknown $pic_spec_micro            
     * @param unknown $instance_id            
     */
    function addPicture($pic_name, $pic_tag, $aclass_id, $pic_cover, $pic_size, $pic_spec, $pic_cover_big, $pic_size_big, $pic_spec_big, $pic_cover_mid, $pic_size_mid, $pic_spec_mid, $pic_cover_small, $pic_size_small, $pic_spec_small, $pic_cover_micro, $pic_size_micro, $pic_spec_micro, $instance_id = 0, $upload_type, $domain, $bucket);

    /**
     * 图片删除
     * 
     * @param unknown $pic_id            
     */
    function deletePicture($pic_id_array);

    /**
     * 获取相册详情
     * 
     * @param unknown $album_id            
     */
    function getAlbumClassDetail($album_id);

    /**
     * 获取图片详情
     * 
     * @param unknown $pic_id            
     */
    function getAlbumDetail($pic_id);

    /**
     * 图片替换
     * 
     * @param unknown $pic_id            
     * @param unknown $pic_name            
     * @param unknown $pic_tag            
     * @param unknown $aclass_id            
     * @param unknown $pic_cover            
     * @param unknown $pic_size            
     * @param unknown $pic_spec            
     * @param unknown $pic_cover_big            
     * @param unknown $pic_size_big            
     * @param unknown $pic_spec_big            
     * @param unknown $pic_cover_mid            
     * @param unknown $pic_size_mid            
     * @param unknown $pic_spec_mid            
     * @param unknown $pic_cover_small            
     * @param unknown $pic_size_small            
     * @param unknown $pic_spec_small            
     * @param unknown $pic_cover_micro            
     * @param unknown $pic_size_micro            
     * @param unknown $pic_spec_micro            
     * @param number $instance_id            
     */
    function ModifyAlbumPicture($pic_id, $pic_cover, $pic_size, $pic_spec, $pic_cover_big, $pic_size_big, $pic_spec_big, $pic_cover_mid, $pic_size_mid, $pic_spec_mid, $pic_cover_small, $pic_size_small, $pic_spec_small, $pic_cover_micro, $pic_size_micro, $pic_spec_micro, $instance_id, $upload_type = 1, $domain, $bucket);

    /**
     * 图片名称修改
     * 
     * @param unknown $pic_id            
     * @param unknown $pic_name            
     */
    function ModifyAlbumPictureName($pic_id, $pic_name);

    /**
     * 更改图片所在相册
     * 
     * @param unknown $pic_id            
     * @param unknown $album_id            
     */
    function ModifyAlbumPictureClass($pic_id, $album_id);

    /**
     * 设此图片为本相册的封面
     * 
     * @param unknown $pic_id            
     * @param unknown $album_id            
     */
    function ModifyAlbumClassCover($pic_id, $album_id);

    /**
     * 获取商品使用的图片空间
     */
    function getGoodsUseAlbum();

    /**
     * 判断图片是否已经被使用
     * return true = 已被使用 false = 未使用
     */
    function checkPictureIsUse($shop_id, $pic_id);

    /**
     * 获取相册图片详情
     * 
     * @param unknown $condition            
     */
    function getAlubmPictureDetail($condition);

    function getGoodsAlbumUsePictureQuery($condition);

    /**
     * 获取默认相册详情
     */
    function getDefaultAlbumDetail();
}