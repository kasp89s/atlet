<?php
define('LOGIN_URL', 'http://www.5lb.ru/cgi-bin/mp/page.pl');
define('DOMAIN', 'http://www.5lb.ru');
define('BASKET_URL', 'http://www.5lb.ru/cgi-bin/mp/page.pl?m=shop');
define('LOGIN', 'seb.global@gmail.com');
define('PASSWORD', 'syntha8');
define('USER_ID', '1616045');

session_start();

require_once 'priceUpdater/DB.php';
require_once 'priceUpdater/Connector.php';
require_once 'priceUpdater/Parser.php';
$connector = new Connector();
$parser = new Parser();
$db = DB::instance();

if (isset($_POST['order'])) {
    $order = array();
    foreach ($_POST['order']['code'] as $key => $item) {
        if ($item != '' && $_POST['order']['count'][$key] != '') {
            $order[$item] = $_POST['order']['count'][$key];
        }
    }

$connector->read(BASKET_URL);

if ($parser->isLogin($startPage) === false) {
    $connector->login(LOGIN_URL, LOGIN, PASSWORD);
    $startPage = $connector->read(BASKET_URL);
}

$list = $parser->getBasketList($startPage);

if (empty($list) === false && count($list) > 0) {
    $parser->cleanBasket($list);
}

if (count($order) > 0) {
    $orders = $parser->addToBasket($order, USER_ID);

    $result = array();
    $products = array();
    foreach ($orders as $code => $check) {
        $result[$code] = $parser->getProductById($code);
        if ($check === true) {
            $products[$code] = array(
                'name' => $result[$code]['name'],
                'taste' => $result[$code]['taste'],
                'count' => $order[$code]
            );
        }

        $result[$code]['availability'] = $check;
    }
    if (count($products) > 0) {
        $products = json_encode($products);
        $ip = $_SERVER['REMOTE_ADDR'];
        $lastOrderId = $db->insert("INSERT INTO `ml_last_orders` (`id`, `products`, `orderNumber`, `send`, `date`, `IP`) VALUES (NULL, '{$products}', NULL, '0', CURRENT_TIMESTAMP, '{$ip}');");
        $_SESSION['lastOrderId'] = $lastOrderId;
    }
}

echo json_encode($result);
exit;
}

if (isset($_POST['sendOrder']) && $_POST['sendOrder'] == 1) {
    $response = $connector->post(DOMAIN . '/cgi-bin/mp/page.pl', array(
        'action' => 'gocart',
        'm' => 'shop',
        'region' => 'reg_moscow',
        'user_id' => USER_ID,
        'fio' => 'Рейченко Андрей Вадимович',
        'country_code' => '7',
        'area_code' => '919',
        'phone' => '7203585',
        'email' => 'seb.global@gmail.com',
        'payment_id' => '1',
        'self_deliv' => '1',
        'delivery_date' => date('d.m.Y', time()),
        'note' => 'Для привоза продукции на склад необходимо заранее (не менее чем за час) на адрес logist@ritm-z.com отправлять данные по ФИО водителя, марке и номеру машины для выписки пропуска на территорию склада.
Телефон склада для связи: 8-926-415-47-46
Адрес: Огородный проезд д.20 стр.23
Представляться как спортивное питание для ИП Краснов Дмитрий Алексеевич',
        'public_offer' => '1',
    ));

    if (stripos($response, 'Moved') !== false) {
        preg_match('|<a href="(.*)">|isU', $response, $matches);
        if (empty($matches[1]) === false) {
            $response = $connector->read($matches[1]);
        }
    }

   preg_match('|<p>Номер вашего заказа: (.*).</p>|isU', $response, $matches);

    if (empty($matches[0]) === false) {
        $orderNumber = $matches[1];
        $lastOrderId = $_SESSION['lastOrderId'];
        $db->execute("UPDATE `ml_last_orders` SET `orderNumber` = '{$orderNumber}', `send` = '1' WHERE `id` = '{$lastOrderId}';");
        echo json_encode(array('order' => $matches[0]));
        exit;
    }
}
