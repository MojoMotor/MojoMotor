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
 * Welcome Class
 *
 * @package		MojoMotor
 * @subpackage	Controllers
 * @category	Controllers
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Welcome extends CI_Controller {

	// Figured out in the constructor and needed for the install process
	var $base_url = '';

	/**
	 * Constructor
	 *
	 * Declared in PHP 4 fashion here and in the setup wizard so that we can send an error
	 * if the user is not using a high enough version of PHP. We want to warn them before
	 * this causes a PHP error.
	 *
	 * @access	public
	 */
	function Welcome()
	{
		parent::__construct();

		// Give the impression that the this controller doesn't exist if MojoMotor is installed.
		if ($this->config->item('install_lock') == 'locked')
		{
			show_404();
		}

		// The base_url won't be available (unless its a validation error but the user has
		// correctly set this value) so detect for that, and then work it out dynamically.
		if (strpos($this->input->server("REQUEST_URI"), 'index'.EXT))
		{
			$temp_base_url = substr($this->input->server("REQUEST_URI"), 0, strpos($this->input->server("REQUEST_URI"), 'index'.EXT));
		}
		else
		{
			$temp_base_url = $this->input->server("REQUEST_URI");
		}

		// What port are we on? If it isn't 80, append it in.
		$port = $this->input->server("SERVER_PORT");
		$temp_server = ($port != '80') ? $this->input->server("SERVER_NAME").":$port" : $this->input->server("SERVER_NAME");

		$this->base_url = trim('http://'.$temp_server.$temp_base_url, '/').'/';
		$this->config->set_item('base_url', $this->base_url);

		// Welcome is a standalone page. The only other standalone pages are used in the installer. During
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

	/**
	 * Index
	 *
	 * The "landing" page for when MojoMotor is present, but not installed.
	 *
	 * @access	public
	 * @return	string
	 */
	function index()
	{
		$vars['page_title'] = 'The publishing engine that does less';

		// This view is NOT language keyed, but rather the text is hardcoded in /views/welcome/welcome_message.php
		// to make changing it before an installation as easy as possible.

		$this->load->view('welcome/welcome_message', $vars);
	}
}

/* End of file welcome.php */
/* Location: system/mojomotor/controllers/welcome.php */