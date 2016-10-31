{getconfig path="core.url_suffix" assign="url_suffix"}
{if $data.total_pages > 1}
<font class="ctext">
	Страницы: &lt; &nbsp;
	{foreach from=$data.pages_array name=pages item=item}
		{if $item === false}...
		{elseif $item == $data.current_page}<font class="apage">{math equation="item + 1" item=$item}</font>
		{else}<a href="{$uri_base}/{if $data.current_uri}{$data.current_uri}/{/if}page{$item+1}{$url_suffix}{$data.query_string}" class="page">{math equation="item + 1" item=$item}</a>
		{/if}
		&nbsp;
	{/foreach}
	&nbsp;  &gt;
</font>
{/if}