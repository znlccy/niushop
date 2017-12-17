SET NAMES 'utf8';

ALTER TABLE ns_goods_evaluate MODIFY again_image varchar(1000) NOT NULL DEFAULT "" COMMENT "追评评价图片";
ALTER TABLE ns_goods_evaluate MODIFY image varchar(1000) NOT NULL DEFAULT "" COMMENT "评价图片";

#广告位
UPDATE sys_module sm set sm.url="system/shopadvlist",sm.method = "shopadvlist" WHERE sm.module_name="广告位" AND sm.url = "system/shopadvpositionlist";
INSERT INTO sys_module(module_name, module, controller, method, pid, level, url, is_menu, is_dev, sort, `desc`, module_picture, icon_class, is_control_auth, create_time, modify_time) VALUES
('广告位管理', 'admin', 'system', 'shopadvpositionlist',IFNULL((SELECT module_id FROM sys_module s1 WHERE s1.module_name='广告位' and s1.url='system/shopadvlist' LIMIT 1), 0), 3, 'system/shopadvpositionlist', 0, 0, 0, '', '', '', 1, unix_timestamp(now()), 0);

#公安备案功能
ALTER TABLE sys_website ADD web_gov_record varchar(60) NOT NULL DEFAULT "" COMMENT "网站公安备案信息";
ALTER TABLE sys_website ADD web_gov_record_url varchar(255) NOT NULL DEFAULT "" COMMENT "网站公安备案跳转链接地址";

#虚拟商品功能
CREATE TABLE ns_virtual_goods (
  virtual_goods_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  virtual_code varbinary(255) NOT NULL COMMENT '虚拟码',
  virtual_goods_name varchar(255) NOT NULL COMMENT '虚拟商品名称',
  money decimal(10, 2) NOT NULL COMMENT '虚拟商品金额',
  buyer_id int(11) NOT NULL COMMENT '买家id',
  buyer_nickname varchar(255) NOT NULL COMMENT '买家名称',
  order_goods_id int(11) NOT NULL COMMENT '关联订单项id',
  order_no varchar(255) NOT NULL COMMENT '订单编号',
  validity_period int(11) NOT NULL COMMENT '有效期/天(0表示不限制)',
  start_time int(11) NOT NULL COMMENT '有效期开始时间',
  end_time int(11) NOT NULL COMMENT '有效期结束时间',
  use_number int(11) NOT NULL COMMENT '使用次数',
  confine_use_number int(11) NOT NULL COMMENT '限制使用次数',
  use_status tinyint(1) NOT NULL COMMENT '使用状态(-1:已过期,0:未使用,1:已使用)',
  shop_id int(11) NOT NULL COMMENT '店铺id',
  remark varchar(255) DEFAULT '' COMMENT '备注',
  create_time int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (virtual_goods_id)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '虚拟商品列表(用户下单支成功后存放)';

CREATE TABLE ns_virtual_goods_group (
  virtual_goods_group_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '虚拟商品分组id',
  virtual_goods_group_name varchar(255) NOT NULL DEFAULT '' COMMENT '虚拟商品分组名称',
  interfaces varchar(1000) DEFAULT '' COMMENT '接口调用地址（JSON）',
  shop_id int(11) NOT NULL DEFAULT 0 COMMENT '店铺id',
  create_time int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (virtual_goods_group_id)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '虚拟商品分组表';

CREATE TABLE ns_virtual_goods_type (
  virtual_goods_type_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '虚拟商品类型id',
  virtual_goods_group_id int(11) NOT NULL COMMENT '关联虚拟商品分组id',
  virtual_goods_type_name varchar(255) NOT NULL COMMENT '虚拟商品类型名称',
  validity_period int(11) NOT NULL DEFAULT 0 COMMENT '有效期/天(0表示不限制)',
  is_enabled tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否启用0：禁用，1启用（禁用后要查询关联的虚拟商品给予弹出确认提示框，确认后将商品下架）',
  money decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '金额',
  config_info varchar(1000) NOT NULL COMMENT '配置信息（API接口、参数等）',
  confine_use_number int(11) NOT NULL DEFAULT 0 COMMENT '限制使用次数',
  shop_id int(11) NOT NULL COMMENT '店铺id',
  create_time int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (virtual_goods_type_id)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
CHARACTER SET utf8;

ALTER TABLE ns_goods ADD virtual_goods_type_id int NULL DEFAULT 0 COMMENT "虚拟商品类型id";
ALTER TABLE ns_goods ADD production_date int NOT NULL DEFAULT 0 COMMENT "生产日期";
ALTER TABLE ns_goods ADD shelf_life varchar(50) NOT NULL DEFAULT "" COMMENT "保质期";

ALTER TABLE ns_goods_deleted MODIFY goods_name varchar(100) NOT NULL DEFAULT "" COMMENT "商品名称";
ALTER TABLE ns_goods_deleted ADD virtual_goods_type_id int NULL DEFAULT 0 COMMENT "虚拟商品类型id";
ALTER TABLE ns_goods_deleted ADD production_date int NOT NULL DEFAULT 0 COMMENT "生产日期";
ALTER TABLE ns_goods_deleted ADD shelf_life varchar(50) NOT NULL DEFAULT "" COMMENT "保质期";

INSERT INTO sys_module(module_name, module, controller, method, pid, level, url, is_menu, is_dev, sort, `desc`, module_picture, icon_class, is_control_auth, create_time, modify_time) VALUES
('虚拟商品类型', 'admin', 'goods', 'virtualgoodstypelist',IFNULL((SELECT module_id FROM sys_module s1 WHERE s1.module_name='商品' and s1.url='goods/goodslist' LIMIT 1), 0), 2, 'goods/virtualgoodstypelist', 1, 0, 3, '', '', '', 1, unix_timestamp(now()), 0);
INSERT INTO sys_module(module_name, module, controller, method, pid, level, url, is_menu, is_dev, sort, `desc`, module_picture, icon_class, is_control_auth, create_time, modify_time) VALUES
('编辑虚拟商品类型', 'admin', 'goods', 'editvirtualgoodstype',IFNULL((SELECT module_id FROM sys_module s1 WHERE s1.module_name='虚拟商品类型' and s1.url='goods/virtualgoodstypelist' LIMIT 1), 0), 3, 'goods/editvirtualgoodstype', 0, 0, 1, '', '', '', 1, unix_timestamp(now()), 0);
INSERT INTO sys_module(module_name, module, controller, method, pid, level, url, is_menu, is_dev, sort, `desc`, module_picture, icon_class, is_control_auth, create_time, modify_time) VALUES
('虚拟订单', 'admin', 'order', 'virtualorderlist',IFNULL((SELECT module_id FROM sys_module s1 WHERE s1.module_name='订单' and s1.url='order/orderlist' LIMIT 1), 0), 2, 'order/virtualorderlist', 1, 0, 2, '', '', '', 1, unix_timestamp(now()), 0);
INSERT INTO sys_module(module_name, module, controller, method, pid, level, url, is_menu, is_dev, sort, `desc`, module_picture, icon_class, is_control_auth, create_time, modify_time) VALUES
('虚拟订单详情', 'admin', 'order', 'virtualorderdetail',IFNULL((SELECT module_id FROM sys_module s1 WHERE s1.module_name='虚拟订单' and s1.url='order/virtualorderlist' LIMIT 1), 0), 3, 'order/virtualorderdetail', 0, 0, 2, '', '', '', 1, unix_timestamp(now()), 0);

ALTER TABLE ns_platform_adv ADD adv_code text NOT NULL COMMENT "广告代码";

ALTER TABLE ns_order ADD fixed_telephone varchar(50) NOT NULL DEFAULT '' COMMENT '固定电话';