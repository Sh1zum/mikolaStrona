<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_country' ) ) :

final class wp_easycart_admin_country{
	
	protected static $_instance = null;
	
	public $country_list_file;
	public $country_details_file;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
	
	public function __construct( ){ 
		$this->country_list_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/country-state/country-list.php';
		$this->country_details_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/country-state/country-details.php';
		
		/* Process Admin Messages */
		add_filter( 'wp_easycart_admin_success_messages', array( $this, 'add_success_messages' ) );
		
		/* Process Form Actions */
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_add_new_country' ) );
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_update_country' ) );
		
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_delete_country' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_delete_country' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_disable_country' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_enable_country' ) );
	}
	
	public function process_add_new_country( ){
		if( $_POST['ec_admin_form_action'] == "add-new-country" ){
			$result = $this->insert_country( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'country', $result );
		}
	}
	
	public function process_update_country( ){
		if( $_POST['ec_admin_form_action'] == "update-country" ){
			$result = $this->update_country( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'country', $result );
		}
	}
	
	public function process_delete_country( ){
		if( isset($_GET['subpage']) == 'country' && $_GET['ec_admin_form_action'] == 'delete-country' && isset( $_GET['id_cnt'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->delete_country( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'country', $result );
		}
	}
	public function process_bulk_delete_country( ){
		if( isset($_GET['subpage']) == 'country' && $_GET['ec_admin_form_action'] == 'delete-country' && !isset( $_GET['id_cnt'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_delete_country( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'country', $result );
		}
	}
	
	public function process_bulk_disable_country( ){
		if( isset($_GET['subpage']) == 'country' && $_GET['ec_admin_form_action'] == 'bulk-enable-country' && !isset( $_GET['id_cnt'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_enable_country( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'country', $result );
		}
	}
	
	public function process_bulk_enable_country( ){
		if( isset($_GET['subpage']) == 'country' && $_GET['ec_admin_form_action'] == 'bulk-disable-country' && !isset( $_GET['id_cnt'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_disable_country( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'country', $result );
		}
	}
	
	public function add_success_messages( $messages ){
		if( isset( $_GET['success'] ) && $_GET['success'] == 'country-inserted' ){
			$messages[] = __( 'Country successfully created', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'country-updated' ){
			$messages[] = __( 'Country successfully updated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'country-deleted' ){
			$messages[] = __( 'Country successfully deleted', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'country-bulk-enabled' ){
			$messages[] = __( 'Countries successfully enabled', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'country-bulk-disabled' ){
			$messages[] = __( 'Countries successfully disabled', 'wp-easycart' );
		}
		return $messages;
	}
	
	public function load_country_list( ){
		if( ( isset( $_GET['id_cnt'] ) && isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'edit' ) || 
			( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ) ){
			
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/inc/wp_easycart_admin_details_country.php' );
			$details = new wp_easycart_admin_details_country( );
			$details->output( esc_attr( $_GET['ec_admin_form_action'] ) );
		
		}else{
			include( $this->country_list_file );
		}
	}
	
	public function insert_country( ){
		global $wpdb;
		
		$name_cnt = stripslashes_deep( $_POST['name_cnt'] );
		$iso2_cnt = $_POST['iso2_cnt'];
		$iso3_cnt = $_POST['iso3_cnt'];
		$sort_order = $_POST['sort_order'];
		$vat_rate_cnt = $_POST['vat_rate_cnt']; 
		$ship_to_active = 0;
		if( isset( $_POST['ship_to_active'] ) )
			$ship_to_active = 1;
		
		$wpdb->query( $wpdb->prepare( "INSERT INTO ec_country( name_cnt, iso2_cnt, iso3_cnt, sort_order, vat_rate_cnt, ship_to_active ) VALUES( %s, %s, %s, %d, %s, %d )", $name_cnt, $iso2_cnt, $iso3_cnt, $sort_order, $vat_rate_cnt, $ship_to_active ) );
		
		return array( 'success' => 'country-inserted' );
	}
	
	public function update_country( ){	
		global $wpdb;
		
		$id_cnt = $_POST['id_cnt'];			
		$name_cnt = stripslashes_deep( $_POST['name_cnt'] );
		$iso2_cnt = $_POST['iso2_cnt'];
		$iso3_cnt = $_POST['iso3_cnt'];
		$sort_order = $_POST['sort_order'];
		$vat_rate_cnt = $_POST['vat_rate_cnt']; 
		$ship_to_active = 0;
		if( isset( $_POST['ship_to_active'] ) )
			$ship_to_active = 1;
		
		$wpdb->query( $wpdb->prepare( "UPDATE ec_country SET name_cnt = %s, iso2_cnt = %s, iso3_cnt = %s, sort_order = %d, vat_rate_cnt = %s, ship_to_active = %d WHERE id_cnt = %d", $name_cnt, $iso2_cnt, $iso3_cnt, $sort_order, $vat_rate_cnt, $ship_to_active, $id_cnt ) );
		
		return array( 'success' => 'country-updated' );	
	}
	
	public function delete_country( ){
		global $wpdb;
		$id_cnt = $_GET['id_cnt'];	
		$result = $wpdb->query( $wpdb->prepare( "DELETE FROM ec_country WHERE id_cnt = %d", $id_cnt ) );
		return array( 'success' => 'country-deleted' );	
	}
	
	public function bulk_delete_country( ){
		global $wpdb;
		$bulk_ids = $_GET['bulk'];
		foreach( $bulk_ids as $bulk_id ){
			$result = $wpdb->query( $wpdb->prepare( "DELETE FROM ec_country WHERE id_cnt = %d", $bulk_id ) );
		}
		return array( 'success' => 'country-deleted' );
	}
	
	public function bulk_enable_country( ){
		global $wpdb;
		$bulk_ids = $_GET['bulk'];
		foreach( $bulk_ids as $bulk_id ){
			$wpdb->query( $wpdb->prepare( "UPDATE ec_country SET ship_to_active = 1 WHERE ec_country.id_cnt = %d", $bulk_id ) );
			$wpdb->query( $wpdb->prepare( "UPDATE ec_state SET ship_to_active = 1 WHERE ec_state.idcnt_sta = %d", $bulk_id ) );
		}
		return array( 'success' => 'country-bulk-enabled' );
	}
	public function bulk_disable_country( ){
		global $wpdb;
		$bulk_ids = $_GET['bulk'];
		foreach( $bulk_ids as $bulk_id ){
			$wpdb->query( $wpdb->prepare( "UPDATE ec_country SET ship_to_active = 0 WHERE ec_country.id_cnt = %d", $bulk_id ) );
			$wpdb->query( $wpdb->prepare( "UPDATE ec_state SET ship_to_active = 0 WHERE ec_state.idcnt_sta = %d", $bulk_id ) );
		}
		return array( 'success' => 'country-bulk-disabled' );
	}
}
endif; // End if class_exists check

function wp_easycart_admin_country( ){
	return wp_easycart_admin_country::instance( );
}
wp_easycart_admin_country( );