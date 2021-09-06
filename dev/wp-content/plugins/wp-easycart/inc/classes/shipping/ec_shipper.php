<?php
	class ec_shipper{
		protected $ec_setting;										// ec_settings structure
		
		private $shipping_data;
		
		function __construct(  ){
			
			$this->shipping_data = $GLOBALS['wpeasycart_live_shipping']->get_rates( );
			
		}
		
		public function get_service_days( $ship_company, $ship_code ){
			
			if( $ship_company == "ups" && isset( $this->shipping_data->ups ) ){
				
				for( $i=0; $i<count( $this->shipping_data->ups ); $i++ ){
					if( isset( $this->shipping_data->ups[$i]->rate_code ) && $this->shipping_data->ups[$i]->rate_code == $ship_code ){
						return $this->shipping_data->ups[$i]->delivery_days;
					}
				}
			
			}else if( $ship_company == "canadapost" && isset( $this->shipping_data->canadapost ) ){
				
				for( $i=0; $i<count( $this->shipping_data->canadapost ); $i++ ){
					if( isset( $this->shipping_data->canadapost[$i]->rate_code ) && $this->shipping_data->canadapost[$i]->rate_code == $ship_code ){
						return $this->shipping_data->canadapost[$i]->delivery_days;
					}
				}
				
			}
				
			return 0;
		
		}
		
		public function get_rate( $ship_company, $ship_code ){
			
			if( $ship_company == "ups" && isset( $this->shipping_data->ups ) ){
				
				for( $i=0; $i<count( $this->shipping_data->ups ); $i++ ){
					if( isset( $this->shipping_data->ups[$i]->rate_code ) && $this->shipping_data->ups[$i]->rate_code == $ship_code ){
						return $this->shipping_data->ups[$i]->rate;
					}
				}
				
				return "ERROR";
			
			}else if( $ship_company == "usps" && isset( $this->shipping_data->usps ) ){
				
				if( isset( $this->shipping_data->usps->{$ship_code} ) && isset( $this->shipping_data->usps->{$ship_code}->rate ) ){
					if( isset( $this->shipping_data->usps->{$ship_code}->rate->{0} ) )
						return $this->shipping_data->usps->{$ship_code}->rate->{0};
					else
						return $this->shipping_data->usps->{$ship_code}->rate;
				}
				
				return "ERROR";
			
			}else if( $ship_company == "fedex" && isset( $this->shipping_data->fedex ) ){
				
				for( $i=0; $i<count( $this->shipping_data->fedex ); $i++ ){
					if( isset( $this->shipping_data->fedex[$i]->rate_code ) && $ship_code == "GROUND_HOME_DELIVERY" && ( $this->shipping_data->fedex[$i]->rate_code == "FEDEX_GROUND" || $this->shipping_data->fedex[$i]->rate_code == "GROUND_HOME_DELIVERY" ) ){
						return $this->shipping_data->fedex[$i]->rate;
						
					}else if( isset( $this->shipping_data->fedex[$i]->rate_code ) && $this->shipping_data->fedex[$i]->rate_code == $ship_code ){
						return $this->shipping_data->fedex[$i]->rate;
					}
				}
				
				return "ERROR";
			
			}else if( $ship_company == "auspost" && isset( $this->shipping_data->auspost ) ){
				
				for( $i=0; $i<count( $this->shipping_data->auspost ); $i++ ){
					if( isset( $this->shipping_data->auspost[$i]->rate_code ) && $this->shipping_data->auspost[$i]->rate_code == $ship_code ){
						return $this->shipping_data->auspost[$i]->rate;
					}else if( $ship_code == "INTL_SERVICE_AIR_MAIL" &&
					 		  isset( $this->shipping_data->auspost[$i]->rate_code ) && 
							  $this->shipping_data->auspost[$i]->rate_code == "INT_PARCEL_AIR_OWN_PACKAGING" ){
						return $this->shipping_data->auspost[$i]->rate;
					}else if( $ship_code == "INTL_SERVICE_SEA_MAIL" &&
					 		  isset( $this->shipping_data->auspost[$i]->rate_code ) && 
							  $this->shipping_data->auspost[$i]->rate_code == "INT_PARCEL_SEA_OWN_PACKAGING" ){
						return $this->shipping_data->auspost[$i]->rate;
					}
				}
				
				return "ERROR";
			
			}else if( $ship_company == "dhl" && isset( $this->shipping_data->dhl ) ){
				
				for( $i=0; $i<count( $this->shipping_data->dhl ); $i++ ){
					if( isset( $this->shipping_data->dhl[$i]->rate_code ) && $this->shipping_data->dhl[$i]->rate_code == $ship_code ){
						return $this->shipping_data->dhl[$i]->rate;
					}
				}
				
				return "ERROR";
			
			}else if( $ship_company == "canadapost" && isset( $this->shipping_data->canadapost ) ){
				
				for( $i=0; $i<count( $this->shipping_data->canadapost ); $i++ ){
					if( isset( $this->shipping_data->canadapost[$i]->rate_code ) && $this->shipping_data->canadapost[$i]->rate_code == $ship_code ){
						return $this->shipping_data->canadapost[$i]->rate;
					}
				}
				
				return "ERROR";
			
			}else if( isset( $this->shipping_data->{$ship_company} ) ){
				for( $i=0; $i<count( $this->shipping_data->{$ship_company} ); $i++ ){
					if( isset( $this->shipping_data->{$ship_company}[$i]->rate_code ) && $this->shipping_data->{$ship_company}[$i]->rate_code == $ship_code ){
						return $this->shipping_data->{$ship_company}[$i]->rate;
					}
				}
			}else{
				return "ERROR";
				
			}
			
		}
		
		public function validate_address( $destination_address, $destination_city, $destination_state, $destination_zip, $destination_country ){
			
			if( isset( $this->ups ) )
				return $this->ups->validate_address( $destination_city, $destination_state, $destination_zip, $destination_country );
			
			else if( isset( $this->usps ) )
				return $this->usps->validate_address( $destination_address, $destination_city, $destination_state, $destination_zip, $destination_country );
			
			else if( isset( $this->fedex ) )
				return $this->fedex->validate_address( $destination_address, $destination_city, $destination_state, $destination_zip, $destination_country );
			
			else if( isset( $this->auspost ) )
				return $this->auspost->validate_address( $destination_address, $destination_city, $destination_state, $destination_zip, $destination_country );
			
			else if( isset( $this->dhl ) )
				return $this->dhl->validate_address( $destination_address, $destination_city, $destination_state, $destination_zip, $destination_country );
				
			else if( isset( $this->canadapost ) )
				return $this->canadapost->validate_address( $destination_address, $destination_city, $destination_state, $destination_zip, $destination_country );
				
			else
				return true;
			
		}
		
	}
?>