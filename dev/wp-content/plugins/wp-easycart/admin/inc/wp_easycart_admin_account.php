<?php
class wp_easycart_admin_account{
	
	private $wpdb;
	
	public $account_file;
	public $settings_file;
		
	public function __construct( ){
		// Keep reference to wpdb
		global $wpdb;
		$this->wpdb =& $wpdb;
		
		// Setup File Names 
		$this->account_file	 						= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/account/account.php';
		$this->settings_file		 				= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/account/settings.php';
		
		// Actions
		add_action( 'wpeasycart_admin_account_settings', array( $this, 'load_success_messages' ) );
		add_action( 'wpeasycart_admin_account_settings', array( $this, 'load_account_settings' ) );
	}
	
	public function load_account( ){
		include( $this->account_file );
	}
	
	public function load_success_messages( ){
		//include( $this->success_messages_file );
	}
	
	public function load_account_settings( ){
		include( $this->settings_file );
	}
	
	public function save_account_settings( ){
		$options = array( 'ec_option_require_account_terms', 'ec_option_require_account_address', 'ec_option_require_email_validation', 'ec_option_enable_recaptcha', 'ec_option_enable_recaptcha_cart', 'ec_option_show_account_subscriptions_link', 'ec_option_enable_user_notes', 'ec_option_show_subscriber_feature', 'ec_subscriptions_use_first_order_details' );
		
		$options_text = array( 'ec_option_recaptcha_site_key', 'ec_option_recaptcha_secret_key' );
		
		if( isset( $_POST['update_var'] ) && in_array( $_POST['update_var'], $options ) ){
			$val = 0;
			if( isset( $_POST['val'] ) && $_POST['val'] == '1' )
				$val = 1;

			update_option( $_POST['update_var'], $val );
		
		}else if( isset( $_POST['update_var'] ) && in_array( $_POST['update_var'], $options_text ) ){
			update_option( $_POST['update_var'], stripslashes_deep( $_POST['val'] ) );
			
		}
	}
	
}

add_action( 'wp_ajax_ec_admin_ajax_save_account_settings', 'ec_admin_ajax_save_account_settings' );
function ec_admin_ajax_save_account_settings( ){
	$account = new wp_easycart_admin_account( );
	$account->save_account_settings( );
	die( );
}