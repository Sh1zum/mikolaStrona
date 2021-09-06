<?php
class ec_sagepay_paynow_za extends ec_third_party{
	
	public function display_form_start( ){
		
		$request_url = "https://paynow.sagepay.co.za/site/paynow.aspx";
		$service_key = get_option( 'ec_option_sagepay_paynow_za_service_key' );
		
		$tax = new ec_tax( 0.00, 0.00, 0.00, $this->order->billing_state, $this->order->billing_country );
		$tax_total = number_format( $this->order->tax_total + $this->order->duty_total + $this->order->gst_total + $this->order->pst_total + $this->order->hst_total, 2 );
		if( !$tax->vat_included )
			$tax_total = number_format( $tax_total + $this->order->vat_total, 2 );
		
		echo "<form action=\"" . $request_url . "\" method=\"post\">";
		echo "<input name=\"m1\" id=\"m1\" type=\"hidden\" value=\"" . $service_key . "\" />";
		echo "<input name=\"m2\" id=\"m2\" type=\"hidden\" value=\"24ade73c-98cf-47b3-99be-cc7b867b3080\" />"; // NEED OUR OWN GUID
		echo "<input name=\"p2\" id=\"p2\" type=\"hidden\" value=\"" . rand( 1111111,9999999 ) . "-" . $this->order_id . "\" />";
		echo "<input name=\"p3\" id=\"p3\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_first_name, ENT_QUOTES ) . " " . htmlspecialchars( $this->order->billing_last_name, ENT_QUOTES ) . " | " .  $this->order_id . "\" />";
		echo "<input name=\"p4\" id=\"p4\" type=\"hidden\" value=\"" . number_format( $this->order->grand_total, 2 ) . "\" />";
		echo "<input name=\"m5\" id=\"m5\" type=\"hidden\" value=\"" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment" . "\" />";
		echo "<input name=\"m6\" id=\"m6\" type=\"hidden\" value=\"" . $this->order_id . "\" />";
		echo "<input name=\"m10\" id=\"m10\" type=\"hidden\" value=\"WP EasyCart\" />";
		echo "<input name=\"return_url\" id=\"return_url\" type=\"hidden\" value=\"" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order_id . "\" />";
		echo "<input name=\"cancel_url\" id=\"cancel_url\" type=\"hidden\" value=\"" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment" . "\" />";
		echo "<input name=\"notify_url\" id=\"notify_url\" type=\"hidden\" value=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/inc/scripts/sagepay_paynow_za_payment_complete.php" ) . "\" />";
		
		echo "<input name=\"first_name\" id=\"first_name\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_first_name, ENT_QUOTES ) . "\" />";
		echo "<input name=\"last_name\" id=\"last_name\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_last_name, ENT_QUOTES ) . "\" />";
		echo "<input name=\"email_address\" id=\"email_address\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->user_email, ENT_QUOTES ) . "\" />";
		echo "<input type=\"submit\" value=\"Complete Payment\">";
		echo "</form>";
	}
	
	public function display_auto_forwarding_form( ){
		
		$request_url = "https://paynow.sagepay.co.za/site/paynow.aspx";
		$service_key = get_option( 'ec_option_sagepay_paynow_za_service_key' );
		
		$tax = new ec_tax( 0.00, 0.00, 0.00, $this->order->billing_state, $this->order->billing_country );
		$tax_total = number_format( $this->order->tax_total + $this->order->duty_total + $this->order->gst_total + $this->order->pst_total + $this->order->hst_total, 2 );
		if( !$tax->vat_included )
			$tax_total = number_format( $tax_total + $this->order->vat_total, 2 );
		
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
		
		echo "<form name=\"ec_sagepay_paynow_za_auto_form\" action=\"" . $request_url . "\" method=\"post\">";
		echo "<input name=\"m1\" id=\"m1\" type=\"hidden\" value=\"" . $service_key . "\" />";
		echo "<input name=\"m2\" id=\"m2\" type=\"hidden\" value=\"24ade73c-98cf-47b3-99be-cc7b867b3080\" />"; // NEED OUR OWN GUID
		echo "<input name=\"p2\" id=\"p2\" type=\"hidden\" value=\"" . rand( 1111111,9999999 ) . "-" . $this->order_id . "\" />";
		echo "<input name=\"p3\" id=\"p3\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_first_name, ENT_QUOTES ) . " " . htmlspecialchars( $this->order->billing_last_name, ENT_QUOTES ) . " | " .  $this->order_id . "\" />";
		echo "<input name=\"p4\" id=\"p4\" type=\"hidden\" value=\"" . number_format( $this->order->grand_total, 2 ) . "\" />";
		echo "<input name=\"m5\" id=\"m5\" type=\"hidden\" value=\"" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment" . "\" />";
		echo "<input name=\"m6\" id=\"m6\" type=\"hidden\" value=\"" . $this->order_id . "\" />";
		echo "<input name=\"m10\" id=\"m10\" type=\"hidden\" value=\"WP EasyCart\" />";
		echo "<input name=\"return_url\" id=\"return_url\" type=\"hidden\" value=\"" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order_id . "\" />";
		echo "<input name=\"cancel_url\" id=\"cancel_url\" type=\"hidden\" value=\"" . $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment" . "\" />";
		echo "<input name=\"notify_url\" id=\"notify_url\" type=\"hidden\" value=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/inc/scripts/sagepay_paynow_za_payment_complete.php" ) . "\" />";
		
		echo "<input name=\"first_name\" id=\"first_name\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_first_name, ENT_QUOTES ) . "\" />";
		echo "<input name=\"last_name\" id=\"last_name\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->billing_last_name, ENT_QUOTES ) . "\" />";
		echo "<input name=\"email_address\" id=\"email_address\" type=\"hidden\" value=\"" . htmlspecialchars( $this->order->user_email, ENT_QUOTES ) . "\" />";
		
		echo "</form>";
		echo "<SCRIPT data-cfasync=\"false\" LANGUAGE=\"Javascript\">document.ec_sagepay_paynow_za_auto_form.submit();</SCRIPT>";
	}
	
}
?>