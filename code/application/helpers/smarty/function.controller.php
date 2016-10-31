<?php
/**
 * @author Antuan
 * @create 18.09.2009
 * 
 * Получение названия текущего контроллера
 * 
 **/

function smarty_function_controller($params, &$smarty){

	return Router::$controller;
}


?>
