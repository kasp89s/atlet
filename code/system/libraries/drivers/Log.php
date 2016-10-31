<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Log API driver.
 *
 * $Id: Log.php 4633 2009-09-26 16:25:14Z cbandy $
 *
 * @package    Kohana_Log
 * @author     Kohana Team
 * @copyright  (c) 2007-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
abstract class Log_Driver {

	protected $config = array();

	public function __construct(array $config)
	{
		$this->config = $config;
	}

	abstract public function save(array $messages);
}