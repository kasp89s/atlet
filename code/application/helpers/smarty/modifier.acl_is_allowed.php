<?php
/**
 * Проверка прав доступа авторизованного пользователя
 * Используется бибилиотека ACL
 * 
 * @author Antuan
 * @create 08.10.2009
 **/
function smarty_modifier_acl_is_allowed($string, $acl_is_allowed = '') {
	
	$out = Acl::instance()->is_allowed($string);

	return $out;
}

?>
