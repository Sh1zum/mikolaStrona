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


class ec_admin_pricetiers{
	
	private $db;	
	
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

	}	
	
	public function _getMethodRoles($methodName){
	   if ($methodName == 'getpricetier') return array('admin');
	   else if($methodName == 'updatepricetier') return array('admin');
	   else if($methodName == 'deletepricetier') return array('admin');
	   else if($methodName == 'addpricetier') return array('admin');
	   else  return null;
	}	
	
	function getpricetier( $productid ){
		
		$sql = "SELECT ec_pricetier.* FROM ec_pricetier WHERE ec_pricetier.product_id = %d ORDER BY ec_pricetier.quantity ASC";
		$results = $this->db->get_results( $this->db->prepare( $sql, $productid ) );
		
		if( count( $results ) > 0 ){
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}
	
	function updatepricetier( $id, $price, $quantity ){
		
		$sql = "UPDATE ec_pricetier SET ec_pricetier.price = %s, ec_pricetier.quantity = %s WHERE ec_pricetier.pricetier_id = %d";
		$this->db->query( $this->db->prepare( $sql, $price, $quantity, $id ) );
		
		return array( "success" );
		
	}
	
	function deletepricetier( $tierid, $productid ){
		
		$sql = "DELETE FROM ec_pricetier WHERE ec_pricetier.pricetier_id = %d";
		$success = $this->db->query( $this->db->prepare( $sql, $tierid ) );
		
		if( $success === FALSE ){
			return array( "error" );
		}else{
			return array( $productid );
		}
		
	}
	
	function addpricetier( $price, $quantity, $productid ){
		
		$sql = "INSERT INTO ec_pricetier( price, quantity, product_id ) VALUES ( %s, %s, %d )";
		$success = $this->db->query( $this->db->prepare( $sql, $price, $quantity, $productid ) );
		
		if( $success === FALSE ){
			return array( "error" );
		}else{
			return array( $productid );
		}
		
	}

}
?>