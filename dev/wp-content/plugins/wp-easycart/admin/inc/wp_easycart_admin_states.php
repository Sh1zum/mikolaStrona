<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_states' ) ) :

final class wp_easycart_admin_states{
	
	protected static $_instance = null;
	
	public $states_list_file;
	public $states_details_file;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
	
	public function __construct( ){ 
		$this->states_list_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/country-state/states-list.php';
		$this->states_details_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/country-state/states-details.php';
		
		/* Process Admin Messages */
		add_filter( 'wp_easycart_admin_success_messages', array( $this, 'add_success_messages' ) );
		
		/* Process Form Actions */
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_add_new_state' ) );
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_update_state' ) );
		
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_delete_state' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_delete_states' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_disable_states' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_enable_states' ) );
	}
	
	public function process_add_new_state( ){
		if( $_POST['ec_admin_form_action'] == "add-new-states" ){
			$result = $this->insert_states( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'states', $result );
		}
	}
	
	public function process_update_state( ){
		if( $_POST['ec_admin_form_action'] == "update-states" ){
			$result = $this->update_states( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'states', $result );
		}
	}
	
	public function process_delete_state( ){
		if( isset($_GET['subpage']) == 'states' && $_GET['ec_admin_form_action'] == 'delete-state' && isset( $_GET['id_sta'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->delete_states( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'states', $result );
		}
	}
	
	public function process_bulk_delete_states( ){
		if( isset($_GET['subpage']) == 'states' && $_GET['ec_admin_form_action'] == 'delete-state' && !isset( $_GET['id_sta'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_delete_states( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'states', $result );
		}
	}
	
	public function process_bulk_disable_states( ){
		if( isset($_GET['subpage']) == 'states' && $_GET['ec_admin_form_action'] == 'bulk-enable-state' && !isset( $_GET['id_sta'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_enable_states( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'states', $result );
		}
	}
	
	public function process_bulk_enable_states( ){
		if( isset($_GET['subpage']) == 'states' && $_GET['ec_admin_form_action'] == 'bulk-disable-state' && !isset( $_GET['id_sta'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_disable_states( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'states', $result );
		}
	}
	
	public function add_success_messages( $messages ){
		if( isset( $_GET['success'] ) && $_GET['success'] == 'states-inserted' ){
			$messages[] = __( 'State successfully created', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'states-updated' ){
			$messages[] = __( 'State successfully updated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'states-deleted' ){
			$messages[] = __( 'State successfully deleted', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'states-bulk-enabled' ){
			$messages[] = __( 'States successfully enabled', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'states-bulk-disabled' ){
			$messages[] = __( 'States successfully disabled', 'wp-easycart' );
		}
		return $messages;
	}
	
	public function load_states_list( ){
		if( ( isset( $_GET['id_sta'] ) && isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'edit' ) || 
			( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ) ){
			
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/inc/wp_easycart_admin_details_states.php' );
			$details = new wp_easycart_admin_details_states( );
			$details->output( esc_attr( $_GET['ec_admin_form_action'] ) );
		
		}else{
			include( $this->states_list_file );
		
		}
	}
	
	public function insert_states( ){
		global $wpdb;
		
		$idcnt_sta = $_POST['idcnt_sta'];
		$code_sta = $_POST['code_sta'];
		$name_sta = stripslashes_deep( $_POST['name_sta'] );
		$sort_order = $_POST['sort_order'];
		$group_sta = $_POST['group_sta']; 
		$ship_to_active = 0;
		if( isset( $_POST['ship_to_active'] ) )
			$ship_to_active = 1;
		
		$wpdb->query( $wpdb->prepare( "INSERT INTO ec_state( idcnt_sta, code_sta, name_sta, sort_order, group_sta, ship_to_active ) VALUES( %d, %s, %s, %d, %s, %d )", $idcnt_sta, $code_sta, $name_sta, $sort_order, $group_sta, $ship_to_active ) );
		
		return array('success' => 'states-inserted' );
	}
	
	
	public function update_states( ){	
		global $wpdb;
		
		$id_sta = $_POST['id_sta'];			
		$idcnt_sta = $_POST['idcnt_sta'];
		$code_sta = $_POST['code_sta'];
		$name_sta = stripslashes_deep( $_POST['name_sta'] );
		$sort_order = $_POST['sort_order'];
		$group_sta = $_POST['group_sta']; 
		$ship_to_active = 0;
		if( isset( $_POST['ship_to_active'] ) )
			$ship_to_active = 1;
		
		$wpdb->query( $wpdb->prepare( "UPDATE ec_state SET idcnt_sta = %d, code_sta = %s, name_sta = %s, sort_order = %d, group_sta = %s, ship_to_active = %d WHERE id_sta = %s", $idcnt_sta, $code_sta, $name_sta, $sort_order, $group_sta, $ship_to_active, $id_sta ) );
		
		return array('success' => 'states-updated' );
	}
	
	
	public function delete_states( ){
		global $wpdb;
		$id_sta = $_GET['id_sta'];	
		$result = $wpdb->query( $wpdb->prepare( "DELETE FROM ec_state WHERE id_sta = %d", $id_sta ) );
		return array('success' => 'states-deleted' );
	}
	
	public function bulk_delete_states( ){
		global $wpdb;
		$bulk_ids = $_GET['bulk'];
		foreach( $bulk_ids as $bulk_id ){
			$result = $wpdb->query( $wpdb->prepare( "DELETE FROM ec_state WHERE id_sta = %d", $bulk_id ) );
		}
		
		return array('success' => 'states-deleted' );
	}
	
	public function bulk_enable_states( ){
		global $wpdb;
		$bulk_ids = $_GET['bulk'];
		foreach( $bulk_ids as $bulk_id ){
			$result = $wpdb->query( $wpdb->prepare( "UPDATE ec_state SET ship_to_active = 1 WHERE id_sta = %d", $bulk_id ) );
		}
		return array('success' => 'states-bulk-enabled' );
	}
	
	public function bulk_disable_states( ){
		global $wpdb;
		$bulk_ids = $_GET['bulk'];
		foreach( $bulk_ids as $bulk_id ){
			$result = $wpdb->query( $wpdb->prepare( "UPDATE ec_state SET ship_to_active = 0 WHERE ec_state.id_sta = %d", $bulk_id ) );
		}
		return array('success' => 'states-bulk-disabled' );
	}
}
endif; // End if class_exists check

function wp_easycart_admin_states( ){
	return wp_easycart_admin_states::instance( );
}
wp_easycart_admin_states( );