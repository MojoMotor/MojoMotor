<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Array Search Key
 *
 * A helper function to search for a specific Array key recursively
 *
 * @access	public
 * @param	string
 * @param	string
 * @return	mixed
 */
function array_search_key($needle, $haystack)
{
	foreach ($haystack AS $key => $value)
	{
		if ($key == $needle)
		{
			return $value;
		}

		if (is_array($value))
		{
			if ( ($result = array_search_key($needle, $value)) !== FALSE)
			{
				return $result;
			}
		}
	}

	return FALSE;
}

// --------------------------------------------------------------------

/**
 * Array Flatten
 *
 * Flatten multidimensional arrays
 *
 * @access	public
 * @param	array
 * @param	array
 * @return	array
 */
function array_flatten($array, $flat = array())
{
	foreach ($array as $key => $val)
	{
		if (is_array($val))
		{
			$flat[] = $key;
			$flat = array_flatten($val, $flat);
		}
		else
		{
			$flat[] = $val;
		}
	}

	return $flat;
}

// --------------------------------------------------------------------

/**
 * Array Find Element by Key
 *
 * @access	public
 * @param	string
 * @param	array
 * @return	mixed
 */
function array_find_element_by_key($needle, $haystack = array())
{
	if (array_key_exists($needle, $haystack))
	{
		$ret =& $haystack[$needle];
		return $ret;
	}

	foreach ($haystack as $k => $v)
	{
		if (is_array($v))
		{
			$ret =& array_find_element_by_key($needle, $haystack[$k]);

			if ($ret)
			{
				return $ret;
			}
		}
	}

	return FALSE;
}

/* End of file array_helper.php */
/* Location: ./system/mojomotor/helpers/array_helper.php */