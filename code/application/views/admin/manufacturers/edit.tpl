<link rel="stylesheet" href="/libs/calendar/skins/aqua/theme.css" type="text/css">
<script type="text/javascript" src="/libs/calendar/calendar_stripped.js"></script>
<script type="text/javascript" src="/libs/calendar/lang/calendar-ru2-utf8.js"></script>
<form action="/admin/manufacturers/edit?id={$data.id}" method="POST" enctype="multipart/form-data">
<input type="hidden" name="id" value="{$data.id}">

	<div class="box">
		<h3>Производитель</h3>
		<div class="inside">
			<p>
				<label for="name">Название*</label>
				<input type="text" id="name" name="name" class="{$data.err_name}" value="{$data.name}" readonly/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_name_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_name_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="description">Полный текст*</label>
				{$data.description_editor}
				{if $data.err_description_required}<br><span class="error">Обязательное поле не задано</span>{/if}
			</p>

		</div>
	</div>

	<p>
		<input type="submit" id="submit" name="submit" value="Сохранить"/>
		{if 'news_del'|acl_is_allowed && $data.id}<input type="button" value="Удалить" onclick="{literal}if(!window.confirm('Удалить?')) { return false; } else { document.del_form.submit(); return false;}{/literal}"/>{/if}
		<input type="button" value="Список" onclick="window.location='/admin/manufacturers'"/>
	</p>
</form>

<form action="/admin/manufacturers/delete" method="get" name="del_form" id="del_form">
	<input type="hidden" name="id" value="{$data.id}">
</form>
