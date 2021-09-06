<?php
class wp_easycart_admin_language_editor{
	
	private $wpdb;
	
	public $language_file;
	public $settings_file;
		
	public function __construct( ){
		// Keep reference to wpdb
		global $wpdb;
		$this->wpdb =& $wpdb;
		
		// Setup File Names 
		$this->language_file	 			= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/language-editor/language.php';
		$this->language_settings_file	 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/language-editor/language-settings.php';
		$this->settings_file		 		= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/language-editor/settings.php';
		
		// Actions
		//add_action( 'wpeasycart_admin_language_editor_settings', array( $this, 'load_success_messages' ) );
		add_action( 'wpeasycart_admin_language_editor_settings', array( $this, 'load_language_editor_settings' ) );
		add_action( 'wpeasycart_admin_language_editor', array( $this, 'load_language_editor' ) );
	}
	
	public function load_language( ){
		include( $this->settings_file );
	}
	
	public function load_success_messages( ){
		//include( $this->success_messages_file );
	}
	
	public function load_language_editor_settings( ){
		include( $this->language_settings_file );
	}
	public function load_language_editor( ){
		include( $this->language_file );
	}
	
	public function save_language_settings( ) {
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
	
}

add_action( 'wp_ajax_ec_admin_ajax_save_language_editor', 'ec_admin_ajax_save_language_editor' );
function ec_admin_ajax_save_language_editor( ){
	$amazon_settings = new wp_easycart_admin_language_editor( );
	$amazon_settings->save_language_settings( );
	die( );
}