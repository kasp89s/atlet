<?php
define('RITM_USERNAME', 'Market');
define('RITM_PASSWORD', '475002');

if ($_SERVER['PHP_AUTH_USER'] != RITM_USERNAME && $_SERVER['PHP_AUTH_PW'] != RITM_PASSWORD) {
    header("HTTP/1.0 401 Unauthorized");
    header("WWW-authenticate: basic realm=\"Orders\"");
    print ("Access denied. User name and password required.");
    exit;
}

function calculateMinPrice($price) {
    if ($price < 500) {
        return $price - 100;
    }

    if ($price > 500 && $price < 1000) {
		if ($price - 100 < 500) {
			return $price - 100;
		}
        return $price - 150;
    }

    if ($price > 1000 && $price < 2000) {
		if ($price - 150 < 1000) {
			return $price - 150;
		}
        return $price - 200;
    }

    if ($price > 2000 && $price < 3000) {
		if ($price - 200 < 2000) {
			return $price - 200;
		}
        return $price - 300;
    }

    if ($price > 3000) {
		if ($price - 300 < 3000) {
			return $price - 300;
		}
        return $price - 400;
    }
}

function applyDiscount($price, $manufacturerName)
{
	if ($manufacturerName == 'QNT') {
        $price = round(($price / 93) * 100);
    }
	
	return $price;
}

require_once 'DB.php';

$db = DB::instance();

if(isset($_POST['action']) && $_POST['action'] == 'price') {
	$db->update('ml_catalog', $_POST['id'], array('price' => $_POST['price']));

    echo json_encode(array('id' => $_POST['id'], 'price' => $_POST['price']));
    exit;
}

$products = $db->select("SELECT `c`.*, GROUP_CONCAT(`t`.`name` SEPARATOR ', ') as `taste`, `m`.`name` as `manufacturer` FROM `ml_catalog` `c` 
LEFT JOIN `ml_catalog_manufacturer` `m` ON `c`.`manufacturer_id` = `m`.`id`
LEFT JOIN `ml_catalog_tastes` `t` ON `c`.`id` = `t`.`productId`
WHERE `c`.`in_yml` = 1 AND `c`.`availability2` > 0
GROUP BY `c`.`id` 
ORDER BY `c`.`name`
")->findAll();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>level 99</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
</head>
<body>
<style>
td{
	max-width: 200px;
}
</style>

<div class="container">
<ul class="nav nav-tabs">
  <li role="presentation" class="active"><a href="#">Level 99</a></li>
  <li role="presentation"><a href="/priceUpdater/isg.php">ИСГ</a></li>
  <li role="presentation"><a href="/priceUpdater/import_v2.php">Минимальные цены</a></li>
</ul>

<h3>level 99</h3>
	<table class="table table-bordered">
        <tr>
            <th>Артикулы</th>
            <th>Производитель</th>
            <th>Наименование</th>
            <th>Фасовка</th>
            <th>Вкусы</th>
            <th>Количество</th>
            <th>Мин. Цена</th>
            <th>Цена продажи</th>
            <th></th>
        </tr>
    <? foreach($products as $product):?>
		<tr <? if (calculateMinPrice($product->price) > $product->price):?> style="background-color: #953b39" <?endif;?>>
			<td><?= str_replace(',', ', ', $product->articles)?></td>
			<td><?= $product->manufacturer?></td>
			<td><?= $product->name?></td>
			<td><?= $product->volume?></td>
			<td><?= $product->taste?></td>
			<td><?= $product->availability2?></td>
			<td><?= applyDiscount(calculateMinPrice($product->price), $product->manufacturer)?></td>
			<td><input type="text" id="<?= $product->id?>" name="price" placeholder="цена на сайте" value="<?= $product->price?>"/></td>
			<td><input type="button" class="btn btn-info btn-xs" value="Обновить" onclick="updatePrice('<?= $product->id?>')"></td>
		</tr>
	<? endforeach;?>
    </table>
	</div>
	<script type="text/javascript">
    function updatePrice(id) {
        var price = $('#' + id).val();
        $.post(
            '/priceUpdater/level.php',
            {action: 'price', id: id, price: price},
            function (data) {
                if (data.id != null){
					$.get('/yandexCatalogUpdater.php');
                    alert('Стоимость изменена!');
                }
            },
            'json'
        );
    }
	</script>
</body>
</html>
