<?php $this->load->view('mojo_header');?>

	<h2><?=$this->lang->line('license')?></h2>

	<div id="license_fulltext"><?=auto_typography($this->lang->line('license_fulltext'))?></div>

	<p style="margin: 15px 0;"><?=$this->lang->line('agree_continue')?></p>

	<p><?=anchor('setup/verify_state', $this->lang->line('i_agree'), 'class="button"')?></p>

<?php $this->load->view('mojo_footer');?>