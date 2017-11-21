<?php
/**
 * Index.php
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
namespace app\api\controller;
use data\service\Member;
use app\api\controller\BaseController;
/**
 * 注册
 * 
 * @author Administrator
 *        
 */
class Register extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        //获取信息
        $user_name = !empty($_POST['user_name'])? $_POST['user_name']:'';
        $password = !empty($_POST['password'])? $_POST['password'] :'';
        //处理信息
        $member = new Member();
        $res = $member->registerMember($user_name, $password, '', '', '', '', '', '', '');    
        //返回信息
        if($res == -2004){
            return $this->outMessage('niu_index_response', $res, -50, '账号已被注册过');  
        }elseif($res == -2006 ){
            return $this->outMessage('niu_index_response', $res, -50, '用户名必须是英文字母或英文字母汉字组合');
        }else{
            return $this->outMessage('niu_index_response', $res);
        }
        
    }

    
}
