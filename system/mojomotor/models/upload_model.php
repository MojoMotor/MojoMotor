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
 * Upload Model
 *
 * @package		MojoMotor
 * @subpackage	Models
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Upload_model extends CI_Model {

	/**
	 * Get Upload
	 *
	 * Returns the first (currently the only) upload folder's info
	 *
	 * @return	mixed
	 */
	public function get_upload()
	{
		$folders = $this->get_upload_folders();
	
		return $folders[1];
	}

	// --------------------------------------------------------------------

	/**
	 * Get upload folders
	 *
	 * Lists all upload folders
	 *
	 * @return	array
	 */
	public function get_upload_folders($id = FALSE)
	{
		$this->db->from('upload_prefs');
		
		if ($id)
		{
			$this->db->where('id', $id);
		}

		$folders = $this->db->get()->result_array();

		// Has the user set overrides in the upload_preferences config variable?
		if ($this->config->item('upload_preferences') !== FALSE && count($folders) > 0)
		{
			$upload_preferences = $this->config->item('upload_preferences');
	      
			// If we are dealing with a single row
			if (isset($folders['id']))
			{
				// If there is an override preference set for this row
				if (isset($upload_preferences[$folders['id']]))
				{
					$folders = array_merge($folders, $upload_preferences[$folders['id']]);
				}
			}
			else // Multiple upload preference rows returned
			{
				// Loop through our results and see if any items need to be overridden
				foreach ($folders as &$folder)
				{
					if (isset($upload_preferences[$folder['id']]))
					{
						// Merge the database result with the custom result, custom keys
						// overwriting database keys
						$folder = array_merge($folder, $upload_preferences[$folder['id']]);
					}
				}
			}
		}

	    // Use upload destination ID as key for row for easy traversing
		$return_array = array();
	
		foreach ($folders as $folder)
		{
			$return_array[$folder['id']] = $folder;
		}
		
		return $return_array;
	}

	// --------------------------------------------------------------------

	/**
	 * Get files
	 *
	 * Lists all files in any 1 upload directory
	 *
	 * @param	int
	 * @return	mixed	FALSE on fail, array on success
	 */
	public function get_files($upload_dir_id = '')
	{
		$this->db->where('id', $upload_dir_id);
		$dir_info = $this->db->get('upload_prefs');

		$files = array();

		if ($dir_info->num_rows() > 0)
		{
			$this->load->helper('file');

			$files = get_dir_file_info($dir_info->row('server_path'));
		}

		return $files;
	}

}

/* End of file upload_model.php */
/* Location: system/mojomotor/models/upload_model.php */