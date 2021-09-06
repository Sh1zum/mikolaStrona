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

class ec_admin_options{
	
	private $db;

	function __construct( ){
	
		global $wpdb;
		$this->db = $wpdb;

	}//ec_admin_options	
	
	public function _getMethodRoles($methodName){
	
			 if( $methodName == 'getoptions' ) 				return array( 'admin' );
		else if( $methodName == 'getoptionsets' )			return array( 'admin' );
		else if( $methodName == 'deleteoption' ) 			return array( 'admin' );
		else if( $methodName == 'updateoption' ) 			return array( 'admin' );
		else if( $methodName == 'addoption' ) 				return array( 'admin' );
		else if( $methodName == 'getproductoptionitems' ) 	return array( 'admin' );
		else 												return null;
	
	}//_getMethodRoles
	
	function getoptions( ){
	
		$sql = "SELECT ec_option.* FROM ec_option ORDER BY ec_option.option_name ASC";
		$results = $this->db->get_results( $sql );
		
		if( count( $results ) > 0 ){
			return $results;
		}else{
			return array( "noresults" );
		}
	
	}//getoptions

	function getoptionsets( $startrecord, $limit, $orderby, $ordertype, $filter ){
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_option.* FROM ec_option WHERE ec_option.option_id != '' " . $filter . " ORDER BY " . $orderby . " " .  $ordertype . " LIMIT " . $startrecord . ", " . $limit;
		$results = $this->db->get_results( $sql );
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( count( $results ) > 0 ){
			$results[0]->totalrows = $totalquery;
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}//getoptionsets
	
	function deleteoption( $optionid ){
		  
		//Delete the Option
		$sql = "DELETE FROM ec_option WHERE ec_option.option_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $optionid ) );
		
		//Update Products Using This Option Set
		//Update Products Using This Option Set
		$sql = "UPDATE ec_product SET ec_product.option_id_1 = 0 WHERE ec_product.option_id_1 = %d;";
		$this->db->query( $this->db->prepare( $sql, $optionid) );
		$sql = "UPDATE ec_product SET ec_product.option_id_2 = 0 WHERE ec_product.option_id_2 = %d;";
		$this->db->query( $this->db->prepare( $sql, $optionid) );
		$sql = "UPDATE ec_product SET ec_product.option_id_3 = 0 WHERE ec_product.option_id_3 = %d;";
		$this->db->query( $this->db->prepare( $sql, $optionid) );
		$sql = "UPDATE ec_product SET ec_product.option_id_4 = 0 WHERE ec_product.option_id_4 = %d;";
		$this->db->query( $this->db->prepare( $sql, $optionid) );
		$sql = "UPDATE ec_product SET ec_product.option_id_5 = 0 WHERE ec_product.option_id_5 = %d;";
		$this->db->query( $this->db->prepare( $sql, $optionid) );
		
		//get all option items and delete them from db
		$sql = "SELECT ec_optionitem.optionitem_id from ec_optionitem WHERE ec_optionitem.option_id = %d";
		$optionitems = $this->db->get_results( $this->db->prepare( $sql, $optionid ) );
		
		foreach( $optionitems as $optionitem ){	
			
			$optionitem_id = $optionitem->optionitem_id;
			
			$sql = "DELETE FROM ec_optionitem WHERE ec_optionitem.optionitem_id = %d";
			$this->db->query( $this->db->prepare( $sql, $optionitem_id ) );
			
			$sql = "DELETE FROM ec_optionitemquantity WHERE ec_optionitemquantity.optionitem_id = %d OR ec_optionitemquantity.optionitem_id_1 = %d OR ec_optionitemquantity.optionitem_id_2 = %d OR ec_optionitemquantity.optionitem_id_3 = %d OR ec_optionitemquantity.optionitem_id_4 = %d OR ec_optionitemquantity.optionitem_id_5 = %d";
			$this->db->query( $this->db->prepare( $sql, $optionitem_id, $optionitem_id, $optionitem_id, $optionitem_id, $optionitem_id, $optionitem_id ) );
			
			$sql = "DELETE FROM ec_optionitemimage WHERE ec_optionitemimage.optionitem_id = %d";
			$this->db->query( $this->db->prepare( $sql, $optionitem_id ) );
			
		}
		
		// Delete all option to products for advanced options	
		$sql = "DELETE FROM ec_option_to_product WHERE ec_option_to_product.option_id = %d";
		$this->db->query( $this->db->prepare( $sql, $optionid ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//deleteoption
	
	function updateoption( $optionid, $option ){
		
		$option = (array)$option;
		
		$sql = "UPDATE ec_option SET ec_option.option_name = %s, ec_option.option_label = %s, ec_option.option_type = %s, ec_option.option_required = %s, ec_option.option_error_text = %s WHERE ec_option.option_id = %d";
		$this->db->query( $this->db->prepare( $sql, $option['optionname'], $option['optionlabel'], $option['optiontype'], $option['optionrequired'], $option['optionerror'], $optionid ) );
		
		//if we are switching from a swatch based option to other option, remove images
		if($option['optiontype'] != 'basic-swatch' && $option['optiontype'] != 'swatch') {
			$sql = "UPDATE ec_optionitem SET  ec_optionitem.optionitem_icon = '' WHERE ec_optionitem.option_id = %d";
			$this->db->query( $this->db->prepare( $sql,  $optionid ) );
		}
		
		//Add ec_optionitem if file, text, or text area, dimensions 1 or 2, date
		if( $option['optiontype'] == 'file' || $option['optiontype'] == 'text' || $option['optiontype'] == 'textarea' || $option['optiontype'] == 'date'  || $option['optiontype'] == 'dimensions1'  || $option['optiontype'] == 'dimensions2'  || $option['optiontype'] == 'number' ){
			
			//remove any old option items
			$sql = "DELETE FROM ec_optionitem  WHERE ec_optionitem.option_id = %d";
			$this->db->query( $this->db->prepare( $sql,  $optionid ) );
			
		  	//determine what type the new option is
			if ($option['optiontype'] == 'file') 		$op_name = 'File Field';
			if ($option['optiontype'] == 'text') 		$op_name = 'Text Box Input';
			if ($option['optiontype'] == 'textarea') 	$op_name = 'Text Area Input';
			if ($option['optiontype'] == 'date') 		$op_name = 'Date Field';
			if ($option['optiontype'] == 'dimensions1') 		$op_name = 'DimensionType1';
			if ($option['optiontype'] == 'dimensions2') 		$op_name = 'DimensionType2'; 
			if ($option['optiontype'] == 'number') 		$op_name = 'Number Field'; 
			
			//insert new option item for this updated option set
			$sql = "INSERT INTO ec_optionitem( ec_optionitem.option_id, ec_optionitem.optionitem_name, ec_optionitem.optionitem_price,  ec_optionitem.optionitem_price_onetime, ec_optionitem.optionitem_price_override, ec_optionitem.optionitem_weight, ec_optionitem.optionitem_weight_onetime, ec_optionitem.optionitem_weight_override, ec_optionitem.optionitem_order, ec_optionitem.optionitem_icon, ec_optionitem.optionitem_initial_value ) VALUES ( %d, %s, '0', '0', '-1', '0', '0', '-1', '1', '', '' )";
			$this->db->query( $this->db->prepare( $sql, $optionid, $op_name ) );
		}
		
		
		
		return array( "success" );
		
	}//updateoption
	
	function addoption( $option ){
		
		$option = (array)$option;
		
		$sql = "INSERT INTO ec_option( ec_option.option_name, ec_option.option_label, ec_option.option_type, ec_option.option_required, ec_option.option_error_text ) VALUES( %s, %s, %s, %s, %s )";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $option['optionname'], $option['optionlabel'], $option['optiontype'], $option['optionrequired'], $option['optionerror'] ) );
		
		$option_id_parent = $this->db->insert_id;
		
		//Add ec_optionitem if file, text, or text area
		if( $option['optiontype'] == 'file' || $option['optiontype'] == 'text' || $option['optiontype'] == 'textarea' || $option['optiontype'] == 'date'  || $option['optiontype'] == 'dimensions1'  || $option['optiontype'] == 'dimensions2'   || $option['optiontype'] == 'number' ){
		  
			if ($option['optiontype'] == 'file') 		$op_name = 'File Field';
			if ($option['optiontype'] == 'text') 		$op_name = 'Text Box Input';
			if ($option['optiontype'] == 'textarea') 	$op_name = 'Text Area Input';
			if ($option['optiontype'] == 'date') 		$op_name = 'Date Field';
			if ($option['optiontype'] == 'dimensions1') 		$op_name = 'DimensionType1';
			if ($option['optiontype'] == 'dimensions2') 		$op_name = 'DimensionType2';
			if ($option['optiontype'] == 'number') 		$op_name = 'Number Field';  
			
			$sql = "INSERT INTO ec_optionitem( ec_optionitem.option_id, ec_optionitem.optionitem_name, ec_optionitem.optionitem_price,  ec_optionitem.optionitem_price_onetime, ec_optionitem.optionitem_price_override, ec_optionitem.optionitem_weight, ec_optionitem.optionitem_weight_onetime, ec_optionitem.optionitem_weight_override, ec_optionitem.optionitem_order, ec_optionitem.optionitem_icon, ec_optionitem.optionitem_initial_value ) VALUES ( %d, %s, '0', '0', '-1', '0', '0', '-1', '1', '', '' )";
			$this->db->query( $this->db->prepare( $sql, $option_id_parent, $op_name ) );
		}
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//addoption
	
	function getproductoptionitems( $optionnum ){
		
		$sql = "SELECT ec_optionitem.*, ec_option.option_name FROM ec_optionitem, ec_option WHERE ec_optionitem.option_id = %d AND ec_option.option_id = ec_optionitem.option_id ORDER BY ec_optionitem.optionitem_order ASC";
		$results = $this->db->get_results( $this->db->prepare( $sql, $optionnum ) );
		
		if( count( $results ) > 0 ){
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}//getproductoptionitems

}//ec_admin_options
?>