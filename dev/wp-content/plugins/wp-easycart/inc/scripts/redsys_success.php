<?php
//Load Wordpress Connection Data
define( 'WP_USE_THEMES', false );
require( '../../../../../wp-load.php' );

global $wpdb;
$mysqli = new ec_db_admin( );

try{
	$redsys = new Tpv( );
	$key = get_option( 'ec_option_redsys_key' );
	
	$parameters = $redsys->getMerchantParameters( $_POST["Ds_MerchantParameters"] );
	$DsResponse = $parameters["Ds_Response"];
	$DsResponse += 0;
	if( $redsys->check( $key, $_POST ) && $DsResponse <= 99 ){
		$order_id = intval( substr( $parameters['Ds_Order'], 0, -3 ) );
		$response_code = intval( $parameters['Ds_Response'] );
		$mysqli->insert_response( $orderid, 0, "Redsys Success", $response_code . ", " . print_r( $parameters, true ) );
		
		
		if( $response_code <= 99 ){
			$mysqli->update_order_transaction_id( $order_id, $parameters['Ds_AuthorisationCode'] );
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
	} else {
		$mysqli->insert_response( 0, 1, "Redsys Failed", "response was invalid." );
	}
}
catch( Exception $e ){
	$mysqli->insert_response( 0, 1, "Redsys Try Failed", $e->getMessage( ) );
}
?>