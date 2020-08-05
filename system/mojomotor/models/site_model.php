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
 * Site Model
 *
 * @package		MojoMotor
 * @subpackage	Models
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Site_model extends CI_Model {

	/**
	 * Default Page
	 *
	 * Returns the default page from site_settings
	 *
	 * @access	public
	 * @return	mixed
	 */
	public function default_page()
	{
		$this->db->select('default_page');
		$default_id = $this->db->get('site_settings')->row('default_page');

		$this->db->select('url_title');
		$this->db->where('id', $default_id);

		$potential_page = $this->db->get('pages');

		if ($potential_page->num_rows() == 1)
		{
			return $potential_page->row('url_title');
		}

		// We need *some* page to display, so let's grab the first one we can find
		$this->db->select('url_title');
		$this->db->limit(1);

		$potential_page = $this->db->get('pages');

		if ($potential_page->num_rows() == 1)
		{
			// Couldn't find the default page, let's log this
			log_message('error', 'The default site page could not be found.');

			return $potential_page->row('url_title');
		}

		// There aren't any pages... catastrophic failure ensues
		log_message('error', 'No pages could not be found, and MojoMotor could not display content.');
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Setting
	 *
	 * Returns one specific site setting
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	public function get_setting($setting = '')
	{
		if ( ! $this->db->field_exists($setting, 'site_settings'))
		{
		   return FALSE;
		}

		$qry = $this->db->select($setting)
						->limit(1)
						->get('site_settings');
		
		if ($qry->num_rows() != 0)
		{
			if ($setting == 'site_structure')
			{
				$site_structure = unserialize($qry->row($setting));

				return $site_structure;
			}
			
			return $qry->row($setting);
		}

		// The field exists query above should mean this else statement
		// never runs, but its here as a fallback to ensure we always
		// return some value.
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Settings
	 *
	 * Returns some or all site settings
	 *
	 * @access	public
	 * @param	mixed
	 * @return	mixed
	 */
	public function get_settings($settings = array())
	{
		if ( ! is_array($settings))
		{
			$settings = array($settings);
		}

		// if $settings is blank, assume all fields
		if (count($settings) > 0)
		{
			$this->db->select(implode(',', $settings));
		}

		$settings = $this->db->get('site_settings');

		return ($settings->num_rows() != 0) ? $settings : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update Setting
	 *
	 * A single setting alias for update_settings()
	 *
	 * @param	mixed (string or array)
	 * @param	string
	 * @return	bool
	 */
	public function update_setting($setting = '', $value = '')
	{
		return $this->update_settings($setting, $value);
	}

	// --------------------------------------------------------------------

	/**
	 * Update Settings
	 *
	 * Updates site settings
	 *
	 * @param	mixed (string or array)
	 * @param	string
	 * @return	bool
	 */
	public function update_settings($settings = array(), $value = '')
	{
		if ( ! is_array($settings))
		{
			$settings = array($settings=>$value);
		}

		if (isset($settings['site_structure']) and is_array($settings['site_structure']))
		{
			$settings['site_structure'] = serialize($settings['site_structure']);
		}

		$language = FALSE;

		// Language is stored in a config file, not in the db, its a special case
		if (isset($settings['language']))
		{
			$language = strtolower($settings['language']);
			unset ($settings['language']);
		}

		$this->db->where('id', 1); // always 1

		// affected_rows() can't be used for true/false, since a valid submission
		// that changes nothing would report as FALSE
		// return ($this->db->affected_rows() > 0) ? TRUE : FALSE;

		// Return values
		// --------------
		// 0 = total failure
		// 1 = db updated worked, lang file did not
		// 2 = everything worked

		if ( ! $this->db->update('site_settings', $settings))
		{
			return 0;
		}
		else
		{
			// make sure the theme is updated for the logged in user
			if (isset($settings['theme']))
			{
				$this->session->set_userdata(array('theme' => $settings['theme']));				
			}
			
			// Do we need to take care of language now?
			if ($language)
			{
				if ($this->config->config_update(array('language'=>$language)))
				{
					return 2;
				}

				return 1;
			}

			return 2;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Version Check
	 *
	 * @return string
	 */
	public function version_check()
	{
		$page_url = 'http://mojomotor.com/resources/mmversion.txt';

		$target = parse_url($page_url);

		$fp = @fsockopen($target['host'], 80, $errno, $errstr, 5);

		if (is_resource($fp))
		{
			fputs ($fp,"GET ".$page_url." HTTP/1.0\r\n" ); 
			fputs ($fp,"Host: ".$target['host'] . "\r\n" ); 
			fputs ($fp,"User-Agent: MojoMotor/\r\n");
			fputs ($fp,"If-Modified-Since: Fri, 01 Jan 2004 12:24:04\r\n\r\n");

			$ver = '';

			while ( ! feof($fp))
			{
				$ver = trim(fgets($fp, 128));
			}

			fclose($fp);

			if ($ver != '')
			{
				if (version_compare($ver, $this->get_setting('mojo_version')) === 1)
				{
					return 'new';
				}

				return 'current';

			}

			return 'undetermined';
		}
		
		return 'connection_failed';
	}
}

/* End of file site_model.php */
/* Location: system/mojomotor/models/site_model.php */