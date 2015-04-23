<?php
if (!defined('IN_FINECMS')) exit('No permission resources');

return array(
    'name'			=> '文章心情',
    'typeid'		=> 1,
    'author'		=> 'finecms',
    'version'		=> '1.3',
    'fields'		=> array(),
    'description'	=> "必须有Jquery支持，将下面代码放在内容页面中<br>
	\{if plugin(\'mood\')\}\&lt;script type=\"text/javascript\" src=\"{url(\'mood/index/show\', array(\'id\'=>\$id))}\"&gt;\&lt;\/script&gt;\{/if\}"
);