<?php
class wp_easycart_admin_checkout{
	
	private $wpdb;
	
	public $checkout_file;
	public $cart_settings_file;
	public $checkout_order_statuses_file;
	public $checkout_form_settings_file;
	public $checkout_settings_file;
	public $checkout_email_settings_file;
	public $checkout_abandoned_cart_file;
		
	public function __construct( ){
		global $wpdb;
		$this->wpdb =& $wpdb;
		
		// Setup File Names 
		$this->checkout_file 				= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/checkout/checkout.php';
		$this->cart_settings_file 			= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/checkout/cart-settings.php';
		$this->checkout_order_statuses_file = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/checkout/order-statuses.php';
		$this->checkout_form_settings_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/checkout/checkout-form-settings.php';
		$this->checkout_settings_file 		= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/checkout/checkout-settings.php';
		$this->checkout_stock_control_file = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/checkout/checkout-stock-control.php';
		
		// Actions
		add_action( 'wpeasycart_admin_checkout_success', array( $this, 'load_success_messages' ) );
		add_action( 'wpeasycart_admin_checkout_settings', array( $this, 'load_cart_settings' ) );
		add_action( 'wpeasycart_admin_checkout_settings', array( $this, 'load_order_status_settings' ) );
		add_action( 'wpeasycart_admin_checkout_settings', array( $this, 'load_checkout_form_settings' ) );
		add_action( 'wpeasycart_admin_checkout_settings', array( $this, 'load_stock_control_settings' ) );
		add_action( 'wpeasycart_admin_checkout_settings', array( $this, 'load_checkout_settings' ) );
	}
	
	public function load_checkout( ){
		include( $this->checkout_file );
	}
	
	public function load_success_messages( ){
		include( $this->success_messages_file );
	}
    
    public function load_order_status_settings( ){
        include( $this->checkout_order_statuses_file );
    }
	
	public function load_cart_settings( ){
		include( $this->cart_settings_file );
	}
	
	public function load_checkout_form_settings( ){
		include( $this->checkout_form_settings_file );
	}
	
	public function load_checkout_settings( ){
		include( $this->checkout_settings_file );
	}
	
	public function load_stock_control_settings( ){
		include( $this->checkout_stock_control_file );
	}
	
	public function save_cart_settings( ){
		$options = array( 'ec_option_load_ssl', 'ec_option_cache_prevent', 'ec_option_enable_tips', 'ec_option_use_estimate_shipping', 'ec_option_estimate_shipping_zip', 'ec_option_estimate_shipping_country', 'ec_option_show_giftcards', 'ec_option_gift_card_shipping_allowed', 'ec_option_show_coupons', 'ec_option_enable_metric_unit_display', 'ec_option_display_country_top', 'ec_option_use_address2', 'ec_option_collect_user_phone', 'ec_option_user_phone_required', 'ec_option_enable_company_name', 'ec_option_collect_vat_registration_number', 'ec_option_user_order_notes', 'ec_option_require_terms_agreement', 'ec_option_use_contact_name', 'ec_option_show_card_holder_name', 'ec_option_skip_shipping_page', 'ec_option_allow_guest', 'ec_option_use_state_dropdown', 'ec_option_use_smart_states', 'ec_option_use_country_dropdown', 'ec_option_send_low_stock_emails', 'ec_option_send_out_of_stock_emails', 'ec_option_show_card_holder_name', 'ec_option_addtocart_return_to_product' );
		
		$options_text = array( 'ec_option_return_to_store_page_url', 'ec_option_enable_metric_unit_display', 'ec_option_minimum_order_total', 'ec_option_terms_link', 'ec_option_privacy_link', 'ec_option_default_country', 'ec_option_low_stock_trigger_total', 'ec_option_default_payment_type' );
		
		if( isset( $_POST['update_var'] ) && in_array( $_POST['update_var'], $options ) ){
			$val = 0;
			if( isset( $_POST['val'] ) && $_POST['val'] == '1' )
				$val = 1;

			update_option( $_POST['update_var'], $val );
		
		}else if( isset( $_POST['update_var'] ) && in_array( $_POST['update_var'], $options_text ) ){
			update_option( $_POST['update_var'], stripslashes_deep( $_POST['val'] ) );
			
		}else if( isset( $_POST['update_var'] ) && $_POST['update_var'] == 'ec_option_default_tips' ){
			$ec_option_default_tips = preg_replace( '/[^0-9\.\,]/', '', $_POST['val'] );
			$tips_arr_final = array( );
			$tips_arr_exploded = explode( ',', $ec_option_default_tips );
			foreach( $tips_arr_exploded as $tip_rate ){
				if( (float) $tip_rate > 0 ){
					$tips_arr_final[] = $tip_rate;
				}
			}
			$ec_option_default_tips = implode( ',', $tips_arr_final );
			update_option( 'ec_option_default_tips', $ec_option_default_tips );
		
		}else if( isset( $_POST['update_var'] ) && $_POST['update_var'] == 'ec_option_current_order_id' ){
			$this->wpdb->query( $this->wpdb->prepare( "ALTER TABLE ec_order AUTO_INCREMENT = %d", (int) $_POST['val'] ) );
		
		}
	}
	
    public function add_order_status( $order_status, $is_approved ){
        global $wpdb;
        $wpdb->query( $wpdb->prepare( "INSERT INTO ec_orderstatus( order_status, is_approved ) VALUES( %s, %d )", $order_status, $is_approved ) );
        return $wpdb->insert_id;
    }
	
    public function update_order_status( $status_id, $order_status ){
        global $wpdb;
        $wpdb->query( $wpdb->prepare( "UPDATE ec_orderstatus SET order_status = %s WHERE status_id = %d", $order_status, $status_id ) );
    }
	
    public function update_order_status_approved( $status_id, $is_approved ){
        global $wpdb;
        $wpdb->query( $wpdb->prepare( "UPDATE ec_orderstatus SET is_approved = %d WHERE status_id = %d", $is_approved, $status_id ) );
    }
	
    public function archieve_order_status( $status_id ){
        global $wpdb;
        $wpdb->query( $wpdb->prepare( "UPDATE ec_orderstatus SET is_archieved = 1 WHERE status_id = %d", $status_id ) );
    }
	
}

add_action( 'wp_ajax_ec_admin_ajax_save_cart_settings', 'ec_admin_ajax_save_cart_settings' );
function ec_admin_ajax_save_cart_settings( ){
	$checkout = new wp_easycart_admin_checkout( );
	$checkout->save_cart_settings( );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_add_orderstatus', 'ec_admin_ajax_add_orderstatus' );
function ec_admin_ajax_add_orderstatus( ){
	$checkout = new wp_easycart_admin_checkout( );
	$insert_id = $checkout->add_order_status( $_POST['order_status'], $_POST['is_approved'] );
    echo $insert_id;
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_orderstatus', 'ec_admin_ajax_save_orderstatus' );
function ec_admin_ajax_save_orderstatus( ){
	$checkout = new wp_easycart_admin_checkout( );
	$checkout->update_order_status( $_POST['status_id'], $_POST['order_status'] );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_orderstatus_approved', 'ec_admin_ajax_save_orderstatus_approved' );
function ec_admin_ajax_save_orderstatus_approved( ){
	$checkout = new wp_easycart_admin_checkout( );
	$checkout->update_order_status_approved( $_POST['status_id'], $_POST['is_approved'] );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_archieve_orderstatus', 'ec_admin_ajax_archieve_orderstatus' );
function ec_admin_ajax_archieve_orderstatus( ){
	$checkout = new wp_easycart_admin_checkout( );
	$checkout->archieve_order_status( $_POST['status_id'] );
	die( );
}