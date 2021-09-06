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

class ec_admin_dashboard{
	
	private $db;

	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;
	
	}//ec_admin_dashboard
	
	public function _getMethodRoles( $methodName ){
		
			 if( $methodName == 'getdashboardorders' ) 		return array( 'admin' );
		else if( $methodName == 'getdashboardmenus' ) 		return array( 'admin' );
		else if( $methodName == 'getdashboardreviews' ) 	return array( 'admin' );
		else if( $methodName == 'getdashboardproducts' ) 	return array( 'admin' );
		else if( $methodName == 'getdashboardstats' ) 		return array( 'admin' );
		else 												return null;
	
	}//_getMethodRoles
	
	function getdashboardorders( ){
		
		$results = $this->db->get_results( "SELECT SQL_CALC_FOUND_ROWS ec_order.billing_first_name, ec_order.billing_last_name, ec_order.grand_total, UNIX_TIMESTAMP(ec_order.order_date) AS order_date, ec_order.user_email, ec_order.orderstatus_id, ec_orderstatus.is_approved, ec_order.order_id, ec_order.order_viewed FROM ec_order, ec_orderstatus WHERE ec_order.orderstatus_id = ec_orderstatus.status_id ORDER BY ec_order.order_date DESC LIMIT 0, 23" );
		
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( count( $results ) > 0 ){
			$results[0]->totalrows = $totalquery;
			for( $i=0; $i<count( $results ); $i++ ){
				$new_total = $GLOBALS['currency']->get_currency_display( $results[$i]->grand_total );
				$results[$i]->formatted_grand_total = $new_total;
			}
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}//getdashboardorders
	
	function getdashboardmenus( ){
		
		$results = $this->db->get_results( "SELECT SQL_CALC_FOUND_ROWS ec_menulevel1.* FROM ec_menulevel1 ORDER BY ec_menulevel1.clicks DESC LIMIT 0, 6" );
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( count( $results ) > 0 ){
			$results[0]->totalrows = $totalquery;
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}//getdashboardmenus
	
	function getdashboardreviews( ){
		
		$results = $this->db->get_results( "SELECT SQL_CALC_FOUND_ROWS  ec_product.title, reviews.*, UNIX_TIMESTAMP(reviews.datesubmitted) AS datesubmitted  FROM reviews  LEFT JOIN products ON ec_product.product_id = reviews.productID ORDER BY reviews.datesubmitted DESC LIMIT 0, 6" );
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( count( $results ) > 0 ){
			$results[0]->totalrows = $totalquery;
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}//getdashboardreviews
	
	function getdashboardproducts( ){
		
		$results = $this->db->get_results( "SELECT SQL_CALC_FOUND_ROWS ec_product.*, statistics.* FROM products LEFT JOIN statistics ON ec_product.product_id = statistics.ProductID ORDER BY statistics.views DESC LIMIT 0, 6" );
		$totalquery = $this->db_get_var( "SELECT FOUND_ROWS( )" );
		
		if( count( $results ) > 0 ){
			$results[0]->totalrows = $totalquery;
			return $results;
		}else{
			return array( "noresults" );
		}
	}//getdashboardproducts
	
	function getdashboardstats( $local_tz_diff ){
		
		if( $local_tz_diff < 0 ){
			$local_tz_diff = $local_tz_diff * -1;
			$daily_sales = $this->db->get_var( $this->db->prepare( "SELECT SUM( ec_order.grand_total ) as total FROM ec_order, ec_orderstatus WHERE ec_orderstatus.status_id = ec_order.orderstatus_id AND ec_orderstatus.is_approved = 1 AND DATE( DATE_SUB( ec_order.order_date, INTERVAL %d HOUR ) ) = DATE( DATE_SUB( NOW( ), INTERVAL %d HOUR ) )", $local_tz_diff, $local_tz_diff ) );
		}else{
			$daily_sales = $this->db->get_var( $this->db->prepare( "SELECT SUM( ec_order.grand_total ) as total FROM ec_order, ec_orderstatus WHERE ec_orderstatus.status_id = ec_order.orderstatus_id AND ec_orderstatus.is_approved = 1 AND DATE( DATE_ADD( ec_order.order_date, INTERVAL %d HOUR ) ) = DATE( DATE_ADD( NOW( ), INTERVAL %d HOUR ) )", $local_tz_diff, $local_tz_diff ) );
		}
		
		return array( 'daily_sales' => $GLOBALS['currency']->get_currency_display( $daily_sales ) );
	
	}//getdashboardstats

}//ec_admin_dashboard
?>