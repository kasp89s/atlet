<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Поступил новый заказ №{$data.id}</title>
 </head>
 <body>

Поступил новый заказ №{$data.id}
<br />
<br />
Содержимое заказа:
<br /><br />
<table>
	<tr>
		<td>
			Контактное лицо:
		</td>
		<td>
			{$data.author}
		</td>
	</tr>
	<tr>
		<td>
			Вид оплаты:
		</td>
		<td>
			{$data.payment[$data.payment_sel_indx].name}
		</td>
	</tr>
	<tr>
		<td>
			Телефон:
		</td>
		<td>
			{$data.phone}
		</td>
	</tr>
	<tr>
		<td>
			Email:
		</td>
		<td>
			{$data.email}
		</td>
	</tr>
	<tr>
		<td>
			Дополнительная информация:
		</td>
		<td>
			{$data.description}
		</td>
	</tr>
	<tr>
		<td>
			Адрес доставки:
		</td>
		<td>
			{$data.delivery_adress}
		</td>
	</tr>
</table>

<h3>Содержимое заказа</h3>
<table border="1">
	<tr>
		<td>
			Название
		</td>
		<td>
			Артикул
		</td>
		<td>
			Цена
		</td>
		<td>
			Количество
		</td>
		<td>
			Стоимость
		</td>
	</tr>
{foreach from=$data.products item=item}
	<tr>
		<td>
			{$item.name}
		</td>
		<td>
			{$item.code}
		</td>
		<td>
			{$item.price}
		</td>
		<td>
			{$item.quantity}
		</td>
		<td>
			{$item.subtotal}
		</td>
	</tr>
{/foreach}
	<tr>
		<td style="text-align:right;" colapsn=5>
			Итого:
		</td>
		<td colspan=4>
			{$data.subtotal} руб.
		</td>
	</tr>
</table>
</body>
</html>