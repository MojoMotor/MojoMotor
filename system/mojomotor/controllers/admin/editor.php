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
 * Editor Class
 *
 * @package		MojoMotor
 * @subpackage	Controllers
 * @category	Controllers
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Editor extends Mojomotor_Controller {

	var $site_structure = array();
	var $max_thumbs_created = 15;

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
	 * The default site page
	 *
	 * @access	public
	 * @return	void
	 */
	function index()
	{
		echo '<pre>// No Oversized Polynesian-style Bamboo Horses were harmed during the production</pre>';
		echo '<pre>// of this motion picture. However, many wicker lawn chairs gave their lives.</pre>';
	}

	// --------------------------------------------------------------------

	/**
	 * Delete File
	 *
	 * The editor calls this function when deleting a file
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function delete_file($file_name = '')
	{
		// Used to ensure this came from a confirm, and not an accidental new tab or something
		if ($this->input->post('ajax') != 'true' OR $file_name === '')
		{
			return;
		}

		$this->load->model('upload_model');

		$upload_settings = $this->upload_model->get_upload();

		$upload_path = $upload_settings['server_path'];

		$file_name = urldecode($file_name);

		if ( ! @unlink($upload_path.$file_name))
		{
			exit($this->lang->line('problem_deleting_file'));
		}

		// Was there a thumb?
		if (file_exists($upload_path.'_thumbs/thumb_'.$file_name))
		{
			// If we're here, then the main image was removed, so don't
			// bother user with any errors in the thumb, as it'll just get
			// over-ridden if another file is uploaded with the same name
			// anyhow.
			@unlink($upload_path.'_thumbs/thumb_'.$file_name);
		}

		exit($file_name);
	}

	// --------------------------------------------------------------------

	/**
	 * Upload
	 *
	 * The editor calls this function when uploading a file
	 *
	 * @access	public
	 * @return	string
	 */
	function upload()
	{
		$this->load->model('upload_model');

		$upload_settings = $this->upload_model->get_upload();

		$config['upload_path'] = $upload_settings['server_path'];

		if ($upload_settings['allowed_types'] == 'all')
		{
			$config['allowed_types'] = '*';
		}
		else
		{
			// jpg are sometimes "jpe" or "jpeg" so allow those also
			$config['allowed_types'] = 'gif|png|jpg|jpeg|jpe';
		}

		$this->load->library('upload', $config);

		$field_name = 'upload'; // name comes from CKEditor, but 'upload' is easy 'nuff I guess :)

		if ( ! $this->upload->do_upload($field_name))
		{
			// The CI error message if a folder is not writable does not include the folder
			// name. If we're only getting 1 error message, and its the generic one, we'll
			// drop in something a bit more helpful.
			if (count($this->upload->error_msg) === 1 && $this->upload->error_msg[0] == $this->lang->line('upload_not_writable'))
			{
				$this->upload->error_msg[0] = '"'.$upload_settings['name'].'" : '.$this->upload->error_msg[0];
			}

			exit($this->upload->display_errors('<p style="color:#A0A0A0;font: 12px sans-serif;">'));
		}

		// Bring in the file now
		$file_data = $this->upload->data();

		// If the file isn't writable after the upload, then try to make it read/write so we
		// can delete/edit it later.
		if ( ! is_really_writable($file_data['full_path']))
		{
			@chmod($file_data['full_path'], FILE_WRITE_MODE);
		}

		// Gather some information to build out a thumb and alternative representation.
		$file_name = base_url().'mm_uploads/'.$file_data['file_name'];
		$alt = str_replace('_', ' ', $file_data['raw_name']);

		// Build thumb now
		$this->_create_thumb($config['upload_path'], $file_data);

		$output = '<html><body><script type="text/javascript">window.parent.mojoEditor.upload_result("'.$file_name.'", "'.$alt.'");</script></body></html>';
		exit($output);
	}

	// --------------------------------------------------------------------

	/**
	 * Create Thumbnail
	 *
	 * Create a Thumbnail for a file
	 *
	 * @access	public
	 * @param	mixed	directory information
	 * @param	mixed	file information
	 * @return	bool	success / failure
	 */
	function _create_thumb($dir, $data)
	{
		$this->load->library('image_lib');

		$img_path = rtrim($dir, '/').'/';
		$thumb_path = $img_path.'_thumbs/';

		if ( ! is_dir($thumb_path))
		{
			mkdir($thumb_path);

			if ( ! file_exists($thumb_path.'index.html'))
			{
				$f = fopen($thumb_path.'index.html', FOPEN_READ_WRITE_CREATE_DESTRUCTIVE);
				fwrite($f, 'Directory access is forbidden.');
				fclose($f);
			}
		}
		elseif ( ! is_really_writable($thumb_path))
		{
			return FALSE;
		}

		$this->image_lib->clear();

		$config['source_image']		= $img_path.$data['file_name'];
		$config['new_image']		= $thumb_path.'thumb_'.$data['file_name'];
		$config['maintain_ratio']	= TRUE;
		$config['width']			= 50;
		$config['height']			= 50;

		// Let's check the width/height of the image. It could be that we don't want to resize it at all
		$image_dims = getimagesize($img_path.$data['file_name']);

		if ($image_dims[0] <= 50 AND $image_dims[1] <= 50)
		{
			// Just copy it, don't create a thumb. If copy fails, we'll still fall back
			// to attempting to resize it anyhow.
			if (copy($config['source_image'], $config['new_image']))
			{
				return TRUE;
			}
		}

		$this->image_lib->initialize($config);

		if ( ! $this->image_lib->resize())
		{
			// @todo find a good way to display errors
			return FALSE;
			// die($this->image_lib->display_errors());
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * List Pages
	 *
	 * Returns a JSON response listing all site pages
	 *
	 * @access	public
	 * @return	string
	 */
	function list_pages()
	{
		$this->load->library('javascript');

		$pages = $this->page_model->get_all_pages_info(TRUE);

		echo $this->javascript->generate_json($pages);
	}

	// --------------------------------------------------------------------

	/**
	 * Browse
	 *
	 * The editor calls this function when browsing
	 *
	 * @access	public
	 * @return	void
	 */
	function browse()
	{
		if ( ! $this->input->server('QUERY_STRING'))
		{
			show_error($this->lang->line('unable_read_upload_dir'));
		}

		$this->load->helper(array('number', 'file'));
		$this->load->model('upload_model');

		// CKEditor works by passing query strings. As they are unused in the rest
		// of Mojo, this is a small hack to read them for this method only. It enables
		// compatibility with the rest of the CK* libraries, and allows for us to
		// code for them moving forward easiest.
		// [0] => CKEditor=page
		// [1] => CKEditorFuncNum=2
		// [2] => langCode=en

		$vars['CKEditorFuncNum'] = $this->input->get('CKEditorFuncNum');

		$upload_settings = $this->upload_model->get_upload();

		$vars['upload_path'] = $upload_settings['server_path'];
		$vars['upload_url'] = $upload_settings['url'];

		if ( ! $vars['files'] = $this->upload_model->get_files($upload_settings['id']))
		{
			$vars['message'] = $this->lang->line('unable_read_upload_dir');
			return $this->load->view('javascript/file_browser_message', $vars);
		}

		$vars['csrf_token'] = $this->security->get_csrf_token_name();
		$vars['csrf'] = $this->security->get_csrf_hash();

		// Let's remove the index.html and the _thumbs folder from there if they exist
		unset($vars['files']['index.html'], $vars['files']['_thumbs']);

		if (count($vars['files']) === 0)
		{
			$vars['message'] = $this->lang->line('no_files_found1').' (<em>'.$upload_settings['name'].'</em>).</p><p>'.$this->lang->line('no_files_found2');
			$this->load->view('javascript/file_browser_message', $vars);
		}
		else
		{
			// Find or create thumbs now
			// In order to avoid destroying the server if a full directory of images without
			// thumbs is found, we'll at most $this->max_thumbs_created per page load.
			$thumbs_created = 0;

			////////////////////////////
			// Begin memory adjustment
			//
			// If large images are processed, or lots of images are converted into thumbs, there
			// is a chance that memory will be pushed too hard. We'll do our best to minimize
			// this here by trying to bump it to a large number during thumbnail creation.

			if (function_exists('memory_get_usage') && memory_get_usage() && ini_get('memory_limit') != '')
			{
				@ini_set('memory_limit', '64M');
			}

			// End memory adjustment
			////////////////////////////

			foreach ($vars['files'] as $file_name => $file_data)
			{
				// Is this an image?
				if (strncmp(get_mime_by_extension($file_name), 'image', 5) == 0)
				{
					// is there a thumb?
					if ( ! file_exists($vars['upload_path'].'_thumbs/thumb_'.$file_name))
					{
						// No thumb. We can build it if we aren't over the maximum thumb count
						if ($thumbs_created <= $this->max_thumbs_created)
						{
							// Build a thumb now
							if ($this->_create_thumb($vars['upload_path'], array('file_name'=>$file_name)))
							{
								$vars['files'][$file_name]['thumb'] = $vars['upload_url'].'_thumbs/thumb_'.$file_name;
							}
							else
							{
								$vars['files'][$file_name]['thumb'] = site_url('assets/img/generic_image.png');
							}

							$thumbs_created++;
						}
						else
						{
							$vars['files'][$file_name]['thumb'] = site_url('assets/img/generic_image.png');
						}
					}
					else
					{
						$vars['files'][$file_name]['thumb'] = $vars['upload_url'].'_thumbs/thumb_'.$file_name;
					}
				}
				else
				{
					$vars['files'][$file_name]['thumb'] = site_url('assets/img/page_white.png');
				}
			}

			$this->load->view('javascript/file_browser', $vars);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Update Page Region
	 *
	 * The default site page
	 *
	 * @access	public
	 * @return	string
	 */
	function update_page_region()
	{
		$this->load->helper('cache_helper');
		
		$content		= $this->input->post('value');
		$region_id		= $this->input->post('region_id');
		$region_type	= $this->input->post('region_type');
		
		if ($region_type == 'global')
		{
			$layout_id = ($this->input->post('region_layout_id')) ? $this->input->post('region_layout_id') : $this->input->post('layout_id');
			
			remove_cache();

			if ( ! $this->page_model->update_global_region($layout_id, $region_id, $content))
			{
				log_message('error', "Unable to update global region $region_id in $layout_id");
			}
		}
		else
		{
			$page_url_title = $this->input->post('page_url_title');

			remove_cache_page($page_url_title);

			if ( ! $this->page_model->update_page_region($page_url_title, $region_id, $content))
			{
				log_message('error', "Unable to update local region $region_id on $page_url_title");
			}
		}

		// We need to send back the parsed results so the user won't get bad links
		// Send back the content for page updating
		echo $content;
	}

	// --------------------------------------------------------------------

	/**
	 * Bar State
	 *
	 * This function loads the state (ie: open or closed) of the editor bar
	 * into a session var so it'll be remembered across page loads.
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function bar_state($open = 'true')
	{
		$open = ($open == 'true') ? TRUE : FALSE;

		$this->session->set_userdata('bar_state', $open);
	}

	// --------------------------------------------------------------------

	/**
	 * CKEditor Lang
	 *
	 * Returns the language file for CKEditor from a custom MojoMotor location.
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	function ckeditor_lang()
	{
		$this->output->set_header("Content-Type: text/javascript");

		$lang_file = APPPATH.'language/'.$this->config->item('language').'/editor.js';

		// Is the file there?
		if ( ! file_exists($lang_file))
		{
			show_error('Language file not found');
		}

		// and send it out to play.
		$this->output->set_output(file_get_contents($lang_file));
	}
}

/* End of file editor.php */
/* Location: system/mojomotor/controllers/editor.php */