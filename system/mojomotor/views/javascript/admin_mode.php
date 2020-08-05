<div id="mojo-modal-content" class="mojo_login">
	<div id="mojo-modal-data">

		<?php $this->load->view('javascript/bar_logo');?>

		<?php $this->load->view('javascript/bar_sub');?>

		<div id="mojo_main_bar">
			<ul>
				<li id="mojo_admin_layouts"><?=anchor('layouts/index', $this->lang->line('layouts'), ' title="'.$this->lang->line('layouts').'"')?></li>
				<li id="mojo_admin_pages"><?=anchor('pages/index', $this->lang->line('pages'), ' title="'.$this->lang->line('pages').'"')?></li>
				<li id="mojo_admin_members"><?=anchor('members/index', $this->lang->line('members'), ' title="'.$this->lang->line('members').'"')?></li>
				<li id="mojo_admin_settings"><?=anchor('settings/index', $this->lang->line('settings'), ' title="'.$this->lang->line('settings').'"')?></li>
				<li id="mojo_admin_utilities"><?=anchor('utilities/index', $this->lang->line('utilities'), ' title="'.$this->lang->line('utilities').'"')?></li>
			</ul>
		</div>

	</div>
</div>