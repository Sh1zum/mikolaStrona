<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_paypal_pro extends ec_gateway{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	
	function get_gateway_data( ){
		
		$paypal_pro_test_mode = get_option( 'ec_option_paypal_pro_test_mode' );
		
		if( $paypal_pro_test_mode )						$paypal_pro_hostaddress = "pilot-payflowpro.paypal.com";
		else											$paypal_pro_hostaddress = "payflowpro.paypal.com";
		
		$paypal_pro_port = 443;
		$paypal_pro_timeout = 90;
		
		$paypal_pro_vendor = trim( get_option( 'ec_option_paypal_pro_vendor' ) );
		$paypal_pro_partner = trim( get_option( 'ec_option_paypal_pro_partner' ) );
		$paypal_pro_currency = get_option( 'ec_option_paypal_pro_currency' );
		$paypal_pro_user = trim( get_option( 'ec_option_paypal_pro_user' ) );
		$paypal_pro_password = trim( get_option( 'ec_option_paypal_pro_password' ) );
			
		$tax_total = number_format( $this->order_totals->tax_total + $this->order_totals->gst_total + $this->order_totals->pst_total + $this->order_totals->hst_total, 2, '.', '' );
		
		$paypal_pro_data = array(	"HOSTADDRESS"		=> $paypal_pro_hostaddress,
									"HOSTPORT"			=> $paypal_pro_port,
									"TIMEOUT"			=> $paypal_pro_timeout,
									"VENDOR" 			=> $paypal_pro_vendor,
									"PARTNER" 			=> $paypal_pro_partner,
									"USER" 				=> $paypal_pro_user,
									"PWD" 				=> $paypal_pro_password,
									"BUTTONSOURCE"		=> "LevelFourDevelopmentLLC_Cart",
									"VERBOSITY" 		=> "HIGH",
									"TENDER" 			=> "C",
									"TRXTYPE" 			=> "S",
									"ACCT" 				=> $this->credit_card->card_number,
									"EXPDATE" 			=> $this->credit_card->expiration_month . $this->credit_card->get_expiration_year( 2 ),
									"CVV2" 				=> $this->credit_card->security_code,
									"CURRENCY" 			=> $paypal_pro_currency,
									"AMT" 				=> number_format( $this->order_totals->grand_total, 2, '.', '' ),
									"TAXAMT"			=> $tax_total,
									"FREIGHTAMT" 		=> $this->order_totals->shipping_total,
									"DUTYAMT"			=> $this->order_totals->duty_total,
									"DISCOUNT" 			=> $this->order_totals->discount_total,
									"ORDERID" 			=> $this->order_id,
									"BILLTOFIRSTNAME" 	=> $this->user->billing->first_name,
									"BILLTOLASTNAME" 	=> $this->user->billing->last_name,
									"BILLTOSTREET" 		=> $this->user->billing->address_line_1,
									"BILLTOSTREET2" 	=> $this->user->billing->address_line_2,
									"BILLTOCITY" 		=> $this->user->billing->city,
									"BILLTOSTATE" 		=> $this->user->billing->state,
									"BILLTOZIP"			=> $this->user->billing->zip,
									"BILLTOCOUNTRY" 	=> $this->user->billing->country,
									"SHIPTOFIRSTNAME" 	=> $this->user->shipping->first_name,
									"SHIPTOLASTNAME"	=> $this->user->shipping->last_name,
									"SHIPTOSTREET" 		=> $this->user->shipping->address_line_1,
									"SHIPTOSTREET2" 	=> $this->user->shipping->address_line_2,
									"SHIPTOCITY" 		=> $this->user->shipping->city,
									"SHIPTOSTATE" 		=> $this->user->shipping->state,
									"SHIPTOZIP" 		=> $this->user->shipping->zip,
									"SHIPTOCOUNTRY" 	=> $this->user->shipping->country
					 );
					 
		if( $this->tax->vat_enabled ){
			$paypal_pro_data["VATREGNUM"] 				= "VATREGNUM";
			$paypal_pro_data["VATTAXAMT"] 				= $this->order_totals->vat_total;
			$paypal_pro_data["VATTAXPERCENT"] 			= $this->tax->vat_rate;
		}
		
		//Create parmlist
		$paypal_pro_parmlist = "";
		$last_parm_i = 0;
		while( list( $key, $val ) = each( $paypal_pro_data ) ){
			if( $last_parm_i > 0 )
				$paypal_pro_parmlist .= "&";
			$paypal_pro_parmlist .= $key . "=" . htmlentities($val);
			$last_parm_i++;
		}
		
		//$paypal_pro_data["PARMLIST"] = $paypal_pro_parmlist;
		
		//Create Return Srring from Array
		$paypal_pro_string = "";
		$last_i = 0;
		foreach( $paypal_pro_data as $var=>$val ){
			if( $last_i > 0 )
				$paypal_pro_string .= "&";
			$paypal_pro_string .= $var . "=" . htmlentities($val);
			$last_i++;
		}
		
		return $paypal_pro_string;
		
	}
	
	function get_gateway_url( ){
		
		$paypal_pro_test_mode = get_option( 'ec_option_paypal_pro_test_mode' );
		
		if( $paypal_pro_test_mode )
			return "https://pilot-payflowpro.paypal.com";
		else
			return "https://payflowpro.paypal.com";
	
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
		
		$result_code = $response_vals["RESULT"];
		$response_message = $response_vals["RESPMSG"];
		
		$response_text = print_r( $response_vals, true );
		
		if( $result_code == 0 )
			$this->is_success = 1;
		else
			$this->is_success = 0;
			
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "PayPal Payflow Pro", $response_text );
			
		if( !$this->is_success )
			$this->error_message = $response_message;
			
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