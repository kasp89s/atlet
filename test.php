<?php
define('group', 3);
define('campaignId', 5);
define('advertisementId', 9);
define('clickCost', 14);
define('url', 12);

$handle = fopen('tovary.csv', "r");

if ($handle) {
    $data = array();
    while (($buffer = fgets($handle)) !== false) {
//        $buffer = iconv('windows-1251', 'utf-8', $buffer);
        if (stripos($buffer, '"-"') !== false) {
            $buffer = explode('	', $buffer);
            $buffer = array_diff($buffer, array(''));
//            var_dump($buffer); exit;
            $group = str_replace('"', '', $buffer[group]);
            if (empty($data[$group]) === true && count(explode('/', $buffer[url])) > 6) {
                $url = explode('/', str_replace('"', '', $buffer[url]));
                $url = $url[count($url) - 2];
                $sql.= "UPDATE `ml_catalog` SET `campaignId` = '" . str_replace('"', '', $buffer[campaignId]) . "', `advertisementId` = '" . str_replace('"', '', $buffer[advertisementId]) . "', `clickCost` = '" . str_replace('"', '', $buffer[clickCost]) . "' WHERE `uri` = '" . $url . "'; \n";

//                $data[$group] = array(
//                    'campaignId' => str_replace('"', '', $buffer[campaignId]),
//                    'advertisementId' => str_replace('"', '', $buffer[advertisementId]),
//                    'clickCost' => str_replace('"', '', $buffer[clickCost]),
//                    'url' => str_replace('"', '', $buffer[url]),
//                );
            }
        }

//        $data['uk'] = explode(';', $buffer);
//        if ($this->checkShop($data) === false) {
//            $data['ru'] = $this->translateData($data['uk']);
//            $this->writeShop($data);
//        }
    }

    if (!feof($handle)) {
        throw new Exception('Error: unexpected fgets() fail');
    }
    fclose($handle);
}
file_put_contents('data.sql', $sql);
