<div class="ec_admin_list_line_item ec_admin_demo_data_line">
            
	<?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_order_statuses_settings_loader" ); ?>
    
    <div class="ec_admin_settings_label">
		<div class="dashicons-before dashicons-feedback"></div>
		<span><?php _e( 'Order Statuses', 'wp-easycart' ); ?></span>
		<a href="<?php echo wp_easycart_admin( )->helpsystem->print_docs_url('settings', 'checkout', 'order-statuses');?>" target="_blank" class="ec_help_icon_link">
			<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
		</a>
    	<?php echo wp_easycart_admin( )->helpsystem->print_vids_url('settings', 'checkout', 'form-settings');?>
	</div>
    <div class="ec_admin_settings_input ec_admin_settings_live_payment_section">
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th width="50%"><?php _e( 'Status', 'wp-easycart' ); ?></th>
                    <th style="text-align:center;"><?php _e( 'Payment Complete?', 'wp-easycart' ); ?></th>
                    <th style="text-align:right;"><?php _e( 'Delete', 'wp-easycart' ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php global $wpdb; ?>
            <?php $order_statuses = $wpdb->get_results( "SELECT * FROM ec_orderstatus WHERE is_archieved = 0 ORDER BY status_id ASC" ); ?>
            <?php foreach( $order_statuses as $order_status ){ ?>
                <tr id="wpeasycart_orderstatus_row_<?php echo $order_status->status_id; ?>">
                    <td>
						<input type="text" style="margin-top:0px;" id="wpeasycart_orderstatus_status_<?php echo $order_status->status_id; ?>" class="wpeasycart_orderstatus_id_edit" data-id="<?php echo $order_status->status_id; ?>" value="<?php echo htmlspecialchars( stripslashes( $order_status->order_status ) ); ?>" />
					</td>
                    <td style="text-align:center;">
						<input type="checkbox"<?php echo ( $order_status->status_id > 19 ) ? ' class="wpeasycart_orderstatus_approved_edit"' : ''; ?> id="wpeasycart_orderstatus_approved_<?php echo $order_status->status_id; ?>" data-id="<?php echo $order_status->status_id; ?>" value="1"<?php echo ( $order_status->status_id <= 19 ) ? ' readonly onclick="return false;"' : ''; ?><?php echo ( $order_status->is_approved ) ? ' checked="checked"' : ''; ?> />
					</td>
                    <td style="text-align:right;">
						<?php echo ( $order_status->status_id > 19 ) ? '<input type="button" class="ec_admin_order_edit_button" onclick="wpeasycart_archieve_orderstatus( ' . $order_status->status_id . ' );" value="' . __( 'Delete', 'wp-easycart' ) . '" />' : '' . __( 'Locked', 'wp-easycart' ) . ''; ?>
					</td>
                </tr>
            <?php }?>
                <tr id="wpeasycart_orderstatus_row_add">
                    <td><input type="text" style="margin-top:0px;" id="wpeasycart_orderstatus_add" value="" placeholder="<?php _e( 'Enter New Status', 'wp-easycart' ); ?>" /></td>
                    <td style="text-align:center;"><input type="checkbox" id="wpeasycart_orderstatus_approved_add" value="1" /></td>
                    <td style="text-align:right;"><input type="button" class="ec_admin_order_edit_button" onclick="return wpeasycart_add_orderstatus( );" value="<?php _e( '+ADD', 'wp-easycart' ); ?>" /></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>