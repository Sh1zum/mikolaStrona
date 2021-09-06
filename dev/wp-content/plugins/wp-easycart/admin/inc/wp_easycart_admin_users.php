<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_users' ) ) :

final class wp_easycart_admin_users{
	
	protected static $_instance = null;
	
	public $users_list_file;
	public $users_details_file;
	public $export_accounts_csv;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
	
	public function __construct( ){ 
		$this->users_list_file 					= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/users/users/user-list.php';	
		$this->users_details_file 				= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/users/users/user-details.php';
		$this->export_accounts_csv				= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/exporters/export-accounts-csv.php';
		
		/* Process Admin Messages */
		add_filter( 'wp_easycart_admin_success_messages', array( $this, 'add_success_messages' ) );
		add_filter( 'wp_easycart_admin_error_messages', array( $this, 'add_failure_messages' ) );
		
		/* Process Form Actions */
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_add_new_user' ) );
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_update_user' ) );
		
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_login_as_user' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_delete_user' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_delete_user' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_export_users' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_force_password_reset' ) );
	}
	
	
	
	public function process_add_new_user( ){
		if( $_POST['ec_admin_form_action'] == "add-new-user" ){
			$result = $this->insert_user( );
			wp_easycart_admin( )->redirect( 'wp-easycart-users', 'accounts', $result );
		}
	}
	
	public function process_update_user( ){
		if( $_POST['ec_admin_form_action'] == "update-user" ){
			$result = $this->update_user( );
			wp_easycart_admin( )->redirect( 'wp-easycart-users', 'accounts', $result );
		}
	}
	
	public function process_login_as_user( ){
		if( $_GET['ec_admin_form_action'] == 'user-login-override' && isset( $_GET['user_id'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->login_as_user( );
			wp_easycart_admin( )->redirect( 'wp-easycart-users', 'accounts', $result );
		}
	}
	
	public function process_delete_user( ){
		if( $_GET['ec_admin_form_action'] == 'delete-account' && isset( $_GET['user_id'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->delete_user( );
			wp_easycart_admin( )->redirect( 'wp-easycart-users', 'accounts', $result );
		}
	}
	
	public function process_bulk_delete_user( ){
		if( $_GET['ec_admin_form_action'] == 'delete-account' && !isset( $_GET['user_id'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_delete_user( );
			wp_easycart_admin( )->redirect( 'wp-easycart-users', 'accounts', $result );
		}
	}
	
	public function process_export_users( ){
		if( $_GET['ec_admin_form_action'] == 'export-accounts-csv' || $_GET['ec_admin_form_action'] == 'export-accounts-csv-all' ){
			include( $this->export_accounts_csv );
			die( );
		}
	}
	
	public function process_force_password_reset( ){
		if( $_GET['ec_admin_form_action'] == 'accounts-force-password-reset' && !isset( $_GET['user_id'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_force_password_reset( );
			wp_easycart_admin( )->redirect( 'wp-easycart-users', 'accounts', $result );
		}
	}
	
	public function add_success_messages( $messages ){
		if( isset( $_GET['success'] ) && $_GET['success'] == 'user-inserted' ){
			$messages[] = __( 'User successfully inserted', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'user-updated' ){
			$messages[] = __( 'User successfully updated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'user-deleted' ){
			$messages[] = __( 'Users(s) successfully deleted', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'user-logged-in' ){
			$messages[] = __( 'You are now logged in as this user. Please use caution when viewing the store.', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'user-password-reset' ){
			$messages[] = __( 'User(s) passwords were successfully reset and emailed with information to update their password.', 'wp-easycart' );
		}
		return $messages;
	}
	
	public function add_failure_messages( $messages ){
		if( isset( $_GET['error'] ) && $_GET['error'] == 'user-duplicate' ){
			$messages[] = __( 'User email already exists', 'wp-easycart' );
		}
		return $messages;
	}
	
	public function load_users_list( ){
		//add new or edit, show details page
		if( ( isset( $_GET['user_id'] ) && isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'edit' ) || 
			( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ) ){
			
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/inc/wp_easycart_admin_details_user.php' );
			$details = new wp_easycart_admin_details_user( );
			$details->output( esc_attr( $_GET['ec_admin_form_action'] ) );
		
		}else{
			include( $this->users_list_file );
		
		}
	}
	
	public function insert_user( ){
		global $wpdb;
		
		$email = stripslashes_deep( $_POST['email'] );
		$password = md5( stripslashes_deep( $_POST['password'] ) );
		$first_name = stripslashes_deep( $_POST['first_name'] );
		$last_name = stripslashes_deep( $_POST['last_name'] );
		$user_level = stripslashes_deep( $_POST['user_level'] );
		$user_notes = stripslashes_deep( $_POST['user_notes'] );
		$vat_registration_number = stripslashes_deep( $_POST['vat_registration_number'] );
		
		$is_subscriber = $exclude_tax = $exclude_shipping = 0;
		if( isset( $_POST['is_subscriber'] ) )
			$is_subscriber = 1;
		if( isset( $_POST['exclude_tax'] ) )
			$exclude_tax = 1;
		if( isset( $_POST['exclude_shipping'] ) )
			$exclude_shipping = 1;
		
		$billing_first_name = stripslashes_deep( $_POST['billing_first_name'] );
		$billing_last_name = stripslashes_deep( $_POST['billing_last_name'] );
		$billing_company_name = stripslashes_deep( $_POST['billing_company_name'] );
		$billing_address_line_1 = stripslashes_deep( $_POST['billing_address_line_1'] );
		$billing_address_line_2 = stripslashes_deep( $_POST['billing_address_line_2'] );
		$billing_city = stripslashes_deep( $_POST['billing_city'] );
		$billing_state = stripslashes_deep( $_POST['billing_state'] );
		$billing_zip = stripslashes_deep( $_POST['billing_zip'] );
		$billing_country = stripslashes_deep( $_POST['billing_country'] );
		$billing_phone = stripslashes_deep( $_POST['billing_phone'] );
		
		$shipping_first_name = stripslashes_deep( $_POST['shipping_first_name'] );
		$shipping_last_name = stripslashes_deep( $_POST['shipping_last_name'] );
		$shipping_company_name = stripslashes_deep( $_POST['shipping_company_name'] );
		$shipping_address_line_1 = stripslashes_deep( $_POST['shipping_address_line_1'] );
		$shipping_address_line_2 = stripslashes_deep( $_POST['shipping_address_line_2'] );
		$shipping_city = stripslashes_deep( $_POST['shipping_city'] );
		$shipping_state = stripslashes_deep( $_POST['shipping_state'] );
		$shipping_zip = stripslashes_deep( $_POST['shipping_zip'] );
		$shipping_country = stripslashes_deep( $_POST['shipping_country'] );
		$shipping_phone = stripslashes_deep( $_POST['shipping_phone'] );
		
		$duplicate = $wpdb->query( $wpdb->prepare( "SELECT * FROM ec_user WHERE ec_user.email = %s", $email ) );
		
		if( !$duplicate ){
			
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_user( email, password, first_name, last_name, user_level, is_subscriber, exclude_tax, exclude_shipping, user_notes, vat_registration_number ) VALUES( %s, %s, %s, %s, %s, %d, %d, %d, %s, %s )", $email, $password, $first_name, $last_name, $user_level, $is_subscriber, $exclude_tax, $exclude_shipping, $user_notes, $vat_registration_number ) );
			$user_id = $wpdb->insert_id;
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_address( user_id, first_name, last_name, company_name, address_line_1, address_line_2, city, state, zip, country, phone ) VALUES( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )", $user_id, $billing_first_name, $billing_last_name, $billing_company_name, $billing_address_line_1, $billing_address_line_2, $billing_city, $billing_state, $billing_zip, $billing_country, $billing_phone ) );
			$billing_id = $wpdb->insert_id;
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_address( user_id, first_name, last_name, company_name, address_line_1, address_line_2, city, state, zip, country, phone ) VALUES( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )", $user_id, $shipping_first_name, $shipping_last_name, $shipping_company_name, $shipping_address_line_1, $shipping_address_line_2, $shipping_city, $shipping_state, $shipping_zip, $shipping_country, $shipping_phone ) );
			$shipping_id = $wpdb->insert_id;
			$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET default_billing_address_id = %d, default_shipping_address_id = %d WHERE user_id = %d", $billing_id, $shipping_id, $user_id ) );
			
			if( $is_subscriber ){
				$wpdb->query( $wpdb->prepare( "DELETE FROM ec_subscriber WHERE ec_subscriber.email = %s", $email ) );
				$wpdb->query( $wpdb->prepare( "INSERT INTO ec_subscriber( email, first_name, last_name ) VALUES( %s, %s, %s )", $email, $first_name, $last_name ) );
			}else{
				$remove_subscriber = $wpdb->query( $wpdb->prepare( "DELETE FROM ec_subscriber WHERE email = %s", $email ) );	
			}
			
			if( function_exists( 'mymail' ) ){
				mymail( 'subscribers' )->add( array(
					'firstname' => $first_name,
					'lastname' 	=> $last_name,
					'email'		=> $email,
					'status' 	=> 1
				), false );
			}
			
			if( file_exists( "../../../../wp-easycart-quickbooks/QuickBooks.php" ) ){
				$quickbooks = new ec_quickbooks( );
				$quickbooks->add_user( $user_id );
			}
				
			do_action( 'wpeasycart_account_added', $user_id, $email, $_POST['password'] );
			
			return array( 'success' => 'user-inserted' );

		}else{
			return array( 'error' => 'user-duplicate' );
		}
	}
	
	public function update_user( ){	
		global $wpdb;
		
		$user_id = $_POST['user_id'];			
		$first_name = stripslashes_deep( $_POST['first_name'] );
		$last_name = stripslashes_deep( $_POST['last_name'] );
		$email = stripslashes_deep( $_POST['email'] );
		$user_level = stripslashes_deep( $_POST['user_level'] );
		$password = stripslashes_deep( $_POST['password'] );
		
		$user_notes = stripslashes_deep( $_POST['user_notes'] );
		$vat_registration_number = stripslashes_deep( $_POST['vat_registration_number'] );
		$is_subscriber = $exclude_tax = $exclude_shipping = 0;
		if( isset( $_POST['is_subscriber'] ) )
			$is_subscriber = 1;
		if( isset( $_POST['exclude_tax'] ) )
			$exclude_tax = 1;
		if( isset( $_POST['exclude_shipping'] ) )
			$exclude_shipping = 1;
		
		$default_billing_address_id = $_POST['default_billing_address_id'];
		$billing_first_name = stripslashes_deep( $_POST['billing_first_name'] );
		$billing_last_name = stripslashes_deep( $_POST['billing_last_name'] );
		$billing_company_name = stripslashes_deep( $_POST['billing_company_name'] );
		$billing_address_line_1 = stripslashes_deep( $_POST['billing_address_line_1'] );
		$billing_address_line_2 = stripslashes_deep( $_POST['billing_address_line_2'] );
		$billing_city = stripslashes_deep( $_POST['billing_city'] );
		$billing_state = stripslashes_deep( $_POST['billing_state'] );
		$billing_zip = stripslashes_deep( $_POST['billing_zip'] );
		$billing_country = stripslashes_deep( $_POST['billing_country'] );
		$billing_phone = stripslashes_deep( $_POST['billing_phone'] );
		
		$default_shipping_address_id = $_POST['default_shipping_address_id'];
		$shipping_first_name = stripslashes_deep( $_POST['shipping_first_name'] );
		$shipping_last_name = stripslashes_deep( $_POST['shipping_last_name'] );
		$shipping_company_name = stripslashes_deep( $_POST['shipping_company_name'] );
		$shipping_address_line_1 = stripslashes_deep( $_POST['shipping_address_line_1'] );
		$shipping_address_line_2 = stripslashes_deep( $_POST['shipping_address_line_2'] );
		$shipping_city = stripslashes_deep( $_POST['shipping_city'] );
		$shipping_state = stripslashes_deep( $_POST['shipping_state'] );
		$shipping_zip = stripslashes_deep( $_POST['shipping_zip'] );
		$shipping_country = stripslashes_deep( $_POST['shipping_country'] );
		$shipping_phone = stripslashes_deep( $_POST['shipping_phone'] );
		
		$old_email = $wpdb->get_var( $wpdb->prepare( "SELECT email FROM ec_user WHERE user_id = %d", $user_id ) );
		
		if( strtolower( $old_email ) != strtolower( $email ) ){
			$duplicate = $wpdb->query( $wpdb->prepare( "SELECT * FROM ec_user WHERE ec_user.email = %s", $email ) );
			if( $duplicate ){
				return array( 'error' => 'user-duplicate' );
			}
		}
		
		if( $default_billing_address_id == 0 ){
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_address( user_id, first_name, last_name,  company_name, address_line_1, address_line_2, city, state, zip, country, phone ) VALUES( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )", $user_id, $billing_first_name, $billing_last_name, $billing_company_name, $billing_address_line_1, $billing_address_line_2, $billing_city, $billing_state, $billing_zip, $billing_country, $billing_phone) );
			$billing_id = $wpdb->insert_id;
			$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET default_billing_address_id = %d WHERE user_id = %d", $billing_id, $user_id ) );
		
		}else{
			$wpdb->query( $wpdb->prepare( "UPDATE ec_address SET first_name = %s, last_name = %s, company_name = %s, address_line_1 = %s, address_line_2 = %s, city = %s, state = %s, zip = %s, country = %s, phone = %s WHERE address_id = %d", $billing_first_name, $billing_last_name, $billing_company_name, $billing_address_line_1, $billing_address_line_2, $billing_city, $billing_state, $billing_zip, $billing_country, $billing_phone, $default_billing_address_id ) );
			
		}
		
		if( $default_shipping_address_id == 0 ){
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_address( user_id, first_name, last_name,  company_name, address_line_1, address_line_2, city, state, zip, country, phone ) VALUES( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )", $user_id, $shipping_first_name, $shipping_last_name, $shipping_company_name, $shipping_address_line_1, $shipping_address_line_2, $shipping_city, $shipping_state, $shipping_zip, $shipping_country, $shipping_phone) );
			$shipping_id = $wpdb->insert_id;
			$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET default_shipping_address_id = %d WHERE user_id = %d", $shipping_id, $user_id ) );
		
		}else{
			$wpdb->query( $wpdb->prepare( "UPDATE ec_address SET first_name = %s, last_name = %s, company_name = %s, address_line_1 = %s, address_line_2 = %s, city = %s, state = %s, zip = %s, country = %s, phone = %s WHERE address_id = %d", $shipping_first_name, $shipping_last_name, $shipping_company_name, $shipping_address_line_1, $shipping_address_line_2, $shipping_city, $shipping_state, $shipping_zip, $shipping_country, $shipping_phone, $default_shipping_address_id ) );
			
		}
			
		if( $is_subscriber ){
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_subscriber WHERE ec_subscriber.email = %s", $email ) );
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_subscriber( email, first_name , last_name ) VALUES( %s, %s, %s )", $email, $first_name, $last_name ) );
		
		}else{
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_subscriber WHERE ec_subscriber.email = %s", $email ) );
		}
		
		if( $password == "" ){
			$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET email = %s, first_name = %s, last_name = %s, user_level = %s, is_subscriber = %d, exclude_tax = %d, exclude_shipping = %d, user_notes = %s, vat_registration_number = %s WHERE ec_user.user_id = %d", $email, $first_name, $last_name, $user_level, $is_subscriber, $exclude_tax, $exclude_shipping, $user_notes, $vat_registration_number, $user_id ) );
			
		}else{
			$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET email = %s, password = %s, first_name = %s, last_name = %s, user_level = %s, is_subscriber = %d, exclude_tax = %d, exclude_shipping = %d, user_notes = %s, vat_registration_number = %s WHERE user_id = %d", $email, md5( $password ), $first_name, $last_name, $user_level, $is_subscriber, $exclude_tax, $exclude_shipping, $user_notes, $vat_registration_number, $user_id ) );
		}
		
		if( file_exists( "../../../../wp-easycart-quickbooks/QuickBooks.php" ) ){
			$quickbooks = new ec_quickbooks( );
			$quickbooks->update_user_admin( $user_id );	
		}	
		
		do_action( 'wpeasycart_account_updated', $user_id );
		
		return array( 'success' => 'user-updated' );
	}
	
	public function login_as_user( ){
		global $wpdb;
		wpeasycart_session( )->handle_session( );
		
		$user_id = $_GET['user_id'];
		$user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_user WHERE user_id = %d", $user_id ) );
		$GLOBALS['ec_cart_data']->cart_data->user_id = $user->user_id;
		$GLOBALS['ec_cart_data']->cart_data->email = $user->email;
		$GLOBALS['ec_cart_data']->cart_data->username = $user->first_name . " " . $user->last_name;
		$GLOBALS['ec_cart_data']->cart_data->first_name = $user->first_name;
		$GLOBALS['ec_cart_data']->cart_data->last_name = $user->last_name;
		$GLOBALS['ec_cart_data']->cart_data->is_guest = "";
		$GLOBALS['ec_cart_data']->cart_data->guest_key = "";
		$GLOBALS['ec_cart_data']->save_session_to_db( );
		
		wp_cache_flush( );
		do_action( 'wpeasycart_login_success', $user->email );
		
		return array( 'ec_admin_form_action' => 'edit', 'user_id' => $user_id, 'success' => 'user-logged-in' );
	}
	
	public function delete_user( ){
		global $wpdb;
		$user_id = $_GET['user_id'];
		$wpdb->query( $wpdb->prepare( "DELETE FROM ec_address WHERE user_id = %d", $user_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ec_user WHERE user_id = %d", $user_id ) );
		return array( 'success' => 'user-deleted' );
	}
	
	public function bulk_delete_user( ){
		global $wpdb;
		
		$bulk_ids = $_GET['bulk'];
		foreach( $bulk_ids as $bulk_id ){
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_address WHERE user_id = %d", $bulk_id ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_user WHERE user_id = %d", $bulk_id ) );
		}
		
		return array( 'success' => 'user-deleted' );
	}
	
	public function bulk_force_password_reset( ){
		global $wpdb;
		
		$bulk_ids = $_GET['bulk'];
		foreach( $bulk_ids as $bulk_id ){
			$user = $wpdb->get_row( $wpdb->prepare( "SELECT email, first_name, last_name FROM ec_user WHERE user_id = %d", $bulk_id ) );
			if( $user ){
				$new_password = $this->get_random_password( );
				$password = md5( $new_password );
				$password = apply_filters( 'wpeasycart_password_hash', $password, $new_password );
				$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET password = %s WHERE user_id = %d", $password, $bulk_id ) );
				$this->send_new_password_email( $user, $new_password );
			}
		}
		
		return array( 'success' => 'user-password-reset' );
	}
	
	private function send_new_password_email( $user, $new_password ){
		
		$email = $user->email;
		$email_logo_url = get_option( 'ec_option_email_logo' );
	 	
		// Get receipt
		ob_start();
        if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_account_retrieve_password_email.php' ) )	
			include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_account_retrieve_password_email.php' );	
		else
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_account_retrieve_password_email.php' );
		$message = ob_get_contents();
		ob_end_clean();
		
		$headers   = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-Type: text/html; charset=utf-8";
		$headers[] = "From: " . stripslashes( get_option( 'ec_option_password_from_email' ) );
		$headers[] = "Reply-To: " . stripslashes( get_option( 'ec_option_password_from_email' ) );
		$headers[] = "X-Mailer: PHP/" . phpversion( );
		
		$email_send_method = get_option( 'ec_option_use_wp_mail' );
		$email_send_method = apply_filters( 'wpeasycart_email_method', $email_send_method );
		
		if( $email_send_method == "1" ){
			wp_mail( $email, $GLOBALS['language']->get_text( "account_forgot_password_email", "account_forgot_password_email_title" ), $message, implode("\r\n", $headers));
		
		}else if( $email_send_method == "0" ){
			$to = $email;
			$subject = $GLOBALS['language']->get_text( "account_forgot_password_email", "account_forgot_password_email_title" );
			$mailer = new wpeasycart_mailer( );
			$mailer->send_customer_email( $to, $subject, $message );
		
		}else{
			do_action( 'wpeasycart_custom_forgot_password_email', stripslashes( get_option( 'ec_option_password_from_email' ) ), $email, "", $GLOBALS['language']->get_text( "account_forgot_password_email", "account_forgot_password_email_title" ), $message );
			
		}
		
	}
	
	private function get_random_password( ){
		$rand_chars = array( "A", "B", "C", "D", "E", "F", "G", "H", "I", "J" );
		$rand_password = $rand_chars[ rand( 0, 9 ) ] . $rand_chars[ rand( 0, 9 ) ] . $rand_chars[ rand( 0, 9 ) ] . $rand_chars[ rand( 0, 9 ) ] . rand( 0, 9 ) . rand( 0, 9 ) . rand( 0, 9 ) . rand( 0, 9 ) . rand( 0, 9 );
		return $rand_password;
	}
	
	public function check_existing_email( ) {
		global $wpdb;
		if( isset( $_POST['email'] ) ){
			 $email = $_POST['email'];
			 $user_id = $_POST['user_id'];
			 $emails = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ec_user WHERE ec_user.email = %s AND ec_user.user_id != %d", $email, $user_id ) );

			 if( count( $emails ) > 0 )
			  	echo "Email Already Exist";
			 else
			  	echo "OK";
		}
	}
	
	public function run_importer() {
		
		global $wpdb;
		$error_list = "";
		$email_index = -1;
		$first_name_index = -1;
		$last_name_index = -1;
		$user_level_index = -1;
		
		// Billing Fields
		$billing_first_name_index = -1;
		$billing_last_name_index = -1;
		$billing_company_name_index = -1;
		$billing_address_line_1_index = -1;
		$billing_address_line_2_index = -1;
		$billing_city_index = -1;
		$billing_state_index = -1;
		$billing_zip_index = -1;
		$billing_country_index = -1;
		$billing_phone_index = -1;
		
		// Shipping Fields
		$shipping_first_name_index = -1;
		$shipping_last_name_index = -1;
		$shipping_company_name_index = -1;
		$shipping_address_line_1_index = -1;
		$shipping_address_line_2_index = -1;
		$shipping_city_index = -1;
		$shipping_state_index = -1;
		$shipping_zip_index = -1;
		$shipping_country_index = -1;
		$shipping_phone_index = -1;
		
		$limit = 20;
		
		ini_set("auto_detect_line_endings", "1");	
		
		require_once( "Encoding.php" );
		
		if( $_POST['import_file_url'] ) {
			
			set_time_limit( 500 );
			
			if( !( $file = fopen( $_POST['import_file_url'] , "r" ) ) ){
				$url_parts = parse_url($_POST['import_file_url']);
				if( !$file = fopen( substr( get_home_path( ), -1 ) . $url_parts['path'], 'r' ) ){
                    if( !$file = fopen( '../' . substr( get_home_path( ), -1 ) . $url_parts['path'], 'r' ) ){
                        $file = tmpfile( );
                        $ch = curl_init( );
                        curl_setopt( $ch, CURLOPT_URL, $_POST['import_file_url'] );
                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 15 );
                        $result = curl_exec( $ch );
                        curl_close( $ch );
                        fwrite( $file, $result ); 
                        rewind( $file );
                    }
				}
			}
			
			/* Setup and test headers */
			$valid_headers_result = $wpdb->get_results( "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`='ec_user'", ARRAY_N );
			$valid_headers = array( );
			foreach( $valid_headers_result as $header ){
				$valid_headers[] = $header[0];
			}
			$headers = fgetcsv( $file );
			
			for( $i=0; $i<count( $headers ); $i++ ){
				
				$headers[$i] = trim( $headers[$i] );
				
				if($headers[$i] == "email" ){ // use to check for errors
					$email_index = $i;
				
				}else if($headers[$i] == "user_level" ){ // use to check for errors
					$user_level_index = $i;
				
				}else if($headers[$i] == "first_name" ){ // use to check for errors
					$first_name_index = $i;
				
				}else if($headers[$i] == "last_name" ){ // use to check for errors
					$last_name_index = $i;
				
				}else if($headers[$i] == "billing_first_name" ){ // use to check for errors
					$billing_first_name_index = $i;
				
				}else if($headers[$i] == "billing_last_name" ){ // use to check for errors
					$billing_last_name_index = $i;
				
				}else if($headers[$i] == "billing_company_name" ){ // use to check for errors
					$billing_company_name_index = $i;
				
				}else if($headers[$i] == "billing_address_line_1" ){ // use to check for errors
					$billing_address_line_1_index = $i;
				
				}else if($headers[$i] == "billing_address_line_2" ){ // use to check for errors
					$billing_address_line_2_index = $i;
				
				}else if($headers[$i] == "billing_city" ){ // use to check for errors
					$billing_city_index = $i;
				
				}else if($headers[$i] == "billing_state" ){ // use to check for errors
					$billing_state_index = $i;
				
				}else if($headers[$i] == "billing_zip" ){ // use to check for errors
					$billing_zip_index = $i;
				
				}else if($headers[$i] == "billing_country" ){ // use to check for errors
					$billing_country_index = $i;
				
				}else if($headers[$i] == "billing_phone" ){ // use to check for errors
					$billing_phone_index = $i;
				
				}else if($headers[$i] == "shipping_first_name" ){ // use to check for errors
					$shipping_first_name_index = $i;
				
				}else if($headers[$i] == "shipping_last_name" ){ // use to check for errors
					$shipping_last_name_index = $i;
				
				}else if($headers[$i] == "shipping_company_name" ){ // use to check for errors
					$shipping_company_name_index = $i;
				
				}else if($headers[$i] == "shipping_address_line_1" ){ // use to check for errors
					$shipping_address_line_1_index = $i;
				
				}else if($headers[$i] == "shipping_address_line_2" ){ // use to check for errors
					$shipping_address_line_2_index = $i;
				
				}else if($headers[$i] == "shipping_city" ){ // use to check for errors
					$shipping_city_index = $i;
				
				}else if($headers[$i] == "shipping_state" ){ // use to check for errors
					$shipping_state_index = $i;
				
				}else if($headers[$i] == "shipping_zip" ){ // use to check for errors
					$shipping_zip_index = $i;
				
				}else if($headers[$i] == "shipping_country" ){ // use to check for errors
					$shipping_country_index = $i;
				
				}else if($headers[$i] == "shipping_phone" ){ // use to check for errors
					$shipping_phone_index = $i;
				
				}else if( !in_array( $headers[$i], $valid_headers ) ){ // error, invalid column
					
                    if( $headers[$i] != 'billing_address_id' && $headers[$i] != 'billing_user_id' && $headers[$i] != 'shipping_address_id' && $headers[$i] != 'shipping_user_id' ){
					   echo "You have an invalid column header at column " . $i . " (value " . $headers[$i] . "), please remove or correct the label of that column to continue.";
                    }
                    
				}
				
			}
			
			if( $email_index == -1 ){
				echo __( "Missing `email` Key field! Unique values are required.", 'wp-easycart' );
			}
			
			if( $first_name_index == -1 ){
				echo __( "Missing `first_name` Key field! Some value is required.", 'wp-easycart' );
			}
			
			if( $last_name_index == -1 ){
				echo __( "Missing `last_name` Key field! Some value is required.", 'wp-easycart' );
			}
			
			/* SETUP basic SQL calls */
			$insert_user_sql = "INSERT INTO ec_user( `email`, `password`, `first_name`, `last_name`, `user_level` ) VALUES( %s, %s, %s, %s, %s)";
			$insert_address_sql = "INSERT INTO ec_address( `user_id`, `first_name`, `last_name`, `company_name`, `address_line_1`, `address_line_2`, `city`, `state`, `zip`, `country`, `phone` ) VALUES( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )";
			
			/* Start through the rows */
			$current_iteration = 0;
			$eof_reached = false;
			
			while( !feof( $file ) && !$eof_reached ){ // each time through, run up to the limit of items until eof hit.
				
				$rows = array( );
			
				for( $current_row = 0; !feof( $file ) && !$eof_reached && $current_row < $limit; $current_row++ ){
			
					$this_row = fgetcsv( $file );
				
					if( strlen( trim( $this_row[$email_index] ) ) <= 0 ){ // checking for file with extra rows that are empty
						$eof_reached = true;
					
					}else{
						$rows[] = $this_row;
					
					}
					
				}
				
				/* Start processing of rows collected in this interation */
				for( $i=0; $i<count( $rows ); $i++ ){
						
					// Insert User
					$wpdb->query( $wpdb->prepare( $insert_user_sql, $rows[$i][$email_index], md5( rand( 999999999999, 999999999999999 ) ), $rows[$i][$first_name_index], $rows[$i][$last_name_index], ( ( $user_level_index != -1 && $rows[$i][$user_level_index] != '' ) ? $rows[$i][$user_level_index] : 'shopper' ) ) );
					$user_id = $wpdb->insert_id;
					$billing_address_id = $shipping_address_id = 0;
					
					// If Successful, Insert Addresses
					if( $user_id ){
						$wpdb->query( $wpdb->prepare( 
							$insert_address_sql,
							$user_id,
							( ( $billing_first_name_index != -1 ) ? $rows[$i][$billing_first_name_index] : '' ),
							( ( $billing_last_name_index != -1 ) ? $rows[$i][$billing_last_name_index] : '' ),
							( ( $billing_company_name_index != -1 ) ? $rows[$i][$$billing_company_name_index] : '' ),
							( ( $billing_address_line_1_index != -1 ) ? $rows[$i][$billing_address_line_1_index] : '' ),
							( ( $billing_address_line_2_index != -1 ) ? $rows[$i][$billing_address_line_2_index] : '' ),
							( ( $billing_city_index != -1 ) ? $rows[$i][$billing_city_index] : '' ),
							( ( $billing_state_index != -1 ) ? $rows[$i][$billing_state_index] : '' ),
							( ( $billing_zip_index != -1 ) ? $rows[$i][$billing_zip_index] : '' ),
							( ( $billing_country_index != -1 ) ? $rows[$i][$billing_country_index] : '' ),
							( ( $billing_phone_index != -1 ) ? $rows[$i][$billing_phone_index] : '' )
						) );
						$billing_address_id = $wpdb->insert_id;
						$wpdb->query( $wpdb->prepare( 
							$insert_address_sql,
							$user_id,
							( ( $shipping_first_name_index != -1 ) ? $rows[$i][$shipping_first_name_index] : '' ),
							( ( $shipping_last_name_index != -1 ) ? $rows[$i][$shipping_last_name_index] : '' ),
							( ( $shipping_company_name_index != -1 ) ? $rows[$i][$$shipping_company_name_index] : '' ),
							( ( $shipping_address_line_1_index != -1 ) ? $rows[$i][$shipping_address_line_1_index] : '' ),
							( ( $shipping_address_line_2_index != -1 ) ? $rows[$i][$shipping_address_line_2_index] : '' ),
							( ( $shipping_city_index != -1 ) ? $rows[$i][$shipping_city_index] : '' ),
							( ( $shipping_state_index != -1 ) ? $rows[$i][$shipping_state_index] : '' ),
							( ( $shipping_zip_index != -1 ) ? $rows[$i][$shipping_zip_index] : '' ),
							( ( $shipping_country_index != -1 ) ? $rows[$i][$shipping_country_index] : '' ),
							( ( $shipping_phone_index != -1 ) ? $rows[$i][$shipping_phone_index] : '' )
						) );
						$shipping_address_id = $wpdb->insert_id;
						$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET default_billing_address_id = %d, default_shipping_address_id = %d WHERE user_id = %d", $billing_address_id, $shipping_address_id, $user_id ) );
					}
					
				} // Close iteration for loop
				
				unset( $rows );
				
				$current_iteration++;
				
			}
			
			unset( $headers );
			
			fclose( $file );
			
			if( $error_list == "" ){
				echo "success" ;
			}else{
				echo $error_list;
			}
			
			
		} else {
			echo 'No URL';
		}
		die( );
	}
	
}
endif; // End if class_exists check

function wp_easycart_admin_users( ){
	return wp_easycart_admin_users::instance( );
}
wp_easycart_admin_users( );

/* Hooks for ajax email check */
add_action( 'wp_ajax_ec_admin_check_email_exists', 'ec_admin_check_email_exists' );
function ec_admin_check_email_exists( ){
	$users = new wp_easycart_admin_users( );
	$users->check_existing_email( );
	die( );

}

add_action( 'wp_ajax_ec_admin_ajax_import_users', 'ec_admin_ajax_import_users' );
function ec_admin_ajax_import_users( ){
	$import_results = wp_easycart_admin_users( )->run_importer();
	//echo $import_results;
	die();
}