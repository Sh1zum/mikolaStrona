<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_category' ) ) :

final class wp_easycart_admin_category{
	
	protected static $_instance = null;
	
	public $category_list_file;
	public $product_list_file;
	public $product_select_list_file;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
	
	public function __construct( ){ 
		$this->category_list_file 			= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/categories/category-list.php';
		$this->product_list_file 			= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/categories/product-list.php';
		$this->product_select_list_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/categories/product-select-list.php';
		
		/* Process Admin Messages */
		add_filter( 'wp_easycart_admin_success_messages', array( $this, 'add_success_messages' ) );
		add_filter( 'wp_easycart_admin_error_messages', array( $this, 'add_failure_messages' ) );
		
		/* Process Form Actions */
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_add_category_product' ) );
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_add_category' ) );
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_update_category' ) );
		
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_duplicate_category' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_delete_category' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_delete_category' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_add_category_product' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_delete_category_product' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_bulk_delete_category_product' ) );
	}
	
	public function process_add_category_product( ){
		if( $_POST['ec_admin_form_action'] == "add-new-category-product" ){
			$result = $this->insert_category_product( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'category', $result );
		}
	}
	
	public function process_add_category( ){
		if( $_POST['ec_admin_form_action'] == "add-new-category" ){
			$result = $this->insert_category( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'category', $result );
		}
	}
	
	public function process_update_category( ){
		if( $_POST['ec_admin_form_action'] == "update-category" ){
			$result = $this->update_category( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'category', $result );
		}
	}
	
	public function process_duplicate_category( ){
		if( isset( $_GET['subpage'] ) &&  $_GET['subpage'] == 'category' && $_GET['ec_admin_form_action'] == 'duplicate-category' && isset( $_GET['category_id'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->duplicate_category( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'category', $result );
		}
	}
	
	public function process_delete_category( ){
		if( isset( $_GET['subpage'] ) &&  $_GET['subpage'] == 'category' && $_GET['ec_admin_form_action'] == 'delete-category' && isset( $_GET['category_id'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->delete_category( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'category', $result );
		}
	}
	
	public function process_bulk_delete_category( ){
		if( isset( $_GET['subpage'] ) && $_GET['subpage']  == 'category' && $_GET['ec_admin_form_action'] == 'delete-category' && !isset( $_GET['category_id'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_delete_category( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'category', $result );
		}
	}
	
	public function process_bulk_add_category_product( ){
		if( isset( $_GET['subpage'] ) && $_GET['subpage']  == 'category-products-manage' && $_GET['ec_admin_form_action'] == 'add-to-category-product' && isset( $_GET['category_id'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_add_category_product( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'category-products', $result );
		}
	}
	
	public function process_delete_category_product( ){
		if( isset( $_GET['subpage'] ) &&  $_GET['subpage'] == 'category-products' && $_GET['ec_admin_form_action'] == 'delete-category-product' && isset( $_GET['categoryitem_id'] ) && !isset( $_GET['bulk'] ) ){
			$result = $this->delete_category_product( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'category-products', $result );
		}
	}
	
	public function process_bulk_delete_category_product( ){
		if( isset( $_GET['subpage'] ) &&  $_GET['subpage'] == 'category-products' && $_GET['ec_admin_form_action'] == 'delete-category-product' && !isset( $_GET['categoryitem_id'] ) && isset( $_GET['bulk'] ) ){
			$result = $this->bulk_delete_category_product( );
			wp_easycart_admin( )->redirect( 'wp-easycart-products', 'category-products', $result );
		}
	}
	
	public function add_success_messages( $messages ){
		if( isset( $_GET['success'] ) && $_GET['success'] == 'category-inserted' ){
			$messages[] = __( 'Category successfully created', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'category-updated' ){
			$messages[] = __( 'Category successfully updated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'category-deleted' ){
			$messages[] = __( 'Category successfully deleted', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'category-duplicated' ){
			$messages[] = __( 'Category successfully duplicated', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'category-item-inserted' ){
			$messages[] = __( 'Category Item successfully created', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'category-item-deleted' ){
			$messages[] = __( 'Category item(s) successfully deleted', 'wp-easycart' );
		}else if( isset( $_GET['success'] ) && $_GET['success'] == 'category-item-added' ){
			$messages[] = __( 'Products(s) successfully added to the category', 'wp-easycart' );
		}
		return $messages;
	}
	
	public function add_failure_messages( $messages ){
		if( isset( $_GET['error'] ) && $_GET['error'] == 'category-inserted-error' ){
			$messages[] = __( 'Category failed to create', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'category-updated-error' ){
			$messages[] = __( 'Category failed to update', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'category-deleted-error' ){
			$messages[] = __( 'Category failed to delete', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'category-duplicate-error' ){
			$messages[] = __( 'Category failed to duplicate', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'category-duplicate' ){
			$messages[] = __( 'Category failed to create due to duplicate', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'category-item-duplicate' ){
			$messages[] = __( 'Category Item failed to create due to duplicate', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'category-item-inserted-error' ){
			$messages[] = __( 'Category Item failed to create', 'wp-easycart' );
		}else if( isset( $_GET['error'] ) && $_GET['error'] == 'category-item-deleted-error' ){
			$messages[] = __( 'Category Item failed to delete', 'wp-easycart' );
		}
		return $messages;
	}
	
	public function load_category_list( ){
		if( ( isset( $_GET['category_id'] ) && isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'edit' ) || 
			( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new-category' ) ){
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/inc/wp_easycart_admin_details_category.php' );
				$details = new wp_easycart_admin_details_category( );
				$details->output( esc_attr( $_GET['ec_admin_form_action'] ) );
		
		}else{
			include( $this->category_list_file );
			
		}
	}
	
	public function load_category_product_list( ){
		include( $this->product_list_file );
	}
	
	public function load_category_product_manage_list( ){
		include( $this->product_select_list_file );
	}
	
	/*************************************
	* Category
	*************************************/
	public function duplicate_category( ){
		
		$category_id = $_GET['category_id'];
		$query_vars = array( );
		global $wpdb;
		
		//FIRST, CREATE DUPLICATE CATEGORY
		//drop temp table if left from elsewhere
		$wpdb->query( "DROP TEMPORARY TABLE IF EXISTS ec_temporary;");
		//create temp table
		$wpdb->query( $wpdb->prepare( "CREATE TEMPORARY TABLE ec_temporary SELECT ec_category.* FROM ec_category WHERE ec_category.category_id = %s", $category_id));
		//remove the primary key
		$wpdb->query( "UPDATE ec_temporary SET category_id = NULL;");
		//insert into the table duplicate
		$result = $wpdb->query(  "INSERT INTO ec_category SELECT ec_temporary.* FROM ec_temporary");
		//get last insert id & category name for post
		$new_category_id = $wpdb->insert_id;
		$new_category_name = $wpdb->get_var( $wpdb->prepare( "SELECT ec_category.category_name FROM ec_category WHERE ec_category.category_id = %s", $category_id));
		//drop temp table if left
		$wpdb->query("DROP TEMPORARY TABLE IF EXISTS ec_temporary;");
		
		// Insert a WordPress Custom post type post.
		$post = array(	'post_content'	=> "[ec_store groupid=\"" . $new_category_id . "\"]",
						'post_status'	=> "publish",
						'post_title'	=> $GLOBALS['language']->convert_text( $new_category_name ),
						'post_type'		=> "ec_store"
					  );
		$post_id = wp_insert_post( $post );
		
		// Update Category Post ID
		$db = new ec_db( );
		$db->update_category_post_id( $new_category_id, $post_id );
		
		//SECOND, CREATE DUPLICATE CATEOGRY ITEMS
		$all_subitems = $wpdb->get_results( $wpdb->prepare( "SELECT ec_categoryitem.* FROM ec_categoryitem WHERE ec_categoryitem.category_id = %s", $category_id));
		foreach($all_subitems AS $subitems) {
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_categoryitem( ec_categoryitem.categoryitem_id, ec_categoryitem.category_id,  ec_categoryitem.product_id ) VALUES(NULL, %s, %s)",  $new_category_id,  $subitems->product_id) );
		}
		
		if( count( $result  )> 0 ){
			$query_vars['success'] = 'category-duplicated';
		}else{
			$query_vars['error'] = 'category-duplicate-error';
		}
		
		return $query_vars;
		
	}
	
	public function insert_category( ){
		global $wpdb;
		
		$featured_category = 0;
		if( isset( $_POST['featured_category'] ) )
			$featured_category = 1;
		$category_name = stripslashes_deep( $_POST['category_name'] );
		$priority = $_POST['priority'];
		$parent_id = $_POST['parent_id'];
		$image = stripslashes_deep( $_POST['image'] );
		$short_description = stripslashes_deep( $_POST['short_description'] );
		
		$wpdb->query( $wpdb->prepare( "INSERT INTO ec_category( ec_category.featured_category, ec_category.category_name, ec_category.parent_id, ec_category.image, ec_category.short_description, ec_category.priority ) VALUES( %d, %s, %d, %s, %s, %d )", $featured_category, $category_name, $parent_id, $image, $short_description, $priority ) );
		$category_id = $wpdb->insert_id;
		
		$post = array(	
			'post_content'	=> "[ec_store groupid=\"" . $category_id . "\"]",
			'post_status'	=> "publish",
			'post_title'	=> $GLOBALS['language']->convert_text( $category_name ),
			'post_type'		=> "ec_store"
		);
		$post_id = wp_insert_post( $post );
		$wpdb->query( $wpdb->prepare( "UPDATE ec_category SET post_id = %d WHERE category_id = %d", $post_id, $category_id ) );
		
		return array( 'success' => 'category-inserted' );
	}
	
	public function update_category( ){	
		$category_id = $_POST['category_id'];			
		$category_name = stripslashes_deep( $_POST['category_name'] );
		$priority = $_POST['priority'];
		$post_slug = preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep( $_POST['post_slug'] ) ) );
		$post_id = $_POST['post_id'];
		$parent_id = $_POST['parent_id'];
		$short_description = stripslashes_deep( $_POST['short_description'] );
		$image = stripslashes_deep( $_POST['image'] );
		if( isset( $_POST['featured_category'] ) )
			$featured_category = $_POST['featured_category'];
		else
			$featured_category = 0;
		
		$query_vars = array( );

		global $wpdb;
		
		//update category			
		$result = $wpdb->query( $wpdb->prepare( "UPDATE ec_category SET category_id = %s, category_name = %s, post_id = %s, parent_id = %s, short_description = %s, image = %s, featured_category = %s, priority = %d WHERE category_id = %s", $category_id, $category_name, $post_id, $parent_id, $short_description, $image, $featured_category, $priority, $category_id) );
		
		// Update WordPress Post
		$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM ec_category WHERE category_id = %d", $category_id ) );
		
		// Create Post Array
		$post = array(	
			'ID'			=> $post_id,
			'post_content'	=> "[ec_store groupid=\"" . $category_id . "\"]",
			'post_status'	=> "publish",
			'post_title'	=> $GLOBALS['language']->convert_text( $category_name ),
			'post_type'		=> "ec_store",
			'post_name'		=> $post_slug,
		);
		
		// Update WordPress Post
		$updated_post_id = wp_update_post( $post );
		if( $updated_post_id == 0 ){
			$insert_post_id = wp_insert_post( array(	
				'post_content'	=> "[ec_store groupid=\"" . $category_id . "\"]",
				'post_status'	=> "publish",
				'post_title'	=> $GLOBALS['language']->convert_text( $category_name ),
				'post_type'		=> "ec_store",
				'post_name'		=> $post_slug,
			) );
			if( $insert_post_id != 0 ){
				$post_id = $insert_post_id;
				$wpdb->query( $wpdb->prepare( "UPDATE ec_category SET post_id = %d WHERE category_id = %d", $post_id, $category_id ) );
			}
		}
		
		// Update GUID
		$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "posts SET " . $wpdb->prefix . "posts.guid = %s WHERE " . $wpdb->prefix . "posts.ID = %d", get_permalink( $post_id ), $post_id ) );

		if( count( $result ) > 0 ){
			$query_vars['success'] = 'category-updated';
		}else{
			$query_vars['error'] = 'category-updated-error';
		}
		
		return $query_vars;	
	}
	
	public function delete_category( ){
		
		$category_id = $_GET['category_id'];		
		$query_vars = array( );
		
		global $wpdb;
		
		// Delete WordPress Post
		$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM ec_category WHERE category_id = %d", $category_id ) );
		wp_delete_post( $post_id, true );
		
		// Delete Category
		$result = $wpdb->query( $wpdb->prepare( "DELETE FROM ec_category WHERE ec_category.category_id = %s", $category_id ) );
		
		// Delete Category Items
		$wpdb->query( $wpdb->prepare(  "DELETE FROM ec_categoryitem WHERE ec_categoryitem.category_id = %d", $category_id ) );
		
		if( count( $result ) > 0 ){
			$query_vars['success'] = 'category-deleted';
		}else{
			$query_vars['error'] = 'category-deleted-error';
		}
	
		return $query_vars;
	}
	
	public function bulk_delete_category( ){
		$bulk_ids = $_GET['bulk'];
		$query_vars = array( );
		
		global $wpdb;
		$errors = 0;
		foreach( $bulk_ids as $bulk_id ){
			// Delete WordPress Post
			$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM ec_category WHERE category_id = %d", $bulk_id ) );
			wp_delete_post( $post_id, true );
			
			//Delete Category
			$result = $wpdb->query( $wpdb->prepare( "DELETE FROM ec_category WHERE ec_category.category_id = %s", $bulk_id ) );
			if( $result === false )
				$errors++;
				
			// Delete Category Items
			$wpdb->query( $wpdb->prepare(  "DELETE FROM ec_categoryitem WHERE ec_categoryitem.category_id = %d", $bulk_id ) );
			
		}
		
		if( $errors ){
			$query_vars['error'] = 'category-deleted-error';
		} else {
			$query_vars['success'] = 'category-deleted';
		}
		
		return $query_vars;
		
	}	
	
	/**************************************
	* Category Products
	**************************************/
	public function bulk_add_category_product( ){
		global $wpdb;
		
		$category_id = $_GET['category_id'];			
		$bulk_ids = $_GET['bulk'];
		
		foreach( $bulk_ids as $bulk_id ){	
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_categoryitem( category_id, product_id ) VALUES( %d, %d )",  $category_id,  $bulk_id ) );
		}
		
		return array( 
			'success' => 'category-item-added', 
			'ec_admin_form_action' => 'edit-products',
			'category_id' => esc_attr( $category_id )
		);
	}
	
	public function delete_category_product( ){
		global $wpdb;
		
		$categoryitem_id = $_GET['categoryitem_id'];
		$category_id = $wpdb->get_var( $wpdb->prepare( "SELECT category_id FROM ec_categoryitem WHERE categoryitem_id = %d", $categoryitem_id ) );
		
		$result = $wpdb->query( $wpdb->prepare( "DELETE FROM ec_categoryitem WHERE ec_categoryitem.categoryitem_id = %d", $categoryitem_id ) );

		return array( 
			'success' => 'category-item-deleted',
			'ec_admin_form_action' => 'edit-products',
			'category_id' => esc_attr( $category_id )
		);
	}
	
	public function bulk_delete_category_product( ){
		global $wpdb;
		
		$bulk_ids = $_GET['bulk'];
		if( count( $bulk_ids ) > 0 )
			$category_id = $wpdb->get_var( $wpdb->prepare( "SELECT category_id FROM ec_categoryitem WHERE categoryitem_id = %d", $bulk_ids[0] ) );
		
		foreach( $bulk_ids as $bulk_id ){	
			$wpdb->query( $wpdb->prepare( "DELETE FROM ec_categoryitem WHERE categoryitem_id = %d", $bulk_id ) );
		}
		
		return array( 
			'success' => 'category-item-deleted',
			'ec_admin_form_action' => 'edit-products',
			'category_id' => esc_attr( $category_id )
		);
	}
	
	public function save_category_order( ){
		global $wpdb;
		$sort_order = $_POST['sort_order'];
		
		foreach( $sort_order as $sort_item ){
			$wpdb->query( $wpdb->prepare( "UPDATE ec_category SET priority = %d WHERE category_id = %d", 99999999 - $sort_item['order'], $sort_item['id'] ) );
		}
	}

}
endif; // End if class_exists check

function wp_easycart_admin_category( ){
	return wp_easycart_admin_category::instance( );
}
wp_easycart_admin_category( );

add_action( 'wp_ajax_ec_admin_ajax_save_category_order', 'ec_admin_ajax_save_category_order' );
function ec_admin_ajax_save_category_order( ){
	wp_easycart_admin_category( )->save_category_order( );
	die( );
}