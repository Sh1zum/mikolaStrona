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

class ec_admin_countries{		
	
	private $db;
	
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

	}//ec_admin_countries
	
	public function _getMethodRoles( $methodName ){
		
	   		if( $methodName == 'getcountries' ) 	return array( 'admin' );
	   else if( $methodName == 'updatecountry' ) 	return array( 'admin' );
	   else if( $methodName == 'deletecountry' ) 	return array( 'admin' );
	   else if( $methodName == 'addcountry' ) 		return array( 'admin' );
	   else  										return null;
	
	}//_getMethodRoles
	
	function getcountries( ){
		
		$results = $this->db->get_results( "SELECT ec_country.* FROM ec_country ORDER BY ec_country.sort_order ASC" );
		
		if( count( $results ) > 0 ){
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}//getcountries
	
	function updatecountry( $id, $name, $iso2, $iso3, $vatrate, $sortorder ){
		
		$sql = "UPDATE ec_country SET ec_country.name_cnt = %s, ec_country.iso2_cnt = %s, ec_country.iso3_cnt = %s, ec_country.vat_rate_cnt = %s, ec_country.sort_order = %s WHERE ec_country.id_cnt = %s";
		$this->db->query( $this->db->prepare( $sql, $name, $iso2, $iso3, $vatrate, $sortorder, $id ) );
		
		return array( "success" );
		
	}//updatecountry
	
	function deletecountry( $id ){
		
		$sql = "DELETE FROM ec_country WHERE ec_country.id_cnt = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $id ) );
		
		//also delete any states associated with country id
		$statesql = "DELETE FROM ec_state WHERE ec_state.idcnt_sta = %d";
		$this->db->query( $this->db->prepare( $statesql, $id ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
	
	}//deletecountry
		
	function addcountry( $name, $iso2, $iso3, $vatrate, $sortorder ){

		$sql = "INSERT INTO ec_country( ec_country.name_cnt, ec_country.iso2_cnt, ec_country.iso3_cnt, ec_country.vat_rate_cnt, ec_country.sort_order ) VALUES( %s, %s, %s, %s, %s )";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $name, $iso2, $iso3, $vatrate, $sortorder ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//addcountry

}//close class
?>