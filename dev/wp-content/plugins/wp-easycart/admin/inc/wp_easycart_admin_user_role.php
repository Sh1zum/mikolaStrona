<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_user_role' ) ) :

final class wp_easycart_admin_user_role{
	
	protected static $_instance = null;
	
	public $user_role_list_file;
	public $user_role_details_file;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}

	public function __construct( ){ 
		$this->user_role_list_file 					= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/users/user-roles/user-role-list.php';	
		$this->user_role_details_file 				= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/users/user-role/user-role-details.php';
		
		/* Process Admin Messages */
		add_filter( 'wp_easycart_admin_success_messages', array( $this, 'add_success_messages' ) );
		add_filter( 'wp_easycart_admin_error_messages', array( $this, 'add_failure_messages' ) );
		
		/* Process Form Actions */
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_add_new_user_role' ) );
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_update_user_role' ) );
		
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_delete_user_role' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_delete_user_role' ) );	
	}
	
	public function process_add_new_user_role( ){
		if( $_POST['ec_admin_form_action'] == "add-new-user-role" ){
			$result = $this->insert_user_role( );
			wp_easycart_admin( )->redirect( 'wp-easycart-users', 'user-roles', $result );
		}
	}
	
	public function process_update_user_role( ){
		if( $_POST['ec_admin_form_action'] == "update-user-role" ){
			$result = $this->update_user_role( );
			wp_easycart_admin( )->redirect( 'wp-easycart-users', 'user-roles', $result );
		}
	}
	
	public function process_delete_user_role( ){
		if( $_GET['ec_admin_form_action'] == 'delete-user-role' && isset( $_GET['role_id'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->delete_user_role( );
			wp_easycart_admin( )->redirect( 'wp-easycart-users', 'user-roles', $result );
		}
	}
	
	public function process_bulk_delete_user_role( ){
		if( $_GET['ec_admin_form_action'] == 'delete-user-role' && !isset( $_GET['role_id'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_delete_user_role( );
			wp_easycart_admin( )->redirect( 'wp-easycart-users', 'user-roles', $result );
		}
	}
	
	public function add_success_messages( $messages ){
		if( isset( $_GET['success'] ) && $_GET['success'] == 'user-role-inserted' ){
			$messages[] = __( 'User role successfully inserted', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'user-role-updated' ){
			$messages[] = __( 'User role successfully updated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'user-role-deleted' ){
			$messages[] = __( 'Users role(s) successfully deleted', 'wp-easycart' );
		}
		return $messages;
	}
	
	public function add_failure_messages( $messages ){
		if( isset( $_GET['error'] ) && $_GET['error'] == 'user-role-edit-master-error' ){
			$messages[] = __( 'You cannot edit the original admin or shopper roles', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'user-role-deleted-master-error' ){
			$messages[] = __( 'You cannot delete the original admin or shopper roles', 'wp-easycart' );
		}
		return $messages;
	}
	
	public function load_user_role_list( ){
		//add new or edit, show details page
		if( ( isset( $_GET['role_id'] ) && isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'edit' ) || 
			( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ) ){
			
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/inc/wp_easycart_admin_details_user_role.php' );
			$details = new wp_easycart_admin_details_user_role( );
			$details->output( esc_attr( $_GET['ec_admin_form_action'] ) );
		
		}else{
			include( $this->user_role_list_file );
		}
	}

	public function insert_user_role( ){
		global $wpdb;
		
		$role_label = stripslashes_deep( $_POST['role_label'] );
		$admin_access = 0;
		if( isset( $_POST['admin_access'] ) )
			$admin_access = 1;
		
		$result = $wpdb->query( $wpdb->prepare( "INSERT INTO ec_role( role_label, admin_access ) VALUES( %s, %d )", $role_label, $admin_access ) );
		if( $admin_access ){
			$this->update_user_remote_access( $role_label );
		}
		return array( 'success' => 'user-role-inserted' );
	}
	
	public function update_user_role( ){	
		global $wpdb;
		
		$role_id = $_POST['role_id'];			
		$old_role_label = $_POST['old_role_label'];
		$role_label = stripslashes_deep( $_POST['role_label'] );
		$admin_access = 0;
		if( isset( $_POST['admin_access'] ) )
			$admin_access = 1;
		
		if( $role_id == 1 || $role_id == 2 ){
			return array( 'error' => 'user-role-edit-master-error' );
		
		}else{
			if( $old_label != $role_label ){
				$wpdb->query( $wpdb->prepare( "UPDATE ec_roleaccess SET role_label = %s WHERE role_label = %s", $role_label, $old_role_label ) );
				$wpdb->query( $wpdb->prepare( "UPDATE ec_roleprice SET role_label = %s WHERE role_label = %s", $role_label, $old_role_label ) );
				$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET user_level = %s WHERE user_level = %s", $role_label, $old_role_label ) );
			}
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_roleaccess WHERE role_label = %s", $role_label ) );
			if( $admin_access ){
				$this->update_user_remote_access( $role_label );
			}
			$wpdb->query( $wpdb->prepare( "UPDATE ec_role SET role_label = %s, admin_access = %d WHERE role_id = %d", $role_label, $admin_access, $role_id ) );
			return array( 'success' => 'user-role-updated' );
		}
	}
	
	private function update_user_remote_access( $role_label ){
		global $wpdb;
		$panels = array( );
		if( isset( $_POST['orders_access'] ) && $_POST['orders_access'] == '1' )
			$panels[] = 'orders';
		if( isset( $_POST['downloads_access'] ) && $_POST['downloads_access'] == '1' )
			$panels[] = 'downloads';
		if( isset( $_POST['subscriptions_access'] ) && $_POST['subscriptions_access'] == '1' )
			$panels[] = 'subscriptions';
		if( isset( $_POST['products_access'] ) && $_POST['products_access'] == '1' )
			$panels[] = 'products';
		if( isset( $_POST['options_access'] ) && $_POST['options_access'] == '1' )
			$panels[] = 'options';
		if( isset( $_POST['menus_access'] ) && $_POST['menus_access'] == '1' )
			$panels[] = 'menus';
		if( isset( $_POST['manufacturers_access'] ) && $_POST['manufacturers_access'] == '1' )
			$panels[] = 'manufacturers';
		if( isset( $_POST['categories_access'] ) && $_POST['categories_access'] == '1' )
			$panels[] = 'categories';
		if( isset( $_POST['reviews_access'] ) && $_POST['reviews_access'] == '1' )
			$panels[] = 'reviews';
		if( isset( $_POST['plans_access'] ) && $_POST['plans_access'] == '1' )
			$panels[] = 'plans';
		if( isset( $_POST['users_access'] ) && $_POST['users_access'] == '1' )
			$panels[] = 'users';
		if( isset( $_POST['giftcards_access'] ) && $_POST['giftcards_access'] == '1' )
			$panels[] = 'giftcards';
		if( isset( $_POST['news_access'] ) && $_POST['news_access'] == '1' )
			$panels[] = 'news';
		if( isset( $_POST['newsletter_access'] ) && $_POST['newsletter_access'] == '1' )
			$panels[] = 'newsletter';
		if( isset( $_POST['coupons_access'] ) && $_POST['coupons_access'] == '1' )
			$panels[] = 'coupons';
		if( isset( $_POST['promotions_access'] ) && $_POST['promotions_access'] == '1' )
			$panels[] = 'promotions';	
		
		foreach( $panels as $admin_panel )
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_roleaccess( role_label, admin_panel ) VALUES( %s, %s )", $role_label, $admin_panel ) );
	}
	
	public function delete_user_role( ){
		global $wpdb;
		$role_id = $_GET['role_id'];
		if( $role_id == 1 || $role_id == 2 ){
			return array( 'error' => 'user-role-deleted-master-error' );
		
		}else{
			$result = $wpdb->query( $wpdb->prepare( "DELETE FROM ec_role WHERE role_id = %d", $role_id ) );
			return array( 'success' => 'user-role-deleted' );		
		}
	}
	
	public function bulk_delete_user_role( ){
		global $wpdb;
		
		$bulk_ids = $_GET['bulk'];
		$has_master = 0;
		foreach( $bulk_ids as $bulk_id ){
			if( $bulk_id == 1 || $bulk_id == 2 ){
				$has_master++;
			}else{
				$wpdb->query( $wpdb->prepare( "DELETE FROM ec_role WHERE role_id = %d", $bulk_id ) );
			}
		}
		
		if( $has_master ){
			return array( 'error' => 'user-role-deleted-master-error' );
		}else{
			return array( 'success' => 'user-role-deleted' );
		}
	}
}
endif; // End if class_exists check

function wp_easycart_admin_user_role( ){
	return wp_easycart_admin_user_role::instance( );
}
wp_easycart_admin_user_role( );