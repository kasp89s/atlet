<?php defined('SYSPATH') or die('No direct script access.');
/**
 * MySQL database result.
 *
 * $Id: Database_Mysql_Result.php 4633 2009-09-26 16:25:14Z cbandy $
 *
 * @package    Kohana
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Database_Mysql_Result_Core extends Database_Result {

	protected $internal_row = 0;

	public function __construct($result, $sql, $link, $return_objects)
	{
		if (is_resource($result))
		{
			// True to return objects, false for arrays
			$this->return_objects = $return_objects;

			$this->total_rows = mysql_num_rows($result);
		}
		elseif (is_bool($result))
		{
			if ($result == FALSE)
			{
				throw new Database_Exception('#:errno: :error [ :query ]',
					array(':error' => mysql_error($link),
					':query' => $sql,
					':errno' => mysql_errno($link)));

			}
			else
			{
				// It's a DELETE, INSERT, REPLACE, or UPDATE query
				$this->insert_id  = mysql_insert_id($link);
				$this->total_rows = mysql_affected_rows($link);
			}
		}

		// Store the result locally
		$this->result = $result;

		$this->sql = $sql;
	}

	public function __destruct()
	{
		if (is_resource($this->result))
		{
			mysql_free_result($this->result);
		}
	}

	public function as_array($return = FALSE)
	{
		// Return arrays rather than objects
		$this->return_objects = FALSE;

		if ( ! $return )
		{
			// Return this result object
			return $this;
		}

		// Return a nested array of all results
		$array = array();

		if ($this->total_rows > 0)
		{
			// Seek to the beginning of the result
			mysql_data_seek($this->result, 0);

			while ($row = mysql_fetch_assoc($this->result))
			{
				// Add each row to the array
				$array[] = $row;
			}

			$this->internal_row = $this->total_rows;
		}

		return $array;
	}

	public function as_object($class = NULL, $return = FALSE)
	{
		// Return objects of type $class (or stdClass if none given)
		$this->return_objects = ($class !== NULL) ? $class : TRUE;

		if ( ! $return )
		{
			// Return this result object
			return $this;
		}

		// Return a nested array of all results
		$array = array();

		if ($this->total_rows > 0)
		{
			// Seek to the beginning of the result
			mysql_data_seek($this->result, 0);

			if (is_string($this->return_objects))
			{
				while ($row = mysql_fetch_object($this->result, $this->return_objects))
				{
					// Add each row to the array
					$array[] = $row;
				}
			}
			else
			{
				while ($row = mysql_fetch_object($this->result))
				{
					// Add each row to the array
					$array[] = $row;
				}
			}

			$this->internal_row = $this->total_rows;
		}

		return $array;
	}

	/**
	 * SeekableIterator: seek
	 */
	public function seek($offset)
	{
		if ($this->offsetExists($offset) AND mysql_data_seek($this->result, $offset))
		{
			// Set the current row to the offset
			$this->current_row = $this->internal_row = $offset;

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Iterator: current
	 */
	public function current()
	{
		if ($this->current_row !== $this->internal_row AND ! $this->seek($this->current_row))
			return NULL;

		++$this->internal_row;

		if ($this->return_objects)
		{
			if (is_string($this->return_objects))
			{
				return mysql_fetch_object($this->result, $this->return_objects);
			}
			else
			{
				return mysql_fetch_object($this->result);
			}
		}
		else
		{
			// Return an array of the row
			return mysql_fetch_assoc($this->result);
		}
	}

	/**
	 * @created by Antuan
	 */
	public function rows()
	{
		return $this->as_array(TRUE);
	}
	
	/**
	 * @created by Antuan
	 */
	public function row()
	{
		return $this->current();
	}
	
	/**
	 * Returns the insert id
	 *
	 * @return int
	 * @created by Antuan
	 */
	public function insert_id()
	{
		return $this->insert_id;
	}
} // End Database_MySQL_Result