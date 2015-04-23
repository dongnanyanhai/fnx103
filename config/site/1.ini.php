<?php
if (!defined('IN_FINECMS')) exit();

/**
 * FineCMS配置
 */
return array(

	'SITE_EXTEND_ID'          => '',  //所继承的网站id
	'SITE_LANGUAGE'           => 'zh-cn',  //系统语言设置，默认zh-cn
	'SITE_TIMEZONE'           => '8',  //时区常量，默认时区为东8区时区
	'SITE_THEME'              => 'default',  //模板风格,默认default
	'SITE_NAME'               => 'FineCMS',  //网站名称，将显示在浏览器窗口标题等位置
	'SITE_TITLE'              => 'FineCMS内容管理系统',  //网站首页SEO标题
	'SITE_KEYWORDS'           => '',  //网站SEO关键字
	'SITE_DESCRIPTION'        => '',  //网站SEO描述信息
	'SITE_WATERMARK'          => '0',  //水印功能
	'SITE_WATERMARK_ALPHA'    => '55',  //图片水印透明度
	'SITE_WATERMARK_TEXT'     => 'FineCMS',  //文字水印
	'SITE_WATERMARK_SIZE'     => '14',  //单位像素，默认14
	'SITE_WATERMARK_IMAGE'    => 'watermark.png',  //Png格式图片，水印图片目录：/extensions/watermark/
	'SITE_WATERMARK_POS'      => '',  //水印位置
	'SITE_THUMB_TYPE'         => '0',  //图片显示模式
	'SITE_THUMB_WIDTH'        => '200',  //内容缩略图默认宽度
	'SITE_THUMB_HEIGHT'       => '200',  //内容缩略图默认高度
	'SITE_TIME_FORMAT'        => 'Y-m-d H:i',  //网站时间显示格式，参数与PHP的date函数一致，默认Y-m-d H:i:s
	'SITE_MOBILE'             => false,  //移动设备访问网站开关，打开之后需要设计移动端模板（默认mobile或者mobile_站点id）

);