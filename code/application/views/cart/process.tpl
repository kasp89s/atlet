{getconfig path="core.url_suffix" assign="url_suffix"}
<div class="order-summary">
	<ul class="selected-goods">
		{assign var="totalSum" value="0"}
    	{assign var="totalQuantity" value="0"}
		{foreach from=$data.rows item=item}
		<li>
			<div>
				<a href="{$catalog_uri_base}{$groups[$item.product_group_id].uri_base}/{$item.product_uri}{$url_suffix}" title="">
					<img src="{$productsFiles[$item.product_id]._rows.image.preview_3}" alt="" />
				</a>
			</div>
		</li>
		{assign var="totalSum" value=`$totalSum+$item.subtotal`}
        {assign var="totalQuantity" value=`$totalQuantity+$item.quantity`}
		{/foreach}
	</ul>
	<div class="order-total">
		<table>
			<tr>
				<td>Сумма:</td>
				<td>{$totalSum+$delivery.cost|show_number} р.</td>
			</tr>
			<tr>
				<td>Доставка:</td>
				<td>{$delivery.name}</td>
			</tr>
		</table>
	</div>
</div>
<div class="order-address">
	<form method="post" id="orderForm">
		<div class="form-line{if $order_data.err_author_required} error{/if}">
			<label><span>Фамилия Имя Отчество</span></label>
			<input type="text" name="author" value="{$order_data.author}"/>
		</div>
		<div class="form-line{if $order_data.err_email_required or $order_data.err_email_email} error{/if}">
			<label><span>E-mail</span></label>
			<input type="text" name="email" value="{$order_data.email}"/>
		</div>
		<div class="form-line{if $order_data.err_phone_required} error{/if}">
			<label><span>Телефон</span><i>С вами свяжется наш менеджер для подтверждения заказа.</i></label>
			<input type="text" name="phone" value="{$order_data.phone}"/>
		</div>

		<div class="form-line{if $order_data.err_delivery_zip_required} error{/if}">
			<label><span>Индекс получателя</span></label>
			<input type="text" name="delivery_zip" value="{$order_data.delivery_zip}"/>
		</div>

		<div class="form-line{if $order_data.err_delivery_region_required} error{/if}">
			<label><span>Область</span></label>
			<input type="text" name="delivery_region" value="{$order_data.delivery_region}"/>
		</div>

		<div class="form-line{if $order_data.err_delivery_city_required} error{/if}">
			<label><span>Город</span></label>
			<input type="text" name="delivery_city" value="{$order_data.delivery_city}"/>
		</div>

		<div class="form-line{if $order_data.err_delivery_street_required} error{/if}">
			<label><span>Улица</span></label>
			<input type="text" name="delivery_street" value="{$order_data.delivery_street}"/>
		</div>

		<div class="form-line{if $order_data.err_delivery_house_required} error{/if}">
			<label><span>Дом</span></label>
			<input type="text" name="delivery_house" value="{$order_data.delivery_house}"/>
		</div>

		<div class="form-line{if $order_data.err_delivery_building_required} error{/if}">
			<label><span>Строение</span></label>
			<input type="text" name="delivery_building" value="{$order_data.delivery_building}"/>
		</div>

		<div class="form-line{if $order_data.err_delivery_corps_required} error{/if}">
			<label><span>Корпус</span></label>
			<input type="text" name="delivery_corps" value="{$order_data.delivery_corps}"/>
		</div>

		<div class="form-line{if $order_data.err_delivery_flat_required} error{/if}">
			<label><span>Квартира</span></label>
			<input type="text" name="delivery_flat" value="{$order_data.delivery_flat}"/>
		</div>

		<input name="payment" type="hidden" value="{$payment.id}">
		<input name="delivery" type="hidden" value="{$delivery.id}">
		<input name="order_cart" type="hidden" value="yes">
		<input name="processOrder" type="hidden" value="yes">

		<div class="form-line{if $order_data.err_description_required} error{/if}">
			<label><span>Комментарий</span></label>
			<textarea name="description">{$order_data.description}</textarea>
		</div>
		<div class="form-line">
			<a href="javascript:void" id="orderGo" title="" class="btn"><span><span>Отправить<i></i></span></span></a>
		</div>
	</form>
</div>
<script type="text/javascript">{literal}

	    $(function(){
	    	$("#orderGo").click(function(){
	    		$('#orderForm').submit();
	    		return false;
	    	});
	    });
	    {/literal}
	</script>
