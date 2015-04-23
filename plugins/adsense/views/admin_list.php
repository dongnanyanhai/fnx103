<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="<?php echo ADMIN_THEME?>images/reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo ADMIN_THEME?>images/system.css" rel="stylesheet" type="text/css" />
<link href="<?php echo ADMIN_THEME?>images/dialog.css" rel="stylesheet" type="text/css" />
<link href="<?php echo ADMIN_THEME?>images/main.css" rel="stylesheet" type="text/css" />
<link href="<?php echo ADMIN_THEME?>images/switchbox.css" rel="stylesheet" type="text/css" />
<link href="<?php echo ADMIN_THEME?>images/table_form.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="<?php echo ADMIN_THEME?>js/jquery.min.js"></script>
<script language="javascript" src="<?php echo ADMIN_THEME?>js/dialog.js"></script>
<title>后台管理</title>
</head>
<body style="font-weight: normal;">
<div class="subnav">

<div class="content-menu ib-a blue line-x">
<a href='<?php echo purl("admin")?>' class="on"><em>广告位管理</em></a><span>|</span>
<a href='<?php echo purl("admin/add")?>'><em>添加广告位</em></a><span>|</span>
<a href='<?php echo purl("admin/cache")?>'><em>更新广告缓存</em></a>
</div>
<div class="table-list">
<form action="" method="post">
<table width="100%">
	<thead>
	<tr>
	  <th width="50" align="right">选择&nbsp;<input name="deletec" id="deletec" type="checkbox" onClick="setC()">&nbsp;</th>
		<th width="150" align="left">广告位名称</th>
		<th width="68" align="left">宽x高</th>
		<th width="88" align="left">显示类型</th>
		<th align="left">管理操作</th>
	</tr>
    </thead>
    <tbody>
    <?php foreach ($list as $t) { 
	?>
	<tr height="25">
	  <td align="right"><input name="data[]" type="checkbox" class="deletec" value="<?php echo $t['id'];?>">&nbsp;</td>
	  <td align="left"><?php echo $t['adname'];?></td>
	  <td align="left"><?php echo $t['width'];?>x<?php echo $t['height'];?></td>
	  <td align="left"><?php echo $t['showtype'] ? '顺序显示' : '随机显示';?></td>
	  <td align="left">
      <a href="<?php echo purl('admin/alist/',array('aid'=>$t[id]));?>">广告管理</a> | 
      <a href="<?php echo purl('admin/edit/',array('id'=>$t[id]));?>">修改</a> | 
      <a href="javascript:;" onClick="if(confirm('确定删除吗？')){ window.location.href='<?php echo purl('admin/del/',array('id'=>$t['id']));?>'; }">删除</a> |
      <a href="javascript:;" onClick="getViewData(<?php echo $t['id']?>);">调用代码</a>
      </td>
	  </tr>
      <?php } ?>
	<tr height="25">
	  <td colspan="9" align="left">
      <input type="submit" class="button" value="删  除" name="submit">
      </td>
	  </tr>
	<tr>
	  <td>      
	  </tbody>
</table>
<?php echo $pagelist['html']?>

</form>
</div>
</div>
<script language="javascript">
function setC() {
	if($("#deletec").attr('checked')) {
		$(".deletec").attr("checked",true);
	} else {
		$(".deletec").attr("checked",false);
	}
}
function getViewData(id) {
	var url = '<?php echo purl("admin/ajaxview/", array("id"=>""))?>'+id;
	window.top.art.dialog(
	    {id:"ajaxview",iframe:url, title:'模板数据调用', width:'380', height:'140', lock:true,
		button: [
            {
				name: '复制',
				callback: function () {
					 var d = window.top.art.dialog({id:"ajaxview"}).data.iframe;
			         var c = d.document.getElementById('ads_'+id).value;
					 copyToClipboard(c);
					 return false;
				},
				focus: true
            }, {
                name: '关闭'
            }
        ]
		
		}
	);
}
function copyToClipboard(meintext) {
    if (window.clipboardData){
        window.clipboardData.setData("Text", meintext);
    } else if (window.netscape){
        try {
            netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
        } catch (e) {
            alert("被浏览器拒绝！\n请在浏览器地址栏输入'about:config'并回车\n然后将 'signed.applets.codebase_principal_support'设置为'true'"); 
		} 
        var clip = Components.classes['@mozilla.org/widget/clipboard;1'].
        createInstance(Components.interfaces.nsIClipboard);
        if (!clip) return;
        var trans = Components.classes['@mozilla.org/widget/transferable;1'].
        createInstance(Components.interfaces.nsITransferable);
        if (!trans) return;
        trans.addDataFlavor('text/unicode');
        var len = new Object();
        var str = Components.classes["@mozilla.org/supports-string;1"].
        createInstance(Components.interfaces.nsISupportsString);
        var copytext=meintext;
        str.data=copytext;
        trans.setTransferData("text/unicode",str,copytext.length*2);
        var clipid=Components.interfaces.nsIClipboard;
        if (!clip) return false;
        clip.setData(trans,null,clipid.kGlobalClipboard);
    }
    alert("复制成功，您可以粘贴到您模板中了");
    return false;
}
</script>
</body>
</html>