<?php
/**
 * Cms.php
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
namespace app\shop\controller;


use data\service\Platform;
/**
 * 内容控制器
 * 创建人：李吉
 * 创建时间：2017-02-06 10:59:23
 */
class Notice extends BaseController
{
    /**
     * 公告详情
     */
    public function detail()
    {
        $platform = new Platform();
        $id = request()->get("id",0);
        $info = $platform -> getNoticeDetail($id);
        if(empty($info)){
            $this->error("没有获取到公告信息");
        }else{
            $this->assign('info', $info);
            //专题详情页网站title
            $this->assign('title_before',$info['notice_title']);
        }
        //上一篇
        $prev_info = $platform->getNoticeList(1,1,[
            "id" => array("<",$id),
        ],"id desc");
        $this->assign("prev_info",$prev_info['data'][0]);
        //下一篇
        $next_info = $platform->getNoticeList(1,1,[
            "id" => array(">",$id),
        ],"id asc");
        $this->assign("next_info",$next_info['data'][0]);
        return view($this->style . 'Notice/detail');
    }
    
    /**
     * 公告列表
     */
    public function noticeList(){
        $platform = new Platform();
        $list = $platform -> getNoticeList(1, 0, "", "sort");
        $this->assign("list", $list['data']);
        return view($this->style. "Notice/noticeList");
    }
}