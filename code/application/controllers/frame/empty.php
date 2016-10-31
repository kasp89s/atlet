<?php

class Frame_Empty_Controller extends Frame_Controller {

	private $template_name = 'empty';


	function __construct() {
		parent::__construct($this->template_name);
	}
}
?>