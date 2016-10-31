<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Text helper class.
 *
 * $Id: text.php 4655 2009-11-04 14:30:08Z isaiah $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class text_Core {

	/**
	 * Limits a phrase to a given number of words.
	 *
	 * @param   string   phrase to limit words of
	 * @param   integer  number of words to limit to
	 * @param   string   end character or entity
	 * @return  string
	 */
	public static function limit_words($str, $limit = 100, $end_char = NULL)
	{
		$limit = (int) $limit;
		$end_char = ($end_char === NULL) ? '&#8230;' : $end_char;

		if (trim($str) === '')
			return $str;

		if ($limit <= 0)
			return $end_char;

		preg_match('/^\s*+(?:\S++\s*+){1,'.$limit.'}/u', $str, $matches);

		// Only attach the end character if the matched string is shorter
		// than the starting string.
		return rtrim($matches[0]).(strlen($matches[0]) === strlen($str) ? '' : $end_char);
	}

	/**
	 * Limits a phrase to a given number of characters.
	 *
	 * @param   string   phrase to limit characters of
	 * @param   integer  number of characters to limit to
	 * @param   string   end character or entity
	 * @param   boolean  enable or disable the preservation of words while limiting
	 * @return  string
	 */
	public static function limit_chars($str, $limit = 100, $end_char = NULL, $preserve_words = FALSE)
	{
		$end_char = ($end_char === NULL) ? '&#8230;' : $end_char;

		$limit = (int) $limit;

		if (trim($str) === '' OR mb_strlen($str) <= $limit)
			return $str;

		if ($limit <= 0)
			return $end_char;

		if ($preserve_words == FALSE)
		{
			return rtrim(mb_substr($str, 0, $limit)).$end_char;
		}

		preg_match('/^.{'.($limit - 1).'}\S*/us', $str, $matches);

		return rtrim($matches[0]).(strlen($matches[0]) == strlen($str) ? '' : $end_char);
	}

	/**
	 * Alternates between two or more strings.
	 *
	 * @param   string  strings to alternate between
	 * @return  string
	 */
	public static function alternate()
	{
		static $i;

		if (func_num_args() === 0)
		{
			$i = 0;
			return '';
		}

		$args = func_get_args();
		return $args[($i++ % count($args))];
	}

	/**
	 * Generates a random string of a given type and length.
	 *
	 * @param   string   a type of pool, or a string of characters to use as the pool
	 * @param   integer  length of string to return
	 * @return  string
	 *
	 * @tutorial  alnum     alpha-numeric characters
	 * @tutorial  alpha     alphabetical characters
	 * @tutorial  hexdec    hexadecimal characters, 0-9 plus a-f
	 * @tutorial  numeric   digit characters, 0-9
	 * @tutorial  nozero    digit characters, 1-9
	 * @tutorial  distinct  clearly distinct alpha-numeric characters
	 */
	public static function random($type = 'alnum', $length = 8)
	{
		$utf8 = FALSE;

		switch ($type)
		{
			case 'alnum':
				$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
			case 'alpha':
				$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
			case 'hexdec':
				$pool = '0123456789abcdef';
			break;
			case 'numeric':
				$pool = '0123456789';
			break;
			case 'nozero':
				$pool = '123456789';
			break;
			case 'distinct':
				$pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
			break;
			default:
				$pool = (string) $type;
				$utf8 = ! text::is_ascii($pool);
			break;
		}

		// Split the pool into an array of characters
		$pool = ($utf8 === TRUE) ? utf8::str_split($pool, 1) : str_split($pool, 1);

		// Largest pool key
		$max = count($pool) - 1;

		$str = '';
		for ($i = 0; $i < $length; $i++)
		{
			// Select a random character from the pool and add it to the string
			$str .= $pool[mt_rand(0, $max)];
		}

		// Make sure alnum strings contain at least one letter and one digit
		if ($type === 'alnum' AND $length > 1)
		{
			if (ctype_alpha($str))
			{
				// Add a random digit
				$str[mt_rand(0, $length - 1)] = chr(mt_rand(48, 57));
			}
			elseif (ctype_digit($str))
			{
				// Add a random letter
				$str[mt_rand(0, $length - 1)] = chr(mt_rand(65, 90));
			}
		}

		return $str;
	}

	/**
	 * Reduces multiple slashes in a string to single slashes.
	 *
	 * @param   string  string to reduce slashes of
	 * @return  string
	 */
	public static function reduce_slashes($str)
	{
		return preg_replace('#(?<!:)//+#', '/', $str);
	}

	/**
	 * Replaces the given words with a string.
	 *
	 * @param   string   phrase to replace words in
	 * @param   array    words to replace
	 * @param   string   replacement string
	 * @param   boolean  replace words across word boundries (space, period, etc)
	 * @return  string
	 */
	public static function censor($str, $badwords, $replacement = '#', $replace_partial_words = TRUE)
	{
		foreach ((array) $badwords as $key => $badword)
		{
			$badwords[$key] = str_replace('\*', '\S*?', preg_quote((string) $badword));
		}

		$regex = '('.implode('|', $badwords).')';

		if ($replace_partial_words === FALSE)
		{
			// Just using \b isn't sufficient when we need to replace a badword that already contains word boundaries itself
			$regex = '(?<=\b|\s|^)'.$regex.'(?=\b|\s|$)';
		}

		$regex = '!'.$regex.'!ui';

		if (mb_strlen($replacement) == 1)
		{
			$regex .= 'e';
			return preg_replace($regex, 'str_repeat($replacement, mb_strlen(\'$1\'))', $str);
		}

		return preg_replace($regex, $replacement, $str);
	}

	/**
	 * Finds the text that is similar between a set of words.
	 *
	 * @param   array   words to find similar text of
	 * @return  string
	 */
	public static function similar(array $words)
	{
		// First word is the word to match against
		$word = current($words);

		for ($i = 0, $max = strlen($word); $i < $max; ++$i)
		{
			foreach ($words as $w)
			{
				// Once a difference is found, break out of the loops
				if ( ! isset($w[$i]) OR $w[$i] !== $word[$i])
					break 2;
			}
		}

		// Return the similar text
		return substr($word, 0, $i);
	}

	/**
	 * An alternative to the php levenshtein() function that work out the
	 * distance between 2 words using the Damerau–Levenshtein algorithm.
	 * Credit: http://forums.devnetwork.net/viewtopic.php?f=50&t=89094
	 *
	 * @see http://en.wikipedia.org/wiki/Damerau%E2%80%93Levenshtein_distance
	 * @param     string    first word
	 * @param     string    second word
	 * @return    int       distance between words
	 */
	public static function distance($string1, $string2)
	{
		$string1_length = strlen($string1);
		$string2_length = strlen($string2);

		// Here we start building the table of values
		$matrix = array();

		// String1 length + 1 = rows.
		for ($i = 0; $i <= $string1_length; ++$i)
		{
			$matrix[$i][0] = $i;
		}

		// String2 length + 1 columns.
		for ($j = 0; $j <= $string2_length; ++$j)
		{
			$matrix[0][$j] = $j;
		}

		for ($i = 1; $i <= $string1_length; ++$i)
		{
			for ($j = 1; $j <= $string2_length; ++$j)
			{
				$cost = substr($string1, $i - 1, 1) == substr($string2, $j - 1, 1) ? 0 : 1;

				$matrix[$i][$j] = min(
					$matrix[$i - 1][$j] + 1,		// deletion
					$matrix[$i][$j - 1] + 1,		// insertion
					$matrix[$i - 1][$j - 1] + $cost	// substitution
				);

				if ($i > 1 && $j > 1 &&	(substr($string1, $i - 1, 1) == substr($string2, $j - 2, 1))
					&& (substr($string1, $i - 2, 1) == substr($string2, $j - 1, 1)))
				{
					$matrix[$i][$j] = min(
						$matrix[$i][$j],
						$matrix[$i - 2][$j - 2] + $cost	// transposition
					);
				}
			}
		}

		return $matrix[$string1_length][$string2_length];
	}

	/**
	 * Converts text anchors into links.
	 *
	 * @param   string   text to auto link
	 * @return  string
	 */
	public static function auto_link_urls($text)
	{
		// Finds all http/https/ftp/ftps links that are not part of an existing html anchor
		if (preg_match_all('~\b(?<!href="|">)(?:ht|f)tps?://\S+(?:/|\b)~i', $text, $matches))
		{
			foreach ($matches[0] as $match)
			{
				// Replace each link with an anchor
				$text = str_replace($match, html::anchor($match), $text);
			}
		}

		// Find all naked www.links.com (without http://)
		if (preg_match_all('~\b(?<!://)www(?:\.[a-z0-9][-a-z0-9]*+)+\.[a-z]{2,6}\b~i', $text, $matches))
		{
			foreach ($matches[0] as $match)
			{
				// Replace each link with an anchor
				$text = str_replace($match, html::anchor('http://'.$match, $match), $text);
			}
		}

		return $text;
	}

	/**
	 * Converts text email addresses into links.
	 *
	 * @param   string   text to auto link
	 * @return  string
	 */
	public static function auto_link_emails($text)
	{
		// Finds all email addresses that are not part of an existing html mailto anchor
		// Note: The "58;" negative lookbehind prevents matching of existing encoded html mailto anchors
		//       The html entity for a colon (:) is &#58; or &#058; or &#0058; etc.
		if (preg_match_all('~\b(?<!href="mailto:|">|58;)(?!\.)[-+_a-z0-9.]++(?<!\.)@(?![-.])[-a-z0-9.]+(?<!\.)\.[a-z]{2,6}\b~i', $text, $matches))
		{
			foreach ($matches[0] as $match)
			{
				// Replace each email with an encoded mailto
				$text = str_replace($match, html::mailto($match), $text);
			}
		}

		return $text;
	}

	/**
	 * Automatically applies <p> and <br /> markup to text. Basically nl2br() on steroids.
	 *
	 * @param   string   subject
	 * @param   boolean  convert single linebreaks to <br />
	 * @return  string
	 */
	public static function auto_p($str, $br = TRUE)
	{
		// Trim whitespace
		if (($str = trim($str)) === '')
			return '';

		// Standardize newlines
		$str = str_replace(array("\r\n", "\r"), "\n", $str);

		// Trim whitespace on each line
		$str = preg_replace('~^[ \t]+~m', '', $str);
		$str = preg_replace('~[ \t]+$~m', '', $str);

		// The following regexes only need to be executed if the string contains html
		if ($html_found = (strpos($str, '<') !== FALSE))
		{
			// Elements that should not be surrounded by p tags
			$no_p = '(?:p|div|h[1-6r]|ul|ol|li|blockquote|d[dlt]|pre|t[dhr]|t(?:able|body|foot|head)|c(?:aption|olgroup)|form|s(?:elect|tyle)|a(?:ddress|rea)|ma(?:p|th))';

			// Put at least two linebreaks before and after $no_p elements
			$str = preg_replace('~^<'.$no_p.'[^>]*+>~im', "\n$0", $str);
			$str = preg_replace('~</'.$no_p.'\s*+>$~im', "$0\n", $str);
		}

		// Do the <p> magic!
		$str = '<p>'.trim($str).'</p>';
		$str = preg_replace('~\n{2,}~', "</p>\n\n<p>", $str);

		// The following regexes only need to be executed if the string contains html
		if ($html_found !== FALSE)
		{
			// Remove p tags around $no_p elements
			$str = preg_replace('~<p>(?=</?'.$no_p.'[^>]*+>)~i', '', $str);
			$str = preg_replace('~(</?'.$no_p.'[^>]*+>)</p>~i', '$1', $str);
		}

		// Convert single linebreaks to <br />
		if ($br === TRUE)
		{
			$str = preg_replace('~(?<!\n)\n(?!\n)~', "<br />\n", $str);
		}

		return $str;
	}

	/**
	 * Returns human readable sizes.
	 * @see  Based on original functions written by:
	 * @see  Aidan Lister: http://aidanlister.com/repos/v/function.size_readable.php
	 * @see  Quentin Zervaas: http://www.phpriot.com/d/code/strings/filesize-format/
	 *
	 * @param   integer  size in bytes
	 * @param   string   a definitive unit
	 * @param   string   the return string format
	 * @param   boolean  whether to use SI prefixes or IEC
	 * @return  string
	 */
	public static function bytes($bytes, $force_unit = NULL, $format = NULL, $si = TRUE)
	{		// Format string
		$format = ($format === NULL) ? '%01.2f %s' : (string) $format;

		// IEC prefixes (binary)
		if ($si == FALSE OR strpos($force_unit, 'i') !== FALSE)
		{
			$units = array(__('B'), __('KiB'), __('MiB'), __('GiB'), __('TiB'), __('PiB'));
			$mod   = 1024;
		}
		// SI prefixes (decimal)
		else
		{
			$units = array(__('B'), __('kB'), __('MB'), __('GB'), __('TB'), __('PB'));
			$mod   = 1000;
		}

		// Determine unit to use
		if (($power = @array_search((string) $force_unit, $units)) === FALSE)
		{
			$power = ($bytes > 0) ? floor(log($bytes, $mod)) : 0;
		}

		return @sprintf($format, $bytes / pow($mod, $power), $units[$power]);
	}

	/**
	 * Prevents widow words by inserting a non-breaking space between the last two words.
	 * @see  http://www.shauninman.com/archive/2006/08/22/widont_wordpress_plugin
	 *
	 * @param   string  string to remove widows from
	 * @return  string
	 */
	public static function widont($str)
	{
		$str = rtrim($str);
		$space = strrpos($str, ' ');

		if ($space !== FALSE)
		{
			$str = substr($str, 0, $space).'&nbsp;'.substr($str, $space + 1);
		}

		return $str;
	}

	/**
	 * Tests whether a string contains only 7bit ASCII bytes. This is used to
	 * determine when to use native functions or UTF-8 functions.
	 *
	 * @see http://sourceforge.net/projects/phputf8/
	 * @copyright  (c) 2007-2009 Kohana Team
	 * @copyright  (c) 2005 Harry Fuecks
	 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
	 *
	 * @param   string  string to check
	 * @return  bool
	 */
	public static function is_ascii($str)
	{
		return is_string($str) AND ! preg_match('/[^\x00-\x7F]/S', $str);
	}

	/**
	 * Strips out device control codes in the ASCII range.
	 *
	 * @see http://sourceforge.net/projects/phputf8/
	 * @copyright  (c) 2007-2009 Kohana Team
	 * @copyright  (c) 2005 Harry Fuecks
	 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
	 *
	 * @param   string  string to clean
	 * @return  string
	 */
	public static function strip_ascii_ctrl($str)
	{
		return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $str);
	}

	/**
	 * Strips out all non-7bit ASCII bytes.
	 *
	 * @see http://sourceforge.net/projects/phputf8/
	 * @copyright  (c) 2007-2009 Kohana Team
	 * @copyright  (c) 2005 Harry Fuecks
	 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
	 *
	 * @param   string  string to clean
	 * @return  string
	 */
	public static function strip_non_ascii($str)
	{
		return preg_replace('/[^\x00-\x7F]+/S', '', $str);
	}

	/**
	 * Replaces special/accented UTF-8 characters by ASCII-7 'equivalents'.
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 * @see http://sourceforge.net/projects/phputf8/
	 * @copyright  (c) 2007-2009 Kohana Team
	 * @copyright  (c) 2005 Harry Fuecks
	 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
	 *
	 * @param   string   string to transliterate
	 * @param   integer  -1 lowercase only, +1 uppercase only, 0 both cases
	 * @return  string
	 */
	public static function transliterate_to_ascii($str, $case = 0)
	{
		static $UTF8_LOWER_ACCENTS = NULL;
		static $UTF8_UPPER_ACCENTS = NULL;

		if ($case <= 0)
		{
			if ($UTF8_LOWER_ACCENTS === NULL)
			{
				$UTF8_LOWER_ACCENTS = array(
					'а' => 'a',   'б' => 'b',   'в' => 'v',
					'г' => 'g',   'д' => 'd',   'е' => 'e',
					'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
					'и' => 'i',   'й' => 'y',   'к' => 'k',
					'л' => 'l',   'м' => 'm',   'н' => 'n',
					'о' => 'o',   'п' => 'p',   'р' => 'r',
					'с' => 's',   'т' => 't',   'у' => 'u',
					'ф' => 'f',   'х' => 'h',   'ц' => 'c',
					'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
					'ь' => '',    'ы' => 'y',   'ъ' => '',
					'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
				);
			}

			$str = str_replace(
				array_keys($UTF8_LOWER_ACCENTS),
				array_values($UTF8_LOWER_ACCENTS),
				$str
			);
		}

		if ($case >= 0)
		{
			if ($UTF8_UPPER_ACCENTS === NULL)
			{
				$UTF8_UPPER_ACCENTS = array(
					'А' => 'A',   'Б' => 'B',   'В' => 'V',
					'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
					'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
					'И' => 'I',   'Й' => 'Y',   'К' => 'K',
					'Л' => 'L',   'М' => 'M',   'Н' => 'N',
					'О' => 'O',   'П' => 'P',   'Р' => 'R',
					'С' => 'S',   'Т' => 'T',   'У' => 'U',
					'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
					'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
					'Ь' => '',    'Ы' => 'Y',   'Ъ' => '',
					'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
				);
			}

			$str = str_replace(
				array_keys($UTF8_UPPER_ACCENTS),
				array_values($UTF8_UPPER_ACCENTS),
				$str
			);
		}

		return $str;
	}

} // End text