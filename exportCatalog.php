<?php
ini_set("memory_limit","512M");

require_once 'priceUpdater/DB.php';

$db = DB::instance();
$catalog = $db->select('SELECT `ml_catalog`.*, `ml_catalog_tastes`.*, `ml_catalog`.`id` as `id`, `ml_catalog_tastes`.`id` as `tasteId`, `ml_catalog`.`name` as `name`, `ml_catalog_tastes`.`name` as `taste_name`, `ml_catalog_group_contents`.`uri` as `categoryUri`, `ml_catalog_groups`.`title` as `categoryTitle`, `ml_catalog_groups`.`level` as `categoryLevel`, `ml_catalog_manufacturer`.`name` as `manufacturer`, `ml_files`.`src` as `image`, `ml_catalog_groups`.`lft` FROM `ml_catalog`
LEFT JOIN `ml_catalog_tastes` ON `ml_catalog`.`id` = `ml_catalog_tastes`.`productId`
LEFT JOIN `ml_catalog_group_contents` ON `ml_catalog`.`group_id` = `ml_catalog_group_contents`.`group_id`
LEFT JOIN `ml_catalog_groups` ON `ml_catalog`.`group_id` = `ml_catalog_groups`.`id`
LEFT JOIN `ml_catalog_manufacturer` ON `ml_catalog`.`manufacturer_id` = `ml_catalog_manufacturer`.`id`
LEFT JOIN `ml_files` ON `ml_catalog`.`id` = `ml_files`.`item_id`
WHERE `price` > 0 AND `ml_catalog_tastes`.`name` IS NOT NULL GROUP BY `ml_catalog_tastes`.`id` ORDER BY `ml_catalog_groups`.`lft`')->findAll();

$csv = "артикул | общая группа товаров (например протеин) | производитель | наименование | фасовка | вкус | склад (есть/нет)\n";

foreach ($catalog as $product) {
    if ($product->categoryLevel == 1) {
        $lastParent = $product->categoryTitle;
    }
    $availaible = ($product->availability > 0) ? 'есть' : 'нет';
    $csv.= "{$product->article}|{$lastParent}|{$product->manufacturer}|{$product->name}|{$product->volume}|{$product->taste_name}|{$availaible}\n";
}

file_put_contents('catalog.csv', $csv); exit;
