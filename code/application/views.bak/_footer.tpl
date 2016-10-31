<table width="100%" class="none">
<tr>
    <td style="text-align:left;" width="40%" nowrap>
		Всего страниц: {$main.total_pages} &nbsp;&nbsp;
		{foreach from=$main.pages name=pages item=item}
			{if $item === false}..
			{elseif $item == $main.page}<b>{math equation="item + 1" item=$item}</b>
			{else}<a href="{$main.page_qs}{$item}">{math equation="item + 1" item=$item}</a>
			{/if}
		{/foreach}
    </td>
	<td style="text-align:center;" width="20%" nowrap>Всего: {$main.row_count}</td>
    <td style="text-align:right;" width="40%" nowrap>Показывать
    	{foreach from=$main.page_sizes name=page_sizes item=item}
	    	{if !$smarty.foreach.page_sizes.first}/{/if}
	    	{if $item.page_size == $main.page_size}<b>{$item.title}</b>
	    	{else}<a href="{$item.qs}">{$item.title}</a>
	    	{/if}
	    {/foreach}
		на страницу
    </td>
</tr>
</table>
