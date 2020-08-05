<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 * Control Panel Class
 *
 * @package		MojoMotor
 * @subpackage	Libraries
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Cp
{
	var $CI;
	var $mojo_version = '1.2.1';
	var $appended_output = array();

	// --------------------------------------------------------------------

	/**
	 * Control Panel Class Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function Cp()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('auth');
	}

	// --------------------------------------------------------------------

	/**
	 * Output
	 *
	 * Handles getting needed files to a user based on their privilege level
	 *
	 * @access	public
	 * @return	string
	 */
	function output()
	{
		$output = '<script charset="utf-8" type="text/javascript" src="'.site_url('javascript/load/jquery').'"></script>'."\n";
		$output .= '<script charset="utf-8" type="text/javascript" src="'.site_url('javascript/load/ui').'"></script>'."\n";
		$output .= '<script charset="utf-8" type="text/javascript" src="'.site_url('javascript/load/plugin/simplemodal').'"></script>'."\n";
		$output .= '<script charset="utf-8" type="text/javascript" src="'.site_url('javascript/load_parse/mojobars/').'"></script>'."\n";
		$output .= '<script charset="utf-8" type="text/javascript" src="'.site_url('javascript/load/plugin/address').'"></script>'."\n"; // history manager, back/forward support

		// If cookies are required and the member is logged in?  We set cookies allowed cookie
		if (config_item('require_cookie_consent') == 'y' && $this->CI->session->userdata('group_id'))
		{
			if (get_cookie('cookies_allowed') != 'y')
			{
				$expires = 60*60*24*365;  // 1 year
				set_cookie('cookies_allowed', 'y', $expires);
			}			
		}		

		// load custom stuff
		if ($this->CI->session->userdata('group_id') == 1)
		{
			// Admin
			$output .= '<script charset="utf-8" type="text/javascript" src="'.site_url('javascript/load_parse/admin_mode').'"></script>'."\n";
		}
		elseif ($this->CI->session->userdata('group_id') == 2)
		{
			// Editor
			$output .= '<script charset="utf-8" type="text/javascript" src="'.site_url('javascript/load_parse/editor_mode').'"></script>'."\n";
		}
		elseif ($this->CI->site_model->get_setting('in_page_login') == 'y') // not logged in, but in-page login is available, so load js
		{
			// Not logged in, but in-page login is possible, so we'll need to load some js here
			$output = '<script charset="utf-8" type="text/javascript" src="'.site_url('javascript/load/jquery').'"></script>'."\n";
			$output .= '<script charset="utf-8" type="text/javascript" src="'.site_url('javascript/load/ui').'"></script>'."\n";
			$output .= '<script charset="utf-8" type="text/javascript" src="'.site_url('javascript/load/plugin/simplemodal').'"></script>'."\n";
			$output .= '<script charset="utf-8" type="text/javascript" src="'.site_url('javascript/load_parse/login/').'"></script>'."\n";
		}
		else
		{
			// User isn't logged in, and in page login isn't an option. We don't need any of
			// MojoMotor's js files, so over-ride the output var.
			$output = '';
		}

		return $output;
	}
}


/* End of file Cp.php */
/* Location: system/mojomotor/libraries/Cp.php */