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
 * Page Model
 *
 * @package		MojoMotor
 * @subpackage	Models
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Page_model extends CI_Model {

	/**
	 * Page Exists
	 *
	 * Used to determine if the requested page exists
	 *
	 * @param	string
	 * @return	bool
	 */
	public function page_exists($page = '')
	{
		if ($page == '')
		{
			return FALSE;
		}

		$this->db->where('url_title', $page);

		return ($this->db->count_all_results('pages')) ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get All Pages
	 *
	 * Returns all pages in the site
	 *
	 * @param	string	Which key you want pages indexed on - usually id, could be url_title
	 * @return	array
	 */
	public function get_all_pages($index = 'id')
	{
		$raw_pages = $this->db->get('pages');

		$pages = array();

		foreach($raw_pages->result() as $page)
		{
			$pages[$page->$index] = $page->page_title;
		}

		return $pages;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Pages Exporter
	 *
	 * Returns everything for the exporter
	 *
	 * @return	array
	 */
	public function get_pages_exporter()
	{
		return $this->db->get('pages');
	}

	// --------------------------------------------------------------------

	/**
	 * Get All Pages Info
	 *
	 * Returns id, page_title, url_title and include_in_page_list information for all pages in the site
	 *
	 * @param	bool
	 * @return	mixed (almost always array)
	 */
	public function get_all_pages_info($show_hidden_pages = FALSE)
	{
		$this->db->select('id, page_title, url_title');

		if ( ! $show_hidden_pages)
		{
			$this->db->where('include_in_page_list', 'y');
		}

		$pages = $this->db->get('pages');

		if ($pages->num_rows() > 0)
		{
			$page_info = array();

			foreach($pages->result_array() as $page)
			{
				$page_info[$page['id']] = $page;
			}

			return $page_info;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Page Map
	 *
	 * Returns a hierarchical array of Page information keyed on page id.
	 * Gets id, page_title, url_title by default
	 *
	 * @access	public
	 * @param	mixed	array or string of additional fields to select
	 * @param	mixed	array or string of additional where clauses
	 * @return	mixed	array or FALSE if no results 
	 */
	public function get_page_map($additional_fields = array(), $additional_where = array())
	{
		// get the page hierarchy
		$query = $this->db->select('site_structure')
							->limit(1)
							->get('site_settings');

		$site_structure = unserialize($query->row('site_structure'));

		// get basic page info
		$this->db->select('id, page_title, url_title');

		// additional fields
		$this->db->select($additional_fields);

		// additional where
		$this->db->where($additional_where);

		$query = $this->db->get('pages');

		if ($query->num_rows() > 0)
		{
			$page_info = array();

			foreach($query->result_array() as $page)
			{
				$page_info[$page['id']] = $page;
			}

			return $this->_build_page_map($site_structure, $page_info);
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Build Page Map
	 *
	 * Called by get_page_map() to recursively build the map
	 *
	 * @access	private
	 * @param	array
	 * @param	array
	 * @return	array
	 */
	private function _build_page_map($site_structure, $page_info, &$map = array())
	{
		foreach($site_structure as $id => $val)
		{
			if (isset($page_info[$id])) // graceful handling of $site_structure/$page_info mismatch
			{
				$map[$id] = $page_info[$id];	
		
				if (is_array($val))
				{
					$this->_build_page_map($val, $page_info, $map[$id]['children']);
				}			
			}
		}
	
		return $map;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Page Status List
	 *
	 * @return array
	 */
	public function get_page_list_status()
	{
		$this->db->select('id, include_in_page_list');
		$pages = $this->db->get('pages');

		if ($pages->num_rows() > 0)
		{
			$page_info = array();

			foreach($pages->result() as $page)
			{
				$page_info[$page->id] = $page->include_in_page_list;
			}

			return $page_info;
		}

		return array();
	}

	// --------------------------------------------------------------------

	/**
	 * Get Page Info
	 *
	 * Returns the information of a page, such as url_title, page_title, keywords, etc
	 *
	 * @param	int
	 * @return	mixed (object or bool)
	 */
	public function get_page($page = '')
	{
		if ($page == '')
		{
			return FALSE;
		}

		$this->db->where('id', $page);

		$page = $this->db->get('pages');

		return ($page->num_rows() > 0) ? $page->row() : FALSE;
	}


	// --------------------------------------------------------------------

	/**
	 * Update Page Region url_title
	 *
	 * Updates the page_regions table if a page url_title changes
	 * Long name, but needed for descriptiveness
	 *
	 * @param	string
	 * @return	mixed (object or bool)
	 */
	public function update_page_region_url_title($old_page_url = '', $new_page_url = '')
	{
		if ($old_page_url == '' OR $new_page_url == '')
		{
			return;
		}

		$this->db->where('page_url_title', $old_page_url);

		if ( ! $this->db->update('page_regions', array('page_url_title'=>$new_page_url)))
		{
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Page Info by url_title
	 *
	 * Returns the information of a page, such as url_title, page_title, keywords, etc
	 *
	 * @param	string
	 * @return	mixed (object or bool)
	 */
	public function get_page_by_url_title($page = '')
	{
		if ($page == '')
		{
			return FALSE;
		}

		$this->db->where('url_title', $page);

		$page = $this->db->get('pages');
		
		return ($page->num_rows() > 0) ? $page->row() : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * get_page_content
	 *
	 * Returns the content of the requested page
	 *
	 * @param	string
	 * @return	mixed
	 */
	public function get_page_content($page = '')
	{
		if ($page == '')
		{
			return FALSE;
		}

		$this->db->join('layouts', 'pages.layout_id = layouts.id');

		$this->db->select('layouts.layout_content, layouts.layout_name, pages.layout_id, pages.id');

		$this->db->where($this->db->dbprefix.'pages.url_title', $page);

		$page = $this->db->get('pages');

		return ($page->num_rows() != 0) ? $page->row() : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert Page
	 *
	 * Inserts a page to the site
	 *
	 * @param	array
	 * @return	mixed
	 */
	public function insert_page($page_info = array())
	{
		$page_info['last_modified'] = time();

		if ($this->db->insert('pages', $page_info))
		{
			$page_id = $this->db->insert_id();

			return $page_id;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update Page
	 *
	 * Updates a page to the site
	 *
	 * @access	public
	 * @param	array
	 * @return	mixed
	 */
	public function update_page($page_info = array())
	{
		$page_info['last_modified'] = time();

		$this->db->where('id', $page_info['id']);

		// No changing the id
		unset($page_info['id']);

		// affected_rows() can't be used for true/false, since a valid submission
		// that changes nothing would report as FALSE
		// return ($this->db->affected_rows() > 0) ? TRUE : FALSE;

		return ( ! $this->db->update('pages', $page_info)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete Page
	 *
	 * Deletes a Page
	 *
	 * @param	int
	 * @return	bool
	 */
	public function delete_page($page_id = '')
	{
		// Editable page regions first.
		$this->db->delete('page_regions', array('page_url_title' => $this->get_page($page_id)->url_title));

		// Now the actual page
		$this->db->delete('pages', array('id' => $page_id));

		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Page Region
	 *
	 * Gets editable page region content
	 *
	 * @param	string
	 * @param	int
	 * @return	mixed
	 */
	public function get_page_region($page_url_title = '', $region_id = '')
	{
		$this->db->where('page_url_title', $page_url_title);
		$this->db->where('region_id', $region_id);

		$region = $this->db->get('page_regions');

		return ($region->num_rows() > 0) ? $region->row() : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update Page Region
	 *
	 * Updates editable page region content
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function update_page_region($page_url_title = '', $region_id = '', $content = '')
	{
		// save_prepare() currently doesn't do anything, but is in there for the purposes
		// of content prep for future applications. For now, this can be commented out
		// to save resources.
		// $this->load->driver('mojomotor_parser');
		// $content = $this->mojomotor_parser->save_prepare($content);

		$this->db->where('region_id', $region_id);
		$this->db->where('page_url_title', $page_url_title);

		$this->db->set('content', $content);

		return ( ! $this->db->update('page_regions')) ? FALSE : TRUE;
	}


	// --------------------------------------------------------------------

	/**
	 * Update Global Region
	 *
	 * Updates editable page region content
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function update_global_region($layout_id = '', $region_id = '', $content = '')
	{
		// save_prepare() currently doesn't do anything, but is in there for the purposes
		// of content prep for future applications. For now, this can be commented out
		// to save resources.
		// $this->load->driver('mojomotor_parser');
		// $content = $this->mojomotor_parser->save_prepare($content);

		$this->db->where('region_id', $region_id);
		$this->db->set('content', $content);
		$this->db->where('layout_id', $layout_id);

		// affected_rows() can't be used for true/false, since a valid submission
		// that changes nothing would report as FALSE
		// return ($this->db->affected_rows() > 0) ? TRUE : FALSE;

		return ( ! $this->db->update('global_regions')) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert Editable Region
	 *
	 * Inserts a page to the site
	 *
	 * @param	array
	 * @return	mixed
	 */
	public function insert_page_region($region_info = array())
	{
		if ($this->db->insert('page_regions', $region_info))
		{
			return $this->db->insert_id();
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete Page Region
	 *
	 * Deletes a Page Region
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function delete_page_region($region_id = '', $layout_id = '')
	{
		$this->db->where('region_id', $region_id);
		$this->db->where('layout_id', $layout_id);

		$this->db->delete('page_regions');

		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Delete Page Regions
	 *
	 * Delete All Page Regions For a URL Title
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function delete_page_regions($url_title = '')
	{
		$this->db->where('page_url_title', $url_title);

		$this->db->delete('page_regions');

		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}	
	
}

/* End of file page_model.php */
/* Location: system/mojomotor/models/page_model.php */