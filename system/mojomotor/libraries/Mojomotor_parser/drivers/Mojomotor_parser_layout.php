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
 * Layout Parser
 *
 * Handles all MojoMotor tags that begin {mojo:layout:...}
 *
 * @package		MojoMotor
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Mojomotor_parser_layout extends CI_Driver {

	private $CI;

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model(array('layout_model'));
	}

	// --------------------------------------------------------------------

	/**
	 * Stylesheet
	 *
	 * Returns a link for stylesheet content
	 *
	 * @param	array
	 * @return	string
	 */
	public function stylesheet($tag)
	{
		if (isset($tag['parameters']['stylesheet']))
		{
			$return = $this->CI->layout_model->get_layout_by_name($tag['parameters']['stylesheet']);

			return ($return) ? $return->row('layout_content') : '';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Javascript
	 *
	 * Returns a link for javascript content
	 *
	 * @param	array
	 * @return	string
	 */
	public function javascript($tag)
	{
		if (isset($tag['parameters']['script']))
		{
			$return = $this->CI->layout_model->get_layout_by_name($tag['parameters']['script']);

			return ($return) ? $return->row('layout_content') : '';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Append Content
	 *
	 * Allows for content to be appended to the very bottom of the document,
	 * immediately before </body>, but after MojoMotor has finished its output.
	 * Useful for adding your own javascript to the document, or an analytics
	 * package.
	 *
	 * @param	array
	 * @return	void
	 */
	public function append_content($tag)
	{
		// I’m a pacifist but when it comes to my code I’m a masochist
		// Another day, another function, another ass to whip
		// Rocking output functions cause they asked for it, I got cash to get

		// CP isn't ideal, but it is where the rest of the Mojo content is held
		// and logically it follows as a good place to track appended content.
		$this->CI->cp->appended_output[] = trim($tag['tag_contents']);
		return;
	}
}

/* End of file Mojomotor_parser_layout.php */
/* Location: system/mojomotor/libraries/Mojomotor_parser/Mojomotor_parser_layout.php */