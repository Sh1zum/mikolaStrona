<?php
class ec_payfast_thirdparty extends ec_third_party{
	
	public function display_form_start( ){
		
		$merchant_id = get_option( 'ec_option_payfast_merchant_id' );
		$merchant_key = get_option( 'ec_option_payfast_merchant_key' );
		$passphrase = get_option( 'ec_option_payfast_passphrase' );
		
		if( get_option( 'ec_option_payfast_sandbox' ) )
			$url = "https://sandbox.payfast.co.za/eng/process";
		else
			$url = "https://www.payfast.co.za/eng/process";
		
		// Merchant Information
		echo "<form action=\"" . $url . "\" method=\"POST\">";
		echo "<input type=\"hidden\" name=\"merchant_id\" value=\"" . $merchant_id . "\" />";
		$signature_str = "merchant_id=" . urlencode( trim( $merchant_id ) );
		echo "<input type=\"hidden\" name=\"merchant_key\" value=\"" . $merchant_key . "\" />";
		$signature_str .= "&merchant_key=" . urlencode( trim( $merchant_key ) );
		echo "<input type=\"hidden\" name=\"return_url\" value=\"". htmlspecialchars( $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order_id ) . "\" />";
		$signature_str .= "&return_url=" . urlencode( trim(  $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order_id  ) );
		echo "<input type=\"hidden\" name=\"cancel_url\" value=\"". htmlspecialchars( $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment" ) . "\" />";
		$signature_str .= "&cancel_url=" . urlencode( trim(  $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment"  ) );
		echo "<input type=\"hidden\" name=\"notify_url\" value=\"". htmlspecialchars( plugins_url( EC_PLUGIN_DIRECTORY . "/inc/scripts/payfast_itn.php" ) ) . "\" />";
		$signature_str .= "&notify_url=" . urlencode( trim(  plugins_url( EC_PLUGIN_DIRECTORY . "/inc/scripts/payfast_itn.php" )  ) );
		
		//Customer Information
		echo "<input type=\"hidden\" name=\"name_first\" value=\"" . $this->order->billing_first_name . "\" />";
		$signature_str .= "&name_first=" . urlencode( trim( $this->order->billing_first_name ) );
		echo "<input type=\"hidden\" name=\"name_last\" value=\"" . $this->order->billing_last_name . "\" />";
		$signature_str .= "&name_last=" . urlencode( trim( $this->order->billing_last_name ) );
		echo "<input type=\"hidden\" name=\"email_address\" value=\"" . $this->order->user_email . "\" />";
		$signature_str .= "&email_address=" . urlencode( trim( $this->order->user_email ) );
		
		// Payment Information
		echo "<input type=\"hidden\" name=\"m_payment_id\" value=\"" . $this->order_id . "\" />";
		$signature_str .= "&m_payment_id=" . urlencode( trim( $this->order_id ) );
		echo "<input type=\"hidden\" name=\"amount\" value=\"" . number_format( $this->order->grand_total, 2, '.', '' ) . "\" />";
		$signature_str .= "&amount=" . urlencode( trim( number_format( $this->order->grand_total, 2, '.', '' ) ) );
		
		$item_name = "";
		for( $i=1; $i<=count( $this->order_details ) && $i<=5; $i++ ){
			if( $i > 1 )
				$item_name .= ', ';
			$item_name .= $this->order_details[$i-1]->title;
		}
		$item_name = substr( $item_name, 0, 100 );
		echo "<input type=\"hidden\" name=\"item_name\" value=\"" . $item_name . "\" />";
		$signature_str .= "&item_name=" . urlencode( trim( $item_name ) );
		
		// Transaction Options
		//echo "<input type=\"hidden\" name=\"email_confirmation\" value=\"0\" />";
		//$signature_str .= "&email_confirmation=0";
		
		if( $passphrase != '' )
			$signature_str .= '&passphrase='. urlencode( trim( $passphrase ) );
		
		// Security Options
		echo "<input type=\"hidden\" name=\"signature\" value=\"" . md5( $signature_str ) . "\" />";
		
		echo "</form>";
	}
	
	public function display_auto_forwarding_form( ){
		
		echo "<style>
		.ec_third_party_submit_button{ width:100%; text-align:center; }
		.ec_third_party_submit_button > input{ margin-top:150px; width:300px; height:45px; background-color:#38E; color:#FFF; font-weight:bold; text-transform:uppercase; border:1px solid #A2C0D8; cursor:pointer; }
		.ec_third_party_submit_button > input:hover{ background-color:#7A99BF; }
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
		
		$merchant_id = get_option( 'ec_option_payfast_merchant_id' );
		$merchant_key = get_option( 'ec_option_payfast_merchant_key' );
		$passphrase = get_option( 'ec_option_payfast_passphrase' );
		
		if( get_option( 'ec_option_payfast_sandbox' ) )
			$url = "https://sandbox.payfast.co.za/eng/process";
		else
			$url = "https://www.payfast.co.za/eng/process";
		
		// Merchant Information
		echo "<form name=\"ec_payfast_auto_form\" action=\"" . $url . "\" method=\"POST\">";
		echo "<input type=\"hidden\" name=\"merchant_id\" value=\"" . $merchant_id . "\" />";
		$signature_str = "merchant_id=" . urlencode( trim( $merchant_id ) );
		echo "<input type=\"hidden\" name=\"merchant_key\" value=\"" . $merchant_key . "\" />";
		$signature_str .= "&merchant_key=" . urlencode( trim( $merchant_key ) );
		echo "<input type=\"hidden\" name=\"return_url\" value=\"". htmlspecialchars( $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order_id ) . "\" />";
		$signature_str .= "&return_url=" . urlencode( trim(  $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order_id  ) );
		echo "<input type=\"hidden\" name=\"cancel_url\" value=\"". htmlspecialchars( $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment" ) . "\" />";
		$signature_str .= "&cancel_url=" . urlencode( trim(  $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment"  ) );
		echo "<input type=\"hidden\" name=\"notify_url\" value=\"". htmlspecialchars( plugins_url( EC_PLUGIN_DIRECTORY . "/inc/scripts/payfast_itn.php" ) ) . "\" />";
		$signature_str .= "&notify_url=" . urlencode( trim(  plugins_url( EC_PLUGIN_DIRECTORY . "/inc/scripts/payfast_itn.php" )  ) );
		
		//Customer Information
		echo "<input type=\"hidden\" name=\"name_first\" value=\"" . $this->order->billing_first_name . "\" />";
		$signature_str .= "&name_first=" . urlencode( trim( $this->order->billing_first_name ) );
		echo "<input type=\"hidden\" name=\"name_last\" value=\"" . $this->order->billing_last_name . "\" />";
		$signature_str .= "&name_last=" . urlencode( trim( $this->order->billing_last_name ) );
		echo "<input type=\"hidden\" name=\"email_address\" value=\"" . $this->order->user_email . "\" />";
		$signature_str .= "&email_address=" . urlencode( trim( $this->order->user_email ) );
		
		// Payment Information
		echo "<input type=\"hidden\" name=\"m_payment_id\" value=\"" . $this->order_id . "\" />";
		$signature_str .= "&m_payment_id=" . urlencode( trim( $this->order_id ) );
		echo "<input type=\"hidden\" name=\"amount\" value=\"" . number_format( $this->order->grand_total, 2, '.', '' ) . "\" />";
		$signature_str .= "&amount=" . urlencode( trim( number_format( $this->order->grand_total, 2, '.', '' ) ) );
		
		$item_name = "";
		for( $i=1; $i<=count( $this->order_details ) && $i<=5; $i++ ){
			if( $i > 1 )
				$item_name .= ', ';
			$item_name .= $this->order_details[$i-1]->title;
		}
		$item_name = substr( $item_name, 0, 100 );
		echo "<input type=\"hidden\" name=\"item_name\" value=\"" . $item_name . "\" />";
		$signature_str .= "&item_name=" . urlencode( trim( $item_name ) );
		
		// Transaction Options
		//echo "<input type=\"hidden\" name=\"email_confirmation\" value=\"0\" />";
		//$signature_str .= "&email_confirmation=0";
		
		if( $passphrase != '' )
			$signature_str .= '&passphrase='. urlencode( trim( $passphrase ) );
		
		// Security Options
		echo "<input type=\"hidden\" name=\"signature\" value=\"" . md5( $signature_str ) . "\" />";
		
		echo "</form>";
		echo "<SCRIPT data-cfasync=\"false\" LANGUAGE=\"Javascript\">document.ec_payfast_auto_form.submit();</SCRIPT>";
		
	}
	
}
?>