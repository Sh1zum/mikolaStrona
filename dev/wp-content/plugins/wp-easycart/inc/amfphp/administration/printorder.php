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

if( get_option( 'ec_option_amfphp_fix' ) ){
	ob_end_clean( );
}

global $wpdb;

$requestID = "-1";
if( isset( $_GET['reqID'] ) )
	$requestID = $_GET['reqID'];

$user_sql = "SELECT  ec_user.*, ec_role.admin_access FROM ec_user LEFT JOIN ec_role ON (ec_user.user_level = ec_role.role_label) WHERE ec_user.password = %s AND  (ec_user.user_level = 'admin' OR ec_role.admin_access = 1)";
$users = $wpdb->get_results( $wpdb->prepare( $user_sql, $requestID ) );

if( !empty( $users ) ){

	$order_id = 0;
	if( isset( $_GET["OrderID"] ) )
		$order_id = $_GET["OrderID"];
	
	$db = new ec_db_admin( );
	$mysqli = new ec_db_admin( );
	$order = $db->get_order_row_admin( $order_id );
	$order_details = $db->get_order_details_admin( $order_id );
	
	$country_list = $db->get_countries( );
	
	$total = $GLOBALS['currency']->get_currency_display( $order->grand_total );
	$subtotal = $GLOBALS['currency']->get_currency_display( $order->sub_total );
	$tax = $GLOBALS['currency']->get_currency_display( $order->tax_total );
	if( $order->duty_total > 0 ){ $has_duty = true; }else{ $has_duty = false; }
	$duty = $GLOBALS['currency']->get_currency_display( $order->duty_total );
	$vat = $GLOBALS['currency']->get_currency_display( $order->vat_total );
	$vat_rate = number_format( $order->vat_rate, 0, '', '' );
	$shipping = $GLOBALS['currency']->get_currency_display( $order->shipping_total );
	$discount = $GLOBALS['currency']->get_currency_display( $order->discount_total );
	$gst_total = $GLOBALS['currency']->get_currency_display( $order->gst_total );
	$pst_total = $GLOBALS['currency']->get_currency_display( $order->pst_total );
	$hst_total = $GLOBALS['currency']->get_currency_display( $order->hst_total );
	$gst_rate = $order->gst_rate ;
	$pst_rate = $order->pst_rate ;
	$hst_rate = $order->hst_rate ;
	
	$email_logo_url = get_option( 'ec_option_email_logo' );

	// Get receipt
	if( $order->subscription_id ){
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_account_subscription_print_receipt.php' ) )
			include WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_account_subscription_print_receipt.php';
		else
			include WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_account_subscription_print_receipt.php';
	}else{
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_admin_packaging_slip.php' ) )
			include WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_admin_packaging_slip.php';
		else
			include WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_admin_packaging_slip.php';
	}

}else{

	echo "Not Authorized...";

}
?>