<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @author Antuan
 * @create 18.09.2009
 *
 * Информатор сообщений. Используется в админке
 * - Если не задано ни одного параметра, то отдается сохранненое сообщение
 * - Если передан один параметр $message, то он сохраняет для одного вывода (после первого редиректа его уже не будет)
 * - Если передан параметр $message и ссылка, то он сохраняет для двух выводов,
 *   чтобы можно было сделать последний вывод после редиректа
 * - Параметр $keep_flash = true нужен для того? чтобы проблить жизнь сообщения (нужен когда посялается запрос с помощью
 *   ajax и страница релодится)
 *
 */
class message_Core {

	public static function info($message = FALSE, $uri = FALSE, $keep_flash = FALSE){
		return message::_message('info_message', $message, $uri, $keep_flash);
	}

	public static function error($message = FALSE, $uri = FALSE, $keep_flash = FALSE){
		return message::_message('error_message', $message, $uri, $keep_flash);
	}

	private static function _message($type = FALSE, $message = FALSE, $uri = FALSE, $keep_flash = FALSE){
		if(!$type)
			return false;

		if($message){
			Session::instance()->set_flash($type, $message);

			if ($uri)
				url::redirect($uri);

		} else {
			return Session::instance()->get($type, FALSE);
		}
	}

}