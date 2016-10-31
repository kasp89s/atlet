{$main.footer}
<form action="" method="POST">
    <table cellspacing="0" cellpadding="0">
        <tr>
            <td><input type="text" name="article" placeholder="артикул" style="width: 200px" /></td>
            <td><input type="text" name="name" placeholder="название" style="width: 360px" /></td>
            <td><input type="submit" value="Найти"></td>
        </tr>
    </table>
</form>
<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
            <td width="1%">№</td>
            <td>артикул</td>
            <td>наименование</td>
            <td>фасовка</td>
            <td>вкус</td>
            <td>количество</td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">

    {foreach from=$products item=item}
        <tr>
            <td></td>
            <td>{$item.article}</td>
            <td>{$item.name}</td>
            <td>{$item.volume}</td>
            <td>{$item.taste}</td>
            <td>{$item.count2}</td>
        </tr>
    {/foreach}
	</tbody>
</table>
