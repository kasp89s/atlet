<?php
require_once 'DB.php';

$db = DB::instance();
$sql = "SELECT `ml_catalog`.`name`, `ml_catalog`.`id`, `ml_catalog`.`description`, `ml_catalog_manufacturer`.`name` as `mName` FROM `ml_catalog`
LEFT JOIN `ml_catalog_manufacturer` ON `ml_catalog_manufacturer`.`id` = `ml_catalog`.`manufacturer_id`
GROUP BY `ml_catalog`.`name` ORDER BY `ml_catalog`.`id` ASC;";
$products = $db->select($sql)->findAll();
$csv = '';
foreach ($products as $product) {
    $description = trim(str_replace("
", '', $product->description));
    $description = str_replace("\n\r", "", $description);
    $description = str_replace("\n", "", $description);
    $description = str_replace("\r", "", $description);
    $csv.= "{$product->id}~{$product->name}~{$product->mName}~{$description}\n";
}

file_put_contents(__DIR__ . '/../unique.csv', $csv);