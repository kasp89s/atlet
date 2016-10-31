{$main.footer}
{if $group_id > 0}
<form action="/admin/catalog/group?group_id={$group_id}" method="POST">
{/if}
<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
        <td width="1%">№</td>
        <td width="1%" nowrap>{sort from="main" sort="self_id" name="ID"}</td>
        <td width="50px">Изобр.</td>
        <td nowrap>{sort from="main" sort="self_name" name="Название"}</td>
        <td nowrap>{sort from="main" sort="self_code" name="Артикул"}</td>
        {if 'catalog_edit'|acl_is_allowed}<td width="1%" title="Активность">Акт.</td>{/if}
        {if 'catalog_del'|acl_is_allowed}<td width="1%" title="Удалить">Уд.</td>{/if}
	    {if 'catalog_edit'|acl_is_allowed}<td width="1%">&nbsp;</td>{/if}
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	    {counter start=0 skip=1 print=false}
	    {foreach from=$main.rows name=rows item=item}
	    <tr class="row_p">
			<td>{counter}.</td>
	        <td>{$item.id}</td>
	        <td style="text-align:center;"><img src="{$item.files._rows.image.preview_3}" border="0"></td>
			<td>{$item.name|truncate:100:"..."}</td>
			<td>{$item.code|truncate:100:"..."}</td>
			{if 'catalog_edit'|acl_is_allowed}
			<td align="center">
				<input type="checkbox" name="act[]" value="{$item.id}" {if $item.active}checked{/if}>
				<input type="hidden" name="ids[]" value="{$item.id}">
			</td>
			{/if}
			{if 'catalog_del'|acl_is_allowed}<td align="center"><a href='/admin/catalog/delete?group_id={$item.group_id}&id={$item.id}' class='confirm'><img src='/i/admin/delete.png' alt='Delete' title='Delete'></a></td>{/if}
			{if 'catalog_edit'|acl_is_allowed}<td><a href="/admin/catalog/edit?group_id={$item.group_id}&id={$item.id}">Ред.</a></td>{/if}
	    </tr>
	    {/foreach}
	</tbody>
</table>
{if $group_id > 0}
<p>
	{if 'catalog_edit'|acl_is_allowed}<input type="submit" value="Сохранить" onclick="if(!window.confirm('Информация будет изменена.\nПродолжить?')) return false; else return true;">{/if}
	{if 'catalog_add'|acl_is_allowed}<input type="button" value="Добавить товар" onclick="window.location='/admin/catalog/edit?group_id={$group_id}'">{/if}
</p>
</form>
{/if}