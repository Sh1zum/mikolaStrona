<?php
class ec_payfort extends ec_third_party{
	
	public function display_form_start( ){
		
		$requestParams = $this->get_request_params( );
		
		$redirect_url = $this->get_url( );
		echo "<form action='" . $redirect_url . "' method='post' name='frm'>\n";
		foreach( $requestParams as $a => $b ){
			echo "\t<input type='hidden' name='" . htmlentities( $a ) . "' value='" . htmlentities( $b )."'>\n";
		}
		
	}
	
	private function get_request_params( ){
		
		if( isset( $_COOKIE['ec_convert_to'] ) && get_option( 'ec_option_payfort_use_currency_service' ) ){
			$currency = strtoupper( htmlspecialchars( $_COOKIE['ec_convert_to'], ENT_QUOTES ) );
		}else{
			$currency = get_option( 'ec_option_payfort_currency_code' );
		}
		
		$requestParams = array(
			'access_code'			=> get_option( 'ec_option_payfort_access_code' ),
			'amount'				=> number_format( 100 * $GLOBALS['currency']->convert_price( $this->order->grand_total ), 0, '', '' ),
			'currency'				=> $currency,
			'customer_email'		=> htmlspecialchars( $this->order->user_email, ENT_QUOTES ),
			'payment_option'		=> $GLOBALS['ec_cart_data']->cart_data->payment_type,
			'merchant_reference'	=> $this->order_id,
			'merchant_extra'		=> $GLOBALS['ec_cart_data']->ec_cart_id,
			'command'				=> 'PURCHASE',
			'merchant_identifier'	=> get_option( 'ec_option_payfort_merchant_id' ),
			'language'				=> get_option( 'ec_option_payfort_language' ),
			'return_url'			=> $this->cart_page . $this->permalink_divider . "order_id=" . $this->order_id,
			'customer_ip'			=> $_SERVER['REMOTE_ADDR'],
			'customer_name'			=> htmlspecialchars( $this->order->billing_first_name, ENT_QUOTES ) . " " . htmlspecialchars( $this->order->billing_last_name, ENT_QUOTES )
		);
		
		if( $GLOBALS['ec_cart_data']->cart_data->payment_type == "SADAD" )
			$requestParams['sadad_olp'] = get_option( 'ec_option_payfort_sadad_olp' );
		
		$signature = $this->get_signature( $requestParams, get_option( 'ec_option_payfort_request_phrase' ) );
		$requestParams['signature'] = $signature;
		return $requestParams;
		
	}
	
	private function get_signature( $requestParams, $phrase ){
		
		ksort( $requestParams );
		$query_string = "";
		foreach( $requestParams as $key => $value ){
			$query_string .= $key . '=' . $value;
		}
		$query_string = $phrase . $query_string . $phrase;
		return hash( get_option( 'ec_option_payfort_sha_type' ), $query_string );
		
	}
	
	private function get_url( ){
		
		if( get_option( 'ec_option_payfort_test_mode' ) )			
			return "https://sbcheckout.payfort.com/FortAPI/paymentPage";
		else
			return "https://checkout.payfort.com/FortAPI/paymentPage";
	
	}
	
	public function display_auto_forwarding_form( ){
		
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
		
		$requestParams = $this->get_request_params( );
		
		$redirect_url = $this->get_url( );
		echo "<form action='" . $redirect_url . "' method='post' name='frm'>\n";
		foreach( $requestParams as $a => $b ){
			echo "\t<input type='hidden' name='" . htmlentities( $a ) . "' value='" . htmlentities( $b )."'>\n";
		}
		
		echo "<div class=\"ec_third_party_submit_button\"><input type=\"submit\" value=\"" . $GLOBALS['language']->get_text( "cart_payment_information", "cart_payment_information_third_party" ) . " PayPal\" id=\"ec_third_party_submit_payment\" /></div>";
		echo "</form>";
		echo "<SCRIPT>document.getElementById( 'ec_third_party_submit_payment' ).style.display = 'none';</SCRIPT>";
		echo "<SCRIPT data-cfasync=\"false\" LANGUAGE=\"Javascript\">document.frm.submit();</SCRIPT>";
	}
	
	public function validate_response( ){
		$responseParams = $this->get_response_params( );
		$signature = $this->get_signature( $responseParams, get_option( 'ec_option_payfort_response_phrase' ) );
		$payfort_signature = $_GET['signature'];
		$this->mysqli->insert_response( $_GET['merchant_reference'], 0, "Payfort Response", print_r( $responseParams, true ) );
		if( $signature == $payfort_signature )
			return true;
		else
			return false;
	}
	
	public function validate_post_response( ){
		$responseParams = $this->get_post_response_params( );
		$signature = $this->get_signature( $responseParams, get_option( 'ec_option_payfort_response_phrase' ) );
		$payfort_signature = $_POST['signature'];
		$this->mysqli->insert_response( $_POST['merchant_reference'], 0, "Payfort Response", print_r( $responseParams, true ) );
		if( $signature == $payfort_signature )
			return true;
		else
			return false;
	}
	
	private function get_response_params( ){
		
		$responseParams = array( 
			"access_code"			=> $_GET['access_code'],
			"status"				=> $_GET['status'],
			"eci"					=> $_GET['eci'],
			"fort_id"				=> $_GET['fort_id'],
			"response_code"			=> $_GET['response_code'],
			"customer_email"		=> $_GET['customer_email'],
			"customer_name"			=> $_GET['customer_name'],
			"customer_ip"			=> $_GET['customer_ip'],
			"currency"				=> $_GET['currency'],
			"merchant_reference"	=> $_GET['merchant_reference'],
			"amount"				=> $_GET['amount'],
			"response_message"		=> $_GET['response_message'],
			"command"				=> $_GET['command'],
			"language"				=> $_GET['language'],
			"merchant_identifier"	=> $_GET['merchant_identifier'],
			"merchant_extra"		=> $_GET['merchant_extra']
		);
		
		if( isset( $_GET['card_number'] ) )
			$responseParams['card_number'] = $_GET['card_number'];
		
		if( isset( $_GET['payment_option'] ) )
			$responseParams['payment_option'] = $_GET['payment_option'];
		
		if( isset( $_GET['expiry_date'] ) )
			$responseParams['expiry_date'] = $_GET['expiry_date'];
		
		if( isset( $_GET['remember_me'] ) )
			$responseParams['remember_me'] = $_GET['remember_me'];
		
		if( isset( $_GET['payment_option'] ) )
			$responseParams['payment_option'] = $_GET['payment_option'];
		
		if( isset( $_GET['token_name'] ) )
			$responseParams['token_name'] = $_GET['token_name'];
		
		if( isset( $_GET['authorization_code'] ) )
			$responseParams['authorization_code'] = $_GET['authorization_code'];
		
		return $responseParams;
		
	}
	
	private function get_post_response_params( ){
		$responseParams = array( 
			"access_code"			=> $_POST['access_code'],
			"status"				=> $_POST['status'],
			"eci"					=> $_POST['eci'],
			"fort_id"				=> $_POST['fort_id'],
			"response_code"			=> $_POST['response_code'],
			"customer_email"		=> $_POST['customer_email'],
			"customer_name"			=> $_POST['customer_name'],
			"customer_ip"			=> $_POST['customer_ip'],
			"currency"				=> $_POST['currency'],
			"merchant_reference"	=> $_POST['merchant_reference'],
			"amount"				=> $_POST['amount'],
			"response_message"		=> $_POST['response_message'],
			"command"				=> $_POST['command'],
			"language"				=> $_POST['language'],
			"merchant_identifier"	=> $_POST['merchant_identifier'],
			"merchant_extra"		=> $_POST['merchant_extra']
		);
		
		if( isset( $_POST['card_number'] ) )
			$responseParams['card_number'] = $_POST['card_number'];
		
		if( isset( $_POST['payment_option'] ) )
			$responseParams['payment_option'] = $_POST['payment_option'];
		
		if( isset( $_POST['expiry_date'] ) )
			$responseParams['expiry_date'] = $_POST['expiry_date'];
		
		if( isset( $_POST['remember_me'] ) )
			$responseParams['remember_me'] = $_POST['remember_me'];
		
		if( isset( $_POST['payment_option'] ) )
			$responseParams['payment_option'] = $_POST['payment_option'];
		
		if( isset( $_POST['token_name'] ) )
			$responseParams['token_name'] = $_POST['token_name'];
		
		if( isset( $_POST['authorization_code'] ) )
			$responseParams['authorization_code'] = $_POST['authorization_code'];
		
		return $responseParams;
	}
	
	public function order_success( ){
		global $wpdb;
		$db_admin = new ec_db_admin( );
		$order_row = $db_admin->get_order_row_admin( $_GET['merchant_reference'] );
		$orderdetails = $db_admin->get_order_details_admin( $_GET['merchant_reference'] );
		if( $order_row ){
			$this->mysqli->update_order_status( $_GET['merchant_reference'], "10" );
			do_action( 'wpeasycart_order_paid', $_GET['merchant_reference'] );
			
			/* Update Stock Quantity */
			foreach( $orderdetails as $orderdetail ){
				$product = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.* FROM ec_product WHERE ec_product.product_id = %d", $orderdetail->product_id ) );
				if( $product ){
					if( $product->use_optionitem_quantity_tracking )	
						$this->mysqli->update_quantity_value( $orderdetail->quantity, $orderdetail->product_id, $orderdetail->optionitem_id_1, $orderdetail->optionitem_id_2, $orderdetail->optionitem_id_3, $orderdetail->optionitem_id_4, $orderdetail->optionitem_id_5 );
					$this->mysqli->update_product_stock( $orderdetail->product_id, $orderdetail->quantity );
				}
			}
			
			// send email
			$order_display = new ec_orderdisplay( $order_row, true, true );
			$order_display->send_email_receipt( );
			$order_display->send_gift_cards( );
			
			// clear cart
			$this->mysqli->clear_tempcart( $GLOBALS['ec_cart_data']->ec_cart_id );
			$GLOBALS['ec_cart_data']->checkout_session_complete( );
		}
		wp_redirect( $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $_GET['merchant_reference'] );
	}
	
	public function order_success_post( ){
		global $wpdb;
		$db_admin = new ec_db_admin( );
		$order_row = $db_admin->get_order_row_admin( $_POST['merchant_reference'] );
		$orderdetails = $db_admin->get_order_details_admin( $_POST['merchant_reference'] );
		if( $order_row ){
			$this->mysqli->update_order_status( $_POST['merchant_reference'], "10" );
			do_action( 'wpeasycart_order_paid', $_POST['merchant_reference'] );
			
			/* Update Stock Quantity */
			foreach( $orderdetails as $orderdetail ){
				$product = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.* FROM ec_product WHERE ec_product.product_id = %d", $orderdetail->product_id ) );
				if( $product ){
					if( $product->use_optionitem_quantity_tracking )	
						$this->mysqli->update_quantity_value( $orderdetail->quantity, $orderdetail->product_id, $orderdetail->optionitem_id_1, $orderdetail->optionitem_id_2, $orderdetail->optionitem_id_3, $orderdetail->optionitem_id_4, $orderdetail->optionitem_id_5 );
					$this->mysqli->update_product_stock( $orderdetail->product_id, $orderdetail->quantity );
				}
			}
			
			// send email
			$order_display = new ec_orderdisplay( $order_row, true, true );
			$order_display->send_email_receipt( );
			$order_display->send_gift_cards( );
			
			// clear cart
			$this->mysqli->clear_tempcart( $_POST['merchant_extra'] );
			$this->mysqli->remove_cart_data( $_POST['merchant_extra'] );
		}
	}
	
	public function refund_success( ){
		global $wpdb;
		$this->mysqli->update_order_status( $_GET['merchant_reference'], "16" );
		do_action( 'wpeasycart_full_order_refund', $_GET['merchant_reference'] );
		
		// Check for gift card to refund
		$order_details = $wpdb->get_results( $wpdb->prepare( "SELECT is_giftcard, giftcard_id FROM ec_orderdetail WHERE order_id = %d", $_GET['merchant_reference'] ) );
		foreach( $order_details as $detail ){
			if( $detail->is_giftcard ){
				$wpdb->query( $wpdb->prepare( "DELETE FROM ec_giftcard WHERE ec_giftcard.giftcard_id = %s", $detail->giftcard_id ) );
			}
		}
	}
	
	public function refund_success_post( ){
		global $wpdb;
		$this->mysqli->update_order_status( $_POST['merchant_reference'], "16" );
		do_action( 'wpeasycart_full_order_refund', $_POST['merchant_reference'] );
		
		// Check for gift card to refund
		$order_details = $wpdb->get_results( $wpdb->prepare( "SELECT is_giftcard, giftcard_id FROM ec_orderdetail WHERE order_id = %d", $_POST['merchant_reference'] ) );
		foreach( $order_details as $detail ){
			if( $detail->is_giftcard ){
				$wpdb->query( $wpdb->prepare( "DELETE FROM ec_giftcard WHERE ec_giftcard.giftcard_id = %s", $detail->giftcard_id ) );
			}
		}
	}
	
	public function payment_failed( ){
		global $wpdb;
		$this->mysqli->remove_order( $_GET['merchant_reference'] );
		
		// Check for gift card to refund
		$order_details = $wpdb->get_results( $wpdb->prepare( "SELECT is_giftcard, giftcard_id FROM ec_orderdetail WHERE order_id = %d", $_GET['merchant_reference'] ) );
		foreach( $order_details as $detail ){
			if( $detail->is_giftcard ){
				$wpdb->query( $wpdb->prepare( "DELETE FROM ec_giftcard WHERE ec_giftcard.giftcard_id = %s", $detail->giftcard_id ) );
			}
		}
		wp_redirect( $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=thirdparty_failed" );
	}
	
	public function payment_failed_post( ){
		global $wpdb;
		$this->mysqli->remove_order( $_POST['merchant_reference'] );
		
		// Check for gift card to refund
		$order_details = $wpdb->get_results( $wpdb->prepare( "SELECT is_giftcard, giftcard_id FROM ec_orderdetail WHERE order_id = %d", $_POST['merchant_reference'] ) );
		foreach( $order_details as $detail ){
			if( $detail->is_giftcard ){
				$wpdb->query( $wpdb->prepare( "DELETE FROM ec_giftcard WHERE ec_giftcard.giftcard_id = %s", $detail->giftcard_id ) );
			}
		}
	}
	
	public function payment_incomplete( ){
		$this->mysqli->remove_order( $_GET['merchant_reference'] );
		wp_redirect( $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=thirdparty_failed" );
	}
	
	public function payment_incomplete_post( ){
		$this->mysqli->remove_order( $_POST['merchant_reference'] );
	}
	
	// Currency Conversion Service
	public function convert_price( $start_price ){
		
		$start_currency = get_option( 'ec_option_payfort_currency_code' );
		if( get_option( 'ec_option_payfort_test_mode' ) )			
			$gateway_url = "https://sbpaymentservices.PayFort.com/FortAPI/paymentApi";
		else
			$gateway_url = "https://paymentservices.PayFort.com/FortAPI/paymentApi";
			
		$requestParams = array(
			"service_command"		=> "CURRENCY_CONVERSION",
			"access_code"			=> get_option( 'ec_option_payfort_access_code' ),
			"merchant_identifier"	=> get_option( 'ec_option_payfort_merchant_id' ),
			"amount"				=> number_format( $start_price * 100, 0, '', '' ),
			"currency"				=> get_option( 'ec_option_payfort_currency_code' ),
			"language"				=> get_option( 'ec_option_payfort_language' ),
			"converted_currency"	=> strtoupper( htmlspecialchars( $_COOKIE['ec_convert_to'], ENT_QUOTES ) ),
		);
		
		$signature = $this->get_signature( $requestParams, get_option( 'ec_option_payfort_request_phrase' ) );
		$requestParams['signature'] = $signature;
		
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json;charset=UTF-8'
        ) );
        curl_setopt( $ch, CURLOPT_URL, $gateway_url);
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_FAILONERROR, 1);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_ENCODING, "compress, gzip");
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 0 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $requestParams ) );
		
		$response = curl_exec($ch);
		if( $response === false ){
			$this->mysqli->insert_response( 0, 1, "Payfort Conversion CURL ERROR", curl_error( $ch ) );
			curl_close ( $ch );
			return $start_price;
		}else
			$this->mysqli->insert_response( 0, 0, "Payfort Conversion Response", print_r( $response, true ) );
		curl_close ($ch);
		
		$result = json_decode( $response );
		if( isset( $result->converted_amount ) )
			return $result->converted_amount / 100;
		else
			return $start_price;
		
	}
	
}

add_action( 'wpeasycart_third_party_checkout_box', 'wpeasycart_payfort_add_payment_type_box' );
function wpeasycart_payfort_add_payment_type_box( ){
	if( get_option( 'ec_option_payment_third_party' ) == "payfort" ){
		echo '<strong>Select Your Payment Method:</strong>';
		echo '<select name="ec_payfort_payment_type">';
			echo '<option value="MASTERCARD">MasterCard</option>';
			echo '<option value="VISA">Visa</option>';
			if( get_option( 'ec_option_payfort_use_sadad' ) )
			echo '<option value="SADAD">Sadad</option>';
			if( get_option( 'ec_option_payfort_use_naps' ) )
			echo '<option value="NAPS">Naps</option>';
		echo '</select>';
	}
}

add_action( 'wpeasycart_submit_order_complete', 'wpeasycart_payfort_custom_checkout_data' );
function wpeasycart_payfort_custom_checkout_data( ){
	$GLOBALS['ec_cart_data']->cart_data->payment_type = $_POST['ec_payfort_payment_type'];
	$GLOBALS['ec_cart_data']->save_session_to_db( );
}

add_action( 'wp', 'wpeasycart_payfort_process' );
function wpeasycart_payfort_process( ){
	if( isset( $_GET['fort_id'] ) && get_option( 'ec_option_payment_third_party' ) == "payfort" ){
		$payfort = new ec_payfort( );
		if( $payfort->validate_response( ) ){
			$response_code = $_GET['status'];
			if( $response_code == "04" ){ // Capture Success
				$payfort->order_success( );
			}else if( $response_code == "06" ){ // Refund Success
				$payfort->refund_success( );
			}else if( $response_code == "14" ){ // Purchase Success
				$payfort->order_success( );
			}else if( $response_code == "13" ){ // Purchase Success
				$payfort->payment_failed( );
			}else{
				$payfort->payment_incomplete( );
			}	
		}
		
	}else if( isset( $_POST['fort_id'] ) && get_option( 'ec_option_payment_third_party' ) == "payfort" ){
		$payfort = new ec_payfort( );
		if( $payfort->validate_post_response( ) ){
			$response_code = $_POST['status'];
			if( $response_code == "04" ){ // Capture Success
				$payfort->order_success_post( );
			}else if( $response_code == "06" ){ // Refund Success
				$payfort->refund_success_post( );
			}else if( $response_code == "14" ){ // Purchase Success
				$payfort->order_success_post( );
			}else if( $response_code == "13" ){ // Purchase Success
				$payfort->payment_failed_post( );
			}else{
				$payfort->payment_incomplete_post( );
			}	
		}
	}
}

?>