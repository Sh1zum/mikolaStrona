<?php 

class ec_prodimages{
	
	public $use_optionitem_images;						// BOOL
	public $options;									// ec_prodoptions
	public $model_number;								// VARCHAR 255
	public $additional_link_options;					// TEXT
	public $post_id;									// INT
	public $guid;										// Permalink to Product
	public $is_deconetwork;								// BOOL
	public $deconetwork_link;							// VARCHAR
	
	public $image1;										// VARCHAR 255
	public $image2;										// VARCHAR 255
	public $image3;										// VARCHAR 255
	public $image4;										// VARCHAR 255
	public $image5;										// VARCHAR 255
	
	public $imageset = array();							// Array of ec_prodimageset structure
	
	private $store_page;
	private $permalink_divider;
	
	function __construct( $product_id, $options, $model_number, $use_optionitem_images, $image1, $image2, $image3, $image4, $image5, $image_data, $additional_link_options, $post_id, $guid, $is_deconetwork = false, $deconetwork_link = "" ){
		
		$this->use_optionitem_images = $use_optionitem_images;
		$this->options = $options;
		$this->model_number = $model_number;
		$this->post_id = $post_id;
		$this->guid = $guid;
		$this->additional_link_options = $additional_link_options;
		$this->is_deconetwork = $is_deconetwork;
		$this->deconetwork_link = $deconetwork_link;
		
		$this->image1 = $image1;
		$this->image2 = $image2;
		$this->image3 = $image3;
		$this->image4 = $image4;
		$this->image5 = $image5;
		
		$storepageid = get_option( 'ec_option_storepage' );
		
		if( function_exists( 'icl_object_id' ) ){
			$storepageid = icl_object_id( $storepageid, 'page', true, ICL_LANGUAGE_CODE );
		}
		
		$this->store_page = get_permalink( $storepageid );
		
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$this->store_page = $https_class->makeUrlHttps( $this->store_page );
		}
		
		if( substr_count( $this->store_page, '?' ) )						$this->permalink_divider = "&";
		else																$this->permalink_divider = "?";
		
		if( $this->use_optionitem_images ){
			
			for( $i=0; $i<count( $image_data ); $i++ ){
				if( $i==0 ){
					$this->image1 = $image_data[$i]->image1;
					$this->image2 = $image_data[$i]->image2;
					$this->image3 = $image_data[$i]->image3;
					$this->image4 = $image_data[$i]->image4;
					$this->image5 = $image_data[$i]->image5;
				}
				array_push( $this->imageset, new ec_prodimageset( $product_id, $image_data[$i] ) );
			}
			
		}
		
	}
	
	public function get_single_image( ){
		if( $this->use_optionitem_images && isset( $this->imageset[0] ) ){
			return $this->imageset[0]->image1;	
		}else{
			return $this->image1;
		}
	}
	
	public function get_product_images( $size, $selected, $id_prefix, $js_function_name ){
		
		if( $this->use_optionitem_images )				return $this->get_image_set_html( $size, $selected, false, $id_prefix, $js_function_name, true );
		else{
														$ret_string = $this->get_image_html( 1, $this->image1, true, 0, $size, true, "", false, $id_prefix );
														return $ret_string;
		}
	}
	
	public function get_product_details_images( $size, $selected, $id_prefix, $js_function_name ){
		
		if( $this->use_optionitem_images )				return $this->get_image_set_html( $size, $selected, true, $id_prefix, $js_function_name, false );
		else if( $this->image1 ){
														$ret_string  = $this->get_image_html( 1, $this->image1, true,  0, $size, false, "return " . $js_function_name . "('" . $this->model_number . "', 0, 1);", true, $id_prefix );
														$ret_string .= $this->get_image_html( 2, $this->image2, false, 0, $size, false, "return " . $js_function_name . "('" . $this->model_number . "', 0, 2);", true, $id_prefix );
														$ret_string .= $this->get_image_html( 3, $this->image3, false, 0, $size, false, "return " . $js_function_name . "('" . $this->model_number . "', 0, 3);", true, $id_prefix );
														$ret_string .= $this->get_image_html( 4, $this->image4, false, 0, $size, false, "return " . $js_function_name . "('" . $this->model_number . "', 0, 4);", true, $id_prefix );
														$ret_string .= $this->get_image_html( 5, $this->image5, false, 0, $size, false, "return " . $js_function_name . "('" . $this->model_number . "', 0, 5);", true, $id_prefix );
														return $ret_string;
		}
		
	}
	
	public function has_thumbnails( ){
		if( ( $this->use_optionitem_images &&  $this->imageset[0]->image1 && $this->imageset[0]->image2 ) || ( !$this->use_optionitem_images && $this->image1 && $this->image2 ) )		
																							return true;
		else																				return false;
	}
	
	public function get_product_thumbnails( $size, $initial, $id_prefix, $js_function_name ){
		
		$ret_string = "";
		if( $this->use_optionitem_images )				return $this->get_image_set_thumbnails_html( $size, $initial, $id_prefix, $js_function_name );
			
		else if( $this->image1 && $this->image2 ){
			
			$ret_string .= $this->get_image_html( 1, $this->image1, true, 0, $size, false, "return " . $js_function_name . "('" . $this->model_number . "', 0, 1);", false, $id_prefix );
			$ret_string .= $this->get_image_html( 2, $this->image2, true, 0, $size, false, "return " . $js_function_name . "('" . $this->model_number . "', 0, 2);", false, $id_prefix );
			$ret_string .= $this->get_image_html( 3, $this->image3, true, 0, $size, false, "return " . $js_function_name . "('" . $this->model_number . "', 0, 3);", false, $id_prefix );
			$ret_string .= $this->get_image_html( 4, $this->image4, true, 0, $size, false, "return " . $js_function_name . "('" . $this->model_number . "', 0, 4);", false, $id_prefix );
			$ret_string .= $this->get_image_html( 5, $this->image5, true, 0, $size, false, "return " . $js_function_name . "('" . $this->model_number . "', 0, 5);", false, $id_prefix );
			return $ret_string;
		
		}
		
	}
	
	private function get_image_set_html( $size, $selected, $allow_popup, $id_prefix, $js_function_name, $islink ){
		
		$ret_string = "";
		for( $i=0; $i<count( $this->imageset ); $i++ ){
			
			if( $i == $selected )	
				$ret_string .= $this->get_image_html( 1, $this->imageset[$i]->image1, true,  $i, $size, $islink, "return " . $js_function_name . "('" . $this->model_number . "', " . $i . ", 1);", $allow_popup, $id_prefix );
			
			else
				$ret_string .= $this->get_image_html( 1, $this->imageset[$i]->image1, false, $i, $size, $islink, "return " . $js_function_name . "('" . $this->model_number . "', " . $i . ", 1);", $allow_popup, $id_prefix );
			
			if( $this->imageset[$i]->image2 != "" )
			$ret_string .= $this->get_image_html( 2, $this->imageset[$i]->image2, false, $i, $size, $islink, "return " . $js_function_name . "('" . $this->model_number . "', " . $i . ", 2);", $allow_popup, $id_prefix );
			
			if( $this->imageset[$i]->image3 != "" )
			$ret_string .= $this->get_image_html( 3, $this->imageset[$i]->image3, false, $i, $size, $islink, "return " . $js_function_name . "('" . $this->model_number . "', " . $i . ", 3);", $allow_popup, $id_prefix );
			
			if( $this->imageset[$i]->image4 != "" )
			$ret_string .= $this->get_image_html( 4, $this->imageset[$i]->image4, false, $i, $size, $islink, "return " . $js_function_name . "('" . $this->model_number . "', " . $i . ", 4);", $allow_popup, $id_prefix );
			
			if( $this->imageset[$i]->image5 != "" )
			$ret_string .= $this->get_image_html( 5, $this->imageset[$i]->image5, false, $i, $size, $islink, "return " . $js_function_name . "('" . $this->model_number . "', " . $i . ", 5);", $allow_popup, $id_prefix );
				
		}
		
		return $ret_string;
	}
	
	private function get_image_set_thumbnails_html( $size, $initial, $id_prefix, $js_function_name ){
		
		$ret_string = "";
		for( $i=0; $i<count( $this->imageset ); $i++ ){
			
			if( $i == $initial )		$display = true;
			else						$display = false;
			
			if( $this->imageset[$i]->image1 && $this->imageset[$i]->image2 ){
				
				$ret_string .= $this->get_image_html( 1, $this->imageset[$i]->image1, $display, $i, $size, false, "return " . $js_function_name . "('" . $this->model_number . "', " . $i . ", 1);", false, $id_prefix );
				$ret_string .= $this->get_image_html( 2, $this->imageset[$i]->image2, $display, $i, $size, false, "return " . $js_function_name . "('" . $this->model_number . "', " . $i . ", 2);", false, $id_prefix );
				$ret_string .= $this->get_image_html( 3, $this->imageset[$i]->image3, $display, $i, $size, false, "return " . $js_function_name . "('" . $this->model_number . "', " . $i . ", 3);", false, $id_prefix );
				$ret_string .= $this->get_image_html( 4, $this->imageset[$i]->image4, $display, $i, $size, false, "return " . $js_function_name . "('" . $this->model_number . "', " . $i . ", 4);", false, $id_prefix );
				$ret_string .= $this->get_image_html( 5, $this->imageset[$i]->image5, $display, $i, $size, false, "return " . $js_function_name . "('" . $this->model_number . "', " . $i . ", 5);", false, $id_prefix );
			}
			
		}
		
		return $ret_string;
	}
	
	private function get_image_html( $level, $img, $active, $i, $size, $islink, $js, $allow_popup, $id_prefix){
		
		if( $this->is_deconetwork ){
			$permalink = $this->deconetwork_link;
		}else{
			$permalink = $this->ec_get_permalink( $this->post_id );
			$add_options_divider = "?";
			if( substr_count( $permalink, '?' ) ){
				$add_options_divider = "&";
			}
		}
		
		if( $img ){
			/////////////////////////////////////////////////////////////
			// GET IMAGE SOURCE
			/////////////////////////////////////////////////////////////
			
			$test_src = ABSPATH . "wp-content/plugins/" . EC_PLUGIN_DIRECTORY . "/products/pics" . $level . "/" . $img;
			$test_src2 = ABSPATH . "wp-content/plugins/wp-easycart-data/products/pics" . $level . "/" . $img;
			$test_src3 = ABSPATH . "wp-content/plugins/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_image_not_found.jpg";
			$test_src4 = ABSPATH . "wp-content/plugins/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg";
			
			if( substr( $img, 0, 7 ) == 'http://' || substr( $img, 0, 8 ) == 'https://' )
				$image_src = $img;
			else if( file_exists( $test_src2 ) )
				$image_src = plugins_url( "wp-easycart-data/products/pics" . $level . "/" . $img );
			else if( file_exists( $test_src ) )
				$image_src = plugins_url( EC_PLUGIN_DIRECTORY . "/products/pics" . $level . "/" . $img );
			else if( file_exists( $test_src3 ) )
				$image_src = plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_image_not_found.jpg" );
			else if( file_exists( $test_src4 ) )
				$image_src = plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg" );
			else
				$image_src = plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option( 'ec_option_latest_theme' ) . "/images/ec_image_not_found.jpg" );
			
			/////////////////////////////////////////////////////////////
			// END SOURCE IMAGE CODE
			/////////////////////////////////////////////////////////////
			
			if( $islink ){
				
				$ret_string = "<a href=\"" . $permalink;
				
				if( $i < count( $this->imageset ) && $this->imageset[$i]->optionitem_id )
					$ret_string .= $add_options_divider . "optionitem_id=" . $this->imageset[$i]->optionitem_id;
				
				if( ( $i < count( $this->imageset ) && $this->imageset[$i]->optionitem_id ) && $this->additional_link_options )
					$ret_string .= $this->additional_link_options;
				else if( $this->additional_link_options )
					$ret_string .= $add_options_divider . substr( $this->additional_link_options, 5, strlen( $this->additional_link_options ) - 5 );
				
				$ret_string .= "\" class=\"ec_product_image";
				
				if( !$active )
				$ret_string .= "_inactive";
				
				$ret_string .= "\" ";
				
				if( !$active )
				$ret_string .= " style=\"display: none !important;\" ";
				
			
				$ret_string .= "  id=\"" . $id_prefix . $this->model_number . "_" . $level . "_" . $i . "\" >";
				
				$ret_string .= "<img src=\"" . $image_src . "\"";
				
				if($js)
				$ret_string .= "onclick=\"" . $js . "\"";
				
				$ret_string .= " \>";
				
				$ret_string .= "</a>";
				
			}else if( $allow_popup ){
				
				if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics" . $level . "/" . $img ) )
					$ret_string = "<a href=\"" . plugins_url( "/wp-easycart-data/products/pics" . $level . "/" . $img ) . "\" class=\"ec_product_image";
				else
					$ret_string = "<a href=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/products/pics" . $level . "/" . $img ) . "\" class=\"ec_product_image";
				
				if( !$active )
				$ret_string .= "_inactive";
				
				$ret_string .= "\" ";
				
				if( !$active )
				$ret_string .= " style=\"display: none !important;\" ";
				
				$ret_string .= "   rel = lightbox[".$this->model_number."] id=\"" . $id_prefix . $this->model_number . "_" . $level . "_" . $i . "\" >";
				
				$ret_string .= "<img src=\"" . $image_src . "\"";
				
				if($js)
				$ret_string .= "onclick=\"" . $js . "\"";
				
				$ret_string .= " \>";
				
				$ret_string .= "</a>";
				
			}else{
				$ret_string = "<img src=\"" . $image_src . "\"";
				$ret_string .= " class=\"ec_product_image";	
				
				if( !$active )
				$ret_string .= "_inactive";
				
				$ret_string .= "\" ";
				
				if( !$active )
				$ret_string .= " style=\"display: none !important;\" ";
				
				$ret_string .= " id=\"" . $id_prefix . $this->model_number . "_" . $level . "_" . $i . "\"";
				
				if($js)
				$ret_string .= "onclick=\"" . $js . "\"";
				
				$ret_string .= " \>";
			}
			
			return $ret_string;
			
		}
		
	}
	
	private function ec_get_permalink( $postid ){
		
		if( !get_option( 'ec_option_use_old_linking_style' ) && $postid != "0" ){
			return $this->guid;
		}else{
			return $this->store_page . $this->permalink_divider . "model_number=" . $this->model_number;
		}
		
	}
	
}

?>