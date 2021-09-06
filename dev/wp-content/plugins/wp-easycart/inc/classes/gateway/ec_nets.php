<?php

class ec_nets extends ec_third_party{
	
	public function display_form_start( ){ }// Lets ignore this for this gateway
	
	public function display_auto_forwarding_form( ){
		
		$gateway_url = $this->get_gateway_url( );
		$gateway_data = $this->get_gateway_data( );
		
		echo $gateway_url;
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_POST, true ); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $gateway_data ) );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)8);
		$response = curl_exec($ch);
		curl_close ($ch);
		
		$this->handle_gateway_response( $response );

	}
	
	function get_gateway_data( ){
		
		$merchant_id = get_option( 'ec_option_nets_merchant_id' );
		$token = get_option( 'ec_option_nets_token' );
		$currency_code = get_option( 'ec_option_nets_currency' );
		
		$serviceType = 'M';
		
		$register_data = array( 'orderNumber' 			=>	$this->order_id,
								'currencyCode'			=>	$currency_code,
								'amount'				=>	number_format( $this->order->grand_total * 100, 0, "", "" ),
								'webServicePlatform'	=>	"PHP5",
								'customerEmail'			=> 	$this->order->user_email,
								'customerPhoneNumber'	=>	$this->order->billing_phone,
								'customerFirstName'		=>	$this->order->billing_first_name,
								'customerLastName'		=>	$this->order->billing_last_name,
								'customerAddress1'		=>	$this->order->billing_address_line_1,
								'customerAddress2'		=>	$this->order->billing_address_line_2,
								'customerPostcode'		=>	$this->order->billing_zip,
								'customerTown'			=>	$this->order->billing_city,
								'customerCountry'		=>	$this->order->billing_country,
								'merchantID'			=>	$merchant_id,
								'token'					=>	$token,
								'redirectUrl'			=>	$this->cart_page . $this->permalink_divider . "ec_page=nets_return"
							);
		
		if( $this->order->vat_total ){
			$register_data['terminalVat'] = $this->order->vat_total;
		}
		
		return $register_data;
		
	}
	
	function get_process_data( $transaction_id ){
		
		$merchant_id = get_option( 'ec_option_nets_merchant_id' );
		$token = get_option( 'ec_option_nets_token' );
		$operation = 'SALE';
		
		$process_data = array(	'merchantID'			=>	$merchant_id,
								'token'					=>	$token,
								'operation'				=>	$operation,
								'transactionId'			=>	$transaction_id );
		
		return $process_data;
		
	}
	
	function get_gateway_url( ){
		
		$test_mode = get_option( 'ec_option_nets_test_mode' );
		
		if( $test_mode )
			return "https://epayment-test.bbs.no/Netaxept/Register.aspx";
		else
			return "https://epayment.nets.eu/Netaxept/Register.aspx";

	}
	
	function get_gateway_process_url( ){
		
		$test_mode = get_option( 'ec_option_nets_test_mode' );
		
		if( $test_mode )
			return "https://epayment-test.bbs.no/Netaxept/Process.aspx";
		else
			return "https://epayment.nets.eu/Netaxept/Process.aspx";
		
	}
	
	function get_gateway_response( $gateway_url, $gateway_data, $gateway_headers ){
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		//curl_setopt($ch, CURLOPT_HTTPHEADER, $gateway_headers );
		curl_setopt($ch, CURLOPT_POST, true ); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $gateway_data ) );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		curl_close ($ch);
		
		return $response;
		
	}
	
	function forward_to_terminal( $transaction_id ){
		
		$test_mode = get_option( 'ec_option_nets_test_mode' );
		$merchant_id = get_option( 'ec_option_nets_merchant_id' );
		
		if( $test_mode )
			header( "Location: https://test.epayment.nets.eu/Terminal/default.aspx?merchantId=" . $merchant_id . "&transactionId=" . $transaction_id );
		else
			header( "Location: https://epayment.nets.eu/Terminal/default.aspx?merchantId=" . $merchant_id . "&transactionId=" . $transaction_id );
			
	}
	
	function handle_gateway_response( $response ){
		
		// Process the response, get the transaction id
		$xml = new SimpleXMLElement( $response );
		
		foreach( $xml->TransactionId as $id ){
			$transaction_id = $id;
		}
		
		$transaction_id = (string)$transaction_id;
		
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "UPDATE ec_order SET ec_order.nets_transaction_id = %s WHERE ec_order.order_id = %d", $transaction_id, $this->order_id ) );
		
		$test_mode = get_option( 'ec_option_nets_test_mode' );
		$merchant_id = get_option( 'ec_option_nets_merchant_id' );
		
		if( $test_mode ){
			$url = "https://test.epayment.nets.eu/Terminal/default.aspx?merchantId=" . $merchant_id . "&transactionId=" . $transaction_id;
		}else{
			$url = "https://epayment.nets.eu/Terminal/default.aspx?merchantId=" . $merchant_id . "&transactionId=" . $transaction_id;
		}
		
		// Forward to terminal
		header( "location:" . $url );
		
		die( );
	}
	
	function process_payment_final( $order_id, $transaction_id, $status_code ){
		
		$cart_page_id = get_option('ec_option_cartpage');
		$this->cart_page = get_permalink( $cart_page_id );
		
		$account_page_id = get_option('ec_option_accountpage');
		$this->account_page = get_permalink( $account_page_id );
		
		//added - jjones
		$mysqli = new ec_db( );
		$mysqli->insert_response( $order_id, 0, "NETS", print_r( $response_body, true ) );
		$order_row = $mysqli->get_order_row( $order_id, "guest", "guest" );
		//end addition - jjones
		
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$this->store_page = $https_class->makeUrlHttps( $this->store_page );
			$this->cart_page = $https_class->makeUrlHttps( $this->cart_page );
			$this->account_page = $https_class->makeUrlHttps( $this->account_page );
		}
				
		if( substr_count( $this->cart_page, '?' ) )					$this->permalink_divider = "&";
		else														$this->permalink_divider = "?";
		
		if( $status_code != "OK" ){
			
			header( "location:" . $this->account_page . $this->permalink_divider . "ec_page=order_details&order_id=" . $order_id . "&account_error=nets_processing" );
			
		}else{
			
			$data = $this->get_process_data( $transaction_id );
			$response_final = $this->get_gateway_response( $this->get_gateway_process_url( ), $data, "" );
			
			$xml = new SimpleXMLElement( $response_final );
			$code = $xml->ResponseCode;
			
			if( $code == "OK" ){
				
				global $wpdb;
				$mysqli->update_order_status( $order_id, "10" );
				
				// send email
				$db_admin = new ec_db_admin( );
				$order_row = $db_admin->get_order_row_admin( $order_id );
				$orderdetails = $db_admin->get_order_details_admin( $order_id );
				
				/* Update Stock Quantity */
				foreach( $orderdetails as $orderdetail ){
					$product = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.* FROM ec_product WHERE ec_product.product_id = %d", $orderdetail->product_id ) );
					if( $product ){
						if( $product->use_optionitem_quantity_tracking )	
							$db_admin->update_quantity_value( $orderdetail->quantity, $orderdetail->product_id, $orderdetail->optionitem_id_1, $orderdetail->optionitem_id_2, $orderdetail->optionitem_id_3, $orderdetail->optionitem_id_4, $orderdetail->optionitem_id_5 );
						$db_admin->mysqli->update_product_stock( $orderdetail->product_id, $orderdetail->quantity );
					}
				}
				
				$order_display = new ec_orderdisplay( $order_row, true, true );
				$order_display->send_email_receipt( );
				$order_display->send_gift_cards( );
			
				do_action( 'wpeasycart_order_paid', $order_id );
				
				$this->is_success = 1;
			}else
				$this->is_success = 0;
			
			$this->mysqli->insert_response( $this->order_id, !$this->is_success, "Nets Payments", print_r( $xml, true ) );
			
			if( !$this->is_success )
				$this->error_message = $result_message;
				
			if( $this->is_success ){

				header( "location:" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $order_id );
			}else{
				header( "location:" . $this->account_page . $this->permalink_divider . "ec_page=order_details&order_id=" . $order_id . "&account_error=nets_processing_payment" );
			}
		}
	}

}

?>