<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_payline extends ec_gateway{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	
	function get_gateway_data( ){
		
		$username 						=  get_option( 'ec_option_payline_username' );
        $password						=  get_option( 'ec_option_payline_password' );
        $currency						=  get_option( 'ec_option_payline_currency' );
		
		$tax_total = number_format( $this->order_totals->tax_total + $this->order_totals->gst_total + $this->order_totals->pst_total + $this->order_totals->hst_total, 2, '.', '' );
		if( !$this->tax->vat_included )
			$tax_total = number_format( $tax_total + $this->order_totals->vat_total, 2, '.', '' );
		
		$data = array(
                 "type" 				=> 'sale',
				 "username"				=> $username,
				 "password"				=> $password,
				 "ccnumber" 			=> $this->credit_card->card_number,
				 "ccexp" 				=> $this->credit_card->expiration_month . $this->credit_card->get_expiration_year( 2 ),
                 "cvv" 					=> $this->credit_card->security_code,
                 "amount" 				=> number_format( $this->order_totals->grand_total, 2, '.', '' ),
				 "currency"				=> $currency,
				 "descriptor"			=> $this->credit_card->card_holder_name,
                 "orderid" 				=> $this->order_id,
				 "ipaddress"			=> $_SERVER['REMOTE_ADDR'],
				 "tax"					=> $tax_total,
				 "shipping"				=> $this->order_totals->shipping_total,
				 "first_name"			=> $this->user->billing->first_name,
				 "last_name"			=> $this->user->billing->last_name,
				 "company"				=> $this->user->billing->company_name,
				 "address1"				=> $this->user->billing->address_line_1,
				 "address2"				=> $this->user->billing->address_line_2,
				 "city"					=> $this->user->billing->city,
				 "state"				=> $this->user->billing->state,
				 "zip"					=> $this->user->billing->zip,
				 "country"				=> $this->user->billing->country,
				 "phone"				=> $this->user->billing->phone,
				 "email"				=> $this->user->email,
				 "shipping_firstname"	=> $this->user->shipping->first_name,
				 "shipping_lastname"	=> $this->user->shipping->last_name,
				 "shipping_company"		=> $this->user->shipping->company_name,
				 "shipping_address1"	=> $this->user->shipping->address_line_1,
				 "shipping_address2"	=> $this->user->shipping->address_line_2,
				 "shipping_city"		=> $this->user->shipping->city,
				 "shipping_state"		=> $this->user->shipping->state,
				 "shipping_zip"			=> $this->user->shipping->zip,
				 "shipping_country"		=> $this->user->shipping->country,
				 "customer_receipt"		=> "false"
        );
						   
		return $data;
		
	}
	
	function get_gateway_url( ){
		return "https://secure.paylinedatagateway.com/api/transact.php";
	}
	
	function get_gateway_response( $gateway_url, $gateway_data, $gateway_headers ){
		
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_VERBOSE, 1);
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt( $curl, CURLOPT_TIMEOUT, 90);
		curl_setopt( $curl, CURLOPT_URL, $gateway_url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $gateway_data ) );
    	curl_setopt( $curl, CURLOPT_HEADER, FALSE );
		
		$result = curl_exec($curl); 
		if( $result === false )
			$this->mysqli->insert_response( 0, 1, "Payline CURL ERROR", curl_error( $curl ) );
		curl_close($curl);
		
		return $result;
	}
	
	function handle_gateway_response( $response ){
		
		$response_arr = array( );
		parse_str( $response, $response_arr );
		
		if( $response_arr['response'] == "1" ){
			$this->mysqli->update_order_transaction_id( $this->order_id, $response_arr['transactionid'] );
			$this->is_success = 1;
		}else{
			$this->is_success = 0;
		}
		
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "Payline", print_r( $response_arr, true ) );
		
		if( !$this->is_success )
			$this->error_message = $response_arr['responsetext'];
			
	}
	
	public function refund_charge( $gateway_transaction_id, $refund_amount ){
		
		$username 						=  get_option( 'ec_option_payline_username' );
        $password						=  get_option( 'ec_option_payline_password' );
        $currency						=  get_option( 'ec_option_payline_currency' );
		
		$gateway_data = array(
			"type"			=> "refund",
			"username"		=> $username,
			"password"		=> $password,
			"transactionid"	=> $gateway_transaction_id,
			"amount"		=> number_format( $refund_amount, 2, ".", "" )
		);
		
		$gateway_url = $this->get_gateway_url( ); 
		
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_VERBOSE, 1);
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt( $curl, CURLOPT_TIMEOUT, 90);
		curl_setopt( $curl, CURLOPT_URL, $gateway_url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $gateway_data ) );
    	curl_setopt( $curl, CURLOPT_HEADER, FALSE );
		
		$response = curl_exec( $curl ); 
		if( $response === false )
			$this->mysqli->insert_response( 0, 1, "Payline Refund CURL ERROR", curl_error( $curl ) );
		curl_close( $curl );
		
		$response_arr = array( );
		parse_str( $response, $response_arr );
		
		$this->mysqli->insert_response( 0, $response_arr['response'], "Payline Refund", print_r( $response_arr, true ) );
		
		if( $response_arr['response'] == "1" ){
			return true;
		}else{
			return false;
		}
		
	}
	
}

?>