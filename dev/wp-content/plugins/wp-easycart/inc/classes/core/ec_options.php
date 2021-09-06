<?php

class ec_options{
	
	public $wpdb;
	public $options;
	public $optionitems;
	public $optionitemimages;
	
	/****************************************
	* CONSTRUCTOR
	*****************************************/
	function __construct( ){
		global $wpdb;
		$this->wpdb =& $wpdb;
		$this->options = array( );
		$this->optionitems = array( );
		$this->optionitemimages = array( );
	}
	
	public function get_option( $option_id ){
		
		$option = wp_cache_get( 'wpeasycart-option-'.$option_id, 'wpeasycart-options' );
		if( !$option ){
			$option = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM ec_option WHERE ec_option.option_id = %d ORDER BY ec_option.option_id", $option_id ) );
			wp_cache_set( 'wpeasycart-option-'.$option_id, $option, 'wpeasycart-options' );
		}
		return $option;
		
	}
	
	public function get_all_optionitems( ){
		$optionitems = wp_cache_get( 'wpeasycart-optionset-optionitems-all', 'wpeasycart-options' );
		if( !$optionitems ){
			$optionitems = $this->wpdb->get_results( "SELECT ec_optionitem.*, ec_option.option_name, ec_option.option_label FROM ec_optionitem LEFT JOIN ec_option ON ec_option.option_id = ec_optionitem.option_id ORDER BY ec_optionitem.option_id ASC, ec_optionitem.optionitem_order ASC" );
			wp_cache_set( 'wpeasycart-optionset-optionitems-all', $optionitems, 'wpeasycart-options' );
		}
		return $optionitems;
	}
	
	public function get_optionitems( $option_id ){
		
		$optionitems = wp_cache_get( 'wpeasycart-optionset-optionitems-'.$option_id, 'wpeasycart-options' );
		if( !$optionitems ){
			$optionitems = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM ec_optionitem WHERE ec_optionitem.option_id = %d ORDER BY ec_optionitem.option_id ASC, ec_optionitem.optionitem_order ASC", $option_id ) );
			$optionitems = apply_filters( 'wpeasycart_basic_optionitems', $optionitems );
			wp_cache_set( 'wpeasycart-optionset-optionitems-'.$option_id, $optionitems, 'wpeasycart-options' );
		}
		return $optionitems;
		
	}
	
	public function get_optionitem( $optionitem_id ){
		
		$optionitem = wp_cache_get( 'wpeasycart-optionitem-'.$optionitem_id, 'wpeasycart-optionitems' );
		if( !$optionitem ){
			$optionitem = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM ec_optionitem WHERE ec_optionitem.optionitem_id = %d ORDER BY ec_optionitem.option_id ASC, ec_optionitem.optionitem_order ASC", $optionitem_id ) );
			$optionitem = apply_filters( 'wpeasycart_basic_optionitem', $optionitem );
			wp_cache_set( 'wpeasycart-optionitem-'.$optionitem_id, $optionitem, 'wpeasycart-optionitems' );
		}
		return $optionitem;
		
	}
	
	public function get_optionitem_images( $product_id ){
		
		$optionitem_images = wp_cache_get( 'wpeascyart-optionitem-images-'.$product_id, 'wpeasycart-optionitems' );
		if( !$optionitem_images ){
			$optionitem_images = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT 
						ec_optionitemimage.optionitemimage_id,
						ec_optionitemimage.optionitem_id, 
						ec_optionitemimage.product_id, 
						ec_optionitemimage.image1, 
						ec_optionitemimage.image2, 
						ec_optionitemimage.image3, 
						ec_optionitemimage.image4, 
						ec_optionitemimage.image5,
						ec_optionitem.optionitem_order
						
						FROM ec_optionitemimage, ec_optionitem
		
						WHERE 
						ec_optionitemimage.product_id = %d AND
						ec_optionitem.optionitem_id = ec_optionitemimage.optionitem_id
						
						ORDER BY
						ec_optionitem.optionitem_order", $product_id ) );
			wp_cache_set( 'wpeascyart-optionitem-images-'.$product_id, $optionitem_images, 'wpeasycart-optionitems' );
		}
		return $optionitem_images;
		
	}
	
	public function get_optionitem_image1( $product_id, $optionitem_id ){
		
		$optionitem_image1 = wp_cache_get( 'wpeasycart-optionitem-image1-'.$product_id.'-'.$optionitem_id, 'wpeasycart-optionitems' );
		if( !$optionitem_image1 ){
			if( $product_id && $optionitem_id ){
				$optionitem_image1 = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT ec_optionitemimage.image1 FROM ec_optionitemimage WHERE ec_optionitemimage.optionitem_id = %d AND ec_optionitemimage.product_id = %d", $optionitem_id, $product_id ) );
			}else{
				$optionitem_image1 = false;
			}
			wp_cache_set( 'wpeasycart-optionitem-image1-'.$product_id.'-'.$optionitem_id, $optionitem_image1, 'wpeasycart-optionitems' );
		}
		return $optionitem_image1;
		
	}	
		
}

?>