<?php
class wp_easycart_admin_shipping{
	
	private $wpdb;
	
	public $shipping_rates_file;
	public $success_messages_file;
	public $shipping_rate_options_file;
	public $price_triggers_file;
	public $weight_triggers_file;
	public $quantity_triggers_file;
	public $percentage_based_file;
	public $static_rates_file;
	public $fraktjakt_file;
	
	public $country_list_file;
	public $state_list_file;
	public $shipping_zones_list_file;
	public $basic_shipping_options_file;
	
	public $edit_zone;
	public $edit_zone_item;
		
	public function __construct( ){
		// Keep reference to wpdb
		global $wpdb;
		$this->wpdb =& $wpdb;
		
		// Setup File Names 
		$this->shipping_rates_file 			= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/shipping/shipping-rates.php';
		$this->shipping_setup_file 			= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/shipping/shipping-settings.php';
		$this->shipping_rate_options_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/shipping/shipping-rate-options.php';
		$this->price_triggers_file			= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/shipping/price-based.php';
		$this->weight_triggers_file		 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/shipping/weight-based.php';
		$this->quantity_triggers_file 		= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/shipping/quantity-based.php';
		$this->percentage_based_file 		= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/shipping/percentage-rates.php';
		$this->static_rates_file 			= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/shipping/static-rates.php';
		$this->fraktjakt_file		 		= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/shipping/fraktjakt.php';
		
		$this->country_list_file		 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/shipping/country-list.php';
		$this->state_list_file		 		= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/shipping/state-list.php';
		$this->shipping_zones_list_file		= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/shipping/shipping-zones.php';
		$this->basic_shipping_options_file	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/shipping/shipping-basic-options.php';
		
		$this->edit_zone = NULL;
		$this->edit_zone_item = NULL;
		if( isset( $_GET['action'] ) && $_GET['action'] == "edit-zone" ){
			$this->edit_zone = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT ec_zone.* FROM ec_zone WHERE zone_id = %d", $_GET['zone_id'] ) );
		}
		if( isset( $_GET['action'] ) && $_GET['action'] == "edit-zone-item" ){
			$this->edit_zone = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT ec_zone_to_location.* FROM ec_zone_to_location WHERE zone_to_location = %d", $_GET['zone_to_location_id'] ) );
		}
		
		// Actions
		add_action( 'wpeasycart_admin_shipping_rates_methods', array( $this, 'load_shipping_methods' ) );
		add_action( 'wpeasycart_admin_shipping_rates', array( $this, 'load_price_triggers' ) );
		add_action( 'wpeasycart_admin_shipping_rates', array( $this, 'load_weight_triggers' ) );
		add_action( 'wpeasycart_admin_shipping_rates', array( $this, 'load_quantity_triggers' ) );
		add_action( 'wpeasycart_admin_shipping_rates', array( $this, 'load_percentage_based' ) );
		add_action( 'wpeasycart_admin_shipping_rates', array( $this, 'load_static_rates' ) );
		add_action( 'wpeasycart_admin_shipping_rates', array( $this, 'load_fraktjakt' ) );
		
		add_action( 'wpeasycart_admin_shipping_setup', array( $this, 'load_basic_shipping_options' ), 1 );
		add_action( 'wpeasycart_admin_shipping_setup', array( $this, 'load_country_list' ) );
		add_action( 'wpeasycart_admin_shipping_setup', array( $this, 'load_state_list' ) );
		add_action( 'wpeasycart_admin_shipping_setup', array( $this, 'load_shipping_zones_list' ) );
	}
	
	public function load_shipping_rates( ){
		include( $this->shipping_rates_file );
	}
	
	public function load_shipping_setup( ){
		include( $this->shipping_setup_file );
	}
	
	public function load_shipping_methods( ){
		include( $this->shipping_rate_options_file );
	}
	
	public function load_price_triggers( ){
		include( $this->price_triggers_file );
	}
	
	public function load_weight_triggers( ){
		include( $this->weight_triggers_file );
	}
	
	public function load_quantity_triggers( ){
		include( $this->quantity_triggers_file );
	}
	
	public function load_percentage_based( ){
		include( $this->percentage_based_file );
	}
	
	public function load_static_rates( ){
		include( $this->static_rates_file );
	}
	
	public function load_fraktjakt( ){
		include( $this->fraktjakt_file );
	}
	
	public function load_country_list( ){
		include( $this->country_list_file );
	}
	
	public function load_state_list( ){
		include( $this->state_list_file );
	}
	
	public function load_shipping_zones_list( ){
		include( $this->shipping_zones_list_file );
	}
	
	public function load_basic_shipping_options( ){
		include( $this->basic_shipping_options_file );
	}
	
	/* Shipping */
	public function update_shipping_method( ){
		$this->wpdb->query( $this->wpdb->prepare( "UPDATE ec_setting SET shipping_method = %s", $_POST['ec_option_shipping_method'] ) );
	}
	
	public function delete_shipping_rate( $shippingrate_id ){
		$this->wpdb->query( $this->wpdb->prepare( "DELETE FROM ec_shippingrate WHERE shippingrate_id = %d", $shippingrate_id ) );
	}
	
	/* Shipping Price Trigger */
	public function add_shipping_price_trigger( ){
		$this->wpdb->query( $this->wpdb->prepare( "INSERT INTO ec_shippingrate( is_price_based, trigger_rate, shipping_rate, zone_id ) VALUES( 1, %s, %s, %d )", $_POST['ec_admin_new_price_trigger'], $_POST['ec_admin_new_price_trigger_rate'], $_POST['ec_admin_new_price_trigger_zone_id'] ) );
		return $this->wpdb->insert_id;
	}
	
	public function update_shipping_price_triggers( ){
		$shipping_rates = $this->wpdb->get_results( "SELECT * FROM ec_shippingrate WHERE is_price_based = 1" );
		foreach( $shipping_rates as $trigger ){
			$this->wpdb->query( $this->wpdb->prepare( "UPDATE ec_shippingrate SET trigger_rate = %s, shipping_rate = %s, zone_id = %d WHERE shippingrate_id = %d", $_POST['ec_admin_price_trigger_'.$trigger->shippingrate_id], $_POST['ec_admin_price_trigger_rate_'.$trigger->shippingrate_id], $_POST['ec_admin_price_trigger_zone_id_'.$trigger->shippingrate_id], $trigger->shippingrate_id ) );
		}
	}
	
	/* Shipping Weight Trigger */
	public function add_shipping_weight_trigger( ){
		$this->wpdb->query( $this->wpdb->prepare( "INSERT INTO ec_shippingrate( is_weight_based, trigger_rate, shipping_rate, zone_id ) VALUES( 1, %s, %s, %d )", $_POST['ec_admin_new_weight_trigger'], $_POST['ec_admin_new_weight_trigger_rate'], $_POST['ec_admin_new_weight_trigger_zone_id'] ) );
		return $this->wpdb->insert_id;
	}
	
	public function update_shipping_weight_triggers( ){
		$shipping_rates = $this->wpdb->get_results( "SELECT * FROM ec_shippingrate WHERE is_weight_based = 1" );
		foreach( $shipping_rates as $trigger ){
			$this->wpdb->query( $this->wpdb->prepare( "UPDATE ec_shippingrate SET trigger_rate = %s, shipping_rate = %s, zone_id = %d WHERE shippingrate_id = %d", $_POST['ec_admin_weight_trigger_'.$trigger->shippingrate_id], $_POST['ec_admin_weight_trigger_rate_'.$trigger->shippingrate_id], $_POST['ec_admin_weight_trigger_zone_id_'.$trigger->shippingrate_id], $trigger->shippingrate_id ) );
		}
	}
	
	/* Shipping Quantity Trigger */
	public function add_shipping_quantity_trigger( ){
		$this->wpdb->query( $this->wpdb->prepare( "INSERT INTO ec_shippingrate( is_quantity_based, trigger_rate, shipping_rate, zone_id ) VALUES( 1, %s, %s, %d )", $_POST['ec_admin_new_quantity_trigger'], $_POST['ec_admin_new_quantity_trigger_rate'], $_POST['ec_admin_new_quantity_trigger_zone_id'] ) );
		return $this->wpdb->insert_id;
	}
	
	public function update_shipping_quantity_triggers( ){
		$shipping_rates = $this->wpdb->get_results( "SELECT * FROM ec_shippingrate WHERE is_quantity_based = 1" );
		foreach( $shipping_rates as $trigger ){
			$this->wpdb->query( $this->wpdb->prepare( "UPDATE ec_shippingrate SET trigger_rate = %s, shipping_rate = %s, zone_id = %d WHERE shippingrate_id = %d", $_POST['ec_admin_quantity_trigger_'.$trigger->shippingrate_id], $_POST['ec_admin_quantity_trigger_rate_'.$trigger->shippingrate_id], $_POST['ec_admin_quantity_trigger_zone_id_'.$trigger->shippingrate_id], $trigger->shippingrate_id ) );
		}
	}
	
	/* Shipping Percentage Trigger */
	public function add_shipping_percentage_trigger( ){
		$this->wpdb->query( $this->wpdb->prepare( "INSERT INTO ec_shippingrate( is_percentage_based, trigger_rate, shipping_rate, zone_id ) VALUES( 1, %s, %s, %d )", $_POST['ec_admin_new_percentage_trigger'], $_POST['ec_admin_new_percentage_trigger_rate'], $_POST['ec_admin_new_percentage_trigger_zone_id'] ) );
		return $this->wpdb->insert_id;
	}
	
	public function update_shipping_percentage_triggers( ){
		$shipping_rates = $this->wpdb->get_results( "SELECT * FROM ec_shippingrate WHERE is_percentage_based = 1" );
		foreach( $shipping_rates as $trigger ){
			$this->wpdb->query( $this->wpdb->prepare( "UPDATE ec_shippingrate SET trigger_rate = %s, shipping_rate = %s, zone_id = %d WHERE shippingrate_id = %d", $_POST['ec_admin_percentage_trigger_'.$trigger->shippingrate_id], $_POST['ec_admin_percentage_trigger_rate_'.$trigger->shippingrate_id], $_POST['ec_admin_percentage_trigger_zone_id_'.$trigger->shippingrate_id], $trigger->shippingrate_id ) );
		}
	}
	
	/* Shipping Static Methods */
	public function add_shipping_static_method( ){
		$free_shipping_at = '-1.000';
		if( $_POST['ec_admin_new_method_trigger_free_shipping_at'] != '' ){
			$free_shipping_at = $_POST['ec_admin_new_method_trigger_free_shipping_at'];
		}
		$this->wpdb->query( $this->wpdb->prepare( "INSERT INTO ec_shippingrate( is_method_based, shipping_label, shipping_rate, zone_id, free_shipping_at, shipping_order ) VALUES( 1, %s, %s, %d, %s, %d )", stripslashes_deep( $_POST['ec_admin_new_method_label'] ), $_POST['ec_admin_new_method_trigger_rate'], $_POST['ec_admin_new_method_trigger_zone_id'], $free_shipping_at, $_POST['ec_admin_new_method_trigger_shipping_order'] ) );
		return $this->wpdb->insert_id;
	}
	
	public function update_shipping_method_triggers( ){
		$shipping_rates = $this->wpdb->get_results( "SELECT * FROM ec_shippingrate WHERE is_method_based = 1" );
		foreach( $shipping_rates as $trigger ){
			$free_shipping_at = '-1.000';
			if( $_POST['ec_admin_method_trigger_free_shipping_at_'.$trigger->shippingrate_id] != '' ){
				$free_shipping_at = $_POST['ec_admin_method_trigger_free_shipping_at_'.$trigger->shippingrate_id];
			}
			$this->wpdb->query( $this->wpdb->prepare( "UPDATE ec_shippingrate SET shipping_label = %s, shipping_rate = %s, zone_id = %d, free_shipping_at = %s, shipping_order = %d WHERE shippingrate_id = %d", stripslashes_deep( $_POST['ec_admin_method_label_'.$trigger->shippingrate_id] ), $_POST['ec_admin_method_trigger_rate_'.$trigger->shippingrate_id], $_POST['ec_admin_method_trigger_zone_id_'.$trigger->shippingrate_id], $free_shipping_at, $_POST['ec_admin_method_trigger_shipping_order_'.$trigger->shippingrate_id], $trigger->shippingrate_id ) );
		}
	}
	
	/* Fraktjakt Settings */
	public function update_fraktjakt_settings( ){
		$this->wpdb->query( $this->wpdb->prepare( "UPDATE ec_setting SET fraktjakt_customer_id = %s, fraktjakt_login_key = %s, fraktjakt_conversion_rate = %s, fraktjakt_test_mode = %s, fraktjakt_address = %s, fraktjakt_city = %s, fraktjakt_state = %s, fraktjakt_zip =  %s, fraktjakt_country = %s", $_POST['fraktjakt_customer_id'], $_POST['fraktjakt_login_key'], $_POST['fraktjakt_conversion_rate'], $_POST['fraktjakt_test_mode'], $_POST['fraktjakt_address'], $_POST['fraktjakt_city'], $_POST['fraktjakt_state'], $_POST['fraktjakt_zip'], $_POST['fraktjakt_country'] ) );
	}
	
	/* Country List */
	public function update_country_list( ){
		$id_cnt = (int) $_POST['id'];
        $ship_to_active = 0;
        if( isset( $_POST['ship_to_active'] ) && $_POST['ship_to_active'] == '1' ){
            $ship_to_active = 1;
        }
        $this->wpdb->query( $this->wpdb->prepare( "UPDATE ec_country SET ship_to_active = %d WHERE id_cnt = %d", $ship_to_active, $id_cnt ) );
        $this->wpdb->query( $this->wpdb->prepare( "UPDATE ec_state SET ship_to_active = %d WHERE idcnt_sta = %d", $ship_to_active, $id_cnt ) );
	}
	
	/* State List */
	public function update_state_list( ){
		$id_sta = (int) $_POST['id'];
        $ship_to_active = 0;
        if( isset( $_POST['ship_to_active'] ) && $_POST['ship_to_active'] == '1' ){
            $ship_to_active = 1;
        }
        $this->wpdb->query( $this->wpdb->prepare( "UPDATE ec_state SET ship_to_active = %d WHERE id_sta = %d", $ship_to_active, $id_sta ) );
    }
	
	/* Shipping Zones */
	public function add_zone( ){
		$this->wpdb->query( $this->wpdb->prepare( "INSERT INTO ec_zone( zone_name ) VALUES( %s )", $_POST['zone_name'] ) );
        return $this->wpdb->insert_id;
	}
	
	public function edit_zone( ){
		$this->wpdb->query( $this->wpdb->prepare( "UPDATE ec_zone SET zone_name = %s WHERE zone_id = %d", $_POST['zone_name'], $_POST['id'] ) );
	}
	
	public function delete_zone( $zone_id ){
		$this->wpdb->query( $this->wpdb->prepare( "DELETE FROM ec_zone WHERE zone_id = %d", $zone_id ) );
		$this->wpdb->query( $this->wpdb->prepare( "DELETE FROM ec_zone_to_location WHERE zone_id = %d", $zone_id ) );
	}
	
	public function add_zone_item( ){
        $state_code = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT ec_state.code_sta FROM ec_state WHERE id_sta = %d", $_POST['id_sta'] ) );
		$this->wpdb->query( $this->wpdb->prepare( "INSERT INTO ec_zone_to_location( zone_id, iso2_cnt, code_sta ) VALUES( %s, %s, %s )", $_POST['zone_id'], $_POST['iso2_cnt'], $state_code ) );
        return $this->wpdb->insert_id;
	}
	
	public function update_zone_item( ){
        $state_code = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT ec_state.code_sta FROM ec_state WHERE id_sta = %d", $_POST['id_sta'] ) );
		$this->wpdb->query( $this->wpdb->prepare( "UPDATE ec_zone_to_location SET zone_id = %d, iso2_cnt = %s, code_sta = %s WHERE zone_to_location_id = %d", $_POST['zone_id'], $_POST['iso2_cnt'], $state_code, $_POST['id'] ) );
	}
	
	public function delete_zone_item( $zone_item_id ){
		$this->wpdb->query( $this->wpdb->prepare( "DELETE FROM ec_zone_to_location WHERE zone_to_location_id = %d", $zone_item_id ) );
	}
    
    public function save_shipping_settings( ){
		global $wpdb;
        
        $options = array( 'ec_option_use_shipping', 'ec_option_hide_shipping_rate_page1', 'ec_option_add_local_pickup', 'ec_option_collect_tax_on_shipping', 'ec_option_show_delivery_days_live_shipping', 'ec_option_show_delivery_days_live_shipping', 'ec_option_collect_shipping_for_subscriptions', 'ec_option_ship_items_seperately', 'ec_option_static_ship_items_seperately', 'ec_option_fedex_use_net_charge', 'ec_option_live_override_always' );
		
		$options_text = array( 'ec_option_enable_metric_unit_display' );
		
		if( isset( $_POST['update_var'] ) && in_array( $_POST['update_var'], $options ) ){
			$val = 0;
			if( isset( $_POST['val'] ) && $_POST['val'] == '1' )
				$val = 1;

			update_option( $_POST['update_var'], $val );
		
		}else if( isset( $_POST['update_var'] ) && in_array( $_POST['update_var'], $options_text ) ){
			update_option( $_POST['update_var'], stripslashes_deep( $_POST['val'] ) );
		
		}else if( isset( $_POST['update_var'] ) && $_POST['update_var'] == 'shipping_handling_rate' ){
			$wpdb->query( $wpdb->prepare( "UPDATE ec_setting SET shipping_handling_rate = %s", stripslashes_deep( $_POST['val'] ) ) );
			
		}else if( isset( $_POST['update_var'] ) && $_POST['update_var'] == 'shipping_expedite_rate' ){
			$wpdb->query( $wpdb->prepare( "UPDATE ec_setting SET shipping_expedite_rate = %s", stripslashes_deep( $_POST['val'] ) ) );
			
		}
	}
	
}

/******************/
/* Shipping Hooks */
/******************/
add_action( 'wp_ajax_ec_admin_ajax_update_shipping_select', 'ec_admin_ajax_update_shipping_select' );
function ec_admin_ajax_update_shipping_select( ){
	$shipping = new wp_easycart_admin_shipping( );
	$shipping->update_shipping_method( );
	die( );
}

/* Shipping Hooks - Price Triggers */
add_action( 'wp_ajax_ec_admin_ajax_add_price_trigger', 'ec_admin_ajax_add_price_trigger' );
function ec_admin_ajax_add_price_trigger( ){
	$shipping = new wp_easycart_admin_shipping( );
	global $wpdb;
	$currency = new ec_currency( );
	$shippingrate_id = $shipping->add_shipping_price_trigger( );
	$trigger = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_shippingrate WHERE shippingrate_id = %d", $shippingrate_id ) );
	$shipping_zones = $wpdb->get_results( "SELECT * FROM ec_zone ORDER BY zone_name ASC" );
	echo '<div class="ec_admin_tax_row ec_admin_shipping_price_trigger_row" id="ec_admin_price_trigger_row_' . $trigger->shippingrate_id . '">
    	<div class="ec_admin_shipping_trigger"><span>' . __( 'Price Trigger', 'wp-easycart' ) . ': ' . $currency->symbol . '</span><input type="number" class="ec_admin_price_trigger_input" step=".01" value="' . $currency->get_number_safe( $trigger->trigger_rate ) . '" name="ec_admin_price_trigger_' . $trigger->shippingrate_id . '" id="ec_admin_new_price_trigger_' . $trigger->shippingrate_id . '" /></div>
        <div class="ec_admin_shipping_rate"><span>' . __( 'Shipping Rate', 'wp-easycart' ) . ': ' . $currency->symbol . '</span><input type="number" class="ec_admin_price_trigger_rate_input" step=".01" value="' . $currency->get_number_safe( $trigger->shipping_rate ) . '" name="ec_admin_price_trigger_rate_' . $trigger->shippingrate_id . '" id="ec_admin_new_price_trigger_rate_' . $trigger->shippingrate_id . '" /></div>
    	<div class="ec_admin_shipping_rate"><span>' . __( 'Shipping Zone', 'wp-easycart' ) . ': </span><select class="ec_admin_price_trigger_zone_id_input" name="ec_admin_price_trigger_zone_id_' . $trigger->shippingrate_id . '" id="ec_admin_price_trigger_zone_id_' . $trigger->shippingrate_id . '">
        	<option value="0">' . __( 'No Zone', 'wp-easycart' ) . '</option>';
            foreach( $shipping_zones as $zone ){
            echo '<option value="' . $zone->zone_id . '"';
			if( $zone->zone_id == $trigger->zone_id ){
				echo ' selected="selected"';
			}
			echo '>' . $zone->zone_name . '</option>';
            }
        echo '</select></div>
		<span class="ec_admin_shipping_rate_delete"><div class="dashicons-before dashicons-trash" onclick="ec_admin_delete_price_trigger( \'' . $trigger->shippingrate_id . '\' );"></div></span>
    </div>';
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_delete_price_trigger', 'ec_admin_ajax_delete_price_trigger' );
function ec_admin_ajax_delete_price_trigger( ){
	$shipping = new wp_easycart_admin_shipping( );
	global $wpdb;
	$shipping->delete_shipping_rate( $_POST['shippingrate_id'] );
	$rows = $wpdb->get_results( "SELECT * FROM ec_shippingrate WHERE is_price_based = 1" );
	echo count( $rows );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_update_shipping_price_triggers', 'ec_admin_ajax_update_shipping_price_triggers' );
function ec_admin_ajax_update_shipping_price_triggers( ){
	$shipping = new wp_easycart_admin_shipping( );
	$shipping->update_shipping_price_triggers( );
	die( );
}

/* Shipping Hooks - Weight Triggers */
add_action( 'wp_ajax_ec_admin_ajax_add_weight_trigger', 'ec_admin_ajax_add_weight_trigger' );
function ec_admin_ajax_add_weight_trigger( ){
	$shipping = new wp_easycart_admin_shipping( );
	global $wpdb;
	$currency = new ec_currency( );
	$shippingrate_id = $shipping->add_shipping_weight_trigger( );
	$trigger = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_shippingrate WHERE shippingrate_id = %d", $shippingrate_id ) );
	$shipping_zones = $wpdb->get_results( "SELECT * FROM ec_zone ORDER BY zone_name ASC" );
	echo '<div class="ec_admin_tax_row ec_admin_shipping_weight_trigger_row" id="ec_admin_weight_trigger_row_' . $trigger->shippingrate_id . '">
    	<div class="ec_admin_shipping_trigger"><span>' . __( 'Weight Trigger', 'wp-easycart' ) . ': </span><input type="number" class="ec_admin_weight_trigger_input" step=".01" value="' . $trigger->trigger_rate . '" name="ec_admin_weight_trigger_' . $trigger->shippingrate_id . '" id="ec_admin_new_weight_trigger_' . $trigger->shippingrate_id . '" /></div>
        <div class="ec_admin_shipping_rate"><span>' . __( 'Shipping Rate', 'wp-easycart' ) . ': ' . $currency->symbol . '</span><input type="number" class="ec_admin_weight_trigger_rate_input" step=".01" value="' . $currency->get_number_safe( $trigger->shipping_rate ) . '" name="ec_admin_weight_trigger_rate_' . $trigger->shippingrate_id . '" id="ec_admin_new_weight_trigger_rate_' . $trigger->shippingrate_id . '" /></div>
    	<div class="ec_admin_shipping_rate"><span>' . __( 'Shipping Zone', 'wp-easycart' ) . ': </span><select class="ec_admin_weight_trigger_zone_id_input" name="ec_admin_weight_trigger_zone_id_' . $trigger->shippingrate_id . '" id="ec_admin_weight_trigger_zone_id_' . $trigger->shippingrate_id . '">
        	<option value="0">' . __( 'No Zone', 'wp-easycart' ) . '</option>';
            foreach( $shipping_zones as $zone ){
            echo '<option value="' . $zone->zone_id . '"';
			if( $zone->zone_id == $trigger->zone_id ){
				echo ' selected="selected"';
			}
			echo '>' . $zone->zone_name . '</option>';
            }
        echo '</select></div>
		<span class="ec_admin_shipping_rate_delete"><div class="dashicons-before dashicons-trash" onclick="ec_admin_delete_weight_trigger( \'' . $trigger->shippingrate_id . '\' );"></div></span>
    </div>';
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_delete_weight_trigger', 'ec_admin_ajax_delete_weight_trigger' );
function ec_admin_ajax_delete_weight_trigger( ){
	$shipping = new wp_easycart_admin_shipping( );
	global $wpdb;
	$shipping->delete_shipping_rate( $_POST['shippingrate_id'] );
	$rows = $wpdb->get_results( "SELECT * FROM ec_shippingrate WHERE is_weight_based = 1" );
	echo count( $rows );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_update_shipping_weight_triggers', 'ec_admin_ajax_update_shipping_weight_triggers' );
function ec_admin_ajax_update_shipping_weight_triggers( ){
	$shipping = new wp_easycart_admin_shipping( );
	$shipping->update_shipping_weight_triggers( );
	die( );
}

/* Shipping Hooks - Quantity Triggers */
add_action( 'wp_ajax_ec_admin_ajax_add_quantity_trigger', 'ec_admin_ajax_add_quantity_trigger' );
function ec_admin_ajax_add_quantity_trigger( ){
	$shipping = new wp_easycart_admin_shipping( );
	global $wpdb;
	$currency = new ec_currency( );
	$shippingrate_id = $shipping->add_shipping_quantity_trigger( );
	$trigger = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_shippingrate WHERE shippingrate_id = %d", $shippingrate_id ) );
	$shipping_zones = $wpdb->get_results( "SELECT * FROM ec_zone ORDER BY zone_name ASC" );
	echo '<div class="ec_admin_tax_row ec_admin_shipping_quantity_trigger_row" id="ec_admin_quantity_trigger_row_' . $trigger->shippingrate_id . '">
    	<div class="ec_admin_shipping_trigger"><span>' . __( 'Quantity Trigger', 'wp-easycart' ) . ': </span><input type="number" class="ec_admin_quantity_trigger_input" step="1" value="' . number_format( $trigger->trigger_rate, 0, '', '' ) . '" name="ec_admin_quantity_trigger_' . $trigger->shippingrate_id . '" id="ec_admin_new_quantity_trigger_' . $trigger->shippingrate_id . '" /></div>
        <div class="ec_admin_shipping_rate"><span>' . __( 'Shipping Rate', 'wp-easycart' ) . ': ' . $currency->symbol . '</span><input type="number" class="ec_admin_quantity_trigger_rate_input" step=".01" value="' . $currency->get_number_safe( $trigger->shipping_rate ) . '" name="ec_admin_quantity_trigger_rate_' . $trigger->shippingrate_id . '" id="ec_admin_new_quantity_trigger_rate_' . $trigger->shippingrate_id . '" /></div>
    	<div class="ec_admin_shipping_rate"><span>' . __( 'Shipping Zone', 'wp-easycart' ) . ': </span><select class="ec_admin_quantity_trigger_zone_id_input" name="ec_admin_quantity_trigger_zone_id_' . $trigger->shippingrate_id . '" id="ec_admin_quantity_trigger_zone_id_' . $trigger->shippingrate_id . '">
        	<option value="0">' . __( 'No Zone', 'wp-easycart' ) . '</option>';
            foreach( $shipping_zones as $zone ){
            echo '<option value="' . $zone->zone_id . '"';
			if( $zone->zone_id == $trigger->zone_id ){
				echo ' selected="selected"';
			}
			echo '>' . $zone->zone_name . '</option>';
            }
        echo '</select></div>
		<span class="ec_admin_shipping_rate_delete"><div class="dashicons-before dashicons-trash" onclick="ec_admin_delete_quantity_trigger( \'' . $trigger->shippingrate_id . '\' );"></div></span>
    </div>';
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_delete_quantity_trigger', 'ec_admin_ajax_delete_quantity_trigger' );
function ec_admin_ajax_delete_quantity_trigger( ){
	$shipping = new wp_easycart_admin_shipping( );
	global $wpdb;
	$shipping->delete_shipping_rate( $_POST['shippingrate_id'] );
	$rows = $wpdb->get_results( "SELECT * FROM ec_shippingrate WHERE is_quantity_based = 1" );
	echo count( $rows );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_update_shipping_quantity_triggers', 'ec_admin_ajax_update_shipping_quantity_triggers' );
function ec_admin_ajax_update_shipping_quantity_triggers( ){
	$shipping = new wp_easycart_admin_shipping( );
	$shipping->update_shipping_quantity_triggers( );
	die( );
}

/* Shipping Hooks - Percentage Triggers */
add_action( 'wp_ajax_ec_admin_ajax_add_percentage_trigger', 'ec_admin_ajax_add_percentage_trigger' );
function ec_admin_ajax_add_percentage_trigger( ){
	$shipping = new wp_easycart_admin_shipping( );
	global $wpdb;
	$currency = new ec_currency( );
	$shippingrate_id = $shipping->add_shipping_percentage_trigger( );
	$trigger = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_shippingrate WHERE shippingrate_id = %d", $shippingrate_id ) );
	$shipping_zones = $wpdb->get_results( "SELECT * FROM ec_zone ORDER BY zone_name ASC" );
	echo '<div class="ec_admin_tax_row ec_admin_shipping_percentage_trigger_row" id="ec_admin_percentage_trigger_row_' . $trigger->shippingrate_id . '">
    	<div class="ec_admin_shipping_trigger"><span>' . __( 'Price Trigger', 'wp-easycart' ) . ': ' . $currency->symbol . '</span><input type="number" class="ec_admin_percentage_trigger_input" step=".01" value="' . $currency->get_number_safe( $trigger->trigger_rate ) . '" name="ec_admin_percentage_trigger_' . $trigger->shippingrate_id . '" id="ec_admin_new_percentage_trigger_' . $trigger->shippingrate_id . '" /></div>
        <div class="ec_admin_shipping_rate"><span>' . __( 'Shipping Rate', 'wp-easycart' ) . ': </span><input type="number" class="ec_admin_percentage_trigger_rate_input" step=".01" value="' . $currency->get_number_safe( $trigger->shipping_rate ) . '" name="ec_admin_percentage_trigger_rate_' . $trigger->shippingrate_id . '" id="ec_admin_new_percentage_trigger_rate_' . $trigger->shippingrate_id . '" />%</div>
    	<div class="ec_admin_shipping_rate"><span>' . __( 'Shipping Zone', 'wp-easycart' ) . ': </span><select class="ec_admin_percentage_trigger_zone_id_input" name="ec_admin_percentage_trigger_zone_id_' . $trigger->shippingrate_id . '" id="ec_admin_percentage_trigger_zone_id_' . $trigger->shippingrate_id . '">
        	<option value="0">' . __( 'No Zone', 'wp-easycart' ) . '</option>';
            foreach( $shipping_zones as $zone ){
            echo '<option value="' . $zone->zone_id . '"';
			if( $zone->zone_id == $trigger->zone_id ){
				echo ' selected="selected"';
			}
			echo '>' . $zone->zone_name . '</option>';
            }
        echo '</select></div>
		<span class="ec_admin_shipping_rate_delete"><div class="dashicons-before dashicons-trash" onclick="ec_admin_delete_percentage_trigger( \'' . $trigger->shippingrate_id . '\' );"></div></span>
    </div>';
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_delete_percentage_trigger', 'ec_admin_ajax_delete_percentage_trigger' );
function ec_admin_ajax_delete_percentage_trigger( ){
	$shipping = new wp_easycart_admin_shipping( );
	global $wpdb;
	$shipping->delete_shipping_rate( $_POST['shippingrate_id'] );
	$rows = $wpdb->get_results( "SELECT * FROM ec_shippingrate WHERE is_percentage_based = 1" );
	echo count( $rows );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_update_shipping_percentage_triggers', 'ec_admin_ajax_update_shipping_percentage_triggers' );
function ec_admin_ajax_update_shipping_percentage_triggers( ){
	$shipping = new wp_easycart_admin_shipping( );
	$shipping->update_shipping_percentage_triggers( );
	die( );
}

/* Shipping Hooks - Static Method */
add_action( 'wp_ajax_ec_admin_ajax_add_method_trigger', 'ec_admin_ajax_add_method_trigger' );
function ec_admin_ajax_add_method_trigger( ){
	$shipping = new wp_easycart_admin_shipping( );
	global $wpdb;
	$currency = new ec_currency( );
	$shippingrate_id = $shipping->add_shipping_static_method( );
	$trigger = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_shippingrate WHERE shippingrate_id = %d", $shippingrate_id ) );
	$shipping_zones = $wpdb->get_results( "SELECT * FROM ec_zone ORDER BY zone_name ASC" );
	echo '<div class="ec_admin_tax_row ec_admin_static_shipping_row" id="ec_admin_method_trigger_row_' . $trigger->shippingrate_id . '">
    		<div class="ec_admin_shipping_trigger"><span>' . __( 'Shipping Label', 'wp-easycart' ) . ':</span><input type="text" class="ec_admin_method_label_input" value="' . $trigger->shipping_label . '" name="ec_admin_method_label_' . $trigger->shippingrate_id . '" id="ec_admin_method_label_' . $trigger->shippingrate_id . '" /></div>
    		<div class="ec_admin_shipping_rate"><span>' . __( 'Shipping Rate', 'wp-easycart' ) . ': ' . $currency->symbol . '</span><input type="number" class="ec_admin_method_trigger_rate_input" step=".01" value="' . $currency->get_number_safe( $trigger->shipping_rate ) . '" name="ec_admin_method_trigger_rate_' . $trigger->shippingrate_id . '" id="ec_admin_new_method_trigger_rate_' . $trigger->shippingrate_id . '" /></div>
    		<div class="ec_admin_shipping_rate"><span>' . __( 'Shipping Zone', 'wp-easycart' ) . ': </span><select class="ec_admin_method_trigger_zone_id_input" name="ec_admin_method_trigger_zone_id_' . $trigger->shippingrate_id . '" id="ec_admin_method_trigger_zone_id_' . $trigger->shippingrate_id . '">
        	<option value="0">' . __( 'No Zone', 'wp-easycart' ) . '</option>';
            foreach( $shipping_zones as $zone ){
            echo '<option value="' . $zone->zone_id . '"';
			if( $zone->zone_id == $trigger->zone_id ){
				echo ' selected="selected"';
			}
			echo '>' . $zone->zone_name . '</option>';
            }
        echo '</select></div>
			<div class="ec_admin_shipping_rate"><span>' . __( 'Free Shipping @', 'wp-easycart' ) . ':</span><input type="number" step=".01" class="ec_admin_method_trigger_free_shipping_at_input" value="';
			if( $trigger->free_shipping_at != -1 ){ 
				echo $currency->get_number_safe( $trigger->free_shipping_at );
			}
			echo '" name="ec_admin_method_trigger_free_shipping_at_' . $trigger->shippingrate_id . '" id="ec_admin_method_trigger_free_shipping_at_' . $trigger->shippingrate_id . '" /></div>
        	<div class="ec_admin_shipping_rate"><span>' . __( 'Rate Order', 'wp-easycart' ) . ':</span><input type="number" step="1" class="ec_admin_method_trigger_shipping_order_input" value="' . $trigger->shipping_order . '" name="ec_admin_method_trigger_shipping_order_' . $trigger->shippingrate_id . '" id="ec_admin_method_trigger_shipping_order_' . $trigger->shippingrate_id . '" /></div>
			<div><span class="ec_admin_shipping_rate_delete"><div class="dashicons-before dashicons-trash" onclick="ec_admin_delete_method_trigger( \'' . $trigger->shippingrate_id . '\' );"></div></span></div>
    	  </div>';
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_delete_method_trigger', 'ec_admin_ajax_delete_method_trigger' );
function ec_admin_ajax_delete_method_trigger( ){
	$shipping = new wp_easycart_admin_shipping( );
	global $wpdb;
	$shipping->delete_shipping_rate( $_POST['shippingrate_id'] );
	$rows = $wpdb->get_results( "SELECT * FROM ec_shippingrate WHERE is_method_based = 1" );
	echo count( $rows );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_update_shipping_method_triggers', 'ec_admin_ajax_update_shipping_method_triggers' );
function ec_admin_ajax_update_shipping_method_triggers( ){
	$shipping = new wp_easycart_admin_shipping( );
	$shipping->update_shipping_method_triggers( );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_fraktjakt_settings', 'ec_admin_ajax_save_fraktjakt_settings' );
function ec_admin_ajax_save_fraktjakt_settings( ){
	$shipping = new wp_easycart_admin_shipping( );
	$shipping->update_fraktjakt_settings( );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_country_list', 'ec_admin_ajax_save_country_list' );
function ec_admin_ajax_save_country_list( ){
	$shipping = new wp_easycart_admin_shipping( );
	$shipping->update_country_list( );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_state_list', 'ec_admin_ajax_save_state_list' );
function ec_admin_ajax_save_state_list( ){
	$shipping = new wp_easycart_admin_shipping( );
	$shipping->update_state_list( );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_add_shipping_zone', 'ec_admin_ajax_add_shipping_zone' );
function ec_admin_ajax_add_shipping_zone( ){
	$shipping = new wp_easycart_admin_shipping( );
	$zone_id = $shipping->add_zone( );
	echo $zone_id;
    die( );
}

add_action( 'wp_ajax_ec_admin_ajax_edit_shipping_zone', 'ec_admin_ajax_edit_shipping_zone' );
function ec_admin_ajax_edit_shipping_zone( ){
	$shipping = new wp_easycart_admin_shipping( );
	$shipping->edit_zone( );
    echo (int) $_POST['id'];
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_delete_shipping_zone', 'ec_admin_ajax_delete_shipping_zone' );
function ec_admin_ajax_delete_shipping_zone( ){
	$shipping = new wp_easycart_admin_shipping( );
    if( is_array( $_POST['id'] ) ){
        foreach( $_POST['id'] as $zone_id ){
            $shipping->delete_zone( (int) $zone_id );
        }
    }else{
        $shipping->delete_zone( $_POST['id'] );
    }
    die( );
}

add_action( 'wp_ajax_ec_admin_ajax_add_shipping_zone_item', 'ec_admin_ajax_add_shipping_zone_item' );
function ec_admin_ajax_add_shipping_zone_item( ){
	$shipping = new wp_easycart_admin_shipping( );
	$id = $shipping->add_zone_item( );
	echo $id;
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_update_shipping_zone_item', 'ec_admin_ajax_update_shipping_zone_item' );
function ec_admin_ajax_update_shipping_zone_item( ){
	$shipping = new wp_easycart_admin_shipping( );
	$shipping->update_zone_item( );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_delete_shipping_zone_item', 'ec_admin_ajax_delete_shipping_zone_item' );
function ec_admin_ajax_delete_shipping_zone_item( ){
	$shipping = new wp_easycart_admin_shipping( );
    if( is_array( $_POST['id'] ) ){
        foreach( $_POST['id'] as $zoneitem_id ){
            $shipping->delete_zone_item( (int) $zoneitem_id );
        }
        
    }else{
        $shipping->delete_zone_item( $_POST['id'] );
        
    }
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_shipping_settings', 'ec_admin_ajax_save_shipping_settings' );
function ec_admin_ajax_save_shipping_settings( ){
	$shipping = new wp_easycart_admin_shipping( );
	$shipping->save_shipping_settings( );
	die( );
}