<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_chronopay extends ec_gateway{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	
	function get_gateway_data( ){
		
		//set the variables for chronopay
		$chronopay_currency = get_option( 'ec_option_chronopay_currency' );
		$chronopay_product_id = get_option( 'ec_option_chronopay_product_id' );
		$chronopay_shared_secret = get_option( 'ec_option_chronopay_shared_secret' );
		
		//set the hash for chronopay
		$hash_var = $chronopay_shared_secret . "1" . $chronopay_product_id . $this->user->billing->first_name . $this->user->billing->last_name . $this->user->billing->address_line_1 . $_SERVER['REMOTE_ADDR'] . $this->credit_card->card_number . $this->order_totals->grand_total;
		$new_hash_var = md5( $hash_var );
		
		$chronopay_values = array(	"opcode"				=> "1",
									"product_id"			=> $chronopay_product_id,
									"fname"					=> $this->user->billing->first_name,
									"lname"					=> $this->user->billing->last_name,
									"street"				=> $this->user->billing->address_line_1,
									"city"					=> $this->user->billing->city,
									"country"				=> $this->user->billing->country,
									"email"					=> $this->user->email,
									"zip"					=> $this->user->billing->zip,
									"phone"					=> $this->user->billing->phone,
									"ip"					=> $_SERVER['REMOTE_ADDR'],
									"card_no"				=> $this->credit_card->card_number,
									"cvv"					=> $this->credit_card->security_code,
									"expirey"				=> $this->credit_card->expiration_year,
									"expirem"				=> $this->credit_card->expiration_month,
									"amount"				=> $this->order_totals->grand_total,
									"currency"				=> $chronopay_currency,
									"hash"					=> $new_hash_var
								);
		
		
		return $chronopay_values;
	}
	
	function get_gateway_url( ){
		return "https://secure.chronopay.com/gateway.cgi";
	}
	
	function handle_gateway_response( $response ){
		
		$response_body = $response["body"];
		
		if( substr( $response_body, 0, 1 ) == 'Y' )
			$this->is_success = true;
		else
			$this->is_success = false;
		
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "Chronopay", $response_body );
		
		if( !$this->is_success )
			$this->error_message = $this->response_body;
			
	}
	
}

?>