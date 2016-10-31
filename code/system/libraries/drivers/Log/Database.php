<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Log API driver.
 *
 * $Id: Database.php 4633 2009-09-26 16:25:14Z cbandy $
 *
 * @package    Kohana_Log
 * @author     Kohana Team
 * @copyright  (c) 2007-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Log_Database_Driver extends Log_Driver {

	public function save(array $messages)
	{
		$insert = db::build($this->config['group'])
						->insert($this->config['table'])
						->columns(array('date', 'level', 'message'));

		$run_insert = FALSE;

		foreach ($messages AS $message)
		{
			if ($this->config['log_levels'][$message['type']] <= $this->config['log_threshold'])
			{
				// Add new message to database
				$insert->values($message);

				// There is data to insert
				$run_insert = TRUE;
			}
		}

		// Update the database
		if ($run_insert)
		{
			$insert->execute();
		}
	}
}