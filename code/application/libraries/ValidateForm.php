<?php
/**
 * Бибилиотека валидации формы.
 * Основывает на проверке системной библиотекой Kohana::Validate
 * и на модификаторах
 *
 * @author Antuan
 */
class ValidateForm {

	public $fields = array();
	public $defaults = array();

	public $form = array();
	public $files = array();
	public $data = array();
	public $fill = array();
	public $fill_ex = array();
	public $key = array();
	public $errors = array();

	/**
	 *  @var cls_field
	 **/
	public $current_field;


	public function __construct() {
	}


	function & get_form($data = NULL){
		if(!is_null($data))
			$this->data = $data;

		unset($this->form);
		$this->form = array();

		$this->_make_form();
		return $this->form;
	}

	function & get_data($form = NULL, $files = NULL){
		if(is_null($form))
			$this->form = $_POST;
		else
			$this->form = $form;

		if(is_null($files))
			$this->files = $_FILES;
		else
			$this->files = $files;

		unset($this->data);
		$this->data = $this->fill = $this->key = array();

		$this->_make_data();
		return $this->data;
	}

	function get_file($name){
		return $this->files[$name];
	}

	function validate(){
		$this->errors = array();

		$this->_validate();

		if(!$this->is_ok())
			$this->set_error('validate');

		return empty($this->errors);
	}

	function & get_fill($key = NULL){
		if(is_null($key))
			return $this->fill;
		else
			return $this->fill_ex[$key];
	}

	function & get_key(){ return $this->key; }

	function & get_errors(){ return $this->errors; }

	function &add_field($name, $modifiers = array(), $checkers = array(), $data_name = NULL){
		$field = new cls_field($name, $modifiers, $checkers, $data_name);
		$this->fields[] = &$field;
		return $this->fields[count($this->fields) - 1];
	}


	function set_error($name){
		$this->errors['err_'.$name] = 'error';
	}

	function add_error($name){
		$this->set_error($name);
	}

	function set_field_err($field){
		$this->errors['err_'.$field] = 'error';
	}

	function set_field_err_ext($field, $error){
		$this->errors['err_'.$field.'_'.$error] = 'error';
		$this->errors['err_'.$field] = 'error';
	}

	function is_ok(){
		return empty($this->errors);
	}


	function _make_form(){
		foreach($this->fields as $field){
			$this->current_field = &$field;
			$this->form[$field->form_name] = isset($this->data[$field->data_name]) ? $this->data[$field->data_name] : null;
			$value = &$this->form[$field->form_name];

			foreach($this->current_field->modifiers as $_){
				if(is_object($_)){
					if($_->modify($value, true, $this)) break;
				}
				elseif($_{0} == '@'){
					$t = substr($_, 1);
					$mod = new $t();
					if($mod->modify($value, true, $this)) break;
				}
				else{
					if($this->{'modify_'.$_}($value, true)) break;
				}
			}
		}
	}

	function _make_data(){
		foreach($this->fields as $field){
			$this->current_field = &$field;
			$this->data[$field->data_name] = isset($this->form[$field->form_name]) ? $this->form[$field->form_name] : null;
			$value = &$this->data[$field->data_name];
			$this->fill[$field->data_name] = &$value;

			foreach($this->current_field->modifiers as $_){
				if(is_object($_)){
					if($_->modify($value, false, $this)) break;
				}
				elseif($_{0} == '@'){
					$t = substr($_, 1);
					$mod = new $t();
					if($mod->modify($value, false, $this)) break;
				}
				else{
					if($this->{'modify_'.$_}($value, false)) break;
				}
			}
		}
	}

	function _validate(){
		if(isset($_FILES) && is_array($_FILES))
			$data = array_merge($this->data, $_FILES);
		else
			$data = $this->data;

		$valid = new Validation($data);

		foreach($this->fields as $field){
			$_required = false;

			foreach($field->checkers as $_){
				if($_ == 'required') $_required = true;
			}


			foreach($field->modifiers as $_){
				if(is_object($_)){
					$_->required = $_required;
					if(!$_->check($this->data[$field->data_name], $this))
						$this->set_field_err_ext($field->form_name, get_class($_));
				}
			}

			foreach($field->checkers as $_){
				if(is_object($_)){
					if(!$_->check($this->data[$field->data_name], $this)){
						$this->set_field_err_ext($field->form_name, get_class($_));
					}
				} elseif($_{0} == '@'){
					$t = substr($_, 1);
					$mod = new $t();
					if(!$mod->check($this->data[$field->data_name], $this)){
						$this->set_field_err_ext($field->form_name, get_class($mod));
					}
				} else {
					$valid->add_rules($field->data_name, $_);
				}
			}
		}

		$valid->validate();
		$errors = $valid->errors();
		foreach ($errors as $field => $error){
			$this->set_field_err_ext($field, $error);
		}
	}


	function modify_int(&$value){
		$value = (int)$value;
	}


	function modify_no_zero(&$value){
		if($value == 0)
			$value = '';
	}

	function modify_float(&$value, $d2f){
		$value = str_replace(',', '.', $value);
		if(!$d2f){
			$value = floatval($value);
		}
	}

	function modify_string(&$value, $d2f){
		if($d2f){
			$value = trim(str_replace('"', '&#34;', $value));
		}
		else{
			$value = trim(strip_tags($value));
			$search = array('"', "'", "«", "»");
			$replace = array('&#034;', "&#039;", "&#171;", "&#187;");
			$value=str_replace($search, $replace, $value);
		}
	}

	function modify_key(&$value, $d2f){
		if(!$d2f){
			$this->key[$this->current_field->data_name] = &$value;
			unset($this->fill[$this->current_field->data_name]);
		}
	}

	function modify_checkbox(&$value, $d2f){
		if($d2f){
			$value = $value?'checked':'';
		}
		else
			$value = (int)$value;
	}

	function modify_no_fill($value, $d2f){
		if(!$d2f){
			unset($this->fill[$this->current_field->data_name]);
		}
	}

	function modify_no_form($value, $d2f){
		if($d2f){
			unset($this->form[$this->current_field->form_name]);
			return true;
		}
	}

	function modify_url(&$value){
        if(substr($value, 0, 7) != 'http://' and !empty($value))
        	$value = 'http://'.$value;
    }

    function modify_html(&$value, $d2f){
    	settype($value, 'string');
    }

    function modify_empty_null(&$value){
    	if(empty($value))
    		$value = null;
    }

}

class cls_field{
	var $form_name;
	var $data_name;
	var $modifiers;
	var $checkers;

	function __construct($form_name, $modifiers, $checkers, $data_name = NULL){
		$this->form_name = $form_name;

		if(!is_array($modifiers))
			$modifiers = isset($modifiers)?array($modifiers):array();
		$this->modifiers = $modifiers;

		if(!is_array($checkers))
			$checkers = isset($checkers)?array($checkers):array();
		$this->checkers = $checkers;

		if(is_null($data_name))
			$this->data_name = $this->form_name;
		else
			$this->data_name = $data_name;
	}

	function modify($mod){
		$this->modifiers[] = $mod;
		return $this;
	}
}


class mod_int2 {
    var $value_def;

    function __construct($value_def) {
        $this->value_def = $value_def;
    }


    function modify(&$value, $d2f, &$form){
        if(strlen($value)==0) $value = $this->value_def;
        $value = (int)$value;

        return $value;
    }

    function check($value){
        return true;
    }
}


class mod_string2 {
    var $value_def;

    function __construct($value_def) {
        $this->value_def = $value_def;
    }


    function modify(&$value, $d2f, &$form){
    	if(strlen($value)==0) $value = $this->value_def;

        if($d2f){
			$value = trim(str_replace('"', '&#34;', $value));
		}
		else{
			$value = trim(strip_tags($value));
			$search = array('"', "'", "«", "»");
			$replace = array('&#034;', "&#039;", "&#171;", "&#187;");
			$value=str_replace($search, $replace, $value);
		}
    }

    function check($value){
        return true;
    }
}


class mod_list{
	var $values;
	var $key;
	var $mark;
	var $required = true;

	function __construct($values, $key = 'id', $mark = 'selected'){
		$this->values = &$values;
		$this->key = $key;
		$this->mark = $mark;
	}

	function modify(&$value, $d2f, &$form){
		if($d2f){
			$old = $value;
			$selIndex = NULL;

			$value = array();
			foreach($this->values as $key => $_){
				if($_[$this->key] == $old || (!$old && isset($_['is_default']) && $_['is_default'])){
					$_['selected'] = $this->mark;
					$old = $_[$this->key];
					$selIndex = $key;
				}

				$value[] = $_;
			}

			$form->form[$form->current_field->form_name.'_sel_'.$old] = $this->mark;
			$form->form[$form->current_field->form_name.'_sel'] = $old;
			$form->form[$form->current_field->form_name.'_sel_indx'] = $selIndex;
		}
	}


	function check($value){
		if($value == 0 && !$this->required)
			return true;

		foreach((array)$this->values as $_){
			if($_[$this->key] == $value){
				return true;
			}
		}
		return false;
	}
}


class mod_rel{
	var $source;
	var $key;
	var $int;

	function __construct(&$source, $key = 'id', $int = true){
		$this->source = &$source;
		$this->key = $key;
		$this->int = $int;
	}

	function modify(&$value, $d2f, &$form){
		if($d2f){
			if(!is_array($this->source)){
				$this->source->select();
				$data = &$this->source->get_rows();
			}
			else
				$data = $this->source;

			if(is_array($value))
				$hash = array_flip($value);
			else
				$hash = array();

			$value = array();
			foreach ($data as $_) {
				if(isset($hash[$_[$this->key]])){
					$_['selected'] = 'checked';
				}

				$value[] = $_;
			}
		}
		else{
			if($this->int)
				$value = int_array($value);
			unset($form->fill[$form->current_field->data_name]);
		}
	}

	function check($value){
		return true;
	}
}


class mod_date {
	var $form_format;
	var $data_format;


	function mod_date($form_format = 'd-m-Y', $data_format = 'Y-m-d H:i:s') {

		$this->form_format = $form_format;
		$this->data_format = $data_format;

	}


	function modify(&$value, $d2f, &$form){
		if($d2f){
			$date = $this->parse_date($this->data_format,$value);

			if ($date!=false) {
				$value = $this->make_date($this->form_format,$date);
			} else {
				$value = '';
			}
		}
		else {
			$date = $this->parse_date($this->form_format,$value);

			if ($date!=false) {
				$value = $this->make_date($this->data_format,$date);
			} else {
				$value = '';
			}
		}
	}


	function parse_date($format, $date) {

		if($format == 'U') {
			if(empty($date))
				return '';
			else
				return array(
					's'=>date('s', $date),
					'i'=>date('i', $date),
					'y'=>date('Y', $date),
					'h'=>date('H', $date),
					'm'=>date('m', $date),
					'd'=>date('d', $date),
				);
		}

		$out = array('s'=>0,'i'=>0,'y'=>0,'h'=>0,'m'=>0,'d'=>0);

		$len = strlen($format);
		$j = 0;
		$i = 0;

		if(strstr($format,'M')){
			$months = array(
							'JAN' => '01',
							'FEB' => '02',
							'MAR' => '03',
							'APR' => '04',
							'MAY' => '05',
							'JUN' => '06',
							'JUL' => '07',
							'AUG' => '08',
							'SEP' => '09',
							'OCT' => '10',
							'NOV' => '11',
							'DEC' => '12'
						);
		}

		while($i < $len){
			switch($format{$i}){
			case 's':
				$out['s'] = (int)substr($date, $j, 2);
				$j += 2;
				break;
			case 'i':
				$out['i'] = (int)substr($date, $j, 2);
				$j += 2;
				break;
			case 'y':
				$out['y'] = (int)substr($date, $j, 2);

				if ($out['y']<70)
					$out['y'] += 2000;
				else
					$out['y'] += 1900;

				$j += 2;
				break;
			case 'H':
			case 'h':
				$out['h'] = (int)substr($date, $j, 2);
				$j += 2;
				break;
			case 'Y':
				$out['y'] = (int)substr($date, $j, 4);
				$j += 4;
				break;
			case 'm':
				$out['m'] = (int)substr($date, $j, 2);
				$j += 2;
				break;
			case 'd':
				$out['d'] = (int)substr($date, $j, 2);
				$j += 2;
				break;
			case 'M':
				$out['m'] = (int)$months[strtoupper(substr($date, $j, 3))];
				$j += 3;
				break;
			default:
				if(!isset($date{$j}) or ($format{$i} != $date{$j}))
					return false;
				else
					$j +=1;
				break;
			}
			$i++;
		}

		return $out;
	}



	function make_date($format,$date) {

		if($format == 'U')
			return mktime($date['h'], $date['i'], $date['s'], $date['m'], $date['d'], $date['y']);

		$out = '';

		$i = 0;
		$len = strlen($format);

		while($i < $len){
			switch($format{$i}){
			case 'Y':
				$out .= sprintf('%04d',$date['y']);
				break;
			case 'y':
				$out .= sprintf('%02d',$date['y']%100);
				break;
			case 'm':
				$out .= sprintf('%02d',$date['m']);
				break;
			case 'd':
				$out .= sprintf('%02d',$date['d']);
				break;
			case 'H':
				$out .= sprintf('%02d',$date['h']);
				break;
			case 'i':
				$out .= sprintf('%02d',$date['i']);
				break;
			case 's':
				$out .= sprintf('%02d',$date['s']);
				break;
			case 'h':
				$out .= sprintf('%02d', $date['h']%12?$date['h']%12:12);
				break;
			default:
				$out .= $format{$i};
			}
			$i++;
		}

		return $out;
	}

	function check($value){
		return true;
	}
}



