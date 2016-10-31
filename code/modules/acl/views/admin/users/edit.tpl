{literal}
<style type="text/css">
.radio_default {
    color: #fff;
    padding: 3px 5px;
    background: #DFDFDF;
    border: 4px;
}
.radio_allow {
    color: #fff;
    padding: 3px 5px;
    background: #C2EFC2;
    border: 4px;
}
.radio_deny {
    color: #fff;
    padding: 4px 5px;
    background: #FAC9CB;
    border: 4px;
}
</style>
{/literal}

<form action="/admin/users/edit?id={$data.id}" method="POST">
<input type="hidden" name="id" value="{$data.id}">

	<div class="box">
		<h3>Пользователь</h3>
		<div class="inside">
			{if $data.username_no_edit}
			<p>
				<label for="username">Логин*</label>
				{$data.username}
				<input type="hidden" id="username" name="username" value="{$data.username}"/>
			</p>
			{else}
			<p>
				<label for="username">Логин*</label>
				<input type="text" id="username" name="username" class="{$data.err_username}" value="{$data.username}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_username_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_username_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			{/if}

			<p>
				<label for="password">Пароль*</label>
				<input type="password" id="password" name="password" class="{$data.err_password} {$data.err_password_check}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_password_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_password_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="password_check">Повторите пароль*</label>
				<input type="password" id="password_check" name="password_check" class="{$data.err_password_check}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_password_check_matches}<br><span class="error">Введенные пароли не совпадают</span>{/if}
			</p>

			<p>
				<label for="fio">ФИО*</label>
				<input type="text" id="fio" name="fio" class="{$data.err_fio}" value="{$data.fio}"/>
				<br><span class="field_comment">Максимальная длина: 255 символов</span>
				{if $data.err_fio_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_fio_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="phone">Телефон</label>
				<input type="text" id="phone" name="phone" class="{$data.err_phone}" value="{$data.phone}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_phone_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>

			<p>
				<label for="email">E-mail</label>
				<input type="text" id="email" name="email" class="{$data.err_email}" value="{$data.email}"/>
				<br><span class="field_comment">Максимальная длина: 255 символов</span>
				{if $data.err_email_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
				{if $data.err_email_email}<br><span class="error">Неверный формат E-mail</span>{/if}
			</p>

			<p>
				<label for="address">Адрес</label>
				<input type="text" id="address" name="address" class="{$data.err_address}" value="{$data.address}"/>
				<br><span class="field_comment">Максимальная длина: 255 символов</span>
			</p>
		</div>
	</div>

	{if $data.roles}
	<table cellspacing="0" cellpadding="0" class="table">
		<thead align="left" valign="middle">
			<tr>
	        <td nowrap>Роли</td>
	        <td nowrap>&nbsp;</td>
			</tr>
		</thead>
		<tbody align="left" valign="middle">
		    {counter start=0 skip=1 print=false}
		    {foreach from=$data.roles name=rows item=item}
		    <tr class="row_p">
				<td>{$item.description} [{$item.name}]</td>
		        <td align="center"><input type="checkbox" name="roles[]" value="{$item.id}" {if $item.selected}checked{/if}></td>
		    </tr>
		    {/foreach}
		</tbody>
	</table>
	{/if}

	{if $actions}
	<table cellspacing="0" cellpadding="0" class="table">
		<thead align="left" valign="middle">
			<tr>
	        <td nowrap>Общие настройки</td>
	        <td align="center">Публикация</td>
	        <td align="center">Добавление</td>
	        <td align="center">Редактирование</td>
	        <td align="center">Удаление</td>
			</tr>
		</thead>
		<tbody align="left" valign="middle">
		    {counter start=0 skip=1 print=false}
		    {foreach from=$actions.common name=rows item=item}
		    <tr class="row_p">
				<td>{$item.name}</td>
		        <td align="center">
		        	{if $item.publication}
		        	<span class="radio_default"><input type="radio" name="actions[{$item.publication.id}]" value="" {if !$item.publication.allow && !$item.publication.deny}checked{/if}></span>
		        	<span class="radio_allow"><input type="radio" name="actions[{$item.publication.id}]" value="allow" {if $item.publication.allow}checked{/if}></span>
		        	<span class="radio_deny"><input type="radio" name="actions[{$item.publication.id}]" value="deny" {if $item.publication.deny}checked{/if}></span>
		        	{else}
		        	&nbsp;
		        	{/if}
		        </td>
		        <td align="center">
		        	<span class="radio_default"><input type="radio" name="actions[{$item.add.id}]" value="" {if !$item.add.allow && !$item.add.deny}checked{/if}></span>
		        	<span class="radio_allow"><input type="radio" name="actions[{$item.add.id}]" value="allow" {if $item.add.allow}checked{/if}></span>
		        	<span class="radio_deny"><input type="radio" name="actions[{$item.add.id}]" value="deny" {if $item.add.deny}checked{/if}></span>
		        </td>
		        <td align="center">
		        	<span class="radio_default"><input type="radio" name="actions[{$item.edit.id}]" value="" {if !$item.edit.allow && !$item.edit.deny}checked{/if}></span>
		        	<span class="radio_allow"><input type="radio" name="actions[{$item.edit.id}]" value="allow" {if $item.edit.allow}checked{/if}></span>
		        	<span class="radio_deny"><input type="radio" name="actions[{$item.edit.id}]" value="deny" {if $item.edit.deny}checked{/if}></span>
		        </td>
		        <td align="center">
		        	<span class="radio_default"><input type="radio" name="actions[{$item.del.id}]" value="" {if !$item.del.allow && !$item.del.deny}checked{/if}></span>
		        	<span class="radio_allow"><input type="radio" name="actions[{$item.del.id}]" value="allow" {if $item.del.allow}checked{/if}></span>
		        	<span class="radio_deny"><input type="radio" name="actions[{$item.del.id}]" value="deny" {if $item.del.deny}checked{/if}></span>
		        </td>
		    </tr>
		    {/foreach}
		</tbody>
	</table>
	{/if}

	<p>
		<input type="submit" id="submit" name="submit" value="Сохранить"/>
		{if 'cms_del'|acl_is_allowed && $data.id}<input type="button" value="Удалить" onclick="{literal}if(!window.confirm('Удалить?')) { return false; } else { document.del_form.submit(); return false;}{/literal}"/>{/if}
		<input type="button" value="Список" onclick="window.location='/admin/users'"/>
	</p>
</form>


<form action="/admin/users/delete" method="get" name="del_form" id="del_form">
	<input type="hidden" name="id" value="{$data.id}">
</form>