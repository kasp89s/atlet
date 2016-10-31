{getconfig path="core.url_suffix" assign="url_suffix"}
	<div style="padding-left:40px;padding-top:40px;padding-right:40px;padding-bottom: 20px;">
		{foreach name="frchArticles" from=$data.rows item=item}
			<font style="font-size:14px;">{$item.date_publication|date_format:"%d.%m.%Y"}</font>
			<br />
			<a href="{$uri_base}/{$item.uri}{$url_suffix}" class="ctext">
				{$item.name}
			</a>
			<br />
			<font style="font-size:16px;">{$item.preview}</font>
			<br />
			<a href="{$uri_base}/{$item.uri}{$url_suffix}" class="ctext">Читать дальше &raquo;</a>
			<br /><br /><br />
		{/foreach}
	</div>
{$data.footer}
