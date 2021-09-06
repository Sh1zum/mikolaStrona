<?php

class ec_page_options{
	
	public $page_options;
	
	/****************************************
	* CONSTRUCTOR
	*****************************************/
	function __construct( ){
		global $post;
		$db = new ec_db( );
		$this->page_options = $db->get_page_options( $post->ID );
	}
	
	public function get_page_option( $option_name ){
		
		for( $i=0; $i<count( $this->page_options ); $i++ ){
			
			if( $this->page_options[$i]->option_type == $option_name )
				return $this->page_options[$i]->option_value;
			
		}
		
	}
		
		
}

?>