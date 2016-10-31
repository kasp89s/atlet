<?php
	include("config.php");

 	$resSql = mysql_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
	mysql_select_db(DB_DATABASE, $resSql);

	setlocale(LC_ALL, 'ru_RU.cp1251');

	mysql_query("SET NAMES 'cp1251'");
	$resFile = fopen("http://partner.sexsnab.com/export/?uid=2143&hash=248a0abd5a94e5386ec662935e12337b", "r");
	$arrCsvData = fgetcsv($resFile, 3000, ";", '"');

	$intCounter = -1;

	while(($row = fgetcsv($resFile, 3000, ";", '"')) !== FALSE) {
	    $resQuery = mysql_query("select * from `ml_catalog_manufacturer`
        			               where `name` = '".mysql_real_escape_string($row[6])."'");

        if(mysql_num_rows($resQuery) > 0){
        	$intManufacturerId = mysql_fetch_assoc($resQuery);
        	$intManufacturerId = $intManufacturerId['manufacturer_id'];
        } else {
        	mysql_query("insert into `ml_catalog_manufacturer` (`name`)
        				 values ('".mysql_real_escape_string($row[6])."')");

        	$intManufacturerId = mysql_insert_id();
        }


		$resQuery = mysql_query("select * from `ml_catalog` where `code` = '".mysql_real_escape_string($row[1])."'");


        $intCurrentProductId = 0;
        if(mysql_num_rows($resQuery)){
        	$arrCurrentData = mysql_fetch_assoc($resQuery);
        	$intCurrentProductId = $arrCurrentData['product_id'];
        	mysql_query("update `ml_catalog`
        				 set `manufacturer_id` = '".intval($intManufacturerId)."',
        				     `price` = '".doubleval($row[8])."',
        				     `1c_code` = '".mysql_real_escape_string($row[2])."',
        				     `name` = '".mysql_real_escape_string($row[0])."',
        				     `description` = '".mysql_real_escape_string($row[7])."'
        				 where `id` = '".intval($intCurrentProductId)."'");
        } else {
        	$resQuery = mysql_query("INSERT INTO `oc_product` (
        									`code`, `manufacturer_id`, `price`,  `1c_code`,
        									`name`, `description`)
        							VALUES ('".mysql_real_escape_string($row[1])."', '".intval($intManufacturerId)."', '".doubleval($row[8])."', '".mysql_real_escape_string($row[2])."',
        							'".mysql_real_escape_string($row[0])."','".mysql_real_escape_string($row[7])."'");
        	$intCurrentProductId = mysql_insert_id();
        }


        $intLastParentId = 0;
        $intTopLevelParent = 0;
        for($i=0; $i < 3; $i++){

        	if(strlen($row[3+$i]) <= 2) continue;

        	$resCatQuery = mysql_query("select cd.`category_id` from `oc_category_description` cd
        								left join `oc_category` c on c.`category_id` = cd.`category_id`
        	                            where cd.`name` = '".mysql_real_escape_string($row[3+$i])."' and c.`parent_id`='".$intLastParentId."'");

        	if(mysql_num_rows($resCatQuery) > 0){
        		$arrGroup = mysql_fetch_assoc($resCatQuery);
        		$intLastParentId = $arrGroup['category_id'];
        	}else{

        		mysql_query("insert into `oc_category` (`parent_id`, `image`, `top`, `column`, `status`, `date_added`, `date_modified`)
        					 values (".intval($intLastParentId).", '', '".($intLastParentId>0?0:1)."', 0, 1, now(), now())");
        		$intLastParentId = mysql_insert_id();

        		mysql_query("insert into `oc_category_description` (`category_id`, `name`,
        															`language_id`, `description`, `meta_description`, `meta_keyword`)
        					 values('".intval($intLastParentId)."', '".mysql_real_escape_string($row[3+$i])."',
        					 										1, '', '', '')");

				mysql_query("INSERT INTO `oc_category_path` (
        									`category_id`, `path_id`, `level`)
        							VALUES ('".intval($intLastParentId)."', 0, 0);");

        		mysql_query("INSERT INTO `oc_category_to_store` (
        									`category_id`, `store_id`)
        							VALUES ('".intval($intLastParentId)."', 0);");
        	}
        }

        $resQuery = mysql_query("select * from `oc_product_to_category` where `product_id` = '".intval($intCurrentProductId)."' and `category_id` = '".intval($intLastParentId)."'");

        if(!mysql_num_rows($resQuery)){
        	$arrCurrentData = mysql_fetch_assoc($resQuery);

        	$resQuery = mysql_query("INSERT INTO `oc_product_to_category` (
        									`product_id`, `category_id`)
        							VALUES ('".intval($intCurrentProductId)."', '".intval($intLastParentId)."');");
        }

        $intCounter++;
 	}

 	repairCategories();

 	function repairCategories($parent_id = 0) {
		$resCat = mysql_query("SELECT * FROM " . DB_PREFIX . "category WHERE parent_id = '" . (int)$parent_id . "'");

		while ($category = mysql_fetch_assoc($resCat)) {
			// Delete the path below the current one
			mysql_query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category['category_id'] . "'");

			// Fix for records with no paths
			$level = 0;

			$query = mysql_query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$parent_id . "' ORDER BY level ASC");

			while ($result = mysql_fetch_assoc($query)) {
				mysql_query("INSERT INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category['category_id'] . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			mysql_query("REPLACE INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category['category_id'] . "', `path_id` = '" . (int)$category['category_id'] . "', level = '" . (int)$level . "'");

			repairCategories($category['category_id']);
		}
	}
?>