<?php
ini_set("memory_limit","512M");
define('DOMAIN_NAME', 'http://atlets.ru');

require_once 'priceUpdater/DB.php';

$db = DB::instance();

$shop = '<?xml version="1.0" encoding="utf-8"?>
<yml_catalog date="' . date('Y-m-d H:i', time()) . '">
<shop>
  <name>Atlets</name>
  <company>Продажа лучшего спортивного питания</company>
  <url>http://atlets.ru/</url>

  {:$currencies}
  {:$categories}
  {:$offers}
</shop>
</yml_catalog>
';

$currencies = '
<currencies>
  <currency id="RUR" rate="1"/>
</currencies>
';

$categories = '
<categories>
{:$xml}
</categories>
';
$categoriesData = $db->select('SELECT * FROM `ml_catalog_groups` WHERE `level` > 0 ORDER BY `lft` ASC')->findAll();
$xml = '';
$lastParent = 0;
foreach ($categoriesData as $category) {
    if ($category->level == 1) {
        $lastParent = $category->id;
        $xml.=' <category id="' . $category->id . '">' . $category->title . '</category>
';
    } else {
        $xml.=' <category id="' . $category->id . '" parentId="' . $lastParent . '">' . $category->title . '</category>
';
    }
}

$categories = str_replace('{:$xml}', $xml, $categories);

$offers = '
<offers>
{:$xml}
</offers>
';

$xml = '';
$catalog = $db->select('SELECT `ml_catalog`.*, `ml_catalog_tastes`.*, `ml_catalog`.`id` as `id`, `ml_catalog_tastes`.`id` as `tasteId`, `ml_catalog`.`name` as `name`, `ml_catalog_tastes`.`name` as `taste_name`, `ml_catalog_group_contents`.`uri` as `categoryUri`, `ml_files`.`src` as `image` FROM `ml_catalog`
LEFT JOIN `ml_catalog_tastes` ON `ml_catalog`.`id` = `ml_catalog_tastes`.`productId`
LEFT JOIN `ml_catalog_group_contents` ON `ml_catalog`.`group_id` = `ml_catalog_group_contents`.`group_id`
LEFT JOIN `ml_files` ON `ml_catalog`.`id` = `ml_files`.`item_id`
WHERE `price` > 0 AND `ml_catalog_tastes`.`name` IS NOT NULL GROUP BY `ml_catalog_tastes`.`id`')->findAll();

$tmpCatalog = array();
foreach ($catalog as $product) {

        $name = ($product->taste_name == 'без вкуса') ? $product->name : $product->name . ' ' . $product->taste_name;
            $tmpCatalog['475' . $product->article] = array(
            'availaible' => ($product->availability > 0) ? 'true' : 'false',
            'name' => $name,
            'url' => DOMAIN_NAME . '/catalog/' . $product->categoryUri . '/' . $product->uri,
            'price' => $product->price,
            'currencyId' => 'RUR',
            'categoryId' => $product->group_id,
            'picture' => DOMAIN_NAME . $product->image,
            'rz_Active' => ($product->availability > 0) ? 'true' : 'false',
            'rz_Quantity' => $product->availability,
            'rz_Weight' => '',
            'rz_Length' => '',
            'rz_Width' => '',
            'rz_Height' => '',
            'rz_SupplierName' => '5lb',
            'rz_SupplierCode' => $product->article,
            'rz_SupplierPrice' => '',
            'description' => $product->description,
            'params' => array(
                0 => array(
                    'name' => 'Фасовка',
                    'taste' => $product->volume,
                ),
                1 => array(
                    'name' => 'Вкус',
                    'taste' => $product->taste_name,
                )
            ),
        );
}
foreach ($tmpCatalog as $id => $product) {
    $xml.= '<offer id="' . $id . '" availaible="' . $product['availaible'] . '">
<name>' . $product['name'] . '</name>
<url>' . $product['url'] . '</url>
<price>' . $product['price'] . '</price>
<currencyId>' . $product['currencyId'] . '</currencyId>
<categoryId>' . $product['categoryId'] . '</categoryId>
<picture>' . $product['picture'] . '</picture>
<rz_Active>' . $product['rz_Active'] . '</rz_Active>
<rz_Quantity>' . $product['rz_Quantity'] . '</rz_Quantity>
<rz_Weight>' . $product['rz_Weight'] . '</rz_Weight>
<rz_Length>' . $product['rz_Length'] . '</rz_Length>
<rz_Width>' . $product['rz_Width'] . '</rz_Width>
<rz_Height>' . $product['rz_Height'] . '</rz_Height>
<rz_SupplierName>' . $product['rz_SupplierName'] . '</rz_SupplierName>
<rz_SupplierCode>' . $product['rz_SupplierCode'] . '</rz_SupplierCode>
<rz_SupplierPrice>' . $product['rz_SupplierPrice'] . '</rz_SupplierPrice>
<description>' . htmlspecialchars($product['description']) . '</description>
';
    foreach ($product['params'] as $param) {
        if ($param['taste'] == 'без вкуса') continue;
        $xml.= '<param name="' . $param['name'] . '">' . $param['taste'] . '</param>
        ';
    }
 $xml.= '</offer>
';
}

$offers = str_replace('{:$xml}', $xml, $offers);

$shop = str_replace('{:$currencies}', $currencies, $shop);
$shop = str_replace('{:$categories}', $categories, $shop);
$shop = str_replace('{:$offers}', $offers, $shop);

file_put_contents('ritmOffers.xml', $shop);
