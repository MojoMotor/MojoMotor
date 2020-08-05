<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$lang = array(

// Installation Errors
'db_unable_to_connect'		=> "Unable to connect to your database server using the provided settings.",
'install_warning'			=> "Installation Warning",
'warnings_encountered'		=> "Before we can install MojoMotor, we need to address the following issues.",
'db_file_not_stock'			=> "The database configuration file is not the stock MojoMotor file. <em>If you continue, you will <span class=\"error\">over-write this file</span> and <span class=\"error\">delete any previous MojoMotor installations</span></em>. If you are sure you want to do this, you can continue.",
'unreadable_config'			=> 'Your config.php file is unreadable. Please make sure the file exists and that the file permissions to 666 (or the equivalent write permissions for your server) on the following file: system/mojomotor/config/config.php.',
'unwritable_config'			=> 'Your config.php file does not appear to have the proper file permissions.',
'unwritable_routes'			=> 'Your routes.php file does not appear to have the proper file permissions.  Please set the file permissions to 666 (or the equivalent write permissions for your server) on the following file: system/mojomotor/config/routes.php. Alternatively, you can manually set $route["default_controller"] to "page" to continue.',
'unreadable_database'		=> 'Your database.php file cannot be read from system/mojomotor/config/database.php.',
'unwritable_database'		=> 'Your database.php file does not appear to have the proper file permissions.',
'unwritable_cache_folder'	=> 'Your cache folder does not appear to have proper permissions.  Please set the folder permissions to 777 (or the equivalent write permissions for your server) on the following folder: system/cache.',
'unwritable_uploads_folder' => 'The upload folder mm_uploads does not appear to have write permissions.',
'min_php_version'			=> 'MojoMotor requires PHP 5, this server is using '.PHP_VERSION.'.',
'add_site_data_fail'		=> "Unable to insert the default site data",
'config_override'			=> "Your config.php and/or database.php files were not writable. You can manually set the information in the files if you like, otherwise, please set the file permissions to 666 (or the equivalent write permissions for your server) on the following files:<br />- system/mojomotor/config/config.php;<br />- system/mojomotor/config/database.php.",
'install_lock_true'			=> 'Installation Lock is currently set. To re-install, you need to remove <br /><br /><code>$config["install_lock"] = "locked";</code><br /><br /> from system/mojomotor/config/config.php.',

// Setup (Install, Update)
'license'					=> 'License Agreement',
'agree_continue'			=> 'In order to install MojoMotor, you must agree to abide by the license Terms and Conditions as stated above.',
'i_agree'					=> 'I agree',
'errors_addressed'			=> 'After you\'ve addressed these issues, you may continue below.',
'install'					=> "Install",
'install_explanation'		=> "In order to install, we'll need to gather a bit of information about you and your site.",
'database_settings'			=> "Database Settings",
'site_settings'				=> "Site Settings",
'show_advanced_options'		=> "Show Advanced Options",
'hide_advanced_options'		=> "Hide Advanced Options",
'advanced_install_options'	=> "Advanced Options",
'install_email_exp'			=> "Your email is used as your login user id.",
'site_title_exp'			=> "The title of your website.",

'site_content'				=> 'Site Content',
'site_content_exp_blank'	=> 'A nearly blank site; perfect if you know what you\'re doing.',
'site_content_exp_default'	=> 'Default site - pre-filled with a layout, pages and content.',
'site_content_exp_import'	=> 'Import - brings your html pages into MojoMotor.',
'import_site'				=> "Import the site ",
'default_site'				=> "Install the default site",
'blank_site'				=> "Install a blank site",

'install_success'			=> "MojoMotor is installed",
'routes_change'				=> "You will now want to manually change your default controller to \"page\" in  system/mojomotor/config/routes.php",
'success_exp'				=> "Seriously, that's all there was to it.",
'important_info_blank_pass' => "You can now start using your new site by logging in. You might want to start by reading the online tutorials. The password below was randomly generated just for you, but you'll probably want to change it to something easier to remember. You can do this by logging in and visiting your account link, in the toolbar.",
'important_info_user_picked_pass' => "You can now start using your new site by logging in. You might want to start by reading the online tutorials.",
'set_installation_lock'		=> 'It is very important for the security of your site, that you now open up system/mojomotor/config/config.php and add <br /><br /><code>$config["install_lock"] = "locked";</code><br /><br /> to it.',
'login_with_email'			=> "You will be logging in with your email",
'login_options_1'			=> "You can either login from the central ",
'login_options_2'			=> ", or look for the \"login\" link on the footer of any page of your site.",
'enjoy'						=> "Most of all, enjoy your ",
'new_site'					=> "new site",

'database_settings'			=> "Database Settings",
'db_name'					=> "Database Name",
'db_name_exp'				=> "The name of the database MojoMotor will run on. If this database isn't found, MojoMotor will attempt to create it for you - depending on your permissions, you may need to manually create it first.",
'db_user'					=> "Database User Name",
'db_user_exp'				=> "Your database user name.",
'db_password'				=> "Database Password",
'db_password_exp'			=> "Your database password.",
'db_host'					=> "Database Host",
'db_host_exp'				=> "This is usually \"localhost\".",
'db_prefix'					=> "Table Prefix",
'db_prefix_exp'				=> "Useful if you're sharing this database with other web applications.",

'advanced_install_options_exp'	=> "These options provide you with fine grain control over the process.",
'sqlite_db'					=> "SQLite",
'mysql_db'					=> "MySQL",
'database_type'				=> "Database Type",
'database_type_exp'			=> "MojoMotor supports both MySQL and SQLite. SQLite installation won't require a database server or database information.",
'base_url'					=> "Site URL",
'base_url_exp'				=> "This is the address your site can be found at. We've taken a guess for you.",
'password'					=> "Password",
'password_exp'				=> "Your account password. If you leave it blank, MojoMotor will choose a hard to guess password for you.",
'pconnect'					=> "Persistent Connections",
'pconnect_exp'				=> "Control database connection persistence.",

'update'					=> 'MojoMotor Update',
'update_complete'			=> 'Update complete, you are now running version %x of MojoMotor. Please review the messages above and return to ',
'update_to_version'			=> 'Update to %x successful.',


// we're done, leave this last one in place
"" => ""
);

/* End of file install_lang.php */
/* Location: system/mojomotor/languages/english/install_lang.php */