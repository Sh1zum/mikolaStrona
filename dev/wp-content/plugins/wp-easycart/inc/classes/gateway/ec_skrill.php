<?php
class ec_skrill extends ec_third_party{
	
	public function display_form_start( ){
		
		$skrill_merchant_id = get_option( 'ec_option_skrill_merchant_id' );
		$skrill_company_name = get_option( 'ec_option_skrill_company_name' );
		$skrill_email = get_option( 'ec_option_skrill_email' );
		$skrill_language = get_option( 'ec_option_skrill_language' );
		$skrill_currency_code = get_option( 'ec_option_skrill_currency_code' );
		
		echo "<form action=\"https://www.moneybookers.com/app/payment.pl\" method=\"POST\">";
		echo "<input type=\"hidden\" name=\"merchant_id\" value=\"" . $skrill_merchant_id . "\" />";
		echo "<input type=\"hidden\" name=\"transaction_id\" value=\"" . $this->order_id . "\" />";
		echo "<input type=\"hidden\" name=\"recipient_description\" value=\"" . $skrill_company_name . "\" />";
		echo "<input type=\"hidden\" name=\"return_url\" value=\"". $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order_id . "\" />";
		echo "<input type=\"hidden\" name=\"cancel_url\" value=\"". $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment\" />";
		if( $skrill_email != "" )
		echo "<input type=\"hidden\" name=\"status_url\" value=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/inc/scripts/skrill_payment_complete.php" ) . "\" />";
		echo "<input type=\"hidden\" name=\"language\" value=\"" . $skrill_language . "\" />";
		
		//Customer Information
		echo "<input type=\"hidden\" name=\"pay_from_email\" value=\"" . $this->order->user_email . "\" />";
		echo "<input type=\"hidden\" name=\"firstname\" value=\"" . $this->order->billing_first_name . "\" />";
		echo "<input type=\"hidden\" name=\"lastname\" value=\"" . $this->order->billing_last_name . "\" />";
		echo "<input type=\"hidden\" name=\"address\" value=\"" . $this->order->billing_address_line_1 . "\" />";
		$phone = str_replace( "-", "", str_replace( "(", "", str_replace( ")", "", str_replace( " ", "", str_replace( ".", "", $this->order->billing_phone ) ) ) ) );
		echo "<input type=\"hidden\" name=\"phone_number\" value=\"" . $phone . "\" />";
		echo "<input type=\"hidden\" name=\"postal_code\" value=\"" . $this->order->billing_zip . "\" />";
		echo "<input type=\"hidden\" name=\"city\" value=\"" . $this->order->billing_city . "\" />";
		echo "<input type=\"hidden\" name=\"state\" value=\"" . $this->order->billing_state . "\" />";
		$iso3_country_code = $this->mysqli->get_ios3_country_code( $this->order->billing_country ); 
		echo "<input type=\"hidden\" name=\"country\" value=\"" . $iso3_country_code . "\" />";
		
		//Cart Totals
		echo "<input type=\"hidden\" name=\"amount\" value=\"" . $this->order->grand_total . "\" />";							
		echo "<input type=\"hidden\" name=\"currency\" value=\"" . $skrill_currency_code . "\" />";
		
		if( $this->order->vat_total ){
			echo "<input type=\"hidden\" name=\"amount2_description\" value=\"" . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_vat' ) . " (" . number_format( ( $this->order->vat_total / ( $this->order->grand_total - $this->order->vat_total ) ) * 100, 0, '', '' ) . "%)\" />";
			echo "<input type=\"hidden\" name=\"amount2\" value=\"" . $this->order->vat_total . "\" />";
		}else{
			$tax = new ec_tax( 0.00, 0.00, 0.00, $this->order->billing_state, $this->order->billing_country );
			$tax_total = number_format( ( $this->order->tax_total + $this->order->duty_total + $this->order->gst_total + $this->order->pst_total + $this->order->hst_total * 100 ), 0, '', '' );
			echo "<input type=\"hidden\" name=\"amount2_description\" value=\"tax\" />";
			echo "<input type=\"hidden\" name=\"amount2\" value=\"" . $tax_total . "\" />";
		}
		echo "<input type=\"hidden\" name=\"amount3_description\" value=\"shipping\" />";
		echo "<input type=\"hidden\" name=\"amount3\" value=\"" . $this->order->shipping_total . "\" />";
		echo "<input type=\"hidden\" name=\"amount4_description\" value=\"discount\" />";
		echo "<input type=\"hidden\" name=\"amount4\" value=\"" . $this->order->discount_total . "\" />";
		
		//Cart Information
		for( $i=1; $i<=count( $this->order_details ) && $i<=5; $i++ ){
			echo "<input type=\"hidden\" name=\"detail" . $i . "_description\" value=\"" . $this->order_details[$i-1]->title . ": \" />";
			echo "<input type=\"hidden\" name=\"detail" . $i . "_text\" value=\"" . $this->order_details[$i-1]->model_number . "\" />";
		}
		echo "</form>";
	}
	
	public function display_auto_forwarding_form( ){
		
		$skrill_merchant_id = get_option( 'ec_option_skrill_merchant_id' );
		$skrill_company_name = get_option( 'ec_option_skrill_company_name' );
		$skrill_email = get_option( 'ec_option_skrill_email' );
		$skrill_language = get_option( 'ec_option_skrill_language' );
		$skrill_currency_code = get_option( 'ec_option_skrill_currency_code' );
		
		echo "<form name=\"ec_skrill_auto_form\" action=\"https://www.moneybookers.com/app/payment.pl\" method=\"POST\">";
		echo "<input type=\"hidden\" name=\"merchant_id\" value=\"" . $skrill_merchant_id . "\" />";
		echo "<input type=\"hidden\" name=\"transaction_id\" value=\"" . $this->order_id . "\" />";
		echo "<input type=\"hidden\" name=\"recipient_description\" value=\"" . $skrill_company_name . "\" />";
		echo "<input type=\"hidden\" name=\"return_url\" value=\"". $this->cart_page . $this->permalink_divider . "ec_page=checkout_success&order_id=" . $this->order_id . "\" />";
		echo "<input type=\"hidden\" name=\"cancel_url\" value=\"". $this->cart_page . $this->permalink_divider . "ec_page=checkout_payment\" />";
		if( $skrill_email != "" )
		echo "<input type=\"hidden\" name=\"status_url\" value=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/inc/scripts/skrill_payment_complete.php" ) . "\" />";
		echo "<input type=\"hidden\" name=\"language\" value=\"" . $skrill_language . "\" />";
		
		//Customer Information
		echo "<input type=\"hidden\" name=\"pay_from_email\" value=\"" . $this->order->user_email . "\" />";
		echo "<input type=\"hidden\" name=\"firstname\" value=\"" . $this->order->billing_first_name . "\" />";
		echo "<input type=\"hidden\" name=\"lastname\" value=\"" . $this->order->billing_last_name . "\" />";
		echo "<input type=\"hidden\" name=\"address\" value=\"" . $this->order->billing_address_line_1 . "\" />";
		$phone = str_replace( "-", "", str_replace( "(", "", str_replace( ")", "", str_replace( " ", "", str_replace( ".", "", $this->order->billing_phone ) ) ) ) );
		echo "<input type=\"hidden\" name=\"phone_number\" value=\"" . $phone . "\" />";
		echo "<input type=\"hidden\" name=\"postal_code\" value=\"" . $this->order->billing_zip . "\" />";
		echo "<input type=\"hidden\" name=\"city\" value=\"" . $this->order->billing_city . "\" />";
		echo "<input type=\"hidden\" name=\"state\" value=\"" . $this->order->billing_state . "\" />";
		$iso3_country_code = $this->mysqli->get_ios3_country_code( $this->order->billing_country ); 
		echo "<input type=\"hidden\" name=\"country\" value=\"" . $iso3_country_code . "\" />";
		
		//Cart Totals
		echo "<input type=\"hidden\" name=\"amount\" value=\"" . $this->order->grand_total . "\" />";							
		echo "<input type=\"hidden\" name=\"currency\" value=\"" . $skrill_currency_code . "\" />";
		
		if( $this->order->vat_total ){
			echo "<input type=\"hidden\" name=\"amount2_description\" value=\"" . $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_vat' ) . " (" . number_format( ( $this->order->vat_total / ( $this->order->grand_total - $this->order->vat_total ) ) * 100, 0, '', '' ) . "%)\" />";
			echo "<input type=\"hidden\" name=\"amount2\" value=\"" . $this->order->vat_total . "\" />";
		}else{
			echo "<input type=\"hidden\" name=\"amount2_description\" value=\"tax\" />";
			echo "<input type=\"hidden\" name=\"amount2\" value=\"" . $this->order->tax_total . "\" />";
		}
		echo "<input type=\"hidden\" name=\"amount3_description\" value=\"shipping\" />";
		echo "<input type=\"hidden\" name=\"amount3\" value=\"" . $this->order->shipping_total . "\" />";
		echo "<input type=\"hidden\" name=\"amount4_description\" value=\"discount\" />";
		echo "<input type=\"hidden\" name=\"amount4\" value=\"" . $this->order->discount_total . "\" />";
		
		//Cart Information
		for( $i=1; $i<=count( $this->order_details ) && $i<=5; $i++ ){
			echo "<input type=\"hidden\" name=\"detail" . $i . "_description\" value=\"" . $this->order_details[$i-1]->title . ": \" />";
			echo "<input type=\"hidden\" name=\"detail" . $i . "_text\" value=\"" . $this->order_details[$i-1]->model_number . "\" />";
		}
		echo "</form>";
		echo "<SCRIPT data-cfasync=\"false\" LANGUAGE=\"Javascript\">document.ec_skrill_auto_form.submit();</SCRIPT>";
	}
	
}
?>