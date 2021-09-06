<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_paypal_payments_pro extends ec_gateway{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	
	function get_gateway_data( ){
		
		$paypal_payments_pro_version = "56.0";
		
		$paypal_payments_pro_currency = get_option( 'ec_option_paypal_payments_pro_currency' );
		$paypal_payments_pro_user = get_option( 'ec_option_paypal_payments_pro_user' );
		$paypal_payments_pro_password = get_option( 'ec_option_paypal_payments_pro_password' );
		$paypal_payments_pro_signature = get_option( 'ec_option_paypal_payments_pro_signature' );
		
		if( $this->credit_card->payment_method == "visa" )
			$card_type = "Visa";
		else if( $this->credit_card->payment_method == "mastercard" )
			$card_type = "MasterCard";
		else if( $this->credit_card->payment_method == "maestro" )
			$card_type = "Maestro";
		else if( $this->credit_card->payment_method == "switch" )
			$card_type = "Maestro";
		else if( $this->credit_card->payment_method == "uke" )
			$card_type = "Visa";
		else if( $this->credit_card->payment_method == "amex" )
			$card_type = "Amex";
		else if( $this->credit_card->payment_method == "discover" )
			$card_type = "Discover";
			
		$tax_total = number_format( $this->order_totals->tax_total + $this->order_totals->duty_total + $this->order_totals->gst_total + $this->order_totals->pst_total + $this->order_totals->hst_total, 2, '.', '' );
		if( !$this->tax->vat_included )
			$tax_total = number_format( $tax_total + $this->order_totals->vat_total, 2, '.', '' );
		
		$paypal_payments_pro_data = array(	"METHOD"			=> "DoDirectPayment",
											"VERSION"			=> $paypal_payments_pro_version,
											"SIGNATURE"			=> $paypal_payments_pro_signature,
											"USER"				=> $paypal_payments_pro_user,
											"PWD"				=> $paypal_payments_pro_password,
											"PAYMENTACTION"		=> "Sale",
											"IPADDRESS"			=> $_SERVER['REMOTE_ADDR'],
											"BUTTONSOURCE"		=> "LevelFourDevelopmentLLC_Cart",
		
											"AMT" 				=> number_format( $this->order_totals->grand_total, 2, '.', '' ),
											"ITEMAMT"			=> number_format( $this->order_totals->sub_total, 2, '.', '' ),
											"SHIPPINGAMT"		=> number_format( $this->order_totals->shipping_total, 2, '.', '' ),
											"TAXAMT"			=> $tax_total,
											"SHIPDISCAMT"		=> "-" . number_format( $this->order_totals->discount_total, 2, '.', '' ),
											"CURRENCYCODE"		=> $paypal_payments_pro_currency,
											
											//"INVNUM"			=> $this->order_id,
											"PAYMENTACTION"		=> "Sale",
											"CREDITCARDTYPE"	=> $card_type,
											"ACCT" 				=> $this->credit_card->card_number,
											"EXPDATE" 			=> $this->credit_card->expiration_month . $this->credit_card->get_expiration_year( 4 ),
											"CVV2" 				=> $this->credit_card->security_code,

											"EMAIL"				=> $this->user->email,
											
											"FIRSTNAME" 		=> $this->user->billing->first_name,
											"LASTNAME" 			=> $this->user->billing->last_name,
											"STREET" 			=> $this->user->billing->address_line_1,
											"STREET2" 			=> $this->user->billing->address_line_2,
											"CITY" 				=> $this->user->billing->city,
											"STATE" 			=> $this->user->billing->state,
											"ZIP"				=> $this->user->billing->zip,
											"COUNTRYCODE" 		=> $this->user->billing->country,
											
											"SHIPTONAME" 		=> $this->user->shipping->first_name . ' ' . $this->user->shipping->last_name,
											"SHIPTOSTREET" 		=> $this->user->shipping->address_line_1,
											"SHIPTOSTREET2" 	=> $this->user->shipping->address_line_2,
											"SHIPTOCITY" 		=> $this->user->shipping->city,
											"SHIPTOSTATE" 		=> $this->user->shipping->state,
											"SHIPTOZIP" 		=> $this->user->shipping->zip,
											"SHIPTOCOUNTRY" 	=> $this->user->shipping->country 
											
							 );
		/* NO Longer Allowed
		//Add Cart Items
		for( $i=0; $i<count($this->cart->cart); $i++ ){
			$paypal_payments_pro_data["L_NAME".$i] = substr( $this->cart->cart[$i]->title, 0, 127 );
			//$paypal_payments_pro_data["L_DESC".$i] = substr( $this->cart->cart[$i]->description, 0, 127 );
			$paypal_payments_pro_data["L_AMT".$i] = number_format( $this->cart->cart[$i]->unit_price, 2, '.', '' );
			$paypal_payments_pro_data["L_NUMBER".$i] = substr( $this->cart->cart[$i]->model_number, 0, 127 );
			$paypal_payments_pro_data["L_QTY".$i] = $this->cart->cart[$i]->quantity;
			//$paypal_payments_pro_data["L_TAXAMT".$i] = substr( $this->cart->cart[$i]->title, 0, 127 );
		}
		
		//Add Shipping
		$paypal_payments_pro_data["SHIPTONAME"] = substr( $this->user->shipping->first_name . " " . $this->user->shipping->last_name, 0, 32 );
		$paypal_payments_pro_data["SHIPTOSTREET"] = substr( $this->user->shipping->address_line_1, 0, 100 );
		$paypal_payments_pro_data["SHIPTOCITY"] = substr( $this->user->shipping->city, 0, 40 );
		$paypal_payments_pro_data["SHIPTOSTATE"] = substr( $this->user->shipping->state, 0, 40 );
		$paypal_payments_pro_data["SHIPTOZIP"] = substr( $this->user->shipping->zip, 0, 20 );
		$paypal_payments_pro_data["SHIPTOCOUNTRY"] = substr( $this->user->shipping->country, 0, 2 );
		$paypal_payments_pro_data["SHIPTOPHONENUM"] = substr( $this->user->shipping->phone, 0, 20 );
		*/
		
		//Create parmlist
		$paypal_payments_pro_string = "";
		$last_i = 0;
		foreach( $paypal_payments_pro_data as $var=>$val ){
			if( $last_i > 0 )
				$paypal_payments_pro_string .= "&";
			$paypal_payments_pro_string .= $var . "=" . urlencode($val);
			$last_i++;
		}
		
		return $paypal_payments_pro_string;
		
	}
	
	function get_gateway_url( ){
		
		$paypal_payments_pro_test_mode = get_option( 'ec_option_paypal_payments_pro_test_mode' );
		
		if( $paypal_payments_pro_test_mode )
			return "https://api-3t.sandbox.paypal.com/nvp";
		else
			return "https://api-3t.paypal.com/nvp";
	
	}
	
	
	
	function handle_gateway_response( $response ){
		
		$response_body = $response;
		
		//Format response data in form key=val&key2=val2&...
		$response_array = explode( "&", $response_body );
		$response_vals = array();
		for( $i=0; $i<count($response_array); $i++){
			$split = explode( "=", $response_array[$i] );
			$response_vals[$split[0]] = $split[1]; 
		}
		
		$result_code = $response_vals["ACK"];
		
		$response_text = print_r( $response_vals, true );
		
		if( $result_code == "Success" || $result_code == "SuccessWithWarning" )
			$this->is_success = 1;
		else
			$this->is_success = 0;
			
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "PayPal Payments Pro", $response_text );
			
		if( !$this->is_success && isset( $response_vals["L_SHORTMESSAGE"] ) )
			$this->error_message = $response_vals["L_SHORTMESSAGE"];
			
	}
	
	function get_gateway_response( $gateway_url, $gateway_data, $gateway_headers ){
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_VERBOSE, 1);
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt( $curl, CURLOPT_TIMEOUT, 90);
		curl_setopt( $curl, CURLOPT_URL, $gateway_url);
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $gateway_data );
		
		$result = curl_exec($curl);      
		curl_close($curl);
		
		return $result;
	}
	
}

?>