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
 * Security Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Security
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/language.html
 */
class Mojomotor_Security Extends CI_Security  {

	/**
	 * Set Cross Site Request Forgery Protection Cookie
	 *
	 * @return	object
	 */
	public function csrf_set_cookie()
	{
		// No permission, no cookie
		if (config_item('require_cookie_consent') == 'y')
		{
			$prefix = config_item('cookie_prefix');
			$cookie_allowed_cookie = (isset($_COOKIE[$prefix.'cookies_allowed']) && $_COOKIE[$prefix.'cookies_allowed'] == 'y') ? TRUE : FALSE;

			if ( ! $cookie_allowed_cookie)
			{
				return;
			}
		}

		$expire = time() + $this->_csrf_expire;
		$secure_cookie = (config_item('cookie_secure') === TRUE) ? 1 : 0;

		if ($secure_cookie)
		{
			$req = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : FALSE;

			if ( ! $req OR $req == 'off')
			{
				return FALSE;
			}
		}

		setcookie($this->_csrf_cookie_name, $this->_csrf_hash, $expire, config_item('cookie_path'), config_item('cookie_domain'), $secure_cookie);

		log_message('debug', "CRSF cookie Set");
		
		return $this;
	}
	
}

// END Security Class

/* End of file Mojomotor_Security.php */
/* Location: system/mojomotor/core/Mojomotor_Security.php */