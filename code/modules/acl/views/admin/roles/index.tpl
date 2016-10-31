{$main.footer}

<form action="/admin/roles/group" method="POST">
<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
        <td width="1%">№</td>
        <td width="1%" nowrap>{sort from="main" sort="id" name="ID"}</td>
        <td nowrap>{sort from="main" sort="name" name="Название"}</td>
        <td nowrap>Описание</td>
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
			<td>{$item.name}</td>	
			<td>{$item.description|truncate:100:"..."}</td>
			<td align="center"><a href='/admin/roles/delete?id={$item.id}' class='confirm'><img src='/i/admin/delete.png' alt='Delete' title='Delete'></a></td>
			<td><a href="/admin/roles/edit?id={$item.id}">Ред.</a></td>
	    </tr>
	    {/foreach} 
	</tbody>
</table>

{literal}
<p>
	<input type="submit" value="Сохранить" onclick="if(!window.confirm('Информация будет изменена.\nПродолжить?')) { return false; } else { return true; }">
</p>
{/literal}