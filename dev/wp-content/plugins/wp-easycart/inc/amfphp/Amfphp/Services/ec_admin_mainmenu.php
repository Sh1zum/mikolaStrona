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

class ec_admin_mainmenu{		
	
	private $db;
	
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

	}//ec_admin_mainmenu	
	
	public function _getMethodRoles( $methodName ){

			 if( $methodName == 'getmenulevel1' ) 		return array( 'admin' );
		else if( $methodName == 'getmenulevel1set' ) 	return array( 'admin' );
		else if( $methodName == 'deletemenulevel1' ) 	return array( 'admin' );
		else if( $methodName == 'updatemenulevel1' ) 	return array( 'admin' );
		else if( $methodName == 'addmenulevel1' ) 		return array( 'admin' );
		else if( $methodName == 'getmenulevel2' ) 		return array( 'admin' );
		else if( $methodName == 'getmenulevel2set' ) 	return array( 'admin' );
		else if( $methodName == 'deletemenulevel2' ) 	return array( 'admin' );
		else if( $methodName == 'updatemenulevel2' ) 	return array( 'admin' );
		else if( $methodName == 'addmenulevel2' ) 		return array( 'admin' );
		else if( $methodName == 'getmenulevel3' ) 		return array( 'admin' );
		else if( $methodName == 'getmenulevel3set' ) 	return array( 'admin' );
		else if( $methodName == 'deletemenulevel3' ) 	return array( 'admin' );
		else if( $methodName == 'updatemenulevel3' ) 	return array( 'admin' );
		else if( $methodName == 'addmenulevel3' ) 		return array( 'admin' );
		else  											return null;

	}//_getMethodRoles
	
	function getmenulevel1( ){
		$sql = "SELECT ec_menulevel1.*, ec_menulevel1.menu_order AS `order`, " . $this->db->prefix . "posts.guid FROM ec_menulevel1 LEFT JOIN " . $this->db->prefix . "posts ON " . $this->db->prefix . "posts.ID = ec_menulevel1.post_id ORDER BY ec_menulevel1.menu_order ASC";
		$results = $this->db->get_results( $sql );
		
		if( count( $results ) > 0 ){
			return $results;
		}else{
			return array( "noresults" );
		}
	}//getmenulevel1
	
	function getmenulevel1set( $startrecord, $limit, $orderby, $ordertype, $filter ){
		$orderby = str_replace( "ec_menulevel1.order", "ec_menulevel1.menu_order", $orderby );
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_menulevel1.*, ec_menulevel1.menu_order AS `order`, " . $this->db->prefix . "posts.guid FROM ec_menulevel1 LEFT JOIN " . $this->db->prefix . "posts ON " . $this->db->prefix . "posts.ID = ec_menulevel1.post_id  WHERE ec_menulevel1.menulevel1_id != '' " . $filter . " ORDER BY " . $orderby ." " . $ordertype . " LIMIT " . $startrecord . ", " . $limit;
		//return $sql;
		$results = $this->db->get_results( $sql );
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
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
	}//getmenulevel1set
	
	function deletemenulevel1( $keyfield ){
		
		// Get Level 2 Items
		$sql = "SELECT ec_menulevel2.menulevel2_id, ec_menulevel2.post_id FROM ec_menulevel2 WHERE ec_menulevel2.menulevel1_id = %d";
		$level2_items = $this->db->get_results( $this->db->prepare( $sql, $keyfield ) );
		
		foreach( $level2_items as $level2_item ){
			
			$sql = "SELECT ec_menulevel3.menulevel3_id, ec_menulevel3.post_id FROM ec_menulevel3 WHERE ec_menulevel3.menulevel2_id = %d";
			$level3_items = $this->db->get_results( $this->db->prepare( $sql, $level2_item->menulevel2_id ) );
			
			// Delete Level 3 Posts from WordPress
			foreach( $level3_items as $level3_item ){
				wp_delete_post( $level3_item->post_id, true );
			}
			
			// Delete all Level 3 DB Items
			$sql = "DELETE FROM ec_menulevel3 WHERE ec_menulevel3.menulevel2_id = %d";
			$this->db->query( $this->db->prepare( $sql, $level2_item->menulevel2_id ) );
			
			// Delete Level 2 Post
			wp_delete_post( $level2_item->post_id, true );
		}
		
		// Delete Level 2 DB Items
		$sql = "DELETE FROM ec_menulevel2 WHERE ec_menulevel2.menulevel1_id = %d";
		$this->db->query( $this->db->prepare( $sql, $keyfield ) );
		
		// Get Level 1 Post ID
		$sql = "SELECT ec_menulevel1.post_id FROM ec_menulevel1 WHERE menulevel1_id = %d";
		$post_id = $this->db->get_var( $this->db->prepare( $sql, $keyfield ) );
		
		// Delete Level 1 Post
		wp_delete_post( $post_id, true );
		
		// Delete Level 1 DB Item
		$sql = "DELETE FROM ec_menulevel1 WHERE ec_menulevel1.menulevel1_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $keyfield ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//deletemenulevel1
	
	function updatemenulevel1( $keyfield, $menulevel1 ){
		
		$menulevel1 = (array)$menulevel1;
		
		//get post ID
		$sql = "SELECT ec_menulevel1.post_id FROM ec_menulevel1 WHERE ec_menulevel1.menulevel1_id = %d";
		$post_id = $this->db->get_var( $this->db->prepare( $sql, $keyfield ) );
		
		//original amf file
		// Update WordPress Post
		$sql = "SELECT ec_menulevel1.post_id FROM ec_menulevel1 WHERE ec_menulevel1.menulevel1_id = %d";
		$post_slug = preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep(  $menulevel1['link_slug']  ) ) );
		$post_id = $this->db->get_var( $this->db->prepare( $sql, $keyfield ) );
		
		//new post coding
		//previous guid
		$previous_guid = $this->db->get_var( $this->db->prepare( "SELECT " . $this->db->prefix . "posts.guid FROM " . $this->db->prefix . "posts WHERE " . $this->db->prefix . "posts.ID = %d", $post_id ) );
		//status
		$status = "publish";
		//post slug	
		$post_slug = preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep(  $menulevel1['link_slug']  ) ) );
		
		//build new GUID with new slug	
		$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
		if( strstr( $store_page, '?' ) )
			$guid = $store_page . '&menuid=' . $keyfield ;
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
			$this->db->query( $this->db->prepare( "UPDATE " . $this->db->prefix . "posts SET post_content = %s, post_status = %s, post_title = %s, post_name = %s, guid = %s WHERE ID = %d", '[ec_store menuid="' . $keyfield  . '"]', $status, utf8_encode( $GLOBALS['language']->convert_text( $menulevel1['menuname']  ) ), $post_slug , $guid, $post_id ) );
			
		}else{
			/* Manually Insert Post */
			$this->db->query( $this->db->prepare( "INSERT INTO " . $this->db->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type ) VALUES( %s, %s, %s, %s, %s, %s )", '[ec_store menuid="' . $keyfield . '"]', $status, utf8_encode($GLOBALS['language']->convert_text( $menulevel1['menuname'] ) ), $post_slug, $guid, "ec_store" ) );
			$post_id = $this->db->insert_id;
			
			$this->db->query( $this->db->prepare( "UPDATE ec_menulevel1 SET post_id = %d WHERE menulevel1_id = %d", $post_id, $keyfield ) );
		}
		
		// Update GUID
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "posts SET " . $wpdb->prefix . "posts.guid = %s WHERE " . $wpdb->prefix . "posts.ID = %d", get_permalink( $post_id ), $post_id ) );
		
		//Update EC DB Fields
		$sql = "UPDATE ec_menulevel1 SET ec_menulevel1.name = %s, ec_menulevel1.clicks = %s, ec_menulevel1.menu_order = %s, ec_menulevel1.seo_keywords = %s, ec_menulevel1.seo_description = %s, ec_menulevel1.banner_image = %s WHERE ec_menulevel1.menulevel1_id = %d";
		$this->db->query( $this->db->prepare( $sql, $menulevel1['menuname'], $menulevel1['clicks'], $menulevel1['menu1order'], $menulevel1['seokeywords'], $menulevel1['seodescription'], $menulevel1['bannerimage'], $keyfield ) );
		
		return array( "success" );
	
	}//updatemenulevel1
	
	function addmenulevel1( $menulevel1 ){
		  
		$menulevel1 = (array)$menulevel1;
		
		// Insert EC DB Value
		$sql = "INSERT INTO ec_menulevel1( ec_menulevel1.name, ec_menulevel1.clicks, ec_menulevel1.menu_order, ec_menulevel1.seo_keywords, ec_menulevel1.seo_description, ec_menulevel1.banner_image ) VALUES( %s, %s, %s, %s, %s, %s )";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $menulevel1['menuname'], $menulevel1['clicks'], $menulevel1['menu1order'], 				$menulevel1['seokeywords'], $menulevel1['seodescription'], $menulevel1['bannerimage'] ) );
		
		$menulevel1_id = $this->db->insert_id;
		
		//new post coding
		//previous guid
		$previous_guid = $this->db->get_var( $this->db->prepare( "SELECT " . $this->db->prefix . "posts.guid FROM " . $this->db->prefix . "posts WHERE " . $this->db->prefix . "posts.ID = %d", $post_id ) );
		//status
		$status = "publish";
		//post slug	
		$post_slug = preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep(  $menulevel1['menuname']  ) ) );
		
		//build new GUID with new slug	
		$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
		if( strstr( $store_page, '?' ) )
			$guid = $store_page . '&menuid=' . $menulevel1_id ;
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
		$this->db->query( $this->db->prepare( "INSERT INTO " . $this->db->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type ) VALUES( %s, %s, %s, %s, %s, %s )", '[ec_store menuid="' . $menulevel1_id . '"]', $status, utf8_encode($GLOBALS['language']->convert_text( $menulevel1['menuname'] ) ), $post_slug, $guid, "ec_store" ) );
		$post_id = $this->db->insert_id;
			
		$this->db->query( $this->db->prepare( "UPDATE ec_menulevel1 SET post_id = %d WHERE menulevel1_id = %d", $post_id, $menulevel1_id ) );
		
		// Update the post_id value
		$db = new ec_db( );
		$db->update_menu_post_id( $menu_id, $post_id );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
	}//addmenulevel1

	function getmenulevel2( ){
		$sql = "SELECT ec_menulevel2.*, ec_menulevel2.menu_order AS `order`, " . $this->db->prefix . "posts.guid FROM ec_menulevel2 LEFT JOIN " . $this->db->prefix . "posts ON " . $this->db->prefix . "posts.ID = ec_menulevel2.post_id ORDER BY ec_menulevel2.menu_order ASC";
		$results = $this->db->get_results( $sql );
		
		if( count( $results ) > 0 ){
			return $results;
		}else{
			return array( "noresults" );
		}
	}//getmenulevel2
	
	function getmenulevel2set( $startrecord, $limit, $orderby, $ordertype, $filter, $menuparentid ){
		$orderby = str_replace( "ec_menulevel2.order", "ec_menulevel2.menu_order", $orderby );
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_menulevel2.*, ec_menulevel2.menu_order AS `order`, " . $this->db->prefix . "posts.guid FROM ec_menulevel2 LEFT JOIN " . $this->db->prefix . "posts ON " . $this->db->prefix . "posts.ID = ec_menulevel2.post_id  WHERE ec_menulevel2.menulevel2_id != '' AND ec_menulevel2.menulevel1_id=" . $menuparentid . " " . $filter . " ORDER BY " . $orderby ." " . $ordertype . " LIMIT " . $startrecord . ", " . $limit;
		//return $sql;
		$results = $this->db->get_results( $sql );
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
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
	}//getmenulevel1set
	
	function deletemenulevel2( $keyfield ){
		
		//Get Level 3 Menu Items
		$sql = "SELECT ec_menulevel3.menulevel3_id, ec_menulevel3.post_id FROM ec_menulevel3 WHERE ec_menulevel3.menulevel2_id = %d";
		$level3_items = $this->db->get_results( $this->db->prepare( $sql, $keyfield ) );
		
		// Delete Level 3 WordPress Posts
		foreach( $level3_items as $level3_item ){
			wp_delete_post( $level3_item->post_id, true );
		}
		
		// Delete Level 3 EC DB Items
		$sql = "DELETE FROM ec_menulevel3 WHERE ec_menulevel3.ec_menulevel2_id = %d";
		$this->db->query( $this->db->prepare( $sql, $keyfield ) );
		
		// Get Level 2 Post ID
		$sql = "SELECT ec_menulevel2.post_id FROM ec_menulevel2 WHERE ec_menulevel2.menulevel2_id = %d";
		$post_id = $this->db->get_var( $this->db->prepare( $sql, $keyfield ) );
		
		// Delete Level 2 WordPress Post
		wp_delete_post( $post_id, true );
		
		// Delete Level 2 EC DB Items
		$sql = "DELETE FROM ec_menulevel2 WHERE ec_menulevel2.menulevel2_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $keyfield ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//deletemenulevel2
	
	function updatemenulevel2( $keyfield, $menulevel2 ){
		
		$menulevel2 = (array)$menulevel2;
		
		//get post ID
		$sql = "SELECT ec_menulevel2.post_id FROM ec_menulevel2 WHERE ec_menulevel2.menulevel2_id = %d";
		$post_id = $this->db->get_var( $this->db->prepare( $sql, $keyfield ) );
		
		//new post coding
		//previous guid
		$previous_guid = $this->db->get_var( $this->db->prepare( "SELECT " . $this->db->prefix . "posts.guid FROM " . $this->db->prefix . "posts WHERE " . $this->db->prefix . "posts.ID = %d", $post_id ) );
		//status
		$status = "publish";
		//post slug	
		$post_slug = preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep(  $menulevel2['link_slug']  ) ) );
		
		//build new GUID with new slug	
		$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
		if( strstr( $store_page, '?' ) )
			$guid = $store_page . '&submenuid=' . $keyfield ;
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
			$this->db->query( $this->db->prepare( "UPDATE " . $this->db->prefix . "posts SET post_content = %s, post_status = %s, post_title = %s, post_name = %s, guid = %s WHERE ID = %d", '[ec_store submenuid="' . $keyfield  . '"]', $status, utf8_encode( $GLOBALS['language']->convert_text( $menulevel2['menuname']  ) ), $post_slug , $guid, $post_id ) );
			
		}else{
			/* Manually Insert Post */
			$this->db->query( $this->db->prepare( "INSERT INTO " . $this->db->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type ) VALUES( %s, %s, %s, %s, %s, %s )", '[ec_store submenuid="' . $keyfield . '"]', $status, utf8_encode($GLOBALS['language']->convert_text( $menulevel2['menuname'] ) ), $post_slug, $guid, "ec_store" ) );
			$post_id = $this->db->insert_id;
			
			$this->db->query( $this->db->prepare( "UPDATE ec_menulevel2 SET post_id = %d WHERE menulevel2_id = %d", $post_id, $keyfield ) );
		}
		
		// Update GUID
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "posts SET " . $wpdb->prefix . "posts.guid = %s WHERE " . $wpdb->prefix . "posts.ID = %d", get_permalink( $post_id ), $post_id ) );
		
		// Update EC DB Item
		$sql = "UPDATE ec_menulevel2 SET ec_menulevel2.menulevel2_id = %d, ec_menulevel2.name = %s, ec_menulevel2.clicks = %s, ec_menulevel2.menu_order = %s, ec_menulevel2.seo_keywords = %s, ec_menulevel2.seo_description = %s, ec_menulevel2.banner_image = %s WHERE ec_menulevel2.menulevel2_id = %d";
		$this->db->query( $this->db->prepare( $sql, $menulevel2['menuparentid'], $menulevel2['menuname'], $menulevel2['clicks'], $menulevel2['menu2order'], $menulevel2['seokeywords'], $menulevel2['seodescription'], $menulevel2['bannerimage'], $keyfield ) );
		
		return array( "success" );
		
	}//updatemenulevel2
	
	function addmenulevel2( $menulevel2 ){
		  
		$menulevel2 = (array)$menulevel2;
		
		$sql = "INSERT INTO ec_menulevel2( ec_menulevel2.menulevel1_id, ec_menulevel2.name, ec_menulevel2.clicks, ec_menulevel2.menu_order, ec_menulevel2.seo_keywords, ec_menulevel2.seo_description, ec_menulevel2.banner_image ) VALUES( %d, %s, %s, %s, %s, %s, %s )";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $menulevel2['menuparentid'], $menulevel2['menuname'], $menulevel2['clicks'], $menulevel2['menu2order'], $menulevel2['seokeywords'], $menulevel2['seodescription'], $menulevel2['bannerimage'] ) );
		
		$menulevel2_id = $this->db->insert_id;
		
		//new post coding
		//previous guid
		$previous_guid = $this->db->get_var( $this->db->prepare( "SELECT " . $this->db->prefix . "posts.guid FROM " . $this->db->prefix . "posts WHERE " . $this->db->prefix . "posts.ID = %d", $post_id ) );
		//status
		$status = "publish";
		//post slug	
		$post_slug = preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep(  $menulevel2['menuname']  ) ) );
		
		//build new GUID with new slug	
		$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
		if( strstr( $store_page, '?' ) )
			$guid = $store_page . '&submenuid=' . $menulevel2_id ;
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
		$this->db->query( $this->db->prepare( "INSERT INTO " . $this->db->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type ) VALUES( %s, %s, %s, %s, %s, %s )", '[ec_store submenuid="' . $menulevel2_id . '"]', $status, utf8_encode($GLOBALS['language']->convert_text( $menulevel2['menuname'] ) ), $post_slug, $guid, "ec_store" ) );
		$post_id = $this->db->insert_id;
			
		$this->db->query( $this->db->prepare( "UPDATE ec_menulevel2 SET post_id = %d WHERE menulevel2_id = %d", $post_id, $menulevel2_id ) );
		
		// Update the EC DB entry
		$db = new ec_db( );
		$db->update_submenu_post_id( $submenu_id, $post_id );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
	}//addmenulevel2
	
	function getmenulevel3( ){
		$sql = "SELECT ec_menulevel3.*, ec_menulevel3.menu_order AS `order`, " . $this->db->prefix . "posts.guid FROM ec_menulevel3 LEFT JOIN " . $this->db->prefix . "posts ON " . $this->db->prefix . "posts.ID = ec_menulevel3.post_id ORDER BY ec_menulevel3.menu_order ASC";
		$results = $this->db->get_results( $sql );
		
		if( count( $results ) > 0 ){
			return $results;
		}else{
			return array( "noresults" );
		}
	}//getmenulevel3
	
	function getmenulevel3set( $startrecord, $limit, $orderby, $ordertype, $filter, $menuparentid ){
		$orderby = str_replace( "ec_menulevel3.order", "ec_menulevel3.menu_order", $orderby );
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_menulevel3.*, ec_menulevel3.menu_order AS `order`, " . $this->db->prefix . "posts.guid FROM ec_menulevel3 LEFT JOIN " . $this->db->prefix . "posts ON " . $this->db->prefix . "posts.ID = ec_menulevel3.post_id  WHERE ec_menulevel3.menulevel3_id != '' AND ec_menulevel3.menulevel2_id = " . $menuparentid . "  " . $filter . " ORDER BY " . $orderby ." " . $ordertype . " LIMIT " . $startrecord . ", " . $limit;
		//return $sql;
		$results = $this->db->get_results( $sql );
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
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
	}//getmenulevel3set
	
	function deletemenulevel3( $keyfield ){
		
		// Get WordPress Post ID
		$sql = "SELECT ec_menulevel3.post_id FROM ec_menulevel3 WHERE ec_menulevel3.menulevel3_id = %d";
		$post_id = $this->db->get_var( $this->db->prepare( $sql, $keyfield ) );
		
		// Delete WordPress Post
		wp_delete_post( $post_id, true );
		
		// Delete EC DB Item
		$sql = "DELETE FROM ec_menulevel3 WHERE ec_menulevel3.menulevel3_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $keyfield ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//deletemenulevel3
	
	function updatemenulevel3( $keyfield, $menulevel3 ){
		
		$menulevel3 = (array)$menulevel3;
		
		//get post ID
		$sql = "SELECT ec_menulevel3.post_id FROM ec_menulevel3 WHERE ec_menulevel3.menulevel3_id = %d";
		$post_id = $this->db->get_var( $this->db->prepare( $sql, $keyfield ) );
		
		//new post coding
		//previous guid
		$previous_guid = $this->db->get_var( $this->db->prepare( "SELECT " . $this->db->prefix . "posts.guid FROM " . $this->db->prefix . "posts WHERE " . $this->db->prefix . "posts.ID = %d", $post_id ) );
		//status
		$status = "publish";
		//post slug	
		$post_slug = preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep(  $menulevel3['link_slug']  ) ) );
		
		//build new GUID with new slug	
		$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
		if( strstr( $store_page, '?' ) )
			$guid = $store_page . '&subsubmenuid=' . $keyfield ;
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
			$this->db->query( $this->db->prepare( "UPDATE " . $this->db->prefix . "posts SET post_content = %s, post_status = %s, post_title = %s, post_name = %s, guid = %s WHERE ID = %d", '[ec_store subsubmenuid="' . $keyfield  . '"]', $status, utf8_encode( $GLOBALS['language']->convert_text( $menulevel3['menuname']  ) ), $post_slug , $guid, $post_id ) );
			
		}else{
			/* Manually Insert Post */
			$this->db->query( $this->db->prepare( "INSERT INTO " . $this->db->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type ) VALUES( %s, %s, %s, %s, %s, %s )", '[ec_store subsubmenuid="' . $keyfield . '"]', $status, utf8_encode($GLOBALS['language']->convert_text( $menulevel3['menuname'] ) ), $post_slug, $guid, "ec_store" ) );
			$post_id = $this->db->insert_id;
			
			$this->db->query( $this->db->prepare( "UPDATE ec_menulevel3 SET post_id = %d WHERE menulevel3_id = %d", $post_id, $keyfield ) );
		}
		
		// Update GUID
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "posts SET " . $wpdb->prefix . "posts.guid = %s WHERE " . $wpdb->prefix . "posts.ID = %d", get_permalink( $post_id ), $post_id ) );
		
		// Update EC DB Item
		$sql = "UPDATE ec_menulevel3 SET ec_menulevel3.menulevel3_id = %d, ec_menulevel3.name = %s, ec_menulevel3.clicks = %s, ec_menulevel3.menu_order = %s, ec_menulevel3.seo_keywords = %s, ec_menulevel3.seo_description = %s, ec_menulevel3.banner_image = %s WHERE ec_menulevel3.menulevel3_id = %d";
		$this->db->query( $this->db->prepare( $sql, $menulevel3['menuparentid'], $menulevel3['menuname'], $menulevel3['clicks'], $menulevel3['menu3order'], $menulevel3['seokeywords'], $menulevel3['seodescription'], $menulevel3['bannerimage'], $keyfield ) );
		
		return array( "success" );
		
	}//updatemenulevel3
	
	function addmenulevel3( $menulevel3 ){
		
		$menulevel3 = (array)$menulevel3;
		
		// Insert EC DB Item
		$sql = "INSERT INTO ec_menulevel3( ec_menulevel3.menulevel2_id, ec_menulevel3.name, ec_menulevel3.clicks, ec_menulevel3.menu_order, ec_menulevel3.seo_keywords, ec_menulevel3.seo_description, ec_menulevel3.banner_image ) VALUES( %d, %s, %s, %s, %s, %s, %s )";

		$rows_affected = $this->db->query( $this->db->prepare( $sql, $menulevel3['menuparentid'], $menulevel3['menuname'], $menulevel3['clicks'], $menulevel3['menu3order'], $menulevel3['seokeywords'], $menulevel3['seodescription'], $menulevel3['bannerimage'] ) );
		
		$menulevel3_id = $this->db->insert_id;
		
		//new post coding
		//previous guid
		$previous_guid = $this->db->get_var( $this->db->prepare( "SELECT " . $this->db->prefix . "posts.guid FROM " . $this->db->prefix . "posts WHERE " . $this->db->prefix . "posts.ID = %d", $post_id ) );
		//status
		$status = "publish";
		//post slug	
		$post_slug = preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep(  $menulevel3['menuname']  ) ) );
		
		//build new GUID with new slug	
		$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
		if( strstr( $store_page, '?' ) )
			$guid = $store_page . '&subsubmenuid=' . $menulevel3_id ;
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
		$this->db->query( $this->db->prepare( "INSERT INTO " . $this->db->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type ) VALUES( %s, %s, %s, %s, %s, %s )", '[ec_store subsubmenuid="' . $menulevel3_id . '"]', $status, utf8_encode($GLOBALS['language']->convert_text( $menulevel3['menuname'] ) ), $post_slug, $guid, "ec_store" ) );
		$post_id = $this->db->insert_id;
			
		$this->db->query( $this->db->prepare( "UPDATE ec_menulevel3 SET post_id = %d WHERE menulevel3_id = %d", $post_id, $menulevel3_id ) );
		
		// Update EC DB post_id
		$db = new ec_db( );
		$db->update_subsubmenu_post_id( $subsubmenu_id, $post_id );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//addmenulevel3

}//ec_admin_mainmenu
?>