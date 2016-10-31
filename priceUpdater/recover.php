<?php

require_once 'DB.php';
$db = DB::instance();

$products = $db->select("SELECT `id`, `price`, `oldprice`, `name` FROM  `ml_catalog` WHERE  `availability2` > 0")->findAll();

foreach($products as $product)
{
	$dump = $db->select("SELECT `id`, `price`, `oldprice`, `name` FROM  `ml_catalog_dump` WHERE `id` = {$product->id}")->find();
	if ($product->name == $dump->name) {
		$sql = "UPDATE `ml_catalog` SET `price` = '{$dump->price}', `oldprice` = '{$dump->oldprice}' WHERE `id` = '{$product->id}';";
        $db->execute($sql);
		echo $product->name . ' set price ' . $dump->price . "\n";
	}
		//echo $product->name . '=>' . $dump->name . "\n";
}
//var_dump($dump);