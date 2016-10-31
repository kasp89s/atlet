{getconfig path="core.url_suffix" assign="url_suffix"}

<div style="padding-left:40px;padding-top:10px;padding-right:40px;padding-bottom: 20px;">
	{if count($data.delayed_rows) > 0 or count($data.rows) > 0}
	{$page.description}
    <table>
    	<tr>
    		<td style="width: 200px;">
    			<b>Готовые к заказу ({$data.rows|@count})</b>
    		</td>
    		<td>
    			<a href="{$uri_base}/delayed{$url_suffix}"  style="font-weight: 100;" class="page">Отложенные ({$data.delayed_rows|@count})</a>
    		</td>
    	</tr>
    </table>
    <br /><br />
    {/if}
	<form action="{$uri_base}/group" method="post" id="productsForm">
	    {if count($data.rows) >0}
			<h3>Товары в корзине</h3>
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
	             	  	  Количество
	             	  </th>
	             	  <th>
	                      Цена
	             	  </th>
	             	  <th>
	             	  	  Стоимость
	             	  </th>
	             	  <th>

	             	  </th>
	             </tr>
	    	{assign var="totalSum" value="0"}
	    	{assign var="totalQuantity" value="0"}
			{foreach from=$data.rows item=item}
	             <tr>
	             	  <td>
	                      <input name="marked[{$item.cart_item_id}]" class="marker" type="checkbox" value="1">
	             	  </td>
	             	  <td style="text-align: left;">
	             	  	  <a target="_blank" style="font-weight: 100;" class="page" href="{$catalog_uri_base}{$groups[$item.product_group_id].uri_base}/{$item.product_uri}{$url_suffix}">
                              <img src="{$productsFiles[$item.product_id]._rows.image.preview_3}" height="40px"  border="0">
	             	  	  </a>
	             	  </td>
	             	  <td style="text-align: left;">
	             	  	  <a style="font-weight: 100;" class="page" target="_blank" href="{$catalog_uri_base}{$groups[$item.product_group_id].uri_base}/{$item.product_uri}{$url_suffix}">{$item.name|truncate:30:"..."}</a>
	             	  </td>
	             	  <td>
	             	  	  <input class="quantityinpt" name="quantity[{$item.cart_item_id}]" type="text" value="{$item.quantity}">
	             	  	  <input class="priceval" type="hidden" value="{$item.price}">
	             	  </td>
	             	  <td>
	             	  	  {$item.price|show_number} руб.
	             	  </td>
	             	  <td>
	                      {$item.subtotal|show_number} руб.
	             	  </td>
	             	  <td>
	                      <a style="font-weight: 100;" class="page" href="{$uri_base}/delete?item_id={$item.cart_item_id}"><img src="/i/redcross.png" alt="Удалить" border="0"></a>
	             	  </td>
	             </tr>
	             {assign var="totalSum" value=`$totalSum+$item.subtotal`}
	             {assign var="totalQuantity" value=`$totalQuantity+$item.quantity`}
			{/foreach}
				 <tr>
	             	  <td colspan=3>
	             	  	  Итого, без стоимости доставки:
	             	  </td>
	             	  <td>
	             	  	  {$totalQuantity}
	             	  </td>
	             	  <td>

	             	  </td>
	             	  <td>
	                      {$totalSum|show_number} руб.
	                      {if $data.couponSumm > 0 && $totalSum >= 20000}
	                      	<br /><font style="color:red">-{$data.couponSumm|show_number} руб.</font>
	                      	<hr>
	                      	{$totalSum-$data.couponSumm|show_number} руб.
	                      {/if}
	             	  </td>
	             	  <td>

	             	  </td>
                      <td>

	             	  </td>
	             </tr>
			</table>
			<table id="funcButtonsTable" cellpadding=0 cellspacing=0>
				<tr>
					<td>
						<input type="submit" name="delay" id="delaySubmit" value="Отложить">
						<input type="submit" name="delete" id="deleteSubmit" value="Удалить">
					<td>
				</tr>
			</table>
	        <br /><br />
		{/if}
		{if count($data.delayed_rows) > 0 or count($data.rows) > 0}
			Купон на скидку:
			{if $data.couponSumm <= 0}
				<input class="couponNumber" name="couponNumber" type="text" value="{$data.couponNumber}"><input type="submit" id="recalculate" value="Пересчитать">
				{if $data.couponError}<br /><font style="color: red; font-size: 10px;">Введен неверный код купона. Попробуйте ввести код еще раз.</font>{/if}
			{else}
				№<b>{$data.couponData[0].activation_code}</b> на имя <b>{$data.couponData[0].author}</b>
				<input name="coupon" type="hidden" value="{$data.couponData[0].activation_code}">
				<input type="submit" id="recalculate" value="Пересчитать">
				{if $totalSum < 20000}<br /><font style="color: red; font-size: 10px;">Недостаточная сумма для использования купона на скидку</font>{/if}
			{/if}

		{else}
            <h3 class="centered">Ваша корзина пуста</h3>
		{/if}
		<input name="clientScrollTop" id="clientScrollTop" type="hidden" value="">
	</form>
	{if count($data.rows) > 0}
		<br /><br /><br />
		<h3>Оформление заказа</h3>
		<font color='#666666' size='2' face='Tahoma, Verdana'>
			<form  action='' method='post' enctype="multipart/form-data">
				<table style="font-size:13px;" cellpadding=4 cellspacing=0>

					<tr class="{$order_data.err_payment}">
						<td>
							<b>Тип оплаты:</b>
						</td>
						<td>
							<select name="payment" id="payment" class="{$order_data.err_payment}" style="width:100%;">
								{foreach from=$order_data.payment item=item}
								<option value="{$item.id}" {$item.selected}>{$item.name}</option>
								{/foreach}
							</select>
						</td>
					</tr>
					{if $order_data.err_payment_required}<tr><td colspan=2><span class="errorMedium">Обязательное поле не задано</span></td></tr>{/if}

                    <tr class="{$order_data.err_orgpropsfile} orgProps">
						<td>
							<b>Реквизиты организации:</b>
						</td>
						<td>
							<input name="orgpropsfile" type="file" value="" style="width:100%;">
						</td>
					</tr>
					{if $order_data.err_author_required}<tr><td colspan=2><span class="errorMedium">Обязательное поле не задано</span></td></tr>{/if}

				    <tr class="{$order_data.err_author}">
						<td>
							<b>Контактное лицо:</b>
						</td>
						<td>
							<input type='text' name='author' size='40' value="{$order_data.author}">
						</td>
					</tr>
					{if $order_data.err_author_required}<tr><td colspan=2><span class="errorMedium">Обязательное поле не задано</span></td></tr>{/if}

					<tr class="{$order_data.err_phone}">
						<td><b>Телефон:</b></td>
						<td><input type='text' name='phone' size='40' value="{$order_data.phone}"></td>
					</tr>
					{if $order_data.err_phone_required}<tr><td colspan=2><span class="errorMedium">Обязательное поле не задано</span></td></tr>{/if}
					{if $order_data.err_phone_length}<tr><td colspan=2><span class="errorMedium">Превышена максимальная длина поля</span></td></tr>{/if}

					<tr class="{$order_data.err_add_phone}">
						<td><b>Доп. телефон:</b></td>
						<td><input type='text' name='add_phone' size='40' value="{$order_data.add_phone}"></td>
					</tr>
					{if $order_data.err_add_phone_length}<tr><td colspan=2><span class="errorMedium">Превышена максимальная длина поля</span></td></tr>{/if}

					<tr class="{$order_data.err_email}">
						<td><b>E-mail:</b></td>
						<td><input type='text' name='email' size='40' value="{$order_data.email}"></td>
					</tr>
					{if $order_data.err_email_email}<tr><td colspan=2><span class="errorMedium">Поле не соответствует формату e-mail someone@supermail.ru</span></td></tr>{/if}
					{if $order_data.err_email_required}<tr><td colspan=2><span class="errorMedium">Обязательное поле не задано</span></td></tr>{/if}

					<tr>
						<td valign='top'><b>Доп. информация:</b></td>
						<td><TEXTAREA ROWS=10 COLS=40 name='description'>{$order_data.description}</TEXTAREA></td>
					</tr>

					<tr class="{$order_data.err_delivery_adress}">
						<td valign='top'><b>Адрес доставки:</b></td>
						<td><TEXTAREA ROWS=10 COLS=40 name='delivery_adress'>{$order_data.delivery_adress}</TEXTAREA></td>
					</tr>
					{if $order_data.err_delivery_adress_required}<tr><td colspan=2><span class="errorMedium">Обязательное поле не задано</span></td></tr>{/if}

					<tr class="{$order_data.err_captcha}">
						<td valign='top'><b>Контрольное слово:</b></td>
						<td>
							<input name="captcha" type="text" id="captcha" value="">
							<img src="/captcha/lux" align="top">
						</td>
					</tr>
					{if $order_data.err_captcha}<tr><td><span class="errorMedium">Не верно введен защитный код</span></td></tr>{/if}
				</table><br>
				<input name="order_cart" type="hidden" value="">
				<center>
					<input type='submit' name='submit' value='Заказать'>
				</center>
				<input type='hidden' name='act' value='send_form'>

			</form>
		</font>
	{/if}
	<div class="quantityForm">
	     <span class="pricetext"></span> x <button class="downval">-</button><input class="quantityField" type="text" value=""><button class="upval">+</button> шт. = <span class="totalprice"></span>
	     <input class='priceVal' type="hidden" value="">
	     <br /><br />
	     <button class="formConfirm">Принять</button> <button class="formCancel">Отмена</button>
	</div>
</div>

<script language="JavaScript">
{literal}
	$('#payment').change(function(){		checkPaymentSelect();	});

	function checkPaymentSelect(){		if($('#payment').val() == 'newerhood' {/literal}{foreach from=$order_data.payment item=item}{if $item.org_props_file_need == 1} || $('#payment').val() == {$item.id}{/if}{/foreach}{literal}){
			showOrgPropFile();
		}else{
			hideOrgPropFile();
		}	}

	function showOrgPropFile(){		$('.orgProps').show();	}

	function hideOrgPropFile(){
		$('.orgProps').hide();
	}

	checkPaymentSelect();

	var formVisible = false;
	$('#recalculate').hide();
	$('#funcButtonsTable').hide();

	$(document).mousedown(function(event) {
	    if ($(event.target).closest(".quantityForm").length) return;
	    formCancel();
	    event.stopPropagation();
	});

	$('#clientScrollTop').val($(document).scrollTop());

	$(window).scroll(function () {
        $('#clientScrollTop').val($(document).scrollTop());
    });

	$('.quantityinpt').focus(function(){
		//$('.quantityForm').appendTo($(this).parent());
		$('.quantityForm .quantityField').val($(this).val());
		$('.quantityForm .priceVal').val($(this).parent().children('.priceval').val());

		$('.quantityForm .pricetext').html($(this).parent().children('.priceval').val() + ' руб.');

		recalculate();
		$('.quantityForm .quantityField').css('width', $(this).css('width'));
		$('.quantityForm').show();

		$('.quantityForm').css('top', $(this).offset().top + $('.quantityForm').offset().top - $('.quantityForm .quantityField').offset().top);
		$('.quantityForm').css('left', $(this).offset().left + $('.quantityForm').offset().left - $('.quantityForm .quantityField').offset().left);

		$(this).addClass('currentQuantityField');
		$('.quantityForm .quantityField').focus();

		formVisible = true;
	});

	$('.quantityForm .quantityField').change(function(){		recalculate();	});

	$('.quantityForm .quantityField').keyup(function(){
		recalculate();
	});

	$('.quantityForm .upval').click(function(){
		$('.quantityForm .quantityField').val(parseInt($('.quantityForm .quantityField').val()) + 1);
		recalculate();	});

	$('.quantityForm .downval').click(function(){		$('.quantityForm .quantityField').val(parseInt($('.quantityForm .quantityField').val()) - 1);
		recalculate();
	});

	$('.quantityForm .formConfirm').click(function(){
		recalculate();
		formOk();
	});

	$('.quantityForm .formCancel').click(function(){
		formCancel();
	});

	function formOk(){		$('.currentQuantityField').val($('.quantityForm .quantityField').val());
		$('.currentQuantityField').removeClass('currentQuantityField');
		$('#productsForm').submit();
		$('.quantityForm').hide();
		formVisible = false;
	}

	function formCancel(){
		$('.quantityForm').hide();
		$('.currentQuantityField').removeClass('currentQuantityField');
		formVisible = false;
	}

	function recalculate(){
		if($('.quantityForm .quantityField').val() <= 0){			$('.quantityForm .quantityField').val(1);		}		$('.quantityForm .totalprice').html($('.quantityForm .priceVal').val() * $('.quantityForm .quantityField').val() + ' руб.');
	}

	$('.couponNumber').keyup(function(){        if($('.couponNumber').val().length > 0){			$('#recalculate').show();		}else{			$('#recalculate').hide();		}
	});

	var markedCount = 0;

	$('.marker').click(function(){
		markedCount = 0;		$('.marker').each(function(){            if($(this).attr('checked')){            	markedCount++;            }		});

		if(markedCount > 0){			$('#funcButtonsTable').show();
			$('#funcButtonsTable').css('width', $('.cart_table').offset().width);
		}else{			$('#funcButtonsTable').hide();		}
		$('#delaySubmit').val('Отложить '+markedCount);
		$('#deleteSubmit').val('Удалить '+markedCount);	});

{/literal}
	{if $clientScrollTop > 0}
		$(document).scrollTop({$clientScrollTop});
	{/if}
{literal}
{/literal}
</script>