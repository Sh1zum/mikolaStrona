<?php
//Load Wordpress Connection Data
define( 'WP_USE_THEMES', false );
require( '../../../../../wp-load.php' );

$mysqli = new ec_db( );

$body = @file_get_contents('php://input');
$json = json_decode( $body );

if( isset( $json->type ) && isset( $json->data ) ){

	$webhook_id = $json->id;
	$webhook_type = $json->type;
	$webhook_data = $json->data->object;
	
	
	$webhook = $mysqli->get_webhook( $webhook_id );
	
	if( !$webhook || $webhook_id == "evt_00000000000000" ){
		
		$mysqli->insert_webhook( $webhook_id, $webhook_type, $webhook_data );
		
		// Refund an Order
		if( $webhook_type == "charge.refunded" ){
			
			global $wpdb;
			$order_status = $wpdb->get_var( $wpdb->prepare( "SELECT ec_order.orderstatus_id FROM ec_order WHERE ec_order.stripe_charge_id = %s", $webhook_data->id ) );
			
			if( $order_status != 16 && $order_status != 17 ){
				// Refund order
				$stripe_charge_id = $webhook_data->id;
				$original_amount = $webhook_data->amount;
				
				$refunds = $webhook_data->refunds->data;
				$refund_total = 0;
				$order_status = 16;
				
				foreach( $refunds as $refund ){
					$refund_total = $refund_total + $refund->amount;
				}
				
				if( $refund_total < $original_amount ){
					$order_status = 17;
				}
				
				$mysqli->update_stripe_order_status( $stripe_charge_id, $order_status, ( $refund_total / 100 ) );
				
				if( $status == "16" )
					do_action( 'wpeasycart_full_order_refund', $orderid );
				else if( $status == "17" )
					do_action( 'wpeasycart_partial_order_refund', $orderid );
			}
		
		// Subscription Cancelled (manaually, by customer, or by failed payments)	
		}else if( $webhook_type == "customer.subscription.deleted" ){
			$stripe_subscription_id = $webhook_data->id;
			$subscription_row = $mysqli->get_stripe_subscription( $stripe_subscription_id );
			$subscription = new ec_subscription( $subscription_row );
			$mysqli->cancel_stripe_subscription( $stripe_subscription_id );
			$user = $mysqli->get_stripe_user( $webhook_data->customer );
			$subscription->send_subscription_ended_email( $user );
		
		// Subscription Trial is Ending in 3 Days	
		}else if( $webhook_type == "customer.subscription.trial_will_end" ){
			$stripe_subscription_id = $webhook_data->id;
			$subscription_row = $mysqli->get_stripe_subscription( $stripe_subscription_id );
			$subscription = new ec_subscription( $subscription_row );
			$subscription->send_subscription_trial_ending_email( );
		
		// Subscription Recurring Billing Succeeded	
		}else if( $webhook_type == "invoice.payment_succeeded" ){
			$payment_timestamp = $webhook_data->created;
			$stripe_subscription_id = $webhook_data->subscription;
			$stripe_charge_id = $webhook_data->charge;
			$subscription = $mysqli->get_stripe_subscription( $stripe_subscription_id );
			
			$mysqli->insert_response( 0, 1, "STRIPE Subscription", print_r( $webhook_data, true ) );
			
			if( $subscription && ( $subscription->last_payment_date + 10 ) >= $payment_timestamp ){
				$mysqli->update_stripe_order( $subscription->subscription_id, $stripe_charge_id );
			}else if( $subscription ){
				$user = $mysqli->get_stripe_user( $webhook_data->customer );
				$order_id = $mysqli->insert_stripe_order( $subscription, $webhook_data, $user );
				
				do_action( 'wpeasycart_subscription_paid', $order_id );
				do_action( 'wpeasycart_order_paid', $order_id );
				
				$db_admin = new ec_db_admin( );
				$order_row = $db_admin->get_order_row_admin( $order_id );
				$order = new ec_orderdisplay( $order_row, true, true );
				$order->send_email_receipt( );
				
				if( $subscription->payment_duration > 0 && $subscription->payment_duration <= $subscription->number_payments_completed + 1 ){
					// Used to cancel when payment duration reached
					$stripe = new ec_stripe( );
					$stripe->cancel_subscription( $user, $stripe_subscription_id );
					$mysqli->cancel_stripe_subscription( $stripe_subscription_id );
				}else{
					$mysqli->update_stripe_subscription( $stripe_subscription_id, $webhook_data );
				}
			}
		
		// Subscription Failed Payment	
		}else if( $webhook_type == "invoice.payment_failed" ){
			
			if( $webhook_data->billing_reason != 'subscription_create' ){
				$payment_timestamp = $webhook_data->date;
				$stripe_subscription_id = $webhook_data->subscription;
				$stripe_charge_id = $webhook_data->charge;
				$subscription = $mysqli->get_stripe_subscription( $stripe_subscription_id );
				
				$mysqli->insert_response( 0, 1, "STRIPE Subscription Failed", print_r( $subscription, true ) );
				
				if( $subscription ){
				
					$order_id = $mysqli->insert_stripe_failed_order( $subscription, $webhook_data );
					$mysqli->update_stripe_subscription_failed( $subscription_id, $webhook_data );
					
					$db_admin = new ec_db_admin( );
					$order_row = $db_admin->get_order_row_admin( $order_id );
					$order = new ec_orderdisplay( $order_row, true, true );
					
					$order->send_failed_payment( );
				}
			}
			
		// iDEAL now chargeable	
		}else if( $webhook_type == "source.chargeable" ){
			global $wpdb;
			$order = $wpdb->get_row( $wpdb->prepare( "SELECT order_id, grand_total FROM ec_order WHERE gateway_transaction_id = %s", $webhook_data->id . ':' . $webhook_data->client_secret ) );
			if( $order ){
				if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
					$stripe = new ec_stripe( );
				else
					$stripe = new ec_stripe_connect( );
				
				$order_totals = (object) array(
					"grand_total"	=> $order->grand_total
				);
				
				$response = $stripe->insert_charge( $order_totals, false, $webhook_data->id, $order->order_id, false );
				
				if( !isset( $response->error ) ){
					$wpdb->query( $wpdb->query( "" ) );
					/* Update Stock Quantity */
					$ec_db_admin = new ec_db_admin( );
					$order_row = $ec_db_admin->get_order_row_admin( $order->order_id );
					$orderdetails = $ec_db_admin->get_order_details_admin( $order->order_id );
					
					foreach( $orderdetails as $orderdetail ){
						$product = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.* FROM ec_product WHERE ec_product.product_id = %d", $orderdetail->product_id ) );
						if( $product ){
							if( $product->use_optionitem_quantity_tracking )	
								$ec_db_admin->update_quantity_value( $orderdetail->quantity, $orderdetail->product_id, $orderdetail->optionitem_id_1, $orderdetail->optionitem_id_2, $orderdetail->optionitem_id_3, $orderdetail->optionitem_id_4, $orderdetail->optionitem_id_5 );
							$ec_db_admin->update_product_stock( $orderdetail->product_id, $orderdetail->quantity );
						}
					}
					
					// Update Order Status/Send Alerts
					$ec_db_admin->update_order_status( $order->order_id, "3" );
					do_action( 'wpeasycart_order_paid', $order->order_id );
					
					// send email
					$order_display = new ec_orderdisplay( $order_row, true, true );
					$order_display->send_email_receipt( );
					$order_display->send_gift_cards( );
				}
			}
			
		// iDEAL failed	
		}else if( $webhook_type == "source.failed" || $webhook_type == "source.canceled" ){
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_order WHERE gateway_transaction_id = %s", $webhook_data->id . ':' . $webhook_data->client_secret ) );
			
		// Payment Intent Succeeded	
		}else if( $webhook_type == "payment_intent.succeeded" ){
			
			global $wpdb;
			$ec_db_admin = new ec_db_admin( );
			
			$mysqli->insert_response( 0, 0, "STRIPE Payment Complete", print_r( $webhook_data, true ) );
			$order = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_order WHERE gateway_transaction_id = %s", $webhook_data->id ) );
			if( $order ){
				$order_row = $ec_db_admin->get_order_row_admin( $order->order_id );
				$orderdetails = $ec_db_admin->get_order_details_admin( $order->order_id );
				// Update Order Status/Send Alerts
				$ec_db_admin->update_order_status( $order->order_id, "6" );
				$wpdb->query( $wpdb->prepare( "UPDATE ec_order SET stripe_charge_id = %s WHERE order_id = %d", $webhook_data->charges->data[0]->id, $order->order_id ) );
				do_action( 'wpeasycart_order_paid', $order->order_id );
				
				// send email
				$order_display = new ec_orderdisplay( $order_row, true, true );
				$order_display->send_email_receipt( );
				$order_display->send_gift_cards( );
			}
			
		}else if( $webhook_type == "payment_intent.payment_failed" ){
			
			global $wpdb;
			$ec_db_admin = new ec_db_admin( );
			
			$mysqli->insert_response( 0, 0, "STRIPE Payment Failed", print_r( $webhook_data, true ) );
			$order = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_order WHERE gateway_transaction_id = %s", $webhook_data->id ) );
			if( $order ){
				$ec_db_admin->update_order_status( $order->order_id, "19" );
			}
			
		}
		
		do_action( 'wpeasycart_stripe_webhook', $webhook_id, $webhook_type, $webhook_data );
		

	}

}

?>