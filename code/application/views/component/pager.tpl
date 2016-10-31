{getconfig path="core.url_suffix" assign="url_suffix"}
{if $data.total_pages > 1}
<div class="bot-nav">
	<ul>
		{foreach from=$data.pages_array name=pages item=item}
			{if $item === false}...
			{elseif $item == $data.current_page}<li class="active"><a href="#">{math equation="item + 1" item=$item}</a></li>
			{else}<li><a href="{$uri_base}/{if $data.current_uri}{$data.current_uri}/{/if}page{$item+1}{$url_suffix}{$data.query_string}">{math equation="item + 1" item=$item}</a></li>
			{/if}
		{/foreach}
	</ul>
</div>
{/if}