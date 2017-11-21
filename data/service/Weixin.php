<?php
/**
 * Weixin.php
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
 * @date : 2015.4.24
 * @version : v1.0.0.0
 */
namespace data\service;

use data\api\IWeixin;
use data\extend\WchatOauth;
use data\model\UserModel;
use data\model\WeixinAuthModel;
use data\model\WeixinDefaultReplayModel;
use data\model\WeixinFansModel;
use data\model\WeixinFollowReplayModel;
use data\model\WeixinKeyReplayModel;
use data\model\WeixinMediaItemModel;
use data\model\WeixinMediaModel;
use data\model\WeixinMenuModel;
use data\model\WeixinOneKeySubscribeModel;
use data\model\WeixinQrcodeConfigModel;
use data\model\WeixinQrcodeTemplateModel;
use data\model\WeixinUserMsgModel;
use data\model\WeixinUserMsgReplayModel;
use data\service\BaseService;
use think\Log;

class Weixin extends BaseService implements IWeixin
{

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::getWeixinMenuList()
     */
    public function getWeixinMenuList($instance_id, $pid = '')
    {
        $weixin_menu = new WeixinMenuModel();
        if ($pid == '') {
            $list = $weixin_menu->pageQuery(1, 0, [
                'instance_id' => $instance_id
            ], 'sort', '*');
        } else {
            $list = $weixin_menu->pageQuery(1, 0, [
                'instance_id' => $instance_id,
                'pid' => $pid
            ], 'sort', '*');
        }
        return $list['data'];
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::addWeixinMenu()
     */
    public function addWeixinMenu($instance_id, $menu_name, $ico, $pid, $menu_event_type, $menu_event_url, $media_id, $sort)
    {
        $weixin_menu = new WeixinMenuModel();
        $data = array(
            'instance_id' => $instance_id,
            'menu_name' => $menu_name,
            'ico' => $ico,
            'pid' => $pid,
            'menu_event_type' => $menu_event_type,
            'menu_event_url' => $menu_event_url,
            'media_id' => $media_id,
            'sort' => $sort,
            'create_date' => time()
        );
        $weixin_menu->save($data);
        return $weixin_menu->menu_id;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::updateWeixinMenu()
     */
    public function updateWeixinMenu($menu_id, $instance_id, $menu_name, $ico, $pid, $menu_event_type, $menu_event_url, $media_id)
    {
        $weixin_menu = new WeixinMenuModel();
        $data = array(
            'instance_id' => $instance_id,
            'menu_name' => $menu_name,
            'ico' => $ico,
            'pid' => $pid,
            'menu_event_type' => $menu_event_type,
            'menu_event_url' => $menu_event_url,
            'media_id' => $media_id,
            'modify_date' => time()
        );
        $retval = $weixin_menu->save($data, [
            "menu_id" => $menu_id
        ]);
        return $retval;
        // TODO Auto-generated method stub
    }

    /**
     * 修改菜单排序
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::updateWeixinMenuSort()
     */
    public function updateWeixinMenuSort($menu_id_arr)
    {
        $weixin_menu = new WeixinMenuModel();
        $retval = 0;
        foreach ($menu_id_arr as $k => $v) {
            $data = array(
                'sort' => $k + 1,
                'modify_date' => time()
            );
            $retval += $weixin_menu->save($data, [
                "menu_id" => $v
            ]);
        }
        return $retval;
    }

    /**
     * 修改菜单名称，目前用的是updateWeixinMenu，还没有单独修改
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::updateWeixinMenuName()
     */
    public function updateWeixinMenuName($menu_id, $menu_name)
    {
        $weixin_menu = new WeixinMenuModel();
        
        $retval = $weixin_menu->save([
            "menu_name" => $menu_name
        ], [
            "menu_id" => $menu_id
        ]);
        return $retval;
    }

    /**
     * 修改跳转链接地址
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::updateWeixinMenuUrl()
     */
    public function updateWeixinMenuUrl($menu_id, $menu_event_url)
    {
        $weixin_menu = new WeixinMenuModel();
        
        $retval = $weixin_menu->save([
            "menu_event_url" => $menu_event_url
        ], [
            "menu_id" => $menu_id
        ]);
        return $retval;
    }

    /**
     * 修改菜单类型，1：文本，2：单图文，3：多图文
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::updateWeixinMenuEventType()
     */
    public function updateWeixinMenuEventType($menu_id, $menu_event_type)
    {
        $weixin_menu = new WeixinMenuModel();
        
        $retval = $weixin_menu->save([
            "menu_event_type" => $menu_event_type
        ], [
            "menu_id" => $menu_id
        ]);
        return $retval;
    }

    /**
     * 修改图文消息
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::updateWeiXinMenuMessage()
     */
    public function updateWeiXinMenuMessage($menu_id, $media_id, $menu_event_type)
    {
        $weixin_menu = new WeixinMenuModel();
        $retval = $weixin_menu->save([
            "media_id" => $media_id,
            "menu_event_type" => $menu_event_type
        ], [
            "menu_id" => $menu_id
        ]);
        return $retval;
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::addMenuHits()
     */
    public function addMenuHits($menu_id)
    {
        
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::getWeixinMenuDetail()
     */
    public function getWeixinMenuDetail($menu_id)
    {
        $weixin_menu = new WeixinMenuModel();
        $data = $weixin_menu->get($menu_id);
        return $data;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::addWeixinAuth()
     */
    public function addWeixinAuth($instance_id, $authorizer_appid, $authorizer_refresh_token, $authorizer_access_token, $func_info, $nick_name, $head_img, $user_name, $alias, $qrcode_url)
    {
        $weixin_auth = new WeixinAuthModel();
        $data = array(
            'instance_id' => $instance_id,
            'authorizer_appid' => $authorizer_appid,
            'authorizer_refresh_token' => $authorizer_refresh_token,
            'authorizer_access_token' => $authorizer_access_token,
            'func_info' => $func_info,
            'nick_name' => $nick_name,
            'head_img' => $head_img,
            'user_name' => $user_name,
            'alias' => $alias,
            'qrcode_url' => $qrcode_url,
            'auth_time' => time()
        );
        $count = $weixin_auth->where([
            'instance_id' => $instance_id
        ])->count();
        if ($count == 0) {
            $weixin_auth = new WeixinAuthModel();
            $retval = $weixin_auth->save($data);
        } else {
            $weixin_auth = new WeixinAuthModel();
            $retval = $weixin_auth->save($data, [
                'instance_id' => $instance_id
            ]);
        }
        
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::addWeixinFans()
     */
    public function addWeixinFans($source_uid, $instance_id, $nickname, $nickname_decode, $headimgurl, $sex, $language, $country, $province, $city, $district, $openid, $groupid, $is_subscribe, $memo, $unionid)
    {
        $weixin_fans = new WeixinFansModel();
        $count = $weixin_fans->where([
            'openid' => $openid
        ])->count();
        if (! empty($this->uid)) {
            $uid = $this->uid;
        } else {
            $uid = 0;
        }
        $data = array(
            'uid' => $uid,
            'instance_id' => $instance_id,
            'nickname' => $nickname,
            'nickname_decode' => $nickname_decode,
            'headimgurl' => $headimgurl,
            'sex' => $sex,
            'language' => $language,
            'country' => $country,
            'province' => $province,
            'city' => $city,
            'district' => $district,
            'openid' => $openid,
            'groupid' => $groupid,
            'is_subscribe' => $is_subscribe,
            'update_date' => time(),
            'memo' => $memo,
            'unionid' => $unionid
        );
        if ($count == 0) {
            $weixin_fans = new WeixinFansModel();
            $data['source_uid'] = $source_uid;
            $data['subscribe_date'] = time();
            $retval = $weixin_fans->save($data);
        } else {
            $weixin_fans = new WeixinFansModel();
            $retval = $weixin_fans->save($data, [
                'openid' => $openid
            ]);
        }
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::addFollowReplay()
     */
    public function addFollowReplay($instance_id, $replay_media_id, $sort)
    {
        $weixin_follow_replay = new WeixinFollowReplayModel();
        $data = array(
            'instance_id' => $instance_id,
            'reply_media_id' => $replay_media_id,
            'sort' => $sort,
            'create_time' => time()
        );
        $weixin_follow_replay->save($data);
        return $weixin_follow_replay->id;
        // TODO Auto-generated method stub
    }

    public function addDefaultReplay($instance_id, $replay_media_id, $sort)
    {
        $weixin_default_replay = new WeixinDefaultReplayModel();
        $data = array(
            'instance_id' => $instance_id,
            'reply_media_id' => $replay_media_id,
            'sort' => $sort,
            'create_time' => time()
        );
        $weixin_default_replay->save($data);
        return $weixin_default_replay->id;
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::updateFollowReplay()
     */
    public function updateFollowReplay($id, $instance_id, $replay_media_id, $sort)
    {
        $weixin_follow_replay = new WeixinFollowReplayModel();
        $data = array(
            'instance_id' => $instance_id,
            'reply_media_id' => $replay_media_id,
            'sort' => $sort,
            'modify_time' => time()
        );
        $retval = $weixin_follow_replay->save($data, [
            'id' => $id
        ]);
        return $retval;
        // TODO Auto-generated method stub
    }

    public function updateDefaultReplay($id, $instance_id, $replay_media_id, $sort)
    {
        $weixin_default_replay = new WeixinDefaultReplayModel();
        $data = array(
            'instance_id' => $instance_id,
            'reply_media_id' => $replay_media_id,
            'sort' => $sort,
            'modify_time' => time()
        );
        $retval = $weixin_default_replay->save($data, [
            'id' => $id
        ]);
        return $retval;
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::addKeyReplay()
     */
    public function addKeyReplay($instance_id, $key, $match_type, $replay_media_id, $sort)
    {
        $weixin_key_replay = new WeixinKeyReplayModel();
        $data = array(
            'instance_id' => $instance_id,
            'key' => $key,
            'match_type' => $match_type,
            'reply_media_id' => $replay_media_id,
            'sort' => $sort,
            'create_time' => time()
        );
        $weixin_key_replay->save($data);
        return $weixin_key_replay->id;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::updateKeyReplay()
     */
    public function updateKeyReplay($id, $instance_id, $key, $match_type, $replay_media_id, $sort)
    {
        $weixin_key_replay = new WeixinKeyReplayModel();
        $data = array(
            'instance_id' => $instance_id,
            'key' => $key,
            'match_type' => $match_type,
            'reply_media_id' => $replay_media_id,
            'sort' => $sort,
            'create_time' => time()
        );
        $retval = $weixin_key_replay->save($data, [
            'id' => $id
        ]);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::getKeyReplayList()
     */
    public function getKeyReplayList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $weixin_key_replay = new WeixinKeyReplayModel();
        $list = $weixin_key_replay->pageQuery($page_index, $page_size, $condition, $order, '*');
        return $list;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::getFollowReplayList()
     */
    public function getFollowReplayList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $weixin_follow_replay = new WeixinFollowReplayModel();
        $list = $weixin_follow_replay->pageQuery($page_index, $page_size, $condition, $order, '*');
        return $list;
        // TODO Auto-generated method stub
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::getDefaultReplayList()
     */
    public function getDefaultReplayList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $weixin_default_replay = new WeixinDefaultReplayModel();
        $list = $weixin_default_replay->pageQuery($page_index, $page_size, $condition, $order, '*');
        return $list;
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::getWeixinFansList()
     */
    public function getWeixinFansList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $weixin_fans = new WeixinFansModel();
        $list = $weixin_fans->pageQuery($page_index, $page_size, $condition, $order, '*');
        return $list;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::getWeixinAuthList()
     */
    public function getWeixinAuthList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $weixin_auth = new WeixinAuthModel();
        $list = $weixin_auth->pageQuery($page_index, $page_size, $condition, $order, '*');
        return $list;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::addWeixinMedia()
     * $content格式 = 标题,作者,封面图片,显示在正文,摘要,正文,链接地址;标题,作者,封面图片,显示在正文,摘要,正文,链接地址
     */
    public function addWeixinMedia($title, $instance_id, $type, $sort, $content)
    {
        $weixin_media = new WeixinMediaModel();
        $weixin_media->startTrans();
        try {
            $data_media = array(
                'title' => $title,
                'instance_id' => $instance_id,
                'type' => $type,
                'sort' => $sort,
                'create_time' => time()
            );
            $weixin_media->save($data_media);
            $media_id = $weixin_media->media_id;
            if ($type == 1) {
                $this->addWeixinMediaItem($media_id, $title, '', '', '', '', '', '', 0);
            } else 
                if ($type == 2) {
                    $info = explode('`|`', $content);
                    $this->addWeixinMediaItem($media_id, $info[0], $info[1], $info[2], $info[3], $info[4], $info[5], $info[6], 0);
                } else 
                    if ($type == 3) {
                        $list = explode('`$`', $content);
                        foreach ($list as $k => $v) {
                            $arr = Array();
                            $arr = explode('`|`', $v);
                            $this->addWeixinMediaItem($media_id, $arr[0], $arr[1], $arr[2], $arr[3], $arr[4], $arr[5], $arr[6], 0);
                        }
                    }
            $weixin_media->commit();
            return 1;
        } catch (\Exception $e) {
            $weixin_media->rollback();
            return $e->getMessage();
        }
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::addWeixinMediaItem()
     */
    public function addWeixinMediaItem($media_id, $title, $author, $cover, $show_cover_pic, $summary, $content, $content_source_url, $sort)
    {
        $weixin_media_item = new WeixinMediaItemModel();
        $data = array(
            'media_id' => $media_id,
            'title' => $title,
            'author' => $author,
            'cover' => $cover,
            'show_cover_pic' => $show_cover_pic,
            'summary' => $summary,
            'content' => $content,
            'content_source_url' => $content_source_url,
            'sort' => $sort
        );
        $retval = $weixin_media_item->save($data);
        return $retval;
        
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::getWeixinMediaList()
     */
    public function getWeixinMediaList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $weixin_media = new WeixinMediaModel();
        $list = $weixin_media->pageQuery($page_index, $page_size, $condition, $order, '*');
        if (! empty($list)) {
            foreach ($list['data'] as $k => $v) {
                $weixin_media_item = new WeixinMediaItemModel();
                $item_list = $weixin_media_item->getQuery([
                    'media_id' => $v['media_id']
                ], 'title', '');
                $list['data'][$k]['item_list'] = $item_list;
            }
        }
        return $list;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::getWeixinMediaDetail()
     */
    public function getWeixinMediaDetail($media_id)
    {
        $weixin_media = new WeixinMediaModel();
        $weixin_media_info = $weixin_media->get($media_id);
        if (! empty($weixin_media_info)) {
            $weixin_media_item = new WeixinMediaItemModel();
            $item_list = $weixin_media_item->getQuery([
                'media_id' => $media_id
            ], '*', '');
            $weixin_media_info['item_list'] = $item_list;
        }
        return $weixin_media_info;
    }

    /**
     * 根据图文消息id查询
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::getWeixinMediaDetailByMediaId()
     */
    public function getWeixinMediaDetailByMediaId($media_id)
    {
        $weixin_media_item = new WeixinMediaItemModel();
        $item_list = $weixin_media_item->getInfo([
            'id' => $media_id
        ], '*');
        
        if (! empty($item_list)) {
            
            // 主表
            $weixin_media = new WeixinMediaModel();
            $weixin_media_info["media_parent"] = $weixin_media->getInfo([
                "media_id" => $item_list["media_id"]
            ], "*");
            
            // 微信配置
            $weixin_auth = new WeixinAuthModel();
            $weixin_media_info["weixin_auth"] = $weixin_auth->getInfo([
                "instance_id" => $weixin_media_info["media_parent"]["instance_id"]
            ], "*");
            
            $weixin_media_info["media_item"] = $item_list;
            
            // 更新阅读次数
            $res = $weixin_media_item->save([
                "hits" => ($item_list["hits"] + 1)
            ], [
                "id" => $media_id
            ]);
            
            return $weixin_media_info;
        }
        return null;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::getShopidByAuthorAppid()
     */
    public function getShopidByAuthorAppid($author_appid)
    {
        $weixin_auth = new WeixinAuthModel();
        $instance_id = $weixin_auth->getInfo([
            'authorizer_appid' => $author_appid
        ], 'instance_id');
        if (! empty($instance_id['instance_id'])) {
            return $instance_id['instance_id'];
        } else {
            return '';
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::getWeixinUidByOpenid()
     */
    public function getWeixinUidByOpenid($openid)
    {
        $weixin_fans = new WeixinFansModel();
        $uid = $weixin_fans->getInfo([
            'openid' => $openid
        ], 'uid');
        if (! empty($uid['uid'])) {
            return $uid['uid'];
        } else {
            return 0;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::getWeixinInfoByAppid()
     */
    public function getWeixinInfoByAppid($author_appid)
    {
        $weixin_auth = new WeixinAuthModel();
        $info = $weixin_auth->getInfo([
            'authorizer_appid' => $author_appid
        ], '*');
        return $info;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::WeixinUserUnsubscribe()
     */
    public function WeixinUserUnsubscribe($instance_id, $openid)
    {
        $weixin_fans = new WeixinFansModel();
        $data = array(
            'is_subscribe' => 0,
            'unsubscribe_date' => time()
        );
        
        $retval = $weixin_fans->save($data, [
            'openid' => $openid
        ]);
        return $retval;
    }

    public function getWeixinAuthInfo($instance_id)
    {
        $weixin_auth = new WeixinAuthModel();
        $data = $weixin_auth->getInfo([
            'instance_id' => $instance_id
        ], '*');
        return $data;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::getInstanceWchatMenu()
     */
    public function getInstanceWchatMenu($instance_id)
    {
        $weixin_menu = new WeixinMenuModel();
        $foot_menu = $weixin_menu->getQuery([
            'instance_id' => $instance_id,
            'pid' => 0
        ], '*', 'sort');
        if (! empty($foot_menu)) {
            foreach ($foot_menu as $k => $v) {
                $foot_menu[$k]['child'] = '';
                $second_menu = $weixin_menu->getQuery([
                    'instance_id' => $instance_id,
                    'pid' => $v['menu_id']
                ], '*', 'sort');
                ;
                if (! empty($second_menu)) {
                    $foot_menu[$k]['child'] = $second_menu;
                    $foot_menu[$k]['child_count'] = count($second_menu);
                } else {
                    $foot_menu[$k]['child_count'] = 0;
                }
            }
        }
        return $foot_menu;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::updateInstanceMenuToWeixin()
     */
    public function updateInstanceMenuToWeixin($instance_id)
    {
        $menu = array();
        $menu_list = $this->getInstanceWchatMenu($instance_id);
        if (! empty($menu_list)) {
            
            foreach ($menu_list as $k => $v) {
                if (! empty($v)) {
                    $menu_item = array(
                        'name' => ''
                    );
                    $menu_item['name'] = $v['menu_name'];
                    
                    // $menu_item['sub_menu'] = array();
                    if (! empty($v['child'])) {
                        
                        foreach ($v['child'] as $k_child => $v_child) {
                            if (! empty($v_child)) {
                                $sub_menu = array();
                                $sub_menu['name'] = $v_child['menu_name'];
                                // $sub_menu['sub_menu'] = array();
                                if ($v_child['menu_event_type'] == 1) {
                                    $sub_menu['type'] = 'view';
                                    $sub_menu['url'] = $v_child['menu_event_url'];
                                } else {
                                    $sub_menu['type'] = 'click';
                                    $sub_menu['key'] = $v_child['menu_id'];
                                }
                                
                                $menu_item['sub_button'][] = $sub_menu;
                            }
                        }
                    } else {
                        if ($v['menu_event_type'] == 1) {
                            $menu_item['type'] = 'view';
                            $menu_item['url'] = $v['menu_event_url'];
                        } else {
                            $menu_item['type'] = 'click';
                            $menu_item['key'] = $v['menu_id'];
                        }
                    }
                    $menu[] = $menu_item;
                }
            }
        }
        $menu_array = array();
        $menu_array['button'] = array();
        foreach ($menu as $k => $v) {
            $menu_array['button'][] = $v;
        }
        // 汉字不编码
        $menu_array = json_encode($menu_array);
        // 链接不转义
        $menu_array = preg_replace_callback("/\\\u([0-9a-f]{4})/i", create_function('$matches', 'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'), $menu_array);
        return $menu_array;
    }

    /**
     * // 构造media数据并返回
     * // media_type 消息素材类型1文本 2单图文 3多图文',(non-PHPdoc)
     *
     * @see \ata\api\IWeixin::getMediaWchatStruct()
     */
    public function getMediaWchatStruct($media_info)
    {
        switch ($media_info['type']) {
            case "1":
                $contentStr = trim($media_info['title']);
                break;
            case "2":
                $pic_url = "";
                if (strstr($media_info['item_list'][0]['cover'], "http")) {
                    $pic_url = $media_info['item_list'][0]['cover'];
                } else {
                    $pic_url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $media_info['item_list'][0]['cover'];
                }
                $contentStr[] = array(
                    "Title" => $media_info['item_list'][0]['title'],
                    "Description" => $media_info['item_list'][0]['summary'],
                    "PicUrl" => $pic_url,
                    "Url" => __URL(__URL__ . '/wap/wchat/templateMessage?media_id=' . $media_info['item_list'][0]['id'])
                );
                break;
            case "3":
                $contentStr = array();
                foreach ($media_info['item_list'] as $k => $v) {
                    $pic_url = "";
                    if (strstr($v['cover'], "http")) {
                        $pic_url = $v['cover'];
                    } else {
                        $pic_url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $v['cover'];
                    }
                    $contentStr[$k] = array(
                        "Title" => $v['title'],
                        "Description" => $v['summary'],
                        "PicUrl" => $pic_url,
                        "Url" => __URL(__URL__ . '/wap/wchat/templateMessage?media_id=' . $v['id'])
                    );
                }
                break;
            default:
                $contentStr = "";
                break;
        }
        return $contentStr;
    }

    /**
     * 获取关键字回复
     *
     * @param unknown $key_words            
     */
    public function getWhatReplay($instance_id, $key_words)
    {
        $weixin_key_replay = new WeixinKeyReplayModel();
        // 全部匹配
        $condition = array(
            'instance_id' => $instance_id,
            'key' => $key_words,
            'match_type' => 2
        );
        $info = $weixin_key_replay->getInfo($condition, '*');
        if (empty($info)) {
            // 模糊匹配
            $condition = array(
                'instance_id' => $instance_id,
                'key' => array(
                    'LIKE',
                    '%' . $key_words . '%'
                ),
                'match_type' => 1
            );
            $info = $weixin_key_replay->getInfo($condition, '*');
        }
        if (! empty($info)) {
            $media_detail = $this->getWeixinMediaDetail($info['reply_media_id']);
            $content = $this->getMediaWchatStruct($media_detail);
            return $content;
        } else {
            return '';
        }
    }

    /**
     * 获取关注回复
     *
     * @param unknown $instance_id            
     * @return unknown|string
     */
    public function getSubscribeReplay($instance_id)
    {
        $weixin_flow_replay = new WeixinFollowReplayModel();
        $info = $weixin_flow_replay->getInfo([
            'instance_id' => $instance_id
        ], '*');
        if (! empty($info)) {
            $media_detail = $this->getWeixinMediaDetail($info['reply_media_id']);
            $content = $this->getMediaWchatStruct($media_detail);
            return $content;
        } else {
            return '';
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::getDefaultReplay()
     */
    public function getDefaultReplay($instance_id)
    {
        $weixin_default_replay = new WeixinDefaultReplayModel();
        $info = $weixin_default_replay->getInfo([
            'instance_id' => $instance_id
        ], '*');
        if (! empty($info)) {
            $media_detail = $this->getWeixinMediaDetail($info['reply_media_id']);
            $content = $this->getMediaWchatStruct($media_detail);
            return $content;
        } else {
            return '';
        }
    }

    /**
     * 获取会员 微信公众号二维码
     *
     * @see \ata\api\IUser::getUserWchatQrcode()
     */
    public function getUserWchatQrcode($uid, $instance_id)
    {
        $weixin_auth = new WchatOauth();
        $qrcode_url = $weixin_auth->ever_qrcode($uid);
        return $qrcode_url;
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::getWeixinQrcodeConfig()
     */
    public function getWeixinQrcodeConfig($instance_id, $uid)
    {
        $user = new UserModel();
        $userinfo = $user->getInfo([
            "uid" => $uid
        ]);
        $qrcode_template_id = $userinfo["qrcode_template_id"];
        $weixin_qrcode = new WeixinQrcodeTemplateModel();
        if ($qrcode_template_id == 0 || $qrcode_template_id == null) {
            $weixin_obj = $weixin_qrcode->getInfo([
                "instance_id" => $instance_id,
                "is_check" => 1
            ], "*");
        } else {
            $weixin_obj = $weixin_qrcode->getInfo([
                "instance_id" => $instance_id,
                "id" => $qrcode_template_id
            ], "*");
        }
        
        if (empty($weixin_obj)) {
            $weixin_obj = $weixin_qrcode->getInfo([
                "instance_id" => $instance_id,
                "is_remove" => 0
            ], "*");
        }
        return $weixin_obj;
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixin::updateWeixinQrcodeConfig()
     */
    public function updateWeixinQrcodeConfig($instance_id, $background, $nick_font_color, $nick_font_size, $is_logo_show, $header_left, $header_top, $name_left, $name_top, $logo_left, $logo_top, $code_left, $code_top)
    {
        $weixin_qrcode = new WeixinQrcodeConfigModel();
        $num = $weixin_qrcode->where([
            'instance_id' => $instance_id
        ])->count();
        if ($num > 0) {
            $data = array(
                'background' => $background,
                'nick_font_color' => $nick_font_color,
                'nick_font_size' => $nick_font_size,
                'is_logo_show' => $is_logo_show,
                'header_left' => $header_left . 'px',
                'header_top' => $header_top . 'px',
                'name_left' => $name_left . 'px',
                'name_top' => $name_top . 'px',
                'logo_left' => $logo_left . 'px',
                'logo_top' => $logo_top . 'px',
                'code_left' => $code_left . 'px',
                'code_top' => $code_top . 'px'
            );
            $res = $weixin_qrcode->save($data, [
                'instance_id' => $instance_id
            ]);
        } else {
            $data = array(
                'instance_id' => $instance_id,
                'background' => $background,
                'nick_font_color' => $nick_font_color,
                'nick_font_size' => $nick_font_size,
                'is_logo_show' => $is_logo_show,
                'header_left' => $header_left . 'px',
                'header_top' => $header_top . 'px',
                'name_left' => $name_left . 'px',
                'name_top' => $name_top . 'px',
                'logo_left' => $logo_left . 'px',
                'logo_top' => $logo_top . 'px',
                'code_left' => $code_left . 'px',
                'code_top' => $code_top . 'px'
            );
            $weixin_qrcode->save($data);
            $res = 1;
        }
        return $res;
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::updateWeixinMedia()
     */
    public function updateWeixinMedia($media_id, $title, $instance_id, $type, $sort, $content)
    {
        $weixin_media = new WeixinMediaModel();
        $weixin_media->startTrans();
        try {
            // 先修改 图文消息表
            $data_media = array(
                'title' => $title,
                'instance_id' => $instance_id,
                'type' => $type,
                'sort' => $sort,
                'create_time' => time()
            );
            $weixin_media->save($data_media, [
                'media_id' => $media_id
            ]);
            // 修改 图文消息内容的时候 先删除了图文消息内容再添加一次
            $weixin_media_item = new WeixinMediaItemModel();
            $weixin_media_item->destroy([
                'media_id' => $media_id
            ]);
            if ($type == 1) {
                $this->addWeixinMediaItem($media_id, $title, '', '', '', '', '', '', 0);
            } else 
                if ($type == 2) {
                    $info = explode('`|`', $content);
                    $this->addWeixinMediaItem($media_id, $info[0], $info[1], $info[2], $info[3], $info[4], $info[5], $info[6], 0);
                } else 
                    if ($type == 3) {
                        $list = explode('`$`', $content);
                        foreach ($list as $k => $v) {
                            $arr = Array();
                            $arr = explode('`|`', $v);
                            $this->addWeixinMediaItem($media_id, $arr[0], $arr[1], $arr[2], $arr[3], $arr[4], $arr[5], $arr[6], 0);
                        }
                    }
            $weixin_media->commit();
            return 1;
        } catch (\Exception $e) {
            $weixin_media->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 删除图文消息
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::deleteWeixinMedia()
     */
    public function deleteWeixinMedia($media_id)
    {
        $res = 0;
        
        $weixin_media = new WeixinMediaModel();
        $res = $weixin_media->destroy([
            'media_id' => $media_id,
            'instance_id' => $this->instance_id
        ]);
        if ($res) {
            $weixin_media_item = new WeixinMediaItemModel();
            $retval = $weixin_media_item->destroy([
                'media_id' => $media_id
            ]);
        }
        
        return $res;
    }

    /**
     * 删除图文消息详情下列表
     */
    public function deleteWeixinMediaDetail($id)
    {
        $weixin_media_item = new WeixinMediaItemModel();
        $res = $weixin_media_item->where("id=$id")->delete();
        return $res;
    }

    /**
     * 删除微信自定义菜单
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::deleteWeixinMenu()
     */
    public function deleteWeixinMenu($menu_id)
    {
        $weixin_menu = new WeixinMenuModel();
        $res = $weixin_menu->where("menu_id=$menu_id or pid=$menu_id")->delete();
        return $res;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::getFollowReplayDetail()
     */
    public function getFollowReplayDetail($condition)
    {
        $weixin_follow_replay = new WeixinFollowReplayModel();
        $info = $weixin_follow_replay->get($condition);
        if ($info['reply_media_id'] > 0) {
            $info['media_info'] = $this->getWeixinMediaDetail($info['reply_media_id']);
        }
        return $info;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::getDefaultReplayDetail()
     */
    public function getDefaultReplayDetail($condition)
    {
        $weixin_default_replay = new WeixinDefaultReplayModel();
        $info = $weixin_default_replay->get($condition);
        if ($info['reply_media_id'] > 0) {
            $info['media_info'] = $this->getWeixinMediaDetail($info['reply_media_id']);
        }
        return $info;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::deleteFollowReplay()
     */
    public function deleteFollowReplay($instance_id)
    {
        $weixin_follow_replay = new WeixinFollowReplayModel();
        return $weixin_follow_replay->destroy([
            'instance_id' => $instance_id
        ]);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::deleteDefaultReplay()
     */
    public function deleteDefaultReplay($instance_id)
    {
        $weixin_default_replay = new WeixinDefaultReplayModel();
        return $weixin_default_replay->destroy([
            'instance_id' => $instance_id
        ]);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::getKeyReplyDetail($id)
     */
    public function getKeyReplyDetail($id)
    {
        $weixin_key_replay = new WeixinKeyReplayModel();
        $info = $weixin_key_replay->get($id);
        if ($info['reply_media_id'] > 0) {
            $info['media_info'] = $this->getWeixinMediaDetail($info['reply_media_id']);
        }
        return $info;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::deleteKeyReplay()
     */
    public function deleteKeyReplay($id)
    {
        $weixin_key_replay = new WeixinKeyReplayModel();
        return $weixin_key_replay->destroy($id);
    }

    /**
     * 得到店铺的推广二维码模板列表
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::getWeixinQrcodeTemplate()
     */
    public function getWeixinQrcodeTemplate($shop_id)
    {
        $weixin_qrcode_template = new WeixinQrcodeTemplateModel();
        return $weixin_qrcode_template->all(array(
            "instance_id" => $shop_id,
            "is_remove" => 0
        ));
    }

    /**
     * 将某个模板设置为最新默认模板
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IWeixin::modifyWeixinQrcodeTemplateValid()
     */
    public function modifyWeixinQrcodeTemplateCheck($shop_id, $id)
    {
        $weixin_qrcode_template = new WeixinQrcodeTemplateModel();
        $weixin_qrcode_template->where(array(
            "instance_id" => $shop_id
        ))->update(array(
            "is_check" => 0
        ));
        $retval = $weixin_qrcode_template->where(array(
            "instance_id" => $shop_id,
            "id" => $id
        ))->update(array(
            "is_check" => 1
        ));
        return $retval;
    }

    /**
     * 添加店铺推广二维码模板
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::addWeixinQrcodeTemplate()
     */
    public function addWeixinQrcodeTemplate($instance_id, $background, $nick_font_color, $nick_font_size, $is_logo_show, $header_left, $header_top, $name_left, $name_top, $logo_left, $logo_top, $code_left, $code_top, $template_url)
    {
        $weixin_qrcode = new WeixinQrcodeTemplateModel();
        $data = array(
            'instance_id' => $instance_id,
            'background' => $background,
            'nick_font_color' => $nick_font_color,
            'nick_font_size' => $nick_font_size,
            'is_logo_show' => $is_logo_show,
            'header_left' => $header_left . 'px',
            'header_top' => $header_top . 'px',
            'name_left' => $name_left . 'px',
            'name_top' => $name_top . 'px',
            'logo_left' => $logo_left . 'px',
            'logo_top' => $logo_top . 'px',
            'code_left' => $code_left . 'px',
            'code_top' => $code_top . 'px',
            'template_url' => $template_url
        );
        $weixin_query = $weixin_qrcode->getQuery([
            "instance_id" => $instance_id,
            "is_check" => 1
        ], "*", '');
        if (empty($weixin_query)) {
            $data["is_check"] = 1;
        }
        $res = $weixin_qrcode->save($data);
        return $weixin_qrcode->id;
    }

    /**
     * 更新模板
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::updateWeixinQrcodeTemplate()
     */
    public function updateWeixinQrcodeTemplate($id, $instance_id, $background, $nick_font_color, $nick_font_size, $is_logo_show, $header_left, $header_top, $name_left, $name_top, $logo_left, $logo_top, $code_left, $code_top, $template_url)
    {
        $weixin_qrcode = new WeixinQrcodeTemplateModel();
        $data = array(
            'instance_id' => $this->instance_id,
            'background' => $background,
            'nick_font_color' => $nick_font_color,
            'nick_font_size' => $nick_font_size,
            'is_logo_show' => $is_logo_show,
            'header_left' => $header_left . 'px',
            'header_top' => $header_top . 'px',
            'name_left' => $name_left . 'px',
            'name_top' => $name_top . 'px',
            'logo_left' => $logo_left . 'px',
            'logo_top' => $logo_top . 'px',
            'code_left' => $code_left . 'px',
            'code_top' => $code_top . 'px',
            'template_url' => $template_url
        );
        
        $res = $weixin_qrcode->save($data, [
            'id' => $id
        ]);
        return $res;
    }

    /**
     * 删除模板
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::deleteWeixinQrcodeTemplate()
     */
    public function deleteWeixinQrcodeTemplate($id, $instance_id)
    {
        $weixin_qrcode_template = new WeixinQrcodeTemplateModel();
        $retval = $weixin_qrcode_template->where(array(
            "instance_id" => $instance_id,
            "id" => $id
        ))->update(array(
            "is_remove" => 1
        ));
        return $retval;
    }

    /**
     * 查询单个模板的具体信息
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::getDetailWeixinQrcodeTemplate()
     */
    public function getDetailWeixinQrcodeTemplate($id)
    {
        if ($id == 0) {
            $template_obj = array(
                "background" => "",
                "nick_font_color" => "#2B2B2B",
                "nick_font_size" => "23",
                "is_logo_show" => 1,
                "header_left" => "59px",
                "header_top" => "15px",
                "name_left" => "150px",
                "name_top" => "13px",
                "name_top" => "120px",
                "logo_top" => "100px",
                "logo_left" => "120px",
                "code_left" => "70px",
                "code_top" => "300px"
            );
            return $template_obj;
        } else {
            $weixin_qrcode_template = new WeixinQrcodeTemplateModel();
            $template_obj = $weixin_qrcode_template->get($id);
            return $template_obj;
        }
    }

    /**
     * 用户更换 自己的推广二维码
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::updateMemberQrcodeTemplate()
     */
    public function updateMemberQrcodeTemplate($shop_id, $uid)
    {
        $user = new UserModel();
        $userinfo = $user->getInfo([
            "uid" => $uid
        ], "qrcode_template_id");
        $qrcode_template_id = $userinfo["qrcode_template_id"];
        $qrcode_template = new WeixinQrcodeTemplateModel();
        if ($qrcode_template_id == 0 || $qrcode_template_id == null) {
            $template_obj = $qrcode_template->getInfo([
                "instance_id" => $shop_id,
                "is_remove" => 0
            ], "*");
        } else {
            $condition["id"] = array(
                ">",
                $qrcode_template_id
            );
            $condition["instance_id"] = $shop_id;
            $condition["is_remove"] = 0;
            $template_obj = $qrcode_template->getInfo($condition, "*");
            if (empty($template_obj)) {
                $template_obj = $qrcode_template->getInfo([
                    "instance_id" => $shop_id,
                    "is_remove" => 0
                ], "*");
            }
        }
        if (! empty($template_obj)) {
            $user->where(array(
                "uid" => $uid
            ))->update(array(
                "qrcode_template_id" => $template_obj["id"]
            ));
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::getInstanceOneKeySubscribe()
     */
    public function getInstanceOneKeySubscribe($instance_id)
    {
        $weixin_subscribe = new WeixinOneKeySubscribeModel();
        $info = $weixin_subscribe->get($instance_id);
        if (empty($info)) {
            $data = array(
                'instance_id' => $instance_id,
                'url' => ''
            );
            $weixin_subscribe->save($data);
            $info = $weixin_subscribe->get($instance_id);
        }
        return $info;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::setInsanceOneKeySubscribe()
     */
    public function setInsanceOneKeySubscribe($instance_id, $url)
    {
        $weixin_subscribe = new WeixinOneKeySubscribeModel();
        $retval = $weixin_subscribe->save([
            'url' => $url
        ], [
            'instance_id' => $instance_id
        ]);
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::getUserOpenid()
     */
    public function getUserOpenid($instance_id)
    {}

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::getWeixinFansCount()
     */
    public function getWeixinFansCount($condition)
    {
        $weixin_fans = new WeixinFansModel();
        $count = $weixin_fans->where($condition)->count();
        return $count;
    }

    /**
     * 获取会员关注微信信息(non-PHPdoc)
     *
     * @see \ata\api\IWeixin::getUserWeixinSubscribeData()
     */
    public function getUserWeixinSubscribeData($uid, $instance_id)
    {
        // 查询会员信息
        $user = new UserModel();
        $user_info = $user->getInfo([
            'uid' => $uid
        ], 'wx_openid,wx_unionid');
        $fans_info = '';
        // 通过openid查询信息
        if (! empty($user_info['wx_openid'])) {
            $weixin_fans = new WeixinFansModel();
            $fans_info = $weixin_fans->getInfo([
                'openid' => $user_info['wx_openid']
            ]);
        }
        if (empty($fans_info) && ! empty($user_info['wx_unionid'])) {
            $weixin_fans = new WeixinFansModel();
            $fans_info = $weixin_fans->getInfo([
                'openid' => $user_info['wx_unionid']
            ]);
        }
        return $fans_info;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::addUserMessage()
     */
    public function addUserMessage($openid, $content, $msg_type)
    {
        $weixin_user_msg = new WeixinUserMsgModel();
        $uid = $this->getWeixinUidByOpenid($openid);
        $data = array(
            'uid' => $uid,
            'msg_type' => $msg_type,
            'content' => $content,
            'create_time' => time()
        );
        if ($uid > 0) {
            $weixin_user_msg->save($data);
            return $weixin_user_msg->msg_id;
        } else {
            return 0;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWeixin::addUserMessageReplay()
     */
    public function addUserMessageReplay($msg_id, $replay_uid, $replay_type, $content)
    {
        $weixin_user_msg_replay = new WeixinUserMsgReplayModel();
        $data = array(
            'msg_id' => $msg_id,
            'replay_uid' => $replay_uid,
            'replay_type' => $replay_type,
            'content' => $content,
            'replay_time' => time()
        );
        $weixin_user_msg_replay->save($data);
        return $weixin_user_msg_replay->replay_id;
    }
    /**
     * 更新粉丝信息
     * @param string $next_openid
     * @return mixed
     */
    public function UpdateWchatFansList($openid_array){
        $wchatOauth = new WchatOauth();
        $fans_list_info = $wchatOauth -> get_fans_info_list($openid_array);
        //获取微信粉丝列表
        if(isset($fans_list_info["errcode"]) && $fans_list_info["errcode"] < 0){
            return $fans_list_info;
        }else{
            foreach ($fans_list_info['user_info_list'] as $info){
                $province = filterStr($info["province"]);
                $city = filterStr($info["city"]);
                $nickname = filterStr($info['nickname']); 
                $nickname_decode = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $info['nickname']);
                $this->addWeixinFans(0, $this->instance_id, $nickname, $nickname_decode, $info["headimgurl"], $info["sex"], $info["language"], $info["country"], $province, $city, "", $info["openid"], $info["groupid"], $info["subscribe"], $info["remark"], $info["unionid"]);
            }
        }
        return array(
            'errcode'  => '0',
            'errorMsg' => 'success'
        );
        
    }
    /**
     * 获取微信所有openid
     */
    public function getWeixinOpenidList(){
        $wchatOauth = new WchatOauth();
        $res = $wchatOauth -> get_fans_list("");
        $openid_list = array();
        if(!empty($res['data']))
        {
            $openid_list = $res['data']['openid'];
            $wchatOauth = new WchatOauth();
            while($res['next_openid']){
                $res = $wchatOauth -> get_fans_list($res['next_openid']);
                if(!empty($res['data']))
                {
                    $openid_list = array_merge($openid_list,$res['data']['openid']);
                }
                
            }
            return array(
                'total' => $res['total'],
                'openid_list' => $openid_list,
                'errcode'  => '0',
                'errorMsg' => ''
            );
           
        }else{
            if(!empty($res["errcode"]))
            {
                return array(
                    'errcode'  => $res['errcode'],
                    'errorMsg' => $res['errmsg'],
                    'total'    => 0,
                    'openid_list' => ''
                );
            }else{
                return array(
                    'errcode'  => '-400001',
                    'errorMsg' => '当前无粉丝列表或者获取失败',
                    'total'    => 0,
                    'openid_list' => ''
                );
            }
         
        }
        
    }
}