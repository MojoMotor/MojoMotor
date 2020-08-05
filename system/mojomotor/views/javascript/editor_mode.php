<div id="mojo-modal-content" class="mojo_login">
	<div id="mojo-modal-data">

		<?php $this->load->view('javascript/bar_logo');?>

		<?php $this->load->view('javascript/bar_sub');?>

		<div id="mojo_main_bar">
			<ul>
				<li id="mojo_admin_pages"><?=anchor('pages/index', $this->lang->line('pages'), ' title="'.$this->lang->line('pages').'"')?></li>
			</ul>
		</div>

	</div>
</div>
