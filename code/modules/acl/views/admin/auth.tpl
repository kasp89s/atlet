<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="/css/admin.css" type="text/css">
<title>Авторизация</title>
</head>

<body bgcolor="#F3F3F3" text="#000000" link="#797979" vlink="#797979" alink="#797979" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table border=0 cellpadding=0 cellspacing=5 width="100%" style="border-bottom: 1px solid #3366cc; ">
	<tr> 
		<td width=50%><b>Добрый день!</b></td>
		<td width=50% align="right"></td>
	</tr>
</table>

<form action="" method="POST">
	<table style="width:250px; padding-top:100px" align="center">
		<tr>
			<td>
			<fieldset>
				<legend><b>Авторизация</b></legend>
				<table cellspacing="1">
					<tr>
						<td width="50%"><b>Логин</b></td>
						<td width="50%"><input type="text" name="login" maxlength="255" size="25" value="{$data.login}"></td>
					</tr>
					<tr>
						<td width="50%"><b>Пароль</b></td>
						<td width="50%"><input type="password" name="password" maxlength="255" size="25"></td>
					</tr>
					<tr>
						<td colspan="2" align="right"><input type="submit" value="Войти"></td>
					</tr>
				</table>
			</fieldset>
			{if $data.err_validate || $data.err_login}
			<span class="c4" style="font-weight: bold"><br>Логин/Пароль неверны</span>
			{/if}
			</td>
		</tr>
	</table>
</form>

<table width=100% border=0 cellspacing=0 cellpadding=5>
	<tr>
		<td align=center>{$content}</td>
	</td>
</table>

</body>
</html>
