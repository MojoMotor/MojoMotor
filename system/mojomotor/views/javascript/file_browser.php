<html>
<head>
<title></title>
<link type="text/css" rel="stylesheet" href="<?=site_url('assets/css')?>" />
<style type="text/css">
body {
	background: #222;
}

#mojo-container {
	background: none;
	border: 0;
	padding: 10px;
	min-width: 0;
}

.mojo_insert_image, .mojo_delete_image {
	height: 16px;
	line-height: 16px;
	padding: 4px 8px 4px 24px!important;
	display: block;
	float: right;
	background-color: #535353;
	background-repeat: no-repeat;
	background-position: 4px center;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
	border: 1px solid #2B2B2B;
}

.mojo_insert_image {
	background-image: url(<?=site_url('assets/img/icon-download-file.png')?>);
}

.mojo_delete_image {
	background-image: url(<?=site_url('assets/img/icon-delete.png')?>);
	margin: 0 0 0 10px;
}

#mojo_reveal_page_content table.mojo_table {
	max-width: 1200px;
	min-width: 830px!important;
	margin: 0;
}

#mojo_reveal_page_content table.mojo_table thead th.mojo_fm_first_th {
	background: url(<?=site_url('assets/img/table_head_830.png')?>) no-repeat left top;
}

#mojo_reveal_page_content table.mojo_table thead th {
	background: url(<?=site_url('assets/img/table_head.png')?>) center top;
}

#mojo_reveal_page_content table.mojo_table thead th.mojo_fm_last_th {
	background: url(<?=site_url('assets/img/table_head_830.png')?>) no-repeat right top;
}

#mojo_reveal_page_content table table tr:hover td.mojo_action_buttons a {
	background-color: #2B2B2B;
}

#mojo_reveal_page_content table table tr:hover td {
	background: #535353;
	color: #C2C2C2;
}

#mojo_reveal_page_content table tr:hover td {
	background-color: inherit;
}

.mojo_preview_thumb {
	text-align: center;
}
.mojo_action_buttons {
	width: 170px;
}

.mojo_col1 {
	width: 90px;
}

#mojo_fm_scroll {
	overflow: auto;
	overflow-x: hidden;
	height: 360px;
}

</style>
</head>
<body>

<div id="mojo-container">
<div id="mojo_reveal_page_content">
<table class="mojo_table">
	<thead>
		<tr>
			<th class="mojo_col1 mojo_fm_first_th">&nbsp;</th>
			<th class="mojo_col2"><?=$this->lang->line('filename')?></th>
			<th class="mojo_col3"><?=$this->lang->line('size')?></th>
			<th class="mojo_col4 mojo_fm_last_th">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
<tr>
	<td colspan="4" style="margin:0;padding:0;">
		<div id="mojo_fm_scroll">
		<table class="mojo_table" style="width:100%;">
		<?php foreach($files as $file): ?>
			<tr>
				<td class="mojo_col1 mojo_preview_thumb"><img src="<?=$file['thumb']?>" alt="" /></td>
				<td class="mojo_col2"><?=$file['name']?></td>
				<td class="mojo_col3"><?=byte_format($file['size'])?></td>
				<td class="mojo_col4 mojo_action_buttons">
					<a href="<?=site_url('editor/delete_file/'.$file['name'])?>" class="mojo_delete_image" title="<?=$this->lang->line('file_delete_confirm').$file['name'].'?'?>"><?=$this->lang->line('delete')?></a>
					<a href="<?=$upload_url.$file['name']?>" class="mojo_insert_image"><?=$this->lang->line('insert')?></a>
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
		</div>
	</td>
</tr>
	</tbody>
</table>
</div>
</div>

<script charset="utf-8" type="text/javascript" src="<?=base_url().index_page()?>/javascript/load/jquery"></script>
<script type="text/javascript">
jQuery(document).ready(function() {

	jQuery('.mojo_insert_image').click(function (e) {
		e.preventDefault();
		window.opener.CKEDITOR.tools.callFunction(<?=$CKEditorFuncNum?>, jQuery(this).attr('href'));
		window.close();
	});

	jQuery('.mojo_delete_image').click(function (e) {
		e.preventDefault();
		if (window.confirm(jQuery(this).attr('title')))
		{
			var image_row = jQuery(this).parents('tr table tr');
			jQuery.ajax({
				type: 'POST',
				url: jQuery(this).attr('href'),
				data: '<?=$csrf_token.'='.$csrf?>'+'&'+'ajax=true', // used to ensure this came from a confirm, and not an accidental new tab or something
				success: function (data) {
					if (data != '')
					{
						image_row.fadeOut();
					}
				},
				error: function (data) {
					alert('<?= $this->lang->line('problem_deleting_file')?>');
				}
			});

		}

	});

});
</script>
</body>
</html>