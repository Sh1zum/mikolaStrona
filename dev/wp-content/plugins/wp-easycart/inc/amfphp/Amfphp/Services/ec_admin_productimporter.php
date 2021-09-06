<?php
/*
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//All Code and Design is copyrighted by Level Four Development, llc
//
//Level Four Development, LLC provides this code "as is" without warranty of any kind, either express or implied,     
//including but not limited to the implied warranties of merchantability and/or fitness for a particular purpose.         
//
//Only licnesed users may use this code and storfront for live purposes. All other use is prohibited and may be 
//subject to copyright violation laws. If you have any questions regarding proper use of this code, please
//contact Level Four Development, llc and EasyCart prior to use.
//
//All use of this storefront is subject to our terms of agreement found on Level Four Development, llc's  website.
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/
require_once( "Encoding.php" );
use \ForceUTF8\Encoding;  // It's namespaced now.

class ec_admin_productimporter{
	
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
	private $headers;
	private $limit;
	
	function __construct( ){
		
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
		$this->limit = 20;
		
		ini_set("auto_detect_line_endings", "1");

	}	
		
	public function _getMethodRoles($methodName){
	   if ($methodName == 'productimport') return array('admin');
	   else return null;
	}
	
	public function productimport( ){
		
		set_time_limit( 500 );
		
		if( !file_exists( "../administration/productimportfile.csv" ) ){
			return "Import file not found on server or did not upload successfully.";
		}
		
		$file = fopen( "../administration/productimportfile.csv", "r" );
		
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
			
			}else if( !in_array( $this->headers[$i], $valid_headers ) ){ // error, invalid column
				return "You have an invalid column header at column " . $i . " (value " . $this->headers[$i] . "), please remove or correct the label of that column to continue.";
				
			}
			
		}
		
		if( $this->product_id_index == -1 ){
			return "Missing `product_id` Key field! Values for additions should be 0, updates should be the exported product_id value.";
		}
		
		if( $this->product_id_index == -1 ){
			return "Missing `post_id` Key field! Values for additions should be 0, updates should be the exported post_id value.";
		}
		
		if( $this->model_number_index == -1 ){
			return "Missing `model_number` Key field! Values must be unique from other imported products and those products already in your store.";
		}
		
		if( $this->activate_in_store_index == -1 ){
			return "Missing `activate_in_storck` Key field! Value of 0 or 1 is required.";
		}
		
		if( $this->title_index == -1 ){
			return "Missing `title` Key field! No value is required, but you must have the key field present.";
		}
		
		/* SETUP basic SQL calls */
		$insert_sql = "INSERT INTO ec_product(";
		$update_sql = "UPDATE ec_product SET ";
		
		$first = true;
		
		for( $i=0; $i<count( $this->headers ); $i++ ){
			
			if( $i != $this->product_id_index && $i != $this->post_id_index ){ // Skip rows with product id and post id
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
			if( $i != $this->product_id_index && $i != $this->post_id_index ){ // Skip rows with product id and post id
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
						
						$this->error_list .= "Product " . $product_id . " on line " . ( ( $current_iteration * $this->limit ) + ($i+1) ) . " failed to update, invalid product_id (if you are trying to add a new product use 0 for the product_id)\r";
						
					}else{ // Valid ID, lets update
						
						$existing_model_numbers[] = $model_number;
						
						$update_vals = array( );
						for( $j=0; $j<count( $rows[$i] ); $j++ ){
							if( $j != $this->product_id_index && $j != $this->post_id_index ){
								$rows[$i][$j] = html_entity_decode( preg_replace( "/U\+([0-9A-F]{4})/", "&#x\\1;", $rows[$i][$j] ), ENT_NOQUOTES, 'UTF-8' );
								if( $j == $this->price_index || $j == $this->list_price_index ){
									$update_vals[] = Encoding::fixUTF8( str_replace( ',', '', $rows[$i][$j] ) );
								}else if( $j == $this->model_number_index ){
									$chars = "!@#$%^&*()+={}[]|\'\";:,<.>/?`~*";
									$pattern = "/[".preg_quote($chars, "/")."]/";
									$update_vals[] = Encoding::fixUTF8( preg_replace( $pattern, "", $rows[$i][$j] ) );
								}else{
									$update_vals[] = Encoding::fixUTF8( $rows[$i][$j] );
								}
							}
						}
						$update_vals[] = $product_id; // Add product id last for the update
						$this->db->query( $this->db->prepare( $update_sql, $update_vals ) );
					
						// Update the WordPress Post
						if( $rows[$i][$this->activate_in_store_index] )
							$status = "publish";
						else
							$status = "private";
						$post = array(	'ID'			=> $post_id,
										'post_content'	=> "[ec_store modelnumber=\"" . $rows[$i][$this->model_number_index] . "\"]",
										'post_status'	=> $status,
										'post_title'	=> $GLOBALS['language']->convert_text( $rows[$i][$this->title_index] ),
										'post_type'		=> "ec_store",
										'post_name'		=> str_replace(' ', '-', $GLOBALS['language']->convert_text( $rows[$i][$this->title_index] ) ),
									  );
						wp_update_post( $post );
						
						// Update GUID
						global $wpdb;
						$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "posts SET " . $wpdb->prefix . "posts.guid = %s WHERE " . $wpdb->prefix . "posts.ID = %d", get_permalink( $post_id ), $post_id ) );
		
			
					}// Check for valid product_id
					
				}else{
					
					if( in_array( $model_number, $existing_model_numbers ) ){
						
						$this->error_list .= "Product on line " . ( ( $current_iteration * $this->limit ) + ($i+1) ) . " failed to update, duplicate model number listed for this product.\r";
						
					}else{ // model number is new, we can insert
						
						$existing_model_numbers[] = $model_number;
						$insert_vals = array( );
						for( $j=0; $j<count( $rows[$i] ); $j++ ){
							if( $j != $this->product_id_index && $j != $this->post_id_index ){
								$rows[$i][$j] = html_entity_decode( preg_replace( "/U\+([0-9A-F]{4})/", "&#x\\1;", $rows[$i][$j] ), ENT_NOQUOTES, 'UTF-8' );
								if( $j == $this->price_index || $j == $this->list_price_index ){
									$insert_vals[] = Encoding::fixUTF8( str_replace( ',', '', $rows[$i][$j] ) );
								}else if( $j == $this->model_number_index ){
									$chars = "!@#$%^&*()+={}[]|\'\";:,<.>/?`~*";
									$pattern = "/[".preg_quote($chars, "/")."]/";
									$insert_vals[] = Encoding::fixUTF8( preg_replace( $pattern, "", $rows[$i][$j] ) );
								}else{
									$insert_vals[] = Encoding::fixUTF8( $rows[$i][$j] );
								}
							}
						}
						
						// Insert WordPress Post
						if( $rows[$i][$this->activate_in_store_index] )
							$status = "publish";
						else
							$status = "private";
						
						$post = array(	'post_content'	=> "[ec_store modelnumber=\"" . $rows[$i][$this->model_number_index] . "\"]",
										'post_status'	=> $status,
										'post_title'	=> $GLOBALS['language']->convert_text( $rows[$i][$this->title_index] ),
										'post_type'		=> "ec_store"
									  );
						$post_id = wp_insert_post( $post );
						
						$insert_vals[] = $post_id;
						
						$this->db->query( $this->db->prepare( $insert_sql, $insert_vals ) );
						$product_id = $this->db->insert_id;
						
						if( !$product_id ){ // never inserted
							
							wp_delete_post( $post_id, true );
							$this->error_list .= "Product on line " . ( ( $current_iteration * $this->limit ) + ($i+1) ) . " never inserted\r";
							
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
						
					}// model number duplicate check
					
				}// close check for insert or update
				
			} // Close iteration for loop
			
			unset( $rows );
			
			$current_iteration++;
			
		}
		
		unset( $this->headers );
		
		fclose( $file );
		
		if( $this->error_list == "" ){
			return array( "success" );
		
		}else{
			return $this->error_list;
		
		}
		
	} // Close Import Function
	
}
?>