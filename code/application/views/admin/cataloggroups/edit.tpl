<form action="/admin/cataloggroups/edit?id={$data.id}" method="POST" enctype="multipart/form-data">
<input type="hidden" name="id" value="{$data.id}">


	<div class="box">
		<h3>Группа</h3>
		<div class="inside">
			<p>
				<label for="title">Название*</label>
				<input type="text" id="title" name="title" class="{$data.err_title}" value="{$data.title}"/>
				<br />
				<input name="use_h1" type="checkbox" value="1"{if $data.use_h1}checked{/if}>
				Показывать как тег H1
				<br><span class="field_comment">Максимальная длина: 200 символов</span>
				{if $data.err_title_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_title_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="code">Артикул для картинки под меню</label>
				<input type="text" id="code" name="code" class="{$data.err_code}" value="{$data.code}"/>
				<br><span class="field_comment">Максимальная длина: 200 символов</span>
				{if $data.err_code_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_code_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
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
				<label for="is_custom_color">Пометить цветом</label>
				<input type="checkbox" id="is_custom_color" name="is_custom_color" value="1" {if $data.is_custom_color}checked{/if}/>
			</p>

			<p>
				<label for="custom_color_value">Цвет шрифта в меню</label>
				<input type="text" id="custom_color_value" name="custom_color_value" class="{$data.err_custom_color_value}" value="{$data.custom_color_value}"/>
				<br><span class="field_comment">Максимальная длина: 7 символов</span>
				{if $data.err_custom_color_value_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="short_descr">Краткое описание</label>
				<textarea id="short_descr" name="short_descr" class="{$data.err_short_descr}" rows=5 cols=20 wrap="on">{$data.short_descr}</textarea>
				<br><span class="field_comment">Максимальная длина: 700 символов</span>
			</p>

			<p>
				<label for="full_descr">Полное описание</label>
				{$data.full_descr_editor}
			</p>

			<p>
				<label for="image">Изображение</label>
				<input type="file" id="image" name="image" class="{$data.err_image}">
				<br><span class="field_comment">Допустимые расширения: gif, jpg, jpeg, png</span>
				<br><span class="field_comment">Максимальный размер: 1Мб</span>
				{if $data.err_image_upload}<br><span class="error">Ошибка при загрузке</span>{/if}
				{if $data.err_image_required}<br><span class="error">Фото не загружено</span>{/if}
				{if $data.err_image_valid}<br><span class="error">Не корректное изображение</span>{/if}
				{if $data.err_image_type}<br><span class="error">Недопустимое расширение</span>{/if}
				{if $data.err_image_size}<br><span class="error">Превышен размер файла</span>{/if}
				{if $data.media._rows.image.preview_2}<br><img src="{$data.media._rows.image.preview_2}" border="0">{/if}
			</p>

			<p>
				<label for="is_vip">Вип категория</label>
				<input type="checkbox" id="is_vip" name="is_vip" value="1" {if $data.is_vip || !$data.id}checked{/if}/>
			</p>

			<p>
				<label for="active">Активность</label>
				<input type="checkbox" id="active" name="active" value="1" {if $data.active || !$data.id}checked{/if}/>
			</p>
		</div>
	</div>

	<div class="box">
		<h3>SEO</h3>
		<div class="inside">
		    <p>
				<label for="seo_name">Name (название)</label>
				<input type="text" id="seo_name" name="seo_name" class="{$data.err_seo_name}" value="{$data.seo_name}"/>
				<br />
				<input name="concat_with_section_title" type="checkbox" value="1"{if $data.concat_with_section_title}checked{/if}>
				Склеить с названием группы
				<br><span class="field_comment">Максимальная длина: 200 символов</span>
				{if $data.err_seo_name_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

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

			<p>
				<label for="seo_title" style="color:red">AUTO Title (заголовок)*</label>
				<input type="text" id="seo_auto_title" name="seo_auto_title" class="{$data.err_seo_auto_title}" value="{$data.seo_auto_title}"/>
				<br><span class="field_comment">Доступный тег {literal}{name}{/literal} - название товара</span>
				<br><span class="field_comment">Максимальная длина: 200 символов</span>
				{if $data.err_seo_auto_title_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="seo_auto_keywords" style="color:red">AUTO Keywords (ключевые слова)*</label>
				<input type="text" id="seo_auto_keywords" name="seo_auto_keywords" class="{$data.err_seo_auto_keywords}" value="{$data.seo_auto_keywords}"/>
				<br><span class="field_comment">Доступный тег {literal}{name}{/literal} - название товара</span>
				<br><span class="field_comment">Максимальная длина: 200 символов</span>
				{if $data.err_seo_auto_keywords_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="seo_auto_description" style="color:red">AUTO Description (описание)*</label>
				<input type="text" id="seo_auto_description" name="seo_auto_description" class="{$data.err_seo_auto_description}" value="{$data.seo_auto_description}"/>
				<br><span class="field_comment">Доступный тег {literal}{name}{/literal} - название товара</span>
				<br><span class="field_comment">Максимальная длина: 200 символов</span>
				{if $data.err_seo_auto_description_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="relative_weight">Вес страницы в предложениях "См. также"*</label>
				<input type="text" id="relative_weight" name="relative_weight" class="{$data.err_relative_weight}" value="{$data.relative_weight}"/>
				<label for="is_show_in_relative">Показывать в предложениях "См. также"*</label>
				<input type="checkbox" id="is_show_in_relative" name="is_show_in_relative" value="1" {if $data.is_show_in_relative}checked{/if}/>
			</p>
		</div>
	</div>

	<p>
		<input type="submit" id="submit" name="submit" value="Сохранить"/>
		{if 'catalog_del'|acl_is_allowed && $data.id}<input type="button" value="Удалить" onclick="{literal}if(!window.confirm('Удалить?')) { return false; } else { document.del_form.submit(); return false;}{/literal}"/>{/if}
		<input type="button" value="Список" onclick="window.location='/admin/cataloggroups'"/>
	</p>
</form>


<form action="/admin/cataloggroups/delete" method="get" name="del_form" id="del_form">
	<input type="hidden" name="id" value="{$data.id}">
</form>

{literal}
	 <script language="JavaScript">
	 	  $('#custom_color_value').ColorPicker({
				onSubmit: function(hsb, hex, rgb, el) {
					$(el).val(hex);
					$(el).ColorPickerHide();
				},
				onBeforeShow: function () {
					$(this).ColorPickerSetColor(this.value);
				}
			})
			.bind('keyup', function(){
				$(this).ColorPickerSetColor(this.value);
			});
	 </script>
{/literal}