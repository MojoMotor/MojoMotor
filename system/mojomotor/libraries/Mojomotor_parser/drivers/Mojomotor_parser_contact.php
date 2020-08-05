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
 * Contact Parser
 *
 * Handles all MojoMotor tags that begin {mojo:contact:...}
 *
 * @package		MojoMotor
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Mojomotor_parser_contact extends CI_Driver {

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
		$this->CI->load->library('email');

		$this->CI->load->database();
		$this->CI->load->model('member_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Form
	 *
	 * This function creates the contact form.
	 *
	 * @param	array
	 * @return	void
	 */
	public function form($template_data)
	{
		$this->CI->load->helper('form');

		// User submited parameters will be appended to this
		$accepted_parameters = array('method'=>'post');

		// Mojo specific params needed to process the form
		$hidden = array();

		if (count($template_data['parameters']) > 0)
		{
			// Mojo specific parameters: recipient, from, subject, return
			// HTML parameters: name, id, class, style
			// Everything else gets kicked to the curb.
			if (isset($template_data['parameters']['recipients']))
			{
				foreach (explode(',', $template_data['parameters']['recipients']) as $recipient)
				{
					// Yo Dawg. I herd you like $recipients,
					// so I put a recipient in your recipients.
					$recipients[] = trim($recipient);
				}
			}

			// From parameter. If there isn't one, we'll fall back to the first admin user.
			// Gets validated in send() below.
			if (isset($template_data['parameters']['from']))
			{
				$from = $template_data['parameters']['from'];
			}
			else
			{
				$from = $this->CI->member_model->get_member_by_id(1)->row('email');
			}

			if (isset($template_data['parameters']['return']))
			{
				$hidden['return'] = $template_data['parameters']['return'];
			}
			else
			{
				$hidden['return'] = $this->CI->uri->uri_string();
			}

			if (isset($template_data['parameters']['subject']))
			{
				$hidden['default_subject'] = $template_data['parameters']['subject'];
			}

			foreach (array('name', 'id', 'class', 'style') as $param)
			{
				if (isset($template_data['parameters'][$param]))
				{
					$accepted_parameters[$param] = $template_data['parameters'][$param];
				}
			}
		}

		// If we're here, and there's no recipient, we'll fall back to the first admin member
		if (empty($recipients))
		{
			$recipients[] = $this->CI->member_model->get_member_by_id(1)->row('email');
		}

		// Hash it, mash it and help send() trash it... (if it isn't valid)
		$hidden['recipients'] = $this->CI->encrypt->encode(implode('|', $recipients));
		$hidden['recipients_hash'] = md5($this->CI->config->item('encryption_key').$hidden['recipients']);

		//Create the from hidden input, with a hash
		$hidden['from'] = $this->CI->encrypt->encode($from);
		$hidden['from_hash'] = md5($this->CI->config->item('encryption_key').$hidden['from']);

		// Open the form
		$return = form_open('addons/contact/send', $accepted_parameters, $hidden);

		// Add in the form contents
		$return .= $template_data['tag_contents'];

		// Close the form
		$return .= form_close();

		return $return;
	}

	// --------------------------------------------------------------------

	/**
	 * Send
	 *
	 * This function receives contact form input, validates and processes it, and
	 * sends the email.
	 *
	 * @return	void
	 */
	public function send()
	{
		$this->CI->load->model('site_model');

		$input_recip = $this->CI->input->post('recipients');

		$raw_recipients = $this->CI->input->post('recipients');
		$recipients = explode('|', $this->CI->encrypt->decode($raw_recipients));
		$recipients_hash = $this->CI->input->post('recipients_hash');

		// Just for code clarity below
		$n = $this->CI->email->crlf;

		// Is there a message body?
		if ( ! $message = trim($this->CI->input->post('message')))
		{
			show_error($this->CI->lang->line('contact_message_empty'));
		}
		
		$message .= "$n$n------------------$n$n";
		

		// Return URI
		$return = ($this->CI->input->post('return')) ? $this->CI->input->post('return') : '';

		if ($recipients_hash == md5($this->CI->config->item('encryption_key').$raw_recipients))
		{
			if (is_array($recipients))
			{
				// Make sure every email checks out
				foreach ($recipients as $key => $recipient)
				{
					if ( ! $this->CI->email->valid_email($recipient))
					{
						log_message('error', 'Contact form is trying to send to an invalid email ('.$recipient.'). Email dropped from mail.');
					    unset($recipient[$key]);
					}
				}
			}

			// Grab the "from"
			$raw_from = $this->CI->input->post('from');
			$from_hash = $this->CI->input->post('from_hash');

			// Let's start with the first admin user as the from. This will get over-ridden below
			// if a valid from parameter was used.
			$from = $this->CI->member_model->get_member_by_id(1)->row('email');

			// Validate the email provided by the hash.
			if ($from_hash == md5($this->CI->config->item('encryption_key').$raw_from))
			{
				$requested_from = $this->CI->encrypt->decode($raw_from);

				if ($this->CI->email->valid_email($requested_from))
				{
					// Its valid, replace the default set above.
					$from = $requested_from;
				}
			}

			// Email subject, was an explict subject field provided?
			if ($this->CI->input->post('subject'))
			{
				$subject = $this->CI->input->post('subject', TRUE);
			}
			// was a default subject provided
			elseif ($this->CI->input->post('default_subject'))
			{
				$subject = $this->CI->input->post('default_subject', TRUE);
			}
			else
			{
				$subject = $this->CI->site_model->get_setting('site_name').' '.$this->CI->lang->line('contact_default_subject');
			}

			// Reply-to
			if ($reply_to_email = $this->CI->input->post('reply_to_email'))
			{
				if ($this->CI->email->valid_email($reply_to_email))
				{
					$this->CI->email->reply_to($reply_to_email, $this->CI->input->post('reply_to_name', TRUE));
				}
				else
				{
					show_error($this->CI->lang->line('contact_invalid_email'));
				}
			}

			// Remove 'known' POST variables before processing extras
			unset($_POST['recipients'],
				  $_POST['recipients_hash'],
				  $_POST['from'],
				  $_POST['from_hash'],
				  $_POST['reply_to_email'],
				  $_POST['reply_to_name'],
				  $_POST['subject'],
				  $_POST['message'],
				  $_POST['return']
			);

			foreach ($_POST as $key => $value)
			{
				$message .= $this->CI->security->xss_clean($key).': '.$this->CI->input->post($key, TRUE)."$n$n";
			}

			$this->CI->email->to($recipients);
			$this->CI->email->from($from);
			$this->CI->email->subject($subject);
			$this->CI->email->message($message);

			if ( ! $this->CI->email->send())
			{
				// exit($this->CI->email->print_debugger());
				show_error($this->CI->lang->line('contact_send_failure'));
			}
		}
		else
		{
			// recipients array almost certainly been tampered with. We'll say nothing about
			// it so as not to encourage them to think it didn't work, but log it.
			log_message('error', 'Information was sent to your contact form from '.$this->CI->input->ip_address().' that could not be verified. This could be a possible mail-relay attempt.');
		}

		// redirect now
		redirect($return);
	}
}

/* End of file Mojomotor_parser_contact.php */
/* Location: system/mojomotor/libraries/Mojomotor_parser/Mojomotor_parser_contact.php */