{$main.footer}

<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
        <td width="1%">№</td>
        <td width="1%" nowrap>{sort from="main" sort="self_id" name="ID"}</td>
        <td nowrap>{sort from="main" sort="self_username" name="Логин"}</td>
        <td nowrap>{sort from="main" sort="self_fio" name="ФИО"}</td>
        <td width="1%" title="Удалить">Уд.</td>
	    <td width="1%">&nbsp;</td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	    {counter start=0 skip=1 print=false}
	    {foreach from=$main.rows name=rows item=item}
	    <tr class="row_p">
			<td>{counter}.</td>
	        <td>{$item.id}</td>
			<td>{$item.username}</td>
			<td>{$item.fio|truncate:100:"..."}</td>
			<td align="center"><a href='/admin/users/delete?id={$item.id}' class='confirm'><img src='/i/admin/delete.png' alt='Delete' title='Delete'></a></td>
			<td><a href="/admin/users/edit?id={$item.id}">Ред.</a></td>
	    </tr>
	    {/foreach}
	</tbody>
</table>