<!--фильтр-->
<form action="" style="margin:0">
<p>
	<label for="active">Активность</label>
	<select name="active" id="active">
		{foreach from=$main.active item=item}
		<option value="{$item.id}" {$item.selected}>{$item.name}</option>
		{/foreach}
	</select>

	<input type="submit" value=">">
</p>
</form>
<!--/фильтр-->

{$main.footer}

<form action="/admin/articles/group" method="POST">
<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
        <td width="1%">№</td>
        <td width="1%" nowrap>{sort from="main" sort="self_id" name="ID"}</td>
        <td nowrap>{sort from="main" sort="self_name" name="Заголовок"}</td>
        <td nowrap>Превью</td>
        {if 'articles_publication'|acl_is_allowed}<td width="1%" title="Активность">Акт.</td>{/if}
        {if 'articles_del'|acl_is_allowed}<td width="1%" title="Удалить">Уд.</td>{/if}
	    {if 'articles_edit'|acl_is_allowed}<td width="1%">&nbsp;</td>{/if}
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	    {counter start=0 skip=1 print=false}
	    {foreach from=$main.rows name=rows item=item}
	    <tr class="row_p">
			<td>{counter}.</td>
	        <td>{$item.id}</td>
			<td>{$item.name}</td>
			<td>{$item.preview|truncate:100:"..."}</td>
			{if 'articles_publication'|acl_is_allowed}
			<td align="center">
				<input type="checkbox" name="act[]" value="{$item.id}" {if $item.active}checked{/if}>
				<input type="hidden" name="ids[]" value="{$item.id}">
			</td>
			{/if}
			{if 'articles_del'|acl_is_allowed}<td align="center"><a href='/admin/articles/delete?id={$item.id}' class='confirm'><img src='/i/admin/delete.png' alt='Delete' title='Delete'></a></td>{/if}
			{if 'articles_edit'|acl_is_allowed}<td><a href="/admin/articles/edit?id={$item.id}">Ред.</a></td>{/if}
	    </tr>
	    {/foreach}
	</tbody>
</table>

{if 'articles_edit'|acl_is_allowed}<p><input type="submit" value="Сохранить" onclick="if(!window.confirm('Информация будет изменена.\nПродолжить?')) return false; else return true;"></p>{/if}
</form>