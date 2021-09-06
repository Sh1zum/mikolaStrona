<?php

class ec_shipping{
	protected $mysqli;											// ec_db structure
	public $shipper;											// ec_shipper structure
	public $has_live_rates;										// Track having live rates.
	
	private $fraktjakt;											// Optional ec_fraktjakt
	
	private $display_type;										// VARCHAR, methods = [RADIO, SELECT, DIV]
	
	private $price_based = array();								// array of array[trigger_rate, shipping_rate] 
	private $weight_based = array();							// array of array[trigger_rate, shipping_rate]
	private $method_based = array();							// array of array[shipping_rate, shipping_label, shippingrate_id]
	private $quantity_based = array();							// array of array[trigger_rate, shipping_rate] 
	private $percentage_based = array();						// array of array[trigger_rate, percentage] 
	private $live_based = array();								// array of array[shipping_code, shipping_label, shippingrate_id, ship_type]
	private $fraktjakt_shipping_options;						// Optional array of shipping options in array( shipment_id, id, description, price, arrival_time)
	
	private $handling;											// FLOAT 11,2
	
	public $subtotal;											// float 7,2
	private $weight;											// float 7,2
	private $width;												// Float 7,2
	private $height;											// Float 7,2
	private $length;											// Float 7,2
	private $quantity;											// float 7,2
	private $express_price;										// float 7,2
	private $ship_express;										// BOOL
	private $destination_zip;									// VARCHAR
	private $destination_state;								    // VARCHAR(2)
	private $destination_country;								// VARCHAR(2)
	
	private $cart;												// Array of ec_cartitem
	
	public $shipping_method;									// shipping_method option
	
	public $shipping_promotion_text;							// TEXT
	
	private $freeshipping;										// Boolean
	
	function __construct( $subtotal, $weight, $quantity = 1, $display_type = 'RADIO', $freeshipping = false, $length = 1, $width = 1, $height = 1, $cart = array( ) ){
		$this->mysqli = new ec_db();
		$this->shipping_method = $GLOBALS['ec_setting']->get_shipping_method( );
		
		$this->cart = $cart;
		
		$email_user = "";
		
		if( $this->shipping_method == 'live' )
			$this->shipper = new ec_shipper( );
			
		$this->freeshipping = $freeshipping;
		
		if( get_option( 'ec_option_use_shipping' ) ){
			$setting_row = $GLOBALS['ec_setting']->setting_row;
			$this->handling = $setting_row->shipping_handling_rate;
			$shipping_rows = $this->mysqli->get_shipping_data( );
			
			// Set the destination zip code
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_zip ) && $GLOBALS['ec_cart_data']->cart_data->shipping_zip != "" )
				$this->destination_zip = $GLOBALS['ec_cart_data']->cart_data->shipping_zip;
			
			else if( isset( $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip) && $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip != "" )
				$this->destination_zip = $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip;
			
			else if( $GLOBALS['ec_user'] && $GLOBALS['ec_user']->shipping && $GLOBALS['ec_user']->shipping->zip )
				$this->destination_zip = $GLOBALS['ec_user']->shipping->zip;
				
			// Set the destination state code
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_state ) && $GLOBALS['ec_cart_data']->cart_data->shipping_state != "" )
				$this->destination_state = $GLOBALS['ec_cart_data']->cart_data->shipping_state;
			
			else if( isset( $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_state ) && $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_state != "" )
				$this->destination_state = $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_state;
			
			else if( $GLOBALS['ec_user'] && $GLOBALS['ec_user']->shipping && $GLOBALS['ec_user']->shipping->state )
				$this->destination_state = $GLOBALS['ec_user']->shipping->state;
				
			// Set the destination country code
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_country ) && $GLOBALS['ec_cart_data']->cart_data->shipping_country != "" )
				$this->destination_country = $GLOBALS['ec_cart_data']->cart_data->shipping_country;
			
			else if( isset( $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_country ) && $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_country != "" )
				$this->destination_country = $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_country;
			
			else if( $GLOBALS['ec_user'] && $GLOBALS['ec_user']->shipping && $GLOBALS['ec_user']->shipping->country )
				$this->destination_country = $GLOBALS['ec_user']->shipping->country;
			
			// Fraktjakt Shipping Info	
			if( $this->shipping_method == "fraktjakt" ){
				$this->fraktjakt = new ec_fraktjakt( );
				$this->fraktjakt_shipping_options = $this->fraktjakt->get_shipping_options( );
			}
			
			$zone_obj = $this->mysqli->get_zone_ids( $this->destination_country, $this->destination_state );
			$zones = array();
			foreach( $zone_obj as $zone ){
				$zones[] = $zone->zone_id;
			}
			
			foreach( $shipping_rows as $shipping_row ){
				
				// Price and Zoned Based
				if( $shipping_row->is_price_based && $shipping_row->zone_id > 0 ){
					if( in_array( $shipping_row->zone_id, $zones ) )
						array_push( $this->price_based, array( $shipping_row->trigger_rate, $shipping_row->shipping_rate ) );
				
				// Price Based		
				}else if( $shipping_row->is_price_based )					
					array_push( $this->price_based, array( $shipping_row->trigger_rate, $shipping_row->shipping_rate ) );
				
				// Weight and Zoned Based
				else if( $shipping_row->is_weight_based && $shipping_row->zone_id > 0 ){
					if( in_array( $shipping_row->zone_id, $zones ) )
						array_push( $this->weight_based, array( $shipping_row->trigger_rate, $shipping_row->shipping_rate ) );
				
				// Weight Based
				}else if( $shipping_row->is_weight_based )			
					array_push( $this->weight_based, array( $shipping_row->trigger_rate, $shipping_row->shipping_rate ) );
				
				// Method and Zoned Based
				else if( $shipping_row->is_method_based && $shipping_row->zone_id > 0 ){
					if( in_array( $shipping_row->zone_id, $zones ) )
						array_push( $this->method_based, array( $shipping_row->shipping_rate, $GLOBALS['language']->convert_text( $shipping_row->shipping_label ), $shipping_row->shippingrate_id, $shipping_row->free_shipping_at ) );
					
				// Method Based	
				}else if( $shipping_row->is_method_based )			
					array_push( $this->method_based, array( $shipping_row->shipping_rate, $GLOBALS['language']->convert_text( $shipping_row->shipping_label ), $shipping_row->shippingrate_id, $shipping_row->free_shipping_at ) );
					
				// Quantity and Zoned Based
				else if( $shipping_row->is_quantity_based && $shipping_row->zone_id > 0 ){
					if( in_array( $shipping_row->zone_id, $zones ) )
						array_push( $this->quantity_based, array( $shipping_row->trigger_rate, $shipping_row->shipping_rate ) );
					
				// Quantity Based	
				}else if( $shipping_row->is_quantity_based )			
					array_push( $this->quantity_based, array( $shipping_row->trigger_rate, $shipping_row->shipping_rate ) );
				
				// Percentage and Zoned Based
				else if( $shipping_row->is_percentage_based && $shipping_row->zone_id > 0 ){
					if( in_array( $shipping_row->zone_id, $zones ) )
						array_push( $this->percentage_based, array( $shipping_row->trigger_rate, $shipping_row->shipping_rate ) );
					
				// Percentage Based	
				}else if( $shipping_row->is_percentage_based )			
					array_push( $this->percentage_based, array( $shipping_row->trigger_rate, $shipping_row->shipping_rate ) );
				
				// Live and Zoned Based
				else if( $this->is_live_based( $shipping_row ) && $shipping_row->zone_id > 0 ){
					if( in_array( $shipping_row->zone_id, $zones ) )
						array_push( $this->live_based, array( $shipping_row->shipping_code, $GLOBALS['language']->convert_text( $shipping_row->shipping_label ), $shipping_row->shippingrate_id, $this->get_live_type( $shipping_row ), $shipping_row->shipping_override_rate, $shipping_row->free_shipping_at ) );
				
				// Live Based	
				}else if( $this->is_live_based( $shipping_row ) ){	
					array_push( $this->live_based, array( $shipping_row->shipping_code, $GLOBALS['language']->convert_text( $shipping_row->shipping_label ), $shipping_row->shippingrate_id, $this->get_live_type( $shipping_row ), $shipping_row->shipping_override_rate, $shipping_row->free_shipping_at ) );
				}
			}
			
			$this->live_based = apply_filters( 'wpeasycart_live_based_codes', $this->live_based );
			$this->method_based = apply_filters( 'wpeasycart_method_based_shipping', $this->method_based );
			
			$this->subtotal = $subtotal - $GLOBALS['wpeasycart_current_coupon_discount'];
			$this->weight = $weight;
			$this->width = $width;
			$this->height = $height;
			$this->length = $length;
			$this->quantity = $quantity;
			$this->express_price = $GLOBALS['ec_setting']->get_setting( "shipping_expedite_rate" );
			if( isset( $GLOBALS['ec_cart_data']->cart_data->expedited_shipping ) && $GLOBALS['ec_cart_data']->cart_data->expedited_shipping != "" ){
				$this->ship_express = $GLOBALS['ec_cart_data']->cart_data->expedited_shipping;
			}else{
				$this->ship_express = false;
			}
			
			$this->display_type = $display_type;
		}
	}
	
	private function is_live_based( $shipping_row ){
		if( $shipping_row->is_ups_based || $shipping_row->is_usps_based || $shipping_row->is_fedex_based || $shipping_row->is_auspost_based || $shipping_row->is_dhl_based || $shipping_row->is_canadapost_based )
			return true;
		else
			return false;
	}
	
	private function get_live_type( $shipping_row ){
		if( $shipping_row->is_ups_based )
			return "ups";
		else if( $shipping_row->is_usps_based )
			return "usps";
		else if( $shipping_row->is_fedex_based )
			return "fedex";
		else if( $shipping_row->is_auspost_based )
			return "auspost";
		else if( $shipping_row->is_dhl_based )
			return "dhl";
		else if( $shipping_row->is_canadapost_based )
			return "canadapost";
		else
			return "none";
	}
	
	public function get_shipping_options( $standard_text, $express_text ){
		
		if( $this->shipping_method == "price" )
			return $this->get_price_based_shipping_options( $standard_text, $express_text );
			
		else if( $this->shipping_method == "weight" )
			return $this->get_weight_based_shipping_options( $standard_text, $express_text );
			
		else if( $this->shipping_method == "method" )
			return $this->get_method_based_shipping_options( $standard_text, $express_text );
			
		else if( $this->shipping_method == "quantity" )
			return $this->get_quantity_based_shipping_options( $standard_text, $express_text );
			
		else if( $this->shipping_method == "percentage" )
			return $this->get_percentage_based_shipping_options( $standard_text, $express_text );
			
		else if( $this->shipping_method == "live" ){
			$shipping_content = $this->get_live_based_shipping_options( $standard_text, $express_text );;
			if( $shipping_content != "" ){
				$this->has_live_rates = true;
				return $shipping_content;
			}else{
				$this->has_live_rates = false;
				return "<div class='ec_cart_no_shipping_methods'>" .  $GLOBALS['language']->get_text( 'cart_shipping_method', 'cart_shipping_no_rates_available' ) . "</div>";
			}
		}else if( $this->shipping_method == "fraktjakt" )
			return $this->get_fraktjakt_based_shipping_options( );
			
	}
	
	private function get_price_based_rate( ){
		for( $i=0; $i<count( $this->price_based ); $i++ ){
			if( $this->subtotal >= $this->price_based[$i][0] ){
				return $this->price_based[$i][1];
			}
		}
	}
	
	private function get_weight_based_rate( ){
		for( $i=0; $i<count( $this->weight_based ); $i++ ){
			if( $this->weight >= $this->weight_based[$i][0] ){
				return $this->weight_based[$i][1];
			}
		}
	}
	
	private function get_quantity_based_rate( ){
		for( $i=0; $i<count( $this->quantity_based ); $i++ ){
			if( $this->quantity >= $this->quantity_based[$i][0] ){
				return $this->quantity_based[$i][1];
			}
		}
	}
	
	private function get_percentage_based_rate( ){
		for( $i=0; $i<count( $this->percentage_based ); $i++ ){
			if( $this->subtotal >= $this->percentage_based[$i][0] ){
				return $this->subtotal * $this->percentage_based[$i][1] / 100;
			}
		}
	}
	
	public function get_shipping_rate_data( $standard_text, $express_text, $multiplier = 100 ){
		
		$rates = array( );
		
		if( $this->shipping_method == "price" || $this->shipping_method == "weight" || $this->shipping_method == "quantity" || $this->shipping_method == "percentage" ){
			
			if( $this->shipping_method == "price" )
				$standard_price = $this->get_price_based_rate( );
			else if( $this->shipping_method == "weight" )
				$standard_price = $this->get_weight_based_rate( );
			else if( $this->shipping_method == "quantity" )
				$standard_price = $this->get_quantity_based_rate( );
			else if( $this->shipping_method == "percentage" )
				$standard_price = $this->get_percentage_based_rate( );
			
			$coupon_code = '';
			if( $GLOBALS['ec_cart_data']->cart_data->coupon_code != "" ){
				$coupon_code = $GLOBALS['ec_cart_data']->cart_data->coupon_code;
			}
			$discount = new ec_discount( array( ), 0.00, $standard_price, $coupon_code, "", 0 );
			$shipping_discount = $discount->shipping_discount;
			$standard_price += $this->handling - $shipping_discount;
			
			if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == 'free' ){
				if( get_option( 'ec_option_add_local_pickup' ) ){
					$rates[] = (object) array(
						'label'		=> $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_free' ),
						'amount'	=> (int) 0,
						'id' 		=> 'free'
					);
				}
			}
			
			if( $GLOBALS['ec_cart_data']->cart_data->expedited_shipping == 'shipexpress' ){
				if( $this->express_price > 0 ){
					$rates[] = (object) array(
						'label'		=> $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_express' ),
						'amount'	=> ( $multiplier == 100 ) ? (int) number_format( ( $standard_price + $this->express_price ) * $multiplier, 0, '', '' ) : number_format( $standard_price + $this->express_price, 2, '.', '' ),
						'id' 		=> 'shipexpress'
					);
				}
			}
			
			$rates[] = (object) array(
				'label'		=> $standard_text,
				'amount'	=> ( $multiplier == 100 ) ? (int) number_format( $standard_price * $multiplier, 0, '', '' ) : number_format( $standard_price, 2, '.', '' ),
				'id' 		=> 'standard'
			);
				
			if( $GLOBALS['ec_cart_data']->cart_data->expedited_shipping != 'shipexpress' ){
				if( $this->express_price > 0 ){
					$rates[] = (object) array(
						'label'		=> $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_express' ),
						'amount'	=> ( $multiplier == 100 ) ? (int) number_format( ( $standard_price + $this->express_price ) * $multiplier, 0, '', '' ) : number_format( $standard_price + $this->express_price, 2, '.', '' ),
						'id' 		=> 'shipexpress'
					);
				}
			}
			
			if( $GLOBALS['ec_cart_data']->cart_data->shipping_method != 'free' ){
				if( get_option( 'ec_option_add_local_pickup' ) ){
					$rates[] = (object) array(
						'label'		=> $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_free' ),
						'amount'	=> (int) 0,
						'id' 		=> 'free'
					);
				}
			}
			
		}else if( $this->shipping_method == "method" ){
			
			if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == 'free' ){
				if( get_option( 'ec_option_add_local_pickup' ) ){
					$rates[] = (object) array(
						'label'		=> $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_free' ),
						'amount'	=> (int) 0,
						'id' 		=> 'free'
					);
				}
			}
			
			for( $i=0; $i<count( $this->method_based ); $i++ ){
				if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == $this->method_based[$i][2] ){
					if( $this->method_based[$i][3] > 0 && $this->subtotal >= $this->method_based[$i][3] )
						$rate = 0;
						
					else if( get_option( 'ec_option_static_ship_items_seperately' ) )
						$rate = ( $this->method_based[$i][0] * $this->quantity ) + $this->handling;
						
					else
						$rate = $this->method_based[$i][0] + $this->handling;
					
					$rates[] = (object) array(
						'label'		=> $this->method_based[$i][1],
						'amount'	=> ( $multiplier == 100 ) ? (int) number_format( $rate * $multiplier, 0, '', '' ) : number_format( $rate, 2, '.', '' ),
						'id' 		=> $this->method_based[$i][2]
					);
				}
			}
			
			for( $i=0; $i<count( $this->method_based ); $i++ ){
				if( $GLOBALS['ec_cart_data']->cart_data->shipping_method != $this->method_based[$i][2] ){
					if( $this->method_based[$i][3] > 0 && $this->subtotal >= $this->method_based[$i][3] )
						$rate = 0;
						
					else if( get_option( 'ec_option_static_ship_items_seperately' ) )
						$rate = ( $this->method_based[$i][0] * $this->quantity ) + $this->handling;
						
					else
						$rate = $this->method_based[$i][0] + $this->handling;
					
					$rates[] = (object) array(
						'label'		=> $this->method_based[$i][1],
						'amount'	=> ( $multiplier == 100 ) ? (int) number_format( $rate * $multiplier, 0, '', '' ) : number_format( $rate, 2, '.', '' ),
						'id' 		=> $this->method_based[$i][2]
					);
				}
			}
				
			if( $GLOBALS['ec_cart_data']->cart_data->shipping_method != 'free' ){
				if( get_option( 'ec_option_add_local_pickup' ) ){
					$rates[] = (object) array(
						'label'		=> $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_free' ),
						'amount'	=> (int) 0,
						'id' 		=> 'free'
					);
				}
			}
			
		}else if( $this->shipping_method == "live" ){
			if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == 'free' ){
				if( get_option( 'ec_option_add_local_pickup' ) ){
					$rates[] = (object) array(
						'label'		=> $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_free' ),
						'amount'	=> (int) 0,
						'id' 		=> 'free'
					);
				}
			}
			
            $ret_string = '';
            $count = 0;
			for( $i=0; $i<count( $this->live_based ); $i++ ){
				if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == $this->live_based[$i][2] ){
					$service_days = 0;
					if( $this->live_based[$i][4] != NULL && get_option( 'ec_option_live_override_always' ) ){
						if( $this->live_based[$i][4] == 0 )
							$rate = "FREE";
						else
							$rate = $this->live_based[$i][4];
							
					}else if( $this->live_based[$i][5] > 0 && $this->subtotal >= $this->live_based[$i][5] ){ // Shipping free at rate
						$rate = "FREE";
						
					}else{
						$rate = $this->shipper->get_rate( $this->live_based[$i][3], $this->live_based[$i][0] );
						$service_days = $this->shipper->get_service_days( $this->live_based[$i][3], $this->live_based[$i][0] );
					}
					
					if( $rate != "ERROR" ){
						$ret_string .= $this->get_live_based_radio( $count, $i, $rate, $service_days );
						
						
						if( $rate == "FREE" || $rate == "free" )
							$rate = 0;
						else
							$rate = doubleval( $rate ) + doubleval( $this->handling );
					
						$id = $this->live_based[$i][2];
						$label = $this->live_based[$i][1];
						if( $service_days > 0 && get_option( 'ec_option_show_delivery_days_live_shipping' ) )
							$label .= " (" . $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'delivery_in' ) . " " . $service_days . "-" . ($service_days+1) . " " . $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'delivery_days' ) . ")";
						
						if( $this->live_based[$i][4] != NULL && !get_option( 'ec_option_live_override_always' ) ){
                            if( $this->live_based[$i][4] == 0 )
                                $rate = "FREE";
                            else
                                $rate = $this->live_based[$i][4];
                        }
                        
						$rates[] = (object) array(
							'label'		=> $label,
							'amount'	=> ( $multiplier == 100 ) ? (int) number_format( $rate * $multiplier, 0, '', '' ) : number_format( $rate, 2, '.', '' ),
							'id' 		=> $id
						);
					}
				}
			}
			
			for( $i=0; $i<count( $this->live_based ); $i++ ){
				if( $GLOBALS['ec_cart_data']->cart_data->shipping_method != $this->live_based[$i][2] ){
					$service_days = 0;
					if( $this->live_based[$i][4] != NULL && get_option( 'ec_option_live_override_always' ) ){
						if( $this->live_based[$i][4] == 0 )
							$rate = "FREE";
						else
							$rate = $this->live_based[$i][4];
							
					}else if( $this->live_based[$i][5] > 0 && $this->subtotal >= $this->live_based[$i][5] ){ // Shipping free at rate
						$rate = "FREE";
						
					}else{
						$rate = $this->shipper->get_rate( $this->live_based[$i][3], $this->live_based[$i][0] );
						$service_days = $this->shipper->get_service_days( $this->live_based[$i][3], $this->live_based[$i][0] );
					}
					
					if( $rate != "ERROR" ){
						$ret_string .= $this->get_live_based_radio( $count, $i, $rate, $service_days );
						
						
						if( $rate == "FREE" || $rate == "free" )
							$rate = 0;
						else
							$rate = doubleval( $rate ) + doubleval( $this->handling );
					
						$id = $this->live_based[$i][2];
						$label = $this->live_based[$i][1];
						if( $service_days > 0 && get_option( 'ec_option_show_delivery_days_live_shipping' ) )
							$label .= " (" . $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'delivery_in' ) . " " . $service_days . "-" . ($service_days+1) . " " . $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'delivery_days' ) . ")";
						
						if( $this->live_based[$i][4] != NULL && !get_option( 'ec_option_live_override_always' ) ){
                            if( $this->live_based[$i][4] == 0 )
                                $rate = "FREE";
                            else
                                $rate = $this->live_based[$i][4];
                        }
						
						$rates[] = (object) array(
							'label'		=> $label,
							'amount'	=> ( $multiplier == 100 ) ? (int) number_format( $rate * $multiplier, 0, '', '' ) : number_format( $rate, 2, '.', '' ),
							'id' 		=> $id
						);
					}
				}
			}
				
			if( $GLOBALS['ec_cart_data']->cart_data->shipping_method != 'free' ){
				if( get_option( 'ec_option_add_local_pickup' ) ){
					$rates[] = (object) array(
						'label'		=> $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_free' ),
						'amount'	=> (int) 0,
						'id' 		=> 'free'
					);
				}
			}
			
		}else if( $this->shipping_method == "fraktjakt" ){
			
			foreach( $this->fraktjakt_shipping_options as $shipping_option ){
				if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == $shipping_option['id'] ){
					$id = $shipping_option['id'];
					$label = $shipping_option['description'];
					$rate = $shipping_option['price'] + $this->handling;
					$rates[] = (object) array(
						'label'		=> $label,
						'amount'	=> ( $multiplier == 100 ) ? (int) number_format( $rate * $multiplier, 0, '', '' ) : number_format( $rate, 2, '.', '' ),
						'id' 		=> $id
					);
				}
			}
			
			foreach( $this->fraktjakt_shipping_options as $shipping_option ){
				if( $GLOBALS['ec_cart_data']->cart_data->shipping_method != $shipping_option['id'] ){
					$id = $shipping_option['id'];
					$label = $shipping_option['description'];
					$rate = $shipping_option['price'] + $this->handling;
					$rates[] = (object) array(
						'label'		=> $label,
						'amount'	=> ( $multiplier == 100 ) ? (int) number_format( $rate * $multiplier, 0, '', '' ) : number_format( $rate, 2, '.', '' ),
						'id' 		=> $id
					);
				}
			}
		}
		
		return $rates;
		
	}
	
	private function get_price_based_shipping_options( $standard_text, $express_text ){
		if( count( $this->price_based ) > 0 ){
			for( $i=0; $i<count($this->price_based); $i++){
				if( $this->subtotal >= $this->price_based[$i][0] )
					return $this->get_single_shipping_price_content( $standard_text, $express_text, apply_filters( 'wp_easycart_trigger_rate', $this->price_based[$i][1], 'price' ) );
			}
		}else{
			return "<div id=\"ec_cart_standard_shipping_row\" class=\"ec_cart_shipping_method_row\">Shipping Rate Setup ERROR: Please visit the EasyCart Admin -> Store Admin -> Rates and add at least one price trigger. If you have done this, check to ensure no gaps in triggers.</div>";
		}
	}
	
	private function get_weight_based_shipping_options( $standard_text, $express_text ){
		if( count( $this->weight_based ) > 0 ){
			for( $i=0; $i<count($this->weight_based); $i++){
				if( $this->weight >= $this->weight_based[$i][0] )
					return $this->get_single_shipping_price_content( $standard_text, $express_text, apply_filters( 'wp_easycart_trigger_rate', $this->weight_based[$i][1], 'weight' ) );
			}
		}else{
			return "<div id=\"ec_cart_standard_shipping_row\" class=\"ec_cart_shipping_method_row\">Shipping Rate Setup ERROR: Please visit the WP EasyCart -> Settings -> Shipping Rates and add at least one weight trigger. If you have done this, check to ensure no gaps in triggers.</div>";
		}
	}
	
	private function get_method_based_shipping_options( $standard_text, $express_text ){
		if( count( $this->method_based ) > 0 ){ 
		
			$ret_string = "";
			
			if( get_option( 'ec_option_add_local_pickup' ) ){
				$this->method_based[] = array( "0", $GLOBALS['language']->convert_text( $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_free' ) ), 'free', 0 );
				$ret_string .= $this->get_method_based_radio( count( $this->method_based ) - 1 );
				array_pop( $this->method_based );
			}
			
			$ret_string = apply_filters( 'wp_easycart_method_rate_pre', $ret_string );
				
			if( $this->display_type == "SELECT" )
				$ret_string .= "<select name=\"ec_cart_shipping_method\" onchange=\"ec_cart_shipping_method_change();\">";
			
			for( $i=0; $i<count($this->method_based); $i++){
				if( $this->display_type == "RADIO" )
					$ret_string .= $this->get_method_based_radio( $i );
				
				else if( $this->display_type == "SELECT" )
					$ret_string .= $this->get_method_based_select( $i );
				
				else //default is div
					$ret_string .= $this->get_method_based_div( $i );
			}
			
			if( $this->display_type == "SELECT" )
				$ret_string .= "</select>";
			
			return $ret_string;
		
		}else{
			return "<div id=\"ec_cart_standard_shipping_row\" class=\"ec_cart_shipping_method_row\">Shipping Rate Setup ERROR: Please visit the WP EasyCart -> Settings -> Shipping Rates and add at least one shipping method.</div>";
		}
	}
	
	private function get_quantity_based_shipping_options( $standard_text, $express_text ){
		if( count( $this->quantity_based ) > 0 ){
			for( $i=0; $i<count($this->quantity_based); $i++){
				if( $this->quantity >= $this->quantity_based[$i][0] )
					return $this->get_single_shipping_price_content( $standard_text, $express_text, apply_filters( 'wp_easycart_trigger_rate', $this->quantity_based[$i][1], 'quantity' ) );
			}
		}else{
			return "<div id=\"ec_cart_standard_shipping_row\" class=\"ec_cart_shipping_method_row\">Shipping Rate Setup ERROR: Please visit the WP EasyCart -> Settings -> Shipping Rates and add at least one quantity trigger. If you have done this, check to ensure no gaps in triggers.</div>";
		}
	}
	
	private function get_percentage_based_shipping_options( $standard_text, $express_text ){
		if( count( $this->percentage_based ) > 0 ){
			for( $i=0; $i<count($this->percentage_based); $i++){
				if( $this->subtotal >= $this->percentage_based[$i][0] )
					return $this->get_single_shipping_price_content( $standard_text, $express_text, apply_filters( 'wp_easycart_trigger_rate', $this->subtotal * ( $this->percentage_based[$i][1] / 100 ), 'percentage' ) );
			}
		}else{
			return "<div id=\"ec_cart_standard_shipping_row\" class=\"ec_cart_shipping_method_row\">Shipping Rate Setup ERROR: Please visit the WP EasyCart -> Settings -> Shipping Rates and add at least one quantity trigger. If you have done this, check to ensure no gaps in triggers.</div>";
		}
	}
	
	private function filter_by_class( ){
		
		$found_count = 0;
		$last_rate_id = 0;
		$allowed_live_rates = array( );
		$applicable_rate_ids = $this->mysqli->get_rates_by_class( $this->cart );
		if( count( $applicable_rate_ids ) ){
			for( $i=0; $i<count( $applicable_rate_ids ); $i++ ){
				if( $last_rate_id != $applicable_rate_ids[$i]->shipping_rate_id ){
					$found_count = 0;
					$last_rate_id = $applicable_rate_ids[$i]->shipping_rate_id;
				}
				for( $j=0; $j<count( $this->cart ); $j++ ){
					if( $applicable_rate_ids[$i]->shipping_class_id == $this->cart[$j]->shipping_class_id )
						$found_count++;
				}
				if( $found_count == count( $this->cart ) ){
					$allowed_live_rates[] = $last_rate_id;
				}
			}
			$new_live_based = array( );
			foreach( $this->live_based as $live_based ){
				if( in_array( $live_based[2], $allowed_live_rates ) )
					$new_live_based[] = $live_based;
			}
			if( count( $new_live_based ) == 0 ){ // Need to offer multiple options
				return false;
			}else{
				$this->live_based = $new_live_based;
				return true;
			}
		}
		
	}
	
	private function get_live_based_shipping_options_by_class( $standard_text, $express_text ){
		
		$last_rate_id = 0;
		$allowed_live_rates = array( );
		$new_rate = 0;
		$applicable_rate_ids = $this->mysqli->get_rates_by_class( $this->cart );
		for( $i=0; $i<count( $applicable_rate_ids ); $i++ ){
			if( $last_rate_id != $applicable_rate_ids[$i]->shipping_rate_id ){
				$found_count = 0;
				$last_rate_id = $applicable_rate_ids[$i]->shipping_rate_id;
				$allowed_live_rates[] = $last_rate_id;
			}
		}
		$new_live_based = array( );
		foreach( $this->live_based as $live_based ){
			if( in_array( $live_based[2], $allowed_live_rates ) )
				$new_live_based[] = $live_based;
		}
		$this->live_based = $new_live_based;
		
	}
	
	private function get_live_based_shipping_options_no_rates( $standard_text, $express_text ){
		
		print_r( $this->method_based );
		
	}
	
	private function get_live_based_shipping_options( $standard_text, $express_text ){
		
		if( count( $this->live_based ) > 0 ){ 
		
			$filter_success = $this->filter_by_class( );
			
			$ret_string = "";
			
			if( $this->display_type == "SELECT" )
				$ret_string .= "<select name=\"ec_cart_shipping_method\" onchange=\"ec_cart_shipping_method_change();\">";
			
			if( get_option( 'ec_option_add_local_pickup' ) ){
				$this->live_based[] = array( "FREE", $GLOBALS['language']->convert_text( $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_free' ) ), 'free', '', 0, 0 );
				$ret_string .= $this->get_live_based_radio( 0, count( $this->live_based ) - 1, 'free', 0 );
				array_pop( $this->live_based );
			}
			
			$ret_string = apply_filters( 'wp_easycart_live_rate_pre', $ret_string );
			
			$count = 0;
			for( $i=0; $i<count( $this->live_based ); $i++){
				$service_days = 0;
				if( $this->live_based[$i][4] != NULL && get_option( 'ec_option_live_override_always' ) ){
					if( $this->live_based[$i][4] == 0 )
						$rate = "FREE";
					else
						$rate = $this->live_based[$i][4];
						
				}else if( $this->live_based[$i][5] > 0 && $this->subtotal >= $this->live_based[$i][5] ) // Shipping free at rate
					$rate = "FREE";
					
				else{
					$rate = $this->shipper->get_rate( $this->live_based[$i][3], $this->live_based[$i][0] );
					$service_days = $this->shipper->get_service_days( $this->live_based[$i][3], $this->live_based[$i][0] );
				}
				
				if( $rate != "ERROR" ){
					if( $this->display_type == "RADIO" )
						$ret_string .= $this->get_live_based_radio( $count, $i, $rate, $service_days );
					
					else if( $this->display_type == "SELECT" )
						$ret_string .= $this->get_live_based_select( $count, $i, $rate );
					
					else //default is div
						$ret_string .= $this->get_live_based_div( $count, $i, $rate );
					
					$count++;
				}
			}
			
			if( $this->display_type == "SELECT" )
			$ret_string .= "</select>";
			
			return $ret_string;
			
		}else{
			return "<div id=\"ec_cart_standard_shipping_row\" class=\"ec_cart_shipping_method_row\">Shipping Rate Setup ERROR: Please visit the WP EasyCart -> Settings -> Shipping Rates and add at least one shipping method for your selected live based shipping company. If you have done this and are still seeing this error, then likely there is a setup error in the live based company settings. Feel free to contact us at www.wpeasycart.com to get help troubleshooting.</div>";
		}
	}
	
	private function get_fraktjakt_based_shipping_options( ){
		
		$i = 0;
		$selected_method = 0;
		if( $GLOBALS['ec_cart_data']->cart_data->shipping_method != "" )
			$selected_method = $GLOBALS['ec_cart_data']->cart_data->shipping_method;
			
		$ret_string = "";
		foreach( $this->fraktjakt_shipping_options as $shipping_option ){
			$ret_string .= "<div id=\"ec_cart_standard_shipping_row\" class=\"ec_cart_shipping_method_row\"><input type=\"radio\" class=\"no_wrap\" name=\"ec_cart_shipping_method\" id=\"ec_cart_shipping_method\" value=\"" . $shipping_option['id'] . "\"";
			if( ( !$selected_method && $i == 0 ) || ( $selected_method == $shipping_option['id'] ) )
				$ret_string .= " checked=\"checked\"";
				
			$ret_string .= ">" . $shipping_option['description'] . " (" . $GLOBALS['currency']->get_symbol( ) . "<span id=\"ec_cart_standard_shipping_price\">" . $GLOBALS['currency']->get_number_only( apply_filters( 'wp_easycart_shipping_price_display', $shipping_option['price'] + $this->handling, $shipping_option['id'] ) ) . "</span>)</div>";
			$i++;
		}
		return $ret_string;
		
	}
	
	private function get_method_based_radio( $i ){
		
		$ret_string = "";
		
		$ret_string .= "<div class=\"ec_cart_shipping_method_row\">";
		$ret_string .= "<input type=\"radio\" class=\"no_wrap\" name=\"ec_cart_shipping_method\" value=\"" . $this->method_based[$i][2] . "\" onchange=\"ec_cart_shipping_method_change('" . $this->method_based[$i][2] . "'); \"";
		
		if( ( $GLOBALS['ec_cart_data']->cart_data->shipping_method == "" && $i==0 ) || ( $GLOBALS['ec_cart_data']->cart_data->shipping_method != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_method == $this->method_based[$i][2] ) )
		$ret_string .= " checked=\"checked\"";
		
		if( $this->method_based[$i][2] == 'free' )
			$rate = 0;
			
		else if( $this->method_based[$i][3] > 0 && $this->subtotal >= $this->method_based[$i][3] )
			$rate = 0;
			
		else if( get_option( 'ec_option_static_ship_items_seperately' ) )
			$rate = ( $this->method_based[$i][0] * $this->quantity ) + $this->handling;
		
		else
			$rate = $this->method_based[$i][0] + $this->handling;
		
		$ret_string .= " /> " . $this->method_based[$i][1] . " (" . $GLOBALS['currency']->get_currency_display( apply_filters( 'wp_easycart_shipping_price_display', $rate, $this->method_based[$i][2] ) ) . ")</div>";
		
		return $ret_string;
	}
	
	private function get_method_based_select( $i ){
		
		$ret_string = "";
		$ret_string .= "<option value=\"" . $this->method_based[$i][2] . "\"";
		
		if( ( $GLOBALS['ec_cart_data']->cart_data->shipping_method == "" && $i==0 ) || ( $GLOBALS['ec_cart_data']->cart_data->shipping_method != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_method == $this->method_based[$i][2] ) )
		$ret_string .= " selected=\"selected\"";
		
		if( $this->method_based[$i][3] > 0 && $this->subtotal >= $this->method_based[$i][3] )
			$rate = 0;
			
		else if( get_option( 'ec_option_static_ship_items_seperately' ) )
			$rate = ( $this->method_based[$i][0] * $this->quantity ) + $this->handling;
			
		else
			$rate = $this->method_based[$i][0] + $this->handling;
		
		$ret_string .= "> " . $this->method_based[$i][1] . " (" . $GLOBALS['currency']->get_currency_display( apply_filters( 'wp_easycart_shipping_price_display', $rate, $this->method_based[$i][2] ) ) . ")</option>";
		
		return $ret_string;
	}
	
	private function get_method_based_div( $i ){
		
		$ret_string = "";
		
		if( $this->method_based[$i][3] > 0 && $this->subtotal >= $this->method_based[$i][3] )
			$rate = 0;
			
		else if( get_option( 'ec_option_static_ship_items_seperately' ) )
			$rate = ( $this->method_based[$i][0] * $this->quantity ) + $this->handling;
			
		else
			$rate = $this->method_based[$i][0] + $this->handling;
		
		$ret_string .= "<div class=\"ec_cart_shipping_method_row\" id=\"" . $this->method_based[$i][2] . "\"> " . $this->method_based[$i][1] . " (" . $GLOBALS['currency']->get_currency_display( apply_filters( 'wp_easycart_shipping_price_display', $rate, $this->method_based[$i][2] ) ) . ")</div>";
		
		return $ret_string;
	}
	
	private function get_live_based_radio( $count, $i, $rate, $service_days = 0 ){
		
		if( $rate != "ERROR" ){
			if( $rate == "FREE" || $rate == "free" )
				$rate = 0;
			else if( $this->live_based[$i][4] != NULL && !get_option( 'ec_option_live_override_always' ) )
                $rate = $this->live_based[$i][4];
            else
				$rate = doubleval( $rate ) + doubleval( $this->handling );

            $ret_string = "";
			
			$ret_string .= "<div class=\"ec_cart_shipping_method_row\">";
			$ret_string .= "<input type=\"radio\" class=\"no_wrap\" name=\"ec_cart_shipping_method\" value=\"" . $this->live_based[$i][2] . "\" onchange=\"ec_cart_shipping_method_change('" . $this->live_based[$i][2] . "', " . apply_filters( 'wp_easycart_shipping_price_display', $rate, $this->live_based[$i][2] ) . " ); \"";
			
			if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == "" && $this->get_lowest_live_based_rate( ) == $this->live_based[$i][2] ){
				$GLOBALS['ec_cart_data']->cart_data->shipping_method = $this->live_based[$i][2];
				$GLOBALS['ec_cart_data']->save_session_to_db( );
			}
			
			if( $GLOBALS['ec_cart_data']->cart_data->shipping_method != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_method == $this->live_based[$i][2] )
				$ret_string .= " checked=\"checked\"";
			else if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == "" && $this->get_lowest_live_based_rate( ) == $this->live_based[$i][2] )
				$ret_string .= " checked=\"checked\"";
			
			$ret_string .= " /><span class=\"label\">" . $this->live_based[$i][1];
			if( $service_days > 0 && get_option( 'ec_option_show_delivery_days_live_shipping' ) )
				$ret_string .= " (" . $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'delivery_in' ) . " " . $service_days . "-" . ($service_days+1) . " " . $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'delivery_days' ) . ")";
			
			$ret_string .= "</span> <span class=\"price\">" . $GLOBALS['currency']->get_currency_display( apply_filters( 'wp_easycart_shipping_price_display', $rate, $this->live_based[$i][2] ) ) . "</span></div>";
			
			return $ret_string;
			
		}
		
	}
	
	private function get_live_based_select( $count, $i, $rate ){
		
		if( $rate != "ERROR" ){
			
			if( $rate == "FREE" )
				$rate = 0;
			else if( $this->live_based[$i][4] != NULL && !get_option( 'ec_option_live_override_always' ) )
                $rate = $this->live_based[$i][4];
            else
				$rate = doubleval( $rate ) + doubleval( $this->handling );
			
			$ret_string = "";
			$ret_string .= "<option value=\"" . $this->live_based[$i][0] . "\"";
			
			if( ( $GLOBALS['ec_cart_data']->cart_data->shipping_method == "" && $count == 0 ) || ( $GLOBALS['ec_cart_data']->cart_data->shipping_method != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_method == $this->live_based[$i][0] ) )
			$ret_string .= " selected=\"selected\"";
			
			$ret_string .= "> " . $this->live_based[$i][1] . " " . $GLOBALS['currency']->get_currency_display( apply_filters( 'wp_easycart_shipping_price_display', $rate, $this->live_based[$i][0] ) ) . "</option>";
			
			return $ret_string;
			
		}
	}
	
	private function get_live_based_div( $count, $i, $rate ){
		
		if( $rate != "ERROR" ){
			
			if( $rate == "FREE" )
				$rate = 0;
			else if( $this->live_based[$i][4] != NULL && !get_option( 'ec_option_live_override_always' ) )
                $rate = $this->live_based[$i][4];
            else
				$rate = doubleval( $rate ) + doubleval( $this->handling );
			
			$ret_string = "<div id=\"" . $this->live_based[$i][0] . "\"> " . $this->live_based[$i][1] . " " . $GLOBALS['currency']->get_currency_display( apply_filters( 'wp_easycart_shipping_price_display', $rate, $this->live_based[$i][0] ) ) . "</div>";
			return $ret_string;
		}
		
	}
	
	public function get_lowest_live_based_rate( ){
		$lowest_i = 0;
		$lowest = 100000.00;
		$lowest_ship_method = "ERROR";
		
		for( $i=0; $i<count( $this->live_based ); $i++ ){
			
			// Find lowest
			if( $this->live_based[$i][5] > 0 && $this->subtotal >= $this->live_based[$i][5] ){ // Shipping free at rate
				$lowest_i = $i;
				$lowest = floatval( 0 );
				$lowest_ship_method = $this->live_based[$i][2];
				
			}else if( $this->live_based[$i][4] != NULL && $this->live_based[$i][4] > 0 && get_option( 'ec_option_live_override_always' ) )
				$subrate = $this->live_based[$i][4];
			else 
				$subrate = $this->shipper->get_rate( $this->live_based[$i][3], $this->live_based[$i][0] );
			
			if( $subrate != "ERROR" && floatval( $subrate ) < $lowest ){
				if( $this->live_based[$i][4] != NULL && $this->live_based[$i][4] > 0 && !get_option( 'ec_option_live_override_always' ) ){
				    $subrate = $this->live_based[$i][4];
                }
                $lowest_i = $i;
				$lowest = floatval( $subrate );
				$lowest_ship_method = $this->live_based[$i][2];
			}
			
		}
		
		return $lowest_ship_method;
	}
	
	public function get_selected_shipping_method( ){
		
		$selected_shipping_method_id = 0;
		if( $GLOBALS['ec_cart_data']->cart_data->shipping_method != "" )
			$selected_shipping_method_id = $GLOBALS['ec_cart_data']->cart_data->shipping_method;
			
		if( $this->shipping_method == "price" || $this->shipping_method == "weight" || $this->shipping_method == "percentage" || $this->shipping_method == "quantity" ){
			if( $this->ship_express ){
				echo $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_express' );
			}else if( $selected_shipping_method_id == "free" ){
				echo $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_free' );
			}else{
				echo $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_standard' );
			}
		
		}else if( $this->shipping_method == "method" ){
			
			for( $i=0; $i<count($this->method_based); $i++){
				if( $this->method_based[$i][2] == $selected_shipping_method_id ){
					return $this->get_method_based_div( $i );
				}
			}
		
		}else if( $this->shipping_method == "live" ){
			for( $i=0; $i<count($this->live_based); $i++){
				if( $this->live_based[$i][2] == $selected_shipping_method_id ){
					if( $this->live_based[$i][4] && get_option( 'ec_option_live_override_always' ) ){
						if( $this->live_based[$i][4] == 0 )
							$rate = "FREE";
						else
							$rate = $this->live_based[$i][4];
						return "<div id=\"" . $this->live_based[$i][0] . "\"> " . $this->live_based[$i][1] . " " . $GLOBALS['currency']->get_currency_display( apply_filters( 'wp_easycart_shipping_price_display', $rate, $this->live_based[$i][2] ) ) . "</div>";
					}else
						$rate = doubleval( $this->shipper->get_rate( $this->live_based[$i][3], $this->live_based[$i][0] ) ) + doubleval( $this->handling );
					
					return $this->get_live_based_div( $i, $i, $rate + $this->handling );
				}
			}
			
			// Nothing currently selected, lets find lowest value!
			$lowest_i = 0;
			$lowest = 100000.00;
			$lowest_ship_method = "ERROR";
			
			for( $i=0; $i<count( $this->live_based ); $i++ ){
				
				// Find lowest
				if( $this->live_based[$i][5] > 0 && $this->subtotal >= $this->live_based[$i][5] ){ // Shipping free at rate
					$lowest_i = $i;
					$lowest = floatval( 0 );
					$lowest_ship_method = $this->live_based[$i][2];
					
				}else if( $this->live_based[$i][4] != NULL && $this->live_based[$i][4] > 0 && get_option( 'ec_option_live_override_always' ) )
					$subrate = $this->live_based[$i][4];
				else 
					$subrate = $this->shipper->get_rate( $this->live_based[$i][3], $this->live_based[$i][0] );
				
				if( $subrate != "ERROR" && $this->live_based[$i][4] != NULL && $this->live_based[$i][4] > 0 && !get_option( 'ec_option_live_override_always' ) ){
                    $subrate = $this->live_based[$i][4];
                }
                
                if( $subrate != "ERROR" && floatval( $subrate ) < $lowest ){
					$lowest_i = $i;
					$lowest = floatval( $subrate );
					$lowest_ship_method = $this->live_based[$i][2];
				}
				
			}
			
			return $this->get_live_based_div( $lowest_i, $lowest_i, $lowest + $this->handling );
		
		}else if( $this->shipping_method == "fraktjakt" ){
			
			$i = 0;
			$selected_method = 0;
			if( $GLOBALS['ec_cart_data']->cart_data->shipping_method != "" )
				$selected_method = $GLOBALS['ec_cart_data']->cart_data->shipping_method;
				
			$ret_string = "";
			foreach( $this->fraktjakt_shipping_options as $shipping_option ){
				if( ( !$selected_method && $i == 0 ) || ( $selected_method == $shipping_option['id'] ) )
					return $shipping_option['description'];
					
				$i++;
			}
		}
		
	}
	
	public function get_single_shipping_price_content( $standard_text, $express_text, $standard_price ){
		
		$coupon_code = "";
		if( $GLOBALS['ec_cart_data']->cart_data->coupon_code != "" )
			$coupon_code = $GLOBALS['ec_cart_data']->cart_data->coupon_code;
			
		$discount = new ec_discount( array(), 0.00, $standard_price, $coupon_code, "", 0 );
		$shipping_discount = $discount->shipping_discount;
		
		$ret_string = "";
		if( get_option( 'ec_option_add_local_pickup' ) ){
			$ret_string .= "<div id=\"ec_cart_standard_shipping_row_free\" class=\"ec_cart_shipping_method_row\"><input type=\"radio\" class=\"no_wrap\" name=\"ec_cart_shipping_method\" id=\"ec_cart_shipping_method\" value=\"free\"";
			if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == "free" )
				$ret_string .= " checked=\"checked\"";
			$ret_string .= " /><span class=\"ec_cart_standard_shipping_price_label\">" . $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_free' ) . " (" . $GLOBALS['currency']->get_symbol( ) . "</span> <span id=\"ec_cart_standard_shipping_price_free\">" . $GLOBALS['currency']->get_number_only( apply_filters( 'wp_easycart_shipping_price_display', 0, 'free' ) ) . "</span>)</div>";
		}
		$ret_string .= "<div id=\"ec_cart_standard_shipping_row\" class=\"ec_cart_shipping_method_row\"><input type=\"radio\" class=\"no_wrap\" name=\"ec_cart_shipping_method\" id=\"ec_cart_shipping_method\" value=\"standard\"";
		if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == "" || $GLOBALS['ec_cart_data']->cart_data->shipping_method == "standard" )
			$ret_string .= " checked=\"checked\"";
		$ret_string .= " /><span class=\"ec_cart_standard_shipping_price_label\">" . $standard_text . " (" . $GLOBALS['currency']->get_symbol( ) . "</span><span id=\"ec_cart_standard_shipping_price\">" . $GLOBALS['currency']->get_number_only( apply_filters( 'wp_easycart_shipping_price_display', $standard_price + $this->handling - $shipping_discount, 'standard' ) )  . "</span>)</div>";
		if( $this->express_price > 0 ){
			$ret_string .= "<div id=\"ec_cart_express_shipping_row\" class=\"ec_cart_shipping_method_row\"><input type=\"checkbox\" name=\"ec_cart_ship_express\" id=\"ec_cart_ship_express\" value=\"shipexpress\"";
			if( $this->ship_express )
				$ret_string .= " checked=\"checked\"";
			$ret_string .= " /><span class=\"ec_cart_standard_shipping_price_label\">" . $express_text . " (+" . $GLOBALS['currency']->get_symbol( ) . "</span><span id=\"ec_cart_express_shipping_price\">" . $GLOBALS['currency']->get_number_only( apply_filters( 'wp_easycart_express_shipping_price_display', $this->express_price ) ) . "</span>)</div>";
		}
		return $ret_string;
	}
	
	public function get_shipping_price( $cart_handling = 0 ){
		if( $this->freeshipping || $this->quantity == 0 ){
			return "0.00";
		}
		
		$rate = "ERROR";
		if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == "free" || $GLOBALS['ec_cart_data']->cart_data->shipping_method == "promo_free" ){
				$rate = 0;
		
		}else if( $this->shipping_method == "price" ){
			for( $i=0; $i<count( $this->price_based ); $i++ ){
				if( $this->subtotal >= $this->price_based[$i][0] ){
					$rate = apply_filters( 'wp_easycart_trigger_rate', $this->price_based[$i][1], 'price' );
					break;
				}
				
			}
			if( $this->ship_express )
				$rate = $rate + $this->express_price;
			
		}else if( $this->shipping_method == "weight" ){
			for( $i=0; $i<count( $this->weight_based ); $i++ ){
				if( $this->weight >= $this->weight_based[$i][0] ){
					$rate = apply_filters( 'wp_easycart_trigger_rate', $this->weight_based[$i][1], 'weight' );
					break;
				}
			}
			if( $this->ship_express )
				$rate = $rate + $this->express_price;
			
		}else if( $this->shipping_method == "method" ){
			if( $this->subtotal <= 0 )
				$rate = "0.00";
			
			else if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == "" ){
				if( $this->method_based[0][3] > 0 && $this->subtotal >= $this->method_based[0][3] ){
					$rate = 0;
				}else if( get_option( 'ec_option_static_ship_items_seperately' ) ){
					$rate = ( $this->method_based[$i][0] * $this->quantity ) + $this->handling;
				}else{
					$rate = $this->method_based[0][0];
				}
			
			}else{
				$rate_found = false;
				for( $i=0; $i<count( $this->method_based ); $i++ ){
					if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == $this->method_based[$i][2] ){
						if( $this->method_based[$i][3] > 0 && $this->subtotal >= $this->method_based[$i][3] ){
							$rate = 0;
						}else if( get_option( 'ec_option_static_ship_items_seperately' ) ){
							$rate = ( $this->method_based[$i][0] * $this->quantity ) + $this->handling;
						}else{
							$rate = $this->method_based[$i][0];
						}
						$rate_found = true;
					}
				}
				
				if( !$rate_found ){
					if( $this->method_based[0][3] > 0 && $this->subtotal >= $this->method_based[0][3] ){
						$rate = 0;
					}else if( get_option( 'ec_option_static_ship_items_seperately' ) ){
						$rate = ( $this->method_based[$i][0] * $this->quantity ) + $this->handling;
					}else{
						$rate = $this->method_based[0][0];
					}
				}
			}
			$rate = apply_filters( 'wp_easycart_trigger_rate', $rate, 'method' );
			
		}else if( $this->shipping_method == "quantity" ){
			for( $i=0; $i<count( $this->quantity_based ); $i++ ){
				if( $this->quantity >= $this->quantity_based[$i][0] ){
					$rate = apply_filters( 'wp_easycart_trigger_rate', $this->quantity_based[$i][1], 'quantity' );
					break;
				}
				
			}
			if( $this->ship_express )
				$rate = $rate + $this->express_price;
			
		}else if( $this->shipping_method == "percentage" ){
			for( $i=0; $i<count( $this->percentage_based ); $i++ ){
				if( $this->subtotal >= $this->percentage_based[$i][0] ){
					$rate = apply_filters( 'wp_easycart_trigger_rate', ( $this->subtotal * ( $this->percentage_based[$i][1] / 100 ) ), 'percentage' );
					break;
				}
				
			}
			if( $this->ship_express )
				$rate = $rate + $this->express_price;
			
		}else if( $this->shipping_method == "live" ){
			if( isset( $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip ) && $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip == "" && isset( $GLOBALS['ec_cart_data']->cart_data->shipping_method ) && $GLOBALS['ec_cart_data']->cart_data->shipping_method == "" && $GLOBALS['ec_cart_data']->cart_data->email == "" )
				return doubleval( "0.00" );
				
			$lowest = 100000.00;
			$lowest_ship_method = "ERROR";
			
			for( $i=0; $i<count( $this->live_based ); $i++ ){
				
				if( $GLOBALS['ec_cart_data']->cart_data->shipping_method != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_method == $this->live_based[$i][2] ){
					if( $this->live_based[$i][4] != NULL && get_option( 'ec_option_live_override_always' ) ){
						if( $this->live_based[$i][4] == 0 )
							$rate = "FREE";
						else
							$rate = $this->live_based[$i][4];
					}else if( $this->live_based[$i][5] > 0 && $this->subtotal >= $this->live_based[$i][5] ){ // Shipping free at rate
						$rate = "FREE";
					}else{
						$rate = $this->shipper->get_rate( $this->live_based[$i][3], $this->live_based[$i][0] );
					
                        if( $rate != "ERROR" && $this->live_based[$i][4] != NULL  && !get_option( 'ec_option_live_override_always' ) ){
                            $rate = $this->live_based[$i][4];
                        }
                    }
					
				}else{
				
					// Find lowest
					if( $this->live_based[$i][5] > 0 && $this->subtotal >= $this->live_based[$i][5] ){ // Shipping free at rate
						$lowest = floatval( 0 );
						$lowest_ship_method = $this->live_based[$i][2];
						
					}else if( $this->live_based[$i][4] != NULL && $this->live_based[$i][4] > 0 && get_option( 'ec_option_live_override_always' ) ){
						$subrate = $this->live_based[$i][4];
					
					}else if( $this->live_based[$i][4] != NULL && $this->live_based[$i][4] == 0 && get_option( 'ec_option_live_override_always' ) ){
						// Skip free shipping rate, typically a local pickup
						$subrate = 99999999;
						
					}else{ 
						$subrate = $this->shipper->get_rate( $this->live_based[$i][3], $this->live_based[$i][0] );
					}
                    
                    if( $subrate != "ERROR" && $this->live_based[$i][4] != NULL  && !get_option( 'ec_option_live_override_always' ) ){
                        $subrate = $this->live_based[$i][4];
                    }
					
					if( $subrate != "ERROR" && floatval( $subrate ) < $lowest ){
						$lowest = floatval( $subrate );
						$lowest_ship_method = $this->live_based[$i][2];
					}
					
				}
				
			}
			
			if( $rate == "ERROR" && $lowest_ship_method != "ERROR" ){
				$rate = $lowest;
			}
			
		}else if( $this->shipping_method == "fraktjakt" ){
			$i = 0;
			$selected_method = 0;
			if( $GLOBALS['ec_cart_data']->cart_data->shipping_method != "" )
				$selected_method = $GLOBALS['ec_cart_data']->cart_data->shipping_method;
			
			if( $this->fraktjakt_shipping_options ){
				$backup = 0.00;
				$frak_is_found = false;
				foreach( $this->fraktjakt_shipping_options as $shipping_option ){
					if( ( !$selected_method && $i == 0 ) || ( $selected_method == $shipping_option['id'] ) ){
						$rate = $shipping_option['price'];
						$frak_is_found = true;
					}else if( $i == 0 )
						$backup = $shipping_option['price'];
						
					$i++;
				}
				
				if( !$frak_is_found )
					$rate = $backup;
			}
		}
		
		if( $rate == "ERROR" ){
			return doubleval( "0.00" );
		}else if( $rate == "FREE" ){
			return 0;
		}else{
			// Add the Handling Rate
			$rate = doubleval( $rate ) + doubleval( $this->handling ) + doubleval( $cart_handling );
			
			$promotion = new ec_promotion( );
			$discount = $promotion->get_shipping_discounts( $this->subtotal, $rate, $this->shipping_promotion_text );
		
			return doubleval( $rate ) - doubleval( $discount );
		}
	}
	
	public function get_shipping_promotion_text( ){
		$promotion = new ec_promotion( );
		$rate = 0;
		$promotion->get_shipping_discounts( $this->subtotal, $rate, $this->shipping_promotion_text );
		return $this->shipping_promotion_text;
	}
	
	public function submit_fraktjakt_shipping_order( ){
		$shipment_id = 0;
		$i = 0;
		$selected_method = 0;
		if( $GLOBALS['ec_cart_data']->cart_data->shipping_method != "" )
			$selected_method = $GLOBALS['ec_cart_data']->cart_data->shipping_method;
		
		foreach( $this->fraktjakt_shipping_options as $shipping_option ){
			if( $selected_method == $shipping_option['id'] )
				$shipment_id = $shipping_option['shipment_id']; 
		}
		
		return $this->fraktjakt->insert_shipping_order( $shipment_id, $GLOBALS['ec_cart_data']->cart_data->shipping_method );
	}
	
	public function validate_address( $destination_address, $destination_city, $destination_state, $destination_zip, $destination_country ){
		
		if( $this->shipping_method == "live" ){
			
			return $this->shipper->validate_address( $destination_address, $destination_city, $destination_state, $destination_zip, $destination_country );
		
		}else if( $this->shipping_method == "fraktjakt" ){
			return $this->fraktjakt->validate_address( $destination_address, $destination_city, $destination_state, $destination_zip, $destination_country );
		
		}else
			return true;
			
	}
	
	public function skip_shipping_selection_page( ){
		
		if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == "" && $this->shipping_method == "live" ){
			
			$lowest_method = $this->get_lowest_live_based_rate( );
			
			$GLOBALS['ec_cart_data']->cart_data->shipping_method = $lowest_method;
			
		}
		
	}
	
	public function has_shipping_rates( ){
		if( $this->shipping_method == "price" )
			return count( $this->price_based );
			
		else if( $this->shipping_method == "weight" )
			return count( $this->weight_based );
			
		else if( $this->shipping_method == "method" )
			return count( $this->method_based );
			
		else if( $this->shipping_method == "quantity" )
			return count( $this->quantity_based );
			
		else if( $this->shipping_method == "percentage" )
			return count( $this->percentage_based );
			
		else if( $this->shipping_method == "live" ){
			return count( $this->live_based );
			
		}else if( $this->shipping_method == "fraktjakt" )
			return true;
	}
	
}

?>