<?php
	
class ec_fraktjakt{
	
	private $fraktjakt_customer_id;										// Your Fraktjakt Customer ID
	private $fraktjakt_login_key;										// Your Fraktjakt Login Key
	private $fraktjakt_conversion_rate;									// Fraktjakt Conversion Rate
	private $fraktjakt_test_mode;										// Fraktjakt Test Mode
	private $fraktjakt_url;												// API URL test or production
	private $order_url;													// API URL for orders, test or production
	private $tracking_url;												// API URL for tracking, test or production
	private $cart;														// ec_cart object
	
	function __construct( ){
		$this->cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$this->fraktjakt_customer_id = $GLOBALS['ec_setting']->get_fraktjakt_customer_id( );
		$this->fraktjakt_login_key = $GLOBALS['ec_setting']->get_fraktjakt_login_key( );	
		$this->fraktjakt_conversion_rate = $GLOBALS['ec_setting']->get_fraktjakt_conversion_rate( );
		$this->fraktjakt_test_mode = $GLOBALS['ec_setting']->get_fraktjakt_test_mode( );
		
		if( $this->fraktjakt_test_mode ){
			$this->fraktjakt_url 	= "http://api2.fraktjakt.se/fraktjakt/query_xml";
			$this->order_url 		= "http://api2.fraktjakt.se/orders/order_xml";
			$this->tracking_url 	= "http://api2.fraktjakt.se/trace/xml_trace";
		}else{
			$this->fraktjakt_url 	= "http://api1.fraktjakt.se/fraktjakt/query_xml";
			$this->order_url 		= "http://api1.fraktjakt.se/orders/order_xml";
			$this->tracking_url 	= "http://api1.fraktjakt.se/trace/xml_trace";
		}
	}
		
	public function get_shipping_options( ){
		
		$ship_data = $this->get_shipper_data( );
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $this->fraktjakt_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'xml=' . urlencode( $ship_data ) );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		curl_close ($ch);
		
		return $this->process_response( $response );
		
	}
	
	public function get_shipping_options_test( $user ){
		$this->user = $user;
		
		$ship_data = $this->get_test_shipper_data( );
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $this->fraktjakt_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'xml=' . urlencode( $ship_data ) );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);
		curl_close ($ch);
		
		return $response;
	}
		
	public function insert_shipping_order( $shipment_id, $shipping_product_id ){
		
		$ship_data = $this->get_shipper_order_data( $shipment_id, $shipping_product_id );
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $this->order_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'xml=' . urlencode( $ship_data ) );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);

		curl_close ($ch);
		
		return $this->process_order_response( $response );
		
	}
	
	public function get_shipping_status( $shipment_id ){
		
		$ch = curl_init( );
		curl_setopt($ch, CURLOPT_URL, $this->tracking_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( 	
								array( 	'shipment_id' => $shipment_id, 
										'consigner_id' => $this->fraktjakt_customer_id, 
										'consigner_key' => $this->fraktjakt_login_key ) ) );
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30);
		$response = curl_exec($ch);

		curl_close ($ch);
		
		return $this->process_tracking_response( $response );
		
	}
	
	private function get_shipper_data( ){
		
		$shipper_data = '<?xml version="1.0" encoding="ISO-8859-1"?>
						<shipment>
							<value>' . $this->convert_to_sek( $this->cart->subtotal ) . '</value>
							<consignor>
								<id>' . $this->fraktjakt_customer_id . '</id>
								<key>' . $this->fraktjakt_login_key . '</key>
								<encoding>IOS-8859-1</encoding>
							</consignor>
							<parcels>';
							
							// Generate Product List
							$products = array( );
							foreach( $this->cart->cart as $cartitem ){
								// Each quantity item is a new product in the shipping world
								for( $i=0; $i<$cartitem->quantity; $i++ ){
									$products[] = array( 'width' 	=> $cartitem->width,
														 'height'	=> $cartitem->height,
													 	 'length'	=> $cartitem->length,
													 	 'weight'	=> $cartitem->weight );
								}// close quantity loop
							}// close cart item loop
							
							$parcel = $this->calculate_parcel( $products );
							
							// Add parcel to the query
							$shipper_data .= '
								<parcel>
									<weight>' . $parcel['weight'] . '</weight>
									<length>' . $parcel['length']  . '</length>
									<width>' . $parcel['width']  . '</width>
									<height>' . $parcel['height']  . '</height>
								</parcel>';
							
							$shipper_data .= '
							</parcels>';
		
		if( $GLOBALS['ec_user']->shipping->address_line_1 != "" )	{
			
			$shipper_data .= '
							<address>
								<street_address_1>' . $GLOBALS['ec_user']->shipping->address_line_1 . '</street_address_1>
								<postal_code>' . $GLOBALS['ec_user']->shipping->zip . '</postal_code>
								<city_name>' . $GLOBALS['ec_user']->shipping->city . '</city_name>
								<country_code>' . $GLOBALS['ec_user']->shipping->country . '</country_code>';
							
							if( $this->user_has_state( ) ){
								$shipper_data .= '
								<country_subdivision_code>' . $GLOBALS['ec_user']->shipping->state . '</country_subdivision_code>';
							}
							
							$shipper_data .= '
							</address>';
			
		}else{
		
			$shipper_data .= '
							<address>
								<street_address_1>' . $GLOBALS['ec_setting']->get_fraktjakt_address( ) . '</street_address_1>
								<postal_code>' . $GLOBALS['ec_setting']->get_fraktjakt_zip( ) . '</postal_code>
								<city_name>' . $GLOBALS['ec_setting']->get_fraktjakt_city( ) . '</city_name>
								<country_code>' . $GLOBALS['ec_setting']->get_fraktjakt_country( ) . '</country_code>';
							
							if( $GLOBALS['ec_setting']->get_fraktjakt_state( ) != "" ){
								$shipper_data .= '
								<country_subdivision_code>' . $GLOBALS['ec_setting']->get_fraktjakt_state( ) . '</country_subdivision_code>';
							}
							
							$shipper_data .= '
							</address>';
							
		}
		
		$shipper_data .= '
							
						</shipment>';
						
						
		return $shipper_data;
	}
	
	private function get_test_shipper_data( ){
		
		$shipper_data = '<?xml version="1.0" encoding="ISO-8859-1"?>
						<shipment>
							<value>' . $this->convert_to_sek( "10.00" ) . '</value>
							<consignor>
								<id>' . $this->fraktjakt_customer_id . '</id>
								<key>' . $this->fraktjakt_login_key . '</key>
								<encoding>IOS-8859-1</encoding>
							</consignor>
							<parcels>';
							
							// Generate Product List
							$products = array( );
							$products[] = array( 'width' 	=> 10,
												 'height'	=> 10,
												 'length'	=> 10,
												 'weight'	=> 3 );
							
							$parcel = $this->calculate_parcel( $products );
							
							// Add parcel to the query
							$shipper_data .= '
								<parcel>
									<weight>' . $parcel['weight'] . '</weight>
									<length>' . $parcel['length']  . '</length>
									<width>' . $parcel['width']  . '</width>
									<height>' . $parcel['height']  . '</height>
								</parcel>';
							
							$shipper_data .= '
							</parcels>
							<address>
								<street_address_1>' . $GLOBALS['ec_user']->shipping->address_line_1 . '</street_address_1>
								<postal_code>' . $GLOBALS['ec_user']->shipping->zip . '</postal_code>
								<city_name>' . $GLOBALS['ec_user']->shipping->city . '</city_name>
								<country_code>' . $GLOBALS['ec_user']->shipping->country . '</country_code>';
							
							if( $this->user_has_state( ) ){
								$shipper_data .= '
								<country_subdivision_code>' . $GLOBALS['ec_user']->shipping->state . '</country_subdivision_code>';
							}
							
							$shipper_data .= '
							</address>
							
						</shipment>';
						
						
		return $shipper_data;
	}
	
	private function get_shipper_order_data( $shipment_id, $shipping_product_id ){
		
		$shipper_data = '<?xml version="1.0" encoding="ISO-8859-1"?>
						<OrderSpecification>
							<value>' . $this->convert_to_sek( $this->cart->subtotal ) . '</value>
							<consignor>
								<id>' . $this->fraktjakt_customer_id . '</id>
								<key>' . $this->fraktjakt_login_key . '</key>
								<encoding>IOS-8859-1</encoding>
							</consignor>
							<shipment_id>' . $shipment_id . '</shipment_id>
							<shipping_product_id>' . $shipping_product_id . '</shipping_product_id>
							<commodities>';
							foreach( $this->cart->cart as $cartitem ){
								$shipper_data .= '
								<commodity>
									<name>' . $cartitem->title . '</name>
									<quantity>' . $cartitem->quantity . '</quantity>
									<quantity_units>EA</quantity_units>
									<description>' . substr( $cartitem->description, 0, 128 ) . '</description>
									<weight>' . ($cartitem->weight * $cartitem->quantity) . '</weight>
									<unit_price>' . $this->convert_to_sek( $cartitem->unit_price ) . '</unit_price>
								</commodity>
								';
							}// close cart item loop
							$shipper_data .= '
							</commodities>
							<recipient>
								<name_to>' . $GLOBALS['ec_user']->shipping->first_name . ' ' . $GLOBALS['ec_user']->shipping->last_name . '</name_to>
								<telephone_to>' . $GLOBALS['ec_user']->shipping->phone . '</telephone_to>
								<email_to>' . $GLOBALS['ec_user']->email . '</email_to>
							</recipient>
						</OrderSpecification>';
						
						
		return $shipper_data;
	}
	
	private function convert_to_sek( $price ){
		return $price/$this->fraktjakt_conversion_rate;
	}
	
	private function convert_from_sek( $price ){
		return $price * $this->fraktjakt_conversion_rate;
	}
	
	private function user_has_state( ){
		$state = $GLOBALS['ec_user']->shipping->state;
		if( $state != "" )
			return true;
		else
			return false;
	}
	
	private function calculate_parcel( $products ){
 
		// Create an empty package
		$package_dimensions = array( 0, 0, 0 );
		$package_weight = 0;
		
		// Step through each product
		foreach( $products as $product ){
		
			// Create an array of product dimensions
			$product_dimensions = array( $product['width'], $product['height'], $product['length'] );
			
			// Twist and turn the item, longest side first ([0]=length, [1]=width, [2]=height)
			rsort( $product_dimensions, SORT_NUMERIC); // Sort $product_dimensions by highest to lowest
			
			// Package height + item height
			$package_dimensions[2] += $product_dimensions[2];
			
			// If this is the widest item so far, set item width as package width
			if($product_dimensions[1] > $package_dimensions[1]) 
				
				$package_dimensions[1] = $product_dimensions[1];
			
			// If this is the longest item so far, set item length as package length
			if($product_dimensions[0] > $package_dimensions[0]) 
				$package_dimensions[0] = $product_dimensions[0];
			
			// Twist and turn the package, longest side first ([0]=length, [1]=width, [2]=height)
			rsort( $package_dimensions, SORT_NUMERIC );
			
			// Add to total weight
			$package_weight = $package_weight + $product['weight'];
		}
		
		$parcel = array( 	'weight' 	=> $package_weight,
							'width'		=> $package_dimensions[0],
							'height'	=> $package_dimensions[1],
							'length'	=> $package_dimensions[2] );
		
		return $parcel;
	}
	
	private function process_response( $result ){
		
		try{
			$xml = new SimpleXMLElement($result);
			$shipping_options = array( );
			
			$shipping_products = $xml->shipping_products->shipping_product;
			foreach( $shipping_products as $shipping_product ){
				$shipping_options[] = array( "shipment_id"	=> $xml->id,
											 "id"			=> $shipping_product->id,
											 "description"	=> $shipping_product->description,
											 "price"		=> $this->convert_from_sek( $shipping_product->price ),
											 "arrival_time"	=> $shipping_product->arrival_time );
			}
			
			return $shipping_options;
		}catch( Exception $e ){
			return "";
		}
		
	}
	
	private function process_order_response( $result ){
		
		$xml = new SimpleXMLElement($result);
		
		if( $xml->code == '0' || $xml->code == '1' ){
			return array(	'shipment_id' 	=> $xml->shipment_id,
							'order_id'		=> $xml->order_id );
		}else{
			return false;
		}
	}
	
	private function process_tracking_response( $result ){
		
		$xml = new SimpleXMLElement($result);
		
		$fraktjakt_id = -1;
		
		if( isset( $xml->shipping_states ) && isset( $xml->shipping_states->shipping_state ) && isset( $xml->shipping_states->shipping_state->fraktjakt_id ) ){
			$fraktjakt_id = $xml->shipping_states->shipping_state->fraktjakt_id;
		}
		
		if( $fraktjakt_id == 0 )
			return "Hanteras av avsändaren";
		
		else if( $fraktjakt_id == 1 )
			return "Avsänt";
		
		else if( $fraktjakt_id == 2 )
			return "Levererat";
		
		else if( $fraktjakt_id == 3 )
			return "Kvitterat";
		
		else if( $fraktjakt_id == 4 )
			return "Returnerat";
			
		else
			return "";
			
		
	}
	
	public function validate_address( ){
		return true;
	}
}	
?>