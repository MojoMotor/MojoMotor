<div id="mojo-modal-content" class="mojo_login" style="display:none">
	<div id="mojo-modal-title"><img src="<?=site_url('assets/img/mojomotor_logo.jpg')?>" width="156" height="53" alt="MojoMotor" /></div>
	<div id="mojo-modal-data">
		<h2><?=$this->lang->line("login")?></h2>

		<?= form_open('login/process')?>

		<p id="mojo_login_error"><?=$this->lang->line('login_sub_greeting')?></p>

		<p class="mojo_login_field">
			<label for="mojo_email"><?=$this->lang->line("email")?>:</label> 
			<?=form_input("email", '', 'id="mojo_email"')?>
		</p>
		<p class="mojo_login_field">
			<label for="mojo_password"><?=$this->lang->line("password")?>:</label> 
			<?=form_password("password", '', 'id="mojo_password"')?>
		</p>
		<p class="mojo_submit_holder">
			<?=form_checkbox(array('name'=>'remember_me', 'value'=>'yes', 'checked'=>FALSE, 'id'=>'remember_me'))?> <label for="remember_me" class="mojo_remember_me"><?=$this->lang->line('remember_me')?></label>
			<?=form_button(array('name'=>'login', 'value'=>'login', 'content'=>$this->lang->line('login'), 'id'=>'mojo_submit', 'class'=>'button', 'type'=>'submit'))?>
		</p>

		<p><small><?=anchor('login/forgotten_password', $this->lang->line('forgotten_password'))?></small></p>

		<?= form_close()?>
	</div>
</div>
