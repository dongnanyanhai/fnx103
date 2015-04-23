﻿<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="<?php echo $admin_path?>images/reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $admin_path?>images/system.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $admin_path?>images/dialog.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $admin_path?>images/main.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $admin_path?>images/switchbox.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $admin_path?>images/table_form.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $admin_path?>js/jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $admin_path?>js/dialog.js"></script>
<script charset="utf-8" src="<?php echo $extension_path?>kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="<?php echo $extension_path?>kindeditor/lang/zh_CN.js"></script>
<title>后台管理</title>
</head>
<body style="font-weight: normal;">
<div class="subnav">
<div class="content-menu ib-a blue line-x">
<a href='<?php echo url("link/admin")?>'><em>友情链接列表</em></a><span>|</span>
<a href='<?php echo url("link/admin/add")?>' class="on"><em>添加友情链接</em></a>
</div>
<div class="bk10"></div>
<div class="table-list">
<form action="" method="post">
<input name="data[id]" type="hidden" id="id" value="<?php echo $data[id]?>">
<table width="100%" class="table_form ">
 <tbody>
    <tr>
        <th width="200">链接类型：</th>
        <td><input name="data[typeid]" type="radio" value="0" <?php echo $data[typeid]==0 ? "checked" : ''?> onClick="$('#logos').hide();"> 文字链接&nbsp;&nbsp;
        <input name="data[typeid]" type="radio" value="1" <?php echo $data[typeid]==1 ? "checked" : ''?> onClick="$('#logos').show();"> 图片链接
        </td>
      </tr>
     <tr>
	<tr>
        <th>网站名称：</th>
        <td><input type="text" class="input-text" style="width:300px;" id="name" value="<?php echo $data[name]?>" name="data[name]">
        </td>
      </tr>
     <tr>
        <th>链接地址：</th>
        <td><input type="text" class="input-text" style="width:300px;" id="url" value="<?php echo $data[url]?>" name="data[url]"></td>
      </tr>
    <tr style="<?php echo $data[typeid]==1 ? "display: table-row;" : 'display:none'?>" id="logos">
        <th>LOGO：</th>
        <td><input type="text" class="input-text" style="width:300px;" value="<?php echo $data[logo]?>" name="data[logo]" id="logo">
        <a href="javascript:;" onClick="preview('logo')">预览图片</a>
        <div id="urlTip" class="onShow">接输入图片完整的URL地址</div>
        </td>
      </tr>
	<tr>
        <th>简单描述：</th>
        <td><textarea name="data[introduce]" style="width:295px;height:80px;"><?php echo $data[introduce]?></textarea></td>
      </tr>
	<tr>
        <th></th>
        <td><input type="submit" class="button" value="提交" name="submit"></td>
      </tr>
	<tr>
</tbody>
</table>
</form>
</div>
</div>
<script language="javascript">
function preview(obj) {
	var filepath = $('#'+obj).val();
	if (filepath) {
		var content = '<img src="'+filepath+'" />';
	} else {
		var content = '图片地址为空';
	}
	window.top.art.dialog({title:'预览',fixed:true, content: content});
}
</script>
</body>
</html>