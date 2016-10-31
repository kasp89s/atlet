<!--фильтр-->
<form action="" style="margin:0">
<p>
	<label for="cat">Статус записи</label>
	<select name="cat" id="cat">
		<option value="0" {$main.cat_sel_0}>Все</option>
			{foreach from=$main.cat item=item}
			<option value="{$item.id}" {$item.selected}>{$item.name}</option>
			{/foreach}
	</select>

	<input type="submit" value=">">
</p>
</form>
<!--/фильтр-->

{$main.footer}

<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
        <td width="1%">№</td>
        <td width="1%" nowrap>{sort from="main" sort="self_id" name="ID"}</td>
        <td nowrap>{sort from="main" sort="self_author" name="Автор"}</td>
        <td nowrap>{sort from="main" sort="cat_name" name="Статус"}</td>
	    {if 'callback_edit'|acl_is_allowed}<td width="1%">&nbsp;</td>{/if}
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	    {counter start=0 skip=1 print=false}
	    {foreach from=$main.rows name=rows item=item}
	    <tr class="row_p">
			<td>{counter}.</td>
	        <td>{$item.id}</td>
			<td>{$item.author|truncate:100:"..."}</td>
			<td>{$item.cat_name}</td>
			{if 'callback_edit'|acl_is_allowed}<td><a href="/admin/callback/edit?id={$item.id}">Ред.</a></td>{/if}
	    </tr>
	    {/foreach}
	</tbody>
</table>