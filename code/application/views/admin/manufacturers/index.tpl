{$main.footer}

<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
        <td width="1%">№</td>
        <td width="1%" nowrap>{sort from="main" sort="self_id" name="ID"}</td>
        <td nowrap>{sort from="main" sort="self_name" name="Название"}</td>
        {if 'news_del'|acl_is_allowed}<td width="1%" title="Удалить">Уд.</td>{/if}
	    {if 'news_edit'|acl_is_allowed}<td width="1%">&nbsp;</td>{/if}
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	    {counter start=0 skip=1 print=false}
	    {foreach from=$main.rows name=rows item=item}
	    <tr class="row_p">
			<td>{counter}.</td>
	        <td>{$item.id}</td>
			<td>{$item.name}</td>
			{if 'manufacturers_del'|acl_is_allowed}<td align="center"><a href='/admin/manufacturers/delete?id={$item.id}' class='confirm'><img src='/i/admin/delete.png' alt='Delete' title='Delete'></a></td>{/if}
			{if 'manufacturers_edit'|acl_is_allowed}<td><a href="/admin/manufacturers/edit?id={$item.id}">Ред.</a></td>{/if}
	    </tr>
	    {/foreach}
	</tbody>
</table>

{if 'news_edit'|acl_is_allowed}<p><input type="submit" value="Сохранить" onclick="if(!window.confirm('Информация будет изменена.\nПродолжить?')) return false; else return true;"></p>{/if}
