{$main.footer}
<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
        <td width="1%" nowrap>{sort from="main" sort="self_id" name="ID"}</td>
        <td nowrap>{sort from="main" sort="self_title" name="Название"}</td>
        <td nowrap>{sort from="main" sort="self_total_shows" name="Всего просм."}</td>
        <td nowrap>{sort from="main" sort="self_relative_shows" name="Показ. в См.Также"}</td>
        <td nowrap>{sort from="main" sort="self_relative_weight" name="Вес"}</td>
        <td nowrap>{sort from="main" sort="self_is_show_in_relative" name="См.Также?"}</td>
	    {if 'catalog_edit'|acl_is_allowed}<td width="1%">&nbsp;</td>{/if}
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	    {foreach from=$main.rows name=rows item=item}
	    <tr class="row_p">
	        <td>{$item.id}</td>
			<td>{$item.title|truncate:100:"..."}</td>

			<td>{$item.total_shows}</td>
			<td>{$item.relative_shows}</td>

			<td>{$item.relative_weight}</td>
			<td>{if $item.is_show_in_relative}ДА{else}нет{/if}</td>

			{if 'catalog_edit'|acl_is_allowed}<td><a target="_blank" href="/admin/cataloggroups/edit?id={$item.id}">Ред.</a></td>{/if}
	    </tr>
	    {/foreach}
	</tbody>
</table>