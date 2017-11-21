<?php
/**
 * Saleservice.php
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
namespace app\admin\controller;

use data\service\Goods;

/**
 * 咨询控制器
 */
class Saleservice extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 咨询 管理
     */
    public function consultList()
    {
        $goods = new Goods();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $type = request()->post('type', '');
            $ct_id = request()->post('ct_id', '');
            $start_date = request()->post('start_date') == '' ? 0 : getTimeTurnTimeStamp(request()->post('start_date'));
            $end_date = request()->post('end_date') == '' ? 0 : getTimeTurnTimeStamp(request()->post('end_date'));
            $goods_name = request()->post('goods_name', '');
            $condition = array();
            if (! empty($ct_id)) {
                $condition['ct_id'] = $ct_id;
            }
            if ($type == 'to_reply') {
                $condition['consult_reply'] = '';
            } else 
                if ($type == 'replied') {
                    $condition['consult_reply'] = array(
                        'NEQ',
                        ''
                    );
                }
            if ($start_date != 0 && $end_date != 0) {
                $condition["consult_addtime"] = [
                    [
                        ">",
                        $start_date
                    ],
                    [
                        "<",
                        $end_date
                    ]
                ];
            } elseif ($start_date != 0 && $end_date == 0) {
                $condition["consult_addtime"] = [
                    [
                        ">",
                        $start_date
                    ]
                ];
            } elseif ($start_date == 0 && $end_date != 0) {
                $condition["consult_addtime"] = [
                    [
                        "<",
                        $end_date
                    ]
                ];
            }
            if (! empty($goods_name)) {
                $condition["goods_name"] = array(
                    "like",
                    "%" . $goods_name . "%"
                );
            }
            $list = $goods->getConsultList($page_index, $page_size, $condition, 'consult_addtime desc');
            return $list;
        }
        $type = request()->get('type', '');
        $child_menu_list = array();
        $child_menu_list[0] = array(
            'url' => "Saleservice/consultList",
            'menu_name' => '全部咨询',
            "active" => $type == '' ? 1 : 0
        );
        $child_menu_list[1] = array(
            'url' => "Saleservice/consultList?type=to_reply",
            'menu_name' => '未回复咨询',
            "active" => $type == 'to_reply' ? 1 : 0
        );
        $child_menu_list[2] = array(
            'url' => "Saleservice/consultList?type=replied",
            'menu_name' => '已回复咨询',
            "active" => $type == 'replied' ? 1 : 0
        );
        $ct_list = $goods->getConsultTypeList();
        $this->assign('ct_list', $ct_list['data']);
        $this->assign('type', $type);
        $this->assign('child_menu_list', $child_menu_list);
        return view($this->style . 'Saleservice/consultList');
    }

    /**
     * 咨询管理后台 回复
     */
    public function replyConsultAjax()
    {
        $consult_id = request()->post('consult_id', '');
        $consult_reply = request()->post('consult_reply', '');
        $consult = new Goods();
        $res = $consult->replyConsult($consult_id, $consult_reply);
        return AjaxReturn($res);
    }

    /**
     * 删除 咨询
     */
    public function deleteConsult()
    {
        $consult_id = request()->post('consult_id', '');
        $consult = new Goods();
        $res = $consult->deleteConsult($consult_id);
        return AjaxReturn($res);
    }
}