<?php
/*
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//All Code and Design is copyrighted by Level Four Development, llc
//
//Level Four Development, LLC provides this code "as is" without warranty of any kind, either express or implied,     
//including but not limited to the implied warranties of merchantability and/or fitness for a particular purpose.         
//
//Only licnesed users may use this code and storfront for live purposes. All other use is prohibited and may be 
//subject to copyright violation laws. If you have any questions regarding proper use of this code, please
//contact Level Four Development, llc and EasyCart prior to use.
//
//All use of this storefront is subject to our terms of agreement found on Level Four Development, llc's  website.
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/


class ec_admin_users{		
		
	private $db;
	
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

	}
	
	//secure all of the services for logged in authenticated users only	
	public function _getMethodRoles($methodName){
	   if ($methodName == 'getclients') return array('admin');
	   else if($methodName == 'deleteclient') return array('admin');
	   else if($methodName == 'updateclient') return array('admin');
	   else if($methodName == 'addclient') return array('admin');
	   else if($methodName == 'getuserroles') return array('admin');
	   else if($methodName == 'deleteuserrole') return array('admin');
	   else if($methodName == 'adduserrole') return array('admin');
	   else if($methodName == 'resendverificationemail') return array('admin');
	   else if($methodName == 'getsecuritylevel') return array('admin');
	   else if($methodName == 'savesecuritylevel') return array('admin');
	   else  return null;
	}	
	
	function getclients( $startrecord, $limit, $orderby, $ordertype, $filter ){
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_user.* FROM ec_user WHERE ec_user.user_id != '' " . $filter . " ORDER BY " . $orderby . " " . $ordertype . " LIMIT ". $startrecord . ", " . $limit;
		
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( $totalrows > 0 ){
			$returnArray = array( );
			
			foreach( $results as $row ){
				
				//get the shipping and billing address id's
				$billing_address_id = $row->default_billing_address_id;
				$shipping_address_id = $row->default_shipping_address_id;
				
				//query for the billing address
				if( $billing_address_id != 0 ){
					$billingquery = "SELECT ec_address.first_name AS billing_first_name, ec_address.last_name AS billing_last_name, ec_address.company_name AS billing_company_name, ec_address.address_line_1 AS billing_address_line_1, ec_address.address_line_2 AS billing_address_line_2, ec_address.city AS billing_city, ec_address.state AS billing_state, ec_address.zip AS billing_zip, ec_address.country AS billing_country, ec_address.phone AS billing_phone FROM ec_address WHERE ec_address.address_id = %s";
					$billing_row = $this->db->get_row( $this->db->prepare( $billingquery, $billing_address_id ) );
				  	$row->billing_first_name = $billing_row->billing_first_name;
					$row->billing_last_name = $billing_row->billing_last_name;
					$row->billing_company_name = $billing_row->billing_company_name;
					$row->billing_address_line_1 = $billing_row->billing_address_line_1;
					$row->billing_address_line_2 = $billing_row->billing_address_line_2;
					$row->billing_city = $billing_row->billing_city;
					$row->billing_state = $billing_row->billing_state;
					$row->billing_zip = $billing_row->billing_zip;
					$row->billing_country = $billing_row->billing_country;
					$row->billing_phone = $billing_row->billing_phone;
				}
				
				//query for the shipping address
				if( $shipping_address_id != 0 ){
					$shippingquery = "SELECT ec_address.first_name AS shipping_first_name, ec_address.last_name AS shipping_last_name, ec_address.company_name AS shipping_company_name, ec_address.address_line_1 AS shipping_address_line_1, ec_address.address_line_2 AS shipping_address_line_2, ec_address.city AS shipping_city, ec_address.state AS shipping_state, ec_address.zip AS shipping_zip, ec_address.country AS shipping_country, ec_address.phone AS shipping_phone FROM ec_address WHERE ec_address.address_id = %s";
					$shipping_row = $this->db->get_row( $this->db->prepare( $shippingquery, $shipping_address_id ) );
				  	$row->shipping_first_name = $shipping_row->shipping_first_name;
					$row->shipping_last_name = $shipping_row->shipping_last_name;
					$row->shipping_company_name = $shipping_row->shipping_company_name;
					$row->shipping_address_line_1 = $shipping_row->shipping_address_line_1;
					$row->shipping_address_line_2 = $shipping_row->shipping_address_line_2;
					$row->shipping_city = $shipping_row->shipping_city;
					$row->shipping_state = $shipping_row->shipping_state;
					$row->shipping_zip = $shipping_row->shipping_zip;
					$row->shipping_country = $shipping_row->shipping_country;
					$row->shipping_phone = $shipping_row->shipping_phone;
				}
			  
				//attach the total rows to the first query
				$row->totalrows = $totalrows;
				$returnArray[] = $row;
			}
			return $returnArray;
		}else{
			return array( "noresults" );
		}
		
	}
	
	function deleteclient( $clientid ){
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_user.* FROM ec_user WHERE ec_user.user_level = 'admin' AND ec_user.user_id != %d";
		
		$results = $this->db->get_results( $this->db->prepare( $sql, $clientid ) );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( $totalrows > 0) {
			
			$success1 = $this->db->query( $this->db->prepare( "DELETE FROM ec_address WHERE ec_address.user_id = %d", $clientid ) );
			$success2 = $this->db->query( $this->db->prepare( "DELETE FROM ec_user WHERE ec_user.user_id = %d", $clientid ) );
			
			do_action( 'wpeasycart_account_deleted', $clientid );
			
			if( $success1 && $success2 ){
				return array( "success" );
			}else{
				return array( "error" );
			}
		
		}else{
			return array( "noadminerror" );
		}
		
	}
	
	function updateclient($clientid, $client) {
		 
		$results = $this->db->get_results( "SELECT ec_user.* FROM ec_user WHERE ec_user.user_level = 'admin'" );
		$totalrows = count( $results );
		
		// Prevent changing user level of last admin to a non admin!!
		$matchlastadmin = false;
		foreach( $results as $row ){
			if( $totalrows == 1 && $clientid == $row->user_id )
				$matchlastadmin = true;
		}
		
		if( $matchlastadmin == true && $client->userlevel != 'admin' ){
			return array( "noadminerror" );
		
		}else{
			
			if( $client->billing_id == 0 ){
				
				// Insert Billing Address
				$sql = "INSERT INTO ec_address( user_id, first_name, last_name,  company_name, address_line_1, address_line_2, city, state, zip, country, phone ) VALUES( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )";
				$success1 = $this->db->query( $this->db->prepare( $sql, $clientid, $client->billname, $client->billlastname, $client->billcompany, $client->billaddress, $client->billaddress2, $client->billcity, $client->billstate, $client->billzip, $client->billcountry, $client->billphone ) );
				$billing_id = $this->db->insert_id;
			
			}else{
				
				// Get existings address with ID, data validation check
				$address_exists = $this->db->get_results( $this->db->prepare( "SELECT ec_address.address_id FROM ec_address WHERE ec_address.address_id = %d", $client->billing_id ) );
				
				if( count( $address_exists ) <= 0 ){
					
					// Insert New Address
					$sql = "INSERT INTO ec_address( user_id, first_name, last_name,  company_name, address_line_1, address_line_2, city, state, zip, country, phone ) VALUES( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )";
					$success1 = $this->db->query( $this->db->prepare( $sql, $clientid, $client->billname, $client->billlastname, $client->billcompany, $client->billaddress, $client->billaddress2, $client->billcity, $client->billstate, $client->billzip, $client->billcountry, $client->billphone ) );
					$billing_id = $this->db->insert_id;
				
				}else{
					
					// Update Addresses
					$sql = "UPDATE ec_address SET ec_address.first_name = %s, ec_address.last_name = %s, ec_address.company_name = %s,  ec_address.address_line_1 = %s, ec_address.address_line_2 = %s, ec_address.city = %s, ec_address.state = %s, ec_address.zip = %s, ec_address.country = %s, ec_address.phone = %s WHERE ec_address.address_id = %d";
				
					$success1 = $this->db->query( $this->db->prepare( $sql, $client->billname, $client->billlastname, $client->billcompany, $client->billaddress, $client->billaddress2, $client->billcity, $client->billstate, $client->billzip, $client->billcountry, $client->billphone, $client->billing_id) );
					$billing_id = $client->billing_id;
				
				}
			
			}
			
			if( $client->shipping_id  == 0 ){
				
				// Insert Shipping Address
				$sql = "INSERT INTO ec_address( user_id, first_name, last_name,  company_name, address_line_1, address_line_2, city, state, zip, country, phone ) VALUES( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )";
				$success2 = $this->db->query( $this->db->prepare( $sql, $clientid, $client->shipname, $client->shiplastname, $client->shipcompany, $client->shipaddress, $client->shipaddress2, $client->shipcity, $client->shipstate, $client->shipzip, $client->shipcountry, $client->shipphone ) );
				$shipping_id = $this->db->insert_id;
			
			}else{
				
				// Get existings address with ID, data validation check
				$address_exists = $this->db->get_results( $this->db->prepare( "SELECT ec_address.address_id FROM ec_address WHERE ec_address.address_id = %d", $client->shipping_id ) );
				
				if( count( $address_exists ) <= 0 ){
					
					// Insert Shipping Address
					$sql = "INSERT INTO ec_address( user_id, first_name, last_name,  company_name, address_line_1, address_line_2, city, state, zip, country, phone ) VALUES( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )";
					$success2 = $this->db->query( $this->db->prepare( $sql, $clientid, $client->shipname, $client->shiplastname, $client->shipcompany, $client->shipaddress, $client->shipaddress2, $client->shipcity, $client->shipstate, $client->shipzip, $client->shipcountry, $client->shipphone ) );
					$shipping_id = $this->db->insert_id;
				
				}else{
					
					// Update Addresses
					$sql = "UPDATE ec_address SET ec_address.first_name = %s, ec_address.last_name = %s, ec_address.company_name = %s,  ec_address.address_line_1 = %s, ec_address.address_line_2 = %s, ec_address.city = %s, ec_address.state = %s, ec_address.zip = %s, ec_address.country = %s, ec_address.phone = %s WHERE ec_address.address_id = %d";
					$success2 = $this->db->query( $this->db->prepare( $sql, $client->shipname, $client->shiplastname, $client->shipcompany, $client->shipaddress, $client->shipaddress2, $client->shipcity, $client->shipstate, $client->shipzip, $client->shipcountry, $client->shipphone, $client->shipping_id ) );
					$shipping_id = $client->shipping_id;
				
				}
			
			}
			
			// Update User
			$sql = "UPDATE ec_user SET ec_user.email = %s, ec_user.password = %s, ec_user.first_name = %s, ec_user.vat_registration_number = %s, ec_user.last_name = %s, ec_user.default_billing_address_id = %d, ec_user.default_shipping_address_id = %d, ec_user.user_level = %s, ec_user.is_subscriber = %d, ec_user.exclude_tax = %s, ec_user.exclude_shipping = %s, ec_user.user_notes = %s WHERE ec_user.user_id = %d";
			$success3 = $this->db->query( $this->db->prepare( $sql, $client->email, $client->password, $client->firstname, $client->vat_registration, $client->lastname, $billing_id, $shipping_id, $client->userlevel, $client->subscriber, $client->exclude_tax, $client->exclude_shipping, $client->usernotes, $clientid ) );
			
			//Enqueue Quickbooks Update Customer
			if( class_exists( 'ec_quickbooks' ) ){
				$quickbooks = new ec_quickbooks( );
				$quickbooks->update_user_admin( $clientid );	
			}	
			
			do_action( 'wpeasycart_account_updated', $clientid );			
			
			if( $success1 === FALSE ){
				return array( "error" );
			}else if( $success2 === FALSE ){
				return array( "error" );
			}else if( $success3 === FALSE ){
				return array( "error" );
			}else{
				return array( "success" );
			}
		}
	}
	
	function addclient( $client ){
		
		// Insert User
		$sql = "INSERT INTO ec_user( email, password, first_name, last_name, vat_registration_number, default_billing_address_id, default_shipping_address_id, user_level, is_subscriber, exclude_tax, exclude_shipping, user_notes ) VALUES( %s, %s, %s,  %s, %s, 0, 0, %s, %s, %s, %s, %s )";
		$success1 = $this->db->query( $this->db->prepare( $sql, $client->email, $client->password, $client->firstname, $client->lastname, $client->vat_registration, $client->userlevel, $client->subscriber, $client->exclude_tax, $client->exclude_shipping, $client->usernotes ) );
		$user_id = $this->db->insert_id;
		
		// Insert Billing Address
		$sql = "INSERT INTO ec_address( user_id, first_name, last_name,  company_name, address_line_1, address_line_2, city, state, zip, country, phone ) VALUES( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )";
		$success2 = $this->db->query( $this->db->prepare( $sql, $user_id, $client->billname, $client->billlastname, $client->billcompany, $client->billaddress, $client->billaddress2, $client->billcity, $client->billstate, $client->billzip, $client->billcountry, $client->billphone ) );
		$billing_id = $this->db->insert_id;
		
		// Insert Shipping Address
		$success3 = $this->db->query( $this->db->prepare( $sql, $user_id, $client->shipname, $client->shiplastname,  $client->shipcompany, $client->shipaddress, $client->shipaddress2, $client->shipcity, $client->shipstate, $client->shipzip, $client->shipcountry, $client->shipphone ) );
		$shipping_id = $this->db->insert_id;
		
		// Update User for Addresses
		$sql = "UPDATE ec_user SET ec_user.default_billing_address_id = %d, ec_user.default_shipping_address_id = %d WHERE ec_user.user_id = %d";
		$success4 = $this->db->query( $this->db->prepare( $sql, $billing_id, $shipping_id, $user_id ) );
		
		// MyMail Hook
		if( function_exists( 'mailster' ) ){
			mailster('subscribers')->add(array(
				'firstname' => $client->billname,
				'lastname' => $client->billlastname,
				'email' => $client->email,
				'status' => 1,
			), false );
		}
		
		//Enqueue Quickbooks Add Customer
		if( class_exists( 'ec_quickbooks' ) ){
			$quickbooks = new ec_quickbooks( );
			$quickbooks->add_user( $user_id );
		}
			
		do_action( 'wpeasycart_account_added', $user_id, $client->email, $client->password );
		
		if( $success1 === FALSE ){
			return array( "error" );
		}else if( $success2 === FALSE ){
			return array( "error" );
		}else if( $success3 === FALSE ){
			return array( "error" );
		}else if( $success4 === FALSE ){
			return array( "error" );
		}else{
			return array( "success" );
		}
		
	}
	
	function getuserroles( ){
		
		$sql = "SELECT ec_role.* FROM ec_role ORDER BY ec_role.role_id ASC";
		$results = $this->db->get_results( $sql );
		$returnArray = array( );
		
		if( $results ){
			foreach( $results as $row ){
				$returnArray[] = $row;
			}
			return $returnArray;
		}else{
			  return array( "noresults" );
		}
	
	}
	
	function deleteuserrole( $role_id ){
		
		$sql = "DELETE FROM ec_role WHERE ec_role.role_id = %d";
		$success = $this->db->query( $this->db->prepare( $sql, $role_id ) );
		
		if( $success ){
			return array( "success" );
		}else{
			return array( "error" );
		}
		
	}
	
	function adduserrole( $role_label, $admin_access ){
		
		if( $admin_access == true )
			$adminok = 1;
		else
			$adminok = 0;
			
		$sql = "INSERT INTO ec_role( role_label, admin_access ) VALUES( %s, %s )";
		$success = $this->db->query( $this->db->prepare( $sql, $role_label, $adminok ) );
		
		if( $success )
			return array( "success" );
		else
			return array( "error" );
		
	}
	
	function getuserroleprices( $product_id ){
		
		$sql = "SELECT ec_roleprice.* FROM ec_roleprice WHERE ec_roleprice.product_id = %d ORDER BY ec_roleprice.role_label ASC";
		$results = $this->db->get_results( $this->db->prepare( $sql, $product_id ) );
		
		if( $results ){
			$returnArray = array( );
			foreach( $results as $row ){
				$returnArray[] = $row;
			}
			return($returnArray);
		}else{
			return array( "noresults" );
		}
	
	}
	
	function deleteuserroleprice( $roleprice_id, $product_id ){
		
		$sql = "DELETE FROM ec_roleprice WHERE ec_roleprice.roleprice_id = %d";
		$success = $this->db->query( $this->db->prepare( $sql, $roleprice_id ) );
		
		if( $success ){
			return array( $product_id );
		
		}else{
			return array( "error" );
		}
		
	}
	
	function adduserroleprice($product_id, $role_label, $price) {
		
		//Create SQL Query
		$sql = "INSERT INTO ec_roleprice( product_id, role_label, role_price ) VALUES( %d, %s, %s)";
		$success = $this->db->query( $this->db->prepare( $sql, $product_id, $role_label, $price ) );
		
		if( $success ){
			return array( $product_id );
		
		}else{
			return array( "error" );
		
		}
	
	}
	
	function resendverificationemail( $clientid, $email ){
		$account = new ec_accountpage;
		$account->send_validation_email( $email );
	}
	
	function getsecuritylevel( $role_label ){
		
		$sql = "SELECT ec_roleaccess.* FROM ec_roleaccess WHERE ec_roleaccess.role_label = %s";
		$results = $this->db->get_results( $this->db->prepare( $sql, $role_label ) );
		
		if( $results && count( $results ) > 0 ){
			$returnArray = array( );
			foreach( $results as $row ){
				$returnArray[] = $row;
			}
			return $returnArray; //return array results if there are some
		
		}else if( $results ){
			return array( "noresults" );
		}else{
			return array( "error" );
		}
		
	}
	
	function savesecuritylevel( $role_label, $role_access ){
		 
		$sql = "DELETE FROM ec_roleaccess WHERE ec_roleaccess.role_label = %s";
		$success_delete = $this->db->query( $this->db->prepare( $sql, $role_label ) );
		
		if( $success_delete === FALSE ){
			
			return array( "error" );
			
		}else{
			
			// Insert Role
			foreach( $role_access as $key=>$value ){
				$sql = "INSERT INTO ec_roleaccess( role_label, admin_panel ) VALUES( %s, %s)";
				$success_insert = $this->db->query( $this->db->prepare( $sql, $role_label, $value ) );
			}
			
			if( $success_insert ){
				$sql = "SELECT ec_roleaccess.* FROM ec_roleaccess WHERE ec_roleaccess.role_label = %s";
				$results = $this->db->get_results( $this->db->prepare( $sql, $role_label ) );
				
				if( $results && count( $results ) > 0 ){
					$returnArray = array( );
					foreach( $results as $row ){
						$returnArray[] = $row;
					}
					return $returnArray;
				
				}else if( $results ){
					return array( "noresults" );
				}
			}
		}
		
		return array( "error" );
	}

}
?>