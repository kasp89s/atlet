<?php defined('SYSPATH') or die('No direct script access.');

Event::add('system.403', 'error_403');
Event::replace('system.404', array('Kohana_404_Exception', 'trigger'), 'error_404');

function error_403() {
	$controller = new Errors_Controller();
	$controller->error_403();
	exit;
}

function error_404() {
	$controller = new Errors_Controller();
	$controller->error_404();
	exit;
}
	
?>