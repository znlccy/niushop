<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 李广
// +----------------------------------------------------------------------
use \data\extend\QRcode as QRcode;
use data\extend\alisms\top\request\AlibabaAliqinFcSmsNumSendRequest;
use data\extend\alisms\top\TopClient;
use data\extend\email\Email;
use data\service\WebSite;
use think\Config;
use think\Hook;
use think\Request;
use think\response\Redirect;
use think\Route;
use data\extend\Barcode;
// 错误级别
// error_reporting(E_ERROR | E_WARNING | E_PARSE);
// 去除警告错误
error_reporting(E_ALL ^ E_NOTICE);
define("PAGESIZE", Config::get('paginate.list_rows'));
define("PAGESHOW", Config::get('paginate.list_showpages'));
define("PICTURESIZE", Config::get('paginate.picture_page_size'));
// 订单退款状态
define('ORDER_REFUND_STATUS', 11);
// 订单完成的状态
define('ORDER_COMPLETE_SUCCESS', 4);
define('ORDER_COMPLETE_SHUTDOWN', 5);
define('ORDER_COMPLETE_REFUND', - 2);

// 后台网站风格
define("STYLE_DEFAULT_ADMIN", "admin");
define("STYLE_BLUE_ADMIN", "adminblue");

// 评价图片存放路径
define("UPLOAD_COMMENT", UPLOAD . "/comment/");

// 插件目录
define('ADDON_PATH', ROOT_PATH . 'addons' . DS);
//数据库路径
define('DB_PATH', UPLOAD .'/dbspl');
//条形码存放路径
define("BAR_CODE", UPLOAD.'/barcode');
urlRoute();

/**
 * 配置pc端缓存
 */
function getShopCache()
{
    if (! Request::instance()->isAjax()) {
        $model = Request::instance()->module();
        $model = strtolower($model);
        $controller = Request::instance()->controller();
        $controller = strtolower($controller);
        $action = Request::instance()->action();
        $action = strtolower($action);
        if ($model == 'shop' && $controller == 'index' && $action = "index") {
            if (Request::instance()->isMobile()) {
                Redirect::create("wap/index/index");
            } else {
                Request::instance()->cache('__URL__', 1800);
            }
        }
        if ($model == 'shop' && $controller != 'goods' && $controller != 'member') {
            Request::instance()->cache('__URL__', 1800);
        }
        if ($model == 'shop' && $controller == 'goods' && $action == 'brandlist') {
            Request::instance()->cache('__URL__', 1800);
        }
    }
}

/**
 * 关闭站点
 */
function webClose($reason)
{
    if (Request::instance()->isMobile()) {
        echo "<meta charset='UTF-8'>
                    <div style='width:100%;margin:auto;margin-top:250px;    overflow: hidden;'>
                    	<img src='" . __ROOT__ . "/public/admin/images/error.png' style='display: inline-block;float: left;width:90%;margin:0 5%;'/>
                    	<span style='font-size: 36px; display: inline-block;width: 70%;color: #666;text-align:center;margin:-130px 15% 0 15%;'>" . $reason . "</span>
                    	</div>
                ";
    } else {
        echo "<meta charset='UTF-8'>
                    <div style='width:100%;margin:auto;margin-top:200px;overflow: hidden;'>
                    	<img src='" . __ROOT__ . "/public/admin/images/error.png' style='display: inline-block;float: left;width:40%;margin:0 30%;'/>
                    	<span style='font-size: 22px; display: inline-block; width:40%;color:#666;margin: -120px 15% 0 30%;text-align:center;font-weight:bold;'>" . $reason . "</span>
                    	</div>
                ";
    }
    
    exit();
}

/**
 * 获取手机端缓存
 */
function getWapCache()
{
    if (! Request::instance()->isAjax()) {
        $model = Request::instance()->module();
        $model = strtolower($model);
        $controller = Request::instance()->controller();
        $controller = strtolower($controller);
        $action = Request::instance()->action();
        $action = strtolower($action);
        // 店铺页面缓存8分钟
        if ($model == 'wap' && $controller == 'shop' && $action == 'index') {
            Request::instance()->cache('__URL__', 480);
        }
        if ($model == 'wap' && $controller != 'goods' && $controller != 'member') {
            Request::instance()->cache('__URL__', 1800);
        }
        if ($model == 'wap' && $controller == 'goods' && $action != 'brandlist') {
            Request::instance()->cache('__URL__', 1800);
        }
        if ($model == 'wap' && $controller == 'goods' && $action != 'goodsGroupList') {
            Request::instance()->cache('__URL__', 1800);
        }
    }
}

// 应用公共函数库
/**
 * 循环删除指定目录下的文件及文件夹
 *
 * @param string $dirpath
 *            文件夹路径
 */
function NiuDelDir($dirpath)
{
    $dh = opendir($dirpath);
    while (($file = readdir($dh)) !== false) {
        if ($file != "." && $file != "..") {
            $fullpath = $dirpath . "/" . $file;
            if (! is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                NiuDelDir($fullpath);
                rmdir($fullpath);
            }
        }
    }
    closedir($dh);
    $isEmpty = true;
    $dh = opendir($dirpath);
    while (($file = readdir($dh)) !== false) {
        if ($file != "." && $file != "..") {
            $isEmpty = false;
            break;
        }
    }
    return $isEmpty;
}

/**
 * 生成数据的返回值
 *
 * @param unknown $msg            
 * @param unknown $data            
 * @return multitype:unknown
 */
function AjaxReturn($err_code, $data = [])
{
    // return $retval;
    $rs = [
        'code' => $err_code,
        'message' => getErrorInfo($err_code)
    ];
    if (! empty($data))
        $rs['data'] = $data;
    return $rs;
}

/**
 * 图片上传函数返回上传的基本信息
 * 传入上传路径
 */
function uploadImage($path)
{
    $fileKey = key($_FILES);
    $file = request()->file($fileKey);
    if ($file === null) {
        return array(
            'error' => '上传文件不存在或超过服务器限制',
            'status' => '-1'
        );
    }
    $validate = new \think\Validate([
        [
            'fileMime',
            'fileMime:image/png,image/gif,image/jpeg,image/x-ms-bmp',
            '只允许上传jpg,gif,png,bmp类型的文件'
        ],
        [
            'fileExt',
            'fileExt:jpg,jpeg,gif,png,bmp',
            '只允许上传后缀为jpg,gif,png,bmp的文件'
        ],
        [
            'fileSize',
            'fileSize:2097152',
            '文件大小超出限制'
        ]
    ]); // 最大2M
    
    $data = [
        'fileMime' => $file,
        'fileSize' => $file,
        'fileExt' => $file
    ];
    if (! $validate->check($data)) {
        return array(
            'error' => $validate->getError(),
            'status' => - 1
        );
    }
    $save_path = './' . getUploadPath() . '/' . $path;
    $info = $file->rule('uniqid')->move($save_path);
    if ($info) {
        // 获取基本信息
        $result['ext'] = $info->getExtension();
        $result['pic_cover'] = $path . '/' . $info->getSaveName();
        $result['pic_name'] = $info->getFilename();
        $result['pic_size'] = $info->getSize();
        $img = \think\Image::open('./' . getUploadPath() . '/' . $result['pic_cover']);
        // var_dump($img);
        return $result;
    }
}

/**
 * 判断当前是否是微信浏览器
 */
function isWeixin()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 

    'MicroMessenger') !== false) {
        
        return 1;
    }
    
    return 0;
}

/**
 * 获取上传根目录
 *
 * @return Ambigous <\think\mixed, NULL, multitype:>
 */
function getUploadPath()
{
    $list = \think\config::get("view_replace_str.__UPLOAD__");
    return $list;
}

/**
 * 获取系统根目录
 */
function getRootPath()
{
    return dirname(dirname(dirname(dirname(__File__))));
}

/**
 * 通过第三方获取随机用户名
 *
 * @param unknown $type            
 */
function setUserNameOauth($type)
{
    $time = time();
    $name = $time . rand(100, 999);
    return $type . '_' . name;
}

/**
 * 获取标准二维码格式
 *
 * @param unknown $url            
 * @param unknown $path            
 * @param unknown $ext            
 */
function getQRcode($url, $path, $qrcode_name)
{
    if (! is_dir($path)) {
        $mode = intval('0777', 8);
        mkdir($path, $mode, true);
        chmod($path, $mode);
    }
    $path = $path . '/' . $qrcode_name . '.png';
    if (file_exists($path)) {
        unlink($path);
    }
    QRcode::png($url, $path, '', 4, 1);
    return $path;
}

/**
 * 根据HTTP请求获取用户位置
 */
function getUserLocation()
{
    $key = "16199cf2aca1fb54d0db495a3140b8cb"; // 高德地图key
    $url = "http://restapi.amap.com/v3/ip?key=$key";
    $json = file_get_contents($url);
    $obj = json_decode($json, true); // 转换数组
    $obj["message"] = $obj["status"] == 0 ? "失败" : "成功";
    return $obj;
}

/**
 * 根据 ip 获取 当前城市
 */
function get_city_by_ip()
{
    if (! empty($_SERVER["HTTP_CLIENT_IP"])) {
        $cip = $_SERVER["HTTP_CLIENT_IP"];
    } elseif (! empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (! empty($_SERVER["REMOTE_ADDR"])) {
        $cip = $_SERVER["REMOTE_ADDR"];
    } else {
        $cip = "";
    }
    $url = 'http://restapi.amap.com/v3/ip';
    $data = array(
        'output' => 'json',
        'key' => '16199cf2aca1fb54d0db495a3140b8cb',
        'ip' => $cip
    );
    
    $postdata = http_build_query($data);
    $opts = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    
    $context = stream_context_create($opts);
    
    $result = file_get_contents($url, false, $context);
    $res = json_decode($result, true);
    if (count($res['province']) == 0) {
        $res['province'] = '北京市';
    }
    if (! empty($res['province']) && $res['province'] == "局域网") {
        $res['province'] = '北京市';
    }
    if (count($res['city']) == 0) {
        $res['city'] = '北京市';
    }
    return $res;
}

/**
 * 颜色十六进制转化为rgb
 */
function hColor2RGB($hexColor)
{
    $color = str_replace('#', '', $hexColor);
    if (strlen($color) > 3) {
        $rgb = array(
            'r' => hexdec(substr($color, 0, 2)),
            'g' => hexdec(substr($color, 2, 2)),
            'b' => hexdec(substr($color, 4, 2))
        );
    } else {
        $color = str_replace('#', '', $hexColor);
        $r = substr($color, 0, 1) . substr($color, 0, 1);
        $g = substr($color, 1, 1) . substr($color, 1, 1);
        $b = substr($color, 2, 1) . substr($color, 2, 1);
        $rgb = array(
            'r' => hexdec($r),
            'g' => hexdec($g),
            'b' => hexdec($b)
        );
    }
    return $rgb;
}

/**
 * 制作推广二维码
 *
 * @param unknown $path
 *            二维码地址
 * @param unknown $thumb_qrcode中继二维码地址            
 * @param unknown $user_headimg
 *            头像
 * @param unknown $shop_logo
 *            店铺logo
 * @param unknown $user_name
 *            用户名
 * @param unknown $data
 *            画布信息 数组
 * @param unknown $create_path
 *            图片创建地址 没有的话不创建图片
 */
function showUserQecode($upload_path, $path, $thumb_qrcode, $user_headimg, $shop_logo, $user_name, $data, $create_path)
{
    
    // 暂无法生成
    if (! strstr($path, "http://") && ! strstr($path, "https://")) {
        if (! file_exists($path)) {
            $path = "public/static/images/template_qrcode.png";
        }
    }
    
    if (! file_exists($upload_path)) {
        $mode = intval('0777', 8);
        mkdir($upload_path, $mode, true);
    }
    
    // 定义中继二维码地址
    
    $image = \think\Image::open($path);
    // 生成一个固定大小为360*360的缩略图并保存为thumb_....jpg
    $image->thumb(288, 288, \think\Image::THUMB_CENTER)->save($thumb_qrcode);
    // 背景图片
    $dst = $data["background"];
    
    if (! strstr($dst, "http://") && ! strstr($dst, "https://")) {
        if (! file_exists($dst)) {
            $dst = "public/static/images/qrcode_bg/shop_qrcode_bg.png";
        }
    }
    
    // $dst = "http://pic107.nipic.com/file/20160819/22733065_150621981000_2.jpg";
    // 生成画布
    list ($max_width, $max_height) = getimagesize($dst);
    // $dests = imagecreatetruecolor($max_width, $max_height);
    $dests = imagecreatetruecolor(640, 1134);
    $dst_im = getImgCreateFrom($dst);
    imagecopy($dests, $dst_im, 0, 0, 0, 0, $max_width, $max_height);
    // ($dests, $dst_im, 0, 0, 0, 0, 640, 1134, $max_width, $max_height);
    imagedestroy($dst_im);
    // 并入二维码
    // $src_im = imagecreatefrompng($thumb_qrcode);
    $src_im = getImgCreateFrom($thumb_qrcode);
    $src_info = getimagesize($thumb_qrcode);
    // imagecopy($dests, $src_im, $data["code_left"] * 2, $data["code_top"] * 2, 0, 0, $src_info[0], $src_info[1]);
    imagecopy($dests, $src_im, $data["code_left"] * 2, $data["code_top"] * 2, 0, 0, $src_info[0], $src_info[1]);
    imagedestroy($src_im);
    // 并入用户头像
    
    if (! strstr($user_headimg, "http://") && ! strstr($user_headimg, "https://")) {
        if (! file_exists($user_headimg)) {
            $user_headimg = "public/static/images/qrcode_bg/head_img.png";
        }
    }
    $src_im_1 = getImgCreateFrom($user_headimg);
    $src_info_1 = getimagesize($user_headimg);
    // imagecopy($dests, $src_im_1, $data['header_left'] * 2, $data['header_top'] * 2, 0, 0, $src_info_1[0], $src_info_1[1]);
    // imagecopy($dests, $src_im_1, $data['header_left'] * 2, $data['header_top'] * 2, 0, 0, $src_info_1[0], $src_info_1[1]);
    imagecopyresampled($dests, $src_im_1, $data['header_left'] * 2, $data['header_top'] * 2, 0, 0, 80, 80, $src_info_1[0], $src_info_1[1]);
    imagedestroy($src_im_1);
    
    // 并入网站logo
    if ($data['is_logo_show'] == '1') {
        if (! strstr($shop_logo, "http://") && ! strstr($shop_logo, "https://")) {
            if (! file_exists($shop_logo)) {
                $shop_logo = "public/static/images/logo.png";
            }
        }
        $src_im_2 = getImgCreateFrom($shop_logo);
        $src_info_2 = getimagesize($shop_logo);
        // imagecopy($dests, $src_im_2, $data['logo_left'] * 2, $data['logo_top'] * 2, 0, 0, $src_info_2[0], $src_info_2[1]);
        imagecopyresampled($dests, $src_im_2, $data['logo_left'] * 2, $data['logo_top'] * 2, 0, 0, 200, 80, $src_info_2[0], $src_info_2[1]);
        imagedestroy($src_im_2);
    }
    // 并入用户姓名
    if ($user_name == "") {
        $user_name = "用户";
    }
    $rgb = hColor2RGB($data['nick_font_color']);
    $bg = imagecolorallocate($dests, $rgb['r'], $rgb['g'], $rgb['b']);
    $name_top_size = $data['name_top'] * 2 + $data['nick_font_size'];
    @imagefttext($dests, $data['nick_font_size'], 0, $data['name_left'] * 2, $name_top_size, $bg, "public/static/font/Microsoft.ttf", $user_name);
    header("Content-type: image/jpeg");
    if ($create_path == "") {
        imagejpeg($dests);
    } else {
        imagejpeg($dests, $create_path);
    }
}

/**
 * 把微信生成的图片存入本地
 *
 * @param [type] $username
 *            [用户名]
 * @param [string] $LocalPath
 *            [要存入的本地图片地址]
 * @param [type] $weixinPath
 *            [微信图片地址]
 *            
 * @return [string] [$LocalPath]失败时返回 FALSE
 */
function save_weixin_img($local_path, $weixin_path)
{
    $weixin_path_a = str_replace("https://", "http://", $weixin_path);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $weixin_path_a);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $r = curl_exec($ch);
    curl_close($ch);
    if (! empty($local_path) && ! empty($weixin_path_a)) {
        $msg = file_put_contents($local_path, $r);
    }
    return $local_path;
}
// 分类获取图片对象
function getImgCreateFrom($img_path)
{
    $ename = getimagesize($img_path);
    $ename = explode('/', $ename['mime']);
    $ext = $ename[1];
    switch ($ext) {
        case "png":
            
            $image = imagecreatefrompng($img_path);
            break;
        case "jpeg":
            
            $image = imagecreatefromjpeg($img_path);
            break;
        case "jpg":
            
            $image = imagecreatefromjpeg($img_path);
            break;
        case "gif":
            
            $image = imagecreatefromgif($img_path);
            break;
    }
    return $image;
}

/**
 * 生成流水号
 *
 * @return string
 */
function getSerialNo()
{
    $no_base = date("ymdhis", time());
    $serial_no = $no_base . rand(111, 999);
    return $serial_no;
}

/**
 * 删除图片文件
 *
 * @param unknown $img_path            
 */
function removeImageFile($img_path)
{
    // 检查图片文件是否存在
    if (file_exists($img_path)) {
        return unlink($img_path);
    } else {
        return false;
    }
}

/**
 * 阿里大于短信发送
 *
 * @param unknown $appkey            
 * @param unknown $secret            
 * @param unknown $signName            
 * @param unknown $smsParam            
 * @param unknown $send_mobile            
 * @param unknown $template_code            
 */
function aliSmsSend($appkey, $secret, $signName, $smsParam, $send_mobile, $template_code, $sms_type = 0)
{
    if ($sms_type == 0) {
        // 旧用户发送短信
        return aliSmsSendOld($appkey, $secret, $signName, $smsParam, $send_mobile, $template_code);
    } else {
        // 新用户发送短信
        return aliSmsSendNew($appkey, $secret, $signName, $smsParam, $send_mobile, $template_code);
    }
}

/**
 * 阿里大于旧用户发送短信
 *
 * @param unknown $appkey            
 * @param unknown $secret            
 * @param unknown $signName            
 * @param unknown $smsParam            
 * @param unknown $send_mobile            
 * @param unknown $template_code            
 * @return Ambigous <unknown, \ResultSet, mixed>
 */
function aliSmsSendOld($appkey, $secret, $signName, $smsParam, $send_mobile, $template_code)
{
    require_once 'data/extend/alisms/TopSdk.php';
    $c = new TopClient();
    $c->appkey = $appkey;
    $c->secretKey = $secret;
    $req = new AlibabaAliqinFcSmsNumSendRequest();
    $req->setExtend("");
    $req->setSmsType("normal");
    $req->setSmsFreeSignName($signName);
    $req->setSmsParam($smsParam);
    $req->setRecNum($send_mobile);
    $req->setSmsTemplateCode($template_code);
    $result = $resp = $c->execute($req);
    return $result;
}

/**
 * 阿里大于新用户发送短信
 *
 * @param unknown $appkey            
 * @param unknown $secret            
 * @param unknown $signName            
 * @param unknown $smsParam            
 * @param unknown $send_mobile            
 * @param unknown $template_code            
 */
function aliSmsSendNew($appkey, $secret, $signName, $smsParam, $send_mobile, $template_code)
{
    require_once 'data/extend/alisms_new/aliyun-php-sdk-core/Config.php';
    require_once 'data/extend/alisms_new/SendSmsRequest.php';
    // 短信API产品名
    $product = "Dysmsapi";
    // 短信API产品域名
    $domain = "dysmsapi.aliyuncs.com";
    // 暂时不支持多Region
    $region = "cn-hangzhou";
    $profile = DefaultProfile::getProfile($region, $appkey, $secret);
    DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
    $acsClient = new DefaultAcsClient($profile);
    
    $request = new SendSmsRequest();
    // 必填-短信接收号码
    $request->setPhoneNumbers($send_mobile);
    // 必填-短信签名
    $request->setSignName($signName);
    // 必填-短信模板Code
    $request->setTemplateCode($template_code);
    // 选填-假如模板中存在变量需要替换则为必填(JSON格式)
    $request->setTemplateParam($smsParam);
    // 选填-发送短信流水号
    $request->setOutId("0");
    // 发起访问请求
    $acsResponse = $acsClient->getAcsResponse($request);
    return $acsResponse;
}

/**
 * 发送邮件
 *
 * @param unknown $toemail            
 * @param unknown $title            
 * @param unknown $content            
 * @return boolean
 */
function emailSend($email_host, $email_id, $email_pass, $email_port, $email_is_security, $email_addr, $toemail, $title, $content, $shopName = "")
{
    $result = false;
    try {
        $mail = new Email();
        if (! empty($shopName)) {
            $mail->_shopName = $shopName;
        } else {
            $mail->_shopName = "NiuShop开源电商";
        }
        $mail->setServer($email_host, $email_id, $email_pass, $email_port, $email_is_security);
        $mail->setFrom($email_addr);
        $mail->setReceiver($toemail);
        $mail->setMail($title, $content);
        $result = $mail->sendMail();
    } catch (\Exception $e) {
        $result = false;
    }
    return $result;
}

/**
 * 执行钩子
 *
 * @param unknown $hookid            
 * @param string $params            
 */
function runhook($class, $tag, $params = null)
{
    $result = array();
    try {
        $result = Hook::exec("\\data\\extend\\hook\\" . $class, $tag, $params);
    } catch (\Exception $e) {
        $result["code"] = - 1;
        $result["message"] = "请求失败!";
    }
    return $result;
}

/**
 * 格式化字节大小
 *
 * @param number $size
 *            字节数
 * @param string $delimiter
 *            数字和单位分隔符
 * @return string 格式化后的带单位的大小
 * @author
 *
 */
function format_bytes($size, $delimiter = '')
{
    $units = array(
        'B',
        'KB',
        'MB',
        'GB',
        'TB',
        'PB'
    );
    for ($i = 0; $size >= 1024 && $i < 5; $i ++)
        $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 获取插件类的类名
 *
 * @param $name 插件名            
 * @param string $type
 *            返回命名空间类型
 * @param string $class
 *            当前类名
 * @return string
 */
function get_addon_class($name, $type = '', $class = null)
{
    $name = \think\Loader::parseName($name);
    if ($type == '' && $class == null) {
        $dir = ADDON_PATH . $name . '/core';
        if (is_dir($dir)) {
            // 目录存在
            $type = 'addons_index';
        } else {
            $type = 'addon_index';
        }
    }
    $class = \think\Loader::parseName(is_null($class) ? $name : $class, 1);
    switch ($type) {
        // 单独的插件addon 入口文件
        case 'addon_index':
            $namespace = "\\addons\\" . $name . "\\" . $class;
            break;
        // 单独的插件addon 控制器
        case 'addon_controller':
            $namespace = "\\addons\\" . $name . "\\controller\\" . $class;
            break;
        // 有下级插件的插件addons 入口文件
        case 'addons_index':
            $namespace = "\\addons\\" . $name . "\\core\\" . $class;
            break;
        // 有下级插件的插件addons 控制器
        case 'addons_controller':
            $namespace = "\\addons\\" . $name . "\\core\\controller\\" . $class;
            break;
        // 插件类型下的下级插件plugin
        default:
            $namespace = "\\addons\\" . $name . "\\" . $type . "\\controller\\" . $class;
    }
    
    return $namespace;
}

/**
 * 处理插件钩子
 *
 * @param string $hook
 *            钩子名称
 * @param mixed $params
 *            传入参数
 * @return void
 */
function hook($hook, $params = [])
{
    // 钩子调用
    \think\Hook::listen($hook, $params);
}

/**
 * 判断钩子是否存在
 * 2017年8月25日19:43:08
 *
 * @param unknown $hook            
 * @return boolean
 */
function hook_is_exist($hook)
{
    $res = \think\Hook::get($hook);
    if (empty($res)) {
        return false;
    }
    return true;
}

/**
 * 插件显示内容里生成访问插件的url
 *
 * @param string $url
 *            url
 * @param array $param
 *            参数
 */
function addons_url($url, $param = [])
{
    $url = parse_url($url);
    $case = config('url_convert');
    $addons = $case ? \think\Loader::parseName($url['scheme']) : $url['scheme'];
    $controller = $case ? \think\Loader::parseName($url['host']) : $url['host'];
    $action = trim($case ? strtolower($url['path']) : $url['path'], '/');
    /* 解析URL带的参数 */
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }
    if (strpos($action, '/') !== false) {
        // 有插件类型 插件类型://插件名/控制器名/方法名
        $controller_action = explode('/', $action);
        $params = array(
            'addons_type' => $addons,
            'addons' => $controller,
            'controller' => $controller_action[0],
            'action' => $controller_action[1]
        );
    } else {
        // 没有插件类型 插件名://控制器名/方法名
        $params = array(
            'addons' => $addons,
            'controller' => $controller,
            'action' => $action
        );
    }
    /* 基础参数 */
    $params = array_merge($params, $param); // 添加额外参数
    $return_url = url("shop/addons/execute", $params, '', true);
    return $return_url;
}

/**
 * 时间戳转时间
 *
 * @param unknown $time_stamp            
 */
function getTimeStampTurnTime($time_stamp)
{
    if ($time_stamp > 0) {
        $time = date('Y-m-d H:i:s', $time_stamp);
    } else {
        $time = "";
    }
    return $time;
}

/**
 * 时间转时间戳
 *
 * @param unknown $time            
 */
function getTimeTurnTimeStamp($time)
{
    $time_stamp = strtotime($time);
    return $time_stamp;
}

/**
 * 导出数据为excal文件
 *
 * @param unknown $expTitle            
 * @param unknown $expCellName            
 * @param unknown $expTableData            
 */
function dataExcel($expTitle, $expCellName, $expTableData)
{
    include 'data/extend/phpexcel_classes/PHPExcel.php';
    $xlsTitle = iconv('utf-8', 'gb2312', $expTitle); // 文件名称
    $fileName = $expTitle . date('_YmdHis'); // or $xlsTitle 文件名称可根据自己情况设定
    $cellNum = count($expCellName);
    $dataNum = count($expTableData);
    $objPHPExcel = new \PHPExcel();
    $cellName = array(
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        'AA',
        'AB',
        'AC',
        'AD',
        'AE',
        'AF',
        'AG',
        'AH',
        'AI',
        'AJ',
        'AK',
        'AL',
        'AM',
        'AN',
        'AO',
        'AP',
        'AQ',
        'AR',
        'AS',
        'AT',
        'AU',
        'AV',
        'AW',
        'AX',
        'AY',
        'AZ'
    );
    for ($i = 0; $i < $cellNum; $i ++) {
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);
    }
    for ($i = 0; $i < $dataNum; $i ++) {
        for ($j = 0; $j < $cellNum; $j ++) {
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), " " . $expTableData[$i][$expCellName[$j][0]]);
        }
    }
    $objPHPExcel->setActiveSheetIndex(0);
    header('pragma:public');
    header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
    header("Content-Disposition:attachment;filename=$fileName.xls"); // attachment新窗口打印inline本窗口打印
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
}

/**
 * 获取url参数
 *
 * @param unknown $action            
 * @param string $param            
 */
function __URL($url, $param = '')
{
    $url = \str_replace('SHOP_MAIN', '', $url);
    $url = \str_replace('APP_MAIN', 'wap', $url);
    $url = \str_replace('ADMIN_MAIN', ADMIN_MODULE, $url);
    // 处理后台页面
    $url = \str_replace(__URL__ . '/wap', 'wap', $url);
    $url = \str_replace(__URL__ . ADMIN_MODULE, ADMIN_MODULE, $url);
    $url = \str_replace(__URL__, '', $url);
    if (empty($url)) {
        return __URL__;
    } else {
        $str = substr($url, 0, 1);
        if ($str === '/' || $str === "\\") {
            $url = substr($url, 1, strlen($url));
        }
        if (REWRITE_MODEL) {
            
            $url = urlRouteConfig($url, $param);
            return $url;
        }
        $action_array = explode('?', $url);
        // 检测是否是pathinfo模式
        $url_model = url_model();
        if ($url_model) {
            $base_url = __URL__ . '/' . $action_array[0];
            $tag = '?';
        } else {
            $base_url = __URL__ . '?s=/' . $action_array[0];
            $tag = '&';
        }
        if (! empty($action_array[1])) {
            // 有参数
            return $base_url . $tag . $action_array[1];
        } else {
            if (! empty($param)) {
                return $base_url . $tag . $param;
            } else {
                return $base_url;
            }
        }
    }
}

/**
 * 特定路由规则
 */
function urlRoute()
{
    /**
     * *********************************************************************************特定路由规则***********************************************
     */
    if (REWRITE_MODEL) {
        \think\Loader::addNamespace('data', 'data/');
        $website = new WebSite();
        $url_route_list = $website->getUrlRoute();
        if (! empty($url_route_list['data'])) {
            foreach ($url_route_list['data'] as $k => $v) {
                // 针对特定路由特殊处理
                if ($v['route'] == 'shop/goods/goodsinfo') {
                    Route::get($v['rule'] . '-<goodsid>', $v['route'], []);
                } elseif ($v['route'] == 'shop/cms/articleclassinfo') {
                    Route::get($v['rule'] . '-<article_id>', $v['route'], []);
                } else {
                    Route::get($v['rule'], $v['route'], []);
                }
            }
        }
    }
}

function urlRouteConfig($url, $param)
{
    // 针对商品信息编辑
    $main = \str_replace('/index.php', '', __URL__);
    if (! empty($param)) {
        $url = $main . '/' . $url . '?' . $param;
    } else {
        $action_array = explode('?', $url);
        $url = $main . '/' . $url;
    }
    $html = Config::get('default_return_type');
    $url = str_replace('.' . $html, '', $url);
    // 针对店铺端进行处理
    $model = Request::instance()->module();
    if ($model == 'shop') {
        \think\Loader::addNamespace('data', 'data/');
        $website = new WebSite();
        $url_route_list = $website->getUrlRoute();
        if (! empty($url_route_list['data'])) {
            foreach ($url_route_list['data'] as $k => $v) {
                $v['route'] = str_replace('shop/', '', $v['route']);
                // 针对特定功能处理
                if ($v['route'] == 'goods/goodsinfo') {
                    $url = str_replace('goods/goodsinfo?goodsid=', $v['rule'] . '-', $url);
                } elseif ($v['route'] == 'cms/articleclassinfo') {
                    $url = str_replace('cms/articleclassinfo?article_id=', $v['rule'] . '-', $url);
                } else {
                    $url = str_replace($v['route'], $v['rule'], $url);
                }
            }
        }
    }
    
    $url_array = explode('?', $url);
    if (! empty($url_array[1])) {
        $url = $url_array[0] . '.' . $html . '?' . $url_array[1];
    } else {
        $url = $url_array[0] . '.' . $html;
    }
    return $url;
}

/**
 * 返回系统是否配置了伪静态
 *
 * @return string
 */
function rewrite_model()
{
    $rewrite_model = REWRITE_MODEL;
    if ($rewrite_model) {
        return 1;
    } else {
        return 0;
    }
}

function url_model()
{
    $url_model = 0;
    try {
        \think\Loader::addNamespace('data', 'data/');
        $website = new WebSite();
        $website_info = $website->getWebSiteInfo();
        if (! empty($website_info)) {
            $url_model = isset($website_info["url_type"]) ? $website_info["url_type"] : 0;
        }
    } catch (Exception $e) {
        $url_model = 0;
    }
    return $url_model;
}

function admin_model()
{
    $admin_model = ADMIN_MODULE;
    return $admin_model;
}

/**
 * 过滤特殊字符(微信qq)
 *
 * @param unknown $str            
 */
function filterStr($str)
{
    if ($str) {
        $name = $str;
        $name = preg_replace_callback('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', function ($matches) {
            return '';
        }, $name);
        $name = preg_replace_callback('/xE0[x80-x9F][x80-xBF]‘.‘|xED[xA0-xBF][x80-xBF]/S', function ($matches) {
            return '';
        }, $name);
        // 汉字不编码
        $name = json_encode($name);
        $name = preg_replace_callback("/\\\ud[0-9a-f]{3}/i", function ($matches) {
            return '';
        }, $name);
        if (! empty($name)) {
            $name = json_decode($name);
            return $name;
        } else {
            return '';
        }
    } else {
        return '';
    }
}

/**
 * 检测ID是否在ID组
 *
 * @param unknown $id
 *            数字
 * @param unknown $id_arr
 *            数字,数字
 */
function checkIdIsinIdArr($id, $id_arr)
{
    $id_arr = $id_arr . ',';
    $result = strpos($id_arr, $id . ',');
    if ($result !== false) {
        return 1;
    } else {
        return 0;
    }
}

/**
 * 用于用户自定义模板判断 为空的话输出
 */
function __isCustomNullUrl($url)
{
    if (trim($url) == "") {
        return "javascript:;";
    } else {
        return __URL('APP_MAIN/' . $url);
    }
}

/**
 * 图片路径拼装(用于完善用于外链的图片)
 *
 * @param unknown $img_path            
 * @param unknown $type            
 * @param unknown $url            
 * @return string
 */
function __IMG($img_path)
{
    $path = "";
    if (! empty($img_path)) {
        if (stristr($img_path, "http://") === false && stristr($img_path, "https://") === false) {
            $path = "__UPLOAD__/" . $img_path;
        } else {
            $path = $img_path;
        }
    }
    return $path;
}

/**
 * *
 * 判断一个数组是否存在于另一个数组中
 *
 * @param unknown $arr            
 * @param unknown $contrastArr            
 * @return boolean
 */
function is_all_exists($arr, $contrastArr)
{
    if (! empty($arr) && ! empty($contrastArr)) {
        for ($i = 0; $i < count($arr); $i ++) {
            if (! in_array($arr[$i], $contrastArr)) {
                return false;
            }
        }
        return true;
    }
}

/**
 * 检查模版是否存在
 * 创建时间：2017年9月13日 18:17:01 王永杰
 *
 * @param 文件夹[shop、wap] $folder            
 * @param 当前目录文件夹 $curr_template            
 * @return boolean
 */
function checkTemplateIsExists($folder, $curr_template)
{
    $file_path = str_replace("\\", "/", ROOT_PATH . 'template/' . $folder . "/" . $curr_template . "/config.xml");
    return file_exists($file_path);
}

/**
 * 通用提示页(专用于数据库的操作)
 *
 * @param string $msg
 *            提示消息（支持语言包变量）
 * @param integer $status
 *            状态（0：失败；1：成功）
 * @param string $extra
 *            附加数据
 */
function showMessage($msg, $status = 0, $extra = '')
{
    $result = array(
        'status' => $status,
        'message' => $msg,
        'result' => $extra
    );
    return $result;
}

/**
 * 发送HTTP请求方法，目前只支持CURL发送请求
 *
 * @param string $url
 *            请求URL
 * @param array $params
 *            请求参数
 * @param string $method
 *            请求方法GET/POST
 * @return array $data 响应数据
 */
function http($url, $timeout = 30, $header = array())
{
    if (! function_exists('curl_init')) {
        throw new Exception('server not install curl');
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    if (! empty($header)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    $data = curl_exec($ch);
    list ($header, $data) = explode("\r\n\r\n", $data);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code == 301 || $http_code == 302) {
        $matches = array();
        preg_match('/Location:(.*?)\n/', $header, $matches);
        $url = trim(array_pop($matches));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $data = curl_exec($ch);
    }
    
    if ($data == false) {
        curl_close($ch);
    }
    @curl_close($ch);
    return $data;
}

/**
 * 多维数组排序
 *
 * @param unknown $data            
 * @param unknown $sort_order_field            
 * @param string $sort_order            
 * @param string $sort_type            
 * @return unknown
 */
function my_array_multisort($data, $sort_order_field, $sort_order = SORT_DESC, $sort_type = SORT_NUMERIC)
{
    foreach ($data as $val) {
        $key_arrays[] = $val[$sort_order_field];
    }
    array_multisort($key_arrays, $sort_order, $sort_type, $data);
    return $data;
}

/**
 * 掩饰用户名
 *
 * @param unknown $username            
 */
function cover_up_username($username)
{
    if (! empty($username)) {
        $patterns = '/^(.{1})(.*)(.{1})$/';
        if (preg_match($patterns, $username)) {
            $username = preg_replace($patterns, "$1*****$3", $username);
        }
    }
    return $username;
}

/**
 * 生成条形码
 * @param unknown $content
 * @return string
 */
function getBarcode($content){
    $barcode = new Barcode(14, $content);
    $path = $barcode ->generateBarcode();
    return $path;
}