<?php $this->load->view('mojo_header');?>

	<h2><?=$this->lang->line('update')?></h2>

	<ul>
		<?php foreach($notices as $notice): ?>
			<li><?=$notice?></li>
		<?php endforeach; ?>
	</ul>

	<p><?= str_replace('%x', $mojo_version, $this->lang->line('update_complete'))?><?=anchor('', $site_name)?>.</p>

<?php $this->load->view('mojo_footer');?>