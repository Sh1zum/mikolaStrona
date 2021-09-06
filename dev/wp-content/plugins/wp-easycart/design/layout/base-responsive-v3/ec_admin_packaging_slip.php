<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_receipt_title" ) . " " . $order_id; ?></title>
<style type='text/css'>
<!--
.style20 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 12px;
}
.style22 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.style24 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color:#F00;
	font-weight: bold;
}
.ec_option_label {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-weight: bold;
}
.ec_option_name {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
}
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
    <td align='left' class='style22'><strong><br><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_success_order_number_is" ) . ' ' .  $order->order_id; ?></strong><br><br>
      </td>
    <td colspan="3" align='right' class='style22'><strong><?php $date = date_create($order->order_date); echo date_format($date , 'l - F jS,  Y'); ?></strong></td>
  </tr>
  <tr>
    <td colspan='4' align='left' class='style20'><table width='100%' border='0' align='center' cellpadding='0' cellspacing='0'>
        <tr>
          <td width='47%' bgcolor='#DFDFDF' class='style20'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_billing_label" ); ?></td>
          <td width='3%'>&nbsp;</td>
          <td width='50%' bgcolor='#DFDFDF' class='style20'><?php if( get_option( 'ec_option_use_shipping' ) ){?>
            <?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_shipping_label" ); ?>
            <?php }?></td>
        </tr>
        <tr>
          <td><span class='style22'><?php echo htmlspecialchars( $order->billing_first_name, ENT_QUOTES ); ?> <?php echo htmlspecialchars( $order->billing_last_name, ENT_QUOTES ); ?></span></td>
          <td>&nbsp;</td>
          <td><span class='style22'>
            <?php if( get_option( 'ec_option_use_shipping' ) ){?>
            <?php echo htmlspecialchars( $order->shipping_first_name, ENT_QUOTES ); ?> <?php echo htmlspecialchars( $order->shipping_last_name, ENT_QUOTES ); ?>
            <?php }?>
            </span></td>
        </tr>
        <?php if( $order->billing_company_name != "" || ( get_option( 'ec_option_use_shipping' ) && $order->shipping_company_name != "" ) ){ ?>
        <tr>
          <td><span class='style22'><?php echo htmlspecialchars( $order->billing_company_name, ENT_QUOTES ); ?></span></td>
          <td>&nbsp;</td>
          <td><span class='style22'>
            <?php if( get_option( 'ec_option_use_shipping' ) ){?>
            <?php echo htmlspecialchars( $order->shipping_company_name, ENT_QUOTES ); ?>
            <?php }?>
            </span></td>
        </tr>
        <?php }?>
        <tr>
          <td><span class='style22'><?php echo htmlspecialchars( $order->billing_address_line_1, ENT_QUOTES ); ?></span></td>
          <td>&nbsp;</td>
          <td><span class='style22'>
            <?php if( get_option( 'ec_option_use_shipping' ) ){?>
            <?php echo htmlspecialchars( $order->shipping_address_line_1, ENT_QUOTES ); ?>
            <?php }?>
            </span></td>
        </tr>
        <?php if( $order->billing_address_line_2 != "" || $order->shipping_address_line_2 != "" ){ ?>
        <tr>
          <td><span class='style22'><?php echo htmlspecialchars( $order->billing_address_line_2, ENT_QUOTES ); ?></span></td>
          <td>&nbsp;</td>
          <td><span class='style22'>
            <?php if( get_option( 'ec_option_use_shipping' ) ){?>
            <?php echo htmlspecialchars( $order->shipping_address_line_2, ENT_QUOTES ); ?>
            <?php }?>
            </span></td>
        </tr>
        <?php } ?>
        <tr>
          <td><span class='style22'><?php echo htmlspecialchars( $order->billing_city, ENT_QUOTES ); ?>, <?php echo htmlspecialchars( $order->billing_state, ENT_QUOTES ); ?> <?php echo htmlspecialchars( $order->billing_zip, ENT_QUOTES ); ?></span></td>
          <td>&nbsp;</td>
          <td><span class='style22'>
            <?php if( get_option( 'ec_option_use_shipping' ) ){?>
            <?php echo htmlspecialchars( $order->shipping_city, ENT_QUOTES ); ?>, <?php echo htmlspecialchars( $order->shipping_state, ENT_QUOTES ); ?> <?php echo htmlspecialchars( $order->shipping_zip, ENT_QUOTES ); ?>
            <?php }?>
            </span></td>
        </tr>
        <tr>
          <td><span class='style22'><?php echo htmlspecialchars( $order->billing_country, ENT_QUOTES ); ?></span></td>
          <td>&nbsp;</td>
          <td><span class='style22'>
            <?php if( get_option( 'ec_option_use_shipping' ) ){?>
            <?php echo htmlspecialchars( $order->shipping_country, ENT_QUOTES ); ?>
            <?php }?>
            </span></td>
        </tr>
        <tr>
          <td><span class='style22'><?php echo htmlspecialchars( $order->billing_phone, ENT_QUOTES ); ?></span></td>
          <td>&nbsp;</td>
          <td><span class='style22'>
            <?php if( get_option( 'ec_option_use_shipping' ) ){?>
            <?php echo htmlspecialchars( $order->shipping_phone, ENT_QUOTES ); ?>
            <?php }?>
            </span></td>
        </tr>
        <tr>
          <td><span class='style22'><?php echo htmlspecialchars( $order->user_email, ENT_QUOTES ); ?></span></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td width='269' align='left'>&nbsp;</td>
    <td width='80' align='center'>&nbsp;</td>
    <td width='91' align='center'>&nbsp;</td>
    <td align='center'>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" align='left' bgcolor='#DFDFDF' class='style20'><?php echo $GLOBALS['language']->get_text( 'cart_shipping_method', 'cart_shipping_method_title' ); ?></td>
  </tr>
  <tr>
    <td colspan="4" align='left' class='style24'><?php echo htmlspecialchars( $order->shipping_method, ENT_QUOTES ); ?></td>
  </tr>
  <tr>
    <td colspan="4" align='left' class='style22'><?php echo htmlspecialchars( $order->shipping_carrier, ENT_QUOTES ); ?></td>
  </tr>
  <tr>
    <td colspan="4" align='left' class='style22'><?php echo htmlspecialchars( $order->tracking_number, ENT_QUOTES ); ?></td>
  </tr>
  <tr>
    <td colspan="4" align='left' class='style22'>&nbsp;</td>
  </tr>
  <tr>
    <td width='<?php if( get_option( 'ec_option_packing_slip_show_pricing' ) ){ ?>269<?php }else{ ?>459<?php }?>'<?php if( !get_option( 'ec_option_packing_slip_show_pricing' ) ){ ?> colspan="3"<?php }?> align='left' bgcolor='#DFDFDF' class='style20'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_details_header_1" ); ?></td>
    <td width='80' align='center' bgcolor='#DFDFDF' class='style20'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_details_header_2" ); ?></td>
    <?php if( get_option( 'ec_option_packing_slip_show_pricing' ) ){ ?>
    <td width='91' align='center' bgcolor='#DFDFDF' class='style20'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_details_header_3" ); ?></td>
    <td align='center' bgcolor='#DFDFDF' class='style20'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_details_header_4" ); ?></td>
  	<?php }?>
  </tr>
  <?php for( $i=0; $i < count( $order_details); $i++){ 


	


			$unit_price = $GLOBALS['currency']->get_currency_display( $order_details[$i]->unit_price );


			$total_price = $GLOBALS['currency']->get_currency_display( $order_details[$i]->total_price );


		


		?>
  <tr>
    <td width='<?php if( get_option( 'ec_option_packing_slip_show_pricing' ) ){ ?>269<?php }else{ ?>459<?php }?>' class='style22'<?php if( !get_option( 'ec_option_packing_slip_show_pricing' ) ){ ?> colspan="3"<?php }?>>
		<?php if( get_option( 'ec_option_show_image_on_receipt' ) ){ ?>
		<?php
        if( $order_details[$i]->is_deconetwork )
			$img_url = "https://" . get_option( 'ec_option_deconetwork_url' ) . $this->deconetwork_image_link;
		else if( substr( $order_details[$i]->image1, 0, 7 ) == 'http://' || substr( $order_details[$i]->image1, 0, 8 ) == 'https://' )
			$img_url = $order_details[$i]->image1;
		else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $order_details[$i]->image1 ) && !is_dir( WP_PLUGIN_DIR . "wp-easycart-data/products/pics1/" . $order_details[$i]->image1 ) )
			$img_url = plugins_url( "wp-easycart-data/products/pics1/" . $order_details[$i]->image1 );
		else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg" ) )
			$img_url = plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg" );
		else
			$img_url = plugins_url( "wp-easycart/design/theme/" . get_option( 'ec_option_latest_theme' ) . "/images/ec_image_not_found.jpg" );
        ?>
      <div style="float:left; width:70px; margin-right:5px;"><img src="<?php echo $img_url; ?>" style="width:70px; height:auto;" alt="<?php echo $GLOBALS['language']->convert_text( $order_details[$i]->title ); ?>" /></div>
      <?php }?>
      <table>
        <tr>
          <td><?php echo $GLOBALS['language']->convert_text( $order_details[$i]->title ); ?></td>
        </tr>
        <tr>
          <td class="ec_option_name"><?php echo $order_details[$i]->model_number; ?></td>
        </tr>
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
      </table></td>
    <td width='80' align='center' class='style22'><?php echo $order_details[$i]->quantity; ?></td>
    <?php if( get_option( 'ec_option_packing_slip_show_pricing' ) ){ ?>
    <td width='91' align='center' class='style22'><?php echo $unit_price; ?></td>
    <td align='center' class='style22'><?php echo $total_price; ?></td>
  	<?php }?>
  </tr>
  <?php }//end for loop ?>
  <?php if( get_option( 'ec_option_packing_slip_show_pricing' ) ){ ?>
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
  <?php if( $vat_rate == 0 ){ ?>
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
  <tr>
    <td>&nbsp;</td>
    <td align='center' class='style22'>&nbsp;</td>
    <td align='center' class='style22'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_discount" ); ?></td>
    <td  align='center'  class='style22'>-<?php echo $discount; ?></td>
  </tr>
  <?php if( $has_duty ){ ?>
  <tr>
    <td width='269'>&nbsp;</td>
    <td width='80' align='center' class='style22'>&nbsp;</td>
    <td width='91' align='center' class='style22'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_duty" ); ?></td>
    <td align='center' class='style22'><?php echo $duty; ?></td>
  </tr>
  <?php }?>
  <?php if( $vat_rate != 0 ){ ?>
  <tr>
    <td width='269'>&nbsp;</td>
    <td width='80' align='center' class='style22'>&nbsp;</td>
    <td width='91' align='center' class='style22'><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_vat" ); ?><?php echo $vat_rate; ?>%</td>
    <td align='center' class='style22'><?php echo $vat; ?></td>
  </tr>
  <?php }?>
  <?php if( $gst_rate != 0 ){ ?>
  <tr>
    <td width='269'>&nbsp;</td>
    <td width='80' align='center' class='style22'>&nbsp;</td>
    <td width='91' align='center' class='style22'>GST (<?php echo $gst_rate; ?>%)</td>
    <td align='center' class='style22'><?php echo $gst_total; ?></td>
  </tr>
  <?php }?>
  <?php if( $pst_rate != 0 ){ ?>
  <tr>
    <td width='269'>&nbsp;</td>
    <td width='80' align='center' class='style22'>&nbsp;</td>
    <td width='91' align='center' class='style22'>PST (<?php echo $pst_rate; ?>%)</td>
    <td align='center' class='style22'><?php echo $pst_total; ?></td>
  </tr>
  <?php }?>
  <?php if( $hst_rate != 0 ){ ?>
  <tr>
    <td width='269'>&nbsp;</td>
    <td width='80' align='center' class='style22'>&nbsp;</td>
    <td width='91' align='center' class='style22'>HST (<?php echo $hst_rate; ?>%)</td>
    <td align='center' class='style22'><?php echo $hst_total; ?></td>
  </tr>
  <?php }?>
  <tr>
    <td width='269'>&nbsp;</td>
    <td width='80' align='center' class='style22'>&nbsp;</td>
    <td width='91' align='center' bgcolor="#DFDFDF" class='style22'><strong><?php echo $GLOBALS['language']->get_text( "cart_success", "cart_payment_complete_order_totals_grand_total" ); ?></strong></td>
    <td align='center' bgcolor="#DFDFDF" class='style22'><strong><?php echo $total; ?></strong></td>
  </tr>
  <?php }?>
  <tr>
    <td colspan='4' class='style22'><p><br>
        <?php if( get_option( 'ec_option_user_order_notes' ) ){ ?>
      <hr />
      <h4><?php echo $GLOBALS['language']->get_text( 'cart_payment_information', 'cart_payment_information_order_notes_title' ); ?></h4>
      <p><?php echo nl2br( htmlspecialchars( $order->order_customer_notes, ENT_QUOTES ) ); ?></p>
      <br>
      <hr />
      <?php }?>
      </td>
  </tr>
</table>
</body>
</html>