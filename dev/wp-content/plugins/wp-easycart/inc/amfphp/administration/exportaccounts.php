<?php 
/*
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//All Code and Design is copyrighted by Level Four Development, llc
//
//Level Four Development, LLC provides this code "as is" without warranty of any kind, either express or implied,     
//including but not limited to the implied warranties of merchantability and/or fitness for a particular purpose.         
//
//Only licensed users may use this code and storfront for live purposes. All other use is prohibited and may be 
//subject to copyright violation laws. If you have any questions regarding proper use of this code, please
//contact Level Four Development, llc and EasyCart prior to use.
//
//All use of this storefront is subject to our terms of agreement found on Level Four Development, llc's  website.
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/

//load our connection settings
ob_get_clean( );
ob_start( NULL, 4096 );

require_once( '../../../../../../wp-load.php' );

ob_end_clean( );

global $wpdb;

$requestID = "-1";
if( isset( $_GET['reqID'] ) )
	$requestID = $_GET['reqID'];

$user_sql = "SELECT  ec_user.*, ec_role.admin_access FROM ec_user LEFT JOIN ec_role ON (ec_user.user_level = ec_role.role_label) WHERE ec_user.password = %s AND  (ec_user.user_level = 'admin' OR ec_role.admin_access = 1)";
$users = $wpdb->get_results( $wpdb->prepare( $user_sql, $requestID ) );

if( !empty( $users ) ){
	
	$header = "";
	$data = "";
	$sql = "SELECT 
			  ec_user.user_id,
			  ec_user.email,
			  ec_user.password,
			  ec_user.list_id,
			  ec_user.edit_sequence,
			  ec_user.quickbooks_status,
			  ec_user.first_name,
			  ec_user.last_name,
			  ec_user.default_billing_address_id,
			  ec_user.default_shipping_address_id,
			  ec_user.user_level,
			  ec_user.is_subscriber,
			  ec_user.realauth_registered,
			  ec_user.stripe_customer_id,
			  ec_user.default_card_type,
			  ec_user.default_card_last4,
			  ec_user.exclude_tax,
			  ec_user.exclude_shipping,
			  ec_user.user_notes,
			  ec_user.vat_registration_number,
			  ec_address.address_id as billing_address_id,
			  ec_address.user_id as billing_user_id,
			  ec_address.first_name as billing_first_name,
			  ec_address.last_name as billing_last_name,
			  ec_address.address_line_1 as billing_address_line_1,
			  ec_address.address_line_2 as billing_address_line_2,
			  ec_address.city as billing_city,
			  ec_address.state as billing_state,
			  ec_address.zip as billing_zip,
			  ec_address.country as billing_country,
			  ec_address.phone as billing_phone,
			  ec_address.company_name as billing_company_name,
			  ec_address1.address_id as shipping_address_id,
			  ec_address1.user_id as shipping_user_id,
			  ec_address1.first_name as shipping_first_name,
			  ec_address1.last_name as shipping_last_name,
			  ec_address1.address_line_1 as shipping_address_line_1,
			  ec_address1.address_line_2 as shipping_address_line_2,
			  ec_address1.city as shipping_city,
			  ec_address1.state as shipping_state,
			  ec_address1.zip as shipping_zip,
			  ec_address1.country as shipping_country,
			  ec_address1.phone as shipping_phone,
			  ec_address1.company_name as shipping_company_name
			FROM
			  ec_user
			  LEFT JOIN ec_address ON (ec_user.default_billing_address_id = ec_address.address_id)
			  LEFT JOIN ec_address ec_address1 ON (ec_user.default_shipping_address_id = ec_address1.address_id)
			ORDER BY
			  ec_user.user_id ";
	$results = $wpdb->get_results( $sql, ARRAY_A );

	$keys = array_keys( $results[0] );
	foreach( $keys as $key ){
		$header .= $key."\t";
	}

	foreach( $results as $result ){

		$line = '';
		foreach( $result as $value ){

			if( !isset( $value ) || $value == "" ){
				$value = "\t";

			}else{
				$value = str_replace( '"', '""', $value);
				$value = '"' . utf8_decode($value) . '"' . "\t";

			}

			$line .= $value;

		}

		$data .= trim( $line )."\n";

	}
	
	$data = str_replace( "\r", "", $data );

	if( $data == "" ){
		$data = "\nno matching records found\n";
	}
	
	ob_end_flush( );
	header("Content-type: application/vnd.ms-excel");
	header("Content-Transfer-Encoding: binary"); 
	header("Content-Disposition: attachment; filename=accounts.xls");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo $header."\n".$data; 

}else{

	echo "Not Authorized...";

}

?>