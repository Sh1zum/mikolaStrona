<?php

class ec_countries{
	
	public $countries;
	
	/****************************************
	* CONSTRUCTOR
	*****************************************/
	function __construct( ){
		global $wpdb;
		$this->countries = wp_cache_get( 'wpeasycart-countries' );
		if( !$this->countries ){
			$this->countries = $wpdb->get_results( "SELECT * FROM ec_country WHERE ship_to_active = 1 ORDER BY ec_country.sort_order ASC" );
			wp_cache_set( 'wpeasycart-countries', $this->countries );
		}
	}
	
	public function get_country_name( $iso2_cnt ){
		
		for( $i=0; $i<count( $this->countries ); $i++ ){
			
			if( $this->countries[$i]->iso2_cnt == $iso2_cnt )
				return $this->countries[$i]->name_cnt;
			
		}
		
	}
		
		
}

?>