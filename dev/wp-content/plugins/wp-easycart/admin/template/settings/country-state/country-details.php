<form action="<?php echo $this->action; ?>"  method="POST" name="wpeasycart_admin_form" id="wpeasycart_admin_form" novalidate="novalidate">
    <input type="hidden" name="ec_admin_form_action" value="<?php echo $this->form_action; ?>" />
    <input type="hidden" name="id_cnt" value="<?php echo $this->country->id_cnt; ?>" />

    <div class="ec_admin_settings_panel ec_admin_details_panel">
        <div class="ec_admin_important_numbered_list">
            <div class="ec_admin_flex_row">
                <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first">

                    <div class="ec_admin_settings_label">
                        <div class="dashicons-before dashicons-products"></div>
                        <span><?php if( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ){ _e( 'ADD NEW', 'wp-easycart' ); }else{ _e( 'EDIT', 'wp-easycart' ); } ?> <?php _e( 'COUNTRY', 'wp-easycart' ); ?></span>
                        <div class="ec_page_title_button_wrap">
                            <a href="<?php echo wp_easycart_admin( )->helpsystem->print_docs_url('settings', 'countries', 'details');?>" target="_blank" class="ec_help_icon_link">
                                <div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
                            </a>
                            <?php echo wp_easycart_admin( )->helpsystem->print_vids_url('settings', 'manage-countries', 'details');?>
                            <a href="<?php echo $this->action; ?>" class="ec_page_title_button"><?php _e( 'Cancel', 'wp-easycart' ); ?></a>
                            <input type="submit" value="<?php _e( 'Save', 'wp-easycart' ); ?>" onclick="return wpeasycart_admin_validate_form( )" class="ec_page_title_button">
                        </div>
                    </div>

                    <div class="ec_admin_settings_input ec_admin_settings_currency_section">
                        <div id="ec_admin_row_heading_title" class="ec_admin_row_heading_title"><?php _e( 'Country Setup', 'wp-easycart' ); ?><br></div>
                        <div id="ec_admin_row_heading_message" class="ec_admin_row_heading_message"><p><?php _e( 'You can control which countries appear in your checkout by editing them here.  Be sure to use the proper ISO2 and ISO3 abbreviations for the countries as many shipping and payment gateways may require this information.', 'wp-easycart' ); ?></p></div>
                        <?php do_action( 'wp_easycart_admin_country_details_basic_fields' ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>