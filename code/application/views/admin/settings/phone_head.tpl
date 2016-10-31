<form method="POST">
	<div class="box">
		<h3>Телефон в шапке</h3>
		<div class="inside">
			<p>
				<label for="city_code">Код города*</label>
				<input type="text" id="city_code" name="city_code" class="{$data.err_city_code}" value="{$data.city_code}"/>
				<br><span class="field_comment">Максимальная длина: 10 символов</span>
				{if $data.err_city_code_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_city_code_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="phone">Телефон*</label>
				<input type="text" id="phone" name="phone" class="{$data.err_phone}" value="{$data.phone}"/>
				<br><span class="field_comment">Максимальная длина: 10 символов</span>
				{if $data.err_phone_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_phone_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>
		</div>
	</div>

	<p>
		<input type="submit" id="submit" name="submit" value="Сохранить"/>
	</p>
</form>