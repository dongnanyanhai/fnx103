<?php
//FineCMS常用代码：
	
	/* 常用常量
		1、{SITE_PATH}：不带域名的网站地址
		2、{SITE_URL}：带域名的网站地址
		3、{CMS_NAME}：软件名称
		4、{CMS_VERSION}：软件版本
		5、{CMS_UPDATE}：更新时间
		6、{SITE_THEME}：前台风格路径
		7、{ADMIN_THEME}：后台风格路径
		8、{EXT_PATH}：扩展目录路径
		9、{LANG_PATH}：语言目录路径
		10、{$数组键名}：网站配置信息（config.ini.php），如{$SITE_NAME}
		11、数组{$cats}：所有栏目数组（{$cats[栏目ID]}）
		12、{$meta_title}：网页title标题
		13、{$meta_keywords}：网页关键词
		14、{$meta_description}：网页描述信息
		15、{$indexc}：是否是首页index
		16、{$param[参数名称]}：用于接收URL参数
		17、{$memberinfo}：当前登陆会员的信息，数组格式
		18、{$membermodel}：会员模型数组
		19、{$membergroup}：会员组数组
		20、{$sites}：多站点数组
		21、{$siteid}：当前站点的id
	*/

	// 文本框数据调用
		{block(1)}
	// 网站标题，关键字，描述代码：
		<title>{$meta_title}</title>
		<meta name="keywords" content="{$meta_keywords}" />
		<meta name="description" content="{$meta_description}" />

	// 模板文件目录：
	{SITE_THEME}

	// 调用公共头、脚文件：
	{template header}

	{template navbar}

	{template footer}

	// list中获取日期：
	{date("Y-m-d", $t['updatetime'])}

	// list中获取阅读数：
	<script type="text/javascript" src="{url('api/hits',array('id'=>$t['id'], 'hits'=>$t['hits']))}"></script>

	// 内容页中获取日期：
	{date("Y-m-d", $updatetime)}

	// 内容页获取阅读数：
	<span id="hits"><script type="text/javascript" src="{url('api/hits',array('id'=>$id, 'hits'=>$hits))}"></script>

	// 推荐位list中获取阅读数：
	{list action=position id=1}
		<script type="text/javascript" src="{url('api/gethits',array('id'=>$t['contentid'], 'hits'=>$t['hits']))}"></script>
	{/list}


	// 内容页上下篇文章使用示例
	<div class="articlebook">
	{if $prev_page}<h2><strong>上一篇：</strong><a href="{$prev_page['url']}">{$prev_page['title']}</a> </h2>{/if}
	{if $next_page}<h2><strong>下一篇：</strong><a href="{$next_page['url']}">{$next_page['title']}</a> </h2>{/if}
	</div>

	// list中使用more=1，必须用下面函数转义html标签：
	{htmlspecialchars_decode($t['content'])}

	// 友情插件使用：
	{if plugin('link')} 
		{list table=link.link order=listorder_asc}
		<a href="{$t['url']}" target="_blank">{$t['name']}</a> 
		{/list} 
	{/if} 

	// 栏目跳转
	<!DOCTYPE html>
	<html>
		<head>
		    <meta http-equiv="refresh" content="0;url=index.php?c=content&a=list&catid=8" />
		    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		</head>
		<body>
		</body>
	</html>

	// $pagelist返回字符串对应css类
	// 在css中设定对应css属性即可
	.pagelist // 外层div
	.pagelist-total // 总数
	.pagelist-nowpage //当前页码
	.pagelist-page // 其他页面
	.pagelist-pre // 第一页，上一页
	.pagelist-next // 最后页，下一页
	.pagelist-note // 数量统计

	// 新增Block 链接类型调用方式

	// 单个图片
	{php $anchor = block(7);}
    <a href="{$anchor['url']}" {if $anchor['isblank']}target="_blank"{/if}>
        <img src="{$anchor['thumb']}">
    </a>

    // 两个图片
	{php $anchor = block(7);}
	<a href="{$anchor['url']}" {if $anchor['isblank']}target="_blank"{/if}>
	{if $anchor['file']}
		<img class="imghover" src="{$anchor['thumb']}" data-thumb="{$anchor['thumb']}" data-thumb2="{$anchor['file']}">
	{else}
		<img src="{$anchor['thumb']}">
	{/if}
	</a>

	$("img.imghover").hover(function(){
		$(this).attr("src",$(this).attr("data-thumb2"));
		},function(){
		$(this).attr("src",$(this).attr("data-thumb"));
	});

	// 调用导航栏
	{php $menu = navbar(1);}
	<ul>
		{loop $menu $m}
		{if $m['parentid'] ==0 && $m['ismenu']}
		<li>
			<a href="{$m['url']}" {if $m['isblank']}target="_blank"{/if}>
				{$m['title']}
				<img src="{$m['thumb']}" />
			</a>
		</li>
		{/if}
		{/loop}
	</ul>


	// 表单模型可以在后台自定义消息模版
	// 自定义消息模版可以得到$msg,$url,$state,$data等数据
	// 前端可以通过提交name="successmsg"或name="failuremsg"的input，来设定消息模版里的$msg值。
	// $url的值由get参数backurl决定，例如&backurl=http://www.baidu.com 
	// $state的值为0时，表示提交失败，为1时，表示提交成功
	// $data为访客提交的表单数据，其中的$data['id']为表单数据在数据库中的id
	
	// wxjs()函数可以获得接口信息
	{php
	    $signPackage = wxjs();
	}
	<script>
	    /*
	     * 注意：
	     * 1. 所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
	     * 2. 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
	     * 3. 常见问题及完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
	     *
	     * 开发中遇到问题详见文档“附录5-常见错误及解决办法”解决，如仍未能解决可通过以下渠道反馈：
	     * 邮箱地址：weixin-open@qq.com
	     * 邮件主题：【微信JS-SDK反馈】具体问题
	     * 邮件内容说明：用简明的语言描述问题所在，并交代清楚遇到该问题的场景，可附上截屏图片，微信团队会尽快处理你的反馈。
	     */
	    wx.config({
	        debug: true,
	        appId: '{$signPackage["appId"]}',
	        timestamp: {$signPackage["timestamp"]},
	        nonceStr: '{$signPackage["nonceStr"]}',
	        signature: '{$signPackage["signature"]}',
	        jsApiList: [
	            // 所有要调用的 API 都要加到这个列表中
	            "onMenuShareTimeline",
	            "onMenuShareAppMessage",
	            "onMenuShareQQ",
	            "onMenuShareWeibo"
	        ]
	    });

	    wx.ready(function () {
	    // 在这里调用 API
	        // 获取“分享到朋友圈”按钮点击状态及自定义分享内容接口
	        wx.onMenuShareTimeline({
	            title: '', // 分享标题
	            link: '', // 分享链接
	            imgUrl: '', // 分享图标
	            success: function () { 
	                // 用户确认分享后执行的回调函数
	            },
	            cancel: function () { 
	                // 用户取消分享后执行的回调函数
	            }
	        });
	</script>

	
	// 栏目自定义字段可通过以下代码进行循环读取：
	{loop $cats[$catid]['ext'] $val}
	    {$cats[$catid][$val]}
	{/loop}