<div class="shrinkwrap">
	<?=form_open('members/update', '', array('user_id'=>$user_id))?>

		<p>
			<label for="email"><?=$this->lang->line('email')?></label>
			<?=form_input('email', $email, 'id="email" class="mojo_textbox"')?>
		</p>

		<?php
			// If this is an existing member, offer to notify them
			if ($user_id == ''):
		?>
			<p>
				<label for="notify_member"><?=$this->lang->line('notify_member')?></label>
				<?=form_checkbox('notify_member', 'y', FALSE, 'id="notify_member"')?> <img src="<?= site_url('assets/img/mojo_more_info_highlight.png')?>" height="12" width="12" alt="" /> <em><?= $this->lang->line('notify_member_exp')?></em>
			</p>
		<?php endif; ?>

		<?php
			// We'll only show "change member group" if they are admins
			// The "primary account holder" (ie: the person who installed it) cannot be moved out of admin
			if ($this->auth->is_admin() && $user_id != 1):
		?>
		<p>
			<label for="member_group"><?=$this->lang->line('member_group')?></label>
			<?= form_dropdown('member_group', $member_groups, $current_member_group, 'class="mojo_textbox"')?>
		</p>
		<?php endif; ?>

		<?php
			// If this is an existing member, offer change password option, otherwise
			// simply have a field to create the password.
			if ($user_id != ''):
		?>
			<p>
				<label for="password_old"><?=$this->lang->line('password_old')?></label>
				<?=form_password('password_old', '', 'id="password_old" class="mojo_textbox"')?> <img src="<?= site_url('assets/img/mojo_more_info.png')?>" height="12" width="12" alt="" /> <?= $this->lang->line('leave_blank')?>
			</p>
		<?php endif; ?>
			<p>
				<label for="password"><?=$this->lang->line($password_lang)?></label>
				<?=form_password('password', '', 'id="password" class="mojo_textbox"')?>
			</p>
			<p>
				<label for="password_confirm"><?=$this->lang->line($password_confirm_lang)?></label>
				<?=form_password('password_confirm', '', 'id="password_confirm" class="mojo_textbox"')?>
			</p>


		<fieldset id="mojo_edit_mode_buttons"><legend><?=$this->lang->line('edit_mode')?></legend>
			<p><label><?=form_radio('edit_mode', 'source', $edit_mode_plain)?> <?=$this->lang->line('plain_text')?></label> <img src="<?=site_url('assets/img')?>/tag.png" alt="" height="16" width="16" /></p>
			<p><label><?=form_radio('edit_mode', 'wysiwyg', $edit_mode_wysiwyg)?> <?=$this->lang->line('wysiwyg')?></label> <img src="<?=site_url('assets/img')?>/text_bold.png" alt="" height="16" width="16" /></p>
		</fieldset>

		<p class="mojo_shift_right">
			<?=form_submit('member_prefs', $this->lang->line('member_save'), 'class="button"')?>
		</p>

	<?=form_close()?>
</div>