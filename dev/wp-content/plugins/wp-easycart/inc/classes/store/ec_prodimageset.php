<?php

class ec_prodimageset{
	
	public $product_id;						// INT
	public $optionitem_id;					// INT
	public $image1;							// VARCHAR 255
	public $image2;							// VARCHAR 255
	public $image3;							// VARCHAR 255
	public $image4;							// VARCHAR 255
	public $image5;							// VARCHAR 255
	
	function __construct($product_id, $image_data){
		
		$this->product_id = $product_id;
		
		$this->optionitem_id = $image_data->optionitem_id;
		$this->image1 = $image_data->image1;
		$this->image2 = $image_data->image2;
		$this->image3 = $image_data->image3;
		$this->image4 = $image_data->image4;
		$this->image5 = $image_data->image5;
		
	}
	
}

?>