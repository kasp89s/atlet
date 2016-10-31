<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Tiny_MCE driver library
 *
 * @package    Editor
 * @author     Brotkin Ivan (BIakaVeron) <BIakaVeron@gmail.com>
 * @copyright  Copyright (c) 2009 Brotkin Ivan
 *
 */
class Editor_Tinymce_Driver extends Editor_Driver {

	/**
	 * Constructor
	 *
	 * Create and set basic properties
	 *
	 * @param  mixed  config array or profile name
	 * @return void
	 */

	protected $defaults = array('path', 'scriptname');

	public function __construct($config) {

		// Saving configuration
		$this->config = $config;

		if (!isset($this->config['editor']['profile'])) {
			$this->config['editor']['profile'] = 'default';
		}

		$this->config += Kohana::config('editors/tinymce.'.$this->config['editor']['profile']);

		foreach($this->defaults as $param) {
			if (!isset($this->config[$param])) {
			// Use config param if there is no $param value in profile
				$this->config[$param] = Kohana::config('editors/tinymce.'.$param);
			}
		}

		//Kohana::log('debug', 'TinyMCE Driver Initialized');
	}

	/**
	 * Display text redactor or returns redactor code
	 *
	 * @param   bool   outputs code directly if TRUE
	 * @param   bool   creates textarea field if TRUE
	 * @return  mixed  returns output code if $print==FALSE
	 */
	public function render($print = FALSE, $create_field = FALSE) {

		// Include JS-file with editor code
		$result = html::script($this->config['path'].$this->config['scriptname']);

		if (TRUE == $create_field) {
			// Create textarea with some config values
			$result.= form::textarea(array('name'=>$this->config['fieldname'], 'width'=>$this->config['width'], 'height'=>$this->config['height'], 'value'=>$this->config['value']))."\r\n";
		}


		// Init redactor object
		// Array settings should be joined into a string
		$result .= '<script language="javascript" type="text/javascript">
	tinyMCE.init({
		theme : "'.$this->config['theme'].'",
		mode: "'.$this->config['mode'].'",
		language: "ru",
		elements : "'.$this->config['fieldname'].'",
		plugins : "'.implode(",", $this->config['plugins']).'",
		theme_advanced_toolbar_location : "'.$this->config['toolbar_location'].'",
		theme_advanced_toolbar_align : "'.$this->config['toolbar_align'].'",
		theme_advanced_buttons1 : "'.implode(",", $this->config['buttons1']).'",
		theme_advanced_buttons2 : "'.implode(",", $this->config['buttons2']).'",
		theme_advanced_buttons3 : "'.implode(",", $this->config['buttons3']).'",
		theme_advanced_buttons4 : "'.implode(",", $this->config['buttons4']).'",
		height:"'.$this->config['height'].'px",
		width:"'.$this->config['width'].'px"
  });
</script>';

		if ($print===TRUE) {
			// Echo code
			echo $result;
		}

		// return generated code
		return $result;
	}

}