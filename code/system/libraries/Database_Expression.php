<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database expression.
 * 
 * $Id: Database_Expression.php 4649 2009-10-20 21:23:00Z cbandy $
 * 
 * @package    Kohana
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Database_Expression_Core {

	protected $expression;

	public function __construct($expression)
	{
		$this->expression = $expression;
	}

	public function __toString()
	{
		return $this->expression;
	}
}
