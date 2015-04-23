<?php

if (!defined('IN_FINECMS')) exit();

/**
 *  custom.php 用户自定义函数/类库
 */

function _formtype() {
    return array(
        'titlestyle' => '标题样式', //前者tstyle表示字段的英文名称，后者表示字段的描述名称
    );
}


function form_titlestyle($setting=null) {
	return '<span>标题样式 暂无相关设定</span>';
}

function content_titlestyle($name, $content=null, $setting=null) {
	$content = is_null($content[0]) ? get_content_value($setting['default']) : $content[0]; //字段内容，固定值
    return '
    <script type="text/javascript" src="views/admin/js/colorpicker.js"></script>
	<input name="data[' . $name . ']" type="hidden" value="' . $content . '" id="tstyle"/>
	<span class="b"><span>' . lang('a-fnx-32') . '</span>&nbsp;
	<input type="checkbox" id="titstyle-b">&nbsp;&nbsp;&nbsp;</span>
	<span class="i"><span>' . lang('a-fnx-33') . '</span>&nbsp;
	<input type="checkbox" id="titstyle-i">&nbsp;&nbsp;&nbsp;</span>
	<span class="u"><span>' . lang('a-fnx-34') . '</span>&nbsp;
	<input type="radio" id="titstyle-u" name="titstyle-us">&nbsp;&nbsp;&nbsp;</span>
	<span class="o"><span>' . lang('a-fnx-35') . '</span>&nbsp;
	<input type="radio" id="titstyle-o" name="titstyle-us">&nbsp;&nbsp;&nbsp;</span>
	<span class="s"><span>' . lang('a-fnx-36') . '</span>&nbsp;
	<input type="radio" id="titstyle-s" name="titstyle-us">&nbsp;&nbsp;&nbsp;</span>
	' . lang('a-fnx-37') . '&nbsp;
	<input type="radio" id="titstyle-n" name="titstyle-us">&nbsp;&nbsp;&nbsp;
	<img src="views/admin/images/colour.png" width="15" height="16" onclick="colorpicker(\'title_colorpanel\',\'set_title_color\');" style="cursor:hand">
	<span id="title_colorpanel" style="position:absolute;" class="colorpanel"></span>';
}

