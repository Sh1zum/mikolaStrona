<?php
if( !defined( 'ABSPATH' ) ) exit;

class wp_easycart_admin_details_category extends wp_easycart_admin_details{
	
	public $category;

	public function __construct( ){
		parent::__construct( );
		add_action( 'wp_easycart_admin_category_details_basic_fields', array( $this, 'basic_fields' ) );
	}
	
	protected function init( ){
		$this->docs_link = 'http://docs.wpeasycart.com/wp-easycart-administrative-console-guide/?wpeasycartadmin=1&section=categories';
		$this->id = 0;
		$this->page = 'wp-easycart-products';
		$this->subpage = 'category';
		$this->action = 'admin.php?page=' . $this->page . '&subpage=' . $this->subpage;
		$this->form_action = 'add-new-category';
		$this->category = (object) array(
			"category_id"					=> "",
			"category_name"					=> "",
			"post_id"						=> "",
			"guid"							=> "",
			"parent_id"						=> "",
			"short_description"				=> "",
			"image"							=> "",
			"featured_category"				=> "",
			"priority"						=> 0
		);

	}
	
	protected function init_data( ){
		$this->form_action = 'update-category';
		$this->category = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT 
				ec_category.*,
				" . $this->wpdb->prefix . "posts.guid 
			FROM 
				ec_category 
				LEFT JOIN " . $this->wpdb->prefix . "posts ON " . $this->wpdb->prefix . "posts.ID = ec_category.post_id 
			WHERE 
				ec_category.category_id = %d", $_GET['category_id']
		) );
		$this->id = $this->category->category_id;
	}

	public function output( $type = 'edit' ){
		$this->init( );
		if( $type == 'edit' )
			$this->init_data( );
		
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/categories/category-details.php' );
	}
	
	public function get_category_link( ){
		if( !get_option( 'ec_option_use_old_linking_style' ) && $this->category->post_id != "0" ){
			return get_permalink( $this->category->post_id );
		
		}else{
			$storepageid = get_option( 'ec_option_storepage' );
			if( function_exists( 'icl_object_id' ) ){
				$storepageid = icl_object_id( $storepageid, 'page', true, ICL_LANGUAGE_CODE );
			}
			$store_page = get_permalink( $storepageid );
			if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
				$https_class = new WordPressHTTPS( );
				$store_page = $https_class->makeUrlHttps( $store_page );
			}
		
			if( substr_count( $store_page, '?' ) )								$permalink_divider = "&";
			else																$permalink_divider = "?";
			
			return $store_page . $permalink_divider . "group_id=" . $this->category->category_id;
		}
	}
	
	public function basic_fields( ){
		global $wpdb;
		$categories = $wpdb->get_results( "SELECT 
				ec_category.category_id AS id, 
				ec_category.category_name AS value,
				" . $wpdb->prefix . "posts.guid 
			FROM 
				ec_category 
				LEFT JOIN " . $wpdb->prefix . "posts ON " . $wpdb->prefix . "posts.ID = ec_category.post_id 
			ORDER BY 
				category_name ASC" 
		);
		$category_image = $this->category->image;
		if( $category_image != "" && substr( $category_image, 0, 7 ) != "http://" && substr( $category_image, 0, 8 ) != "https://" ){
			$category_image = plugins_url( '/wp-easycart-data/products/categories/' . $category_image );
		}
		
		$fields = apply_filters( 'wp_easycart_admin_category_details_basic_fields_list', array(
			array(
				"name"				=> "category_id",
				"alt_name"			=> "category_id",
				"type"				=> "hidden",
				"value"				=> $this->category->category_id
			),
			array(
				"name"				=> "post_id",
				"alt_name"			=> "post_id",
				"type"				=> "hidden",
				"value"				=> $this->category->post_id
			),
            array(
				"name"				=> "featured_category",
				"type"				=> "checkbox",
				"label"				=> __( "Is Featured Category?", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please enter an option", 'wp-easycart' ),
				"validation_type" 	=> 'checkbox',
				"value"				=> $this->category->featured_category
			),
			array(
				"name"				=> "category_name",
				"type"				=> "text",
				"label"				=> __( "Category Name", 'wp-easycart' ),
				"required" 			=> true,
				"message" 			=> __( "Please enter a unique category name.", 'wp-easycart' ),
				"validation_type" 	=> 'text',
				"value"				=> $this->category->category_name
			),
			array(
				"name"				=> "priority",
				"type"				=> "number",
				"label"				=> __( "Priority (largest first)", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please enter 0 if you prefer alphabetical order.", 'wp-easycart' ),
				"validation_type" 	=> 'number',
				"step"				=> 1,
				"value"				=> $this->category->priority
			),
			array(
				"name"				=> "post_slug",
				"type"				=> "text",
				"label"				=> __( "Link Slug", 'wp-easycart' ),
				"required" 			=> false,
				"validation_type" 	=> 'post_slug',
				"visible"			=> ($this->id == '0') ? false : true,
				"value"				=> basename( $this->category->guid ),
				"message"			=> __( "Post Slug values must be unique and may only include letters, numbers, and dashes", 'wp-easycart' )
			),
			array(
				"name"				=> "parent_id",
				"type"				=> "select",
				"data"				=> $categories,
				"data_label" 		=> __( "No Parent Category", 'wp-easycart' ),
				"label" 			=> __( "Parent Category", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please select a parent category.", 'wp-easycart' ),
				"value" 			=> $this->category->parent_id
			),
			array(
				"name"				=> "image",
				"type"				=> "image_upload",
				"label"				=> __( "Banner Image", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please select an image for this category.", 'wp-easycart' ),
				"validation_type" 	=> 'image_upload',
				"value"				=> $category_image
			),
			array(
				"name"				=> "short_description",
				"type"				=> "textarea",
				"label"				=> __( "Short Description", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please enter a unique category description.", 'wp-easycart' ),
				"validation_type" 	=> 'textarea',
				"value"				=> $this->category->short_description
			)
		) );
		$this->print_fields( $fields );
	}
}