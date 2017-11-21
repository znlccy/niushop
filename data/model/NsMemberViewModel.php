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
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 前台会员视图表
 * @author Administrator
 *
 */
class NsMemberViewModel extends BaseModel {
    protected $table = 'ns_member';
    /**
     * 获取列表返回数据格式
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @return unknown
     */
    public function getViewList($page_index, $page_size, $condition, $order){
    
        $queryList = $this->getViewQuery($page_index, $page_size, $condition, $order);
        $queryCount = $this->getViewCount($condition);
        $list = $this->setReturnList($queryList, $queryCount, $page_size);
        return $list;
    }
    /**
     * 获取列表
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @return \data\model\multitype:number
     */
    public function getViewQuery($page_index, $page_size, $condition, $order)
    {
        //设置查询视图
        $viewObj = $this->alias('nm')
        ->join('ns_member_level nml','nm.member_level = nml.level_id','left')
        ->join('sys_user su','nm.uid= su.uid','left')
        ->field('nm.uid, nm.member_level, nm.reg_time, nm.memo, nml.level_name, nml.goods_discount, su.uid, su.instance_id, su.user_name, su.user_password, su.user_status, su.user_headimg, su.is_system, su.is_member, su.user_tel, su.user_tel_bind, su.user_qq, su.qq_openid, su.qq_info, su.user_email, su.user_email_bind, su.wx_openid, su.wx_sub_time, su.wx_notsub_time, su.wx_is_sub, su.wx_info, su.other_info, su.reg_time, su.current_login_ip, su.current_login_time, su.current_login_type, su.last_login_time, su.last_login_ip, su.last_login_type, su.login_num, su.real_name, su.sex, su.birthday, su.location, su.nick_name, su.wx_unionid, su.qrcode_template_id');
        $list = $this->viewPageQuery($viewObj, $page_index, $page_size, $condition, $order);
        return $list;
    }
    /**
     * 获取列表数量
     * @param unknown $condition
     * @return \data\model\unknown
     */
    public function getViewCount($condition)
    {
        $viewObj = $this->alias('nm')
        ->join('ns_member_level nml','nm.member_level = nml.level_id','left')
        ->join('sys_user su','nm.uid= su.uid','left')
        ->field('nm.uid');
        $count = $this->viewCount($viewObj,$condition);
        return $count;
    }

}
