<?php
if (!defined('IN_FINECMS')) exit('No permission resources');

return array(
    "DROP TABLE IF EXISTS `{prefix}variable`;",
	"CREATE TABLE IF NOT EXISTS `{prefix}variable` (
	`id` smallint(5) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL COMMENT '变量名',
	`content` TEXT NOT NULL COMMENT '变量值',
	`type` varchar(20) NOT NULL COMMENT '字段类型',
	`info` varchar(255) NOT NULL COMMENT '说明',
	PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;",
);