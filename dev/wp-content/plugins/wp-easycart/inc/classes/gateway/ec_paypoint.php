<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_paypoint extends ec_gateway{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	
	function get_gateway_data( ){
		
		$paypoint_merchant_id = get_option( 'ec_option_paypoint_merchant_id' );
		$paypoint_vpn_password = get_option( 'ec_option_paypoint_vpn_password' );
		$paypoint_test_mode = get_option( 'ec_option_paypoint_test_mode' );
		
		if( $paypoint_test_mode )
			$options = "test_status=true,dups=false,cv2=" . $this->credit_card->security_code; 
		else
			$options = "dups=false,cv2=" . $this->credit_card->security_code;
		
		$order_info = "";
		for( $i=0; $i<count($this->cart->cart); $i++){
			if($i>0)
			$order_info .= ";";
			$order_info .= "prod=".$this->cart->cart[$i]->title.",item_amount=".$this->cart->cart[$i]->unit_price."x".$this->cart->cart[$i]->quantity;
		}
		
		$shipping_info = "name=".$this->user->shipping->first_name." ".$this->user->shipping->last_name.",addr_1=".$this->user->shipping->address_line_1.",city=".$this->user->shipping->city.",state=".$this->user->shipping->state.",post_code=".$this->user->shipping->zip.",tel=".$this->user->shipping->phone.",email=".$this->user->email;
		
		$billing_info = "name=".$this->user->billing->first_name." ".$this->user->billing->last_name.",addr_1=".$this->user->billing->address_line_1.",city=".$this->user->billing->city.",state=".$this->user->billing->state.",post_code=".$this->user->billing->zip.",tel=".$this->user->billing->phone.",email=".$this->user->email;
		
		$paypoint_xml = "<?xml version=\"1.0\"?> 
						 <methodCall> 
							<methodName>SECVPN.validateCardFull</methodName> 
							<params>
								<param>
									<value><string>" . $paypoint_merchant_id . "</string></value>
								</param>
								<param>
									<value><string>" . $paypoint_vpn_password . "</string></value>
								</param>
								<param>
									<value><string>" . $this->order_id . "</string></value>
								</param>
								<param>
									<value><string>" . $_SERVER['REMOTE_ADDR'] . "</string></value>
								</param>
								<param>
									<value><string>" . $this->credit_card->card_holder_name . "</string></value>
								</param>
								<param>
									<value><string>" . $this->credit_card->card_number . "</string></value>
								</param>
								<param>
									<value><string>" . $this->order_totals->grand_total . "</string></value>
								</param>
								<param>
									<value><string>" . $this->credit_card->expiration_month . $this->credit_card->get_expiration_year( 2 ) . "</string></value>
								</param>
								<param>
									<value><string>" . $this->issue_number . "</string></value>
								</param>
								<param>
									<value><string>" . "" . "</string></value>
								</param>
								<param>
									<value><string>" . $order_info . "</string></value>
								</param>
								<param>
									<value><string>" . $shipping_info . "</string></value>
								</param>
								<param>
									<value><string>" . $billing_info . "</string></value>
								</param>
								<param>
									<value><string>" . $options . "</string></value>
								</param>
							</params>
						</methodCall>";
										
		return $paypoint_xml;
		
	}
	
	function get_gateway_url( ){
		
		return "https://www.secpay.com/secxmlrpc/make_call";
	}
	
	function handle_gateway_response( $response ){
		
		$response_body = $response["body"];
		
		$xml = new SimpleXMLElement($response_body);
		$vals = explode( "&", $xml->params->param->value[0]);
		$response_array = array();
		foreach( $vals as $val ){
			$split = explode( "=", $val );
			$response_array[ $split[0] ] = $split[1];
		}
		
		$valid = $response_array["?valid"];
		$message = $response_array["message"];
		$response_text = print_r( $response_array, true );
		
		if( $valid == "true" )
			$this->is_success = 1;
		else
			$this->is_success = 0;
			
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "PayPalPro", $response_text );
		
		if( !$this->is_success )
			$this->error_message = $message;
			
	}
	
}

?>