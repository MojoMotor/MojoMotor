<?=form_open('settings/edit')?>

<div class="shrinkwrap">
		<p>
			<label for="site_name"><?=$this->lang->line('site_name')?></label>
			<?=form_input('site_name', $site_name, 'id="site_name" class="mojo_textbox"')?>
		</p>
		<p>
			<label for="default_page"><?=$this->lang->line('default_page')?></label>
			<?= form_dropdown('default_page', $all_pages, $default_page, 'id="default_page" class="mojo_textbox" style="padding:6px;"')?>
		</p>
		<p>
			<label for="page_404"><?=$this->lang->line('page_404')?></label>
			<?= form_dropdown('page_404', $page_404_list, $page_404, 'id="page_404" class="mojo_textbox" style="padding:6px;"')?>
		</p>
		<p>
			<label for="in_page_login"><?=$this->lang->line('in_page_login')?></label>
			<?=form_checkbox('in_page_login', 'y', $in_page_login, 'style="height: 35px;"')?>
		</p>
		<p>
			<label for="language"><?=$this->lang->line('language')?></label>
			<?= form_dropdown('language', $all_langs, $lang, 'id="theme" class="mojo_textbox"')?>
		</p>
		<p>
			<label for="theme"><?=$this->lang->line('theme')?></label>
			<?= form_dropdown('theme', $all_themes, $theme, 'id="theme" class="mojo_textbox"')?>
		</p>
</div>

<div class="shrinkwrap mojo_shift_right">
	<?=form_submit('site_settings', $this->lang->line('save_settings'), 'class="button"')?>
</div>

<?=form_close()?>