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
 * Page Parser
 *
 * Handles all MojoMotor tags that begin {mojo:page:...}
 *
 * @package		MojoMotor
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Mojomotor_parser_page extends CI_Driver {

	private $CI;
	protected $page_info;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model(array('page_model', 'layout_model'));

		$this->page_info = $this->CI->page_model->get_page_by_url_title($this->CI->mojomotor_parser->url_title);
	}

	// --------------------------------------------------------------------

	/**
	 * Page Title
	 *
	 * Returns the page title
	 *
	 * @return	string
	 */
	public function page_title()
	{
		return $this->page_info->page_title;
	}

	// --------------------------------------------------------------------

	/**
	 * URL Title
	 *
	 * Returns the page url_title
	 *
	 * @return	string
	 */
	public function url_title()
	{
		return $this->CI->mojomotor_parser->url_title;
	}

	// --------------------------------------------------------------------

	/**
	 * Keywords
	 *
	 * Returns the page keywords
	 *
	 * @param	array
	 * @return	string
	 */
	public function keywords()
	{
		return $this->page_info->meta_keywords;
	}

	// --------------------------------------------------------------------

	/**
	 * Description
	 *
	 * Returns the page description
	 *
	 * @param	array
	 * @return	string
	 */
	public function description()
	{
		return $this->page_info->meta_description;
	}

	// --------------------------------------------------------------------

	/**
	 * Page Region
	 *
	 * Returns the content for a given page region
	 *
	 * @param	array
	 * @return	string
	 */
	public function page_region($tag)
	{
		if ($return = $this->CI->page_model->get_page_region($this->CI->mojomotor_parser->url_title, $tag['parameters']['id']))
		{
			return $return->content;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Global Region
	 *
	 * Returns the content for a given global region
	 *
	 * @param	array
	 * @return	string
	 */
	public function global_region($tag)
	{
		$layout_id = (isset($tag['parameters']['emb_layout_id'])) ? $tag['parameters']['emb_layout_id'] : $this->page_info->layout_id;
				
		if ($return = $this->CI->layout_model->get_global_region($layout_id, $tag['parameters']['id']))
		{
			return $return->content;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get last modified time of the page as a timestamp
	 *
	 * @return 	string
	 */
	public function last_modified()
	{
		return $this->page_info->last_modified;
	}
}

/* End of file Mojomotor_parser_page.php */
/* Location: system/mojomotor/libraries/Mojomotor_parser/Mojomotor_parser_page.php */