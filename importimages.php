<?php
	include("config.php");

 	$resSql = mysql_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
	mysql_select_db(DB_DATABASE, $resSql);

	setlocale(LC_ALL, 'ru_RU.cp1251');

	mysql_query("SET NAMES 'cp1251'");
	$resFile = fopen("http://partner.sexsnab.com/export/photo?uid=2143&hash=248a0abd5a94e5386ec662935e12337b", "r");
	$arrCsvData = fgetcsv($resFile, 3000, ";", '"');

	$intCounter = -1;

	while(($row = fgetcsv($resFile, 3000, ";", '"')) !== FALSE) {

		if(!preg_match('/([^0-9]+)/i', $row[0])){
		} else {


        if(mysql_num_rows($resQuery)){
            $arrCurrentData = mysql_fetch_assoc($resQuery);
        	$intCurrentProductId = $arrCurrentData['product_id'];

        	$resSubQuery = mysql_query("select * from `oc_product_image` where `product_id` = '".intval($intCurrentProductId)."' and `image` like '%".$row[3]."'");

        	if(!mysql_num_rows($resSubQuery)){

				 				 intval(substr($intCurrentProductId, strlen($intCurrentProductId)-3, 1)).'/'.
				 				 intval(substr($intCurrentProductId, strlen($intCurrentProductId)-2, 1)).'/'.
				 				 intval(substr($intCurrentProductId, strlen($intCurrentProductId)-1, 1)).'/'.
				 				 'item_'.intval($intCurrentProductId).'/';

				if(!file_exists('image/'.$strFilePath)) mkdir('image/'.$strFilePath, 0777, true);

				if(copy($row[2], 'image/'.$strFilePath.$row[3])){
								 values ('".intval($intCurrentProductId)."','".mysql_real_escape_string($strFilePath.$row[3])."',0)");

					if(strlen($arrCurrentData['image'])<3){

				usleep(110000);
			}


			echo("Not found: ".$row[0]."<br>");
		}
        $intCounter++;
 	}


?>