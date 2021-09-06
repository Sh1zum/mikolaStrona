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


class resizer {

///////////////////////////////////////////////
//only used in swatch resizing for some reason
///////////////////////////////////////////////
	private $originalFile = '';

	public function __construct($originalFile = '') {

		$this -> originalFile = $originalFile;

	}

	public function resize($newWidth, $newHeight,  $targetFile, $quality) {

		if (empty($newWidth) || empty($newHeight) || empty($targetFile)) {

			return false;

		}

		$src = imagecreatefromjpeg($this -> originalFile);

		list($width, $height) = getimagesize($this -> originalFile);

		$tmp = @imagecreatetruecolor($newWidth, $newHeight);

		@imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

		if (file_exists($targetFile)) {

			unlink($targetFile);

		}

		imagejpeg($tmp, $targetFile, $quality); // 90 is my choice, make it between 0 – 100 for output image quality with 100 being the most luxurious

	}

}

?>