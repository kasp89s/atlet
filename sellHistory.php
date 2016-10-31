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
    $history = array();
    $dates = $db->select("SELECT DISTINCT `date` FROM `ml_5lb_history` WHERE `date` >= '{$dateStart}' AND `date` <= '{$dateEnd}' ORDER BY `date` asc")->findAll();
    foreach ($dates as $record) {
        $history[$record->date] = $db->select("SELECT `h`.*, `c`.`name` as `productName`, `t`.`name` as `taste`, `c`.`volume` FROM `ml_5lb_history` `h`
 LEFT JOIN `ml_catalog_tastes` `t` ON `t`.`article` = `h`.`article`
 LEFT JOIN `ml_catalog` `c` ON `c`.`id` = `t`.`productId`
 WHERE `h`.`date` = '{$record->date}'
 ORDER BY `h`.`date` asc")->findAll();

    }

    $historyArray = array();
    foreach ($history as $date => $records) {
        foreach ($records as $record) {
            if (empty($historyArray[$record->article]) === true) {
                $historyArray[$record->article] = array(
                    'record' => array(
                        'productName' => $record->productName,
                        'taste' => $record->taste,
                        'volume' => $record->volume,
                    ),
                    strtotime($date) => array(
                        'count' => $record->count,
                    )
                );
            } else {
                $historyArray[$record->article][strtotime($date)] = array(
                    'count' => $record->count,
                );
            }
        }
    }


    $lastCount = array();
    $intervalChange = array();
    foreach ($historyArray as $article => $data) {
        foreach ($dates as $record) {
            if (empty($data[strtotime($record->date)])) {
                $historyArray[$article][strtotime($record->date)] = array(
                    'count' => 'Нет данных',
                );

                continue;
            }
            if (isset($lastCount[$article])) {
                $change = - ($lastCount[$article] - $data[strtotime($record->date)]['count']);
                $x = ($change >= 0) ? $change : - $change;
                $historyArray[$article][strtotime($record->date)]['change'] = $change;
            } else {
                $change = '';
                $x = 0;
            }

            $intervalChange[$article]+= $x;
            $lastCount[$article] = $data[strtotime($record->date)]['count'];
            $historyArray[$article]['intervalChange'] = $intervalChange[$article];
        }
    }
    arsort($intervalChange);
}


//asort() по возрастанию
//arsort()
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>История продаж</title>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.0-rc2/js/bootstrap.min.js"></script>
    <script src="js/jquery.tablesorter.min.js"></script>
    <script src="js/jquery.tablesorter.widgets.min.js"></script>
    <script src="addons/pager/jquery.tablesorter.pager.js"></script>
    <!--    <script src="js/jquery.tablesorter.pager.js"></script>-->
    <script src="js/moment.js"></script>
    <script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/master/src/js/bootstrap-datetimepicker.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/master/build/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="css/jquery.tablesorter.pager.css">
    <link rel="stylesheet" href="css/theme.blue.css">
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
    <? if (empty($historyArray) === false):?>
        <table class="tablesorter-blue" id="sort-table">
            <thead>
            <tr>
                <th>Позиция</th>
                <? foreach ($dates as $record):?>
                    <th>
                        <?= date('d.m.Y', strtotime($record->date));?>
                    </th>
                <? endforeach;?>
                <th>Изменение наличия</th>
            </tr>
            </thead>
            <? $i = 0; foreach ($intervalChange as $article => $periodChange):?>
                <?
//                var_dump($article);
//                var_dump($periodChange); exit;
                $data = $historyArray[$article];
                $i++; if ($i >= 200) break;?>
                <tr>
                    <td>
                        <b><?= $article?></b> <?= $data['record']['productName']?> <?= $data['record']['volume']?> <?= $data['record']['taste']?>
                    </td>
                    <? foreach ($dates as $record):?>
                        <td>
                            <?= $data[strtotime($record->date)]['change']?> (<?= $data[strtotime($record->date)]['count'];?>)
                        </td>
                    <? endforeach;?>
                    <td>
                        <?= $data['intervalChange'];?>
                    </td>
                </tr>
            <? endforeach;?>
            <tfoot>
            </tfoot>
        </table>
    <? endif;?>
</div>

<script type="text/javascript">
//    $(document).ready(function(){
//        $(function(){
//            $("#sort-table").tablesorter(
//                {
//                    theme : 'blue',
//
//                    sortList : [[1,0],[2,0],[3,0]],
//
//                    // header layout template; {icon} needed for some themes
//                    headerTemplate : '{content}{icon}',
//
//                    // initialize column styling of the table
//                    widgets : ["columns", "uitheme", "zebra"],
//                    widgetOptions : {
//                        // change the default column class names
//                        // primary is the first column sorted, secondary is the second, etc
//                        columns : [ "primary", "secondary", "tertiary" ]
//                    }
//                });
//        });
//    });
    $('.date').datetimepicker();
</script>
</body>
</html>
