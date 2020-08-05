<table class="mojo_table">
	<thead>
	<tr>
		<th><?=$this->lang->line('layout')?></th>
		<th><?=$this->lang->line('layout_type')?></th>
		<th><?= $this->pagination->create_links();?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($layouts->result() as $layout): ?>
	<tr id="mojo_layout_delete_<?=$layout->id?>" class="<?=alternator('even','odd')?>">
		<td><?=$layout->layout_name?></td>
		<td><?=$this->lang->line('layout_'.$layout->layout_type)?></td>
		<td class="mojo_table_edit">
			<img src="<?=site_url('assets/img')?>/pencil.png" alt="<?=$this->lang->line('layout_edit')?>" height="16" width="16" /> 
			<?=anchor('layouts/edit/'.$layout->id, $this->lang->line('edit'), 'class="mojo_sub_page" title="'.$this->lang->line('layout_edit').'"')?>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<img src="<?=site_url('assets/img')?>/cross.png" alt="<?=$this->lang->line('delete')?>" height="16" width="16" /> 
			<?=anchor('layouts/delete/'.$layout->id, $this->lang->line('delete'), 'class="mojo_layout_delete" title="'.str_replace('%', $layout->layout_name, $this->lang->line('delete_confirm')).' '.$this->lang->line('layout_delete_message_warning').'"')?>
		</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<div class="shrinkwrap">
	<?= $this->pagination->create_links(); ?>
	<?=anchor('layouts/add', $this->lang->line('layout_add'), 'class="mojo_sub_page shrinkwrap_submit" title="'.$this->lang->line('layout_add').'"')?>
	<div class="mojo_clear"></div>
</div>