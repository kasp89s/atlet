<?php
/**
 * Интерфейс оплаты
 *
 */
class Pay_Controller extends T_Controller {

	/**
	 * Уведомление о платеже
	 *
	 */
	public function gate() {
		$config = Kohana::config('payment.robokassa');
		$tbl_order = new Order_Model();

		//установка текущего времени
		$tm = getdate(time()+9*3600);
		$date = "{$tm['year']}-{$tm['mon']}-{$tm['mday']} {$tm['hours']}:{$tm['minutes']}:{$tm['seconds']}";

		// чтение параметров
		$out_summ = $_REQUEST["OutSum"];
		$inv_id   = (int)$_REQUEST["InvId"];
		$crc      = $_REQUEST["SignatureValue"];

		$order = $tbl_order->db
			->select()
			->from($tbl_order->table_name)
			->where('id', $inv_id)
			->get()
			->row();

		if(!$order) {
		  echo "bad inv_id\n";
		  exit();
		}

		if(floatval($order['robokassa_payed'])>0){			echo "already payed\n";
		  	exit();		}

		/*if(intval($order['sid']) <= 0) {
		  echo "not confirmed order\n";
		  exit();
		}*/

		$crc = strtoupper($crc);
		$my_crc = strtoupper(md5("{$out_summ}:{$inv_id}:{$config['password2']}"));

		// проверка корректности подписи
		if ($my_crc != $crc) {
			echo "bad sign : ".$my_crc."\n";
			exit();
		}

		$out_summ = floatval($out_summ);

		// проверка корректности суммы платежа

		$mdlProducts = new OrderProducts_Model();
		$productsCost = $mdlProducts->db
       		->select(Array('total' => db::expr('sum(`ml_self`.`quantity` * `ml_self`.`productPrice`)')))
       		->from($mdlProducts->table_name)
       		->where('orderID', $order['id'])
       		->get()
       		->row();

  		$deliveryCost = $mdlProducts->db
       		->select(Array('total' => 'cost', 'sid'))
       		->from('delivery_costs')
       		->where('id', $order['delivery'])
       		->get()
       		->row();

		if (($productsCost['total']+$deliveryCost['total']) != $out_summ) {
			echo "bad sum\n";
			exit();
		}

		$mdlProducts = new OrderProducts_Model();
		$arrOrderContent = $mdlProducts->db
       		->select(Array('self.id', 'self.productID', 'self.productName', 'self.productPrice', 'self.productCode', 'self.quantity',
       					   'product_uri' => 'catalog.uri', 'catalog.1c_code',
       					   'catalog.group_id', 'subtotal' => db::expr('`ml_self`.`quantity` * `ml_self`.`productPrice`')))
       		->from($mdlProducts->table_name)
       		->left_join('catalog', 'catalog.id', 'self.productID')
       		->where('orderID', $inv_id)
       		->get()
       		->rows();


  		$strIds = "";
       	foreach($arrOrderContent as $k => $v){
       		if(strlen($strIds) > 0)$strIds .= "&";
       		$strIds .= "id[]=".urlencode(iconv('utf-8', 'windows-1251', $v['1c_code']));
       	}
		$strStatus = file_get_contents("http://www.sexsnab.com/api/check_available_many?".$strIds."&partner_id=2145&md5=6d000176f3d205b80e0ec1955e645fc9");
		$arrTmpRows = explode("\n", $strStatus);

		$arrCheckResult = Array();
		foreach($arrTmpRows as $k => $v){
			$arrTmpColsData = explode(":", $v);
			$arrCheckResult[$arrTmpColsData[0]] = intval($arrTmpColsData[1]);
		}

		$storeOk = true;
		$fltTotalSum = 0;
		$arrItemsToSend = Array();
		foreach($arrOrderContent as $k => $v){
			$arrOrderContent[$k]['apiStatus'] = intval($arrCheckResult[iconv('utf-8', 'windows-1251', $v['1c_code'])]);
			if($arrOrderContent[$k]['apiStatus'] < $arrOrderContent[$k]['quantity']){
				$storeOk = false;
			}

			$arrItemsToSend[] = Array(iconv('utf-8', 'windows-1251', $v['1c_code']), $v['quantity']);

			$fltTotalSum += $arrOrderContent[$k]['productPrice'] * $arrOrderContent[$k]['quantity'];
		}

		//$fltTotalSum += $deliveryCost['total'];

		if($storeOk){
			$arrOrderDataToSend = Array(
				'sum' => $fltTotalSum,
				'name' => iconv('utf-8', 'windows-1251', $data['author']),
				'surname' => '',
				'fathername' => '',
				'phone' => iconv('utf-8', 'windows-1251', $data['phone']),
				'email' => $data['email'],
				'zip' => iconv('utf-8', 'windows-1251', $data['delivery_zip']),
				'region' => iconv('utf-8', 'windows-1251', $data['delivery_region']),
				'city' => iconv('utf-8', 'windows-1251', $data['delivery_city']),
				'street' => iconv('utf-8', 'windows-1251', $data['delivery_street']),
				'house' => iconv('utf-8', 'windows-1251', $data['delivery_house']),
				'building' => iconv('utf-8', 'windows-1251', $data['delivery_building']),
				'corps' => iconv('utf-8', 'windows-1251', $data['delivery_corps']),
				'flat' => iconv('utf-8', 'windows-1251', $data['delivery_flat']),
				'items' => $arrItemsToSend,
				'delivery' => $deliveryCost['sid'],
				'transport' => '',
				'comments' => iconv('utf-8', 'windows-1251', $data['description'])
			);

			//echo("http://www.sexsnab.com/api/order?".http_build_query($arrOrderDataToSend)."&partner_id=2145&md5=6d000176f3d205b80e0ec1955e645fc9");

			$strStatus = file_get_contents("http://www.sexsnab.com/api/order?".http_build_query($arrOrderDataToSend)."&partner_id=2145&md5=6d000176f3d205b80e0ec1955e645fc9");
			if(substr($strStatus, 0, 2) == 'ok'){
				$intSId = intval(substr($strStatus, 2, strlen($strStatus) - 2));
				$tmpFill = Array('sid' => $intSId);

				$tbl_order->update($tmpFill, array('id'=>$inv_id));
				// Смена статуса
				$tbl_order->update(array('robokassa_payed' => $out_summ), array('id' => $inv_id));

				// письмо
				$letter = new View('robokassa/letter_to_manager');
				$order['site'] = 'intimel.ru';
				$letter->data = $order;
				$letter->sumOut = $out_summ;

				email::send(Kohana::config('cms.contacts.order_email'), $mail->From = Kohana::config('cms.contacts.default_email_from'), 'На сайте заказ', $letter->render(), TRUE);

				if(!empty($order['email'])) {
					$letter = new View('robokassa/letter_to_client');
					$letter->data = $order;
					$letter->sumOut = $out_summ;
					email::send($order['email'], $mail->From = Kohana::config('cms.contacts.default_email_from'), 'Вы сделали заказ', $letter->render(), TRUE);
				}

				// признак успешно проведенной операции
				echo "OK{$inv_id}\n";
			}else{				echo('not accepted order. message:'.$strStatus);			}
		}else{			echo('store error');		}
	}
}
?>