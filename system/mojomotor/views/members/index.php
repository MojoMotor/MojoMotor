<table class="mojo_table">
	<thead>
	<tr>
		<th><?=$this->lang->line('member')?></th>
		<th><?=$this->lang->line('member_group')?></th>
		<th><?= $this->pagination->create_links();?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($members->result() as $member): ?>
	<tr id="mojo_member_delete_<?=$member->id?>" class="<?=alternator('even','odd')?>">
		<td><?=$member->email?></td>
		<td><?=ucwords($member->group_title)?></td>
		<td class="mojo_table_edit">
			<img src="<?=site_url('assets/img')?>/pencil.png" alt="<?=$this->lang->line('member_edit')?>" height="16" width="16" /> 
			<?=anchor('members/edit/'.$member->id, $this->lang->line('edit'), 'class="mojo_sub_page" title="'.$this->lang->line('member_edit').'"')?>
			<?php if($member->id != 1 && $member->id != $this->session->userdata('id')): ?>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<img src="<?=site_url('assets/img')?>/cross.png" alt="<?=$this->lang->line('delete')?>" height="16" width="16" /> 
			<?=anchor('members/delete/'.$member->id, $this->lang->line('delete'), 'class="mojo_member_delete" title="'.str_replace('%', $member->email, $this->lang->line('delete_confirm')).'"')?> 
			<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<div class="shrinkwrap">
	<?= $this->pagination->create_links(); ?><?=anchor('members/add', $this->lang->line('member_add'), 'class="mojo_sub_page shrinkwrap_submit" title="'.$this->lang->line('member_register').'"')?>
	<div class="mojo_clear"></div>
</div>
