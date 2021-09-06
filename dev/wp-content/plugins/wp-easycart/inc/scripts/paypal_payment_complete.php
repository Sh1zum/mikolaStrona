<?php
//Load Wordpress Connection Data
define( 'WP_USE_THEMES', false );
require( '../../../../../wp-load.php' );

if( !class_exists( 'ec_ipnlistener' ) ){
	include( '../classes/gateway/ec_ipnlistener.php' );
}


$mysqli = new ec_db_admin( );
$listener = new ec_ipnlistener( );

if( get_option( 'ec_option_paypal_use_sandbox' ) )
$listener->use_sandbox = true;

try{
	$listener->requirePostMethod( );
	$verified = $listener->processIpn( );
}catch( Exception $e ){
	$mysqli->insert_response( 0, 1, "PayPal Standard Exception", $e->getMessage( ) );
	exit( 0 );
}

if( $verified ) {

    $errmsg = '';
    
    if( strtolower( $_POST['receiver_email'] ) != strtolower( get_option( 'ec_option_paypal_email' ) ) && strtolower( $_POST['business'] ) != strtolower( get_option( 'ec_option_paypal_email' ) ) ) {
        $errmsg .= "'receiver_email' does not match: ";
        $errmsg .= $_POST['receiver_email']."\n";
    }
	
    if( $_POST['mc_currency'] != get_option( 'ec_option_paypal_currency_code' ) ) {
        $errmsg .= "'mc_currency' does not match: ";
        $errmsg .= $_POST['mc_currency']."\n";
    }
	
	$order_id = $_POST['custom'];
	
	// IF WE GET AN ERROR, THEN RESPONSE HAS ALREADY BEEN HANDLED!!
	$has_processed = $mysqli->get_response_from_order_id( $order_id );
	
	// If has been handled already the error value is === 0, not false, not 1, if either of those we need to still process.
	if( is_array( $has_processed ) && count( $has_processed ) > 0 ){
        $errmsg .= "'txn_id' is being processed twice: ";
        $errmsg .= $_POST['txn_id']."\n";
    }
		
    if( !empty( $errmsg ) ){
        $body = "IPN failed fraud checks: \n";
        $body .= $listener->getTextReport();
       	$mysqli->insert_response( 0, 1, "PayPal Standard", "Error Message: " . $errmsg . "\n------\n" .  $body );
    
	}else{
		
		$paypal_response_string = print_r( $_POST, true );
		
		if( $_POST['payment_status'] == 'Completed' ){
		
			$mysqli->insert_response( $order_id, 0, "PayPal", $paypal_response_string ); 
			
			if( $_POST['txn_type'] == "subscr_payment" ){
				
				if( $mysqli->has_subscription_inserted( $_POST['subscr_id'] ) ){
					
					// Update a subscription item
					$mysqli->update_paypal_subscription( $_POST['payment_date'], $_POST['subscr_id'] );
				
				}else{
					
					// Add a subscription item
					$order_row = $mysqli->get_order_row_admin( $order_id );
					
					$mysqli->insert_response( $order_id, 1, "PayPal Subscription", print_r( $order_row, true ) );
				
					$mysqli->insert_paypal_subscription( $_POST['item_name'], $_POST['payer_email'], $_POST['first_name'], $_POST['last_name'], $_POST['residence_country'], $_POST['mc_gross'], $_POST['payment_date'], $_POST['txn_id'], $_POST['txn_type'], $_POST['subscr_id'], $_POST['username'], $_POST['password'] );
				
					$mysqli->update_order_status( $order_id, "10" );
					do_action( 'wpeasycart_order_paid', $order_id );
					
					// send email
					$order_display = new ec_orderdisplay( $order_row, true, true );
					$order_display->send_email_receipt( );
					
				}
				
			}else if(  $_POST['txn_type'] == "subscr_signup" ){
				
				// Not a lot of useful information is passed here. We won't do anything.
				
			}else if( $_POST['txn_type'] == "subscr_cancel" ){
				
				// Canel a subscription item
				$mysqli->cancel_paypal_subscription( $_POST['subscr_id'] );
				
			}else{
				global $wpdb;
				$order_row = $mysqli->get_order_row_admin( $order_id );
				$orderdetails = $mysqli->get_order_details_admin( $order_id );
				if( $order_row ){
					$mysqli->update_order_status( $order_id, "10" );
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
			
		} else if( $_POST['payment_status'] == 'Refunded' ){ 
			$mysqli->update_order_status( $order_id, "16" );
			do_action( 'wpeasycart_full_order_refund', $order_id );
			
			// Check for gift card to refund
			global $wpdb;
			$order_details = $wpdb->get_results( $wpdb->prepare( "SELECT is_giftcard, giftcard_id FROM ec_orderdetail WHERE order_id = %d", $order_id ) );
			foreach( $order_details as $detail ){
				if( $detail->is_giftcard ){
					$wpdb->query( $wpdb->prepare( "DELETE FROM ec_giftcard WHERE ec_giftcard.giftcard_id = %s", $detail->giftcard_id ) );
				}
			}
			
		} else {
			$mysqli->update_order_status( $order_id, "8" );
		}
		
    }
    
}	
?>