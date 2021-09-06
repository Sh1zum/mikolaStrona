<?php

class ec_cartpage{
	protected $mysqli;							// ec_db structure
	public $cart;								// ec_cart structure
	public $user;								// ec_user
	public $tax;								// ec_tax structure
	public $shipping;							// ec_shipping structure
	public $discount;							// ec_discount structure
	public $order_totals;						// ec_order_totals structure
	public $payment;							// ec_payment structure
	public $order;								// ec_order structure
	public $coupon;								// ec_coupon structure
	public $giftcard;							// ec_giftcard structure
	
	public $coupon_code;						// VARCHAR
	public $gift_card;							// VARCHAR
	
	public $subscription_option1;				// Option Item ID
	public $subscription_option2;				// Option Item ID
	public $subscription_option3;				// Option Item ID
	public $subscription_option4;				// Option Item ID
	public $subscription_option5;				// Option Item ID
	
	public $subscription_option1_name;			// Option Item Name
	public $subscription_option2_name;			// Option Item Name
	public $subscription_option3_name;			// Option Item Name
	public $subscription_option4_name;			// Option Item Name
	public $subscription_option5_name;			// Option Item Name
	
	public $subscription_option1_label;			// Option Item Label
	public $subscription_option2_label;			// Option Item Label
	public $subscription_option3_label;			// Option Item Label
	public $subscription_option4_label;			// Option Item Label
	public $subscription_option5_label;			// Option Item Label
	
	public $subscription_advanced_options;		// Array
	
	public $has_downloads;						// BOOL
	
	public $store_page;							// VARCHAR
	public $cart_page;							// VARCHAR
	public $account_page;						// VARCHAR
	public $permalink_divider;					// CHAR
	
	private $analytics;							// ec_googleanalytics class
	private $is_affirm;
	
	////////////////////////////////////////////////////////
	// CONSTUCTOR FUNCTION
	////////////////////////////////////////////////////////
	function __construct( $is_affirm = false ){
		
		add_filter( 'wp_easycart_live_rate_pre', array( $this, 'maybe_add_free_shipping' ), 10, 1 );
		add_filter( 'wp_easycart_method_rate_pre', array( $this, 'maybe_add_free_shipping' ), 10, 1 );
		add_filter( 'wp_easycart_trigger_rate', array( $this, 'maybe_set_trigger_rate_free' ), 10, 1 );
		
		$this->is_affirm = $is_affirm;
		
		$this->mysqli = new ec_db();
		$this->cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$this->user =& $GLOBALS['ec_user'];
		// For the cart, alter the user to use the saved data only.
		if( $GLOBALS['ec_cart_data']->cart_data->is_guest == "" ){
		
			if( isset( $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip ) )
				$estimate_shipping_zip = $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip;
			else
				$estimate_shipping_zip = "";
			
			if( isset( $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_country ) )
				$estimate_shipping_country = $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_country;
			else
				$estimate_shipping_country = "";
			
			if( isset( $GLOBALS['ec_cart_data']->cart_data->billing_zip ) )
				$billing_zip = $GLOBALS['ec_cart_data']->cart_data->billing_zip;
			else
				$billing_zip = "";
			
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_zip ) )
				$shipping_zip = $GLOBALS['ec_cart_data']->cart_data->shipping_zip;
			else
				$shipping_zip = "";
			
			if( isset( $GLOBALS['ec_cart_data']->cart_data->billing_country ) )
				$billing_country = $GLOBALS['ec_cart_data']->cart_data->billing_country;
			else
				$billing_country = "";
			
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_country ) )
				$shipping_country = $GLOBALS['ec_cart_data']->cart_data->shipping_country;
			else
				$shipping_country = "";
			
			if( isset( $GLOBALS['ec_cart_data']->cart_data->billing_state ) )
				$billing_state = $GLOBALS['ec_cart_data']->cart_data->billing_state;
			else
				$billing_state = "";
			
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_state ) )
				$shipping_state = $GLOBALS['ec_cart_data']->cart_data->shipping_state;
			else
				$shipping_state = "";
				
			if( $billing_zip == "" )
				$billing_zip = $estimate_shipping_zip;
			if( $shipping_zip == "" )
				$shipping_zip = $estimate_shipping_zip;
			if( $billing_country == "" )
				$billing_country = $estimate_shipping_country;
			if( $shipping_country == "" )
				$shipping_country = $estimate_shipping_country;
				
		}else if( $GLOBALS['ec_cart_data']->cart_data->is_guest != "" && $GLOBALS['ec_cart_data']->cart_data->is_guest ){
			if( isset( $GLOBALS['ec_cart_data']->cart_data->billing_zip ) )
				$billing_zip = $GLOBALS['ec_cart_data']->cart_data->billing_zip;
			else
				$billing_zip = "";
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_zip ) )
				$shipping_zip = $GLOBALS['ec_cart_data']->cart_data->shipping_zip;
			else
				$shipping_zip = "";
			if( isset( $GLOBALS['ec_cart_data']->cart_data->billing_country ) )
				$billing_country = $GLOBALS['ec_cart_data']->cart_data->billing_country;
			else
				$billing_country = "";
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_country ) )
				$shipping_country = $GLOBALS['ec_cart_data']->cart_data->shipping_country;
			else
				$shipping_country = "";
		}
			
		if( $GLOBALS['ec_cart_data']->cart_data->is_guest != "" && $GLOBALS['ec_cart_data']->cart_data->is_guest ){
			
			$billing_first_name = $billing_last_name = $billing_company = $billing_address_line_1 = $billing_address_line_2 = $billing_city = $billing_state = $billing_phone = "";
			if( isset( $GLOBALS['ec_cart_data']->cart_data->billing_first_name ) )
				$billing_first_name = $GLOBALS['ec_cart_data']->cart_data->billing_first_name;
			if( isset( $GLOBALS['ec_cart_data']->cart_data->billing_last_name ) )
				$billing_last_name = $GLOBALS['ec_cart_data']->cart_data->billing_last_name;
			if( isset( $GLOBALS['ec_cart_data']->cart_data->billing_company_name ) )
				$billing_company = $GLOBALS['ec_cart_data']->cart_data->billing_company_name;
			if( isset( $GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 ) )
				$billing_address_line_1 = $GLOBALS['ec_cart_data']->cart_data->billing_address_line_1;
			if( isset( $GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 ) )
				$billing_address_line_2 = $GLOBALS['ec_cart_data']->cart_data->billing_address_line_2;
			if( isset( $GLOBALS['ec_cart_data']->cart_data->billing_city ) )
				$billing_city = $GLOBALS['ec_cart_data']->cart_data->billing_city;
			if( isset( $GLOBALS['ec_cart_data']->cart_data->billing_state ) )
				$billing_state = $GLOBALS['ec_cart_data']->cart_data->billing_state;
			if( isset( $GLOBALS['ec_cart_data']->cart_data->billing_phone ) )
				$billing_phone = $GLOBALS['ec_cart_data']->cart_data->billing_phone;
			$this->user->setup_billing_info_data( $billing_first_name, $billing_last_name, $billing_address_line_1, $billing_address_line_2, $billing_city, $billing_state, $billing_country, $billing_zip, $billing_phone, $billing_company );
			
			$shipping_first_name = $shipping_last_name = $shipping_company = $shipping_address_line_1 = $shipping_address_line_2 = $shipping_city = $shipping_state = $shipping_phone = "";
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_first_name ) )
				$shipping_first_name = $GLOBALS['ec_cart_data']->cart_data->shipping_first_name;
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_last_name ) )
				$shipping_last_name = $GLOBALS['ec_cart_data']->cart_data->shipping_last_name;
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_company_name ) )
				$shipping_company = $GLOBALS['ec_cart_data']->cart_data->shipping_company_name;
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 ) )
				$shipping_address_line_1 = $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1;
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 ) )
				$shipping_address_line_2 = $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2;
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_city ) )
				$shipping_city = $GLOBALS['ec_cart_data']->cart_data->shipping_city;
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_state ) )
				$shipping_state = $GLOBALS['ec_cart_data']->cart_data->shipping_state;
			if( isset( $GLOBALS['ec_cart_data']->cart_data->shipping_phone ) )
				$shipping_phone = $GLOBALS['ec_cart_data']->cart_data->shipping_phone;
			$this->user->setup_shipping_info_data( $shipping_first_name, $shipping_last_name, $shipping_address_line_1, $shipping_address_line_2, $shipping_city, $shipping_state, $shipping_country, $shipping_zip, $shipping_phone, $shipping_company );
			
		}
		
		if( isset( $GLOBALS['ec_cart_data']->cart_data->coupon_code ) && $GLOBALS['ec_cart_data']->cart_data->coupon_code != "" ){
			$this->coupon_code = $GLOBALS['ec_cart_data']->cart_data->coupon_code;
			$coupon_result = $GLOBALS['ec_coupons']->redeem_coupon_code( $this->coupon_code );
			if( $coupon_result ){
				$this->coupon = $coupon_result;
			}
		}else{
			$this->coupon_code = "";
		}
		
		if( isset( $GLOBALS['ec_cart_data']->cart_data->giftcard ) && $GLOBALS['ec_cart_data']->cart_data->giftcard != "" ){
			$this->gift_card = $GLOBALS['ec_cart_data']->cart_data->giftcard;
			$this->giftcard = $this->mysqli->redeem_gift_card( $this->gift_card );
			if( !$this->giftcard )
				$this->gift_card = "";
		}else{
			$this->gift_card = "";
		}
		
		// Create Promotion and apply free shipping if necessary.
		$promotion = new ec_promotion( );
		$promotion->apply_free_shipping( $this->cart );
		
		// Shipping
		$sales_tax_discount = new ec_discount( $this->cart, $this->cart->discountable_subtotal, 0.00, $this->coupon_code, "", 0 );
		$GLOBALS['wpeasycart_current_coupon_discount'] = $sales_tax_discount->coupon_discount;
		$this->shipping = new ec_shipping( $this->cart->shipping_subtotal, $this->cart->weight, $this->cart->shippable_total_items, 'RADIO', $GLOBALS['ec_user']->freeshipping, $this->cart->length, $this->cart->width, $this->cart->height, $this->cart->cart );
		$shipping_price = $this->shipping->get_shipping_price( $this->cart->get_handling_total( ) );
		// Tax (no VAT here)
		$sales_tax_discount = new ec_discount( $this->cart, $this->cart->discountable_subtotal, 0.00, $this->coupon_code, "", 0 );
		$this->tax = new ec_tax( $this->cart->subtotal, $this->cart->taxable_subtotal - $sales_tax_discount->coupon_discount, 0, $shipping_state, $shipping_country, $GLOBALS['ec_user']->taxfree, $shipping_price, $this->cart );
		// Duty (Based on Product Price) - already calculated in tax
		// Get Total Without VAT, used only breifly
		if( get_option( 'ec_option_no_vat_on_shipping' ) ){
			$total_without_vat_or_discount = $this->cart->vat_subtotal + $this->tax->tax_total + $this->tax->duty_total;
		}else{
			$total_without_vat_or_discount = $this->cart->vat_subtotal + $shipping_price + $this->tax->tax_total + $this->tax->duty_total;
		}
		//If a discount used, and no vatable subtotal, we need to set to 0
		if( $total_without_vat_or_discount < 0 )
			$total_without_vat_or_discount = 0;
		// Discount for Coupon
		$this->discount = new ec_discount( $this->cart, $this->cart->discountable_subtotal, $shipping_price, $this->coupon_code, $this->gift_card, $total_without_vat_or_discount );
		// Amount to Apply VAT on
		$promotion = new ec_promotion( );
		$vatable_subtotal = $total_without_vat_or_discount - $this->tax->tax_total - $this->discount->coupon_discount - $promotion->get_discount_total( $this->cart->subtotal );
		// If for some reason this is less than zero, we should correct
		if( $vatable_subtotal < 0 )
			$vatable_subtotal = 0;
		// Get Tax Again For VAT
		$this->tax = new ec_tax( $this->cart->subtotal, $this->cart->taxable_subtotal - $sales_tax_discount->coupon_discount, $vatable_subtotal, $shipping_state, $shipping_country, $GLOBALS['ec_user']->taxfree, $shipping_price, $this->cart );
		// Discount for Gift Card
		$grand_total = ( $this->cart->subtotal + $this->tax->tax_total + $shipping_price + $this->tax->duty_total );
		$this->discount = new ec_discount( $this->cart, $this->cart->discountable_subtotal, $shipping_price, $this->coupon_code, $this->gift_card, $grand_total );
		// Order Totals
		$this->order_totals = new ec_order_totals( $this->cart, $GLOBALS['ec_user'], $this->shipping, $this->tax, $this->discount );
		$GLOBALS['ec_order_grand_total' ] = $this->order_totals->grand_total;
		
		// Credit Card
		if( isset( $_POST['ec_expiration_month'] ) && isset( $_POST['ec_expiration_year'] ) ){
			$exp_month = $_POST['ec_expiration_month'];
			$exp_year = $_POST['ec_expiration_year'];
		
		}else if( isset( $_POST['ec_cc_expiration'] ) ){
			$exp_date = $_POST['ec_cc_expiration'];
			$exp_month = substr( $exp_date, 0, 2 );
			$exp_year = substr( $exp_date, 5 );
			if( strlen( $exp_year ) == 2 ){
				$exp_year = "20" . $exp_year;
			}
		}
		if( isset( $_POST['ec_cart_payment_type'] ) )
			$credit_card = new ec_credit_card( $_POST['ec_cart_payment_type'], stripslashes( $_POST['ec_card_holder_name'] ), $this->sanatize_card_number( $_POST['ec_card_number'] ), $exp_month, $exp_year, $_POST['ec_security_code'] );
		else if( isset( $_POST['ec_card_number'] ) )
			$credit_card = new ec_credit_card( $this->get_payment_type( $this->sanatize_card_number( $_POST['ec_card_number'] ) ), stripslashes( $_POST['ec_card_holder_name'] ),  $this->sanatize_card_number( $_POST['ec_card_number'] ), $exp_month, $exp_year, $_POST['ec_security_code'] );
		else
			$credit_card = new ec_credit_card( "", "", "", "", "", "" );
		
		// Payment
		if( isset( $_POST['ec_cart_payment_selection'] ) )
			$this->payment = new ec_payment( $credit_card, $_POST['ec_cart_payment_selection'] );
		else if( $is_affirm )
			$this->payment = new ec_payment( $credit_card, "affirm" );
		else
			$this->payment = new ec_payment( $credit_card, "" );
		
		// Order
		$this->order = new ec_order( $this->cart, $GLOBALS['ec_user'], $this->shipping, $this->tax, $this->discount, $this->order_totals, $this->payment );
		
		$store_page_id = get_option('ec_option_storepage');
		$cart_page_id = get_option('ec_option_cartpage');
		$account_page_id = get_option('ec_option_accountpage');
		
		if( function_exists( 'icl_object_id' ) ){
			$store_page_id = icl_object_id( $store_page_id, 'page', true, ICL_LANGUAGE_CODE );
			$cart_page_id = icl_object_id( $cart_page_id, 'page', true, ICL_LANGUAGE_CODE );
			$account_page_id = icl_object_id( $account_page_id, 'page', true, ICL_LANGUAGE_CODE );
		}
		
		$this->store_page = get_permalink( $store_page_id );
		$this->cart_page = get_permalink( $cart_page_id );
		$this->account_page = get_permalink( $account_page_id );
		
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$this->store_page = $https_class->makeUrlHttps( $this->store_page );
			$this->cart_page = $https_class->makeUrlHttps( $this->cart_page );
			$this->account_page = $https_class->makeUrlHttps( $this->account_page );
		
        }else if( get_option( 'ec_option_load_ssl' ) ){
            $this->store_page = str_replace( 'http://', 'https://', $this->store_page );
			$this->cart_page = str_replace( 'http://', 'https://', $this->cart_page );
			$this->account_page = str_replace( 'http://', 'https://', $this->account_page );
            
        }
		
		if( substr_count( $this->cart_page, '?' ) )					$this->permalink_divider = "&";
		else														$this->permalink_divider = "?";
		
		// Subscription Options
		$this->subscription_option1 = $this->subscription_option2 = $this->subscription_option3 = $this->subscription_option4 = $this->subscription_option5 = 0;
			
		if( ( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option1 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option1 != "" ) || 
			( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option2 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option2 != "" ) || 
			( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option3 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option3 != "" ) || 
			( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option4 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option4 != "" ) || 
			( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option5 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option5 != "" ) ){
			
			$optionitem_list = $GLOBALS['ec_options']->get_all_optionitems( );
			
			if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option1 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option1 != "" ){
				$this->subscription_option1 = $GLOBALS['ec_cart_data']->cart_data->subscription_option1;
			}
			
			if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option2 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option2 != "" ){
				$this->subscription_option2 = $GLOBALS['ec_cart_data']->cart_data->subscription_option2;
			}
			
			if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option3 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option3 != "" ){
				$this->subscription_option3 = $GLOBALS['ec_cart_data']->cart_data->subscription_option3;
			}
			
			if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option4 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option4 != "" ){
				$this->subscription_option4 = $GLOBALS['ec_cart_data']->cart_data->subscription_option4;
			}
			
			if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option5 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option5 != "" ){
				$this->subscription_option5 = $GLOBALS['ec_cart_data']->cart_data->subscription_option5;
			}
			
			foreach( $optionitem_list as $option_item ){
				if( $option_item->optionitem_id == $this->subscription_option1 ){
					$this->subscription_option1_name = $option_item->optionitem_name;
					$this->subscription_option1_label = $option_item->option_label;
				
				}
				
				if( $option_item->optionitem_id == $this->subscription_option2 ){
					$this->subscription_option2_name = $option_item->optionitem_name;
					$this->subscription_option2_label = $option_item->option_label;
				
				}
				
				if( $option_item->optionitem_id == $this->subscription_option3 ){
					$this->subscription_option3_name = $option_item->optionitem_name;
					$this->subscription_option3_label = $option_item->option_label;
				
				}
				
				if( $option_item->optionitem_id == $this->subscription_option4 ){
					$this->subscription_option4_name = $option_item->optionitem_name;
					$this->subscription_option4_label = $option_item->option_label;
				
				}
				
				if( $option_item->optionitem_id == $this->subscription_option5 ){
					$this->subscription_option5_name = $option_item->optionitem_name;
					$this->subscription_option5_label = $option_item->option_label;
				}
			}
			
		}
		
		// Subscription Advanced Options
		if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_advanced_option ) && $GLOBALS['ec_cart_data']->cart_data->subscription_advanced_option != "" )
			$this->subscription_advanced_options = maybe_unserialize( $GLOBALS['ec_cart_data']->cart_data->subscription_advanced_option );
		else
			$this->subscription_advanced_options = "";
		
		// Check for downloads in cart
		$this->has_downloads = false;
		foreach( $this->cart->cart as $cart_item ){
			if( $cart_item->is_download ){
				$this->has_downloads = true;
				break;
			}
		}
		
		add_filter( 'wp_easycart_shipping_price_display', array( $this, 'apply_promotions_to_shipping' ) );
		add_filter( 'wp_easycart_express_shipping_price_display', array( $this, 'apply_promotions_to_shipping' ) );
		
		$this->cart_page = apply_filters( 'wp_easycart_cart_page_url', $this->cart_page );
		$this->account_page = apply_filters( 'wp_easycart_account_page_url', $this->account_page );
		
	}
	
	public function maybe_add_free_shipping( $ret_string ){
		$count = 0;
		for( $i=0; $i<count( $this->cart->cart ); $i++ ){
			if( $this->cart->cart[$i]->is_shippable && !$this->cart->cart[$i]->exclude_shippable_calculation ){
				$count++;
			}
		}
		if( !$count ){
			$promotion = new ec_promotion( );
			$ret_string .= '<div class="ec_cart_shipping_method_row"><input type="radio" class="no_wrap" name="ec_cart_shipping_method" value="promo_free" onchange="ec_cart_shipping_method_change(\'promo_free\', 0 );"';
			if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == 'promo_free' )
				$ret_string .= ' checked="checked"';
			$ret_string .= '> ' . $promotion->get_free_shipping_promo_label( $this->cart ) . ' (' . $GLOBALS['currency']->get_currency_display( apply_filters( 'wp_easycart_shipping_price_display', 0, 'free' ) ) . ')</div>';
		}
		return $ret_string;
	}
	
	public function maybe_set_trigger_rate_free( $rate ){
		$count = 0;
		for( $i=0; $i<count( $this->cart->cart ); $i++ ){
			if( $this->cart->cart[$i]->is_shippable && !$this->cart->cart[$i]->exclude_shippable_calculation ){
				$count++;
			}
		}
		if( !$count ){
			$rate = 0;
		}
		return $rate;
	}
	
	public function apply_promotions_to_shipping( $rate ){
		$new_rate = $GLOBALS['ec_promotions']->apply_promotions_to_shipping( $this->order_totals->sub_total, $rate );
		return ( $new_rate >= 0 ) ? $new_rate : 0;
	}
	
	public function display_cart_success( $success_code = '' ){
		$success_notes = array(	"account_created" => $GLOBALS['language']->get_text( "ec_success", "cart_account_created" ) );
		
		if( isset( $_GET['ec_cart_success'] ) ){
			echo "<div class=\"ec_cart_success\"><div>" . $success_notes[ $_GET['ec_cart_success'] ] . "</div></div>";
		}else if( $success_code != '' ){
			echo "<div class=\"ec_cart_success\"><div>" . $success_notes[ $success_code ] . "</div></div>";
		}
	}
	
	public function display_cart_error( $error_code = '' ){
		$error_notes = array( "email_exists" => $GLOBALS['language']->get_text( "ec_errors", "email_exists_error" ),
							  "login_failed" => $GLOBALS['language']->get_text( "ec_errors", "login_failed" ),
							  "3dsecure_failed" => $GLOBALS['language']->get_text( "ec_errors", "3dsecure_failed" ),
							  "manualbill_failed" => $GLOBALS['language']->get_text( "ec_errors", "manualbill_failed" ),
							  "thirdparty_failed" => $GLOBALS['language']->get_text( "ec_errors", "thirdparty_failed" ),
							  "payment_failed" => $GLOBALS['language']->get_text( "ec_errors", "payment_failed" ),
							  "card_error" => $GLOBALS['language']->get_text( "ec_errors", "payment_failed" ),
							  "already_subscribed" => $GLOBALS['language']->get_text( "ec_errors", "already_subscribed" ),
							  "not_activated" => $GLOBALS['language']->get_text( "ec_errors", "not_activated" ),
							  "subscription_not_found" => $GLOBALS['language']->get_text( "ec_errors", "subscription_not_found" ),
							  "user_insert_error" => $GLOBALS['language']->get_text( "ec_errors", "user_insert_error" ),
							  "subscription_added_failed" => $GLOBALS['language']->get_text( "ec_errors", "subscription_added_failed" ),
							  "subscription_failed" => $GLOBALS['language']->get_text( "ec_errors", "subscription_failed" ),
							  "invalid_address" => $GLOBALS['language']->get_text( "ec_errors", "invalid_address" ),
							  "session_expired" => $GLOBALS['language']->get_text( "ec_errors", "session_expired" ),
							  "invalid_vat_number" => $GLOBALS['language']->get_text( "ec_errors", "invalid_vat_number" ),
							  "stock_invalid" => $GLOBALS['language']->get_text( "ec_errors", "cart_stock_invalid" )
							);
		if( isset( $_GET['ec_cart_error'] ) && $GLOBALS['ec_cart_data']->cart_data->card_error != '' ){
			echo "<div class=\"ec_cart_error\"><div>" . esc_attr( $GLOBALS['ec_cart_data']->cart_data->card_error ) . "</div></div>";
		}else if( isset( $_GET['ec_cart_error'] ) ){
			echo "<div class=\"ec_cart_error\"><div>" . $error_notes[ $_GET['ec_cart_error'] ] . "</div></div>";
		}else if( $error_code != '' ){
        	echo "<div class=\"ec_cart_error\"><div>" . $error_notes[ $error_code ] . "</div></div>";
		}
	}
    
    public function display_cart_success_page( $order_id, $success_code = false, $error_code = false ){
        if( $GLOBALS['ec_cart_data']->cart_data->is_guest ){
            $order_row = $this->mysqli->get_guest_order_row( $order_id, $GLOBALS['ec_cart_data']->cart_data->guest_key );
        }else{
            $order_row = $this->mysqli->get_order_row( $order_id, $GLOBALS['ec_cart_data']->cart_data->user_id );
        }
        
        if( !$order_row ){
            $this->display_cart_page( );
            return false;
        }
        
        do_action( 'wpeasycart_order_success' );
        
        $order = new ec_orderdisplay( $order_row, true );

        if( $GLOBALS['ec_cart_data']->cart_data->guest_key != "" ){
            $order_details = $this->mysqli->get_guest_order_details( $order_id, $GLOBALS['ec_cart_data']->cart_data->guest_key );

        }else{
            $order_details = $this->mysqli->get_order_details( $order_id, $GLOBALS['ec_cart_data']->cart_data->user_id );
        }

        $GLOBALS['ec_user']->setup_billing_info_data( $order->billing_first_name, $order->billing_last_name, $order->billing_address_line_1, $order->billing_address_line_2, $order->billing_city, $order->billing_state, $order->billing_country, $order->billing_zip, $order->billing_phone, $order->billing_company_name );

        $GLOBALS['ec_user']->setup_shipping_info_data( $order->shipping_first_name, $order->shipping_last_name, $order->shipping_address_line_1, $order->shipping_address_line_2, $order->shipping_city, $order->shipping_state, $order->shipping_country, $order->shipping_zip, $order->shipping_phone, $order->shipping_company_name );

        $tax_struct = $this->tax;

        $total = $GLOBALS['currency']->get_currency_display( $order->grand_total );
        $subtotal = $GLOBALS['currency']->get_currency_display( $order->sub_total );
        $tax = $GLOBALS['currency']->get_currency_display( $order->tax_total );
        $duty = $GLOBALS['currency']->get_currency_display( $order->duty_total );
        $vat = $GLOBALS['currency']->get_currency_display( $order->vat_total );
        if( ( $order->grand_total - $order->vat_total ) > 0 )
            $vat_rate = number_format( $this->tax->vat_rate, 0, '', '' );
        else
            $vat_rate = number_format( 0, 0, '', '' );
        $shipping = $GLOBALS['currency']->get_currency_display( $order->shipping_total );
        $discount = $GLOBALS['currency']->get_currency_display( $order->discount_total );

        if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_email_receipt/emaillogo.jpg" ) ){
            $email_logo_url = plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_email_receipt/emaillogo.jpg");
            $email_footer_url = plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_email_receipt/emailfooter.jpg");
        }else{
            $email_logo_url = plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_email_receipt/emaillogo.jpg");
            $email_footer_url = plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_email_receipt/emailfooter.jpg");
        }

        //google analytics
        $this->analytics = new ec_googleanalytics($order_details, $order->shipping_total, $order->tax_total , $order->grand_total, $order_id);
        $google_urchin_code = get_option('ec_option_googleanalyticsid');
        $google_wp_url = $_SERVER['SERVER_NAME'];
        $google_transaction = $this->analytics->get_transaction_js();
        $google_items = $this->analytics->get_item_js();
        //end google analytics
        $this->display_cart_error();

        //Backwards compatibility for an error... Don't want the button showing if user didn't create an account.
        if( $GLOBALS['ec_cart_data']->cart_data->is_guest != "" && $GLOBALS['ec_cart_data']->cart_data->is_guest )
            $GLOBALS['ec_cart_data']->cart_data->email = "guest";

        if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_success.php' ) )	
            include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_success.php' );
        else
            include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_success.php' );
    }
	
	public function display_cart_page(){
		
		if( get_option( 'ec_option_googleanalyticsid' ) != "UA-XXXXXXX-X" && get_option( 'ec_option_googleanalyticsid' ) != "" ){
			echo "<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			ga('create', '" . get_option( 'ec_option_googleanalyticsid' ) . "', 'auto');
			ga('send', 'pageview');
			ga('require', 'ec');
			function ec_google_removeFromCart( model_number, title, quantity, price ){
			  ga('ec:addProduct', {
				'id': model_number,
				'name': title,
				'price': price,
				'quantity': quantity
			  });
			  ga('ec:setAction', 'remove');
			  ga('send', 'event', 'UX', 'click', 'remove from cart');     // Send data using an event.
			}";
			
			// Setup Cart
			for( $i=0; $i < count( $this->cart->cart ); $i++ ){
				echo "
				ga( 'ec:addProduct', {
				  'id': '" . $this->cart->cart[$i]->model_number . "',
				  'name': '" . str_replace( "'", "\'", $this->cart->cart[$i]->title ) . "',
				  'price': '" . $this->cart->cart[$i]->unit_price . "',
				  'quantity': '" . $this->cart->cart[$i]->quantity . "'
				});";
			}
			
			// View of Cart
			if( !isset( $_GET['ec_page'] )  ){
				echo "
				ga('ec:setAction','checkout', {
					'step': 1,
					'option': 'Cart View'
				});
				ga('send', 'pageview');";
			
			// View of Checkout Info
			}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_info" ){
				echo "
				ga('ec:setAction','checkout', {
					'step': 2,
					'option': 'Checkout Info'
				});
				ga('send', 'pageview');";
			
			// View of Payment Method
			}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_payment" ){
				echo "
				ga('ec:setAction','checkout', {
					'step': 3,
					'option': 'Checkout Payment'
				});
				ga('send', 'pageview');";
			
			// View of thankyou page
			}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_success" ){
				echo "
				ga('ec:setAction','checkout', {
					'step': 4,
					'option': 'Checkout Success'
				});
				ga('send', 'pageview');";
			
			}
			
			echo "</script>";
		}
		
		echo "<div class=\"ec_cart_page\">";
		if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_success" ){
			do_action( 'wpeasycart_order_success' );
			$order_id = $_GET['order_id'];
			if( $GLOBALS['ec_cart_data']->cart_data->is_guest ){
				$order_row = $this->mysqli->get_guest_order_row( $order_id, $GLOBALS['ec_cart_data']->cart_data->guest_key );
			}else{
				$order_row = $this->mysqli->get_order_row( $order_id, $GLOBALS['ec_cart_data']->cart_data->user_id );
			}
			$order = new ec_orderdisplay( $order_row, true );
			
			if( $GLOBALS['ec_cart_data']->cart_data->guest_key != "" ){
				$order_details = $this->mysqli->get_guest_order_details( $order_id, $GLOBALS['ec_cart_data']->cart_data->guest_key );
				
			}else{
				$order_details = $this->mysqli->get_order_details( $order_id, $GLOBALS['ec_cart_data']->cart_data->user_id );
			}
			
			$GLOBALS['ec_user']->setup_billing_info_data( $order->billing_first_name, $order->billing_last_name, $order->billing_address_line_1, $order->billing_address_line_2, $order->billing_city, $order->billing_state, $order->billing_country, $order->billing_zip, $order->billing_phone, $order->billing_company_name );
			
			$GLOBALS['ec_user']->setup_shipping_info_data( $order->shipping_first_name, $order->shipping_last_name, $order->shipping_address_line_1, $order->shipping_address_line_2, $order->shipping_city, $order->shipping_state, $order->shipping_country, $order->shipping_zip, $order->shipping_phone, $order->shipping_company_name );
			
			$tax_struct = $this->tax;
			
			$total = $GLOBALS['currency']->get_currency_display( $order->grand_total );
			$subtotal = $GLOBALS['currency']->get_currency_display( $order->sub_total );
			$tax = $GLOBALS['currency']->get_currency_display( $order->tax_total );
			$duty = $GLOBALS['currency']->get_currency_display( $order->duty_total );
			$vat = $GLOBALS['currency']->get_currency_display( $order->vat_total );
			if( ( $order->grand_total - $order->vat_total ) > 0 )
				$vat_rate = number_format( $this->tax->vat_rate, 0, '', '' );
			else
				$vat_rate = number_format( 0, 0, '', '' );
			$shipping = $GLOBALS['currency']->get_currency_display( $order->shipping_total );
			$discount = $GLOBALS['currency']->get_currency_display( $order->discount_total );
			
			if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_email_receipt/emaillogo.jpg" ) ){
				$email_logo_url = plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_email_receipt/emaillogo.jpg");
				$email_footer_url = plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_email_receipt/emailfooter.jpg");
			}else{
				$email_logo_url = plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_email_receipt/emaillogo.jpg");
				$email_footer_url = plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_email_receipt/emailfooter.jpg");
			}
			
			//google analytics
			$this->analytics = new ec_googleanalytics($order_details, $order->shipping_total, $order->tax_total , $order->grand_total, $order_id);
			$google_urchin_code = get_option('ec_option_googleanalyticsid');
			$google_wp_url = $_SERVER['SERVER_NAME'];
			$google_transaction = $this->analytics->get_transaction_js();
			$google_items = $this->analytics->get_item_js();
			//end google analytics
			$this->display_cart_error();
			
			//Backwards compatibility for an error... Don't want the button showing if user didn't create an account.
			if( $GLOBALS['ec_cart_data']->cart_data->is_guest != "" && $GLOBALS['ec_cart_data']->cart_data->is_guest )
				$GLOBALS['ec_cart_data']->cart_data->email = "guest";
			
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_success.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_success.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_success.php' );
			
		}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "third_party" ){
			$order_id = $_GET['order_id'];
			
			if( $GLOBALS['ec_cart_data']->cart_data->is_guest != "" && $GLOBALS['ec_cart_data']->cart_data->is_guest ){
				$order = $this->mysqli->get_guest_order_row( $order_id, $GLOBALS['ec_cart_data']->cart_data->guest_key );
				$order_details = $this->mysqli->get_guest_order_details( $this->order_id, $GLOBALS['ec_cart_data']->cart_data->guest_key );
			}else{
				$order = $this->mysqli->get_order_row( $order_id, $GLOBALS['ec_cart_data']->cart_data->user_id );
				$order_details = $this->mysqli->get_order_details( $this->order_id, $GLOBALS['ec_cart_data']->cart_data->user_id );
			}
			
			//google analytics
			$this->analytics = new ec_googleanalytics($order_details, $order->shipping_total, $order->tax_total , $order->grand_total, $order_id);
			$google_urchin_code = get_option('ec_option_googleanalyticsid');
			$google_wp_url = $_SERVER['SERVER_NAME'];
			$google_transaction = $this->analytics->get_transaction_js();
			$google_items = $this->analytics->get_item_js();
			//end google analytics
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_third_party.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_third_party.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_third_party.php' );
			
		}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "subscription_info" ){
			
			$this->display_subscription_page( );
				
		}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "invoice" ){
            global $wpdb;
            $invoice = $invoice = $wpdb->get_row( $wpdb->prepare( "SELECT ec_order.* FROM ec_order, ec_orderstatus WHERE guest_key = %s AND ec_orderstatus.status_id = ec_order.orderstatus_id AND ec_orderstatus.is_approved = 0", $_GET['ec_guest_key'] ) );
            if( !$invoice ){
                
                
            }else{
                $invoice_items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ec_orderdetail WHERE order_id = %d", $invoice->order_id ) );
                if( !$invoice_items ){
                    
                    
                }else{
                    for( $i=0; $i<count( $invoice_items ); $i++ ){
                        if( $invoice_items[$i]->use_advanced_optionset ){
                            $invoice_items[$i]->advanced_options = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ec_order_option WHERE orderdetail_id = %d ORDER BY option_order ASC", $invoice_items[$i]->orderdetail_id ) );
                       }
                    }
                    if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_invoice.php' ) )	
                        include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_invoice.php' );
                    else
                        include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_invoice.php' );
                }
            }
            
        }else{
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_page.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_page.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_page.php' );
		}
		echo "</div>";
	}
    
    public function display_subscription_page( $subscription_product_id = false ){
        $this->display_cart_error( );
			
        $subscription_found = false;
        
        if( isset( $_GET['subscription'] ) || $subscription_product_id ){

            wpeasycart_session( )->handle_session( );

            global $wpdb;
            $model_number = $_GET['subscription'];
            if( $subscription_product_id ){
                $products = $this->mysqli->get_product_list( $wpdb->prepare( " WHERE product.product_id = %d", $subscription_product_id ), "", "", "" );
            }else{
                $products = $this->mysqli->get_product_list( $wpdb->prepare( " WHERE product.model_number = %s", $model_number ), "", "", "" );
            }
            if( count( $products ) > 0 ){
                $model_number = $products[0]['model_number'];
                $subscription_found = true;
                $product = new ec_product( $products[0], 0, 1, 0 );

                if( !get_option( 'ec_option_subscription_one_only' ) && $GLOBALS['ec_cart_data']->cart_data->subscription_quantity != "" ){ 
                    $subscription_quantity = $GLOBALS['ec_cart_data']->cart_data->subscription_quantity;
                }else{ 
                    $subscription_quantity = 1; 
                }

                wpeasycart_taxcloud( )->setup_subscription_for_tax( $product, $subscription_quantity );

                // Get option item price adjustments
                $option_promotion_multiplier = 1;
                $option_promotion_discount = 0;
                $promotions = $GLOBALS['ec_promotions']->promotions;
                for( $i=0; $i<count( $promotions ); $i++ ){
                    if( $product->promotion_text == $promotions[$i]->promotion_name ){
                        if( $promotions[$i]->price1 == 0 ){
                            $option_promotion_multiplier = ( 100 - $promotions[$i]->percentage1 ) / 100;
                        }else if( $promotions[$i]->price1 != 0 ){
                            $option_promotion_discount = $promotions[$i]->price1;
                        }
                    }
                }

                $option_total = 0;
                if( $this->subscription_option1 != 0 ){
                    $subscription_option1 = $GLOBALS['ec_options']->get_optionitem( $this->subscription_option1 );
                    if( $subscription_option1->optionitem_price > 0 ){
                        $option_total += $subscription_option1->optionitem_price;
                    }
                }
                if( $this->subscription_option2 != 0 ){
                    $subscription_option2 = $GLOBALS['ec_options']->get_optionitem( $this->subscription_option2 );
                    if( $subscription_option2->optionitem_price > 0 ){
                        $option_total += $subscription_option2->optionitem_price;
                    }
                }
                if( $this->subscription_option3 != 0 ){
                    $subscription_option3 = $GLOBALS['ec_options']->get_optionitem( $this->subscription_option3 );
                    if( $subscription_option3->optionitem_price > 0 ){
                        $option_total += $subscription_option3->optionitem_price;
                    }
                }
                if( $this->subscription_option4 != 0 ){
                    $subscription_option4 = $GLOBALS['ec_options']->get_optionitem( $this->subscription_option4 );
                    if( $subscription_option4->optionitem_price > 0 ){
                        $option_total += $subscription_option4->optionitem_price;
                    }
                }
                if( $this->subscription_option5 != 0 ){
                    $subscription_option5 = $GLOBALS['ec_options']->get_optionitem( $this->subscription_option5 );
                    if( $subscription_option5->optionitem_price > 0 ){
                        $option_total += $subscription_option5->optionitem_price;
                    }
                }

                // get discount amount
                $discount_amount = 0;
                if( isset( $this->coupon ) ){ // Invalid Coupon
                    if( $this->coupon->is_percentage_based ){
                        $discount_amount = ( $product->price + $option_total ) * $subscription_quantity * ( $this->coupon->promo_percentage / 100 );
                    }else if( $this->coupon->is_dollar_based ){
                        $discount_amount = $this->coupon->promo_dollar;
                    }
                    if( $discount_amount > ( $product->price + $option_total ) * $subscription_quantity )
                        $discount_amount = ( $product->price + $option_total ) * $subscription_quantity;
                    $discount_amount = round( $discount_amount, 2 );

                }else if( $option_promotion_multiplier < 1 ){
                    $discount_amount = round( ( $product->price + $option_total ) * $subscription_quantity * ( 1 - $option_promotion_multiplier ), 2 );

                }else if( $option_promotion_discount > 0 ){
                    $discount_amount = round( $option_promotion_discount, 2 );
                }

                $sub_total = ( ( $product->price + $product->subscription_signup_fee + $option_total ) * $subscription_quantity ) - $discount_amount;
                $tax_subtotal = ( $product->is_taxable ) ? $sub_total - ( $product->subscription_signup_fee * $subscription_quantity ) : 0;
                $vat_subtotal = ( $product->vat_rate > 0 ) ? $sub_total - ( $product->subscription_signup_fee * $subscription_quantity ) : 0;
                $ec_tax = new ec_tax( $sub_total, $tax_subtotal, $vat_subtotal, ( $GLOBALS['ec_cart_data']->cart_data->shipping_state ) ? $GLOBALS['ec_cart_data']->cart_data->shipping_state : $GLOBALS['ec_user']->shipping->state, ( $GLOBALS['ec_cart_data']->cart_data->shipping_country ) ? $GLOBALS['ec_cart_data']->cart_data->shipping_country : $GLOBALS['ec_user']->shipping->country, $GLOBALS['ec_user']->taxfree, 0, $this->cart );

                $tax_total = round( $ec_tax->tax_total, 2 );
                $vat_rate = $ec_tax->vat_rate;
                $vat_total = round( $ec_tax->vat_total, 2 );

                $hst_total = round( $ec_tax->hst, 2 );
                $pst_total = round( $ec_tax->pst, 2 );
                $gst_total = round( $ec_tax->gst, 2 );

                $hst_rate = $ec_tax->hst_rate;
                $pst_rate = $ec_tax->pst_rate;
                $gst_rate = $ec_tax->gst_rate;

                if( $product->trial_period_days > 0 ){
                   $grand_total = ( ( $product->subscription_signup_fee ) * $subscription_quantity );
                }else{
                    $grand_total = ( ( $product->price + $product->subscription_signup_fee + $option_total ) * $subscription_quantity ) - $discount_amount + $tax_total + $hst_total + $pst_total + $gst_total;
                }

                if( !$ec_tax->vat_included ){
                    $grand_total += $vat_total;
                }

                // Subscription
                if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_subscription.php' ) )	
                    include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_subscription.php' );
                else
                    include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_subscription.php' );
            }

        }

        if( !$subscription_found ){
            echo "No subscription was found to match the model number provided.";
        }
    }
	
	public function display_cart_process( ){
		if(	$this->cart->total_items > 0 || ( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_success" ) ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_process.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_process.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_process.php' );
		}
	}
	
	public function display_cart_process_cart_link( $link_text ){
		if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_success" ){
			echo $link_text;
		}else{
			echo "<a href=\"" . $this->cart_page . "\" class=\"ec_process_bar_link\">" . $link_text . "</a>";
		}
	}
	
	public function display_cart_process_shipping_link( $link_text ){
		if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_success" ){
			echo $link_text;
		}else{
			echo "<a href=\"" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info\" class=\"ec_process_bar_link\">" . $link_text . "</a>";
		}
	}
	
	public function display_cart_process_review_link( $link_text ){
		if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_success" ){
			echo $link_text;
		}else if( $GLOBALS['ec_cart_data']->cart_data->billing_first_name != "" ){
			echo "<a href=\"" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment\" class=\"ec_process_bar_link\">" . $link_text . "</a>";
		}else{
			echo $link_text;
		}
	}
    
    public function display_cart_dynamic( $cart_page, $success_code, $error_code ){
        $ec_db = new ec_db( );
        $cart_count = $ec_db->get_cart_count( $GLOBALS['ec_cart_data']->ec_cart_id );
        if( $cart_count == 0 && (int) substr( $cart_page, 0, 1 ) < 5 ){
            $this->display_cart_page( );
            
        }else if( $cart_count > 0 && (float) get_option( 'ec_option_minimum_order_total' ) > 0 && (float) get_option( 'ec_option_minimum_order_total' ) > $this->cart->subtotal ){
            $this->display_cart_page( );
            
        }else if( preg_match( '/4\-paypal\-(PAYID-[A-Z0-9]+)\-([A-Z0-9]+)/', $cart_page, $matches ) ){
            $pid = $matches[1];
            $pyid = $matches[2];
            $this->display_payment_paypal_express( $pid, $pyid );
            
        }else if( preg_match( '/4\-paypal\-([A-Z0-9]+)\-([A-Z0-9]+)/', $cart_page, $matches ) ){
            $oid = $matches[1];
            $pyid = $matches[2];
            $this->display_payment_paypal_express( false, $pyid, $oid );
            
        }else if( preg_match( '/4\-ideal\-([a-zA-Z0-9\_]+)\-([a-zA-Z0-9\_]+)/', $cart_page, $matches ) ){
            $ideal_source = $matches[1];
            $ideal_client_secret = $matches[2];
            $this->display_cart_top( 3, $success_code, $error_code );
            $this->display_payment( $ideal_source, $ideal_client_secret );
            
        }else if( $cart_page == 1 ){
            $this->display_cart_top( 1, $success_code, $error_code );
            $this->display_cart( '' );
            
        }else if( $cart_page == 2 ){
            $this->display_cart_top( 2, $success_code, $error_code );
            $this->display_checkout_details( );
        
        }else if( $cart_page == 3 ){
            $this->display_cart_top( 2, $success_code, $error_code );
            $this->display_shipping_method( );
        
        }else if( $cart_page == 4 ){
            $this->display_cart_top( 3, $success_code, $error_code );
            $this->display_payment( );
        
        }else if( preg_match( '/5\-sub\-([0-9]+)/', $cart_page, $matches ) ){
            $this->display_cart_error( $error_code );
			$this->display_subscription_page( $matches[1] );
        
        }else if( substr( $cart_page, 0, 1 ) == 6 ){
            $this->display_cart_success_page( substr( $cart_page, 2, strlen( $cart_page ) - 1  ), $success_code, $error_code );
        
        }
    }
    
    public function display_cart_top( $page_num, $success_code, $error_code ){
        if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_dynamic_top.php' ) )	
            include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_dynamic_top.php' );
        else
            include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_dynamic_top.php' );
    }
	
	public function display_cart( $empty_cart_string ){
		if(	$this->cart->total_items > 0 ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart.php' );
			
			echo "<input type=\"hidden\" name=\"ec_cart_page\" id=\"ec_cart_page\" value=\"" . $this->cart_page . "\" />";
			echo "<input type=\"hidden\" name=\"ec_cart_base_path\" id=\"ec_cart_base_path\" value=\"" . plugins_url( ) . "\" />";
		}else
			echo $empty_cart_string;
	}
	
	public function display_login(){
		if(	$this->cart->total_items > 0 ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_login.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_login.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_login.php' );
		}
	}
	
	public function display_login_complete(){
		if(	$this->cart->total_items > 0 ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_login_complete.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_login_complete.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_login_complete.php' );
		}
	}
	
	public function display_subscription_login_complete( ){
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_login_complete.php' ) )	
			include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_login_complete.php' );
		else if( file_exists( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_login_complete.php' ) )
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_login_complete.php' );
	}
	
	public function should_display_cart( ){
		// Check minimum order amount
		if( (float) get_option( 'ec_option_minimum_order_total' ) > 0 && (float) get_option( 'ec_option_minimum_order_total' ) > $this->cart->subtotal ){
			return true;
		}else if( apply_filters( 'wpeasycart_restrict_cart_only', false ) ){
			return true;
		}
		
		if( !$this->should_display_login( ) )
			return true;
		else
			return false;
	}
	
	public function should_display_login( ){
		return ( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_login" && ( $GLOBALS['ec_cart_data']->cart_data->email == "" || $GLOBALS['ec_cart_data']->cart_data->is_guest == "" || $GLOBALS['ec_cart_data']->cart_data->is_guest ) );
	}
	
	public function payment_processor_requires_billing( ){
		if( get_option( 'ec_option_payment_process_method' ) == "skrill" ){
			return false;	
		}
	}
	
	public function should_hide_shipping_panel(){
		return ( $GLOBALS['ec_cart_data']->cart_data->shipping_selector == "" || ( $GLOBALS['ec_cart_data']->cart_data->shipping_selector != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_selector == "false" ) );
	}
	
	public function should_display_page_one( ){
		// Check minimum order amount
		if( (float) get_option( 'ec_option_minimum_order_total' ) > 0 && (float) get_option( 'ec_option_minimum_order_total' ) > $this->cart->subtotal ){
			return false;
		}else if( apply_filters( 'wpeasycart_restrict_cart_only', false ) ){
			return false;
		}
		
		return ( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_info" );
	}
	
	public function display_page_one_form_start(){
		$next_page = "checkout_shipping";
		if( !get_option( 'ec_option_use_shipping' ) || $this->order_totals->shipping_total <= 0 )
			$next_page = "checkout_payment";
		
		echo "<form action=\"" . $this->cart_page . $this->permalink_divider . "ec_page=" . $next_page . "\" method=\"POST\" id=\"wpeasycart_checkout_details_form\">";
		echo "<input type=\"hidden\" name=\"ec_cart_form_action\" value=\"save_checkout_info\" />";
	}
	
	public function display_page_one_form_end(){
		echo "</form>";
	}
	
	public function should_display_page_two( ){
		// Check minimum order amount
		if( (float) get_option( 'ec_option_minimum_order_total' ) > 0 && (float) get_option( 'ec_option_minimum_order_total' ) > $this->cart->subtotal ){
			return false;
		}
		
		return ( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_shipping" && $GLOBALS['ec_cart_data']->cart_data->email != "" );
	}
	
	public function display_page_two_form_start(){
		echo "<form action=\"" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment\" method=\"post\" id=\"wpeasycart_payment_shipping_method_form\">";
		echo "<input type=\"hidden\" name=\"ec_cart_form_action\" value=\"save_checkout_shipping\" />";
	}
	
	public function display_page_two_form_end(){
		echo "</form>";
		if( get_option( 'ec_option_payment_process_method' ) == "eway" && get_option( 'ec_option_eway_use_rapid_pay' ) ){
			echo '<script src="https://secure.ewaypayments.com/scripts/eCrypt.min.js"></script>';
		}
	}
	
	public function should_display_page_three( ){
		// Check minimum order amount
		if( (float) get_option( 'ec_option_minimum_order_total' ) > 0 && (float) get_option( 'ec_option_minimum_order_total' ) > $this->cart->subtotal ){
			return false;
		}
		
		return ( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_payment" && $GLOBALS['ec_cart_data']->cart_data->email != "" );
	}
	
	public function display_page_three_form_start(){
		if( get_option( 'ec_option_payment_process_method' ) == "eway" && get_option( 'ec_option_eway_use_rapid_pay' ) ){
			echo "<form data-eway-encrypt-key=\"" . get_option( 'ec_option_eway_client_key' ) . "\" action=\"" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_submit_order\" method=\"post\" id=\"ec_submit_order_form\">";
		}else{
			echo "<form action=\"" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_submit_order\" method=\"post\" id=\"ec_submit_order_form\">";
		}
		echo "<input type=\"hidden\" name=\"ec_cart_form_action\" value=\"submit_order\" />";
	}
	
	public function display_page_three_form_end(){
		echo "</form>";
		if( get_option( 'ec_option_payment_process_method' ) == "eway" && get_option( 'ec_option_eway_use_rapid_pay' ) ){
			echo "<script src=\"https://secure.ewaypayments.com/scripts/eCrypt.min.js\"></script>";
		}
	}
	
	public function display_subscription_form_start( $model_number ){
		echo "<form action=\"" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_submit_order\" id=\"ec_submit_order_form\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"ec_cart_form_action\" value=\"insert_subscription\" />";
		echo "<input type=\"hidden\" name=\"ec_cart_model_number\" id=\"ec_cart_model_number\" value=\"" . $model_number . "\" />";
	}
	
	public function display_subscription_form_end(){
		echo "</form>";
	}
	
	/* START CART FUNCTIONS */
	public function is_cart_type_one( ){
		return ( !isset( $_GET['ec_page'] ) || ( isset( $_GET['ec_page'] ) && $GLOBALS['ec_cart_data']->cart_data->email == "" ) );
	}
	
	public function is_cart_type_two( ){
		return ( !isset( $_GET['ec_page'] ) || ( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_payment" ) || $GLOBALS['ec_cart_data']->cart_data->email == "" );
	}
	
	public function is_cart_type_three( ){
		return ( ( $this->shipping->shipping_method == "live" ) && $this->cart->weight > 0 && ( !isset( $_GET['ec_page'] ) || $GLOBALS['ec_cart_data']->cart_data->email == "" ) );
	}
	
	public function display_total_items( ){
		echo "<span id=\"ec_cart_total_items\">" . $this->cart->get_total_items() . "</span>";
	}
	
	public function display_cart_items( ){
		$this->cart->display_cart_items( $this->tax->vat_enabled, $this->tax->vat_country_match );	
	}
	
	public function has_cart_total_promotion( ){
		if( $this->cart->cart_total_promotion )
			return true;
		else
			return false;
	}
	
	public function display_cart_total_promotion( ){
		echo $this->cart->cart_total_promotion;
	}
	
	public function has_cart_shipping_promotion( ){
		if( $this->shipping->get_shipping_promotion_text( ) )
			return true;
		else
			return false;
	}
	
	public function display_cart_shipping_promotion( ){
		echo $this->shipping->get_shipping_promotion_text();
	}
	
	public function display_shipping_costs_input( $label, $button_text, $label2 = 'Country:', $select_label = 'Select One' ){
		
		if( get_option( 'ec_option_estimate_shipping_country' ) ){
			
			$countries = $GLOBALS['ec_countries']->countries;
			
			if( $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_country != "" )
				$selected_country = $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_country;
			else if( count( $countries ) == 1 )
				$selected_country = $countries[0]->iso2_cnt;
			else if( get_option( 'ec_option_default_country' ) )
				$selected_country = get_option( 'ec_option_default_country' );
			else
				$selected_country = "0";
			
			echo "<div class=\"ec_estimate_shipping_country\"><span>" . $label2 . "</span><select name=\"ec_cart_country\" id=\"ec_cart_country\" class=\"no_wrap\">";
			echo "<option value=\"0\"";
			if( $selected_country == "0" )
				echo " selected=\"selected\"";
			echo ">" . $select_label . "</option>";
			foreach( $countries as $country ){
				echo "<option value=\"" . $country->iso2_cnt . "\"";
				if( $country->iso2_cnt == $selected_country )
					echo " selected=\"selected\"";
				echo ">" . $country->name_cnt . "</option>";
			}
			echo "</select></div>";
		}else{
			echo "<input type=\"hidden\" name=\"ec_cart_country\" id=\"ec_cart_country\" value=\"0\" />";
		}
		echo "<div class=\"ec_estimate_shipping_zip\"><span>" . $label . "</span><input type=\"text\" name=\"ec_cart_zip_code\" id=\"ec_cart_zip_code\" value=\"";
		if( $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip != "" )
			echo $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip;
		echo "\" /><a href=\"#\" onclick=\"return ec_estimate_shipping_click();\">" . $button_text . "</a></div>";
	}
	
	public function display_estimate_shipping_country_select( ){
		
		$countries = $GLOBALS['ec_countries']->countries;
			
		if( $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_country != "" )
			$selected_country = $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_country;
		else if( count( $countries ) == 1 )
			$selected_country = $countries[0]->iso2_cnt;
		else if( get_option( 'ec_option_default_country' ) )
			$selected_country = get_option( 'ec_option_default_country' );
		else
			$selected_country = "0";
		
		echo "<select name=\"ec_estimate_country\" id=\"ec_estimate_country\" class=\"no_wrap\">";
		echo "<option value=\"0\""; if( $selected_country == "0" ){ echo " selected=\"selected\""; } echo ">" . $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_select_one' ) . "</option>";
		foreach( $countries as $country ){
			echo "<option value=\"" . $country->iso2_cnt . "\"";
			if( $country->iso2_cnt == $selected_country )
				echo " selected=\"selected\"";
			echo ">" . $country->name_cnt . "</option>";
		}
		echo "</select>";
	}
	
	public function display_shipping_costs_input_text( $label ){
		echo "<span>" . $label . "</span><input type=\"text\" name=\"ec_cart_zip_code\" id=\"ec_cart_zip_code\" value=\"";
		if( $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip != "" )
			echo $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip;
		echo "\" />";
	}
	
	public function display_shipping_costs_input_button( $button_text ){
		echo "<a href=\"#\" onclick=\"return ec_estimate_shipping_click();\">" . $button_text . "</a>";
	}
	
	public function display_estimate_shipping_loader( ){
		if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_page/loader.gif" ) )	
			echo "<div class=\"ec_estimate_shipping_loader\" id=\"ec_estimate_shipping_loader\"><img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_page/loader.gif" ) . "\" /></div>";	
		else
			echo "<div class=\"ec_estimate_shipping_loader\" id=\"ec_estimate_shipping_loader\"><img src=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_page/loader.gif" ) . "\" /></div>";
	}
	
	public function display_subtotal( ){
		echo "<span id=\"ec_cart_subtotal\">" . $GLOBALS['currency']->get_currency_display( $this->order_totals->get_converted_sub_total( ), false ) . "</span>";	
	}
	
	public function get_subtotal( ){
		return $GLOBALS['currency']->get_currency_display( $this->order_totals->get_converted_sub_total( ), false );
	}
	
	public function display_tax_total( ){
		echo "<span id=\"ec_cart_tax\">" . $GLOBALS['currency']->get_currency_display( $this->order_totals->tax_total ) . "</span>";	
	}
	
	public function get_tax_total( ){
		return $GLOBALS['currency']->get_currency_display( $this->order_totals->tax_total );	
	}
	
	public function has_duty( ){
		if ( $this->tax->duty_total > 0 )			return true;
		else										return false;	
	}
	
	public function display_duty_total( ){
		echo "<span id=\"ec_cart_duty\">" . $GLOBALS['currency']->get_currency_display( $this->order_totals->duty_total ) . "</span>";	
	}
	
	public function get_duty_total( ){
		return $GLOBALS['currency']->get_currency_display( $this->order_totals->duty_total );	
	}
	
	public function get_vat_total( ){
		return $this->tax->vat_total;
	}
	
	public function get_vat_total_formatted( ){
		return $GLOBALS['currency']->get_currency_display( $this->tax->vat_total );
	}
	
	public function get_vat_rate_formatted( ){
        $vat_rate = $this->tax->vat_rate;
        $vat_rate_formatted = $vat_rate;
        if( round( $vat_rate_formatted, 0 ) == $vat_rate ){
            $vat_rate_formatted = number_format( round( $vat_rate_formatted, 0 ), 0, '', '' );
        
        }else if( round( $vat_rate_formatted, 1 ) == $vat_rate ){
            $vat_rate_formatted = number_format( $vat_rate_formatted, 1, '.', '' );
        
        }else if( round( $vat_rate_formatted, 2 ) == $vat_rate ){
            $vat_rate_formatted = number_format( $vat_rate_formatted, 2, '.', '' );
        
        }else if( round( $vat_rate_formatted, 3 ) == $vat_rate ){
            $vat_rate_formatted = number_format( $vat_rate_formatted, 3, '.', '' );
        
        }
		return apply_filters( 'wpeasycart_format_vat_rate', '(' . $vat_rate_formatted . '%)', $vat_rate );
	}
	
	public function display_vat_total( ){
		echo "<span id=\"ec_cart_vat\">" . $GLOBALS['currency']->get_currency_display( $this->order_totals->vat_total ) . "</span>";	
	}
	
	public function display_shipping_total( ){
		echo "<span id=\"ec_cart_shipping\">" . $GLOBALS['currency']->get_currency_display( $this->order_totals->shipping_total ) . "</span>";
	}
	
	public function get_shipping_total( ){
		return $GLOBALS['currency']->get_currency_display( $this->order_totals->shipping_total );
	}
	
	public function display_discount_total( ){
		echo "<span id=\"ec_cart_discount\">" . $GLOBALS['currency']->get_currency_display( $this->order_totals->discount_total ) . "</span>";
	}
	
	public function get_discount_total( ){
		return $GLOBALS['currency']->get_currency_display( (-1) * $this->order_totals->discount_total );
	}
	
	public function get_gst_total( ){
		return $GLOBALS['currency']->get_currency_display( $this->order_totals->gst_total );	
	}
	
	public function get_pst_total( ){
		return $GLOBALS['currency']->get_currency_display( $this->order_totals->pst_total );	
	}
	
	public function get_hst_total( ){
		return $GLOBALS['currency']->get_currency_display( $this->order_totals->hst_total );	
	}
	
	public function get_tip_total( ){
		return $GLOBALS['currency']->get_currency_display( $this->order_totals->tip_total );	
	}
	
	public function display_grand_total( ){
		echo "<span id=\"ec_cart_grandtotal\">" . $GLOBALS['currency']->get_currency_display( $this->order_totals->get_converted_grand_total( ), false ) . "</span>"; 	
	}
	
	public function get_grand_total( ){
		return $GLOBALS['currency']->get_currency_display( $this->order_totals->get_converted_grand_total( ), false ); 	
	}
	
	public function display_continue_shopping_button( $button_text ){
		echo "<a href=\"" . $this->store_page;
		
		echo "\" class=\"ec_cart_continue_shopping_link\">" . $button_text . "</a>";
	}
	
	public function display_checkout_button( $button_text ){
		$checkout_page = "checkout_login";
		if( $GLOBALS['ec_cart_data']->cart_data->email != "" ){
			$checkout_page = "checkout_info";
			
		}else if( get_option( 'ec_option_skip_cart_login' ) ){
			$checkout_page = "checkout_info";
			$GLOBALS['ec_cart_data']->cart_data->email = "guest";
			$GLOBALS['ec_cart_data']->cart_data->username = "guest";
		}
		if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/admin_panel.php" ) )
			echo "<a href=\"" . $this->cart_page . $this->permalink_divider . "ec_page=" . $checkout_page . "\" class=\"ec_cart_checkout_link\">" . $button_text . "</a>";
		else
			echo "<a href=\"" . $this->cart_page . "\" class=\"ec_cart_checkout_link\">" . $button_text . "</a>";
	}
	/* END CART FUNCTIONS */

	// Forward the page to the cart page minus form submission with success note
	private function forward_cart_success(){
		
	}
	
	// Forward the page to the last product page, plus a failed note
	private function forward_product_failed(){
		
	}
	
	/* Login Form Functions */
	public function display_cart_login_form_start(){
		echo "<form action=\"". $this->cart_page . "\" method=\"post\">";	
	}
	
	public function display_cart_login_form_start_subscription( ){
		echo "<form action=\"". $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $_GET['subscription'] . "\" method=\"post\">";
	}
	
	public function display_cart_login_form_end(){
		if( isset( $_GET['subscription'] ) ){
			echo "<input type=\"hidden\" name=\"ec_cart_subscription\" value=\"" . $_GET['subscription'] . "\" />";
		}
		echo "<input type=\"hidden\" name=\"ec_cart_form_action\" value=\"login_user\" />";
		echo "</form>";
	}
	
	public function display_cart_login_form_guest_start(){
		echo "<form action=\"". $this->cart_page . "\" method=\"post\">";
	}
	
	public function display_cart_login_form_guest_end(){
		if( isset( $_GET['subscription'] ) ){
			echo "<input type=\"hidden\" name=\"ec_cart_subscription\" value=\"" . $_GET['subscription'] . "\" />";
		}
		echo "<input type=\"hidden\" name=\"ec_cart_form_action\" value=\"login_user\" />";
		echo "<input type=\"hidden\" name=\"ec_cart_login_email\" value=\"guest\" />";
		echo "<input type=\"hidden\" name=\"ec_cart_login_password\" value=\"guest\" />";
		echo "</form>";
	}
	
	public function display_cart_login_email_input(){
		echo "<input type=\"email\" id=\"ec_cart_login_email\" name=\"ec_cart_login_email\" class=\"ec_cart_login_input\" autocorrect=\"off\" autocapitalize=\"off\" />";
	}
	
	public function display_cart_login_password_input(){
		echo "<input type=\"password\" id=\"ec_cart_login_password\" name=\"ec_cart_login_password\" class=\"ec_cart_login_input\" />";
	}
	
	public function display_cart_login_login_button( $input ){
		echo "<input type=\"submit\" id=\"ec_cart_login_login_button\" name=\"ec_cart_login_login_button\" class=\"ec_cart_login_button\" value=\"" . $input . "\" />";
	}
	
	public function display_cart_login_forgot_password_link( $link_text ){
		echo "<a href=\"" . $this->account_page . $this->permalink_divider . "ec_page=forgot_password\" class=\"ec_cart_login_complete_logout_link\">" . $link_text . "</a>";
	}
	
	public function display_cart_login_guest_button( $input ){
		echo "<input type=\"submit\" id=\"ec_cart_login_guest_button\" name=\"ec_cart_login_guest_button\" class=\"ec_cart_login_button\" value=\"" . $input . "\" />";
	}
	
	public function display_cart_login_complete_user_name( $input ){
		echo "<input type=\"hidden\" id=\"ec_cart_login_guest_text\" value=\"" . $input . "\" /><span id=\"ec_cart_login_complete_username\">";
		if( $GLOBALS['ec_cart_data']->cart_data->username != "guest" )			echo $GLOBALS['ec_cart_data']->cart_data->username;
		else														echo $input;
		echo "</span>";
	}
	
	public function display_cart_login_complete_signout_link( $input ){
		if( isset( $_GET['subscription'] ) ){
			echo "<a href=\"" . $this->cart_page . $this->permalink_divider . "ec_cart_action=logout&subscription=" . $_GET['subscription'] . "\" class=\"ec_cart_login_complete_logout_link\">" . $input . "</a>";
		}else{
			echo "<a href=\"" . $this->cart_page . $this->permalink_divider . "ec_cart_action=logout\" class=\"ec_cart_login_complete_logout_link\">" . $input . "</a>";
		}
	}
	
	/* END LOGIN/LOGOUT FUNCTIONS */
	
	/* START BILLING FUNCTIONS */
	public function display_checkout_details( ){
		do_action( 'wp_easycart_display_checkout_details_pre' );
		if(	$this->cart->total_items > 0 && apply_filters( 'wp_easycart_allow_checkout_details', 1 ) ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_checkout_details.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_checkout_details.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_checkout_details.php' );
		}
        do_action( 'wp_easycart_display_checkout_details_post' );
	}
	
	public function display_billing(){
		if(	$this->cart->total_items > 0 ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_billing.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_billing.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_billing.php' );
		}
	}
	
	public function display_billing_input( $name ){
		
		if( $name == "country" ){
			
			if( get_option( 'ec_option_use_country_dropdown' ) || get_option( 'ec_option_payment_process_method' ) == 'square' || $GLOBALS['ec_setting']->get_shipping_method( ) == 'live' ){
				
				// DISPLAY COUNTRY DROP DOWN MENU
				$countries = $GLOBALS['ec_countries']->countries;
				if( $GLOBALS['ec_cart_data']->cart_data->billing_country != "" && $GLOBALS['ec_cart_data']->cart_data->billing_country != "0"  )
					$selected_country = $GLOBALS['ec_cart_data']->cart_data->billing_country;
				else if( $GLOBALS['ec_user']->billing->get_value( "country2" ) != 0 )
					$selected_country = $GLOBALS['ec_user']->billing->get_value( "country2" );
				else if( count( $countries ) == 1 )
					$selected_country = $countries[0]->iso2_cnt;
				else if( get_option( 'ec_option_default_country' ) )
					$selected_country = get_option( 'ec_option_default_country' );
				else
					$selected_country = $GLOBALS['ec_user']->billing->get_value( "country2" );
				
				echo "<select name=\"ec_cart_billing_country\" id=\"ec_cart_billing_country\" class=\"ec_cart_billing_input_text no_wrap\" onchange=\"wpeasycart_cart_billing_country_update( );\">";
				echo "<option value=\"0\">" . $GLOBALS['language']->get_text( "cart_billing_information", "cart_billing_information_select_country" ) . "</option>";
				foreach($countries as $country){
					echo "<option value=\"" . $country->iso2_cnt . "\"";
					if( $country->iso2_cnt == $selected_country )
						echo " selected=\"selected\"";
					echo ">" . $country->name_cnt . "</option>";
				}
				echo "</select>";
			}else{
				// DISPLAY COUNTRY TEXT INPUT
				if( $GLOBALS['ec_cart_data']->cart_data->billing_country && $GLOBALS['ec_cart_data']->cart_data->billing_country != "" && $GLOBALS['ec_cart_data']->cart_data->billing_country != "0"  )
					$selected_country = $GLOBALS['ec_cart_data']->cart_data->billing_country;
				else
					$selected_country = $GLOBALS['ec_user']->billing->get_value( "country" );
					
				echo "<input type=\"text\" name=\"ec_cart_billing_country\" id=\"ec_cart_billing_country\" class=\"ec_cart_billing_input_text\" value=\"" . htmlspecialchars( $selected_country, ENT_QUOTES ) . "\" />";
			}
		}else if( $name == "state" ){
			
			if( get_option( 'ec_option_use_smart_states' ) || get_option( 'ec_option_payment_process_method' ) == 'square' || $GLOBALS['ec_setting']->get_shipping_method( ) == 'live' ){
			
				// DISPLAY STATE DROP DOWN MENU
				$states = $this->mysqli->get_states( );
				if( $GLOBALS['ec_cart_data']->cart_data->billing_state != "" && $GLOBALS['ec_cart_data']->cart_data->billing_state != "" && $GLOBALS['ec_cart_data']->cart_data->billing_state != "0"  )
					$selected_state = $GLOBALS['ec_cart_data']->cart_data->billing_state;
				else
					$selected_state = $GLOBALS['ec_user']->billing->get_value( "state" );
					
				if( $GLOBALS['ec_cart_data']->cart_data->billing_country != "" && $GLOBALS['ec_cart_data']->cart_data->billing_country != "" && $GLOBALS['ec_cart_data']->cart_data->billing_country != "0"  )
					$selected_country = $GLOBALS['ec_cart_data']->cart_data->billing_country;
				else
					$selected_country = $GLOBALS['ec_user']->billing->get_value( "country2" );
				
				$current_country = "";
				$close_last_state = false;
				$state_found = false;
				$current_state_group = "";
				$close_last_state_group = false;
				
				foreach($states as $state){ if( $state->iso2_cnt ){
					if( $current_country != $state->iso2_cnt ){
						if( $close_last_state ){
							echo "</select>";
						}
						echo "<select name=\"ec_cart_billing_state_" . $state->iso2_cnt . "\" id=\"ec_cart_billing_state_" . $state->iso2_cnt . "\" class=\"ec_cart_billing_input_text ec_billing_state_dropdown no_wrap\"";
						if( $state->iso2_cnt != $selected_country ){
							echo " style=\"display:none;\"";
						}else{
							$state_found = true;
						}
						echo ">";
						
						if( $state->iso2_cnt == "CA" ){ // Canada
							echo "<option value=\"0\">" . $GLOBALS['language']->get_text( "cart_billing_information", "cart_billing_information_select_province" ) . "</option>";
						}else if( $state->iso2_cnt == "GB" ){ // United Kingdom
							echo "<option value=\"0\">" . $GLOBALS['language']->get_text( "cart_billing_information", "cart_billing_information_select_county" ) . "</option>";
						}else if( $state->iso2_cnt == "US" ){ //USA 
							echo "<option value=\"0\">" . $GLOBALS['language']->get_text( "cart_billing_information", "cart_billing_information_select_state" ) . "</option>";
						}else{
							echo "<option value=\"0\">" . $GLOBALS['language']->get_text( "cart_billing_information", "cart_billing_information_select_other" ) . "</option>";
						}
						
						$current_country = $state->iso2_cnt;
						$close_last_state = true;
					}
					
					if( $current_state_group != $state->group_sta && $state->group_sta != "" ){
						if( $close_last_state_group ){
							echo "</optgroup>";
						}
						echo "<optgroup label=\"" . $state->group_sta . "\">";
						$current_state_group = $state->group_sta;
						$close_last_state_group = true;
					}
					
					echo "<option value=\"" . $state->code_sta . "\"";
					if( $state->code_sta == $selected_state )
						echo " selected=\"selected\"";
					echo ">" . $state->name_sta . "</option>";
				} }
				
				if( $close_last_state_group ){
					echo "</optgroup>";
				}
				
				echo "</select>";
				
				// DISPLAY STATE TEXT INPUT	
				echo "<input type=\"text\" name=\"ec_cart_billing_state\" id=\"ec_cart_billing_state\" class=\"ec_cart_billing_input_text\" value=\"" . htmlspecialchars( $selected_state, ENT_QUOTES ) . "\"";
				if( $state_found ){
					echo " style=\"display:none;\"";
				}
				echo " />";
				
			}else{
				// Use the basic method of old
				if( get_option( 'ec_option_use_state_dropdown' ) ){
					// DISPLAY STATE DROP DOWN MENU
					$states = $this->mysqli->get_states( );
					if( $GLOBALS['ec_cart_data']->cart_data->billing_state != "" && $GLOBALS['ec_cart_data']->cart_data->billing_state != "" && $GLOBALS['ec_cart_data']->cart_data->billing_state != "0"  )
						$selected_state = $GLOBALS['ec_cart_data']->cart_data->billing_state;
					else
						$selected_state = $GLOBALS['ec_user']->billing->get_value( "state" );
					
					echo "<select name=\"ec_cart_billing_state\" id=\"ec_cart_billing_state\" class=\"ec_cart_billing_input_text no_wrap\">";
					echo "<option value=\"0\">" . $GLOBALS['language']->get_text( "cart_billing_information", "cart_billing_information_select_state" ) . "</option>";
					foreach($states as $state){
						echo "<option value=\"" . $state->code_sta . "\"";
						if( $state->code_sta == $selected_state )
							echo " selected=\"selected\"";
						echo ">" . $state->name_sta . "</option>";
					}
					echo "</select>";
				}else{
					// DISPLAY STATE TEXT INPUT
					if( $GLOBALS['ec_cart_data']->cart_data->billing_state != "" && $GLOBALS['ec_cart_data']->cart_data->billing_state != "" && $GLOBALS['ec_cart_data']->cart_data->billing_state != "0"  )
						$selected_state = $GLOBALS['ec_cart_data']->cart_data->billing_state;
					else
						$selected_state = $GLOBALS['ec_user']->billing->get_value( "state" );
						
					echo "<input type=\"text\" name=\"ec_cart_billing_state\" id=\"ec_cart_billing_state\" class=\"ec_cart_billing_input_text\" value=\"" . htmlspecialchars( $selected_state, ENT_QUOTES ) . "\" />";
				}
			}// Close if/else for state display type
			
		}else{
		
			$value = $GLOBALS['ec_user']->billing->get_value( $name );
			
			echo "<input type=\"text\" name=\"ec_cart_billing_" . $name . "\" id=\"ec_cart_billing_" . $name . "\" class=\"ec_cart_billing_input_text\" value=\"" . htmlspecialchars( $value, ENT_QUOTES ) . "\" />";
			
		}
	}
	
	public function display_vat_registration_number_input( ){
		$name = "vat_registration_number";
		if( $GLOBALS['ec_cart_data']->cart_data->vat_registration_number  )
            $value = $GLOBALS['ec_cart_data']->cart_data->vat_registration_number;
        else
            $value = $GLOBALS['ec_user']->vat_registration_number;
		echo "<input type=\"text\" name=\"ec_cart_billing_" . $name . "\" id=\"ec_cart_billing_" . $name . "\" class=\"ec_cart_billing_input_text\" value=\"" . htmlspecialchars( $value, ENT_QUOTES ) . "\" />";
	}
	/* END BILLING FUNCTIONS */
	
	/* START SHIPPING FUNCTIONS */
	public function display_shipping(){
		if(	$this->cart->total_items > 0 ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_shipping.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_shipping.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_shipping.php' );
		}
	}
	
	public function display_shipping_selector( $first_opt, $second_opt ){
		if( $this->cart->shipping_subtotal > 0 )
			echo "<div class=\"ec_cart_shipping_selector_row\">";
		else
			echo "<div class=\"ec_cart_shipping_selector_row_hidden\">";
			
		echo "<input type=\"radio\" name=\"ec_shipping_selector\" id=\"ec_cart_use_billing_for_shipping\" value=\"false\"";
		if( $GLOBALS['ec_cart_data']->cart_data->shipping_selector == "" || ( $GLOBALS['ec_cart_data']->cart_data->shipping_selector != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_selector == "false" ) )
		echo " checked=\"checked\"";
		echo " onchange=\"ec_cart_use_billing_for_shipping_change(); return false;\" />" . $first_opt;
		echo "</div>";
		
		if( get_option( 'ec_option_use_shipping' ) ){
			if( $this->cart->shipping_subtotal > 0 )
				echo "<div class=\"ec_cart_shipping_selector_row\">";
			else
				echo "<div class=\"ec_cart_shipping_selector_row_hidden\">";
			
			echo "<input type=\"radio\" name=\"ec_shipping_selector\" id=\"ec_cart_use_shipping_for_shipping\" value=\"true\"";
			if( $GLOBALS['ec_cart_data']->cart_data->shipping_selector != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_selector == "true" )
			echo " checked=\"checked\"";
			echo " onchange=\"ec_cart_use_shipping_for_shipping_change(); return false;\" />" . $second_opt;
			echo "</div>";
		}else{
			echo "<script>jQuery('.ec_cart_shipping_selector_row').hide();</script>";	
		}
	}
	
	public function display_shipping_input( $name ){
		
		if( $name == "country" ){
			
			if( get_option( 'ec_option_use_country_dropdown' ) || get_option( 'ec_option_payment_process_method' ) == 'square' || $GLOBALS['ec_setting']->get_shipping_method( ) == 'live' ){
				
				// DISPLAY COUNTRY DROP DOWN MENU
				$countries = $GLOBALS['ec_countries']->countries;
				if( $GLOBALS['ec_cart_data']->cart_data->shipping_country != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_country != "0" )
					$selected_country = $GLOBALS['ec_cart_data']->cart_data->shipping_country;
				else if( $GLOBALS['ec_user']->shipping->get_value( "country2" ) != 0 )
					$selected_country = $GLOBALS['ec_user']->shipping->get_value( "country2" );
				else if( count( $countries ) == 1 )
					$selected_country = $countries[0]->iso2_cnt;
				else if( get_option( 'ec_option_default_country' ) )
					$selected_country = get_option( 'ec_option_default_country' );
				else
					$selected_country = $GLOBALS['ec_user']->shipping->get_value( "country2" );
				
				echo "<select name=\"ec_cart_shipping_country\" id=\"ec_cart_shipping_country\" class=\"ec_cart_shipping_input_text no_wrap\">";
				echo "<option value=\"0\">" . $GLOBALS['language']->get_text( "cart_shipping_information", "cart_shipping_information_select_country" ) . "</option>";
				foreach($countries as $country){
					echo "<option value=\"" . $country->iso2_cnt . "\"";
					if( $country->iso2_cnt == $selected_country )
					echo " selected=\"selected\"";
					echo ">" . $country->name_cnt . "</option>";	
				}
				echo "</select>";
			}else{
				// DISPLAY STATE TEXT INPUT
				if( $GLOBALS['ec_cart_data']->cart_data->shipping_country != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_country != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_country != 0 )
					$selected_country = $GLOBALS['ec_cart_data']->cart_data->shipping_country;
				else
					$selected_country = $GLOBALS['ec_user']->shipping->get_value( "country" );
					
				echo "<input type=\"text\" name=\"ec_cart_shipping_country\" id=\"ec_cart_shipping_country\" class=\"ec_cart_shipping_input_text\" value=\"" . htmlspecialchars( $selected_country, ENT_QUOTES ) . "\" />";
			}
		}else if( $name == "state" ){
			
			if( get_option( 'ec_option_use_smart_states' ) || get_option( 'ec_option_payment_process_method' ) == 'square' || $GLOBALS['ec_setting']->get_shipping_method( ) == 'live' ){ // Use new method
				// DISPLAY STATE DROP DOWN MENU
				$states = $this->mysqli->get_states( );
				if( $GLOBALS['ec_cart_data']->cart_data->shipping_state != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_state != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_state != 0 )
					$selected_state = $GLOBALS['ec_cart_data']->cart_data->shipping_state;
				else
					$selected_state = $GLOBALS['ec_user']->shipping->get_value( "state" );
					
				if( $GLOBALS['ec_cart_data']->cart_data->shipping_country != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_country != 0 )
					$selected_country = $GLOBALS['ec_cart_data']->cart_data->shipping_country;
				else
					$selected_country = $GLOBALS['ec_user']->shipping->get_value( "country2" );
				
				$current_country = "";
				$close_last_state = false;
				$state_found = false;
				$current_state_group = "";
				$close_last_state_group = false;
				
				foreach($states as $state){
					if( $current_country != $state->iso2_cnt ){
						if( $close_last_state ){
							echo "</select>";
						}
						echo "<select name=\"ec_cart_shipping_state_" . $state->iso2_cnt . "\" id=\"ec_cart_shipping_state_" . $state->iso2_cnt . "\" class=\"ec_cart_shipping_input_text ec_shipping_state_dropdown no_wrap\"";
						if( $state->iso2_cnt != $selected_country ){
							echo " style=\"display:none;\"";
						}else{
							$state_found = true;
						}
						echo ">";
						
						if( $state->iso2_cnt == "CA" ){ // Canada
							echo "<option value=\"0\">" . $GLOBALS['language']->get_text( "cart_shipping_information", "cart_shipping_information_select_province" ) . "</option>";
						}else if( $state->iso2_cnt == "GB" ){ // United Kingdom
							echo "<option value=\"0\">" . $GLOBALS['language']->get_text( "cart_shipping_information", "cart_shipping_information_select_county" ) . "</option>";
						}else if( $state->iso2_cnt == "US" ){ //USA 
							echo "<option value=\"0\">" . $GLOBALS['language']->get_text( "cart_shipping_information", "cart_shipping_information_select_state" ) . "</option>";
						}else{
							echo "<option value=\"0\">" . $GLOBALS['language']->get_text( "cart_shipping_information", "cart_shipping_information_select_other" ) . "</option>";
						}
						
						$current_country = $state->iso2_cnt;
						$close_last_state = true;
					}
					
					if( $current_state_group != $state->group_sta && $state->group_sta != "" ){
						if( $close_last_state_group ){
							echo "</optgroup>";
						}
						echo "<optgroup label=\"" . $state->group_sta . "\">";
						$current_state_group = $state->group_sta;
						$close_last_state_group = true;
					}
					
					echo "<option value=\"" . $state->code_sta . "\"";
					if( $state->code_sta == $selected_state )
						echo " selected=\"selected\"";
					echo ">" . $state->name_sta . "</option>";
				}
				
				if( $close_last_state_group ){
					echo "</optgroup>";
				}
				
				echo "</select>";
				
				// DISPLAY STATE TEXT INPUT	
				echo "<input type=\"text\" name=\"ec_cart_shipping_state\" id=\"ec_cart_shipping_state\" class=\"ec_cart_shipping_input_text\" value=\"" . htmlspecialchars( $selected_state, ENT_QUOTES ) . "\"";
				if( $state_found ){
					echo " style=\"display:none;\"";
				}
				echo " />";
				
			}else{// Use old method
				
				if( get_option( 'ec_option_use_state_dropdown' ) ){
					// DISPLAY STATE DROP DOWN MENU
					$states = $this->mysqli->get_states( );
					if( $GLOBALS['ec_cart_data']->cart_data->shipping_state != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_state != 0 )
						$selected_state = $GLOBALS['ec_cart_data']->cart_data->shipping_state;
					else
						$selected_state = $GLOBALS['ec_user']->shipping->get_value( "state" );
					
					echo "<select name=\"ec_cart_shipping_state\" id=\"ec_cart_shipping_state\" class=\"ec_cart_shipping_input_text no_wrap\">";
					echo "<option value=\"0\">" . $GLOBALS['language']->get_text( "cart_shipping_information", "cart_shipping_information_select_state" ) . "</option>";
					foreach($states as $state){
						echo "<option value=\"" . $state->code_sta . "\"";
						if( $state->code_sta == $selected_state )
						echo " selected=\"selected\"";
						echo ">" . $state->name_sta . "</option>";
					}
					echo "</select>";
				}else{
					// DISPLAY STATE TEXT INPUT
					if( $GLOBALS['ec_cart_data']->cart_data->shipping_state != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_state != 0 )
						$selected_state = $GLOBALS['ec_cart_data']->cart_data->shipping_state;
					else
						$selected_state = $GLOBALS['ec_user']->shipping->get_value( "state" );
						
					echo "<input type=\"text\" name=\"ec_cart_shipping_state\" id=\"ec_cart_shipping_state\" class=\"ec_cart_shipping_input_text\" value=\"" . htmlspecialchars( $selected_state, ENT_QUOTES ) . "\" />";
				}
				
			}// Close if/else for state display type
			
		}else{
			$value = $GLOBALS['ec_user']->shipping->get_value( $name );
			
			echo "<input type=\"text\" name=\"ec_cart_shipping_" . $name . "\" id=\"ec_cart_shipping_" . $name . "\" class=\"ec_cart_shipping_input_text\" value=\"" . htmlspecialchars( $value, ENT_QUOTES ) . "\" />";
		}
	}
	/* END SHIPPING FUNCTIONS */
	
	/* START SHIPPING METHOD FUNCTIONS */
	public function display_shipping_method( ){
		do_action( 'wp_easycart_display_shipping_method_pre' );
		if(	$this->cart->total_items > 0 && apply_filters( 'wp_easycart_allow_shipping_method', 1 ) ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_shipping_method.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_shipping_method.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_shipping_method.php' );
		}
        do_action( 'wp_easycart_display_shipping_method_post' );
	}
	
	public function ec_cart_display_shipping_methods( $standard_text, $express_text, $ship_method ){
		$shipping_options = $this->shipping->get_shipping_options( $standard_text, $express_text );	
		if( $shipping_options )
			echo $shipping_options;
		else if( $GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip != "" )
			echo "<div class=\"ec_cart_shipping_method_row\">" . $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_error' ) . "</div>";
	}
	
	public function ec_cart_display_shipping_methods_stripe_dynamic( $standard_text, $express_text ){
		return $this->shipping->get_shipping_rate_data( $standard_text, $express_text );	
	}
	
	public function ec_cart_display_shipping_methods_square_dynamic( $standard_text, $express_text ){
		return $this->shipping->get_shipping_rate_data( $standard_text, $express_text, 1 );	
	}
	
	public function get_stripe_intent_client_secret( $order_totals = false ){
		if( get_option( 'ec_option_payment_process_method' ) == 'stripe' || get_option( 'ec_option_payment_process_method' ) == 'stripe_connect' ){
            if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
                $stripe = new ec_stripe( );
            else
                $stripe = new ec_stripe_connect( );

            if( !$order_totals ){
                $order_totals = $this->order_totals;
            }

            if( $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id == '' || $GLOBALS['ec_cart_data']->cart_data->stripe_pi_client_secret == '' ){
                $response = $stripe->create_payment_intent( $order_totals );
                if( $response ){
                    $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id = $response->id;
                    $GLOBALS['ec_cart_data']->cart_data->stripe_pi_client_secret = $response->client_secret;
                    $GLOBALS['ec_cart_data']->save_session_to_db( );
                }
                do_action( 'wpeasycart_cart_updated' );
            }else{
                $response = $stripe->get_payment_intent( $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id );
                if( !$response || $response->status != 'requires_payment_method' ){
                    $response = $stripe->create_payment_intent( $order_totals );
                    if( $response ){
                        $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id = $response->id;
                        $GLOBALS['ec_cart_data']->cart_data->stripe_pi_client_secret = $response->client_secret;
                        $GLOBALS['ec_cart_data']->save_session_to_db( );
                    }
                    do_action( 'wpeasycart_cart_updated' );
                }else{
                    return $GLOBALS['ec_cart_data']->cart_data->stripe_pi_client_secret;
                }
            }

            if( $response ){
              return $response->client_secret;
            }
        }
        
        return '';
	}
	
	public function get_stripe_intent_client_secret_subscription( $grand_total ){
		if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
			$stripe = new ec_stripe( );
		else
			$stripe = new ec_stripe_connect( );
		
		$order_totals = (object) array( 'grand_total' => $grand_total );
		
		if( $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id == '' ){
			$response = $stripe->create_payment_intent( $order_totals );
			$GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id = $response->id;
			$GLOBALS['ec_cart_data']->save_session_to_db( );
			do_action( 'wpeasycart_cart_updated' );
		}else{
			$response = $stripe->get_payment_intent( $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id );
			if( !$response || $response->status != 'requires_payment_method' ){
				$response = $stripe->create_payment_intent( $order_totals );
				$GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id = $response->id;
				$GLOBALS['ec_cart_data']->save_session_to_db( );
				do_action( 'wpeasycart_cart_updated' );
			}
		}
			
		return $response->client_secret;
	}
    
    public function print_square_payment_button( $is_payment = false ){
        
        if( get_option( 'ec_option_square_digital_wallet' ) && apply_filters( 'wp_easycart_allow_paypal_express', false ) && (int) ( $this->order_totals->get_converted_grand_total( ) * 100 ) >= 50 && !get_option( 'ec_option_square_is_sandbox' ) ){
            
            $location_id = ( get_option( 'ec_option_square_is_sandbox' ) ) ? get_option( 'ec_option_square_sandbox_location_id' ) : get_option( 'ec_option_square_location_id' );
            if( !$location_id ){
                $square = new ec_square( );
                $location_id = $square->get_location_id( );
            }
            
            if( $is_payment ){
				echo '<div class="ec_cart_option_row" id="ec_apple_pay_row">
					<input type="radio" class="no_wrap" name="ec_cart_payment_selection" id="ec_payment_apple" value="apple_pay"';
					if( $this->get_selected_payment_method( ) == "apple_pay" ){
						echo ' checked="checked"';
					}
					echo 'onChange="ec_update_payment_display( );" /> ' . $GLOBALS['language']->get_text( 'cart_payment_information', 'cart_payment_information_apple_pay' ) . '
				</div>
				<div id="ec_apple_pay_form"';
				if( $this->get_selected_payment_method( ) == "apple_pay" ){
					echo ' style="display:block;"';
				}else{
					echo ' style="display:none;"';
				}
				echo '><div class="ec_cart_box_section">';
			}
            
            echo '<div id="sq-walletbox"' . ( ( (float) get_option( 'ec_option_minimum_order_total' ) > 0 && (float) get_option( 'ec_option_minimum_order_total' ) > $this->cart->subtotal ) ? ' style="display:none"' : '' ) . '>
                <button id="sq-apple-pay" type="button"></button>
                <button id="sq-masterpass" type="button"></button>
                <button id="sq-google-pay" class="button-google-pay" type="button"></button>
            </div>';
            
            if( $is_payment ){
				echo '</div></div>';
			}
            
			echo '<div id="square-success-cover" style="display:none; cursor:default; position:fixed; top:0; left:0; width:100%; height:100%; z-index:999999; background-color: rgba(0, 0, 0, 0.8); color:#FFF;">
				<style>
				@keyframes rotation{
					0%  { transform:rotate(0deg); }
					100%{ transform:rotate(359deg); }
				}
				</style>
				<div style=\'font-family: "HelveticaNeue", "HelveticaNeue-Light", "Helvetica Neue Light", helvetica, arial, sans-serif; font-size: 14px; text-align: center; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; -ms-box-sizing: border-box; box-sizing: border-box; width: 350px; top: 50%; left: 50%; position: absolute; margin-left: -165px; margin-top: -80px; cursor: pointer; text-align: center;\'>
					<div class="square-checkout-loader">
						<div style="height: 30px; width: 30px; display: inline-block; box-sizing: content-box; opacity: 1; filter: alpha(opacity=100); -webkit-animation: rotation .7s infinite linear; -moz-animation: rotation .7s infinite linear; -o-animation: rotation .7s infinite linear; animation: rotation .7s infinite linear; border-left: 8px solid rgba(0, 0, 0, .2); border-right: 8px solid rgba(0, 0, 0, .2); border-bottom: 8px solid rgba(0, 0, 0, .2); border-top: 8px solid #fff; border-radius: 100%;"></div>
					</div>
				</div>
			</div>';
            echo '<script>
            jQuery( document.getElementById( \'square-success-cover\' ) ).appendTo( document.body );
            var applicationId = "' . ( ( get_option( 'ec_option_square_is_sandbox' ) ) ? 'sandbox-sq0idb-khAAob2bNi889KPQSVsF6Q' : 'sq0idp-H8Mnz1zzbv1mOyeWyKpF6Q' ) . '";
            var locationId = "' . $location_id . '";
            var minimum_require_met = true;
            if( jQuery( \'.ec_minimum_purchase_box\' ).length && jQuery( \'.ec_minimum_purchase_box\' ).is( \':visible\' ) ){
                minimum_require_met = false;
            }
            var paymentForm = new SqPaymentForm({
                applicationId: applicationId,
                locationId: locationId,
                inputClass: \'sq-input\',
                autoBuild: ' . ( ( get_option( 'ec_option_cache_prevent' ) ) ? 'false' : 'true' ) . ',
                applePay: {
                    elementId: \'sq-apple-pay\'
                },
                masterpass: {
                    elementId: \'sq-masterpass\'
                },
                googlePay: {
                    elementId: \'sq-google-pay\'
                },
                callbacks: {
                    methodsSupported: function (methods) {
                        var walletBox = document.getElementById( \'sq-walletbox\' );
                        var applePayBtn = document.getElementById( \'sq-apple-pay\' );
                        var googlePayBtn = document.getElementById( \'sq-google-pay\' );
                        var masterpassBtn = document.getElementById( \'sq-masterpass\' );
                        if( methods.applePay === true ){
                            if( minimum_require_met ){
                                walletBox.style.display = \'block\';
                            }
                            applePayBtn.style.display = \'block\';
                        }';
            if( $is_payment ){
            echo '
                        if( methods.masterpass === true ){
                            if( minimum_require_met ){
                                walletBox.style.display = \'block\';
                            }
                            masterpassBtn.style.display = \'block\';
                        }';
            }
            echo '
                        if( methods.googlePay === true ){
                            if( minimum_require_met ){
                                walletBox.style.display = \'block\';
                            }
                            googlePayBtn.style.display = \'inline-block\';
                        }
                    },
                    createPaymentRequest: function () {
                        return {';
            if( !$is_payment ){
            echo '
                            requestShippingAddress: true,
                            requestBillingInfo: true,';
            }
            echo '
                            currencyCode: "' . get_option( 'ec_option_square_currency' ) . '",
                            countryCode: "' . get_option( 'ec_option_square_location_country' ) . '",';
        
            if( !$is_payment ){
            echo '
                            shippingOptions: ' . json_encode( $this->ec_cart_display_shipping_methods_square_dynamic( $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_standard' ),$GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_express' ) ) ) . ',';
            }
            
            echo '
                            total: {
                                label: "' . get_option( 'ec_option_square_merchant_name' ) . '",
                                amount: "' . $this->order_totals->get_converted_grand_total( ) . '",
                                pending: false
                            },
                            lineItems: ' . json_encode( $this->get_dynamic_square_line_items( ) ) . '
                        }
                    },';
            if( !$is_payment ){
            echo '
                    shippingContactChanged: function( contact, done ){
                        var data = {
                            action: \'ec_ajax_update_square_shipping_address_dynamic\',
                            shippingAddress: contact.data,
                            language: wpeasycart_ajax_object.current_language
                        };
                        jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: \'post\', data: data, success: function( result ){
                            var json_result = JSON.parse( result );
                            ec_update_cart( json_result.cart_data );
                            done({
                                shippingOptions: json_result.shipping_options,
                                lineItems: json_result.display_items,
                                total: {
                                  label: "' . get_option( 'ec_option_square_merchant_name' ) . '",
                                  amount: json_result.total,
                                  pending: false
                                }
                            });
                        } } );
                    },
                    shippingOptionChanged: function( shipping_option, done ){
                        var data = {
                            action: \'ec_ajax_update_square_shipping_option_dynamic\',
                            shippingAddress: shipping_option.data.id,
                        };
                        jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: \'post\', data: data, success: function( result ){
                            var json_result = JSON.parse( result );
                            ec_update_cart( json_result.cart_data );
                            done({
                                lineItems: json_result.display_items,
                                total: {
                                  label: "' . get_option( 'ec_option_square_merchant_name' ) . '",
                                  amount: json_result.total,
                                  pending: false
                                }
                            });
                        } } );
                    },';
            }
            echo '
                    cardNonceResponseReceived: function( errors, nonce, cardData, billingContact, shippingContact, shippingOption ) {
                        if( errors ){
                            console.log( "Encountered errors:" );
                            errors.forEach ( function ( error ){
                            console.log( \'  \' + error.message );
                            alert( error.message );
                            } );
                            return;
                        }else{
                            jQuery( document.getElementById( \'square-success-cover\' ) ).fadeIn( );
                            var data = {
                                action: \'ec_ajax_square_complete_payment\',
                                nonce: nonce,';
            if( !$is_payment ){
            echo '
                                shipping_address: shippingContact.data,
                                shipping_method: shippingOption.id,
                                billing_address: billingContact.data,';
            }
            echo '
                                card_type: cardData.card_brand,
                                last_4: cardData.last_4,
                                exp_month: cardData.exp_month,
                                exp_year: cardData.exp_year,';
            if( get_option( 'ec_option_require_terms_agreement' ) ){
            echo '
                                ec_terms_agree: 1,';	
            }
            echo '
                                language: wpeasycart_ajax_object.current_language
                            };
                            jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: \'post\', data: data, success: function( result ){
                                if( result == \'0\' ){
                                    jQuery( location ).attr( \'href\', \'' . $this->cart_page . $this->permalink_divider . ( ( $is_payment ) ? 'ec_page=checkout_payment&' : '' ) . 'ec_cart_error=payment_failed\' );
                                }else{
                                    jQuery( location ).attr( \'href\', result );
                                }
                            } } );
                        }
                    },
                }
            } );' . ( ( get_option( 'ec_option_cache_prevent' ) ) ? 'paymentForm.build( );' : '' ) . '
            </script>';
            
            echo '<style>
            /* Customize the Apple Pay on the Web button */
            #sq-apple-pay {
              width: 100%;
              height: 48px;
              padding: 0;
              margin: 24px 0 16px 0;
              background-image: -webkit-named-image(apple-pay-logo-white);
              background-color: black;
              background-size: 100% 60%;
              background-repeat: no-repeat;
              background-position: 50% 50%;
              border-radius: 4px;
              cursor: pointer;
              display: none;
            }

            /* Customize the Masterpass button */
            #sq-masterpass {
              width: 100%;
              height: 48px;
              padding: 0;
              margin: 24px 0 24px;
              background-image: url("https://masterpass.com/dyn/img/acc/global/mp_mark_hor_wht.svg");
              background-color: black;
              background-size: 100% 60%;
              background-repeat: no-repeat;
              background-position: calc((100% - 32px) / 2) 50%;
              border-radius: 4px;
              cursor: pointer;
              display: none;
            }

            #sq-masterpass::after {
              box-sizing: border-box;
              float: right;
              width: 32px;
              height: 48px;
              padding-top: 12px;
              content: url("data:image/svg+xml; utf8, <svg width=\'14\' height=\'24\' viewBox=\'0 0 14 24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M1.891 23.485c-.389 0-.778-.144-1.075-.436a1.46 1.46 0 0 1 0-2.102l9.141-8.944L.817 3.06a1.463 1.463 0 0 1 0-2.104 1.544 1.544 0 0 1 2.15 0l10.217 9.994a1.464 1.464 0 0 1 0 2.105L2.966 23.049a1.525 1.525 0 0 1-1.075.436\' fill=\'#FFF\' fill-rule=\'evenodd\'/></svg>");
              border-radius: 0 4px 4px 0;
            }

            /* Customize the Google Pay button */
            .button-google-pay {
              min-width: 200px;
              min-height: 40px;
              padding: 11px 24px;
              margin: 10px;
              background-color: #000;
              background-image: url(data:image/svg+xml,%3Csvg%20width%3D%22103%22%20height%3D%2217%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cpath%20d%3D%22M.148%202.976h3.766c.532%200%201.024.117%201.477.35.453.233.814.555%201.085.966.27.41.406.863.406%201.358%200%20.495-.124.924-.371%201.288s-.572.64-.973.826v.084c.504.177.912.471%201.225.882.313.41.469.891.469%201.442a2.6%202.6%200%200%201-.427%201.47c-.285.43-.667.763-1.148%201.001A3.5%203.5%200%200%201%204.082%2013H.148V2.976zm3.696%204.2c.448%200%20.81-.14%201.085-.42.275-.28.413-.602.413-.966s-.133-.684-.399-.959c-.266-.275-.614-.413-1.043-.413H1.716v2.758h2.128zm.238%204.368c.476%200%20.856-.15%201.141-.448.285-.299.427-.644.427-1.036%200-.401-.147-.749-.441-1.043-.294-.294-.688-.441-1.183-.441h-2.31v2.968h2.366zm5.379.903c-.453-.518-.679-1.239-.679-2.163V5.86h1.54v4.214c0%20.579.138%201.013.413%201.302.275.29.637.434%201.085.434.364%200%20.686-.096.966-.287.28-.191.495-.446.644-.763a2.37%202.37%200%200%200%20.224-1.022V5.86h1.54V13h-1.456v-.924h-.084c-.196.336-.5.611-.91.826-.41.215-.845.322-1.302.322-.868%200-1.528-.259-1.981-.777zm9.859.161L16.352%205.86h1.722l2.016%204.858h.056l1.96-4.858H23.8l-4.41%2010.164h-1.624l1.554-3.416zm8.266-6.748h1.666l1.442%205.11h.056l1.61-5.11h1.582l1.596%205.11h.056l1.442-5.11h1.638L36.392%2013h-1.624L33.13%207.876h-.042L31.464%2013h-1.596l-2.282-7.14zm12.379-1.337a1%201%200%200%201-.301-.735%201%201%200%200%201%20.301-.735%201%201%200%200%201%20.735-.301%201%201%200%200%201%20.735.301%201%201%200%200%201%20.301.735%201%201%200%200%201-.301.735%201%201%200%200%201-.735.301%201%201%200%200%201-.735-.301zM39.93%205.86h1.54V13h-1.54V5.86zm5.568%207.098a1.967%201.967%200%200%201-.686-.406c-.401-.401-.602-.947-.602-1.638V7.218h-1.246V5.86h1.246V3.844h1.54V5.86h1.736v1.358H45.75v3.36c0%20.383.075.653.224.812.14.187.383.28.728.28.159%200%20.299-.021.42-.063.121-.042.252-.11.392-.203v1.498c-.308.14-.681.21-1.12.21-.317%200-.616-.051-.896-.154zm3.678-9.982h1.54v2.73l-.07%201.092h.07c.205-.336.511-.614.917-.833.406-.22.842-.329%201.309-.329.868%200%201.53.254%201.988.763.457.509.686%201.202.686%202.079V13h-1.54V8.688c0-.541-.142-.947-.427-1.218-.285-.27-.656-.406-1.113-.406-.345%200-.656.098-.931.294a2.042%202.042%200%200%200-.651.777%202.297%202.297%200%200%200-.238%201.029V13h-1.54V2.976zm32.35-.341v4.083h2.518c.6%200%201.096-.202%201.488-.605.403-.402.605-.882.605-1.437%200-.544-.202-1.018-.605-1.422-.392-.413-.888-.62-1.488-.62h-2.518zm0%205.52v4.736h-1.504V1.198h3.99c1.013%200%201.873.337%202.582%201.012.72.675%201.08%201.497%201.08%202.466%200%20.991-.36%201.819-1.08%202.482-.697.665-1.559.996-2.583.996h-2.485v.001zm7.668%202.287c0%20.392.166.718.499.98.332.26.722.391%201.168.391.633%200%201.196-.234%201.692-.701.497-.469.744-1.019.744-1.65-.469-.37-1.123-.555-1.962-.555-.61%200-1.12.148-1.528.442-.409.294-.613.657-.613%201.093m1.946-5.815c1.112%200%201.989.297%202.633.89.642.594.964%201.408.964%202.442v4.932h-1.439v-1.11h-.065c-.622.914-1.45%201.372-2.486%201.372-.882%200-1.621-.262-2.215-.784-.594-.523-.891-1.176-.891-1.96%200-.828.313-1.486.94-1.976s1.463-.735%202.51-.735c.892%200%201.629.163%202.206.49v-.344c0-.522-.207-.966-.621-1.33a2.132%202.132%200%200%200-1.455-.547c-.84%200-1.504.353-1.995%201.062l-1.324-.834c.73-1.045%201.81-1.568%203.238-1.568m11.853.262l-5.02%2011.53H96.42l1.864-4.034-3.302-7.496h1.635l2.387%205.749h.032l2.322-5.75z%22%20fill%3D%22%23FFF%22%2F%3E%3Cpath%20d%3D%22M75.448%207.134c0-.473-.04-.93-.116-1.366h-6.344v2.588h3.634a3.11%203.11%200%200%201-1.344%202.042v1.68h2.169c1.27-1.17%202.001-2.9%202.001-4.944%22%20fill%3D%22%234285F4%22%2F%3E%3Cpath%20d%3D%22M68.988%2013.7c1.816%200%203.344-.595%204.459-1.621l-2.169-1.681c-.603.406-1.38.643-2.29.643-1.754%200-3.244-1.182-3.776-2.774h-2.234v1.731a6.728%206.728%200%200%200%206.01%203.703%22%20fill%3D%22%2334A853%22%2F%3E%3Cpath%20d%3D%22M65.212%208.267a4.034%204.034%200%200%201%200-2.572V3.964h-2.234a6.678%206.678%200%200%200-.717%203.017c0%201.085.26%202.11.717%203.017l2.234-1.731z%22%20fill%3D%22%23FABB05%22%2F%3E%3Cpath%20d%3D%22M68.988%202.921c.992%200%201.88.34%202.58%201.008v.001l1.92-1.918c-1.165-1.084-2.685-1.75-4.5-1.75a6.728%206.728%200%200%200-6.01%203.702l2.234%201.731c.532-1.592%202.022-2.774%203.776-2.774%22%20fill%3D%22%23E94235%22%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E);
              background-origin: content-box;
              background-position: center;
              background-repeat: no-repeat;
              background-size: contain;
              border: 0;
              border-radius: 4px;
              box-shadow: 0 1px 1px 0 rgba(60, 64, 67, 0.30), 0 1px 3px 1px rgba(60, 64, 67, 0.15);
              outline: 0;
              cursor: pointer;
              display: none;
            }
            #sq-walletbox {
              margin: 0;
              padding: 0;
              text-align: center;
              vertical-align: top;
              width:100%;
              height:123;
              display:none;
            }
            </style>';
        }
        
    }
    
    public function get_dynamic_square_line_items( ){
        $return_arr     = array( (object) array(
            'label'     => $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_subtotal' ),
            'amount'    => number_format( $this->order_totals->sub_total, 2, '.', '' ),
            'pending'   => false
        ) );
        
        if( $this->order_totals->tax_total > 0 ){
            $return_arr[] = (object) array(
                'label'     => $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tax' ),
                'amount'    => number_format( $this->order_totals->tax_total, 2, '.', '' ),
                'pending'   => true
            );
        }

        if( get_option( 'ec_option_use_shipping' ) && ( $this->cart->shippable_total_items > 0 || $this->order_totals->handling_total > 0 ) && $this->order_totals->shipping_total > 0 ){
            $return_arr[] = (object) array(
                'label'     => $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_shipping' ),
                'amount'    => number_format( $this->order_totals->shipping_total, 2, '.', '' ),
                'pending'   => true
            );
        }

        if( $this->order_totals->discount_total > 0 ){
            $return_arr[] = (object) array(
                'label'     => $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_discounts' ),
                'amount'    => number_format( $this->order_totals->discount_total, 2, '.', '' ),
                'pending'   => false
            );
        }

        if( $this->tax->is_duty_enabled( ) && $this->order_totals->duty_total > 0 ){
            $return_arr[] = (object) array(
                'label'     => $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_duty' ),
                'amount'    => number_format( $this->order_totals->duty_total, 2, '.', '' ),
                'pending'   => true
            );
        }

        if( $this->tax->is_vat_enabled( ) && $this->order_totals->vat_total > 0 ){
            $return_arr[] = (object) array(
                'label'     => $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_vat' ),
                'amount'    => number_format( $this->order_totals->vat_total, 2, '.', '' ),
                'pending'   => true
            );
        }

        if( get_option( 'ec_option_enable_easy_canada_tax' ) && $this->order_totals->gst_total > 0  ){
            $return_arr[] = (object) array(
                'label'     => 'GST' . ( ( $this->tax->gst_rate > 0 ) ? ' ' .$this->tax->gst_rate . '%' : '' ),
                'amount'    => number_format( $this->order_totals->gst_total, 2, '.', '' ),
                'pending'   => true
            );
        }

        if( get_option( 'ec_option_enable_easy_canada_tax' ) && $this->order_totals->pst_total > 0  ){
            $return_arr[] = (object) array(
                'label'     => 'PST' . ( ( $this->tax->pst_rate > 0 ) ? ' ' .$this->tax->pst_rate . '%' : '' ),
                'amount'    => number_format( $this->order_totals->pst_total, 2, '.', '' ),
                'pending'   => true
            );
        }

        if( get_option( 'ec_option_enable_easy_canada_tax' ) && $this->order_totals->hst_total > 0 ){
            $return_arr[] = (object) array(
                'label'     => 'HST' . ( ( $this->tax->hst_rate > 0 ) ? ' ' .$this->tax->hst_rate . '%' : '' ),
                'amount'    => number_format( $this->order_totals->hst_total, 2, '.', '' ),
                'pending'   => true
            );
        }
        
        return $return_arr;
    }
	
	public function print_stripe_payment_button( $is_payment = false ){
		if( get_option( 'ec_option_stripe_enable_apple_pay' ) && apply_filters( 'wp_easycart_allow_paypal_express', false ) && (int) ( $this->order_totals->get_converted_grand_total( ) * 100 ) >= 50 ){
			if( (float) get_option( 'ec_option_minimum_order_total' ) > 0 && (float) get_option( 'ec_option_minimum_order_total' ) > $this->cart->subtotal ){
                echo '<div id="ec-stripe-wallet-button"></div>';
            }else{
                $client_secret = $this->get_stripe_intent_client_secret( );
                if( $is_payment ){
                    echo '<div class="ec_cart_option_row" id="ec_apple_pay_row">
                        <input type="radio" class="no_wrap" name="ec_cart_payment_selection" id="ec_payment_apple" value="apple_pay"';
                        if( $this->get_selected_payment_method( ) == "apple_pay" ){
                            echo ' checked="checked"';
                        }
                        echo 'onChange="ec_update_payment_display( );" /> ' . $GLOBALS['language']->get_text( 'cart_payment_information', 'cart_payment_information_apple_pay' ) . '
                    </div>
                    <div id="ec_apple_pay_form"';
                    if( $this->get_selected_payment_method( ) == "apple_pay" ){
                        echo ' style="display:block;"';
                    }else{
                        echo ' style="display:none;"';
                    }
                    echo '><div class="ec_cart_box_section">';
                }else{
                    echo '<div id="ec-stripe-wallet-button">';
                }
                echo '<div id="payment-request-button" style="float:left; width:100%;">
                  <!-- A Stripe Element will be inserted here. -->
                </div>';
                if( $is_payment ){
                    echo '</div></div>';
                }
                echo '<div id="stripe-success-cover" style="display:none; cursor:default; position:fixed; top:0; left:0; width:100%; height:100%; z-index:999999; background-color: rgba(0, 0, 0, 0.8); color:#FFF;">
                    <style>
                    @keyframes rotation{
                        0%  { transform:rotate(0deg); }
                        100%{ transform:rotate(359deg); }
                    }
                    </style>
                    <div style=\'font-family: "HelveticaNeue", "HelveticaNeue-Light", "Helvetica Neue Light", helvetica, arial, sans-serif; font-size: 14px; text-align: center; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; -ms-box-sizing: border-box; box-sizing: border-box; width: 350px; top: 50%; left: 50%; position: absolute; margin-left: -165px; margin-top: -80px; cursor: pointer; text-align: center;\'>
                        <div class="paypal-checkout-loader">
                            <div style="height: 30px; width: 30px; display: inline-block; box-sizing: content-box; opacity: 1; filter: alpha(opacity=100); -webkit-animation: rotation .7s infinite linear; -moz-animation: rotation .7s infinite linear; -o-animation: rotation .7s infinite linear; animation: rotation .7s infinite linear; border-left: 8px solid rgba(0, 0, 0, .2); border-right: 8px solid rgba(0, 0, 0, .2); border-bottom: 8px solid rgba(0, 0, 0, .2); border-top: 8px solid #fff; border-radius: 100%;"></div>
                        </div>
                    </div>
                </div>';
                if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
                    $pkey = get_option( 'ec_option_stripe_public_api_key' );
                else if( get_option( 'ec_option_payment_process_method' ) == 'stripe_connect' && get_option( 'ec_option_stripe_connect_use_sandbox' ) )
                    $pkey = get_option( 'ec_option_stripe_connect_sandbox_publishable_key' );
                else
                    $pkey = get_option( 'ec_option_stripe_connect_production_publishable_key' );
                echo '<script type="text/javascript">
                    jQuery( document.getElementById( \'stripe-success-cover\' ) ).appendTo( document.body );
                    var stripe = Stripe( \'' . $pkey . '\' );
                    var clientSecret = \'' . $client_secret . '\';
                    var paymentRequest = stripe.paymentRequest({
                        country: \'' . get_option( 'ec_option_stripe_company_country' ) . '\',
                        currency: \'' . strtolower( get_option( 'ec_option_stripe_currency' ) ) . '\',
                        displayItems:' . json_encode( wpeasycart_get_cart_display_items( $this->cart, $this->order_totals, $this->tax ) ) . ',
                        total: {
                            label: \'' . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_grand_total' ) . '\',
                            amount: ' . (int) ( $this->order_totals->get_converted_grand_total( ) * 100 ) . ',
                        },';
                if( !$is_payment ){
                echo '
                        requestShipping: true,
                        requestPayerPhone: true,';
                }
                if( !$is_payment && !$GLOBALS['ec_user']->user_id ){
                echo '		
                        requestPayerEmail: true,';
                }
                if( !$is_payment ){
                echo '
                        shippingOptions: ' . json_encode( $this->ec_cart_display_shipping_methods_stripe_dynamic( $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_standard' ),$GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_express' ) ) ) . ',';
                }
                echo '
                    });
                    paymentRequest.on( \'paymentmethod\', function(ev) {
                        jQuery( document.getElementById( \'stripe-success-cover\' ) ).show( );
                        jQuery( document.getElementById( \'ec_stripe_error\' ) ).fadeOut( );';
                    if( !$is_payment ){
                    echo '
                        var data = {
                            action: \'ec_ajax_get_stripe_shipping_dynamic\',
                            shippingAddress: ev.shippingAddress,
                            language: wpeasycart_ajax_object.current_language
                        };
                        jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: \'post\', data: data, success: function( result ){
                            var json_result = JSON.parse( result );
                            ec_update_cart( json_result.cart_data );';
                    }
                    echo '
                            stripe.confirmPaymentIntent( clientSecret, {
                                payment_method: ev.paymentMethod.id,
                            } ).then( function( confirmResult ){
                                if( confirmResult.error ){
                                    jQuery( document.getElementById( \'stripe-success-cover\' ) ).fadeOut( );
                                    jQuery( document.getElementById( \'ec_stripe_error\' ) ).fadeIn( ).find( \'div\' ).html( confirmResult.error.message );
                                    ev.complete( \'fail\' );
                                }else if( confirmResult.paymentIntent.status == \'succeeded\' ){
                                    var data = {
                                        action: \'ec_ajax_get_stripe_complete_payment\',';
                    if( !$is_payment ){
                    echo '
                                        payment_id: ev.paymentMethod.id,
                                        shipping_address: ev.shippingAddress,
                                        shipping_method: ev.shippingOption.id,
                                        billing_address: ev.paymentMethod.billing_details.address,
                                        billing_name: ev.paymentMethod.billing_details.name,
                                        billing_phone: ev.paymentMethod.billing_details.phone,
                                        billing_email: ev.paymentMethod.billing_details.email,';
                    }
                    echo '
                                        card_type: ev.paymentMethod.card.brand,
                                        last_4: ev.paymentMethod.card.last4,
                                        exp_month: ev.paymentMethod.card.exp_month,
                                        exp_year: ev.paymentMethod.card.exp_year,
                                        email: ev.payerEmail,
                                        phone: ev.payerPhone,';
                    if( get_option( 'ec_option_require_terms_agreement' ) ){
                    echo '
                                        ec_terms_agree: 1,';	
                    }					
                    echo '
                                        clientSecret:clientSecret,
                                        language: wpeasycart_ajax_object.current_language
                                    };
                                    jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: \'post\', data: data, success: function( result ){
                                        ev.complete( \'success\' );
                                        jQuery( location ).attr( \'href\', result );
                                    } } );
                                }else{
                                    ev.complete( \'success\' );
                                    stripe.handleCardPayment( clientSecret ).then( function( result ){
                                        if( result.error ){
                                            jQuery( document.getElementById( \'stripe-success-cover\' ) ).fadeOut( );
                                            jQuery( document.getElementById( \'ec_stripe_error\' ) ).fadeIn( ).find( \'div\' ).html( result.error.message );
                                        }else{
                                            var data = {
                                                action: \'ec_ajax_get_stripe_complete_payment\',
                                                payment_id: ev.paymentMethod.id,';
                            if( !$is_payment ){
                            echo '
                                                shipping_address: ev.shippingAddress,
                                                shipping_method: ev.shippingOption.id,
                                                billing_address: ev.paymentMethod.billing_details.address,
                                                billing_name: ev.paymentMethod.billing_details.name,
                                                billing_phone: ev.paymentMethod.billing_details.phone,
                                                billing_email: ev.paymentMethod.billing_details.email,';
                            }
                            echo '
                                                card_type: ev.paymentMethod.card.brand,
                                                last_4: ev.paymentMethod.card.last4,
                                                exp_month: ev.paymentMethod.card.exp_month,
                                                exp_year: ev.paymentMethod.card.exp_year,
                                                email: ev.payerEmail,
                                                phone: ev.payerPhone,';
                            if( get_option( 'ec_option_require_terms_agreement' ) ){
                            echo '
                                                ec_terms_agree: 1,';	
                            }
                            echo '
                                                clientSecret:clientSecret,
                                                language: wpeasycart_ajax_object.current_language
                                            };
                                            jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: \'post\', data: data, success: function( result ){
                                                jQuery( location ).attr( \'href\', result );
                                            } } );
                                        }
                                    } );
                                }
                            } );';
                    if( !$is_payment ){
                    echo '
                        } } );';
                    }
                    echo '
                    } );
                    paymentRequest.on( \'shippingaddresschange\', function(ev) {
                        var data = {
                            action: \'ec_ajax_get_stripe_shipping_dynamic\',
                            shippingAddress: ev.shippingAddress,
                            language: wpeasycart_ajax_object.current_language
                        };
                        jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: \'post\', data: data, success: function( result ){
                            var json_result = JSON.parse( result );
                            ec_update_cart( json_result.cart_data );
                            ev.updateWith( {
                                status: \'success\',
                                shippingOptions: json_result.shipping_options,
                                displayItems: json_result.display_items,
                                total: {
                                    label: \'' . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_grand_total' ) . '\',
                                    amount: json_result.total,
                                  },
                            } );
                        } } );
                    } );
                    paymentRequest.on( \'shippingoptionchange\', function(ev) {
                        var data = {
                            action: \'ec_ajax_get_stripe_shipping_option_dynamic\',
                            billingAddress: ev.billingAddress,
                            shippingAddress: ev.shippingAddress,
                            shippingOption: ev.shippingOption,
                            language: wpeasycart_ajax_object.current_language
                        };
                        jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: \'post\', data: data, success: function( result ){
                            var json_result = JSON.parse( result );
                            ec_update_cart( json_result.cart_data );
                            ev.updateWith( {
                                status: \'success\',
                                shippingOptions: json_result.shipping_options,
                                displayItems: json_result.display_items,
                                total: {
                                  label: \'' . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_grand_total' ) . '\',
                                  amount: json_result.total,
                                }
                            } );
                        } } );
                    } );
                    paymentRequest.on( \'cancel\', function( ev ){
                        jQuery( document.getElementById( \'stripe-success-cover\' ) ).fadeOut( );
                    } );
                    var elements = stripe.elements( );
                    var prButton = elements.create( \'paymentRequestButton\', {
                        paymentRequest: paymentRequest
                    } );';
                if( get_option( 'ec_option_require_terms_agreement' ) ){
                echo '
                    prButton.on( \'click\', function( ev ){
                        if( !confirm( \'' . str_replace( "'", "\'", $GLOBALS['language']->get_text( 'cart_payment_information', 'cart_payment_review_agree' ) ) . '.\' ) ){
                            paymentRequest.trigger( \'cancel\' );
                        }
                    } );';
                }
                echo '
                    paymentRequest.canMakePayment( ).then( function( result ){
                        if( result ){';
                if( $is_payment ){
                    echo '
                    ec_update_payment_display( );';
                }
                echo '
                            prButton.mount( \'#payment-request-button\' );
                        }else{';
                if( $is_payment ){
                    echo '
                            document.getElementById( \'ec_apple_pay_row\' ).style.display = \'none\';
                            document.getElementById( \'ec_apple_pay_form\' ).style.display = \'none\';';
                }
                echo '
                            document.getElementById( \'payment-request-button\' ).style.display = \'none\';
                        }
                    });
                </script>';
                if( !$is_payment ){
                    echo '</div>';
                }
            }
        }
	}
	/* END SHIPPING METHOD FUNCTIONS */
	
	/* START COUPON FUNCTIONS */
	public function display_coupon( ){
		if(	$this->cart->total_items > 0 ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_coupon.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_coupon.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_coupon.php' );
		}
	}
	
	public function display_coupon_input( $redeem_text ){
		echo "<input type=\"text\" name=\"ec_cart_coupon_code\" id=\"ec_cart_coupon_code\" class=\"ec_cart_coupon_input_text\" value=\"";
		if( $this->coupon_code != "" )
			echo $this->coupon_code;
		echo "\" /><div class=\"ec_cart_coupon_code_redeem_button\"><a href=\"#\" onclick=\"ec_cart_coupon_code_redeem(); return false;\">" . $redeem_text . "</a></div>";
	}
	
	public function display_coupon_input_text( ){
		echo "<input type=\"text\" name=\"ec_cart_coupon_code\" id=\"ec_cart_coupon_code\" class=\"ec_cart_coupon_input_text\" value=\"";
		if( $this->coupon_code != "" )
			echo $this->coupon_code;
		
		echo "\" />";
	}
	
	public function display_coupon_input_button( $redeem_text ){
		echo "<div class=\"ec_cart_coupon_code_redeem_button\"><a href=\"#\" onclick=\"ec_cart_coupon_code_redeem(); return false;\">" . $redeem_text . "</a></div>";
	}
	
	public function display_coupon_loader( ){
		if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_page/loader.gif" ) )	
			echo "<div class=\"ec_cart_coupon_loader\" id=\"ec_cart_coupon_loader\"><img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_page/loader.gif" ) . "\" /></div>";	
		else
			echo "<div class=\"ec_cart_coupon_loader\" id=\"ec_cart_coupon_loader\"><img src=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_page/loader.gif" ) . "\" /></div>";
	}
	
	public function display_coupon_message( ){
		if( isset( $this->coupon ) )
			echo $this->coupon->message;
		else if( $this->coupon_code != "" )
			echo $GLOBALS['language']->get_text( 'cart_coupons', 'cart_invalid_coupon' );
	}
	/* END COUPON FUNCTIONS */
	
	/* START GIFT CARD FUNCTIONS */
	public function display_gift_card( ){
		if(	$this->cart->total_items > 0 ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_gift_card.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_gift_card.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_gift_card.php' );
		}
	}
	
	public function display_gift_card_input( $redeem_text ){
		echo "<input type=\"text\" name=\"ec_cart_gift_card\" id=\"ec_cart_gift_card\" class=\"ec_cart_gift_card_input_text\" value=\"";
		if( $this->gift_card != "" )
			echo $this->gift_card;
		echo "\" /><div class=\"ec_cart_gift_card_redeem_button\"><a href=\"#\" onclick=\"ec_cart_gift_card_redeem(); return false;\">" . $redeem_text . "</a></div>";
	}
	
	public function display_gift_card_input_text( ){
		echo "<input type=\"text\" name=\"ec_cart_gift_card\" id=\"ec_cart_gift_card\" class=\"ec_cart_gift_card_input_text\" value=\"";
		if( $this->gift_card != "" )
			echo $this->gift_card;
			
		echo "\" />";
	}
	
	public function display_gift_card_input_button( $redeem_text ){
		echo "<div class=\"ec_cart_gift_card_redeem_button\"><a href=\"#\" onclick=\"ec_cart_gift_card_redeem(); return false;\">" . $redeem_text . "</a></div>";
	}
	
	public function display_gift_card_loader( ){
		if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_page/loader.gif" ) )	
			echo "<div class=\"ec_cart_gift_card_loader\" id=\"ec_cart_gift_card_loader\"><img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_page/loader.gif" ) . "\" /></div>";
		else
			echo "<div class=\"ec_cart_gift_card_loader\" id=\"ec_cart_gift_card_loader\"><img src=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_page/loader.gif" ) . "\" /></div>";
		
	}
	
	public function display_gift_card_message( ){
		if( isset( $this->giftcard ) )
			echo $this->giftcard->message;
		else if( $this->gift_card != "" )
			echo $GLOBALS['language']->get_text( 'cart_coupons', 'cart_invalid_giftcard' );
	}
	/* END GIFT CARD FUNCTIONS */
	
	public function display_continue_to_shipping_button( $button_text ){
		echo "<input type=\"submit\" class=\"ec_cart_continue_to_shipping_button\" value=\"" . $button_text . "\" onclick=\"return ec_cart_validate_checkout_info( );\" />";
	}
	
	/* START CONTINUE TO PAYMENT FUNCTIONS */
	public function display_continue_to_payment_button( $button_text ){
		echo "<input type=\"submit\" class=\"ec_cart_continue_to_payment_button\" value=\"" . $button_text . "\" onclick=\"return ec_cart_validate_checkout_shipping( );\" />";
	}
	/* END CONTINUE TO PAYMENT FUNCTIONS */
	
	public function display_submit_order_button( $button_text ){
		
		if( isset( $_GET['subscription'] ) ){
			echo "<input type=\"submit\" id=\"ec_submit_payment_button\" class=\"ec_cart_submit_order_button\" value=\"" . $button_text . "\" onclick=\"return ec_cart_validate_subscription_order();\" />";
		}else{
			echo "<input type=\"submit\" id=\"ec_submit_payment_button\" class=\"ec_cart_submit_order_button\" value=\"" . $button_text . "\" onclick=\"return ec_cart_validate_checkout_submit_order();\" />";
		}
		
	}
	
	public function display_cancel_order_button( $button_text ){
		echo "<input type=\"button\" id=\"ec_cancel_payment_button\" class=\"ec_cart_submit_order_button\" value=\"" . $button_text . "\" onclick=\"return ec_cart_cancel_order();\" />";
	}
	
	public function display_order_review_button( $button_text ){
		echo "<input type=\"button\" id=\"ec_review_payment_button\" class=\"ec_cart_submit_order_button\" value=\"" . $button_text . "\" onclick=\"if( ec_cart_validate_checkout_submit_order( ) ){ ec_cart_show_review_panel( ); } return false;\" />";
	}
	
	/* START ADDRESS REVIEW FUNCTIONS */
	public function display_address_review(){
		if(	$this->cart->total_items > 0 ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_address_review.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_address_review.php' );
			else	
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_address_review.php' );
		}
		
		if( !get_option( 'ec_option_use_shipping' ) )
			echo "<script>jQuery('.ec_cart_address_review_middle').html('');</script>";
	}
	
	public function display_edit_address_link( $link_text ){
		echo "<a href=\"" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info\">" . $link_text . "</a>";	
	}
	
	public function display_review_billing( $name ){
		if( $name == "first_name" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->billing_first_name, ENT_QUOTES );
		else if( $name == "last_name" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->billing_last_name, ENT_QUOTES );
		else if( $name == "address" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->billing_address_line_1, ENT_QUOTES );
		else if( $name == "address2" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->billing_address_line_2, ENT_QUOTES );
		else if( $name == "city" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->billing_city, ENT_QUOTES );
		else if( $name == "state" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->billing_state, ENT_QUOTES );
		else if( $name == "zip" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->billing_zip, ENT_QUOTES );
		else if( $name == "country" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->billing_country, ENT_QUOTES );
		else if( $name == "phone" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->billing_phone, ENT_QUOTES );
		
	}
	
	public function has_billing_address_line2( ){
		if( $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 != "" ){
			return true;
		}else{
			return false;
		}
	}
	
	public function display_review_shipping( $name ){
		if( $name == "first_name" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->shipping_first_name, ENT_QUOTES );
		else if( $name == "last_name" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->shipping_last_name, ENT_QUOTES );
		else if( $name == "address" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1, ENT_QUOTES );
		else if( $name == "address2" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2, ENT_QUOTES );
		else if( $name == "city" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->shipping_city, ENT_QUOTES );
		else if( $name == "state" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->shipping_state, ENT_QUOTES );
		else if( $name == "zip" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->shipping_zip, ENT_QUOTES );
		else if( $name == "country" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->shipping_country, ENT_QUOTES );
		else if( $name == "phone" )
			echo htmlspecialchars( $GLOBALS['ec_cart_data']->cart_data->shipping_phone, ENT_QUOTES );
	}
	
	public function has_shipping_address_line2( ){
		if( $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 != "" ){
			return true;
		}else{
			return false;
		}
	}
	
	public function display_selected_shipping_method( ){
		echo $this->shipping->get_selected_shipping_method();
	}
	/* END ADDRESS REVIEW FUNCTIONS */
	
	/* START PAYMENT INFORMATION FUNCTIONS */
    public function display_payment( $ideal_source = '', $ideal_client_secret = '' ){
        do_action( 'wp_easycart_display_payment_pre' );
		if(	$this->cart->total_items > 0 && apply_filters( 'wp_easycart_allow_payment', 1 ) ){
			if( isset( $_GET['PID'] ) && isset( $_GET['PYID'] ) && file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_paypal_express.php' ) ){
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_paypal_express.php' );
			}else if( isset( $_GET['PID'] ) && isset( $_GET['PYID'] ) ){
					include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_paypal_express.php' );
			}else if( get_option( 'ec_option_payment_third_party' ) == "paypal_advanced" ){ 
				$this->payment->show_paypal_iframe( $this->order_totals->grand_total );
			}else if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_payment.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_payment.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_payment.php' );
		}
        do_action( 'wp_easycart_display_payment_post' );
	}
    
    public function display_payment_paypal_express( $pid = false, $pyid = false, $oid = false ){
        do_action( 'wp_easycart_display_payment_pre' );
		if(	$this->cart->total_items > 0 && apply_filters( 'wp_easycart_allow_payment', 1 ) ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_paypal_express.php' ) ){
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_paypal_express.php' );
			}else{
                include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_paypal_express.php' );
			}
		}
        do_action( 'wp_easycart_display_payment_post' );
	}
	
    public function display_payment_information( ){
    	if(	$this->cart->total_items > 0 && $this->order_totals->grand_total > 0 ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_payment_information.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_payment_information.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_payment_information.php' );
			
			echo "<script>jQuery(\"input[name=ec_cart_payment_selection][value='" . get_option( 'ec_option_default_payment_type' ) . "']\").attr('checked', 'checked');";
			if( get_option( 'ec_option_default_payment_type' ) == "manual_bill" ){
				echo "jQuery('#ec_cart_pay_by_manual_payment').show();";
			}else if( get_option( 'ec_option_default_payment_type' ) == "affirm" ){
				echo "jQuery('#ec_cart_pay_by_affirm').show();";
			}else if( get_option( 'ec_option_default_payment_type' ) == "third_party" ){
				echo "jQuery('#ec_cart_pay_by_third_party').show();";
			}else if( get_option( 'ec_option_default_payment_type' ) == "credit_card" ){
				echo "jQuery('#ec_cart_pay_by_credit_card_holder').show();";
			}
			echo "</script>";
		}
	}
	
	public function use_manual_payment( ){
		if( get_option( 'ec_option_use_direct_deposit' ) )
			return true;
		else
			return false;
	}
	
	public function display_manual_payment_text( ){
		echo nl2br( $GLOBALS['language']->convert_text( get_option( 'ec_option_direct_deposit_message' ) ) );
	}
	
	public function use_third_party( ){
		if( get_option( 'ec_option_payment_third_party' ) )
			return true;
		else
			return false;
	}
	
	public function ec_cart_display_third_party_form_start( ){
		$this->payment->third_party->initialize( $_GET['order_id'] );
		$this->payment->third_party->display_form_start( );
	}
	
	public function ec_cart_display_third_party_form_end( ){
		echo "</form>";
	}
	
	public function display_third_party_submit_button( $button_text ){
		echo "<input type=\"submit\" class=\"ec_cart_submit_third_party\" value=\"" . $button_text . "\" />";
	}
	
	public function ec_cart_display_current_third_party_name( ){
		if( get_option( 'ec_option_payment_third_party' ) == "2checkout_thirdparty" )
			echo "2Checkout";
		else if( get_option( 'ec_option_payment_third_party' ) == "cashfree" )
			echo "Cashfree";
		else if( get_option( 'ec_option_payment_third_party' ) == "dwolla_thirdparty" )
			echo "Dwolla";
		else if( get_option( 'ec_option_payment_third_party' ) == "nets" )
			echo "Nets Netaxept";
		else if( get_option( 'ec_option_payment_third_party' ) == "payfast_thirdparty" )
			echo "Payfast";
		else if( get_option( 'ec_option_payment_third_party' ) == "payfort" )
			echo "Payfort";
		else if( get_option( 'ec_option_payment_third_party' ) == "paypal" )
			echo "PayPal";
		else if( get_option( 'ec_option_payment_third_party' ) == "sagepay_paynow_za" )
			echo "SagePay Pay Now";
		else if( get_option( 'ec_option_payment_third_party' ) == "skrill" )
			echo "Skrill";
		else if( get_option( 'ec_option_payment_third_party' ) == "realex_thirdparty" )
			echo "Realex Payments";
		else if( get_option( 'ec_option_payment_third_party' ) == "redsys" )
			echo "Redsys";
		else if( get_option( 'ec_option_payment_third_party' ) == "paymentexpress_thirdparty" )
			echo "Payment Express";
		else
			echo get_option( 'ec_option_custom_third_party' );
	}
	
	public function ec_cart_get_current_third_party_name( ){
		if( get_option( 'ec_option_payment_third_party' ) == "dwolla_thirdparty" )
			return "Dwolla";
		else if( get_option( 'ec_option_payment_third_party' ) == "nets" )
			return "Nets Netaxept";
		else if( get_option( 'ec_option_payment_third_party' ) == "paypal" )
			return "PayPal";
		else if( get_option( 'ec_option_payment_third_party' ) == "sagepay_paynow_za" )
			echo "SagePay Pay Now";
		else if( get_option( 'ec_option_payment_third_party' ) == "skrill" )
			return "Skrill";
		else if( get_option( 'ec_option_payment_third_party' ) == "realex_thirdparty" )
			return "Realex Payments";
		else if( get_option( 'ec_option_payment_third_party' ) == "redsys" )
			return "Redsys";
		else if( get_option( 'ec_option_payment_third_party' ) == "paymentexpress_thirdparty" )
			return "Payment Express";
		else
			return get_option( 'ec_option_custom_third_party' );
	}
	
	public function ec_cart_display_third_party_logo( ){
		if( get_option( 'ec_option_payment_third_party' ) == "paypal" ){
			if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/paypal.jpg" ) )	
				echo "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/paypal.jpg" ) . "\" alt=\"PayPal\" />";
			else
				echo "<img src=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/paypal.jpg") . "\" alt=\"PayPal\" />";
		}else if( get_option( 'ec_option_payment_third_party' ) == "skrill" ){
			if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/skrill-logo.gif" ) )	
				echo "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/skrill-logo.gif" ) . "\" alt=\"Skrill\" />";
			else
				echo "<img src=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/skrill-logo.gif") . "\" alt=\"Skrill\" />";
		}
	}
	
	public function use_payment_gateway( ){
		if( get_option( 'ec_option_payment_process_method' ) )
			return true;
		else
			return false;
	}
	
	public function ec_cart_display_credit_card_images(){
		//display credit card icons
		if( get_option('ec_option_use_visa') || get_option('ec_option_use_delta') || get_option('ec_option_use_uke') )
			echo "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/visa.png") . "\" alt=\"Visa\" class=\"ec_cart_payment_information_credit_card_active\" id=\"ec_cart_payment_credit_card_icon_visa\" />" . "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/visa_inactive.png") . "\" alt=\"Visa\" class=\"ec_cart_payment_information_credit_card_inactive\" id=\"ec_cart_payment_credit_card_icon_visa_inactive\" />";
		
		/*
		if( get_option('ec_option_use_delta') )
			echo "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/visadebit.png") . "\" alt=\"Visa Debit\" class=\"ec_cart_payment_information_credit_card_active\" id=\"ec_cart_payment_credit_card_icon_delta\" />" . "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/visadebit_inactive.png") . "\" alt=\"Visa Debit\" class=\"ec_cart_payment_information_credit_card_inactive\" id=\"ec_cart_payment_credit_card_icon_delta_inactive\" />";
			
		if( get_option('ec_option_use_uke') )
			echo "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/visaelectron.png") . "\" alt=\"Visa Electron\" class=\"ec_cart_payment_information_credit_card_active\" id=\"ec_cart_payment_credit_card_icon_uke\" />" . "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/visaelectron_inactive.png") . "\" alt=\"Visa Electron\" class=\"ec_cart_payment_information_credit_card_inactive\" id=\"ec_cart_payment_credit_card_icon_uke_inactive\" />";
		*/
		
		if( get_option('ec_option_use_discover') )
			echo "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/discover.png") . "\" alt=\"Discover\" class=\"ec_cart_payment_information_credit_card_active\" id=\"ec_cart_payment_credit_card_icon_discover\" />" . "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/discover_inactive.png") . "\" alt=\"Discover\" class=\"ec_cart_payment_information_credit_card_inactive\" id=\"ec_cart_payment_credit_card_icon_discover_inactive\" />";
		
		if( get_option('ec_option_use_mastercard') || get_option('ec_option_use_mcdebit') )
			echo "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/mastercard.png") . "\" alt=\"Mastercard\" class=\"ec_cart_payment_information_credit_card_active\" id=\"ec_cart_payment_credit_card_icon_mastercard\" />" . "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/mastercard_inactive.png") . "\" alt=\"Mastercard\" class=\"ec_cart_payment_information_credit_card_inactive\" id=\"ec_cart_payment_credit_card_icon_mastercard_inactive\" />";
		
		if( get_option('ec_option_use_amex') )
			echo "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/american_express.png") . "\" alt=\"AMEX\" class=\"ec_cart_payment_information_credit_card_active\" id=\"ec_cart_payment_credit_card_icon_amex\" />" . "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/american_express_inactive.png") . "\" alt=\"AMEX\" class=\"ec_cart_payment_information_credit_card_inactive\" id=\"ec_cart_payment_credit_card_icon_amex_inactive\" />";
		
		if( get_option('ec_option_use_jcb') )
			echo "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/jcb.png") . "\" alt=\"JCB\" class=\"ec_cart_payment_information_credit_card_active\" id=\"ec_cart_payment_credit_card_icon_jcb\" />" . "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/jcb_inactive.png") . "\" alt=\"JCB\" class=\"ec_cart_payment_information_credit_card_inactive\" id=\"ec_cart_payment_credit_card_icon_jcb_inactive\" />";
		
		if( get_option('ec_option_use_diners') )
			echo "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/diners.png") . "\" alt=\"Diners\" class=\"ec_cart_payment_information_credit_card_active\" id=\"ec_cart_payment_credit_card_icon_diners\" />" . "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/diners_inactive.png") . "\" alt=\"Diners\" class=\"ec_cart_payment_information_credit_card_inactive\" id=\"ec_cart_payment_credit_card_icon_diners_inactive\" />";
		
		/*
		if( get_option('ec_option_use_laser') )
			echo "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/laser.png") . "\" alt=\"Laser\" class=\"ec_cart_payment_information_credit_card_active\" id=\"ec_cart_payment_credit_card_icon_laser\" />" . "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/laser_inactive.png") . "\" alt=\"Laser\" class=\"ec_cart_payment_information_credit_card_inactive\" id=\"ec_cart_payment_credit_card_icon_laser_inactive\" />";
		*/
		
		if( get_option('ec_option_use_maestro') || get_option('ec_option_use_laser'))
			echo "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/maestro.png") . "\" alt=\"Maestro\" class=\"ec_cart_payment_information_credit_card_active\" id=\"ec_cart_payment_credit_card_icon_maestro\" />" . "<img src=\"" . plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_payment_information/maestro_inactive.png") . "\" alt=\"Maestro\" class=\"ec_cart_payment_information_credit_card_inactive\" id=\"ec_cart_payment_credit_card_icon_maestro_inactive\" />";
		
		
	}
	
	public function ec_cart_display_payment_method_input( $select_one_text ){
		echo "<select name=\"ec_cart_payment_type\" id=\"ec_cart_payment_type\" class=\"ec_cart_payment_information_input_select no_wrap\">";
		
		echo "<option value=\"0\">" . $select_one_text . "</option>";
		
		if( get_option('ec_option_use_visa') )
		echo "<option value=\"visa\">Visa</option>";
		
		if( get_option('ec_option_use_delta') )
		echo "<option value=\"delta\">Visa Debit/Delta</option>";
		
		if( get_option('ec_option_use_uke') )
		echo "<option value=\"uke\">Visa Electron</option>";
		
		if( get_option('ec_option_use_discover') )
		echo "<option value=\"discover\">Discover</option>";
		
		if( get_option('ec_option_use_mastercard') )
		echo "<option value=\"mastercard\">Mastercard</option>";
		
		if( get_option('ec_option_use_mcdebit') )
		echo "<option value=\"mcdebit\">Debit Mastercard</option>";
		
		if( get_option('ec_option_use_amex') )
		echo "<option value=\"amex\">American Express</option>";
		
		if( get_option('ec_option_use_jcb') )
		echo "<option value=\"jcb\">JCB</option>";
		
		if( get_option('ec_option_use_diners') )
		echo "<option value=\"diners\">Diners</option>";
		
		if( get_option('ec_option_use_laser') )
		echo "<option value=\"laser\">Laser</option>";
		
		if( get_option('ec_option_use_maestro') )
		echo "<option value=\"maestro\">Maestro</option>";
		
		echo "</select>";
	}
	
	public function ec_cart_display_card_holder_name_input(){
		echo "<input type=\"text\" name=\"ec_card_holder_name\" id=\"ec_card_holder_name\" class=\"ec_cart_payment_information_input_text\" value=\"\" />";
	}
	
	public function ec_cart_display_card_holder_name_hidden_input(){
		echo "<input type=\"hidden\" name=\"ec_card_holder_name\" id=\"ec_card_holder_name\" class=\"ec_cart_payment_information_input_text\" value=\"" . htmlspecialchars( $GLOBALS['ec_user']->billing->first_name, ENT_QUOTES ) . " " . htmlspecialchars( $GLOBALS['ec_user']->billing->last_name, ENT_QUOTES ) . "\" />";
	}
	
	public function ec_cart_display_card_number_input(){
		if( get_option( 'ec_option_payment_process_method' ) == "eway" && get_option( 'ec_option_eway_use_rapid_pay' ) ){
			echo "<input type=\"text\" name=\"ec_card_number\" data-eway-encrypt-name=\"ec_card_number\" id=\"ec_card_number\" class=\"ec_cart_payment_information_input_text\" value=\"\" autocomplete=\"off\" />";
		}else{
			echo "<input type=\"text\" name=\"ec_card_number\" id=\"ec_card_number\" class=\"ec_cart_payment_information_input_text\" value=\"\" autocomplete=\"off\" />";
		}
	}
	
	public function ec_cart_display_card_expiration_month_input( $select_text ){
		echo "<select name=\"ec_expiration_month\" id=\"ec_expiration_month\" class=\"ec_cart_payment_information_input_select no_wrap\" autocomplete=\"off\">";
		echo "<option value=\"0\">" . $select_text . "</option>";
		for( $i=1; $i<=12; $i++ ){
			echo "<option value=\"";
			if( $i<10 )										$month = "0" . $i;
			else											$month = $i;
			echo $month . "\">" . $month . "</option>";
		}
		echo "</select>";
	}
	
	public function ec_cart_display_card_expiration_year_input( $select_text ){
		echo "<select name=\"ec_expiration_year\" id=\"ec_expiration_year\" class=\"ec_cart_payment_information_input_select no_wrap\" autocomplete=\"off\">";
		echo "<option value=\"0\">" . $select_text . "</option>";
		for( $i=date( 'Y' ); $i < date( 'Y' ) + 15; $i++ ){
			echo "<option value=\"" . $i . "\">" . $i . "</option>";	
		}
		echo "</select>";
	}
	
	public function ec_cart_display_card_security_code_input(){
		if( get_option( 'ec_option_payment_process_method' ) == "eway" && get_option( 'ec_option_eway_use_rapid_pay' ) ){
			echo "<input type=\"text\" name=\"ec_security_code\" data-eway-encrypt-name=\"ec_security_code\" id=\"ec_security_code\" class=\"ec_cart_payment_information_input_text\" value=\"\" autocomplete=\"off\" />";
		}else{
			echo "<input type=\"text\" name=\"ec_security_code\" id=\"ec_security_code\" class=\"ec_cart_payment_information_input_text\" value=\"\" autocomplete=\"off\" />";
		}
	}
	/* END PAYMENT INFORMATION FUNCTIONS */
    
	/* START CONTACT INFORMATION FUNCTIONS */
    public function display_contact_information(){
		if(	$this->cart->total_items > 0 ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_contact_information.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_contact_information.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_contact_information.php' );
		}
	}
	
	public function ec_cart_display_contact_first_name_input(){
		if( $GLOBALS['ec_cart_data']->cart_data->first_name != "" )
			$first_name = $GLOBALS['ec_cart_data']->cart_data->first_name;
		else
			$first_name = $GLOBALS['ec_user']->first_name;
			
		if( $first_name == "guest" )
			$first_name = "";
			
		echo "<input type=\"text\" name=\"ec_contact_first_name\" id=\"ec_contact_first_name\" class=\"ec_cart_contact_information_input_text\" value=\"" . htmlspecialchars( $first_name, ENT_QUOTES ) . "\" />";
	}
	
	public function ec_cart_display_contact_last_name_input(){
		if( $GLOBALS['ec_cart_data']->cart_data->last_name != "" )
			$last_name = $GLOBALS['ec_cart_data']->cart_data->last_name;
		else
			$last_name = $GLOBALS['ec_user']->last_name;
			
		if( $last_name == "guest" )
			$last_name = "";
			
		echo "<input type=\"text\" name=\"ec_contact_last_name\" id=\"ec_contact_last_name\" class=\"ec_cart_contact_information_input_text\" value=\"" . htmlspecialchars( $last_name, ENT_QUOTES ) . "\" />";
	}
	
	public function ec_cart_display_contact_email_input(){
		if( $GLOBALS['ec_cart_data']->cart_data->email != "" )
			$email = $GLOBALS['ec_cart_data']->cart_data->email;
		else
			$email = $GLOBALS['ec_user']->email;
			
		if( $email == "guest" )
			$email = "";
			
		echo "<input type=\"text\" name=\"ec_contact_email\" id=\"ec_contact_email\" class=\"ec_cart_contact_information_input_text\" value=\"" . htmlspecialchars( $email, ENT_QUOTES ) . "\" />";
	}
	
	public function ec_cart_display_contact_email_retype_input(){
		if( $GLOBALS['ec_cart_data']->cart_data->email != "" )
			$email = $GLOBALS['ec_cart_data']->cart_data->email;
		else
			$email = $GLOBALS['ec_user']->email;
			
		if( $email == "guest" )
			$email = "";
			
		echo "<input type=\"text\" name=\"ec_contact_email_retype\" id=\"ec_contact_email_retype\" class=\"ec_cart_contact_information_input_text\" value=\"" . htmlspecialchars( $email, ENT_QUOTES ) . "\" />";
	}
	
	public function ec_cart_display_contact_create_account_box( ){
		echo "<input type=\"checkbox\" name=\"ec_contact_create_account\" id=\"ec_contact_create_account\" onchange=\"ec_contact_create_account_change( );\"";
		if( $GLOBALS['ec_cart_data']->cart_data->create_account != "" )
			echo " checked=\checked\"";
		echo " />";
		
		if( !get_option( 'ec_option_allow_guest' ) ){
			echo "<script>jQuery('#ec_contact_create_account').hide(); jQuery('#ec_contact_create_account').attr('checked', true);</script>";
		}
	}
	
	public function ec_cart_display_contact_password_input( ){
		echo "<input type=\"password\" name=\"ec_contact_password\" id=\"ec_contact_password\" class=\"ec_cart_contact_information_input_text\" />";
	}
	
	public function ec_cart_display_contact_password_retype_input( ){
		echo "<input type=\"password\" name=\"ec_contact_password_retype\" id=\"ec_contact_password_retype\" class=\"ec_cart_contact_information_input_text\" />";
	}
	
	public function ec_cart_display_contact_is_subscriber_input( ){
		echo "<input type=\"checkbox\" name=\"ec_contact_is_subscriber\" id=\"ec_contact_is_subscriber\" />";
	}
	/* END CONTACT INFORMATION FUNCTIONS */
	
	/* START SUBMIT ORDER DISPLAY FUNCTIONS */
    public function display_submit_order(){
		if(	$this->cart->total_items > 0 ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_submit_order.php' ) )	
				include( WP_PLUGIN_DIR . "/" . 'wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_submit_order.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_submit_order.php' );
		}
	}
	
	public function display_customer_order_notes( ){
		if( get_option( 'ec_option_user_order_notes' ) ){
			echo "<div class=\"ec_cart_payment_information_title\">" . $GLOBALS['language']->get_text( 'cart_payment_information', 'cart_payment_information_order_notes_title' ) . "</div>";
			echo "<div class=\"ec_cart_submit_order_message\">" . $GLOBALS['language']->get_text( 'cart_payment_information', 'cart_payment_information_order_notes_message' ) . "</div>";	
			echo "<div class=\"ec_cart_payment_information_row\"><textarea name=\"ec_order_notes\" id=\"ec_order_notes\">";
			if( $GLOBALS['ec_cart_data']->cart_data->order_notes != "" )
				echo $GLOBALS['ec_cart_data']->cart_data->order_notes;
			
			echo "</textarea></div><hr />";
		}
	}
	
	public function display_order_finalize_panel( ){
		if(	$this->cart->total_items > 0 ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_finalize_order.php' ) )	
				include( WP_PLUGIN_DIR . "/" . 'wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_finalize_order.php' );
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_finalize_order.php' );
		}
	}
	
	public function display_ajax_loader( $img ){
		echo "<img src=\"" .  plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ajax-loader.gif" ) . "\" class=\"ec_cart_final_loader\" />";
	}
	/* END SUBMIT ORDER DISPLAY FUNCTIONS */
	
	/* START SUCCESS PAGE FUNCTIONS */
	public function display_print_receipt_link( $link_text, $order_id ){
		if( substr_count( $this->account_page, '?' ) )				$permalink_divider = "&";
		else														$permalink_divider = "?";
		
		if( $GLOBALS['ec_cart_data']->cart_data->is_guest == "" ){
			echo "<a href=\"" . $this->account_page . $permalink_divider . "ec_page=print_receipt&order_id=" . $order_id . "\" target=\"_blank\">" . $link_text . "</a>";
		}else{
			echo "<a href=\"" . $this->account_page . $permalink_divider . "ec_page=print_receipt&order_id=" . $order_id . "&guest_key=" . $GLOBALS['ec_cart_data']->cart_data->guest_key . "\" target=\"_blank\">" . $link_text . "</a>";
		}
	}
	
	public function get_printer_icon( $image_name ){
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/images/' . $image_name ) )	
			return plugins_url( 'wp-easycart-data/design/theme/' . get_option( 'ec_option_base_themet' ) . '/images/' . $image_name );
		else
			return plugins_url( EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_latest_theme' ) . '/images/' . $image_name );
	}
	
	public function display_success_account_create_form_start( $order_id, $email ){
		echo "<form action=\"" . $this->account_page . $this->permalink_divider . "ec_page=order_details&order_id=" . $order_id . "\" method=\"POST\">";
		echo "<input type=\"hidden\" value=\"order_create_account\" name=\"ec_account_form_action\" />";
		echo "<input type=\"hidden\" value=\"" . $order_id . "\" name=\"order_id\" />";
		echo "<input type=\"hidden\" value=\"" . $email . "\" name=\"email_address\" />";
	}
	
	public function display_success_create_password( ){
		echo "<input type=\"password\" name=\"ec_password\" id=\"ec_password\" />";
	}
	
	public function display_success_verify_password( ){
		echo "<input type=\"password\" name=\"ec_verify_password\" id=\"ec_verify_password\" />";
	}
	
	public function display_success_account_create_submit_button( $button_text ){
		echo "<input type=\"submit\" value=\"" . $button_text . "\" onclick=\"return ec_check_success_passwords( );\" />";
	}
	
	public function display_success_account_create_form_end( ){
		echo "</form>";
	}
	/* END SUCCESS PAGE FUNCTIONS */
	
	/* START FORM PROCESSING FUNCTIONS */
	// Process the cart page form action
	public function process_form_action( $action ){
		wpeasycart_session( )->handle_session( );
		if( $action == "add_to_cart" )								$this->process_add_to_cart();
		else if( $action == "add_to_cart_v3" )						$this->process_add_to_cart_v3( );
		else if( $action == "ec_update_action" )					$this->process_update_cartitem( $_POST['ec_update_cartitem_id'], $_POST['ec_cartitem_quantity_' . $_POST['ec_update_cartitem_id'] ] );
		else if( $action == "ec_delete_action" )					$this->process_delete_cartitem( $_POST['ec_delete_cartitem_id'] );
		else if( $action == "submit_order" )						$this->process_submit_order();
		else if( $action == "3dsecure" )							$this->process_3dsecure_response();
		else if( $action == "3ds" )									$this->process_3ds_response();
		else if( $action == "3dsprocess" )							$this->process_3ds_final();
		else if( $action == "third_party_forward" )					$this->process_third_party_forward();
		else if( $action == "login_user" )							$this->process_login_user();
		else if( $action == "save_checkout_info" )					$this->process_save_checkout_info();
		else if( $action == "save_checkout_shipping" )				$this->process_save_checkout_shipping();
		else if( $action == "logout" )								$this->process_logout_user();
		else if( $action == "realex_redirect" )						$this->process_realex_redirect( );
		else if( $action == "realex_response" )						$this->process_realex_response( );
		else if( $action == "paymentexpress_thirdparty_response" )	$this->process_paymentexpress_thirdparty_response( );
		else if( $action == "purchase_subscription" )				$this->process_purchase_subscription( );
		else if( $action == "insert_subscription" )					$this->process_insert_subscription( );
		else if( $action == "send_inquiry" )						$this->process_send_inquiry( );
		else if( $action == "deconetwork_add_to_cart" )				$this->process_deconetwork_add_to_cart( );
		else if( $action == "subscribe_v3" )						$this->process_subscribe_v3( );
		else if( $action == "process_update_subscription_quantity" )$this->process_update_subscription_quantity( );
	}
	
	// Process the add to cart form submission
	private function process_add_to_cart(){
		
		if( !$this->check_quantity( $_POST['product_id'], $_POST['product_quantity'] ) ){
			header( "location: " . $this->store_page . $this->permalink_divider . "model_number=" . $_POST['model_number'] . "&ec_store_error=minquantity" );
			
		}else{
			
			//add_to_cart_replace Hook
			if( isset( $GLOBALS['ec_hooks']['add_to_cart_replace'] ) ){
				$class_args = array( "cart_page" => $this->cart_page, "permalink_divider" => $this->permalink_divider );
				for( $i=0; $i<count( $GLOBALS['ec_hooks']['add_to_cart_replace'] ); $i++ ){
					ec_call_hook( $GLOBALS['ec_hooks']['add_to_cart_replace'][$i], $class_args );
				}
			}else{
				//Product Info
				$session_id = $GLOBALS['ec_cart_data']->ec_cart_id;
				$product_id = $_POST['product_id'];
				if( isset( $_POST['product_quantity'] ) )
					$quantity = $_POST['product_quantity'];
				else
					$quantity = 1;
				
				$model_number = stripslashes( $_POST['model_number'] );
				
				//Optional Gift Card Info
				$gift_card_message = "";
				if( isset( $_POST['ec_gift_card_message'] ) )
					$gift_card_message = stripslashes( $_POST['ec_gift_card_message'] );
				
				$gift_card_to_name = "";
				if( isset( $_POST['ec_gift_card_to_name'] ) )
					$gift_card_to_name = stripslashes( $_POST['ec_gift_card_to_name'] );
				
				$gift_card_from_name = "";
				if( isset( $_POST['ec_gift_card_from_name'] ) )
					$gift_card_from_name = stripslashes( $_POST['ec_gift_card_from_name'] );
				
				// Optional Donation Price
				$donation_price = 0.000;
				if( isset( $_POST['ec_product_input_price'] ) )
					$donation_price = $_POST['ec_product_input_price'];
				
				$use_advanced_optionset = false;
				//Product Options
				if( isset( $_POST['ec_use_advanced_optionset'] ) && $_POST['ec_use_advanced_optionset'] ){
					$option1 = "";
					$option2 = "";
					$option3 = "";
					$option4 = "";
					$option5 = "";
					$use_advanced_optionset = true;
				}else{
					$option1 = "";
					if( isset( $_POST['ec_option1'] ) )
						$option1 = $_POST['ec_option1'];
					
					$option2 = "";
					if( isset( $_POST['ec_option2'] ) )
						$option2 = $_POST['ec_option2'];
					
					$option3 = "";
					if( isset( $_POST['ec_option3'] ) )
						$option3 = $_POST['ec_option3'];
					
					$option4 = "";
					if( isset( $_POST['ec_option4'] ) )
						$option4 = $_POST['ec_option4'];
					
					$option5 = "";
					if( isset( $_POST['ec_option5'] ) )
						$option5 = $_POST['ec_option5'];
						
				}
				
				$tempcart_id = $this->mysqli->add_to_cart( $product_id, $session_id, $quantity, $option1, $option2, $option3, $option4, $option5, $gift_card_message, $gift_card_to_name, $gift_card_from_name, $donation_price, $use_advanced_optionset, false );
				
				$option_vals = array( );
				// Now insert the advanced option set tempcart table if needed
				if( $use_advanced_optionset ){
					
					$optionsets = $GLOBALS['ec_advanced_optionsets']->get_advanced_optionsets( $product_id );
					$grid_quantity = 0;
					
					foreach( $optionsets as $optionset ){
						if( $optionset->option_type == "checkbox" ){
							$optionitems = $this->mysqli->get_advanced_optionitems( $optionset->option_id );
							foreach( $optionitems as $optionitem ){
								if( isset( $_POST['ec_option_' . $optionset->option_id . "_" . $optionitem->optionitem_id] ) ){
									$option_vals[] = array( "option_id" => $optionset->option_id, "optionitem_id" => $optionitem->optionitem_id, "option_name" => $optionitem->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionitem->option_type, "optionitem_value" => stripslashes( $_POST['ec_option_' . $optionset->option_id . "_" . $optionitem->optionitem_id] ), "optionitem_model_number" => $optionitem->optionitem_model_number );
								}
							}
						}else if( $optionset->option_type == "grid" ){
							$optionitems = $this->mysqli->get_advanced_optionitems( $optionset->option_id );
							foreach( $optionitems as $optionitem ){
								if( isset( $_POST['ec_option_' . $optionset->option_id . "_" . $optionitem->optionitem_id] ) && $_POST['ec_option_' . $optionset->option_id . "_" . $optionitem->optionitem_id] > 0 ){
									$grid_quantity = $grid_quantity + $_POST['ec_option_' . $optionset->option_id . "_" . $optionitem->optionitem_id];
									$option_vals[] = array( "option_id" => $optionset->option_id, "optionitem_id" => $optionitem->optionitem_id, "option_name" => $optionitem->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionitem->option_type, "optionitem_value" => $_POST['ec_option_' . $optionset->option_id . "_" . $optionitem->optionitem_id], "optionitem_model_number" => $optionitem->optionitem_model_number );
								}
							}
						}else if( $optionset->option_type == "combo" || $optionset->option_type == "swatch" || $optionset->option_type == "radio" ){
							$optionitems = $this->mysqli->get_advanced_optionitems( $optionset->option_id );
							foreach( $optionitems as $optionitem ){
								if( $optionitem->optionitem_id == $_POST['ec_option_' . $optionset->option_id] ){
									$option_vals[] = array( "option_id" => $optionset->option_id, "optionitem_id" => $optionitem->optionitem_id, "option_name" => $optionitem->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionitem->option_type, "optionitem_value" => $optionitem->optionitem_name, "optionitem_model_number" => $optionitem->optionitem_model_number );
								}
							}
						}else if( $optionset->option_type == "file" ){
							$optionitems = $this->mysqli->get_advanced_optionitems( $optionset->option_id );
							foreach( $optionitems as $optionitem ){
								$option_vals[] = array( "option_id" => $optionset->option_id, "optionitem_id" => $optionitem->optionitem_id, "option_name" => $optionitem->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionitem->option_type, "optionitem_value" => $_FILES['ec_option_' . $optionset->option_id]['name'], "optionitem_model_number" => $optionitem->optionitem_model_number );
							}
						}else{
							$optionitems = $this->mysqli->get_advanced_optionitems( $optionset->option_id );
							foreach( $optionitems as $optionitem ){
								$option_vals[] = array( "option_id" => $optionset->option_id, "optionitem_id" => $optionitem->optionitem_id, "option_name" => $optionitem->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionitem->option_type, "optionitem_value" => stripslashes( $_POST['ec_option_' . $optionset->option_id] ), "optionitem_model_number" => $optionitem->optionitem_model_number );
							}
						}
						
						if( $optionset->option_type == "file" ){
							//upload the file
							$this->upload_customer_file( $tempcart_id, 'ec_option_' . $optionset->option_id );
						}
					}
				}
				
				for( $i=0; $i<count( $option_vals ); $i++ ){
					$this->mysqli->add_option_to_cart( $tempcart_id, $GLOBALS['ec_cart_data']->ec_cart_id, $option_vals[$i] );
				}
				
				if( $grid_quantity > 0 ){
					$this->mysqli->update_tempcart_grid_quantity( $tempcart_id, $grid_quantity );
				}
				
				if( get_option( 'ec_option_addtocart_return_to_product' ) ){
					$return_url = $_SERVER['HTTP_REFERER'];
					$return_url = str_replace( "ec_store_success=addtocart", "", $return_url );
					$divider = "?";
					if( substr_count( $return_url, '?' ) )
						$divider = "&";
					
					do_action( 'wpeasycart_cart_updated' );
					
					
					header( "location: " . $return_url . $divider . "ec_store_success=addtocart&model=" . $_POST['model_number'] );
				}else{
					header( "location: " . $this->cart_page );
				}
			}
		}
	}
	
	private function send_inquiry( $product ){
			
		$inquiry_name = "";
		$inquiry_email = "";
		$inquiry_message = "";
		$send_copy = false;
		$has_product_options = false;
		
		if( isset( $_POST['ec_inquiry_name'] ) )			$inquiry_name = stripslashes( $_POST['ec_inquiry_name'] );
		if( isset( $_POST['ec_inquiry_email'] ) )			$inquiry_email = filter_var( stripslashes( $_POST['ec_inquiry_email'] ), FILTER_SANITIZE_EMAIL );
		if( isset( $_POST['ec_inquiry_message'] ) )			$inquiry_message = stripslashes( $_POST['ec_inquiry_message'] );
		if( isset( $_POST['ec_inquiry_send_copy'] ) )		$send_copy = true;
		
		//Product Options
		$option1 = $option2 = $option3 = $option4 = $option5 = "";
		$optionitem_list = $GLOBALS['ec_options']->optionitems;
		
		if( !$product->use_advanced_optionset ){
			
			if( isset( $_POST['ec_option1'] ) )				$option1 = $_POST['ec_option1'];
			if( isset( $_POST['ec_option2'] ) )				$option2 = $_POST['ec_option2'];
			if( isset( $_POST['ec_option3'] ) )				$option3 = $_POST['ec_option3'];
			if( isset( $_POST['ec_option4'] ) )				$option4 = $_POST['ec_option4'];
			if( isset( $_POST['ec_option5'] ) )				$option5 = $_POST['ec_option5'];
			
			if( isset( $_POST['ec_option1'] ) || isset( $_POST['ec_option2'] ) || isset( $_POST['ec_option3'] ) || isset( $_POST['ec_option4'] ) || isset( $_POST['ec_option5'] ) ){
				$has_product_options = true;
			}
			
		}
		
		foreach( $optionitem_list as $optionitem ){
			if( $option1 == $optionitem->optionitem_id ){
				$option1 = $optionitem->optionitem_name;
			}else if( $option2 == $optionitem->optionitem_id ){
				$option2 = $optionitem->optionitem_name;
			}else if( $option3 == $optionitem->optionitem_id ){
				$option3 = $optionitem->optionitem_name;
			}else if( $option4 == $optionitem->optionitem_id ){
				$option4 = $optionitem->optionitem_name;
			}else if( $option5 == $optionitem->optionitem_id ){
				$option5 = $optionitem->optionitem_name;
			}
		}
		
		if( $product->use_advanced_optionset ){
			$tempcart_id = rand( 0, 99999999 );
			$option_vals = $this->get_advanced_option_vals( $product->product_id, $tempcart_id );
		}
		
		// send inquiry
		// Create mail script
		$email_logo_url = get_option( 'ec_option_email_logo' ) . "' alt='" . get_bloginfo( "name" );
		
		$headers   = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-Type: text/html; charset=utf-8";
		$headers[] = "From: " . stripslashes( get_option( 'ec_option_password_from_email' ) );
		$headers[] = "Reply-To: " . $inquiry_email;
		$headers[] = "X-Mailer: PHP/".phpversion();
		
		ob_start();
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_inquiry_email.php' ) )	
			include WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_inquiry_email.php';	
		else
			include WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_inquiry_email.php';
		$message = ob_get_clean();
		
		if( get_option( 'ec_option_use_wp_mail' ) ){
			if( $send_copy )
				wp_mail( $inquiry_email, "New Product Inquiry", $message, implode("\r\n", $headers) );
			
			wp_mail( stripslashes( get_option( 'ec_option_bcc_email_addresses' ) ), "New Product Inquiry", $message, implode("\r\n", $headers) );
		}else{
			$admin_email = stripslashes( get_option( 'ec_option_bcc_email_addresses' ) );
			$to = $inquiry_email;
			$subject = "New Product Inquiry";
			$mailer = new wpeasycart_mailer( );
			if( $send_copy )
				$mailer->send_order_email( $to, $subject, $message );
			$mailer->send_order_email( $admin_email, $subject, $message );
		}
		
		header( "location: " . $this->store_page . $this->permalink_divider . "model_number=" . $product->model_number . "&ec_store_success=inquiry_sent" );
		// return to previous url with success message.
			
	}
	
	private function get_advanced_option_vals( $product_id, $tempcart_id ){
		
		$option_vals = array( );
		$optionsets = $GLOBALS['ec_advanced_optionsets']->get_advanced_optionsets( $product_id );
		$grid_quantity = 0;
		
		foreach( $optionsets as $optionset ){
			
			$optionitems = $optionset->option_items;
			
			if( $optionset->option_type == "checkbox" ){
				foreach( $optionitems as $optionitem ){
					if( isset( $_POST['ec_option_' . $optionset->option_id . "_" . $optionitem->optionitem_id] ) ){
						$option_vals[] = array( "option_id" => $optionset->option_id, "option_label" => $optionset->option_label, "option_name" => $optionset->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionset->option_type, "optionitem_id" => $optionitem->optionitem_id, "optionitem_value" => stripslashes( $_POST['ec_option_' . $optionset->option_id . "_" . $optionitem->optionitem_id] ), "optionitem_model_number" => $optionitem->optionitem_model_number );
					
					}else if( isset( $_POST['ec_option_adv_' . $optionset->option_to_product_id . "_" . $optionitem->optionitem_id] ) ){
						$option_vals[] = array( "option_id" => $optionset->option_id, "option_label" => $optionset->option_label, "option_name" => $optionset->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionset->option_type, "optionitem_id" => $optionitem->optionitem_id, "optionitem_value" => stripslashes( $_POST['ec_option_adv_' . $optionset->option_to_product_id . "_" . $optionitem->optionitem_id] ), "optionitem_model_number" => $optionitem->optionitem_model_number );
					}
				}
			
			}else if( $optionset->option_type == "grid" ){
				foreach( $optionitems as $optionitem ){
					if( isset( $_POST['ec_option_' . $optionset->option_id . "_" . $optionitem->optionitem_id] ) && $_POST['ec_option_' . $optionset->option_id . "_" . $optionitem->optionitem_id] > 0 ){
						$grid_quantity = $grid_quantity + $_POST['ec_option_' . $optionset->option_id . "_" . $optionitem->optionitem_id];
						$option_vals[] = array( "option_id" => $optionset->option_id, "option_label" => $optionset->option_label, "option_name" => $optionset->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionset->option_type, "optionitem_id" => $optionitem->optionitem_id, "optionitem_value" => $_POST['ec_option_' . $optionset->option_id . "_" . $optionitem->optionitem_id], "optionitem_model_number" => $optionitem->optionitem_model_number );
					
					}else if( isset( $_POST['ec_option_adv_' . $optionset->option_to_product_id . "_" . $optionitem->optionitem_id] ) && $_POST['ec_option_adv_' . $optionset->option_to_product_id . "_" . $optionitem->optionitem_id] > 0 ){
						$grid_quantity = $grid_quantity + $_POST['ec_option_adv_' . $optionset->option_to_product_id . "_" . $optionitem->optionitem_id];
						$option_vals[] = array( "option_id" => $optionset->option_id, "option_label" => $optionset->option_label, "option_name" => $optionset->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionset->option_type, "optionitem_id" => $optionitem->optionitem_id, "optionitem_value" => $_POST['ec_option_adv_' . $optionset->option_to_product_id . "_" . $optionitem->optionitem_id], "optionitem_model_number" => $optionitem->optionitem_model_number );
					}
				}
			
			}else if( $optionset->option_type == "combo" || $optionset->option_type == "swatch" || $optionset->option_type == "radio" ){
				foreach( $optionitems as $optionitem ){
					if( $optionitem->optionitem_id == $_POST['ec_option_' . $optionset->option_id] ){
						$option_vals[] = array( "option_id" => $optionset->option_id, "option_label" => $optionset->option_label, "option_name" => $optionset->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionset->option_type, "optionitem_id" => $optionitem->optionitem_id, "optionitem_value" => $optionitem->optionitem_name, "optionitem_model_number" => $optionitem->optionitem_model_number );
					
					}else if( $optionitem->optionitem_id == $_POST['ec_option_adv_' . $optionset->option_to_product_id] ){
						$option_vals[] = array( "option_id" => $optionset->option_id, "option_label" => $optionset->option_label, "option_name" => $optionset->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionset->option_type, "optionitem_id" => $optionitem->optionitem_id, "optionitem_value" => $optionitem->optionitem_name, "optionitem_model_number" => $optionitem->optionitem_model_number );
					}
				}
			
			}else if( $optionset->option_type == "file" ){
				foreach( $optionitems as $optionitem ){
					if( isset( $_FILES['ec_option_' . $optionset->option_id] ) ){
						$option_vals[] = array( "option_id" => $optionset->option_id, "option_label" => $optionset->option_label, "option_name" => $optionset->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionset->option_type, "optionitem_id" => $optionitem->optionitem_id, "optionitem_value" => $_FILES['ec_option_' . $optionset->option_id]['name'], "optionitem_model_number" => $optionitem->optionitem_model_number );
					
					}else if( isset( $_FILES['ec_option_adv_' . $optionset->option_to_product_id] ) ){
						$option_vals[] = array( "option_id" => $optionset->option_id, "option_label" => $optionset->option_label, "option_name" => $optionset->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionset->option_type, "optionitem_id" => $optionitem->optionitem_id, "optionitem_value" => $_FILES['ec_option_adv_' . $optionset->option_to_product_id]['name'], "optionitem_model_number" => $optionitem->optionitem_model_number );
					}
				}
			
			}else if( $optionset->option_type == "dimensions1" || $optionset->option_type == "dimensions2" ){
				foreach( $optionitems as $optionitem ){
					
					if( isset( $_POST['ec_option_' . $optionset->option_id . '_width'] ) ){
						$vals = array( );
						$vals[] = $_POST['ec_option_' . $optionset->option_id . '_width'];
						
						if( isset( $_POST['ec_option_' . $optionset->option_id . '_sub_width'] ) ){
							$vals[] = $_POST['ec_option_' . $optionset->option_id . '_sub_width'];
							
						}
						
						$vals[] = $_POST['ec_option_' . $optionset->option_id . '_height'];
						
						if( isset( $_POST['ec_option_' . $optionset->option_id . '_sub_height'] ) ){
							$vals[] = $_POST['ec_option_' . $optionset->option_id . '_sub_height'];
							
						}
						
						$option_vals[] = array( "option_id" => $optionset->option_id, "option_label" => $optionset->option_label, "option_name" => $optionset->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionset->option_type, "optionitem_id" => $optionitem->optionitem_id, "optionitem_value" => json_encode( $vals ), "optionitem_model_number" => $optionitem->optionitem_model_number );
					
					}else if( isset( $_POST['ec_option_adv_' . $optionset->option_to_product_id . '_width'] ) ){
						$vals = array( );
						$vals[] = $_POST['ec_option_adv_' . $optionset->option_to_product_id . '_width'];
						
						if( isset( $_POST['ec_option_adv_' . $optionset->option_to_product_id . '_sub_width'] ) ){
							$vals[] = $_POST['ec_option_adv_' . $optionset->option_to_product_id . '_sub_width'];
							
						}
						
						$vals[] = $_POST['ec_option_adv_' . $optionset->option_to_product_id . '_height'];
						
						if( isset( $_POST['ec_option_adv_' . $optionset->option_to_product_id . '_sub_height'] ) ){
							$vals[] = $_POST['ec_option_adv_' . $optionset->option_to_product_id . '_sub_height'];
							
						}
						
						$option_vals[] = array( "option_id" => $optionset->option_id, "option_label" => $optionset->option_label, "option_name" => $optionset->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionset->option_type, "optionitem_id" => $optionitem->optionitem_id, "optionitem_value" => json_encode( $vals ), "optionitem_model_number" => $optionitem->optionitem_model_number );
					
					}
				}
				
			}else{
				foreach( $optionitems as $optionitem ){
					if( isset( $_POST['ec_option_' . $optionset->option_id] ) ){
						$option_vals[] = array( "option_id" => $optionset->option_id, "option_label" => $optionset->option_label, "option_name" => $optionset->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionset->option_type, "optionitem_id" => $optionitem->optionitem_id, "optionitem_value" => stripslashes( $_POST['ec_option_' . $optionset->option_id] ), "optionitem_model_number" => $optionitem->optionitem_model_number );
					
					}else if( isset( $_POST['ec_option_adv_' . $optionset->option_to_product_id] ) ){
						$option_vals[] = array( "option_id" => $optionset->option_id, "option_label" => $optionset->option_label, "option_name" => $optionset->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionset->option_type, "optionitem_id" => $optionitem->optionitem_id, "optionitem_value" => stripslashes( $_POST['ec_option_adv_' . $optionset->option_to_product_id] ), "optionitem_model_number" => $optionitem->optionitem_model_number );
					}
				}
			}
			
			if( $optionset->option_type == "file" ){
				if( isset( $_FILES['ec_option_' . $optionset->option_id] ) ){
					$this->upload_customer_file( $tempcart_id, 'ec_option_' . $optionset->option_id );
				
				}else if( isset( $_FILES['ec_option_adv_' . $optionset->option_to_product_id] ) ){
					$this->upload_customer_file( $tempcart_id, 'ec_option_adv_' . $optionset->option_to_product_id );
				}
			}
		}
		return $option_vals;
		
	}
	
	private function get_grid_quantity( $product_id, $tempcart_id ){
		
		$optionsets = $GLOBALS['ec_advanced_optionsets']->get_advanced_optionsets( $product_id );
		$grid_quantity = 0;
		foreach( $optionsets as $optionset ){
			
			if( $optionset->option_type == "grid" ){
				$optionitems = $this->mysqli->get_advanced_optionitems( $optionset->option_id );
				foreach( $optionitems as $optionitem ){
					if( isset( $_POST['ec_option_' . $optionset->option_id . "_" . $optionitem->optionitem_id] ) && $_POST['ec_option_' . $optionset->option_id . "_" . $optionitem->optionitem_id] > 0 ){
						$grid_quantity = $grid_quantity + $_POST['ec_option_' . $optionset->option_id . "_" . $optionitem->optionitem_id];
					
					}else if( isset( $_POST['ec_option_adv_' . $optionset->option_to_product_id . "_" . $optionitem->optionitem_id] ) && $_POST['ec_option_adv_' . $optionset->option_to_product_id . "_" . $optionitem->optionitem_id] > 0 ){
						$grid_quantity = $grid_quantity + $_POST['ec_option_adv_' . $optionset->option_to_product_id . "_" . $optionitem->optionitem_id];
					}
				}
			}
		}
		return $grid_quantity;
		
	}
	
	private function process_add_to_cart_v3( ){
		
		$cart_id = $GLOBALS['ec_cart_data']->ec_cart_id;
		$product_id = $_POST['product_id'];
		
		$product = $this->mysqli->get_product( "", $product_id );
		
		if( $product->inquiry_mode ){
			
			$this->send_inquiry( $product );
			
		}else if( $product->is_subscription_item ){ // && !class_exists( "ec_stripe" ) ){
			
		}else{
		
			if( isset( $_POST['ec_quantity'] ) )				$quantity = $_POST['ec_quantity'];
			else												$quantity = 1;
			
			//Optional Gift Card Info
			$gift_card_message = "";
			if( isset( $_POST['ec_giftcard_message'] ) )		$gift_card_message = stripslashes( $_POST['ec_giftcard_message'] );
			
			$gift_card_to_name = "";
			if( isset( $_POST['ec_giftcard_to_name'] ) )		$gift_card_to_name = stripslashes( $_POST['ec_giftcard_to_name'] );
			
			$gift_card_from_name = "";
			if( isset( $_POST['ec_giftcard_from_name'] ) )		$gift_card_from_name = stripslashes( $_POST['ec_giftcard_from_name'] );
			
			$gift_card_email = "";
			if( isset( $_POST['ec_giftcard_to_email'] ) )		$gift_card_email = stripslashes( $_POST['ec_giftcard_to_email'] );
			
			// Optional Donation Price
			$donation_price = 0.000;
			if( isset( $_POST['ec_donation_amount'] ) )			$donation_price = $_POST['ec_donation_amount'];
			
			$use_advanced_optionset = 							$product->use_advanced_optionset;
			
			//Product Options
			$option1 = $option2 = $option3 = $option4 = $option5 = "";
			if( !$use_advanced_optionset ){
				
				if( isset( $_POST['ec_option1'] ) )				$option1 = $_POST['ec_option1'];
				if( isset( $_POST['ec_option2'] ) )				$option2 = $_POST['ec_option2'];
				if( isset( $_POST['ec_option3'] ) )				$option3 = $_POST['ec_option3'];
				if( isset( $_POST['ec_option4'] ) )				$option4 = $_POST['ec_option4'];
				if( isset( $_POST['ec_option5'] ) )				$option5 = $_POST['ec_option5'];
				
			}
			
			$tempcart_id = $this->mysqli->add_to_cart( $product_id, $cart_id, $quantity, $option1, $option2, $option3, $option4, $option5, $gift_card_message, $gift_card_to_name, $gift_card_from_name, $donation_price, $use_advanced_optionset, false, $gift_card_email );
			
			// Now insert the advanced option set tempcart table if needed
			if( $use_advanced_optionset ){
				
				$option_vals = $this->get_advanced_option_vals( $product_id, $tempcart_id );
				$grid_quantity = $this->get_grid_quantity( $product_id, $tempcart_id );
			
				for( $i=0; $i<count( $option_vals ); $i++ ){
					$this->mysqli->add_option_to_cart( $tempcart_id, $cart_id, $option_vals[$i] );
				}
				
				if( $grid_quantity > 0 ){
					$this->mysqli->update_tempcart_grid_quantity( $tempcart_id, $grid_quantity );
				}
				
			}
			
			do_action( 'wpeasycart_item_added_to_cart', $tempcart_id, $cart_id );
			do_action( 'wpeasycart_cart_updated' );
			
			if( get_option( 'ec_option_addtocart_return_to_product' ) ){
				$return_url = $_SERVER['HTTP_REFERER'];
				$return_url = str_replace( "ec_store_success=addtocart", "", $return_url );
				$divider = "?";
				if( substr_count( $return_url, '?' ) )
					$divider = "&";
				
				header( "location: " . $return_url . $divider . "ec_store_success=addtocart&model=" . $product->model_number );
			
			}else{
				header( "location: " . $this->cart_page );
			
			}
			
		}
		
	}
	
	private function check_quantity( $product_id, $quantity ){
		
		global $wpdb;
		$min_quantity = $wpdb->get_var( $wpdb->prepare( "SELECT ec_product.min_purchase_quantity FROM ec_product WHERE ec_product.product_id = %d", $product_id ) );
		
		if( $min_quantity > 0 ){
			$current_amount = $quantity;
			foreach( $this->cart->cart as $cartitem ){
				if( $cartitem->product_id == $product_id ){
					$current_amount = $current_amount + $cartitem->quantity;
				}
			}
			
			if( $min_quantity <= $current_amount ){
				return true;
				
			}else{
				return false;
				
			}
		
		
		}else{
			return true;
		}
		
	}
	
	private function process_update_cartitem( $cartitem_id, $new_quantity ){
		$this->mysqli->update_cartitem( $cartitem_id, $GLOBALS['ec_cart_data']->ec_cart_id, $new_quantity );
		
		do_action( 'wpeasycart_cart_updated' );
		
		if( isset( $_GET['ec_page'] ) )
			header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=" . htmlspecialchars( $_GET['ec_page'], ENT_QUOTES ) );	
		else
			header( "location: " . $this->cart_page );
	}
	
	private function process_delete_cartitem( $cartitem_id ){
		$this->mysqli->delete_cartitem( $cartitem_id, $GLOBALS['ec_cart_data']->ec_cart_id );
		
		do_action( 'wpeasycart_cart_updated' );
		
		if( isset( $_GET['ec_page'] ) )
			header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=" . htmlspecialchars( $_GET['ec_page'], ENT_QUOTES ) );	
		else
			header( "location: " . $this->cart_page );
	}
	
	private function validate_submit_order_data( ){
		
		$data_validated = true;
		
		// Basic Validation
		if( $GLOBALS['ec_cart_data']->cart_data->billing_country == "0" || $GLOBALS['ec_cart_data']->cart_data->billing_first_name == "" || $GLOBALS['ec_cart_data']->cart_data->billing_last_name == "" || $GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 == "" || $GLOBALS['ec_cart_data']->cart_data->billing_city == "" || $GLOBALS['ec_cart_data']->cart_data->email == "" ){
			$data_validated =  false;
			
		}
		
		$data_validated = apply_filters( 'wpeasycart_validate_submit_order_data', $data_validated, $GLOBALS['ec_user'] );
		
		return $data_validated;
		
	}
	
	private function validate_checkout_data( ){
		
		$data_validated = true;
		
		// Basic Validation
		if( $GLOBALS['ec_cart_data']->cart_data->billing_country == "0" || $GLOBALS['ec_cart_data']->cart_data->billing_first_name == "" || $GLOBALS['ec_cart_data']->cart_data->billing_last_name == "" || $GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 == "" || $GLOBALS['ec_cart_data']->cart_data->billing_city == "" || $GLOBALS['ec_cart_data']->cart_data->email == "" ){
			$data_validated =  false;
			
		}
		
		$data_validated = apply_filters( 'wpeasycart_validate_checkout_data', $data_validated, $GLOBALS['ec_user'] );
		
		return $data_validated;
		
	}
	
	private function validate_tax_cloud( ){
		
		if( $GLOBALS['ec_cart_data']->cart_data->shipping_country == "US" && get_option( 'ec_option_tax_cloud_api_id' ) != "" && get_option( 'ec_option_tax_cloud_api_key' ) != "" ){
			
			return $GLOBALS['ec_cart_data']->cart_data->taxcloud_address_verified;
			
		}else{
		
			return true;
		}
		
	}
	
	private function validate_vat_registration_number( $vat_number ){
		
		// Validate with vatlayer
		if( $vat_number != "" && get_option( 'ec_option_collect_vat_registration_number' ) && get_option( 'ec_option_validate_vat_registration_number' ) && get_option( 'ec_option_vatlayer_api_key' ) != "" ){
			// set API Endpoint and Access Key
			$endpoint = 'validate';
			$access_key = get_option( 'ec_option_vatlayer_api_key' );
			
			// Initialize CURL:
			$ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&vat_number='.$vat_number.'');  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			// Store the data:
			$json = curl_exec($ch);
			curl_close($ch);
			
			// Decode JSON response:
			$validationResult = json_decode($json, true);
			
			// Access and use your preferred validation result objects
			$validationResult['valid'];
			$validationResult['query'];
			$validationResult['company_name'];
			$validationResult['company_address'];
			
			if( $validationResult['valid'] == "true" ){
				return true;
			}else{
				return false;
			}
			
		// No validation required
		}else{
			return true;
		}
		
	}
	
	public function get_paypal_express_button_code( $is_payment_page = false ){
		ob_start( );
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_paypal_button_code.php' ) ){
			include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_paypal_button_code.php' );
		}else{
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_paypal_button_code.php' );
		}
		return ob_get_clean( );
	}
	
	public function get_paypal_express_button_code_order( $is_payment_page = false ){
		ob_start( );
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_paypal_button_code_order.php' ) ){
			include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_paypal_button_code_order.php' );
		}else{
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_paypal_button_code_order.php' );
		}
		return ob_get_clean( );
	}
    
    public function submit_square_quick_payment( $nonce, $card_type, $last_4, $exp_month, $exp_year ){
        global $wpdb;
		$ec_db_admin = new ec_db_admin( );
		$response = $this->order->submit_order( "credit_card" );
		if( $response ){
            $wpdb->query( $wpdb->prepare( "UPDATE ec_order SET card_holder_name = %s, payment_method = %s, creditcard_digits = %s, cc_exp_month = %s, cc_exp_year = %s WHERE order_id = %d", $GLOBALS['ec_cart_data']->cart_data->billing_first_name . ' ' . $GLOBALS['ec_cart_data']->cart_data->billing_last_name, $card_type, $last_4, $exp_month, $exp_year, $this->order->order_id ) );
            do_action( 'wpeasycart_submit_order_complete' );
            return $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order->order_id;
        
        }else{
            return '0';
            
        }
    }
	
	public function submit_stripe_quick_payment( $payment_id, $card_type, $last_4, $exp_month, $exp_year ){
		global $wpdb;
		$ec_db_admin = new ec_db_admin( );
		
		if( isset( $_POST['ec_cart_is_subscriber'] ) ){
			$first_name = $GLOBALS['ec_cart_data']->cart_data->billing_first_name;
			$last_name = $GLOBALS['ec_cart_data']->cart_data->billing_last_name;
			$email = $GLOBALS['ec_cart_data']->cart_data->email;
			
			$this->mysqli->insert_subscriber( $email, $first_name, $last_name );
			
			if( $GLOBALS['ec_user']->user_id ){
				global $wpdb;
				$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET is_subscriber = 1 WHERE ec_user.user_id = %d", $GLOBALS['ec_user']->user_id ) );
			}
			
			// MyMail Hook
			if( function_exists( 'mailster' ) ){
				$subscriber_id = mailster('subscribers')->add(array(
					'firstname' => $first_name,
					'lastname' => $last_name,
					'email' => $email,
					'status' => 1,
				), false );
			}
		}
		
		$this->order->submit_order( "third_party" );
		
		// Verify Payment Status in Case Already Successful
		$order_status = 12;
		if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
			$stripe = new ec_stripe( );
		else
			$stripe = new ec_stripe_connect( );
			
		$stripe->initialize( $this->cart, $this->user, $this->shipping, $this->tax, $this->discount, $this->payment->credit_card, $this->order_totals, $this->order->order_id );
        
		// Maybe create customer
		if( get_option( 'ec_option_stripe_order_create_customer' ) && $this->user->user_id != 0 && $this->user->stripe_customer_id == "" ){
			$customer_id = $stripe->insert_quick_customer( $payment_id );
			$ec_db_admin->update_user_stripe_id( $this->user->user_id, $customer_id );
		
		}else if( get_option( 'ec_option_stripe_order_create_customer' ) && $this->user->stripe_customer_id == "" ){
			$stripe->insert_guest_customer( $payment_id, $this->order->order_id );
		
		}else if( get_option( 'ec_option_stripe_order_create_customer' ) ){
			$payment_intent = $stripe->get_payment_intent( $payment_id );
			$stripe->attach_payment_method( $payment_intent->payment_method, $this->user );
			
		}
			
		// Set Order ID and Confirm Current Payment Status
		$payment_status = $stripe->update_payment_intent_description( $payment_id, $this->order->order_id );
		if( $payment_status && $payment_status->status == 'succeeded' ){
			$order_status = 3;
		}else if( $payment_status->status == 'canceled' ){
			$order_status = 19;
		}
		
		// Get Charge ID
		$stripe_charge_id = '';
		if( isset( $payment_status->charges ) && isset( $payment_status->charges->data ) && count( $payment_status->charges->data ) > 0 ){
			$stripe_charge_id = $payment_status->charges->data[0]->id;
		}
		
		// Update Order
		$wpdb->query( $wpdb->prepare( "UPDATE ec_order SET order_gateway = %s, creditcard_digits = %s, cc_exp_month = %s, cc_exp_year = %s, stripe_charge_id = %s, gateway_transaction_id = %s, payment_method = %s, orderstatus_id = %d WHERE order_id = %d", get_option( 'ec_option_payment_process_method' ), $last_4, $exp_month, $exp_year, $stripe_charge_id, $payment_id, $card_type, $order_status, $this->order->order_id ) );
		
		// Clear tempcart
		$ec_db_admin->clear_tempcart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$this->order->clear_session( );
		
		// Maybe send email receipts
		if( $order_status == 3 ){
			$order_row = $ec_db_admin->get_order_row_admin( $this->order->order_id );
			$orderdetails = $ec_db_admin->get_order_details_admin( $this->order->order_id );
			
			/* Update Stock Quantity */
            foreach( $orderdetails as $orderdetail ){
                $product = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.* FROM ec_product WHERE ec_product.product_id = %d", $orderdetail->product_id ) );
                if( $product ){
                    if( $product->use_optionitem_quantity_tracking )	
                        $ec_db_admin->update_quantity_value( $orderdetail->quantity, $orderdetail->product_id, $orderdetail->optionitem_id_1, $orderdetail->optionitem_id_2, $orderdetail->optionitem_id_3, $orderdetail->optionitem_id_4, $orderdetail->optionitem_id_5 );
                    $ec_db_admin->update_product_stock( $orderdetail->product_id, $orderdetail->quantity );
                    $this->mysqli->update_details_stock_adjusted( $orderdetail->orderdetail_id );
                }
            }

            // Update Order Status/Send Alerts
            do_action( 'wpeasycart_order_paid', $this->order->order_id );
            
			// send email
			$order_display = new ec_orderdisplay( $order_row, true, true );
			$order_display->send_email_receipt( );
			$order_display->send_gift_cards( );
		}
		
		$GLOBALS['ec_cart_data']->save_session_to_db( );
		
		return $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order->order_id;
	}
	
	public function submit_stripe_invoice_payment( $payment_id, $card_type, $last_4, $exp_month, $exp_year ){
		global $wpdb;
		$ec_db_admin = new ec_db_admin( );
		
		if( isset( $_POST['ec_cart_is_subscriber'] ) ){
			$first_name = $GLOBALS['ec_cart_data']->cart_data->billing_first_name;
			$last_name = $GLOBALS['ec_cart_data']->cart_data->billing_last_name;
			$email = $GLOBALS['ec_cart_data']->cart_data->email;
			
			$this->mysqli->insert_subscriber( $email, $first_name, $last_name );
			
			if( $GLOBALS['ec_user']->user_id ){
				global $wpdb;
				$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET is_subscriber = 1 WHERE ec_user.user_id = %d", $GLOBALS['ec_user']->user_id ) );
			}
			
			// MyMail Hook
			if( function_exists( 'mailster' ) ){
				$subscriber_id = mailster('subscribers')->add(array(
					'firstname' => $first_name,
					'lastname' => $last_name,
					'email' => $email,
					'status' => 1,
				), false );
			}
		}
		
		// Verify Payment Status in Case Already Successful
		$order_status = 12;
		if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
			$stripe = new ec_stripe( );
		else
			$stripe = new ec_stripe_connect( );
			
		// Maybe create customer
		if( get_option( 'ec_option_stripe_order_create_customer' ) && $this->user->user_id != 0 && $this->user->stripe_customer_id == "" ){
			$customer_id = $stripe->insert_quick_customer( $payment_id );
			$ec_db_admin->update_user_stripe_id( $this->user->user_id, $customer_id );
		
		}else if( get_option( 'ec_option_stripe_order_create_customer' ) && $this->user->stripe_customer_id == "" ){
			$stripe->insert_guest_customer( $payment_id, (int) $_POST['invoice_id'] );
		
		}else if( get_option( 'ec_option_stripe_order_create_customer' ) ){
			$payment_intent = $stripe->get_payment_intent( $payment_id );
			$stripe->attach_payment_method( $payment_intent->payment_method, $this->user );
			
		}
			
		// Set Order ID and Confirm Current Payment Status
		$payment_status = $stripe->update_payment_intent_description( $payment_id, (int) $_POST['invoice_id'] );
		if( $payment_status && $payment_status->status == 'succeeded' ){
			$order_status = 3;
		}else if( $payment_status->status == 'canceled' ){
			$order_status = 19;
		}
		
		// Get Charge ID
		$stripe_charge_id = '';
		if( isset( $payment_status->charges ) && isset( $payment_status->charges->data ) && count( $payment_status->charges->data ) > 0 ){
			$stripe_charge_id = $payment_status->charges->data[0]->id;
		}
		
		// Update Order
		$wpdb->query( $wpdb->prepare( "UPDATE 
                ec_order 
            SET 
                billing_first_name = %s, billing_last_name = %s, billing_company_name = %s, billing_address_line_1 = %s, billing_address_line_2 = %s, 
                billing_city = %s, billing_state = %s, billing_zip = %s, billing_country = %s, billing_phone = %s, 
                shipping_first_name = %s, shipping_last_name = %s, shipping_company_name = %s, shipping_address_line_1 = %s, shipping_address_line_2 = %s, 
                shipping_city = %s, shipping_state = %s, shipping_zip = %s, shipping_country = %s, shipping_phone = %s, 
                order_gateway = %s, creditcard_digits = %s, cc_exp_month = %s, cc_exp_year = %s, stripe_charge_id = %s, 
                gateway_transaction_id = %s, payment_method = 'credit_card', orderstatus_id = %d 
            WHERE order_id = %d", 
                $_POST['billing_address']['first_name'], $_POST['billing_address']['last_name'], $_POST['billing_address']['company_name'], $_POST['billing_address']['address1'], $_POST['billing_address']['address2'],
                $_POST['billing_address']['city'], $_POST['billing_address']['state'], $_POST['billing_address']['zip'], $_POST['billing_address']['country'], $_POST['billing_address']['phone'],
                $_POST['shipping_address']['first_name'], $_POST['shipping_address']['last_name'], $_POST['shipping_address']['company_name'], $_POST['shipping_address']['address1'], $_POST['shipping_address']['address2'],
                $_POST['shipping_address']['city'], $_POST['shipping_address']['state'], $_POST['shipping_address']['zip'], $_POST['shipping_address']['country'], $_POST['shipping_address']['phone'],
                get_option( 'ec_option_payment_process_method' ), $last_4, $exp_month, $exp_year, $stripe_charge_id, 
                $payment_id, $order_status, (int) $_POST['invoice_id'] 
        ) );
		
		// Clear tempcart
		$ec_db_admin->clear_tempcart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$this->order->clear_session( );
		
		// Maybe send email receipts
		if( $order_status == 3 ){
			$order_row = $ec_db_admin->get_order_row_admin( (int) $_POST['invoice_id'] );
			$orderdetails = $ec_db_admin->get_order_details_admin( (int) $_POST['invoice_id'] );
			
			do_action( 'wp_easycart_invoice_paid', (int) $_POST['invoice_id'] );
			
			// send email
			$order_display = new ec_orderdisplay( $order_row, true, true );
			$order_display->send_email_receipt( );
			$order_display->send_gift_cards( );
		}
		
		$GLOBALS['ec_cart_data']->save_session_to_db( );
		
		return $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . (int) $_POST['invoice_id'];
	}
	
	public function submit_stripe_quick_subscription_payment( $payment_id ){
		global $wpdb;
		
        if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
			$stripe = new ec_stripe( );
		else
			$stripe = new ec_stripe_connect( );
        
		if( isset( $_POST['ec_cart_is_subscriber'] ) ){
			$first_name = $GLOBALS['ec_cart_data']->cart_data->billing_first_name;
			$last_name = $GLOBALS['ec_cart_data']->cart_data->billing_last_name;
			$email = $GLOBALS['ec_cart_data']->cart_data->email;
			
			$this->mysqli->insert_subscriber( $email, $first_name, $last_name );
			
			if( $GLOBALS['ec_user']->user_id ){
				global $wpdb;
				$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET is_subscriber = 1 WHERE ec_user.user_id = %d", $GLOBALS['ec_user']->user_id ) );
			}
			
			// MyMail Hook
			if( function_exists( 'mailster' ) ){
				$subscriber_id = mailster('subscribers')->add(array(
					'firstname' => $first_name,
					'lastname' => $last_name,
					'email' => $email,
					'status' => 1,
				), false );
			}
		}
		
		$model_number = $_POST['model_number'];
		$products = $this->mysqli->get_product_list( $wpdb->prepare( " WHERE product.model_number = %s", $model_number ), "", "", "" );
		$product = new ec_product( $products[0] );
		
		$subscription_row = $this->mysqli->get_subscription_row( $_POST['subscription_id'] );
		$subscription = new ec_subscription( $subscription_row );
		
		$quantity = 1;
		if( isset( $_POST['ec_quantity'] ) )
			$quantity = (int) $_POST['ec_quantity'];
		
		if( $product->trial_period_days > 0 ){
			$subscription->send_trial_start_email( $GLOBALS['ec_user'] );
			
		}
        
        // Get option item price adjustments
        $option_promotion_multiplier = 1;
        $option_promotion_discount = 0;
        $promotions = $GLOBALS['ec_promotions']->promotions;
        for( $i=0; $i<count( $promotions ); $i++ ){
            if( $product->promotion_text == $promotions[$i]->promotion_name ){
                if( $promotions[$i]->price1 == 0 ){
                    $option_promotion_multiplier = ( 100 - $promotions[$i]->percentage1 ) / 100;
                }else if( $promotions[$i]->price1 != 0 ){
                    $option_promotion_discount = $promotions[$i]->price1;
                }
            }
        }

        // Handle Option Pricing Plans
        $option_price_adjustment = 0;
        $optionitem_list = $GLOBALS['ec_options']->get_all_optionitems( );

        foreach( $optionitem_list as $option_item ){
            $found = false;
            $check_option = false;
            if( $option_item->optionitem_id == $this->subscription_option1 || $option_item->optionitem_id == $this->subscription_option2 || $option_item->optionitem_id == $this->subscription_option3 || $option_item->optionitem_id == $this->subscription_option4 || $option_item->optionitem_id == $this->subscription_option5 ){
                if( $option_item->optionitem_price > 0 ){
                    $option_price_adjustment += $option_item->optionitem_price;
                }
            }
        }
        
        if( $product->trial_period_days > 0 ){
            $product->price = 0;
        }else{
            $product->price = $product->price + $option_price_adjustment;
        }

        // Coupon Information
        $coupon = NULL;
        $discount_total = 0;
        $is_match = false;
        if( isset( $_POST['coupon_code'] ) && $_POST['coupon_code'] != "" ){
            $coupon_row = $GLOBALS['ec_coupons']->redeem_coupon_code( $_POST['coupon_code'] );
            if( $coupon_row ){
                if( $coupon_row->by_product_id ){
                    if( $products[0]['product_id'] == $coupon_row->product_id ){
                        $is_match = true;
                    }
                }else if( $coupon_row->by_manufacturer_id ){
                    if( $products[0]['manufacturer_id'] == $coupon_row->manufacturer_id ){
                        $is_match = true;
                    }
                }else{
                    $is_match = true;
                }

                if( $is_match ){
                    $coupon = $coupon_row->promocode_id;
                }
            }
        }

        // IF MATCH FOUND, APPLY TO PRODUCT
        if( $is_match ){
            if( $coupon_row->is_dollar_based ){
                $discount_total = round( $coupon_row->promo_dollar, 2 );

            }else if( $coupon_row->is_percentage_based ){
                $discount_total = round( $product->price * $quantity * ( $coupon_row->promo_percentage / 100 ), 2 );

            }

        }else if( $option_promotion_multiplier != 1 ){
            $discount_total = round( $product->price * $quantity * ( 1 - $option_promotion_multiplier ), 2 );

        }else if( $option_promotion_discount > 0 ){
            $discount_total = round( $option_promotion_discount, 2 );

        }
        // END MATCHING COUPON SECTION

        if( $product->is_shippable ){
            $ship_price_total = $product->price * $quantity;
            $ship_weight_total = $product->weight * $quantity;
            $ship_quantity = $quantity;
        }else{
            $ship_price_total = 0;
            $ship_weight_total = 0;
            $ship_quantity = 0;
        }

        $this->shipping = new ec_shipping( $ship_price_total, $ship_weight_total, $ship_quantity, 'RADIO', $GLOBALS['ec_user']->freeshipping, $product->length, $product->width, $product->height * $quantity, array( $product ) );
        if( get_option( 'ec_option_collect_shipping_for_subscriptions' ) && $product->is_shippable ){
            $this->cart->shippable_total_items = $quantity;
        }

        if( get_option( 'ec_option_collect_shipping_for_subscriptions' ) && $this->order_totals->shipping_total > 0 ){
            $stripe->update_customer( $GLOBALS['ec_user'], $this->order_totals->shipping_total );
        }

        // Get Shipping Method to Save
        $shipping_method = "";
        if( !get_option( 'ec_option_use_shipping' ) || $this->order_totals->shipping_total <= 0 ){
            $shipping_method = "";

        }else if( $this->shipping->shipping_method == "fraktjakt" ){
            $shipping_method = $this->shipping->get_selected_shipping_method( );

        }else if( $GLOBALS['ec_cart_data']->cart_data->shipping_method != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_method != "standard" )
            $shipping_method = $this->mysqli->get_shipping_method_name( $GLOBALS['ec_cart_data']->cart_data->shipping_method );

        else if( ( $this->shipping->shipping_method == "price" || $this->shipping->shipping_method == "weight" ) && $GLOBALS['ec_cart_data']->cart_data->expedited_shipping != "" )
            $shipping_method = $GLOBALS['language']->get_text( "cart_estimate_shipping", "cart_estimate_shipping_express" );

        else
            $shipping_method = $GLOBALS['language']->get_text( "cart_estimate_shipping", "cart_estimate_shipping_standard" );

        $this->cart->subtotal = ( $product->price + $product->subscription_signup_fee ) * $quantity;

        if( $product->is_taxable || $product->vat_rate ){
            $taxable_subtotal = 0;
            $vatable_subtotal = 0;
            if( $product->is_taxable )
                $taxable_subtotal = $product->price * $quantity - $discount_total;
            if( $product->vat_rate )
                $vatable_subtotal = $product->price * $quantity - $discount_total;

            if( get_option( 'ec_option_tax_cloud_api_id' ) != "" && get_option( 'ec_option_tax_cloud_api_key' ) != "" ){
                wpeasycart_taxcloud( )->setup_subscription_for_tax( $product, $quantity );
            }
            $this->tax = new ec_tax( $product->price * $quantity, $taxable_subtotal, $vatable_subtotal, $_POST['billing_details']['address']['state'], $_POST['billing_details']['address']['country'], $GLOBALS['ec_user']->taxfree, $this->shipping->get_shipping_price( ( $product->handling_price_each * $quantity ) + $product->handling_price ), $this->cart );
        }else{
            $this->tax = new ec_tax( 0, 0, 0, $_POST['billing_details']['address']['state'], $_POST['billing_details']['address']['country'], $GLOBALS['ec_user']->taxfree, $this->shipping->get_shipping_price( ( $product->handling_price_each * $quantity ) + $product->handling_price ), $this->cart );
        }

        $this->order_totals = new ec_order_totals( $this->cart, $GLOBALS['ec_user'], $this->shipping, $this->tax, $this->discount );

        // Update Custom Order Total
        if( $product->trial_period_days > 0 ){
            $this->order_totals->grand_total = ( $product->subscription_signup_fee * $quantity );
            
        }else{
            $this->order_totals->grand_total = ( ( $product->price + $product->subscription_signup_fee ) * $quantity ) - $discount_total + $this->order_totals->tax_total + $this->tax->hst + $this->tax->pst + $this->tax->gst;
            if( !$this->tax->vat_included ){
                 $this->order_totals->grand_total += $this->order_totals->vat_total;
            }
        }

        $card = new ec_credit_card( $_POST['card']['brand'], $_POST['card']['name'], $_POST['card']['last4'], $_POST['card']['exp_month'], $_POST['card']['exp_year'], $_POST['card']['cvv'] );

        $order_id = $this->mysqli->insert_subscription_order( $product, $GLOBALS['ec_user'], $card, (int) $_POST['subscription_id'], $coupon, ( isset( $_POST['order_notes'] ) ) ? strip_tags( $_POST['order_notes'] ) : '', $this->subscription_option1_name, $this->subscription_option2_name, $this->subscription_option3_name, $this->subscription_option4_name, $this->subscription_option5_name, $this->subscription_option1_label, $this->subscription_option2_label, $this->subscription_option3_label, $this->subscription_option4_label, $this->subscription_option5_label, $quantity, $this->order_totals, $shipping_method, $this->tax, $discount_total, $_POST['stripe_charge_id'], $_POST['paymentintent_id'] );
        $this->mysqli->update_user_default_card( $GLOBALS['ec_user'], $card );

        do_action( 'wpeasycart_order_paid', $order_id );

        $order_row = $this->mysqli->get_order_row( $order_id, $GLOBALS['ec_cart_data']->cart_data->user_id );
        $order = new ec_orderdisplay( $order_row );
        $order_details = $this->mysqli->get_order_details( $order_id, $GLOBALS['ec_cart_data']->cart_data->user_id );
        $subscription->send_email_receipt( $GLOBALS['ec_user'], $order, $order_details );
        $this->mysqli->update_product_stock( $product->product_id, $quantity );

        if( $subscription->payment_duration > 0 && $subscription->payment_duration == 1 ){
            $stripe->cancel_subscription( $GLOBALS['ec_user'], $subscription->stripe_subscription_id );
            $this->mysqli->cancel_stripe_subscription( $subscription->stripe_subscription_id );
        }
			
		return $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $order_id;
		
	}
	
	public function submit_stripe_quick_subscription( $payment_id ){
		wpeasycart_session( )->handle_session( );
		global $wpdb;
		$model_number = $_POST['model_number'];
		$products = $this->mysqli->get_product_list( $wpdb->prepare( " WHERE product.model_number = %s", $model_number ), "", "", "" );
		$product = new ec_product( $products[0] );
		
		$quantity = 1;
		if( isset( $_POST['ec_quantity'] ) )
			$quantity = (int) $_POST['ec_quantity'];
		
		// Verify Payment Status in Case Already Successful
		if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
			$stripe = new ec_stripe( );
		else
			$stripe = new ec_stripe_connect( );
		
		// Handle Option Pricing Plans
		$option_price_adjustment = 0;
		$subscription_plan_options = array( );
		$optionitem_list = $GLOBALS['ec_options']->get_all_optionitems( );
		
		foreach( $optionitem_list as $option_item ){
			$found = false;
			$check_option = false;
			if( $option_item->optionitem_id == $this->subscription_option1 || $option_item->optionitem_id == $this->subscription_option2 || $option_item->optionitem_id == $this->subscription_option3 || $option_item->optionitem_id == $this->subscription_option4 || $option_item->optionitem_id == $this->subscription_option5 ){
				if( $option_item->optionitem_price > 0 ){
					if( $option_item->stripe_plan_id || $option_item->stripe_plan_id == '' ){
						$option_plan_exists = $stripe->get_plan( (object) array( 'subscription_unique_id' => $option_item->stripe_plan_id ) );
					}
					if( 
						!$option_item->stripe_plan_id || 
						$option_item->stripe_plan_id == '' || 
						$option_plan_exists->amount != (int) ( $option_item->optionitem_price * 100 ) || 
						$option_plan_exists->nickname != $GLOBALS['language']->convert_text( $option_item->optionitem_name )
					){
						$stripe_plan = $stripe->insert_option_as_plan( $product, $option_item );
						if( $stripe_plan ){
							$wpdb->query( $wpdb->prepare( "UPDATE ec_optionitem SET stripe_plan_id = %d WHERE optionitem_id = %d", $stripe_plan->id, $option_item->optionitem_id ) );
							$option_item->stripe_plan_id = $stripe_plan->id;
						}
					}
					$option_price_adjustment += $option_item->optionitem_price;
					$subscription_plan_options[] = $option_item->stripe_plan_id;
				}
			}
		}
		$product->price = $product->price + $option_price_adjustment;
		
		// Coupon Information
		$coupon = NULL;
		$discount_total = 0;
		$is_match = false;
        
		if( isset( $_POST['coupon_code'] ) && $_POST['coupon_code'] != "" ){
			$coupon_row = $GLOBALS['ec_coupons']->redeem_coupon_code( $_POST['coupon_code'] );
			$is_match = false;
			if( $coupon_row->by_product_id ){
				if( $products[0]['product_id'] == $coupon_row->product_id ){
					$is_match = true;
				}
			}else if( $coupon_row->by_manufacturer_id ){
				if( $products[0]['manufacturer_id'] == $coupon_row->manufacturer_id ){
					$is_match = true;
				}
			}else{
				$is_match = true;
			}
			
			if( $is_match ){
				$coupon = $coupon_row->promocode_id;
			}
		}
		// Coupon Check
		if( $coupon ){
			
			$coupon_exists = $stripe->get_coupon( $coupon );
			
			// Insert Coupon
			if( $coupon_exists === false ){
				$is_amount_off = false;
				if( $coupon_row->promo_dollar > 0 )
					$is_amount_off = true;
				$redeem_by = NULL;
				if( $coupon_row->expiration_date != '' ){
					$redeem_by = strtotime( $coupon_row->expiration_date ) + 7*60*60;
				}
				$stripe_coupon = array(
					"promocode_id"		=> $coupon_row->promocode_id,
					"duration"			=> $coupon_row->duration,
					"duration_in_months"=> $coupon_row->duration_in_months,
					"is_amount_off"		=> $is_amount_off,
					"amount_off"		=> $coupon_row->promo_dollar * 100,
					"percent_off"		=> $coupon_row->promo_percentage,
					"redeem_by"			=> $redeem_by,
					"max_redemptions"	=> $coupon_row->max_redemptions
				);
				$stripe_coupon_response = $stripe->insert_coupon( $stripe_coupon );
				if( $stripe_coupon_response === false ){
					return array( 'error' => 'coupon_failed' );
				}
			}
		
		}
		// END COUPON CHECK
		
		// BEGIN PROMOTIONS CHECK
		if( !$coupon && $product->has_promotion_text( ) ){
			$promotion_exists = $stripe->get_coupon( preg_replace( "/[^A-Za-z0-9_\-]/", "", strtoupper( $product->promotion_text ) ) );
			$promotions = $GLOBALS['ec_promotions']->promotions;
			$applicable_promotion = false;
			for( $i=0; $i<count( $promotions ); $i++ ){
				if( $product->promotion_text == $promotions[$i]->promotion_name ){
					$applicable_promotion = $promotions[$i];
				}
			}
			$promotion_code = "";
			if( $applicable_promotion ){
				// Insert Coupon
				$promotion_code = $coupon = preg_replace( "/[^A-Za-z0-9_\-]/", "", strtoupper( $applicable_promotion->promotion_name ) );
				// Promotion Not Added OR Coupon no Longer Matches Promotion
				if( 
					$promotion_exists === false || 
					( $applicable_promotion->price1 > 0 && !$promotion_exists->amount_off ) || 
					( $applicable_promotion->price1 <= 0 && !$promotion_exists->percent_off ) || 
					( $applicable_promotion->price1 > 0 && (int) ( $applicable_promotion->price1 * 100 ) != $promotion_exists->amount_off ) || 
					( $applicable_promotion->price1 <= 0 && $applicable_promotion->percentage1 != $promotion_exists->percent_off )
				){
					$is_amount_off = false;
					if( $applicable_promotion->price1 > 0 )
						$is_amount_off = true;
					$redeem_by = strtotime( $applicable_promotion->end_date ) + 7*60*60;
					$stripe_coupon = array(
						"promocode_id"		=> $promotion_code,
						"duration"			=> 'once',
						"is_amount_off"		=> $is_amount_off,
						"amount_off"		=> $applicable_promotion->price1 * 100,
						"percent_off"		=> $applicable_promotion->percentage1,
						"redeem_by"			=> $redeem_by,
						"max_redemptions"	=> 999
					);
					$stripe_coupon_response = $stripe->insert_coupon( $stripe_coupon );
					if( $stripe_coupon_response === false ){
						return array( 'error' => 'coupon_failed' );
					}
				}
			}
		}
		// END PROMOTIONS CHECK
		
		$is_subscriber = 0;
		if( $_POST['is_subscriber'] == 1 ){
			$is_subscriber = 1;
		}
		
		// CREATE ACCOUNT IF NEEDED
		if( isset( $_POST['create_account'] ) ){
			
			if( $this->mysqli->does_user_exist( $_POST['billing_details']['email'] ) ){
				return array( 'error' => array( 
					'id'		=> 'user_create_error',
					'message'	=> $GLOBALS['language']->get_text( "ec_errors", "email_exists_error" )
				) );
				
			}else{
				$password = md5( $_POST['create_account']['password'] );
				$password = apply_filters( 'wpeasycart_password_hash', $password, $_POST['create_account']['password'] );
				
				$billing_id = $this->mysqli->insert_address( strip_tags( $_POST['billing_details']['first_name'] ), strip_tags( $_POST['billing_details']['last_name'] ), strip_tags( $_POST['billing_details']['address']['line1'] ), strip_tags( $_POST['billing_details']['address']['line2'] ), strip_tags( $_POST['billing_details']['address']['city'] ), strip_tags( $_POST['billing_details']['address']['state'] ), strip_tags( $_POST['billing_details']['address']['postal_code'] ), strip_tags( $_POST['billing_details']['address']['country'] ), strip_tags( $_POST['billing_details']['phone'] ), strip_tags( $_POST['billing_details']['company_name'] ) );
				$shipping_id = $this->mysqli->insert_address( strip_tags( $_POST['shipping_details']['first_name'] ), strip_tags( $_POST['shipping_details']['last_name'] ), strip_tags( $_POST['shipping_details']['address']['line1'] ), strip_tags( $_POST['shipping_details']['address']['line2'] ), strip_tags( $_POST['shipping_details']['address']['city'] ), strip_tags( $_POST['shipping_details']['address']['state'] ), strip_tags( $_POST['shipping_details']['address']['postal_code'] ), strip_tags( $_POST['shipping_details']['address']['country'] ), strip_tags( $_POST['shipping_details']['phone'] ), strip_tags( $_POST['shipping_details']['company_name'] ) );
				
				$user_id = $this->mysqli->insert_user( filter_var( $_POST['billing_details']['email'], FILTER_SANITIZE_EMAIL ), $password, strip_tags( $_POST['billing_details']['first_name'] ), strip_tags( $_POST['billing_details']['last_name'] ), $billing_id, $shipping_id, "shopper", $is_subscriber, "", preg_replace( '[^a-zA-Z0-9\s]', '', $_POST['vat_registration_number'] ) );
				$GLOBALS['ec_cart_data']->cart_data->user_id = $user_id;
				
				$this->mysqli->update_address_user_id( $billing_id, $user_id );
				$this->mysqli->update_address_user_id( $shipping_id, $user_id );
				
				do_action( 'wpeasycart_account_added', $user_id, filter_var( $_POST['billing_details']['email'], FILTER_SANITIZE_EMAIL ), $_POST['create_account']['password'] );
				
				// Maybe insert WP user
				if( apply_filters( 'wp_easycart_sync_wordpress_users', false ) ){
					$user_name = 'wpeasycart_' . $user_id;
					$wp_user_id = wp_create_user( $user_name, $_POST['create_account']['password'], filter_var( $_POST['billing_details']['email'], FILTER_SANITIZE_EMAIL ) );
					add_user_meta( $wp_user_id, 'wpeasycart_user_id', $user_id, true );
				}
				
				// Send registration email if needed
				if( get_option( 'ec_option_send_signup_email' ) ){
					
					$headers   = array();
					$headers[] = "MIME-Version: 1.0";
					$headers[] = "Content-Type: text/html; charset=utf-8";
					$headers[] = "From: " . stripslashes( get_option( 'ec_option_order_from_email' ) );
					$headers[] = "Reply-To: " . stripslashes( get_option( 'ec_option_order_from_email' ) );
					$headers[] = "X-Mailer: PHP/" . phpversion( );
					
					$message = $GLOBALS['language']->get_text( "account_register", "account_register_email_message" ) . " " . $email;
					
					if( get_option( 'ec_option_use_wp_mail' ) ){
						wp_mail( stripslashes( get_option( 'ec_option_bcc_email_addresses' ) ), $GLOBALS['language']->get_text( "account_register", "account_register_email_title" ), $message, implode("\r\n", $headers) );
					}else{
						$admin_email = stripslashes( get_option( 'ec_option_bcc_email_addresses' ) );
						$subject = $GLOBALS['language']->get_text( "account_register", "account_register_email_title" );
						$mailer = new wpeasycart_mailer( );
						$mailer->send_order_email( $admin_email, $subject, $message );
					}
					
				}
			
				$GLOBALS['ec_cart_data']->cart_data->is_guest = false;
				$GLOBALS['ec_cart_data']->cart_data->user_id = $user_id;
				$GLOBALS['ec_cart_data']->cart_data->email = filter_var( $_POST['billing_details']['email'], FILTER_SANITIZE_EMAIL );
				$GLOBALS['ec_cart_data']->cart_data->username = strip_tags( $_POST['billing_details']['first_name'] ) . ' ' . strip_tags( $_POST['billing_details']['last_name'] );
				$GLOBALS['ec_cart_data']->cart_data->first_name = strip_tags( $_POST['billing_details']['first_name'] );
				$GLOBALS['ec_cart_data']->cart_data->last_name = strip_tags( $_POST['billing_details']['last_name'] );
				
				$GLOBALS['ec_user'] = new ec_user( "" );
			}
		
		}else{ // Customer already exists, lets update their billing address
			$GLOBALS['ec_user']->vat_registration_number = preg_replace( '/[^a-zA-Z0-9\s]/', '', $_POST['vat_registration_number'] );
			$this->mysqli->update_user( $GLOBALS['ec_user']->user_id, preg_replace( '/[^a-zA-Z0-9\s]/', '', $_POST['vat_registration_number'] ) );
			if( $GLOBALS['ec_user']->billing_id == 0 ){
				$billing_id = $this->mysqli->insert_address( strip_tags( $_POST['billing_details']['first_name'] ), strip_tags( $_POST['billing_details']['last_name'] ), strip_tags( $_POST['billing_details']['address']['line1'] ), strip_tags( $_POST['billing_details']['address']['line2'] ), strip_tags( $_POST['billing_details']['address']['city'] ), strip_tags( $_POST['billing_details']['address']['state'] ), strip_tags( $_POST['billing_details']['address']['postal_code'] ), strip_tags( $_POST['billing_details']['address']['country'] ), strip_tags( $_POST['billing_details']['phone'] ), strip_tags( $_POST['billing_details']['company_name'] ) );
				$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET default_billing_address_id = %d WHERE user_id = %d", $billing_id, $GLOBALS['ec_user']->user_id ) );
				$this->mysqli->update_address_user_id( $billing_id, $GLOBALS['ec_user']->user_id );
				
			}else{
				$this->mysqli->update_address( $GLOBALS['ec_user']->billing_id, strip_tags( $_POST['billing_details']['first_name'] ), strip_tags( $_POST['billing_details']['last_name'] ), strip_tags( $_POST['billing_details']['address']['line1'] ), strip_tags( $_POST['billing_details']['address']['line2'] ), strip_tags( $_POST['billing_details']['address']['city'] ), strip_tags( $_POST['billing_details']['address']['state'] ), strip_tags( $_POST['billing_details']['address']['postal_code'] ), strip_tags( $_POST['billing_details']['address']['country'] ), strip_tags( $_POST['billing_details']['phone'] ), strip_tags( $_POST['billing_details']['company_name'] ) );
			}
			if( $_POST['shipping_details']['first_name'] != '' ){
				if( $GLOBALS['ec_user']->shipping_id == 0 ){
					$shipping_id = $this->mysqli->insert_address( strip_tags( $_POST['shipping_details']['first_name'] ), strip_tags( $_POST['shipping_details']['last_name'] ), strip_tags( $_POST['shipping_details']['address']['line1'] ), strip_tags( $_POST['shipping_details']['address']['line2'] ), strip_tags( $_POST['shipping_details']['address']['city'] ), strip_tags( $_POST['shipping_details']['address']['state'] ), strip_tags( $_POST['shipping_details']['address']['postal_code'] ), strip_tags( $_POST['shipping_details']['address']['country'] ), strip_tags( $_POST['shipping_details']['phone'] ), strip_tags( $_POST['shipping_details']['company_name'] ) );
					$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET default_shipping_address_id = %d WHERE user_id = %d", $shipping_id, $GLOBALS['ec_user']->user_id ) );
					$this->mysqli->update_address_user_id( $shipping_id, $GLOBALS['ec_user']->user_id );
					
				}else{
					$this->mysqli->update_address( $GLOBALS['ec_user']->shipping_id, strip_tags( $_POST['shipping_details']['first_name'] ), strip_tags( $_POST['shipping_details']['last_name'] ), strip_tags( $_POST['shipping_details']['address']['line1'] ), strip_tags( $_POST['shipping_details']['address']['line2'] ), strip_tags( $_POST['shipping_details']['address']['city'] ), strip_tags( $_POST['shipping_details']['address']['state'] ), strip_tags( $_POST['shipping_details']['address']['postal_code'] ), strip_tags( $_POST['shipping_details']['address']['country'] ), strip_tags( $_POST['shipping_details']['phone'] ), strip_tags( $_POST['shipping_details']['company_name'] ) );
				}
			
			}
		}
		
		// Set Sessions
		$GLOBALS['ec_cart_data']->cart_data->billing_first_name = strip_tags( $_POST['billing_details']['first_name'] );
		$GLOBALS['ec_cart_data']->cart_data->billing_last_name = strip_tags( $_POST['billing_details']['last_name'] );
		$GLOBALS['ec_cart_data']->cart_data->billing_company_name = strip_tags( $_POST['billing_details']['company_name'] );
		$GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = strip_tags( $_POST['billing_details']['address']['line1'] );
		$GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = strip_tags( $_POST['billing_details']['address']['line2'] );
		$GLOBALS['ec_cart_data']->cart_data->billing_city = strip_tags( $_POST['billing_details']['address']['city'] );
		$GLOBALS['ec_cart_data']->cart_data->billing_state = strip_tags( $_POST['billing_details']['address']['state'] );
		$GLOBALS['ec_cart_data']->cart_data->billing_zip = strip_tags( $_POST['billing_details']['address']['postal_code'] );
		$GLOBALS['ec_cart_data']->cart_data->billing_country = strip_tags( $_POST['billing_details']['address']['country'] );
		$GLOBALS['ec_cart_data']->cart_data->billing_phone = strip_tags( $_POST['billing_details']['phone'] );
		
		$GLOBALS['ec_cart_data']->cart_data->shipping_selector = ( strlen( $_POST['shipping_details']['address']['first_name'] ) > 0) ? 1 : 0;
		
		$GLOBALS['ec_cart_data']->cart_data->shipping_first_name = strip_tags( $_POST['shipping_details']['first_name'] );
		$GLOBALS['ec_cart_data']->cart_data->shipping_last_name = strip_tags( $_POST['shipping_details']['last_name'] );
		$GLOBALS['ec_cart_data']->cart_data->shipping_company_name = strip_tags( $_POST['shipping_details']['company_name'] );
		$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = strip_tags( $_POST['shipping_details']['address']['line1'] );
		$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = strip_tags( $_POST['shipping_details']['address']['line2'] );
		$GLOBALS['ec_cart_data']->cart_data->shipping_city = strip_tags( $_POST['shipping_details']['address']['city'] );
		$GLOBALS['ec_cart_data']->cart_data->shipping_state = strip_tags( $_POST['shipping_details']['address']['state'] );
		$GLOBALS['ec_cart_data']->cart_data->shipping_zip = strip_tags( $_POST['shipping_details']['address']['postal_code'] );
		$GLOBALS['ec_cart_data']->cart_data->shipping_country = strip_tags( $_POST['shipping_details']['address']['country'] );
		$GLOBALS['ec_cart_data']->cart_data->shipping_phone = strip_tags( $_POST['shipping_details']['phone'] );

		$GLOBALS['ec_cart_data']->cart_data->first_name = strip_tags( $_POST['create_account']['first_name'] );
		$GLOBALS['ec_cart_data']->cart_data->last_name = strip_tags( $_POST['create_account']['last_name'] );

		$GLOBALS['ec_cart_data']->cart_data->order_notes = $_POST['order_notes'];

		$GLOBALS['ec_cart_data']->save_session_to_db( );

		$GLOBALS['ec_user']->setup_billing_info_data( $GLOBALS['ec_cart_data']->cart_data->billing_first_name, $GLOBALS['ec_cart_data']->cart_data->billing_last_name, $GLOBALS['ec_cart_data']->cart_data->billing_address, $GLOBALS['ec_cart_data']->cart_data->billing_address2, $GLOBALS['ec_cart_data']->cart_data->billing_city, $GLOBALS['ec_cart_data']->cart_data->billing_state, $GLOBALS['ec_cart_data']->cart_data->billing_country, $GLOBALS['ec_cart_data']->cart_data->billing_zip, $GLOBALS['ec_cart_data']->cart_data->billing_phone, $GLOBALS['ec_cart_data']->cart_data->billing_company_name );
		$GLOBALS['ec_user']->setup_shipping_info_data( $GLOBALS['ec_cart_data']->cart_data->shipping_first_name, $GLOBALS['ec_cart_data']->cart_data->shipping_last_name, $GLOBALS['ec_cart_data']->cart_data->shipping_address, $GLOBALS['ec_cart_data']->cart_data->shipping_address2, $GLOBALS['ec_cart_data']->cart_data->shipping_city, $GLOBALS['ec_cart_data']->cart_data->shipping_state, $GLOBALS['ec_cart_data']->cart_data->shipping_country, $GLOBALS['ec_cart_data']->cart_data->shipping_zip, $GLOBALS['ec_cart_data']->cart_data->shipping_phone, $GLOBALS['ec_cart_data']->cart_data->shipping_company_name );
		
		if( $is_subscriber ){
			$this->mysqli->insert_subscriber( $GLOBALS['ec_user']->email, strip_tags( $_POST['billing_details']['first_name'] ), strip_tags( $_POST['billing_details']['last_name'] ) );
		}
		
		if( function_exists( 'mailster' ) ){
			$subscriber_id = mailster('subscribers')->add(array(
				'firstname' => strip_tags( $_POST['billing_details']['first_name'] ),
				'lastname' => strip_tags( $_POST['billing_details']['last_name'] ),
				'email' => $GLOBALS['ec_user']->email,
				'status' => $is_subscriber,
			), false );
		}
		// END SUBSCRIBER
						
		// Possibly discount the initial fee
		$initial_fee = $product->subscription_signup_fee * $quantity;
		if( $discount_total > $product->price ){
			$remaining_discount = $discount_total - $product->price;
			$initial_fee = $initial_fee - $remaining_discount;
		}
		
		$customer_id = $GLOBALS['ec_user']->stripe_customer_id;
		
		$this->cart->subtotal = ( $product->price + $product->subscription_signup_fee ) * $quantity;
		
		if( $product->is_taxable || $product->vat_rate ){
			$taxable_subtotal = 0;
			$vatable_subtotal = 0;
			if( $product->is_taxable )
				$taxable_subtotal = $product->price * $quantity - $discount_total;
			if( $product->vat_rate )
				$vatable_subtotal = $product->price * $quantity - $discount_total;
			
			if( get_option( 'ec_option_tax_cloud_api_id' ) != "" && get_option( 'ec_option_tax_cloud_api_key' ) != "" ){
				wpeasycart_taxcloud( )->setup_subscription_for_tax( $product, $quantity );
			}
			$this->tax = new ec_tax( $product->price * $quantity, $taxable_subtotal, $vatable_subtotal, $_POST['billing_details']['address']['state'], $_POST['billing_details']['address']['country'], $GLOBALS['ec_user']->taxfree, $this->shipping->get_shipping_price( ( $product->handling_price_each * $quantity ) + $product->handling_price ), $this->cart );
		}else{
			$this->tax = new ec_tax( 0, 0, 0, $_POST['billing_details']['address']['state'], $_POST['billing_details']['address']['country'], $GLOBALS['ec_user']->taxfree, $this->shipping->get_shipping_price( ( $product->handling_price_each * $quantity ) + $product->handling_price ), $this->cart );
		}
						
		$this->order_totals = new ec_order_totals( $this->cart, $GLOBALS['ec_user'], $this->shipping, $this->tax, $this->discount );
			
		$need_to_update_customer_id = false;
		$customer_insert_test = false;
		
		if( $customer_id == "" ){
			$customer_id = $stripe->insert_customer( $GLOBALS['ec_user'], NULL, $initial_fee );
			$need_to_update_customer_id = true;
		
		}else{
			$found_customer = $stripe->update_customer( $GLOBALS['ec_user'], $initial_fee );
			if( !$found_customer ){ // Likely switched from test to live or to a new account, so customer id was wrong
				$customer_id = $stripe->insert_customer( $GLOBALS['ec_user'], NULL, $initial_fee );
				$need_to_update_customer_id = true;
			}
		}
		
		if( $need_to_update_customer_id && $customer_id ){ // Customer inserted to stripe successfully
			$this->mysqli->update_user_stripe_id( $GLOBALS['ec_user']->user_id, $customer_id );
			$GLOBALS['ec_user']->stripe_customer_id = $customer_id;
			$customer_insert_test = true;
		}else if( $need_to_update_customer_id && !$customer_id ){
			$customer_insert_test = false;
		}else{
			$customer_insert_test = true;
		}
		
		// Failed to Insert/Update Customer
		if( !$customer_insert_test ){ 
			return array( 'error' => 'customer_error' );
		}
		
		$plan_added = $product->stripe_plan_added;
		$plan_check = $stripe->get_plan( $product );
		
		if( !$product->stripe_plan_added ){ // Add plan if needed
			$plan_added = $stripe->insert_plan( $product );
			$this->mysqli->update_product_stripe_added( $product->product_id );
		}else if( !$plan_check || $plan_check->amount != (int) ( $product->price * 100 ) ){
			$plan_added = $stripe->insert_plan( $product );
		}
		
		// Failed to add plan
		if( !$plan_added ){
			$plan_added = $stripe->get_plan( $product );
            if( $plan_added ){
                $this->mysqli->update_product_stripe_added( $product->product_id );
            }else{
                return array( 'error' => 'plan_error' );
            }
		}
		
		// Add customer to payment intent
		$card_response = $stripe->insert_card( $GLOBALS['ec_user'], $_POST['stripeToken'] );
		if( !$card_response ){
			return array( 'error' => 'payment_fail' );
		}
		$default_response = $stripe->set_default_payment_method( $card_response, $GLOBALS['ec_user'] );
		
		$prorate = "false";
		if( $product->subscription_prorate )
			$prorate = "true";
		$trial_end_date = NULL;
		if( $product->trial_period_days > 0 )
			$trial_end_date = strtotime( "+" . $product->trial_period_days . " days" );
		
		$tax_rates = $this->tax->get_stripe_tax_rates(  $product->is_taxable, $product->vat_rate, ( $this->order_totals->tax_total / ( $product->price * $quantity - $discount_total ) ) );
		$stripe_response = $stripe->insert_subscription( $product, $GLOBALS['ec_user'], $card_response, $coupon, $prorate, $trial_end_date, $quantity, number_format( $this->tax->get_tax_rate( ), 2, '.', '' ), $subscription_plan_options, $tax_rates );
		if( $stripe_response === false ){
			return array( 'error' => 'subscription_fail' );
		}
		
		$subscription_id = $this->mysqli->insert_stripe_subscription( $stripe_response, $product, $GLOBALS['ec_user'], NULL, $quantity );
			
		return array( 
			'subscription_id' 	=> $subscription_id,
			'status'			=> $stripe_response->latest_invoice->status,
			'clientSecret'		=> $stripe_response->latest_invoice->payment_intent->client_secret,
			'paymentintent_id'	=> $stripe_response->latest_invoice->payment_intent->id,
			'stripe_charge_id'	=> $stripe_response->latest_invoice->charge
		);
	}
	
	public function submit_paypal_order( ){
		
		global $wpdb;
		
		$this->order->submit_order( "third_party" );
		$wpdb->query( $wpdb->prepare( "UPDATE ec_order SET orderstatus_id = 10 WHERE order_id = %d", $this->order->order_id ) );
		do_action( 'wpeasycart_order_paid', $order_id );
		$this->order->send_email_receipt( );
		
		$GLOBALS['ec_cart_data']->save_session_to_db( );
		
		return $this->order->order_id;
		
	}
	
	public function update_authorized_paypal_order( $paypal_response ){
		
		if( isset( $_GET['ec_firstpage'] ) ){
			
			if( isset( $paypal_response->payer ) )
				$payer_info = $paypal_response->payer->payer_info;
			else
				$payer_info = $paypal_response->payer_info;
			
			$GLOBALS['ec_cart_data']->cart_data->billing_first_name = $payer_info->first_name;
			$GLOBALS['ec_cart_data']->cart_data->billing_last_name = $payer_info->last_name;
			$GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = $payer_info->shipping_address->line1;
			$GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = $payer_info->shipping_address->line2;
			$GLOBALS['ec_cart_data']->cart_data->billing_city = $payer_info->shipping_address->city;
			$GLOBALS['ec_cart_data']->cart_data->billing_state = $payer_info->shipping_address->state;
			$GLOBALS['ec_cart_data']->cart_data->billing_zip = $payer_info->shipping_address->postal_code;
			$GLOBALS['ec_cart_data']->cart_data->billing_country = $payer_info->shipping_address->country_code;
			if( isset( $payer_info->phone ) && $payer_info->phone != "" ){
				$GLOBALS['ec_cart_data']->cart_data->billing_phone = $payer_info->phone;
			}
		
			$GLOBALS['ec_cart_data']->cart_data->shipping_selector = "";
			$GLOBALS['ec_cart_data']->cart_data->shipping_first_name = $payer_info->first_name;
			$GLOBALS['ec_cart_data']->cart_data->shipping_last_name = $payer_info->last_name;
			$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = $payer_info->shipping_address->line1;
			$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = $payer_info->shipping_address->line2;
			$GLOBALS['ec_cart_data']->cart_data->shipping_city = $payer_info->shipping_address->city;
			$GLOBALS['ec_cart_data']->cart_data->shipping_state = $payer_info->shipping_address->state;
			$GLOBALS['ec_cart_data']->cart_data->shipping_zip = $payer_info->shipping_address->postal_code;
			$GLOBALS['ec_cart_data']->cart_data->shipping_country = $payer_info->shipping_address->country_code;
			if( isset( $payer_info->phone ) && $payer_info->phone != "" ){
				$GLOBALS['ec_cart_data']->cart_data->shipping_phone = $payer_info->phone;
			}
		
			$GLOBALS['ec_cart_data']->cart_data->email = $payer_info->email;
			$GLOBALS['ec_cart_data']->cart_data->username = $payer_info->first_name . " " . $payer_info->last_name;
			$GLOBALS['ec_cart_data']->cart_data->first_name = $payer_info->first_name;
			$GLOBALS['ec_cart_data']->cart_data->last_name = $payer_info->last_name;
			
			$GLOBALS['ec_cart_data']->cart_data->is_guest = true;
			$GLOBALS['ec_cart_data']->cart_data->guest_key = $GLOBALS['ec_cart_data']->ec_cart_id;
		
			$GLOBALS['ec_cart_data']->save_session_to_db( );
			do_action( 'wpeasycart_cart_updated' );
		}
		
	}
	
	public function submit_authorized_paypal_order( ){
		
		global $wpdb;
		
		// Create Order
		$this->order->submit_order( "third_party" );
		
		// Execute payment
		$paypal = new ec_paypal( );
		$result = $paypal->execute_order( $this->order->order_id, $this->cart, $this->order_totals, $this->tax );
		
		// Update Order or Remove Order
		if( $result ){
			
			$ec_db_admin = new ec_db_admin( );
			$order_row = $ec_db_admin->get_order_row_admin( $this->order->order_id );
			$orderdetails = $ec_db_admin->get_order_details_admin( $this->order->order_id );
				
			// Clear tempcart
			$ec_db_admin->clear_tempcart( $GLOBALS['ec_cart_data']->ec_cart_id );
			$this->order->clear_session( );
			
			$GLOBALS['ec_cart_data']->save_session_to_db( );
			header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order->order_id );	
		}else{
			$this->mysqli->remove_order( $this->order->order_id );
			header( "location: " . $this->cart_page . $this->permalink_divider . "ec_cart_error=payment_failed" );
		}
		die( );
		
	}
	
	public function insert_ideal_order( $source ){
		global $wpdb;
		$this->order->submit_order( "ideal" );
		$order_id = $this->order->order_id;
		$wpdb->query( $wpdb->prepare( "UPDATE ec_order SET gateway_transaction_id = %s, payment_method = %s WHERE order_id = %d", $source['id'] . ':' . $source['client_secret'], get_option( 'ec_option_payment_process_method' ), $order_id ) );
	}
	
	private function process_submit_order(){
		
		if( isset( $_POST['ec_cart_is_subscriber'] ) ){
			$first_name = $GLOBALS['ec_cart_data']->cart_data->billing_first_name;
			$last_name = $GLOBALS['ec_cart_data']->cart_data->billing_last_name;
			$email = $GLOBALS['ec_cart_data']->cart_data->email;
			
			$this->mysqli->insert_subscriber( $email, $first_name, $last_name );
			
			if( $GLOBALS['ec_user']->user_id ){
				global $wpdb;
				$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET is_subscriber = 1 WHERE ec_user.user_id = %d", $GLOBALS['ec_user']->user_id ) );
			}
			
			// MyMail Hook
			if( function_exists( 'mailster' ) ){
				$subscriber_id = mailster('subscribers')->add(array(
					'firstname' => $first_name,
					'lastname' => $last_name,
					'email' => $email,
					'status' => 1,
				), false );
			}
		}
		
		if( isset( $_POST['paypal_payment_id'] ) || isset( $_POST['paypal_order_id'] ) ){
			global $wpdb;
			
			// Create Order
			$this->order->submit_order( "third_party" );
			
			// Execute payment
			$paypal = new ec_paypal( );
			if( isset( $_POST['paypal_order_id'] ) )
				$result = $paypal->execute_order( $this->order->order_id, $this->cart, $this->order_totals, $this->tax );
			
			else
				$result = $paypal->execute_payment( $this->order->order_id, $this->cart, $this->order_totals, $this->tax );
			
			// Update Order or Remove Order
			if( $result ){
				
				$ec_db_admin = new ec_db_admin( );
				$order_row = $ec_db_admin->get_order_row_admin( $this->order->order_id );
				$orderdetails = $ec_db_admin->get_order_details_admin( $this->order->order_id );
				if( $order_row && !isset( $_POST['paypal_order_id'] ) ){
					
					/* Update Stock Quantity */
					foreach( $orderdetails as $orderdetail ){
						$product = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.* FROM ec_product WHERE ec_product.product_id = %d", $orderdetail->product_id ) );
						if( $product ){
							if( $product->use_optionitem_quantity_tracking )	
								$ec_db_admin->update_quantity_value( $orderdetail->quantity, $orderdetail->product_id, $orderdetail->optionitem_id_1, $orderdetail->optionitem_id_2, $orderdetail->optionitem_id_3, $orderdetail->optionitem_id_4, $orderdetail->optionitem_id_5 );
							$ec_db_admin->update_product_stock( $orderdetail->product_id, $orderdetail->quantity );
                            $this->mysqli->update_details_stock_adjusted( $orderdetail->orderdetail_id );
						}
					}
					
					// Update Order Status/Send Alerts
					if( $result == 'approved' ){
						$ec_db_admin->update_order_status( $this->order->order_id, "10" );
						do_action( 'wpeasycart_order_paid', $this->order->order_id );
					}
					
					// send email
					$order_display = new ec_orderdisplay( $order_row, true, true );
					$order_display->send_email_receipt( );
					$order_display->send_gift_cards( );
				}
					
				// Clear tempcart
				$ec_db_admin->clear_tempcart( $GLOBALS['ec_cart_data']->ec_cart_id );
				$this->order->clear_session( );
				
				$GLOBALS['ec_cart_data']->save_session_to_db( );
				header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order->order_id );	
			}else{
				$this->mysqli->remove_order( $this->order->order_id );
				header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=payment_failed" );
			}
			die( );
			
		}
		
		if( $GLOBALS['ec_cart_data']->cart_data->email == "" ){
			
			header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info&ec_cart_error=session_expired" );
			
		}else if( !$this->validate_submit_order_data( ) ){
			
			header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info&ec_cart_error=invalid_address" );
			
		}else{
			
			if( get_option( 'ec_option_skip_shipping_page' ) ){
				$this->shipping->skip_shipping_selection_page( );
			}
		
			if( isset( $_POST['ec_cart_payment_selection'] ) )
				$payment_type = $_POST['ec_cart_payment_selection'];
			else if( $this->is_affirm )
				$payment_type = "affirm";
			else
				$payment_type = $GLOBALS['language']->get_text( "ec_success", "cart_account_free_order" );
				
			if( isset( $_POST['ec_order_notes'] ) )
				$GLOBALS['ec_cart_data']->cart_data->order_notes = stripslashes( $_POST['ec_order_notes'] );
			
			/************************************** 
			Place 3Ds Payment Processing HERE
			***************************************/
			if( get_option( 'ec_option_payment_process_method' ) == "nmi" && get_option( 'ec_option_nmi_3ds' ) == "2" ){ // 3D Secure
				
				$response = $this->order->submit_order( $payment_type );
				
				if( $response ){
					
					$gateway = new ec_cardinal( );
					$gateway->initialize( $this->cart, $this->user, $this->shipping, $this->tax, 
										  $this->discount, $this->payment->credit_card, $this->order_totals, $this->order->order_id );
					
					$response = $gateway->secure_3d_lookup( );
					
					if( $response == "ERROR" ){ // Failed to Process CC at Cardinal
						$this->mysqli->remove_order( $this->order->order_id );
						header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=payment_failed" );
						
					}else if( $response == "NO3DS" ){
						$this->process_nmi_no_3ds( );
						
					}else{ // NO 3DS for User, Process Normally
						$submit_return_val = $this->order->submit_order( $payment_type );
						if( $submit_return_val == "1" ){
							$GLOBALS['ec_cart_data']->save_session_to_db( );
							header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order->order_id );	
						}else{
							$this->mysqli->remove_order( $order_id );
							header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=payment_failed" );
						}
					
					}
				
				}else{ // order failed to insert
					header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=payment_failed" );
					
				}
				
			/************************************** 
			Place Standard Payment Processing HERE
			***************************************/
			}else{ // Process Non-3D Secure (V3.2.4 and higher 3Ds)
				
				$submit_return_val = $this->order->submit_order( $payment_type );
				do_action( 'wpeasycart_submit_order_complete' );
				
				if( $this->order_totals->grand_total <= 0 ){
					$GLOBALS['ec_cart_data']->save_session_to_db( );
					header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order->order_id );	
					
				}else if( $payment_type == "manual_bill" ){ // Show fail message or the success landing page (including the manual bill notice).
					if( $submit_return_val == "1" ){
						$GLOBALS['ec_cart_data']->save_session_to_db( );
						header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order->order_id );
					}else{
						$GLOBALS['ec_cart_data']->save_session_to_db( );
						header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&cart_error=manualbill_failed" );
					}
					
				}else if( $payment_type == "affirm" ){
					if( $submit_return_val == "1" ){
						$GLOBALS['ec_cart_data']->save_session_to_db( );
						header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order->order_id );
					}else{
						$GLOBALS['ec_cart_data']->save_session_to_db( );
						header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=payment_failed" );
					}
					
				}else if( $payment_type == "third_party" ){ // Show the third party landing page
					if( $submit_return_val == "1" ){
						$GLOBALS['ec_cart_data']->save_session_to_db( );
						header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=third_party&order_id=" . $this->order->order_id );
					}else{
						$GLOBALS['ec_cart_data']->save_session_to_db( );
						header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=thirdparty_failed" );
					}
					
				}else{ // Either show the success landing page
					
					if( $submit_return_val == "1" ){
						if( $this->order->payment->is_3d_auth )
							$this->auth_3d_form();
						else{
							$GLOBALS['ec_cart_data']->save_session_to_db( );
							header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order->order_id );	
						}
						
					}else{
						header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=payment_failed" );
					}
					
				}
				
			}
			
		}
		
	}
	
	public function auth_3d_form( ){
		echo "<form name=\"ec_cart_3dauth_form\" method=\"POST\" action=\"" . $this->order->payment->post_url . "\">";
		echo "<input type=\"hidden\" name=\"" . $this->order->payment->post_id_input_name . "\" value=\"" . $this->order->payment->post_id . "\">";
		echo "<input type=\"hidden\" name=\"" . $this->order->payment->post_message_input_name . "\" value=\"" . $this->order->payment->post_message . "\">";
		echo "<input type=\"hidden\" name=\"" . $this->order->payment->post_return_url_input_name . "\" value=\"" . $this->cart_page . $this->permalink_divider . "ec_page=3dsecure&order_id=" . $this->order->order_id . "\">";
		echo "</form>";
		echo "<SCRIPT LANGUAGE=\"Javascript\">document.ec_cart_3dauth_form.submit();</SCRIPT>";
	}
	
	public function process_nmi_no_3ds( ){
		
		$gateway = new ec_nmi( );
		if( isset( $_POST['ec_expiration_month'] ) && isset( $_POST['ec_expiration_year'] ) ){
			$exp_month = $_POST['ec_expiration_month'];
			$exp_year = $_POST['ec_expiration_year'];
		}else{
			$exp_date = $_POST['ec_cc_expiration'];
			$exp_month = substr( $exp_date, 0, 2 );
			$exp_year = substr( $exp_date, 5 );
			if( strlen( $exp_year ) == 2 ){
				$exp_year = "20" . $exp_year;
			}
		}
		$credit_card = new ec_credit_card( $this->get_payment_type( $this->sanatize_card_number( $_POST['ec_card_number'] ) ), stripslashes( $GLOBALS['ec_cart_data']->cart_data->billing_first_name . " " . $GLOBALS['ec_cart_data']->cart_data->billing_last_name ),  $this->sanatize_card_number( $_POST['ec_card_number'] ), $exp_month, $exp_year, $_POST['ec_security_code'] );
		$gateway->initialize( $this->cart, $this->user, $this->shipping, $this->tax, $this->discount, $credit_card, $this->order_totals, $_POST['order_id'] );
		$result = $gateway->process_credit_card( );
		
		if( $result ){
			
			$this->mysqli->update_order_status( $_POST['order_id'], "6" );
			
			do_action( 'wpeasycart_order_paid', $_POST['order_id'] );
			
			$db_admin = new ec_db_admin( );
			$order_row = $db_admin->get_order_row_admin( $_POST['order_id'] );
			$order_display = new ec_orderdisplay( $order_row, true, true );
			$order_display->send_email_receipt( );
			
			$this->mysqli->clear_tempcart( $GLOBALS['ec_cart_data']->ec_cart_id );
			$GLOBALS['ec_cart_data']->checkout_session_complete( );
				
			header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $_POST['order_id'] );
			
		}else{
			$this->mysqli->remove_order( $_POST['order_id'] );
			$GLOBALS['ec_cart_data']->save_session_to_db( );	
			header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=3dsecure_failed" );	
		}
		
	}
	
	public function process_3ds_final( ){
		
		$gateway = new ec_nmi( );
		if( isset( $_POST['ec_expiration_month'] ) && isset( $_POST['ec_expiration_year'] ) ){
			$exp_month = $_POST['ec_expiration_month'];
			$exp_year = $_POST['ec_expiration_year'];
		}else{
			$exp_date = $_POST['ec_cc_expiration'];
			$exp_month = substr( $exp_date, 0, 2 );
			$exp_year = substr( $exp_date, 5 );
			if( strlen( $exp_year ) == 2 ){
				$exp_year = "20" . $exp_year;
			}
		}
		$credit_card = new ec_credit_card( $this->get_payment_type( $this->sanatize_card_number( $_POST['ec_card_number'] ) ), stripslashes( $GLOBALS['ec_cart_data']->cart_data->billing_first_name . " " . $GLOBALS['ec_cart_data']->cart_data->billing_last_name ),  $this->sanatize_card_number( $_POST['ec_card_number'] ), $exp_month, $exp_year, $_POST['ec_security_code'] );
		$gateway->initialize( $this->cart, $this->user, $this->shipping, $this->tax, $this->discount, $credit_card, $this->order_totals, $_POST['order_id'] );
		$result = $gateway->process_3ds( );
		
		if( $result ){
			
			$this->mysqli->update_order_status( $_POST['order_id'], "6" );
			
			do_action( 'wpeasycart_order_paid', $_POST['order_id'] );
			
			$db_admin = new ec_db_admin( );
			$order_row = $db_admin->get_order_row_admin( $_POST['order_id'] );
			$order_display = new ec_orderdisplay( $order_row, true, true );
			$order_display->send_email_receipt( );
			
			$this->mysqli->clear_tempcart( $GLOBALS['ec_cart_data']->ec_cart_id );
			$GLOBALS['ec_cart_data']->checkout_session_complete( );
				
			header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $_POST['order_id'] );
			
		}else{
			$this->mysqli->remove_order( $_POST['order_id'] );
			$GLOBALS['ec_cart_data']->save_session_to_db( );	
			header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=3dsecure_failed" );	
		}
		
	}
	
	public function process_3dsecure_response( ){
		
        global $wpdb;
		$success = false;
        
        // Check if order has already been approved, fixing data error.
        $order = $wpdb->get_row( $wpdb->prepare( "SELECT ec_order.order_id FROM ec_order, ec_orderstatus WHERE ec_order.order_id = %d AND ec_order.orderstatus_id = ec_orderstatus.status_id AND ec_orderstatus.is_approved = 1", (int) $_GET['order_id'] ) );
        if( $order ){
            header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . (int) $_GET['order_id'] );
            die( );
        }
		
        // Verify the order with the gateway
		if( get_option( 'ec_option_payment_process_method' ) == "sagepay" ){
			$gateway = new ec_sagepay( );
		}else if( get_option( 'ec_option_payment_process_method' ) == "realex" ){
			$gateway = new ec_realex( );
		}
		
		if( isset( $gateway ) ){
			$success = $gateway->secure_3d_auth( );
			if( $success ){
				
				do_action( 'wpeasycart_order_paid', (int) $_GET['order_id'] );
				
				$this->order->clear_session();
				if( $this->discount->giftcard_code )
					$this->mysqli->update_giftcard_total( $this->discount->giftcard_code, $this->discount->giftcard_discount );
					
				$GLOBALS['ec_cart_data']->save_session_to_db( );		
				header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . (int) $_GET['order_id'] );
                die( );
			}
		}
		
		if( !$success ){
			$this->mysqli->remove_order( (int) $_GET['order_id'] );
			$GLOBALS['ec_cart_data']->save_session_to_db( );	
			header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=3dsecure_failed" );
            die( );
		}	
	}
	
	public function process_3ds_response( ){
		
		if( isset( $_GET['order_id'] ) ){
			$order_id = $_GET['order_id'];
			$db = new ec_db_admin( );
			$order = $db->get_order_row_admin( $order_id );
			if( $order ){
				$gateway = new ec_cardinal( );
				$response = $gateway->secure_3d_auth( $order_id, $order, $_POST );
				
				if( !$response ){
					header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=3dsecure_failed" );
				}
			
			}else{// No VALID Order ID Returned, Likely Fraud
				header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=3dsecure_failed" );
				
			}
		}else{// No Order ID Returned, Likely Fraud
			header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=3dsecure_failed" );
		}
		
	}
	
	private function process_realex_redirect( ){
		// Check response, if success, send to success page. If failed, return to last page of cart
		if( isset( $_POST['AUTHCODE'] ) && isset( $_POST['ORDER_ID'] ) && $_POST['AUTHCODE'] == "00" )
			header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $_POST['ORDER_ID'] );
		else
			header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=thirdparty_failed" );
	}
	
	private function process_realex_response( ){
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/inc/scripts/realex_payment_complete.php" );
	}
	
	private function process_paymentexpress_thirdparty_response( ){
		$gateway = new ec_paymentexpress_thirdparty( );
		$gateway->update_order_status( );
		$db = new ec_db( );
		$db->clear_tempcart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$GLOBALS['ec_cart_data']->save_session_to_db( );	
		header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $_GET['order_id'] );
	}
	
	private function process_third_party_forward( ){
		$this->payment->third_party->initialize( $_GET['order_id'] );
		$this->payment->third_party->display_auto_forwarding_form( );
		die( );
	}
	
	private function process_login_user( ){
		$recaptcha_valid = true;
		if( get_option( 'ec_option_enable_recaptcha' ) && get_option( 'ec_option_enable_recaptcha_cart' ) ){
			
            if( !isset( $_POST['ec_grecaptcha_response_login'] ) || $_POST['ec_grecaptcha_response_login'] == '' ){
                header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info&account_error=login_failed" );
                die( );
                
            }
            
            $db = new ec_db_admin( );
            $recaptcha_response = $_POST['ec_grecaptcha_response_login'];
            
            $data = array(
				"secret"	=> get_option( 'ec_option_recaptcha_secret_key' ),
				"response"	=> $recaptcha_response
			);
			$verify = curl_init();
			curl_setopt( $verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify" );
			curl_setopt( $verify, CURLOPT_POST, true );
			curl_setopt( $verify, CURLOPT_POSTFIELDS, http_build_query( $data ) );
			curl_setopt( $verify, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $verify, CURLOPT_RETURNTRANSFER, true );
			$response = curl_exec( $verify );
            if( $response === false ){
                $db->insert_response( 0, 1, "GOOGLE RECAPTCHA CURL ERROR", curl_error( $verify ) );
                $response = (object) array( "error" => curl_error( $verify ) );
            }else{
                $response = json_decode( $response );
                $db->insert_response( 0, 0, "Google Recaptcha Response", print_r( $response, true ) );
            }
            curl_close( $verify );
            
			$recaptcha_valid = ( isset( $response->success ) && $response->success ) ? true : false;
		}
		
		if( $recaptcha_valid ){
		
            $email = trim( $_POST['ec_cart_login_email'] );
            $password = $_POST['ec_cart_login_password'];
            $password_hash = md5( $password );
            $password_hash = apply_filters( 'wpeasycart_password_hash', $password_hash, $password );

            do_action( 'wpeasycart_pre_login_attempt', $email );
            $user = $this->mysqli->get_user_login( $email, $password, $password_hash );

            if( $user && $user->user_level == "pending" ){

                $GLOBALS['ec_cart_data']->save_session_to_db( );
                do_action( 'wpeasycart_cart_updated' );
                if( isset( $_POST['ec_cart_subscription'] ) ){	
                    header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&ec_cart_error=not_activated&subscription=" . $_POST['ec_cart_subscription'] );
                }else{
                    header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info&ec_cart_error=not_activated");
                }

            }else if( $email == "guest"){
                $GLOBALS['ec_cart_data']->cart_data->email = "guest";
                $GLOBALS['ec_cart_data']->cart_data->username = "guest";
                $GLOBALS['ec_cart_data']->save_session_to_db( );	
                do_action( 'wpeasycart_cart_updated' );
                if( isset( $_POST['ec_cart_subscription'] ) ){
                    header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $_POST['ec_cart_subscription'] );
                }else{
                    header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info");
                }
            }else if( $user ){
                if( apply_filters( 'wp_easycart_sync_wordpress_users', false ) ){
                    $wp_user = wp_signon( array( 'user_login' => $email, 'user_password' => $_POST['ec_cart_login_password'] ), false );
                }

                do_action( 'wpeasycart_login_success', $email );
                $GLOBALS['ec_cart_data']->cart_data->billing_first_name = $user->billing_first_name;
                $GLOBALS['ec_cart_data']->cart_data->billing_last_name = $user->billing_last_name;
                $GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = $user->billing_address_line_1;
                $GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = $user->billing_address_line_2;
                $GLOBALS['ec_cart_data']->cart_data->billing_city = $user->billing_city;
                $GLOBALS['ec_cart_data']->cart_data->billing_state = $user->billing_state;
                $GLOBALS['ec_cart_data']->cart_data->billing_zip = $user->billing_zip;
                $GLOBALS['ec_cart_data']->cart_data->billing_country = $user->billing_country;
                $GLOBALS['ec_cart_data']->cart_data->billing_phone = $user->billing_phone;

                $GLOBALS['ec_cart_data']->cart_data->shipping_selector = "";
                $GLOBALS['ec_cart_data']->cart_data->shipping_first_name = $user->shipping_first_name;
                $GLOBALS['ec_cart_data']->cart_data->shipping_last_name = $user->shipping_last_name;
                $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = $user->shipping_address_line_1;
                $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = $user->shipping_address_line_2;
                $GLOBALS['ec_cart_data']->cart_data->shipping_city = $user->shipping_city;
                $GLOBALS['ec_cart_data']->cart_data->shipping_state = $user->shipping_state;
                $GLOBALS['ec_cart_data']->cart_data->shipping_zip = $user->shipping_zip;
                $GLOBALS['ec_cart_data']->cart_data->shipping_country = $user->shipping_country;
                $GLOBALS['ec_cart_data']->cart_data->shipping_phone = $user->shipping_phone;
                $GLOBALS['ec_cart_data']->cart_data->is_guest = "";
                $GLOBALS['ec_cart_data']->cart_data->guest_key = "";

                $GLOBALS['ec_cart_data']->cart_data->user_id = $user->user_id;
                $GLOBALS['ec_cart_data']->cart_data->email = $email;
                $GLOBALS['ec_cart_data']->cart_data->username = $user->first_name . " " . $user->last_name;
                $GLOBALS['ec_cart_data']->cart_data->first_name = $user->first_name;
                $GLOBALS['ec_cart_data']->cart_data->last_name = $user->last_name;

                $GLOBALS['ec_cart_data']->save_session_to_db( );
                do_action( 'wpeasycart_cart_updated' );
                if( $GLOBALS['ec_cart_data']->cart_data->cart_subscription != "" ){
                    header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $_POST['ec_cart_subscription'] );
                }else if( isset( $_POST['ec_cart_model_number'] ) ){
                    header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $_POST['ec_cart_model_number'] );
                }else{
                    header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info");
                }

            }else{
                do_action( 'wpeasycart_login_failed', $email );
                $GLOBALS['ec_cart_data']->save_session_to_db( );
                if( isset( $_POST['ec_cart_subscription'] ) ){
                    header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $_POST['ec_cart_subscription'] . "&ec_cart_error=login_failed" );
                }else{
                    header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info&ec_cart_error=login_failed");
                }
            }
        }
	}
	
	private function process_logout_user( ){
	
		$GLOBALS['ec_cart_data']->cart_data->user_id = "";
		$GLOBALS['ec_cart_data']->cart_data->email = "";
		$GLOBALS['ec_cart_data']->cart_data->username = "";
		$GLOBALS['ec_cart_data']->cart_data->first_name = "";
		$GLOBALS['ec_cart_data']->cart_data->last_name = "";
		
		$GLOBALS['ec_cart_data']->cart_data->is_guest = "";
		$GLOBALS['ec_cart_data']->cart_data->guest_key = "";
		
		$GLOBALS['ec_cart_data']->cart_data->billing_first_name = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_last_name = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_city = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_state = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_zip = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_country = "";
		$GLOBALS['ec_cart_data']->cart_data->billing_phone = "";
		
		$GLOBALS['ec_cart_data']->cart_data->shipping_selector = "";
		
		$GLOBALS['ec_cart_data']->cart_data->shipping_first_name = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_last_name = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_city = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_state = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_zip = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_country = "";
		$GLOBALS['ec_cart_data']->cart_data->shipping_phone = "";
		
		$GLOBALS['ec_cart_data']->cart_data->first_name = "";
		$GLOBALS['ec_cart_data']->cart_data->last_name = "";
		
		$GLOBALS['ec_cart_data']->cart_data->create_account = "";
		
		$GLOBALS['ec_cart_data']->cart_data->order_notes = "";
		
		$GLOBALS['ec_cart_data']->cart_data->shipping_method = "";
		$GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip = "";
		$GLOBALS['ec_cart_data']->cart_data->estimate_shipping_country = "";
		
		$GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id = "";
		$GLOBALS['ec_cart_data']->cart_data->stripe_pi_client_secret = "";
		
		$GLOBALS['ec_cart_data']->save_session_to_db( );
				
		wp_cache_flush( );
		
		if( apply_filters( 'wp_easycart_sync_wordpress_users', false ) ){
			wp_logout( );
		}
		
		if( isset( $_GET['subscription'] ) ){
			header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $_GET['subscription'] );
		}else if( !get_option( 'ec_option_skip_cart_login' ) && file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/admin_panel.php" ) ){
			header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_login");
		}else{
			header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info");
		}
	}
	
	private function process_save_checkout_info( ){
		
		if( isset( $_POST['ec_login_selector'] ) ){
			$this->process_login_user( );
			
		}else{
			$this->process_save_checkout_info_helper( );
		}
		
		do_action( 'wpeasycart_user_updated' );
		
	}
	
	private function process_save_checkout_info_helper( ){
		
		$recaptcha_valid = true;
        if( get_option( 'ec_option_enable_recaptcha' ) && get_option( 'ec_option_enable_recaptcha_cart' ) ){

            if( !isset( $_POST['ec_grecaptcha_response_register'] ) || $_POST['ec_grecaptcha_response_register'] == '' ){
                header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info&cart_error=register_invalid" );
                die( );
            }

            $db = new ec_db_admin( );
            $recaptcha_response = $_POST['ec_grecaptcha_response_register'];
            $data = array(
                "secret"	=> get_option( 'ec_option_recaptcha_secret_key' ),
                "response"	=> $recaptcha_response
            );
            $verify = curl_init();
            curl_setopt( $verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify" );
            curl_setopt( $verify, CURLOPT_POST, true );
            curl_setopt( $verify, CURLOPT_POSTFIELDS, http_build_query( $data ) );
            curl_setopt( $verify, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $verify, CURLOPT_RETURNTRANSFER, true );
            $response = curl_exec( $verify );
            if( $response === false ){
                $db->insert_response( 0, 1, "GOOGLE RECAPTCHA CURL ERROR", curl_error( $verify ) );
                $response = (object) array( "error" => curl_error( $verify ) );
            }else{
                $response = json_decode( $response );
                $db->insert_response( 0, 0, "Google Recaptcha Response", print_r( $response, true ) );
            }
            curl_close( $verify );

            $recaptcha_valid = ( isset( $response->success ) && $response->success ) ? true : false;
        }

        if( $recaptcha_valid ){
        
            $billing_country = $shipping_country = stripslashes( $_POST['ec_cart_billing_country'] );

            $billing_first_name = $shipping_first_name = stripslashes( $_POST['ec_cart_billing_first_name'] );
            $billing_last_name = $shipping_last_name = stripslashes( $_POST['ec_cart_billing_last_name'] );

            if( isset( $_POST['ec_cart_billing_company_name'] ) ){
                $billing_company_name = $shipping_company_name = stripslashes( $_POST['ec_cart_billing_company_name'] );
            }else{
                $billing_company_name = $shipping_company_name = "";
            }

            if( isset( $_POST['ec_cart_billing_vat_registration_number'] ) ){
                $vat_registration_number = stripslashes( $_POST['ec_cart_billing_vat_registration_number'] );
            }else{
                $vat_registration_number = "";
            }

            $billing_address = $shipping_address = stripslashes( $_POST['ec_cart_billing_address'] );
            if( isset( $_POST['ec_cart_billing_address2'] ) ){
                $billing_address2 = $shipping_address2 = stripslashes( $_POST['ec_cart_billing_address2'] );
            }else{
                $billing_address2 = $shipping_address2 = "";
            }

            $billing_city = $shipping_city = stripslashes( $_POST['ec_cart_billing_city'] );
            if( isset( $_POST['ec_cart_billing_state_' . $billing_country] ) ){
                $billing_state = $shipping_state = stripslashes( $_POST['ec_cart_billing_state_' . $billing_country] );
            }else{
                $billing_state = $shipping_state = stripslashes( $_POST['ec_cart_billing_state'] );
            }

            $billing_zip = $shipping_zip = trim( stripslashes( $_POST['ec_cart_billing_zip'] ) );
            if( isset( $_POST['ec_cart_billing_phone'] ) ){
                $billing_phone = $shipping_phone = stripslashes( $_POST['ec_cart_billing_phone'] );
            }else{
                $billing_phone = "";
            }

            if( isset( $_POST['ec_shipping_selector'] ) )
                $shipping_selector = $_POST['ec_shipping_selector'];
            else
                $shipping_selector = "false";

            if( $shipping_selector == "true" ){
                $shipping_country = stripslashes( $_POST['ec_cart_shipping_country'] );

                $shipping_first_name = stripslashes( $_POST['ec_cart_shipping_first_name'] );
                $shipping_last_name = stripslashes( $_POST['ec_cart_shipping_last_name'] );

                if( isset( $_POST['ec_cart_shipping_company_name'] ) ){
                    $shipping_company_name = stripslashes( $_POST['ec_cart_shipping_company_name'] );
                }else{
                    $shipping_company_name = "";
                }

                $shipping_address = stripslashes( $_POST['ec_cart_shipping_address'] );
                if( isset( $_POST['ec_cart_shipping_address2'] ) ){
                    $shipping_address2 = stripslashes( $_POST['ec_cart_shipping_address2'] );
                }else{
                    $shipping_address2 = "";
                }

                $shipping_city = stripslashes( $_POST['ec_cart_shipping_city'] );

                if( isset( $_POST['ec_cart_shipping_state_' . $shipping_country] ) ){
                    $shipping_state = stripslashes( $_POST['ec_cart_shipping_state_' . $shipping_country] );
                }else{
                    $shipping_state = stripslashes( $_POST['ec_cart_shipping_state'] );
                }

                $shipping_zip = trim( stripslashes( $_POST['ec_cart_shipping_zip'] ) );
                if( isset( $_POST['ec_cart_shipping_phone'] ) ){
                    $shipping_phone = stripslashes( $_POST['ec_cart_shipping_phone'] );
                }else{
                    $shipping_phone = "";
                }
            }

            if( isset( $_POST['ec_order_notes'] ) ){
                $order_notes = stripslashes( $_POST['ec_order_notes'] );
            }else if( $GLOBALS['ec_cart_data']->cart_data->order_notes != "" ){
                $order_notes = $GLOBALS['ec_cart_data']->cart_data->order_notes;
            }else{
                $order_notes = "";
            }

            if( isset( $_POST['ec_contact_first_name'] ) ){
                $first_name = stripslashes( $_POST['ec_contact_first_name'] );
            }else if( isset( $_POST['ec_cart_billing_first_name'] ) ){
                $first_name = stripslashes( $_POST['ec_cart_billing_first_name'] );
            }else{
                $first_name = "";
            }
            if( isset( $_POST['ec_contact_last_name'] ) ){
                $last_name = stripslashes( $_POST['ec_contact_last_name'] );
            }else if( isset( $_POST['ec_cart_billing_last_name'] ) ){
                $last_name = stripslashes( $_POST['ec_cart_billing_last_name'] );
            }else{
                $last_name = "";
            }

            if( isset( $_POST['ec_contact_create_account'] ) )
                $create_account = $_POST['ec_contact_create_account'];
            else if( isset( $_POST['ec_create_account_selector'] ) )
                $create_account = true;
            else
                $create_account = false;

            $GLOBALS['ec_cart_data']->cart_data->billing_first_name = $billing_first_name;
            $GLOBALS['ec_cart_data']->cart_data->billing_last_name = $billing_last_name;
            $GLOBALS['ec_cart_data']->cart_data->billing_company_name = $billing_company_name;
            $GLOBALS['ec_cart_data']->cart_data->vat_registration_number = $vat_registration_number;
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = $billing_address;
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = $billing_address2;
            $GLOBALS['ec_cart_data']->cart_data->billing_city = $billing_city;
            $GLOBALS['ec_cart_data']->cart_data->billing_state = $billing_state;
            $GLOBALS['ec_cart_data']->cart_data->billing_zip = $billing_zip;
            $GLOBALS['ec_cart_data']->cart_data->billing_country = $billing_country;
            $GLOBALS['ec_cart_data']->cart_data->billing_phone = $billing_phone;

            $GLOBALS['ec_cart_data']->cart_data->shipping_selector = $shipping_selector;

            $GLOBALS['ec_cart_data']->cart_data->shipping_first_name = $shipping_first_name;
            $GLOBALS['ec_cart_data']->cart_data->shipping_last_name = $shipping_last_name;
            $GLOBALS['ec_cart_data']->cart_data->shipping_company_name = $shipping_company_name;
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = $shipping_address;
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = $shipping_address2;
            $GLOBALS['ec_cart_data']->cart_data->shipping_city = $shipping_city;
            $GLOBALS['ec_cart_data']->cart_data->shipping_state = $shipping_state;
            $GLOBALS['ec_cart_data']->cart_data->shipping_zip = $shipping_zip;
            $GLOBALS['ec_cart_data']->cart_data->shipping_country = $shipping_country;
            $GLOBALS['ec_cart_data']->cart_data->shipping_phone = $shipping_phone;

            $GLOBALS['ec_cart_data']->cart_data->first_name = $first_name;
            $GLOBALS['ec_cart_data']->cart_data->last_name = $last_name;

            $GLOBALS['ec_cart_data']->cart_data->order_notes = $order_notes;

            $next_page = "checkout_shipping";
            if( !get_option( 'ec_option_use_shipping' ) || $this->cart->shippable_total_items == 0 )
                $next_page = "checkout_payment";

            if( get_option( 'ec_option_skip_shipping_page' ) || $GLOBALS['ec_user']->freeshipping )//|| $this->discount->shipping_discount == $this->discount->shipping_subtotal )
                $next_page = "checkout_payment";

            if( isset( $_POST['ec_contact_email'] ) ){
                $email = $_POST['ec_contact_email'];
                $GLOBALS['ec_cart_data']->cart_data->email = $email;
            }

            if( isset( $_POST['ec_contact_email'] ) && !$create_account ){
                $GLOBALS['ec_cart_data']->cart_data->is_guest = true;
                $GLOBALS['ec_cart_data']->cart_data->guest_key = $GLOBALS['ec_cart_data']->ec_cart_id;
            }else if( isset( $_POST['ec_contact_email'] ) ){
                $GLOBALS['ec_cart_data']->cart_data->is_guest = true;
                $GLOBALS['ec_cart_data']->cart_data->guest_key = $GLOBALS['ec_cart_data']->ec_cart_id;
            }else{
                $GLOBALS['ec_cart_data']->cart_data->is_guest = false;
                $GLOBALS['ec_cart_data']->cart_data->guest_key = "";
            }

            $GLOBALS['ec_cart_data']->save_session_to_db( );	

            if( !$this->validate_checkout_data( ) ){

                header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info&ec_cart_error=invalid_address" );

            }else{

                if( $create_account ){

                    if( $this->mysqli->does_user_exist( $_POST['ec_contact_email'] ) ){
                        do_action( 'wpeasycart_cart_updated' );	
                        $GLOBALS['ec_cart_data']->save_session_to_db( );
                        header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info&ec_cart_error=email_exists");

                    }else{
                        $email = $_POST['ec_contact_email'];
                        $password = md5( $_POST['ec_contact_password'] );
                        $password = apply_filters( 'wpeasycart_password_hash', $password, $_POST['ec_contact_password'] );

                        // INSERT USER
                        $billing_id = $this->mysqli->insert_address( $billing_first_name, $billing_last_name, $billing_address, $billing_address2, $billing_city, $billing_state, $billing_zip, $billing_country, $billing_phone, $billing_company_name );

                        $shipping_id = $this->mysqli->insert_address( $shipping_first_name, $shipping_last_name, $shipping_address, $shipping_address2, $shipping_city, $shipping_state, $shipping_zip, $shipping_country, $shipping_phone, $shipping_company_name );

                        $user_level = "shopper";
                        if( isset( $_POST['ec_cart_is_subscriber'] ) )
                            $is_subscriber = true;
                        else
                            $is_subscriber = false;

                        $user_id = $this->mysqli->insert_user( $email, $password, $first_name, $last_name, $billing_id, $shipping_id, $user_level, $is_subscriber, "", $vat_registration_number );
                        if( $user_id != 0 ){
                            $this->mysqli->update_address_user_id( $billing_id, $user_id );
                            $this->mysqli->update_address_user_id( $shipping_id, $user_id );

                            // MyMail Hook
                            if( function_exists( 'mailster' ) ){
                                $subscriber_id = mailster('subscribers')->add(array(
                                    'firstname' => $first_name,
                                    'lastname' => $last_name,
                                    'email' => $email,
                                    'status' => 1,
                                ), false );
                            }

                            // Maybe insert WP user
                            if( apply_filters( 'wp_easycart_sync_wordpress_users', false ) ){
                                $user_name = 'wpeasycart_' . $user_id;
                                $wp_user_id = wp_create_user( $user_name, $_POST['ec_contact_password'], $email );
                                add_user_meta( $wp_user_id, 'wpeasycart_user_id', $user_id, true );
                            }

                            do_action( 'wpeasycart_account_added', $user_id, $email, $_POST['ec_contact_password'] );

                            // Send registration email if needed
                            if( get_option( 'ec_option_send_signup_email' ) ){

                                $headers   = array();
                                $headers[] = "MIME-Version: 1.0";
                                $headers[] = "Content-Type: text/html; charset=utf-8";
                                $headers[] = "From: " . stripslashes( get_option( 'ec_option_order_from_email' ) );
                                $headers[] = "Reply-To: " . stripslashes( get_option( 'ec_option_order_from_email' ) );
                                $headers[] = "X-Mailer: PHP/" . phpversion( );

                                $message = $GLOBALS['language']->get_text( "account_register", "account_register_email_message" ) . " " . $email;

                                if( get_option( 'ec_option_use_wp_mail' ) ){
                                    wp_mail( stripslashes( get_option( 'ec_option_bcc_email_addresses' ) ), $GLOBALS['language']->get_text( "account_register", "account_register_email_title" ), $message, implode("\r\n", $headers) );
                                }else{
                                    $admin_email = stripslashes( get_option( 'ec_option_bcc_email_addresses' ) );
                                    $subject = $GLOBALS['language']->get_text( "account_register", "account_register_email_title" );
                                    $mailer = new wpeasycart_mailer( );
                                    $mailer->send_order_email( $admin_email, $subject, $message );
                                }

                            }

                            $GLOBALS['ec_cart_data']->cart_data->is_guest = false;
                            $GLOBALS['ec_cart_data']->cart_data->user_id = $user_id;
                            $GLOBALS['ec_cart_data']->cart_data->email = $email;
                            $GLOBALS['ec_cart_data']->cart_data->username = $first_name . " " . $last_name;
                            $GLOBALS['ec_cart_data']->cart_data->first_name = $first_name;
                            $GLOBALS['ec_cart_data']->cart_data->last_name = $last_name;

                            if( $this->shipping->validate_address( $shipping_address, $shipping_city, $shipping_state, $shipping_zip, $shipping_country ) ){
                                $GLOBALS['ec_cart_data']->cart_data->is_guest = "";
                                $GLOBALS['ec_cart_data']->cart_data->guest_key = "";

                                $GLOBALS['ec_cart_data']->save_session_to_db( );
                                do_action( 'wpeasycart_cart_updated' );	
                                if( !$this->validate_tax_cloud( ) ){
                                    header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info&ec_cart_error=invalid_address" );

                                }else if( !$this->validate_vat_registration_number( $vat_registration_number ) ){
                                    header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info&ec_cart_error=invalid_vat_number" );

                                }else{
                                    header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=" . $next_page . "&ec_cart_success=account_created");

                                }

                            }else{
                                $GLOBALS['ec_cart_data']->save_session_to_db( );
                                do_action( 'wpeasycart_cart_updated' );	
                                header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info&ec_cart_success=account_created&ec_cart_error=invalid_address");
                            }

                        }else{
                            $GLOBALS['ec_cart_data']->save_session_to_db( );
                            header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info&ec_cart_error=email_exists");
                        }

                    }

                }else{

                    $this->mysqli->update_user( $GLOBALS['ec_user']->user_id, $vat_registration_number );

                    if( $this->shipping->validate_address( $shipping_address, $shipping_city, $shipping_state, $shipping_zip, $shipping_country ) ){

                        if( $GLOBALS['ec_user']->billing_id ){
                            $this->mysqli->update_address( $GLOBALS['ec_user']->billing_id, $billing_first_name, $billing_last_name, $billing_address, $billing_address2, $billing_city, $billing_state, $billing_zip, $billing_country, $billing_phone, $billing_company_name );

                        }else{
                            $this->mysqli->insert_user_address( $billing_first_name, $billing_last_name, $billing_company_name, $billing_address, $billing_address2, $billing_city, $billing_state, $billing_zip, $billing_country, $billing_phone, $GLOBALS['ec_user']->user_id, "billing" );
                        }

                        if( $GLOBALS['ec_user']->shipping_id ){
                                $this->mysqli->update_address( $GLOBALS['ec_user']->shipping_id, $shipping_first_name, $shipping_last_name, $shipping_address, $shipping_address2, $shipping_city, $shipping_state, $shipping_zip, $shipping_country, $shipping_phone, $shipping_company_name );

                        }else{
                            $this->mysqli->insert_user_address( $shipping_first_name, $shipping_last_name, $shipping_company_name, $shipping_address, $shipping_address2, $shipping_city, $shipping_state, $shipping_zip, $shipping_country, $shipping_phone, $GLOBALS['ec_user']->user_id, "shipping" );

                        }

                        $GLOBALS['ec_cart_data']->save_session_to_db( );

                        do_action( 'wpeasycart_cart_updated' );

                        do_action( 'wpeasycart_account_updated', $GLOBALS['ec_user']->user_id );

                        if( !$this->validate_tax_cloud( ) ){
                            header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info&ec_cart_error=invalid_address" );

                        }else if( !$this->validate_vat_registration_number( $vat_registration_number ) ){
                            header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info&ec_cart_error=invalid_vat_number" );

                        }else{
                            header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=" . $next_page);

                        }

                    }else{
                        $GLOBALS['ec_cart_data']->save_session_to_db( );
                        do_action( 'wpeasycart_cart_updated' );
                        header("location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_info&ec_cart_error=invalid_address");
                    }

                }

            }
            
        } // close recaptcha check
		
	}
	
	private function process_save_checkout_shipping( ){
		if( isset( $_POST['ec_cart_shipping_method'] ) )
			$shipping_method = $_POST['ec_cart_shipping_method'];
		else
			$shipping_method = "";
		if( isset( $_POST['ec_cart_ship_express'] ) )
			$ship_express = $_POST['ec_cart_ship_express'];
		else
			$ship_express = "";
		
		$GLOBALS['ec_cart_data']->cart_data->shipping_method = $shipping_method;
		$GLOBALS['ec_cart_data']->cart_data->expedited_shipping = $ship_express;
		
		$GLOBALS['ec_cart_data']->save_session_to_db( );
		
		do_action( 'wpeasycart_cart_updated' );
		$url = $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment";
		if( isset( $_POST['paypal_payment_id'] ) && isset( $_POST['paypal_payer_id'] ) && isset( $_POST['paypal_payment_method'] ) )
			$url .= '&PID=' . preg_replace( "/[^A-Za-z0-9\-]/", '', $_POST['paypal_payment_id'] ) . '&PYID=' . preg_replace( "/[^A-Za-z0-9\-]/", '', $_POST['paypal_payer_id'] ) . '&PMETH=' . preg_replace( "/[^A-Za-z0-9\_]/", '', $_POST['paypal_payment_method'] );
		header( "location: " . $url );
	}
	
	private function process_purchase_subscription( ){
		
		$model_number = 0;
		if( isset( $_POST['model_number'] ) )
			$model_number = $_POST['model_number'];
		
		header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $model_number );
		
	}
	
	private function process_insert_subscription( ){
		
		if( isset( $_POST['ec_login_selector'] ) ){
			$this->process_login_user( );
			
		}else{
			$this->process_insert_subscription_helper( );
		}
		
	}
	
	private function process_insert_subscription_helper( ){
		
		global $wpdb;
		$model_number = $_POST['ec_cart_model_number'];
		$products = $this->mysqli->get_product_list( $wpdb->prepare( " WHERE product.model_number = %s", $model_number ), "", "", "" );
		
		$user_error = false;
		if( isset( $_POST['ec_contact_email'] ) ){
			$user_error = $this->mysqli->does_user_exist( $_POST['ec_contact_email'] );
		}
		
		// If checkout out as new user and the email already exists, this is an error.
		if( !$user_error ){
		
			if( count( $products > 0 ) ){
				
				// Try to get a subscription for this product and email address!
				if( isset( $_POST['ec_contact_email'] ) )	$email_test = $_POST['ec_contact_email'];
				else										$email_test = $GLOBALS['ec_cart_data']->cart_data->email;
				
				$subscription_list = $this->mysqli->find_subscription_match( $email_test, $products[0]['product_id'] );
				
				// Coupon Information
				$coupon = NULL;
				$discount_total = 0;
				$is_match = false;
				if( isset( $_POST['ec_cart_coupon_code'] ) && $_POST['ec_cart_coupon_code'] != "" ){
					$coupon_row = $GLOBALS['ec_coupons']->redeem_coupon_code( $_POST['ec_cart_coupon_code'] );
					$is_match = false;
					if( $coupon_row->by_product_id ){
						if( $products[0]['product_id'] == $coupon_row->product_id ){
							$is_match = true;
						}
					}else if( $coupon_row->by_manufacturer_id ){
						if( $products[0]['manufacturer_id'] == $coupon_row->manufacturer_id ){
							$is_match = true;
						}
					}else{
						$is_match = true;
					}
					
					if( $is_match ){
						$coupon = $coupon_row->promocode_id;
					}
				}else if( isset( $_POST['ec_coupon_code'] ) && $_POST['ec_coupon_code'] != "" ){
					$coupon_row = $GLOBALS['ec_coupons']->redeem_coupon_code( $_POST['ec_coupon_code'] );
					$is_match = false;
					if( $coupon_row->by_product_id ){
						if( $products[0]['product_id'] == $coupon_row->product_id ){
							$is_match = true;
						}
					}else if( $coupon_row->by_manufacturer_id ){
						if( $products[0]['manufacturer_id'] == $coupon_row->manufacturer_id ){
							$is_match = true;
						}
					}else{
						$is_match = true;
					}
					
					if( $is_match ){
						$coupon = $coupon_row->promocode_id;
					}
				
				}
				// END COUPON FIND SECTION
				
				// IF MATCH FOUND, APPLY TO PRODUCT
				if( $is_match ){
					
					if( $coupon_row->is_dollar_based ){
						$discount_total = floatval( $coupon_row->promo_dollar );
						
					}else if( $coupon_row->is_percentage_based ){
						$discount_total = ( floatval( $products[0]['price'] ) * ( floatval( $coupon_row->promo_percentage ) / 100 ) );
						
					}
			
				}
				// END MATCHING COUPON SECTION
			
				// Billing Information
				$billing_country = $shipping_country = stripslashes( $_POST['ec_cart_billing_country'] );
	
				$billing_first_name = $shipping_first_name = stripslashes( $_POST['ec_cart_billing_first_name'] );
				$billing_last_name = $shipping_last_name = stripslashes( $_POST['ec_cart_billing_last_name'] );
					
				if( isset( $_POST['ec_cart_billing_company_name'] ) ){
					$billing_company_name = $shipping_company_name = stripslashes( $_POST['ec_cart_billing_company_name'] );
				}else{
					$billing_company_name = $shipping_company_name = "";
				}
				
				$billing_address = $shipping_address = stripslashes( $_POST['ec_cart_billing_address'] );
				if( isset( $_POST['ec_cart_billing_address2'] ) ){
					$billing_address2 = $shipping_address2 = stripslashes( $_POST['ec_cart_billing_address2'] );
				}else{
					$billing_address2 = $shipping_address2 = "";
				}
				
				$billing_city = $shipping_city = stripslashes( $_POST['ec_cart_billing_city'] );
				if( isset( $_POST['ec_cart_billing_state_' . $billing_country] ) ){
					$billing_state = $shipping_state = stripslashes( $_POST['ec_cart_billing_state_' . $billing_country] );
				}else{
					$billing_state = $shipping_state = stripslashes( $_POST['ec_cart_billing_state'] );
				}
				
				$billing_zip = $shipping_zip = stripslashes( $_POST['ec_cart_billing_zip'] );
				if( isset( $_POST['ec_cart_billing_phone'] ) ){
					$billing_phone = $shipping_phone = stripslashes( $_POST['ec_cart_billing_phone'] );
				}else{
					$billing_phone = "";
				}
				// END BILLING INFO
				
				// Shipping Information
				if( isset( $_POST['ec_shipping_selector'] ) )
					$shipping_selector = $_POST['ec_shipping_selector'];
				else
					$shipping_selector = "false";
				
				if( $shipping_selector == "true" ){
					$shipping_country = stripslashes( $_POST['ec_cart_shipping_country'] );
					
					$shipping_first_name = stripslashes( $_POST['ec_cart_shipping_first_name'] );
					$shipping_last_name = stripslashes( $_POST['ec_cart_shipping_last_name'] );
					
					if( isset( $_POST['ec_cart_shipping_company_name'] ) ){
						$shipping_company_name = stripslashes( $_POST['ec_cart_shipping_company_name'] );
					}else{
						$shipping_company_name = "";
					}
					
					$shipping_address = stripslashes( $_POST['ec_cart_shipping_address'] );
					if( isset( $_POST['ec_cart_shipping_address2'] ) ){
						$shipping_address2 = stripslashes( $_POST['ec_cart_shipping_address2'] );
					}else{
						$shipping_address2 = "";
					}
					
					$shipping_city = stripslashes( $_POST['ec_cart_shipping_city'] );
					
					if( isset( $_POST['ec_cart_shipping_state_' . $shipping_country] ) ){
						$shipping_state = stripslashes( $_POST['ec_cart_shipping_state_' . $shipping_country] );
					}else{
						$shipping_state = stripslashes( $_POST['ec_cart_shipping_state'] );
					}
					
					$shipping_zip = stripslashes( $_POST['ec_cart_shipping_zip'] );
					if( isset( $_POST['ec_cart_shipping_phone'] ) ){
						$shipping_phone = stripslashes( $_POST['ec_cart_shipping_phone'] );
					}else{
						$shipping_phone = "";
					}
				}
				// END SHIPPING INFO
				
				// Order Notes
				if( isset( $_POST['ec_order_notes'] ) ){
					$order_notes = stripslashes( $_POST['ec_order_notes'] );
				}else{
					$order_notes = "";
				}
				
				// Create Account Information
				if( isset( $_POST['ec_contact_first_name'] ) ){
					$first_name = stripslashes( $_POST['ec_contact_first_name'] );
				}else if( isset( $_POST['ec_cart_billing_first_name'] ) ){
					$first_name = stripslashes( $_POST['ec_cart_billing_first_name'] );
				}else{
					$first_name = "";
				}
				if( isset( $_POST['ec_contact_last_name'] ) ){
					$last_name = stripslashes( $_POST['ec_contact_last_name'] );
				}else if( isset( $_POST['ec_cart_billing_last_name'] ) ){
					$last_name = stripslashes( $_POST['ec_cart_billing_last_name'] );
				}else{
					$last_name = "";
				}
				
				if( isset( $_POST['ec_contact_create_account'] ) )
					$create_account = $_POST['ec_contact_create_account'];
				else if( isset( $_POST['ec_create_account_selector'] ) )
					$create_account = true;
				else
					$create_account = false;
				
				
				// CREATE ACCOUNT IF NEEDED
				if( isset( $_POST['ec_contact_email'] ) ){
					$email = $_POST['ec_contact_email'];
					$GLOBALS['ec_cart_data']->cart_data->email = $email;
				}
				
				if( isset( $_POST['ec_contact_email'] ) && !$create_account ){
					$GLOBALS['ec_cart_data']->cart_data->is_guest = true;
					$GLOBALS['ec_cart_data']->cart_data->guest_key = $GLOBALS['ec_cart_data']->ec_cart_id;
				}else{
					$GLOBALS['ec_cart_data']->cart_data->is_guest = false;
				}
				
				if( $create_account ){
					$email = $_POST['ec_contact_email'];
					$password = md5( $_POST['ec_contact_password'] );
					$password = apply_filters( 'wpeasycart_password_hash', $password, $_POST['ec_contact_password'] );
					
					// INSERT USER
					$billing_id = $this->mysqli->insert_address( $billing_first_name, $billing_last_name, $billing_address, $billing_address2, $billing_city, $billing_state, $billing_zip, $billing_country, $billing_phone, $billing_company_name );
					
					$shipping_id = $this->mysqli->insert_address( $shipping_first_name, $shipping_last_name, $shipping_address, $shipping_address2, $shipping_city, $shipping_state, $shipping_zip, $shipping_country, $shipping_phone, $shipping_company_name );
					
					$user_level = "shopper";
					if( isset( $_POST['ec_contact_is_subscriber'] ) )
						$is_subscriber = true;
					else
						$is_subscriber = false;
					
					$user_id = $this->mysqli->insert_user( $email, $password, $first_name, $last_name, $billing_id, $shipping_id, $user_level, $is_subscriber );
					$this->mysqli->update_address_user_id( $billing_id, $user_id );
					$this->mysqli->update_address_user_id( $shipping_id, $user_id );
		
					do_action( 'wpeasycart_account_added', $user_id, $email, $_POST['ec_contact_password'] );
		
					// MyMail Hook
					if( function_exists( 'mailster' ) ){
						$subscriber_id = mailster('subscribers')->add(array(
							'firstname' => $first_name,
							'lastname' => $last_name,
							'email' => $email,
							'status' => 1,
						), false );
					}
					
					if( $user_id != 0 ){
					
						$GLOBALS['ec_cart_data']->cart_data->user_id = $user_id;
						$GLOBALS['ec_cart_data']->cart_data->email = $email;
						$GLOBALS['ec_cart_data']->cart_data->username = $first_name . " " . $last_name;
						$GLOBALS['ec_cart_data']->cart_data->first_name = $first_name;
						$GLOBALS['ec_cart_data']->cart_data->last_name = $last_name;
				
						$GLOBALS['ec_user'] = new ec_user( "" );
						
					}
				}else{ // Customer already exists, lets update their billing address
					$user = new ec_user( "" );
					$this->mysqli->update_address( $user->billing_id, $billing_first_name, $billing_last_name, $billing_address, $billing_address2, $billing_city, $billing_state, $billing_zip, $billing_country, $billing_phone, $billing_company_name );
				}
				// END CREATE ACCOUNT
				
				// Set Sessions
				$GLOBALS['ec_cart_data']->cart_data->billing_first_name = $billing_first_name;
				$GLOBALS['ec_cart_data']->cart_data->billing_last_name = $billing_last_name;
				$GLOBALS['ec_cart_data']->cart_data->billing_company_name = $billing_company_name;
				$GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = $billing_address;
				$GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = $billing_address2;
				$GLOBALS['ec_cart_data']->cart_data->billing_city = $billing_city;
				$GLOBALS['ec_cart_data']->cart_data->billing_state = $billing_state;
				$GLOBALS['ec_cart_data']->cart_data->billing_zip = $billing_zip;
				$GLOBALS['ec_cart_data']->cart_data->billing_country = $billing_country;
				$GLOBALS['ec_cart_data']->cart_data->billing_phone = $billing_phone;
				
				$GLOBALS['ec_cart_data']->cart_data->shipping_selector = $shipping_selector;
				
				$GLOBALS['ec_cart_data']->cart_data->shipping_first_name = $shipping_first_name;
				$GLOBALS['ec_cart_data']->cart_data->shipping_last_name = $shipping_last_name;
				$GLOBALS['ec_cart_data']->cart_data->shipping_company_name = $shipping_company_name;
				$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = $shipping_address;
				$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = $shipping_address2;
				$GLOBALS['ec_cart_data']->cart_data->shipping_city = $shipping_city;
				$GLOBALS['ec_cart_data']->cart_data->shipping_state = $shipping_state;
				$GLOBALS['ec_cart_data']->cart_data->shipping_zip = $shipping_zip;
				$GLOBALS['ec_cart_data']->cart_data->shipping_country = $shipping_country;
				$GLOBALS['ec_cart_data']->cart_data->shipping_phone = $shipping_phone;
				
				$GLOBALS['ec_cart_data']->cart_data->first_name = $first_name;
				$GLOBALS['ec_cart_data']->cart_data->last_name = $last_name;
				
				$GLOBALS['ec_cart_data']->cart_data->order_notes = $order_notes;
				
				$GLOBALS['ec_cart_data']->save_session_to_db( );
										
				$GLOBALS['ec_user']->setup_billing_info_data( $billing_first_name, $billing_last_name, $billing_address, $billing_address2, $billing_city, $billing_state, $billing_country, $billing_zip, $billing_phone, $billing_company_name );
				$GLOBALS['ec_user']->setup_shipping_info_data( $shipping_first_name, $shipping_last_name, $shipping_address, $shipping_address2, $shipping_city, $shipping_state, $shipping_country, $shipping_zip, $shipping_phone, $shipping_company_name );
				$product = new ec_product( $products[0] );
				$quantity = 1;
				if( isset( $_POST['ec_quantity'] ) )
					$quantity = $_POST['ec_quantity'];
				
				if( count( $subscription_list ) <= 0 ){
					// Setup for processing
					// Setup for processing
					if( class_exists( "ec_stripe" ) || class_exists( "ec_stripe_connect" ) ){
						
						if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
							$stripe = new ec_stripe( );
						else
							$stripe = new ec_stripe_connect( );
						
						// Coupon Check
						if( $coupon && !$coupon_row->is_free_item_based ){
							$coupon_exists = $stripe->get_coupon( $coupon );
							if( $coupon_exists === false ){
								$is_amount_off = false;
								if( $coupon_row->promo_dollar > 0 )
									$is_amount_off = true;
								$redeem_by = NULL;
								if( $coupon_row->expiration_date != '' ){
									$redeem_by = strtotime( $coupon_row->expiration_date ) + 7*60*60;
								}
								$stripe_coupon = array(
									"promocode_id"		=> $coupon_row->promocode_id,
									"duration"			=> $coupon_row->duration,
									"duration_in_months"=> $coupon_row->duration_in_months,
									"is_amount_off"		=> $is_amount_off,
									"amount_off"		=> $coupon_row->promo_dollar * 100,
									"percent_off"		=> $coupon_row->promo_percentage,
									"redeem_by"			=> $redeem_by,
									"max_redemptions"	=> $coupon_row->max_redemptions
								);
								$stripe->insert_coupon( $stripe_coupon );
							}
						
						}else if( $coupon_row->is_free_item_based ){
							$coupon = "";
						}
						
						// Possibly discount the initial fee
						$initial_fee = $product->subscription_signup_fee;
						if( $discount_total > $product->price ){
							$remaining_discount = $discount_total - $product->price;
							$initial_fee = $initial_fee - $remaining_discount;
						}
						
						// Payment Information
						$payment_method = $this->get_payment_type( $this->sanatize_card_number( $_POST['ec_card_number'] ) );
						$card_holder_name = stripslashes( $_POST['ec_cart_billing_first_name'] . " " . $_POST['ec_cart_billing_last_name'] );
						$card_number = $_POST['ec_card_number'];
						if( isset( $_POST['ec_expiration_month'] ) && isset( $_POST['ec_expiration_year'] ) ){
							$exp_month = $_POST['ec_expiration_month'];
							$exp_year = $_POST['ec_expiration_year'];
						}else{
							$exp_date = $_POST['ec_cc_expiration'];
							$exp_month = substr( $exp_date, 0, 2 );
							$exp_year = substr( $exp_date, 5 );
							if( strlen( $exp_year ) == 2 ){
								$exp_year = "20" . $exp_year;
							}
						}
						$security_code = $_POST['ec_security_code'];
						
						$card = new ec_credit_card( $payment_method, $card_holder_name, $card_number, $exp_month, $exp_year, $security_code );
						$customer_id = $GLOBALS['ec_user']->stripe_customer_id;
						
						// Tests vars
						$need_to_update_customer_id = false;
						$customer_insert_test = false;
						
						if( $customer_id == "" ){
							$customer_id = $stripe->insert_customer( $GLOBALS['ec_user'], NULL, $initial_fee );
							$need_to_update_customer_id = true;
						}else{
							$found_customer = $stripe->update_customer( $GLOBALS['ec_user'], $initial_fee );
							if( !$found_customer ){ // Likely switched from test to live or to a new account, so customer id was wrong
								$customer_id = $stripe->insert_customer( $GLOBALS['ec_user'], NULL, $initial_fee );
								$need_to_update_customer_id = true;
							}
						}
						
						if( $need_to_update_customer_id && $customer_id ){ // Customer inserted to stripe successfully
							$this->mysqli->update_user_stripe_id( $GLOBALS['ec_user']->user_id, $customer_id );
							$GLOBALS['ec_user']->stripe_customer_id = $customer_id;
							$customer_insert_test = true;
						}else if( $need_to_update_customer_id && !$customer_id ){
							$customer_insert_test = false;
						}else{
							$customer_insert_test = true;
						}
						
						if( $customer_insert_test ){ // Customer inserted successfully (OR didn't need to be inserted)
							
							if( isset( $_POST['stripeToken'] ) ){
								$card_result = true;
							}else{
								$card_result = $stripe->insert_card( $GLOBALS['ec_user'], $card );
							}	
							
							if( $card_result ){ //Card Submitted Successfully
								
								$plan_added = $product->stripe_plan_added;
								
								if( !$product->stripe_plan_added ){ // Add plan if needed
									$plan_added = $stripe->insert_plan( $product );
									$this->mysqli->update_product_stripe_added( $product->product_id );
								}
								
								if( $plan_added ){ // Plan added successfully
									
									if( $product->is_shippable ){
										$ship_price_total = $product->price * $quantity;
										$ship_weight_total = $product->weight * $quantity;
										$ship_quantity = $quantity;
									}else{
										$ship_price_total = 0;
										$ship_weight_total = 0;
										$ship_quantity = 0;
									}
									
									do_action( 'wpeasycart_cart_subscription_updated', $product, $quantity );
									
									$this->shipping = new ec_shipping( $ship_price_total, $ship_weight_total, $ship_quantity, 'RADIO', $GLOBALS['ec_user']->freeshipping, $product->length, $product->width, $product->height * $quantity, array( $product ) );
									if( get_option( 'ec_option_collect_shipping_for_subscriptions' ) && $product->is_shippable ){
										$this->cart->shippable_total_items = $quantity;
									}
									
									$this->cart->subtotal = ( $product->price + $product->subscription_signup_fee ) * $quantity;
									
									$this->order_totals = new ec_order_totals( $this->cart, $GLOBALS['ec_user'], $this->shipping, $this->tax, $this->discount );
									
									if( $product->is_taxable || $product->vat_rate ){
										$taxable_subtotal = 0;
										$vatable_subtotal = 0;
										if( $product->is_taxable )
											$taxable_subtotal = $product->price * $quantity - $discount_total;
										if( $product->vat_rate )
											$vatable_subtotal = $product->price * $quantity - $discount_total;
										
										if( get_option( 'ec_option_tax_cloud_api_id' ) != "" && get_option( 'ec_option_tax_cloud_api_key' ) != "" ){
											wpeasycart_taxcloud( )->setup_subscription_for_tax( $product, $quantity );
										}
										$this->tax = new ec_tax( $product->price * $quantity, $taxable_subtotal, $vatable_subtotal, $GLOBALS['ec_user']->shipping->state, $GLOBALS['ec_user']->shipping->country, $GLOBALS['ec_user']->taxfree, $this->shipping->get_shipping_price( ( $this->product->handling_price_each * $quantity ) + $this->product->handling_price ), $this->cart );
									}else{
										$this->tax = new ec_tax( 0, 0, 0, $GLOBALS['ec_user']->shipping->state, $GLOBALS['ec_user']->shipping->country, $GLOBALS['ec_user']->taxfree, $this->shipping->get_shipping_price( ( $this->product->handling_price_each * $quantity ) + $this->product->handling_price ), $this->cart );
									}
									
									$this->order_totals = new ec_order_totals( $this->cart, $GLOBALS['ec_user'], $this->shipping, $this->tax, $this->discount );
									
									if( get_option( 'ec_option_collect_shipping_for_subscriptions' ) && $this->order_totals->shipping_total > 0 ){
										$stripe->update_customer( $GLOBALS['ec_user'], $this->order_totals->shipping_total );
									}
									
									$prorate = "false";
									if( $product->subscription_prorate )
										$prorate = "true";
									$trial_end_date = NULL;
									if( $product->trial_period_days > 0 )
										$trial_end_date = strtotime( "+" . $product->trial_period_days . " days" );
									$stripe_response = $stripe->insert_subscription( $product, $GLOBALS['ec_user'], $card, $coupon, $prorate, $trial_end_date, $quantity, number_format( $this->tax->get_tax_rate( ), 2, '.', '' ) );
									
									if( $stripe_response ){ // Subscription added successfully
										
										$subscription_id = $this->mysqli->insert_stripe_subscription( $stripe_response, $product, $GLOBALS['ec_user'], $card, $quantity );
										$subscription_row = $this->mysqli->get_subscription_row( $subscription_id );
										$coupon_promocode_id = "";
										if( isset( $coupon_row ) )
											$coupon_promocode_id = $coupon_row->promocode_id;
										
										$this->mysqli->update_user_default_card( $GLOBALS['ec_user'], $card );
										$subscription = new ec_subscription( $subscription_row );
										
										if( $product->trial_period_days > 0 ){
											
											$subscription->send_trial_start_email( $GLOBALS['ec_user'] );
											
										}else{
											// Get Shipping Method to Save
											$shipping_method = "";
											if( !get_option( 'ec_option_use_shipping' ) || $this->order_totals->shipping_total <= 0 ){
												$shipping_method = "";
											}else if( $this->shipping->shipping_method == "fraktjakt" ){
												$shipping_method = $this->shipping->get_selected_shipping_method( );
												
											}else if( $GLOBALS['ec_cart_data']->cart_data->shipping_method != "" && $GLOBALS['ec_cart_data']->cart_data->shipping_method != "standard" )
												$shipping_method = $this->mysqli->get_shipping_method_name( $GLOBALS['ec_cart_data']->cart_data->shipping_method );
											
											else if( ( $this->shipping->shipping_method == "price" || $this->shipping->shipping_method == "weight" ) && $GLOBALS['ec_cart_data']->cart_data->expedited_shipping != "" )
												$shipping_method = $GLOBALS['language']->get_text( "cart_estimate_shipping", "cart_estimate_shipping_express" );
											
											else
												$shipping_method = $GLOBALS['language']->get_text( "cart_estimate_shipping", "cart_estimate_shipping_standard" );
											
											$order_id = $this->mysqli->insert_subscription_order( $product, $GLOBALS['ec_user'], $card, $subscription_id, $coupon_promocode_id, $order_notes, $this->subscription_option1_name, $this->subscription_option2_name, $this->subscription_option3_name, $this->subscription_option4_name, $this->subscription_option5_name, $this->subscription_option1_label, $this->subscription_option2_label, $this->subscription_option3_label, $this->subscription_option4_label, $this->subscription_option5_label, $quantity, $this->order_totals, $shipping_method, $this->tax, $discount_total );	
											do_action( 'wpeasycart_order_paid', $order_id );
											$order_row = $this->mysqli->get_order_row( $order_id, $GLOBALS['ec_cart_data']->cart_data->user_id );
											$order = new ec_orderdisplay( $order_row );
											$order_details = $this->mysqli->get_order_details( $order_id, $GLOBALS['ec_cart_data']->cart_data->user_id );
											$subscription->send_email_receipt( $GLOBALS['ec_user'], $order, $order_details );
											$this->mysqli->update_product_stock( $product->product_id, $quantity );
											
											if( $subscription->payment_duration > 0 && $subscription->payment_duration == 1 ){
												$stripe->cancel_subscription( $GLOBALS['ec_user'], $subscription->stripe_subscription_id );
												$this->mysqli->cancel_stripe_subscription( $subscription->stripe_subscription_id );
											}
											
										}
										
										// Unset Variables Entered
										$GLOBALS['ec_cart_data']->checkout_session_complete( );
										
										if( $product->trial_period_days > 0 ){
											header( "location: " . $this->account_page . $this->permalink_divider . "ec_page=subscription_details&subscription_id=" . $subscription_id );
										
										}else{
											header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $order_id );
										}
									
									}else{
										header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $model_number . "&ec_cart_error=subscription_failed" );	
									
									}// Close check for subscription insertion
									
								}else{
									header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $model_number . "&ec_cart_error=subscription_added_failed" );
								
								}// Close check for stripe plan insertion
								
							}else{
								header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $model_number . "&ec_cart_error=card_error" );
							
							}// Close check for card insertion
						
						}else{
							header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $model_number . "&ec_cart_error=user_insert_error" );
						
						}// Close check for customer insertion to stripe check
						
					}else if( class_exists( 'ec_paypal' ) ){ // Close check for PayPal
						
						$coupon_promocode_id = "";
						if( isset( $coupon_row ) )
							$coupon_promocode_id = $coupon_row->promocode_id;
										
						$order_id = $this->mysqli->insert_paypal_subscription_order( $product, $GLOBALS['ec_user'], $coupon_promocode_id, $order_notes, $this->subscription_option1_name, $this->subscription_option2_name, $this->subscription_option3_name, $this->subscription_option4_name, $this->subscription_option5_name, $this->subscription_option1_label, $this->subscription_option2_label, $this->subscription_option3_label, $this->subscription_option4_label, $this->subscription_option5_label, $quantity );
						$paypal = new ec_paypal( );
						$paypal->display_subscription_form( $order_id, $GLOBALS['ec_user'], $product );
						
						// Unset Variables Entered
						$GLOBALS['ec_cart_data']->cart_data->subscription_option1 = "";
						$GLOBALS['ec_cart_data']->cart_data->subscription_option2 = "";
						$GLOBALS['ec_cart_data']->cart_data->subscription_option3 = "";
						$GLOBALS['ec_cart_data']->cart_data->subscription_option4 = "";
						$GLOBALS['ec_cart_data']->cart_data->subscription_option5 = "";
						
						$GLOBALS['ec_cart_data']->cart_data->subscription_advanced_option = "";
						
						$GLOBALS['ec_cart_data']->cart_data->billing_first_name = "";
						$GLOBALS['ec_cart_data']->cart_data->billing_last_name = "";
						$GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = "";
						$GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = "";
						$GLOBALS['ec_cart_data']->cart_data->billing_city = "";
						$GLOBALS['ec_cart_data']->cart_data->billing_state = "";
						$GLOBALS['ec_cart_data']->cart_data->billing_zip = "";
						$GLOBALS['ec_cart_data']->cart_data->billing_country = "";
						$GLOBALS['ec_cart_data']->cart_data->billing_phone = "";
						
						$GLOBALS['ec_cart_data']->cart_data->shipping_selector = "";
						$GLOBALS['ec_cart_data']->cart_data->shipping_first_name = "";
						$GLOBALS['ec_cart_data']->cart_data->shipping_last_name = "";
						$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = "";
						$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = "";
						$GLOBALS['ec_cart_data']->cart_data->shipping_city = "";
						$GLOBALS['ec_cart_data']->cart_data->shipping_state = "";
						$GLOBALS['ec_cart_data']->cart_data->shipping_zip = "";
						$GLOBALS['ec_cart_data']->cart_data->shipping_country = "";
						$GLOBALS['ec_cart_data']->cart_data->shipping_phone = "";
						
						$GLOBALS['ec_cart_data']->cart_data->use_shipping = "";
						$GLOBALS['ec_cart_data']->cart_data->shipping_method = "";
						$GLOBALS['ec_cart_data']->cart_data->expedited_shipping = ""; 
						
						if( $GLOBALS['ec_cart_data']->cart_data->user_id == "" ){
							$GLOBALS['ec_cart_data']->cart_data->email = "";
							$GLOBALS['ec_cart_data']->cart_data->first_name = "";
							$GLOBALS['ec_cart_data']->cart_data->last_name = "";
						}
						
						$GLOBALS['ec_cart_data']->cart_data->create_account = "";
						$GLOBALS['ec_cart_data']->cart_data->coupon_code = "";
						$GLOBALS['ec_cart_data']->cart_data->giftcard = "";
						$GLOBALS['ec_cart_data']->cart_data->order_notes = "";
						setcookie('ec_cart_id', "", time( ) - 300, "/" ); 
		
						$GLOBALS['ec_cart_data']->clear_db_session( );
						
						global $wpdb;
						
						$vals = array( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z' );
						$session_cart_id = $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)];
						
						$check_tempcart_id = $wpdb->get_row( $wpdb->prepare( "SELECT ec_tempcart.* FROM ec_tempcart WHERE ec_tempcart.session_id = %s", $session_cart_id ) );
						$check_tempcart_data_id = $wpdb->get_row( $wpdb->prepare( "SELECT ec_tempcart_data.* FROM ec_tempcart_data WHERE ec_tempcart_data.session_id = %s", $session_cart_id ) );
						while( $check_tempcart_id || $check_tempcart_data_id ){ // If we get a result, create new and go until we get a unique tempcart id...
							$session_cart_id = $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)] . $vals[rand(0, 25)];
							$check_tempcart_id = $wpdb->get_row( $wpdb->prepare( "SELECT ec_tempcart.* FROM ec_tempcart WHERE ec_tempcart.session_id = %s", $session_cart_id ) );
							$check_tempcart_data_id = $wpdb->get_row( $wpdb->prepare( "SELECT ec_tempcart_data.* FROM ec_tempcart_data WHERE ec_tempcart_data.session_id = %s", $session_cart_id ) );
						}
						$GLOBALS['ec_cart_id'] = $session_cart_id;
						setcookie( 'ec_cart_id', $session_cart_id, time( ) + ( 3600 * 24 * 1 ), "/" );
		
						$GLOBALS['ec_cart_data'] = new ec_cart_data( $GLOBALS['ec_cart_data']->ec_cart_id );
						
						die( );
						
					}else{ // Close check for paypal
						$GLOBALS['ec_cart_data']->save_session_to_db( );
						header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $model_number . "&ec_cart_error=subscription_setup_error" );
					}
			
				}else{
					$GLOBALS['ec_cart_data']->save_session_to_db( );
					header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $model_number . "&ec_cart_error=already_subscribed" );
				
				}// Close check for already subscribed error
			
			}else{
				
				$GLOBALS['ec_cart_data']->save_session_to_db( );
				header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $model_number . "&ec_cart_error=subscription_not_found" );
				
			}// Close check for subscription existing
			
		}else{
			$GLOBALS['ec_cart_data']->save_session_to_db( );
			header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $model_number . "&ec_cart_error=email_exists" );
			
		}// Close user exists error for guest checkout
		
	}
	
	private function process_send_inquiry( ){
		
		$recaptcha_valid = true;
		if( get_option( 'ec_option_enable_recaptcha' ) ){
			$db = new ec_db_admin( );
            $recaptcha_response = $_POST['ec_grecaptcha_response_inquiry'];
            
            $data = array(
				"secret"	=> get_option( 'ec_option_recaptcha_secret_key' ),
				"response"	=> $recaptcha_response
			);
			$verify = curl_init();
			curl_setopt( $verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify" );
			curl_setopt( $verify, CURLOPT_POST, true );
			curl_setopt( $verify, CURLOPT_POSTFIELDS, http_build_query( $data ) );
			curl_setopt( $verify, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $verify, CURLOPT_RETURNTRANSFER, true );
			$response = curl_exec( $verify );
            if( $response === false ){
                $db->insert_response( 0, 1, "GOOGLE RECAPTCHA CURL ERROR", curl_error( $verify ) );
                $response = (object) array( "error" => curl_error( $verify ) );
            }else{
                $response = json_decode( $response );
                $db->insert_response( 0, 0, "Google Recaptcha Response", print_r( $response, true ) );
            }
            curl_close( $verify );
            
			$recaptcha_valid = ( isset( $response->success ) && $response->success ) ? true : false;
		}
		
		if( $recaptcha_valid ){
			$inquiry_email = filter_var( stripslashes( $_POST['ec_inquiry_email'] ), FILTER_SANITIZE_EMAIL );
			$inquiry_name = stripslashes( $_POST['ec_inquiry_name'] );
			$inquiry_message = stripslashes( $_POST['ec_inquiry_message'] );
			$model_number = $_POST['ec_inquiry_model_number'];
			if( isset( $_POST['ec_inquiry_send_copy'] ) )
				$send_copy = true;
			else
				$send_copy = false;
			
			$product = $this->mysqli->get_product( $model_number );
			$file_temp_num = rand( 1000000, 999999999 );
			if( $product->use_advanced_optionset ){
				$option_vals = $this->get_advanced_option_vals( $product->product_id, $file_temp_num );
			}else{
				$option1 = $option2 = $option3 = $option4 = $option5 = "";
				if( isset( $_POST['ec_option1'] ) )				$option1 = $GLOBALS['ec_options']->get_optionitem( $_POST['ec_option1'] );
				if( isset( $_POST['ec_option2'] ) )				$option2 = $GLOBALS['ec_options']->get_optionitem( $_POST['ec_option2'] );
				if( isset( $_POST['ec_option3'] ) )				$option3 = $GLOBALS['ec_options']->get_optionitem( $_POST['ec_option3'] );
				if( isset( $_POST['ec_option4'] ) )				$option4 = $GLOBALS['ec_options']->get_optionitem( $_POST['ec_option4'] );
				if( isset( $_POST['ec_option5'] ) )				$option5 = $GLOBALS['ec_options']->get_optionitem( $_POST['ec_option5'] );
			}
			
			if( $product && $inquiry_email != "" && $inquiry_name != "" && $inquiry_message != "" ){
				
				// Create mail script
				$email_logo_url = get_option( 'ec_option_email_logo' ) . "' alt='" . get_bloginfo( "name" );
				
				$headers   = array();
				$headers[] = "MIME-Version: 1.0";
				$headers[] = "Content-Type: text/html; charset=utf-8";
				$headers[] = "From: " . stripslashes( get_option( 'ec_option_password_from_email' ) );
				$headers[] = "Reply-To: " . stripslashes( get_option( 'ec_option_password_from_email' ) );
				$headers[] = "X-Mailer: PHP/".phpversion();
				
				$headers2   = array();
				$headers2[] = "MIME-Version: 1.0";
				$headers2[] = "Content-Type: text/html; charset=utf-8";
				$headers2[] = "From: " . $inquiry_email;
				$headers2[] = "Reply-To: " . $inquiry_email;
				$headers2[] = "X-Mailer: PHP/".phpversion();
				
				$has_product_options = false;
				
				ob_start();
				if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_inquiry_email.php' ) )	
					include WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_inquiry_email.php';	
				else
					include WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_inquiry_email.php';
				$message = ob_get_clean();
				
				$email_send_method = get_option( 'ec_option_use_wp_mail' );
				$email_send_method = apply_filters( 'wpeasycart_email_method', $email_send_method );
				
				if( $email_send_method == "1" ){
					if( $send_copy )
						wp_mail( $inquiry_email, $GLOBALS['language']->get_text( "product_details", "product_details_inquiry_title" ), $message, implode("\r\n", $headers ) );
					
					wp_mail( stripslashes( get_option( 'ec_option_bcc_email_addresses' ) ), $GLOBALS['language']->get_text( "product_details", "product_details_inquiry_title" ), $message, implode( "\r\n", $headers2 ) );
				}else if( $email_send_method == "0" ){
					$admin_email = stripslashes( get_option( 'ec_option_bcc_email_addresses' ) );
					$to = $inquiry_email;
					$subject = $GLOBALS['language']->get_text( "product_details", "product_details_inquiry_title" );
					$mailer = new wpeasycart_mailer( );
					if( $send_copy )
						$mailer->send_order_email( $to, $subject, $message );
					$mailer->send_order_email( $admin_email, $subject, $message );
				}else{
					if( $send_copy )
						do_action( 'wpeasycart_custom_inquiry_email', stripslashes( get_option( 'ec_option_order_from_email' ) ), $inquiry_email, stripslashes( get_option( 'ec_option_bcc_email_addresses' ) ), $GLOBALS['language']->get_text( "product_details", "product_details_inquiry_title" ), $message );
					else
						wp_mail( stripslashes( get_option( 'ec_option_bcc_email_addresses' ) ), $GLOBALS['language']->get_text( "product_details", "product_details_inquiry_title" ), $message, implode("\r\n", $headers2 ) );
				}
				
				if( get_option( 'ec_option_use_old_linking_style' ) )
					header( "location: " . $this->store_page . $this->permalink_divider . "model_number=" . $product->model_number . "&ec_store_success=inquiry_sent" );
				else
					header( "location: " . get_permalink( $product->post_id ) . $this->permalink_divider . "ec_store_success=inquiry_sent" );
				
			}
			
		}
		
	}
	
	private function process_deconetwork_add_to_cart( ){
		
		$this->mysqli->deconetwork_add_to_cart( );
		header( "location: " . $this->cart_page );
	
	}
	
	public function process_subscribe_v3( ){
		
		$product_id = $_POST['product_id'];
		$cart_id = $GLOBALS['ec_cart_data']->ec_cart_id;
		$product = $this->mysqli->get_product( "", $product_id );
		$use_advanced_optionset = $product->use_advanced_optionset;
		$quantity = 1;
		if( isset( $_POST['ec_quantity'] ) )
			$quantity = $_POST['ec_quantity'];
			
		$GLOBALS['ec_cart_data']->cart_data->subscription_quantity = $quantity;
			
		//Product Options
		$GLOBALS['ec_cart_data']->cart_data->subscription_option1 = "";
		$GLOBALS['ec_cart_data']->cart_data->subscription_option2 = "";
		$GLOBALS['ec_cart_data']->cart_data->subscription_option3 = "";
		$GLOBALS['ec_cart_data']->cart_data->subscription_option4 = "";
		$GLOBALS['ec_cart_data']->cart_data->subscription_option5 = "";
		
		if( !$use_advanced_optionset ){
			
			if( isset( $_POST['ec_option1'] ) )				$GLOBALS['ec_cart_data']->cart_data->subscription_option1 = $_POST['ec_option1'];
			else											$GLOBALS['ec_cart_data']->cart_data->subscription_option1 = "";
			
			if( isset( $_POST['ec_option2'] ) )				$GLOBALS['ec_cart_data']->cart_data->subscription_option2 = $_POST['ec_option2'];
			else											$GLOBALS['ec_cart_data']->cart_data->subscription_option2 = "";
			
			
			if( isset( $_POST['ec_option3'] ) )				$GLOBALS['ec_cart_data']->cart_data->subscription_option3 = $_POST['ec_option3'];
			else											$GLOBALS['ec_cart_data']->cart_data->subscription_option3 = "";
			
			
			if( isset( $_POST['ec_option4'] ) )				$GLOBALS['ec_cart_data']->cart_data->subscription_option4 = $_POST['ec_option4'];
			else											$GLOBALS['ec_cart_data']->cart_data->subscription_option4 = "";
			
			
			if( isset( $_POST['ec_option5'] ) )				$GLOBALS['ec_cart_data']->cart_data->subscription_option5 = $_POST['ec_option5'];
			else											$GLOBALS['ec_cart_data']->cart_data->subscription_option5 = "";
			
		}
		
		if( $use_advanced_optionset ){
			
			$option_vals = $this->get_advanced_option_vals( $product_id, $cart_id );
			
		}
		
		$GLOBALS['ec_cart_data']->cart_data->subscription_advanced_option = maybe_serialize( $option_vals );
		$GLOBALS['ec_cart_data']->save_session_to_db( );
		
		header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $product->model_number );
		
	}
	
	private function process_update_subscription_quantity( ){
		
		$product_id = $_POST['product_id'];
		$product = $this->mysqli->get_product( "", $product_id );
		
		if( $product ){
		
			$quantity = $_POST[ 'ec_quantity' ];
			$GLOBALS['ec_cart_data']->cart_data->subscription_quantity = $quantity;
		
		}
		
		$GLOBALS['ec_cart_data']->save_session_to_db( );
		header( "location: " . $this->cart_page . $this->permalink_divider . "ec_page=subscription_info&subscription=" . $product->model_number  );
	}
	/* END PROCESS FORM SUBMISSION FUNCTIONS */
	
	/* Customer File Upload Function */
	private function file_upload_max_size( ){
		$max_size = -1;
		if( $max_size < 0 ){
			$post_max_size = $this->parse_size( ini_get( 'post_max_size' ) );
			if( $post_max_size > 0 ){
				$max_size = $post_max_size;
			}
		
			// If upload_max_size is less, then reduce. Except if upload_max_size is
			// zero, which indicates no limit.
			$upload_max = $this->parse_size( ini_get( 'upload_max_filesize' ) );
			if( $upload_max > 0 && $upload_max < $max_size ){
				$max_size = $upload_max;
			}
		}
		return $max_size;
	}
	
	private function parse_size( $size ){
		$unit = preg_replace( '/[^bkmgtpezy]/i', '', $size ); // Remove the non-unit characters from the size.
		$size = preg_replace( '/[^0-9\.]/', '', $size ); // Remove the non-numeric characters from the size.
		if( $unit ){
			// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
			return round( $size * pow( 1024, stripos( 'bkmgtpezy', $unit[0] ) ) );
		}else{
			return round( $size );
		}
	}

	private function upload_customer_file( $tempcart_id, $upload_field_name ){
		
		# Check to see if the file is accessible
		if( isset($_FILES[$upload_field_name]['name']) && $_FILES[$upload_field_name]['name'] != '' ) {
			$max_filesize = $this->file_upload_max_size( );
			$max_filesize = apply_filters( 'wp_easycart_max_filesize_upload_limit', $max_filesize );
			
			$filetypes = array( 'text/plain', 'image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/x-compressed', 'application/x-zip-compressed', 'application/zip', 'multipart/x-zip', 'application/x-bzip2', 'application/x-bzip', 'application/x-bzip2', 'application/x-gzip', 'application/x-gzip', 'multipart/x-gzip' );
			$filtered_file_types = apply_filters( 'wpeasycart_allowed_file_upload_types', $filetypes );
			if( is_array( $filtered_file_types) )
				$filetypes = $filtered_file_types;
			
			if( is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/uploads/" ) )
				$upload_path =  WP_PLUGIN_DIR . "/wp-easycart-data/products/uploads/";
			else
				$upload_path =  WP_PLUGIN_DIR . "/wp-easycart/products/uploads/";
		 
			# Check to see if the filesize is too large
			if( $_FILES[$upload_field_name]['size'] <= $max_filesize && in_array( $_FILES[$upload_field_name]['type'], $filetypes ) ){
				
				# Create a custom dir for this order
				mkdir( $upload_path . $tempcart_id . "/", 0711 );
				
				# If file has gotten this far, it is successful
				$copy_to = $upload_path . $tempcart_id . "/" . $_FILES[$upload_field_name]['name'];
		
				# Upload the file
				$upload = move_uploaded_file( $_FILES[$upload_field_name]['tmp_name'], $copy_to );
		 
				# Check to see if upload was successful
				if( $upload ){
					return true;
				}
			}
		}
		return false;
	}
	
	private function sanatize_card_number( $card_number ){
		
		return preg_replace( "/[^0-9]/", "", $card_number );
	
	}
	
	private function get_payment_type( $card_number ){
		
		if( preg_match("/^5[1-5]\d{14}$/", $card_number ) )
                return "mastercard";
 
        else if( preg_match( "/^4[0-9]{12}(?:[0-9]{3}|[0-9]{6})?$/", $card_number))
                return "visa";
 
        else if( preg_match( "/^3[47][0-9]{13}$/", $card_number ) )
                return "amex";
 
        else if( preg_match( "/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/", $card_number ) )
                return "diners";
 
        else if( preg_match( "/^6(?:011\d{12}|5\d{14}|4[4-9]\d{13}|22(?:1(?:2[6-9]|[3-9]\d)|[2-8]\d{2}|9(?:[01]\d|2[0-5]))\d{10})$/", $card_number ) )
                return "discover";
 
        else if( preg_match( "/^(?:2131|1800|35\d{3})\d{11}$/", $card_number ) )
                return "jcb";
				
		else
				return $GLOBALS['language']->get_text( 'cart_payment_information', 'cart_payment_card_type_credit_card' );
		
	}
	
	public function display_order_number_link( $order_id ){
		
		if( substr_count( $this->account_page, '?' ) )				$permalink_divider = "&";
		else														$permalink_divider = "?";
		
		if( $GLOBALS['ec_cart_data']->cart_data->is_guest == "" ){
			echo "<a href=\"" . $this->account_page . $permalink_divider . "ec_page=order_details&order_id=" . $order_id . "\">" . $order_id . "</a>";
		}else{
			echo "<a href=\"" . $this->account_page . $permalink_divider . "ec_page=order_details&order_id=" . $order_id . "&guest_key=" . $GLOBALS['ec_cart_data']->cart_data->guest_key . "\">" . $order_id . "</a>";
		}
	}
	
	public function get_shipping_method_name( ){
		return $this->mysqli->get_shipping_method_name( $GLOBALS['ec_cart_data']->cart_data->shipping_method );
	}
	
	public function get_payment_image_source( $image ){
		
		if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/" . $image ) ){
			return plugins_url( "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/" . $image );
		}else{
			return plugins_url( "/wp-easycart/design/theme/" . get_option( 'ec_option_latest_theme' ) . "/images/" . $image );
		}
		
	}
	
	private function add_affiliatewp_subscription_order( $order_id, $user, $product ){
		
		if( affiliate_wp( )->tracking->was_referred( ) ){
			
			$affiliate_id = affiliate_wp( )->tracking->get_affiliate_id( );
			$default_rate = affwp_get_affiliate_rate( $affiliate_id );
			$has_affiliate_rule = false;
		
			$affiliate_rule = $this->mysqli->get_affiliate_rule( affiliate_wp()->tracking->get_affiliate_id( ), $product->product_id );
			if( $affiliate_rule )
				$has_affiliate_rule = true;
			
			if( $has_affiliate_rule ){
				if( $affiliate_rule->rule_type == "percentage" )
					$total_earned += ( $product->price * ( $affiliate_rule->rule_amount / 100 ) );
						
				else if( $affiliate_rule->rule_type == "amount" )
					$total_earned += $affiliate_rule->rule_amount;	
				
			}else
				$total_earned += ( $product->price * $default_rate );
			
			$data = array(
				'affiliate_id' => $affiliate_id,
				'visit_id'     => affiliate_wp()->tracking->get_visit_id( ),
				'amount'       => $total_earned,
				'description'  => $user->billing->first_name . " " . $user->billing->last_name,
				'reference'    => $order_id,
				'context'      => 'WP EasyCart',
			);
			$result = affiliate_wp()->referrals->add( $data );
			
			return $result;

		}
		
		return "";
		
	}
	
	public function get_selected_payment_method( ){
		$default_method =  get_option( 'ec_option_default_payment_type' );
		if( $GLOBALS['ec_cart_data']->cart_data->payment_method != '' )
			return $GLOBALS['ec_cart_data']->cart_data->payment_method;
		else if( $default_method == "manual_bill" && $this->use_manual_payment( ) )
			return "manual_bill";
		else if( $default_method == "affirm" && get_option( 'ec_option_use_affirm' ) )
			return "affirm";
		else if( $default_method == "third_party" && $this->use_third_party( ) )
			return "third_party";
		else if( $default_method == "credit_card" && $this->use_payment_gateway( ) )
			return "credit_card";
		else if( $this->use_payment_gateway( ) )
			return "credit_card";
		else if( $this->use_third_party( ) )
			return "third_party";
		else if( get_option( 'ec_option_use_affirm' ) )
			return "affirm";
		else if( $this->use_manual_payment( ) )
			return "manual_bill";
	}
	
	public function is_coupon_expired( ){
		if( $this->coupon_code == '' || ( $this->coupon && !$this->coupon->coupon_expired && ( $this->coupon->max_redemptions == 999 || $this->coupon->times_redeemed < $this->coupon->max_redemptions ) ) )
			return false;
		else
			return true;
	}
	
	public function get_coupon_expiration_note( ){
		if( $this->coupon_code == '' || ( $this->coupon && !$this->coupon->coupon_expired && ( $this->coupon->max_redemptions == 999 || $this->coupon->times_redeemed < $this->coupon->max_redemptions ) ) )
			return "";
			
		else if( $this->coupon && $this->coupon->times_redeemed >= $this->coupon->max_redemptions )
			return $GLOBALS['language']->get_text( 'cart_coupons', 'cart_max_exceeded_coupon' );
		
		else if( $this->coupon->coupon_expired )
			return $GLOBALS['language']->get_text( 'cart_coupons', 'cart_coupon_expired' );
			
		else
			return $GLOBALS['language']->get_text( 'cart_coupons', 'cart_invalid_coupon' );
			
	}
	
	public function return_to_store_page( $url ){
		return apply_filters( 'wp_easycart_return_store_url', ( get_option( 'ec_option_return_to_store_page_url' ) != "" ) ? get_option( 'ec_option_return_to_store_page_url' ) : $url );
	}
	
}

?>