<?php

class Frame_Common_Controller extends Frame_Controller {

	private $template_name = 'common';


	function __construct() {
		parent::__construct($this->template_name);
		session_start();

		$this->set_attribute('css', '/css/style.css');
		$this->set_attribute('js', '/js/jquery/jquery.js');
		$this->set_attribute('js', '/js/jquery/jquery-ui.min.js');
		$this->set_attribute('js', '/js/jquery/jquery.form.js');

		//$this->add_component('topmenu', 'topmenu');
		$this->add_component('leftmenu', 'leftmenu');
		$this->add_component('searchform', 'searchform');
		$this->add_component('productpromo', 'productpromo');
		$this->add_component('addlinks', 'addlinks');
		$this->add_component('banners', 'banners');

		$this->add_component('discount', 'discount');

        $strCartID = $this->get_cart_id();

		$mdlCart = new Cart_Model();
		$arrCartContent = $mdlCart->db
        		->select(db::expr('1'))
        		->from($mdlCart->table_name)
        		->where('session_id', $strCartID)
        		->where('is_delay', '<=' ,db::expr('0'))
        		->get()
        		->rows();

		$this->template->cart_count = count($arrCartContent);

		$this->template->phone_head_code  = Kohana::config('cms.phone_head.city_code');
		$this->template->phone_head_number  = Kohana::config('cms.phone_head.phone');

		$this->template->needshowfullcart = $_SESSION['needshowfullcart'];
		$this->template->needshowshortcart = $_SESSION['needshowshortcart'];
		unset($_SESSION['needshowfullcart']);
		unset($_SESSION['needshowshortcart']);
	}

	private function get_cart_id(){
		$strCartID = $this->input->cookie('cart_id', md5(microtime(true)+rand(1,10000)));
		cookie::set('cart_id', $strCartID, Kohana::config('coocie.expire'));

		return $strCartID;
	}

}
?>