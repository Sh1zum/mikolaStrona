<div class="ec_admin_slideout_container" id="new_adv_optionitem_box">
    <div class="ec_admin_slideout_container_content">
        <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_new_adv_optionitem_display_loader" ); ?>
        <header class="ec_admin_slideout_container_content_header">
            <div class="ec_admin_slideout_container_content_header_inner">
                <h3><?php _e( 'Create an Option Item', 'wp-easycart' ); ?></h3>
                <div class="ec_admin_slideout_close" onclick="wp_easycart_admin_close_slideout( 'new_adv_optionitem_box' );">
                    <div class="dashicons-before dashicons-no-alt"></div>
                </div>
            </div>
        </header>
        <input type="hidden" id="ec_new_adv_optionitem_option_id" value="0" />
        <input type="hidden" id="ec_new_adv_optionitem_option_type" value="" />
        <input type="hidden" id="ec_new_adv_optionitem_sort_order" value="0" />
        <div class="ec_admin_slideout_container_content_inner">
            <div class="ec_admin_slideout_container_input_row">
                <label for="ec_new_adv_optionitem_name"><?php _e( 'Option Item Name', 'wp-easycart' ); ?></label>
                <div>
                    <input type="text" id="ec_new_adv_optionitem_name" name="ec_new_adv_optionitem_name" placeholder="<?php _e( 'Small', 'wp-easycart' ); ?>" />
                </div>
            </div>
            <div class="ec_admin_slideout_container_input_row">
                <label for="ec_new_adv_optionitem_model_number_extension"><?php _e( 'Model Number Extension (Optional - Extends Model Number in Cart)', 'wp-easycart' ); ?></label>
                <div>
                    <input type="text" id="ec_new_adv_optionitem_model_number_extension" name="ec_new_adv_optionitem_model_number_extension" placeholder="<?php _e( 'XL', 'wp-easycart' ); ?>" />
                </div>
            </div>
            <div class="ec_admin_slideout_container_input_row" id="ec_admin_adv_optionitem_initially_selected_row" style="display:none;">
                <input type="checkbox" id="ec_admin_adv_optionitem_initially_selected" name="ec_admin_adv_optionitem_initially_selected" value="1" />
                <label for="ec_admin_adv_optionitem_initially_selected"><?php _e( 'Initially Selected?', 'wp-easycart' ); ?></label>
            </div>
            <div class="ec_admin_slideout_container_input_row" id="ec_admin_adv_optionitem_allows_download_row" style="display:none;">
                <input type="checkbox" id="ec_admin_adv_optionitem_allows_download" name="ec_admin_adv_optionitem_allows_download" value="1" />
                <label for="ec_admin_adv_optionitem_allows_download"><?php _e( 'Option Allows Product Download?', 'wp-easycart' ); ?></label>
            </div>
            <div class="ec_admin_slideout_container_input_row" id="ec_admin_adv_optionitem_no_shipping_row" style="display:none;">
                <input type="checkbox" id="ec_admin_adv_optionitem_no_shipping" name="ec_admin_adv_optionitem_no_shipping" value="1" />
                <label for="ec_admin_adv_optionitem_no_shipping"><?php _e( 'Option Makes NO Shipping on Product', 'wp-easycart' ); ?></label>
            </div>
            <div class="ec_admin_slideout_container_input_row">
                <label for="ec_new_adv_optionitem_price"><?php _e( 'Price Adjustment Type', 'wp-easycart' ); ?></label>
                <div>
                    <select id="ec_new_adv_optionitem_price" name="ec_new_adv_optionitem_price" class="select2-basic" onchange="ec_admin_update_advanced_optionitem_price_fields( );">
                        <option value="0"><?php _e( 'No Price Adjustments', 'wp-easycart' ); ?></option>
                        <option value="basic_price"><?php _e( 'Basic Price Adjustment', 'wp-easycart' ); ?></option>
                        <option value="one_time_price"><?php _e( 'One-Time Price Adjustment', 'wp-easycart' ); ?></option>
                        <option value="override_price"><?php _e( 'Product Price Override', 'wp-easycart' ); ?></option>
                        <option value="multiplier_price"><?php _e( 'Product Price Multiplier', 'wp-easycart' ); ?></option>
                    </select>
                </div>
            </div>
            <div class="ec_admin_slideout_container_input_row" id="ec_new_adv_optionitem_price_adjustment_row" style="display:none;">
                <label for="ec_new_adv_optionitem_price_adjustment"><?php _e( 'Price Adjustment (+/-)', 'wp-easycart' ); ?></label>
                <div>
                    <input type="text" id="ec_new_adv_optionitem_price_adjustment" name="ec_new_adv_optionitem_price_adjustment" placeholder="0.00" />
                </div>
            </div>
            <div class="ec_admin_slideout_container_input_row">
                <label for="ec_new_adv_optionitem_weight"><?php _e( 'Weight Adjustment Type', 'wp-easycart' ); ?></label>
                <div>
                    <select id="ec_new_adv_optionitem_weight" name="ec_new_adv_optionitem_weight" class="select2-basic" onchange="ec_admin_update_advanced_optionitem_weight_fields( );">
                        <option value="0"><?php _e( 'No Weight Adjustments', 'wp-easycart' ); ?></option>
                        <option value="basic_weight"><?php _e( 'Basic Weight Adjustment', 'wp-easycart' ); ?></option>
                        <option value="one_time_weight"><?php _e( 'One-Time Weight Adjustment', 'wp-easycart' ); ?></option>
                        <option value="override_weight"><?php _e( 'Product Weight Override', 'wp-easycart' ); ?></option>
                        <option value="multiplier_weight"><?php _e( 'Product Weight Multiplier', 'wp-easycart' ); ?></option>
                    </select>
                </div>
            </div>
            <div class="ec_admin_slideout_container_input_row" id="ec_new_adv_optionitem_weight_adjustment_row" style="display:none;">
                <label for="ec_new_adv_optionitem_weight_adjustment"><?php _e( 'Weight Adjustment (+/-)', 'wp-easycart' ); ?></label>
                <div>
                    <input type="text" id="ec_new_adv_optionitem_weight_adjustment" name="ec_new_adv_optionitem_weight_adjustment" placeholder="0.00" />
                </div>
            </div>
        </div>
        <footer class="ec_admin_slideout_container_content_footer">
            <div class="ec_admin_slideout_container_content_footer_inner">
                <div class="ec_admin_slideout_container_content_footer_inner_body">
                    <ul>
                        <li>
                            <button onclick="ec_admin_save_new_adv_optionitem( true );">
                                <span><?php _e( 'Create and add another', 'wp-easycart' ); ?></span>
                            </button>
                        </li>
                        <li>
                            <button onclick="ec_admin_save_new_adv_optionitem( false );">
                                <span><?php _e( 'Create and Close', 'wp-easycart' ); ?></span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </footer>
    </div>
</div>
<script>jQuery( document.getElementById( 'new_adv_optionitem_box' ) ).appendTo( document.body );</script>