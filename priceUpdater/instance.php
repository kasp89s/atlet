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
$config = $db->select("SELECT * FROM  `ml_config` WHERE  `key` = 'retailPercent'")->find();

foreach ($categories as $manufacturerName => $category) {

//    if ($manufacturerName != 'Syntrax') {
//        continue;
//    }
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
                if (empty($product->id) === true) {
                    $productInfo = $parser->getProductInfo($connector->read('http://www.5lb.ru' . $volumeData['data']['link']));

                    $uri = explode('/', $volumeData['data']['link']);
                    $uri = str_replace('.html', '', end($uri));

                    // Исчим категорию!
                    $sql = "SELECT * FROM `ml_catalog_groups` WHERE `title` = '" . $productInfo['groupName'] . "' LIMIT 1";
                    $group = $db->select($sql)->find();
                    if (empty($group->id) === true) {
                        // Пока не научились добавлять категорию пропускаем ее.
                        continue;
                    }

                    // Исчим производителя!
                    $sql = "SELECT * FROM `ml_catalog_manufacturer` WHERE `name` = '" . trim($manufacturerName) . "' LIMIT 1";
                    $manufacturer = $db->select($sql)->find();
                    if (empty($manufacturer->id) === true) {
                        // Если нет производителя, добавим его херли...
                        $sql = "INSERT INTO `ml_catalog_manufacturer` (`id`, `name`, `description`) VALUES (NULL, '" . trim($manufacturerName) . "', NULL);";
                        $manufacturerId = $db->insert($sql);
                    } else {
                        $manufacturerId = $manufacturer->id;
                    }

                    if ((int) $config->value == 0) {
                        $retailPrice = $volumeData['data']['retailPrice'];
                    } else {
                        $retailPrice = ($volumeData['data']['retailPrice'] > 0) ? floor(($volumeData['data']['retailPrice'] + ($volumeData['data']['retailPrice'] / 100 * (int) $config->value)) / 10) * 10 : 0 ;
//                        if ($retailPrice > $volumeData['data']['retailPrice'] || $retailPrice < $volumeData['data']['tradePrice']) {
//                            $retailPrice = $volumeData['data']['retailPrice'];
//                        }
                    }
                    if (((int)$retailPrice - (int)$volumeData['data']['tradePrice']) <= 0) {
                        $retailPrice = 0;
                    }

                    $check = $db->select("SELECT `id` FROM `ml_catalog` WHERE `uri` = '{$uri}'")->find();
                    if (empty($check->id) === false) {
                        echo "Товара ({$uri}) уже существует \n";
                        continue;
                    }
//                    echo "\nТовар: {$uri} Розница: " .$volumeData['data']['retailPrice']. " Опт: " .$volumeData['data']['tradePrice']. " Добавляет: {$retailPrice} \n";
//                    continue;
                    $sql = "INSERT INTO `ml_catalog`
                    (`id`, `group_id`, `name`, `manufacturer_id`, `articles`,`code`, `1c_code`, `price`, `priceSupplier`, `oldprice`, `availability`, `description`, `uri`, `date_create`, `date_modified`, `sort_order`, `is_show_on_main_page`, `is_show_in_left_block`, `active`, `seo_name`, `seo_title`, `seo_keywords`, `seo_description`, `use_h1`, `concat_with_section_title`, `is_show_in_relative`, `relative_weight`, `relative_shows`, `total_shows`, `in_yml`, `in_sgs`, `in_action`, `is_use_auto_tags`, `volume`, `tastes`)
                    VALUES (NULL, '{$group->id}', '{$productName}', '{$manufacturerId}', '" .implode(',', $volumeData['data']['articles']). "','" . $volumeData['data']['articles'][0]. "',
                    '" . $volumeData['data']['articles'][0]. "', '0', '" . $retailPrice . "', '" . $volumeData['data']['tradePrice'] . "',
                    '', '" . addslashes($productInfo['description']) . "', '{$uri}', CURRENT_TIMESTAMP, '0000-00-00 00:00:00',
                    '500', '0', '0', '1', '', '{$productName}', '{$productName}', '{$productName}', '0', '0', '0', '0', '0', '0', '0', '', '0', '1', '{$volume}', '');";

                    $productId = $db->insert($sql);
                    downloadImage($productInfo, $productId, $uri);

                } else {
                    $productId = $product->id;
                }

                $allTastesCount = 0;
                foreach ($volumeData["tastes"] as $tasteName => $tasteData) {
                    // Исчим вкус если не найдем добавим, найдем обновим количество.
                    $sql = "SELECT * FROM `ml_catalog_tastes` WHERE `productId` = '{$productId}' AND `article` = '" . $tasteData['article'] . "' LIMIT 1";
                    $taste = $db->select($sql)->find();
                    if (empty($taste->id) === true) {
                        $sql = "INSERT INTO `ml_catalog_tastes` (`id`, `productId`, `name`, `article`, `count`) VALUES (NULL, '{$productId}', '{$tasteName}', '" . $tasteData['article'] . "', '" . $tasteData['count'] . "');";
                        $db->insert($sql);
                    } else {
                        // Обновим количество если нашли!
                        $sql = "UPDATE `ml_catalog_tastes` SET `count` = '" . $tasteData['count'] . "' WHERE `id` = '{$taste->id}';";
                        $db->execute($sql);
                    }
                    $allTastesCount+= (int) $tasteData['count'];
                }

                if ((int) $config->value == 0) {
                    // если не врублен коэфициент ставим розничную цену.
                    $price = ($volumeData['data']['retailPrice'] > 0) ?  $volumeData['data']['retailPrice'] : 0 ;
                } else {
                    // если коефициент врублен щетаем
                    $price = ($volumeData['data']['retailPrice'] > 0) ? floor(($volumeData['data']['retailPrice'] + ($volumeData['data']['retailPrice'] / 100 * (int) $config->value)) / 10) * 10 : 0;
                    // если то что мы насчитали окажеться дороже розницы или дешевле закупки то ставим розницу.
//                    if ($price > (float) $volumeData['data']['retailPrice'] || $price < (float) $volumeData['data']['tradePrice']) {
//                        $price = $volumeData['data']['retailPrice'];
//                    }
                }

                // если то что мы нащетали - закупка меньше 0 ставим 0.
//                if ($price - (int)$volumeData['data']['tradePrice'] <= 0) {
//                    $price = 0;
//                }

//                echo "Товар " . $product->name . "\n";
//                echo "retailPrice = " . $volumeData['data']['retailPrice'] . "\n";
//                echo "Пишу = " . $price . "\n";
//                echo "Количество = " . $allTastesCount . "\n";

                $price = '`priceSupplier` = \'' . $price . '\',';

                $sql = "UPDATE `ml_catalog` SET $price `articles` = '" .implode(',', $volumeData['data']['articles']). "', `oldprice` = '" . $volumeData['data']['tradePrice'] . "', `availability` = '" . $allTastesCount . "' WHERE `id` = '{$productId}';";
                $db->execute($sql);
            }

//            $price = ($productData['retailPrice'] > 0) ? '`price` = ' . $productData['retailPrice'] . ',' : '';
//            $sql.= "UPDATE `ml_catalog` SET `availability` = '" . $productData['count'] . "', $price `oldprice` = '" . $productData['tradePrice'] . "' WHERE `code` = '{$productCode}';" . "\n";
        }

    }
    echo "\n Выполняеться обновление количества и цен... \n";
//    file_put_contents('sql.txt', $sql, FILE_APPEND);
//    $db->execute($sql);
}

echo "\n Выполняеться обновление выгрузки каталога... \n";
require_once __DIR__ . '/../ritmCatalogUpdater.php';

function downloadImage($data, $productId, $uri)
{
    $preview = file_get_contents('http://www.5lb.ru' . $data['preview']);

    $image = file_get_contents('http://www.5lb.ru' . $data['image']);

    $imageName = end(explode('/', $data['image']));
    echo "\n Качаю {$data['image']} {$data['preview']}... \n";
    $ext = end(explode('.', $imageName));
    $directoryId = saveImageInFackingTable($productId, $imageName);

    mkdir(__DIR__ . '/../files/catalog/photo/' . $directoryId, 0777);
    mkdir(__DIR__ . '/../files/catalog/photo/' . $directoryId . '/0', 0777);
    mkdir(__DIR__ . '/../files/catalog/photo/' . $directoryId . '/1', 0777);
    mkdir(__DIR__ . '/../files/catalog/photo/' . $directoryId . '/2', 0777);

    $imageData = array(
        '/files/catalog/photo/' . $directoryId . '/0/' . $productId . $directoryId . '_' . $uri . '.' . $ext,
        '/files/catalog/photo/' . $directoryId . '/1/' . $productId . $directoryId . '_' . $uri . '.' . $ext,
        '/files/catalog/photo/' . $directoryId . '/2/' . $productId . $directoryId . '_' . $uri . '.' . $ext,
    );

    file_put_contents(__DIR__ . '/../files/catalog/photo/' . $directoryId . '/0/' . $productId . $directoryId . '_' . $uri . '.' . $ext, $image);
    file_put_contents(__DIR__ . '/../files/catalog/photo/' . $directoryId . '/1/' . $productId . $directoryId . '_' . $uri . '.' . $ext, $preview);
    file_put_contents(__DIR__ . '/../files/catalog/photo/' . $directoryId . '/2/' . $productId . $directoryId . '_' . $uri . '.' . $ext, $preview);

    updateImageInFackingTable($directoryId, $imageData);
}

function saveImageInFackingTable($productId, $image)
{
    $db = DB::instance();
    $sql = "
    INSERT INTO `ml_files`
    (`id`, `name`, `type`, `src`, `item_table`, `item_id`, `preview_1`, `preview_2`, `preview_3`, `input_name`, `file_size`, `image_width`, `image_height`, `image_orientation`)
    VALUES (NULL, '{$image}', 'image', '', 'catalog', '{$productId}', '', '', '', NULL, '0', '0', '0', 'notset');
    ";
    $imageId = $db->insert($sql);

    return $imageId;
}

function updateImageInFackingTable($directoryId, $imageData)
{
    $db = DB::instance();
    $sql = "UPDATE `ml_files` SET `src` = '" . $imageData[0] . "', `preview_1` = '{$imageData[1]}', `preview_2` = '{$imageData[2]}', `preview_3` = '{$imageData[2]}', `input_name` = 'image' WHERE `id` = '{$directoryId}'";

    $db->execute($sql);

}

?>
