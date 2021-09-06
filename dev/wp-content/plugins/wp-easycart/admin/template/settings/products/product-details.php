<div class="ec_admin_list_line_item">
            
	<?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_display_loader" ); ?>
            
	<div class="ec_admin_settings_label">
		<div class="dashicons-before dashicons-analytics"></div>
		<span><?php _e( 'Product Details Display', 'wp-easycart' ); ?></span>
		<a href="<?php echo wp_easycart_admin( )->helpsystem->print_docs_url('settings', 'product-settings', 'product-details');?>" target="_blank" class="ec_help_icon_link">
			<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
		</a>
    	<?php echo wp_easycart_admin( )->helpsystem->print_vids_url('settings', 'product-settings', 'product-details');?>
	</div>
    
    <div class="ec_admin_settings_input ec_admin_settings_products_section">
		
		<?php wp_easycart_admin( )->load_toggle_group_text( 'ec_option_model_number_extension', 'ec_admin_save_product_text_setting', get_option( 'ec_option_model_number_extension' ), __( 'Model Number Extension', 'wp-easycart' ), __( 'This is the character injected between the main model number and option set extensions.  Default: "-"', 'wp-easycart' ), '-', 'ec_admin_model_number_extension_row', true, false, false ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_show_breadcrumbs', 'ec_admin_save_product_options', get_option( 'ec_option_show_breadcrumbs' ), __( 'Breadcrumbs', 'wp-easycart' ), __( 'Enabling this will show breadcrumbs with links within the product details.', 'wp-easycart' ) ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_show_magnification', 'ec_admin_save_product_options', get_option( 'ec_option_show_magnification' ), __( 'Image Magnification', 'wp-easycart' ), __( 'Enabling this will show an image hover box when hovering the product image.', 'wp-easycart' ) ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_show_large_popup', 'ec_admin_save_product_options', get_option( 'ec_option_show_large_popup' ), __( 'Image Lightbox', 'wp-easycart' ), __( 'Enabling this will show a larger image popup when the image is clicked.', 'wp-easycart' ) ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_show_model_number', 'ec_admin_save_product_options', get_option( 'ec_option_show_model_number' ), __( 'Model Number', 'wp-easycart' ), __( 'Enabling this will show the model number on the product details page.', 'wp-easycart' ) ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_show_categories', 'ec_admin_save_product_options', get_option( 'ec_option_show_categories' ), __( 'Categories', 'wp-easycart' ), __( 'Enabling this will show categories associated with the product with links.', 'wp-easycart' ) ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_show_manufacturer', 'ec_admin_save_product_options', get_option( 'ec_option_show_manufacturer' ), __( 'Manufacturer', 'wp-easycart' ), __( 'Enabling this will show the manufacturer with a link to all products made by the manufacturer.', 'wp-easycart' ) ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_show_stock_quantity', 'ec_admin_save_product_options', get_option( 'ec_option_show_stock_quantity' ), __( 'Stock Quantity (CAUTION ON IS RECOMMENDED)', 'wp-easycart' ), __( 'CAUTION, disabling this feature will disable quantity tracking site-wide and override all other options.', 'wp-easycart' ) ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_use_facebook_icon', 'ec_admin_save_product_options', get_option( 'ec_option_use_facebook_icon' ), __( 'Social Icon: Facebook', 'wp-easycart' ), __( 'This adds a Facebook sharing link for each product.', 'wp-easycart' ) ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_use_twitter_icon', 'ec_admin_save_product_options', get_option( 'ec_option_use_twitter_icon' ), __( 'Social Icon: Twitter', 'wp-easycart' ), __( 'This adds a Twitter sharing link for each product.', 'wp-easycart' ) ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_use_delicious_icon', 'ec_admin_save_product_options', get_option( 'ec_option_use_delicious_icon' ), __( 'Social Icon: Delicious', 'wp-easycart' ), __( 'This adds a Delicious sharing link for each product.', 'wp-easycart' ) ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_use_myspace_icon', 'ec_admin_save_product_options', get_option( 'ec_option_use_myspace_icon' ), __( 'Social Icon: MySpace', 'wp-easycart' ), __( 'This adds a MySpace sharing link for each product.', 'wp-easycart' ) ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_use_linkedin_icon', 'ec_admin_save_product_options', get_option( 'ec_option_use_linkedin_icon' ), __( 'Social Icon: LinkedIn', 'wp-easycart' ), __( 'This adds a LinkedIn sharing link for each product.', 'wp-easycart' ) ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_use_email_icon', 'ec_admin_save_product_options', get_option( 'ec_option_use_email_icon' ), __( 'Social Icon: Email', 'wp-easycart' ), __( 'This adds a Email sharing link for each product.', 'wp-easycart' ) ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_use_digg_icon', 'ec_admin_save_product_options', get_option( 'ec_option_use_digg_icon' ), __( 'Social Icon: Digg', 'wp-easycart' ), __( 'This adds a Digg sharing link for each product.', 'wp-easycart' ) ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_use_googleplus_icon', 'ec_admin_save_product_options', get_option( 'ec_option_use_googleplus_icon' ), __( 'Social Icon: Google+', 'wp-easycart' ), __( 'This adds a Google+ sharing link for each product.', 'wp-easycart' ) ); ?>
		
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_use_pinterest_icon', 'ec_admin_save_product_options', get_option( 'ec_option_use_pinterest_icon' ), __( 'Social Icon: Pinterest', 'wp-easycart' ), __( 'This adds a Pinterest sharing link for each product.', 'wp-easycart' ) ); ?>
		
    </div>
    
    <div class="ec_admin_settings_input">
        <input type="submit" class="ec_admin_settings_simple_button" onclick="return ec_admin_save_product_details_display_options( );" value="<?php _e( 'Save Options', 'wp-easycart' ); ?>" />
    </div>
    
</div>