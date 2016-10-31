<link rel="stylesheet" href="/libs/calendar/skins/aqua/theme.css" type="text/css">
<script type="text/javascript" src="/libs/calendar/calendar_stripped.js"></script>
<script type="text/javascript" src="/libs/calendar/lang/calendar-ru2-utf8.js"></script>
<script type="text/javascript" src="/libs/calendar/calendar-setup_stripped.js"></script>

<form action="/admin/news/edit?id={$data.id}" method="POST" enctype="multipart/form-data">
<input type="hidden" name="id" value="{$data.id}">

	<div class="box">
		<h3>Новость</h3>
		<div class="inside">
			<p>
				<label for="date_publication">Дата публикации*</label>
				<input type="text" style="width: 70px;" value="{$data.date_publication|date_format:"%d.%m.%Y"}" id="date_publication" name="date_publication" maxlength="10" class="ti_input_data {$data.err_date_publication}">
				<input type="button" alt="Календарь" class="ti_cal" id="trigger_date_publication">
				{if $data.err_date_publication_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_date_publication_date}<br><span class="error">Неверный формат даты 31.01.2055</span>{/if}
			</p>

			<p>
				<label for="uri">URI*</label>
				<input type="text" id="uri" name="uri" class="{$data.err_uri}" value="{$data.uri}"/>
				<br><span class="field_comment">Минимальная длина: 3 символа</span>
				<br><span class="field_comment">Максимальная длина: 200 символов</span>
				{if $data.err_uri_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_uri_length}<br><span class="error">Не допустимая длина поля</span>{/if}
			</p>

			<p>
				<label for="name">Заголовок*</label>
				<input type="text" id="name" name="name" class="{$data.err_name}" value="{$data.name}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_name_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_name_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="preview">Краткий текст*</label>
				<textarea id="preview" name="preview" class="{$data.err_preview}" style="width:650px" rows="5">{$data.preview}</textarea>
				{if $data.err_preview_required}<br><span class="error">Обязательное поле не задано</span>{/if}
			</p>

			<p>
				<label for="description">Полный текст*</label>
				{$data.description_editor}
				{if $data.err_description_required}<br><span class="error">Обязательное поле не задано</span>{/if}
			</p>

			{if 'news_publication'|acl_is_allowed}
			<p>
				<label for="active">Активность*</label>
				<input type="checkbox" id="active" name="active" value="1" {if $data.active || !$data.id}checked{/if}/>
			</p>
			{/if}

		</div>
	</div>

	<div class="box">
		<h3>SEO</h3>
		<div class="inside">
			<p>
				<label for="seo_title">Title (заголовок)*</label>
				<input type="text" id="seo_title" name="seo_title" class="{$data.err_seo_title}" value="{$data.seo_title}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_seo_title_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_seo_title_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="seo_keywords">Keywords (ключевые слова)*</label>
				<input type="text" id="seo_keywords" name="seo_keywords" class="{$data.err_seo_keywords}" value="{$data.seo_keywords}"/>
				<br><span class="field_comment">Максимальная длина: 200 символов</span>
				{if $data.err_seo_keywords_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_seo_keywords_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="seo_description">Description (описание)*</label>
				<input type="text" id="seo_description" name="seo_description" class="{$data.err_seo_description}" value="{$data.seo_description}"/>
				<br><span class="field_comment">Максимальная длина: 200 символов</span>
				{if $data.err_seo_description_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_seo_description_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>
		</div>
	</div>

	<p>
		<input type="submit" id="submit" name="submit" value="Сохранить"/>
		{if 'news_del'|acl_is_allowed && $data.id}<input type="button" value="Удалить" onclick="{literal}if(!window.confirm('Удалить?')) { return false; } else { document.del_form.submit(); return false;}{/literal}"/>{/if}
		<input type="button" value="Список" onclick="window.location='/admin/news'"/>
	</p>
</form>

<form action="/admin/news/delete" method="get" name="del_form" id="del_form">
	<input type="hidden" name="id" value="{$data.id}">
</form>

{literal}
<script type="text/javascript"><!--
Calendar.setup({
	inputField : "date_publication",
	ifFormat : "%d.%m.%Y",
	showsTime : false,
	button : "trigger_date_publication",
	onClose : function(cal) {
		document.getElementById('date_publication').value = cal.date.print("%d.%m.%Y");
		cal.hide();
	}
});
//--></script>
{/literal}