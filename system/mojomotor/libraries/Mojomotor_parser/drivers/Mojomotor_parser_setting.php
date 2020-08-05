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
 * Setting Parser
 *
 * Handles all MojoMotor tags that begin {mojo:setting:...}
 *
 * @package		MojoMotor
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Mojomotor_parser_setting extends CI_Driver {

	private $CI;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model(array('site_model'));
	}

	// --------------------------------------------------------------------

	/**
	 * Version
	 *
	 * Returns the currently installed version of MojoMotor
	 *
	 * @return	string
	 */
	public function version()
	{
		return $this->CI->site_model->get_setting('mojo_version');
	}
}

/* End of file Mojomotor_parser_setting.php */
/* Location: system/mojomotor/libraries/Mojomotor_parser/Mojomotor_parser_setting.php */