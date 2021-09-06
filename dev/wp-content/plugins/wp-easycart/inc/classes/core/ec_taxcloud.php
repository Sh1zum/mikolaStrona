<?php
if( !class_exists( 'ec_taxcloud' ) ) :

final class ec_taxcloud{
	
	protected static $_instance = null;
	
	public $address_verified;
	
	public $tax_amount;
	
	public $subscription_product;
	public $subscription_product_quantity;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
	
	public function __construct( ){
		
		add_action( 'init', array( $this, 'initiate_taxcloud' ), 10 );
		add_action( 'wpeasycart_cart_updated', array( $this, 'update_tax_amount' ), 10 );
		add_action( 'wpeasycart_order_inserted', array( $this, 'add_tax_cloud_order' ), 10, 5 );
		add_action( 'wpeasycart_order_paid', array( $this, 'approve_tax_cloud_order' ), 10, 1 );
		add_action( 'wpeasycart_full_order_refund', array( $this, 'refund_tax_cloud_order' ), 10, 1 );
		
	}
	
	public function setup_subscription_for_tax( $product, $quantity ){
		$this->subscription_product = $product;
		$this->subscription_product_quantity = $quantity;
		$this->update_tax_amount( );
	}
	
	public function initiate_taxcloud( ){
		
		if( $GLOBALS['ec_cart_data']->cart_data->taxcloud_tax_amount != "" ){
			$this->tax_amount = $GLOBALS['ec_cart_data']->cart_data->taxcloud_tax_amount;
			$this->address_verified = $GLOBALS['ec_cart_data']->cart_data->taxcloud_address_verified;
			
		}else{
			$this->tax_amount = 0;
			$this->address_verified = 0;
			
		}
		
	}
	
	public function update_tax_amount( ){
		
		if( get_option( 'ec_option_tax_cloud_api_id' ) != "" && get_option( 'ec_option_tax_cloud_api_key' ) != "" ){
			
			$db = new ec_db( );
			$cartpage = new ec_cartpage( );
			
			$api_id = get_option( 'ec_option_tax_cloud_api_id' );
			$api_key = get_option( 'ec_option_tax_cloud_api_key' );
			$cart_id = $GLOBALS['ec_cart_data']->ec_cart_id;
			
			$customerID = $GLOBALS['ec_cart_data']->cart_data->user_id;
			if( $GLOBALS['ec_cart_data']->cart_data->is_guest )
				$customerID = $GLOBALS['ec_cart_data']->cart_data->guest_key;
			
			$cartitems = $this->get_tax_cloud_cartitems( $cartpage->order_totals->shipping_total, $cartpage->order_totals->discount_total );
			$origin = $this->get_tax_cloud_origin( );
			$destination = $this->get_tax_cloud_destination( );
			
			$parameters = array(	"apiLoginID" 		=> $api_id,
									"apiKey"			=> $api_key,
									"customerID"		=> $customerID,
									"cartID"			=> $cart_id,
									"cartItems"			=> $cartitems,
									"origin"			=> $origin,
									"destination"		=> $destination,
									"deliveredBySeller"	=> false,
									"exemptCert"		=> NULL );
			
			if( $destination ){
				
				$is_verified = $this->tax_cloud_address_verification( );
				
				// Address Verified, now return the rate
				if( $is_verified ){
					
					// Update Destination with the verified address
					$parameters["destination"] = $this->get_tax_cloud_destination( );
				
					$ch = curl_init( );
					curl_setopt( $ch, CURLOPT_URL, $this->get_tax_cloud_url( ) . "Lookup" );
					curl_setopt( $ch, CURLOPT_POST, true); 
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true); // tell curl to return data in a variable 
					curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
					curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Content-Length: ' . strlen( json_encode( $parameters ) ) ) );
					curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $parameters ) );								
					
					$response = json_decode( curl_exec( $ch ) );
					curl_close( $ch );
						
					$db->insert_response( 0, 0, "Tax Cloud Lookup", print_r( $response, true ) );
					
					if( $response->ResponseType == 0 ){
						// Invalid call, return 0
						$this->tax_amount = 0;
						
					}else{
						$total = 0;
						foreach( $response->CartItemsResponse as $cart_item ){
							$total = $total + doubleval( $cart_item->TaxAmount );
						}
						
						$this->tax_amount = $total;
						
					}
					
				}else{ // address not verified, so cannot return tax.
					
					$this->tax_amount = 0;
					
				}
				
			}else{
				
				$this->tax_amount = 0;
				
			}
			
			$GLOBALS['ec_cart_data']->cart_data->taxcloud_tax_amount = $this->tax_amount;
			$GLOBALS['ec_cart_data']->cart_data->taxcloud_address_verified = $this->address_verified;
			$GLOBALS['ec_cart_data']->save_session_to_db( );
		
		}
		
	}
	
	// Action from ec_order
	public function add_tax_cloud_order( $order_id, $cart, $order_totals, $user, $payment_type ){
		
		if( get_option( 'ec_option_tax_cloud_api_id' ) != "" && get_option( 'ec_option_tax_cloud_api_key' ) != "" ){
			
			$db = new ec_db( );
		
			$dateTimeauthorizedDate = gmdate(DATE_ATOM);
			$dateTimecapturedDate 	= gmdate(DATE_ATOM);
			
			$customerID = $GLOBALS['ec_cart_data']->cart_data->user_id;
			if( $GLOBALS['ec_cart_data']->cart_data->is_guest )
				$customerID = $GLOBALS['ec_cart_data']->cart_data->guest_key;
			
			$parameters = array( 	'apiLoginID' 		=> get_option( 'ec_option_tax_cloud_api_id' ),
									'apiKey' 			=> get_option( 'ec_option_tax_cloud_api_key' ),
									'customerID' 		=> $customerID,
									'cartID' 			=> $GLOBALS['ec_cart_data']->ec_cart_id,
									'orderID' 			=> $order_id,
									'dateAuthorized' 	=> $dateTimeauthorizedDate,
									'dateCaptured' 		=> $dateTimecapturedDate );
			
			$ch = curl_init( );
			curl_setopt( $ch, CURLOPT_URL,  $this->get_tax_cloud_url( ) . "Authorized" );
			curl_setopt( $ch, CURLOPT_POST, true); 
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true); // tell curl to return data in a variable 
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Content-Length: ' . strlen( json_encode( $parameters ) ) ) );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $parameters ) );								
			
			$response = json_decode( curl_exec( $ch ) );
			curl_close( $ch );
				
			$db->insert_response( 0, 0, "Tax Cloud Insert Order", print_r( $response, true ) );
			
			$GLOBALS['ec_cart_data']->cart_data->taxcloud_tax_amount = "";
			$GLOBALS['ec_cart_data']->cart_data->taxcloud_address_verified = 0;
			$GLOBALS['ec_cart_data']->save_session_to_db( );
			
		}
		
	}
	
	public function approve_tax_cloud_order( $order_id ){
		
		if( get_option( 'ec_option_tax_cloud_api_id' ) != "" && get_option( 'ec_option_tax_cloud_api_key' ) != "" ){
			
			$db = new ec_db( );
			
			$parameters = array( 	'apiLoginID' 		=> get_option( 'ec_option_tax_cloud_api_id' ),
									'apiKey' 			=> get_option( 'ec_option_tax_cloud_api_key' ),
									'orderID' 			=> $order_id );
			
			$ch = curl_init( );
			curl_setopt( $ch, CURLOPT_URL,  $this->get_tax_cloud_url( ) . "Captured" );
			curl_setopt( $ch, CURLOPT_POST, true); 
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true); // tell curl to return data in a variable 
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Content-Length: ' . strlen( json_encode( $parameters ) ) ) );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $parameters ) );								
			
			$response = json_decode( curl_exec( $ch ) );
			curl_close( $ch );
				
			$db->insert_response( 0, 0, "Tax Cloud Approve Order", print_r( $response, true ) );
			
		}
		
	}
	
	public function refund_tax_cloud_order( $order_id ){
		
		if( get_option( 'ec_option_tax_cloud_api_id' ) != "" && get_option( 'ec_option_tax_cloud_api_key' ) != "" ){
			
			$db = new ec_db( );
			$dateTimereturnedDate = gmdate(DATE_ATOM);
			
			$parameters = array( 	'apiLoginID' 		=> get_option( 'ec_option_tax_cloud_api_id' ),
									'apiKey' 			=> get_option( 'ec_option_tax_cloud_api_key' ),
									'orderID' 			=> $order_id,
									'returnedDate' 		=> $dateTimereturnedDate );
			
			$ch = curl_init( );
			curl_setopt( $ch, CURLOPT_URL,  $this->get_tax_cloud_url( ) . "Returned" );
			curl_setopt( $ch, CURLOPT_POST, true); 
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true); // tell curl to return data in a variable 
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Content-Length: ' . strlen( json_encode( $parameters ) ) ) );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $parameters ) );								
			
			$response = json_decode( curl_exec( $ch ) );
			curl_close( $ch );
				
			$db->insert_response( 0, 0, "Tax Cloud Refund Order", print_r( $response, true ) );
		
		}
		
	}
	
	private function get_tax_cloud_url( ){
		return "https://api.taxcloud.net/1.0/Taxcloud/";
	}
	
	public function tax_cloud_address_verification( ){
		
		$db = new ec_db( );
		
		$zip_split = explode( '-', $GLOBALS['ec_cart_data']->cart_data->shipping_zip );
		$zip5 = $GLOBALS['ec_cart_data']->cart_data->shipping_zip;
		if( count( $zip_split ) > 0 )
			$zip5 = $zip_split[0];
		
		$zip4 = "";
		if( count( $zip_split ) > 1 )
			$zip4 = $zip_split[1];
		
		$parameters = array( 	"apiLoginID" 	=> get_option( 'ec_option_tax_cloud_api_id' ),
								"apiKey" 		=> get_option( 'ec_option_tax_cloud_api_key' ),
								"Address1"		=> $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1,
								"Address2"		=> $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2,
								"City"			=> $GLOBALS['ec_cart_data']->cart_data->shipping_city,
								"State"			=> $GLOBALS['ec_cart_data']->cart_data->shipping_state,
								"Zip5"			=> $zip5,
								"Zip4"			=> $zip4
							);
		
								
		
		$ch = curl_init( $this->get_tax_cloud_url( ) . "VerifyAddress" );
		curl_setopt_array( $ch, array(	CURLOPT_POST			=> true,
										CURLOPT_RETURNTRANSFER	=> true,
										CURLOPT_SSL_VERIFYPEER	=> false,
										CURLOPT_HTTPHEADER		=> array( 'Content-Type: application/json' ),
										CURLOPT_POSTFIELDS		=> json_encode( $parameters ) ) );
										
		$response = curl_exec( $ch );
		curl_close( $ch );
		
		$response = json_decode( $response );
		$db->insert_response( 0, 0, "Tax Cloud VerifyAddress", print_r( $response, true ) );
		
		if( $response->ErrNumber == 0 ){
			$GLOBALS['ec_cart_data']->cart_data->shipping_zip = $response->Zip5 . '-' . $response->Zip4;
			if( $GLOBALS['ec_cart_data']->cart_data->shipping_selector == 'false' ){
				$GLOBALS['ec_cart_data']->cart_data->billing_zip = $response->Zip5 . '-' . $response->Zip4;
			}
			$this->address_verified = true;	
			
		}else{
            // We no longer require the verification, Tax Cloud suggests to continue with the address provided.
			// $this->address_verified = false;
            $this->address_verified = true;	
			
		}
		
		return $this->address_verified;
		
	}
	
	private function get_tax_cloud_cartitems( $shipping_total, $discount_total ){
		
		global $wpdb;
		$cartitems = array( );
			
		if( isset( $this->subscription_product ) ){
			
			$unit_price = $this->subscription_product->price - ( $discount_total / $this->subscription_product_quantity );
			
			$cartitems[] = array(	"Index"		=> 0,
									"TIC"		=> $this->subscription_product->TIC,
									"ItemID"	=> $this->subscription_product->model_number,
									"Price"		=> $unit_price,
									"Qty"		=> $this->subscription_product_quantity
								 );
			
		}else{
			$cart = $wpdb->get_results( $wpdb->prepare( "SELECT ec_tempcart.quantity, ec_product.price, ec_product.model_number, ec_product.TIC FROM ec_tempcart LEFT JOIN ec_product ON ec_product.product_id = ec_tempcart.product_id WHERE ec_tempcart.session_id = %s AND ec_product.is_taxable", $GLOBALS['ec_cart_data']->ec_cart_id ) );
			$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
			// Return empty array if no cart items exist.
			if( count( $cart->cart ) == 0 )
				return $cartitems;
				
			$discount_remaining = $discount_total;
			$onetime_added = 0;
			
			for( $i=0; $i<count( $cart->cart ); $i++ ){
				
				if( $discount_remaining <= 0 ){
					$unit_price = $cart->cart[$i]->unit_price;
					
				}else if( $discount_remaining > ( $cart->cart[$i]->unit_price * $cart->cart[$i]->quantity ) ){
					$discount_remaining = $discount_remaining - ( $cart->cart[$i]->quantity * ( $cart->cart[$i]->unit_price - .01 ) );
					$unit_price = .01;
				
				}else{
					$unit_price = ( $cart->cart[$i]->unit_price - ( $discount_remaining / $cart->cart[$i]->quantity ) );
					$discount_remaining = 0;
				
				}
				
				$cartitems[] = array(	
					"Index"		=> $i + $onetime_added,
					"TIC"		=> $cart->cart[$i]->TIC,
					"ItemID"	=> $cart->cart[$i]->model_number,
					"Price"		=> $unit_price,
					"Qty"		=> $cart->cart[$i]->quantity
				 );
									 
				if( $cart->cart[$i]->options_price_onetime > 0 ){
					$onetime_added++;
					$cartitems[] = array(	
						"Index"		=> $i + $onetime_added,
						"TIC"		=> $cart->cart[$i]->TIC,
						"ItemID"	=> $cart->cart[$i]->model_number,
						"Price"		=> $cart->cart[$i]->options_price_onetime,
						"Qty"		=> 1
					);
				}
			}
		}
		if( $shipping_total > 0 ){
			$cartitems[] = array( 	'Index' 	=> $i + $onetime_added, 
								    'TIC' 		=> '11010', 
									'ItemID' 	=> 'Shipping', 
									'Price' 	=> $shipping_total, 
									'Qty' 		=> 1 );
		}
		
		$ec_db = new ec_db( );
		$ec_db->insert_response( 0, 0, "TaxCloud Cart Items", print_r( $cartitems, true ) );
		
		
		return $cartitems;
		
	}
	
	private function get_tax_cloud_origin( ){
		
		$zip_split = explode( '-', get_option( 'ec_option_tax_cloud_zip' ) );
		$zip5 = get_option( 'ec_option_tax_cloud_zip' );
		if( count( $zip_split ) > 0 )
			$zip5 = $zip_split[0];
		
		$zip4 = "";
		if( count( $zip_split ) > 1 )
			$zip4 = $zip_split[1];
		
		$origin = array(	"Address1"	=> get_option( 'ec_option_tax_cloud_address' ),
							"City"		=> get_option( 'ec_option_tax_cloud_city' ),
							"State"		=> get_option( 'ec_option_tax_cloud_state' ),
							"Zip5"		=> $zip5,
							"Zip4"		=> $zip4
						 );
		return $origin;
							 
	}
	
	private function get_tax_cloud_destination( ){
		
		$zip_split = explode( '-', $GLOBALS['ec_cart_data']->cart_data->shipping_zip );
		$zip5 = $GLOBALS['ec_cart_data']->cart_data->shipping_zip;
		if( count( $zip_split ) > 0 )
			$zip5 = $zip_split[0];
		
		$zip4 = "";
		if( count( $zip_split ) > 1 )
			$zip4 = $zip_split[1];
		
		$parameters = array( 	"Address1"		=> $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1,
								"Address2"		=> $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2,
								"City"			=> $GLOBALS['ec_cart_data']->cart_data->shipping_city,
								"State"			=> $GLOBALS['ec_cart_data']->cart_data->shipping_state,
								"Zip5"			=> $zip5,
								"Zip4"			=> $zip4
							);
		return $parameters; 
		
		
	}
	
}
endif; // End if class_exists check


function wpeasycart_taxcloud( ){

	return ec_taxcloud::instance( );

}

$GLOBALS['wpeasycart_taxcloud'] = wpeasycart_taxcloud( );

?>