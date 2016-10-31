{getconfig path="core.url_suffix" assign="url_suffix"}
{if count($data.rows) > 0}

<section class="page-basket">
	<br />
	<form id="orderForm" action="" method="post">
		<table>
			{assign var="totalSum" value="0"}
	    	{assign var="totalQuantity" value="0"}
	    	{assign var="warehouse" value="2"}
			{foreach from=$data.rows item=item}
                {if $item.arrOptions.warehouse == 1}
                    {assign var="warehouse" value="1"}
                {/if}
			<tr>
				<td><a href="{$uri_base}/item_delete?id={$item.cart_item_id}" ><div class="off"></div></a></td>
				<td><figure><img src="{$productsFiles[$item.product_id]._rows.image.preview_3}" alt="" /></figure></td>
				<td>{$item.name|truncate:60:"..."}</td>
				<td>{if $item.arrOptions.taste != ""}Вкус: <span id="taste-{$item.cart_item_id}">{$item.arrOptions.taste}</span>{/if}</td>
				<td>
					<input class="itemQuantity" itemId="{$item.cart_item_id}" type="text" value="{$item.quantity}" />
					<input class="basePrice" type="hidden" value="{$item.price}">
                    <input type="hidden" id="productId-{$item.cart_item_id}" value="{$item.product_id}">
				</td>
				<td>{$item.price} р.</td>
				<td><span id="totalPrice{$item.cart_item_id}">{$item.subtotal}</span> р.</td>
			</tr>
			{assign var="totalSum" value=`$totalSum+$item.subtotal`}
	        {assign var="totalQuantity" value=`$totalQuantity+$item.quantity`}
			{/foreach}
		</table>
		<div class="itog clearfix">
			<p>Заказано <span id="prodCount">{$totalQuantity}</span> товара(ов) на сумму: </p>
			<span class="price"><span id="subtotal">{$totalSum}</span> р.</span>
		</div>
		{*<div class="itog clearfix" style="display: none;">*}
			{*<p>Доставка: </p>*}
			{*<span class="price"><span id="deliveryCost">{$delivery.cost|round:0}</span> р.</span>*}
		{*</div>*}
		<div class="order-product">
			<p class="orange">ОФОРМЛЕНИЕ  ЗАКАЗА</p>
			<p class="clearfix">
				<span class="l-ord">Имя*:</span>
				<span class="r-ord"><input name="author" type="text" value="{$order_data.author}"/></span>
				{if $order_data.err_author_required}<font style="color: red">Поле обязательно для заполнения</font>{/if}
			</p>
			<p class="clearfix" id="address">
				<span class="l-ord">Адрес*: </span>
				<span class="r-ord"><input name="delivery_address" type="text" value="{$order_data.delivery_address}"/></span>
				{if $order_data.err_delivery_address_required}<font style="color: red">Поле обязательно для заполнения</font>{/if}
			</p>
			<p class="clearfix">
				<span class="l-ord">Телефон*: </span>
				<span class="r-ord"><input name="phone" type="tel" value="{$order_data.phone}"/></span>
				{if $order_data.err_phone_required}<font style="color: red">Поле обязательно для заполнения</font>{/if}
			</p>
			<p class="clearfix">
				<span class="l-ord">E-mail:</span>
				<span class="r-ord"><input name="email" type="email" value="{$order_data.email}"/></span>
				{if $order_data.err_email_email}<font style="color: red">Неверный формат Email</font>{/if}
			</p>
			<p class="clearfix">
				<span class="r-ord">Укажите свою почту если хотите следить за состоянием заказа.</span>
			</p>
			<div class="shipping clearfix">
				<span class="l-ord">Способ доставки:</span>
				{if $order_data.err_delivery_required}<font style="color: red">Выберите способ доставки</font>{/if}
				<div class="r-ord">
					<p><input type="radio" checked="checked" id="kurer" name="delivery" class="niceRadio" value="2" /> <label for="kurer" onclick="$('#address').show()">Доставка курьером по Москве (<span id="deliveryCost">{$delivery.cost|round:0}</span> руб.)</label></p>
					{*<p><input type="radio" name="delivery" id="post" class="niceRadio" value="1"/><label for="post">Доставка по России (До 7 дней, от 250 руб.)</label></p>*}
					<p><input type="radio" name="delivery" id="post" class="niceRadio" value="3" /><label for="post" onclick="$('#address').hide()">Самовывоз (100 руб.)</label></p>
                    <p style="margin-left: 24px;padding: 0px;font-size: 12px;" id="delivery-date">
                        {if $warehouse == 2}
                            {if $hour < 18}
                                ({$delivery[0]})
                            {else}
                                ({$delivery[1]})
                            {/if}
                        {else}
                            {if $hour < 18}
                                ({$delivery[2]})
                            {else}
                                ({$delivery[3]})
                            {/if}
                        {/if}
                    </p>
                </div>
			</div>
			<div style="margin-top:-15px;" class="payment clearfix">
				<span class="l-ord">Способ оплаты:</span>
				{if $order_data.err_payment_required}<font style="color: red">Выберите способ оплаты</font>{/if}
				<div class="r-ord">
					<!-- 
<p>
						<img src="/images/visa2.png" alt="" />
						<img src="/images/m-card.png" alt="" />
						<img src="/images/maestro.png" alt="" />
						<img src="/images/sb-bank.png" alt="" />
						<img src="/images/yd.png" alt="" />
						<img src="/images/qiwi.png" alt="" />
					</p>
 -->
					<p><input type="radio" checked="checked" id="nal-kur" name="payment" class="niceRadio" value="1"/> <label for="nal-kur">Оплата наличными (Москва)</label></p>
					<!-- p><input type="radio" id="nal-post" name="payment" class="niceRadio" value="2"/> <label for="nal-post">Наличными при получении (При заказе по России)</label></p -->
				</div>
			</div>
			<div class="comment clearfix">
				<span class="l-ord">Комментарий:</span>
				<div class="r-ord">
					<textarea name="description"></textarea>
					<p><input type="submit" value="Оформить заказ" class="btn" /></p>
					<input name="order_cart" type="hidden" value="yes">
					<input name="processOrder" type="hidden" value="yes">
				</div>
			</div>
			<div class="es-sendmail">
				<p class="orange" style="color:white">мы свяжемся с вами в ближайшее время !</p>
			</div>
		</div>
	</form>
</section>
<div class="clear"></div>
{else}
	{$page.description}
{/if}
{literal}
    <script type="text/javascript">
        $(".niceRadio").live('click',
                function() {
                    var val = $(this).children().val();
                    if (val == 3) {
                        $('#address').hide();
                    } else {
                        $('#address').show();
                    }
                }
        );
    </script>
{/literal}
