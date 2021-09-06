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


class ec_admin_reviews{		

	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

	}	
	
	public function _getMethodRoles( $methodName ){
	   
	   if( 		$methodName == 'getreviews' 	) 	return array( 'admin' );
	   else if(	$methodName == 'deletereview' 	) 	return array( 'admin' );
	   else if(	$methodName == 'updatereview' 	) 	return array( 'admin' );
	   else  										return null;
	
	}

	function getreviews( $startrecord, $limit, $orderby, $ordertype, $filter ){
		//Create SQL Query
		$sql = "SELECT SQL_CALC_FOUND_ROWS  ec_review.*, UNIX_TIMESTAMP(ec_review.date_submitted) AS date_submitted, ec_product.model_number, ec_product.title as product_title, ec_product.activate_in_store, ec_product.image1, ec_product.price, ec_user.first_name, ec_user.last_name, ec_user.email  FROM ec_review LEFT JOIN ec_product ON ec_product.product_id = ec_review.product_id LEFT JOIN ec_user ON ec_review.user_id = ec_user.user_id WHERE ec_review.review_id != '' ".$filter." ORDER BY ".  $orderby ." ".  $ordertype . " LIMIT ".  $startrecord .", ".  $limit."";
		$results = $this->db->get_results( $sql );
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( count( $results) > 0 ){
		  $results[0]->totalrows = $totalquery;
		  return $results;
		}else{
		  return array( "noresults" );
		}
	}
	
	function deletereview($reviewid) {
		
		$deletesql = "DELETE FROM ec_review WHERE ec_review.review_id = '%s'";
		$success = $this->db->query( $this->db->prepare( $deletesql, $reviewid));
		
		if( $success === FALSE ) {
			return array( "error" );
		}else{
			return array( "success" );
		}
		
	}
	
	function updatereview($reviewid, $review) {
		
		$review = (array)$review;
		
		$sql = "UPDATE ec_review SET ec_review.approved='%s', ec_review.title='%s', ec_review.description='%s', ec_review.rating='%s' WHERE ec_review.review_id = '%s'";
		$this->db->query( $this->db->prepare( $sql, $review['approved'], $review['reviewtitle'],$review['reviewdescription'],$review['rating'],$reviewid));
		
		return array( "success" );
		
	}
	
}//close class
?>