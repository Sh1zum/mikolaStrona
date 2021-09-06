<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_products' ) ) :



final class wp_easycart_admin_products{
	
	protected static $_instance = null;
	
	public $products_setup_file;
	public $product_list_setup_file;
	public $product_details_file;
	public $customer_review_file;
	public $product_settings_file;
	public $price_display_options_file;
	public $inventory_options_file;
	public $product_list_file;
	public $product_details_edit_file;
	public $export_products_csv;
	public $export_product_optionitem_quantities_csv;
	
	
	//importer
	private $db;
	private $error_list;
	private $product_id_index;
	private $post_id_index;
	private $model_number_index;
	private $title_index;
	private $price_index;
	private $list_price_index;
	private $activate_in_store_index;
	private $is_subscription_index;
	private $bill_period_index;
	private $bill_length_index;
	private $trial_period_index;
	private $use_advanced_optionset_index;
	private $advanced_option_ids_index;
	private $categories_index;
	private $headers;
	private $limit;

	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
	
	public function __construct( ){ 
		$this->products_setup_file 			= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/products/products-setup.php';
		$this->product_list_setup_file		= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/products/product-list.php';
		$this->product_details_setup_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/products/product-details.php';
		$this->customer_review_setup_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/products/customer-review.php';
		$this->product_settings_file	 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/products/products-settings.php';
		$this->price_display_options_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/products/price-display-options.php';
		$this->inventory_options_file 		= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/products/inventory-options.php';
		$this->product_list_file 			= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/products/product-list.php';
		$this->product_details_edit_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/products/product-details.php';
		$this->export_products_csv		    = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/exporters/export-products-csv.php';
		$this->export_product_optionitem_quantities_csv		= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/exporters/export-product-optionitems-csv.php';
		
		add_action( 'wpeasycart_admin_products_setup', array( $this, 'load_product_settings' ) );
		add_action( 'wpeasycart_admin_products_setup', array( $this, 'load_product_list_setup' ) );
		add_action( 'wpeasycart_admin_products_setup', array( $this, 'load_customer_review_setup' ) );
		add_action( 'wpeasycart_admin_products_setup', array( $this, 'load_product_details_setup' ) );
		add_action( 'wpeasycart_admin_products_setup', array( $this, 'load_price_display_options' ) );
		add_action( 'wpeasycart_admin_products_setup', array( $this, 'load_inventory_options' ) );
		add_action( 'wp_easycart_admin_settings_product_inventory_end', array( $this, 'add_inventory_notification_setting' ) );
		add_action( 'init', array( $this, 'save_settings' ) );
		add_action( 'admin_head', array( $this, 'add_menu_js' ) );
		
		/* Process Admin Messages */
		add_filter( 'wp_easycart_admin_success_messages', array( $this, 'add_success_messages' ) );
		add_filter( 'wp_easycart_admin_error_messages', array( $this, 'add_failure_messages' ) );
		
		/* Process Form Actions */
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_deactivate_product' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_deactivate_product' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_activate_product' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_duplicate_product' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_delete_product' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_delete_product' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_export_products_csv' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_export_product_optionitem_quantities' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_import_product_optionitem_quantities' ) );
		
	}
	
	public function process_deactivate_product( ){
		if( isset( $_GET['product_id'] ) && $_GET['ec_admin_form_action'] == 'deactivate-product' && !isset( $_GET['bulk'] ) ){
			$result = $this->deactivate_product( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'products', $result );
		}
	}
	
	public function process_bulk_deactivate_product( ){
		if( $_GET['ec_admin_form_action'] == 'deactivate-product' && !isset( $_GET['product_id'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_deactivate_product( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'products', $result );
		}
	}
	
	public function process_bulk_activate_product( ){
		if( $_GET['ec_admin_form_action'] == 'activate-product' && !isset( $_GET['product_id'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_activate_product( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'products', $result );
		}
	}
	
	public function process_duplicate_product( ){
		if( $_GET['ec_admin_form_action'] == 'duplicate-product' && isset( $_GET['product_id'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->duplicate_product( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'products', $result );
		}
	}
	
	public function process_delete_product( ){
		if( $_GET['ec_admin_form_action'] == 'delete-product' && isset( $_GET['product_id'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->delete_product( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'products', $result );
		}
	}
	
	public function process_bulk_delete_product( ){
		if( $_GET['ec_admin_form_action'] == 'delete-product' && !isset( $_GET['product_id'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_delete_product( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'products', $result );
		}
	}
	
	public function process_export_products_csv( ){
		if( isset( $_GET['ec_admin_form_action'] ) && ( $_GET['ec_admin_form_action'] == 'export-products-csv' || $_GET['ec_admin_form_action'] == 'export-all-products-csv' ) ){
			include( $this->export_products_csv );
			die( );
		}
	}
	
	public function process_export_product_optionitem_quantities( ){
		if( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'export-option-item-quantities' ){
			include( $this->export_product_optionitem_quantities_csv );
			die( );
		}
	}
	
	public function process_import_product_optionitem_quantities( ){
		if( isset( $_POST['ec_admin_form_action'] ) && $_POST['ec_admin_form_action'] == 'import-option-item-quantities' ){
			$this->import_optionitem_quantities( );
			wp_redirect( "admin.php?page=wp-easycart-products&subpage=products&product_id=" . (int) $_POST['product_id'] . "&ec_admin_form_action=edit&success=option-items-imported#quantities" );
			die( );
		}
	}
	
	public function add_success_messages( $messages ){
		if( isset( $_GET['success'] ) && $_GET['success'] == 'product-inserted' ){
			$messages[] = __( 'Product successfully created', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'product-updated' ){
			$messages[] = __( 'Product successfully updated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'product-deleted' ){
			$messages[] = __( 'Product successfully deleted', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'product-duplicated' ){
			$messages[] = __( 'Product successfully duplicated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'product-deactivated' ){
			$messages[] = __( 'Products successfully deactivated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'product-activated' ){
			$messages[] = __( 'Products successfully activated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'product-activate-single' ){
			$messages[] = __( 'Product successfully activated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'product-deactivate-single' ){
			$messages[] = __( 'Product successfully deactivated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'option-items-imported' ){
			$messages[] = __( 'Product quantities successfully imported', 'wp-easycart' );
		}
		return $messages;
	}
	
	public function add_failure_messages( $messages ){
		if( isset( $_GET['error'] ) && $_GET['error'] == 'product-inserted-error' ){
			$messages[] = __( 'Product failed to create', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'product-updated-error' ){
			$messages[] = __( 'Product failed to update', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'product-deleted-error' ){
			$messages[] = __( 'Product failed to delete', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'product-duplicated-error' ){
			$messages[] = __( 'Product failed to duplicate', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'product-deactivated-error' ){
			$messages[] = __( 'Product failed to deactivate', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'product-activated-error' ){
			$messages[] = __( 'Product failed to activate', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'product-duplicate' ){
			$messages[] = __( 'Product failed to create due to duplicate', 'wp-easycart' );
		}
		return $messages;
	}
	
	public function load_products_setup( ){
		include( $this->products_setup_file );
	}
	
	public function load_product_list_setup( ){
		include( $this->product_list_setup_file );
	}
	
	public function load_product_details_setup( ){
		include( $this->product_details_setup_file );
	}
	public function load_product_settings( ){
		include( $this->product_settings_file );
	}
	
	public function load_customer_review_setup( ){
		include( $this->customer_review_setup_file );
	}
	
	public function load_price_display_options( ){
		include( $this->price_display_options_file );
	}
	
	public function load_inventory_options( ){
		include( $this->inventory_options_file );
	}
	
	public function add_inventory_notification_setting( ){
		echo '<div style="margin-top:5px;"><input type="checkbox" name="ec_option_enable_inventory_notification" id="ec_option_enable_inventory_notification" value="0" onclick="show_pro_required( );" readonly="readonly" /><span class="dashicons dashicons-lock" style="color:#FC0; margin-top:10px;"></span> ' . __( 'Allow Customers to Subscribe to Stock Notifications', 'wp-easycart' ) . '</div>';
	}
	
	public function load_products_list( ){
		if( isset( $_GET['product_id'] ) && isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'edit' ){
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/inc/wp_easycart_admin_details_products.php' );
			$details = new wp_easycart_admin_details_products( );
			$details->output( 'edit' );
		
		}else if( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ){
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/inc/wp_easycart_admin_details_products.php' );
			$details = new wp_easycart_admin_details_products( );
			$details->output( 'add-new' );
			
		}else{
			include( $this->product_list_file );
		
		}
	}
	
	public function deactivate_product() {
		global $wpdb;
		
		$product_id = $_GET['product_id'];		
		$product = $wpdb->get_row( $wpdb->prepare( "SELECT post_id, model_number, title, activate_in_store FROM ec_product WHERE product_id = %d", $product_id ) );
		$active_status = 1;
		$status = "publish";
		if( $product->activate_in_store == 1 ){ 
			$active_status = 0;
			$status = "private";
		}
		
		/* Manually Update Post */
		$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "posts SET post_status = %s WHERE ID = %d", $status, $product->post_id ) );
		$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET activate_in_store = %d WHERE product_id = %d", $active_status, $product_id ) );
		
		if( $active_status )
			$args = array( 'success' => 'product-activate-single' );
		else
			$args = array( 'success' => 'product-deactivate-single' );
			
		if( isset( $_GET['pagenum'] ) )
			$args['pagenum'] = (int) $_GET['pagenum'];
			
		if( isset( $_GET['orderby'] ) )
			$args['orderby'] = esc_attr( $_GET['orderby'] );
			
		if( isset( $_GET['order'] ) )
			$args['order'] = esc_attr( $_GET['order'] );
			
		if( isset( $_GET['s'] ) )
			$args['s'] = esc_attr( $_GET['s'] );
		
		return $args;
	}
	
	public function bulk_deactivate_product() {
		global $wpdb;
		
		$bulk_ids = $_GET['bulk'];
		foreach( $bulk_ids as $bulk_id ){
			$product = $wpdb->get_row( $wpdb->prepare( "SELECT post_id, model_number, title, activate_in_store FROM ec_product WHERE product_id = %d", $bulk_id ) );
			$active_status = 0;
			$status = "private";
			$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "posts SET post_status = %s WHERE ID = %d", $status, $product->post_id ) );
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET activate_in_store = %d WHERE product_id = %d", $active_status, $bulk_id ) );
		}
		
		$args = array( 'success' => 'product-deactivated' );
		
		if( isset( $_GET['pagenum'] ) )
			$args['pagenum'] = (int) $_GET['pagenum'];
			
		if( isset( $_GET['orderby'] ) )
			$args['orderby'] = esc_attr( $_GET['orderby'] );
			
		if( isset( $_GET['order'] ) )
			$args['order'] = esc_attr( $_GET['order'] );
			
		if( isset( $_GET['s'] ) )
			$args['s'] = esc_attr( $_GET['s'] );
			
		return $args;
	}
	
	public function bulk_activate_product() {
		global $wpdb;
		
		$bulk_ids = $_GET['bulk'];
		foreach( $bulk_ids as $bulk_id ){
			$product = $wpdb->get_row( $wpdb->prepare( "SELECT post_id, model_number, title, activate_in_store FROM ec_product WHERE product_id = %d", $bulk_id ) );
			$active_status = 1;
			$status = "publish";
			$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "posts SET post_status = %s WHERE ID = %d", $status, $product->post_id ) );
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET activate_in_store = %d WHERE product_id = %d", $active_status, $bulk_id ) );
		}
		
		$args = array( 'success' => 'product-activated' );
		
		if( isset( $_GET['pagenum'] ) )
			$args['pagenum'] = (int) $_GET['pagenum'];
			
		if( isset( $_GET['orderby'] ) )
			$args['orderby'] = esc_attr( $_GET['orderby'] );
			
		if( isset( $_GET['order'] ) )
			$args['order'] = esc_attr( $_GET['order'] );
			
		if( isset( $_GET['s'] ) )
			$args['s'] = esc_attr( $_GET['s'] );
			
		return $args;
	}
	
	public function duplicate_product( ){
		global $wpdb;
		
		$product_id = $_GET['product_id'];		
		$product = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.* FROM ec_product WHERE product_id = %d", $product_id ) );
		$original_record = $product;
		$randmodel = rand(1000000, 10000000);
		
		$wpdb->query( $wpdb->prepare( "INSERT INTO ec_product( model_number ) VALUES( %s )", $randmodel ) );
		$newid = $wpdb->insert_id;
		
		$sql = "UPDATE ec_product SET ";
		foreach( $original_record as $key => $value ){
			
			if( $key != "product_id" && $key != "model_number" ){
				if( $key == 'added_to_db_date' ){
					$sql .= '`'.$key.'` = NOW(), ';
				}else if( $key == 'views' ){
					$sql .= '`'.$key.'` = "0", ';
				}else if( $key == 'subscription_unique_id' ){
					$sql .= '`'.$key.'` = "0", ';
				}else{
					$sql .= '`'.$key.'` = ' . $wpdb->prepare( '%s', $value ) .', ';
				}
			}

		}
		
		$sql = substr( $sql, 0, strlen( $sql ) - 2 ); # lop off the extra trailing comma
		$sql .= " WHERE product_id = " . $newid;
		$duplicate_result = $wpdb->query( $sql );
		// END DUPLICATION INSERT
		
		// Duplicate Option Image Rows
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ec_optionitemimage WHERE product_id = %d", $product_id ) );
		foreach( $results as $row ){
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_optionitemimage( optionitem_id, image1, image2, image3, image4, image5, product_id ) VALUES( %s, %s, %s, %s, %s, %s, %d )", $row->optionitem_id, $row->image1, $row->image2, $row->image3, $row->image4, $row->image5, $newid ) );
		}
		// END DUPLICATE OPTION IMAGE ROWS
		
		// Duplicate Tiered Pricing
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ec_pricetier WHERE product_id = %d", $product_id ) );
		foreach( $results as $row ){
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_pricetier( product_id, price, quantity) VALUES( %d, %s, %s )", $newid, $row->price, $row->quantity ) );
		}
		// END DUPLICATE TIERED PRICING
		
		// Duplicate Category Listings
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ec_categoryitem WHERE product_id = %d", $product_id ) );
		foreach( $results as $row ){
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_categoryitem( product_id, category_id ) VALUES( %d, %d )", $newid, $row->category_id ) ); 
		}
		// END DUPLICATE CATEGORY LISTINGS
		
		// Duplicate B2B Role Pricing
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ec_roleprice WHERE product_id = %d", $product_id));
		foreach( $results as $row ){
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_roleprice( product_id, role_label, role_price ) VALUES( %d, %s, %s )", $newid, $row->role_label, $row->role_price ) ); 
		}
		// END DUPLICATE B2B ROLE PRICING
		
		// Duplicate Option Quantity Rows
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ec_optionitemquantity WHERE product_id = %d", $product_id ) );
		foreach( $results as $row ){
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_optionitemquantity( optionitem_id_1, optionitem_id_2, optionitem_id_3, optionitem_id_4, optionitem_id_5, quantity, product_id ) VALUES( %d, %d, %d, %d, %d, %s, %d )", $row->optionitem_id_1, $row->optionitem_id_2, $row->optionitem_id_3, $row->optionitem_id_4, $row->optionitem_id_5, $row->quantity, $newid ) );
		}
		// END DUPLICATE OPTION QUANTITY ROWS
		
		// Duplicate Advanced Option Rows
		$replace_ids = array( );
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ec_option_to_product WHERE product_id = %d ORDER BY option_to_product_id ASC", $product_id ) );
		foreach( $results as $row ){
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_option_to_product( option_id, product_id, role_label, option_order, conditional_logic ) VALUES( %d, %d, %s, %d, %s )", $row->option_id, $newid, $row->role_label, $row->option_order, $row->conditional_logic ) );
			$replace_ids[$row->option_to_product_id] = $wpdb->insert_id;
		}
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ec_option_to_product WHERE product_id = %d ORDER BY option_to_product_id ASC", $newid ) );
		foreach( $results as $row ){
			if( $row->conditional_logic ){
				$meta = json_decode( stripslashes( $row->conditional_logic ) );
				for( $i=0; $i<count( $meta->rules ); $i++ ){
					$meta->rules[$i]->option_id = $replace_ids[$meta->rules[$i]->option_id];
				}
				$wpdb->query( $wpdb->prepare( "UPDATE ec_option_to_product SET conditional_logic = %s WHERE option_to_product_id = %d", json_encode( $meta ), $row->option_to_product_id ) );
			}
		}
		// END DUPLICATE ADVANCED OPTION ROWS
		
		//Enqueue Quickbooks Update Customer
		if( file_exists( "../../../../wp-easycart-quickbooks/QuickBooks.php" ) ){
			$quickbooks = new ec_quickbooks( );
			$quickbooks->add_product( $randmodel );	
		}
		
		// Insert a WordPress Custom post type post.
		$status = "private";
		if( $product->activate_in_store )
			$status = "publish";
			
		$post_slug = preg_replace( '/(\-+)/', '-', preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep( strtolower( $product->title ) ) ) ) );
		while( substr( $post_slug, -1 ) == '-' ){
			$post_slug = substr( $post_slug, 0, strlen( $post_slug ) - 1 );
		}
		while( substr( $post_slug, 0, 1 ) == '-' ){
			$post_slug = substr( $post_slug, 1, strlen( $post_slug ) );
		}
		if( $post_slug == '' ){
			$post_slug = rand( 1000000, 9999999 );
		}
		$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
		if( strstr( $store_page, '?' ) )
			$guid = $store_page . '&model_number=' . $randmodel;
		else if( substr( $store_page, strlen( $store_page ) - 1 ) == '/' )
			$guid = $store_page . $post_slug;
		else
			$guid = $store_page . '/' . $post_slug;
		
		$guid = strtolower( $guid );
		$post_slug_orig = $post_slug;
		$guid_orig = $guid;
		$guid = $guid . '/';
		$i=1;
		while( $guid_check = $wpdb->get_row( $wpdb->prepare( "SELECT " . $wpdb->prefix . "posts.guid FROM " . $wpdb->prefix . "posts WHERE " . $wpdb->prefix . "posts.guid = %s", $guid ) ) ){
			$guid = $guid_orig . '-' . $i . '/';
			$post_slug = $post_slug_orig . '-' . $i;
			$i++;
		} 
		
		/* Manually Insert Post */
		$wpdb->query( $wpdb->prepare( "INSERT INTO " . $wpdb->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type, post_date, post_date_gmt, post_modified, post_modified_gmt, comment_status ) VALUES( %s, %s, %s, %s, %s, %s, NOW( ), UTC_TIMESTAMP( ), NOW( ), UTC_TIMESTAMP( ), 'closed' )", "[ec_store modelnumber=\"" . $randmodel . "\"]", $status, $GLOBALS['language']->convert_text( $product->title ), $post_slug, $guid, "ec_store" ) );
		$post_id = $wpdb->insert_id;
		
		$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET post_id = %d WHERE product_id = %d", $post_id, $newid ) );
		
		/* If Stripe Insert New Plan */
		if( get_option( 'ec_option_payment_process_method' ) == 'stripe' || get_option( 'ec_option_payment_process_method' ) == 'stripe_connect' ){
			if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
				$stripe = new ec_stripe( );
			else if( get_option( 'ec_option_payment_process_method' ) == 'stripe_connect' )
				$stripe = new ec_stripe_connect( );
			
			$product_row = $wpdb->get_row( $wpdb->prepare( "SELECT post_id, is_subscription_item, stripe_plan_added, subscription_unique_id, product_id, price, title, subscription_bill_period, subscription_bill_length, trial_period_days FROM ec_product WHERE product_id = %d", $newid ) );
			$product_row->subscription_unique_id = rand(10000, 10000000);
			$result = $stripe->insert_plan( $product_row );
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET subscription_unique_id = %d, stripe_plan_added = 1 WHERE product_id = %d", $product_row->subscription_unique_id, $newid ) );
		}
		
		$args = array( 'success' => 'product-duplicated' );
		
		if( isset( $_GET['pagenum'] ) )
			$args['pagenum'] = (int) $_GET['pagenum'];
			
		if( isset( $_GET['orderby'] ) )
			$args['orderby'] = esc_attr( $_GET['orderby'] );
			
		if( isset( $_GET['order'] ) )
			$args['order'] = esc_attr( $_GET['order'] );
			
		if( isset( $_GET['s'] ) )
			$args['s'] = esc_attr( $_GET['s'] );
			
		return $args;
	}
	
	public function delete_product() {
		global $wpdb;
		
		$product_id = $_GET['product_id'];		
		$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM ec_product WHERE product_id = %d", $product_id ) );
		
		wp_delete_post( $post_id, true );
		if( get_option( 'ec_option_payment_process_method' ) == 'stripe' ){
			$stripe_plan = ( object ) array( "product_id" => $product_id );
			$stripe = new ec_stripe;
			$response = $stripe->delete_plan( $stripe_plan );
		}else if( get_option( 'ec_option_payment_process_method' ) == 'stripe_connect' ){
			$stripe_plan = ( object ) array( "product_id" => $product_id );
			$stripe = new ec_stripe_connect;
			$response = $stripe->delete_plan( $stripe_plan );
		}
		
		$wpdb->query( $wpdb->prepare( "DELETE FROM ec_product WHERE product_id = %d", $product_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ec_optionitemimage WHERE product_id = %d", $product_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ec_pricetier WHERE product_id = %d", $product_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ec_roleprice WHERE product_id = %d", $product_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ec_optionitemquantity WHERE product_id = %d", $product_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ec_option_to_product WHERE product_id = %d", $product_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ec_review WHERE product_id = %d", $product_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ec_affiliate_rule_to_product WHERE product_id = %d", $product_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ec_categoryitem WHERE product_id = %d", $product_id ) );

		$args = array( 'success' => 'product-deleted' );
		
		if( isset( $_GET['pagenum'] ) )
			$args['pagenum'] = (int) $_GET['pagenum'];
			
		if( isset( $_GET['orderby'] ) )
			$args['orderby'] = esc_attr( $_GET['orderby'] );
			
		if( isset( $_GET['order'] ) )
			$args['order'] = esc_attr( $_GET['order'] );
			
		if( isset( $_GET['s'] ) )
			$args['s'] = esc_attr( $_GET['s'] );
			
		return $args;
	}
	
	public function bulk_delete_product() {
		$bulk_ids = $_GET['bulk'];
		$query_vars = array( );
		
		global $wpdb;
		$errors = 0;
		foreach( $bulk_ids as $bulk_id ){
			$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM ec_product WHERE product_id = %d", $bulk_id ) );
			
			wp_delete_post( $post_id, true );
			if( get_option( 'ec_option_payment_process_method' ) == 'stripe' ){
				$stripe_plan = ( object ) array( "product_id" => $bulk_id );
				$stripe = new ec_stripe;
				$response = $stripe->delete_plan( $stripe_plan );
			}else if( get_option( 'ec_option_payment_process_method' ) == 'stripe_conect' ){
				$stripe_plan = ( object ) array( "product_id" => $bulk_id );
				$stripe = new ec_stripe_conect;
				$response = $stripe->delete_plan( $stripe_plan );
			}
			
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_product WHERE product_id = %d", $bulk_id ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_optionitemimage WHERE product_id = %d", $bulk_id ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_pricetier WHERE product_id = %d", $bulk_id ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_roleprice WHERE product_id = %d", $bulk_id ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_optionitemquantity WHERE product_id = %d", $bulk_id ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_option_to_product WHERE product_id = %d", $bulk_id ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_review WHERE product_id = %d", $bulk_id ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_affiliate_rule_to_product WHERE product_id = %d", $bulk_id ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_categoryitem WHERE product_id = %d", $bulk_id ) );
		}
		
		$args = array( 'success' => 'product-deleted' );
		
		if( isset( $_GET['pagenum'] ) )
			$args['pagenum'] = (int) $_GET['pagenum'];
			
		if( isset( $_GET['orderby'] ) )
			$args['orderby'] = esc_attr( $_GET['orderby'] );
			
		if( isset( $_GET['order'] ) )
			$args['order'] = esc_attr( $_GET['order'] );
			
		if( isset( $_GET['s'] ) )
			$args['s'] = esc_attr( $_GET['s'] );
			
		return $args;
	}
	
	public function import_optionitem_quantities( ){
		global $wpdb;
		$product_id = (int) $_POST['product_id'];
		$total_quantity = 0;
		
		// Delete old values
		$wpdb->query( $wpdb->prepare( "DELETE FROM ec_optionitemquantity WHERE product_id = %d", $product_id ) );
		
		// Process File
		$file_name = $_FILES['import_file']['tmp_name'];
		$first = true;
		if( ( $handle = fopen( $file_name, "r" ) ) !== FALSE ){
			while( ( $data = fgetcsv( $handle, 1000, "," ) ) !== FALSE ){
				if( !$first ){
					if( count( $data ) >= 7 ){
						$wpdb->query( $wpdb->prepare( "INSERT INTO ec_optionitemquantity( product_id, optionitem_id_1, optionitem_id_2, optionitem_id_3, optionitem_id_4, optionitem_id_5, quantity ) VALUES( %d, %d, %d, %d, %d, %d, %d )", $product_id, $data[2], $data[3], $data[4], $data[5], $data[6], $data[1] ) );
						$total_quantity += $data[1];
					}
				}else{
					$first = false;
				}
			}
			fclose( $handle );
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET stock_quantity = %d WHERE product_id = %d", $total_quantity, $product_id ) );
		}
	}
	
	public function save_product_settings( ){
		$ec_option_display_as_catalog = 0;
		$ec_option_subscription_one_only = 0;
		$ec_option_restrict_store = '';
		
		if( isset( $_POST['ec_option_display_as_catalog'] ) && $_POST['ec_option_display_as_catalog'] == '1' )
			$ec_option_display_as_catalog = 1;
		if( isset( $_POST['ec_option_subscription_one_only'] ) && $_POST['ec_option_subscription_one_only'] == '1' )
			$ec_option_subscription_one_only = 1;
		if( isset( $_POST['ec_option_restrict_store'] ))
			$ec_option_restrict_store = implode( '***', $_POST['ec_option_restrict_store'] )  ;
		
		update_option( 'ec_option_display_as_catalog', $ec_option_display_as_catalog );
		update_option( 'ec_option_subscription_one_only', $ec_option_subscription_one_only );
		update_option( 'ec_option_restrict_store', $ec_option_restrict_store );
	}
	
	public function save_product_list_display( ){
		$ec_option_show_sort_box = $_POST['ec_option_show_sort_box'];
		$ec_option_default_store_filter = $_POST['ec_option_default_store_filter'];
		
		$ec_option_product_filter_0 = 0;
		if( isset( $_POST['ec_option_product_filter_0'] ) && $_POST['ec_option_product_filter_0'] == '1' )
			$ec_option_product_filter_0 = 1;
		
		$ec_option_product_filter_1 = 0;
		if( isset( $_POST['ec_option_product_filter_1'] ) && $_POST['ec_option_product_filter_1'] == '1' )
			$ec_option_product_filter_1 = 1;
			
		$ec_option_product_filter_2 = 0;
		if( isset( $_POST['ec_option_product_filter_2'] ) && $_POST['ec_option_product_filter_2'] == '1' )
			$ec_option_product_filter_2 = 1;
			
		$ec_option_product_filter_3 = 0;
		if( isset( $_POST['ec_option_product_filter_3'] ) && $_POST['ec_option_product_filter_3'] == '1' )
			$ec_option_product_filter_3 = 1;
			
		$ec_option_product_filter_4 = 0;
		if( isset( $_POST['ec_option_product_filter_4'] ) && $_POST['ec_option_product_filter_4'] == '1' )
			$ec_option_product_filter_4 = 1;
			
		$ec_option_product_filter_5 = 0;
		if( isset( $_POST['ec_option_product_filter_5'] ) && $_POST['ec_option_product_filter_5'] == '1' )
			$ec_option_product_filter_5 = 1;
			
		$ec_option_product_filter_6 = 0;
		if( isset( $_POST['ec_option_product_filter_6'] ) && $_POST['ec_option_product_filter_6'] == '1' )
			$ec_option_product_filter_6 = 1;
			
		$ec_option_product_filter_7 = 0;
		if( isset( $_POST['ec_option_product_filter_7'] ) && $_POST['ec_option_product_filter_7'] == '1' )
			$ec_option_product_filter_7 = 1;
		
		$ec_option_short_description_on_product = 0;
		if( isset( $_POST['ec_option_short_description_on_product'] ) && $_POST['ec_option_short_description_on_product'] == '1' )
			$ec_option_short_description_on_product = 1;
		
		$ec_option_show_featured_categories = 0;
		if( isset( $_POST['ec_option_show_featured_categories'] ) && $_POST['ec_option_show_featured_categories'] == '1' )
			$ec_option_show_featured_categories = 1;
		
		$ec_option_enable_product_paging = $_POST['ec_option_enable_product_paging'];
		
		update_option( 'ec_option_show_sort_box', $ec_option_show_sort_box );
		update_option( 'ec_option_default_store_filter', $ec_option_default_store_filter );
		update_option( 'ec_option_product_filter_0', $ec_option_product_filter_0 );
		update_option( 'ec_option_product_filter_1', $ec_option_product_filter_1 );
		update_option( 'ec_option_product_filter_2', $ec_option_product_filter_2 );
		update_option( 'ec_option_product_filter_3', $ec_option_product_filter_3 );
		update_option( 'ec_option_product_filter_4', $ec_option_product_filter_4 );
		update_option( 'ec_option_product_filter_5', $ec_option_product_filter_5 );
		update_option( 'ec_option_product_filter_6', $ec_option_product_filter_6 );
		update_option( 'ec_option_product_filter_7', $ec_option_product_filter_7 );
		update_option( 'ec_option_short_description_on_product', $ec_option_short_description_on_product );
		update_option( 'ec_option_show_featured_categories', $ec_option_show_featured_categories );
		update_option( 'ec_option_enable_product_paging', $ec_option_enable_product_paging );
	}
	
	public function save_product_details_display( ){
		$ec_option_model_number_extension = stripslashes_deep( $_POST['ec_option_model_number_extension'] );
		$ec_option_show_breadcrumbs = 0;
		if( isset( $_POST['ec_option_show_breadcrumbs'] ) && $_POST['ec_option_show_breadcrumbs'] )
			$ec_option_show_breadcrumbs = 1;
			
		$ec_option_show_magnification = 0;
		if( isset( $_POST['ec_option_show_magnification'] ) && $_POST['ec_option_show_magnification'] )
			$ec_option_show_magnification = 1;
		
		$ec_option_show_large_popup = 0;
		if( isset( $_POST['ec_option_show_large_popup'] ) && $_POST['ec_option_show_large_popup'] )
			$ec_option_show_large_popup = 1;
		
		$ec_option_show_model_number = 0;
		if( isset( $_POST['ec_option_show_model_number'] ) && $_POST['ec_option_show_model_number'] )
			$ec_option_show_model_number = 1;
		
		$ec_option_show_categories = 0;
		if( isset( $_POST['ec_option_show_categories'] ) && $_POST['ec_option_show_categories'] )
			$ec_option_show_categories = 1;
		
		$ec_option_show_manufacturer = 0;
		if( isset( $_POST['ec_option_show_manufacturer'] ) && $_POST['ec_option_show_manufacturer'] )
			$ec_option_show_manufacturer = 1;
			
		$ec_option_show_stock_quantity = 0;
		if( isset( $_POST['ec_option_show_stock_quantity'] ) && $_POST['ec_option_show_stock_quantity'] )
			$ec_option_show_stock_quantity = 1;
		
		$ec_option_use_facebook_icon = 0;
		if( isset( $_POST['ec_option_use_facebook_icon'] ) && $_POST['ec_option_use_facebook_icon'] )
			$ec_option_use_facebook_icon = 1;
		
		$ec_option_use_twitter_icon = 0;
		if( isset( $_POST['ec_option_use_twitter_icon'] ) && $_POST['ec_option_use_twitter_icon'] )
			$ec_option_use_twitter_icon = 1;
		
		$ec_option_use_delicious_icon = 0;
		if( isset( $_POST['ec_option_use_delicious_icon'] ) && $_POST['ec_option_use_delicious_icon'] )
			$ec_option_use_delicious_icon = 1;
		
		$ec_option_use_myspace_icon = 0;
		if( isset( $_POST['ec_option_use_myspace_icon'] ) && $_POST['ec_option_use_myspace_icon'] )
			$ec_option_use_myspace_icon = 1;
		
		$ec_option_use_linkedin_icon = 0;
		if( isset( $_POST['ec_option_use_linkedin_icon'] ) && $_POST['ec_option_use_linkedin_icon'] )
			$ec_option_use_linkedin_icon = 1;
		
		$ec_option_use_email_icon = 0;
		if( isset( $_POST['ec_option_use_email_icon'] ) && $_POST['ec_option_use_email_icon'] )
			$ec_option_use_email_icon = 1;
		
		$ec_option_use_digg_icon = 0;
		if( isset( $_POST['ec_option_use_digg_icon'] ) && $_POST['ec_option_use_digg_icon'] )
			$ec_option_use_digg_icon = 1;
		
		$ec_option_use_googleplus_icon = 0;
		if( isset( $_POST['ec_option_use_googleplus_icon'] ) && $_POST['ec_option_use_googleplus_icon'] )
			$ec_option_use_googleplus_icon = 1;
		
		$ec_option_use_pinterest_icon = 0;
		if( isset( $_POST['ec_option_use_pinterest_icon'] ) && $_POST['ec_option_use_pinterest_icon'] )
			$ec_option_use_pinterest_icon = 1;
		
		update_option( 'ec_option_model_number_extension', $ec_option_model_number_extension );
		update_option( 'ec_option_show_breadcrumbs', $ec_option_show_breadcrumbs );
		update_option( 'ec_option_show_magnification', $ec_option_show_magnification );
		update_option( 'ec_option_show_large_popup', $ec_option_show_large_popup );
		update_option( 'ec_option_show_model_number', $ec_option_show_model_number );
		update_option( 'ec_option_show_categories', $ec_option_show_categories );
		update_option( 'ec_option_show_manufacturer', $ec_option_show_manufacturer );
		update_option( 'ec_option_show_stock_quantity', $ec_option_show_stock_quantity );
		
		update_option( 'ec_option_use_facebook_icon', $ec_option_use_facebook_icon );
		update_option( 'ec_option_use_twitter_icon', $ec_option_use_twitter_icon );
		update_option( 'ec_option_use_delicious_icon', $ec_option_use_delicious_icon );
		update_option( 'ec_option_use_myspace_icon', $ec_option_use_myspace_icon );
		update_option( 'ec_option_use_linkedin_icon', $ec_option_use_linkedin_icon );
		update_option( 'ec_option_use_email_icon', $ec_option_use_email_icon );
		update_option( 'ec_option_use_digg_icon', $ec_option_use_digg_icon );
		update_option( 'ec_option_use_googleplus_icon', $ec_option_use_googleplus_icon );
		update_option( 'ec_option_use_pinterest_icon', $ec_option_use_pinterest_icon );
	}
	
	public function save_customer_review_display( ){
		$ec_option_customer_review_require_login = 0;
		if( isset( $_POST['ec_option_customer_review_require_login'] ) && $_POST['ec_option_customer_review_require_login'] )
			$ec_option_customer_review_require_login = 1;
		
		$ec_option_customer_review_show_user_name = 0;
		if( isset( $_POST['ec_option_customer_review_show_user_name'] ) && $_POST['ec_option_customer_review_show_user_name'] )
			$ec_option_customer_review_show_user_name = 1;
		
		$ec_option_customer_review_notification = 0;
		if( isset( $_POST['ec_option_customer_review_notification'] ) && $_POST['ec_option_customer_review_notification'] )
			$ec_option_customer_review_notification = 1;
		
		update_option( 'ec_option_customer_review_require_login', $ec_option_customer_review_require_login );
		update_option( 'ec_option_customer_review_show_user_name', $ec_option_customer_review_show_user_name );
		update_option( 'ec_option_customer_review_notification', $ec_option_customer_review_notification );
	}
	
	public function save_price_display( ){
		$ec_option_hide_price_seasonal = 0;
		if( isset( $_POST['ec_option_hide_price_seasonal'] ) && $_POST['ec_option_hide_price_seasonal'] )
			$ec_option_hide_price_seasonal = 1;
		
		$ec_option_hide_price_inquiry = 0;
		if( isset( $_POST['ec_option_hide_price_inquiry'] ) && $_POST['ec_option_hide_price_inquiry'] )
			$ec_option_hide_price_inquiry = 1;
		
		$ec_option_show_multiple_vat_pricing = 0;
		if( isset( $_POST['ec_option_show_multiple_vat_pricing'] ) && $_POST['ec_option_show_multiple_vat_pricing'] )
			$ec_option_show_multiple_vat_pricing = 1;
			
		$ec_option_tiered_price_format = 0;
		if( isset( $_POST['ec_option_tiered_price_format'] ) && $_POST['ec_option_tiered_price_format'] )
			$ec_option_tiered_price_format = 1;
			
		$ec_option_tiered_price_by_option = 0;
		if( isset( $_POST['ec_option_tiered_price_by_option'] ) && $_POST['ec_option_tiered_price_by_option'] )
			$ec_option_tiered_price_by_option = 1;
		
		update_option( 'ec_option_hide_price_seasonal', $ec_option_hide_price_seasonal );
		update_option( 'ec_option_hide_price_inquiry', $ec_option_hide_price_inquiry );
		update_option( 'ec_option_show_multiple_vat_pricing', $ec_option_show_multiple_vat_pricing );
		update_option( 'ec_option_tiered_price_format', $ec_option_tiered_price_format );
		update_option( 'ec_option_tiered_price_by_option', $ec_option_tiered_price_by_option );
	}
	
	public function save_inventory_options( ){
		$ec_option_stock_removed_in_cart = 0;
		$ec_option_enable_inventory_notification = 0;
		if( isset( $_POST['ec_option_stock_removed_in_cart'] ) && $_POST['ec_option_stock_removed_in_cart'] )
			$ec_option_stock_removed_in_cart = 1;
		if( isset( $_POST['ec_option_enable_inventory_notification'] ) && $_POST['ec_option_enable_inventory_notification'] )
			$ec_option_enable_inventory_notification = 1;
			
		update_option( 'ec_option_stock_removed_in_cart', $ec_option_stock_removed_in_cart );
		update_option( 'ec_option_tempcart_stock_hours', ( ( round( $_POST['ec_option_tempcart_stock_hours'] ) <= 0 ) ? 1 : round( $_POST['ec_option_tempcart_stock_hours'] ) ) );
		update_option( 'ec_option_tempcart_stock_timeframe', $_POST['ec_option_tempcart_stock_timeframe'] );
		update_option( 'ec_option_enable_inventory_notification', $ec_option_enable_inventory_notification );
	}
	
	public function save_settings( ){
		
		if( current_user_can( 'manage_options' ) && isset( $_POST['ec_admin_form_action'] ) && $_POST['ec_admin_form_action'] == "save-products-setup" ){
			
			$this->save_product_list_display( );
			$this->save_product_details_display( );
			$this->save_customer_review_display( );
			$this->save_price_display( );
			
			wp_redirect( "admin.php?page=wp-easycart-settings&subpage=products&success=easycart-products-saved" );
			
		}
	
	}
	
	public function add_menu_js( ){
		global $wpdb;
		$menus = $wpdb->get_results( "SELECT ec_menulevel1.menulevel1_id as id, ec_menulevel1.name as text FROM ec_menulevel1 ORDER BY ec_menulevel1.name ASC" );
		$submenus = $wpdb->get_results( "SELECT ec_menulevel2.menulevel2_id as id, ec_menulevel2.menulevel1_id AS parent_id, ec_menulevel2.name as text FROM ec_menulevel2 ORDER BY ec_menulevel2.menulevel1_id ASC, ec_menulevel2.name ASC" );
		$subsubmenus = $wpdb->get_results( "SELECT ec_menulevel3.menulevel3_id as id, ec_menulevel3.menulevel2_id AS parent_id, ec_menulevel3.name as text FROM ec_menulevel3 ORDER BY ec_menulevel3.menulevel2_id ASC, ec_menulevel3.name ASC" );
	
		echo "<script>";
		echo "var menulevel1 = [";
		for( $i=0; $i<count( $menus ); $i++ ){
			if( $i != 0 )
				echo ", ";
			echo "{ id:" . $menus[$i]->id . ", text:'" . str_replace( "'", "\'", $menus[$i]->text ) . "' }";
		}
		echo "];";
		
		echo "var menulevel2 = [";
		for( $i=0; $i<count( $submenus ); $i++ ){
			if( $i != 0 )
				echo ", ";
			echo "{ id:" . $submenus[$i]->id . ", text:'" . str_replace( "'", "\'", $submenus[$i]->text ) . "', parent_id:" . $submenus[$i]->parent_id . " }";
		}
		echo "];";
		
		echo "var menulevel3 = [";
		for( $i=0; $i<count( $subsubmenus ); $i++ ){
			if( $i != 0 )
				echo ", ";
			echo "{ id:" . $subsubmenus[$i]->id . ", text:'" . str_replace( "'", "\'", $subsubmenus[$i]->text ) . "', parent_id:" . $subsubmenus[$i]->parent_id . " }";
		}
		echo "];";
		echo "jQuery( document ).ready( function( ){
			/* Load Select2 Menu CBs (if applicable) */
			if( jQuery( document.getElementById( 'menulevel1_id_1' ) ).length ){
				if( jQuery( document.getElementById( 'menulevel1_id_1' ) ).val( ) == '0' ){ // Clear 2 & 3
					jQuery( '#ec_admin_row_menulevel1_id_2 select option' ).remove( );
					jQuery( '#ec_admin_row_menulevel1_id_3 select option' ).remove( );
				
				}else{ // Update Menu 2 Options
					for( i=0; i<menulevel2.length; i++ ){
						if( menulevel2[i].parent_id != jQuery( document.getElementById( 'menulevel1_id_1' ) ).val( ) ){
							jQuery( '#ec_admin_row_menulevel1_id_2 select option[value=\"' +  menulevel2[i].id + '\"]' ).remove( );
						}
					}
				}
				
				if( jQuery( document.getElementById( 'menulevel1_id_2' ) ).val( ) == '0' ){ // Clear 3
					jQuery( '#ec_admin_row_menulevel1_id_3 select option' ).remove( );
				
				}else{ // Update Menu 3 Options
					for( i=0; i<menulevel3.length; i++ ){
						if( menulevel3[i].parent_id != jQuery( document.getElementById( 'menulevel1_id_2' ) ).val( ) ){
							jQuery( '#ec_admin_row_menulevel1_id_3 select option[value=\"' +  menulevel3[i].id + '\"]' ).remove( );
						}
					}
				}
				
				if( jQuery( document.getElementById( 'menulevel2_id_1' ) ).val( ) == '0' ){ // Clear 2 & 3
					jQuery( '#ec_admin_row_menulevel2_id_2 select option' ).remove( );
					jQuery( '#ec_admin_row_menulevel2_id_3 select option' ).remove( );
				
				}else{ // Update Menu 2 Options
					for( i=0; i<menulevel2.length; i++ ){
						if( menulevel2[i].parent_id != jQuery( document.getElementById( 'menulevel2_id_1' ) ).val( ) ){
							jQuery( '#ec_admin_row_menulevel2_id_2 select option[value=\"' +  menulevel2[i].id + '\"]' ).remove( );
						}
					}
				}
				
				if( jQuery( document.getElementById( 'menulevel2_id_2' ) ).val( ) == '0' ){ // Clear 3
					jQuery( '#ec_admin_row_menulevel2_id_3 select option' ).remove( );
				
				}else{ // Update Menu 3 Options
					for( i=0; i<menulevel3.length; i++ ){
						if( menulevel3[i].parent_id != jQuery( document.getElementById( 'menulevel2_id_2' ) ).val( ) ){
							jQuery( '#ec_admin_row_menulevel2_id_3 select option[value=\"' +  menulevel3[i].id + '\"]' ).remove( );
						}
					}
				}
				
				if( jQuery( document.getElementById( 'menulevel3_id_1' ) ).val( ) == '0' ){ // Clear 2 & 3
					jQuery( '#ec_admin_row_menulevel3_id_2 select option' ).remove( );
					jQuery( '#ec_admin_row_menulevel3_id_3 select option' ).remove( );
				
				}else{ // Update Menu 2 Options
					for( i=0; i<menulevel2.length; i++ ){
						if( menulevel2[i].parent_id != jQuery( document.getElementById( 'menulevel3_id_1' ) ).val( ) ){
							jQuery( '#ec_admin_row_menulevel3_id_2 select option[value=\"' +  menulevel2[i].id + '\"]' ).remove( );
						}
					}
				}
				
				if( jQuery( document.getElementById( 'menulevel3_id_2' ) ).val( ) == '0' ){ // Clear 3
					jQuery( '#ec_admin_row_menulevel3_id_3 select option' ).remove( );
				
				}else{ // Update Menu 3 Options
					for( i=0; i<menulevel3.length; i++ ){
						if( menulevel3[i].parent_id != jQuery( document.getElementById( 'menulevel3_id_2' ) ).val( ) ){
							jQuery( '#ec_admin_row_menulevel3_id_3 select option[value=\"' +  menulevel3[i].id + '\"]' ).remove( );
						}
					}
				}
				
				// ALSO ADD ON CHANGE EVENTS TO MENU LEVEL 1 & 2 AND UPDATE CBS ON CHANGE!
				/* Update Open Panel Based on Hash */
				var hash = jQuery.trim( window.location.hash ).substr( 1, jQuery.trim( window.location.hash ).length ).replace( '-', '_' );
				if( hash.length > 0 && jQuery( document.getElementById( 'ec_admin_product_details_options_section' ) ) )
					jQuery( document.getElementById( 'ec_admin_product_details_' + hash + '_section' ) ).show( );
			}
		} );";
		echo "</script>";
	}
	
	public function save_new_quick_product( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$activate_in_store = $_POST['ec_new_product_status'];
			
			// Product Type
			$product_type = $_POST['ec_new_product_type'];
			$is_download = $is_donation = $is_invoice = $is_subscription = $is_giftcard = $is_deconetwork = $is_inquiry = $is_seasonal = 0;
			if( $product_type == 1 || $product_type == 2 )
				$is_download = 1;
			else if( $product_type == 3 || $product_type == 4 )
				$is_donation = 1;
			else if( $product_type == 5 || $product_type == 6 )
				$is_subscription = 1;
			else if( $product_type == 7 )
				$is_giftcard = 1;
			else if( $product_type == 8 )
				$is_deconetwork = 1;
			else if( $product_type == 9 )
				$is_inquiry = 1;
			else if( $product_type == 10 )
				$is_seasonal = 1;
			
			$post_status = ( $activate_in_store ) ? 'publish' : 'private';
			$show_on_startup = $_POST['ec_new_product_featured'];
			$title = stripslashes_deep( $_POST['ec_new_product_title'] );
			$post_slug = preg_replace( '/(\-+)/', '-', preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep( strtolower( $title ) ) ) ) );
			while( substr( $post_slug, -1 ) == '-' ){
				$post_slug = substr( $post_slug, 0, strlen( $post_slug ) - 1 );
			}
			while( substr( $post_slug, 0, 1 ) == '-' ){
				$post_slug = substr( $post_slug, 1, strlen( $post_slug ) );
			}
			if( $post_slug == '' ){
				$post_slug = rand( 1000000, 9999999 );
			}
			$sku = preg_replace( '/(\-+)/', '-', preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep( $_POST['ec_new_product_sku'] ) ) ) );
			$manufacturer = $_POST['ec_new_product_manufacturer'];
			$price = $_POST['ec_new_product_price'];
			$image = stripslashes_deep( $_POST['ec_new_product_image'] );
			$is_shippable = $_POST['ec_new_product_is_shippable'];
			
			$show_stock_quantity = $stock_quantity = $use_optionitem_quantity_tracking = 0;
			if( $_POST['ec_new_product_stock_option'] == '1' ){
				$show_stock_quantity = 1;
				$stock_quantity = $_POST['ec_new_product_stock_quantity'];
				
			}else if( $_POST['ec_new_product_stock_option'] == '2' ){
				$use_optionitem_quantity_tracking = 1;
			}
			
			$weight = $length = $width = $height = 0;
			if( $is_shippable ){
				$weight = $_POST['ec_new_product_weight'];
				$length = $_POST['ec_new_product_length'];
				$width = $_POST['ec_new_product_width'];
				$height = $_POST['ec_new_product_height'];
			}
			
			if( !get_option( 'ec_option_admin_product_show_stock_option' ) && $product_type == 0 ){
				$is_shippable = 1;
				$weight = .1;
				$length = 1;
				$width = 1;
				$height = 1;
				
			}
			
			$is_taxable = $vat_rate = 0;
			if( $_POST['ec_new_product_is_taxable'] == '1' )
				$is_taxable = 1;
			else if( $_POST['ec_new_product_is_taxable'] == '2' )
				$vat_rate = 1;
				
			$option_type = $_POST['ec_new_product_option_type'];
			$use_advanced_optionset = ( $option_type == '2' ) ? 1 : 0;
			$option1 = ( $option_type == '1' ) ? $_POST['option1'] : 0;
			$option2 = ( $option_type == '1' ) ? $_POST['option2'] : 0;
			$option3 = ( $option_type == '1' ) ? $_POST['option3'] : 0;
			$option4 = ( $option_type == '1' ) ? $_POST['option4'] : 0;
			$option5 = ( $option_type == '1' ) ? $_POST['option5'] : 0;
			
			if( !$this->verify_model_number( $sku ) )
				return array( 'error' => 'model-number-error' );
				
			// Get URL
			$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
			if( strstr( $store_page, '?' ) )									$guid = $store_page . '&model_number=' . $model_number;
			else if( substr( $store_page, strlen( $store_page ) - 1 ) == '/' )	$guid = $store_page . $post_slug;
			else																$guid = $store_page . '/' . $post_slug;
			
			$guid = strtolower( $guid );
			$post_slug_orig = $post_slug;
			$guid_orig = $guid;
			$guid = $guid . '/';
			
			/* Fix for Duplicate GUIDs */
			$i=1;
			while( $guid_check = $wpdb->get_row( $wpdb->prepare( "SELECT " . $wpdb->prefix . "posts.guid FROM " . $wpdb->prefix . "posts WHERE " . $wpdb->prefix . "posts.guid = %s", $guid ) ) ){
				$guid = $guid_orig . '-' . $i . '/';
				$post_slug = $post_slug_orig . '-' . $i;
				$i++;
			} 
			
			/* Manually Insert Post */
			$wpdb->query( $wpdb->prepare( "INSERT INTO " . $wpdb->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type, post_excerpt, post_date, post_date_gmt, post_modified, post_modified_gmt, comment_status ) VALUES( %s, %s, %s, %s, %s, %s, %s, NOW( ), UTC_TIMESTAMP( ), NOW( ), UTC_TIMESTAMP( ), 'closed' )", "[ec_store modelnumber=\"" . $sku . "\"]", $post_status, $GLOBALS['language']->convert_text( $title ), $post_slug, $guid, "ec_store", '' ) );
			$post_id = $wpdb->insert_id;
			
			/* Insert Product */
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_product( activate_in_store, show_on_startup, title, model_number, manufacturer_id, price, image1, post_id, use_advanced_optionset, option_id_1, option_id_2, option_id_3, option_id_4, option_id_5, is_shippable, weight, length, width, height, is_taxable, vat_rate, show_stock_quantity, stock_quantity, use_optionitem_quantity_tracking, is_giftcard, is_download, is_donation, is_subscription_item, is_deconetwork, inquiry_mode, catalog_mode ) VALUES( %d, %d, %s, %s, %d, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d )", $activate_in_store, $show_on_startup, $title, $sku, $manufacturer, $price, $image, $post_id, $use_advanced_optionset, $option1, $option2, $option3, $option4, $option5, $is_shippable, $weight, $length, $width, $height, $is_taxable, $vat_rate, $show_stock_quantity, $stock_quantity, $use_optionitem_quantity_tracking, $is_giftcard, $is_download, $is_donation, $is_subscription, $is_deconetwork, $is_inquiry, $is_seasonal ) );
			$product_id =  $wpdb->insert_id;
			
			/* Maybe add Subscription */
			if( $is_subscription ){
				if( get_option( 'ec_option_payment_process_method' ) == 'stripe' || get_option( 'ec_option_payment_process_method' ) == 'stripe_connect' ){
					if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
						$stripe = new ec_stripe( );
					else
						$stripe = new ec_stripe_connect( );
					
					$product_row = $wpdb->get_row( $wpdb->prepare( "SELECT post_id, is_subscription_item, stripe_plan_added, subscription_unique_id, product_id, price, title, subscription_bill_period, subscription_bill_length, trial_period_days FROM ec_product WHERE product_id = %d", $product_id ) );
					$product_row->subscription_unique_id = rand(10000, 10000000);
					$result = $stripe->insert_plan( $product_row );
					$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET subscription_unique_id = %d, stripe_plan_added = 1 WHERE product_id = %d", $product_row->subscription_unique_id, $product_id ) );
				}
			}
			
			do_action( 'wpeasycart_product_added', $product_id, $sku );
			do_action( 'wpeasycart_admin_product_inserted', $product_id, $sku );
			
			return array( 'product_id' => $product_id );
		}
	}
	
	public function save_product_details_basic( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$activate_in_store = $_POST['activate_in_store'];
			$title = stripslashes_deep( $_POST['title'] );
			$post_slug = preg_replace( '/(\-+)/', '-', preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep( strtolower( $_POST['post_slug'] ) ) ) ) );
			while( substr( $post_slug, -1 ) == '-' ){
				$post_slug = substr( $post_slug, 0, strlen( $post_slug ) - 1 );
			}
			while( substr( $post_slug, 0, 1 ) == '-' ){
				$post_slug = substr( $post_slug, 1, strlen( $post_slug ) );
			}
			if( $post_slug == '' ){
				$post_slug = rand( 1000000, 9999999 );
			}
			$model_number = $_POST['model_number'];
			$manufacturer_id = $_POST['manufacturer_id'];
			$price = $_POST['price'];
			$description = $_POST['description'];
			
			if( $this->verify_model_number( ) ){
				if( $product_id != '0' ){
					$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET activate_in_store = %d, title = %s, model_number = %s, manufacturer_id = %d, price = %s, description = %s WHERE product_id = %d", $activate_in_store, $title, $model_number, $manufacturer_id, $price, $description, $product_id ) );
					$product_row = $wpdb->get_row( $wpdb->prepare( "SELECT post_id, is_subscription_item, stripe_plan_added, subscription_unique_id, product_id, price, title, subscription_bill_period, subscription_bill_length, trial_period_days FROM ec_product WHERE product_id = %d", $product_id ) );
					$previous_guid = $wpdb->get_var( $wpdb->prepare( "SELECT " . $wpdb->prefix . "posts.guid FROM " . $wpdb->prefix . "posts WHERE " . $wpdb->prefix . "posts.ID = %d", $product_row->post_id ) );
					
					if( $activate_in_store )
						$status = "publish";
					else
						$status = "private";
						
					$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
					if( strstr( $store_page, '?' ) )
						$guid = $store_page . '&model_number=' . $model_number;
					else if( substr( $store_page, strlen( $store_page ) - 1 ) == '/' )
						$guid = $store_page . $post_slug;
					else
						$guid = $store_page . '/' . $post_slug;
					
					$guid = strtolower( $guid );
					$post_slug_orig = $post_slug;
					$guid_orig = $guid;
					$guid = $guid . '/';
					
					// If GUID has changed, be sure its not a duplicate.
					if( $previous_guid != $guid ){
						$i=1;
						while( $guid_check = $wpdb->get_row( $wpdb->prepare( "SELECT " . $wpdb->prefix . "posts.guid FROM " . $wpdb->prefix . "posts WHERE " . $wpdb->prefix . "posts.guid = %s AND " . $wpdb->prefix . "posts.ID != %d", $guid, $product_row->post_id ) ) ){
							$guid = $guid_orig . '-' . $i . '/';
							$post_slug = $post_slug_orig . '-' . $i;
							$i++;
						}
					}
					
					/* Check the Post Exists, Create if it Doesn't */
					$post_exists = false;
					$post_check = $wpdb->get_row( $wpdb->prepare( "SELECT " . $wpdb->prefix . "posts.guid FROM " . $wpdb->prefix . "posts WHERE " . $wpdb->prefix . "posts.ID = %d", $product_row->post_id ) );
					if( $post_check ){
						/* Manually Update Post */
						$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "posts SET post_content = %s, post_status = %s, post_title = %s, post_name = %s, guid = %s, post_excerpt = %s, post_modified = NOW( ), post_modified_gmt = UTC_TIMESTAMP( ) WHERE ID = %d", "[ec_store modelnumber=\"" . $model_number . "\"]", $status, $GLOBALS['language']->convert_text( $title ), $post_slug, $guid, $description, $product_row->post_id ) );
					}else{
						/* Manually Insert Post */
						$wpdb->query( $wpdb->prepare( "INSERT INTO " . $wpdb->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type, post_excerpt, post_date, post_date_gmt, post_modified, post_modified_gmt, comment_status ) VALUES( %s, %s, %s, %s, %s, %s, %s, NOW( ), UTC_TIMESTAMP( ), NOW( ), UTC_TIMESTAMP( ), 'closed' )", "[ec_store modelnumber=\"" . $model_number . "\"]", $status, $GLOBALS['language']->convert_text( $title ), $post_slug, $guid, "ec_store", $description ) );
						$post_id = $wpdb->insert_id;
						$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET post_id = %d WHERE product_id = %d", $post_id, $product_id ) );
					}
					
					/* Update Subscription Item */
					if( $product_row->is_subscription_item && ( get_option( 'ec_option_payment_process_method' ) == 'stripe' || get_option( 'ec_option_payment_process_method' ) == 'stripe_connect' ) ){
						if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
							$stripe = new ec_stripe( );
						else
							$stripe = new ec_stripe_connect( );
						
						if( $product_row->stripe_plan_added ){
							$stripe_arr = (object) array( "product_id" => $product_row->product_id, "title" => $product_row->title, "trial_period_days" => $product_row->trial_period_days );
							if( $product_row->subscription_unique_id )
								$stripe_arr->product_id = $product_row->subscription_unique_id;
							
							$plan = $stripe->get_plan( $stripe_arr );
							
							if( $plan === false || $price != ( $plan->amount / 100 ) ){ // Doesn't Exist or Price Changed
								$product_row->subscription_unique_id = rand(10000, 10000000);
								$result = $stripe->insert_plan( $product_row );
								$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET subscription_unique_id = %d, stripe_plan_added = 1 WHERE product_id = %d", $product_row->subscription_unique_id, $product_id ) );
							
							}else if( $plan->name != $product_row->title ){ // Plan title only changed, update plan
								$result = $stripe->update_plan( $stripe_arr );
							}
							
						}else{
							$product_row->subscription_unique_id = rand(10000, 10000000);
							$result = $stripe->insert_plan( $product_row );
							$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET subscription_unique_id = %d, stripe_plan_added = 1 WHERE product_id = %d", $product_row->subscription_unique_id, $product_id ) );
						}
					}
					do_action( 'wpeasycart_product_updated', $product_id, $model_number );
					return true;
				}else{
					if( $activate_in_store )
						$status = "publish";
					else
						$status = "private";
					
					$post_slug = preg_replace( '/(\-+)/', '-', preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep( strtolower( $title ) ) ) ) );
					$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
					if( strstr( $store_page, '?' ) )
						$guid = $store_page . '&model_number=' . $model_number;
					else if( substr( $store_page, strlen( $store_page ) - 1 ) == '/' )
						$guid = $store_page . $post_slug;
					else
						$guid = $store_page . '/' . $post_slug;
					
					$guid = strtolower( $guid );
					$post_slug_orig = $post_slug;
					$guid_orig = $guid;
					$guid = $guid . '/';
					$i=1;
					while( $guid_check = $wpdb->get_row( $wpdb->prepare( "SELECT " . $wpdb->prefix . "posts.guid FROM " . $wpdb->prefix . "posts WHERE " . $wpdb->prefix . "posts.guid = %s", $guid ) ) ){
						$guid = $guid_orig . '-' . $i . '/';
						$post_slug = $post_slug_orig . '-' . $i;
						$i++;
					} 
					
					/* Manually Insert Post */
					$wpdb->query( $wpdb->prepare( "INSERT INTO " . $wpdb->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type, post_excerpt, post_date, post_date_gmt, post_modified, post_modified_gmt, comment_status ) VALUES( %s, %s, %s, %s, %s, %s, %s,  NOW( ), UTC_TIMESTAMP( ), NOW( ), UTC_TIMESTAMP( ), 'closed' )", "[ec_store modelnumber=\"" . $model_number . "\"]", $status, $GLOBALS['language']->convert_text( $title ), $post_slug, $guid, "ec_store", $description ) );
					$post_id = $wpdb->insert_id;
					
					/* Insert Product */
					$wpdb->query( $wpdb->prepare( "INSERT INTO ec_product( activate_in_store, title, model_number, manufacturer_id, price, description, post_id, show_on_startup, show_stock_quantity ) VALUES( %d, %s, %s, %d, %s, %s, %d, 1, 0 )", $activate_in_store, $title, $model_number, $manufacturer_id, $price, $description, $post_id ) );
					$product_id = $wpdb->insert_id;
					do_action( 'wpeasycart_product_added', $product_id, $model_number );
					do_action( 'wpeasycart_admin_product_inserted', $product_id, $model_number );
					return $product_id;
				}
				
			}else{
				return false;
			}
		}
	}
	
	public function save_new_optionset( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$option_type = $_POST['ec_new_option_type'];
			$option_name = $_POST['ec_new_option_name'];
			$option_label = $_POST['ec_new_option_label'];
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_option( option_name, option_label, option_type ) VALUES( %s, %s, %s )", $option_name, $option_label, $option_type ) );
			return $wpdb->insert_id;
		}
	}
	
	public function save_new_optionitem( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$option_id = $_POST['ec_new_optionitem_option_id'];
			$order = $_POST['ec_new_optionitem_sort_order'];
			$name = $_POST['ec_new_optionitem_name'];
			$model_number = $_POST['ec_new_optionitem_model_number_extension'];
			$price_adjustment = $_POST['ec_new_optionitem_price_adjustment'];
			$weight_adjustment = $_POST['ec_new_optionitem_weight_adjustment'];
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_optionitem( option_id, optionitem_order, optionitem_name, optionitem_model_number, optionitem_price, optionitem_weight ) VALUES( %d, %d, %s, %s, %s, %s )", $option_id, $order, $name, $model_number, $price_adjustment, $weight_adjustment ) );
		}
	}
	
	public function save_new_adv_optionset( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$option_type = $_POST['ec_new_adv_option_type'];
			$option_name = $_POST['ec_new_adv_option_name'];
			$option_label = $_POST['ec_new_adv_option_label'];
			$option_meta = array(
				"min"	=> $_POST['ec_new_adv_option_meta_min'],
				"max"	=> $_POST['ec_new_adv_option_meta_max'],
				"step"	=> $_POST['ec_new_adv_option_meta_step']
			);
			$option_required = 0;
			if( isset( $_POST['ec_new_adv_option_required'] ) && $_POST['ec_new_adv_option_required'] == '1' )
				$option_required = 1;
			$option_error_text = stripslashes_deep( $_POST['ec_new_adv_option_error_text'] );
			
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_option( option_name, option_label, option_type, option_required, option_error_text, option_meta ) VALUES( %s, %s, %s, %s, %s, %s )", $option_name, $option_label, $option_type, $option_required, $option_error_text, maybe_serialize( $option_meta ) ) );
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
			
			return $option_id;
		}
	}
	
	public function save_new_adv_optionitem( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$option_id = $_POST['ec_new_optionitem_option_id'];
			$order = $_POST['ec_new_optionitem_sort_order'];
			$name = $_POST['ec_new_optionitem_name'];
			$model_number = $_POST['ec_new_optionitem_model_number_extension'];
			$initial_value = '';
			$icon = '';
			$optionitem_initially_selected = false;
			if( isset( $_POST['ec_admin_adv_optionitem_initially_selected'] ) && $_POST['ec_admin_adv_optionitem_initially_selected'] == '1' )
				$optionitem_initially_selected = 1;
			$optionitem_disallow_shipping = false;
			if( isset( $_POST['ec_admin_adv_optionitem_no_shipping'] ) && $_POST['ec_admin_adv_optionitem_no_shipping'] == '1' )
				$optionitem_disallow_shipping = 1;
			$optionitem_allow_download = false;
			if( isset( $_POST['ec_admin_adv_optionitem_allows_download'] ) && $_POST['ec_admin_adv_optionitem_allows_download'] == '1' )
				$optionitem_allow_download = 1;
			
			$price_adjustment_type = $_POST['ec_new_optionitem_price_adjustment_type'];
			$optionitem_price = 0; $optionitem_price_onetime = 0; $optionitem_price_override = -1; $optionitem_price_multiplier = 0;
			if( $price_adjustment_type == 'basic_price' ){
				$optionitem_price = $_POST['ec_new_optionitem_price_adjustment'];
			}else if( $price_adjustment_type == 'one_time_price' ){
				$optionitem_price_onetime = $_POST['ec_new_optionitem_price_adjustment'];
			}else if( $price_adjustment_type == 'override_price' ){
				$optionitem_price_override = $_POST['ec_new_optionitem_price_adjustment'];
			}else if( $price_adjustment_type == 'multiplier_price' ){
				$optionitem_price_multiplier = $_POST['ec_new_optionitem_price_adjustment'];
			}
			
			$weight_adjustment_type = $_POST['ec_new_optionitem_weight_adjustment_type'];
			$optionitem_weight = 0; $optionitem_weight_onetime = 0; $optionitem_weight_override = -1; $optionitem_weight_multiplier = 0;
			if( $weight_adjustment_type == 'basic_weight' ){
				$optionitem_weight = $_POST['ec_new_optionitem_weight_adjustment'];
			}else if( $weight_adjustment_type == 'one_time_weight' ){
				$optionitem_weight_onetime = $_POST['ec_new_optionitem_weight_adjustment'];
			}else if( $weight_adjustment_type == 'override_weight' ){
				$optionitem_weight_override = $_POST['ec_new_optionitem_weight_adjustment'];
			}else if( $weight_adjustment_type == 'multiplier_weight' ){
				$optionitem_weight_multiplier = $_POST['ec_new_optionitem_weight_adjustment'];
			}
			
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_optionitem( 
				option_id, optionitem_name, optionitem_price, optionitem_price_onetime, optionitem_price_override,
				optionitem_price_multiplier, optionitem_weight, optionitem_weight_onetime, optionitem_weight_override,
				optionitem_weight_multiplier, optionitem_order, optionitem_icon, optionitem_initial_value, optionitem_model_number,
				optionitem_allow_download, optionitem_disallow_shipping, optionitem_initially_selected
			) VALUES( 
				%d, %s, %s, %s, %s,
				%s, %s, %s, %s,
				%s, %d, %s, %s, %s, 
				%d, %d, %d
			)", 
			
				$option_id, $name, $optionitem_price, $optionitem_price_onetime, $optionitem_price_override,
				$optionitem_price_multiplier, $optionitem_weight, $optionitem_weight_onetime, $optionitem_weight_override,
				$optionitem_weight_multiplier, $order, $icon, $initial_value, $model_number, 
				$optionitem_allow_download, $optionitem_disallow_shipping, $optionitem_initially_selected
			) );
		}
	}
	
	public function save_product_details_options( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$use_advanced_optionset = $_POST['use_advanced_optionset'];
			$option1 = $_POST['option1'];
			$option2 = $_POST['option2'];
			$option3 = $_POST['option3'];
			$option4 = $_POST['option4'];
			$option5 = $_POST['option5'];
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET use_advanced_optionset = %d, option_id_1 = %d, option_id_2 = %d, option_id_3 = %d, option_id_4 = %d, option_id_5 = %d WHERE product_id = %d", $use_advanced_optionset, $option1, $option2, $option3, $option4, $option5, $product_id ) );
		}
	}
	
	public function add_advanced_option( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$option_id = $_POST['option_id'];
			
			$highest_sort = $wpdb->get_var( $wpdb->prepare( "SELECT option_order FROM ec_option_to_product WHERE product_id = %d ORDER BY option_order DESC", $product_id ) );
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_option_to_product( product_id, option_id, option_order ) VALUES( %d, %d, %d )", $product_id, $option_id, $highest_sort + 1 ) );
			return $wpdb->insert_id;
		}
	}
	
	public function delete_advanced_option( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$option_to_product_id = $_POST['option_to_product_id'];
			
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_option_to_product WHERE option_to_product_id = %d", $option_to_product_id ) );
		}
	}
	
	public function save_product_details_images( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$use_optionitem_images = $_POST['use_optionitem_images'];
			$image1 = stripslashes_deep( $_POST['image1'] );
			$image2 = stripslashes_deep( $_POST['image2'] );
			$image3 = stripslashes_deep( $_POST['image3'] );
			$image4 = stripslashes_deep( $_POST['image4'] );
			$image5 = stripslashes_deep( $_POST['image5'] );
			$optionitem_images = $_POST['optionitem_images'];
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET use_optionitem_images = %d, image1 = %s, image2 = %s, image3 = %s, image4 = %s, image5 = %s WHERE product_id = %d", $use_optionitem_images, $image1, $image2, $image3, $image4, $image5, $product_id ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_optionitemimage WHERE product_id = %d", $product_id ) );
			foreach( $optionitem_images as $optionitem_image ){
				$wpdb->query( $wpdb->prepare( "INSERT INTO ec_optionitemimage( optionitem_id, product_id, image1, image2, image3, image4, image5 ) VALUES( %d, %d, %s, %s, %s, %s, %s )", $optionitem_image['optionitem_id'], $product_id, $optionitem_image['image1'], $optionitem_image['image2'], $optionitem_image['image3'], $optionitem_image['image4'], $optionitem_image['image5'] ) );
			}
		}
	}
	
	public function save_product_details_menus( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$menulevel1_id_1 = $_POST['menulevel1_id_1'];
			$menulevel1_id_2 = $_POST['menulevel1_id_2'];
			$menulevel1_id_3 = $_POST['menulevel1_id_3'];
			$menulevel2_id_1 = $_POST['menulevel2_id_1'];
			$menulevel2_id_2 = $_POST['menulevel2_id_2'];
			$menulevel2_id_3 = $_POST['menulevel2_id_3'];
			$menulevel3_id_1 = $_POST['menulevel3_id_1'];
			$menulevel3_id_2 = $_POST['menulevel3_id_2'];
			$menulevel3_id_3 = $_POST['menulevel3_id_3'];
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET menulevel1_id_1 = %d, menulevel1_id_2 = %d, menulevel1_id_3 = %d, menulevel2_id_1 = %d, menulevel2_id_2 = %d, menulevel2_id_3 = %d, menulevel3_id_1 = %d, menulevel3_id_2 = %d, menulevel3_id_3 = %d WHERE product_id = %d", $menulevel1_id_1, $menulevel1_id_2, $menulevel1_id_3, $menulevel2_id_1, $menulevel2_id_2, $menulevel2_id_3, $menulevel3_id_1, $menulevel3_id_2, $menulevel3_id_3, $product_id ) );
		}
	}
	
	public function add_category( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$category_id = $_POST['category_id'];
			
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_categoryitem( product_id, category_id ) VALUES( %d, %d )", $product_id, $category_id ) );
			return $wpdb->insert_id;
		}
	}
	
	public function delete_category( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$category_id = $_POST['category_id'];
			
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_categoryitem WHERE category_id = %d AND product_id = %d", $category_id, $product_id ) );
		}
	}
	
	public function save_product_details_quantities( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$show_stock_quantity = 0;
			$use_optionitem_quantity_tracking = 0;
			$stock_quantity_type = $_POST['stock_quantity_type'];
			if( $stock_quantity_type == '1' )
				$show_stock_quantity = 1;
			else if( $stock_quantity_type == '2' )
				$use_optionitem_quantity_tracking = 1;
			$stock_quantity = $_POST['stock_quantity'];
			$min_purchase_quantity = $_POST['min_purchase_quantity'];
			$max_purchase_quantity = $_POST['max_purchase_quantity'];
			$model_number = $wpdb->get_var( $wpdb->prepare( "SELECT model_number FROM ec_product WHERE product_id = %d", $product_id ) );
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET show_stock_quantity = %d, use_optionitem_quantity_tracking = %d, stock_quantity = %d, min_purchase_quantity = %d, max_purchase_quantity = %d WHERE product_id = %d", $show_stock_quantity, $use_optionitem_quantity_tracking, $stock_quantity, $min_purchase_quantity, $max_purchase_quantity, $product_id ) );
			do_action( 'wpeasycart_product_updated', $product_id, $model_number );
		}
	}
	
	public function add_optionitem_quantity( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$optionitem_id_1 = $_POST['add_new_optionitem_quantity_1'];
			$optionitem_id_2 = $_POST['add_new_optionitem_quantity_2'];
			$optionitem_id_3 = $_POST['add_new_optionitem_quantity_3'];
			$optionitem_id_4 = $_POST['add_new_optionitem_quantity_4'];
			$optionitem_id_5 = $_POST['add_new_optionitem_quantity_5'];
			$quantity = $_POST['add_new_optionitem_quantity'];
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_optionitemquantity( product_id, optionitem_id_1, optionitem_id_2, optionitem_id_3, optionitem_id_4, optionitem_id_5, quantity ) VALUES( %d, %d, %d, %d, %d, %d, %d )", $product_id, $optionitem_id_1, $optionitem_id_2, $optionitem_id_3, $optionitem_id_4, $optionitem_id_5, $quantity ) );
			$optionitemquantity_id = $wpdb->insert_id;
			$this->update_stock_from_optionitem_quantity( $product_id );
			return $optionitemquantity_id;
		}
	}
	
	public function update_optionitem_quantity( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$optionitemquantity_id = $_POST['optionitemquantity_id'];
			$quantity = $_POST['quantity'];
			$wpdb->query( $wpdb->prepare( "UPDATE ec_optionitemquantity SET quantity = %d WHERE optionitemquantity_id = %d", $quantity, $optionitemquantity_id ) );
			$this->update_stock_from_optionitem_quantity( $product_id );
		}
	}
	
	public function delete_optionitem_quantity( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$optionitemquantity_id = $_POST['optionitemquantity_id'];
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_optionitemquantity WHERE optionitemquantity_id = %d", $optionitemquantity_id ) );
			$this->update_stock_from_optionitem_quantity( $product_id );
		}
	}
	
	public function update_stock_from_optionitem_quantity( $product_id ){
		global $wpdb;
		$total = $wpdb->get_var( $wpdb->prepare( "SELECT SUM( ec_optionitemquantity.quantity ) as quantity FROM ec_optionitemquantity WHERE product_id = %d", $product_id ) );
		$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET stock_quantity = %d WHERE product_id = %d", $total, $product_id ) );
	}
	
	public function save_product_details_pricing( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$model_number = $wpdb->get_var( $wpdb->prepare( "SELECT model_number FROM ec_product WHERE product_id = %d", $product_id ) );
			$list_price = $_POST['list_price'];
			$login_for_pricing = ( isset( $_POST['login_for_pricing'] ) && $_POST['login_for_pricing'] == '1' ) ? 1 : 0;
			$login_for_pricing_user_level = json_encode( $_POST['login_for_pricing_user_level'] );
			$login_for_pricing_label = $_POST['login_for_pricing_label'];
			
			$show_custom_price_range = 0;
			if( isset( $_POST['show_custom_price_range'] ) && $_POST['show_custom_price_range'] == '1' )
				$show_custom_price_range = 1;
			
			$price_range_low = $_POST['price_range_low'];
			$price_range_high = $_POST['price_range_high'];
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET list_price = %s, show_custom_price_range = %d, price_range_low = %s, price_range_high = %s, login_for_pricing = %s, login_for_pricing_user_level = %s, login_for_pricing_label = %s WHERE product_id = %d", $list_price, $show_custom_price_range, $price_range_low, $price_range_high, $login_for_pricing, $login_for_pricing_user_level, $login_for_pricing_label, $product_id ) );
			do_action( 'wpeasycart_product_updated', $product_id, $model_number );
		}
	}
	
	public function add_price_tier( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$price = $_POST['ec_admin_new_price_tier_price'];
			$quantity = $_POST['ec_admin_new_price_tier_quantity'];
			
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_pricetier( product_id, price, quantity ) VALUES( %d, %s, %s )", $product_id, $price, $quantity ) );
			return $wpdb->insert_id;
		}
	}
	
	public function update_price_tier( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$pricetier_id = $_POST['pricetier_id'];
			$product_id = $_POST['product_id'];
			$quantity = $_POST['quantity'];
			$price = $_POST['price'];
			$wpdb->query( $wpdb->prepare( "UPDATE ec_pricetier SET quantity = %s, price = %s WHERE pricetier_id = %d AND product_id = %d", $quantity, $price, $pricetier_id, $product_id ) );
		}
	}
	
	public function delete_price_tier( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$pricetier_id = $_POST['pricetier_id'];
			
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_pricetier WHERE pricetier_id = %d", $pricetier_id ) );
		}
	}
	
	public function add_role_price( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$role_label = $_POST['add_new_role_price_role'];
			$role_price = $_POST['ec_admin_new_role_price'];
			
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_roleprice( product_id, role_label, role_price ) VALUES( %d, %s, %s )", $product_id, $role_label, $role_price ) );
			return $wpdb->insert_id;
		}
	}
	
	public function delete_role_price( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$roleprice_id = $_POST['roleprice_id'];
			
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_roleprice WHERE roleprice_id = %d", $roleprice_id ) );
		}
	}
	
	public function save_product_details_packaging( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$weight = $_POST['weight'];
			$width = $_POST['width'];
			$height = $_POST['height'];
			$length = $_POST['length'];
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET weight = %s, width = %s, height = %s, length = %s WHERE product_id = %d", $weight, $width, $height, $length, $product_id ) );
		}
	}
	
	public function save_product_details_shipping( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$is_shippable = $_POST['is_shippable'];
			$exclude_shippable_calculation = $_POST['exclude_shippable_calculation'];
			$allow_backorders = $_POST['allow_backorders'];
			$backorder_fill_date = stripslashes_deep( $_POST['backorder_fill_date'] );
			$handling_price = $_POST['handling_price'];
			$handling_price_each = $_POST['handling_price_each'];
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET is_shippable = %d, exclude_shippable_calculation = %d, allow_backorders = %d, backorder_fill_date = %s, handling_price = %s, handling_price_each = %s WHERE product_id = %d", $is_shippable, $exclude_shippable_calculation, $allow_backorders, $backorder_fill_date, $handling_price, $handling_price_each, $product_id ) );
		}
	}
	
	public function save_product_details_short_description( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$short_description = stripslashes_deep( $_POST['short_description'] );
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET short_description = %s WHERE product_id = %d", $short_description, $product_id ) );
		}
	}
	
	public function save_product_details_specifications( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$specifications = $_POST['specifications'];
			$use_specifications = 0;
			if( strlen( trim( $specifications ) ) > 0 )
				$use_specifications = 1;
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET specifications = %s, use_specifications = %d WHERE product_id = %d", $specifications, $use_specifications, $product_id ) );
		}
	}
	
	public function save_product_details_order_completed_note( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$order_completed_note = $_POST['order_completed_note'];
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET order_completed_note = %s WHERE product_id = %d", $order_completed_note, $product_id ) );
		}
	}
	
	public function save_product_details_order_completed_email_note( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$order_completed_email_note = $_POST['order_completed_email_note'];
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET order_completed_email_note = %s WHERE product_id = %d", $order_completed_email_note, $product_id ) );
		}
	}
	
	public function save_product_details_order_completed_details_note( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$order_completed_details_note = $_POST['order_completed_details_note'];
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET order_completed_details_note = %s WHERE product_id = %d", $order_completed_details_note, $product_id ) );
		}
	}
	
	public function save_product_details_tags( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$tag_type = $_POST['tag_type'];
			$tag_text = $_POST['tag_text'];
			$tag_bg_color = $_POST['tag_bg_color'];
			$tag_text_color = $_POST['tag_text_color'];
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET tag_type = %d, tag_text = %s, tag_bg_color = %s, tag_text_color = %s WHERE product_id = %d", $tag_type, $tag_text, $tag_bg_color, $tag_text_color, $product_id ) );
		}
	}
	
	public function save_product_details_featured_products( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$featured_product_id_1 = $_POST['featured_product_id_1'];
			$featured_product_id_2 = $_POST['featured_product_id_2'];
			$featured_product_id_3 = $_POST['featured_product_id_3'];
			$featured_product_id_4 = $_POST['featured_product_id_4'];
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET featured_product_id_1 = %d, featured_product_id_2 = %d, featured_product_id_3 = %d, featured_product_id_4 = %d WHERE product_id = %d", $featured_product_id_1, $featured_product_id_2, $featured_product_id_3, $featured_product_id_4, $product_id ) );
		}
	}
	
	public function save_product_details_general_options( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$show_on_startup = $_POST['show_on_startup'];
			$is_special = $_POST['is_special'];
			$use_customer_reviews = $_POST['use_customer_reviews'];
			$is_donation = $_POST['is_donation'];
			$is_giftcard = $_POST['is_giftcard'];
			$inquiry_mode = $_POST['inquiry_mode'];
			$inquiry_url = stripslashes_deep( $_POST['inquiry_url'] );
			$catalog_mode = $_POST['catalog_mode'];
			$catalog_mode_phrase = stripslashes_deep( $_POST['catalog_mode_phrase'] );
			$role_id = stripslashes_deep( $_POST['role_id'] );
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET show_on_startup = %d, is_special = %d, use_customer_reviews = %d, is_donation = %d, is_giftcard = %d, inquiry_mode = %d, inquiry_url = %s, catalog_mode = %d, catalog_mode_phrase = %s, role_id = %s WHERE product_id = %d", $show_on_startup, $is_special, $use_customer_reviews, $is_donation, $is_giftcard, $inquiry_mode, $inquiry_url, $catalog_mode, $catalog_mode_phrase, $role_id, $product_id ) );
		}
	}
	
	public function save_product_details_tax( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$is_taxable = $_POST['is_taxable'];
			$vat_rate = $_POST['vat_rate'];
			$TIC = $_POST['TIC'];
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET is_taxable = %d, vat_rate = %s, TIC = %s WHERE product_id = %d", $is_taxable, $vat_rate, $TIC, $product_id ) );
		}
	}
	
	public function save_product_details_deconetwork( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$is_deconetwork = $_POST['is_deconetwork'];
			$deconetwork_mode = stripslashes_deep( $_POST['deconetwork_mode'] );
			$deconetwork_product_id = stripslashes_deep( $_POST['deconetwork_product_id'] );
			$deconetwork_size_id = stripslashes_deep( $_POST['deconetwork_size_id'] );
			$deconetwork_color_id = stripslashes_deep( $_POST['deconetwork_color_id'] );
			$deconetwork_design_id = stripslashes_deep( $_POST['deconetwork_design_id'] );
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET is_deconetwork = %d, deconetwork_mode = %s, deconetwork_product_id = %s, deconetwork_size_id = %s, deconetwork_color_id = %s, deconetwork_design_id = %s WHERE product_id = %d", $is_deconetwork, $deconetwork_mode, $deconetwork_product_id, $deconetwork_size_id, $deconetwork_color_id, $deconetwork_design_id, $product_id ) );
		}
	}
	
	public function save_product_details_google_merchant( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			
			$array = array(
				'google_product_category' 	=> $_POST['google_product_category'],
				'product_type' 				=> $_POST['product_type'],
				'gtin' 						=> $_POST['gtin'],
				'mpn' 						=> $_POST['mpn'],
				'identifier_exists' 		=> $_POST['identifier_exists'],
				'gender' 					=> $_POST['gender'],
				'age_group' 				=> $_POST['age_group'],
				'size_type' 				=> $_POST['size_type'],
				'size_system' 				=> $_POST['size_system'],
				'item_group_id' 			=> $_POST['item_group_id'],
				'color' 					=> $_POST['color'],
				'material' 					=> $_POST['material'],
				'pattern' 					=> $_POST['pattern'],
				'size' 						=> $_POST['size'],
				'weight_type' 				=> $_POST['weight_type'],
				'shipping_label' 			=> $_POST['shipping_label']
			);
			
			$json_array = json_encode( $array );
			$array_exists = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ec_product_google_attributes WHERE product_id = %d", $product_id ) );
			if( $array_exists ){
				$wpdb->query( $wpdb->prepare( "UPDATE ec_product_google_attributes SET attribute_value = %s WHERE product_id = %d", $json_array, $product_id ) );
			}else{
				$wpdb->query( $wpdb->prepare( "INSERT INTO ec_product_google_attributes( product_id, attribute_value ) VALUES( %d, %s )", $product_id, $json_array ) );
			}
		}
	}
	
	public function save_product_details_subscription( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$is_subscription_item = $_POST['is_subscription_item'];
			$subscription_bill_length = $_POST['subscription_bill_length'];
			$subscription_bill_period = $_POST['subscription_bill_period'];
			$subscription_bill_duration = $_POST['subscription_bill_duration'];
			$trial_period_days = $_POST['trial_period_days'];
			$subscription_signup_fee = $_POST['subscription_signup_fee'];
			$allow_multiple_subscription_purchases = $_POST['allow_multiple_subscription_purchases'];
			$subscription_prorate = $_POST['subscription_prorate'];
			$subscription_plan_id = $_POST['subscription_plan_id'];
			$membership_page = stripslashes_deep( $_POST['membership_page'] );
			
			$intervals = array(
				"day"	=> "D",
				"week"	=> "W",
				"month"	=> "M",
				"year"	=> "Y"
			);
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET is_subscription_item = %d, subscription_bill_length = %s, subscription_bill_period = %s, subscription_bill_duration = %s, trial_period_days = %s, subscription_signup_fee = %s, allow_multiple_subscription_purchases = %s, subscription_prorate = %s, subscription_plan_id = %s, membership_page = %s WHERE product_id = %d", $is_subscription_item, $subscription_bill_length, $subscription_bill_period, $subscription_bill_duration, $trial_period_days, $subscription_signup_fee, $allow_multiple_subscription_purchases, $subscription_prorate, $subscription_plan_id, $membership_page, $product_id ) );
			$product_row = $wpdb->get_row( $wpdb->prepare( "SELECT stripe_plan_added, subscription_unique_id, product_id, price, title, subscription_bill_period, subscription_bill_length, trial_period_days FROM ec_product WHERE product_id = %d", $product_id ) );
			
			if( $is_subscription_item && ( get_option( 'ec_option_payment_process_method' ) == 'stripe' || get_option( 'ec_option_payment_process_method' ) == 'stripe_connect' ) ){
				if(  get_option( 'ec_option_payment_process_method' ) == 'stripe' )
					$stripe = new ec_stripe( );
				else
					$stripe = new ec_stripe_connect( );
					
				if( $product_row->stripe_plan_added ){
					$stripe_arr = (object) array( "product_id" => $product_row->product_id, "title" => $product_row->title, "trial_period_days" => $product_row->trial_period_days );
					if( $product_row->subscription_unique_id )
						$stripe_arr->product_id = $product_row->subscription_unique_id;
					
					$plan = $stripe->get_plan( $stripe_arr );
					
					if( $plan ===  false || ( $plan->amount / 100 ) != $product_row->price || $intervals[$plan->interval] != $subscription_bill_period || $plan->interval_count != $subscription_bill_length ){ // Plan changed, insert a new plan
						$product_row->subscription_unique_id = rand(10000, 10000000);
						$result = $stripe->insert_plan( $product_row );
						$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET subscription_unique_id = %d, stripe_plan_added = 1 WHERE product_id = %d", $product_row->subscription_unique_id, $product_id ) );
						
					}else if( ( !isset( $plan->trial_period_days ) && $trial_period_days != 0 ) || ( isset( $plan->trial_period_days ) && $trial_period_days != $plan->trial_period_days ) ){
						$stripe->update_plan( $stripe_arr );
						
					}
					
				}else{
					$product_row->subscription_unique_id = rand(10000, 10000000);
					$result = $stripe->insert_plan( $product_row );
					$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET subscription_unique_id = %d, stripe_plan_added = 1 WHERE product_id = %d", $product_row->subscription_unique_id, $product_id ) );
					
				}
			}
		}
	}
	
	public function save_product_details_seo( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$seo_description = stripslashes_deep( $_POST['seo_description'] );
			$seo_keywords = stripslashes_deep( $_POST['seo_keywords'] );
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET seo_description = %s, seo_keywords = %s WHERE product_id = %d", $seo_description, $seo_keywords, $product_id ) );
		}
	}
	
	public function save_product_details_downloads( ){
		if( current_user_can( 'manage_options' ) ){
			global $wpdb;
			$product_id = $_POST['product_id'];
			$is_download = $_POST['is_download'];
			$is_amazon_download = $_POST['is_amazon_download'];
			$amazon_key = stripslashes_deep( $_POST['amazon_key'] );
			$download_file_name = stripslashes_deep( $_POST['download_file_name'] );
			$maximum_downloads_allowed = $_POST['maximum_downloads_allowed'];
			$download_timelimit_seconds = $_POST['download_timelimit_seconds'];
			
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET is_download = %d, is_amazon_download = %s, amazon_key = %s, download_file_name = %s, maximum_downloads_allowed = %s, download_timelimit_seconds = %s WHERE product_id = %d", $is_download, $is_amazon_download, $amazon_key, $download_file_name, $maximum_downloads_allowed, $download_timelimit_seconds, $product_id ) );
		}
	}
	
	public function verify_model_number( $model_number = '' ){
		global $wpdb;
		if( $model_number != '' ){
			$possible_match = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.model_number FROM ec_product WHERE ec_product.model_number = %s", $model_number ) );
			if( $possible_match )
				return false;
		}else{
			$product_id = $_POST['product_id'];
			$current_model = $wpdb->get_var( $wpdb->prepare( "SELECT ec_product.model_number FROM ec_product WHERE ec_product.product_id = %d", $product_id ) );
			$model_number = $_POST['model_number'];
			if( !preg_match( '/^[a-zA-Z0-9-]*$/', $model_number ) ){
				return false;
				
			}else if( $product_id == '0' || $current_model != $model_number ){
				$possible_match = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.model_number FROM ec_product WHERE ec_product.model_number = %s", $model_number ) );
				if( $possible_match )
					return false;
			}
		}
		return true;
	}
	
	public function run_importer() {
		
		global $wpdb;
		$this->db = $wpdb;
		$this->error_list = "";
		$this->product_id_index = -1;
		$this->post_id_index = -1;
		$this->model_number_index = -1;
		$this->title_index = -1;
		$this->activate_in_store_index = -1;
		$this->is_subscription_index = -1;
		$this->bill_period_index = -1;
		$this->bill_length_index = -1;
		$this->trial_period_index = -1;
		$this->use_advanced_optionset_index = -1;
		$this->advanced_option_ids_index = -1;
        $this->categories_index = -1;
		$this->limit = 20;
		
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
			
			/** Setup valid value check arrays */
			$valid_product_ids = array( );
			$existing_model_numbers = array( );
			$valid_product_ids_result = $this->db->get_results( "SELECT product_id FROM ec_product", ARRAY_N );
			$existing_model_numbers_result = $this->db->get_results( "SELECT model_number FROM ec_product", ARRAY_N );
			
			foreach( $valid_product_ids_result as $product_id ){
				$valid_product_ids[] = $product_id[0];
			}
			
			foreach( $existing_model_numbers_result as $model_number ){
				$existing_model_numbers[] = $model_number[0];
			}
			
			/* Setup and test headers */
			$valid_headers_result = $this->db->get_results( "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`='ec_product'", ARRAY_N );
			$valid_headers = array( );
			foreach( $valid_headers_result as $header ){
				$valid_headers[] = $header[0];
			}
			$valid_headers[] = 'advanced_option_ids';
			$this->headers = fgetcsv( $file );
			
			for( $i=0; $i<count( $this->headers ); $i++ ){
				
				$this->headers[$i] = trim( $this->headers[$i] );
				
				if( $this->headers[$i] == chr(0xEF) . chr(0xBB) . chr(0xBF) . "product_id" || $this->headers[$i] == "product_id" ){ // do not add product id to list
					$this->product_id_index = $i;
					
				}else if($this->headers[$i] == "post_id" ){ // do not add post id to list
					$this->post_id_index = $i;
					
				}else if($this->headers[$i] == "activate_in_store" ){ // do not add post id to list
					$this->activate_in_store_index = $i;
					
				}else if($this->headers[$i] == "model_number" ){ // use to check for errors
					$this->model_number_index = $i;
				
				}else if($this->headers[$i] == "title" ){ // use to check for errors
					$this->title_index = $i;
				
				}else if($this->headers[$i] == "price" ){ // use to check for errors
					$this->price_index = $i;
				
				}else if($this->headers[$i] == "list_price" ){ // use to check for errors
					$this->list_price_index = $i;
				
				}else if($this->headers[$i] == "is_subscription_item" ){ // use to check for errors
					$this->is_subscription_index = $i;
				
				}else if($this->headers[$i] == "subscription_bill_period" ){ // use to check for errors
					$this->bill_period_index = $i;
				
				}else if($this->headers[$i] == "subscription_bill_length" ){ // use to check for errors
					$this->bill_length_index = $i;
				
				}else if($this->headers[$i] == "trial_period_days" ){ // use to check for errors
					$this->trial_period_index = $i;
				
				}else if($this->headers[$i] == "use_advanced_optionset" ){ // use to check for errors
					$this->use_advanced_optionset_index = $i;
				
				}else if($this->headers[$i] == "advanced_option_ids" ){ // use to check for errors
					$this->advanced_option_ids_index = $i;
				
				}else if($this->headers[$i] == "categories" ){ // use to check for errors
					$this->categories_index = $i;
				
				}else if( !in_array( $this->headers[$i], $valid_headers ) ){ // error, invalid column
					echo "You have an invalid column header at column " . $i . " (value " . $this->headers[$i] . "), please remove or correct the label of that column to continue.";
					
				}
				
			}
			
			if( $this->product_id_index == -1 ){
				echo __( "Missing `product_id` Key field! Values for additions should be 0, updates should be the exported product_id value.", 'wp-easycart' );
			}
			
			if( $this->product_id_index == -1 ){
				echo __( "Missing `post_id` Key field! Values for additions should be 0, updates should be the exported post_id value.", 'wp-easycart' );
			}
			
			if( $this->model_number_index == -1 ){
				echo __( "Missing `model_number` Key field! Values must be unique from other imported products and those products already in your store.", 'wp-easycart' );
			}
			
			if( $this->activate_in_store_index == -1 ){
				echo __( "Missing `activate_in_storck` Key field! Value of 0 or 1 is required.", 'wp-easycart' );
			}
			
			if( $this->title_index == -1 ){
				echo __( "Missing `title` Key field! No value is required, but you must have the key field present.", 'wp-easycart' );
			}
			
			/* SETUP basic SQL calls */
			$insert_sql = "INSERT INTO ec_product(";
			$update_sql = "UPDATE ec_product SET ";
			
			$first = true;
			
			for( $i=0; $i<count( $this->headers ); $i++ ){
				
				if( $i != $this->product_id_index && $i != $this->post_id_index && $i != $this->advanced_option_ids_index && $i != $this->categories_index ){ // Skip rows with product id and post id
					if( !$first ){
						$insert_sql .= ",";
						$update_sql .= ",";
					}
					
					$insert_sql .= "`" . $this->headers[$i] . "`";
					$update_sql .= "`" . $this->headers[$i] . "`=%s";
					$first = false;
				}
			}
			
			$insert_sql .= ", `post_id`) VALUES(";
			
			$first = true;
			
			for( $i=0; $i<count( $this->headers ); $i++ ){
				if( $i != $this->product_id_index && $i != $this->post_id_index && $i != $this->advanced_option_ids_index && $i != $this->categories_index ){ // Skip rows with product id and post id
					if( !$first )
						$insert_sql .= ",";
						
					$insert_sql .= "%s";
					$first = false;
				}
			}
			
			$insert_sql .= ",%d)";
			$update_sql .= " WHERE ec_product.product_id = %s";
			
			/* Start through the rows */
			$current_iteration = 0;
			$eof_reached = false;
			
			while( !feof( $file ) && !$eof_reached ){ // each time through, run up to the limit of items until eof hit.
				
				$rows = array( );
			
				for( $current_row = 0; !feof( $file ) && !$eof_reached && $current_row < $this->limit; $current_row++ ){
			
					$this_row = fgetcsv( $file );
				
					if( strlen( trim( $this_row[$this->model_number_index] ) ) <= 0 ){ // checking for file with extra rows that are empty
						$eof_reached = true;
					
					}else{
						$rows[] = $this_row;
					
					}
					
				}
				
				/* Start processing of rows collected in this interation */
				for( $i=0; $i<count( $rows ); $i++ ){
						
					$product_id = $rows[$i][$this->product_id_index];
					$post_id = $rows[$i][$this->post_id_index];
					$model_number = $rows[$i][$this->model_number_index];
					
					if( $rows[$i][$this->product_id_index] != 0 && $rows[$i][$this->product_id_index] != "" ){ // product_id is available
						
						if( !in_array( $product_id, $valid_product_ids ) ){
							
							$this->error_list .= sprintf( __( "Product %s on line %s failed to update, invalid product_id (if you are trying to add a new product use 0 for the product_id)", 'wp-easycart' ), $product_id, ( ( $current_iteration * $this->limit ) + ($i+1) ) ) . "\r";
							
						}else{ // Valid ID, lets update
							
							$existing_model_numbers[] = $model_number;
							
							$update_vals = array( );
							for( $j=0; $j<count( $rows[$i] ); $j++ ){
								if( $j != $this->product_id_index && $j != $this->post_id_index && $j != $this->advanced_option_ids_index && $j != $this->categories_index ){
									$rows[$i][$j] = html_entity_decode( preg_replace( "/U\+([0-9A-F]{4})/", "&#x\\1;", $rows[$i][$j] ), ENT_NOQUOTES, 'UTF-8' );
									if( $j == $this->price_index || $j == $this->list_price_index ){
										//$update_vals[] = \ForceUTF8\Encoding::fixUTF8( str_replace( ',', '', $rows[$i][$j] ) );
										$update_vals[] = str_replace( ',', '', $rows[$i][$j] );
									}else if( $j == $this->model_number_index ){
										$chars = "!@#$%^&*()+={}[]|\'\";:,<.>/?`~*";
										$pattern = "/[".preg_quote($chars, "/")."]/";
										//$update_vals[] = \ForceUTF8\Encoding::fixUTF8( preg_replace( $pattern, "", $rows[$i][$j] ) );
										$update_vals[] = preg_replace( $pattern, "", $rows[$i][$j] );
									}else{
										//$update_vals[] = \ForceUTF8\Encoding::fixUTF8( $rows[$i][$j] );
										$update_vals[] = $rows[$i][$j];
									}
								}
							}
							$update_vals[] = $product_id; // Add product id last for the update
							
							$result = $this->db->query( $this->db->prepare( $update_sql, $update_vals ) );
							if( $result === false ){
								$this->error_list .= sprintf( __( "Product on line %s failed to update due to an error.", 'wp-easycart' ), ( ( $current_iteration * $this->limit ) + ($i+1) ) ) . "<br />";	
								echo $this->error_list;
								//die();
							}
							
						
							// Update the WordPress Post
							if( $rows[$i][$this->activate_in_store_index] )
								$status = "publish";
							else
								$status = "private";
							
							/* Manually Update Post */
							$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "posts SET post_content = %s, post_status = %s, post_title = %s, post_modified = NOW( ), post_modified_gmt = UTC_TIMESTAMP( ) WHERE ID = %d", "[ec_store modelnumber=\"" . $rows[$i][$this->model_number_index] . "\"]", $status, $GLOBALS['language']->convert_text( $rows[$i][$this->title_index] ), $post_id ) );
							
							// If Advanced Option, Lets 
							if( $this->use_advanced_optionset_index != -1 && $this->advanced_option_ids_index != -1 && $rows[$i][$this->use_advanced_optionset_index] == '1' ){
								$advanced_option_ids_string = str_replace( " ", "", $rows[$i][$this->advanced_option_ids_index] );
								$advanced_option_ids = explode( ',', $advanced_option_ids_string );
								if( count( $advanced_option_ids ) > 0 && trim( $advanced_option_ids[0] ) != "" ){
									$wpdb->query( $wpdb->prepare( "DELETE FROM ec_option_to_product WHERE product_id = %d", $product_id ) );
									$new_adv_opt_sql = "INSERT INTO ec_option_to_product( option_id, product_id ) VALUES";
									for( $adv_ins_index=0; $adv_ins_index<count( $advanced_option_ids ); $adv_ins_index++ ){
										if( $adv_ins_index != 0 )
											$new_adv_opt_sql .= ",";
										$new_adv_opt_sql .= $wpdb->prepare( "(%d, %d)", $advanced_option_ids[$adv_ins_index], $product_id );
									}
									$wpdb->query( $new_adv_opt_sql );
								}
							}
                            
                            // Process categories if available
                            if( $this->categories_index != -1 ){
                                $categories_ids_string = str_replace( " ", "", $rows[$i][$this->categories_index] );
								$categories_ids = explode( ',', $categories_ids_string );
                                $wpdb->query( $wpdb->prepare( "DELETE FROM ec_categoryitem WHERE product_id = %d;", $product_id ) );
                                if( count( $categories_ids ) > 0 ){
                                    $sql = 'INSERT INTO ec_categoryitem( product_id, category_id ) VALUES';
                                    for( $j=0; $j<count( $categories_ids ); $j++ ){
                                        if( $j > 0 ){
                                            $sql .= ',';
                                        }
                                        $sql .= $wpdb->prepare( '( %d, %d)', $product_id, $categories_ids[$j] );
                                    }
                                }
                                $wpdb->query( $sql );
                            }
							
						}// Check for valid product_id
						
					}else{
						
						if( in_array( $model_number, $existing_model_numbers ) ){
							
							$this->error_list .= sprintf( __( "Product on line %s failed to update, duplicate model number listed for this product.", 'wp-easycart' ), ( ( $current_iteration * $this->limit ) + ($i+1) ) ) . "\r";
							
						}else{ // model number is new, we can insert
							
							$existing_model_numbers[] = $model_number;
							$insert_vals = array( );
							for( $j=0; $j<count( $rows[$i] ); $j++ ){
								if( $j != $this->product_id_index && $j != $this->post_id_index && $j != $this->advanced_option_ids_index ){
									$rows[$i][$j] = html_entity_decode( preg_replace( "/U\+([0-9A-F]{4})/", "&#x\\1;", $rows[$i][$j] ), ENT_NOQUOTES, 'UTF-8' );
									if( $j == $this->price_index || $j == $this->list_price_index ){
										//$insert_vals[] = \ForceUTF8\Encoding::fixUTF8( str_replace( ',', '', $rows[$i][$j] ) );
										$insert_vals[] = str_replace( ',', '', $rows[$i][$j] );
									}else if( $j == $this->model_number_index ){
										$chars = "!@#$%^&*()+={}[]|\'\";:,<.>/?`~*";
										$pattern = "/[".preg_quote($chars, "/")."]/";
										//$insert_vals[] = \ForceUTF8\Encoding::fixUTF8( preg_replace( $pattern, "", $rows[$i][$j] ) );
										$insert_vals[] = preg_replace( $pattern, "", $rows[$i][$j] );
									}else{
										//$insert_vals[] = \ForceUTF8\Encoding::fixUTF8( $rows[$i][$j] );
										$insert_vals[] = $rows[$i][$j];
									}
								}
							}
							
							// Insert WordPress Post
							if( $rows[$i][$this->activate_in_store_index] )
								$status = "publish";
							else
								$status = "private";
							
							$post_slug = preg_replace( '/(\-+)/', '-', preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep( strtolower( $rows[$i][$this->title_index] ) ) ) ) );
							$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
							if( strstr( $store_page, '?' ) )
								$guid = $store_page . '&model_number=' . $rows[$i][$this->model_number_index];
							else if( substr( $store_page, strlen( $store_page ) - 1 ) == '/' )
								$guid = $store_page . $post_slug;
							else
								$guid = $store_page . '/' . $post_slug;
							
							$guid = strtolower( $guid );
							$post_slug_orig = $post_slug;
							$guid_orig = $guid;
							$guid = $guid . '/';
							$k=1;
							while( $guid_check = $wpdb->get_row( $wpdb->prepare( "SELECT " . $wpdb->prefix . "posts.guid FROM " . $wpdb->prefix . "posts WHERE " . $wpdb->prefix . "posts.guid = %s", $guid ) ) ){
								$guid = $guid_orig . '-' . $k . '/';
								$post_slug = $post_slug_orig . '-' . $k;
								$k++;
							} 
							
							/* Manually Insert Post */
							$wpdb->query( $wpdb->prepare( "INSERT INTO " . $wpdb->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type, post_date, post_date_gmt, post_modified, post_modified_gmt, comment_status ) VALUES( %s, %s, %s, %s, %s, %s, NOW( ), UTC_TIMESTAMP( ), NOW( ), UTC_TIMESTAMP( ), 'closed' )", "[ec_store modelnumber=\"" . $rows[$i][$this->model_number_index] . "\"]", $status, $GLOBALS['language']->convert_text( $rows[$i][$this->title_index] ), $post_slug, $guid, "ec_store" ) );
							$post_id = $wpdb->insert_id;
							
							$insert_vals[] = $post_id;
							
							$this->db->query( $this->db->prepare( $insert_sql, $insert_vals ) );
							$product_id = $this->db->insert_id;
							
							if( !$product_id ){ // never inserted
								
								wp_delete_post( $post_id, true );
								$this->error_list .= sprintf( __( "Product on line %s never inserted", 'wp-easycart' ), ( ( $current_iteration * $this->limit ) + ($i+1) ) ) . "\r";
								
							}
							
							if( $this->is_subscription_index != -1 && $product_id && ( get_option( 'ec_option_payment_process_method' ) == "stripe" || get_option( 'ec_option_payment_process_method' ) == "stripe_connect" ) && $rows[$i][$this->is_subscription_index] == "1" ){
								$stripe_plan = ( object ) array(
										"price" 						=> $rows[$i][$this->price_index],
										"product_id" 					=> $product_id,
										"title"							=> $rows[$i][$this->title_index]
								);
								if( $this->bill_period_index != -1 )
									$stripe_plan->subscription_bill_period = $rows[$i][$this->bill_period_index];
								else
									$stripe_plan->subscription_bill_period = 'M';
								if( $this->bill_length_index != -1 )
									$stripe_plan->subscription_bill_length = $rows[$i][$this->bill_length_index];
								else
									$stripe_plan->subscription_bill_length = 1;
								if( $this->trial_period_index != -1 )
									$stripe_plan->trial_period_days = $rows[$i][$this->trial_period_index];
								else
									$stripe_plan->trial_period_days = 0;
								
								if( get_option( 'ec_option_payment_process_method' ) == "stripe" )
									$stripe = new ec_stripe( );
								else
									$stripe = new ec_stripe_connect( );
								$response = $stripe->insert_plan( $stripe_plan );
								
							}
							
							// If Advanced Option, Lets Insert
							if( $this->use_advanced_optionset_index != -1 && $this->advanced_option_ids_index != -1 && $rows[$i][$this->use_advanced_optionset_index] ){
								$advanced_option_ids_string = str_replace( " ", "", $rows[$i][$this->advanced_option_ids_index] );
								$advanced_option_ids = explode( ',', $advanced_option_ids_string );
								if( count( $advanced_option_ids ) > 0 && trim( $advanced_option_ids[0] ) != "" ){
									$new_adv_opt_sql = "INSERT INTO ec_option_to_product( option_id, product_id ) VALUES";
									for( $adv_ins_index=0; $adv_ins_index<count( $advanced_option_ids ); $adv_ins_index++ ){
										if( $adv_ins_index != 0 )
											$new_adv_opt_sql .= ",";
										$new_adv_opt_sql .= $wpdb->prepare( "(%d, %d)", $advanced_option_ids[$adv_ins_index], $product_id );
									}
									$wpdb->query( $new_adv_opt_sql );
								}
							}
                            
                            // Process categories if available
                            if( $this->categories_index != -1 ){
                                $categories_ids_string = str_replace( " ", "", $rows[$i][$this->categories_index] );
								$categories_ids = explode( ',', $categories_ids_string );
                                $wpdb->query( $wpdb->prepare( "DELETE FROM ec_categoryitem WHERE product_id = %d;", $product_id ) );
                                if( count( $categories_ids ) > 0 ){
                                    $sql = 'INSERT INTO ec_categoryitem( product_id, category_id ) VALUES';
                                    for( $j=0; $j<count( $categories_ids ); $j++ ){
                                        if( $j > 0 ){
                                            $sql .= ',';
                                        }
                                        $sql .= $wpdb->prepare( '( %d, %d)', $product_id, $categories_ids[$j] );
                                    }
                                }
                                $wpdb->query( $sql );
                            }
							
						}// model number duplicate check
						
					}// close check for insert or update
					
				} // Close iteration for loop
				
				unset( $rows );
				
				$current_iteration++;
				
			}
			
			unset( $this->headers );
			
			fclose( $file );
			
			if( $this->error_list == "" ){
				echo "success" ;
			}else{
				echo $this->error_list;
			}
			
			
		} else {
			echo __( 'No URL', 'wp-easycart' );
		}
		die( );
	}
	
	function get_product_link( $product_id ){
		global $wpdb;
		$product = $wpdb->get_row( $wpdb->prepare( "SELECT post_id, model_number FROM ec_product WHERE product_id = %d", $product_id ) );
		if( $product ){
			if( !get_option( 'ec_option_use_old_linking_style' ) ){
				return get_permalink( $product->post_id );
			}else{
				$storepageid = get_option( 'ec_option_storepage' );
				$store_page = get_permalink( $storepageid );
				if( substr_count( $store_page, '?' ) )
					$permalink_divider = "&";
				else
					$permalink_divider = "?";
				return $store_page . $permalink_divider . "model_number=" . $product->model_number;
			}
		}else{
			return "";
		}
	}
	
	public function save_product_advanced_option_order( ){
		global $wpdb;
		$sort_order = $_POST['sort_order'];
		$product_id = $_POST['product_id'];
		
		foreach( $sort_order as $sort_item ){
			$wpdb->query( $wpdb->prepare( "UPDATE ec_option_to_product SET option_order = %d WHERE option_to_product_id = %d AND product_id = %d", $sort_item['order'], $sort_item['id'], $product_id ) );
		}
	}
	
	public function save_logic( ){
		global $wpdb;
		$option_to_product_id = $_POST['option_to_product_id'];
		$enabled = $_POST['enabled'];
		$show_field = $_POST['show_field'];
		$and_rules = $_POST['and_rules'];
		$rules = json_decode( stripslashes( $_POST['rules'] ) );
		
		$meta = (object) array(
			"enabled"		=> $enabled,
			"show_field"	=> $show_field,
			"and_rules"		=> $and_rules,
			"rules"			=> $rules
		);
		$wpdb->query( $wpdb->prepare( "UPDATE ec_option_to_product SET conditional_logic = %s WHERE option_to_product_id = %d", json_encode( $meta ), $option_to_product_id ) );
	}
    
    public function get_quick_product( $product_id ){
        global $wpdb;
        $product = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_product WHERE product_id = %d", $product_id ) );
        $product->price = $GLOBALS['currency']->get_number_only( $product->price );
        $product->list_price = $GLOBALS['currency']->get_number_only( $product->list_price );
        return $product;
    }
    
    public function product_quick_update( ){
        global $wpdb;
        $is_taxable = $vat_rate = $show_stock_quantity = $use_optionitem_quantity_tracking = $stock_quantity = 0;
        if( $_POST['is_taxable'] == '1' || $_POST['is_taxable'] == '3' ){
            $is_taxable = 1;
        }
        if( $_POST['is_taxable'] == '2' || $_POST['is_taxable'] == '3' ){
            $vat_rate = 1;
        }
        if( $_POST['stock_option'] != '0' ){
            $show_stock_quantity = 1;
            $stock_quantity = $_POST['stock_quantity'];
        }
        if( $_POST['stock_option'] == '2' ){
            $use_optionitem_quantity_tracking = 1;
        }
        $wpdb->query( $wpdb->prepare( "UPDATE ec_product SET 
            activate_in_store = %d, show_on_startup = %d, title = %s, model_number = %s, manufacturer_id = %d,
            price = %s, list_price = %s, image1 = %s, show_stock_quantity = %d, use_optionitem_quantity_tracking = %d, stock_quantity = %d, is_shippable = %d,
            weight = %s, length = %s, width = %s, height = %s, is_taxable = %d, vat_rate = %s
            WHERE product_id = %d",
            $_POST['activate_in_store'], $_POST['show_on_startup'], $_POST['title'], $_POST['model_number'], $_POST['manufacturer_id'],
            $_POST['price'], $_POST['list_price'], $_POST['image1'], $show_stock_quantity, $use_optionitem_quantity_tracking, $stock_quantity, $_POST['is_shippable'],
            $_POST['weight'], $_POST['length'], $_POST['width'], $_POST['height'], $is_taxable, $vat_rate, $_POST['product_id']
        ) );
    }
	
}
endif; // End if class_exists check

function wp_easycart_admin_products( ){
	return wp_easycart_admin_products::instance( );
}
wp_easycart_admin_products( );

add_action( 'wp_ajax_ec_admin_ajax_save_product_settings', 'ec_admin_ajax_save_product_settings' );
function ec_admin_ajax_save_product_settings( ){
	wp_easycart_admin_products( )->save_product_settings( );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_product_list_display', 'ec_admin_ajax_save_product_list_display' );
function ec_admin_ajax_save_product_list_display( ){	
	wp_easycart_admin_products( )->save_product_list_display( );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_customer_review_display', 'ec_admin_ajax_save_customer_review_display' );
function ec_admin_ajax_save_customer_review_display( ){
	wp_easycart_admin_products( )->save_customer_review_display( );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_product_details_display', 'ec_admin_ajax_save_product_details_display' );
function ec_admin_ajax_save_product_details_display( ){
	wp_easycart_admin_products( )->save_product_details_display( );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_price_display', 'ec_admin_ajax_save_price_display' );
function ec_admin_ajax_save_price_display( ){
	wp_easycart_admin_products( )->save_price_display( );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_inventory_options', 'ec_admin_ajax_save_inventory_options' );
function ec_admin_ajax_save_inventory_options( ){
	wp_easycart_admin_products( )->save_inventory_options( );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_product_details_basic', 'ec_admin_ajax_save_product_details_basic' );
function ec_admin_ajax_save_product_details_basic( ){
	global $wpdb;
	$result = wp_easycart_admin_products( )->save_product_details_basic( );
	if( $_POST['product_id'] == '0' )
		$product_id = $result;
	else
		$product_id = $_POST['product_id'];
	$guid = $wpdb->get_var( $wpdb->prepare( "SELECT " . $wpdb->prefix . "posts.guid FROM ec_product LEFT JOIN " . $wpdb->prefix . "posts ON " . $wpdb->prefix . "posts.ID = ec_product.post_id WHERE product_id = %d", $product_id ) );
	echo json_encode( array( 
		'product_id' => esc_attr( $product_id ), 
		'link' => wp_easycart_admin_products( )->get_product_link( $product_id ),
		'post_slug' => basename( $guid )
	) );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_options', 'ec_admin_ajax_save_product_details_options' );
function ec_admin_ajax_save_product_details_options( ){
	wp_easycart_admin_products( )->save_product_details_options( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_product_details_add_advanced_option', 'ec_admin_ajax_product_details_add_advanced_option' );
function ec_admin_ajax_product_details_add_advanced_option( ){
	$option_to_product_id = wp_easycart_admin_products( )->add_advanced_option( );
	$product_id = $_POST['product_id'];
	global $wpdb;
	$advanced_options = $wpdb->get_results( $wpdb->prepare( "SELECT ec_option.*, ec_option_to_product.product_id, ec_option_to_product.option_to_product_id, ec_option_to_product.conditional_logic FROM ec_option_to_product, ec_option WHERE ec_option_to_product.product_id = %d AND ec_option.option_id = ec_option_to_product.option_id ORDER BY ec_option_to_product.option_order", $product_id ) );
	foreach( $advanced_options as $advanced_option ){
		echo '<div class="ec_admin_option_row" id="ec_admin_product_details_advanced_option_row_' . $advanced_option->option_to_product_id . '" data-id="' . $advanced_option->option_to_product_id . '"><span>' . $advanced_option->option_name . '</span><span>' . $advanced_option->option_type . '</span><span>' . ( $advanced_option->option_required ? 'Yes' : 'No' ) . '</span><span><a href="" onclick="return ec_admin_product_details_delete_advanced_option( \'' . $advanced_option->option_to_product_id . '\' );"><div class="dashicons-before dashicons-trash"></div></a></span>';
		do_action( 'wp_easycart_admin_product_advanced_option_row_end', $advanced_option );
		echo '</div>';
	}
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_product_details_delete_advanced_option', 'ec_admin_ajax_product_details_delete_advanced_option' );
function ec_admin_ajax_product_details_delete_advanced_option( ){
	global $wpdb;
	$option_to_product_id = $_POST['option_to_product_id'];
	$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT product_id FROM ec_option_to_product WHERE option_to_product_id = %d", $option_to_product_id ) );
	wp_easycart_admin_products( )->delete_advanced_option( );
	$advanced_options = $wpdb->get_results( $wpdb->prepare( "SELECT ec_option.*, ec_option_to_product.product_id, ec_option_to_product.option_to_product_id, ec_option_to_product.conditional_logic FROM ec_option_to_product, ec_option WHERE ec_option_to_product.product_id = %d AND ec_option.option_id = ec_option_to_product.option_id ORDER BY ec_option_to_product.option_order", $product_id ) );
	foreach( $advanced_options as $advanced_option ){
		echo '<div class="ec_admin_option_row" id="ec_admin_product_details_advanced_option_row_' . $advanced_option->option_to_product_id . '" data-id="' . $advanced_option->option_to_product_id . '"><span>' . $advanced_option->option_name . '</span><span>' . $advanced_option->option_type . '</span><span>' . ( $advanced_option->option_required ? 'Yes' : 'No' ) . '</span><span><a href="" onclick="return ec_admin_product_details_delete_advanced_option( \'' . $advanced_option->option_to_product_id . '\' );"><div class="dashicons-before dashicons-trash"></div></a></span>';
		do_action( 'wp_easycart_admin_product_advanced_option_row_end', $advanced_option );
		echo '</div>';
	}
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_images', 'ec_admin_ajax_save_product_details_images' );
function ec_admin_ajax_save_product_details_images( ){
	wp_easycart_admin_products( )->save_product_details_images( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_menus', 'ec_admin_ajax_save_product_details_menus' );
function ec_admin_ajax_save_product_details_menus( ){
	wp_easycart_admin_products( )->save_product_details_menus( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_product_details_add_category', 'ec_admin_ajax_product_details_add_category' );
function ec_admin_ajax_product_details_add_category( ){
	$categoryitem_id = wp_easycart_admin_products( )->add_category( );
	global $wpdb;
	$category = $wpdb->get_row( $wpdb->prepare( "SELECT ec_categoryitem.category_id, ec_category.category_name FROM ec_categoryitem, ec_category WHERE ec_categoryitem.categoryitem_id = %d AND ec_category.category_id = ec_categoryitem.category_id", $categoryitem_id ) );
	echo '<div class="ec_admin_category_row" id="ec_admin_product_details_category_row_' . $category->category_id . '"><span>' . $category->category_name . '</span><span><a href="" onclick="return ec_admin_product_details_delete_category( \'' . $category->category_id . '\' );"><div class="dashicons-before dashicons-trash"></div></a></span></div>';
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_product_details_delete_category', 'ec_admin_ajax_product_details_delete_category' );
function ec_admin_ajax_product_details_delete_category( ){
	wp_easycart_admin_products( )->delete_category( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_quantities', 'ec_admin_ajax_save_product_details_quantities' );
function ec_admin_ajax_save_product_details_quantities( ){
	wp_easycart_admin_products( )->save_product_details_quantities( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_product_details_add_optionitem_quantity', 'ec_admin_ajax_product_details_add_optionitem_quantity' );
function ec_admin_ajax_product_details_add_optionitem_quantity( ){
	$optionitemquantity_id = wp_easycart_admin_products( )->add_optionitem_quantity( );
	global $wpdb;
	$option_item_quantity = $wpdb->get_row( $wpdb->prepare( "SELECT 
				ec_optionitemquantity.*, 
				optionitem1.optionitem_name as optionitem_name_1, 
				optionitem2.optionitem_name as optionitem_name_2, 
				optionitem3.optionitem_name as optionitem_name_3, 
				optionitem4.optionitem_name as optionitem_name_4, 
				optionitem5.optionitem_name as optionitem_name_5
			FROM 
				ec_optionitemquantity 
				LEFT JOIN ec_optionitem AS optionitem1 ON ( optionitem1.optionitem_id = ec_optionitemquantity.optionitem_id_1 )
				LEFT JOIN ec_optionitem AS optionitem2 ON ( optionitem2.optionitem_id = ec_optionitemquantity.optionitem_id_2 )
				LEFT JOIN ec_optionitem AS optionitem3 ON ( optionitem3.optionitem_id = ec_optionitemquantity.optionitem_id_3 )
				LEFT JOIN ec_optionitem AS optionitem4 ON ( optionitem4.optionitem_id = ec_optionitemquantity.optionitem_id_4 )
				LEFT JOIN ec_optionitem AS optionitem5 ON ( optionitem5.optionitem_id = ec_optionitemquantity.optionitem_id_5 )
			WHERE 
				ec_optionitemquantity.optionitemquantity_id = %d", $optionitemquantity_id ) );
	echo '<div id="ec_admin_product_details_optionitem_quantity_row_' . $option_item_quantity->optionitemquantity_id . '" class="ec_admin_opionitem_quantity_row"><label>';
				echo $option_item_quantity->optionitem_name_1;
				if( $option_item_quantity->optionitem_id_2 )
					echo ', ' . $option_item_quantity->optionitem_name_2;
				if( $option_item_quantity->optionitem_id_3 )
					echo ', ' . $option_item_quantity->optionitem_name_3;
				if( $option_item_quantity->optionitem_id_4 )
					echo ', ' . $option_item_quantity->optionitem_name_4;
				if( $option_item_quantity->optionitem_id_5 )
					echo ', ' . $option_item_quantity->optionitem_name_5;
				
				echo '</label><input type="number" name="optionitem_quantity_' . $option_item_quantity->optionitemquantity_id . '" id="optionitem_quantity_' . $option_item_quantity->optionitemquantity_id . '" value="' . $option_item_quantity->quantity . '" /><span><a href="#" onclick="return ec_admin_product_details_delete_optionitem_quantity( \'' . $option_item_quantity->optionitemquantity_id . '\' )" title="' . __( 'Delete', 'wp-easycart' ) . '"><div class="dashicons-before dashicons-trash"></div></a> <a href="#" onclick="return ec_admin_product_details_update_optionitem_quantity( \'' . $option_item_quantity->optionitemquantity_id . '\' )" title="' . __( 'Save', 'wp-easycart' ) . '"><div class="dashicons-before dashicons-yes"></div></a>';
				echo '</div>';
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_product_details_update_optionitem_quantity', 'ec_admin_ajax_product_details_update_optionitem_quantity' );
function ec_admin_ajax_product_details_update_optionitem_quantity( ){
	wp_easycart_admin_products( )->update_optionitem_quantity( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_product_details_delete_optionitem_quantity', 'ec_admin_ajax_product_details_delete_optionitem_quantity' );
function ec_admin_ajax_product_details_delete_optionitem_quantity( ){
	wp_easycart_admin_products( )->delete_optionitem_quantity( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_pricing', 'ec_admin_ajax_save_product_details_pricing' );
function ec_admin_ajax_save_product_details_pricing( ){
	wp_easycart_admin_products( )->save_product_details_pricing( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_product_details_add_price_tier', 'ec_admin_ajax_product_details_add_price_tier' );
function ec_admin_ajax_product_details_add_price_tier( ){
	$pricetier_id = wp_easycart_admin_products( )->add_price_tier( );
	global $wpdb;
	$price_tier = $wpdb->get_row( $wpdb->prepare( "SELECT ec_pricetier.* FROM ec_pricetier WHERE pricetier_id = %d", $pricetier_id ) );
	echo '<div class="ec_admin_price_tier_row" id="ec_admin_product_details_price_tier_row_' . $price_tier->pricetier_id . '"><span><input type="number" value="' . $price_tier->quantity . '" id="ec_admin_product_details_price_tier_row_' . $price_tier->pricetier_id . '_quantity" onchange="ec_admin_product_details_edit_price_tier( \'' . $price_tier->pricetier_id . '\' );" /></span><span><input type="number" min="0" step=".001" value="' . $GLOBALS['currency']->get_number_only( $price_tier->price ) . '" id="ec_admin_product_details_price_tier_row_' . $price_tier->pricetier_id . '_price" onchange="ec_admin_product_details_edit_price_tier( \'' . $price_tier->pricetier_id . '\' );" /></span><span><a href="" onclick="return ec_admin_product_details_delete_price_tier( \'' . $price_tier->pricetier_id . '\' );" title="' . __( 'Delete', 'wp-easycart' ) . '"><div class="dashicons-before dashicons-trash"></div></a><a href="" onclick="return ec_admin_product_details_edit_price_tier( \'' . $price_tier->pricetier_id . '\' );" title="' . __( 'Save', 'wp-easycart' ) . '"><div class="dashicons-before dashicons-yes"></div></a></span></div>';
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_product_details_update_price_tier', 'ec_admin_ajax_product_details_update_price_tier' );
function ec_admin_ajax_product_details_update_price_tier( ){
	$pricetier_id = wp_easycart_admin_products( )->update_price_tier( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_product_details_delete_price_tier', 'ec_admin_ajax_product_details_delete_price_tier' );
function ec_admin_ajax_product_details_delete_price_tier( ){
	wp_easycart_admin_products( )->delete_price_tier( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_product_details_add_role_price', 'ec_admin_ajax_product_details_add_role_price' );
function ec_admin_ajax_product_details_add_role_price( ){
	$roleprice_id = wp_easycart_admin_products( )->add_role_price( );
	global $wpdb;
	$role_price = $wpdb->get_row( $wpdb->prepare( "SELECT ec_roleprice.* FROM ec_roleprice WHERE roleprice_id = %d", $roleprice_id ) );
	echo '<div class="ec_admin_role_price_row" id="ec_admin_product_details_role_price_row_' . $role_price->roleprice_id . '"><span>' . $role_price->role_label . '</span><span>' . $role_price->role_price . '</span><span><a href="" onclick="return ec_admin_product_details_delete_role_price( \'' . $role_price->roleprice_id . '\' );"><div class="dashicons-before dashicons-trash"></div></a></span></div>';
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_product_details_delete_role_price', 'ec_admin_ajax_product_details_delete_role_price' );
function ec_admin_ajax_product_details_delete_role_price( ){
	wp_easycart_admin_products( )->delete_role_price( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_packaging', 'ec_admin_ajax_save_product_details_packaging' );
function ec_admin_ajax_save_product_details_packaging( ){
	wp_easycart_admin_products( )->save_product_details_packaging( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_shipping', 'ec_admin_ajax_save_product_details_shipping' );
function ec_admin_ajax_save_product_details_shipping( ){
	wp_easycart_admin_products( )->save_product_details_shipping( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_short_description', 'ec_admin_ajax_save_product_details_short_description' );
function ec_admin_ajax_save_product_details_short_description( ){
	wp_easycart_admin_products( )->save_product_details_short_description( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_specifications', 'ec_admin_ajax_save_product_details_specifications' );
function ec_admin_ajax_save_product_details_specifications( ){
	wp_easycart_admin_products( )->save_product_details_specifications( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_order_completed_note', 'ec_admin_ajax_save_product_details_order_completed_note' );
function ec_admin_ajax_save_product_details_order_completed_note( ){
	wp_easycart_admin_products( )->save_product_details_order_completed_note( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_order_completed_email_note', 'ec_admin_ajax_save_product_details_order_completed_email_note' );
function ec_admin_ajax_save_product_details_order_completed_email_note( ){
	wp_easycart_admin_products( )->save_product_details_order_completed_email_note( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_order_completed_details_note', 'ec_admin_ajax_save_product_details_order_completed_details_note' );
function ec_admin_ajax_save_product_details_order_completed_details_note( ){
	wp_easycart_admin_products( )->save_product_details_order_completed_details_note( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_tags', 'ec_admin_ajax_save_product_details_tags' );
function ec_admin_ajax_save_product_details_tags( ){
	wp_easycart_admin_products( )->save_product_details_tags( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_featured_products', 'ec_admin_ajax_save_product_details_featured_products' );
function ec_admin_ajax_save_product_details_featured_products( ){
	wp_easycart_admin_products( )->save_product_details_featured_products( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_general_options', 'ec_admin_ajax_save_product_details_general_options' );
function ec_admin_ajax_save_product_details_general_options( ){
	wp_easycart_admin_products( )->save_product_details_general_options( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_tax', 'ec_admin_ajax_save_product_details_tax' );
function ec_admin_ajax_save_product_details_tax( ){
	wp_easycart_admin_products( )->save_product_details_tax( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_deconetwork', 'ec_admin_ajax_save_product_details_deconetwork' );
function ec_admin_ajax_save_product_details_deconetwork( ){
	wp_easycart_admin_products( )->save_product_details_deconetwork( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_google_merchant', 'ec_admin_ajax_save_product_details_google_merchant' );
function ec_admin_ajax_save_product_details_google_merchant( ){
	wp_easycart_admin_products( )->save_product_details_google_merchant( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_subscription', 'ec_admin_ajax_save_product_details_subscription' );
function ec_admin_ajax_save_product_details_subscription( ){
	wp_easycart_admin_products( )->save_product_details_subscription( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_seo', 'ec_admin_ajax_save_product_details_seo' );
function ec_admin_ajax_save_product_details_seo( ){
	wp_easycart_admin_products( )->save_product_details_seo( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_downloads', 'ec_admin_ajax_save_product_details_downloads' );
function ec_admin_ajax_save_product_details_downloads( ){
	wp_easycart_admin_products( )->save_product_details_downloads( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_get_optionitem_images_content', 'ec_admin_ajax_get_optionitem_images_content' );
function ec_admin_ajax_get_optionitem_images_content( ){
	global $wpdb;
	$optionitems = $wpdb->get_results( $wpdb->prepare( "SELECT ec_optionitem.*, ec_optionitemimage.image1, ec_optionitemimage.image2, ec_optionitemimage.image3, ec_optionitemimage.image4, ec_optionitemimage.image5 FROM ec_optionitem LEFT JOIN ec_optionitemimage ON ( ec_optionitemimage.optionitem_id = ec_optionitem.optionitem_id AND ec_optionitemimage.product_id = %d ) WHERE option_id = %d ORDER BY optionitem_order ASC", $_POST['product_id'], $_POST['option_id'] ) );
	$advanced_options = $wpdb->get_results( "SELECT * FROM ec_option WHERE option_type != 'basic-combo' AND option_type != 'basic-swatch' ORDER BY option_label ASC" );
		
	echo '<div id="ec_admin_add_new_optionitem_image_row">';
	echo '<label>' . __( 'Choose Option', 'wp-easycart' ) . ':</label>';
	echo '<select name="optionitems_images" id="optionitems_images" onchange="ec_admin_product_details_update_optionitem_images( );">';
	foreach( $optionitems as $optionitem ){
		echo '<option value="' . $optionitem->optionitem_id . '">' . $optionitem->optionitem_name . '</option>';
	}
	echo '</select>';
	echo '</div>';
	echo '<div id="optionitem_images_holder">';
	for( $i=0; $i<count( $optionitems ); $i++ ){
		echo '<div class="ec_admin_optionitem_image_row';
		if( $i!=0 )
			echo ' ec_admin_hidden';
		echo '" id="ec_admin_product_details_optionitem_image_row_' . $optionitems[$i]->optionitem_id . '">';
		echo '<div class="ec_admin_product_details_optionitem_image_row_label">' . __( 'Images for', 'wp-easycart' ) . ' ' . $optionitems[$i]->optionitem_name . '</div>';
		$fields = array(
			array(
				"name"				=> "image1_" . $optionitems[$i]->optionitem_id,
				"type"				=> "image_upload",
				"label"				=> __( "Image 1", 'wp-easycart' ),
				"required" 			=> false,
				"validation_type" 	=> 'image',
				"visible"			=> true,
				"value"				=> $optionitems[$i]->image1
			),
			array(
				"name"				=> "image2_" . $optionitems[$i]->optionitem_id,
				"type"				=> "image_upload",
				"label"				=> __( "Image 2", 'wp-easycart' ),
				"required" 			=> false,
				"validation_type" 	=> 'image',
				"visible"			=> true,
				"value"				=> $optionitems[$i]->image2
			),
			array(
				"name"				=> "image3_" . $optionitems[$i]->optionitem_id,
				"type"				=> "image_upload",
				"label"				=> __( "Image 3", 'wp-easycart' ),
				"required" 			=> false,
				"validation_type" 	=> 'image',
				"visible"			=> true,
				"value"				=> $optionitems[$i]->image3
			),
			array(
				"name"				=> "image4_" . $optionitems[$i]->optionitem_id,
				"type"				=> "image_upload",
				"label"				=> __( "Image 4", 'wp-easycart' ),
				"required" 			=> false,
				"validation_type" 	=> 'image',
				"visible"			=> true,
				"value"				=> $optionitems[$i]->image4
			),
			array(
				"name"				=> "image5_" . $optionitems[$i]->optionitem_id,
				"type"				=> "image_upload",
				"label"				=> __( "Image 5", 'wp-easycart' ),
				"required" 			=> false,
				"validation_type" 	=> 'image',
				"visible"			=> true,
				"value"				=> $optionitems[$i]->image5
			)
		);
		$details = new wp_easycart_admin_details( );
		$details->print_fields( $fields );
		echo '</div>';
	}
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_get_optionitem_quantity_content', 'ec_admin_ajax_get_optionitem_quantity_content' );
function ec_admin_ajax_get_optionitem_quantity_content( ){
	global $wpdb;
	$optionitems1 = array( );
	$optionitems2 = array( );
	$optionitems3 = array( );
	$optionitems4 = array( );
	$optionitems5 = array( );
	if( $_POST['option1'] )
		$optionitems1 = $wpdb->get_results( $wpdb->prepare( "SELECT ec_optionitem.* FROM ec_optionitem WHERE option_id = %d ORDER BY optionitem_order ASC", $_POST['option1'] ) );
	if( $_POST['option2'] )
		$optionitems2 = $wpdb->get_results( $wpdb->prepare( "SELECT ec_optionitem.* FROM ec_optionitem WHERE option_id = %d ORDER BY optionitem_order ASC", $_POST['option2'] ) );
	if( $_POST['option3'] )
		$optionitems3 = $wpdb->get_results( $wpdb->prepare( "SELECT ec_optionitem.* FROM ec_optionitem WHERE option_id = %d ORDER BY optionitem_order ASC", $_POST['option3'] ) );
	if( $_POST['option4'] )
		$optionitems4 = $wpdb->get_results( $wpdb->prepare( "SELECT ec_optionitem.* FROM ec_optionitem WHERE option_id = %d ORDER BY optionitem_order ASC", $_POST['option4'] ) );
	if( $_POST['option5'] )
		$optionitems5 = $wpdb->get_results( $wpdb->prepare( "SELECT ec_optionitem.* FROM ec_optionitem WHERE option_id = %d ORDER BY optionitem_order ASC", $_POST['option5'] ) );
	
	$option_item_quantities = $wpdb->get_results( $wpdb->prepare( "SELECT 
			ec_optionitemquantity.*, 
			optionitem1.optionitem_name as optionitem_name_1, 
			optionitem2.optionitem_name as optionitem_name_2, 
			optionitem3.optionitem_name as optionitem_name_3, 
			optionitem4.optionitem_name as optionitem_name_4, 
			optionitem5.optionitem_name as optionitem_name_5
		FROM 
			ec_optionitemquantity 
			LEFT JOIN ec_optionitem AS optionitem1 ON ( optionitem1.optionitem_id = ec_optionitemquantity.optionitem_id_1 )
			LEFT JOIN ec_optionitem AS optionitem2 ON ( optionitem2.optionitem_id = ec_optionitemquantity.optionitem_id_2 )
			LEFT JOIN ec_optionitem AS optionitem3 ON ( optionitem3.optionitem_id = ec_optionitemquantity.optionitem_id_3 )
			LEFT JOIN ec_optionitem AS optionitem4 ON ( optionitem4.optionitem_id = ec_optionitemquantity.optionitem_id_4 )
			LEFT JOIN ec_optionitem AS optionitem5 ON ( optionitem5.optionitem_id = ec_optionitemquantity.optionitem_id_5 )
		WHERE 
			ec_optionitemquantity.product_id = %d", 
	$_POST['product_id'] ) );
	
	echo '<div id="ec_admin_add_new_optionitem_quantity_row"><h3>' . __( 'Add New Quantity Item', 'wp-easycart' ) . ' <a href="admin.php?page=wp-easycart-products&subpage=products&product_id=' . esc_attr( $_POST['product_id'] ) . '&ec_admin_form_action=export-option-item-quantities" target="_blank"' . apply_filters( 'wp_easycart_admin_lock_icon', ' onclick="return show_pro_required( );"' ) . '>' . __( 'Export', 'wp-easycart' ) . apply_filters( 'wp_easycart_admin_lock_icon', ' <span class="dashicons dashicons-lock" style="color:#FC0; margin-top:8px;"></span>' ) . '</a><form action="" method="POST" enctype="multipart/form-data" style="float:right; border:1px solid #CCC; padding:5px;"><input type="hidden" name="ec_admin_form_action" value="import-option-item-quantities" /><input type="hidden" name="product_id" id="product_id" value="' . $optionitem->product_id . '" /><input type="file" placeholder="' . __( 'Choose Quantity File', 'wp-easycart' ) . '" name="import_file" /><input type="submit" value="' . __( 'Import Quantities', 'wp-easycart' ) . '"' . apply_filters( 'wp_easycart_admin_lock_icon', ' onclick="return show_pro_required( );"' ) . ' />' . apply_filters( 'wp_easycart_admin_lock_icon', ' <span class="dashicons dashicons-lock" style="float:right; color:#FC0; margin-top:8px;"></span>' ) . '</form></h3>';
	if( count( $optionitems1 ) ){
		echo '<select name="add_new_optionitem_quantity_1" id="add_new_optionitem_quantity_1" class="select2-basic">';
		echo '<option value="0">' . __( 'No Selection', 'wp-easycart' ) . '</option>';
		foreach( $optionitems1 as $optionitem ){
			echo '<option value="' . $optionitem->optionitem_id . '">' . $optionitem->optionitem_name . '</option>';
		}
		echo '</select>';
	}
	
	if( count( $optionitems2 ) ){
		echo '<select name="add_new_optionitem_quantity_2" id="add_new_optionitem_quantity_2" class="select2-basic">';
		echo '<option value="0">' . __( 'No Selection', 'wp-easycart' ) . '</option>';
		foreach( $optionitems2 as $optionitem ){
			echo '<option value="' . $optionitem->optionitem_id . '">' . $optionitem->optionitem_name . '</option>';
		}
		echo '</select>';
	}
	
	if( count( $optionitems3 ) ){
		echo '<select name="add_new_optionitem_quantity_3" id="add_new_optionitem_quantity_3" class="select2-basic">';
		echo '<option value="0">' . __( 'No Selection', 'wp-easycart' ) . '</option>';
		foreach( $optionitems3 as $optionitem ){
			echo '<option value="' . $optionitem->optionitem_id . '">' . $optionitem->optionitem_name . '</option>';
		}
		echo '</select>';
	}
	
	if( count( $optionitems4 ) ){
		echo '<select name="add_new_optionitem_quantity_4" id="add_new_optionitem_quantity_4" class="select2-basic">';
		echo '<option value="0">' . __( 'No Selection', 'wp-easycart' ) . '</option>';
		foreach( $optionitems4 as $optionitem ){
			echo '<option value="' . $optionitem->optionitem_id . '">' . $optionitem->optionitem_name . '</option>';
		}
		echo '</select>';
	}
	
	if( count( $optionitems5 ) ){
		echo '<select name="add_new_optionitem_quantity_5" id="add_new_optionitem_quantity_5" class="select2-basic">';
		echo '<option value="0">' . __( 'No Selection', 'wp-easycart' ) . '</option>';
		foreach( $optionitems5 as $optionitem ){
			echo '<option value="' . $optionitem->optionitem_id . '">' . $optionitem->optionitem_name . '</option>';
		}
		echo '</select>';
	}
	$add_new_click_action = apply_filters( 'wp_easycart_admin_optionitem_quantity_add_click', 'show_pro_required' );
	$update_click_action = apply_filters( 'wp_easycart_admin_optionitem_quantity_update_click', 'show_pro_required' );
	$delete_click_action = apply_filters( 'wp_easycart_admin_optionitem_quantity_delete_click', 'show_pro_required' );
	echo '<input type="number" value="" placeholder="' . __( 'Quantity', 'wp-easycart' ) . '" name="add_new_optionitem_quantity" id="add_new_optionitem_quantity" />';
	echo '<input type="button" value="' . __( 'Add New', 'wp-easycart' ) . '" onclick="return ' . $add_new_click_action . '( );" />' . apply_filters( 'wp_easycart_admin_lock_icon', ' <span class="dashicons dashicons-lock" style="float:left; width:25px !important; color:#FC0; margin-top:15px;"></span>' );
	echo '</div>';
	echo '<div class="ec_admin_optionitem_quantity_header"><span>' . __( 'Options', 'wp-easycart' ) . '</span><span>' . __( 'Quantity', 'wp-easycart' ) . '</span><span></span></div>';
	echo '<div id="ec_admin_product_details_optionitem_quantities_holder">';
	if( count( $option_item_quantities ) ){
		for( $i=0; $i<count( $option_item_quantities ); $i++ ){
			echo '<div id="ec_admin_product_details_optionitem_quantity_row_' . $option_item_quantities[$i]->optionitemquantity_id . '" class="ec_admin_opionitem_quantity_row"><label>';
			echo $option_item_quantities[$i]->optionitem_name_1;
			if( $option_item_quantities[$i]->optionitem_id_2 )
				echo ', ' . $option_item_quantities[$i]->optionitem_name_2;
			if( $option_item_quantities[$i]->optionitem_id_3 )
				echo ', ' . $option_item_quantities[$i]->optionitem_name_3;
			if( $option_item_quantities[$i]->optionitem_id_4 )
				echo ', ' . $option_item_quantities[$i]->optionitem_name_4;
			if( $option_item_quantities[$i]->optionitem_id_5 )
				echo ', ' . $option_item_quantities[$i]->optionitem_name_5;
			
			echo '</label><input type="number" name="optionitem_quantity_' . $option_item_quantities[$i]->optionitemquantity_id . '" id="optionitem_quantity_' . $option_item_quantities[$i]->optionitemquantity_id . '" value="' . $option_item_quantities[$i]->quantity . '" /><span><a href="#" onclick="return ' . $delete_click_action . '( \'' . $option_item_quantities[$i]->optionitemquantity_id . '\' )" title="' . __( 'Delete', 'wp-easycart' ) . '"><div class="dashicons-before dashicons-trash"></div></a> <a href="#" onclick="return ' . $update_click_action . '( \'' . $option_item_quantities[$i]->optionitemquantity_id . '\' )"><div class="dashicons-before dashicons-yes" title="' . __( 'Save', 'wp-easycart' ) . '"></div></a>';
			echo '</div>';
		}
	}else{
		echo '<div id="ec_admin_no_optionitem_quantities">' . __( 'No Option Item Quantities Setup', 'wp-easycart' ) . '</div>';
	}
	echo '</div>';
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_product_details_insert_manufacturer', 'ec_admin_ajax_product_details_insert_manufacturer' );
function ec_admin_ajax_product_details_insert_manufacturer( ){
	global $wpdb;
	$result = wp_easycart_admin_manufacturers( )->insert_manufacturer( );
	$manufacturer_id = $result['manufacturer_id'];
	$manufacturer_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_manufacturer WHERE manufacturer_id = %d", $manufacturer_id ) );
	echo json_encode( $manufacturer_row );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_validate_model_number', 'ec_admin_ajax_validate_model_number' );
function ec_admin_ajax_validate_model_number( ){
	if( wp_easycart_admin_products( )->verify_model_number( ) ){
		echo '1';
	}else{
		global $wpdb;
		$possible_match = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.model_number FROM ec_product WHERE ec_product.model_number = %s AND ec_product.product_id != %d", $_POST['model_number'], $_POST['product_id'] ) );
		if( $possible_match ){
			echo '-1';
		}else{
			echo '0';
		}
	}
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_import_products', 'ec_admin_ajax_import_products' );
function ec_admin_ajax_import_products( ){
	$import_results = wp_easycart_admin_products( )->run_importer();
	//echo $import_results;
	die();
}

add_action( 'wp_ajax_ec_admin_ajax_save_new_quick_product', 'ec_admin_ajax_save_new_quick_product' );
function ec_admin_ajax_save_new_quick_product( ){
	$result = wp_easycart_admin_products( )->save_new_quick_product( );
	echo json_encode( $result );
	die();
}

add_action( 'wp_ajax_ec_admin_ajax_save_new_optionset', 'ec_admin_ajax_save_new_optionset' );
function ec_admin_ajax_save_new_optionset( ){
	$option_id = wp_easycart_admin_products( )->save_new_optionset( );
	echo json_encode( array( 'option_id' => $option_id ) );
	die();
}

add_action( 'wp_ajax_ec_admin_ajax_save_new_optionitem', 'ec_admin_ajax_save_new_optionitem' );
function ec_admin_ajax_save_new_optionitem( ){
	wp_easycart_admin_products( )->save_new_optionitem( );
	die();
}
add_action( 'wp_ajax_ec_admin_ajax_save_new_adv_optionset', 'ec_admin_ajax_save_new_adv_optionset' );
function ec_admin_ajax_save_new_adv_optionset( ){
	$option_id = wp_easycart_admin_products( )->save_new_adv_optionset( );
	echo json_encode( array( 'option_id' => $option_id ) );
	die();
}
add_action( 'wp_ajax_ec_admin_ajax_save_new_adv_optionitem', 'ec_admin_ajax_save_new_adv_optionitem' );
function ec_admin_ajax_save_new_adv_optionitem( ){
	wp_easycart_admin_products( )->save_new_adv_optionitem( );
	die();
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_advanced_option_order', 'ec_admin_ajax_save_product_advanced_option_order' );
function ec_admin_ajax_save_product_advanced_option_order( ){
	wp_easycart_admin_products( )->save_product_advanced_option_order( );
	die();
}
add_action( 'wp_ajax_ec_admin_ajax_save_product_details_logic', 'ec_admin_ajax_save_product_details_logic' );
function ec_admin_ajax_save_product_details_logic( ){
	wp_easycart_admin_products( )->save_logic( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_get_product_quick_edit', 'ec_admin_ajax_get_product_quick_edit' );
function ec_admin_ajax_get_product_quick_edit( ){
	$product = wp_easycart_admin_products( )->get_quick_product( (int) $_POST['product_id'] );
    echo json_encode( $product );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_product_quick_update', 'ec_admin_ajax_product_quick_update' );
function ec_admin_ajax_product_quick_update( ){
	wp_easycart_admin_products( )->product_quick_update( );
    $product = wp_easycart_admin_products( )->get_quick_product( (int) $_POST['product_id'] );
    $product->unit_price_formatted = $GLOBALS['currency']->get_currency_display( $product->price );
    echo json_encode( $product );
    die( );
}