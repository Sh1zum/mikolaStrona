<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_cart_importer' ) ) :

final class wp_easycart_admin_cart_importer{
	
	protected static $_instance = null;
	
	private $wpdb;
	
	public $oscommerce_import_file;
	public $woo_import_file;
	public $square_import_file;
	public $shopify_import_file;
	public $settings_file;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
		
	public function __construct( ){
		// Keep reference to wpdb
		global $wpdb;
		$this->wpdb =& $wpdb;
		
		// Setup File Names 
		$this->oscommerce_import_file	 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/cart-importer/oscommerce-import.php';
		$this->woo_import_file	 			= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/cart-importer/woo-import.php';
		$this->square_import_file	 		= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/cart-importer/square-import.php';
		$this->shopify_import_file	 		= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/cart-importer/shopify-import.php';
		$this->settings_file		 		= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/cart-importer/settings.php';
		
		// Actions
		add_action( 'wpeasycart_admin_cart_importer', array( $this, 'load_woo_importer' ) );
		add_action( 'wpeasycart_admin_cart_importer', array( $this, 'load_oscommerce_importer' ) );
		add_action( 'wpeasycart_admin_cart_importer', array( $this, 'load_square_importer' ) );
		add_action( 'wpeasycart_admin_cart_importer', array( $this, 'load_shopify_importer' ) );
		add_action( 'wp_easycart_process_post_form_action', array( $this, 'process_square_import' ) );
	}
	
	public function load_cart_importer( ){
		include( $this->settings_file );
	}
	
	public function load_woo_importer( ){
		include( $this->woo_import_file );
	}
	public function load_oscommerce_importer( ){
		include( $this->oscommerce_import_file );
	}

	public function load_square_importer( ){
		include( $this->square_import_file );
	}

	public function load_shopify_importer( ){
		include( $this->shopify_import_file );
	}
    
    public function shopify_import_products( $cursor, $curr_count ){
        global $wpdb;
        $mysqli = new ec_db_admin( );

        $url = 'https://' . $_POST['wpeasycart_shopify_api_key'] . ':'  . $_POST['wpeasycart_shopify_api_password'] . '@' . $_POST['wpeasycart_shopify_domain'] . '/admin/api/2021-01/products.json?limit=5';
        
        if( $cursor != '' ){
            $url .= '&page_info=' . $cursor;
        }

        $headr = array();
        $headr[] = 'Content-Type: application/json';

        $ch = curl_init( );
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, false ); 
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
        curl_setopt($ch, CURLOPT_TIMEOUT, (int) 30);
        $response = curl_exec($ch);
        if( $response === false ){
            $mysqli->insert_response( 0, 1, "Shopify CURL ERROR", curl_error( $ch ) );
        }else{
            $header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
            $header = substr( $response, 0, $header_size );
            $header_data = explode( "\n", $header );
            $headers = array( );
            foreach( $header_data as $part ){
                $split = explode( ":", $part, 2 );
                if( isset( $split[1] ) ){
                    $headers[trim( $split[0])] = trim( $split[1] );
                }
            }
            $body = substr( $response, $header_size );
            $mysqli->insert_response( 0, 0, "Shopify Import Products Response", print_r( $response, true ) );
        }

        curl_close ($ch);

        if( $response === false ){
            echo json_encode( array( 'has_errors' => true ) );
            return;
        }

        $response_decode = json_decode( $body );
        
        if( $response_decode && isset( $response_decode->errors ) ){
            echo json_encode( array( 'has_errors' => true ) );
            return;
        }
        
        $last_id = '';
        if( isset( $headers['link'] ) ){
            $pattern = '/page_info=([0-9A-Za-z]*)\>\; rel\=\"next/';
            preg_match( $pattern, $headers['link'], $matches );
            if( count( $matches ) > 1 ){
                $last_id = $matches[1];
            }
        }
        
        if( $response_decode && $response_decode->products && count( $response_decode->products ) ){
            
            $curr_count += count( $response_decode->products );
            
            foreach( $response_decode->products as $product ){

                $title = $product->title;
                $description = $product->body_html;
                $image_url = $this->import_shopify_image( $product->image->src );
                $price = $product->variants[0]->price;
                $list_price = $product->variants[0]->compare_at_price;
                $taxable = $product->variants[0]->taxable;
                $shippable = $product->variants[0]->requires_shipping;
                $weight = $product->variants[0]->weight;
                $weight_unit = $product->variants[0]->weight_unit;
                if( $weight_unit == 'oz' ){
                    $weight = $weight / 16;
                }
                $quantity = $product->variants[0]->inventory_quantity;
                $manufacturer = $product->vendor;
                $category = $product->product_type;
                $model_number = $product->handle;
                $tags = $product->tags;
                $post_status = 'publish';

                // Create Post Slug
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

                /* Fix duplicate model number */
                $model_number_orig = $model_number;
                $i=1;
                while( $model_check = $wpdb->get_row( $wpdb->prepare( "SELECT model_number FROM ec_product WHERE ec_product.model_number = %s", $model_number ) ) ){
                    $model_number = $model_number_orig . '-' . $i;
                    $i++;
                }

                /* Manually Insert Post */
                $wpdb->query( $wpdb->prepare( "INSERT INTO " . $wpdb->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type, post_excerpt, post_date, post_date_gmt, post_modified, post_modified_gmt, comment_status ) VALUES( %s, %s, %s, %s, %s, %s, %s, NOW( ), UTC_TIMESTAMP( ), NOW( ), UTC_TIMESTAMP( ), 'closed' )", "[ec_store modelnumber=\"" . $model_number . "\"]", $post_status, $GLOBALS['language']->convert_text( $title ), $post_slug, $guid, "ec_store", '' ) );
                $post_id = $wpdb->insert_id;

                // Create Manufacturer or Find
                $wpec_manufacturer = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_manufacturer WHERE `name` = %s", $manufacturer ) );
                if( $wpec_manufacturer ){
                    $manufacturer_id = $wpec_manufacturer->manufacturer_id;
                }else{
                    $manufacturer_id = $this->import_insert_manufacturer( $manufacturer );
                }

                // Create Product
                $wpdb->query( $wpdb->prepare( "INSERT INTO ec_product( activate_in_store, show_on_startup, use_advanced_optionset, use_optionitem_quantity_tracking, post_id, title, description, model_number, image1, manufacturer_id, price, list_price, is_taxable, is_shippable, weight, stock_quantity, shopify_id ) VALUES( 1, 1, 0, 1, %d, %s, %s, %s, %s, %d, %s, %s, %d, %d, %s, %d, %d )", $post_id, $title, $description, $model_number, $image_url, $manufacturer_id, $price, $list_price, $taxable, $shippable, $weight, $quantity, $product->id ) );
                $product_id = $wpdb->insert_id;

                // Loop Options into Option Sets
                $option_conversions = array( );
                $options = $product->options; // each has name, values
                $option_i = 0;
                $product_options = array( );
                foreach( $options as $option ){

                    $wpdb->query( $wpdb->prepare( "INSERT INTO ec_option( option_name, option_label, option_type ) VALUES( %s, %s, 'basic-combo' )", $option->name, $option->name ) );
                    $option_id = $wpdb->insert_id;
                    $product_options[] = $option_id;

                    $sql_optionitems = "INSERT INTO ec_optionitem( option_id, optionitem_name, optionitem_order ) VALUES";
                    for( $optionitem_i = 0; $optionitem_i < count( $option->values ); $optionitem_i++ ){
                        if( $optionitem_i > 0 ){
                            $sql_optionitems .= ', ';
                        }
                        $sql_optionitems .= $wpdb->prepare( "( %d, %s, %d )", $option_id, $option->values[$optionitem_i], $optionitem_i );
                    }
                    $wpdb->query( $sql_optionitems );
                    $wpdb->query( $wpdb->prepare( "INSERT INTO ec_option_to_product( option_id, product_id, option_order ) VALUES( %d, %d, %d )", $option_id, $product_id, $option_i ) );
                    $option_i++;

                    $option_conversion = (object) array( 'option_id' => $option_id, $optionitems = array( ) );
                    $optionitems = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ec_optionitem WHERE option_id = %d", $option_id ) );
                    foreach( $optionitems as $optionitem ){
                        $option_conversion->optionitems[$optionitem->optionitem_name] = $optionitem;
                    }
                    $option_conversions[] = $option_conversion;
                }
                $additional_images = $product->images;
                $image2 = $image3 = $image4 = $image5 = '';
                for( $i=1; $i<count( $additional_images ); $i++ ){
                    ${'image'.($i+1)} = $this->import_shopify_image( $additional_images[$i]->src );
                }

                // Process Variants
                $total_quantity = 0;
                foreach( $product->variants as $variant ){
                    $variant_optionitems = array( 0, 0, 0, 0 );
                    for( $i=0; $i<count( $option_conversions ); $i++ ){
                        $variant_optionitems[$i] = ( ( isset( $option_conversions[$i]->optionitems[$variant->{'option'.($i+1)}] ) ) ? $option_conversions[$i]->optionitems[$variant->{'option'.($i+1)}]->optionitem_id : 0 );
                    }
                    $total_quantity += $variant->inventory_quantity;
                    $wpdb->query( $wpdb->prepare( "INSERT INTO ec_optionitemquantity( product_id, optionitem_id_1, optionitem_id_2, optionitem_id_3, optionitem_id_4, optionitem_id_5, quantity ) VALUES( %d, %d, %d, %d, %d, %d, %d )", $product_id, $variant_optionitems[0], $variant_optionitems[1], $variant_optionitems[2], $variant_optionitems[3], $variant_optionitems[4], $variant->inventory_quantity ) );
                }

                // Update the Product
                $wpdb->query( $wpdb->prepare( "UPDATE ec_product SET stock_quantity = %d, image2 = %s, image3 = %s, image4 = %s, image5 = %s, option_id_1 = %d, option_id_2 = %d, option_id_3 = %d, option_id_4 = %d, option_id_5 = %d WHERE product_id = %d", $total_quantity, $image2, $image3, $image4, $image5, ( ( isset( $product_options[0] ) ) ? $product_options[0] : 0 ), ( ( isset( $product_options[1] ) ) ? $product_options[1] : 0 ), ( ( isset( $product_options[2] ) ) ? $product_options[2] : 0 ), ( ( isset( $product_options[3] ) ) ? $product_options[3] : 0 ), ( ( isset( $product_options[4] ) ) ? $product_options[4] : 0 ), $product_id ) );

            }
            
            echo json_encode( array( 'has_more' => ( ( $last_id != '' ) ? true : false ), 'cursor' => $last_id, 'curr_count' => $curr_count ) );
            
        }else{
            echo json_encode( array( 'has_more' => false ) );
        
        }
    }
    
    public function shopify_import_users( $cursor, $curr_count ){
        
        global $wpdb;
        $mysqli = new ec_db_admin( );

        $url = 'https://' . $_POST['wpeasycart_shopify_api_key'] . ':'  . $_POST['wpeasycart_shopify_api_password'] . '@' . $_POST['wpeasycart_shopify_domain'] . '/admin/api/2021-01/customers.json?limit=25';

        if( $cursor != '' ){
            $url .= '&page_info=' . $cursor;
        }
        
        $headr = array();
        $headr[] = 'Content-Type: application/json';

        $ch = curl_init( );
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, false ); 
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
        curl_setopt($ch, CURLOPT_TIMEOUT, (int) 30);
        $response = curl_exec($ch);
        if( $response === false ){
            $mysqli->insert_response( 0, 1, "Shopify CURL ERROR", curl_error( $ch ) );
        }else{
            $header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
            $header = substr( $response, 0, $header_size );
            $header_data = explode( "\n", $header );
            $headers = array( );
            foreach( $header_data as $part ){
                $split = explode( ":", $part, 2 );
                if( isset( $split[1] ) ){
                    $headers[trim( $split[0])] = trim( $split[1] );
                }
            }
            $body = substr( $response, $header_size );
            $mysqli->insert_response( 0, 0, "Shopify Import Customers Response", print_r( $response, true ) );
        }

        curl_close ($ch);

        if( $response === false ){
            echo json_encode( array( 'has_errors' => true ) );
            return;
        }

        $response_decode = json_decode( $body );
        
        if( $response_decode && isset( $response_decode->errors ) ){
            echo json_encode( array( 'has_errors' => true ) );
            return;
        }
        
        $last_id = '';
        if( isset( $headers['link'] ) ){
            $pattern = '/page_info=([0-9A-Za-z]*)\>\; rel\=\"next/';
            preg_match( $pattern, $headers['link'], $matches );
            if( count( $matches ) > 1 ){
                $last_id = $matches[1];
            }
        }
        
        if( $response_decode && $response_decode->customers && count( $response_decode->customers ) ){
            
            $curr_count += count( $response_decode->customers );
            
            foreach( $response_decode->customers as $customer ){
                
                $wpdb->query( $wpdb->prepare( "INSERT INTO ec_user( email, password, first_name, last_name, is_subscriber ) VALUES( %s, %s, %s, %s, %d )", $customer->email, rand( 1000000000, 999999999999 ), $customer->first_name, $customer->last_name, ( isset( $customer->marketing_opt_in_level ) && $customer->marketing_opt_in_level != 'unknown' ) ? 1 : 0 ) );
                $user_id = $wpdb->insert_id;

                if( $user_id ){
                    // Use default address for billing and shipping
                    $address = $customer->default_address;
                    $billing_first_name = $address->first_name;
                    $billing_last_name = $address->last_name;
                    $billing_company_name = $address->company;
                    $billing_address_line_1 = $address->address1;
                    $billing_address_line_2 = $address->address2;
                    $billing_city = $address->city;
                    $billing_state = $address->province_code;
                    $billing_zip = $address->zip;
                    $billing_country = $address->country_code;
                    $billing_phone = $address->phone;
                    $wpdb->query( $wpdb->prepare( "INSERT INTO ec_address( user_id, first_name, last_name, company_name, address_line_1, address_line_2, city, state, zip, country, phone ) VALUES( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )", $user_id, $billing_first_name, $billing_last_name, $billing_company_name, $billing_address_line_1, $billing_address_line_2, $billing_city, $billing_state, $billing_zip, $billing_country, $billing_phone ) );
                    $billing_address_id = $wpdb->insert_id;
                    $wpdb->query( $wpdb->prepare( "INSERT INTO ec_address( user_id, first_name, last_name, company_name, address_line_1, address_line_2, city, state, zip, country, phone ) VALUES( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )", $user_id, $billing_first_name, $billing_last_name, $billing_company_name, $billing_address_line_1, $billing_address_line_2, $billing_city, $billing_state, $billing_zip, $billing_country, $billing_phone ) );
                    $shipping_address_id = $wpdb->insert_id;

                    // Update user address id
                    $wpdb->query( $wpdb->prepare( "UPDATE ec_user SET default_billing_address_id = %d, default_shipping_address_id = %d WHERE user_id = %d", $billing_address_id, $shipping_address_id, $user_id ) );
                }
            }
            
            echo json_encode( array( 'has_more' => ( ( $last_id != '' ) ? true : false ), 'cursor' => $last_id, 'curr_count' => $curr_count ) );
            
        }else{
            echo json_encode( array( 'has_more' => false ) );
        
        }
    }
    
    public function shopify_import_collections( $cursor, $curr_count ){
        
        global $wpdb;
        $mysqli = new ec_db_admin( );

        $url = 'https://' . $_POST['wpeasycart_shopify_api_key'] . ':'  . $_POST['wpeasycart_shopify_api_password'] . '@' . $_POST['wpeasycart_shopify_domain'] . '/admin/api/2021-01/custom_collections.json?limit=25';

        if( $cursor != '' ){
            $url .= '&page_info=' . $cursor;
        }
        
        $headr = array();
        $headr[] = 'Content-Type: application/json';

        $ch = curl_init( );
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, false ); 
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
        curl_setopt($ch, CURLOPT_TIMEOUT, (int) 30);
        $response = curl_exec($ch);
        if( $response === false ){
            $mysqli->insert_response( 0, 1, "Shopify CURL ERROR", curl_error( $ch ) );
        }else{
            $header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
            $header = substr( $response, 0, $header_size );
            $header_data = explode( "\n", $header );
            $headers = array( );
            foreach( $header_data as $part ){
                $split = explode( ":", $part, 2 );
                if( isset( $split[1] ) ){
                    $headers[trim( $split[0])] = trim( $split[1] );
                }
            }
            $body = substr( $response, $header_size );
            $mysqli->insert_response( 0, 0, "Shopify Import Custom Collections Response", print_r( $response, true ) );
        }

        curl_close ($ch);

        if( $response === false ){
            echo json_encode( array( 'has_errors' => true ) );
            return;
        }

        $response_decode = json_decode( $body );
        
        if( $response_decode && isset( $response_decode->errors ) ){
            echo json_encode( array( 'has_errors' => true ) );
            return;
        }
        
        $last_id = '';
        if( isset( $headers['link'] ) ){
            $pattern = '/page_info=([0-9A-Za-z]*)\>\; rel\=\"next/';
            preg_match( $pattern, $headers['link'], $matches );
            if( count( $matches ) > 1 ){
                $last_id = $matches[1];
            }
        }
        
        if( $response_decode && $response_decode->custom_collections && count( $response_decode->custom_collections ) ){
            
            $curr_count += count( $response_decode->custom_collections );
            
            foreach( $response_decode->custom_collections as $custom_collection ){
                
                $image = ( isset( $custom_collection->image ) ) ? $this->import_shopify_image( $custom_collection->image->src ) : '';
                
                $wpdb->query( $wpdb->prepare( "INSERT INTO ec_category( category_name, short_description, image, featured_category ) VALUES( %s, %s, %s, 0 )", $custom_collection->title, $custom_collection->body_html, $image ) );
                
                $category_id = $wpdb->insert_id;
                
                $this->import_shopify_collection_products( $custom_collection->id, $category_id );
                
                
            }
            
            echo json_encode( array( 'has_more' => ( ( $last_id != '' ) ? true : false ), 'cursor' => $last_id, 'curr_count' => $curr_count ) );
            
        }else{
            echo json_encode( array( 'has_more' => false ) );
        
        }
    }
    
    private function import_shopify_collection_products( $collection_id, $category_id, $cursor = false, $curr_count = 0 ){
        global $wpdb;
        $mysqli = new ec_db_admin( );

        $url = 'https://' . $_POST['wpeasycart_shopify_api_key'] . ':'  . $_POST['wpeasycart_shopify_api_password'] . '@' . $_POST['wpeasycart_shopify_domain'] . '/admin/api/2021-01/collections/' . $collection_id . '/products.json?limit=250';

        if( $cursor != '' ){
            $url .= '&page_info=' . $cursor;
        }
        
        $headr = array();
        $headr[] = 'Content-Type: application/json';

        $ch = curl_init( );
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr );
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, false ); 
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT );
        curl_setopt($ch, CURLOPT_TIMEOUT, (int) 30);
        $response = curl_exec($ch);
        if( $response === false ){
            $mysqli->insert_response( 0, 1, "Shopify CURL ERROR", curl_error( $ch ) );
        }else{
            $header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
            $header = substr( $response, 0, $header_size );
            $header_data = explode( "\n", $header );
            $headers = array( );
            foreach( $header_data as $part ){
                $split = explode( ":", $part, 2 );
                if( isset( $split[1] ) ){
                    $headers[trim( $split[0])] = trim( $split[1] );
                }
            }
            $body = substr( $response, $header_size );
            $mysqli->insert_response( 0, 0, "Shopify Import Collection Products Response", print_r( $response, true ) );
        }

        curl_close ($ch);

        if( $response === false ){
            return;
        }

        $response_decode = json_decode( $body );
        
        if( $response_decode && isset( $response_decode->errors ) ){
            return;
        }
        
        $last_id = '';
        if( isset( $headers['link'] ) ){
            $pattern = '/page_info=([0-9A-Za-z]*)\>\; rel\=\"next/';
            preg_match( $pattern, $headers['link'], $matches );
            if( count( $matches ) > 1 ){
                $last_id = $matches[1];
            }
        }
        
        if( $response_decode && $response_decode->products && count( $response_decode->products ) ){
            
            $curr_count += count( $response_decode->products );
            
            $product_list = array( );
            
            foreach( $response_decode->products as $product ){
                $product_list[] = $product->id;
            }
            
            $mysqli->insert_response( 0, 0, "Shopify Import Collection Products Response 2", print_r( $product_list, true ) );
            
            if( count( $product_list ) ){
                $product_ids = $wpdb->get_results( "SELECT product_id FROM ec_product WHERE shopify_id IN ( " . implode( ',', $product_list ) . " )" );
                if( count( $product_ids ) ){
                    $insert_sql = "INSERT INTO ec_categoryitem( category_id, product_id ) VALUES";
                    for( $i=0; $i<count( $product_ids ); $i++ ){
                        $insert_sql .= ( ( $i>0 ) ? ',' : '' ) . '(' . $category_id . ', ' . $product_ids[$i]->product_id . ')';
                    }
                    $wpdb->query( $insert_sql );
                }
            }
            
            if( $last_id != '' ){
                import_shopify_collection_products( $collection_id, $category_id, $last_id, $curr_count );
            }
            
        }
    }
	
	private function import_insert_manufacturer( $name ){
		
		global $wpdb;
		
		$name = stripslashes_deep( $name );
		$post_slug = preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep( strtolower( $name ) ) ) );
		$wpdb->query( $wpdb->prepare( "INSERT INTO ec_manufacturer( `name` ) VALUES( %s )", $name ) );
		$manufacturer_id = $wpdb->insert_id;
		
		// Get URL
		$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
		if( strstr( $store_page, '?' ) )									$guid = $store_page . '&manufacturer=' . $manufacturer_id;
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
		$wpdb->query( $wpdb->prepare( "INSERT INTO " . $wpdb->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type, post_excerpt, comment_status ) VALUES( %s, %s, %s, %s, %s, %s, %s, 'closed' )", "[ec_store manufacturerid=\"" . $manufacturer_id . "\"]", "publish", $GLOBALS['language']->convert_text( $name ), $post_slug, $guid, "ec_store", '' ) );
		$post_id = $wpdb->insert_id;
		$wpdb->query( $wpdb->prepare( "UPDATE ec_manufacturer SET post_id = %d WHERE manufacturer_id = %d", $post_id, $manufacturer_id ) );
		
		return $manufacturer_id;
	}
	
	private function import_shopify_image( $file ){
		$filename = strtok( basename( $file ), '?' );
		$file_exp = explode( '.', $filename );
		$filename = $file_exp[0] . '-' . time( ) . '.' . $file_exp[1];
		
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_URL, $file );
		curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		$result = curl_exec( $ch );
		curl_close($ch);
		if( $result === false ){
			return '';
		}
		// Now upload the file to WordPress library
		$upload_file = wp_upload_bits( $filename, null, $result );
		$attachment_data = false;
		if( !$upload_file['error'] ){
			$wp_filetype = wp_check_filetype( $filename, null );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_parent' => 0,
				'post_title' => preg_replace( '/\.[^.]+$/', '', $filename ),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], 0 );
			return wp_get_attachment_url( $attachment_id );
		}
		return '';
	}
	
	public function process_square_import( ){
		if( $_POST['ec_admin_form_action'] == 'import-square-products' ){
			$square = new ec_square( );
			$response = $square->get_catalog( );
			
			while( $response ){
				
				foreach( $response->objects as $object ){
					if( $object->type == "CATEGORY" ){
						$square->insert_category( $object );
						
					}else if( $object->type == "ITEM" ){
						$square->insert_product( $object );
						
					}
				}
				if( $response->cursor ){
					$response = $square->get_catalog( $response->cursor );
				}else{
					$response = false;
				}
			}
			
			header( "location:admin.php?page=wp-easycart-settings&subpage=cart-importer&ec_success=square-imported" );
			die( );
		}
	}
    
    public function square_import_modifiers( $cursor, $curr_count ){ // These are our option items
        $square = new ec_square( );
        $response = $square->get_modifiers( $cursor );
        
        foreach( $response->objects as $object ){
            $square->insert_option_item( $object, (bool) $_POST['sync_products'] );
            $curr_count++;
        }
        if( $response->cursor ){
            echo json_encode( array(
                'has_more'      => true,
                'cursor'        => $response->cursor,
                'curr_count'    => $curr_count
            ) );
        }else{
            echo json_encode( array(
                'has_more'      => false,
                'curr_count'    => $curr_count
            ) );
        }
    }
    
    public function square_import_modifier_items( $cursor, $curr_count ){ // Note, these are our options
        $square = new ec_square( );
        $response = $square->get_modifier_items( $cursor );
        
        foreach( $response->objects as $object ){
             $square->insert_option( $object, (bool) $_POST['sync_products'] );
             $curr_count++;
        }
        if( $response->cursor ){
            echo json_encode( array(
                'has_more'      => true,
                'cursor'        => $response->cursor,
                'curr_count'    => $curr_count
            ) );
        }else{
            echo json_encode( array(
                'has_more'      => false,
                'curr_count'    => $curr_count
            ) );
        }
    }
    
    public function square_import( $cursor, $curr_count ){
        $square = new ec_square( );
        $response = $square->get_catalog( $cursor );
        
        foreach( $response->objects as $object ){
            if( $object->type == "CATEGORY" ){
                $square->insert_category( $object, (bool) $_POST['sync_products'] );

            }else if( $object->type == "ITEM" ){
                $square->insert_product( $object, (bool) $_POST['sync_products'] );
                $curr_count++;

            }
        }
        if( $response->cursor ){
            echo json_encode( array(
                'has_more'      => true,
                'cursor'        => $response->cursor,
                'curr_count'    => $curr_count
            ) );
        }else{
            echo json_encode( array(
                'has_more'      => false,
                'curr_count'    => $curr_count
            ) );
        }
    }
	
}
endif; // End if class_exists check

function wp_easycart_admin_cart_importer( ){
	return wp_easycart_admin_cart_importer::instance( );
}
wp_easycart_admin_cart_importer( );

add_action( 'wp_ajax_ec_admin_ajax_save_woo_importer', 'ec_admin_ajax_save_woo_importer' );
function ec_admin_ajax_save_woo_importer( ){
	wp_easycart_admin_cart_importer( )->save_woo_importer_settings( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_save_oscommerce_importer', 'ec_admin_ajax_save_oscommerce_importer' );
function ec_admin_ajax_save_oscommerce_importer( ){
	wp_easycart_admin_cart_importer( )->save_oscommerce_importer_settings( );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_square_modifier_import', 'ec_admin_ajax_square_modifier_import' );
function ec_admin_ajax_square_modifier_import( ){
	wp_easycart_admin_cart_importer( )->square_import_modifiers( $_POST['cursor'], $_POST['curr_count'] );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_square_modifier_items_import', 'ec_admin_ajax_square_modifier_items_import' );
function ec_admin_ajax_square_modifier_items_import( ){
	wp_easycart_admin_cart_importer( )->square_import_modifier_items( $_POST['cursor'], $_POST['curr_count'] );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_square_import', 'ec_admin_ajax_square_import' );
function ec_admin_ajax_square_import( ){
	wp_easycart_admin_cart_importer( )->square_import( $_POST['cursor'], $_POST['curr_count'] );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_shopify_import_products', 'ec_admin_ajax_shopify_import_products' );
function ec_admin_ajax_shopify_import_products( ){
	wp_easycart_admin_cart_importer( )->shopify_import_products( $_POST['cursor'], $_POST['curr_count'] );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_shopify_import_users', 'ec_admin_ajax_shopify_import_users' );
function ec_admin_ajax_shopify_import_users( ){
	wp_easycart_admin_cart_importer( )->shopify_import_users( $_POST['cursor'], $_POST['curr_count'] );
	die( );
}
add_action( 'wp_ajax_ec_admin_ajax_shopify_import_categories', 'ec_admin_ajax_shopify_import_categories' );
function ec_admin_ajax_shopify_import_categories( ){
	wp_easycart_admin_cart_importer( )->shopify_import_collections( $_POST['cursor'], $_POST['curr_count'] );
	die( );
}