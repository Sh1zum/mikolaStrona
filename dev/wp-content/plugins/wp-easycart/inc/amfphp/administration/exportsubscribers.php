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

$requestID = "-1";
if( isset( $_GET['reqID'] ) )
	$requestID = $_GET['reqID'];

$user_sql = "SELECT  ec_user.*, ec_role.admin_access FROM ec_user LEFT JOIN ec_role ON (ec_user.user_level = ec_role.role_label) WHERE ec_user.password = %s AND  (ec_user.user_level = 'admin' OR ec_role.admin_access = 1)";
$users = $wpdb->get_results( $wpdb->prepare( $user_sql, $requestID ) );

if( !empty( $users ) ){

	$header = "";
	$data = "";
	$sql = "SELECT * FROM ec_subscriber ORDER BY ec_subscriber.email ASC";
	$results = $wpdb->get_results( $sql, ARRAY_A );
	$keys = array_keys( $results[0] );
	
	foreach( $keys as $key ){
		$header .= $key."\t";
	}

	foreach( $results as $result ){

		$line = '';
		foreach( $result as $value ){

			if( !isset( $value ) || $value == "" ){
				$value = "\t";

			}else{
				$value = str_replace( '"', '""', $value);
				$value = '"' . utf8_decode($value) . '"' . "\t";

			}

			$line .= $value;

		}

		$data .= trim( $line )."\n";

	}
	
	$data = str_replace( "\r", "", $data );

	if( $data == "" ){
		$data = "\nno matching records found\n";
	}
	
	ob_end_flush( );
	header("Content-type: application/vnd.ms-excel");
	header("Content-Transfer-Encoding: binary"); 
	header("Content-Disposition: attachment; filename=subscribers.xls");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo $header."\n".$data; 

}else{

	echo "Not Authorized...";

}

?>