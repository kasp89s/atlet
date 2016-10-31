<?php defined('SYSPATH') OR die('No direct access allowed.');

// editor path
$config['basepath'] = 'media/js/fckeditor/';
// editor scriptname (usually 'fckeditor.php' or 'fckeditor_php5.php')
$config['scriptname'] = 'fckeditor_php5.php';

$config['customconfig'] = 'editor_config.js';

// default profile
$config['default'] = array
(
	'toolbarset' => 'Comment',
	'config' => array
	(
		'ToolbarCanCollapse' => FALSE,
		'ToolbarLocation' => 'In',
		'EnterMode' => 'p',
	),
);