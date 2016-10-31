{getconfig path="core.url_suffix" assign="url_suffix"}

{if count($data.delayed_rows) > 0 or count($data.rows) > 0}

	<a href="" title="" class="add-to-fav"><i></i>Добавить все товары в избранное</a>
	<form action="{$uri_base}/group" method="post" id="productsForm">
	    {if count($data.rows) >0}
		<ul class="goods-in-basket">
            {assign var="totalSum" value="0"}
	    	{assign var="totalQuantity" value="0"}
			{foreach from=$data.rows item=item}
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
							{if isset($data.storeProblems[$item.cart_item_id])}<br/><font style="color:red">Проблемы с доступностью на складе.<br/>Имеется только {$data.storeProblems[$item.cart_item_id]} позиций</font>{/if}
						</td>
						<td class="goods-in-basket-total">
							<div class="counter">
								<i class="count-up"></i>
								<input class="itemQuantity" itemId="{$item.cart_item_id}" type="text" value="{$item.quantity}" />
								<input class="basePrice" type="hidden" value="{$item.price}">
								<i class="count-down"></i>
							</div>
						</td>
						<td class="goods-in-basket-price">
							= <span id="totalPrice{$item.cart_item_id}">{$item.subtotal}</span> р.
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
	<form id="orderForm" action="" method="post">
		<dl class="choosing">
			<dt>Способ доставки</dt>
			<dd>
				<div class="variant">
					<input type="radio" name="delivery" class="niceRadio" deltype="rel" relitem='place' value="relCurPlace" checked="checked"/><label>Доставка курьером</label>
				</div>
				<div class="select-block-content">
					<div class="select">
						<select id="place" name="CurPlace">
							<option value="1" selected="selected">По г.Москва внутри МКАД</option>
							<option value="2">По г.Москва за МКАД</option>
						</select>
					</div>
					<p class="fast">Доставим быстро!</p>
				</div>
			</dd>
			<dd>
				<div class="variant">
					<input type="radio" name="delivery" deltype="id" class="niceRadio" value="3"/><label>Почта России</label>
				</div>
				<div class="select-block-content">
					<p class="subnote">Стоимость уточняйте у менеджера</p>
				</div>
			</dd>
		</dl>

		<dl class="choosing">
			<dt>Способ оплаты</dt>
			<dd>
				<div class="variant">
					<input type="radio" id="payMethod1" name="payment" class="niceRadio" value="1" checked="checked"/><label>Наличными курьеру</label>
				</div>
			</dd>
			<dd>
				<div class="variant">
					<input type="radio" id="payMethod3" name="payment" class="niceRadio" value="3"/><label>Электронные методы оплаты</label>
				</div>
				<div class="select-block-content">
					<ul class="el-money">
						<li><img src="img/visa.jpg" alt="" /></li>
						<li><img src="img/mcard.jpg" alt="" /></li>
						<li><img src="img/wmoney.jpg" alt="" /></li>
						<li><img src="img/quwi-a.jpg" alt="" /></li>
						<li><img src="img/yamoney-a.jpg" alt="" /></li>
						<li><img src="img/mmoney.jpg" alt="" /></li>
					</ul>
				</div>
			</dd>
		</dl>
		<dl class="choosing ttl">
			<dt>К оплате</dt>
			<dd>
				<table>
					<tr>
						<td>Сумма без скидки:</td>
						<td><span id="subtotal">{$totalSum}</span> р.</td>
					</tr>
					<tr>
						<td>Сумма со скидками:</td>
						<td><span id="subtotalDiscount">{$totalSum}</span> р.</td>
					</tr>
					<tr>
						<td>Доставка:</td>
						<td><span id="deliveryCost">{$deliverySum}</span> р.</td>
					</tr>
					<tr>
						<td class="total-price" colspan="2">= <span id="totalSum">{$totalSum}</span> р.</td>
					</tr>
					<tr>
						<td class="order-now" colspan="2">
							<a href="javascript:void" id="orderGo" title="" class="btn"><span><span>Оформить заказ<i></i></span></span></a>
						</td>
					</tr>
				</table>
			</dd>
		</dl>
		<input name="processOrder" type="hidden" value="yes">
	</form>
	<script type="text/javascript">{literal}

	    $(function(){
	    	$("#orderGo").click(function(){
	    		$('#orderForm').submit();
	    		return false;
	    	});
	    });
	    {/literal}
	</script>
	<div class="clear"></div>
{else}
	{$page.description}
{/if}