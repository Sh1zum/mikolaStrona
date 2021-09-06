<?php
class ec_redsys extends ec_third_party{
	
	public function display_form_start( ){
		
		$merchant_code 						= get_option( 'ec_option_redsys_merchant_code' );
		$terminal 							= get_option( 'ec_option_redsys_terminal' );
		$currency 							= get_option( 'ec_option_redsys_currency' );
		$secret_key 						= get_option( 'ec_option_redsys_key' );
		$test_mode							= get_option( 'ec_option_redsys_test_mode' );
		
		$environment = "live";
		if( $test_mode )
			$environment = "test";
		
		$order_val = $this->order_id;
		if( strlen( $order_val ) == 1 ){
			$order_val = "000" . $order_val;
		}else if( strlen( $order_val ) == 2 ){
			$order_val = "00" . $order_val;
		}else if( strlen( $order_val ) == 3 ){
			$order_val = "0" . $order_val;
		}
		
		$chars = array( "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z" );
		$order_val = $order_val . $chars[rand(0, 25)] . $chars[rand(0, 25)] . $chars[rand(0, 25)];
		
		try{
	
			$redsys = new Tpv( );
			$redsys->setAmount( $this->order->grand_total );
			$redsys->setOrder( $order_val );
			$redsys->setMerchantcode( $merchant_code ); //Reemplazar por el código que proporciona el banco
			$redsys->setCurrency( $currency );
			$redsys->setTransactiontype( '0' );
			$redsys->setTerminal( $terminal );
			$redsys->setMethod( 'C' ); //Solo pago con tarjeta, no mostramos iupay
			$redsys->setNotification( plugins_url( EC_PLUGIN_DIRECTORY . "/inc/scripts/redsys_success.php" ) ); //Url de notificacion
			$redsys->setUrlOk( $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order_id ); //Url de notificacion
			$redsys->setVersion( 'HMAC_SHA256_V1' );
			$redsys->setEnviroment( $environment ); //Entorno test
	
			$signature = $redsys->generateMerchantSignature( $secret_key );
			$redsys->setMerchantSignature( $signature );
	
			$form = $redsys->createForm();
			
		}
		catch(Exception $e){
			echo $e->getMessage();
		}
		
		echo $form;
		
	}
	
	public function display_auto_forwarding_form( ){
		
		$merchant_code 						= get_option( 'ec_option_redsys_merchant_code' );
		$terminal 							= get_option( 'ec_option_redsys_terminal' );
		$currency 							= get_option( 'ec_option_redsys_currency' );
		$secret_key 						= get_option( 'ec_option_redsys_key' );
		$test_mode							= get_option( 'ec_option_redsys_test_mode' );
		
		$environment = "live";
		if( $test_mode )
			$environment = "test";
		
		$order_val = $this->order_id;
		if( strlen( $order_val ) == 1 ){
			$order_val = "000" . $order_val;
		}else if( strlen( $order_val ) == 2 ){
			$order_val = "00" . $order_val;
		}else if( strlen( $order_val ) == 3 ){
			$order_val = "0" . $order_val;
		}
		
		$chars = array( "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z" );
		$order_val = $order_val . $chars[rand(0, 25)] . $chars[rand(0, 25)] . $chars[rand(0, 25)];
		
		try{
	
			$redsys = new Tpv( );
			$redsys->setAmount( $this->order->grand_total );
			$redsys->setOrder( $order_val );
			$redsys->setMerchantcode( $merchant_code ); //Reemplazar por el código que proporciona el banco
			$redsys->setCurrency( $currency );
			$redsys->setTransactiontype( '0' );
			$redsys->setTerminal( $terminal );
			$redsys->setMethod( 'C' ); //Solo pago con tarjeta, no mostramos iupay
			$redsys->setNotification( plugins_url( EC_PLUGIN_DIRECTORY . "/inc/scripts/redsys_success.php" ) ); //Url de notificacion
			$redsys->setUrlOk( $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order_id ); //Url de notificacion
			$redsys->setVersion( 'HMAC_SHA256_V1' );
			$redsys->setEnviroment( $environment ); //Entorno test
	
			$signature = $redsys->generateMerchantSignature( $secret_key );
			$redsys->setMerchantSignature( $signature );
	
			$form = $redsys->createForm();
			
		}
		catch(Exception $e){
			echo $e->getMessage();
		}
		
		echo "<style>
		.ec_third_party_submit_button{ width:100%; text-align:center; }
		.ec_third_party_submit_button > input{ margin-top:150px; width:300px; height:45px; background-color:#38E; color:#FFF; font-weight:bold; text-transform:uppercase; border:1px solid #A2C0D8; cursor:pointer; }
		.ec_third_party_submit_button > input:hover{ background-color:#7A99BF; }
		.ec_third_party_loader{ display:block !important; position:absolute; top:50%; left:50%; }
		@-webkit-keyframes ec_third_party_loader {
		  0% {
			-webkit-transform: rotate(0deg);
			-moz-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		  }
		
		  100% {
			-webkit-transform: rotate(360deg);
			-moz-transform: rotate(360deg);
			-ms-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		  }
		}
		
		@-moz-keyframes ec_third_party_loader {
		  0% {
			-webkit-transform: rotate(0deg);
			-moz-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		  }
		
		  100% {
			-webkit-transform: rotate(360deg);
			-moz-transform: rotate(360deg);
			-ms-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		  }
		}
		
		@-o-keyframes ec_third_party_loader {
		  0% {
			-webkit-transform: rotate(0deg);
			-moz-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		  }
		
		  100% {
			-webkit-transform: rotate(360deg);
			-moz-transform: rotate(360deg);
			-ms-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		  }
		}
		
		@keyframes ec_third_party_loader {
		  0% {
			-webkit-transform: rotate(0deg);
			-moz-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		  }
		
		  100% {
			-webkit-transform: rotate(360deg);
			-moz-transform: rotate(360deg);
			-ms-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		  }
		}
		
		/* Styles for old versions of IE */
		.ec_third_party_loader {
		  font-family: sans-serif;
		  font-weight: 100;
		}
		
		/* :not(:required) hides this rule from IE9 and below */
		.ec_third_party_loader:not(:required) {
		  -webkit-animation: ec_third_party_loader 1250ms infinite linear;
		  -moz-animation: ec_third_party_loader 1250ms infinite linear;
		  -ms-animation: ec_third_party_loader 1250ms infinite linear;
		  -o-animation: ec_third_party_loader 1250ms infinite linear;
		  animation: ec_third_party_loader 1250ms infinite linear;
		  border: 8px solid #3388ee;
		  border-right-color: transparent;
		  border-radius: 16px;
		  box-sizing: border-box;
		  display: inline-block;
		  position: relative;
		  overflow: hidden;
		  text-indent: -9999px;
		  width: 32px;
		  height: 32px;
		}
		</style>";
		
		echo "<div style=\"display:none;\" class=\"ec_third_party_loader\">Loading...</div>";
		
		echo $form;
		
		echo "<SCRIPT>document.getElementById( 'ec_third_party_submit_payment' ).style.display = 'none';</SCRIPT>";
		echo "<SCRIPT data-cfasync=\"false\" LANGUAGE=\"Javascript\">document.ec_redsys_standard_auto_form.submit();</SCRIPT>";
	}
	
	private function arrayToJson( $data ){
        return json_encode( $data );
    }
	
    private function JsonToArray( $data ){
        return json_decode( $data, true );
    }
	
    private function hmac256( $data, $key ){
        $sha256 = hash_hmac( 'sha256', $data, $key, true );
        return $sha256;
    }
	
    private function encrypt_3DES( $data, $key ){
        $iv = "\0\0\0\0\0\0\0\0";
        $ciphertext = mcrypt_encrypt( MCRYPT_3DES, $key, $data, MCRYPT_MODE_CBC, $iv );
        return $ciphertext;
    }
	
    private function decodeParameters( $data ){
        $decode = base64_decode(strtr($data, '-_', '+/'));
        return $decode;
    }
    
	private function priceToSQL( $price ){
        $price = preg_replace( '/[^0-9\.,]*/i', '', $price );
        $price = str_replace( ',', '.', $price );
        if( substr( $price, -3, 1 ) == '.' ){
            $price = explode( '.', $price );
            $last = array_pop( $price );
            $price = join( $price, '' ) . '.' . $last;
        }else{
            $price = str_replace('.', '', $price);
        }
        return $price;
    }
	
    private function convertNumber( $price ){
        $number=number_format( str_replace( ',', '.', $price ), 2, '.', '' );
        return $number;
    }
	
    private function base64_url_encode( $input ){
        return strtr( base64_encode( $input ), '+/', '-_');
    }
	
    private function encodeBase64( $data ){
        $data = base64_encode( $data );
        return $data;
    }
	
    private function base64_url_decode( $input ){
        return base64_decode( strtr( $input, '-_', '+/' ) );
    }
	
    private function decodeBase64( $data ){
        $data = base64_decode($data);
        return $data;
    }
	
}
?>