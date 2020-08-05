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
 * Cookie Consent Parser
 *
 * Handles all MojoMotor tags that begin {mojo:cookie_consent:...}
 *
 * @package		MojoMotor
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Mojomotor_parser_cookie_consent extends CI_Driver {

	private $CI;
	protected $addon_version = '1.0';

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('encrypt');
	}

	// --------------------------------------------------------------------

	/**
	 * Allow cookie link
	 *
	 * Creates the cookies allowed link
	 *
	 * @param	array
	 * @return	string
	 */
	public function allow_cookies_link($template_data)
	{
		return $this->cookie_link_helper($template_data, 'allow');
	}

	// --------------------------------------------------------------------

	/**
	 * Disallow cookie link
	 *
	 * Creates the disallow cookie link
	 *
	 * @param	array
	 * @return	string
	 */
	public function disallow_cookies_link($template_data)
	{
		return $this->cookie_link_helper($template_data);
	}


	// --------------------------------------------------------------------

	/**
	 * Set Cookies Allowed
	 *
	 * This function sets the cookies_allowed cookie
	 *
	 * @return	void
	 */
	public function allow_cookies()
	{
		$this->CI->load->helper('cookie');
		$expires = 60*60*24*365;  // 1 year
		
		set_cookie('cookies_allowed', 'y', $expires, '', '/', '', FALSE);
		
		$return = $this->CI->uri->segment(5, '');
		$return = base64_decode(strtr($return, '_-', '/='));

		redirect($return);
	}

	// --------------------------------------------------------------------

	/**
	 * Set Cookies Disallowed
	 *
	 * This function unsets site cookies
	 *
	 * @return	void
	 */
	public function disallow_cookies()
	{
		$this->CI->load->helper('cookie');
		
		$prefix = (config_item('cookie_prefix')) ? config_item('cookie_prefix') : '';
		$prefix_length = strlen($prefix);

		// Nuke the MM cookies
		foreach($_COOKIE as $name => $value)
		{
			if (strncmp($name, $prefix, $prefix_length) == 0)
			{
				set_cookie(substr($name, $prefix_length));
			}
		}

		$return = $this->CI->uri->segment(5, '');
		$return = base64_decode(strtr($return, '_-', '/='));

		redirect($return);
	}
	// --------------------------------------------------------------------

	/**
	 * Cookie link helper
	 *
	 * Creates the allow and disallow cookie links
	 *
	 * @param	array
	 * @param	string
	 * @return	string
	 */
	public function cookie_link_helper($template_data, $type = 'disallow')
	{
		// Check for config
		if (config_item('require_cookie_consent') != 'y')
		{
			return '';
		}
		
		$cookies_allowed = (get_cookie('cookies_allowed') != 'y') ? FALSE : TRUE;

		// Conditionally display content
		if (($cookies_allowed && $type == 'allow') OR ( ! $cookies_allowed && $type == 'disallow'))
		{
			return '';
		}
		
		$ret = $this->CI->uri->uri_string();
		$ret = strtr(base64_encode($ret), '/=', '_-');
		
		$type = ($type == 'disallow') ? 'disallow' : 'allow';		
				
		$link = site_url('addons/cookie_consent/'.$type.'_cookies/'.$ret);

		if (count($template_data['parameters']) > 0)
		{
			if (isset($template_data['parameters']['text']))
			{
				$text = $template_data['parameters']['text'];
				$class = '';
	
				if (isset($template_data['parameters']['class']))
				{
					$class = 'class="'.$template_data['parameters']['class'].'"';

				}
				
				$link = anchor($link, $text, $class);
			}
		}

		return $link;		
	}
}

/* End of file Mojomotor_parser_cookie_consent.php */
/* Location: system/mojomotor/libraries/Mojomotor_parser/Mojomotor_parser_cookie_consent.php */