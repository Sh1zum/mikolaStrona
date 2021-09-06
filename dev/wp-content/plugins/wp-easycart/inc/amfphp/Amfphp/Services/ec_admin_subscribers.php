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

class ec_admin_subscribers{
	
	private $db;	
	
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

	}
	
	//secure all of the services for logged in authenticated users only	
	public function _getMethodRoles( $methodName ){
	   if( 		$methodName == 'getsubscribers') 	return array('admin');
	   else if( $methodName == 'deletesubscriber') 	return array('admin');
	   else if( $methodName == 'updatesubscriber') 	return array('admin');
	   else if( $methodName == 'addsubscriber') 	return array('admin');
	   else 										return NULL;
	}
	
	//subscriber functions
	function getsubscribers( $startrecord, $limit, $orderby, $ordertype, $filter ){
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_subscriber.* FROM ec_subscriber  WHERE ec_subscriber.subscriber_id != '' ".$filter." ORDER BY ".  $orderby ." ".  $ordertype . " LIMIT ".  $startrecord .", ".  $limit."";
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		$returnArray = array( );
		
		if( !empty( $results ) ){
			foreach( $results as $row ){
		  		$row->totalrows = $totalrows;
		  		$returnArray[] = $row;
			}
			return $returnArray;
		
		}else{
			return array( "noresults" );
		}
		
	}
	
	function deletesubscriber( $subscriberid ){
		
		$sql = "DELETE FROM ec_subscriber WHERE ec_subscriber.subscriber_id = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $subscriberid ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function updatesubscriber( $subscriberid, $subscriber ){
		
		$subscriber = (array)$subscriber;
		
		$sql = "UPDATE ec_subscriber SET ec_subscriber.email = %s, ec_subscriber.first_name = %s, ec_subscriber.last_name = %s WHERE ec_subscriber.subscriber_id = %s";
		$this->db->query( $this->db->prepare( $sql, $subscriber['email'], $subscriber['firstname'], $subscriber['lastname'], $subscriberid ) );
		
		return array( "success" );
		
	}
	
	function addsubscriber( $subscriber ){
		
		$subscriber = (array)$subscriber;
		
		$sql = "INSERT INTO ec_subscriber( ec_subscriber.email, ec_subscriber.first_name, ec_subscriber.last_name ) VALUES( %s, %s, %s )";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $subscriber['email'], $subscriber['firstname'], $subscriber['lastname'] ) );
		
		// MyMail Hook
		if( function_exists( 'mailster' ) ){
			mailster('subscribers')->add(array(
				'firstname' => $subscriber['firstname'],
				'lastname' => $subscriber['lastname'],
				'email' => $subscriber['email'],
				'status' => 1,
			), false );
		}
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
}//close class
?>