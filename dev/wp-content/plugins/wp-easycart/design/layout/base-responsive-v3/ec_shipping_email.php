<html>


<head>


    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


    <style type='text/css'>


    <!--


		.style20 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; }


        .style22 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; }


		.ec_option_label{font-family: Arial, Helvetica, sans-serif; font-size:11px; font-weight:bold; }


		.ec_option_name{font-family: Arial, Helvetica, sans-serif; font-size:11px; }

	-->


    </style>


</head>


<body>

    <table width='539' border='0' align='center'>

        <tr>

            <td colspan='4' align='left' class='style22'>

                <img src='<?php echo $email_logo_url; ?>' style="max-height:250px; max-width:100%; height:auto;">

            </td>

        </tr>

		<tr>
            <td colspan='4' align='left' class='style22'>
                <p><br><?php echo $GLOBALS['language']->get_text( 'ec_shipping_email', 'shipping_dear' )?> <?php echo $order[0]->billing_first_name . " " . $order[0]->billing_last_name; ?>: </p>
                <p><?php echo $GLOBALS['language']->get_text( 'ec_shipping_email', 'shipping_subtitle1' )?> <strong><?php echo $order[0]->order_id; ?></strong> <?php echo $GLOBALS['language']->get_text( 'ec_shipping_email', 'shipping_subtitle2' )?><br>
                <?php if( $trackingnumber != '0' && $trackingnumber != 'Null' && $trackingnumber != 'NULL' && $trackingnumber != 'null' && $trackingnumber != NULL && $trackingnumber != '' ){ ?>
                <br><?php echo $GLOBALS['language']->get_text( 'ec_shipping_email', 'shipping_description' )?></p>
                <p><?php echo $GLOBALS['language']->get_text( 'ec_shipping_email', 'shipping_carrier' )?> <?php echo $shipcarrier; ?><br><?php echo $GLOBALS['language']->get_text( 'ec_shipping_email', 'shipping_tracking' )?> <?php echo $trackingnumber; ?></p>
                <?php } ?>
                <?php if( get_option( 'ec_option_show_email_on_receipt' ) ){ ?><p><strong><?php echo htmlspecialchars( $this->user_email, ENT_QUOTES ); ?></strong></p><?php }?>
            </td>
        </tr>

        <tr>

        	<td colspan='4' align='left' class='style20'>

            	<table width='100%' border='0' align='center' cellpadding='0' cellspacing='0'>

                	<tr>

                    	<td width='47%' bgcolor='#F3F1ED' class='style20'><?php echo $GLOBALS['language']->get_text( "ec_shipping_email", "shipping_billing_label" ); ?></td>

                        <td width='3%'>&nbsp;</td><td width='50%' bgcolor='#F3F1ED' class='style20'><?php if( get_option( 'ec_option_use_shipping' ) ){?><?php echo $GLOBALS['language']->get_text( "ec_shipping_email", "shipping_shipping_label" ); ?><?php }?></td>

                    </tr>

                    <tr>

                    	<td><span class='style22'><?php echo htmlspecialchars( $order[0]->billing_first_name, ENT_QUOTES ); ?> <?php echo htmlspecialchars( $order[0]->billing_last_name, ENT_QUOTES ); ?></span></td>

                        <td>&nbsp;</td>

                        <td><span class='style22'><?php if( get_option( 'ec_option_use_shipping' ) ){?><?php echo htmlspecialchars( $order[0]->shipping_first_name, ENT_QUOTES ); ?> <?php echo htmlspecialchars( $order[0]->shipping_last_name, ENT_QUOTES ); ?><?php }?></span></td>

                    </tr>
                    
                    <?php if( $order[0]->billing_company_name != "" || ( get_option( 'ec_option_use_shipping' ) && $order[0]->shipping_company_name != "" ) ){ ?>
                    <tr>

                    	<td><span class='style22'><?php echo htmlspecialchars( $order[0]->billing_company_name, ENT_QUOTES ); ?></span></td>

                        <td>&nbsp;</td>

                        <td><span class='style22'><?php if( get_option( 'ec_option_use_shipping' ) ){?><?php echo htmlspecialchars( $order[0]->shipping_company_name, ENT_QUOTES ); ?><?php }?></span></td>

                    </tr>
                    <?php }?>

                    <tr>

                    	<td><span class='style22'><?php echo htmlspecialchars( $order[0]->billing_address_line_1, ENT_QUOTES ); ?></span></td>

                        <td>&nbsp;</td>

                        <td><span class='style22'><?php if( get_option( 'ec_option_use_shipping' ) ){?><?php echo htmlspecialchars( $order[0]->shipping_address_line_1, ENT_QUOTES ); ?><?php }?></span></td>

                    </tr>

				    <?php if( $order[0]->billing_address_line_2 != "" || ( $order[0]->shipping_address_line_2 != "" && get_option( 'ec_option_use_shipping' ) ) ){ ?>

                    <tr>

                      <td class='style22'><?php echo htmlspecialchars( $order[0]->billing_address_line_2, ENT_QUOTES ); ?></td>

                      <td>&nbsp;</td>

                      <td class='style22'><?php if( get_option( 'ec_option_use_shipping' ) ){ ?><?php echo htmlspecialchars( $order[0]->shipping_address_line_2, ENT_QUOTES ); ?><?php }?></td>

                    </tr>

                    <?php }?>

                    <tr>

                    	<td><span class='style22'><?php echo htmlspecialchars( $order[0]->billing_city, ENT_QUOTES ); ?>, <?php echo htmlspecialchars( $order[0]->billing_state, ENT_QUOTES ); ?> <?php echo htmlspecialchars( $order[0]->billing_zip, ENT_QUOTES ); ?></span></td>

                        <td>&nbsp;</td>

                        <td><span class='style22'><?php if( get_option( 'ec_option_use_shipping' ) ){?><?php echo htmlspecialchars( $order[0]->shipping_city, ENT_QUOTES ); ?>, <?php echo htmlspecialchars( $order[0]->shipping_state, ENT_QUOTES ); ?> <?php echo htmlspecialchars( $order[0]->shipping_zip, ENT_QUOTES ); ?><?php }?></span></td>

                    </tr>

                    <tr>

                    	<td><span class='style22'><?php echo htmlspecialchars( $order[0]->billing_country_name, ENT_QUOTES ); ?></span></td>

                        <td>&nbsp;</td>

                        <td><span class='style22'><?php if( get_option( 'ec_option_use_shipping' ) ){?><?php echo htmlspecialchars( $order[0]->shipping_country_name, ENT_QUOTES ); ?><?php }?></span></td>

                    </tr>

                    <tr>

                    	<td><span class='style22'><?php echo htmlspecialchars( $order[0]->billing_phone, ENT_QUOTES ); ?></span></td>

                        <td>&nbsp;</td>

                        <td><span class='style22'><?php if( get_option( 'ec_option_use_shipping' ) ){?><?php echo htmlspecialchars( $order[0]->shipping_phone, ENT_QUOTES ); ?><?php }?></span></td>

                    </tr>
                    
                    <?php if( $order[0]->vat_registration_number != "" ){ ?>
                    
                    <tr>

                    	<td colspan="3"><span class='style22'><strong><?php echo $GLOBALS['language']->get_text( 'cart_billing_information', 'cart_billing_information_vat_registration_number' ); ?>:</strong> <?php echo htmlspecialchars( $order[0]->vat_registration_number, ENT_QUOTES ); ?></span></td>
                        
                    </tr>
                    
                    <?php }?>

                </table>

            </td>

        </tr>

        <tr>

        	<td width='269' align='left'>&nbsp;</td>

            <td width='80' align='center'>&nbsp;</td>

            <td width='91' align='center'>&nbsp;</td>

            <td align='center'>&nbsp;</td>

        </tr>

        <tr>

        	<td width='269' align='left' bgcolor='#F3F1ED' class='style20'><?php echo $GLOBALS['language']->get_text( "ec_shipping_email", "shipping_product" ); ?></td>

            <td width='80' align='center' bgcolor='#F3F1ED' class='style20'><?php echo $GLOBALS['language']->get_text( "ec_shipping_email", "shipping_quantity" ); ?></td>

            <td width='91' align='center' bgcolor='#F3F1ED' class='style20'><?php echo $GLOBALS['language']->get_text( "ec_shipping_email", "shipping_unit_price" ); ?></td>

            <td align='center' bgcolor='#F3F1ED' class='style20'><?php echo $GLOBALS['language']->get_text( "ec_shipping_email", "shipping_total_price" ); ?></td>

        </tr>
        
        <?php for( $i=0; $i < count( $orderdetails); $i++){ 

			$unit_price = $GLOBALS['currency']->get_currency_display( $orderdetails[$i]->unit_price );
			$total_price = $GLOBALS['currency']->get_currency_display( $orderdetails[$i]->total_price );

		?>

        <tr>

			<td width='269' class='style22'>
				
                <table>
                	
                    <tr>
                    	
                        <?php if( get_option( 'ec_option_show_image_on_receipt' ) ){ ?>
                        <td>
							<?php
                            if( $orderdetails[$i]->is_deconetwork )
                                $img_url = "https://" . get_option( 'ec_option_deconetwork_url' ) . $orderdetails[$i]->deconetwork_image_link;
                            
							else if( substr( $orderdetails[$i]->image1, 0, 7 ) == 'http://' || substr( $orderdetails[$i]->image1, 0, 8 ) == 'https://' )
								$img_url = $orderdetails[$i]->image1;
							
							else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $orderdetails[$i]->image1 ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $orderdetails[$i]->image1 ) )
                                $img_url = plugins_url( "wp-easycart-data/products/pics1/" . $orderdetails[$i]->image1 );
								
							else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg" ) )
								$img_url = plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg" );
								
							else
								$img_url = plugins_url( "wp-easycart/design/theme/" . get_option( 'ec_option_latest_theme' ) . "/images/ec_image_not_found.jpg" );
                            ?>
                            <div style="ec_lineitem_image"><img src="<?php echo str_replace( "https://", "http://", $img_url ); ?>" width="70" alt="<?php echo $orderdetails[$i]->title; ?>" /></div>
						</td>
                        <?php }?>
                        
                    	<td>
				
                			<table>

								<tr>
                    
                    				<td class='style20'>

                    					<?php echo $orderdetails[$i]->title; ?>

                    				</td>
                        
                    			</tr>
            
                    			<tr>
                    
                    				<td class="ec_option_name">
        
                    					<?php echo $orderdetails[$i]->model_number; ?>
        
                    				</td>
                        
                    			</tr>

                    			<?php if( $orderdetails[$i]->gift_card_message ){ ?>

                    			<tr>
                                
                                	<td class='style22'>

                      					<?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_gift_message' ) . htmlspecialchars( $orderdetails[$i]->gift_card_message, ENT_QUOTES ); ?>

                    				</td>
                                    
                                </tr>

                    			<?php }?>

                    			<?php if( $orderdetails[$i]->gift_card_from_name ){ ?>

                    			<tr>
                                	
                                    <td class='style22'>

										<?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_gift_from' ) . htmlspecialchars( $orderdetails[$i]->gift_card_from_name, ENT_QUOTES ); ?>

                    				</td>
                                    
                                </tr>

                    			<?php }?>

                    			<?php if( $orderdetails[$i]->gift_card_to_name ){ ?>

                    			<tr>
                                	
                                    <td class='style22'>

                      					<?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_gift_to' ) . htmlspecialchars( $orderdetails[$i]->gift_card_to_name, ENT_QUOTES ); ?>

                    				</td>
                                    
                                </tr>

                    			<?php }?>
	
								<?php 
	
								do_action( 'wpeasycart_email_receipt_line_item', $orderdetails[$i]->model_number, $orderdetails[$i]->orderdetail_id );
								
								$advanced_option_allow_download = true;
								$db = new ec_db( );
								if( $orderdetails[$i]->use_advanced_optionset ){
									$advanced_options = $db->get_order_options( $orderdetails[$i]->orderdetail_id );
									
									foreach( $advanced_options as $advanced_option ){
										
										if( !$advanced_option->optionitem_allow_download ){
											$advanced_option_allow_download = false;
										}
										
										if( $advanced_option->option_type == "file" ){
											
											$file_split = explode( "/", $advanced_option->option_value );
											echo "<tr><td><span class=\"ec_option_label\">" . $advanced_option->option_label . ":</span> <span class=\"ec_option_name\">" . $file_split[1] . $advanced_option->option_price_change . "</span></td></tr>";

										}else if( $advanced_option->option_type == "grid" ){

											echo "<tr><td><span class=\"ec_option_label\">" . $advanced_option->option_label . ":</span> <span class=\"ec_option_name\">" . $advanced_option->optionitem_name . " (" . $advanced_option->option_value . ")" . $advanced_option->option_price_change . "</span></td></tr>";

										}else{

											echo "<tr><td><span class=\"ec_option_label\">" . $advanced_option->option_label . ":</span> <span class=\"ec_option_name\">" . htmlspecialchars( $advanced_option->option_value, ENT_QUOTES ) . $advanced_option->option_price_change . "</span></td></tr>";

										}

									}

								}else{

									if( $orderdetails[$i]->optionitem_name_1 ){
	
										echo "<tr><td><span class=\"ec_option_name\">" . $orderdetails[$i]->optionitem_name_1;
	
										if( $orderdetails[$i]->optionitem_price_1 < 0 )
											echo " (" . $GLOBALS['currency']->get_currency_display( $orderdetails[$i]->optionitem_price_1 ) . ")";
	
										else if( $orderdetails[$i]->optionitem_price_1 > 0 )
											echo " (+" . $GLOBALS['currency']->get_currency_display( $orderdetails[$i]->optionitem_price_1 ) . ")";
	
										echo "</span></td></tr>";
	
									}
		
									if( $orderdetails[$i]->optionitem_name_2 ){
		
										echo "<tr><td><span class=\"ec_option_name\">" . $orderdetails[$i]->optionitem_name_2;
		
										if( $orderdetails[$i]->optionitem_price_2 < 0 )
											echo " (" . $GLOBALS['currency']->get_currency_display( $orderdetails[$i]->optionitem_price_2 ) . ")";
		
										else if( $orderdetails[$i]->optionitem_price_2 > 0 )
											echo " (+" . $GLOBALS['currency']->get_currency_display( $orderdetails[$i]->optionitem_price_2 ) . ")";
		
										echo "</span></td></tr>";
		
									}
		
									if( $orderdetails[$i]->optionitem_name_3 ){
		
										echo "<tr><td><span class=\"ec_option_name\">" . $orderdetails[$i]->optionitem_name_3;
		
										if( $orderdetails[$i]->optionitem_price_3 < 0 )
											echo " (" . $GLOBALS['currency']->get_currency_display( $orderdetails[$i]->optionitem_price_3 ) . ")";
		
										else if( $orderdetails[$i]->optionitem_price_3 > 0 )
											echo " (+" . $GLOBALS['currency']->get_currency_display( $orderdetails[$i]->optionitem_price_3 ) . ")";
		
										echo "</span></td></tr>";
		
									}
		
									if( $orderdetails[$i]->optionitem_name_4 ){
		
										echo "<tr><td><span class=\"ec_option_name\">" . $orderdetails[$i]->optionitem_name_4;
		
										if( $orderdetails[$i]->optionitem_price_4 < 0 )
											echo " (" . $GLOBALS['currency']->get_currency_display( $orderdetails[$i]->optionitem_price_4 ) . ")";
										
										else if( $orderdetails[$i]->optionitem_price_4 > 0 )
											echo " (+" . $GLOBALS['currency']->get_currency_display( $orderdetails[$i]->optionitem_price_4 ) . ")";
		
										echo "</span></td></tr>";
		
									}
		
									if( $orderdetails[$i]->optionitem_name_5 ){
		
										echo "<tr><td><span class=\"ec_option_name\">" . $orderdetails[$i]->optionitem_name_5;
		
										if( $orderdetails[$i]->optionitem_price_5 < 0 )
											echo " (" . $GLOBALS['currency']->get_currency_display( $orderdetails[$i]->optionitem_price_5 ) . ")";
		
										else if( $orderdetails[$i]->optionitem_price_5 > 0 )
											echo " (+" . $GLOBALS['currency']->get_currency_display( $orderdetails[$i]->optionitem_price_5 ) . ")";
		
										echo "</span></td></tr>";
		
									}
	
								}// Close basic options
								?>

                    			<?php if( $orderdetails[$i]->is_giftcard || ( $orderdetails[$i]->is_download && $advanced_option_allow_download ) ){ ?>

                    			<tr>
                                
                                	<td class='style22'>

                    				<?php 
										$account_page_id = get_option('ec_option_accountpage');
										$account_page = get_permalink( $account_page_id );
										if( substr_count( $account_page, '?' ) )
											$permalink_divider = "&";
										else
											$permalink_divider = "?";

										if( $orderdetails[$i]->is_giftcard ){
											echo "<a href=\"" . $account_page . $permalink_divider . "ec_page=order_details&order_id=" . $this->order_id . "\" target=\"_blank\">" . $GLOBALS['language']->get_text( "account_order_details", "account_orders_details_print_online" ) . "</a>";
										
										}else if( $orderdetails[$i]->is_download ){
											echo "<a href=\"" . $account_page . $permalink_divider . "ec_page=order_details&order_id=" . $this->order_id . "\" target=\"_blank\">" . $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_download' ) . "</a>";

										}
									?>
									
                                    </td>
                                    
                                </tr>
								
								<?php } ?>
                                
                                <?php if( $orderdetails[$i]->include_code && $this->is_approved ){ 
								global $wpdb;
								$codes = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ec_code WHERE ec_code.orderdetail_id = %d", $orderdetails[$i]->orderdetail_id ) );
								$code_list = "";
								for( $code_index = 0; $code_index < count( $codes ); $code_index++ ){
									if( $code_index > 0 )
										$code_list .= ", ";
									$code_list .= $codes[$code_index]->code_val;
								}
								?>
                                
                                <tr>
                                
                                	<td class='style22'>
                                    
                                    	<?php echo $GLOBALS['language']->get_text( 'account_order_details', 'account_orders_details_your_codes' ); ?> <?php echo $code_list; ?>
                                    
                                    </td>
                                    
                                </tr>
                                
                                <?php }?>

							</table>
                
						</td>
                    
                	</tr>
                
            	</table>

            </td>

            <td width='65' align='center' class='style22'><?php echo $orderdetails[$i]->quantity; ?></td>

            <td width='90' align='center' class='style22'><?php echo $unit_price; ?></td>

            <td width='90' align='center' class='style22'><?php echo $total_price; ?></td>

        </tr>

		<?php }//end for loop ?>

		<tr>

        	<td width='269'>&nbsp;</td>

            <td width='80' align='center'>&nbsp;</td>

            <td width='91' align='center'>&nbsp;</td>

            <td>&nbsp;</td>

        </tr>

        <tr>

        	<td width='269'>&nbsp;</td>

            <td width='80' align='center' class='style22'>&nbsp;</td>

            <td width='91' align='center' class='style22'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_subtotal" ); ?></td>

            <td  align='center'  class='style22'><?php echo $GLOBALS['currency']->get_currency_display( $order[0]->sub_total ); ?></td>

        </tr>
        
        <?php if( $order[0]->tip_total > 0 ){?>

        <tr>

        	<td width='269'>&nbsp;</td>

            <td width='80' align='center' class='style22'>&nbsp;</td>

            <td width='91' align='center' class='style22'><?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tip' ); ?></td>

            <td  align='center'  class='style22'><?php echo $GLOBALS['currency']->get_currency_display( $order[0]->tip_total ); ?></td>

        </tr>

        <?php }?>

       <?php if( $order[0]->tax_total > 0 ){ ?>

        <tr>

        	<td width='269'>&nbsp;</td>

            <td width='80' align='center' class='style22'>&nbsp;</td>

            <td width='91' align='center' class='style22'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_tax" ); ?></td>

            <td align='center' class='style22'><?php echo $GLOBALS['currency']->get_currency_display( $order[0]->tax_total ); ?></td>

        </tr>

        <?php }?>

        <?php if( get_option( 'ec_option_use_shipping' ) ){?>

        <tr>

        	<td width='269'>&nbsp;</td>

            <td width='80' align='center' class='style22'>&nbsp;</td>

            <td width='91' align='center' class='style22'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_shipping" ); ?></td>

            <td  align='center'  class='style22'><?php echo $GLOBALS['currency']->get_currency_display( $order[0]->shipping_total ); ?></td>

        </tr>

        <?php }?>

		<?php if( $order[0]->discount_total > 0 ){ ?>
	
        <tr>

          <td>&nbsp;</td>

          <td align='center' class='style22'>&nbsp;</td>

          <td align='center' class='style22'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_discount" ); ?></td>

          <td  align='center'  class='style22'>-<?php echo $GLOBALS['currency']->get_currency_display( $order[0]->discount_total ); ?></td>

        </tr>
        
        <?php }?>
      
        <?php if( $order[0]->duty_total > 0 ){ ?>

        <tr>

        	<td width='269'>&nbsp;</td>

            <td width='80' align='center' class='style22'>&nbsp;</td>

            <td width='91' align='center' class='style22'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_duty" ); ?></td>

            <td align='center' class='style22'><?php echo $GLOBALS['currency']->get_currency_display( $order[0]->duty_total ); ?></td>

        </tr>

        <?php }?>

        <?php if( $order[0]->vat_total > 0 ){ ?>

        <tr>

        	<td width='269'>&nbsp;</td>

            <td width='80' align='center' class='style22'>&nbsp;</td>

            <td width='91' align='center' class='style22'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_vat" ); ?><?php echo number_format( $order[0]->vat_rate, 0 ); ?>%</td>

            <td align='center' class='style22'><?php echo $GLOBALS['currency']->get_currency_display( $order[0]->vat_total ); ?></td>

        </tr>

        <?php }?>

        <?php if( $order[0]->gst_total > 0 ){ ?>

        <tr>

        	<td width='269'>&nbsp;</td>

            <td width='80' align='center' class='style22'>&nbsp;</td>

            <td width='91' align='center' class='style22'>GST (<?php echo $order[0]->gst_rate; ?>%)</td>

            <td align='center' class='style22'><?php echo $GLOBALS['currency']->get_currency_display( $order[0]->gst_total ); ?></td>

        </tr>

        <?php }?>

        <?php if( $order[0]->pst_total > 0 ){ ?>

        <tr>

        	<td width='269'>&nbsp;</td>

            <td width='80' align='center' class='style22'>&nbsp;</td>

            <td width='91' align='center' class='style22'>PST (<?php echo $order[0]->pst_rate; ?>%)</td>

            <td align='center' class='style22'><?php echo $GLOBALS['currency']->get_currency_display( $order[0]->pst_total ); ?></td>

        </tr>

        <?php }?>

        <?php if( $order[0]->hst_total > 0 ){ ?>

        <tr>

        	<td width='269'>&nbsp;</td>

            <td width='80' align='center' class='style22'>&nbsp;</td>

            <td width='91' align='center' class='style22'>HST (<?php echo $order[0]->hst_rate; ?>%)</td>

            <td align='center' class='style22'><?php echo $GLOBALS['currency']->get_currency_display( $order[0]->hst_total ); ?></td>

        </tr>

        <?php }?>

        <tr>

        	<td width='269'>&nbsp;</td>

            <td width='80' align='center' class='style22'>&nbsp;</td>

            <td width='91' align='center' class='style22'><strong><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_grand_total" ); ?></strong></td>

            <td align='center' class='style22'><strong><?php echo $GLOBALS['currency']->get_currency_display( $order[0]->grand_total ); ?></strong></td>

        </tr>

        <tr>

        	<td colspan='4' class='style22'>

            	<p><br>

				<?php if( get_option( 'ec_option_user_order_notes' ) ){ ?>

                    <hr />

                    <h4><?php echo $GLOBALS['language']->get_text( 'cart_payment_information', 'cart_payment_information_order_notes_title' ); ?></h4>

                    <p><?php echo nl2br( htmlspecialchars( $order[0]->order_customer_notes, ENT_QUOTES ) ); ?></p>

                    <br>

                    <hr />

                <?php }?>

				<?php echo $GLOBALS['language']->get_text( 'ec_shipping_email', 'shipping_final_note1' )?><br><br><?php echo $GLOBALS['language']->get_text( 'ec_shipping_email', 'shipping_final_note2' )?></p>

            	<p>&nbsp;</p>

            </td>

        </tr>

        <tr>

        	<td colspan='4'>

            </td>

        </tr>

    </table>

</body>

</html>