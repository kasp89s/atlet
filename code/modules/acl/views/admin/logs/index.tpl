<link rel="stylesheet" href="/libs/calendar/skins/aqua/theme.css" type="text/css">
<script type="text/javascript" src="/libs/calendar/calendar_stripped.js"></script>
<script type="text/javascript" src="/libs/calendar/lang/calendar-ru2-utf8.js"></script>
<script type="text/javascript" src="/libs/calendar/calendar-setup_stripped.js"></script>

<!--фильтр-->
<form action="" style="margin:0">
<p>
	<label for="user">Пользователь</label>
	<select name="user" id="user">
		<option value="0" {$main.user_sel_0}>Все</option>
			{foreach from=$main.user item=item}
			<option value="{$item.id}" {$item.selected}>{$item.name}</option>
			{/foreach}
	</select>
	
	<label for="section">Раздел</label>
	<select name="section" id="section">
		<option value="0" {$main.section_sel_0}>Все</option>
			{foreach from=$main.section item=item}
			<option value="{$item.id}" {$item.selected}>{$item.name}</option>
			{/foreach}
	</select>
	
	<label for="section">Дата</label>
	<input type="text" style="width: 70px;" value="{$main.date|date_format:"%d.%m.%Y"}" id="date" name="date" maxlength="10">
	<input type="button" alt="Календарь" class="ti_cal" id="trigger_date">
	
	<br><input type="submit" value=">">
</p>
</form>	
<!--/фильтр-->

{$main.footer}

<form action="/admin/roles/group" method="POST">
<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
        <td width="1%">№</td>
        <td nowrap>Раздел</td>
        <td nowrap>Операция</td>
        <td nowrap>{sort from="main" sort="user_username" name="Логин"}</td>
        <td nowrap>{sort from="main" sort="user_fio" name="ФИО"}</td>
        <td nowrap>{sort from="main" sort="self_date_create" name="Дата"}</td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	    {counter start=0 skip=1 print=false}
	    {foreach from=$main.rows name=rows item=item}
	    <tr class="row_p">
			<td>{counter}.</td>
			<td>{$item.section_name}</td>	
			<td>{$item.action}</td>	
			<td>{$item.user_username}</td>	
			<td>{$item.user_fio}</td>	
			<td>{$item.date_create|date_format:"%d.%m.%Y"}</td>	
	    </tr>
	    {/foreach} 
	</tbody>
</table>

{literal}
<script type="text/javascript"><!--
Calendar.setup({
	inputField : "date",
	ifFormat : "%d.%m.%Y",
	showsTime : false,
	button : "trigger_date",
	onClose : function(cal) { 
		document.getElementById('date').value = cal.date.print("%d.%m.%Y");
		cal.hide(); 
	}
});
//--></script>
{/literal}