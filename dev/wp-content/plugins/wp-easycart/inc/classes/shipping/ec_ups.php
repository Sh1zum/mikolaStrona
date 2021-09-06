<?php
	
class ec_ups{
	private $ups_access_license_number; 						// Your UPS license number
	private $ups_user_id; 										// Your UPS user id
	private $ups_password; 										// Your UPS password
	private $ups_ship_from_state; 								// Your UPS ship from state code
	private $ups_ship_from_zip; 								// Your UPS ship from zip code
	private $ups_shipper_number; 								// Your UPS shipper number
	private $ups_country_code;									// Your UPS country code
	private $ups_weight_type;									// Your UPS weight type
	private $ups_conversion_rate;								// A Conversion Rate
	private $ups_negotiated_rates;								// Optional negotiated rates
	
	private $shipper_url;										// String
	
	function __construct( $ec_setting ){
		$this->ups_access_license_number = $ec_setting->get_ups_access_license_number( );
		$this->ups_user_id = $ec_setting->get_ups_user_id( );
		$this->ups_password = $ec_setting->get_ups_password( );
		$this->ups_ship_from_state = $ec_setting->get_ups_ship_from_state( );
		$this->ups_ship_from_zip = $ec_setting->get_ups_ship_from_zip( );
		$this->ups_shipper_number = $ec_setting->get_ups_shipper_number( );
		$this->ups_country_code = $ec_setting->get_ups_country_code( );
		$this->ups_weight_type = $ec_setting->get_ups_weight_type( );
		$this->ups_conversion_rate = $ec_setting->get_ups_conversion_rate( );
		$this->ups_negotiated_rates = $ec_setting->get_ups_negotiated_rates( );
		
		$this->shipper_url = "https://onlinetools.ups.com/webservices/Rate";
	}
	
	public function get_rate( $ship_code, $destination_zip, $destination_country, $weight, $length = 1, $width = 1, $height = 1, $declared_value = 0, $cart = array( ) ){
		if( $weight == 0 )
			$weight = 1/16;// min 1 ounce
		
		if( !$destination_country )
			$destination_country = $this->ups_country_code;
		
		if( !$destination_zip )
			$destination_zip = $this->ups_ship_from_zip;
		
		$shipper_data = $this->get_shipper_data( $ship_code, $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, $cart );
		$request = new WP_Http;
		$response = $request->request( $this->shipper_url, array( 'method' => 'POST', 'body' => $shipper_data, 'sslverify' => false ) );
		if( is_wp_error( $response ) ){
			$error_message = $response->get_error_message();
			$db = new ec_db( );
			$db->insert_response( 0, 0, "UPS ERROR", "error in ups get rate, " . $error_message );
			return false;
		}else{
			$db = new ec_db( );
			$db->insert_response( 0, 0, "UPS", print_r( $shipper_data, true ) . '------' . print_r( $response, true ) );
			return $this->process_response( $response['body'] );
		}
	}
	
	public function get_all_rates( $destination_zip, $destination_country, $weight, $length = 1, $width = 1, $height = 1, $declared_value = 0, $cart = array( ) ){
		if( $weight == 0 )
			$weight = 1/16;// min 1 ounce
		
		if( !$destination_country )
			$destination_country = $this->ups_country_code;
		
		if( !$destination_zip )
			$destination_zip = $this->ups_ship_from_zip;
		
		$shipper_data = $this->get_all_rates_shipper_data( $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, $cart );
		$request = new WP_Http;
		$response = $request->request( $this->shipper_url, array( 'method' => 'POST', 'body' => $shipper_data, 'sslverify' => false ) );
		if( is_wp_error( $response ) ){
			$error_message = $response->get_error_message();
			$db = new ec_db( );
			$db->insert_response( 0, 0, "UPS ERROR", "error in ups get rate, " . $error_message );
			return false;
		}else{
			$db = new ec_db( );
			$db->insert_response( 0, 0, "UPS", print_r( $shipper_data, true ) . '------' . print_r( $response, true ) );
			return $this->process_all_rates_response( $response['body'] );
		}
	}
	
	public function get_rate_test( $ship_code, $destination_zip, $destination_country, $weight, $length = 10, $width = 10, $height = 10, $declared_value = 0, $cart = array( ) ){
		if( $weight == 0 )
			$weight = 1/16;// min 1 ounce
		
		if( !$destination_country )
			$destination_country = $this->ups_country_code;
		
		$shipper_data = $this->get_all_rates_shipper_data( $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, $cart );
		$request = new WP_Http;
		$response = $request->request( $this->shipper_url, array( 'method' => 'POST', 'body' => $shipper_data, 'sslverify' => false ) );
		$db = new ec_db( );
		$db->insert_response( 0, 0, "UPS TEST", print_r( $response, true ) );
		if( is_wp_error( $response ) ){
			$error_message = $response->get_error_message();
			$db = new ec_db( );
			$db->insert_response( 0, 0, "UPS ERROR", "error in ups get rate, " . $error_message );
			return false;
		}else{
			$db = new ec_db( );
			$db->insert_response( 0, 0, "UPS Test", print_r( $shipper_data, true ) . '------' . print_r( $response, true ) );
			return $response['body'];
		}
	}
	
	private function get_shipper_data( $ship_code, $destination_zip, $destination_country, $weight, $length = 1, $width = 1, $height = 1, $declared_value = 0, $cart = array( ) ){
		$shipper_data = '<env:Envelope xmlns:env="http://schemas.xmlsoap.org/soap/envelope/" 
				xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
				xmlns:upss="http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0" 
				xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
				xmlns:common="http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0">
			<env:Header>
				<upss:UPSSecurity>
					<upss:UsernameToken>
						<upss:Username>' .  $this->ups_user_id . '</upss:Username>
						<upss:Password>' . htmlspecialchars( $this->ups_password ) . '</upss:Password>
					</upss:UsernameToken>
					<upss:ServiceAccessToken>
						<upss:AccessLicenseNumber>' . $this->ups_access_license_number . '</upss:AccessLicenseNumber>
					</upss:ServiceAccessToken>
				</upss:UPSSecurity>
			</env:Header>
			<env:Body>
				<rate:RateRequest 
					xmlns:rate="http://www.ups.com/XMLSchema/XOLTWS/Rate/v1.1" 
					xmlns:common="http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0" 
					xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
					<common:Request>
						<common:RequestOption>Rate</common:RequestOption>
						<common:TransactionReference>
							<common:CustomerContext>Rate Request</common:CustomerContext>
						</common:TransactionReference>
					</common:Request>
					<rate:PickupType>
						<rate:Code>01</rate:Code>
					</rate:PickupType>
					<rate:Shipment>
						<rate:Shipper>
							<rate:ShipperNumber>' . $this->ups_shipper_number . '</rate:ShipperNumber>
							<rate:Address>
								<rate:PostalCode>' . $this->ups_ship_from_zip . '</rate:PostalCode>
								<rate:CountryCode>' . $this->ups_country_code . '</rate:CountryCode>
							</rate:Address>
						</rate:Shipper>
						<rate:ShipTo>
							<rate:Address>
								<rate:PostalCode>' . $destination_zip . '</rate:PostalCode>
								<rate:CountryCode>' . $destination_country . '</rate:CountryCode>
							<rate:ResidentialAddressIndicator/>
							</rate:Address>
						</rate:ShipTo>
						<rate:ShipFrom>
							<rate:Address>
								<rate:PostalCode>' . $this->ups_ship_from_zip . '</rate:PostalCode>
								<rate:CountryCode>' . $this->ups_country_code . '</rate:CountryCode>
							</rate:Address>
						</rate:ShipFrom>
						<rate:Service>
							<rate:Code>' . $ship_code . '</rate:Code>
						</rate:Service>';
						
						if( get_option( 'ec_option_ship_items_seperately' ) && count( $cart ) > 0 ){
							foreach( $cart as $cartitem ){
								if( $cartitem->is_shippable && !$cartitem->exclude_shippable_calculation ){
									for( $i=0; $i<$cartitem->quantity; $i++ ){
										$shipper_data .= "
						<rate:Package>
							<rate:PackagingType>
								<rate:Code>02</rate:Code>
							</rate:PackagingType>
							<rate:Dimensions>
								<rate:UnitOfMeasurement>
									<rate:Code>";
		
									if( $this->ups_weight_type == 'LBS' ){
										$shipper_data .= "IN";
									}else{
										$shipper_data .= "CM";
									}
		
									$shipper_data .= "</rate:Code>
								</rate:UnitOfMeasurement>
								<rate:Length>" . ceil( $cartitem->length ) . "</rate:Length>
								<rate:Width>" . ceil( $cartitem->width ) . "</rate:Width>
								<rate:Height>" . ceil( $cartitem->height ) . "</rate:Height>
							</rate:Dimensions>
							<rate:PackageWeight>
								<rate:UnitOfMeasurement>
									<rate:Code>$this->ups_weight_type</rate:Code>
								</rate:UnitOfMeasurement>
								<rate:Weight>" . ( ( $cartitem->weight > 0 ) ? $cartitem->weight : (1/16) ) . "</rate:Weight>
							</rate:PackageWeight>
						</rate:Package>";
						
									}// close quantity loop
								}// close is shippable check
							}// close cart item loop
							
						}else{
						
							$package_total = 0;
							$last_package_i = 0;
							
							// Generate Product List
							$current_weight = 0;
							$products = array( );
							foreach( $cart as $cartitem ){
								if( $cartitem->is_shippable && !$cartitem->exclude_shippable_calculation ){
									// Each quantity item is a new product in the shipping world
									for( $i=0; $i<$cartitem->quantity; $i++ ){
										
										// Add the new product
										$products[] = array( 
											'width' 	=> $cartitem->width,
											'height'	=> $cartitem->height,
											'length'	=> $cartitem->length,
											'weight'	=> $cartitem->weight 
										);
										$current_weight += $cartitem->weight;
										
										// create a package
										$parcel = $this->calculate_parcel( $products );
										
										if( $current_weight > 150 ){
											$shipper_data .= "
							<rate:Package>
								<rate:PackagingType>
									<rate:Code>02</rate:Code>
								</rate:PackagingType>
								<rate:Dimensions>
									<rate:UnitOfMeasurement>
										<rate:Code>";
			
										if( $this->ups_weight_type == 'LBS' ){
											$shipper_data .= "IN";
										}else{
											$shipper_data .= "CM";
										}
			
										$shipper_data .= "</rate:Code>
									</rate:UnitOfMeasurement>
									<rate:Length>" . ceil( $parcel['length'] ) . "</rate:Length>
									<rate:Width>" . ceil( $parcel['width'] ) . "</rate:Width>
									<rate:Height>" . ceil( $parcel['height'] ) . "</rate:Height>
								</rate:Dimensions>
								<rate:PackageWeight>
									<rate:UnitOfMeasurement>
										<rate:Code>$this->ups_weight_type</rate:Code>
									</rate:UnitOfMeasurement>
									<rate:Weight>" . $current_weight . "</rate:Weight>
								</rate:PackageWeight>
							</rate:Package>";
											
											// Reset product list
											$products = array( );
											$current_weight = 0;
										}
									
								}// close quantity loop
								}// close is shippable check
							}// close cart item loop
							
							// Maybe insert remaining items as a final package
							if( count( $products ) > 0 ){
							
								$parcel = $this->calculate_parcel( $products );
								$shipper_data .= "
						<rate:Package>
							<rate:PackagingType>
								<rate:Code>02</rate:Code>
							</rate:PackagingType>
							<rate:Dimensions>
								<rate:UnitOfMeasurement>
									<rate:Code>";
		
									if( $this->ups_weight_type == 'LBS' ){
										$shipper_data .= "IN";
									}else{
										$shipper_data .= "CM";
									}
		
									$shipper_data .= "</rate:Code>
								</rate:UnitOfMeasurement>
								<rate:Length>" . ceil( $parcel['length'] ) . "</rate:Length>
								<rate:Width>" . ceil( $parcel['width'] ) . "</rate:Width>
								<rate:Height>" . ceil( $parcel['height'] ) . "</rate:Height>
							</rate:Dimensions>
							<rate:PackageWeight>
								<rate:UnitOfMeasurement>
									<rate:Code>$this->ups_weight_type</rate:Code>
								</rate:UnitOfMeasurement>
								<rate:Weight>" . ( ( $current_weight > 0 ) ? $current_weight : (1/16) ) . "</rate:Weight>
							</rate:PackageWeight>
						</rate:Package>";
							}
						
						}
					$shipper_data .= "
					</rate:Shipment>
				</rate:RateRequest>
			</env:Body> 
		</env:Envelope>";
		return $shipper_data;
	}
	
	private function get_all_rates_shipper_data( $destination_zip, $destination_country, $weight = 1, $length = 1, $width = 1, $height = 1, $declared_value = 0, $cart = array( ) ){
		$shipper_data = '<env:Envelope xmlns:env="http://schemas.xmlsoap.org/soap/envelope/" 
				xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
				xmlns:upss="http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0" 
				xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
				xmlns:common="http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0">
			<env:Header>
				<upss:UPSSecurity>
					<upss:UsernameToken>
						<upss:Username>' .  $this->ups_user_id . '</upss:Username>
						<upss:Password>' . htmlspecialchars( $this->ups_password ) . '</upss:Password>
					</upss:UsernameToken>
					<upss:ServiceAccessToken>
						<upss:AccessLicenseNumber>' . $this->ups_access_license_number . '</upss:AccessLicenseNumber>
					</upss:ServiceAccessToken>
				</upss:UPSSecurity>
			</env:Header>
			<env:Body>
				<rate:RateRequest 
					xmlns:rate="http://www.ups.com/XMLSchema/XOLTWS/Rate/v1.1" 
					xmlns:common="http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0" 
					xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
					<common:Request>
						<common:RequestOption>Shop</common:RequestOption>
						<common:TransactionReference>
							<common:CustomerContext>Rate Request</common:CustomerContext>
						</common:TransactionReference>
					</common:Request>
					<rate:PickupType>
						<rate:Code>01</rate:Code>
					</rate:PickupType>
					<rate:Shipment>
						<rate:Shipper>
							<rate:ShipperNumber>' . $this->ups_shipper_number . '</rate:ShipperNumber>
							<rate:Address>
								<rate:PostalCode>' . $this->ups_ship_from_zip . '</rate:PostalCode>
								<rate:CountryCode>' . $this->ups_country_code . '</rate:CountryCode>
							</rate:Address>
						</rate:Shipper>
						<rate:ShipTo>
							<rate:Address>
								<rate:PostalCode>' . $destination_zip . '</rate:PostalCode>
								<rate:CountryCode>' . $destination_country . '</rate:CountryCode>
							<rate:ResidentialAddressIndicator/>
							</rate:Address>
						</rate:ShipTo>
						<rate:ShipFrom>
							<rate:Address>';
									if( $this->ups_negotiated_rates ){
									$shipper_data .= "
								<rate:StateProvinceCode>$this->ups_ship_from_state</rate:StateProvinceCode>";
									}
									$shipper_data .= "
								<rate:PostalCode>$this->ups_ship_from_zip</rate:PostalCode>
								<rate:CountryCode>$this->ups_country_code</rate:CountryCode>
							</rate:Address>
						</rate:ShipFrom>";
						
						if( get_option( 'ec_option_ship_items_seperately' ) && count( $cart ) > 0 ){
							foreach( $cart as $cart_item ){
								if( $cart_item->is_shippable && !$cart_item->exclude_shippable_calculation ){
									for( $i=0; $i<$cart_item->quantity; $i++ ){
										$shipper_data .= "
						<rate:Package>
							<rate:PackagingType>
								<rate:Code>02</rate:Code>
							</rate:PackagingType>						
							<rate:PackageWeight>
								<rate:UnitOfMeasurement>
									<rate:Code>$this->ups_weight_type</rate:Code>
								</rate:UnitOfMeasurement>
								<rate:Weight>" . ( ( $cart_item->weight > 0 ) ? $cart_item->weight : (1/16) ) . "</rate:Weight>
							</rate:PackageWeight>
							<rate:Dimensions>
								<rate:UnitOfMeasurement>
									<rate:Code>";
		
									if( $this->ups_weight_type == 'LBS' ){
										$shipper_data .= "IN";
									}else{
										$shipper_data .= "CM";
									}
		
									$shipper_data .= "</rate:Code>
								</rate:UnitOfMeasurement>
								<rate:Length>" . ceil( $cart_item->length ) . "</rate:Length>
								<rate:Width>" . ceil( $cart_item->width ) . "</rate:Width>
								<rate:Height>" . ceil( $cart_item->height ) . "</rate:Height>
							</rate:Dimensions>
							<rate:PackageServiceOptions>
								<rate:CurrencyCode>" . get_option( 'ec_option_base_currency' ) . "</rate:CurrencyCode>
								<rate:MonetaryValue>" . $cart_item->unit_price . "</rate:MonetaryValue>
							</rate:PackageServiceOptions>
						</rate:Package>";
									}// close quantity loop
								}// close is shippable check
							}// close cart item loop
						
						}else{
					
							$package_total = 0;
							$last_package_i = 0;
							
							// Generate Product List
							$current_weight = 0;
							$products = array( );
							foreach( $cart as $cartitem ){
								if( !isset( $cartitem->exclude_shippable_calculation ) ){
                                    $cartitem->exclude_shippable_calculation = 0;
                                }
								if( $cartitem->is_shippable && !$cartitem->exclude_shippable_calculation ){
									// Each quantity item is a new product in the shipping world
									for( $i=0; $i<$cartitem->quantity; $i++ ){
										
										// Add the new product
										$products[] = array( 
											'width' 	=> $cartitem->width,
											'height'	=> $cartitem->height,
											'length'	=> $cartitem->length,
											'weight'	=> $cartitem->weight 
										);
										$current_weight += $cartitem->weight;
										
										// create a package
										$parcel = $this->calculate_parcel( $products );
										
										if( $current_weight > 150 ){
											$shipper_data .= "
								<rate:Package>
									<rate:PackagingType>
										<rate:Code>02</rate:Code>
									</rate:PackagingType>
									<rate:Dimensions>
										<rate:UnitOfMeasurement>
											<rate:Code>";
				
											if( $this->ups_weight_type == 'LBS' ){
												$shipper_data .= "IN";
											}else{
												$shipper_data .= "CM";
											}
				
											$shipper_data .= "</rate:Code>
										</rate:UnitOfMeasurement>
										<rate:Length>" . ceil( $parcel['length'] ) . "</rate:Length>
										<rate:Width>" . ceil( $parcel['width'] ) . "</rate:Width>
										<rate:Height>" . ceil( $parcel['height'] ) . "</rate:Height>
									</rate:Dimensions>
									<rate:PackageWeight>
										<rate:UnitOfMeasurement>
											<rate:Code>$this->ups_weight_type</rate:Code>
										</rate:UnitOfMeasurement>
										<rate:Weight>" . $current_weight . "</rate:Weight>
									</rate:PackageWeight>
								</rate:Package>";
											
											// Reset product list
											$products = array( );
											$current_weight = 0;
										}
									
									}// close quantity loop
								}// close is shippable check
							}// close cart item loop
							
							// Maybe insert remaining items as a final package
							if( count( $products ) > 0 ){
							
								$parcel = $this->calculate_parcel( $products );
								$shipper_data .= "
							<rate:Package>
								<rate:PackagingType>
									<rate:Code>02</rate:Code>
								</rate:PackagingType>
								<rate:Dimensions>
									<rate:UnitOfMeasurement>
										<rate:Code>";
			
										if( $this->ups_weight_type == 'LBS' ){
											$shipper_data .= "IN";
										}else{
											$shipper_data .= "CM";
										}
			
										$shipper_data .= "</rate:Code>
									</rate:UnitOfMeasurement>
									<rate:Length>" . ceil( $parcel['length'] ) . "</rate:Length>
									<rate:Width>" . ceil( $parcel['width'] ) . "</rate:Width>
									<rate:Height>" . ceil( $parcel['height'] ) . "</rate:Height>
								</rate:Dimensions>
								<rate:PackageWeight>
									<rate:UnitOfMeasurement>
										<rate:Code>$this->ups_weight_type</rate:Code>
									</rate:UnitOfMeasurement>
									<rate:Weight>" . ( ( $current_weight > 0 ) ? $current_weight : (1/16) ) . "</rate:Weight>
								</rate:PackageWeight>
							</rate:Package>";
							}
							
						
						}
						
						if( $this->ups_negotiated_rates ){
						$shipper_data .= "
						<rate:RateInformation>
							<rate:NegotiatedRatesIndicator></rate:NegotiatedRatesIndicator>
						</rate:RateInformation>";
						}
					$shipper_data .= "
					</rate:Shipment>
				</rate:RateRequest>
			</env:Body> 
		</env:Envelope>";
		return $shipper_data;
	}
	
	private function process_response( $result ){
		$ups_response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result );
		$ups_xml = new SimpleXMLElement( $ups_response );
		$body = $ups_xml->xpath( '//soapenvBody' );
		$xml = $body[0];
		
		if( $xml && $xml->rateRateResponse->rateRatedShipment && $xml->rateRateResponse->rateRatedShipment->rateTotalCharges && $xml->rateRateResponse->rateRatedShipment->rateTotalCharges->rateMonetaryValue )
			$rate = $xml->rateRateResponse->rateRatedShipment->rateTotalCharges->rateMonetaryValue;
		else
			$rate = "ERROR";
			
		if( isset( $rate ) ){
			$rate = floatval( $rate );
			return ( $rate * $this->ups_conversion_rate );
		}else{
			return "ERROR";	
		}
	} 
	
	private function process_all_rates_response( $result ){
		$rates = array( );
		$ups_response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result );
		$ups_xml = new SimpleXMLElement( $ups_response );
		$body = $ups_xml->xpath( '//soapenvBody' );
		$xml = $body[0];
		
		for( $i=0; $i<count( $xml->rateRateResponse->rateRatedShipment ); $i++ ){
			$rates[] = array( 'rate_code' => (string) $xml->rateRateResponse->rateRatedShipment[$i]->rateService->rateCode, 'rate' => number_format( floatval( $xml->rateRateResponse->rateRatedShipment[$i]->rateTotalCharges->rateMonetaryValue ) * $this->ups_conversion_rate, 2, ".", "" ), 'delivery_days' =>  (string) $xml->rateRateResponse->rateRatedShipment[$i]->rateGuaranteedDelivery->rateBusinessDaysInTransit );
		}
		
		return $rates;
		
	}
	
	public function validate_address( $destination_city, $destination_state, $destination_zip, $destination_country ){
		
		// For now, limit errors
		return true;
		
		// This service is limited to the US by EasyCart to prevent common issues.
		if( $this->ups_country_code != "US" || $destination_country != "US" )
			return true;
		
		$shipper_data = "<?xml version=\"1.0\"?>
			<AccessRequest xml:lang=\"en-US\">
				<AccessLicenseNumber>$this->ups_access_license_number</AccessLicenseNumber>
				<UserId>$this->ups_user_id</UserId>
				<Password>" . htmlspecialchars( $this->ups_password ) . "</Password>
			</AccessRequest>
			<?xml version=\"1.0\"?>
			<AddressValidationRequest xml:lang=\"en-US\">
				<Request>
					<TransactionReference>
						<CustomerContext>Validate Address</CustomerContext>
						<XpciVersion>1.0001</XpciVersion>
					</TransactionReference>
					<RequestAction>AV</RequestAction>
				</Request>
				<Address>
					<City>$destination_city</City>";
		
		if( $destination_state ){		
			$shipper_data .= "
					<StateProvinceCode>$destination_state</StateProvinceCode>";
		}
		
		$shipper_data .= "
					<PostalCode>$destination_zip</PostalCode>
					<CountryCode>$destination_country</CountryCode>
				</Address>
			</AddressValidationRequest>";
		
		$request = new WP_Http;
		$response = $request->request( "https://onlinetools.ups.com/ups.app/xml/AV", array( 'method' => 'POST', 'body' => $shipper_data, 'sslverify' => false ) );
		if( is_wp_error( $response ) ){
			$error_message = $response->get_error_message();
			$db = new ec_db( );
			$db->insert_response( 0, 0, "UPS ERROR", "error in ups address validation, " . $error_message );
			return false;
		}else{
			
			$xml = new SimpleXMLElement( $response['body'] );
			
			if( $xml->Response->ResponseStatusCode == '0' )
				return false;
			else
				return true;
			
		}
			
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