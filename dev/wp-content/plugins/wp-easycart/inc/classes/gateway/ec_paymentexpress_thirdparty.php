<?php
class ec_paymentexpress_thirdparty extends ec_third_party{
	
	private $payment_express_redirect_url;						// STRING
	
	public function display_form_start( ){
		echo "<form action=\"" . $this->payment_express_redirect_url . "\" method=\"post\">";
		
		
	}
	
	public function display_auto_forwarding_form( ){
		$payment_express_username = get_option( 'ec_option_payment_express_thirdparty_username' );
		$payment_express_key = get_option( 'ec_option_payment_express_thirdparty_key' );
		$payment_express_currency = get_option( 'ec_option_payment_express_thirdparty_currency' );
		
		$payment_express_xml = "<GenerateRequest>
									<PxPayUserId>" . $payment_express_username . "</PxPayUserId>
									<PxPayKey>" . $payment_express_key . "</PxPayKey>
									<AmountInput>" . number_format($this->order->grand_total, 2, '.', '' ). "</AmountInput>
									<CurrencyInput>" . $payment_express_currency . "</CurrencyInput>
									<MerchantReference>" . $this->order_id . "</MerchantReference>
									<EmailAddress>" . $this->order->user_email . "</EmailAddress>
									<TxnData1>" . htmlentities( $this->order->billing_first_name . " " . $this->order->billing_last_name ) . "</TxnData1>
									<TxnData2>" . htmlentities( $this->order->billing_phone ) . "</TxnData2>
									<TxnData3>" . htmlentities( $this->order->billing_address_line_1 . ", " . $this->order->billing_city . " " . $this->order->billing_zip ) . "</TxnData3>
									<TxnType>Purchase</TxnType>
									<TxnId>" . $this->order_id . "</TxnId>
									<BillingId></BillingId>
									<EnableAddBillCard>0</EnableAddBillCard>
									<UrlSuccess>" . htmlentities( $this->cart_page . $this->permalink_divider . "ec_action=paymentexpress&ec_page=checkout_success&order_id=" . $this->order_id ) . "</UrlSuccess>
									<UrlFail>". htmlentities( $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment" ) . "</UrlFail>
								</GenerateRequest>";
		
		$response = $this->send_xml_request( $payment_express_xml );
		
		$response_body = $response["body"];
		$xml = new SimpleXMLElement($response_body);
		
		if( isset( $xml->URI ) ){
			
			$this->payment_express_redirect_url = $xml->URI;
		
			echo "<form action=\"" . $this->payment_express_redirect_url . "\" method=\"post\" name=\"ec_paymentexpress_auto_form\" id=\"ec_paymentexpress_auto_form\">";
			echo "</form>";
			echo "<SCRIPT LANGUAGE=\"Javascript\">document.ec_paymentexpress_auto_form.submit();</SCRIPT>";
	
		}else{
			
			header( "location: " . $_SERVER['HTTP_REFERER'] . "&ec_cart_error=thirdparty_failed" );
			
		}
		
	}
	
	private function send_xml_request( $xml ){
		$request = new WP_Http;
		$response = $request->request( "https://sec.paymentexpress.com/pxaccess/pxpay.aspx", array( 'method' => 'POST', 'body' => $xml, 'headers' => "" ) );
		$mysqli = new ec_db( );
		$mysqli->insert_response( $this->order_id, 1, "Payment Express 3rd Party", print_r( $response, true ) );
		if( is_wp_error( $response ) ){
			$this->error_message = $response->get_error_message();
			return false;
		}else
			return $response;
	}
	
	public function update_order_status( ){
		$payment_express_username = get_option( 'ec_option_payment_express_thirdparty_username' );
		$payment_express_key = get_option( 'ec_option_payment_express_thirdparty_key' );
		$response_val = $_GET['result'];
		
		$xml = "<ProcessResponse>
				  <PxPayUserId>" . $payment_express_username . "</PxPayUserId>
				  <PxPayKey>" . $payment_express_key . "</PxPayKey>
				  <Response>" . $response_val . "</Response>
				</ProcessResponse>";
		$response = $this->send_xml_request( $xml );
		$response_body = $response["body"];
		$this->process_result( $response_body );
	}
	
	private function process_result( $response_body ){
		
		$xml = new SimpleXMLElement( $response_body );
			
		if( isset( $_GET['order_id'] ) ){
			
			$order_id = $_GET['order_id'];
			
			global $wpdb;
			$mysqli = new ec_db_admin( );
			$order_row = $mysqli->get_order_row_admin( $order_id );
			$orderdetails = $mysqli->get_order_details_admin( $order_id );
			
			if( $_POST['AUTHCODE'] == 'refund' ){ 
					$mysqli->update_order_status( $order_id, "16" );
			
			}else if( $order_row->orderstatus_id != "10" ){
			
				// Insert Response
				$mysqli->insert_response( $order_id, 0, "PaymentExpress Third Party", print_r( $response_body, true ) );
				
				if( $xml->Success == '1' ){ 
					
					$this->clear_session( );
					
					// Fix for PXPay in which the script is called twice very quickly.
					$db_admin = new ec_db_admin( );
					$order_row = $db_admin->get_order_row_admin( $order_id );
					
					if( $order_row->orderstatus_id != "10" ){
						$mysqli->update_order_status( $order_id, "10" );
					
						do_action( 'wpeasycart_order_paid', $order_id );
						
						/* Update Stock Quantity */
						foreach( $orderdetails as $orderdetail ){
							$product = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.* FROM ec_product WHERE ec_product.product_id = %d", $orderdetail->product_id ) );
							if( $product ){
								if( $product->use_optionitem_quantity_tracking )	
									$mysqli->update_quantity_value( $orderdetail->quantity, $orderdetail->product_id, $orderdetail->optionitem_id_1, $orderdetail->optionitem_id_2, $orderdetail->optionitem_id_3, $orderdetail->optionitem_id_4, $orderdetail->optionitem_id_5 );
								$mysqli->update_product_stock( $orderdetail->product_id, $orderdetail->quantity );
							}
						}
						
						
						// send email
						$order_display = new ec_orderdisplay( $order_row, true, true );
						$order_display->send_email_receipt( );
						$order_display->send_gift_cards( );
						
					}
					
				}
				
			}
		
		}
	
	}
	
	private function clear_session( ){
		$GLOBALS['ec_cart_data']->cart_data->billing_first_name = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_last_name = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_address = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_address2 = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_city = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_state = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_zip = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_country = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_phone = "";
		
		$GLOBALS['ec_cart_data']->cart_data->shipping_first_name = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_last_name = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_address = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_address2 = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_city = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_state = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_zip = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_country = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_phone = "";
		
		$GLOBALS['ec_cart_data']->cart_data->use_shipping = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_method = "";
		$GLOBALS['ec_cart_data']->cart_data->expedited_shipping = ""; 
		
		if( $GLOBALS['ec_cart_data']->cart_data->create_account != "" ){
			$GLOBALS['ec_cart_data']->cart_data->first_name = "";
			$GLOBALS['ec_cart_data']->cart_data->last_name = "";
		}
		
		$GLOBALS['ec_cart_data']->cart_data->create_account = "";
		$GLOBALS['ec_cart_data']->cart_data->coupon_code = "";
		$GLOBALS['ec_cart_data']->cart_data->giftcard = "";
		$GLOBALS['ec_cart_data']->cart_data->order_notes = "";
		$GLOBALS['ec_cart_data']->save_session_to_db( );
	}
	
}
?>