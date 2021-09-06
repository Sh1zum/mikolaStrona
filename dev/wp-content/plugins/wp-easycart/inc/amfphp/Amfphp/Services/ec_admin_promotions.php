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


class ec_admin_promotions{		
		
	private $db;
	
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

	}
	
	public function _getMethodRoles($methodName){
	   if ($methodName == 'getpromotions') return array('admin');
	   else if($methodName == 'deletepromotion') return array('admin');
	   else if($methodName == 'updatepromotion') return array('admin');
	   else if($methodName == 'addpromotion') return array('admin');
	   else  return null;
	}
	
	function getpromotions( $startrecord, $limit, $orderby, $ordertype, $filter ) {
		
		$timezonesql = "SELECT ec_setting.timezone FROM ec_setting";
		$timezone = $this->db->get_var( $timezonesql );
		date_default_timezone_set( $timezone );
		
		$serverdtz = date_default_timezone_get( );
		$dtz = new DateTimeZone( $serverdtz );
		$server_time = new DateTime( 'now', $dtz );
		$offset = $dtz->getOffset( $server_time );
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_promotion.*, ec_promotion.promo_limit AS `limit`, ec_promotion.start_date AS original_start_date, ec_promotion.end_date AS original_end_date, UNIX_TIMESTAMP( ec_promotion.start_date ) AS start_date, UNIX_TIMESTAMP( ec_promotion.end_date ) AS end_date FROM ec_promotion WHERE ec_promotion.promotion_id != '' " . $filter . " ORDER BY " . $orderby . " " . $ordertype . " LIMIT ". $startrecord . ", " . $limit;
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		$returnArray = array( );
		
		if( count( $results ) > 0 ){
			foreach( $results as $row ){
				$row->timezone = $serverdtz;
				$phpstarttime = strtotime( $row->original_start_date );
				$phpendtime = strtotime( $row->original_end_date );
				$row->offset = $offset;
				$row->offset_start_date = strval( $phpstarttime );
				$row->offset_end_date = strval( $phpendtime );
				$row->totalrows = $totalrows;
				$returnArray[] = $row;
		  	}
		  	return $returnArray;
		
		}else{
		  return array( "noresults" );
		}
		
	}
	
	function deletepromotion( $promotionid ){
		  
		$sql = "DELETE FROM ec_promotion WHERE ec_promotion.promotion_id = %s";
		$success = $this->db->query( $this->db->prepare( $sql, $promotionid ) );
		
		if( $success === FALSE ){
			return array( "error" );
		}else{
			return array( "success" );
		}
	
	}
	
	function updatepromotion($promotionid, $promotioninfo) {
			
		//convert object to array
		$promotioninfo = (array)$promotioninfo;
		
		if( $promotioninfo['startdate'] != '' ){
			$unixstartdate = $promotioninfo['startdate']->timeStamp / 1000;
			$startdate = date("'Y-m-d H:i:s'", strtotime("midnight", $unixstartdate));
		}else{
			$startdate = 'NULL';
		}
		
		if( $promotioninfo['enddate'] != '' ){
			$unixenddate = $promotioninfo['enddate']->timeStamp / 1000;
			$enddate = date("'Y-m-d H:i:s'", strtotime("tomorrow", $unixenddate) -1);
		}else{
			$enddate = 'NULL';
		}
		
		//Create SQL Query
		$sql = "Replace into ec_promotion(ec_promotion.promotion_id, ec_promotion.name, ec_promotion.type, ec_promotion.start_date, ec_promotion.end_date, ec_promotion.product_id_1, ec_promotion.manufacturer_id_1, ec_promotion.category_id_1, ec_promotion.price1, ec_promotion.price2, ec_promotion.percentage1)
		values('".$promotionid."', '%s', '%s', ".$startdate.", ".$enddate.", '%s', '%s', '%s', '%s', '%s', '%s')";
		
		$this->db->query( $this->db->prepare( $sql,  $promotioninfo['promotionname'],
				$promotioninfo['promotiontype'],
				$promotioninfo['product1'],
				$promotioninfo['manufacturer1'],
				$promotioninfo['category1'],
				$promotioninfo['price1'],
				$promotioninfo['price2'],
				$promotioninfo['percentage1']));
		
		return array( "success" );
			
	}
	
	function addpromotion($promotioninfo) {
			
			 //convert object to array
			 $promotioninfo = (array)$promotioninfo; 
			
			
			if ($promotioninfo['startdate'] != '') {
				$unixstartdate = $promotioninfo['startdate']->timeStamp / 1000;
				$startdate = date("'Y-m-d H:i:s'", strtotime("midnight", $unixstartdate));
			} else {
				$startdate = 'NULL';
			}
			if ($promotioninfo['enddate'] != '') {
				$unixenddate = $promotioninfo['enddate']->timeStamp / 1000;
				$enddate = date("'Y-m-d H:i:s'", strtotime("tomorrow", $unixenddate) -1);
			} else {
				$enddate = 'NULL';
			}
			
			
			
			
			  //Create SQL Query
			  $sql = "Insert into ec_promotion(ec_promotion.promotion_id, ec_promotion.name, ec_promotion.type, ec_promotion.start_date, ec_promotion.end_date, ec_promotion.product_id_1, ec_promotion.manufacturer_id_1, ec_promotion.category_id_1, ec_promotion.price1, ec_promotion.price2, ec_promotion.percentage1)
				values(NULL, '%s', '%s', ".$startdate.", ".$enddate.", '%s', '%s', '%s', '%s', '%s', '%s')";
				
			 $this->db->query( $this->db->prepare( $sql,
				 $promotioninfo['promotionname'],
				 $promotioninfo['promotiontype'],
				 $promotioninfo['product1'],
				 $promotioninfo['manufacturer1'],
				 $promotioninfo['category1'],
				 $promotioninfo['price1'],
				 $promotioninfo['price2'],
				 $promotioninfo['percentage1']));
				
			  return array( "success" );
			  
		}

}
?>