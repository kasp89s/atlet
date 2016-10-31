<?php
define('RITM_USERNAME', 'Ritm-Z');
define('RITM_PASSWORD', 'RitM-oRdeRs-469');

if ($_SERVER['PHP_AUTH_USER'] != RITM_USERNAME && $_SERVER['PHP_AUTH_PW'] != RITM_PASSWORD) {
    header("HTTP/1.0 401 Unauthorized");
    header("WWW-authenticate: basic realm=\"Orders\"");
    print ("Access denied. User name and password required.");
    exit;
}

$errors = array();
if (empty($_REQUEST['b_date']) === true) {
    $errors[] = 'Не указана дата начала выгрузки';
}
if (empty($_REQUEST['e_date']) === true) {
    $errors[] = 'Не указана дата окончания выгрузки';
}

if (count($errors) > 0) {
    header('Content-Type: text/xml; charset=utf-8');
    $xml = '<?xml version="1.0" encoding="utf-8"?>
<errors>
';
    foreach ($errors as $error) {
        $xml.= ' <error>' . $error . '</error>
';
    }
    $xml.= '</errors>';
    echo $xml;
    exit;
}

require_once 'priceUpdater/DB.php';
$db = DB::instance();
header('Content-Type: text/xml; charset=utf-8');
$xml = '<?xml version="1.0" encoding="utf-8"?>
<orders>
';

$orders = $db->select("SELECT `ml_order`.*, `ml_order_products`.*, `ml_order_products`.`id` as `opId`, `ml_order_products`.`quantity` as `quantity`, `ml_order`.`id` as `id`, `ml_catalog`.`volume`, `ml_catalog`.`articles`, group_concat(DISTINCT `ml_catalog_tastes`.`name`) as `tastes` FROM `ml_order`
LEFT JOIN `ml_order_products` ON `ml_order`.`id` = `ml_order_products`.`orderID`
LEFT JOIN `ml_catalog` ON `ml_catalog`.`id` = `ml_order_products`.`productID`
LEFT JOIN `ml_catalog_tastes` ON `ml_catalog`.`id` = `ml_catalog_tastes`.`productId`
WHERE `ml_order`.`date_create` >= :dateStart AND `ml_order`.`date_create` <= :dateEnd GROUP BY `ml_order_products`.`id` ORDER BY `date_create` DESC", array(':dateStart' => $_REQUEST['b_date'], ':dateEnd' => $_REQUEST['e_date']))->findAll();

if (empty($orders) || !is_array($orders)) {
    $xml.= '</orders>';
    echo $xml;
}
$tmpOrders = array();
foreach ($orders as $order) {

    $articles = explode(',', $order->articles);
    $tastes = explode(',', $order->tastes);
    $option = array();
    foreach ($tastes as $key => $taste) {
        $option[$taste] = $articles[$key];
    }
    $taste = json_decode($order->options);
    if (isset($taste->taste) && empty($option[$taste->taste]) === false) {
        $article = $option[$taste->taste];
    } else {
        $article = end($option);
    }

    if (empty($tmpOrders[$order->id]) === true) {
        $tmpOrders[$order->id] = array(
            'c_name' => $order->author,
            'c_contacts' => $order->phone,
            'c_address' => $order->delivery_address,
//            'd_date' => date('Y-m-d', strtotime($order->date_create)),
//            'b_time' => date('H:i', strtotime($order->date_create)),
//            'e_time' => date('H:i', strtotime($order->date_create)),
            'd_date' => '',
            'b_time' => '',
            'e_time' => '',
            'incl_deliv_sum' => 0,
            'type_d' => $order->delivery,
            'e_mail' => $order->email,
            'descriptions' => $order->description,
            'items' => array(
                0 => array(
                    'id' => '475' . $article,
                    'name' => $order->productName,
                    'quantity' => $order->quantity,
                    'price' => $order->productPrice,
                    'chars' => array(
                        'article' => $article,
                        'taste' => isset($taste->taste) ? $taste->taste : null,
                        'volume' => isset($order->volume) ? $order->volume : null,
                    )
                )
            ),
    );
    } else {
        $tmpOrders[$order->id]['items'][] = array(
            'id' => '475' . $article,
            'name' => $order->productName,
            'quantity' => $order->quantity,
            'price' => $order->productPrice,
            'chars' => array(
                'article' => $article,
                'taste' => isset($taste->taste) ? $taste->taste : null,
                'volume' => isset($order->volume) ? $order->volume : null,
            )
        );
    }

}

foreach ($tmpOrders as $id => $order) {
    $price = 0;

    foreach ($order['items'] as $item) {
        $price+= (int) $item['price'];
    }
    if ($price < 3000) {
        $order['incl_deliv_sum'] = 300;
    }

 $xml.= '<order id="' . $id . '">
 <c_name>' . $order['c_name'] . '</c_name>
 <c_contacts>' . $order['c_contacts'] . '</c_contacts>
 <c_address>' . $order['c_address'] . '</c_address>
 <d_date>' . $order['d_date'] . '</d_date>
 <b_time>' . $order['b_time'] . '</b_time>
 <e_time>' . $order['e_time'] . '</e_time>
 <incl_deliv_sum>' . $order['incl_deliv_sum'] . '</incl_deliv_sum>
 <type_d>' . $order['type_d'] . '</type_d>
 <e_mail>' . $order['e_mail'] . '</e_mail>
 <descriptions>' . $order['descriptions'] . '</descriptions>
 <items>';
    foreach ($order['items'] as $item) {
        $xml.= '
 <item>
 <id>' . $item['id'] . '</id>
 <name>' . $item['name'] . '</name>
 <quantity>' . $item['quantity'] . '</quantity>
 <price>' . $item['price'] . '</price>';
        if ($item['chars']['taste'] != null || $item['chars']['volume'] != null) {
            $xml.= '
 <chars>';
            if ($item['chars']['taste'] != null) {
                $xml.= '<char name="Вкус" val="' . $item['chars']['taste'] . '" />';
            }
            if ($item['chars']['volume'] != null) {
                $xml.= ' <char name="Фасовка" val="' . $item['chars']['volume'] . '" />';
            }
            if ($item['chars']['article'] != null) {
                $xml.= ' <char name="Артикул поставщика" val="' . $item['chars']['article'] . '" />';
            }

  $xml.= '</chars>';
        }
        $xml.= '
 </item>';
    }

$xml.= '
 </items>
</order>
';
}
$xml.= '</orders>';
echo $xml;
