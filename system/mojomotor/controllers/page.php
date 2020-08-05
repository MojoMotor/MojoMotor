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
 * Page Class
 *
 * @package		MojoMotor
 * @subpackage	Controllers
 * @category	Controllers
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Page extends Mojomotor_Controller {

	var $home_page;

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();

		$this->load->driver('mojomotor_parser');

		$this->home_page = $this->site_model->default_page();

		// Caching. Since editors and admins will be seeing dynamic pages,
		// only this controller uses cache.
		if ( ! $this->auth->is_editor() && $this->config->item('time_to_cache') != '')
		{
			$this->output->cache($this->config->item('time_to_cache'));
		}
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
		if ( ! $this->home_page)
		{
			show_404('Default Page');
		}

		$this->content($this->home_page);
	}

	// --------------------------------------------------------------------

	/**
	 * Content
	 *
	 * Grabs the page and displays the contents
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function content()
	{
		$page = func_get_args();
		$page = implode('/', $page);

		if ( ! $this->page_model->page_exists($page))
		{
			$page_404_id = $this->site_model->get_setting('page_404');

			if ( ! $page_404_id)
			{
				// Show default CI error page
				show_404($page);		
			}
			
			if ($page_404_row = $this->page_model->get_page($page_404_id))
			{
				// Show our assigned 404 page, but with a 404 header status
				$page = $page_404_row->url_title;
				$this->output->set_status_header('404');
			}
		}

		$page_content = '';

		// page data
		$page_info = $this->page_model->get_page_content($page);

		if ( ! $page_info)
		{
			show_404($page);
		}

		// Get page content
		$page_content = $page_info->layout_content;
		
		$this->mojomotor_parser->url_title = $page;
		$this->mojomotor_parser->layout_name = $page_info->layout_name;

		// Parse the template
		$page_content =& $this->mojomotor_parser->parse_template($page_content);

		if ($this->session->userdata('group_id') || $this->site_model->get_setting('in_page_login') == 'y')
		{
			$page_content .= '<script charset="utf-8" type="text/javascript" src="'.site_url('javascript/mojo/'.$page).'"></script>'."\n";
			$page_content .= $this->load->view('login/in_page', '', TRUE);
		}

		$this->output->append_output($page_content);
	}

	// --------------------------------------------------------------------

	/**
	 * _output
	 *
	 * Controls page display, allowing Mojobars to be added at the last minute
	 *
	 * @access	private
	 * @param	string
	 * @return	void
	 */
	function _output($output)
	{
		if (preg_match("|</body>.*?</html>|is", $output))
		{
			$output  = preg_replace("|</body>.*?</html>|is", '', $output);
			$output .= $this->cp->output();
			$output .= implode("\n", $this->cp->appended_output);
			$output .= "\n".'</body></html>';
		}
		else
		{
			$output .= implode("\n", $this->cp->appended_output);
			$output .= $this->cp->output();
		}

		// Do we need to write a cache file?
		if ($this->output->cache_expiration > 0)
		{
			$this->output->_write_cache($output);
		}

		echo $output;
	}
}

/* End of file page.php */
/* Location: system/mojomotor/controllers/page.php */