<?php
define('email', 'kasp89s@gmail.com');
define('password', 'rbk7kitpic');
define('loginUrl', 'https://login.vk.com/?act=login');

$photoId = 'photo4406103_369108725';

require_once 'Connector.php';
$connector = new Connector();

// Сюда передаем данные согласно документа по API
$response = $connector->get('balance', [
        'Username' => '1640',
        'SessionID' => '2f561cd0-00c5-431f-80e7-e804b6e28982',
    ]);
var_dump($response);
exit;
$response = post("https://test.megalot.emict.net/maxmoney/ru/getBalance",
array(
    'ClientID' => '1',
    'ClientPassword' => '1',
    'Username' => '1640',
    'SessionID' => '2f561cd0-00c5-431f-80e7-e804b6e28982',
)
);
function post($url, $postFields){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    // не проверять SSL сертификат
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
    // не проверять Host SSL сертификата
    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    // возвращать результат работы
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    $result=curl_exec($ch);

    curl_close($ch);

    return $result;
}
var_dump($response);
exit;
$page = $connector->read("http://vk.com/creepy_horrors");
file_put_contents('data.html', $page);
//var_dump($page);
exit;
$page = $connector->read("http://login.vk.com/?act=login&email=" . email . "&pass=" . password);
var_dump($page); exit;
preg_match('|<input type="hidden" name="ip_h" value="(.*)" />|isU', $page, $matches);
$ip_h = $matches[1];

$login = $connector->vkLogin(loginUrl, email, password, $ip_h);

var_dump($login);

