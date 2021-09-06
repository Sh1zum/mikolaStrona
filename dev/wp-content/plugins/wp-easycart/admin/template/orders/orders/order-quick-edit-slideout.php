<?php
global $wpdb;
$order_status = $wpdb->get_results( "SELECT ec_orderstatus.status_id AS value, ec_orderstatus.order_status AS label FROM ec_orderstatus ORDER BY status_id ASC" );

?>
<div class="ec_admin_slideout_container" id="order_quick_edit_box" style="z-index:1028;">
    <div class="ec_admin_slideout_container_content">
        <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_order_quick_edit_display_loader" ); ?>
        <header class="ec_admin_slideout_container_content_header">
            <div class="ec_admin_slideout_container_content_header_inner">
                <h3><?php _e( 'Order Quick Edit', 'wp-easycart' ); ?></h3>
                <div class="ec_admin_slideout_close" onclick="wp_easycart_admin_close_slideout( 'order_quick_edit_box' );">
                    <div class="dashicons-before dashicons-no-alt"></div>
                </div>
            </div>
        </header>
        <div class="ec_admin_slideout_container_content_inner">
            <div class="ec_admin_slideout_container_input_row">
                <div class="ec_admin_slideout_container_simple_row">
                	<strong><?php _e( 'Order', 'wp-easycart' ); ?> <span id="ec_qe_order_id"></span></strong>
                </div>
            	<div class="ec_admin_slideout_container_simple_row">
                	<strong><?php _e( 'Shipping Address', 'wp-easycart' ); ?>:</strong><br /><br />
					<span id="ec_qe_order_shipping_address"></span>
                    <hr>
                	<strong><?php _e( 'Items', 'wp-easycart' ); ?>:</strong><br /><br />
					<span id="ec_qe_order_items"></span>
                </div>
            </div>
            <div class="ec_admin_slideout_container_input_row">
                <label for="ec_qe_order_status"><?php _e( 'Order Status', 'wp-easycart' ); ?></label>
                <div>
                    <select id="ec_qe_order_status" name="ec_qe_order_status" class="select2-basic">
                        <?php foreach( $order_status as $status ){ ?>
                        <option value="<?php echo $status->value; ?>"><?php echo $status->label; ?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
            <div class="ec_admin_slideout_container_input_row">
                <label for="ec_qe_order_use_expedited_shipping"><?php _e( 'Shipping Type', 'wp-easycart' ); ?></label>
                <div>
                    <select id="ec_qe_order_use_expedited_shipping" name="ec_qe_order_use_expedited_shipping" class="select2-basic">
                        <option value="0"><?php _e( 'Standard Shipping', 'wp-easycart' ); ?></option>
                        <option value="1"><?php _e( 'Expedite Shipping', 'wp-easycart' ); ?></option>
                    </select>
                </div>
            </div>
            <div class="ec_admin_slideout_container_input_row">
                <label for="ec_qe_order_shipping_type"><?php _e( 'Shipping Method', 'wp-easycart' ); ?></label>
                <div>
                    <input type="text" id="ec_qe_order_shipping_method" name="ec_qe_order_shipping_method" placeholder="<?php _e( 'Shipping Method', 'wp-easycart' ); ?>" />
                </div>
            </div>
            <div class="ec_admin_slideout_container_input_row">
                <label for="ec_qe_order_shipping_type"><?php _e( 'Shipping Carrier', 'wp-easycart' ); ?></label>
                <div>
                    <input type="text" id="ec_qe_order_shipping_carrier" name="ec_qe_order_shipping_carrier" placeholder="<?php _e( 'Shipping Carrier', 'wp-easycart' ); ?>" />
                </div>
            </div>
            <div class="ec_admin_slideout_container_input_row">
                <label for="ec_qe_order_shipping_type"><?php _e( 'Tracking Number', 'wp-easycart' ); ?></label>
                <div>
                    <input type="text" id="ec_qe_order_tracking_number" name="ec_qe_order_tracking_number" placeholder="<?php _e( 'Tracking Number', 'wp-easycart' ); ?>" />
                </div>
            </div>
            <div class="ec_admin_slideout_container_input_row">
                <label for="ec_qe_order_send_tracking_email"><?php _e( 'Send Shipped Email on Save?', 'wp-easycart' ); ?></label>
                <div>
                     <select id="ec_qe_order_send_tracking_email" name="ec_qe_order_send_tracking_email" class="select2-basic">
                        <option value="0"><?php _e( 'No, Do Not Send', 'wp-easycart' ); ?></option>
                        <option value="1"><?php _e( 'Yes, Send Shipped Email', 'wp-easycart' ); ?></option>
                    </select>
                </div>
            </div>
        </div>
        <footer class="ec_admin_slideout_container_content_footer">
            <div class="ec_admin_slideout_container_content_footer_inner">
                <div class="ec_admin_slideout_container_content_footer_inner_body">
                    <ul>
                        <li class="ec_admin_mobile_hide">
                            <button onclick="ec_admin_cancel_order_quick_edit( );">
                                <span><?php _e( 'Cancel', 'wp-easycart' ); ?></span>
                            </button>
                        </li>
                        <li>
                            <button onclick="ec_admin_save_order_quick_edit( );">
                                <span><?php _e( 'Save Changes', 'wp-easycart' ); ?></span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </footer>
    </div>
</div>
<script>jQuery( document.getElementById( 'order_quick_edit_box' ) ).appendTo( document.body );</script>