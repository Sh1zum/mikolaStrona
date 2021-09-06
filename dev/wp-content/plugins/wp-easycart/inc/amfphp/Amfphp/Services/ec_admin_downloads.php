<?php
/*
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//All Code and Design is copyrighted by Level Four Development, LLC
//
//Level Four Development, LLC provides this code "as is" without warranty of any kind, either express or implied,     
//including but not limited to the implied warranties of merchantability and/or fitness for a particular purpose.         
//
//Only licnesed users may use this code and storfront for live purposes. All other use is prohibited and may be 
//subject to copyright violation laws. If you have any questions regarding proper use of this code, please
//contact Level Four Development, llc and EasyCart prior to use.
//
//All use of this storefront is subject to our terms of agreement found on Level Four Development, LLC's  website.
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/

class ec_admin_downloads{		
	
	private $db;
	
	function __construct( ){
	
		global $wpdb;
		$this->db = $wpdb;

	}//ec_admin_downloads
	
	public function _getMethodRoles($methodName){
			 if( $methodName == 'getdownloads' ) 			return array( 'admin' );
		else if( $methodName == 'deletedownload' ) 			return array( 'admin' );
		else if( $methodName == 'updatedownload' ) 			return array( 'admin' );
		else if( $methodName == 'readdownloaddirectory' ) 	return array( 'admin' );
		else  												return null;
	}//_getMethodRoles
	
	function getdownloads( $startrecord, $limit, $orderby, $ordertype, $filter ){
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_download.*, UNIX_TIMESTAMP( ec_download.date_created ) AS date_created FROM ec_download WHERE ec_download.download_id != '' " . $filter . " ORDER BY " . $orderby . " " .  $ordertype . " LIMIT " . $startrecord . ", " . $limit;
		$results = $this->db->get_results( $sql );
		$totalquery = $this->db->get_var( "SELECT FOUND_ROWS( ) " );
		
		if( count( $results ) > 0 ){
			$results[0]->totalrows = $totalquery;
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}//getdownlods
	
	function deletedownload( $downloadid ){
		$sql = "DELETE FROM ec_download WHERE ec_download.download_id = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $downloadid ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
	
	}//deletedownload
	
	function updatedownload( $download){
		
		$newdownloaddate = (array)$download;
		
		if( $newdownloaddate['order_date'] != '' ){
			$unixexpirationdate = $newdownloaddate['order_date']->timeStamp / 1000;
			$neworderdate = date("'Y-m-d H:i:s'", strtotime("midnight", $unixexpirationdate));
		}else{
			$neworderdate = 'NULL';
		}
		//return $neworderdate;
		 
		$sql = "UPDATE ec_download SET ec_download.download_count = %s, ec_download.download_file_name = %s, ec_download.is_amazon_download = %s,  ec_download.amazon_key = %s,  ec_download.date_created = $neworderdate WHERE ec_download.download_id = %s";
		$this->db->query( $this->db->prepare( $sql, $download->totaldownloads, $download->downloadproductid, $download->is_amazon_download, $download->amazon_key,  $download->uniqueid) );
		
		$sql = "UPDATE ec_orderdetail SET ec_orderdetail.download_file_name = %s, ec_orderdetail.is_amazon_download = %s, ec_orderdetail.amazon_key = %s WHERE ec_orderdetail.download_key = %s";
		$this->db->query( $this->db->prepare( $sql, $download->downloadproductid, $download->is_amazon_download, $download->amazon_key, $download->uniqueid ) );
		
		return array( "success" );
		
	}//updatedownload
	
	function readdownloaddirectory( ){ 
		
		$listDir = array( ); 
		$dir = "../../../../wp-easycart-data/products/downloads";
		if( $handler = opendir( $dir ) ){ 
			while( ( $sub = readdir( $handler ) ) !== FALSE ){ 
				if( $sub != "." && $sub != ".." && $sub != "Thumb.db" && $sub != "_notes" && $sub != ".htaccess" ){ 
					if( is_file( $dir . "/" . $sub ) ){ 
						$listDir[] = $sub; 
					}
				} 
			}
			closedir( $handler ); 
		} 
		return $listDir;    
	}//readdwonloaddirectory

}//ec_admin_downloads
?>