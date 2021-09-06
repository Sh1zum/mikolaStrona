<?php
header( 'HTTP/1.0 200 OK' );
flush( );

//Load Wordpress Connection Data
define( 'WP_USE_THEMES', false );
require( '../../../../../wp-load.php' );

$mysqli = new ec_db_admin( );

// Get Environment URL
if( get_option( 'ec_option_payfast_sandbox' ) )
	$pfHost = "sandbox.payfast.co.za";
else
	$pfHost = "www.payfast.co.za";


$pfData = $_POST;
$order_id = $pfData['m_payment_id'];

// Strip any slashes in data
foreach( $pfData as $key => $val ){
    $pfData[$key] = stripslashes( $val );
}

// Construct variables
foreach( $pfData as $key => $val ){
    if( $key != 'signature' ){
        $pfParamString .= $key .'='. urlencode( $val ) .'&';
    }
}

// Remove the last '&' from the parameter string
$pfParamString = substr( $pfParamString, 0, -1 );
$pfTempParamString = $pfParamString;

// Passphrase stored in website database
$passPhrase = get_option( 'ec_option_payfast_passphrase' );

if( $passPhrase != '' ){
    $pfTempParamString .= '&passphrase='.urlencode( trim( $passPhrase ) );
}

$signature = md5( $pfTempParamString );

if( $signature != $pfData['signature'] ){
	$mysqli->insert_response( $order_id, 1, "PayFast ITN Error", print_r( $_POST, true ) );
    die( 'Invalid Signature' );
}

// Variable initialization
$validHosts = array(
    'www.payfast.co.za',
    'sandbox.payfast.co.za',
    'w1w.payfast.co.za',
    'w2w.payfast.co.za',
);

$validIps = array( );

foreach( $validHosts as $pfHostname ){
    $ips = gethostbynamel( $pfHostname );
    if( $ips !== false ){
        $validIps = array_merge( $validIps, $ips );
    }
}

// Remove duplicates
$validIps = array_unique( $validIps );

if( !in_array( $_SERVER['REMOTE_ADDR'], $validIps ) ){
	$mysqli->insert_response( $order_id, 1, "PayFast ITN IP Error", print_r( $_POST, true ) );
    die( 'Source IP not Valid' );
}

// This amount needs to be sourced from your application
$order_row = $mysqli->get_order_row_admin( $order_id );
$cartTotal = $order_row->grand_total;
if( abs( floatval( $cartTotal ) - floatval( $pfData['amount_gross'] ) ) > 0.01 ){
    $mysqli->insert_response( $order_id, 1, "PayFast ITN Amount Error", print_r( $_POST, true ) );
	die( 'Amounts Mismatch' );
}

// Variable initialization
$url = 'https://'. $pfHost .'/eng/query/validate';

// Create default cURL object
$ch = curl_init( );

// Set cURL options - Use curl_setopt for greater PHP compatibility
// Base settings
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_HEADER, false );      
curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 1 );

// Standard settings
curl_setopt( $ch, CURLOPT_URL, $url );
curl_setopt( $ch, CURLOPT_POST, true );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $pfParamString );

// Execute CURL
$response = curl_exec( $ch );
curl_close( $ch );

$lines = explode( "\r\n", $response );
$verifyResult = trim( $lines[0] );

if( strcasecmp( $verifyResult, 'VALID' ) != 0 ){
    $mysqli->insert_response( $order_id, 1, "PayFast ITN Data Error", print_r( $response, true ) . " ---- " . print_r( $_POST, true ) );
	die('Data not valid');
}

// Valid, now insert response
$has_processed = $mysqli->get_response_from_order_id( $order_id );

$pfPaymentId = $pfData['pf_payment_id'];

if( $pfData ['payment_status'] == 'COMPLETE' ){
	$mysqli->insert_response( $order_id, 0, "PayFast ITN", print_r( $_POST, true ) );
	if( !is_array( $has_processed ) || count( $has_processed ) <= 0 ){
		global $wpdb;
		$orderdetails = $mysqli->get_order_details_admin( $order_id );
		if( $order_row ){
			$mysqli->update_order_status( $order_id, "10" );
			$mysqli->update_order_transaction_id( $order_id, $pfPaymentId );
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
}else{
	// If unknown status, do nothing (which is the safest course of action)
}

?>