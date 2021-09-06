<?php
// First of all send css header
header("Content-type: text/css");

//Load Wordpress Connection Data
define('WP_USE_THEMES', false);
require('../../../../../wp-load.php');

$css_content = "";

if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/css/ec-store-list.css' ) ){
	
	$css_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/css/ec-store-list.css' );
	
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/css/ec-store-details.css' ) )
		$css_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/css/ec-store-details.css' );
	
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/css/ec-cart.css' ) )
		$css_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/css/ec-cart.css' );
	
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/css/ec-account.css' ) )
		$css_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/css/ec-account.css' );
	
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/css/ec-widgets.css' ) )
		$css_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/css/ec-widgets.css' );
		
	if( get_option( 'ec_option_use_rtl' ) && file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/rtl_support.css' ) )
	$css_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/rtl_support.css' );
else if( get_option( 'ec_option_use_rtl' ) && file_exists( WP_PLUGIN_DIR . '/' . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/rtl_support.css' ) )
	$css_content .= file_get_contents( WP_PLUGIN_DIR . '/' . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/rtl_support.css' );

}else{

$ec_store_css = array(
	'product',
	'product_filter_bar',
	'product_menu_filter',
	'product_page',
	'product_details_page',
	'customer_review',
	'featured_product',
	'gift_card_input'
);

$ec_cart_css = array(
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

$ec_account_css = array(
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
	'account_subscriptions',
	'account_subscription_details'
);

$ec_widget_css = array(
	'menu_horizontal_widget',
	'menu_vertical_widget',
	'category_widget',
	'cart_widget',
	'donation_widget',
	'group_widget',
	'manufacturer_widget',
	'newsletter_widget',
	'price_widget',
	'product_widget',
	'search_widget',
	'specials_widget',
);

// Additional CSS used for the product (single and multiple) shortcode for display type 3
$css_content .= "
.ec_productlist_ul > li{
	margin-bottom:44px;
}
.ec_productlist_ul > li > a{
	float: left; width: 100%; text-align: center;
}
.ec_productlist_ul li a img{
	padding-bottom:15px;
}
.ec_productlist_ul > li > h3{ 
	margin-top:0px; font-weight: 300; font-size: 18px; padding-bottom: 21px; text-align:center;
} 
.ec_productlist_ul > li > h3 > a{ 
	text-decoration: none; color: #2e2e2e; float:none;
}
.ec_productlist_ul > li > .ec_price_button{
	display: inline-block; text-align:center;
}
.ec_productlist_ul > li > .ec_price_button .ec_price_before{
	padding: 8px;
	font-weight: bold;
	border: 1px solid #d5d6d3;
	border-right: 0;
	-webkit-border-top-left-radius: 10px;
	-webkit-border-bottom-left-radius: 10px;
	-moz-border-radius-topleft: 10px;
	-moz-border-radius-bottomleft: 10px;
	border-top-left-radius: 10px;
	border-bottom-left-radius: 10px;
	color: #1f1e1e;
	text-shadow: 1px 1px 1px #fff;
	-moz-box-shadow: 0 2px 2px 0px rgba( 0,0,0,0.1 );
	-webkit-box-shadow: 0 2px 2px 0px rgba( 0,0,0,0.1 );
	box-shadow: 0 2px 2px 0px rgba( 0,0,0,0.1 );
	background: #efefef;
	background: -moz-linear-gradient(top, #ffffff 0%, #efefef 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#efefef));
	background: -webkit-linear-gradient(top, #ffffff 0%,#efefef 100%);
	background: -o-linear-gradient(top, #ffffff 0%,#efefef 100%);
	background: -ms-linear-gradient(top, #ffffff 0%,#efefef 100%);
	background: linear-gradient(to bottom, #ffffff 0%,#efefef 100%);
	display: inline-block;
}
.ec_productlist_ul > li > .ec_price_button .ec_price_before del{
	text-decoration: line-through;
}
.ec_productlist_ul > li > .ec_price_button .ec_price_sale{
	border: 1px solid #aed23f;
	margin-left: -4px;
	-webkit-border-top-right-radius: 10px;
	-webkit-border-bottom-right-radius: 10px;
	-moz-border-radius-topright: 10px;
	-moz-border-radius-bottomright: 10px;
	border-top-right-radius: 10px;
	border-bottom-right-radius: 10px;
	color: #fff;
	text-shadow: 1px 1px 1px rgba( 0,0,0,0.3 );
	-moz-box-shadow: 0 2px 2px 0px rgba( 0,0,0,0.1 ), inset 0 1px 0 rgba( 255,255,255,0.6 );
	-webkit-box-shadow: 0 2px 2px 0px rgba( 0,0,0,0.1 ), inset 0 1px 0 rgba( 255,255,255,0.6 );
	box-shadow: 0 2px 2px 0px rgba( 0,0,0,0.1 ), inset 0 1px 0 rgba( 255,255,255,0.6 );
	background: #cff06b;
	background: -moz-linear-gradient(top, #cff06b 0%, #c0dd65 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#cff06b), color-stop(100%,#c0dd65));
	background: -webkit-linear-gradient(top, #cff06b 0%,#c0dd65 100%);
	background: -o-linear-gradient(top, #cff06b 0%,#c0dd65 100%);
	background: -ms-linear-gradient(top, #cff06b 0%,#c0dd65 100%);
	background: linear-gradient(to bottom, #cff06b 0%,#c0dd65 100%);
	padding: 8px;
	font-weight: bold;
	display: inline-block;
}

.ec_productlist_ul > li > .ec_price_button .ec_price{
	border: 1px solid #aed23f;
	margin-left: -4px;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
	color: #fff;
	text-shadow: 1px 1px 1px rgba( 0,0,0,0.3 );
	-moz-box-shadow: 0 2px 2px 0px rgba( 0,0,0,0.1 ), inset 0 1px 0 rgba( 255,255,255,0.6 );
	-webkit-box-shadow: 0 2px 2px 0px rgba( 0,0,0,0.1 ), inset 0 1px 0 rgba( 255,255,255,0.6 );
	box-shadow: 0 2px 2px 0px rgba( 0,0,0,0.1 ), inset 0 1px 0 rgba( 255,255,255,0.6 );
	background: #cff06b;
	background: -moz-linear-gradient(top, #cff06b 0%, #c0dd65 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#cff06b), color-stop(100%,#c0dd65));
	background: -webkit-linear-gradient(top, #cff06b 0%,#c0dd65 100%);
	background: -o-linear-gradient(top, #cff06b 0%,#c0dd65 100%);
	background: -ms-linear-gradient(top, #cff06b 0%,#c0dd65 100%);
	background: linear-gradient(to bottom, #cff06b 0%,#c0dd65 100%);
	padding: 8px;
	font-weight: bold;
	display: inline-block;
}
";

foreach ($ec_store_css as $css_file ){
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' ) )
		$css_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' );
	else if( file_exists( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' ) )
		$css_content .= file_get_contents( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' );
}

foreach ($ec_cart_css as $css_file ){
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' ) )
		$css_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' );
	else if( file_exists( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' ) )
		$css_content .= file_get_contents( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' );
}

foreach ($ec_account_css as $css_file ){ 
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' ) )
		$css_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' );
	else if( file_exists( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' ) )
		$css_content .= file_get_contents( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' );
}

foreach ($ec_widget_css as $css_file ){
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' ) )
		$css_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' );
	else if( file_exists( WP_PLUGIN_DIR . '/wp-easycart/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' ) )
		$css_content .= file_get_contents( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec_' . $css_file . '/ec_' . $css_file . '.css' );
}

if( get_option( 'ec_option_use_rtl' ) && file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/rtl_support.css' ) )
	$css_content .= file_get_contents( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/rtl_support.css' );
else if( get_option( 'ec_option_use_rtl' ) && file_exists( WP_PLUGIN_DIR . '/' . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/rtl_support.css' ) )
	$css_content .= file_get_contents( WP_PLUGIN_DIR . '/' . EC_PLUGIN_DIRECTORY . '/design/theme/' . get_option( 'ec_option_base_theme' ) . '/rtl_support.css' );

}//Close if/else new/old method

// Theme css replacement
$replacements_string = get_option( 'ec_option_css_replacements' );
$replacements_rows = explode( ",", $replacements_string );

foreach( $replacements_rows as $replacement_row ){
	$temp = explode( "=", $replacement_row );
	if( count( $temp ) == 2 )
		$css_content = str_replace( "[" . $temp[0] . "]", $temp[1], $css_content );
}

$font_families = get_option( 'ec_option_font_replacements' );
$font_rows = explode( ":::", $font_families );

foreach( $font_rows as $font_row ){
	$temp = explode( "=", $font_row );
	if( count( $temp ) == 2 )
		$css_content = str_replace( "[" . $temp[0] . "]", $temp[1], $css_content );
}

$screen_sizes = get_option( 'ec_option_responsive_sizes' );
$size_rows = explode( ":::", $screen_sizes );

foreach( $size_rows as $size_row ){
	$temp = explode( "=", $size_row );
	if( count( $temp ) == 2 )
		$css_content = str_replace( "[" . $temp[0] . "]", $temp[1], $css_content );
}

if( !get_option( 'ec_option_show_giftcards' ) && !get_option( 'ec_option_show_coupons' ) ){
	$css_content .= ".ec_cart_lower_left{ display:none; } ";
}else if( !get_option( 'ec_option_show_giftcards' ) ){
	$css_content .= ".ec_cart_gift_card_row{ display:none; } .ec_cart_gift_card_row_message{ display:none; } ";
}else if( !get_option( 'ec_option_show_coupons' ) ){
	$css_content .= ".ec_cart_coupon_row{ display:none; } .ec_cart_coupon_row_message{ display:none; } ";
}

$css_content .= ".filter_bar_bottom .ec_sort_menu{ display:none; }";

$css_content .= get_option( 'ec_option_custom_css' );

// Show store as a catalog
if( apply_filters( 'wp_easycart_catalog_display', get_option( 'ec_option_display_as_catalog' ) ) ){
	$css_content .= ".ec_product_details_add_to_cart_button{ display: none !important; }
		.ec_product_quick_view_box_content_option_row{ display:none !important; }
		.ec_product_details_quantity{ display:none !important; }
		.ec_product_quick_view_holder{ opacity:0 !important; filter:alpha(opacity=0) !important; }
		.ec_product_details_add_to_cart{ display:none !important; }
		.ec_product_details_quantity{ display:none !important; }";
}

// print the css content
echo $css_content;	
?>
