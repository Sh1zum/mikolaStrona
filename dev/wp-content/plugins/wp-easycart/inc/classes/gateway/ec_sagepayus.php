<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_sagepayus extends ec_gateway{
	
	private $account_page;						// VARCHAR
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	function get_gateway_data( ){
		
		$m_id = get_option( 'ec_option_sagepayus_mid' );
		$m_key = get_option( 'ec_option_sagepayus_mkey' );
		$application_id = get_option( 'ec_option_sagepayus_application_id' );
		
		$sagepay_data = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
							<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">
  								<soap:Body>
									<BANKCARD_SALE xmlns=\"wsVTExtensions\">
									  <M_ID>" . $m_id . "</M_ID>
									  <M_KEY>" . $m_key . "</M_KEY>
									  <C_NAME>" . $this->credit_card->card_holder_name . "</C_NAME>
									  <C_ADDRESS>" . $this->user->billing->address_line_1 . "</C_ADDRESS>
									  <C_CITY>" . $this->user->billing->city . "</C_CITY>
									  <C_STATE>" . $this->user->billing->state . "</C_STATE>
									  <C_ZIP>" . $this->user->billing->zip . "</C_ZIP>
									  <C_COUNTRY>" . $this->user->billing->country . "</C_COUNTRY>
									  <C_EMAIL>" . $this->user->email . "</C_EMAIL>
									  <C_CARDNUMBER>" . $this->credit_card->card_number . "</C_CARDNUMBER>
									  <C_EXP>" . $this->credit_card->expiration_month . $this->credit_card->get_expiration_year( 2 ) . "</C_EXP>
									  <C_CVV>" . $this->credit_card->security_code . "</C_CVV>
									  <T_ORDERNUM>" . $this->order_id . "</T_ORDERNUM>
									  <T_AMT>" . number_format( $this->order_totals->grand_total, 2, ".", "" ) . "</T_AMT>
									  <C_TELEPHONE>" . $this->user->billing->phone . "</C_TELEPHONE>
									  <T_APPLICATION_ID>" . $application_id . "</T_APPLICATION_ID>
									</BANKCARD_SALE>
								</soap:Body>
							</soap:Envelope>";
		
		return $sagepay_data;
		
	}
	
	function get_gateway_url( ){
		
		return "https://gateway.sagepayments.net/web_services/wsvtextensions/transaction_processing.asmx";

	}
	
	function get_gateway_response( $gateway_url, $gateway_data, $gateway_headers ){
		
		$content_length = strlen( $gateway_data );
		
		$headr = array();
		$headr[] = 'Content-Type: text/xml; charset=utf-8';
		$headr[] = 'Content-Length: ' . $content_length;
		$headr[] = 'SOAPAction: "wsVTExtensions/BANKCARD_SALE"';
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt($ch, CURLOPT_POST, true ); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $gateway_data );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		curl_close ($ch);
		
		return $response;
			
	}
	
	function handle_gateway_response( $response ){
		
		$response = str_replace( ":", "", str_replace( 'xmlns="wsVTExtensions"', "", $response ) );
		
		$xml = simplexml_load_string( $response );
		
		$result = $xml->soapBody->BANKCARD_SALEResponse->BANKCARD_SALEResult->diffgrdiffgram->NewDataSet->Table1;
		
		if( $result->APPROVAL_INDICATOR == "A" )
			$this->is_success = 1;
		else
			$this->is_success = 0;
		
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "Sagepay US", print_r( $response, true ) );
		
		if( !$this->is_success )
			$this->error_message = $status_detail;
			
	}
	
}
?>