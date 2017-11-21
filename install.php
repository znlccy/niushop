<?php
/**
 * install.php
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 
 * 非授权用户允许商用，严禁去除Niushop相关的版权信息。
 * 请尊重Niushop开发人员劳动成果，严禁使用本系统转卖、销售或二次开发后转卖、销售等商业行为。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */

define('IN_IA', true);
error_reporting(0);
@set_time_limit(0);
//@set_magic_quotes_runtime(0);
ob_start();
define('IA_ROOT', str_replace("\\",'/', dirname(__FILE__)));
if($_GET['res']) {
	$res = $_GET['res'];
	$reses = tpl_resources();
	if(array_key_exists($res, $reses)) {
		if($res == 'css') {
			header('content-type:text/css');
		} else {
			header('content-type:image/png');
		}
		echo base64_decode($reses[$res]);
		exit();
	}
}
if($_GET['action']){
    $dbserver = $_GET['dbserver'];
    $dbusername = $_GET['dbusername'];
    $dbpassword = $_GET['dbpassword'];
    $dbname = $_GET['dbname'];
    $link = mysql_connect($dbserver, $dbusername, $dbpassword);
    $query = mysql_query("SHOW DATABASES LIKE  '{$dbname}';");
    //     				var_dump($query);
    if(mysql_fetch_assoc($query) != false){
        //说明数据库已经存在
        echo 1;
        exit();
    }else{
        echo 0;
        exit();
    }
}
$actions = array('license', 'env', 'db', 'finish');
$action = $_COOKIE['action'];
$action = in_array($action, $actions) ? $action : 'license';
$ispost = strtolower($_SERVER['REQUEST_METHOD']) == 'post';
if(file_exists(IA_ROOT . '/install.lock') && $action != 'finish') {
	header('location: ./index.php/shop');
	exit;
}
header('content-type: text/html; charset=utf-8');
if($action == 'license') {
	if($ispost) {
		setcookie('action', 'env');
		header('location: ?refresh');
		exit;
	}
	tpl_install_license();
}
if($action == 'env') {
	if($ispost) {
		setcookie('action', $_POST['do'] == 'continue' ? 'db' : 'license');
		header('location: ?refresh');
		exit;
	}
	$ret = array();
	$ret['server']['os']['value'] = php_uname();
	if(PHP_SHLIB_SUFFIX == 'dll') {
		$ret['server']['os']['remark'] = '建议使用 Linux 系统以提升程序性能';
		$ret['server']['os']['class'] = 'warning';
	}
	$ret['server']['sapi']['value'] = $_SERVER['SERVER_SOFTWARE'];
	if(PHP_SAPI == 'isapi') {
		$ret['server']['sapi']['remark'] = '建议使用 Apache 或 Nginx 以提升程序性能';
		$ret['server']['sapi']['class'] = 'warning';
	}
	$ret['server']['php']['value'] = PHP_VERSION;
	$ret['server']['dir']['value'] = IA_ROOT;
	if(function_exists('disk_free_space')) {
		$ret['server']['disk']['value'] = floor(disk_free_space(IA_ROOT) / (1024*1024)).'M';
	} else {
		$ret['server']['disk']['value'] = 'unknow';
	}
	$ret['server']['upload']['value'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow';

	$ret['php']['version']['value'] = PHP_VERSION;
	$ret['php']['version']['class'] = 'success';
	if(version_compare(PHP_VERSION, '5.4.0') == -1) {
		$ret['php']['version']['class'] = 'danger';
		$ret['php']['version']['failed'] = true;
		$ret['php']['version']['remark'] = 'PHP版本必须为 5.4.0 以上.';
	}
	if(strstr(PHP_VERSION, '7.'))
	{
			$ret['php']['mysql']['ok'] = function_exists('mysqli_connect');
			var_dump($ret['php']['mysql']['ok']);
		
		if($ret['php']['mysql']['ok']) {
			$ret['php']['mysql']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		} else {
			$ret['php']['pdo']['failed'] = true;
			$ret['php']['mysql']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		}

		$ret['php']['pdo']['ok'] = extension_loaded('pdo') && extension_loaded('pdo_mysql');
		if($ret['php']['pdo']['ok']) {
			$ret['php']['pdo']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
			$ret['php']['pdo']['class'] = 'success';
			if(!$ret['php']['mysql']['ok']) {
				$ret['php']['pdo']['remark'] = '您的PHP环境不支持 mysql_connect，请开启此扩展. ';
			}
		} else {
			$ret['php']['pdo']['failed'] = true;
			if($ret['php']['mysql']['ok']) {
				$ret['php']['pdo']['value'] = '<span class="glyphicon glyphicon-remove text-warning"></span>';
				$ret['php']['pdo']['class'] = 'warning';
				$ret['php']['pdo']['remark'] = '您的PHP环境不支持PDO, 请开启此扩展. ';
			} else {
				$ret['php']['pdo']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
				$ret['php']['pdo']['class'] = 'danger';
				$ret['php']['pdo']['remark'] = '您的PHP环境不支持PDO, 也不支持 mysql_connect, 系统无法正常运行. ';
			}
		}
	}else{
		$ret['php']['mysql']['ok'] = function_exists('mysqli_connect');
		if($ret['php']['mysql']['ok']) {
			$ret['php']['mysql']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		} else {
			$ret['php']['pdo']['failed'] = true;
			$ret['php']['mysql']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		}

		$ret['php']['pdo']['ok'] = extension_loaded('pdo') && extension_loaded('pdo_mysql');
		if($ret['php']['pdo']['ok']) {
			$ret['php']['pdo']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
			$ret['php']['pdo']['class'] = 'success';
			if(!$ret['php']['mysql']['ok']) {
				$ret['php']['pdo']['remark'] = '您的PHP环境不支持 mysqli_connect，请开启此扩展. ';
			}
		} else {
			$ret['php']['pdo']['failed'] = true;
			if($ret['php']['mysql']['ok']) {
				$ret['php']['pdo']['value'] = '<span class="glyphicon glyphicon-remove text-warning"></span>';
				$ret['php']['pdo']['class'] = 'warning';
				$ret['php']['pdo']['remark'] = '您的PHP环境不支持PDO, 请开启此扩展. ';
			} else {
				$ret['php']['pdo']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
				$ret['php']['pdo']['class'] = 'danger';
				$ret['php']['pdo']['remark'] = '您的PHP环境不支持PDO, 也不支持 mysqli_connect, 系统无法正常运行. ';
			}
		}
	}
	

	$ret['php']['fopen']['ok'] = @ini_get('allow_url_fopen') && function_exists('fsockopen');
	if($ret['php']['fopen']['ok']) {
		$ret['php']['fopen']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
	} else {
		$ret['php']['fopen']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
	}

	//$ret['php']['curl']['ok'] = extension_loaded('curl') && function_exists('curl_init');
	$ret['php']['curl']['ok'] = 1;
	if($ret['php']['curl']['ok']) {
		$ret['php']['curl']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		$ret['php']['curl']['class'] = 'success';
		if(!$ret['php']['fopen']['ok']) {
			$ret['php']['curl']['remark'] = '您的PHP环境虽然不支持 allow_url_fopen, 但已经支持了cURL, 这样系统是可以正常高效运行的, 不需要额外处理. ';
		}
	} else {
		if($ret['php']['fopen']['ok']) {
			$ret['php']['curl']['value'] = '<span class="glyphicon glyphicon-remove text-warning"></span>';
			$ret['php']['curl']['class'] = 'warning';
			$ret['php']['curl']['remark'] = '您的PHP环境不支持cURL, 但支持 allow_url_fopen, 这样系统虽然可以运行, 但还是建议你开启cURL以提升程序性能和系统稳定性. ';
		} else {
			$ret['php']['curl']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
			$ret['php']['curl']['class'] = 'danger';
			$ret['php']['curl']['remark'] = '您的PHP环境不支持cURL, 也不支持 allow_url_fopen, 系统无法正常运行. ';
			$ret['php']['curl']['failed'] = true;
		}
	}

// 	$ret['php']['ssl']['ok'] = extension_loaded('openssl');
// 	if($ret['php']['ssl']['ok']) {
// 		$ret['php']['ssl']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
// 		$ret['php']['ssl']['class'] = 'success';
// 	} else {
// 		$ret['php']['ssl']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
// 		$ret['php']['ssl']['class'] = 'danger';
// 		$ret['php']['ssl']['failed'] = true;
// 		$ret['php']['ssl']['remark'] = '没有启用OpenSSL, 将无法访问公众平台的接口, 系统无法正常运行. ';
// 	}

	$ret['php']['gd']['ok'] = extension_loaded('gd');
	if($ret['php']['gd']['ok']) {
		$ret['php']['gd']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		$ret['php']['gd']['class'] = 'success';
	} else {
		$ret['php']['gd']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		$ret['php']['gd']['class'] = 'danger';
		$ret['php']['gd']['failed'] = true;
		$ret['php']['gd']['remark'] = '没有启用GD, 将无法正常上传和压缩图片, 系统无法正常运行. ';
	}

	$ret['php']['dom']['ok'] = class_exists('DOMDocument');
	if($ret['php']['dom']['ok']) {
		$ret['php']['dom']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		$ret['php']['dom']['class'] = 'success';
	} else {
		$ret['php']['dom']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		$ret['php']['dom']['class'] = 'danger';
		$ret['php']['dom']['failed'] = true;
		$ret['php']['dom']['remark'] = '没有启用DOMDocument, 将无法正常安装使用模块, 系统无法正常运行. ';
	}

	$ret['php']['session']['ok'] = ini_get('session.auto_start');
	if($ret['php']['session']['ok'] == 0 || strtolower($ret['php']['session']['ok']) == 'off') {
		$ret['php']['session']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		$ret['php']['session']['class'] = 'success';
	} else {
		$ret['php']['session']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		$ret['php']['session']['class'] = 'danger';
		$ret['php']['session']['failed'] = true;
		$ret['php']['session']['remark'] = '系统session.auto_start开启, 将无法正常注册会员, 系统无法正常运行. ';
	}

	$ret['php']['asp_tags']['ok'] = ini_get('asp_tags');
	if(empty($ret['php']['asp_tags']['ok']) || strtolower($ret['php']['asp_tags']['ok']) == 'off') {
		$ret['php']['asp_tags']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		$ret['php']['asp_tags']['class'] = 'success';
	} else {
		$ret['php']['asp_tags']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		$ret['php']['asp_tags']['class'] = 'danger';
		$ret['php']['asp_tags']['failed'] = true;
		$ret['php']['asp_tags']['remark'] = '请禁用可以使用ASP 风格的标志，配置php.ini中asp_tags = Off';
	}
	$ret['write']['bottom']['ok'] = local_writeable(dirname(__FILE__));
	if($ret['write']['bottom']['ok']) {
	    $ret['write']['bottom']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
	    $ret['write']['bottom']['class'] = 'success';
	} else {
	    $ret['write']['bottom']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
	    $ret['write']['bottom']['class'] = 'danger';
	    $ret['write']['bottom']['failed'] = true;
	    $ret['write']['bottom']['remark'] = '项目根目录无法写入,系统将无法正常运行.  ';
	}
	$ret['write']['root']['ok'] = local_writeable(IA_ROOT . '/upload');
	if($ret['write']['root']['ok']) {
		$ret['write']['root']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		$ret['write']['root']['class'] = 'success';
	} else {
		$ret['write']['root']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		$ret['write']['root']['class'] = 'danger';
		$ret['write']['root']['failed'] = true;
		$ret['write']['root']['remark'] = 'upload无法写入, 将无法使用自动更新功能, 系统无法正常运行.  ';
	}
	$ret['write']['data']['ok'] = local_writeable(IA_ROOT . '/runtime');
	if($ret['write']['data']['ok']) {
		$ret['write']['data']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
		$ret['write']['data']['class'] = 'success';
	} else {
		$ret['write']['data']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
		$ret['write']['data']['class'] = 'danger';
		$ret['write']['data']['failed'] = true;
		$ret['write']['data']['remark'] = 'runtime目录无法写入, 将无法写入配置文件, 系统无法正常安装. ';
	}
	

	$ret['write']['database']['ok'] = local_writeable(IA_ROOT . '/application');
	if($ret['write']['database']['ok']) {
	    $ret['write']['database']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
	    $ret['write']['database']['class'] = 'success';
	} else {
	    $ret['write']['database']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
	    $ret['write']['database']['class'] = 'danger';
	    $ret['write']['database']['failed'] = true;
	    $ret['write']['database']['remark'] = 'application目录无法写入, 将无法写入配置文件, 系统无法正常安装. ';
	}

	$ret['continue'] = true;
	foreach($ret['php'] as $opt) {
		if($opt['failed']) {
			$ret['continue'] = false;
			break;
		}
	}
	foreach($ret['write'] as $v){
    	if($v['failed']) {
    		$ret['continue'] = false;
    	}	    
	}
	tpl_install_env($ret);
}
if($action == 'db') {
	if($ispost) {
		if($_POST['do'] != 'continue') {
			setcookie('action', 'env');
			header('location: ?refresh');
			exit();
		}
		$family = $_POST['family'] == 'x' ? 'x' : 'v';
		$db = $_POST['db'];
		$user = $_POST['user'];
		//  针对php7版本数据库安装
		if(strstr(PHP_VERSION, '7.'))
		{
			$link = mysqli_connect($db['server'], $db['username'], $db['password']);
    		if(!$link) {
    			$error = mysqli_connect_error();
    			if (strpos($error, 'Access denied for user') !== false) {
    				$error = '您的数据库访问用户名或是密码错误. <br />';
    			} else {
    				$error = iconv('gbk', 'utf8', $error);
    			}
		    } else {
    			mysqli_query($link, "SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary");
    			mysqli_query($link, "SET sql_mode=''");
    			if(mysqli_errno($link)) {
    				$error = mysqli_error($link);
    			} else {
    				$query = mysqli_query($link, "SHOW DATABASES LIKE  '{$db['name']}';");
    				if (!mysqli_fetch_assoc($query)) {
    					if(mysqli_get_server_info() > '4.1') {
    						mysqli_query($link, "CREATE DATABASE IF NOT EXISTS `{$db['name']}` DEFAULT CHARACTER SET utf8");
    					} else {
    						mysqli_query($link, "CREATE DATABASE IF NOT EXISTS `{$db['name']}`");
    					}
    				}
    				$query = mysqli_query($link, "SHOW DATABASES LIKE  '{$db['name']}';");
    				if (!mysqli_fetch_assoc($query)) {
    					$error .= "数据库不存在且创建数据库失败. <br />";
    				}
    				if(mysqli_errno($link)) {
    					$error .= mysqli_error($link);
    				}
    			}
		    }
    		if(empty($error)) {
    			mysqli_select_db($link, $db['name']);
    			$query = mysqli_query($link,"SHOW TABLES LIKE '{$db['prefix']}%';");
    			if (mysqli_fetch_assoc($query)) {
    				//$error = '您的数据库不为空，请重新建立数据库或是清空该数据库！';
    				die('<script type="text/javascript">alert("您的数据库不为空，请重新建立数据库或是清空该数据库.");history.back();</script>');
    			}
		    }
     		if(empty($error)) {
    			$pieces = explode(':', $db['server']);
    			$db['port'] = !empty($pieces[1]) ? $pieces[1] : '3306';
    			$config = db_config();
    			$cookiepre = local_salt(4) . '_';
    			$authkey = local_salt(8);
    			$config = str_replace(array(
    				'{db-server}', '{db-username}', '{db-password}', '{db-port}','{db-name}'
    			), array(
    				$db['server'], $db['username'], $db['password'], $db['port'], $db['name']
    			), $config);
				mysqli_close($link);
			



                //循环添加数据
                if(file_exists(IA_ROOT . '/niushop_b2c.sql')){
    				
    				$link = mysqli_connect($db['server'], $db['username'], $db['password'], $db['name']);
    				mysqli_query($link, "SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary");
    				mysqli_query($link, "SET sql_mode=''");
    				if(!$link){		    
    				    die('<script type="text/javascript">alert("连接不到数据库, 请稍后重试！");history.back();</script>');
    				}    				
                    $sql = file_get_contents(IA_ROOT . '/niushop_b2c.sql');
                    $sql = str_replace("\r", "\n", $sql);
                    $sql = explode(";\n", $sql);
                    foreach ($sql as $k =>$item) {
    					
                        $item = trim($item);
                        if(empty($item)) continue;
    
                        preg_match('/CREATE TABLE `([^ ]*)`/', $item, $matches);
    		
    					
                        if($matches) {
    						mysqli_select_db($db['name']);
                            $table_name = $matches[1];
    						
                            $result = mysqli_query($link, $item);
                        } else {
    						
    						mysqli_select_db($db['name']);
                            $result = mysqli_query($link, $item);
                            //$db->execute($item);
                        }
                        	
                    }
                }else{
                    die('<script type="text/javascript">alert("安装包不正确, 数据安装脚本缺失.");history.back();</script>');
                }
                //删除商品sku错误信息 和 商品属性错误信息
                mysqli_query($link, "DELETE FROM ns_goods_sku WHERE goods_id NOT IN (SELECT goods_id FROM ns_goods)");
                mysqli_query($link, "DELETE FROM ns_goods_attribute WHERE goods_id NOT IN ( SELECT goods_id FROM ns_goods)");
                
            //添加用户管理员
    			$password = md5($user['password']);
    			$datetime =date('Y-m-d H:i:s', time());
    			mysqli_query($link, "DELETE FROM sys_user WHERE user_name = '{$user['username']}'");
    			$insert_error = mysqli_query($link, "INSERT INTO sys_user (user_name, user_password, is_system, is_member, reg_time) 
    			VALUES('{$user['username']}', '{$password}', '1', '1','" . $datetime . "')");			
			if($insert_error){
			    $insert_id = mysqli_insert_id($link);

				$member_level_result=mysqli_query($link, "SELECT * FROM ns_member_level WHERE is_default=1;");
				$member_default_level=0;
				while ($row=mysqli_fetch_array($member_level_result))
			    {
			        $member_default_level =$row["level_id"];
			    }
				mysqli_query($link, "INSERT INTO ns_member ( uid, member_name, member_level, reg_time, memo) VALUES (".$insert_id .",'{$user['username']}', ".$member_default_level.", '".$datetime."', '');");

			    //添加管理员用户组
			    $group_list = array();
			    $group_string = "";
			    $result= mysqli_query($link, "SELECT * FROM sys_module where is_control_auth = 1");
			    while ($row=mysqli_fetch_array($result))
			    {
			        $group_string .=",".$row["module_id"];
			    }
			    if($group_string != ''){
			        $group_string = substr($group_string, 1);
			    }
			    $group_error = mysqli_query($link, "INSERT INTO sys_user_group (group_name,instance_id, is_system, module_id_array, create_time)
			        VALUES('管理员组','0', '1','{$group_string}','" . $datetime . "')");
			        if($group_error){
			        $group_insert_id = mysqli_insert_id($link);
			        //给用户添加管理员权限
			        mysqli_query($link, "INSERT INTO sys_user_admin (uid, admin_name, group_id_array, is_admin, admin_status)
			        VALUES('{$insert_id}', '管理员','{$group_insert_id}', '1', '1')");
			        }else{
			        die('<script type="text/javascript">alert("管理员账户注册失败.");history.back();</script>');
			    }
			}
			}else{
			    die('<script type="text/javascript">alert("'.$error.'");history.back();</script>');
			}
			    
	}else{
		$link = mysql_connect($db['server'], $db['username'], $db['password']);
		if(empty($link)) {
			$error = mysql_error();
		
			if (strpos($error, 'Access denied for user') !== false) {
				$error = '您的数据库访问用户名或是密码错误';
			} else {
				$error = iconv('gbk', 'utf8', $error);
			}
    		} else {
    			mysql_query("SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary");
    			mysql_query("SET sql_mode=''");
    			if(mysql_errno()) {
    				$error = mysql_error();
    			} else {
    				$query = mysql_query("SHOW DATABASES LIKE  '{$db['name']}';");
//     				var_dump($query);
    				
    				if (!mysql_fetch_assoc($query)) {
    					if(mysql_get_server_info() > '4.1') {
    						mysql_query("CREATE DATABASE IF NOT EXISTS `{$db['name']}` DEFAULT CHARACTER SET utf8", $link);
    					} else {
    						mysql_query("CREATE DATABASE IF NOT EXISTS `{$db['name']}`", $link);
    					}
    				}
    				
    				$query = mysql_query("SHOW DATABASES LIKE  '{$db['name']}';");
    				if (!mysql_fetch_assoc($query)) {
    					$error .= "数据库不存在且创建数据库失败";
    				}
    				if(mysql_errno()) {
    					$error .= mysql_error();
    				}
    			}
    		}
    	if(empty($error)) {
    		mysql_select_db($db['name']);
    		$query = mysql_query("SHOW TABLES LIKE '{$db['prefix']}%';");
    		if (mysql_fetch_assoc($query)) {
    			//$error = '您的数据库不为空，请重新建立数据库或是清空该数据库！';
    			die('<script type="text/javascript">alert("您的数据库不为空，请重新建立数据库或是清空该数据库.");history.back();</script>');
    		}
    	}
 		if(empty($error)) {
			$pieces = explode(':', $db['server']);
			$db['port'] = !empty($pieces[1]) ? $pieces[1] : '3306';
			$config = db_config();
			$cookiepre = local_salt(4) . '_';
			$authkey = local_salt(8);
			$config = str_replace(array(
				'{db-server}', '{db-username}', '{db-password}', '{db-port}','{db-name}'
			), array(
				$db['server'], $db['username'], $db['password'], $db['port'], $db['name']
			), $config);

	
				mysql_close($link);
			

				$link = mysql_connect($db['server'], $db['username'], $db['password']);
				if(!$link){
				    die('<script type="text/javascript">alert("连接不到服务器, 请稍后重试！");history.back();</script>');
				}
				$mysql_db = mysql_select_db($db['name']);
				if(!$mysql_db){
				    die('<script type="text/javascript">alert("连接不到数据库, 请稍后重试！");history.back();</script>');
				}
				mysql_query("SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary");
				mysql_query("SET sql_mode=''");
				


            //循环添加数据
            if(file_exists(IA_ROOT . '/niushop_b2c.sql')){
                $sql = file_get_contents(IA_ROOT . '/niushop_b2c.sql');
                $sql = str_replace("\r", "\n", $sql);
                $sql = explode(";\n", $sql);

                foreach ($sql as $item) {
                    $item = trim($item);
                    if(empty($item)) continue;
                    preg_match('/CREATE TABLE `([^ ]*)`/', $item, $matches);
                    if($matches) {
                        $table_name = $matches[1];
                        mysql_query($item, $link);
                    } else {
                        mysql_close($link);
                        $link = mysql_connect($db['server'], $db['username'], $db['password']);
                        mysql_select_db($db['name']);
                        mysql_query("SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary");
                        mysql_query("SET sql_mode=''");
                        mysql_query($item, $link);
                        //$db->execute($item);
                    }
                    	
                }
                
            }else{
                die('<script type="text/javascript">alert("安装包不正确, 数据安装脚本缺失.");history.back();</script>');
            }
            
            //添加用户管理员
            mysql_close($link);
            $link = mysql_connect($db['server'], $db['username'], $db['password']);
            mysql_select_db($db['name']);
            mysql_query("SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary");
            mysql_query("SET sql_mode=''");
            //删除商品sku错误信息 和 商品属性错误信息
            mysql_query("DELETE FROM ns_goods_sku WHERE goods_id NOT IN (SELECT goods_id FROM ns_goods)");
            mysql_query("DELETE FROM ns_goods_attribute WHERE goods_id NOT IN ( SELECT goods_id FROM ns_goods)");
            
			$password = md5($user['password']);
			$datetime =date('Y-m-d H:i:s', time());
			mysql_query("DELETE FROM sys_user WHERE user_name = '{$user['username']}'");
			$insert_error = mysql_query("INSERT INTO sys_user (user_name, user_password, is_system, is_member, reg_time, nick_name) 
			VALUES('{$user['username']}', '{$password}', '1', '1','" . $datetime . "', '{$user['username']}')");	
	
			if($insert_error){
			    $insert_id = mysql_insert_id();
				$member_level_result=mysql_query("SELECT * FROM ns_member_level WHERE is_default=1;", $link);
				$member_default_level=0;
				while ($row=mysql_fetch_array($member_level_result))
			    {
			        $member_default_level =$row["level_id"];
			    }
				mysql_query("INSERT INTO ns_member ( uid, member_name, member_level, reg_time, memo) VALUES (".$insert_id .",'{$user['username']}', ".$member_default_level.", '".$datetime."', '');", $link);
			    //添加管理员用户组
			    $group_list = array();
			    $group_string = "";
			    $result= mysql_query("SELECT * FROM sys_module where is_control_auth = 1", $link);
			    while ($row=mysql_fetch_array($result))
			    {
			        $group_string .=",".$row["module_id"];
			    }
			    if($group_string != ''){
			        $group_string = substr($group_string, 1);
			    }
			    $group_error = mysql_query("INSERT INTO sys_user_group (group_name,instance_id, is_system, module_id_array, create_time)
			        VALUES('管理员组','0', '1','{$group_string}','" . $datetime . "')", $link);
			        if($group_error){
			        $group_insert_id = mysql_insert_id();
			        //给用户添加管理员权限
			        mysql_query("INSERT INTO sys_user_admin (uid, admin_name, group_id_array, is_admin, admin_status)
			        VALUES('{$insert_id}', '管理员','{$group_insert_id}', '1', '1')", $link);

			        }else{
			        die('<script type="text/javascript">alert("管理员账户注册失败.");history.back();</script>');
			    }
			}else{
			    die('<script type="text/javascript">alert("管理员账户注册失败.");history.back();</script>');
			}
		
			             
			}else{
			die('<script type="text/javascript">alert("'.$error.'");history.back();</script>');
			}	
	   }	
				//配置数据库
			file_put_contents(IA_ROOT . '/application/database.php', $config);
			touch(IA_ROOT . '/install.lock');
			setcookie('action', 'finish');		
			header('location: ?refresh');
			exit();
		
	
	}
	tpl_install_db($error);

}
if($action == 'finish') {
	//setcookie('action', '', -10);

// 	$dbfile = IA_ROOT . '/data/db.php';
// 	@unlink($dbfile);
// 	define('IN_SYS', true);
// 	require IA_ROOT . '/framework/bootstrap.inc.php';
// 	require IA_ROOT . '/web/common/bootstrap.sys.inc.php';
// 	$_W['uid'] = $_W['isfounder'] = 1;
// 	load()->web('common');
// 	load()->web('template');
// 	load()->model('setting');
// 	load()->model('cache');

// 	cache_build_frame_menu();
// 	cache_build_setting();
// 	cache_build_users_struct();
// 	cache_build_module_subscribe_type();
	tpl_install_finish();
}

function local_writeable($dir) {
	$writeable = 0;
	if(!is_dir($dir)) {
		@mkdir($dir, 0777);
	}
	if(is_dir($dir)) {
		if($fp = fopen("$dir/test.txt", 'w')) {
			fclose($fp);
			unlink("$dir/test.txt");
			$writeable = 1;
		} else {
			$writeable = 0;
		}
	}
	return $writeable;
}

function local_salt($length = 8) {
	$result = '';
	while(strlen($result) < $length) {
		$result .= sha1(uniqid('', true));
	}
	return substr($result, 0, $length);
}

function local_config() {
	$cfg = <<<EOF
<?php
defined('IN_IA') or exit('Access Denied');

\$config = array();

\$config['db']['master']['host'] = '{db-server}';
\$config['db']['master']['username'] = '{db-username}';
\$config['db']['master']['password'] = '{db-password}';
\$config['db']['master']['port'] = '{db-port}';
\$config['db']['master']['database'] = '{db-name}';
\$config['db']['master']['charset'] = 'utf8';
\$config['db']['master']['pconnect'] = 0;
\$config['db']['master']['tablepre'] = '{db-tablepre}';

\$config['db']['slave_status'] = false;
\$config['db']['slave']['1']['host'] = '';
\$config['db']['slave']['1']['username'] = '';
\$config['db']['slave']['1']['password'] = '';
\$config['db']['slave']['1']['port'] = '3307';
\$config['db']['slave']['1']['database'] = '';
\$config['db']['slave']['1']['charset'] = 'utf8';
\$config['db']['slave']['1']['pconnect'] = 0;
\$config['db']['slave']['1']['tablepre'] = 'ims_';
\$config['db']['slave']['1']['weight'] = 0;

\$config['db']['common']['slave_except_table'] = array('core_sessions');

// --------------------------  CONFIG COOKIE  --------------------------- //
\$config['cookie']['pre'] = '{cookiepre}';
\$config['cookie']['domain'] = '';
\$config['cookie']['path'] = '/';

// --------------------------  CONFIG SETTING  --------------------------- //
\$config['setting']['charset'] = 'utf-8';
\$config['setting']['cache'] = 'mysql';
\$config['setting']['timezone'] = 'Asia/Shanghai';
\$config['setting']['memory_limit'] = '256M';
\$config['setting']['filemode'] = 0644;
\$config['setting']['authkey'] = '{authkey}';
\$config['setting']['founder'] = '1';
\$config['setting']['development'] = 0;
\$config['setting']['referrer'] = 0;
\$config['setting']['https'] = 0;

// --------------------------  CONFIG UPLOAD  --------------------------- //
\$config['upload']['image']['extentions'] = array('gif', 'jpg', 'jpeg', 'png');
\$config['upload']['image']['limit'] = 5000;
\$config['upload']['attachdir'] = '{attachdir}';
\$config['upload']['audio']['extentions'] = array('mp3');
\$config['upload']['audio']['limit'] = 5000;

// --------------------------  CONFIG MEMCACHE  --------------------------- //
\$config['setting']['memcache']['server'] = '';
\$config['setting']['memcache']['port'] = 11211;
\$config['setting']['memcache']['pconnect'] = 1;
\$config['setting']['memcache']['timeout'] = 30;
\$config['setting']['memcache']['session'] = 1;

// --------------------------  CONFIG PROXY  --------------------------- //
\$config['setting']['proxy']['host'] = '';
\$config['setting']['proxy']['auth'] = '';
EOF;
	return trim($cfg);
}
function db_config(){
    $cfg = <<<EOF
<?php

return [
// 数据库类型
'type'           => 'mysql',
// 服务器地址
'hostname'       => '{db-server}',
// 数据库名
'database'       => '{db-name}',
// 用户名
'username'       => '{db-username}',
// 密码
'password'       => '{db-password}',
// 端口
'hostport'       => '{db-port}',
// 连接dsn
'dsn'            => '',
// 数据库连接参数
'params'         => [],
// 数据库编码默认采用utf8
'charset'        => 'utf8',
// 数据库表前缀
'prefix'         => '',
// 数据库调试模式
'debug'          => true,
// 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
'deploy'         => 0,
// 数据库读写是否分离 主从式有效
'rw_separate'    => false,
// 读写分离后 主服务器数量
'master_num'     => 1,
// 指定从服务器序号
'slave_no'       => '',
// 是否严格检查字段是否存在
'fields_strict'  => true,
// 数据集返回类型 array 数组 collection Collection对象
'resultset_type' => 'array',
// 是否自动写入时间戳字段
'auto_timestamp' => false,
    // 是否需要进行SQL性能分析
'sql_explain'    => false,
];
    

EOF;
    return trim($cfg);
}
function local_mkdirs($path) {
	if(!is_dir($path)) {
		local_mkdirs(dirname($path));
		mkdir($path);
	}
	return is_dir($path);
}

function local_run($sql) {
	global $link, $db;

	if(!isset($sql) || empty($sql)) return;

	$sql = str_replace("\r", "\n", str_replace('', ' '.$db['prefix'], $sql));
	$sql = str_replace("\r", "\n", str_replace('', ' `'.$db['prefix'], $sql));
	$ret = array();
	$num = 0;
	foreach(explode(";\n", trim($sql)) as $query) {
		$ret[$num] = '';
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0].$query[1] == '--') ? '' : $query;
		}
		$num++;
	}
	unset($sql);
	foreach($ret as $query) {
		$query = trim($query);
		if($query) {
			if(!mysql_query($query, $link)) {
				echo mysql_errno() . ": " . mysql_error() . "<br />";
				exit($query);
			}
		}
	}
}

function local_create_sql($schema) {
	$pieces = explode('_', $schema['charset']);
	$charset = $pieces[0];
	$engine = $schema['engine'];
	$sql = "CREATE TABLE IF NOT EXISTS `{$schema['tablename']}` (\n";
	foreach ($schema['fields'] as $value) {
		if(!empty($value['length'])) {
			$length = "({$value['length']})";
		} else {
			$length = '';
		}

		$signed  = empty($value['signed']) ? ' unsigned' : '';
		if(empty($value['null'])) {
			$null = ' NOT NULL';
		} else {
			$null = '';
		}
		if(isset($value['default'])) {
			$default = " DEFAULT '" . $value['default'] . "'";
		} else {
			$default = '';
		}
		if($value['increment']) {
			$increment = ' AUTO_INCREMENT';
		} else {
			$increment = '';
		}

		$sql .= "`{$value['name']}` {$value['type']}{$length}{$signed}{$null}{$default}{$increment},\n";
	}
	foreach ($schema['indexes'] as $value) {
		$fields = implode('`,`', $value['fields']);
		if($value['type'] == 'index') {
			$sql .= "KEY `{$value['name']}` (`{$fields}`),\n";
		}
		if($value['type'] == 'unique') {
			$sql .= "UNIQUE KEY `{$value['name']}` (`{$fields}`),\n";
		}
		if($value['type'] == 'primary') {
			$sql .= "PRIMARY KEY (`{$fields}`),\n";
		}
	}
	$sql = rtrim($sql);
	$sql = rtrim($sql, ',');

	$sql .= "\n) ENGINE=$engine DEFAULT CHARSET=$charset;\n\n";
	return $sql;
}

function __remote_install_headers($ch = '', $header = '') {
	static $hash;
	if(!empty($header)) {
		$pieces = explode(':', $header);
		if(trim($pieces[0]) == 'hash') {
			$hash = trim($pieces[1]);
		}
	}
	if($ch == '' && $header == '') {
		return $hash;
	}
	return strlen($header);
}



function __remote_download_headers($ch = '', $header = '') {
	static $hash;
	if(!empty($header)) {
		$pieces = explode(':', $header);
		if(trim($pieces[0]) == 'hash') {
			$hash = trim($pieces[1]);
		}
	}
	if($ch == '' && $header == '') {
		return $hash;
	}
	return strlen($header);
}



function tpl_frame() {
	global $action, $actions;
	$action = $_COOKIE['action'];
	$step = array_search($action, $actions);
	$steps = array();
	for($i = 0; $i <= $step; $i++) {
		if($i == $step) {
			$steps[$i] = ' list-group-item-info';
		} else {
			$steps[$i] = ' list-group-item-success';
		}
	}
	$progress = $step * 25 + 25;
	$content = ob_get_contents();
	ob_clean();
	$tpl = <<<EOF
<!DOCTYPE html>
<html lang="zh-cn">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>安装系统 - Niushop开源商城</title>
		<link rel="stylesheet" href="./public/install/css/bootstrap.min.css">
		<style>
			html,body{font-size:13px;font-family:"Microsoft YaHei UI", "微软雅黑", "宋体";}
			.pager li.previous a{margin-right:10px;}
			.header a{color:#FFF;}
			.header a:hover{color:#428bca;}
			.footer{padding:10px;}
			.footer a,.footer{color:#eee;font-size:14px;line-height:25px;}
		</style>
		
	</head>
	<body style="background-color:#28b0e4;">
		<div class="container">
			<div class="header" style="margin:15px auto;">
				<ul class="nav nav-pills pull-right" role="tablist">
					<li role="presentation" class="active"><a href="javascript:;">安装Niushop开源商城</a></li>
					<li role="presentation"><a target = "_blank" href="http://www.niushop.com.cn">Niushop开源商城官网</a></li>
					<li role="presentation"><a target = "_blank" href="http://www.niushop.com.cn/forummain.html">访问论坛</a></li>
				</ul>
				<img src="?res=logo" />
			</div>
			<div class="row well" style="margin:auto 0;">
				<div class="col-xs-3">
					<div class="progress" title="安装进度">
						<div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="{$progress}" aria-valuemin="0" aria-valuemax="100" style="width: {$progress}%;">
							{$progress}%
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							安装步骤
						</div>
						<ul class="list-group">
							<a href="javascript:;" class="list-group-item{$steps[0]}"><span class="glyphicon glyphicon-copyright-mark"></span> &nbsp; 许可协议</a>
							<a href="javascript:;" class="list-group-item{$steps[1]}"><span class="glyphicon glyphicon-eye-open"></span> &nbsp; 环境监测</a>
							<a href="javascript:;" class="list-group-item{$steps[2]}"><span class="glyphicon glyphicon-cog"></span> &nbsp; 参数配置</a>
							<a href="javascript:;" class="list-group-item{$steps[3]}"><span class="glyphicon glyphicon-ok"></span> &nbsp; 成功</a>
						</ul>
					</div>
				</div>
				<div class="col-xs-9">
					{$content}
				</div>
			</div>
			<div class="footer" style="margin:15px auto;">
				<div class="text-center">
					<a  target = "_blank" href="http://www.niushop.com.cn">Niushop开源商城官网</a> &nbsp; &nbsp; <a  target = "_blank" href="http://www.niushop.com.cn/forummain.html">Niushop开源商城论坛</a> &nbsp; &nbsp; <a target = "_blank" href="http://www.niushop.com.cn/authorization.html">购买授权</a>
				</div>
				<div class="text-center">
					Powered by <a   target = "_blank" href="http://www.niushop.com.cn"><b>Niushop开源商城</b></a> niu_version &copy; 2015-2025 <a target = "_blank" href="http://www.niushop.com.cn">www.niushop.com.cn</a>
				</div>
			</div>
		</div>
		<script src="./public/install/js/jquery.min.js"></script>
		<script src="./public/install/js/bootstrap.min.js"></script>
	</body>
</html>
EOF;
    include "version.php";
	$niu_version = NIU_VERSION;
    $tpl=str_replace("niu_version", $niu_version, $tpl);
	echo trim($tpl);
}

function tpl_install_license() {
	echo <<<EOF
		<div class="panel panel-default">
			<div class="panel-heading">阅读许可协议</div>
			<div class="panel-body" style="overflow-y:scroll;max-height:400px;line-height:20px;">
				<h3>版权所有 (c)2016，Niushop开源商城团队保留所有权利。 </h3>
				<p>
					感谢您选择Niushop开源商城（以下简称NiuShop）NiuShop基于 PHP + MySQL的技术开发，全部源码开放。 <br />
					为了使你正确并合法的使用本软件，请你在使用前务必阅读清楚下面的协议条款：
				</p>
				<p>
					<strong>一、本授权协议适用且仅适用于Niushop开源商城系统(以下简称Niushop)任何版本，Niushop开源商城官方对本授权协议的最终解释权。</strong>
				</p>
				<p>
					<strong>二、协议许可的权利 </strong>
					<ol>
						<li>非授权用户允许商用，严禁去除Niushop相关的版权信息。</li>
                        <li>请尊重Niushop开发人员劳动成果，严禁使用本系统转卖、销售或二次开发后转卖、销售等商业行为。</li>
                        <li>任何企业和个人不允许对程序代码以任何形式任何目的再发布。</li>
						<li>您可以在协议规定的约束和限制范围内修改Niushop开源商城源代码或界面风格以适应您的网站要求。</li>
						<li>您拥有使用本软件构建的网站全部内容所有权，并独立承担与这些内容的相关法律义务。</li>
						<li>获得商业授权之后，您可以将本软件应用于商业用途，同时依据所购买的授权类型中确定的技术支持内容，自购买时刻起，在技术支持期限内拥有通过指定的方式获得指定范围内的技术支持服务。商业授权用户享有反映和提出意见的权力，相关意见将被作为首要考虑，但没有一定被采纳的承诺或保证。</li>
					</ol>
				</p>
				<p>
					<strong>三、协议规定的约束和限制 </strong>
					<ol>
						<li>未获商业授权之前，允许您对Niushop应用于商业用途，但严禁去除Niushop任何相关的版权信息。</li>
						<li>未经官方许可，不得对本软件或与之关联的商业授权进行出租、出售、抵押或发放子许可证。</li>
						<li>未经官方许可，禁止在Niushop开源商城的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发。</li>
						<li>如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。</li>
					</ol>
				</p>
				<p>
					<strong>四、有限担保和免责声明 </strong>
					<ol>
						<li>本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。</li>
						<li>用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺对免费用户提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。</li>
						<li>电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始确认本协议并安装  Niushop，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。</li>
						<li>如果本软件带有其它软件的整合API示范例子包，这些文件版权不属于本软件官方，并且这些文件是没经过授权发布的，请参考相关软件的使用许可合法的使用。</li>
					</ol>
				</p>
			</div>
		</div>
		<form class="form-inline" role="form" method="post">
			<ul class="pager">
				<li class="pull-left" style="display:block;padding:5px 10px 5px 0;">
					<div class="checkbox">
						<label>
							<input type="checkbox"> 我已经阅读并同意此协议
						</label>
					</div>
				</li>
				<li class="previous"><a href="javascript:;" onclick="if(jQuery(':checkbox:checked').length == 1){jQuery('form')[0].submit();}else{alert('您必须同意软件许可协议才能安装！')};">继续 <span class="glyphicon glyphicon-chevron-right"></span></a></li>
			</ul>
		</form>
EOF;
	tpl_frame();
}

function tpl_install_env($ret = array()) {
	if(empty($ret['continue'])) {
		$continue = '<li class="previous disabled"><a href="javascript:;">请先解决环境问题后继续</a></li>';
	} else {
		$continue = '<li class="previous"><a href="javascript:;" onclick="$(\'#do\').val(\'continue\');$(\'form\')[0].submit();">继续 <span class="glyphicon glyphicon-chevron-right"></span></a></li>';
	}
	echo <<<EOF
		<div class="panel panel-default">
			<div class="panel-heading">服务器信息</div>
			<table class="table table-striped">
				<tr>
					<th style="width:150px;">参数</th>
					<th>值</th>
					<th></th>
				</tr>
				<tr class="{$ret['server']['os']['class']}">
					<td>服务器操作系统</td>
					<td>{$ret['server']['os']['value']}</td>
					<td>{$ret['server']['os']['remark']}</td>
				</tr>
				<tr class="{$ret['server']['sapi']['class']}">
					<td>Web服务器环境</td>
					<td>{$ret['server']['sapi']['value']}</td>
					<td>{$ret['server']['sapi']['remark']}</td>
				</tr>
				<tr class="{$ret['server']['php']['class']}">
					<td>PHP版本</td>
					<td>{$ret['server']['php']['value']}</td>
					<td>{$ret['server']['php']['remark']}</td>
				</tr>
				<tr class="{$ret['server']['dir']['class']}">
					<td>程序安装目录</td>
					<td>{$ret['server']['dir']['value']}</td>
					<td>{$ret['server']['dir']['remark']}</td>
				</tr>
				<tr class="{$ret['server']['disk']['class']}">
					<td>磁盘空间</td>
					<td>{$ret['server']['disk']['value']}</td>
					<td>{$ret['server']['disk']['remark']}</td>
				</tr>
				<tr class="{$ret['server']['upload']['class']}">
					<td>上传限制</td>
					<td>{$ret['server']['upload']['value']}</td>
					<td>{$ret['server']['upload']['remark']}</td>
				</tr>
			</table>
		</div>

		<div class="alert alert-info">PHP环境要求必须满足下列所有条件，否则系统或系统部份功能将无法使用。</div>
		<div class="panel panel-default">
			<div class="panel-heading">PHP环境要求</div>
			<table class="table table-striped">
				<tr>
					<th style="width:150px;">选项</th>
					<th style="width:180px;">要求</th>
					<th style="width:50px;">状态</th>
					<th>说明及帮助</th>
				</tr>
				<tr class="{$ret['php']['version']['class']}">
					<td>PHP版本</td>
					<td>5.4或者5.4以上</td>
					<td>{$ret['php']['version']['value']}</td>
					<td>{$ret['php']['version']['remark']}</td>
				</tr>
				<tr class="{$ret['php']['pdo']['class']}">
					<td>MySQL</td>
					<td>支持</td>
					<td>{$ret['php']['mysql']['value']}</td>
					<td rowspan="2">{$ret['php']['pdo']['remark']}</td>
				</tr>
				<tr class="{$ret['php']['pdo']['class']}">
					<td>PDO_MYSQL</td>
					<td>支持(强烈建议支持)</td>
					<td>{$ret['php']['pdo']['value']}</td>
				</tr>
				<tr class="{$ret['php']['curl']['class']}">
					<td>allow_url_fopen</td>
					<td>支持(建议支持cURL)</td>
					<td>{$ret['php']['fopen']['value']}</td>
					<td rowspan="2">{$ret['php']['curl']['remark']}</td>
				</tr>
				<tr class="{$ret['php']['curl']['class']}">
					<td>cURL</td>
					<td>支持(强烈建议支持)</td>
					<td>{$ret['php']['curl']['value']}</td>
				</tr>
				<tr class="{$ret['php']['gd']['class']}">
					<td>GD2</td>
					<td>支持</td>
					<td>{$ret['php']['gd']['value']}</td>
					<td>{$ret['php']['gd']['remark']}</td>
				</tr>
				<tr class="{$ret['php']['dom']['class']}">
					<td>DOM</td>
					<td>支持</td>
					<td>{$ret['php']['dom']['value']}</td>
					<td>{$ret['php']['dom']['remark']}</td>
				</tr>
				<tr class="{$ret['php']['session']['class']}">
					<td>session.auto_start</td>
					<td>关闭</td>
					<td>{$ret['php']['session']['value']}</td>
					<td>{$ret['php']['session']['remark']}</td>
				</tr>
				<tr class="{$ret['php']['asp_tags']['class']}">
					<td>asp_tags</td>
					<td>关闭</td>
					<td>{$ret['php']['asp_tags']['value']}</td>
					<td>{$ret['php']['asp_tags']['remark']}</td>
				</tr>
			</table>
		</div>

		<div class="alert alert-info">系统要求Niushop开源商城安装目录下的runtime和upload必须可写, 才能使用Niushop开源商城所有功能。</div>
		<div class="panel panel-default">
			<div class="panel-heading">目录权限监测</div>
			<table class="table table-striped">
				<tr>
					<th style="width:150px;">目录</th>
					<th style="width:180px;">要求</th>
					<th style="width:50px;">状态</th>
					<th>说明及帮助</th>
				</tr>
				<tr class="{$ret['write']['bottom']['class']}">
					<td>/</td>
					<td>根目录可写</td>
					<td>{$ret['write']['bottom']['value']}</td>
					<td>{$ret['write']['bottom']['remark']}</td>
				</tr>
				<tr class="{$ret['write']['root']['class']}">
					<td>/upload</td>
					<td>upload目录可写</td>
					<td>{$ret['write']['root']['value']}</td>
					<td>{$ret['write']['root']['remark']}</td>
				</tr>
				<tr class="{$ret['write']['data']['class']}">
					<td>/runtime</td>
					<td>runtime目录可写</td>
					<td>{$ret['write']['data']['value']}</td>
					<td>{$ret['write']['data']['remark']}</td>
				</tr>
				<tr class="{$ret['write']['database']['class']}">
					<td>/application</td>
					<td>application目录可写</td>
					<td>{$ret['write']['database']['value']}</td>
					<td>{$ret['write']['database']['remark']}</td>
				</tr>
			</table>
		</div>
		<form class="form-inline" role="form" method="post">
			<input type="hidden" name="do" id="do" />
			<ul class="pager">
				<li class="previous"><a href="javascript:;" onclick="$('#do').val('back');$('form')[0].submit();"><span class="glyphicon glyphicon-chevron-left"></span> 返回</a></li>
				{$continue}
			</ul>
		</form>
EOF;
	tpl_frame();
}

function tpl_install_db($error = '') {
	if(!empty($error)) {
		$message = '<div class="alert alert-danger">发生错误: ' . $error . '</div>';
	}
	$insTypes = array();
	if(file_exists(IA_ROOT . '/index.php') && is_dir(IA_ROOT . '/app') && is_dir(IA_ROOT . '/web')) {
		$insTypes['local'] = ' checked="checked"';
	} else {
		$insTypes['remote'] = ' checked="checked"';
	}
	if (!empty($_POST['type'])) {
		$insTypes = array();
		$insTypes[$_POST['type']] = ' checked="checked"';
	}
	$disabled = empty($insTypes['local']) ? ' disabled="disabled"' : '';
	echo <<<EOF
	{$message}
	<form class="form-horizontal" method="post" role="form">
		<div class="panel panel-default">
			<div class="panel-heading">安装选项</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">安装方式</label>
					<div class="col-sm-10">
						<!-- <label class="radio-inline">
							<input type="radio" name="type" value="remote"{$insTypes['remote']}> 在线安装
						</label>  -->
						<label class="radio-inline">
							<input type="radio" name="type" value="local"{$insTypes['local']} checked> 离线安装
						</label>
						<!-- <div class="help-block">
							<span style="color:red">由于在线安装是精简版，安装后，请务必注册云服务更新到完整版</span> <br/>
							在线安装能够直接安装最新版本Niushop开源商城系统, 如果在线安装困难, 请下载离线安装包后使用本地安装. <br/>
							离线安装包可能不是最新程序, 如果你不确定, 可以现在访问官网重新下载一份最新的.
						</div> -->
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">数据库选项</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">数据库主机</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="db[server]" id="dbserver" value="127.0.0.1">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">数据库用户</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="db[username]" id="dbusername" value="root">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">数据库密码</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="db[password]" id="dbpassword">
					</div>
				</div>
				<div class="form-group" style="display:none;">
					<label class="col-sm-2 control-label">表前缀</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="db[prefix]" value="ims_">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">数据库名称</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="db[name]" id="dbname" value="niushop_b2c">
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">管理选项</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">管理员账号</label>
					<div class="col-sm-4">
						<input class="form-control" type="username" name="user[username]" value="admin">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">管理员密码</label>
					<div class="col-sm-4">
						<input class="form-control" type="password" name="user[password]" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">确认密码</label>
					<div class="col-sm-4">
						<input class="form-control" type="password" value="">
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="do" id="do" />
		<ul class="pager">
			<li class="previous"><a href="javascript:;" onclick="$('#do').val('back');$('form')[0].submit();"><span class="glyphicon glyphicon-chevron-left"></span> 返回</a></li>
			<li class="previous"><a href="javascript:;" onclick="if(check(this)){jQuery('#do').val('continue');if($('input[name=type]').val() == 'remote'){alert('在线线安装时，安装程序会下载精简版快速完成安装，完成后请务必注册云服务更新到完整版。')}$('form')[0].submit();}">继续 <span class="glyphicon glyphicon-chevron-right"></span></a></li>
		</ul>
	</form>
	<script>
		var lock = false;
		function check(obj) {
			if(lock) {
				return;
			}
			$('.form-control').parent().parent().removeClass('has-error');
			var error = false;
			$('.form-control').each(function(){							   
				if($(this).attr('name') != 'db[password]'){
							    
    				if($(this).val() == '') {
    					$(this).parent().parent().addClass('has-error');
    					this.focus();
    					error = true;
    				}
				}
			});
			if(error) {
				alert('请检查未填项');
				return false;
			}
			if($(':password').eq(0).val() != $(':password').eq(1).val()) {
				$(':password').parent().parent().addClass('has-error');
				alert('确认密码不正确.');
				return false;
			}
			$.ajax({
			     url: "install.php?action=true",
			     type: 'get',
			     async: false,
			     data: {'dbserver':$("#dbserver").val(), 'dbpassword':$("#dbpassword").val(), 'dbusername':$("#dbusername").val(),'dbname':$("#dbname").val()},
			     success: function(res){
    				    if(res > 0){
                                if(confirm("当前数据库已经存在，会覆盖掉本地的数据，确定吗？")){
                                    error = false;
                                }else{
                                    error = true;
                                }
    				    }else{
    				        error = false;
    				    }
			     }
            });
    	    if(error){
    	         return false;
    	    }
			lock = true;
			$(obj).parent().addClass('disabled');
			$(obj).html('正在执行安装');
			return true;
		}
	</script>
EOF;
	tpl_frame();
}

function tpl_install_finish() {
// 	$modules = get_store_module();
//     var_dump(55);
// 	$themes = get_store_theme();
	echo <<<EOF
	<div class="page-header"><h3>安装完成</h3></div>
	<div class="alert alert-success">
		恭喜您!已成功安装“Niushop开源商城”系统，您现在可以: <a target="_blank" class="btn btn-success" href="./index.php">访问网站首页</a>
	    <a target="_blank" class="btn btn-success" href="./index.php?s=/admin">访问网站后台</a>
	</div>
	<div class="form-group">
		<h5><strong></strong></h5>		
	</div>

	
EOF;
	tpl_frame();
}

function tpl_resources() {
	static $res = array(
		'logo' => 'iVBORw0KGgoAAAANSUhEUgAAAaQAAABaCAYAAAD3oyLoAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjU1NzdDQjYzMzQ3RDExRTc4MThBQjZFQTI3NERFNTQxIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjU1NzdDQjY0MzQ3RDExRTc4MThBQjZFQTI3NERFNTQxIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NTU3N0NCNjEzNDdEMTFFNzgxOEFCNkVBMjc0REU1NDEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NTU3N0NCNjIzNDdEMTFFNzgxOEFCNkVBMjc0REU1NDEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7Go4y2AAAb90lEQVR42uxdCZhU1ZU+1TTQiE2DoCA7iHtcwC1KVFxiHDXgiGtEY5zgjMZxkhklmsQl6sQ9TlSMW9xGEsdtjIqJC+qouAIqils6Ci64gawCDXTX3J/3P/tSvuVW1Xu1dJ3/+87XTfPq1Xt3Of855557biabzYpCoVAoFOVGRglJoVAoFEpICoVCoVAoISkUCoVCCUmhUCgUCiUkhUKhUCghKRQKhUKhhKRQKBQKJSSFQqFQKJSQFAqFQqGEpFAoFAqFEpJCoVAolJAUCoVCoVBCUigUCoUSkkKhUCgUSkgKhUKhUEJSKBQKhUIJSaFQKBRKSAqFQqFQKCEpFAqFQglJoVAoFAolJIVCoVAoISmqHXVG+hoZaWRXI98yMsTIxka6YTwYWW1kkZFPjLxv5E0js400829rtRlTQ1cjjUZ6UzY00sVIZyOYqCuNLGY/oI+W8+8KRYchpGHmZ3cjbSEKDJPg7yV4lo2MDOAEa6Vy9FFvZI2RDzkJc9GJn23ie+ROUny+xcg8I6uC2oHf3zVkgmf4/QtD2ikJdOIzdOazCtshS2njsxdCCHj/XYwcZWR/I5tT0bkCY2CukdeMTDPyfySrpMgJJLlJSN/V8b3nsQ/SIuqN+RxtAX1cz2f4kG2RJAYa2YEGwjZGBrEtfEKqs67FeFhi5DMjH3NePmdklpG3+P9JowuNlm5s/wz7qM763ZdMEjqJ4wpz7csU55uiQgnpafNzaIgixqBbZuQSI7el/CwnGPkNB+DanMHdjQrwLCNPBHwWZHSZkf2ozHPfpYEK7SQjrwZ8HgrgcnoOqwM+D6J6j5//LKX339TIpSSOhRYxt7If0C5/NvK7PO8LZXKGkQkk7GLRSsX8rJFbjTzDNisGeO8jQxRaht83gWMgDfQ0MsnIT6jwc/sf4+9tI2eSAJLA7nynfY0M4xgrtD/gMb1k5AEj93HOJoUtjdzCZ2yxSChj/S4pENICGkF4r4dJuOoNdnDUUxEOirnuKirFW1J8lp58ljC0UDGEDeL+JJYwZCMmPdphW4awwtBE7yXNcM22VABhiqc5j/s1UOH9iqSUpCc3lHIEFcZdRu6lYizGO66P6N+6FNse9+5lpAclCF/m6VWG9fGBRiYaGcPIRBL9MZByqJGfG5li5A4jHySgxP379yuxbhpG4wxj7GwjTxq5jj9bVHV3TGAirnC4DpP0IiP/kOKzxFnZayNCRK0OoZQ1ESGNtQ7tsDLl8EE2pg3wjMvzUHynG7kxYTIK+p49jVxt5DEjp0Qo9Li2jcKKlNu+zWH8tUpxIcrhRq43cqeRgxMio6D5vLWRC9kf/0zDpNi2WVVmPYUxNY4e4GQSpKKDEpKrm92XimevDtoW2Q7yjPAyEHo6t8TPBu/uGiMPUnl01en1NRCWu8fIDxMgCFdgnfBaIzcxetARgAjFP9FTGqbDqmMSUj7YTLyw3Rglo7I8o8tzHs4QR30ZnjFDgwXK93YjO0q6obZqABQowmcjy9Qfx9Kz2K8DtSk8zMvVU1JC8kMPN4iXFaSEVFkAASCBoafj9QgBviPeQv3jRp4Sb00IC8jzpfBkBZAhkhS+V+Oe0mlGrpDotdFSYCfxQoVjq9jQysVhbN9GVeMdB/VSWGYMwgHX0Br/oIMQUrWH7NCXWNQe5XCfT6mgEF5DogQyy/zMxnpO8j60QLcTb3EZns9GeTwrstLul+TTpKvJiv+ZFJbZuNTIu+KldsNowCI+wlW9GKXYvAAPuA8NyQWSXKYggLW1V4z81fobxpKfPu9nitpZo/b/reH1GHNb0ZPb2vG7YfRMFW8bgqKGCcm3um428iPx0nKrGZkOQEhYwxnncI+Z4iU8PBVxzWL26SskLXg5yIL7Lo2QXRwU7R0dYFwUCmRr/lK8TERXIENxungpzi+Kt71gBb3UNkYzulBxjxBvP9l+NEA2dPwOrANjTWk8veAkgOdDAsXZCd0PmbLHcIzGheSGkPhnGPlK1Xn1I5+khqDPYrF2iqSbyaUekhsQFopb6EU4bmIMGQUBFjr2YSFL7AB6SxdFEM5MjosVNTinmqicd3e8/gvx9tDtJl6KM9ZoUR1jIb1L27vAvz+nh3O+kb3ZH/8j7puG4YUgKaBnQu+bkWS3Q+D9sNfueHrZcRgt8dtWFFVESMUOxu+Il17co4rboRoIya/WEObpbhPjtUBh3Uuvp9jnQCmhX4i3p+bGAOsUCuWjGp1Txxk5yPHaF+mtTCrCm3xevMSFH9NoyDrM2dEJejQiyWyIzQX2G02W+JDvdvT8FEpIXw9GWGpXSvEbB5WUCkODw6REGG5uwt8LSx7VK46kYgQeFS9BohZr3iHh5x/FLYSGiiMniFfpoljAe0JWI8KprzmMY2x2RQr6thXensgOnB5zDUKYusWgAxFSEtZNF1ppV0t50o2TIKNqQFuEgokLm0BJbpzSc2Hd4/u0aJFV9nmNzid4Hts7XIfQKRIe3k74++H9+qGuuDENb/pfK7w9PyDBxqFeFB2GkOpiLC+kBc90uFdXWl2Tq3CAVPsaEvoprpwKyi4hxJbWeh/WPE6lh9Rag3MJljqSDPo4tBMqKcxO6TleF69CQ1zNRcxRpIGPrPB2RVXzuO0HGVF0GEKKU9RviBeWecORlOAp/VqbNhXSDPOQEGf/1OEeh4i3T6mHNmfiQPjLJeX+D+Ktj6QJhAFRfzIubAryPKjC2xWp7nFLAVoRvEYIyS86ibUClKNxCcWgRtfJRv5dm7ekHhI82SUO16IfkZI9Wqp7zS9pFGtloz23i7kGoTRUsVhegvdBYsmLMZ4/Qr37Suk2l6J9jpLwIsm5GOjQplnRM7pqhpD8a7A+geMGJjoqPZDYOeJVHq4W76Pa9yH5B+q5AOs9D4l3VAHWMvYRbzMiwnlIH8daUxM93loIh7gU140C2mo3h+uQ5fh6id4J74ONsC0xc3uUlC65AeMM1ed3cbz+WH4mCjgT6ktV5R0DrhtjM3SL/yJefBobYjdwmKRn0BqcXOXKvhpIc454m1h3dLwf9qEcTEHID/thcI4O4vVr+HMlDRCUEfqQhIfvmSvlrwAdpFw3sMarf2RFPQ0q+2c9vYMuHNdI+BhcxHdvId5m1Sig+sJrJW43ZD4ukujSRQ00Rl5I+VnwDHuIt2n4aBq4UaE2eFKnSnyyzgtSuxuwa5aQfEBR/S8n/mQH1xunXmK/CkJ9d2tzp0qasPJxLtEBjta6jW6OCrmFBsZ7VChQeAgLfSTljeMjTHwmFd1XFunUk3T8bNJOFlnZZyxlpLjjIBBaGhBzDcoAzStxu3xI46FfxDzvVCIPCWNyf/4+gX2DDb0L2Q+NHIeN9IqwSTguQQR9/bDUblZnzROS0HL+Iy2X6x2uR+l77FHCOsd9Vep9VIsXh8STc9kvaWTTdaX0ZtjlZ/SaQISoMPBuGdoxQ4WKtbFeZeqXIRJf+eB9eqGlBLyx2SSDTIRnmXallb4kmN78N0gH4f8f0XNspZfaLc/7/km8skWu2Irf3ZbwnJwVMCb700C6Vtbf3Atv+iJ61Gvy+B4YDn4YNt/Tu/GdiGphnROZ0LeXaZ7AKcFeOezVe4D9cAaNprt9QioELSQlWIYuO75hPV7BBv2r2gGpERLwiHh1wK6Q4sJQrkC5IqwVov7YJHrQq0vYJiDIfctIRv6py3EJIvBWlpTh+eIqZmQkuTJCYYDxclhI221U4D1fYJRmgeP1qAz+6xTeFZGJH+eQBLJYn+LcGCrt+72wR226uNceDMKcAggJEa0GKxpSDI7n88MYWJTnZ/0s1JEkpBNIzkuLJSRh6OZyvuDpDtejY/6LltEzUlmoltJBrs94D8MY6OxvS2nOJBpMy+tC9vOyErULiOjIMvZLgyMZfiblqXy+wmHcdE7x+/vSWEnyYEJ4JP9m5NU8PoOisihztiUNanhKm/Hd36A3mYmZf6M4l2ZZbQqv90nHdphCZY6s5VOp0DvlEQmAR/Wx9TckfdyRZ9tdR4nDy/Sm7CK8OB36As71FwII6XiS84YBXmibZSCj7w4hJ+D937MtlGKU1VJaHejgXzpcj8HwOz70LHV4UvGQfDwt3pHzB3Kg7CXpp/fCSzifoZnzxCtXlLZ3sreUd3NnnaNS6STlyVhscPjeND3aPUO8o0IA0riLY+v9PD+7IsdwQdLEtfTQxjrebyXb8yzxQoXZPOfGcBryV+eQGFLbZ1P5j87D8/icHnBPyS/852LkYY04d29jU4QTg8jXAY5jvMnylhbTYJFiPSTbU7qSYYsTHa6H8viteNl671SQom/jz0pOc87Xi1vKCXw/wwUIbeHYkK1onaS1QRYW0Gu03tak2B5QDuPK3CdQdAsdrhtIy7HUFdD7O4yptNKm+7F/ivWO4G2jPuINVOQtUn3ws0AXWYZaVxpUP+S/B5Ekmy2v1ddHMzmXbTwm61c6h8GP8CiSjf6ex7PB00I4+dECjZNrqFugQ69ihAbGA9YGx3ActPF3VMFH+PpuPv+jtl5LqsQPJiQW0zehKxaHvdnwE213TeFEmoUAg2wGBQN9Yw6SgQxbDOHvCCv0ofTKI5wQhBNpNX2SYpsgRv/dMvfLWoZRWiS6yOcQtmkpM8I6U0lFGVkYU/NT+v5tqIR848gfw/7hfPZP++9ZXt/M0BCs9TlSPYc9YizcS6K5P2TM9OL8sE/ehsFydMR9D5fw8DSiH5M47+CBneb4rOPpIPRmROWxAt53CMca1pUuyPHw7FqE8Cx3pv45O6g/izmgLxdwHU8mMx7ucD0Y9WaS0t+kvGs4lbKGlImxsJKI9a+hAoLMygkpdaH735+k1JNKBWm4O0p+IT/E6/fghEyjth2edzCtrTlF3Mc//A7EPKDAe8zlJOwXM2n7lTgqgO8bETOu0Ddpbdb9GyMhLdQL9gmy9qmya3P+r5Ve0TKpzrJAeNczKUHjrQfJald6S/6ab6cQY3IEw1ogj/MoQV7kSySkPRiJcjlqZiQdidkRxtL36J3aSVIzLa/M1wuLY/rrff5/t7AxmSQh+aT0U7qmLjWy4CkhGwxrHLV+4qN/ImjU/3dL8fv9A+BWBng1SFQYRYNjvLhnCe3LUMuSlJ4X936qSMLDhEdM+xyO3ULwAYmxX4w3h6PHS3nc9iiJP5YE/f1mSt//oeim1aD2fothMqFRODHmM4heYJ/cWAkOwfalvj2Y5L89SQuEFBUuBYl935pPIMk75Jvr+3W8dil1O3hjufU5HzszUtZCcp1PIvPD1K9Y12NsTs91BNKoyv0xraI9Lfc0zCtp4P83SrqZPkl4SA1S3ObJOGwQo+jtjZylBryqFynTjFwi0bv/7QHaJOmlO7dIMusJbUV6yG9T4krioBo4SjZ9WqJ+Q6Sia8x7vymVs5bbUYC5jHV1rJ9hf9y9lveTtMcHQrkrgHjGUlwxkoK9hUjJPtUyJv5CwXLM9STFvS3i8lP3dwmYAwjNYR9cM8fZWouQns81JpP2kGxP6U95fqach2ytkvj0WMQ9h4q35pV0CCpDBT8gxiOohHI9/y3eIvi1Er/PaTgH6wcVrkAyUtx62Upae0fGjGN4l1Ml/1TdQrA/lVXc8TJPS/rZkLWGzowOIOw9hoQUFSVaENNP2ZBxhTJMZ5GM3iSRQEdsR28Jf3+UY3MDa6zuSuMI93yG/7+STsReJLJ7ONfzAdafXqMB253jHTrt9+JthoVXhbDdjtKeQl8SQqo2tFHJtkYoJoTLtmTIpTWFATxCokNyCC19USHtNZWu+FkxXmOjFLcJsJoAxY6kkdEx/Ywq+K+L28FzxVjoCNnEZVEiBPOgTv+yAvqmd4GfHULvAwbFhXQCtibZgIyQEIJKCEFnb/2G8xckhHXe31oGJ0o7IVkIm+zzScIBid0q7Zl64JaTaZj6ehWEtIN4IWysUZ8g3n6ydckUdToevsZch/DP1kVa0mHoTkspCiscPY0+HKhpGxqoIfZxzDWtUtqqDeXE2xJ/3LYfGsFG8n4pPsv5tILjsuuQvTazRvrnEWkPzUPulPZQ03s5/xcmDda9/DAvfr5RxHM9IevXVwySQfR+ctGTOnwtvY+t6Y2BZLAOfJGEHwSJGqOX8Hes459meVC+MZnG8TSz+LzQUai0cTGfU3xCUg/Jg0ta6UFk9qTbbIi0Ly6GAYuKzTHXwOPFpuMn+TPNdbkWB0/xCynN2T+VgCytvGaHa2GBnl2EZRwFJGb8xKHvYSjcXkMGg5+1B29ikRURAZbwb3Hih/Ttvy0OGQuLS6hb8V1IDJtGUsL7TAohMRvIArycv6NgwfgCvtuvbyn0+uPGk5/YAJ13BDnoXluBKTy8TA9kk4hrhjLkggW/pNZzmujWdo9RdnBr34q51zYcVCBNxG2RyYM9CdjvkHQW4zhZf1NeEGZI5YQZSwF4HMj8G+Fw7SnircHBUk0i7XpDKpjTJX49FuPpOancYsdpIHcrChKvLqMO3N4x+uBXajhaois1LKPxOoDzFlEV/1iYVfQSDrWiLhN4XVC/4fqdrQjK/JD+RLi/L0nhRHGvhHMGSQF6DVs1+ufZroOt53ZZi5wm6ye6IYR3qU1I6iF5+JQK5VsSnSqJ1EisF/xRij+pMsOBepSDBTRdojN0MKgO4eAVTgIM4j0YlkF8GZkyc6X46glYMJ8o8etDT4m3TlErgPJAzTSEy1yOMz+ERgQSRBC7L2TTLPp9H5LRPuIWUoYFfW6Nz/chnOcLJPk14SwNsS+sPupp/R+iCzdQl2wq7skDMPCmBPy9F/+O8YRwHfZ3buFgNPvnhcFLQvWWd/hcac8RhEi3o/47xdZrSkjr41a6kVHZbmizaxiK+nORg3kgFUNTzHUIA90fcw2svKBd3AjdoMDqLrSeXyJRTOcAzKeUDWLKx/GZ47wjEF+1lnkpBjMZ/kBcvo/D9cOpEI6hxwJv9k0HZYI1qNEcr7DGXTctt/L5nlVCWjc3Fkl6m2/htYyV9dOdfaC0D45NOY+GhL9xuIlkAkNuDr2POhrBWBsMK1O1lEbQFGtuuu5bPJffcY6kXyDgYL5fht7c45JC6aCOAr+BfiDRMfhGkhfc3NsK8DgydNWvk/b9WmFYxO/6KOZ+uM8OEdd04kCFjOd9F/C+r1KaadWtpOKqoxe0JSfNfrSkXNamcPjavBodR1ibQYbbBeJ+3MFOlP+gt46+eJf9Aw8ZmXMIJ49gf2wibsde5FrusMYv1qn+9RlQr0o62ynQLzjvCQv3SDA4OWDO7sroyHUkA3hs2AeEDDjs+TuA16JAMrLoxtH7CUILozuDSGT/4mh0XEZDdQK9o7S2lmQ5Zn9vRaCG5zpESkjfxMUMe8WdotnIxt2LFucsR+uiCwchqqQPc7j+RYk//2SUtBdodEUvyuYkG3/Q+Iu1q/mssMTz3RCM9bgbpXYSGoIALxox/Ul5ksZGlG1SUAjT+DyranyOo1/8tRJEC9KoEtOdxoVPermhVBgqqIKO/Y27R9wHhsh3aKxMoKEX97zvins5rQUWQRaC7aU9dN8aMfbg6T1MwlzE92+kketnQGqWXQDeptXgskBXzxDWA7QuDpbgUi11tAZw7Z1U1i5khJ3SlzoMwN6O4SEXzw3KcDN6cJsVQEYY4D+X/KoNd1TA0vUPpiw3EKI9UWorySQMOCpnIH+fJslnGtbR+8Gcx3rdLfLNdel6y3uOqmSygkapcI5vWSFtiGc5lF5P3LLFVxx/I/k7skD96iDjbQ7SNaRg3EZFfJG4HdEAawtp1seSROYy1LKK9xlM5d4/D68Uih2pwS6HfyHMiAVNpH4eKekfSR2GeRxsT+sQWgeEPpFFh1RXxP+3KsMzIJx8O5/jc+2SdWupUKSdaRwuSuE74DFcwN+RQYmjFq7ivxdbns+O9AziMvxWW15VfynvWXJ+FOgRa87HHVCZoU6EsY5tCUiwOobkupMSkhuuJfNfJu4LxlhE3IJSDD5haMW1xIy/OW8SBz4s4aPp5ZQK8IgmMgSS1eGzHu4mKZ0j8eWFkjYQzmKYp027YV27I7w+iMbir1IgpAznP4gPIeubQ4xQPMtQegxxa63L+bz9qMQfCrgGIUH/XKv9qL/ejRhrbXzWw6xozFIJL5aApYzTZP1EiWZ6mEdI9FH03XmN7Qli79GB9Jp60QDXNaQYoJAgFgsRNtu4RN+JkCFizw8X+PmPaI0j/XgcXeLdJN0SPigZ8p/iJYUoGQWjmV40JuJJ4q3bpVW9fSGtUKxxvqV9sg4ILSEr9tv8N7ykNM5ig+cz2VLyt+TMPb8vtqD+xdH2Ux0ICfN6RIRxDKX/II2fIyT6XKUgXEpC8sOIyyzP7AhGX7rx+f8gXpWHZhq/x/Da3NCdX0kiGxKFuo6e6uH8fV2DxFWY7i7plMsJshii0CXims4Sv9bRpUACvlW8NFxkoqCaQkNK749Y8QN09ZM4DmA+FRI6HkkaWN8aQwurKYH7Y/A9y8n3kBR3eFpjjKfeI2XjqZMDOdRL8aVUVlMpIrV7f070vWixF/t+LSSfqVRKryfkFdVLe2HOsLbrLpWNYWxzP3JxSkqevO8d7cr5cHOIZ1In7aV6ZlOx42+7c0yIrL9/byF1Agipf4T+RHh2Avt/T4lfu/TJAjoOSwrPSPuBgdj4+7llcDaRPB/Pmev+fq7FFiGBhA+iEZ8hgQUl0sziO4+3CWkaBxxeZq20H57VKu254qXIykHH+Ju+1uRYFCBNpMKGnXu/jMrcP7kzm/P5RiroQsv+v8RQC/YU/JRWVlLW7Vfsg6v4Mw2ie5qC/sQ6BtJJR5Oc+tOSiVOI/imeCM09x0H6vCSzIDyNiq0tQIni759IuhWpV7J9erG92nLeewNaux8l9H0tJI6pJNs92ScjOcF78zvDSHotx/wXDMu8TAUyQ5I/Mt73tgYG6IF6tlWlrxk2UWlCp2Gt9ZGUvMbO0l5+B7royoBrFvFZhjGE5YflEcV4wvKsbrI+8yUJaWyOh7TKMnS+sn6/m+KKE8TbC+eXsnqAhG3PgZvyuN9geqSr+H5XSfB+xHv43sO/ZvRsNtudX2if1qgufjhAjnszHIafm0v+63CtHGDP0WJ+Qkq/gbQTB80QSzalUm6wrLnlJIT3aBDMkfDNeYri0IOKfwAndT/2R3cSzRKSkG9coV/mSjon8iq+icM43y+L8D67UsEjq8wuugoP5CQSxYyAz+G+/tEuSGbKLXo7iN4EKnj7SRDwQH5AY+22It4Lxs9xNFanhDxflB75BT2nlwoYiyhddB8N3XWEpMOscPccVg5KYPjnewxl53aj9ZilNbuSFu08eoIz6K7Or9D38rcD+NWMdZAoFIr0lY8SkkKhUCiUkBQKhUKhUEJSKBQKhRKSQqFQKBRKSAqFQqFQQlIoFAqFQglJoVAoFEpICoVCoVAoISkUCoVCCUmhUCgUCiUkhUKhUCghKRQKhUKhhKRQKBQKJSSFQqFQKJSQFAqFQqGEpFAoFAqFEpJCoVAolJAUCoVCoVBCUigUCoUSkkKhUCgUSkgKhUKhUEJSKBQKhSIl/L8AAwAPuNy+GZCYbQAAAABJRU5ErkJggg==',
	);
	return $res;
}

function showerror($errno, $message = '') {
	return array(
		'errno' => $errno,
		'error' => $message,
	);
}

function get_store_module() {
	load()->func('communication');
	$response = ihttp_request(APP_STORE_API, array('controller' => 'store', 'action' => 'api', 'do' => 'module'));
	$response = json_decode($response['content'], true);

	$modules = '';
	foreach ($response['message'] as $key => $module) {
		if ($key % 3 < 1) {
			$modules .= '</tr><tr>';
		}
		$module['detail_link'] = APP_STORE_URL . trim($module['detail_link'], '.');
		$modules .= '<td>';
		$modules .= '<div class="col-sm-4">';
		$modules .= '<a href="' . $module['detail_link'] . '" title="查看详情" target="_blank">';
		$modules .= '<img src="' . $module['logo']. '"' . ' width="50" height="50" ' . $module['title'] . '" /></a>';
		$modules .= '</div>';
		$modules .= '<div class="col-sm-8">';
		$modules .= '<p><a href="' . $module['detail_link'] .'" title="查看详情" target="_blank">' . $module['title'] . '</a></p>';
		$modules .= '<p>安装量：<span class="text-danger">' . $module['purchases'] . '</span></p>';
		$modules .= '</div>';
		$modules .= '</td>';
	}
	$modules = substr($modules, 5) . '</tr>';

	return $modules;
}

function get_store_theme() {
	load()->func('communication');
	$response = ihttp_request(APP_STORE_API, array('controller' => 'store', 'action' => 'api', 'do' => 'theme'));
	$response = json_decode($response['content'], true);

	$themes = '<tr><td colspan="' . count($response['message']) . '">';
	$themes .= '<div class="form-group">';
	foreach ($response['message'] as $key => $theme) {
		$theme['detail_link'] = APP_STORE_URL . trim($theme['detail_link'], '.');
		$themes .= '<div class="col-sm-2" style="padding-left: 7px;margin-right: 25px;">';
		$themes .= '<a href="' . $theme['detail_link'] .'" title="查看详情" target="_blank" /><img src="' . $theme['logo']. '" /></a>';
		$themes .= '<p></p><p class="text-right">';
		$themes .= '<a href="' . $theme['detail_link']. '" title="查看详情" target="_blank">'  . $theme['title'] . '</a></p>';
		$themes .= '</div>';
	}
	$themes .= '</div>';

	return $themes;
}

