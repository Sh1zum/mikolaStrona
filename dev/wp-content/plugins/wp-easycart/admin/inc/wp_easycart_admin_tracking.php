<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_tracking' ) ) :

final class wp_easycart_admin_tracking{
	
	protected static $_instance = null;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
		
	public function __construct( ){
		add_action( 'wpeasycart_admin_usage_tracking_accepted', array( $this, 'access_granted' ) );
		add_action( 'wpeasycart_activated', array( $this, 'plugin_activated' ) );
		add_action( 'wpeasycart_deactivated', array( $this, 'plugin_custom_deactivate' ) );
		add_action( 'deactivated_plugin', array( $this, 'plugin_deactivated' ), 10, 2 );
		add_action( 'wpeasycart_manual_billing_updated', array( $this, 'manual_payment_updated' ), 10, 1 );
		add_action( 'wpeasycart_third_party_payment_updated', array( $this, 'third_party_updated' ), 10, 1 );
		add_action( 'wpeasycart_live_gateway_updated', array( $this, 'live_gateway_updated' ), 10, 1 );
		add_action( 'wpeasycart_admin_product_inserted', array( $this, 'product_inserted' ), 10, 1 );
		add_action( 'wpeasycart_admin_demo_data_installed', array( $this, 'demo_data_installed' ) );
		add_action( 'wpeasycart_pro_activated', array( $this, 'plugin_pro_activated' ) );
	}
	
	public function access_granted( ){
		global $wpdb;
		$settings_row = $wpdb->get_row( "SELECT * FROM ec_setting" );
		$product_count = $wpdb->get_var( "SELECT COUNT( ec_product.product_id ) FROM ec_product" );
		$using_vat = $using_duty = $using_tax_cloud = $using_ca_tax = $using_state_tax = $using_country_tax = $using_global_tax = $using_auspost = $using_capost = $using_dhl = $using_fedex = $using_ups = $using_usps = 0;
		$tax_counts = $wpdb->get_row( "SELECT SUM( tax_by_state ) AS state_count, SUM( tax_by_country ) AS country_count, SUM( tax_by_duty ) AS duty_count, SUM( tax_by_vat ) AS vat_count, SUM( tax_by_single_vat ) AS vat_single_count, SUM( tax_by_all ) AS global_count FROM ec_taxrate" );
		if( $tax_counts->vat_count > 0 || $tax_counts->vat_single_count )
			$using_vat = 1;
		if( $tax_counts->duty_count > 0 )
			$using_duty = 1;
		if( $tax_counts->state_count > 0 )
			$using_state_tax = 1;
		if( $tax_counts->country_count > 0 )
			$using_country_tax = 1;
		if( $tax_counts->global_count > 0 )
			$using_global_tax = 1;
		if( get_option( 'ec_option_tax_cloud_api_id' ) != '' )
			$using_tax_cloud = 1;
		if( get_option( 'ec_option_enable_easy_canada_tax' ) )
			$using_ca_tax = 1;
		
		if( $settings_row->auspost_api_key != '' )
			$using_auspost = 1;
		if( $settings_row->canadapost_username != '' )
			$using_capost = 1;
		if( $settings_row->dhl_password != '' )
			$using_dhl = 1;
		if( $settings_row->fedex_key != '' )
			$using_fedex = 1;
		if( $settings_row->ups_password != '' )
			$using_ups = 1;
		if( $settings_row->usps_user_name != '' )
			$using_usps = 1;
				
			
		$post_linking_type = 'Basic';
		if( !get_option( 'ec_option_use_old_linking_style' ) )
			$post_linking_type = 'Permalinks';
		$init_data = array(
			'third_party' 		=> get_option( 'ec_option_payment_third_party' ),
			'live_gateway'		=> get_option( 'ec_option_payment_process_method' ),
			'shipping_type'		=> $settings_row->shipping_method,
			'product_count'		=> $product_count,
			'using_vat'			=> $using_vat,
			'using_duty'		=> $using_duty,
			'using_tax_cloud'	=> $using_tax_cloud,
			'using_ca_tax'		=> $using_ca_tax,
			'using_state_tax'	=> $using_state_tax,
			'using_country_tax' => $using_country_tax,
			'using_global_tax'  => $using_global_tax,
			'using_auspost'		=> $using_auspost,
			'using_capost'		=> $using_capost,
			'using_dhl'			=> $using_dhl,
			'using_fedex'		=> $using_fedex,
			'using_ups'			=> $using_ups,
			'using_usps'		=> $using_usps,
			'post_linking_type' => $post_linking_type
		);
		$this->send_tracking( 'access_granted', $init_data );
	}
	
	public function plugin_activated( ){
		$this->send_tracking( 'activated' );
	}
	
	public function plugin_deactivated( $plugin, $network_activation ){
		if( $plugin == 'wp-easycart/wpeasycart.php' ){
			$this->send_tracking( 'deactivated' );
		}
	}
	
	public function plugin_custom_deactivate( ){
		$reason_num = (int) $_POST['reason'];
		$reasons = array(
			__( "The plugin didn't work.", 'wp-easycart' ),
			__( "I found a better plugin.", 'wp-easycart' ),
			__( "I need a PRO feature and the upgrade cost is too high.", 'wp-easycart' ),
			__( "Plugin is missing a feature that my project requires.", 'wp-easycart' ),
			__( "It's a temporary deactivation. I'm just debugging an issue.", 'wp-easycart' ),
			__( "Other.", 'wp-easycart' )
		);
		
		$data = array(
			'reason'	=> $reasons[$reason_num-1],
		);
		
		if( $reason_num == 2 )
			$data['plugin'] = esc_attr( $_POST['plugin'] );
		
		if( $reason_num == 4 )
			$data['feature'] = esc_attr( $_POST['feature'] );
		
		if( $reason_num == 6 )
			$data['other'] = esc_attr( $_POST['other'] );
			
		$this->send_tracking( 'deactivation_reason', $data );
	}
	
	public function manual_payment_updated( $enabled ){
		if( $enabled )
			$this->send_tracking( 'manual_payment_enabled' );
		else
			$this->send_tracking( 'manual_payment_disabled' );
	}
	
	public function third_party_updated( $method ){
		$this->send_tracking( 'third_party_updated', array( 'method' => $method ) );
	}
	
	public function live_gateway_updated( $method ){
		$this->send_tracking( 'live_gateway_updated', array( 'method' => $method ) );
	}
	
	public function product_inserted( $product_id ){
		$this->send_tracking( 'product_inserted' );
	}
	
	public function demo_data_installed( ){
		$this->send_tracking( 'demo_data_installed' );
	}
	
	public function plugin_pro_activated( ){
		$this->send_tracking( 'pro_activated' );
	}
	
	public function send_tracking( $event, $args = array( ) ){
		$headr = array( );
		$data = array(
			'event'			=> $event,
			'wpversion'		=> get_bloginfo( 'version' ),
			'phpversion'	=> phpversion( ),
			'wpecversion'	=> EC_CURRENT_VERSION,
			'lang'			=> get_bloginfo( 'language' ),
			'license'		=> $this->get_license_type( ),
		);
		foreach( $args as $key => $val ){
			$data[$key] = $val;
		}
        if( function_exists( 'curl_init' ) ){
            $ch = curl_init( );
            curl_setopt( $ch, CURLOPT_URL, 'https://connect.wpeasycart.com/logging/logging.php' );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, $headr );
            curl_setopt( $ch, CURLOPT_POST, true ); 
            curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
            curl_setopt( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
            curl_setopt( $ch, CURLOPT_TIMEOUT, (int) 5 );
            curl_exec  ( $ch);
            curl_close ( $ch);
        }
	}
	
	public function get_license_type( ){
		$type = 'FREE';
		if( function_exists( 'wp_easycart_admin_license' ) && wp_easycart_admin_license( )->valid_license ){
			$type = 'PRO';
			if( function_exists( 'ec_license_manager' ) ){
				$license_data = ec_license_manager( )->ec_get_license( );
				if( isset( $license_data->model_number ) && $license_data->model_number == 'ec410' ){
					$type = 'PREMIUM';
				}
			}
			if( !wp_easycart_admin_license( )->active_license ){
				$type .= '-EXPIRED';
			}
		}
		return $type;
	}
}
endif; // End if class_exists check

function wp_easycart_admin_tracking( ){
	return wp_easycart_admin_tracking::instance( );
}
wp_easycart_admin_tracking( );