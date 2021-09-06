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

//Flash Variables
$date = $_POST['datemd5'];
$requestID = $_POST['reqID'];
$menuid = $_POST['menuitemid'];
$menulevel = $_POST['menulevel'];

//Get User Information
$user_sql = "SELECT  ec_user.*, ec_role.admin_access FROM ec_user LEFT JOIN ec_role ON (ec_user.user_level = ec_role.role_label) WHERE ec_user.password = %s AND  (ec_user.user_level = 'admin' OR ec_role.admin_access = 1)";
$users = $wpdb->get_results( $wpdb->prepare( $user_sql, $requestID ) );

if( !empty( $users ) ){

	$filename = $_FILES['Filedata']['name'];	
	$filetmpname = $_FILES['Filedata']['tmp_name'];	
	$fileType = $_FILES["Filedata"]["type"];
	$fileSizeMB = ($_FILES["Filedata"]["size"] / 1024 / 1000);

	$explodedfilename = pathinfo($filename);
	$nameoffile = $explodedfilename['filename'];
	$fileextension = $explodedfilename['extension'];

	if(strtolower($fileextension)  == 'jpg' || strtolower($fileextension)  == 'jpeg' || strtolower($fileextension)  == 'gif' || strtolower($fileextension)  == 'png') {
		// Place file on server, into the banners folder
		move_uploaded_file($_FILES['Filedata']['tmp_name'], "../../../../wp-easycart-data/products/banners/".$nameoffile."_".$date.".".$fileextension );
	} else {
		return 'not allowed';
	}
	
}

?>