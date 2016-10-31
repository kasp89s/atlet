<?php

define('DOMAIN', 'http://www.level99.ru');
define('LOGIN_URL', 'http://www.level99.ru/ajax/login/');
define('CATEGORIES_URL', 'http://www.level99.ru/catalog/');
define('LOGIN', 'workcdn@gmail.com');
define('PASSWORD', 'YvFAc');
//define('USER_ID', '1768065');

require_once 'DB.php';
require_once 'Connector.php';
require_once 'Level99Logic.php';

$connector = new Connector();
$logic = new Level99Logic();

if ($logic->checkAuth($connector->read(DOMAIN)) === false) {
	echo "make autorization";
	$a = $connector->post(LOGIN_URL, array(
		'email' => LOGIN,
		'password' => PASSWORD,
	));
}

if ($logic->checkAuth($connector->read(DOMAIN)) === false) {
	die('autorization false :(');
}

$categories = $logic->getCategoryList($connector->read(CATEGORIES_URL));

foreach ($categories as $categoryName => $categoryUrl) {
	
	$categoryId = $logic->checkCategory($categoryName);
	
	if($categoryId === false) {
		echo "Warning: category {$categoryName} not found! \n";
		continue;
	}
	echo "
	/**********************************************/\n
	/*Загрузка категории ({{$categoryName}}) ...  */\n
	/**********************************************/\n
	";
	$items = $logic->getItemList($connector->read(DOMAIN . $categoryUrl));
	
	if ($items === false) continue;
	
	foreach ($items as $itemName => $itemUrl) {
		$itemInfo = $logic->getItemInfo($connector->read(DOMAIN . $itemUrl));

        $uri = str_replace(' ', '_', $itemInfo['name']);
        $uri = str_replace('&', '', $itemInfo['name']);
		$uri = strtolower(preg_replace('~[^a-zA-Z_]+~', "", $uri));

        $itemInfo['uri'] = $uri;
		//file_put_contents('dump.txt', var_export($itemInfo, true), FILE_APPEND | LOCK_EX);

		$action = $logic->getItemAction($itemInfo, $categoryId);

	}	
}

	echo "\n Выполняеться обновление выгрузки каталога... \n";
	require_once __DIR__ . '/../yandexCatalogUpdater.php';
