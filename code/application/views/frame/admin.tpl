<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Админ-панель</title>
	<link rel="stylesheet" type="text/css" href="/css/admin/ui/jquery-ui.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/admin/layout.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/admin/ui.tabs.css" media="screen" />
	<script type="text/javascript" src="/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="/js/jquery/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/js/jquery/ui/ui.tree.js"></script>
	<script type="text/javascript" src="/js/admin/stuff.js"></script>
	{foreach from=$head item=item}{$item}{/foreach}
</head>

<body>
	<div id="dialog" style="display: none;">Вы уверены?</div>

	<div id="header">
		Админ-панель
		<div class="info">
			Добрый день, {acl_get_username} ! | <a href="/admin/auth/logout">Выйти</a>
		</div>
	</div>


	<div id="navigation">
		<ul>
		{foreach from=$topmenu item=item}
			<li><a href="/admin/{$item.link}">{$item.name}</a></li>
			{if $item.link == 'votings'}</ul></div><div id="navigation"><ul>{/if}
		{/foreach}
		</ul>
	</div>


	<div id="main">
		<div id="title">
			<h2>{$section_title} {if $section_subtitle}| {$section_subtitle}{/if}</h2>
		</div>

		<div id="left">
			<div id="sidebar">
				{if $menu}
				<h3>Задачи</h3>
				<p>
					{foreach from=$menu item=item}
						<a href="{$item.url}">{$item.section}</a><br />
					{/foreach}
				</p>
				{/if}
			</div>
		</div>


		{message name="info" assign="info_message"}
		{message name="error" assign="error_message"}
		<div id="content">
			{if $info_message}
			<div id="info_message">
				<p>{$info_message}</p>
			</div>
			{/if}
			{if $error_message}
			<div id="error_message">
				<p>{$error_message}</p>
			</div>
			{/if}

			{$content}
		</div>
	</div>
</body>
</html>