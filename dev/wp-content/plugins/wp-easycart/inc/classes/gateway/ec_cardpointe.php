<?php

class ec_cardpointe extends ec_gateway{
	
	function get_gateway_data( ){
		
		$merchant_id = get_option( 'ec_option_cardpointe_merch' );
		$currency_code = get_option( 'ec_option_cardpointe_currency' );
		$shipfromzip = get_option( 'ec_option_cardpointe_shipfromzip' );
		
		$tax_total = ( $this->order_totals->tax_total + $this->order_totals->vat_total + $this->order_totals->gst_total + $this->order_totals->hst_total + $this->order_totals->pst_total );
		
		$register_data = array( 
			'merchid'				=>	$merchant_id,
			'amount'				=>	number_format( $this->order_totals->grand_total, 2, ".", "" ),
			'account'				=>	$_POST['cardpointe_token'],
			'currency'				=>	$currency_code,
			'capture'				=>  'Y',
			'tokenize'				=>  'Y',
			'name'					=>	$this->user->billing->first_name . ' ' . $this->user->billing->last_name,
			'address'				=>	$this->user->billing->address_line_1,
			'address2'				=>	$this->user->billing->address_line_2,
			'city'					=>	$this->user->billing->city,
			'postal'				=>	$this->user->billing->zip,
			'region'				=>	$this->user->billing->state,
			'country'				=>	$this->user->billing->country,
			'phone'					=>	$this->user->billing->phone,
			'email'					=> 	$this->user->email,
			'orderid' 				=>	$this->order_id,
			'taxexempt' 			=>	( $tax_total <= 0 ) ? 'Y' : 'N',
			'ecomind'				=>	"E",
			'taxamnt'				=>  number_format( $tax_total, 2, ".", "" ),
			'frtamnt'				=>  number_format( $this->order_totals->shipping_total, 2, ".", "" ),
			'dutyamnt'				=>  number_format( $this->order_totals->duty_total, 2, ".", "" ),
			'shiptozip'				=>	$this->user->shipping->zip,
			'shipfromzip'			=>	$shipfromzip,
			'shiptocountry'			=>	$this->user->shipping->country
		);
		
		return json_encode( $register_data );
		
	}
	
	function get_gateway_url( ){
		
		$merchant_id = get_option( 'ec_option_cardpointe_merch' );
		$site = get_option( 'ec_option_cardpointe_site' );
		return 'https://' . $site . '.cardconnect.com/cardconnect/rest/auth?merchid=' . $merchant_id;
		
	}
	
	function get_gateway_response( $gateway_url, $gateway_data, $gateway_headers ){
		
		$site = get_option( 'ec_option_cardpointe_site' );
		$merchant_id = get_option( 'ec_option_cardpointe_merch' );
		$username = get_option( 'ec_option_cardpointe_username' );
		$password = get_option( 'ec_option_cardpointe_password' );
		
		$headr = array();
		$headr[] = 'Content-Type: application/json';
		$headr[] = 'Authorization: Basic ' . base64_encode( $username . ':' . $password );
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt($ch, CURLOPT_POST, true ); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $gateway_data );
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		if( $response === false )
			$this->mysqli->insert_response( 0, 1, "Card Pointe CURL ERROR", 'error url: ' . $gateway_url . ', ' . curl_error( $ch ) );
		else
			$this->mysqli->insert_response( 0, 0, "Card Pointe Charge Response", 'gateway url: ' . $gateway_url . ', ' . print_r( $response, true ) );
		
		curl_close ($ch);
		
		return json_decode( $response );
		
	}
	
	function handle_gateway_response( $response ){
		
		$response_code = $response->respcode;
		
		if( $response_code == '00' || $response_code == '000' ){
			$this->mysqli->update_order_transaction_id( $this->order_id, $response->retref );
			$this->is_success = true;
		
		}else{
			$this->is_success = false;
		}	
	}
	
	public function refund_charge( $gateway_transaction_id, $refund_amount ){
		global $wpdb;
		$order = $wpdb->get_row( $wpdb->prepare( "SELECT ec_order.creditcard_digits, ec_order.order_id, ec_order.grand_total FROM ec_order WHERE ec_order.gateway_transaction_id = %s", $gateway_transaction_id ) );
		
		$site = get_option( 'ec_option_cardpointe_site' );
		$merchant_id = get_option( 'ec_option_cardpointe_merch' );
		$username = get_option( 'ec_option_cardpointe_username' );
		$password = get_option( 'ec_option_cardpointe_password' );
		
		$gateway_url = 'https://' . $site . '.cardconnect.com/cardconnect/rest/refund?merchid=' . $merchant_id;
		
		$register_data = array( 
			'retref'				=>  $gateway_transaction_id,
			'merchid'				=>	$merchant_id,
			'amount'				=>	number_format( $refund_amount, 2, ".", "" )
		);
		
		$gateway_data = json_encode( $register_data );
		
		$headr = array();
		$headr[] = 'Content-Type: application/json';
		$headr[] = 'Authorization: Basic ' . base64_encode( $username . ':' . $password );
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt($ch, CURLOPT_POST, true ); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $gateway_data );
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		if( $response === false )
			$this->mysqli->insert_response( 0, 1, "Card Pointe Refund CURL ERROR", 'error url: ' . $gateway_url . ', ' . curl_error( $ch ) );
		else
			$this->mysqli->insert_response( 0, 0, "Card Pointe Refund Response", 'gateway url: ' . $gateway_url . ', ' . print_r( $response, true ) );
		
		curl_close ($ch);
		
		$response_json = json_decode( $response );
		
		if( $response_json->respcode == "00" || $response_json->respcode == "000" ){
			return true;
			
		}else if( $response_json->respcode == "28" ){
			return $this->void_charge( $gateway_transaction_id, $refund_amount );
		
		}else{
			return false;
		}
		
	}
	
	public function void_charge( $gateway_transaction_id, $refund_amount ){
		global $wpdb;
		$order = $wpdb->get_row( $wpdb->prepare( "SELECT ec_order.creditcard_digits, ec_order.order_id, ec_order.grand_total FROM ec_order WHERE ec_order.gateway_transaction_id = %s", $gateway_transaction_id ) );
		
		$site = get_option( 'ec_option_cardpointe_site' );
		$merchant_id = get_option( 'ec_option_cardpointe_merch' );
		$username = get_option( 'ec_option_cardpointe_username' );
		$password = get_option( 'ec_option_cardpointe_password' );
		
		$gateway_url = 'https://' . $site . '.cardconnect.com/cardconnect/rest/void?merchid=' . $merchant_id;
		
		$register_data = array( 
			'retref'				=>  $gateway_transaction_id,
			'merchid'				=>	$merchant_id,
			'amount'				=>	number_format( $refund_amount, 2, ".", "" )
		);
		
		$gateway_data = json_encode( $register_data );
		
		$headr = array();
		$headr[] = 'Content-Type: application/json';
		$headr[] = 'Authorization: Basic ' . base64_encode( $username . ':' . $password );
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt($ch, CURLOPT_POST, true ); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $gateway_data );
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		if( $response === false )
			$this->mysqli->insert_response( 0, 1, "Card Pointe VOID CURL ERROR", 'error url: ' . $gateway_url . ', ' . curl_error( $ch ) );
		else
			$this->mysqli->insert_response( 0, 0, "Card Pointe VOID Response", 'gateway url: ' . $gateway_url . ', ' . print_r( $response, true ) );
		
		curl_close ($ch);
		
		$response_json = json_decode( $response );
		
		if( $response_json->respcode == "00" || $response_json->respcode == "000" ){
			return true;
			
		}else{
			return false;
		}
		
	}

}

?>