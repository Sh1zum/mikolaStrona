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

function imageResizer($url, $width, $height) {
	
	header('Content-type: image/jpeg');
	
	list($width_orig, $height_orig) = getimagesize($url);

	$ratio_orig = $width_orig/$height_orig;

	if ($width/$height > $ratio_orig) {
	  $width = $height*$ratio_orig;
	} else {
	  $height = $width/$ratio_orig;
	}

	// This resamples the image
	$image_p = imagecreatetruecolor($width, $height);
	$type = strtolower(substr(strrchr($url,"."),1));
	if($type == 'jpeg') $type = 'jpg';
	switch($type){
		case 'bmp': $image = imagecreatefromwbmp($url); break;
		case 'gif': $image = imagecreatefromgif($url); break;
		case 'jpg': $image = imagecreatefromjpeg($url); break;
		case 'png': $image = imagecreatefrompng($url); break;
		default : return "Unsupported picture type!";
	}
	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

	// Output the image
	imagejpeg($image_p, null, 100);
	
}

$image_width = 100;
$image_height = 100;
if( isset( $_GET['w'] ) )
	$image_width = $_GET['w'];
	
if( isset( $_GET['h'] ) )
	$image_height = $_GET['h'];

if( file_exists( '../../../../wp-easycart-data/'.$_GET['src'] ) )
	imageResizer( '../../../../wp-easycart-data/'.$_GET['src'], $image_width, $image_height );
else
	imageResizer( '../../../../wp-easycart/design/theme/base-responsive-v3/images/ec_image_not_found.jpg', $image_width, $image_height );
die( );
?>