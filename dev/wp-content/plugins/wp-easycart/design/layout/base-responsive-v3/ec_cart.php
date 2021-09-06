<?php 

if( $this->should_display_cart( ) ){ 

?>

<section class="ec_cart_page">
	
    <div class="ec_cart_error" id="ec_stripe_error" style="display:none;">
    	<div>
			<?php echo $GLOBALS['language']->get_text( "ec_errors", "payment_failed" ); ?>
        </div>
    </div>
    
    <div class="ec_cart_breadcrumbs">
    	<div class="ec_cart_breadcrumb<?php if( isset( $_GET['ec_page'] ) ){?> ec_inactive<?php }?>"><?php echo $GLOBALS['language']->get_text( 'cart', 'cart_title' ); ?></div>
        <div class="ec_cart_breadcrumb_divider"></div>
        <div class="ec_cart_breadcrumb<?php if( !isset( $_GET['ec_page'] ) || ( isset( $_GET['ec_page'] ) && $_GET['ec_page'] != "checkout_info" ) ){?> ec_inactive<?php }?>"><?php echo $GLOBALS['language']->get_text( 'cart', 'cart_checkout_details_title' ); ?></div>
        <div class="ec_cart_breadcrumb_divider"></div>
        <div class="ec_cart_breadcrumb<?php if( !isset( $_GET['ec_page'] ) || ( isset( $_GET['ec_page'] ) && $_GET['ec_page'] != "checkout_payment" ) ){?> ec_inactive<?php }?>"><?php echo $GLOBALS['language']->get_text( 'cart', 'cart_submit_payment_title' ); ?></div>
    </div>
    
    <?php if( !isset( $_GET['ec_page'] ) || ( (float) get_option( 'ec_option_minimum_order_total' ) > 0 && (float) get_option( 'ec_option_minimum_order_total' ) > $this->cart->subtotal ) ){ ?>
	
	<div class="ec_cart_left ec_cart_holder">
    	
		<div class="ec_cart_backorders_present" id="ec_cart_backorder_message"<?php if( !$this->cart->has_backordered_item( ) ){ ?> style="display:none;"<?php }?>><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_backordered_message' ); ?></div>
        <?php do_action( 'wpeasycart_cart_post_minimum_notice' ); ?>
    	
        <table class="ec_cart" cellspacing="0">
            <thead>
                <tr>
                    <th class="ec_cartitem_head_name" colspan="3"><?php echo $GLOBALS['language']->get_text( 'cart', 'cart_header_column1' )?></th>
                    <th class="ec_cartitem_head_price"><?php echo $GLOBALS['language']->get_text( 'cart', 'cart_header_column3' )?></th>
                    <th class="ec_cartitem_head_quantity"><?php echo $GLOBALS['language']->get_text( 'cart', 'cart_header_column4' )?></th>
                    <th class="ec_cartitem_head_total"><?php echo $GLOBALS['language']->get_text( 'cart', 'cart_header_column5' )?></th>
                </tr>
            </thead>
            
            <tbody>
                <?php for( $cartitem_index = 0; $cartitem_index<count( $this->cart->cart ); $cartitem_index++ ){ ?>
                <?php do_action( 'wpeasycart_cart_item_row_pre', $this->cart->cart[$cartitem_index], $cartitem_index ); ?>
                <tr class="ec_cartitem_error_row" id="ec_cartitem_max_error_<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>">
                	<td colspan="6"><?php echo $GLOBALS['language']->get_text( 'cart', 'cartitem_max_error' )?></td>
                </tr>
                
                <tr class="ec_cartitem_error_row" id="ec_cartitem_min_error_<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>">
                	<td colspan="6"><?php echo $GLOBALS['language']->get_text( 'cart', 'cartitem_min_error' )?></td>
                </tr>
                
                <tr class="ec_cartitem_row" id="ec_cartitem_row_<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>">
                    <td class="ec_cartitem_remove_column">
                    	<div class="ec_cartitem_delete" id="ec_cartitem_delete_<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>" onclick="ec_cartitem_delete( '<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>', '<?php echo $this->cart->cart[$cartitem_index]->model_number; ?>' );"></div>
                    	<div class="ec_cartitem_deleting" id="ec_cartitem_deleting_<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>"></div>
                    </td>
                    <td class="ec_cartitem_image"><img src="<?php echo $this->cart->cart[$cartitem_index]->get_image_url( ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->cart->cart[$cartitem_index]->title ); ?>" /></td>
                    <td class="ec_cartitem_details">
                        <a href="<?php echo $this->cart->cart[$cartitem_index]->get_title_link( ); ?>" class="ec_cartitem_title"><?php $this->cart->cart[$cartitem_index]->display_title( ); ?></a>
                        <?php if( 
							$this->cart->cart[$cartitem_index]->use_optionitem_quantity_tracking && 
							$this->cart->cart[$cartitem_index]->allow_backorders
						){ ?>
						<div class="ec_cart_backorder_date" id="ec_cartitem_backorder_<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>"<?php if( $this->cart->cart[$cartitem_index]->optionitem_stock_quantity >= $this->cart->cart[$cartitem_index]->quantity ){ ?> style="display:none;"<?php }?>><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_some_backordered' ); ?> <?php echo $this->cart->cart[$cartitem_index]->optionitem_stock_quantity; ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_some_backordered_remaining' ); ?><?php if( $this->cart->cart[$cartitem_index]->backorder_fill_date != "" ){ ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_backorder_until' ); ?> <?php echo $this->cart->cart[$cartitem_index]->backorder_fill_date; ?><?php }?></div>
                        
						<?php }else if( 
							$this->cart->cart[$cartitem_index]->show_stock_quantity &&
							$this->cart->cart[$cartitem_index]->allow_backorders ){ ?>
                        <div class="ec_cart_backorder_date" id="ec_cartitem_backorder_<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>"<?php if( $this->cart->cart[$cartitem_index]->stock_quantity > $this->cart->cart[$cartitem_index]->quantity ){ ?> style="display:none;"<?php }?>><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_backordered' ); ?><?php if( $this->cart->cart[$cartitem_index]->backorder_fill_date != "" ){ ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_backorder_until' ); ?> <?php echo $this->cart->cart[$cartitem_index]->backorder_fill_date; ?><?php }?></div>
                        
						<?php } ?>
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
                    </td>
                    <td class="ec_cartitem_price" id="ec_cartitem_price_<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>"><?php echo $this->cart->cart[$cartitem_index]->get_unit_price( ); ?></td>
                    <td class="ec_cartitem_quantity">
                    	<?php if( $this->cart->cart[$cartitem_index]->grid_quantity > 0 ){ ?>
                        	<?php echo $this->cart->cart[$cartitem_index]->grid_quantity; ?>
                        <?php }else if( $this->cart->cart[$cartitem_index]->is_deconetwork ){ ?>
                        	<?php echo $this->cart->cart[$cartitem_index]->quantity; ?>
                        <?php }else{ ?>
                        <div class="ec_cartitem_updating" id="ec_cartitem_updating_<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>"></div>
                        <table class="ec_cartitem_quantity_table">
                        	<tbody>
                            	<tr>
                                	<td class="ec_minus_column">
                        				<input type="button" value="-" class="ec_minus" onclick="ec_minus_quantity( '<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>', '<?php echo $this->cart->cart[$cartitem_index]->min_quantity; ?>' );" /></td>
                        			<td class="ec_quantity_column"><input type="number" value="<?php echo $this->cart->cart[$cartitem_index]->quantity; ?>" id="ec_quantity_<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>" autocomplete="off" step="1" min="<?php echo $this->cart->cart[$cartitem_index]->min_quantity; ?>" max="<?php echo $this->cart->cart[$cartitem_index]->max_quantity; ?>" class="ec_quantity" /></td>
                        			<td class="ec_plus_column"><input type="button" value="+" class="ec_plus" onclick="ec_plus_quantity( '<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>', '<?php echo $this->cart->cart[$cartitem_index]->show_stock_quantity; ?>', '<?php echo $this->cart->cart[$cartitem_index]->max_quantity; ?>' );" /></td>
                                </tr>
                                <tr>
                                	<td colspan="3"><div class="ec_cartitem_update_button" id="ec_cartitem_update_<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>" onclick="ec_cartitem_update( '<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>', '<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>' );"><?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_update_button' )?></div></td>
                                </tr>
                            </tbody>
                        </table>
						<?php }?>
                    </td>
                    <td class="ec_cartitem_total" id="ec_cartitem_total_<?php echo $this->cart->cart[$cartitem_index]->cartitem_id; ?>"><?php echo $this->cart->cart[$cartitem_index]->get_total( ); ?></td>
                </tr>
                <?php }?>
            </tbody>
    	</table>
    </div>
    <div class="ec_cart_right">
    	<div class="ec_cart_header ec_top">
        	<?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_label' )?>
        </div>
        <div class="ec_cart_price_row ec_cart_price_row_subtotal">
        	<div class="ec_cart_price_row_label"><?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_subtotal' )?></div>
            <div class="ec_cart_price_row_total" id="ec_cart_subtotal"><?php echo $this->get_subtotal( ); ?></div>
        </div>
        <div class="ec_cart_price_row ec_cart_price_row_tax_total"<?php if( $this->order_totals->tax_total <= 0 ){ ?> style="display:none;"<?php }?>>
        	<div class="ec_cart_price_row_label"><?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tax' )?></div>
            <div class="ec_cart_price_row_total" id="ec_cart_tax"><?php echo $this->get_tax_total( ); ?></div>
        </div>
        <?php if( get_option( 'ec_option_use_shipping' ) && ( $this->cart->shippable_total_items > 0 || $this->order_totals->handling_total > 0 ) && !get_option( 'ec_option_hide_shipping_rate_page1' ) ){ ?>
        <div class="ec_cart_price_row ec_cart_price_row_shipping_total" id="ec_cart_shipping_row"<?php if( !$this->shipping->has_shipping_rates( ) ){ ?> style="display:none;"<?php }?>>
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
         <?php if( get_option( 'ec_option_enable_easy_canada_tax' ) ){ ?>
        <div class="ec_cart_price_row ec_cart_price_row_gst_total"<?php if( $this->order_totals->gst_total <= 0 ){ ?> style="display:none"<?php }?>>
        	<div class="ec_cart_price_row_label">GST<?php if( $this->tax->gst_rate > 0 ){ ?> (<?php echo $this->tax->gst_rate; ?>%)<?php }?></div>
            <div class="ec_cart_price_row_total" id="ec_cart_gst"><?php echo $this->get_gst_total( ); ?></div>
        </div>
        <?php }?>
        <?php if( get_option( 'ec_option_enable_easy_canada_tax' ) ){ ?>
        <div class="ec_cart_price_row ec_cart_price_row_pst_total"<?php if( $this->order_totals->pst_total <= 0 ){ ?> style="display:none"<?php }?>>
        	<div class="ec_cart_price_row_label">PST<?php if( $this->tax->pst_rate > 0 ){ ?> (<?php echo $this->tax->pst_rate; ?>%)<?php }?></div>
            <div class="ec_cart_price_row_total" id="ec_cart_pst"><?php echo $this->get_pst_total( ); ?></div>
        </div>
        <?php }?>
        <?php if( get_option( 'ec_option_enable_easy_canada_tax' ) ){ ?>
        <div class="ec_cart_price_row ec_cart_price_row_hst_total"<?php if( $this->order_totals->hst_total <= 0 ){ ?> style="display:none"<?php }?>>
        	<div class="ec_cart_price_row_label">HST<?php if( $this->tax->hst_rate > 0 ){ ?> (<?php echo $this->tax->hst_rate; ?>%)<?php }?></div>
            <div class="ec_cart_price_row_total" id="ec_cart_hst"><?php echo $this->get_hst_total( ); ?></div>
        </div>
        <?php }?>
        <div class="ec_cart_price_row ec_order_total">
        	<div class="ec_cart_price_row_label"><?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_grand_total' )?></div>
            <div class="ec_cart_price_row_total" id="ec_cart_total"><?php echo $this->get_grand_total( ); ?></div>
        </div>
        <input type="hidden" name="ec_cart_weight" id="ec_cart_weight" value="<?php echo $this->cart->weight; ?>" />
        
        <?php do_action( 'wp_easycart_totals_after' ); ?>
        
		<?php if( get_option( 'ec_option_payment_process_method' ) == 'stripe' || get_option( 'ec_option_payment_process_method' ) == 'stripe_connect' ){ $this->print_stripe_payment_button( ); } ?>
		
        <?php if( get_option( 'ec_option_payment_process_method' ) == 'square' ){ $this->print_square_payment_button( ); } ?>
		
		<?php if( apply_filters( 'wpeasycart_show_checkout_button', true ) ){ ?>
        <div class="ec_cart_button_row">
        	<a class="ec_cart_button" href="<?php echo $this->cart_page . $this->permalink_divider . "ec_page=checkout_info"; ?>"<?php if( trim( get_option( 'ec_option_fb_pixel' ) ) != '' ){ ?> onclick="fbq('track', 'InitiateCheckout', {value: <?php echo number_format( $this->order_totals->grand_total, 2, '.', '' ); ?>, currency: '<?php echo $GLOBALS['currency']->get_currency_code( ); ?>', num_items: '<?php echo $this->cart->total_items; ?>', contents: [<?php
				for( $i=0; $i<count( $this->cart->cart ); $i++ ){
					if( $i > 0 )
						echo ", ";
					echo "{ id: '" . $this->cart->cart[$i]->product_id . "', quantity: " . $this->cart->cart[$i]->quantity . ", price: " . $this->cart->cart[$i]->unit_price . " }";
				}		
				?>]});"<?php }?><?php do_action( 'wpeasycart_checkout_button_ahref' ); ?>><?php echo $GLOBALS['language']->get_text( 'cart', 'cart_checkout' ); ?></a>
        </div>
        <?php } ?>
        <?php if( get_option( 'ec_option_payment_third_party' ) == 'paypal' && apply_filters( 'wp_easycart_allow_paypal_express', false ) && get_option( 'ec_option_paypal_express_page1_checkout' ) && ( get_option( 'ec_option_paypal_enable_credit' ) == '1' || get_option( 'ec_option_paypal_enable_pay_now' ) == '1' ) && $this->order_totals->grand_total > 0 && ( $GLOBALS['ec_cart_data']->cart_data->user_id != "" || ( get_option( 'ec_option_allow_guest' ) && !$this->has_downloads ) ) ){ ?>
    	<div id="paypal-button-container" style="float:left; width:100%; margin:10px 0;"></div>
		<div id="paypal-success-cover" style="display:none; cursor:default; position:fixed; top:0; left:0; width:100%; height:100%; z-index:999999; background-color: rgba(0, 0, 0, 0.8); color:#FFF;">
            <style>
			@keyframes rotation{
				0%  { transform:rotate(0deg); }
				100%{ transform:rotate(359deg); }
			}
			</style>
            <div style='font-family: "HelveticaNeue", "HelveticaNeue-Light", "Helvetica Neue Light", helvetica, arial, sans-serif; font-size: 14px; text-align: center; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; -ms-box-sizing: border-box; box-sizing: border-box; width: 350px; top: 50%; left: 50%; position: absolute; margin-left: -165px; margin-top: -80px; cursor: pointer; text-align: center;'>
                <div class="paypal-checkout-logo">
                    <img class="paypal-checkout-logo-pp" alt="pp" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAyNCAzMiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBwcmVzZXJ2ZUFzcGVjdFJhdGlvPSJ4TWluWU1pbiBtZWV0Ij4KICAgIDxwYXRoIGZpbGw9IiNmZmZmZmYiIG9wYWNpdHk9IjAuNyIgZD0iTSAyMC43MDIgOS40NDYgQyAyMC45ODIgNy4zNDcgMjAuNzAyIDUuOTQ3IDE5LjU3OCA0LjU0OCBDIDE4LjM2MSAzLjE0OCAxNi4yMDggMi41NDggMTMuNDkzIDIuNTQ4IEwgNS41MzYgMi41NDggQyA0Ljk3NCAyLjU0OCA0LjUwNiAyLjk0OCA0LjQxMiAzLjU0OCBMIDEuMTM2IDI1Ljc0IEMgMS4wNDIgMjYuMjM5IDEuMzIzIDI2LjYzOSAxLjc5MSAyNi42MzkgTCA2Ljc1MyAyNi42MzkgTCA2LjM3OCAyOC45MzggQyA2LjI4NSAyOS4yMzggNi42NTkgMjkuNjM4IDYuOTQgMjkuNjM4IEwgMTEuMTUzIDI5LjYzOCBDIDExLjYyMSAyOS42MzggMTEuOTk1IDI5LjIzOCAxMi4wODkgMjguNzM5IEwgMTIuMTgyIDI4LjUzOSBMIDEyLjkzMSAyMy4zNDEgTCAxMy4wMjUgMjMuMDQxIEMgMTMuMTE5IDIyLjQ0MSAxMy40OTMgMjIuMTQxIDEzLjk2MSAyMi4xNDEgTCAxNC42MTYgMjIuMTQxIEMgMTguNjQyIDIyLjE0MSAyMS43MzEgMjAuMzQyIDIyLjY2OCAxNS40NDMgQyAyMy4wNDIgMTMuMzQ0IDIyLjg1NSAxMS41NDUgMjEuODI1IDEwLjM0NSBDIDIxLjQ1MSAxMC4wNDYgMjEuMDc2IDkuNjQ2IDIwLjcwMiA5LjQ0NiBMIDIwLjcwMiA5LjQ0NiI+PC9wYXRoPgogICAgPHBhdGggZmlsbD0iI2ZmZmZmZiIgb3BhY2l0eT0iMC43IiBkPSJNIDIwLjcwMiA5LjQ0NiBDIDIwLjk4MiA3LjM0NyAyMC43MDIgNS45NDcgMTkuNTc4IDQuNTQ4IEMgMTguMzYxIDMuMTQ4IDE2LjIwOCAyLjU0OCAxMy40OTMgMi41NDggTCA1LjUzNiAyLjU0OCBDIDQuOTc0IDIuNTQ4IDQuNTA2IDIuOTQ4IDQuNDEyIDMuNTQ4IEwgMS4xMzYgMjUuNzQgQyAxLjA0MiAyNi4yMzkgMS4zMjMgMjYuNjM5IDEuNzkxIDI2LjYzOSBMIDYuNzUzIDI2LjYzOSBMIDcuOTcgMTguMzQyIEwgNy44NzYgMTguNjQyIEMgOC4wNjMgMTguMDQzIDguNDM4IDE3LjY0MyA5LjA5MyAxNy42NDMgTCAxMS40MzMgMTcuNjQzIEMgMTYuMDIxIDE3LjY0MyAxOS41NzggMTUuNjQzIDIwLjYwOCA5Ljk0NiBDIDIwLjYwOCA5Ljc0NiAyMC42MDggOS41NDYgMjAuNzAyIDkuNDQ2Ij48L3BhdGg+CiAgICA8cGF0aCBmaWxsPSIjZmZmZmZmIiBkPSJNIDkuMjggOS40NDYgQyA5LjI4IDkuMTQ2IDkuNDY4IDguODQ2IDkuODQyIDguNjQ2IEMgOS45MzYgOC42NDYgMTAuMTIzIDguNTQ2IDEwLjIxNiA4LjU0NiBMIDE2LjQ4OSA4LjU0NiBDIDE3LjIzOCA4LjU0NiAxNy44OTMgOC42NDYgMTguNTQ4IDguNzQ2IEMgMTguNzM2IDguNzQ2IDE4LjgyOSA4Ljc0NiAxOS4xMSA4Ljg0NiBDIDE5LjIwNCA4Ljk0NiAxOS4zOTEgOC45NDYgMTkuNTc4IDkuMDQ2IEMgMTkuNjcyIDkuMDQ2IDE5LjY3MiA5LjA0NiAxOS44NTkgOS4xNDYgQyAyMC4xNCA5LjI0NiAyMC40MjEgOS4zNDYgMjAuNzAyIDkuNDQ2IEMgMjAuOTgyIDcuMzQ3IDIwLjcwMiA1Ljk0NyAxOS41NzggNC42NDggQyAxOC4zNjEgMy4yNDggMTYuMjA4IDIuNTQ4IDEzLjQ5MyAyLjU0OCBMIDUuNTM2IDIuNTQ4IEMgNC45NzQgMi41NDggNC41MDYgMy4wNDggNC40MTIgMy41NDggTCAxLjEzNiAyNS43NCBDIDEuMDQyIDI2LjIzOSAxLjMyMyAyNi42MzkgMS43OTEgMjYuNjM5IEwgNi43NTMgMjYuNjM5IEwgNy45NyAxOC4zNDIgTCA5LjI4IDkuNDQ2IFoiPjwvcGF0aD4KICAgIDxnIHRyYW5zZm9ybT0ibWF0cml4KDAuNDk3NzM3LCAwLCAwLCAwLjUyNjEyLCAxLjEwMTQ0LCAwLjYzODY1NCkiIG9wYWNpdHk9IjAuMiI+CiAgICAgICAgPHBhdGggZmlsbD0iIzIzMWYyMCIgZD0iTTM5LjMgMTYuN2MwLjkgMC41IDEuNyAxLjEgMi4zIDEuOCAxIDEuMSAxLjYgMi41IDEuOSA0LjEgMC4zLTMuMi0wLjItNS44LTEuOS03LjgtMC42LTAuNy0xLjMtMS4yLTIuMS0xLjdDMzkuNSAxNC4yIDM5LjUgMTUuNCAzOS4zIDE2Ljd6Ij48L3BhdGg+CiAgICAgICAgPHBhdGggZmlsbD0iIzIzMWYyMCIgZD0iTTAuNCA0NS4yTDYuNyA1LjZDNi44IDQuNSA3LjggMy43IDguOSAzLjdoMTZjNS41IDAgOS44IDEuMiAxMi4yIDMuOSAxLjIgMS40IDEuOSAzIDIuMiA0LjggMC40LTMuNi0wLjItNi4xLTIuMi04LjRDMzQuNyAxLjIgMzAuNCAwIDI0LjkgMEg4LjljLTEuMSAwLTIuMSAwLjgtMi4zIDEuOUwwIDQ0LjFDMCA0NC41IDAuMSA0NC45IDAuNCA0NS4yeiI+PC9wYXRoPgogICAgICAgIDxwYXRoIGZpbGw9IiMyMzFmMjAiIGQ9Ik0xMC43IDQ5LjRsLTAuMSAwLjZjLTAuMSAwLjQgMC4xIDAuOCAwLjQgMS4xbDAuMy0xLjdIMTAuN3oiPjwvcGF0aD4KICAgIDwvZz4KPC9zdmc+Cg=="><img class="paypal-checkout-logo-paypal" alt="paypal" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjMyIiB2aWV3Qm94PSIwIDAgMTAwIDMyIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaW5ZTWluIG1lZXQiPgogICAgPHBhdGggZmlsbD0iI2ZmZmZmZiIgZD0iTSAxMiA1LjMxNSBMIDQuMiA1LjMxNSBDIDMuNyA1LjMxNSAzLjIgNS43MTUgMy4xIDYuMjE1IEwgMCAyNi4yMTUgQyAtMC4xIDI2LjYxNSAwLjIgMjYuOTE1IDAuNiAyNi45MTUgTCA0LjMgMjYuOTE1IEMgNC44IDI2LjkxNSA1LjMgMjYuNTE1IDUuNCAyNi4wMTUgTCA2LjIgMjAuNjE1IEMgNi4zIDIwLjExNSA2LjcgMTkuNzE1IDcuMyAxOS43MTUgTCA5LjggMTkuNzE1IEMgMTQuOSAxOS43MTUgMTcuOSAxNy4yMTUgMTguNyAxMi4zMTUgQyAxOSAxMC4yMTUgMTguNyA4LjUxNSAxNy43IDcuMzE1IEMgMTYuNiA2LjAxNSAxNC42IDUuMzE1IDEyIDUuMzE1IFogTSAxMi45IDEyLjYxNSBDIDEyLjUgMTUuNDE1IDEwLjMgMTUuNDE1IDguMyAxNS40MTUgTCA3LjEgMTUuNDE1IEwgNy45IDEwLjIxNSBDIDcuOSA5LjkxNSA4LjIgOS43MTUgOC41IDkuNzE1IEwgOSA5LjcxNSBDIDEwLjQgOS43MTUgMTEuNyA5LjcxNSAxMi40IDEwLjUxNSBDIDEyLjkgMTAuOTE1IDEzLjEgMTEuNjE1IDEyLjkgMTIuNjE1IFoiPjwvcGF0aD4KICAgIDxwYXRoIGZpbGw9IiNmZmZmZmYiIGQ9Ik0gMzUuMiAxMi41MTUgTCAzMS41IDEyLjUxNSBDIDMxLjIgMTIuNTE1IDMwLjkgMTIuNzE1IDMwLjkgMTMuMDE1IEwgMzAuNyAxNC4wMTUgTCAzMC40IDEzLjYxNSBDIDI5LjYgMTIuNDE1IDI3LjggMTIuMDE1IDI2IDEyLjAxNSBDIDIxLjkgMTIuMDE1IDE4LjQgMTUuMTE1IDE3LjcgMTkuNTE1IEMgMTcuMyAyMS43MTUgMTcuOCAyMy44MTUgMTkuMSAyNS4yMTUgQyAyMC4yIDI2LjUxNSAyMS45IDI3LjExNSAyMy44IDI3LjExNSBDIDI3LjEgMjcuMTE1IDI5IDI1LjAxNSAyOSAyNS4wMTUgTCAyOC44IDI2LjAxNSBDIDI4LjcgMjYuNDE1IDI5IDI2LjgxNSAyOS40IDI2LjgxNSBMIDMyLjggMjYuODE1IEMgMzMuMyAyNi44MTUgMzMuOCAyNi40MTUgMzMuOSAyNS45MTUgTCAzNS45IDEzLjExNSBDIDM2IDEyLjkxNSAzNS42IDEyLjUxNSAzNS4yIDEyLjUxNSBaIE0gMzAuMSAxOS44MTUgQyAyOS43IDIxLjkxNSAyOC4xIDIzLjQxNSAyNS45IDIzLjQxNSBDIDI0LjggMjMuNDE1IDI0IDIzLjExNSAyMy40IDIyLjQxNSBDIDIyLjggMjEuNzE1IDIyLjYgMjAuODE1IDIyLjggMTkuODE1IEMgMjMuMSAxNy43MTUgMjQuOSAxNi4yMTUgMjcgMTYuMjE1IEMgMjguMSAxNi4yMTUgMjguOSAxNi42MTUgMjkuNSAxNy4yMTUgQyAzMCAxNy44MTUgMzAuMiAxOC43MTUgMzAuMSAxOS44MTUgWiI+PC9wYXRoPgogICAgPHBhdGggZmlsbD0iI2ZmZmZmZiIgZD0iTSA1NS4xIDEyLjUxNSBMIDUxLjQgMTIuNTE1IEMgNTEgMTIuNTE1IDUwLjcgMTIuNzE1IDUwLjUgMTMuMDE1IEwgNDUuMyAyMC42MTUgTCA0My4xIDEzLjMxNSBDIDQzIDEyLjgxNSA0Mi41IDEyLjUxNSA0Mi4xIDEyLjUxNSBMIDM4LjQgMTIuNTE1IEMgMzggMTIuNTE1IDM3LjYgMTIuOTE1IDM3LjggMTMuNDE1IEwgNDEuOSAyNS41MTUgTCAzOCAzMC45MTUgQyAzNy43IDMxLjMxNSAzOCAzMS45MTUgMzguNSAzMS45MTUgTCA0Mi4yIDMxLjkxNSBDIDQyLjYgMzEuOTE1IDQyLjkgMzEuNzE1IDQzLjEgMzEuNDE1IEwgNTUuNiAxMy40MTUgQyA1NS45IDEzLjExNSA1NS42IDEyLjUxNSA1NS4xIDEyLjUxNSBaIj48L3BhdGg+CiAgICA8cGF0aCBmaWxsPSIjZmZmZmZmIiBkPSJNIDY3LjUgNS4zMTUgTCA1OS43IDUuMzE1IEMgNTkuMiA1LjMxNSA1OC43IDUuNzE1IDU4LjYgNi4yMTUgTCA1NS41IDI2LjExNSBDIDU1LjQgMjYuNTE1IDU1LjcgMjYuODE1IDU2LjEgMjYuODE1IEwgNjAuMSAyNi44MTUgQyA2MC41IDI2LjgxNSA2MC44IDI2LjUxNSA2MC44IDI2LjIxNSBMIDYxLjcgMjAuNTE1IEMgNjEuOCAyMC4wMTUgNjIuMiAxOS42MTUgNjIuOCAxOS42MTUgTCA2NS4zIDE5LjYxNSBDIDcwLjQgMTkuNjE1IDczLjQgMTcuMTE1IDc0LjIgMTIuMjE1IEMgNzQuNSAxMC4xMTUgNzQuMiA4LjQxNSA3My4yIDcuMjE1IEMgNzIgNi4wMTUgNzAuMSA1LjMxNSA2Ny41IDUuMzE1IFogTSA2OC40IDEyLjYxNSBDIDY4IDE1LjQxNSA2NS44IDE1LjQxNSA2My44IDE1LjQxNSBMIDYyLjYgMTUuNDE1IEwgNjMuNCAxMC4yMTUgQyA2My40IDkuOTE1IDYzLjcgOS43MTUgNjQgOS43MTUgTCA2NC41IDkuNzE1IEMgNjUuOSA5LjcxNSA2Ny4yIDkuNzE1IDY3LjkgMTAuNTE1IEMgNjguNCAxMC45MTUgNjguNSAxMS42MTUgNjguNCAxMi42MTUgWiI+PC9wYXRoPgogICAgPHBhdGggZmlsbD0iI2ZmZmZmZiIgZD0iTSA5MC43IDEyLjUxNSBMIDg3IDEyLjUxNSBDIDg2LjcgMTIuNTE1IDg2LjQgMTIuNzE1IDg2LjQgMTMuMDE1IEwgODYuMiAxNC4wMTUgTCA4NS45IDEzLjYxNSBDIDg1LjEgMTIuNDE1IDgzLjMgMTIuMDE1IDgxLjUgMTIuMDE1IEMgNzcuNCAxMi4wMTUgNzMuOSAxNS4xMTUgNzMuMiAxOS41MTUgQyA3Mi44IDIxLjcxNSA3My4zIDIzLjgxNSA3NC42IDI1LjIxNSBDIDc1LjcgMjYuNTE1IDc3LjQgMjcuMTE1IDc5LjMgMjcuMTE1IEMgODIuNiAyNy4xMTUgODQuNSAyNS4wMTUgODQuNSAyNS4wMTUgTCA4NC4zIDI2LjAxNSBDIDg0LjIgMjYuNDE1IDg0LjUgMjYuODE1IDg0LjkgMjYuODE1IEwgODguMyAyNi44MTUgQyA4OC44IDI2LjgxNSA4OS4zIDI2LjQxNSA4OS40IDI1LjkxNSBMIDkxLjQgMTMuMTE1IEMgOTEuNCAxMi45MTUgOTEuMSAxMi41MTUgOTAuNyAxMi41MTUgWiBNIDg1LjUgMTkuODE1IEMgODUuMSAyMS45MTUgODMuNSAyMy40MTUgODEuMyAyMy40MTUgQyA4MC4yIDIzLjQxNSA3OS40IDIzLjExNSA3OC44IDIyLjQxNSBDIDc4LjIgMjEuNzE1IDc4IDIwLjgxNSA3OC4yIDE5LjgxNSBDIDc4LjUgMTcuNzE1IDgwLjMgMTYuMjE1IDgyLjQgMTYuMjE1IEMgODMuNSAxNi4yMTUgODQuMyAxNi42MTUgODQuOSAxNy4yMTUgQyA4NS41IDE3LjgxNSA4NS43IDE4LjcxNSA4NS41IDE5LjgxNSBaIj48L3BhdGg+CiAgICA8cGF0aCBmaWxsPSIjZmZmZmZmIiBkPSJNIDk1LjEgNS45MTUgTCA5MS45IDI2LjIxNSBDIDkxLjggMjYuNjE1IDkyLjEgMjYuOTE1IDkyLjUgMjYuOTE1IEwgOTUuNyAyNi45MTUgQyA5Ni4yIDI2LjkxNSA5Ni43IDI2LjUxNSA5Ni44IDI2LjAxNSBMIDEwMCA2LjExNSBDIDEwMC4xIDUuNzE1IDk5LjggNS40MTUgOTkuNCA1LjQxNSBMIDk1LjggNS40MTUgQyA5NS40IDUuMzE1IDk1LjIgNS41MTUgOTUuMSA1LjkxNSBaIj48L3BhdGg+Cjwvc3ZnPgo=">
                </div>
                <div class="paypal-checkout-loader">
                	<div style="height: 30px; width: 30px; display: inline-block; box-sizing: content-box; opacity: 1; filter: alpha(opacity=100); -webkit-animation: rotation .7s infinite linear; -moz-animation: rotation .7s infinite linear; -o-animation: rotation .7s infinite linear; animation: rotation .7s infinite linear; border-left: 8px solid rgba(0, 0, 0, .2); border-right: 8px solid rgba(0, 0, 0, .2); border-bottom: 8px solid rgba(0, 0, 0, .2); border-top: 8px solid #fff; border-radius: 100%;"></div>
				</div>
            </div>
        </div>
        <?php echo $this->get_paypal_express_button_code( ); ?>
		<?php }?>
        <div class="ec_cart_button_row">
        	<a class="ec_cart_button" href="<?php echo $this->return_to_store_page( $this->store_page ); ?>"><?php echo $GLOBALS['language']->get_text( 'cart', 'cart_continue_shopping' ); ?></a>
        </div>
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
        <div class="ec_cart_success_message" id="ec_gift_card_success"<?php if( $this->gift_card != "" ){?> style="display:block;"<?php }?>><?php if( $this->gift_card != "" ){ echo $GLOBALS['language']->convert_text( $this->giftcard->message ); } ?> ...test...</div>  
        <div class="ec_cart_input_row">
        	<input type="text" name="ec_gift_card" id="ec_gift_card" value="<?php echo $this->gift_card; ?>" placeholder="<?php echo $GLOBALS['language']->get_text( 'cart_coupons', 'cart_enter_gift_code' )?>" />
        </div>
        <div class="ec_cart_button_row">
        	<div class="ec_cart_button" id="ec_apply_gift_card" onclick="ec_apply_gift_card( );"><?php echo $GLOBALS['language']->get_text( 'cart_coupons', 'cart_redeem_gift_card' ); ?></div>
            <div class="ec_cart_button_working" id="ec_applying_gift_card"><?php echo $GLOBALS['language']->get_text( 'cart', 'cart_please_wait' )?></div>
        </div>
        <?php }?>
        
        <?php if( get_option( 'ec_option_use_estimate_shipping' ) ){ ?>
        	<?php if( get_option( 'ec_option_use_shipping' ) && ( $this->cart->shippable_total_items > 0 || $this->order_totals->handling_total > 0 ) ){ ?>
        	<div class="ec_cart_header">
        		<?php echo $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_button' )?>
        	</div>
			<?php if( get_option( 'ec_option_estimate_shipping_country' ) ){ ?>
            <div class="ec_cart_input_row">
                <?php $this->display_estimate_shipping_country_select( ); ?>
            </div>
            <?php }?>
            <?php if( get_option( 'ec_option_estimate_shipping_zip' ) ){ ?>
            <div class="ec_cart_input_row">
                <input type="text" name="ec_estimate_zip" id="ec_estimate_zip" value="<?php if( $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip != "" ){ echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip, ENT_QUOTES ); } ?>" placeholder="<?php echo $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_hint' )?>" />
            </div>
            <?php }?>
            <div class="ec_cart_button_row">
                <div class="ec_cart_button" id="ec_estimate_shipping" onclick="ec_estimate_shipping( );"><?php echo $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_button' ); ?></div>
                <div class="ec_cart_button_working" id="ec_estimating_shipping"><?php echo $GLOBALS['language']->get_text( 'cart', 'cart_please_wait' )?></div>
            </div>
        	<?php }?>
        <?php }?>
        
    </div>
    <?php } ?>
    
</section>

<div style="clear:both;"></div>
<div id="ec_current_media_size"></div>

<?php }// close should display cart ?>