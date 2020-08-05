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
if("undefined"==typeof console||!console.log)console={log:function(){return!1}};jQuery.ajaxSetup({cache:!0});update_edit_mode_callback=function(a){Mojo.edit_mode=a};mojoEditor={revealed_page:"",breadcrumb_object:{},is_open:!0,last_active_menu:"",active_menu:"",region_type:"local",mojo_editor_ref:"",editor_active:!1,follow_link:!0};jQuery(window).resize(function(){jQuery("#mojo-container").width(jQuery(this).width())});
mojoEditor.setup_mojobars=function(a){jQuery.address.init(function(){jQuery.address.strict(!1)}).change(function(a){""!=a.path&&(mojoEditor.follow_link||mojoEditor.reveal_page(a.path))});jQuery("head").append('<link type="text/css" rel="stylesheet" href="'+Mojo.URL.css_path+'/jqueryui" />\n<link type="text/css" rel="stylesheet" href="'+Mojo.URL.css_path+'" />');""!=Mojo.Vars.additional_css&&jQuery("head").append('<link type="text/css" rel="stylesheet" href="'+Mojo.Vars.additional_css+'" />\n');!1==
Mojo.Vars.bar_state?(mojoEditor.is_open=!1,bar_position="-77px"):(mojoEditor.is_open=!0,bar_position="0px");jQuery("body").prepend('<div id="MojoMotorContentPusher" style="height:77px;margin-top:'+bar_position+';"></div>');jQuery.modal(a,{overlayId:"mojo_editor_overlay",containerId:"mojo-container",maxHeight:77,containerCss:{top:bar_position,left:0,width:"100%"},close:!1,focus:!1,autoResize:!1,autoPosition:!1,onOpen:function(a){a.container.slideDown("fast",function(){a.data.show()})},onClose:function(a){a.container.slideUp("fast")}});
reveal_page=jQuery('<p id="mojo_reveal_page_notice"></p><div id="mojo_breadcrumbs"><div id="mojo_ajax_page_loader"><img src="{cp_img_path}ajax-loader_dark.gif" alt="" height="24" width="24" /></div><a href="#" id="mojo_reveal_page_back" class="mojo_sub_page"></a><span id="mojo_reveal_current_page"></span></div><div id="mojo_reveal_page"><div id="mojo_reveal_page_content"></div></div><a href="#" class="mojo_reveal_page_close">'+Mojo.Lang.close+"</a>").css({overflow:"auto","max-height":jQuery(window).height()-
150+"px"});jQuery("#mojo-container").append(reveal_page);jQuery(reveal_page).hide();jQuery(".mojo_reveal_page_close").css({left:jQuery(document).width()/2-15+"px"});jQuery(".mojo_reveal_page_close").click(function(a){a.preventDefault();mojoEditor.unreveal_page()});mojoEditor.collapse_bar();mojoEditor.setup_logout();jQuery("#mojo-container").delegate(".mojo_page_refresh_trigger","click",function(){window.location.reload()});jQuery("#mojo-container").delegate("#mojo_main_bar li a, .mojo_sub_page","click",
function(a){a.preventDefault();mojoEditor.follow_link=true;mojoEditor.last_active_menu=mojoEditor.active_menu;mojoEditor.active_menu=jQuery(this).parent().attr("id");if(mojoEditor.active_menu=="")mojoEditor.active_menu=mojoEditor.last_active_menu;if(jQuery("#mojo_reveal_page").is(":visible")===false||mojoEditor.revealed_page!=jQuery(this).attr("href")){mojoEditor.revealed_page=jQuery(this).attr("href");if(jQuery(this).hasClass("mojo_sub_page")&&!jQuery(this).hasClass("mojo_breadcrumb_supress"))mojoEditor.breadcrumb_object.seg_method=
jQuery(this).attr("title");else{jQuery(this).hasClass("mojo_breadcrumb_supress")&&mojoEditor.active_page(true);mojoEditor.breadcrumb_object.seg_controller=[jQuery(this).attr("title"),jQuery(this).attr("href")];mojoEditor.breadcrumb_object.seg_method=""}mojoEditor.reveal_page(mojoEditor.revealed_page)}else(!jQuery(this).hasClass("mojo_sub_page")||jQuery(this).hasClass("mojo_breadcrumb_supress"))&&mojoEditor.unreveal_page()});jQuery("#mojo-container").delegate("#layout_name","keyup",function(){mojoEditor.liveUrlTitle(this)});
jQuery("#mojo-container").delegate("#page_title","keyup",function(){jQuery("input[name=page_id]").length||mojoEditor.liveUrlTitle(this,"url_title")});jQuery.getScript(Mojo.URL.site_path+"/javascript/load_ckeditor",function(){mojoEditor.is_open&&mojoEditor.enable_page_regions(true)})};
mojoEditor.collapse_bar=function(){collapse_tab=jQuery('<div id="collapse_tab"></div>');jQuery("#mojo-container").append(collapse_tab);!0==Mojo.Vars.bar_state&&jQuery(collapse_tab).hide();jQuery("#mojo_bar_view_mode, #collapse_tab").click(function(){mojoEditor.is_open?(mojoEditor.enable_page_regions(!1),jQuery(reveal_page).slideUp("fast"),jQuery("#collapse_tab").slideDown("fast"),jQuery("#MojoMotorContentPusher").animate({"margin-top":"-77px"}),jQuery("#mojo-container").animate({top:"-"+jQuery("#mojo-container").height()}),
mojoEditor.is_open=!1):(!1==mojoEditor.editor_active&&mojoEditor.enable_page_regions(!0),jQuery("#MojoMotorContentPusher").animate({"margin-top":0}),jQuery("#mojo-container").animate({top:0},function(){jQuery("#collapse_tab").slideUp("fast")}),mojoEditor.is_open=!0);jQuery.ajax({type:"POST",data:Mojo.Vars.csrf_token+"="+Mojo.Vars.csrf,url:Mojo.URL.admin_path+"/editor/bar_state/"+mojoEditor.is_open})})};
mojoEditor.enable_page_regions=function(a){!1==a?jQuery(".mojo_editable_layer_header, .mojo_editable_layer").fadeOut("fast",function(){jQuery(this).remove()}):(jQuery(".mojo_page_region, .mojo_global_region").each(function(){var a,f=jQuery(this).attr("id").replace("_"," ");jQuery(this).hasClass("mojo_page_region")?(a=Mojo.Lang.local,region_class="mojo_local"):(a=jQuery(this).attr("data-mojo_id"),a=void 0!=a&&Mojo.Vars.layout_id!=a?Mojo.Lang.super_global:Mojo.Lang.global,region_class="mojo_global");
f=jQuery("<div class='mojo_editable_layer_header'><p>"+f+" ("+a+")</p></div>");jQuery("<div class='mojo_editable_layer "+region_class+"'></div>").css({opacity:"0.4",width:jQuery(this).width(),height:jQuery(this).outerHeight()}).hide().prependTo(jQuery(this)).fadeIn("fast");f.hide().prependTo(jQuery(this)).fadeIn("fast")}),jQuery(".mojo_editable_layer, .mojo_editable_layer_header").click(function(){mojoEditor.init_editor(jQuery(this).parent())}))};
mojoEditor.upload_result=function(a,e){var f,c;c=CKEDITOR.dialog.getCurrent();"image"==c.getName()&&(c.selectPage("info"),f=c.getContentElement("info","txtUrl"),c=c.getContentElement("info","txtAlt"),f&&f.setValue(a),c&&c.setValue(e))};
mojoEditor.init_editor=function(a){region_id=a.attr("id");region_layout_id=a.attr("data-mojo_id");mojoEditor.region_type=a.hasClass("mojo_global_region")?"global":"local";jQuery(".mojo_editable_layer_header, .mojo_editable_layer").remove();""==mojoEditor.mojo_editor_ref&&(CKEDITOR.on("dialogDefinition",function(a){var c=a.data.name,a=a.data.definition;"image"==c?(uploadTab=a.getContents("Upload"),uploadTab.get("upload").label="",uploadTab.get("uploadButton").style="margin-bottom: 2em",Mojo.Vars.show_expanded_image_options||
(a.removeContents("advanced"),a.removeContents("Link"),a.removeContents("image"),infoTab=a.getContents("info"),infoTab.remove("txtBorder"),infoTab.remove("txtHSpace"),infoTab.remove("txtVSpace"),infoTab.remove("cmbAlign"),infoTab.get("htmlPreview").style="display:none",infoTab.get("basic").style="width:10%")):"link"==c&&(a.onLoad=function(){this.hidePage("target")},a.removeContents("upload"),infoTab=a.getContents("info"),infoTab.remove("linkType"),infoTab.remove("browse"),infoTab.add({type:"select",
label:Mojo.Lang.or_choose_page,style:"margin-top: 5px",id:"link",items:[],onLoad:function(){var a=this;a.add(Mojo.Lang.or_choose_page_dropdown,"0");jQuery.ajax({type:"POST",dataType:"json",url:Mojo.URL.admin_path+"/editor/list_pages/",success:function(b){jQuery.each(b,function(b,c){a.add(c.page_title,c.url_title)})}})},onChange:function(){if(""!=this.getValue()&&0!==this.getValue()){var a=CKEDITOR.dialog.getCurrent();null!==a&&(a.setValueOf("info","url",Mojo.URL.site_path+"/"+this.getValue()),a.setValueOf("info",
"protocol","http://"))}}}),infoTab.add({type:"checkbox",id:"targeta",label:Mojo.Lang.open_in_new_window,style:"margin: 15px 0;",setup:function(){var a=mojoEditor.mojo_editor_ref.getSelection().getStartElement().$;"_blank"==jQuery(a).attr("target")&&this.setValue(!0)},commit:function(){var a=CKEDITOR.dialog.getCurrent();null!==a&&(this.getValue()?a.setValueOf("target","linkTargetType","_blank"):a.setValueOf("target","linkTargetType","notSet"))}}))}),CKEDITOR.plugins.basePath=Mojo.URL.editor_plugin_path);
var e={skin:"mojo,"+Mojo.URL.editor_skin_path,language:"en",startupMode:Mojo.edit_mode,toolbar:Mojo.toolbar,extraPlugins:"mojo_cancel,mojo_save",removePlugins:"scayt,bidi,iframe,save",toolbarCanCollapse:!1,toolbarStartupExpanded:!0,resize_enabled:!0,width:128>a.width()?128:a.width(),height:a.height()+20,minHeight:80,dialog_backgroundCoverColor:"#5C5C5C",dialog_backgroundCoverOpacity:0.8,filebrowserBrowseUrl:Mojo.URL.admin_path+"/editor/browse/",filebrowserWindowWidth:"864",filebrowserWindowHeight:"500",
filebrowserUploadUrl:Mojo.URL.admin_path+"/editor/upload/"};a.ckeditor(function(){mojoEditor.mojo_editor_ref=this;mojoEditor.pre_edit_data=this.getData();mojoEditor.editor_active=true;jQuery("body").addClass("mojo_editor_active")},e)};mojoEditor.remove_editor=function(a){a&&(1===a.getCommand("maximize").state&&a.execCommand("maximize"),a.destroy(),mojoEditor.editor_active=!1,jQuery("body").removeClass("mojo_editor_active"),mojoEditor.is_open&&mojoEditor.enable_page_regions(!0))};
mojoEditor.setup_logout=function(){jQuery("#mojo_logout").click(function(a){a.preventDefault();a={Cancel:function(){jQuery(this).dialog("close")}};a[Mojo.Lang.logout]=function(){jQuery.post(Mojo.URL.site_path+"/login/logout",function(){jQuery.modal.close();window.location=Mojo.URL.site_path});jQuery(this).dialog("close")};jQuery("<p>"+Mojo.Lang.logout_confirm+"</p>").dialog({resizable:!1,title:Mojo.Lang.logout,modal:!0,position:["center",110],buttons:a})})};
mojoEditor.delete_abstraction=function(a){jQuery("#mojo-container").delegate(".mojo_"+a,"click",function(e){e.preventDefault();var f=jQuery(this).attr("href"),e=Mojo.Lang[a],c="";"page_delete"==a&&0<jQuery(this).closest("li").find("li").length&&(c+='<p class="error">'+Mojo.Lang.subpage_delete+"</p>");if(("layout_delete"==a||"page_delete"==a)&&1==jQuery(".mojo_"+a).length)c+='<p class="error">'+Mojo.Lang.last_item_delete+"</p>";var g={Cancel:function(){jQuery(".mojo_"+a+"_dialog").dialog("close")}};
g[e]=function(){jQuery.ajax({type:"POST",url:f,data:Mojo.Vars.csrf_token+"="+Mojo.Vars.csrf+"&confirmed=true",dataType:"json",success:function(b){"success"==b.result&&jQuery("#mojo_"+a+"_"+b.id).slideUp(function(){jQuery(this).remove();"page_delete"==a&&mojoEditor.send_site_structure()});jQuery(".mojo_"+a+"_dialog").dialog("close");mojoEditor.add_notice(b.message,b.result)}})};jQuery("<div class='mojo_"+a+"_dialog'><p>"+jQuery(this).attr("title")+"</p>"+c+"</div>").dialog({resizable:!1,title:e,modal:!0,
width:"400px",buttons:g})})};
mojoEditor.subpage_reinit=function(){0<jQuery("#mojo_reveal_page_content form").length&&jQuery("#mojo_reveal_page_content form input:visible").eq(0).focus();jQuery("textarea.mojo_textbox").length&&jQuery.getScript(Mojo.URL.js_path+"/plugin/tabby",function(){jQuery("textarea.mojo_textbox").tabby()});jQuery("#mojo_site_structure").length&&(mojoEditor.update_page_hierarchy(),jQuery("#mojo_site_structure li").draggable({handle:" > div",opacity:0.75,helper:"clone"}),jQuery("#mojo_site_structure div, .mojo_site_structure_placeholder").droppable({accept:"#mojo_site_structure li",tolerance:"pointer",
drop:function(a,e){var f=jQuery(this).parent(),c=!jQuery(this).hasClass("mojo_site_structure_placeholder");c&&0===f.children("ul").length&&f.append("<ul />");c?f.children("ul").prepend(e.draggable):f.after(e.draggable);f.find("div.ie_fix").removeClass("ie_fix_hover");f.find(".mojo_site_structure_placeholder").css({visibility:"hidden",opacity:"0",height:"10px"});mojoEditor.send_site_structure()},over:function(){jQuery(this).filter("div.ie_fix").addClass("ie_fix_hover");jQuery(this).filter(".mojo_site_structure_placeholder").css({visibility:"visible",
opacity:"1",height:"29px"})},out:function(){jQuery(this).filter("div.ie_fix").removeClass("ie_fix_hover");jQuery(this).filter(".mojo_site_structure_placeholder").css({visibility:"hidden",opacity:"0",height:"10px"})}}),mojoEditor.pages_line_tree())};
mojoEditor.pages_line_tree=function(){jQuery("#mojo_site_structure li").css({background:"none"});jQuery("#mojo_site_structure ul > li").css({background:"url({cp_img_path}page_line_child_single.png) no-repeat left top"});jQuery("#mojo_site_structure > li").has("ul li").css({background:"url({cp_img_path}page_line_parent_children.png) no-repeat left top"});jQuery("#mojo_site_structure ul li").has("ul li").css({background:"url({cp_img_path}page_line_child_children.png) no-repeat left top"});jQuery("#mojo_site_structure ul").each(function(){jQuery(this).children("li:last").css({background:"#202020 url({cp_img_path}page_line_child_last.jpg) no-repeat left top"})});
jQuery("#mojo_site_structure ul li:last-child").has("ul li").css({background:"url({cp_img_path}page_line_sublast_children.png) no-repeat left top"})};mojoEditor.active_page=function(a){a?(jQuery("#mojo_main_bar li").removeClass(),mojoEditor.active_menu=""):mojoEditor.last_active_menu!=mojoEditor.active_menu&&""!=mojoEditor.active_menu&&(jQuery("#mojo_main_bar li").removeClass(),jQuery("#mojo-container #"+mojoEditor.active_menu).addClass(mojoEditor.active_menu+"_active"))};
mojoEditor.reveal_page=function(a,e){"undefined"==typeof e&&(e=!0);jQuery("#mojo_ajax_page_loader").fadeIn("fast");jQuery("body").css({cursor:"wait"});mojoEditor.active_page();jQuery.address.value(a);jQuery("#mojo_reveal_page_content").load(a,function(){jQuery("#mojo_ajax_page_loader").hide();jQuery(".mojo_reveal_page_close").show();jQuery("body").css({cursor:"default"});jQuery("#mojo_reveal_page_content form").submit(function(a){a.preventDefault();mojoEditor.follow_link=!0;jQuery.ajax({type:"POST",
url:jQuery(this).attr("action"),data:jQuery(this).serialize(),dataType:"json",success:function(a){void 0!==a.message&&void 0!==a.result&&mojoEditor.add_notice(a.message,a.result);void 0!==a.reveal_page&&(mojoEditor.breadcrumb_object.seg_method="",jQuery("#mojo_reveal_current_page").hide(),mojoEditor.revealed_page=a.reveal_page,mojoEditor.reveal_page(a.reveal_page,!1));void 0!==a.callback&&(args=void 0!==a.callback_args?a.callback_args:"",window[a.callback](args))}})});jQuery(reveal_page).slideDown("fast",
function(){mojoEditor.breadcrumbs()});mojoEditor.subpage_reinit()});!0===e&&mojoEditor.remove_notice();mojoEditor.follow_link=!1};mojoEditor.unreveal_page=function(){mojoEditor.remove_notice();mojoEditor.active_page(!0);jQuery("#mojo_breadcrumbs").slideUp("fast");jQuery("#mojo_reveal_page").slideUp("fast");jQuery(".mojo_reveal_page_close").hide();mojoEditor.follow_link=!1};
mojoEditor.add_notice=function(a,e){"undefined"==typeof e&&(e="notice");jQuery("#mojo_reveal_page_notice").html(a);jQuery("#mojo_reveal_page_notice").attr("class",e);jQuery("#mojo_reveal_page_notice").css({visibility:"visible","margin-left":"-"+jQuery("#mojo_reveal_page_notice").outerWidth()/2+"px"});jQuery("#mojo_reveal_page_notice").hide().fadeIn("fast")};mojoEditor.remove_notice=function(){jQuery("#mojo_reveal_page_notice").fadeOut("fast",function(){jQuery(this).css({visibility:"hidden"})})};
mojoEditor.breadcrumbs=function(){jQuery("#mojo_reveal_page_back").hide();jQuery("#mojo_reveal_current_page").hide();jQuery("#mojo_reveal_page_back").html(mojoEditor.breadcrumb_object.seg_controller[0]).show();jQuery("#mojo_reveal_page_back").attr("href",mojoEditor.breadcrumb_object.seg_controller[1]);""!=mojoEditor.breadcrumb_object.seg_method&&jQuery("#mojo_reveal_current_page").html(mojoEditor.breadcrumb_object.seg_method).show()};
mojoEditor.update_page_hierarchy=function(){jQuery(".mojo_add_page_inline").each(function(){var a="";jQuery(this).parents("li").each(function(){a=jQuery(this).attr("id").substring(17)+"/"+a});var e=jQuery(this).attr("href").substring(0,jQuery(this).attr("href").indexOf("add")+3);jQuery(this).attr("href",e+"/"+a)})};
mojoEditor.send_site_structure=function(){jQuery.ajax({type:"POST",url:Mojo.URL.admin_path+"/pages/page_reorder/",data:Mojo.Vars.csrf_token+"="+Mojo.Vars.csrf+"&"+jQuery("#mojo_site_structure").serializeTree("site_structure",".ui-draggable-dragging"),dataType:"json",success:function(a){mojoEditor.add_notice(a.message,a.result);mojoEditor.update_page_hierarchy();mojoEditor.pages_line_tree()}})};
mojoEditor.liveUrlTitle=function(a,e){for(var f=jQuery(a).getCaretPos(),c=jQuery(a).val().toLowerCase(),g=Mojo.URL.separator,b="",h=0;h<c.length;h++){var d=c.charCodeAt(h);32<=d&&128>d?b+=c.charAt(h):"223"==d?b+="ss":"224"==d?b+="a":"225"==d?b+="a":"226"==d?b+="a":"229"==d?b+="a":"227"==d?b+="ae":"230"==d?b+="ae":"228"==d?b+="ae":"231"==d?b+="c":"232"==d?b+="e":"233"==d?b+="e":"234"==d?b+="e":"235"==d?b+="e":"236"==d?b+="i":"237"==d?b+="i":"238"==d?b+="i":"239"==d?b+="i":"241"==d?b+="n":"242"==d?
b+="o":"243"==d?b+="o":"244"==d?b+="o":"245"==d?b+="o":"246"==d?b+="oe":"249"==d?b+="u":"250"==d?b+="u":"251"==d?b+="u":"252"==d?b+="ue":"255"==d?b+="y":"257"==d?b+="aa":"269"==d?b+="ch":"275"==d?b+="ee":"291"==d?b+="gj":"299"==d?b+="ii":"311"==d?b+="kj":"316"==d?b+="lj":"326"==d?b+="nj":"353"==d?b+="sh":"363"==d?b+="uu":"382"==d?b+="zh":"256"==d?b+="aa":"268"==d?b+="ch":"274"==d?b+="ee":"290"==d?b+="gj":"298"==d?b+="ii":"310"==d?b+="kj":"315"==d?b+="lj":"325"==d?b+="nj":"352"==d?b+="sh":"362"==d?
b+="uu":"381"==d&&(b+="zh")}h=RegExp(g+"{2,}","g");c=b.replace("/<(.*?)>/g","");c=c.replace(/\s+/g,g);c=c.replace(/\//g,g);c=c.replace(/[^a-z0-9\-\._]/g,"");c=c.replace(/\+/g,g);c=c.replace(h,g);c=c.replace(/^_/g,"");c=c.replace(/^-/g,"");c=c.replace(/\./g,"");e="undefined"==typeof e?a:jQuery("#"+e);jQuery(e).val(c);jQuery(a).setCaretPos(f)};
jQuery.fn.getCaretPos=function(){var a=this[0];if(a.selectionStart)return 0<a.selectionStart?a.selectionStart:0;if(a.createTextRange){a.focus();var e=document.selection.createRange(),a=a.createTextRange(),f=a.duplicate();a.moveToBookmark(e.getBookmark());f.setEndPoint("EndToStart",a);return f.text.length}return"0"};
jQuery.fn.setCaretPos=function(a){var e=this[0];e.setSelectionRange?(e.focus(),e.setSelectionRange(a,a)):e.createTextRange&&e.createTextRange().collapse(!0).moveStart("character",a).moveEnd("character",a).select()};
jQuery.fn.serializeTree=function(a,e){var f="",c;c=void 0===e?this.children():this.children().not(e);0<c.length?c.each(function(){if(-1===this.id.indexOf("drop_target")){var c=jQuery(this).attr("id").substring(17),b="";0<jQuery(this).find("ul li").length?(a+="["+c+"]",b=jQuery("ul:first",jQuery(this)).serializeTree(a,e),a=a.replace(/\[[^\]\[]*\]$/,"")):f+="&"+a+"["+c+"]="+c;""!=b&&(f+=b)}}):f+="&"+a+"["+this.attr("id")+"]=";return""!=f?f:!1};