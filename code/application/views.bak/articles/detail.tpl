{getconfig path="core.url_suffix" assign="url_suffix"}
{if $data.id}
	<div style="padding-left:40px;padding-top:40px;padding-right:40px;padding-bottom: 20px;">
		<font style="font-size: 14px;">{$data.date_publication|date_format:"%d.%m.%Y"}</font>
		<br />
		<font class="z">{$data.name}</font>
		<br /><br />
		<font style="color:black;">
			{$data.description}
		</font>
	</div>
	<p><a href="{$uri_base}{$url_suffix}" class="ctext" title="Все статьи">Все статьи</a></p>
{else}
	<br />
    <h3 class="centered">Запрошенная Вами статья не найдена.</h3>
    <a href="{$uri_base}{$url_suffix}" class="ctext" title="Все статьи">Все статьи</a>
{/if}

