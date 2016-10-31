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

    //if ($manufacturerName != 'Sculpt') {
    //    continue;
    //}
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
                if ($volumeData['data']['tradePrice'] == 0) continue;
                echo "\n Обработка товара {$productName}: \n";
                // Исчим карточку фасофки товара!
                $sql = "SELECT * FROM `ml_catalog` WHERE `code` IN (" . implode(',', $volumeData['data']['articles']). ") LIMIT 1;";
                $product = $db->select($sql)->find();

                if (empty($product->id) === false && $product->availability2 > 0) {
                    echo "\n Товар {$productName} уже в наличие! \n";
                    continue;
                }
                // Добавляем карточку товара если нет фасофки!
                if (empty($product->id) === true) {
                    echo "1. Товар новый \n";
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

                    $retailPrice = $volumeData['data']['retailPrice'];
//                    $volumeData['data']['tradePrice']
                    $check = $db->select("SELECT `id` FROM `ml_catalog` WHERE `uri` = '{$uri}'")->find();
                    if (empty($check->id) === false) {
                        echo "Товар ({$uri}) уже существует \n";
                        continue;
                    }

                    $productId = $db->create('ml_catalog',
                        array(
                            'group_id' => $group->id,
                            'name' => $productName,
                            'manufacturer_id' => $manufacturerId,
                            'articles' => implode(',', $volumeData['data']['articles']),
                            'code' => $volumeData['data']['articles'][0],
                            '1c_code' => $volumeData['data']['articles'][0],
                            'price' => getActualPrice($volumeData['data']['retailPrice']),
                            'priceSupplier' => 0,
                            'oldprice' => 0,
                            'availability' => 0,
                            'availability2' => 0,
                            'description' => $productInfo['description'],
                            'uri' => $uri,
                            'date_modified' => '0000-00-00 00:00:00',
                            'sort_order' => '500',
                            'active' => '1',
                            'seo_title' => $productName,
                            'seo_keywords' => $productName,
                            'seo_description' => $productName,
                            'volume' => $volume,
                            'is_use_auto_tags' => 1,
                            'in_isg' => 0,
                            'in_5lb' => 1,
                            'in_yml' => 0,
                        )
                    );
                    downloadImage($productInfo, $productId, $uri);

                } else {
                    $productId = $product->id;
                    echo "1. Товар существует \n";
                }

                $allTastesCount = 0;
                foreach ($volumeData["tastes"] as $tasteName => $tasteData) {
                    echo "2. Вкус $tasteName\n";
                    if($tasteData['count'] < 10) continue;
                    echo " > 10\n";
                    // Исчим вкус если не найдем добавим, найдем обновим количество.
                    $sql = "SELECT * FROM `ml_catalog_tastes` WHERE `productId` = '{$productId}' AND `article` = '" . $tasteData['article'] . "' LIMIT 1";
                    $taste = $db->select($sql)->find();
                    if (empty($taste->id) === true) {
                        $sql = "INSERT INTO `ml_catalog_tastes` (`id`, `productId`, `name`, `article`, `count2`) VALUES (NULL, '{$productId}', '{$tasteName}', '" . $tasteData['article'] . "', '" . $tasteData['count'] . "');";
                        $db->insert($sql);
                    } else {
                        // Обновим количество если нашли!
                        $sql = "UPDATE `ml_catalog_tastes` SET `count2` = '" . $tasteData['count'] . "' WHERE `id` = '{$taste->id}';";
                        $db->execute($sql);
                    }
                    $allTastesCount+= (int) $tasteData['count'];
                }
                if ($allTastesCount >= 10) {
                    $price = '`price` = \'' . getActualPrice($volumeData['data']['retailPrice']) . '\',';

                    $sql = "UPDATE `ml_catalog` SET $price `articles` = '" .implode(',', $volumeData['data']['articles']). "', `oldprice` = '" . $volumeData['data']['tradePrice'] . "', `availability2` = '" . $allTastesCount . "', `in_5lb` = 1, `in_isg` = 0, `in_yml` = 0 WHERE `id` = '{$productId}';";
                    echo "3. Выполнен запрос $sql на обновление товара \n";
                    $db->execute($sql);
                }
            }

//            $price = ($productData['retailPrice'] > 0) ? '`price` = ' . $productData['retailPrice'] . ',' : '';
//            $sql.= "UPDATE `ml_catalog` SET `availability` = '" . $productData['count'] . "', $price `oldprice` = '" . $productData['tradePrice'] . "' WHERE `code` = '{$productCode}';" . "\n";
        }

    } else {
        echo "Не найдены товары в категории... \n";
    }
    echo "\n Выполняеться обновление количества и цен... \n";
    file_put_contents('sql.txt', $sql, FILE_APPEND);
    $db->execute($sql);
}

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

/**
 * Возвращает цену с наценкой
 */
function getActualPrice($price)
{
	$buy = $price - ($price * 0.15);
	if ($buy < 500) {
		
        return ceil(($buy + 100) / 10) * 10;
    } else {
		return ceil(($buy * 1.1) / 10) * 10;
	}

    return $price;
	/*
    $price = round($price);
    if ($price < 500) {
        return $price + 100;
    }

    if ($price > 500 && $price < 1000) {
        return $price + 150;
    }

    if ($price > 1000 && $price < 2000) {
        return $price + 200;
    }

    if ($price > 2000 && $price < 3000) {
        return $price + 300;
    }

    if ($price > 3000) {
        return $price + 400;
    }
	*/
}
?>
