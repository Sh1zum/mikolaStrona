<?php

class ec_googleanalytics {
	
	private $cart;														// ec_cart structure
	private $shipping;													// ec_shipping structure
	private $tax;														// ec_tax structure
	private $order_totals;												// ec_order_totals structure
	private $order_id;													// successful order id
	
	
	function __construct( $cart, $shipping, $tax, $order_totals, $order_id ){
		
		$this->cart = $cart;
		$this->shipping = $shipping;	
		$this->tax = $tax;
		$this->order_total = $order_totals;
		$this->order_id = $order_id;
	}
	

	
	public function get_transaction_js(){
		  
	 	return "ga('ecommerce:addTransaction', {id: '".$this->order_id."', affiliation: '', revenue: '".$this->order_total."', shipping: '".$this->shipping."', tax: '".$this->tax."'});\n";
			
	}
	

	public function get_item_js() {
		
		$returning_items = null;
		for( $i = 0; $i < count( $this->cart); $i++ ){
			  $returning_items .= "ga('ecommerce:addItem', {id: '".$this->cart[$i]->order_id."', name: '".str_replace( "'", "\'", $this->cart[$i]->title )."', sku: '".$this->cart[$i]->model_number."', price: '".$this->cart[$i]->unit_price."', quantity: '".$this->cart[$i]->quantity."'});\n";
		}
		return $returning_items;
		
	}

	
}

?>