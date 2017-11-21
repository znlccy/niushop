<?php
/**
 * AlbumPictureModel.php
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
use think\Db;
use data\model\BaseModel as BaseModel;
/**
 * 图片model
 */
class AlbumPictureModel extends BaseModel {

    protected $table = 'sys_album_picture';
    protected $rule = [
        'pic_id'  =>  '', 
        'pic_tag'  =>  'no_html_parse',
        'pic_name'  =>  'no_html_parse',
        'pic_cover'  =>  'no_html_parse',
        'pic_cover_big'  =>  'no_html_parse',
        'pic_cover_mid'  =>  'no_html_parse',
        'pic_cover_small'  =>  'no_html_parse',
        'pic_cover_micro'  =>  'no_html_parse'
    ];
    protected $msg = [
        'pic_id'  =>  '',
        'pic_tag'  =>  '',
        'pic_name'  =>  '',
        'pic_cover'  =>  '',
        'pic_cover_big'  =>  '',
        'pic_cover_mid'  =>  '',
        'pic_cover_small'  =>  '',
        'pic_cover_micro'  =>  ''
    ];
}