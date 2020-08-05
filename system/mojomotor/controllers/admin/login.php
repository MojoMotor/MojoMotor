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
 * Login Class
 *
 * @package		MojoMotor
 * @subpackage	Controllers
 * @category	Controllers
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Login extends Mojomotor_Controller {

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();

		if (config_item('require_cookie_consent') == 'y')
		{
			$this->load->helper('cookie');
			
			if (get_cookie('cookies_allowed') != 'y')
			{
				$ret = $this->uri->uri_string();
				$link = site_url('addons/cookie_consent/allow_cookies/'.$ret);

				show_error(sprintf($this->lang->line('cookies_required_for_login'), $link));	
			}
		}

		// Login is a standalone page. The only other standalone pages are used in the installer. During
		// the install process, the base_url won't have been set, and thus the site path to things like
		// image assets won't be accurate. To get around this, the _ci_view_path gets dynamically built.
		// So we'll simply add them to the top of the page instead.
		$this->load->vars(array('additional_style'=>
			".mojo_header h1 {
				background: url(".site_url('assets/img/mojomotor_logo_only.jpg').") no-repeat left top;
			}
			.mojo_submit_ajax {
				background: #ddd url(".site_url('assets/img/ajax-loader.gif').") no-repeat center center!important;
			}
			.mojo_header {
				background: #222 url(".site_url('assets/img/mojobar_bg.jpg').") top;
			}
			.button {
				background: #F5C062 url(".site_url('assets/img/button_back.png').") repeat-x center center;
			}
		"));
	}

	// --------------------------------------------------------------------

	/**
	 * Index
	 *
	 * The default site page
	 *
	 * @access	public
	 * @param	bool
	 * @return	mixed
	 */
	function index($first_attempt = TRUE)
	{
		$this->load->library('form_validation');
		$this->load->helper('form');
		
		// They already logged in? Direct them to the home page
		if ($this->session->userdata('group_id'))
		{
			redirect('');
		}

		$this->form_validation->set_rules('email', $this->lang->line('email'), 'required|max_length[60]|valid_email');
		$this->form_validation->set_rules('password', $this->lang->line('db_prefix'), 'required|max_length[50]');
		$this->form_validation->run();

		$vars['page_title'] = $this->lang->line('login');
		$vars['message'] = ($first_attempt) ? '' : '<p class="error">'.$this->lang->line('login_failure').'</p>';
		$vars['remember_me'] = $this->input->post('remember_me');

		$this->closing_output('<script charset="utf-8" type="text/javascript" src="'.base_url().index_page().'/javascript/load/jquery"></script>');
		$this->closing_output('<script charset="utf-8" type="text/javascript">
		jQuery(document).ready(function() {

			jQuery("#mojo_email").focus();

			jQuery(".mojo_login form").submit(function(e) {
				// First thing we do is validate on the client end to ensure there is no honest inputting mistake
				// We are not paranoid here, just make sure its not an honest error
				if (
						jQuery("#mojo_email").val().indexOf("@") == -1 || // its got an "@"
						jQuery("#mojo_email").val().indexOf(".") == -1 || // its got a "."
						jQuery("#mojo_email").val().length < 5 || // its at least 5 chars
						jQuery("#mojo_password").val() == "" // password is not blank
					) {
					jQuery("#mojo_login_error").addClass("error").html("'.$this->lang->line('email_password_warning').'");
					e.preventDefault();
				}
				else
				{
					// Thinking indicator
					jQuery("#mojo_submit").addClass("mojo_submit_ajax");
					jQuery("#mojo_submit span").hide();
				}
			});
		});
		</script>');

		$this->load->view('login/index', $vars);
	}

	// --------------------------------------------------------------------

	/**
	 * Process
	 *
	 * Run Traditional Login check
	 *
	 * @access	public
	 * @return	void
	 */
	function process()
	{
		$this->load->library('auth');

		// There's a corner on the floor,
		// They're tellin' you its yours,
		// You're confident, but not really sure.

		if ($this->auth->login($this->input->post('email'), $this->input->post('password')))
		{
			// Enable the cache override
			$this->load->helper('cache_helper');
			set_cache_override();

			// Default MojoBar to open
			$this->session->set_userdata('bar_state', TRUE);

			// Go to the site's homepage
			redirect('');
		}
		else
		{
			$this->index(FALSE);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Process Ajax
	 *
	 * Run a login check via ajax
	 *
	 * @access	public
	 * @return	string
	 */
	function process_ajax()
	{
		$this->load->library('auth');
		$this->load->library('javascript');

		if ($user_id = $this->auth->login($this->input->post('email'), $this->input->post('password')))
		{
			// Enable the cache override
			$this->load->helper('cache_helper');
			set_cache_override();

			$this->load->model('member_model');
			$member = $this->member_model->get_member_by_id($user_id);

			$result = $this->javascript->generate_json(array(
																'login_status' => 'success',
																'message' => '',
																'edit_mode' => $member->row('edit_mode'),
																'group_name' => $this->member_model->get_group_name($member->row('group_id'))
															));
		}
		else
		{
			// I haven't seen crap like this since my Broadway show "Crap Like This". Ran for five years.
			$result = $this->javascript->generate_json(array(
																'login_status' => 'failure',
																'message' => $this->lang->line('login_failure'),
															));
		}

		exit($result);
	}

	// --------------------------------------------------------------------

	/**
	 * Logout
	 *
	 * @access	public
	 * @param	bool
	 * @return	void
	 */
	function logout($redirect = FALSE)
	{
		$this->load->library('auth');
		$this->auth->logout();

		// Disable the cache override
		$this->load->helper('cache_helper');
		set_cache_override(FALSE);

		if ($redirect)
		{
			redirect('');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Forgotten Password
	 *
	 * Allow user to reset password
	 *
	 * @access	public
	 * @return	void
	 */
	function forgotten_password()
	{
		// They already logged in? Direct them to the home page
		if ($this->session->userdata('login_group'))
		{
			redirect('');
		}

		$this->load->library('form_validation');
		$this->load->helper('form');

		$this->closing_output('<script charset="utf-8" type="text/javascript" src="'.base_url().index_page().'/javascript/load/jquery"></script>');
		$this->closing_output('<script charset="utf-8" type="text/javascript">jQuery(document).ready(function() {jQuery("#email").focus();});</script>');

		$vars['page_title'] = $this->lang->line('forgotten_password');

		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_error_delimiters('<p class="error">', '</p>');

		if ($this->form_validation->run() === FALSE)
		{
			$this->load->view('login/forgotten_password', $vars);
		}
		else
		{
			$this->load->library('email');
			$this->load->model(array('member_model', 'site_model'));

			$member = $this->member_model->get_member($this->input->post('email'));

			if ($member)
			{
				$this->load->helper(array('string', 'url'));

				$user_id = $member->row('id');
				$random_passkey = random_string('alnum', 12);

				$member_data = array(
									'id' => $user_id,
									'auth_code' => $random_passkey,
									'remember_me' => '' // Remove any remembered data
				);

				if ( ! $this->member_model->update_member($member_data))
				{
					// This should always work, if something goes wrong here, throw error
					show_error($this->lang->line('password_reset_unable'));
				}

				// Grab the first admin member for "from" data - this account is undeletable
				$admin_details = $this->member_model->get_member_by_id(1);

				$email_body = $this->lang->line('password_reset_email1').$this->site_model->get_setting('site_name')."\n\r\n\r";
				$email_body .= $this->lang->line('password_reset_email2').' '.site_url("login/confirm_password/{$user_id}/{$random_passkey}")."\n\r\n\r";
				$email_body .= $this->lang->line('password_reset_email3')."\n\r\n\r";
				$email_body .= "--\n\r\n\r".$this->input->ip_address();

				$this->email->to($member->row('email'));
				$this->email->from($admin_details->row('email'), $this->site_model->get_setting('site_name'));
				$this->email->subject($this->lang->line('password_reset').' '.$this->site_model->get_setting('site_name'));
				$this->email->message($email_body);

				if ($this->email->send())
				{
					$vars['message'] = str_replace('%email', $this->input->post('email', TRUE), $this->lang->line('forgotten_password_sent'));
				}
				else
				{
					$vars['message'] = $this->lang->line('trouble_sending_email');
				}
			}
			else
			{
				// Member couldn't be found. Send success anyhow
				$vars['message'] = str_replace('%email', $this->input->post('email', TRUE), $this->lang->line('forgotten_password_sent'));
			}

			// Next line for debugging. Leave commented out please.
			// show_error($this->email->print_debugger());

			// Even if this failed, we'll tell them it was sent so that nobody can
			// glean if accounts exist or not based on this page.
			$this->load->view('login/forgotten_password_message', $vars);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Confirm Password
	 *
	 * Allow user to reset password with confirmed data that was emailed
	 *
	 * @access	public
	 * @return	mixed
	 */
	function confirm_password()
	{
		$this->load->model('member_model');
		$user_id = (int) $this->uri->segment(3);
		$passkey = $this->uri->segment(4);

		$member = $this->member_model->get_member_password_reset($user_id, $passkey);

		if ($member)
		{
			$this->load->library('email');
			$this->load->model('site_model');
			$this->load->helper('string');

			$password_new = random_string('alnum', 12);

			$member_data = array(
								'id' => $user_id,
								'auth_code' => '',
								'remember_me' => '',
								'password'	=> $password_new
			);

			if ($this->member_model->update_member($member_data))
			{
				// Grab the first admin member for "from" data - this account is undeletable
				$admin_details = $this->member_model->get_member_by_id(1);

				$email_body = $this->lang->line('password_email1')."$password_new".$this->lang->line('password_email2').' '.site_url('login');

				$this->email->to($member->row('email'));
				$this->email->from($admin_details->row('email'), $this->site_model->get_setting('site_name'));
				$this->email->subject($this->lang->line('password_reset').' : '.$this->site_model->get_setting('site_name'));
				$this->email->message($email_body);

				if ($this->email->send())
				{
					$vars['message'] = $this->lang->line('password_change_success');
				}
				else
				{
					$vars['message'] = $this->lang->line('trouble_sending_email');
				}

				// Next line for debugging. Leave commented out please.
				// show_error($this->email->print_debugger());
			}
			else
			{
				$vars['message'] = $this->lang->line('password_change_fail');
			}
		}
		else
		{
			$vars['message'] = $this->lang->line('no_record');
		}

		$vars['page_title'] = $this->lang->line('forgotten_password');

		$this->load->view('login/forgotten_password_message', $vars);
	}
}

/* End of file login.php */
/* Location: system/mojomotor/controllers/login.php */