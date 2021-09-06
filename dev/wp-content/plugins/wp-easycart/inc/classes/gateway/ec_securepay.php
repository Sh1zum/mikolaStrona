<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_securepay extends ec_gateway{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	function get_gateway_data( ){
		
		$securepay_merchant_id = get_option( 'ec_option_securepay_merchant_id' );
		$securepay_password = get_option( 'ec_option_securepay_password' );
		$securepay_currency = get_option( 'ec_option_securepay_currency' );
		
		if( $securepay_currency == "JPY" )
			$grandtotal = number_format( $this->order_totals->grand_total, 0, '', '' );
		else
			$grandtotal = $this->order_totals->get_grand_total_in_cents( );
			
		// Create time stamp
		$date = new DateTime();
		$securepay_timestamp  = $date->getTimestamp() . "+660";
		
		$securepay_xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><SecurePayMessage><MessageInfo><messageID>" . rand( 10000000000, 9999999999999999 ) . "</messageID><messageTimestamp>" . $securepay_timestamp . "</messageTimestamp><timeoutValue>60</timeoutValue><apiVersion>xml-4.2</apiVersion></MessageInfo><MerchantInfo><merchantID>" . $securepay_merchant_id . "</merchantID><password>" . $securepay_password . "</password></MerchantInfo><RequestType>Payment</RequestType><Payment><TxnList count=\"1\"><Txn ID=\"1\"><txnType>0</txnType><txnSource>23</txnSource><amount>" . $grandtotal . "</amount><currency>" . $securepay_currency . "</currency><purchaseOrderNo>" . $this->order_id . "</purchaseOrderNo><CreditCardInfo><cardNumber>" . $this->credit_card->card_number . "</cardNumber><cvv>" . $this->credit_card->security_code . "</cvv><expiryDate>" . $this->credit_card->expiration_month . "/" . $this->credit_card->get_expiration_year( 2 ) . "</expiryDate></CreditCardInfo></Txn></TxnList></Payment></SecurePayMessage>";
		
		$this->mysqli->insert_response( 0, 0, "SecurePay Data", $securepay_xml );
		
		return $securepay_xml;
		
	}
	
	function get_gateway_url( ){
		
		$securepay_test_mode = get_option( 'ec_option_securepay_test_mode' );
		
		if( $securepay_test_mode )
			return "https://test.securepay.com.au/xmlapi/payment";
		else
			return "https://api.securepay.com.au/xmlapi/payment";

	}
	
	function get_gateway_response( $gateway_url, $gateway_data, $gateway_headers ){
		
		$headr = array();
		//$headr[] = 'Authorization: Basic ' . $api_base64_encoded;
		$headr[] = 'Content-Type: text/xml';
		$headr[] = "Content-length: " . strlen( $gateway_data );
		//$headr[] = json_encode( $gateway_data );
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		
		curl_setopt($ch, CURLOPT_POST, true ); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $gateway_data );
		
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		$response = curl_exec($ch);
		if( $response === false ){
			$this->mysqli->insert_response( $this->order_id, 0, "SecurePay CURL ERROR", curl_error( $ch ) );
			curl_close ($ch);
			return false;
		}else{
			curl_close ($ch);
			return $response;
		}
		
	}
	
	function handle_gateway_response( $response ){
		
		$this->mysqli->insert_response( $this->order_id, 0, "Securepay Pre Processing", print_r( $response, true ) );
		
		$response_body = $response;
		$xml = new SimpleXMLElement($response_body);
		
		$transaction_approved = "No";
		$message_id = $xml->MessageInfo->messageID;
		$message_time_stamp = $xml->MessageInfo->messageTimestamp;
		$api_version = $xml->MessageInfo->apiVersion;
		$request_type = $xml->RequestType;
		$merchant_id = $xml->MerchantInfo->merchantID;
		$status_code = $xml->Status->statusCode;
		$status_description = $xml->Status->statusDescription;
		
		if( $xml->Payment ){
			foreach( $xml->Payment->TxnList as $TxnList ){
				foreach( $TxnList->Txn as $Txn ){
					$this->mysqli->insert_response( $this->order_id, 0, "Securepay Item XML", print_r( $Txn, true ) );
					$transaction_approved = $Txn->approved;
					$transaction_response_text = $Txn->responseText;
				}
			}
		}
		
		$response_text = print_r( $xml, true );
		
		if( $transaction_approved == "Yes" )
			$this->is_success = 1;
		else
			$this->is_success = 0;
		
		$this->mysqli->insert_response( $this->order_id, $this->is_success, "Securepay", $response_text );
		
		if( !$this->is_success )
			$this->error_message = $transaction_response_text;
			
	}
	
}

?>