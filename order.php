<?php
define('RITM_USERNAME', 'Ritm-Z');
define('RITM_PASSWORD', 'RitM-cAtaLOg-469');

if ($_SERVER['PHP_AUTH_USER'] != RITM_USERNAME && $_SERVER['PHP_AUTH_PW'] != RITM_PASSWORD) {
    header("HTTP/1.0 401 Unauthorized");
    header("WWW-authenticate: basic realm=\"Offers\"");
    print ("Access denied. User name and password required.");
    exit;
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Заказ оформление</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme.blue.css">
</head>
<body>
<style>
    td{
        text-align: center;
    }
</style>
<div class="container">
<input type="hidden" value="1" id="number">
<form class="product-form">
<table class="product-table table" >
    <tr>
        <th></th>
        <th>Артикул поставщика</th>
        <th>Кол-во</th>
    </tr>
    <tr>
        <td>1.</td>
        <td><input class="form-control" type="text" name="order[code][]"></td>
        <td><input class="form-control" name="order[count][]"></td>
    </tr>
    <tr class="last">
        <td></td>
        <td><input class="btn" type="button" id="submit-check" value="Продолжить"></td>
        <td></td>
        <td><span id="add-product" style="cursor: pointer">+</span></td>
    </tr>
</table>
</form>
<div id="output-box">
    <? if (isset($_REQUEST['lastOrder'])):?>
        <?= $_REQUEST['lastOrder']?>
    <? endif;?>
</div>
<?php
require_once 'priceUpdater/DB.php';
$db = DB::instance();

$orders = $db->select("SELECT * FROM `ml_last_orders` WHERE `send` = 1 ORDER BY `date` DESC LIMIT 50")->findAll();
?>
<h2>Последние заказы.</h2>
<table class="product-table tablesorter-blue" border="1" cellspacing="0">
    <tr>
        <th>№</th>
        <th>Дата</th>
        <th>IP</th>
        <th>Заказ</th>
    </tr>
    <? if(count($orders) > 0):?>
        <?foreach ($orders as $order):?>
            <tr>
                <td><?= $order->orderNumber?></td>
                <td><?= $order->date?></td>
                <td>
                    <?= $order->ip?>
                </td>
                <td>
                    <table class="product-table">
                        <tr>
                            <th>Артикул</th>
                            <th>Название</th>
                            <th>Количество</th>
                        </tr>
                    <?php
                    $products = json_decode($order->products);
                    ?>
                    <? if(count($orders) > 0):?>
                        <? foreach ($products as $code => $product):?>
                            <tr>
                                <td><?= $code?></td>
                                <td><?= $product->name?></td>
                                <td><?= $product->count?></td>
                            </tr>
                        <? endforeach;?>
                    <? endif?>
                    </table>
                </td>
            </tr>
        <? endforeach;?>
    <? endif?>
</table>
</div>
</body>
</html>

        <script type="text/javascript">
            $('#add-product').click(
                    function() {
                        $('#number').val(parseInt($('#number').val()) + 1);
                        var row = '<tr>'
                                +'<td>' + (parseInt($('#number').val())) + '.</td>'
                                +'<td><input class="form-control" type="text" name="order[code][]"></td>'
                                +'<td><input class="form-control" type="text" name="order[count][]"></td>'
                                +'</tr>';
                        $('.last').before(row);
                    }
            );

            $('#submit-check').click(
                    function(){
                        $('#output-box').html('<p>Выполняеться обработка...</p>');
                        $.post('/createOrder.php', $('form.product-form').serialize(), function(json){
                            if(json != null) {
                                var translate = {true: 'Есть', false: 'Нету'}
                                var html = '<table class="table">'
                                        +'<tr>' +
                                        '<th>Артикул</th>' +
                                        '<th>Название</th>' +
                                        '<th>Вкус</th>' +
                                        '<th>Наличие</th>' +
                                        '</tr>';
                                for (var i in json){
                                    html+= '<tr>' +
                                            '<td>' + i + '</td>' +
                                            '<td>' + json[i].name + '</td>' +
                                            '<td>' + json[i].taste + '</td>' +
                                            '<td>' + translate[json[i].availability] + '</td>' +
                                            '</tr>'
                                }
                                html+= '<tr>' +
                                        '<td></td>' +
                                        '<td><input type="button" class="btn btn-success" id="submit-order" value="Передать заказ поставщику" onclick="sendOrder()"></td>' +
                                        '<td></td>' +
                                        '<td></td>' +
                                        '</tr>';
                                html+= '</table>';
                                $('#output-box').html(html);
                            } else {
                                $('#output-box').html('<p>Ошибка заполнения...</p>');
                            }
                        }, 'json');
                    }
            );

            function sendOrder() {
                if (confirm("Отправить заказ?")) {
                    $('#output-box').html('<p>Выполняеться обработка...</p>');
                    $.post('/createOrder.php', {sendOrder: 1}, function(json){
                    if (json != null) {
                        location.href = '/order.php?lastOrder=' + json.order;
//                        $('#output-box').html(json.order);
                    } else {
                        $('#output-box').html('<p>При отправке произошла ошибка...</p>');
                    }
                }, 'json');
                }
            }
        </script>
