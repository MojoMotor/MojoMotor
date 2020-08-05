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
 * Layout Model
 *
 * @package		MojoMotor
 * @subpackage	Models
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Layout_model extends CI_Model {

	/**
	 * Layout Exists
	 *
	 * Used to determine if the requested layout exists
	 *
	 * @param	string
	 * @return	bool
	 */
	public function layout_exists($layout_name = '')
	{
		if ($layout_name == '')
		{
			return FALSE;
		}

		$this->db->where('layout_name', $layout_name);

		return ($this->db->count_all_results('layouts')) ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Layout
	 *
	 * Gets a Layout
	 *
	 * @param	int
	 * @return	mixed
	 */
	public function get_layout($layout_id = '')
	{
		$this->db->where('id', $layout_id);
		$layout = $this->db->get('layouts');

		return ($layout->num_rows() > 0) ? $layout : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Layout by Name
	 *
	 * Gets a Layout by its name as opposed to its id
	 *
	 * @param	int
	 * @return	mixed
	 */
	public function get_layout_by_name($layout_name = '')
	{
		$this->db->where('layout_name', $layout_name);

		$layout = $this->db->get('layouts');

		return ($layout->num_rows() > 0) ? $layout : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get All Layouts
	 *
	 * returns an array of all layouts
	 *
	 * @param	bool
	 * @return	object
	 */
	public function get_layouts($show_all_layout_types = FALSE, $limit = '*', $offset = '*')
	{
		if ($show_all_layout_types !== TRUE)
		{
			$this->db->where('layout_type', 'webpage');
		}

		// This function is also used elsewhere. Hence we need all layouts, and '*'
		// simply signifies to grab everything. For the layouts controller, there's
		// a limit and offset.
		if ($limit != '*')
		{
			$this->db->limit($limit, $offset);
		}

		// There always has to be at least 1 layout, no need to
		// count results here
		return $this->db->get('layouts');
	}

	// --------------------------------------------------------------------

	/**
	 * Count All Layouts
	 *
	 * Returns a count of all layouts
	 *
	 * @return	int
	 */
	public function count_all_layouts()
	{
		return $this->db->count_all('layouts');
	}

	// --------------------------------------------------------------------

	/**
	 * Count Pages By Layout
	 *
	 * Counts the number of pages build on a layout
	 *
	 * @param	int
	 * @return	int
	 */
	public function count_pages_by_layout($layout_id = '')
	{
		if ($layout_id == '')
		{
			return 0;
		}

		$this->db->where('layout_id', $layout_id);
		return $this->db->count_all_results('pages');
	}

	// --------------------------------------------------------------------

	/**
	 * Get Pages By Layout
	 *
	 * Returns the pages built on a layout
	 *
	 * @param	int
	 * @return	array
	 */
	public function get_pages_by_layout($layout_id = '')
	{
		$this->db->where('layout_id', $layout_id);
		$pages_result = $this->db->get('pages');

		$pages = array();

		if ($pages_result->num_rows() > 0)
		{
			foreach($pages_result->result() as $page_result)
			{
				$pages[] = $page_result->url_title;
			}
		}

		return $pages;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Layout Types
	 *
	 * Returns an array of all layout types
	 *
	 * @return	object
	 */
	public function get_layout_types()
	{
		$layout_types = array(
			'webpage' => $this->lang->line('layout_webpage'),
			'embed'	=> $this->lang->line('layout_embed'),
			'css' => $this->lang->line('layout_css'),
			'js' => $this->lang->line('layout_js')
		);

		return $layout_types;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert Layout
	 *
	 * Inserts a Layout
	 *
	 * @param	array
	 * @return	mixed
	 */
	public function insert_layout($layout_info = array())
	{
		if (isset($layout_info['id']))
		{
			unset($layout_info['id']);
		}

		$layout_info['last_modified'] = time();

		if ($this->db->insert('layouts', $layout_info))
		{
			return $this->db->insert_id();
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update Layout
	 *
	 * Updates a Layout
	 *
	 * @param	array
	 * @return	bool
	 */
	public function update_layout($layout_info = array())
	{
		$this->db->where('id', $layout_info['id']);

		// No changing the id
		unset($layout_info['id']);

		$layout_info['last_modified'] = time();

		// affected_rows() can't be used for true/false, since a valid submission
		// that changes nothing would report as FALSE
		// return ($this->db->affected_rows() > 0) ? TRUE : FALSE;

		return ( ! $this->db->update('layouts', $layout_info)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete Layout
	 *
	 * Deletes a Layout
	 *
	 * @param	int
	 * @return	bool
	 */
	public function delete_layout($layout_id = '')
	{
		$this->db->delete('layouts', array('id' => $layout_id));

		if ($this->db->affected_rows() > 0)
		{
			// layout was deleted, now get any global regions that were unique to it
			$this->db->delete('global_regions', array('layout_id'=>$layout_id));

			// And now remove any pages using that layout
			$this->db->select('id');
			$this->db->where('layout_id', $layout_id);

			$pages = $this->db->get('pages');

			if ($pages->num_rows() > 0)
			{
				$this->load->model('page_model');
				
				foreach ($pages->result() as $page)
				{			
					$this->page_model->delete_page($page->id);
				}
			}

			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert Global Region
	 *
	 * Inserts a Global Region
	 *
	 * @param	array
	 * @return	mixed
	 */
	public function insert_global_region($region_info = array())
	{
		if ($this->db->insert('global_regions', $region_info))
		{
			return $this->db->insert_id();
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update Global Region
	 *
	 * Updates a Global Region
	 *
	 * @param	array
	 * @return	bool
	 */
	public function update_global_region($region_info = array())
	{
		$this->db->where('id', $region_info['layout_id']);

		// No changing the id
		unset($region_info['layout_id']);

		// affected_rows() can't be used for true/false, since a valid submission
		// that changes nothing would report as FALSE
		// return ($this->db->affected_rows() > 0) ? TRUE : FALSE;

		return ( ! $this->db->update('global_regions', $region_info)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete Global Region
	 *
	 * Deletes a Global Region
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function delete_global_region($region_id = '', $layout_id = '')
	{
		$this->db->where('region_id', $region_id);
		$this->db->where('layout_id', $layout_id);

		$this->db->delete('global_regions');

		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Global Regions
	 *
	 * Returns an array of all currently defined global regions
	 *
	 * @param	int
	 * @return	array
	 */
	public function get_global_regions($layout_id = '')
	{
		$this->db->where('layout_id', $layout_id);
		$regions = $this->db->get('global_regions');

		$global_regions = array();

		if ($regions->num_rows() > 0)
		{
			foreach ($regions->result() as $region)
			{
				$global_regions[$region->region_id] = $region->region_name;
			}
		}

		return $global_regions;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Global Regions Export
	 *
	 * Returns an array of all currently defined global regions for export purposes
	 *
	 * @param	int
	 * @return	array
	 */
	public function get_global_regions_export()
	{
		$regions = $this->db->get('global_regions');
		$layouts = $this->db->get('layouts');
		
		$l = array();
		
		foreach ($layouts->result() as $row)
		{
			$l[$row->id] = $row->layout_name;
		}

		$global_regions = array();

		if ($regions->num_rows() > 0)
		{
			foreach ($regions->result() as $region)
			{
				$global_regions[$l[$region->layout_id].'_'.$region->region_id] = $region->content;
			}
		}

		return $global_regions;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Page Regions
	 *
	 * Returns an array of all currently defined page regions
	 *
	 * @param	int
	 * @return	array
	 */
	public function get_page_regions($layout_id = '')
	{
		$regions = $this->db->get_where('page_regions', compact('layout_id'));

		$page_regions = array();

		if ($regions->num_rows() > 0)
		{
			foreach ($regions->result() as $region)
			{
				$page_regions[$region->region_id] = $region->region_name;
			}
		}

		return $page_regions;
	}

	// --------------------------------------------------------------------

	/**
	 * Get All Page Regions
	 *
	 * Returns an array of all currently defined page regions everywhere
	 * Used for export
	 *
	 * @return	array
	 */
	public function get_all_page_regions()
	{
		$regions = $this->db->get('page_regions');

		return $regions->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Get Global Region
	 *
	 * Gets a global region's content
	 *
	 * @param	int
	 * @param	int
	 * @return	mixed
	 */
	public function get_global_region($layout_id = '', $region_id = '')
	{
		$this->db->where('layout_id', $layout_id);
		$this->db->where('region_id', $region_id);

		$region = $this->db->get('global_regions');

		return ($region->num_rows() > 0) ? $region->row() : FALSE;
	}
}


/* End of file layout_model.php */
/* Location: system/mojomotor/models/layout_model.php */