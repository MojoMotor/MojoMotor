<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoMotor Cache Helpers
 *
 * @package		MojoMotor
 * @subpackage	Helpers
 * @category	Cache
 * @author		MojoMotor Dev Team
 */

// ------------------------------------------------------------------------

/**
 * Remove Cache
 *
 * Deletes all files in the cache except index.html
 *
 * @access	public
 * @return	string
 */
function remove_cache()
{
	$CI =& get_instance();
	// kill cache

	$CI->load->helper('directory');

	$cache_files = directory_map($CI->config->item('cache_path'));
	
	if ($cache_files === FALSE)
	{
		return;
	}
	
	unset($cache_files[array_search('index.html', $cache_files)]);

	foreach($cache_files as $file)
	{
		if ($file != 'index.html')
		{
			unlink($CI->config->item('cache_path').$file);
		}
	}
}

// ------------------------------------------------------------------------

/**
 * Remove Cache Page
 *
 * Deletes a page in the cache
 *
 * @access	public
 * @param	string
 * @return	string
 */
function remove_cache_page($page_url_title)
{
	$CI =& get_instance();

	$filename = $CI->config->item('cache_path').md5($CI->config->item('base_url').$CI->config->item('index_page').'/'.$page_url_title);

	if (file_exists($filename))
	{
		unlink($filename);
	}
}

// ------------------------------------------------------------------------

/**
 * Set Cache Overrie
 *
 * Set's the cache override cookie.
 *
 * @access	public
 * @param	bool
 * @return	void
 */
function set_cache_override($enable = TRUE)
{
	$CI =& get_instance();

	if ($enable)
	{
		$CI->input->set_cookie('override_cache', '1', 86500);
	}
	else
	{
		$CI->load->helper('cookie');
		delete_cookie('override_cache');
	}
}

/* End of file cache_helper.php */
/* Location: ./system/mojomotor/helpers/cache_helper.php */