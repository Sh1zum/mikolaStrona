<?php 
if( current_user_can( 'manage_options' ) ){
	global $wpdb;
	
	if( !isset( $_GET['product_id'] ) ){
		echo 'Missing Product ID';
		die( );
	}
	
	$product_id = (int) $_GET['product_id'];
	$product = $wpdb->get_row( $wpdb->prepare( "SELECT option_id_1, option_id_2, option_id_3, option_id_4, option_id_5 FROM ec_product WHERE product_id = %d", $product_id ) );
	$optionitem_quantities = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ec_optionitemquantity WHERE product_id = %d", $product_id ) ); 
	
	$option1_items = array( ); $option2_items = array( ); $option3_items = array( ); $option4_items = array( ); $option5_items = array( );
	if( $product->option_id_1 )
		$option1_items = $wpdb->get_results( $wpdb->prepare( "SELECT optionitem_id, optionitem_name FROM ec_optionitem WHERE option_id = %d ORDER BY optionitem_order ASC", $product->option_id_1 ) );
	if( $product->option_id_2 )
		$option2_items = $wpdb->get_results( $wpdb->prepare( "SELECT optionitem_id, optionitem_name FROM ec_optionitem WHERE option_id = %d ORDER BY optionitem_order ASC", $product->option_id_2 ) );
	if( $product->option_id_3 )
		$option3_items = $wpdb->get_results( $wpdb->prepare( "SELECT optionitem_id, optionitem_name FROM ec_optionitem WHERE option_id = %d ORDER BY optionitem_order ASC", $product->option_id_3 ) );
	if( $product->option_id_4 )
		$option4_items = $wpdb->get_results( $wpdb->prepare( "SELECT optionitem_id, optionitem_name FROM ec_optionitem WHERE option_id = %d ORDER BY optionitem_order ASC", $product->option_id_4 ) );
	if( $product->option_id_5 )
		$option5_items = $wpdb->get_results( $wpdb->prepare( "SELECT optionitem_id, optionitem_name FROM ec_optionitem WHERE option_id = %d ORDER BY optionitem_order ASC", $product->option_id_5 ) );
	
	// Establish Quantity Array
	$optionitem_quantity_array = array( );
	for( $i=0; $i<count( $optionitem_quantities ); $i++ ){
		$optionitem_quantity_array[$optionitem_quantities[$i]->optionitem_id_1.$optionitem_quantities[$i]->optionitem_id_2.$optionitem_quantities[$i]->optionitem_id_3.$optionitem_quantities[$i]->optionitem_id_4.$optionitem_quantities[$i]->optionitem_id_5] = $optionitem_quantities[$i]->quantity;
	}
	
	$header = ""; 
	$data = "";
	//$data .= chr(0xEF) . chr(0xBB) . chr(0xBF);
	
	if( count( $option1_items ) > 0 ){
		$data .= "Option Combination,Quantity,Option Item 1 ID,Option Item 2 ID,Option Item 3 ID,Option Item 4 ID,Option Item 5 ID\n";
		for( $a=0; $a < count( $option1_items ); $a++ ){
			if( count( $option2_items ) > 0 ){
				
				// Loop option 2
				for( $b=0; $b < count( $option2_items ); $b++ ){
					if( count( $option3_items ) > 0 ){
						
						// Loop option 3
						for( $c=0; $c < count( $option3_items ); $c++ ){
							if( count( $option4_items ) > 0 ){
							
								// Loop option 4
								for( $d=0; $d < count( $option4_items ); $d++ ){
									if( count( $option5_items ) > 0 ){
										
										// Loop option 5
										for( $e=0; $e < count( $option5_items ); $e++ ){
											$data .= str_replace( '"', '""', stripslashes_deep( preg_replace( "/\n{2,}/", "\n\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $option1_items[$a]->optionitem_name ) ) ) ) ) . "/" . str_replace( '"', '""', stripslashes_deep( preg_replace( "/\n{2,}/", "\n\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $option2_items[$b]->optionitem_name ) ) ) ) ) . "/" . str_replace( '"', '""', stripslashes_deep( preg_replace( "/\n{2,}/", "\n\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $option3_items[$c]->optionitem_name ) ) ) ) ) . "/" . str_replace( '"', '""', stripslashes_deep( preg_replace( "/\n{2,}/", "\n\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $option4_items[$d]->optionitem_name ) ) ) ) ) . "/" . str_replace( '"', '""', stripslashes_deep( preg_replace( "/\n{2,}/", "\n\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $option5_items[$e]->optionitem_name ) ) ) ) ) . ",";
											if( isset( $optionitem_quantity_array[$option1_items[$a]->optionitem_id.$option2_items[$b]->optionitem_id.$option3_items[$c]->optionitem_id.$option4_items[$d]->optionitem_id.$option5_items[$e]->optionitem_id] ) ){
												$data .= $optionitem_quantity_array[$option1_items[$a]->optionitem_id.$option2_items[$b]->optionitem_id.$option3_items[$c]->optionitem_id.$option4_items[$d]->optionitem_id.$option5_items[$e]->optionitem_id];
											}else{
												$data .= 0;
											}
											$data .= "," . $option1_items[$a]->optionitem_id . "," . $option2_items[$b]->optionitem_id . "," . $option3_items[$c]->optionitem_id . "," . $option4_items[$d]->optionitem_id . "," . $option5_items[$e]->optionitem_id . "\n";
										
										}
									
									// Create Row for 4 level of items
									}else{
										$data .= str_replace( '"', '""', stripslashes_deep( preg_replace( "/\n{2,}/", "\n\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $option1_items[$a]->optionitem_name ) ) ) ) ) . "/" . str_replace( '"', '""', stripslashes_deep( preg_replace( "/\n{2,}/", "\n\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $option2_items[$b]->optionitem_name ) ) ) ) ) . "/" . str_replace( '"', '""', stripslashes_deep( preg_replace( "/\n{2,}/", "\n\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $option3_items[$c]->optionitem_name ) ) ) ) ) . "/" . str_replace( '"', '""', stripslashes_deep( preg_replace( "/\n{2,}/", "\n\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $option4_items[$d]->optionitem_name ) ) ) ) ) . ",";
										if( isset( $optionitem_quantity_array[$option1_items[$a]->optionitem_id.$option2_items[$b]->optionitem_id.$option3_items[$c]->optionitem_id.$option4_items[$d]->optionitem_id."0"] ) ){
											$data .= $optionitem_quantity_array[$option1_items[$a]->optionitem_id.$option2_items[$b]->optionitem_id.$option3_items[$c]->optionitem_id.$option4_items[$d]->optionitem_id."0"];
										}else{
											$data .= 0;
										}
										$data .= "," . $option1_items[$a]->optionitem_id . "," . $option2_items[$b]->optionitem_id . "," . $option3_items[$c]->optionitem_id . "," . $option4_items[$d]->optionitem_id . ",0\n";
									
									}// Close if/else 4 level
								}
							
							// Create Row for 3 level of items
							}else{
								$data .= str_replace( '"', '""', stripslashes_deep( preg_replace( "/\n{2,}/", "\n\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $option1_items[$a]->optionitem_name ) ) ) ) ) . "/" . str_replace( '"', '""', stripslashes_deep( preg_replace( "/\n{2,}/", "\n\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $option2_items[$b]->optionitem_name ) ) ) ) ) . "/" . str_replace( '"', '""', stripslashes_deep( preg_replace( "/\n{2,}/", "\n\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $option3_items[$c]->optionitem_name ) ) ) ) ) . ",";
								if( isset( $optionitem_quantity_array[$option1_items[$a]->optionitem_id.$option2_items[$b]->optionitem_id.$option3_items[$c]->optionitem_id."00"] ) ){
									$data .= $optionitem_quantity_array[$option1_items[$a]->optionitem_id.$option2_items[$b]->optionitem_id.$option3_items[$c]->optionitem_id."00"];
								}else{
									$data .= 0;
								}
								$data .= "," . $option1_items[$a]->optionitem_id . "," . $option2_items[$b]->optionitem_id . "," . $option3_items[$c]->optionitem_id . ",0,0\n";
							
							}// Close if/else 3 level
						}
						
					// Create Row for 2 level of items
					}else{
						$data .= str_replace( '"', '""', stripslashes_deep( preg_replace( "/\n{2,}/", "\n\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $option1_items[$a]->optionitem_name ) ) ) ) ) . "/" . str_replace( '"', '""', stripslashes_deep( preg_replace( "/\n{2,}/", "\n\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $option2_items[$b]->optionitem_name ) ) ) ) ) . ",";
						if( isset( $optionitem_quantity_array[$option1_items[$a]->optionitem_id.$option2_items[$b]->optionitem_id."000"] ) ){
							$data .= $optionitem_quantity_array[$option1_items[$a]->optionitem_id.$option2_items[$b]->optionitem_id."000"];
						}else{
							$data .= 0;
						}
						$data .= "," . $option1_items[$a]->optionitem_id . "," . $option2_items[$b]->optionitem_id . ",0,0,0\n";
					
					}// Close if/else 2 level
				}
				
			// Create Row for 1 level of items
			}else{
				
				$data .= str_replace( '"', '""', stripslashes_deep( preg_replace( "/\n{2,}/", "\n\n", str_replace( "\r", "\n", str_replace( "\r\n", "\n", $option1_items[$a]->optionitem_name ) ) ) ) ) . ",";
				if( isset( $optionitem_quantity_array[$option1_items[$a]->optionitem_id."0000"] ) ){
					$data .= $optionitem_quantity_array[$option1_items[$a]->optionitem_id."0000"];
				}else{
					$data .= 0;
				}
				$data .= "," . $option1_items[$a]->optionitem_id . ",0,0,0,0\n";
			
			}// Close if/else 1 level
		
		} // Close Data loop
		
	}else{
		if( $data == "" ){
			$data = "\nno options setup with product\n";
		}
	}
	
	
	header("Content-type: text/csv; charset=UTF-8");
	header("Content-Transfer-Encoding: binary"); 
	header("Content-Disposition: attachment; filename=product-optionitem-quantity-" . date( 'Y-m-d' ). ".csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	
	echo $data;
	
}else{
	echo 'Not Authenticated'; 
	die( );
}

?>