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
namespace app\shop\controller;

use data\service\Platform;

/**
 * 帮助中心
 * 创建人：李志伟
 * 创建时间：2017年2月17日20:12:50
 */
class Helpcenter extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function _empty($name)
    {}

    /**
     * 首页
     */
    public function index()
    {
       
        $platform = new Platform();
        $document_id = request()->get("id", "");
        $class_id = request()->get("class_id", "");
        
        $platform_help_class = $platform->getPlatformHelpClassList(1, 0, '', 'sort');
        $this->assign('platform_help_class', $platform_help_class['data']); // 帮助中心分类列表
        
        $platform_help_document = $platform->getPlatformHelpDocumentList(1, 0, '', 'sort');
        $this->assign('platform_help_document', $platform_help_document['data']); // 帮助中心列表
        
        if (empty($document_id)) {
            $is_exit = false;
            foreach ($platform_help_class['data'] as $class) {
                if ($is_exit) {
                    break;
                }
                foreach ($platform_help_document['data'] as $document) {
                    if ($class['class_id'] == $document['class_id']) {
                        $is_exit = true;
                        $title = $document['title'];
                        $content = $document['content'];
                        break;
                    }
                }
            }
            $help_document_info = array(
                'title' => $title,
                'content' => $content
            );
            $this->assign('help_document_info', $help_document_info); // 帮助中心信息详情
                                                                      
            // 帮助中心地址栏title(帮助中心详情页)
            $this->assign('title_before', $help_document_info['title']);
            $seoconfig['seo_desc'] = $help_document_info['title'];
            $this->assign("seoconfig", $seoconfig);
        } else {
            $help_document_info = $platform->getPlatformHelpDocumentList(1, 0, [
                'id' => $document_id
            ], 'sort');
            if (empty($help_document_info['data'])) {
                $redirect = __URL(__URL__ . '/index');
                $this->redirect($redirect);
            }
            $this->assign('help_document_info', $help_document_info['data'][0]); // 帮助中心信息详情
                                                                                 
            // 帮助中心地址栏title(帮助中心详情页)
            $this->assign('title_before', $help_document_info['data'][0]['title']);
            $seoconfig['seo_desc'] = $help_document_info['data'][0]['title'];
            $this->assign("seoconfig", $seoconfig);
        }
        $this->assign("title_before", "帮助中心");
        $this->assign('document_id', $document_id);
        $this->assign('class_id', $class_id);
        return view($this->style . 'Helpcenter/index');
    }
}