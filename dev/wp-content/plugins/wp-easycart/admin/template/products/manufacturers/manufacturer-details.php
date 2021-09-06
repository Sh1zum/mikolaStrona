<form action="<?php echo $this->action; ?>"  method="POST" name="wpeasycart_admin_form" id="wpeasycart_admin_form" novalidate="novalidate">
<input type="hidden" name="ec_admin_form_action" value="<?php echo $this->form_action; ?>" />
<input type="hidden" name="manufacturer_id" value="<?php echo $this->id; ?>" />

<div class="ec_admin_settings_panel ec_admin_details_panel">
    <div class="ec_admin_important_numbered_list">
        <div class="ec_admin_flex_row">
            <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first">
            	<div class="ec_admin_settings_label">
                    <div class="dashicons-before dashicons-products"></div>
                    <span><?php if( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ){ _e( 'ADD NEW MANUFACTURER', 'wp-easycart' ); }else{ _e('EDIT MANUFACTURER', 'wp-easycart' ); } ?></span>
                    <div class="ec_page_title_button_wrap">
                        <a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
                            <div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
                        </a>
                        <?php echo wp_easycart_admin( )->helpsystem->print_vids_url('products', 'manufacturers', 'details');?>
                        <a href="<?php echo $this->action; ?>" class="ec_page_title_button"><?php _e( 'Cancel', 'wp-easycart' ); ?></a>
                        <input type="submit" value="<?php _e( 'Save', 'wp-easycart' ); ?>" onclick="return wpeasycart_admin_validate_form( )" class="ec_page_title_button">
                    </div>
                </div>
            
                <div class="ec_admin_settings_input ec_admin_settings_currency_section">
                	<?php do_action( 'wp_easycart_admin_manufacturers_details_basic_fields' ); ?>
                </div>
            </div>
        </div>
		<div class="ec_admin_details_footer">
            <div class="ec_page_title_button_wrap">
                <a href="<?php echo $this->action; ?>" class="ec_page_title_button"><?php _e( 'Cancel', 'wp-easycart' ); ?></a>
                <input type="submit" value="<?php _e( 'Save', 'wp-easycart' ); ?>" onclick="return wpeasycart_admin_validate_form( )" class="ec_page_title_button">
            </div>
        </div>  
    </div>
</div>
</form>