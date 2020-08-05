<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There is a reserved route:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
*/

$route = array(
	'default_controller'					=> 'page',
	'admin/?'								=> 'admin/login/index',
	'(setup|admin|welcome)(?:(/.+))?'		=> '$1$2',
	'(assets|javascript|login)(?:(/.+))?'	=> 'admin/$1$2',
	'(.+)'									=> 'page/content/$1'
);

// Change this to welcome to run the installer, or change it
// to page if the installer asked you to.

if (config_item('install_lock') == 'unlocked')
{
	$route["default_controller"] = "welcome";
}

/* End of file routes.php */
/* Location: system/mojomotor/config/routes.php */