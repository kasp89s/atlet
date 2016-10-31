<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Validation library.
 *
 * $Id: Validation.php 4624 2009-09-17 16:08:30Z isaiah $
 *
 * @package    Validation
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Validation extends Validation_Core {

	/**
	 * Rule: length. Generates an error if the field is too long or too short.
	 *
	 * @param   mixed   input value
	 * @param   array   minimum, maximum, or exact length to match
	 * @return  bool
	 */
	public function value($integer, array $values)
	{
		if ( ! is_integer($integer))
			return FALSE;

		$status=false;

		if (count($values) > 1)
		{
			list ($min, $max) = $values;

			if (($integer >= $min or $min==="") AND ($integer <= $max or $max===""))
			{
				$status = TRUE;
			}
		}
		else
		{
			$status = ($integer === (int) $values[0]);
		}

		return $status;
	}

} // End Validation