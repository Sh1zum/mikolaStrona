<?php 
/*
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//All Code and Design is copyrighted by Level Four Development, llc
//
//Level Four Development, LLC provides this code "as is" without warranty of any kind, either express or implied,     
//including but not limited to the implied warranties of merchantability and/or fitness for a particular purpose.         
//
//Only licensed users may use this code and storfront for live purposes. All other use is prohibited and may be 
//subject to copyright violation laws. If you have any questions regarding proper use of this code, please
//contact Level Four Development, llc and EasyCart prior to use.
//
//All use of this storefront is subject to our terms of agreement found on Level Four Development, llc's  website.
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/

//load our connection settings
ob_get_clean( );
ob_start( NULL, 4096 );

require_once( '../../../../../../wp-load.php' );

ob_end_clean( );

global $wpdb;

if( ( isset( $_GET['email'] ) ) && ( $_GET['email'] != "" ) ){

	$sql = "DELETE FROM ec_subscriber WHERE ec_subscriber.email = %s";
	$wpdb->query( $wpdb->prepare( $sql, $_GET['email'] ) );
	
	$deleteGoTo = "successfullydeleted.php";
	
	if( isset( $_SERVER['QUERY_STRING'] ) ){
	
		$deleteGoTo .= ( strpos( $deleteGoTo, '?' ) ) ? "&" : "?";
		$deleteGoTo .= $_SERVER['QUERY_STRING'];
	
	}
	
	header( "Location: " . $deleteGoTo );

}
?>