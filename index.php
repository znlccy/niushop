<?php
/**
 * 开发规范：
 * 1.不同版本不同控制器以及模板
 * 2.不同版本不同数据库，但是对应数据表表结构必须一致
 * 3.不同版本共用service层，所以修改表结构必须所有版本统一
 */
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 检测PHP环境
if (version_compare(PHP_VERSION, '5.4.0', '<'))
    die('require PHP > 5.4.0 !');

// 发布代码的时候把下面的代码放开 2017年5月12日 09:29:16
 if (! file_exists('./install.lock')) {
     header('location: ./install.php');
     exit();
 }
// [ 应用入口文件 ]
// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';


