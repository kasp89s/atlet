<h3>Оформление заказа</h3>
{$page.description}
<font color='#666666' size='2' face='Tahoma, Verdana'>
	<form onsubmit='return validate()' action='' method='post'>
		<table style="font-size:13px;" cellpadding=4 cellspacing=0>

		    <tr class="{$data.err_code}">
				<td><b>Артикул:</b><font color='Red'><sup><b>*</b></sup></font></td>
				<td><input type='code' name='code' size='40' value="{$data.code}"></td>
			</tr>
			{if $data.err_code_required}<tr><td colspan=2><span class="errorMedium">Обязательное поле не задано</span></td></tr>{/if}
			{if $data.err_code_length}<tr><td colspan=2><span class="errorMedium">Превышена максимальная длина поля</span></td></tr>{/if}

			<tr class="{$data.err_quantity}">
				<td><b>Количество:</b><font color='Red'><sup><b>*</b></sup></font></td>
				<td><input type='quantity' name='quantity' size='40' value="{if $data.quantity > 0}{$data.quantity}{/if}"></td>
			</tr>
			{if $data.err_quantity_required}<tr><td colspan=2><span class="errorMedium">Обязательное поле не задано</span></td></tr>{/if}
			{if $data.err_quantity_value}<tr><td colspan=2><span class="errorMedium">Некорректное значение поля</span></td></tr>{/if}

			<tr class="{$data.err_author}">
				<td>
					<b>Контактное лицо:</b><font color='Red'><sup><b>*</b></sup></font>
				</td>
				<td>
					<input type='text' name='author' size='40' value="{$data.author}">
				</td>
			</tr>
			{if $data.err_author_required}<tr><td colspan=2><span class="errorMedium">Обязательное поле не задано</span></td></tr>{/if}

			<tr class="{$data.err_phone}">
				<td><b>Телефон:</b><font color='Red'><sup><b>*</b></sup></font></td>
				<td><input type='text' name='phone' size='40' value="{$data.phone}"></td>
			</tr>
			{if $data.err_phone_required}<tr><td colspan=2><span class="errorMedium">Обязательное поле не задано</span></td></tr>{/if}
			{if $data.err_phone_length}<tr><td colspan=2><span class="errorMedium">Превышена максимальная длина поля</span></td></tr>{/if}

			<tr class="{$data.err_email}">
				<td><b>E-mail:</b><sup><b>*</b></sup></td>
				<td><input type='text' name='email' size='40' value="{$data.email}"></td>
			</tr>
			{if $data.err_email_email}<tr><td colspan=2><span class="errorMedium">Поле не соответствует формату e-mail someone@supermail.ru</span></td></tr>{/if}
			{if $data.err_email_required}<tr><td colspan=2><span class="errorMedium">Обязательное поле не задано</span></td></tr>{/if}

			<tr>
				<td valign='top'><b>Доп. информация:</b></td>
				<td><TEXTAREA ROWS=10 COLS=40 name='description'>{$data.description}</TEXTAREA></td>
			</tr>

			<tr class="{$data.err_captcha}">
				<td valign='top'><b>Контрольное слово:</b><font color='Red'><sup><b>*</b></sup></font></td>
				<td>
					<input name="captcha" type="text" id="captcha" value="">
					<img src="/captcha/lux" align="top">
				</td>
			</tr>
			{if $data.err_captcha}<tr><td><span class="errorMedium">Не верно введен защитный код</span></td></tr>{/if}
		</table><br>
		<center>
			<input type='submit' name='submit' value='Отправить'>
		</center>
		<input type='hidden' name='act' value='send_form'>
	</form>
</font>