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
 * MojoMotor Controller Class
 *
 * @package		MojoMotor
 * @subpackage	Core Library
 * @category	Controllers
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Mojomotor_Controller extends CI_Controller
{

	var $closing_output = '';

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();

		$this->load->database();

		$this->load->helper(array('url', 'form'));
		$this->load->model(array('page_model', 'member_model', 'layout_model', 'site_model'));

		// Some of the libs depend on the models, so load them second.
		$this->load->library('parser');
		$this->load->library('cp');

		$this->load->vars(array('group_id' => $this->session->userdata('group_id')));

		// Global control of profiler
		$this->output->enable_profiler($this->config->item('show_profiler'));
	}

	// --------------------------------------------------------------------

	/**
	 * Closing Output
	 *
	 * MojoMotor allows for content to be stored and appended to the output
	 * stream. This function is analogous to append_output(), only for closing
	 * data.
	 *
	 * @access	public
	 * @param	array
	 * @return	bool
	 */
	function closing_output($output = '')
	{
		$this->closing_output .= $output;
	}

}

/* End of file Mojomotor_Controller.php */
/* Location: system/mojomotor/core/Mojomotor_Controller.php */