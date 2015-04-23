<?php
if (!defined('IN_FINECMS')) exit('No permission resources');

return array(
   "CREATE TABLE IF NOT EXISTS `{prefix}link` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `typeid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `logo` varchar(255) NOT NULL DEFAULT '',
  `introduce` text NOT NULL,
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `typeid` (`typeid`,`listorder`,`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;",
);