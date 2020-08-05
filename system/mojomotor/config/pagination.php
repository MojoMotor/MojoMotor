<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This number can be changed to represent the number of results shown per page
$config['per_page'] = 10;

// The following are MojoMotor configurations. Changing them is not recommended.
$config['display_pages'] = FALSE;
$config['first_link'] = FALSE;
$config['last_link'] = FALSE;

$config['full_tag_open'] = '<div id="mojo_pagination">';
$config['full_tag_close'] = '</div>';

$config['next_link'] = '&rsaquo;';
$config['next_tag_open'] = '&nbsp;';
$config['next_tag_close'] = '';
$config['prev_link'] = '&lsaquo;';
$config['prev_tag_open'] = '';
$config['prev_tag_close'] = '';

// Do not change the anchor class
$config['anchor_class'] = 'mojo_sub_page button';

/* End of file pagination.php */
/* Location: ./application/config/pagination.php */