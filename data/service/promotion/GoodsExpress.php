<?php
/**
 * GoodsExpress.php
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
namespace data\service\promotion;

use data\model\NsGoodsModel;
use data\model\NsGoodsSkuModel;
use data\model\NsOrderShippingFeeExtendModel;
use data\service\BaseService;
use data\model\NsOrderShippingFeeModel;
use data\model\NsOrderExpressCompanyModel;
use data\model\CityModel;
use data\model\DistrictModel;
use data\service\Config;

/**
 * 商品邮费操作类
 *
 * @author Administrator
 *        
 */
class GoodsExpress extends BaseService
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * ***************************************************************************************订单运费管理开始**************************************************
     */
    /**
     * 获取商品邮费总和
     *
     * @param unknown $goods_sku_list            
     */
    public function getSkuListExpressFee($goods_sku_list, $express_company_id, $province, $city, $district)
    {
        $config = new Config();
        // 查询用户是否选择物流
        $is_able_select = $config->getConfig(0, 'ORDER_IS_LOGISTICS');
        if (! empty($is_able_select)) {
            $is_able = $is_able_select['value'];
        } else {
            $is_able = 0;
        }
        if ($is_able == 1) {
            $fee = $this->getSameExpressSkuListFee($goods_sku_list, $express_company_id, $province, $city, $district);
            return $fee;
        } else {
            $company_list = $this->getGoodsSkuExpressGroup($goods_sku_list, 0);
            if (! empty($company_list)) {
                $fee = 0;
                foreach ($company_list as $k => $v) {
                    if (! empty($v['shipping_sku_list'])) {
                        $same_fee = $this->getSameExpressSkuListFee($v['shipping_sku_list'], $v['co_id'], $province, $city, $district);
                        if ($same_fee >= 0) {
                            $fee += $same_fee;
                        } else {
                            return NULL_EXPRESS_FEE;
                        }
                    }
                }
                return $fee;
            } else {
                return NULL_EXPRESS_FEE;
            }
        }
    }

    /**
     * 获取相同运费模板运费情况
     *
     * @param unknown $goods_sku_list            
     * @param unknown $express_company_id            
     * @param unknown $province            
     * @param unknown $city            
     * @param unknown $district            
     * @return string|Ambigous <number, string, unknown>
     */
    public function getSameExpressSkuListFee($goods_sku_list, $express_company_id, $province, $city, $district)
    {
        $fee = 0;
        if (! empty($goods_sku_list)) {
            $shipping_template = $this->getShippingFeeTemplate($express_company_id, $province, $city, $district);
            if ($shipping_template == NULL_EXPRESS_FEE) {
                // 当前地址不支持配送
                return NULL_EXPRESS_FEE;
            }
            
            $goods_express_list_array = $this->getSkuGroup($goods_sku_list);
            // 计算称重方式运费
            $weight_shipping_fee = $this->getWeightShippingExpressFee($shipping_template, $goods_express_list_array['weight_shipping']);
            if ($weight_shipping_fee < 0) {
                return $weight_shipping_fee;
            } else {
                $fee += $weight_shipping_fee;
            }
            // 计算体积方式运费
            $volume_shipping_fee = $this->getVolumeShippingExpressFee($shipping_template, $goods_express_list_array['volume_shipping']);
            if ($volume_shipping_fee < 0) {
                return $volume_shipping_fee;
            } else {
                // var_dump("体积运费".$volume_shipping_fee);
                $fee += $volume_shipping_fee;
            }
            // 计件方式计算运费
            $bynum_shipping_fee = $this->getBynumShippingExpressFee($shipping_template, $goods_express_list_array['bynum_shipping']);
            if ($bynum_shipping_fee < 0) {
                return $bynum_shipping_fee;
            } else {
                $fee += $bynum_shipping_fee;
            }
            return $fee;
        } else {
            return $fee;
        }
    }

    /**
     * 根据地址获取运费模板
     *
     * @param unknown $express_company_id            
     * @param unknown $province            
     * @param unknown $city            
     */
    private function getShippingFeeTemplate($express_company_id, $province, $city, $district = 0)
    {
        $shipping_fee = new NsOrderShippingFeeModel();
        $fee_array = $shipping_fee->getQuery([
            'co_id' => $express_company_id
        ], '*', '');
        $district_model = new DistrictModel();
        // 检测城市是否有区概念
        $count = $district_model->getCount([
            'city_id' => $city
        ]);
        $temp = array();
        $default = array();
        foreach ($fee_array as $k => $v) {
            if ($v['is_default'] == 1) {
                $default = $v;
            }
            if ($count == 0) {
                
                if (! empty($v['city_id_array'])) {
                    $city_array = explode(',', $v['city_id_array']);
                    if (in_array($city, $city_array)) {
                        $temp = $v;
                    }
                }
            } else {
                $district_array = explode(',', $v['district_id_array']);
                if (in_array($district, $district_array)) {
                    $temp = $v;
                }
            }
        }
        // 如果模板为空，找到默认模板
        if (empty($temp)) {
            if (! empty($default)) {
                $temp = $default;
                return $temp;
            } else {
                return NULL_EXPRESS_FEE; // 返回表示该地址不支持配送
            }
        } else {
            return $temp;
        }
    }

    /**
     * 商品进行邮费分组(考虑满减送活动)
     *
     * @param unknown $goods_sku_list
     *            skuid:1,skuid:2,skuid:3
     */
    private function getSkuGroup($goods_sku_list)
    {
        // 分离商品
        $goods_sku_list_array = explode(",", $goods_sku_list);
        // 获取商品列表满减送活动,方便查询是否满邮情况
        $goods_mansong = new GoodsMansong();
        $mansong_goods_sku_array = $goods_mansong->getFreeExpressGoodsSkuList($goods_sku_list);
        // 获取整体数据
        $goods_express_list_array = array();
        // 获取免运费商品列表
        $free_express_list = array();
        // 获取非免费商品列表
        // 按照重量计算运费
        $goods_sku_weight_array = array();
        // 按照体积计算运费
        $goods_sku_volume_array = array();
        // 按照计算方式计算运费
        $goods_sku_bynum_array = array();
        foreach ($goods_sku_list_array as $k => $goods_sku_array) {
            $goods_sku = explode(':', $goods_sku_array);
            $goods_sku_model = new NsGoodsSkuModel();
            $goods_id = $goods_sku_model->getInfo([
                'sku_id' => $goods_sku[0]
            ], 'goods_id');
            $goods = new NsGoodsModel();
            $shipping_fee = $goods->getInfo([
                'goods_id' => $goods_id['goods_id']
            ], 'shipping_fee,goods_weight,goods_volume,shipping_fee_type');
            if ($shipping_fee['shipping_fee'] <= 0) {
                $free_express_list[] = $goods_sku_array;
            } else {
                if (in_array($goods_sku[0], $mansong_goods_sku_array)) {
                    $free_express_list[] = $goods_sku_array;
                } else {
                    $shipping_array = array(
                        'goods_sku_id' => $goods_sku[0],
                        'goods_sku_num' => $goods_sku[1],
                        'goods_id' => $goods_id['goods_id'],
                        'goods_weight' => $shipping_fee['goods_weight'],
                        'goods_volume' => $shipping_fee['goods_volume']
                    );
                    // var_dump($shipping_array);
                    if ($shipping_fee['shipping_fee_type'] == 1) {
                        // 按照重量计算运费
                        $goods_sku_weight_array[] = $shipping_array;
                    } elseif ($shipping_fee['shipping_fee_type'] == 2) {
                        // 按照体积计算运费
                        $goods_sku_volume_array[] = $shipping_array;
                    } elseif ($shipping_fee['shipping_fee_type'] == 3) {
                        // 按照计件方式计算运费
                        $goods_sku_bynum_array[] = $shipping_array;
                    }
                }
            }
        }
        $goods_express_list_array = array(
            'free_shipping' => $free_express_list,
            'weight_shipping' => $goods_sku_weight_array,
            'volume_shipping' => $goods_sku_volume_array,
            'bynum_shipping' => $goods_sku_bynum_array
        );
        return $goods_express_list_array;
    }

    /**
     * 计算称重方式运费总和
     *
     * @param unknown $temp
     *            //运费模板
     * @param unknown $goods_sku_weight_array            
     *
     */
    private function getWeightShippingExpressFee($temp, $goods_sku_weight_array)
    {
        if (empty($goods_sku_weight_array)) {
            return 0;
        }
        if ($temp['weight_is_use'] == 0) {
            // 不支持配送
            return NULL_EXPRESS_FEE;
        } else {
            $weight = 0;
            foreach ($goods_sku_weight_array as $k => $v) {
                // 计算总重量
                $weight += $v['goods_weight'] * $v['goods_sku_num'];
            }
            if ($weight > 0) {
                if ($weight <= $temp['weight_snum']) {
                    return $temp['weight_sprice'];
                } else {
                    $ext_weight = $weight - $temp['weight_snum'];
                    if ($temp['weight_xnum'] == 0) {
                        $temp['weight_xnum'] = 1;
                    }
                    if (($ext_weight * 100) % ($temp['weight_xnum'] * 100) == 0) {
                        $ext_data = $ext_weight / $temp['weight_xnum'];
                    } else {
                        $ext_data = floor($ext_weight / $temp['weight_xnum']) + 1;
                    }
                    return $temp['weight_sprice'] + $ext_data * $temp['weight_xprice'];
                }
            } else {
                return 0;
            }
        }
    }

    /**
     * 计算体积方式运费总和
     *
     * @param unknown $temp
     *            //运费模板
     * @param unknown $goods_sku_volume_array            
     *
     */
    private function getVolumeShippingExpressFee($temp, $goods_sku_volume_array)
    {
        if (empty($goods_sku_volume_array)) {
            return 0;
        }
        
        if ($temp['volume_is_use'] == 0) {
            // 不支持配送
            return NULL_EXPRESS_FEE;
        } else {
            $volume = 0;
            foreach ($goods_sku_volume_array as $k => $v) {
                // 计算总重量
                $volume += $v['goods_volume'] * $v['goods_sku_num'];
            }
            if ($volume > 0) {
                
                if ($volume <= $temp['volume_snum']) {
                    return $temp['volume_sprice'];
                } else {
                    $ext_volume = $volume - $temp['volume_snum'];
                    if ($temp['volume_xnum'] == 0) {
                        $temp['volume_xnum'] = 1;
                    }
                    if($temp['weight_xnum'] == 0){
                        $temp['weight_xnum'] = 1;
                    }
                    if (($ext_volume * 100) % ($temp['volume_xnum'] * 100) == 0) {
                        $ext_data = $ext_volume / $temp['volume_xnum'];
                    } else {
                        $ext_data = floor($ext_volume / $temp['weight_xnum']) + 1;
                    }
                    return $temp['volume_sprice'] + $ext_data * $temp['volume_xprice'];
                }
            } else {
                return 0;
            }
        }
    }

    /**
     * 计算计件方式运费总和
     *
     * @param unknown $temp
     *            //运费模板
     * @param unknown $goods_sku_bynum_array            
     *
     */
    private function getBynumShippingExpressFee($temp, $goods_sku_bynum_array)
    {
        if (empty($goods_sku_bynum_array)) {
            return 0;
        }
        if ($temp['bynum_is_use'] == 0) {
            // 不支持配送
            return NULL_EXPRESS_FEE;
        } else {
            $num = 0;
            foreach ($goods_sku_bynum_array as $k => $v) {
                // 计算总数量
                $num += $v['goods_sku_num'];
            }
            if ($num > 0) {
                if ($num <= $temp['bynum_snum']) {
                    return $temp['bynum_sprice'];
                } else {
                    $ext_num = $num - $temp['bynum_snum'];
                    if ($temp['bynum_xnum'] == 0) {
                        $temp['bynum_xnum'] = 1;
                    }
                    if ($ext_num % $temp['bynum_xnum'] == 0) {
                        $ext_data = $ext_num / $temp['bynum_xnum'];
                    } else {
                        $ext_data = floor($ext_num / $temp['bynum_xnum']) + 1;
                    }
                    
                    return $temp['bynum_sprice'] + $ext_data * $temp['bynum_xprice'];
                }
            } else {
                return 0;
            }
        }
    }

    /**
     * 获取商品运费模板名称
     *
     * @param unknown $shipping_fee_id            
     */
    public function getGoodsExpressName($id)
    {
        $name = '';
        $shipping_fee = new NsOrderShippingFeeExtendModel();
        $shipping_fee_info = $shipping_fee->getInfo([
            'id' => $id
        ], 'snum,sprice,xnum,xprice');
        if (! empty($shipping_fee_info)) {
            $name = $shipping_fee_info['snum'] . '件以下' . $shipping_fee_info['sprice'] . '元,' . '超过每' . $shipping_fee_info['xnum'] . '件' . $shipping_fee_info['xprice'] . '元';
        }
        
        return $name;
    }

    /**
     * 获取商品运费
     *
     * @param unknown $goods_id            
     * @param unknown $province_id            
     * @param unknown $city_id            
     * @return string
     */
    public function getGoodsExpressTemplate($goods_id, $province_id, $city_id, $district_id)
    {
        $goods = new NsGoodsModel();
        $shipping_fee = $goods->getInfo([
            'goods_id' => $goods_id
        ], 'shop_id, shipping_fee');
        if ($shipping_fee['shipping_fee'] <= 0) {
            return "免邮";
        } else {
            $goods_sku_model = new NsGoodsSkuModel();
            $goods_sku = $goods_sku_model->getQuery([
                'goods_id' => $goods_id
            ], 'sku_id', '');
            $express_company_list = $this->getExpressCompany($shipping_fee['shop_id'], $goods_sku[0]['sku_id'] . ':1', $province_id, $city_id, $district_id);
            
            $config = new Config();
            $is_able_select = $config->getConfig(0, 'ORDER_IS_LOGISTICS');
            if (! empty($is_able_select)) {
                $is_able = $is_able_select['value'];
            } else {
                $is_able = 0;
            }
            if ($is_able == 1) {
                return $express_company_list;
            } else {
                // 如果禁用选择物流公司查询默认或者第一条，只显示运费即可
                if (! empty($express_company_list)) {
                    return "￥" . $express_company_list[0]['express_fee'];
                }
            }
        }
    }

    /**
     * 查询可用物流公司
     *
     * @param unknown $province_id            
     * @param unknown $city_id            
     */
    public function getExpressCompany($shop_id, $goods_sku_list, $province_id, $city_id, $district_id)
    {
        $express_company_model = new NsOrderExpressCompanyModel();
        // 查询设置如果禁用只查询默认或者第一条
        $config = new Config();
        // 查询用户是否选择物流
        $is_able_select = $config->getConfig(0, 'ORDER_IS_LOGISTICS');
        if (! empty($is_able_select)) {
            $is_able = $is_able_select['value'];
        } else {
            $is_able = 0;
        }
        if ($is_able == 1) {
            $list = $express_company_model->getQuery([
                'shop_id' => $shop_id,
                'is_enabled' => 1
            ], 'co_id,company_name,is_default', 'orders');
        } else {
            $list = $express_company_model->getQuery([
                'shop_id' => $shop_id,
                'is_enabled' => 1,
                'is_default' => 1
            ], 'co_id,company_name,is_default', 'orders');
            if (empty($list)) {
                $new_list = $express_company_model->getFirstData([
                    'shop_id' => $shop_id,
                    'is_enabled' => 1
                ], 'orders');
                if(!empty($new_list)){
                    $list = array();
                    $list[0] = $new_list;
                }
            }
        }
        
        if (! empty($list)) {
            $new_list = array();
            foreach ($list as $k => $v) {
                $express_fee = $this->getSkuListExpressFee($goods_sku_list, $v['co_id'], $province_id, $city_id, $district_id);
                if ($express_fee >= 0) {
                    $new_list[] = array(
                        'co_id' => $v['co_id'],
                        'company_name' => $v['company_name'],
                        'is_default' => $v['is_default'],
                        'express_fee' => $express_fee
                    );
                }
            }
            return $new_list;
        } else {
            return '';
        }
    }

    /**
     * 获取店铺所有物流公司
     *
     * @param unknown $shop_id            
     */
    public function getAllExpressCompany($shop_id)
    {
        $express_company_model = new NsOrderExpressCompanyModel();
        $list = $express_company_model->getQuery([
            'shop_id' => $shop_id,
            'is_enabled' => 1,
        ], '*', '');
        return $list;
    }

    /**
     * 获取店铺物流公司
     *
     * @param unknown $shop_id            
     * @return unknown
     */
    public function getExpressCompanyCount($shop_id)
    {
        $express_company_model = new NsOrderExpressCompanyModel();
        $count = $express_company_model->getCount([
            'shop_id' => $shop_id,
            'is_enabled' => 1,
        ]);
        if (empty($count)) {
            $count = 0;
        }
        return $count;
    }

    /**
     * 商品邮费的sku分组
     *
     * @param unknown $goods_sku_list            
     */
    public function getGoodsSkuExpressGroup($goods_sku_list, $shop_id)
    {
        // 分离商品
        $goods_sku_list_array = explode(",", $goods_sku_list);
        // 获取默认物流公司
        $express_company_model = new NsOrderExpressCompanyModel();
        $express_company_list = $express_company_model->getQuery([
            'shop_id' => $shop_id,
            'is_enabled' => 1,
        ], '*', '');
        if (! empty($express_company_list)) {
            $default_company = $express_company_model->getInfo([
                'shop_id' => $shop_id,
                'is_default' => 1,
                'is_enabled' => 1,
            ]);
            if (empty($default_company)) {
                $default_company = $express_company_list[0];
            }
            if (! empty($express_company_list)) {
                foreach ($express_company_list as $k_company => $v) {
                    $sku_list = '';
                    foreach ($goods_sku_list_array as $k => $goods_sku_array) {
                        $goods_sku = explode(':', $goods_sku_array);
                        $goods_sku_model = new NsGoodsSkuModel();
                        $goods_id = $goods_sku_model->getInfo([
                            'sku_id' => $goods_sku[0]
                        ], 'goods_id');
                        $goods = new NsGoodsModel();
                        $shipping_fee = $goods->getInfo([
                            'goods_id' => $goods_id['goods_id']
                        ], 'shipping_fee,shipping_fee_id');
                        if ($shipping_fee['shipping_fee'] == 1) {
                            if ($shipping_fee['shipping_fee_id'] == 0) {
                                // 商品未设置物流公司
                                if ($v['co_id'] == $default_company['co_id']) {
                                    $sku_list = $sku_list . $goods_sku_array . ',';
                                }
                            } else {
                                if ($v['co_id'] == $shipping_fee['shipping_fee_id']) {
                                    $sku_list = $sku_list . $goods_sku_array . ',';
                                }
                            }
                        }
                    }
                    $express_company_list[$k_company]['shipping_sku_list'] = $sku_list;
                }
            }
            return $express_company_list;
        } else {
            return '';
        }
    }
}