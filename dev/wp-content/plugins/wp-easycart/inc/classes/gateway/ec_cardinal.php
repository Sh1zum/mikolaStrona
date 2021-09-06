<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_cardinal extends ec_3ds{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	
	function get_lookup_data( ){
		
		$tax_total = number_format( $this->order_totals->tax_total + $this->order_totals->gst_total + $this->order_totals->pst_total + $this->order_totals->hst_total, 2, '.', '' );
		if( !$this->tax->vat_included )
			$tax_total = number_format( $tax_total + $this->order_totals->vat_total, 2, '.', '' );
		
		$data = "<CardinalMPI>
	<MsgType>cmpi_lookup</MsgType>
	<Version>1.7</Version>
	<ProcessorId>" . get_option( 'ec_option_cardinal_processor_id' ) . "</ProcessorId>
	<MerchantId>" . get_option( 'ec_option_cardinal_merchant_id' ) . "</MerchantId>
	<TransactionPwd>" . get_option( 'ec_option_cardinal_password' ) . "</TransactionPwd>
	<TransactionType>C</TransactionType>
	<Amount>" . number_format( $this->order_totals->grand_total * 100, 0, '', '' ) . "</Amount>
	<TaxAmount>" . number_format( $tax_total * 100, 0, '', '' ) . "</TaxAmount>
	<ShippingAmount>" . number_format( $this->order_totals->shipping_total * 100, 0, '', '' ) . "</ShippingAmount>
	<CurrencyCode>" . get_option( 'ec_option_cardinal_currency' ) . "</CurrencyCode>
	<CardNumber>" . $this->credit_card->card_number . "</CardNumber>
	<CardExpMonth>" . $this->credit_card->expiration_month . "</CardExpMonth>
	<CardExpYear>" . $this->credit_card->get_expiration_year( 4 ) . "</CardExpYear>
	<OrderNumber>" . $this->order_id . "</OrderNumber>
	<MerchantReferenceNumber>" . $this->order_id . "</MerchantReferenceNumber>
	<TransactionMode>S</TransactionMode>
	<EMail>" . $this->user->email . "</EMail>
	<IPAddress>" . $_SERVER['REMOTE_ADDR'] . "</IPAddress>
	<BillingFirstName>" . $this->user->billing->first_name . "</BillingFirstName>
	<BillingLastName>" . $this->user->billing->last_name . "</BillingLastName>
	<BillingAddress1>" . $this->user->billing->address_line_1 . "</BillingAddress1>
	<BillingAddress2>" . $this->user->billing->address_line_2 . "</BillingAddress2>
	<BillingCity>" . $this->user->billing->city . "</BillingCity>
	<BillingState>" . $this->user->billing->state . "</BillingState>
	<BillingPostalCode>" . $this->user->billing->zip . "</BillingPostalCode>
	<BillingCountryCode>" . $this->user->billing->country . "</BillingCountryCode>
	<BillingPhone>" . str_replace( array( "(", ")", ".", "-", "+" ), "", $this->user->billing->phone ) . "</BillingPhone>
	<ShippingFirstName>" . $this->user->shipping->first_name . "</ShippingFirstName>
	<ShippingLastName>" . $this->user->shipping->last_name . "</ShippingLastName>
	<ShippingAddress1>" . $this->user->shipping->address_line_1 . "</ShippingAddress1>
	<ShippingAddress2>" . $this->user->shipping->address_line_2 . "</ShippingAddress2>
	<ShippingCity>" . $this->user->shipping->city . "</ShippingCity>
	<ShippingState>" . $this->user->shipping->state . "</ShippingState>
	<ShippingPostalCode>" . $this->user->shipping->zip . "</ShippingPostalCode>
	<ShippingCountryCode>" . $this->user->shipping->country . "</ShippingCountryCode>
	<ShippingPhone>" . str_replace( array( "(", ")", ".", "-", "+" ), "", $this->user->shipping->phone ) . "</ShippingPhone>";
		
		
		for( $i=0; $i<count( $this->cart->cart ); $i++ ){
			
			$item_tax = number_format( ( $this->cart->cart[$i]->total_price / $this->order_totals->sub_total ) * $tax_total, 2, '.', '' );
			$item_tax_rate = $item_tax / $this->cart->cart[$i]->total_price;
			
			$data .= "
	<Item_Name_" . ($i+1) . ">" . $this->cart->cart[$i]->title . "</Item_Name_" . ($i+1) . ">
	<Item_Desc_" . ($i+1) . ">" . substr( strip_tags( $this->cart->cart[$i]->description ), 256 ) . "</Item_Desc_" . ($i+1) . ">
	<Item_Price_" . ($i+1) . ">" . number_format( $this->cart->cart[$i]->unit_price * 100, 0, '', '' ) . "</Item_Price_" . ($i+1) . ">
	<Item_Quantity_" . ($i+1) . ">" . $this->cart->cart[$i]->quantity . "</Item_Quantity_" . ($i+1) . ">
	<Item_SKU_" . ($i+1) . ">" . $this->cart->cart[$i]->model_number . "</Item_SKU_" . ($i+1) . ">";
			
		}
		$data .= "
</CardinalMPI>";
		
		return "cmpi_msg=".urlencode( str_replace( "&nbsp;", "", $data ) );
	}
	
	function get_authenticate_data( $transaction_response ){
		
		$data = "<CardinalMPI>
	<MsgType>cmpi_authenticate</MsgType>
	<Version>1.7</Version>
	<ProcessorId>" . get_option( 'ec_option_cardinal_processor_id' ) . "</ProcessorId>
	<MerchantId>" . get_option( 'ec_option_cardinal_merchant_id' ) . "</MerchantId>
	<TransactionType>C</TransactionType>
	<TransactionPwd>" . get_option( 'ec_option_cardinal_password' ) . "</TransactionPwd>
	<TransactionId>" . $transaction_response['MD'] . "</TransactionId>
	<PAResPayload>" . $transaction_response['PaRes'] . "</PAResPayload>
</CardinalMPI>";

		return "cmpi_msg=".urlencode( str_replace( "&nbsp;", "", $data ) );
		
	}
	
	function get_gateway_url( ){
		
		if( get_option( 'ec_option_cardinal_test_mode' ) )
			return "https://centineltest.cardinalcommerce.com/maps/txns.asp";
		else
			return "https://centinel1000.cardinalcommerce.com/maps/txns.asp";
			
	}
	
	function handle_lookup_response( $response ){
		
		$response_body = $response['body'];
		
		$xml = new SimpleXMLElement( $response_body );
		
		$error_number 				= $xml->ErrorNo;
		$error_description 			= $xml->ErrorDesc;
		$transaction_id 			= $xml->TransactionId;
		$enrolled 					= $xml->Enrolled;
		$to_url 					= $xml->ACSUrl;
		$payload 					= $xml->Payload;
		
		$this->mysqli->insert_response( $this->order_id, 0, "Cardinal 3DS Lookup", print_r( $response, true ) );
		
		if( $error_number != "0" ){ // An Error Occurred
			return "ERROR";
			
		}else{
			if( $enrolled == "Y" ){
				//$this->add_customer_to_vault( );
				$this->process_send_to_verification( $payload, $to_url, $transaction_id );
			
			}else if( $enrolled == "N" || $enrolled == "U" ){
				return "NO3DS";
			
			}
		}
			
	}
	
	function process_send_to_verification( $payload, $to_url, $transaction_id ){
		
		// Create new form with CC + room for 3d elements
		echo "<form name='3dsfinalform' id='3dsfinalform' method=\"POST\" action=\"" . $this->cart_page . $this->permalink_divider . "ec_page=3dsprocess\">";
		echo "<input type=\"hidden\" name=\"ec_card_number\" value=\"" . $this->credit_card->card_number . "\" />";
		echo "<input type=\"hidden\" name=\"ec_expiration_month\" value=\"" . $this->credit_card->expiration_month . "\" />";
		echo "<input type=\"hidden\" name=\"ec_expiration_year\" value=\"" . $this->credit_card->get_expiration_year( 4 ) . "\" />";
		echo "<input type=\"hidden\" name=\"ec_security_code\" value=\"" . $this->credit_card->security_code . "\" />";
		echo "<input type=\"hidden\" name=\"order_id\" value=\"" . $this->order_id . "\" />";
		echo "<input type=\"hidden\" name=\"cavv\" id=\"ec_cavv\" value=\"\" />";
		echo "<input type=\"hidden\" name=\"eci\" id=\"ec_eci\" value=\"\" />";
		echo "<input type=\"hidden\" name=\"xid\" id=\"ec_xid\" value=\"\" />";
		echo "<input type=\"hidden\" name=\"paresstatus\" id=\"ec_paresstatus\" value=\"\" />";
		echo "<input type=\"hidden\" name=\"verified\" id=\"ec_verified\" value=\"\" />";
		echo "</form>";
		
		// Create iFrame
		echo "<iframe width='100%' height='100%' id='ec_3dsframe' style='border:none !important;'></iframe>";
		$iframe_content = "<HTML>";
		$iframe_content .= "<BODY onload=\"document.ec_3dsform.submit( );\">";
		$iframe_content .= "<FORM name=\"ec_3dsform\" id=\"ec_3dsform\" method=\"POST\" action=\"" . $to_url . "\">";
		$iframe_content .= "<input type=\"hidden\" name=\"PaReq\" value=\"" . $payload . "\">";
		$iframe_content .= "<input type=\"hidden\" name=\"TermUrl\" value=\"" . $this->cart_page . $this->permalink_divider . "ec_page=3ds&order_id=" . $this->order_id . "\">";
		$iframe_content .= "<input type=\"hidden\" name=\"MD\" value=\"" . $transaction_id . "\">";
		$iframe_content .= "</FORM>";
		$iframe_content .= "</BODY>";
		$iframe_content .= "</HTML>";
		echo "<script>";
		echo "var doc = document.getElementById( 'ec_3dsframe' ).contentWindow.document;";
		echo "doc.open( );";
		echo "doc.write('" . $iframe_content . "');";
		echo "doc.close( );";
		echo "</script>";
		die( );
		
	}
	
	function handle_authenticate_response( $response ){
		
		$response_body = $response['body'];
		
		$xml = new SimpleXMLElement( $response_body );
		
		$error_number 				= $xml->ErrorNo;
		$error_description 			= $xml->ErrorDesc;
		$result_status 				= $xml->PAResStatus;
		$sig_verification 			= $xml->SignatureVerification;
		$cavv 						= $xml->Cavv;
		$eci_flag 					= $xml->EciFlag;
		$xid 						= $xml->Xid;
		
		$this->mysqli->insert_response( $this->order_id, 0, "Cardinal 3DS Authenticate", print_r( $response, true ) );
		
		if( $error_number != "0" ){ // An Error Occurred
		
			return false;
		
		}else{
			
			if( ( $result_status == "Y" && $sig_verification == "Y" ) || 
				( $result_status == "U" && $sig_verification == "Y" ) ||
				( $result_status == "A" && $sig_verification == "Y" ) 
			){
				return (object) array( "order_id" => $this->order_id, "cavv" => $cavv, "eci" => $eci_flag, "xid" => $xid, "paresstatus" => $result_status, "verified" => $sig_verification );
			
			}else{
				return false;
			}
			
		}
		
	}
	
}

?>