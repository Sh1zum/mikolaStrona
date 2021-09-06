<?php
class ec_paypal_advanced extends ec_third_party{
	
	private $securetokenid;
	private $securetoken;
	
	
	public function display_iframe( $total ){
		
		$this->create_token( $total );
		echo '<iframe src="https://payflowlink.paypal.com?MODE=TEST&SECURETOKENID=' . $this->securetokenid . '&SECURETOKEN=' . $this->securetoken . '" name="test_iframe" scrolling="no" width="570px" height="540px"></iframe>';
	}
	
	private function get_mode( ){
		if( get_option( 'ec_option_paypal_advanced_test_mode' ) ){
			return "TEST";
		}else{
			return "LIVE";
		}
	}
	
	private function get_url( ){
		if( get_option( 'ec_option_paypal_advanced_test_mode' ) ){
			return "https://pilot-payflowpro.paypal.com";
		}else{
			return "https://payflowpro.paypal.com";
		}
	}
	
	private function create_token( $total ){
		$paypal_partner = get_option( 'ec_option_paypal_advanced_partner' );
		$paypal_vendor = get_option( 'ec_option_paypal_advanced_vendor' );
		$paypal_user = get_option( 'ec_option_paypal_advanced_user' );
		$paypal_pwd = get_option( 'ec_option_paypal_advanced_password' );
		$paypal_currency = get_option( 'ec_option_paypal_advanced_currency' );
		
		$this->securetokenid = $this->generate_token_id( );
		
		
		$gateway_data = array(  "PARTNER" 			=> $paypal_partner,
								"VENDOR"			=> $paypal_vendor,
								"USER"				=> $paypal_user,
								"PWD"				=> $paypal_pwd,
								"TRXTYPE"			=> "S",
								"AMT"				=> $total,
								"CURRENCY" 			=> $paypal_currency,
								"CREATESECURETOKEN"	=> "Y",
								"SECURETOKENID"		=> $this->securetokenid,
								
								"URLMETHOD"			=> "POST",
								"RETURNURL" 		=> $this->get_cart_page_url( ),
								"CANCELURL" 		=> $this->get_cart_page_url( ),
								"ERRORURL" 			=> $this->get_cart_page_url( ),
								
								"BILLTOFIRSTNAME" 	=> $GLOBALS['ec_user']->billing->first_name,
								"BILLTOLASTNAME" 	=> $GLOBALS['ec_user']->billing->last_name,
								"BILLTOSTREET" 		=> $GLOBALS['ec_user']->billing->address_line_1,
								"BILLTOCITY" 		=> $GLOBALS['ec_user']->billing->city,
								"BILLTOSTATE" 		=> $GLOBALS['ec_user']->billing->state,
								"BILLTOZIP" 		=> $GLOBALS['ec_user']->billing->zip,
								"BILLTOCOUNTRY" 	=> $GLOBALS['ec_user']->billing->country,
								
								"SHIPTOFIRSTNAME" 	=> $GLOBALS['ec_user']->shipping->first_name,
								"SHIPTOLASTNAME" 	=> $GLOBALS['ec_user']->shipping->last_name,
								"SHIPTOSTREET" 		=> $GLOBALS['ec_user']->shipping->address_line_1,
								"SHIPTOCITY" 		=> $GLOBALS['ec_user']->shipping->city,
								"SHIPTOSTATE" 		=> $GLOBALS['ec_user']->shipping->state,
								"SHIPTOZIP" 		=> $GLOBALS['ec_user']->shipping->zip,
								"SHIPTOCOUNTRY" 	=> $GLOBALS['ec_user']->shipping->country );
		
		$headr = array();
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $this->get_url( ) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt($ch, CURLOPT_POST, true ); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $gateway_data ) );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		curl_close ($ch);
		
		parse_str( $response, $response_array );
		$this->securetoken = $response_array['SECURETOKEN'];
		
	}
	
	private function generate_token_id( ){
		$string_options = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z' );
		$string = $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )] . $string_options[rand( 0, 35 )];
		return $string;
	}
	
	private function get_permalink_divider( $cart_page ){
		if( substr_count( $cart_page, '?' ) )					
			return "&";
		else														
			return "?";
	}
	
	private function get_cart_page_url( ){
		
		$cart_page_id = get_option('ec_option_cartpage');
		
		if( function_exists( 'icl_object_id' ) ){
			$cart_page_id = icl_object_id( $cart_page_id, 'page', true, ICL_LANGUAGE_CODE );
		}
		
		$cart_page = get_permalink( $cart_page_id );
		
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$cart_page = $https_class->makeUrlHttps( $cart_page );
		}
		return $cart_page;
	}
	
}
?>