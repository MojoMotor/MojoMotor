<div>

	<?=form_open('pages/update', '', $form_hidden)?>

		<p>
			<label for="page_title"><?=$this->lang->line('page_title')?></label>
			<?=form_input('page_title', $page_title, 'id="page_title" class="mojo_textbox"')?>
		</p>

		<p>
			<label for="url_title"><?=$this->lang->line('url_title')?></label>
			<span class="mojo_textbox" id="mojo_partial_url_title"><?=site_url('')?>/<?=form_input('url_title', $url_title, 'id="url_title" size="10"')?></span> <img src="<?= site_url('assets/img/mojo_more_info.png')?>" height="12" width="12" alt="" /> <?= $this->lang->line('alpha_dash_exp')?>
		</p>

		<p>
			<label for="include_in_page_list"><?=$this->lang->line('include_in_page_list')?></label>
			<?=form_checkbox('include_in_page_list', 'y', $include_in_page_list)?>
			 <img src="<?= site_url('assets/img/mojo_more_info.png')?>" height="12" width="12" alt="" /> <?= $this->lang->line('include_in_page_list_exp')?>
		</p>

		<p>
			<label for="layout_id"><?=$this->lang->line('layout')?></label>
			<?= form_dropdown('layout_id', $layouts, $layout_id, 'class="mojo_textbox"' . ($layout_id ? ' disabled' : ''))?>
			 <img src="<?= site_url('assets/img/mojo_more_info.png')?>" height="12" width="12" alt="" /> <?= $this->lang->line('page_change_layout')?>
		</p>

		<p>
			<label for="meta_keywords"><?=$this->lang->line('meta_keywords')?></label>
			<?=form_input('meta_keywords', $meta_keywords, 'id="meta_keywords" class="mojo_textbox"')?> <img src="<?= site_url('assets/img/mojo_more_info.png')?>" height="12" width="12" alt="" /> <?= $this->lang->line('comma_separated')?>
		</p>

		<p>
			<label for="meta_description"><?=$this->lang->line('meta_description')?></label>
			<?=form_textarea(array('name'=>'meta_description', 'id'=>'meta_description', 'value'=>$meta_description, 'class'=>'mojo_textbox'))?>
		</p>

		<p class="mojo_shift_right">
			<?=form_submit('page_prefs', $this->lang->line('page_save'), 'class="button"')?>
		</p>

	<?=form_close()?>
<div>