<?php defined('SYSPATH') OR die('No direct access allowed.');

// editor path
$config['path'] = 'media/js/tinymce/';
// editor scriptname (usually 'tiny_mce.php')
$config['scriptname'] = 'tiny_mce.js';

// default profile
$config['default'] = array
(
	'theme' => 'advanced',
	'mode' => 'exact',
	'toolbar_location' => 'top',
	'toolbar_align' => 'left',
	'plugins' => array(),
	'buttons1' => array
		(
			'bold', 'italic', 'underline', 'strikethrough', '|',
			'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', 
			'bullist', 'numlist', 'outdent', 'indent'
		),
	'buttons2' => array(),
	'buttons3' => array(),
);