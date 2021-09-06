<?php

class ec_tax{
	
	protected $mysqli;										// ec_db structure
	
	public $state_tax;										// FLOAT 15,3
	public $country_tax;									// FLOAT 15,3
	public $all_tax;										// FLOAT 15,3
	
	public $tax_total;										// FLOAT 15,3
	public $tax_shipping;									// BOOL
	
	public $duty_total;										// FLOAT 15,3
	public $vat_total;										// FLOAT 15,3
	public $shipping_total;									// FLOAT 15,3
	
	// State Tax
	public $state_tax_enabled;								// BOOL
	public $state_tax_match;								// BOOL
	public $state_tax_rate;									// FLOAT 11,2
	
	// Country Tax
	public $country_tax_enabled;							// BOOL
	public $country_tax_match;								// BOOL
	public $country_tax_rate;								// FLOAT 11,2
	
	// All Tax
	public $all_tax_enabled;								// BOOL
	public $all_tax_rate;									// FLOAT 11,2
	
	// Duty
	public $duty_enabled;									// BOOL
	public $duty_country_match;								// BOOL
	public $duty_rate;										// FLOAT 11,2
	
	// VAT
	public $vat_enabled;									// BOOL
	public $vat_country_match;								// BOOL
	public $vat_rate_default;								// FLOAT 11,2
	public $vat_rate;										// FLOAT 11,2
	public $vat_added;										// BOOL
	public $vat_included;									// BOOL
	
	// Canada Tax
	public $easy_canada_tax_enabled;						// BOOL
	public $collect_alberta;								// BOOL
	public $collect_british_columbia;						// BOOL
	public $collect_manitoba;								// BOOL
	public $collect_new_brunswick;							// BOOL
	public $collect_newfoundland;							// BOOL
	public $collect_northwest_territories;					// BOOL
	public $collect_nova_scotia;							// BOOL
	public $collect_nunavut;								// BOOL
	public $collect_ontario;								// BOOL
	public $collect_prince_edward_island;					// BOOL
	public $collect_quebec;									// BOOL
	public $collect_saskatchewan;							// BOOL
	public $collect_yukon;									// BOOL
	public $gst;											// FLOAT 15,3
	public $gst_rate;										// FLOAT 15,3
	public $pst;											// FLOAT 15,3
	public $pst_rate;										// FLOAT 15,3
	public $hst;											// FLOAT 15,3
	public $hst_rate;										// FLOAT 15,3
	
	// Tax Cloud
	public $tax_cloud_enabled;								// BOOL
	
	// List of Countries for VAT
	public $country_list;									// Array( 'name_cnt'=>'United States', 
															//		  'iso2_cnt'=>'US', 
															//		  'vat_rate_cnt'=>'20' )
	
	// Passed in Values
    private $cart;                                          // ec_cart element
	private $cart_subtotal;		// Used for Duty			// FLOAT 7,2
	private $taxable_subtotal;	// Used for Tax				// FLOAT 7,2
	private $vatable_total;		// Used for VAT				// FLOAT 15,3
	
	private $shipping_state;								// VARCHAR 255
	private $shipping_country;								// VARCHAR 255
	
	private $taxfree;										// BOOLEAN
	
	function __construct( $cart_subtotal, $taxable_subtotal, $vatable_total, $shipping_state, $shipping_country, $taxfree = false, $shipping_total = 0.00, $cart = false ){
		
		// Initialize Structures and Lists
		$this->mysqli 							= 			new ec_db();
        $this->cart                             =           ( $cart ) ? $cart : array( );
		$this->country_list						=			$GLOBALS['ec_countries']->countries;
		$taxrates								= 			$this->mysqli->get_taxrates( );
		
		// Save the Subtotals
		$this->cart_subtotal 					= 			$cart_subtotal;
		if( $this->cart_subtotal < 0 )
			$this->cart_subtotal				=			0;
			
		$this->taxable_subtotal 				= 			$taxable_subtotal;
		if( $this->taxable_subtotal < 0 )
			$this->taxable_subtotal				=			0;
		
		$this->vatable_total 					= 			$vatable_total;
		if( $this->vatable_total < 0 )
			$this->vatable_total				=			0;
			
		$this->shipping_total					=			$shipping_total;
		
		// Save the User Entered Data
		$this->shipping_state 					= 			apply_filters( 'wp_easycart_tax_shipping_state', strtoupper( $shipping_state ) );
		$this->shipping_country 				= 			apply_filters( 'wp_easycart_tax_shipping_country', strtoupper( $shipping_country ) );
		
		$this->taxfree							=			$taxfree;
		
		// Initialize the Values to Zero/False
		$this->initialize_tax_values( );
		
		// Setup the Values from DB
		$this->setup_tax_info( $taxrates );
		
		// Calculate Actual Values (possible alternate method)
        if( apply_filters( 'wp_easycart_enable_alt_tax', false ) ){
            $this->calculate_alt_tax( );
        }else{
            $this->calculate_taxes( );
        }
	}
	
	private function initialize_tax_values( ){
		// Overall Options
		$this->tax_shipping = get_option( 'ec_option_collect_tax_on_shipping' );
		
		// State Tax
		$this->state_tax_enabled 		= false;
		$this->state_tax_match 			= false;
		$this->state_tax_rate			= 0;
		
		// Country Tax
		$this->country_tax_enabled		= false;
		$this->country_tax_match		= false;
		$this->country_tax_rate			= 0;
		
		// All Tax
		$this->all_tax_enabled			= false;
		$this->all_tax_rate				= 0;
		
		// Duty
		$this->duty_enabled				= false;
		$this->duty_country_match		= false;
		$this->duty_rate				= 0;
		
		// VAT
		$this->vat_enabled				= false;
		$this->vat_country_match		= false;
		$this->vat_rate_default			= 0;
		$this->vat_rate					= 0;
        $this->vat_rounding             = ( (float) get_option( 'ec_option_vat_rounding' ) < .001 ) ? .01 : (float) get_option( 'ec_option_vat_rounding' );
		
		// Canada Tax
		$this->easy_canada_tax_enabled = get_option( 'ec_option_enable_easy_canada_tax' );
		$this->collect_alberta = get_option( 'ec_option_collect_alberta_tax' );
		$this->collect_british_columbia = get_option( 'ec_option_collect_british_columbia_tax' );
		$this->collect_manitoba = get_option( 'ec_option_collect_manitoba_tax' );
		$this->collect_new_brunswick = get_option( 'ec_option_collect_new_brunswick_tax' );
		$this->collect_newfoundland = get_option( 'ec_option_collect_newfoundland_tax' );
		$this->collect_northwest_territories = get_option( 'ec_option_collect_northwest_territories_tax' );
		$this->collect_nova_scotia = get_option( 'ec_option_collect_nova_scotia_tax' );
		$this->collect_nunavut = get_option( 'ec_option_collect_nunavut_tax' );
		$this->collect_ontario = get_option( 'ec_option_collect_ontario_tax' );
		$this->collect_prince_edward_island = get_option( 'ec_option_collect_prince_edward_island_tax' );
		$this->collect_quebec = get_option( 'ec_option_collect_quebec_tax' );
		$this->collect_saskatchewan = get_option( 'ec_option_collect_saskatchewan_tax' );
		$this->collect_yukon = get_option( 'ec_option_collect_yukon_tax' );
		$this->gst = 0;
		$this->gst_rate = 0;
		$this->pst = 0;
		$this->pst_rate = 0;
		$this->hst = 0;
		$this->hst_rate = 0;
		
		// Tax Cloud
		if( get_option( 'ec_option_tax_cloud_api_id' ) != "" && get_option( 'ec_option_tax_cloud_api_key' ) != "" )
			$this->tax_cloud_enabled = true;
		else
			$this->tax_cloud_enabled = false;
		
	}
	
	private function setup_tax_info( $taxrates ){
		foreach( $taxrates as $taxrate ){
			if( !$this->state_tax_match && $taxrate->tax_by_state ){
				$this->state_tax_enabled = true;
				if(	$this->shipping_state == $taxrate->state_code && ( $taxrate->country_code == "" || $this->shipping_country == $taxrate->country_code ) ){
					$this->state_tax_match = true;
					$this->state_tax_rate = $taxrate->state_rate;
				}
			}else if( $taxrate->tax_by_country ){
				$this->country_tax_enabled = true;
				if( $this->shipping_country == $taxrate->country_code ){
					$this->country_tax_match = true;
					$this->country_tax_rate = $taxrate->country_rate;	
				}
			}else if( $taxrate->tax_by_all ){
				$this->all_tax_enabled = true;
				$this->all_tax_rate = $taxrate->all_rate;
			}else if( $taxrate->tax_by_duty ){
				$this->duty_enabled = true;
				if( $this->shipping_country != $taxrate->duty_exempt_country_code ){
					$this->duty_country_match = true;
					$this->duty_rate = $taxrate->duty_rate;
				}
			}else if( $taxrate->tax_by_vat ){
				$this->vat_enabled = true;
				$vat_row = $taxrate;
				$this->vat_rate_default = $taxrate->vat_rate;
				$this->vat_added = $taxrate->vat_added;
				$this->vat_included = $taxrate->vat_included;
			}else if( $taxrate->tax_by_single_vat ){
				$this->vat_enabled = true;
				$this->vat_rate_default = $taxrate->vat_rate;
				$this->vat_rate = $taxrate->vat_rate;
				$this->vat_added = $taxrate->vat_added;
				$this->vat_included = $taxrate->vat_included;
			}
		}
		
		if( $this->vat_enabled && $this->vat_rate <= 0 ){
			for( $i=0; $i<count($this->country_list); $i++){
				if( get_option( 'ec_option_collect_vat_registration_number' ) && $GLOBALS['ec_user']->vat_registration_number != "" && $this->shipping_country == $this->country_list[$i]->iso2_cnt && !$this->country_list[$i]->vat_b2b_enabled ){
                    // Ignore for Businesses
                    $this->vat_country_match = true;
					$this->vat_rate = 0;
                    
                }else if( get_option( 'ec_option_collect_vat_registration_number' ) && $GLOBALS['ec_cart_data']->cart_data->vat_registration_number != "" && $this->shipping_country == $this->country_list[$i]->iso2_cnt && !$this->country_list[$i]->vat_b2b_enabled ){
                    // Ignore for Businesses
                    $this->vat_country_match = true;
					$this->vat_rate = 0;
                
                }else if( $this->shipping_country == $this->country_list[$i]->iso2_cnt ){
					$this->vat_country_match = true;
					$this->vat_rate = $this->country_list[$i]->vat_rate_cnt;
				}
			}
			if( !$this->vat_country_match ){
				$this->vat_rate = $vat_row->vat_rate;
			}
		}
	}
    
    private function calculate_alt_tax( ){
        $this->tax_total = $this->tax_rate = $this->duty_total = $this->vat_total = $this->pst_total = $this->pst_rate = $this->hst_total = $this->hst_rate = $this->gst_total = $this->gst_rate = 0;
        
        $alt_tax_list = (object) array(
            'tax_total' => 0,
            'tax_rate' => 0,
            'duty_total' => 0,
            'vat_total' => 0,
            'vat_rate' => $this->vat_rate,
            'vat_enabled' => $this->vat_enabled,
            'pst_total' => 0,
            'pst_rate' => 0,
            'hst_total' => 0,
            'hst_rate' => 0,
            'gst_total' => 0,
            'gst_rate' => 0
        );
        
        $tax_settings = (object) array(
            'tax_shipping' => $this->tax_shipping,
            'state_tax_enabled' => $this->state_tax_enabled,
            'state_tax_match' => $this->state_tax_match,
            'state_tax_rate' => $this->state_tax_rate,
            'country_tax_enabled' => $this->country_tax_enabled,
            'country_tax_match' => $this->country_tax_match,
            'country_tax_rate' => $this->country_tax_rate,
            'all_tax_enabled' => $this->all_tax_enabled,
            'all_tax_rate' => $this->all_tax_rate,
            'duty_enabled' => $this->duty_enabled,
            'duty_country_match' => $this->duty_country_match,
            'duty_rate' => $this->duty_rate,
            'vat_enabled' => $this->vat_enabled,
            'vat_country_match' => $this->vat_country_match,
            'vat_rate_default' => $this->vat_rate_default,
            'vat_rate' => $this->vat_rate,
            'vat_added' => $this->vat_added,
            'vat_included' => $this->vat_included,
            'vat_rounding' => $this->vat_rounding,
            'easy_canada_tax_enabled' => $this->easy_canada_tax_enabled,
            'collect_alberta' => $this->collect_alberta,
            'collect_british_columbia' => $this->collect_british_columbia,
            'collect_manitoba' => $this->collect_manitoba,
            'collect_new_brunswick' => $this->collect_new_brunswick,
            'collect_newfoundland' => $this->collect_newfoundland,
            'collect_northwest_territories' => $this->collect_northwest_territories,
            'collect_nova_scotia' => $this->collect_nova_scotia,
            'collect_nunavut' => $this->collect_nunavut,
            'collect_ontario' => $this->collect_ontario,
            'collect_prince_edward_island' => $this->collect_prince_edward_island,
            'collect_quebec' => $this->collect_quebec,
            'collect_saskatchewan' => $this->collect_saskatchewan,
            'collect_yukon' => $this->collect_yukon,
            'tax_cloud_enabled' => $this->tax_cloud_enabled,
            'country_list' => $this->country_list,
            'taxable_subtotal' => $this->taxable_subtotal,
            'vatable_total' => $this->vatable_total,
            'taxfree' => $this->taxfree
        );
        
        $alt_tax_list = apply_filters( 'wp_easycart_alt_tax_calculate', $alt_tax_list, $this->cart_subtotal, $this->cart, $this->shipping_total, $tax_settings );
        
        $this->tax_total 						= 			$alt_tax_list->tax_total;
        $this->tax_rate 						= 			$alt_tax_list->tax_rate;
		$this->duty_total 						= 			$alt_tax_list->duty_total;
		$this->vat_total 						= 			$alt_tax_list->vat_total;
		$this->vat_rate 						= 			$alt_tax_list->vat_rate;
        $this->vat_enabled                      =           $alt_tax_list->vat_enabled;
        $this->pst_total 						= 			$alt_tax_list->pst_total;
        $this->pst_rate 						= 			$alt_tax_list->pst_rate;
        $this->hst_total 						= 			$alt_tax_list->hst_total;
        $this->hst_rate 						= 			$alt_tax_list->hst_rate;
        $this->gst_total 						= 			$alt_tax_list->gst_total;
        $this->gst_rate 						= 			$alt_tax_list->gst_rate;
        
    }
	
	private function calculate_taxes( ){
		$this->state_tax 						= 			0;
		$this->country_tax 						= 			0;
		$this->all_tax 							= 			0;
		$this->duty_total 						= 			0;
		$this->vat_total 						= 			0;
		
		if( $this->taxfree ){
			// Tax free order, lets not charge the customer
			$this->tax_total = 0;
			
		}else if( $this->tax_cloud_enabled && $this->shipping_country == 'US' ){
			$this->tax_total = wpeasycart_taxcloud( )->tax_amount;
			$this->state_tax_rate = ( $this->cart_subtotal > 0 ) ? ( $this->tax_total / $this->cart_subtotal ) * 100 : 0;
			
		}else{
			// Add Shipping to Taxable Total
			if( $this->tax_shipping ){
				$this->taxable_subtotal = $this->taxable_subtotal + $this->shipping_total;
			}
			
			// Calculate State Tax
			if( $this->state_tax_enabled && $this->state_tax_match )
				$this->state_tax = $this->taxable_subtotal * $this->state_tax_rate / 100;
			
			// Calculate Country Tax
			if( $this->country_tax_enabled && $this->country_tax_match )
				$this->country_tax = $this->taxable_subtotal * $this->country_tax_rate / 100;
			
			// Calculate All Tax
			if( $this->all_tax_enabled )
				$this->all_tax = $this->taxable_subtotal * $this->all_tax_rate / 100;	
			
			//Calculate Sales Tax Total
			$this->tax_total = apply_filters( 'wp_easycart_tax_total', $this->state_tax + $this->country_tax + $this->all_tax, $this->taxable_subtotal );
			
			// Calculate Duty
			if( $this->duty_enabled && $this->duty_country_match )
				$this->duty_total = $this->cart_subtotal * $this->duty_rate / 100;
			
			// Calculate VAT Values
			if( $this->vat_enabled ){
				if( get_option( 'ec_option_collect_vat_registration_number' ) && $GLOBALS['ec_user']->vat_registration_number != "" ){
					$this->vat_rate = get_option( 'ec_option_vat_custom_rate' );
				}
				$GLOBALS['ec_vat_rate'] = $this->vat_rate;
				if( $this->vat_included ){
					$this->vat_total = ( $this->vatable_total / ( ( $this->vat_rate / 100 ) + 1 ) ) * ( $this->vat_rate / 100 );
				}else{
					$this->vat_total = $this->vatable_total * $this->vat_rate / 100;
				}
                $this->vat_total = round( $this->vat_total / $this->vat_rounding ) * $this->vat_rounding;
			}
			
			// Calculate Canada Tax
			if( $this->easy_canada_tax_enabled && $this->shipping_country == "CA" ){
				
				$canada_tax_options = get_option( 'ec_option_canada_tax_options' );
				$user_level = $GLOBALS['ec_user']->user_level;
				if( $user_level == 'admin' || $GLOBALS['ec_user']->user_level == "" )
					$user_level = 'shopper';
				
				if( $canada_tax_options ){ // New Version of Canada Tax
				
					$this->gst_rate = 0;
					$this->pst_rate = 0;
					$this->hst_rate = 0;
				
					if( isset( $canada_tax_options['ec_option_collect_alberta_tax_' . $user_level] ) && $this->shipping_state == "AB" ){
						$this->gst_rate = $canada_tax_options['ec_option_alberta_tax_' . $user_level . '_gst'] * 100;
						$this->pst_rate = $canada_tax_options['ec_option_alberta_tax_' . $user_level . '_pst'] * 100;
						$this->hst_rate = $canada_tax_options['ec_option_alberta_tax_' . $user_level . '_hst'] * 100;
						
					}else if( isset( $canada_tax_options['ec_option_collect_british_columbia_tax_' . $user_level] ) && $this->shipping_state == "BC" ){
						$this->gst_rate = $canada_tax_options['ec_option_british_columbia_tax_' . $user_level . '_gst'] * 100;
						$this->pst_rate = $canada_tax_options['ec_option_british_columbia_tax_' . $user_level . '_pst'] * 100;
						$this->hst_rate = $canada_tax_options['ec_option_british_columbia_tax_' . $user_level . '_hst'] * 100;
						
					}else if( isset( $canada_tax_options['ec_option_collect_manitoba_tax_' . $user_level] ) && $this->shipping_state == "MB" ){
						$this->gst_rate = $canada_tax_options['ec_option_manitoba_tax_' . $user_level . '_gst'] * 100;
						$this->pst_rate = $canada_tax_options['ec_option_manitoba_tax_' . $user_level . '_pst'] * 100;
						$this->hst_rate = $canada_tax_options['ec_option_manitoba_tax_' . $user_level . '_hst'] * 100;
						
					}else if( isset( $canada_tax_options['ec_option_collect_new_brunswick_tax_' . $user_level] ) && $this->shipping_state == "NF" ){
						$this->gst_rate = $canada_tax_options['ec_option_new_brunswick_tax_' . $user_level . '_gst'] * 100;
						$this->pst_rate = $canada_tax_options['ec_option_new_brunswick_tax_' . $user_level . '_pst'] * 100;
						$this->hst_rate = $canada_tax_options['ec_option_new_brunswick_tax_' . $user_level . '_hst'] * 100;
						
					}else if( isset( $canada_tax_options['ec_option_collect_newfoundland_tax_' . $user_level] ) && $this->shipping_state == "NB" ){
						$this->gst_rate = $canada_tax_options['ec_option_newfoundland_tax_' . $user_level . '_gst'] * 100;
						$this->pst_rate = $canada_tax_options['ec_option_newfoundland_tax_' . $user_level . '_pst'] * 100;
						$this->hst_rate = $canada_tax_options['ec_option_newfoundland_tax_' . $user_level . '_hst'] * 100;
						
					}else if( isset( $canada_tax_options['ec_option_collect_northwest_territories_tax_' . $user_level] ) && $this->shipping_state == "NT" ){
						$this->gst_rate = $canada_tax_options['ec_option_northwest_territories_tax_' . $user_level . '_gst'] * 100;
						$this->pst_rate = $canada_tax_options['ec_option_northwest_territories_tax_' . $user_level . '_pst'] * 100;
						$this->hst_rate = $canada_tax_options['ec_option_northwest_territories_tax_' . $user_level . '_hst'] * 100;
						
					}else if( isset( $canada_tax_options['ec_option_collect_nova_scotia_tax_' . $user_level] ) && $this->shipping_state == "NS" ){
						$this->gst_rate = $canada_tax_options['ec_option_nova_scotia_tax_' . $user_level . '_gst'] * 100;
						$this->pst_rate = $canada_tax_options['ec_option_nova_scotia_tax_' . $user_level . '_pst'] * 100;
						$this->hst_rate = $canada_tax_options['ec_option_nova_scotia_tax_' . $user_level . '_hst'] * 100;
						
					}else if( isset( $canada_tax_options['ec_option_collect_nunavut_tax_' . $user_level] ) && $this->shipping_state == "NU" ){
						$this->gst_rate = $canada_tax_options['ec_option_nunavut_tax_' . $user_level . '_gst'] * 100;
						$this->pst_rate = $canada_tax_options['ec_option_nunavut_tax_' . $user_level . '_pst'] * 100;
						$this->hst_rate = $canada_tax_options['ec_option_nunavut_tax_' . $user_level . '_hst'] * 100;
						
					}else if( isset( $canada_tax_options['ec_option_collect_ontario_tax_' . $user_level] ) && $this->shipping_state == "ON" ){
						$this->gst_rate = $canada_tax_options['ec_option_ontario_tax_' . $user_level . '_gst'] * 100;
						$this->pst_rate = $canada_tax_options['ec_option_ontario_tax_' . $user_level . '_pst'] * 100;
						$this->hst_rate = $canada_tax_options['ec_option_ontario_tax_' . $user_level . '_hst'] * 100;
						
					}else if( isset( $canada_tax_options['ec_option_collect_prince_edward_island_tax_' . $user_level] ) && $this->shipping_state == "PE" ){
						$this->gst_rate = $canada_tax_options['ec_option_prince_edward_island_tax_' . $user_level . '_gst'] * 100;
						$this->pst_rate = $canada_tax_options['ec_option_prince_edward_island_tax_' . $user_level . '_pst'] * 100;
						$this->hst_rate = $canada_tax_options['ec_option_prince_edward_island_tax_' . $user_level . '_hst'] * 100;
						
					}else if( isset( $canada_tax_options['ec_option_collect_quebec_tax_' . $user_level] ) && $this->shipping_state == "QC" ){
						$this->gst_rate = $canada_tax_options['ec_option_quebec_tax_' . $user_level . '_gst'] * 100;
						$this->pst_rate = $canada_tax_options['ec_option_quebec_tax_' . $user_level . '_pst'] * 100;
						$this->hst_rate = $canada_tax_options['ec_option_quebec_tax_' . $user_level . '_hst'] * 100;
						
					}else if( isset( $canada_tax_options['ec_option_collect_saskatchewan_tax_' . $user_level] ) && $this->shipping_state == "SK" ){
						$this->gst_rate = $canada_tax_options['ec_option_saskatchewan_tax_' . $user_level . '_gst'] * 100;
						$this->pst_rate = $canada_tax_options['ec_option_saskatchewan_tax_' . $user_level . '_pst'] * 100;
						$this->hst_rate = $canada_tax_options['ec_option_saskatchewan_tax_' . $user_level . '_hst'] * 100;
						
					}else if( isset( $canada_tax_options['ec_option_collect_yukon_tax_' . $user_level] ) && $this->shipping_state == "YT" ){
						$this->gst_rate = $canada_tax_options['ec_option_yukon_tax_' . $user_level . '_gst'] * 100;
						$this->pst_rate = $canada_tax_options['ec_option_yukon_tax_' . $user_level . '_pst'] * 100;
						$this->hst_rate = $canada_tax_options['ec_option_yukon_tax_' . $user_level . '_hst'] * 100;
						
					}
					
					$this->gst = $this->taxable_subtotal * ( $this->gst_rate / 100 );
					$this->pst = $this->taxable_subtotal * ( $this->pst_rate / 100 );
					$this->hst = $this->taxable_subtotal * ( $this->hst_rate / 100 );
					
				}else{ // old canada tax
				
					if( $this->collect_alberta && $this->shipping_state == "AB" ){
						$this->gst = $this->taxable_subtotal * .05;
						$this->gst_rate = 5;
						
					}else if( $this->collect_british_columbia && $this->shipping_state == "BC" ){
						$this->gst = $this->taxable_subtotal * .05;
						$this->gst_rate = 5;
						$this->pst = $this->taxable_subtotal * .07;
						$this->pst_rate = 7;
						
					}else if( $this->collect_manitoba && $this->shipping_state == "MB" ){
						$this->gst = $this->taxable_subtotal * .05;
						$this->gst_rate = 5;
						$this->pst = $this->taxable_subtotal * .08;
						$this->pst_rate = 8;
						
					}else if( $this->collect_newfoundland && $this->shipping_state == "NF" ){
						$this->hst = $this->taxable_subtotal * .13;
						$this->hst_rate = 13;
						
					}else if( $this->collect_new_brunswick && $this->shipping_state == "NB" ){
						$this->hst = $this->taxable_subtotal * .13;
						$this->hst_rate = 13;
						
					}else if( $this->collect_nova_scotia && $this->shipping_state == "NS" ){
						$this->hst = $this->taxable_subtotal * .15;
						$this->hst_rate = 15;
						
					}else if( $this->collect_northwest_territories && $this->shipping_state == "NT" ){
						$this->gst = $this->taxable_subtotal * .05;
						$this->gst_rate = 5;
						
					}else if( $this->collect_nunavut && $this->shipping_state == "NU" ){
						$this->gst = $this->taxable_subtotal * .05;
						$this->gst_rate = 5;
						
					}else if( $this->collect_ontario && $this->shipping_state == "ON" ){
						$this->hst = $this->taxable_subtotal * .13;
						$this->hst_rate = 13;
						
					}else if( $this->collect_prince_edward_island && $this->shipping_state == "PE" ){
						$this->hst = $this->taxable_subtotal * .14;
						$this->hst_rate = 14;
						
					}else if( $this->collect_quebec && $this->shipping_state == "QC" ){
						$this->gst = $this->taxable_subtotal * .05;
						$this->gst_rate = 5;
						$this->pst = $this->taxable_subtotal * .09975;
						$this->pst_rate = 9.975;
						
					}else if( $this->collect_saskatchewan && $this->shipping_state == "SK" ){
						$this->gst = $this->taxable_subtotal * .05;
						$this->gst_rate = 5;
						$this->pst = $this->taxable_subtotal * .05;
						$this->pst_rate = 5;
						
					}else if( $this->collect_yukon && $this->shipping_state == "YT" ){
						$this->gst = $this->taxable_subtotal * .05;
						$this->gst_rate = 5;
						
					}
					
				}// Close different types of canada tax
				
			} // Close Canada Taxation Check
			
		}
		
	}
	
	public function get_square_tax_rates( $taxable, $vatable, $taxcloud_rate ){
		$square_taxrates = array( );
		if( $taxable && get_option( 'ec_option_tax_cloud_api_id' ) != "" && get_option( 'ec_option_tax_cloud_api_key' ) != "" && $taxcloud_rate > 0 ){
			$square_taxrates[] = array( 'Tax Cloud Rate', round( $taxcloud_rate * 100, 2 ), $this->shipping_state, 'ADDITIVE', 'tax' );
		}
		
		$taxrates = $this->mysqli->get_taxrates( );
		foreach( $taxrates as $taxrate ){
			if( $taxable && $taxrate->tax_by_state ){
				$this->state_tax_enabled = true;
				if(	$this->shipping_state == $taxrate->state_code && ( $taxrate->country_code == "" || $this->shipping_country == $taxrate->country_code ) && $taxrate->state_rate > 0 ){
					$square_taxrates[] = array( $taxrate->state_code . ' ' . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tax' ), $taxrate->state_rate, 'ADDITIVE', 'tax' );
				
                }
			}else if( $taxable && $taxrate->tax_by_country && $taxrate->country_rate > 0 ){
				$this->country_tax_enabled = true;
				if( $this->shipping_country == $taxrate->country_code ){
					$square_taxrates[] = array( $taxrate->country_code . ' ' . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tax' ), $taxrate->country_rate, 'ADDITIVE', 'tax' );
				}
                
			}else if( $taxable && $taxrate->tax_by_all && $taxrate->all_rate > 0 ){
				$square_taxrates[] = array( $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tax' ), $taxrate->all_rate, 'ADDITIVE', 'tax' );
				
			}else if( $taxable && $taxrate->tax_by_duty ){
				$square_taxrates[] = array( $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_duty' ), $taxrate->duty_rate, 'ADDITIVE', 'duty' );
				
			}else if( $vatable && ( $taxrate->tax_by_vat || $taxrate->tax_by_single_vat ) ){
				if( get_option( 'ec_option_collect_vat_registration_number' ) && $GLOBALS['ec_user']->vat_registration_number != "" && get_option( 'ec_option_vat_custom_rate' ) > 0 ){
					if( $taxrate->vat_included ){
					   $square_taxrates[] = array( $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_vat' ), get_option( 'ec_option_vat_custom_rate' ), 'INCLUSIVE', 'vat' );
                    }else{
					   $square_taxrates[] = array( $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_vat' ), get_option( 'ec_option_vat_custom_rate' ), 'ADDITIVE', 'vat' );
                    }
                    
				}else if( $taxrate->tax_by_single_vat && $taxrate->vat_rate > 0 ){
					if( $taxrate->vat_included ){
					   $square_taxrates[] = array( $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_vat' ), $taxrate->vat_rate, 'INCLUSIVE', 'vat' );
                    }else{
					   $square_taxrates[] = array( $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_vat' ), $taxrate->vat_rate, 'ADDITIVE', 'vat' );
                    }
				
				}else{
					for( $i=0; $i<count($this->country_list); $i++){
						if( $this->shipping_country == $this->country_list[$i]->iso2_cnt && $this->country_list[$i]->vat_rate_cnt > 0 ){
							if( get_option( 'ec_option_collect_vat_registration_number' ) && $GLOBALS['ec_user']->vat_registration_number != "" && !$this->country_list[$i]->vat_b2b_enabled ){
                                // Ignore for Businesses
                            }else if( get_option( 'ec_option_collect_vat_registration_number' ) && $GLOBALS['ec_cart_data']->cart_data->vat_registration_number != "" && !$this->country_list[$i]->vat_b2b_enabled ){
                                // Ignore for Businesses
                            }else if( $taxrate->vat_included ){
                               $square_taxrates[] = array( $this->shipping_country . ' ' . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_vat' ), $this->country_list[$i]->vat_rate_cnt, 'INCLUSIVE', 'vat' );
                            }else{
                               $square_taxrates[] = array( $this->shipping_country . ' ' . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_vat' ), $this->country_list[$i]->vat_rate_cnt, 'ADDITIVE', 'vat' );
                            }
                            
						}
					}
				}
			}
		}
		
		// Calculate Canada Tax
		if( $taxable && $this->easy_canada_tax_enabled && $this->shipping_country == "CA" ){
			
			$canada_tax_options = get_option( 'ec_option_canada_tax_options' );
			$user_level = $GLOBALS['ec_user']->user_level;
			if( $user_level == 'admin' || $GLOBALS['ec_user']->user_level == "" )
				$user_level = 'shopper';
			
			if( $canada_tax_options ){ // New Version of Canada Tax
			
				$this->gst_rate = 0;
				$this->pst_rate = 0;
				$this->hst_rate = 0;
				
				$gst_tax_rule_name = 'GST ' . $this->shipping_state . ' ' . ucwords( strtolower( $user_level ) );
				$gst_square_id = '';
				$gst_square_var = '';
				$pst_tax_rule_name = 'PST ' . $this->shipping_state . ' ' . ucwords( strtolower( $user_level ) );
				$pst_square_id = '';
				$pst_square_var = '';
				$hst_tax_rule_name = 'HST ' . $this->shipping_state . ' ' . ucwords( strtolower( $user_level ) );
				$hst_square_id = '';
				$hst_square_var = '';
			
				if( isset( $canada_tax_options['ec_option_collect_alberta_tax_' . $user_level] ) && $this->shipping_state == "AB" ){
					$this->gst_rate = $canada_tax_options['ec_option_alberta_tax_' . $user_level . '_gst'] * 100;
					$gst_square_var = 'ec_option_alberta_tax_' . $user_level . '_gst_square_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_alberta_tax_' . $user_level . '_pst'] * 100;
					$pst_square_var = 'ec_option_alberta_tax_' . $user_level . '_pst_square_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_alberta_tax_' . $user_level . '_hst'] * 100;
					$hst_square_var = 'ec_option_alberta_tax_' . $user_level . '_hst_square_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_british_columbia_tax_' . $user_level] ) && $this->shipping_state == "BC" ){
					$this->gst_rate = $canada_tax_options['ec_option_british_columbia_tax_' . $user_level . '_gst'] * 100;
					$gst_square_var = 'ec_option_british_columbia_tax_' . $user_level . '_gst_square_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_british_columbia_tax_' . $user_level . '_pst'] * 100;
					$pst_square_var = 'ec_option_british_columbia_tax_' . $user_level . '_pst_square_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_british_columbia_tax_' . $user_level . '_hst'] * 100;
					$hst_square_var = 'ec_option_british_columbia_tax_' . $user_level . '_hst_square_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_manitoba_tax_' . $user_level] ) && $this->shipping_state == "MB" ){
					$this->gst_rate = $canada_tax_options['ec_option_manitoba_tax_' . $user_level . '_gst'] * 100;
					$gst_square_var = 'ec_option_manitoba_tax_' . $user_level . '_gst_square_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_manitoba_tax_' . $user_level . '_pst'] * 100;
					$pst_square_var = 'ec_option_manitoba_tax_' . $user_level . '_pst_square_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_manitoba_tax_' . $user_level . '_hst'] * 100;
					$hst_square_var = 'ec_option_manitoba_tax_' . $user_level . '_hst_square_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_new_brunswick_tax_' . $user_level] ) && $this->shipping_state == "NF" ){
					$this->gst_rate = $canada_tax_options['ec_option_new_brunswick_tax_' . $user_level . '_gst'] * 100;
					$gst_square_var = 'ec_option_new_brunswick_tax_' . $user_level . '_gst_square_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_new_brunswick_tax_' . $user_level . '_pst'] * 100;
					$pst_square_var = 'ec_option_new_brunswick_tax_' . $user_level . '_pst_square_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_new_brunswick_tax_' . $user_level . '_hst'] * 100;
					$hst_square_var = 'ec_option_new_brunswick_tax_' . $user_level . '_hst_square_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_newfoundland_tax_' . $user_level] ) && $this->shipping_state == "NB" ){
					$this->gst_rate = $canada_tax_options['ec_option_newfoundland_tax_' . $user_level . '_gst'] * 100;
					$gst_square_var = 'ec_option_newfoundland_tax_' . $user_level . '_gst_square_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_newfoundland_tax_' . $user_level . '_pst'] * 100;
					$pst_square_var = 'ec_option_newfoundland_tax_' . $user_level . '_pst_square_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_newfoundland_tax_' . $user_level . '_hst'] * 100;
					$hst_square_var = 'ec_option_newfoundland_tax_' . $user_level . '_hst_square_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_northwest_territories_tax_' . $user_level] ) && $this->shipping_state == "NT" ){
					$this->gst_rate = $canada_tax_options['ec_option_northwest_territories_tax_' . $user_level . '_gst'] * 100;
					$gst_square_var = 'ec_option_northwest_territories_tax_' . $user_level . '_gst_square_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_northwest_territories_tax_' . $user_level . '_pst'] * 100;
					$pst_square_var = 'ec_option_northwest_territories_tax_' . $user_level . '_pst_square_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_northwest_territories_tax_' . $user_level . '_hst'] * 100;
					$hst_square_var = 'ec_option_northwest_territories_tax_' . $user_level . '_hst_square_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_nova_scotia_tax_' . $user_level] ) && $this->shipping_state == "NS" ){
					$this->gst_rate = $canada_tax_options['ec_option_nova_scotia_tax_' . $user_level . '_gst'] * 100;
					$gst_square_var = 'ec_option_nova_scotia_tax_' . $user_level . '_gst_square_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_nova_scotia_tax_' . $user_level . '_pst'] * 100;
					$pst_square_var = 'ec_option_nova_scotia_tax_' . $user_level . '_pst_square_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_nova_scotia_tax_' . $user_level . '_hst'] * 100;
					$hst_square_var = 'ec_option_nova_scotia_tax_' . $user_level . '_hst_square_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_nunavut_tax_' . $user_level] ) && $this->shipping_state == "NU" ){
					$this->gst_rate = $canada_tax_options['ec_option_nunavut_tax_' . $user_level . '_gst'] * 100;
					$gst_square_var = 'ec_option_collect_nunavut_tax_' . $user_level . '_gst_square_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_nunavut_tax_' . $user_level . '_pst'] * 100;
					$pst_square_var = 'ec_option_collect_nunavut_tax_' . $user_level . '_pst_square_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_nunavut_tax_' . $user_level . '_hst'] * 100;
					$hst_square_var = 'ec_option_collect_nunavut_tax_' . $user_level . '_hst_square_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_ontario_tax_' . $user_level] ) && $this->shipping_state == "ON" ){
					$this->gst_rate = $canada_tax_options['ec_option_ontario_tax_' . $user_level . '_gst'] * 100;
					$gst_square_var = 'ec_option_ontario_tax_' . $user_level . '_gst_square_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_ontario_tax_' . $user_level . '_pst'] * 100;
					$pst_square_var = 'ec_option_ontario_tax_' . $user_level . '_pst_square_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_ontario_tax_' . $user_level . '_hst'] * 100;
					$hst_square_var = 'ec_option_ontario_tax_' . $user_level . '_hst_square_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_prince_edward_island_tax_' . $user_level] ) && $this->shipping_state == "PE" ){
					$this->gst_rate = $canada_tax_options['ec_option_prince_edward_island_tax_' . $user_level . '_gst'] * 100;
					$gst_square_var = 'ec_option_prince_edward_island_tax_' . $user_level . '_gst_square_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_prince_edward_island_tax_' . $user_level . '_pst'] * 100;
					$pst_square_var = 'ec_option_prince_edward_island_tax_' . $user_level . '_pst_square_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_prince_edward_island_tax_' . $user_level . '_hst'] * 100;
					$hst_square_var = 'ec_option_prince_edward_island_tax_' . $user_level . '_hst_square_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_quebec_tax_' . $user_level] ) && $this->shipping_state == "QC" ){
					$this->gst_rate = $canada_tax_options['ec_option_quebec_tax_' . $user_level . '_gst'] * 100;
					$gst_square_var = 'ec_option_quebec_tax_' . $user_level . '_gst_square_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_quebec_tax_' . $user_level . '_pst'] * 100;
					$pst_square_var = 'ec_option_quebec_tax_' . $user_level . '_pst_square_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_quebec_tax_' . $user_level . '_hst'] * 100;
					$hst_square_var = 'ec_option_quebec_tax_' . $user_level . '_hst_square_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_saskatchewan_tax_' . $user_level] ) && $this->shipping_state == "SK" ){
					$this->gst_rate = $canada_tax_options['ec_option_saskatchewan_tax_' . $user_level . '_gst'] * 100;
					$gst_square_var = 'ec_option_saskatchewan_tax_' . $user_level . '_gst_square_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_saskatchewan_tax_' . $user_level . '_pst'] * 100;
					$pst_square_var = 'ec_option_saskatchewan_tax_' . $user_level . '_pst_square_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_saskatchewan_tax_' . $user_level . '_hst'] * 100;
					$hst_square_var = 'ec_option_saskatchewan_tax_' . $user_level . '_hst_square_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_yukon_tax_' . $user_level] ) && $this->shipping_state == "YT" ){
					$this->gst_rate = $canada_tax_options['ec_option_yukon_tax_' . $user_level . '_gst'] * 100;
					$gst_square_var = 'ec_option_yukon_tax_' . $user_level . '_gst_square_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_yukon_tax_' . $user_level . '_pst'] * 100;
					$pst_square_var = 'ec_option_yukon_tax_' . $user_level . '_pst_square_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_yukon_tax_' . $user_level . '_hst'] * 100;
					$hst_square_var = 'ec_option_yukon_tax_' . $user_level . '_hst_square_id';
					
				}
				
				$gst_square_id  = ( isset( $canada_tax_options[$gst_square_var] ) ) ? $canada_tax_options[$gst_square_var] : false;
				$pst_square_id  = ( isset( $canada_tax_options[$pst_square_var] ) ) ? $canada_tax_options[$pst_square_var] : false;
				$hst_square_id  = ( isset( $canada_tax_options[$hst_square_var] ) ) ? $canada_tax_options[$hst_square_var] : false;
				
				
				$this->gst = $this->taxable_subtotal * ( $this->gst_rate / 100 );
				$this->pst = $this->taxable_subtotal * ( $this->pst_rate / 100 );
				$this->hst = $this->taxable_subtotal * ( $this->hst_rate / 100 );
				
				if( $this->gst_rate > 0 ){
					$square_taxrates[] = array( $gst_tax_rule_name, $this->gst_rate, 'ADDITIVE', 'tax' );
				}
				
				if( $this->pst_rate > 0 ){
					$square_taxrates[] = array( $pst_tax_rule_name, $this->pst_rate, 'ADDITIVE', 'tax' );
				}
				
				if( $this->hst_rate > 0 ){
					$square_taxrates[] = array( $$hst_tax_rule_name, $this->hst_rate, 'ADDITIVE', 'tax' );
				}
				
			}
		}
		return $square_taxrates;
	}
	
	public function get_stripe_tax_rates( $taxable, $vatable, $taxcloud_rate ){
		$stripe_taxrates = array( );
		if( $taxable && get_option( 'ec_option_tax_cloud_api_id' ) != "" && get_option( 'ec_option_tax_cloud_api_key' ) != "" && $taxcloud_rate > 0 ){
			$stripe_taxrates[] = $this->get_stripe_tax_rate( '', 'Tax Cloud Rate', round( $taxcloud_rate * 100, 2 ), $this->shipping_state );
		}
		
		$taxrates = $this->mysqli->get_taxrates( );
		foreach( $taxrates as $taxrate ){
			if( $taxable && $taxrate->tax_by_state ){
				$this->state_tax_enabled = true;
				if(	$this->shipping_state == $taxrate->state_code && ( $taxrate->country_code == "" || $this->shipping_country == $taxrate->country_code ) && $taxrate->state_rate > 0 ){
					if( $taxrate->stripe_taxrate_id != $new_stripe_taxrate_id = $this->get_stripe_tax_rate( $taxrate->stripe_taxrate_id, $taxrate->state_code . ' ' . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tax' ), $taxrate->state_rate, $taxrate->state_code ) ){
						$this->mysqli->update_stripe_taxrate_id( $taxrate->taxrate_id, $new_stripe_taxrate_id );
					}
					$stripe_taxrates[] = $new_stripe_taxrate_id;
				}
			}else if( $taxable && $taxrate->tax_by_country && $taxrate->country_rate > 0 ){
				$this->country_tax_enabled = true;
				if( $this->shipping_country == $taxrate->country_code ){
					if( $taxrate->stripe_taxrate_id != $new_stripe_taxrate_id = $this->get_stripe_tax_rate( $taxrate->stripe_taxrate_id, $taxrate->country_code . ' ' . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tax' ), $taxrate->country_rate, $taxrate->country_code ) ){
						$this->mysqli->update_stripe_taxrate_id( $taxrate->taxrate_id, $new_stripe_taxrate_id );
					}
					$stripe_taxrates[] = $new_stripe_taxrate_id;
				}
			}else if( $taxable && $taxrate->tax_by_all && $taxrate->all_rate > 0 ){
				if( $taxrate->stripe_taxrate_id != $new_stripe_taxrate_id = $this->get_stripe_tax_rate( $taxrate->stripe_taxrate_id, $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tax' ), $taxrate->all_rate ) ){
					$this->mysqli->update_stripe_taxrate_id( $taxrate->taxrate_id, $new_stripe_taxrate_id );
				}
				$stripe_taxrates[] = $new_stripe_taxrate_id;
				
			}else if( $taxable && $taxrate->tax_by_duty ){
				
			}else if( $vatable && ( $taxrate->tax_by_vat || $taxrate->tax_by_single_vat ) ){
				if( get_option( 'ec_option_collect_vat_registration_number' ) && $GLOBALS['ec_user']->vat_registration_number != "" && get_option( 'ec_option_vat_custom_rate' ) > 0 ){
					if( get_option( 'ec_option_collect_vat_registration_number_stripe_id' ) != $new_stripe_taxrate_id = $this->get_stripe_tax_rate( get_option( 'ec_option_collect_vat_registration_number_stripe_id' ), $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_vat' ), get_option( 'ec_option_vat_custom_rate' ), '', $taxrate->vat_included ) ){
						update_option( 'ec_option_collect_vat_registration_number_stripe_id', $new_stripe_taxrate_id );
					}
					$stripe_taxrates[] = $new_stripe_taxrate_id;
				
				}else if( $taxrate->tax_by_single_vat && $taxrate->vat_rate > 0 ){
					if( $taxrate->stripe_taxrate_id != $new_stripe_taxrate_id = $this->get_stripe_tax_rate( $taxrate->stripe_taxrate_id, $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_vat' ), $taxrate->vat_rate, '', $taxrate->vat_included ) ){
						$this->mysqli->update_stripe_taxrate_id( $taxrate->taxrate_id, $new_stripe_taxrate_id );
					}
					$stripe_taxrates[] = $new_stripe_taxrate_id;
				
				}else{
					for( $i=0; $i<count($this->country_list); $i++){
						if( $this->shipping_country == $this->country_list[$i]->iso2_cnt && $this->country_list[$i]->vat_rate_cnt > 0 ){
							$this->vat_country_match = true;
							$this->vat_rate = $this->country_list[$i]->vat_rate_cnt;
							
							if( get_option( 'ec_option_collect_vat_registration_number' ) && $GLOBALS['ec_user']->vat_registration_number != "" && !$this->country_list[$i]->vat_b2b_enabled ){
                                // Ignore for Businesses
                            }else if( get_option( 'ec_option_collect_vat_registration_number' ) && $GLOBALS['ec_cart_data']->cart_data->vat_registration_number != "" && !$this->country_list[$i]->vat_b2b_enabled ){
                                // Ignore for Businesses
                            }else if( $this->country_list[$i]->stripe_taxrate_id != $new_stripe_taxrate_id = $this->get_stripe_tax_rate( $this->country_list[$i]->stripe_taxrate_id, $this->shipping_country . ' ' . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_vat' ), $this->country_list[$i]->vat_rate_cnt, $this->shipping_country, $taxrate->vat_included ) ){
								$this->mysqli->update_stripe_country_taxrate_id( $this->country_list[$i]->id_cnt, $new_stripe_taxrate_id );
							}
							$stripe_taxrates[] = $new_stripe_taxrate_id;
						}
					}
				}
			}
		}
		
		// Calculate Canada Tax
		if( $taxable && $this->easy_canada_tax_enabled && $this->shipping_country == "CA" ){
			
			$canada_tax_options = get_option( 'ec_option_canada_tax_options' );
			$user_level = $GLOBALS['ec_user']->user_level;
			if( $user_level == 'admin' || $GLOBALS['ec_user']->user_level == "" )
				$user_level = 'shopper';
			
			if( $canada_tax_options ){ // New Version of Canada Tax
			
				$this->gst_rate = 0;
				$this->pst_rate = 0;
				$this->hst_rate = 0;
				
				$gst_tax_rule_name = 'GST ' . $this->shipping_state . ' ' . ucwords( strtolower( $user_level ) );
				$gst_stripe_id = '';
				$gst_stripe_var = '';
				$pst_tax_rule_name = 'PST ' . $this->shipping_state . ' ' . ucwords( strtolower( $user_level ) );
				$pst_stripe_id = '';
				$pst_stripe_var = '';
				$hst_tax_rule_name = 'HST ' . $this->shipping_state . ' ' . ucwords( strtolower( $user_level ) );
				$hst_stripe_id = '';
				$hst_stripe_var = '';
			
				if( isset( $canada_tax_options['ec_option_collect_alberta_tax_' . $user_level] ) && $this->shipping_state == "AB" ){
					$this->gst_rate = $canada_tax_options['ec_option_alberta_tax_' . $user_level . '_gst'] * 100;
					$gst_stripe_var = 'ec_option_alberta_tax_' . $user_level . '_gst_stripe_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_alberta_tax_' . $user_level . '_pst'] * 100;
					$pst_stripe_var = 'ec_option_alberta_tax_' . $user_level . '_pst_stripe_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_alberta_tax_' . $user_level . '_hst'] * 100;
					$hst_stripe_var = 'ec_option_alberta_tax_' . $user_level . '_hst_stripe_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_british_columbia_tax_' . $user_level] ) && $this->shipping_state == "BC" ){
					$this->gst_rate = $canada_tax_options['ec_option_british_columbia_tax_' . $user_level . '_gst'] * 100;
					$gst_stripe_var = 'ec_option_british_columbia_tax_' . $user_level . '_gst_stripe_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_british_columbia_tax_' . $user_level . '_pst'] * 100;
					$pst_stripe_var = 'ec_option_british_columbia_tax_' . $user_level . '_pst_stripe_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_british_columbia_tax_' . $user_level . '_hst'] * 100;
					$hst_stripe_var = 'ec_option_british_columbia_tax_' . $user_level . '_hst_stripe_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_manitoba_tax_' . $user_level] ) && $this->shipping_state == "MB" ){
					$this->gst_rate = $canada_tax_options['ec_option_manitoba_tax_' . $user_level . '_gst'] * 100;
					$gst_stripe_var = 'ec_option_manitoba_tax_' . $user_level . '_gst_stripe_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_manitoba_tax_' . $user_level . '_pst'] * 100;
					$pst_stripe_var = 'ec_option_manitoba_tax_' . $user_level . '_pst_stripe_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_manitoba_tax_' . $user_level . '_hst'] * 100;
					$hst_stripe_var = 'ec_option_manitoba_tax_' . $user_level . '_hst_stripe_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_new_brunswick_tax_' . $user_level] ) && $this->shipping_state == "NF" ){
					$this->gst_rate = $canada_tax_options['ec_option_new_brunswick_tax_' . $user_level . '_gst'] * 100;
					$gst_stripe_var = 'ec_option_new_brunswick_tax_' . $user_level . '_gst_stripe_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_new_brunswick_tax_' . $user_level . '_pst'] * 100;
					$pst_stripe_var = 'ec_option_new_brunswick_tax_' . $user_level . '_pst_stripe_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_new_brunswick_tax_' . $user_level . '_hst'] * 100;
					$hst_stripe_var = 'ec_option_new_brunswick_tax_' . $user_level . '_hst_stripe_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_newfoundland_tax_' . $user_level] ) && $this->shipping_state == "NB" ){
					$this->gst_rate = $canada_tax_options['ec_option_newfoundland_tax_' . $user_level . '_gst'] * 100;
					$gst_stripe_var = 'ec_option_newfoundland_tax_' . $user_level . '_gst_stripe_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_newfoundland_tax_' . $user_level . '_pst'] * 100;
					$pst_stripe_var = 'ec_option_newfoundland_tax_' . $user_level . '_pst_stripe_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_newfoundland_tax_' . $user_level . '_hst'] * 100;
					$hst_stripe_var = 'ec_option_newfoundland_tax_' . $user_level . '_hst_stripe_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_northwest_territories_tax_' . $user_level] ) && $this->shipping_state == "NT" ){
					$this->gst_rate = $canada_tax_options['ec_option_northwest_territories_tax_' . $user_level . '_gst'] * 100;
					$gst_stripe_var = 'ec_option_northwest_territories_tax_' . $user_level . '_gst_stripe_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_northwest_territories_tax_' . $user_level . '_pst'] * 100;
					$pst_stripe_var = 'ec_option_northwest_territories_tax_' . $user_level . '_pst_stripe_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_northwest_territories_tax_' . $user_level . '_hst'] * 100;
					$hst_stripe_var = 'ec_option_northwest_territories_tax_' . $user_level . '_hst_stripe_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_nova_scotia_tax_' . $user_level] ) && $this->shipping_state == "NS" ){
					$this->gst_rate = $canada_tax_options['ec_option_nova_scotia_tax_' . $user_level . '_gst'] * 100;
					$gst_stripe_var = 'ec_option_nova_scotia_tax_' . $user_level . '_gst_stripe_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_nova_scotia_tax_' . $user_level . '_pst'] * 100;
					$pst_stripe_var = 'ec_option_nova_scotia_tax_' . $user_level . '_pst_stripe_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_nova_scotia_tax_' . $user_level . '_hst'] * 100;
					$hst_stripe_var = 'ec_option_nova_scotia_tax_' . $user_level . '_hst_stripe_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_nunavut_tax_' . $user_level] ) && $this->shipping_state == "NU" ){
					$this->gst_rate = $canada_tax_options['ec_option_nunavut_tax_' . $user_level . '_gst'] * 100;
					$gst_stripe_var = 'ec_option_collect_nunavut_tax_' . $user_level . '_gst_stripe_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_nunavut_tax_' . $user_level . '_pst'] * 100;
					$pst_stripe_var = 'ec_option_collect_nunavut_tax_' . $user_level . '_pst_stripe_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_nunavut_tax_' . $user_level . '_hst'] * 100;
					$hst_stripe_var = 'ec_option_collect_nunavut_tax_' . $user_level . '_hst_stripe_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_ontario_tax_' . $user_level] ) && $this->shipping_state == "ON" ){
					$this->gst_rate = $canada_tax_options['ec_option_ontario_tax_' . $user_level . '_gst'] * 100;
					$gst_stripe_var = 'ec_option_ontario_tax_' . $user_level . '_gst_stripe_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_ontario_tax_' . $user_level . '_pst'] * 100;
					$pst_stripe_var = 'ec_option_ontario_tax_' . $user_level . '_pst_stripe_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_ontario_tax_' . $user_level . '_hst'] * 100;
					$hst_stripe_var = 'ec_option_ontario_tax_' . $user_level . '_hst_stripe_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_prince_edward_island_tax_' . $user_level] ) && $this->shipping_state == "PE" ){
					$this->gst_rate = $canada_tax_options['ec_option_prince_edward_island_tax_' . $user_level . '_gst'] * 100;
					$gst_stripe_var = 'ec_option_prince_edward_island_tax_' . $user_level . '_gst_stripe_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_prince_edward_island_tax_' . $user_level . '_pst'] * 100;
					$pst_stripe_var = 'ec_option_prince_edward_island_tax_' . $user_level . '_pst_stripe_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_prince_edward_island_tax_' . $user_level . '_hst'] * 100;
					$hst_stripe_var = 'ec_option_prince_edward_island_tax_' . $user_level . '_hst_stripe_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_quebec_tax_' . $user_level] ) && $this->shipping_state == "QC" ){
					$this->gst_rate = $canada_tax_options['ec_option_quebec_tax_' . $user_level . '_gst'] * 100;
					$gst_stripe_var = 'ec_option_quebec_tax_' . $user_level . '_gst_stripe_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_quebec_tax_' . $user_level . '_pst'] * 100;
					$pst_stripe_var = 'ec_option_quebec_tax_' . $user_level . '_pst_stripe_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_quebec_tax_' . $user_level . '_hst'] * 100;
					$hst_stripe_var = 'ec_option_quebec_tax_' . $user_level . '_hst_stripe_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_saskatchewan_tax_' . $user_level] ) && $this->shipping_state == "SK" ){
					$this->gst_rate = $canada_tax_options['ec_option_saskatchewan_tax_' . $user_level . '_gst'] * 100;
					$gst_stripe_var = 'ec_option_saskatchewan_tax_' . $user_level . '_gst_stripe_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_saskatchewan_tax_' . $user_level . '_pst'] * 100;
					$pst_stripe_var = 'ec_option_saskatchewan_tax_' . $user_level . '_pst_stripe_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_saskatchewan_tax_' . $user_level . '_hst'] * 100;
					$hst_stripe_var = 'ec_option_saskatchewan_tax_' . $user_level . '_hst_stripe_id';
					
				}else if( isset( $canada_tax_options['ec_option_collect_yukon_tax_' . $user_level] ) && $this->shipping_state == "YT" ){
					$this->gst_rate = $canada_tax_options['ec_option_yukon_tax_' . $user_level . '_gst'] * 100;
					$gst_stripe_var = 'ec_option_yukon_tax_' . $user_level . '_gst_stripe_id';
					
					$this->pst_rate = $canada_tax_options['ec_option_yukon_tax_' . $user_level . '_pst'] * 100;
					$pst_stripe_var = 'ec_option_yukon_tax_' . $user_level . '_pst_stripe_id';
					
					$this->hst_rate = $canada_tax_options['ec_option_yukon_tax_' . $user_level . '_hst'] * 100;
					$hst_stripe_var = 'ec_option_yukon_tax_' . $user_level . '_hst_stripe_id';
					
				}
				
				$gst_stripe_id  = ( isset( $canada_tax_options[$gst_stripe_var] ) ) ? $canada_tax_options[$gst_stripe_var] : false;
				$pst_stripe_id  = ( isset( $canada_tax_options[$pst_stripe_var] ) ) ? $canada_tax_options[$pst_stripe_var] : false;
				$hst_stripe_id  = ( isset( $canada_tax_options[$hst_stripe_var] ) ) ? $canada_tax_options[$hst_stripe_var] : false;
				
				
				$this->gst = $this->taxable_subtotal * ( $this->gst_rate / 100 );
				$this->pst = $this->taxable_subtotal * ( $this->pst_rate / 100 );
				$this->hst = $this->taxable_subtotal * ( $this->hst_rate / 100 );
				
				if( $this->gst_rate > 0 ){
					if( $gst_stripe_id != $new_stripe_taxrate_id = $this->get_stripe_tax_rate( $gst_stripe_id, $gst_tax_rule_name, $this->gst_rate ) ){
						$canada_tax_options[$gst_stripe_id] = $new_stripe_taxrate_id;
					}
					$stripe_taxrates[] = $new_stripe_taxrate_id;
				}
				
				if( $this->pst_rate > 0 ){
					if( $pst_stripe_id != $new_stripe_taxrate_id = $this->get_stripe_tax_rate( $pst_stripe_id, $pst_tax_rule_name, $this->pst_rate ) ){
						$canada_tax_options[$pst_stripe_id] = $new_stripe_taxrate_id;
					}
					$stripe_taxrates[] = $new_stripe_taxrate_id;
				}
				
				if( $this->hst_rate > 0 ){
					if( $hst_stripe_id != $new_stripe_taxrate_id = $this->get_stripe_tax_rate( $hst_stripe_id, $hst_tax_rule_name, $this->hst_rate ) ){
						$canada_tax_options[$hst_stripe_id] = $new_stripe_taxrate_id;
					}
					$stripe_taxrates[] = $new_stripe_taxrate_id;
				}
				
			}
		}
		return $stripe_taxrates;
	}
	
	public function get_stripe_tax_rate( $stripe_taxrate_id, $display_name, $tax_rate, $jurisdiction = '', $inclusive = false ){
		if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
			$stripe = new ec_stripe( );
		else
			$stripe = new ec_stripe_connect( );
			
		if( $stripe_taxrate_id != '' ){
			$taxrate_check_result = $stripe->get_taxrate( $stripe_taxrate_id );
			if( $taxrate_check_result ){
				if( number_format( $taxrate_check_result->percentage, 3 ) == number_format( $tax_rate, 3 ) && (bool) $taxrate_check_result->inclusive == $inclusive ){
					return $taxrate_check_result->id;
				}
			}
		}
		$taxrate_item = $stripe->add_taxrate( $display_name, $tax_rate, $jurisdiction, $inclusive );
		if( !$taxrate_item )
			return false;
		
		return $taxrate_item->id;
	}
	
	public function get_tax_rate( ){
		return $this->state_tax_rate + $this->all_tax_rate + $this->country_tax_rate + $this->gst_rate + $this->hst_rate + $this->pst_rate + $this->vat_rate;
	}
	
	public function is_tax_enabled( ){
		if( $this->state_tax_enabled || $this->country_tax_enabled || $this->all_tax_enabled || $this->tax_cloud_enabled )
			return true;
		else
			return false;
	}
	
	public function is_duty_enabled( ){
		if( $this->duty_enabled && $this->duty_country_match )
			return true;
		else
			return false;
	}
	
	public function is_vat_enabled( ){
		if( $this->vat_enabled )
			return true;
		else
			return false;
	}
	
}

?>