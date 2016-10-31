{getconfig path="core.url_suffix" assign="url_suffix"}

{if count($data.delayed_rows) > 0}

	<form action="{$uri_base}/group" method="post" id="productsForm">
	    {if count($data.delayed_rows) >0}
		<ul class="goods-in-basket">
            {assign var="totalSum" value="0"}
	    	{assign var="totalQuantity" value="0"}
			{foreach from=$data.delayed_rows item=item}
			<li>
				<table>
					<tr>
						<td class="goods-in-basket-preview">
							<div>
								<a href="{$catalog_uri_base}{$groups[$item.product_group_id].uri_base}/{$item.product_uri}{$url_suffix}" title="">
									<img src="{$productsFiles[$item.product_id]._rows.image.preview_3}" alt="" />
								</a>
							</div>
						</td>
						<td class="goods-in-basket-name">
							<a href="{$catalog_uri_base}{$groups[$item.product_group_id].uri_base}/{$item.product_uri}{$url_suffix}" title="">{$item.name|truncate:60:"..."}</a>
						</td>
						<td class="goods-in-basket-price">
							Цена {$item.subtotal|show_number} р.
						</td>
						<td class="goods-in-basket-remove">
							<a href="{$uri_base}/item_delete?id={$item.cart_item_id}"><i class="remove"></i></a>
						</td>
					</tr>
				</table>
			</li>
			{assign var="totalSum" value=`$totalSum+$item.subtotal`}
	        {assign var="totalQuantity" value=`$totalQuantity+$item.quantity`}
			{/foreach}
		</ul>
		{/if}
	</form>
	<div class="clear"></div>
{else}
	{$page.description}
{/if}