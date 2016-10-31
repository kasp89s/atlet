<?php
/**
 * Заказ
 *
 */
class Robokassa_Controller extends T_Controller {

	public function __construct() {
		$this->frame = 'common';
		parent::__construct();
	}

	public function success() {
		$arrData=array(
      		'text'    =>  "Успешная оплата!",
      		'use_h1'  =>  true
       	);
        $this->add_component('sectiontitle', 'sectiontitle',$arrData);
        $this->del_component('leftmenu', 'leftmenu');

		$this->template = new View('robokassa/success');

		$inv_id = (int)$_REQUEST["InvId"];

		$tbl_order = new Order_Model;
		$order = $tbl_order->db
			->from($tbl_order->table_name)
			->where('id', $inv_id)
			->count();

		if(!$order)
			url::redirect('/');

		$this->template->id = $inv_id;
	}

	public function fail() {
		$arrData=array(
      		'text'    =>  "Отказ от оплаты!",
      		'use_h1'  =>  true
       	);
        $this->add_component('sectiontitle', 'sectiontitle',$arrData);
        $this->del_component('leftmenu', 'leftmenu');
		$this->template = new View('robokassa/fail');

		$inv_id = (int)$_REQUEST["InvId"];

		$tbl_order = new Order_Model;
		$order = $tbl_order->db
			->from($tbl_order->table_name)
			->where('id', $inv_id)
			->count();

		if(!$order)
			url::redirect('/');

		$this->template->id = $inv_id;
	}
}
?>