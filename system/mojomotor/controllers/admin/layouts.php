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
 * Layouts Class
 *
 * @package		MojoMotor
 * @subpackage	Controllers
 * @category	Controllers
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Layouts extends Mojomotor_Controller {

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
		$this->load->model('layout_model');
		$this->load->helper('page_helper');

		// They have permission to be here?
		if ( ! $this->auth->is_admin())
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
	 * @param	int
	 * @return	void
	 */
	function index($offset = 0)
	{
		$this->load->library('pagination');

		$config['base_url'] = site_url('layouts/index');
		$config['total_rows'] = $this->layout_model->count_all_layouts();
		$this->pagination->initialize($config);

		$vars['layouts'] = $this->layout_model->get_layouts(TRUE, $this->pagination->per_page, $offset);

		$this->load->view('layouts/index', $vars);
	}

	// --------------------------------------------------------------------

	/**
	 * Add
	 *
	 * The add layout front end
	 *
	 * @access	public
	 * @return	void
	 */
	function add()
	{
		$vars['layout_id'] = '';
		$vars['layout_name'] = '';
		$vars['layout_type'] = 'webpage';
		$vars['layout_content'] = '';

		$vars['page_title'] = $this->lang->line('layout_add');
		$vars['layout_types'] = $this->layout_model->get_layout_types();
		$vars['layout_type_message'] = '<img src="'.site_url('assets/img/mojo_more_info.png').'" height="12" width="12" alt="'.$this->lang->line('layout_type_message').'" /> '.$this->lang->line('layout_type_message');

		$vars['form_attributes'] = array();
		$vars['form_hidden'] = array();

		$vars['html_page_title'] = $this->lang->line('layout_add');

		$this->load->view('layouts/add_edit', $vars);
	}

	// --------------------------------------------------------------------

	/**
	 * Edit
	 *
	 * The layout edit front end
	 *
	 * @access	public
	 * @param	int
	 * @return	void
	 */
	function edit($layout_id = '')
	{
		$layout = $this->layout_model->get_layout($layout_id);

		if ( ! $layout)
		{
			show_error($this->lang->line('layout_nonexistent'));
		}

		$vars['layout_name'] = $layout->row('layout_name');
		$vars['layout_type'] = $layout->row('layout_type');
		$vars['layout_content'] = $layout->row('layout_content');

		$vars['page_title'] = $this->lang->line('layout_edit');
		$vars['layout_types'] = $this->layout_model->get_layout_types();

		$vars['form_attributes']['id'] = 'mojo_layout_edit_form';
		$vars['form_hidden']['layout_id'] = $layout->row('id');

		if ($this->layout_model->count_pages_by_layout($layout_id) > 0)
		{
			$vars['layout_type_message'] = '<img src="'.site_url('assets/img/mojo_more_info_highlight.png').'" height="12" width="12" alt="'.$this->lang->line('layout_type_message_warning').'" /> <em>'.$this->lang->line('layout_type_message_warning').'</em>';
		}
		else
		{
			$vars['layout_type_message'] = '<img src="'.site_url('assets/img/mojo_more_info.png').'" height="12" width="12" alt="'.$this->lang->line('layout_type_message').'" /> '.$this->lang->line('layout_type_message');

			// If there are no pages based on this layout, then we don't need to worry about
			// issuing a warning that the layout has removed regions.
			$vars['form_hidden']['region_warning'] = 'accepted';
		}

		$vars['html_page_title'] = $this->lang->line('layout_edit');

		$this->load->view('layouts/add_edit', $vars);
	}

	// --------------------------------------------------------------------

	/**
	 * Check Regions
	 *
	 * Processes new or removed regions when a layout is edited.
	 * If regions were removed, a warning is issued to the admin during update()
	 *
	 * @access	private
	 * @param	int			layout id
	 * @param	string		region type
	 * @param	string		layout content
	 * @return	array
	 */
	function _check_regions($layout_id, $region_type = 'page', $layout_contents = NULL)
	{
		$this->load->helper('dom');

		if ($layout_contents == NULL)
		{
			$layout_dom = str_get_html($this->layout_model->get_layout($layout_id)->row('layout_content'));
		}
		else
		{
			$layout_dom = str_get_html($layout_contents);
		}

		$regions = array();

		// If an element is submitted without an id, then mojo has no way of recognizing it
		// so we'll insert "mojo_region_N" as the element id.
		$mojo_inserted_region_number = 1;

		// Replace regions with MojoMotor parsing tag, and insert into DB
		foreach($layout_dom->find('*[class=mojo_'.$region_type.'_region]') as $region)
		{
			if ( ! $region->id)
			{
				while (strpos($layout_dom, 'mojo_region_'.$mojo_inserted_region_number) !== FALSE)
				{
					$mojo_inserted_region_number++;
				}

				// id was created by Mojo, so drop it into the DOM for further processing
				$region->id = 'mojo_region_'.$mojo_inserted_region_number;
			}
			
			if ( ! $region->{'data-mojo_id'})
			{
				$region->{'data-mojo_id'} = $layout_id; 
			}

			$regions[$region->id] = trim($region->innertext);

			if ($region_type == 'global')
			{
				// Replace content with MojoMotor tag
				$region->innertext  = "\n{!-- ".$this->lang->line('global_region_comment')." --}\n".'{mojo:page:global_region id="'.$region->id.'" emb_layout_id="'.$layout_id.'"}'."\n";
			}
			else
			{
				// Replace content with MojoMotor tag
				$region->innertext  = "\n{!-- ".$this->lang->line('page_region_comment')." --}\n".'{mojo:page:page_region id="'.$region->id.'" emb_layout_id="'.$layout_id.'"}'."\n";
			}
		}

		// Some servers return this as string, others as text. This is just defensive coding.
		if (is_object($layout_dom))
		{
			$layout_dom = $layout_dom->save();
		}

		// Let's break everything down. We need existing regions, which regions are new
		// and which have been deleted. New regions will just be silently inserted, but
		// deleted ones will throw a warning, as it could produce terminal data loss.
		if ($region_type == 'global')
		{
			$current_regions = $this->layout_model->get_global_regions($layout_id);
		}
		else
		{
			$current_regions = $this->layout_model->get_page_regions($layout_id);
		}

		$new_regions = array_diff_key($regions, $current_regions);
		$deleted_regions = array_diff_key($current_regions, $regions);

		return array('layout_content'=> (string) $layout_dom, 'current_regions'=>$current_regions, 'new_regions'=>$new_regions, 'deleted_regions'=>$deleted_regions);
	}

	// --------------------------------------------------------------------

	/**
	 * Update
	 *
	 * Processes adding or updating layouts
	 *
	 * @access	public
	 * @return	string
	 */
	function update()
	{
		$this->load->library('javascript');
		$this->load->library('form_validation');

		$embed_content_rules = '';
		
		// Embed templates may not contain page regions
		if ($this->input->post('layout_type') == 'embed')
		{
			$embed_content_rules = 'callback__check_embed_pages';
		}

		$this->form_validation->set_rules('layout_name', $this->lang->line('layout_name'), 'required|alpha_dash|max_length[100]|callback__duplicate_layout_name');
		$this->form_validation->set_rules('layout_type', $this->lang->line('layout_type'), 'required');
		$this->form_validation->set_rules('layout_content', $this->lang->line('layout_content'), $embed_content_rules);
		$this->form_validation->set_error_delimiters('', '');
		


		if ($this->form_validation->run() === FALSE)
		{
			$json['result'] = 'error';
			$json['message'] = validation_errors();

			exit($this->javascript->generate_json($json));
		}
		else
		{
			$layout_id = $this->input->post('layout_id');

			$layout = array(
				'layout_name' => $this->input->post('layout_name'),
				'layout_type' => $this->input->post('layout_type'),
				'layout_content' => $this->input->post('layout_content'),
			);

			// If we're updating, we'll also have a layout_id, otherwise what we have an insert
			// Even after we insert the layout, we'll need to parse it out for regions. This
			// happens after the successful insert or edit, so that the layout has been inserted
			// and we have a stable and predictable state to work with.
			if ($layout_id)
			{
				// Remove cached files
				$this->load->helper('cache_helper');
				remove_cache();

				$layout['id'] = $layout_id;

				// Parse out Global regions, we're looking right now specifically for deleted regions
				$global_region_info = $this->_check_regions($layout_id, 'global', $this->input->post('layout_content'));

				// Parse out Page regions, we're looking right now specifically for deleted regions
				$page_region_info = $this->_check_regions($layout_id, 'page', $this->input->post('layout_content'));

				// This an update, were regions removed? This ensures a warning is fired. If the warning
				// was accepted, then skip this part - the admin has already choosen to remove the regions.
				if ((count($global_region_info['deleted_regions']) > 0 OR count($page_region_info['deleted_regions']) > 0) && $this->input->post('region_warning') != 'accepted')
				{
					// Gather all deleted regions into one place
					$deleted_regions = array_merge($global_region_info['deleted_regions'], $page_region_info['deleted_regions']);

					$json['callback'] = 'region_alter_callback';
					$json['callback_args'] = "'".implode(', ', $deleted_regions )."'";

					exit($this->javascript->generate_json($json));
				}

				if ($this->layout_model->update_layout($layout))
				{
					$json['result'] = 'success';
					$json['reveal_page'] = site_url('layouts');
					$json['message'] = $this->lang->line('layout_edit_successful').refresh_string();
				}
				else
				{
					$json['result'] = 'error';
					$json['message'] = $this->lang->line('layout_edit_fail');
				}
			}
			else
			{
				// Remove cached files
				$this->load->helper('cache_helper');
				remove_cache();

				// Over-ride the $layout_id var after an insert we we have it for region parsing below.
				if ($layout_id = $this->layout_model->insert_layout($layout))
				{
					// The subsequent update will need the layout id added to the $layout array for updating
					$layout['id'] = $layout_id;

					$json['result'] = 'success';
					$json['reveal_page'] = site_url('layouts');
					$json['message'] = $this->lang->line('layout_add_successful').refresh_string();
				}
				else
				{
					$json['result'] = 'error';
					$json['message'] = $this->lang->line('layout_add_fail');
				}
			}

			// Was the layout inserted correctly? If not, stop at this point and
			// throw an error up. If not, we'll continue to region parsing
			if ($json['result'] == 'error')
			{
				exit($this->javascript->generate_json($json));
			}

			// Layout is inserted, and we now have the layout_id available.
			// Time to parse out the global/page regions.

			// Parse out Global regions
			$global_region_info = $this->_check_regions($layout_id, 'global', $this->input->post('layout_content'));

			// The contents of the layout may have been modified, so update it
			$layout['layout_content'] = $global_region_info['layout_content'];

			// Parse out Page regions
			$page_region_info = $this->_check_regions($layout_id, 'page', $layout['layout_content']);

			// The contents of the layout may have been modified, so update it
			$layout['layout_content'] = $page_region_info['layout_content'];

			// Delete global regions not wanted anymore
			foreach ($global_region_info['deleted_regions'] as $region_id => $region_content)
			{
				$this->layout_model->delete_global_region($region_id, $layout_id);
			}

			// Insert new global regions
			foreach ($global_region_info['new_regions'] as $region_id => $region_content)
			{
				$this->layout_model->insert_global_region(array(
										'region_id'			=> $region_id,
										'region_name'		=> ucwords(str_replace('_', ' ', $region_id)),
										'layout_id'			=> $layout_id,
										'content'			=> $region_content
				));
			}

			// Delete page regions not wanted anymore
			foreach ($page_region_info['deleted_regions'] as $region_id => $region_content)
			{
				$this->page_model->delete_page_region($region_id, $layout_id);
			}

			// Since each page is based on a layout, if we are inserting a new page region,
			// then we need to loop through each page and add it to the db also. There's simply
			// no way around this.
			$pages_using_layout = $this->layout_model->get_pages_by_layout($layout_id);

			
			// If there are currently no pages using the layout (i.e. its a new layout) then we
			// must add dummy regions into the database so that new pages will properly save.
			// The dummy regions will be removed when a page is added using this layout.
			
			// No- we will do this when we actually create the page
			//if (empty($pages_using_layout))
			//{
			//	$pages_using_layout = array(0 => '');
			//}

			foreach ($pages_using_layout as $page_url_title)
			{
				// Insert new page regions
				foreach ($page_region_info['new_regions'] as $region_id => $region_content)
				{
					$this->page_model->insert_page_region(array(
											'region_id'			=> $region_id,
											'region_name'		=> ucwords(str_replace('_', ' ', $region_id)),
											'page_url_title'	=> $page_url_title,
											'content'			=> $region_content,
											'layout_id'			=> $layout_id
					));
				}
			}

			if ( ! $this->layout_model->update_layout($layout))
			{
				$json['result'] = 'error';
				$json['message'] = $this->lang->line('layout_edit_fail');
			}

			exit($this->javascript->generate_json($json));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Delete
	 *
	 * @access	public
	 * @param	int
	 * @return	mixed
	 */
	function delete($layout_id = '')
	{
		$this->load->library('javascript');

		// This is to prevent accidental right-clicks, or otherwise accessing this page
		// without having gone through the javascript confirm box.
		if ($this->input->post('confirmed') != 'true')
		{
			exit;
		}

		$json['id'] = $layout_id;

		if ($this->layout_model->delete_layout((int)$layout_id))
		{
			// Remove cached files
			$this->load->helper('cache_helper');
			remove_cache();

			$json['result'] = 'success';
			$json['message'] = $this->lang->line('layout_delete_successful');
		}
		else
		{
			$json['result'] = 'error';
			$json['message'] = $this->lang->line('layout_delete_fail');
		}

		exit($this->javascript->generate_json($json));
	}


	// --------------------------------------------------------------------

	/**
	 * Checks Embed type layouts for page regions
	 *
	 * @access	private
	 * @param	string
	 * @return	bool
	 */
	function _check_embed_pages($str)
	{
		// Do fast check
		if (strpos($str, 'mojo_page_region') !== FALSE)
		{
			$this->load->helper('dom');

			$layout_dom = str_get_html($str);

			$regions = array();

			// Get page regions
			$page_regions = $layout_dom->find('*[class=mojo_page_region]');
			
			//var_dump($page_regions); exit;
			if ( ! empty($page_regions))
			{
				$this->form_validation->set_message('_check_embed_pages', $this->lang->line('layout_embed_p_region'));
				return FALSE;
			}
		}

		return TRUE;
		
	}


	// --------------------------------------------------------------------

	/**
	 * Duplicate layout name callback
	 *
	 * @access	private
	 * @param	string
	 * @return	bool
	 */
	function _duplicate_layout_name($layout_name)
	{
		// If we're editing, we want this rule to always pass
		if ($this->input->post('layout_id'))
		{
			return TRUE;
		}

		if ($this->layout_model->layout_exists($layout_name))
		{
			$this->form_validation->set_message('_duplicate_layout_name', $this->lang->line('layout_name_taken'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
}

/* End of file layouts.php */
/* Location: system/mojomotor/controllers/layouts.php */