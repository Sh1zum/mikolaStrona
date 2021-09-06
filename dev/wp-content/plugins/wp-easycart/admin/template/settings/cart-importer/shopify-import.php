<div class="ec_admin_list_line_item ec_admin_demo_data_line">
            
	<?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_shopify_importer" ); ?>
    
    <div class="ec_admin_settings_label">
		<div class="dashicons-before dashicons-migrate"></div>
		<span><?php _e( 'Shopify Importer', 'wp-easycart' ); ?></span>
		<a href="<?php echo wp_easycart_admin( )->helpsystem->print_docs_url('settings', 'cart-importer', 'shopify' );?>" target="_blank" class="ec_help_icon_link">
			<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
		</a>
    	<?php echo wp_easycart_admin( )->helpsystem->print_vids_url('settings', 'cart-importer', 'shopify');?>
	</div>
	
    <div class="ec_admin_settings_input wp_easycart_admin_no_padding">
        <div id="wpeasycart_shopify_import_errors" class="ec_admin_message_error" style="margin-left:0px; display:none">
            <p><?php _e( 'There was an error connecting to your private app with Shopify, please consult our documentation for the correct steps to import. Contact us for help if you are still having trouble.', 'wp-easycart' ); ?></p>
        </div>
		
		<?php if( isset( $_GET['ec_success'] ) && $_GET['ec_success'] == "shopify-imported" ){ ?>
			<div  class="ec_save_success">
				<p><?php _e( 'Your Shopify store has been imported to WP EasyCart. There are no guarantees that all options have been imported, becuase Shopify offers so many product features. Please check over the data and manually add anything that may be missing.', 'wp-easycart' ); ?></p>
			</div>
		
		<?php }else if( isset( $_GET['ec_action'] ) && $_GET['ec_action'] == "import-shopify" ){	?>
			<div class="ec_save_success">
				<p><?php _e( 'Importing... Please Wait...', 'wp-easycart' ); ?></p>
			</div>
        <?php } ?>
        <div class="settings_list_items">
            <p><?php _e( 'Importing your data from your Shopify store is as simple as a click of a button! Although we do our best to import your data, not everything is transferrable or is known about all features in Shopify. The following information is imported by our system:', 'wp-easycart' ); ?></p>
            <ul>
                <li><?php _e( 'Shopify Product Categories', 'wp-easycart' ); ?></li>
                <li><?php _e( 'Shopify Attributes are imported as option sets to our system', 'wp-easycart' ); ?></li>
                <li><?php _e( 'Shopify Products are imported by the following rules:', 'wp-easycart' ); ?><ul>
                    <li><?php _e( 'Title, Description, Short Description, Price (Sale/Regular), Allow Comments, Taxable, Download, Service Item (Virtual), SKU, Download File, Download Limit, Download Expiry, Manage Stock, Stock Status, Stock Quantity', 'wp-easycart' ); ?></li>
                    <li><?php _e( 'Connects Imported Attributes (now option sets) to products the same as Woo has connected.', 'wp-easycart' ); ?></li>
                    <li><?php _e( 'Connects Product Categories to Products.', 'wp-easycart' ); ?></li>
                    <li><?php _e( 'If no SKU available, random model number is created.', 'wp-easycart' ); ?></li>
                    <li><?php _e( 'Product images are copied into our system from WordPress upload system.', 'wp-easycart' ); ?></li>
                    <li><?php _e( 'Limited to 5 images and first 5 of image gallery used.', 'wp-easycart' ); ?></li>
                    <li><?php _e( 'If no image gallery, uses featured image.', 'wp-easycart' ); ?></li>
                </ul></li>
                <li><?php _e( 'Shopify Users', 'wp-easycart' ); ?></li>
            </ul>

        </div>

        <div id="wpeasycart_shopify_input_fields">
            <?php wp_easycart_admin( )->load_toggle_group_text( 'wpeasycart_shopify_domain', 'wpeasycart_shopify_domain', '', 'Shopify Domain', 'This is a domain for your Shopify account that will give us access to import your data.', '', 'ec_admin_verify_shopify_token_row', true, false ); ?>

            <?php wp_easycart_admin( )->load_toggle_group_text( 'wpeasycart_shopify_api_key', 'ec_admin_verify_shopify_key', '', 'Shopify API Key', 'This is a token from your Shopify account that will give us access to import your data.', '', 'ec_admin_verify_shopify_token_row', true, false ); ?>

            <?php wp_easycart_admin( )->load_toggle_group_text( 'wpeasycart_shopify_api_password', 'ec_admin_verify_shopify_password', '', 'Shopify API Password', 'This is a token from your Shopify account that will give us access to import your data.', '', 'ec_admin_verify_shopify_token_row', true, false ); ?>
        </div>

        <div id="wpeasycart_shopify_import_progress_bar" style="display:none; margin:15px 0;">
            <div class="ec_admin_progress_bar"><div style="width:10%;"></div></div>
            <div class="ec_admin_process_status"><span><?php _e( 'Importing Products', 'wp-easycart' ); ?></span></div>
        </div>

        <div class="ec_admin_settings_input" style="padding:0px;">
            <input type="submit" id="wpeasycart_shopify_start_button" value="IMPORT Shopify DATA NOW" class="ec_admin_settings_simple_button" onclick="wpeasycart_start_shopify_import( ); return false;" style="float:left; width:auto; font-size:13px; text-transform:uppercase;" />
            <button type="button" class="ec_admin_settings_simple_button" style="font-weight:normal; padding:20px; border-radius:10px; font-size:18px; display:none; color:#AAA; border:none;" id="wpeasycart_shopify_processing_button"><?php _e( 'PROCESSING', 'wp-easycart' ); ?></button>
        </div>
    </div>
</div>