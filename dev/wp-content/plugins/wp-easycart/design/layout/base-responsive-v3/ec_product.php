<?php 

// DISPLAY OPTIONS //   
if( isset( $product->page_options->dynamic_image_sizing ) )
	$dynamic_image_sizing = $product->page_options->dynamic_image_sizing;
else
	$dynamic_image_sizing = get_option( 'ec_option_default_dynamic_sizing' );
	
if( isset( $product->page_options->product_type ) )
	$product_type = $product->page_options->product_type;
else
	$product_type = get_option( 'ec_option_default_product_type' );

if( isset( $product->page_options->use_quickview ) )
	$use_quickview = $product->page_options->use_quickview;
else
	$use_quickview = get_option( 'ec_option_default_quick_view' );
	
if( isset( $product->page_options->image_height_desktop ) )
	$image_height_desktop = $product->page_options->image_height_desktop;
else
	$image_height_desktop = get_option( 'ec_option_default_desktop_image_height' );
	
$show_rating = $product->use_customer_reviews;

// Check for iPad or iPhone
$ipad = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
$iphone = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPhone');

if( isset( $_GET['preview'] ) ){
	$is_preview = true;
}else{
	$is_preview = false;
}

if( $ipad || $iphone || $is_preview )
	$use_quickview = false;

// Show admin if logged in and not using preview
if( current_user_can( 'manage_options' ) && !$is_preview && !get_option( 'ec_option_hide_live_editor' ) )
	$admin_access = true;
else
	$admin_access = false;
// DISPLAY OPTIONS //
if( !$product->tag_bg_color ){
	$product->tag_bg_color = "#000000";
}

if( !$product->tag_text_color ){
	$product->tag_text_color = "#FFFFFF";
}
 
?>

<li class="ec_product_li" id="ec_product_li_<?php echo $product->model_number; ?>">

<?php 
/////////// QUICK VIEW ////////////////

if( $admin_access || $use_quickview ){ ?>
<div id="ec_product_quickview_container_<?php echo $product->model_number; ?>" class="ec_product_quickview_container">
	<div class="ec_product_quickview_content">
		<div class="ec_product_quickview_content_padding">
        	<div class="ec_product_quickview_content_holder">
                <div class="ec_product_quickview_content_images" data-image-list="<?php if( $product->images->use_optionitem_images ){ 
						for( $i=0; $i<count( $product->images->imageset ); $i++ ){ 
							if( $i > 0 ){ 
								echo ",";
							}
							if( substr( $product->images->imageset[$i]->image1, 0, 7 ) == 'http://' || substr( $product->images->imageset[$i]->image1, 0, 8 ) == 'https://' ){
								echo $product->images->imageset[$i]->image1;
							}else{
								echo plugins_url( "/wp-easycart-data/products/pics1/" . $product->images->imageset[$i]->image1 );
							}
						}
					}else{ 
						echo $product->get_first_image_url( );
						if( trim( $product->images->image2 ) != "" ){ 
							if( substr( $product->images->image2, 0, 7 ) == 'http://' || substr( $product->images->image2, 0, 8 ) == 'https://' ){
								echo "," . $product->images->image2;
							}else{
								echo "," . plugins_url( "/wp-easycart-data/products/pics2/" . $product->images->image2 ); 
							}
						} 
						if( trim( $product->images->image3 ) != "" ){ 
							if( substr( $product->images->image3, 0, 7 ) == 'http://' || substr( $product->images->image3, 0, 8 ) == 'https://' ){
								echo "," . $product->images->image3;
							}else{
								echo "," . plugins_url( "/wp-easycart-data/products/pics3/" . $product->images->image3 ); 
							}
						}
						if( trim( $product->images->image4 ) != "" ){ 
							if( substr( $product->images->image4, 0, 7 ) == 'http://' || substr( $product->images->image4, 0, 8 ) == 'https://' ){
								echo "," . $product->images->image4;
							}else{
								echo "," . plugins_url( "/wp-easycart-data/products/pics4/" . $product->images->image4 ); 
							}
						} 
						if( trim( $product->images->image5 ) != "" ){ 
							if( substr( $product->images->image5, 0, 7 ) == 'http://' || substr( $product->images->image5, 0, 8 ) == 'https://' ){
								echo "," . $product->images->image5;
							}else{
								echo "," . plugins_url( "/wp-easycart-data/products/pics5/" . $product->images->image5 ); 
							}
						} 
					} ?>">
                    <?php if( ( $product->images->use_optionitem_images && count( $product->images->imageset ) > 1 ) || trim( $product->images->image2 ) != "" ){ ?>
                    <div class="ec_flipbook_left">&#65513;</div>
                    <div class="ec_flipbook_right">&#65515;</div>
                    <?php }?>
                    <img src="<?php echo $product->get_first_image_url( ); ?>" alt="<?php echo $product->title; ?>" class="ec_flipbook_image skip-lazy" />
                </div>
                <div class="ec_product_quickview_content_data">
                    
					<?php if( $product->is_subscription_item && $product->trial_period_days > 0 ){ ?>
                    <div class="ec_product_quickview_trial_notice"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_page_start_trial_1' ); ?> <?php echo $product->trial_period_days; ?> <?php echo $GLOBALS['language']->get_text( 'product_page', 'product_page_start_trial_2' ); ?></div>
                    <?php }?>
                    
                    <h1 class="ec_product_quickview_content_title"><a href="<?php echo $product->get_product_link( ); ?>"><?php echo $product->title; ?></a></h1>
                    <div class="ec_product_quickview_content_divider"></div>
                    <?php if( $product->login_for_pricing && !$product->is_login_for_pricing_valid( ) ){
							  // No Pricing
							  
						  }else if( ( $product->is_catalog_mode && get_option( 'ec_option_hide_price_seasonal' ) ) || 
					  		  ( $product->is_inquiry_mode && get_option( 'ec_option_hide_price_inquiry' ) ) ){  // don't show price
			  			  
						  }else if( $product->show_custom_price_range ){ ?>
                             <div class="ec_product_quickview_content_price">
								<?php if( $product->list_price > 0 ){ ?>
                                <span class="ec_list_price"><?php 
                            
                                        $list_price = $GLOBALS['currency']->get_currency_display( $product->list_price );
                                        $list_price = apply_filters( 'wp_easycart_product_list_price_display', $list_price, $product->list_price );
                                        echo $list_price;
                                
                                    ?></span>
                             	 <?php }?>
                                 <span class="ec_price">
                                 <?php echo $GLOBALS['currency']->get_currency_display( $product->price_range_low ) . ' - ' . $GLOBALS['currency']->get_currency_display( $product->price_range_high ); ?>
                                 </span>
                             </div>
                                 
                          <?php }else{ ?>
                    <div class="ec_product_quickview_content_price"><?php if( $product->list_price > 0 ){ ?><span class="ec_list_price"><?php 
				
							$list_price = $GLOBALS['currency']->get_currency_display( $product->list_price );
							$list_price = apply_filters( 'wp_easycart_product_list_price_display', $list_price, $product->list_price );
							echo $list_price;
					
						?></span><?php }?><span class="ec_price"><?php 
					
						$display_price = $GLOBALS['currency']->get_currency_display( $product->price );
						if( $product->pricing_per_sq_foot && !get_option( 'ec_option_enable_metric_unit_display' ) ){ 
							$display_price .= "/sq ft";
						}else if( $product->pricing_per_sq_foot && get_option( 'ec_option_enable_metric_unit_display' ) ){ 
							$display_price .= "/sq m";
						}
						
						$display_price = apply_filters( 'wp_easycart_product_price_display', $display_price, $product->price, $product->product_id );
						echo $display_price; 
						
					?><?php if( $GLOBALS['ec_vat_included'] && $product->vat_rate == 1 && get_option( 'ec_option_show_multiple_vat_pricing' ) ){ ?> <span class="ec_inc_vat_text"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_inc_vat_text' ); ?></span><?php }else if( $GLOBALS['ec_vat_added'] && $product->vat_rate == 1 && get_option( 'ec_option_show_multiple_vat_pricing' ) ){ ?> <span class="ec_inc_vat_text"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_excluding_vat_text' ); ?></span><?php }?></span></div>
                    <?php }?>
                    
                    <div class="ec_product_quickview_content_description"><?php echo nl2br( stripslashes( $product->short_description)); ?></div>
                    <?php if( isset( $product->pricetiers[0] ) && count( $product->pricetiers[0] ) > 1 ){ ?>
                    
                    <ul class="ec_product_quickview_price_tier">
						<?php 
						foreach( $product->pricetiers as $pricetier ){
							$tier_price = $GLOBALS['currency']->get_currency_display( $pricetier[0] );
							$tier_quantity = $pricetier[1];
							?>
						
                        <li><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_tier_buy' ); ?> <?php echo $tier_quantity; ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_tier_buy_at' ); ?> <?php echo $tier_price; ?></li>
                        
						<?php } ?>
                    </ul>
					<?php }?>
					<?php if( $product->handling_price > 0 ){ ?>
                    <div class="ec_product_quickview_shipping_notice"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_handling_fee_notice1' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $product->handling_price ); ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_handling_fee_notice2' ); ?></div>
                    <?php } ?>
                    <div class="ec_product_quickview_content_add_to_cart_container">
                        <?php if( apply_filters( 'wp_easycart_catalog_display', get_option( 'ec_option_display_as_catalog' ) ) ){
						// Show nothing
						
						}else if( $product->login_for_pricing && !$product->is_login_for_pricing_valid( ) && $GLOBALS['ec_user']->user_id != 0 ){ ?>
						<div class="ec_seasonal_mode ec_call_for_pricing"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_page_login_for_price_no_access' ); ?></div>
        				
						<?php }else if( $product->login_for_pricing && !$product->is_login_for_pricing_valid( ) ){ ?>
							<div class="ec_product_quickview_content_add_to_cart"><a href="<?php echo $this->account_page; ?>"><?php echo ( $product->login_for_pricing_label != '' ) ? $product->login_for_pricing_label : $GLOBALS['language']->get_text( 'product_page', 'product_page_login_for_price' ); ?></a></div>
							
						<?php }else if( $product->is_catalog_mode ){ ?>
						<div class="ec_seasonal_mode"><?php echo $product->catalog_mode_phrase; ?></div>	
        
						<?php }else if( $product->is_deconetwork ){ ?>
                        <div class="ec_product_quickview_content_add_to_cart"><a href="<?php echo $product->get_deconetwork_link( ); ?>"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_design_now' ); ?></a></div>
                        
                        <?php }else if( ( $product->in_stock( ) || $product->allow_backorders ) && ( $product->has_options( ) || $product->is_giftcard || $product->is_inquiry_mode || $product->is_donation || apply_filters( 'wp_easycart_product_force_select_options', false, $product->product_id ) ) ){ ?>
                        <div class="ec_product_quickview_content_add_to_cart"><a href="<?php echo $product->get_product_link( ); ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_select_options' ); ?></a></div>
                        
                        <?php }else if( $product->in_stock( ) && $product->is_subscription_item ){ // && !class_exists( "ec_stripe" ) ){ ?>
                        <div class="ec_product_quickview_content_add_to_cart"><a href="<?php echo $product->get_subscription_link( ); ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_sign_up_now' ); ?></a></div>
                        
                        <?php }else if( $product->in_stock( ) ){ ?>
                        
                        <div class="ec_details_option_row_error" id="ec_addtocart_quantity_exceeded_error_<?php echo $product->model_number; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_maximum_quantity' ); ?></div>
            			<div class="ec_details_option_row_error" id="ec_addtocart_quantity_minimum_error_<?php echo $product->model_number; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_minimum_quantity_text1' ); ?> <?php echo $product->min_purchase_quantity; ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_minimum_quantity_text2' ); ?></div>
                        
                        <?php
					
						$show_add_to_cart_button = true;
						$show_add_to_cart_button = apply_filters( 'wp_easycart_product_show_add_to_cart_button', $show_add_to_cart_button, $product );
						
						if( $show_add_to_cart_button ){
						?>
                        <table class="ec_product_quickview_content_quantity">
                        	<tr>
                            	<td>
                        			<input type="button" value="-" class="ec_minus" onclick="ec_minus_quantity( '<?php echo $product->model_number; ?>', <?php echo $product->min_purchase_quantity; ?> );" />
                            	</td>
                                <td>
                                    <input type="number" value="<?php if( $product->min_purchase_quantity > 0 ){ echo $product->min_purchase_quantity; }else{ echo '1'; } ?>" name="quantity" id="ec_quantity_<?php echo $product->model_number; ?>" autocomplete="off" step="1" min="<?php if( $product->min_purchase_quantity > 0 ){ echo $product->min_purchase_quantity; }else{ echo '1'; } ?>"<?php if( $product->show_stock_quantity || $product->max_purchase_quantity){ ?> max="<?php if( $product->max_purchase_quantity > 0 ){ echo $product->max_purchase_quantity; }else{ echo $product->stock_quantity; } ?>"<?php }?> class="ec_quantity" />
                                </td>
                                <td>
                            		<input type="button" value="+" class="ec_plus" onclick="ec_plus_quantity( '<?php echo $product->model_number; ?>', <?php echo $product->show_stock_quantity; ?>, <?php if( $product->max_purchase_quantity > 0 ){ echo $product->max_purchase_quantity; }else if( $product->show_stock_quantity ){ echo $product->stock_quantity; }else{ echo 1000000; } ?> );" />
                                </td>
                            </tr>
                            <tr>
                            	<td colspan="3">
                                	<input type="button" value="<?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_add_to_cart' ); ?>" onclick="<?php if( trim( get_option( 'ec_option_fb_pixel' ) ) != '' ){
										echo "fbq('track', 'AddToCart', { content_name: '" . $product->title . "', contents: [{id: '" . $product->product_id . "', quantity: jQuery( document.getElementById( 'ec_quantity_" . $product->model_number . "' ) ).val( ), item_price: " . number_format( $product->price, 2, '.', '' ) . "}], content_type: 'product', value: Number( jQuery( document.getElementById( 'ec_quantity_" . $product->model_number . "' ) ).val( ) * " . number_format( $product->price, 2, '.', '' ) . " ).toFixed( 2 ), currency: '" . $GLOBALS['currency']->get_currency_code( ) . "' });"; 
									}?> ec_add_to_cart( '<?php echo $product->product_id; ?>', '<?php echo $product->model_number; ?>', jQuery( document.getElementById( 'ec_quantity_<?php echo $product->model_number; ?>' ) ).val( ), <?php echo $product->show_stock_quantity; ?>, <?php echo $product->min_purchase_quantity; ?>, <?php if( $product->max_purchase_quantity > 0 ){ echo $product->max_purchase_quantity; }else if( $product->show_stock_quantity ){ echo $product->stock_quantity; }else{ echo 1000000; } ?> );" />
                                    
                                     <?php if( ( $product->show_stock_quantity || $product->use_optionitem_quantity_tracking ) && get_option( 'ec_option_show_stock_quantity' ) ){ ?><div class="ec_details_stock_total"><span id="ec_details_stock_quantity"><?php echo $product->stock_quantity; ?></span> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_left_in_stock' ); ?></div><?php }?>
                                    
                                    <?php if( $product->min_purchase_quantity > 1 ){ ?><div class="ec_details_min_purchase_quantity"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_minimum_quantity_text1' ); ?> <?php echo $product->min_purchase_quantity; ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_minimum_quantity_text2' ); ?></div><?php }?>
                                    
                                    <?php if( $product->max_purchase_quantity > 0 ){ ?><div class="ec_details_min_purchase_quantity"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_maximum_quantity_text1' ); ?> <?php echo $product->max_purchase_quantity; ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_maximum_quantity_text2' ); ?></div><?php }?>
            
									<?php if( $product->handling_price > 0 ){ ?><div class="ec_details_handling_fee"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_handling_fee_notice1' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $product->handling_price ); ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_handling_fee_notice2' ); ?></div><?php }?>
                                    
                                </td>
                             </tr>
                        </table>
                        <?php }?>
                        
                        <?php }else if( $product->allow_backorders ){ ?>
						<div class="ec_details_option_row_error" id="ec_addtocart_quantity_exceeded_error_<?php echo $product->model_number; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_maximum_quantity' ); ?></div>
            			<div class="ec_details_option_row_error" id="ec_addtocart_quantity_minimum_error_<?php echo $product->model_number; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_minimum_quantity_text1' ); ?> <?php echo $product->min_purchase_quantity; ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_minimum_quantity_text2' ); ?></div>
                        
                        <?php
					
						$show_add_to_cart_button = true;
						$show_add_to_cart_button = apply_filters( 'wp_easycart_product_show_add_to_cart_button', $show_add_to_cart_button, $product );
						
						if( $show_add_to_cart_button ){
						?>
                        <table class="ec_product_quickview_content_quantity">
                        	<tr>
                            	<td>
                        			<input type="button" value="-" class="ec_minus" onclick="ec_minus_quantity( '<?php echo $product->model_number; ?>', <?php echo $product->min_purchase_quantity; ?> );" />
                            	</td>
                                <td>
                                    <input type="number" value="<?php if( $product->min_purchase_quantity > 0 ){ echo $product->min_purchase_quantity; }else{ echo '1'; } ?>" name="quantity" id="ec_quantity_<?php echo $product->model_number; ?>" autocomplete="off" step="1" min="<?php if( $product->min_purchase_quantity > 0 ){ echo $product->min_purchase_quantity; }else{ echo '1'; } ?>"<?php if( $product->show_stock_quantity ){ ?> max="1000000"<?php }?> class="ec_quantity" />
                                </td>
                                <td>
                            		<input type="button" value="+" class="ec_plus" onclick="ec_plus_quantity( '<?php echo $product->model_number; ?>', <?php echo $product->show_stock_quantity; ?>, <?php if( $product->max_purchase_quantity > 0 ){ echo $product->max_purchase_quantity; }else{ echo $product->stock_quantity; } ?> );" />
                                </td>
                            </tr>
                            <tr>
                            	<td colspan="3">
                                	<input type="button" value="BACKORDER" onclick="<?php if( trim( get_option( 'ec_option_fb_pixel' ) ) != '' ){
										echo "fbq('track', 'AddToCart', { content_name: '" . $product->title . "', contents: [{id: '" . $product->product_id . "', quantity: jQuery( document.getElementById( 'ec_quantity_" . $product->model_number . "' ) ).val( ), item_price: " . number_format( $product->price, 2, '.', '' ) . "}], content_type: 'product', value: Number( jQuery( document.getElementById( 'ec_quantity_" . $product->model_number . "' ) ).val( ) * " . number_format( $product->price, 2, '.', '' ) . " ).toFixed( 2 ), currency: '" . $GLOBALS['currency']->get_currency_code( ) . "' });"; 
									}?> ec_add_to_cart( '<?php echo $product->product_id; ?>', '<?php echo $product->model_number; ?>', jQuery( document.getElementById( 'ec_quantity_<?php echo $product->model_number; ?>' ) ).val( ), <?php echo $product->show_stock_quantity; ?>, <?php echo $product->min_purchase_quantity; ?>, <?php if( $product->max_purchase_quantity > 0 ){ echo $product->max_purchase_quantity; }else{ echo $product->stock_quantity; } ?> );" />
                                    
                                     <?php if( $product->show_stock_quantity || $product->use_optionitem_quantity_tracking ){ ?><div class="ec_details_stock_total"><span id="ec_details_stock_quantity">Out of Stock<?php if( $product->backorder_fill_date != "" ){ ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_backorder_until' ); ?> <?php echo $product->backorder_fill_date; ?><?php }?></span></div><?php }?>
                                    
                                    <?php if( $product->min_purchase_quantity > 1 ){ ?><div class="ec_details_min_purchase_quantity"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_minimum_quantity_text1' ); ?> <?php echo $product->min_purchase_quantity; ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_minimum_quantity_text2' ); ?></div><?php }?>
                                    
                                    <?php if( $product->max_purchase_quantity > 0 ){ ?><div class="ec_details_min_purchase_quantity"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_maximum_quantity_text1' ); ?> <?php echo $product->max_purchase_quantity; ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_maximum_quantity_text2' ); ?></div><?php }?>
            
									<?php if( $product->handling_price > 0 ){ ?><div class="ec_details_handling_fee"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_handling_fee_notice1' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $product->handling_price ); ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_handling_fee_notice2' ); ?></div><?php }?>
                                    
                                </td>
                             </tr>
                        </table>
                        <?php }?>
                            
						<?php }else{ ?>
                        <div class="ec_out_of_stock"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_out_of_stock' ); ?></div>
                        <?php }?>
                        
                        <?php do_action( 'wp_easycart_after_quick_add_to_cart_button', $product ); ?>
                        
                    </div>
                </div>
                <div class="ec_product_quickview_close"><input type="button" onclick="ec_product_hide_quick_view_link( '<?php echo $product->model_number; ?>' );" value="x"></div>
            </div>
        </div>
	</div>
</div>
<script>
jQuery( document.getElementById( "ec_product_quickview_container_<?php echo $product->model_number; ?>" ) ).appendTo( document.body );
</script>
<?php }  ?>


<?php ///// Admin editor /// ?>
<?php if( $admin_access ){ ?>
<div id="ec_product_editor_<?php echo $product->model_number; ?>" class="ec_product_editor" data-changes-made="0" data-model-number="<?php echo $product->model_number; ?>">
	<div><strong>Image Hover Effect</strong></div><div><select class="no_wrap" name="ec_product_image_hover_type" id="ec_product_image_hover_type_<?php echo $product->model_number; ?>" onchange="ec_admin_update_image_hover_effect( '<?php echo $product->model_number; ?>' );" data-default="<?php echo $product->image_hover_type; ?>"><option value="1"<?php if( $product->image_hover_type == '1' ){ echo ' selected="selected"'; }?>>Image Flip</option><option value="2"<?php if( $product->image_hover_type == '2' ){ echo ' selected="selected"'; }?>>Image Crossfade</option><option value="3"<?php if( $product->image_hover_type == '3' ){ echo ' selected="selected"'; }?>>Lighten</option><option value="5"<?php if( $product->image_hover_type == '5' ){ echo ' selected="selected"'; }?>>Image Grow</option><option value="6"<?php if( $product->image_hover_type == '6' ){ echo ' selected="selected"'; }?>>Image Shrink</option><option value="7"<?php if( $product->image_hover_type == '7' ){ echo ' selected="selected"'; }?>>Grey-Color</option><option value="8"<?php if( $product->image_hover_type == '8' ){ echo ' selected="selected"'; }?>>Brighten</option><option value="9"<?php if( $product->image_hover_type == '9' ){ echo ' selected="selected"'; }?>>Image Slide</option><option value="10"<?php if( $product->image_hover_type == '10' ){ echo ' selected="selected"'; }?>>FlipBook</option><option value="4"<?php if( $product->image_hover_type == '4' ){ echo ' selected="selected"'; }?>>No Effect</option></select></div>
    
    <div><strong>Image Effect</strong></div><div><select class="no_wrap" name="ec_product_image_effect_type" id="ec_product_image_effect_type_<?php echo $product->model_number; ?>" onchange="ec_admin_update_image_effect_type( '<?php echo $product->model_number; ?>' );" data-default="<?php echo $product->image_effect_type; ?>"><option value="none"<?php if( $product->image_effect_type == "none" ){ echo ' selected="selected"'; }?>>None</option><option value="border"<?php if( $product->image_effect_type == "border" ){ echo ' selected="selected"'; }?>>Border</option><option value="shadow"<?php if( $product->image_effect_type == "shadow" ){ echo ' selected="selected"'; }?>>Shadow</option></select></div>
    
    <div><strong>Tag Type</strong></div><div><select class="no_wrap" name="ec_product_tag_type" id="ec_product_tag_type_<?php echo $product->model_number; ?>" value="<?php echo $product->tag_type; ?>" onchange="ec_admin_update_tag_type( '<?php echo $product->model_number; ?>' );" data-default="<?php echo $product->tag_type; ?>"><option value="0"<?php if( $product->tag_type == "0" ){ echo ' selected="selected"'; }?>>No Tag</option><option value="1"<?php if( $product->tag_type == "1" ){ echo ' selected="selected"'; }?>>Round Tag</option><option value="2"<?php if( $product->tag_type == "2" ){ echo ' selected="selected"'; }?>>Square Tag</option><option value="3"<?php if( $product->tag_type == "3" ){ echo ' selected="selected"'; }?>>Diagonal Tag</option><option value="4"<?php if( $product->tag_type == "4" ){ echo ' selected="selected"'; }?>>Classy Tag</option></select></div>
    
    <div><strong>Tag Text</strong></div><div><input type="text" name="ec_product_tag_text" id="ec_product_tag_text_<?php echo $product->model_number; ?>" value="<?php echo $product->tag_text; ?>" onkeypress="ec_admin_update_tag_text( '<?php echo $product->model_number; ?>' );" onchange="ec_admin_update_tag_text( '<?php echo $product->model_number; ?>' );" data-default="<?php echo $product->tag_text; ?>" /></div>
    
    <div><span style="float:left; width:50%;"><strong>Tag Color</strong></span><span style="float:right; width:50%;"><strong>Tag Text Color</strong></span></div>
    
    <div class="ec_admin_product_color_selection"><span style="float:left; width:50%;"><input type="color" name="ec_product_tag_color" id="ec_product_tag_color_<?php echo $product->model_number; ?>" value="<?php echo $product->tag_bg_color; ?>" onchange="ec_admin_update_tag_color( '<?php echo $product->model_number; ?>' );" data-default="<?php echo $product->tag_bg_color; ?>" /></span><span style="float:right; width:50%;"><input type="color" name="ec_product_tag_text_color" id="ec_product_tag_text_color_<?php echo $product->model_number; ?>" value="<?php echo $product->tag_text_color; ?>" onchange="ec_admin_update_tag_color( '<?php echo $product->model_number; ?>' );" data-default="<?php echo $product->tag_text_color; ?>" /></span></div>
    
    <div><input type="submit" value="SAVE" onclick="ec_admin_save_product_display( '<?php echo $product->model_number; ?>' ); return false;" style="float:left; width:45%;" /><input type="submit" value="CANCEL" onclick="ec_admin_cancel_product_display( '<?php echo $product->model_number; ?>' ); return false;" style="float:right; width:45%;" /></div>
    
</div>

<a href="#" target="_blank" id="ec_product_editor_openclose_button_<?php echo $product->model_number; ?>" class="ec_product_openclose" onclick="ec_product_editor_openclose( '<?php echo $product->model_number; ?>' ); return false;"><div class="dashicons dashicons-admin-generic"></div></a>

<?php } // Close Admin Editor /// ?>

<?php ///////TAGS CODE//////// ?>
<?php if( $admin_access || $product->tag_type == 1 ){ ?>
	<span class="ec_tag1" style="color:<?php echo $product->tag_text_color; ?>; background: <?php echo $product->tag_bg_color; ?> !important;<?php if( $product->tag_type != 1 ){ ?> display:none;<?php }?>"><?php echo $GLOBALS['language']->convert_text( $product->tag_text ); ?></span>
<?php }?>

<?php if( $admin_access || $product->tag_type == 2 ){ ?>
	<div class="ec_tag2"<?php if( $product->tag_type != 2 ){ ?> style="display:none;"<?php }?>><span style="background: none repeat scroll 0 0 <?php echo $product->tag_bg_color; ?>; color: <?php echo $product->tag_text_color; ?>;"><?php echo $GLOBALS['language']->convert_text( $product->tag_text ); ?></span></div>
<?php }?>

<?php if( $admin_access || $product->tag_type == 3 ){ ?>
	<div class="ec_tag3" style="border-bottom-color:<?php echo $product->tag_bg_color; ?>; color:<?php echo $product->tag_text_color; ?>;<?php if( $product->tag_type != 3 ){ ?> display:none;<?php }?>"><span style="background-color:<?php echo $product->tag_bg_color; ?>;"><?php echo $GLOBALS['language']->convert_text( $product->tag_text ); ?></span></div>
<?php }?>

<?php if( $admin_access || $product->tag_type == 4 ){ ?>
	<div class="ec_tag4"<?php if( $product->tag_type != 4 ){ ?> style="display:none;"<?php }?>><span style="color: <?php echo $product->tag_text_color; ?>;"><?php echo $GLOBALS['language']->convert_text( $product->tag_text ); ?></span></div>
<?php }?>
	
	<div style="padding:0px; margin:auto; vertical-align:middle;<?php if( $product_type == 0 ){ ?> display:none;<?php }?>" class="ec_product_type<?php echo $product_type; ?>" id="ec_product_image_<?php echo $product->model_number; ?>">
    	
        <?php ///////////////// IMAGE HOLDER///////////// ?>
        <div id="ec_product_image_effect_<?php echo $product->model_number; ?>" class="ec_image_container_<?php echo $product->image_effect_type; ?> ec_dynamic_image_height<?php if( $dynamic_image_sizing ){ ?> dynamic_height_rule<?php }?>">
        	<?php do_action( 'wp_easycart_product_image_holder_pre', $product ); ?>
        	<a href="<?php echo $product->get_product_link( ); ?>" class="ec_image_link_cover"></a>
        
        	<?php ///////////////// IMAGE OPTIONS /////////////////////// ?>
            <?php
			$image_types = array( '', 'ec_flip_container', 'ec_fade_container', 'ec_single_fade_container', 'ec_single_none_container', 'ec_single_grow_container', 'ec_single_shrink_container', 'ec_single_btw_container', 'ec_single_brighten_container', 'ec_slide_container', 'ec_flipbook' );
			?>
            <div class="ec_product_image_display_type <?php if( $product->image_hover_type > 0 && $product->image_hover_type < 11 ){ echo $image_types[ $product->image_hover_type ]; }else{ echo $image_types[4]; } ?> ec_dynamic_image_height<?php if( $dynamic_image_sizing ){ ?> dynamic_height_rule<?php }?>"<?php if( $admin_access || $product->image_hover_type == 10 ){ ?> data-image-list="<?php if( $product->images->use_optionitem_images ){ 
						for( $i=0; $i<count( $product->images->imageset ); $i++ ){ 
							if( $i > 0 ){ 
								echo ",";
							}
							if( substr( $product->images->imageset[$i]->image1, 0, 7 ) == 'http://' || substr( $product->images->imageset[$i]->image1, 0, 8 ) == 'https://' ){
								echo $product->images->imageset[$i]->image1;
							}else{
								echo plugins_url( "/wp-easycart-data/products/pics1/" . $product->images->imageset[$i]->image1 );
							}
						}
					}else{ 
						echo $product->get_first_image_url( );
						if( trim( $product->images->image2 ) != "" ){ 
							if( substr( $product->images->image2, 0, 7 ) == 'http://' || substr( $product->images->image2, 0, 8 ) == 'https://' ){
								echo "," . $product->images->image2;
							}else{
								echo "," . plugins_url( "/wp-easycart-data/products/pics2/" . $product->images->image2 ); 
							}
						} 
						if( trim( $product->images->image3 ) != "" ){ 
							if( substr( $product->images->image3, 0, 7 ) == 'http://' || substr( $product->images->image3, 0, 8 ) == 'https://' ){
								echo "," . $product->images->image3;
							}else{
								echo "," . plugins_url( "/wp-easycart-data/products/pics3/" . $product->images->image3 ); 
							}
						}
						if( trim( $product->images->image4 ) != "" ){ 
							if( substr( $product->images->image4, 0, 7 ) == 'http://' || substr( $product->images->image4, 0, 8 ) == 'https://' ){
								echo "," . $product->images->image4;
							}else{
								echo "," . plugins_url( "/wp-easycart-data/products/pics4/" . $product->images->image4 ); 
							}
						} 
						if( trim( $product->images->image5 ) != "" ){ 
							if( substr( $product->images->image5, 0, 7 ) == 'http://' || substr( $product->images->image5, 0, 8 ) == 'https://' ){
								echo "," . $product->images->image5;
							}else{
								echo "," . plugins_url( "/wp-easycart-data/products/pics5/" . $product->images->image5 ); 
							}
						} 
					} ?>"<?php }?>>
            	
                <?php if( $admin_access || $product->image_hover_type == 10 ){ ?>
            	<div class="ec_flipbook_left">&#65513;</div>
                <div class="ec_flipbook_right">&#65515;</div>
                <img src="<?php echo $product->get_first_image_url( ); ?>" alt="<?php echo $product->title; ?>" class="ec_flipbook_image skip-lazy" />
                <?php }?>
                
                <?php if( $admin_access || $product->image_hover_type != 10 ){ ?>
                <div class="ec_dynamic_image_height ec_product_image_container <?php if( $dynamic_image_sizing ){ ?> dynamic_height_rule<?php }?>">
					
                	<?php if( $admin_access || $product->image_hover_type == 9 ){ ?>
                    <img src="<?php echo $product->get_first_image_url( ); ?>" class="ec_image_auto_sizer skip-lazy" alt="<?php echo $product->title; ?>" />
            		<?php }?>
                
                	<div class="ec_dynamic_image_height ec_product_image_1 <?php if( $dynamic_image_sizing ){ ?> dynamic_height_rule<?php }?>"><img src="<?php echo $product->get_first_image_url( ); ?>" alt="<?php echo $product->title; ?>" class="skip-lazy" /></div>
					
                    <?php if( $admin_access || $product->image_hover_type == 1 || $product->image_hover_type == 2 || $product->image_hover_type == 9 ){ ?>
                    <div class="ec_dynamic_image_height ec_product_image_2 <?php if( $dynamic_image_sizing ){ ?> dynamic_height_rule<?php }?>"><img src="<?php echo $product->get_second_image_url( ); ?>" alt="<?php echo $product->title; ?>" class="skip-lazy" /></div>
                    <?php }?>
                    
        		</div>
                <?php }?>
            </div>
        	<?php do_action( 'wp_easycart_product_image_holder_post', $product ); ?>
        </div>
        
        <?php /////// START CONTENT AREA //// ?>
        <div class="ec_product_meta_type6">
        	<h3 class="ec_product_title"><a href="<?php echo $product->get_product_link( ); ?>" class="ec_image_link_cover"><?php echo $product->title; ?></a></h3>
            <?php do_action( 'wpeasycart_after_product_title', $product ); ?>
        	<div class="ec_product_description"><?php echo nl2br( stripslashes( $product->short_description)); ?></div>
            <?php if( $product->login_for_pricing && !$product->is_login_for_pricing_valid( ) ){
					  // No Pricing
					  
				  }else if( ( $product->is_catalog_mode && get_option( 'ec_option_hide_price_seasonal' ) ) || 
					  ( $product->is_inquiry_mode && get_option( 'ec_option_hide_price_inquiry' ) ) ){ // don't show price
			  	  
				  }else if( $product->show_custom_price_range ){ ?>
                     <div class="ec_price_container_type<?php echo $product_type; ?>">
					 <?php if( $product->list_price > 0 ){ ?>
                        <span class="ec_list_price"><?php 
                    
                                $list_price = $GLOBALS['currency']->get_currency_display( $product->list_price );
                                $list_price = apply_filters( 'wp_easycart_product_list_price_display', $list_price, $product->list_price );
                                echo $list_price;
                        
                            ?></span>
                     <?php }?>
                     <?php echo $GLOBALS['currency']->get_currency_display( $product->price_range_low ) . ' - ' . $GLOBALS['currency']->get_currency_display( $product->price_range_high ); ?>
                     </div>
                         
                  <?php }else{ ?>
            <div class="ec_price_container">
				<?php if( $product->list_price > 0 ){ ?>
                    <span class="ec_list_price"><?php 
				
							$list_price = $GLOBALS['currency']->get_currency_display( $product->list_price );
							$list_price = apply_filters( 'wp_easycart_product_list_price_display', $list_price, $product->list_price );
							echo $list_price;
					
						?></span>
                <?php }?>
                <span class="ec_price"><?php 
				
					$display_price = $GLOBALS['currency']->get_currency_display( $product->price );
					if( $product->pricing_per_sq_foot && !get_option( 'ec_option_enable_metric_unit_display' ) ){ 
						$display_price .= "/sq ft";
					}else if( $product->pricing_per_sq_foot && get_option( 'ec_option_enable_metric_unit_display' ) ){ 
						$display_price .= "/sq m";
					}
					
					$display_price = apply_filters( 'wp_easycart_product_price_display', $display_price, $product->price, $product->product_id );
					echo $display_price;
				
				?><?php if( $GLOBALS['ec_vat_included'] && $product->vat_rate == 1 && get_option( 'ec_option_show_multiple_vat_pricing' ) ){ ?> <span class="ec_inc_vat_text"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_inc_vat_text' ); ?></span><?php }else if( $GLOBALS['ec_vat_added'] && $product->vat_rate == 1 && get_option( 'ec_option_show_multiple_vat_pricing' ) ){ ?> <span class="ec_inc_vat_text"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_excluding_vat_text' ); ?></span><?php }?></span>
            </div>
            <?php }?>
            
            <?php if( $admin_access || $use_quickview ){ ?>
        	<div class="ec_product_quickview_container"><span class="ec_product_quickview"<?php if( !$use_quickview ){ echo " style='display:none;'"; } ?>><input type="button" onclick="ec_product_show_quick_view_link( '<?php echo $product->model_number; ?>' );" value="<?php echo $GLOBALS['language']->get_text( 'product_page', 'product_quick_view' ); ?>" /> </span></div>
        	<?php }?>
			
			<?php if( apply_filters( 'wp_easycart_catalog_display', get_option( 'ec_option_display_as_catalog' ) ) ){
			// Show nothing
			
			}else if( $product->login_for_pricing && !$product->is_login_for_pricing_valid( ) && $GLOBALS['ec_user']->user_id != 0 ){ ?>
            <div class="ec_seasonal_mode ec_call_for_pricing"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_page_login_for_price_no_access' ); ?></div>
            
            <?php }else if( $product->login_for_pricing && !$product->is_login_for_pricing_valid( ) ){ ?>
                <div class="ec_product_addtocart_container"><span class="ec_product_addtocart"><a href="<?php echo $this->account_page; ?>"><?php echo ( $product->login_for_pricing_label != '' ) ? $product->login_for_pricing_label : $GLOBALS['language']->get_text( 'product_page', 'product_page_login_for_price' ); ?></a></span></div>
                
            <?php }else if( $product->is_catalog_mode ){ ?>
			<div class="ec_seasonal_mode"><?php echo $product->catalog_mode_phrase; ?></div>	

			<?php }else if( $product->is_deconetwork ){ ?>
			<div class="ec_product_quickview_content_add_to_cart ec_product_addtocart_no_margin"><a href="<?php echo $product->get_deconetwork_link( ); ?>"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_design_now' ); ?></a></div>
			
			<?php }else if( ( $product->in_stock( ) || $product->allow_backorders ) && ( $product->has_options( ) || $product->is_giftcard || $product->is_inquiry_mode || $product->is_donation || $product->min_purchase_quantity > 1 || apply_filters( 'wp_easycart_product_force_select_options', false, $product->product_id ) ) ){ ?>
            <div class="ec_product_addtocart_container"><span class="ec_product_addtocart ec_product_addtocart_no_margin"><a href="<?php echo $product->get_product_link( ); ?>" target="_self"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_select_options' ); ?></a></span></div>
            
            <?php }else if( $product->in_stock( ) && $product->is_subscription_item ){ // && !class_exists( "ec_stripe" ) ){ ?>
            <div class="ec_product_addtocart_container"><span class="ec_product_addtocart ec_product_addtocart_no_margin"><a href="<?php echo $product->get_subscription_link( ); ?>" target="_self"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_sign_up_now' ); ?></a></span></div>
            
            <?php }else if( $product->in_stock( ) ){ ?>
            <div class="ec_product_addtocart_container">
            	<span class="ec_product_addtocart">
                	<a id="ec_add_to_cart_type6_<?php echo $product->product_id; ?>" href="<?php echo $product->get_add_to_cart_link( ); ?>" onclick="<?php if( trim( get_option( 'ec_option_fb_pixel' ) ) != '' ){
						echo "fbq('track', 'AddToCart', { content_name: '" . $product->title . "', contents: [{id: '" . $product->product_id . "', quantity: 1, item_price: " . number_format( $product->price, 2, '.', '' ) . "}], content_type: 'product', value: " . number_format( $product->price, 2, '.', '' ) . ", currency: '" . $GLOBALS['currency']->get_currency_code( ) . "' });";
					}?> ec_add_to_cart( '<?php echo $product->product_id; ?>', '<?php echo $product->model_number; ?>', 1 ); return false;"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_add_to_cart' ); ?></a>
            		<a id="ec_added_to_cart_type6_<?php echo $product->product_id; ?>" href="<?php echo $product->cart_page; ?>" class="ec_added_to_cart_button"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_added_to_cart' ); ?></a>
            	</span>
            </div>
           
            <?php }else if( !$product->in_stock( ) && $product->allow_backorders ){ ?>
			<div class="ec_product_addtocart_container">
            	<span class="ec_product_addtocart">
                	<a id="ec_add_to_cart_type6_<?php echo $product->product_id; ?>" href="<?php echo $product->get_add_to_cart_link( ); ?>" onclick="<?php if( trim( get_option( 'ec_option_fb_pixel' ) ) != '' ){
						echo "fbq('track', 'AddToCart', { content_name: '" . $product->title . "', contents: [{id: '" . $product->product_id . "', quantity: 1, item_price: " . number_format( $product->price, 2, '.', '' ) . "}], content_type: 'product', value: " . number_format( $product->price, 2, '.', '' ) . ", currency: '" . $GLOBALS['currency']->get_currency_code( ) . "' });";
					}?> ec_add_to_cart( '<?php echo $product->product_id; ?>', '<?php echo $product->model_number; ?>', 1 ); return false;"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_backorder_button' ); ?></a>
            		<a id="ec_added_to_cart_type6_<?php echo $product->product_id; ?>" href="<?php echo $product->cart_page; ?>" class="ec_added_to_cart_button"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_added_to_cart' ); ?></a>
                </span>
            </div>
             
            <div class="ec_out_of_stock ec_oos_type_6"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_out_of_stock' ); ?><?php if( $product->backorder_fill_date != "" ){ ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_backorder_until' ); ?> <?php echo $product->backorder_fill_date; ?><?php }?></div>
            
			<?php }else{ ?>
            <div class="ec_out_of_stock ec_oos_type_6"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_out_of_stock' ); ?></div>
            <?php }?>
        </div>
        
        <?php if( get_option( 'ec_option_short_description_on_product' ) && strlen( trim( $product->short_description ) ) > 0 ){ ?>
        <div class="ec_product_basic_description"><?php echo nl2br( stripslashes( $product->short_description ) ); ?></div>
        <?php }?>
        
        <h3 class="ec_product_title_type<?php echo $product_type; ?>"><a href="<?php echo $product->get_product_link( ); ?>" class="ec_image_link_cover"><?php echo $product->title; ?></a></h3>
        <?php do_action( 'wpeasycart_after_product_title', $product ); ?>
        <?php if( $show_rating && $product->get_rating( ) > 0 ){ 
			$average = $product->get_rating( );
		?>
        <div class="ec_product_stars_type<?php echo $product_type; ?>" title="Rated <?php echo number_format( floatval( $average ), 2 ); ?> out of 5"><span><?php $product->display_product_stars(); ?></span></div>
        <?php }?>
        
		<?php if( $product->login_for_pricing && !$product->is_login_for_pricing_valid( ) ){
				// No Pricing
				
			  }else if( ( $product->is_catalog_mode && get_option( 'ec_option_hide_price_seasonal' ) ) || 
					  ( $product->is_inquiry_mode && get_option( 'ec_option_hide_price_inquiry' ) ) ){ // don't show price
			  
			  }else if( $product->show_custom_price_range ){ ?>
              	 <div class="ec_price_container_type<?php echo $product_type; ?>">
				 <?php if( $product->list_price > 0 ){ ?>
                    <span class="ec_list_price_type<?php echo $product_type; ?>"><?php 
                
                            $list_price = $GLOBALS['currency']->get_currency_display( $product->list_price );
                            $list_price = apply_filters( 'wp_easycart_product_list_price_display', $list_price, $product->list_price );
                            echo $list_price;
                    
                        ?></span>
                 <?php }?>
                     <span class="ec_price_type<?php echo $product_type; ?>">
                     <?php echo $GLOBALS['currency']->get_currency_display( $product->price_range_low ) . ' - ' . $GLOBALS['currency']->get_currency_display( $product->price_range_high ); ?>
                     </span>
                 </div>
					 
			  <?php }else{ ?>
        <div class="ec_price_container_type<?php echo $product_type; ?>">
			<?php if( count( $product->pricetiers ) > 0 && get_option( 'ec_option_tiered_price_format' ) ){ ?>
				<span class="ec_price_type<?php echo $product_type; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_as_low_as' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $product->pricetiers[count( $product->pricetiers )-1][0] ); ?></span>
                
			<?php }else{ // Non-Price-Tiered Pricing Display
			
			if( $product->list_price > 0 ){ ?>
        		<span class="ec_list_price_type<?php echo $product_type; ?>"><?php 
				
					$list_price = $GLOBALS['currency']->get_currency_display( $product->list_price );
					$list_price = apply_filters( 'wp_easycart_product_list_price_display', $list_price, $product->list_price );
					echo $list_price;
					
				?></span>
       		<?php }?>
        	<span class="ec_price_type<?php echo $product_type; ?>"><?php 
			
				$display_price = $GLOBALS['currency']->get_currency_display( $product->price );
				if( $product->pricing_per_sq_foot && !get_option( 'ec_option_enable_metric_unit_display' ) ){ 
					$display_price .= "/sq ft";
				}else if( $product->pricing_per_sq_foot && get_option( 'ec_option_enable_metric_unit_display' ) ){ 
					$display_price .= "/sq m";
				}
				
				$display_price = apply_filters( 'wp_easycart_product_price_display', $display_price, $product->price, $product->product_id );
				echo $display_price;
				
			?><?php if( $GLOBALS['ec_vat_included'] && $product->vat_rate == 1 && get_option( 'ec_option_show_multiple_vat_pricing' ) ){ ?> <span class="ec_inc_vat_text"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_inc_vat_text' ); ?></span><?php }else if( $GLOBALS['ec_vat_added'] && $product->vat_rate == 1 && get_option( 'ec_option_show_multiple_vat_pricing' ) ){ ?> <span class="ec_inc_vat_text"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_excluding_vat_text' ); ?></span><?php }?></span>
        	<?php } // Close Tiered Pricing If ?>
        </div>
        <?php }?>
        
        <?php if( $product->is_subscription_item && $product->trial_period_days > 0 ){ ?>
        <div class="ec_product_quickview_trial_notice"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_page_start_trial_1' ); ?> <?php echo $product->trial_period_days; ?> <?php echo $GLOBALS['language']->get_text( 'product_page', 'product_page_start_trial_2' ); ?></div>
        <?php }?>
        
        <?php if( apply_filters( 'wp_easycart_catalog_display', get_option( 'ec_option_display_as_catalog' ) ) ){
		// Show nothing
		
		}else if( $product->login_for_pricing && !$product->is_login_for_pricing_valid( ) && $GLOBALS['ec_user']->user_id != 0 ){ ?>
        <div class="ec_seasonal_mode ec_call_for_pricing"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_page_login_for_price_no_access' ); ?></div>
            
        <?php }else if( $product->login_for_pricing && !$product->is_login_for_pricing_valid( ) ){ ?>
        <div class="ec_product_addtocart_container"><span class="ec_product_addtocart"><a href="<?php echo $this->account_page; ?>"><?php echo ( $product->login_for_pricing_label != '' ) ? $product->login_for_pricing_label : $GLOBALS['language']->get_text( 'product_page', 'product_page_login_for_price' ); ?></a></span></div>
            
        <?php }else if( $product->is_catalog_mode ){ ?>
		<div class="ec_seasonal_mode"<?php if( $product_type == 6 ){ echo ' style="display:none;"'; } ?>><?php echo $product->catalog_mode_phrase; ?></div>	
		
		<?php }else if( $product->is_deconetwork ){ ?>
        <div class="ec_product_addtocart_container"><span class="ec_product_addtocart ec_product_addtocart_no_margin"><a href="<?php echo $product->get_deconetwork_link( ); ?>"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_design_now' ); ?></a></span></div>
        
        <?php }else if( ( $product->in_stock( ) || $product->allow_backorders ) && ( $product->has_options( ) || $product->is_giftcard || $product->is_inquiry_mode || $product->is_donation || $product->min_purchase_quantity > 1 || apply_filters( 'wp_easycart_product_force_select_options', false, $product->product_id ) ) ){ ?>
        <?php
					
		$show_add_to_cart_button = true;
		$show_add_to_cart_button = apply_filters( 'wp_easycart_product_show_add_to_cart_button', $show_add_to_cart_button, $product );
		
		if( $show_add_to_cart_button ){
		?>
        <div class="ec_product_addtocart_container"><span class="ec_product_addtocart ec_product_addtocart_no_margin"><a href="<?php echo $product->get_product_link( ); ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_select_options' ); ?></a></span></div>
        <?php }?>
        
        <?php if( !$product->in_stock( ) && $product->allow_backorders ){ ?>
        <div class="ec_out_of_stock ec_oos_type_1"<?php if( $product_type == 6 ){ echo ' style="display:none;"'; } ?>><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_out_of_stock' ); ?><?php if( $product->backorder_fill_date != "" ){ ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_backorder_until' ); ?> <?php echo $product->backorder_fill_date; ?><?php }?></div>
        <?php }?>
        
        <?php }else if( $product->in_stock( ) && $product->is_subscription_item ){ // && !class_exists( "ec_stripe" ) ){ ?>
        <div class="ec_product_addtocart_container"><span class="ec_product_addtocart ec_product_addtocart_no_margin"><a href="<?php echo $product->get_subscription_link( ); ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_sign_up_now' ); ?></a></span></div>
        
		<?php }else if( $product->in_stock( ) ){ ?>
        <?php
					
		$show_add_to_cart_button = true;
		$show_add_to_cart_button = apply_filters( 'wp_easycart_product_show_add_to_cart_button', $show_add_to_cart_button, $product );
		
		if( $show_add_to_cart_button ){
		?>
        <div class="ec_product_addtocart_container">
        	<span class="ec_product_addtocart">
                <a id="ec_add_to_cart_<?php echo $product->product_id; ?>" href="<?php echo $product->get_add_to_cart_link( ); ?>" onclick="<?php if( trim( get_option( 'ec_option_fb_pixel' ) ) != '' ){
						echo "fbq('track', 'AddToCart', { content_name: '" . $product->title . "', contents: [{id: '" . $product->product_id . "', quantity: 1, item_price: " . number_format( $product->price, 2, '.', '' ) . "}], content_type: 'product', value: " . number_format( $product->price, 2, '.', '' ) . ", currency: '" . $GLOBALS['currency']->get_currency_code( ) . "' });";
					}?> ec_add_to_cart( '<?php echo $product->product_id; ?>', '<?php echo $product->model_number; ?>', 1 ); return false;"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_add_to_cart' ); ?></a>
                <a id="ec_added_to_cart_<?php echo $product->product_id; ?>" href="<?php echo $product->cart_page; ?>" class="ec_added_to_cart_button"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_added_to_cart' ); ?></a>
           	</span>
        </div>
		<?php }?>
        
		<?php }else if( $product->allow_backorders ){ ?>
        <?php
					
		$show_add_to_cart_button = true;
		$show_add_to_cart_button = apply_filters( 'wp_easycart_product_show_add_to_cart_button', $show_add_to_cart_button, $product );
		
		if( $show_add_to_cart_button ){
		?>
        <div class="ec_product_addtocart_container">
        	<span class="ec_product_addtocart">
            	<a id="ec_add_to_cart_<?php echo $product->product_id; ?>" href="<?php echo $product->get_add_to_cart_link( ); ?>" onclick="<?php if( trim( get_option( 'ec_option_fb_pixel' ) ) != '' ){
						echo "fbq('track', 'AddToCart', { content_name: '" . $product->title . "', contents: [{id: '" . $product->product_id . "', quantity: 1, item_price: " . number_format( $product->price, 2, '.', '' ) . "}], content_type: 'product', value: " . number_format( $product->price, 2, '.', '' ) . ", currency: '" . $GLOBALS['currency']->get_currency_code( ) . "' });";
					}?> ec_add_to_cart( '<?php echo $product->product_id; ?>', '<?php echo $product->model_number; ?>', 1 ); return false;"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_backorder_button' ); ?></a>
            	<a id="ec_added_to_cart_<?php echo $product->product_id; ?>" href="<?php echo $product->cart_page; ?>" class="ec_added_to_cart_button"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_added_to_cart' ); ?></a>
            </span>
        </div>
        <?php }?>
                        
        <?php do_action( 'wp_easycart_after_add_to_cart_button', $product ); ?>
        
        <div class="ec_out_of_stock ec_oos_type_1"<?php if( $product_type == 6 ){ echo ' style="display:none;"'; } ?>><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_out_of_stock' ); ?><?php if( $product->backorder_fill_date != "" ){ ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_backorder_until' ); ?> <?php echo $product->backorder_fill_date; ?><?php }?></div>
        
		<?php }else{ ?>
        <div class="ec_out_of_stock ec_oos_type_1"<?php if( $product_type == 6 ){ echo ' style="display:none;"'; } ?>><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_out_of_stock' ); ?></div>
        <?php }?>
        <?php if( $admin_access || $use_quickview ){ ?>
        <span class="ec_product_quickview" <?php if( $product_type == 3 || $product_type == 4 ){ ?>style="top:<?php $quickview_top = substr( $image_height_desktop, 0, -2 ); echo ( $quickview_top - 21 ) . "px"; ?>;<?php if( !$use_quickview ){ echo " display:none;"; } ?>"<?php }else{ ?><?php if( !$use_quickview ){ echo "style='display:none;'"; } ?><?php }?>><input type="button" onclick="ec_product_show_quick_view_link( '<?php echo $product->model_number; ?>' ); return false;" value="<?php echo $GLOBALS['language']->get_text( 'product_page', 'product_quick_view' ); ?>" /></span>
        <?php }?>
        
        <div class="ec_product_successfully_added_container" id="ec_product_added_<?php echo $product->model_number; ?>"><div class="ec_product_successfully_added"><div><?php echo $GLOBALS['language']->get_text( 'ec_success', 'add_to_cart_success' ); ?></div></div></div>
        
        <div class="ec_product_loader_container" id="ec_product_loader_<?php echo $product->model_number; ?>"><div class="ec_product_loader"><div><?php echo $GLOBALS['language']->get_text( 'ec_success', 'adding_to_cart' ); ?></div></div></div>
        
    </div>
    
</li>