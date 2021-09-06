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

class ec_admin_manufacturers{		
	
	private $db;
	
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb; 

	}//ec_admin_manufacturers

	public function _getMethodRoles( $methodName ){

			 if( $methodName == 'getmanufacturers' ) 		return array( 'admin' );
		else if( $methodName == 'getmanufacturerset' ) 		return array( 'admin' );
		else if( $methodName == 'deletemanufacturer' ) 		return array( 'admin' );
		else if( $methodName == 'updatemanufacturer' ) 		return array( 'admin' );
		else if( $methodName == 'addmanufacturer' ) 		return array( 'admin' );
		else  												return null;

	}//_getMethodRoles
	
	function getmanufacturers( ){
		
		$sql = "SELECT ec_manufacturer.*, " . $this->db->prefix . "posts.guid FROM ec_manufacturer LEFT JOIN " . $this->db->prefix . "posts ON " . $this->db->prefix . "posts.ID = ec_manufacturer.post_id  ORDER BY ec_manufacturer.name";
		$results = $this->db->get_results( $sql );
		
		if( count( $results ) > 0 ){
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}//getmanufacturers
	
	function getmanufacturerset( $startrecord, $limit, $orderby, $ordertype, $filter ){

		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_manufacturer.*, " . $this->db->prefix . "posts.guid FROM ec_manufacturer LEFT JOIN " . $this->db->prefix . "posts ON " . $this->db->prefix . "posts.ID = ec_manufacturer.post_id WHERE ec_manufacturer.manufacturer_id != '' " . $filter . " ORDER BY " .  $orderby . " " . $ordertype . " LIMIT " . $startrecord . ", " . $limit;
		$results = $this->db->get_results( $sql );
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS()" );
		
		if( count( $results ) > 0 ){
			$results[0]->totalrows = $totalquery;
			//get base of link slug from GUID
			foreach ($results as $key => $field) {
				//return basename($results[$key]->guid);
				$results[$key]->guid = basename($results[$key]->guid);
			}
			
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}//getmanufacturerset
	
	function deletemanufacturer( $manufacturerid ){
		
		// Delete WordPress Post
		$sql = "SELECT ec_manufacturer.post_id FROM ec_manufacturer WHERE ec_manufacturer.manufacturer_id = %d";
		$post_id = $this->db->get_var( $this->db->prepare( $sql, $manufacturerid ) );
		wp_delete_post( $post_id, true );
		
		// Delete EC DB Items
		$sql = "DELETE FROM ec_manufacturer WHERE ec_manufacturer.manufacturer_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $manufacturerid ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//deletemanufacturer
	
	function updatemanufacturer( $manufacturerid, $manufacturer ){
				
		global $wpdb;
		$manufacturer = (array)$manufacturer;

		
		//original amf file
		// Update WordPress Post
		$sql = "SELECT ec_manufacturer.post_id FROM ec_manufacturer WHERE ec_manufacturer.manufacturer_id = %d";
		$post_slug = preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep(  $manufacturer['link_slug']  ) ) );
		$post_id = $this->db->get_var( $this->db->prepare( $sql, $manufacturerid ) );
		
		//new post coding
		//previous guid
		$previous_guid = $this->db->get_var( $this->db->prepare( "SELECT " . $this->db->prefix . "posts.guid FROM " . $this->db->prefix . "posts WHERE " . $this->db->prefix . "posts.ID = %d", $post_id ) );
		//status
		$status = "publish";
		//post slug	
		$post_slug = preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep(  $manufacturer['link_slug']  ) ) );
		
		//build new GUID with new slug	
		$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
		if( strstr( $store_page, '?' ) )
			$guid = $store_page . '&manufacturer=' . $manufacturerid ;
		else if( substr( $store_page, strlen( $store_page ) - 1 ) == '/' )
			$guid = $store_page . $post_slug;
		else
			$guid = $store_page . '/' . $post_slug;
		
		$guid = strtolower( $guid );
		$post_slug_orig = $post_slug;
		$guid_orig = $guid;
		$guid = $guid . '/';
		
		// If GUID has changed, be sure its not a duplicate.
		if( $previous_guid != $guid ){
			$i=1;
			while( $guid_check = $this->db->get_row( $this->db->prepare( "SELECT " . $this->db->prefix . "posts.guid FROM " . $this->db->prefix . "posts WHERE " . $this->db->prefix . "posts.guid = %s AND " . $this->db->prefix . "posts.ID != %d", $guid, $post_id ) ) ){
				$guid = $guid_orig . '-' . $i . '/';
				$post_slug = $post_slug_orig . '-' . $i;
				$i++;
			}
		}
		
		/* Check the Post Exists, Create if it Doesn't */
		$post_exists = false;
		$post_check = $this->db->get_row( $this->db->prepare( "SELECT " . $this->db->prefix . "posts.guid FROM " . $this->db->prefix . "posts WHERE " . $this->db->prefix . "posts.ID = %d", $post_id) );
		
		if( $post_check || $post_check != null ){
			/* Manually Update Post */
			$this->db->query( $this->db->prepare( "UPDATE " . $this->db->prefix . "posts SET post_content = %s, post_status = %s, post_title = %s, post_name = %s, guid = %s WHERE ID = %d", '[ec_store manufacturerid="' . $manufacturerid  . '"]', $status, utf8_encode( $GLOBALS['language']->convert_text( $manufacturer['manufacturername']  ) ), $post_slug , $guid, $post_id ) );
			
		}else{
			/* Manually Insert Post */
			$this->db->query( $this->db->prepare( "INSERT INTO " . $this->db->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type ) VALUES( %s, %s, %s, %s, %s, %s )", '[ec_store manufacturerid="' . $manufacturerid . '"]', $status, utf8_encode($GLOBALS['language']->convert_text( $manufacturer['manufacturername'] ) ), $post_slug, $guid, "ec_store" ) );
			$post_id = $this->db->insert_id;
			
			$this->db->query( $this->db->prepare( "UPDATE ec_manufacturer SET post_id = %d WHERE manufacturer_id = %d", $post_id, $manufacturerid ) );
		}

		//return $post_id;
		// Update EC DB Item
		$sql = "UPDATE ec_manufacturer SET ec_manufacturer.name = %s, ec_manufacturer.clicks = %s WHERE ec_manufacturer.manufacturer_id = %d";
		
		$this->db->query( $this->db->prepare( $sql, $manufacturer['manufacturername'], $manufacturer['clicks'],  $manufacturerid ) );
		
		return array( "success" );
		
	}//updatemanufacturer
	
	function addmanufacturer( $manufacturer ){
		error_reporting(E_ALL);
ini_set('display_errors', '1');


		
		$manufacturer = (array)$manufacturer;
		
		// Insert EC DB Item
		$sql = "INSERT INTO ec_manufacturer( ec_manufacturer.name, ec_manufacturer.clicks ) VALUES( '%s', '%s' )";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $manufacturer['manufacturername'], $manufacturer['clicks'] ) );
		
		if( $rows_affected ){
			$manufacturerid = $this->db->insert_id;

			//new post coding
			//previous guid
			$previous_guid = $this->db->get_var( $this->db->prepare( "SELECT " . $this->db->prefix . "posts.guid FROM " . $this->db->prefix . "posts WHERE " . $this->db->prefix . "posts.ID = %d", $post_id ) );
			//status
			$status = "publish";
			//post slug	
			$post_slug = preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep(  $manufacturer['manufacturername']   ) ) );
			
			//build new GUID with new slug	
			$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
			if( strstr( $store_page, '?' ) )
				$guid = $store_page . '&manufacturer=' . $manufacturerid ;
			else if( substr( $store_page, strlen( $store_page ) - 1 ) == '/' )
				$guid = $store_page . $post_slug;
			else
				$guid = $store_page . '/' . $post_slug;
			
			$guid = strtolower( $guid );
			$post_slug_orig = $post_slug;
			$guid_orig = $guid;
			$guid = $guid . '/';
			
			
			// If GUID has changed, be sure its not a duplicate.
			if( $previous_guid != $guid ){
				$i=1;
				while( $guid_check = $this->db->get_row( $this->db->prepare( "SELECT " . $this->db->prefix . "posts.guid FROM " . $this->db->prefix . "posts WHERE " . $this->db->prefix . "posts.guid = %s AND " . $this->db->prefix . "posts.ID != %d", $guid, $post_id ) ) ){
					$guid = $guid_orig . '-' . $i . '/';
					$post_slug = $post_slug_orig . '-' . $i;
					$i++;
				}
			}
			
			/* Manually Insert Post */
			$this->db->query( $this->db->prepare( "INSERT INTO " . $this->db->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type ) VALUES( %s, %s, %s, %s, %s, %s )", '[ec_store manufacturerid="' . $manufacturerid . '"]', $status, utf8_encode($GLOBALS['language']->convert_text( $manufacturer['manufacturername'] ) ), $post_slug, $guid, "ec_store" ) );
			$post_id = $this->db->insert_id;
			
			$this->db->query( $this->db->prepare( "UPDATE ec_manufacturer SET post_id = %d WHERE manufacturer_id = %d", $post_id, $manufacturerid ) );
			
			
			// Update post_id for EC DB Item
			$db = new ec_db( );
			$db->update_manufacturer_post_id( $manufacturerid, $post_id );
		
			return array( "success" );
		
		}else{
			return array( "error" );
		}
		
	}//addmanufacturer

}//ec_admin_manufacturers
?>