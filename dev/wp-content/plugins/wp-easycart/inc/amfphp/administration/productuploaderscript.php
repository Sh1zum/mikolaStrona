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
if( isset( $_POST['reqID'] ) )
	$requestID = $_POST['reqID'];
	
$user_sql = "SELECT  ec_user.*, ec_role.admin_access FROM ec_user LEFT JOIN ec_role ON (ec_user.user_level = ec_role.role_label) WHERE ec_user.password = %s AND  (ec_user.user_level = 'admin' OR ec_role.admin_access = 1)";
$users = $wpdb->get_results( $wpdb->prepare( $user_sql, $requestID ) );

if( !empty( $users ) ){

	$date = $_POST['datemd5'];
	$insertupdate = $_POST['insertupdate'];
	$productid = $_POST['productid'];

	$filename = $_FILES['Filedata']['name'];	
	$filetmpname = $_FILES['Filedata']['tmp_name'];	
	$fileType = $_FILES["Filedata"]["type"];
	$fileSizeMB = ($_FILES["Filedata"]["size"] / 1024 / 1000);

	$explodedfilename = pathinfo($filename);
	$nameoffile = $explodedfilename['filename'];
	$fileextension = strtolower($explodedfilename['extension']);
	
	if(strtolower($fileextension)  != 'php' && strtolower($fileextension)  != 'php3' && strtolower($fileextension)  != 'php4' && strtolower($fileextension)  != 'php5' && strtolower($fileextension)  != 'phtml' && strtolower($fileextension)  != 'pl' && strtolower($fileextension)  != 'py' && strtolower($fileextension)  != 'jsp' && strtolower($fileextension)  != 'asp' && strtolower($fileextension)  != 'htm' && strtolower($fileextension)  != 'html' && strtolower($fileextension)  != 'html5' && strtolower($fileextension)  != 'sh' && strtolower($fileextension)  != 'cgi') {

		move_uploaded_file( $_FILES['Filedata']['tmp_name'], "../../../../wp-easycart-data/products/downloads/".$nameoffile."_".$date.".".$fileextension );
	

		if( $insertupdate == 'update' ){
	
			$sqlfilename = $nameoffile . '_' . $date . '.' .$fileextension;
			$sql = "UPDATE ec_product SET ec_product.download_file_name = %s WHERE ec_product.product_id = %s";
			$wpdb->query( $wpdb->prepare( $sql, $sqlfilename, $productid ) );
	
		}
	}
}else{

	echo "Not Authorized...";

}
?>