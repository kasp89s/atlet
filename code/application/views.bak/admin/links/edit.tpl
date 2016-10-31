<form action="/admin/links/edit?id={$data.id}" method="POST" enctype="multipart/form-data">
<input type="hidden" name="id" value="{$data.id}">

	<div class="box">
		<h3>Статья</h3>
		<div class="inside">
			<p>
				<label for="name">Название*</label>
				<input type="text" id="name" name="name" class="{$data.err_name}" value="{$data.name}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_name_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_name_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="url">Ссылка*</label>
				<input type="text" id="url" name="url" class="{$data.err_url}" value="{$data.url}"/>
				<br><span class="field_comment">Максимальная длина: 250 символов</span>
				{if $data.err_url_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
				{if $data.err_url_url}<br><span class="error">Поле не соответствует формату адреса http://www.someaddress.ru/</span>{/if}
			</p>

			<p>
				<label for="sort">Порядок*</label>
				<input type="text" id="sort" name="sort" class="{$data.err_sort}" value="{$data.sort}"/>
			</p>

			<p>
				<label for="active">Активность*</label>
				<input type="checkbox" id="active" name="active" value="1" {if $data.active || !$data.id}checked{/if}/>
			</p>
		</div>
	</div>

	<p>
		<input type="submit" id="submit" name="submit" value="Сохранить"/>
		{if 'links_del'|acl_is_allowed && $data.id}<input type="button" value="Удалить" onclick="{literal}if(!window.confirm('Удалить?')) { return false; } else { document.del_form.submit(); return false;}{/literal}"/>{/if}
		<input type="button" value="Список" onclick="window.location='/admin/links'"/>
	</p>
</form>


<form action="/admin/links/delete" method="get" name="del_form" id="del_form">
	<input type="hidden" name="id" value="{$data.id}">
</form>