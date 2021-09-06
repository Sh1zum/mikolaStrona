<form action="<?php echo $this->action; ?>"  method="POST" id="wpeasycart_admin_form" name="wpeasycart_admin_form" novalidate="novalidate">
<input type="hidden" name="ec_admin_form_action" value="<?php echo $this->form_action; ?>" />
<input type="hidden" name="subscriber_id" value="<?php echo $this->subscriber->subscriber_id; ?>" />

<div class="ec_admin_settings_panel ec_admin_details_panel">
    <div class="ec_admin_important_numbered_list">
        <div class="ec_admin_flex_row">
            <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first">
            
                <div class="ec_admin_settings_label">
                    <div class="dashicons-before dashicons-admin-users"></div>
                    <span><?php if( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ){ _e( 'ADD NEW SUBSCRIBER', 'wp-easycart' ); }else{ _e( 'EDIT SUBSCRIBER', 'wp-easycart' ); } ?></span>
                    <div class="ec_page_title_button_wrap">
                        <a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
                            <div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
                        </a>
                        <a href="<?php echo $this->action; ?>" class="ec_page_title_button"><?php _e( 'Cancel', 'wp-easycart' ); ?></a>
                        <input type="submit" value="<?php _e( 'Save', 'wp-easycart' ); ?>" onclick="return wpeasycart_admin_validate_form( )" class="ec_page_title_button">
                    </div>
                </div>
            
                <div class="ec_admin_settings_input ec_admin_settings_currency_section">
                	<div id="ec_admin_row_heading_title" class="ec_admin_row_heading_title"><?php _e( 'Subscriber Setup', 'wp-easycart' ); ?><br></div>
                    <div id="ec_admin_row_heading_message" class="ec_admin_row_heading_message"><p><?php _e( 'Subscribers are customers who choose to be added to your newsletter or subscriber list during EasyCart account creation or during order checkout.  From here you can export user names and emails and import into any number of mailing services or wordpress newsletter programs such as MyMail, MailChimp, and others.', 'wp-easycart' ); ?></p></div>
					<?php do_action( 'wp_easycart_admin_subscribers_details_basic_fields' ); ?>
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