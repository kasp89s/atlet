<?php
/**
 * Стартовые данные.
 * fileName - имя входного файла.
 * available - доступно к показу (есть в ниличие).
 * notAvailable - на товары которых нет в наличие.
 *
 * Пример записи (0 => 'Привет мир!',) будет означать что в 1 столбец будет установлено значение Привет мир!.
 * Нумерация столбцов сдесь начинаеться с 0!!!
 *
 * На выходе парса в корне будет файл import_fileName
 **/
$bootstrap = array(
    'fileName' => 'obshhie_frazy_10206911.csv',
    'available' => array(
        16 => 'Принято к показу',
    ),
    'notAvailable' => array(
//        9 => '0,3',
//        14 => '0,3',
        16 => 'Остановлено',
    )
);

define('advertisementId', 9);
define('clickCost', 14);

require_once 'priceUpdater/DB.php';

$db = DB::instance();

$products = array();
$catalog = $db->select('SELECT `uri`, `availability` FROM `ml_catalog` WHERE 1;')->findAll();
foreach ($catalog as $item) {
    $products[$item->uri] = (int) $item->availability;
}

$newCsv = '';
$handle = fopen($bootstrap['fileName'], "r");

if ($handle) {
    $data = array();
    while (($buffer = fgets($handle)) !== false) {
        if (stripos($buffer, '"-"') !== false) {
            $buffer = explode('	', $buffer);
            if (count(explode('/', $buffer[12])) > 6) {
                $url = explode('/', str_replace('"', '', $buffer[12]));
                $url = $url[count($url) - 2];

                if ($products[$url] == 0) {
                    foreach($bootstrap['notAvailable'] as $key => $value) {
                        $buffer[$key] = $value;
                    }
                } else {
                    foreach($bootstrap['available'] as $key => $value) {
                        $buffer[$key] = $value;
                    }
                }
            }

            $buffer = implode('	', $buffer);
            $newCsv.= $buffer;
        } else {
            $newCsv.= $buffer;
        }
    }

    if (!feof($handle)) {
        throw new Exception('Error: unexpected fgets() fail');
    }
    fclose($handle);
}
file_put_contents('import_' . $bootstrap['fileName'], $newCsv);
