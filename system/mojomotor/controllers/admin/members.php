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
 * Members Class
 *
 * @package		MojoMotor
 * @subpackage	Controllers
 * @category	Controllers
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Members extends Mojomotor_Controller {

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->library('auth');

		// We aren't checking permissions here since different functions have
		// different permission levels, and we need more discreet control.

		$this->output->enable_profiler(FALSE);
	}

	// --------------------------------------------------------------------

	/**
	 * Index
	 *
	 * The default site page
	 *
	 * @access	public
	 * @param	int
	 * @return	string
	 */
	function index($offset = 0)
	{
		// They have permission to be here?
		if ( ! $this->auth->is_admin())
		{
			if ($this->auth->is_editor())
			{
				// Keep editors on the edit screen for themeselves
				$this->edit($this->session->userdata('id'));
				return;
			}
			else
			{
				show_error($this->lang->line('no_permissions'), 404);
			}
		}

		$this->load->library('pagination');

		$config['base_url'] = site_url('members/index');
		$config['total_rows'] = $this->member_model->count_all_members();
		$this->pagination->initialize($config);

		$vars['members'] = $this->member_model->get_members($this->pagination->per_page, $offset);

		$this->load->view('members/index', $vars);
	}

	// --------------------------------------------------------------------

	/**
	 * Add
	 *
	 * Front-end for adding a new member
	 *
	 * @access	public
	 * @return	string
	 */
	function add()
	{
		// They have permission to be here?
		if ( ! $this->auth->is_admin())
		{
			show_error($this->lang->line('no_permissions'), 404);
		}

		$vars['user_id'] = '';
		$vars['email'] = '';
		$vars['current_member_group'] = 2;
		$vars['member_groups'] = $this->member_model->get_member_groups();
		$vars['edit_mode_plain'] = FALSE;
		$vars['edit_mode_wysiwyg'] = TRUE;

		$vars['password_lang'] = 'password';
		$vars['password_confirm_lang'] = 'password_confirm';
		
		$vars['html_page_title'] = $this->lang->line('member_add');

		$this->load->view('members/add_edit', $vars);
	}

	// --------------------------------------------------------------------

	/**
	 * Edit Member
	 *
	 * Edits a member
	 *
	 * @access	public
	 * @param	int		The member id
	 * @return	string	The view
	 */
	function edit($member_id = '')
	{
		// Only admins can edit other people's profiles
		if ($member_id != $this->session->userdata('id') && ! $this->auth->is_admin())
		{
			// I said "no Homer/s/". We're allowed to have 1.
			show_error($this->lang->line('no_permissions'), 404);
		}

		$member = $this->member_model->get_member_by_id($member_id);

		if ( ! $member)
		{
			show_error($this->lang->line('member_edit_fail'));
		}

		$vars['edit_mode_plain'] = ($member->row('edit_mode') == 'source') ? TRUE : FALSE;
		$vars['edit_mode_wysiwyg'] = ($member->row('edit_mode') == 'source') ? FALSE : TRUE;

		$vars['current_member_group'] = $member->row('group_id');
		$vars['user_id'] = $member_id;

		$vars = array_merge($vars, $member->row_array());

		$vars['member_groups'] = $this->member_model->get_member_groups();

		$vars['password_lang'] = 'password_new';
		$vars['password_confirm_lang'] = 'password_new_confirm';
		
		$vars['html_page_title'] = $this->lang->line('member_edit');

		$this->load->view('members/add_edit', $vars);
	}

	// --------------------------------------------------------------------

	/**
	 * Edit Process
	 *
	 * Updates a member profile
	 *
	 * @access	public
	 * @return	string	The view
	 */
	function update()
	{
		// Only admins can edit other people's profiles
		if ($this->input->post('user_id') != $this->session->userdata('id') && ! $this->auth->is_admin())
		{
			show_error($this->lang->line('no_permissions'), 404);
		}

		$this->load->library('javascript');
		$this->load->helper('email');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('email', 'Email', 'required|max_length[60]|valid_email|callback__duplicate_email');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('password', 'Password', 'alpha_dash|max_length[50]');
		$this->form_validation->set_rules('password_confirm', 'Password Confirm', 'matches[password]');

		if ($this->form_validation->run() === FALSE)
		{
			$json['result'] = 'error';
			$json['message'] = validation_errors();
		}
		else
		{
			// Member data common to both insert and edit
			$member_data = array(
									'email'					=> $this->input->post('email'),
									'edit_mode'				=> $this->input->post('edit_mode'),
			);

			// New member or are we editing?
			if ($this->input->post('user_id') == '')
			{
				// New members need a password. We don't need to check it matches
				// password_confirm, since that happened above in the form validation.
				if ( ! $this->input->post('password'))
				{
					$json['result'] = 'error';
					$json['message'] = $this->lang->line('password_required');
					exit($this->javascript->generate_json($json));
				}

				if (strlen($this->input->post('password')) < 6)
				{
					$json['result'] = 'error';
					$json['message'] = str_replace('%password_length', '6', $this->lang->line('password_too_short'));
					exit($this->javascript->generate_json($json));
				}
				
				if (strlen($this->input->post('password')) > 50)
				{
					$json['result'] = 'error';
					$json['message'] = $this->lang->line('password_too_long');
					exit($this->javascript->generate_json($json));
				}

				
				// Members created this way don't have system assigned passwords
				$member_data['autogen_password'] = 'n';

				// Add the password and group to the member array for insertion
				$member_data['password'] = $this->input->post('password');
				$member_data['group_id'] = $this->input->post('member_group');

				if ($this->member_model->insert_member($member_data))
				{
					// $notification is used to pass any additional messages to the admin
					$notification = '';

					// Are we notifying the member?
					if ($this->input->post('notify_member') == 'y')
					{
						$this->load->library('email');
						$this->email->to($this->input->post('email'));
						$this->email->from($this->session->userdata('email'));
						$this->email->subject(str_replace('%site_name', $this->site_model->get_setting('site_name'), $this->lang->line('mojo_account_activation')));

						$activation_variables = array('%site_name', '%email', '%password', '%login_page');

						$replacements = array(
							$this->site_model->get_setting('site_name'),
							$this->input->post('email'),
							$this->input->post('password'),
							site_url('login')
						);

						$this->email->message(str_replace($activation_variables, $replacements, $this->lang->line('mojo_account_activation_body')));

						$notification = ($this->email->send()) ? $this->lang->line('notification_success') : $this->lang->line('notification_failure');

						// Below line commented out for debugging
						// exit($this->email->print_debugger());
					}

					$json['result'] = 'success';
					$json['reveal_page'] = site_url('members');
					$json['message'] = $this->lang->line('member_add_successful').$notification.'.'; // period isn't part of the lang file
				}
				else
				{
					$json['result'] = 'error';
					$json['message'] = $this->lang->line('member_add_fail');
				}
			}
			else
			{
				$member = $this->member_model->get_member_by_id($this->input->post('user_id'));

				$email = $member->row('email');

				// Starting values
				$error_messages = array();

				// Password change?
				// This password block assumes no error messages have been thrown before it.
				// If it gets moved, it'll be important to re-consider the logic here.
				if ($this->input->post('password'))
				{
					// They provide the correct password?
					if ($this->member_model->generate_password($this->input->post('password_old')) != $member->row('password'))
					{
						$error_messages[] = $this->lang->line('password_wrong');
					}

					// New passwords filled out and match?
					if ($this->input->post('password') != $this->input->post('password_confirm'))
					{
						$error_messages[] = $this->lang->line('passwords_no_match');
					}
					
					if (strlen($this->input->post('password')) < 6)
					{
						$error_messages[] = str_replace('%password_length', '6', $this->lang->line('password_too_short'));
					}
					
					if (strlen($this->input->post('password')) > 50)
					{
						$error_messages[] = $this->lang->line('password_too_long');
					}

					if (count($error_messages) == 0)
					{
						// Still no errors? Then add password stuff into the array of data to be changed.
						$member_data['password'] = $this->input->post('password');
					}
				}

				// Sanity check for email. They're changing it, not trying to trick us.
				if ($this->input->post('email'))
				{
					if (valid_email($this->input->post('email')))
					{
						$email = $this->input->post('email');
					}
					else
					{
						$error_messages[] = $this->lang->line('invalid_email');
					}
				}

				if (count($error_messages) == 0)
				{
					// No errors, process the request
					$member_data['id'] = $this->input->post('user_id');

					// No moving the main admin out of the admin group please and thank you
					if ($this->input->post('user_id') == 1)
					{
						$member_data['group_id'] = 1;
					}
					else
					{
						if ($this->auth->is_admin() && $this->input->post('member_group'))
						{
						    $member_data['group_id'] = (int) $this->input->post('member_group');
						}
					}

					// Are we also changing the password?
					if ($this->input->post('password'))
					{
						$member_data['autogen_password'] = 'n';
						$member_data['password'] = $this->input->post('password');
					}

					if ($this->member_model->update_member($member_data))
					{
						$json['result'] = 'success';
						$json['reveal_page'] = site_url('members');
						$json['message'] = $this->lang->line('member_edit_successful');

						$json['callback'] = 'update_edit_mode_callback';
						$json['callback_args'] = $member_data['edit_mode'];
					}
					else
					{
						$json['result'] = 'error';
						$json['message'] = $this->lang->line('member_edit_fail');
					}
				}
				else
				{
					$json['result'] = 'error';
					$json['message'] = implode('<br />', $error_messages);
				}
			}
		}

		exit($this->javascript->generate_json($json));
	}

	// --------------------------------------------------------------------

	/**
	 * Delete Member
	 *
	 * Deletes new member
	 *
	 * The delete process has been confirmed, and this method's job is to
	 * remove the user completely.
	 *
	 * @access	public
	 * @param	int		The member id
	 * @return	string	The view
	 */
	function delete($member_id = '')
	{
		// They have permission to be here?
		if ( ! $this->auth->is_admin())
		{
			show_error($this->lang->line('no_permissions'), 404);
		}

		// Don't allow for deletion of the principal admin user under any circumstance
		if ($member_id == 1)
		{
			show_error($this->lang->line('user_cannot_be_deleted'));
		}

		// Members cannot delete their own accounts, even if they are admins
		if ($member_id == $this->session->userdata('id'))
		{
			show_error($this->lang->line('cannot_delete_self'));
		}

		$this->load->library('javascript');

		// This is to prevent accidental right-clicks, or otherwise accessing this page
		// without having gone through the javascript confirm box.
		if ($this->input->post('confirmed') != 'true')
		{
			exit;
		}

		$json['id'] = $member_id;

		if ($this->member_model->delete_member((int)$member_id))
		{
			$json['result'] = 'success';
			$json['message'] = $this->lang->line('member_delete_successful');
		}
		else
		{
			$json['result'] = 'error';
			$json['message'] = $this->lang->line('member_delete_fail');
		}

		exit($this->javascript->generate_json($json));
	}

	// --------------------------------------------------------------------

	/**
	 * Duplicate email callback
	 *
	 * @access	private
	 * @param	string
	 * @return	bool
	 */
	function _duplicate_email($email)
	{
		// If we're editing, allow the same email
		if ($this->input->post('user_id'))
		{
			// Histrionic plus delusions
			// Tangled dendrites, mad confusion
			if ($email == $this->member_model->get_member_by_id($this->input->post('user_id'))->row('email'))
			{
				return TRUE;
			}
		}

		if ($this->member_model->get_member($email) !== FALSE)
		{
			$this->form_validation->set_message('_duplicate_email', $this->lang->line('duplicate_email'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
}

/* End of file members.php */
/* Location: system/mojomotor/controllers/members.php */