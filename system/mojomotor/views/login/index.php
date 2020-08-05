<?php $this->load->view('mojo_header');?>

			<h2><?=$this->lang->line("login_greeting")?></h2>

			<?= form_open('login/process')?>

			<p id="mojo_login_error"><?=$this->lang->line('login_sub_greeting')?></p>

			<?=$message?>

			<dl>
				<dt><p><label for="mojo_email"><?=$this->lang->line("email")?>:</label></p></dt>
				<dd><p><?=form_input("email", '', 'id="mojo_email" class="mojo_textbox"')?></p></dd>
				<dt><p><label for="mojo_password"><?=$this->lang->line("password")?>:</label></p></dt>
				<dd><p><?=form_password("password", '', 'id="mojo_password" class="mojo_textbox"')?></p></dd>
				<dt>&nbsp;</dt>
				<dd><p><?=form_button(array('name'=>'login', 'value'=>'login', 'content'=>'&nbsp;<span>'.$this->lang->line('login').'</span>&nbsp;', 'id'=>'mojo_submit', 'class'=>'button', 'type'=>'submit'))?></p></dd>
				<dt>&nbsp;</dt>
				<dd><p><?=form_checkbox(array('name'=>'remember_me', 'value'=>'yes', 'checked'=>$remember_me, 'id'=>'remember_me'))?> <label for="remember_me" class="mojo_remember_me"><?=$this->lang->line('remember_me')?></label></p></dd>
			</dl>

			<?= form_close()?>

			<p><small><?=anchor('login/forgotten_password', $this->lang->line('forgotten_password'))?></small></p>

<?php $this->load->view('mojo_footer');?>