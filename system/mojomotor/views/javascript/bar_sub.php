			<div id="mojo_sub_bar">
				<div id="mojo_bar_view_mode">
					<p><br /><br /><?=$this->lang->line('view_mode')?></p></div>
					<p id="mojo_welcome"><?=$this->session->userdata('email')?></p>
					<p>
						<?=anchor('members/edit/'.$this->session->userdata('id'), $this->lang->line('account'), 'class="mojo_sub_page mojo_breadcrumb_supress" title="'.$this->lang->line('account').'"')?>&nbsp;&nbsp;&nbsp;&nbsp;
						<?=anchor('help', $this->lang->line('help'), 'class="mojo_sub_page mojo_breadcrumb_supress" title="'.$this->lang->line('help').'"')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<img class="mojo_logout_icon" src="<?=site_url('assets/img')?>/logout.png" alt="" height="14" width="13" /><?=anchor('login/logout', $this->lang->line('logout'), 'id="mojo_logout"')?>
					</p>
			</div>