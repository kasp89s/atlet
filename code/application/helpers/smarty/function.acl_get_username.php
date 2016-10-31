<?php
/**
 * @author Antuan
 * @create 18.09.2009
 * 
 * Получение username авторизованного пользователя. 
 * Используется бибилиотека ACL
 **/

function smarty_function_acl_get_username($params, &$smarty){

	$user = Acl::instance()->get_user();
	$out = "{$user['fio']} [{$user['username']}]";
	
	return $out;

}


?>
