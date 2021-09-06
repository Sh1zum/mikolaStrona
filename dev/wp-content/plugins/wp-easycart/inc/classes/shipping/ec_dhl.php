<?php
	
class ec_dhl{
	
	private $dhl_site_id;												// Your DHL Site ID
	private $dhl_password;												// Your DHL Password
	private $dhl_ship_from_country;										// Your DHL Country
	private $dhl_ship_from_zip;											// Your DHL Zip
	private $dhl_weight_unit;											// Your DHL Weight Unit

	function __construct( $ec_setting ){
		$this->dhl_site_id = $ec_setting->get_dhl_site_id( );
		$this->dhl_password = $ec_setting->get_dhl_password( );	
		$this->dhl_ship_from_country = $ec_setting->get_dhl_ship_from_country( );
		$this->dhl_ship_from_zip = $ec_setting->get_dhl_ship_from_zip( );
		$this->dhl_weight_unit = $ec_setting->get_dhl_weight_unit( );
		$this->dhl_test_mode = $ec_setting->get_dhl_test_mode( );
		
		if( $this->dhl_test_mode )
			$this->shipper_url = "https://xmlpitest-ea.dhl.com/XMLShippingServlet";
		else
			$this->shipper_url = "https://xmlpi-ea.dhl.com/XMLShippingServlet";
	}
		
	public function get_rate( $ship_code, $destination_zip, $destination_country, $weight, $length = 1, $width = 1, $height = 1, $declared_value = 0, $cart = array( ) ){
		
		if( $this->dhl_site_id == "" || $this->dhl_password == "" || $this->dhl_ship_from_zip == "" ){
			return "ERROR";
		}else{
		
			if( $weight == 0 )
				return "0.00";
				
			if( !$destination_country )
				$destination_country = $this->dhl_ship_from_country;
				
			if( !$destination_zip || $destination_zip == "" )
				$destination_zip = $this->dhl_ship_from_zip;
			
			$ship_data = $this->get_shipper_data( $ship_code, $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, $cart );
			
			$ch = curl_init(); //initiate the curl session 
			curl_setopt($ch, CURLOPT_URL, $this->shipper_url); //set to url to post to
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // tell curl to return data in a variable 
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POST, true); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $ship_data ); // post the xml 
			curl_setopt($ch, CURLOPT_TIMEOUT, (int)30); // set timeout in seconds 
			$response = curl_exec($ch);
			curl_close ($ch); 
			
			return $this->process_response( $response, $ship_code );
			
		}
		
	}
	
	public function get_all_rates( $destination_zip, $destination_country, $weight, $length = 1, $width = 1, $height = 1, $declared_value = 0, $cart = array( ) ){
		
		if( $this->dhl_site_id == "" || $this->dhl_password == "" || $this->dhl_ship_from_zip == "" ){
			return "ERROR";
		}else{
		
			if( $weight == 0 )
				return "0.00";
				
			if( !$destination_country )
				$destination_country = $this->dhl_ship_from_country;
				
			if( !$destination_zip || $destination_zip == "" )
				$destination_zip = $this->dhl_ship_from_zip;
			
			$ship_data = $this->get_shipper_data( $ship_code, $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, $cart );
			
			$ch = curl_init(); //initiate the curl session 
			curl_setopt($ch, CURLOPT_URL, $this->shipper_url); //set to url to post to
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // tell curl to return data in a variable 
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POST, true); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $ship_data ); // post the xml 
			curl_setopt($ch, CURLOPT_TIMEOUT, (int)30); // set timeout in seconds 
			$response = curl_exec($ch);
			curl_close ($ch);
			
			return $this->process_all_rates_response( $response );
			
		}
		
	}
	
	public function get_rate_test( $ship_code, $destination_zip, $destination_country, $weight, $length = 1, $width = 1, $height = 1, $declared_value = 0, $cart = array( ) ){
		
		if( $weight == 0 )
			return "0.00";
			
		$destination_country = "US";
		$destination_zip = "97801";
		
		$db = new ec_db( );
		$ship_data = $this->get_shipper_data( $ship_code, $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, $cart );
		
		$ch = curl_init(); //initiate the curl session 
		curl_setopt( $ch, CURLOPT_URL, $this->shipper_url ); //set to url to post to
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // tell curl to return data in a variable 
		curl_setopt( $ch, CURLOPT_HEADER, false );
		curl_setopt( $ch, CURLOPT_POST, true ); 
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $ship_data ); // post the xml 
		curl_setopt( $ch, CURLOPT_TIMEOUT, ( int )30 ); // set timeout in seconds 
		$response = curl_exec( $ch );
		if( $response === false )
			$db->insert_response( 0, 0, "DHL CURL ERROR", curl_error( $ch ) );
		curl_close( $ch ); 
		
		$db->insert_response( 0, 0, "DHL TEST", print_r( $response, true ) );
		
		return $response;
		
	}
	
	private function get_shipper_data( $ship_code, $destination_zip, $destination_country, $weight, $length = 1, $width = 1, $height = 1, $declared_value = 0, $cart = array( ) ){
		
		$dimensions = ( get_option( 'ec_option_enable_metric_unit_display' ) ) ? 'CM' : 'IN';
		if( $destination_country == $this->dhl_ship_from_country )
			$is_dutiable = "N";
		else
			$is_dutiable = "Y";
			
		if( $length <= 0 )
			$length = 1;
				
		if( $width <= 0 )
			$width = 1;
			
		if( $height <= 0 )
			$height = 1;
		
		$shipper_data = '<?xml version="1.0" encoding="UTF-8"?>
						<p:DCTRequest xmlns:p="http://www.dhl.com" xmlns:p1="http://www.dhl.com/datatypes" xmlns:p2="http://www.dhl.com/DCTRequestdatatypes" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.dhl.com DCT-req.xsd ">
						  <GetQuote>
							<Request>
							  <ServiceHeader>
								<MessageTime>'.date('c').'</MessageTime>
								<MessageReference>' . rand( 1, 9 ) . rand( 1000000000, 9999999999 ) . rand( 1000000000, 9999999999 ) . rand( 1000000000, 9999999999 ). '</MessageReference>
								<SiteID>' . $this->dhl_site_id . '</SiteID>
								<Password>' . $this->dhl_password . '</Password>
							  </ServiceHeader>
							</Request>
							<From>
							  <CountryCode>' . $this->dhl_ship_from_country . '</CountryCode>
							  <Postalcode>' . $this->dhl_ship_from_zip . '</Postalcode>
							</From>
							<BkgDetails>
							  <PaymentCountryCode>' . $destination_country . '</PaymentCountryCode>
							  <Date>' . date( 'Y-m-d', strtotime( "+1 day" ) ) . '</Date>
							  <ReadyTime>PT10H21M</ReadyTime>
									<ReadyTimeGMTOffset>+00:00</ReadyTimeGMTOffset>
									<DimensionUnit>' . $dimensions . '</DimensionUnit>
							  		<WeightUnit>' . $this->dhl_weight_unit . '</WeightUnit>
									<Pieces><Piece>
										<PieceID>1</PieceID>
										<Height>' . $height . '</Height>
										<Depth>' . $length . '</Depth>
										<Width>' . $width . '</Width>
										<Weight>' . $weight . '</Weight>
									</Piece></Pieces>';
		if( get_option( 'ec_option_dhl_account_number' ) != '' ){
		$shipper_data .= '
									<PaymentAccountNumber>' . get_option( 'ec_option_dhl_account_number' ) . '</PaymentAccountNumber>';	
		}
		$shipper_data .= '
									<IsDutiable>' . $is_dutiable . '</IsDutiable>
									<NetworkTypeCode>AL</NetworkTypeCode>
								</BkgDetails>
								<To>
									<CountryCode>' . $destination_country . '</CountryCode>
							  		<Postalcode>' . $destination_zip . '</Postalcode>
								</To>';
		if( $is_dutiable == "Y" ){
		$shipper_data .= '
								<Dutiable>
									<DeclaredCurrency>' . get_option( 'ec_option_base_currency' ) . '</DeclaredCurrency>
									<DeclaredValue>' . number_format( $declared_value, 2, '.', '' ) . '</DeclaredValue>
								</Dutiable>';
		}
		$shipper_data .= '						       
							</GetQuote>
						</p:DCTRequest>';
		
		return $shipper_data;
	}
	
	private function process_all_rates_response( $result ){
		
		$rates = array( );
		
		$xml = new SimpleXMLElement($result);
		
		if( $xml && $xml->GetQuoteResponse && $xml->GetQuoteResponse->BkgDetails && $xml->GetQuoteResponse->BkgDetails->QtdShp ){
			if( count( $xml->GetQuoteResponse->BkgDetails->QtdShp ) > 1 ){
				for( $i=0; $i<count( $xml->GetQuoteResponse->BkgDetails->QtdShp ); $i++ ){
					$rates[] = array( 	
						'rate_code' => $this->get_product_code( $xml->GetQuoteResponse->BkgDetails->QtdShp[$i]->ProductShortName ), 
						'rate' 		=> (string) $xml->GetQuoteResponse->BkgDetails->QtdShp[$i]->ShippingCharge
					);
				}
			}else{
				$rates[] = array(
					'rate_code' 	=> $this->get_product_code( $xml->GetQuoteResponse->BkgDetails->QtdShp->ProductShortName ), 
					'rate' 			=> (string) $xml->GetQuoteResponse->BkgDetails->QtdShp->ShippingCharge
				);
			}
		}
		
		return $rates;
	}
	
	private function process_response( $result, $rate_code ){
		
		$xml = new SimpleXMLElement($result);
		
		if( $xml && $xml->GetQuoteResponse && $xml->GetQuoteResponse->BkgDetails && $xml->GetQuoteResponse->BkgDetails->QtdShp ){
			if( count( $xml->GetQuoteResponse->BkgDetails->QtdShp ) > 1 ){
				$rate = "ERROR";
				for( $i=0; $i<count( $xml->GetQuoteResponse->BkgDetails->QtdShp ); $i++ ){
					if( $xml->GetQuoteResponse->BkgDetails->QtdShp[$i]->ProductShortName == $this->get_product_name( $rate_code ) ){
						$rate = $xml->GetQuoteResponse->BkgDetails->QtdShp[$i]->ShippingCharge;
					}
				}
			}else{
				if( $xml->GetQuoteResponse->BkgDetails->QtdShp->ProductShortName == $this->get_product_name( $rate_code ) )
					$rate = $xml->GetQuoteResponse->BkgDetails->QtdShp->ShippingCharge;
				else
					$rate = "ERROR";
			}
		}else{
			$rate = "ERROR";
		}
		
		if( $rate == "0.00" )
			$rate = "ERROR";
		
		if( $rate )
			return $rate;
		else{
			error_log( "error in DHL get rate, response: " . $result );
			return "ERROR";
		}
	}
	
	private function get_product_name( $rate_code ){
		//--------------------------
		// DHL product codes
		//--------------------------
		switch( $rate_code ){
			case '0':
				return "LOGISTICS SERVICES";
				break;
			case '1':
				return "DOMESTIC EXPRESS 12:00";
				break;
			case '2':
				return "B2C";
				break;
			case '3':
				return "B2C";
				break;
			case '4':
				return "JETLINE";
				break;
			case '5':
				return "SPRINTLINE";
				break;
			case '6':
				return "SECURELINE";
				break;
			case '7':
				return "EXPRESS EASY";
				break;
			case '8':
				return "EXPRESS EASY";
				break;
			case '9':
				return "EUROPACK";
				break;
			case 'A':
				return "AUTO REVERSALS";
				break;
			case 'B':
				return "BREAK BULK EXPRESS";
				break;
			case 'C':
				return "MEDICAL EXPRESS";
				break;
			case 'D':
				return "EXPRESS WORLDWIDE";
				break;
			case 'E':
				return "EXPRESS 9:00";
				break;
			case 'F':
				return "FREIGHT WORLDWIDE";
				break;
			case 'G':
				return "DOMESTIC ECONOMY SELECT";
				break;
			case 'H':
				return "ECONOMY SELECT";
				break;
			case 'I':
				return "BREAK BULK ECONOMY";
				break;
			case 'J':
				return "JUMBO BOX";
				break;
			case 'K':
				return "EXPRESS 9:00";
				break;
			case 'L':
				return "EXPRESS 10:30";
				break;
			case 'M':
				return "EXPRESS 10:30";
				break;
			case 'N':
				return "EXPRESS DOMESTIC";
				break;
			case 'O':
				return "DOM EXPRESS 10:30";
				break;
			case 'P':
				return "";
				break;
			case 'U':
				return "EXPRESS WORLDWIDE";
				break;
			case 'Q':
				return "MEDICAL EXPRESS";
				break;
			case 'R':
				return "GLOBALMAIL BUSINESS";
				break;
			case 'S':
				return "SAME DAY";
				break;
			case 'T':
				return "EXPRESS 12:00";
				break;
			case 'V':
				return "EUROPACK";
				break;
			case 'W':
				return "ECONOMY SELECT";
				break;
			case 'X':
				return "EXPRESS ENVELOPE";
				break;
			case 'Y':
				return "EXPRESS 12:00";
				break;
			case 'Z':
				return "Destination Charges";
				break;
		}
	}
	
	private function get_product_code( $rate_name ){
		//--------------------------
		// DHL product codes
		//--------------------------
		switch( $rate_name ){
			case "LOGISTICS SERVICES":
				return '0';
				break;
			case "DOMESTIC EXPRESS 12:00":
				return '1';
				break;
			case "B2C":
				return '2';
				break;
			case "B2C":
				return '3';
				break;
			case "JETLINE":
				return '4';
				break;
			case "SPRINTLINE":
				return '5';
				break;
			case "SECURELINE":
				return '6';
				break;
			case "EXPRESS EASY":
				return '7';
				break;
			case "EXPRESS EASY":
				return '8';
				break;
			case "EUROPACK":
				return '9';
				break;
			case "AUTO REVERSALS":
				return 'A';
				break;
			case "BREAK BULK EXPRESS":
				return 'B';
				break;
			case "MEDICAL EXPRESS":
				return 'C';
				break;
			case "EXPRESS WORLDWIDE":
				return 'D';
				break;
			case "EXPRESS 9:00":
				return 'E';
				break;
			case "FREIGHT WORLDWIDE":
				return 'F';
				break;
			case "DOMESTIC ECONOMY SELECT":
				return 'G';
				break;
			case "ECONOMY SELECT":
				return 'H';
				break;
			case "BREAK BULK ECONOMY":
				return 'I';
				break;
			case "JUMBO BOX":
				return 'J';
				break;
			case "EXPRESS 9:00":
				return 'K';
				break;
			case "EXPRESS 10:30":
				return 'L';
				break;
			case "EXPRESS 10:30":
				return 'M';
				break;
			case "EXPRESS DOMESTIC":
				return 'N';
				break;
			case "DOM EXPRESS 10:30":
				return 'O';
				break;
			case "":
				return 'P';
				break;
			case "EXPRESS WORLDWIDE":
				return 'U';
				break;
			case "MEDICAL EXPRESS":
				return 'Q';
				break;
			case "GLOBALMAIL BUSINESS":
				return 'R';
				break;
			case "SAME DAY":
				return 'S';
				break;
			case "EXPRESS 12:00":
				return 'T';
				break;
			case "EUROPACK":
				return 'V';
				break;
			case "ECONOMY SELECT":
				return 'W';
				break;
			case "EXPRESS ENVELOPE":
				return 'X';
				break;
			case "EXPRESS 12:00":
				return 'Y';
				break;
			case "Destination Charges":
				return 'Z';
				break;
		}
	}
	
	public function validate_address( $desination_address, $destination_city, $destination_state, $destination_zip, $destination_country ){
		
		$ship_data = $this->get_shipper_data( $ship_code, $destination_zip, $destination_country, '2' );
		
		$ch = curl_init(); //initiate the curl session 
		curl_setopt($ch, CURLOPT_URL, $this->shipper_url); //set to url to post to
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // tell curl to return data in a variable 
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $ship_data ); // post the xml 
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)30); // set timeout in seconds 
		$response = curl_exec($ch);
		curl_close ($ch);
		
		$xml = new SimpleXMLElement( $response );
		if( isset( $xml ) && isset( $xml->GetQuoteResponse ) && isset( $xml->GetQuoteResponse->Note ) )
			return false;
		else
			return true;
		
	}
}	
?>