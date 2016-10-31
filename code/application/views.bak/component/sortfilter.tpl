{getconfig path="core.url_suffix" assign="url_suffix"}
<form method="get" action="{$data.current_url}{$url_suffix}">
	<font class="ctext">Сортировать:&nbsp;&nbsp;&nbsp;</font>
	<select name="order" onchange="this.form.submit();">
		{foreach from=$items item=item}
		<option  value="{$item.value}" {if $item.value == $data.order_val}selected{/if}>{$item.text}</option>
		{/foreach}
	</select>
</form>