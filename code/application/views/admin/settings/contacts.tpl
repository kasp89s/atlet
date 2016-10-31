<form method="POST">
	<div class="box">
		<h3>Контактные данные для системы</h3>
		<div class="inside">
			<p>
				<label for="order_email">Email для заказов*</label>
				<input type="text" id="order_email" name="order_email" class="{$data.err_order_email}" value="{$data.order_email}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_order_email_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_order_email_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
				{if $data.err_order_email_email}<br><span class="error">Поле не соответствует формату e-mail someone@supermail.ru</span>{/if}
			</p>
            <p>
				<label for="order_copy_email">Email для заказов (копия)*</label>
				<input type="text" id="order_copy_email" name="order_copy_email" class="{$data.err_order_copy_email}" value="{$data.order_copy_email}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_order_copy_email_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_order_copy_email_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
				{if $data.err_order_copy_email_email}<br><span class="error">Поле не соответствует формату e-mail someone@supermail.ru</span>{/if}
			</p>

			<p>
				<label for="callback_email">Email для заказов звонков*</label>
				<input type="text" id="callback_email" name="callback_email" class="{$data.err_callback_email}" value="{$data.callback_email}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_callback_email_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_callback_email_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
				{if $data.err_callback_email_email}<br><span class="error">Поле не соответствует формату e-mail someone@supermail.ru</span>{/if}
			</p>

			<p>
				<label for="default_email_from">Email отправителя по умолчанию*</label>
				<input type="text" id="default_email_from" name="default_email_from" class="{$data.err_default_email_from}" value="{$data.default_email_from}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_default_email_from_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_default_email_from_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
				{if $data.err_default_email_from_email}<br><span class="error">Поле не соответствует формату e-mail someone@supermail.ru</span>{/if}
			</p>

			<p>
				<label for="default_email_from_title">Имя отправителя по умолчанию*</label>
				<input type="text" id="default_email_from_title" name="default_email_from_title" class="{$data.err_default_email_from_title}" value="{$data.default_email_from_title}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_default_email_from_title_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="retailPercent">Процент рознечной цены*</label>
				<input type="text" id="retailPercent" name="retailPercent" class="{$data.err_retailPercent}" value="{$data.retailPercent}"/>
				<br><span class="field_comment"></span>
				{if $data.err_retailPercent_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>
		</div>
	</div>

	<p>
		<input type="submit" id="submit" name="submit" value="Сохранить"/>
	</p>
</form>
