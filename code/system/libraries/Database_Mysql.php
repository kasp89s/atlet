<?php defined('SYSPATH') or die('No direct script access.');
/**
 * MySQL database connection.
 *
 * $Id: Database_Mysql.php 4645 2009-10-13 22:14:09Z cbandy $
 *
 * @package    Kohana
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Database_Mysql_Core extends Database {

	// Quote character to use for identifiers (tables/columns/aliases)
	protected $quote = '`';

	public function connect()
	{
		if ($this->connection)
			return;

		extract($this->config['connection']);

		// Set the connection type
		$connect = ($this->config['persistent'] === TRUE) ? 'mysql_pconnect' : 'mysql_connect';

		$host = isset($host) ? $host : $socket;
		$port = isset($port) ? ':'.$port : '';

		try
		{
			// Connect to the database
			$this->connection = $connect($host.$port, $user, $pass, TRUE);
		}
		catch (Kohana_PHP_Exception $e)
		{
			// No connection exists
			$this->connection = NULL;

			// Unable to connect to the database
			throw new Database_Exception('#:errno: :error',
				array(':error' => mysql_error(),
				':errno' => mysql_errno()));
		}

		if ( ! mysql_select_db($database, $this->connection))
		{
			// Unable to select database
			throw new Database_Exception('#:errno: :error',
				array(':error' => mysql_error($this->connection),
				':errno' => mysql_errno($this->connection)));
		}

		if (isset($this->config['character_set']))
		{
			// Set the character set
			$this->set_charset($this->config['character_set']);
		}
	}

	public function disconnect()
	{
		try
		{
			// Database is assumed disconnected
			$status = TRUE;

			if (is_resource($this->connection))
			{
				$status = mysql_close($this->connection);
				
				/**
				 * @modified by Antuan
				 */
				$this->connection = NULL;
			}
		}
		catch (Exception $e)
		{
			// Database is probably not disconnected
			$status = is_resource($this->connection);
		}

		return $status;
	}

	public function set_charset($charset)
	{
		// Make sure the database is connected
		$this->connection or $this->connect();

		if ( ! mysql_set_charset($charset, $this->connection))
		{
			// Unable to set charset
			throw new Database_Exception('#:errno: :error',
				array(':error' => mysql_error($this->connection),
				':errno' => mysql_errno($this->connection)));
		}
	}

	public function query_execute($sql)
	{
		// Make sure the database is connected
		$this->connection or $this->connect();

		$result = mysql_query($sql, $this->connection);

		// Set the last query
		$this->last_query = $sql;

		return new Database_Mysql_Result($result, $sql, $this->connection, $this->config['object']);
	}

	public function escape($value)
	{
		// Make sure the database is connected
		$this->connection or $this->connect();

		if (($value = mysql_real_escape_string($value, $this->connection)) === FALSE)
		{
			throw new Database_Exception('#:errno: :error',
				array(':error' => mysql_error($this->connection),
				':errno' => mysql_errno($this->connection)));
		}

		return $value;
	}

	public function list_constraints($table)
	{
		$prefix = strlen($this->table_prefix());
		$result = array();

		$constraints = $this->query('
			SELECT c.constraint_name, c.constraint_type, k.column_name, k.referenced_table_name, k.referenced_column_name
			FROM information_schema.table_constraints c
			JOIN information_schema.key_column_usage k ON (k.table_schema = c.table_schema AND k.table_name = c.table_name AND k.constraint_name = c.constraint_name)
			WHERE c.table_schema = '.$this->quote($this->config['connection']['database']).'
				AND c.table_name = '.$this->quote($this->table_prefix().$table).'
				AND (k.referenced_table_schema IS NULL OR k.referenced_table_schema ='.$this->quote($this->config['connection']['database']).')
			ORDER BY k.ordinal_position
		');

		foreach ($constraints->as_array() as $row)
		{
			switch ($row['constraint_type'])
			{
				case 'FOREIGN KEY':
					if (isset($result[$row['constraint_name']]))
					{
						$result[$row['constraint_name']][1][] = $row['column_name'];
						$result[$row['constraint_name']][3][] = $row['referenced_column_name'];
					}
					else
					{
						$result[$row['constraint_name']] = array($row['constraint_type'], array($row['column_name']), substr($row['referenced_table_name'], $prefix), array($row['referenced_column_name']));
					}
				break;
				case 'PRIMARY KEY':
				case 'UNIQUE':
					if (isset($result[$row['constraint_name']]))
					{
						$result[$row['constraint_name']][1][] = $row['column_name'];
					}
					else
					{
						$result[$row['constraint_name']] = array($row['constraint_type'], array($row['column_name']));
					}
				break;
			}
		}

		return $result;
	}

	public function list_fields($table)
	{
		$result = array();

		foreach ($this->query('SHOW COLUMNS FROM '.$this->quote_table($table))->as_array() as $row)
		{
			$column = $this->sql_type($row['Type']);

			$column['default'] = $row['Default'];
			$column['nullable'] = $row['Null'] === 'YES';
			$column['sequenced'] = $row['Extra'] === 'auto_increment';

			if (isset($column['length']) AND $column['type'] === 'float')
			{
				list($column['precision'], $column['scale']) = explode(',', $column['length']);
			}

			$result[$row['Field']] = $column;
		}

		return $result;
	}

	public function list_tables()
	{
		$prefix = strlen($this->table_prefix());
		$tables = array();

		foreach ($this->query('SHOW TABLES FROM '.$this->escape($this->config['connection']['database']).' LIKE '.$this->quote($this->table_prefix().'%'))->as_array() as $row)
		{
			// The value is the table name
			$tables[] = substr(current($row), $prefix);
		}

		return $tables;
	}

} // End Database_MySQL
