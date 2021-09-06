<div class="ec_admin_list_line_item ec_admin_demo_data_line">
            
	<?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_square_importer" ); ?>
    
    <div class="ec_admin_settings_label">
        <div class="dashicons-before dashicons-migrate"></div>
        <span><?php _e( 'SquareUp Importer', 'wp-easycart' ); ?></span>
        <a href="<?php echo wp_easycart_admin( )->helpsystem->print_docs_url('settings', 'cart-importer', 'square');?>" target="_blank" class="ec_help_icon_link">
            <div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
        </a>
        <?php echo wp_easycart_admin( )->helpsystem->print_vids_url('settings', 'cart-importer', 'square');?>
    </div>
    
    <div class="ec_admin_settings_input ec_admin_settings_live_payment_section">
        
        <?php if( get_option( 'ec_option_payment_process_method' ) != 'square' || ( get_option( 'ec_option_square_access_token' ) == '' && get_option( 'ec_option_square_sandbox_access_token' ) != '' ) ){ ?>
        
        <div style="line-height:1.8em; text-align:center;"><strong><?php _e( 'Please visit the Settings -> Payment panel and setup Square first. Once you do, return here to import your products.', 'wp-easycart' ); ?></strong></div>
        
        <?php }else{ ?>
        
        <?php $square = new ec_square( ); if( !$square->has_inventory_scope( ) ){ ?>
        <?php $app_redirect_state = rand( 1000000, 9999999 ); ?>
        <div style="line-height:1.8em; text-align:left; font-weight:bold; background:red; color:white; padding:20px; margin-bottom:20px;"><?php _e( 'Inventory Authorization Missing! You need additional permissions to run this process with inventory data included.', 'wp-easycart' ); ?> <a href="https://connect.wpeasycart.com/<?php echo ( get_option( 'ec_option_square_is_sandbox' ) ) ? 'square-sandbox' : 'square-v2'; ?>/?url=<?php echo urlencode( admin_url( ) . '?ec_admin_form_action=handle-square' ); ?>&state=<?php echo $app_redirect_state; ?>" style="color:#feffdd;"><?php _e( 'Click here', 'wp-easycart' ); ?></a> <?php _e( 'to add those permissions.', 'wp-easycart' ); ?></div>
        
        <?php }?>
        
        <div style="line-height:1.8em; text-align:center;"><?php _e( 'This process may take a while. We will process just a few items at a time to limit problems on slower servers. If you experience a problem, you may need to increase your server max execution time.', 'wp-easycart' ); ?></div>
        
		<div style="text-align:center; font-weight:bold; margin:25px 0 0;"><input type="checkbox" id="wpeasycart_square_sync_matches" value="1" /> <?php _e( 'Sync already imported items from Square -> EasyCart?', 'wp-easycart' ); ?></div>
        
		<div class="ec_admin_settings_input" style="text-align:center; margin:15px 0;">
            <input type="submit" id="wpeasycart_square_start_button" value="<?php _e( 'IMPORT PRODUCTS', 'wp-easycart' ); ?>" class="ec_admin_settings_simple_button" onclick="wpeasycart_start_square_import( ); return false;" style="font-weight:normal; padding:20px; border-radius:10px; font-size:18px;" />
            <button type="button" class="ec_admin_settings_simple_button" style="font-weight:normal; padding:20px; border-radius:10px; font-size:18px; display:none; color:#AAA; border:none;" id="wpeasycart_square_processing_button"><?php _e( 'PROCESSING', 'wp-easycart' ); ?></button>
        </div>
        
        <div id="wpeasycart_square_import_progress_bar" style="display:none">
            <div class="ec_admin_progress_bar"><div style="width:10%;"></div></div>
            <div class="ec_admin_process_status"><span><?php _e( 'Importing Modifiers', 'wp-easycart' ); ?></span></div>
		</div>
        <?php }?>
        
    </div>
</div>