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

class ec_admin_affiliates{		
	
	private $db;
	
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

	}
		
	public function _getMethodRoles( $methodName ){
	   		if( $methodName == 'getaffiliates' ) 			return array( 'admin' );
	   else if( $methodName == 'deleteaffiliate' ) 			return array( 'admin' );
	   else if( $methodName == 'updateaffiliate' ) 			return array( 'admin' );
	   else if( $methodName == 'addaffiliate' ) 			return array( 'admin' );
	   else if( $methodName == 'getaffiliatelist' ) 		return array( 'admin' );
	   else if( $methodName == 'getaffiliateproducts' ) 	return array( 'admin' );
	   else if( $methodName == 'getattachedaffiliates' ) 	return array( 'admin' );
	   else if( $methodName == 'getaffiliatesfulllist' ) 	return array( 'admin' );
	   else if( $methodName == 'deleteaffiliatefromrule' ) 	return array( 'admin' );
	   else if( $methodName == 'addaffiliatetorule' ) 		return array( 'admin' );
	   else if( $methodName == 'getattachedaffiliateproduct' ) 	return array( 'admin' );
	   else if( $methodName == 'getaffiliateproductsfulllist' ) return array( 'admin' );
	   else if( $methodName == 'deleteproductfromrule' ) 	return array( 'admin' );
	   else if( $methodName == 'addproducttorule' ) 		return array( 'admin' );
	   else  												return null;
	}//_getMethodRoles
	
	

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
//affiliate rule
/////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	function getaffiliates( $startrecord, $limit, $orderby, $ordertype, $filter ){

		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_affiliate_rule.* FROM ec_affiliate_rule WHERE ec_affiliate_rule.affiliate_rule_id != '' " . $filter . " ORDER BY " . $orderby . " " . $ordertype . " LIMIT " . $startrecord . ", " . $limit;
		$results = $this->db->get_results( $sql );
		
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( count( $results ) > 0 ){
			$results[0]->totalrows = $totalquery;
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}//getaffiliates

	
	function deleteaffiliate( $affiliate_id ){
		// Delete affiliate rule
		$sql = "DELETE FROM ec_affiliate_rule WHERE ec_affiliate_rule.affiliate_rule_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $affiliate_id ) );
		
		// Delete affiliate rule products link
		$sql = "DELETE FROM ec_affiliate_rule_to_product WHERE ec_affiliate_rule_to_product.affiliate_rule_id = %d";
		$this->db->query( $this->db->prepare( $sql, $affiliate_id ) );
		
		// Delete affiliate rule affilates link
		$sql = "DELETE FROM ec_affiliate_rule_to_affiliate WHERE ec_affiliate_rule_to_affiliate.affiliate_rule_id = %d";
		$this->db->query( $this->db->prepare( $sql, $affiliate_id ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//deleteaffiliate
	
	function updateaffiliate( $affiliate_rule_id, $affiliate_rule ){
		
		$sql = "UPDATE ec_affiliate_rule SET ec_affiliate_rule.rule_name = %s, ec_affiliate_rule.rule_type = %s, ec_affiliate_rule.rule_amount = %s, ec_affiliate_rule.rule_limit = %s, ec_affiliate_rule.rule_active = %s, ec_affiliate_rule.rule_recurring = %s WHERE ec_affiliate_rule.affiliate_rule_id = %s";
		
		$preparedsql = $this->db->prepare( $sql, $affiliate_rule->rule_name, $affiliate_rule->rule_type, $affiliate_rule->rule_amount, $affiliate_rule->rule_limit, $affiliate_rule->rule_active, $affiliate_rule->rule_recurring, $affiliate_rule_id);
		
		$success = $this->db->query( $preparedsql );
		
		if( $success === FALSE ){
			return array( "error" );
		}else{
			return array( "success" );
		}
		
	}//updateaffiliate
	
	function addaffiliate( $affiliate_rule ){
		
		$sql = "INSERT INTO ec_affiliate_rule( rule_name, rule_type, rule_amount, rule_limit, rule_active, rule_recurring ) VALUES( %s, %s, %s, %s, %s, %s )";
		$success = $this->db->query( $this->db->prepare( $sql, $affiliate_rule->rule_name, $affiliate_rule->rule_type, $affiliate_rule->rule_amount, $affiliate_rule->rule_limit, $affiliate_rule->rule_active, $affiliate_rule->rule_recurring) );
		$affiliate_rule_id = $this->db->insert_id;
		
		if( $success === FALSE ){
			return array( "error" );
		}else{
			return array( $affiliate_rule_id );
		}
	
	}//addaffiliate




/////////////////////////////////////////////////////////////////////////////////////////////////////////////
//affiliate rule to affiliate
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function getattachedaffiliates($affiliate_rule_id){
		  
		//need a list of ec_affiliate_rule_to_affiliate + affiliate first/last name
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
		ec_affiliate_rule_to_affiliate.*,
		".$this->db->prefix."users.display_name, 
		".$this->db->prefix."users.user_email 
		FROM ec_affiliate_rule_to_affiliate 
		LEFT JOIN ".$this->db->prefix."affiliate_wp_affiliates ON ".$this->db->prefix."affiliate_wp_affiliates.affiliate_id = ec_affiliate_rule_to_affiliate.affiliate_id 
		LEFT JOIN ".$this->db->prefix."users ON ".$this->db->prefix."users.ID = ".$this->db->prefix."affiliate_wp_affiliates.user_id
		WHERE ec_affiliate_rule_to_affiliate.affiliate_rule_id != '' 
		AND ec_affiliate_rule_to_affiliate.affiliate_rule_id = " . $affiliate_rule_id . " 
		ORDER BY ".$this->db->prefix."users.display_name ASC";
	
		//return $sql;
		
		$results = $this->db->get_results( $sql );

		if( count( $results) > 0 ){
			return $results;
		} else {
			return array( "noresults" );
		}
		
	}//getattachedaffiliates
	
	function getaffiliatesfulllist( $affiliate_rule_id){
		  
		//need a list of affiliate ID's and their first/last names for identification from affiliate program
		$sql = "SELECT 
		".$this->db->prefix."users.display_name, 
		".$this->db->prefix."users.user_email, 
		".$this->db->prefix."affiliate_wp_affiliates.affiliate_id, 
		ec_affiliate_rule_to_affiliate.affiliate_rule_id
		FROM ".$this->db->prefix."affiliate_wp_affiliates
		LEFT JOIN ".$this->db->prefix."users ON ".$this->db->prefix."users.ID = ".$this->db->prefix."affiliate_wp_affiliates.user_id 		LEFT JOIN ec_affiliate_rule_to_affiliate ON (".$this->db->prefix."affiliate_wp_affiliates.affiliate_id = ec_affiliate_rule_to_affiliate.affiliate_id AND ec_affiliate_rule_to_affiliate.affiliate_rule_id = '".$affiliate_rule_id."')
		ORDER BY ".$this->db->prefix."users.display_name ASC";
		
		//return $sql;
		
		$results = $this->db->get_results( $sql );

		
		if( count( $results ) > 0 ){
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}//getaffiliatesfulllist
	
	function deleteaffiliatefromrule( $affiliate_rule_id, $affiliate_id ){
		  
		$sql = "DELETE FROM ec_affiliate_rule_to_affiliate WHERE ec_affiliate_rule_to_affiliate.affiliate_rule_id = %d AND ec_affiliate_rule_to_affiliate.affiliate_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql,  $affiliate_rule_id, $affiliate_id  ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//deleteaffiliatefromrule

	function addaffiliatetorule( $affiliate_rule_id, $affiliate_id ){
		
		$sql = "Insert into ec_affiliate_rule_to_affiliate( affiliate_rule_id, affiliate_id ) values( %d, %d )";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $affiliate_rule_id, $affiliate_id ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//addaffiliatetorule
	
	
	
	
	
	
	
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
//affiliate rule to product
/////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	function getattachedaffiliateproduct($affiliate_rule_id){
		  
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_affiliate_rule_to_product.*, ec_product.title, ec_product.product_id FROM ec_affiliate_rule_to_product LEFT JOIN ec_product ON ec_product.product_id = ec_affiliate_rule_to_product.product_id  WHERE ec_affiliate_rule_to_product.affiliate_rule_id != '' AND ec_affiliate_rule_to_product.affiliate_rule_id = " . $affiliate_rule_id . " ORDER BY ec_product.title ASC";
		$results = $this->db->get_results( $sql );
		
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( count( $results) > 0 ){
			$results[0]->totalrows = $totalquery;
			return $results;
		} else {
			return array( "noresults" );
		}
		
	}//getattachedaffiliateproduct
	
	
	function getaffiliateproductsfulllist( $affiliate_rule_id, $startrecord, $limit, $orderby, $ordertype, $filter  ){
		
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
		ec_product.*, 
		ec_affiliate_rule_to_product.rule_to_product_id, 
		ec_affiliate_rule_to_product.product_id as affiliate_product_id,
		ec_affiliate_rule_to_product.affiliate_rule_id,
		ec_manufacturer.name AS manufacturer 
		FROM ec_product 
		LEFT JOIN ec_affiliate_rule_to_product ON (ec_product.product_id = ec_affiliate_rule_to_product.product_id AND ec_affiliate_rule_to_product.affiliate_rule_id = '".$affiliate_rule_id."') 
		LEFT JOIN ec_manufacturer ON (ec_product.manufacturer_id = ec_manufacturer.manufacturer_id) 
		WHERE ec_product.product_id != '' " . $filter . " 
		ORDER BY ".  $orderby ." ".  $ordertype . " 
		LIMIT ".  $startrecord . ", ". $limit;

		
		$results = $this->db->get_results( $sql );
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS()" );
		
		if( count( $results ) > 0 ){
			$results[0]->totalrows = $totalquery;
			return $results;
		}else{
			return array( "noresults" );
		}
		
		
		
	}//getaffiliateproductsfulllist
	
	function deleteproductfromrule( $affiliate_rule_id, $product_id ){
		  
		$sql = "DELETE FROM ec_affiliate_rule_to_product WHERE ec_affiliate_rule_to_product.affiliate_rule_id = %d AND ec_affiliate_rule_to_product.product_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql,  $affiliate_rule_id, $product_id  ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//deleteaffiliatefromrule

	function addproducttorule( $affiliate_rule_id, $product_id ){
		
		$sql = "Insert into ec_affiliate_rule_to_product( affiliate_rule_id, product_id ) values( %d, %d )";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $affiliate_rule_id, $product_id ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//addaffiliatetorule
	
}//ec_admin_affiliates
?>