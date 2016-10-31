<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Class for providing most knowns Text Editors like Tiny_MCE and FCKeditor
 *
 * @package    Editor
 * @author     Brotkin Ivan (BIakaVeron) <BIakaVeron@gmail.com>
 * @copyright  Copyright (c) 2009 Brotkin Ivan
 *
 */

class Editor_Core {

	// Configuration array
	protected $config = array();

	// Text Editor driver object
	protected $driver;

	/**
	 * Create an instance of Editor.
	 *
	 * @param   mixed   configuration array or profile name
	 * @return  __CLASS__
	 */
	public static function factory($config = NULL)
	{
		return new Editor($config);
	}

	/**
	 * Constructor
	 *
	 * @param   mixed   configuration array or profile name
	 * @return  void
	 */
		public function __construct($config = NULL)
		{
			if (is_null($config)) {
				$config = 'default';
			}

			if (is_string($config)) {
				$config = Kohana::config('editor.'.$config);
			}

			$this->driver = 'Editor_'.ucfirst($config['editor']['driver']).'_Driver';

			if (! Kohana::auto_load ( $this->driver ))
			throw new Kohana_Exception('core.driver_not_found', $this->driver, get_class($this));
			// Load the driver
			$this->driver = new $this->driver($config);

			//Kohana::log('debug', 'Editor Library loaded');
		}

	/**
	 * Changing editor width
	 *
	 * @param  int  new width value
	 * @return void
	 */
	public function set_width($width = NULL) {
		$this->driver->set_width($width);
		return $this;
	}

	/**
	 * Changing editor height
	 *
	 * @param  int  new height value
	 * @return void
	 */
	public function set_height($height = NULL) {
		$this->driver->set_height($height);
		return $this;
	}

	/**
	 * Changing text field name
	 *
	 * @param  string new field (textarea) name
	 * @return void
	 */
	public function set_fieldname($fname = NULL) {
		$this->driver->set_fieldname($fname);
		return $this;
	}

	/**
	 * Changing text field value
	 *
	 * @param  string new text value
	 * @return void
	 */
	public function set_value($value = NULL) {
		$this->driver->set_value($value);
		return $this;
	}

	/**
	 * Display text redactor or returns redactor code
	 *
	 * @param   bool   outputs code directly if TRUE
	 * @param   bool   creates textarea field if TRUE
	 * @return  mixed  returns output code if $print==FALSE
	 */
	public function render($print = FALSE, $create_field = TRUE)
	{
		return $this->driver->render($print, $create_field);
	}
}