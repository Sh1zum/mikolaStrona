<?php

class ec_rating{
	
	public $review_count;										// INT
	public $product_rating;										// Decimal
	
	function __construct( $rating_data ){
		$this->review_count = 0;
		$this->product_rating = 0.0;
		
		if(count($rating_data) == 1 && $rating_data[0]){
			$this->review_count = count($rating_data);
			$total = 0;
			
			for($i=0; $i<count($rating_data); $i++){
				if(isset($rating_data[$i]['rating'])) {
					$total = $total + $rating_data[$i]['rating'];
				}
			}
				
			$this->product_rating = ($total/($this->review_count * 5)) * 5;
		}
	
	}
	
	public function display_stars( $average = 0 ){
		
																$ret_string = "";
		for( $i = 0; $i < $average; $i++)						$ret_string .= $this->display_star_on();
		for ($i = $average; $i < 5; $i++)						$ret_string .= $this->display_star_off();
																return $ret_string;
			
	}
	
	public function display_number_reviews(){
		
		return $this->review_count;
			
	}
	
	private function display_star_on( ){
	
		return "<div class=\"ec_product_star_on\"></div>";
		//return "<img src=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option('ec_option_base_layout') . "/ec_product/star-on.png" ) . "\" class=\"ec_product_star_on\" />";
		
	}
	
	private function display_star_off( ){
	
		return "<div class=\"ec_product_star_off\"></div>";
		//return "<img src=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option('ec_option_base_layout') . "/ec_product/star-off.png" ) . "\" class=\"ec_product_star_off\" />";
		
	}
	
	
	
}

?>