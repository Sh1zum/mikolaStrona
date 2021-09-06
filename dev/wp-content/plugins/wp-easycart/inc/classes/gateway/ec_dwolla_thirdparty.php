<?php
class ec_dwolla_thirdparty extends ec_third_party{
	
	public function display_form_start( ){
		$dwolla_account_id = get_option( 'ec_option_dwolla_thirdparty_account_id' );
		$dwolla_key = get_option( 'ec_option_dwolla_thirdparty_key' );
		$dwolla_sec = get_option( 'ec_option_dwolla_thirdparty_secret' );
		$test_mode = get_option( 'ec_option_dwolla_thirdparty_test_mode' );
		$timestamp 	= time(); // example: 1390408833
		$signature 	= hash_hmac('sha1', "{$dwolla_key}&{$timestamp}&{$this->order_id}", $dwolla_sec );
		
		$tax = new ec_tax( 0.00, 0.00, 0.00, $this->order->billing_state, $this->order->billing_country );
		$tax_total = number_format( $this->order->tax_total + $this->order->duty_total + $this->order->gst_total + $this->order->pst_total + $this->order->hst_total, 2 );
		if( !$tax->vat_included )
			$tax_total = number_format( $tax_total + $this->order->vat_total, 2 );
		
		if( $test_mode )
			$test_mode = "true";
		else
			$test_mode= "false";
		
		echo "<form action=\"" . $this->get_gateway_url( $test_mode ) . "\" method=\"post\">";
		echo "<input id=\"key\" name=\"key\" type=\"hidden\" value=\"" . $dwolla_key . "\" />";
		echo "<input id=\"signature\" name=\"signature\" type=\"hidden\" value=\"" . $signature . "\" />";
		//echo "<input id=\"callback\" name=\"callback\" type=\"hidden\" value=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/inc/scripts/dwolla_callback.php" ) . "\" />";
		echo "<input id=\"redirect\" name=\"redirect\" type=\"hidden\" value=\"" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order_id . "\" />";
		echo "<input id=\"test\" name=\"test\" type=\"hidden\" value=\"" . $test_mode . "\" />";
		echo "<input id=\"name\" name=\"name\" type=\"hidden\" value=\"Order #" . $this->order_id . "\" />";
		echo "<input id=\"description\" name=\"description\" type=\"hidden\" value=\"Description\" />";
		echo "<input id=\"destinationid\" name=\"destinationid\" type=\"hidden\" value=\"" . $dwolla_account_id . "\" />";
		echo "<input id=\"amount\" name=\"amount\" type=\"hidden\" value=\"" . number_format($this->order->sub_total, 2) . "\" />";
		echo "<input id=\"shipping\" name=\"shipping\" type=\"hidden\" value=\"" . number_format( $this->order->shipping_total, 2 ) . "\" />";
		echo "<input id=\"tax\" name=\"tax\" type=\"hidden\" value=\"" . $tax_total . "\" />";
		echo "<input id=\"orderid\" name=\"orderid\" type=\"hidden\" value=\"" . $this->order_id . "\" />";
		echo "<input id=\"timestamp\" name=\"timestamp\" type=\"hidden\" value=\"" . $timestamp . "\" />";
		echo "</form>";
	}
	
	public function display_auto_forwarding_form( ){
		
		$dwolla_account_id = get_option( 'ec_option_dwolla_thirdparty_account_id' );
		$dwolla_key = get_option( 'ec_option_dwolla_thirdparty_key' );
		$dwolla_sec = get_option( 'ec_option_dwolla_thirdparty_secret' );
		$test_mode = get_option( 'ec_option_dwolla_thirdparty_test_mode' );
		$timestamp 	= time(); // example: 1390408833
		$signature 	= hash_hmac('sha1', "{$dwolla_key}&{$timestamp}&{$this->order_id}", $dwolla_sec );
		
		$tax = new ec_tax( 0.00, 0.00, 0.00, $this->order->billing_state, $this->order->billing_country );
		$tax_total = number_format( $this->order->tax_total + $this->order->duty_total + $this->order->gst_total + $this->order->pst_total + $this->order->hst_total, 2 );
		if( !$tax->vat_included )
			$tax_total = number_format( $tax_total + $this->order->vat_total, 2 );
			
		if( $test_mode )
			$test_mode = "true";
		else
			$test_mode= "false";
		
		echo "<style>
		.ec_third_party_loader{ display:block !important; position:absolute; top:50%; left:50%; }
		@-webkit-keyframes ec_third_party_loader {
		  0% {
			-webkit-transform: rotate(0deg);
			-moz-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		  }
		
		  100% {
			-webkit-transform: rotate(360deg);
			-moz-transform: rotate(360deg);
			-ms-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		  }
		}
		
		@-moz-keyframes ec_third_party_loader {
		  0% {
			-webkit-transform: rotate(0deg);
			-moz-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		  }
		
		  100% {
			-webkit-transform: rotate(360deg);
			-moz-transform: rotate(360deg);
			-ms-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		  }
		}
		
		@-o-keyframes ec_third_party_loader {
		  0% {
			-webkit-transform: rotate(0deg);
			-moz-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		  }
		
		  100% {
			-webkit-transform: rotate(360deg);
			-moz-transform: rotate(360deg);
			-ms-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		  }
		}
		
		@keyframes ec_third_party_loader {
		  0% {
			-webkit-transform: rotate(0deg);
			-moz-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		  }
		
		  100% {
			-webkit-transform: rotate(360deg);
			-moz-transform: rotate(360deg);
			-ms-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		  }
		}
		
		/* Styles for old versions of IE */
		.ec_third_party_loader {
		  font-family: sans-serif;
		  font-weight: 100;
		}
		
		/* :not(:required) hides this rule from IE9 and below */
		.ec_third_party_loader:not(:required) {
		  -webkit-animation: ec_third_party_loader 1250ms infinite linear;
		  -moz-animation: ec_third_party_loader 1250ms infinite linear;
		  -ms-animation: ec_third_party_loader 1250ms infinite linear;
		  -o-animation: ec_third_party_loader 1250ms infinite linear;
		  animation: ec_third_party_loader 1250ms infinite linear;
		  border: 8px solid #3388ee;
		  border-right-color: transparent;
		  border-radius: 16px;
		  box-sizing: border-box;
		  display: inline-block;
		  position: relative;
		  overflow: hidden;
		  text-indent: -9999px;
		  width: 32px;
		  height: 32px;
		}
		</style>";
		
		echo "<div style=\"display:none;\" class=\"ec_third_party_loader\">Loading...</div>";
		
		echo "<form action=\"" . $this->get_gateway_url( $test_mode ) . "\" method=\"post\" name=\"dwolla_thirdparty_form\">";
		echo "<input id=\"key\" name=\"key\" type=\"hidden\" value=\"" . $dwolla_key . "\" />";
		echo "<input id=\"signature\" name=\"signature\" type=\"hidden\" value=\"" . $signature . "\" />";
		//echo "<input id=\"callback\" name=\"callback\" type=\"hidden\" value=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/inc/scripts/dwolla_callback.php" ) . "\" />";
		echo "<input id=\"redirect\" name=\"redirect\" type=\"hidden\" value=\"" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order_id . "\" />";
		echo "<input id=\"test\" name=\"test\" type=\"hidden\" value=\"" . $test_mode . "\" />";
		echo "<input id=\"name\" name=\"name\" type=\"hidden\" value=\"Order #" . $this->order_id . "\" />";
		echo "<input id=\"description\" name=\"description\" type=\"hidden\" value=\"Description\" />";
		echo "<input id=\"destinationid\" name=\"destinationid\" type=\"hidden\" value=\"" . $dwolla_account_id . "\" />";
		echo "<input id=\"amount\" name=\"amount\" type=\"hidden\" value=\"" . number_format($this->order->sub_total, 2) . "\" />";
		echo "<input id=\"shipping\" name=\"shipping\" type=\"hidden\" value=\"" . number_format( $this->order->shipping_total, 2 ) . "\" />";
		echo "<input id=\"tax\" name=\"tax\" type=\"hidden\" value=\"" . $tax_total . "\" />";
		echo "<input id=\"orderid\" name=\"orderid\" type=\"hidden\" value=\"" . $this->order_id . "\" />";
		echo "<input id=\"timestamp\" name=\"timestamp\" type=\"hidden\" value=\"" . $timestamp . "\" />";
		echo "</form>";
		echo "<SCRIPT LANGUAGE=\"Javascript\">document.dwolla_thirdparty_form.submit();</SCRIPT>";
	}
	
	public function get_gateway_url( $test_mode ){
		
		if( $test_mode )
			return "https://uat.dwolla.com/payment/pay";
		else
			return "https://www.dwolla.com/payment/pay";
			
	}
	
}
?>