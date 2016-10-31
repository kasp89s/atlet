<?php
/**
 * Форматирует вывод в колонки
 * Входные данные:
 *   $args[0] или $args['data'] - массив входных данных
 *   $args['cols'] - количество колонок
 * Выходные данные:
 *   массив строк таблицы, в них - массивы ячеек (с исходными данными),
 *   пустые ячейки - false
 * 
 * Полностью идентичен фильтру table_cols (см пример использованию в нем)
 * за одним исключением - форимирует таблицу по стракам.
 *  
 *  Отличие между table_cols_by_line и table_cols
 *  $menu = array('user', 'area', 'place');
 *  после применения'
 *  {filter table_cols_by_line $menu cols=2}
 *  $menu = array(
 * 		array('user', 'area'), 
 * 		array('place', false)
 *	);
 *  в то время как table_cols вернет
 * {filter table_cols $menu cols=2}
 *  $menu = array(
 * 		array('user', 'place'), 
 * 		array('area', false)
 *	);
 * 
 * 
 * @author wild_honey
 * @return array
 * modified by Antuan
 **/
function smarty_function_table_cols2($params, &$smarty){

	if(!$cols = $params['cols']){
		$smarty->trigger_error("column number is not specified");
		return;
	}
	if(!isset($params['data'])){
		$smarty->trigger_error("data is not specified");
		return;
	}
	if(!isset($params['assign'])){
		$smarty->trigger_error("assign var is not specified");
		return;
	}
	
		
	$data = $params['data'];

	$i = 0;
	$out = array();
		
	if(is_array($data) && count($data)){
		foreach ($data as $_){
			$out[$i / $cols][$i % $cols] = $_;
			$i++;
		}
		
		while($i % $cols){
			$out[$i / $cols][$i % $cols] = false;
			$i++;
		}
	}

	
	$smarty->assign($params['assign'], $out);
}


?>
