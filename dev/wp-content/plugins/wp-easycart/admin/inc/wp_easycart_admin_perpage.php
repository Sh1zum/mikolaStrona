<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_perpage' ) ) :

final class wp_easycart_admin_perpage{
	
	protected static $_instance = null;
	
	public $perpage_list_file;
	public $perpage_details_file;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
	
	public function __construct( ){ 
		$this->perpage_list_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/perpage/perpage-list.php';
		$this->perpage_details_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/perpage/perpage-details.php';
		
		/* Process Admin Messages */
		add_filter( 'wp_easycart_admin_success_messages', array( $this, 'add_success_messages' ) );
		
		/* Process Form Actions */
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_add_new_perpage' ) );
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_update_perpage' ) );
		
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_delete_perpage' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_delete_perpages' ) );
	}
	
	public function process_add_new_perpage( ){
		if( $_POST['ec_admin_form_action'] == "add-new-perpage" ){
			$result = $this->insert_perpage( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'perpage', $result );
		}
	}
	
	public function process_update_perpage( ){
		if( $_POST['ec_admin_form_action'] == "update-perpage" ){
			$result = $this->update_perpage( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'perpage', $result );
		}
	}
	public function process_delete_perpage( ){
		if( $_GET['ec_admin_form_action'] == 'delete-perpage' && isset( $_GET['perpage_id'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->delete_perpage( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'perpage', $result );
		}
	}
	public function process_bulk_delete_perpages( ){
		if( $_GET['ec_admin_form_action'] == 'delete-perpage' && !isset( $_GET['perpage_id'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_delete_perpage( );
			wp_easycart_admin( )->redirect( 'wp-easycart-settings', 'perpage', $result );
		}
	}
	
	public function add_success_messages( $messages ){
		if( isset( $_GET['success'] ) && $_GET['success'] == 'perpage-inserted' ){
			$messages[] = __( 'Per page successfully created', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'perpage-updated' ){
			$messages[] = __( 'Per page successfully updated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'perpage-deleted' ){
			$messages[] = __( 'Per page successfully deleted', 'wp-easycart' );
		}
		return $messages;
	}
	
	public function load_perpage_list( ){
		if( ( isset( $_GET['perpage_id'] ) && isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'edit' ) || 
			( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ) ){
			
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/inc/wp_easycart_admin_details_perpage.php' );
			$details = new wp_easycart_admin_details_perpage( );
			$details->output( esc_attr( $_GET['ec_admin_form_action'] ) );
		
		}else{
			include( $this->perpage_list_file );
		
		}
	}
	
	public function insert_perpage( ){
		global $wpdb;
		$perpage = $_POST['perpage'];
		$wpdb->query( $wpdb->prepare( "INSERT INTO ec_perpage( perpage ) VALUES( %d )", $perpage ) );
		return array( 'success' => 'perpage-inserted' );
	}
	
	public function update_perpage( ){	
		global $wpdb;
		$perpage_id = $_POST['perpage_id'];			
		$perpage = $_POST['perpage'];
		$result = $wpdb->query( $wpdb->prepare( "UPDATE ec_perpage SET perpage = %d WHERE perpage_id = %d", $perpage, $perpage_id ) );
		return array( 'success' => 'perpage-updated' );
	}
	
	public function delete_perpage( ){
		global $wpdb;
		$perpage_id = $_GET['perpage_id'];		
		$result = $wpdb->query( $wpdb->prepare( "DELETE FROM ec_perpage WHERE perpage_id = %d", $perpage_id ) );
		return array( 'success' => 'perpage-deleted' );
	}
	
	public function bulk_delete_perpage( ){
		global $wpdb;
		$bulk_ids = $_GET['bulk'];
		foreach( $bulk_ids as $bulk_id ){
			$result = $wpdb->query( $wpdb->prepare( "DELETE FROM ec_perpage WHERE perpage_id = %d", $bulk_id ) );
		}
		return array( 'success' => 'perpage-deleted' );
	}
}
endif; // End if class_exists check

function wp_easycart_admin_perpage( ){
	return wp_easycart_admin_perpage::instance( );
}
wp_easycart_admin_perpage( );