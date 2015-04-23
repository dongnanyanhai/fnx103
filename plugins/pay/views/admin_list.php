<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>admin</title>
<link href="<?php echo ADMIN_THEME?>images/reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo ADMIN_THEME?>images/system.css" rel="stylesheet" type="text/css" />
<link href="<?php echo ADMIN_THEME?>images/main.css" rel="stylesheet" type="text/css" />
<link href="<?php echo ADMIN_THEME?>images/table_form.css" rel="stylesheet" type="text/css" />
<link href="<?php echo ADMIN_THEME?>images/dialog.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="<?php echo ADMIN_THEME?>js/jquery.min.js"></script>
<script language="javascript" src="<?php echo ADMIN_THEME?>js/dialog.js"></script>
</head>
<body>
<div class="subnav">
	<div class="content-menu ib-a blue line-x">
	<?php
	$_GET['a'] = isset($_GET['a']) ? $_GET['a'] : 'index';
	foreach ($menu as $i=>$t) {
	    $class = $_GET['a'] == $t[0] ? ' class="on"' : '';
		$span  = $i >= count($menu)-1  ? '' : '<span>|</span>';
	    echo '<a href="' . purl('admin/' . $t[0]) . '" ' . $class . '><em>' . $t[1] . '</em></a>' . $span;
	}
	?>
	</div>
<div class="bk10"></div>
<div class="table-list">
<form method="post" action="" name="searchform">
<div class="explain-col search-form">
状态 
<select name="status">
<option value="-1">--</option>
<option value="0">未付</option>
<option value="1">成功</option>
</select>
&nbsp;&nbsp;
支付单号  <input type="text" name="order_sn" size=30 class="input-text" value=""> 
用户名  <input type="text" name="username" class="input-text" value="">  
<input type="submit" name="submit" class="button" value="搜索">
</div>
<input type="hidden" name="form" value="search">
</form>
<form action="" method="post">
<input type="hidden" name="form" value="delete" />
<table width="100%">
	<thead>
	<tr>
		<th width="22" align="right"><input name="deletec" id="deletec" type="checkbox" onClick="setC()" /></th>
		<th width="130" align="left">支付订单</th>
		<th width="80" align="left">用户名</th>
		<th width="70" align="left">充值金额</th>
		<th width="100" align="left">ip地址</th>
		<th width="120" align="left">下单时间</th>
		<th width="50" align="left">状态</th>
		<th width="60" align="left">付款方式</th>
		<th width="120" align="left">付款时间</th>
		<th width="" align="left">&nbsp;</th>
	</tr>
    </thead>
    <tbody>
    <?php foreach ($list as $t) { ?>
	<tr height="25">
	  <td align="right"><input name="dels[]" value="<?php echo $t['id']?>" type="checkbox" class="deletec" /></td>
	  <td align="left"><?php echo $t['order_sn']?></td>
	  <td align="left"><?php echo $t['username'] ?></td>
	  <td align="left"><?php echo $t['money']?></td>
	  <td align="left"><?php echo $t['ip']?></td>
	  <td align="left"><?php echo date('Y-m-d H:i:s', $t['addtime'])?></td>
	  <td align="left"><?php echo $t['status'] ? '成功' : '<font color=red>未付款</font>';?></td>
	  <td align="left"><?php if ($t['paytype']) { echo $pay_config[$t['paytype']]['name'];}else{ echo '管理员充值';}?></td>
	  <td align="left"><?php if ($t['paytime']) echo date('Y-m-d H:i:s', $t['paytime'])?></td>
	  <td align="left"><?php echo $t['adminnote']?></td>
	  </tr>
    <?php } ?>
	  </tbody>
</table><div class="bk10"></div>
<input type="submit" class="button" value="删  除" name="submit">&nbsp;
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
</script>
</body>
</html>
