<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_paymentexpress extends ec_gateway{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	
	function get_gateway_data( ){
		
		$payment_express_username = get_option( 'ec_option_payment_express_username' );
		$payment_express_password = get_option( 'ec_option_payment_express_password' );
		$payment_express_currency = get_option( 'ec_option_payment_express_currency' );
		
		
		$payment_express_xml .= "<Txn>
							         <PostUsername>" . $payment_express_username . "</PostUsername>
							         <PostPassword>". $payment_express_password . "</PostPassword>
							         <CardHolderName>" . $this->credit_card->card_holder_name . "</CardHolderName>
							         <CardNumber>"  .$this->credit_card->card_number . "</CardNumber>
							         <Amount>" . $this->order_totals->grand_total . "</Amount>
							         <DateExpiry>" . $this->credit_card->expiration_month . $this->credit_card->get_expiration_year( 2 ) . "</DateExpiry>
							         <Cvc2>" . $this->credit_card->security_code . "</Cvc2>
							         <InputCurrency>" . $payment_express_currency . "</InputCurrency>
							         <TxnType>Purchase</TxnType>
							         <TxnId>" . $this->order_id . "</TxnId>
							         <MerchantReference>" . $this->order_id . "</MerchantReference>
							</Txn>";
			  
		return $payment_express_xml;
		
	}
	
	function get_gateway_url( ){
		
		if( get_option( 'ec_option_payment_express_developer_account' ) )
			return "https://uat.paymentexpress.com/pxpost.aspx";

		else
			return "https://sec.paymentexpress.com/pxpost.aspx";

	}
	
	function handle_gateway_response( $response ){
		
		$response_body = $response["body"];
		$xml = new SimpleXMLElement($response_body);
		
		$authorized = $xml->Transaction->Authorized;
		
		$response_text = print_r( $xml->Transaction, true );
		
		if( $authorized == 1 )
			$this->is_success = 1;
		else
			$this->is_success = 0;
		
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "PaymentExpress", $response_text );
		
		if( !$this->is_success )
			$this->error_message = $xml->Transaction->MerchantResponseDescription;
			
	}
	
}

?>