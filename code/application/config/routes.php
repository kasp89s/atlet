<?php defined('SYSPATH') or die('No direct access');

// Remove the standard default route
unset($configuration['_default'], $config['_default']);



$config['default'] = array (
	'uri' => ':controller/:method/:id',

	'defaults' => array (
		'controller' => 'index',
		'method' => 'index',
		'id' => FALSE,
	),
);


$config['admin'] = array (
	'uri' => 'admin/:controller/:method',
	'prefix' => array('controller' => 'admin_'),

	'defaults' => array (
		'controller' => 'index',
		'method' => 'index',
		'id' => FALSE,
	),

	'skip' => TRUE
);

$config['images_controler'] = array (
  	'uri' => 'files/:id',

	'defaults' => array (
		'controller' => 'files',
		'method' => 'get_image',
		'id' => FALSE,
	),

	'regex' => array(
        'controller' => 'files',
        'id'         => '.*'),

 	'skip' => true
);


$config['uri_router'] = array (
  	'uri' => ':controller/:id',

	'defaults' => array (
		'controller' => 'index',
		'method' => 'router',
		'id' => FALSE,
	),

	'regex' => array(
        'controller' => 'catalog|news|articles',
        'id'         => '.*')
);

$config['uri_router_hook'] = array (
  	'uri' => 'catalog2/:id',

	'defaults' => array (
		'controller' => 'catalog',
		'method' => 'router_for_hook',
		'id' => FALSE,
	),

	'regex' => array(
        'controller' => 'catalog2',
        'id'         => '.*')
);



$config['order_page_method'] = array (
  	'uri' => ':controller/:method',

	'regex' => array(
        'controller' => 'order'
 	),
 	'skip' => true
);

$config['robokassa'] = Array(
	'uri' => ':controller/:method',

	'defaults' => array (
		'controller' => 'index',
		'method' => 'index',
		'id' => FALSE,
	),

	'regex' => array(
		'controller' => 'pay|robokassa'
	),

	'skip' => TRUE
);


$config['static_scripts'] = array (
	'uri' => ':controller',

	'defaults' => array (
		'method' => 'index',
		'id' => FALSE,
	),

	'regex' => array(
        'controller' => 'xmlmap|map|yandex|sgsapi',
        'id'         => '.*'
 	),

 	'skip' => true
);

$config['action'] = array (
	'uri' => ':controller/:id',

	'defaults' => array (
		'controller' => 'action',
		'method' => 'index',
		'id' => 1,
	),

	'regex' => array(
        'controller' => 'action',
        'id'         => 'page(.*)'
 	),
);


$config['captcha'] = array (
	'uri' => ':controller/:id',

	'defaults' => array (
		'controller' => 'captcha',
		'method' => 'index',
		'id' => FALSE,
	),

	'regex' => array(
        'controller' => 'captcha'
 	),

 	'skip' => true
);
