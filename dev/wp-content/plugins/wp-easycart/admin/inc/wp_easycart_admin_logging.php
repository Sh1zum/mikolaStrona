<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_logging' ) ) :

final class wp_easycart_admin_logging{
	
	protected static $_instance = null;
	
	public $log_list_file;
	public $log_details_file;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	}
	
	public function __construct( ){ 
		$this->log_list_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/logging/log-list.php';
		$this->log_details_file = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/logging/log-details.php';
	}
	
	public function load_log_list( ){
		if( ( isset( $_GET['response_id'] ) && isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'edit' ) || 
			( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ) ){
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/inc/wp_easycart_admin_details_logging.php' );
				$details = new wp_easycart_admin_details_logging( );
				$details->output( esc_attr( $_GET['ec_admin_form_action'] ) );
		}else{
			include( $this->log_list_file );
		
		}
	}
	
	
}
endif; // End if class_exists check

function wp_easycart_admin_logging( ){
	return wp_easycart_admin_logging::instance( );
}
wp_easycart_admin_logging( );