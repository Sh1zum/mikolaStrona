<?php
class ec_2checkout_thirdparty extends ec_third_party{
	
	public function display_form_start( ){
		
		$sid = get_option( 'ec_option_2checkout_thirdparty_sid' );
		$mode = '2CO';
		$currency_code = get_option( 'ec_option_2checkout_thirdparty_currency_code' );
		$lang = get_option( 'ec_option_2checkout_thirdparty_lang' );
		$sandbox_mode = get_option( 'ec_option_2checkout_thirdparty_sandbox_mode' );
		$demo_mode = get_option( 'ec_option_2checkout_thirdparty_demo_mode' );
		$purchase_step = "payment-method";
		$receipt_link_url = site_url( );
		//$webhook_url = plugins_url( EC_PLUGIN_DIRECTORY . "/inc/scripts/ec_2checkout_complete.php" );
		
		if( $sandbox_mode )					$checkout_url = "https://sandbox.2checkout.com/checkout/purchase";
		else								$checkout_url = "https://www.2checkout.com/checkout/purchase";
		
		$tax = new ec_tax( 0.00, 0.00, 0.00, $this->order->billing_state, $this->order->billing_country );
		$tax_total = number_format( $this->order->tax_total + $this->order->duty_total + $this->order->gst_total + $this->order->pst_total + $this->order->hst_total, 2 );
		if( !$tax->vat_included )
			$tax_total = number_format( $tax_total + $this->order->vat_total, 2 );
		
		echo "<form action=\"" . $checkout_url . "\" method=\"post\">";
		echo "<input name=\"sid\" id=\"sid\" type=\"hidden\" value=\"" . $sid . "\" />";
		echo "<input name=\"mode\" id=\"mode\" type=\"hidden\" value=\"" . $mode . "\" />";
		if( $demo_mode )
			echo "<input name=\"demo\" id=\"demo\" type=\"hidden\" value=\"Y\" />";
		echo "<input name=\"currency_code\" id=\"currency_code\" type=\"hidden\" value=\"" . $currency_code . "\" />";
		echo "<input name=\"lang\" id=\"lang\" type=\"hidden\" value=\"" . $lang . "\" />";
		echo "<input name=\"merchant_order_id\" id=\"merchant_order_id\" type=\"hidden\" value=\"" . $this->order_id . "\" />";
		echo "<input name=\"purchase_step\" id=\"purchase_step\" type=\"hidden\" value=\"" . $purchase_step . "\" />";
		echo "<input name=\"x_receipt_link_url\" id=\"x_receipt_link_url\" type=\"hidden\" value=\"" . $receipt_link_url . "\" />";
		
		/* Billing and Shipping */
		echo "<input name=\"card_holder_name\" id=\"first_name\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_first_name, ENT_QUOTES ) . " " . htmlspecialchars( $this->order->billing_last_name, ENT_QUOTES ) . "\" />";	
		echo "<input name=\"address1\" id=\"address1\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_address_line_1, ENT_QUOTES ) . "\" />";
		if( get_option( 'ec_option_use_address2' ) )
			echo "<input name=\"address2\" id=\"address12\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_address_line_2, ENT_QUOTES ) . "\" />";
		echo "<input name=\"city\" id=\"city\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_city, ENT_QUOTES ) . "\" />";
		echo "<input name=\"state\" id=\"state\" type=\"hidden\" value=\"" . htmlspecialchars( strtoupper($this->order->billing_state ), ENT_QUOTES ) . "\" />";
		echo "<input name=\"zip\" id=\"zip\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_zip, ENT_QUOTES ) . "\" />";
		echo "<input name=\"country\" id=\"country\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_country, ENT_QUOTES ) . "\" />";
		echo "<input name=\"email\" id=\"email\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->user_email, ENT_QUOTES ) . "\" />";
		if( get_option( 'ec_option_collect_user_phone' ) )
			echo "<input name=\"phone\" id=\"phone\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_phone, ENT_QUOTES ) . "\" />";
		
		if( get_option( 'ec_option_use_shipping' ) ){
			echo "<input name=\"ship_name\" id=\"ship_name\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->shipping_first_name, ENT_QUOTES ) . " " . htmlspecialchars( $this->order->shipping_last_name, ENT_QUOTES ) . "\" />";	
			echo "<input name=\"ship_street_address\" id=\"ship_street_address\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->shipping_address_line_1, ENT_QUOTES ) . "\" />";
			if( get_option( 'ec_option_use_address2' ) )
				echo "<input name=\"ship_street_address2\" id=\"ship_street_address2\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->shipping_address_line_2, ENT_QUOTES ) . "\" />";
			echo "<input name=\"ship_city\" id=\"ship_city\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->shipping_city, ENT_QUOTES ) . "\" />";
			echo "<input name=\"ship_state\" id=\"ship_state\" type=\"hidden\" value=\"" . htmlspecialchars( strtoupper($this->order->shipping_state ), ENT_QUOTES ) . "\" />";
			echo "<input name=\"ship_zip\" id=\"ship_zip\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->shipping_zip, ENT_QUOTES ) . "\" />";
			echo "<input name=\"ship_country\" id=\"ship_country\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->shipping_country, ENT_QUOTES ) . "\" />";
		}
		
		// Setup Cart Items
		for( $i=0; $i<count( $this->order_details ); $i++ ){
			$tangible = 'N';
			if( $this->order_details[$i]->is_shippable )
				$tangible = 'Y';
			echo "<input name=\"li_" . $i . "_type\" type=\"hidden\" value=\"product\" />";
			echo "<input name=\"li_" . $i . "_name\" type=\"hidden\" value=\"" . str_replace( ">", "", str_replace( "<", "", str_replace( '"', '&quot;', substr( $this->order_details[$i]->title, 0, 128 ) ) ) ) . "\" />";
			echo "<input name=\"li_" . $i . "_quantity\" type=\"hidden\" value=\"" . $this->order_details[$i]->quantity . "\" />";
			echo "<input name=\"li_" . $i . "_price\" type=\"hidden\" value=\"" . number_format( ( $this->order_details[$i]->total_price / $this->order_details[$i]->quantity ), 2, '.', '' ) . "\" />";
			echo "<input name=\"li_" . $i . "_tangible\" type=\"hidden\" value=\"" . $tangible . "\" />";
			echo "<input name=\"li_" . $i . "_product_id\" type=\"hidden\" value=\"" . $this->order_details[$i]->product_id . "\" />";
			if( $this->order_details[$i]->use_advanced_optionset ){ // Advanced Options
				$advanced_options = $this->mysqli->get_order_options( $this->order_details[$i]->orderdetail_id );
				for( $j=0; $j<count( $advanced_options ); $j++ ){
					echo "<input name=\"li_" . $i . "_option_" . $j . "_name\" type=\"hidden\" value=\"" . $advanced_options[$j]->option_label . "\" />";
					echo "<input name=\"li_" . $i . "_option_" . $j . "_value\" type=\"hidden\" value=\"" . $advanced_options[$j]->option_value . "\" />";
					echo "<input name=\"li_" . $i . "_option_" . $j . "_surcharge\" type=\"hidden\" value=\"" . $advanced_options[$j]->option_price_change . "\" />";
				}
				
			}else{ // Basic Options
			
				if( $this->order_details[$i]->optionitem_id_1 != 0 ){
					echo "<input name=\"li_" . $i . "_option_0_name\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_label_1 . "\" />";
					echo "<input name=\"li_" . $i . "_option_0_value\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_name_1 . "\" />";
					echo "<input name=\"li_" . $i . "_option_0_surcharge\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_price_1 . "\" />";
				}
				
				if( $this->order_details[$i]->optionitem_id_2 != 0 ){
					echo "<input name=\"li_" . $i . "_option_1_name\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_label_2 . "\" />";
					echo "<input name=\"li_" . $i . "_option_1_value\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_name_2 . "\" />";
					echo "<input name=\"li_" . $i . "_option_1_surcharge\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_price_2 . "\" />";
				}
				
				if( $this->order_details[$i]->optionitem_id_3 != 0 ){
					echo "<input name=\"li_" . $i . "_option_2_name\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_label_3 . "\" />";
					echo "<input name=\"li_" . $i . "_option_2_value\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_name_3 . "\" />";
					echo "<input name=\"li_" . $i . "_option_2_surcharge\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_price_3 . "\" />";
				}
				
				if( $this->order_details[$i]->optionitem_id_4 != 0 ){
					echo "<input name=\"li_" . $i . "_option_3_name\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_label_4 . "\" />";
					echo "<input name=\"li_" . $i . "_option_3_value\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_name_4 . "\" />";
					echo "<input name=\"li_" . $i . "_option_3_surcharge\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_price_4 . "\" />";
				}
				
				if( $this->order_details[$i]->optionitem_id_5 != 0 ){
					echo "<input name=\"li_" . $i . "_option_4_name\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_label_5 . "\" />";
					echo "<input name=\"li_" . $i . "_option_4_value\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_name_5 . "\" />";
					echo "<input name=\"li_" . $i . "_option_4_surcharge\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_price_5 . "\" />";
				}
				
			}
		}
		
		/* Setup Order Totals */
		if( $this->order->shipping_total > 0 ){
			echo "<input name=\"li_" . $i . "_type\" type=\"hidden\" value=\"shipping\" />";
			echo "<input name=\"li_" . $i . "_name\" type=\"hidden\" value=\"" . $this->order->shipping_method. "\" />";
			echo "<input name=\"li_" . $i . "_price\" type=\"hidden\" value=\"" . number_format( $this->order->shipping_total, 2, '.', '' ) . "\" />";
			$i++;
		}
		if( $tax_total > 0 ){
			echo "<input name=\"li_" . $i . "_type\" type=\"hidden\" value=\"tax\" />";
			echo "<input name=\"li_" . $i . "_name\" type=\"hidden\" value=\"" . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tax' ) . "\" />";
			echo "<input name=\"li_" . $i . "_price\" type=\"hidden\" value=\"" . number_format( $tax_total, 2, '.', '' ) . "\" />";
			$i++;
		}
		if( $this->order->discount_total > 0 ){
			echo "<input name=\"li_" . $i . "_type\" type=\"hidden\" value=\"coupon\" />";
			echo "<input name=\"li_" . $i . "_name\" type=\"hidden\" value=\"" . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_discounts' ) . "\" />";
			echo "<input name=\"li_" . $i . "_price\" type=\"hidden\" value=\"" . number_format( $this->order->discount_total, 2, '.', '' ) . "\" />";
			$i++;
		}
		
		//echo "<input type=\"hidden\" name=\"return\" value=\"". $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order_id . "\" />";
		//echo "<input type=\"hidden\" name=\"cancel_return\" value=\"". $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment\" />";
		
	}
	
	public function display_auto_forwarding_form( ){
		
		$sid = get_option( 'ec_option_2checkout_thirdparty_sid' );
		$mode = '2CO';
		$currency_code = get_option( 'ec_option_2checkout_thirdparty_currency_code' );
		$lang = get_option( 'ec_option_2checkout_thirdparty_lang' );
		$sandbox_mode = get_option( 'ec_option_2checkout_thirdparty_sandbox_mode' );
		$demo_mode = get_option( 'ec_option_2checkout_thirdparty_demo_mode' );
		$purchase_step = "payment-method";
		$receipt_link_url = site_url( );
		//$webhook_url = plugins_url( EC_PLUGIN_DIRECTORY . "/inc/scripts/ec_2checkout_complete.php" );
		
		if( $sandbox_mode )					$checkout_url = "https://sandbox.2checkout.com/checkout/purchase";
		else								$checkout_url = "https://www.2checkout.com/checkout/purchase";
		
		$tax = new ec_tax( 0.00, 0.00, 0.00, $this->order->billing_state, $this->order->billing_country );
		$tax_total = number_format( $this->order->tax_total + $this->order->duty_total + $this->order->gst_total + $this->order->pst_total + $this->order->hst_total, 2 );
		if( !$tax->vat_included )
			$tax_total = number_format( $tax_total + $this->order->vat_total, 2 );
		
		echo "<style>
		.ec_third_party_submit_button{ width:100%; text-align:center; }
		.ec_third_party_submit_button > input{ margin-top:150px; width:300px; height:45px; background-color:#38E; color:#FFF; font-weight:bold; text-transform:uppercase; border:1px solid #A2C0D8; cursor:pointer; }
		.ec_third_party_submit_button > input:hover{ background-color:#7A99BF; }
		.ec_third_party_loader{ display:block !important; position:absolute; top:50%; left:50%; }
		@-webkit-keyframes ec_third_party_loader {
		  0% {
			-webkit-transform: rotate(0deg);
			-moz-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		  }
		
		  100% {
			-webkit-transform: rotate(360deg);
			-moz-transform: rotate(360deg);
			-ms-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		  }
		}
		
		@-moz-keyframes ec_third_party_loader {
		  0% {
			-webkit-transform: rotate(0deg);
			-moz-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		  }
		
		  100% {
			-webkit-transform: rotate(360deg);
			-moz-transform: rotate(360deg);
			-ms-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		  }
		}
		
		@-o-keyframes ec_third_party_loader {
		  0% {
			-webkit-transform: rotate(0deg);
			-moz-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		  }
		
		  100% {
			-webkit-transform: rotate(360deg);
			-moz-transform: rotate(360deg);
			-ms-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		  }
		}
		
		@keyframes ec_third_party_loader {
		  0% {
			-webkit-transform: rotate(0deg);
			-moz-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		  }
		
		  100% {
			-webkit-transform: rotate(360deg);
			-moz-transform: rotate(360deg);
			-ms-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		  }
		}
		
		/* Styles for old versions of IE */
		.ec_third_party_loader {
		  font-family: sans-serif;
		  font-weight: 100;
		}
		
		/* :not(:required) hides this rule from IE9 and below */
		.ec_third_party_loader:not(:required) {
		  -webkit-animation: ec_third_party_loader 1250ms infinite linear;
		  -moz-animation: ec_third_party_loader 1250ms infinite linear;
		  -ms-animation: ec_third_party_loader 1250ms infinite linear;
		  -o-animation: ec_third_party_loader 1250ms infinite linear;
		  animation: ec_third_party_loader 1250ms infinite linear;
		  border: 8px solid #3388ee;
		  border-right-color: transparent;
		  border-radius: 16px;
		  box-sizing: border-box;
		  display: inline-block;
		  position: relative;
		  overflow: hidden;
		  text-indent: -9999px;
		  width: 32px;
		  height: 32px;
		}
		</style>";
		
		echo "<div style=\"display:none;\" class=\"ec_third_party_loader\">Loading...</div>";
		
		echo "<form name=\"ec_2checkout_auto_form\" action=\"" . $checkout_url . "\" method=\"post\">";
		echo "<input name=\"sid\" id=\"sid\" type=\"hidden\" value=\"" . $sid . "\" />";
		echo "<input name=\"mode\" id=\"mode\" type=\"hidden\" value=\"" . $mode . "\" />";
		if( $demo_mode )
			echo "<input name=\"demo\" id=\"demo\" type=\"hidden\" value=\"Y\" />";
		echo "<input name=\"currency_code\" id=\"currency_code\" type=\"hidden\" value=\"" . $currency_code . "\" />";
		echo "<input name=\"lang\" id=\"lang\" type=\"hidden\" value=\"" . $lang . "\" />";
		echo "<input name=\"merchant_order_id\" id=\"merchant_order_id\" type=\"hidden\" value=\"" . $this->order_id . "\" />";
		echo "<input name=\"purchase_step\" id=\"purchase_step\" type=\"hidden\" value=\"" . $purchase_step . "\" />";
		echo "<input name=\"x_receipt_link_url\" id=\"x_receipt_link_url\" type=\"hidden\" value=\"" . $receipt_link_url . "\" />";
		
		/* Billing and Shipping */
		echo "<input name=\"card_holder_name\" id=\"first_name\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_first_name, ENT_QUOTES ) . " " . htmlspecialchars( $this->order->billing_last_name, ENT_QUOTES ) . "\" />";	
		echo "<input name=\"address1\" id=\"address1\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_address_line_1, ENT_QUOTES ) . "\" />";
		if( get_option( 'ec_option_use_address2' ) )
			echo "<input name=\"address2\" id=\"address12\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_address_line_2, ENT_QUOTES ) . "\" />";
		echo "<input name=\"city\" id=\"city\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_city, ENT_QUOTES ) . "\" />";
		echo "<input name=\"state\" id=\"state\" type=\"hidden\" value=\"" . htmlspecialchars( strtoupper($this->order->billing_state ), ENT_QUOTES ) . "\" />";
		echo "<input name=\"zip\" id=\"zip\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_zip, ENT_QUOTES ) . "\" />";
		echo "<input name=\"country\" id=\"country\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_country, ENT_QUOTES ) . "\" />";
		echo "<input name=\"email\" id=\"email\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->user_email, ENT_QUOTES ) . "\" />";
		if( get_option( 'ec_option_collect_user_phone' ) )
			echo "<input name=\"phone\" id=\"phone\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_phone, ENT_QUOTES ) . "\" />";
		
		if( get_option( 'ec_option_use_shipping' ) ){
			echo "<input name=\"ship_name\" id=\"ship_name\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->shipping_first_name, ENT_QUOTES ) . " " . htmlspecialchars( $this->order->shipping_last_name, ENT_QUOTES ) . "\" />";	
			echo "<input name=\"ship_street_address\" id=\"ship_street_address\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->shipping_address_line_1, ENT_QUOTES ) . "\" />";
			if( get_option( 'ec_option_use_address2' ) )
				echo "<input name=\"ship_street_address2\" id=\"ship_street_address2\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->shipping_address_line_2, ENT_QUOTES ) . "\" />";
			echo "<input name=\"ship_city\" id=\"ship_city\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->shipping_city, ENT_QUOTES ) . "\" />";
			echo "<input name=\"ship_state\" id=\"ship_state\" type=\"hidden\" value=\"" . htmlspecialchars( strtoupper($this->order->shipping_state ), ENT_QUOTES ) . "\" />";
			echo "<input name=\"ship_zip\" id=\"ship_zip\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->shipping_zip, ENT_QUOTES ) . "\" />";
			echo "<input name=\"ship_country\" id=\"ship_country\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->shipping_country, ENT_QUOTES ) . "\" />";
		}
		
		// Setup Cart Items
		for( $i=0; $i<count( $this->order_details ); $i++ ){
			$tangible = 'N';
			if( $this->order_details[$i]->is_shippable )
				$tangible = 'Y';
			echo "<input name=\"li_" . $i . "_type\" type=\"hidden\" value=\"product\" />";
			echo "<input name=\"li_" . $i . "_name\" type=\"hidden\" value=\"" . str_replace( ">", "", str_replace( "<", "", str_replace( '"', '&quot;', substr( $this->order_details[$i]->title, 0, 128 ) ) ) ) . "\" />";
			echo "<input name=\"li_" . $i . "_quantity\" type=\"hidden\" value=\"" . $this->order_details[$i]->quantity . "\" />";
			echo "<input name=\"li_" . $i . "_price\" type=\"hidden\" value=\"" . number_format( ( $this->order_details[$i]->total_price / $this->order_details[$i]->quantity ), 2, '.', '' ) . "\" />";
			echo "<input name=\"li_" . $i . "_tangible\" type=\"hidden\" value=\"" . $tangible . "\" />";
			echo "<input name=\"li_" . $i . "_product_id\" type=\"hidden\" value=\"" . $this->order_details[$i]->product_id . "\" />";
			if( $this->order_details[$i]->use_advanced_optionset ){ // Advanced Options
				$advanced_options = $this->mysqli->get_order_options( $this->order_details[$i]->orderdetail_id );
				for( $j=0; $j<count( $advanced_options ); $j++ ){
					echo "<input name=\"li_" . $i . "_option_" . $j . "_name\" type=\"hidden\" value=\"" . $advanced_options[$j]->option_label . "\" />";
					echo "<input name=\"li_" . $i . "_option_" . $j . "_value\" type=\"hidden\" value=\"" . $advanced_options[$j]->option_value . "\" />";
					echo "<input name=\"li_" . $i . "_option_" . $j . "_surcharge\" type=\"hidden\" value=\"" . $advanced_options[$j]->option_price_change . "\" />";
				}
				
			}else{ // Basic Options
			
				if( $this->order_details[$i]->optionitem_id_1 != 0 ){
					echo "<input name=\"li_" . $i . "_option_0_name\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_label_1 . "\" />";
					echo "<input name=\"li_" . $i . "_option_0_value\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_name_1 . "\" />";
					echo "<input name=\"li_" . $i . "_option_0_surcharge\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_price_1 . "\" />";
				}
				
				if( $this->order_details[$i]->optionitem_id_2 != 0 ){
					echo "<input name=\"li_" . $i . "_option_1_name\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_label_2 . "\" />";
					echo "<input name=\"li_" . $i . "_option_1_value\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_name_2 . "\" />";
					echo "<input name=\"li_" . $i . "_option_1_surcharge\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_price_2 . "\" />";
				}
				
				if( $this->order_details[$i]->optionitem_id_3 != 0 ){
					echo "<input name=\"li_" . $i . "_option_2_name\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_label_3 . "\" />";
					echo "<input name=\"li_" . $i . "_option_2_value\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_name_3 . "\" />";
					echo "<input name=\"li_" . $i . "_option_2_surcharge\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_price_3 . "\" />";
				}
				
				if( $this->order_details[$i]->optionitem_id_4 != 0 ){
					echo "<input name=\"li_" . $i . "_option_3_name\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_label_4 . "\" />";
					echo "<input name=\"li_" . $i . "_option_3_value\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_name_4 . "\" />";
					echo "<input name=\"li_" . $i . "_option_3_surcharge\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_price_4 . "\" />";
				}
				
				if( $this->order_details[$i]->optionitem_id_5 != 0 ){
					echo "<input name=\"li_" . $i . "_option_4_name\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_label_5 . "\" />";
					echo "<input name=\"li_" . $i . "_option_4_value\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_name_5 . "\" />";
					echo "<input name=\"li_" . $i . "_option_4_surcharge\" type=\"hidden\" value=\"" . $this->order_details[$i]->optionitem_price_5 . "\" />";
				}
				
			}
		}
		
		/* Setup Order Totals */
		if( $this->order->shipping_total > 0 ){
			echo "<input name=\"li_" . $i . "_type\" type=\"hidden\" value=\"shipping\" />";
			echo "<input name=\"li_" . $i . "_name\" type=\"hidden\" value=\"" . $this->order->shipping_method. "\" />";
			echo "<input name=\"li_" . $i . "_price\" type=\"hidden\" value=\"" . number_format( $this->order->shipping_total, 2, '.', '' ) . "\" />";
			$i++;
		}
		if( $tax_total > 0 ){
			echo "<input name=\"li_" . $i . "_type\" type=\"hidden\" value=\"tax\" />";
			echo "<input name=\"li_" . $i . "_name\" type=\"hidden\" value=\"" . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tax' ) . "\" />";
			echo "<input name=\"li_" . $i . "_price\" type=\"hidden\" value=\"" . number_format( $tax_total, 2, '.', '' ) . "\" />";
			$i++;
		}
		if( $this->order->discount_total > 0 ){
			echo "<input name=\"li_" . $i . "_type\" type=\"hidden\" value=\"coupon\" />";
			echo "<input name=\"li_" . $i . "_name\" type=\"hidden\" value=\"" . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_discounts' ) . "\" />";
			echo "<input name=\"li_" . $i . "_price\" type=\"hidden\" value=\"" . number_format( $this->order->discount_total, 2, '.', '' ) . "\" />";
			$i++;
		}
		//echo "<input type=\"hidden\" name=\"return\" value=\"". $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order_id . "\" />";
		//echo "<input type=\"hidden\" name=\"cancel_return\" value=\"". $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment\" />";
		
		echo "<div class=\"ec_third_party_submit_button\"><input type=\"submit\" value=\"" . $GLOBALS['language']->get_text( "cart_payment_information", "cart_payment_information_third_party" ) . " PayPal\" id=\"ec_third_party_submit_payment\" /></div>";
		echo "</form>";
		echo "<SCRIPT>document.getElementById( 'ec_third_party_submit_payment' ).style.display = 'none';</SCRIPT>";
		echo "<SCRIPT data-cfasync=\"false\" LANGUAGE=\"Javascript\">document.ec_2checkout_auto_form.submit( );</SCRIPT>";
	}
	
}

add_action( 'wp', 'ec_2checkout_thirdparty_response' );
function ec_2checkout_thirdparty_response( ){
	
	if( isset( $_POST['md5_hash'] ) || isset( $_POST['key'] ) ){
		$ec_db = new ec_db_admin( );
		$ec_db->insert_response( 0, 1, "2Checkout POST", print_r( $_POST, true ) );
	}
	
	if( isset( $_POST['md5_hash'] ) && ec_is_valid_2checkout_thirdparty_webhook( ) ){
		
		if( $_POST['message_type'] == "ORDER_CREATED" && $_POST['invoice_status'] == "approved" ){ // Order Created
			ec_2checkout_thirdparty_payment_complete( $_POST['vendor_order_id'] );
		
		}else if( $_POST['message_type'] == "INVOICE_STATUS_CHANGED" && $_POST['invoice_status'] == "approved" ){ // Order Status Changed
			ec_2checkout_thirdparty_payment_complete( $_POST['vendor_order_id'] );
		
		}else if( $_POST['message_type'] == "REFUND_ISSUED" ){ // Order Refunded
			ec_2checkout_thirdparty_order_refunded( $_POST['vendor_order_id'] );
		
		}
		
	}else if( isset( $_GET['key'] ) && ec_is_valid_2checkout_thirdparty_return( ) ){ // User Returned From 2Checkout
		ec_2checkout_thirdparty_payment_complete( $_GET['merchant_order_id'] );
		
		$cart_page_id = get_option('ec_option_cartpage');
		$cart_page = get_permalink( $cart_page_id );
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$cart_page = $https_class->makeUrlHttps( $cart_page );
		}
		if( substr_count( $cart_page, '?' ) )					$permalink_divider = "&";
		else													$permalink_divider = "?";
		
		wp_redirect( $cart_page . $permalink_divider . "ec_page=checkout_success&order_id=" . $_GET['merchant_order_id'] );
	}
	
}

function ec_is_valid_2checkout_thirdparty_webhook( ){
	
	$is_valid = false;
	$secret_word = get_option( 'ec_option_2checkout_thirdparty_secret_word' );
	$sid = get_option( 'ec_option_2checkout_thirdparty_sid' );
	$hash_order = $_POST['sale_id'];
	$hash_invoice = $_POST['invoice_id'];
	$string_to_hash = strtoupper( md5( $hash_order . $sid . $hash_invoice . $secret_word ) );

	if( $string_to_hash != $_POST['md5_hash'] )
		return false;
	else
		return true;
	
}

function ec_is_valid_2checkout_thirdparty_return( ){
	
	$is_valid = false;
	$secret_word = get_option( 'ec_option_2checkout_thirdparty_secret_word' );
	$sid = get_option( 'ec_option_2checkout_thirdparty_sid' );
	$hash_order = $_GET['order_number'];
	$hash_total = $_GET['total'];
	$string_to_hash = strtoupper( md5( $secret_word . $sid . $hash_order . $hash_total ) );

	if( $string_to_hash != $_GET['key'] )
		return false;
	else
		return true;
	
}

function ec_2checkout_thirdparty_payment_complete( $order_id ){
	
	$ec_db = new ec_db_admin( );
	$order_row = $ec_db->get_order_row_admin( $order_id );
	$ec_db->update_order_status( $order_id, "10" );
	do_action( 'wpeasycart_order_paid', $order_id );
	
	// send email
	$order_display = new ec_orderdisplay( $order_row, true, true );
	$order_display->send_email_receipt( );
	$order_display->send_gift_cards( );
	
}

function ec_2checkout_thirdparty_order_refunded( $order_id ){
	
	$ec_db = new ec_db_admin( );
	$ec_db->update_order_status( $order_id, "16" );
	do_action( 'wpeasycart_full_order_refund', $order_id );
	
	// Check for gift card to refund
	global $wpdb;
	$order_details = $wpdb->get_results( $wpdb->prepare( "SELECT is_giftcard, giftcard_id FROM ec_orderdetail WHERE order_id = %d", $order_id ) );
	foreach( $order_details as $detail ){
		if( $detail->is_giftcard ){
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_giftcard WHERE ec_giftcard.giftcard_id = %s", $detail->giftcard_id ) );
		}
	}
	
}
?>