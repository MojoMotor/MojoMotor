/**
 * MojoMotor - by EllisLab
 *
 * @package		MojoMotor
 * @author		MojoMotor Dev Team
 * @copyright	Copyright (c) 2003 - 2012, EllisLab, Inc.
 * @license		http://mojomotor.com/user_guide/license.html
 * @link		http://mojomotor.com
 * @since		Version 1.0
 */
 
// ------------------------------------------------------------------------

(function()
{
	var save_command = {
		modes: {wysiwyg: 1, source: 1},
		exec: function (editor) {
			jQuery.ajax({
				type: 'POST',
				url: Mojo.URL.admin_path + "/editor/update_page_region",
				complete: function () {
					mojoEditor.remove_editor(editor);
				},
				data: {
					ci_csrf_token: Mojo.Vars.csrf,
					page_url_title: Mojo.Vars.page_url_title,
					page_id: Mojo.Vars.page_id,
					region_id: region_id,
					region_layout_id: region_layout_id,
					layout_id: Mojo.Vars.layout_id,
					region_type: mojoEditor.region_type,
					value: editor.getData()
				}
			});
		}
	};

	var pluginName = 'mojo_save';

	CKEDITOR.plugins.add(pluginName, {
		init: function(editor) {
			editor.addCommand(pluginName, save_command);
					
			editor.ui.addButton(pluginName, {
				label: editor.lang.save,
				command: pluginName
			});
     	}
	});

})();