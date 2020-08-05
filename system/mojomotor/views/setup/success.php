<?php $this->load->view('mojo_header');?>

	<h2><?=$this->lang->line('install_success')?></h2>


	<p><?=$this->lang->line('success_exp')?></p>

	<p><?=$message?></p>

	<ul>
		<?php foreach($notices as $notice): ?>
			<li><?=$notice?></li>
		<?php endforeach; ?>
	</ul>

	<p><?=$this->lang->line('login_options_1').anchor('login', strtolower($this->lang->line('login'))).$this->lang->line('login_options_2')?></p>

	<p><?=$this->lang->line('enjoy')?> <?=anchor('', $this->lang->line('new_site'))?>!</p>

<?php $this->load->view('mojo_footer');?>