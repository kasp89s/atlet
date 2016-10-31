<?php
/**
 * Задается относительный путь.
 *
 */
$config['files_dir'] = "/upload/";


/**
 * Максимальное количество файлов в одной папке.
 *
 */
$config['max_files_in_dir'] = 600;


/**
 * Максимальный размер файлов в папке(суммарный размер) в мегабайтах
 *
 */
$config['max_dir_size'] = FALSE;


/**
 * Длина названия папки в символах. Актуально для вновь создаваемых папок.
 * В любой момент может быть изменено. Изменение в меньшую сторону может привести в невозможности создать папку.
 *
 */
$config['dir_name_len'] = 10;


/**
 * Список запрещенных к загрузке расширений и их замена
 *
 */
$config['denied_extentions'] = array(
	'php'    => 'php.bak',
	'html'   => 'html.bak',
	'htm'    => 'htm.bak',
	'xml'    => 'xml.bak',
	'js'     => 'js.bak',
	'css'    => 'css.bak'
);


/**
 * Размеры для превью-картинок (для картинок)
 *
 */
$config['imagesPreview1Size'] = "400x400";
$config['imagesPreview2Size'] = "150x150";
$config['imagesPreview3Size'] = "100x100";


/**
 * Размеры для виде и его превьшек
 *
 */
$config['videoSize']         = "480x360";
$config['videoPreview1Size'] = "480x360";
$config['videoPreview2Size'] = "200x200";
$config['videoPreview3Size'] = "100x100";


/**
 * Пути к библиотекам
 *
 */
$config['path_ffmpeg'] = DOCROOT . 'tools/ffmpeg.exe';
$config['path_yamdi'] = DOCROOT . 'tools/yamdi.exe';

?>
