<p>
	Заполните все поля.<br>
	Важно знать, что цена за напоминанеи составит <span class="label label-important">{$price}руб</span>
</p>
<br>
{if $data.err_validate}
<div class="alert alert-error">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<strong>Ошибка</strong>. Форма заполнена некорректно. Исправьте ошибки и попробуйте еще раз
</div>
{/if}
<form action="" method="post" class="form-horizontal" id="form-order">

	<div class="control-group {$data.err_phone_code} {$data.err_phone_number}">
		<label class="control-label" for="phone_code">Телефон <span class="required">*</span></label>
		<div class="controls">
			<input type="text" id="phone_code" name="phone_code" placeholder="905" maxlength="3" class="span1" value="{$data.phone_code}">
			<input type="text" id="phone_number" name="phone_number" placeholder="1234567" maxlength="7" class="span2" value="{$data.phone_number}">
			{if $data.err_phone_code_required || $data.err_phone_number_required}<span class="help-inline">Обязательное поле не задано</span>{/if}
			{if $data.err_phone_code_length || $data.err_phone_number_length}<span class="help-inline">Формат телефона некорректный</span>{/if}
			{if $data.err_phone_code_digit || $data.err_phone_number_digit}<span class="help-inline">Неверный формат телефона</span>{/if}
		</div>
	</div>

	<div class="control-group {$data.err_email}">
		<label class="control-label" for="email">Email <span class="required">*</span></label>
		<div class="controls">
			<input type="text" id="email" name="email" placeholder="test@example.com" class="span3" value="{$data.email}">
			{if $data.err_email_length}<span class="help-inline">Превышена максимальная длина поля</span>{/if}
			{if $data.err_email_email}<span class="help-inline">Формат Email некорректный</span>{/if}
			{if $data.err_email_required}<span class="help-inline">Обязательное поле не задано</span>{/if}
		</div>
	</div>


	<div class="control-group {$data.err_message}">
		<label class="control-label" for="message">Сообщение <span class="required">*</span></label>
		<div class="controls">
			<textarea id="message" name="message" rows="3" class="span3">{$data.message}</textarea>
			{if $data.err_message_required}<span class="help-inline">Обязательное поле не задано</span>{/if}
			{if $data.err_message_length}<span class="help-inline">Слишком длинное сообщение</span>{/if}
			<div>&nbsp;</div>
		</div>
	</div>

	<div class="control-group {$data.err_timezone}">
		<label class="control-label" for="timezone">Часовой пояс <span class="required">*</span></label>
		<div class="controls">

			<select name="timezone" id="timezone" class="span3">
				<option value="">Выберите часовой пояс</option>
				{foreach from=$data.timezone item=item}
				<option value="{$item.UTC}">{$item.UTC|sign}{$item.UTC} UTC | {$item.MSK|sign}{$item.MSK} МСК | {$item.cities}</option>
				{/foreach}
			</select>
			{if $data.err_timezone_mod_list}<span class="help-inline">Обязательное поле не задано</span>{/if}
		</div>
	</div>

	<div class="control-group {$data.err_date} {$data.err_time}">
		<label class="control-label" for="date">Когда <span class="required">*</span></label>
		<div class="controls">
			<input type="text" id="date" name="date" class="span2" value="{$data.date}">
			<input type="text" id="time" name="time" class="span1" value="{$data.time}">
			{if $data.err_date_required || $data.err_time_required}<span class="help-inline">Обязательное поле не задано</span>{/if}
		</div>
	</div>

	<div class="control-group {$data.err_captcha}">
		<label class="control-label" for="captcha">Введите циферки <span class="required">*</span></label>
		<div class="controls">
			<div class="input-prepend">
				{$captcha}
				<input type="text" id="captcha" name="captcha" class="span2 {$data.err_captcha}" autocomplete="off" maxlength="3" />
			</div>
			{if $data.err_captcha}<span class="help-inline">Неверное значение</span>{/if}
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<label class="checkbox">
				<input type="checkbox" id="agree" name="agree" value="1"> С <a href="#rules" data-toggle="modal">правилами</a> согласен
			</label>
			<button type="button" id="btn-submit" class="btn btn-primary disabled">Сохранить</button>
		</div>
	</div>
</form>


<div id="rules" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Правила сервиса</h3>
	</div>
	<div class="modal-body">
		<p>One fine body…</p>
		<ol>
			<li>Запрещается</li>
			<li>Запрещается</li>
			<li>Запрещается</li>
			<li>Запрещается</li>
		</ol>
		<p>One fine body…</p>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</button>
	</div>
</div>

{literal}
<script>
$(function(){
	$("#message").counter({
		goal: 70,
		msg: 'осталось'
	});

	$('#date').datepicker({
		format: 'dd.mm.yyyy',
		viewMode: 'years'
	});

	$('#time').timepicker({
		showMeridian: false,
		minuteStep: 5,
		defaultTime: 'value'
	});

	$('#agree').click(function(){
		refresh_status();
	});
	refresh_status();

	$('#btn-submit').click(function(){
		if($(this).is(':not(.disabled)')) {
			$(this).button('loading');
			$('#form-order').submit();
		}
	});

	odate = new Date();
	timezone = -odate.getTimezoneOffset()/60;
	$('#timezone').val(timezone);
})

function refresh_status() {
	if($('#agree').prop('checked')) {
		$('#btn-submit').removeClass('disabled');
		$('#btn-submit').addClass('loading');
	} else {
		$('#btn-submit').addClass('disabled');
		$('#btn-submit').removeClass('loading');
	}
}
</script>
{/literal}