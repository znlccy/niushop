<?php
/**
 * IWeixin.php
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
namespace data\api;

/**
 * 微信接口       
 */
interface IWeixin
{

    /**
     * 获取微信菜单列表
     *
     * @param unknown $instance_id            
     * @param unknown $pid
     *            当pid=''查询全部
     */
    function getWeixinMenuList($instance_id, $pid = '');

    /**
     * 添加微信菜单
     *
     * @param unknown $indtance_id            
     * @param unknown $menu_name            
     * @param unknown $ico            
     * @param unknown $pid            
     * @param unknown $menu_event_type            
     * @param unknown $menu_event_url            
     * @param unknown $sort            
     */
    function addWeixinMenu($instance_id, $menu_name, $ico, $pid, $menu_event_type, $menu_event_url, $media_id, $sort);

    /**
     * 修改微信菜单
     *
     * @param unknown $menu_id            
     * @param unknown $instance_id            
     * @param unknown $menu_name            
     * @param unknown $ico            
     * @param unknown $pid            
     * @param unknown $menu_event_type            
     * @param unknown $menu_event_url            
     * @param unknown $sort            
     */
    function updateWeixinMenu($menu_id, $instance_id, $menu_name, $ico, $pid, $menu_event_type, $menu_event_url, $media_id);

    /**
     * 修改菜单排序
     *
     * @param unknown $menu_id_arr            
     * @param unknown $sort            
     */
    function updateWeixinMenuSort($menu_id_arr);

    /**
     * 修改菜单名称
     *
     * @param unknown $menu_id            
     * @param unknown $menu_name            
     */
    function updateWeixinMenuName($menu_id, $menu_name);

    /**
     * 修改跳转链接
     *
     * @param unknown $menu_id            
     * @param unknown $menu_eventurl            
     */
    function updateWeixinMenuUrl($menu_id, $menu_event_url);

    /**
     * 修改菜单类型，1：文本，2：单图文，3：多图文
     *
     * @param unknown $menu_id            
     * @param unknown $menu_event_type            
     */
    function updateWeixinMenuEventType($menu_id, $menu_event_type);

    /**
     * 修改图文消息
     *
     * @param unknown $menu_id            
     * @param unknown $media_id            
     * @param unknown $menu_event_type            
     */
    function updateWeiXinMenuMessage($menu_id, $media_id, $menu_event_type);

    /**
     * 添加微信菜单点击数
     *
     * @param unknown $menu_id            
     */
    function addMenuHits($menu_id);

    /**
     * 获取微信菜单详情
     *
     * @param unknown $menu_id            
     */
    function getWeixinMenuDetail($menu_id);

    /**
     * 公众号授权
     *
     * @param unknown $instance_id            
     * @param unknown $authorizer_appid            
     * @param unknown $authorizer_refresh_token            
     * @param unknown $authorizer_access_token            
     * @param unknown $func_info            
     * @param unknown $nick_name            
     * @param unknown $head_img            
     * @param unknown $user_name            
     * @param unknown $alias            
     * @param unknown $qrcode_url            
     */
    function addWeixinAuth($instance_id, $authorizer_appid, $authorizer_refresh_token, $authorizer_access_token, $func_info, $nick_name, $head_img, $user_name, $alias, $qrcode_url);

    /**
     * 用户关注添加粉丝信息
     *
     * @param unknown $instance_id            
     * @param unknown $nickname            
     * @param unknown $headimgurl            
     * @param unknown $sex            
     * @param unknown $language            
     * @param unknown $country            
     * @param unknown $province            
     * @param unknown $city            
     * @param unknown $district            
     * @param unknown $openid            
     * @param unknown $groupid            
     * @param unknown $is_subscribe            
     * @param unknown $memo            
     */
    function addWeixinFans($source_uid, $instance_id, $nickname, $nickname_decode, $headimgurl, $sex, $language, $country, $province, $city, $district, $openid, $groupid, $is_subscribe, $memo, $unionid);

    /**
     * 添加关注回复
     *
     * @param unknown $instance_id            
     * @param unknown $replay_media_id            
     * @param unknown $sort            
     */
    function addFollowReplay($instance_id, $replay_media_id, $sort);
    /**
     * 添加默认回复
     *
     * @param unknown $instance_id            
     * @param unknown $replay_media_id            
     * @param unknown $sort            
     */
    function addDefaultReplay($instance_id, $replay_media_id, $sort);

    /**
     * 修改关注回复
     *
     * @param unknown $id            
     * @param unknown $instance_id            
     * @param unknown $replay_media_id            
     * @param unknown $sort            
     */
    function updateFollowReplay($id, $instance_id, $replay_media_id, $sort);
    /**
     * 修改默认回复
     *
     * @param unknown $id            
     * @param unknown $instance_id            
     * @param unknown $replay_media_id            
     * @param unknown $sort            
     */
    function updateDefaultReplay($id, $instance_id, $replay_media_id, $sort);

    /**
     * 添加关键字回复
     *
     * @param unknown $instance_id            
     * @param unknown $key            
     * @param unknown $match_type            
     * @param unknown $replay_media_id            
     * @param unknown $sort            
     */
    function addKeyReplay($instance_id, $key, $match_type, $replay_media_id, $sort);

    /**
     * 修改关键字回复
     *
     * @param unknown $id            
     * @param unknown $instance_id            
     * @param unknown $key            
     * @param unknown $match_type            
     * @param unknown $replay_media_id            
     * @param unknown $sort            
     */
    function updateKeyReplay($id, $instance_id, $key, $match_type, $replay_media_id, $sort);

    /**
     * 获取关键词回复列表
     *
     * @param number $page_index            
     * @param number $page_size            
     * @param string $condition            
     * @param string $order            
     */
    function getKeyReplayList($page_index = 1, $page_size = 0, $condition = '', $order = '');

    /**
     * 获取关注时回复列表
     *
     * @param number $page_index            
     * @param number $page_size            
     * @param string $condition            
     * @param string $order            
     */
    function getFollowReplayList($page_index = 1, $page_size = 0, $condition = '', $order = '');
    /**
     * 获取默认回复列表
     *
     * @param number $page_index            
     * @param number $page_size            
     * @param string $condition            
     * @param string $order            
     */
    function getDefaultReplayList($page_index = 1, $page_size = 0, $condition = '', $order = '');

    /**
     * 获取 关注时回复信息
     *
     * @param unknown $condition            
     */
    function getFollowReplayDetail($condition);
    /**
     * 获取 默认回复信息
     *
     * @param unknown $condition            
     */
    function getDefaultReplayDetail($condition);

    /**
     * 获取微信粉丝列表
     *
     * @param number $page_index            
     * @param number $page_size            
     * @param string $condition            
     * @param string $order            
     */
    function getWeixinFansList($page_index = 1, $page_size = 0, $condition = '', $order = '');

    /**
     * 获取微信授权列表
     *
     * @param number $page_index            
     * @param number $page_size            
     * @param string $condition            
     * @param string $order            
     */
    function getWeixinAuthList($page_index = 1, $page_size = 0, $condition = '', $order = '');

    /**
     * 查询用户实例的授权信息
     *
     * @param unknown $instance_id            
     */
    function getWeixinAuthInfo($instance_id);

    /**
     * 添加图文消息
     *
     * @param unknown $title            
     * @param unknown $instance_id            
     * @param unknown $type            
     * @param unknown $sort            
     * @param unknown $content            
     */
    function addWeixinMedia($title, $instance_id, $type, $sort, $content);

    /**
     * 添加图文消息内容
     *
     * @param unknown $media_id            
     * @param unknown $title            
     * @param unknown $author            
     * @param unknown $cover            
     * @param unknown $show_cover_pic            
     * @param unknown $summary            
     * @param unknown $content            
     * @param unknown $content_source_url            
     * @param unknown $sort            
     */
    function addWeixinMediaItem($media_id, $title, $author, $cover, $show_cover_pic, $summary, $content, $content_source_url, $sort);

    /**
     * 修改图文消息
     *
     * @param unknown $media_id            
     * @param unknown $title            
     * @param unknown $instance_id            
     * @param unknown $type            
     * @param unknown $sort            
     * @param unknown $content            
     */
    function updateWeixinMedia($media_id, $title, $instance_id, $type, $sort, $content);

    /**
     * 删除 图文消息
     *
     * @param unknown $media_id            
     */
    function deleteWeixinMedia($media_id);

    /**
     * 获取微信图文消息列表
     *
     * @param number $page_index            
     * @param number $page_size            
     * @param string $condition            
     * @param string $order            
     */
    function getWeixinMediaList($page_index = 1, $page_size = 0, $condition = '', $order = '');

    /**
     * 获取图文消息详情，包括子
     *
     * @param unknown $media_id            
     */
    function getWeixinMediaDetail($media_id);

    function getWeixinMediaDetailByMediaId($media_id);

    /**
     * 通过微信openID查询uid
     *
     * @param unknown $openid            
     */
    function getWeixinUidByOpenid($openid);

    /**
     * 通过author_appid获取shopid
     *
     * @param unknown $author_appid            
     */
    function getShopidByAuthorAppid($author_appid);

    /**
     * 取消关注
     *
     * @param unknown $instance_id            
     * @param unknown $openid            
     */
    function WeixinUserUnsubscribe($instance_id, $openid);

    /**
     * 通过appid获取公众账号信息
     *
     * @param unknown $author_appid            
     */
    function getWeixinInfoByAppid($author_appid);

    /**
     * 获取实例微信菜单结构
     *
     * @param unknown $instance_id            
     */
    function getInstanceWchatMenu($instance_id);

    /**
     * 更新实例自定义菜单到微信
     *
     * @param unknown $instance_id            
     */
    function updateInstanceMenuToWeixin($instance_id);

    /**
     * 获取图文消息的微信数据结构
     *
     * @param unknown $media_info            
     */
    function getMediaWchatStruct($media_info);

    /**
     * 获取微信回复的消息内容返回media_id
     *
     * @param unknown $instance_id            
     * @param unknown $key_words            
     */
    function getWhatReplay($instance_id, $key_words);

    /**
     * 获取微信关注回复
     *
     * @param unknown $instance_id            
     */
    function getSubscribeReplay($instance_id);
    
    /**
     * 获取微信默认回复
     * @param unknown $instance_id
     */
    function getDefaultReplay($instance_id);

    /**
     * 获取微信推广二维码配置（注意没有的话添加一条）
     *
     * @param unknown $instance_id            
     */
    function getWeixinQrcodeConfig($instance_id, $uid);

    /**
     * 微信推广二维码设置修改
     *
     * @param unknown $instance_id            
     * @param unknown $background            
     * @param unknown $nick_font_color            
     * @param unknown $nick_font_size            
     * @param unknown $is_logo_show            
     * @param unknown $header_left            
     * @param unknown $header_top            
     * @param unknown $name_left            
     * @param unknown $name_top            
     * @param unknown $logo_left            
     * @param unknown $logo_top            
     * @param unknown $code_left            
     * @param unknown $code_top            
     */
    function updateWeixinQrcodeConfig($instance_id, $background, $nick_font_color, $nick_font_size, $is_logo_show, $header_left, $header_top, $name_left, $name_top, $logo_left, $logo_top, $code_left, $code_top);

    /**
     * 删除微信自定义菜单
     *
     * @param unknown $menu_id            
     */
    function deleteWeixinMenu($menu_id);

    /**
     * 删除 关注时 回复
     *
     * @param unknown $instance_id            
     */
    function deleteFollowReplay($instance_id);
    /**
     * 删除默认 回复
     *
     * @param unknown $instance_id            
     */
    function deleteDefaultReplay($instance_id);

    /**
     * 获取 关键字回复 详情
     */
    function getKeyReplyDetail($id);

    /**
     * 删除 关键字 回复
     *
     * @param unknown $id            
     */
    function deleteKeyReplay($id);

    /**
     * 查询所有二维码模板
     *
     * @param unknown $shop_id            
     */
    function getWeixinQrcodeTemplate($shop_id);

    /**
     * 选择模板
     *
     * @param unknown $shop_id            
     * @param unknown $id            
     */
    function modifyWeixinQrcodeTemplateCheck($shop_id, $id);

    /**
     * 添加店铺的推广二维码模板
     *
     * @param unknown $instance_id            
     * @param unknown $background            
     * @param unknown $nick_font_color            
     * @param unknown $nick_font_size            
     * @param unknown $is_logo_show            
     * @param unknown $header_left            
     * @param unknown $header_top            
     * @param unknown $name_left            
     * @param unknown $name_top            
     * @param unknown $logo_left            
     * @param unknown $logo_top            
     * @param unknown $code_left            
     * @param unknown $code_top            
     * @param unknown $template_url            
     */
    function addWeixinQrcodeTemplate($instance_id, $background, $nick_font_color, $nick_font_size, $is_logo_show, $header_left, $header_top, $name_left, $name_top, $logo_left, $logo_top, $code_left, $code_top, $template_url);

    /**
     * 更新店铺的推广二维码模板
     *
     * @param unknown $id            
     * @param unknown $instance_id            
     * @param unknown $background            
     * @param unknown $nick_font_color            
     * @param unknown $nick_font_size            
     * @param unknown $is_logo_show            
     * @param unknown $header_left            
     * @param unknown $header_top            
     * @param unknown $name_left            
     * @param unknown $name_top            
     * @param unknown $logo_left            
     * @param unknown $logo_top            
     * @param unknown $code_left            
     * @param unknown $code_top            
     * @param unknown $template_url            
     */
    function updateWeixinQrcodeTemplate($id, $instance_id, $background, $nick_font_color, $nick_font_size, $is_logo_show, $header_left, $header_top, $name_left, $name_top, $logo_left, $logo_top, $code_left, $code_top, $template_url);

    /**
     * 删除店铺推广二维码模板
     *
     * @param unknown $id            
     * @param unknown $instance_id            
     */
    function deleteWeixinQrcodeTemplate($id, $instance_id);

    /**
     * 查询单个的模板信息
     *
     * @param unknown $id            
     * @param unknown $instance_id            
     */
    function getDetailWeixinQrcodeTemplate($id);

    /**
     * 更新用户的推广二维码模板
     *
     * @param unknown $shop_id            
     * @param unknown $uid            
     */
    function updateMemberQrcodeTemplate($shop_id, $uid);

    /**
     * 获取微信一键关注设置
     *
     * @param unknown $instance_id            
     */
    function getInstanceOneKeySubscribe($instance_id);

    /**
     * 设置一键关注
     *
     * @param unknown $instance_id            
     * @param unknown $url            
     */
    function setInsanceOneKeySubscribe($instance_id, $url);

    /**
     * 通过微信获取用户对应实例openid
     *
     * @param unknown $instance_id            
     */
    function getUserOpenid($instance_id);
    /**
     * 获取粉丝个数
     * @param unknown $condition
     */
    function getWeixinFansCount($condition);
    /**
     * 获取会员微信关注信息
     * @param unknown $uid
     * @param unknown $instance_id
     */
    function getUserWeixinSubscribeData($uid, $instance_id);
    
    /**
     * 添加 用户消息记录
     * @param unknown $openid
     * @param unknown $content
     * @param unknown $msg_type
     */
    function addUserMessage($openid, $content, $msg_type);
    
    /**
     * 添加 用户消息 回复 记录
     * @param unknown $msg_id
     * @param unknown $replay_uid
     * @param unknown $replay_type
     * @param unknown $content
     */
    function addUserMessageReplay($msg_id, $replay_uid, $replay_type, $content);
}

