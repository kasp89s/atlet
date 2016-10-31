<?php defined('SYSPATH') OR die('No direct access allowed.');

// default driver type
$config['default_driver'] = 'tinymce';

$config['jquerypath'] = 'media/js/jquery.pack.js';

// NOTE: when MarkItUp! used, you should set its size at PATH/skins/SKINNAME/style.css

/**
 * driver - тип редактора
 * [fckeditor,tinymce,markitup]
 */
$config['default'] = array
(
	'width' => 700,
	'height' => 200,
	'editor' => array
	(
		'driver' => 'tinymce',
		'profile' => 'default',
	),
	'fieldname' => 'text',
	'value' => '',
);

$config['admin'] = array
(
	'width' => 600,
	'height' => 200,
	'editor' => array
	(
		'driver' => 'tinymce',
		'profile' => 'admin',
	),
	'fieldname' => 'text',
	'value' => '',
);

$config['comment'] = array
(
	'width' => 500,
	'height' => 170,
	'editor' => array
	(
		'driver' => 'fckeditor',
		'profile' => 'comment',
	),
	'fieldname' => 'text',
	'value' => '',
);


//*********************
$config['fck_default'] = array
(
	'width' => 700,
	'height' => 200,
	'editor' => array
	(
		'driver' => 'fckeditor',
		'profile' => 'default',
	),
	'fieldname' => 'text',
	'value' => '',
);

$config['tiny_default'] = array
(
	'width' => 700,
	'height' => 200,
	'editor' => array
	(
		'driver' => 'tinymce',
		'profile' => 'default',
	),
	'fieldname' => 'text',
	'value' => '',
);

$config['markitup_default'] = array
(
	'width' => 500,
	'height' => 200,
	'editor' => array
	(
		'driver' => 'markitup',
		'profile' => 'default',
	),
	'fieldname' => 'text',
	'value' => '',
);