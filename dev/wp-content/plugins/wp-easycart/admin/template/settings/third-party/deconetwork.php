<div class="ec_admin_list_line_item ec_admin_demo_data_line">
            
	<?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_deconetwork_loader" ); ?>
    
    <div class="ec_admin_settings_label">
        <div class="dashicons-before dashicons-admin-generic"></div>
        <span><?php _e( 'DecoNetwork Setup', 'wp-easycart' ); ?></span>
        <a href="<?php echo wp_easycart_admin( )->helpsystem->print_docs_url('settings', 'third-party', 'deconetwork');?>" target="_blank" class="ec_help_icon_link">
            <div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
        </a>
        <?php echo wp_easycart_admin( )->helpsystem->print_vids_url('settings', 'third-party', 'deconetwork');?>
    </div>
    
    <div class="ec_admin_settings_input ec_admin_settings_live_payment_section">
        <div><?php _e( 'DecoNetwork URL', 'wp-easycart' ); ?> <input name="ec_option_deconetwork_url" id="ec_option_deconetwork_url" type="text" value="<?php echo stripslashes( get_option( 'ec_option_deconetwork_url' ) ); ?>" /></div>
        <div><?php _e( 'DecoNetwork Order Password', 'wp-easycart' ); ?> <input name="ec_option_deconetwork_password" id="ec_option_deconetwork_password" type="text" value="<?php echo stripslashes( get_option( 'ec_option_deconetwork_password' ) );  ?>" /></div>
        <div class="settings_list_items"><p><?php _e( 'Note: You must complete the following steps in your DecoNetwork settings panel to offer these product types:', 'wp-easycart' ); ?></p>
        <ul>
            <li><?php _e( 'External Cart Integration must be enabled by going to your DecoNetwork Manage Store -> Website Settings -> API Settings.', 'wp-easycart' ); ?></li>
            <li><?php _e( 'Add the Add to Cart Callback URL:', 'wp-easycart' ); ?> <?php echo get_permalink( get_option( 'ec_option_cartpage' ) ); ?></li>
            <li><?php _e( 'Add the Cancel Callback URL:', 'wp-easycart' ); ?> <?php echo get_permalink( get_option( 'ec_option_storepage' ) ); ?></li>
            <li><?php _e( 'Create a Custom Order Commit URL. This is a custom value DIFFERENT from your account password AND should be entered in the field provided above!', 'wp-easycart' ); ?></li>
            <li><?php _e( 'Additional note, the hard part of setting this up tends to be finding the product id (not product code!) and is available if you go to the product on your DecoNetowrk site, look at the URL, and where it says n=xxxxxxx, the xxxxxxx is the id you need to enter in the EasyCart system when setting up a product.', 'wp-easycart' ); ?></li>
            <li><?php _e( 'It is recommended that you turn off email correspondence from your DecoNetwork site. This can be done by going to Manage Store -> Website Settings -> Correspondence Settings and check the box to force correspondence from the WP EasyCart instead of your DecoNetwork site.', 'wp-easycart' ); ?></li>
        </ul>
        </div>
       	
        <div class="ec_admin_settings_input">
            <input type="submit" class="ec_admin_settings_simple_button" onclick="return ec_admin_save_deconetwork_settings( );" value="<?php _e( 'Save Setup', 'wp-easycart' ); ?>" />
        </div>
    </div>
</div>