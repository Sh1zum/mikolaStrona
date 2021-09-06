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

class ec_admin_shipping{
	
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

		if( !defined( 'WP_PREFIX' ) ) define( 'WP_PREFIX', $wpdb->prefix );

	}
	
	//secure all of the services for logged in authenticated users only	
	public function _getMethodRoles( $methodName ){
	   if( 		$methodName == 'canadaposttest' ) 				return array('admin');
	   else if( $methodName == 'upstest' ) 						return array('admin');
	   else if( $methodName == 'uspstest') 						return array('admin');
	   else if( $methodName == 'fedextest') 					return array('admin');
	   else if( $methodName == 'dhltest') 						return array('admin');
	   else if( $methodName == 'ausposttest') 					return array('admin');
	   else if( $methodName == 'getshippingsettings') 			return array('admin');
	   else if( $methodName == 'updateshippingmethodsetting') 	return array('admin');
	   else if( $methodName == 'updateshippingsettings') 		return array('admin');
	   else if( $methodName == 'getups') 						return array('admin');
	   else if( $methodName == 'deleteups') 					return array('admin');
	   else if( $methodName == 'updateups') 					return array('admin');
	   else if( $methodName == 'addups') 						return array('admin');
	   else if( $methodName == 'getusps') 						return array('admin');
	   else if( $methodName == 'deleteusps') 					return array('admin');
	   else if( $methodName == 'updateusps') 					return array('admin');
	   else if( $methodName == 'addusps') 						return array('admin');
	   else if( $methodName == 'getfedex') 						return array('admin');
	   else if( $methodName == 'deletefedex') 					return array('admin');
	   else if( $methodName == 'updatefedex') 					return array('admin');
	   else if( $methodName == 'addfedex') 						return array('admin');
	   else if( $methodName == 'getauspost') 					return array('admin');
	   else if( $methodName == 'deleteauspost') 				return array('admin');
	   else if( $methodName == 'updateauspost') 				return array('admin');
	   else if( $methodName == 'addauspost') 					return array('admin');
	   else if( $methodName == 'getdhl') 						return array('admin');
	   else if( $methodName == 'deletedhl') 					return array('admin');
	   else if( $methodName == 'updatedhl') 					return array('admin');
	   else if( $methodName == 'adddhl') 						return array('admin');
	   else if( $methodName == 'getcanadapost') 				return array('admin');
	   else if( $methodName == 'deletecanadapost') 				return array('admin');
	   else if( $methodName == 'updatecanadapost') 				return array('admin');
	   else if( $methodName == 'addcanadapost') 				return array('admin');
	   else if( $methodName == 'updateexpeditedrates') 			return array('admin');
	   else if( $methodName == 'getmethodshippingrates') 		return array('admin');
	   else if( $methodName == 'deleteshippingmethodrate') 		return array('admin');
	   else if( $methodName == 'updateshippingmethodrate') 		return array('admin');
	   else if( $methodName == 'addshippingmethodrate') 		return array('admin');
	   else if( $methodName == 'getweightshippingrates') 		return array('admin');
	   else if( $methodName == 'deleteshippingweightrate') 		return array('admin');
	   else if( $methodName == 'updateshippingweightrate') 		return array('admin');
	   else if( $methodName == 'addshippingweightrate') 		return array('admin');
	   else if( $methodName == 'getpriceshippingrates') 		return array('admin');
	   else if( $methodName == 'deleteshippingpricerate') 		return array('admin');
	   else if( $methodName == 'updateshippingpricerate') 		return array('admin');
	   else if( $methodName == 'addshippingpricerate') 			return array('admin');
	   else if( $methodName == 'getquantityshippingrates') 		return array('admin');
	   else if( $methodName == 'deleteshippingquantityrate') 	return array('admin');
	   else if( $methodName == 'updateshippingquantityrate') 	return array('admin');
	   else if( $methodName == 'addshippingquantityrate') 		return array('admin');
	   else if( $methodName == 'getzonedetails') 				return array('admin');
	   else if( $methodName == 'deletezonedetails') 			return array('admin');
	   else if( $methodName == 'insertzonedetails') 			return array('admin');
	   else if( $methodName == 'getshippingzones') 				return array('admin');
	   else if( $methodName == 'deleteshippingzone') 			return array('admin');
	   else if( $methodName == 'insertshippingzone') 			return array('admin');
	   else 													return NULL;
	
	}
	
	/////////////////////////////////////////////////////////////////////////////////
	//Shipping Testers
	/////////////////////////////////////////////////////////////////////////////////
	function canadaposttest() {
		//test function for canada post
		$db = new ec_db_admin( );
		$setting_row = $db->get_settings( );
		$settings = new ec_setting( $setting_row );
		
		if( $setting_row->canadapost_username && $setting_row->canadapost_password && $setting_row->canadapost_customer_number && $setting_row->canadapost_ship_from_zip ){
			
			if( !class_exists( "ec_shipper" ) ){
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_shipper.php' );
			}
			if( !class_exists( "ec_canadapost" ) ){
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_canadapost.php' );
			}
			
			$canadapost = new ec_canadapost( $settings );
			$message = $canadapost->get_rate_test( "DOM.PC", $setting_row->canada_ship_from_zip, "CA", "1" );
			
			if( $message == "SUCCESS" ){
				$result = 1;
			}else{
				$result = 3;
			}
		}else{
			$result = 3;
			$message = "Missing required credentials";
		}
		
		$finalresults = new StdClass;
		if( $result == 1 ){
			//if success (green light)
			$finalresults->success_code = 1;
			$finalresults->success_message = 'success';
		}else if( $result == 2 ){	
			//if problem (yellow light)
			$finalresults->success_code = 2;
			$finalresults->success_message = "Error occured with the response: " . $message;
		}else if( $result == 3 ) {	
			//if error (red light)
			$finalresults->success_code = 3;
			$finalresults->success_message = "Error occured with the response: " . $message;
		}
		$returnArray[] = $finalresults;
		return $returnArray;
		
	}
	
	
	function upstest( ){
		
		$db = new ec_db_admin( );
		$setting_row = $db->get_settings( );
		$settings = new ec_setting( $setting_row );
		
		$message = "";
		
		if( $setting_row->ups_access_license_number && $setting_row->ups_user_id && $setting_row->ups_password && $setting_row->ups_ship_from_zip && $setting_row->ups_shipper_number && $setting_row->ups_country_code && $setting_row->ups_weight_type ){
			
			if( !class_exists( "ec_shipper" ) ){
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_shipper.php' );
			}
			if( !class_exists( "ec_ups" ) ){
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_ups.php' );
			}
			
			// Run test of the settings
			$ups_class = new ec_ups( $settings );
			$ups_response = $ups_class->get_rate_test( "01", $setting_row->ups_ship_from_zip, $setting_row->ups_country_code, "1" );
			$ups_xml = new SimpleXMLElement($ups_response);
			
			if( $ups_xml->Response->ResponseStatusCode == "1" ){
				$result = 1;
			}else if( $ups_xml->Response->Error->ErrorCode == "111210" ){
				$result = 3;
				$message = "The zip + country combination you have entered as your ship from location is invalid.";
			}else{
				$result = 3;
				$message = (string) $ups_xml->Response->Error->ErrorDescription[0];
			}
			
		}else{
			$result = 3;
			$message = "You are missing some of the required settings. Please ensure you have something entered for the license number, user id, password, postal code, and shipper number.";
		}	
		
		if( $setting_row->ups_conversion_rate <= 0 ){
			$result = 2;
			$message = "You have the conversion rate set to zero or less, which is typically an invalid value. This will return zero or less shipping values every time.";
		}
			
		$finalresults = new StdClass;
		if( $result == 1 ){
			//if success (green light)
			$finalresults->success_code = 1;
			$finalresults->success_message = 'success';
		}else if( $result == 2 ){	
			//if problem (yellow light)
			$finalresults->success_code = 2;
			$finalresults->success_message = $message;
		}else if( $result == 3 ) {	
			//if error (red light)
			$finalresults->success_code = 3;
			$finalresults->success_message = $message;
		}
		$returnArray[] = $finalresults;
		return $returnArray;
	
	}
	
	function uspstest( ){
		
		$db = new ec_db_admin( );
		$setting_row = $db->get_settings( );
		$settings = new ec_setting( $setting_row );
		$message = "";
		
		if( $setting_row->usps_user_name && $setting_row->usps_ship_from_zip ){
			
			if( !class_exists( "ec_shipper" ) ){
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_shipper.php' );
			}
			if( !class_exists( "ec_usps" ) ){
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_usps.php' );
			}
			
			$usps_class = new ec_usps( $settings );
			$usps_response = $usps_class->get_rate_test( "PRIORITY", $setting_row->usps_ship_from_zip, "US", "1" );
			$usps_xml = new SimpleXMLElement( $usps_response );
			
			if( $usps_xml->Number ){
				$result = 3;
				$message = (string) $usps_xml->Description;
			}else if( $usps_xml->Package[0]->Error ){
				$result = 3;
				$message = (string) $usps_xml->Package[0]->Error->Description[0];
			}else{
				$result = 1;
			}
		}else{
			$result = 3;
			$message = "You are missing some of the required settings. Please ensure you have something entered for the user name and ship from postal code.";
		}
		
		$finalresults = new StdClass;	
		if( $result == 1 ){
			//if success (green light)
			$finalresults->success_code = 1;
			$finalresults->success_message = 'success';
		}else if( $result == 2 ){	
			//if problem (yellow light)
			$finalresults->success_code = 2;
			$finalresults->success_message = 'There was a problem with your configuration or transmitting.';
		}else if( $result == 3 ){	
			//if error (red light)
			$finalresults->success_code = 3;
			$finalresults->success_message = $message;
		}
		$returnArray[] = $finalresults;
		return $returnArray;
	
	}
	
	function fedextest( ){
		
		$db = new ec_db_admin( );
		$setting_row = $db->get_settings( );
		$settings = new ec_setting( $setting_row );
	
		if( $setting_row->fedex_key && $setting_row->fedex_account_number && $setting_row->fedex_meter_number && $setting_row->fedex_password && $setting_row->fedex_ship_from_zip && $setting_row->fedex_weight_units && $setting_row->fedex_country_code ){
			
			if( !class_exists( "ec_shipper" ) ){
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_shipper.php' );
			}
			if( !class_exists( "ec_fedex" ) ){
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_fedex.php' );
			}
			
			$fedex_class = new ec_fedex( $settings );
			$fedex_response = $fedex_class->get_rate_test( "FEDEX_GROUND", $setting_row->fedex_ship_from_zip, $setting_row->fedex_country_code, "1" );
			
			if( $fedex_response->HighestSeverity == 'FAILURE' || $fedex_response->HighestSeverity == 'ERROR' || $fedex_response->HighestSeverity == 'WARNING' ){
				if( isset( $fedex_response->Notifications ) ){
					$result = 3;
					
					if( $fedex_response->Notifications->Code == "1000" )
						$message = "FedEx returned an authentication error, meaning your access key + password was not a valid login in their system. It could also mean that you have or have not checked the test mode option to match with the account you are using.";
					else if( $fedex_response->Notifications->Code == "556" )
						$message = "There are no available services from the selected postal code + country. Likely you have an invalid postal code or have not selected the correct country to match.";
					else if( $fedex_response->Notifications->Code == "803" )
						$message = "FedEx has told us the meter number you have entered is incorrect.";
					else if( $fedex_response->Notifications->Code == "860" )
						$message = "FedEx has told us the account number you have entered is incorrect.";
					else
						$message = print_r( $fedex_response->Notifications, true );
				
				}else{
					$result = 3;
					$message= "Unknown error occurred.";
				}
			}else{
				$result = 1;
			}
		}else{
			$result = 3;
			$message = "You are missing some of the required settings. Please ensure you have something entered for the access key, account number, meter number, postal code, and password.";
		}
		
		if( $setting_row->fedex_conversion_rate <= 0 ){
			$result = 2;
			$message = "You have the conversion rate set to zero or less, which is typically an invalid value. This will return zero or less shipping values every time.";
		}
			
		$finalresults = new StdClass;	
		if( $result == 1 ){
			//if success (green light)
			$finalresults->success_code = 1;
			$finalresults->success_message = 'success';
		}else if( $result == 2 ){	
			//if problem (yellow light)
			$finalresults->success_code = 2;
			$finalresults->success_message = $message;
		}else if( $result == 3 ){	
			//if error (red light)
			$finalresults->success_code = 3;
			$finalresults->success_message = $message;
		}
		$returnArray[] = $finalresults;
		return $returnArray;
	}
	
	function dhltest( ){
		
		$db = new ec_db_admin( );
		$setting_row = $db->get_settings( );
		$settings = new ec_setting( $setting_row );
		
		if( $setting_row->dhl_site_id && $setting_row->dhl_password && $setting_row->dhl_ship_from_country && $setting_row->dhl_ship_from_zip && $setting_row->dhl_weight_unit ){
			
			if( !class_exists( "ec_shipper" ) ){
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_shipper.php' );
			}
			if( !class_exists( "ec_dhl" ) ){
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_dhl.php' );
			}
			
			$dhl_class = new ec_dhl( $settings );
			$dhl_response = $dhl_class->get_rate_test( "N", $setting_row->dhl_ship_from_zip, $setting_row->dhl_ship_from_country, "1" );
			$dhl_xml = new SimpleXMLElement( $dhl_response );
			
			if( $dhl_xml && $dhl_xml->Response && $dhl_xml->Response->Status && $dhl_xml->Response->Status->ActionStatus && $dhl_xml->Response->Status->ActionStatus == "Error" ){
				$result = 3;
				if( $dhl_xml->Response->Status->Condition->ConditionCode == '100' ){
					$message = "DHL failed because the site ID and/or password provided is incorrect.";
				}else{
					$message = print_r( $dhl_xml->Response->Status->Condition, true );
				}
			}else if( $dhl_xml && $dhl_xml->GetQuoteResponse && $dhl_xml->GetQuoteResponse->Note && $dhl_xml->GetQuoteResponse->Note->Condition ){
				$result = 3;
				$message = ( string ) $dhl_xml->GetQuoteResponse->Note->Condition->ConditionData;
			}else{
				$result = 1;
			}
		}else{
			$result = 3;
			$message = "You are missing some of the required settings. Please ensure you have something entered for the Site ID, Password, and Postal Code.";
		}
		
		$finalresults = new StdClass;
		if( $result == 1 ){
			//if success (green light)
			$finalresults->success_code = 1;
			$finalresults->success_message = 'success';
		}else if( $result == 2 ){	
			//if problem (yellow light)
			$finalresults->success_code = 2;
			$finalresults->success_message = 'There was a problem with your configuration or transmitting.';
		}else if( $result == 3 ){	
			//if error (red light)
			$finalresults->success_code = 3;
			$finalresults->success_message = $message;
		}
		$returnArray[] = $finalresults;
		return $returnArray;
		
	}
	
	function ausposttest( ){
		
		$db = new ec_db_admin( );
		$setting_row = $db->get_settings( );
		$settings = new ec_setting( $setting_row );
	
		if( $setting_row->auspost_api_key && $setting_row->auspost_ship_from_zip ){
			
			if( !class_exists( "ec_shipper" ) ){
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_shipper.php' );
			}
			if( !class_exists( "ec_auspost" ) ){
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_auspost.php' );
			}
			
			$auspost_class = new ec_auspost( $settings );
			$auspost_response = $auspost_class->get_rate_test( "AUS_PARCEL_EXPRESS", $setting_row->auspost_ship_from_zip, "AU", "1" );
			
			if( !$auspost_response ){
				$result = 3;
				$message = "No response was returned from Australia Post, this means your key is incorrect or the postal code entered is not a valid Australian postal code.";
			}else
				$result = 1;
		}else{
			$result = 3;
			$message = "You are missing some of the required settings. Please ensure you have something entered for the API key and the postal code.";
		}
			
		$finalresults = new StdClass;	
		if( $result == 1 ){
			//if success (green light)
			$finalresults->success_code = 1;
			$finalresults->success_message = 'success';
		}else if( $result == 2 ){	
			//if problem (yellow light)
			$finalresults->success_code = 2;
			$finalresults->success_message = 'There was a problem with your configuration or transmitting.';
		}else if( $result == 3 ){	
			//if error (red light)
			$finalresults->success_code = 3;
			$finalresults->success_message = $message;
		}
		$returnArray[] = $finalresults;
		return $returnArray;
		
	}
	
	/////////////////////////////////////////////////////////////////////////////////
	//Shipping Zones 
	/////////////////////////////////////////////////////////////////////////////////
	function getzonedetails( $zone_id ){
		  
		  $sql = "SELECT 
					 a.*,
						b.*,
						c.*
						
					FROM 
					 ec_zone_to_location as a
						LEFT JOIN ec_country as b ON ( a.`iso2_cnt` = b.`iso2_cnt` )
						LEFT JOIN ec_state as c ON ( a.`code_sta` = c.`code_sta` AND b.`id_cnt` = c.`idcnt_sta` )
						
					WHERE 
					 a.zone_id = %s
					
					ORDER BY 
					 b.name_cnt";
		  
		  $results = $this->db->get_results( $this->db->prepare( $sql, $zone_id ) );
		  
		  if( !empty( $results ) ){
			  return $results;
		  }else{
			  return array( "noresults" );
		  }
		  
	}
	
	function deletezonedetails($keyfield, $zone_id) {
		
		$deletesql = "DELETE FROM ec_zone_to_location WHERE ec_zone_to_location.zone_to_location_id = '".$keyfield."'";
		$rows_affected = $result = $this->db->query( $deletesql );
		
		if( $rows_affected ){
		
			$sql = "SELECT a.*, b.*, c.*
					FROM ec_zone_to_location as a
					LEFT JOIN ec_country as b ON ( a.`iso2_cnt` = b.`iso2_cnt` )
					LEFT JOIN ec_state as c ON ( a.`code_sta` = c.`code_sta` AND b.`id_cnt` = c.`idcnt_sta` )
					WHERE a.zone_id = %s
					ORDER BY b.name_cnt";
		
			$results = $this->db->get_results( $this->db->prepare( $sql, $zone_id ));
		
			if( !empty( $results ) ){
				return $results;
			}else{
				return array( "noresults" );
			}
		}else{
			return array( "error" );
		}
		
	}

	function insertzonedetails($zone_id, $zonecountry, $zonestate) {
		
		$sql = "INSERT INTO ec_zone_to_location(ec_zone_to_location.zone_to_location_id, ec_zone_to_location.zone_id, ec_zone_to_location.iso2_cnt, ec_zone_to_location.code_sta)values(null, %s, %s, %s)";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $zone_id, $zonecountry, $zonestate));
		
		if( $rows_affected ){
			
			$sql = "SELECT a.*, b.*, c.*
					FROM ec_zone_to_location as a
					LEFT JOIN ec_country as b ON ( a.`iso2_cnt` = b.`iso2_cnt` )
					LEFT JOIN ec_state as c ON ( a.`code_sta` = c.`code_sta` AND b.`id_cnt` = c.`idcnt_sta` )
					WHERE a.zone_id = %s
					ORDER BY b.name_cnt";
			$results = $this->db->get_results( $this->db->prepare(  $sql, $zone_id));
			
			if( !empty( $results ) ){
				return $results;
			}else{
				return array( "noresults" );
			}
		
		}else{
			return array( "error" );
		}
		
	}
	
	function getshippingzones( ){
		
		$sql = "SELECT ec_zone.* FROM ec_zone ORDER BY ec_zone.zone_id ASC";
		$results = $this->db->get_results( $sql );
		
		if( !empty( $results ) ){
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}
	
	function deleteshippingzone( $keyfield ){
		
		$deletesql = "DELETE FROM ec_zone WHERE ec_zone.zone_id = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $deletesql, $keyfield));
		
		$deletesql = "DELETE FROM ec_zone_to_location WHERE ec_zone_to_location.zone_id = %s";
		$this->db->query( $this->db->prepare( $deletesql, $keyfield));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}

	function insertshippingzone( $zonename ){
		
		$sql = "INSERT INTO ec_zone(ec_zone.zone_id, ec_zone.zone_name) values(null, %s)";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $zonename));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}

	/////////////////////////////////////////////////////////////////////////////////
	//Shipping Settings
	/////////////////////////////////////////////////////////////////////////////////
	function getshippingsettings( ){
		  
		  $sql = "SELECT SQL_CALC_FOUND_ROWS ec_setting.shipping_method, ec_setting.shipping_expedite_rate, ec_setting.shipping_handling_rate, ec_setting.ups_access_license_number, ec_setting.ups_user_id, ec_setting.ups_password, ec_setting.ups_ship_from_zip, ec_setting.ups_shipper_number, ec_setting.ups_country_code, ec_setting.ups_weight_type, ec_setting.ups_conversion_rate, ec_setting.usps_user_name, ec_setting.usps_ship_from_zip, ec_setting.fedex_key, ec_setting.fedex_account_number, ec_setting.fedex_meter_number, ec_setting.fedex_password, ec_setting.fedex_ship_from_zip, ec_setting.fedex_weight_units, ec_setting.fedex_country_code, ec_setting.fedex_conversion_rate, ec_setting.fedex_test_account, ec_setting.auspost_api_key, ec_setting.auspost_ship_from_zip, ec_setting.dhl_site_id, ec_setting.dhl_password, ec_setting.dhl_ship_from_country, ec_setting.dhl_ship_from_zip, ec_setting.dhl_weight_unit, ec_setting.dhl_test_mode, ec_setting.fraktjakt_customer_id, ec_setting.fraktjakt_login_key, ec_setting.fraktjakt_conversion_rate, ec_setting.fraktjakt_test_mode, ec_setting.fraktjakt_address, ec_setting.fraktjakt_city, ec_setting.fraktjakt_state, ec_setting.fraktjakt_zip, ec_setting.fraktjakt_country, ec_setting.canadapost_username, ec_setting.canadapost_password, ec_setting.canadapost_customer_number, ec_setting.canadapost_contract_id, ec_setting.canadapost_test_mode, ec_setting.canadapost_ship_from_zip FROM ec_setting  WHERE ec_setting.setting_id = 1";
		  $results = $this->db->get_results( $sql );
		  $totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );

		  
		  //if results, convert to an array for use in flash
		  if( !empty( $results ) ){
			  $results[0]->totalrows = $totalrows;
			  return $results;
		  }else{
			  return array( "noresults" );
		  }
		  
	}
	
	function updateshippingmethodsetting( $shippingmethod, $handlingcharge ){
		
		$sql = "UPDATE ec_setting SET ec_setting.shipping_method=%s, ec_setting.shipping_handling_rate=%s WHERE ec_setting.setting_id = 1";
		$this->db->query( $this->db->prepare( $sql, $shippingmethod, $handlingcharge ) );
		
		return array( "success" );
		
	}	 
	
	function updateshippingsettings($shippingsettings) {
		
		$shippingsettings = (array)$shippingsettings;
		$sql = "UPDATE ec_setting SET ec_setting.shipping_method=%s, ec_setting.shipping_handling_rate=%s, ec_setting.ups_access_license_number=%s, ec_setting.ups_user_id=%s, ec_setting.ups_password=%s, ec_setting.ups_ship_from_zip=%s, ec_setting.ups_shipper_number=%s, ec_setting.ups_country_code=%s, ec_setting.ups_weight_type=%s, ec_setting.ups_conversion_rate =%s, ec_setting.usps_user_name=%s, ec_setting.usps_ship_from_zip=%s, ec_setting.fedex_key=%s, ec_setting.fedex_account_number=%s, ec_setting.fedex_meter_number=%s, ec_setting.fedex_password=%s, ec_setting.fedex_ship_from_zip=%s, ec_setting.fedex_weight_units=%s, ec_setting.fedex_country_code=%s,  ec_setting.fedex_conversion_rate =%s, ec_setting.fedex_test_account=%s, ec_setting.auspost_api_key = %s, ec_setting.auspost_ship_from_zip = %s , ec_setting.dhl_site_id = %s, ec_setting.dhl_password = %s, ec_setting.dhl_ship_from_country = %s, ec_setting.dhl_ship_from_zip = %s, ec_setting.dhl_weight_unit = %s, ec_setting.dhl_test_mode = %s, ec_setting.fraktjakt_customer_id = %s, ec_setting.fraktjakt_login_key = %s, ec_setting.fraktjakt_conversion_rate = %s, ec_setting.fraktjakt_test_mode = %s, ec_setting.fraktjakt_address = %s, ec_setting.fraktjakt_city = %s, ec_setting.fraktjakt_state = %s, ec_setting.fraktjakt_zip = %s, ec_setting.fraktjakt_country = %s, ec_setting.canadapost_username = %s, ec_setting.canadapost_password = %s, ec_setting.canadapost_customer_number = %s, ec_setting.canadapost_contract_id = %s, ec_setting.canadapost_test_mode = %s, ec_setting.canadapost_ship_from_zip = %s WHERE ec_setting.setting_id = 1";
		$this->db->query( $this->db->prepare( $sql, 
					$shippingsettings['shippingmethod'], 
					$shippingsettings['handlingcharge'], 
					$shippingsettings['ups_access_license_number'], 
					$shippingsettings['ups_user_id'],  
					$shippingsettings['ups_password'], 
					$shippingsettings['ups_ship_from_zip'], 
					$shippingsettings['ups_shipper_number'], 
					$shippingsettings['ups_country_code'], 
					$shippingsettings['ups_weight_type'],
					$shippingsettings['ups_conversion_rate'],
					$shippingsettings['usps_user_name'], 
					$shippingsettings['usps_ship_from_zip'],
					$shippingsettings['fedex_key'], 
					$shippingsettings['fedex_account_number'], 
					$shippingsettings['fedex_meter_number'], 
					$shippingsettings['fedex_password'], 
					$shippingsettings['fedex_ship_from_zip'],
					$shippingsettings['fedex_weight_units'], 
					$shippingsettings['fedex_country_code'],
					$shippingsettings['fedex_conversion_rate'],
					$shippingsettings['fedex_test_account'],
					$shippingsettings['auspost_api_key'], 
					$shippingsettings['auspost_ship_from_zip'],
					$shippingsettings['dhl_site_id'], 
					$shippingsettings['dhl_password'],
					$shippingsettings['dhl_ship_from_country'], 
					$shippingsettings['dhl_ship_from_zip'],
					$shippingsettings['dhl_weight_unit'],
					$shippingsettings['dhl_test_mode'],
					$shippingsettings['fj_customerid'],
					$shippingsettings['fj_loginkey'],
					$shippingsettings['fj_conversionrate'],
					$shippingsettings['fj_testmode'],
					$shippingsettings['fj_address'],
					$shippingsettings['fj_city'],
					$shippingsettings['fj_state'],
					$shippingsettings['fj_zip'],
					$shippingsettings['fj_country'],
					$shippingsettings['canadapost_username'],
					$shippingsettings['canadapost_password'],
					$shippingsettings['canadapost_customer_number'],
					$shippingsettings['canadapost_contract_id'],
					$shippingsettings['canadapost_test_mode'],
					$shippingsettings['canadapost_ship_from_zip']));
		
		return array( "success" );
		
	}
	
	/////////////////////////////////////////////////////////////////////////////////
	//DHL BASED SHIPPING
	/////////////////////////////////////////////////////////////////////////////////
	function getdhl( ){
		  
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_shippingrate.* FROM ec_shippingrate WHERE ec_shippingrate.is_dhl_based = 1 ORDER BY ec_shippingrate.shipping_order ASC";
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( !empty( $results ) ){
			$results[0]->totalrows = $totalrows;
			return $results;
		}else{
			return array( "noresults" );
		}
		  
	}
	
	function deletedhl( $keyfield ){
		
		$deletesql = "DELETE FROM ec_shippingrate WHERE ec_shippingrate.shippingrate_id = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $deletesql, $keyfield));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function updatedhl( $keyfield, $info ){
		
		$info = (array)$info;
		
		if( $info['shippingoverride'] != '' ){
			$sql = "UPDATE ec_shippingrate SET ec_shippingrate.shipping_label = %s, ec_shippingrate.shipping_code = %s, ec_shippingrate.shipping_order = %d, ec_shippingrate.shipping_override_rate = %s, ec_shippingrate.zone_id = %d, ec_shippingrate.free_shipping_at = %s WHERE ec_shippingrate.shippingrate_id = %d";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'],$info['shippingcode'], $info['shipping_order'], $info['shippingoverride'], $info['zoneid'], $info['free_shipping_at'], $keyfield ) );
		
		}else{
			$sql = "UPDATE ec_shippingrate SET ec_shippingrate.shipping_label = %s, ec_shippingrate.shipping_code = %s, ec_shippingrate.shipping_order = %d, ec_shippingrate.shipping_override_rate = NULL, ec_shippingrate.zone_id = %d, ec_shippingrate.free_shipping_at = %s WHERE ec_shippingrate.shippingrate_id = %d";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['zoneid'], $info['free_shipping_at'], $keyfield ) );
		
		}
		
		return array( "success" );
		
	}
	
	function adddhl($info) {
		
		$info = (array)$info;
		
		if( $info['shippingoverride'] != '' ){
			
			$sql = "INSERT INTO ec_shippingrate( ec_shippingrate.shipping_label, ec_shippingrate.shipping_code, ec_shippingrate.shipping_order, ec_shippingrate.shipping_override_rate, ec_shippingrate.is_dhl_based, ec_shippingrate.zone_id, ec_shippingrate.free_shipping_at ) VALUES( %s, %s, %d, %s, 1, %d, %s )";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['shippingoverride'], $info['zoneid'], $info['free_shipping_at'] ) );
		
		}else{
			
			$sql = "INSERT INTO ec_shippingrate( ec_shippingrate.shipping_label, ec_shippingrate.shipping_code, ec_shippingrate.shipping_order, ec_shippingrate.is_dhl_based, ec_shippingrate.zone_id, ec_shippingrate.free_shipping_at ) VALUES( %s, %s, %d, 1, %d, %s )";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['zoneid'], $info['free_shipping_at'] ) );
		
		}
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	/////////////////////////////////////////////////////////////////////////////////
	//CANADA POST BASED SHIPPING
	/////////////////////////////////////////////////////////////////////////////////
	function getcanadapost( ){
		  
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_shippingrate.* FROM ec_shippingrate WHERE ec_shippingrate.is_canadapost_based = 1 ORDER BY ec_shippingrate.shipping_order ASC";
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( !empty( $results ) ){
			$results[0]->totalrows = $totalrows;
			return $results;
		}else{
			return array( "noresults" );
		}
		  
	}
	
	function deletecanadapost( $keyfield ){
		
		$deletesql = "DELETE FROM ec_shippingrate WHERE ec_shippingrate.shippingrate_id = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $deletesql, $keyfield));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function updatecanadapost ($keyfield, $info ){
		
		$info = (array)$info;
		
		if( $info['shippingoverride'] != '' ){
			$sql = "UPDATE ec_shippingrate SET ec_shippingrate.shipping_label = %s, ec_shippingrate.shipping_code = %s, ec_shippingrate.shipping_order = %d, ec_shippingrate.shipping_override_rate = %s, ec_shippingrate.zone_id = %d, ec_shippingrate.free_shipping_at = %s WHERE ec_shippingrate.shippingrate_id = %d";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'],$info['shippingcode'], $info['shipping_order'], $info['shippingoverride'], $info['zoneid'], $info['free_shipping_at'], $keyfield ) );
		
		}else{
			$sql = "UPDATE ec_shippingrate SET ec_shippingrate.shipping_label = %s, ec_shippingrate.shipping_code = %s, ec_shippingrate.shipping_order = %d, ec_shippingrate.shipping_override_rate = NULL, ec_shippingrate.zone_id = %d, ec_shippingrate.free_shipping_at = %s WHERE ec_shippingrate.shippingrate_id = %d";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['zoneid'], $info['free_shipping_at'], $keyfield ) );
		
		}
		
		return array( "success" );
		
	}
	
	function addcanadapost($info) {
		
		$info = (array)$info;
		
		if( $info['shippingoverride'] != '' ){
			
			$sql = "INSERT INTO ec_shippingrate( ec_shippingrate.shipping_label, ec_shippingrate.shipping_code, ec_shippingrate.shipping_order, ec_shippingrate.shipping_override_rate, ec_shippingrate.is_canadapost_based, ec_shippingrate.zone_id, ec_shippingrate.free_shipping_at ) VALUES( %s, %s, %d, %s, 1, %d, %s )";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['shippingoverride'], $info['zoneid'], $info['free_shipping_at'] ) );
		
		}else{
			
			$sql = "INSERT INTO ec_shippingrate( ec_shippingrate.shipping_label, ec_shippingrate.shipping_code, ec_shippingrate.shipping_order, ec_shippingrate.is_canadapost_based, ec_shippingrate.zone_id, ec_shippingrate.free_shipping_at ) VALUES( %s, %s, %d, 1, %d, %s )";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['zoneid'], $info['free_shipping_at'] ) );
		
		}
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	
	/////////////////////////////////////////////////////////////////////////////////
	//AUS POST BASED SHIPPING
	/////////////////////////////////////////////////////////////////////////////////
	function getauspost( ){

		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_shippingrate.* FROM ec_shippingrate WHERE ec_shippingrate.is_auspost_based = 1 ORDER BY ec_shippingrate.shipping_order ASC";
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( $totalrows ){
			$results[0]->totalrows = $totalrows;
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}
	
	function deleteauspost( $keyfield ){
		
		$deletesql = "DELETE FROM ec_shippingrate WHERE ec_shippingrate.shippingrate_id = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $deletesql, $keyfield));
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function updateauspost( $keyfield, $info ){
		
		$info = (array)$info;
		
		if( $info['shippingoverride'] != '' ){
			$sql = "UPDATE ec_shippingrate SET ec_shippingrate.shipping_label = %s, ec_shippingrate.shipping_code = %s, ec_shippingrate.shipping_order = %d, ec_shippingrate.shipping_override_rate = %s, ec_shippingrate.zone_id = %d, ec_shippingrate.free_shipping_at = %s WHERE ec_shippingrate.shippingrate_id = %d";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'],$info['shippingcode'], $info['shipping_order'], $info['shippingoverride'], $info['zoneid'], $info['free_shipping_at'], $keyfield ) );
		
		}else{
			$sql = "UPDATE ec_shippingrate SET ec_shippingrate.shipping_label = %s, ec_shippingrate.shipping_code = %s, ec_shippingrate.shipping_order = %d, ec_shippingrate.shipping_override_rate = NULL, ec_shippingrate.zone_id = %d, ec_shippingrate.free_shipping_at = %s WHERE ec_shippingrate.shippingrate_id = %d";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['zoneid'], $info['free_shipping_at'], $keyfield ) );
		
		}
		
		return array( "success" );
		
	}
	
	function addauspost( $info ){
		
		$info = (array)$info;
		
		if( $info['shippingoverride'] != '' ){
			
			$sql = "INSERT INTO ec_shippingrate( ec_shippingrate.shipping_label, ec_shippingrate.shipping_code, ec_shippingrate.shipping_order, ec_shippingrate.shipping_override_rate, ec_shippingrate.is_auspost_based, ec_shippingrate.zone_id, ec_shippingrate.free_shipping_at ) VALUES( %s, %s, %d, %s, 1, %d, %s )";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['shippingoverride'], $info['zoneid'], $info['free_shipping_at'] ) );
		
		}else{
			
			$sql = "INSERT INTO ec_shippingrate( ec_shippingrate.shipping_label, ec_shippingrate.shipping_code, ec_shippingrate.shipping_order, ec_shippingrate.is_auspost_based, ec_shippingrate.zone_id, ec_shippingrate.free_shipping_at ) VALUES( %s, %s, %d, 1, %d, %s )";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['zoneid'], $info['free_shipping_at'] ) );
		
		}
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	/////////////////////////////////////////////////////////////////////////////////
	//UPS BASED SHIPPING
	/////////////////////////////////////////////////////////////////////////////////
	function getups( ){
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_shippingrate.* FROM ec_shippingrate WHERE ec_shippingrate.is_ups_based = 1 ORDER BY ec_shippingrate.shipping_order ASC";
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( !empty( $results ) ){
			$results[0]->totalrows = $totalrows;
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}
	
	function deleteups( $keyfield ){

		$deletesql = "DELETE FROM ec_shippingrate WHERE ec_shippingrate.shippingrate_id = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $deletesql, $keyfield));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function updateups( $keyfield, $info ){
		
		$info = (array)$info;
		
		if( $info['shippingoverride'] != '' ){
			$sql = "UPDATE ec_shippingrate SET ec_shippingrate.shipping_label = %s, ec_shippingrate.shipping_code = %s, ec_shippingrate.shipping_order = %d, ec_shippingrate.shipping_override_rate = %s, ec_shippingrate.zone_id = %d, ec_shippingrate.free_shipping_at = %s WHERE ec_shippingrate.shippingrate_id = %d";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'],$info['shippingcode'], $info['shipping_order'], $info['shippingoverride'], $info['zoneid'], $info['free_shipping_at'], $keyfield ) );
		
		}else{
			$sql = "UPDATE ec_shippingrate SET ec_shippingrate.shipping_label = %s, ec_shippingrate.shipping_code = %s, ec_shippingrate.shipping_order = %d, ec_shippingrate.shipping_override_rate = NULL, ec_shippingrate.zone_id = %d, ec_shippingrate.free_shipping_at = %s WHERE ec_shippingrate.shippingrate_id = %d";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['zoneid'], $info['free_shipping_at'], $keyfield ) );
		
		}
		
		return array( "success" );
		
	}
	
	function addups( $info ){
		
		$info = (array)$info;
		
		if( $info['shippingoverride'] != '' ){
			
			$sql = "INSERT INTO ec_shippingrate( ec_shippingrate.shipping_label, ec_shippingrate.shipping_code, ec_shippingrate.shipping_order, ec_shippingrate.shipping_override_rate, ec_shippingrate.is_ups_based, ec_shippingrate.zone_id, ec_shippingrate.free_shipping_at ) VALUES( %s, %s, %d, %s, 1, %d, %s )";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['shippingoverride'], $info['zoneid'], $info['free_shipping_at'] ) );
		
		}else{
			
			$sql = "INSERT INTO ec_shippingrate( ec_shippingrate.shipping_label, ec_shippingrate.shipping_code, ec_shippingrate.shipping_order, ec_shippingrate.is_ups_based, ec_shippingrate.zone_id, ec_shippingrate.free_shipping_at ) VALUES( %s, %s, %d, 1, %d, %s )";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['zoneid'], $info['free_shipping_at'] ) );
		
		}
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	/////////////////////////////////////////////////////////////////////////////////
	//USPS BASED SHIPPING 
	/////////////////////////////////////////////////////////////////////////////////
	function getusps( ){
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_shippingrate.* FROM ec_shippingrate WHERE ec_shippingrate.is_usps_based = 1 ORDER BY ec_shippingrate.shipping_order ASC";
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( !empty( $results ) ){
			$results[0]->totalrows = $totalrows;
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}
	
	function deleteusps( $keyfield ){

		$deletesql = "DELETE FROM ec_shippingrate WHERE ec_shippingrate.shippingrate_id = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $deletesql, $keyfield));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function updateusps( $keyfield, $info ){
		
		$info = (array)$info;
		
		if( $info['shippingoverride'] != '' ){
			$sql = "UPDATE ec_shippingrate SET ec_shippingrate.shipping_label = %s, ec_shippingrate.shipping_code = %s, ec_shippingrate.shipping_order = %d, ec_shippingrate.shipping_override_rate = %s, ec_shippingrate.zone_id = %d, ec_shippingrate.free_shipping_at = %s WHERE ec_shippingrate.shippingrate_id = %d";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'],$info['shippingcode'], $info['shipping_order'], $info['shippingoverride'], $info['zoneid'], $info['free_shipping_at'], $keyfield ) );
		
		}else{
			$sql = "UPDATE ec_shippingrate SET ec_shippingrate.shipping_label = %s, ec_shippingrate.shipping_code = %s, ec_shippingrate.shipping_order = %d, ec_shippingrate.shipping_override_rate = NULL, ec_shippingrate.zone_id = %d, ec_shippingrate.free_shipping_at = %s WHERE ec_shippingrate.shippingrate_id = %d";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['zoneid'], $info['free_shipping_at'], $keyfield ) );
		
		}
		
		return array( "success" );
		
	}
	
	function addusps( $info ){
		
		$info = (array)$info;
		
		if( $info['shippingoverride'] != '' ){
			
			$sql = "INSERT INTO ec_shippingrate( ec_shippingrate.shipping_label, ec_shippingrate.shipping_code, ec_shippingrate.shipping_order, ec_shippingrate.shipping_override_rate, ec_shippingrate.is_usps_based, ec_shippingrate.zone_id, ec_shippingrate.free_shipping_at ) VALUES( %s, %s, %d, %s, 1, %d, %s )";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['shippingoverride'], $info['zoneid'], $info['free_shipping_at'] ) );
		
		}else{
			
			$sql = "INSERT INTO ec_shippingrate( ec_shippingrate.shipping_label, ec_shippingrate.shipping_code, ec_shippingrate.shipping_order, ec_shippingrate.is_usps_based, ec_shippingrate.zone_id, ec_shippingrate.free_shipping_at ) VALUES( %s, %s, %d, 1, %d, %s )";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['zoneid'], $info['free_shipping_at'] ) );
		
		}
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	/////////////////////////////////////////////////////////////////////////////////
	//FedEx BASED SHIPPING
	/////////////////////////////////////////////////////////////////////////////////
	function getfedex( ){
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_shippingrate.* FROM ec_shippingrate WHERE ec_shippingrate.is_fedex_based = 1 ORDER BY ec_shippingrate.shipping_order ASC";
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		
		if( !empty( $results ) ){
			$results[0]->totalrows = $totalrows;
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}
	
	function deletefedex( $keyfield ){
		
		$deletesql = "DELETE FROM ec_shippingrate WHERE ec_shippingrate.shippingrate_id = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $deletesql, $keyfield));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function updatefedex( $keyfield, $info ){
		
		$info = (array)$info;
		
		if( $info['shippingoverride'] != '' ){
			$sql = "UPDATE ec_shippingrate SET ec_shippingrate.shipping_label = %s, ec_shippingrate.shipping_code = %s, ec_shippingrate.shipping_order = %d, ec_shippingrate.shipping_override_rate = %s, ec_shippingrate.zone_id = %d, ec_shippingrate.free_shipping_at = %s WHERE ec_shippingrate.shippingrate_id = %d";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'],$info['shippingcode'], $info['shipping_order'], $info['shippingoverride'], $info['zoneid'], $info['free_shipping_at'], $keyfield ) );
		
		}else{
			$sql = "UPDATE ec_shippingrate SET ec_shippingrate.shipping_label = %s, ec_shippingrate.shipping_code = %s, ec_shippingrate.shipping_order = %d, ec_shippingrate.shipping_override_rate = NULL, ec_shippingrate.zone_id = %d, ec_shippingrate.free_shipping_at = %s WHERE ec_shippingrate.shippingrate_id = %d";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['zoneid'], $info['free_shipping_at'], $keyfield ) );
		
		}
		
		return array( "success" );
		
	}
	
	function addfedex( $info ){
		
		$info = (array)$info;
		
		if( $info['shippingoverride'] != '' ){
			
			$sql = "INSERT INTO ec_shippingrate( ec_shippingrate.shipping_label, ec_shippingrate.shipping_code, ec_shippingrate.shipping_order, ec_shippingrate.shipping_override_rate, ec_shippingrate.is_fedex_based, ec_shippingrate.zone_id, ec_shippingrate.free_shipping_at ) VALUES( %s, %s, %d, %s, 1, %d, %s )";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['shippingoverride'], $info['zoneid'], $info['free_shipping_at'] ) );
		
		}else{
			
			$sql = "INSERT INTO ec_shippingrate( ec_shippingrate.shipping_label, ec_shippingrate.shipping_code, ec_shippingrate.shipping_order, ec_shippingrate.is_fedex_based, ec_shippingrate.zone_id, ec_shippingrate.free_shipping_at ) VALUES( %s, %s, %d, 1, %d, %s )";
			$rows_affected = $this->db->query( $this->db->prepare( $sql, $info['shippinglabel'], $info['shippingcode'], $info['shipping_order'], $info['zoneid'], $info['free_shipping_at'] ) );
		
		}
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	///////////////////////////////////////////
	//Shipping Functions
	///////////////////////////////////////////
	function updateexpeditedrates( $rate ){
		
		$sql = "UPDATE ec_setting SET ec_setting.shipping_expedite_rate=%s WHERE ec_setting.setting_id = 1";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $rate));
		
		if( $rows_affected ) {
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}

	/////////////////////////////////////////////////////////////////////////////////
	//METHOD BASED SHIPPING
	/////////////////////////////////////////////////////////////////////////////////
	function getmethodshippingrates( ){
		
		$sql= "SELECT SQL_CALC_FOUND_ROWS ec_shippingrate.* FROM ec_shippingrate WHERE ec_shippingrate.is_method_based = 1 ORDER BY ec_shippingrate.shipping_order ASC";
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( !empty( $results ) ){
			$results[0]->totalrows = $totalrows;
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}
	
	function deleteshippingmethodrate( $keyfield ){
		
		$sql = "DELETE FROM ec_shippingrate WHERE ec_shippingrate.shippingrate_id = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $keyfield ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function updateshippingmethodrate( $keyfield, $rate ){
		
		$rate = (array)$rate;
		
		$sql = "Replace into ec_shippingrate(ec_shippingrate.shippingrate_id,  ec_shippingrate.shipping_rate, ec_shippingrate.free_shipping_at, ec_shippingrate.shipping_label,  ec_shippingrate.shipping_order,  ec_shippingrate.is_method_based, ec_shippingrate.zone_id)
		values('".$keyfield."', %s, %s, %s, %s, 1, %s)";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $rate['shippingrate'], $rate['free_shipping_at'], $rate['shippinglabel'], $rate['shippingorder'], $rate['zoneid'] ));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function addshippingmethodrate( $rate ){
		
		$rate = (array)$rate;
		
		$sql = "INSERT INTO ec_shippingrate(ec_shippingrate.shippingrate_id,  ec_shippingrate.shipping_rate,  ec_shippingrate.free_shipping_at, ec_shippingrate.shipping_label, ec_shippingrate.shipping_order,  ec_shippingrate.is_method_based, ec_shippingrate.zone_id)
		values(null, %s, %s, %s, %s, 1, %s)";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $rate['shippingrate'], $rate['free_shipping_at'],$rate['shippinglabel'], $rate['shippingorder'], $rate['zoneid'] ));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	/////////////////////////////////////////////////////////////////////////////////
	//WEIGHT BASED SHIPPING
	/////////////////////////////////////////////////////////////////////////////////
	function getweightshippingrates( ){
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_shippingrate.* FROM ec_shippingrate WHERE ec_shippingrate.is_weight_based = 1 ORDER BY ec_shippingrate.trigger_rate ASC";
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( !empty( $results ) ){
			$results[0]->totalrows = $totalrows;
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}
	
	function deleteshippingweightrate( $keyfield ){
		
		$sql = "DELETE FROM ec_shippingrate WHERE ec_shippingrate.shippingrate_id = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $keyfield));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function updateshippingweightrate( $keyfield, $rate ){
		
		$rate = (array)$rate;
		
		$sql = "Replace into ec_shippingrate(ec_shippingrate.shippingrate_id, ec_shippingrate.trigger_rate, ec_shippingrate.shipping_rate, ec_shippingrate.is_weight_based, ec_shippingrate.zone_id) values(%s, %s, %s, 1, %s)";
		//Run query on database;
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $keyfield, $rate['triggerrate'], $rate['shippingrate'], $rate['zoneid'] ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function addshippingweightrate( $rate ){
		
		$rate = (array)$rate;
		
		$sql = "INSERT INTO ec_shippingrate(ec_shippingrate.shippingrate_id, ec_shippingrate.trigger_rate, ec_shippingrate.shipping_rate, ec_shippingrate.is_weight_based, ec_shippingrate.zone_id) values(null, %s, %s, 1, %s)";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $rate['triggerrate'],$rate['shippingrate'],  $rate['zoneid']));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	/////////////////////////////////////////////////////////////////////////////////
	//PRICE BASED SHIPPING
	/////////////////////////////////////////////////////////////////////////////////
	function getpriceshippingrates( ){
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_shippingrate.* FROM ec_shippingrate WHERE ec_shippingrate.is_price_based = 1 ORDER BY ec_shippingrate.trigger_rate ASC";
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( !empty( $results ) ){
			$results[0]->totalrows = $totalrows;
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}
	
	function deleteshippingpricerate( $keyfield ){
		
		$sql = "DELETE FROM ec_shippingrate WHERE ec_shippingrate.shippingrate_id = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $keyfield));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function updateshippingpricerate( $keyfield, $rate ){
		
		$rate = (array)$rate;
		
		$sql = "Replace into ec_shippingrate(ec_shippingrate.shippingrate_id, ec_shippingrate.trigger_rate, ec_shippingrate.shipping_rate, ec_shippingrate.is_price_based, ec_shippingrate.zone_id) values(%s, %s, %s, 1, %s)";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $keyfield, $rate['triggerrate'],$rate['shippingrate'],  $rate['zoneid']));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function addshippingpricerate( $rate ){
		
		$rate = (array)$rate;
		
		$sql = "INSERT INTO ec_shippingrate(ec_shippingrate.trigger_rate, ec_shippingrate.shipping_rate, ec_shippingrate.is_price_based, ec_shippingrate.zone_id) values(%s, %s, 1, %s)";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $rate['triggerrate'],$rate['shippingrate'],  $rate['zoneid'] ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	/////////////////////////////////////////////////////////////////////////////////
	//PRICE BASED SHIPPING
	/////////////////////////////////////////////////////////////////////////////////
	function getpercentageshippingrates( ){
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_shippingrate.* FROM ec_shippingrate WHERE ec_shippingrate.is_percentage_based = 1 ORDER BY ec_shippingrate.trigger_rate ASC";
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( !empty( $results ) ){
			$results[0]->totalrows = $totalrows;
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}
	
	function deleteshippingpercentagerate( $keyfield ){
		
		$sql = "DELETE FROM ec_shippingrate WHERE ec_shippingrate.shippingrate_id = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $keyfield ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function updateshippingpercentagerate( $keyfield, $rate ){
		
		$rate = (array)$rate;
		
		$sql = "Replace into ec_shippingrate(ec_shippingrate.shippingrate_id, ec_shippingrate.trigger_rate, ec_shippingrate.shipping_rate, ec_shippingrate.is_percentage_based, ec_shippingrate.zone_id) values(%s, %s, %s, 1, %s)";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $keyfield, $rate['triggerrate'],$rate['shippingrate'],  $rate['zoneid'] ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function addshippingpercentagerate( $rate ){
		
		$rate = (array)$rate;
		
		$sql = "INSERT INTO ec_shippingrate( ec_shippingrate.trigger_rate, ec_shippingrate.shipping_rate, ec_shippingrate.is_percentage_based, ec_shippingrate.zone_id ) VALUES( %s, %s, 1, %s)";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $rate['triggerrate'], $rate['shippingrate'], $rate['zoneid'] ) );
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
		
	/////////////////////////////////////////////////////////////////////////////////
	//QUANTITY BASED SHIPPING
	/////////////////////////////////////////////////////////////////////////////////
	function getquantityshippingrates( ){
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_shippingrate.* FROM ec_shippingrate WHERE ec_shippingrate.is_quantity_based = 1 ORDER BY ec_shippingrate.trigger_rate ASC";
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( !empty( $results ) ){
			$results[0]->totalrows = $totalrows;
			return $results;
		}else{
			return array( "noresults" );
		}
		
	}
	
	function deleteshippingquantityrate( $keyfield ){
		
		$sql = "DELETE FROM ec_shippingrate WHERE ec_shippingrate.shippingrate_id = %s";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $keyfield));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function updateshippingquantityrate( $keyfield, $rate ){
		
		$rate = (array)$rate;
		
		$sql = "Replace into ec_shippingrate(ec_shippingrate.shippingrate_id, ec_shippingrate.trigger_rate, ec_shippingrate.shipping_rate, ec_shippingrate.is_quantity_based, ec_shippingrate.zone_id) values(%s, %s, %s, 1, %s)";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $keyfield, $rate['triggerrate'],$rate['shippingrate'],  $rate['zoneid']));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function addshippingquantityrate( $rate ){
		
		$rate = (array)$rate;
		
		$sql = "INSERT INTO ec_shippingrate( ec_shippingrate.trigger_rate, ec_shippingrate.shipping_rate, ec_shippingrate.is_quantity_based, ec_shippingrate.zone_id) values( %s, %s, 1, %s)";
		$rows_affected = $this->db->query( $this->db->prepare( $sql, $rate['triggerrate'],$rate['shippingrate'],  $rate['zoneid']));
		
		if( $rows_affected ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
}//close class
?>