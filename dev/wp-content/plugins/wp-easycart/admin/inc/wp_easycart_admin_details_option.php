<?php
if( !defined( 'ABSPATH' ) ) exit;

class wp_easycart_admin_details_option extends wp_easycart_admin_details{
	
	public $option;

	
	public function __construct( ){
		parent::__construct( );
		add_action( 'wp_easycart_admin_option_details_basic_fields', array( $this, 'basic_fields' ) );
	}
	
	protected function init( ){
		$this->docs_link = 'http://docs.wpeasycart.com/wp-easycart-administrative-console-guide/?wpeasycartadmin=1&section=option-sets';
		$this->id = 0;
		$this->page = 'wp-easycart-products';
		$this->subpage = 'option';
		$this->action = 'admin.php?page=' . $this->page . '&subpage=' . $this->subpage;
		$this->form_action = 'add-new-option';
		$this->option = (object) array(
			"option_id"					=> "",
			"option_name"					=> "",
			"option_label"					=> "",
			"option_type"					=> "",
			"option_required"				=> false,
			"option_error_text"				=> "",
			"option_meta"					=> false,
			"post_id"						=> "",
			"parent_id"						=> "",
			"short_description"				=> "",
			"image"							=> "",
			"featured_option"				=> ""
		);

	}
	
	protected function init_data( ){
		$this->form_action = 'update-option';
		$this->option = $this->item = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT ec_option.* FROM ec_option WHERE option_id = %d", $_GET['option_id'] ) );
		$this->id = $this->option->option_id;
	}

	public function output( $type = 'edit' ){
		$this->init( );
		if( $type == 'edit' )
			$this->init_data( );
		
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/options/option-details.php' );
	}
	
	public function basic_fields( ){
		$option_type = array( 
			(object) array(
				'id'	=> 'basic-combo',
				'value'	=> __( 'Basic Combo', 'wp-easycart' )
			),
			(object) array(
				'id'	=> 'basic-swatch',
				'value'	=> __( 'Basic Swatch', 'wp-easycart' )
			),
			(object) array(
				'id'	=> apply_filters( 'wp_easycart_admin_advanced_option_type', 'combo' ),
				'value'	=> __( 'Advanced Combo Box', 'wp-easycart' )
			),
			(object) array(
				'id'	=> apply_filters( 'wp_easycart_admin_advanced_option_type', 'swatch' ),
				'value'	=> __( 'Advanced Image Swatches', 'wp-easycart' )
			),
			(object) array(
				'id'	=> apply_filters( 'wp_easycart_admin_advanced_option_type', 'text' ),
				'value'	=> __( 'Advanced Text Input', 'wp-easycart' )
			),
			(object) array(
				'id'	=> apply_filters( 'wp_easycart_admin_advanced_option_type', 'textarea' ),
				'value'	=> __( 'Advanced Text Area', 'wp-easycart' )
			),
			(object) array(
				'id'	=> apply_filters( 'wp_easycart_admin_advanced_option_type', 'number' ),
				'value'	=> __( 'Advanced Number Field', 'wp-easycart' )
			),
			(object) array(
				'id'	=> apply_filters( 'wp_easycart_admin_advanced_option_type', 'file' ),
				'value'	=> __( 'Advanced File Upload', 'wp-easycart' )
			),
			(object) array(
				'id'	=> apply_filters( 'wp_easycart_admin_advanced_option_type', 'radio' ),
				'value'	=> __( 'Advanced Radio Group', 'wp-easycart' )
			),
			(object) array(
				'id'	=> apply_filters( 'wp_easycart_admin_advanced_option_type', 'checkbox' ),
				'value'	=> __( 'Advanced Checkbox Group', 'wp-easycart' )
			),
			(object) array(
				'id'	=> apply_filters( 'wp_easycart_admin_advanced_option_type', 'grid' ),
				'value'	=> __( 'Advanced Quantity Grid', 'wp-easycart' )
			),
			(object) array(
				'id'	=> apply_filters( 'wp_easycart_admin_advanced_option_type', 'date' ),
				'value'	=> __( 'Advanced Date', 'wp-easycart' )
			),
			(object) array(
				'id'	=> apply_filters( 'wp_easycart_admin_advanced_option_type', 'dimensions1' ),
				'value'	=> __( 'Advanced Dimensions (Whole Inch)', 'wp-easycart' )
			),
			(object) array(
				'id'	=> apply_filters( 'wp_easycart_admin_advanced_option_type', 'dimensions2' ),
				'value'	=> __( 'Advanced Dimensions (Sub-Inch)', 'wp-easycart' )
			)
		);
		
		if( $this->option->option_meta ){
			$option_meta = maybe_unserialize( $this->option->option_meta );
		}else{
			$option_meta = array(
				"min"	=> "",
				"max"	=> "",
				"step"	=> "",
				"url_var"	=> ""
			);
		}

		$fields = apply_filters( 'wp_easycart_admin_option_details_basic_fields_list', array(
			array(
				"name"				=> "option_id",
				"alt_name"			=> "option_id",
				"type"				=> "hidden",
				"value"				=> $this->option->option_id
			),
			array(
				"name"				=> "option_type",
				"type"				=> "select",
				"data"				=> $option_type,
				"data_label" 		=> __( "Please Select", 'wp-easycart' ),
				"label" 			=> __( "Option Type", 'wp-easycart' ),
				"required" 			=> true,
				"message" 			=> __( "Please select an option type", 'wp-easycart' ),
				"value" 			=> $this->option->option_type,
				"validation_type" 	=> 'select',
				"onchange"			=> 'ec_admin_option_type_change'
			),
			array(
				"name"				=> "option_name",
				"type"				=> "text",
				"label"				=> __( "Option Name", 'wp-easycart' ),
				"required" 			=> true,
				"message" 			=> __( "Please enter a unique option name.", 'wp-easycart' ),
				"validation_type" 	=> 'text',
				"value"				=> $this->option->option_name
			),
			array(
				"name"				=> "option_label",
				"type"				=> "text",
				"label"				=> __( "Option Label", 'wp-easycart' ),
				"required" 			=> true,
				"message" 			=> __( "Please enter an option label to display to user.", 'wp-easycart' ),
				"validation_type" 	=> 'text',
				"value"				=> $this->option->option_label
			),
			array(
				"name"				=> "option_meta_url_var",
				"type"				=> "text",
				"label"				=> __( "Option URL Variable", 'wp-easycart' ),
				"required" 			=> false,
				"validation_type" 	=> 'text',
				"value"				=> $option_meta["url_var"]
			),
			array(
				"name"				=> "option_meta_min",
				"type"				=> "number",
				"label"				=> __( "Minimum Value", 'wp-easycart' ),
				"required" 			=> false,
				"validation_type" 	=> 'text',
				"value"				=> $option_meta["min"],
				"requires"			=> array(
					"name"			=> "option_type",
					"value"			=> 'number',
					"default_show"	=> false
				),
				"visible"			=> false
			),
			array(
				"name"				=> "option_meta_max",
				"type"				=> "number",
				"label"				=> __( "Maximum Value", 'wp-easycart' ),
				"required" 			=> false,
				"validation_type" 	=> 'text',
				"value"				=> $option_meta["max"],
				"requires"			=> array(
					"name"			=> "option_type",
					"value"			=> 'number',
					"default_show"	=> false
				),
				"visible"			=> false
			),
			array(
				"name"				=> "option_meta_step",
				"type"				=> "number",
				"label"				=> __( "Step (e.g.: .01, .1, 1)", 'wp-easycart' ),
				"required" 			=> false,
				"validation_type" 	=> 'text',
				"value"				=> $option_meta["step"],
				"requires"			=> array(
					"name"			=> "option_type",
					"value"			=> 'number',
					"default_show"	=> false
				),
				"visible"			=> false
			),
			array(
				"type"				=> "heading",
				"label"				=> __( "Advanced Option Settings", 'wp-easycart' ),
				"horizontal_rule"	=> true, 
				"message" 			=> __( "Only advanced options can be optional/required to your users on the product.  Advanced options allow for much more configuration, but they can not track quantity per option. Only basic options can track quantity at their option level.  Other than this, advanced options are the most configurable and best option set to use.", 'wp-easycart' ),
			),
			array(
				"name"				=> "option_required",
				"type"				=> "checkbox",
				"label"				=> __( "Is Option going to be required by user?", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please enter an option", 'wp-easycart' ),
				"validation_type" 	=> 'checkbox',
				"show"  => array(
					"name" =>"option_error_text",
					"value"=>"1"
				),
				"value"				=> $this->option->option_required
			),
			array(
				"name"				=> "option_error_text",
				"type"				=> "text",
				"label"				=> __( "Error Message", 'wp-easycart' ),
				"required" 			=> false,
				"message" 			=> __( "Please enter an option error message to display to user.", 'wp-easycart' ),
				"validation_type" 	=> 'text',
				"requires"=>array(
					"name"=>"option_required",
					"value"=>"1"
				),
				"value"				=> $this->option->option_error_text
			)
		) );
		$this->print_fields( $fields );
	}
}