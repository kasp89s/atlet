<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database helper class.
 *
 * $Id: $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
 class db_Core {

	public static function query($sql)
	{
		$query = new Database_Query($sql);
		return $query->execute();
	}

	public static function build($database = 'default')
	{
		return new Database_Builder($database);
	}

	public static function select($columns = NULL)
	{
		return db::build()->select($columns);
	}

	public static function insert($table = NULL, $set = NULL)
	{
		return db::build()->insert($table, $set)->execute();
	}

	public static function update($table = NULL, $set = NULL, $where = NULL)
	{
		return db::build()->update($table, $set, $where)->execute();
	}

	public static function delete($table = NULL, $where = NULL)
	{
		return db::build()->delete($table, $where)->execute();
	}

	public static function expr($expression)
	{
		return new Database_Expression($expression);
	}

} // End db
