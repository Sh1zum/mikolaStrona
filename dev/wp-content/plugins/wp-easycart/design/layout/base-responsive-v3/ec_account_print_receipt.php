<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_receipt_title" ) . " " . $order_id; ?></title>
	<style type='text/css'>
    <!--

		.style20 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; }
        .style22 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
		.ec_option_label{font-family: Arial, Helvetica, sans-serif; font-size:11px; font-weight:bold; }
		.ec_option_name{font-family: Arial, Helvetica, sans-serif; font-size:11px; }
		.ec_admin_page_break{ page-break-before:always; }

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


    <td colspan='4' align='left' class='style22'><p><br>


        <?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_line_1" ) . " " . htmlspecialchars( $order->billing_first_name, ENT_QUOTES ) . " " . htmlspecialchars( $order->billing_last_name, ENT_QUOTES ); ?>:</p>


      <p><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_line_2" ); ?> <strong><?php echo $order_id; ?></strong></p>


      <p><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_line_3" ); ?></p>


      <p><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_line_4" ); ?><br>


        <br>


        <br>


      </p></td>


  </tr>


  <tr>


    <td colspan='4' align='left' class='style20'><table width='100%' border='0' align='center' cellpadding='0' cellspacing='0'>


        <tr>


          <td width='47%' bgcolor='#F3F1ED' class='style20'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_billing_label" ); ?></td>


          <td width='3%'>&nbsp;</td>


          <td width='50%' bgcolor='#F3F1ED' class='style20'><?php if( get_option( 'ec_option_use_shipping' ) ){?><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_shipping_label" ); ?><?php }?></td>


        </tr>


        <tr>


          <td><span class='style22'><?php echo htmlspecialchars( $order->billing_first_name, ENT_QUOTES ); ?> <?php echo htmlspecialchars( $order->billing_last_name, ENT_QUOTES ); ?></span></td>


          <td>&nbsp;</td>


          <td><span class='style22'><?php if( get_option( 'ec_option_use_shipping' ) ){?><?php echo htmlspecialchars( $order->shipping_first_name, ENT_QUOTES ); ?> <?php echo htmlspecialchars( $order->shipping_last_name, ENT_QUOTES ); ?><?php }?></span></td>


        </tr>

		<?php if( $order->billing_company_name != "" || ( get_option( 'ec_option_use_shipping' ) && $order->shipping_company_name != "" ) ){ ?>

        <tr>


          <td><span class='style22'><?php echo htmlspecialchars( $order->billing_company_name, ENT_QUOTES ); ?></span></td>


          <td>&nbsp;</td>


          <td><span class='style22'><?php if( get_option( 'ec_option_use_shipping' ) ){?><?php echo htmlspecialchars( $order->shipping_company_name, ENT_QUOTES ); ?><?php }?></span></td>


        </tr>
		
        <?php }?>

        <tr>


          <td><span class='style22'><?php echo htmlspecialchars( $order->billing_address_line_1, ENT_QUOTES ); ?></span></td>


          <td>&nbsp;</td>


          <td><span class='style22'><?php if( get_option( 'ec_option_use_shipping' ) ){?><?php echo htmlspecialchars( $order->shipping_address_line_1, ENT_QUOTES ); ?><?php }?></span></td>


        </tr>


        <?php if( $order->billing_address_line_2 != "" || $order->shipping_address_line_2 != "" ){ ?>


        <tr>


          <td><span class='style22'><?php echo htmlspecialchars( $order->billing_address_line_2, ENT_QUOTES ); ?></span></td>


          <td>&nbsp;</td>


          <td><span class='style22'><?php if( get_option( 'ec_option_use_shipping' ) ){?><?php echo htmlspecialchars( $order->shipping_address_line_2, ENT_QUOTES ); ?><?php }?></span></td>


        </tr>


        <?php } ?>


        <tr>


          <td><span class='style22'><?php echo htmlspecialchars( $order->billing_city, ENT_QUOTES ); ?>, <?php echo htmlspecialchars( $order->billing_state, ENT_QUOTES ); ?> <?php echo htmlspecialchars( $order->billing_zip, ENT_QUOTES ); ?></span></td>


          <td>&nbsp;</td>


          <td><span class='style22'><?php if( get_option( 'ec_option_use_shipping' ) ){?><?php echo htmlspecialchars( $order->shipping_city, ENT_QUOTES ); ?>, <?php echo htmlspecialchars( $order->shipping_state, ENT_QUOTES ); ?> <?php echo htmlspecialchars( $order->shipping_zip, ENT_QUOTES ); ?><?php }?></span></td>


        </tr>


        <tr>


          <td><span class='style22'><?php echo htmlspecialchars( $order->billing_country, ENT_QUOTES ); ?></span></td>


          <td>&nbsp;</td>


          <td><span class='style22'><?php if( get_option( 'ec_option_use_shipping' ) ){?><?php echo htmlspecialchars( $order->shipping_country, ENT_QUOTES ); ?><?php }?></span></td>


        </tr>


        <tr>


          <td><span class='style22'><?php echo htmlspecialchars( $order->billing_phone, ENT_QUOTES ); ?></span></td>


          <td>&nbsp;</td>


          <td><span class='style22'><?php if( get_option( 'ec_option_use_shipping' ) ){?><?php echo htmlspecialchars( $order->shipping_phone, ENT_QUOTES ); ?><?php }?></span></td>


        </tr>
                    
		<?php if( $order->vat_registration_number != "" ){ ?>
        
        <tr>

            <td colspan="3"><span class='style22'><strong><?php echo $GLOBALS['language']->get_text( 'cart_billing_information', 'cart_billing_information_vat_registration_number' ); ?>:</strong> <?php echo htmlspecialchars( $order->vat_registration_number, ENT_QUOTES ); ?></span></td>
            
        </tr>
        
        <?php }?>


      </table></td>


  </tr>


  <tr>


    <td width='269' align='left'>&nbsp;</td>


    <td width='80' align='center'>&nbsp;</td>


    <td width='91' align='center'>&nbsp;</td>


    <td align='center'>&nbsp;</td>


  </tr>


  <tr>


    <td width='269' align='left' bgcolor='#F3F1ED' class='style20'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_details_header_1" ); ?></td>


    <td width='80' align='center' bgcolor='#F3F1ED' class='style20'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_details_header_2" ); ?></td>


    <td width='91' align='center' bgcolor='#F3F1ED' class='style20'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_details_header_3" ); ?></td>


    <td align='center' bgcolor='#F3F1ED' class='style20'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_details_header_4" ); ?></td>


  </tr>


  <?php for( $i=0; $i < count( $order_details); $i++){ 


	


			$unit_price = $GLOBALS['currency']->get_currency_display( $order_details[$i]->unit_price );


			$total_price = $GLOBALS['currency']->get_currency_display( $order_details[$i]->total_price );


		


		?>


  <tr>


    <td width='269' class='style22'>
    	<?php if( get_option( 'ec_option_show_image_on_receipt' ) ){ ?>
		<?php
        if( $order_details[$i]->is_deconetwork )
			$img_url = "https://" . get_option( 'ec_option_deconetwork_url' ) . $order_details[$i]->deconetwork_image_link;
		
		else if( substr( $order_details[$i]->image1, 0, 7 ) == 'http://' || substr( $order_details[$i]->image1, 0, 8 ) == 'https://' )
			$img_url = $order_details[$i]->image1;
		
		else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $order_details[$i]->image1 ) && !is_dir( WP_PLUGIN_DIR . "wp-easycart-data/products/pics1/" . $order_details[$i]->image1 ) )
			$img_url = plugins_url( "wp-easycart-data/products/pics1/" . $order_details[$i]->image1 );
		
		else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg" ) )
			$img_url = plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg" );
		
		else
			$img_url = plugins_url( "wp-easycart/design/theme/" . get_option( 'ec_option_latest_theme' ) . "/images/ec_image_not_found.jpg" );
        ?>
		<div style="float:left; width:70px; margin-right:5px;"><img src="<?php echo $img_url; ?>" style="width:70px; height:auto;" alt="<?php echo $order_details[$i]->title; ?>" /></div>
        <?php }?>
        
		<table>


            <tr><td>


            <?php echo $order_details[$i]->title; ?>


            </td></tr>
            
            <tr><td class="ec_option_name">


            <?php echo $order_details[$i]->model_number; ?>


            </td></tr>


            <?php 


			if( $order_details[$i]->use_advanced_optionset ){


				$advanced_options = $mysqli->get_order_options( $order_details[$i]->orderdetail_id );


				foreach( $advanced_options as $advanced_option ){


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


			


			if( $order_details[$i]->optionitem_name_1 ){


				echo "<tr><td><span class=\"ec_option_name\">" . $order_details[$i]->optionitem_name_1;


				if( $order_details[$i]->optionitem_price_1 < 0 )


					echo " (" . $GLOBALS['currency']->get_currency_display( $order_details[$i]->optionitem_price_1 ) . ")";


				else if( $order_details[$i]->optionitem_price_1 > 0 )


					echo " (+" . $GLOBALS['currency']->get_currency_display( $order_details[$i]->optionitem_price_1 ) . ")";


				echo "</span></td></tr>";


			}


			


			if( $order_details[$i]->optionitem_name_2 ){


				echo "<tr><td><span class=\"ec_option_name\">" . $order_details[$i]->optionitem_name_2;


				if( $order_details[$i]->optionitem_price_2 < 0 )


					echo " (" . $GLOBALS['currency']->get_currency_display( $order_details[$i]->optionitem_price_2 ) . ")";


				else if( $order_details[$i]->optionitem_price_2 > 0 )


					echo " (+" . $GLOBALS['currency']->get_currency_display( $order_details[$i]->optionitem_price_2 ) . ")";


				echo "</span></td></tr>";


			}


			


			if( $order_details[$i]->optionitem_name_3 ){


				echo "<tr><td><span class=\"ec_option_name\">" . $order_details[$i]->optionitem_name_3;


				if( $order_details[$i]->optionitem_price_3 < 0 )


					echo " (" . $GLOBALS['currency']->get_currency_display( $order_details[$i]->optionitem_price_3 ) . ")";


				else if( $order_details[$i]->optionitem_price_3 > 0 )


					echo " (+" . $GLOBALS['currency']->get_currency_display( $order_details[$i]->optionitem_price_3 ) . ")";


				echo "</span></td></tr>";


			}


			


			if( $order_details[$i]->optionitem_name_4 ){


				echo "<tr><td><span class=\"ec_option_name\">" . $order_details[$i]->optionitem_name_4;


				if( $order_details[$i]->optionitem_price_4 < 0 )


					echo " (" . $GLOBALS['currency']->get_currency_display( $order_details[$i]->optionitem_price_4 ) . ")";


				else if( $order_details[$i]->optionitem_price_4 > 0 )


					echo " (+" . $GLOBALS['currency']->get_currency_display( $order_details[$i]->optionitem_price_4 ) . ")";


				echo "</span></td></tr>";


			}


			


			if( $order_details[$i]->optionitem_name_5 ){


				echo "<tr><td><span class=\"ec_option_name\">" . $order_details[$i]->optionitem_name_5;


				if( $order_details[$i]->optionitem_price_5 < 0 )


					echo " (" . $GLOBALS['currency']->get_currency_display( $order_details[$i]->optionitem_price_5 ) . ")";


				else if( $order_details[$i]->optionitem_price_5 > 0 )


					echo " (+" . $GLOBALS['currency']->get_currency_display( $order_details[$i]->optionitem_price_5 ) . ")";


				echo "</span></td></tr>";


			}


			


			}//close basic options


			?>
            
			<?php do_action( 'wp_easycart_print_receipt_optionitems', $order_details[$i] ); ?>


        </table>


	</td>


    <td width='80' align='center' class='style22'><?php echo $order_details[$i]->quantity; ?></td>


    <td width='91' align='center' class='style22'><?php echo apply_filters( 'wp_easycart_cart_item_unit_price_display', $unit_price, $order_details[$i]->product_id ); ?></td>


    <td align='center' class='style22'><?php echo $total_price; ?></td>


  </tr>
  
  <?php if( $order_details[$i]->subscription_signup_fee > 0 ){ ?>

  <tr>
	
	<td width='269' class='style22'>

		<table>

			<tr><td>
			<?php if( get_option( 'ec_option_show_image_on_receipt' ) ){ ?>
			<?php
            if( $order_details[$i]->is_deconetwork )
                $img_url = "https://" . get_option( 'ec_option_deconetwork_url' ) . $order_details[$i]->deconetwork_image_link;
            else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $order_details[$i]->image1 ) && !is_dir( WP_PLUGIN_DIR . "wp-easycart-data/products/pics1/" . $order_details[$i]->image1 ) )
                $img_url = plugins_url( "wp-easycart-data/products/pics1/" . $order_details[$i]->image1 );
            else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg" ) )
                $img_url = plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg" );
            else
                $img_url = plugins_url( "wp-easycart/design/theme/" . get_option( 'ec_option_latest_theme' ) . "/images/ec_image_not_found.jpg" );
            ?>
            <div style="float:left; width:70px; margin-right:5px;"><img src="<?php echo $img_url; ?>" style="width:70px; height:auto;" alt="<?php echo $order_details[$i]->title; ?>" /></div>
            <?php }?>
        
			<?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_signup_fee_notice1' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $order_details[$i]->subscription_signup_fee ); ?>
			</td></tr>
		
		</table>
	
	</td>

	<td width='80' align='center' class='style22'>1</td>

	<td width='91' align='center' class='style22'><?php echo $GLOBALS['currency']->get_currency_display( $order_details[$i]->subscription_signup_fee ); ?></td>

	<td align='center' class='style22'><?php echo $GLOBALS['currency']->get_currency_display( $order_details[$i]->subscription_signup_fee ); ?></td>

  </tr>		
			
  <?php }?>


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


    <td  align='center'  class='style22'><?php echo $subtotal; ?></td>


  </tr>
        
  <?php if( $order->tip_total > 0 ){?>

  <tr>

    <td width='269'>&nbsp;</td>

    <td width='80' align='center' class='style22'>&nbsp;</td>

    <td width='91' align='center' class='style22'><?php echo $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tip' ); ?></td>

    <td  align='center'  class='style22'><?php echo $tip; ?></td>

  </tr>

  <?php }?>


  <?php if( $order->tax_total > 0 ){ ?>


  <tr>


    <td width='269'>&nbsp;</td>


    <td width='80' align='center' class='style22'>&nbsp;</td>


    <td width='91' align='center' class='style22'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_tax" ); ?></td>


    <td align='center' class='style22'><?php echo $tax; ?></td>


  </tr>


  <?php }?>


  <?php if( get_option( 'ec_option_use_shipping' ) ){?>


  <tr>


    <td width='269'>&nbsp;</td>


    <td width='80' align='center' class='style22'>&nbsp;</td>


    <td width='91' align='center' class='style22'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_shipping" ); ?></td>


    <td  align='center'  class='style22'><?php echo $shipping; ?></td>


  </tr>


  <?php }?>


  <?php if( $order->discount_total > 0 ){ ?>

  <tr>


    <td>&nbsp;</td>


    <td align='center' class='style22'>&nbsp;</td>


    <td align='center' class='style22'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_discount" ); ?></td>


    <td  align='center'  class='style22'>-<?php echo $discount; ?></td>


  </tr>
  
  <?php } ?>


  <?php if( $has_duty ){ ?>


  <tr>


    <td width='269'>&nbsp;</td>


    <td width='80' align='center' class='style22'>&nbsp;</td>


    <td width='91' align='center' class='style22'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_duty" ); ?></td>


    <td align='center' class='style22'><?php echo $duty; ?></td>


  </tr>


  <?php }?>


  <?php if( $tax_struct->is_vat_enabled( ) ){ ?>


  <tr>


    <td width='269'>&nbsp;</td>


    <td width='80' align='center' class='style22'>&nbsp;</td>


    <td width='91' align='center' class='style22'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_vat" ); ?><?php echo $vat_rate; ?>%</td>


    <td align='center' class='style22'><?php echo $vat; ?></td>


  </tr>


  <?php }?>


  <?php if( $gst > 0 ){ ?>


  <tr>


    <td width='269'>&nbsp;</td>


    <td width='80' align='center' class='style22'>&nbsp;</td>


    <td width='91' align='center' class='style22'>GST (<?php echo $gst_rate; ?>%)</td>


    <td align='center' class='style22'><?php echo $GLOBALS['currency']->get_currency_display( $gst ); ?></td>


  </tr>


  <?php }?>


  <?php if( $pst > 0 ){ ?>


  <tr>


    <td width='269'>&nbsp;</td>


    <td width='80' align='center' class='style22'>&nbsp;</td>


    <td width='91' align='center' class='style22'>PST (<?php echo $pst_rate; ?>%)</td>


    <td align='center' class='style22'><?php echo $GLOBALS['currency']->get_currency_display( $pst ); ?></td>


  </tr>


  <?php }?>


  <?php if( $hst > 0 ){ ?>


  <tr>


    <td width='269'>&nbsp;</td>


    <td width='80' align='center' class='style22'>&nbsp;</td>


    <td width='91' align='center' class='style22'>HST (<?php echo $hst_rate; ?>%)</td>


    <td align='center' class='style22'><?php echo $GLOBALS['currency']->get_currency_display( $hst ); ?></td>


  </tr>


  <?php }?>


  <tr>


    <td width='269'>&nbsp;</td>


    <td width='80' align='center' class='style22'>&nbsp;</td>


    <td width='91' align='center' class='style22'><strong><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_grand_total" ); ?></strong></td>


    <td align='center' class='style22'><strong><?php echo $total; ?></strong></td>


  </tr>
    
  <?php if( $order->refund_total > 0 ){ ?>

  <tr>


    <td>&nbsp;</td>


    <td align='center' class='style22'>&nbsp;</td>


    <td align='center' class='style22' style="color:red; font-weight:bold;"><?php echo $GLOBALS['language']->get_text( "account_order_details", "account_orders_details_refund_total_short" ); ?></td>


    <td  align='center'  class='style22' style="color:red; font-weight:bold;">-<?php echo $refund; ?></td>


  </tr>
  
  <?php } ?>


  <tr>


    <td colspan='4' class='style22'><p><br>


		<?php if( get_option( 'ec_option_user_order_notes' ) ){ ?>


            <hr />


            <h4><?php echo $GLOBALS['language']->get_text( 'cart_payment_information', 'cart_payment_information_order_notes_title' ); ?></h4>


            <p><?php echo nl2br( htmlspecialchars( $order->order_customer_notes, ENT_QUOTES ) ); ?></p>


            <br>


            <hr />


        <?php }?>


        <?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_bottom_line_1" ); ?><br>


        <br>


        <?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_bottom_line_2" ); ?></p>


      <p>&nbsp;</p></td>


  </tr>


</table>


</body>


</html>