<?php $this->load->view('mojo_header');?>

	<p><?=$this->lang->line('forgotten_password_instructions')?></p>

	<?= form_open('login/forgotten_password')?>

	<?=validation_errors()?>

	<dl>
		<dt><p><label for="email"><?=$this->lang->line("email")?>:</label></p></dt>
		<dd><p><?=form_input("email", '', 'id="email" class="mojo_textbox"')?></p></dd>
		<dt>&nbsp;</dt>
		<dd><p><?=form_submit('submit', $this->lang->line('submit'), 'id="mojo_submit" class="button"')?></p></dd>
	</dl>

	<?= form_close()?>

<?php $this->load->view('mojo_footer');?>
