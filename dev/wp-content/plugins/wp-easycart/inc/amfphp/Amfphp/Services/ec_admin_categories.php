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

class ec_admin_categories{		
	
	private $db;
	
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

	}
		
	public function _getMethodRoles( $methodName ){
	   		if( $methodName == 'getcategories' ) 		return array( 'admin' );
	   else if( $methodName == 'getcategorylist' ) 		return array( 'admin' );
	   else if( $methodName == 'deletecategory' ) 		return array( 'admin' );
	   else if( $methodName == 'updatecategory' ) 		return array( 'admin' );
	   else if( $methodName == 'addcategory' ) 			return array( 'admin' );
	   else if( $methodName == 'getcategoryitems' ) 	return array( 'admin' );
	   else if( $methodName == 'deleteindividualcategoryitem' ) 	return array( 'admin' );
	   else if( $methodName == 'deletecategoryitem' ) 	return array( 'admin' );
	   else if( $methodName == 'addcategoryitem' ) 		return array( 'admin' );
	   else if( $methodName == 'getcategoryproducts' ) 	return array( 'admin' );
	   else  											return null;
	}//_getMethodRoles
	
		
	function level_categories( $results, $startrecord = 0, $limit = 9999999999 ){
		
		if( count( $results ) <= 0 ){
			return array( );
		}
		$return_results = array( );
		$last_level1 = -1;
		$last_level2 = -1;
		$last_level3 = -1;
		$last_level4 = -1;
		
		$row_count = 0;
		$limit = $limit + $startrecord;
		
		for( $i=0; $i<count( $results ); $i++ ){
			
			if( isset( $results[$i]['level2_id'] ) && $last_level1 != $results[$i]['level1_id'] ){
				$new_result = array( );
				$last_level1 = $results[$i]['level1_id'];
				$new_result['category_id'] =  $results[$i]['level1_id'];
				$new_result['parent_id'] =  $results[$i]['level1_parent_id'];
				$new_result['post_id'] =  $results[$i]['level1_post_id'];
				$new_result['category_name'] =  $results[$i]['level1_name'];
				$new_result['short_description'] =  $results[$i]['level1_short_description'];
				$new_result['image'] =  $results[$i]['level1_image'];
				$new_result['featured_category'] =  $results[$i]['level1_featured_category'];
				$new_result['level'] = 0;
				$new_result['guid'] = basename($results[$i]['level1_guid']);
				if( $row_count >= $startrecord && $row_count < $limit )
					$return_results[] = $new_result;
				$row_count++;
			}
			
			if( isset( $results[$i]['level3_id'] ) && $last_level2 != $results[$i]['level2_id'] ){
				$new_result = array( );
				$last_level2 = $results[$i]['level2_id'];
				$new_result['category_id'] =  $results[$i]['level2_id'];
				$new_result['parent_id'] =  $results[$i]['level2_parent_id'];
				$new_result['post_id'] =  $results[$i]['level2_post_id'];
				$new_result['category_name'] =  $results[$i]['level2_name'];
				$new_result['short_description'] =  $results[$i]['level2_short_description'];
				$new_result['image'] =  $results[$i]['level2_image'];
				$new_result['featured_category'] =  $results[$i]['level2_featured_category'];
				$new_result['level'] = 1;
				$new_result['guid'] = basename($results[$i]['level2_guid']);
				if( $row_count >= $startrecord && $row_count < $limit )
					$return_results[] = $new_result;
				$row_count++;
			}
			
			if( isset( $results[$i]['level4_id'] ) && $last_level3 != $results[$i]['level3_id'] ){
				$new_result = array( );
				$last_level3 = $results[$i]['level3_id'];
				$new_result['category_id'] =  $results[$i]['level3_id'];
				$new_result['parent_id'] =  $results[$i]['level3_parent_id'];
				$new_result['post_id'] =  $results[$i]['level3_post_id'];
				$new_result['category_name'] =  $results[$i]['level3_name'];
				$new_result['short_description'] =  $results[$i]['level3_short_description'];
				$new_result['image'] =  $results[$i]['level3_image'];
				$new_result['featured_category'] =  $results[$i]['level3_featured_category'];
				$new_result['level'] = 2;
				$new_result['guid'] = basename($results[$i]['level3_guid']);
				if( $row_count >= $startrecord && $row_count < $limit )
					$return_results[] = $new_result;
				$row_count++;
			}
			
			if( isset( $results[$i]['level5_id'] ) && $last_level4 != $results[$i]['level4_id'] ){
				$new_result = array( );
				$last_level4 = $results[$i]['level4_id'];
				$new_result['category_id'] =  $results[$i]['level4_id'];
				$new_result['parent_id'] =  $results[$i]['level4_parent_id'];
				$new_result['post_id'] =  $results[$i]['level4_post_id'];
				$new_result['category_name'] =  $results[$i]['level4_name'];
				$new_result['short_description'] =  $results[$i]['level4_short_description'];
				$new_result['image'] =  $results[$i]['level4_image'];
				$new_result['featured_category'] =  $results[$i]['level4_featured_category'];
				$new_result['level'] = 3;
				$new_result['guid'] = basename($results[$i]['level4_guid']);
				if( $row_count >= $startrecord && $row_count < $limit )
					$return_results[] = $new_result;
				$row_count++;
			}
			
			$new_result = array( );
			if( isset( $results[$i]['level5_id'] ) ){
				$new_result['category_id'] =  $results[$i]['level5_id'];
				$new_result['parent_id'] =  $results[$i]['level5_parent_id'];
				$new_result['post_id'] =  $results[$i]['level5_post_id'];
				$new_result['category_name'] =  $results[$i]['level5_name'];
				$new_result['short_description'] =  $results[$i]['level5_short_description'];
				$new_result['image'] =  $results[$i]['level5_image'];
				$new_result['featured_category'] =  $results[$i]['level5_featured_category'];
				$new_result['level'] = 4;
				$new_result['guid'] = basename($results[$i]['level5_guid']);
			
			}else if( isset( $results[$i]['level4_id'] ) ){
				$new_result['category_id'] =  $results[$i]['level4_id'];
				$new_result['parent_id'] =  $results[$i]['level4_parent_id'];
				$new_result['post_id'] =  $results[$i]['level4_post_id'];
				$new_result['category_name'] =  $results[$i]['level4_name'];
				$new_result['short_description'] =  $results[$i]['level4_short_description'];
				$new_result['image'] =  $results[$i]['level4_image'];
				$new_result['featured_category'] =  $results[$i]['level4_featured_category'];
				$new_result['level'] = 3;
				$new_result['guid'] = basename($results[$i]['level4_guid']);
				
			}else if( isset( $results[$i]['level3_id'] ) ){
				$new_result['category_id'] =  $results[$i]['level3_id'];
				$new_result['parent_id'] =  $results[$i]['level3_parent_id'];
				$new_result['post_id'] =  $results[$i]['level3_post_id'];
				$new_result['category_name'] =  $results[$i]['level3_name'];
				$new_result['short_description'] =  $results[$i]['level3_short_description'];
				$new_result['image'] =  $results[$i]['level3_image'];
				$new_result['featured_category'] =  $results[$i]['level3_featured_category'];
				$new_result['level'] = 2;
				$new_result['guid'] = basename($results[$i]['level3_guid']);
				
			}else if( isset( $results[$i]['level2_id'] ) ){
				$new_result['category_id'] =  $results[$i]['level2_id'];
				$new_result['parent_id'] =  $results[$i]['level2_parent_id'];
				$new_result['post_id'] =  $results[$i]['level2_post_id'];
				$new_result['category_name'] =  $results[$i]['level2_name'];
				$new_result['short_description'] =  $results[$i]['level2_short_description'];
				$new_result['image'] =  $results[$i]['level2_image'];
				$new_result['featured_category'] =  $results[$i]['level2_featured_category'];
				$new_result['level'] = 1;
				$new_result['guid'] = basename($results[$i]['level2_guid']);
				
			}else{
				$new_result['category_id'] =  $results[$i]['level1_id'];
				$new_result['parent_id'] =  $results[$i]['level1_parent_id'];
				$new_result['post_id'] =  $results[$i]['level1_post_id'];
				$new_result['category_name'] =  $results[$i]['level1_name'];
				$new_result['short_description'] =  $results[$i]['level1_short_description'];
				$new_result['image'] =  $results[$i]['level1_image'];
				$new_result['featured_category'] =  $results[$i]['level1_featured_category'];
				$new_result['level'] = 0;
				$new_result['guid'] = basename($results[$i]['level1_guid']);
			}
			
			
			
			if( $row_count >= $startrecord && $row_count < $limit )
					$return_results[] = $new_result;
			$row_count++;
			
		}
		
		$return_results[0]['totalrows'] = $row_count;
		
		return $return_results;
		
	}
		
	function getcategories( $startrecord, $limit, $orderby, $ordertype, $filter ){
		
		$sql = "SELECT 
			a.category_id AS level1_id,
			a.parent_id AS level1_parent_id,
			a.category_name AS level1_name,
			a.short_description AS level1_short_description,
			a.image AS level1_image,
			a.featured_category AS level1_featured_category,
			a.post_id AS level1_post_id,
			aa.guid AS level1_guid,
			
			b.category_name AS level2_name,
			b.category_id AS level2_id,
			b.parent_id AS level2_parent_id,
			b.short_description AS level2_short_description,
			b.image AS level2_image,
			b.featured_category AS level2_featured_category,
			b.post_id AS level2_post_id,
			bb.guid AS level2_guid,
			
			c.category_name AS level3_name,
			c.category_id AS level3_id,
			c.parent_id AS level3_parent_id,
			c.short_description AS level3_short_description,
			c.image AS level3_image,
			c.featured_category AS level3_featured_category,
			c.post_id AS level3_post_id,
			cc.guid AS level3_guid,
			
			d.category_name AS level4_name,
			d.category_id AS level4_id,
			d.parent_id AS level4_parent_id,
			d.short_description AS level4_short_description,
			d.image AS level4_image,
			d.featured_category AS level4_featured_category,
			d.post_id AS level4_post_id,
			dd.guid AS level4_guid,
			
			e.category_name AS level5_name,
			e.category_id AS level5_id,
			e.parent_id AS level5_parent_id,
			e.short_description AS level5_short_description,
			e.image AS level5_image,
			e.featured_category AS level5_featured_category,
			e.post_id AS level5_post_id,
			ee.guid AS level5_guid
			
			FROM 
			
			ec_category a 
			
			LEFT JOIN ec_category b ON ( a.category_id = b.parent_id )
			
			LEFT JOIN ec_category c ON ( b.category_id = c.parent_id )
			
			LEFT JOIN ec_category d ON ( c.category_id = d.parent_id )
			
			LEFT JOIN ec_category e ON ( d.category_id = e.parent_id )
			
			LEFT JOIN " . $this->db->prefix . "posts aa ON (aa.ID = a.post_id)
			LEFT JOIN " . $this->db->prefix . "posts bb ON (bb.ID = b.post_id)
			LEFT JOIN " . $this->db->prefix . "posts cc ON (cc.ID = c.post_id)
			LEFT JOIN " . $this->db->prefix . "posts dd ON (dd.ID = d.post_id)
			LEFT JOIN " . $this->db->prefix . "posts ee ON (ee.ID = e.post_id)

			
			WHERE a.parent_id = 0
			
			";
		
		if( $filter != "" ){
			
			$sql .= $this->db->prepare( "HAVING ( level1_name LIKE %s OR level2_name LIKE %s OR level3_name LIKE %s OR level4_name LIKE %s OR level5_name LIKE %s )", "%" . $filter . "%", "%" . $filter . "%", "%" . $filter . "%", "%" . $filter . "%", "%" . $filter . "%" );
			
		}
			
		$sql .= "
			
			ORDER BY 
			
			a.parent_id " . $ordertype. ", a.category_name " . $ordertype. ", 
			b.parent_id " . $ordertype. ", b.category_name " . $ordertype. ", 
			c.parent_id " . $ordertype. ", c.category_name " . $ordertype. ", 
			d.parent_id " . $ordertype. ", d.category_name " . $ordertype. ", 
			e.parent_id " . $ordertype. ", e.category_name " . $ordertype;
		
		//return $sql;
		
		$this->db->query( "SET SQL_BIG_SELECTS=1" );
		
		$results = $this->db->get_results( $sql, ARRAY_A );
		
			
		$results = $this->level_categories( $results, $startrecord, $limit );
		
		//trying to return total products inside this category_id
		if(count( $results ) > 0 ) {
			$rowcount = 0;
			foreach( $results as $row ){ 
				$product_sql = "SELECT COUNT( ec_categoryitem.categoryitem_id ) as total FROM ec_categoryitem WHERE ec_categoryitem.category_id = %d";
				$total_products = $this->db->get_var( $this->db->prepare( $product_sql,  $row['category_id'] ) );
				$results[$rowcount]['totalproducts'] = $total_products;
				$rowcount++; 
			}
		}
		//end new section
		
		if( count( $results ) > 0 ){
			
			
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}//getcategories
	
	function getcategorylist( ){

		$sql = "SELECT 
			a.category_id AS level1_id,
			a.parent_id AS level1_parent_id,
			a.category_name AS level1_name,
			a.short_description AS level1_short_description,
			a.image AS level1_image,
			a.featured_category AS level1_featured_category,
			
			 
			b.category_name AS level2_name,
			b.category_id AS level2_id,
			b.parent_id AS level2_parent_id,
			b.short_description AS level2_short_description,
			b.image AS level2_image,
			b.featured_category AS level2_featured_category,
			
			
			c.category_name AS level3_name,
			c.category_id AS level3_id,
			c.parent_id AS level3_parent_id,
			c.short_description AS level3_short_description,
			c.image AS level3_image,
			c.featured_category AS level3_featured_category,
			
			
			d.category_name AS level4_name,
			d.category_id AS level4_id,
			d.parent_id AS level4_parent_id,
			d.short_description AS level4_short_description,
			d.image AS level4_image,
			d.featured_category AS level4_featured_category,

			
			e.category_name AS level5_name,
			e.category_id AS level5_id,
			e.parent_id AS level5_parent_id,
			e.short_description AS level5_short_description,
			e.image AS level5_image,
			e.featured_category AS level5_featured_category

			
			FROM 
			
			ec_category a 
			
			LEFT JOIN ec_category b ON a.category_id = b.parent_id
			
			LEFT JOIN ec_category c ON b.category_id = c.parent_id
			
			LEFT JOIN ec_category d ON c.category_id = d.parent_id
			
			LEFT JOIN ec_category e ON d.category_id = e.parent_id
			
			
			
			WHERE a.parent_id = 0
			
			ORDER BY 
			
			a.parent_id ASC, a.category_name ASC, 
			b.parent_id ASC, b.category_name ASC, 
			c.parent_id ASC, c.category_name ASC, 
			d.parent_id ASC, d.category_name ASC, 
			e.parent_id ASC, e.category_name ASC";// . $orderby;
		
		$this->db->query( "SET SQL_BIG_SELECTS=1" );
		$results = $this->db->get_results( $sql, ARRAY_A );
		$results = $this->level_categories( $results );
		
		if( count( $results ) > 0 ){
			
			
			return $results;
		}else{
			return array( "noresults" );
		}
	}//getcategorylist
	
	function deletecategory( $categoryid ){
		
		// Delete WordPress Post
		$sql = "SELECT post_id FROM ec_category WHERE category_id = %d";
		$post_id = $this->db->get_var( $this->db->prepare( $sql, $categoryid ) );
		wp_delete_post( $post_id, true );
		
		// Update children to newer parent
		$sql = "SELECT parent_id FROM ec_category WHERE category_id = %d";
		$parent_id = $this->db->get_var( $this->db->prepare( $sql, $categoryid ) );
		$sql = "UPDATE ec_category SET ec_category.parent_id = %s WHERE ec_category.parent_id = %s";
		$this->db->query( $this->db->prepare( $sql, $parent_id, $categoryid ) );
		
		// Delete Category	
		$sql = "DELETE FROM ec_category WHERE ec_category.category_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $categoryid ) );
		
		// Delete Category Items
		$sql = "DELETE FROM ec_categoryitem WHERE ec_categoryitem.category_id = %d";
		$this->db->query( $this->db->prepare( $sql, $categoryid ) );
		
		do_action( 'wpeasycart_category_deleted', $categoryid );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//deletecategory
	
	function updatecategory( $categoryid, $categoryitem ){
		$categoryitem = (array)$categoryitem;
		
		$sql = "UPDATE ec_category SET ec_category.category_name = %s, ec_category.short_description = %s, ec_category.image = %s, ec_category.parent_id = %s, ec_category.featured_category = %s WHERE ec_category.category_id = %d";
		
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $categoryitem['categoryname'], $categoryitem['categorydescription'], $categoryitem['categoryimage'], $categoryitem['categoryparent'], $categoryitem['categoryfeatured'], $categoryid ) );

		// Update WordPress Post
		$sql = "SELECT ec_category.post_id FROM ec_category WHERE ec_category.category_id = %d";
		$post_slug = preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep(  $categoryitem['link_slug']  ) ) );
		$post_id = $this->db->get_var( $this->db->prepare( $sql, $categoryid ) );

		//new post coding
			//previous guid
			$previous_guid = $this->db->get_var( $this->db->prepare( "SELECT " . $this->db->prefix . "posts.guid FROM " . $this->db->prefix . "posts WHERE " . $this->db->prefix . "posts.ID = %d", $post_id ) );
			//status
			$status = "publish";
			//post slug	
			$post_slug = preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep(  $categoryitem['link_slug']   ) ) );
			
			//build new GUID with new slug	
			$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
			if( strstr( $store_page, '?' ) )
				$guid = $store_page . '&group_id=' . $category_id ;
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
				$this->db->query( $this->db->prepare( "UPDATE " . $this->db->prefix . "posts SET post_content = %s, post_status = %s, post_title = %s, post_name = %s, guid = %s WHERE ID = %d", '[ec_store groupid="' . $categoryid  . '"]', $status, utf8_encode( $GLOBALS['language']->convert_text( $categoryitem['categoryname']  ) ), $post_slug , $guid, $post_id ) );
				
			}else{
				/* Manually Insert Post */
				$this->db->query( $this->db->prepare( "INSERT INTO " . $this->db->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type ) VALUES( %s, %s, %s, %s, %s, %s )", '[ec_store groupid="' . $categoryid . '"]', $status, utf8_encode($GLOBALS['language']->convert_text( $categoryitem['categoryname'] ) ), $post_slug, $guid, "ec_store" ) );
				$post_id = $this->db->insert_id;
			}
			
		
		// Update GUID
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "posts SET " . $wpdb->prefix . "posts.guid = %s WHERE " . $wpdb->prefix . "posts.ID = %d", get_permalink( $results[0]->post_id ), $results[0]->post_id ) );

		do_action( 'wpeasycart_category_updated', $categoryid );
				
		return array( "success" );
		
	}//updatecategory
	
	function addcategory( $categoryitem ){
		
		if(is_string($categoryitem)) {
			$categoryitem = array(
			'categoryname' 			=> $categoryitem,
			'categorydescription' 	=> '',
			'categoryimage' 		=> '',
			'categoryparent' 		=> 0,
			'categoryfeatured' 		=> 0);
		} else {
			$categoryitem = (array)$categoryitem;
		}
		
		$sql = "INSERT INTO ec_category( category_name, short_description, image, parent_id, featured_category ) VALUES( %s,  %s ,  %s ,  %s ,  %s  )";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $categoryitem['categoryname'], $categoryitem['categorydescription'], $categoryitem['categoryimage'], $categoryitem['categoryparent'], $categoryitem['categoryfeatured'] ) );
		//return $this->db->prepare( $sql, $categoryitem['categoryname'], $categoryitem['categorydescription'], $categoryitem['categoryimage'], $categoryitem['categoryparent'], $categoryitem['categoryfeatured'] );
		if( $rows_affected ){
			// Insert a WordPress Custom post type post.
			$category_id = $this->db->insert_id;
					
			
			//new post coding
			//previous guid
			$previous_guid = $this->db->get_var( $this->db->prepare( "SELECT " . $this->db->prefix . "posts.guid FROM " . $this->db->prefix . "posts WHERE " . $this->db->prefix . "posts.ID = %d", $post_id ) );
			//status
			$status = "publish";
			//post slug	
			$post_slug = preg_replace( "/[^A-Za-z0-9\-]/", '', str_replace( ' ', '-', stripslashes_deep(  $categoryitem['categoryname']   ) ) );
			
			//build new GUID with new slug	
			$store_page = get_permalink( get_option( 'ec_option_storepage' ) );
			if( strstr( $store_page, '?' ) )
				$guid = $store_page . '&group_id=' . $category_id ;
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
			$this->db->query( $this->db->prepare( "INSERT INTO " . $this->db->prefix . "posts( post_content, post_status, post_title, post_name, guid, post_type ) VALUES( %s, %s, %s, %s, %s, %s )", '[ec_store groupid="' . $category_id . '"]', $status, utf8_encode($GLOBALS['language']->convert_text( $categoryitem['categoryname'] ) ), $post_slug, $guid, "ec_store" ) );
			$post_id = $this->db->insert_id;
			
			// Update Category Post ID
			$db = new ec_db( );
			$db->update_category_post_id( $category_id, $post_id );
		
			do_action( 'wpeasycart_category_added', $category_id );
			
			return array( "success" );
		}else{
			return array( "error" );
		}
	
	}//addcategory

	function getcategoryitems( $startrecord, $limit, $orderby, $ordertype, $filter, $parentid ){
		  
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_categoryitem.*, ec_product.title, ec_product.product_id FROM ec_categoryitem LEFT JOIN ec_product ON ec_product.product_id = ec_categoryitem.product_id  WHERE ec_categoryitem.categoryitem_id != '' AND ec_categoryitem.category_id = " . $parentid . " " . $filter . " ORDER BY " . $orderby . " " . $ordertype . " LIMIT " . $startrecord . ", " . $limit;
		$results = $this->db->get_results( $sql );
		
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( count( $results) > 0 ){
			$results[0]->totalrows = $totalquery;
			return $results;
		} else {
			return array( "noresults" );
		}
	}//getcategoryitems
	
	function deleteindividualcategoryitem(  $categoryitemid ){
		  
		$sql = "DELETE FROM ec_categoryitem WHERE ec_categoryitem.categoryitem_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql,  $categoryitemid) );
			
		do_action( 'wpeasycart_category_item_deleted', $categoryitemid );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//deletecategoryitem
	
	function deletecategoryitem(  $productid, $categoryid ){
		  
		$sql = "DELETE FROM ec_categoryitem WHERE ec_categoryitem.product_id = %d AND ec_categoryitem.category_id = %d";
		$rows_affected = $this->db->query( $this->db->prepare( $sql,  $productid, $categoryid  ) );
		
		do_action( 'wpeasycart_category_items_deleted', $productid, $categoryid );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//deletecategoryitem

	function addcategoryitem( $productid, $categoryid ){
		
		$sql = "Insert into ec_categoryitem( product_id, category_id ) values( %d, %d )";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $productid, $categoryid ) );
		
		do_action( 'wpeasycart_category_item_added', $productid, $categoryid );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//addcategoryitem

	function getcategoryproducts( $categoryid, $startrecord, $limit, $orderby, $ordertype, $filter  ){
		
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
		ec_product.*, 
		ec_categoryitem.categoryitem_id, 
		ec_categoryitem.category_id,
		ec_manufacturer.name AS manufacturer 
		FROM ec_product 
		LEFT OUTER JOIN ec_categoryitem ON (ec_product.product_id = ec_categoryitem.product_id AND ec_categoryitem.category_id = ".$categoryid.")
		LEFT OUTER JOIN ec_manufacturer ON (ec_product.manufacturer_id = ec_manufacturer.manufacturer_id)   
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
		
		
		
	}//getcategoryproducts

}//ec_admin_categories
?>