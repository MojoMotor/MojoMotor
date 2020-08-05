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
var mojoLogin={success:false,container:null,open:function(a){a.overlay.fadeIn("fast");a.container.fadeIn("fast");a.data.fadeIn("fast",function(){jQuery("#mojo_email").select()})},close:function(a){a.overlay.fadeOut("fast");a.container.fadeOut("fast");a.data.fadeOut(function(){mojoLogin.success||jQuery.modal.close()})}};
jQuery(document).ready(function(){jQuery("head").append('<link type="text/css" rel="stylesheet" href="'+Mojo.URL.css_path+'/modal_login" />');jQuery("a.mojo_activate_login").click(function(a){a.preventDefault();jQuery("div.mojo_login").modal({persist:true,overlayId:"mojo-overlay",containerId:"mojo-container",opacity:35,overlayClose:true,onOpen:mojoLogin.open,onClose:mojoLogin.close});jQuery(".mojo_login_field input").focus(function(){jQuery(this).val()==Mojo.Lang.email&&jQuery(this).val("")})});jQuery(".mojo_login form").submit(function(a){if(mojoLogin.success)return true;
a.preventDefault();if(jQuery("#mojo_email").val().indexOf("@")==-1||jQuery("#mojo_email").val().indexOf(".")==-1||jQuery("#mojo_email").val().length<5||jQuery("#mojo_password").val()==""){jQuery("#mojo_login_error").addClass("error").html(Mojo.Lang.email_password_warning);return false}a=jQuery(".mojo_login form").attr("action")+"_ajax";jQuery.ajax({type:"POST",url:a,dataType:"json",data:jQuery(".mojo_login form").serialize(),success:function(b){if(b==null)jQuery("#mojo_login_error").addClass("error").html(Mojo.Lang.login_result_failure);
else if(b.login_status==="success"){group_name=b.group_name;mojoLogin.success=true;jQuery(".mojo_activate_login").remove();jQuery("#mojo_login_error").removeClass("error").html(b.message);jQuery.modal.close();jQuery(".mojo_login form").submit()}else jQuery("#mojo_login_error").addClass("error").html(b.message)}})})});
