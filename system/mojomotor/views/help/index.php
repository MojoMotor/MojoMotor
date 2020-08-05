<div class="mojo_diagnostic">
	<p><?=$this->lang->line('version').': '.$version?></p>
	<p><?=$this->lang->line('language').': '.ucwords($this->config->item('language'));?></p>
	
	<div id="mojo_copyright">
	<p><a href="http://mojomotor.com">MojoMotor.com</a></p>
	<p>&copy;<?=date('Y')?> <a href="http://ellislab.com">EllisLab, Inc.</a></p>
	</div>

</div>

<div class="mojo_diagnostic_supplement">
	<?=auto_typography($this->lang->line('help_verbiage1'))?>
	<?=auto_typography($this->lang->line('help_verbiage2'))?>
	<div class="mojo_clear"></div>
</div>

