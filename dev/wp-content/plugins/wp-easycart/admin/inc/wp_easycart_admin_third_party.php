<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_third_party' ) ) :

final class wp_easycart_admin_third_party{
	
	protected static $_instance = null;
	
	public $google_analytics_design_file;
	public $google_adwords_design_file;
	public $google_merchant_file;
	public $settings_file;
	public $upgrade_file;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
		
	public function __construct( ){
		// Setup File Names 
		$this->google_analytics_design_file	 = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/third-party/google-analytics.php';
		$this->google_adwords_design_file	 = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/third-party/google-adwords.php';
		$this->google_merchant_file	 		 = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/third-party/google-merchant.php';
		$this->settings_file		 		 = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/third-party/settings.php';
		$this->upgrade_file 				= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/upgrade/upgrade-simple.php';
		
		// Load Panels
		add_action( 'wpeasycart_admin_third_party', array( $this, 'load_google_analytics_design' ) );
		add_action( 'wpeasycart_admin_third_party', array( $this, 'load_google_adwords_design' ) );
		add_action( 'wpeasycart_admin_third_party', array( $this, 'load_facebook_settings' ) );
		add_action( 'wpeasycart_admin_third_party', array( $this, 'load_google_merchant' ) );
		add_action( 'wpeasycart_admin_third_party', array( $this, 'load_amazon_settings' ) );
		add_action( 'wpeasycart_admin_third_party', array( $this, 'load_deconetwork_settings' ) );
		
		// Process Actions
		add_filter( 'wp_easycart_admin_success_messages', array( $this, 'add_success_messages' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_download_csv' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_upload_feed' ) );
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_download_feed' ) );
	}
	
	public function process_download_csv( ){
		if( $_GET['ec_admin_form_action'] == 'download-google-csv' ){
			global $wpdb;
			$products = $wpdb->get_results( "SELECT ec_product.product_id, ec_product.model_number, ec_product.title, ec_product.price, ec_product.list_price, ec_manufacturer.name as manufacturer_name FROM ec_product LEFT JOIN ec_manufacturer ON ec_manufacturer.manufacturer_id = ec_product.manufacturer_id ORDER BY ec_product.title ASC" );
			
			$data = 'product_id,model_number,title,price,sale_price,brand,google_product_category,product_type,condition,gtin,mpn,identifier_exists,gender,age_group,size_type,size_system,item_group_id,color,material,pattern,size,weight_type,shipping_label';
			$data .= "\n";
			foreach( $products as $product ){ 
				$attributes_result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_product_google_attributes WHERE product_id = %d", $product->product_id ) );
				if( $attributes_result ){
					$attributes = json_decode( $attributes_result->attribute_value, true );
				}else{
					$attributes = array( 	"google_product_category" => "None Selected",
											"product_type" => "",
											"condition" => "",
											"gtin" => "",
											"mpn" => "",
											"identifier_exists" => "",
											"gender" => "",
											"age_group" => "",
											"size_type" => "",
											"size_system" => "",
											"item_group_id" => "",
											"color" => "",
											"material" => "",
											"pattern" => "",
											"size" => "",
											"weight_type" => "lb",
											"shipping_label" => "" );
				
				}
				
				$data .= '"' . str_replace( '"', '""', $product->product_id ) . '","' . str_replace( '"', '""', $product->model_number ) . '","' . str_replace( '"', '""', $product->title ) . '","' . str_replace( '"', '""', $product->price ) . '","' . str_replace( '"', '""', $product->list_price ) . '","' . str_replace( '"', '""', $product->manufacturer_name ) . '"';
				foreach( $attributes as $attribute ){
					$data .= ',"' . str_replace( '"', '""', $attribute ) . '"';
				}
				$data .= "\n";
			}
			header("Content-type: text/csv; charset=UTF-8");
			header("Content-Transfer-Encoding: binary"); 
			header("Content-Disposition: attachment; filename=google-feed.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
		
			echo $data;
			die( );
		}
	}
	
	public function process_upload_feed( ){
		if( $_GET['ec_admin_form_action'] == 'upload-google-csv' ){
			global $wpdb;
			$file = fopen( $_FILES['csv_file']['tmp_name'], "r" );
			$headers = fgetcsv( $file );
			if( $headers[0] != "product_id" ){
				echo "You must upload a CSV with the first column product_id";
				die( );
			
			}else if( count( $headers ) != 23 ){
				echo "You must have 23 columns in your CSV file. You should download and add content, do not delete columns or rows.";
				die( );
			
			}else{
				$line_number = 1;
				$eof_reached = false;
				while( !feof( $file ) && !$eof_reached ){ // each time through, run up to the limit of items until eof hit.
					$row = fgetcsv( $file );
					if( strlen( trim( $row[0] ) ) <= 0 ){ // checking for file with extra rows that are empty
						$eof_reached = true;
					}else{
						if( count( $row ) != 23 ){
							echo "Something went wrong when processing line " . $line_number . ". Please ensure you have data in all 23 columns to continue.";
							die( );
						}else{
							// Save your Google Merchant Product Options
							$attribute_array = array( 	"google_product_category" 	=> $row[6],
														"product_type" 				=> $row[7],
														"condition" 				=> $row[8],
														"gtin" 						=> $row[9],
														"mpn" 						=> $row[10],
														"identifier_exists" 		=> $row[11],
														"gender" 					=> $row[12],
														"age_group" 				=> $row[13],
														"size_type" 				=> $row[14],
														"size_system" 				=> $row[15],
														"item_group_id" 			=> $row[16],
														"color" 					=> $row[17],
														"material" 					=> $row[18],
														"pattern" 					=> $row[19],
														"size" 						=> $row[20],
														"weight_type" 				=> $row[21],
														"shipping_label" 			=> $row[22] );
														
							$attribute_json = json_encode( $attribute_array );
							
							$product = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.product_id FROM ec_product WHERE ec_product.product_id = %d", $row[0] ) );
							if( $product ){
								$wpdb->query( $wpdb->prepare( "DELETE FROM ec_product_google_attributes WHERE product_id = %d", $row[0] ) );
								$wpdb->query( $wpdb->prepare( "INSERT INTO ec_product_google_attributes(product_id, attribute_value) VALUES( %d, %s )", $row[0], $attribute_json ) );
							}else{
								
								echo "No product found with product_id " . $row[0] . " on line " . $line_number;
								die( );
								
							}
					
						}// close row check
					
						$line_number++;
						
					} // close end of file check
					
				} // close while loop
				
				fclose( $file );
				
			}
			
			header( "location:admin.php?page=wp-easycart-settings&subpage=third-party&success=google-import-complete" );
		}
	}
	
	public function process_download_feed( ){
		if( $_GET['ec_admin_form_action'] == 'download-feed' ){
			global $wpdb;
			$products = $wpdb->get_results( "SELECT ec_product.product_id, ec_product.model_number, ec_product.show_stock_quantity, ec_product.stock_quantity, ec_product.title, ec_product.description, ec_product.price, ec_product.list_price, ec_product.weight, ec_product.post_id, ec_product.image1, ec_product.option_id_1, ec_product.option_id_2, ec_product.option_id_3, ec_product.option_id_4, ec_product.option_id_5, ec_manufacturer.name as manufacturer_name FROM ec_product LEFT JOIN ec_manufacturer ON ec_manufacturer.manufacturer_id = ec_product.manufacturer_id WHERE ec_product.activate_in_store = 1 ORDER BY ec_product.title ASC" );
			$file_contents =  "<?xml version=\"1.0\"?>\r\n";
			$file_contents .=  "<rss version=\"2.0\" xmlns:g=\"http://base.google.com/ns/1.0\">\r\n";
				$file_contents .=  "<channel>\r\n";
					$file_contents .=  "<title>" . __( 'WP EasyCart Data Feed', 'wp-easycart' ) . "</title>\r\n";
					$file_contents .=  "<link>" . site_url( ) . "</link>\r\n";
					$file_contents .=  "<description>" . __( 'My Site Products', 'wp-easycart' ) . "</description>\r\n";
					foreach( $products as $product ){
						if( !get_option( 'ec_option_use_old_linking_style' ) && $product->post_id != "0" ){
							$link = get_permalink( $product->post_id );
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
							if( substr_count( $store_page, '?' ) )						
								$permalink_divider = "&";
							else																
								$permalink_divider = "?";
							$link = $store_page . $permalink_divider . "model_number=" . $product->model_number;
						}
						
						// Get Image Link
						$test_src = ABSPATH . "wp-content/plugins/wp-easycart-data/products/pics1/" . $product->image1;
						$test_src2 = ABSPATH . "wp-content/plugins/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg";
						
						if( substr( $product->image1, 0, 7 ) == 'http://' || substr( $product->image1, 0, 8 ) == 'https://' ){
							$image_link = $product->image1;
						}else if( file_exists( $test_src ) && !is_dir( $test_src ) ){
							$image_link = plugins_url( "/wp-easycart-data/products/pics1/" . $product->image1 );
						}else if( file_exists( $test_src2 ) ){
							$image_link = plugins_url( "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/images/ec_image_not_found.jpg" );
						}else{
							$image_link = plugins_url( "/wp-easycart/design/theme/" . get_option( 'ec_option_latest_theme' ) . "/images/ec_image_not_found.jpg" );
						}
						
						// Get Attributes
						$attributes_result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_product_google_attributes WHERE ec_product_google_attributes.product_id = %d", $product->product_id ) );
						$attributes = json_decode( $attributes_result->attribute_value, true );
						
						// Check the product has required attributes
						$is_valid = true;
						if( $attributes['gtin'] == "" && $attributes['mpn'] == "" ){
							$is_valid = false;
						}
						if( $attributes['condition'] == "" ){
							$is_valid = false;
						}
						if( $is_valid ){
						
					$file_contents .=  "<item>\r\n";
						$file_contents .=  "<g:id>" . htmlspecialchars( $product->model_number ) . "</g:id>\r\n";
						$file_contents .=  "<g:title>" . htmlspecialchars( $product->title ) . "</g:title>\r\n";
						$file_contents .=  "<g:description>" . htmlspecialchars( $product->description ) . "</g:description>\r\n";
						$file_contents .=  "<g:link>" . htmlspecialchars( $link ) . "</g:link>\r\n";
						$file_contents .=  "<g:image_link>" . htmlspecialchars( $image_link ) . "</g:image_link>\r\n";
						if( !$product->show_stock_quantity || $product->stock_quantity > 0 ){
							$file_contents .=  "<g:availability>in stock</g:availability>\r\n";
						}else{
							$file_contents .=  "<g:availability>out of stock</g:availability>\r\n";
						}
						if( $product->list_price > 0 ){
							$file_contents .=  "<g:price currency=\"" . get_option( 'ec_option_base_currency' ). "\">" . number_format( $product->list_price, 2, '.', '' ) . "</g:price>\r\n";
							$file_contents .=  "<g:sale_price currency=\"" . get_option( 'ec_option_base_currency' ). "\">" . number_format( $product->price, 2, '.', '' ) . "</g:sale_price>\r\n";
						}else{
							$file_contents .=  "<g:price currency=\"" . get_option( 'ec_option_base_currency' ). "\">" . number_format( $product->price, 2, '.', '' ) . "</g:price>\r\n";
						}
						$file_contents .=  "<g:brand>" . htmlspecialchars( $product->manufacturer_name ) . "</g:brand>\r\n";
						
						foreach( $attributes as $key => $value ){
							if( $key == "weight_type" ){
								$file_contents .=  "<g:shipping_weight>".$product->weight . " " . $value ."</g:shipping_weight>\r\n";
							}else if( $value != "" && $value != "None Selected" ){
								$file_contents .=  "<g:".$key.">".htmlspecialchars( $value )."</g:".$key.">\r\n";
							}
						}
						
					$file_contents .=  "</item>\r\n";
						}// Close check is valid
					
					}// Close foreach loop
				
				$file_contents .=  "</channel>\r\n";
			$file_contents .=  "</rss>\r\n";
			
			$xml_shortname = "Google_Merchant_Feed_" . date( 'Y_m_d' ) . ".xml";
			$xmlname = WP_PLUGIN_DIR . "/wp-easycart-data/" . $xml_shortname;
			
			file_put_contents( $xmlname, $file_contents );
			
			header( "Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header( "Content-type: text/xml");
			header( 'Content-Disposition: attachment; filename=' . $xml_shortname );
			header( 'Content-Length: ' . ( string )( filesize( $xmlname ) ) );
			header( "Content-Transfer-Encoding: binary" );
			header( 'Expires: 0');
			header( 'Cache-Control: private');
			header( 'Pragma: private');
			
			readfile( $xmlname );
			unlink( $xmlname );
			
			// Stop the page execution so that it doesn't print HTML to the file accidently
			die();
		}
	}
	
	public function add_success_messages( $messages ){
		if( isset( $_GET['success'] ) && $_GET['success'] == 'google-import-complete' ){
			$messages[] = __( 'Google Merchant CSV Successfully Uploaded', 'wp-easycart' );
		}
		return $messages;
	}
	
	public function load_third_party( ){
		include( $this->settings_file );
	}
	
	public function load_google_analytics_design( ){
		include( $this->google_analytics_design_file );
	}
	
	public function load_google_adwords_design( ){
		include( $this->google_adwords_design_file );
	}
	
	public function load_google_merchant( ){
		include( $this->google_merchant_file );
	}
	
	public function load_amazon_settings( ){
		$upgrade_icon = "dashicons-admin-generic";
		$upgrade_title = __( "Amazon S3 Setup", 'wp-easycart' );
		$upgrade_subtitle = "";
		$upgrade_checkbox_label = apply_filters( 'wp_easycart_admin_lock_icon', ' <span class="dashicons dashicons-lock" style="color:#FC0; margin-top:5px;"></span>' ) . __( "Enable Amazon S3 for Download Products", 'wp-easycart' );
		$upgrade_button_label = __( "Save Setup", 'wp-easycart' );
		include( $this->upgrade_file  );
	}
	
	public function load_deconetwork_settings( ){
		$upgrade_icon = "dashicons-admin-generic";
		$upgrade_title = __( "Deconetwork Setup", 'wp-easycart' );
		$upgrade_subtitle = "";
		$upgrade_checkbox_label = apply_filters( 'wp_easycart_admin_lock_icon', ' <span class="dashicons dashicons-lock" style="color:#FC0; margin-top:5px;"></span>' ) . __( "Enable Deconetwork for Customizable Products", 'wp-easycart' );
		$upgrade_button_label = __( "Save Setup", 'wp-easycart' );
		include( $this->upgrade_file  );
	}
	
	public function load_facebook_settings( ){
		$upgrade_icon = "dashicons-admin-generic";
		$upgrade_title = __( "Facebook Pixel Setup", 'wp-easycart' );
		$upgrade_subtitle = "";
		$upgrade_checkbox_label = apply_filters( 'wp_easycart_admin_lock_icon', ' <span class="dashicons dashicons-lock" style="color:#FC0; margin-top:5px;"></span>' ) . __( "Enable Facebook Pixel for Your Cart", 'wp-easycart' );
		$upgrade_button_label = __( "Save Setup", 'wp-easycart' );
		include( $this->upgrade_file  );
	}
	
	public function save_google_analytics( ){
		$ec_option_googleanalyticsid =  'UA-XXXXXXX-X';
		
		if( isset( $_POST['ec_option_googleanalyticsid'] ))
			$ec_option_googleanalyticsid = $_POST['ec_option_googleanalyticsid'] ;
		
		update_option( 'ec_option_googleanalyticsid', $ec_option_googleanalyticsid );

	}
	public function save_google_adwords( ){
		$ec_option_google_adwords_conversion_id =  '';
		$ec_option_google_adwords_language =  'en';
		$ec_option_google_adwords_format =  '3';
		$ec_option_google_adwords_color =  'FFFFFF';
		$ec_option_google_adwords_currency =  'USD';
		$ec_option_google_adwords_remarketing_only =  "false";
		
		if( isset( $_POST['ec_option_google_adwords_conversion_id'] ))
			$ec_option_google_adwords_conversion_id = $_POST['ec_option_google_adwords_conversion_id'] ;
		if( isset( $_POST['ec_option_google_adwords_language'] ))
			$ec_option_google_adwords_language = $_POST['ec_option_google_adwords_language'] ;
		if( isset( $_POST['ec_option_google_adwords_format'] ))
			$ec_option_google_adwords_format = $_POST['ec_option_google_adwords_format'] ;
		if( isset( $_POST['ec_option_google_adwords_color'] ))
			$ec_option_google_adwords_color = $_POST['ec_option_google_adwords_color'] ;
		if( isset( $_POST['ec_option_google_adwords_currency'] ))
			$ec_option_google_adwords_currency = $_POST['ec_option_google_adwords_currency'] ;
		if( isset( $_POST['ec_option_google_adwords_label'] ))
			$ec_option_google_adwords_label = $_POST['ec_option_google_adwords_label'] ;
		if( isset( $_POST['ec_option_google_adwords_remarketing_only'] ))
			$ec_option_google_adwords_remarketing_only = $_POST['ec_option_google_adwords_remarketing_only'] ;
		
		update_option( 'ec_option_google_adwords_conversion_id', $ec_option_google_adwords_conversion_id );
		update_option( 'ec_option_google_adwords_language', $ec_option_google_adwords_language );
		update_option( 'ec_option_google_adwords_format', $ec_option_google_adwords_format );
		update_option( 'ec_option_google_adwords_color', $ec_option_google_adwords_color );
		update_option( 'ec_option_google_adwords_currency', $ec_option_google_adwords_currency );
		update_option( 'ec_option_google_adwords_label', $ec_option_google_adwords_label );
		update_option( 'ec_option_google_adwords_remarketing_only', $ec_option_google_adwords_remarketing_only );
	}
	
}
endif; // End if class_exists check

function wp_easycart_admin_third_party( ){
	return wp_easycart_admin_third_party::instance( );
}
wp_easycart_admin_third_party( );

add_action( 'wp_ajax_ec_admin_ajax_save_google_analytics', 'ec_admin_ajax_save_google_analytics' );
function ec_admin_ajax_save_google_analytics( ){
	wp_easycart_admin_third_party( )->save_google_analytics( );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_google_adwords', 'ec_admin_ajax_save_google_adwords' );
function ec_admin_ajax_save_google_adwords( ){
	wp_easycart_admin_third_party( )->save_google_adwords( );
	die( );
}