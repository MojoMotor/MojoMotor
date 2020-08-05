<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$lang = array(

// Site wide Language vars
'no_such_page' 				=> 'The page you requested cannot be found',
'submit'					=> "Submit",
'email'						=> "Email", // used as username also
'site_title'				=> "Site Title",
'continue'					=> "Continue",
'yes'						=> "Yes",
'no'						=> "No",
'or'						=> 'Or',
'none'						=> 'None',
'help'						=> 'Help',
'account'					=> 'Account',
'close'						=> 'Close',
'back_to'					=> 'Back to ',
'edit'						=> 'Edit',
'insert'					=> 'Insert',
'delete'					=> 'Delete',
'view_mode'					=> 'View Mode',
'id'						=> 'id',
'local'						=> 'Local',
'global'					=> 'Global',
'super_global'				=> 'Super Global',
'alpha_dash_exp'			=> 'Letters, numbers, slashes, dashes and underscores.',
'comma_separated'			=> 'A comma separated list of words.',
'refresh'					=> 'Refresh',

'layouts'					=> 'Layouts',
'pages'						=> 'Pages',
'files'						=> 'Files',
'members'					=> 'Members',
'settings'					=> 'Settings',
'utilities'					=> 'Utilities',

'edit_mode'					=> 'Edit Mode',
'plain_text'				=> 'Plain Text',
'wysiwyg'					=> 'Graphical',
'welcome_back'				=> 'Welcome back ',
'delete_confirm'			=> 'Are you sure you want to remove %?',



// Login
'password'					=> "Password",
'remember_me'				=> "Remember Me",
'login_greeting'			=> "Welcome to MojoMotor! The publishing engine that does less",
'login'						=> "Login",
'logout'					=> "Logout",
'logout_confirm'			=> "Are you sure you want to logout?",
'email_password_warning'	=> "Please enter your email and password to login.",
'login_again'				=> "Log me back in",
'login_sub_greeting'		=> "Please login below. You're about to have fun!",
'login_failure'				=> "Wrong user and/or password. Please try again.",
'login_result_failure'		=> 'System unable to send result. Try removing \"www.\" from your URL.',
'forgotten_password'		=> "Forgotten password?",
'forgotten_password_instructions'	=> "Please enter your email. A password reset request will be emailed to you shortly.",
'logout_success_message'	=> "You've been successfully logged out of MojoMotor.",
'forgotten_password_sent'	=> "A password reset confirmation has been sent to <em>%email</em>.",
'trouble_sending_email'		=> 'There was a problem sending email. Please enable debugging and contact support.',
'password_email1'			=> 'Your password change has been successful. Your new password is ',
'password_email2'			=> ' and can now be used to login.',
'password_change_success'	=> 'Your password change has been successful, and your new password has been emailed to you.',
'password_change_fail'		=> 'Your password change encountered an error.',
'password_reset_unable'		=> 'Unable to reset your password. Please try again.',
'password_reset'			=> 'Password Reset',
'password_reset_email1'		=> 'Someone (presumably you), has requested a password reset for your account at ',
'password_reset_email2'		=> 'To reset it now, follow this link to our website:',
'password_reset_email3'		=> "If you did not initiate this request, simply disregard this email, and we're sorry for bothering you.",
'no_record'					=> 'There is no record for this entry.',


// General Errors and such
'missing_js_file'			=> "The requested file could not be found.",
'no_permissions'			=> 'You lack sufficient permissions for this action',
'last_item_delete'			=> 'IMPORTANT!<br/>MojoMotor requires at least one layout and one page to function correctly. If you delete the last one, your site may become unstable. Be absolutely sure you want to do this.',
'page_not_found'			=> 'MojoMotor cannot find a page to load. Be sure you have defined a layout and a page.',


// Layouts
'layout'					=> 'Layout',
'layout_name'				=> 'Layout Name',
'layout_type'				=> 'Layout Type',
'layout_content'			=> 'Content',
'layout_add'				=> 'Add Layout',
'layout_edit'				=> 'Edit Layout',
'layout_delete'				=> 'Remove Layout',
'layout_webpage'			=> 'Webpage',
'layout_embed'				=> 'Embed Content',
'layout_css'				=> 'Style Sheet',
'layout_js'					=> 'Javascript',
'layout_add_successful'		=> 'Layout was successfully added.',
'layout_add_fail'			=> 'There was a problem adding this layout.',
'layout_edit_successful'	=> 'Layout was successfully updated.',
'layout_edit_fail'			=> 'There was a problem updating this layout.',
'layout_delete_successful'	=> 'Layout was successfully removed.',
'layout_delete_fail'		=> 'There was a problem removing this layout.',
'layout_nonexistent'		=> 'Could not find this layout.',
'layout_name_taken'			=> 'Layout name already in use.',
'layout_type_message'		=> 'Changing this after pages are built may produce unexpected results.',
'layout_type_message_warning'	=> 'There are Pages using this layout, changing its type may result in errors.',
'layout_delete_message_warning' => 'Any Pages using this layout will also be deleted.',
'layout_region_warning_title'	=> 'Regions have been removed',
'layout_region_warning'		=> ' has been removed. If you proceed, this <em>will result in data loss</em>. Are you sure you want to save this layout with these regions removed?',
'layout_embed_p_region'		=> 'Page regions are not allowed in Embed Content type layouts.',
'layout_save'				=> 'Save Layout',
'global_region_comment'		=> 'MojoMotor will replace this content dynamically with the contents of the Global Region',
'page_region_comment'		=> 'MojoMotor will replace this content dynamically with the contents of the Page Region',


// Pages
'page'						=> 'Page',
'page_add'					=> 'Add Page',
'page_edit'					=> 'Edit Page',
'page_delete'				=> 'Remove Page',
'subpage_delete'			=> 'This page has child pages. If you remove it you will PERMANENTLY remove them also. This cannot be undone. Please be absolutely sure you want to proceed.',
'page_new'					=> 'New Page',
'page_settings'				=> 'Page Settings',
'page_title'				=> 'Page Title',
'url_title'					=> 'URL Title',
'include_in_page_list'		=> 'Include in Page List?',
'include_in_page_list_exp'	=> 'If unchecked this page will not show when a page list is called that would otherwise include it.',
'meta_keywords'				=> 'Keywords',
'meta_description'			=> 'Description',
'page_change_layout'		=> 'This cannot be changed once the Page has been created.',
'page_add_successful'		=> 'Page was successfully added.',
'page_add_fail'				=> 'There was a problem adding this page.',
'page_edit_successful'		=> 'Page was successfully updated.',
'page_edit_fail'			=> 'There was a problem updating this page.',
'page_delete_successful'	=> 'Page was successfully removed.',
'page_delete_fail'			=> 'There was a problem removing this page.',
'page_nonexistent'			=> 'Could not find this page.',
'site_structure_update_successful'	=> 'Page structure was successfully updated.',
'site_structure_update_fail'=> 'There was a problem updating the page structure.',
'url_title_taken'			=> 'URL Title already in use.',
'visit_page'				=> 'Visit Page',


// Member stuff
'invalid_email'				=> 'Could not process with the new email provided.',
'duplicate_email'			=> 'Email already in use',
'user_cannot_be_deleted'	=> 'This user cannot be removed',
'cannot_delete_self'		=> 'You cannot remove your own account',
'change_password'			=> 'Change Password',
'password_old'				=> 'Old Password',
'password_new'				=> 'New Password',
'password_new_confirm'		=> 'New Password Confirm',
'password_confirm'			=> 'Password Confirm',
'leave_blank'				=> 'Blank for no change',
'password_wrong'			=> 'The provided password was incorrect.',
'password_too_long'			=> 'The password you provided is too long.',
'password_too_short'		=> 'The password you provided must be at least %password_length characters.',
'password_required'			=> 'New members must be provided with a password',
'passwords_no_match'		=> 'Passwords did not match.',
'member_register'			=> 'Register Member',
'member_add'				=> 'Add Member',
'member_edit'				=> 'Edit Member',
'member_delete'				=> 'Remove Member',
'member_group'				=> 'Member Group',
'member'					=> 'Member',
'member_add_successful'		=> 'Member was successfully added',
'member_add_fail'			=> 'There was a problem adding this member',
'member_edit_successful'	=> 'Member was successfully edited.',
'member_edit_fail'			=> 'There was a problem editing this member.',
'member_delete_successful'	=> 'Member was successfully removed.',
'member_delete_fail'		=> 'There was a problem removing this member.',
'notify_member'				=> 'Notify Member?',
'notify_member_exp'			=> 'The username/password will be sent in an email.',
'mojo_account_activation'	=> '%site_name account created',
'mojo_account_activation_body'	=> "You have just had an account created at %site_name. You may login with \nEmail: %email \npassword: %password \n\n%login_page",
'notification_success'		=> ' and notification was successfully sent',
'notification_failure'		=> ' but email notification could not be sent',
'member_save'				=> 'Save Member',


// Settings stuff
'site_name'					=> 'Site Name',
'default_page'				=> 'Default Page',
'page_404'					=> '404 Page',
'in_page_login'				=> 'In Page Login',
'site_path'					=> 'Site Path',
'setting_update_successful'	=> 'Site Settings Updated',
'setting_update_lang_failure' => 'Site Settings Updated, but language could not be',
'setting_update_failure'	=> 'Unable to update site settings',
'theme'						=> 'MojoMotor Theme',
'save_settings'				=> 'Save Settings',
'language'					=> 'Language',


// Utilities
'new_version'				=> 'New version of MojoMotor available',
'run_update'				=> 'Perform MojoMotor Update',
'new_version_exp'			=> '<em>There is a new version of MojoMotor available.</em> We recommend keeping your versions current. Please visit the <a href="%x">MojoMotor download area</a> to get a copy of the latest release.',
'export_to_ee'				=> "Export to ExpressionEngine ",
'import_site'				=> 'Import Site',
'export_ee_description'		=> 'MojoMotor has the ability to export into <a href="http://expressionengine.com">ExpressionEngine 2</a>. The resulting file will be importable into ExpressionEngine via an import module. Please see <a href="http://mojomotor.com/user_guide/admin/admin_utilities.html">the export documentation</a> for more information on using this feature, including <em>important notes</em>.',
'php_info'					=> 'PHP Info',
'php_info_exp'				=> ' can be used to help debugging or technical support issues.',


// Help
'version'					=> 'Version',
'help_verbiage1'			=> 'If you need support, the <a href="http://mojomotor.com/forums/">MojoMotor support forums</a> are here for you.',
'help_verbiage2'			=> 'MojoMotor is about empowerment. We want to empower you to shape MojoMotor. Found a bug? Please <a href="http://mojomotor.com/bug_tracker/">report it</a>. Have a feature suggestion? Hit us up on the <a href="http://mojomotor.com/forums/">MotorMotor forums</a>. We\'re here for you!',


// File Manager
'filename'					=> "Name",
'size'						=> "Size",
'date'						=> 'Date',
'no_files_found1'			=> 'There are no files in the upload directory ', // space after
'no_files_found2'			=> 'To add files, close this window, and select the "Upload" tab from the Image Properties dialog; select your file and choose "Upload".', // space after
'file_delete_confirm'		=> 'Are you sure you wish to permanently delete ', // space after
'problem_deleting_file'		=> 'There was a problem deleting the file.',
'unable_read_upload_dir'	=> 'MojoMotor was unable to read your upload directory. This could be caused if the upload path is not a valid directory or the directory can not be opened due to permission restrictions or filesystem errors.',
'open_in_new_window'		=> 'Open link in a new tab',


// Addons
'unable_to_locate_addon'	=> 'Unable to locate the addon you have specified: ',
'invalid_addon'				=> 'Invalid Addon',
'invalid_addon_call'		=> 'Invalid Addon call',

// Contact
'contact_default_subject' 	=> 'contact form', // gets preceded with site name
'contact_send_failure'		=> 'There was a problem sending this contact form.',
'contact_message_empty'		=> 'Please enter a message to send.',
'contact_invalid_email'		=> 'Please enter a valid email address.',

// Editor
'enter_url'					=> 'Provide a URL to link to',
'or_choose_page'			=> 'Or choose a page on your site:',
'or_choose_page_dropdown'	=> '(None)',
'page_save'					=> 'Save Page',

// Cookie Consent
'cookies_required_for_login'	=> 'Cookies are required for login.  <a href=%s>Click here to enable cookies</a>.',


// we're done, leave this last one in place
"" => ""
);

/* End of file mojomotor_lang.php */
/* Location: system/mojomotor/languages/english/mojomotor_lang.php */