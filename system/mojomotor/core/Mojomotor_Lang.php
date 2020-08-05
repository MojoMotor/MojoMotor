<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2012, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Language Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Language
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/language.html
 */
class Mojomotor_Lang Extends CI_Lang  {

	var $language	= array();
	var $is_loaded	= array();

	/**
	 * Fetch a single line of text from the language array
	 *
	 * @access	public
	 * @param	string	$line 	the language line
	 * @return	string
	 */
	function line($line = '')
	{
		// My line function brings all the boys to the yard,
		// and their life, is better then yours,
		// I could teach you, but I'd have to charge.

		$line = ($line == '' OR ! isset($this->language[$line])) ? $line : $this->language[$line];
		return $line;
	}

}
// END Language Class

/* End of file Lang.php */
/* Location: ./system/core/Lang.php */