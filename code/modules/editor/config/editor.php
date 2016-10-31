<?php defined('SYSPATH') OR die('No direct access allowed.');

// default driver type
$config['default_driver'] = 'tinymce';

$config['jquerypath'] = 'media/js/jquery.pack.js';

// NOTE: when MarkItUp! used, you should set its size at PATH/skins/SKINNAME/style.css

$config['default'] = array
(
	'width' => 500,
	'height' => 200,
	'editor' => array
	(
		'driver' => 'fckeditor',
		'profile' => 'default',
	),
	'fieldname' => 'text',
	'value' => '',
);