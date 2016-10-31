{if $main.total_pages > 1}
<div class="pager">
	<ul>
	{foreach from=$main.pages name=pages item=item}
		{if $item === false}<li class="no-bg color1"><b>...</b></li>
		{elseif $item == $main.page}<li class="no-bg color1"><b>{math equation="item + 1" item=$item}</b></li>
		{else}<li><a href="{$main.page_qs}{$item}" title="">{math equation="item + 1" item=$item}</a></li>
		{/if}
	{/foreach}
	</ul>
	<b>Страницы:</b>
</div>
{else}
&nbsp;
{/if}