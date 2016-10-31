<form action="/admin/catorder/edit?id={$data.id}" method="POST">
<input type="hidden" name="id" value="{$data.id}">

	<div class="box">
		<h3>Тематика</h3>
		<div class="inside">
			<p>
				<label for="name">Название*</label>
				<input type="text" id="name" name="name" class="{$data.err_name}" value="{$data.name}"/>
				<br><span class="field_comment">Максимальная длина: 170 символов</span>
				{if $data.err_name_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_name_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="description">Описание</label>
				{$data.description_editor}
			</p>

			<p>
				<label for="active">Активность*</label>
				<input type="checkbox" id="active" name="active" value="1" {if $data.active || !$data.id}checked{/if}/>
			</p>
		</div>
	</div>

	<p>
		<input type="submit" id="submit" name="submit" value="Сохранить"/>
		{if 'feedback_del'|acl_is_allowed && $data.id}<input type="button" value="Удалить" onclick="{literal}if(!window.confirm('Удалить?')) { return false; } else { document.del_form.submit(); return false;}{/literal}"/>{/if}
		<input type="button" value="Список" onclick="window.location='/admin/catorder'"/>
	</p>
</form>


<form action="/admin/catorder/delete" method="get" name="del_form" id="del_form">
	<input type="hidden" name="id" value="{$data.id}">
</form>