<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_migs extends ec_gateway{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	
	function get_gateway_data( ){
		
		$SECURE_SECRET =  get_option( 'ec_option_migs_signature' );
        $accessCode    =  get_option( 'ec_option_migs_access_code' );
        $merchantId    =  get_option( 'ec_option_migs_merchant_id' );
		
		$paymentdata = array(
                 "vpc_AccessCode" => $accessCode,
                 "vpc_Amount" => ( $this->order_totals->grand_total * 100 ),
                 "vpc_CardSecurityCode" => $this->credit_card->security_code,
				 "vpc_Command" => 'pay',
				 "vpc_CardNum" => $this->credit_card->card_number,
				 "vpc_CardExp" => $this->credit_card->get_expiration_year( 2 ) . $this->credit_card->expiration_month,
                 "vpc_Locale" => "en",
                 "vpc_MerchTxnRef" => $this->order_id . "-" . $GLOBALS['ec_cart_data']->ec_cart_id,
                 "vpc_Merchant" => $merchantId,
                 "vpc_OrderInfo" => $this->order_id,
                 "vpc_ReturnURL" => "",
                 "vpc_Version" => '1',
				 "vpc_SecureHash" => strtoupper( md5( $SECURE_SECRET ) )
        );
						   
		return $paymentdata;
		
	}
	
	function get_gateway_url( ){
		return "https://migs.mastercard.com.au/vpcdps";
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
			$this->mysqli->insert_response( 0, 1, "MIGS CURL ERROR", curl_error( $curl ) );
		curl_close($curl);
		
		return $result;
	}
	
	function handle_gateway_response( $response ){
		
		$response_arr = array( );
		parse_str ( $response, $response_arr );
		
		if( $response_arr['vpc_AcqResponseCode'] == "00" )
			$this->is_success = 1;
		else
			$this->is_success = 0;
		
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "MIGS", print_r( $response_arr, true ) );
		
		if( !$this->is_success )
			$this->error_message = $response_arr['vpc_Message'];
			
	}
	
}

?>