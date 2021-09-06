<?php

class ec_prodoptions{
	private $mysqli;											// ec_db structure
	
	public $product_id;											// INT
	public $optionset1;											// ec_optionset
	public $optionset2;											// ec_optionset
	public $optionset3;											// ec_optionset
	public $optionset4;											// ec_optionset
	public $optionset5;											// ec_optionset
	
	public $quantity_array = array();							// up to a 5d array of quantities
	
	function __construct( $product_id, $option_id_1, $option_id_2, $option_id_3, $option_id_4, $option_id_5, $use_quantity_tracking ){
		
		$this->mysqli = new ec_db();
		
		$this->product_id = $product_id;
		$this->optionset1 = new ec_optionset( $option_id_1 );
		$this->optionset2 = new ec_optionset( $option_id_2 );
		$this->optionset3 = new ec_optionset( $option_id_3 );
		$this->optionset4 = new ec_optionset( $option_id_4 );
		$this->optionset5 = new ec_optionset( $option_id_5 );
		
		if( $use_quantity_tracking )
			$this->quantity_array = $this->mysqli->get_quantity_data( $this->product_id, $this->optionset1, $this->optionset2, $this->optionset3, $this->optionset4, $this->optionset5 );
		else
			$this->quantity_array = array();
	
	}
	
	public function get_quantity_string( $level, $num ){
		
		if($level == 1){
																return $this->quantity_array[$num][1];
		}else if($level == 2){
																$ret_string = "";
			for($a=0; $a<count($this->quantity_array); $a++){
				if($a>0)										$ret_string .= ",";
																$ret_string .= $this->quantity_array[$a][0][$num][1];
			}
																return $ret_string;
		}else if($level == 3){
																$ret_string = "";
			for($a=0; $a<count($this->quantity_array); $a++){
				for($b=0; $b<count($this->quantity_array[$a][0]); $b++){
					if($a>0 || $b>0)							$ret_string .= ",";
																$ret_string .= $this->quantity_array[$a][0][$b][0][$num][1];
				}
			}
																return $ret_string;
		}else if($level == 4){
																$ret_string = "";
			for($a=0; $a<count($this->quantity_array); $a++){
				for($b=0; $b<count($this->quantity_array[$a][0]); $b++){
					for($c=0; $c<count($this->quantity_array[$a][0][$b][0]); $c++){
						if($a>0 || $b>0 || $c>0)				$ret_string .= ",";
																$ret_string .= $this->quantity_array[$a][0][$b][0][$c][0][$num][1];
					}
				}
			}
																return $ret_string;
		}else if($level == 5){
																$ret_string = "";
			for($a=0; $a<count($this->quantity_array); $a++){
				for($b=0; $b<count($this->quantity_array[$a][0]); $b++){
					for($c=0; $c<count($this->quantity_array[$a][0][$b][0]); $c++){
						for($d=0; $d<count($this->quantity_array[$a][0][$b][0][$c][0]); $d++){
							if($a>0 || $b>0 || $c>0 || $d>0)	$ret_string .= ",";
																$ret_string .= $this->quantity_array[$a][0][$b][0][$c][0][$d][0][$num][1];
						}
					}
				}
			}
																return $ret_string;
		}
	}
	
}

?>