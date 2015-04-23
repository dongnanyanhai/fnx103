DROP TABLE IF EXISTS `{pre}content_1_verify`;
CREATE TABLE IF NOT EXISTS `{pre}content_1_verify` (
  `id` int(10) NOT NULL,
  `catid` smallint(5) NOT NULL,
  `modelid` smallint(5) NOT NULL,
  `userid` mediumint(8) NOT NULL,
  `username` CHAR( 20 ) NOT NULL,
  `title` varchar(255) NOT NULL,
  `tablename` char(30) NOT NULL,
  `updatetime` bigint(10) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `content` longtext NOT NULL,
  KEY `id` (`id`),
  KEY `catid` (`catid`),
  KEY `modelid` (`modelid`,`updatetime`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}content`;
CREATE TABLE IF NOT EXISTS `{pre}content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}form`;
CREATE TABLE IF NOT EXISTS `{pre}form` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}content_1_extend`;
CREATE TABLE IF NOT EXISTS `{pre}content_1_extend` (
  `id` int(10) NOT NULL,
  `catid` smallint(5) NOT NULL,
  `relation` varchar(255) NOT NULL,
  `verify` varchar(255) NOT NULL,
  `position` varchar(100) NOT NULL,
  KEY `id` (`id`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}ip`;
CREATE TABLE IF NOT EXISTS `{pre}ip` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) NOT NULL,
  `addtime` bigint(10) NOT NULL,
  `endtime` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}tag`;
CREATE TABLE IF NOT EXISTS `{pre}tag` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `letter` varchar(200) NOT NULL,
  `listorder` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `letter` (`letter`,`listorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}tag_cache`;
CREATE TABLE IF NOT EXISTS `{pre}tag_cache` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `params` varchar(32) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `addtime` bigint(10) unsigned NOT NULL DEFAULT '0',
  `sql` mediumtext NOT NULL,
  `total` mediumint(8) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `params` (`params`,`addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}search`;
CREATE TABLE IF NOT EXISTS `{pre}search` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `modelid` SMALLINT(5) NOT NULL,
  `catid` SMALLINT(5) NOT NULL,
  `params` varchar(32) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `addtime` bigint(10) unsigned NOT NULL DEFAULT '0',
  `sql` text NOT NULL,
  `total` mediumint(8) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `params` (`params`,`addtime`),
  KEY `modelid` (`modelid`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}block`;
CREATE TABLE `{pre}block` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `site` tinyint(3) NOT NULL COMMENT '站点id',
  `type` tinyint(1) NOT NULL,
  `name` varchar(50) NOT NULL,
  `content` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{pre}category`;
CREATE TABLE `{pre}category` (
  `catid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `site` tinyint(3) NOT NULL COMMENT '站点id',
  `typeid` tinyint(1) NOT NULL COMMENT '类别(1内容,2单页,3外链)',
  `modelid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `parentid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `arrparentid` varchar(255) NOT NULL,
  `child` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否存在子栏目，1，存在',
  `arrchildid` varchar(255) NOT NULL,
  `catname` varchar(30) NOT NULL COMMENT '栏目名称',
  `image` varchar(100) NOT NULL COMMENT '图片',
  `content` mediumtext NOT NULL COMMENT '单网页内容',
  `meta_title` varchar(255) NOT NULL,
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  `catdir` varchar(30) NOT NULL COMMENT '栏目URL目录',
  `url` varchar(100) NOT NULL COMMENT 'URL地址',
  `urlpath` varchar(255) NOT NULL,
  `items` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '内容数量',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ismenu` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否为菜单',
  `categorytpl` varchar(50) NOT NULL,
  `listtpl` varchar(50) NOT NULL,
  `showtpl` varchar(50) NOT NULL,
  `setting` text NOT NULL,
  `pagesize` smallint(5) NOT NULL,
  PRIMARY KEY (`catid`),
  KEY `listorder` (`listorder`,`child`),
  KEY `ismenu` (`ismenu`),
  KEY `parentid` (`parentid`),
  KEY `site` (`site`),
  KEY `modelid` (`modelid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}category_block`;
CREATE TABLE `{pre}category_block` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `site` tinyint(3) NOT NULL COMMENT '站点id',
  `type` tinyint(1) NOT NULL,
  `name` varchar(50) NOT NULL,
  `fieldname` varchar(50) NOT NULL,
  `content` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}content_1`;
CREATE TABLE `{pre}content_1` (
  `id` int(10) unsigned NOT NULL,
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `catid2` varchar(255) NOT NULL,
  `modelid` smallint(5) NOT NULL,
  `title` varchar(80) NOT NULL DEFAULT '',
  `thumb` varchar(255) NOT NULL DEFAULT '',
  `keywords` char(40) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `url` char(100) NOT NULL,
  `listorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `hits` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sysadd` tinyint(1) NOT NULL COMMENT '是否后台添加',
  `userid` smallint(8) NOT NULL,
  `username` char(20) NOT NULL,
  `inputtime` bigint(10) unsigned NOT NULL DEFAULT '0',
  `updatetime` bigint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY `id` (`id`),
  KEY `admin` (`modelid`,`status`,`listorder`,`updatetime`),
  KEY `catid` (`catid`,`status`,`updatetime`),
  KEY `member` (`userid`,`modelid`,`status`,`sysadd`,`updatetime`),
  KEY `status` (`status`,`updatetime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}favorite`;
CREATE TABLE `{pre}favorite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site` tinyint(3) NOT NULL COMMENT '站点id',
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `contentid` int(10) NOT NULL,
  `title` char(100) NOT NULL,
  `url` char(100) NOT NULL,
  `adddate` bigint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `contentid` (`contentid`),
  KEY `userid` (`userid`),
  KEY `site` (`site`),
  KEY `adddate` (`adddate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}linkage`;
CREATE TABLE `{pre}linkage` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `site` tinyint(3) NOT NULL COMMENT '站点id',
  `name` varchar(30) NOT NULL,
  `parentid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `child` tinyint(1) NOT NULL,
  `arrchilds` varchar(200) NOT NULL,
  `keyid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `list` (`site`,`parentid`,`keyid`,`listorder`),
  KEY `keyid` (`site`,`keyid`),
  KEY `child` (`child`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}member`;
CREATE TABLE `{pre}member` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(20) NOT NULL DEFAULT '',
  `password` char(32) NOT NULL DEFAULT '',
  `salt` CHAR(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `avatar` varchar(100) NOT NULL DEFAULT '',
  `groupid` smallint(5) NOT NULL DEFAULT '1',
  `modelid` smallint(5) NOT NULL,
  `credits` int(10) NOT NULL,
  `regdate` bigint(10) unsigned NOT NULL DEFAULT '0',
  `regip` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `randcode` varchar(32) NOT NULL,
  `lastloginip` varchar(15) NOT NULL,
  `lastlogintime` bigint(10) NOT NULL,
  `loginip` varchar(15) NOT NULL,
  `logintime` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `groupid` (`groupid`),
  KEY `status` (`status`),
  KEY `modelid` (`modelid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}member_count`;
CREATE TABLE `{pre}member_count` (
  `id` mediumint(8) NOT NULL,
  `post` mediumint(5) NOT NULL,
  `pms` mediumint(5) NOT NULL,
  `updatetime` bigint(10) NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}member_group`;
CREATE TABLE `{pre}member_group` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `credits` mediumint(8) NOT NULL,
  `allowpost` mediumint(8) NOT NULL,
  `allowpms` mediumint(8) NOT NULL,
  `allowattachment` tinyint(1) NOT NULL,
  `postverify` tinyint(1) NOT NULL,
  `auto` tinyint(1) NOT NULL DEFAULT '0',
  `filesize` smallint(5) NOT NULL,
  `listorder` tinyint(3) NOT NULL,
  `disabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{pre}member_group` VALUES('1','新手上路','0','3','1','0','1','0','5','0','0');
INSERT INTO `{pre}member_group` VALUES('2','普通会员','20','1','0','0','1','0','10','0','0');
INSERT INTO `{pre}member_group` VALUES('3','中级会员','50','10','0','0','0','0','20','0','0');
INSERT INTO `{pre}member_group` VALUES('4','高级会员','100','12','0','1','0','0','50','0','0');
INSERT INTO `{pre}member_group` VALUES('5','金牌会员','200','100','10','1','0','0','0','0','0');

DROP TABLE IF EXISTS `{pre}member_pms`;
CREATE TABLE `{pre}member_pms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sendname` varchar(30) NOT NULL DEFAULT '',
  `sendid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `toname` varchar(30) NOT NULL DEFAULT '',
  `toid` mediumint(8) NOT NULL,
  `isadmin` tinyint(1) NOT NULL,
  `title` varchar(60) NOT NULL DEFAULT '',
  `sendtime` bigint(10) unsigned NOT NULL DEFAULT '0',
  `hasview` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `senddel` mediumint(8) NOT NULL,
  `todel` mediumint(8) NOT NULL,
  `content` text,
  PRIMARY KEY (`id`),
  KEY `sendtime` (`sendtime`),
  KEY `sendid` (`sendid`),
  KEY `hasview` (`hasview`),
  KEY `isadmin` (`isadmin`),
  KEY `toid` (`toid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{pre}model`;
CREATE TABLE `{pre}model` (
  `modelid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `site` tinyint(3) NOT NULL COMMENT '站点id',
  `typeid` tinyint(3) NOT NULL,
  `modelname` char(30) NOT NULL,
  `tablename` varchar(30) NOT NULL,
  `categorytpl` varchar(30) NOT NULL,
  `listtpl` varchar(30) NOT NULL,
  `showtpl` varchar(30) NOT NULL,
  `joinid` smallint(5) NULL,
  `setting` TEXT NULL,
  PRIMARY KEY (`modelid`),
  KEY `typeid` (`typeid`),
  KEY `site` (`site`),
  KEY `joinid` (`joinid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}model_field`;
CREATE TABLE `{pre}model_field` (
  `fieldid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `modelid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `field` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `type` varchar(15) NOT NULL,
  `length` char(10) NOT NULL,
  `indexkey` varchar(10) NOT NULL,
  `isshow` tinyint(1) NOT NULL,
  `tips` text NOT NULL,
  `not_null` tinyint(1) NOT NULL DEFAULT '0',
  `pattern` varchar(255) NOT NULL,
  `errortips` varchar(255) NOT NULL,
  `formtype` varchar(20) NOT NULL,
  `setting` mediumtext NOT NULL,
  `listorder` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldid`),
  KEY `modelid` (`modelid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}oauth`;
CREATE TABLE `{pre}oauth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '',
  `oauth_openid` varchar(80) NOT NULL DEFAULT '',
  `oauth_name` varchar(30) NOT NULL DEFAULT '',
  `oauth_data` text NOT NULL,
  `nickname` varchar(255) NOT NULL DEFAULT '',
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `logintimes` bigint(10) unsigned NOT NULL DEFAULT '0',
  `logintime` bigint(10) unsigned NOT NULL DEFAULT '0',
  `addtime` bigint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `site` (`oauth_openid`,`oauth_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{pre}plugin`;
CREATE TABLE IF NOT EXISTS `{pre}plugin` (
  `pluginid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `typeid` tinyint(1) NOT NULL,
  `markid` smallint(5) NOT NULL,
  `name` varchar(40) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `controller` varchar(30) NOT NULL DEFAULT '',
  `dir` varchar(30) NOT NULL,
  `author` varchar(100) NOT NULL DEFAULT '',
  `version` varchar(20) NOT NULL DEFAULT '',
  `disable` tinyint(1) NOT NULL DEFAULT '0',
  `setting` text NOT NULL,
  PRIMARY KEY (`pluginid`),
  KEY `dir` (`dir`),
  KEY `markid` (`markid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}position`;
CREATE TABLE `{pre}position` (
  `posid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `site` tinyint(3) NOT NULL COMMENT '站点id',
  `catid` smallint(5) unsigned DEFAULT '0',
  `name` char(30) NOT NULL DEFAULT '',
  `maxnum` smallint(5) NOT NULL DEFAULT '20',
  PRIMARY KEY (`posid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}position_data`;
CREATE TABLE `{pre}position_data` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) NOT NULL,
  `posid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `contentid` mediumint(8) NULL,
  `thumb` varchar(100) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL,
  `isblank` int(1) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `tstyle` varchar(1024) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(200) NOT NULL,
  `listorder` mediumint(8) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `posid` (`posid`),
  KEY `listorder` (`listorder`),
  KEY `catid` (`catid`),
  KEY `contentid` (`contentid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}navbar`;
CREATE TABLE IF NOT EXISTS `{pre}navbar` (
  `navid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `site` tinyint(3) NOT NULL COMMENT '站点id',
  `name` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`navid`)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}navbar_data`;
CREATE TABLE IF NOT EXISTS `{pre}navbar_data` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `site` tinyint(3) NOT NULL COMMENT '站点id',
  `navid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `parentid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `arrparentid` varchar(255) NOT NULL,
  `child` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否存在子栏目，1，存在',
  `arrchildid` varchar(255) NOT NULL,
  `thumb` varchar(100) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL,
  `isblank` int(1) NOT NULL,
  `ismenu` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否为菜单',
  `title` varchar(200) DEFAULT NULL,
  `tstyle` varchar(1024) NOT NULL COMMENT '标题样式',
  `description` text NOT NULL,
  `setting` text NOT NULL,
  `url` varchar(200) NOT NULL,
  `listorder` mediumint(8) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `navid` (`navid`),
  KEY `ismenu` (`ismenu`),
  KEY `parentid` (`parentid`),
  KEY `site` (`site`),
  KEY `listorder` (`listorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}relatedlink`;
CREATE TABLE `{pre}relatedlink` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `sort` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}role`;
CREATE TABLE `{pre}role` (
  `roleid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `rolename` varchar(50) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`roleid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `{pre}role` VALUES('1','超级管理员','超级管理员');
INSERT INTO `{pre}role` VALUES('2','总编','总编');
INSERT INTO `{pre}role` VALUES('3','编辑','编辑');

DROP TABLE IF EXISTS `{pre}user`;
CREATE TABLE `{pre}user` (
  `userid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `site` tinyint(3) DEFAULT NULL COMMENT '站点id',
  `username` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL,
  `salt` CHAR(10) NOT NULL,
  `roleid` int(3) NOT NULL,
  `lastloginip` varchar(15) DEFAULT NULL,
  `lastlogintime` bigint(10) unsigned DEFAULT '0',
  `loginip` varchar(15) DEFAULT NULL, 
  `logintime` bigint(10) DEFAULT NULL, 
  `email` varchar(40) DEFAULT NULL,
  `realname` varchar(50) DEFAULT NULL DEFAULT '',
  `usermenu` text DEFAULT NULL,
  PRIMARY KEY (`userid`),
  KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{pre}user` (`username`, `password`, `roleid`, `salt`, `realname`) VALUES ('{username}', '{password}', 1, '{salt}', '网站创始人');

DROP TABLE IF EXISTS `{pre}content_1_fang`;
CREATE TABLE `{pre}content_1_fang` (
  `id` int(10) NOT NULL,
  `catid` smallint(5) NOT NULL,
  `content` mediumtext NOT NULL,
  `quyu` int(5) DEFAULT NULL,
  `shi` tinyint(2) DEFAULT NULL,
  `ting` tinyint(2) DEFAULT NULL,
  `wei` tinyint(2) DEFAULT NULL,
  `zhuangxiu` varchar(20) DEFAULT NULL,
  `louceng` tinyint(2) DEFAULT NULL,
  `zongceng` tinyint(2) DEFAULT NULL,
  `zujin` int(5) DEFAULT NULL,
  `zujinleixing` varchar(30) DEFAULT NULL,
  `mianji` int(20) DEFAULT NULL,
  `xiaoqu` varchar(50) DEFAULT NULL,
  `peizhi` text,
  `tupian` text,
  `dizhi` varchar(200) DEFAULT NULL,
  `dianhua` varchar(40) DEFAULT NULL,
  `ditu` varchar(100) DEFAULT NULL,
  PRIMARY KEY `id` (`id`),
  KEY `catid` (`catid`),
  KEY `quyu` (`quyu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `{pre}content_1_article`;
CREATE TABLE `{pre}content_1_image` (
  `id` int(10) NOT NULL,
  `catid` smallint(5) NOT NULL,
  `content` mediumtext NOT NULL,
  `images` text,
  PRIMARY KEY `id` (`id`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}content_1_article`;
CREATE TABLE `{pre}content_1_news` (
  `id` int(10) NOT NULL,
  `catid` smallint(5) NOT NULL,
  `content` mediumtext NOT NULL,
  PRIMARY KEY `id` (`id`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}content_1_item`;
CREATE TABLE IF NOT EXISTS `{pre}content_1_item` (
  `id` int(10) NOT NULL,
  `catid` smallint(5) NOT NULL,
  `content` mediumtext NOT NULL,
  `jiage` decimal(10,0) DEFAULT NULL,
  `shuliang` mediumint(8) DEFAULT NULL,
  `chushou` mediumint(8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{pre}content_1_down`;
CREATE TABLE IF NOT EXISTS `{pre}content_1_down` (
  `id` int(10) NOT NULL,
  `catid` smallint(5) NOT NULL,
  `content` mediumtext NOT NULL,
  `version` char(20) DEFAULT NULL,
  `language` char(20) DEFAULT NULL,
  `os` text,
  `developers` char(20) DEFAULT NULL,
  `softsize` char(20) DEFAULT NULL,
  `downdata` text,
  PRIMARY KEY (`id`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{pre}model` (`modelid`,`site`, `typeid`, `modelname`, `tablename`, `categorytpl`, `listtpl`, `showtpl`) VALUES
(1, 1, 1, '文章', 'content_1_news', 'category_news.html', 'list_news.html', 'show_news.html'),
(2, 1, 1, '图片', 'content_1_image', 'category_image.html', 'list_image.html', 'show_image.html'),
(3, 1, 1, '房产', 'content_1_fang', 'list_fang.html', 'list_fang.html', 'show_fang.html'),
(4, 1, 1, '下载', 'content_1_down', 'list_down.html', 'list_down.html', 'show_down.html'),
(5, 1, 1, '商品', 'content_1_item', 'list_item.html', 'list_item.html', 'show_item.html');

INSERT INTO `{pre}model_field` (`fieldid`, `modelid`, `field`, `name`, `type`, `length`, `indexkey`, `isshow`, `tips`, `pattern`, `errortips`, `formtype`, `setting`, `listorder`, `disabled`) VALUES
(1, 1, 'content', '内容', '', '0', '', 1, '', '', '', 'editor', 'array (\n  ''width'' => ''80'',\n  ''height'' => ''500'',\n  ''type'' => ''1'',\n)', 0, 0),
(2, 2, 'content', '内容', '', '0', '', 1, '', '', '', 'editor', 'array (\n  ''width'' => ''80'',\n  ''height'' => ''300'',\n  ''type'' => ''0'',\n)', 0, 0),
(3, 2, 'images', '上传图片', 'TEXT', '0', '', 1, '', '', '', 'files', 'array (\n  ''type'' => ''jpg,jpeg,png,gif'',\n  ''size'' => ''2'',\n)', 0, 0),
(4, 3, 'content', '内容', '', '0', '', 1, '', '', '', 'editor', 'array (\n  ''width'' => ''90'',\n  ''height'' => ''200'',\n  ''type'' => ''0'',\n)', 99, 0),
(6, 3, 'quyu', '区域', 'INT', '5', 'INDEX', 1, '', '', '', 'linkage', 'array (\n  ''id'' => ''1'',\n  ''level'' => ''2'',\n)', 1, 0),
(7, 3, 'shi', '室', 'TINYINT', '2', '', 1, '', '', '', 'input', 'array (\n  ''size'' => ''50'',\n)', 0, 0),
(8, 3, 'ting', '厅', 'TINYINT', '2', '', 1, '', '', '', 'input', 'array (\n  ''size'' => ''50'',\n)', 0, 0),
(9, 3, 'wei', '卫', 'TINYINT', '2', '', 1, '', '', '', 'input', 'array (\n  ''size'' => ''50'',\n)', 0, 0),
(10, 3, 'zhuangxiu', '装修', 'VARCHAR', '20', '', 1, '', '', '', 'select', 'array (\n  ''content'' => ''毛坯\r\n简单\r\n精装\r\n豪华'',\n)', 0, 0),
(11, 3, 'louceng', '楼层', 'TINYINT', '2', '', 1, '', '', '', 'input', 'array (\n  ''size'' => ''50'',\n)', 0, 0),
(12, 3, 'zongceng', '总层', 'TINYINT', '2', '', 1, '', '', '', 'input', 'array (\n  ''size'' => ''50'',\n)', 0, 0),
(13, 3, 'zujin', '租金', 'INT', '5', '', 1, '', '', '', 'input', 'array (\n  ''size'' => ''120'',\n)', 0, 0),
(14, 3, 'zujinleixing', '租金类型', 'VARCHAR', '30', '', 1, '', '', '', 'select', 'array (\n  ''content'' => ''面议\r\n押一付三\r\n押一付一\r\n付半年\r\n付一年'',\n)', 0, 0),
(15, 3, 'mianji', '面积', 'INT', '20', '', 1, '平方', '', '', 'input', 'array (\n  ''size'' => ''130'',\n)', 6, 0),
(17, 3, 'xiaoqu', '小区', 'VARCHAR', '50', '', 1, '', '', '', 'input', 'array (\n  ''size'' => ''250'',\n)', 2, 0),
(18, 3, 'fangwuhuxing', '房屋户型', '', '0', '', 1, '', '', '', 'merge', 'array (\n  ''content'' => ''{shi}室 {ting}厅 {wei}卫 {zhuangxiu}'',\n)', 3, 0),
(19, 3, 'loucengleixing', '楼层类型', '', '0', '', 1, '', '', '', 'merge', 'array (\n  ''content'' => ''{louceng}楼，共{zongceng}楼'',\n)', 4, 0),
(20, 3, 'zujingzuhe', '租金', '', '0', '', 1, '', '', '', 'merge', 'array (\n  ''content'' => ''{zujin}元/月，{zujinleixing}'',\n)', 5, 0),
(21, 3, 'peizhi', '房屋配置', '', '0', '', 1, '', '', '', 'checkbox', 'array (\n  ''content'' => ''床\r\n热水器\r\n洗衣机\r\n空调\r\n冰箱\r\n电视\r\n宽带\r\n沙发\r\n衣柜'',\n)', 7, 0),
(22, 3, 'tupian', '房屋图片', '', '0', '', 1, '', '', '', 'files', 'array (\n  ''type'' => ''jpg,jpeg,png,gif'',\n  ''size'' => ''2'',\n)', 8, 0),
(25, 3, 'dizhi', '地址', 'VARCHAR', '200', '', 1, '', '', '', 'input', 'array (\n  ''size'' => ''300'',\n)', 8, 0),
(26, 3, 'dianhua', '联系电话', 'VARCHAR', '40', '', 1, '', '', '', 'input', 'array (\n  ''size'' => '''',\n)', 9, 0),
(27, 3, 'ditu', '地图', 'VARCHAR', '100', '', 1, '', '', '', 'map', 'array (\n  ''apikey'' => ''D8DA516A60D11BE12A649224CD1DEB373AEAB063'',\n  ''city'' => ''成都'',\n)', 0, 0),
(28, 4, 'content', '软件介绍', '', '', '', 1, '', '', '', 'editor', 'array (\n  ''width'' => ''90'',\n  ''height'' => ''200'',\n  ''type'' => ''1'',\n)', 99, 0),
(29, 5, 'content', '商品描述', '', '', '', 1, '', '', '', 'editor', 'array (\n  ''width'' => ''90'',\n  ''height'' => ''300'',\n  ''type'' => ''1'',\n)', 99, 0),
(30, 4, 'version', '软件版本', 'CHAR', '20', '', 1, '', '', '', 'input', 'array (\n  ''size'' => '''',\n)', 0, 0),
(31, 4, 'language', '软件语言', 'CHAR', '20', '', 1, '', '', '', 'input', 'array (\n  ''size'' => '''',\n)', 0, 0),
(32, 4, 'os', '操作系统', '', '', '', 1, '', '', '', 'checkbox', 'array (\n  ''content'' => ''winxp\r\nwin2003\r\nwin7\r\nwin8\r\nwin9\r\nlinux'',\n)', 0, 0),
(33, 4, 'developers', '软件作者', 'CHAR', '20', '', 1, '', '', '', 'input', 'array (\n  ''size'' => '''',\n)', 0, 0),
(34, 4, 'softsize', '软件大小', 'CHAR', '20', '', 1, '', '', '', 'input', 'array (\n  ''size'' => '''',\n)', 0, 0),
(35, 4, 'downdata', '下载地址', '', '', '', 1, '', '', '', 'files', 'array (\n  ''type'' => ''zip,rar'',\n  ''size'' => ''20'',\n)', 0, 0),
(36, 5, 'jiage', '商品价格', 'DECIMAL', '10,2', '', 1, '', '', '', 'input', 'array (\n  ''size'' => '''',\n)', 0, 0),
(37, 5, 'shuliang', '商品数量', 'MEDIUMINT', '8', '', 1, '', '', '', 'input', 'array (\n  ''size'' => '''',\n)', 0, 0),
(38, 5, 'chushou', '已经出售', 'MEDIUMINT', '8', '', 0, '', '', '', 'input', 'array (\n  ''size'' => '''',\n)', 0, 0);

INSERT INTO `{pre}model_field` VALUES(39, 6, 'xingming', '姓名', 'VARCHAR', '255', '', 1, '', 0, '', '', 'input', 'a:2:{s:4:"size";s:3:"150";s:7:"default";s:0:"";}', 0, 0);

DROP TABLE IF EXISTS `{pre}member_geren`;
CREATE TABLE IF NOT EXISTS `{pre}member_geren` (
  `id` mediumint(8) NOT NULL,
  `xingming` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{pre}model` VALUES(6, 1, 2, '个人会员', 'member_geren', 'category_geren.html', 'list_geren.html', 'show_geren.html', NULL, '');
