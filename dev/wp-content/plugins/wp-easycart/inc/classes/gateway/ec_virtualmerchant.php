<?php

class ec_virtualmerchant extends ec_gateway{
	
	function get_gateway_data( ){
		
		$ssl_merchant_id 				= 		get_option( 'ec_option_virtualmerchant_ssl_merchant_id' );
		$ssl_user_id 					= 		get_option( 'ec_option_virtualmerchant_ssl_user_id' );
		$ssl_pin 						= 		get_option( 'ec_option_virtualmerchant_ssl_pin' );
		
		$tax_total = number_format( $this->order_totals->tax_total + $this->order_totals->duty_total + $this->order_totals->gst_total + $this->order_totals->pst_total + $this->order_totals->hst_total, 2, '.', '' );
		if( !$this->tax->vat_included )
			$tax_total = number_format( $tax_total + $this->order_totals->vat_total, 2, '.', '' );
			
		$stripped_postal_code =  $this->user->billing->zip;
		$stripped_postal_code = str_replace('-', '', $stripped_postal_code);
		$stripped_postal_code = str_replace(' ', '', $stripped_postal_code);

		
		$ssl_transaction_type 			= 		"ccsale";
		$ssl_show_form					=		"false";
		$ssl_result_format				=		"ASCII";
		$ssl_card_number				=		$this->credit_card->card_number;
		$ssl_exp_date					= 		$this->credit_card->expiration_month . $this->credit_card->get_expiration_year( 2 );
		$ssl_amount						=		number_format( $this->order_totals->grand_total, 2, '.', '' );
		$ssl_card_present				=		"N";
		$ssl_first_name					=		$this->user->billing->first_name;
		$ssl_last_name					=		$this->user->billing->last_name;
		$ssl_avs_zip					=		$stripped_postal_code;
		$ssl_avs_address				=		substr( $this->user->billing->address_line_1, 0, 30 );
		$ssl_cvv2cvc2					=		$this->credit_card->security_code;
		$ssl_cvv2cvc2_indicator			=		"1";
		$ssl_invoice_number				=		$this->order_id;
		$ssl_salestax					=		$tax_total;
		$ssl_transaction_currency 		= 		get_option( 'ec_option_virtualmerchant_currency' );
		
		$data = array(
			"ssl_merchant_id"			=> $ssl_merchant_id,
			"ssl_user_id"				=> $ssl_user_id,
			"ssl_pin"					=> $ssl_pin,
			"ssl_transaction_type"		=> $ssl_transaction_type,
			"ssl_show_form"				=> $ssl_show_form,
			"ssl_result_format"			=> $ssl_result_format,
			"ssl_card_number"			=> $ssl_card_number,
			"ssl_exp_date"				=> $ssl_exp_date,
			"ssl_amount"				=> $ssl_amount,
			"ssl_card_present"			=> $ssl_card_present,
			"ssl_first_name"			=> $ssl_first_name,
			"ssl_last_name"				=> $ssl_last_name,
			"ssl_avs_zip"				=> $ssl_avs_zip,
			"ssl_avs_address"			=> $ssl_avs_address,
			"ssl_cvv2cvc2"				=> $ssl_cvv2cvc2,
			"ssl_cvv2cvc2_indicator"	=> $ssl_cvv2cvc2_indicator,
			"ssl_invoice_number"		=> $ssl_invoice_number,
			"ssl_salestax"				=> $ssl_salestax
		);
		
		return $data;
	}
	
	function get_gateway_url( ){
		
		$is_demo_account = get_option( 'ec_option_virtualmerchant_demo_account' );
		
		if( $is_demo_account )							
			return "https://demo.myvirtualmerchant.com/VirtualMerchantDemo/process.do";
		else														
			return "https://www.myvirtualmerchant.com/VirtualMerchant/process.do";
	
	}
	
	function handle_gateway_response( $response ){
		
		$response = str_ireplace( array( "\r", "\n", '\r', '\n' ), '[ec_split]', $response['body'] );
		$keypairs_str = explode( '[ec_split]', $response );
		$keypairs = array( );
		foreach( $keypairs_str as $str ){
			$split = explode( "=", $str );
			$keypairs[$split[0]] = $split[1];
		}
		
		if( $keypairs['ssl_result'] == '0' )
			$this->is_success = true;
		else
			$this->is_success = false;
		
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "Virtual Merchant", print_r( $response, true ) );
		
		if( !$this->is_success )
			$this->error_message = $keypairs['ssl_result_message'];
			
	}
	
}

?>