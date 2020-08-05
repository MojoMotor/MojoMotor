<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| Editor Configuration
| -------------------------------------------------------------------
| Specify your toolbar below as an array containing arrays of toolbar groups.
*/


// Default
$config['wysiwyg_toolbar'] = array(
	array('mojo_save'),
	array('Maximize'),
	array('Bold', 'Italic', 'BulletedList', 'NumberedList'),
	array('Link', 'Unlink', 'Image', 'Format'),
	array('mojo_cancel')
);


// Default, with the addition of the 'Source' tool, for manually editing HTML source code.
/*
$config['wysiwyg_toolbar'] = array(
	array('mojo_save'),
	array('Source'),
	array('Maximize'),
	array('Bold', 'Italic', 'BulletedList', 'NumberedList'),
	array('Link', 'Unlink', 'Image', 'Format'),
	array('mojo_cancel')
);
*/


// This toolbar has every possible option enabled
/*
$config['wysiwyg_toolbar'] = array(
	array('mojo_save'),
	array('Source'),
	array('Maximize'),
	array('NewPage', 'Preview', 'Templates'),
	array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord'),
	array('Print', 'SpellChecker'),
	array('Undo', 'Redo'),
	array('Find', 'Replace'),
	array('SelectAll', 'RemoveFormat'),
	array('Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'),
	array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'BulletedList', 'NumberedList'),
	array('Outdent', 'Indent', 'Blockquote', 'CreateDiv', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'),
	array('Link', 'Unlink', 'Anchor', 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'PageBreak'),
	array('Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor', 'ShowBlocks'),
	array('mojo_cancel')
);
*/


/* End of file ckeditor.php */
/* Location: system/mojomotor/config/ckeditor.php */