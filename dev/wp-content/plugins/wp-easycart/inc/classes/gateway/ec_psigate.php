<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_psigate extends ec_gateway{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	
	function get_gateway_data( ){
		
		$psigate_store_id = get_option( 'ec_option_psigate_store_id' ); //teststore
		$psigate_passphrase = get_option( 'ec_option_psigate_passphrase' ); //psigate1234
			
		$tax_total = number_format( $this->order_totals->tax_total + $this->order_totals->duty_total + $this->order_totals->gst_total + $this->order_totals->pst_total + $this->order_totals->hst_total, 2, '.', '' );
		if( !$this->tax->vat_included )
			$tax_total = number_format( $tax_total + $this->order_totals->vat_total, 2, '.', '' );
		
		$psigate_xml = "<Order>".
							"<StoreID>".htmlentities( $psigate_store_id )."</StoreID>".
							"<Passphrase>".htmlentities( $psigate_passphrase )."</Passphrase>".
							"<TaxTotal>".htmlentities( $tax_total )."</TaxTotal>".
							"<ShippingTotal>".htmlentities( $this->order_totals->shipping_total )."</ShippingTotal>".
							"<Subtotal>".htmlentities( $this->order_totals->sub_total )."</Subtotal>".
							"<PaymentType>".htmlentities( "CC" )."</PaymentType>".
							"<CardAction>".htmlentities( "0" )."</CardAction>".
							"<CardNumber>".htmlentities( $this->credit_card->card_number )."</CardNumber>".
							"<CardExpMonth>".htmlentities( $this->credit_card->expiration_month )."</CardExpMonth>".
							"<CardExpYear>".htmlentities( $this->credit_card->get_expiration_year( 2 ) )."</CardExpYear>".
							"<CardIDCode>".htmlentities( "1" )."</CardIDCode>".
							"<CardIDNumber>".htmlentities( $this->credit_card->security_code )."</CardIDNumber>".
							"<TestResult>".htmlentities( $this->myTestResult )."</TestResult>".
							"<OrderID>".htmlentities( $this->order_id )."</OrderID>".
							"<UserID>".htmlentities( $this->user->user_id )."</UserID>".
							"<Bname>".htmlentities( $this->user->billing->first_name . " " . $this->user->billing->last_name )."</Bname>".
							"<Baddress1>".htmlentities( $this->user->billing-address_line_1 )."</Baddress1>".
							"<Bcity>".htmlentities( $this->user->billing->city )."</Bcity>".
							"<Bprovince>".htmlentities( $this->user->billing->state )."</Bprovince>".
							"<Bpostalcode>".htmlentities( $this->user->billing->zip )."</Bpostalcode>".
							"<Bcountry>".htmlentities( $this->user->billing->country )."</Bcountry>".
							"<Sname>".htmlentities( $this->user->shipping->first_name . " " . $this->user->shipping->last_name )."</Sname>".
							"<Saddress1>".htmlentities( $this->user->shipping->address_line_1 )."</Saddress1>".
							"<Scity>".htmlentities( $this->user->shipping->city )."</Scity>".
							"<Sprovince>".htmlentities( $this->user->shipping->state )."</Sprovince>".
							"<Spostalcode>".htmlentities( $this->user->shipping->zip )."</Spostalcode>".
							"<Scountry>".htmlentities( $this->user->shipping->country )."</Scountry>".
							"<Phone>".htmlentities( $this->user->billing->phone )."</Phone>".
							"<Email>".htmlentities( $this->user->email )."</Email>".
							"<CustomerIP>".htmlentities( $_SERVER['REMOTE_ADDR'] )."</CustomerIP>".
						"</Order>";
		
		
		return $psigate_xml;
		
	}
	
	function get_gateway_url( ){
		
		$psigate_test_mode = get_option( 'ec_option_psigate_test_mode' );
		
		if( $psigate_test_mode )
			return "https://dev.psigate.com:7989/Messenger/XMLMessenger";
		else
			return "https://psigate.com:7989/Messenger/XMLMessenger";

	}
	
	function get_gateway_headers( ){
		return array('Content-Type: application/json; charset=UTF-8;', 'Accept: application/json' );
	}
	
	function get_gateway_response( $gateway_url, $gateway_data, $gateway_headers ){
		
		$gateway_string = json_encode( $gateway_data );

		// Initializing curl
		$ch = curl_init( $gateway_url );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt( $ch, CURLOPT_PORT, 7989 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $gateway_string );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $gateway_headers );
		
		// Getting results
		$response = curl_exec( $ch );
		if( curl_errno( $ch ) ){
			$this->error_message = curl_error( $ch );
			curl_close( $ch );
			return false;
		}else{
			curl_close( $ch );
			return $response;
		}
	}
	
	function handle_gateway_response( $response ){
		
		$response_string = json_decode( $response );
		
		print_r( $response );
		
		if( $response_string && $response_string->bank_resp_code == '100' )
			$this->is_success = 1;
		else
			$this->is_success = 0;
		
		$response_text = print_r( $response_string, true );
		
		$this->mysqli->insert_response( $this->order_id, $this->temp_order_id, !$this->is_success, "Psigate", $response_text );
		
		if( !$this->is_success )
			$this->error_message = $response_string->bank_message;
			
	}
	
}

?>