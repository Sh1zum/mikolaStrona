<?php
if( !class_exists( 'ec_live_shipping' ) ) :

final class ec_live_shipping{
	
	protected static $_instance = null;
	protected $wpdb;
	
	public $shipping_rates;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
	
	public function __construct( ){
		
		global $wpdb;
		$this->wpdb =& $wpdb; 
		$this->shipping_rates = array( );
		
		add_action( 'wpeasycart_cart_updated', array( $this, 'update_shipping_rates' ), 10 );
		add_action( 'wpeasycart_cart_subscription_updated', array( $this, 'update_subscription_shipping_rates' ), 10, 2 );
		
	}
	
	public function get_rates( ){
		
		if( !$this->shipping_rates || count( $this->shipping_rates ) <= 0 ){
			$shipping_rates_data = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT ec_live_rate_cache.rate_data FROM ec_live_rate_cache WHERE ec_live_rate_cache.ec_cart_id = %s", $GLOBALS['ec_cart_data']->ec_cart_id ) );
			if( $shipping_rates_data )
				$this->shipping_rates = json_decode( $shipping_rates_data );
		}
		
		return $this->shipping_rates;
		
	}
	
	public function update_shipping_rates( ){
		
		$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$this->shipping_rates = array( );
		
		// Create Promotion and apply free shipping if necessary.
		$promotion = new ec_promotion( );
		$promotion->apply_free_shipping( $cart );
		
		$destination_zip = $GLOBALS['ec_cart_data']->cart_data->shipping_zip;
		$destination_country = $GLOBALS['ec_cart_data']->cart_data->shipping_country;
		if( $destination_zip == "" || $destination_country == "" ){
			$user = new ec_user( "" );
			if( $user->user_id != 0 ){
				$destination_zip = $user->shipping->zip;
				$destination_country = $user->shipping->country;
			}
		}
		$weight = $cart->weight;
		$length = $cart->length;
		$width = $cart->width;
		$height = $cart->height;
		$declared_value = $cart->shipping_subtotal;
		
		$use_auspost = false;
		$use_canadapost = false;
		$use_dhl = false;
		$use_fedex = false;
		$use_ups = false;
		$use_usps = false;
		
		$rates = $this->wpdb->get_results( "SELECT shippingrate_id, is_ups_based, is_usps_based, is_fedex_based, is_auspost_based, is_dhl_based, is_canadapost_based FROM ec_shippingrate" );
			
		foreach( $rates as $rate ){
			if( $rate->is_auspost_based )
				$use_auspost = true;
			else if( $rate->is_canadapost_based )
				$use_canadapost = true;
			else if( $rate->is_dhl_based )
				$use_dhl = true;
			else if( $rate->is_fedex_based )
				$use_fedex = true;
			else if( $rate->is_ups_based )
				$use_ups = true;
			else if( $rate->is_usps_based )
				$use_usps = true;
		}
		
		if( $use_auspost ){
			$auspost = new ec_auspost( $GLOBALS['ec_setting'] );
			$auspost_data = $auspost->get_all_rates( $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, $cart->cart );
			$this->shipping_rates["auspost"] = $auspost_data;
		}
		
		if( $use_canadapost ){
			$canadapost = new ec_canadapost( $GLOBALS['ec_setting'] );
			$canadapost_data = $canadapost->get_all_rates( $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, $cart->cart );
			$this->shipping_rates["canadapost"] = $canadapost_data;
		}
		
		if( $use_dhl ){
			$dhl = new ec_dhl( $GLOBALS['ec_setting'] );
			$dhl_data = $dhl->get_all_rates( $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, $cart->cart );
			$this->shipping_rates["dhl"] = $dhl_data;
		}
		
		if( $use_fedex ){
			$fedex = new ec_fedex( $GLOBALS['ec_setting'] );
			$fedex_data = $fedex->get_all_rates( $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, $cart->cart );
			$this->shipping_rates["fedex"] = $fedex_data;
		}
		
		if( $use_ups ){
			$ups = new ec_ups( $GLOBALS['ec_setting'] );
			$ups_data = $ups->get_all_rates( $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, $cart->cart );
			$this->shipping_rates["ups"] = $ups_data;
		}
		
		if( $use_usps ){
			$usps = new ec_usps( $GLOBALS['ec_setting'] );
			$usps_data = $usps->get_all_rates( $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, $cart->cart );
			$this->shipping_rates["usps"] = $usps_data;
		}
        
        $promotion = new ec_promotion( );
        foreach( $this->shipping_rates as $key => $rates ){
            for( $i=0; $i<count( $rates ); $i++ ){
                if( isset( $rates[$i]['rate'] ) ){
                    $rates[$i]['rate'] = $rates[$i]['rate'] - $promotion->get_shipping_discounts( $cart->subtotal, $rates[$i]['rate'], $promo_message );
                }
            }
            $this->shipping_rates[$key] = $rates;
        }
		
		$this->shipping_rates = apply_filters( 'wpeasycart_live_shipping_rates', $this->shipping_rates, $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, $cart->cart );
		
		// Fix in cases when rates are updated via AJAX
		$rates_encoded = json_encode( $this->shipping_rates );
		$this->shipping_rates = json_decode( $rates_encoded );
		
		$this->wpdb->query( $this->wpdb->prepare( "DELETE FROM ec_live_rate_cache WHERE ec_live_rate_cache.ec_cart_id = %s", $GLOBALS['ec_cart_data']->ec_cart_id ) );
		$this->wpdb->query( $this->wpdb->prepare( "INSERT INTO ec_live_rate_cache( ec_cart_id, rate_data ) VALUES( %s, %s )", $GLOBALS['ec_cart_data']->ec_cart_id, json_encode( $this->shipping_rates ) ) );
		
	}
	
	public function update_subscription_shipping_rates( $product, $quantity ){
		
		$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$this->shipping_rates = array( );
		
		$destination_zip = $GLOBALS['ec_cart_data']->cart_data->shipping_zip;
		$destination_country = $GLOBALS['ec_cart_data']->cart_data->shipping_country;
		$weight = $product->weight * $quantity;
		$length = $product->length;
		$width = $product->width;
		$height = $product->height * $quantity;
		$declared_value = $product->price * $quantity;
		
		$use_auspost = false;
		$use_canadapost = false;
		$use_dhl = false;
		$use_fedex = false;
		$use_ups = false;
		$use_usps = false;
		
		$rates = $this->wpdb->get_results( "SELECT shippingrate_id, is_ups_based, is_usps_based, is_fedex_based, is_auspost_based, is_dhl_based, is_canadapost_based FROM ec_shippingrate" );
			
		foreach( $rates as $rate ){
			if( $rate->is_auspost_based )
				$use_auspost = true;
			else if( $rate->is_canadapost_based )
				$use_canadapost = true;
			else if( $rate->is_dhl_based )
				$use_dhl = true;
			else if( $rate->is_fedex_based )
				$use_fedex = true;
			else if( $rate->is_ups_based )
				$use_ups = true;
			else if( $rate->is_usps_based )
				$use_usps = true;
		}
		
		if( $use_auspost ){
			$auspost = new ec_auspost( $GLOBALS['ec_setting'] );
			$auspost_data = $auspost->get_all_rates( $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, array( $product ) );
			$this->shipping_rates["auspost"] = $auspost_data;
		}
		
		if( $use_canadapost ){
			$canadapost = new ec_canadapost( $GLOBALS['ec_setting'] );
			$canadapost_data = $canadapost->get_all_rates( $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, array( $product ) );
			$this->shipping_rates["canadapost"] = $canadapost_data;
		}
		
		if( $use_dhl ){
			$dhl = new ec_dhl( $GLOBALS['ec_setting'] );
			$dhl_data = $dhl->get_all_rates( $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, array( $product ) );
			$this->shipping_rates["dhl"] = $dhl_data;
		}
		
		if( $use_fedex ){
			$fedex = new ec_fedex( $GLOBALS['ec_setting'] );
			$fedex_data = $fedex->get_all_rates( $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, array( $product ) );
			$this->shipping_rates["fedex"] = $fedex_data;
		}
		
		if( $use_ups ){
			$ups = new ec_ups( $GLOBALS['ec_setting'] );
			$ups_data = $ups->get_all_rates( $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, array( $product ) );
			$this->shipping_rates["ups"] = $ups_data;
		}
		
		if( $use_usps ){
			$usps = new ec_usps( $GLOBALS['ec_setting'] );
			$usps_data = $usps->get_all_rates( $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, array( $product ) );
			$this->shipping_rates["usps"] = $usps_data;
		}
		
		$this->shipping_rates = apply_filters( 'wpeasycart_live_shipping_rates', $this->shipping_rates, $destination_zip, $destination_country, $weight, $length, $width, $height, $declared_value, array( $product ) );
		
		// Fix in cases when rates are updated via AJAX
		$rates_encoded = json_encode( $this->shipping_rates );
		$this->shipping_rates = json_decode( $rates_encoded );
		
		$this->wpdb->query( $this->wpdb->prepare( "DELETE FROM ec_live_rate_cache WHERE ec_live_rate_cache.ec_cart_id = %s", $GLOBALS['ec_cart_data']->ec_cart_id ) );
		$this->wpdb->query( $this->wpdb->prepare( "INSERT INTO ec_live_rate_cache( ec_cart_id, rate_data ) VALUES( %s, %s )", $GLOBALS['ec_cart_data']->ec_cart_id, json_encode( $this->shipping_rates ) ) );
		
	}
	
}
endif; // End if class_exists check


function wpeasycart_live_shipping( ){

	return ec_live_shipping::instance( );

}

$GLOBALS['wpeasycart_live_shipping'] = wpeasycart_live_shipping( );

?>