<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * Authorization Class
 *
 * @package		MojoMotor
 * @subpackage	Libraries
 * @category	Auth
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class CI_Auth {

	private $login = FALSE;
	private $group = 1;
	// 1 == guest
	// 2 == author
	// 3 == admin

	/**
	 * Authorization Class Constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// Set the super object to a local variable for use later
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->library('session');
		$this->CI->load->model('member_model');
		$this->CI->load->helper('cookie');

		// Stats? If so, load here.

		$this->_check_remember_me();

		// Log
		log_message('debug', "Auth Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Is Editor
	 *
	 * Ensures the user is either an editor or an admin
	 *
	 * @return	bool
	 */
	public function is_editor()
	{
		$group_id = $this->CI->session->userdata('group_id');
		
		return ($group_id == 1 OR $group_id == 2) ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Is Admin
	 *
	 * Ensures the user is either an admin
	 *
	 * @return	bool
	 */
	public function is_admin()
	{
		return ($this->CI->session->userdata('group_id') == 1) ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Login
	 *
	 * Verifies the user
	 *
	 * @param	string
	 * @param	string
	 * @return	mixed
	 */
	public function login($email = '', $password = '')
	{
		// blank? That ain't right
		if ($email == '' OR $password == '')
		{
			return FALSE;
		}

		// Get all data for this user first, this allows us to run checks first like
		// are they in a banned group, etc. After we have the user, compare password.
		$member = $this->CI->member_model->get_member($email);

		if ($member)
		{
			// Compare the user and pass
			if ($this->CI->member_model->generate_password($password) == $member->row('password'))
			{
				$group_id	= $member->row('group_id');
				$user_id	= $member->row('id');

				$this->CI->session->set_userdata(array(
					'id'		=> $user_id,
					'group_id'	=> $group_id,
					'email'		=> $member->row('email'),
					'theme'		=> ($this->CI->site_model->get_setting('theme')) ? $this->CI->site_model->get_setting('theme') : 'default'
				));

				// Remember me?
				if ($this->CI->input->post('remember_me') == 'yes')
				{
					$this->_set_remember_me($user_id);
				}

				return $user_id;
			}
		}

		// If we've gotten this far, then all tests have failed.
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Logout
	 *
	 * That's right genius... it logs the user out
	 *
	 * @return	void
	 */
	public function logout()
	{
		$user_id = $this->CI->session->userdata('user_id');

		$this->CI->session->sess_destroy();

		// Eat Cookie
		$this->CI->load->helper('cookie');
		delete_cookie('rememberme');

		$member_data = array(
							'id' => $this->CI->session->userdata('user_id'),
							'remember_me' => '' // Remove any remembered data
		);

		$this->CI->member_model->update_member($member_data);
	}

	// --------------------------------------------------------------------

	/**
	 * Set Remember Me
	 *
	 * Updates the remember me cookie and database information
	 *
	 * @param	string unique identifier
	 * @return	void
	 */
	private function _set_remember_me($user_id)
	{
		$this->CI->load->library('encrypt');

		$token = md5(uniqid(rand(), TRUE));
		$timeout = 60 * 60 * 24 * 7; // One week

		$remember_me = $this->CI->encrypt->encode($user_id.':'.$token.':'.(time() + $timeout));

		// Set the cookie and database
		$cookie = array(
						'name'		=> 'rememberme',
						'value'		=> $remember_me,
						'expire'	=> $timeout
						);

		set_cookie($cookie);
		$this->CI->member_model->update_member(array('id'=>$user_id, 'remember_me'=>$remember_me));
	}

	// --------------------------------------------------------------------

	/**
	 * Check Remember Me
	 *
	 * Checks if a user is logged in and "remembered"
	 *
	 * @access	private
	 * @return	bool
	 */
	function _check_remember_me()
	{
		$this->CI->load->library('encrypt');

		// The cookie exist?
		if($cookie_data = get_cookie('rememberme'))
		{
			$user_id = '';
			$token = '';
			$timeout = '';

			$cookie_data = $this->CI->encrypt->decode($cookie_data);
			
			if (strpos($cookie_data, ':') !== FALSE)
			{
				$cookie_data = explode(':', $cookie_data);
				
				if (count($cookie_data) == 3)
				{
					list($user_id, $token, $timeout) = $cookie_data;
				}
			}

			// Cookie should've expired
			if ((int) $timeout < time())
			{
				return FALSE;
			}

			// Grab the user, returns false if he/she doesn't exist or
			// the cookie was tampered with
			if ($data = $this->CI->member_model->get_member_by_id($user_id))
			{
				// Fill the session and renew the remember me cookie
				$this->CI->session->set_userdata(array(
					'id'		=> $user_id,
					'group_id'	=> $data->row('group_id'),
					'edit_mode'	=> $data->row('edit_mode')
				));

				// Renew the cookie Remember me?
				$this->_set_remember_me($user_id);

				return TRUE;
			}

			// You cheat, cookie monster get's cookie
			delete_cookie('rememberme');
		}

		return FALSE;
	}
}

/* End of file Auth.php */
/* Location: system/mojomotor/libraries/Auth.php */