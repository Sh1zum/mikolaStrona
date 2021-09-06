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

class ec_admin_states{
	
	private $db;

	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;
	
	}
	
	//secure all of the services for logged in authenticated users only	
	public function _getMethodRoles( $methodName ){
		if( 	 $methodName == 'getstates') 	return array('admin');
		else if( $methodName == 'updatestate') 	return array('admin');
		else if( $methodName == 'deletestate') 	return array('admin');
		else if( $methodName == 'addstate') 	return array('admin');
		else 									return NULL;
	}
	
	function getstates( ){
		
		$sql = "SELECT ec_state.* FROM ec_state ORDER BY ec_state.sort_order ASC";
		$results = $this->db->get_results( $sql );
		
		if( !empty( $results ) ){
			foreach( $results as $row ){
				$returnArray[] = $row;
			}
			return $returnArray;
		}else{
			return array( "noresults" );
		}
		
	}
	
	function updatestate( $id, $countryid, $iso2, $name, $sortorder ){
		
		$sql = "UPDATE ec_state SET  ec_state.name_sta='%s', ec_state.code_sta='%s', ec_state.idcnt_sta='%s', ec_state.sort_order='%s' WHERE ec_state.id_sta = '%s'";
		$results = $this->db->get_results( $this->db->prepare( $sql, $name, $iso2, $countryid, $sortorder,  $id) );
		
		if( count( $results ) > 0 ){
			foreach( $results as $row ){
			  $returnArray[] = $row;
			}
			return $returnArray;
		}else{
			return array( "noresults" );
		}
		
	}	
	
	function deletestate( $id ){
		
		$sql = "DELETE FROM ec_state WHERE ec_state.id_sta = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $id ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}	
	
	function addstate( $countryid, $iso2, $name, $sortorder ){
		
		$sql = "INSERT INTO ec_state( ec_state.name_sta, ec_state.code_sta, ec_state.idcnt_sta, ec_state.sort_order) VALUES( '%s', '%s', '%s', '%s' )";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $name, $iso2, $countryid, $sortorder ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
}//close class
?>