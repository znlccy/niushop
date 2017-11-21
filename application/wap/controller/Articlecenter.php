<?php
/**
 * Helpcenter.php
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
namespace app\wap\controller;

use data\service\Article;

/**
 * 帮助中心
 * 创建人：李志伟
 * 创建时间：2017年2月17日20:12:50
 */
class Articlecenter extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 首页
     */
    public function index()
    {
        $document_id = request()->post('id', '');
        $article = new Article();
        $platform_help_class = $article->getArticleClassQuery();
        $this->assign('platform_help_class', $platform_help_class); // 文章一级分类列表
        
//         if (empty($document_id)) {
//             $help_document_info = array(
//                 'title' => '文章中心',
//                 'content' => "1、下完订单后在账户里看不见相关信息怎么办？<br/>您可能在{$this->shop_name}有多个账户，建议您核实一下当时下订单的具体账户，如有疑问您可致电客服400-99-00001，帮您核查。<br/>2、网站显示有赠品为何下单后没有收到赠品？<br/>赠品的配送是和您的收货地址有关的，若您在浏览商品时用的地址非最终的收货地址，有可能出现下单后没有赠品的情况；您所在的地址是否支持赠品配送，请以结算页面的购物明细为准，谢谢。;"
//             );
//             $this->assign('help_document_info', $help_document_info); // 文章详情
//         } else {
//             $help_document_info = $article->getArticleDetail($document_id);
//             return $help_document_info;
//         }
        $this->assign("title_before","文章中心");
        return view($this->style . 'Articlecenter/index');
    }

    /**
     * 获取分类下文章列表
     */
    public function getArticleList()
    {
        $class_id = request()->post('class_id', '');
        $page = request()->post("page",1);
        if (! is_numeric($class_id)) {
            $this->error("未获取到分类信息");
        }
        $article = new Article();
        $article_list = $article->getArticleList($page, PAGESIZE, [
            'nca.class_id' => $class_id
        ], 'nca.sort desc');
        return $article_list;
    }

    /**
     * 文章内容
     */
    public function articleContent()
    {
        $article_id = request()->get('article_id', '');
        $article = new Article();
        $article_info = $article->getArticleDetail($article_id);
        if (empty($article_info)) {
            $this->error("未获取到文章信息");
        }
        $this->assign("title_before",$article_info['title']);
        $this->assign('article_info', $article_info);
        return view($this->style . 'Articlecenter/articleContent');
    }
}