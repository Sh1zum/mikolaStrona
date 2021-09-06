<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_option' ) ) :

final class wp_easycart_admin_option{
	
	protected static $_instance = null;
	
	public $option_list_file;
	public $product_list_file;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
	
	public function __construct( ){ 
		$this->option_list_file 			= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/options/option-list.php';
		$this->optionitem_list_file 		= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/options/optionitem-list.php';
		
		/* Process Admin Messages */
		add_filter( 'wp_easycart_admin_success_messages', array( $this, 'add_success_messages' ) );
		add_filter( 'wp_easycart_admin_error_messages', array( $this, 'add_failure_messages' ) );
		
		/* Process Form Actions */
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_add_new_optionitem' ) );
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_update_optionitem' ) );
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_add_new_option' ) );
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_update_option' ) );
		
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_duplicate_option' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_duplicate_optionitem' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_delete_optionitem' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_delete_optionitem' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_delete_option' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_delete_option' ) );
	}
	
	public function process_add_new_optionitem( ){
		if( $_POST['ec_admin_form_action'] == "add-new-optionitem" ){
			$result = $this->insert_optionitem( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'optionitems', $result );
		}
	}
	
	public function process_update_optionitem( ){
		if( $_POST['ec_admin_form_action'] == "update-optionitem" ){
			$result = $this->update_optionitem( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'optionitems', $result );
		}
	}
	
	public function process_add_new_option( ){
		if( $_POST['ec_admin_form_action'] == "add-new-option" ){
			$result = $this->insert_option( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'option', $result );
		}
	}
	
	public function process_update_option( ){
		if( $_POST['ec_admin_form_action'] == "update-option" ){
			$result = $this->update_option( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'option', $result );
		}
	}
	
	public function process_duplicate_option( ){
		if( isset( $_GET['subpage'] ) == 'option' && $_GET['ec_admin_form_action'] == 'duplicate-option' && isset( $_GET['option_id'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->duplicate_option( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'option', $result );
		}
	}
	
	public function process_duplicate_optionitem( ){
		if( isset($_GET['subpage']) == 'option' && $_GET['ec_admin_form_action'] == 'duplicate-optionitem' && isset( $_GET['optionitem_id'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->duplicate_optionitem( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'optionitems', $result );
		}
	}
	
	public function process_delete_optionitem( ){
		if( isset($_GET['subpage']) == 'option' && $_GET['ec_admin_form_action'] == 'delete-optionitem' && isset( $_GET['optionitem_id'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->delete_optionitem( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'optionitems', $result );
		}
	}
	
	public function process_bulk_delete_optionitem( ){
		if( isset($_GET['subpage']) == 'option' && $_GET['ec_admin_form_action'] == 'delete-optionitem' && !isset( $_GET['optionitem_id'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_delete_optionitem( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'optionitems', $result );
		}
	}
	
	public function process_delete_option( ){
		if( isset($_GET['subpage']) == 'option' && $_GET['ec_admin_form_action'] == 'delete-option' && isset( $_GET['option_id'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->delete_option( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'option', $result );
		}
	}
	
	public function process_bulk_delete_option( ){
		if( isset($_GET['subpage']) == 'option' && $_GET['ec_admin_form_action'] == 'delete-option' && !isset( $_GET['option_id'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_delete_option( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'option', $result );
		}
	}
	
	public function add_success_messages( $messages ){
		if( isset( $_GET['success'] ) && $_GET['success'] == 'option-inserted' ){
			$messages[] = __( 'Option successfully created', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'option-updated' ){
			$messages[] = __( 'Option successfully updated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'option-deleted' ){
			$messages[] = __( 'Option successfully deleted', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'option-item-inserted' ){
			$messages[] = __( 'Option item successfully created', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'option-item-updated' ){
			$messages[] = __( 'Option item successfully updated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'option-item-deleted' ){
			$messages[] = __( 'Option item successfully deleted', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'option-item-duplicated' ){
			$messages[] = __( 'Option item successfully duplicated', 'wp-easycart' );
		}
		return $messages;
	}
	
	public function add_failure_messages( $messages ){
		if( isset( $_GET['error'] ) && $_GET['error'] == 'option-inserted-error' ){
			$messages[] = __( 'Option failed to create', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'option-updated-error' ){
			$messages[] = __( 'Option failed to update', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'option-deleted-error' ){
			$messages[] = __( 'Option failed to delete', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'option-duplicate' ){
			$messages[] = __( 'Option failed to create due to duplicate', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'option-item-duplicate' ){
			$messages[] = __( 'Option item failed to create due to duplicate', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'option-item-inserted-error' ){
			$messages[] = __( 'Option item failed to create', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'option-item-deleted-error' ){
			$messages[] = __( 'Option item failed to delete', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'option-item-duplicate-error' ){
			$messages[] = __( 'Option item failed to duplicate', 'wp-easycart' );
		}
		return $messages;
	}
	
	public function load_option_list( ){
		if( ( isset( $_GET['option_id'] ) && isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'edit' ) || 
			( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new-option' ) ){
			
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/inc/wp_easycart_admin_details_option.php' );
			$details = new wp_easycart_admin_details_option( );
			$details->output( esc_attr( $_GET['ec_admin_form_action'] ) );
		
		}else{
			include( $this->option_list_file );
			
		}
	}
	
	public function load_optionitem_list( ){
		if( ( isset( $_GET['optionitem_id'] ) && isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'edit' ) ||
			( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new-optionitem' ) ){
			
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/inc/wp_easycart_admin_details_optionitem.php' );
			$details = new wp_easycart_admin_details_optionitem( );
			$details->output( esc_attr( $_GET['ec_admin_form_action'] ) );
				
		}else{
			include( $this->optionitem_list_file );

		}
	}
	
	/******************************
	* Options
	******************************/
	public function insert_option( ){
		global $wpdb;
		
		$option_name = stripslashes_deep( $_POST['option_name'] );
		$option_label = stripslashes_deep( $_POST['option_label'] );
		$option_type = $_POST['option_type'];
		$option_error_text = stripslashes_deep( $_POST['option_error_text'] );
		$url_var = preg_replace( "/[^a-zA-Z0-9\_]+/", "", $_POST['option_meta_url_var'] );
		$url_var = preg_replace( "/^[^a-zA-Z]+/", "", $url_var );
		$option_meta = array(
			"min"	=> $_POST['option_meta_min'],
			"max"	=> $_POST['option_meta_max'],
			"step"	=> $_POST['option_meta_step'],
			"url_var"	=> $url_var
		);
		$option_required = 0;
		if( isset( $_POST['option_required'] ) || $option_type == 'basic-swatch' || $option_type == 'basic-combo' )
			$option_required = 1;
		
		$result = $wpdb->query( $wpdb->prepare( "INSERT INTO ec_option( option_name, option_label, option_type, option_required, option_error_text, option_meta ) VALUES( %s, %s, %s, %d, %s, %s )", $option_name, $option_label, $option_type, $option_required, $option_error_text, maybe_serialize( $option_meta ) ) );
		$option_id = $wpdb->insert_id;
		
		if( $option_type == 'file' || $option_type == 'text' || $option_type == 'number' || $option_type == 'textarea' || $option_type == 'date'  || $option_type == 'dimensions1'  || $option_type == 'dimensions2' ){
		  	if ($option_type == 'file') 			$option_name = 'File Field';
			if ($option_type == 'text') 			$option_name = 'Text Box Input';
			if ($option_type == 'number') 			$option_name = 'Number Box Input';
			if ($option_type == 'textarea') 		$option_name = 'Text Area Input';
			if ($option_type == 'date') 			$option_name = 'Date Field';
			if ($option_type == 'dimensions1') 		$option_name = 'DimensionType1';
			if ($option_type == 'dimensions2') 		$option_name = 'DimensionType2'; 
			
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_optionitem( option_id, optionitem_name, optionitem_price, optionitem_price_onetime, optionitem_price_override, optionitem_weight, optionitem_weight_onetime, optionitem_weight_override, optionitem_order, optionitem_icon, optionitem_initial_value ) VALUES( %d, %s, '0.00', '0.00', '-1', '0.00', '0.00', '-1.00', 1, '', '' )", $option_id, $option_name ) );
		}
		
		return array( 'success' => 'option-inserted' );
	}
	
	
	public function update_option( ){	
		global $wpdb;
		
		$option_id = $_POST['option_id'];			
		$option_name = stripslashes_deep( $_POST['option_name'] );
		$option_label = stripslashes_deep( $_POST['option_label'] );
		$option_type = $_POST['option_type'];
		$option_error_text = stripslashes_deep( $_POST['option_error_text'] );
		$url_var = preg_replace( "/[^a-zA-Z0-9\_]+/", "", $_POST['option_meta_url_var'] );
		$url_var = preg_replace( "/^[^a-zA-Z]+/", "", $url_var );
		$option_meta = array(
			"min"	=> $_POST['option_meta_min'],
			"max"	=> $_POST['option_meta_max'],
			"step"	=> $_POST['option_meta_step'],
			"url_var"	=> $url_var
		);
		$option_required = 0;
		if( isset( $_POST['option_required'] ) || $option_type == 'basic-swatch' || $option_type == 'basic-combo' )
			$option_required = 1;
		
		$wpdb->query( $wpdb->prepare( "UPDATE ec_option SET option_id = %s, option_name = %s, option_label = %s, option_type = %s, option_required = %s, option_error_text = %s, option_meta = %s WHERE option_id = %s", $option_id, $option_name, $option_label, $option_type, $option_required, $option_error_text, maybe_serialize( $option_meta ), $option_id) );
		
		if( $option_type == 'file' || $option_type == 'text' || $option_type == 'number' || $option_type == 'textarea' || $option_type == 'date'  || $option_type == 'dimensions1'  || $option_type == 'dimensions2' ){
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_optionitem WHERE option_id = %d", $option_id ) );
			
			if ($option_type == 'file') 			$option_name = 'File Field';
			if ($option_type == 'text') 			$option_name = 'Text Box Input';
			if ($option_type == 'number') 			$option_name = 'Number Box Input';
			if ($option_type == 'textarea') 		$option_name = 'Text Area Input';
			if ($option_type == 'date') 			$option_name = 'Date Field';
			if ($option_type == 'dimensions1') 		$option_name = 'DimensionType1';
			if ($option_type == 'dimensions2') 		$option_name = 'DimensionType2'; 
			
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_optionitem( option_id, optionitem_name, optionitem_price, optionitem_price_onetime, optionitem_price_override, optionitem_weight, optionitem_weight_onetime, optionitem_weight_override, optionitem_order, optionitem_icon, optionitem_initial_value ) VALUES( %d, %s, '0.00', '0.00', '-1', '0.00', '0.00', '-1.00', 1, '', '' )", $option_id, $option_name ) );
		}
		
		return array( 'success' => 'option-updated' );	
	}
	
	public function duplicate_option( ){
		global $wpdb;
		$option_id = $_GET['option_id'];
		
		$optionitems = $wpdb->get_results( $wpdb->prepare( "SELECT optionitem_id FROM ec_optionitem WHERE option_id = %d", $option_id ) );
		
		$wpdb->query( "DROP TEMPORARY TABLE IF EXISTS ec_temporary" );
		$wpdb->query( $wpdb->prepare( "CREATE TEMPORARY TABLE ec_temporary SELECT ec_option.* FROM ec_option WHERE ec_option.option_id = %d", $option_id ) );
		$wpdb->query( "UPDATE ec_temporary SET option_id = NULL" );
		$wpdb->query( "INSERT INTO ec_option SELECT ec_temporary.* FROM ec_temporary" );
		$option_id_new = $wpdb->insert_id;
		$wpdb->query( "DROP TEMPORARY TABLE IF EXISTS ec_temporary" );
		
		foreach( $optionitems as $optionitem ){
			$wpdb->query( "DROP TEMPORARY TABLE IF EXISTS ec_temporary" );
			$wpdb->query( $wpdb->prepare( "CREATE TEMPORARY TABLE ec_temporary SELECT ec_optionitem.* FROM ec_optionitem WHERE ec_optionitem.optionitem_id = %d", $optionitem->optionitem_id ) );
			$wpdb->query( "UPDATE ec_temporary SET optionitem_id = NULL" );
			$wpdb->query( "INSERT INTO ec_optionitem SELECT ec_temporary.* FROM ec_temporary" );
			$optionitem_id = $wpdb->insert_id;
			$wpdb->query( "DROP TEMPORARY TABLE IF EXISTS ec_temporary" );
			$wpdb->query( $wpdb->prepare( "UPDATE ec_optionitem SET option_id = %d WHERE optionitem_id = %d", $option_id_new, $optionitem_id ) );
		}
		
		$args = array( 'success' => 'option-duplicated' );
		
		if( isset( $_GET['pagenum'] ) )
			$args['pagenum'] = (int) $_GET['pagenum'];
			
		if( isset( $_GET['orderby'] ) )
			$args['orderby'] = esc_attr( $_GET['orderby'] );
			
		if( isset( $_GET['order'] ) )
			$args['order'] = esc_attr( $_GET['order'] );
			
		return $args;
	}
	
	public function delete_option( ){
		global $wpdb;
		
		$option_id = $_GET['option_id'];		
		
		$wpdb->query( $wpdb->prepare( "DELETE FROM ec_option WHERE ec_option.option_id = %d", $option_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ec_optionitem WHERE ec_optionitem.option_id = %d", $option_id ) );
	
		$args = array( 'success' => 'option-deleted' );
		
		if( isset( $_GET['pagenum'] ) )
			$args['pagenum'] = (int) $_GET['pagenum'];
			
		if( isset( $_GET['orderby'] ) )
			$args['orderby'] = esc_attr( $_GET['orderby'] );
			
		if( isset( $_GET['order'] ) )
			$args['order'] = esc_attr( $_GET['order'] );
			
		return $args;
	}
	
	public function bulk_delete_option( ){
		global $wpdb;
		$bulk_ids = $_GET['bulk'];
		
		foreach( $bulk_ids as $bulk_id ){
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_option WHERE option_id = %d", $bulk_id ) );
			$wpdb->query( $wpdb->prepare(  "DELETE FROM ec_optionitem WHERE option_id = %d", $bulk_id ) );
		}
		
		$args = array( 'success' => 'option-deleted' );
		
		if( isset( $_GET['pagenum'] ) )
			$args['pagenum'] = (int) $_GET['pagenum'];
			
		if( isset( $_GET['orderby'] ) )
			$args['orderby'] = esc_attr( $_GET['orderby'] );
			
		if( isset( $_GET['order'] ) )
			$args['order'] = esc_attr( $_GET['order'] );
			
		return $args;
	}	
	
	/***************************************
	* Option Items
	***************************************/
	public function duplicate_optionitem( ){
		global $wpdb;
		
		$optionitem_id = $_GET['optionitem_id'];
		$args = array( );
		
		$option_id = $wpdb->get_var( $wpdb->prepare( "SELECT option_id FROM ec_optionitem WHERE optionitem_id = %d", $optionitem_id ) );
		$last_optionitem_sort = $wpdb->get_var( $wpdb->prepare( "SELECT optionitem_order FROM ec_optionitem WHERE option_id = %d ORDER BY optionitem_order DESC", $option_id ) );
		$wpdb->query( "DROP TEMPORARY TABLE IF EXISTS ec_temporary" );
		$wpdb->query( $wpdb->prepare( "CREATE TEMPORARY TABLE ec_temporary SELECT ec_optionitem.* FROM ec_optionitem WHERE ec_optionitem.optionitem_id = %d", $optionitem_id ) );
		$wpdb->query( $wpdb->prepare( "UPDATE ec_temporary SET optionitem_id = NULL, optionitem_order = %d", $last_optionitem_sort + 1 ) );
		$result = $wpdb->query( "INSERT INTO ec_optionitem SELECT ec_temporary.* FROM ec_temporary" );
		$wpdb->query( "DROP TEMPORARY TABLE IF EXISTS ec_temporary" );

		$args['option_id'] = esc_attr( $option_id );
		$args['ec_admin_form_action'] = 'edit-optionitem';
		if( count( $result ) > 0 )
			$args['success'] = 'option-item-duplicated';
		else
			$args['error'] = 'option-item-duplicate-error';
		
		if( isset( $_GET['pagenum'] ) )
			$args['pagenum'] = (int) $_GET['pagenum'];
			
		if( isset( $_GET['orderby'] ) )
			$args['orderby'] = esc_attr( $_GET['orderby'] );
			
		if( isset( $_GET['order'] ) )
			$args['order'] = esc_attr( $_GET['order'] );
			
		return $args;
	}
	
	public function insert_optionitem( ){
		global $wpdb;
				
		$option_id = $_POST['option_id'];	
		$optionitem_name = stripslashes_deep( $_POST['optionitem_name'] );	
		$optionitem_price = $_POST['optionitem_price'];	
		$optionitem_price_onetime = $_POST['optionitem_price_onetime'];	
		$optionitem_price_override = $_POST['optionitem_price_override'];	
		$optionitem_price_multiplier = $_POST['optionitem_price_multiplier'];	
		$optionitem_price_per_character = $_POST['optionitem_price_per_character'];	
		$optionitem_weight = $_POST['optionitem_weight'];	
		$optionitem_weight_onetime = $_POST['optionitem_weight_onetime'];	
		$optionitem_weight_override = $_POST['optionitem_weight_override'];	
		$optionitem_weight_multiplier = $_POST['optionitem_weight_multiplier'];	
		$optionitem_order = $_POST['optionitem_order'];	
		$optionitem_icon = '';
		if( isset( $_POST['optionitem_icon'] ) )
			$optionitem_icon = stripslashes_deep( $_POST['optionitem_icon'] );
		$optionitem_initial_value = $_POST['optionitem_initial_value'];	
		$optionitem_model_number = $_POST['optionitem_model_number'];	
		$optionitem_allow_download = $optionitem_disallow_shipping = $optionitem_initially_selected = 0;
		if( isset( $_POST['optionitem_allow_download'] ) )
			$optionitem_allow_download = 1;
		if( isset( $_POST['optionitem_disallow_shipping'] ) )
			$optionitem_disallow_shipping = 1;
		if( isset( $_POST['optionitem_initially_selected'] ) )
			$optionitem_initially_selected = 1;
		$optionitem_download_override_file = $_POST['optionitem_download_override_file'];
		$optionitem_download_addition_file = $_POST['optionitem_download_addition_file'];
		
		$result = $wpdb->query( $wpdb->prepare( "INSERT INTO ec_optionitem( option_id, optionitem_name, optionitem_price, optionitem_price_onetime, optionitem_price_override, optionitem_price_multiplier, optionitem_price_per_character, optionitem_weight, optionitem_weight_onetime, optionitem_weight_override, optionitem_weight_multiplier, optionitem_order, optionitem_icon, optionitem_initial_value, optionitem_model_number, optionitem_allow_download, optionitem_disallow_shipping, optionitem_initially_selected, optionitem_download_override_file, optionitem_download_addition_file ) VALUES( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s, %d, %d, %d, %s, %s )", $option_id, $optionitem_name, $optionitem_price, $optionitem_price_onetime, $optionitem_price_override, $optionitem_price_multiplier, $optionitem_price_per_character, $optionitem_weight, $optionitem_weight_onetime, $optionitem_weight_override, $optionitem_weight_multiplier, $optionitem_order, $optionitem_icon, $optionitem_initial_value, $optionitem_model_number, $optionitem_allow_download, $optionitem_disallow_shipping, $optionitem_initially_selected, $optionitem_download_override_file, $optionitem_download_addition_file ) );
		
		$args = array(
			'success' => 'option-item-inserted',
			'option_id' => esc_attr( $option_id )
		);
		
		if( isset( $_GET['pagenum'] ) )
			$args['pagenum'] = (int) $_GET['pagenum'];
			
		if( isset( $_GET['orderby'] ) )
			$args['orderby'] = esc_attr( $_GET['orderby'] );
			
		if( isset( $_GET['order'] ) )
			$args['order'] = esc_attr( $_GET['order'] );
			
		return $args;
	}
	
	public function update_optionitem( ){
		global $wpdb;
			
		$optionitem_id = $_POST['optionitem_id'];	
		$option_id = $_POST['option_id'];	
		$optionitem_name = stripslashes_deep( $_POST['optionitem_name'] );	
		$optionitem_price = $_POST['optionitem_price'];	
		$optionitem_price_onetime = $_POST['optionitem_price_onetime'];	
		$optionitem_price_override = $_POST['optionitem_price_override'];	
		$optionitem_price_multiplier = $_POST['optionitem_price_multiplier'];	
		$optionitem_price_per_character = $_POST['optionitem_price_per_character'];	
		$optionitem_weight = $_POST['optionitem_weight'];	
		$optionitem_weight_onetime = $_POST['optionitem_weight_onetime'];	
		$optionitem_weight_override = $_POST['optionitem_weight_override'];	
		$optionitem_weight_multiplier = $_POST['optionitem_weight_multiplier'];	
		$optionitem_order = $_POST['optionitem_order'];	
		$optionitem_icon = '';
		if( isset( $_POST['optionitem_icon'] ) )
			$optionitem_icon = stripslashes_deep( $_POST['optionitem_icon'] );
		$optionitem_initial_value = $_POST['optionitem_initial_value'];	
		$optionitem_model_number = $_POST['optionitem_model_number'];	
		$optionitem_allow_download = $optionitem_disallow_shipping = $optionitem_initially_selected = 0;
		if( isset( $_POST['optionitem_allow_download'] ) )
			$optionitem_allow_download = 1;
		if( isset( $_POST['optionitem_disallow_shipping'] ) )
			$optionitem_disallow_shipping = 1;
		if( isset( $_POST['optionitem_initially_selected'] ) )
			$optionitem_initially_selected = 1;
		$optionitem_download_override_file = $_POST['optionitem_download_override_file'];
		$optionitem_download_addition_file = $_POST['optionitem_download_addition_file'];
		
		$wpdb->query( $wpdb->prepare( "UPDATE ec_optionitem SET optionitem_id = %d, option_id = %d, optionitem_name = %s, optionitem_price = %s, optionitem_price_onetime = %s, optionitem_price_override = %s, optionitem_price_multiplier = %s, optionitem_price_per_character = %s, optionitem_weight = %s, optionitem_weight_onetime = %s, optionitem_weight_override = %s, optionitem_weight_multiplier = %s, optionitem_order = %d, optionitem_icon = %s, optionitem_initial_value = %s, optionitem_model_number = %s, optionitem_allow_download = %d, optionitem_disallow_shipping = %d, optionitem_initially_selected = %d, optionitem_download_override_file = %s, optionitem_download_addition_file = %s WHERE optionitem_id = %d", $optionitem_id, $option_id, $optionitem_name, $optionitem_price, $optionitem_price_onetime,  $optionitem_price_override,  $optionitem_price_multiplier,  $optionitem_price_per_character,  $optionitem_weight,  $optionitem_weight_onetime,  $optionitem_weight_override,  $optionitem_weight_multiplier,  $optionitem_order,  $optionitem_icon, $optionitem_initial_value,  $optionitem_model_number,  $optionitem_allow_download,  $optionitem_disallow_shipping,  $optionitem_initially_selected, $optionitem_download_override_file, $optionitem_download_addition_file, $optionitem_id) );
		
		$args = array(
			'success' => 'option-item-updated',
			'option_id' => esc_attr( $option_id )
		);
		
		if( isset( $_GET['pagenum'] ) )
			$args['pagenum'] = (int) $_GET['pagenum'];
			
		if( isset( $_GET['orderby'] ) )
			$args['orderby'] = esc_attr( $_GET['orderby'] );
			
		if( isset( $_GET['order'] ) )
			$args['order'] = esc_attr( $_GET['order'] );
			
		return $args;
	}
	
	public function delete_optionitem( ){
		global $wpdb;
		
		$optionitem_id = $_GET['optionitem_id'];
		$option_id = $wpdb->get_var( $wpdb->prepare( "SELECT option_id FROM ec_optionitem WHERE optionitem_id = %d", $optionitem_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ec_optionitem WHERE optionitem_id = %d", $optionitem_id ) );
		
		$args = array(
			'success' => 'option-item-deleted',
			'option_id' => esc_attr( $option_id )
		);
		
		if( isset( $_GET['pagenum'] ) )
			$args['pagenum'] = (int) $_GET['pagenum'];
			
		if( isset( $_GET['orderby'] ) )
			$args['orderby'] = esc_attr( $_GET['orderby'] );
			
		if( isset( $_GET['order'] ) )
			$args['order'] = esc_attr( $_GET['order'] );
			
		return $args;
	}
	
	public function bulk_delete_optionitem( ){
		global $wpdb;
		
		$bulk_ids = $_GET['bulk'];
		$query_vars = array( );
		if( count( $bulk_ids ) > 0 )
			$option_id = $wpdb->get_var( $wpdb->prepare( "SELECT option_id FROM ec_optionitem WHERE optionitem_id = %d", $bulk_ids[0] ) );
		
		foreach( $bulk_ids as $bulk_id ){	
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_optionitem WHERE optionitem_id = %d", $bulk_id ) );
		}
		
		$args = array(
			'success' => 'option-item-deleted',
			'option_id' => esc_attr( $option_id )
		);
		
		if( isset( $_GET['pagenum'] ) )
			$args['pagenum'] = (int) $_GET['pagenum'];
			
		if( isset( $_GET['orderby'] ) )
			$args['orderby'] = esc_attr( $_GET['orderby'] );
			
		if( isset( $_GET['order'] ) )
			$args['order'] = esc_attr( $_GET['order'] );
			
		return $args;
	}
	
	public function save_optionitem_order( ){
		global $wpdb;
		$sort_order = $_POST['sort_order'];
		$option_id = $_POST['option_id'];
		
		foreach( $sort_order as $sort_item ){
			$wpdb->query( $wpdb->prepare( "UPDATE ec_optionitem SET optionitem_order = %d WHERE optionitem_id = %d AND option_id = %d", $sort_item['order'], $sort_item['id'], $option_id ) );
		}
	}
}
endif; // End if class_exists check

function wp_easycart_admin_option( ){
	return wp_easycart_admin_option::instance( );
}
wp_easycart_admin_option( );

add_action( 'wp_ajax_ec_admin_ajax_save_optionitem_order', 'ec_admin_ajax_save_optionitem_order' );
function ec_admin_ajax_save_optionitem_order( ){
	wp_easycart_admin_option( )->save_optionitem_order( );
	die( );
}