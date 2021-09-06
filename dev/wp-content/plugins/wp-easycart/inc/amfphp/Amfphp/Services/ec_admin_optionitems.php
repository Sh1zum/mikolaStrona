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

class ec_admin_optionitems{		
	
	private $db;
	
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

	}//ec_admin_optionitems
	
	public function _getMethodRoles( $methodName ){

			 if( $methodName == 'getoptionitems' ) 					return array( 'admin' );
		else if( $methodName == 'deleteoptionitem' ) 				return array( 'admin' );
		else if( $methodName == 'updateoptionitem' ) 				return array( 'admin' );
		else if( $methodName == 'addoptionitem' ) 					return array( 'admin' );
		else if( $methodName == 'getproductimages' ) 				return array( 'admin' );
		else if( $methodName == 'getoptionitemquantities' ) 		return array( 'admin' );
		else if( $methodName == 'saveoptionitemquantities' ) 		return array( 'admin' );
		else if( $methodName == 'removealloptionitemquantities' ) 	return array( 'admin' );
		else if( $methodName == 'updateoptionvalues' ) 				return array( 'admin' );
		else  														return null;

	}//_getMethodRoles
	
	function getoptionitems( $startrecord, $limit, $orderby, $ordertype, $filter, $parentid ){

		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_optionitem.* FROM ec_optionitem WHERE ec_optionitem.optionitem_id != '' AND ec_optionitem.option_id = " . $parentid . " " . $filter . " ORDER BY " . $orderby . " " . $ordertype . " LIMIT " . $startrecord . ", " . $limit;
		$results = $this->db->get_results( $sql );
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( count( $results ) > 0 ){
			$results[0]->totalrows = $totalquery;
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}//getoptionitems
	
	
	function deleteoptionitem( $optionitemid ){
		
		// Delete the Option Item  
		$sql = "DELETE FROM ec_optionitem WHERE ec_optionitem.optionitem_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $optionitemid ) );
		
		// Delete the Option Item Quantity Values
		$sql = "DELETE FROM ec_optionitemquantity WHERE ec_optionitemquantity.optionitem_id = %d OR ec_optionitemquantity.optionitem_id_1 = %d OR ec_optionitemquantity.optionitem_id_2 = %d OR ec_optionitemquantity.optionitem_id_3 = %d OR ec_optionitemquantity.optionitem_id_4 = %d OR ec_optionitemquantity.optionitem_id_5 = %d";
		$this->db->query( $this->db->prepare( $sql, $optionitemid, $optionitemid, $optionitemid, $optionitemid, $optionitemid, $optionitemid ) );
		
		// Delete the Option Item Image Values
		$sql = "DELETE FROM ec_optionitemimage WHERE ec_optionitemimage.optionitem_id = %d";
		$this->db->query( $this->db->prepare( $sql, $optionitemid ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//deleteoptionitem
	
	function updateoptionitem( $optionitemid, $optionitem ){
		
		$optionitem = (array)$optionitem;
		
		$sql = "UPDATE ec_optionitem SET ec_optionitem.option_id = %d, ec_optionitem.optionitem_name = %s, ec_optionitem.optionitem_price = %s, ec_optionitem.optionitem_price_onetime = %s, ec_optionitem.optionitem_price_override = %s, ec_optionitem.optionitem_price_multiplier = %s, ec_optionitem.optionitem_weight = %s, ec_optionitem.optionitem_weight_onetime = %s, ec_optionitem.optionitem_weight_override = %s, ec_optionitem.optionitem_weight_multiplier = %s, ec_optionitem.optionitem_order = %s, ec_optionitem.optionitem_icon = %s, ec_optionitem.optionitem_initial_value = %s, ec_optionitem.optionitem_model_number = %s, ec_optionitem.optionitem_allow_download = %s, ec_optionitem.optionitem_disallow_shipping = %s, ec_optionitem.optionitem_initially_selected = %s, ec_optionitem.optionitem_price_per_character = %s WHERE ec_optionitem.optionitem_id = %d";
		
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $optionitem['optionparentID'], $optionitem['optionitemname'], $optionitem['optionitemprice'], $optionitem['optionitempriceonetime'], $optionitem['optionitempriceoverride'], $optionitem['optionitempricemultiplier'], $optionitem['optionitemweight'], $optionitem['optionitemweightonetime'], $optionitem['optionitemweightoverride'], $optionitem['optionitemweightmultiplier'], $optionitem['optionorder'], $optionitem['optionitemicon'], $optionitem['optioniteminitialvalue'], $optionitem['optionitemmodelnumber'], $optionitem['optionitem_allow_download'], $optionitem['optionitem_disallow_shipping'], $optionitem['optionitem_initially_selected'], $optionitem['optionitem_price_per_character'], $optionitemid ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//updateoptionitem
	
	function addoptionitem( $optionitem ){
		
		$optionitem = (array)$optionitem;
		
		$sql = "INSERT INTO ec_optionitem( ec_optionitem.option_id, ec_optionitem.optionitem_name, ec_optionitem.optionitem_price, ec_optionitem.optionitem_price_onetime, ec_optionitem.optionitem_price_override, ec_optionitem.optionitem_price_multiplier, ec_optionitem.optionitem_weight, ec_optionitem.optionitem_weight_onetime, ec_optionitem.optionitem_weight_override, ec_optionitem.optionitem_weight_multiplier, ec_optionitem.optionitem_order, ec_optionitem.optionitem_icon, ec_optionitem.optionitem_initial_value, ec_optionitem.optionitem_model_number, ec_optionitem.optionitem_allow_download, ec_optionitem.optionitem_disallow_shipping, ec_optionitem.optionitem_initially_selected, ec_optionitem.optionitem_price_per_character) VALUES( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s, %s, %s, %s, %s )";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $optionitem['optionparentID'], $optionitem['optionitemname'], $optionitem['optionitemprice'], $optionitem['optionitempriceonetime'], $optionitem['optionitempriceoverride'], $optionitem['optionitempricemultiplier'], $optionitem['optionitemweight'], $optionitem['optionitemweightonetime'], $optionitem['optionitemweightoverride'], $optionitem['optionitemweightmultiplier'], $optionitem['optionorder'], $optionitem['optionitemicon'], $optionitem['optioniteminitialvalue'], $optionitem['optionitemmodelnumber'], $optionitem['optionitem_allow_download'], $optionitem['optionitem_disallow_shipping'], $optionitem['optionitem_initially_selected'], $optionitem['optionitem_price_per_character']  ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//addoptionitem
	
	function getproductimages( $ProductID, $OptionItemID ){
		
		$sql = "SELECT * FROM ec_optionitemimage WHERE ec_optionitemimage.product_id = %d AND ec_optionitemimage.optionitem_id = %d";
		$results = $this->db->get_results( $this->db->prepare( $sql, $ProductID, $OptionItemID ) );
		
		if( count( $results ) > 0 ){
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}//getproductimages
	
	function getoptionitemquantities( $productid, $option1, $option2, $option3, $option4, $option5 ){
		
		/* START THE CREATION OF A COMPLEX QUERY. THIS COMBINES MULTIPLE OPTIONS TO ALLOW A USER TO ENTER A QUANTITY FOR EACH */
		$sql = "";
		if( $option1 != 0 ){
			$sql .= $this->db->prepare( "SELECT * FROM ( SELECT optionitem_name AS optname1, optionitem_id as optid1 FROM ec_optionitem WHERE option_id = %d ) as optionitems1 ", $option1 );
		}
		
		if($option2 != 0){
			$sql .= $this->db->prepare(" JOIN ( SELECT optionitem_name AS optname2, optionitem_id as optid2 FROM ec_optionitem WHERE option_id = %d ) as optionitems2 ON (1=1) ", $option2 );
		}
		
		if($option3 != 0){
			$sql .= $this->db->prepare(" JOIN ( SELECT optionitem_name AS optname3, optionitem_id as optid3 FROM ec_optionitem WHERE option_id = %d ) as optionitems3 ON (1=1) ", $option3 );
		}
		
		if($option4 != 0){
			$sql .= $this->db->prepare(" JOIN ( SELECT optionitem_name AS optname4, optionitem_id as optid4 FROM ec_optionitem WHERE option_id = %d ) as optionitems4 ON (1=1) ", $option4 );
		}
		
		if($option5 != 0){
			$sql .= $this->db->prepare(" JOIN ( SELECT optionitem_name AS optname5, optionitem_id as optid5 FROM ec_optionitem WHERE option_id = %s ) as optionitems5 ON (1=1) ", $option5 );
		}
		
		$sql .= " LEFT JOIN ec_optionitemquantity ON ( 1=1 ";
		
		if($option1 != 0){
			$sql .= " AND ec_optionitemquantity.optionitem_id_1 = optid1";
		}
		
		if($option2 != 0){
			$sql .= " AND ec_optionitemquantity.optionitem_id_2 = optid2";
		}
		
		if($option3 != 0){
			$sql .= " AND ec_optionitemquantity.optionitem_id_3 = optid3";
		}
		
		if($option4 != 0){
			$sql .= " AND ec_optionitemquantity.optionitem_id_4 = optid4";
		}
		
		if($option5 != 0){
			$sql .= " AND ec_optionitemquantity.optionitem_id_5 = optid5";
		}
		
		$sql .= $this->db->prepare( " AND ec_optionitemquantity.product_id = %d )", $productid );
		
		$sql .= " ORDER BY optname1";

		//Finally, get the query results
		$results = $this->db->get_results( $sql );
		
		if( count( $results ) > 0 ){
			return $results;
		}else{
			return array( "noresults" );
		}
	}//getoptionitemquantities
	
	function objectToArray( $object ){
		if( !is_object( $object ) && !is_array( $object ) ){
			return $object;
		}
		
		if( is_object( $object ) ){
			$object = get_object_vars( $object );
		}
		
		return array_map( 'objectToArray', $object );
	}//objectToArray
	
	
	function saveoptionitemquantities( $optionitems ){

		$numitems = count( $optionitems );
		
		for( $i=0; $i<$numitems; $i++ ){
			
			$optionitems[$i] = get_object_vars( $optionitems[$i] );

			//if they are 1 which is odd for amfphp to return for undefined or null variables, then set them to 0
			if (isset($optionitems[$i]['optid1']) && $optionitems[$i]['optid1'] != 1) {
				$opt1var = $optionitems[$i]['optid1'];
			} else { 
				$opt1var = 0;
			}
			if (isset($optionitems[$i]['optid2']) && $optionitems[$i]['optid2'] != 1) {
				$opt2var = $optionitems[$i]['optid2'];
			} else { 
				$opt2var = 0;
			}
			if (isset($optionitems[$i]['optid3']) && $optionitems[$i]['optid3'] != 1) {
				$opt3var = $optionitems[$i]['optid3'];
			} else { 
				$opt3var = 0;
			}
			if (isset($optionitems[$i]['optid4']) && $optionitems[$i]['optid4'] != 1) {
				$opt4var = $optionitems[$i]['optid4'];
			} else { 
				$opt4var = 0;
			}
			if (isset($optionitems[$i]['optid5']) && $optionitems[$i]['optid5'] != 1) {
				$opt5var = $optionitems[$i]['optid5'];
			} else { 
				$opt5var = 0;
			}
				
			//now we can update or insert
			if( $optionitems[$i]['optionitemquantityid'] ){
				$sql = $this->db->prepare( "UPDATE ec_optionitemquantity SET ec_optionitemquantity.optionitem_id_1 = %d, ec_optionitemquantity.optionitem_id_2 = %d, ec_optionitemquantity.optionitem_id_3 = %d, ec_optionitemquantity.optionitem_id_4 = %d, ec_optionitemquantity.optionitem_id_5 = %d, ec_optionitemquantity.product_id = %d, ec_optionitemquantity.quantity = %d WHERE ec_optionitemquantity.optionitemquantity_id = %d", $opt1var, $opt2var, $opt3var, $opt4var, $opt5var, $optionitems[$i]['product_id'], $optionitems[$i]['quantity'], $optionitems[$i]['optionitemquantityid'] );
				
				$this->db->query( $sql );
				$rows_affected = 1; // Updating, do not check rows affected in case no changes were made
				
			}else{
				$sql = $this->db->prepare( "INSERT INTO ec_optionitemquantity(ec_optionitemquantity.optionitem_id_1, ec_optionitemquantity.optionitem_id_2, ec_optionitemquantity.optionitem_id_3, ec_optionitemquantity.optionitem_id_4, ec_optionitemquantity.optionitem_id_5, ec_optionitemquantity.product_id, ec_optionitemquantity.quantity) VALUES(%d, %d, %d, %d, %d, %d, %d)", $opt1var, $opt2var, $opt3var, $opt4var, $opt5var, $optionitems[$i]['product_id'], $optionitems[$i]['quantity'] ); 
				
				$rows_affected = $this->db->query( $sql );
			
			}
			
		}
		
		if( $rows_affected ){
			return array( "success" );
		} else {
			return array( "error" );
		}
					
	}//saveoptionitemquantities
	
	function removealloptionitemquantities( $productid ){
		
		$sql = "DELETE FROM ec_optionitemquantity WHERE ec_optionitemquantity.product_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $productid ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//removealloptionitemquantities
	
	function updateoptionvalues( $productid, $option1, $option2, $option3, $option4, $option5 ){
		
		$sql = "UPDATE ec_product SET ec_product.option_id_1 = %d, ec_product.option_id_2 = %d, ec_product.option_id_3 = %d, ec_product.option_id_4 = %d, ec_product.option_id_5 = %d WHERE ec_product.product_id = %d";
		$this->db->query( $this->db->prepare( $sql, $option1, $option2, $option3, $option4, $option5, $productid ) );
		
		$sql = "DELETE FROM ec_optionitemimage WHERE ec_optionitemimage.product_id = %d";
		$this->db->query( $this->db->prepare( $sql, $productid ) );
		
		return array( "success" );
		
	}//updateoptionvalues


}//ec_admin_optionitems
?>