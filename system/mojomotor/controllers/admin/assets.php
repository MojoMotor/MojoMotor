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
 * Assets Class
 *
 * @package		MojoMotor
 * @subpackage	Controllers
 * @category	Controllers
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Assets extends CI_Controller {

	var $theme = 'default';

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();

		// If session is available and theme exists, load the theme from there, but if it isn't fall back to default
		$this->theme = ($this->session->userdata('theme') && is_dir(APPPATH.'views/themes/'.$this->session->userdata('theme'))) ? $this->session->userdata('theme') : 'default';

		$this->output->enable_profiler(FALSE);
	}

	// --------------------------------------------------------------------

	/**
	 * Index
	 *
	 * Nothing happening by default
	 *
	 * @access	public
	 * @return	void
	 */
	function index()
	{
		// Cloudy skies they all just clear up
		// got my high fructose corn syrup
		// You can buy happiness I am sure
		// and it costs one eighty five.
		// Pour it in my red American Idol cup!
	}

	// --------------------------------------------------------------------

	/**
	 * CSS Load
	 *
	 * Used to manage stylesheets within mojomotor
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	void
	 */
	function css($css_file = 'mojomotor')
	{
		$this->load->library('parser');

		$this->output->set_header('Content-type: text/css');

		$parse_vars = array(
			'cp_img_path'	=> site_url('assets/img').'/',
		);

		$css_file = $this->security->sanitize_filename($css_file);

		$path = ($css_file == 'jqueryui') ? 'themes/'.$this->theme.'/css/jquery-ui-1.8rc3.custom.css' : 'themes/'.$this->theme.'/css/'.$css_file.'_css';

		$this->parser->parse($path, $parse_vars);
	}

	// --------------------------------------------------------------------

	/**
	 * Image Load
	 *
	 * Returns an image. This allows images for the CP to be saved in application/views,
	 * and not confuse the authors directory structure with MojoMotor files.
	 * Since the image usage is relatively light, overhead won't be an issue.
	 *
	 * If the requested file can't be found, or isn't an image, then a stock "image not
	 * found" image is sent.
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	function img($img = '')
	{
		if ($img === '')
		{
			header('Content-type: image/jpeg');
			$img_file = APPPATH.'views/themes/'.$this->theme.'/images/image_not_found.jpg';
			exit(file_get_contents($img_file));
		}

		// This is a workaround for servers that require the use of 'index.php?'.
		// In this case the dot (.) in $img is converted to an underscore.  So we
		// need to turn the last underscore into a dot.
		// @todo we may be able to remove this workaround after some planned changes
		// to CI's Router and URI classes when GET requests are used
		if (strpos($img, '.') === FALSE)
		{
			if (($last_underscore = strrpos($img, '_')) !== FALSE)
			{
				$img = substr($img, 0, $last_underscore).'.'.substr($img, $last_underscore + 1);
			}
		}

		$this->load->helper(array('file'));

		$mime = get_mime_by_extension($img);
		header('Content-type: '.$mime);
		$img_file = APPPATH.'views/themes/'.$this->theme.'/images/'.$this->security->sanitize_filename($img);

		// Is the file there, and are they requesting an image type?
		if ( ! file_exists($img_file) OR strpos($mime, 'image') === FALSE)
		{
			header('Content-type: image/jpeg');
			$img_file = APPPATH.'views/themes/'.$this->theme.'/images/image_not_found.jpg';
		}

		exit(file_get_contents($img_file));
	}
}

/* End of file assets.php */
/* Location: system/mojomotor/controllers/assets.php */