<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_stripe extends ec_gateway{
	
	private $account_page;						// VARCHAR
		
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	function process_credit_card( ){
		
		$gateway_url = $this->get_gateway_url( );
		$gateway_response = $this->get_gateway_response( $gateway_url, "", array( ) );
		
		if( !$gateway_response ){
			return false;
		}else{
			if( $this->is_success )
				return true;
			else
				return false;
			
		}
	}
	
	function get_gateway_response( $gateway_url, $gateway_data, $gateway_headers ){
		
		if( get_option( 'ec_option_stripe_order_create_customer' ) && !isset( $response->error ) && $this->user->user_id != 0 && $this->user->stripe_customer_id == "" ){
			$customer_id = $this->insert_customer( $this->user, $this->credit_card );
			$this->user->stripe_customer_id = $customer_id;
			$this->mysqli->update_user_stripe_id( $this->user->user_id, $customer_id );
			
			$card_id = $this->insert_card( $this->user, $this->credit_card );
			$this->user->stripe_customer_card_id = $card_id;
			
			$response = $this->insert_charge( $this->order_totals, $this->user, $this->credit_card, $this->order_id, true );
		
		}else if(  get_option( 'ec_option_stripe_order_create_customer' ) && !isset( $response->error ) && $this->user->user_id != 0 && $this->user->stripe_customer_id != "" ){
			$this->update_customer( $this->user );
			
			$card_id = $this->insert_card( $this->user, $this->credit_card );
			$this->user->stripe_customer_card_id = $card_id;
			
			$response = $this->insert_charge( $this->order_totals, $this->user, $this->credit_card, $this->order_id, true );
		
		}else{
			$response = $this->insert_charge( $this->order_totals, $this->user, $this->credit_card, $this->order_id, false );
		}
		
		$this->handle_gateway_response( $response );
		
		if( $this->is_success ){
			//$this->process_subscriptions( );
			$this->mysqli->update_order_stripe_charge_id( $this->order_id, $response->id );
			return true;
		}else{
			return false;
		}
			
	}
	
	function process_subscriptions( ){
		$has_subscriptions = false;
		for( $i=0; $i<count( $this->cart->cart ); $i++ ){
			if( $this->cart->cart[$i]->is_subscription_item ){
				$has_subscriptions = true;
				break;
			}
		}
		
		if( $has_subscriptions ){
			// Make sure customer exists
			if( $this->user->stripe_customer_id == "" ){
				$customer_id = $this->insert_customer( $this->user );
				$this->mysqli->update_user_stripe_id( $this->user->user_id, $customer_id );
				$GLOBALS['ec_user']->stripe_customer_id = $this->user->user_id = $customer_id;
			}
			
			// Get Coupon Value Available
			$coupon_code = NULL;
			if( $GLOBALS['ec_cart_data']->cart_data->coupon_code && $GLOBALS['ec_cart_data']->cart_data->coupon_code != '' )
				$coupon_code = $GLOBALS['ec_cart_data']->cart_data->coupon_code;
			
			foreach( $this->cart->cart as $cart_item ){
				// Check if subscription plan added
				if( !$cart_item->stripe_plan_added ){ // Add plan if needed
					$plan_added = $this->insert_plan( $cart_item );
					$this->mysqli->update_product_stripe_added( $cart_item->product_id );
				}
				
				// Insert subscriptions
				if( $cart_item->is_subscription_item ){
					$start_date = time( );
					if( $cart_item->subscription_bill_period == "W" ){
						$sub_start_date = strtotime( '+' . $cart_item->subscription_bill_length . ' week', $start_date);
					
					}else if( $cart_item->subscription_bill_period == "M" ){
						$sub_start_date = strtotime( '+' . $cart_item->subscription_bill_length . ' month', $start_date);
					
					}else if( $cart_item->subscription_bill_period == "Y" ){
						$sub_start_date = strtotime( '+' . $cart_item->subscription_bill_length . ' year', $start_date);
					}
					
					// Insert a subscription
					$this->insert_subscription( $cart_item, $this->user, $this->credit_card, $coupon_code, $cart_item->prorate, $sub_start_date, $cart_item->quantity, $this->tax->get_tax_rate( ), array( ), $this->tax->get_stripe_tax_rates( ) );
					die( );
				}
			}
		}
	}
	
	function get_gateway_url( $card = '' ){
		
		if( is_string( $card ) && substr( $card, 0, 3 ) == "pm_" ){
            return "https://api.stripe.com/v1/payment_intents";
        }
		return "https://api.stripe.com/v1/charges";

	}
	
	function handle_gateway_response( $response ){
		
		if( $response == "" || isset( $response->error ) ){
			$status = $response->error;
			$this->is_success = 0;
		}else{
			$this->is_success = 1;
		}
		
		$this->mysqli->insert_response( $this->order_id, !$this->is_success, "Stripe", print_r( $response, true ) );
		
		if( !$this->is_success )
			$this->error_message = $response->error->message;
			
	}
	
	////////////////////////////////////////////////
	// PUBLIC CHARGE FUNCTIONS
	////////////////////////////////////////////////
	public function insert_charge( $order_totals, $user, $card, $order_id, $token_used = false ){
		
		$gateway_data = $this->get_insert_charge_data( $order_totals, $user, $card, $order_id, $token_used );
		$response = $this->call_stripe( $this->get_gateway_url( $card ), $gateway_data );
		$json = json_decode( $response );
		if( isset( $json->error ) ){
			$GLOBALS['ec_cart_data']->cart_data->card_error = $json->error->message;
		}else{
			$GLOBALS['ec_cart_data']->cart_data->card_error = '';
		}
		$GLOBALS['ec_cart_data']->save_session_to_db( );
		return $json;
		
	}
	
	public function get_charge( $charge_id ){
		
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/charges/".$charge_id, array( ) );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
		
	}
	
	public function refund_charge( $charge_id, $amount ){
		
		$data = $this->get_refund_charge_data( $charge_id, $amount );
		$response = $this->call_stripe( "https://api.stripe.com/v1/charges/" . $charge_id . "/refund", $data );
		$json = json_decode( $response );
		
		$this->mysqli->insert_response( $this->order_id, 0, "Stripe Refund", print_r( $json, true ) );
		
		if( $response != "" && !isset( $json->error ) )
			return true;
		else
			return false;
		
	}
	
	public function capture_charge( $charge_id, $amount ){
		
		$data = $this->get_capture_charge_data( $charge_id, $amount );
		$response = $this->call_stripe( "https://api.stripe.com/v1/charges/" . $charge_id . "/capture", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return true;
		else
			return false;
		
	}
	
	public function get_charge_list( $limit, $offset, $customer_id = 0, $starting_after = NULL ){
		
		$data = $this->get_charge_list_data( $limit, $offset, $customer_id, $starting_after );
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/charges", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
		
	}
	
	public function get_refund_list( $limit = 100, $starting_after = NULL ){
		
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/refunds", array( "limit" => $limit, "starting_after" => $starting_after ) );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
		
	}
	
	////////////////////////////////////////////////
	// PUBLIC SUBSCRIPTION FUNCTIONS
	////////////////////////////////////////////////
	
	public function insert_subscription( $product, $user, $card, $coupon = NULL, $prorate = "true", $trial_end = NULL, $quantity = 1, $tax_rate = 0.00, $subscription_options = array( ), $tax_rates = array( ) ){
		
		$data = $this->get_insert_subscription_data( $product, $user, $card, $coupon, $prorate, $trial_end, $quantity, $tax_rate, $subscription_options, $tax_rates );
		$response = $this->call_stripe( "https://api.stripe.com/v1/customers/" . $user->stripe_customer_id . "/subscriptions", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) ){
			$GLOBALS['ec_cart_data']->cart_data->card_error = '';
			$GLOBALS['ec_cart_data']->save_session_to_db( );
			return $json;
		}else{
			$GLOBALS['ec_cart_data']->cart_data->card_error = $json->error->message;
			$GLOBALS['ec_cart_data']->save_session_to_db( );
			return false;
		}
	
	}
	
	public function get_subscription( $customer_id, $subscription_id ){
		
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/customers/" . $customer_id . "/subscriptions/" . $subscription_id, array( ) );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	
	}
	
	public function get_subscription_list( $customer_id = NULL, $product_id = NULL, $ending_before = NULL, $starting_after = NULL, $limit = 10, $status = NULL ){
		
		$vars = array( "limit" => $limit );
		if( isset( $customer_id ) )
			$vars["customer"] = $customer_id;
		if( isset( $product_id ) )
			$vars["plan"] = $product_id;
		if( isset( $ending_before ) )
			$vars["ending_before"] = $ending_before;
		if( isset( $starting_after ) )
			$vars["starting_after"] = $starting_after;
		if( isset( $status ) )
			$vars["status"] = $status;
		
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/subscriptions", $vars );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	
	}
	
	public function update_subscription( $product, $user, $card = NULL, $subscription_id, $coupon = NULL, $prorate = "true", $trial_end = NULL, $quantity = 1 ){
		
		$data = $this->get_update_subscription_data( $product, $user, $card, $coupon, $prorate, $trial_end, $quantity );
		$response = $this->call_stripe( "https://api.stripe.com/v1/customers/" . $user->stripe_customer_id . "/subscriptions/" . $subscription_id, $data );
		$json = json_decode( $response );
		
		$this->mysqli->insert_response( $this->order_id, 0, "Stripe Update Subscription " . $subscription_id, print_r( $response, true ) );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	
	}
	
	public function cancel_subscription( $user, $subscription_id, $cancel_at_end_of_current_period = "false" ){
		
		$response = $this->call_stripe_delete( "https://api.stripe.com/v1/customers/" . $user->stripe_customer_id . "/subscriptions/" . $subscription_id );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	
	}
	
	public function get_active_subscription_list( $user, $limit = 25, $offset = 0 ){
		
		$data = $this->get_subscription_list_data( $user, $limit, $offset );
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/customers/" . $user->stripe_customer_id . "/subscriptions", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	
	}
	
	public function set_subscription_payment_method( $payment_method, $stripe_subscription_info, $subscription, $quantity ){
		$data = array(
			'default_payment_method'	=> $payment_method,
			'items'						=> array(
				array(
					'id'				=> $stripe_subscription_info->items->data[0]->id,
					'quantity'			=> $quantity,
					'plan'				=> $subscription->subscription_unique_id
				)
			)
		);
		$response = $this->call_stripe( "https://api.stripe.com/v1/subscriptions/" . $subscription->stripe_subscription_id, $data );
		$json = json_decode( $response );
		
		$this->mysqli->insert_response( $this->order_id, 0, "Stripe Update Subscription " . $subscription_id, print_r( $response, true ) );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	////////////////////////////////////////////////
	// PUBLIC CUSTOMER FUNCTIONS
	////////////////////////////////////////////////
	
	public function insert_customer( $user, $card = NULL, $account_balance = 0 ){
		
		$data = $this->get_insert_customer_data( $user, $card, $account_balance );
		$response = $this->call_stripe( "https://api.stripe.com/v1/customers", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json->id;
		else
			return false;
	
	}
	
	public function insert_quick_customer( $payment_id ){
		
		$payment_intent = $this->get_payment_intent( $payment_id );
		
		if( isset( $payment_intent->charges->data[0]->billing_details ) ){
			$data = array(
				'description'		=> $payment_intent->charges->data[0]->billing_details->name,
				'email'				=> $payment_intent->charges->data[0]->billing_details->email,
				'name'				=> $payment_intent->charges->data[0]->billing_details->name,
				'payment_method'	=> $payment_intent->payment_method
			);
		}else{
			$data = array(
				'description'		=> $GLOBALS['ec_cart_data']->cart_data->billing_first_name . ' ' . $GLOBALS['ec_cart_data']->cart_data->billing_last_name . ' - ' . $order_id,
				'email'				=> $GLOBALS['ec_cart_data']->cart_data->email,
				'name'				=> $GLOBALS['ec_cart_data']->cart_data->billing_first_name . ' ' . $GLOBALS['ec_cart_data']->cart_data->billing_last_name,
				'payment_method'	=> $payment_intent->payment_method
			);
		}
		$response = $this->call_stripe( "https://api.stripe.com/v1/customers", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json->id;
		else
			return false;
			
	}
	
	public function insert_guest_customer( $payment_id, $order_id ){
		
		$payment_intent = $this->get_payment_intent( $payment_id );
		
		if( isset( $payment_intent->charges->data[0]->billing_details ) ){
			$data = array(
				'description'		=> $payment_intent->charges->data[0]->billing_details->name . ' - ' . $order_id,
				'email'				=> $payment_intent->charges->data[0]->billing_details->email,
				'name'				=> $payment_intent->charges->data[0]->billing_details->name,
				'payment_method'	=> $payment_intent->payment_method
			);
		}else{
			$data = array(
				'description'		=> $GLOBALS['ec_cart_data']->cart_data->billing_first_name . ' ' . $GLOBALS['ec_cart_data']->cart_data->billing_last_name . ' - ' . $order_id,
				'email'				=> $GLOBALS['ec_cart_data']->cart_data->email,
				'name'				=> $GLOBALS['ec_cart_data']->cart_data->billing_first_name . ' ' . $GLOBALS['ec_cart_data']->cart_data->billing_last_name,
				'payment_method'	=> $payment_intent->payment_method
			);
		}
		$response = $this->call_stripe( "https://api.stripe.com/v1/customers", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json->id;
		else
			return false;
			
	}
	
	public function get_customer( $user ){
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/customers/" . $user->stripe_customer_id, $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function update_customer( $user, $account_balance = 0, $default_source = NULL ){
		$data = $this->get_update_customer_data( $user, $account_balance, $default_source );
		$response = $this->call_stripe( "https://api.stripe.com/v1/customers/" . $user->stripe_customer_id, $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return true;
		else
			return false;
	}
	
	public function delete_customer( $user ){
		$response = $this->call_stripe_delete( "https://api.stripe.com/v1/customers/" . $user->stripe_customer_id );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return true;
		else
			return false;
	}
	
	public function get_customer_list( $limit = 25, $offset = 0 ){
		$data = $this->get_customer_list_data( $limit, $offset );
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/customers", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	////////////////////////////////////////////////
	// PUBLIC PLAN FUNCTIONS
	////////////////////////////////////////////////
	public function insert_plan( $product ){
		$data = $this->get_insert_plan_data( $product );
		$response = $this->call_stripe( "https://api.stripe.com/v1/plans", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return true;
		else
			return false;
	}
	
	public function get_plan( $product ){
		$data = $this->get_get_plan_data( $product );
		$response = $this->call_stripe( "https://api.stripe.com/v1/plans/" . $product->subscription_unique_id, $data );
		$json = json_decode( $response );
		
		if( !$json->error )
			return $json;
		else
			return false;
	}
	
	public function update_plan( $product ){
		$data = $this->get_update_plan_data( $product );
		$response = $this->call_stripe( "https://api.stripe.com/v1/plans/" . $product->subscription_unique_id, $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return true;
		else
			return false;
	}
	
	public function delete_plan( $product ){
		$response = $this->call_stripe_delete( "https://api.stripe.com/v1/plans/" . $product->subscription_unique_id );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return true;
		else
			return false; 
	}
	
	public function get_plan_list( $limit = 25, $offset = 0 ){
		$data = $this->get_plan_list_data( $product );
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/plans", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function insert_option_as_plan( $product, $option_item ){
		$stripe_plan_main = $this->get_plan( $product );
		$currency = get_option( 'ec_option_stripe_currency' );
		$data = array(
			"id"						=> rand( 1000000, 9999999 ),
			"amount"					=> number_format( $option_item->optionitem_price * 100, 0, "", "" ),
			"currency"					=> $currency,
			"interval"					=> $this->convert_period_to_name( $product->subscription_bill_period ), //week, month, or year
			"interval_count"			=> $product->subscription_bill_length,
			"product"					=> $stripe_plan_main->product,
			"nickname"					=> $GLOBALS['language']->convert_text( $option_item->optionitem_name ),
			"trial_period_days"			=> $product->trial_period_days
		);
		$response = $this->call_stripe( "https://api.stripe.com/v1/plans", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	////////////////////////////////////////////////
	// PUBLIC CARDS FUNCTIONS
	////////////////////////////////////////////////
	
	public function insert_card( $user, $card ){
		
		$data = $this->get_insert_card_data( $user, $card );
		$response = $this->call_stripe( "https://api.stripe.com/v1/customers/" . $user->stripe_customer_id . "/cards", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json->id;
		else
			return false;
		
	}
	
	public function get_card( $user, $card_id ){
		
		$data = $this->get_get_card_data( $user, $card_id );
		$response = $this->call_stripe( "https://api.stripe.com/v1/customers/" . $user->stripe_customer_id . "/cards/" . $card_id, $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
		
	}
	
	public function update_card( $user, $exp_month = NULL, $exp_year = NULL, $card_name ){
		
		$data = $this->get_update_card_data( $user, $exp_month, $exp_year, $card_name );
		$response = $this->call_stripe( "https://api.stripe.com/v1/customers/" . $user->stripe_customer_id . "/cards/" . $card_id, $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return true;
		else
			return false;
		
	}
	
	public function delete_card( $user, $card_id ){
		
		$response = $this->call_stripe_delete( "https://api.stripe.com/v1/customers/" . $user->stripe_customer_id . "/cards/" . $card_id );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return true;
		else
			return false;
		
	}
	
	public function get_card_list( $customer_id, $limit = 25, $offset = 0 ){
	
		$data = $this->get_card_list_data( $customer_id, $limit, $offset );
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/payment_methods", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
		
	}
	
	////////////////////////////////////////////////
	// PUBLIC SUBSCRIPTION COUPONS FUNCTIONS
	////////////////////////////////////////////////
	
	public function insert_coupon( $coupon ){
		
		$data = $this->get_insert_coupon_data( $coupon );
		$response = $this->call_stripe( "https://api.stripe.com/v1/coupons", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json->id;
		else
			return false;
		
	}
	
	public function get_coupon( $coupon_id ){
		
		$response = $this->call_stripe( "https://api.stripe.com/v1/coupons/" . $coupon_id, array( ) );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
		
	}
	
	public function delete_coupon( $coupon_id ){
		
		$response = $this->call_stripe_delete( "https://api.stripe.com/v1/coupons/" . $coupon_id );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return true;
		else
			return false;
		
	}
	
	public function get_coupon_list( $limit, $offset ){
		
		$data = $this->get_coupon_list_data( $limit, $offset );
		$response = $this->call_stripe( "https://api.stripe.com/v1/coupons", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
		
	}
	
	////////////////////////////////////////////////
	// PUBLIC EVENT FUNCTIONS
	////////////////////////////////////////////////
	public function get_event_list( $type, $limit ){
		
		$data = $this->get_event_list_data( $type, $limit );
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/events", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
		
	}
	
	////////////////////////////////////////////////
	// PUBLIC BALANCE FUNCTIONS
	////////////////////////////////////////////////
	public function get_balance( ){
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/balance", array( ) );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function get_balance_history( $transfer_id, $starting_after = false ){
		$data = array( 
			"payout" => $transfer_id,
			"limit" => "100"
		);
		if( $starting_after )
			$data["starting_after"] = $starting_after;
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/balance/history", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function get_balance_transaction( $id ){
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/balance/history/" . $id, array( ) );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	////////////////////////////////////////////////
	// PUBLIC TRANSFERS FUNCTIONS
	////////////////////////////////////////////////
	public function get_transfer( $id ){
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/payouts/" . $id, array( ) );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function get_transfer_list( $status = NULL, $limit = 100, $starting_after = NULL, $ending_before = NULL ){
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/payouts", array( "status" => $status, "limit" => $limit, "starting_after" => $starting_after, "ending_before" => $ending_before ) );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	////////////////////////////////////////////////
	// PUBLIC TRANSFERS FUNCTIONS
	////////////////////////////////////////////////
	public function get_dispute( $id ){
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/disputes/" . $id, array( ) );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function get_dispute_list( $limit = 100, $starting_after = NULL ){
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/disputes", array( "limit" => $limit, "starting_after" => $starting_after ) );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function update_customer_payment_method( $token_id, $customer_id ){
		$response = $this->call_stripe( "https://api.stripe.com/v1/customers/" . $customer_id, array( 'source' => $token_id ) );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function attach_payment_method( $payment_id, $customer ){
		$response = $this->call_stripe( "https://api.stripe.com/v1/payment_methods/" . $payment_id . '/attach', array( 'customer' => $customer->stripe_customer_id ) );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function detach_payment_method( $payment_id ){
		$response = $this->call_stripe( "https://api.stripe.com/v1/payment_methods/" . $payment_id . '/detach', array( ) );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function set_default_payment_method( $payment_id, $customer ){
		$response = $this->call_stripe( 
			"https://api.stripe.com/v1/customers/" . $customer->stripe_customer_id, 
			array( 
				'invoice_settings' => array(
					'default_payment_method' => $payment_id
				)
			)
		);
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function subscription_update_default_card( $subscription_id, $payment_id ){
		$response = $this->call_stripe( 
			"https://api.stripe.com/v1/subscriptions/" . $subscription_id, 
			array( 
				'default_payment_method' => $payment_id,
				"expand" => array(
					"latest_invoice.payment_intent"
				)
			)
		);
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	////////////////////////////////////////////////
	// PUBLIC PAYMENT INTENT FUNCTIONS
	////////////////////////////////////////////////
	public function create_payment_intent( $order_totals ){
		$data = $this->get_create_payment_intent_data( $order_totals );
		$response = $this->call_stripe( "https://api.stripe.com/v1/payment_intents", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function update_payment_intent_total( $id, $order_totals ){
		$data = $this->get_create_payment_intent_data( $order_totals );
		$response = $this->call_stripe( "https://api.stripe.com/v1/payment_intents/" . $id, $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function update_payment_intent_description( $id, $description ){
		$data = $this->get_update_payment_intent_description_data( $description );
		$response = $this->call_stripe( "https://api.stripe.com/v1/payment_intents/" . $id, $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function update_payment_intent_customer( $id, $customer_id ){
		$data = $this->get_update_payment_intent_customer_data( $customer_id );
		$response = $this->call_stripe( "https://api.stripe.com/v1/payment_intents/" . $id, $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function get_payment_intent( $id ){
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/payment_intents/" . $id, array( ) );
		$json = json_decode( $response );
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function create_setup_intent( $customer_id = false ){
		$data = array(
			'payment_method_types' => array(
				'card'
			)
		);
		if( $customer_id ){
			$data['customer'] = $customer_id;
		}
		$response = $this->call_stripe( "https://api.stripe.com/v1/setup_intents", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function update_setup_intent( $setup_intent_id, $customer_id ){
		$data = array(
			'customer' => $customer_id
		);
		$response = $this->call_stripe( "https://api.stripe.com/v1/setup_intents/" . $setup_intent_id, $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	////////////////////////////////////////////////
	// PUBLIC TAX RATE FUNCTIONS
	////////////////////////////////////////////////
	public function add_taxrate( $display_name, $tax_rate, $jurisdiction = '', $inclusive = false ){
		$data = $this->get_add_taxrate_data( $display_name, $tax_rate, $jurisdiction, $inclusive );
		$response = $this->call_stripe( "https://api.stripe.com/v1/tax_rates", $data );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	public function get_taxrate( $taxrate_id ){
		$response = $this->call_stripe_get( "https://api.stripe.com/v1/tax_rates/" . $taxrate_id, array( ) );
		$json = json_decode( $response );
		
		if( $response != "" && !isset( $json->error ) )
			return $json;
		else
			return false;
	}
	
	////////////////////////////////////////////////
	// PRIVATE MAIN STRIPE CALL
	////////////////////////////////////////////////
	private function call_stripe( $gateway_url, $gateway_data ){
		
		$app_info = array(
			'name' 		=> 'WordPress EasyCart',
			'version' 	=> EC_CURRENT_VERSION,
			'url' 		=> 'https://www.wpeasycart.com'
		);
		
		$api_key = get_option( 'ec_option_stripe_api_key' );
		$headr = array( );
		$headr[] = 'User-Agent: ' . $this->_formatAppInfo( $app_info );
		$headr[] = 'Authorization: Bearer ' . $api_key;
		$headr[] = 'Stripe-Version: 2019-05-16';
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt($ch, CURLOPT_POST, true ); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $gateway_data ) );
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		if( $response === false ){
			$this->mysqli->insert_response( $this->order_id, 1, "STRIPE CURL ERROR", curl_error( $ch ) );
			$response = (object) array( "error" => curl_error( $ch ) );
		}else
			$this->mysqli->insert_response( $this->order_id, 0, "Stripe Response", print_r( $response, true ) );
		curl_close ($ch);
		
		return $response;
		
	}
	
	private function call_stripe_get( $gateway_url, $gateway_data ){
		
		$app_info = array(
			'name' 		=> 'WordPress EasyCart',
			'version' 	=> EC_CURRENT_VERSION,
			'url' 		=> 'https://www.wpeasycart.com'
		);
		
		$api_key = get_option( 'ec_option_stripe_api_key' );
		$headr = array( );
		$headr[] = 'User-Agent: ' . $this->_formatAppInfo( $app_info );
		$headr[] = 'Authorization: Bearer ' . $api_key;
		$headr[] = 'Stripe-Version: 2019-05-16';
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url . "?" . http_build_query( $gateway_data ) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt($ch, CURLOPT_POST, false ); 
		curl_setopt($ch, CURLOPT_HTTPGET, true );
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		if( $response === false ){
			$this->mysqli->insert_response( $this->order_id, 1, "STRIPE GET CURL ERROR", curl_error( $ch ) );
			$response = (object) array( "error" => curl_error( $ch ) );
		}else{
			$this->mysqli->insert_response( $this->order_id, 0, "Stripe Get Response", print_r( $response, true ) );
		}
		curl_close ($ch);
		
		return $response;
		
	}
	
	private function call_stripe_delete( $gateway_url ){
		
		$app_info = array(
			'name' 		=> 'WordPress EasyCart',
			'version' 	=> EC_CURRENT_VERSION,
			'url' 		=> 'https://www.wpeasycart.com'
		);
		
		$api_key = get_option( 'ec_option_stripe_api_key' );
		$headr = array( );
		$headr[] = 'User-Agent: ' . $this->_formatAppInfo( $app_info );
		$headr[] = 'Authorization: Bearer ' . $api_key;
		$headr[] = 'Stripe-Version: 2019-05-16';
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $gateway_url );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		if( $response === false ){
			$this->mysqli->insert_response( $this->order_id, 1, "STRIPE DELETE CURL ERROR", curl_error( $ch ) );
			$response = (object) array( "error" => curl_error( $ch ) );
		}
		curl_close ($ch);
		
		return $response;
		
	}
	
	private function _formatAppInfo( $appInfo ){
        if( $appInfo !== null ){
            $string = $appInfo['name'];
            if( $appInfo['version'] !== null ){
                $string .= '/' . $appInfo['version'];
            }
            if( $appInfo['url'] !== null ){
                $string .= ' (' . $appInfo['url'] . ')';
            }
            return $string;
        } else {
            return null;
        }
    }
	
	////////////////////////////////////////////////
	// PRIVATE CHARGES DATA FUNCTIONS
	////////////////////////////////////////////////
	
	private function get_insert_charge_data( $order_totals, $user, $card, $order_id, $token_used ){
		
		$amount = number_format( $order_totals->grand_total * 100, 0, "", "" );
		$currency = get_option( 'ec_option_stripe_currency' );
		
		if( isset( $_POST['stripeToken'] ) && $token_used ){
			$gateway_data = array( 	"amount"			=> $amount,
									"currency"			=> $currency,
									"description"		=> $order_id,
									"customer"			=> $user->stripe_customer_id,
									"source"			=> $user->stripe_customer_card_id );
			
		}else if( isset( $_POST['stripeToken'] ) ){
			$gateway_data = array( 	"amount"			=> $amount,
									"currency"			=> $currency,
									"description"		=> $order_id,
									"source"			=> esc_attr( $_POST['stripeToken'] ) );
									
		}else if( is_object( $card ) ){
			$card_array = array( 	"number" 			=> $card->card_number,
									"exp_month"			=> $card->expiration_month,
									"exp_year"			=> $card->get_expiration_year( 2 ),
									"cvc"				=> $card->security_code,
									"name"				=> $card->card_holder_name,
									"address_line1"		=> $user->billing->address_line_1,
									"address_city"		=> $user->billing->city,
									"address_zip"		=> $user->billing->zip,
									"address_state"		=> $user->billing->state,
									"address_country"	=> $user->billing->country );
							
			$gateway_data = array( 	"amount"			=> $amount,
									"currency"			=> $currency,
									"card"				=> $card_array,
									"description"		=> $order_id );
		
		}else if( is_string( $card ) && substr( $card, 0, 4 ) == "tok_" ){
			$gateway_data = array( 	"amount"			=> $amount,
									"currency"			=> $currency,
									"description"		=> $order_id,
									"source"			=> $card );
		
		}else if( is_string( $card ) && substr( $card, 0, 4 ) == "src_" ){
			$gateway_data = array( 	"amount"			=> $amount,
									"currency"			=> $currency,
									"description"		=> $order_id,
									"source"			=> $card );
		}else if( is_string( $card ) && substr( $card, 0, 3 ) == "pm_" ){
			$gateway_data = array( 	"customer"			=> $user->stripe_customer_id,
                                    "amount"			=> $amount,
									"currency"			=> $currency,
									"description"		=> $order_id,
									"payment_method"	=> $card,
                                    "off_session"       => "true",
                                    "confirm"           => "true" );
        }else{
			$gateway_data = array( 	"customer"			=> $user->stripe_customer_id,
									"amount"			=> $amount,
									"currency"			=> $currency,
									"description"		=> $order_id );
		}
								
		return $gateway_data;
	}
	
	private function get_get_charge_data( $charge_id ){
		
		return array( "id" => $charge_id );
		
	}
	
	private function get_refund_charge_data( $charge_id, $amount ){
		
		return array( "amount" => number_format( $amount * 100, 0, "", "" ) );
		
	}
	
	private function get_capture_charge_data( $charge_id, $amount ){
		
		return array( "id" => $charge_id, "amount" => number_format( $amount * 100, 0, "", "" ) );
		
	}
	
	private function get_charge_list_data( $limit, $offset, $customer_id, $starting_after ){
	
		$gateway_data = array( 	"count" 	=> $limit,
								"offset"	=> $offset,
                                "starting_after" => $starting_after );
								
		if( $customer_id > 0 ){
			$gateway_data["customer"] = $customer_id;
		}
		
		return $gateway_data;
		
	}
	
	////////////////////////////////////////////////
	// PRIVATE SUBSCRIPTION DATA FUNCTIONS
	////////////////////////////////////////////////
	
	private function get_insert_subscription_data( $product, $user, $card, $coupon, $prorate, $trial_end, $quantity, $tax_rate = 0.00, $subscription_options, $tax_rates = array( ) ){
		
		if( isset( $product->subscription_unique_id ) && $product->subscription_unique_id )
			$product_id = $product->subscription_unique_id;
		else
			$product_id = $product->product_id;
		
		if( isset( $_POST['stripeToken'] ) ){
			$gateway_data = array(	
				"items"						=> array(
					array(
						"plan"				=> $product_id,
						"quantity"			=> $quantity,
						"tax_rates"			=> $tax_rates
					)
				),
				"coupon"					=> $coupon,
				"default_source"			=> $card,
				//"tax_percent"				=> $tax_rate,
				"expand"					=> array(
					"latest_invoice.payment_intent"
				),
				"trial_end"					=> $trial_end
			);
			
			for( $i=0; $i<count( $subscription_options ); $i++ ){
				$gateway_data['items'][] = array(
					"plan"					=> $subscription_options[$i],
					"quantity"				=> $quantity,
					"tax_rates"				=> $tax_rates
				);
			}
			
			if( is_string( $card ) && substr( $card, 0, 5 ) == 'card_' ){
				$gateway_data["default_payment_method"]	= $card;
			}
									
		}else{
			if( is_object( $card ) ){
				$card_array = array( 	
					"number" 				=> $card->card_number,
					"exp_month"				=> $card->expiration_month,
					"exp_year"				=> $card->get_expiration_year( 2 ),
					"cvc"					=> $card->security_code,
					"name"					=> $card->card_holder_name,
					"address_line1"			=> $user->billing->address_line_1,
					"address_city"			=> $user->billing->city,
					"address_zip"			=> $user->billing->zip,
					"address_state"			=> $user->billing->state,
					"address_country"		=> $user->billing->country 
				);
				$gateway_data = array(	
					"plan"					=> $product_id,
					"coupon"				=> $coupon,
					"trial_end"				=> $trial_end,
					"card"					=> $card_array,
					"quantity"				=> $quantity,
					"tax_percent"			=> $tax_rate
				);
			}else{
				$gateway_data = array(	
					"plan"					=> $product_id,
					"coupon"				=> $coupon,
					"trial_end"				=> $trial_end,
					"default_payment_method"=> $card,
					"quantity"				=> $quantity,
					"tax_percent"			=> $tax_rate
				);
			}
		}
								
		return $gateway_data;
	
	}
	
	private function get_update_subscription_data( $product, $user, $card, $coupon, $prorate, $trial_end, $quantity ){
		
		if( $prorate == '0' )
			$prorate = "false";
		else if( $prorate == '1' )
			$prorate = "true";
		
		if( isset( $product->subscription_unique_id ) && $product->subscription_unique_id )
			$product_id = $product->subscription_unique_id;
		else
			$product_id = $product->product_id;
		
		$gateway_data = array(	"plan"					=> $product_id,
								"coupon"				=> $coupon,
								"prorate"				=> $prorate,
								"trial_end"				=> $trial_end,
								"quantity"				=> $quantity );
								
		if( isset( $_POST['stripeToken'] ) )
			$gateway_data['source'] = esc_attr( $_POST['stripeToken'] );
		
		return $gateway_data;
		
	}
	
	private function get_subscription_list_data( $user, $limit, $offset ){
		
		$gateway_data = array(	"count"					=> $limit,
								"offset"				=> $offset );
								
		return $gateway_data;
		
	}
	
	////////////////////////////////////////////////
	// PRIVATE CUSTOMER DATA FUNCTIONS
	////////////////////////////////////////////////
	
	private function get_insert_customer_data( $user, $card, $account_balance = 0 ){
		
		if( $card == NULL || isset( $_POST['stripeToken'] ) ){
			$card_array = NULL;
		}else if( $card != NULL ){
			$card_array = array("number" 				=> $card->card_number,
								"exp_month"				=> $card->expiration_month,
								"exp_year"				=> $card->get_expiration_year( 2 ),
								"cvc"					=> $card->security_code,
								"name"					=> $card->card_holder_name,
								"address_line1"			=> $user->billing->address_line_1,
								"address_city"			=> $user->billing->city,
								"address_zip"			=> $user->billing->zip,
								"address_state"			=> $user->billing->state,
								"address_country"		=> $user->billing->country );
		}
		
		$meta_data = array(		"first_name"			=> $user->billing->first_name,
								"last_name"				=> $user->billing->last_name,
								"address_line1"			=> $user->billing->address_line_1,
								"city"					=> $user->billing->city,
								"state"					=> $user->billing->state,
								"zip"					=> $user->billing->zip,
								"country"				=> $user->billing->country,
								"phone"					=> $user->billing->phone );
		
		$gateway_data = array(	"card"					=> $card_array,
								"name"					=> $user->billing->first_name . " " . $user->billing->last_name,
								"description"			=> $user->billing->first_name . " " . $user->billing_last_name,
								"email"					=> $user->email,
								"account_balance"		=> number_format( $account_balance * 100, 0, '', '' ),
								"metadata"				=> $meta_data );
								
		return $gateway_data;
		
	}
	
	private function get_update_customer_data( $user, $account_balance = 0, $default_source = NULL ){
		
		$meta_data = array(		"first_name"			=> $user->billing->first_name,
								"last_name"				=> $user->billing->last_name,
								"address_line1"			=> $user->billing->address_line_1,
								"city"					=> $user->billing->city,
								"state"					=> $user->billing->state,
								"zip"					=> $user->billing->zip,
								"country"				=> $user->billing->country,
								"phone"					=> $user->billing->phone );
		
		$gateway_data = array(	"default_source"		=> $default_source,
								"name"					=> $user->billing->first_name . " " . $user->billing->last_name,
								"description"			=> $user->billing->first_name . " " . $user->billing->last_name,
								"email"					=> $user->email,
								"account_balance"		=> number_format( $account_balance * 100, 0, '', '' ),
								"metadata"				=> $meta_data );
								
		return $gateway_data;
		
	}
	
	private function get_customer_list_data( $limit, $offset ){
		
		return array( "count" => $limit, "offset" => $offset );
		
	}
	
	////////////////////////////////////////////////
	// PRIVATE PLAN DATA FUNCTIONS
	////////////////////////////////////////////////
	
	private function get_insert_plan_data( $product ){
		
		if( isset( $product->subscription_unique_id ) && $product->subscription_unique_id )
			$product_id = $product->subscription_unique_id;
		else
			$product_id = $product->product_id;
		
		$currency = get_option( 'ec_option_stripe_currency' );
		$gateway_data = array(	"id"					=> $product_id,
								"amount"				=> number_format( $product->price * 100, 0, "", "" ),
								"currency"				=> $currency,
								"interval"				=> $this->convert_period_to_name( $product->subscription_bill_period ), //week, month, or year
								"interval_count"		=> $product->subscription_bill_length,
								"product"				=> array(
									"name"				=> $GLOBALS['language']->convert_text( $product->title )
								),
								"nickname"				=> $GLOBALS['language']->convert_text( $product->title ),
								"trial_period_days"		=> $product->trial_period_days );
								
		return $gateway_data;
		
	}
	
	private function get_get_plan_data( $product ){
		
		$gateway_data = array( );
								
		return $gateway_data;
		
	}
	
	private function get_update_plan_data( $product ){
		
		$gateway_data = array(	"nickname"					=> $product->title );
		if( isset( $product->trial_period_days ) )
			$gateway_data['trial_period_days'] 			= $product->trial_period_days;
								
		return $gateway_data;
		
	}
	
	private function get_plan_list_data( $limit, $offset ){
		
		$gateway_data = array(	"count"					=> $limit,
								"offset"				=> $offset );
								
		return $gateway_data;
		
	}
	
	////////////////////////////////////////////////
	// PRIVATE PLAN DATA FUNCTIONS
	////////////////////////////////////////////////
	
	private function get_insert_card_data( $user, $card ){
		
		if( isset( $_POST['stripeToken'] ) ){
			return array( "source" => esc_attr( $_POST['stripeToken'] ) );
		}else{
			$card_array = array(	"number" 				=> $card->card_number,
									"exp_month"				=> $card->expiration_month,
									"exp_year"				=> $card->get_expiration_year( 2 ),
									"cvc"					=> $card->security_code,
									"name"					=> $card->card_holder_name,
									"address_line1"			=> $user->billing->address_line_1,
									"address_city"			=> $user->billing->city,
									"address_zip"			=> $user->billing->zip,
									"address_state"			=> $user->billing->state,
									"address_country"		=> $user->billing->country );
									
			return array( "card" => $card_array );
		}
		
	}
	
	private function get_get_card_data( $user, $card_id ){
		
		return array( "id" => $card_id, "customer" => $user->user_id );
		
	}
	
	private function get_update_card_data( $user, $exp_month, $exp_year, $card_name ){
		
		$gateway_data = array(	"exp_month"				=> $exp_month,
								"exp_year"				=> $exp_year,
								"name"					=> $card->card_holder_name,
								"address_line1"			=> $user->billing->address_line_1,
								"address_city"			=> $user->billing->city,
								"address_zip"			=> $user->billing->zip,
								"address_state"			=> $user->billing->state,
								"address_country"		=> $user->billing->country );
		
		return $gateway_data;
		
	}
	
	private function get_card_list_data( $customer_id, $limit, $offset ){
		
		return array( "customer" => $customer_id, "type" => "card", "limit" => $limit, "starting_after" => ( ( $offset == 0 ) ? NULL : $offset ) );
		
	}
	
	////////////////////////////////////////////////
	// PRIVATE COUPON DATA FUNCTIONS
	////////////////////////////////////////////////
	
	private function get_insert_coupon_data( $coupon ){
		
		$currency = get_option( 'ec_option_stripe_currency' );
		
		$gateway_data = array(	"id"					=> $coupon['promocode_id'],
								"duration"				=> $coupon['duration'], //forever, once, or repeating
								"currency"				=> $currency );
		
		if( $coupon['is_amount_off'] )
			$gateway_data['amount_off'] = number_format( $coupon['amount_off'], 0, '', '' );
								
		else
			$gateway_data['percent_off'] =number_format( $coupon['percent_off'], 0, '', '' );
		
		if( $coupon['redeem_by'] )
			$gateway_data['redeem_by'] = (integer)$coupon['redeem_by'];
		
		if( $coupon['max_redemptions'] != "999" )
			$gateway_data['max_redemptions'] = (integer)$coupon['max_redemptions'];
								
		if( $coupon['duration'] == "repeating" ){
			$gateway_data[ "duration_in_months" ] = $coupon['duration_in_months'];
		}
		
		return $gateway_data;		
		
	}
	
	private function get_update_coupon_data( $coupon ){
		
		return array( $id = $coupon->promocode_id );
		
	}
	
	private function get_coupon_list_data( $limit, $offset ){
		
		return array( "count" => $limit, "offset" => $offset );
		
	}
	
	private function get_event_list_data( $type, $limit ){
		
		return array( "type" => $type, "limit" => $limit );
		
	}
	
	private function get_create_payment_intent_data( $order_totals ){
		
		$amount = number_format( $order_totals->grand_total * 100, 0, "", "" );
		$currency = get_option( 'ec_option_stripe_currency' );
		
		$data = array( 
			"amount" 					=> $amount,
			"currency" 					=> $currency,
			"payment_method_types"		=> array( "card" )
		);
		
		if( get_option( 'ec_option_stripe_order_create_customer' ) ){
			if( $GLOBALS['ec_user']->user_id != 0 && $GLOBALS['ec_user']->stripe_customer_id != '' ){
				$data["customer"] = $GLOBALS['ec_user']->stripe_customer_id;
			}
			$data["setup_future_usage"] = 'off_session';
		}
		
		return $data;
	}
	
	private function get_update_payment_intent_description_data( $description ){
		
		$data = array( 
			"description"				=> $description,
		);
		return $data;
		
	}
	
	private function get_update_payment_intent_customer_data( $customer_id ){
		
		$data = array( 
			"customer"				=> $customer_id,
		);
		return $data;
		
	}
	
	private function get_add_taxrate_data( $display_name, $tax_rate, $jurisdiction, $inclusive ){
		
		$data = array(
			"display_name"			=> $display_name,
			"inclusive"				=> ( $inclusive ) ? 'true' : 'false',
			"percentage"			=> $tax_rate
		);
		if( $jurisdiction )
			$data['jurisdiction']	=  $jurisdiction;
			
		return $data;
		
	}
	
	////////////////////////////////////////////////
	// PRIVATE SUBSCRIPTION HELPER FUNCTIONS
	////////////////////////////////////////////////
	
	private function convert_period_to_name( $period ){
		if( $period == "M" ){
			return "month";
		}else if( $period == "D" ){
			return "day";	
		}else if( $period == "Y" ){
			return "year";
		}else if( $period == "W" ){
			return "week";
		}
	}
	
}

add_action( 'wpeasycart_cart_updated', 'wpeasycart_stripe_connect_update_intent', 100 );
function wpeasycart_stripe_connect_update_intent( ){
	if( $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id ){
		$stripe = new ec_stripe( );
		$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$order_totals = ec_get_order_totals( $cart );
		$stripe->update_payment_intent_total( $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id, $order_totals );
	}
}
?>