<?php
if( !defined( 'ABSPATH' ) ) exit;

class wp_easycart_admin_details_manufacturer extends wp_easycart_admin_details{
	
	public $manufacturer;

	
	public function __construct( ){
		parent::__construct( );
		add_action( 'wp_easycart_admin_manufacturers_details_basic_fields', array( $this, 'basic_fields' ) );
	}
	
	protected function init( ){
		$this->docs_link = 'http://docs.wpeasycart.com/wp-easycart-administrative-console-guide/?wpeasycartadmin=1&section=manufacturers';
		$this->id = 0;
		$this->page = 'wp-easycart-products';
		$this->subpage = 'manufacturers';
		$this->action = 'admin.php?page=' . $this->page . '&subpage=' . $this->subpage;
		$this->form_action = 'add-new-manufacturer';
		$this->manufacturer = (object) array(
			"manufacturer_id"					=> "",
			"manufacturer_name"					=> "",
			"guid"								=> "",
			"clicks"							=> "",
			"post_id" 							=> ""
		);
	}
	
	protected function init_data( ){
		$this->form_action = 'update-manufacturer';
		$this->manufacturer = $this->item = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT 
				ec_manufacturer.*,
				" . $this->wpdb->prefix . "posts.guid
			FROM 
				ec_manufacturer 
				LEFT JOIN " . $this->wpdb->prefix . "posts ON " . $this->wpdb->prefix . "posts.ID = ec_manufacturer.post_id 
			WHERE 
				manufacturer_id = %d", $_GET['manufacturer_id']
		) );
		$this->id = $this->manufacturer->manufacturer_id;
	}

	public function output( $type = 'edit' ){
		$this->init( );
		if( $type == 'edit' )
			$this->init_data( );
		
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/manufacturers/manufacturer-details.php' );
	}
	
	public function basic_fields( ){

		$fields = apply_filters( 'wp_easycart_admin_manufacturers_details_basic_fields_list', array(
			array(
				"name"				=> "manufacturer_id",
				"alt_name"			=> "manufacturer_id",
				"type"				=> "hidden",
				"value"				=> $this->manufacturer->manufacturer_id
			),
			array(
				"name"				=> "post_id",
				"alt_name"			=> "post_id",
				"type"				=> "hidden",
				"value"				=> $this->manufacturer->post_id
			),
			array(
				"name"				=> "manufacturer_name",
				"type"				=> "text",
				"label"				=> __( "Manufacturer Name", 'wp-easycart' ),
				"required" 			=> true,
				"message" 			=> __( "Please enter a unique manufacturer name.", 'wp-easycart' ),
				"validation_type" 	=> 'text',
				"value"				=> $this->manufacturer->name
			),
			array(
				"name"				=> "post_slug",
				"type"				=> "text",
				"label"				=> __( "Link Slug", 'wp-easycart' ),
				"required" 			=> false,
				"validation_type" 	=> 'post_slug',
				"visible"			=> ($this->id == '0') ? false : true,
				"value"				=> basename( $this->manufacturer->guid ),
				"message"			=> __( "Post Slug values must be unique and may only include letters, numbers, and dashes", 'wp-easycart' )
			)
		) );
		$this->print_fields( $fields );
	}
}