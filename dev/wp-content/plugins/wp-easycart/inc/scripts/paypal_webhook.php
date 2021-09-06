<?php
//Load Wordpress Connection Data
define( 'WP_USE_THEMES', false );
require( '../../../../../wp-load.php' );

// Init DB References
global $wpdb;
$ec_db_admin = new ec_db_admin( );

$body = @file_get_contents('php://input');
$json = json_decode( $body );


// Payment was voided
if( $json->event_type == 'PAYMENT.AUTHORIZATION.VOIDED' ){
	$paypal_payment_id = $json->resource->parent_payment;
	$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM ec_order WHERE gateway_transaction_id = %s", $paypal_order_id ) );
	if( !$order_id ){
		die( );
	}
	$ec_db_admin->insert_response( $order_id, 0, "PayPal Webhook VOIDED Response", print_r( $json, true ) );
	
	$ec_db_admin->update_order_status( $order_id, "19" );
	
// Order Processed
}else if( $json->event_type == 'CHECKOUT.ORDER.PROCESSED' || ( $json->event_type == 'PAYMENT.SALE.COMPLETED' && $json->resource->payment_mode == 'ECHECK' ) ){
	$paypal_order_id = $json->resource->id;
	$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM ec_order WHERE gateway_transaction_id = %s", $paypal_order_id ) );
	if( !$order_id ){
		die( );
	}
	
	$order_row = $ec_db_admin->get_order_row_admin( $order_id );
	$orderdetails = $ec_db_admin->get_order_details_admin( $order_id );
	$ec_db_admin->insert_response( $order_id, 0, "PayPal Webhook Complete Response", print_r( $json, true ) . " --- " . print_r( $order_row, true ) );
	if( $order_row ){
		// Update Order Gateway ID From Order to Payment (used on refunds)
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "UPDATE ec_order SET gateway_transaction_id = %s WHERE order_id = %d", $json->resource->payment_details->payment_id, $order_id ) );
		
		/* Update Stock Quantity */
		foreach( $orderdetails as $orderdetail ){
			$product = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.* FROM ec_product WHERE ec_product.product_id = %d", $orderdetail->product_id ) );
			if( $product ){
				if( $product->use_optionitem_quantity_tracking )	
					$ec_db_admin->update_quantity_value( $orderdetail->quantity, $orderdetail->product_id, $orderdetail->optionitem_id_1, $orderdetail->optionitem_id_2, $orderdetail->optionitem_id_3, $orderdetail->optionitem_id_4, $orderdetail->optionitem_id_5 );
				$ec_db_admin->update_product_stock( $orderdetail->product_id, $orderdetail->quantity );
			}
		}
		
		// Update Order Status to Paid
		$ec_db_admin->update_order_status( $order_id, "10" );
		do_action( 'wpeasycart_order_paid', $order_id );
		
		// send email
		$order_display = new ec_orderdisplay( $order_row, true, true );
		$order_display->send_email_receipt( );
		$order_display->send_gift_cards( );
	}
	
// Payment was Refunded
}else if( $json->event_type == 'PAYMENT.CAPTURE.REFUNDED' || $json->event_type == 'PAYMENT.SALE.REFUNDED' ){
	$paypal_sale_id = $json->resource->sale_id;
	$paypal_payment_id = $json->resource->parent_payment;
	$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM ec_order WHERE gateway_transaction_id = %s OR gateway_transaction_id = %s", $paypal_payment_id, $paypal_sale_id ) );
	if( !$order_id ){
		die( );
	}
	$ec_db_admin->insert_response( $order_id, 0, "PayPal Webhook REFUNDED Response", print_r( $json, true ) );
	
	$order = $wpdb->get_row( $wpdb->prepare( "SELECT orderstatus_id, refund_total, grand_total FROM ec_order WHERE order_id = %d", $order_id ) );
	$order_status = $order->orderstatus_id;
	
	if( $order_status != 16 && $order_status != 17 ){
		$original_amount = (float) $order->grand_total;
		$refund_total = (float) $order->refund_total + (float) $json->resource->amount->total;
		$order_status = ( $refund_total < $original_amount ) ? 17 : 16;
		$wpdb->query( $wpdb->prepare( "UPDATE ec_order SET orderstatus_id = %d, refund_total = %s WHERE order_id = %d", $order_status, $refund_total, $order_id ) );
		
		if( $order_status == "16" )
			do_action( 'wpeasycart_full_order_refund', $orderid );
		else if( $order_status == "17" )
			do_action( 'wpeasycart_partial_order_refund', $orderid );
	}

// Payment was Denied
}else if( $json->event_type == 'PAYMENT.CAPTURE.DENIED' || $json->event_type == 'PAYMENT.SALE.DENIED' ){
	$paypal_sale_id = $json->resource->sale_id;
	$paypal_payment_id = $json->resource->parent_payment;
	$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM ec_order WHERE gateway_transaction_id = %s OR gateway_transaction_id = %s", $paypal_payment_id, $paypal_sale_id ) );
	if( !$order_id ){
		die( );
	}
	$ec_db_admin->insert_response( $order_id, 0, "PayPal Webhook DENIED Response", print_r( $json, true ) );
	$ec_db_admin->update_order_status( $order_id, "7" );

// Payment Pending
}else if( $json->event_type == 'PAYMENT.CAPTURE.PENDING' || $json->event_type == 'PAYMENT.SALE.PENDING' ){
	$paypal_sale_id = $json->resource->id;
	$paypal_payment_id = $json->resource->parent_payment;
	$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM ec_order WHERE gateway_transaction_id = %s OR gateway_transaction_id = %s", $paypal_payment_id, $paypal_sale_id ) );
	if( !$order_id ){
		die( );
	}
	$ec_db_admin->insert_response( $order_id, 0, "PayPal Webhook PENDING Response", print_r( $json, true ) );
	$ec_db_admin->update_order_status( $order_id, "8" );

// Payment Reversed
}else if( $json->event_type == 'PAYMENT.CAPTURE.REVERSED' || $json->event_type == 'PAYMENT.SALE.REVERSED' ){
	$paypal_sale_id = $json->resource->sale_id;
	$paypal_payment_id = $json->resource->parent_payment;
	$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM ec_order WHERE gateway_transaction_id = %s OR gateway_transaction_id = %s", $paypal_payment_id, $paypal_sale_id ) );
	if( !$order_id ){
		die( );
	}
	$ec_db_admin->insert_response( $order_id, 0, "PayPal Webhook REVERSED Response", print_r( $json, true ) );
	$ec_db_admin->update_order_status( $order_id, "9" );
	
}else{
	$ec_db_admin->insert_response( 0, 0, "PayPal Webhook", 'No event type match! ---- ' . print_r( $json, true ) );
}