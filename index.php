<?php
define('IN_PRODUCTION', TRUE);

$kohana_application = 'code/application';
$kohana_modules = 'code/modules';
$kohana_system = 'code/system';


//version_compare(PHP_VERSION, '5.2', '<') and exit('Kohana requires PHP 5.2 or newer.');
//(E_ALL | E_STRICT);
error_reporting(E_ERROR);
ini_set('display_errors', FALSE);
define('EXT', '.php');


$kohana_pathinfo = pathinfo(__FILE__);
define('DOCROOT', $kohana_pathinfo['dirname'].DIRECTORY_SEPARATOR);
define('KOHANA',  $kohana_pathinfo['basename']);

is_link(KOHANA) and chdir(dirname(realpath(__FILE__)));

$kohana_application = file_exists($kohana_application) ? $kohana_application : DOCROOT.$kohana_application;
$kohana_modules = file_exists($kohana_modules) ? $kohana_modules : DOCROOT.$kohana_modules;
$kohana_system = file_exists($kohana_system) ? $kohana_system : DOCROOT.$kohana_system;

define('APPPATH', str_replace('\\', '/', realpath($kohana_application)).'/');
define('MODPATH', str_replace('\\', '/', realpath($kohana_modules)).'/');
define('SYSPATH', str_replace('\\', '/', realpath($kohana_system)).'/');

unset($kohana_application, $kohana_modules, $kohana_system);

if (file_exists(DOCROOT.'install'.EXT) AND is_readable(DOCROOT.'install'.EXT)) {
	// Load the installation tests
	include DOCROOT.'install'.EXT;
} else {
	// Initialize Kohana
	require APPPATH.'Bootstrap'.EXT;
}

?>