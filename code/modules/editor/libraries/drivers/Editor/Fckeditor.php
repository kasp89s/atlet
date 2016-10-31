<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * FCKeditor driver library
 *
 * @package    Editor
 * @author     Brotkin Ivan (BIakaVeron) <BIakaVeron@gmail.com>
 * @copyright  Copyright (c) 2009 Brotkin Ivan
 *
 */
class Editor_Fckeditor_Driver extends Editor_Driver {

	// editor field name
	protected $instancename;

	// Configuration params that must be save on FCKeditor object as properties
	protected $params = array
	(
		'Width', 'Height', 'ToolbarSet', 'BasePath', 'Value',
	);

	protected $defaults = array
	(
		'basepath', 'scriptname', 'customconfig',
	);

	/**
	 * Constructor
	 *
	 * Create and set basic properties
	 *
	 * @param  mixed  config array or profile name
	 * @return void
	 */
	public function __construct($config) {

		// Saving configuration
		$this->config = $config;

		// Loading driver profile
		if (!isset($this->config['editor']['profile'])) {
			$this->config['editor']['profile'] = 'default';
		}

		$this->config += Kohana::config('editors/fckeditor.'.$this->config['editor']['profile']);

		foreach($this->defaults as $param) {
			if (!isset($this->config[$param])) {
				$this->config[$param] = Kohana::config('editors/fckeditor.'.$param);
			}
		}

		//Kohana::log('debug', 'FCKeditor Driver Initialized');
	}

	/**
	 * Display text redactor or returns redactor code
	 *
	 * @param   bool   outputs code directly if TRUE
	 * @param   bool   creates textarea field if TRUE
	 * @return  mixed  returns output code if $print==FALSE
	 */
	public function render($print = FALSE, $create_field = FALSE) {
		// Loading FCKeditor library
		require_once('./'.$this->config['basepath'].$this->config['scriptname']);
		$this->instancename = $this->config['fieldname'];
		// Create editor object with fieldName param
		$FCKeditor = new FCKeditor($this->instancename);
		// Setting object properties
		foreach($this->params as $param) {
		  if (is_array($this->config[strtolower($param)])) {
				// Join array params into string
				$FCKeditor->$param = '['.implode(",", $this->config[strtolower($param)]).']';
			}
			else $FCKeditor->$param = $this->config[strtolower($param)];
		}
		$FCKeditor->BasePath = url::base().$FCKeditor->BasePath;
		$FCKeditor->Config = $this->config['config'];
		$FCKeditor->Config['CustomConfigurationsPath'] = $FCKeditor->BasePath.$this->config['customconfig'];
		$FCKeditor->Config['AutoDetectLanguage'] = FALSE;
		$FCKeditor->Config['DefaultLanguage'] = 'ru';

		if ($print===TRUE) {
			// Output generated code directly
			$FCKeditor->Create();
		}
		else {
			// Generate code and return as result
			return $FCKeditor->CreateHtml();
		}

		return NULL;
	}
}