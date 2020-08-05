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
 * @subpackage	Libraries
 * @category	Addons
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Mojomotor_addons
{
	/**
	 * Register Addon
	 *
	 * Takes care of validating if an addon can be used
	 *
	 * @return	array
	 */
	public function register_addons()
	{
		$CI =& get_instance();
		$CI->load->helper('directory');

		$addons = array();

		foreach (directory_map(APPPATH.'third_party', TRUE) as $addon)
		{
			$addons[] = $addon;
		}

		return $addons;
	}
}

/* End of file Mojomotor_addons.php */
/* Location: system/mojomotor/libraries/Mojomotor_addons.php */