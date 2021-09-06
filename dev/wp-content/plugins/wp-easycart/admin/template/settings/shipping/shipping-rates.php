<form action="admin.php?page=wp-easycart-settings&subpage=shipping-rates" method="POST" name="wpeasycart_admin_form" id="wpeasycart_admin_form_rates" novalidate="novalidate">
    
    <input type="hidden" name="ec_admin_form_action" value="save-shipping-rates" />
    
	<?php do_action( 'wpeasycart_admin_shipping_rates_success' ); ?>	
        
    <div class="ec_admin_shipping_rates_panel">
        
        <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_shipping_method_loader" ); ?>
        
        <div class="ec_admin_settings_label">
			<div class="dashicons-before dashicons-location-alt"></div>
			<span><?php _e( 'Shipping Method', 'wp-easycart' ); ?></span>
			<a href="<?php echo wp_easycart_admin( )->helpsystem->print_docs_url('settings', 'shipping-rates', 'shipping-method');?>" target="_blank" class="ec_help_icon_link">
				<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
			</a>
        	<?php echo wp_easycart_admin( )->helpsystem->print_vids_url('settings', 'shipping-rates', 'shipping-method');?>
		</div>
        <div class="ec_admin_settings_input">
            <span><?php _e( 'Select and Manage Your Shipping Method', 'wp-easycart' ); ?></span>
            <div>
            	<select name="ec_option_shipping_method" id="ec_option_shipping_method" onchange="toggle_shipping_method( true );" style="float:left;">
                	<?php do_action( 'wpeasycart_admin_shipping_rates_methods' ); ?>
				</select>
			</div>
        </div>

        <div class="ec_admin_settings_shipping_divider"></div>
        
      	<?php do_action( 'wpeasycart_admin_shipping_rates' ); ?>
        
        <div class="ec_admin_tax_spacer"></div>

	</div>
    
</form>
<script>
toggle_shipping_method( false );
</script>