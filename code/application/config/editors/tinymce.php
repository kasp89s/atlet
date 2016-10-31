<?php defined('SYSPATH') OR die('No direct access allowed.');

// editor path
$config['path'] = 'libs/tinymce/';
// editor scriptname (usually 'tiny_mce.php')
$config['scriptname'] = 'tiny_mce.js';

/**
 * http://wiki.moxiecode.com/index.php/TinyMCE:Configuration
 * theme - тема
 * [advanced,simple]
 */


$config['default'] = array
(
	'theme' => 'advanced',
	'mode' => 'exact',
	'toolbar_location' => 'top',
	'toolbar_align' => 'left',
	'plugins' => array('safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager'),
	'buttons1' => array('save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect'),
	'buttons2' => array('cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor'),
	'buttons3' => array('tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen'),
	'buttons4' => array('insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage'),
);

$config['admin'] = array
(
	'theme' => 'advanced',
	'mode' => 'exact',
	'toolbar_location' => 'top',
	'toolbar_align' => 'left',
	'plugins' => array('safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager'),
	'buttons1' => array('save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect'),
	'buttons2' => array('cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor'),
	'buttons3' => array('tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen'),
	'buttons4' => array('insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage'),

);


$config['article'] = array
(
	'theme' => 'advanced',
	'mode' => 'exact',
	'toolbar_location' => 'top',
	'toolbar_align' => 'left',
	'plugins' => array
	(
		'emotions',
	),
	'buttons1' => array
	(
		'bold', 'italic', 'underline', 'strikethrough', '|',
		'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', 'formatselect',
		'bullist', 'numlist', 'outdent', 'indent', '|',
		'fontselect', 'fontsizeselect',
	),
	'buttons2' => array
	(
		'link', 'unlink', 'anchor', 'image', 'blockquote', '|',
		'undo', 'redo', 'cleanup', 'code', '|',
		'sub', 'sup', 'charmap', '|', 'emotions',
	),
	'buttons3' => array
	(
	),
);


$config['comment'] = array
(
	'theme' => 'advanced',
	'mode' => 'exact',
	'toolbar_location' => 'top',
	'toolbar_align' => 'left',
	'plugins' => array
	(
		'emotions',
	),
	'buttons1' => array
	(
		'bold', 'italic', 'underline', 'strikethrough', '|',
		'bullist', 'numlist', 'emotions', 'blockquote', 'code', 'source'
	),
	'buttons2' => array(),
	'buttons3' => array(),
);
