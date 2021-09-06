<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_inventory' ) ) :

final class wp_easycart_admin_inventory{
	
	protected static $_instance = null;
	
	public $inventory_list_file;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
	
	public function __construct( ){ 
		$this->inventory_list_file 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/inventory/inventory-list.php';
		add_action( 'wp_easycart_process_get_form_action', array( $this, 'process_export_inventory' ) );
	}
	
	public function process_export_inventory( ){
		if( isset( $_GET['subpage'] ) && $_GET['subpage'] == 'inventory' && $_GET['ec_admin_form_action'] == 'export-inventory-list' ){
			$this->export_inventory_list( );
		}
	}
	
	public function load_inventory_list( ){
		include( $this->inventory_list_file );
	}
	
	public function export_inventory_list( ){
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=data.csv');
		
		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');
		
		// output the column headings
		fputcsv( $output, array( __( 'Title (Options)', 'wp-easycart' ), __( 'Quantity', 'wp-easycart' ) ) );
		
		global $wpdb;
		$products = $wpdb->get_results( "SELECT ec_product.product_id, ec_product.title, ec_product.model_number, ec_product.stock_quantity, ec_product.use_optionitem_quantity_tracking, ec_product.show_stock_quantity, ec_product.option_id_1, ec_product.option_id_2, ec_product.option_id_3, ec_product.option_id_4, ec_product.option_id_5 FROM ec_product WHERE ec_product.activate_in_store = 1 ORDER BY ec_product.title ASC" );
		
		$inventory_cvs = "";
		
		foreach( $products as $product ){
			
			if( $product->use_optionitem_quantity_tracking ){ 
				/* START THE CREATION OF A COMPLEX QUERY. THIS COMBINES MULTIPLE OPTIONS TO ALLOW A USER TO ENTER A QUANTITY FOR EACH */
				$sql = "";
				if( $product->option_id_1 != 0 ){
					$sql .= $wpdb->prepare( "SELECT * FROM ( SELECT optionitem_name AS optname1, optionitem_id as optid1 FROM ec_optionitem WHERE option_id = %d ) as optionitems1 ", $product->option_id_1 );
				}
				
				if($product->option_id_2 != 0){
					$sql .= $wpdb->prepare(" JOIN ( SELECT optionitem_name AS optname2, optionitem_id as optid2 FROM ec_optionitem WHERE option_id = %d ) as optionitems2 ON (1=1) ", $product->option_id_2 );
				}
				
				if($product->option_id_3 != 0){
					$sql .= $wpdb->prepare(" JOIN ( SELECT optionitem_name AS optname3, optionitem_id as optid3 FROM ec_optionitem WHERE option_id = %d ) as optionitems3 ON (1=1) ", $product->option_id_3 );
				}
				
				if($product->option_id_4 != 0){
					$sql .= $wpdb->prepare(" JOIN ( SELECT optionitem_name AS optname4, optionitem_id as optid4 FROM ec_optionitem WHERE option_id = %d ) as optionitems4 ON (1=1) ", $product->option_id_4 );
				}
				
				if($product->option_id_5 != 0){
					$sql .= $wpdb->prepare(" JOIN ( SELECT optionitem_name AS optname5, optionitem_id as optid5 FROM ec_optionitem WHERE option_id = %s ) as optionitems5 ON (1=1) ", $product->option_id_5 );
				}
				
				$sql .= " LEFT JOIN ec_optionitemquantity ON ( 1=1 ";
				
				if($product->option_id_1 != 0){
					$sql .= " AND ec_optionitemquantity.optionitem_id_1 = optid1";
				}
				
				if($product->option_id_2 != 0){
					$sql .= " AND ec_optionitemquantity.optionitem_id_2 = optid2";
				}
				
				if($product->option_id_3 != 0){
					$sql .= " AND ec_optionitemquantity.optionitem_id_3 = optid3";
				}
				
				if($product->option_id_4 != 0){
					$sql .= " AND ec_optionitemquantity.optionitem_id_4 = optid4";
				}
				
				if($product->option_id_5 != 0){
					$sql .= " AND ec_optionitemquantity.optionitem_id_5 = optid5";
				}
				
				$sql .= $wpdb->prepare( " AND ec_optionitemquantity.product_id = %d )", $product->product_id );
				
				$sql .= " ORDER BY optname1";
		
				//Finally, get the query results
				$optionitems = $wpdb->get_results( $sql );
				foreach( $optionitems as $optionitem ){ 
				
					$opt_title = $product->title . " (";
					if( $optionitem->optionitem_id_1 != 0 ){
						$opt_title .= $optionitem->optname1;
					}
					if( $optionitem->optionitem_id_2 != 0 ){
						$opt_title .= ", " . $optionitem->optname2;
					}
					if( $optionitem->optionitem_id_3 != 0 ){
						$opt_title .= ", " . $optionitem->optname3;
					}
					if( $optionitem->optionitem_id_4 != 0 ){
						$opt_title .= ", " . $optionitem->optname4;
					}
					if( $optionitem->optionitem_id_5 != 0 ){
						$opt_title .= ", " . $optionitem->optname5;
					}
					
					$opt_title .= ")";
					
					fputcsv( $output, array( $opt_title, $optionitem->quantity ) ); 
				
				} // Close optionitem quantity tracking loop
				
			}else if( $product->show_stock_quantity ){
					
				fputcsv( $output, array( $product->title, $product->stock_quantity ) );
				
            }else{
				
				fputcsv( $output, array( $product->title, '' ) );
				
			}
        
		} // Close foreach
		
		die( );
	}
}
endif; // End if class_exists check

function wp_easycart_admin_inventory( ){
	return wp_easycart_admin_inventory::instance( );
}
wp_easycart_admin_inventory( );