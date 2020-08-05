<?php $this->load->view('mojo_header');?>

	<h2><?=$this->lang->line("login_greeting")?></h2>

	<?php
	echo form_open('setup/wizard');
	if ( ! $sqlite_support)
	{
		echo form_hidden('db_type', 'mysql');
	}
	?>
	<input type="hidden" name="show_advanced" value="<?=$show_advanced?>" id="show_advanced" />

	<?=validation_errors()?>

	<p id="mojo_install_error"><?=$this->lang->line('install_explanation')?></p>

	<table>
		<tbody>
			<tr>
				<th colspan="3"><br /><?=$this->lang->line('site_settings')?></th>
			</tr>
			<tr class="<?=alternator('odd', 'even')?>">
				<td class="labels"><label for="email"><?=$this->lang->line("email")?></label></td>
				<td class="form_elements"><?=form_input("email", set_value('email'), 'id="mojo_email" size="30" class="mojo_textbox"')?></td>
				<td class="description"><?=$this->lang->line("install_email_exp")?></td>
			</tr>
			<tr class="<?=alternator('odd', 'even')?>">
				<td class="labels"><label for="password"><?=$this->lang->line("password")?></label></td>
				<td><?=form_password("password", set_value('password'), 'id="password" size="30" class="mojo_textbox"')?></td>
				<td><?=$this->lang->line("password_exp")?></td>
			</tr>
			<tr class="<?=alternator('odd', 'even')?>">
				<td class="labels"><label for="site_title"><?=$this->lang->line("site_title")?></label></td>
				<td><?=form_input("site_title", set_value('site_title', ''), 'id="site_title" size="30" class="mojo_textbox" maxlength="100"')?></td>
				<td><?=$this->lang->line("site_title_exp")?></td>
			</tr>

			<tr class="<?=alternator('odd', 'even')?>">
				<td class="labels" rowspan="<?=$site_options_rowcount;?>"><?=$this->lang->line("site_content")?></label></td>
				<td><ul class="radio_options">
					<li><label><?=form_radio('site_content', 'blank_site', $site_content['blank_site'])?> <?=$this->lang->line('blank_site')?></label></li>
				</ul></td>
				<td>
					<?=$this->lang->line("site_content_exp_blank")?>
				</td>
			</tr>
			<?php if ($offer_default_site): ?>
			<tr>
				<td><ul class="radio_options">
					<li><label><?=form_radio('site_content', 'default_site', $site_content['default_site'])?> <?=$this->lang->line('default_site')?></label></li>
				</ul></td>
				<td>
					<?=$this->lang->line("site_content_exp_default")?>
				</td>
			</tr>
			<?php endif; ?>
			<?php if (count($importable_sites) > 0): ?>
			<tr>
				<td nowrap="nowrap"><ul class="radio_options">
					<li>
						<label><?=form_radio('site_content', 'import_site', $site_content['import_site'], 'id="site_content"')?> <?=$this->lang->line('import_site')?></label>
						 <?=form_dropdown('import_site', $importable_sites, '', 'id="import_site"')?>
					</li>
				</ul></td>
				<td>
					<?=$this->lang->line("site_content_exp_import")?>
				</td>
			</tr>
			<?php endif; ?>

			<?php
			// If the database.php file has already shown to have correct DB information in it, we won't
			// bother the user with entering that information.
			if ( ! $db_file_connected):
			?>

				<tr>
					<th colspan="3"><br /><?=$this->lang->line('database_settings')?></th>
				</tr>
				<tr class="<?=alternator('odd', 'even')?>">
					<td class="labels"><label for="db_host"><?=$this->lang->line("db_host")?></label></td>
					<td><?=form_input("db_host", set_value('db_host', 'localhost'), 'id="db_host" size="30" class="mojo_textbox" class="mojo_textbox"')?></td>
					<td><?=$this->lang->line("db_host_exp")?></td>
				</tr>
				<tr class="<?=alternator('odd', 'even')?>">
					<td class="labels"><label for="db_name"><?=$this->lang->line("db_name")?></label></td>
					<td><?=form_input("db_name", set_value('db_name', 'mojomotor'), 'id="db_name" size="30" class="mojo_textbox"')?></td>
					<td><?=$this->lang->line("db_name_exp")?></td>
				</tr>
				<tr class="mysql_info <?=alternator('odd', 'even')?>">
					<td class="labels"><label for="db_user"><?=$this->lang->line("db_user")?></label></td>
					<td><?=form_input("db_user", set_value('db_user'), 'id="db_user" size="30" class="mojo_textbox"')?></td>
					<td><?=$this->lang->line("db_user_exp")?></td>
				</tr>
				<tr class="mysql_info <?=alternator('odd', 'even')?>">
					<td class="labels"><label for="db_password"><?=$this->lang->line("db_password")?></label></td>
					<td><?=form_password("db_password", set_value('db_password'), 'id="db_password" size="30" class="mojo_textbox"')?></td>
					<td><?=$this->lang->line("db_password_exp")?></td>
				</tr>

			<?php endif; ?>

			<?php
			// If the database.php file has already shown to have correct DB information in it, we won't
			// bother the user with entering that information.
			if ( ! $db_file_connected):
			?>

			<tr>
				<th colspan="3"><a href="#" id="show_advanced_options"><?=$this->lang->line('advanced_install_options')?></a>  <img id="advanced_options_arrow" alt="" src="<?=site_url('assets/img/arrow_right.png')?>" /></th>
			</tr>

				<?php if ($sqlite_support): ?>
				<tr class="advanced_install_options <?=alternator('odd', 'even')?>">
					<td class="labels"><?=$this->lang->line("database_type")?></label></td>
					<td><ul class="radio_options">
						<li><?=form_radio('db_type', 'mysql', $db_type['mysql'], 'id="db_type_mysql"')?> <?=$this->lang->line('mysql_db')?></li>
						<li><?=form_radio('db_type', 'sqlite3', $db_type['sqlite'], 'id="db_type_sqlite"')?> <?=$this->lang->line('sqlite_db')?></li>
					</ul></td>
					<td><?=$this->lang->line("database_type_exp")?></td>
				</tr>
				<?php endif; ?>

				<tr class="advanced_install_options mysql_info <?=alternator('odd', 'even')?>">
					<td class="labels"><?=$this->lang->line("pconnect")?></label></td>
					<td><ul class="radio_options">
						<li><label><?=form_radio('pconnect', 'y', $pconnect['y'])?> <?=$this->lang->line('yes')?></label></li>
						<li><label><?=form_radio('pconnect', 'n', $pconnect['n'])?> <?=$this->lang->line('no')?></label></li>
					</ul></td>
					<td><?=$this->lang->line("pconnect_exp")?></td>
				</tr>
				<tr class="advanced_install_options">
					<td class="labels"><label for="db_prefix"><?=$this->lang->line("db_prefix")?></label></td>
					<td><?=form_input("db_prefix", set_value('db_prefix', 'mojo_'), 'id="db_prefix" size="30" class="mojo_textbox"')?></td>
					<td><?=$this->lang->line("db_prefix_exp")?></td>
				</tr>

			<?php endif; ?>

			<tr>
				<td colspan="3" class="break">
					<p><?=form_button(array('name'=>'submit', 'value'=>'submit', 'content'=>'&nbsp;<span>'.$this->lang->line('submit').'</span>&nbsp;', 'class'=>'button', 'type'=>'submit'))?></p>
				</td>
			</tr>

		</tbody>
	</table>

	<?= form_close()?>

<?php $this->load->view('mojo_setup_footer');?>