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

/*
		http://mojomotor.com/user_guide/license.html
 @link		http://mojomotor.com
 @since		Version 1.0
 @filesource
*/
function CKEDITOR_GETURL(a){if(a.indexOf("lang")>=0)return Mojo.URL.editor_lang_path}
function region_alter_callback(a){var b={};b.Cancel=function(){jQuery(".layout_edit_form_dialog").dialog("close")};b.OK=function(){jQuery(".layout_edit_form_dialog").dialog("close");jQuery("#mojo_layout_edit_form").prepend('<input type="hidden" name="region_warning" value="accepted" />').submit()};jQuery("<div class='layout_edit_form_dialog'><p>"+a+Mojo.Lang.layout_region_warning+"</p></div>").dialog({resizable:false,title:Mojo.Lang.layout_region_warning_title,modal:true,width:"400px",buttons:b})}
jQuery(document).ready(function(){function a(){mojoEditor.add_notice('<a href="'+Mojo.URL.site_path+'/setup/update">'+Mojo.Lang.run_update+"</a>","system_notice")}mojoEditor.setup_mojobars("{adminMarkup}");mojoEditor.delete_abstraction("layout_delete");mojoEditor.delete_abstraction("page_delete");mojoEditor.delete_abstraction("member_delete");if(Mojo.Vars.update_flag)mojoEditor.is_open?a():jQuery("#mojo-container").delegate("#collapse_tab","click",function(){a()})});
