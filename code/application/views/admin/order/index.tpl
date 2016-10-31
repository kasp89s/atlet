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
        <td nowrap>Тип доставки</td>
        <td nowrap>{sort from="main" sort="self_date_create" name="Дата"}</td>
        <td nowrap>Сумма</td>
        <td nowrap>Сумма суточная</td>
	    {if 'order_edit'|acl_is_allowed}<td width="1%">&nbsp;</td>{/if}
		</tr>
	</thead>
	<tbody align="left" valign="middle">
        {assign var="old_date" value=false}
	    {counter start=0 skip=1 print=false}
	    {foreach from=$main.rows name=rows item=item}
	    <tr class="row_p">
			<td>{counter}.</td>
            <td><a href="/admin/order/edit?id={$item.id}">{$item.id}</a></td>
			<td>{$item.author|truncate:100:"..."}</td>
			<td>{$item.cat_name}</td>
			<td>{if $item.delivery == 2}Доставка{/if}{if $item.delivery == 3}Самовывоз{/if}</td>
			<td>{$item.date_create}</td>
			<td>{$item.price}</td>
            {if $old_date != $item.monthPrice.date}
                <td rowspan="{$item.monthPrice.rowspan}" style="border-bottom: 1px solid #D9D9D9;">{$item.monthPrice.price}</td>
            {/if}
            {assign var="old_date" value=$item.monthPrice.date}
			{if 'order_edit'|acl_is_allowed}<td><a href="/admin/order/edit?id={$item.id}">Ред.</a></td>{/if}
	    </tr>
	    {/foreach}
	</tbody>
</table>
