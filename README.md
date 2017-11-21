# Niushop B2C 商城系统


团队十年电商经验汇集巨献！

Niushop 采用 ThinkPHP5.0 + MySQL 开发语言，完全面向对象的技术架构设计开发。完全开源，是目前国内首家最为完善的开源商城系统。系统支持多语言版本，操作简单、安全稳定，是广大用户二次开发的最佳选择。

1. 非授权用户严禁去除Niushop相关的版权信息。
2. 请尊重Niushop开发人员劳动成果，严禁使用本系统转卖、销售或二次开发后转卖、销售等商业行为！
3. 请关注Niushop官方网址了解产品最新咨询、功能升级、BUG修复。

Niushop 官方网址: http://www.niushop.com.cn

# 安装环境

1. PHP5.4版本以上，支持PHP7.0
2. 支持rewrite伪静态规则
3. 支持php扩展：php_curl,php_gd2,(如果配置邮箱需要添加php_openssl,php_sockets)
4. 设置upload权限，因为系统上传相片生成二维码等需要上传的这个文件夹，需要设置这个文件夹以及子项文件夹777权限
5. 安装完成以后删除install.php

+---------------------------------------------------------------------------------------+
+  推荐使用宝塔Linux面板搭建环境，2分钟装好面板，一键管理服务器，可实现一键部署Niushop  						+
+  查看：https://www.bt.cn/download/linux.html?niushop                                  	+
# 特色功能

1. 完善的QQ、微信第三方支付、第三方用户登录机制
2. 精细的微信模块处理
3. 惰性加载
4. 路由、自动加载的缓存机制
5. 模型及关联MongoDB支持
6. 数据库集群及数据缓冲

# 开始使用

1. 将源码解压到服务器空间
2. 访问你的网址进行安装， 正常会跳转到 http://域名/install.php
3. 按照系统提示进行安装 
4. 进入后台  后台地址：http://域名/index.php?s=/admin
5. 进入前台  前台地址：http://域名/index.php
6. 系统伪静态配置：
    - 配置伪静态环境，apache、iis、nginx 配置环境不同
    - 系统修改伪静态配置：
        ./application/config.php下面修改配置：
        define("REWRITE_MODEL", true); 配置伪静态设置为true  默认false
	 
	 
> 伪静态环境配置：

	[ Apache ]
	1. httpd.conf配置文件中加载了mod_rewrite.so模块
	2. AllowOverride None 将None改为 All
	
	
	[ IIS ]
	如果你的服务器环境支持ISAPI_Rewrite的话，可以配置httpd.ini文件，添加下面的内容：

	RewriteRule (.*)$ /index\.php\?s=$1 [I]

	在IIS的高版本下面可以配置web.Config，在中间添加rewrite节点：

	<rewrite>
	<rules>
	<rule name="OrgPage" stopProcessing="true">
	<match url="^(.*)$" />
	<conditions logicalGrouping="MatchAll">
	<add input="{HTTP_HOST}" pattern="^(.*)$" />
	<add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
	<add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
	</conditions>
	<action type="Rewrite" url="index.php/{R:1}" />
	</rule>
	</rules>
	</rewrite>
	
	[nginx]
	在Nginx低版本中，是不支持PATHINFO的，但是可以通过在Nginx.conf中配置转发规则实现：

	location / {
            if (!-e $request_filename) {
                rewrite  ^(.*)$  /index.php?s=$1  last;  
                break;
            }
        }

# 目录结构

Niushop 开源商城目录说明:

```
├─application 应用目录
│ ├─admin 后台用户目录
│ │ ├─controller 后台用户控制器目录
│ ├─shop 前台目录
│ │ ├─controller 前台控制器目录
│ ├─wap 手机,H5微信端目录
│ ├─controller 控制器
├─data 电商核心业务处理目录
│ ├─api 数据API接口定义函数
│ ├─model 数据表Model定义
│ ├─service Service层业务处理
├─public 公共资源文件目录
│ ├─admin 后台资源文件目录
│ ├─static 公共资源文件目录
│ ├─install 安装文件资源目录
├─runtime 运行时
├─template 模版
├─thinkphp Thinkphp
├─upload 上传文件目录
│ ├─advertising 广告位
│ ├─avator 用户头像
│ ├─common 公共上传文件目录
│ ├─goods 商品相册上传目录
│ ├─goods_brand 商品品牌图片
│ ├─goods_category 商品分类图片
│ ├─goods_qrcode 商品二维码图片
│ ├─goods_sku 商品SKU图片
│ ├─qrcode 店铺二维码
├─vendor Thinkphp支持库(必须)
├─.htaccess 伪静态
├─.index.php 入口文件
```

# 常见问题

- 安装时提示必须同意安装协议解决方案

    安装时出现“必须同意安装协议”，一般是使用了Chrome内核的浏览器，您可以尝试更换IE或火狐等浏览器安装也可以设置下Chrome浏览器的配置，步骤如下：在Chrome的“设置”中找到“显示高级设置”找到“隐私设置”中的“内容设置”按钮，并点开在”cookies“选项…	

# 许可协议