<form action="/admin/catalog/edit?group_id={$group_id}&id={$data.id}" method="POST" enctype="multipart/form-data">
<input type="hidden" name="id" value="{$data.id}">
<input type="hidden" name="group_id" value="{$group_id}">

	<div class="box">
		<h3>Редактирование товара</h3>
		<div class="inside">

			<p>
				<label for="name">Название*</label>
				<input type="text" id="name" name="name" class="{$data.err_name}" value="{$data.name}"/>
                <br />
				<input name="use_h1" type="checkbox" value="1"{if $data.use_h1}checked{/if}>
				Показывать как тег H1
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_name_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_name_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="code">Артикул*</label>
				<input type="text" id="code" name="code" class="{$data.err_code}" value="{$data.code}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_code_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_code_length}<br><span class="error">Не допустимая длина поля</span>{/if}
			</p>

			<p>
				<label for="price">Цена*</label>
				<input type="text" id="price" name="price" class="{$data.err_price}" value="{$data.price}"/>
				{if $data.err_price_required}<br><span class="error">Обязательное поле не задано</span>{/if}
			</p>

			<p>
				<label for="oldprice">Старая цена(для акции)*</label>
				<input type="text" id="oldprice" name="oldprice" class="{$data.err_oldprice}" value="{$data.oldprice}"/>
			</p>

			<p>
				<label for="availability">Наличие*</label>
				<select name="availability" id="availability" class="{$data.err_availability}" style="width:200px">
					<option value="0">--</option>
					{foreach from=$data.availability item=item}
					<option value="{$item.id}" {$item.selected}>{$item.name}</option>
					{/foreach}
				</select>
				{if $data.err_availability_mod_list}<br><span class="error">Обязательное поле не задано</span>{/if}
			</p>

			<p>
				<label for="uri">URI*</label>
				<input type="text" id="uri" name="uri" class="{$data.err_uri}" value="{$data.uri}"/>
				<br><span class="field_comment">Минимальная длина: 3 символа</span>
				<br><span class="field_comment">Максимальная длина: 200 символов</span>
				{if $data.err_uri_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_uri_length}<br><span class="error">Не допустимая длина поля</span>{/if}
				{if $data.err_uri_unical}<br><span class="error">Элемент с таким URI уже существует. Придумайте другой URI</span>{/if}
			</p>

			<p>
				<label for="sort_order">Порядок сортировки</label>
				<input type="text" id="sort_order" name="sort_order" class="{$data.err_sort_order}" value="{$data.sort_order}"/>
			</p>

			<p>
				<label for="in_action">Учавствует в акции*</label>
				<input type="checkbox" id="in_action" name="in_action" value="1" {if $data.in_action}checked{/if}/>
			</p>

			<p>
				<label for="in_yml">Выгружать в Yandex*</label>
				<input type="checkbox" id="in_yml" name="in_yml" value="1" {if $data.in_yml}checked{/if}/>
			</p>

			<p>
				<label for="in_sgs">Выгружать в Sgs*</label>
				<input type="checkbox" id="in_sgs" name="in_sgs" value="1" {if $data.in_sgs}checked{/if}/>
			</p>

            <p>
				<label for="is_show_in_left_block">Выводить в левом блоке*</label>
				<input type="checkbox" id="is_show_in_left_block" name="is_show_in_left_block" value="1" {if $data.is_show_in_left_block}checked{/if}/>
			</p>

			<p>
				<label for="is_show_on_main_page">Выводить на главной*</label>
				<input type="checkbox" id="is_show_on_main_page" name="is_show_on_main_page" value="1" {if $data.is_show_on_main_page}checked{/if}/>
			</p>

			<p>
				<label for="active">Активность*</label>
				<input type="checkbox" id="active" name="active" value="1" {if $data.active || !$data.id}checked{/if}/>
			</p>

			<p>
				<label for="description">Полное описание</label>
				{$data.description_editor}
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
				<label for="imageleft">Изображение для блока под меню</label>
				<input type="file" id="imageleft" name="imageleft" class="{$data.err_imageleft}">
				<br><span class="field_comment">Допустимые расширения: gif, jpg, jpeg, png</span>
				<br><span class="field_comment">Максимальный размер: 1Мб</span>
				{if $data.err_imageleft_upload}<br><span class="error">Фото не загружено</span>{/if}
				{if $data.err_imageleft_required}<br><span class="error">Фото не загружено</span>{/if}
				{if $data.err_imageleft_valid}<br><span class="error">Фото не загружено</span>{/if}
				{if $data.err_imageleft_type}<br><span class="error">Недопустимое расширение</span>{/if}
				{if $data.err_imageleft_size}<br><span class="error">Превышен размер файла</span>{/if}
				{if $data.media._rows.imageleft}<br><img src="{$data.media._rows.imageleft.preview_2}" border="0">{/if}
			</p>

		</div>
	</div>

	<div class="box">
		<h3>Дополнительные фото</h3>
		<div class="inside">
			<p id="list_photo">
				<label for="photo">Фото</label>
				{foreach from=$data.media.photo item=item}
					<span><a href="{$item.src}" target="_blank">{$item.name}</a>&nbsp;&nbsp;<a href="#" onclick="if(!window.confirm('Потребуется перезагрузка страницы.\nВведенная информация будет потеряна.\nПродолжить?')) return false; else window.location='/admin/catalog/delete_file?group_id={$group_id}&page={$data.id}&id={$item.id}';">[-] Удалить файл</a><br></span>
				{/foreach}
				<a href="#" id="add_photo" onclick="return false;">[+] Добавить файл</a>
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
				<label for="is_use_auto_tags">Использовать АВТО теги*</label>
				<input type="checkbox" id="is_use_auto_tags" name="is_use_auto_tags" value="1" {if $data.is_use_auto_tags}checked{/if}/>
			</p>

			<p>
				<label for="relative_weight">Вес страницы в предложениях "См. также"*</label>
				<input type="text" id="relative_weight" name="relative_weight" class="{$data.err_relative_weight}" value="{$data.relative_weight}"/>
				<label for="is_show_in_relative">Показывать в предложениях "См. также"*</label>
				<input type="checkbox" id="is_show_in_relative" name="is_show_in_relative" value="1" {if $data.is_show_in_relative}checked{/if}/>
			</p>
		</div>
	</div>

	<div class="box">
		<h3>Учавсвует в VIP категориях</h3>
		<div class="inside">
			<p>
				{foreach from=$data.in_vip_cats item=item}
					<input style="float:left" id="check{$item.group_id}" name="in_vip_cat_{$data.id}[{$item.group_id}]" type="checkbox" value="{$item.group_id}"{if $item.in_group}checked{/if}>
					<label for="check{$item.group_id}">{$item.group_title}</label>
					<br />
				{/foreach}
			</p>
		</div>
	</div>

	<p>
		<input type="submit" id="submit" name="submit" value="Сохранить"/>
		{if 'catalog_del'|acl_is_allowed && $data.id}<input type="button" value="Удалить" onclick="{literal}if(!window.confirm('Удалить?')) { return false; } else { document.del_form.submit(); return false;}{/literal}"/>{/if}
		<input type="button" value="Список группы" onclick="window.location='/admin/catalog?group_id={$group_id}'">
	</p>
</form>


<form action="/admin/catalog/delete" method="get" name="del_form" id="del_form">
	<input type="hidden" name="id" value="{$data.id}">
	<input type="hidden" name="group_id" value="{$group_id}">
</form>
{literal}
<script type="text/javascript"><!--
var count_photo = 0;

$(document).ready(function(){
	$("#add_photo").click(function(){
		count_photo++;
		$("#add_photo").before('<span id="div_photo_'+count_photo+'"><input type="file" name="photo_'+count_photo+'" style="margin-bottom:5px;"/>&nbsp;<a href="#" onclick="$(\'span#div_photo_'+count_photo+'\').empty(); return false;">[-] Удалить файл</a><br></span>');
	});
});
//--></script>
{/literal}