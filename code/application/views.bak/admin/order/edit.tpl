{getconfig path="core.url_suffix" assign="url_suffix"}
<link rel="stylesheet" href="/libs/calendar/skins/aqua/theme.css" type="text/css">
<script type="text/javascript" src="/libs/calendar/calendar_stripped.js"></script>
<script type="text/javascript" src="/libs/calendar/lang/calendar-ru2-utf8.js"></script>
<script type="text/javascript" src="/libs/calendar/calendar-setup_stripped.js"></script>

<form action="/admin/order/edit?id={$data.id}" method="POST" enctype="multipart/form-data">
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
            {if $data.code}
			<p>
				<label for="code">Артикул*</label>
				<input type="text" id="code" name="code" class="{$data.err_code}" value="{$data.code}"/>
				<br><span class="field_comment">Максимальная длина: 50 символов</span>
				{if $data.err_code_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
				{if $data.err_code_required}<br><span class="error">Обязательное поле не задано</span>{/if}
			</p>
			{/if}
            {if $data.quantity}
			<p>
				<label for="quantity">Количество*</label>
				<input type="text" id="quantity" name="quantity" class="{$data.err_quantity}" value="{$data.quantity}"/>
				<br><span class="field_comment">Максимальное значение = 50</span>
				{if $data.err_quantity_value}<br><span class="error">Некорректное значение поля</span>{/if}
				{if $data.err_quantity_required}<br><span class="error">Обязательное поле не задано</span>{/if}
			</p>
			{/if}

			<p>
				<label for="author">Данные отправителя заказа*</label>
				<textarea id="author" name="author" class="{$data.err_author}" style="width:650px; height:70px;">{$data.author}</textarea>
				{if $data.err_author_required}<br><span class="error">Обязательное поле не задано</span>{/if}
			</p>
            {if $files._rows.orgpropsfile.src}
			<p>
				<label for="author">Файл с реквизитами закачика:</label>
				<a target="_blank" href="{$files._rows.orgpropsfile.src}" style="color:red;">{$files._rows.orgpropsfile.name}</a>
				<a target="_blank" href="{$files._rows.orgpropsfile.src}" style="color:red;">[скачать]</a>
			</p>
            {/if}
			<p>
				<label for="phone">Телефон*</label>
				<input type="text" id="phone" name="phone" class="{$data.err_phone}" value="{$data.phone}"/>
				<br><span class="field_comment">Максимальная длина: 50 символов</span>
				{if $data.err_phone_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
				{if $data.err_phone_required}<br><span class="error">Обязательное поле не задано</span>{/if}
			</p>

            {if $data.add_phone}
			<p>
				<label for="add_phone">Дополнительный телефон</label>
				<input type="text" id="add_phone" name="add_phone" class="{$data.err_add_phone}" value="{$data.add_phone}"/>
				<br><span class="field_comment">Максимальная длина: 50 символов</span>
				{if $data.err_add_phone_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>
			{/if}

			<p>
				<label for="email">E-mail*</label>
				<input type="text" id="email" name="email" class="{$data.err_email}" value="{$data.email}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_email_email}<br><span class="error">Поле не соответствует формату e-mail someone@supermail.ru</span>{/if}
				{if $data.err_email_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
				{if $data.err_email_required}<br><span class="error">Обязательное поле не задано</span>{/if}
			</p>

			<p>
				<label for="description">Дополнительная информация</label>
				<textarea id="description" name="description" class="{$data.err_description}" style="width:650px; height:70px;">{$data.description}</textarea>
			</p>

		</div>
	</div>

	<p>
		<input type="submit" id="submit" name="submit" value="Сохранить"/>
		<input type="button" value="Список" onclick="window.location='/admin/order'"/>
	</p>
</form>

{if $data.couponData.coupon_id}
	<div class="box">
		<h3 style="color:red">Заказ оформлен с купоном на скидку!!!!</h3>
		<table style="width:97%;">
			<tr>
				<td>
					Код купона
				</td>
				<td>
					{$data.couponData.activation_code}
				</td>
			</tr>
			<tr>
				<td>
					Имя в купоне
				</td>
				<td>
					{$data.couponData.author}
				</td>
			</tr>
			<tr>
				<td>
					Email
				</td>
				<td>
					{$data.couponData.email}
				</td>
			</tr>
		</table>
	</div>
{/if}

{if count($data.products) > 0}
<div class="box">
	<h3>Содержимое заказа</h3>
	<table style="width:97%;">
		<tr>
			<td>
				Название
			</td>
			<td>
				Артикул
			</td>
			<td>
				Стоимость
			</td>
			<td>
				Количество
			</td>
			<td>
				Цена
			</td>
		</tr>
	{foreach from=$data.products item=item}
		<tr>
			<td>
				{if $item.group_id}
					<a target="_blank" href="{$catalog_uri_base}{$groups[$item.group_id].uri_base}/{$item.product_uri}{$url_suffix}">{$item.productName}</a>
				{else}
					{$item.productName}
				{/if}
			</td>
			<td>
				{$item.productCode}
			</td>
			<td>
				{$item.productPrice}
			</td>
			<td>
				{$item.quantity}
			</td>
			<td>
				{$item.subtotal}
			</td>
		</tr>
	{/foreach}
	</table>
</div>
{/if}

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
