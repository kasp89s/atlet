{getconfig path="core.url_suffix" assign="url_suffix"}

<div style="padding-left:40px;padding-top:40px;padding-right:40px;padding-bottom: 20px;">
	{if count($data.delayed_rows) > 0 or count($data.rows) > 0}
	{$page.description}
    <table>
    	<tr>
    		<td style="width: 200px;">
    			<a style="font-weight: 100;" class="page" href="{$uri_base}{$url_suffix}">Готовые к заказу ({$data.rows|@count})</a>
    		</td>
    		<td>
    			<b>Отложенные ({$data.delayed_rows|@count})</b>
    		</td>
    	</tr>
    </table>
    <br /><br />
    {/if}
	<form action="{$uri_base}/group" method="post">
	    {if count($data.delayed_rows) >0}
			<h3>Отложенные товары</h3>
			<table class="cart_table" cellpadding=0 cellspacing=0>
				 <tr>
				 	  <th>

	             	  </th>
				 	  <th>

	             	  </th>
	             	  <th style="text-align: left;">
	             	  	  Наименование
	             	  </th>
	             	  <th>
	             	  	  Цена
	             	  </th>
	             	  <th>

	             	  </th>
	             </tr>
	    	{assign var="totalSum" value="0"}
	    	{assign var="totalQuantity" value="0"}
			{foreach from=$data.delayed_rows item=item}
	             <tr>
	             	  <td>
	                      <input name="marked[{$item.cart_item_id}]" class="marker" type="checkbox" value="1">
	             	  </td>
	             	  <td style="text-align: left;">
	             	  	  <a style="font-weight: 100;" class="page" target="_blank" href="{$catalog_uri_base}{$groups[$item.product_group_id].uri_base}/{$item.product_uri}{$url_suffix}">
                              <img src="{$productsFiles[$item.product_id]._rows.image.preview_3}" height="40px" border="0">
	             	  	  </a>
	             	  </td>
	             	  <td style="text-align: left;">
	             	  	  <a style="font-weight: 100;" class="page" target="_blank" href="{$catalog_uri_base}{$groups[$item.product_group_id].uri_base}/{$item.product_uri}{$url_suffix}">{$item.name|truncate:30:"..."}</a>
	             	  </td>
	             	  <td>
	             	  	  {$item.price|show_number} руб.
	             	  </td>
	             	  <td>
	                      <a style="font-weight: 100;" class="page" href="{$uri_base}/delete?item_id={$item.cart_item_id}"><img src="/i/redcross.png" alt="Удалить" border="0"></a>
	             	  </td>
	             </tr>
	             {assign var="totalSum" value=`$totalSum+$item.subtotal`}
	             {assign var="totalQuantity" value=`$totalQuantity+$item.quantity`}
			{/foreach}
			</table>
			<table id="funcButtonsTable" cellpadding=0 cellspacing=0>
				<tr>
					<td>
						<input type="submit" name="notdelay" id="notdelaySubmit" value="Добавить в корзину">
						<input type="submit" name="delete" id="deleteSubmit" value="Удалить">
					<td>
				</tr>
			</table>
	        <br /><br />
		{/if}
		{if count($data.delayed_rows) > 0 or count($data.rows) > 0}

		{else}
            <h3 class="centered">Ваша корзина пуста</h3>
		{/if}
	</form>
</div>
<script language="JavaScript">
{literal}
	$('#funcButtonsTable').hide();
    var markedCount = 0;

	$('.marker').click(function(){
		markedCount = 0;
		$('.marker').each(function(){
            if($(this).attr('checked')){
            	markedCount++;
            }
		});

		if(markedCount > 0){
			$('#funcButtonsTable').show();
			$('#funcButtonsTable').css('width', $('.cart_table').offset().width);

		}else{
			$('#funcButtonsTable').hide();
		}
		$('#notdelaySubmit').val('Добавить в корзину '+markedCount);
		$('#deleteSubmit').val('Удалить '+markedCount);
	});{/literal}
</script>