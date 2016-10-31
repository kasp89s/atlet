<?php
class security extends security_Core {

	/**
	 * Дополнительная защита от xss-атак
	 * Использовать для входящих строковых переменных
	 *
	 * @param string $str
	 * @return string
	 */
	public static function htmlspecialchars($str){
		$search = array('"', "'", "«", "»");
		$replace = array('&#034;', "&#039;", "&#171;", "&#187;");
		$str = str_replace($search, $replace, $str);
		
		return $str;
	}


} 