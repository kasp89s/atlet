<?php defined('SYSPATH') OR die('No direct access allowed.');

class form_filter {
	
	public static function fill_list($name, $value, &$data, $values = array()){
		$data[$name.'_sel_'.$value] = 'selected';
		$data[$name.'_sel'] = $value;
		
		if(!$values) return;
		foreach ($values as $_) {
			if($_['id'] == $value)
				$_['selected'] = 'selected';
			$data[$name][] = $_;
		}
	}
	
	public static function fill_item($name, $value, &$data){
		if(empty($value))
			$data[$name] = '';
		else
			$data[$name] = $value;
	}
}
