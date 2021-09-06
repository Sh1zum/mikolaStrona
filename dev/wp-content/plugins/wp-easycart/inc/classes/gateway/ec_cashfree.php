<?php
class ec_cashfree extends ec_third_party{
	
	public function get_order_url( ){
		$app_id = get_option( 'ec_option_cashfree_app_id' );
		$secret_key = get_option( 'ec_option_cashfree_secret' );
		$currency = get_option( 'ec_option_cashfree_currency' );
		$test_mode = get_option( 'ec_option_cashfree_testmode' );
		
		$gateway_url = ( $test_mode == '1' ) ? 'https://test.cashfree.com/api/v1/order/create' : 'https://api.cashfree.com/api/v1/order/create';
		
		$order_id = $this->order_id;
		$total = number_format( $this->order->grand_total, 0, '', '' );
		
		$data = array(
			appId					=> $app_id,
			secretKey				=> $secret_key,
			orderId					=> $order_id,
			orderAmount				=> $total,
			orderCurrency			=> $currency,
			customerName			=> htmlspecialchars( $this->order->billing_first_name . ' ' . $this->order->billing_last_name, ENT_QUOTES ),
			customerPhone			=> htmlspecialchars( $this->order->billing_phone, ENT_QUOTES ),
			customerEmail			=> htmlspecialchars( $this->order->user_email, ENT_QUOTES ),
			returnUrl				=> htmlspecialchars( $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order_id . '&cashfree=return', ENT_QUOTES ),
			notifyUrl				=> htmlspecialchars( plugins_url( '/wp-easycart/inc/scripts/cashfree.php' ), ENT_QUOTES )
		);
		
		$headr = array();
		$headr[] = 'Cache-Control: no-cache';
		$headr[] = 'Content-Type: application/x-www-form-urlencoded';
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt($ch, CURLOPT_POST, true ); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query ( $data ) );
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		if( $response === false )
			$this->mysqli->insert_response( 0, 1, "Cashfree CURL ERROR", 'error url: ' . $gateway_url . ', ' . curl_error( $ch ) );
		else
			$this->mysqli->insert_response( 0, 0, "Cashfree Get URL Response", 'gateway url: ' . $gateway_url . ', ' . print_r( $response, true ) );
		
		curl_close ($ch);
		
		$json = json_decode( $response );
		
		if( $json->status != 'OK' ){
			echo '<h1>There is a problem with the Cashfree setup, contact the merchant for help. If you are the merchant, contact WP EasyCart for help!'; die( );
		}
		
		return $json->paymentLink;
	}
	
	public function display_form_start( ){
		$link = $this->get_order_url( );
		echo "<form action=\"" . $link . "\" method=\"get\">";
	}
	
	public function display_auto_forwarding_form( ){
		$link = $this->get_order_url( );
		echo "<form action=\"" . $link . "\" method=\"get\" name=\"ec_cashfree_auto_form\">";
		echo "</form>";
		echo "<SCRIPT LANGUAGE=\"Javascript\">document.ec_cashfree_auto_form.submit();</SCRIPT>";
	}
	
}

add_action( 'init', 'wp_easycart_cashfree_order_test' );
function wp_easycart_cashfree_order_test( ){
	if( !isset( $_GET['cashfree'] ) )
		return;
	
	if( !isset( $_GET['order_id'] ) )
		return;
	
	if( $_GET['cashfree'] != 'return' )
		return;
	
	global $wpdb;
	
	$mysqli = new ec_db( );
	$order_id = (int) $_GET['order_id'];
	$order_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_order WHERE order_id = %d", $order_id ) );
	
	if( !$order_row ){
		return;
	}

	$app_id = get_option( 'ec_option_cashfree_app_id' );
	$secret_key = get_option( 'ec_option_cashfree_secret' );
	$currency = get_option( 'ec_option_cashfree_currency' );
	$test_mode = get_option( 'ec_option_cashfree_testmode' );

	$gateway_url = ( $test_mode == '1' ) ? 'https://test.cashfree.com/api/v1/order/info/status' : 'https://api.cashfree.com/api/v1/order/info/status';

	$data = array(
		appId					=> $app_id,
		secretKey				=> $secret_key,
		orderId					=> $order_id,
	);

	$headr = array();
	$headr[] = 'Cache-Control: no-cache';
	$headr[] = 'Content-Type: application/x-www-form-urlencoded';

	$ch = curl_init( );
	curl_setopt($ch, CURLOPT_URL, $gateway_url );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
	curl_setopt($ch, CURLOPT_POST, true ); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query ( $data ) );
	curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
	curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
	$response = curl_exec($ch);
	if( $response === false )
		$mysqli->insert_response( 0, 1, "Cashfree Order Status CURL ERROR", 'error url: ' . $gateway_url . ', ' . curl_error( $ch ) );
	else
		$mysqli->insert_response( 0, 0, "Cashfree Order Status Response", 'gateway url: ' . $gateway_url . ', ' . print_r( $response, true ) );

	curl_close ($ch);

	$json = json_decode( $response );
	$cartpage = new ec_cartpage( );
	
	if( $json->status != 'OK' || $json->txStatus == 'FAILED' || $json->orderStatus == 'ACTIVE' ){
		$mysqli->remove_order( $order_id );
		wp_redirect( $cartpage->cart_page . $cartpage->permalink_divider . "ec_page=checkout_payment" );
		exit;
	}
    
    if( $json->status == 'OK' && $json->orderStatus == 'PAID' ){
        $mysqli->update_order_status( $order_id, "10" );
        do_action( 'wpeasycart_order_paid', $order_id );

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
                $db_admin->update_product_stock( $orderdetail->product_id, $orderdetail->quantity );
            }
        }

        $order_display = new ec_orderdisplay( $order_row, true, true );
        $order_display->send_email_receipt( );
        $order_display->send_gift_cards( );
    }
	
	$ec_db_admin = new ec_db_admin( );
	$ec_db_admin->clear_tempcart( $GLOBALS['ec_cart_data']->ec_cart_id );
	$cartpage->order->clear_session( );
}
?>