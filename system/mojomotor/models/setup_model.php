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
 * Setup Model
 *
 * @package		MojoMotor
 * @subpackage	Models
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Setup_model extends CI_Model {

	/**
	 * Insert Initial Settings
	 *
	 * Inserts initial settings for a site. This belongs here and not in site_model, since
	 * it is only called during install. Subsequent calls will use update_settings()
	 *
	 * @param	string
	 * @return	mixed
	 */
	public function insert_initial_settings($default_page = NULL)
	{
		$this->db->set('site_name', $this->input->post('site_title'));
		$this->db->set('default_page', $default_page);

		return $this->db->insert('site_settings');
	}

	// --------------------------------------------------------------------

	/**
	 * Install Blank Site
	 *
	 * Inserts data for an essentially blank installation
	 *
	 * @return	bool
	 */
	public function install_blank_site()
	{
		$errors = 0;

		// Blank Layout
		$layout_info = array(
							'layout_name'		=> 'simple_layout',
							'layout_type' 		=> 'webpage',
							'layout_content'	=> $this->load->view('setup/blank_layout', '', TRUE)
		);

		$layout_id = $this->layout_model->insert_layout($layout_info);

		if ($layout_id === FALSE)
		{
			$errors++;
		}

		$page_data = array(
							'page_title'		=> 'Simple Demo Title',
							'url_title'			=> 'home',
							'meta_keywords'		=> 'simple, demo, site',
							'meta_description'	=> 'The simple demo is really just a simple demonstration of MojoMotor in action.',
							'layout_id'			=> $layout_id
		);

		$page_id = $this->page_model->insert_page($page_data);

		if ($page_id === FALSE)
		{
			$errors++;
		}

		$region_data = array(
							'region_id'			=> 'editable_content',
							'region_name'		=> 'editable_content',
							'page_url_title'	=> 'home',
							'content'			=> '<p>After you are logged in, click me to edit! <br /><br /></p>',
							'layout_id'			=> $layout_id
		);

		if ( ! $this->page_model->insert_page_region($region_data))
		{
			$errors++;
		}

		if ( ! $this->site_model->update_settings(array('site_structure'=>array(1=>1))))
		{
			$errors++;
		}

		return ($errors == 0) ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Field Definitions
	 *
	 * Holds the db schema for MojoMotor.
	 *
	 * @return	array
	 */
	public function field_definitions()
	{
		$db_schema['pages'] = array(
						'id' => array(
												'type' => 'INT',
												'auto_increment' => TRUE,
						),
						'page_title' => array(
												'type' => 'VARCHAR',
												'constraint' => '100',
												'default' => '',
						),
						'url_title' => array(
												'type' => 'VARCHAR',
												'constraint' => '75',
												'default' => '',
						),
						'meta_keywords' => array(
												'type' =>'VARCHAR',
												'constraint' => '255',
												'default' => '',
						),
						'include_in_page_list' => array(
												'type' =>'VARCHAR',
												'constraint' => '1',
												'default' => 'y',
						),
						'meta_description' => array(
												'type' =>'VARCHAR',
												'constraint' => '255',
												'default' => '',
						),
						'layout_id' => array(
												'type' => 'INT',
												'constraint' => 3,
						),
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

		$db_schema['layouts'] = array(
						'id' => array(
												'type' => 'INT',
												'auto_increment' => TRUE,
						),
						'layout_name' => array(
												'type' => 'VARCHAR',
												'constraint' => '100',
												'default' => '',
						),
						'layout_type' => array(
												'type' => 'VARCHAR',
												'constraint' => '10',
												'default' => 'webpage',
						),
						'layout_content' => array(
												'type' => 'TEXT',
												'null' => TRUE,
						),
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

		$db_schema['page_regions'] = array(
						'id' => array(
												'type' => 'INT',
												'auto_increment' => TRUE,
						),
						'region_id' => array(
												'type' => 'VARCHAR',
												'constraint' => '100',
												'default' => '',
						),
						'region_name' => array(
												'type' => 'VARCHAR',
												'constraint' => '100',
												'default' => '',
						),
						'page_url_title' => array(
												'type' => 'VARCHAR',
												'constraint' => 75,
						),
						'content' => array(
												'type' =>'TEXT',
												'null' => TRUE,
						),
						'layout_id' => array(
												'type' => 'INT',
												'constraint' => 5,
						),
		);

		$db_schema['global_regions'] = array(
						'id' => array(
												'type' => 'INT',
												'auto_increment' => TRUE,
						),
						'region_id' => array(
												'type' => 'VARCHAR',
												'constraint' => '100',
												'default' => '',
						),
						'region_name' => array(
												'type' => 'VARCHAR',
												'constraint' => '100',
												'default' => '',
						),
						'layout_id' => array(
												'type' => 'INT',
												'constraint' => 5,
						),
						'content' => array(
												'type' =>'TEXT',
												'null' => TRUE,
						),
		);

		$db_schema['site_settings'] = array(
						'id' => array(
												'type' => 'INT',
												'auto_increment' => TRUE,
						),
						'site_name' => array(
												'type' => 'VARCHAR',
												'constraint' => '100',
												'default' => '',
						),
						'default_page' => array(
												'type' => 'INT',
						),
						'page_404' => array(
												'type' => 'INT',
						),
						'in_page_login' => array(
												'type' =>'VARCHAR',
												'constraint' => '1',
												'default' => 'y',
						),
						'theme' => array(
												'type' =>'VARCHAR',
												'constraint' => '50',
												'default' => 'default',
						),
						'site_structure' => array(
												'type' =>'TEXT',
												'null' => TRUE,
						),
						'mojo_version' => array(
												'type' => 'VARCHAR',
												'constraint' => '10',
						)
		);

		$db_schema['members'] = array(
						'id' => array(
												'type' => 'INT',
												'auto_increment' => TRUE,
						),
						'email' => array(
												'type' => 'VARCHAR',
												'constraint' => '60',
												'default' => '',
						),
						'group_id' => array(
												'type' => 'INT',
												'constraint' => 3,
						),
						'password' => array(
												'type' => 'VARCHAR',
												'constraint' => '50',
												'default' => '',
						),
						'autogen_password' => array(
												'type' =>'VARCHAR',
												'constraint' => '1',
												'default' => 'y',
						),
						'auth_code' => array(
												'type' => 'VARCHAR',
												'constraint' => '32',
												'default' => '',
						),
						'edit_mode' => array(
												'type' =>'VARCHAR',
												'constraint' => '7',
												'default' => 'wysiwyg',
						),
						'remember_me' => array(
												'type' => 'TEXT',
						),
		);

		$db_schema['member_groups'] = array(
						'id' => array(
												'type' => 'INT',
												'auto_increment' => TRUE,
						),
						'group_title' => array(
												'type' => 'VARCHAR',
												'constraint' => '60',
												'default' => '',
						),
		);

		$db_schema['upload_prefs'] = array(
						'id' => array(
												'type' => 'INT',
												'auto_increment' => TRUE,
						),
						'name' => array(
												'type' => 'VARCHAR',
												'constraint' => '50',
												'default' => '',
						),
						'server_path' => array(
												'type' => 'VARCHAR',
												'constraint' => '150',
												'default' => '',
						),
						'url' => array(
												'type' => 'VARCHAR',
												'constraint' => '150',
												'default' => '',
						),
						'allowed_types' => array(
												'type' => 'VARCHAR',
												'constraint' => '3',
												'default' => '',
						),
						'max_size' => array(
												'type' => 'VARCHAR',
												'constraint' => '16',
												'default' => '',
						),
						'max_height' => array(
												'type' => 'VARCHAR',
												'constraint' => '6',
												'default' => '',
						),
						'max_width' => array(
												'type' => 'VARCHAR',
												'constraint' => '6',
												'default' => '',
						),
						'properties' => array(
												'type' => 'VARCHAR',
												'constraint' => '120',
												'default' => '',
						),
						'pre_format' => array(
												'type' => 'VARCHAR',
												'constraint' => '120',
												'default' => '',
						),
						'post_format' => array(
												'type' => 'VARCHAR',
												'constraint' => '120',
												'default' => '',
						),
						'file_properties' => array(
												'type' => 'VARCHAR',
												'constraint' => '120',
												'default' => '',
						),
						'file_pre_format' => array(
												'type' => 'VARCHAR',
												'constraint' => '120',
												'default' => '',
						),
						'file_post_format' => array(
												'type' => 'VARCHAR',
												'constraint' => '120',
												'default' => '',
						),
		);

		// Add in a table for sessions, even though it isn't
		 // getting used, for future options of "flipping it on"

		$db_schema['sessions'] = array(
						'session_id' => array(
												'type' => 'VARCHAR',
												'constraint' => '40',
												'default' => '0',
						),
						'ip_address' => array(
												'type' => 'VARCHAR',
												'constraint' => '16',
												'default' => '0',
						),
						'user_agent' => array(
												'type' => 'VARCHAR',
												'constraint' => '50',
												'default' => '',
						),
						'last_activity' => array(
												'type' => 'INT',
												'constraint' => '10',
						),
						'user_data'	=> array(
												'type' => 'TEXT',
												'null' => TRUE
						)
		);

		return $db_schema;
	}
}
/* End of file setup_model.php */
/* Location: system/mojomotor/models/setup_model.php */