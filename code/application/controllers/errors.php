<?php defined('SYSPATH') OR die('No direct access allowed.');

class Errors_Controller extends T_Controller {

	public function __construct() {
		$this->frame = 'common';
	}


	function error_403() {
	    header('HTTP/1.1 403 Forbidden');
		$this->template = new View('errors/403');

		/**
		 * Биндим заголовки
		 */
		$this->add_attribute('title', '403');

	}

	function error_404() {
	    header('HTTP/1.1 404 File Not Found');

		$this->template = new View('errors/404');

		/**
		 * Биндим заголовки
		 */
		$this->add_attribute('title', '404');
	}

}
