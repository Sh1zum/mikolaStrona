<?php
if( !defined( 'ABSPATH' ) ) exit;

class wp_easycart_admin_details_optionitem extends wp_easycart_admin_details{
	
	public $optionitem;
	public $option;

	
	public function __construct( ){
		parent::__construct( );
		add_action( 'wp_easycart_admin_optionitem_details_basic_fields', array( $this, 'basic_fields' ) );
		add_action( 'wp_easycart_admin_optionitem_details_advanced_fields', array( $this, 'advanced_fields' ) );
		add_action( 'wp_easycart_admin_optionitem_details_price_fields', array( $this, 'price_fields' ) );
		add_action( 'wp_easycart_admin_optionitem_details_weight_fields', array( $this, 'weight_fields' ) );
		add_filter( 'wp_easycart_admin_optionitem_details_basic_fields_list', array( $this, 'maybe_remove_icon_field' ) );
	}
	
	protected function init( ){
		$this->docs_link = 'http://docs.wpeasycart.com/wp-easycart-administrative-console-guide/?wpeasycartadmin=1&section=option';
		$this->id = 0;
		$this->page = 'wp-easycart-products';
		$this->subpage = 'optionitems';
		$this->action = 'admin.php?page=' . $this->page . '&subpage=' . $this->subpage;
		$this->form_action = 'add-new-optionitem';
		$this->optionitem = (object) array(
			"optionitem_id"							=> "",
			"optionitem_id"              			=> "",
			"option_id"              				=> "",
			"optionitem_name"             			=> "",
			"optionitem_price"              		=> "",
			"optionitem_price_onetime"             	=> "",
			"optionitem_price_override"             => "",
			"optionitem_price_multiplier"           => "",
			"optionitem_price_per_character"        => "",
			"optionitem_weight"              		=> "",
			"optionitem_weight_onetime"             => "",
			"optionitem_weight_override"            => "",	
			"optionitem_weight_multiplier"          => "",	
			"optionitem_order"              		=> "",	
			"optionitem_icon"              			=> "",
			"optionitem_initial_value"              => "",	
			"optionitem_model_number"              	=> "",	
			"optionitem_allow_download"             => "",	
			"optionitem_disallow_shipping"          => "",
			"optionitem_initially_selected"         => "",
			"optionitem_download_override_file"     => "",
			"optionitem_download_addition_file"     => ""
		);
		if( isset( $_GET['option_id'] ) )
			$this->option = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT ec_option.* FROM ec_option WHERE option_id = %d", $_GET['option_id'] ) );
	}
	
	protected function init_data( ){
		$this->form_action = 'update-optionitem';
		$this->optionitem = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT ec_optionitem.* FROM ec_optionitem WHERE optionitem_id = %d", $_GET['optionitem_id'] ) );
		$this->id = $this->optionitem->optionitem_id;
		$this->option = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT ec_option.* FROM ec_option WHERE option_id = %d", $this->optionitem->option_id ) );
	}

	public function output( $type = 'edit' ){
		$this->init( );
		if( $type == 'edit' )
			$this->init_data( );
		
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/options/optionitem-details.php' );
	}
	
	public function basic_fields( ){
		$swatch_image = $this->optionitem->optionitem_icon;
		
		if( $swatch_image != "" && substr( $swatch_image, 0, 7 ) != "http://" && substr( $swatch_image, 0, 8 ) != "https://" ){
			$swatch_image = plugins_url( '/wp-easycart-data/products/swatches/' . $swatch_image );
		}

		$fields = array(
			array(
				"name"				=> "optionitem_id",
				"alt_name"			=> "optionitem_id",
				"type"				=> "hidden",
				"value"				=> $this->optionitem->optionitem_id
			),
			
			array(
				"name"				=> "optionitem_name",
				"type"				=> "text",
				"label"				=> __( "Option Item Name", 'wp-easycart' ),
				"required" 			=> true,
				"message" 			=> __( "Please enter a unique option item name.", 'wp-easycart' ),
				"validation_type" 	=> 'text',
				"value"				=> $this->optionitem->optionitem_name
			),
			array(
				"name"				=> "optionitem_order",
				"type"				=> "number",
				"label"				=> __( "Option Sort Order", 'wp-easycart' ),
				"decimals" 			=> 0,
				"step" 				=> 1,
				"required" 			=> true,
				"message" 			=> __( "Please enter a unique sort order number value.", 'wp-easycart' ),
				"validation_type" 	=> 'number',
				"value"				=> $this->optionitem->optionitem_order
			),
			array(
				"name"				=> "optionitem_model_number",
				"type"				=> "text",
				"label"				=> __( "Model Number Extension", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please enter a unique model number extension (letters, numbers and dashes only).", 'wp-easycart' ),
				"validation_type" 	=> 'model_number',
				"value"				=> $this->optionitem->optionitem_model_number
			),
			array(
				"name"				=> "optionitem_icon",
				"type"				=> "image_upload",
				"label"				=> __( "Image Swatch (optional)", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please select an image for this option swatch.", 'wp-easycart' ),
				"validation_type" 	=> 'image_upload',
				"value"				=> $swatch_image
			)
		);
		
		if( $this->option->option_type == 'basic-combo' || $this->option->option_type == 'basic-swatch' ){
			$fields[] = array(
				"name"				=> "optionitem_price",
				"type"				=> "currency",
				"label"				=> __( "Price Adjustment (+/-)", 'wp-easycart' ),
				"required" 			=> false,
				"validation_type" 	=> 'price',
				"visible"			=> true,
				"value"				=> $this->optionitem->optionitem_price
			);
		}
		
		if( $this->option->option_type == 'basic-combo' || $this->option->option_type == 'basic-swatch' ){
			$fields[] = array(
				"name"				=> "optionitem_weight",
				"type"				=> "number",
				"label"				=> __( "Weight Adjustment (+/-)", 'wp-easycart' ),
				"required" 			=> false,
				"validation_type" 	=> 'number',
				"visible"			=> true,
				"value"				=> $this->optionitem->optionitem_weight
			);
		}
		$fields = apply_filters( 'wp_easycart_admin_optionitem_details_basic_fields_list', $fields );
		$this->print_fields( $fields );
	}
	
	public function maybe_remove_icon_field( $fields ){
		
		if( !$this->is_swatch( ) ){
			$new_fields = array( );
			for( $i=0; $i<count( $fields ); $i++ ){
				if( $fields[$i]['name'] != 'optionitem_icon' ){
					$new_fields[] = $fields[$i];
				}
			}
			return $new_fields;
		}else{
			return $fields;
		}
	}
	
	public function is_swatch( ){
		global $wpdb;
		if( isset( $_GET['option_id'] ) )
			$option_id = $_GET['option_id'];
		else
			$option_id = $this->optionitem->option_id;
		
		if( $this->option->option_type == 'basic-swatch' || $this->option->option_type == 'swatch' )
			return true;
		else
			return false;
	}
			
	public function advanced_fields( ){
		
		$fields = apply_filters( 'wp_easycart_admin_optionitem_details_advanced_fields_list', array(	
			array(
				"name"				=> "optionitem_initially_selected",
				"type"				=> "checkbox",
				"label"				=> __( "Initially Selected?", 'wp-easycart' ),
				"required" 			=> false,
				"message"			=> "",
				"selected" 			=> false,
				"validation_type" 	=> 'checkbox',
				"value" 			=> $this->optionitem->optionitem_initially_selected
			),
			array(
				"name"				=> "optionitem_allow_download",
				"type"				=> "checkbox",
				"label"				=> __( "Option Allows Product Download?", 'wp-easycart' ),
				"required" 			=> false,
				"message"			=> "",
				"selected" 			=> false,
				"validation_type" 	=> 'checkbox',
				"value" 			=> $this->optionitem->optionitem_allow_download
			),
			array(
				"name"				=> "optionitem_download_override_file",
				"type"				=> "image_upload",
				"button_label"		=> __( "Upload File", 'wp-easycart' ),
				"label"				=> __( "Selection Here Overrides Default Download File", 'wp-easycart' ),
				"validation_type" 	=> 'image',
				"image_action"		=> 'ec_admin_download_upload',
				"visible"			=> true,
				"delete_label"		=> __( 'Remove File', 'wp-easycart' ),
				"value"				=> $this->optionitem->optionitem_download_override_file
			),
			array(
				"name"				=> "optionitem_download_addition_file",
				"type"				=> "image_upload",
				"button_label"		=> __( "Upload File", 'wp-easycart' ),
				"label"				=> __( "Selection Here Adds a File to Download", 'wp-easycart' ),
				"validation_type" 	=> 'image',
				"image_action"		=> 'ec_admin_download_upload',
				"visible"			=> true,
				"delete_label"		=> __( 'Remove File', 'wp-easycart' ),
				"value"				=> $this->optionitem->optionitem_download_addition_file
			),
			array(
				"name"				=> "optionitem_disallow_shipping",
				"type"				=> "checkbox",
				"label"				=> __( "Option Makes NO Shipping on Product?", 'wp-easycart' ),
				"required" 			=> false,
				"message"			=> "",
				"selected" 			=> false,
				"validation_type" 	=> 'checkbox',
				"value" 			=> $this->optionitem->optionitem_disallow_shipping
			),
			array(
				"name"				=> "optionitem_initial_value",
				"type"				=> ( $this->optionitem->optionitem_name == 'Text Box Input' ) ? "text" : "currency",
				"label"				=> __( "Initial Value", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please enter the initial value of option.", 'wp-easycart' ),
				"validation_type" 	=> 'text',
				"value"				=> $this->optionitem->optionitem_initial_value
			),
		) );
		$this->print_fields( $fields );
	}
    
	public function price_fields( ){

		$fields = apply_filters( 'wp_easycart_admin_optionitem_details_price_fields_list', array(	
			array(
				"name"				=> "optionitem_price",
				"type"				=> "currency",
				"label"				=> __( "Basic Price Adjustment", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please enter a basic price adjustment for this option", 'wp-easycart' ),
				"validation_type" 	=> 'currency',
				"value"				=> $this->optionitem->optionitem_price
			),
			array(
				"name"				=> "optionitem_price_onetime",
				"type"				=> "currency",
				"label"				=> __( "One Time Price", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please enter a one time price adjustment for this option", 'wp-easycart' ),
				"validation_type" 	=> 'currency',
				"value"				=> $this->optionitem->optionitem_price_onetime
			),
			array(
				"name"				=> "optionitem_price_override",
				"type"				=> "currency",
				"label"				=> __( "Price Over-Ride", 'wp-easycart' ),
				"required" 			=> false,
				"default"			=> "-1.000",
				"message" 			=> __( "Please enter an override price adjustment for this option", 'wp-easycart' ),
				"validation_type" 	=> 'currency',
				"value"				=> $this->optionitem->optionitem_price_override
			),
			array(
				"name"				=> "optionitem_price_multiplier",
				"type"				=> "currency",
				"label"				=> __( "Price Multiplier", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please enter a price multiplier adjustment for this option", 'wp-easycart' ),
				"validation_type" 	=> 'currency',
				"value"				=> $this->optionitem->optionitem_price_multiplier
			),
			array(
				"name"				=> "optionitem_price_per_character",
				"type"				=> "currency",
				"label"				=> __( "Price Per Character", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please enter a price per character for this option", 'wp-easycart' ),
				"validation_type" 	=> 'currency',
				"value"				=> $this->optionitem->optionitem_price_per_character
			)
        ) );
		$this->print_fields( $fields );
	}
    
	public function weight_fields( ){
		$fields = apply_filters( 'wp_easycart_admin_optionitem_details_weight_fields_list', array(		
			array(
				"name"				=> "optionitem_weight",
				"type"				=> "currency",
				"label"				=> __( "Basic Weight Adjustment", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please enter a basic weight adjustment for this option", 'wp-easycart' ),
				"validation_type" 	=> 'currency',
				"value"				=> $this->optionitem->optionitem_weight
			),
			array(
				"name"				=> "optionitem_weight_onetime",
				"type"				=> "currency",
				"label"				=> __( "One Time Weight", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please enter a one time weight adjustment for this option", 'wp-easycart' ),
				"validation_type" 	=> 'currency',
				"value"				=> $this->optionitem->optionitem_weight_onetime
			),
			array(
				"name"				=> "optionitem_weight_override",
				"type"				=> "currency",
				"label"				=> __( "Weight Over-Ride", 'wp-easycart' ),
				"required" 			=> false,
				"default"			=> "-1.000",
				"message" 			=> __( "Please enter an override weight adjustment for this option", 'wp-easycart' ),
				"validation_type" 	=> 'currency',
				"value"				=> $this->optionitem->optionitem_weight_override
			),
			array(
				"name"				=> "optionitem_weight_multiplier",
				"type"				=> "currency",
				"label"				=> __( "Weight Multiplier", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please enter a weight multiplier adjustment for this option", 'wp-easycart' ),
				"validation_type" 	=> 'currency',
				"value"				=> $this->optionitem->optionitem_weight_multiplier
			)
		) );
		$this->print_fields( $fields );
	}
}