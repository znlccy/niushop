<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : NiuTeam
 * @date : 2017年11月13日 16:03:16
 * @version : v1.0.0.0
 */
namespace addons\wxtemplatemsg\controller;

use addons\wxtemplatemsg\Wxtemplatemsg as baseWxtemplatemsg;
use data\extend\WchatOauth;

class Wxtemplatemsg extends baseWxtemplatemsg
{

    /**
     * 获取模板id
     */
    public function getTemplateId()
    {
        $condition['template_id'] = '';
        $list = \think\Db::table("$this->table")->where($condition)->select();
        if (! empty($list)) {
            foreach ($list as $k => $v) {
                $template_id = $this->getTemplateIdByTemplateNo($v['template_no']);
                if ($template_id) {
                    \think\Db::table("$this->table")->where('id', $v['id'])->update([
                        'template_id' => $template_id
                    ]);
                }
            }
        }
        return AjaxReturn(1);
    }

    /**
     * 设置模板消息是否启用
     */
    public function changeIsEnable()
    {
        $id = request()->post('id', 0);
        $is_enable = request()->post('is_enable', 0);
        $res = \think\Db::table("$this->table")->where([
            'id' => $id
        ])->update([
            'is_enable' => $is_enable
        ]);
        return AjaxReturn($res);
    }

    /**
     * 根据模板编号 获取 模板id
     */
    protected function getTemplateIdByTemplateNo($template_no)
    {
        $wchat = new WchatOauth();
        $json = $wchat->templateID($template_no);
        $array = json_decode($json, true);
        $template_id = '';
        if ($array) {
            $template_id = $array['template_id'];
        }
        return $template_id;
    }
}