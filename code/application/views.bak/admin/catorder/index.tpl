{$main.footer}

<form action="/admin/catorder/group" method="POST">
<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
        <td width="1%">№</td>
        <td width="1%" nowrap>{sort from="main" sort="id" name="ID"}</td>
        <td nowrap>{sort from="main" sort="name" name="Название"}</td>
        {if 'feedback_edit'|acl_is_allowed}<td width="1%" title="Активность">Акт.</td>{/if}
        {if 'feedback_del'|acl_is_allowed}<td width="1%" title="Удалить">Уд.</td>{/if}
	    {if 'feedback_edit'|acl_is_allowed}<td width="1%">&nbsp;</td>{/if}
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	    {counter start=0 skip=1 print=false}
	    {foreach from=$main.rows name=rows item=item}
	    <tr class="row_p">
			<td>{counter}.</td>
	        <td>{$item.id}</td>
			<td>{$item.name}</td>
			{if 'feedback_edit'|acl_is_allowed}
			<td align="center">
				<input type="checkbox" name="act[]" value="{$item.id}" {if $item.active}checked{/if}>
				<input type="hidden" name="ids[]" value="{$item.id}">
			</td>
			{/if}
			{if 'feedback_del'|acl_is_allowed}<td align="center"><a href='/admin/catorder/delete?id={$item.id}' class='confirm'><img src='/i/admin/delete.png' alt='Delete' title='Delete'></a></td>{/if}
			{if 'feedback_edit'|acl_is_allowed}<td><a href="/admin/catorder/edit?id={$item.id}">Ред.</a></td>{/if}
	    </tr>
	    {/foreach}
	</tbody>
</table>

{if 'feedback_edit'|acl_is_allowed}<p><input type="submit" value="Сохранить" onclick="if(!window.confirm('Информация будет изменена.\nПродолжить?')) return false; else return true;"></p>{/if}
</form>