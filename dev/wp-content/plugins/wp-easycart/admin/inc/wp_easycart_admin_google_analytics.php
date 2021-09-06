<?php
class wp_easycart_admin_google_analytics{
	
	private $wpdb;
	
	public $design_file;
	public $settings_file;
		
	public function __construct( ){
		// Keep reference to wpdb
		global $wpdb;
		$this->wpdb =& $wpdb;
		
		// Setup File Names 
		$this->google_analytics_design_file	 = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/third-party/google-analytics.php';
		$this->google_adwords_design_file	 = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/third-party/google-adwords.php';
		$this->settings_file		 		 = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/third-party/google.php';
		$this->amazon_file	 				 = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/third-party/amazon.php';
		$this->deconetwork_file	 			 = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/third-party/deconetwork.php';
		
		// Actions
		add_action( 'wpeasycart_admin_google_settings', array( $this, 'load_success_messages' ) );
		add_action( 'wpeasycart_admin_google_settings', array( $this, 'load_google_analytics_design' ) );
		add_action( 'wpeasycart_admin_google_settings', array( $this, 'load_google_adwords_design' ) );
		add_action( 'wpeasycart_admin_google_settings', array( $this, 'load_amazon_settings' ) );
		add_action( 'wpeasycart_admin_google_settings', array( $this, 'load_deconetwork_settings' ) );
	}
	
	public function load_google_analytics( ){
		
		include( $this->settings_file );
	}
	
	public function load_success_messages( ){
		include( $this->success_messages_file );
	}
	public function load_amazon_settings( ){
		include( $this->amazon_file );
	}
	public function load_deconetwork_settings( ){
		include( $this->deconetwork_file );
	}
	public function load_google_analytics_design( ){
		
		include( $this->google_analytics_design_file );
	}
	public function load_google_adwords_design( ){
		
		include( $this->google_adwords_design_file );
	}
	
	public function save_amazon_settings( ) {
		$ec_option_amazon_key = $_POST['ec_option_amazon_key'] ;
		$ec_option_amazon_secret = $_POST['ec_option_amazon_secret'] ;
		$ec_option_amazon_bucket = $_POST['ec_option_amazon_bucket'] ;
		$ec_option_amazon_bucket_region = $_POST['ec_option_amazon_bucket_region'] ;
		
		if( isset( $_POST['ec_option_amazon_key'] ))
			$ec_option_amazon_key = $_POST['ec_option_amazon_key']  ;
		if( isset( $_POST['ec_option_amazon_secret'] ))
			$ec_option_amazon_secret = $_POST['ec_option_amazon_secret']  ;
		if( isset( $_POST['ec_option_amazon_bucket'] ))
			$ec_option_amazon_bucket = $_POST['ec_option_amazon_bucket']  ;
		if( isset( $_POST['ec_option_amazon_bucket_region'] ))
			$ec_option_amazon_bucket_region = $_POST['ec_option_amazon_bucket_region']  ;

		
		update_option( 'ec_option_amazon_key', $ec_option_amazon_key );
		update_option( 'ec_option_amazon_secret', $ec_option_amazon_secret );
		update_option( 'ec_option_amazon_bucket', $ec_option_amazon_bucket );
		update_option( 'ec_option_amazon_bucket_region', $ec_option_amazon_bucket_region );
	}
	
	public function save_deconetwork_settings( ) {
		$ec_option_deconetwork_url = $_POST['ec_option_deconetwork_url'] ;
		$ec_option_deconetwork_password = $_POST['ec_option_deconetwork_password'] ;
		
		if( isset( $_POST['ec_option_deconetwork_url'] ))
			$ec_option_deconetwork_url = $_POST['ec_option_deconetwork_url'] ;
		if( isset( $_POST['ec_option_deconetwork_password'] ))
			$ec_option_deconetwork_password = $_POST['ec_option_deconetwork_password'] ;

		
		update_option( 'ec_option_deconetwork_url', $ec_option_deconetwork_url );
		update_option( 'ec_option_deconetwork_password', $ec_option_deconetwork_password );
	}
	
	public function save_google_analytics( ){
		$ec_option_googleanalyticsid =  'UA-XXXXXXX-X';
		
		if( isset( $_POST['ec_option_googleanalyticsid'] ))
			$ec_option_googleanalyticsid = $_POST['ec_option_googleanalyticsid'] ;
		
		update_option( 'ec_option_googleanalyticsid', $ec_option_googleanalyticsid );

	}
	public function save_google_adwords( ){
		$ec_option_google_adwords_conversion_id =  '';
		$ec_option_google_adwords_language =  'en';
		$ec_option_google_adwords_format =  '3';
		$ec_option_google_adwords_color =  'FFFFFF';
		$ec_option_google_adwords_currency =  'USD';
		$ec_option_google_adwords_remarketing_only =  "false";
		
		if( isset( $_POST['ec_option_google_adwords_conversion_id'] ))
			$ec_option_google_adwords_conversion_id = $_POST['ec_option_google_adwords_conversion_id'] ;
		if( isset( $_POST['ec_option_google_adwords_language'] ))
			$ec_option_google_adwords_language = $_POST['ec_option_google_adwords_language'] ;
		if( isset( $_POST['ec_option_google_adwords_format'] ))
			$ec_option_google_adwords_format = $_POST['ec_option_google_adwords_format'] ;
		if( isset( $_POST['ec_option_google_adwords_color'] ))
			$ec_option_google_adwords_color = $_POST['ec_option_google_adwords_color'] ;
		if( isset( $_POST['ec_option_google_adwords_currency'] ))
			$ec_option_google_adwords_currency = $_POST['ec_option_google_adwords_currency'] ;
		if( isset( $_POST['ec_option_google_adwords_label'] ))
			$ec_option_google_adwords_label = $_POST['ec_option_google_adwords_label'] ;
		if( isset( $_POST['ec_option_google_adwords_remarketing_only'] ))
			$ec_option_google_adwords_remarketing_only = $_POST['ec_option_google_adwords_remarketing_only'] ;
		
		update_option( 'ec_option_google_adwords_conversion_id', $ec_option_google_adwords_conversion_id );
		update_option( 'ec_option_google_adwords_language', $ec_option_google_adwords_language );
		update_option( 'ec_option_google_adwords_format', $ec_option_google_adwords_format );
		update_option( 'ec_option_google_adwords_color', $ec_option_google_adwords_color );
		update_option( 'ec_option_google_adwords_currency', $ec_option_google_adwords_currency );
		update_option( 'ec_option_google_adwords_label', $ec_option_google_adwords_label );
		update_option( 'ec_option_google_adwords_remarketing_only', $ec_option_google_adwords_remarketing_only );
	}
	
}

add_action( 'wp_ajax_ec_admin_ajax_save_deconetwork_settings', 'ec_admin_ajax_save_deconetwork_settings' );
function ec_admin_ajax_save_deconetwork_settings( ){
	$deconetwork_settings = new wp_easycart_admin_google_analytics( );
	$deconetwork_settings->save_deconetwork_settings( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_amazon_settings', 'ec_admin_ajax_save_amazon_settings' );
function ec_admin_ajax_save_amazon_settings( ){
	$amazon_settings = new wp_easycart_admin_google_analytics( );
	$amazon_settings->save_amazon_settings( );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_google_analytics', 'ec_admin_ajax_save_google_analytics' );
function ec_admin_ajax_save_google_analytics( ){
	$google_analytics = new wp_easycart_admin_google_analytics( );
	$google_analytics->save_google_analytics( );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_google_adwords', 'ec_admin_ajax_save_google_adwords' );
function ec_admin_ajax_save_google_adwords( ){
	$google_analytics = new wp_easycart_admin_google_analytics( );
	$google_analytics->save_google_adwords( );
	die( );
}