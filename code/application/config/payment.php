<?php defined('SYSPATH') OR die('No direct access allowed.');


$config['robokassa'] = array (
	/**
	 * URL сервиса
	 */
	'url' => 'https://merchant.roboxchange.com/Index.aspx',

	/**
	 * Логин
	 */
	'login' => 'IT777',

	/**
	 * Пароль #1: [используется интерфейсом инициализации оплаты]
	 */
	'password1' => 'jhk78hfgf',

	/**
	 * Пароль #2: [используется интерфейсом оповещения о платеже, XML-интерфейсах]
	 */
	'password2' => 'vskd75sdj',

	/**
	 * Название услуги
	 */
	'description' => 'Оплата заказа в магазине Intimel.ru',
);
