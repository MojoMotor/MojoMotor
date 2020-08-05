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
 * Addons Class
 *
 * @package		MojoMotor
 * @subpackage	Controllers
 * @category	Controllers
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Addons extends CI_Controller {

	// Addons that might be called
	var $first_party_addons;
	var $third_party_addons;

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();

		$this->load->library('Mojomotor_addons');

		// No point in resetting these arrays if they're already set
		if ( ! is_array($this->first_party_addons))
		{
			$this->first_party_addons = array('contact', 'cookie_consent');
		}

		if ( ! is_array($this->third_party_addons))
		{
			$this->third_party_addons = $this->mojomotor_addons->register_addons();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * _remap
	 *
	 * This function is the heart of enabling Addons to have callbacks to other Addon
	 * functions. It simply remaps the controller code into the called Addon's methods.
	 *
	 * @access	_private
	 * @return	mixed
	 */
	function _remap()
	{
		// What is being called?
		$addon = $this->uri->segment(3, FALSE);
		$method = $this->uri->segment(4, 'index');

		if ( ! $addon)
		{
			show_error($this->lang->line('invalid_addon'));
		}

		if (in_array($addon, $this->first_party_addons))
		{
			$this->load->driver('mojomotor_parser');

			$addon = $this->mojomotor_parser->$addon;
		}
		elseif (in_array($addon, $this->third_party_addons))
		{
			$addon_path = APPPATH.'third_party/'.$addon.'/';

			$this->load->add_package_path($addon_path);
			$this->load->library($addon);

			$addon = $this->$addon;
		}

		// This allows addon library functions to accept parameters
		// just like controllers do.

		$params = array();
		if ($this->uri->total_segments() > 4)
		{
			$params = array_slice($this->uri->segment_array(), 4);
		}

		// Be sure the method exists
		if (method_exists($addon, $method))
		{
			call_user_func_array(array($addon, $method), $params);
		}
		else
		{
			// Addon doesn't exist
			show_error($this->lang->line('invalid_addon_call'));
		}
	}
}

/* End of file addons.php */
/* Location: system/mojomotor/controllers/addons.php */