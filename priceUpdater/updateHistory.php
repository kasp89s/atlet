<?php
define('DOMAIN', 'http://www.5lb.ru');
define('LOGIN_URL', 'http://www.5lb.ru/cgi-bin/mp/page.pl');
define('CATEGORIES_URL', 'http://www.5lb.ru/cgi-bin/mp/page.pl?m=cabinet&p=opt');
define('LOGIN', 'workcdn@gmail.com');
define('PASSWORD', '7Fgd8IkaZ');
define('USER_ID', '1768065');
//define('LOGIN', 'seb.global@gmail.com');
//define('PASSWORD', 'syntha8');
//define('USER_ID', '1768065');

require_once 'DB.php';
require_once 'Connector.php';
require_once 'Parser.php';

$connector = new Connector();
$db = DB::instance();
$parser = new Parser(false);
$date = date('Y-m-d 00:00:00', time());
$startPage = $connector->read(CATEGORIES_URL);
$categories = $parser->getCategoryList($startPage);

if ($categories === false) {
    $connector->login(LOGIN_URL, LOGIN, PASSWORD);
    $startPage = $connector->read(CATEGORIES_URL);

    $categories = $parser->getCategoryList($startPage);
}

foreach ($categories as $manufacturerName => $category) {

    echo 'Обработка производителя ' . $manufacturerName . "\n";
    $sql = '';
    $productCount = $parser->getProductsCount($connector->read(DOMAIN . $category));

    if (is_array($productCount) && count($productCount) > 0)
    {
        foreach ($productCount as $productName => $productData) {

            if (empty($productData["volumes"]) || !is_array($productData["volumes"])) {
                continue;
            }

            foreach ($productData["volumes"] as $volume => $volumeData) {
                // Исчим карточку фасофки товара!
                $sql = "SELECT * FROM `ml_catalog` WHERE `code` IN (" . implode(',', $volumeData['data']['articles']). ") LIMIT 1;";
                $product = $db->select($sql)->find();

                // Добавляем карточку товара если нет фасофки!
                if (empty($product->id) === false) {
//                    $productInfo = $parser->getProductInfo($connector->read('http://www.5lb.ru' . $volumeData['data']['link']));
                    foreach ($volumeData["tastes"] as $tasteName => $tasteData) {
                        updateHistory($tasteData['article'], $tasteData['count'], $date);
                    }
                }
            }
        }
    }
}

/**
 * Обновляет историю по вкусу.
 *
 * @param $article
 * @param $count
 */
function updateHistory($article, $count, $date)
{
    $db = DB::instance();
//    $count = ($count > 0) ? $count + rand(1, 3) : 0;

    $find = "SELECT * FROM `ml_5lb_history` WHERE `article` = '{$article}' AND `date` = '{$date}';";
    $historyRecord = $db->select($find)->find();

    if (empty($historyRecord) === true) {
        $db->insert("INSERT INTO `ml_5lb_history` (
`id` ,
`article` ,
`date` ,
`count`
)
VALUES (
NULL , '{$article}' ,  '{$date}', '{$count}'
);");
    }
}
?>
