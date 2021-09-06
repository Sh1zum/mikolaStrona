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

class ec_admin_giftcards{		
	
	private $db;
		
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

	}//ec_admin_giftcards
	
	public function _getMethodRoles( $methodName ){
			 if( $methodName == 'getgiftcards' ) 	return array( 'admin' );
		else if( $methodName == 'deletegiftcard' ) 	return array( 'admin' );
		else if( $methodName == 'updategiftcard' ) 	return array( 'admin' );
		else if( $methodName == 'addgiftcard' ) 	return array( 'admin' );
		else  										return null;
	}//_getMethodRoles
	
	function getgiftcards( $startrecord, $limit, $orderby, $ordertype, $filter ){
		  
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_orderdetail.order_id, ec_orderdetail.orderdetail_id, ec_giftcard.* FROM ec_giftcard LEFT JOIN ec_orderdetail ON ( ec_giftcard.giftcard_id = ec_orderdetail.giftcard_id) WHERE ec_giftcard.giftcard_id != '' ".$filter." ORDER BY ".  $orderby ." ".  $ordertype . " LIMIT ".  $startrecord .", ".  $limit;
		$results = $this->db->get_results( $sql );
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( count( $results ) > 0 ){
			$results[0]->totalrows = $totalquery;
			return $results;
		}else{
		  return array( "noresults" );
		}
		
	}//getgiftcards
	
	function deletegiftcard( $cardid ){
		$sql = "DELETE FROM ec_giftcard WHERE ec_giftcard.giftcard_id = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $cardid) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}//deletegiftcard
	
	function updategiftcard( $cardid, $card ){
		
		$card = (array)$card;
		
		$sql = "UPDATE ec_giftcard SET ec_giftcard.amount = %s, ec_giftcard.message = %s WHERE ec_giftcard.giftcard_id = %s";
		$this->db->query( $this->db->prepare( $sql, $card['giftcardamount'], $card['giftcardmessage'], $cardid ) );
		
		return array( "success" );
		
	}//updategiftcard
	
	function addgiftcard( $card ){
		
		$card = (array)$card;
		
		$sql = "INSERT INTO ec_giftcard( ec_giftcard.giftcard_id, ec_giftcard.amount, ec_giftcard.message ) VALUES(%s, %s, %s)";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $card['giftcardid'], $card['giftcardamount'], $card['giftcardmessage'] ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			$sql = "SELECT * FROM ec_giftcard WHERE giftcard_id = %s";
			$results = $this->db->get_results( $this->db->prepare( $sql, $card['giftcardid'] ) );
			
			if( empty( $results ) ){
				return array( "Unknown error has occurred" );
			}else{
				return array( "duplicate" );
			}
		}
	}//addgiftcard


}//close class
?>