<?php
/**
 * GoodsBrand.php
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
namespace data\service;

/**
 * 商品品牌服务层
 */
use data\service\BaseService as BaseService;
use data\model\NsGoodsBrandModel as NsGoodsBrand;
use data\model\AlbumPictureModel as AlbumPictureModel;
use data\api\IGoodsBrand as IGoodsBrand;

class GoodsBrand extends BaseService implements IGoodsBrand
{

    private $goods_brand;

    function __construct()
    {
        parent::__construct();
        $this->goods_brand = new NsGoodsBrand();
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IGoodsBrand::getGoodsBrandList()
     */
    public function getGoodsBrandList($page_index = 1, $page_size = 0, $condition = '', $order = 'brand_initial asc', $field = '*')
    {
        $list = $this->goods_brand->pageQuery($page_index, $page_size, $condition, $order, $field);
        return $list;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IGoodsBrand::addOrUpdateGoodsBrand()
     */
    public function addOrUpdateGoodsBrand($brand_id, $shop_id = 0, $brand_name, $brand_initial, $brand_class, $brand_pic, $brand_recommend, $sort, $brand_category_name = '', $category_id_array = '', $brand_ads, $category_name, $category_id_1, $category_id_2, $category_id_3)
    {
        $data = array(
            'shop_id' => $shop_id,
            'brand_name' => $brand_name,
            'brand_initial' => $brand_initial,
            'brand_pic' => $brand_pic,
            'brand_recommend' => $brand_recommend,
            'sort' => $sort,
            'brand_ads' => $brand_ads,
            'category_name' => $category_name,
            'category_id_1' => $category_id_1,
            'category_id_2' => $category_id_2,
            'category_id_3' => $category_id_3
        );
        if ($brand_id == "") {
            $res = $this->goods_brand->save($data);
            $data['brand_id'] = $this->goods_brand->brand_id;
            hook("goodsBrandSaveSuccess", $data);
            return $this->goods_brand->brand_id;
        } else {
            $res = $this->goods_brand->save($data, [
                "brand_id" => $brand_id
            ]);
            $data['brand_id'] = $brand_id;
            hook("goodsBrandSaveSuccess", $data);
            return $res;
        }
        
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IGoodsBrand::ModifyGoodsBrandSort()
     */
    public function ModifyGoodsBrandSort($brand_id, $sort)
    {
        $data = array()

        ;
        $res = $this->goods_brand->save($data, [
            'brand_id' => $brand_id
        ]);
        return $res;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IGoodsBrand::ModifyGoodsBrandRecomend()
     */
    public function ModifyGoodsBrandRecomend($brand_id, $brand_recommend)
    {
        $data = array()

        ;
        $res = $this->goods_brand->save($data, [
            'brand_id' => $brand_id
        ]);
        return $res;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IGoodsBrand::deleteGoodsBrand()
     */
    public function deleteGoodsBrand($brand_id_array)
    {
        $res = $this->goods_brand->destroy($brand_id_array);
        hook("goodsBrandDeleteSuccess", [
            'brand_id' => $brand_id_array
        ]);
        return $res;
        // TODO Auto-generated method stub
    }

    /**
     * 根据id获取商品品牌信息
     * 
     * @param unknown $module_id            
     */
    public function getGoodsBrandInfo($brand_id, $field = '*')
    {
        $info = $this->goods_brand->getInfo(array(
            'brand_id' => $brand_id
        ), $field);
        return $info;
    }
}