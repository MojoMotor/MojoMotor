<div>
	<?=form_open('layouts/update', $form_attributes, $form_hidden)?>

	<p>
		<label for="layout_name"><?=$this->lang->line('layout_name')?></label>
		<?=form_input('layout_name', $layout_name, 'id="layout_name" class="mojo_textbox"')?> <img src="<?= site_url('assets/img/mojo_more_info.png')?>" height="12" width="12" alt="" /> <?= $this->lang->line('alpha_dash_exp')?>
	</p>

	<p>
		<label for="layout_type"><?=$this->lang->line('layout_type')?></label>
		<?= form_dropdown('layout_type', $layout_types, $layout_type, 'id="layout_type" class="mojo_textbox"')?>  <?= $layout_type_message?>
	</p>

	<p>
		<label for="layout_content"><?=$this->lang->line('layout_content')?></label>
		<?=form_textarea(array('name'=>'layout_content', 'id'=>'layout_content', 'value'=>$layout_content, 'class'=>'mojo_textbox'))?>
	</p>

	<p class="mojo_shift_right">
		<?=form_submit('layout_prefs', $this->lang->line('layout_save'), 'class="button"')?>
	</p>

	<?=form_close()?>
</div>