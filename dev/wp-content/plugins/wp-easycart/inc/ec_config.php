<?php
error_reporting(0);

// Start up the Amazon S3 Loader
if( get_option( 'ec_option_amazon_bucket' ) != "" && version_compare( phpversion( ), "5.3" ) >= 0 ){
	require WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/aws/aws-autoloader.php';
}

// Session Handler Class
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/wpeasycart_session.php' );
	
// Language Translation Check
if( isset( $_POST['ec_language_conversion'] ) ){
	setcookie( "ec_translate_to", "", time( ) - 300, "/" ); 
	setcookie( 'ec_translate_to', htmlspecialchars( $_POST['ec_language_conversion'], ENT_QUOTES ), time( ) + ( 3600 * 24 * 30 ), "/" );
	header('Location: '.$_SERVER['REQUEST_URI']);
	die;

}else if( isset( $_GET['eclang'] ) ){
	setcookie( "ec_translate_to", "", time( ) - 300, "/" ); 
	setcookie( 'ec_translate_to', htmlspecialchars( $_GET['eclang'], ENT_QUOTES ), time( ) + ( 3600 * 24 * 30 ), "/" );
	header( 'Location: ' . strtok( $_SERVER['REQUEST_URI'], '?' ) );
	die;
}
	
// Currency Conversion Check
if( isset( $_POST['ec_currency_conversion'] ) ){
	setcookie( "ec_convert_to", "", time( ) - 300, "/" ); 
	setcookie( 'ec_convert_to', htmlspecialchars( $_POST['ec_currency_conversion'], ENT_QUOTES ), time( ) + ( 3600 * 24 * 30 ), "/" );
	header('Location: '.$_SERVER['REQUEST_URI']);
	die;
	
}else if( isset( $_GET['eccurrency'] ) ){
	setcookie( "ec_convert_to", "", time( ) - 300, "/" ); 
	setcookie( 'ec_convert_to', htmlspecialchars( $_GET['eccurrency'], ENT_QUOTES ), time( ) + ( 3600 * 24 * 30 ), "/" );
	header( 'Location: ' . preg_replace( "/eccurrency\=[a-zA-Z]+/m", "", $_SERVER['REQUEST_URI'] ) );
	die;
	
}

// LIVE GATEWAY CLASSES
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_gateway.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_square.php' );

if( get_option( 'ec_option_payment_process_method' ) != '0' || get_option( 'ec_option_use_affirm' ) ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_3ds.php' );
}

if( get_option( 'ec_option_use_affirm' ) ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_affirm.php' );
}

if( get_option( 'ec_option_payment_process_method' ) == 'authorize' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_authorize.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'beanstream' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_beanstream.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'braintree' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_braintree.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'cardpointe' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_cardpointe.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'chronopay' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_chronopay.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'eway' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_eway.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'firstdata' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_firstdata.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'goemerchant' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_goemerchant.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'intuit' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/oAuthSimple.php' );
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_intuit.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'migs' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_migs.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'moneris_ca' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_moneris_ca.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'moneris_us' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_moneris_us.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'nmi' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_cardinal.php' );
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_nmi.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'payline' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_payline.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'paymentexpress' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_paymentexpress.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'paypal_payments_pro' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_paypal_payments_pro.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'paypal_pro' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_paypal_pro.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'paypoint' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_paypoint.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'realex' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_realex.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'sagepay' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_sagepay.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'sagepayus' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_sagepayus.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'securenet' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_securenet.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'securepay' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_securepay.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'stripe' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_stripe.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'stripe_connect' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_stripe_connect.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'virtualmerchant' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_virtualmerchant.php' );
}else if( get_option( 'ec_option_payment_process_method' ) == 'custom' && file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/ec_customgateway.php' ) ){
	include( WP_PLUGIN_DIR . '/wp-easycart-data/ec_customgateway.php' );
}

// THIRD PARTY GATEWAYS
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_third_party.php' );

if( get_option( 'ec_option_payment_third_party' ) == '2checkout_thirdparty' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_2checkout_thirdparty.php' );
}else if( get_option( 'ec_option_payment_third_party' ) == 'cashfree' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_cashfree.php' );
}else if( get_option( 'ec_option_payment_third_party' ) == 'dwolla_thirdparty' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_dwolla_thirdparty.php' );
}else if( get_option( 'ec_option_payment_third_party' ) == 'payfast_thirdparty' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_payfast_thirdparty.php' );
}else if( get_option( 'ec_option_payment_third_party' ) == 'payfort' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_payfort.php' );
}else if( get_option( 'ec_option_payment_third_party' ) == 'paymentexpress_thirdparty' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_paymentexpress_thirdparty.php' );
}else if( get_option( 'ec_option_payment_third_party' ) == 'paypal' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_paypal.php' );
	include_once( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_ipnlistener.php' );
}else if( get_option( 'ec_option_payment_third_party' ) == 'sagepay_paynow_za' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_sagepay_paynow_za.php' );
}else if( get_option( 'ec_option_payment_third_party' ) == 'paypal_advanced' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_paypal_advanced.php' );
}else if( get_option( 'ec_option_payment_third_party' ) == 'nets' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_nets.php' );
}else if( get_option( 'ec_option_payment_third_party' ) == 'realex_thirdparty' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_realex_thirdparty.php' );
}else if( get_option( 'ec_option_payment_third_party' ) == 'redsys' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/Tpv.php' );
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_redsys.php' );
}else if( get_option( 'ec_option_payment_third_party' ) == 'skrill' ){
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_skrill.php' );
}else if( get_option( 'ec_option_payment_third_party' ) == 'custom_thirdparty' && file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/ec_custom_thirdparty.php' ) ){
	include( WP_PLUGIN_DIR . '/wp-easycart-data/ec_custom_thirdparty.php' );
}else{
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/gateway/ec_paypal.php' );
}

// INCLUDE SHIPPER CLASSES
$use_auspost = false; $use_dhl = false; $use_fedex = false; $use_ups = false; $use_usps = false; $use_canadapost = false;
if( get_option( 'ec_option_is_installed' ) ){
	global $wpdb;
	$rates = wp_cache_get( 'wpeasycart-config-get-rates', 'wpeasycart-shipping' );
	if( !$rates ){
		$rates = $wpdb->get_results( "SELECT shippingrate_id, is_ups_based, is_usps_based, is_fedex_based, is_auspost_based, is_dhl_based, is_canadapost_based FROM ec_shippingrate" );
		wp_cache_set( 'wpeasycart-config-get-rates', $rates, 'wpeasycart-shipping' );
	}
	$shipping_method = wp_cache_get( 'wpeasycart-config-get-shipping-method', 'wpeasycart-settings' );
	if( !$shipping_method ){
		$shipping_method = $wpdb->get_var( "SELECT shipping_method FROM ec_setting WHERE setting_id = 1" );
		wp_cache_set( 'wpeasycart-config-get-shipping-method', $shipping_method, 'wpeasycart-settings' );
	}
}else{
	$rates = array( );
	$shipping_method = "";
}

foreach( $rates as $rate ){
	if( $rate->is_auspost_based )
		$use_auspost = true;
	else if( $rate->is_dhl_based )
		$use_dhl = true;
	else if( $rate->is_fedex_based )
		$use_fedex = true;
	else if( $rate->is_ups_based )
		$use_ups = true;
	else if( $rate->is_usps_based )
		$use_usps = true;
	else if( $rate->is_canadapost_based )
		$use_canadapost = true;
}

if( ( $shipping_method == 'live' && $use_auspost ) || is_admin( ) )
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_auspost.php' );
if( ( $shipping_method == 'live' && $use_dhl ) || is_admin( ) )
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_dhl.php' );
if( ( $shipping_method == 'live' && $use_fedex ) || is_admin( ) )
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_fedex.php' );
if( $shipping_method == 'fraktjakt' )
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_fraktjakt.php' );
if( $shipping_method == 'live' )
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_live_shipping.php' );
if( $shipping_method == 'live' )
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_shipper.php' );
if( ( $shipping_method == 'live' && $use_ups ) || is_admin( ) )
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_ups.php' );
if( ( $shipping_method == 'live' && $use_usps ) || is_admin( ) )
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_usps.php' );
if( ( $shipping_method == 'live' && $use_canadapost ) || is_admin( ) )
	include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/shipping/ec_canadapost.php' );

// INCLUDE CORE CLASSES
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_address.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_advanced_optionsets.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_categories.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_category.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_categorylist.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_countries.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_coupons.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_credit_card.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_customer_reviews.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_currency.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_googleanalytics.php' ); 
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_db.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_db_admin.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_db_manager.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_discount.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_language.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_license.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_manufacturer.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_manufacturers.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_menu.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_menuitem.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_notifications.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_optionimage.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_optionitem.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_options.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_optionset.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_order.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_order_totals.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_page_options.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_payment.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_perpages.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_pricepoints.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_pricetiers.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_product.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_product_filter.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_productlist.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_products.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_promotion.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_promotion_item.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_promotions.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_rating.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_review.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_roleprices.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_scriptaction.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_selectedoptions.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_setting.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_shipping.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_subscription.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_tax.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_taxcloud.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_user.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_validation.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_wpoption.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_wpoptionset.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/ec_wpstyle.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/wpeasycart_cache_management.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/core/wpeasycart_mailer.php' );

// INCLUDE ACCOUNT CLASSES
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/account/ec_accountpage.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/account/ec_orderdetail.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/account/ec_orderdisplay.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/account/ec_orderlist.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/account/ec_subscription_list.php' );

// INCLUDE CART CLASSES
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/cart/ec_cart.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/cart/ec_cart_data.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/cart/ec_cartitem.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/cart/ec_cartpage.php' );

// INCLUDE STORE CLASSES
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/store/ec_featuredproducts.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/store/ec_filter.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/store/ec_giftcard.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/store/ec_paging.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/store/ec_perpage.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/store/ec_prodimages.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/store/ec_prodimageset.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/store/ec_prodmenu.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/store/ec_prodoptions.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/store/ec_social_media.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/store/ec_storepage.php' );

//INCLUDE WIDGET CLASSES
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_breadcrumbwidget.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_cartwidget.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_categorywidget.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_colorwidget.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_currencywidget.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_donationwidget.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_groupwidget.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_languagewidget.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_loginwidget.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_manufacturerwidget.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_menuwidget.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_newsletterwidget.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_pricepointwidget.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_productwidget.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_searchwidget.php' );
include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/classes/widget/ec_specialswidget.php' );

if( get_option( 'ec_option_is_installed' ) && !defined( 'WPEASYCART_ACCESSING_AMFPHP' ) ){
	
	$GLOBALS['ec_cart_data'] = new ec_cart_data( ( ( isset( $GLOBALS['ec_cart_id'] ) ) ? $GLOBALS['ec_cart_id'] : 'not-set' ) );
	$GLOBALS['ec_cart_data']->restore_session_from_db( );
	
	$GLOBALS['ec_advanced_optionsets'] = new ec_advanced_optionsets( );
	$GLOBALS['ec_categories'] = new ec_categories( );
	$GLOBALS['ec_countries'] = new ec_countries( );
	$GLOBALS['ec_coupons'] = new ec_coupons( );
    if( !is_admin( ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ){
	   $GLOBALS['ec_customer_reviews'] = new ec_customer_reviews( );
    }
    $GLOBALS['ec_manufacturers'] = new ec_manufacturers( );
	$GLOBALS['ec_menu'] = new ec_menu( );
	$GLOBALS['ec_notifications'] = new ec_notifications( );
	$GLOBALS['ec_options'] = new ec_options( );
	$GLOBALS['ec_perpages'] = new ec_perpages( );
	$GLOBALS['ec_pricepoints'] = new ec_pricepoints( );
	$GLOBALS['ec_pricetiers'] = new ec_pricetiers( );
	$GLOBALS['ec_products'] = new ec_products( );
	$GLOBALS['ec_promotions'] = new ec_promotions( );
	$GLOBALS['ec_roleprices'] = new ec_roleprices( );
	$GLOBALS['ec_setting'] = new ec_setting( );
	
	$GLOBALS['language'] = new ec_language( );
	$GLOBALS['currency'] = new ec_currency( );
	$GLOBALS['ec_user'] = new ec_user( "" );
	
	global $wpdb;
	
	$vat_included = wp_cache_get( 'wpeasycart-config-vat-included' );
	if( !$vat_included ){
		$vat_included = $wpdb->get_var( "SELECT ec_taxrate.vat_included FROM ec_taxrate WHERE ec_taxrate.vat_included = 1" );
		if( !$vat_included )
			$vat_included = "EMPTY";
		wp_cache_set( 'wpeasycart-config-vat-included', $vat_included );
	}
	if( $vat_included == "EMPTY" )
		$vat_included = false;
	$GLOBALS['ec_vat_included'] = $vat_included;
	
	$vat_added = wp_cache_get( 'wpeasycart-config-vat-added' );
	if( !$vat_added ){
		$vat_added = $wpdb->get_var( "SELECT ec_taxrate.vat_added FROM ec_taxrate WHERE ec_taxrate.vat_added = 1" );
		if( !$vat_added )
			$vat_added = "EMPTY";
		wp_cache_set( 'wpeasycart-config-vat-added', $vat_added );
	}
	if( $vat_added == "EMPTY" )
		$vat_added = false;
	$GLOBALS['ec_vat_added'] = $vat_added;
	
	do_action( 'wpeasycart_config_loaded' );
}

if( get_option( 'ec_option_is_installed' ) ){
	
	$GLOBALS['language'] = new ec_language( );
	$GLOBALS['currency'] = new ec_currency( );
	
}

add_action( 'init', 'wpeasycart_load_admin', 10 );
function wpeasycart_load_admin( ){
	if( current_user_can( 'manage_options' ) && is_admin( ) ){
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/admin-init.php' );
	}
}

?>