<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * MarkItUp! driver library
 *
 * @package    Editor
 * @author     Brotkin Ivan (BIakaVeron) <BIakaVeron@gmail.com>
 * @copyright  Copyright (c) 2009 Brotkin Ivan
 *
 */
class Editor_Markitup_Driver extends Editor_Driver {

	/**
	 * Constructor
	 *
	 * Create and set basic properties
	 *
	 * @param  mixed  config array or profile name
	 * @return void
	 */
	protected $defaults = array('path', 'scriptname', 'setspath', 'skinspath');

	public function __construct($config) {

		// Saving configuration
		$this->config = $config;

		if (!isset($this->config['editor']['profile'])) {
			$this->config['editor']['profile'] = 'default';
		}

		$this->config += Kohana::config('editors/markitup.'.$this->config['editor']['profile']);

		foreach($this->defaults as $param) {
			if (!isset($this->config[$param])) {
				$this->config[$param] = Kohana::config('editors/markitup.'.$param);
			}
		}

		Kohana::log('debug', 'MarkItUp Driver Initialized');
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
		$result = html::script(Kohana::config('editor.jquerypath'));
		$result.= html::script($this->config['path'].$this->config['scriptname']);
		$result.= html::stylesheet($this->config['path'].$this->config['setspath'].$this->config['toolbarset'].'/style.css');
		$result.= html::stylesheet($this->config['path'].$this->config['skinspath'].$this->config['skin'].'/style.css');
		$result.= html::script($this->config['path'].$this->config['setspath'].$this->config['toolbarset'].'/set.js');
		if (TRUE == $create_field) {
			// Create textarea with some config values
			$result.= form::textarea(array('name'=>$this->config['fieldname'], 'width'=>$this->config['width'], 'height'=>$this->config['height'], 'value'=>$this->config['value']))."\r\n";
		}

		// Init redactor object
		// Array settings should be joined into a string
		$result .= '<script language="javascript" type="text/javascript">
<!--
$(document).ready(function()	{
	// $("textarea").markItUp( { Settings }, { OptionalExtraSettings } );
	$("#'.$this->config['fieldname'].'").markItUp(mySettings);
});
-->
</script>';

		if ($print===TRUE) {
			// Echo code
			echo $result;
		}

		// return generated code
		return $result;
	}

}
