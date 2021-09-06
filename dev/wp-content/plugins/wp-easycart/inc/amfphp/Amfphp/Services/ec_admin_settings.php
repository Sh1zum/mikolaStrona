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
class ec_admin_settings{		
	
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;
		
		if(!defined('WP_PREFIX')) define('WP_PREFIX', $wpdb->prefix);

	}	
	
	
	//secure all of the services for logged in authenticated users only	
	public function _getMethodRoles($methodName){
	   if ($methodName == 'gettimezones') return array('admin');
	   else if($methodName == 'getsitesettings') return array('admin');
	   else if($methodName == 'updatesitesettings') return array('admin');
	   else if($methodName == 'clearmenustatistics') return array('admin');
	   else if($methodName == 'clearproductstatistics') return array('admin');
	   else if($methodName == 'insertregcode') return array('admin');
	   else  return null;
	}
	
	function gettimezones() {
		  //Create SQL Query
		  $sql = "SELECT ec_timezone.* FROM ec_timezone ORDER BY ec_timezone.timezone_id ASC";
		  // Run query on database
		  $results = $this->db->get_results( $sql );
		  //if results, convert to an array for use in flash
		  if( count( $results ) > 0 ){
			  return $results;
		  }else{
			  return array( "noresults" );
		  }
	}
	
	//convert our max upload file string from 32M or 64M size to bytes
	function convertBytes( $value ) {
		if ( is_numeric( $value ) ) {
			return $value;
		} else {
			$value_length = strlen($value);
			$qty = substr( $value, 0, $value_length - 1 );
			$unit = strtolower( substr( $value, $value_length - 1 ) );
			switch ( $unit ) {
				case 'k':
					$qty *= 1024;
					break;
				case 'm':
					$qty *= 1048576;
					break;
				case 'g':
					$qty *= 1073741824;
					break;
			}
			return $qty;
		}
	}
	
	
	//site settings functions
	function getsitesettings() {
		
		if (WP_PREFIX) {
			$dbprefix = WP_PREFIX; //use special prefix
		} else {
			$dbprefix = 'wp_'; //else use default
		}
		if(ini_get('upload_max_filesize')) {
			$maxuploadsize = $this->convertBytes(ini_get('upload_max_filesize'));
		} else {
			$maxuploadsize = 10000000;
		}
		
		//Create SQL Query
		$query_settings_sql = "SELECT ec_setting.* FROM ec_setting WHERE ec_setting.setting_id = 1";
		$query_settings = $this->db->get_results( $query_settings_sql );
		$query_options_sql = "SELECT 
			wp_options0.option_value AS WPstorepage,
			wp_options1.option_value AS WP_currency_seperator,
			wp_options2.option_value AS WP_decimal_symbol,
			wp_options3.option_value AS WP_decimal_places,
			wp_options4.option_value AS WP_currency_symbol
			
			FROM
			".$dbprefix."options wp_options0, ".$dbprefix."options wp_options1, ".$dbprefix."options wp_options2, ".$dbprefix."options wp_options3, ".$dbprefix."options wp_options4 
			
			WHERE
			wp_options0.option_name = 'ec_option_storepage' AND
			wp_options1.option_name = 'ec_option_currency_thousands_seperator' AND
			wp_options2.option_name = 'ec_option_currency_decimal_symbol' AND
			wp_options3.option_name = 'ec_option_currency_decimal_places' AND
			wp_options4.option_name = 'ec_option_currency'";
			
			
			$query_options = $this->db->get_results( $query_options_sql );
		//return $query_options_sql;
		$row = $query_settings[0] ;
		if( $query_options && count( $query_options ) > 0 ){
			$row2 = $query_options ;
			$row->WPstorepage = $row2[0]->WPstorepage;
			$row->WP_currency_seperator = $row2[0]->WP_currency_seperator;
			$row->WP_decimal_symbol = $row2[0]->WP_decimal_symbol;
			$row->WP_decimal_places = $row2[0]->WP_decimal_places;
			$row->WP_currency_symbol = $row2[0]->WP_currency_symbol;
			$row->maxuploadsize = $maxuploadsize;
			$returnArray[] = $row;
			return($returnArray);
		} else {
			$row->WPstorepage = "";
			$row->WP_currency_seperator = "";
			$row->WP_decimal_symbol = "";
			$row->WP_decimal_places = "";
			$row->WP_currency_symbol = "";
			$row->maxuploadsize = $maxuploadsize;
			$returnArray[] = $row;
			return($returnArray);
		}
	}
	
	
	function updatesitesettings($settings) {
		
		$settings = (array)$settings;
		  
		$sql = "UPDATE ec_setting SET ec_setting.site_url='%s', ec_setting.timezone='%s'  WHERE ec_setting.setting_id = 1";
		$success = $this->db->query( $this->db->prepare( $sql, $settings['siteURL'],$settings['timezone'] ));
		
		return array( "success" );
		
	}		
	
	
	function clearmenustatistics() {
		//Create SQL Query
		$sql = "UPDATE ec_menulevel1, ec_menulevel2, ec_menulevel3 SET ec_menulevel1.clicks = 0, ec_menulevel2.clicks = 0, ec_menulevel3.clicks = 0";
		//Run query on database;
		$success = $this->db->query( $sql );
		//if no errors, return their current Client ID
		//if results, convert to an array for use in flash
		if( $success === FALSE ) {
			return array( "error" );
		}else{
			return array( "success" );
		}
	}	
	function clearproductstatistics() {
		//Create SQL Query
		$sql = "UPDATE ec_product SET ec_product.views = 0";
		//Run query on database;
		$success = $this->db->query( $sql );
		//if no errors, return their current Client ID
		//if results, convert to an array for use in flash
		if( $success === FALSE ) {
			return array( "error" );
		}else{
			return array( "success" );
		}
	}	
	
	function insertregcode($regcode) {
		//Create SQL Query
		$sql = "UPDATE ec_setting SET ec_setting.reg_code='%s'  WHERE ec_setting.setting_id = 1";
		
		//Run query on database;
		$success = $this->db->query( $this->db->prepare( $sql, $regcode));
		//if no errors, return their current Client ID
		//if results, convert to an array for use in flash
		if( $success === FALSE ) {
			return array( "error" );
		}else{
			return array( "success" );
		}
	}	
	
	
}//close class
?>