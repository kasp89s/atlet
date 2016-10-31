<?php defined('SYSPATH') OR die('No direct access allowed.');

// editor path
$config['path'] = 'libs/markitup/markitup/';
// editor scriptname
$config['scriptname'] = 'jquery.markitup.pack.js';
$config['setspath'] = 'sets/';
$config['skinspath'] = 'skins/';



/**
 * toolbarset - вариант набора кнопок. Хранятся в /libs/markitup/markitup/sets/[папка]
 * [default,bbcode,css,dotclearhtml,html,markdown,text2tags,textile,texy,wiki]
 * 
 * scin - тема
 * [markitup,jtageditor,live,macosx,simple]
 */

$config['default'] = array
(
	'toolbarset' => 'default',
	'skin' => 'markitup',
);

$config['admin'] = array
(
	'toolbarset' => 'default',
	'skin' => 'markitup',
);

$config['article'] = array
(
	'toolbarset' => 'html',
	'skin' => 'markitup',
);

$config['comment'] = array
(
	'toolbarset' => 'bbcode',
	'skin' => 'markitup',
);