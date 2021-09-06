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


class ec_admin_perpage{	
		
	private $db;
	
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

	}
	
	public function _getMethodRoles($methodName){
	   if ($methodName == 'getperpage') return array('admin');
	   else if($methodName == 'updateperpage') return array('admin');
	   else if($methodName == 'deleteperpage') return array('admin');
	   else if($methodName == 'addperpage') return array('admin');
	   else  return null;
	}
	
	function getperpage( ){
		
		$sql = "SELECT ec_perpage.* FROM ec_perpage ORDER BY ec_perpage.perpage ASC";
		$results = $this->db->get_results( $sql );
		
		if( count( $results ) > 0 ){
			return $results;
		}else{
			return array( "noresults" );
		}
	}
	
	function updateperpage( $id, $perpage ){
		
		$sql = "UPDATE ec_perpage SET ec_perpage.perpage='%s' WHERE ec_perpage.perpage_id = %d";
		$this->db->query( $this->db->prepare( $sql, $perpage, $id));
		
		return array( "success" );
		
	}	
	
	function deleteperpage( $id ){
		
		$sql = "DELETE FROM ec_perpage WHERE ec_perpage.perpage_id = %d";
		$success = $this->db->query( $this->db->prepare( $sql, $id ) );
		
		if( $success === FALSE ){
			return array( "error" );
		}else{
			return array( "success" );
		}
		
	}	
	
	function addperpage($perpage) {
		
		$sql = "INSERT INTO ec_perpage( perpage ) VALUES ( %s )";
		$success = $this->db->query( $this->db->prepare( $sql, $perpage ) );
		
		if( $success === FALSE ){
			return array( "error" );
		}else{
			return array( "success" );
		}
		
	}

}
?>