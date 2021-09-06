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


class ec_admin_pricepoints{	
		
	private $db;

	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

	}
	
	public function _getMethodRoles($methodName){
	   if ($methodName == 'getpricepoints') return array('admin');
	   else if($methodName == 'updatepricepoint') return array('admin');
	   else if($methodName == 'deletepricepoint') return array('admin');
	   else if($methodName == 'addpricepoint') return array('admin');
	   else  return null;
	}
	
	function getpricepoints( ){
		
		$sql = "SELECT ec_pricepoint.*, ec_pricepoint.pricepoint_order AS `order` FROM ec_pricepoint ORDER BY ec_pricepoint.pricepoint_order ASC";
		$results = $this->db->get_results( $sql );

		if( count( $results ) > 0 ){
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}
	
	function updatepricepoint( $id, $pricepoint ){
		
		$sql = "UPDATE ec_pricepoint SET ec_pricepoint.is_less_than = %s, ec_pricepoint.is_greater_than = %s, ec_pricepoint.low_point = %s, ec_pricepoint.high_point = %s, ec_pricepoint.pricepoint_order = %s WHERE ec_pricepoint.pricepoint_id = %d";
		$this->db->query( $this->db->prepare( $sql, $pricepoint->lessthan, $pricepoint->greaterthan, $pricepoint->lowpoint, $pricepoint->highpoint, $pricepoint->pricepointorder, $id ) );
		
		return array( "success" );
		
	}
	
	function deletepricepoint( $id ){
		
		$sql = "DELETE FROM ec_pricepoint WHERE ec_pricepoint.pricepoint_id = %d";
		$success = $this->db->query( $this->db->prepare( $sql, $id ) );
		
		if( $success === FALSE ){
			return array( "error" );
		}else{
			return array( "success" );
		}
		
	}	
	function addpricepoint( $pricepoint ){
		
		$sql = "INSERT INTO ec_pricepoint( is_less_than, is_greater_than, low_point, high_point, ec_pricepoint.pricepoint_order ) VALUES( %s, %s, %s, %s, %s )";
		$success = $this->db->query( $this->db->prepare( $sql, $pricepoint->lessthan, $pricepoint->greaterthan, $pricepoint->lowpoint, $pricepoint->highpoint, $pricepoint->pricepointorder ) );
		
		if( $success === FALSE ){
			return array( "error" );
		} else {
			return array( "success" );
		}
		
	}

}
?>