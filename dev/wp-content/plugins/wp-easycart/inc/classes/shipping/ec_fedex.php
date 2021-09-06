<?php
	
class ec_fedex{
	private $fedex_key;											// Your FedEx Account Key
	private $fedex_account_number;								// Your FedEx Account Number
	private $fedex_meter_number;								// Your FedEx Meter Number
	private $fedex_password;									// Your FedEx Password
	private $fedex_ship_from_zip;								// Your FedEx ship from zip code
	private $fedex_weight_units;								// The weight units to use for the FedEx api
	private $fedex_country_code;								// The country code for the FedEx api
	private $fedex_conversion_rate;								// A conversion rate option
	private $fedex_test_account;								// Is this is a FedEx test account
	
	private $shipper_url;										// String

	function __construct( $ec_setting ){
		$this->fedex_key = $ec_setting->get_fedex_key( );
		$this->fedex_account_number = $ec_setting->get_fedex_account_number();
		$this->fedex_meter_number = $ec_setting->get_fedex_meter_number();
		$this->fedex_password = $ec_setting->get_fedex_password();
		$this->fedex_ship_from_zip = $ec_setting->get_fedex_ship_from_zip();
		$this->fedex_weight_units = $ec_setting->get_fedex_weight_units();
		$this->fedex_country_code = $ec_setting->get_fedex_country_code();	
		$this->fedex_conversion_rate = $ec_setting->get_fedex_conversion_rate();
		$this->fedex_test_account = $ec_setting->get_fedex_test_account();
	}
		
	public function get_rate( $ship_code, $destination_zip, $destination_country, $weight, $length = 1, $width = 1, $height = 1, $declared_value = 0, $cart = array( ) ){
		if( $weight == 0 )
			$weight = 1/16; // Min 1 ounce
		
		$dimensions_units = 'IN';
		if( get_option( 'ec_option_enable_metric_unit_display' ) )
			$dimensions_units = 'CM';
			
		if( !$destination_country )
			$destination_country = $this->fedex_country_code;
			
		if( !$destination_zip || $destination_zip == "" )
			$destination_zip = $this->fedex_ship_from_zip;
		
		$service_type = strtoupper( $ship_code );
		
		$service_types = array( 	"EUROPE_FIRST_INTERNATIONAL_PRIORITY", 
									"FEDEX_1_DAY_FREIGHT", 
									"FEDEX_2_DAY", 
									"FEDEX_2_DAY_AM", 
									"FEDEX_2_DAY_FREIGHT", 
									"FEDEX_3_DAY_FREIGHT",
									"FEDEX_EXPRESS_SAVER",
									"FEDEX_FIRST_FREIGHT",
									"FEDEX_FREIGHT_ECONOMY",
									"FEDEX_FREIGHT_PRIORITY",
									"FEDEX_GROUND",
									"FIRST_OVERNIGHT",
									"GROUND_HOME_DELIVERY",
									"INTERNATIONAL_ECONOMY",
									"INTERNATIONAL_ECONOMY_FREIGHT",
									"INTERNATIONAL_FIRST",
									"INTERNATIONAL_PRIORITY",
									"INTERNATIONAL_PRIORITY_FREIGHT",
									"PRIORITY_OVERNIGHT",
									"SMART_POST",
									"STANDARD_OVERNIGHT" );
		
		if( in_array( $service_type, $service_types ) ){
			
			if( $this->fedex_test_account ){
				$path_to_wsdl = dirname(__FILE__) . "/fedex_rate_service_v16_test_account.wsdl";
			}else{
				$path_to_wsdl = dirname(__FILE__) . "/fedex_rate_service_v16.wsdl";
			}
	
			ini_set("soap.wsdl_cache_enabled", "0");
			 
			$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
			
			$request['WebAuthenticationDetail'] = array(
				'UserCredential' =>array(
					'Key' => $this->fedex_key, 
					'Password' => $this->fedex_password
				)
			); 
			$request['ClientDetail'] = array(
				'AccountNumber' => $this->fedex_account_number, 
				'MeterNumber' => $this->fedex_meter_number
			);
			$request['TransactionDetail'] = array( 'CustomerTransactionId' => ' *** Rate Request v16 using PHP ***' );
			$request['Version'] = array(
				'ServiceId' => 'crs', 
				'Major' => '16', 
				'Intermediate' => '0', 
				'Minor' => '0'
			);
			$request['ReturnTransitAndCommit'] = true;
			$request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
			$request['RequestedShipment']['ShipTimestamp'] = date( 'c' );
			$request['RequestedShipment']['ServiceType'] = $service_type; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
			$request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
			
			$shipper = array( 'Address' => array( 'PostalCode' => $this->fedex_ship_from_zip, 'CountryCode' => $this->fedex_country_code ) );
			$request['RequestedShipment']['Shipper'] = $shipper;
			
			$recipient = array( 
				'Address' => array( 
					'PostalCode' => $destination_zip, 
					'CountryCode' => $destination_country, 
					'Residential' => 1
				)
			);
			$request['RequestedShipment']['Recipient'] = $recipient;
			
			$request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT'; 
			$request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
			if( get_option( 'ec_option_ship_items_seperately' ) && count( $cart ) > 0 ){ // Each Item Separate
				$total_items = 0;
				$request['RequestedShipment']['RequestedPackageLineItems'] = array( );
				for( $i=0; $i<count( $cart ); $i++ ){
					if( $cart[$i]->is_shippable && !$cart[$i]->exclude_shippable_calculation ){
						for( $j=0; $j<$cart[$i]->quantity; $j++ ){
							$total_items++;
							$packageLineItem 	= array( 
								'SequenceNumber' 	=> $total_items, 
								'GroupPackageCount'	=> 1, 
								'Dimensions'	=> array(
									'Length'		=> ( $cart[$i]->length < 1 ) ? 1 : $cart[$i]->length,
									'Width'			=> ( $cart[$i]->width < 1 ) ? 1 : $cart[$i]->width,
									'Height'		=> ( $cart[$i]->height < 1 ) ? 1 : $cart[$i]->height,
									'Units'			=> $dimensions_units
								),
								'Weight' 		=> array( 
									'Value' 		=> ( $cart[$i]->weight > 0 ) ? $cart[$i]->weight : (1/16), // min 1 ounce 
									'Units' 		=> $this->fedex_weight_units 
								) 
							);
							$request['RequestedShipment']['RequestedPackageLineItems'][] = $packageLineItem;
						}// close quantity loop
					}// close is shippable check
				}// close cart item loop
				$request['RequestedShipment']['PackageCount'] = $total_items;
			
			}else{
				$total_items = 0;
				
				// Generate Product List
				$current_weight = 0;
				$products = array( );
				foreach( $cart as $cartitem ){
					if( $cartitem->is_shippable && !$cartitem->exclude_shippable_calculation ){
						// Each quantity item is a new product in the shipping world
						for( $i=0; $i<$cartitem->quantity; $i++ ){
							
							// Add the new product
							$products[] = array( 
								'width' 	=> ( $cartitem->width < 0 ) ? .01 : $cartitem->width,
								'height'	=> ( $cartitem->height < 0 ) ? .01 : $cartitem->height,
								'length'	=> ( $cartitem->length < 0 ) ? .01 : $cartitem->length,
								'weight'	=> $cartitem->weight 
							);
							$current_weight += $cartitem->weight;
							
							$parcel = $this->calculate_parcel( $products );
							
							if( $current_weight > 150 || $parcel['width'] > 108 || ( $parcel['width']*2 + $parcel['height']*2 + $parcel['length'] ) > 165 ){
								$total_items++;
								$packageLineItem 	= array( 
									'SequenceNumber' 	=> $total_items, 
									'GroupPackageCount'	=> 1, 
									'Dimensions'	=> array(
										'Length'		=> ( $curr_package['length'] < 1 ) ? 1 : $curr_package['length'],
										'Width'			=> ( $curr_package['width'] < 1 ) ? 1 : $curr_package['width'],
										'Height'		=> ( $curr_package['height'] < 1 ) ? 1 : $curr_package['height'],
										'Units'			=> $dimensions_units
									),
									'Weight' 		=> array( 
										'Value' 		=> $curr_package['weight'], 
										'Units' 		=> $this->fedex_weight_units 
									)  
								);
								$request['RequestedShipment']['RequestedPackageLineItems'][] = $packageLineItem;
								$current_weight = 0;
								$products = array( );
							}
							
						}// close quantity loop
					}// close shippable check
				}// close cart item loop
				
				// Maybe add last package
				if( count( $products ) > 0 ){
					$total_items++;
					$packageLineItem 	= array( 
						'SequenceNumber' 	=> $total_items, 
						'GroupPackageCount'	=> 1, 
						'Dimensions'	=> array(
							'Length'		=> ( $curr_package['length'] < 1 ) ? 1 : $curr_package['length'],
							'Width'			=> ( $curr_package['width'] < 1 ) ? 1 : $curr_package['width'],
							'Height'		=> ( $curr_package['height'] < 1 ) ? 1 : $curr_package['height'],
							'Units'			=> $dimensions_units
						),
						'Weight' 		=> array( 
							'Value' 		=> ( $curr_package['weight'] > 0 ) ? $curr_package['weight'] : (1/16), // min 1 ounce, 
							'Units' 		=> $this->fedex_weight_units 
						)  
					);
					$request['RequestedShipment']['RequestedPackageLineItems'][] = $packageLineItem;
				}
				$request['RequestedShipment']['PackageCount'] = $total_items;
				
			}
			
			try{
				$response = $client->getRates($request);
				$db = new ec_db( );
				$db->insert_response( 0, 0, "FedEx", print_r( $request, true ) . '------' . print_r( $response, true ) );
					
				if( $response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR' ){  	
					$rateReply = $response->RateReplyDetails;
					$serviceType = $rateReply->ServiceType;
					
					$payor_account_package = 0.000;
					$rated_account_package = 0.000;
					$payor_list_package = 0.000;
					$rated_list_package = 0.000;
					$rate_other = 0.000;
					
					if( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "PAYOR_ACCOUNT_PACKAGE" ){
						$payor_account_package = number_format( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
					}else if( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "RATED_ACCOUNT_PACKAGE" ){
						$rated_account_package = number_format( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
					}else if( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "PAYOR_LIST_PACKAGE" ){
						$payor_list_package = number_format( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
					}else if( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "RATED_LIST_PACKAGE" ){
						$rated_list_package = number_format( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
					}else{
						$rate_other = number_format( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
					}
					
					if( $payor_account_package > 0 ){
						$rate = $payor_account_package;
					}else if( $rated_account_package > 0 ){
						$rate = $rated_account_package;
					}else if( $payor_list_package > 0 ){
						$rate = $payor_list_package;
					}else if( $rated_list_package > 0 ){
						$rate = $rated_list_package;
					}else {
						$rate = $rate_other;
					}
										
					$rate_discount = number_format( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalFreightDiscounts->Amount * $this->fedex_conversion_rate, 2, ".", "" );
					
					if( !get_option( 'ec_option_fedex_use_net_charge' ) ){
						$rate = $rate + $rate_discount;
					}
					
					return $rate;
				}else{
					$db = new ec_db( );
					$db->insert_response( 0, 0, "FedEx", "error in fedex get rate, " . $this->printFault($exception, $client) );
					return "ERROR";
				}
			}catch (SoapFault $exception){
				$db = new ec_db( );
				$db->insert_response( 0, 0, "FedEx", "error in fedex get rate, " . $this->printFault($exception, $client) );
				return "ERROR";        
			}
		}
	}
		
	public function get_all_rates( $destination_zip, $destination_country, $weight, $length = 10, $width = 10, $height = 10, $declared_value = 0, $cart = array( ) ){
		
		if( $weight == 0 )
			$weight = 1/16;
		
		$dimensions_units = 'IN';
		if( get_option( 'ec_option_enable_metric_unit_display' ) )
			$dimensions_units = 'CM';
			
		if( !$destination_country )
			$destination_country = $this->fedex_country_code;
			
		if( !$destination_zip || $destination_zip == "" )
			$destination_zip = $this->fedex_ship_from_zip;
		
		if( $this->fedex_test_account ){
			$path_to_wsdl = dirname(__FILE__) . "/fedex_rate_service_v16_test_account.wsdl";
		}else{
			$path_to_wsdl = dirname(__FILE__) . "/fedex_rate_service_v16.wsdl";
		}

		ini_set("soap.wsdl_cache_enabled", "0");
		 
		$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
		
		$request['WebAuthenticationDetail'] = array(
			'UserCredential' =>array(
				'Key' => $this->fedex_key, 
				'Password' => $this->fedex_password
			)
		); 
		$request['ClientDetail'] = array(
			'AccountNumber' => $this->fedex_account_number, 
			'MeterNumber' => $this->fedex_meter_number
		);
		$request['TransactionDetail'] = array( 'CustomerTransactionId' => ' *** Rate Request v16 using PHP ***' );
		$request['Version'] = array(
			'ServiceId' => 'crs', 
			'Major' => '16', 
			'Intermediate' => '0', 
			'Minor' => '0'
		);
		$request['ReturnTransitAndCommit'] = true;
		$request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
		$request['RequestedShipment']['ShipTimestamp'] = date( 'c' );
		$request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
		
		$shipper = array( 'Address' => array( 'PostalCode' => $this->fedex_ship_from_zip, 'CountryCode' => $this->fedex_country_code ) );
		$request['RequestedShipment']['Shipper'] = $shipper;
		
		$recipient = array( 
			'Address' => array( 
				'PostalCode' 	=> $destination_zip, 
				'CountryCode' 	=> $destination_country,
				'Residential' 	=> 1
			)
		);
		$request['RequestedShipment']['Recipient'] = $recipient;
		
		$request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT'; 
		$request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
		
		if( get_option( 'ec_option_ship_items_seperately' ) && count( $cart ) > 0 ){
			$total_items = 0;
			$request['RequestedShipment']['RequestedPackageLineItems'] = array( );
			for( $i=0; $i<count( $cart ); $i++ ){
				if( $cart[$i]->is_shippable && !$cart[$i]->exclude_shippable_calculation ){
					for( $j=0; $j<$cart[$i]->quantity; $j++ ){
						$total_items++;
						$packageLineItem 	= array( 
							'SequenceNumber' 	=> $total_items, 
							'GroupPackageCount'	=> 1, 
							'Dimensions'	=> array(
								'Length'		=> ( $cart[$i]->length < 1 ) ? 1 : $cart[$i]->length,
								'Width'			=> ( $cart[$i]->width < 1 ) ? 1 : $cart[$i]->width,
								'Height'		=> ( $cart[$i]->height < 1 ) ? 1 : $cart[$i]->height,
								'Units'			=> $dimensions_units
							),
							'Weight' 		=> array( 
								'Value' 		=> ( $cart[$i]->weight > 0 ) ? $cart[$i]->weight : (1/16), // min 1 ounce 
								'Units' 		=> $this->fedex_weight_units 
							) 
						);
						$request['RequestedShipment']['RequestedPackageLineItems'][] = $packageLineItem;
					}// close quantity loop
				}// close is shippable check
			}// close cart items loop
			$request['RequestedShipment']['PackageCount'] = $total_items;
		
		}else{
			$total_items = 0;
			
			// Generate Product List
			$current_weight = 0;
			$products = array( );
			foreach( $cart as $cartitem ){
				if( $cartitem->is_shippable && !$cartitem->exclude_shippable_calculation ){
					// Each quantity item is a new product in the shipping world
					for( $i=0; $i<$cartitem->quantity; $i++ ){
						
						// Add the new product
						$products[] = array( 
							'width' 	=> ( $cartitem->width < 0 ) ? .01 : $cartitem->width,
							'height'	=> ( $cartitem->height < 0 ) ? .01 : $cartitem->height,
							'length'	=> ( $cartitem->length < 0 ) ? .01 : $cartitem->length,
							'weight'	=> $cartitem->weight 
						);
						$current_weight += $cartitem->weight;
						
						$parcel = $this->calculate_parcel( $products );
						
						if( $current_weight > 150 || $parcel['width'] > 108 || ( $parcel['width']*2 + $parcel['height']*2 + $parcel['length'] ) > 165 ){
							$total_items++;
							$packageLineItem 	= array( 
								'SequenceNumber' 	=> $total_items, 
								'GroupPackageCount'	=> 1, 
								'Dimensions'	=> array(
									'Length'		=> ( $parcel['length'] < 1 ) ? 1 : $parcel['length'],
									'Width'			=> ( $parcel['width'] < 1 ) ? 1 : $parcel['width'],
									'Height'		=> ( $parcel['height'] < 1 ) ? 1 : $parcel['height'],
									'Units'			=> $dimensions_units
								),
								'Weight' 		=> array( 
									'Value' 		=> $parcel['weight'], 
									'Units' 		=> $this->fedex_weight_units 
								)  
							);
							$request['RequestedShipment']['RequestedPackageLineItems'][] = $packageLineItem;
							$current_weight = 0;
							$products = array( );
						}
					}// close quantity loop
				}// close is shippable check
			}// close cart item loop
			
			// Maybe add last package
			if( count( $products ) > 0 ){
				$parcel = $this->calculate_parcel( $products );
				$total_items++;
				$packageLineItem 	= array( 
					'SequenceNumber' 	=> $total_items, 
					'GroupPackageCount'	=> 1, 
					'Dimensions'	=> array(
						'Length'		=> ( $parcel['length'] < 1 ) ? 1 : $parcel['length'],
						'Width'			=> ( $parcel['width'] < 1 ) ? 1 : $parcel['width'],
						'Height'		=> ( $parcel['height'] < 1 ) ? 1 : $parcel['height'],
						'Units'			=> $dimensions_units
					),
					'Weight' 		=> array( 
						'Value' 		=> ( $parcel['weight'] > 0 ) ? $parcel['weight'] : (1/16), // min 1 ounce
						'Units' 		=> $this->fedex_weight_units 
					)  
				);
				$request['RequestedShipment']['RequestedPackageLineItems'][] = $packageLineItem;
			}
			$request['RequestedShipment']['PackageCount'] = $total_items;
			
		}
		
		try{
			$response = $client->getRates($request);
			$db = new ec_db( );
			$db->insert_response( 0, 0, "FedEx", print_r( $request, true ) . '------' . print_r( $response, true ) );
			
			if( $response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR' ){ 	
				
				if( $response->HighestSeverity == 'WARNING' && $response->Notifications->Code == 556 ){
					return "ERROR";
				} 	
				
				$rates = array( );
				
				// If only 1 result, NOT returned as array...
				if( is_array( $response->RateReplyDetails ) ){
					
					for( $i=0; $i<count( $response->RateReplyDetails ); $i++ ){
						
						$code = $response->RateReplyDetails[$i]->ServiceType;
						$rate = 0.000;
						$payor_account_package = 0.000;
						$rated_account_package = 0.000;
						$payor_list_package = 0.000;
						$rated_list_package = 0.000;
						$rate_other = 0.000;
						
						if( $response->RateReplyDetails[$i]->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "PAYOR_ACCOUNT_PACKAGE" ){
							$payor_account_package = number_format( $response->RateReplyDetails[$i]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						}else if( $response->RateReplyDetails[$i]->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "RATED_ACCOUNT_PACKAGE" ){
							$rated_account_package = number_format( $response->RateReplyDetails[$i]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						}else if( $response->RateReplyDetails[$i]->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "PAYOR_LIST_PACKAGE" ){
							$payor_list_package = number_format( $response->RateReplyDetails[$i]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						}else if( $response->RateReplyDetails[$i]->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "RATED_LIST_PACKAGE" ){
							$rated_list_package = number_format( $response->RateReplyDetails[$i]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						}else{
							$rate_other = number_format( $response->RateReplyDetails[$i]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						}
						
						if( $payor_account_package > 0 ){
							$rate = $payor_account_package;
						}else if( $rated_account_package > 0 ){
							$rate = $rated_account_package;
						}else if( $payor_list_package > 0 ){
							$rate = $payor_list_package;
						}else if( $rated_list_package > 0 ){
							$rate = $rated_list_package;
						}else {
							$rate = $rate_other;
						}
						
						$rate_discount = number_format( $response->RateReplyDetails[$i]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalFreightDiscounts->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						
						if( !get_option( 'ec_option_fedex_use_net_charge' ) ){
							$rate = $rate + $rate_discount;
						}
						
						$rates[] = array( 'rate_code' => $code, 'rate' => $rate );
					
					}
				
				}else{
					$code = $response->RateReplyDetails->ServiceType;
					$rate = 0.000;
					$payor_account_package = 0.000;
					$rated_account_package = 0.000;
					$payor_list_package = 0.000;
					$rated_list_package = 0.000;
					$rate_other = 0.000;
					
					if( 
						isset( $response ) && 
						isset( $response->RateReplyDetails ) && 
						is_array( $response->RateReplyDetails ) &&
						isset( $response->RateReplyDetails[0] ) && 
						isset( $response->RateReplyDetails[0]->RatedShipmentDetails ) &&  
						isset( $response->RateReplyDetails[0]->RatedShipmentDetails[0] ) && 
						isset( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail ) && 
						isset( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->RateType )
					){
						if( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "PAYOR_ACCOUNT_PACKAGE" ){
							$payor_account_package = number_format( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						}else if( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "RATED_ACCOUNT_PACKAGE" ){
							$rated_account_package = number_format( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						}else if( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "PAYOR_LIST_PACKAGE" ){
							$payor_list_package = number_format( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						}else if( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "RATED_LIST_PACKAGE" ){
							$rated_list_package = number_format( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						}else{
							$rate_other = number_format( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						}
						
						if( $payor_account_package > 0 ){
							$rate = $payor_account_package;
						}else if( $rated_account_package > 0 ){
							$rate = $rated_account_package;
						}else if( $payor_list_package > 0 ){
							$rate = $payor_list_package;
						}else if( $rated_list_package > 0 ){
							$rate = $rated_list_package;
						}else {
							$rate = $rate_other;
						}
						
						$rate_discount = number_format( $response->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalFreightDiscounts->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						
						if( !get_option( 'ec_option_fedex_use_net_charge' ) ){
							$rate = $rate + $rate_discount;
						}
						
						$rates[] = array( 'rate_code' => $code, 'rate' => $rate );
					}else if( 
						isset( $response ) && 
						isset( $response->RateReplyDetails ) && 
						isset( $response->RateReplyDetails->RatedShipmentDetails ) &&  
						isset( $response->RateReplyDetails->RatedShipmentDetails[0] ) && 
						isset( $response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail ) && 
						isset( $response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->RateType )
					){
						if( $response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "PAYOR_ACCOUNT_PACKAGE" ){
							$payor_account_package = number_format( $response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						}else if( $response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "RATED_ACCOUNT_PACKAGE" ){
							$rated_account_package = number_format( $response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						}else if( $response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "PAYOR_LIST_PACKAGE" ){
							$payor_list_package = number_format( $response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						}else if( $response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->RateType == "RATED_LIST_PACKAGE" ){
							$rated_list_package = number_format( $response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						}else{
							$rate_other = number_format( $response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						}
						
						if( $payor_account_package > 0 ){
							$rate = $payor_account_package;
						}else if( $rated_account_package > 0 ){
							$rate = $rated_account_package;
						}else if( $payor_list_package > 0 ){
							$rate = $payor_list_package;
						}else if( $rated_list_package > 0 ){
							$rate = $rated_list_package;
						}else {
							$rate = $rate_other;
						}
						
						$rate_discount = number_format( $response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->TotalFreightDiscounts->Amount * $this->fedex_conversion_rate, 2, ".", "" );
						
						if( !get_option( 'ec_option_fedex_use_net_charge' ) ){
							$rate = $rate + $rate_discount;
						}
						
						$rates[] = array( 'rate_code' => $code, 'rate' => $rate );
					}
				}
				
				return $rates;
			}else{
				$db = new ec_db( );
				$db->insert_response( 0, 0, "FedEx", "error in fedex get rate, " . $this->printFault($exception, $client) );
				return "ERROR";
			}
		}catch (SoapFault $exception){
			$db = new ec_db( );
			$db->insert_response( 0, 0, "FedEx", "error in fedex get rate, " . $this->printFault($exception, $client) );
			return "ERROR";        
		}
	}
		
	public function get_rate_test( $ship_code, $destination_zip, $destination_country, $weight, $length = 10, $width = 10, $height = 10, $declared_value = 0, $cart = array( )  ){
		if( $weight == 0 )
			return "0.00";
		
		$dimensions_units = 'IN';
		if( get_option( 'ec_option_enable_metric_unit_display' ) )
			$dimensions_units = 'CM';
			
		if( !$destination_country )
			$destination_country = $this->fedex_country_code;
		
		if( $this->fedex_test_account ){
			$path_to_wsdl = dirname(__FILE__) . "/fedex_rate_service_v16_test_account.wsdl";
		}else{
			$path_to_wsdl = dirname(__FILE__) . "/fedex_rate_service_v16.wsdl";
		}

		ini_set("soap.wsdl_cache_enabled", "0");
		 
		$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
		
		$request['WebAuthenticationDetail'] = array(
			'UserCredential' =>array(
				'Key' => $this->fedex_key, 
				'Password' => $this->fedex_password
			)
		); 
		$request['ClientDetail'] = array(
			'AccountNumber' => $this->fedex_account_number, 
			'MeterNumber' => $this->fedex_meter_number
		);
		$request['TransactionDetail'] = array( 'CustomerTransactionId' => ' *** Rate Request v16 using PHP ***' );
		$request['Version'] = array(
			'ServiceId' => 'crs', 
			'Major' => '16', 
			'Intermediate' => '0', 
			'Minor' => '0'
		);
		$request['ReturnTransitAndCommit'] = true;
		$request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
		$request['RequestedShipment']['ShipTimestamp'] = date( 'c' );
		$request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
		
		$shipper = array( 'Address' => array( 'PostalCode' => $this->fedex_ship_from_zip, 'CountryCode' => $this->fedex_country_code ) );
		$request['RequestedShipment']['Shipper'] = $shipper;
		
		$recipient = array( 'Address' => array( 'PostalCode' => $destination_zip, 'CountryCode' => $destination_country ) );
		$request['RequestedShipment']['Recipient'] = $recipient;
		
		$request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT'; 
		$request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
		$request['RequestedShipment']['PackageCount'] = '1';
		
		$packageLineItem = array( 
			'SequenceNumber' 	=> 1, 
			'GroupPackageCount'	=> 1, 
			'Dimensions'	=> array(
				'Length'		=> ( $length < 1 ) ? 1 : $length,
				'Width'			=> ( $width < 1 ) ? 1 : $width,
				'Height'		=> ( $height < 1 ) ? 1 : $height,
				'Units'			=> $dimensions_units
			),
			'Weight' => array( 
				'Value' 			=> $weight, 
				'Units' 			=> $this->fedex_weight_units
			)
		);
		$request['RequestedShipment']['RequestedPackageLineItems'] = $packageLineItem;
		
		try{
			$response = $client->getRates($request);
			
		}catch (SoapFault $exception){
			$reponse = array( "ERROR" => "Error in fedex get rate, " . $this->printFault( $exception, $client ) );     
		}
		
		$db = new ec_db( );
		$db->insert_response( 0, 0, "FedEx Test",  print_r( $request, true ) . ' ----- ' . print_r( $response, true ) );
		
		return $response;
	}
	
	private function printError( $client, $response ){
		$string = 'Error returned in processing transaction: ';
		$string .= $this->printNotifications( $response -> Notifications );
		$string .= $this->printRequestResponse( $client, $response );
		return $string;
	}
	
	private function printNotifications( $notes ){
		$string = "";
		foreach( $notes as $noteKey => $note ){
			if(is_string($note)){    
				$string .= $noteKey . ': ' . $note . "\r\n";
			}
			else{
				$string .= $this->printNotifications( $note );
			}
		}
		return $string;
	}
	
	private function printRequestResponse($client){
		return 'Request: ' .  htmlspecialchars($client->__getLastRequest()) . ", Response " . htmlspecialchars($client->__getLastResponse());
	}
	
	private function printFault($exception, $client) {
		$string = '<h2>Fault</h2>' . "<br>\n";                        
		$string .= "<b>Code:</b>{$exception->faultcode}<br>\n";
		$string .="<b>String:</b>{$exception->faultstring}<br>\n";
		$string .= sprintf( "\r%s:- %s", date("D M j G:i:s T Y"), $client->__getLastRequest( ). "\n\n" . $client->__getLastResponse( ) );
		return $string;
	}
	
	public function validate_address( $desination_address, $destination_city, $destination_state, $destination_zip, $destination_country ){
		return true;
		/*
		if( $this->fedex_test_account ){
			return true; //Cannot test address in test mode environment!! STUPID FEDEX.
		}else{
			$path_to_wsdl = dirname(__FILE__) . "/fedex_address_validation_service_v2.wsdl";

			ini_set("soap.wsdl_cache_enabled", "0");
			 
			$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
			
			$request['WebAuthenticationDetail'] = array(
				'UserCredential' =>array(
					'Key' => $this->fedex_key, 
					'Password' => $this->fedex_password
				)
			); 
			$request['ClientDetail'] = array(
				'AccountNumber' => $this->fedex_account_number, 
				'MeterNumber' => $this->fedex_meter_number
			);
			$request['Version'] = array(
				'ServiceId' => 'aval', 
				'Major' => '2', 
				'Intermediate' => '0', 
				'Minor' => '0'
			);
			
			$request['RequestTimestamp'] = date( 'Y-m-d' ) . 'T' . date( 'H:i:sP' );
			$request['AddressesToValidate'] = array(
				0 => array( 
					'Address' => $desination_address,
					'City' => $destination_city,
					'StateorProvinceCode' => $destination_state,
					'PostalCode' => $destination_zip,
					'CountryCode' => $destination_country
				)
			);
			$request['Options'] = array(
				'CheckResidentialStatus' => 1,
				'MaximumNumberOfMatches' => 5,
				'StreetAccuracy' => 'LOOSE',
				'DirectionalAccuracy' => 'LOOSE',
				'CompanyNameAccuracy' => 'LOOSE',
				'ConvertToUpperCase' => 1,
				'RecognizeAlternateCityNames' => 1,
				'ReturnParsedElements' => 1
			);
			
			try{
				$response = $client->addressValidation( $request );
			
				print_r( $response );
				die( );
				
				if( $response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR' ){  	
				
					if( $response->ProposedAddressDetails->Score > 0 )
						return true;
						
					else
						return false;
					
				}else{
					
					return true;
					
				}
					
				return $response;
			
			}catch (SoapFault $exception){
				
				return true; // Do not let an error stop the checkout!
			
			}
			
		}
		*/
	}
	
	private function check_package( $curr_package, $next_item ){
		$test_package = $this->add_to_package( $curr_package, $next_item );
		if( $test_package['weight'] > 150 )	
			return false;
		else if( $test_package['length'] > 108 )
			return false;
		else if( $test_package['width']*2 + $test_package['height']*2 + $test_package['length'] > 165 )
			return false;
		else
			return true;
	}
	
	private function check_item( $next_item ){
		// Rotate the package
		$dimensions = array( $next_item->length, $next_item->width, $next_item->height );
		sort( $dimensions );
		
		if( $next_item->weight > 150 )
			return false;
		
		else if( min( $next_item->length, $next_item->width, $next_item->height ) > 119 )
			return false;
		
		else if( ( $dimensions[0] * 2 ) + ( $dimensions[1] * 2 ) > 165 )
			return false;
		
		else
			return true;
			
	}
	
	private function get_empty_package( ){
		return array(
			'length' => 0,
			'width'  => 0,
			'height' => 0,
			'weight' => 0
		);
	}
	
	private function add_to_package( $curr_package, $item ){
		// Rotate item to find length
		$dimensions = array( $item->length, $item->width, $item->height );
		sort( $dimensions );
		
		// Should we put in box w+w, h+h, or l+l?
		$new_width = $curr_package['width'] + $dimensions[0];
		$new_height = $curr_package['height'] + $dimensions[0];
		$new_length = $curr_package['length'] + $dimensions[0];
		
		$volume_width = $new_width * max( $curr_package['height'], $dimensions[1] ) * max( $curr_package['length'], $dimensions[2] );
		$volume_height = $new_height * max( $curr_package['width'], $dimensions[1] ) * max( $curr_package['length'], $dimensions[2] );
		$volume_length = $new_length * max( $curr_package['width'], $dimensions[1] ) * max( $curr_package['height'], $dimensions[2] );
		
		if( $curr_package['weight'] == 0 || ( $volume_width < $volume_height && $volume_width < $volume_length ) ){
			$curr_package['length'] = max( $curr_package['length'], $dimensions[2] );
			$curr_package['width'] += $dimensions[0];
			$curr_package['height'] = max( $curr_package['height'], $dimensions[1] );
		
		}else if( $volume_height < $volume_width && $volume_height < $volume_length ){
			$curr_package['length'] = max( $curr_package['length'], $dimensions[2] );
			$curr_package['width'] = max( $dimensions[1], $curr_package['width'] );
			$curr_package['height'] += $dimensions[0];
			
		}else{
			$curr_package['length'] += $dimensions[0];
			$curr_package['width'] = max( $curr_package['width'], $dimensions[1] );
			$curr_package['height'] = max( $curr_package['height'], $dimensions[2] );
			
		}
		$curr_package['weight'] += $item->weight;
		return $curr_package;
	}
	
	private function calculate_parcel( $products ){
 
		// Create an empty package
		$package_dimensions = array( 0, 0, 0 );
		$package_weight = 0;
		$package_volume = 0;
		$package_volume_empty = 0;
		$package_volume_used = 0;
		
		// Step through each product
		foreach( $products as $product ){
		
			// Create an array of product dimensions
			$product_dimensions = array( $product['width'], $product['height'], $product['length'] );
			
			// Twist and turn the item, longest side first ([0]=length, [1]=width, [2]=height)
			rsort( $product_dimensions, SORT_NUMERIC); // Sort $product_dimensions by highest to lowest
			
			if( $product_dimensions[0] <= $package_dimensions[0] && $product_dimensions[1] <= $package_dimensions[1] && $product_dimensions[2] <= $package_dimensions[2] && ( $product_dimensions[0] * $product_dimensions[1] * $product_dimensions[2] ) <= $package_volume_empty ){
				$package_volume_empty -= $product_dimensions[0] * $product_dimensions[1] * $product_dimensions[2];
				$package_volume_used += $product_dimensions[0] * $product_dimensions[1] * $product_dimensions[2];
			
			}else{
				
				// Package height + item height
				$package_dimensions[2] += $product_dimensions[2];
				
				// If this is the widest item so far, set item width as package width
				if($product_dimensions[1] > $package_dimensions[1]) 
					
					$package_dimensions[1] = $product_dimensions[1];
				
				// If this is the longest item so far, set item length as package length
				if($product_dimensions[0] > $package_dimensions[0]) 
					$package_dimensions[0] = $product_dimensions[0];
				
				// Twist and turn the package, longest side first ([0]=length, [1]=width, [2]=height)
				rsort( $package_dimensions, SORT_NUMERIC );
				
				$package_volume = $package_dimensions[0] * $package_dimensions[1] * $package_dimensions[2];
				$package_volume_used += $product_dimensions[0] * $product_dimensions[1] * $product_dimensions[2];
				$package_volume_empty = $package_volume - $package_volume_used;
				
			}
			
			// Add to total weight
			$package_weight = $package_weight + $product['weight'];
		}
		
		$parcel = array( 	'weight' 	=> $package_weight,
							'width'		=> $package_dimensions[0],
							'height'	=> $package_dimensions[1],
							'length'	=> $package_dimensions[2] );
		
		return $parcel;
	}

}
?>