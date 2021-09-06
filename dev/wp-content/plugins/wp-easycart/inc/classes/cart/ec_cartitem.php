<?php

class ec_cartitem{
	
	public $mysqli;													// ec_db class
	
	public $orderdetail_id = 0;										// INT
	public $cartitem_id;											// INT
	public $product_id;												// INT
	public $model_number;											// VARCHAR 255
	public $orderdetails_model_number;								// VARCHAR 255
	public $post_id;												// INT
	public $guid;													// Permalink to Product
	public $manufacturer_id;										// INT
	public $use_advanced_optionset;									// BOOL
	
	public $quantity;												// INT
	public $show_stock_quantity;
	public $min_purchase_quantity;									// INT
	public $max_purchase_quantity;									// INT
	public $grid_quantity;											// INT
	public $weight;													// INT
	public $total_weight;											// FLOAT
	public $width;
	public $height;
	public $length;
	public $shipping_class_id;
	
	public $title;													// VARCHAR 255
	public $description;											// BLOB
	
	public $unit_price;												// FLOAT 15,3
	public $total_price;											// FLOAT 15,3
	public $converted_total_price;									// FLOAT 15,3
	public $prev_price;												// FLOAT 15,3
	public $handling_price;											// FLOAT 15,3
	public $handling_price_each;									// FLOAT 15,3
	public $discount_price;											// FLOAT 15,3
	public $pricetiers = array();									// Array of rows of ec_pricetier
	
	public $options_price_onetime;
	public $grid_price_change;
	
	public $vat_enabled;											// FLAOT 15,3
	
	public $is_giftcard;											// BOOL
	public $is_download;											// BOOL
	public $is_donation;											// BOOL
	public $is_taxable;												// BOOL
	public $is_shippable;											// BOOL
	public $exclude_shippable_calculation;										// BOOL
	public $is_amazon_download;										// BOOL
	public $include_code;											// BOOL
	public $TIC;													// VARCHAR(5)
	
	public $allow_backorders;										// BOOL
	public $backorder_fill_date;									// VARCHAR 512
	public $stock_quantity;											// INT
	
	public $image1;													// VARCHAR 255
	public $image1_optionitem;										// VARCHAR 255
	
	public $optionitem1_name;										// VARCHAR 255
	public $optionitem2_name;										// VARCHAR 255
	public $optionitem3_name;										// VARCHAR 255
	public $optionitem4_name;										// VARCHAR 255
	public $optionitem5_name;										// VARCHAR 255
	
	public $optionitem1_label;										// VARCHAR 255
	public $optionitem2_label;										// VARCHAR 255
	public $optionitem3_label;										// VARCHAR 255
	public $optionitem4_label;										// VARCHAR 255
	public $optionitem5_label;										// VARCHAR 255
	
	public $optionitem1_price;										// FLOAT 7,2
	public $optionitem2_price;										// FLOAT 7,2
	public $optionitem3_price;										// FLOAT 7,2
	public $optionitem4_price;										// FLOAT 7,2
	public $optionitem5_price;										// FLOAT 7,2
	
	public $optionitem1_weight;										// FLOAT 11,3
	public $optionitem2_weight;										// FLOAT 11,3
	public $optionitem3_weight;										// FLOAT 11,3
	public $optionitem4_weight;										// FLOAT 11,3
	public $optionitem5_weight;										// FLOAT 11,3
	
	public $optionitem1_id;											// INT
	public $optionitem2_id;											// INT
	public $optionitem3_id;											// INT
	public $optionitem4_id;											// INT
	public $optionitem5_id;											// INT
	
	public $advanced_options;										// array
	
	public $custom_vars = array();									// array
	
	public $giftcard_id = 0;										// INT
	public $gift_card_message;										// TEXT
	public $gift_card_from_name;									// VARCHAR 255
	public $gift_card_to_name;										// VARCHAR 255
	public $gift_card_email;										// VARCHAR 255
		
	public $donation_price;											// FLOAT 9,2
	
	public $is_deconetwork;											// BOOL
	public $deconetwork_id;											// VARCHAR 64
	public $deconetwork_name;										// VARCHAR 512
	public $deconetwork_product_code;								// VARCHAR 64
	public $deconetwork_options;									// VARCHAR 512
	public $deconetwork_edit_link;									// VARCHAR 512
	public $deconetwork_color_code;									// VARCHAR 64
	public $deconetwork_product_id;									// VARCHAR 64
	public $deconetwork_image_link;									// VARCHAR 512
	public $deconetwork_discount;									// FLOAT 15,3
	public $deconetwork_tax;										// FLOAT 15,3
	public $deconetwork_total;										// FLOAT 15,3
	public $deconetwork_version;									// INT
	
	public $has_affiliate_rule;										// Bool
	public $affiliate_rule;											// ec_affiliate_rule Object
	
	public $download_id = 0;										// INT
	public $download_file_name;										// VARCHAR 255
	public $amazon_key;												// VARCHAR 255
	public $use_optionitem_quantity_tracking;						// BOOL
	public $optionitem_stock_quantity;								// INT
	public $track_quantity;											// BOOL
	public $max_quantity;											// INT
	public $min_quantity;											// INT
	
	// Subscription Options
	public $is_subscription_item;
	public $subscription_bill_length;
	public $subscription_bill_period;
	public $subscription_bill_duration;
	public $trial_period_days;
	public $subscription_signup_fee;
	public $subscription_prorate;
	public $stripe_plan_added;
	public $subscription_unique_id;
	
	public $promotions;												// array of promtions
	
	public $store_page;												// VARCHAR
	public $cart_page;												// VARCHAR
	public $permalink_divider;										// CHAR
	
	function __construct( $cartitem_data ){
		$this->mysqli = new ec_db( );
		
		$this->cartitem_id = $cartitem_data->cartitem_id;
		$this->product_id = $cartitem_data->product_id;
		$this->model_number = $cartitem_data->model_number;
		$this->orderdetails_model_number = $cartitem_data->model_number;
		$this->post_id = $cartitem_data->post_id;
		$this->guid = $cartitem_data->guid;
		$this->manufacturer_id = $cartitem_data->manufacturer_id;
		
		$this->quantity = $cartitem_data->quantity;
		$this->show_stock_quantity = $cartitem_data->show_stock_quantity;
		$this->min_purchase_quantity = $cartitem_data->min_purchase_quantity;
		$this->max_purchase_quantity = $cartitem_data->max_purchase_quantity;
		$this->grid_quantity = $cartitem_data->grid_quantity;
		if( $this->grid_quantity > 0 )
			$this->quantity = $this->grid_quantity;
			
		$this->weight = $cartitem_data->weight;
		$this->width = $cartitem_data->width;
		$this->height = $cartitem_data->height;
		$this->length = $cartitem_data->length;
		$this->shipping_class_id = $cartitem_data->shipping_class_id;
		
		$this->title = $GLOBALS['language']->convert_text( $cartitem_data->title );
		$this->description = $GLOBALS['language']->convert_text( $cartitem_data->description );
		
		$this->is_giftcard = $cartitem_data->is_giftcard;
		$this->is_download = $cartitem_data->is_download;
		$this->is_donation = $cartitem_data->is_donation;
		$this->is_taxable = $cartitem_data->is_taxable;
		$this->is_shippable = $cartitem_data->is_shippable;
		$this->exclude_shippable_calculation = $cartitem_data->exclude_shippable_calculation;
		$this->is_amazon_download = $cartitem_data->is_amazon_download;
		$this->include_code = $cartitem_data->include_code;
		$this->TIC = $cartitem_data->TIC;
		
		$this->allow_backorders = $cartitem_data->allow_backorders;
		$this->backorder_fill_date = $cartitem_data->backorder_fill_date;
		$this->stock_quantity = $cartitem_data->stock_quantity;
		
		$this->image1 = $cartitem_data->image1;
		$this->image1_optionitem = $GLOBALS['ec_options']->get_optionitem_image1( $this->product_id, $cartitem_data->optionitem_id_1 );
		$this->image1_optionitem = apply_filters( 'wpeasycart_cartitem_image1_optionitem', $this->image1_optionitem );
		
		if( $cartitem_data->optionitem_id_1 ){
			$optionitem = $GLOBALS['ec_options']->get_optionitem( $cartitem_data->optionitem_id_1 );
			$this->optionitem1_name = $GLOBALS['language']->convert_text( $optionitem->optionitem_name ); 
			$this->optionitem1_price = $optionitem->optionitem_price;
			$this->optionitem1_weight = $optionitem->optionitem_weight;
			if( $cartitem_data->option_id_1 != 0 ){
				$option = $GLOBALS['ec_options']->get_option( $cartitem_data->option_id_1 );
				$this->optionitem1_label = $GLOBALS['language']->convert_text( $option->option_label );
			}
			$this->optionitem1_id = $optionitem->optionitem_id;
			if( $optionitem->optionitem_model_number != "" )
				$this->orderdetails_model_number = $this->orderdetails_model_number . get_option( 'ec_option_model_number_extension' ) . $optionitem->optionitem_model_number;
		}else{
			$this->optionitem1_name = $this->optionitem1_label = ""; $this->optionitem1_price = 0.00; $this->optionitem1_weight = 0.00; $this->optionitem1_id = 0;
		}
		
		if($cartitem_data->optionitem_id_2 ){
			$optionitem = $GLOBALS['ec_options']->get_optionitem( $cartitem_data->optionitem_id_2 );
			$this->optionitem2_name = $GLOBALS['language']->convert_text( $optionitem->optionitem_name ); 
			$this->optionitem2_price = $optionitem->optionitem_price;
			$this->optionitem2_weight = $optionitem->optionitem_weight;
			if( $cartitem_data->option_id_2 != 0 ){
				$option = $GLOBALS['ec_options']->get_option( $cartitem_data->option_id_2 );
				$this->optionitem2_label = $GLOBALS['language']->convert_text( $option->option_label );
			}
			$this->optionitem2_id = $optionitem->optionitem_id;
			if( $optionitem->optionitem_model_number != "" )
				$this->orderdetails_model_number = $this->orderdetails_model_number . get_option( 'ec_option_model_number_extension' ) . $optionitem->optionitem_model_number;
		}else{
			$this->optionitem2_name = $this->optionitem2_label = ""; $this->optionitem2_price = 0.00; $this->optionitem2_weight = 0.00; $this->optionitem2_id = 0;
		}
		
		if($cartitem_data->optionitem_id_3 ){
			$optionitem = $GLOBALS['ec_options']->get_optionitem( $cartitem_data->optionitem_id_3 );
			$this->optionitem3_name = $GLOBALS['language']->convert_text( $optionitem->optionitem_name ); 
			$this->optionitem3_price = $optionitem->optionitem_price;
			$this->optionitem3_weight = $optionitem->optionitem_weight;
			if( $cartitem_data->option_id_3 != 0 ){
				$option = $GLOBALS['ec_options']->get_option( $cartitem_data->option_id_3 );
				$this->optionitem3_label = $GLOBALS['language']->convert_text( $option->option_label );
			}
			$this->optionitem3_id = $optionitem->optionitem_id;
			if( $optionitem->optionitem_model_number != "" )
				$this->orderdetails_model_number = $this->orderdetails_model_number . get_option( 'ec_option_model_number_extension' ) . $optionitem->optionitem_model_number;
		}else{
			$this->optionitem3_name = $this->optionitem3_label = ""; $this->optionitem3_price = 0.00; $this->optionitem3_weight = 0.00; $this->optionitem3_id = 0;
		}
		
		if($cartitem_data->optionitem_id_4 ){
			$optionitem = $GLOBALS['ec_options']->get_optionitem( $cartitem_data->optionitem_id_4 );
			$this->optionitem4_name = $GLOBALS['language']->convert_text( $optionitem->optionitem_name ); 
			$this->optionitem4_price = $optionitem->optionitem_price;
			$this->optionitem4_weight = $optionitem->optionitem_weight;
			if( $cartitem_data->option_id_4 != 0 ){
				$option = $GLOBALS['ec_options']->get_option( $cartitem_data->option_id_4 );
				$this->optionitem4_label = $GLOBALS['language']->convert_text( $option->option_label );
			}
			$this->optionitem4_id = $optionitem->optionitem_id;
			if( $optionitem->optionitem_model_number != "" )
				$this->orderdetails_model_number = $this->orderdetails_model_number . get_option( 'ec_option_model_number_extension' ) . $optionitem->optionitem_model_number;
		}else{
			$this->optionitem4_name = $this->optionitem4_label = ""; $this->optionitem4_price = 0.00; $this->optionitem4_weight = 0.00; $this->optionitem4_id = 0;
		}
		
		if($cartitem_data->optionitem_id_5 ){
			$optionitem = $GLOBALS['ec_options']->get_optionitem( $cartitem_data->optionitem_id_5 );
			$this->optionitem5_name = $GLOBALS['language']->convert_text( $optionitem->optionitem_name ); 
			$this->optionitem5_price = $optionitem->optionitem_price;
			$this->optionitem5_weight = $optionitem->optionitem_weight;
			if( $cartitem_data->option_id_5 != 0 ){
				$option = $GLOBALS['ec_options']->get_option( $cartitem_data->option_id_5 );
				$this->optionitem5_label = $GLOBALS['language']->convert_text( $option->option_label );
			}
			$this->optionitem5_id = $optionitem->optionitem_id;
			if( $optionitem->optionitem_model_number != "" )
				$this->orderdetails_model_number = $this->orderdetails_model_number . get_option( 'ec_option_model_number_extension' ) . $optionitem->optionitem_model_number;
		}else{
			$this->optionitem5_name = $this->optionitem5_label = ""; $this->optionitem5_price = 0.00; $this->optionitem5_weight = 0.00; $this->optionitem5_id = 0;
		}
		
		$this->pricetiers = $GLOBALS['ec_pricetiers']->get_pricetiers( $this->product_id );
		
		$this->use_advanced_optionset = $cartitem_data->use_advanced_optionset;
		$this->optionitem_stock_quantity = 0;
		if( $this->use_optionitem_quantity_tracking ){
			global $wpdb;
			$optionitem_sql = "SELECT quantity FROM ec_optionitemquantity WHERE product_id = %d AND optionitem_id_1 = %d AND optionitem_id_2 = %d AND optionitem_id_3 = %d AND optionitem_id_4 = %d AND optionitem_id_5 = %d";
			$this->optionitem_stock_quantity = $wpdb->get_row( $wpdb->prepare( $optionitem_sql, $this->product_id, $this->optionitem1_id, $this->optionitem2_id, $this->optionitem3_id, $this->optionitem4_id, $this->optionitem5_id ) );
		}
		
		$this->gift_card_message = $cartitem_data->gift_card_message;
		$this->gift_card_from_name = $cartitem_data->gift_card_from_name;
		$this->gift_card_to_name = $cartitem_data->gift_card_to_name;
		$this->gift_card_email = $cartitem_data->gift_card_email;
		
		$this->download_file_name = $cartitem_data->download_file_name;
		$this->amazon_key = $cartitem_data->amazon_key;
		$this->maximum_downloads_allowed = $cartitem_data->maximum_downloads_allowed;
		$this->download_timelimit_seconds = $cartitem_data->download_timelimit_seconds;
		
		$this->is_subscription_item = $cartitem_data->is_subscription_item;
		$this->subscription_bill_length = $cartitem_data->subscription_bill_length;
		$this->subscription_bill_period = $cartitem_data->subscription_bill_period;
		$this->subscription_bill_duration = $cartitem_data->subscription_bill_duration;
		$this->trial_period_days = $cartitem_data->trial_period_days;
		$this->subscription_signup_fee = $cartitem_data->subscription_signup_fee;
		$this->subscription_prorate = $cartitem_data->subscription_prorate;
		$this->stripe_plan_added = $cartitem_data->stripe_plan_added;
		$this->subscription_unique_id = $cartitem_data->subscription_unique_id;
		
		$this->use_optionitem_quantity_tracking = $cartitem_data->use_optionitem_quantity_tracking;
		$this->optionitem_stock_quantity = 0;
		if( $this->use_optionitem_quantity_tracking ){
			global $wpdb;
			$optionitem_sql = "SELECT quantity FROM ec_optionitemquantity WHERE product_id = %d AND optionitem_id_1 = %d AND optionitem_id_2 = %d AND optionitem_id_3 = %d AND optionitem_id_4 = %d AND optionitem_id_5 = %d";
			$this->optionitem_stock_quantity = $wpdb->get_var( $wpdb->prepare( $optionitem_sql, $this->product_id, $this->optionitem1_id, $this->optionitem2_id, $this->optionitem3_id, $this->optionitem4_id, $this->optionitem5_id ) );
		}
		
		// Determine Quantity Info
		$this->track_quantity = false;
		$this->max_quantity = 10000000;
		$this->min_quantity = 1;
		
		if( $this->min_purchase_quantity > 0 || $this->max_purchase_quantity > 0 || $this->show_stock_quantity || $this->use_optionitem_quantity_tracking ){
			
			$this->track_quantity = true;
			if( $this->max_purchase_quantity > 0 ){ 
				$this->max_quantity = $this->max_purchase_quantity; 
			}
			
			if( !$this->allow_backorders && $this->show_stock_quantity && $this->stock_quantity < $this->max_quantity ){ 
				$this->max_quantity = $this->stock_quantity; 
			}
			
			if( !$this->allow_backorders && $this->use_optionitem_quantity_tracking && $this->optionitem_stock_quantity < $this->max_quantity ){ 
				$this->max_quantity = $this->optionitem_stock_quantity; 
			}
			
			if( $this->min_purchase_quantity > 0 ){ 
				$this->min_quantity = $this->min_purchase_quantity; 
			}
			
		}
		
		$this->donation_price = $cartitem_data->donation_price;
		
		$this->is_deconetwork = $cartitem_data->is_deconetwork;
		$this->deconetwork_id = $cartitem_data->deconetwork_id;
		$this->deconetwork_name = str_replace( "%2F", "/", str_replace( "%3F", "?", str_replace( "%3D", "=", str_replace( "%26", "&", $cartitem_data->deconetwork_name ) ) ) );
		$this->deconetwork_product_code = $cartitem_data->deconetwork_product_code;
		$this->deconetwork_options = str_replace( "<br/><br/>", "<br/>", str_replace( "%2F", "/", str_replace( "%3F", "?", str_replace( "%3D", "=", str_replace( "%26", "&", str_replace( "%3A", ":", str_replace( "%2C", ",", str_replace( "%3C", "<", str_replace( "%3E", ">", $cartitem_data->deconetwork_options ) ) ) ) ) ) ) ) );
		$this->deconetwork_edit_link = str_replace( "%2F", "/", str_replace( "%3F", "?", str_replace( "%3D", "=", str_replace( "%26", "&", $cartitem_data->deconetwork_edit_link ) ) ) );
		$this->deconetwork_color_code = $cartitem_data->deconetwork_color_code;
		$this->deconetwork_product_id = $cartitem_data->deconetwork_product_id;
		$this->deconetwork_image_link = str_replace( "%2F", "/", str_replace( "%3F", "?", str_replace( "%3D", "=", str_replace( "%26", "&", $cartitem_data->deconetwork_image_link ) ) ) );
		$this->deconetwork_discount = $cartitem_data->deconetwork_discount;
		$this->deconetwork_tax = $cartitem_data->deconetwork_tax;
		$this->deconetwork_total = $cartitem_data->deconetwork_total;
		$this->deconetwork_version = $cartitem_data->deconetwork_version;
		
		if( isset( $GLOBALS['ec_hooks']['ec_extra_cartitem_vars'] ) ){
			for( $i=0; $i<count( $GLOBALS['ec_hooks']['ec_extra_cartitem_vars'] ); $i++ ){
				$arr = $GLOBALS['ec_hooks']['ec_extra_cartitem_vars'][$i][0]( array( ), array( ) );
				for( $j=0; $j<count( $arr ); $j++ ){
					$this->custom_vars[ $arr[$j] ] =  $cartitem_data->{$arr[$j]};
				}
			}
		}
		$options_price = 0;
		$this->options_price_onetime = 0;
		$options_weight = 0;
		$options_weight_onetime = 0;
		$grid_weight_change = 0;
		$this->grid_price_change = 0;
		$price_multiplier = 0;
		$weight_multiplier = 0;
		
		$this->advanced_options = $GLOBALS['ec_cart_data']->get_advanced_cart_options( $this->cartitem_id );
			
		// Loop through options, select correct text if transalation used
		for( $adv_index = 0; $adv_index < count( $this->advanced_options ); $adv_index++ ){
			if( isset( $this->advanced_options[$adv_index]->option_label ) )
				$this->advanced_options[$adv_index]->option_label = $GLOBALS['language']->convert_text( $this->advanced_options[$adv_index]->option_label );
			$this->advanced_options[$adv_index]->optionitem_value = $GLOBALS['language']->convert_text( $this->advanced_options[$adv_index]->optionitem_value );
			if( isset( $this->advanced_options[$adv_index]->optionitem_download_override_file ) ){
				$this->download_file_name = $this->advanced_options[$adv_index]->optionitem_download_override_file;
			}
		}
		
		$grid_id = 0;
		
		if( $this->use_advanced_optionset ){
			
			foreach( $this->advanced_options as $advanced_option ){
				
				if( $advanced_option->optionitem_disallow_shipping ){
					$this->is_shippable = false;
				}
				
				if( $advanced_option->optionitem_model_number != "" )
					$this->orderdetails_model_number = $this->orderdetails_model_number . get_option( 'ec_option_model_number_extension' ) . $advanced_option->optionitem_model_number;
				
				if( $advanced_option->option_type == "grid" ){
					
					$grid_id = $advanced_option->option_id;
					
					if( $advanced_option->optionitem_price != 0 ){
						$this->grid_price_change = $this->grid_price_change + ( $advanced_option->optionitem_price * $advanced_option->optionitem_value ); 
					}else if( $advanced_option->optionitem_price_onetime != 0 ){ 
						$this->grid_price_change = $this->grid_price_change + $advanced_option->optionitem_price_onetime; 
					}else if( $advanced_option->optionitem_price_override >= 0 ){
						$this->grid_price_change = $this->grid_price_change + ( ( $advanced_option->optionitem_price_override - $cartitem_data->price ) * $advanced_option->optionitem_value );
					}else if( $advanced_option->optionitem_price_multiplier > 1 ){
						$this->grid_price_change = $cartitem_data->price * ( $advanced_option->optionitem_price_multiplier - 1 );
					}
					
					if( $advanced_option->optionitem_weight != 0 ){
						$grid_weight_change = $grid_weight_change + ( $advanced_option->optionitem_weight * $advanced_option->optionitem_value ); 
					}else if( $advanced_option->optionitem_weight_onetime != 0 ){ 
						$grid_weight_change = $grid_weight_change + $advanced_option->optionitem_weight_onetime; 
					}else if( $advanced_option->optionitem_weight_override >= 0 ){
						$grid_weight_change = $grid_weight_change + ( ( $advanced_option->optionitem_weight_override - $cartitem_data->weight ) * $advanced_option->optionitem_value );
					}else if( $advanced_option->optionitem_weight_multiplier > 1 ){
						$grid_weight_change = $cartitem_data->weight * ( $advanced_option->optionitem_weight_multiplier - 1 );
					}
					
				}else if( $advanced_option->option_type == "number" ){
					if( $advanced_option->optionitem_price != 0 ){
						$options_price = $options_price + ( $advanced_option->optionitem_price * $advanced_option->optionitem_value ); 
					}else if( $advanced_option->optionitem_price_onetime != 0 ){ 
						$this->options_price_onetime = $this->options_price_onetime + $advanced_option->optionitem_price_onetime; 
					}else if( $advanced_option->optionitem_price_override >= 0 ){
						$cartitem_data->price = $advanced_option->optionitem_price_override;
					}
					
					if( $advanced_option->optionitem_price_multiplier != 0 ){
						if( $price_multiplier == 0 )
							$price_multiplier = 1;
						$price_multiplier = $price_multiplier * $advanced_option->optionitem_price_multiplier * $advanced_option->optionitem_value;
					}
					
					if( $advanced_option->optionitem_weight != 0 ){
						$options_weight = $options_weight + ( $advanced_option->optionitem_weight * $advanced_option->optionitem_value ); 
					}else if( $advanced_option->optionitem_weight_onetime != 0 ){ 
						$options_weight_onetime = $options_weight_onetime + $advanced_option->optionitem_weight_onetime; 
					}else if( $advanced_option->optionitem_weight_override >= 0 ){
						$this->weight = $advanced_option->optionitem_weight_override;
					}
					
					if( $advanced_option->optionitem_weight_multiplier > 1 ){
						$weight_multiplier = $advanced_option->optionitem_weight_multiplier * $advanced_option->optionitem_value;
					}
					
				}else if( $advanced_option->option_type == "dimensions1" || $advanced_option->option_type == "dimensions2" ){
					$dimensions = json_decode( $advanced_option->optionitem_value ); 
					
					if( count( $dimensions ) == 2 ){ 
						if( !get_option( 'ec_option_enable_metric_unit_display' ) ){
							$cartitem_data->price = $cartitem_data->price * ( ( $dimensions[0] / 12 ) * ( $dimensions[1] / 12 ) );
							$cartitem_data->weight = $cartitem_data->weight * ( ( $dimensions[0] / 12 ) * ( $dimensions[1] / 12 ) );
						}else{
							$cartitem_data->price = $cartitem_data->price * ( ( $dimensions[0] / 1000 ) * ( $dimensions[1] / 1000 ) );
							$cartitem_data->weight = $cartitem_data->weight * ( ( $dimensions[0] / 1000 ) * ( $dimensions[1] / 1000 ) );
						}
					}else if( count( $dimensions ) == 4 ){ 
						if( !get_option( 'ec_option_enable_metric_unit_display' ) ){
							$cartitem_data->price = $cartitem_data->price * ( ( ( intval( $dimensions[0] ) + $this->get_dimension_decimal( $dimensions[1] ) ) / 12 ) * ( ( intval( $dimensions[2] ) + $this->get_dimension_decimal( $dimensions[3] ) ) / 12 ) );
							$cartitem_data->weight = $cartitem_data->weight * ( ( ( intval( $dimensions[0] ) + $this->get_dimension_decimal( $dimensions[1] ) ) / 12 ) * ( ( intval( $dimensions[2] ) + $this->get_dimension_decimal( $dimensions[3] ) ) / 12 ) );
						}else{
							$cartitem_data->price = $cartitem_data->price * ( ( ( intval( $dimensions[0] ) + $this->get_dimension_decimal( $dimensions[1] ) ) / 1000 ) * ( ( intval( $dimensions[2] ) + $this->get_dimension_decimal( $dimensions[3] ) ) / 1000 ) );
							$cartitem_data->weight = $cartitem_data->weight * ( ( ( intval( $dimensions[0] ) + $this->get_dimension_decimal( $dimensions[1] ) ) / 1000 ) * ( ( intval( $dimensions[2] ) + $this->get_dimension_decimal( $dimensions[3] ) ) / 1000 ) );
						}
					}
					
				}else{
					if( $advanced_option->optionitem_price != 0 ){
						$options_price = $options_price + $advanced_option->optionitem_price; 
					}else if( $advanced_option->optionitem_price_onetime != 0 ){ 
						$this->options_price_onetime = $this->options_price_onetime + $advanced_option->optionitem_price_onetime; 
					}else if( $advanced_option->optionitem_price_override >= 0 ){
						$cartitem_data->price = $advanced_option->optionitem_price_override;
					}
					
					if( $advanced_option->optionitem_price_multiplier != 0 ){
						if( $price_multiplier == 0 )
							$price_multiplier = 1;
						$price_multiplier = $price_multiplier * $advanced_option->optionitem_price_multiplier;
					}
					
					if( $advanced_option->optionitem_price_per_character > 0 ){
						$num_chars = strlen( preg_replace('/\s+/', '', $advanced_option->optionitem_value ) );
						$options_price = $options_price + ( $num_chars * $advanced_option->optionitem_price_per_character );
					}
					
					if( $advanced_option->optionitem_weight != 0 ){
						$options_weight = $options_weight + $advanced_option->optionitem_weight; 
					}else if( $advanced_option->optionitem_weight_onetime != 0 ){ 
						$options_weight_onetime = $options_weight_onetime + $advanced_option->optionitem_weight_onetime; 
					}else if( $advanced_option->optionitem_weight_override >= 0 ){
						$this->weight = $advanced_option->optionitem_weight_override;
					}
					
					if( $advanced_option->optionitem_weight_multiplier > 1 ){
						$weight_multiplier = $advanced_option->optionitem_weight_multiplier;
					}
				}
			}
			for( $i=0; $i<count( $this->advanced_options ); $i++ ){
				$this->advanced_options[$i]->option_name = $GLOBALS['language']->convert_text( $this->advanced_options[$i]->option_name );
				$this->advanced_options[$i]->optionitem_name = $GLOBALS['language']->convert_text( $this->advanced_options[$i]->optionitem_name );
				$this->advanced_options[$i]->optionitem_value = $GLOBALS['language']->convert_text( $this->advanced_options[$i]->optionitem_value );
			}
		}else{
			$options_price = $this->optionitem1_price + $this->optionitem2_price + $this->optionitem3_price + $this->optionitem4_price + $this->optionitem5_price;
			$options_weight = $this->optionitem1_weight + $this->optionitem2_weight + $this->optionitem3_weight + $this->optionitem4_weight + $this->optionitem5_weight;
		}
		
		// Update the weight from option item weight
		$this->weight = $this->weight + $options_weight + $grid_weight_change;
		
		// Look for role based pricing
		$roleprice = $GLOBALS['ec_roleprices']->get_roleprice( $this->product_id );
		
		if( $this->is_donation ){
			$this->unit_price = $cartitem_data->donation_price + $options_price;
		}else if( $roleprice ){
			$this->unit_price = $roleprice + $options_price;
		}else if( count( $this->pricetiers ) > 0 ){
			
			if( $grid_id == 0 && get_option( 'ec_option_tiered_price_by_option' ) )
				$total_items = $this->quantity;
			else if( $grid_id == 0 )
				$total_items = $this->mysqli->get_total_cart_items_by_product_id( $this->product_id, $GLOBALS['ec_cart_data']->ec_cart_id );
			else{
				$total_items = $this->mysqli->get_total_cart_items_with_grid_by_product_id( $this->product_id, $grid_id, $GLOBALS['ec_cart_data']->ec_cart_id );
			}
			
			$this->unit_price = $cartitem_data->price + $options_price;
			for( $i=0; $i<count( $this->pricetiers ); $i++ ){
				if( $total_items >= $this->pricetiers[$i]->quantity ){
					$this->unit_price = $this->pricetiers[$i]->price + $options_price;	
				}
			}
		}else{
			$this->unit_price = $cartitem_data->price + $options_price;	
		}
		
		if( $price_multiplier > 1 ){
			$this->unit_price = $this->unit_price * $price_multiplier;
		}
		
		if( $weight_multiplier > 1 ){
			$this->weight = $this->weight * $weight_multiplier;
		}
		
		$this->total_price = ( $this->unit_price * $this->quantity ) + $this->options_price_onetime + $this->grid_price_change;
		$this->total_price = apply_filters( 'wp_easycart_cart_item_total_price', $this->total_price, $this->cartitem_id, $this->product_id );
		$this->converted_total_price = ( $GLOBALS['currency']->convert_price( $this->unit_price ) * $this->quantity ) + $GLOBALS['currency']->convert_price( $this->options_price_onetime ) + $GLOBALS['currency']->convert_price( $this->grid_price_change );
		$this->converted_total_price = apply_filters( 'wp_easycart_cart_item_total_price', $this->converted_total_price, $this->cartitem_id, $this->product_id );
		$this->total_weight = ( $this->weight * $this->quantity ) + $options_weight_onetime;
		$this->handling_price = $cartitem_data->handling_price;
		$this->handling_price_each = $cartitem_data->handling_price_each;
		
		if( $cartitem_data->vat_rate > 0 )
			$this->vat_enabled = true;
		else
			$this->vat_enabled = false;
		
		if( $this->is_deconetwork ){
			$this->unit_price = $this->deconetwork_total / $this->quantity;
			$this->total_price = $this->deconetwork_total;
			$this->discount_price = $this->deconetwork_discount;
		}
		
		$this->has_affiliate_rule = false;
		
		/*if( class_exists( "Affiliate_WP" ) ){
			$this->affiliate_rule = $this->mysqli->get_affiliate_rule( affiliate_wp()->tracking->get_affiliate_id( ), $this->product_id );
			if( $this->affiliate_rule )
				$this->has_affiliate_rule = true;
		}*/
			
		$store_page_id = get_option('ec_option_storepage');
		$cart_page_id = get_option('ec_option_cartpage');
		
		if( function_exists( 'icl_object_id' ) ){
			$store_page_id = icl_object_id( $store_page_id, 'page', true, ICL_LANGUAGE_CODE );
			$cart_page_id = icl_object_id( $cart_page_id, 'page', true, ICL_LANGUAGE_CODE );
		}
		
		$this->store_page = get_permalink( $store_page_id );
		$this->cart_page = get_permalink( $cart_page_id );
		
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$this->store_page = $https_class->makeUrlHttps( $this->store_page );
			$this->cart_page = $https_class->makeUrlHttps( $this->cart_page );
		}
		
		if( substr_count( $this->cart_page, '?' ) )					$this->permalink_divider = "&";
		else														$this->permalink_divider = "?";
	}
	
	private function get_dimension_decimal( $value ){
		
		if( $value == "1/16" ){
			return .0625;
		}else if( $value == "1/8" ){
			return .1250;
		}else if( $value == "3/16" ){
			return .1875;
		}else if( $value == "1/4" ){
			return .2500;
		}else if( $value == "5/16" ){
			return .3125;
		}else if( $value == "3/8" ){
			return .3750;
		}else if( $value == "7/16" ){
			return .4375;
		}else if( $value == "1/2" ){
			return .5000;
		}else if( $value == "9/16" ){
			return .5625;
		}else if( $value == "5/8" ){
			return .6250;
		}else if( $value == "11/16" ){
			return .6875;
		}else if( $value == "3/4" ){
			return .7500;
		}else if( $value == "13/16" ){
			return .8125;
		}else if( $value == "7/8" ){
			return .8750;
		}else if( $value == "15/16" ){
			return .9375;
		}else{
			return 0;
		}
		
	}
	
	public function display_cartitem_id(){
		echo $this->cartitem_id;
	}
	
	public function get_quantity(){
		return $this->quantity;
	}
	
	public function display_quantity(){
		echo $this->quantity;
	}
	
	public function get_item_unit_price(){
		return $this->unit_price;
	}
	
	public function get_discount_unit_price(){
		return $this->discount_price;
	}
	
	public function get_item_total(){
		return $this->total_price;
	}
	
	public function get_weight(){
		return $this->total_weight;
	}
	
	public function get_shippable_total(){
		if( $this->is_shippable ){
			return $this->total_price;
		}else{
			return 0;
		}
	}
	
	public function display_image( $size ){
		
		if( $this->is_deconetwork ){
		
			echo "<a href=\"https://" . get_option( 'ec_option_deconetwork_url' ) . $this->deconetwork_edit_link . "\"><img src=\"https://" . get_option( 'ec_option_deconetwork_url' ) . $this->deconetwork_image_link . "?version=" . $this->deconetwork_version . "\" alt=\"" . $this->model_number . "\" /></a>";
		
		}else{
			
			echo "<a href=\"" . $this->ec_get_permalink( $this->post_id );
		
			if( substr_count( $this->ec_get_permalink( $this->post_id ), '?' ) ){
				$second_permalink_divider = "&";
			}else{
				$second_permalink_divider = "?";
			}
			
			if( $this->image1_optionitem ){
				if( substr( $this->image1_optionitem, 0, 7 ) == 'http://' || substr( $this->image1_optionitem, 0, 8 ) == 'https://' )
					echo $second_permalink_divider . "optionitem_id=" . $this->optionitem1_id . "\"><img src=\"" . $this->image1_optionitem . "\" alt=\"" . $this->model_number . "\" />";
				else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $this->image1_optionitem ) )
					echo $second_permalink_divider . "optionitem_id=" . $this->optionitem1_id . "\"><img src=\"" . plugins_url( "wp-easycart-data/products/pics1/" . $this->image1_optionitem ) . "\" alt=\"" . $this->model_number . "\" />";
				else
					echo $second_permalink_divider . "optionitem_id=" . $this->optionitem1_id . "\"><img src=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/products/pics1/" . $this->image1_optionitem ) . "\" alt=\"" . $this->model_number . "\" />";
			}else{
				if( substr( $this->image1 , 0, 7 ) == 'http://' || substr( $this->image1 , 0, 8 ) == 'https://' )
					echo "\"><img src=\"" . $this->image1 . "\" alt=\"" . $this->model_number . "\" />";
				
				else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $this->image1 ) )
					echo "\"><img src=\"" . plugins_url( "wp-easycart-data/products/pics1/" . $this->image1 ) . "\" alt=\"" . $this->model_number . "\" />";
				
				else if( file_exists( WP_PLUGIN_DIR . '/' . EC_PLUGIN_DIRECTORY . "/products/pics1/" . $this->image1 ) )
					echo "\"><img src=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/products/pics1/" . $this->image1 ) . "\" alt=\"" . $this->model_number . "\" />";
				
				else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg" ) )
					echo "\"><img src=\"" . plugins_url( "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg" ) . "\" alt=\"" . $this->model_number . "\" />";
				
				else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_image_not_found.jpg" ) )
					echo "\"><img src=\"" . plugins_url( "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_image_not_found.jpg" ) . "\" alt=\"" . $this->model_number . "\" />";
				
				else
					echo "\"><img src=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_image_not_found.jpg" ) . "\" alt=\"" . $this->model_number . "\" />";
			}
			echo "</a>";
			
		}
		
	}
	
	public function get_image_url( ){
		
		if( $this->is_deconetwork ){
			return "https://" . get_option( 'ec_option_deconetwork_url' ) . $this->deconetwork_image_link . "?version=" . $this->deconetwork_version;
		
		}else if( substr( $this->image1_optionitem, 0, 7 ) == 'http://' || substr( $this->image1_optionitem, 0, 8 ) == 'https://' ){
			return $this->image1_optionitem;
		
		}else if( $this->image1_optionitem && file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $this->image1_optionitem ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $this->image1_optionitem ) ){
			return plugins_url( "wp-easycart-data/products/pics1/" . $this->image1_optionitem );
		
		}else if( substr( $this->image1, 0, 7 ) == 'http://' || substr( $this->image1, 0, 8 ) == 'https://' ){
			return $this->image1;
		
		}else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $this->image1 ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $this->image1 ) ){
			return plugins_url( "wp-easycart-data/products/pics1/" . $this->image1 );
		
		}else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg" ) ){
			return plugins_url( "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg" );
		
		}else{
			return plugins_url( "/wp-easycart/design/theme/" . get_option( 'ec_option_latest_theme' ) . "/images/ec_image_not_found.jpg" );
		}
		
	}
	
	public function get_product_url( ){
		
		if( $this->is_deconetwork )
			return "https://" . get_option( 'ec_option_deconetwork_url' ) . $this->deconetwork_edit_link;
		else
			return $this->ec_get_permalink( $this->post_id );
		
	}
	
	public function display_title( ){
		
		if( $this->is_deconetwork )
			echo $this->deconetwork_name;
		else
			echo $this->title;
		
	}
	
	public function display_title_link( ){
		
		if( $this->is_deconetwork ){
			
			echo "<a href=\"https://" . get_option( 'ec_option_deconetwork_url' ) . $this->deconetwork_edit_link . "\">" . $this->deconetwork_name . "</a>";
			
		}else{
			
			echo "<a href=\"" . $this->ec_get_permalink( $this->post_id );
		
			if( substr_count( $this->ec_get_permalink( $this->post_id ), '?' ) ){
				$second_permalink_divider = "&";
			}else{
				$second_permalink_divider = "?";
			}
			
			if( $this->image1_optionitem )
				echo $second_permalink_divider . "optionitem_id=" . $this->optionitem1_id;
			
			echo "\">" . $this->title . "</a>";
			
		}
		
	}
	
	public function get_title_link( ){
		
		if( $this->is_deconetwork ){
			
			return "https://" . get_option( 'ec_option_deconetwork_url' ) . $this->deconetwork_edit_link;
			
		}else{
			
			$ret_string = $this->ec_get_permalink( $this->post_id );
		
			if( substr_count( $ret_string, '?' ) ){
				$second_permalink_divider = "&";
			}else{
				$second_permalink_divider = "?";
			}
			
			if( $this->image1_optionitem )
				$ret_string .= $second_permalink_divider . "optionitem_id=" . $this->optionitem1_id;
			
			return $ret_string;
			
		}
		
	}
	
	public function has_option1( ){
		
		if( ( $this->is_deconetwork && $this->deconetwork_options ) || $this->optionitem1_name )
			return true;
		else
			return false;
		
	}
	
	public function display_option1( ){
		
		if( $this->is_deconetwork ){
			
			echo str_replace( "<br/><br/>", "<br/>", $this->deconetwork_options );
			echo "<br/><a href=\"https://" . get_option( 'ec_option_deconetwork_url' ) . $this->deconetwork_edit_link . "\">Edit Design</a>";
			
		}else{
			
			if( $this->optionitem1_price == "0.00" &&  $this->optionitem1_name)
				echo $this->optionitem1_label . ": " . $this->optionitem1_name;
			else if( $this->optionitem1_name ){
				if( $this->optionitem1_price > 0.00 )
					echo $this->optionitem1_label . ": " . $this->optionitem1_name . " ( +" . $GLOBALS['currency']->get_currency_display( $this->optionitem1_price ) . " )";
				else
					echo $this->optionitem1_label . ": " . $this->optionitem1_name . " ( " . $GLOBALS['currency']->get_currency_display( $this->optionitem1_price ) . " )";
			}
			
		}
		
	}
	
	public function has_option2( ){
		if( $this->optionitem2_name )
			return true;
		else
			return false;
	}
	
	public function display_option2( ){
		if( $this->optionitem2_price == "0.00" &&  $this->optionitem2_name )
			echo $this->optionitem2_label . ": " . $this->optionitem2_name;
		else if( $this->optionitem2_name ){
			if( $this->optionitem2_price > 0.00 )
				echo $this->optionitem2_label . ": " . $this->optionitem2_name . " ( +" . $GLOBALS['currency']->get_currency_display( $this->optionitem2_price ) . " )";
			else
				echo $this->optionitem2_label . ": " . $this->optionitem2_name . " ( " . $GLOBALS['currency']->get_currency_display( $this->optionitem2_price ) . " )";
		}
	}
	
	public function has_option3( ){
		if( $this->optionitem3_name )
			return true;
		else
			return false;
	}
	
	public function display_option3( ){
		if( $this->optionitem3_price == "0.00" &&  $this->optionitem3_name )
			echo $this->optionitem3_label . ": " . $this->optionitem3_name;
		else if( $this->optionitem3_name ){
			if( $this->optionitem3_price > 0.00 )
				echo $this->optionitem3_label . ": " . $this->optionitem3_name . " ( +" . $GLOBALS['currency']->get_currency_display( $this->optionitem3_price ) . " )";
			else
				echo $this->optionitem3_label . ": " . $this->optionitem3_name . " ( " . $GLOBALS['currency']->get_currency_display( $this->optionitem3_price ) . " )";
		}
	}
	
	public function has_option4( ){
		if( $this->optionitem4_name )
			return true;
		else
			return false;
	}
	
	public function display_option4( ){
		if( $this->optionitem4_price == "0.00" &&  $this->optionitem4_name )
			echo $this->optionitem4_label . ": " . $this->optionitem4_name;
		else if( $this->optionitem4_name ){
			if( $this->optionitem4_price > 0.00 )
				echo $this->optionitem4_label . ": " . $this->optionitem4_name . " ( +" . $GLOBALS['currency']->get_currency_display( $this->optionitem4_price ) . " )";
			else
				echo $this->optionitem4_label . ": " . $this->optionitem4_name . " ( " . $GLOBALS['currency']->get_currency_display( $this->optionitem4_price ) . " )";
		}
	}
	
	public function has_option5( ){
		if( $this->optionitem5_name )
			return true;
		else
			return false;
	}
	
	public function display_option5( ){
		if( $this->optionitem5_price == "0.00" &&  $this->optionitem5_name )
			echo $this->optionitem5_label . ": " . $this->optionitem5_name;
		else if( $this->optionitem5_name ){
			if( $this->optionitem5_price > 0.00 )
				echo $this->optionitem5_label . ": " . $this->optionitem5_name . " ( +" . $GLOBALS['currency']->get_currency_display( $this->optionitem5_price ) . " )";
			else
				echo $this->optionitem5_label . ": " . $this->optionitem5_name . " ( " . $GLOBALS['currency']->get_currency_display( $this->optionitem5_price ) . " )";
		}
	}
	
	public function has_gift_card_message( ){
		if( $this->gift_card_message )
			return true;
		else
			return false;
	}
	
	public function display_gift_card_message( $message_text ){
		if( $this->gift_card_message )
			echo $message_text . $this->gift_card_message;
	}
	
	public function has_gift_card_from_name( ){
		if( $this->gift_card_from_name )
			return true;
		else
			return false;
	}
	
	public function display_gift_card_from_name( $from_text ){
		if( $this->gift_card_from_name )
			echo $from_text . $this->gift_card_from_name;
	}
	
	public function has_gift_card_to_name( ){
		if( $this->gift_card_to_name )
			return true;
		else
			return false;
	}
	
	public function display_gift_card_to_name( $to_text ){
		if( $this->gift_card_to_name )
			echo $to_text . $this->gift_card_to_name;
	}
	
	public function has_print_gift_card_link( ){
		if( $this->is_giftcard )
			return true;
		else
			return false;
	}
	
	public function has_download_link( ){
		if( $this->is_download )
			return true;
		else
			return false;
	}
	
	public function display_update_form_start( ){
		
		if( !$this->is_deconetwork ){
			
			if( isset( $_GET['ec_page'] ) )
				echo "<form action=\"" . $this->cart_page . $this->permalink_divider . "ec_page=" . htmlspecialchars ( $_GET['ec_page'], ENT_QUOTES ) . "\" method=\"post\">";
			else
				echo "<form action=\"" . $this->cart_page . "\" method=\"post\">";
		
		}
	
	}
	
	public function display_update_form_end( ){
		
		if( !$this->is_deconetwork ){
			
			echo "<input type=\"hidden\" name=\"ec_update_cartitem_id\" id=\"ec_update_cartitem_id_" . $this->cartitem_id . "\" value=\"" . $this->cartitem_id . "\" />";
			echo "<input type=\"hidden\" name=\"ec_cart_form_action\" id=\"ec_cart_form_action\" value=\"ec_update_action\" />";
			echo "</form>";	
		
		}
		
	}
	
	public function display_quantity_box( ){
		
		if( $this->is_deconetwork ){
			
			echo $this->quantity;
			
		}else{
			
			if( $this->grid_quantity > 0 ){
				echo "<input type=\"hidden\" id=\"ec_cartitem_quantity_" . $this->cartitem_id . "\" name=\"ec_cartitem_quantity_" . $this->cartitem_id . "\" value=\"" . $this->quantity . "\" min=\"1\" />" . $this->grid_quantity;
			}else{
				echo "<input type=\"number\" id=\"ec_cartitem_quantity_" . $this->cartitem_id . "\" name=\"ec_cartitem_quantity_" . $this->cartitem_id . "\" value=\"" . $this->quantity . "\" min=\"1\" />";
			}
			
		}
		
	}
	
	public function display_update_button( $update_text ){
		
		if( !$this->is_deconetwork ){
			
			echo "<input type=\"submit\" id=\"update_" . $this->cartitem_id . "\" name=\"update_" . $this->cartitem_id . "\" value=\"" . $update_text . "\" onclick=\"ec_cart_item_update( '" . $this->cartitem_id . "' ); return false;\" />";
			
		}
		
	}
	
	public function display_delete_button( $remove_text ){
		
		if( isset( $_GET['ec_page'] ) )
			echo "<form action=\"" . $this->cart_page . $this->permalink_divider . "ec_page=" . htmlspecialchars ( $_GET['ec_page'], ENT_QUOTES ) . "\" method=\"post\">";
		else
			echo "<form action=\"" . $this->cart_page . "\" method=\"post\">";
			
		echo "<input type=\"submit\" id=\"remove_" . $this->cartitem_id . "\" name=\"remove_" . $this->cartitem_id . "\" value=\"" . $remove_text . "\" onclick=\"ec_google_removeFromCart( '" . $this->model_number . "', '" . $this->title . "', document.getElementById( 'ec_cartitem_quantity_" . $this->cartitem_id . "' ), '" . number_format( $this->unit_price, 2, '.', '' ) . "' ); ec_cart_item_delete( '" . $this->cartitem_id . "' ); return false;\"/>";
		echo "<input type=\"hidden\" name=\"ec_cart_form_action\" id=\"ec_cart_form_action\" value=\"ec_delete_action\" />";
		echo "<input type=\"hidden\" name=\"ec_delete_cartitem_id\" id=\"ec_delete_cartitem_id_" . $this->cartitem_id . "\" value=\"" . $this->cartitem_id . "\" />";
		echo "</form>";
	
	}
	
	public function display_unit_price( ){
		
		if( $this->is_deconetwork ){
			
			echo "<span id=\"ec_cartitem_unit_price_" . $this->cartitem_id . "\">" . $GLOBALS['currency']->get_currency_display( $this->deconetwork_total / $this->quantity ) . "</span>";
			
		}else{
			
			echo "<span id=\"ec_cartitem_unit_price_" . $this->cartitem_id . "\">" . $GLOBALS['currency']->get_currency_display( $this->unit_price ) . "</span>";
		
			if( $this->prev_price )
				echo "<span id=\"ec_cartitem_prev_price_" . $this->cartitem_id . "\" class=\"ec_product_old_price\">" . $GLOBALS['currency']->get_currency_display( $this->prev_price ) . "</span>";
				
		}
		
	}
	
	public function get_unit_price( ){
		
		if( $this->is_deconetwork )
			return $GLOBALS['currency']->get_currency_display( $this->deconetwork_total / $this->quantity );
		else
			return apply_filters( 'wp_easycart_cart_item_unit_price_display', $GLOBALS['currency']->get_currency_display( $this->unit_price ), $this->product_id );
		
	}
	
	public function display_item_total( ){
		
		if( $this->is_deconetwork ){
			
			echo "<span id=\"ec_cartitem_unit_price_" . $this->cartitem_id . "\">" . $GLOBALS['currency']->get_currency_display( $this->deconetwork_total ) . "</span>";
			
		}else{
		
			echo "<span id=\"ec_cartitem_total_" . $this->cartitem_id . "\">" . $GLOBALS['currency']->get_currency_display( $this->total_price ) . "</span>";
		
		}
		
	}
	
	public function get_total( ){
		
		if( $this->is_deconetwork )
			return $GLOBALS['currency']->get_currency_display( $this->deconetwork_total );
		else
			return ( $GLOBALS['currency']->get_conversion_rate( ) == 1 ) ? $GLOBALS['currency']->get_currency_display( $this->total_price, false ) : $GLOBALS['currency']->get_currency_display( $this->converted_total_price, false );
		
	}
	
	public function display_vat_rate( ){
		if( $this->vat_enabled )
			if( isset( $GLOBALS['ec_vat_rate'] ) )
				echo number_format( $GLOBALS['ec_vat_rate'], 0 );	
			else{
				$tax_struct = new ec_tax( 0,0,0,"","" );
				echo number_format( $tax_struct->vat_rate , 0 );
			}	
		else
			echo number_format( 0, 0 );	
	}
	
	public function display_item_loader( ){
		if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_page/loader.gif" ) )	
			echo "<div class=\"ec_cart_item_loader\" id=\"ec_cart_item_loader_" . $this->cartitem_id . "\"><img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_page/loader.gif" ) . "\" /></div>";	
		else
			echo "<div class=\"ec_cart_item_loader\" id=\"ec_cart_item_loader_" . $this->cartitem_id . "\"><img src=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_page/loader.gif" ) . "\" /></div>";
	}
	
	public function get_advanced_options( ){
		return $this->advanced_options;
	}
	
	private function ec_get_permalink( $postid ){
		
		if( !get_option( 'ec_option_use_old_linking_style' ) && $postid != "0" ){
			return $this->guid;
		}else{
			return $this->store_page . $this->permalink_divider . "model_number=" . $this->model_number;
		}
		
	}
	
	static function ec_sort_price_tier( $a, $b ){
		if( $a[1] > $b[1] ){
			return 1;
		}else{
			return -1;
		}
	}
}

?>