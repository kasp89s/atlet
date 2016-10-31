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
if (is_file(__DIR__ . '/tmp/lastUploadFile.xls')) {
    $lastUploadFile = 'lastUploadFile.xls';
}

if(isset($_POST['action']) && $_POST['action'] == 'refresh') {
    if(is_file(__DIR__ . '/tmp/lastUploadFile.xls')) {
        unlink(__DIR__ . '/tmp/lastUploadFile.xls');
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
    copy($_FILES['xls']['tmp_name'], __DIR__ . '/tmp/lastUploadFile.xls');
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
    <title>Минимальные цены</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
</head>
<body>
<div class="container">
<? if(isset($products) && is_array($products) && sizeof($products) > 0):?>
    <form method="post" enctype="multipart/form-data" style="margin: 10px;">
        <a href="javascript: void(0);" class="btn btn-warning" onclick="refresh()">Вернуться к загрузке файла</a>
    </form>
    <table class="table table-bordered">
        <tr>
            <th>Артикул</th>
            <th>Производитель</th>
            <th>Наименование</th>
			<th>Фасовка</th>
            <th>Вкус</th>
            <th>Мин. Цена</th>
            <th>Цена сайта</th>
            <th></th>
        </tr>
	<?php
	function findProduct($productName, $products)
	{
		foreach($products as $product) {
			$a = explode(',', $product['name']);
			unset($a[0]);
			$name = implode(',', $a);
			if($name == $productName) {
				return $product;
			}
		}
	}
	
	$tmpArray = array();
	foreach($products as $product) {
			$a = explode(',', $product['name']);
			unset($a[0]);
			$name = implode(',', $a);
		$tmpArray[] = $name;
	}
	sort($tmpArray);
	$tmp = array();
	foreach ($tmpArray as $productName) {
		$tmp[] = findProduct($productName, $products);
	}
	
	$products = $tmp;
	?>

    <? foreach($products as $product):?>
        <?php
            $taste = $db->select("
            SELECT `ml_catalog`.`price`, `ml_catalog_tastes`.`count2` FROM `ml_catalog_tastes`
            LEFT JOIN `ml_catalog` ON `ml_catalog`.`id` = `ml_catalog_tastes`.`productId`
            WHERE  `ml_catalog_tastes`.`article` = '". $product['article'] ."'
            ")->find();

            $a = explode(',', $product['name']);
			if(is_numeric(preg_replace('~[^0-9]+~', '', $a[2]))){
				$volume = $a[2];
				$t = isset($a[3]) ? $a[3] : '';
			} else {
				$volume = '';
				$t = $a[2];
			}
        ?>
        <tr style="border-bottom: 1px solid #111">
            <td><?= $product['article']?></td>
            <td><?= isset($a[0]) ? $a[0] : ''?></td>
            <td><?= isset($a[1]) ? $a[1] : ''?></td>
            <td><?= $volume?></td>
            <td><?= $t?></td> 
            <td><?= $product['count']?></td>
            <td><input type="text" id="<?= $product['article']?>" name="price" placeholder="цена на сайте" value="<?= $taste->price?>"/></td>
            <td><input type="button" value="Обновить" class="btn btn-info btn-xs" onclick="updatePrice('<?= $product['article']?>')"></td>
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
            '/priceUpdater/uploader.php',
            {action: 'price', article: article, price: price},
            function (data) {
                if (data.article != null){
                    alert('Стоимость изменена!');
					$.get('/yandexCatalogUpdater.php');
//                    $('#' + data.article).val(data.price);
                }
            },
            'json'
        );
    }

    function updateCount(fileName) {
        $.post(
            '/priceUpdater/uploader.php',
            {action: 'count', fileName: fileName},
            function (data) {
                alert('Количество обновлено!' + data.errors);
            },
            'json'
        );
    }
	
    function updateAll(fileName) {
        $.post(
            '/priceUpdater/uploader.php',
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
            '/priceUpdater/uploader.php',
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
