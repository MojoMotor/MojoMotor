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
 * Cache Override
 *
 * Checks for the existence of the override_cache cookie.  This cookie is
 * set on login and destroyed on logout.  It is required so that the cached
 * pages are not shown while logged in.
 *
 * @access	public
 * @return	bool
 */
function cache_override()
{
	if ( ! isset($_COOKIE[config_item('cookie_prefix').'override_cache']))
	{
		// We use the globals here because get_instance() is not defined yet.
		global $OUT, $URI, $CFG;

		if ($OUT->_display_cache($CFG, $URI) == TRUE)
		{
			exit;
		}
	}
}

/* End of file cache_override.php */
/* Location: system/mojomotor/hooks/cache_override.php */