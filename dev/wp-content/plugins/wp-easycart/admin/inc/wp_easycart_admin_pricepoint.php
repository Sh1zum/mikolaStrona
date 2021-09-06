<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_pricepoint' ) ) :

final class wp_easycart_admin_pricepoint{
	
	protected static $_instance = null;
	
	public $pricepoint_list_file;
	public $pricepoint_details_file;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
	
	public function __construct( ){ 
		$this->pricepoint_list_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/pricepoint/pricepoint-list.php';
		$this->pricepoint_details_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/pricepoint/pricepoint-details.php';
		
		/* Process Admin Messages */
		add_filter( 'wp_easycart_admin_success_messages', array( $this, 'add_success_messages' ) );
		
		/* Process Form Actions */
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_add_new_pricepoint' ) );
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_update_pricepoint' ) );
		
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_delete_pricepoint' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_delete_pricepoints' ) );
	}
	
	public function process_add_new_pricepoint( ){
		if( $_POST['ec_admin_form_action'] == "add-new-pricepoint" ){
			$result = $this->insert_pricepoint( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'pricepoint', $result );
		}
	}
	
	public function process_update_pricepoint( ){
		if( $_POST['ec_admin_form_action'] == "update-pricepoint" ){
			$result = $this->update_pricepoint( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'pricepoint', $result );
		}
	}
	
	public function process_delete_pricepoint( ){
		if( isset($_GET['subpage']) == 'pricepoint' && $_GET['ec_admin_form_action'] == 'delete-pricepoint' && isset( $_GET['pricepoint_id'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->delete_pricepoint( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'pricepoint', $result );
		}
	}
	
	public function process_bulk_delete_pricepoints( ){
		if( isset($_GET['subpage']) == 'pricepoint' && $_GET['ec_admin_form_action'] == 'delete-pricepoint' && !isset( $_GET['pricepoint_id'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_delete_pricepoint( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'pricepoint', $result );
		}
	}
			
	public function add_success_messages( $messages ){
		if( isset( $_GET['success'] ) && $_GET['success'] == 'pricepoint-inserted' ){
			$messages[] = __( 'Price point successfully created', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'pricepoint-updated' ){
			$messages[] = __( 'Price point successfully updated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'pricepoint-deleted' ){
			$messages[] = __( 'Price point successfully deleted', 'wp-easycart' );
		}
		return $messages;
	}
	
	public function load_pricepoint_list( ){
		if( ( isset( $_GET['pricepoint_id'] ) && isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'edit' ) || 
			( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ) ){
			
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/inc/wp_easycart_admin_details_pricepoint.php' );
			$details = new wp_easycart_admin_details_pricepoint( );
			$details->output( esc_attr( $_GET['ec_admin_form_action'] ) );
		
		}else{
			include( $this->pricepoint_list_file );
		
		}
	}
	
	public function insert_pricepoint( ){
		global $wpdb;
		
		$low_point = $_POST['low_point'];
		$high_point = $_POST['high_point'];
		$pricepoint_order = $_POST['pricepoint_order'];
		$is_less_than = $is_greater_than = 0;
		if( isset( $_POST['is_less_than'] ) )
			$is_less_than = 1;
		
		if( isset( $_POST['is_greater_than'] ) )
			$is_greater_than = 1;
		
		$wpdb->query( $wpdb->prepare( "INSERT INTO ec_pricepoint( is_less_than, is_greater_than, low_point, high_point, pricepoint_order ) VALUES( %d, %d, %s, %s, %d )", $is_less_than, $is_greater_than, $low_point, $high_point, $pricepoint_order ) );
		
		
		return array( 'success' => 'pricepoint-inserted' );
	}
	
	
	public function update_pricepoint( ){	
		global $wpdb;
		
		$pricepoint_id = $_POST['pricepoint_id'];			
		$low_point = $_POST['low_point'];
		$high_point = $_POST['high_point'];
		$pricepoint_order = $_POST['pricepoint_order'];
		$is_less_than = $is_greater_than = 0;
		if( isset( $_POST['is_less_than'] ) )
			$is_less_than = 1;
		
		if( isset( $_POST['is_greater_than'] ) )
			$is_greater_than = 1;
		
		$wpdb->query( $wpdb->prepare( "UPDATE ec_pricepoint SET is_less_than = %d, is_greater_than = %d, low_point = %s, high_point = %s, pricepoint_order = %d WHERE pricepoint_id = %d", $is_less_than, $is_greater_than, $low_point, $high_point, $pricepoint_order, $pricepoint_id ) );
		
		return array( 'success' => 'pricepoint-updated' );
	}
	
	
	public function delete_pricepoint( ){
		global $wpdb;
		$pricepoint_id = $_GET['pricepoint_id'];		
		$result = $wpdb->query( $wpdb->prepare( "DELETE FROM ec_pricepoint WHERE pricepoint_id = %d", $pricepoint_id ) );
		return array( 'success' => 'pricepoint-deleted' );
	}
	
	public function bulk_delete_pricepoint( ){
		global $wpdb;
		$bulk_ids = $_GET['bulk'];
		foreach( $bulk_ids as $bulk_id ){
			$result = $wpdb->query( $wpdb->prepare( "DELETE FROM ec_pricepoint WHERE pricepoint_id = %d", $bulk_id ) );
		}
		return array( 'success' => 'pricepoint-deleted' );
	}
}
endif; // End if class_exists check

function wp_easycart_admin_pricepoint( ){
	return wp_easycart_admin_pricepoint::instance( );
}
wp_easycart_admin_pricepoint( );