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
 * Javascript Class
 *
 * @package		MojoMotor
 * @subpackage	Controllers
 * @category	Controllers
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Javascript extends CI_Controller {

	var $theme = 'default';

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		// Call the Controller constructor.
		// Without this, the world as we know it will end!
		parent::__construct();

		$this->output->enable_profiler(FALSE);

		if ( ! defined('PATH_JQUERY'))
		{
			if ($this->config->item('use_compressed_js') == 'n')
			{
				define('PATH_JQUERY', APPPATH.'javascript/src/jquery/');
			}
			else
			{
				define('PATH_JQUERY', APPPATH.'javascript/compressed/jquery/');
			}
		}

		if ( ! defined('PATH_CKEDITOR'))
		{
			define('PATH_CKEDITOR', APPPATH.'javascript/ckeditor/');
		}

		$this->load->library('session');

		// If session is available, load the theme from there, but if it isn't fall back to default
		$this->theme = ($this->session->userdata('theme')) ? $this->session->userdata('theme') : 'default';
	}

	// --------------------------------------------------------------------

	/**
	 * Index function
	 *
	 * Every controller must have an index function, which gets called
	 * automatically by CodeIgniter when the URI does not contain a call to
	 * a specific method call
	 *
	 * @return	mixed
	 */
	public function index()
	{
		// Bracing for impact but none is insight
		// I'm suspened up high in the air so light
		exit('console.log("Attempting to load the index method of Javascript.")');
	}

	// --------------------------------------------------------------------

	/**
	 * Load Parse
	 *
	 * Sends javascript files to the browser after variable parsing
	 *
	 * @param	string
	 * @return	string
	 */
	public function load_parse($loadfile = '')
	{
		$contents = $this->_load($loadfile);

		// Time to parse files that aren't "stock" jQuery or UI or plugin files
		// We'll need this for all types of reasons, but notably lang keys. This
		// also allows us to keep markup we need to generate (there's a lot in
		// this app) in view files.

		$this->load->library('parser');
		$this->load->helper('form');

		// Javascript hates newlines, strip'em. Also, we need to escape slashes. Tabs should be
		// fine, but in the interest of browser proofing, they're gone also. Buh bye.
		$parse_vars = array(
							'cp_img_path'		=> site_url('assets/img').'/',
							'editorMarkup'		=> addslashes(str_replace(array("\n", "\r", "\t"), '', $this->load->view('javascript/editor_mode', '', TRUE))),
							'adminMarkup'		=> addslashes(str_replace(array("\n", "\r", "\t"), '', $this->load->view('javascript/admin_mode', '', TRUE))),
		);

		$contents = $this->parser->parse_string($contents, $parse_vars, TRUE);

		$this->_set_headers($loadfile);
		$this->output->set_output($contents);
	}

	// --------------------------------------------------------------------

	/**
	 * Load
	 *
	 * Sends javascript files to the browser
	 *
	 * @param	string
	 * @return	string
	 */
	public function load($loadfile = '')
	{
		$contents = $this->_load($loadfile);
		$this->_set_headers($loadfile);
		$this->output->set_output($contents);
	}

	// --------------------------------------------------------------------

	/**
	 * Load CKEditor
	 *
	 * Loads the JS needed for CKeditor, including jQuery adapter
	 *
	 * @param	string
	 * @return	string
	 */
	public function load_ckeditor()
	{
		$this->load->library('auth');

		// They have permission to be here?
		if ( ! $this->auth->is_editor())
		{
			show_error($this->lang->line('no_permissions'), 404);
		}

		// Set the basepath for CKEditor...
		$contents = 'var CKEDITOR_BASEPATH = "';
		$contents .= base_url().SYSDIR;
		$contents .= "/mojomotor/javascript/ckeditor/\";\n";

		// grab CKEditor...
		$contents .= file_get_contents(PATH_CKEDITOR.'ckeditor.js');

		// and the jQuery adapter...
		$contents .= file_get_contents(PATH_CKEDITOR.'adapters/jquery.js');

		// Now slap some js headers in there...
		$this->_set_headers('load_ckeditor');

		// and send it out to play.
		$this->output->set_output($contents);
	}

	// --------------------------------------------------------------------

	/**
	 * Load CKEditor Plugin
	 *
	 * Can load one-off plugins from the ckeditor plugin directory
	 *
	 * @param	string
	 * @return	string
	 */
	public function load_ckeditor_plugin($loadfile = '')
	{
		if (empty($loadfile))
		{
			return;
		}

		$loadfile = $this->security->sanitize_filename($loadfile);

		$path = PATH_CKEDITOR.'plugins/'.$loadfile.'/plugin.js';

		// Ensure the plugin is there
		if ( ! file_exists($path))
		{
			show_404();
		}

		// grab plugin...
		$contents = file_get_contents($path);

		// Now slap some js headers in there...
		$this->_set_headers('load_ckeditor_plugin');

		// and send it out to play.
		$this->output->set_output($contents);
	}

	// --------------------------------------------------------------------

	/**
	 * _Load
	 *
	 * Handles the bulk of the work of retrieving a javascript file and handing off
	 * the contents to load() or load_parse()
	 *
	 * @param	string
	 * @return	string
	 */
	private function _load($loadfile)
	{
		if ($loadfile == '')
		{
			show_error($this->lang->line('missing_js_file'));
		}

		$loadfile = $this->security->sanitize_filename($loadfile);
		$addon_seg = $this->security->sanitize_filename($this->uri->segment(4));
		
		if ($loadfile == 'jquery')
		{
			$file = PATH_JQUERY.'jquery.js';
		}
		elseif ($loadfile == 'ui')
		{
			$file = PATH_JQUERY.'ui/jquery.ui.custom.js';
		}
		elseif ($loadfile == 'plugin')
		{
			$file = PATH_JQUERY.'plugins/'.$addon_seg.'.js';
		}
		elseif ($loadfile == 'effect')
		{
			$file = PATH_JQUERY.'ui/effect.'.$addon_seg.'.js';
		}
		else
		{
			if ($this->config->item('use_compressed_js') == 'n')
			{
				$file = APPPATH.'javascript/src/'.$loadfile.'.js';
			}
			else
			{
				$file = APPPATH.'javascript/compressed/'.$loadfile.'.js';
			}
		}

		if ( ! file_exists($file))
		{
			if ($this->config->item('debug') >= 1)
			{
				$this->output->fatal_error($this->lang->line('missing_js_file'));
			}
			else
			{
				return FALSE;
			}
		}

		return file_get_contents($file);
	}


	// --------------------------------------------------------------------

	/**
	 * Javascript Combo Loader
	 *
	 * Combo load multiple javascript files to reduce HTTP requests
	 * BASE.AMP.'C=javascript&M=combo&ui=ui,packages&file=another&plugin=plugins&package=third,party,packages'
	 *
	 * @todo check for duplicated files.
	 * @return string
	 */
	public function combo_load()
	{
		$this->output->enable_profiler(FALSE);

		$contents = '';

		$file_mtime = array();

		// Load jQuery UI
		$ui = $this->input->get_post('ui');
		$load_file = $this->input->get_post('file');
		$plugins = $this->input->get_post('plugin');

		if ($ui)
		{
			$ui = explode(',', $ui);

			foreach ($ui as $ui)
			{
				$ui = $this->security->sanitize_filename($ui);
				$file = PATH_JQUERY.'ui/ui.'.$ui.'.js';

				if (file_exists($file))
				{
					$contents .= file_get_contents($file)."\n\n";

					$file_mtime[$file] = filemtime($file);
				}
			}
		}

		if ($load_file)
		{
			$load_file = explode(',', $load_file);

			foreach ($load_file as $file)
			{
				$parts = explode('/', $file);
				$clean_parts = array();
				
				foreach ($parts as $part)
				{
					if ($part != '..')
					{
						$clean_parts[] = $this->security->sanitize_filename($part);
					}
				}
				
				$file = implode('/', $clean_parts);
				$file = APPPATH.'javascript/compressed/'.$file.'.js';

				if (file_exists($file))
				{
					$contents .= file_get_contents($file)."\n\n";

					$file_mtime[$file] = filemtime($file);
				}
			}
		}

		// Load Plugins
		if ($plugins)
		{
			$plugins = explode(',', $plugins);

			foreach ($plugins as $plugin)
			{
				$plugin = $this->security->sanitize_filename($plugin);
				$file = PATH_JQUERY.'plugins/'.$plugin.'.js';

				if (file_exists($file))
				{
					$contents .= file_get_contents($file)."\n\n";

					$file_mtime[$file] = filemtime($file);
				}
			}
		}

		header("Content-type: text/javascript");
		exit($contents);
	}

	// --------------------------------------------------------------------

	/**
	 * Set Headers
	 *
	 * @param string
	 * @return str
	 */
	private function _set_headers($file, $mtime = FALSE)
	{
		$this->output->set_header("Content-Type: text/javascript");
/*
		$max_age		= 5184000;
		$modified_since	= $this->input->server('HTTP_IF_MODIFIED_SINCE');

		if ($mtime !== FALSE)
		{
			$modified = $mtime;
		}
		elseif (@filemtime($file) !== FALSE)
		{
			$modified = filemtime($file);
		}
		else
		{
			
			$modified = time();
		}

		// Remove anything after the semicolon

		if ($pos = strrpos($modified_since, ';') !== FALSE)
		{
			$modified_since = substr($modified_since, 0, $pos);
		}

		// If the file is in the client cache, we'll
		// send a 304 and be done with it.

		if ($modified_since && (strtotime($modified_since) == $modified))
		{
			$this->output->set_status_header(304);
			exit;
		}

		// Send a custom ETag to maintain a useful cache in
		// load-balanced environments
        $this->output->set_header("ETag: ".md5($modified.$file));

		// All times GMT
		$modified = gmdate('D, d M Y H:i:s', $modified).' GMT';
		$expires = gmdate('D, d M Y H:i:s', time() + $max_age).' GMT';

		$this->output->set_status_header(200);
		$this->output->set_header("Cache-Control: max-age={$max_age}, must-revalidate");
		$this->output->set_header('Vary: Accept-Encoding');
		$this->output->set_header('Last-Modified: '.$modified);
		$this->output->set_header('Expires: '.$expires);
		*/
    }

	// --------------------------------------------------------------------

	/**
	 * Mojo
	 *
	 * Simply the basic MojoMotor js setup. Mostly this defines vars needed
	 * elsewhere in the system.
	 *
	 * @param	string
	 * @return	string
	 */
	public function mojo()
	{
		$page = func_get_args();
		$page = implode('/', $page);
		
		if ($page === '')
		{
			return;
		}

		// $this->load->database();
		$this->load->model(array('page_model', 'member_model'));
		$this->load->library('auth');

		$out = 'var Mojo = ' . $this->_generate_mojo_json($page);
		
		$this->output->set_header("Content-Type: text/javascript");
		$this->output->set_output($out);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Generate MojoMotor JSON
	 *
	 * This method generates a big 'ole pile of JSON.  
	 *
	 * @param 	
	 */
	private function _generate_mojo_json($page)
	{
		$url_separator = ( ! $this->config->item('url_separator')) ? '-' : $this->config->item('url_separator');
		
		$page_info = $this->page_model->get_page_content($page);
		
		$expanded_bar_opts = ($this->config->item('show_expanded_image_options')) ? TRUE : FALSE;

		$update = FALSE;
		
		// Auto update flag. We'll check if this is an admin before we spend time loading resources.
		if ($this->auth->is_admin())
		{
			// We'll need the CP and site model to get resources
			$this->load->model('site_model');
			$this->load->library('cp');

			if (version_compare($this->cp->mojo_version, $this->site_model->get_setting('mojo_version')) == 1)
			{
				$update = TRUE;
			}
		}

		// Build 'er up
		$page = array(
				'edit_mode'	=> 'wysiwyg',
				'Lang'	=> $this->_setup_language_keys(),
				'URL'	=> array(
						'admin_path'		=> rtrim(site_url('admin'), '/'),
						'css_path'			=> site_url('assets/css'),
						'js_path'			=> site_url('javascript/load_parse'),
						'separator'			=> $url_separator,
						'site_path'			=> rtrim(site_url(''), '/'),
				),
				'Vars'	=> array(
						'additional_css'	=> $this->config->item('additional_css'),
						'bar_state'			=> $this->session->userdata('bar_state'),
						'csrf'				=> $this->security->get_csrf_hash(),
						'csrf_token'		=> $this->security->get_csrf_token_name(),
						'layout_id'			=> ($page_info) ? $page_info->layout_id : NULL,
						'page_id'			=> ($page_info) ? $page_info->id : 0,
						'page_url_title'	=> $page,
						'show_expanded_image_options' => $expanded_bar_opts,
						'update_flag'		=> $update
				),
				'toolbar'=> array(),
		);
		
		if ($this->auth->is_editor())
		{
			// Parse URLs of plugin and skin paths to get the URI, which we'll
			// pass to CKEditor. This solves the bug where CKEditor would fail to
			// load if the word "lang" was contained in the domain of a site while
			// still allowing Mojo to run from a subfolder.
			$skin_path_array = parse_url(base_url().SYSDIR.'/mojomotor/views/themes/'.$this->theme.'/editor_skin/');
			$editor_path_array = parse_url(base_url().SYSDIR.'/mojomotor/javascript/ckeditor/plugins/');
			
			$page['URL']['editor_skin_path'] = $skin_path_array['path'];
			$page['URL']['editor_plugin_path'] = $editor_path_array['path'];

			$page['URL']['editor_lang_path'] = site_url('editor/ckeditor_lang');
			
			$prefs = $this->member_model->get_member_by_id($this->session->userdata('id'));
			
			$page['edit_mode'] = $prefs->row('edit_mode');

			// If the ckeditor config isn't viable, fail silently and load the most minimal possible bar
			if ($this->config->load('ckeditor', FALSE, TRUE))
			{
				$page['toolbar'] = $this->config->item('wysiwyg_toolbar');				
			}
			else
			{
				$page['toolbar'] = array(array('mojo_save'), array('Maximize'), array('mojo_cancel'));
			}
		}
		
		$this->load->library('javascript');
		return $this->javascript->generate_json($page, TRUE);
	}	

	// --------------------------------------------------------------------	
	
	/**
	 * Setup language keys
	 *
	 * This is just to break it out from the larger method above.  If you need
	 * to add a new language key, simply add it to the array below, and it'll 
	 * work
	 *
	 * @return array 
	 */
	private function _setup_language_keys()
	{
		$words = array(
			'close', 'logout', 'logout_confirm', 'member_delete', 'page_delete', 
			'subpage_delete', 'layout_delete', 'delete_confirm', 'email', 
			'email_password_warning', 'layouts', 'pages', 'members', 'settings', 
			'utilities', 'local', 'global', 'super_global', 'layout_region_warning_title', 
			'layout_region_warning', 'last_item_delete', 'enter_url', 
			'open_in_new_window', 'login_result_failure', 'run_update', 'or',
			'or_choose_page', 'or_choose_page_dropdown'
		);
		
		$lang_array = array();
		
		foreach ($words as $word)
		{
			$lang_array[$word] = $this->lang->line($word);
		}
		
		return $lang_array;
	}
}

/* End of file javascript.php */
/* Location: system/mojomotor/controllers/javascript.php */