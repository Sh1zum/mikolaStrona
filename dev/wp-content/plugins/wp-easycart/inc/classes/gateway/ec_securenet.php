<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_securenet extends ec_gateway{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	function process_credit_card( ){
		
		$gateway_url = $this->get_gateway_url( );
		$gateway_response = $this->get_gateway_response( $gateway_url );
		
		if( !$gateway_response ){
			error_log( "error in process_credit_card, could not get a response from the server." );
			return false;
		}else{
			if( $this->is_success )
				return true;
			else{
				return false;
			}
		}
	}
	
	function get_gateway_response( $gateway_url ){
		
		$api_id = get_option( 'ec_option_securenet_id' );
		$api_key = get_option( 'ec_option_securenet_secure_key' );
		$api_str = $api_id . ":" . $api_key;
		$api_base64_encoded = base64_encode( $api_str );
		
		if( $this->cart->weight > 0 ){
			$type_of_goods = "PHYSICAL";
		}else{
			$type_of_goods = "DIGITAL";
		}
		
		$address 	= array(	"line1"	=> $this->user->billing->address_line_1,
								"city"	=> $this->user->billing->city,
								"state"	=> $this->user->billing->state,
								"zip"	=> $this->user->billing->zip );
		
		$card 		= array(	"number" 			=> $this->credit_card->card_number,
								"cvv"				=> $this->credit_card->security_code,
								"expirationDate"	=> $this->credit_card->expiration_month . '/' . $this->credit_card->get_expiration_year( 4 ),
								"address"			=> $address );
						
		$gateway_data = array( 	"amount" 	=> $this->order_totals->grand_total,
							  	"card"		=> $card,
							  	"extendedInformation" 	=> array( 	"typeOfGoods"	=> $type_of_goods ),
							  	"developerApplication"	=> array( 	
																	"developerId"	=> 10000525,
																	"Version"		=> '1.0'
																)
							);
						
		
		$headr = array();
		$headr[] = 'Authorization: Basic ' . $api_base64_encoded;
		$headr[] = 'Content-Type: application/json';
		$headr[] = "Content-length: " . strlen( json_encode( $gateway_data ) );
		//$headr[] = json_encode( $gateway_data );
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		
		curl_setopt($ch, CURLOPT_POST, true ); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $gateway_data ) );
		
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		$response = curl_exec($ch);
		if( $response === false )
			$this->mysqli->insert_response( $this->order_id, 0, "SecureNet CURL ERROR", curl_error( $ch ) );
		curl_close ($ch);
		
		$this->handle_gateway_response( $response );
		
		if( $this->is_success ){
			return true;
		}else{
			return false;
		}
			
	}
	
	function get_gateway_url( ){
		
		if( get_option( 'ec_option_securenet_use_sandbox' ) )
			return "https://gwapi.demo.securenet.com/api/Payments/Charge";
		else
			return "https://gwapi.securenet.com/api/Payments/Charge";
			
	}
	
	function handle_gateway_response( $response_data ){
		
		$response = json_decode( $response_data ); 
		
		$status = $response->result;
		$failure_message = $response->message;
		
		if( $status == "APPROVED" )
			$this->is_success = 1;
		else
			$this->is_success = 0;
		
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "SecureNet", $response_data );
		
		if( !$this->is_success )
			$this->error_message = $failure_message;
			
	}
	
}
?>