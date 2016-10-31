<?php
/**
 * @author Antuan
 * @create 18.09.2009
 * 
 * Работа с системными сообщениями
 **/

function smarty_function_message($params, &$smarty){

	if(!$name = $params['name']){
		$smarty->trigger_error("name is not specified");
		return;
	}
	
	$print = !empty($params['print']) ? $params['print'] : false;
	$assign = !empty($params['assign']) ? $params['assign'] : false;
	
	$out = Session::instance()->get($name.'_message');
	
	if($print)
		return $out;
	elseif($assign)
		$smarty->assign($assign, $out);

}


?>
