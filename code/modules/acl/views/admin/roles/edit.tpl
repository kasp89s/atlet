<form action="/admin/roles/edit?id={$data.id}" method="POST">
<input type="hidden" name="id" value="{$data.id}">

	<div class="box">
		<h3>Роль</h3>
		<div class="inside">
			<p>
				<label for="name">Название*</label>
				<input type="text" id="name" name="name" class="{$data.err_name}" value="{$data.name}"/>
				<br><span class="field_comment">Максимальная длина: 50 символов</span>
				{if $data.err_name_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_name_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="description">Описание</label>
				<input type="text" id="description" name="description" class="{$data.err_description}" value="{$data.description}"/>
				<br><span class="field_comment">Максимальная длина: 255 символов</span>
				{if $data.err_description_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

		</div>
	</div>

	{if $actions}
	<table cellspacing="0" cellpadding="0" class="table">
		<thead align="left" valign="middle">
			<tr>
	        <td nowrap>Общие настройки</td>
	        <td nowrap width="1%">Публикация</td>
	        <td nowrap width="1%">Добавление</td>
	        <td nowrap width="1%">Редактирование</td>
	        <td nowrap width="1%">Удаление</td>
			</tr>
		</thead>
		<tbody align="left" valign="middle">
		    {counter start=0 skip=1 print=false}
		    {foreach from=$actions.common name=rows item=item}
		    <tr class="row_p">
				<td>{$item.name}</td>
				<td align="center">{if $item.publication}<input type="checkbox" name="actions[]" value="{$item.publication.id}" {if $item.publication.selected}checked{/if}>{else}&nbsp;{/if}</td>
		        <td align="center"><input type="checkbox" name="actions[]" value="{$item.add.id}" {if $item.add.selected}checked{/if}></td>
		        <td align="center"><input type="checkbox" name="actions[]" value="{$item.edit.id}" {if $item.edit.selected}checked{/if}></td>
		        <td align="center"><input type="checkbox" name="actions[]" value="{$item.del.id}" {if $item.del.selected}checked{/if}></td>
		    </tr>
		    {/foreach}
		</tbody>
	</table>
	{/if}

	<p>
		<input type="submit" id="submit" name="submit" value="Сохранить"/>
		{if $data.id}<input type="button" value="Удалить" onclick="{literal}if(!window.confirm('Удалить?')) { return false; } else { document.del_form.submit(); return false;}{/literal}"/>{/if}
		<input type="button" value="Список" onclick="window.location='/admin/roles'"/>
	</p>
</form>


<form action="/admin/roles/delete" method="get" name="del_form" id="del_form">
	<input type="hidden" name="id" value="{$data.id}">
</form>