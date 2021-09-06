<?php

class ec_perpages{
	
	public $perpages;
	public $selected;
	
	/****************************************
	* CONSTRUCTOR
	*****************************************/
	function __construct( ){
		global $wpdb;
		$this->perpages = wp_cache_get( 'wpeasycart-perpage' );
		if( !$this->perpages ){
			$this->perpages = $wpdb->get_results( "SELECT * FROM ec_perpage ORDER BY perpage ASC" );
			wp_cache_set( 'wpeasycart-perpage', $this->perpages );
		}
		$this->selected = $this->get_current( );
	}
	
	public function get_current( ){
		if( isset( $_GET['perpage'] ) && $this->is_valid_selection( $_GET['perpage'] ) )							
			return $_GET['perpage'];
		
		else if( isset( $GLOBALS['ec_cart_data']->cart_data->perpage ) && $GLOBALS['ec_cart_data']->cart_data->perpage != "" && $this->is_valid_selection( $GLOBALS['ec_cart_data']->cart_data->perpage ) )				
			return $GLOBALS['ec_cart_data']->cart_data->perpage;
		
		else
			return $this->get_default( );
	
	}
	
	private function is_valid_selection( $selection ){
		
		for( $i=0; $i<count( $this->perpages ); $i++ ){
			
			if( $this->perpages[$i] == $selection )
				return true;
			
		}
		
		return false;
		
	}
	
	private function get_default(){
		
		$sel_item = ( ceil( count( $this->perpages ) / 2 )  );
		
		if( $sel_item > 0 )
			return $this->perpages[$sel_item-1]->perpage;
		
		else
			return $this->perpages[0]->perpage;
	
	}
		
		
}

?>