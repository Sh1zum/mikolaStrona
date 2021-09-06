<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_nmi extends ec_gateway{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	
	function get_gateway_data( ){
		
		$tax_total = number_format( $this->order_totals->tax_total + $this->order_totals->gst_total + $this->order_totals->pst_total + $this->order_totals->hst_total, 2, '.', '' );
		if( !$this->tax->vat_included )
			$tax_total = number_format( $tax_total + $this->order_totals->vat_total, 2, '.', '' );
		
		$data = array( 
			"type" 						=> "sale",
			"username"					=> get_option( 'ec_option_nmi_username' ),
			"password"					=> get_option( 'ec_option_nmi_password' ),
			"ccnumber"					=> $this->credit_card->card_number,
			"ccexp"						=> $this->credit_card->expiration_month . $this->credit_card->get_expiration_year( 2 ),
			"cvv"						=> $this->credit_card->security_code,
			"amount"					=> number_format( $this->order_totals->grand_total, 2, ".", "" ),
			"currency"					=> get_option( 'ec_option_nmi_currency' ),
			"payment"					=> "creditcard",
			"processor_id"				=> get_option( 'ec_option_nmi_processor_id' ),
			"firstname"					=> $this->user->billing->first_name,
			"lastname"					=> $this->user->billing->last_name,
			"phone"						=> $this->user->billing->phone,
			"address1"					=> $this->user->billing->address_line_1,
			"address2"					=> $this->user->billing->address_line_2,
			"city"						=> $this->user->billing->city,
			"state"						=> $this->user->billing->state,
			"zip"						=> $this->user->billing->zip,
			"country"					=> $this->user->billing->country,
			"email"						=> $this->user->email,
			"orderid"					=> $this->order_id,
			"ipaddress"					=> $_SERVER['REMOTE_ADDR'],
			"tax"						=> $tax_total,
			"shipping"					=> $this->order_totals->shipping_total,
			"ponumber"					=> $this->order_id,
			"shipping_country"			=> $this->user->shipping->country,
			"shipping_postal"			=> $this->user->shipping->zip,
			"ship_from_postal"			=> get_option( 'ec_option_nmi_ship_from_zip' ),
			"summary_commodity_code"	=> get_option( 'ec_option_nmi_commodity_code' ),
			"customer_receipt"			=> "false"
		);
		
		
		for( $i=0; $i<count( $this->cart->cart ); $i++ ){
			
			$item_tax = number_format( ( $this->cart->cart[$i]->total_price / $this->order_totals->sub_total ) * $tax_total, 2, '.', '' );
			$item_tax_rate = $item_tax / $this->cart->cart[$i]->total_price;
			
			$data['item_product_code_' . $i] = $this->cart->cart[$i]->model_number;
			$data['item_description_' . $i] = $this->cart->cart[$i]->title;
			$data['item_commodity_code_' . $i] = $this->cart->cart[$i]->TIC;
			$data['item_unit_of_measure_' . $i] = 'EACH';
			$data['item_unit_cost_' . $i] = $this->cart->cart[$i]->unit_price;
			$data['item_quantity_' . $i] = $this->cart->cart[$i]->quantity;
			$data['item_total_amount_' . $i] = $this->cart->cart[$i]->total_price;
			$data['item_tax_amount_' . $i] = $item_tax;
			$data['item_tax_rate_' . $i] = $item_tax_rate;
			
		}
		
		return $data;
	}
	
	function get_3ds_gateway_data( ){
		
		$db = new ec_db_admin( );
		$order = $db->get_order_row_admin( $_POST['order_id'] );
		
		$tax_total = number_format( $order->tax_total + $order->gst_total + $order->pst_total + $order->hst_total + $order->vat_total, 2, '.', '' );
		
		$data = array( 
			"type" 						=> "sale",
			"username"					=> get_option( 'ec_option_nmi_username' ),
			"password"					=> get_option( 'ec_option_nmi_password' ),
			"ccnumber"					=> $this->credit_card->card_number,
			"ccexp"						=> $this->credit_card->expiration_month . $this->credit_card->get_expiration_year( 2 ),
			"cvv"						=> $this->credit_card->security_code,
			"eci"						=> $_POST['eci'],
			"cavv"						=> $_POST['cavv'],
			"amount"					=> number_format( $order->grand_total, 2, ".", "" ),
			"currency"					=> get_option( 'ec_option_nmi_currency' ),
			"payment"					=> "creditcard",
			"processor_id"				=> get_option( 'ec_option_nmi_processor_id' ),
			"first_name"				=> $order->billing_first_name,
			"last_name"					=> $order->billing_last_name,
			"phone"						=> $order->billing_phone,
			"address1"					=> $order->billing_address_line_1,
			"city"						=> $order->billing_city,
			"state"						=> $order->billing_state,
			"zipcode"					=> $order->billing_zip,
			"country"					=> $order->billing_country,
			"orderid"					=> $order->order_id,
			"ipaddress"					=> $_SERVER['REMOTE_ADDR'],
			"tax"						=> $tax_total,
			"shipping"					=> number_format( $order->shipping_total, 2, ".", "" ),
			"ponumber"					=> $order->order_id,
			"shipping_country"			=> $order->shipping_country,
			"shipping_postal"			=> $order->shipping_zip,
			"ship_from_postal"			=> get_option( 'ec_option_nmi_ship_from_zip' ),
			"summary_commodity_code"	=> get_option( 'ec_option_nmi_commodity_code' ),
			"customer_receipt"			=> "false"
		);
		
		
		for( $i=0; $i<count( $this->cart->cart ); $i++ ){
			
			$item_tax = number_format( ( $this->cart->cart[$i]->total_price / $this->order_totals->sub_total ) * $tax_total, 2, '.', '' );
			$item_tax_rate = $item_tax / $this->cart->cart[$i]->total_price;
			
			$data['item_product_code_' . $i] = $this->cart->cart[$i]->model_number;
			$data['item_description_' . $i] = $this->cart->cart[$i]->title;
			$data['item_commodity_code_' . $i] = $this->cart->cart[$i]->TIC;
			$data['item_unit_of_measure_' . $i] = 'EACH';
			$data['item_unit_cost_' . $i] = $this->cart->cart[$i]->unit_price;
			$data['item_quantity_' . $i] = $this->cart->cart[$i]->quantity;
			$data['item_total_amount_' . $i] = $this->cart->cart[$i]->total_price;
			$data['item_tax_amount_' . $i] = $item_tax;
			$data['item_tax_rate_' . $i] = $item_tax_rate;
			
		}
		
		return $data;
	}
	
	function get_gateway_url( ){
		
		return "https://secure.networkmerchants.com/api/transact.php";
	
	}
	
	function handle_gateway_response( $response ){
		
		
		$response_body = $response['body'];
		$response_array = array( );
		parse_str( $response_body, $response_array );
		
		if( $response_array['response'] == 1 ){
			$this->mysqli->update_order_transaction_id( $this->order_id,  $response_array['transactionid'] );
			$this->is_success = true;
		}else
			$this->is_success = false;
		
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "NMI", print_r( $response, true ) );
		
		if( !$this->is_success )
			$this->error_message =  $response_array['responsetext'];
			
	}
	
	public function refund_charge( $gateway_transaction_id, $refund_amount ){
		
		$gateway_url = $this->get_gateway_url( );
		$data = array( 
			"type"	=> "refund",
			"username"		=> get_option( 'ec_option_nmi_username' ),
			"password"		=> get_option( 'ec_option_nmi_password' ),
			"transactionid"	=> $gateway_transaction_id,
			"amount"		=> $refund_amount
		);
		
		$request = new WP_Http;
		$response = $request->request( $gateway_url, array( 'method' => 'POST', 'body' => $data, 'headers' => "" ) );
		if( is_wp_error( $response ) ){
			$this->error_message = $response->get_error_message();
			return false;
		}else{
			$response_body = $response['body'];
			$response_array = array( );
			parse_str( $response_body, $response_array );
			
			$this->mysqli->insert_response( $order->order_id, 1, "NMI Refund", print_r( $response, true ) );
			
			if( $response_array['response'] == 1 ){
				
				return true;
				
			}else{
				
				return false;
			
			}
		}
		
	}
	
	public function process_3ds( ){
		
		$gateway_url = $this->get_gateway_url( );
		$data = $this->get_3ds_gateway_data( );
		
		$request = new WP_Http;
		$response = $request->request( $gateway_url, array( 'method' => 'POST', 'body' => $data, 'headers' => "" ) );
		if( is_wp_error( $response ) ){
			$this->error_message = $response->get_error_message();
			$this->mysqli->insert_response( $this->order_id, 1, "NMI 3DS Direct Post ERROR", $this->error_message );
			return false;
		}else{
			$response_body = $response['body'];
			$response_array = array( );
			parse_str( $response_body, $response_array );
			
			$this->mysqli->insert_response( $this->order_id, 1, "NMI 3DS Direct Post", print_r( $response, true ) );
			
			if( $response_array['response'] == 1 ){
				
				$this->mysqli->update_order_transaction_id( $this->order_id, $response_array['transactionid'] );
				return true;
				
			}else{
				
				return false;
			
			}
		}
		
	}
	
}

?>