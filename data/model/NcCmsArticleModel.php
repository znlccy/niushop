<?php
/**
 * NcCmsArticleModel.php
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

namespace data\model;

use data\model\BaseModel as BaseModel;
use think\Validate;

/**
 * cms文章表
 * article_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文章编号',
  title varchar(50) NOT NULL COMMENT '文章标题',
  class_id int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文章分类编号',
  short_title varchar(50) NOT NULL DEFAULT '' COMMENT '文章短标题',
  source varchar(50) NOT NULL DEFAULT '' COMMENT '文章来源',
  url varchar(255) NOT NULL DEFAULT '' COMMENT '文章来源链接',
  author varchar(50) NOT NULL COMMENT '文章作者',
  summary varchar(140) NOT NULL DEFAULT '' COMMENT '文章摘要',
  content text NOT NULL COMMENT '文章正文',
  image varchar(255) NOT NULL DEFAULT '' COMMENT '文章标题图片',
  keyword varchar(255) NOT NULL DEFAULT '' COMMENT '文章关键字',
  article_id_array varchar(255) NOT NULL DEFAULT '' COMMENT '相关文章',
  click int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文章点击量',
  sort tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文章排序0-255',
  commend_flag tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文章推荐标志0-未推荐，1-已推荐',
  comment_flag tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '文章是否允许评论1-允许，0-不允许',
  status tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0-草稿、1-待审核、2-已发布、-1-回收站',
  attachment_path varchar(50) NOT NULL COMMENT '文章附件路径',
  tag varchar(255) NOT NULL DEFAULT '' COMMENT '文章标签',
  comment_count int(10) UNSIGNED NOT NULL COMMENT '文章评论数',
  last_comment_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最新评论时间',
  share_count int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文章分享数',
  publisher_name varchar(50) NOT NULL COMMENT '发布者用户名 ',
  uid int(10) UNSIGNED NOT NULL COMMENT '发布者编号',
  public_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '发布时间',
  create_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '文章发布时间',
  modify_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '文章修改时间',
 *
 */
class NcCmsArticleModel extends BaseModel{
    
    protected $table = 'nc_cms_article';
    protected $rule = [
        'article_id'  =>  '',
        'title' => '',
        'content'  =>  'no_html_parse',
    ];
    protected $msg = [
        'article_id'  =>  '',
        'title' => '',
        'content'  =>  'no_html_parse',
    ];   
}