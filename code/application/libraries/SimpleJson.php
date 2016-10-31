<?php
/**
 * Легковесный конвертор в JSON формат
 *
 * Пример передачи данных
 * <?php
 * $data = array('toyota', 'mazda', 'honda');
 * $obj_json = new SimpleJson();
 * $obj_json->done($data);
 * ?>
 */
class SimpleJson {
	
	/**
	 * Легковесный конвертор в JSON формат
	 *
	 * @param bool $start Указывает начинать ли сбор данных в output buffer
	 * @return cls_simple_json
	 */
	function __construct($start = true){
		if($start)
			$this->start();
			
	}
	
	function start(){
		ob_start();
	}
	
	/**
	 * Передает данные клиенту
	 * 
	 * Также выводит содержимое output buffer на клиенте алертом, 
	 * что может использоваться для отладочных целей
	 *
	 * @param mixed $result данные для передачи клиенту
	 */
	function done($result){
		print $this->encode($result);
		exit();
	}
	
	/**
	 * Кодирует данные в формате JSON
	 *
	 * @param mixed $var
	 * @return string
	 */
	function encode($var){
		if(is_array($var)){
			reset($var);
			$first = key($var);
			end($var);
			$last = key($var);
			if(!count($var) || is_int($first) && is_int($last) && $first == 0 && $last == count($var) - 1){
				$out = "[\n";
				foreach ($var as $k => $v) {
					if($out !== "[\n") $out .= ",\n";
					$out .= $this->encode($v);
				}
				$out .= "\n]";
			}
			else{
				$out = "{\n";
				foreach ($var as $k => $v) {
					if($out !== "{\n") $out .= ",\n";
					$out .= "'$k' : ".$this->encode($v);
				}
				$out .= "\n}";
			}
		}
		elseif(is_int($var)){
			$out = $var;
		}
		else{
			$out = "'".addcslashes($var, "\n\r\t'\"")."'";
		}
		
		return $out;
	}
}

?>