<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_3ds{
	
	protected $mysqli;													// ec_db structure
	
	protected $cart;													// ec_cart structure
	protected $user;													// ec_user structure
	protected $shipping;												// ec_shipping structure
	protected $tax;														// ec_tax structure
	protected $discount;												// ec_discount structure
	protected $credit_card;												// ec_credit_card structure
	protected $order_totals;											// ec_order_totals structure
	protected $order_id;												// INT
	
	protected $error_message;											// TEXT
	protected $is_success;												// BOOL
	
	//3d auth values
	public $post_url = "";												// Used for 3D Auth
	public $post_id_input_name = "";									// Used for 3D Auth
	public $post_id = "";												// Used for 3D Auth
	public $post_message_input_name = "";								// Used for 3D Auth
	public $post_message = "";											// Used for 3D Auth
	public $post_return_url_input_name = "";							// Used for 3D Auth
	
	public $cart_page;													// VARCHAR
	public $permalink_divider;											// CHAR
	
	function __construct( ){ 
		$this->mysqli = new ec_db();
		
		$cart_page_id = get_option('ec_option_cartpage');
		
		if( function_exists( 'icl_object_id' ) ){
			$cart_page_id = icl_object_id( $cart_page_id, 'page', true, ICL_LANGUAGE_CODE );
		}
		
		$this->cart_page = get_permalink( $cart_page_id );
		
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$this->cart_page = $https_class->makeUrlHttps( $this->cart_page );
		}
		
		if( substr_count( $this->cart_page, '?' ) )					$this->permalink_divider = "&";
		else														$this->permalink_divider = "?";
	}
	
	/****************************************
	* INITIALIZATION FUNCTIONS
	*****************************************/
	
	public function initialize( $cart, $user, $shipping, $tax, $discount, $credit_card, $order_totals, $order_id ){
		
		$this->cart = $cart;
		$this->user = $user;
		$this->shipping = $shipping;
		$this->tax = $tax;
		$this->discount = $discount;
		$this->credit_card = $credit_card;
		$this->order_totals = $order_totals;
		$this->order_id = $order_id;
		
		$this->is_success = false;
		
	}
	
	/****************************************
	* WORKER FUNCTIONS
	*****************************************/
	
	public function secure_3d_lookup( ){
		
		$gateway_url = $this->get_gateway_url( );
		$gateway_headers = $this->get_gateway_headers( );
		$lookup_data = $this->get_lookup_data( );
		$lookup_response = $this->get_gateway_response( $gateway_url, $lookup_data, $gateway_headers );
		$lookup_response_handled = $this->handle_lookup_response( $lookup_response );
		
		if( $lookup_response_handled == "ERROR" || $lookup_response_handled == "NO3DS" ){
			return $lookup_response_handled;
		}
		
	}
	
	public function secure_3d_auth( $order_id, $order, $transaction_reponse ){
		
		$this->order_id = $order_id;
		$gateway_url = $this->get_gateway_url( );
		$gateway_headers = $this->get_gateway_headers( );
		$authenticate_data = $this->get_authenticate_data( $transaction_reponse );
		$authenticate_response = $this->get_gateway_response( $gateway_url, $authenticate_data, $gateway_headers );
		
		if( !$authenticate_response ){
			error_log( "error in 3ds authenticate, could not get a response from the server." );
			$this->redirect_for_error( );
		}else{
			$response = $this->handle_authenticate_response( $authenticate_response );
			if( $response )
				$this->update_3ds_form( $response );
			else
				$this->redirect_for_error( );
		}
		
	}
	
	/****************************************
	* RETURNING FUNCTIONS
	*****************************************/
	public function get_response_message( ){
		return $this->error_message;
	}
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	protected function get_gateway_url( ){
		error_log( "get_gateway_url( ) must be override by a gateway-specific child." );
		return false;
	}
	
	protected function get_gateway_headers( ){
		// This is optional, needed for some gateways
		return "";	
	}
	
	protected function get_lookup_data( ){
		// 1. Setup gateway specific variables
		// 2. If it uses an xml format, build it, if an array, build that.
		// 3. return the xml/array data.
		error_log( "get_lookup_data( ) must be override by a gateway-specific child." );
		return false;
	}
	
	protected function handle_lookup_response( $response ){
		// 1. Break apart response
		// 2. Set is_success variable
		// 3. If ERROR, set the error message
		// 3. Store response to DB
		error_log( "handle_lookup_response( ) must be override by a gateway-specific child." );
		return false;
	}
	
	protected function process_send_to_verification( $payload, $to_url, $transaction_id ){
		// Creates and prints form to redirect user to verify payment info.
		error_log( "process_send_to_verification( ) must be override by a gateway-specific child." );
		return false;
	}
	
	protected function get_authenticate_data( $transaction_data ){
		// 1. Setup gateway specific variables
		// 2. If it uses an xml format, build it, if an array, build that.
		// 3. return the xml/array data.
		error_log( "get_authenticate_data( ) must be override by a gateway-specific child." );
		return false;
	}
	
	protected function handle_authenticate_response( $response ){
		// 1. Break apart response
		// 2. Set is_success variable
		// 3. If ERROR, set the error message
		// 3. Store response to DB
		error_log( "handle_authenticate_response( ) must be override by a gateway-specific child." );
		return false;
	}
	
	/********************************************************************************
	* CONSTANT HELPER FUNCTIONS ( SOME GATEWAYS MAY NEED TO CHANGE THIS FUNCTION )
	*********************************************************************************/
	
	protected function get_gateway_response( $gateway_url, $gateway_data, $gateway_headers ){
		
		$request = new WP_Http;
		$response = $request->request( $gateway_url, array( 'method' => 'POST', 'body' => $gateway_data, 'headers' => $gateway_headers ) );
		if( is_wp_error( $response ) ){
			$this->error_message = $response->get_error_message();
			return false;
		}else
			return $response;
			
	}
	
	protected function update_3ds_form( $response ){
		echo "<script>\r\n";
		echo "window.parent.document.getElementById( 'ec_cavv' ).value = '" . $response->cavv . "';\r\n";
		echo "window.parent.document.getElementById( 'ec_eci' ).value = '" . $response->eci . "';\r\n";
		echo "window.parent.document.getElementById( 'ec_xid' ).value = '" . $response->xid . "';\r\n";
		echo "window.parent.document.getElementById( 'ec_paresstatus' ).value = '" . $response->paresstatus . "';\r\n";
		echo "window.parent.document.getElementById( 'ec_verified' ).value = '" . $response->verified . "';\r\n";
		echo "window.parent.document.getElementById( '3dsfinalform' ).submit( );\r\n";
		echo "</script>";
		die( );
	}
	
	protected function redirect_for_error( ){
		echo "<script>\r\n";
		echo "window.parent.location='" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment&ec_cart_error=payment_failed';";
		echo "</script>";
		die( );
	}

}

?>