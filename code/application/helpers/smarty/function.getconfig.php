<?php
/**
 * @author Sinner
 * @create 18.09.2009
 *
 * Получение значения переменной конфига из шаблона.
 **/

function smarty_function_getconfig($params, &$smarty){

	$strResult=Kohana::config($params['path']);

	$assign = !empty($params['assign']) ? $params['assign'] : false;


	if($assign)
		$smarty->assign($assign, $strResult);
	elseif($assign)
		return($strResult);

}


?>
