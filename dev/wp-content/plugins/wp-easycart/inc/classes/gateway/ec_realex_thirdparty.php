<?php
class ec_realex_thirdparty extends ec_third_party{
	
	public function init_hpp( $grand_total ){
		$realex_merchant_id = get_option( 'ec_option_realex_thirdparty_merchant_id' );
		$realex_secret = get_option( 'ec_option_realex_thirdparty_secret' );
		$realex_currency = get_option( 'ec_option_realex_thirdparty_currency' );
		$realex_total = number_format( $grand_total * 100, 0, '', '' );
		$realex_order_id = rand( 10000000, 999999999 );
		$realex_timestamp = $this->get_timestamp( );
		$realex_sha1hash = $this->get_secret_hash( $realex_timestamp, $realex_order_id );
		$realex_account = "internet";
		if( get_option( 'ec_option_realex_thirdparty_account' ) != '' ){
			$realex_account =  get_option( 'ec_option_realex_thirdparty_account' );
		}
		
		$hppRequest = array(
			"MERCHANT_ID"		=> $realex_merchant_id,
			"ACCOUNT"			=> $realex_account,
			"ORDER_ID"			=> $realex_order_id,
			"AMOUNT"			=> $realex_total,
			"CURRENCY"			=> $realex_currency,
			"TIMESTAMP"			=> $realex_timestamp,
			"SHA1HASH"			=> $realex_sha1hash,
			"AUTO_SETTLE_FLAG" 	=> TRUE
		);
		return json_encode( 
			array( 
				"response" => $hppRequest, 
				"order_id" => $realex_order_id
			)
		);
	}
	
	public function get_timestamp( ){
		return strftime("%Y%m%d%H%M%S");
	}
	
	public function get_secret_hash( $realex_timestamp, $realex_order_id ){
		$realex_merchant_id = get_option( 'ec_option_realex_thirdparty_merchant_id' );
		$realex_secret = get_option( 'ec_option_realex_thirdparty_secret' );
		$realex_currency = get_option( 'ec_option_realex_thirdparty_currency' );
		$realex_total = number_format( $GLOBALS['ec_order_grand_total' ] * 100, 0, '', '' );
		
		mt_srand((double)microtime()*1000000);
		
		$realex_order_id = $this->order_id;
		$realex_total = number_format( $realex_total * 100, 0, '', '' );
		
		$tmp = "$realex_timestamp.$realex_merchant_id.$realex_order_id.$realex_total.$realex_currency";
		
		$sha1hash = strtolower( sha1( $tmp ) );
		$tmp_sha1 = "$sha1hash.$realex_secret";
		$sha1hash = sha1($tmp_sha1);
		
		return $sha1hash;
	}
	
	public function display_form_start( ){
		$realex_merchant_id = get_option( 'ec_option_realex_thirdparty_merchant_id' );
		$realex_secret = get_option( 'ec_option_realex_thirdparty_secret' );
		$realex_currency = get_option( 'ec_option_realex_thirdparty_currency' );
		
		$realex_timestamp = strftime("%Y%m%d%H%M%S");
		mt_srand((double)microtime()*1000000);
		
		$realex_order_id = $this->order_id;
		$realex_total = number_format( $this->order->grand_total * 100, 0, '', '' );
		
		$tmp = "$realex_timestamp.$realex_merchant_id.$realex_order_id.$realex_total.$realex_currency";
		
		$md5hash = strtolower( md5( $tmp ) );
		$tmp_md5 = "$md5hash.$realex_secret";
		$md5hash = md5($tmp_md5);
		
		$sha1hash = strtolower( sha1( $tmp ) );
		$tmp_sha1 = "$sha1hash.$realex_secret";
		$sha1hash = sha1($tmp_sha1);
		
		$realex_account = "redirect";
		if( get_option( 'ec_option_realex_thirdparty_account' ) != '' ){
			$realex_account =  get_option( 'ec_option_realex_thirdparty_account' );
		}
		
		echo "<form action=\"https://epage.payandshop.com/epage.cgi\" method=\"post\">";
		echo "<input name=\"MERCHANT_ID\" id=\"cmd\" type=\"hidden\" value=\"" . $realex_merchant_id . "\" />";
		echo "<input name=\"ORDER_ID\" id=\"cmd\" type=\"hidden\" value=\"" . $realex_order_id . "\" />";
		echo "<input name=\"AMOUNT\" id=\"cmd\" type=\"hidden\" value=\"" . $realex_total . "\" />";
		echo "<input name=\"ACCOUNT\" id=\"cmd\" type=\"hidden\" value=\"" . $realex_account . "\" />";
		echo "<input name=\"CURRENCY\" id=\"cmd\" type=\"hidden\" value=\"" . $realex_currency . "\" />";
		echo "<input name=\"TIMESTAMP\" id=\"cmd\" type=\"hidden\" value=\"" . $realex_timestamp . "\" />";
		echo "<input name=\"MD5HASH\" id=\"cmd\" type=\"hidden\" value=\"" . $md5hash . "\" />";
		echo "<input name=\"SHA1HASH\" id=\"cmd\" type=\"hidden\" value=\"" . $sha1hash . "\" />";
		echo "<input name=\"AUTO_SETTLE_FLAG\" id=\"cmd\" type=\"hidden\" value=\"1\" />";
		echo "<input name=\"RETURN_TSS\" id=\"cmd\" type=\"hidden\" value=\"1\" />";
		echo "<input name=\"SHIPPING_CODE\" id=\"cmd\" type=\"hidden\" value=\"" . $this->order->shipping_zip . "\" />";
		echo "<input name=\"SHIPPING_CO\" id=\"cmd\" type=\"hidden\" value=\"" . $this->mysqli->get_country_code( $this->order->shipping_country ) . "\" />";
		echo "<input name=\"BILLING_CODE\" id=\"cmd\" type=\"hidden\" value=\"" . $this->order->billing_zip . "\" />";
		echo "<input name=\"BILLING_CO\" id=\"cmd\" type=\"hidden\" value=\"" . $this->mysqli->get_country_code( $this->order->billing_country ) . "\" />";
	}
	
	public function display_auto_forwarding_form( ){
		$realex_merchant_id = get_option( 'ec_option_realex_thirdparty_merchant_id' );
		$realex_secret = get_option( 'ec_option_realex_thirdparty_secret' );
		$realex_currency = get_option( 'ec_option_realex_thirdparty_currency' );
		
		$realex_timestamp = strftime("%Y%m%d%H%M%S");
		mt_srand((double)microtime()*1000000);
		
		$realex_order_id = $this->order_id;
		$realex_total = number_format( $this->order->grand_total * 100, 0, '', '' );
		
		$tmp = "$realex_timestamp.$realex_merchant_id.$realex_order_id.$realex_total.$realex_currency";
		
		$md5hash = md5($tmp);
		$tmp_md5 = "$md5hash.$realex_secret";
		$md5hash = md5($tmp_md5);
		
		$sha1hash = sha1($tmp);
		$tmp_sha1 = "$sha1hash.$realex_secret";
		$sha1hash = sha1($tmp_sha1);
		
		$realex_account = "redirect";
		if( get_option( 'ec_option_realex_thirdparty_account' ) != '' ){
			$realex_account =  get_option( 'ec_option_realex_thirdparty_account' );
		}
		
		echo "<form action=\"https://epage.payandshop.com/epage.cgi\" method=\"post\" name=\"ec_realex_thirdparty_auto_form\">";
		echo "<input name=\"MERCHANT_ID\" id=\"cmd\" type=\"hidden\" value=\"" . $realex_merchant_id . "\" />";
		echo "<input name=\"ORDER_ID\" id=\"cmd\" type=\"hidden\" value=\"" . $realex_order_id . "\" />";
		echo "<input name=\"AMOUNT\" id=\"cmd\" type=\"hidden\" value=\"" . $realex_total . "\" />";
		echo "<input name=\"ACCOUNT\" id=\"cmd\" type=\"hidden\" value=\"" . $realex_account . "\" />";
		echo "<input name=\"CURRENCY\" id=\"cmd\" type=\"hidden\" value=\"" . $realex_currency . "\" />";
		echo "<input name=\"TIMESTAMP\" id=\"cmd\" type=\"hidden\" value=\"" . $realex_timestamp . "\" />";
		echo "<input name=\"MD5HASH\" id=\"cmd\" type=\"hidden\" value=\"" . $md5hash . "\" />";
		echo "<input name=\"SHA1HASH\" id=\"cmd\" type=\"hidden\" value=\"" . $sha1hash . "\" />";
		echo "<input name=\"AUTO_SETTLE_FLAG\" id=\"cmd\" type=\"hidden\" value=\"1\" />";
		echo "<input name=\"RETURN_TSS\" id=\"cmd\" type=\"hidden\" value=\"1\" />";
		echo "<input name=\"SHIPPING_CODE\" id=\"cmd\" type=\"hidden\" value=\"" . $this->order->shipping_zip . "\" />";
		echo "<input name=\"SHIPPING_CO\" id=\"cmd\" type=\"hidden\" value=\"" . $this->mysqli->get_country_code( $this->order->shipping_country ) . "\" />";
		echo "<input name=\"BILLING_CODE\" id=\"cmd\" type=\"hidden\" value=\"" . $this->order->billing_zip . "\" />";
		echo "<input name=\"BILLING_CO\" id=\"cmd\" type=\"hidden\" value=\"" . $this->mysqli->get_country_code( $this->order->billing_country ) . "\" />";
		echo "</form>";
		echo "<SCRIPT LANGUAGE=\"Javascript\">document.ec_realex_thirdparty_auto_form.submit();</SCRIPT>";
	}
	
}

add_action( 'wp_ajax_ec_ajax_realex_hpp_init', 'ec_ajax_realex_hpp_init' );
add_action( 'wp_ajax_nopriv_ec_ajax_realex_hpp_init', 'ec_ajax_realex_hpp_init' );
function ec_ajax_realex_hpp_init( ){
	$realex = new ec_realex_thirdparty( );
	$result = $realex->init_hpp( $_POST['total'] );
	echo $result;
	die( );
}

add_action( 'wp_enqueue_scripts', 'ec_realex_hpp_load_scripts' );
function ec_realex_hpp_load_scripts( ){
	if( get_option( 'ec_option_realex_thirdparty_type' ) == 'hpp' && isset( $_GET['ec_page'] ) && $_GET['ec_page'] == 'checkout_payment' ){
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/rxp-js.min.js' ) ){
			wp_enqueue_script( 'wpeasycart_realexhpp_js', plugins_url( 'wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/rxp-js.min.js' ), array( 'jquery', 'wpeasycart_js' ), EC_CURRENT_VERSION, false );
		}else{
			wp_enqueue_script( 'wpeasycart_realexhpp_js', plugins_url( 'wp-easycart/design/theme/' . get_option( 'ec_option_latest_theme' ) . '/rxp-js.min.js' ), array( 'jquery', 'wpeasycart_js' ), EC_CURRENT_VERSION, false );
		}
	}
}
?>