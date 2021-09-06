<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_beanstream extends ec_gateway{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	
	function get_gateway_data( ){
		
		$data = array( 	
			"order_number"		=> $this->order_id,
			"amount"			=> number_format( $this->order_totals->grand_total, 2, ".", "" ),
			"payment_method"	=> "card",
			"card"				=> array(	
				"name"				=> $this->credit_card->card_holder_name,
				"number"			=> $this->credit_card->card_number,
				"expiry_month"		=> $this->credit_card->expiration_month,
				"expiry_year"		=> $this->credit_card->get_expiration_year( 2 ),
				"cvd"				=> $this->credit_card->security_code,
				"complete"			=> "true"
			),
			"billing"			=> array(
				"name"				=> $this->user->billing->first_name . ' ' . $this->user->billing->last_name,
				"address_line_1"	=> $this->user->billing->address_line_1,
				"address_line_2"	=> $this->user->billing->address_line_2,
				"city"				=> $this->user->billing->city,
				"province"			=> $this->user->billing->state,
				"postal_code"		=> $this->user->billing->zip,
				"country"			=> $this->user->billing->country,
				"phone_number"		=> $this->user->billing->phone,
				"email_address"		=> $this->user->email,
			)
		);
		
		return $data;
	}
	
	function get_gateway_response( $gateway_url, $gateway_data, $gateway_headers ){
		
		$merchant_id = get_option('ec_option_beanstream_merchant_id');
		$api_passcode = get_option('ec_option_beanstream_api_passcode');
		
		$encoded_authorization = base64_encode( $merchant_id . ":" . $api_passcode );
		
		$api_key = get_option( 'ec_option_stripe_api_key' );
		$headr = array();
		$headr[] = 'Content-Type: application/json';
		$headr[] = 'Authorization: Passcode ' . $encoded_authorization;
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $gateway_data ) );
		$response = curl_exec($ch);
		if( $response === false )
			$this->mysqli->insert_response( 0, 1, "BeanStream CURL ERROR", curl_error( $ch ) );
		curl_close ($ch);
		
		return $response;
			
	}
	
	function get_gateway_url( ){
		
		return "https://www.beanstream.com/api/v1/payments";
	
	}
	
	function handle_gateway_response( $response ){
		
		$result = json_decode( $response );
		$trans_id = $result->id;
		
		if( $result->approved == "1" ){
			$this->mysqli->update_order_transaction_id( $this->order_id, $trans_id );
			$this->is_success = true;
		}else
			$this->is_success = false;
		
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "Beanstream", print_r( $result, true ) );
		
		if( !$this->is_success )
			$this->error_message = $result->message;
			
	}
	
	public function refund_charge( $gateway_transaction_id, $refund_amount ){
		
		$gateway_url = "https://www.beanstream.com/api/v1/payments/" . $gateway_transaction_id . "/returns";
		
		$merchant_id = get_option('ec_option_beanstream_merchant_id');
		$api_passcode = get_option('ec_option_beanstream_api_passcode');
		
		$encoded_authorization = base64_encode( $merchant_id . ":" . $api_passcode );
		
		$api_key = get_option( 'ec_option_stripe_api_key' );
		$headr = array( );
		$headr[] = 'Content-Type: application/json';
		$headr[] = 'Authorization: Passcode ' . $encoded_authorization;
		
		global $wpdb;
		$order = $wpdb->get_row( $wpdb->prepare( "SELECT ec_order.creditcard_digits, ec_order.order_id, ec_order.grand_total FROM ec_order WHERE ec_order.gateway_transaction_id = %s", $gateway_transaction_id ) );
		
		$gateway_data = array( 	"order_number" 	=> $order->order_id,
								"amount" 		=> $refund_amount );
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $gateway_data ) );
		$response = curl_exec($ch);
		if( $response === false )
			$this->mysqli->insert_response( 0, 1, "BeanStream CURL ERROR", curl_error( $ch ) );
		curl_close ($ch);
						
		$result = json_decode( $response );
						
		$this->mysqli->insert_response( $order->order_id, 0, "Beanstream Refund", print_r( $result, true ) );
		
		if( $result->approved )
			return true;
		else
			return false;
		
		
	}
	
	private function get_error_message( $response_code, $response_reason_code ){
		
		switch( $response_code ){
			case "1":
				return "No Error";
				break;
			case "2":
				switch( $response_reason_code ){
					case "27":
						return "The transaction resulted in an AVS mismatch. The address provided does not match billing address of cardholder.";
						break;
					case "28":
						return "The merchant does not accept this type of credit card.";
						break;
					case "29":
						return "The Paymentech identification numbers are incorrect. Call Merchant Service Provider.";
						break;
					case "30":
						return "The configuration with the processor is invalid. Call Merchant Service Provider.";
						break;
					case "31":
						return "The FDC Merchant ID or Terminal ID is incorrect. Call Merchant Service Provider.";
						break;
					case "34":
						return "The VITAL identification numbers are incorrect. Call Merchant Service Provider.";
						break;
					case "35":
						return "An error occurred during processing. Call Merchant Service Provider.";
						break;
					case "37":
						return "The credit card number is invalid.";
						break;
					case "38":
						return "The Global Payment System identification numbers are incorrect. Call Merchant Service Provider.";
						break;
					case "127":
						return "The transaction resulted in an AVS mismatch. The address provided does not match billing address of cardholder";
						break;
					case "171":
						return "An error occurred during processing. Please contact the merchant.";
						break;
					case "172":
						return "An error occurred during processing. Please contact the merchant.";
						break;
					case "174":
						return "The transaction type is invalid. Please contact the merchant.";
						break;
					case "315":
						return "The credit card number is invalid.";
						break;
					case "316":
						return "The credit card expiration date is invalid.";
						break;
					case "317":
						return "The credit card has expired.";
						break;
					case "318":
						return "A duplicate transaction has been submitted.";
						break;
					case "319":
						return "The transaction cannot be found.";
						break;
					default:
						return "This transaction has been declined.";
						break;
				}
			case "3":
				switch( $response_reason_code ){
					case "5":
						return "A valid amount is required.";
						break;
					case "6":
						return "The credit card number is invalid.";
						break;
					case "7":
						return "The credit card expiration date is invalid.";
						break;
					case "8":
						return "The credit card has expired.";
						break;
					case "9":
						return "The ABA code is invalid.";
						break;
					case "10":
						return "The account number is invalid";
						break;
					case "11":
						return "A duplicate transaction has been submitted.";
						break;
					case "12":
						return "An authorization code is required but not present.";
						break;
					case "13":
						return "The merchant API Login ID is invalid or the account is inactive.";
						break;
					case "14":
						return "The Referrer or Relay Response URL is invalid.";
						break;
					case "15":
						return "The transaction ID is invalid.";
						break;
					case "16":
						return "The transaction was not found.";
						break;
					case "17":
						return "The merchant does not accept this type of credit card.";
						break;
					case "18":
						return "ACH transactions are not accepted by this merchant.";
						break;
					case "19":
						return "An error occurred during processing. Please try again in 5 minutes.";
						break;
					case "20":
						return "An error occurred during processing. Please try again in 5 minutes.";
						break;
					case "21":
						return "An error occurred during processing. Please try again in 5 minutes.";
						break;
					case "22":
						return "An error occurred during processing. Please try again in 5 minutes.";
						break;
					case "23":
						return "An error occurred during processing. Please try again in 5 minutes.";
						break;
					case "24":
						return "The Nova Bank Number or Terminal ID is incorrect. Call Merchant Service Provider.";
						break;
					case "25":
						return "An error occurred during processing. Please try again in 5 minutes.";
						break;
					case "26":
						return "An error occurred during processing. Please try again in 5 minutes.";
						break;
					case "32":
						return "This reason code is reserved or not applicable to this API.";
						break;
					default:
						return "ERROR: $responseCodeReason This transaction has been declined.";
						break;
				}
			case "4":
				return "ERROR: This transaction is being held for review.";
				break;
			default:
				return "ERROR 100: There was an error processing your order";
		}
	}
	
}

?>