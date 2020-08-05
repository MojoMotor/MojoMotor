<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Robot example Addon
 *
 * @package		MojoMotor
 * @subpackage	Addons
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Robots
{
	var $addon;
	var $addon_version = '1.0';

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		$this->addon =& get_instance();
	}

	// --------------------------------------------------------------------

	/**
	 * Initiate
	 *
	 * Simply returns a string
	 *
	 * @access	public
	 * @return	string
	 */
	function initiate()
	{
		return '<strong>Robot uprising initiated</strong>';
	}

	// --------------------------------------------------------------------

	/**
	 * Describe
	 *
	 * Used to export all data fed into the tag. This is just an example
	 * of what type of data a tag receives.
	 *
	 * @access	public
	 * @param	array
	 * @return	string
	 */
	function describe($template_data = array())
	{
		// Just a quick safety check. The parser class should basically mean this test
		// never fails, but defensive coding is always a good idea.
		if ( ! isset($template_data['parameters']))
		{
			return;
		}

		return '<pre>'.var_export($template_data['parameters'], TRUE).'</pre>';
	}

	// --------------------------------------------------------------------

	/**
	 * Speak
	 *
	 * An example of tag parsing. In this case we get the tag contents, and
	 * use a language file to generate some output for our robot. Let's name
	 * him Gilgamesh.
	 *
	 * @access	public
	 * @param	array
	 * @return	string
	 */
	function speak($template_data = array())
	{
		if ( ! isset($template_data['tag_contents']))
		{
			$speech = 'You probably want to use me in a template. Bleep Blurp.';
		}
		else
		{
			// Let's append a language variable before the tag contents
			$speech = $this->addon->lang->line('programmed_to_say').trim($template_data['tag_contents']);
		}

		$robot = '
			                    "'.$speech.'"
			          _____     /
			         /_____\\
			    ____[\\\'---\'/]____
			   /\\ #\\ \\_____/ /# /\\
			  /  \\# \\_.---._/ #/  \\
			 /   /|\\  |   |  /|\\   \\
			/___/ | | |   | | | \\___\\
			|  |  | | |---| | |  |  |
			|__|  \\_| |_#_| |_/  |__|
			//\\\\  <\\ _//^\\\\_ />  //\\\\
			\\||/  |\\//// \\\\\\\\/|  \\||/
			      |   |   |   |
			      |---|   |---|
			      |---|   |---|
			      |   |   |   |
			      |___|   |___|
			      /   \\   /   \\
			     |_____| |_____|
			     |HHHHH| |HHHHH|
			
		(http://www.asciiworld.com/-Robots,24-.html#id2958)
		';
		return "<pre>$robot</pre>";
	}

}

/* End of file robots.php */
/* Location: system/mojomotor/third_party/robots/libraries/robots.php */