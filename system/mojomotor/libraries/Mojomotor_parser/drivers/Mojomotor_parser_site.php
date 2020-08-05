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
 * Site Parser
 *
 * Handles all MojoMotor tags that begin {mojo:site:...}
 *
 * @package		MojoMotor
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Mojomotor_parser_site extends CI_Driver {

	private $CI;
	
	private $_default_page;
	private $_page_count;

	/**
	 * Constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('site_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Site Name
	 *
	 * Returns the site name
	 *
	 * @return	string
	 */
	public function site_name()
	{
		return $this->CI->site_model->get_setting('site_name');
	}

	// --------------------------------------------------------------------

	/**
	 * Site URL
	 *
	 * Returns the base_url config item
	 *
	 * @return	string
	 */
	public function site_url()
	{
		return base_url();
	}

	// --------------------------------------------------------------------

	/**
	 * Asset URL
	 *
	 * Returns the site's base asset URL
	 *
	 * @return	string
	 */
	public function asset_url()
	{
		$asset_url = trim($this->CI->config->item('asset_url'));

		if ( ! $asset_url)
		{
			// Fall back to site_path; They need to run the updater.
			$asset_url = $this->CI->site_model->get_setting('site_path');
		}
		
		return trim($asset_url, '/').'/';
	}

	// --------------------------------------------------------------------

	/**
	 * Link
	 *
	 * Creates a link within the MojoMotor URL structure
	 *
	 * @param	array
	 * @return	string
	 */
	public function link($tag)
	{
		$page = '';

		if (isset($tag['parameters']['page']))
		{
			$page = $tag['parameters']['page'];
		}

		return trim(site_url($page), '/').'/';
	}

	// --------------------------------------------------------------------

	/**
	 * Login
	 *
	 * Generates an in-page login form to be revealed dynamically
	 *
	 * @param	array
	 * @return	string
	 */
	public function login($tag)
	{
		// They only get the form if (i) in_page_login pref is set, (i++) they
		// aren't already logged in
		if ($this->CI->site_model->get_setting('in_page_login') != 'y' OR $this->CI->session->userdata('group_id'))
		{
			return '';
		}
		
		// If cookies are required and not set?  They don't get the login
		if (config_item('require_cookie_consent') == 'y')
		{
			$this->CI->load->helper('cookie');
			
			if (get_cookie('cookies_allowed') != 'y')
			{
				$ret = $this->CI->uri->uri_string();
				$ret = strtr(base64_encode($ret), '/=', '_-');
				
				$link = site_url('addons/cookie_consent/allow_cookies/'.$ret);

				return sprintf($this->CI->lang->line('cookies_required_for_login'), $link);
			}
		}		

		$login_text = isset($tag['parameters']['text']) ? $tag['parameters']['text'] : $this->CI->lang->line('login');

		return anchor('login', $login_text, 'class="mojo_activate_login"');
	}

	// --------------------------------------------------------------------

	/**
	 * Page list
	 *
	 * Creates an unordered list of all pages in the site that haven't been
	 * opted out of appearing in the page_list.
	 *
	 * @param	array
	 * @return	string
	 */
	public function page_list($tag)
	{
		$this->CI->load->helper('array');
		$this->CI->load->model('page_model');

		if ( ! $page_map = $this->CI->page_model->get_page_map('include_in_page_list'))
		{
			return;
		}

		$defaults = $this->CI->page_model->get_page($this->CI->site_model->get_setting('default_page'));
		$this->_default_page = ($defaults) ? $defaults->url_title : '';

		$attributes = array();

		// Gather up parameters
		foreach (array('class', 'id') as $param)
		{
			if (isset($tag['parameters'][$param]))
			{
				$attributes[$param] = trim($tag['parameters'][$param]);
			}
		}

		// If there is a secific page requested, then we'll start there
		// otherwise, start from the homepage.
		if (isset($tag['parameters']['page']))
		{
			$page = $tag['parameters']['page'];

			if ($page == 'CURRENT_PAGE')
			{
				$current_page = trim($this->CI->uri->uri_string, '/');
				$page = empty($current_page) ? $this->_default_page : $current_page;
			}

			if ($start_page = $this->CI->page_model->get_page_by_url_title($page))
			{
				$page_map = array_find_element_by_key($start_page->id, $page_map);

				// No children? Alrighty then.
				if (empty($page_map['children']))
				{
					return '';
				}

				$page_map = $page_map['children'];
			}
		}

		// Depth parameter specified?
		$max_depth = 0;

		if (isset($tag['parameters']['depth']) && ctype_digit($tag['parameters']['depth']))
		{
			$max_depth = $tag['parameters']['depth'];
		}

		$this->_page_count = 0;

		$list = $this->_build_page_list($page_map, $attributes, $max_depth);

		// if no pages were actually output, return an empty string
		$list = ($this->_page_count) ? $list : '';
		
		return $list;
	}

	// --------------------------------------------------------------------

	/**
	 * Build Page List
	 *
	 * Recursively constructs the output for the parser tag
	 * {mojo:site:page_list}
	 *
	 * @access	private
	 * @param	array
	 * @param	array
	 * @param	int
	 */
	private function _build_page_list($page_map, $attributes = array(), $max_depth, $cur_depth = 1)
	{
		// Are we done?
		if (($max_depth != 0 AND $cur_depth > $max_depth) OR ! is_array($page_map))
		{
			return;
		}
		
		// Set the indentation based on the depth
		$out = str_repeat(" ", $cur_depth * 2);
	
		$atts = '';
	
		// Were any attributes submitted?  If so generate a string
		if (is_array($attributes))
		{
			foreach ($attributes as $att => $val)
			{
				$atts .= ' '.$att.'="'.$val.'"';
	
				// We only want id applied to the top level list, so we'll unset it here so children
				// don't inherit it. I wish I could have done with the big nose on my mothers side...
				if ($att == 'id')
				{
					unset($attributes[$att]);
				}
			}
		}
	
		// Write the opening list tag
		$out .= "<ul".$atts.">\n";
	
		$current_uri = trim($this->CI->uri->uri_string, '/');

		// Cycle through the list elements.  If an array is
		// encountered we will recursively call build_page_list()
		foreach ($page_map as $id => $page)
		{
			if ($page['include_in_page_list'] == 'n')
			{
				continue;
			}

			$this->_page_count++;

			// As of 1.1.0 we allow any URI, so convert any forward slashes to
			// underscores to ensure valid (X)HTML id's are generated
			$url_title = str_replace('/', '_', $page['url_title']);

			// Active page class?
			if (($current_uri == '' && $page['url_title'] == $this->_default_page) OR
				$page['url_title'] == $current_uri)
			{
				$active_class = ' class="mojo_active"';
			}
			else
			{
				$active_class = '';
			}
	
			$out .= str_repeat(" ", $cur_depth * 2);
			$out .= '<li id="mojo_page_list_'.$url_title.'"'.$active_class.'>';
	
			if ($page['url_title'] == $this->_default_page)
			{
				$out .= anchor('', $page['page_title']);
			}
			else
			{
				$out .= anchor($page['url_title'], $page['page_title']);
			}
	
			if (isset($page['children']))
			{
				$out .= "\n".$this->_build_page_list($page['children'], $attributes, $max_depth, $cur_depth + 1);
				$out .= str_repeat(" ", $cur_depth * 2);
			}
	
			$out .= "</li>\n";
		}
	
		// Closing tag
		$out .= str_repeat(" ", $cur_depth * 2) . "</ul>\n";
	
		return $out;
	}

	// --------------------------------------------------------------------

	/**
	 * Site Path
	 *
	 * Alias of asset_url() as of 1.2.0.
	 *
	 * @return	string
	 */
	public function site_path()
	{
		return $this->asset_url();
	}

}

/* End of file Mojomotor_parser_site.php */
/* Location: system/mojomotor/libraries/Mojomotor_parser/Mojomotor_parser_site.php */