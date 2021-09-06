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
if( !defined( 'WP_USE_THEMES' ) )
	define( 'WP_USE_THEMES', false );
define( 'WP_DEBUG', false );

ob_get_clean( );
ob_start( NULL, 4096 );

require_once( '../../../../../../wp-load.php' );

if( get_option( 'ec_option_amfphp_fix' ) ){
	ob_end_clean( );
}

global $wpdb;

$date = $_POST['datemd5'];
$requestID = $_POST['reqID'];
$imagenumber = $_POST['imagenumber'];
$insertupdate = $_POST['insertupdate'];
$productid = $_POST['product_id'];
$optionitemid = $_POST['optionitemid'];
$useoptionitemimages = $_POST['useoptionitemimages'];
$imagequality = $_POST['imagequality'];

if( $useoptionitemimages ){

	$sql = "SELECT COUNT(ec_optionitemimage.optionitemimage_id) as numrows FROM ec_optionitemimage WHERE ec_optionitemimage.optionitem_id = %s AND ec_optionitemimage.product_id = %s";
	$results = $wpdb->get_row( $wpdb->prepare( $sql, $optionitemid, $productid ) );
	if( $results->numrows == 0 ){
		
		$sql = "INSERT INTO ec_optionitemimage( ec_optionitemimage.optionitem_id, ec_optionitemimage.product_id ) VALUES( %s, %s )";
		$wpdb->query( $wpdb->prepare( $sql, $optionitemid, $productid ) );
			
	}

}

$user_sql = "SELECT  ec_user.*, ec_role.admin_access FROM ec_user LEFT JOIN ec_role ON (ec_user.user_level = ec_role.role_label) WHERE ec_user.password = %s AND  (ec_user.user_level = 'admin' OR ec_role.admin_access = 1)";
$users = $wpdb->get_results( $wpdb->prepare( $user_sql, $requestID ) );

if( !empty( $users ) ){

	$filename = $_FILES['Filedata']['name'];	
	$filetmpname = $_FILES['Filedata']['tmp_name'];	
	$fileType = $_FILES["Filedata"]["type"];
	$fileSizeMB = ($_FILES["Filedata"]["size"] / 1024 / 1000);

	$explodedfilename = pathinfo( $filename );
	$nameoffile = $explodedfilename['filename'];
	$fileextension = $explodedfilename['extension'];

	if(strtolower($fileextension)  == 'jpg' || strtolower($fileextension)  == 'jpeg' || strtolower($fileextension)  == 'gif' || strtolower($fileextension)  == 'png' || strtolower($fileextension)  == 'tiff') {
		//include( "resizer.php" );
	
		if( $imagenumber == '1' ){
	
			$move_results = move_uploaded_file( $_FILES['Filedata']['tmp_name'], "../../../../wp-easycart-data/products/pics1/".$nameoffile."_".$date.".".$fileextension );
			
			if( $useoptionitemimages ){
				$sql = "UPDATE ec_optionitemimage SET ec_optionitemimage.image1 = '".$nameoffile."_".$date.".".$fileextension."' WHERE ec_optionitemimage.product_id = '".$productid."' AND ec_optionitemimage.optionitem_id = '".$optionitemid."'";
				$wpdb->query( $sql );
	
			}else{
				
				if( $insertupdate == 'update' ){
					
					$sql = "UPDATE ec_product SET ec_product.image1 = '".$nameoffile."_".$date.".".$fileextension."' WHERE ec_product.product_id = ".$productid;
					$wpdb->query( $sql );
	
				}
	
			}
	
		}
	
		if( $imagenumber == '2' ){
	
			$move_results = move_uploaded_file($_FILES['Filedata']['tmp_name'], "../../../../wp-easycart-data/products/pics2/".$nameoffile."_".$date.".".$fileextension  );
			
			if( $useoptionitemimages ){
		
				$sql = "UPDATE ec_optionitemimage SET ec_optionitemimage.image2 = '".$nameoffile."_".$date.".".$fileextension."' WHERE ec_optionitemimage.product_id = '".$productid."' AND ec_optionitemimage.optionitem_id = '".$optionitemid."'";
				$wpdb->query( $sql );
	
			}else{
				
				if( $insertupdate == 'update' ){
					
					$sql = "UPDATE ec_product SET ec_product.image2 = '".$nameoffile."_".$date.".".$fileextension."' WHERE ec_product.product_id = ".$productid;
					$wpdb->query( $sql );
	
				}
	
			}
	
		}
	
		if( $imagenumber == '3' ){
	
			$move_results = move_uploaded_file($_FILES['Filedata']['tmp_name'], "../../../../wp-easycart-data/products/pics3/".$nameoffile."_".$date.".".$fileextension  );
			
			if( $useoptionitemimages ){
		
				$sql = "UPDATE ec_optionitemimage SET ec_optionitemimage.image3 = '".$nameoffile."_".$date.".".$fileextension."' WHERE ec_optionitemimage.product_id = '".$productid."' AND ec_optionitemimage.optionitem_id = '".$optionitemid."'";
				$wpdb->query( $sql );
	
			}else{
				
				if( $insertupdate == 'update' ){
					
					$sql = "UPDATE ec_product SET ec_product.image3 = '".$nameoffile."_".$date.".".$fileextension."' WHERE ec_product.product_id = ".$productid;
					$wpdb->query( $sql );
	
				}
	
			}
	
		}
	
		if( $imagenumber == '4' ){
	
			$move_results = move_uploaded_file($_FILES['Filedata']['tmp_name'], "../../../../wp-easycart-data/products/pics4/".$nameoffile."_".$date.".".$fileextension  );
			
			if( $useoptionitemimages ){
		
				$sql = "UPDATE ec_optionitemimage SET ec_optionitemimage.image4 = '".$nameoffile."_".$date.".".$fileextension."' WHERE ec_optionitemimage.product_id = '".$productid."' AND ec_optionitemimage.optionitem_id = '".$optionitemid."'";
				$wpdb->query( $sql );
	
			}else{
				
				if( $insertupdate == 'update' ){
					
					$sql = "UPDATE ec_product SET ec_product.image4 = '".$nameoffile."_".$date.".".$fileextension."' WHERE ec_product.product_id = ".$productid;
					$wpdb->query( $sql );
	
				}
	
			}
	
		}
	
		if( $imagenumber == '5' ){
	
			$move_results = move_uploaded_file($_FILES['Filedata']['tmp_name'], "../../../../wp-easycart-data/products/pics5/".$nameoffile."_".$date.".".$fileextension  );	
	
			if( $useoptionitemimages ){
		
				$sql = "UPDATE ec_optionitemimage SET ec_optionitemimage.image5 = '".$nameoffile."_".$date.".".$fileextension."' WHERE ec_optionitemimage.product_id = '".$productid."' AND ec_optionitemimage.optionitem_id = '".$optionitemid."'";
				$wpdb->query( $sql );
	
			}else{
				
				if( $insertupdate == 'update' ){
					
					$sql = "UPDATE ec_product SET ec_product.image5 = '".$nameoffile."_".$date.".".$fileextension."' WHERE ec_product.product_id = ".$productid;
					$wpdb->query( $sql );
	
				} 
	
			}
	
		}
	}

}

?>