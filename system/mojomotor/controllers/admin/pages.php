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
 * Pages Class
 *
 * @package		MojoMotor
 * @subpackage	Controllers
 * @category	Controllers
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Pages extends Mojomotor_Controller {

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->library('auth');
		$this->load->helper('page');

		// They have permission to be here?
		if ( ! $this->auth->is_editor())
		{
			show_error($this->lang->line('no_permissions'), 404);
		}

		$this->output->enable_profiler(FALSE);
	}

	// --------------------------------------------------------------------

	/**
	 * Index
	 *
	 * The default page
	 *
	 * @access	public
	 * @return	string
	 */
	function index()
	{
		$page_map = $this->page_model->get_page_map('include_in_page_list');

		$attributes = array('id'=>'mojo_site_structure', 'class'=>'mojo_sub_structure');

		$vars['site_structure'] = $this->_build_page_list($page_map, $attributes);

		$this->load->view('pages/index', $vars);
	}

	// --------------------------------------------------------------------

	/**
	 * Add
	 *
	 * The Add Page front end
	 *
	 * @access	public
	 * @return	string
	 */
	function add()
	{
		$vars['form_hidden'] = array();
		$vars['page_title'] = '';
		$vars['url_title'] = '';
		$vars['include_in_page_list'] = TRUE;
		$vars['meta_keywords'] = '';
		$vars['meta_description'] = '';
		$vars['layout_id'] = FALSE;

		$vars['layouts'] = array();

		foreach ($this->layout_model->get_layouts()->result() as $layout)
		{
			$vars['layouts'][$layout->id] = $layout->layout_name;
		}

		// Pages can be created from a parent directly. If so, pass that data
		$segs = $this->uri->segment_array();
		unset($segs[1], $segs[2], $segs[3]); // Remove the directory, controller and method, don't want those
		
		$vars['form_hidden']['parent_hierarchy'] = urlencode(serialize($segs));

		// This is the actual page title to be displayed in the view
		$vars['html_page_title'] = $this->lang->line('page_add');

		$this->load->view('pages/add_edit', $vars);
	}

	// --------------------------------------------------------------------

	/**
	 * Edit
	 *
	 * The page edit front end
	 *
	 * @access	public
	 * @param	int			layout id
	 * @return	string
	 */
	function edit($page_id = FALSE)
	{
		$page = $this->page_model->get_page($page_id);

		if ( ! $page)
		{
			show_error($this->lang->line('page_nonexistent'));
		}

		$vars['form_hidden'] = array(
									'page_id' => $page->id,
									'layout_id' => $page->layout_id
									);

		$vars['page_title'] = htmlspecialchars_decode($page->page_title);
		$vars['url_title'] = $page->url_title;
		$vars['meta_keywords'] = htmlspecialchars_decode($page->meta_keywords);
		$vars['meta_description'] = htmlspecialchars_decode($page->meta_description);
		$vars['layout_id'] = $page->layout_id;
		$vars['include_in_page_list'] = ($page->include_in_page_list == 'n') ? FALSE : TRUE;

		$vars['layouts'] = array();

		foreach ($this->layout_model->get_layouts()->result() as $layout)
		{
			$vars['layouts'][$layout->id] = $layout->layout_name;
		}


		// This is the actual page title to be displayed in the view
		$vars['html_page_title'] = $this->lang->line('page_edit');

		$this->load->view('pages/add_edit', $vars);
	}

	// --------------------------------------------------------------------

	/**
	 * Update
	 *
	 * Processes adding or updating pages
	 *
	 * @access	public
	 * @return	string
	 */
	function update()
	{
		$this->load->library('javascript');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('page_title', $this->lang->line('page_title'), 'htmlspecialchars|required|max_length[100]');
		$this->form_validation->set_rules('url_title', $this->lang->line('url_title'), 'required|url_title|max_length[100]|callback__duplicate_url_title');
		$this->form_validation->set_rules('meta_keywords', $this->lang->line('meta_keywords'), 'max_length[225]|htmlspecialchars');
		$this->form_validation->set_rules('meta_description', $this->lang->line('meta_description'), 'max_length[225]|htmlspecialchars');
		$this->form_validation->set_rules('layout_id', $this->lang->line('layout'), 'required|numeric');
		$this->form_validation->set_error_delimiters('', '');

		if ($this->form_validation->run() === FALSE)
		{
			$json['result'] = 'error';
			$json['message'] = validation_errors();

			exit($this->javascript->generate_json($json));
		}
		else
		{
			//  @todo, put in convert_accented_characters()?
			// $this->load->helper('text');

			// url_title should not have any accents because of the javascript pre-filtering anyhow. But just in case...
			$page = array(
						'page_title' 			=> $this->input->post('page_title'),
						'url_title' 			=> trim($this->input->post('url_title'), '/'),
						// 'url_title' 			=> convert_accented_characters($this->input->post('url_title')),
						'include_in_page_list' 	=> ($this->input->post('include_in_page_list') == 'y') ? 'y' : 'n',
						'meta_keywords' 		=> trim($this->input->post('meta_keywords'), ','),
						'meta_description' 		=> $this->input->post('meta_description'),
						'layout_id' 			=> (int) $this->input->post('layout_id')
			);

			// If we're updating, we'll also have a layout_id, otherwise
			// what we have here is an insert
			if ($this->input->post('page_id'))
			{
				// Remove cached files
				$this->load->helper('cache_helper');
				remove_cache_page($page['url_title']);

				$page['id'] = $this->input->post('page_id');
				$old_page_data = $this->page_model->get_page($page['id']);
				
				$old_page_url = $old_page_data->url_title;

				if ($old_page_url != $page['url_title'])
				{
					// Update page region url titles
					$this->page_model->update_page_region_url_title($old_page_url, $page['url_title']);					
				}

				if ($this->page_model->update_page($page))
				{
					// When the response goes, we want the form cleared
					$json['reveal_page'] = site_url('pages');
					$json['result'] = 'success';
					$json['message'] = $this->lang->line('page_edit_successful').refresh_string();
				}
				else
				{
					$json['result'] = 'error';
					$json['message'] = $this->lang->line('page_edit_fail');
				}
			}
			else
			{
				// so far so good. We'll change this to fail if we can't update the site_structure
				$json['result'] = 'success';
				$json['message'] = $this->lang->line('page_add_successful').refresh_string();

				// When the response goes, we want the form cleared even if updating site_structure fails
				$json['reveal_page'] = site_url('pages');

				if ($page_id = $this->page_model->insert_page($page))
				{
					// Remove cached files incase this page has the same url_title as a previosuly
					// deleted page.
					$this->load->helper('cache_helper');
					remove_cache_page($page['url_title']);

					// Insert succeeded, add editable page regions for this page.

					// Since each page is based on a layout, if we are inserting a new page we need,
					// to poll its layout for existing regions and drop those into the db.
					
					$regions = $this->_check_page_regions($page['layout_id']);
					
					foreach ($regions as $region_id)
					{
						// Insert new page regions
						$this->page_model->insert_page_region(array(
												'region_id'			=> $region_id,
												'region_name'		=> ucwords(str_replace('_', ' ', $region_id)),
												'page_url_title'	=> $page['url_title'],
												'content'			=> '<p>Click to edit<br /><br /></p>',
												'layout_id'			=> $page['layout_id']
						));
					}

					// Do the site_structure magic now
					$site_structure = $this->site_model->get_setting('site_structure');

					$parent_hierarchy = unserialize(urldecode($this->input->post('parent_hierarchy')));
					
					if (count($parent_hierarchy) > 0)
					{
						// Copy of the site structure to manipulate as we move through it
						$insertion_point =& $site_structure;

						// The final destination (I know what you did last summer!)
						$parent = end($parent_hierarchy);

						foreach($parent_hierarchy as $step)
						{
							$insertion_point =& $insertion_point[$step];

							// Are we on the final item?
							if ($step == $parent)
							{
								// If the final item already has children, we need to account for that
								if (is_array($insertion_point))
								{
									$insertion_point[$page_id] = $page_id;
								}
								else
								{
									$insertion_point = array($page_id => $page_id);
								}
							}
						}
					}
					else
					{
						// Top level page, just add the page to the end of the array
						$site_structure[$page_id] = $page_id;
					}

					if ( ! $this->site_model->update_settings(array('site_structure' => $site_structure)))
					{
						$json['result'] = 'error';
						$json['message'] .= $this->lang->line('site_structure_update_fail');
					}
				}
				else
				{
					$json['result'] = 'error';
					$json['message'] = $this->lang->line('page_add_fail');
				}
			}

			exit($this->javascript->generate_json($json));
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Check for page regions
	 *
	 * Skims layout content and returns an array of page region ids
	 *
	 * @access	private
	 * @param	int			layout id
	 * @return	array
	 */
	function _check_page_regions($layout_id)
	{
		$this->load->helper('dom');
		$region_type = 'page';

		$layout_dom = str_get_html($this->layout_model->get_layout($layout_id)->row('layout_content'));

		$regions = array();

		foreach($layout_dom->find('*[class=mojo_'.$region_type.'_region]') as $region)
		{
			$regions[] = $region->id;
		}

		return $regions;
	}


	// --------------------------------------------------------------------

	/**
	 * Delete
	 *
	 * Deletes a page and all child pages. This function does not actually update
	 * the site structure. That gets updated via ajax after the user receives the
	 * the "success" notification.
	 *
	 * @access	public
	 * @param	int			page id
	 * @return	string
	 */
	function delete($page_id = '')
	{
		$this->load->library('javascript');
		$this->load->helper('array');

		// This is to prevent accidental right-clicks, or otherwise accessing this page
		// without having gone through the javascript confirm box.
		if ($this->input->post('confirmed') != 'true')
		{
			exit;
		}

		$json['id'] = $page_id;
		$errors = 0; // Start with a clean slate yo

		$site_structure = $this->site_model->get_setting('site_structure');

		// Grab all child pages. This may result in a multidimensional array
		// that we'll flatten later.
		$child_pages = array_search_key($page_id, $site_structure);
		$pages_to_delete = ( ! is_array($child_pages)) ? array($child_pages => $child_pages) : $child_pages;

		// Add in the parent we're intending to remove
		$pages_to_delete[$page_id] = $page_id;

		// Flatten this all out into a single array of pages we can loop through and remove
		$pages_to_delete = array_flatten($pages_to_delete);

		foreach ($pages_to_delete as $page)
		{
			if ( ! $this->page_model->delete_page((int)$page))
			{
				$errors++;
			}
		}

		if ($errors == 0)
		{
			$json['result'] = 'success';
			$json['message'] = $this->lang->line('page_delete_successful').refresh_string();
		}
		else
		{
			$json['result'] = 'error';
			$json['message'] = $this->lang->line('page_delete_fail');
		}

		exit($this->javascript->generate_json($json));
	}

	// --------------------------------------------------------------------

	/**
	 * Page Reorder
	 *
	 * Drag and drop for the pages site structure
	 *
	 * @access	public
	 * @return	string
	 */
	function page_reorder()
	{
		$this->load->library('javascript');

		$site_structure = $this->input->post('site_structure');

		$update = FALSE;

		if ($site_structure != 0)
		{
			$update = $this->site_model->update_settings(array('site_structure'=>$site_structure));
		}

		if ($update)
		{
			$json['result'] = 'success';
			$json['message'] = $this->lang->line('site_structure_update_successful').refresh_string();
		}
		else
		{
			$json['result'] = 'error';
			$json['message'] = $this->lang->line('site_structure_update_fail');
		}

		exit($this->javascript->generate_json($json));
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Build Page List
	 *
	 * Used to construct the pages menu in the Mojo bar
	 *
	 * @access	public
	 * @param	array
	 * @param	array
	 * @param	array
	 * @param	integer
	 * @return	string
	 */
	function _build_page_list($page_map, $attributes, $cur_depth = 1)
	{
		$CI =& get_instance();
		
		// Set the indentation based on the depth
		$out = str_repeat(" ", $cur_depth);

		// Were any attributes submitted?  If so generate a string
		if (is_array($attributes))
		{
			$atts = '';
			foreach ($attributes as $att => $val)
			{
				$atts .= ' '.$att.'="'.$val.'"';
			}
			$attributes = $atts;
		}
	
		// Write the opening list tag
		$out .= "<ul".$attributes.">\n";
	
		$count = 1;
	
		// Cycle through the list elements.  If an array is
		// encountered we will recursively call build_page_list()
		foreach ($page_map as $id => $page)
		{
			$out .= str_repeat(" ", $cur_depth * 2);
	
			if($count == 1 && $cur_depth == 1)
			{
				$out .= '<li id="mojo_first_drop_target"><div class="mojo_site_structure_placeholder"></div></li>';
				$count++;
			}
	
			if ($page['include_in_page_list'] == 'n')
			{
				$class = 'class="mojo_page_hidden" ';
			}
			else
			{
				$class = '';
			}
	
			$out .= '<li '.$class.'id="mojo_page_delete_'.$id.'">';
	
			$out .= '<div class="ie_fix">';
	
			$out .= $page['page_title'];
	
			$out .= '<div class="mojo_page_edit_delete">';
	
			// If the page is hidden, there are different styles and icons
			if ($page['include_in_page_list'] == 'n')
			{
				$out .= anchor('pages/edit/'.$id, '<img src="'.site_url('assets/img').'/page_edit_hidden.png" alt="'.$CI->lang->line('page_edit').'" height="29" width="23" />', 'class="mojo_sub_page" title="'.$CI->lang->line('page_edit').'"');
				$out .= '&nbsp;';
				$out .= anchor('pages/delete/'.$id, '<img src="'.site_url('assets/img').'/page_delete_hidden.png" alt="'.$CI->lang->line('page_delete').'" height="29" width="23" />', 'class="mojo_page_delete" title="'.str_replace('%', $page['page_title'], $CI->lang->line('delete_confirm')).'"');
			}
			else
			{
				$out .= anchor('pages/edit/'.$id, '<img src="'.site_url('assets/img').'/page_edit.png" alt="'.$CI->lang->line('page_edit').'" height="29" width="23" />', 'class="mojo_sub_page" title="'.$CI->lang->line('page_edit').'"');
				$out .= '&nbsp;';
				$out .= anchor('pages/delete/'.$id, '<img src="'.site_url('assets/img').'/page_delete.png" alt="'.$CI->lang->line('page_delete').'" height="29" width="23" />', 'class="mojo_page_delete" title="'.str_replace('%', $page['page_title'], $CI->lang->line('delete_confirm')).'"');
			}
	
			$out .= anchor($page['url_title'], $CI->lang->line('visit_page'), 'class="mojo_page_link_inline" title="'.$CI->lang->line('link').'"');
	
			$out .= anchor('pages/add/', $CI->lang->line('page_add'), 'class="mojo_sub_page mojo_add_page_inline" title="'.$CI->lang->line('page_add').'"');
	
			$out .= '</div>';
			$out .= '</div>'; // close ie_fix div
	
			// droppable target. Its better to create it here vs inserting it via js.
			// I've found the results much more predictable, and the cycles not being
			// used by js seem to help.
			$out .= '<div class="mojo_site_structure_placeholder"></div>';
	
			if (isset($page['children']))
			{
				$out .= "\n".$this->_build_page_list($page['children'], array('class'=>'mojo_sub_structure'), $cur_depth + 1);
				$out .= str_repeat(" ", $cur_depth * 2);
			}
	
			$out .= "</li>\n";
		}
	
		// Set the indentation for the closing tag
		$out .= str_repeat(" ", $cur_depth);
	
		// Write the closing list tag
		$out .= "</ul>\n";
	
		return $out;
	}

	// --------------------------------------------------------------------

	/**
	 * Duplicate URL title callback
	 *
	 * @access	private
	 * @param	string
	 * @return	bool
	 */
	function _duplicate_url_title($url_title)
	{
		// If we're editing, renaming is fine, but not duplicating another page's url_title
		if ($this->input->post('page_id'))
		{
			// Histrionic plus delusions
			// Tangled dendrites, mad confusion
			if (strtolower($url_title) == strtolower($this->page_model->get_page($this->input->post('page_id'))->url_title))
			{
				return TRUE;
			}
		}

		if ($this->page_model->page_exists($url_title))
		{
			$this->form_validation->set_message('_duplicate_url_title', $this->lang->line('url_title_taken'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
}

/* End of file pages.php */
/* Location: system/mojomotor/controllers/pages.php */