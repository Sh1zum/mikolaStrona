<?php
// This is a payment gateway basic structure,
// child classes will be based on this class.

class ec_realex extends ec_gateway{
	
	/****************************************
	* GATEWAY SPECIFIC HELPER FUNCTIONS
	*****************************************/
	
	function get_gateway_data( $autosettle = 1 ){
		
		$realex_merchant_id = get_option( 'ec_option_realex_merchant_id' );
		$realex_secret = get_option( 'ec_option_realex_secret' );
		$realex_currency = get_option( 'ec_option_realex_currency' );
		$realex_use_3dsecure = get_option( 'ec_option_realex_3dsecure' );
		
		$realex_timestamp = strftime("%Y%m%d%H%M%S");
		mt_srand((double)microtime()*1000000);
		
		$realex_order_id = $this->order_id;
		$realex_total = $this->order_totals->get_grand_total_in_cents( );
		$realex_card_number = $this->credit_card->card_number;
		
		$tmp = "$realex_timestamp.$realex_merchant_id.$realex_order_id.$realex_total.$realex_currency.$realex_card_number";
		$md5hash = md5($tmp);
		$tmp_md5 = "$md5hash.$realex_secret";
		$md5hash = md5($tmp_md5);
		
		$sha1hash = sha1($tmp);
		$tmp_sha1 = "$sha1hash.$realex_secret";
		$sha1hash = sha1($tmp_sha1);
		
		if( $this->credit_card->payment_method == "visa" )
			$realex_payment_method = "VISA";
		else if( $this->credit_card->payment_method == "mastercard" )
			$realex_payment_method = "MC";
		else if( $this->credit_card->payment_method == "amex" )
			$realex_payment_method = "AMEX";
		else if( $this->credit_card->payment_method == "jcb" )
			$realex_payment_method = "JCB";
		else if( $this->credit_card->payment_method == "diners" )
			$realex_payment_method = "DINERS";
		else if( $this->credit_card->payment_method == "laser" )
			$realex_payment_method = "LASER";
		else if( $this->credit_card->payment_method == "maestro" )
			$realex_payment_method = "SWITCH";
		else
			$realex_payment_method = "VISA";
		
		if( $realex_payment_method == "AMEX" )
			$realex_account = "remoteamex";
		else if( $realex_payment_method == "DINERS" )
			$realex_account = "remotediners";
		else
			$realex_account = "remote";
		
		$realex_xml = "<request type='auth' timestamp='" . $realex_timestamp . "'>
				<merchantid>" . $realex_merchant_id . "</merchantid>
				<account>" . $realex_account . "</account>
				<channel>ECOM</channel>
				<orderid>" . $realex_order_id . "</orderid>
				<amount currency='" . $realex_currency . "'>" . $this->order_totals->get_grand_total_in_cents( ) . "</amount>
				<card> 
					<number>" . $this->credit_card->card_number . "</number>
					<expdate>" . $this->credit_card->expiration_month . $this->credit_card->get_expiration_year( 2 ) . "</expdate>
					<chname>" . $this->credit_card->card_holder_name . "</chname> 
					<type>" . $realex_payment_method . "</type> 
					<cvn>
						<number>" . $this->credit_card->security_code . "</number>
						<presind>1</presind>
					</cvn>
				</card>
				<autosettle flag='" . $autosettle . "'/>
				<sha1hash>" . $sha1hash . "</sha1hash>
			</request>";
		
		return $realex_xml;
		
	}
	
	function get_gateway_auth_data( $cavv, $xid, $eci, $user_id, $cvn ){
		$realex_merchant_id = get_option( 'ec_option_realex_merchant_id' );
		$realex_secret = get_option( 'ec_option_realex_secret' );
		$realex_currency = get_option( 'ec_option_realex_currency' );
		$realex_use_3dsecure = get_option( 'ec_option_realex_3dsecure' );
		
		$realex_timestamp = strftime("%Y%m%d%H%M%S");
		mt_srand((double)microtime()*1000000);
		
		$realex_order_id = $_GET['order_id'];
		$db = new ec_db( );
		$order = $db->get_order_row( $realex_order_id, "guest", "guest" );
		$realex_total = number_format( $order->grand_total * 100, 0, '', '' );
		$realex_user_id = $user_id;
		
		$tmp = "$realex_timestamp.$realex_merchant_id.$realex_order_id.$realex_total.$realex_currency.$realex_user_id";
		$md5hash = md5($tmp);
		$tmp_md5 = "$md5hash.$realex_secret";
		$md5hash = md5($tmp_md5);
		
		$sha1hash = sha1($tmp);
		$tmp_sha1 = "$sha1hash.$realex_secret";
		$sha1hash = sha1($tmp_sha1);
		
		$realex_xml = "<request timestamp='" . $realex_timestamp . "' type='receipt-in'>
				<merchantid>" . $realex_merchant_id . "</merchantid>
				<account>remote3dsecure</account>
				<orderid>" . $realex_order_id . "</orderid>
				<paymentdata>
					<cvn>
						<number>" . $cvn . "</number>
					</cvn>
				</paymentdata>
				<mpi>
					<cavv>" . $cavv . "</cavv>
					<xid>" . $xid . "</xid>
					<eci>" . $eci . "</eci>
				</mpi>
				<amount currency='" . $realex_currency . "'>" . $realex_total . "</amount>
				<payerref>" . $user_id . "</payerref>
				<paymentmethod>" . $user_id . "01</paymentmethod>
				<autosettle flag=\"1\" />
				<sha1hash>" . $sha1hash . "</sha1hash>
			</request>";
		
		return $realex_xml;
	}
	
	function get_gateway_void_data( $cavv, $xid, $eci, $pasref, $authcode ){
		$realex_merchant_id = get_option( 'ec_option_realex_merchant_id' );
		$realex_secret = get_option( 'ec_option_realex_secret' );
		$realex_currency = get_option( 'ec_option_realex_currency' );
		$realex_use_3dsecure = get_option( 'ec_option_realex_3dsecure' );
		
		$realex_timestamp = strftime("%Y%m%d%H%M%S");
		mt_srand((double)microtime()*1000000);
		
		$realex_order_id = $_GET['order_id'];
		$db = new ec_db( );
		$order = $db->get_order_row( $realex_order_id, "guest", "guest" );
		$realex_total = number_format( $order->grand_total * 100, 0, '', '' );
		$realex_card_number = "";
		
		$tmp = "$realex_timestamp.$realex_merchant_id.$realex_order_id.$realex_total.$realex_currency.$realex_card_number";
		$md5hash = md5($tmp);
		$tmp_md5 = "$md5hash.$realex_secret";
		$md5hash = md5($tmp_md5);
		
		$sha1hash = sha1($tmp);
		$tmp_sha1 = "$sha1hash.$realex_secret";
		$sha1hash = sha1($tmp_sha1);
		
		$realex_xml = "<request timestamp='" . $realex_timestamp . "' type='void'>
				<merchantid>" . $realex_merchant_id . "</merchantid>
				<account>remote3dsecure</account>
				<orderid>" . $realex_order_id . "</orderid>
				<amount currency='" . $realex_currency . "'>" . $realex_total . "</amount>
				<pasref>" . $pasref . "</pasref>
				<authcode>" . $authcode . "</authcode>
				<mpi>
					<cavv>" . $cavv . "</cavv>
					<xid>" . $xid . "</xid>
					<eci>" . $eci . "</eci>
				</mpi>
				<sha1hash>" . $sha1hash . "</sha1hash>
			</request>";
		
		return $realex_xml;
	}
	
	function check_enrollment_status( ){
		$realex_merchant_id = get_option( 'ec_option_realex_merchant_id' );
		$realex_secret = get_option( 'ec_option_realex_secret' );
		$realex_currency = get_option( 'ec_option_realex_currency' );
		$realex_use_3dsecure = get_option( 'ec_option_realex_3dsecure' );
		
		$realex_timestamp = strftime("%Y%m%d%H%M%S");
		mt_srand((double)microtime()*1000000);
		
		$realex_order_id = $this->order_id;
		$realex_total = $this->order_totals->get_grand_total_in_cents( );
		$realex_card_number = $this->credit_card->card_number;
		
		$tmp = "$realex_timestamp.$realex_merchant_id.$realex_order_id.$realex_total.$realex_currency.$realex_card_number";
		$md5hash = md5($tmp);
		$tmp_md5 = "$md5hash.$realex_secret";
		$md5hash = md5($tmp_md5);
		
		$sha1hash = sha1($tmp);
		$tmp_sha1 = "$sha1hash.$realex_secret";
		$sha1hash = sha1($tmp_sha1);
		
		$realex_xml = "<request timestamp=\"" . $realex_timestamp . "\" type=\"3ds-verifyenrolled\">
				<merchantid>" . $realex_merchant_id . "</merchantid>
				<account>remote3dsecure</account>
				<orderid>" . $realex_order_id . "</orderid>
				<amount currency=\"" . $realex_currency . "\">" . $this->order_totals->get_grand_total_in_cents( ) . "</amount>
				<card>
					<number>" . $this->credit_card->card_number . "</number>
					<expdate>" . $this->credit_card->expiration_month . $this->credit_card->get_expiration_year( 2 ) . "</expdate>
					<type>VISA</type>
					<chname>" . $this->credit_card->card_holder_name . "</chname>
				</card>
				<sha1hash>" . $sha1hash . "</sha1hash>
			</request>";
		
		// Get server response
		$response = $this->get_3d_auth_gateway_response( $this->get_gateway_url( true ), $realex_xml, "" );
		
		// Set the variables that are needed for 3d secure if needed
		return $this->handle_enrollment_check( $response, $realex_timestamp, $sha1hash );
		
	}
	
	function handle_enrollment_check( $response, $timestamp, $sha1hash ){
		
		// Handle response data
		$response_body = $response["body"];
		$xml = new SimpleXMLElement($response_body);
		
		// Get the important values
		if( $xml->result == "00" && $xml->enrolled == "Y" ){
			$this->is_3d_auth = true;
			$this->post_url = $xml->url;
			$this->post_id_input_name = "MD";
			$this->post_message_input_name = "PaReq";
			$this->post_message = $xml->pareq;
			$this->post_return_url_input_name = "TermUrl";
			return true;
		}else if( ( $xml->result == "110" && $xml->enrolled == "N" ) || ( $xml->enrolled == "U" && get_option( 'ec_option_realex_3dsecure' ) == "1" ) || ( $xml->result == "220" && get_option( 'ec_option_realex_3dsecure' ) == "1" ) ){
			return true;
		}else{
			return false;
		}
	}
	
	function get_realvalue_store_payer_data( $payer_type = "new" ){
		$realex_merchant_id = get_option( 'ec_option_realex_merchant_id' );
		$realex_secret = get_option( 'ec_option_realex_secret' );
		$realex_currency = get_option( 'ec_option_realex_currency' );
		$realex_use_3dsecure = get_option( 'ec_option_realex_3dsecure' );
		
		$realex_timestamp = strftime("%Y%m%d%H%M%S");
		mt_srand((double)microtime()*1000000);
		
		$realex_order_id = $this->order_id;
		$realex_total = $this->order_totals->get_grand_total_in_cents( );
		$realex_card_number = $this->credit_card->card_number;
		$realex_user_id = $this->user->user_id;
		
		$tmp = "$realex_timestamp.$realex_merchant_id.$realex_order_id" . '...' . "$realex_user_id";
		$md5hash = md5($tmp);
		$tmp_md5 = "$md5hash.$realex_secret";
		$md5hash = md5($tmp_md5);
		
		$sha1hash = sha1($tmp);
		$tmp_sha1 = "$sha1hash.$realex_secret";
		$sha1hash = sha1($tmp_sha1);
		
		$realex_xml = "<request timestamp=\"" . $realex_timestamp . "\" type=\"payer-" . $payer_type . "\">
				<merchantid>" . $realex_merchant_id . "</merchantid>
				<account>remote3dsecure</account>
				<orderid>" . $realex_order_id . "</orderid>
				<payer type=\"" . $this->user->user_level . "\" ref=\"" . $this->user->user_id . "\">
					<firstname>" . $this->user->billing->first_name . "</firstname>
					<surname>" . $this->user->billing->last_name . "</surname>
					<address>
						<line1>" . $this->user->billing->address_line_1 . "</line1>
						<line2 />
						<line3 />
						<city>" . $this->user->billing->city . "</city>
						<postcode>" . $this->user->billing->zip . "</postcode>
						<country code=\"" . $this->user->billing->country . "\">" . $this->user->billing->country_name . "</country>
					</address>
					<phonenumbers>
						<work>" . $this->user->billing->phone . "</work>
					</phonenumbers>
					<email>" . $this->user->email . "</email>
				</payer>
				<sha1hash>" . $sha1hash . "</sha1hash>
			</request>";
		
		return $realex_xml;
	}
	
	function get_realvault_store_card_data( $card_type = "new" ){
		$realex_merchant_id = get_option( 'ec_option_realex_merchant_id' );
		$realex_secret = get_option( 'ec_option_realex_secret' );
		$realex_currency = get_option( 'ec_option_realex_currency' );
		$realex_use_3dsecure = get_option( 'ec_option_realex_3dsecure' );
		
		$realex_timestamp = strftime("%Y%m%d%H%M%S");
		mt_srand((double)microtime()*1000000);
		
		$realex_order_id = $this->order_id;
		$realex_total = $this->order_totals->get_grand_total_in_cents( );
		$realex_card_number = $this->credit_card->card_number;
		$realex_user_id = $this->user->user_id;
		$realex_chname = $this->credit_card->card_holder_name;
		
		$tmp = "$realex_timestamp.$realex_merchant_id.$realex_order_id" . '...' . "$realex_user_id.$realex_chname.$realex_card_number";
		$md5hash = md5($tmp);
		$tmp_md5 = "$md5hash.$realex_secret";
		$md5hash = md5($tmp_md5);
		
		$sha1hash = sha1($tmp);
		$tmp_sha1 = "$sha1hash.$realex_secret";
		$sha1hash = sha1($tmp_sha1);
		
		if( $this->credit_card->payment_method == "visa" )
			$realex_payment_method = "VISA";
		else if( $this->credit_card->payment_method == "mastercard" )
			$realex_payment_method = "MC";
		else if( $this->credit_card->payment_method == "amex" )
			$realex_payment_method = "AMEX";
		else if( $this->credit_card->payment_method == "jcb" )
			$realex_payment_method = "JCB";
		else if( $this->credit_card->payment_method == "diners" )
			$realex_payment_method = "DINERS";
		else if( $this->credit_card->payment_method == "laser" )
			$realex_payment_method = "LASER";
		else if( $this->credit_card->payment_method == "maestro" )
			$realex_payment_method = "SWITCH";
		else
			$realex_payment_method = "VISA";
		
		$realex_xml = "<request timestamp=\"" . $realex_timestamp . "\" type=\"card-" . $card_type . "\">
				<merchantid>" . $realex_merchant_id . "</merchantid>
				<account>remote3dsecure</account>
				<orderid>" . $realex_order_id . "</orderid>
				<card>
					<ref>" . $this->user->user_id . "01</ref>
					<payerref>" . $this->user->user_id . "</payerref>
					<number>" . $this->credit_card->card_number . "</number>
					<expdate>" . $this->credit_card->expiration_month . $this->credit_card->get_expiration_year( 2 ) . "</expdate>
					<chname>" . $this->credit_card->card_holder_name . "</chname> 
					<type>" . $realex_payment_method . "</type>
				</card>
				<sha1hash>" . $sha1hash . "</sha1hash>
			</request>";
		
		return $realex_xml;
	}
	
	function get_realvault_remove_card_data( ){
		$realex_merchant_id = get_option( 'ec_option_realex_merchant_id' );
		$realex_secret = get_option( 'ec_option_realex_secret' );
		$realex_currency = get_option( 'ec_option_realex_currency' );
		$realex_use_3dsecure = get_option( 'ec_option_realex_3dsecure' );
		
		$realex_timestamp = strftime("%Y%m%d%H%M%S");
		mt_srand((double)microtime()*1000000);
		
		$realex_user_id = $this->user->user_id;
		
		$tmp = "$realex_timestamp.$realex_merchant_id.$realex_user_id.$realex_user_id" . "01";
		$md5hash = md5($tmp);
		$tmp_md5 = "$md5hash.$realex_secret";
		$md5hash = md5($tmp_md5);
		
		$sha1hash = sha1($tmp);
		$tmp_sha1 = "$sha1hash.$realex_secret";
		$sha1hash = sha1($tmp_sha1);
		
		$realex_xml = "<request timestamp=\"" . $realex_timestamp . "\" type=\"card-cancel-card\">
				<merchantid>" . $realex_merchant_id . "</merchantid>
				<account>remote3dsecure</account>
				<card>
					<ref>" . $this->user->user_id . "01</ref>
					<payerref>" . $this->user->user_id . "</payerref>
				</card>
				<sha1hash>" . $sha1hash . "</sha1hash>
			</request>";
		
		return $realex_xml;
	}
	
	function get_gateway_3dcomplete_data( $pares, $mdval ){
		$realex_merchant_id = get_option( 'ec_option_realex_merchant_id' );
		$realex_secret = get_option( 'ec_option_realex_secret' );
		$realex_currency = get_option( 'ec_option_realex_currency' );
		$realex_use_3dsecure = get_option( 'ec_option_realex_3dsecure' );
		
		$realex_timestamp = strftime("%Y%m%d%H%M%S");
		mt_srand((double)microtime()*1000000);
		
		$realex_order_id = $_GET['order_id'];
		$db = new ec_db( );
		$order = $db->get_order_row( $realex_order_id, "guest", "guest" );
		$realex_total = number_format( $order->grand_total * 100, 0, '', '' );
		$realex_card_number = "";
		
		$tmp = "$realex_timestamp.$realex_merchant_id.$realex_order_id.$realex_total.$realex_currency.$realex_card_number";
		$md5hash = md5($tmp);
		$tmp_md5 = "$md5hash.$realex_secret";
		$md5hash = md5($tmp_md5);
		
		$sha1hash = sha1($tmp);
		$tmp_sha1 = "$sha1hash.$realex_secret";
		$sha1hash = sha1($tmp_sha1);
		
		$realex_xml = "<request timestamp=\"" . $realex_timestamp . "\" type=\"3ds-verifysig\">
				<merchantid>" . $realex_merchant_id . "</merchantid>
				<account>remote3dsecure</account>
				<amount currency=\"" . $realex_currency . "\">" . $realex_total . "</amount>
				<orderid>" . $realex_order_id . "</orderid>
				<pares>" . $pares . "</pares>
				<sha1hash>" . $sha1hash . "</sha1hash>
			</request>";
			
		return $realex_xml;
	}
	
	function get_gateway_url( $mpi = false, $realvault = false ){
		
		if( $mpi )
			return "https://epage.payandshop.com/epage-3dsecure.cgi";// For MPI Requests Only
		else if( $realvault )
			return "https://epage.payandshop.com/epage-remote-plugins.cgi";// For RealVault Requests Only
		else if( get_option( 'ec_option_realex_test_mode' ) )
			return "https://api.sandbox.realexpayments.com/epage-remote.cgi";
		else
			return "https://api.realexpayments.com/epage-remote.cgi";// For Basic Authorization Requests

	}
	
	function get_3d_auth_gateway_response( $gateway_url, $gateway_data, $gateway_headers ){
		
		$request = new WP_Http;
		$response = $request->request( $gateway_url, array( 'method' => 'POST', 'body' => $gateway_data, 'headers' => $gateway_headers ) );
		if( is_wp_error( $response ) ){
			$this->error_message = $response->get_error_message();
			return false;
		}else
			return $response;
	}
	
	function get_gateway_response( $gateway_url, $gateway_data, $gateway_headers ){
		
		// If 3d authentication is possible, check enrollment
		$realex_use_3dsecure = get_option( 'ec_option_realex_3dsecure' );
		if( $realex_use_3dsecure && ( $this->credit_card->payment_method == "visa" || $this->credit_card->payment_method == "mastercard" || $this->credit_card->payment_method == "switch" ) ){
			//Check enrollment if it is a visa and using 3d secure
			$enrollment_result = $this->check_enrollment_status( );
			
			if( !$enrollment_result ) // If we got a result that is invalid, we should revoke transaction here.
				return false;
		}
		
		// Check if using 3d authentication, if so just return 1
		if( $this->is_3d_auth && $enrollment_result == 1 ){
			// Setup new Payer
			$request = new WP_Http;
			$gateway_url = $this->get_gateway_url( 0, 1 );
			if( $this->user->realauth_registered )
				$gateway_data = $this->get_realvalue_store_payer_data( "edit" );
			else
				$gateway_data = $this->get_realvalue_store_payer_data( "new" );
				
			$response = $request->request( $gateway_url, array( 'method' => 'POST', 'body' => $gateway_data, 'headers' => $gateway_headers ) );
			if( is_wp_error( $response ) ){
				$this->error_message = $response->get_error_message();
				return false;
			}
			
			$response_body = $response["body"];
			$xml = new SimpleXMLElement($response_body);
			
			// Must now store card information in the "RealVault"
			if( $this->user->realauth_registered ){ // If registered delete the old card, then add a new one.
				$request = new WP_Http;
				$gateway_url = $this->get_gateway_url( 0, 1 );
				$gateway_data = $this->get_realvault_remove_card_data( );
				$response = $request->request( $gateway_url, array( 'method' => 'POST', 'body' => $gateway_data, 'headers' => $gateway_headers ) );
				if( is_wp_error( $response ) ){
					$this->error_message = $response->get_error_message();
					return false;
				}
				
				$response_body = $response["body"];
				$xml = new SimpleXMLElement($response_body);
				
			}
			
			$request = new WP_Http;
			$gateway_url = $this->get_gateway_url( 0, 1 );
			$gateway_data = $this->get_realvault_store_card_data( "new" );
				
			$response = $request->request( $gateway_url, array( 'method' => 'POST', 'body' => $gateway_data, 'headers' => $gateway_headers ) );
			if( is_wp_error( $response ) ){
				$this->error_message = $response->get_error_message();
				return false;
			}
			
			$response_body = $response["body"];
			$xml = new SimpleXMLElement($response_body);
			
			$this->mysqli->update_user_realvault_registered( $this->user->user_id );
			
			// Set MD to be retrieved later
			$this->post_id = $this->user->user_id . "|" . $this->credit_card->security_code;
			return true;
		
		// Not using 3D Secure, so process as usual
		}else{
			$request = new WP_Http;
			$response = $request->request( $gateway_url, array( 'method' => 'POST', 'body' => $gateway_data, 'headers' => $gateway_headers ) );
			if( is_wp_error( $response ) ){
				$this->error_message = $response->get_error_message();
				return false;
			}else
				return $response;
		}
	}
	
	function handle_gateway_response( $response ){
		
		if( !$this->is_3d_auth ){
			$response_body = $response["body"];
			$xml = new SimpleXMLElement($response_body);
			
			$result_code = $xml->result;
			$result_message = $xml->message;
			
			$response_text = print_r( $xml, true );
			
			if( $result_code == "00" ){
				$this->is_success = 1;
			}else{
				$this->is_success = 0;
			}
			
			$this->mysqli->insert_response( $this->order_id, !$this->is_success, "Realex", $response_text );
			
			if( !$this->is_success )
				$this->error_message = $result_message;
		}else{
			$this->is_success = 1;
		}
			
	}
	
	public function secure_3d_auth( ){
		
		$gateway_url = $this->get_gateway_url( true );
		$gateway_data = $this->get_gateway_3dcomplete_data( $_POST['PaRes'], $_POST['MD'] );
		
		if( isset( $gateway_url ) && isset( $gateway_data ) ){
			$request = new WP_Http;
			$response = $request->request( $gateway_url, array( 'method' => 'POST', 'body' => $gateway_data, 'headers' => "" ) );
			if( is_wp_error( $response ) ){
				$this->error_message = $response->get_error_message();
				return false;
			}
			
			$response_body = $response["body"];
			$xml = new SimpleXMLElement($response_body);
			
			$result = $xml->result;
			$status = $xml->threedsecure->status;
			
			if( $result == "00" && ( $status == "Y" || $status == "A" || ( $status == "U" && get_option( 'ec_option_realex_3dsecure' ) == "1" ) ) ){
				// Success, so settle the transaction
				$gateway_url = $this->get_gateway_url( );
				$userid_cvn_authcode_array = explode( "|", $_POST['MD'] );
				$user_id = $userid_cvn_authcode_array[0];
				$cvn = $userid_cvn_authcode_array[1];
				$gateway_data = $this->get_gateway_auth_data( $xml->threedsecure->cavv, $xml->threedsecure->xid, $xml->threedsecure->eci, $user_id, $cvn );
				$request = new WP_Http;
				$response = $request->request( $gateway_url, array( 'method' => 'POST', 'body' => $gateway_data, 'headers' => "" ) );
				if( is_wp_error( $response ) ){
					$this->error_message = $response->get_error_message();
					return false;
				}
				$response_body = $response["body"];
				$xml = new SimpleXMLElement($response_body);
				
				if( $xml->result == "00" ){ // Successful settlement, update order
					$this->mysqli->update_order_status( $_GET['order_id'], "6" );
					// send email
					$db_admin = new ec_db_admin( );
					$order_row = $db_admin->get_order_row_admin( $_GET['order_id'] );
					$order_display = new ec_orderdisplay( $order_row, true, true );
					$order_display->send_email_receipt( );
					$this->mysqli->clear_tempcart( $GLOBALS['ec_cart_data']->ec_cart_id );
					return true;
					
				}else{ // Settlement failed
					return false;
				}
			}else{ // 3D Failed
				return false;
			}
		}else{
			return false;
		}
	}
	
}

?>