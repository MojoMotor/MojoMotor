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
 * Utilities Class
 *
 * @package		MojoMotor
 * @subpackage	Controllers
 * @category	Controllers
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Utilities extends Mojomotor_Controller {

	var $embed_map = array();
	var $ee_export_version = '2.1';

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('language');
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
		$this->load->helper('directory');

		$data = array(
			'ee_version'		=> '2',
			'version'			=> $this->site_model->get_setting('mojo_version'),
			'update_status'		=> $this->site_model->version_check(),
			'mm_download_url'	=> 'https://secure.mojomotor.com/download'
		);
		
		$this->load->view('utilities/index', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Export
	 *
	 * Exports MojoMotor data into ExpressionEngine2
	 *
	 * @access	public
	 * @return	mixed
	 */
	function export()
	{
		$this->CI =& get_instance();

		$this->load->library('zip');
		$this->load->helper('dom');

		$filename	= 'mojo_export_'.date("Y_m_d", time());

		$pages		= $this->page_model->get_pages_exporter();
		$members	= $this->member_model->get_members('*', '*', TRUE); // TRUE == get passwords also		
				
		$layouts		= $this->layout_model->get_layouts(TRUE);
		$page_regions	= $this->layout_model->get_all_page_regions();
		$global_regions	= $this->layout_model->get_global_regions_export();

		// Config information
		$asset_url = trim($this->CI->config->item('asset_url'));

		$export_data = array();

		$export_data['diagnostic'] = array(
			'mojo_version'		=> $this->site_model->get_setting('mojo_version'),
			'ee_export_version'	=> $this->ee_export_version,
			'time'				=> time(),
			'site_id'			=> 1, // admin can change this for MSM
			'asset_url'			=> $asset_url ? trim($asset_url, '/').'/' : base_url()
		);

		// Now get the custom Mojo stuff and translate

		$member_ids	= array();

		// Members
		foreach ($members->result() as $member)
		{
			$member_ids[] = $member->id;

			$export_data['members'][] = array(
				'member_id'		=> $member->id,
				'email'			=> $member->email,
				'password'		=> $member->password,
				'member_group'	=> $member->group_id
			);
		}

		// Global regions. Let's start with an emtpy array and add to it as we go.
		// Vroooom!
		$export_data['global_regions'] = array();

		foreach ($global_regions as $region_id => $region_content)
		{
			$export_data['global_regions'][] = array(
				'variable_id'	=> $region_id,
				'variable_data'	=> $region_content
			);
		}
		
		// Page regions
		foreach($page_regions as $region)
		{
			if ( ! isset($pg_regions[$region['layout_id']]))
			{
				$pg_regions[$region['layout_id']] = array();
			}
			
			if ( ! isset($pg_regions[$region['layout_id']][$region['region_name']]))
			{
				$pg_regions[$region['layout_id']][$region['region_id']] = array(
					'region_id'		=> $region['region_id'],
					'region_name'	=> $region['region_name']
				);
			}
		}
		
		$export_data['page_regions'] = $pg_regions;

		$editable_regions = array();

		// Layouts
		foreach ($layouts->result() as $layout)
		{
			/*
			// get layout content, and change mojo global regions into EE global variables
			$pattern = '/mojo:page:global_region id=\"([^"]*)\"/';
			$replacement = $global_region_prefix.$layout->id.'_${1}';
			$layout_content = preg_replace($pattern, $replacement, $layout->layout_content);

			// get layout content, and change mojo page regions into EE channel calls
			$pattern = '/mojo:page:page_region id=\"([^"]*)\"/';
			$replacement = 'exp:channel:entries channel="mojo_import"}{${1}}{/exp:channel:entries';
			$layout_content = preg_replace($pattern, $replacement, $layout_content);
			
			// Replace mojo embeds with EE embeds- layout_name/index
			//{mojo:layout:embed layout="layout_name"}
			$pattern = '/mojo:layout:embed layout=\"([^"]*)\"/';
			$replacement = 'embed="${1}/index"';
			$layout_content = preg_replace($pattern, $replacement, $layout_content);			

			
			// preg_match($pattern, $layout->layout_content, $match);
			// $export_data['editable_regions'][$layout->id][] = $match[1];

			// The following strings are used within MojoMotor layouts, but aren't useful in EE
			$mojo_vars = array(
				'{mojo:site:login}',
				'<!-- MojoMotor will replace this content dynamically with the contents of the Global Region -->',
				'<!-- MojoMotor will replace this content dynamically with the contents of the Page Region -->',
				'{mojo:site:site_path}'
			);

			// In some cases we want to replace Mojo vars with EE equivalents. This array represetns the
			// equivalents for the $not_needed array above.
			$ee_vars = array(
				'',
				'',
				'{site_url}'
			);

			$layout_content = str_replace($mojo_vars, $ee_vars, $layout_content);
			
			$export_data['layouts'][] = array(
				'layout_id' => $layout->id,
				'layout_name' => $layout->layout_name,
				'layout_type' => ($layout->layout_type == 'embed') ? 'webpage' : $layout->layout_type,
				'layout_content' => $layout_content,
			);
			*/
			$export_data['layouts'][] = array(
				'layout_id'		=> $layout->id,
				'layout_name'	=> $layout->layout_name,
				'layout_type'	=> $layout->layout_type,
				'layout_content'=> $layout->layout_content
			);
		}

		// Pages
		foreach ($pages->result() as $page)
		{
			$export_data['pages'][$page->id] = array(
				'page_id'		=> $page->id,
				'layout_id'		=> $page->layout_id,
				'title'			=> $page->page_title,
				'url_title'		=> $page->url_title,
				'meta_keywords'	=> $page->meta_keywords,
				'meta_description'		=> $page->meta_description,
				'include_in_page_list'	=> $page->include_in_page_list
			);

			$this->db->where('page_url_title', $page->url_title);
			foreach ($this->db->get('page_regions')->result() as $region)
			{
				$export_data['pages'][$page->id][$region->region_id] = $region->content;
			}
		}
		
		$export_data['site_structure'] = $this->site_model->get_setting('site_structure');

		$data = "<?php\n";
		$data .= '$import_data = ';
		$data .= var_export($export_data, TRUE);
		$data .= ';';

		$this->zip->add_data($filename.'.txt', $data);

		// Uncomment the next line to display the SQL dump onscreen
		// You need to htmlentities() for true representation of html
		// echo '<pre>';print_r($export_data);echo '</pre>'; exit;

		// Order's up! Get it while its hot!
		$this->zip->download($filename.'.zip');
	}

	// --------------------------------------------------------------------

	/**
	 * PHP Info
	 *
	 * Display's PHPInfo()
	 *
	 * @access	public
	 * @return	string
	 */
	function php_info()
	{
		$data['page_title'] = $this->lang->line('utilities_phpinfo');

		ob_start();

		phpinfo();

		$buffer = ob_get_contents();

		ob_end_clean();

		// OK, the output from PHPinfo is ugly and messy, but I'm not going
		// through it to clear everything out.  This is how ExpressionEngine 
		// cleans up PHPinfo, and I'm happy to blatently stea... 
		// erm... "resuse" this function.

		$output = (preg_match("/<body.*?".">(.*)<\/body>/is", $buffer, $match)) ? $match['1'] : $buffer;
		$output = preg_replace("/width\=\".*?\"/", "width=\"100%\"", $output);
		$output = preg_replace("/<hr.*?>/", "<br />", $output); // <?
		$output = preg_replace("/<a href=\"http:\/\/www.php.net\/\">.*?<\/a>/", "", $output);
		$output = preg_replace("/<a href=\"http:\/\/www.zend.com\/\">.*?<\/a>/", "", $output);
		$output = preg_replace("/<a.*?<\/a>/", "", $output);// <?
		$output = preg_replace("/<th(.*?)>/", "<th \\1 align=\"left\" class=\"tableHeading\">", $output);
		$output = preg_replace("/<tr(.*?).*?".">/", "<tr \\1>\n", $output);
		$output = preg_replace("/<td.*?".">/", "<td valign=\"top\" class=\"tableCellOne\">", $output);
		$output = preg_replace("/cellpadding=\".*?\"/", "cellpadding=\"2\"", $output);
		$output = preg_replace("/cellspacing=\".*?\"/", "", $output);
		$output = preg_replace("/<h2 align=\"center\">PHP License<\/h2>.*?<\/table>/si", "", $output);
		$output = preg_replace("/ align=\"center\"/", "", $output);
		$output = preg_replace("/<table(.*?)bgcolor=\".*?\">/", "\n\n<table\\1>", $output);
		$output = preg_replace("/<table(.*?)>/", "\n\n<table\\1 class=\"tableBorderNoBot\" cellspacing=\"0\">", $output);
		$output = preg_replace("/<h2>PHP License.*?<\/table>/is", "", $output);
		$output = preg_replace("/<br \/>\n*<br \/>/is", "", $output);
		$output = str_replace("<h1></h1>", "", $output);
		$output = str_replace("<h2></h2>", "", $output);

		$data['output'] = $output;

		$this->load->view('utilities/php_info', $data);
	}
}

/* End of file utilities.php */
/* Location: system/mojomotor/controllers/utilities.php */