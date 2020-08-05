		</div>
	</div>

	<script charset="utf-8" type="text/javascript" src="<?=base_url().index_page()?>/javascript/load/jquery"></script>
	<script charset="utf-8" type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#mojo_email").focus();

		jQuery("#import_site").change(function() {
			jQuery("#site_content").click();
		});

		jQuery("#db_type_mysql").click(function() {
			jQuery(".mysql_info").fadeIn();
		});

		jQuery("#db_type_sqlite").click(function() {
			jQuery(".mysql_info").fadeOut();
		});

		jQuery("#advanced_options_arrow").css({'cursor':'pointer'});

		// Form validation, this little snippet ensures the state of advanced options is remembered
		if (jQuery('#show_advanced').val() == 'y') {
			jQuery(".advanced_install_options").show();
			jQuery("#advanced_options_arrow").attr('src', '<?=site_url('assets/img/arrow_down.png')?>');
			jQuery("#show_advanced_options").text("<?=$this->lang->line('hide_advanced_options')?>");
		} else {
			jQuery(".advanced_install_options").hide();
			jQuery("#advanced_options_arrow").attr('src', '<?=site_url('assets/img/arrow_right.png')?>');
			jQuery("#show_advanced_options").text("<?=$this->lang->line('show_advanced_options')?>");
		}

		jQuery("#show_advanced_options, #advanced_options_arrow").click(function(e) {
			if (jQuery('#show_advanced').val() == 'n') {
				jQuery('#show_advanced').val('y');
				jQuery(".advanced_install_options").show();
				jQuery("#advanced_options_arrow").attr('src', '<?=site_url('assets/img/arrow_down.png')?>');
				jQuery("#show_advanced_options").text("<?=$this->lang->line('hide_advanced_options')?>");
			} else {
				jQuery('#show_advanced').val('n');
				jQuery(".advanced_install_options").hide();
				jQuery("#advanced_options_arrow").attr('src', '<?=site_url('assets/img/arrow_right.png')?>');
				jQuery("#show_advanced_options").text("<?=$this->lang->line('show_advanced_options')?>");
			}

			e.preventDefault();
		});
	});
	</script>

</body>
</html>