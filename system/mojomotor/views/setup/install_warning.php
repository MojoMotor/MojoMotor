<?php $this->load->view('mojo_header');?>

	<h2><?=$this->lang->line("install_warning")?></h2>

	<p class="error"><?=$this->lang->line('warnings_encountered')?></p>

	<ul>
	<?php foreach($installation_warnings as $warning): ?>
		<li><?=$warning?></li>
	<?php endforeach; ?>
	</ul>

	<?php if ($must_manual_config): ?>
		<p><?=$this->lang->line('config_override')?></p>
	<?php endif; ?>

	<p><?=$this->lang->line('errors_addressed')?></p>

	<p><?=anchor('setup/verify_state/skip_config_check', $this->lang->line('continue'), 'class="button"')?></p>

<?php $this->load->view('mojo_footer');?>