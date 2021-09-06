<?php
// First of all send css header
header("Content-type: text/javascript");

//Load Wordpress Connection Data
define('WP_USE_THEMES', false);
require('../../../../../wp-load.php');

$ec_store_js = array(
	'product',
	'product_filter_bar',
	'product_menu_filter',
	'product_page',
	'product_details_page',
	'customer_review',
	'featured_product',
	'gift_card_input'
);

$ec_cart_js = array(
	'cart_page',
	'cart',
	'cart_item',
	'cart_login',
	'cart_billing',
	'cart_shipping',
	'cart_shipping_method',
	'cart_coupon',
	'cart_gift_card',
	'cart_continue_to_payment',
	'cart_address_review',
	'cart_payment_information',
	'cart_contact_information',
	'cart_submit_order',
	'cart_subscription',
	'cart_success',
	'cart_third_party'
);

$ec_account_js = array(
	'account_billing_information',
	'account_dashboard',
	'account_login',
	'account_order_details',
	'account_orders',
	'account_page',
	'account_password',
	'account_personal_information',
	'account_shipping_information',
	'account_forgot_password',
	'account_register',
	'account_order_line',
	'account_order_details_item_display',
	'account_subscription_line',
	'account_subscription_list',
	'account_subscription_details'
);

$ec_widget_js = array(
	'menu_horizontal_widget',
	'menu_vertical_widget',
	'category_widget',
	'cart_widget',
	'donation_widget',
	'manufacturer_widget',
	'newsletter_widget',
	'price_widget',
	'product_widget',
	'search_widget',
);

$js_content = "";

foreach ($ec_store_js as $js_file) {
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' ) )
		$js_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' );
	else if( file_exists( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' ) )
		$js_content .= file_get_contents( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' );
}

foreach ($ec_cart_js as $js_file){
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' ) )
		$js_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' );
	else if( file_exists( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' ) )
		$js_content .= file_get_contents( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' );
}

foreach ($ec_account_js as $js_file) {
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' ) )
		$js_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' );
	else if( file_exists( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' ) )
		$js_content .= file_get_contents( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' );
}

foreach ($ec_widget_js as $js_file){
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' ) )
		$js_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' );
	else if( file_exists( WP_PLUGIN_DIR . '/wp-easycart/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' ) )
		$js_content .= file_get_contents( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $js_file . '/ec_' . $js_file . '.js' );
}

if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_validation.js' ) )
	$js_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_validation.js' );
else if( file_exists( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_validation.js' ) )
	$js_content .= file_get_contents( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_validation.js' );

// Replace some variables
$js_content = str_replace( '$GLOBALVAR_SWATCH_ERROR', $GLOBALS['language']->get_text( "quick_view", "quick_view_select_options" ), $js_content );

// print the js content
echo $js_content;	
?>
