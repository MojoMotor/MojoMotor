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
 * Refresh String
 *
 * After an action has occurred inside MojoMotor that requires a page refresh
 * (for example, a layout change), this helper function just generates the
 * image and text that offers the user a chance to refresh the page.
 *
 * @access	public
 * @return	string
 */
function refresh_string()
{
	$CI =& get_instance();
	return ' <img class="mojo_page_refresh_trigger" src="'.site_url('assets/img/arrow_refresh_small.png').'" width="16" height="16" alt="'.$CI->lang->line('refresh').'" />';
}


/* End of file page_helper.php */
/* Location: ./system/mojomotor/helpers/page_helper.php */