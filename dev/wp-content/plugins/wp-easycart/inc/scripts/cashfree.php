<?php
if( !isset( $_POST['orderId'] ) ){
	header('Allow: POST', true, 405);
    die( );
}

if( !isset( $_POST['orderAmount'] ) ){
	header('Allow: POST', true, 405);
    die( );
}

if( !isset( $_POST['referenceId'] ) ){
	header('Allow: POST', true, 405);
    die( );
}

if( !isset( $_POST['txStatus'] ) ){
	header('Allow: POST', true, 405);
    die( );
}

if( !isset( $_POST['paymentMode'] ) ){
	header('Allow: POST', true, 405);
    die( );
}

//Load Wordpress Connection Data
define( 'WP_USE_THEMES', false );
require( '../../../../../wp-load.php' );

global $wpdb;
$mysqli = new ec_db( );

// Log the request
$mysqli->insert_response( 0, 0, "Cashfree Notify URL Response", print_r( $_POST, true ) );
	
// Get the order status to confirm
$app_id = get_option( 'ec_option_cashfree_app_id' );
$secret_key = get_option( 'ec_option_cashfree_secret' );
$currency = get_option( 'ec_option_cashfree_currency' );
$test_mode = get_option( 'ec_option_cashfree_testmode' );

$gateway_url = ( $test_mode == '1' ) ? 'https://test.cashfree.com/api/v1/order/info/status' : 'https://cashfree.com/api/v1/order/info/status';

$order_id = $_POST['orderId'];
$order_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_order WHERE order_id = %d", $order_id ) );

if( !$order_row ){
	header('Allow: POST', true, 405);
    die( );
}

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
	$this->mysqli->insert_response( 0, 1, "Cashfree Order Status CURL ERROR", 'error url: ' . $gateway_url . ', ' . curl_error( $ch ) );
else
	$this->mysqli->insert_response( 0, 0, "Cashfree Order Status Response", 'gateway url: ' . $gateway_url . ', ' . print_r( $response, true ) );

curl_close ($ch);

$json = json_decode( $response );

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
die( );
?>