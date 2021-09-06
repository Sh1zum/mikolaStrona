<?php
/*
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//All Code and Design is copyrighted by Level Four Development, LLC
//
//Level Four Development, LLC provides this code "as is" without warranty of any kind, either express or implied,     
//including but not limited to the implied warranties of merchantability and/or fitness for a particular purpose.         
//
//Only licnesed users may use this code and storfront for live purposes. All other use is prohibited and may be 
//subject to copyright violation laws. If you have any questions regarding proper use of this code, please
//contact Level Four Development, llc and EasyCart prior to use.
//
//All use of this storefront is subject to our terms of agreement found on Level Four Development, LLC's  website.
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/

class ec_admin_orders{		
		
	private $db;
	
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;
		
	}//ec_admin_orders
	
	public function _getMethodRoles( $methodName ){
	   if( $methodName == 'updateorderinfo' ) 						return array( 'admin' );
	   else if( $methodName == 'updateorderdownloadinfo' ) 			return array( 'admin' );
	   else if( $methodName == 'updateordergiftcardinfo' ) 			return array( 'admin' );
	   else if( $methodName == 'updateorderoptioninfo' ) 			return array( 'admin' );
	   else if( $methodName == 'deleteorderitem' ) 					return array( 'admin' );
	   else if( $methodName == 'updateorderiteminfo' ) 				return array( 'admin' );		
	   else if( $methodName == 'updateorderaddresses' ) 			return array( 'admin' );	
	   else if( $methodName == 'getorders' ) 						return array( 'admin' );
	   else if( $methodName == 'getorderdetailsadvancedoptions' ) 	return array( 'admin' );
	   else if( $methodName == 'getorderdetails' ) 					return array( 'admin' );
	   else if( $methodName == 'getorderstatus' ) 					return array( 'admin' );
	   else if( $methodName == 'updateorderstatus' ) 				return array( 'admin' );
	   else if( $methodName == 'updateorderviewed' ) 				return array( 'admin' );
	   else if( $methodName == 'deleteorder' ) 						return array( 'admin' );
	   else if( $methodName == 'updateshippingstatus' ) 			return array( 'admin' );
	   else if( $methodName == 'updatefraktjaktshipping' ) 			return array( 'admin' );
	   else if( $methodName == 'refundorder' ) 						return array( 'admin' );
	   else if( $methodName == 'resendgiftcardemail' ) 				return array( 'admin' );
	   else  														return null;
	   
	}//_getMethodRoles
	
	function updateorderdownloadinfo($orderid, $downloadkey, $orderinfo) {
		//convert object to array
		$orderinfo = (array)$orderinfo;
		
		//get previous order notes
		$sql = "SELECT ec_order.order_notes FROM ec_order WHERE ec_order.order_id = %d";
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
		
		$old_order_notes = (string)$results[0]->order_notes;
		$new_order_notes = $old_order_notes .  PHP_EOL .  PHP_EOL . "***** Download Info Updated:  " . date("M d, Y") . " *****". PHP_EOL;
			  
		//first, edit the data
		$editedsql = "UPDATE ec_download SET ec_download.download_count = %s, ec_download.download_file_name = %s WHERE ec_download.download_id = %d";
		
		$this->db->query( $this->db->prepare( $editedsql, $orderinfo['download_count'], $orderinfo['download_file_name'], $downloadkey ) ); 
		
		//second, update notes status
		$editedsql = "UPDATE ec_order SET ec_order.order_notes = %s WHERE ec_order.order_id = %d";
		$this->db->query( $this->db->prepare( $editedsql, $new_order_notes, $orderid ) ); 
		

		//then, just get the new order information and return	
		$sql = "SELECT 
				ec_orderdetail.*, 
				ec_order.*, 
				( ec_orderdetail.use_advanced_optionset OR ec_orderdetail.is_deconetwork ) AS use_advanced_optionset,
				ec_orderstatus.order_status, 
				ec_orderstatus.is_approved,
				ec_orderdetail.giftcard_id as product_giftcard_id, 
				UNIX_TIMESTAMP( DATE_ADD( ec_order.order_date, INTERVAL 0 HOUR ) ) AS order_date, 
				ec_download.date_created, 
				ec_download.download_count 
				FROM 
				(((ec_order LEFT JOIN ec_orderdetail ON ec_orderdetail.order_id = ec_order.order_id) 
				LEFT JOIN ec_download ON ec_orderdetail.download_key = ec_download.download_id) 
				LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id) 
				WHERE ec_order.order_id = %d 
				ORDER BY ec_orderdetail.product_id";
		  
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
		
		if( count( $results ) > 0 ){
			 return($results);
		}else{
			return array( "error" );
		}
	} 
	
	function updateordergiftcardinfo($orderid, $orderdetail_id, $orderinfo) {
		//convert object to array
		$orderinfo = (array)$orderinfo;
		
		//get previous order notes
		$sql = "SELECT ec_order.order_notes FROM ec_order WHERE ec_order.order_id = %d";
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
		
		$old_order_notes = (string)$results[0]->order_notes;
		$new_order_notes = $old_order_notes .  PHP_EOL .  PHP_EOL . "***** Order Gift Card Info Updated:  " . date("M d, Y") . " *****". PHP_EOL;
			  
		//first, edit the data
		$editedsql = "UPDATE ec_orderdetail SET ec_orderdetail.gift_card_to_name = %s, ec_orderdetail.gift_card_from_name = %s, ec_orderdetail.gift_card_message = %s WHERE ec_orderdetail.orderdetail_id = %d";
		
		$this->db->query( $this->db->prepare( $editedsql, $orderinfo['gift_card_to_name'], $orderinfo['gift_card_from_name'], $orderinfo['gift_card_message'], $orderdetail_id ) ); 
		
		//second, update notes status
		$editedsql = "UPDATE ec_order SET ec_order.order_notes = %s WHERE ec_order.order_id = %d";
		$this->db->query( $this->db->prepare( $editedsql, $new_order_notes, $orderid ) ); 
		

		//then, just get the new order information and return	
		$sql = "SELECT 
				ec_orderdetail.*, 
				ec_order.*, 
				( ec_orderdetail.use_advanced_optionset OR ec_orderdetail.is_deconetwork ) AS use_advanced_optionset,
				ec_orderstatus.order_status, 
				ec_orderstatus.is_approved,
				ec_orderdetail.giftcard_id as product_giftcard_id, 
				UNIX_TIMESTAMP( DATE_ADD( ec_order.order_date, INTERVAL 0 HOUR ) ) AS order_date, 
				ec_download.date_created, 
				ec_download.download_count 
				FROM 
				(((ec_order LEFT JOIN ec_orderdetail ON ec_orderdetail.order_id = ec_order.order_id) 
				LEFT JOIN ec_download ON ec_orderdetail.download_key = ec_download.download_id) 
				LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id) 
				WHERE ec_order.order_id = %d 
				ORDER BY ec_orderdetail.product_id";
		  
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
		
		if( count( $results ) > 0 ){
			 return($results);
		}else{
			return array( "error" );
		}
	} 
	
	function updateorderoptioninfo($orderid, $orderdetail_id, $orderinfo) {
		//convert object to array
		$orderinfo = (array)$orderinfo;
		
		//get previous order notes
		$sql = "SELECT ec_order.order_notes FROM ec_order WHERE ec_order.order_id = %d";
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
		
		$old_order_notes = (string)$results[0]->order_notes;
		$new_order_notes = $old_order_notes .  PHP_EOL .  PHP_EOL . "***** Order Item Options Updated:  " . date("M d, Y") . " *****". PHP_EOL;
			  
		//first, edit the data
		$editedsql = "UPDATE ec_orderdetail SET ec_orderdetail.optionitem_name_1 = %s, ec_orderdetail.optionitem_name_2 = %s, ec_orderdetail.optionitem_name_3 = %s, ec_orderdetail.optionitem_name_4 = %s, ec_orderdetail.optionitem_name_5 = %s WHERE ec_orderdetail.orderdetail_id = %d";
		
		$this->db->query( $this->db->prepare( $editedsql, $orderinfo['optionitem_name_1'], $orderinfo['optionitem_name_2'], $orderinfo['optionitem_name_3'], $orderinfo['optionitem_name_4'], $orderinfo['optionitem_name_5'], $orderdetail_id ) ); 
		
		//second, update notes status
		$editedsql = "UPDATE ec_order SET ec_order.order_notes = %s WHERE ec_order.order_id = %d";
		$this->db->query( $this->db->prepare( $editedsql, $new_order_notes, $orderid ) ); 
		

		//then, just get the new order information and return
		$sql = "SELECT 
				ec_orderdetail.*, 
				ec_order.*, 
				( ec_orderdetail.use_advanced_optionset OR ec_orderdetail.is_deconetwork ) AS use_advanced_optionset,
				ec_orderstatus.order_status, 
				ec_orderstatus.is_approved,
				ec_orderdetail.giftcard_id as product_giftcard_id, 
				UNIX_TIMESTAMP( DATE_ADD( ec_order.order_date, INTERVAL 0 HOUR ) ) AS order_date, 
				ec_download.date_created, 
				ec_download.download_count 
				FROM 
				(((ec_order LEFT JOIN ec_orderdetail ON ec_orderdetail.order_id = ec_order.order_id) 
				LEFT JOIN ec_download ON ec_orderdetail.download_key = ec_download.download_id) 
				LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id) 
				WHERE ec_order.order_id = %d 
				ORDER BY ec_orderdetail.product_id";
		  
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
		
		if( count( $results ) > 0 ){
			 return($results);
		}else{
			return array( "error" );
		}
	} 

	function deleteorderitem($orderid, $orderdetail_id) {

		$sql = "DELETE FROM ec_orderdetail WHERE ec_orderdetail.orderdetail_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $orderdetail_id ) );
		
		//get previous order notes
		$sql = "SELECT ec_order.order_notes FROM ec_order WHERE ec_order.order_id = %d";
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
		
		$old_order_notes = (string)$results[0]->order_notes;
		$new_order_notes = $old_order_notes .  PHP_EOL .  PHP_EOL . "***** Deleted Item:  " . date("M d, Y") . " *****". PHP_EOL;
		
		//second, update notes status
		$editedsql = "UPDATE ec_order SET ec_order.order_notes = %s WHERE ec_order.order_id = %d";
		$this->db->query( $this->db->prepare( $editedsql, $new_order_notes, $orderid ) ); 
		

		//then, just get the new order information and return
		$sql = "SELECT 
				ec_orderdetail.*, 
				ec_order.*, 
				( ec_orderdetail.use_advanced_optionset OR ec_orderdetail.is_deconetwork ) AS use_advanced_optionset,
				ec_orderstatus.order_status, 
				ec_orderstatus.is_approved,
				ec_orderdetail.giftcard_id as product_giftcard_id, 
				UNIX_TIMESTAMP( DATE_ADD( ec_order.order_date, INTERVAL 0 HOUR ) ) AS order_date, 
				ec_download.date_created, 
				ec_download.download_count 
				FROM 
				(((ec_order LEFT JOIN ec_orderdetail ON ec_orderdetail.order_id = ec_order.order_id) 
				LEFT JOIN ec_download ON ec_orderdetail.download_key = ec_download.download_id) 
				LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id) 
				WHERE ec_order.order_id = %d 
				ORDER BY ec_orderdetail.product_id";
		  
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
		
		if( count( $results ) > 0 ){
			 return($results);
		}else{
			return array( "error" );
		}
	}
	
	
	function updateorderiteminfo($orderid, $orderdetail_id, $orderinfo) {
		//convert object to array
		$orderinfo = (array)$orderinfo;
		
		//get previous order notes
		$sql = "SELECT ec_order.order_notes FROM ec_order WHERE ec_order.order_id = %d";
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
		
		$old_order_notes = (string)$results[0]->order_notes;
		$new_order_notes = $old_order_notes .  PHP_EOL .  PHP_EOL . "***** Order Item Updated:  " . date("M d, Y") . " *****". PHP_EOL;
			  
		//first, edit the data
		$editedsql = "UPDATE ec_orderdetail SET ec_orderdetail.title = %s, ec_orderdetail.model_number = %s, ec_orderdetail.unit_price = %s, ec_orderdetail.total_price = %s, ec_orderdetail.quantity = %s WHERE ec_orderdetail.orderdetail_id = %d";
		
		$this->db->query( $this->db->prepare( $editedsql, $orderinfo['title'], $orderinfo['model_number'], $orderinfo['unit_price'], $orderinfo['total_price'], $orderinfo['quantity'], $orderdetail_id ) ); 
		
		//second, update notes status
		$editedsql = "UPDATE ec_order SET ec_order.order_notes = %s WHERE ec_order.order_id = %d";
		$this->db->query( $this->db->prepare( $editedsql, $new_order_notes, $orderid ) ); 
		

		//then, just get the new order information and return
		$sql = "SELECT 
				ec_orderdetail.*, 
				ec_order.*, 
				( ec_orderdetail.use_advanced_optionset OR ec_orderdetail.is_deconetwork ) AS use_advanced_optionset,
				ec_orderstatus.order_status, 
				ec_orderstatus.is_approved,
				ec_orderdetail.giftcard_id as product_giftcard_id, 
				UNIX_TIMESTAMP( DATE_ADD( ec_order.order_date, INTERVAL 0 HOUR ) ) AS order_date, 
				ec_download.date_created, 
				ec_download.download_count 
				FROM 
				(((ec_order LEFT JOIN ec_orderdetail ON ec_orderdetail.order_id = ec_order.order_id) 
				LEFT JOIN ec_download ON ec_orderdetail.download_key = ec_download.download_id) 
				LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id) 
				WHERE ec_order.order_id = %d 
				ORDER BY ec_orderdetail.product_id";
		  
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
		
		do_action( 'wpeasycart_order_updated', $orderid );
		
		if( count( $results ) > 0 ){
			 return($results);
		}else{
			return array( "error" );
		}
	} 

	function updateorderinfo($orderid, $orderinfo) {
		//convert object to array
		$orderinfo = (array)$orderinfo;
		
		//get previous order notes
		$sql = "SELECT ec_order.order_notes FROM ec_order WHERE ec_order.order_id = %d";
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
		
		$old_order_notes = (string)$results[0]->order_notes;
		$new_order_notes = $old_order_notes .  PHP_EOL .  PHP_EOL . "***** Order Information Updated:  " . date("M d, Y") . " *****". PHP_EOL;
			  
		//first, edit the data
		$editedsql = "UPDATE ec_order SET ec_order.order_weight = %s, ec_order.promo_code = %s, ec_order.giftcard_id = %s, ec_order.grand_total = %s, ec_order.tax_total = %s, ec_order.shipping_total = %s, ec_order.discount_total = %s, ec_order.vat_total = %s, ec_order.duty_total = %s, ec_order.order_notes = %s WHERE ec_order.order_id = %d";
		
		$this->db->query( $this->db->prepare( $editedsql, $orderinfo['orderweight'], $orderinfo['couponcode'], $orderinfo['giftcard'], $orderinfo['ordertotal'], $orderinfo['tax'], $orderinfo['shipping'], $orderinfo['discounts'], $orderinfo['vat'], $orderinfo['duty'], $new_order_notes, $orderid ) ); 
		

		//then, just get the new order information and return
		$sql = "SELECT 
				ec_orderdetail.*, 
				ec_order.*, 
				( ec_orderdetail.use_advanced_optionset OR ec_orderdetail.is_deconetwork ) AS use_advanced_optionset,
				ec_orderstatus.order_status, 
				ec_orderstatus.is_approved,
				ec_orderdetail.giftcard_id as product_giftcard_id, 
				UNIX_TIMESTAMP( DATE_ADD( ec_order.order_date, INTERVAL 0 HOUR ) ) AS order_date, 
				ec_download.date_created, 
				ec_download.download_count 
				FROM 
				(((ec_order LEFT JOIN ec_orderdetail ON ec_orderdetail.order_id = ec_order.order_id) 
				LEFT JOIN ec_download ON ec_orderdetail.download_key = ec_download.download_id) 
				LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id) 
				WHERE ec_order.order_id = %d 
				ORDER BY ec_orderdetail.product_id";
		  
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
		
		if( count( $results ) > 0 ){
			 return($results);
		}else{
			return array( "error" );
		}
	} 


	function updateorderaddresses($orderid, $addressinfo) {
		//convert object to array
		$addressinfo = (array)$addressinfo;
		
		//get previous order notes
		$sql = "SELECT ec_order.order_notes FROM ec_order WHERE ec_order.order_id = %d";
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
		
		$old_order_notes = (string)$results[0]->order_notes;
		$new_order_notes = $old_order_notes .  PHP_EOL .  PHP_EOL . "***** Address Information Updated:  " . date("M d, Y") . " *****". PHP_EOL;
			  
		//first, edit the data
		$editedsql = "UPDATE ec_order SET ec_order.billing_first_name = %s, ec_order.billing_last_name = %s, ec_order.billing_company_name = %s, ec_order.billing_address_line_1 = %s, ec_order.billing_address_line_2 = %s, ec_order.billing_city = %s, ec_order.billing_state = %s, ec_order.billing_country = %s,  ec_order.billing_zip = %s, ec_order.billing_phone = %s, ec_order.shipping_first_name = %s, ec_order.shipping_last_name = %s, ec_order.shipping_company_name = %s, ec_order.shipping_address_line_1 = %s, ec_order.shipping_address_line_2 = %s, ec_order.shipping_city = %s, ec_order.shipping_state = %s, ec_order.shipping_country = %s,  ec_order.shipping_zip = %s, ec_order.shipping_phone = %s, ec_order.user_email = %s, ec_order.order_notes = %s, ec_order.vat_registration_number = %s  WHERE ec_order.order_id = %d";
		
		$this->db->query( $this->db->prepare( $editedsql, $addressinfo['billing_first_name'], $addressinfo['billing_last_name'], $addressinfo['billing_company_name'], $addressinfo['billing_address_line_1'], $addressinfo['billing_address_line_2'], $addressinfo['billing_city'], $addressinfo['billing_state'], $addressinfo['billing_country'], $addressinfo['billing_zip'], $addressinfo['billing_phone'], $addressinfo['shipping_first_name'], $addressinfo['shipping_last_name'], $addressinfo['shipping_company_name'], $addressinfo['shipping_address_line_1'], $addressinfo['shipping_address_line_2'], $addressinfo['shipping_city'], $addressinfo['shipping_state'], $addressinfo['shipping_country'], $addressinfo['shipping_zip'], $addressinfo['shipping_phone'], $addressinfo['user_email'], $new_order_notes, $addressinfo['vat_registration'], $orderid ) );
		

		//then, just get the new order information and return
		$sql = "SELECT 
				ec_orderdetail.*, 
				ec_order.*, 
				( ec_orderdetail.use_advanced_optionset OR ec_orderdetail.is_deconetwork ) AS use_advanced_optionset,
				ec_orderstatus.order_status, 
				ec_orderstatus.is_approved,
				ec_orderdetail.giftcard_id as product_giftcard_id, 
				UNIX_TIMESTAMP( DATE_ADD( ec_order.order_date, INTERVAL 0 HOUR ) ) AS order_date, 
				ec_download.date_created, 
				ec_download.download_count 
				FROM 
				(((ec_order LEFT JOIN ec_orderdetail ON ec_orderdetail.order_id = ec_order.order_id) 
				LEFT JOIN ec_download ON ec_orderdetail.download_key = ec_download.download_id) 
				LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id) 
				WHERE ec_order.order_id = %d 
				ORDER BY ec_orderdetail.product_id";
		  
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
		
		do_action( 'wpeasycart_order_updated', $orderid );
		
		if( count( $results ) > 0 ){
			 return($results);
		}else{
			return array( "error" );
		}
	}

	function getorderdetailsadvancedoptions( $orderdetails_id ){
		
		$sql = "SELECT 
					ec_order_option.* 
				FROM 
					ec_order_option 
				WHERE 
					ec_order_option.orderdetail_id = %s 
				ORDER BY 
					ec_order_option.order_option_id ASC";
		
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderdetails_id ) );
		  
		 // If product is deconetwork, lets add the data into the system
		$order_detail_row = $this->db->get_row( $this->db->prepare( "SELECT 
															ec_orderdetail.is_deconetwork,
															ec_orderdetail.deconetwork_id,
															ec_orderdetail.deconetwork_name,
															ec_orderdetail.deconetwork_product_code,
															ec_orderdetail.deconetwork_options,
															ec_orderdetail.deconetwork_color_code,
															ec_orderdetail.product_id,
															ec_orderdetail.deconetwork_image_link
														FROM ec_orderdetail 
														WHERE ec_orderdetail.orderdetail_id = %d", $orderdetails_id ) );
		if( $order_detail_row->is_deconetwork ){
			$deconetwork1 = new stdClass( );
			$deconetwork1->orderdetail_id = $orderdetails_id;
			$deconetwork1->option_name = "DecoNetwork ID: ";
			$deconetwork1->optionitem_name = "";
			$deconetwork1->option_type = "text";
			$deconetwork1->option_value = $order_detail_row->deconetwork_id;
			$deconetwork1->option_price_change = "";
			$results[] = $deconetwork1;
			$deconetwork2 = new stdClass( );
			$deconetwork2->option_name = "DecoNetwork Name: ";
			$deconetwork2->optionitem_name = "";
			$deconetwork2->option_type = "text";
			$deconetwork2->option_value =  $order_detail_row->deconetwork_name;
			$deconetwork2->option_price_change = "";
			$results[] = $deconetwork2;
			$deconetwork3 = new stdClass( );
			$deconetwork3->option_name = "DecoNetwork Product Code: ";
			$deconetwork3->optionitem_name = "";
			$deconetwork3->option_type = "text";
			$deconetwork3->option_value = $order_detail_row->deconetwork_product_code;
			$deconetwork3->option_price_change = "";
			$results[] = $deconetwork3;
			$deconetwork4 = new stdClass( );
			$deconetwork4->option_name = "DecoNetwork Options: ";
			$deconetwork4->optionitem_name = "";
			$deconetwork4->option_type = "text";
			$deconetwork4->option_value = $order_detail_row->deconetwork_options;
			$deconetwork4->option_price_change = "";
			$results[] = $deconetwork4;
			$deconetwork5 = new stdClass( );
			$deconetwork5->option_name = "DecoNetwork Color Code: ";
			$deconetwork5->optionitem_name = "";
			$deconetwork5->option_type = "text";
			$deconetwork5->option_value = $order_detail_row->deconetwork_color_code;
			$deconetwork5->option_price_change = "";
			$results[] = $deconetwork5;
			$deconetwork6 = new stdClass( );
			$deconetwork6->option_name = "DecoNetwork Image Link: ";
			$deconetwork6->optionitem_name = "";
			$deconetwork6->option_type = "text";
			$deconetwork6->option_value = $order_detail_row->deconetwork_image_link;
			$deconetwork6->option_price_change = "";
			$results[] = $deconetwork6;
		}
		// END deconetwork check
		  
		  if( count( $results ) > 0 ){
			  return( $results );
		  }else{
			  return array( "noresults" );
		  }
		  
	}//getorderdetailsadvancedoptions
	
	function getorders($startrecord, $limit, $orderby, $ordertype, $filter) {
		  
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				ec_order.billing_first_name, 
				ec_order.billing_last_name, 
				ec_order.order_viewed, 
				ec_order.grand_total, 
				UNIX_TIMESTAMP( DATE_ADD( ec_order.order_date, INTERVAL 0 HOUR ) ) AS order_date, 
				ec_order.user_email, ec_order.user_id,  
				ec_order.order_id, ec_order.orderstatus_id, ec_orderstatus.order_status FROM ec_order LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE ec_order.user_id != -1 " . $filter . " ORDER BY " . $orderby . " " . $ordertype . " LIMIT " . $startrecord . ", " . $limit;
		
		$results = $this->db->get_results( $sql );
		
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( count( $results ) > 0 ){
			$results[0]->totalrows = $totalquery;
			return( $results ); 
		}else {
			return array( "noresults" );
		}
		  
	}//getorders
	
	function getorderdetails( $orderid ){
		  
		$sql = "SELECT 
				ec_orderdetail.*, 
				ec_order.*, 
				( ec_orderdetail.use_advanced_optionset OR ec_orderdetail.is_deconetwork ) AS use_advanced_optionset,
				ec_orderstatus.order_status, 
				ec_orderstatus.is_approved,
				ec_orderdetail.giftcard_id as product_giftcard_id,
				UNIX_TIMESTAMP( DATE_ADD( ec_order.order_date, INTERVAL 0 HOUR ) ) AS order_date, 
				ec_download.date_created, 
				ec_download.download_count 
				FROM 
				(((ec_order LEFT JOIN ec_orderdetail ON ec_orderdetail.order_id = ec_order.order_id) LEFT JOIN ec_download ON ec_orderdetail.download_key = ec_download.download_id) LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id) WHERE ec_order.order_id = %d ORDER BY ec_orderdetail.product_id";
		
		$results = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
		
		if( count( $results ) > 0 ){
			return($results);
		}else{
			return array( "noresults" );
		}
		  
	}//getorderdetail
	
	function getorderstatus( ){
		  
		$sql = "SELECT ec_orderstatus.* FROM ec_orderstatus ORDER BY ec_orderstatus.status_id ASC";
		$results = $this->db->get_results( $sql );
		
		for( $i=0; $i<count( $results ); $i++ ){
			if( $GLOBALS['language']->get_text( 'account_order_details', 'order_status_' . str_replace( " ", "_", strtolower( $results[$i]->order_status ) ) ) ){
				$results[$i]->order_status = $GLOBALS['language']->get_text( 'account_order_details', 'order_status_' . str_replace( " ", "_", strtolower( $results[$i]->order_status ) ) ); 
			}else{
				$results[$i]->order_status = $results[$i]->order_status;
			}
		}
		
		if( count( $results ) > 0 ){
			return( $results );
		}else{
			return array( "noresults" );
		}
		  
	}//getorderstatus
	
	function updateorderstatus( $orderid, $status ){
		
		/* Check for Applicable Stock Adjustments */
		$order = $this->db->get_row( $this->db->prepare( "SELECT ec_order.*, ec_orderstatus.is_approved FROM ec_order LEFT JOIN ec_orderstatus ON ec_orderstatus.status_id = ec_order.orderstatus_id WHERE ec_order.order_id = %d", $orderid ) );
		$orderstatus = $this->db->get_row( $this->db->prepare( "SELECT ec_orderstatus.* FROM ec_orderstatus WHERE ec_orderstatus.status_id = %d", $status ) );
		$orderdetails = $this->db->get_results( $this->db->prepare( "SELECT * FROM ec_orderdetail WHERE ec_orderdetail.order_id = %d", $orderid ) );
		
		if( !$order->is_approved && $orderstatus->is_approved ){ // Take out of stock
			$ec_db = new ec_db( );
			foreach( $orderdetails as $orderdetail ){
				if( !$orderdetail->stock_adjusted ){
					$product = $this->db->get_row( $this->db->prepare( "SELECT ec_product.* FROM ec_product WHERE ec_product.product_id = %d", $orderdetail->product_id ) );
					if( $product ){
						if( $product->use_optionitem_quantity_tracking )
							$ec_db->update_quantity_value( $orderdetail->quantity, $orderdetail->product_id, $orderdetail->optionitem_id_1, $orderdetail->optionitem_id_2, $orderdetail->optionitem_id_3, $orderdetail->optionitem_id_4, $orderdetail->optionitem_id_5 );
						$ec_db->update_product_stock( $orderdetail->product_id, $orderdetail->quantity );
						$ec_db->update_details_stock_adjusted( $orderdetail->orderdetail_id );
					}
				}
			}
		}
		/* END Stock Adjustment Check */
		
		$sql = "UPDATE ec_order SET ec_order.orderstatus_id = %s WHERE ec_order.order_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $status, $orderid ) );
		
		if( $status == "3" || $status == "6" || $status == "10" || $status == "15" )
			do_action( 'wpeasycart_order_paid', $orderid );
		else if( $status == "2" )
			do_action( 'wpeasycart_order_shipped', $orderid );
		else if( $status == "16" )
			do_action( 'wpeasycart_full_order_refund', $orderid );
		else if( $status == "17" )
			do_action( 'wpeasycart_partial_order_refund', $orderid );
		
		do_action( 'wpeasycart_order_updated', $orderid );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
	
	}//updateorderstatus
	
	function updateordernotes( $orderid, $notes ){
		
		$sql = "UPDATE ec_order SET ec_order.order_notes = %s WHERE ec_order.order_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $notes, $orderid ) );
		
		if( $rows_affected ){
			return array( "success" );	
		}else{
			return array( "error" );
		}
	
	}//updateordernotes
	
	function updateorderviewed( $orderid ){
		
		$sql = "UPDATE ec_order SET order_viewed = 1 WHERE ec_order.order_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $orderid ) );
		
		if( $rows_affected ){
			return array( "success" );	
		}else{
			return array( "error" );
		}
		
	}	//updateorderviewed
	
	function deleteorder( $orderid ){
		
		$sql = "DELETE FROM ec_order WHERE ec_order.order_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $orderid ) );
		
		$sql = "DELETE FROM ec_orderdetail WHERE ec_orderdetail.order_id = %d";
		$this->db->query( $this->db->prepare( $sql, $orderid ) );
		
		$sql = "DELETE FROM ec_download WHERE ec_download.orderid = %d";
		$this->db->query( $this->db->prepare( $sql, $orderid ) );
		
		do_action( 'wpeasycart_order_deleted', $orderid );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//deleteorder
	
	function updatefraktjaktshipping( $orderid, $fraktjaktorderid, $fraktjaktshippingid){
		
		$sql = "UPDATE ec_order SET ec_order.fraktjakt_order_id = %s, ec_order.fraktjakt_shipment_id = %s WHERE ec_order.order_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $fraktjaktorderid, $fraktjaktshippingid, $orderid ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function updateshippingstatus( $orderid, $shipcarrier, $shiptrackingcode, $sendemail, $clientemail ){
		
		$sql = "UPDATE ec_order SET ec_order.shipping_carrier = %s, ec_order.tracking_number = %s WHERE ec_order.order_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $shipcarrier, $shiptrackingcode, $orderid ) );
		
		if( $sendemail == 1 ){
			
			$sql = "SELECT ec_order.*, ec_country1.name_cnt as billing_country_name, ec_country2.name_cnt as shipping_country_name FROM ec_order LEFT JOIN ec_country as ec_country1 ON ec_country1.iso2_cnt = ec_order.billing_country LEFT JOIN ec_country AS ec_country2 ON ec_country2.iso2_cnt = ec_order.shipping_country WHERE ec_order.order_id = %d";
			$order = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
			
			$sql = "SELECT ec_orderdetail.* FROM ec_orderdetail WHERE ec_orderdetail.order_id = %d ORDER BY ec_orderdetail.product_id";
			$orderdetails = $this->db->get_results( $this->db->prepare( $sql, $orderid ) );
			
			$trackingnumber = $order[0]->tracking_number;
			$shipcarrier = $order[0]->shipping_carrier;
			
			$email_logo_url = stripslashes( get_option( 'ec_option_email_logo' ) ) . "' alt='" . get_bloginfo( "name" );
	 	
			$orderfromemail = stripslashes( get_option( 'ec_option_order_from_email' ) );
			
			ob_start( );
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_shipping_email.php' ) ){ 
				include WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_shipping_email.php';
			}else{
				include WP_PLUGIN_DIR . '/wp-easycart/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_shipping_email.php';
			}
			$message = ob_get_clean();
			
			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=utf-8\r\n";
			$headers .= "From: " . stripslashes( get_option( 'ec_option_order_from_email' ) )  . "\r\n";
			$headers .= "Reply-To: " . stripslashes( get_option( 'ec_option_order_from_email' ) )  . "\r\n";
			$headers .= "X-Mailer: PHP/".phpversion()  . "\r\n";
			
			if( get_option( 'ec_option_use_wp_mail' ) ){
				wp_mail( $order[0]->user_email, $GLOBALS['language']->get_text( 'ec_shipping_email', 'shipping_email_title' ) . " " . $orderid, $message, $headers );
			}else{
				$admin_email = stripslashes( get_option( 'ec_option_bcc_email_addresses' ) );
				$to = $order[0]->user_email;
				$subject = $GLOBALS['language']->get_text( 'ec_shipping_email', 'shipping_email_title' ) . " " . $orderid;
				$mailer = new wpeasycart_mailer( );
				$mailer->send_order_email( $to, $subject, $message );
				$mailer->send_order_email( $admin_email, $subject, $message );
			}
		}
		
		do_action( 'wpeasycart_order_updated', $orderid );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//updateshippingstatus
	
	function refundorder( $order_id, $is_full_refund, $refund_amount, $order_gateway ){
		
		// Get order charge info
		$order = $this->db->get_row( $this->db->prepare( "SELECT affirm_charge_id, stripe_charge_id, order_notes, refund_total, grand_total, gateway_transaction_id FROM ec_order WHERE order_id = %d", $order_id ) );
		
		// First refund order
		$result = false;
		
		if( $order_gateway == "affirm" ){
			$gateway = new ec_affirm( );
			$result = $gateway->refund_order( $order_id, $order->affirm_charge_id, $refund_amount );
			
		}else if( $order_gateway == "stripe" ){
			if( class_exists( 'ec_stripe' ) )
				$gateway = new ec_stripe( );
			else
				$gateway = new ec_stripe_connect( );
			$result = $gateway->refund_charge( $order->stripe_charge_id, $refund_amount );
		
		}else if( $order_gateway == "stripe_connect" ){
			$gateway = new ec_stripe_connect( );
			$result = $gateway->refund_charge( $order->stripe_charge_id, $refund_amount );
		
		}else if( $order_gateway == "authorize" ){
			$gateway = new ec_authorize( );
			$result = $gateway->refund_charge( $order->gateway_transaction_id, $refund_amount );
		
		}else if( $order_gateway == "beanstream" ){
			$gateway = new ec_beanstream( );
			$result = $gateway->refund_charge( $order->gateway_transaction_id, $refund_amount );
		
		}else if( $order_gateway == "payline" ){
			$gateway = new ec_payline( );
			$result = $gateway->refund_charge( $order->gateway_transaction_id, $refund_amount );
		
		}else if( $order_gateway == "nmi" ){
			$gateway = new ec_nmi( );
			$result = $gateway->refund_charge( $order->gateway_transaction_id, $refund_amount );
		
		}else if( $order_gateway == "intuit" ){
			$gateway = new ec_intuit( );
			$result = $gateway->refund_charge( $order->gateway_transaction_id, $refund_amount );
		
		}else if( $order_gateway == "square" ){
			$gateway = new ec_square( );
			$result = $gateway->refund_charge( $order->gateway_transaction_id, $refund_amount );
		
		}
		
		if( $result ){	// If goes through successfully, update order and return true
			
			$date = date('l jS \of F Y h:i:s A');
			if( strlen( $order->order_notes ) > 0 )
				$new_order_notes = $order->order_notes . PHP_EOL .  PHP_EOL . "Refund of " . $refund_amount . " made on " . $date;
			else
				$new_order_notes = "Refund of " . $refund_amount . " made on " . $date;
			
			if( $is_full_refund || ( $refund_amount + $order->refund_total ) >= $order->grand_total )
				$orderstatus_id = 16;
			else
				$orderstatus_id = 17;
			
			$this->db->query( $this->db->prepare( "UPDATE ec_order SET ec_order.refund_total = ( ec_order.refund_total + %s ), ec_order.order_notes = %s, ec_order.orderstatus_id = %d WHERE ec_order.order_id = %d", $refund_amount, $new_order_notes, $orderstatus_id, $order_id ) );
			
			if( $orderstatus_id == 16 ){
				// Check for gift card to refund
				$order_details = $this->db->get_results( $this->db->prepare( "SELECT is_giftcard, giftcard_id FROM ec_orderdetail WHERE order_id = %d", $order_id ) );
				foreach( $order_details as $detail ){
					if( $detail->is_giftcard ){
						$this->db->query( $this->db->prepare( "DELETE FROM ec_giftcard WHERE ec_giftcard.giftcard_id = %s", $detail->giftcard_id ) );
					}
				}
			}
			if( $is_full_refund || ( $refund_amount + $order->refund_total ) >= $order->grand_total )
				do_action( 'wpeasycart_full_order_refund', $order_id );
			else
				do_action( 'wpeasycart_partial_order_refund', $order_id, $refund_amount, ( $refund_amount + $order->refund_total ) );
		
			do_action( 'wpeasycart_order_updated', $orderid );
			
			return array( "order_notes" => $new_order_notes, "orderstatus_id" => $orderstatus_id );
			
		}else{
			return false;
		}
		
	}//refund order
	
	function resendgiftcardemail( $order_id, $orderdetails_id ){
		
		$sql = "SELECT giftcard_id, gift_card_message, gift_card_from_name, gift_card_to_name, gift_card_email, title, unit_price, is_deconetwork, deconetwork_image_link, image1 FROM ec_orderdetail WHERE orderdetail_id = %d";
		$orderdetail_row = $this->db->get_row( $this->db->prepare( $sql, $orderdetails_id ) );
		$this->send_gift_card_email( $orderdetail_row, $orderdetail_row->giftcard_id );
		
		//return 'success' or 'error'
		return 'success';
		
	}
	
	function send_gift_card_email( $cart_item, $giftcard_id ){
		
		$storepageid = get_option('ec_option_storepage');
		
		if( function_exists( 'icl_object_id' ) ){
			$storepageid = icl_object_id( $storepageid, 'page', true, ICL_LANGUAGE_CODE );
		}
		
		$store_page = get_permalink( $storepageid );
		
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$store_page = $https_class->makeUrlHttps( $store_page );
		}
		
		if( substr_count( $store_page, '?' ) )				$permalink_divider = "&";
		else												$permalink_divider = "?";
		
		$email_logo_url = get_option( 'ec_option_email_logo' ) . "' alt='" . get_bloginfo( "name" );
	 	
		$headers   = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-Type: text/html; charset=utf-8";
		$headers[] = "From: " . stripslashes( get_option( 'ec_option_order_from_email' ) );
		$headers[] = "Reply-To: " . stripslashes( get_option( 'ec_option_order_from_email' ) );
		$headers[] = "X-Mailer: PHP/".phpversion();
		
		ob_start();
        if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_email_giftcard.php' ) )	
			include WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_email_giftcard.php';
		else
			include WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cart_email_giftcard.php';
			
        $message = ob_get_clean();
		
		if( get_option( 'ec_option_use_wp_mail' ) ){
			wp_mail( $cart_item->gift_card_email, $GLOBALS['language']->get_text( "cart_success", "cart_giftcard_receipt_title" ), $message, implode("\r\n", $headers) );
			wp_mail( stripslashes( get_option( 'ec_option_bcc_email_addresses' ) ), $GLOBALS['language']->get_text( "cart_success", "cart_giftcard_receipt_title" ), $message, implode("\r\n", $headers) );
		}else{
			$admin_email = stripslashes( get_option( 'ec_option_bcc_email_addresses' ) );
			$to = $cart_item->gift_card_email;
			$subject = $GLOBALS['language']->get_text( "cart_success", "cart_giftcard_receipt_title" );
			$mailer = new wpeasycart_mailer( );
			$mailer->send_order_email( $to, $subject, $message );
			$mailer->send_order_email( $admin_email, $subject, $message );
		}
		
	}
	
}//ec_admin_orders
?>