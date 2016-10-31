<form action="/admin/pages/edit?id={$data.id}" method="POST" enctype="multipart/form-data">
<input type="hidden" name="id" value="{$data.id}">

	<div class="box">
		<h3>Настройки страницы</h3>
		<div class="inside">
			<table cellspacing="0" cellpadding="0" style="width:97%;border:0;">
				<tr>
					<td style="width:23%;">
						<input type="radio" name="type" value="none" onchange="change_type('none')" {if $data.type_none_checked}checked{/if}/> Текстовая страница
					</td>
					<td>
						<!--пусто-->
					</td>
				</tr>
				<tr>
					<td>
						<input type="radio" name="type" value="module" onchange="change_type('module')" {if $data.type_module_checked}checked{/if}/> Загрузить модуль:

					</td>
					<td>
						<select name="module" id="module" style="width:300px; {if !$data.type_module_checked}background-color: #DFDFDF{/if}">
							{foreach from=$data.module item=item}
							<option value="{$item.id}" {$item.selected}>{$item.name}</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<input type="radio" name="type" value="redirect" onchange="change_type('redirect')" {if $data.type_redirect_checked}checked{/if}/> Переадресовать в:
					</td>
					<td>
						<input type="text" id="redirect" name="redirect" value="{$data.redirect}"  style="width:300px; {if !$data.type_redirect_checked}background-color: #DFDFDF{/if}"/>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="box">
		<h3>Страница</h3>
		<div class="inside">
			<p>
				<label for="title">Название страницы*</label>
				<input type="text" id="title" name="title" class="{$data.err_title}" value="{$data.title}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_title_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_title_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="title">URI (фрагмент ссылки)*</label>
				<input type="text" id="uri" name="uri" class="{$data.err_uri}" value="{$data.uri}"/>
				<br><span class="field_comment">Максимальная длина: 200 символов</span>
				{if $data.err_uri_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_uri_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p id="pageData" {if not $data.type_none_checked}style="display:none;"{/if}>
				<label for="preview">Краткий текст*</label>
				<textarea id="preview" name="preview" class="{$data.err_preview}">{$data.preview}</textarea>
				{if $data.err_preview_required}<br><span class="error">Обязательное поле не задано</span>{/if}
			</p>

			<p>
				<label for="description">Полный текст*</label>
				{$data.description_editor}
				{if $data.err_description_required}<br><span class="error">Обязательное поле не задано</span>{/if}
			</p>


			<p>
				<label for="image">Изображение</label>
				<input type="file" id="image" name="image" class="{$data.err_image}">
				<br><span class="field_comment">Допустимые расширения: gif, jpg, jpeg, png</span>
				<br><span class="field_comment">Максимальный размер: 1Мб</span>
				{if $data.err_image_upload}<br><span class="error">Фото не загружено</span>{/if}
				{if $data.err_image_required}<br><span class="error">Фото не загружено</span>{/if}
				{if $data.err_image_valid}<br><span class="error">Фото не загружено</span>{/if}
				{if $data.err_image_type}<br><span class="error">Недопустимое расширение</span>{/if}
				{if $data.err_image_size}<br><span class="error">Превышен размер файла</span>{/if}
				{if $data.media._rows.image}<br><img src="{$data.media._rows.image.preview_2}" border="0">{/if}
			</p>

			<p>
				<label for="active_menu">Узел участвует в меню</label>
				<input type="checkbox" id="active_menu" name="active_menu" value="1" {if $data.active_menu || !$data.id}checked{/if}/>
			</p>
		</div>
	</div>

	<div class="box" id="pageSeo" {if $data.type_redirect_checked}style="display:none;"{/if}>
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
		{if 'cms_del'|acl_is_allowed && $data.id}<input type="button" value="Удалить" onclick="{literal}if(!window.confirm('Удалить?')) { return false; } else { document.del_form.submit(); return false;}{/literal}"/>{/if}
		<input type="button" value="Список" onclick="window.location='/admin/pages'"/>
	</p>
</form>

<form action="/admin/pages/delete" method="get" name="del_form" id="del_form">
	<input type="hidden" name="id" value="{$data.id}">
</form>

{literal}
<script type="text/javascript">
function change_type(name){
	var el_module = document.getElementById('module');
	var el_redirect = document.getElementById('redirect');
	var pageData=document.getElementById('pageData');
	var pageSeo=document.getElementById('pageSeo');
	if(name == 'none') {
		el_module.style.backgroundColor = "#DFDFDF";
		el_redirect.style.backgroundColor = "#DFDFDF";
		pageData.style.display="block";
		pageSeo.style.display="block";

	} else if(name == 'module') {
		el_module.style.backgroundColor = "";
		el_redirect.style.backgroundColor = "#DFDFDF";
		pageData.style.display="none";
		pageSeo.style.display="block";

	} else if(name == 'redirect') {
		el_module.style.backgroundColor = "#DFDFDF";
		el_redirect.style.backgroundColor = "";
		pageData.style.display="none";
		pageSeo.style.display="none";
	}
}
</script>
{/literal}