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
<?php
require_once 'priceUpdater/DB.php';
$db = DB::instance();

if (isset($_POST['dateStart']) && isset($_POST['dateEnd'])) {

    $dateStart = date('Y-m-d 00:00:00', strtotime($_POST['dateStart']));
    $dateEnd = date('Y-m-d 00:00:00', strtotime($_POST['dateEnd']));

//    $history = $db->select("SELECT `h`.*, `c`.`name` as `productName`, `t`.`name` as `taste`, `c`.`volume` FROM `ml_5lb_history` `h`
// LEFT JOIN `ml_catalog_tastes` `t` ON `t`.`article` = `h`.`article`
// LEFT JOIN `ml_catalog` `c` ON `c`.`id` = `t`.`productId`
// WHERE `h`.`date` >= '{$dateStart}' AND `h`.`date` <= '{$dateEnd}'
// ORDER BY `h`.`date` asc")->findAll();

    $historyArray = array();
    foreach ($history as $record) {
        if (empty($historyArray[$record->article]) === true) {
            $historyArray[$record->article] = array(
                'record' => array(
                    'productName' => $record->productName,
                    'taste' => $record->taste,
                    'volume' => $record->volume,
                ),
                strtotime($record->date) => array(
                    'count' => $record->count,
                )
            );
            continue;
        }

        $historyArray[$record->article][strtotime($record->date)] = array(
            'count' => $record->count,
        );
    }
    $dates = $db->select("SELECT DISTINCT `date` FROM `ml_5lb_history` WHERE `date` >= '{$dateStart}' AND `date` <= '{$dateEnd}' ORDER BY `date` asc")->findAll();
}

$lastCount = array();
$intervalChange = array();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>История продаж</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/master/build/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="css/bootstrap-table.min.css">
    <script src="//wenzhixin.net.cn/p/bootstrap-table/docs/assets/jquery.min.js"></script>
<!--    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>-->
<!--    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.0-rc2/js/bootstrap.min.js"></script>-->
    <script src="//wenzhixin.net.cn/p/bootstrap-table/docs/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="//wenzhixin.net.cn/p/bootstrap-table/docs/assets/bulletin/jquery.bulletin.js"></script>
    <!--    <script src="js/jquery.tablesorter.pager.js"></script>-->
    <script src="js/moment.js"></script>
    <script src="js/bootstrap-table.min.js"></script>
    <script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/master/src/js/bootstrap-datetimepicker.js"></script>
</head>
<body>
<div class="container">
    <form method="post">
        <div class="col-sm-6" style="height:75px;">
            <div class='col-md-5'>
                <div class="form-group">
                    <div class='input-group date' id='datetimepicker9'>
                        <input type='text' class="form-control" name="dateStart"/>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    </div>
                </div>
            </div>
            <div class='col-md-5'>
                <div class="form-group">
                    <div class='input-group date' id='datetimepicker10'>
                        <input type='text' class="form-control" name="dateEnd"/>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    </div>
                </div>
            </div>
            <input type="submit" class="btn" value="Отчёт">
        </div>

    </form>
<!--    --><?// if (empty($historyArray) === false):?>
<!--        <table class="tablesorter" id="sort-table">-->
<!--            <thead>-->
<!--            <tr>-->
<!--                <th>Позиция</th>-->
<!--                --><?// foreach ($dates as $record):?>
<!--                    <th>-->
<!--                        --><?//= date('d.m.Y', strtotime($record->date));?>
<!--                    </th>-->
<!--                --><?// endforeach;?>
<!--                <th>Изменение наличия</th>-->
<!--            </tr>-->
<!--            </thead>-->
<!--            --><?// foreach ($historyArray as $article => $data):?>
<!--                <tr>-->
<!--                    <td>-->
<!--                        <b>--><?//= $article?><!--</b> --><?//= $data['record']['productName']?><!-- --><?//= $data['record']['volume']?><!-- --><?//= $data['record']['taste']?>
<!--                    </td>-->
<!--                    --><?// foreach ($dates as $record):?>
<!--                        --><?php
//                        if (isset($lastCount[$article])) {
//                            $change = - ($lastCount[$article] - $data[strtotime($record->date)]['count']);
//                            $x = ($lastCount[$article] - $data[strtotime($record->date)]['count'] >= 0) ? $lastCount[$article] - $data[strtotime($record->date)]['count'] : - $lastCount[$article] - $data[strtotime($record->date)]['count'];
//                        } else {
//                            $change = '';
//                            $x = 0;
//                        }
//
//                        $intervalChange[$article]+= $x;
//                        ?>
<!--                        <td>-->
<!--                            --><?//= $change?><!-- (--><?//= ($data[strtotime($record->date)]['count'] > 0) ? $data[strtotime($record->date)]['count'] : 0;?><!--)-->
<!--                        </td>-->
<!--                        --><?php
//                        $lastCount[$article] = $data[strtotime($record->date)]['count'];
//                        ?>
<!--                    --><?// endforeach;?>
<!--                    <td>-->
<!--                        --><?//= $intervalChange[$article];?>
<!--                    </td>-->
<!--                </tr>-->
<!--            --><?// endforeach;?>
<!--            <tfoot>-->
<!--            </tfoot>-->
<!--        </table>-->
<!--    --><?// endif;?>
    <table id="table-pagination" data-height="400" data-pagination="true" data-search="true" data-page-size="10">
    <thead>
    <tr>
        <th data-field="id" data-align="right" data-sortable="true">Позиция</th>
        <th data-field="id1" data-align="right" data-sortable="true">08.12.2014</th>
        <th data-field="id2" data-align="right" data-sortable="true">09.12.2014</th>
        <th data-field="id3" data-align="right" data-sortable="true">Изменение наличия</th>
    </tr>
    </thead>
    <tbody>
    <tr data-index="0">
        <td>
            <b>5340</b> Testagen 120 таб без вкуса                    </td>
        <td>
            (8)
        </td>
        <td>
            -8 (0)
        </td>
        <td>
            8                    </td>
    </tr>
    <tr data-index="1">
        <td>
            <b>7089</b> Z-Core PM 60 капс без вкуса                    </td>
        <td>
            (20)
        </td>
        <td>
            -20 (0)
        </td>
        <td>
            20                    </td>
    </tr>
    <tr data-index="2">
        <td>
            <b>3712</b> Platinum Hydrobuilder 1040 г шоколад                    </td>
        <td>
            (30)
        </td>
        <td>
            -30 (0)
        </td>
        <td>
            30                    </td>
    </tr>
    <tr data-index="3">
        <td>
            <b>6385</b> Acetyl L-Carnitine 500 mg 200 капс без вкуса                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>1107</b> Matrix 2.0 980 г апельсин                    </td>
        <td>
            (45)
        </td>
        <td>
            -45 (0)
        </td>
        <td>
            45                    </td>
    </tr>
    <tr>
        <td>
            <b>6496</b> Paleo (MHP) 908 g 908 г тройной шоколад                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>2888</b> 100% Pure Platinum Whey 2270 г банан                    </td>
        <td>
            (50)
        </td>
        <td>
            -50 (0)
        </td>
        <td>
            50                    </td>
    </tr>
    <tr>
        <td>
            <b>2024</b> Metabolift Ephedra Free 120 капс без вкуса                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>7731</b> GL3 L-Glutamine  1200 г без вкуса                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>1619</b> L-Carnitine Bar 24 шт земляника                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>5289</b> Casein AAE 907 г шоколад                    </td>
        <td>
            (20)
        </td>
        <td>
            -20 (0)
        </td>
        <td>
            20                    </td>
    </tr>
    <tr>
        <td>
            <b>5870</b> Whey Supreme 2275 г пина колада                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>10917</b> Пробник GlutamaX Warrior  15 г ананас                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>3098</b> naNO Vapor Hardcore Pro Series Ignition Stix 20 пак без вкуса                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>485</b> Thermo Cuts 200 капс без вкуса                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>9649</b> ProStak-Expansion Pak (3 контейнера) голубой 	 1 шт зеленый                    </td>
        <td>
            (10)
        </td>
        <td>
            -10 (0)
        </td>
        <td>
            10                    </td>
    </tr>
    <tr>
        <td>
            <b>9493</b> Coral Calcium 400 мг with Vitamin D 90 капс без вкуса                    </td>
        <td>
            (20)
        </td>
        <td>
            -20 (0)
        </td>
        <td>
            20                    </td>
    </tr>
    <tr>
        <td>
            <b>1555</b> Liporedux 177 мл без вкуса                    </td>
        <td>
            (60)
        </td>
        <td>
            -60 (0)
        </td>
        <td>
            60                    </td>
    </tr>
    <tr>
        <td>
            <b>3075</b> IsoBolic 908 г банан                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>8071</b> Assault 435 г ежевика                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>7433</b> Whey Protein  2350 г шоколад                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>1418</b> 100% Whey Gold Standard 2270 г дабл рич шоколад                    </td>
        <td>
            (250)
        </td>
        <td>
            -250 (0)
        </td>
        <td>
            250                    </td>
    </tr>
    <tr>
        <td>
            <b>1668</b> Diet Ripped 120 капс без вкуса                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>2640</b> Varcil R2 2000 г ваниль                    </td>
        <td>
            (2)
        </td>
        <td>
            -2 (0)
        </td>
        <td>
            2                    </td>
    </tr>
    <tr>
        <td>
            <b>1626</b> Nitro-Tech Bar 12 шт двойной шоколад                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>9361</b> 100% Whey 600 г печенье                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>1685</b> Magnesium Liquid 7 амп без вкуса                    </td>
        <td>
            (2)
        </td>
        <td>
            -2 (0)
        </td>
        <td>
            2                    </td>
    </tr>
    <tr>
        <td>
            <b>2939</b> Magic Milk 1125 г молочный шоколад                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>2310</b> Energy Drink Tabs 5 таб апельсиновая карамель                    </td>
        <td>
            (100)
        </td>
        <td>
            -100 (0)
        </td>
        <td>
            100                    </td>
    </tr>
    <tr>
        <td>
            <b>4809</b> Nitro-Tech Performance 907 г шоколад                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>12111</b> WOD Crusher Fire Works 360 г Scitec Nutrition 360 г апельсин-лимон                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>1696</b> BCAA 5000 Powder 345 г нейтральный                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>9555</b> Amino Drive  411 г дыня                    </td>
        <td>
            (0)
        </td>
        <td>
            0 (0)
        </td>
        <td>
            0                    </td>
    </tr>
    <tr>
        <td>
            <b>12413</b> Proto Whey 900 г шоколад                    </td>
        <td>
            (45)
        </td>
        <td>
            -45 (0)
        </td>
        <td>
            45                    </td>
    </tr>
    <tr>
        <td>
            <b>10513</b> HemodrauliX (Axis Labs)  96 капс без вкуса                    </td>
        <td>
            (10)
        </td>
        <td>
            -10 (0)
        </td>
        <td>
            10                    </td>
    </tr>
    </tbody>
    </table>
</div>
<script>
    $(document).ready(function(){
        $(function(){
            $('#table-pagination').bootstrapTable();
        });
    });
</script>
</body>
</html>
