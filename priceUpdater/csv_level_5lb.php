<?php
define('DOMAIN', 'http://www.5lb.ru');
define('LOGIN_URL', 'http://www.5lb.ru/cgi-bin/mp/page.pl');
define('CATEGORIES_URL', 'http://www.5lb.ru/cgi-bin/mp/page.pl?m=cabinet&p=opt');
define('LOGIN', 'seb.global@gmail.com');
define('PASSWORD', 'syntha8');
define('USER_ID', '1768065');
//define('LOGIN', 'seb.global@gmail.com');
//define('PASSWORD', 'syntha8');
//define('USER_ID', '1616045');

require_once 'DB.php';
require_once 'Connector.php';
require_once 'Parser.php';

file_put_contents('sql.txt', '');
$connector = new Connector();
$db = DB::instance();
$parser = new Parser();

$startPage = $connector->read(CATEGORIES_URL);
$categories = $parser->getCategoryList($startPage);

if ($categories === false) {
    $connector->login(LOGIN_URL, LOGIN, PASSWORD);
    $startPage = $connector->read(CATEGORIES_URL);

    $categories = $parser->getCategoryList($startPage);
}

$csv = '';
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
                foreach ($volumeData["tastes"] as $tasteName => $tasteData) {
                    $t = $db->select("SELECT * FROM  `ml_catalog_tastes` WHERE  `article` = '" . $tasteData['article'] . "'")->find();
                    $p = $db->select("SELECT * FROM  `ml_catalog` WHERE `id` = '" . $t->productId . "' AND `in_yml` = 1")->find();
                    if (empty($p->id) === false) {
                        $csv.= $tasteData['article'] . ';' . $manufacturerName . ';' . $productName . ';' . $volume . ';' . $tasteName . "\n";
                    }
                }
            }
        }

    } else {
        echo "Не найдены товары в категории... \n";
    }
    //echo "\n Выполняеться обновление количества и цен... \n";

    //$db->execute($sql);
}
file_put_contents('csv.txt', $csv);
?>
