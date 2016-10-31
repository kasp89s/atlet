<form action="/admin/users/edit?id={$data.id}" method="POST">
<input type="hidden" name="id" value="{$data.id}">

	<div class="box">
		<h3>Пользователь</h3>
		<div class="inside">
			<p>
				<label for="username">Логин*</label>
				{$data.username}
				<input type="hidden" id="username" name="username" value="{$data.username}"/>
			</p>
			
			<p>
				<label for="password">Пароль*</label>
				<input type="password" id="password" name="password" class="{$data.err_password} {$data.err_password_check}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_password_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_password_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="password_check">Повторите пароль*</label>
				<input type="password" id="password_check" name="password_check" class="{$data.err_password_check}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_password_check_matches}<br><span class="error">Введенные пароли не совпадают</span>{/if}
			</p>
			
			<p>
				<label for="fio">ФИО*</label>
				<input type="text" id="fio" name="fio" class="{$data.err_fio}" value="{$data.fio}"/>
				<br><span class="field_comment">Максимальная длина: 255 символов</span>
				{if $data.err_fio_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_fio_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>
			
			<p>
				<label for="phone">Телефон</label>
				<input type="text" id="phone" name="phone" class="{$data.err_phone}" value="{$data.phone}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_phone_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>
			
			<p>
				<label for="email">E-mail</label>
				<input type="text" id="email" name="email" class="{$data.err_email}" value="{$data.email}"/>
				<br><span class="field_comment">Максимальная длина: 255 символов</span>
				{if $data.err_email_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
				{if $data.err_email_email}<br><span class="error">Неверный формат E-mail</span>{/if}
			</p>
			
			<p>
				<label for="address">Адрес</label>
				<input type="text" id="address" name="address" class="{$data.err_address}" value="{$data.address}"/>
				<br><span class="field_comment">Максимальная длина: 255 символов</span>
			</p>
			
		</div>
	</div>
	

	
<p><input type="submit" id="submit" name="submit" value="Сохранить"/></p>
</form>