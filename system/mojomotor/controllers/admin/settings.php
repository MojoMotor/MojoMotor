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
 * Settings Class
 *
 * @package		MojoMotor
 * @subpackage	Controllers
 * @category	Controllers
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Settings extends Mojomotor_Controller {

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
	 * @return	string
	 */
	function index()
	{
		$this->load->model('page_model');
		$this->load->helper(array('directory', 'inflector'));
		$settings = $this->site_model->get_settings();

		$all_pages = $this->page_model->get_all_pages();

		$vars = array(
			'site_name'		=> htmlspecialchars_decode($settings->row('site_name')),
			'default_page'	=> $settings->row('default_page'),
			'page_404'		=> $settings->row('page_404'),
			'theme'			=> $settings->row('theme'),
			'lang'			=> $this->config->item('language'),
			'all_pages'		=> $all_pages,
			'page_404_list' => array(0 => $this->lang->line('none')) + $all_pages,
			'in_page_login'	=> ($settings->row('in_page_login') == 'y') ? TRUE : FALSE
		);

		$vars['all_themes'] = array();
		foreach (directory_map(APPPATH.'views/themes', TRUE) as $theme)
		{
			$vars['all_themes'][$theme] = humanize($theme);
		}

		$vars['all_langs'] = array();
		foreach (directory_map(APPPATH.'language', TRUE) as $lang)
		{
			$vars['all_langs'][$lang] = ucfirst($lang);
		}

		$this->load->view('settings/index', $vars);
	}

	// --------------------------------------------------------------------

	/**
	 * Edit
	 *
	 * Updates settings
	 *
	 * @access	public
	 * @return	string
	 */
	function edit()
	{
		$this->load->library('form_validation');
		$this->load->library('javascript');

		$this->form_validation->set_rules('site_name', 'Site Name', 'htmlspecialchars|max_length[100]');
		$this->form_validation->set_rules('default_page', 'Default Page', 'required|max_length[25]');
		$this->form_validation->set_rules('page_404', '404 Page', 'required|max_length[25]');
		$this->form_validation->set_rules('in_page_login', 'In Page Login', 'max_length[1]');
		$this->form_validation->set_rules('language', $this->lang->line('language'), 'required|max_length[50]');
		$this->form_validation->set_rules('theme', $this->lang->line('theme'), 'required|max_length[50]');
		$this->form_validation->set_error_delimiters('', '');

		if ($this->form_validation->run() === FALSE)
		{
			$json['result'] = 'error';
			$json['message'] = validation_errors();
		}
		else
		{
			// Remove cached files
			$this->load->helper('cache_helper');
			remove_cache();

			$site_data = array(
				'site_name'			=> $this->input->post('site_name'),
				'default_page'		=> $this->input->post('default_page'),
				'page_404'			=> $this->input->post('page_404'),
				'in_page_login'		=> ($this->input->post('in_page_login') == 'y') ? 'y' : 'n',
				'language'			=> $this->input->post('language'),
				'theme'				=> $this->input->post('theme')
			);

			// Return values
			// --------------
			// 0 = total failure
			// 1 = db updated worked, lang file did not
			// 2 = everything worked
			$result = $this->site_model->update_settings($site_data);

			if ($result === 2)
			{
				$json['result'] = 'success';
				$json['reveal_page'] = site_url('settings');
				$json['message'] = $this->lang->line('setting_update_successful');
			}
			elseif ($result === 1)
			{
				$json['result'] = 'error';
				$json['message'] = $this->lang->line('setting_update_lang_failure');
			}
			else
			{
				$json['result'] = 'error';
				$json['message'] = $this->lang->line('setting_update_failure');
			}
		}

		exit($this->javascript->generate_json($json));
	}
}

/* End of file settings.php */
/* Location: system/mojomotor/controllers/settings.php */