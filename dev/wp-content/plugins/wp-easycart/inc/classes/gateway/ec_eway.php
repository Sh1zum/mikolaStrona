<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_eway extends ec_gateway{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	
	function get_gateway_data( ){
		
		$eway_customer_id = get_option( 'ec_option_eway_customer_id' );
		$eway_test_mode = get_option( 'ec_option_eway_test_mode' );
		$eway_test_mode_success = get_option( 'ec_option_eway_test_mode_success' );
		$eway_use_rapid_pay = get_option( 'ec_option_eway_use_rapid_pay' );
		
		if( $eway_use_rapid_pay ){ // User on RapidAPI
		
			$eway_array = array(	
				"CustomerIP" => $_SERVER['REMOTE_ADDR'],
				"Method" => "ProcessPayment",
				"TransactionType" => "Purchase",
				"Customer"	 => array( 
					"CardDetails" => array(
						"Name"			=> $this->credit_card->card_holder_name,
						"Number"		=> $_POST['ec_card_number'],
						"ExpiryMonth"	=> $this->credit_card->expiration_month,
						"ExpiryYear"	=> $this->credit_card->get_expiration_year( 2 ),
						"CVN"			=> $this->credit_card->security_code
					)
				),
				"Payment" => array(
					"TotalAmount" 	=> $this->order_totals->get_grand_total_in_cents( ),
					"InvoiceNumber"	=> $this->order_id
				)
			);
			return json_encode( $eway_array );
			
		}else{ // User on Direct Payment
		
			if( $eway_test_mode ){
				$this->credit_card->card_number = "4444333322221111";
				$eway_customer_id = "87654321";
				if( $eway_test_mode_success )
					$this->order_totals->grand_total = 10.00;	
				else
					$this->order_totals->grand_total = 10.67;
			}
			
			$eway_xml = "<ewaygateway>
							<ewayCustomerID>" . $eway_customer_id . "</ewayCustomerID> 
							<ewayTotalAmount>" . $this->order_totals->get_grand_total_in_cents( ) . "</ewayTotalAmount> 
							<ewayCustomerFirstName>" . $this->user->billing->first_name . "</ewayCustomerFirstName> 
							<ewayCustomerLastName>" . $this->user->billing->last_name . "</ewayCustomerLastName> 
							<ewayCustomerEmail>" . $this->user->email . "</ewayCustomerEmail> 
							<ewayCustomerAddress>" . $this->user->billing->address_line_1 . "</ewayCustomerAddress> 
							<ewayCustomerPostcode>" . $this->user->billing->zip . "</ewayCustomerPostcode>
							<ewayCustomerInvoiceDescription></ewayCustomerInvoiceDescription>
							<ewayCustomerInvoiceRef>" . $this->order_id . "</ewayCustomerInvoiceRef> 
							<ewayCardHoldersName>" . $this->credit_card->card_holder_name . "</ewayCardHoldersName> 
							<ewayCardNumber>" . $this->credit_card->card_number . "</ewayCardNumber> 
							<ewayCardExpiryMonth>" . $this->credit_card->expiration_month . "</ewayCardExpiryMonth> 
							<ewayCardExpiryYear>" . $this->credit_card->get_expiration_year( 2 ) . "</ewayCardExpiryYear>
							<ewayTrxnNumber></ewayTrxnNumber> 
							<ewayOption1></ewayOption1> 
							<ewayOption2></ewayOption2> 
							<ewayOption3></ewayOption3> 
							<ewayCVN>" . $this->credit_card->security_code . "</ewayCVN>
						  </ewaygateway>";
			
			return $eway_xml;
			
		}
		
	}
	
	function get_gateway_url( ){
		
		$eway_test_mode = get_option( 'ec_option_eway_test_mode' );
		
		if( get_option( 'ec_option_eway_use_rapid_pay' ) ){ // Rapid Pay
			if( $eway_test_mode )
				return "https://api.sandbox.ewaypayments.com/Transaction";
			else
				return "https://api.ewaypayments.com/Transaction";
			
			
		}else{ // Direct Pay
			if( $eway_test_mode )
				return "https://www.eway.com.au/gateway_cvn/xmltest/testpage.asp";
			else
				return "https://www.eway.com.au/gateway_cvn/xmlpayment.asp";
				
		}
	}
	
	function get_gateway_response( $gateway_url, $gateway_data, $gateway_headers ){
		
		if( get_option( 'ec_option_eway_use_rapid_pay' ) ){ // Rapid Pay
			
			$eway_api_key = get_option( 'ec_option_eway_api_key' );
			$eway_api_password = get_option( 'ec_option_eway_api_password' );
		
			$headr = array();
			$headr[] = 'Content-Type: application/json';
			$userpwd = $eway_api_key . ":" . $eway_api_password;
			
			$ch = curl_init( );
			curl_setopt($ch, CURLOPT_URL, $gateway_url );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt($ch, CURLOPT_USERPWD, $userpwd );
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
			curl_setopt($ch, CURLOPT_POST, true ); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $gateway_data );
			curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
			$response = curl_exec($ch);
			if( $response === false )
				$this->mysqli->insert_response( 0, 1, "Eway CURL ERROR", curl_error( $ch ) );
			else
				$this->mysqli->insert_response( 0, 0, "Eway Response", print_r( $response, true ) );
			curl_close ($ch);
			
			return $response;
			
		}else{ // Direct Pay
			$request = new WP_Http;
			$response = $request->request( $gateway_url, array( 'method' => 'POST', 'body' => $gateway_data, 'headers' => $gateway_headers, 'timeout' => 30 ) );
			if( is_wp_error( $response ) ){
				$this->error_message = $response->get_error_message();
				$this->mysqli->insert_response( $this->order_id, 1, "Gateway Error", $this->error_message );
				return false;
			}else
				return $response;
			
		}
	}
	
	function handle_gateway_response( $response ){
		
		
		if( get_option( 'ec_option_eway_use_rapid_pay' ) ){ // Rapid Pay
		
			$data = json_decode( $response );
			if( $data->TransactionStatus == "1" ){
				$this->is_success = 1;
			}else{
				$this->is_success = 0;
			}
			
			$this->mysqli->insert_response( $this->order_id, !$this->is_success, "Eway Rapid", print_r( $data, true ) );
			
			if( !$this->is_success )
				$this->error_message = $data->Errors;
		
		}else{ // Direct Pay
		
			$response_body = $response["body"];
			
			$xml = new SimpleXMLElement($response_body);
			
			$response_text = print_r( $xml, true );
			
			if( $xml->ewayTrxnStatus == "True" )
				$this->is_success = 1;
			else
				$this->is_success = 0;
			
			$this->mysqli->insert_response( $this->order_id, !$this->is_success, "Eway", $response_text );
			
			if( !$this->is_success )
				$this->error_message = $ewayTrxnError;
				
		}
			
	}
	
}

?>