<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {sort} function plugin
 *
 * Type:     function<br>
 * Name:     math<br>
 * Purpose:  handle math computations in template<br>
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param array
 * @param Smarty
 * @return string
 * 
 * modified by Antuan
 */
function smarty_function_sort($params, &$smarty) {
  
	if(!count($params))
		$smarty->trigger_error("sort: not enough params");

	$from = $params['from'];
	$var_name = $params['sort'];
	//$var_name = str_replace(array('@', '.', '#'), '_', $var_name);
	
	if(!empty($params['name']))
		$name = $params['name'];
	else
		$name = $params['name'];
		
		
    $assigned_vars = $smarty->_tpl_vars;
    $sort_href = $assigned_vars[$from]['sort'][$var_name.'_href'];
    $sort_img = $assigned_vars[$from]['sort'][$var_name.'_img']['href'];
    
    if($sort_img)
    	return '<a href="'.$sort_href.'">'.$name.'</a> <a href="'.$sort_href.'"><img src="'.$sort_img.'" align="absmiddle" border=0 /></a>';
    else 
    	return '<a href="'.$sort_href.'">'.$name.'</a>';
}

/* vim: set expandtab: */

?>