<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_sagepay extends ec_gateway{
	
	private $account_page;						// VARCHAR
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	function get_gateway_data( ){
		
		$sagepay_vendor = get_option( 'ec_option_sagepay_vendor' );
		$sagepay_currency = get_option( 'ec_option_sagepay_currency' );
		$sagepay_simulator = get_option( 'ec_option_sagepay_simulator' );
		
		if( $this->credit_card->payment_method == "visa" )
			$card_type = "VISA";
		else if( $this->credit_card->payment_method == "mastercard" )
			$card_type = "MC";
		else if( $this->credit_card->payment_method == "mcdebit" )
			$card_type = "MCDEBIT";
		else if( $this->credit_card->payment_method == "delta" )
			$card_type = "DELTA";
		else if( $this->credit_card->payment_method == "maestro" )
			$card_type = "MAESTRO";
		else if( $this->credit_card->payment_method == "uke" )
			$card_type = "UKE";
		else if( $this->credit_card->payment_method == "amex" )
			$card_type = "AMEX";
		else if( $this->credit_card->payment_method == "discover" )
			$card_type = "DC";
		else if( $this->credit_card->payment_method == "jcb" )
			$card_type = "JCB";
		else if( $this->credit_card->payment_method == "laser" )
			$card_type = "LASER";
		
		if( $sagepay_simulator )
			$VPS_Protocol = "2.23";
		else
			$VPS_Protocol = "3.00";
			
		$account_page_id = get_option('ec_option_accountpage');
		$this->account_page = get_permalink( $account_page_id );
		
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$this->account_page = $https_class->makeUrlHttps( $this->account_page );
		}
		
		if( substr_count( $this->account_page, '?' ) )
			$this->permalink_divider = "&";
		else
			$this->permalink_divider = "?";
		
		//make sure to set IPV4, Sagepay not working with IPV6
		$ipaddress = null;                                                                    
		foreach ([                                                                           
			'HTTP_CF_CONNECTING_IP',                                                    
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'REMOTE_ADDR',                                                                
		] as $hdr) {
			$ipaddress = (
				(!empty($_SERVER[$hdr]))                                            
				&& (filter_var($_SERVER[$hdr], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) 
			) ? $_SERVER[$hdr] : $ipaddress;                                         
			if (!empty($ipaddress)) break;                                               
		}
		//if we ever have empty IP
		$ipaddress = empty($ipaddress) ? '127.0.0.1' : $ipaddress;                          
	
		
		$sagepay_data = array(	"VPSProtocol" => $VPS_Protocol,
								"TxType" => "PAYMENT", // PAYMENT, DEFERRED, OR AUTHENTICATE
								"Vendor" => $sagepay_vendor,
								"VendorTxCode" => $this->order_id,
								"Amount" => number_format( $this->order_totals->grand_total, 2, ".", "," ),
								"Currency" => $sagepay_currency,
								"Description" => "Online Sale",
								"CardHolder" => substr( $this->credit_card->card_holder_name, 0, 50 ),
								"CardNumber" => $this->credit_card->card_number,
								"ExpiryDate" => $this->credit_card->expiration_month . $this->credit_card->get_expiration_year( 2 ),
								"CV2" => $this->credit_card->security_code,
								"CardType" => $card_type,
								"BillingSurname" => substr( $this->user->billing->last_name, 0, 20 ),
								"BillingFirstnames" => substr( $this->user->billing->first_name, 0, 20 ),
								"BillingAddress1" => substr( $this->user->billing->address_line_1, 0, 100 ),
								"BillingCity" => substr( $this->user->billing->city, 0, 40 ),
								"BillingPostCode" => substr( $this->user->billing->zip, 0, 10 ),
								"BillingCountry" => $this->user->billing->country,
								"BillingPhone" => substr( $this->user->billing->phone, 0, 20 ),
								"DeliverySurname" => substr( $this->user->shipping->last_name, 0, 20 ),
								"DeliveryFirstnames" => substr( $this->user->shipping->first_name, 0, 20 ),
								"DeliveryAddress1" => substr( $this->user->shipping->address_line_1, 0, 100 ),
								"DeliveryCity" => substr( $this->user->shipping->city, 0, 40 ),
								"DeliveryPostCode" => preg_replace( "/[^A-Za-z0-9\-\s]/", '', substr( $this->user->shipping->zip, 0, 10 ) ),
								"DeliveryCountry" => $this->user->shipping->country,
								"DeliveryPhone" => substr( $this->user->shipping->phone, 0, 20 ),
								"ClientIPAddress" => $ipaddress,
								"ReferrerID" => "CF6785FC-6E02-40C4-9E9E-00B80CCA6376",
								"NotificationURL" => $this->account_page . $this->permalink_divider . "ec_page=order_details&order_id=" . $this->order_id,
								
					 );
		
		if( $this->user->email != "guest" )
			$sagepay_data["CustomerEMail"] = $this->user->email;
			
		if( $this->user->billing->country == "US" )
			$sagepay_data["BillingState"] = $this->user->billing->state;
		
		if( $this->user->shipping->country == "US" )
			$sagepay_data["DeliveryState"] = $this->user->shipping->state;
		
		return $sagepay_data;
		
	}
	
	function get_gateway_url( ){
		
		$sagepay_simulator = get_option( 'ec_option_sagepay_simulator' );
		$sagepay_test_mode = get_option( 'ec_option_sagepay_testmode' );
		
		if( $sagepay_simulator )
			return "https://test.sagepay.com/Simulator/VSPDirectGateway.asp";
		else if( $sagepay_test_mode )
			return "https://test.sagepay.com/gateway/service/vspdirect-register.vsp";
		else
			return "https://live.sagepay.com/gateway/service/vspdirect-register.vsp";

	}
	
	function handle_gateway_response( $response ){
		
		$response_body = $response["body"];
		
		//Format response data in form key=val&key2=val2&...
		$response_array = explode( "\r\n", $response_body );
		$response_vals = array();
		for( $i=0; $i<count($response_array); $i++){
			$split = explode( "=", $response_array[$i] );
			$val = "";
			for( $j=1; $j<count($split); $j++){
				if( $j > 1 )
					$val .= "=";
				$val .= $split[$j];
			}
			$response_vals[$split[0]] = $val; 
		}
		
		$status = $response_vals["Status"];
		if( isset( $response_vals["StatusDetail"] ) )
			$status_detail = $response_vals["StatusDetail"];
		else
			$status_detail = "";
			
		//3D Secure Specific Variables
		if( isset( $response_vals['3DSecureStatus'] ) )
			$secure_status = $response_vals['3DSecureStatus'];
		if( isset( $response_vals['MD'] ) )
			$secure_id = $response_vals['MD'];
		if( isset( $response_vals['ACSURL'] ) )
			$post_url = $response_vals['ACSURL'];
		if( isset( $response_vals['PAReq'] ) )
			$post_message = $response_vals['PAReq'];
		
		$response_text = print_r( $response_vals, true );
		
		if( $status == "3DAUTH" ){
			if( $secure_status == "OK" ){
				$this->is_3d_auth = true;
				$this->post_url = $post_url;
				$this->post_id_input_name = "MD";
				$this->post_id = $secure_id;
				$this->post_message_input_name = "PaReq";
				$this->post_message = $post_message;
				$this->post_return_url_input_name = "TermUrl";
				$this->is_success = 1;
			}else{
				$this->is_success = 0;	
			}
		}else if( $status == "OK" )
			$this->is_success = 1;
		else
			$this->is_success = 0;
		
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "Sagepay", $response_text );
		
		if( !$this->is_success )
			$this->error_message = $status_detail;
			
	}
	
	public function secure_3d_auth( ){
		
		if( get_option( 'ec_option_sagepay_simulator' ) )
			$gateway_url = "https://test.sagepay.com/Simulator/VSPDirectCallback.asp";
		else if( get_option( 'ec_option_sagepay_testmode' ) )
			$gateway_url = "https://test.sagepay.com/gateway/service/direct3dcallback.vsp";
		else
			$gateway_url = "https://live.sagepay.com/gateway/service/direct3dcallback.vsp";
			
		if( isset( $_POST['MD'] ) && isset( $_POST['PaRes'] ) )
			$gateway_data = array( "MD" => $_POST['MD'], "PARes" => $_POST['PaRes'] );
		
		
		if( isset( $gateway_url ) && isset( $gateway_data ) ){
			$request = new WP_Http;
			$response = $request->request( $gateway_url, array( 'method' => 'POST', 'body' => $gateway_data, 'headers' => "" ) );
			$response_body = $response["body"];
			
			//Format response data in form key=val&key2=val2&...
			$response_array = explode( "\r\n", $response_body );
			$response_vals = array();
			
			for( $i=0; $i<count($response_array); $i++){
				$split = explode( "=", $response_array[$i] );
				$val = "";
				for( $j=1; $j<count($split); $j++){
					if( $j > 1 )
						$val .= "=";
					$val .= $split[$j];
				}
				$response_vals[$split[0]] = $val; 
			}
            
            $this->mysqli->insert_response( (int) $_GET['order_id'], ( ( !isset( $response_vals['Status'] ) || isset( $response_vals['Status'] ) != 'OK' ) ? true: false ), "Sagepay 3ds", print_r( $response_vals, true ) );
			
			if( isset( $response_vals['Status'] ) )
				$status = $response_vals['Status'];
			else
				$status = "error";
			
			if( $status == "OK" ){
				$this->mysqli->update_order_status( $_GET['order_id'], "6" );
				// send email
				$db_admin = new ec_db_admin( );
				$order_row = $db_admin->get_order_row_admin( $_GET['order_id'] );
				$order_display = new ec_orderdisplay( $order_row, true, true );
				$order_display->send_email_receipt( );
				$this->mysqli->clear_tempcart( $GLOBALS['ec_cart_data']->ec_cart_id );
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
}
?>