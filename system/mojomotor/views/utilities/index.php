<?php if ($update_status === 'new'):?>
<div>
	<h3><?=lang('new_version')?></h3>

	<p class="shun"><?=str_replace('%x', $mm_download_url, lang('new_version_exp'))?></p>
	
</div>
<?php endif;?>

<div>
	<h3><?=lang('export_to_ee')?></h3>

	<p class="shun"><?= lang('export_ee_description')?></p>
	<p class="mojo_shift_right">
	<button class="button" onclick="document.location='<?=site_url('utilities/export')?>';">
		<?=anchor('utilities/export', lang('export_to_ee').$ee_version)?>
	</button>
	</p>
</div>

<div>
	<h3><?=lang('php_info')?></h3>

	<p class="shun"><?=anchor('utilities/php_info', lang('php_info'), 'class="mojo_sub_page" title="'.lang('php_info').'"')?><?=lang('php_info_exp')?></p>
	
</div>