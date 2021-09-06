<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_goemerchant extends ec_gateway{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	
	function get_gateway_data( ){
		
		$goemerchant_gateway_id = get_option( 'ec_option_goemerchant_gateway_id' );
		$goemerchant_trans_center_id = get_option( 'ec_option_goemerchant_trans_center_id' );
		$goemerchant_processor_id = get_option( 'ec_option_goemerchant_processor_id' );
		
		if( $this->credit_card->payment_method == "visa" )
			$goemerchant_payment_method = "visa";
		else if( $this->credit_card->payment_method == "mastercard" )
			$goemerchant_payment_method = "mastercard";
		else if( $this->credit_card->payment_method == "amex" )
			$goemerchant_payment_method = "amex";
		else if( $this->credit_card->payment_method == "discover" )
			$goemerchant_payment_method = "discover";
		else
			$goemerchant_payment_method = "no";
		
		$goemerchant_xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?> 
<TRANSACTION> 
	<FIELDS>
		<FIELD KEY=\"transaction_center_id\">" . $goemerchant_trans_center_id . "</FIELD>
		<FIELD KEY=\"gateway_id\">" . $goemerchant_gateway_id . "</FIELD> 
		<FIELD KEY=\"operation_type\">Sale</FIELD>
		<FIELD KEY=\"processor_id\">" . $goemerchant_processor_id . "</FIELD>
		<FIELD KEY=\"order_id\">" . $this->order_id . "</FIELD>
		<FIELD KEY=\"total\">" . $this->order_totals->grand_total . "</FIELD>
		<FIELD KEY=\"card_name\">" . $goemerchant_payment_method . "</FIELD>
		<FIELD KEY=\"card_number\">" . $this->credit_card->card_number . "</FIELD>
		<FIELD KEY=\"card_exp\">" . $this->credit_card->expiration_month . $this->credit_card->get_expiration_year( 2 ) . "</FIELD>
		<FIELD KEY=\"cvv2\">" . $this->credit_card->security_code . "</FIELD>
		<FIELD KEY=\"owner_name\">" . $this->credit_card->card_holder_name . "</FIELD>
		<FIELD KEY=\"owner_street\">" . $this->user->billing->address_line_1 . "</FIELD>
		<FIELD KEY=\"owner_city\">" . $this->user->billing->city . "</FIELD>
		<FIELD KEY=\"owner_state\">" . $this->user->billing->state . "</FIELD>
		<FIELD KEY=\"owner_zip\">" . $this->user->billing->zip . "</FIELD>
		<FIELD KEY=\"owner_country\">" . $this->user->billing->country . "</FIELD>
		<FIELD KEY=\"owner_phone\">" . $this->user->billing->phone . "</FIELD>
		<FIELD KEY=\"owner_email\">" . $this->user->email . "</FIELD>
		<FIELD KEY=\"shipping_name\">" . $this->user->shipping->first_name . " " . $this->user->shipping->last_name . "</FIELD>
		<FIELD KEY=\"shipping_street\">" . $this->user->shipping->address_line_1 . "</FIELD>
		<FIELD KEY=\"shipping_city\">" . $this->user->shipping->city . "</FIELD>
		<FIELD KEY=\"shipping_state\">" . $this->user->shipping->state . "</FIELD>
		<FIELD KEY=\"shipping_zip\">" . $this->user->shipping->zip . "</FIELD>
		<FIELD KEY=\"shippping_country\">" . $this->user->shipping->country . "</FIELD>
		<FIELD KEY=\"shipping_phone\">" . $this->user->shipping->phone . "</FIELD>
		<FIELD KEY=\"remote_ip_address\">" . $_SERVER['REMOTE_ADDR'] . "</FIELD>
	</FIELDS> 
</TRANSACTION>";
		
		return $goemerchant_xml;
		
	}
	
	function get_gateway_url( ){
		
		return "https://secure.goemerchant.com/secure/gateway/xmlgateway.aspx";

	}
	
	function handle_gateway_response( $response ){
		
		$response_body = $response;
		$xml = new SimpleXMLElement($response_body);
		
		$result_code = "0";
		for( $i=0; $i<count( $xml->FIELDS->FIELD ); $i=$i+2 ){
			$atts = $xml->FIELDS->FIELD[1]->attributes();
			if( $atts["KEY"] == "auth_code" ){
				$result_code = $xml->FIELDS->FIELD[0];
				$i = count( $xml->FIELDS->FIELD );
			}
		}
		
		if( $result_code == "0" )
			$result_message = "There was an error procesing your request";
		else if( $result_code == "2" )
			$result_message = "The credit card was declined";
		
		$response_text = print_r( $xml, true );
		
		if( $result_code == "1" )
			$this->is_success = 1;
		else
			$this->is_success = 0;
		
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "GoeMerchant", $response_text );
		
		if( !$this->is_success )
			$this->error_message = $result_message;
			
	}
	
	function get_gateway_response( $gateway_url, $gateway_data, $gateway_headers ){
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_VERBOSE, 1);
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt( $curl, CURLOPT_TIMEOUT, 90);
		curl_setopt( $curl, CURLOPT_URL, $gateway_url);
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $gateway_data );
		
		$result = curl_exec($curl);      
		curl_close($curl);
		
		return $result;
	}
	
}

?>