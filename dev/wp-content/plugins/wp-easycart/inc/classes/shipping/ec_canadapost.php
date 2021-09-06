<?php
	
class ec_canadapost{
	private $canadapost_username; 								// Your Australian Post API Key
	private $canadapost_password; 								// Your Australian Post ship from zip code
	private $canadapost_customer_number;						// String
	private $canadapost_contract_id;							// String
	private $canadapost_test_mode;								// BOOL
	
	private $canadapost_ship_from_zip;							// String
	
	private $canadapost_url;									// String
	
	function __construct( $ec_setting ){
		$this->canadapost_username = $ec_setting->get_canadapost_username( );
		$this->canadapost_password = $ec_setting->get_canadapost_password( );
		$this->canadapost_customer_number = $ec_setting->get_canadapost_customer_number( );
		$this->canadapost_contract_id = $ec_setting->get_canadapost_contract_id( );
		$this->canadapost_test_mode = $ec_setting->get_canadapost_test_mode( );
		
		$this->canadapost_ship_from_zip = $ec_setting->get_canadapost_ship_from_zip( );
		
		if( $this->canadapost_test_mode )
			$this->canadapost_url = "https://ct.soa-gw.canadapost.ca/rs/ship/price";
		else
			$this->canadapost_url = "https://soa-gw.canadapost.ca/rs/ship/price";
	}
	
	public function get_rate( $ship_code, $destination_zip, $destination_country, $weight, $length = 10, $width = 10, $height = 10, $is_test = false ){
		
		if( $weight == 0 )
			return "0.00";

		if( $length <= 0 )
			$length = 1;
		
		if( $width <= 0 )
			$width = 1;

		if( $height <= 0 )
			$height = 1;
		
		if( !$destination_country )
			$destination_country = "CA";
			
		if( !$destination_zip || $destination_zip == "" )
			$destination_zip = $this->canadapost_ship_from_zip;
		
		$xmlRequest = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<mailing-scenario xmlns=\"http://www.canadapost.ca/ws/ship/rate-v3\">
				<customer-number>" . $this->canadapost_customer_number . "</customer-number>";
				if( $this->canadapost_contract_id ){ 
				$xmlRequest .= "
				<contract-id>" . $this->canadapost_contract_id . "</contract-id>";
				}
				$xmlRequest .= "
				<parcel-characteristics>
					<weight>" . $weight . "</weight>
					<dimensions>
						<length>" . $length . "</length>
						<width>" . $width . "</width>
						<height>" . $height . "</height>
					</dimensions>
				</parcel-characteristics>
				<services>
					<service-code>" . $ship_code . "</service-code>
				</services>
				<origin-postal-code>" . $this->format_zip_code( $this->canadapost_ship_from_zip ) . "</origin-postal-code>
				<destination>";
				if( $destination_country == "CA" ){
				$xmlRequest .= "
					<domestic>
						<postal-code>" . $this->format_zip_code( $destination_zip ) . "</postal-code>
					</domestic>";
				}else if( $destination_country == "US" ){
				$xmlRequest .= "
					<united-states>
						<zip-code>" . $this->format_zip_code( $destination_zip ) . "</zip-code>
					</united-states>";
				}else{
				$xmlRequest .= "
					<international>
						<country-code>" . $destination_country . "</country-code>
					</international>";
				}
				$xmlRequest .= "
				</destination>
			</mailing-scenario>";
			
		$headers = array( 	'Content-Type: application/vnd.cpc.ship.rate-v3+xml', 
							'Accept: application/vnd.cpc.ship.rate-v3+xml', 
							'Authorization: Basic ' . base64_encode( $this->canadapost_username . ':' . $this->canadapost_password ) );
			
		$curl = curl_init( $this->canadapost_url ); // Create REST Request
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlRequest);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($curl); // Execute REST Request
		if( $response === false ){
			$db = new ec_db( );
			$db->insert_response( 0, 1, "CANADA POST CURL ERROR", curl_error( $curl ) );
		}
		curl_close($curl);
		
		if( $is_test )
			return $response;
		
		return $this->process_response( $response );
		
	}
	
	public function get_all_rates( $destination_zip, $destination_country, $weight, $length = 10, $width = 10, $height = 10 ){
        $db = new ec_db( );
		
		if( $weight == 0 )
			return "0.00";

		if( $length <= 0 )
			$length = 1;
		
		if( $width <= 0 )
			$width = 1;

		if( $height <= 0 )
			$height = 1;
		
		if( !$destination_country )
			$destination_country = "CA";
			
		if( !$destination_zip || $destination_zip == "" )
			$destination_zip = $this->canadapost_ship_from_zip;
		
		$xmlRequest = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<mailing-scenario xmlns=\"http://www.canadapost.ca/ws/ship/rate-v3\">
				<customer-number>" . $this->canadapost_customer_number . "</customer-number>";
				if( $this->canadapost_contract_id ){ 
				$xmlRequest .= "
				<contract-id>" . $this->canadapost_contract_id . "</contract-id>";
				}
				$xmlRequest .= "
				<parcel-characteristics>
					<weight>" . $weight . "</weight>
					<dimensions>
						<length>" . $length . "</length>
						<width>" . $width . "</width>
						<height>" . $height . "</height>
					</dimensions>
				</parcel-characteristics>
				<origin-postal-code>" . $this->format_zip_code( $this->canadapost_ship_from_zip ) . "</origin-postal-code>
				<destination>";
				if( $destination_country == "CA" ){
				$xmlRequest .= "
					<domestic>
						<postal-code>" . $this->format_zip_code( $destination_zip ) . "</postal-code>
					</domestic>";
				}else if( $destination_country == "US" ){
				$xmlRequest .= "
					<united-states>
						<zip-code>" . $this->format_zip_code( $destination_zip ) . "</zip-code>
					</united-states>";
				}else{
				$xmlRequest .= "
					<international>
						<country-code>" . $destination_country . "</country-code>
					</international>";
				}
				$xmlRequest .= "
				</destination>
			</mailing-scenario>";
			
		$headers = array( 	'Content-Type: application/vnd.cpc.ship.rate-v3+xml', 
							'Accept: application/vnd.cpc.ship.rate-v3+xml', 
							'Authorization: Basic ' . base64_encode( $this->canadapost_username . ':' . $this->canadapost_password ) );
			
		$curl = curl_init( $this->canadapost_url ); // Create REST Request
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlRequest);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($curl); // Execute REST Request
		if( $response === false ){
			$db->insert_response( 0, 1, "CANADA POST CURL ERROR", curl_error( $curl ) );
		}
		curl_close($curl);
		
		$rates = $this->process_all_rates_response( $response );
		
        $db->insert_response( 0, 1, "CANADA POST RATES", print_r( $rates, true ) );
        
        return $rates;
		
	}
	
	public function get_rate_test( $ship_code, $destination_zip, $destination_country, $weight ){
		
		$response = $this->get_rate( $ship_code, $destination_zip, $destination_country, $weight, 10, 10, 10, true );
		
		$xml =  new SimpleXMLElement( $response );
		
		if( isset( $xml->message ) && isset( $xml->message->code ) ){
			
			$db = new ec_db( );
			$db->insert_response( 0, 1, "Canada Post Error", (string) $xml->message->description );
			
			return (string) $xml->message->description;
			
		}else if( isset( $xml->{'price-quote'} ) )
			return "SUCCESS";
		
		else{
			
			return "error";
			
		}
		
	}
	
	private function process_all_rates_response( $result ){
		
		$rates = array( );
		$xml =  new SimpleXMLElement( $result );
		
		if( isset( $xml->{'price-quote'} ) ){
			for( $i=0; $i<count( $xml->{'price-quote'} ); $i++ ){
				$rates[] = array( 'rate_code' => (string) $xml->{'price-quote'}[$i]->{'service-code'}, 'rate' => (string) $xml->{'price-quote'}[$i]->{'price-details'}->{'due'}, 'delivery_days' => (string) $xml->{'price-quote'}[$i]->{'service-standard'}->{'expected-transit-time'} );
			}
		}
		return $rates;
	}
	
	private function process_response( $result ){
		
		$xml =  new SimpleXMLElement( $result );
		
		if( isset( $xml->{'price-quote'} ) )
			return $xml->{'price-quote'}[$i]->{'price-details'}->{'due'};
		else{
			error_log( "error in canada post get rate, response: " . print_r( $result, true ) );
			return "ERROR";
		}
	}
	
	public function validate_address( $desination_address, $destination_city, $destination_state, $destination_zip, $destination_country ){
		
		if( $destination_country == "CA" ){
			return $this->is_valid_zip_code( $destination_zip );
		}else{
			return true;
		}
		
	}
	
	private function is_valid_zip_code( $zip ){
		if( preg_match( "/[A-Z]\d[A-Z]\d[A-Z]\d/", $this->format_zip_code( $zip ) ) ){
			return true;
		}else{
			return false;
		}
	}
	
	private function format_zip_code( $zip ){
		return preg_replace( "/[^a-zA-Z0-9]+/", "", strtoupper( $zip ) );
	}
}
	
?>