<?php $currency = new ec_currency( ); ?>

<form action="<?php echo $this->action; ?>"  method="POST" name="wpeasycart_admin_form" id="wpeasycart_admin_form" novalidate="novalidate">
<input type="hidden" name="ec_admin_form_action" value="<?php echo $this->form_action; ?>" />
<input type="hidden" name="pricepoint_id" value="<?php echo $this->pricepoint->pricepoint_id; ?>" />

<div class="ec_admin_settings_panel ec_admin_details_panel">
    <div class="ec_admin_important_numbered_list">
        <div class="ec_admin_flex_row">
            <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first">
            
                <div class="ec_admin_settings_label">
                    <div class="dashicons-before dashicons-products"></div>
                    <span><?php if( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ){ _e( 'ADD NEW PRICE POINT', 'wp-easycart' ); }else{ _e( 'EDIT PRICE POINT', 'wp-easycart' ); } ?></span>
                    <div class="ec_page_title_button_wrap">
                        <a href="<?php echo wp_easycart_admin( )->helpsystem->print_docs_url('settings', 'price-points', 'details');?>" target="_blank" class="ec_help_icon_link">
                            <div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
                        </a>
                        <?php echo wp_easycart_admin( )->helpsystem->print_vids_url('settings', 'manage-price-points', 'details');?>
                        <a href="<?php echo $this->action; ?>" class="ec_page_title_button"><?php _e( 'Cancel', 'wp-easycart' ); ?></a>
                        <input type="submit" value="<?php _e( 'Save', 'wp-easycart' ); ?>" onclick="return wpeasycart_admin_validate_form( )" class="ec_page_title_button">
                    </div>
                </div>
            
                <div class="ec_admin_settings_input ec_admin_settings_currency_section">
                	<div id="ec_admin_row_heading_title" class="ec_admin_row_heading_title"><?php _e( 'Price Point Setup', 'wp-easycart' ); ?><br></div>
                    <div id="ec_admin_row_heading_message" class="ec_admin_row_heading_message">
                        <p><?php _e( 'Price Points let you create a collection of products that exist within a price range and set those price points.  Here you can create greater than, less than, or between price range groups.  This is especially useful if you use our price point widget to allow customers to filter based on price points.', 'wp-easycart' ); ?></p>
                        <p><strong><?php _e( 'Note: There should only be one Less Than price point and one Greater Than price point. All the rest should be filled in and cover the range of zero (0.00) to infinity.', 'wp-easycart' ); ?></strong></p>
                    <p><strong><?php _e( 'Example', 'wp-easycart' ); ?>:</strong><br />
                    <?php echo $currency->get_currency_display( 0 ); ?> -  <?php echo $currency->get_currency_display( 100 ); ?> (<em><?php _e( 'Less  Than', 'wp-easycart' ); ?></em>)                    <br />
                    <?php echo $currency->get_currency_display( 100.01 ); ?> -  <?php echo $currency->get_currency_display( 500 ); ?> (<em><?php _e( 'In Between', 'wp-easycart' ); ?></em>)<br />
                    <?php echo $currency->get_currency_display( 500.01 ); ?> -  <?php echo $currency->get_currency_display( 1000 ); ?> (<em><?php _e( 'In Between', 'wp-easycart' ); ?></em>)<br />
                    <?php echo $currency->get_currency_display( 1000.01 ); ?> - <?php _e( 'Infinity', 'wp-easycart' ); ?> (<em><?php _e( 'Greater Than', 'wp-easycart' ); ?></em>)<br />
                    <br />
                    <hr />
                    <br />
                  </div>
					<?php do_action( 'wp_easycart_admin_pricepoint_details_basic_fields' ); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ec_admin_details_footer">
	<div class="ec_page_title_button_wrap">
    	<a href="<?php echo $this->action; ?>" class="ec_page_title_button"><?php _e( 'Cancel', 'wp-easycart' ); ?></a>
        <input type="submit" value="<?php _e( 'Save', 'wp-easycart' ); ?>" onclick="return wpeasycart_admin_validate_form( )" class="ec_page_title_button">
    </div>
</div>
</form>