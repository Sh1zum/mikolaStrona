<section class="ec_account_page" id="ec_account_order_details">
	<?php if( $this->order ){ ?>
    <div class="right">
    
        <a href="<?php echo $this->account_page . $this->permalink_divider; ?>ec_page=print_receipt&order_id=<?php echo $this->order->order_id; ?><?php if( $this->order->guest_key != "" ){ 
            echo '&ec_guest_key=' . $this->order->guest_key; 
        } ?>" target="_blank"><img src="<?php echo $this->get_print_order_icon_url( ); ?>" /></a>
        
    </div>
	
    <div class="ec_account_order_details_main_holder">
    
		<div class="ec_account_order_details_left">
        
            <div class="ec_cart_header ec_top"><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_order_info_title' )?></div>
            
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_order_number' )?></strong> <?php $this->order->display_order_id( ); ?></div>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_order_date' )?></strong> <?php $this->order->display_order_date( ); ?></div>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_order_status' )?></strong> <?php $this->order->display_order_status( ); ?></div>
        
            <?php if( get_option( 'ec_option_use_shipping' ) ){?>
        
                <?php if( $this->order->shipping_method ){ ?>

                <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_shipping_method' )?></strong> <?php $this->order->display_order_shipping_method( ); ?></div>

                <?php }?>
        
            <?php }?>
        
            <?php if( $this->order->promo_code ){ ?>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_coupon_code' )?></strong> <?php $this->order->display_order_promocode( ); ?></div>
        
            <?php }?>
        
            <?php if( $this->order->giftcard_id ){ ?>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_gift_card' )?></strong> <?php $this->order->display_order_giftcard( ); ?></div>
        
            <?php }?>
        
            <?php if( $this->order->has_tracking_number( ) ){ ?>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_order_tracking' )?></strong> <?php $this->order->display_order_tracking_number( ); ?></div>
        
            <?php }?>
        
            <?php if( $this->order->subscription_id ){?>
        
            <div class="ec_cart_input_row"><strong><?php $this->order->display_subscription_link( $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_view_subscription' ) ); ?></strong> <?php $this->order->display_order_tracking_number( ); ?></div>
        
            <?php }?>
            
            <?php if( $this->order->has_membership_page( ) ){ ?>

            <div class="ec_cart_input_row"><strong><a href="<?php echo $this->order->get_membership_page_link( ); ?>"><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_line_5" ); ?></a></strong></div>
            
            <?php }?>
            
            <?php if( $this->order->vat_registration_number != "" ){ ?>
            
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'cart_billing_information', 'cart_billing_information_vat_registration_number' ); ?>:</strong> <?php echo htmlspecialchars( stripslashes( $this->order->vat_registration_number ), ENT_QUOTES ); ?></div>
            
            <?php }?>
        
            <div class="ec_cart_input_row">&nbsp;&nbsp;&nbsp;</div>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_billing_label' )?></strong></div>
        
            <div class="ec_cart_input_row"><?php $this->order->display_order_billing_first_name( ); ?> <?php $this->order->display_order_billing_last_name( ); ?></div>
            
            <?php if( $this->order->billing_company_name != "" ){ ?>
            
            <div class="ec_cart_input_row"><?php echo htmlspecialchars( $this->order->billing_company_name, ENT_QUOTES ); ?></div>
            
            <?php }?>
        
            <div class="ec_cart_input_row"><?php $this->order->display_order_billing_address_line_1( ); ?></div>
        
            <?php if( $this->order->billing_address_line_2 != "" ){ ?>
        
            <div class="ec_cart_input_row"><?php echo htmlspecialchars( $this->order->billing_address_line_2, ENT_QUOTES ); ?></div>
        
            <?php }?>
        
            <div class="ec_cart_input_row"><?php $this->order->display_order_billing_city( ); ?>, <?php $this->order->display_order_billing_state( ); ?> <?php $this->order->display_order_billing_zip( ); ?></div>
        
            <div class="ec_cart_input_row"><?php $this->order->display_order_billing_country( ); ?></div>
        
            <div class="ec_cart_input_row"><?php $this->order->display_order_billing_phone( ); ?></div>
        
            <div class="ec_cart_input_row">&nbsp;&nbsp;&nbsp;</div>
        
            <?php if( get_option( 'ec_option_use_shipping' ) && ( !$this->order->subscription_id || get_option( 'ec_option_collect_shipping_for_subscriptions' ) ) ){?>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_shipping_label' )?></strong></div>
        
            <div class="ec_cart_input_row"><?php $this->order->display_order_shipping_first_name( ); ?> <?php $this->order->display_order_shipping_last_name( ); ?></div>
            
            <?php if( $this->order->shipping_company_name != "" ){ ?>
            
            <div class="ec_cart_input_row"><?php echo htmlspecialchars( $this->order->shipping_company_name, ENT_QUOTES ); ?></div>
            
            <?php }?>
        
            <div class="ec_cart_input_row"><?php $this->order->display_order_shipping_address_line_1( ); ?></div>
        
            <?php if( $this->order->shipping_address_line_2 != "" ){ ?>
        
            <div class="ec_cart_input_row"><?php echo htmlspecialchars( $this->order->shipping_address_line_2, ENT_QUOTES ); ?></div>
        
            <?php }?>
        
            <div class="ec_cart_input_row"><?php $this->order->display_order_shipping_city( ); ?>, <?php $this->order->display_order_shipping_state( ); ?> <?php $this->order->display_order_shipping_zip( ); ?></div>
        
            <div class="ec_cart_input_row"><?php $this->order->display_order_shipping_country( ); ?></div>
        
            <div class="ec_cart_input_row"><?php $this->order->display_order_shipping_phone( ); ?></div>
        
            <div class="ec_cart_input_row">&nbsp;&nbsp;&nbsp;</div>
        
            <?php }?>
        
            <?php if( $this->order->creditcard_digits != "" ){ ?>
            
            <?php if( $this->order->card_holder_name != "" ){ ?>
            
            <div class="ec_cart_input_row"><strong><?php echo $this->order->card_holder_name; ?></strong></div>
            
            <?php }?>
        
            <div class="ec_cart_input_row"><?php $this->order->display_payment_method( ); ?>: ************<?php echo $this->order->creditcard_digits; ?></div>
        
            <?php }else{?>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_payment_method' )?></strong> <?php $this->order->display_payment_method( ); ?></div>
        
            <?php }?>
        
            <div class="ec_cart_input_row">&nbsp;&nbsp;&nbsp;</div>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_subtotal' )?></strong> <?php $this->order->display_sub_total( ); ?></div>
        
            <?php if( $this->order->tip_total > 0 ){?>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tip' ); ?></strong> <?php $this->order->display_tip_total( ); ?></div>
        
            <?php } ?>
        
            <?php if( get_option( 'ec_option_use_shipping' ) && $this->order->shipping_method ){?>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_shipping_total' )?></strong> <?php $this->order->display_shipping_total( ); ?></div>
        
            <?php }?>
            
            <?php if( $this->order->tax_total > 0 ){?>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_tax_total' )?></strong> <?php $this->order->display_tax_total( ); ?></div>
        
            <?php } ?>
        
			<?php if( $this->order->discount_total != 0 ){ ?>
    
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_discount_total' )?></strong> -<?php $this->order->display_discount_total( ); ?></div>
    
            <?php } ?>
            
            <?php if( $this->order->has_duty( ) ){?>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_duty_total' )?></strong> <?php $this->order->display_duty_total( ); ?></div>
        
            <?php } ?>
        
            <?php if( $this->order->has_vat( ) ){?>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_vat_total' )?></strong> <?php $this->order->display_vat_total( ); ?></div>
        
            <?php } ?>
        
            <?php if( $this->order->gst_total > 0 ){?>
        
            <div class="ec_cart_input_row"><strong>GST (<?php echo $this->order->gst_rate; ?>%)</strong> <?php $this->order->display_gst_total( ); ?></div>
        
            <?php } ?>
        
            <?php if( $this->order->pst_total > 0 ){?>
        
            <div class="ec_cart_input_row"><strong>PST (<?php echo $this->order->pst_rate; ?>%)</strong> <?php $this->order->display_pst_total( ); ?></div>
        
            <?php } ?>
        
            <?php if( $this->order->hst_total > 0 ){?>
        
            <div class="ec_cart_input_row"><strong>HST (<?php echo $this->order->hst_rate; ?>%)</strong> <?php $this->order->display_hst_total( ); ?></div>
        
            <?php } ?>
        
            <?php if( $this->order->has_refund( ) ){?>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_refund_total' )?></strong> <?php $this->order->display_refund_total( ); ?></div>
        
            <?php } ?>
        
            <div class="ec_cart_input_row"><strong><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_grand_total' )?></strong> <?php $this->order->display_grand_total( ); ?></div>
        
        </div>
        
        <div class="ec_account_order_details_right">
        
        	<?php do_action( 'wpeasycart_account_order_details_right_top', $this->order->order_id, $this->order ); ?>
        
            <div class="ec_cart_header ec_top"><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_your_order_title' )?></div>
        
            <?php $this->order->display_order_customer_details_notes( ); ?>
            
            <table class="ec_account_order_details_table">
        
                <thead>
                    
                    <tr>
                
                        <th class="ec_account_orderitem_head_name" colspan="2"><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_header_1' )?></th>
                
                        <th class="ec_account_orderitem_head_price"><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_header_3' )?></th>
                
                        <th class="ec_account_orderitem_head_quantity"><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_header_4' )?></th>
                
                        <th class="ec_account_orderitem_head_total"><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_header_5' )?></th>
                
                    </tr>
                    
                </thead>
                
                <tbody>
                
                <?php $this->display_order_detail_product_list( ); ?>
                
                </tbody>
          
            </table>
        
            <?php if( get_option( 'ec_option_user_order_notes' ) && strlen( trim( $this->order->order_customer_notes ) ) > 0 ){ ?>
        
            <div class="ec_account_order_notes">
        
                <h4><?php echo $GLOBALS['language']->get_text( 'cart_payment_information', 'cart_payment_information_order_notes_title' ); ?></h4>
        
                <p><?php echo nl2br( $this->order->order_customer_notes ); ?></p>
        
                <br>
        
                <hr />
        
            </div>
        
            <?php }?>
        
        </div>
    
    </div>

    <?php }else{ ?>
    <div class="ec_account_no_order_found"><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'no_order_found' )?></div>
    <div class="ec_account_return_to_dashboard_button"><a href="<?php echo $this->account_page; ?>"><?php echo $GLOBALS['language']->get_text( 'account_order_details', 'return_to_dashboard' )?></a></div>
    <?php } ?>

    <div style="clear:both;"></div>
    <div id="ec_current_media_size"></div>

</section>