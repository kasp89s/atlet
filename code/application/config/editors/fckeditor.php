<?php defined('SYSPATH') OR die('No direct access allowed.');

// editor path
$config['basepath'] = 'libs/fckeditor/';
// editor scriptname (usually 'fckeditor.php' or 'fckeditor_php5.php')
$config['scriptname'] = 'fckeditor_php5.php';

$config['customconfig'] = 'editor_config.js';


/**
 * toolbarset - вариант набора кнопок. хранятся в /libs/fckeditor/editor_config.js
 */

$config['default'] = array
(
	'toolbarset' => 'Default',
	'config' => array
	(
		'ToolbarCanCollapse' => FALSE,
		'ToolbarLocation' => 'In',
		'EnterMode' => 'p',
	),
);

$config['admin'] = array
(
	'toolbarset' => 'Admin',
	'config' => array
	(
		'ToolbarCanCollapse' => FALSE,
		'ToolbarLocation' => 'In',
		'EnterMode' => 'p',
	),
);


$config['comment'] = array
(
	'toolbarset' => 'Comment',
	'config' => array
	(
		'ToolbarCanCollapse' => FALSE,
		'ToolbarLocation' => 'In',
		'EnterMode' => 'p',
	),
);

$config['article'] = array
(
	'toolbarset' => 'Article',
	'config' => array
	(
		'ToolbarCanCollapse' => FALSE,
		'ToolbarLocation' => 'In',
		'EnterMode' => 'p',
	),
);