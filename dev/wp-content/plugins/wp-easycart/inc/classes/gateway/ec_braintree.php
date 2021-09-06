<?php
class ec_braintree extends ec_gateway{
	
	function get_client_token( ){
		
		$headr = array( );
		$headr[] = 'User-Agent: WP EasyCart';
        $headr[] = 'X-ApiVersion: 4';
		$headr[] = 'Accept: application/json';
        $headr[] = 'Content-Type: application/json';
		
		if( get_option( 'ec_option_braintree_environment' ) == "sandbox" ){
			$url = 'https://api.sandbox.braintreegateway.com/merchants/' . get_option( 'ec_option_braintree_merchant_id' ) . '/client_token';
		}else{
			$url = 'https://api.braintreegateway.com/merchants/' . get_option( 'ec_option_braintree_merchant_id' ) . '/client_token';
		}
		
		$data = array(
			"client_token"	=> array(
				"version" => 2
			)
		);
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
		curl_setopt($ch, CURLOPT_USERPWD, get_option( 'ec_option_braintree_public_key' ) . ':' . get_option( 'ec_option_braintree_private_key' ) );
		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt($ch, CURLOPT_POST, true ); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $data ) );
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		if( $response === false ){
			$this->mysqli->insert_response( 0, 1, "BRAINTREE CURL ERROR", curl_error( $ch ) );
			$response = (object) array( "error" => curl_error( $ch ) );
		}else
			$this->mysqli->insert_response( 0, 0, "Braintree Client Token Response", print_r( $response, true ) );
		curl_close ($ch);
		
        $json = json_decode( $response );
        return $json->clientToken->value;
	}
	
	function get_gateway_data( ){
		
        return array(
			'transaction'    => array(
			  'type' => 'sale',
			  'amount' => number_format( $this->order_totals->grand_total, 2, ".", "" ),
			  'channel' => 'LevelFourDevelopment_SP_BT',
			  'orderId' => (int) $this->order_id,
			  'paymentMethodNonce' => $_POST['braintree_nonce'],
			  'customer' => array(
				'firstName' => $this->user->billing->first_name,
				'lastName' => $this->user->billing->last_name,
				'phone' => $this->user->billing->phone,
				'email' => $this->user->email
              ),
			  'billing' => array(
				'firstName' => $this->user->billing->first_name,
				'lastName' => $this->user->billing->last_name,
				'streetAddress' => $this->user->billing->address_line_1,
				'locality' => $this->user->billing->city,
				'region' => $this->user->billing->state,
				'postalCode' => $this->user->billing->zip,
				'countryCodeAlpha2' => $this->user->billing->country
              ),
			  'shipping' => array(
				'firstName' => $this->user->shipping->first_name,
				'lastName' => $this->user->shipping->last_name,
				'streetAddress' => $this->user->shipping->address_line_1,
				'locality' => $this->user->shipping->city,
				'region' => $this->user->shipping->state,
				'postalCode' => $this->user->shipping->zip,
				'countryCodeAlpha2' => $this->user->shipping->country
			  ),
              'options' => array(
				'submitForSettlement' => true
              )
            )
        );
	}
	
	function get_gateway_url( ){
		
		if( get_option( 'ec_option_braintree_environment' ) == "sandbox" ){
			return 'https://api.sandbox.braintreegateway.com/merchants/' . get_option( 'ec_option_braintree_merchant_id' ) . '/transactions';
		}else{
			return 'https://api.braintreegateway.com/merchants/' . get_option( 'ec_option_braintree_merchant_id' ) . '/transactions';
		}

	}
	
	protected function get_gateway_response( $gateway_url, $gateway_data, $gateway_headers ){
		
		$headr = array( );
		$headr[] = 'User-Agent: WP EasyCart';
        $headr[] = 'X-ApiVersion: 6';
        $headr[] = 'Content-Type: application/json';
		
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
		curl_setopt( $ch, CURLOPT_USERPWD, get_option( 'ec_option_braintree_public_key' ) . ':' . get_option( 'ec_option_braintree_private_key' ) );
		curl_setopt( $ch, CURLOPT_URL, $gateway_url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $ch, CURLOPT_POST, true ); 
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $gateway_data ) );// $gateway_data );
		curl_setopt( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
		curl_setopt( $ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		
		if( $response === false ){
			$this->mysqli->insert_response( $this->order_id, 1, "BRAINTREE CURL ERROR", curl_error( $ch ) );
			$response = (object) array( "errors" => curl_error( $ch ) );
		}else{
			$this->mysqli->insert_response( $this->order_id, 0, "Braintree Response", print_r( $response, true ) );
        }
		curl_close ($ch);
        
		$xml = new SimpleXMLElement( $response );
		return $xml;
	}
	
	function handle_gateway_response( $result ){

        if( isset( $result->errors ) ){
			$this->is_success = 0;
			$this->error_message = ( isset( $result->errors->transaction ) ) ? (string) $result->errors->transaction->errors[0]->error->message : (string) $result->errors;
			
		}else{
			$this->mysqli->update_order_transaction_id( (int) $this->order_id, (string) $result->id );
			$this->is_success = 1;
            
		}
			
	}
	
	public function refund_charge( $gateway_transaction_id, $refund_amount ){
		
		if( get_option( 'ec_option_braintree_environment' ) == "sandbox" ){
			$url = 'https://api.sandbox.braintreegateway.com/merchants/' . get_option( 'ec_option_braintree_merchant_id' ) . '/transactions/' . $gateway_transaction_id . '/refund';
		}else{
			$url = 'https://api.braintreegateway.com/merchants/' . get_option( 'ec_option_braintree_merchant_id' ) . '/transactions/' . $gateway_transaction_id . '/refund';
		}
		
		$headr = array( );
		$headr[] = 'User-Agent: WP EasyCart';
        $headr[] = 'X-ApiVersion: 6';
        $headr[] = 'Content-Type: application/json';
		
		$braintree_arr = array(
            'transaction'   => array(
                'amount'        => number_format( $refund_amount, 2, ".", "" )
            )
        );
		
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
		curl_setopt( $ch, CURLOPT_USERPWD, get_option( 'ec_option_braintree_public_key' ) . ':' . get_option( 'ec_option_braintree_private_key' ) );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $ch, CURLOPT_POST, true ); 
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $braintree_arr ) );
		curl_setopt( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
		curl_setopt( $ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		
		if( $response === false ){
			$this->mysqli->insert_response( 0, 1, "BRAINTREE CURL ERROR", curl_error( $ch ) );
			$response = (object) array( "errors" => curl_error( $ch ) );
		}else{
			$response = new SimpleXMLElement( $response );
			$this->mysqli->insert_response( 0, 0, "Braintree Refund Response", print_r( $response, true ) );
		}
		curl_close ($ch);
		
		if( isset( $response->errors ) && isset( $response->errors->transaction ) && isset( $response->errors->transaction->errors ) && isset( $response->errors->transaction->errors[0] ) && isset( $response->errors->transaction->errors[0]->error ) && isset( $response->errors->transaction->errors[0]->error->code ) && $response->errors->transaction->errors[0]->error->code == '91506' ){
			global $wpdb;
			$order = $wpdb->get_row( $wpdb->prepare( "SELECT ec_order.grand_total FROM ec_order WHERE ec_order.gateway_transaction_id = %s", $gateway_transaction_id ) );
			
			if( $refund_amount == $order->grand_total ){ // probably new charge, try voiding
				return $this->void_charge( $gateway_transaction_id );
				
			}else{
				return false;
			}
			
		}else if( isset( $response->errors ) ){
			return false;
			
		}else{
			return true;
			
		}
		
	}
	
	public function void_charge( $gateway_transaction_id ){
		
		if( get_option( 'ec_option_braintree_environment' ) == "sandbox" ){
			$url = 'https://api.sandbox.braintreegateway.com/merchants/' . get_option( 'ec_option_braintree_merchant_id' ) . '/transactions/' . $gateway_transaction_id . '/void';
		}else{
			$url = 'https://api.braintreegateway.com/merchants/merchants/' . get_option( 'ec_option_braintree_merchant_id' ) . '/transactions/' . $gateway_transaction_id . '/void';
		}
		
		$headr = array( );
		$headr[] = 'User-Agent: WP EasyCart';
        $headr[] = 'X-ApiVersion: 6';
        $headr[] = 'Content-Type: application/json';
		
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
		curl_setopt( $ch, CURLOPT_USERPWD, get_option( 'ec_option_braintree_public_key' ) . ':' . get_option( 'ec_option_braintree_private_key' ) );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
		curl_setopt( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
		curl_setopt( $ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		
		if( $response === false ){
			$this->mysqli->insert_response( 0, 1, "BRAINTREE CURL ERROR", curl_error( $ch ) );
			$response = (object) array( "errors" => curl_error( $ch ) );
		}else{
			$this->mysqli->insert_response( 0, 0, "Braintree Void Response", print_r( $response, true ) );
			$response = new SimpleXMLElement( $response );
		}
		curl_close ($ch);
		
		if( isset( $response->errors ) ){
			return false;
			
		}else{
			return true;
		}
		
	}
	
}

?>