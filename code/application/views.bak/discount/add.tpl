<h3>Получение сертификата на скидку</h3>
{$page.description}
<font color='#666666' size='2' face='Tahoma, Verdana'>
	<form onsubmit='return validate()' action='' method='post'><input type="hidden" name="PHPSESSID" value="9e43aca4fec82c4352ec55b3db2d2772" />
		<table style="font-size:13px;" cellpadding=4 cellspacing=0>
			<tr class="{$data.err_author}">
				<td>
					<b>Контактное лицо:</b><font color='Red'><sup><b>*</b></sup></font>
				</td>
				<td>
					<input type='text' name='author' size='40' value="{$data.author}">
				</td>
			</tr>
			{if $data.err_author_required}<tr><td colspan=2><span class="errorMedium">Обязательное поле не задано</span></td></tr>{/if}

			<tr class="{$data.err_email}">
				<td><b>E-mail:</b><font color='Red'><sup><b>*</b></sup></font></td>
				<td><input type='text' name='email' size='40' value="{$data.email}"></td>
			</tr>
			{if $data.err_email_email}<tr><td colspan=2><span class="errorMedium">Поле не соответствует формату e-mail someone@supermail.ru</span></td></tr>{/if}
			{if $data.err_email_required}<tr><td colspan=2><span class="errorMedium">Обязательное поле не задано</span></td></tr>{/if}

		</table><br>
		<center>
			<input type='submit' name='submit' value='Отправить'>
		</center>
		<input type='hidden' name='act' value='send_form'>
	</form>
</font>