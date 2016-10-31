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
 * Пример использования в шаблоне:
 * {filter table_cols $countries cols=4}
 * <table>
 * {loop $countries}
 * <tr>
 * 	{loop $#}
 * 		<td>
 * 		{if $#false}Пусто :(
 * 		{else}
 * 			{$name}
 * 		{/if}
 * 		</td>
 *  {/loop}
 * </tr>
 * {/loop}
 * </table>;
 * в этом случае $countries после вызова будет представлять
 * array(
 *   array(
 *     array( 'name' => 'Азейрбаджан1', 'id' => 123 ),
 *     array( 'name' => 'Азейрбаджан2', 'id' => 1234 ),
 *     array( 'name' => 'Азейрбаджан3', 'id' => 1235 ),
 *     array( 'name' => 'Азейрбаджан4', 'id' => 1236 ),
 *   ),
 *   ...
 *   array(
 * 		array( 'name' => 'Зимбабве', 'id' => 12345),
 * 		false,
 * 		false,
 * 		false,
 *   )
 * )
 * 
 * {filter table_cols $countries cols=4 result=$countries_rows}
 * В этом случае $countries останется неизменным, а данные будут записаны в
 * $countries_rows
 * 
 * @author DoK
 * @return array
 * modified by Antuan
 **/

function smarty_function_table_cols($params, &$smarty){

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
		$rows = ceil(count($data) / $cols);
		
		foreach ($data as $_){
			$out[$i % $rows][$i / $rows] = $_;
			$i++;
		}
		
		while($i % $rows){
			$out[$i % $rows][$i / $rows] = false;
			$i++;
		}
	}
	
	$smarty->assign($params['assign'], $out);
}


?>
