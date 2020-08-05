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
 * Setup Class
 *
 * @package		MojoMotor
 * @subpackage	Controllers
 * @category	Controllers
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Setup extends CI_Controller {

	// Various operations of the setup process require generating random strings. This controls their length.
	var $random_name_length = 12;

	// Email and Password gets displayed at the end of the installer
	var $admin_password = '';
	var $admin_email = '';

	// Some database defaults and information that needs tracking throughout the process
	var $db_driver = 'mysql';
	var $db_prefix = 'mojo_';

	// Figured out in the constructor and needed for the install process
	var $base_url = '';

	// The folder containing the default site.
	var $default_site_folder = 'default';

	// The default uploads folder
	var $default_upload_location = "mm_uploads";

	// A list of pages we'll look for (in descending order) as candidates to be the $default_page
	var $desired_default_pages = array('index.html', 'index.htm', 'default.html', 'default.htm', 'home.html', 'home.htm');

	// If this is an update, the update method takes care of this
	var $mojo_version = '1.2.1';

	// Track messages for the user during the update process
	var $update_notices = array();

	/**
	 * Constructor
	 *
	 * Declared in PHP 4 fashion here and in the welcome page so that we can send an error
	 * if the user is not using a high enough version of PHP. We want to warn them before
	 * this causes a PHP error.
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();
		
		// Within this controller, ONLY update can be visited with the installer lock set to true
		if ($this->router->method != 'update' && $this->config->item('install_lock') != 'unlocked')
		{
			// Give the impression that the setup controller has been removed.
			show_404();
		}

		// This is needed for every update, load it now.
		$this->lang->load('install');
		$this->load->helper('string');
		$this->load->library('session');

		// The base_url won't be available (unless its a validation error but the user has
		// correctly set this value) so detect for that, and then work it out dynamically.
		if (strpos($this->input->server("REQUEST_URI"), 'index'.EXT))
		{
			$temp_base_url = substr($this->input->server("REQUEST_URI"), 0, strpos($this->input->server("REQUEST_URI"), 'index'.EXT));
		}
		else
		{
			$temp_base_url = $this->input->server("REQUEST_URI");
		}

		// What port are we on? If it isn't 80, append it in.
		$port = $this->input->server("SERVER_PORT");
		$temp_server = ($port != '80') ? $this->input->server("SERVER_NAME").":$port" : $this->input->server("SERVER_NAME");
		$protocol = ($this->input->server('HTTPS') != '' && strtolower($this->input->server('HTTPS')) != 'off') ? 'https://' : 'http://';

		$this->base_url = trim($protocol.$temp_server.$temp_base_url, '/').'/';
		$this->config->set_item('base_url', $this->base_url);

		// During the install process, the base_url won't have been set, and thus the site path to things
		// like image assets won't be accurate. To get around this, the _ci_view_path gets dynamically built.
		// So we'll simply add them to the top of the page instead.
		$this->load->vars(array('additional_style'=>
			".mojo_header h1 {
				background: url(".site_url('assets/img/mojomotor_logo_only.jpg').") no-repeat left top;
			}
			.mojo_submit_ajax {
				background: #ddd url(".site_url('assets/img/ajax-loader.gif').") no-repeat center center!important;
			}
			.mojo_header {
				background: #222 url(".site_url('assets/img/mojobar_bg.jpg').") top;
			}
			.button {
				background: #F5C062 url(".site_url('assets/img/button_back.png').") repeat-x center center;
			}
		"));
	}

	// --------------------------------------------------------------------

	/**
	 * Index
	 *
	 * The license agreement, after accepting the user is advanced to verify_state()
	 *
	 * @access	public
	 * @return	string
	 */
	function index()
	{
		$this->load->helper('typography');
		$this->lang->load('license');

		$vars['page_title'] = $this->lang->line('license');

		$this->load->view('setup/license', $vars);
	}

	// --------------------------------------------------------------------

	/**
	 * Verify State
	 *
	 * A check to ensure the server setup meets some conditions needed to install MojoMotor.
	 *
	 * @access	public
	 * @param	bool
	 * @return	mixed
	 */
	function verify_state($skip_config_check = FALSE)
	{
		$skip_config_check = ($skip_config_check == 'skip_config_check') ? TRUE : FALSE;

		$vars['installation_warnings'] = array();

		// Do we show the user the message that they must manually configure things?
		$vars['must_manual_config'] = FALSE;

		// High enough PHP version?
		if ( ! is_php('5.1.6'))
		{
			$vars['installation_warnings'][] = $this->lang->line('min_php_version');
		}

		// Testing if the config file is readable is unneeded, as CodeIgniter would have
		// already thrown an internal error by this stage if it was not. Is the config
		// file writable? Does it need to be?
		if ( ! $skip_config_check && ! is_really_writable($this->config->config_path) && ! @chmod($this->config->config_path, FILE_WRITE_MODE))
		{
			$vars['must_manual_config'] = TRUE;
			$vars['installation_warnings'][] = $this->lang->line('unwritable_config');
		}

		// Is there a database.php file?
		if (@include($this->config->database_path))
		{
			// Can we connect with provided information? Don't trust SQLite, but otherwise we're done
			if ($db[$active_group]['dbdriver'] != 'sqlite3' && $this->_db_connection_test($db[$active_group]))
			{
				$this->session->set_userdata('load_db_from_config', TRUE);
			}
			else
			{
				// Ensure the session isn't remembered from a previous test
				$this->session->set_userdata('load_db_from_config', FALSE);

				// The connection information wasn't valid, so the database.php file needs to be changed.
				// We'll try to make it writable so that MojoMotor can handle this internally, but even
				// if it can't, we won't issue any complaints, it'll simply be up to the admin to handle it.
				@chmod($this->config->database_path, FILE_WRITE_MODE);

				if (is_really_writable($this->config->database_path) === FALSE)
				{
					$vars['must_manual_config'] = TRUE;
					$vars['installation_warnings'][] = $this->lang->line('unwritable_database');
				}
			}
		}
		else
		{
			$vars['installation_warnings'][] = $this->lang->line('unreadable_database');
		}

		// Is the cache folder writable?
		// The cache is not needed "out of the box", so we'll still try to make it writable, but we won't
		// prevent MojoMotor from installing if it isn't (or cannot be).
		if ( ! is_really_writable(BASEPATH.'cache') && ! @chmod(BASEPATH.'cache', FILE_WRITE_MODE))
		{
			log_message('debug', $this->lang->line('unwritable_cache_folder'));
		}

		// Is the upload folder writable?
		// We'll try to make it writable, but we won't prevent MojoMotor from installing if it isn't (or cannot be).
		// if ( ! is_really_writable(FCPATH.$this->default_upload_location) && ! @chmod(FCPATH.$this->default_upload_location, DIR_WRITE_MODE))
		// {
		// 	log_message('debug', $this->lang->line('unwritable_uploads_folder'));
		// }

		if (count($vars['installation_warnings']) == 0)
		{
			redirect('setup/wizard');
		}
		else
		{
			$vars['page_title'] = $this->lang->line('install_warning');
			$this->load->view('setup/install_warning', $vars);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Wizard
	 *
	 * The installation wizard
	 *
	 * @access	public
	 * @return	string
	 */
	function wizard()
	{
		$this->load->library('form_validation');
		$this->load->helper('form');

		// Earlier, we checked if the database.php info was correct. If so, we don't need
		// some of the validation. Also, set 'db_file_connected' to TRUE if it works.
		if ($this->session->userdata('load_db_from_config') === FALSE)
		{
			$vars['db_file_connected'] = FALSE;

			$this->form_validation->set_rules('db_type', $this->lang->line('db_type'), 'required|callback__db_verify');
			$this->form_validation->set_rules('db_name', $this->lang->line('db_name'), 'alpha_dash|max_length[64]');
			$this->form_validation->set_rules('db_host', $this->lang->line('db_host'), 'max_length[100]');
			$this->form_validation->set_rules('db_user', $this->lang->line('site_title'), 'max_length[100]');
			$this->form_validation->set_rules('db_password', $this->lang->line('db_password'), 'max_length[100]');
			$this->form_validation->set_rules('db_prefix', $this->lang->line('db_prefix'), 'alpha_dash|max_length[100]');

			$this->form_validation->set_message('_db_verify', $this->lang->line('db_unable_to_connect'));
		}
		else
		{
			$vars['db_file_connected'] = TRUE;
		}

		$this->form_validation->set_rules('email', $this->lang->line('email'), 'required|max_length[60]|valid_email');
		$this->form_validation->set_rules('site_title', $this->lang->line('site_title'), 'htmlspecialchars|max_length[100]');
		$this->form_validation->set_rules('password', $this->lang->line('password'), 'min_length[6]|max_length[50]');

		$this->form_validation->set_error_delimiters('<p class="error">', '</p>');

		if ($this->form_validation->run())
		{
			$vars = $this->_do_install();

			if ($this->input->post('password') == '')
			{
				$vars['message'] = $this->lang->line('important_info_blank_pass');
			}
			else
			{
				$vars['message'] = $this->lang->line('important_info_user_picked_pass');
			}

			$this->load->view('setup/success', $vars);
		}
		else
		{
			$vars['page_title'] = $this->lang->line('install');

			// This is used to track if the advanced options should be exposed or not after a validation fail
			$vars['show_advanced'] = ($this->input->post('show_advanced') == 'y') ? 'y' : 'n';

			// Do we present an option for sqlite?
			$vars['sqlite_support'] = (extension_loaded('pdo_sqlite')) ? TRUE : FALSE;

			// temporary overwrite
			$vars['sqlite_support'] = FALSE;

			// For layout, we need to know how many options we're offering. This is 1 for "blank"
			$site_count = 1;

			$importable_sites = $this->_importable_files();

			// Start assuming the default site is not going to be available
			$vars['offer_default_site'] = FALSE;

			// Is the default site present? If so, exclude it from the list of
			// importable options, but account for it in the wizard
			if (isset($importable_sites[$this->default_site_folder]))
			{
				$site_count++;
				$vars['offer_default_site'] = TRUE;
				unset($importable_sites[$this->default_site_folder]);
			}

			// If there are candidate files for import, let's offer the opportunity to import during install
			$vars['importable_sites'] = array();

			foreach (array_keys($importable_sites) as $import_site)
			{
				$vars['importable_sites'][$import_site] = ucwords(str_replace('_', ' ', $import_site));
			}

			$vars['site_options_rowcount'] = (count($vars['importable_sites']) > 0) ? $site_count + 1 : $site_count;

			// Radio button settings
			// @confirm: these next few lines are among the ones I'm least pleased with, but I
			// couldn't find a more clever approach here while still using set_checkbox() in the view
			$vars['site_content']['import_site'] = ($this->input->post('site_content') == 'import_site') ? TRUE : FALSE;
			$vars['site_content']['default_site'] = ( ! $this->input->post('site_content') OR $this->input->post('site_content') == 'default_site') ? TRUE : FALSE;
			$vars['site_content']['blank_site'] = ($this->input->post('site_content') == 'blank_site') ? TRUE : FALSE;
			$vars['pconnect']['y'] = ($this->input->post('pconnect') == 'y') ? TRUE : FALSE;
			$vars['pconnect']['n'] = ($this->input->post('pconnect') == 'y') ? FALSE : TRUE;
			$vars['db_type']['mysql'] = ($this->input->post('db_type') == 'sqlite3') ? FALSE : TRUE;
			$vars['db_type']['sqlite'] = ($this->input->post('db_type') == 'sqlite3') ? TRUE : FALSE;

			$this->load->view('setup/wizard', $vars);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Do Install
	 *
	 * What it says on the tin! Installs the software
	 *
	 * @access	private
	 * @return	mixed
	 */
	function _do_install()
	{
		// Set up a variable to hold user notices.
		$vars['notices'] = array();

		// This is needed throughout the install
		$this->db_prefix = $this->input->post('db_prefix');

		if ($this->input->post('db_type') === 'sqlite3')
		{
			if ( ! $this->_setup_sqlite())
			{
				show_error($this->lang->line('unwritable_database'));
			}
		}
		else
		{
			if ( ! $this->_setup_mysql())
			{
				show_error($this->lang->line('unwritable_database'));
			}
		}

		// Database is up and running now, but during the setup, some config prefs might not be
		// registered correctly. Now that everything is written into the config.php and database.php
		// files, we destroy the db so that we can re-init it using the settings in those files.
		// Database Forge is needed to construct the tables, so load it also.
		unset($this->db);
		$this->load->database();
		$this->load->dbforge();

		// These models are all used during the install process to insert data
		$this->load->model(array('setup_model', 'page_model', 'member_model', 'layout_model', 'site_model'));

		// We don't want to force the user to open up a config file, but leaving the encryption_key
		// at default value isn't a great idea for security, so we'll update it here before it gets
		// used in _add_site_data(). This *will* generate a session log error as we change encryption
		// keys, "The session cookie data did not match what was expected." but its unavoidable.
		// While we're updating configs, we'll set the base_url also.
		if ( ! $this->config->config_update(array('encryption_key'=>'mojo_'.random_string('alnum'), 'base_url'=>$this->base_url)))
		{
			// If we got here, and config_update isn't readable, don't say anything. The user would
			// already have needed to explicitly ignore this error, and has recieved instructions on
			// how to configure this themselves in verify_state()
			log_message('debug', 'Not able to write config file in _do_install');
		}

		// field_definitions() returns an array of fields for each table.
		if ( ! is_array($db_schema = $this->setup_model->field_definitions()))
		{
			show_error("Could not retrieve field definitions");
		}

		$tables = array_keys($db_schema);

		foreach ($tables as $table)
		{
			// If they've made it to here, they've opted to over-ride their old install, so
			// if the table exists, blast it first.

			if ($this->db->table_exists($table))
			{
				$this->dbforge->drop_table($table);
			}

			$this->dbforge->add_field($db_schema[$table]);

			// If there's an id field, let's set it up as a key. There is an exception for
			// the session table, as that's pre-set by CodeIgniter and we shouldn't change it.
			if (isset($db_schema[$table]['id']))
			{
				$this->dbforge->add_key('id', TRUE);
			}
			elseif (isset($db_schema[$table]['session_id']))
			{
				$this->dbforge->add_key('session_id', TRUE);
			}

			// region id is used a lot for lookups, so let's make it a key
			if ($this->db_driver != 'sqlite3' && ($table == 'page_regions' OR $table == 'global_regions'))
			{
				$this->dbforge->add_key('region_id');
			}
			elseif ($table == 'pages')
			{
				$this->dbforge->add_key('url_title');
			}

			if ( ! $this->dbforge->create_table($table))
			{
				show_error("Could not create the table $table");
			}
		}

		// Add the data needed by MojoMotor
		if ( ! $this->_add_site_data())
		{
			show_error($this->lang->line('add_site_data_fail'));
		}

		// If the admin indicated they wanted to try an import, its time to rawk the _import_site()
		// Alternatively, they can select "blank", or "default"
		if ($this->input->post('site_content') == 'import_site')
		{
			if ( ! $this->_import_site($this->input->post("import_site")))
			{
				show_error("There was a problem importing your site");
			}
		}
		elseif ($this->input->post('site_content') == 'default_site')
		{
			if ( ! $this->_install_default_site())
			{
				show_error("There was a problem installing the default site your site");
			}
		}
		else
		{
			if ( ! $this->_install_blank_site())
			{
				show_error("There was a problem installing a blank site on your system");
			}
		}

		if ( ! $this->config->config_update(array('install_lock'=>"locked")))
		{
			$vars['notices'][] = $this->lang->line('set_installation_lock');
		}

		// These files are almost certainly already not writable as they are passed through the config
		// lib, which sets them to non-writable when its done, but if the admin manually did some
		// manual configuration they still might be. Silently try to make them read-only.
		@chmod($this->config->config_path, FILE_READ_MODE);
		@chmod($this->config->database_path, FILE_READ_MODE);

		// And we're done. Success notification for them
		$vars['page_title'] = $this->lang->line('install');

		// Add the email and password to the beginning of the notices array
		array_unshift($vars['notices'], $this->lang->line('login_with_email').' ('.$this->admin_email.')', $this->lang->line('password').': <strong>'.$this->admin_password.'</strong>');

		return $vars;
	}

	// --------------------------------------------------------------------

	/**
	 * Setup MySQL
	 *
	 * Prepares SQLite for MojoMotor
	 *
	 * @access	private
	 * @return	bool
	 */
	function _setup_mysql()
	{
		// If connection data in database.php has already proven to work, no need to re-write this
		if ($this->session->userdata('load_db_from_config'))
		{
			return TRUE;
		}

		$db_config['hostname'] = $this->input->post('db_host');
		$db_config['username'] = $this->input->post('db_user');
		$db_config['password'] = $this->input->post('db_password');
		$db_config['database'] = $this->input->post('db_name');
		$db_config['dbdriver'] = $this->db_driver;
		$db_config['dbprefix'] = $this->db_prefix;
		$db_config['pconnect'] = ($this->input->post('pconnect')) ? TRUE : FALSE;

		// Update config/database.php with the correct information for this database setup
		return $this->config->dbconfig_update($db_config);
	}

	// --------------------------------------------------------------------

	/**
	 * Setup SQLite
	 *
	 * Prepares SQLite for MojoMotor
	 *
	 * @access	private
	 * @return	bool
	 */
	function _setup_sqlite()
	{
		$this->load->helper('file');

		// We'll need a folder for the database. A random name is used as an added precaution against directory browsing
		$db_path = ($this->input->post('sqlite_path') != '') ? $this->input->post('sqlite_path') : APPPATH.'_'.strtolower(random_string('alpha', $this->random_name_length));

		if ( ! @mkdir($db_path, DIR_WRITE_MODE))
		{
			// return FALSE;
			show_error('Could not create the directory '.$db_path.' for your SQLite database.');
		}

		// Populate our new folder with an index.html file as an added precaution against directory browsing
		// Try to grab the contents for index.html from a known CI file (future-proofing ftw!)
		$existing_index = read_file(BASEPATH.'index.html');
		$html = ($existing_index !== FALSE) ? $existing_index : '<html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>';

		if ( ! @write_file($db_path.'/index.html', $html))
		{
			// Just log this one, don't show_error() or return FALSE
			log_message('debug',' Unable to write index.html to '.$db_path);
		}

		// If MojoMotor is picking the name, it'll be random as an added precaution against directory browsing
		$db_name = ($this->input->post('sqlite_name') != '') ? $db_path.'/'.$this->input->post('sqlite_name').'.sqlite' : $db_path.'/'.strtolower(random_string('alpha', $this->random_name_length)).'.sqlite';

		// Update config/database.php with the correct information for this database setup,
		// including the random path and database name.
		return $this->config->dbconfig_update(array(
												'database' => $db_name,
												'dbdriver' => 'sqlite3',
												'dbprefix' => $this->db_prefix,
												'pconnect' => FALSE
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Importable Files
	 *
	 * Generates an array of files that are candidates to import into the site
	 *
	 * @access	private
	 * @return	array
	 */
	function _importable_files()
	{
		$this->load->helper('directory');

		// Take a look at all files currently in the import directory. If it isn't
		// found (or can't be read), send a blank array back, as if it was found but empty.
		if ( ! $import_files = directory_map('import/'))
		{
			return array();
		}

		// Without one of our desired home pages, we can't continue, return as if
		// the entire directory was empty.
		foreach ($import_files as $dir=>$files)
		{
			if ( ! $this->_determine_default($files))
			{
				unset($import_files[$dir]);
			}
		}

		return $import_files;
	}

	// --------------------------------------------------------------------

	/**
	 * Determine Default
	 *
	 * Mojo looks for a very specific set of pages to be used as the default
	 * page and as a site's layout template. This function looks for that
	 * page, and returns it, or FALSE on failure to locate a suitable page.
	 *
	 * @access	private
	 * @param	array	an array of files to look through
	 * @return	mixed	a string filename, or FALSE on failure
	 */
	function _determine_default($files)
	{
		// We need first some detection, and then a foreach loop here, and not
		// in_array() or array_search() as depending on the directory stucture,
		// this will sometimes be a straight array, sometimes a multi-dimensional,
		// and sometimes none of the above. There's just no good way to avoid this.

		// If this is just a regular file and not a directory, then there's nothing
		// to find.
		if (is_string($files))
		{
			return FALSE;
		}

		foreach ($files as $dir => $file)
		{
			// Only HTML pages need be examined (.html or .htm and strings)
			// Subfolders are represented in arrays - we're not interested in those.
			if ( ! is_string($file) OR strpos($file, '.htm') === FALSE)
			{
				continue;
			}

			foreach ($this->desired_default_pages as $page)
			{
				if (array_search($page, $files) !== FALSE)
				{
					return $page;
				}
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Import Site
	 *
	 * Does the heavy lifting of reading a site to be imported, inserting
	 * the layout, settings and pages into MojoMotor.
	 *
	 * @access	private
	 * @param	string
	 * @return	void
	 */
	function _import_site($import_directory = '')
	{
		if ($import_directory == '')
		{
			return FALSE;
		}

		// Track if this is the default site or not
		$default_site_install = ($import_directory == 'default') ? TRUE : FALSE;

		// We'll need to reference this a few times through this method
		$import_directory = trim('import/'.$import_directory, '/').'/';

		$this->load->library('parser');
		$this->load->helper(array('file', 'url', 'directory', 'dom'));
		$this->load->model(array('layout_model', 'page_model'));

		$import_files = directory_map($import_directory);

		// Default page is used as the site's layout, as well as the
		// "default_page" in site settings.
		$default_page = $this->_determine_default($import_files);

		// --------------------------------------------------------------------
		// Setup the Layout
		// --------------------------------------------------------------------

		$layout_content = read_file($import_directory.$default_page);

		// Drop in the layout. We need to insert it now, as we need the id, but content 
		// is yet to be parsed out, so we'll update it again momentarily.
		$layout_id = $this->layout_model->insert_layout(array(
									'layout_name'		=> 'main_layout',
									'layout_type'		=> 'webpage',
									'layout_content'	=> $layout_content
		));

		// No layout_id means the database never recieved this. Time to bail.
		if ($layout_id === FALSE)
		{
			return FALSE;
		}

		$layout_content = $this->_add_mojo($layout_content);

		$layout_dom = str_get_html($layout_content);

		// Replace Global regions with MojoMotor parsing tag, and insert into DB
		foreach($layout_dom->find('*[class=mojo_global_region]') as $global_region)
		{
			// Add region to DB
			$this->layout_model->insert_global_region(array(
									'region_id'			=> $global_region->id,
									'region_name'		=> ucwords(str_replace('_', ' ', $global_region->id)),
									'layout_id'			=> $layout_id,
									'content'			=> $global_region->innertext
			));

			// Replace content with MojoMotor tag
			$global_region->innertext  = '{mojo:page:global_region id="'.$global_region->id.'"}';
		}

		// Replace Page regions with MojoMotor parsing tag, content from page regions comes
		// from the pages themselves lower on.
		foreach($layout_dom->find('*[class=mojo_page_region]') as $page_region)
		{
			// Replace content with MojoMotor tag
			$page_region->innertext  = '{mojo:page:page_region id="'.$page_region->id.'"}';
		}

		// Some servers return this as string, others as text. This is just defensive coding.
		if (is_object($layout_dom))
		{
			$layout_dom = $layout_dom->save();
		}

		// A final update, with content that's had tags swapped out.
		$this->layout_model->update_layout(array(
									'id'				=> $layout_id,
									'layout_content'	=> (string) $layout_dom
		));


		// --------------------------------------------------------------------
		// Setup the Pages
		// --------------------------------------------------------------------

		$url_separator = ( ! $this->config->item('url_separator')) ? '-' : $this->config->item('url_separator');

		foreach ($import_files as $file)
		{
			// Only HTML pages will be imported (.html or .htm)
			// Subfolders are represented in arrays - we're not interested in those.
			if ( ! is_string($file) OR strpos($file, '.htm') === FALSE)
			{
				continue;
			}

			// Get the file contents
			$file_contents = read_file($import_directory.$file);

			// For extracting content for editable regions, parse the file contents out
			$file_dom = str_get_html($file_contents);

			// Get the page <title>
			$raw_title = $file_dom->find('title', 0); // PHP 4 style... sigh
			$page_title = $raw_title->plaintext;

			$url_title = url_title(substr(ltrim($file, '_'), 0, strpos($file, '.htm')), $url_separator);

			$meta = get_meta_tags($import_directory.$file);

			$page_id = $this->page_model->insert_page(array(
									'page_title'		=> $page_title,
									'url_title'			=> $url_title,
									'meta_keywords'		=> (isset($meta['keywords'])) ? $meta['keywords'] : '',
									'meta_description'	=> (isset($meta['description'])) ? $meta['description'] : '',
									// pages that start with an underscore won't show in pagelist
									'include_in_page_list'	=> (strncmp($file, '_', 1) == 0) ? 'n' : 'y',
									'layout_id'			=> $layout_id
			));

			// Editable region - insert content for each page
			foreach($file_dom->find('div[class=mojo_page_region]') as $page_region)
			{
				$this->page_model->insert_page_region(array(
									'region_id'			=> $page_region->id,
									'region_name'		=> ucwords(str_replace('_', ' ', $page_region->id)),
									'page_url_title'	=> $url_title,
									'content'			=> $this->_add_mojo($page_region->innertext),
									'layout_id'			=> $layout_id
				));
			}
		}

		// The last task is inserting updated settings. Here's the url_title for
		// the default, which will match what was inserted into the pages table.
		$url_title = url_title(substr($default_page, 0, strpos($default_page, '.htm')), $url_separator);
		$raw_default_page = $this->page_model->get_page_by_url_title($url_title);
		$default_page = $raw_default_page->id;

		$site_structure = array();
		foreach ($this->page_model->get_all_pages() as $id=>$name)
		{
			$site_structure[$id] = $id;
		}

		// remove any empty/null items
		$site_structure = array_filter($site_structure);

		$update_settings = array('default_page' => $default_page, 'site_structure' => $site_structure, 'mojo_version' => $this->mojo_version);

		// Default site will have in_page_login enabled
		if ($default_site_install)
		{
			$update_settings['in_page_login'] = 'y';
		}

		// Set asset_url config item to the import directory
		$asset_url = $this->base_url . $import_directory;

		if ( ! $this->config->config_update(array('asset_url' => $asset_url)))
		{
			$this->update_notices[] = 'Unable to automatically update your config file. Please open system/mojomotor/config/config.php and add: $config[\'asset_url\'] = "' . $asset_url.'";';
		}
		
		if ( ! $this->site_model->update_settings($update_settings))
		{
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Add Mojo
	 *
	 * Takes "normal" HTML, and adds in Mojo tags to be passed through the Parser
	 * when the site is displaying.
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	function _add_mojo($layout_content)
	{
		// relative paths need to be updated in anchors, Mojo handles html page links
		// different then all other files.
		$pattern = '/href=(\'|\")(?!https?:)([a-z0-9\-\_\.]*?)\.html?\\1/is';
		$replacement = 'href="{mojo:site:link page="${2}"}"';
		$layout_content = preg_replace($pattern, $replacement, $layout_content);

		// relative paths need to be updated for javascript and images
		$pattern = '/src=(\'|\")(?!https?:)(.*?)\\1/is';
		$replacement = 'src="{mojo:site:asset_url}${2}"';
		$layout_content = preg_replace($pattern, $replacement, $layout_content);

		// relative paths need to be updated in everything else with "href"
		$pattern = '/href=(\'|\")(?!https?:)([a-z0-9\/\-\_\.]*?)\\1/is';
		$replacement = 'href="{mojo:site:asset_url}${2}"';
		$layout_content = preg_replace($pattern, $replacement, $layout_content);

		// meta tags
		// 	    preg_match_all('/<[\s]*meta[\s]*name="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si',  $layout_content, $out);
		// 
		// foreach ($out as $meta => $content)
		// {
		// 	echo $content[0].' is '.$content[1].'<br>';
		// }
		// 
		// echo '<pre>';print_r($out);echo '</pre>';exit;
		// 
		// 	    for ($i=0;$i < count($out[1]);$i++) {
		// 	        // loop through the meta data - add your own tags here if you need
		// 	        if (strtolower($out[1][$i]) == "keywords") $meta['keywords'] = $out[2][$i];
		// 	        if (strtolower($out[1][$i]) == "description") $meta['description'] = $out[2][$i];
		// 	    }
		// 	    

		return $layout_content;
	}

	// --------------------------------------------------------------------

	/**
	 * Implement Blank Site
	 *
	 * If the user has opted not to install a blank site, this handles
	 * data insertion.
	 *
	 * @access	private
	 * @return	mixed
	 */
	function _install_blank_site()
	{
		if ( ! $this->setup_model->install_blank_site())
		{
			show_error("There was a problem installing a blank site on your system");
		}

		$site_settings = array('default_page' => 1, 'mojo_version' => $this->mojo_version);

		if ( ! $this->site_model->update_setting($site_settings))
		{
			log_message('debug', 'Unable to set the default home page to "home" for the blank installation');
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Implement Default Site
	 *
	 * If the user has opted not to import a site, this fills it with the
	 *
	 * @access	private
	 * @return	bool
	 */
	function _install_default_site()
	{
		$this->_import_site($this->default_site_folder);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Add Site Dummy Data
	 *
	 * Inserts the default settings for the site
	 *
	 * @access	private
	 * @return	bool
	 */
	function _add_site_data()
	{
		// This function handles inserting a few things, so we'll track errors with a counter
		$errors = 0;
		if ( ! $this->setup_model->insert_initial_settings())
		{
			$errors++;
		}

		if ( ! $admin_group_id = $this->member_model->insert_member_group('admin'))
		{
			$errors++;
		}

		if ( ! $editor_group_id = $this->member_model->insert_member_group('editor'))
		{
			$errors++;
		}

		$this->admin_password = ($this->input->post('password')) ? $this->input->post('password') : random_string('alpha');
		$this->admin_email = $this->input->post('email');

		$member_data = array(
								'id' => 1,
								'email' => $this->admin_email,
								'group_id' => $admin_group_id,
								'password' => $this->admin_password,
								'autogen_password' => ($this->input->post('password')) ? 'n' : 'y'
		);

		if ( ! $this->member_model->insert_member($member_data))
		{
			$errors++;
		}

		// Upload location
		$this->load->helper('path');
		$upload_prefs = array(
								'name' => $this->default_upload_location,
								'server_path' => set_realpath($this->default_upload_location),
								'url' => base_url().$this->default_upload_location.'/',
								'allowed_types' => 'all',
		);

		if ( ! $this->db->insert('upload_prefs', $upload_prefs))
		{
			$errors++;
		}

		return ($errors == 0) ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * DB Verify
	 *
	 * A callback function used to validate db information before the installation
	 * occurs.
	 *
	 * @access	private
	 * @param	string
	 * @return	bool
	 */
	function _db_verify($given_driver)
	{
		$db_config['hostname'] = $this->input->post('db_host');
		$db_config['username'] = $this->input->post('db_user');
		$db_config['password'] = $this->input->post('db_password');
		$db_config['database'] = $this->input->post('db_name');
		$db_config['dbdriver'] = $given_driver;

		if ($this->_db_connection_test($db_config) === TRUE)
		{
			// Keep track of which driver we're using.
			$this->db_driver = $given_driver;

			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * DB Driver Test
	 *
	 * Test a given driver to ensure the server can use it. We'll also create the
	 * database here if we need to.
	 *
	 * @access	private
	 * @param	array
	 * @return	bool
	 */
	function _db_connection_test($db_config)
	{
		// If the extension is loaded, then SQLite3 is successful
		if ($db_config['dbdriver'] == 'sqlite3' && extension_loaded('pdo_sqlite'))
		{
			return TRUE;
		}

		// Unset any existing DB information
		unset($this->db);

		// Explicitly set debugging to FALSE to avoid CI throwing errors if its wrong
		$db_config['db_debug'] = FALSE;

		// load based on custom passed information
		$this->load->database($db_config);

		if (is_resource($this->db->conn_id) OR is_object($this->db->conn_id))
		{
			// So far we know the connection worked, but not if the db exists
			// If we can't find it, we'll attempt to build it on their behalf.

			$this->load->dbutil();

			// Now then, does the DB exist?
			if ($this->dbutil->database_exists($this->db->database))
			{
				// Connected and found the db. Happy days are here again!
				return TRUE;
			}
			else
			{
				$this->load->dbforge();

				if ($this->dbforge->create_database($this->db->database))
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
			}
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Update
	 *
	 * The updater to move from one version to the next
	 *
	 * @access	public
	 * @return	string
	 */
	function update()
	{
		$this->load->library('auth');

		// Only admins should be updating
		if ( ! $this->auth->is_admin())
		{
			show_404();
		}

		$this->load->database();
		$this->load->dbforge();
		$this->load->model('site_model');

		// Get the version of Mojo currently installed.
		$this->mojo_version = $this->site_model->get_setting('mojo_version');

		if ( ! $this->mojo_version)
		{
			show_error('Could not determine currently installed version of MojoMotor. Please contact support.');
		}

		// These are all the versions that Mojo has ever had. We loop through them to
		// determine which updates need running. It is important that these stay in the
		// order builds were released.
		$versions = array(
			'0.0.1', '0.0.2', '0.0.3', '0.0.4',
			'0.1.0',
			'1.0.0', '1.0.1', '1.0.2', '1.0.3', '1.0.4', '1.0.5', '1.0.6', '1.0.7',
			'1.1.0', '1.1.1', '1.1.2',
			'1.2.0', '1.2.1'
		);

		foreach($versions as $key => $version)
		{
			$function_name = '_update_'.str_replace('.', '_', $version);

			if (version_compare($this->mojo_version, $version, '==') && method_exists('Setup', $function_name))
			{
				// If at any point an update function returns FALSE, then something 
				// has gone wrong, so exit the versions loop and report back to the user.
				if ( ! call_user_func(array('Setup', $function_name)))
				{
					break;
				}
				
				// the mojo_version field was not added until 0.1.0
				if (version_compare($this->mojo_version, '0.1.0', '>'))
				{
					// Update mojo_version
					$this->db->where('id', 1); // there's only 1 row, but we still need to use this
					$this->db->update('site_settings', array('mojo_version' => $this->mojo_version));

					if ($this->db->affected_rows() < 1)
					{
						$this->update_notices[] = 'Unable to update <em>mojo_version</em> in the site_settings table.';
						break;
					}					
				}
				
				$this->update_notices[] = str_replace('%x', $this->mojo_version, $this->lang->line('update_to_version'));
			}
		}

		$vars['page_title'] = 'MojoMotor '.$this->lang->line('update');
		$vars['site_name'] = $this->site_model->get_setting('site_name');
		$vars['mojo_version'] = $this->mojo_version;
		$vars['notices'] = $this->update_notices;

		$this->load->view('setup/update', $vars);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update 1.2.0
	 *
	 * Handles updating MojoMotor from 1.2.0 to 1.2.1
	 *
	 * @access	public
	 * @return	bool
	 */
	function _update_1_2_0()
	{
		// No errors encountered
		$this->mojo_version = '1.2.1';
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update 1.1.2
	 *
	 * Handles updating MojoMotor from 1.1.2 to 1.2.0
	 *
	 * @access	public
	 * @return	bool
	 */
	function _update_1_1_2()
	{
		// add new page_404 field after default_page in site_settings
		$this->load->dbforge();
		$this->dbforge->add_column('site_settings', array('page_404' => array('type' => 'INT')), 'default_page');

		// pull old site_path setting out of the DB and into config.php as asset_url
		$asset_url = $this->site_model->get_setting('site_path');
		
		if ( ! $this->config->config_update(array('asset_url' => $asset_url)))
		{
			$this->update_notices[] = 'Unable to automatically update your config file. Please open system/mojomotor/config/config.php and add: $config[\'asset_url\'] = "' . $asset_url.'";';
		}

		// drop old site_path column
		$this->dbforge->drop_column('site_settings', 'site_path');
		
		// swap a few tags with their simpler counterparts
		$tag_swap = array(
			'{mojo:site:site_path}' 	=> '{mojo:site:asset_url}'
		);

		$this->load->model('layout_model');
		$query = $this->layout_model->get_layouts(TRUE); // We want 'em all.

		foreach($query->result_array() as $layout)
		{
			foreach($tag_swap as $from => $to)
			{
				$layout['layout_content'] = str_replace($from, $to, $layout['layout_content']);
			}

			$this->layout_model->update_layout($layout);
		}

		// Done!
		$this->mojo_version = '1.2.0';

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update 1.1.1
	 *
	 * Handles updating MojoMotor from 1.1.1 to 1.1.2
	 *
	 * @access	public
	 * @return	bool
	 */
	function _update_1_1_1()
	{
		// add a 'user_data' field to the currently unused sessions table, for more standard CI compatibility
		// we will remove the 'content' field in a future update
		$this->load->dbforge();
		$this->dbforge->add_column('sessions', array('user_data' => array('type' => 'TEXT', 'null' => TRUE)));
		$this->mojo_version = '1.1.2';
		return TRUE;		
	}

	// --------------------------------------------------------------------
	
	/**
	 * Update 1.1.0
	 *
	 * Handles updating MojoMotor from 1.1.0 to 1.1.1
	 *
	 * @access	public
	 * @return	bool
	 */
	function _update_1_1_0()
	{
		// No errors encountered, so update the version variable for
		// any further version updates.
		$this->mojo_version = '1.1.1';
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update 1.0.7
	 *
	 * Handles updating MojoMotor from 1.0.7 to 1.1.0
	 *
	 * @access	public
	 * @return	bool
	 */
	function _update_1_0_7()
	{
		// Go through mojo_page_regions and remove any records with no url_title
		$this->db->delete('page_regions', array('page_url_title' => '')); 

		$update = array(
			'pages'			=> 'url_title',
			'page_regions'	=> 'page_url_title'
		);
		
		foreach ($update as $table => $field)
		{
			$qry = $this->db->select($field.', id')->get($table);
			$res = $qry->result_array();

			foreach ($res as $k => &$row)
			{
				if ($row[$field] == 'index')
				{
					unset($res[$k]);
					continue;
				}

				$row[$field] = 'page/'.$row[$field];
			}
			
			if (count($res))
			{
				$this->db->update_batch($table, $res, 'id');
			}
		}

		// No errors encountered, so update the version variable for
		// any further version updates.
		$this->mojo_version = '1.1.0';
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update 1.0.6
	 *
	 * Handles updating MojoMotor from 1.0.6 to 1.0.7
	 *
	 * @access	public
	 * @return	bool
	 */
	function _update_1_0_6()
	{
		if ( ! $this->config->config_update(array('enable_hooks' => TRUE)))
		{
			$this->update_notices[] = 'Unable to automatically update your config file. Please open system/mojomotor/config/config.php and find $config[\'enable_hooks\'] to TRUE.';
		}

		// No errors encountered, so update the version variable for
		// any further version updates.
		$this->mojo_version = '1.0.7';
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update 1.0.5
	 *
	 * Handles updating MojoMotor from 1.0.5 to 1.0.6
	 *
	 * @access	public
	 * @return	bool
	 */
	function _update_1_0_5()
	{
		// No errors encountered, so update the version variable for
		// any further version updates.
		$this->mojo_version = '1.0.6';
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update 1.0.4
	 *
	 * Handles updating MojoMotor from 1.0.4 to 1.0.5
	 *
	 * @access	public
	 * @return	bool
	 */
	function _update_1_0_4()
	{
		// No errors encountered, so update the version variable for
		// any further version updates.
		$this->mojo_version = '1.0.5';
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update 1.0.3
	 *
	 * Handles updating MojoMotor from 1.0.3 to 1.0.4
	 *
	 * @access	public
	 * @return	bool
	 */
	function _update_1_0_3()
	{
		// Add last_modified and original_layout_id to layouts
		$layout_track = array(
								'last_modified' => array(
									'type' => 'INT',
									'constraint' => '10',
									'default' => '0',
								),
								'original_layout_id' => array(
									'type' => 'INT',
									'constraint' => '11',
									'default' => '0',
								)
		);

		// Add the last_modified field
		if ( ! $this->dbforge->add_column('layouts', $layout_track))
		{
			$this->update_notices[] = 'Unable to add <em>last_modified and original_layout_id</em> into the layouts table.';
			return FALSE;
		}

		// Add last_modified and original_page_id to pages
		$page_track = array(
								'last_modified' => array(
									'type' => 'INT',
									'constraint' => '10',
									'default' => '0',
								),
								'original_page_id' => array(
									'type' => 'INT',
									'constraint' => '11',
									'default' => '0',
								)
		);

		// Add the last_modified field
		if ( ! $this->dbforge->add_column('pages', $page_track))
		{
			$this->update_notices[] = 'Unable to add <em>last_modified and original_page_id</em> into the pages table.';
			return FALSE;
		}

		// No errors encountered, so update the version variable for
		// any further version updates.
		$this->mojo_version = '1.0.4';
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update 1.0.2
	 *
	 * Handles updating MojoMotor from 1.0.2 to 1.0.3
	 *
	 * @access	public
	 * @return	bool
	 */
	function _update_1_0_2()
	{
		// Update permitted uri. We need &= in there for the ckeditor file browse. This can be removed
		// after ckeditor has the ability to over-ride their addquerystring() function.
		if ( ! $this->config->config_update(array('permitted_uri_chars'=>'a-z 0-9~%.:_\-&=')))
		{
			$this->update_notices[] = 'Unable to automatically update your config file. Please open system/mojomotor/config/config.php and find $config[\'permitted_uri_chars\'] to \'a-z 0-9~%.:_\-&=\'.';
		}

		// No errors encountered, so update the version variable for
		// any further version updates.
		$this->mojo_version = '1.0.3';
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update 1.0.1
	 *
	 * Handles updating MojoMotor from 1.0.1 to 1.0.2
	 *
	 * @access	public
	 * @return	bool
	 */
	function _update_1_0_1()
	{
		// Increase url_title to 75 chars
		$this->db->query('ALTER TABLE `'.$this->db->dbprefix('pages').'` MODIFY `url_title` VARCHAR(75)');
		$this->db->query('ALTER TABLE `'.$this->db->dbprefix('page_regions').'` MODIFY `page_url_title` VARCHAR(75)');

		// No errors encountered, so update the version variable for
		// any further version updates.
		$this->mojo_version = '1.0.2';
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update 1.0.0
	 *
	 * Handles updating MojoMotor from 1.0.0 to 1.0.1
	 *
	 * @access	public
	 * @return	bool
	 */
	function _update_1_0_0()
	{
		// No errors encountered, so update the version variable for
		// any further version updates.
		$this->mojo_version = '1.0.1';
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update 0.1.0
	 *
	 * Handles updating MojoMotor from 0.1.0 to 1.0.0
	 *
	 * @access	public
	 * @return	bool
	 */
	function _update_0_1_0()
	{
		if ( ! $this->config->config_update(array('csrf_protection'=>TRUE)))
		{
			$this->update_notices[] = 'Unable to set CSRF protection.';
			return FALSE;
		}

		// No errors encountered, so update the version variable for
		// any further version updates.
		$this->mojo_version = '1.0.0';
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update 0.0.4
	 *
	 * Handles updating MojoMotor from 0.0.4 to 0.1.0
	 *
	 * @access	public
	 * @return	bool
	 */
	function _update_0_0_4()
	{
		// Specific error messages not lang keyed. This is to facilitate support (in english), and
		// its rare that someone would be updating with a language pack (it'd be a clean install)

		// Add a mojo_verson field to the db
		$version_field = array('mojo_version' => array(
								'type' => 'VARCHAR',
								'constraint' => '10',
								'default' => '',
		));

		// Add the mojo_version field
		if ( ! $this->dbforge->add_column('site_settings', $version_field))
		{
			$this->update_notices[] = 'Unable to add <em>mojo_version</em> into the site_settings table.';
			return FALSE;
		}

		// Insert mojo_version now
		$this->db->where('id', 1); // there's only 1 row, but we still need to use this
		$this->db->update('site_settings', array('mojo_version' => '0.1.0'));

		if ($this->db->affected_rows() != 1)
		{
			$this->update_notices[] = 'Unable to update <em>mojo_version</em> in the site_settings table.';
			return FALSE;
		}

		// No errors encountered, so update the version variable for
		// any further version updates.
		$this->mojo_version = '0.1.0';
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update 0.0.3
	 *
	 * Handles updating MojoMotor from 0.0.3 to 0.0.4
	 *
	 * @access	public
	 * @return	bool
	 */
	function _update_0_0_3()
	{
		$this->mojo_version = '0.0.4';
		return TRUE;
	}

	// --------------------------------------------------------------------

}

/* End of file setup.php */
/* Location: system/mojomotor/controllers/setup.php */