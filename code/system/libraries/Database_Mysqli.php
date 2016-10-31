<?php defined('SYSPATH') or die('No direct script access.');
/**
 * MySQL database connection.
 * 
 * $Id: Database_Mysqli.php 4640 2009-10-08 21:30:59Z cbandy $
 * 
 * @package    Kohana
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */

define('RUNS_MYSQLND', function_exists('mysqli_fetch_all'));

class Database_Mysqli_Core extends Database_Mysql {

	public function connect()
	{
		if (is_object($this->connection))
			return;

		extract($this->config['connection']);

		// Persistent connections are supported as of PHP 5.3
		if (RUNS_MYSQLND AND $this->config['persistent'] === TRUE)
		{
			$host = 'p:'.$host;
		}

		$host = isset($host) ? $host : $socket;

		if($this->connection = new mysqli($host, $user, $pass, $database, $port)) {
			
			if (isset($this->config['character_set']))
			{
				// Set the character set
				$this->set_charset($this->config['character_set']);
			}
			
			// Clear password after successful connect
			$this->db_config['connection']['pass'] = NULL;
			
			return $this->connection;
		}

		// Unable to connect to the database
			throw new Database_Exception('#:errno: :error',
				array(':error' => $this->connection->connect_error,
				':errno' => $this->connection->connect_errno));
	}

	public function disconnect()
	{
		return is_object($this->connection) and $this->connection->close();
	}

	public function set_charset($charset)
	{
		// Make sure the database is connected
		is_object($this->connection) or $this->connect();

		if ( ! $this->connection->set_charset($charset))
		{
			// Unable to set charset
			throw new Database_Exception('#:errno: :error',
				array(':error' => $this->connection->connect_error,
				':errno' => $this->connection->connect_errno));
		}
	}

	public function query_execute($sql)
	{
		// Make sure the database is connected
		is_object($this->connection) or $this->connect();

		$result = $this->connection->query($sql);

		// Set the last query
		$this->last_query = $sql;

		return new Database_Mysqli_Result($result, $sql, $this->connection, $this->config['object']);
	}

	public function escape($value)
	{
		// Make sure the database is connected
		is_object($this->connection) or $this->connect();

		return $this->connection->real_escape_string($value);
	}

} // End Database_MySQLi
