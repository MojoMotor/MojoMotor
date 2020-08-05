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
 * Help Class
 *
 * @package		MojoMotor
 * @subpackage	Controllers
 * @category	Controllers
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Help extends Mojomotor_Controller {

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

		// They have permission to be here?
		if ( ! $this->auth->is_editor())
		{
			show_error($this->lang->line('no_permissions'), 404);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Index
	 *
	 * The default page
	 *
	 * @access	public
	 * @return	void
	 */
	function index()
	{
		$this->load->helper('typography');

		$vars['version'] = $this->site_model->get_setting('mojo_version');
		$vars['language'] = $this->config->item('language');

		$this->load->view('help/index', $vars);
	}
}

/* End of file help.php */
/* Location: system/mojomotor/controllers/help.php */