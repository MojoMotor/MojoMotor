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
 * Parser Class
 *
 * @package		MojoMotor
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Mojomotor_parser extends CI_Driver_Library {

	var $_template = FALSE;

	var $template	= '';
	var $tag_data	= array();
	var $l_delim	= '{';
	var $r_delim	= '}';
	var $trigger	= 'mojo:';
	var $max_depth	= 3; // How many tags can be nested within a single tag. Prevents runaway loop.
	var $url_title	= '';
	var $loop_count = 0;
	var $template_embed = array(); // Used in embed loop check
	var $layout_name = 'default';

	// A little GnR inspired string to insert into identified chunks
	var $marker		= 't4k3m3d0wn2p4r4d153c1ty'; // "oh won't you please take me hoooooome!"

	protected $valid_drivers 	= array(
		// Main mojo tags
		'mojomotor_parser_page',
		'mojomotor_parser_site',
		'mojomotor_parser_layout',
		'mojomotor_parser_setting',
		// First party addons
		'mojomotor_parser_contact',
		'mojomotor_parser_cookie_consent'
	);

	/**
	 *  Parse a template
	 *
	 * Parses pseudo-variables contained in the specified template,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse($template = FALSE, $data = array(), $return = FALSE)
	{
		$CI =& get_instance();

		if ($template === FALSE AND $this->_template === FALSE)
		{
			$this->_template = $CI->load->view($template, $data, TRUE);
		}

		if ($this->_template == '')
		{
			return FALSE;
		}

		foreach ($data as $key => $val)
		{
			if (is_array($val))
			{
				$this->_template = $this->_parse_pair($key, $val, $this->_template);
			}
			else
			{
				$this->_template = $this->_parse_single($key, (string)$val, $this->_template);
			}
		}

		if ($return === FALSE)
		{
			$CI->output->append_output($this->_template);
		}

		return $this->_template;
	}

	// --------------------------------------------------------------------

	/**
	 *  Set the template manually
	 *
	 * @param	string
	 * @return	void
	 */
	public function set_template($template)
	{
		$this->_template = $template;
	}

	// --------------------------------------------------------------------

	/**
	 *  Set the left/right variable delimiters
	 *
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	public function set_delimiters($l = '{', $r = '}')
	{
		$this->l_delim = $l;
		$this->r_delim = $r;
	}

	// --------------------------------------------------------------------

	/**
	 *  Parse a single key/value
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	private function _parse_single($key, $val, $string)
	{
		return str_replace($this->l_delim.$key.$this->r_delim, $val, $string);
	}

	// --------------------------------------------------------------------

	/**
	 *  Parse a tag pair
	 *
	 * Parses tag pairs:  {some_tag} string... {/some_tag}
	 *
	 * @param	string
	 * @param	array
	 * @param	string
	 * @return	string
	 */
	private function _parse_pair($variable, $data, $string)
	{
		if (FALSE === ($match = $this->_match_pair($string, $variable)))
		{
			return $string;
		}

		$str = '';

		foreach ($data as $row)
		{
			$temp = $match['1'];
			foreach ($row as $key => $val)
			{
				if ( ! is_array($val))
				{
					$temp = $this->_parse_single($key, $val, $temp);
				}
				else
				{
					$temp = $this->_parse_pair($key, $val, $temp);
				}
			}

			$str .= $temp;
		}

		return str_replace($match['0'], $str, $string);
	}

	// --------------------------------------------------------------------

	/**
	 *  Matches a variable pair
	 *
	 * @param	string
	 * @param	string
	 * @return	mixed
	 */
	private function _match_pair($string, $variable)
	{
		if ( ! preg_match("|".$this->l_delim . $variable . $this->r_delim."(.+?)".$this->l_delim . '/' . $variable . $this->r_delim."|s", $string, $match))
		{
			return FALSE;
		}

		return $match;
	}

	// --------------------------------------------------------------------

	/**
	 * Template Parser
	 *
	 * This function parses the template and loads the corresponding class/function
	 * based on the tags in the template
	 *
	 * @return	void
	 */
	public function parse_template($template)
	{
		if ($template == '')
		{
			return '';
		}

		// Anything that's commented gets pulled out now.
		$template = $this->_remove_mm_comments($template);
		
		$this->template_embed[$this->layout_name] = 0;
		$embed_format = $this->l_delim.'mojo:layout:embed ';
		
		if (strpos($template, $embed_format) !== FALSE)
		{
			$this->template_embed[$this->layout_name] = substr_count($template, $embed_format);
		}

		// Extract the tags from the supplied template
		$template = $this->_extract_tags($template);

		// Did the parser gather anything?
		if (count($this->tag_data) == 0)
		{
			return $template;
		}

		$CI =& get_instance();

		$packages_loaded = array();

		// Run through the tags and load the corresponding library
		foreach ($this->tag_data as $tags)
		{
			// Validate the tag data
			if ($tags['class'] == '' OR $tags['function'] == '')
			{
				continue;
			}

			$class    = $tags['class'];
			$function = $tags['function'];

			// Is this one of the core parser types? If so, don't bother looking around,
			// and save a few file_exists() calls. Performance FTW!
			if (in_array("mojomotor_parser_$class", $this->valid_drivers))
			{
				$return = $this->_process($CI->mojomotor_parser->$class, $function, $tags);
			}
			elseif ($CI->config->item('disable_third_party_addons'))
			{
				$return = $tags['full_match'];
			}
			else
			{
				$addon = strtolower($class);

				// $addon_path = APPPATH.'addons/'.$addon.'/'.$addon.EXT;
				$addon_path = APPPATH.'third_party/'.$class.'/';

				// Has this package been added already? If so, we can skip checking if the
				// file exists, and loading it, and just jump straight to the fun stuff.
				if (in_array($addon_path, $packages_loaded))
				{
					$return = $this->_process($CI->$class, $function, $tags);
				}
				// Before we try to load the package... does it actually exist?
				elseif ( ! file_exists($addon_path))
				{
					log_message('error', $CI->lang->line('unable_to_locate_addon').$class);
					$return = $tags['full_match'];
				}
				else
				{
					// New addon - load it up.
					$CI->load->add_package_path($addon_path);
					$CI->load->library($class);

					// To work around a limitation of packages, we'll try to load the language file here
					if (file_exists($addon_path.'language/'.$CI->config->item('language')))
					{
						$CI->lang->load($addon, '', FALSE, TRUE, $addon_path);
					}

					// Track it. We'll be unloading it at the end of this function.
					$packages_loaded[] = $addon_path;

					$return = $this->_process($CI->$class, $function, $tags);
				}
			}

			// Replace the marker in the template with the data
			$template = str_replace($tags['marker'], $return, $template);
		}

		// Adding packages adds to the list of dirs that CI will check for resources.
		// To prevent a bunch of extra paths potentially slowing things down, in later
		// execution, loop through $packages_loaded and unload any packages that were loaded
		foreach ($packages_loaded as $addon_path)
		{
			$CI->load->remove_package_path($addon_path);
		}

		return $template;
	}

	// --------------------------------------------------------------------

	/**
	 * Process
	 *
	 * All parser resources are recovered the same way, by reading a class
	 * and method. This function simply abstracts it with a sanity check.
	 *
	 * @param	object
	 * @param	string
	 * @param	array
	 * @return	string
	 */
	protected function _process($class, $function, $tags)
	{
		if (method_exists($class, $function))
		{
			$return = $class->$function($tags);
		}
		else
		{
			$return = $tags['full_match'];
		}

		return $return;
	}

	// --------------------------------------------------------------------

	/**
	 * Template Extractor
	 *
	 * This function identifies tags, parses them into their respective
	 * components, and stores them in a master array
	 *
	 * @return	void
	 */
	protected function _extract_tags($template, $params = array())
	{
//		$this->tag_data		= array();
//		$this->loop_count	= 0;

		// This loop keeps cycling as long as it continues to find valid opening tags
		while (is_int(strpos($template, $this->l_delim.$this->trigger)))
		{
			// Make a temporary copy of the template.
			// We will progressively slice it into pieces with each loop
			$_template = $template;

			// Identify the string position of the first occurence of a matched tag
			$this->in_point = strpos($_template, $this->l_delim.$this->trigger);

			// If the "in_point" returns false we are done looking for tags
			// This single conditional keeps the template engine from spiraling
			// into an infinite loop.
			if (FALSE === $this->in_point)
			{
				break;
			}


			// Let's parse out the components contained in the matched tag
			// ------------------------------------------------------------

			// Grab the opening portion of the tag: {mojo:some:tag param="value" param="value"}
			if ( ! preg_match("/".$this->l_delim.$this->trigger.".*?".$this->r_delim."/s", $_template, $matches))
			{
				$template = preg_replace("/".$this->l_delim.$this->trigger.".*?$/", '', $template);
				break;
			}

			$raw_tag = preg_replace("/(\r\n)|(\r)|(\n)|(\t)/", ' ', $matches['0']);

			$tag_length = strlen($raw_tag);

			$data_start = $this->in_point + $tag_length;

			$tag  = trim(substr($raw_tag, 1, -1));
			$args = trim((preg_match("/\s+.*/", $tag, $matches))) ? $matches['0'] : '';
			$tag  = trim(str_replace($args, '', $tag));

			$cur_tag_close = $this->l_delim.'/'.$tag.$this->r_delim;

			// ------------------------------------------------------------

			// Assign the class name/method name and any parameters
			$segs = $this->_extract_segments(substr($tag, strlen($this->trigger)));
			$args = $this->_extract_parameters($args);

			if ( ! empty($params))
			{
				$args = array_merge($params, $args);
			}
			
			// Trim the floating template, removing the tag we just parsed.

			$_template = substr($_template, $this->in_point + $tag_length);

			$out_point = strpos($_template, $cur_tag_close);

			// Do we have a tag pair?
			if (FALSE !== $out_point)
			{
				// Assign the data contained between the opening/closing tag pair
				$block = substr($template, $data_start, $out_point);

				// Define the entire "chunk" - from the left edge of the opening tag to the right edge of closing tag.
				$out_point = $out_point + $tag_length + strlen($cur_tag_close);

				$chunk = substr($template, $this->in_point, $out_point);
			}
			else
			{
				// Single tag...
				$block = ''; // Single tags don't contain data blocks

				// Define the entire opening tag as a "chunk"
				$chunk = substr($template, $this->in_point, $tag_length);
			}

			// Strip the "chunk" from the template, replacing it with a unique marker.
			// This will allow us to replace the data easier.
			
			if ($segs[0] == 'layout' && $segs[1] == 'embed')
			{
				// OK- an embed exists- so first see if it's in the existing array of layouts
				if (in_array($args['layout'], array_keys($this->template_embed)))
				{
					$this->reduce_layout_array();
					
					log_message('error', 'Recursive embedded template: '.$args['layout'].'.');
					
					$template = str_replace($chunk, '', $template);

					continue;					
				}

				// Get the contents for the embedded layout
				$CI =& get_instance();
				$embed_layout = $CI->layout_model->get_layout_by_name($args['layout']);						


				// If FALSE here?  The layout being embedded doesn't actually exist
				if ($embed_layout == FALSE)
				{
					$this->reduce_layout_array();
					
					log_message('error', 'Attemped to embed non-existent layout: '.$args['layout'].'.');
					
					$template = str_replace($chunk, '', $template);

					continue;					
				}
				
				$embed_string = $embed_layout->row('layout_content');
				
				// Oy- layouts can have regions w/identical names - can't allow that
				
				// Now we take a look at the current embed contents
				
				
				// OK- IF there are zero embeds left from the original count
				// we'll know the next embed is one level up
				// and we chop the end of the array and decrease the number
				// of those embeds by 1

				$embed_format = $this->l_delim.'mojo:layout:embed ';
	
				// We check for embeds ONLY in the included layout
				if (strpos($embed_string, $embed_format) !== FALSE)
				{
					// We know the next embed will be from the layout we just pulled in
					// We add the layout name to the array
					$this->template_embed[$args['layout']] = substr_count($embed_string, $embed_format);
				}
				else
				{
					// Has no embeds- we reduce cause next embed has to be a level up
					$this->reduce_layout_array();
				}
				

				
				// Anything that's commented gets pulled out now.
				$embed_string = $this->_remove_mm_comments($embed_string);
				// Extract the tags from the supplied template
				$embed_string = $this->_extract_tags($embed_string, array('emb_layout_id' => $embed_layout->row('id')));
				
				$template = str_replace($chunk, $embed_string, $template);

				continue;
			}

			$marker = 'M'.$this->loop_count.$this->marker;

			$template = str_replace($chunk, $marker, $template);

			// Build a multi-dimensional array containing all of the tag data we've assembled
			$this->tag_data[$this->loop_count]['tag_open']		= $raw_tag;
			$this->tag_data[$this->loop_count]['class']			= $segs[0];
			$this->tag_data[$this->loop_count]['function']		= $segs[1];
			$this->tag_data[$this->loop_count]['parameters']	= $args;
			$this->tag_data[$this->loop_count]['full_match']	= $chunk; // Matched data block - including opening/closing tags
			$this->tag_data[$this->loop_count]['template']		= '{'.$this->trigger.$segs[0].':'.$segs[1].'}'.$block.'{/'.$this->trigger.$segs[0].':'.$segs[1].'}';
			$this->tag_data[$this->loop_count]['marker']		= $marker;
			$this->tag_data[$this->loop_count]['tag_contents']	= $block;

			// Increment counter
			$this->loop_count++;
		}


		return $template;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Reduces the layout array once an embed is done with
	 *
	 * Cycles through the layouts, removing any moot levels
	 *
	 * @access	public
	 * @return	void
	 */
	function reduce_layout_array()
	{
		$last_key = array_pop(array_keys($this->template_embed));

		if (is_null($last_key))
		{
			return;
		}
		
		$this->template_embed[$last_key]--; 
						
		if ($this->template_embed[$last_key] == 0)
		{
			unset($this->template_embed[$last_key]);
			$this->reduce_layout_array();
		}
	}	
	
	// --------------------------------------------------------------------

	/**
	 * Extract Segments
	 *
	 * This function separates the tag segments and returns them in an array
	 *
	 * @param	string
	 * @return	array
	 */
	protected function _extract_segments($tag)
	{
		$result = array();

		// Grab the class name and method names contained
		// in the tag and assign them to variables.

		$result = explode(':', $tag);

		// Tags can either have one segment or two:
		// {mojo:first_segment}
		// {mojo:first_segment:second_segment}

		foreach($result as $key => $value)
		{
			$result[$key] = trim($result[$key]);
		}

		if ( ! isset($result[1]) OR $result[1] == '')
		{
			$result[1] = FALSE;
		}

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Extract Parameters
	 *
	 * This function extracts parameters from the opening portion of a tag
	 *
	 * @param	string
	 * @return	mixed
	 */
	protected function _extract_parameters($str)
	{
		if ($str == "")
		{
			return array();
		}

		// \047 - Single quote octal
		// \042 - Double quote octal

		// I don't know for sure, but I suspect using octals is more reliable than ASCII.
		// I ran into a situation where a quote wasn't being matched until I switched to octal.
		// I have no idea why, so just to be safe I used them here. - Rick

		/* ---------------------------------------
		/* matches['0'] => attribute and value
		/* matches['1'] => attribute name
		/* matches['2'] => single or double quote
		/* matches['3'] => attribute value
		/* ---------------------------------------*/

		preg_match_all("/(\S+?)\s*=\s*(\042|\047)([^\\2]*?)\\2/is",	 $str, $matches, PREG_SET_ORDER);

		if (count($matches) > 0)
		{
			$result = array();

			foreach($matches as $match)
			{
				$result[$match['1']] = (trim($match['3']) == '') ? $match['3'] : trim($match['3']);
			}

			return $result;
		}

		return array();
	}

	// --------------------------------------------------------------------
	//
	// MojoMotor Tag Parsing
	//
	// --------------------------------------------------------------------

	/**
	 * Tag handler
	 *
	 * This function is the gatekeeper for all MojoMotor tag parsing. It
	 * sends content to the correct function to handle each tag.
	 *
	 * @param	array
	 * @return	string
	 */
	public function tag_handler($tag)
	{
		$function = '_'.$tag['tag_seg1'];

		return ( ! method_exists($this, $function)) ? '' : $this->$function($tag);
	}

	// --------------------------------------------------------------------

	/**
	 * Remove all MojoMotor Code Comment Strings
	 *
	 * Mojo Layouts have a special Code Comments for site designer notes and are
	 * removed prior to processing.
	 *
	 * @param	string
	 * @return	string
	 */
	public function _remove_mm_comments($str)
	{
		return ( ! strpos($str, '{!--')) ? $str : preg_replace("/\{!--.*?--\}/s", '', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Save Prepare
	 *
	 * This function runs before region content is saved into the db, but doesn't
	 * affect what the user sees in the editor. It can handle pre-parsing if needed.
	 * Currently, it is not utilized.
	 *
	 * @param	array
	 * @return	string
	 */
	public function save_prepare($content)
	{
		return $content;
	}
}

/* End of file Mojomotor_Parser.php */
/* Location: system/mojomotor/libraries/Mojomotor_Parser.php */