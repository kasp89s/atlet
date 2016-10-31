<?php
session_start();
$_SESSION['upload_file'];
define('RITM_USERNAME', 'Market');
define('RITM_PASSWORD', '475002');

if ($_SERVER['PHP_AUTH_USER'] != RITM_USERNAME && $_SERVER['PHP_AUTH_PW'] != RITM_PASSWORD) {
    header("HTTP/1.0 401 Unauthorized");
    header("WWW-authenticate: basic realm=\"Orders\"");
    print ("Access denied. User name and password required.");
    exit;
}
require_once "ParserExcel.php";
require_once 'DB.php';

$lastUploadFile = false;
if (is_file(__DIR__ . '/tmp/lastImportv2File.xls')) {
    $lastUploadFile = 'lastImportv2File.xls';
}

if(isset($_POST['action']) && $_POST['action'] == 'refresh') {
    if(is_file(__DIR__ . '/tmp/lastImportv2File.xls')) {
        unlink(__DIR__ . '/tmp/lastImportv2File.xls');
    }
    exit;
}

if(isset($_POST['action']) && $_POST['action'] == 'price') {
    $db = DB::instance();
    $taste = $db->select("SELECT * FROM `ml_catalog_tastes` WHERE `article` = '". $_POST['article'] ."'")->find();
    $sql = "UPDATE `ml_catalog` SET `price` = '" . $_POST['price'] . "' WHERE `id` = " . $taste->productId . ";";
    $db->execute($sql);

    echo json_encode(array('article' => $_POST['article'], 'price' => $_POST['price']));
    exit;
}

if(isset($_POST['action']) && $_POST['action'] == 'count') {
    $db = DB::instance();
    $pars = new ParserExcel(__DIR__ . '/tmp/' . $_POST['fileName']);
    $products = $pars->getArray3();

    $errors = "\n";
    $db->execute("UPDATE `ml_catalog` SET  `availability2` = '0' WHERE 1;");
    $db->execute("UPDATE `ml_catalog_tastes` SET `count2` = '0' WHERE 1;");

    foreach($products as $product) {
        $taste = $db->select("SELECT * FROM  `ml_catalog_tastes` WHERE  `article` = '". $product['article'] ."'")->find();

        if (!empty($taste->productId)) {
            $sql = "UPDATE `ml_catalog_tastes` SET `count2` = '" . $product['count'] . "' WHERE `id` = " . $taste->id . ";";
            $db->execute($sql);
			$sql = "UPDATE `ml_catalog` SET `availability2` = `availability2` + " . $product['count'] . " WHERE `id` = " . $taste->productId . ";";
            $db->execute($sql);
        } else {
            $errors.= "товар: 475" . $product['article'] . ' ' .  $product['name'] . " не найден \n";
        }
    }
    echo json_encode(array(
            'complete' => 1,
            'errors' => $errors
        ));
    exit;
}

if(isset($_POST['action']) && $_POST['action'] == 'all') {
    $db = DB::instance();
    $pars = new ParserExcel(__DIR__ . '/tmp/' . $_POST['fileName']);
    $products = $pars->getArray3();

    $errors = "\n";
    $db->execute("UPDATE `ml_catalog` SET  `in_yml` = '0' WHERE 1;");
    $db->execute("UPDATE `ml_catalog` SET  `in_isg` = '0' WHERE 1;");
    $db->execute("UPDATE `ml_catalog` SET  `in_5lb` = '0' WHERE 1;");
    $db->execute("UPDATE `ml_catalog` SET  `availability2` = '0' WHERE 1;");
    $db->execute("UPDATE `ml_catalog_tastes` SET `count2` = '0' WHERE 1;");

    foreach($products as $product) {

        $minPrice = calculateMinPrice($product['cost'], $product['sell']);

        $taste = $db->select("SELECT * FROM  `ml_catalog_tastes` WHERE  `article` = '". $product['article'] ."'")->find();

        if (!empty($taste->productId)) {
            $sql = "UPDATE `ml_catalog_tastes` SET `count2` = '" . $product['count'] . "' WHERE `id` = " . $taste->id . ";";
            $db->execute($sql);
			$sql = "UPDATE `ml_catalog` SET `availability2` = `availability2` + " . $product['count'] . ", `price` = '" . $minPrice . "' WHERE `id` = " . $taste->productId . ";";
            $db->execute($sql);
        } else {
            $errors.= "товар: 475" . $product['article'] . ' ' .  $product['name'] . " не найден \n";
        }
    }
    echo json_encode(array(
            'complete' => 1,
            'errors' => $errors
        ));
    exit;
}

if(
isset($_FILES['xls']['name']) &&
isset($_FILES['xls']['name']) != '' &&
isset($_FILES['xls']['name']) == 'application/vnd.ms-excel'
) {
    $db = DB::instance();
    $pars = new ParserExcel($_FILES['xls']['tmp_name']);

    if (!is_dir( __DIR__ . '/tmp')) {
        mkdir(__DIR__ . '/tmp');
    }
    copy($_FILES['xls']['tmp_name'], __DIR__ . '/tmp/lastImportv2File.xls');
    $products = $pars->getArray3();
} elseif(isset($lastUploadFile) && $lastUploadFile != '' && is_file(__DIR__ . '/tmp/' . $lastUploadFile)) {
    $db = DB::instance();
    $pars = new ParserExcel( __DIR__ . '/tmp/' . $lastUploadFile);
    $products = $pars->getArray3();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Загрузка склада</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
</head>
<body>
<div class="container">
<ul class="nav nav-tabs">
  <li role="presentation"><a href="/priceUpdater/level.php">Level 99</a></li>
    <li role="presentation"><a href="/priceUpdater/isg.php">ИСГ</a></li>
  <li role="presentation" class="active"><a href="#">Минимальные цены</a></li>
    <li role="presentation"><a href="/priceUpdater/direct.php">Директ</a></li>
</ul>
<h3>Минимальные цены</h3>
<? if(isset($products) && is_array($products) && sizeof($products) > 0):?>
    <form method="post" enctype="multipart/form-data" class="form-inline" style="margin: 10px;">
	<div class="form-group">
        <input type="button" class="btn btn-primary" value="Обновить количество" onclick="updateCount('lastImportv2File.xls')"/>
	</div>
	<div class="form-group">
		<input type="button" class="btn btn-success" value="Импорт файла" onclick="updateAll('lastImportv2File.xls')"/>
	</div>
        <a href="javascript: void(0);" class="btn btn-warning" onclick="refresh()">Вернуться к загрузке файла</a>
    </form>
    <table class="table table-bordered">
        <tr>
            <th>Артикул</th>
            <th>Производитель</th>
            <th>Наименование</th>
            <th>Фасовка</th>
            <th>Вкус</th>
            <th>Количество</th>
            <th>Мин. Цена</th>
<!--            <th>Себестоимость</th>-->
            <th>Цена продажи</th>
            <th></th>
        </tr>
    <? foreach($products as $product):?>
        <?php
        $taste = $db->select("
            SELECT `ml_catalog`.`price`, `ml_catalog_tastes`.`count2` FROM `ml_catalog_tastes`
            LEFT JOIN `ml_catalog` ON `ml_catalog`.`id` = `ml_catalog_tastes`.`productId`
            WHERE  `ml_catalog_tastes`.`article` = '". $product['article'] ."'
            ")->find();
        /* Недостающий параметр – минимальная цена – должен формироваться по правилу:
                Себестоимость до 500 руб; мин цена  = продажной цене.
                        Себ. от 500 до 1000; мин цена  = себ+150 руб
                Себ. от 1000 до 2000; мин цена = себ+200 руб
                Себ. от 2000 до 3000; мин цена = себ+300 руб
                Себ. от 3000 до 5000 и далее; мин цена=себ+400 руб.*/
        $a = explode(',', $product['name']);

        if(is_numeric(preg_replace('~[^0-9]+~', '', $a[2]))){
            $volume = $a[2];
            $t = isset($a[3]) ? $a[3] : '';
        } else {
            $volume = '';
            $t = $a[2];
        }
        ?>
        <tr style="border-bottom: 1px solid #111; <? if ((int) calculateMinPrice($product['cost'], $product['sell']) > (int) $taste->price):?>background-color: #953b39 <?endif;?>">
            <td><?= $product['article']?></td>
            <td><?= isset($a[0]) ? $a[0] : ''?></td>
            <td><?= isset($a[1]) ? $a[1] : ''?></td>
            <td><?= $volume?></td>
            <td><?= $t?></td>
            <td><?= $product['count']?></td>
            <td><?= round(calculateMinPrice($product['cost'], $product['sell']))?></td>
            <td><input type="text" id="<?= $product['article']?>" name="price" placeholder="цена на сайте" value="<?= $taste->price?>"/></td>
            <td><input type="button" class="btn btn-info btn-xs" value="Обновить" onclick="updatePrice('<?= $product['article']?>')"></td>
<!--            <td>--><?//= $product['cost']?><!--</td>-->
<!--            <td>--><?//= $product['sell']?><!--</td>-->
        </tr>
    <? endforeach;?>
    </table>
<? else:?>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="xls" />
        &nbsp;&nbsp;&nbsp;
        <input type="submit" value="Загрузить" />
    </form>
<? endif;?>
<script type="text/javascript">
    function updatePrice(article) {
        var price = $('#' + article).val();
        $.post(
            '/priceUpdater/import_v2.php',
            {action: 'price', article: article, price: price},
            function (data) {
                if (data.article != null){
					$.get('/yandexCatalogUpdater.php');
                    alert('Стоимость изменена!');
                }
            },
            'json'
        );
    }

    function updateCount(fileName) {
        $.post(
            '/priceUpdater/import_v2.php',
            {action: 'count', fileName: fileName},
            function (data) {
                alert('Количество обновлено!' + data.errors);
            },
            'json'
        );
    }
	
    function updateAll(fileName) {
        $.post(
            '/priceUpdater/import_v2.php',
            {action: 'all', fileName: fileName},
            function (data) {
                alert('Цены обновлено!' + data.errors);
				$.get('/yandexCatalogUpdater.php');
                location.reload();

            },
            'json'
        );
    }

    function refresh() {
        $.post(
            '/priceUpdater/import_v2.php',
            {action: 'refresh'},
            function (data) {
                location.reload();
            }
        );
    }
</script>
</div>
</body>
</html>
