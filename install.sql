# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 172.16.6.198 (MySQL 5.6.25-log)
# Database: xl_node_tools
# Generation Time: 2021-04-01 06:48:22 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table admin_file
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_file`;

CREATE TABLE `admin_file` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '文件id',
  `file_type` varchar(32) DEFAULT NULL COMMENT '文件类型',
  `file_path` varchar(128) DEFAULT NULL COMMENT '文件路径',
  `file_ext` varchar(16) DEFAULT NULL COMMENT '扩展名',
  `file_size` int(11) DEFAULT NULL COMMENT '文件尺寸',
  `user_id` int(11) DEFAULT NULL COMMENT '上传用户Id',
  `file_hash` varchar(64) DEFAULT NULL COMMENT '文件哈希值',
  `create_time` datetime DEFAULT NULL COMMENT '上传时间',
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


# Dump of table admin_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_log`;

CREATE TABLE `admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `data_type` varchar(64) NOT NULL DEFAULT '',
  `data_id` int(11) NOT NULL,
  `operate_type` varchar(32) NOT NULL DEFAULT '',
  `log_message` varchar(64) NOT NULL DEFAULT '' COMMENT '内容',
  `log_data` text NOT NULL COMMENT 'IP',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `request_url` varchar(128) NOT NULL DEFAULT '',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作时间',
  PRIMARY KEY (`id`),
  KEY `idx_userid` (`user_id`),
  KEY `idx_createtime` (`create_time`),
  KEY `idx_dataid_datatype` (`data_id`,`data_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员日志表';


# Dump of table admin_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_menu`;

CREATE TABLE `admin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `icon` varchar(50) DEFAULT '' COMMENT '图标',
  `path` varchar(100) NOT NULL DEFAULT '' COMMENT '菜单路径',
  `menu_name` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '菜单级别',
  `weight` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `type` enum('menu','node') NOT NULL DEFAULT 'node' COMMENT 'menu为菜单,file为权限节点',
  `status` varchar(30) NOT NULL DEFAULT 'show' COMMENT '菜单状态on正常 off未生效',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `is_public` tinyint(4) DEFAULT '0' COMMENT '是否不校验权限的公用菜单',
  `is_hidden` tinyint(4) DEFAULT '0' COMMENT '是否是前端隐藏页面',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_path` (`path`),
  KEY `idx_parentid` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单表';

LOCK TABLES `admin_menu` WRITE;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;

INSERT INTO `admin_menu` (`id`, `icon`, `path`, `menu_name`, `parent_id`, `level`, `weight`, `type`, `status`, `remark`, `is_public`, `is_hidden`, `create_time`, `update_time`, `deleted_at`)
VALUES
	(1,'ums','ums','权限管理',0,1,0,'menu','on','权限管理',0,0,'2020-12-29 00:00:00','2020-12-29 00:00:00',NULL),
	(2,'ums-admin','admin','用户管理',1,2,30,'menu','on','product-list',0,0,'2020-12-29 00:00:00','2021-01-09 15:47:04',NULL),
	(3,'ums-role','role','角色管理',1,2,10,'menu','on','角色管理列表',0,0,'2020-12-29 00:00:00','2021-01-18 23:59:42',NULL),
	(4,'ums-menu','menu','菜单管理',1,2,0,'menu','on','菜单管理列表',0,0,'2020-12-29 00:00:00','2021-01-09 15:47:19',NULL),
	(5,'product','home','默认公用权限组',0,1,0,'node','on','首页',1,0,'2020-12-29 00:00:00','2020-12-29 00:00:00',NULL),
	(6,'product-list','/admin/home/menu','获取前端用户菜单列表',5,2,10,'node','on','product-list',1,0,'2020-12-29 00:00:00','2021-01-09 11:28:12',NULL),
	(7,'product-list','/admin/user/list','获取用户列表接口',2,3,0,'node','on','接口',0,0,'2020-12-29 00:00:00','2021-01-09 11:25:10',NULL),
	(8,'product-list','/admin/menu/list','获取菜单列表接口',4,3,0,'node','on','接口',0,0,'2020-12-29 00:00:00','2020-12-29 00:00:00',NULL),
	(9,'product-list','/admin/role/list','获取用户下属角色列表',5,2,0,'node','on','接口',1,0,'2020-12-29 00:00:00','2021-01-18 23:59:42',NULL),
	(12,'product-list','/admin/menu/get','获取菜单详情接口',4,3,0,'node','on','接口',0,0,'2020-12-29 00:00:00','2020-12-29 00:00:00',NULL),
	(13,'product-list','/admin/menu/update','更新菜单接口',4,3,0,'node','on','接口',0,0,'2020-12-29 00:00:00','2021-01-08 18:28:40',NULL),
	(17,'product-list','/admin/user/update','更新用户信息接口',2,3,0,'node','on','接口',0,0,'2020-12-29 00:00:00','2021-01-09 11:28:18',NULL),
	(18,'product-list','/admin/user/roleUpdate','更新用户所属用户组',2,3,0,'node','on','接口',0,0,'2020-12-29 00:00:00','2021-01-09 11:28:18',NULL),
	(19,'product-list','updateMenu','编辑菜单',4,3,0,'menu','on','菜单管理-编辑菜单',0,1,'2020-12-29 00:00:00','2021-01-09 15:47:19',NULL),
	(20,'product-list','addMenu','添加菜单',4,3,0,'menu','on','菜单管理-添加菜单',0,1,'2020-12-29 00:00:00','2021-01-09 15:47:19',NULL),
	(21,'product-list','allocMenu','分配菜单',3,3,10,'menu','on','角色管理-分配菜单',0,1,'2020-12-29 00:00:00','2021-02-20 11:40:21',NULL),
	(22,'product-list','/admin/menu/listFormat','获取菜单列表接口',4,3,0,'node','on','接口',0,0,'2020-12-29 00:00:00','2020-12-29 00:00:00',NULL),
	(23,'product-list','/admin/role/updateMenu','更新角色菜单权限接口',3,3,0,'node','on','接口',0,0,'2020-12-29 00:00:00','2021-02-20 09:51:59',NULL),
	(24,'product-list','/admin/menu/create','创建菜单接口',4,3,0,'node','on','接口',0,0,'2020-12-29 00:00:00','2021-01-08 18:28:40','2021-01-18 17:20:53'),
	(25,'product-list','/admin/menu/delete','删除菜单接口',4,3,0,'node','on','接口',0,0,'2020-12-29 00:00:00','2021-01-20 14:40:04',NULL),
	(26,'product-list','/admin/user/delete','删除用户接口',2,3,0,'node','on','接口',0,0,'2020-12-29 00:00:00','2021-01-09 11:28:18',NULL),
	(27,'product-list','/admin/role/update','更新角色信息',3,3,0,'node','on','接口',0,0,'2020-12-29 00:00:00','2021-01-18 23:59:42',NULL),
	(29,'product-list','/admin/role/get','获取角色信息',3,3,0,'node','on','接口',0,0,'2020-12-29 00:00:00','2021-01-18 23:59:42',NULL),
	(30,'product-list','/admin/user/get','获取用户信息接口',5,2,0,'node','on','接口',1,0,'2020-12-29 00:00:00','2021-01-09 11:28:18',NULL),
	(31,'product-list','/admin/home/user','获取当前登录用户信息接口',5,2,0,'node','on','接口',1,0,'2020-12-29 00:00:00','2021-01-09 11:28:18',NULL),
	(32,'product-list','/admin/home/fileUpload','上传文件',5,2,0,'node','on','接口',1,0,'2020-12-29 00:00:00','2021-01-09 11:28:18',NULL),
	(33,'product-list','/admin/home/userUpdate','更新用户信息',5,2,0,'node','on','接口',1,0,'2020-12-29 00:00:00','2021-01-09 11:28:18',NULL);
/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_role`;

CREATE TABLE `admin_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(64) NOT NULL DEFAULT '' COMMENT '用户组名称',
  `role_code` varchar(32) NOT NULL DEFAULT '' COMMENT '用户组标识',
  `status` varchar(32) NOT NULL DEFAULT 'on' COMMENT '用户组状态on正常 off未生效',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父级用户组Id',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台用户组';

LOCK TABLES `admin_role` WRITE;
/*!40000 ALTER TABLE `admin_role` DISABLE KEYS */;

INSERT INTO `admin_role` (`id`, `role_name`, `role_code`, `status`, `parent_id`, `create_time`, `update_time`, `deleted_at`)
VALUES
	(1,'超级管理员','admin','on',0,'2020-12-29 15:10:56','2020-12-29 15:10:56',NULL),
	(2,'游客','guest','on',1,'2020-12-29 16:05:02','2020-12-29 16:05:02',NULL);

/*!40000 ALTER TABLE `admin_role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_role_menu_map
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_role_menu_map`;

CREATE TABLE `admin_role_menu_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联用户组id',
  `menu_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联菜单id',
  `status` varchar(32) NOT NULL DEFAULT 'on' COMMENT '关联状态on生效 off失效',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_roleid` (`role_id`),
  KEY `idx_menuid` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户组对应菜单权限';


# Dump of table admin_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_user`;

CREATE TABLE `admin_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) NOT NULL COMMENT '用户名',
  `password` varchar(256) NOT NULL COMMENT '密码',
  `true_name` varchar(64) NOT NULL COMMENT '用户姓名',
  `mobile` varchar(64) DEFAULT NULL COMMENT '手机号',
  `email` varchar(128) DEFAULT NULL COMMENT '邮箱',
  `avatar` varchar(256) DEFAULT NULL COMMENT '头像地址',
  `role_id` int(11) NOT NULL DEFAULT '2' COMMENT '所属用户组id 默认游客',
  `status` varchar(32) NOT NULL DEFAULT 'on',
  `password_time` datetime DEFAULT NULL COMMENT '最后修改密码时间',
  `login_time` datetime DEFAULT NULL COMMENT '最后登录时间',
  `remark` text,
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_roleid` (`role_id`),
  KEY `idx_username` (`username`),
  KEY `idx_mobile` (`mobile`),
  KEY `idx_createtime` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台用户表';

LOCK TABLES `admin_user` WRITE;
/*!40000 ALTER TABLE `admin_user` DISABLE KEYS */;

INSERT INTO `admin_user` (`id`, `username`, `password`, `true_name`, `mobile`, `email`, `avatar`, `role_id`, `status`, `password_time`, `login_time`, `remark`, `create_time`, `update_time`, `deleted_at`)
VALUES
	(1,'admin','56f9138a217e25b5d0060512a7abed70','管理员','13930125015','admin@xianglin.cn',NULL,1,'on','2020-12-28 15:15:05','2021-04-01 14:35:50',NULL,'2020-12-28 15:15:05','2021-04-01 14:35:50',NULL);
/*!40000 ALTER TABLE `admin_user` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
