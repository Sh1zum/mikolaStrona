<div class="ec_admin_list_line_item ec_admin_demo_data_line" style="float:left;">
	
	<div class="ec_admin_settings_label"><div class="dashicons-before <?php echo $upgrade_icon; ?>"></div><span><?php echo $upgrade_title; ?></span></div>
	
	<div class="ec_admin_settings_input ec_admin_settings_tax_section">
		<span><?php echo $upgrade_subtitle; ?></span>
		<div><input type="checkbox" onchange="show_pro_required( ); return false;" value="1" /> <?php echo $upgrade_checkbox_label; ?></div>
    </div>
	
	<div class="ec_admin_settings_input">
		<input type="submit" class="ec_admin_settings_simple_button" value="<?php echo $upgrade_button_label; ?>" onclick="show_pro_required( ); return false;" />
	</div>
	
	<div class="ec_admin_tax_spacer"></div>
	
</div>