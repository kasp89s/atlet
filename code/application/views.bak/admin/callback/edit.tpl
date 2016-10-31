<link rel="stylesheet" href="/libs/calendar/skins/aqua/theme.css" type="text/css">
<script type="text/javascript" src="/libs/calendar/calendar_stripped.js"></script>
<script type="text/javascript" src="/libs/calendar/lang/calendar-ru2-utf8.js"></script>
<script type="text/javascript" src="/libs/calendar/calendar-setup_stripped.js"></script>

<form action="/admin/callback/edit?id={$data.id}" method="POST" enctype="multipart/form-data">
<input type="hidden" name="id" value="{$data.id}">

	<div class="box">
		<h3>Запись</h3>
		<div class="inside">
			<p>
				<label>Номер</label>
				{$data.id}
			</p>

			<p>
				<label>Дата подачи</label>
				{$data.date_create|date_format:"%d.%m.%Y"}
			</p>

			<p>
				<label for="cat">Статус*</label>
				<select name="cat" id="cat" class="{$data.err_cat}" style="width:200px">
					<option value="0">--</option>
					{foreach from=$data.cat item=item}
					<option value="{$item.id}" {$item.selected}>{$item.name}</option>
					{/foreach}
				</select>
				{if $data.err_cat_mod_list}<br><span class="error">Обязательное поле не задано</span>{/if}
			</p>

			<p>
				<label for="author">Данные отправителя сообщения*</label>
				<textarea id="author" name="author" class="{$data.err_author}" style="width:650px; height:70px;">{$data.author}</textarea>
				{if $data.err_author_required}<br><span class="error">Обязательное поле не задано</span>{/if}
			</p>

			<p>
				<label for="phone">Телефон*</label>
				<input type="text" id="phone" name="phone" class="{$data.err_phone}" value="{$data.phone}"/>
				<br><span class="field_comment">Максимальная длина: 50 символов</span>
				{if $data.err_phone_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
				{if $data.err_phone_required}<br><span class="error">Обязательное поле не задано</span>{/if}
			</p>

			<p>
				<label for="email">E-mail</label>
				<input type="text" id="email" name="email" class="{$data.err_email}" value="{$data.email}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_email_email}<br><span class="error">Поле не соответствует формату e-mail someone@supermail.ru</span>{/if}
				{if $data.err_email_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="description">Дополнительная информация</label>
				<textarea id="description" name="description" class="{$data.err_description}" style="width:650px; height:70px;">{$data.description}</textarea>
			</p>

		</div>
	</div>

	<p>
		<input type="submit" id="submit" name="submit" value="Сохранить"/>
		<input type="button" value="Список" onclick="window.location='/admin/callback'"/>
	</p>
</form>


{literal}
<script type="text/javascript"><!--
var count_files = 0;

$(document).ready(function(){
	$("#add_file").click(function(){
		count_files++;
		$("#add_file").before('<span id="div_file_'+count_files+'"><input type="file" name="file_'+count_files+'" style="margin-bottom:5px;"/>&nbsp;<a href="#" onclick="$(\'span#div_file_'+count_files+'\').empty(); return false;">[-] Удалить файл</a><br></span>');
	});
});
//--></script>
{/literal}
