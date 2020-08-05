<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MojoMotor - by EllisLab
 *
 * @package		MojoMotor
 * @author		MojoMotor Dev Team
 * @copyright	Copyright (c) 2003 - 2012, EllisLab, Inc.
 * @license		http://mojomotor.com/user_guide/license.html
 * @link		http://mojomotor.com
 * @since		Version 1.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Form_validation Class
 *
 * @package		MojoMotor
 * @subpackage	Libraries
 * @category	Form Validation
 * @author		EllisLab Dev Team
 */
class Mojomotor_Form_validation extends CI_Form_validation {

	/**
	 * Alpha-numeric with underscores, dashes, and slashes
	 * @todo	Move error messages to language keys
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function url_title($str)
	{
		$str = trim($str, '/');
		
		// reserved first segments
		if (preg_match("#^(setup|admin|assets|javascript|login)(?:(/.*))?$#i", $str, $matches))
		{
			$this->set_message('url_title', 'First segment ('.$matches[1].') is a reserved word.');
			return FALSE;
		}
		
		$this->set_message('url_title', 'The %s field may only contain alpha-numeric characters, underscores, slashes, and dashes.');
				
		return ( ! preg_match("/^([-a-z0-9_\-\/])+$/i", $str)) ? FALSE : TRUE;
	}
	
}

/* End of file Mojomotor_form_validation.php */
/* Location: ./system/mojomotor/libraries/Mojomotor_form_validation.php */