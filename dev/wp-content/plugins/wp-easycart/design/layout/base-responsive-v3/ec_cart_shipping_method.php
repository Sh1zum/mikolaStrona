<?php $this->display_page_two_form_start( ); ?>
<div class="ec_cart_left">
	<div class="ec_cart_header ec_top">
        <?php echo $GLOBALS['language']->get_text( 'cart_shipping_method', 'cart_shipping_method_title' ); ?>
    </div>
    <div class="ec_cart_error_row" id="ec_cart_billing_country_error">
        <?php echo $GLOBALS['language']->get_text( 'cart_shipping_method', 'cart_shipping_method_please_select_one' ); ?>
    </div>
    <div class="ec_cart_input_row">
        <label for="ec_cart_billing_country"><?php echo $GLOBALS['language']->get_text( 'cart_shipping_information', 'cart_shipping_information_title' ); ?></label>
        <?php $this->ec_cart_display_shipping_methods( $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_standard' ),$GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_express' ), "RADIO" ); ?>
    </div>
</div>

<div class="ec_cart_right">
    
    <div class="ec_cart_header ec_top">
        <?php echo $GLOBALS['language']->get_text( 'cart', 'your_cart_title' ); ?>
    </div>
    
    <?php for( $cartitem_index = 0; $cartitem_index<count( $this->cart->cart ); $cartitem_index++ ){ ?>
    
    <div class="ec_cart_price_row ec_cart_price_row_cartitem_<?php echo $cartitem_index; ?>">
        <div class="ec_cart_price_row_label"><?php $this->cart->cart[$cartitem_index]->display_title( ); ?><?php if( $this->cart->cart[$cartitem_index]->grid_quantity > 1 ){ ?> x <?php echo $this->cart->cart[$cartitem_index]->grid_quantity; ?><?php }else if( $this->cart->cart[$cartitem_index]->quantity > 1 ){ ?> x <?php echo $this->cart->cart[$cartitem_index]->quantity; ?><?php }?>
        
        <?php if( $this->cart->cart[$cartitem_index]->stock_quantity <= 0 && $this->cart->cart[$cartitem_index]->allow_backorders ){ ?>
        <div class="ec_cart_backorder_date"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_backordered' ); ?><?php if( $this->cart->cart[$cartitem_index]->backorder_fill_date != "" ){ ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_backorder_until' ); ?> <?php echo $this->cart->cart[$cartitem_index]->backorder_fill_date; ?><?php }?></div>
        <?php }?>
        <?php if( $this->cart->cart[$cartitem_index]->optionitem1_name ){ ?>
        <dl>
            <dt><?php echo $this->cart->cart[$cartitem_index]->optionitem1_name; ?><?php if( $this->cart->cart[$cartitem_index]->optionitem1_price > 0 ){ ?> ( +<?php echo $GLOBALS['currency']->get_currency_display( $this->cart->cart[$cartitem_index]->optionitem1_price ); ?> )<?php }else if( $this->cart->cart[$cartitem_index]->optionitem1_price < 0 ){ ?> ( <?php echo $GLOBALS['currency']->get_currency_display( $this->cart->cart[$cartitem_index]->optionitem1_price ); ?> )<?php } ?></dt>
        
        <?php if( $this->cart->cart[$cartitem_index]->optionitem2_name ){ ?>
            <dt><?php echo $this->cart->cart[$cartitem_index]->optionitem2_name; ?><?php if( $this->cart->cart[$cartitem_index]->optionitem2_price > 0 ){ ?> ( +<?php echo $GLOBALS['currency']->get_currency_display( $this->cart->cart[$cartitem_index]->optionitem2_price ); ?> )<?php }else if( $this->cart->cart[$cartitem_index]->optionitem2_price < 0 ){ ?> ( <?php echo $GLOBALS['currency']->get_currency_display( $this->cart->cart[$cartitem_index]->optionitem2_price ); ?> )<?php } ?></dt>
        <?php }?>
        
        <?php if( $this->cart->cart[$cartitem_index]->optionitem3_name ){ ?>
            <dt><?php echo $this->cart->cart[$cartitem_index]->optionitem3_name; ?><?php if( $this->cart->cart[$cartitem_index]->optionitem3_price > 0 ){ ?> ( +<?php echo $GLOBALS['currency']->get_currency_display( $this->cart->cart[$cartitem_index]->optionitem3_price ); ?> )<?php }else if( $this->cart->cart[$cartitem_index]->optionitem3_price < 0 ){ ?> ( <?php echo $GLOBALS['currency']->get_currency_display( $this->cart->cart[$cartitem_index]->optionitem3_price ); ?> )<?php } ?></dt>
        <?php }?>
        
        <?php if( $this->cart->cart[$cartitem_index]->optionitem4_name ){ ?>
            <dt><?php echo $this->cart->cart[$cartitem_index]->optionitem4_name; ?><?php if( $this->cart->cart[$cartitem_index]->optionitem4_price > 0 ){ ?> ( +<?php echo $GLOBALS['currency']->get_currency_display( $this->cart->cart[$cartitem_index]->optionitem4_price ); ?> )<?php }else if( $this->cart->cart[$cartitem_index]->optionitem4_price < 0 ){ ?> ( <?php echo $GLOBALS['currency']->get_currency_display( $this->cart->cart[$cartitem_index]->optionitem4_price ); ?> )<?php } ?></dt>
        <?php }?>
        
        <?php if( $this->cart->cart[$cartitem_index]->optionitem5_name ){ ?>
            <dt><?php echo $this->cart->cart[$cartitem_index]->optionitem5_name; ?><?php if( $this->cart->cart[$cartitem_index]->optionitem5_price > 0 ){ ?> ( +<?php echo $GLOBALS['currency']->get_currency_display( $this->cart->cart[$cartitem_index]->optionitem5_price ); ?> )<?php }else if( $this->cart->cart[$cartitem_index]->optionitem5_price < 0 ){ ?> ( <?php echo $GLOBALS['currency']->get_currency_display( $this->cart->cart[$cartitem_index]->optionitem5_price ); ?> )<?php } ?></dt>
        <?php }?>
        </dl>
        <?php }?>
        
        <?php if( $this->cart->cart[$cartitem_index]->use_advanced_optionset ){ ?>
        <dl>
        <?php foreach( $this->cart->cart[$cartitem_index]->advanced_options as $advanced_option_set ){ ?>
            <?php if( $advanced_option_set->option_type == "grid" ){ ?>
            <dt><?php echo $advanced_option_set->optionitem_name; ?>: <?php echo $advanced_option_set->optionitem_value; ?><?php if( $advanced_option_set->optionitem_price > 0 ){ echo ' (+' . $GLOBALS['currency']->get_currency_display( $advanced_option_set->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')'; }else if( $advanced_option_set->optionitem_price < 0 ){ echo ' (' . $GLOBALS['currency']->get_currency_display( $advanced_option_set->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')'; }else if( $advanced_option_set->optionitem_price_onetime > 0 ){ echo ' (+' . $GLOBALS['currency']->get_currency_display( $advanced_option_set->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')'; }else if( $advanced_option_set->optionitem_price_onetime < 0 ){ echo ' (' . $GLOBALS['currency']->get_currency_display( $advanced_option_set->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')'; }else if( $advanced_option_set->optionitem_price_override > -1 ){ echo ' (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $advanced_option_set->optionitem_price_override ) . ')'; } ?></dt>
            <?php }else if( $advanced_option_set->option_type == "dimensions1" || $advanced_option_set->option_type == "dimensions2" ){ ?>
            <strong><?php echo $advanced_option_set->option_label; ?>:</strong><br /><?php $dimensions = json_decode( $advanced_option_set->optionitem_value ); if( count( $dimensions ) == 2 ){ echo $dimensions[0]; if( !get_option( 'ec_option_enable_metric_unit_display' ) ){ echo "\""; } echo " x " . $dimensions[1]; if( !get_option( 'ec_option_enable_metric_unit_display' ) ){ echo "\""; } }else if( count( $dimensions ) == 4 ){ echo $dimensions[0] . " " . $dimensions[1] . "\" x " . $dimensions[2] . " " . $dimensions[3] . "\""; } ?><br />
            
            <?php }else{ ?>
            <dt><?php echo $advanced_option_set->option_label; ?>: <?php echo htmlspecialchars( $advanced_option_set->optionitem_value, ENT_QUOTES ); ?><?php if( $advanced_option_set->optionitem_price > 0 ){ echo ' (+' . $GLOBALS['currency']->get_currency_display( $advanced_option_set->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')'; }else if( $advanced_option_set->optionitem_price < 0 ){ echo ' (' . $GLOBALS['currency']->get_currency_display( $advanced_option_set->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')'; }else if( $advanced_option_set->optionitem_price_onetime > 0 ){ echo ' (+' . $GLOBALS['currency']->get_currency_display( $advanced_option_set->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')'; }else if( $advanced_option_set->optionitem_price_onetime < 0 ){ echo ' (' . $GLOBALS['currency']->get_currency_display( $advanced_option_set->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')'; }else if( $advanced_option_set->optionitem_price_override > -1 ){ echo ' (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $advanced_option_set->optionitem_price_override ) . ')'; } ?></dt>
            <?php } ?>
        <?php }?>
        </dl>
        <?php }?>
        
        <?php if( $this->cart->cart[$cartitem_index]->is_giftcard ){ ?>
        <dl>
        <dt><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_gift_card_recipient_name' ); ?>: <?php echo htmlspecialchars( $this->cart->cart[$cartitem_index]->gift_card_to_name, ENT_QUOTES ); ?></dt>
        <dt><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_gift_card_recipient_email' ); ?>: <?php echo htmlspecialchars( $this->cart->cart[$cartitem_index]->gift_card_email, ENT_QUOTES ); ?></dt>
        <dt><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_gift_card_sender_name' ); ?>: <?php echo htmlspecialchars( $this->cart->cart[$cartitem_index]->gift_card_from_name, ENT_QUOTES ); ?></dt>
        <dt><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_gift_card_message' ); ?>: <?php echo htmlspecialchars( $this->cart->cart[$cartitem_index]->gift_card_message, ENT_QUOTES ); ?></dt>
        </dl>
        <?php }?>
        
        <?php if( $this->cart->cart[$cartitem_index]->is_deconetwork ){ ?>
        <dl>
        <dt><?php echo $this->cart->cart[$cartitem_index]->deconetwork_options; ?></dt>
        <dt><?php echo "<a href=\"https://" . get_option( 'ec_option_deconetwork_url' ) . $this->cart->cart[$cartitem_index]->deconetwork_edit_link . "\">" . $GLOBALS['language']->get_text( 'cart', 'deconetwork_edit' ) . "</a>"; ?></dt>
        </dl>
        <?php }?>
        
        <?php do_action( 'wp_easycart_cartitem_post_optionitems', $this->cart->cart[$cartitem_index] ); ?>
        
        </div>
        <div class="ec_cart_price_row_total" id="ec_cart_subtotal"><?php echo $this->cart->cart[$cartitem_index]->get_total( ); ?></div>
    </div>
    
    <?php }?>
    
    <?php if( get_option( 'ec_option_show_coupons' ) ){ ?>
    <div class="ec_cart_header">
        <?php echo $GLOBALS['language']->get_text( 'cart_coupons', 'cart_coupon_title' )?>
    </div>
    <div class="ec_cart_error_message" id="ec_coupon_error"<?php if( $this->is_coupon_expired( ) ){ ?> style="display:block;"<?php }?>><?php echo $this->get_coupon_expiration_note( ); ?></div>
    <div class="ec_cart_success_message" id="ec_coupon_success"<?php if( isset( $this->coupon ) && !$this->is_coupon_expired( ) ){?> style="display:block;"<?php }?>><?php if( isset( $this->coupon ) ){ if( $this->discount->coupon_matches <= 0 ){ echo $GLOBALS['language']->get_text( 'cart_coupons', 'coupon_not_applicable' ); }else{ echo $GLOBALS['language']->convert_text( $this->coupon->message ); } } ?></div>
    <div class="ec_cart_input_row">
        <input type="text" name="ec_coupon_code" id="ec_coupon_code" value="<?php if( isset( $this->coupon ) ){ echo $this->coupon_code; } ?>" placeholder="<?php echo $GLOBALS['language']->get_text( 'cart_coupons', 'cart_enter_coupon' )?>" />
    </div>
    <div class="ec_cart_button_row">
        <div class="ec_cart_button" id="ec_apply_coupon" onclick="ec_apply_coupon( );"><?php echo $GLOBALS['language']->get_text( 'cart_coupons', 'cart_apply_coupon' ); ?></div>
        <div class="ec_cart_button_working" id="ec_applying_coupon"><?php echo $GLOBALS['language']->get_text( 'cart', 'cart_please_wait' )?></div>
    </div>
    <?php }?>
    <?php if( get_option( 'ec_option_show_giftcards' ) ){ ?>
    <div class="ec_cart_header">
        <?php echo $GLOBALS['language']->get_text( 'cart_coupons', 'cart_gift_card_title' )?>
    </div>
    <div class="ec_cart_error_message" id="ec_gift_card_error"></div>
    <div class="ec_cart_success_message" id="ec_gift_card_success"<?php if( $this->gift_card != "" ){?> style="display:block;"<?php }?>><?php if( $this->gift_card != "" ){ echo $this->giftcard->message; } ?></div>
    <div class="ec_cart_input_row">
        <input type="text" name="ec_gift_card" id="ec_gift_card" value="<?php echo $this->gift_card; ?>" placeholder="<?php echo $GLOBALS['language']->get_text( 'cart_coupons', 'cart_enter_gift_code' )?>" />
    </div>
    <div class="ec_cart_button_row">
        <div class="ec_cart_button" id="ec_apply_gift_card" onclick="ec_apply_gift_card( );"><?php echo $GLOBALS['language']->get_text( 'cart_coupons', 'cart_redeem_gift_card' ); ?></div>
        <div class="ec_cart_button_working" id="ec_applying_gift_card"><?php echo $GLOBALS['language']->get_text( 'cart', 'cart_please_wait' )?></div>
    </div>
    <?php }?>
    
    <div class="ec_cart_header">
        <?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_title' ); ?>
    </div>
    <div class="ec_cart_price_row ec_cart_price_row_subtotal">
        <div class="ec_cart_price_row_label"><?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_subtotal' )?></div>
        <div class="ec_cart_price_row_total" id="ec_cart_subtotal"><?php echo $this->get_subtotal( ); ?></div>
    </div>
    <?php if( get_option( 'ec_option_enable_tips' ) ){ ?>
    <?php $default_tips = explode( ',', get_option( 'ec_option_default_tips' ) ); ?>
    <div calss="ec_cart_price_row ec_cart_tips">

        <div class="ec_cart_price_row_label"><?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tip' ); ?></div>
        <div class="ec_cart_price_row_total" id="ec_cart_tip"><?php echo $this->get_tip_total( ); ?></div>
        <ul class="ec_cart_tip_items">
            <?php foreach( $default_tips as $tip_rate ){ ?>
            <li class="ec_cart_tip_item<?php echo ( (float) $GLOBALS['ec_cart_data']->cart_data->tip_rate == (float) $tip_rate ) ? ' ec_tip_selected' : ''; ?>">
                <a href="" onclick="wpeasycart_update_tip( '<?php echo $tip_rate; ?>' ); jQuery( this ).parent( ).addClass( 'ec_tip_selected' ); jQuery( document.getElementById( 'ec_cart_tip' ) ).html( jQuery( this ).find( 'span' ).html( ) ); jQuery( document.getElementById( 'ec_cart_tip_custom' ) ).val( '' ); return false;"><strong><?php echo number_format( $tip_rate, 0, '', '' ); ?>%</strong><br /><span><?php echo $GLOBALS['currency']->get_currency_display( $tip_rate / 100 * $this->order_totals->get_converted_sub_total( ), false ); ?></span></a>
            </li>
            <?php }?>
        </ul>
        <div class="ec_cart_tip_item ec_cart_tip_custom_item ec_cart_button_row">
            <label><?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tip_custom' ); ?></label>
            <input type="number" id="ec_cart_tip_custom" value="<?php echo ( $GLOBALS['ec_cart_data']->cart_data->tip_rate == 'custom' ) ? number_format( $GLOBALS['ec_cart_data']->cart_data->tip_amount, 2, '.', '' ) : ''; ?>" />
            <input type="button" value="<?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_apply_tip_custom' ); ?>" id="ec_apply_tip_button" class="ec_cart_button" onclick="wpeasycart_update_tip( 'custom' ); return false;" />
            <div class="ec_cart_button_working" id="ec_applying_tip"><?php echo $GLOBALS['language']->get_text( 'cart', 'cart_please_wait' )?></div>
        </div>
    </div>
    <?php }?>
    <?php if( $this->order_totals->tax_total > 0 ){ ?>
    <div class="ec_cart_price_row ec_cart_price_row_tax_total">
        <div class="ec_cart_price_row_label"><?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tax' )?></div>
        <div class="ec_cart_price_row_total" id="ec_cart_tax"><?php echo $this->get_tax_total( ); ?></div>
    </div>
    <?php }?>
    <?php if( get_option( 'ec_option_use_shipping' ) && ( $this->cart->shippable_total_items > 0 || $this->order_totals->handling_total > 0 ) ){?>
    <div class="ec_cart_price_row ec_cart_price_row_shipping_total">
        <div class="ec_cart_price_row_label"><?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_shipping' )?></div>
        <div class="ec_cart_price_row_total" id="ec_cart_shipping"><?php echo $this->get_shipping_total( ); ?></div>
    </div>
    <?php }?>
    <div class="ec_cart_price_row ec_cart_price_row_discount_total<?php if( $this->order_totals->discount_total == 0 ){ ?> ec_no_discount<?php }else{ ?> ec_has_discount<?php }?>">
        <div class="ec_cart_price_row_label"><?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_discounts' )?></div>
        <div class="ec_cart_price_row_total" id="ec_cart_discount"><?php echo $this->get_discount_total( ); ?></div>
    </div>
    <?php if( $this->tax->is_duty_enabled( ) ){ ?>
    <div class="ec_cart_price_row ec_cart_price_row_duty_total">
        <div class="ec_cart_price_row_label"><?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_duty' )?></div>
        <div class="ec_cart_price_row_total" id="ec_cart_duty"><?php echo $this->get_duty_total( ); ?></div>
    </div>
    <?php }?>
    <?php if( $this->tax->is_vat_enabled( ) ){ ?>
    <div class="ec_cart_price_row ec_cart_price_row_vat_total">
        <div class="ec_cart_price_row_label"><?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_vat' )?> <span id="ec_cart_vat_rate"<?php echo ( $this->order_totals->vat_total <= 0 ) ? ' style="display:none;"' : ''; ?>><?php echo $this->get_vat_rate_formatted( ); ?></span></div>
        <div class="ec_cart_price_row_total" id="ec_cart_vat"><?php echo $this->get_vat_total_formatted( ); ?></div>
    </div>
    <?php }?>
	<?php if( get_option( 'ec_option_enable_easy_canada_tax' ) && $this->order_totals->gst_total > 0 ){ ?>
    <div class="ec_cart_price_row ec_cart_price_row_gst_total">
        <div class="ec_cart_price_row_label">GST (<?php echo $this->tax->gst_rate; ?>%)</div>
        <div class="ec_cart_price_row_total" id="ec_cart_tax"><?php echo $this->get_gst_total( ); ?></div>
    </div>
    <?php }?>
    <?php if( get_option( 'ec_option_enable_easy_canada_tax' ) && $this->order_totals->pst_total > 0 ){ ?>
    <div class="ec_cart_price_row ec_cart_price_row_pst_total">
        <div class="ec_cart_price_row_label">PST (<?php echo $this->tax->pst_rate; ?>%)</div>
        <div class="ec_cart_price_row_total" id="ec_cart_tax"><?php echo $this->get_pst_total( ); ?></div>
    </div>
    <?php }?>
    <?php if( get_option( 'ec_option_enable_easy_canada_tax' ) && $this->order_totals->hst_total > 0 ){ ?>
    <div class="ec_cart_price_row ec_cart_price_row_hst_total">
        <div class="ec_cart_price_row_label">HST (<?php echo $this->tax->hst_rate; ?>%)</div>
        <div class="ec_cart_price_row_total" id="ec_cart_tax"><?php echo $this->get_hst_total( ); ?></div>
    </div>
    <?php }?>
    <div class="ec_cart_price_row ec_order_total">
        <div class="ec_cart_price_row_label"><?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_grand_total' )?></div>
        <div class="ec_cart_price_row_total" id="ec_cart_total"><?php echo $this->get_grand_total( ); ?></div>
    </div>
    
    <div class="ec_cart_error_row" id="ec_checkout_error">
        <?php echo $GLOBALS['language']->get_text( 'cart_form_notices', 'cart_notice_checkout_details_errors' )?>
    </div>
    
    <?php if( $this->shipping->shipping_method != "live" || $this->shipping->has_live_rates ){ ?>
    <div class="ec_cart_button_row">
        <input type="submit" value="<?php echo $GLOBALS['language']->get_text( 'cart_contact_information', 'cart_contact_information_continue_payment' ); ?>" class="ec_cart_button" />
    </div>
    <?php } ?>
    
</div>
<?php $this->display_page_two_form_end( ); ?>

<div style="clear:both;"></div>
<div id="ec_current_media_size"></div>