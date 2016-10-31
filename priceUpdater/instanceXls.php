<?php
require_once "ParserExcel.php";
require_once 'DB.php';
$db = DB::instance();
//$config = $db->select("SELECT * FROM  `ml_config` WHERE  `key` = 'retailPercent'")->find();
$pars = new ParserExcel('upload/report-Stock.xls');
$products = $pars->getArray3(); // возвращает массив

//echo "Начинаю обновление: процент наценки {$config->value}% от закупки \n";
echo "Начинаю обновление: процент наценки % от закупки \n";
$db->execute("UPDATE `ml_catalog` SET  `in_yml` = '0' WHERE 1;");
$db->execute("UPDATE `ml_catalog` SET  `in_isg` = '0' WHERE 1;");
$db->execute("UPDATE `ml_catalog` SET  `in_5lb` = '0' WHERE 1;");
$db->execute("UPDATE `ml_catalog` SET  `availability2` = '0' WHERE 1;");
$db->execute("UPDATE `ml_catalog_tastes` SET `count2` = '0' WHERE 1;");


foreach($products as $product) {
    $taste = $db->select("SELECT * FROM  `ml_catalog_tastes` WHERE  `article` = '". $product['article'] ."'")->find();

    if (!empty($taste->productId)) {
        echo "Обновление: " . $product['name'] . " \n";
        $sql = "UPDATE `ml_catalog_tastes` SET `count2` = '" . $product['count'] . "' WHERE `id` = " . $taste->id . ";";
        $db->execute($sql);
        $sql = "UPDATE `ml_catalog` SET `availability2` = `availability2` + " . $product['count'] . ", `price` = '" . $product['cost'] . "' WHERE `id` = " . $taste->productId . ";";
        $db->execute($sql);
    } else {
        echo "товар: 475" . $product['article'] . ' ' .  $product['name'] . " не найден \n";
    }
}
