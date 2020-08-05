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
 * MojoMotor Config Class
 *
 * @package		MojoMotor
 * @subpackage	Core Library
 * @category	Config
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Mojomotor_Config Extends CI_Config {

	var $config_path 		= ''; // Set in the constructor below
	var $database_path		= ''; // Set in the constructor below
	var $autoload_path		= ''; // Set in the constructor below

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();

		$this->config_path		= APPPATH.'config/config'.EXT;
		$this->database_path	= APPPATH.'config/database'.EXT;
		$this->autoload_path	= APPPATH.'config/autoload'.EXT;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Site URL
	 *
	 * Extended from CI to automatically detect admin urls.
	 *
	 * @access	public
	 * @param	string	the URI string
	 * @return	string
	 */
	function site_url($uri = '')
	{
		$uri = trim($uri, '/');

		if ($uri)
		{
			$admin = array('addons', 'pages', 'members', 'editor', 'layouts', 'settings', 'utilities', 'help');
			
			$seg_1 = substr($uri, 0, strcspn($uri, '/'));

			if (in_array($seg_1, $admin))
			{
				$uri = 'admin/'.$uri;
			}
		}
		
		return parent::site_url($uri);
	}

	// --------------------------------------------------------------------

	/**
	 * Update the config file
	 *
	 * Reads the existing config file as a string and swaps out
	 * any values passed to the function.  Will alternately remove values
	 *
	 * Note: If the new values passed via the first parameter are not
	 * found in the config file we will add them to the file.  Effectively
	 * this lets us use this function instead of the "append" function used
	 * previously
	 *
	 * @access	public
	 * @param	array
	 * @param	array
	 * @return	bool
	 */
	function config_update($new_values = array(), $remove_values = array())
	{
		if ( ! is_array($new_values) && count($remove_values) == 0)
		{
			return FALSE;
		}

		@chmod($this->config_path, FILE_WRITE_MODE);

		// Is the config file writable?
		if ( ! is_really_writable($this->config_path))
		{
			show_error($this->config_path.' does not appear to have the proper file permissions.  Please make the file writeable.');
		}

		// Read the config file as PHP
		require $this->config_path;

		// load the file helper
		$this->CI =& get_instance();
		$this->CI->load->helper('file');

		// Read the config data as a string
		$config_file = read_file($this->config_path);

		// Trim it
		$config_file = trim($config_file);

		// Remove values if needed
		if (count($remove_values) > 0)
		{
			foreach ($remove_values as $key => $val)
			{
				$config_file = preg_replace('#\$'."config\[(\042|\047)".$key."\\1\].*#", "", $config_file);
			}
		}

		// Cycle through the newconfig array and swap out the data
		$to_be_added = array();
		if (is_array($new_values))
		{
			foreach ($new_values as $key => $val)
			{
				if (is_bool($val))
				{
					$val = ($val == TRUE) ? 'TRUE' : 'FALSE';
				}
				else
				{
					$val = str_replace("\\\"", "\"", $val);
					$val = str_replace("\\'", "'", $val);
					$val = str_replace('\\\\', '\\', $val);

					$val = str_replace('\\', '\\\\', $val);
					$val = str_replace("'", "\\'", $val);
					$val = str_replace("\"", "\\\"", $val);

					$val = '\''.$val.'\'';
				}

				// Are we adding a brand new item to the config file?
				if ( ! isset($config[$key]))
				{
					$to_be_added[$key] = $val;
				}
				else
				{
					// Update the value
					$config_file = preg_replace('#(\$'."config\[(['\"])".$key."\\2\]\s*=\s*).*;#", "\\1$val;", $config_file);
				}
			}
		}

		// Do we need to add totally new items to the config file?
		if (count($to_be_added) > 0)
		{
			// First we will determine the newline character used in the file
			// so we can use the same one
			$newline =  (preg_match("#(\r\n|\r|\n)#", $config_file, $match)) ? $match[1] : "\n";

			$new_data = '';
			foreach ($to_be_added as $key => $val)
			{
				$new_data .= "\$config['".$key."'] = ".$val.";".$newline;
			}

			// If we didn't find the marker we'll remove the opening PHP line and
			// add the new config data to the top of the file
			if (preg_match("#<\?php.*#i", $config_file, $match))
			{
				// Remove the opening PHP line
				$config_file = str_replace($match[0], '', $config_file);

				// Trim it
				$config_file = trim($config_file);

				// Add the new data string along with the opening PHP we removed
				$config_file = $match[0].$newline.$newline.$new_data.$config_file;
			}
			// If that didn't work we'll add the new config data to the bottom of the file
			else
			{
				// Remove the closing PHP tag
				$config_file = preg_replace("#\?>$#", "", $config_file);

				$config_file = trim($config_file);

				// Add the new data string
				$config_file .= $newline.$newline.$new_data.$newline;
			}
		}

		if ( ! $fp = fopen($this->config_path, FOPEN_WRITE_CREATE_DESTRUCTIVE))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);
		fwrite($fp, $config_file, strlen($config_file));
		flock($fp, LOCK_UN);
		fclose($fp);

		@chmod($this->config_path, FILE_READ_MODE);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update Database Config File
	 *
	 * Reads the existing DB config file as a string and swaps out
	 * any values passed to the function.
	 *
	 * @access	public
	 * @param	array
	 * @param	array
	 * @return	bool
	 */
	function dbconfig_update($dbconfig = array(), $remove_values = array())
	{
		@chmod($this->database_path, FILE_WRITE_MODE);

		// Is the database file writable?
		if ( ! is_really_writable($this->database_path))
		{
			show_error($this->database_path.' does not appear to have the proper file permissions.  Please make the file writeable.');
		}

		// load the file helper
		$this->CI =& get_instance();
		$this->CI->load->helper('file');

		$prototype = array(
							'hostname'	=> 'localhost',
							'username'	=> '',
							'password'	=> '',
							'database'	=> '',
							'dbdriver'	=> 'mysql',
							'dbprefix'	=> 'mojo_',
							'pconnect'	=> TRUE,
							'db_debug'	=> FALSE,
							'cache_on'	=> FALSE,
							'cachedir'	=> '',
							'char_set'	=> 'utf8',
							'dbcollat'	=> 'utf8_general_ci'
						);

		// Just to be safe let's kill anything we don't want in the config file
		foreach ($dbconfig as $key => $val)
		{
			if ( ! isset($prototype[$key]))
			{
				unset($dbconfig[$key]);
			}
		}

		// Fetch the DB file
		require $this->database_path;

		// Is the active group available in the array?
		if ( ! isset($db) OR ! isset($db[$active_group]))
		{
			show_error('Your database.php file seems to have a problem.  Unable to find the active group.');
		}

		// Now we read the file data as a string
		$config_file = read_file($this->database_path);

		// Dollar signs seem to create a problem with our preg_replace
		// so we'll temporarily swap them out
		$config_file = str_replace('$', '@s@', $config_file);

		// Remove values if needed
		if (count($remove_values) > 0)
		{
			foreach ($remove_values as $key => $val)
			{
				$config_file = preg_replace("#\@s\@db\[(['\"])".$active_group."\\1\]\[(['\"])".$key."\\2\].*#", "", $config_file);
			}
		}

		// Cycle through the newconfig array and swap out the data
		if (count($dbconfig) > 0)
		{
			foreach ($dbconfig as $key => $val)
			{
				if ($val === 'y')
				{
					$val = TRUE;
				}
				elseif ($val == 'n')
				{
					$val = FALSE;
				}

				if (is_bool($val))
				{
					$val = ($val == TRUE) ? 'TRUE' : 'FALSE';
				}
				else
				{
					$val = '\''.$val.'\'';
				}

				$val .= ';';

				// Update the value
				$config_file = preg_replace("#(\@s\@db\[(['\"])".$active_group."\\2\]\[(['\"])".$key."\\3\]\s*=\s*).*?;#", "\\1$val", $config_file);
			}
		}

		// Put the dollar signs back
		$config_file = str_replace('@s@', '$', $config_file);

		// Just to make sure we don't have any unwanted whitespace
		$config_file = trim($config_file);

		// Write the file
		if ( ! $fp = fopen($this->database_path, FOPEN_WRITE_CREATE_DESTRUCTIVE))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);
		fwrite($fp, $config_file, strlen($config_file));
		flock($fp, LOCK_UN);
		fclose($fp);

		@chmod($this->database_path, FILE_READ_MODE);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update Arbitrary Config File
	 *
	 * A search and replace handler for arbitrary config values
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function update_config_file($file, $search, $replace)
	{
		$this->CI =& get_instance();
		$this->CI->load->helper('file');
		$file = APPPATH.'config/'.$file.EXT;

		@chmod($file, FILE_WRITE_MODE);

		$config_content = read_file($file);
		$config_content = str_replace($search, $replace, $config_content);
		$write_status = write_file($file, $config_content);

		@chmod($file, FILE_READ_MODE);

		return $write_status;
	}
}


/* End of file Mojomotor_Config.php */
/* Location: system/mojomotor/core/Mojomotor_Config.php */