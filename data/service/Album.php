<?php
/**
 * Album.php
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
 * @date : 2015.4.24
 * @version : v1.0.0.0
 */
namespace data\service;

/**
 * 相册以及图片业务层
 */
use data\service\BaseService as BaseService;
use data\api\IAlbum as IAlbum;
use data\model\AlbumClassModel as AlbumClassModel;
use data\model\AlbumPictureModel as AlbumPictureModel;
use data\model\NsGoodsModel;
use data\service\Goods;
use data\model\NsGoodsDeletedModel;

class Album extends BaseService implements IAlbum
{

    public $album_class;

    public $album_picture;

    function __construct()
    {
        parent::__construct();
        $this->album_class = new AlbumClassModel();
        $this->album_picture = new AlbumPictureModel();
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IAlbumClass::getAlbumClassList()
     */
    public function getAlbumClassList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $album_class = new AlbumClassModel();
        $list = $album_class->pageQuery($page_index, $page_size, $condition, $order, $field);
        if (! empty($list['data'])) {
            foreach ($list['data'] as $k => $v) {
                // 查询相册图片数量
                $count = $this->getAlbumPictureCount($v['album_id']);
                $list['data'][$k]['pic_count'] = $count;
                // 查询相册背景图片
                $album_picture = new AlbumPictureModel();
                $pic_cover = "";
                if ($v["album_cover"]) {
                    $pic_info = $album_picture->getInfo([
                        'album_id' => $v['album_id'],
                        "pic_id" => $v["album_cover"]
                    ], 'pic_cover,upload_type,domain');
                    if (! empty($pic_info)) {
                        $pic_cover = $pic_info["pic_cover"];
                    }
                    $list['data'][$k]['pic_info'] = $pic_info;
                    $list['data'][$k]["pic_album_cover"] = $pic_cover;
                }
            }
        }
        return $list;
    }

    /**
     * 查询相册图片数
     *
     * @param unknown $album_id            
     */
    private function getAlbumPictureCount($album_id)
    {
        $album_picture = new AlbumPictureModel();
        $count = $album_picture->where('album_id=' . $album_id)->count();
        return $count;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IAlbumClass::addAlbumClass()
     */
    public function addAlbumClass($aclass_name, $aclass_sort, $pid = 0, $aclass_cover = '', $is_default = 0, $instance_id = 1)
    {
        $album_class = new AlbumClassModel();
        $data = array(
            'album_name' => $aclass_name,
            'sort' => $aclass_sort,
            'album_cover' => $aclass_cover,
            'is_default' => $is_default,
            'shop_id' => $instance_id,
            'create_time' => time(),
            'pid' => $pid
        );
        $retval = $album_class->save($data);
        if ($retval == 1) {
            $data['album_id'] = $album_class->album_id;
            hook("albumSaveSuccess", $data);
            return $album_class->album_id;
        } else {
            return $retval;
        }
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IAlbumClass::updateAlbumClass()
     */
    public function updateAlbumClass($aclass_id, $aclass_name, $aclass_sort, $pid = 0, $aclass_cover = '', $is_default = 0)
    {
        $album_class = new AlbumClassModel();
        $data = array(
            'album_name' => $aclass_name,
            'sort' => $aclass_sort,
            "album_cover" => $aclass_cover
        );
        $retval = $album_class->save($data, [
            'album_id' => $aclass_id
        ]);
        $data['album_id'] = $aclass_id;
        hook("albumSaveSuccess", $data);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IAlbumClass::ModifyAlbumSort()
     */
    public function ModifyAlbumSort($aclass_id, $aclass_sort)
    {
        $album_class = new AlbumClassModel();
        $data = array();
        
        $retval = $album_class->save($data, [
            'aclass_id' => $aclass_id
        ]);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IAlbumClass::ModifyAlbumPid()
     */
    public function ModifyAlbumPid($aclass_id, $pid)
    {
        $album_class = new AlbumClassModel();
        $data = array();
        
        $res = $this->album_class->save($data, [
            'aclass_id' => $aclass_id
        ]);
        return $res;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IAlbumClass::deleteAlbumClass()
     */
    public function deleteAlbumClass($aclass_id_array)
    {
        $this->album_class->startTrans();
        try {
            $shop_id = $this->instance_id;
            $condition = array(
                'shop_id' => $shop_id,
                'album_id' => array(
                    'in',
                    $aclass_id_array
                )
            );
            $album_info = $this->album_class->getInfo([
                "is_default" => 1
            ], "album_id");
            $album_id = $album_info["album_id"];
            // 删除所选相册
            $res = $this->album_class->destroy($condition);
            // 将被删除相册下的图片转移到默认
            $this->album_picture->save([
                "album_id" => $album_id
            ], $condition);
            $this->album_class->commit();
            if ($res == 1) {
                hook("albumDeleteSuccess", $aclass_id_array);
                return SUCCESS;
            } else {
                return DELETE_FAIL;
            }
        } catch (\Exception $e) {
            $this->album_class->rollback();
            return $e->getMessage();
        }
        
        return 0;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IAlbum::getPictureList()
     */
    public function getPictureList($page_index = 1, $page_size = 0, $condition = '', $order = " upload_time desc", $field = '*')
    {
        // TODO Auto-generated method stub
        $list = $this->album_picture->pageQuery($page_index, $page_size, $condition, $order, $field);
        return $list;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IAlbum::addPicture()
     */
    public function addPicture($pic_name, $pic_tag, $aclass_id, $pic_cover, $pic_size, $pic_spec, $pic_cover_big, $pic_size_big, $pic_spec_big, $pic_cover_mid, $pic_size_mid, $pic_spec_mid, $pic_cover_small, $pic_size_small, $pic_spec_small, $pic_cover_micro, $pic_size_micro, $pic_spec_micro, $instance_id = 0, $upload_type, $domain, $bucket)
    {
        // TODO Auto-generated method stub
        $data = array(
            'shop_id' => $instance_id,
            'album_id' => $aclass_id,
            'is_wide' => "0",
            'pic_name' => $pic_name,
            'pic_tag' => $pic_tag,
            'pic_cover' => $pic_cover,
            'pic_size' => $pic_size,
            'pic_spec' => $pic_spec,
            'pic_cover_big' => $pic_cover_big,
            'pic_size_big' => $pic_size_big,
            'pic_spec_big' => $pic_spec_big,
            'pic_cover_mid' => $pic_cover_mid,
            'pic_size_mid' => $pic_size_mid,
            'pic_spec_mid' => $pic_spec_mid,
            'pic_cover_small' => $pic_cover_small,
            'pic_size_small' => $pic_size_small,
            'pic_spec_small' => $pic_spec_small,
            'pic_cover_micro' => $pic_cover_micro,
            'pic_size_micro' => $pic_size_micro,
            'pic_spec_micro' => $pic_spec_micro,
            'upload_time' => time(),
            "upload_type" => $upload_type,
            "domain" => $domain,
            "bucket" => $bucket
        );
        $res = $this->album_picture->save($data);
        if ($res == 1) {
            return $this->album_picture->pic_id;
        } else {
            return $res;
        }
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IAlbum::deletePicture()
     */
    public function deletePicture($pic_id_array)
    {
        // TODO Auto-generated method stub
        $shop_id = $this->instance_id;
        $pic_array = explode(',', $pic_id_array);
        $res = 1;
        if (! empty($pic_array)) {
            $user_img_array = $this->getGoodsAlbumUsePictureQuery([
                "shop_id" => $shop_id
            ]);
            
            // 判断当前图片是否在商品中使用过
            foreach ($pic_array as $pic_id) {
                $retval = in_array($pic_id, $user_img_array);
                if (! $retval) {
                    $condition = array(
                        'shop_id' => $shop_id,
                        'pic_id' => $pic_id
                    );
                    // 得到当前图片的信息
                    $picture_obj = $this->album_picture->get($pic_id);
                    if (! empty($picture_obj)) {
                        $pic_cover = $picture_obj["pic_cover"];
                        removeImageFile($pic_cover);
                        $pic_cover_big = $picture_obj["pic_cover_big"];
                        removeImageFile($pic_cover_big);
                        $pic_cover_mid = $picture_obj["pic_cover_mid"];
                        removeImageFile($pic_cover_mid);
                        $pic_cover_small = $picture_obj["pic_cover_small"];
                        removeImageFile($pic_cover_small);
                        $pic_cover_micro = $picture_obj["pic_cover_micro"];
                        removeImageFile($pic_cover_micro);
                    }
                    $result = $this->album_picture->destroy($condition);
                    if (! $result > 0) {
                        $res = - 1;
                    }
                } else {
                    $res = - 1;
                }
            }
        } else {
            $res = - 1;
        }
        if ($res == 1) {
            return SUCCESS;
        } else {
            return DELETE_FAIL;
        }
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IAlbum::getAlbumClassDetail()
     */
    public function getAlbumClassDetail($album_id)
    {
        // TODO Auto-generated method stub
        $res = $this->album_class->get($album_id);
        return $res;
        // TODO Auto-generated method stub
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IAlbum::getAlbumDetail()
     */
    public function getAlbumDetail($pic_id)
    {
        // TODO Auto-generated method stub
        $res = $this->album_picture->get($pic_id);
        return $res;
        // TODO Auto-generated method stub
    }

    public function getAlbumListUseInGoods($condition = '')
    {
        $res = $this->album_class->getQuery($condition, "*", "sort");
        return $res;
    }

    public function ModifyAlbumPicture($pic_id, $pic_cover, $pic_size, $pic_spec, $pic_cover_big, $pic_size_big, $pic_spec_big, $pic_cover_mid, $pic_size_mid, $pic_spec_mid, $pic_cover_small, $pic_size_small, $pic_spec_small, $pic_cover_micro, $pic_size_micro, $pic_spec_micro, $instance_id, $upload_type, $domain, $bucket)
    {
        // TODO Auto-generated method stub
        $data = array(
            'pic_cover' => $pic_cover,
            'pic_size' => $pic_size,
            'pic_spec' => $pic_spec,
            'pic_cover_big' => $pic_cover_big,
            'pic_size_big' => $pic_size_big,
            'pic_spec_big' => $pic_spec_big,
            'pic_cover_mid' => $pic_cover_mid,
            'pic_size_mid' => $pic_size_mid,
            'pic_spec_mid' => $pic_spec_mid,
            'pic_cover_small' => $pic_cover_small,
            'pic_size_small' => $pic_size_small,
            'pic_spec_small' => $pic_spec_small,
            'pic_cover_micro' => $pic_cover_micro,
            'pic_size_micro' => $pic_size_micro,
            'pic_spec_micro' => $pic_spec_micro,
            'upload_time' => time(),
            'upload_type' => $upload_type,
            "domain" => $domain,
            "bucket" => $bucket
        );
        $res = $this->album_picture->save($data, [
            "pic_id" => $pic_id
        ]);
        return $res;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IAlbum::ModifyAlbumPictureName()
     */
    public function ModifyAlbumPictureName($pic_id, $pic_name)
    {
        $data = array(
            'pic_name' => $pic_name
        );
        $res = $this->album_picture->save($data, [
            "pic_id" => $pic_id
        ]);
        if ($res == 1) {
            return SUCCESS;
        } else {
            return UPDATA_FAIL;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IAlbum::ModifyAlbumPictureClass()
     */
    public function ModifyAlbumPictureClass($pic_id, $album_id)
    {
        $data = array(
            'album_id' => $album_id
        );
        $condition["pic_id"] = [
            "in",
            $pic_id
        ];
        $res = $this->album_picture->save($data, $condition);
        if ($res > 0) {
            return SUCCESS;
        } else {
            return UPDATA_FAIL;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IAlbum::ModifyAlbumClassCover()
     */
    public function ModifyAlbumClassCover($pic_id, $album_id)
    {
        $data = array(
            'album_cover' => $pic_id
        );
        $res = $this->album_class->save($data, [
            "album_id" => $album_id
        ]);
        if ($res == 1) {
            return SUCCESS;
        } else {
            return UPDATA_FAIL;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IAlbum::getGoodsUseAlbum()
     */
    public function getGoodsUseAlbum()
    {}

    public function delAlbumPicYuan()
    {
        $list = $this->album_picture->getQuery('', 'pic_cover', '');
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IAlbum::checkPictureIsUse()
     */
    public function checkPictureIsUse($shop_id, $pic_id)
    {
        // 1.判断商品图片是否已经使用
        $goods = new NsGoodsModel();
        $res = $goods->where(" FIND_IN_SET('" . $pic_id . "', img_id_array) and shop_id = " . $shop_id)->count();
        if ($res > 0) {
            return true;
        } else {
            return false;
        }
        // 2.判断商品sku图片是否已经使用
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IAlbum::getPictureDetail()
     */
    public function getAlubmPictureDetail($condition)
    {
        // TODO Auto-generated method stub
        $res = $this->album_picture->getInfo($condition, "*");
        return $res;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IAlbum::getGoodsAlbumUsePictureQuery()
     */
    public function getGoodsAlbumUsePictureQuery($condition)
    {
        // TODO Auto-generated method stub
        $goods = new NsGoodsModel();
        $goods_query = $goods->getQuery($condition, "img_id_array", "");
        $goods_deleted = new NsGoodsDeletedModel();
        $goods_deleted_query = $goods_deleted->getQuery($condition, "img_id_array", "");
        $img_array = array();
        foreach ($goods_query as $k => $v) {
            if (trim($v["img_id_array"]) != "") {
                $tmp_array = explode(",", trim($v["img_id_array"]));
                $img_array = array_merge($img_array, $tmp_array);
            }
        }
        foreach ($goods_deleted_query as $k => $v) {
            if (trim($v["img_id_array"]) != "") {
                $tmp_array = explode(",", trim($v["img_id_array"]));
                $img_array = array_merge($img_array, $tmp_array);
            }
        }
        $img_array = array_unique($img_array);
        return $img_array;
    }

    public function getDefaultAlbumDetail()
    {
        $res = $this->album_class->getInfo([
            "shop_id" => $this->instance_id,
            "is_default" => 1
        ]);
        return $res;
    }
}