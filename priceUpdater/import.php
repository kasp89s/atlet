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
if (is_file(__DIR__ . '/tmp/lastImportFile.xls')) {
    $lastUploadFile = 'lastImportFile.xls';
}

if(isset($_POST['action']) && $_POST['action'] == 'refresh') {
    if(is_file(__DIR__ . '/tmp/lastImportFile.xls')) {
        unlink(__DIR__ . '/tmp/lastImportFile.xls');
    }
    exit;
}

if(isset($_POST['action']) && $_POST['action'] == 'count') {
    $db = DB::instance();
    $pars = new ParserExcel(__DIR__ . '/tmp/' . $_POST['fileName']);
    $products = $pars->getArray2();

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
    $products = $pars->getArray2();

    $errors = "\n";
    $db->execute("UPDATE `ml_catalog` SET  `availability2` = '0' WHERE 1;");
    $db->execute("UPDATE `ml_catalog_tastes` SET `count2` = '0' WHERE 1;");

    foreach($products as $product) {
        $taste = $db->select("SELECT * FROM  `ml_catalog_tastes` WHERE  `article` = '". $product['article'] ."'")->find();

        if (!empty($taste->productId)) {
            $sql = "UPDATE `ml_catalog_tastes` SET `count2` = '" . $product['count'] . "' WHERE `id` = " . $taste->id . ";";
            $db->execute($sql);
			$sql = "UPDATE `ml_catalog` SET `availability2` = `availability2` + " . $product['count'] . ", `price` = '" . $product['price'] . "' WHERE `id` = " . $taste->productId . ";";
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
    copy($_FILES['xls']['tmp_name'], __DIR__ . '/tmp/lastImportFile.xls');
    $products = $pars->getArray2();
} elseif(isset($lastUploadFile) && $lastUploadFile != '' && is_file(__DIR__ . '/tmp/' . $lastUploadFile)) {
    $db = DB::instance();
    $pars = new ParserExcel( __DIR__ . '/tmp/' . $lastUploadFile);
    $products = $pars->getArray2();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Загрузка склада</title>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
</head>
<body>
<? if(isset($products) && is_array($products) && sizeof($products) > 0):?>
    <form method="post" enctype="multipart/form-data">
        <input type="button" value="Обновить количество" onclick="updateCount('lastImportFile.xls')"/>
        &nbsp;&nbsp;&nbsp;
        <input type="button" value="Импорт файла" onclick="updateAll('lastImportFile.xls')"/>
        &nbsp;&nbsp;&nbsp;
        <a href="javascript: void(0);" onclick="refresh()">Вернуться к загрузке файла</a>
    </form>
    <table border=1>
        <tr>
            <th>Артикул</th>
            <th>Производитель</th>
            <th>Наименование</th>
            <th>Фасовка</th>
            <th>Количество</th>
            <th>Цена</th>
        </tr>
    <? foreach($products as $product):?>
        <?php
            $a = explode(',', $product['name']);
        ?>
        <tr style="border-bottom: 1px solid #111">
            <td><?= $product['article']?></td>
            <td><?= isset($a[0]) ? $a[0] : ''?></td>
            <td><?= isset($a[1]) ? $a[1] : ''?></td>
            <td><?= isset($a[2]) ? $a[2] : ''?></td>
            <td><?= $product['count']?></td>
            <td><?= $product['price']?></td>
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
    function updateCount(fileName) {
        $.post(
            '/priceUpdater/import.php',
            {action: 'count', fileName: fileName},
            function (data) {
                alert('Количество обновлено!' + data.errors);
            },
            'json'
        );
    }
	
    function updateAll(fileName) {
        $.post(
            '/priceUpdater/import.php',
            {action: 'all', fileName: fileName},
            function (data) {
                alert('Цены обновлено!' + data.errors);
				$.get('/yandexCatalogUpdater.php');
            },
            'json'
        );
    }

    function refresh() {
        $.post(
            '/priceUpdater/import.php',
            {action: 'refresh'},
            function (data) {
                location.reload();
            }
        );
    }
</script>
</body>
</html>
