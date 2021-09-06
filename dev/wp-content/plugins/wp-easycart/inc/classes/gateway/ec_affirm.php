<?php

class ec_affirm extends ec_gateway{
	
	function process_credit_card( ){
		
		$charge_object = $this->get_charge_object( );
		$capture_charge_result = $this->capture_charge( $charge_object );
		$this->handle_gateway_response( $capture_charge_result );
		
		if( $this->is_success )
			return true;
		else{
			return false;
		}
	
	}
	
	function get_gateway_data( ){
		
		$token = $_POST['checkout_token'];
		$token_string = '{"checkout_token":"' . $token . '"}';
		
		return $token_string;
		
	}
	
	function get_gateway_url( ){
		
		$is_sandbox_account = get_option( 'ec_option_affirm_sandbox_account' );
		
		if( $is_sandbox_account )							
			return "https://sandbox.affirm.com/api/v2/charges/";
		else														
			return "https://api.affirm.com/api/v2/charges/";
	}
	
	
	
	function get_charge_object( ){
		
		// Get URL
		$gateway_url = $this->get_gateway_url( );
		
		// Get Headers
		$headr = array();
		$headr[] = 'Content-Type: application/json';
		
		// Setup and run CURL
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_USERPWD, get_option( 'ec_option_affirm_public_key' ) . ':' . get_option( 'ec_option_affirm_private_key' ) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST" ); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->get_gateway_data( ) );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		curl_close ($ch);
		
		$this->mysqli->insert_response( $this->order_id, 0, "Affirm 1", print_r( json_decode( $response ), true ) );
		return json_decode( $response );
			
	}
	
	function capture_charge( $charge_obj ){
		
		// Get URL
		$gateway_url = $this->get_gateway_url( ) . $charge_obj->id . '/capture';
		
		// Get Headers
		$headr = array();
		$headr[] = 'Content-Type: application/json';
		
		// Setup and run CURL
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_USERPWD, get_option( 'ec_option_affirm_public_key' ) . ':' . get_option( 'ec_option_affirm_private_key' ) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt($ch, CURLOPT_POSTFIELDS, "{}" );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		curl_close ($ch);
		
		$response_json = json_decode( $response );
		if( $response_json->type == "capture" ){
			$this->add_charge_id( $charge_obj->id );
		}
		
		return $response_json;
		
	}
	
	
	function handle_gateway_response( $response ){
		
		if( $response->type == "capture" )
			$this->is_success = 1;
		else
			$this->is_success = 0;
		
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "Affirm 2", print_r( $response, true ) );
		
		return;
			
	}
	
	function add_charge_id( $charge_id ){
		
		$this->mysqli->update_affirm_id( $this->order_id, $charge_id );
		
	}
	
	function refund_order( $order_id, $charge_id, $refund_amount ){
		
		// Get URL
		$gateway_url = $this->get_gateway_url( ) . $charge_id . '/refund';
		
		// Get Headers
		$headr = array();
		$headr[] = 'Content-Type: application/json';
		
		// Setup and run CURL
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_USERPWD, get_option( 'ec_option_affirm_public_key' ) . ':' . get_option( 'ec_option_affirm_private_key' ) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt($ch, CURLOPT_POSTFIELDS, '{"amount": ' . number_format( $refund_amount * 100, 0, '', '' ) . '}' );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		curl_close ($ch);
		
		$response_json = json_decode( $response );
		$this->mysqli->insert_response( $order_id, 1, "Affirm Refund", print_r( $response, true ) );
		
		if( $response_json->type == "refund" ){
			return true;
		}else{
			return false;
		}
		
	}
	
}

?>