<?php
/**
 * Plugin Name: WP EasyCart
 * Plugin URI: http://www.wpeasycart.com
 * Description: The WordPress Shopping Cart by WP EasyCart is a simple eCommerce solution that installs into new or existing WordPress blogs. Customers purchase directly from your store! Get a full ecommerce platform in WordPress! Sell products, downloadable goods, gift cards, clothing and more! Now with WordPress, the powerful features are still very easy to administrate! If you have any questions, please view our website at <a href="http://www.wpeasycart.com" target="_blank">WP EasyCart</a>.
 
 * Version: 5.0.7
 * Author: WP EasyCart
 * Author URI: http://www.wpeasycart.com
 * Text Domain: wp-easycart
 * Domain Path: /languages
 *
 * This program is free to download and install and sell with PayPal. Although we offer a ton of FREE features, some of the more advanced features and payment options requires the purchase of our professional shopping cart admin plugin. Professional features include alternate third party gateways, live payment gateways, coupons, promotions, advanced product features, and much more!
 *
 * @package wpeasycart
 * @version  5.0.7
 * @author WP EasyCart <sales@wpeasycart.com>
 * @copyright Copyright (c) 2012, WP EasyCart
 * @link http://www.wpeasycart.com
 */
 
define( 'EC_PUGIN_NAME', 'WP EasyCart');
define( 'EC_PLUGIN_DIRECTORY', 'wp-easycart');
define( 'EC_CURRENT_VERSION', '5_0_7' );
define( 'EC_CURRENT_DB', '1_30' );/* Backwards Compatibility */
define( 'EC_UPGRADE_DB', '74' );

require_once( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/inc/ec_config.php' );

add_action( 'init', 'wpeasycart_load_startup', 1 );
add_action( 'plugins_loaded', 'wpeasycart_load_translation', 1 );
add_action( 'wp_head', 'wpeasycart_remove_yoast_og_store_posts', 1);
add_action( 'widgets_init', 'wpeasycart_register_widgets' );
add_filter( 'upload_mimes', 'wp_easycart_add_allow_uploads_admin', 1, 1 );

function wp_easycart_add_allow_uploads_admin( $mimes ){
    $mimes['csv'] = 'text/csv';
    $mimes['pdf'] = 'application/pdf';
    $mimes['zip'] = 'application/zip';
    $mimes['gzip'] = 'application/x-gzip';
    return $mimes;
}
    
function wpeasycart_load_translation( ){
    load_plugin_textdomain( 'wp-easycart', FALSE, '/' . EC_PLUGIN_DIRECTORY . '/languages' );
}

function wpeasycart_load_startup( ){
	
	// Setup Hook Structure
	ec_setup_hooks( );
	
	// Check and add hooks
	if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/ec_hooks.php" ) )
		include( WP_PLUGIN_DIR . "/wp-easycart-data/ec_hooks.php" );
		
	if( !is_admin( ) && get_option( 'ec_option_load_ssl' ) && !is_ssl( ) && ( !defined( 'WP_CLI' ) || !WP_CLI ) ){
		$redirect_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		wp_redirect( $redirect_url, 301 );
		exit;
	}
    
    if( version_compare( str_replace( '_', '.', EC_CURRENT_VERSION ), get_option( 'ec_option_db_version_updated' ), '<' ) ){
        $db_manager = new ec_db_manager( );
        $db_manager->try_db_update( );
    }
    
    do_action( 'wp_easycart_startup' );
}

function wpeasycart_remove_yoast_og_store_posts( ){
	if( get_post_type( ) == 'ec_store' && isset( $GLOBALS['wpseo_og'] ) ){
		remove_action( 'wpseo_head', array( $GLOBALS['wpseo_og'], 'opengraph' ), 30 );
	}
}

function wpeasycart_register_widgets( ) {
	register_widget( 'ec_categorywidget' );
	register_widget( 'ec_cartwidget' );
	register_widget( 'ec_colorwidget' );
	register_widget( 'ec_currencywidget' );
	register_widget( 'ec_donationwidget' );
	register_widget( 'ec_groupwidget' );
	register_widget( 'ec_languagewidget' );
	register_widget( 'ec_loginwidget' );
	register_widget( 'ec_manufacturerwidget' );
	register_widget( 'ec_menuwidget' );
	register_widget( 'ec_newsletterwidget' );
	register_widget( 'ec_pricepointwidget' );
	register_widget( 'ec_productwidget' );
	register_widget( 'ec_searchwidget' );
	register_widget( 'ec_specialswidget' );
}

function ec_activate( ){
	
	global $wpdb;
	
	// ADD WORDPRESS OPTIONS
	$wpoptions = new ec_wpoptionset();
	$wpoptions->add_options();
	update_option( 'ec_option_wpoptions_version', EC_CURRENT_VERSION );
	
	
	// ATTEMPT TO INSTALL OR UPDATE THE DB.
	if( !get_option( 'ec_option_db_new_version' ) || EC_UPGRADE_DB != get_option( 'ec_option_db_new_version' ) ){
		$db_manager = new ec_db_manager( );
		$db_manager->install_db( );
		update_option( 'ec_option_is_installed', '1' );
	}
	
	//INITIALIZE DATABASE
	$mysqli = new ec_db();
	
	// UPDATE SITE URL
	$site = explode( "://", ec_get_url( ) );
	$site = $site[1];
	$mysqli->update_url( $site );
	// END UPDATE SITE URL 
	
	//SETUP BASIC LANGUAGE SETTINGS
	$GLOBALS['ec_cart_data'] = new ec_cart_data( ( ( isset( $GLOBALS['ec_cart_id'] ) ) ? $GLOBALS['ec_cart_id'] : 'not-set' ) );
	$GLOBALS['ec_cart_data']->restore_session_from_db( );
	$language = new ec_language( );
	$language->update_language_data( ); //Do this to update the database if a new language is added
	
	//WE BLOCK THIS FROM THE ec_config.php TO PREVENT OUTPUT ON ACTIVATION, INCLUDE HERE...
	update_option( 'ec_option_is_installed', '1' );
	
	// FIX FOR CURRENCY ISSUES
	if( get_option( 'ec_option_currency' ) == '&#36;' ){
		update_option( 'ec_option_currency', '$' );	
	}
	// END FIX FOR CURRENCY ISSUES
	
	// CREATE DATA FOLDER IF IT DOESN'T EXIST
	if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/" ) ){
		
		$to = WP_PLUGIN_DIR . "/wp-easycart-data/";
		$from = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/";
		
		if( !is_writable( WP_PLUGIN_DIR ) ){
			
			// We really can't do anything now about the data folder. Lets try and get people to do this in the install page.
			
		}else{
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/", 0755 );
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/", 0755 );
			
			// Now backup
			wpeasycart_copyr( $from . "products", $to . "products" );
			if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/" ) )
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/", 0755 );
			if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" ) )
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/", 0755 );
			if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/custom-theme/" ) )
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/custom-theme/", 0755 );
			if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/" ) )
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/", 0755 );
			if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/custom-layout/" ) )
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/custom-layout/", 0755 );
			if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/banners/" ) )
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/banners/", 0755 );
			if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/categories/" ) )
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/categories/", 0751 );
			if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/downloads/" ) )
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/downloads/", 0751 );
			if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" ) )
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/", 0755 );
			if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics2/" ) )
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics2/", 0755 );
			if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics3/" ) )
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics3/", 0755 );
			if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics4/" ) )
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics4/", 0755 );
			if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics5/" ) )
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics5/", 0755 );
			if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/swatches/" ) )
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/swatches/", 0755 );
			if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/uploads/" ) )
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/uploads/", 0751 );
			
		}
	}
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/", 0755 );
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/", 0755 );
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/custom-theme/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/custom-theme/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/custom-theme/", 0755 );
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/", 0755 );
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/custom-layout/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/custom-layout/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/custom-layout/", 0755 );
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/", 0755 );
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/banners/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/banners/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/banners/", 0755 );
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/categories/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/categories/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/categories/", 0751 );
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/downloads/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/downloads/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/downloads/", 0751 );
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/", 0755 );
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics2/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics2/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics2/", 0755 );
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics3/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics3/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics3/", 0755 );
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics4/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics4/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics4/", 0755 );
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics5/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics5/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics5/", 0755 );
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/swatches/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/swatches/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/swatches/", 0755 );
	
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/uploads/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/uploads/" ) )
		mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/uploads/", 0751 );
	// END CREATE DATA FOLDER IF IT DOESN'T EXIST
	
	if( get_option( 'ec_option_allow_tracking' ) && get_option( 'ec_option_allow_tracking' ) == '1' && !function_exists( 'wp_easycart_admin_tracking' ) ){
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/inc/wp_easycart_admin_tracking.php' );
	}
	do_action( 'wpeasycart_activated' );
}

function ec_uninstall(){
	
	// Uninstall DB
	$db_manager = new ec_db_manager( );
	$db_manager->uninstall_db( );
	
	//delete options
	$wpoptions = new ec_wpoptionset();
	$wpoptions->delete_options();
	
	$data_dir = WP_PLUGIN_DIR . "/wp-easycart-data/";
	if( is_dir( $data_dir ) && !is_writable( $data_dir ) ){
		// Could not open the file, lets write it via ftp!
		$ftp_server = $_POST['hostname'];
		$ftp_user_name = $_POST['username'];
		$ftp_user_pass = $_POST['password'];
		
		// set up basic connection
		$conn_id = ftp_connect( $ftp_server ) or die("Couldn't connect to $ftp_server");
		
		// login with username and password
		$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
		
		if( !$login_result ){
			
			die( "Could not connect to your server via FTP to uninstall your wp-easycart. Please remove the files manually." );
			
		}else{
			ec_delete_directory_ftp( $conn_id, $data_dir );
		}
	}else{
		ec_recursive_remove_directory( $data_dir );
	}
	
	// Clean up linking structure
	$store_posts = get_posts( array( 'post_type' => 'ec_store', 'posts_per_page' => 10000 ) );
	foreach( $store_posts as $store_post ) {
		wp_delete_post( $store_post->ID, true);
	}
	
	// Maybe clear square scheduled hook
	wp_clear_scheduled_hook( 'wp_easycart_square_renew_token' );
}

function wpeasycart_update_check( ){
	// UPDATE OPTIONS IF NEEDED
	if( !get_option( 'ec_option_wpoptions_version' ) || get_option( 'ec_option_wpoptions_version' ) != EC_CURRENT_VERSION ){	
		$wpoptions = new ec_wpoptionset( );
		$wpoptions->add_options( );
		$language = new ec_language( );
		$language->update_language_data( );
		update_option( 'ec_option_wpoptions_version', EC_CURRENT_VERSION );
	}
	
	// UPGRADE THE DB IF NEEDED
	if( is_admin( ) && !get_option( 'ec_option_db_new_version' ) || EC_UPGRADE_DB != get_option( 'ec_option_db_new_version' ) ){
		$db_manager = new ec_db_manager( );
		$db_manager->install_db( );
		update_option( 'ec_option_is_installed', '1' );
	}
	
	// CHECK FOLDER SETUP EACH VERSION
	if( !get_option( 'ec_option_data_folders_installed' ) || EC_CURRENT_VERSION != get_option( 'ec_option_data_folders_installed' ) ){
		
		// CREATE DATA FOLDER IF IT DOESN'T EXIST
		if( !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/" ) ){
			
			$to = WP_PLUGIN_DIR . "/wp-easycart-data/";
			$from = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/";
			
			if( !is_writable( WP_PLUGIN_DIR ) ){
				// We really can't do anything now about the data folder. Lets try and get people to do this in the install page.
				
			}else{
				mkdir( $to, 0755 );
				
				// Now backup
				wpeasycart_copyr( $from . "products", $to . "products" );
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/", 0755 );
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/", 0755 );
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/custom-theme/", 0755 );
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/", 0755 );
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/custom-layout/", 0755 );
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/banners/", 0755 );
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/categories/", 0751 );
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/downloads/", 0751 );
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/", 0755 );
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics2/", 0755 );
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics3/", 0755 );
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics4/", 0755 );
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics5/", 0755 );
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/swatches/", 0755 );
				mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/uploads/", 0751 );
				
			}
		}
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/", 0755 );
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/", 0755 );
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/custom-theme/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/custom-theme/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/custom-theme/", 0755 );
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/", 0755 );
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/custom-layout/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/custom-layout/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/custom-layout/", 0755 );
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/", 0755 );
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/banners/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/banners/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/banners/", 0755 );
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/categories/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/categories/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/categories/", 0751 );
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/downloads/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/downloads/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/downloads/", 0751 );
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/", 0755 );
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics2/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics2/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics2/", 0755 );
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics3/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics3/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics3/", 0755 );
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics4/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics4/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics4/", 0755 );
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics5/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics5/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics5/", 0755 );
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/swatches/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/swatches/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/swatches/", 0755 );
		
		if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/uploads/" ) && !is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/products/uploads/" ) )
			mkdir( WP_PLUGIN_DIR . "/wp-easycart-data/products/uploads/", 0751 );
		// END CREATE DATA FOLDER IF IT DOESN'T EXIST
		update_option( 'ec_option_data_folders_installed', EC_CURRENT_VERSION );
		
	}

}
add_action( 'plugins_loaded', 'wpeasycart_update_check' );
register_activation_hook( __FILE__, 'ec_activate' );
register_uninstall_hook( __FILE__, 'ec_uninstall' );

function load_ec_pre(){
	
	// START STATS AND FORM PROCESSING
	$storepageid = get_option('ec_option_storepage');
	$cartpageid = get_option('ec_option_cartpage');
	$accountpageid = get_option('ec_option_accountpage');
	
	if( function_exists( 'icl_object_id' ) ){
		$storepageid = icl_object_id( $storepageid, 'page', true, ICL_LANGUAGE_CODE );
		$cartpageid = icl_object_id( $cartpageid, 'page', true, ICL_LANGUAGE_CODE );
		$accountpageid = icl_object_id( $accountpageid, 'page', true, ICL_LANGUAGE_CODE );
	}
	
	$storepage = get_permalink( $storepageid );
	$cartpage = get_permalink( $cartpageid );
	$accountpage = get_permalink( $accountpageid );
			
	if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
		$https_class = new WordPressHTTPS( );
		$storepage = $https_class->makeUrlHttps( $storepage );
		$cartpage = $https_class->makeUrlHttps( $cartpage );
		$accountpage = $https_class->makeUrlHttps( $accountpage );
	}
	
	if(substr_count($storepage, '?'))							$permalinkdivider = "&";
	else														$permalinkdivider = "?";
	
	if( isset( $_SERVER['HTTPS'] ) )							$currentpageid = url_to_postid( "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] );
	else														$currentpageid = url_to_postid( "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] );
	
	$cartpage = apply_filters( 'wp_easycart_cart_page_url', $cartpage );
	
	if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_success" && isset( $_GET['error_description'] ) && get_option( 'ec_option_payment_third_party' ) == "dwolla_thirdparty" ){
		$db = new ec_db( );
		$db->insert_response( $_GET['order_id'], 1, "Dwolla Third Party", print_r( $_GET, true ) );
		header( "location: " . $accountpage . $permalinkdivider . "ec_page=order_details&order_id=" . htmlspecialchars( $_GET['order_id'], ENT_QUOTES ) . "&ec_error=dwolla_error" );
	
	}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_success" && get_option( 'ec_option_payment_third_party' ) == "dwolla_thirdparty" && isset( $_GET['signature'] ) && isset( $_GET['checkoutId'] ) && isset( $_GET['amount'] ) ){
		
		$dwolla_verification = ec_dwolla_verify_signature( $_GET['signature'], $_GET['checkoutId'], $_GET['amount'] );
		if( $dwolla_verification ){
			global $wpdb;
			$db = new ec_db_admin( );
			$db->update_order_status( $_GET['order_id'], "10" );
				
			// send email
			$order_row = $db->get_order_row_admin( $_GET['order_id'] );
			$orderdetails = $db->get_order_details_admin( $_GET['order_id'] );
			
			/* Update Stock Quantity */
			foreach( $orderdetails as $orderdetail ){
				$product = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.* FROM ec_product WHERE ec_product.product_id = %d", $orderdetail->product_id ) );
				if( $product ){
					if( $product->use_optionitem_quantity_tracking )	
						$db->update_quantity_value( $orderdetail->quantity, $orderdetail->product_id, $orderdetail->optionitem_id_1, $orderdetail->optionitem_id_2, $orderdetail->optionitem_id_3, $orderdetail->optionitem_id_4, $orderdetail->optionitem_id_5 );
					$db->update_product_stock( $orderdetail->product_id, $orderdetail->quantity );
				}
			}
			
			$order_display = new ec_orderdisplay( $order_row, true );
			$order_display->send_email_receipt( );
			$order_display->send_gift_cards( );
						
			do_action( 'wpeasycart_order_paid', $this->order_id );
			
			header( "location: " . $cartpage . $permalinkdivider . "ec_page=checkout_success&order_id=" . htmlspecialchars( $_GET['order_id'], ENT_QUOTES ) );
			
		}else{
			$db = new ec_db( );
			$db->insert_response( $_GET['order_id'], 1, "Dwolla Third Party", print_r( $_GET, true ) );
			header( "location: " . $accountpage . $permalinkdivider . "ec_page=order_details&order_id=" . htmlspecialchars( $_GET['order_id'], ENT_QUOTES ) . "&ec_error=dwolla_error" );
	
		}
	}
	
	/* Update the Menu and Product Statistics */
	if( isset( $_GET['model_number'] ) ){
		$db = new ec_db( );
		$db->update_product_views( $_GET['model_number'] );
	}else if( isset( $_GET['menuid'] ) ){
		$db = new ec_db( );
		$db->update_menu_views( $_GET['menuid'] );	
	}else if( isset( $_GET['submenuid'] ) ){
		$db = new ec_db( );
		$db->update_submenu_views( $_GET['submenuid'] );	
	}else if( isset( $_GET['subsubmenuid'] ) ){
		$db = new ec_db( );
		$db->update_subsubmenu_views( $_GET['subsubmenuid'] );	
	}
	
	/* Cart Form Actions, Process Prior to WP Loading */
	if( isset( $_POST['ec_cart_form_action'] ) ){
		$ec_cartpage = new ec_cartpage();
		$ec_cartpage->process_form_action( $_POST['ec_cart_form_action'] );
	}else if( isset( $_GET['ec_cart_action'] ) ){
		$ec_cartpage = new ec_cartpage();
		$ec_cartpage->process_form_action( $_GET['ec_cart_action'] );	
	}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "3dsecure" ){
		$ec_cartpage = new ec_cartpage();
		$ec_cartpage->process_form_action( "3dsecure" );
	}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "3ds" ){
		$ec_cartpage = new ec_cartpage();
		$ec_cartpage->process_form_action( "3ds" );
	}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "3dsprocess" ){
		$ec_cartpage = new ec_cartpage();
		$ec_cartpage->process_form_action( "3dsprocess" );
	}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "third_party" ){
		$ec_cartpage = new ec_cartpage();
		$ec_cartpage->process_form_action( "third_party_forward" );
	}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "realex_redirect" ){
		$ec_cartpage = new ec_cartpage();
		$ec_cartpage->process_form_action( "realex_redirect" );
	}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "realex_response" ){
		$ec_cartpage = new ec_cartpage();
		$ec_cartpage->process_form_action( "realex_response" );
	}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "process_affirm" ){
		$ec_cartpage = new ec_cartpage( true );
		$ec_cartpage->process_form_action( "submit_order" );
	}else if( isset( $_GET['ec_action'] ) && $_GET['ec_action'] == "deconetwork_add_to_cart" ){
		$ec_cartpage = new ec_cartpage( true );
		$ec_cartpage->process_form_action( "deconetwork_add_to_cart" );
	}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_success" && isset( $_GET['ec_action'] ) && $_GET['ec_action'] == "paymentexpress" ){
		$ec_cartpage = new ec_cartpage();
		$ec_cartpage->process_form_action( "paymentexpress_thirdparty_response" );
	}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "nets_return" && isset( $_GET['transactionId'] ) ){
		global $wpdb;
		$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT ec_order.order_id FROM ec_order WHERE ec_order.nets_transaction_id = %s", $_GET['transactionId'] ) );
		
		$nets = new ec_nets( );
		$nets->process_payment_final( $order_id, htmlspecialchars( $_GET['transactionId'], ENT_QUOTES ), htmlspecialchars( $_GET['responseCode'], ENT_QUOTES ) );
	}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "wp-easycart-sagepay-za" ){
		$sagepay_za = new ec_sagepay_paynow_za( );
		$sagepay_za->process_response( );
	}
	
	/* Account Form Actions, Process Prior to WP Loading */
	if( isset( $_POST['ec_account_form_action'] ) ){
		$ec_accountpage = new ec_accountpage();
		$ec_accountpage->process_form_action( $_POST['ec_account_form_action'] );
	
	}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "logout" ){
		$ec_accountpage = new ec_accountpage();
		$ec_accountpage->process_form_action( "logout" );
	
	}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "print_receipt" ){
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/inc/scripts/print_receipt.php" );
		die( );
	
	}else if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "activate_account" && isset( $_GET['email'] ) && isset( $_GET['key'] ) ){
		$db = new ec_db( );
		$is_activated = $db->activate_user( $_GET['email'], $_GET['key'] );
		if( $is_activated ){
			header( "location: " . $account_page . $permalinkdivider . "ec_page=login&account_success=activation_success" );
		}else{
			header( "location: " . $account_page . $permalinkdivider . "ec_page=login&account_error=activation_error" );
		}
	}
	
	if( isset( $_GET['ec_add_to_cart'] ) ){
		
		global $wpdb;
		
		wpeasycart_session( )->handle_session( );
		
		$cart_id = $GLOBALS['ec_cart_data']->ec_cart_id;
		if( isset( $_POST['cart_id'] ) )
			$cart_id = $_POST['cart_id'];
		
		$db = new ec_db( );
		$tempcart_id = $db->quick_add_to_cart( $_GET['ec_add_to_cart'] );
		
		if( $tempcart_id ){
			
			$product = $wpdb->get_row( $wpdb->prepare( "SELECT product_id, model_number, option_id_1, option_id_2, option_id_3, option_id_4, option_id_5, use_advanced_optionset FROM ec_product WHERE model_number = %s", $_GET['ec_add_to_cart'] ) );
			if( $product ){
				
				$product_id = $product->product_id;
				$use_advanced_optionset = $product->use_advanced_optionset;
				$option_vals = array( );
				
				if( $use_advanced_optionset ){
					
					$optionsets = $GLOBALS['ec_advanced_optionsets']->get_advanced_optionsets( $product_id );
					$grid_quantity = 0;
					
					foreach( $optionsets as $optionset ){
						if( $optionset->option_meta['url_var'] != "" && isset( $_GET[$optionset->option_meta['url_var']] ) ){
							
							if( $optionset->option_type == "checkbox" ){
								$selected_optionitems = array( );
								if( is_array( $_GET[$optionset->option_meta['url_var']] ) ){
									foreach( $_GET[$optionset->option_meta['url_var']] as $selected_optionitem ){
										$selected_optionitems[] = $selected_optionitem;
									}
								}else{
									$selected_optionitems[] = $_GET[$optionset->option_meta['url_var']];
								}
								$optionitems = $db->get_advanced_optionitems( $optionset->option_id );
								foreach( $optionitems as $optionitem ){
									if( in_array( $optionitem->optionitem_name, $selected_optionitems ) ){
										$option_vals[] = array( "option_id" => $optionset->option_id, "optionitem_id" => $optionitem->optionitem_id, "option_name" => $optionitem->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionitem->option_type, "optionitem_value" => $optionitem->optionitem_name, "optionitem_model_number" => $optionitem->optionitem_model_number );
									}
								}
							}else if( $optionset->option_type == "combo" || $optionset->option_type == "swatch" || $optionset->option_type == "radio" ){
								$optionitems = $db->get_advanced_optionitems( $optionset->option_id );
								foreach( $optionitems as $optionitem ){
									if( $optionitem->optionitem_name == $_GET[$optionset->option_meta['url_var']] ){
										$option_vals[] = array( "option_id" => $optionset->option_id, "optionitem_id" => $optionitem->optionitem_id, "option_name" => $optionitem->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionitem->option_type, "optionitem_value" => $optionitem->optionitem_name, "optionitem_model_number" => $optionitem->optionitem_model_number );
									}
								}
							}else{
								$optionitems = $db->get_advanced_optionitems( $optionset->option_id );
								foreach( $optionitems as $optionitem ){
									$option_vals[] = array( "option_id" => $optionset->option_id, "optionitem_id" => $optionitem->optionitem_id, "option_name" => $optionitem->option_name, "optionitem_name" => $optionitem->optionitem_name, "option_type" => $optionitem->option_type, "optionitem_value" => stripslashes( $_GET[$optionset->option_meta['url_var']] ), "optionitem_model_number" => $optionitem->optionitem_model_number );
								}
							}
						}
						
					} //end foreach
				
				}else{// else use basic
					$option_id_1 = $option_id_2 = $option_id_3 = $option_id_4 = $option_id_5 = 0;
					
					if( $product->option_id_1 || $product->option_id_2 || $product->option_id_3 || $product->option_id_4 || $product->option_id_5 ){
						$products = $db->get_product_list( $wpdb->prepare( " WHERE product.model_number = %s AND product.activate_in_store = 1", $product->model_number ), "", "", "", "wpeasycart-product-only-".$product->model_number );
						if( count( $products ) ){
							$product_item = new ec_product( $products[0], 0, 1, 0 );
							if( $product_item->has_options ){
								if( $product->option_id_1 && $product_item->options->optionset1->option_meta['url_var'] != '' && isset( $_GET[$product_item->options->optionset1->option_meta['url_var']] ) ){
									for( $j=0; $j<count( $product_item->options->optionset1->optionset ); $j++ ){
										if( $_GET[$product_item->options->optionset1->option_meta['url_var']] == $product_item->options->optionset1->optionset[$j]->optionitem_name ){
											$option_id_1 = $product_item->options->optionset1->optionset[$j]->optionitem_id;
										}
									}
								}
								
								if( $product->option_id_2 && $product_item->options->optionset2->option_meta['url_var'] != '' && isset( $_GET[$product_item->options->optionset2->option_meta['url_var']] ) ){
									for( $j=0; $j<count( $product_item->options->optionset2->optionset ); $j++ ){
										if( $_GET[$product_item->options->optionset2->option_meta['url_var']] == $product_item->options->optionset2->optionset[$j]->optionitem_name ){
											$option_id_2 = $product_item->options->optionset2->optionset[$j]->optionitem_id;
										}
									}
								}
								
								if( $product->option_id_3 && $product_item->options->optionset3->option_meta['url_var'] != '' && isset( $_GET[$product_item->options->optionset3->option_meta['url_var']] ) ){
									for( $j=0; $j<count( $product_item->options->optionset3->optionset ); $j++ ){
										if( $_GET[$product_item->options->optionset3->option_meta['url_var']] == $product_item->options->optionset3->optionset[$j]->optionitem_name ){
											$option_id_3 = $product_item->options->optionset3->optionset[$j]->optionitem_id;
										}
									}
								}
								
								if( $product->option_id_4 && $product_item->options->optionset4->option_meta['url_var'] != '' && isset( $_GET[$product_item->options->optionset4->option_meta['url_var']] ) ){
									for( $j=0; $j<count( $product_item->options->optionset4->optionset ); $j++ ){
										if( $_GET[$product_item->options->optionset4->option_meta['url_var']] == $product_item->options->optionset4->optionset[$j]->optionitem_name ){
											$option_id_4 = $product_item->options->optionset4->optionset[$j]->optionitem_id;
										}
									}
								}
								
								if( $product->option_id_5 && $product_item->options->optionset5->option_meta['url_var'] != '' && isset( $_GET[$product_item->options->optionset5->option_meta['url_var']] ) ){
									for( $j=0; $j<count( $product_item->options->optionset5->optionset ); $j++ ){
										if( $_GET[$product_item->options->optionset5->option_meta['url_var']] == $product_item->options->optionset5->optionset[$j]->optionitem_name ){
											$option_id_5 = $product_item->options->optionset5->optionset[$j]->optionitem_id;
										}
									}
								}
								
								$wpdb->query( $wpdb->prepare( "UPDATE ec_tempcart SET optionitem_id_1 = %d, optionitem_id_2 = %d, optionitem_id_3 = %d, optionitem_id_4 = %d, optionitem_id_5 = %d WHERE tempcart_id = %d", $option_id_1, $option_id_2, $option_id_3, $option_id_4, $option_id_5, $tempcart_id ) );
							}
						}
					}
				}
				
				for( $i=0; $i<count( $option_vals ); $i++ ){
					$db->add_option_to_cart( $tempcart_id, $GLOBALS['ec_cart_data']->ec_cart_id, $option_vals[$i] );
				}
			}// If product found
			
			header( "location: " . $cartpage );
			die( );
		}else{
			header( "location: " . $storepage . "?model_number=" . htmlspecialchars( $_GET['ec_add_to_cart'], ENT_QUOTES ) );
			die( );
		}
	
	}else if( isset( $_GET['ec_action'] ) && $_GET['ec_action'] == "addtocart" && isset( $_GET['model_number'] ) ){
		$cart_id = $GLOBALS['ec_cart_id'];
		if( isset( $_POST['cart_id'] ) )
			$cart_id = $_POST['cart_id'];
		
		wpeasycart_session( )->handle_session( $cart_id );
		
		$db = new ec_db( );
		$added_successfully = $db->quick_add_to_cart( $_GET['model_number'] );
		if( $added_successfully ){
			header( "location: " . $cartpage );
		}else{
			header( "location: " . $storepage . "?model_number=" . htmlspecialchars( $_GET['model_number'], ENT_QUOTES ) );
		}
	}
	
	/* Load abandoned cart */
	if( isset( $_GET['ec_load_tempcart'] ) && isset( $_GET['ec_load_email'] ) ){
		global $wpdb;
		$tempcart_row = $wpdb->get_row( $wpdb->prepare( "SELECT ec_tempcart.session_id FROM ec_tempcart, ec_tempcart_data WHERE ec_tempcart.session_id = %s AND ec_tempcart_data.session_id = ec_tempcart.session_id AND ec_tempcart_data.email = %s", $_GET['ec_load_tempcart'], $_GET['ec_load_email'] ) );
		if( $tempcart_row ){
			$GLOBALS['ec_cart_id'] = $tempcart_row->session_id;
			setcookie( "ec_cart_id", "", time( ) - 3600 );
			setcookie( "ec_cart_id", "", time( ) - 3600, "/" );
			setcookie( 'ec_cart_id', $GLOBALS['ec_cart_id'], time( ) + ( 3600 * 24 * 1 ), "/" );
			$cart_page_id = get_option('ec_option_cartpage');
			if( function_exists( 'icl_object_id' ) )
				$cart_page_id = icl_object_id( $cart_page_id, 'page', true, ICL_LANGUAGE_CODE );
			$cart_page = get_permalink( $cart_page_id );
			if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
				$https_class = new WordPressHTTPS( );
				$cart_page = $https_class->makeUrlHttps( $cart_page );
			}
			wp_redirect( $cart_page );
		}
	}
	
	/* Newsletter Form Actions */
	if( isset( $_POST['ec_newsletter_email'] ) ){
		
		if( isset( $_POST['ec_newsletter_name'] ) )
			$newsletter_name = $_POST['ec_newsletter_name'];
		else
			$newsletter_name = "";
		
		if( filter_var( $_POST['ec_newsletter_email'], FILTER_VALIDATE_EMAIL ) ){
			$ec_db = new ec_db();
			$ec_db->insert_subscriber( $_POST['ec_newsletter_email'], $newsletter_name, "" );
				
			// MyMail Hook
			if( function_exists( 'mailster' ) ){
				$subscriber_id = mailster('subscribers')->add( array(
					'email' => $_POST['ec_newsletter_email'],
					'name' => $newsletter_name,
					'status' => 1,
				), false );
			}
			
			do_action( 'wpeasycart_subscriber_added', $_POST['ec_newsletter_email'], $newsletter_name );
		}
		setcookie( 'ec_newsletter_popup', 'hide', time( ) + ( 10 * 365 * 24 * 60 * 60 ), "/" );
	}
	
	/* Manual Hide Video */
	if( current_user_can( 'manage_options' ) && isset( $_GET['ec_admin_action'] ) && $_GET['ec_admin_action'] == "hide-video" ){
		update_option( 'ec_option_hide_design_help_video', '1' );
	}
	
	// END STATS AND FORM PROCESSING
	
} // CLOSE PRE FUNCTION

function ec_custom_headers( ){
	if( isset( $_GET['order_id'] ) && isset( $_GET['orderdetail_id'] ) && isset( $_GET['download_id'] ) && $GLOBALS['ec_cart_data']->cart_data->user_id != "" ){
		$mysqli = new ec_db( );
		$orderdetail_row = $mysqli->get_orderdetail_row( $_GET['order_id'], $_GET['orderdetail_id'], $GLOBALS['ec_cart_data']->cart_data->user_id );
		$ec_orderdetail = new ec_orderdetail( $orderdetail_row, 1 );
	}
	
	if( !get_option( 'ec_option_cache_prevent' ) && (
            ( 
                isset( $_GET['ec_page'] ) && 
                ( 
                    $_GET['ec_page'] == "checkout_payment" || $_GET['ec_page'] == "checkout_shipping" || $_GET['ec_page'] == "checkout_info"
                )
            ) || (
                get_option( 'ec_option_cartpage' ) == get_the_ID( )
            ) || (
                get_option( 'ec_option_accountpage' ) == get_the_ID( )
            )
        )
	){
		header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
		header('Pragma: no-cache'); // HTTP 1.0.
		header('Expires: 0'); // Proxies.
	}
}

function ec_cache_management( ){
	if( get_option( 'ec_option_caching_on' ) ){
		// File does not exist at all
		if( !file_exists( ABSPATH . "wp-content/plugins/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec-store-css.css" ) ){
			ec_regenerate_css( );
			ec_regenerate_js( );
			update_option( 'ec_option_cached_date', time( ) );
		}
		
		// Use cache management system
		else if( get_option( 'ec_option_cache_update_period' ) ){
			
			$update_time = true;
			$new_time = time( );
			
			// Use a automatic cache builder and the last update has not been set
			if( get_option( 'ec_option_cache_update_period' ) && !get_option( 'ec_option_cached_date' ) ){
				ec_regenerate_css( );
				ec_regenerate_js( );
			}
			
			// Cache update daily
			else if( get_option( 'ec_option_cache_update_period' ) == '1' && get_option( 'ec_option_cached_date' ) < strtotime("-1 day") ){
				ec_regenerate_css( );
				ec_regenerate_js( );				
			}
			
			// Cache update weekly
			else if( get_option( 'ec_option_cache_update_period' ) == '1' && get_option( 'ec_option_cached_date' ) < strtotime("-1 week") ){
				ec_regenerate_css( );
				ec_regenerate_js( );				
			}
			
			// Cache update monthly
			else if( get_option( 'ec_option_cache_update_period' ) == '1' && get_option( 'ec_option_cached_date' ) < strtotime("-1 month") ){
				ec_regenerate_css( );
				ec_regenerate_js( );				
			}
			
			// Cache update yearly
			else if( get_option( 'ec_option_cache_update_period' ) == '1' && get_option( 'ec_option_cached_date' ) < strtotime("-1 year") ){
				ec_regenerate_css( );
				ec_regenerate_js( );				
			}
			
			// Do not update
			else{
				$update_time = false;
			}
			
			if( $update_time ){
				update_option( 'ec_option_cached_date', $new_time );
			}
		}
	}else{
		ec_regenerate_css( );
		ec_regenerate_js( );
		update_option( 'ec_option_cached_date', time( ) );
	}
}

function ec_css_loader_v3( ){
	
	$pageURL = 'http';
	if( isset( $_SERVER["HTTPS"] ) )
		$pageURL .= "s";
		
	if( current_user_can( 'manage_options' ) ){
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/live-editor.css' ) ){
			wp_register_style( 'wpeasycart_admin_css', plugins_url( 'wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/live-editor.css' ) );
		}else{
			wp_register_style( 'wpeasycart_admin_css', plugins_url( 'wp-easycart/design/theme/' . get_option( 'ec_option_latest_theme' ) . '/live-editor.css' ) );
		}
		wp_enqueue_style( 'wpeasycart_admin_css' );
	}
	
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec-store.css' ) ){
		wp_register_style( 'wpeasycart_css', plugins_url( 'wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec-store.css' ), array( ), EC_CURRENT_VERSION );
	}else{
		wp_register_style( 'wpeasycart_css', plugins_url( 'wp-easycart/design/theme/' . get_option( 'ec_option_latest_theme' ) . '/ec-store.css' ), array( ), EC_CURRENT_VERSION );
	}
	wp_enqueue_style( 'wpeasycart_css' );
	
	$gfont_string = "://fonts.googleapis.com/css?family=Lato|Monda|Open+Sans|Droid+Serif";
	if( get_option( 'ec_option_font_main' ) ){
		$gfont_string .= "|" . str_replace( " ", "+", get_option( 'ec_option_font_main' ) );
	}
	wp_register_style( "wpeasycart_gfont", $pageURL . $gfont_string );
	wp_enqueue_style( 'wpeasycart_gfont' );
	
	if( get_option( 'ec_option_use_rtl' ) ){
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/rtl_support.css' ) ){
			wp_register_style( 'wpeasycart_rtl_css', plugins_url( 'wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/rtl_support.css' ) );
		}else{
			wp_register_style( 'wpeasycart_rtl_css', plugins_url( 'wp-easycart/design/theme/' . get_option( 'ec_option_latest_theme' ) . '/rtl_support.css' ) );
		}
		wp_enqueue_style( 'wpeasycart_rtl_css' );
	}
	
	wp_register_style( 'jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css' );
    wp_enqueue_style( 'jquery-ui' );  

}

function ec_js_loader_v3( ){
		
	if( current_user_can( 'manage_options' ) ){
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/live-editor.js' ) ){
			wp_enqueue_script( 'wpeasycart_admin_js', plugins_url( 'wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/live-editor.js' ), array( 'jquery', 'jquery-ui-core' ), EC_CURRENT_VERSION );
		}else{
			wp_enqueue_script( 'wpeasycart_admin_js', plugins_url( 'wp-easycart/design/theme/' . get_option( 'ec_option_latest_theme' ) . '/live-editor.js' ), array( 'jquery', 'jquery-ui-core' ), EC_CURRENT_VERSION );
		}
	}
	
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec-store.js' ) ){
		wp_enqueue_script( 'wpeasycart_js', plugins_url( 'wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec-store.js' ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion', 'jquery-ui-datepicker' ), EC_CURRENT_VERSION, false );
	}else{
		wp_enqueue_script( 'wpeasycart_js', plugins_url( 'wp-easycart/design/theme/' . get_option( 'ec_option_latest_theme' ) . '/ec-store.js' ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion', 'jquery-ui-datepicker' ), EC_CURRENT_VERSION, false );
	}
	
	if( get_option( 'ec_option_enable_recaptcha' ) ){
		wp_enqueue_script( 'wpeasycart_google_recaptcha_js', 'https://www.google.com/recaptcha/api.js?onload=wpeasycart_recaptcha_onload&render=explicit', array( ), EC_CURRENT_VERSION, false );
	}

}

function wp_easycart_load_cart_js( ){
    if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/jquery.payment.min.js' ) ){
		wp_enqueue_script( 'payment_jquery_js', plugins_url( 'wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/jquery.payment.min.js' ), array( 'jquery' ), EC_CURRENT_VERSION, false );
	}else{
		wp_enqueue_script( 'payment_jquery_js', plugins_url( 'wp-easycart/design/theme/' . get_option( 'ec_option_latest_theme' ) . '/jquery.payment.min.js' ), array( 'jquery' ), EC_CURRENT_VERSION, false );
	}
    
    if( get_option( 'ec_option_payment_process_method' ) == "square" ){
		wp_enqueue_script( 'wpeasycart_square_js', ( ( get_option( 'ec_option_square_is_sandbox' ) ) ? 'https://js.squareupsandbox.com/v2/paymentform' : 'https://js.squareup.com/v2/paymentform' ), array( ), EC_CURRENT_VERSION, false );
        add_filter( 'sgo_js_async_exclude', 'wp_easycart_exclude_from_siteground', 10, 1 );
	}
    
    if( get_option( 'ec_option_payment_process_method' ) == "stripe" || get_option( 'ec_option_payment_process_method' ) == 'stripe_connect' ){
		wp_enqueue_script( 'wpeasycart_stripe_js', 'https://js.stripe.com/v3/', array( ), EC_CURRENT_VERSION, false );
        add_filter( 'sgo_js_async_exclude', 'wp_easycart_exclude_from_siteground', 10, 1 );
	}
    
    if( get_option( 'ec_option_payment_third_party' ) == "paypal" && ( get_option( 'ec_option_paypal_enable_credit' ) == '1' || get_option( 'ec_option_paypal_enable_pay_now' ) == '1' ) ){
        wp_enqueue_script( 'wpeasycart_paypal_js', 'https://www.paypalobjects.com/api/checkout.js', array( ), EC_CURRENT_VERSION, false );
        add_filter( 'sgo_js_async_exclude', 'wp_easycart_exclude_from_siteground', 10, 1 );
    }
    
    if( get_option( 'ec_option_payment_process_method' ) == "braintree" && isset( $_GET['ec_page'] ) && ( $_GET['ec_page'] == 'checkout_payment' || $_GET['ec_page'] == 'subscription_info' ) ){
		wp_enqueue_script( 'wpeasycart_braintree_js', 'https://js.braintreegateway.com/web/dropin/1.13.0/js/dropin.min.js', array( ), EC_CURRENT_VERSION, false );
	}
}

function wp_easycart_exclude_from_siteground( $list ){
    $list[] = 'wpeasycart_stripe_js';
    $list[] = 'wpeasycart_square_js';
    $list[] = 'wpeasycart_paypal_js';
    return $list;
}

function ec_regenerate_css( ){
	ob_start( "ec_save_css_file" );
	include( ABSPATH . "wp-content/plugins/" . EC_PLUGIN_DIRECTORY . '/inc/scripts/ec_css_generator.php' );
	ob_end_flush();
}

function ec_save_css_file( $buffer ){
	file_put_contents( ABSPATH . "wp-content/plugins/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec-store-css.css", $buffer );
}

function ec_regenerate_js( ){
	if( file_exists( ABSPATH . "wp-content/plugins/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_account_billing_information/" ) ){ //check to see if any of the old folders exist
		ob_start( "ec_save_js_file" );
		include( ABSPATH . "wp-content/plugins/" . EC_PLUGIN_DIRECTORY . '/inc/scripts/ec_js_generator.php' );
		ob_end_flush();
	}
}

function ec_save_js_file( $buffer ){
	file_put_contents( ABSPATH . "wp-content/plugins/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec-store-js.js", $buffer );
}

function ec_load_css( ){
	
	if( !get_option( 'ec_option_base_theme' ) || file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/head_content.php" ) || !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_image_not_found.jpg" ) ){
		ec_css_loader_v3( );
	
	}else{
		ec_cache_management( );
	
		if( file_exists( ABSPATH . "wp-content/plugins/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec-store-css.css" ) && filesize( ABSPATH . "wp-content/plugins/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec-store-css.css" ) ){
			// Load the cached file because it exists
			wp_register_style( 'wpeasycart_css', plugins_url( 'wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec-store-css.css' ) );
			wp_enqueue_style( 'wpeasycart_css' );
		
		}else{
			// File did not exist, revert back to the development mode loader
			wp_register_style( 'wpeasycart_css', plugins_url( EC_PLUGIN_DIRECTORY . '/inc/scripts/ec_css_loader.php' ) );
			wp_enqueue_style( 'wpeasycart_css' );
		}
		
		$gfont_list = "";
		$font_list = explode( ":::", get_option( 'ec_option_font_replacements' ) );
		$fonts_added = 0;
		
		for( $i=0; $i<count( $font_list ); $i++ ){
			$temp = explode( "=", $font_list[$i] );
			if(  	$temp[1] != "Verdana, Geneva, sans-serif" && 
					$temp[1] != "Georgia, Times New Roman, Times, serif" && 
					$temp[1] != "Courier New, Courier, monospace" && 
					$temp[1] != "Arial, Helvetica, sans-serif" && 
					$temp[1] != "Tahoma, Geneva, sans-serif" && 
					$temp[1] != "Trebuchet MS, Arial, Helvetica, sans-serif" && 
					$temp[1] != "Arial Black, Gadget, sans-serif" && 
					$temp[1] != "Times New Roman, Times, serif" && 
					$temp[1] != "Palatino Linotype, Book Antiqua, Palatino, serif" && 
					$temp[1] != "Lucida Sans Unicode, Lucida Grande, sans-serif" && 
					$temp[1] != "MS Serif, New York, serif" && 
					$temp[1] != "Lucida Console, Monaco, monospace" && 
					$temp[1] != "Comic Sans MS, cursive" &&
					$temp[1] != ""
			){
				if( $fonts_added > 0 )
					$gfont_list .= "|";
				
				$gfont_list .= $temp[1];
				$fonts_added++;
				
			}
		}
		
		if( $fonts_added > 0 ){
			$pageURL = 'http';
			if( isset( $_SERVER["HTTPS"] ) )
				$pageURL .= "s";
			
			wp_register_style( "wpeasycart_gfont", $pageURL . "://fonts.googleapis.com/css?family=" . $gfont_list );
			wp_enqueue_style( 'wpeasycart_gfont' );
		}
		
	}
	
}	

function ec_load_js( ){
	
	if( !get_option( 'ec_option_base_theme' ) || file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/head_content.php" ) || !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_image_not_found.jpg" ) ){
		ec_js_loader_v3( );
	
	}else{
		
		if( file_exists( ABSPATH . "wp-content/plugins/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec-store-js.js" ) && filesize( ABSPATH . "wp-content/plugins/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec-store-js.js" ) ){
			// Load the cached file because it exists
			wp_register_script( 'wpeasycart_js', plugins_url( 'wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . '/ec-store-js.js' ), array( 'jquery' ) );
			wp_enqueue_script( 'wpeasycart_js' );
		
		}else{
			// File did not exist, revert back to the development mode loader
			wp_register_script( 'wpeasycart_js', plugins_url( EC_PLUGIN_DIRECTORY . '/inc/scripts/ec_js_loader.php' ), array( 'jquery' ) );
			wp_enqueue_script( 'wpeasycart_js' );
		}
		
	}
	
	$ajax_subfolder = "";
	if( file_exists( plugins_url( 'wp-easycart-data/ajax-subfolder.txt' ) ) ){
		$ajax_subfolder = file_get_contents( plugins_url( 'wp-easycart-data/ajax-subfolder.txt' ) );
	}
	
	$https_link = "";
	if( class_exists( "WordPressHTTPS" ) ){
		$https_class = new WordPressHTTPS( );
		if( $ajax_subfolder != "" ){
			$https_link = $https_class->getHttpsUrl() . $ajax_subfolder . '/wp-admin/admin-ajax.php';
		}else{
			$https_link = $https_class->makeUrlHttps( admin_url( 'admin-ajax.php' ) );
		}
	}else{
		if( $ajax_subfolder != "" ){
			$https_link = str_replace( "http://", "https://", str_replace( "/wp-admin", $ajax_subfolder . "/wp-admin", admin_url( 'admin-ajax.php' ) ) );
		}else{
			$https_link = str_replace( "http://", "https://", admin_url( 'admin-ajax.php' ) );
		}
	}
	
	if( defined( 'ICL_LANGUAGE_CODE' ) ){
		$current_language = ICL_LANGUAGE_CODE;
	}else{
		$current_language = $GLOBALS['language']->language_code;
	}
	
	if( isset( $_SERVER['HTTPS'] ) && $_SERVER["HTTPS"] == "on" )
		wp_localize_script( 'wpeasycart_js', 'wpeasycart_ajax_object', array( 'ajax_url' => $https_link, 'current_language' => $current_language ) );
	else
		wp_localize_script( 'wpeasycart_js', 'wpeasycart_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'current_language' => $current_language ) );
	
}

function wpeasycart_seo_tags( ){
	
	global $wp_query;
	global $wpdb;
	
	/* Check for Post Content Shortcodes */
	$post_obj = $wp_query->get_queried_object();
	if( $post_obj && isset( $post_obj->post_content ) ){
		
		if( strstr( $post_obj->post_content, "[ec_store" ) && strstr( $post_obj->post_content, "modelnumber" ) ){
			$matches = array( );
			preg_match( '/\[ec_store modelnumber=\"(.*)?\"\]/', $post_obj->post_content, $matches );
			if( count( $matches ) >= 2 ){
				$model_number = $matches[1];
				$product_seo = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.seo_keywords, ec_product.seo_description FROM ec_product WHERE ec_product.model_number = %s", $model_number ) );
				if( $product_seo->seo_description != "" )
					echo "<meta name=\"description\" content=\"" . $product_seo->seo_description . "\">\n";
				if( $product_seo->seo_keywords != "" )
					echo "<meta name=\"keywords\" content=\"" . $product_seo->seo_keywords . "\">\n";
			}
			ec_show_facebook_meta( $model_number );
		
		}else if( strstr( $post_obj->post_content, "[ec_store" ) && strstr( $post_obj->post_content, "menuid" ) ){
			$matches = array( );
			preg_match( '/\[ec_store menuid=\"(.*)?\"\]/', $post_obj->post_content, $matches );
			if( count( $matches ) >= 2 ){
				$menu_id = $matches[1];
				$menu_seo = $wpdb->get_row( $wpdb->prepare( "SELECT ec_menulevel1.seo_keywords, ec_menulevel1.seo_description FROM ec_menulevel1 WHERE ec_menulevel1.menulevel1_id = %d", $menu_id ) );
				if( $menu_seo->seo_description != "" )
					echo "<meta name=\"description\" content=\"" . $menu_seo->seo_description . "\">\n";
				if( $menu_seo->seo_keywords != "" )
					echo "<meta name=\"keywords\" content=\"" . $menu_seo->seo_keywords . "\">\n";
			}
		
		}else if( strstr( $post_obj->post_content, "[ec_store" ) && strstr( $post_obj->post_content, "submenuid" ) ){
			$matches = array( );
			preg_match( '/\[ec_store submenuid=\"(.*)?\"\]/', $post_obj->post_content, $matches );
			if( count( $matches ) >= 2 ){
				$submenu_id = $matches[1];
				$submenu_seo = $wpdb->get_row( $wpdb->prepare( "SELECT ec_menulevel2.seo_keywords, ec_menulevel2.seo_description FROM ec_menulevel2 WHERE ec_menulevel2.menulevel2_id = %d", $submenu_id ) );
				if( $submenu_seo->seo_description != "" )
					echo "<meta name=\"description\" content=\"" . $submenu_seo->seo_description . "\">\n";
				if( $submenu_seo->seo_keywords != "" )
					echo "<meta name=\"keywords\" content=\"" . $submenu_seo->seo_keywords . "\">\n";
			}
		
		}else if( strstr( $post_obj->post_content, "[ec_store" ) && strstr( $post_obj->post_content, "subsubmenuid" ) ){
			$matches = array( );
			preg_match( '/\[ec_store menuid=\"(.*)?\"\]/', $post_obj->post_content, $matches );
			if( count( $matches ) >= 2 ){
				$subsubmenu_id = $matches[1];
				$subsubmenu_seo = $wpdb->get_row( $wpdb->prepare( "SELECT ec_menulevel3.seo_keywords, ec_menulevel3.seo_description FROM ec_menulevel3 WHERE ec_menulevel3.menulevel3_id = %d", $subsubmenu_id ) );
				if( $subsubmenu_seo->seo_description != "" )
					echo "<meta name=\"description\" content=\"" . $subsubmenu_seo->seo_description . "\">\n";
				if( $subsubmenu_seo->seo_keywords != "" )
					echo "<meta name=\"keywords\" content=\"" . $subsubmenu_seo->seo_keywords . "\">\n";
			}
		}
	
	}
	
	/* Check for GET VARS */
	if( isset( $_GET['model_number'] ) ){
		$matches = array( );
		$model_number = $_GET['model_number'];
		$product_seo = wp_cache_get( 'wpeasycart-product-seo-'.$model_number, 'wpeasycart-product-seo' );
		if( !$product_seo ){
			$product_seo = $wpdb->get_row( $wpdb->prepare( "SELECT ec_product.seo_keywords, ec_product.seo_description FROM ec_product WHERE ec_product.model_number = %s", $model_number ) );
			wp_cache_set( 'wpeasycart-product-seo-'.$model_number, $product_seo, 'wpeasycart-product-seo' );
		}
		if( $product_seo->seo_description != "" )
			echo "<meta name=\"description\" content=\"" . $product_seo->seo_description . "\">\n";
		if( $product_seo->seo_keywords != "" )
			echo "<meta name=\"keywords\" content=\"" . $product_seo->seo_keywords . "\">\n";
		ec_show_facebook_meta( $model_number );
		
	}else if( isset( $_GET['menuid'] ) ){
		$menu_id = $_GET['menuid'];
		$menu_seo = $wpdb->get_row( $wpdb->prepare( "SELECT ec_menulevel1.seo_keywords, ec_menulevel1.seo_description FROM ec_menulevel1 WHERE ec_menulevel1.menulevel1_id = %d", $menu_id ) );
		if( $menu_seo->seo_description != "" )
			echo "<meta name=\"description\" content=\"" . $menu_seo->seo_description . "\">\n";
		if( $menu_seo->seo_keywords != "" )
			echo "<meta name=\"keywords\" content=\"" . $menu_seo->seo_keywords . "\">\n";
	
	}else if( isset( $_GET['submenuid'] ) ){
		$submenu_id = $_GET['submenuid'];
		$submenu_seo = $wpdb->get_row( $wpdb->prepare( "SELECT ec_menulevel2.seo_keywords, ec_menulevel2.seo_description FROM ec_menulevel2 WHERE ec_menulevel2.menulevel2_id = %d", $submenu_id ) );
		if( $submenu_seo->seo_description != "" )
			echo "<meta name=\"description\" content=\"" . $submenu_seo->seo_description . "\">\n";
		if( $submenu_seo->seo_keywords != "" )
			echo "<meta name=\"keywords\" content=\"" . $submenu_seo->seo_keywords . "\">\n";
	
	}else if( isset( $_GET['subsubmenuid'] ) ){
		$subsubmenu_id = $_GET['subsubmenuid'];
		$subsubmenu_seo = $wpdb->get_row( $wpdb->prepare( "SELECT ec_menulevel3.seo_keywords, ec_menulevel3.seo_description FROM ec_menulevel3 WHERE ec_menulevel3.menulevel3_id = %d", $subsubmenu_id ) );
		if( $subsubmenu_seo->seo_description != "" )
			echo "<meta name=\"description\" content=\"" . $subsubmenu_seo->seo_description . "\">\n";
		if( $subsubmenu_seo->seo_keywords != "" )
			echo "<meta name=\"keywords\" content=\"" . $subsubmenu_seo->seo_keywords . "\">\n";
	
	}
	
	if( get_option( 'ec_option_use_affirm' ) && get_option( 'ec_option_affirm_public_key' ) != "" ){
		
		if( get_option( 'ec_option_affirm_sandbox_account' ) ){
			echo '<script>
			  var _affirm_config = {
				public_api_key:  "' . get_option( 'ec_option_affirm_public_key' ) . '",
				script:          "https://cdn1-sandbox.affirm.com/js/v2/affirm.js"
			  };
			  (function(l,g,m,e,a,f,b){var d,c=l[m]||{},h=document.createElement(f),n=document.getElementsByTagName(f)[0],k=function(a,b,c){return function(){a[b]._.push([c,arguments])}};c[e]=k(c,e,"set");d=c[e];c[a]={};c[a]._=[];d._=[];c[a][b]=k(c,a,b);a=0;for(b="set add save post open empty reset on off trigger ready setProduct".split(" ");a<b.length;a++)d[b[a]]=k(c,e,b[a]);a=0;for(b=["get","token","url","items"];a<b.length;a++)d[b[a]]=function(){};h.async=!0;h.src=g[f];n.parentNode.insertBefore(h,n);delete g[f];d(g);l[m]=c})(window,_affirm_config,"affirm","checkout","ui","script","ready");
			</script>';
		}else{
			echo '<script>
			  var _affirm_config = {
				public_api_key:  "' . get_option( 'ec_option_affirm_public_key' ) . '",
				script:          "https://cdn1.affirm.com/js/v2/affirm.js"
			  };
			  (function(l,g,m,e,a,f,b){var d,c=l[m]||{},h=document.createElement(f),n=document.getElementsByTagName(f)[0],k=function(a,b,c){return function(){a[b]._.push([c,arguments])}};c[e]=k(c,e,"set");d=c[e];c[a]={};c[a]._=[];d._=[];c[a][b]=k(c,a,b);a=0;for(b="set add save post open empty reset on off trigger ready setProduct".split(" ");a<b.length;a++)d[b[a]]=k(c,e,b[a]);a=0;for(b=["get","token","url","items"];a<b.length;a++)d[b[a]]=function(){};h.async=!0;h.src=g[f];n.parentNode.insertBefore(h,n);delete g[f];d(g);l[m]=c})(window,_affirm_config,"affirm","checkout","ui","script","ready");
			</script>';
		}
	}
	
}
	
function ec_show_facebook_meta( $model_number ){
	
	global $wpdb;
	$ec_db = new ec_db( );
	$product = wp_cache_get( 'wpeasycart-product-only-'.$model_number, 'wpeasycart-product-list' );
	if( !$product ){
		$product = $ec_db->get_product_list( $wpdb->prepare( " WHERE product.model_number = %s AND product.activate_in_store = 1", $model_number ), "", "", "", "wpeasycart-product-only-".$model_number );	
		wp_cache_set( "wpeasycart-product-only-".$model_number, $product, 'wpeasycart-product-list' );
	}
	if( count( $product ) > 0 )
		$product = $product[0];
	$product_id = $product['product_id'];
	$prod_title = $product['title'];
	$prod_model_number = $product['model_number'];
	$prod_description = $product['seo_description'];
	if( $prod_description == "" ){
		$prod_description = htmlspecialchars( strip_tags( $product['short_description'] ), ENT_QUOTES );
	}
	if( $prod_description == "" ){
		$prod_description = htmlspecialchars( strip_tags( $product['description'] ), ENT_QUOTES );
	}
	$prod_use_optionitem_images = $product['use_optionitem_images'];
	$prod_image = $product['image1'];
		
	if( $prod_use_optionitem_images ){
		$optimgs = $wpdb->get_results( $wpdb->prepare( "SELECT 
				optionitemimage.optionitemimage_id,
				optionitemimage.optionitem_id, 
				optionitemimage.product_id, 
				optionitemimage.image1, 
				optionitemimage.image2, 
				optionitemimage.image3, 
				optionitemimage.image4, 
				optionitemimage.image5,
				optionitem.optionitem_order
				
				FROM ec_optionitemimage as optionitemimage, ec_optionitem as optionitem

				WHERE 
				optionitemimage.product_id = %d AND
				optionitem.optionitem_id = optionitemimage.optionitem_id
				
                GROUP BY optionitemimage.optionitemimage_id
				
				ORDER BY
				optionitemimage.product_id,
				optionitem.optionitem_order", $product_id ) );
		if( count( $optimgs ) > 0 )
			$prod_image = $optimgs[0]->image1;
	}	
	
	remove_action('wp_head', 'rel_canonical');
	
	//this method places to early, before html tags open
	echo "\n";
	echo "<meta property=\"og:title\" content=\"" . str_replace( '"', '\"', strip_tags( $prod_title ) ) . "\" />\n"; 
	echo "<meta property=\"og:type\" content=\"product\" />\n";
	echo "<meta property=\"og:description\" content=\"" . ec_short_string( $prod_description, 300) . "\" />\n";
	if( substr( $prod_image, 0, 7 ) == 'http://' || substr( $prod_image, 0, 8 ) == 'https://' ){
		echo "<meta property=\"og:image\" content=\"" .  $prod_image . "\" />\n"; 
		if( file_exists( $prod_image ) && list( $width, $height ) = @getimagesize( $prod_image ) ){
			echo "<meta property=\"og:image:width\" content=\"" . $width . "\" />\n";
			echo "<meta property=\"og:image:height\" content=\"" . $height . "\" />\n";
		}
		
	}else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $prod_image ) ){
		echo "<meta property=\"og:image\" content=\"" .  plugin_dir_url( "" ) . "wp-easycart-data/products/pics1/" . $prod_image . "\" />\n"; 
		if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $prod_image ) && list( $width, $height ) = @getimagesize( WP_PLUGIN_DIR . "/wp-easycart-data/products/pics1/" . $prod_image ) ){
			echo "<meta property=\"og:image:width\" content=\"" . $width . "\" />\n";
			echo "<meta property=\"og:image:height\" content=\"" . $height . "\" />\n";
		}
	}
	echo "<meta property=\"og:url\" content=\"" . ec_curPageURL() . "\" /> \n";
	
}

function ec_theme_head_data( ){
	$GLOBALS['ec_page_options'] = new ec_page_options( );
	
	if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/head_content.php" ) ){
		include( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/head_content.php" );

	}else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart/design/theme/" . get_option( 'ec_option_latest_theme' ) . "/head_content.php" ) ){
		include( WP_PLUGIN_DIR . "/wp-easycart/design/theme/" . get_option( 'ec_option_latest_theme' ) . "/head_content.php" );
		
	}
}
	
function ec_curPageURL() {
	$pageURL = 'http';
	if( isset( $_SERVER["HTTPS"] ) )
		$pageURL .= "s";

	$pageURL .= "://";
	if( $_SERVER["SERVER_PORT"] != "80" )
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].htmlspecialchars ( $_SERVER["REQUEST_URI"], ENT_QUOTES );
	else
		$pageURL .= $_SERVER["SERVER_NAME"].htmlspecialchars ( $_SERVER["REQUEST_URI"], ENT_QUOTES );

	return $pageURL;
}

function ec_short_string($text, $length){
	$text = strip_tags( $text );
	if( strlen( $text ) > $length )
		$text = substr($text, 0, strpos($text, ' ', $length));
	
	return $text;
}

//[ec_store]
function load_ec_store( $atts ){
	
	if( !defined( 'DONOTCACHEPAGE' ) )
		define( "DONOTCACHEPAGE", true );
	
	if( !defined( 'DONOTCDN' ) )
		define('DONOTCDN', true);
	
	extract( shortcode_atts( array(
		'menuid' => 'NOMENU',
		'submenuid' => 'NOSUBMENU',
		'subsubmenuid' => 'NOSUBSUBMENU',
		'manufacturerid' => 'NOMANUFACTURER',
		'groupid' => 'NOGROUP',
		'modelnumber' => 'NOMODELNUMBER',
		'language' => 'NONE'
	), $atts ) );
	
	if( $language != 'NONE' ){
		$GLOBALS['language'] = new ec_language( $language );
	}
	
	$GLOBALS['ec_store_shortcode_options'] = array( $menuid, $submenuid, $subsubmenuid, $manufacturerid, $groupid, $modelnumber );
	
	ob_start();
    $store_page = new ec_storepage( $menuid, $submenuid, $subsubmenuid, $manufacturerid, $groupid, $modelnumber );
	$store_page->display_store_page();
    return ob_get_clean();

}

//[ec_cart]
function load_ec_cart( $atts ){
	
	if( !get_option( 'ec_option_cache_prevent' ) ){
        if( !defined( 'DONOTCACHEPAGE' ) )
            define( "DONOTCACHEPAGE", true );

        if( !defined( 'DONOTCDN' ) )
            define('DONOTCDN', true);
    }
	
	extract( shortcode_atts( array(
		'language' => 'NONE'
	), $atts ) );
	
	if( $language != 'NONE' ){
		$GLOBALS['language'] = new ec_language( $language );
	}
	
	ob_start( );
	if( get_option( 'ec_option_cache_prevent' ) ){
        wp_easycart_dynamic_cart_display( );
    }else{
	   $cart_page = new ec_cartpage( );
	   $cart_page->display_cart_page( );
    }
	return ob_get_clean( );
}

function wp_easycart_dynamic_cart_display( ){
    $ec_db = new ec_db( );
    if( isset( $_GET['ec_page'] ) ){
        if( $_GET['ec_page'] == 'checkout_success' ){
            $cart_page = 6;
        }else if( $_GET['ec_page'] == 'checkout_info' ){
            $cart_page = 2;
        }else if( $_GET['ec_page'] == 'checkout_shipping' ){
            $cart_page = 3;
        }else if( $_GET['ec_page'] == 'checkout_payment' ){
            $cart_page = 4;
            if( isset( $_GET['ideal'] ) && $_GET['ideal'] == 'returning' && isset( $_GET['client_secret'] ) && isset( $_GET['source'] ) ){
                $source = htmlspecialchars( $_GET['source'], ENT_QUOTES );
                $client_secret = htmlspecialchars( $_GET['client_secret'], ENT_QUOTES );
                $cart_page .= '-ideal' . '-' . $source . '-' . $client_secret;
            }
        }else if( $_GET['ec_page'] == 'subscription_info' ){
            $cart_page = 1;
            global $wpdb;
            $model_number = preg_replace( "/[^A-Za-z0-9\-\_]/", '', $_GET['subscription'] );
            $products = $ec_db->get_product_list( $wpdb->prepare( " WHERE product.model_number = %s", $model_number ), "", "", "" );
            if( count( $products ) > 0 ){
                $cart_page = 5;
                $product_id = $products[0]['product_id'];
            }
        }
    }else{
        $cart_page = 1;
    }
    $error_codes = array( "email_exists", "login_failed", "3dsecure_failed", "manualbill_failed", "thirdparty_failed", "payment_failed", "card_error", "already_subscribed", "not_activated", "subscription_not_found", "user_insert_error", "subscription_added_failed", "subscription_failed", "invalid_address", "session_expired", "invalid_vat_number", "stock_invalid", "ideal-pending" );
    echo '<div id="wpeasycart_cart_holder" style="position:relative; width:100%; min-height:350px;"><style>
    @keyframes rotation{
        0%  { transform:rotate(0deg); }
        100%{ transform:rotate(359deg); }
    }
    </style>
    <div style=\'font-family: "HelveticaNeue", "HelveticaNeue-Light", "Helvetica Neue Light", helvetica, arial, sans-serif; font-size: 14px; text-align: center; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; -ms-box-sizing: border-box; box-sizing: border-box; width: 350px; top: 50%; left: 50%; position: absolute; margin-left: -165px; margin-top: -80px; cursor: pointer; text-align: center;\'>
        <div>
            <div style="height: 30px; width: 30px; display: inline-block; box-sizing: content-box; opacity: 1; filter: alpha(opacity=100); -webkit-animation: rotation .7s infinite linear; -moz-animation: rotation .7s infinite linear; -o-animation: rotation .7s infinite linear; animation: rotation .7s infinite linear; border-left: 8px solid rgba(0, 0, 0, .2); border-right: 8px solid rgba(0, 0, 0, .2); border-bottom: 8px solid rgba(0, 0, 0, .2); border-top: 8px solid #fff; border-radius: 100%;"></div>
        </div>
    </div></div><script type="text/javascript">jQuery( document ).ready( function( ){ wpeasycart_load_cart( \'' . $cart_page . ( ( isset( $_GET['order_id'] ) ) ? '-'.(int) $_GET['order_id'] : '' ) . ( ( isset( $_GET['PID'] ) && $_GET['PID'] != '' ) ? '-paypal-' . preg_replace( "/[^A-Za-z0-9\-]/", '', $_GET['PID'] ) . '-' . preg_replace( "/[^A-Za-z0-9\-]/", '', $_GET['PYID'] ) : '' ) . ( ( isset( $_GET['OID'] ) && $_GET['OID'] != '' ) ? '-paypal-' . preg_replace( "/[^A-Z0-9]/", '', $_GET['OID'] ) . '-' . preg_replace( "/[^A-Z0-9]/", '', $_GET['PYID'] ) : '' ) . ( ( $cart_page == 5 ) ? '-sub-' . $product_id : '' ) . '\', \'' . ( ( isset( $_GET['ec_cart_success'] ) && $_GET['ec_cart_success'] == 'account_created' ) ? 'account_created' : '' ) . '\', \'' . ( ( isset( $_GET['ec_cart_error'] ) && in_array( $_GET['ec_cart_error'], $error_codes ) ) ? $_GET['ec_cart_error'] : '' ) . '\' ); } );</script>';
}

//[ec_account]
function load_ec_account( $atts ){
	
	if( !get_option( 'ec_option_cache_prevent' ) ){
        if( !defined( 'DONOTCACHEPAGE' ) )
            define( "DONOTCACHEPAGE", true );

        if( !defined( 'DONOTCDN' ) )
            define('DONOTCDN', true);
    }
	
	extract( shortcode_atts( array(
		'language' => 'NONE',
		'redirect' => false
	), $atts ) );
	
	if( $language != 'NONE' ){
		$GLOBALS['language'] = new ec_language( $language );
	}
	
	ob_start( );
    if( isset( $_POST['ec_form_action'] ) ){
        $account_page = new ec_accountpage( $redirect );
        $account_page->process_form_action( $_POST['ec_form_action'] );	
    
    }else if( get_option( 'ec_option_cache_prevent' ) ){
        wp_easycart_dynamic_account_display( );
        
    }else{
        $account_page = new ec_accountpage( $redirect );
        $account_page->display_account_page( );
    }
    return ob_get_clean();
}

function wp_easycart_dynamic_account_display( ){
    $account_page = '';
    $pages = array( 'forgot_password', 'register', 'billing_information', 'shipping_information', 'personal_information', 'password', 'orders', 'order_details', 'subscription', 'subscriptions', 'subscription_details' );
    if( isset( $_GET['ec_page'] ) && in_array( $_GET['ec_page'], $pages ) ){
        $account_page = $_GET['ec_page'];
    }
    echo '<div id="wpeasycart_account_holder" style="position:relative; width:100%; min-height:350px;"><style>
    @keyframes rotation{
        0%  { transform:rotate(0deg); }
        100%{ transform:rotate(359deg); }
    }
    </style>
    <div style=\'font-family: "HelveticaNeue", "HelveticaNeue-Light", "Helvetica Neue Light", helvetica, arial, sans-serif; font-size: 14px; text-align: center; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; -ms-box-sizing: border-box; box-sizing: border-box; width: 350px; top: 50%; left: 50%; position: absolute; margin-left: -165px; margin-top: -80px; cursor: pointer; text-align: center;\'>
        <div>
            <div style="height: 30px; width: 30px; display: inline-block; box-sizing: content-box; opacity: 1; filter: alpha(opacity=100); -webkit-animation: rotation .7s infinite linear; -moz-animation: rotation .7s infinite linear; -o-animation: rotation .7s infinite linear; animation: rotation .7s infinite linear; border-left: 8px solid rgba(0, 0, 0, .2); border-right: 8px solid rgba(0, 0, 0, .2); border-bottom: 8px solid rgba(0, 0, 0, .2); border-top: 8px solid #fff; border-radius: 100%;"></div>
        </div>
    </div></div><script type="text/javascript">jQuery( document ).ready( function( ){ ';
    $valid_success_codes = array( 'validation_required', 'reset_email_sent', 'personal_information_updated', 'billing_information_updated', 'billing_information_updated', 'shipping_information_updated', 'shipping_information_updated', 'subscription_updated', 'subscription_updated', 'subscription_canceled', 'cart_account_created' );
    $valid_error_codes = array( 'register_email_error', 'not_activated', 'login_failed', 'register_email_error', 'register_invalid', 'no_reset_email_found', 'personal_information_update_error', 'password_no_match', 'password_wrong_current', 'billing_information_error', 'shipping_information_error', 'subscription_update_failed', 'subscription_cancel_failed' );
    $success_code = ( isset( $_GET['account_success'] ) && in_array( $_GET['account_success'], $valid_success_codes ) ) ? $_GET['account_success'] : '';
    $error_code = ( isset( $_GET['account_error'] ) && in_array( $_GET['account_error'], $valid_error_codes ) ) ? $_GET['account_error'] : '';
    if( $account_page == 'order_details' && isset( $_GET['order_id'] ) && isset( $_GET['ec_guest_key'] ) ){
        echo 'wpeasycart_load_account( \'' . $account_page . '-' . (int) $_GET['order_id'] . '-' . substr( preg_replace( '/[^A-Z]/', '', $_GET['ec_guest_key'] ), 0, 30 ) . '\', ' . get_queried_object_id( ) . ', \'' . $success_code . '\', \'' . $error_code . '\' ); } );';
    
    }else if( $account_page == 'order_details' && isset( $_GET['order_id'] ) ){
        echo 'wpeasycart_load_account( \'' . $account_page . '-' . (int) $_GET['order_id']. '\', ' . get_queried_object_id( ) . ', \'' . $success_code . '\', \'' . $error_code . '\' ); } );';
    
    }else if( $account_page == 'subscription_details' && isset( $_GET['subscription_id'] ) ){
        echo 'wpeasycart_load_account( \'' . $account_page . '-' . (int) $_GET['subscription_id'] . '\', ' . get_queried_object_id( ) . ', \'' . $success_code . '\', \'' . $error_code . '\' ); } );';
    
    }else{
        echo 'wpeasycart_load_account( \'' . $account_page . '\', ' . get_queried_object_id( ) . ', \'' . $success_code . '\', \'' . $error_code . '\' ); } );';
    
    }
    echo '</script>';
}

//[ec_product]
function load_ec_product( $atts ){
	extract( shortcode_atts( array(
		'model_number' => 'NOPRODUCT',
		'productid' => 'NOPRODUCTID',
		'columns' => '3',
		'margin' => '45px',
		'width' => '175px',
		'minheight' => '375px',
		'imagew' => '140px',
		'imageh' => '140px',
		'style' => '1'
	), $atts ) );
	$simp_product_id = $model_number;
	ob_start( );
    $mysqli = new ec_db( );
	if( $model_number != "NOPRODUCT" ){
		$products = $mysqli->get_product_list( " WHERE product.model_number = '" . $model_number . "'", "", "", "" );
	}else{
		$product_ids = explode( ',', $productid );
		$product_where = " WHERE ";
		$product_order = " ORDER BY ";
		$ids = 0;
		foreach( $product_ids as $product_id ){
			if( $ids > 0 ){
				$product_where .= " OR ";
				$product_order .= ", ";
			}
			$product_where .= "product.product_id = " . $product_id;
			$product_order .= "product.product_id = " . $product_id . " DESC";
			$ids++;
		}
		$products = $mysqli->get_product_list( $product_where, $product_order, "", "" );
	}
	if( count( $products ) > 0 ){
		
		$cart_page_id = get_option('ec_option_cartpage');
		if( function_exists( 'icl_object_id' ) ){
			$cart_page_id = icl_object_id( $cart_page_id, 'page', true, ICL_LANGUAGE_CODE );
		}
		$cart_page = get_permalink( $cart_page_id );
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$cart_page = $https_class->makeUrlHttps( $cart_page );
		}
		
		echo "<div class=\"ec_product_shortcode\" style=\"float:left; width:100%;\"><div class=\"ec_product_added_to_cart\"><div class=\"ec_product_added_icon\"></div><a href=\"" . $cart_page . "\" title=\"View Cart\">" . $GLOBALS['language']->get_text( "product_page", "product_view_cart" ) . "</a> " . $GLOBALS['language']->get_text( "product_page", "product_product_added_note" ) . "</div><div id=\"ec_current_media_size\"></div><ul class=\"ec_productlist_ul\" style=\"list-style:none; margin: 0px; float:left; width:100%; min-height:" . $minheight . ";\">";
		
		for( $prod_index=0; $prod_index<count( $products ); $prod_index++ ){
			$product = new ec_product( $products[$prod_index], 0, 0, 1 );
			if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/admin_panel.php" ) ){
				if( $prod_index%$columns == $columns-1 ){
					echo "<li style=\"float:right;\">";
				}else{
					echo "<li style=\"float:left; margin-right:" . $margin . ";\">";
				}
			}
			
			if( $style == '1' ){
				if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_product.php' ) )
					include( WP_PLUGIN_DIR . "/" . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_product.php' );
				else
					include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_product.php' );
			
			}else if( $style == '2' ){
				if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_product_widget.php' ) )
					include( WP_PLUGIN_DIR . "/" . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_product_widget.php' );
				else
					include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_product_widget.php' );
				
			}else{
				echo "<a href=\"" . $product->get_product_link( ) . "\">";
				echo "<img src=\"" . $product->get_product_single_image( ) . "\" alt=\"" . $product->title . "\" width=\"" . $imagew . "\" height=\"" . $imageh . "\">";
				echo "</a>";
				echo "<h3><a href=\"" . $product->get_product_link( ) . "\">" . $product->title . "</a></h3>";
				echo "<span class=\"ec_price_button\" style=\"width:" . $width . "\">";
				if( $product->has_sale_price( ) ){
					echo "<span class=\"ec_price_before\"><del>" . $product->get_formatted_before_price( ) . "</del></span>";
					echo "<span class=\"ec_price_sale\">" . $product->get_formatted_price( ) . "</span>";
				}else{
					echo "<span class=\"ec_price\">" . $product->get_formatted_price( ) . "</span>";
				}
				echo "</span>";
			}
			if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/admin_panel.php" ) ){
				echo "</li>";
			}
		}
		echo "</ul><div style=\"clear:both;\"></div></div>";
	}
    return ob_get_clean( );
}

//[ec_addtocart]
function load_ec_addtocart( $atts ){
	extract( shortcode_atts( array(
		'productid' => 'NOPRODUCTID'
	), $atts ) );
	ob_start( );
	$mysqli = new ec_db( );
	$products = $mysqli->get_product_list( " WHERE product.product_id = " . $productid, "", "", "" );
	if( count( $products ) > 0 ){
		$product = new ec_product( $products[0], 0, 1, 1 );
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_add_to_cart_shortcode.php' ) )
			include( WP_PLUGIN_DIR . "/" . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_add_to_cart_shortcode.php' );
		else
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_add_to_cart_shortcode.php' );
	}
    return ob_get_clean( );
}

//[ec_cartdisplay]
function load_ec_cartdisplay( $atts ){
	
	ob_start( );
	$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cartdisplay_shortcode.php' ) )
		include( WP_PLUGIN_DIR . "/" . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cartdisplay_shortcode.php' );
	else
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_cartdisplay_shortcode.php' );
	
    return ob_get_clean( );
}

//[ec_membership productid=''][/ec_membership]
function load_ec_membership( $atts, $content = NULL ){
	extract( shortcode_atts( array(
		'productid' => '',
		'userroles' => ''
	), $atts ) );
	
	if( current_user_can( 'manage_options' ) ){
		
		return "<h4>MEMBER AND NON MEMBER CONTENT SHOWN TO ADMIN USER</h4><hr />" . do_shortcode( $content ) . "<hr />";
		
	}else if( $GLOBALS['ec_user']->user_id ){
		
		$db = new ec_db( );
		$is_member = false;
		
		if( $productid != '' ){
			$is_member = $db->has_membership_product_ids( $productid );
			
		}
		
		if( $userroles != '' ){
			$user_role_array = explode( ',', $userroles );
			
			if( in_array( $GLOBALS['ec_user']->user_level, $user_role_array ) )
				$is_member = true;
			
		}
		
		if( $is_member )
			return do_shortcode( $content );
			
		else
			return "";
		
	}
	
}

//[ec_membership_alt productid=''][/ec_membership_alt]
function load_ec_membership_alt( $atts, $content = NULL ){
	extract( shortcode_atts( array(
		'productid' => '',
		'userroles' => ''
	), $atts ) );
	
	if( current_user_can( 'manage_options' ) ){
		
		return "<h4>NON-MEMBER CONTENT (WORDPRESS ADMIN DISPLAY ONLY)</h4><hr />" . do_shortcode( $content ) . "<hr />";
		
	}else if( $GLOBALS['ec_user']->user_id ){
	
		$db = new ec_db( );
		$is_member = false;
		
		if( $productid != '' ){
			$is_member = $db->has_membership_product_ids( $productid );
			
		}
		
		if( $userroles != '' ){
			$user_role_array = explode( ',', $userroles );
			
			if( in_array( $GLOBALS['ec_user']->user_level, $user_role_array ) )
				$is_member = true;
			
		}
		
	
		if( !$is_member )
			return do_shortcode( $content );
			
		else
			return "";
		
	}else{
	
		return do_shortcode( "[ec_account redirect='" . get_the_ID( ) . "']" ) . do_shortcode( $content );
	
	}
	
}

//[ec_store_table]
function load_ec_store_table_display( $atts ){
	
	global $wpdb;
	
	extract( shortcode_atts( array(
		'productid' => '',
		'menuid' => '',
		'submenuid' => '',
		'subsubmenuid' => '',
		'categoryid' => '',
		'labels' => 'Model Number,Product Name,Price,',
		'columns' => 'model_number,title,price,details_link',
		'view_details' => 'VIEW DETAILS'
	), $atts ) );
	
	$label_start = explode( ",", $labels );
	$columns_start = explode( ",", $columns );
	
	$columns = array( );
	$labels = array( );
	
	for( $k=0; $k<count($columns_start); $k++ ){
		if( $columns_start[$k] != '0' ){
			$columns[] = $columns_start[$k];
			$labels[] = $label_start[$k];
		}
	}
	
	$storepageid = get_option('ec_option_storepage');
	
	if( function_exists( 'icl_object_id' ) ){
		$storepageid = icl_object_id( $storepageid, 'page', true, ICL_LANGUAGE_CODE );
	}
	
	$storepage = get_permalink( $storepageid );
			
	if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
		$https_class = new WordPressHTTPS( );
		$storepage = $https_class->makeUrlHttps( $storepage );
	}
	
	if(substr_count($storepage, '?'))							$permalink_divider = "&";
	else														$permalink_divider = "?";
	
	$product_ids = array( );
	$menu_ids = array( );
	$submenu_ids = array( );
	$subsubmenu_ids = array( );
	$category_ids = array( );
	
	if( $productid != '' ){
		$product_ids = explode( ",", $productid );
	}
	
	if( $menuid != '' ){
		$menu_ids = explode( ",", $menuid );
	}
	
	if( $submenuid != '' ){
		$submenu_ids = explode( ",", $submenuid );
	}
	
	if( $subsubmenuid != '' ){
		$subsubmenu_ids = explode( ",", $subsubmenuid );
	}
	
	if( $categoryid != '' ){
		$category_ids = explode( ",", $categoryid );
	}
	
	$has_added_to_where = false;
	$where_query = "";
	if( count( $product_ids ) > 0 || 
		count( $menu_ids ) > 0 || 
		count( $submenu_ids ) > 0 || 
		count( $subsubmenu_ids ) > 0 || 
		count( $category_ids ) > 0 ){
		
		$where_query = " WHERE";
				
	}
	
	if( count( $product_ids ) > 0 ){
		if( !$has_added_to_where )
			$where_query .= " (";
		else
			$where_query .= " OR (";
		
		for( $i=0; $i<count( $product_ids ); $i++ ){
			if( $i > 0 )
				$where_query .= " OR";
			$where_query .= $wpdb->prepare( " product.product_id = %d", $product_ids[$i] );
		}
		$where_query .= ")";
		$has_added_to_where = true;
	}
	
	if( count( $menu_ids ) > 0 ){
		if( !$has_added_to_where )
			$where_query .= " (";
		else
			$where_query .= " OR (";
		
		for( $i=0; $i<count( $menu_ids ); $i++ ){
			if( $i > 0 )
				$where_query .= " OR";
			
			$where_query .= $wpdb->prepare( " ( product.menulevel1_id_1 = %d OR product.menulevel2_id_1 = %d OR product.menulevel3_id_1 = %d )", $menu_ids[$i], $menu_ids[$i], $menu_ids[$i] );
		}
		$where_query .= ")";
		$has_added_to_where = true;
	}
	
	if( count( $submenu_ids ) > 0 ){
		if( !$has_added_to_where )
			$where_query .= " (";
		else
			$where_query .= " OR (";
		
		for( $i=0; $i<count( $submenu_ids ); $i++ ){
			if( $i > 0 )
				$where_query .= " OR";
			
			$where_query .= $wpdb->prepare( " ( product.menulevel1_id_2 = %d OR product.menulevel2_id_2 = %d OR product.menulevel3_id_2 = %d )", $submenu_ids[$i], $submenu_ids[$i], $submenu_ids[$i] );
		}
		$where_query .= ")";
		$has_added_to_where = true;
	}
	
	if( count( $subsubmenu_ids ) > 0 ){
		if( !$has_added_to_where )
			$where_query .= " (";
		else
			$where_query .= " OR (";
		
		for( $i=0; $i<count( $subsubmenu_ids ); $i++ ){
			if( $i > 0 )
				$where_query .= " OR";
			
			$where_query .= $wpdb->prepare( " ( product.menulevel1_id_3 = %d OR product.menulevel2_id_3 = %d OR product.menulevel3_id_3 = %d )", $subsubmenu_ids[$i], $subsubmenu_ids[$i], $subsubmenu_ids[$i] );
		}
		$where_query .= ")";
		$has_added_to_where = true;
	}
	
	if( count( $category_ids ) > 0 ){
		if( !$has_added_to_where )
			$where_query .= " (";
		else
			$where_query .= " OR (";
		
		for( $i=0; $i<count( $category_ids ); $i++ ){
			if( $i > 0 )
				$where_query .= " OR";
			
			$where_query .= $wpdb->prepare( " ec_categoryitem.category_id = %d", $category_ids[$i] );
		}
		$where_query .= ")";
		$has_added_to_where = true;
	}
	$order_query = " ORDER BY product.title ASC";
	$limit_query = "";
	$session_id = $GLOBALS['ec_cart_id'];
	
	$db = new ec_db( );
	$products = $db->get_product_list( $where_query, $order_query, $limit_query, $session_id  );
	
	ob_start( );
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_store_table_display.php' ) )
		include( WP_PLUGIN_DIR . "/" . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_store_table_display.php' );
	else
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_store_table_display.php' );
	
    return ob_get_clean( );
}

//[ec_category_view]
function load_ec_category_view( $atts ){
	
	extract( shortcode_atts( array(
		'parentid' => '0',
		'columns' => 2
	), $atts ) );
	
	ob_start( );
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_category_view.php' ) )
		include( WP_PLUGIN_DIR . "/" . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_category_view.php' );
	else
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_category_view.php' );
	
    return ob_get_clean( );
	
}

//[ec_categories]
function load_ec_categories( $atts ){
	
	if( !defined( 'DONOTCACHEPAGE' ) )
		define( "DONOTCACHEPAGE", true );
	
	if( !defined( 'DONOTCDN' ) )
		define('DONOTCDN', true);
	
	extract( shortcode_atts( array(
		'menuid' => 'NOMENU',
		'submenuid' => 'NOSUBMENU',
		'subsubmenuid' => 'NOSUBSUBMENU',
		'manufacturerid' => 'NOMANUFACTURER',
		'groupid' => 'NOGROUP',
		'modelnumber' => 'NOMODELNUMBER',
		'language' => 'NONE'
	), $atts ) );
	
	if( $language != 'NONE' ){
		$GLOBALS['language'] = new ec_language( $language );
	}
	
	$GLOBALS['ec_store_shortcode_options'] = array( $menuid, $submenuid, $subsubmenuid, $manufacturerid, $groupid, $modelnumber );
	
	ob_start();
    $store_page = new ec_storepage( $menuid, $submenuid, $subsubmenuid, $manufacturerid, $groupid, $modelnumber );
	$store_page->display_category_page();
    return ob_get_clean();
	
}

function ec_plugins_loaded( ){
	/* Admin Form Actions */
	if( current_user_can('manage_options') && isset( $_GET['ec_action'] ) && isset( $_GET['ec_language'] ) && $_GET['ec_action'] == "export-language" ){
		$language = new ec_language( );
		$language->export_language( $_GET['ec_language'] );
		die( );
	}
}

function ec_footer_load( ){
	if( get_option( 'ec_option_enable_newsletter_popup' ) && !isset( $_COOKIE['ec_newsletter_popup'] ) ){
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_newsletter_popup.php' ) )	
			include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_newsletter_popup.php' );
		else
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_newsletter_popup.php' );
		
	}
}

add_action( 'wp', 'load_ec_pre' );
add_action( 'wp_enqueue_scripts', 'ec_load_css' );
add_action( 'wp_enqueue_scripts', 'ec_load_js' );
add_action( 'send_headers', 'ec_custom_headers' );
add_action( 'plugins_loaded', 'ec_plugins_loaded' );
add_action( 'wp_footer', 'ec_footer_load' );

if( !is_admin( ) ){
	add_shortcode( 'ec_store', 'load_ec_store' );
	add_shortcode( 'ec_cart', 'load_ec_cart' );
	add_shortcode( 'ec_account', 'load_ec_account' );
	add_shortcode( 'ec_product', 'load_ec_product' );
	add_shortcode( 'ec_addtocart', 'load_ec_addtocart' );
	add_shortcode( 'ec_cartdisplay', 'load_ec_cartdisplay' );
	add_shortcode( 'ec_membership', 'load_ec_membership' );
	add_shortcode( 'ec_membership_alt', 'load_ec_membership_alt' );
	add_shortcode( 'ec_store_table', 'load_ec_store_table_display' );
	add_shortcode( 'ec_category_view', 'load_ec_category_view' );
	add_shortcode( 'ec_categories', 'load_ec_categories' );
}

add_filter( 'widget_text', 'do_shortcode');

add_action( 'wp_head', 'wpeasycart_seo_tags' );
add_action('wp_head', 'ec_theme_head_data');
add_action( 'wp_head', 'wpeasycart_order_completed' );
function wpeasycart_order_completed( ){
	// Checkout Success Check.
	if( isset( $_GET['ec_page'] ) && $_GET['ec_page'] == "checkout_success" && isset( $_GET['order_id'] ) ){
		// Try and get order and run action
		$ec_db = new ec_db_admin( );
		$order_id = $_GET['order_id'];
		if( $GLOBALS['ec_cart_data']->cart_data->is_guest ){
			$order_row = $ec_db->get_guest_order_row( $order_id, $GLOBALS['ec_cart_data']->cart_data->guest_key );
		}else{
			$order_row = $ec_db->get_order_row( $order_id, $GLOBALS['ec_cart_data']->cart_data->user_id );
		}
		if( $order_row ){ // order found and valid for user
			$order = new ec_orderdisplay( $order_row, true );
			do_action( 'wpeasycart_order_success_pre', $order_id, $order_row, $order->orderdetails );
		}
	}
}

add_action( 'wp_enqueue_scripts', 'ec_load_dashicons' );
function ec_load_dashicons() {
    wp_enqueue_style( 'dashicons' );
}

//////////////////////////////////////////////
//UPDATE FUNCTIONS
//////////////////////////////////////////////

function wpeasycart_copyr( $source, $dest ){
    
	// Check for symlinks
    if( is_link( $source ) ){
        return symlink( readlink( $source ), $dest );
    }

    // Simple copy for a file
    if( is_file( $source ) ){
		$success = copy( $source, $dest );
		if( $success ){
 	       return true;
		}else{
			$err_message = "wpeasycart - error backing up " . $source . ". Updated halted.";
			error_log( $err_message );
			exit( $err_message );
		}
	}

    // Make destination directory
    if ( !is_dir( $dest ) ){
        $success = mkdir( $dest, 0755 );
		if( !$success ){
			$err_message = "wpeasycart - error creating backup directory: " . $dest . ". Updated halted.";
			error_log( $err_message );
			exit( $err_message );
		}
    }

    // Loop through the folder
    $dir = dir( $source );
    while( false !== $entry = $dir->read( ) ){
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        wpeasycart_copyr( "$source/$entry", "$dest/$entry" ); // <------- defines wpeasycart copy action
    }

    // Clean up
    $dir->close( );
    return true;
}

function wpeasycart_backup( ){
	// Test for data folder
	if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/" ) ){
		echo "YOU DO NOT HAVE A WP EASYCART DATA FOLDER, PLEASE <a href=\"http://www.wpeasycart.com/plugin-update-help/\" target=\"_blank\">CLICK HERE TO READ HOW TO PREVENT DATA LOSS DURING THE UPDATE</a>";
		die( );
	}
}

function ec_recursive_remove_directory( $directory, $empty=FALSE ) {
     // if the path has a slash at the end we remove it here
     if( substr( $directory, -1 ) == '/' )
         $directory = substr( $directory, 0, -1);
  
     // if the path is not valid or is not a directory ...
     if( !file_exists( $directory ) || !is_dir( $directory ) )
         return FALSE;
  
     // ... if the path is not readable
     elseif(!is_readable($directory))
         return FALSE;
  
     // ... else if the path is readable
     else{
  
         // we open the directory
         $handle = opendir( $directory );
  
         // and scan through the items inside
         while( FALSE !== ( $item = readdir( $handle ) ) ){
             // if the filepointer is not the current directory
             // or the parent directory
             if( $item != '.' && $item != '..' ){
                 // we build the new path to delete
                 $path = $directory . '/' . $item;
  
                 // if the new path is a directory
                 if( is_dir( $path ) ){
                     // we call this function with the new path
                    ec_recursive_remove_directory( $path );

                 // if the new path is a file
                 }else{
                     // we remove the file
                     unlink( $path );
                 }
             }
         }
         // close the directory
         closedir( $handle );
		  
         // if the option to empty is not set to true
         if( $empty == FALSE ){
             // try to delete the now empty directory
             if( !rmdir( $directory ) ){
                 // return false if not possible
                 return FALSE;
             }
         }
         // return success
         return TRUE;
    }
}
 
function ec_delete_directory_ftp( $resource, $path ) {
    $result_message = "";
    $list = ftp_nlist( $resource, $path );
	
	if ( empty($list) ) {
        $list = ec_ran_list_n( ftp_rawlist($resource, $path), $path . ( substr($path, strlen($path) - 1, 1) == "/" ? "" : "/" ) );
    }
    if ($list[0] != $path) {
        $path .= ( substr($path, strlen($path)-1, 1) == "/" ? "" : "/" );
        foreach ($list as $item) {
			if ($item != $path.".." && $item != $path.".") {
				$result_message .= ec_delete_directory_ftp($resource, $item);
			}
        }
        if (ftp_rmdir ($resource, $path)) {
            $result_message .= "Successfully deleted $path <br />\n";
        } else {
            $result_message .= "There was a problem while deleting $path <br />\n";
        }
    }
    else {
		$res = ftp_site( $resource, 'CHMOD 0777 ' . $path );
        if (ftp_delete ($resource, $path)) {
            $result_message .= "Successfully deleted $path <br />\n";
        } else {
            $result_message .= "There was a problem while deleting $path <br />\n";
        }
    }
    return $result_message;
}

function ec_ran_list_n($rawlist, $path) {
    $array = array();
    foreach ($rawlist as $item) {
        $filename = trim(substr($item, 55, strlen($item) - 55));
        if ($filename != "." || $filename != "..") {
        $array[] = $path . $filename;
        }
    }
    return $array;
}

add_filter( 'upgrader_pre_install', 'wpeasycart_backup', 10, 2 );

//////////////////////////////////////////////
//END UPDATE FUNCTIONS
//////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////
//AJAX SETUP FUNCTIONS
/////////////////////////////////////////////////////////////////////
add_action( 'wp_ajax_ec_ajax_get_optionitem_quantities', 'ec_ajax_get_optionitem_quantities' );
add_action( 'wp_ajax_nopriv_ec_ajax_get_optionitem_quantities', 'ec_ajax_get_optionitem_quantities' );
function ec_ajax_get_optionitem_quantities( ){
	
	$db = new ec_db( );
	
	$product_id = $_POST['product_id'];
	$optionitem_id_1 = $_POST['optionitem_id_1'];
	
	if( isset( $_POST['optionitem_id_2'] ) )
		$optionitem_id_2 = $_POST['optionitem_id_2'];
	else{
		$quantity_values = $db->get_option2_quantity_values( $product_id, $optionitem_id_1 );
		echo json_encode( $quantity_values );
		
		die( );
	}
	
	if( isset( $_POST['optionitem_id_3'] ) )
		$optionitem_id_3 = $_POST['optionitem_id_3'];
	else{
		$quantity_values = $db->get_option3_quantity_values( $product_id, $optionitem_id_1, $optionitem_id_2 );
		echo json_encode( $quantity_values );
		
		die( );
	}
	
	if( isset( $_POST['optionitem_id_4'] ) )
		$optionitem_id_4 = $_POST['optionitem_id_4'];
	else{
		$quantity_values = $db->get_option4_quantity_values( $product_id, $optionitem_id_1, $optionitem_id_2, $optionitem_id_3 );
		echo json_encode( $quantity_values );
		
		die( );
	}
	
	
	$quantity_values = $db->get_option5_quantity_values( $product_id, $optionitem_id_1, $optionitem_id_2, $optionitem_id_3, $optionitem_id_4 );
	echo json_encode( $quantity_values );
	
	die( );
	
}

add_action( 'wp_ajax_ec_ajax_add_to_cart', 'ec_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_ec_ajax_add_to_cart', 'ec_ajax_add_to_cart' );
function ec_ajax_add_to_cart( ){
	$cart_id = $GLOBALS['ec_cart_id'];
	if( isset( $_POST['cart_id'] ) )
		$cart_id = $_POST['cart_id'];
	
	wpeasycart_session( )->handle_session( $cart_id );
	
	$product_id = $_POST['product_id'];
	$model_number = $_POST['model_number'];
	$quantity = $_POST['quantity'];
	$db = new ec_db( );
	
	$tempcart = $db->add_to_cart( $product_id, $GLOBALS['ec_cart_data']->ec_cart_id, $quantity, 0, 0, 0, 0, 0, "", "", "", 0.00, false, 1 );
	wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	$cart_arr = array( );
	$total_items = 0;
	$total_cost = 0;
	
	foreach( $tempcart as $item ){
		$cart_arr[] = array( 'title' => $item->title, 'price' => $GLOBALS['currency']->get_currency_display( $item->unit_price ), 'quantity' => $item->quantity );
		$total_items = $total_items + $item->quantity;
		$total_cost = $total_cost + ( $item->quantity * $item->unit_price );
	}
	$cart_arr[0]['total_items'] = $total_items;
	$cart_arr[0]['total_price'] = $GLOBALS['currency']->get_currency_display( $total_cost );
	echo json_encode( $cart_arr );
	
	die( );
}

add_action( 'wp_ajax_ec_ajax_cartitem_update', 'ec_ajax_cartitem_update' );
add_action( 'wp_ajax_nopriv_ec_ajax_cartitem_update', 'ec_ajax_cartitem_update' );
function ec_ajax_cartitem_update( ){
	wpeasycart_session( )->handle_session( );
	
	// UPDATE CART ITEM
	$tempcart_id = $_POST['cartitem_id'];
	$session_id = $GLOBALS['ec_cart_data']->ec_cart_id;
	$quantity = $_POST['quantity'];
	
	if( is_numeric( $quantity ) ){
		$db = new ec_db();
		$db->update_cartitem( $tempcart_id, $session_id, $quantity );
		wp_cache_flush( );
		do_action( 'wpeasycart_cart_updated' );
	}
	// UPDATE CART ITEM
	
	// GET NEW CART ITEM INFO
	if( isset( $_POST['ec_v3_24'] ) ){
		$return_array = ec_get_cart_data( );
								
		echo json_encode( $return_array );
	}else{
		$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
		
		$unit_price = 0;
		$total_price = 0;
		$new_quantity = 0;
		for( $i=0; $i<count( $cart->cart ); $i++ ){
			if( $cart->cart[$i]->cartitem_id == $tempcart_id ){
				$unit_price = $cart->cart[$i]->unit_price;
				$total_price = $cart->cart[$i]->total_price;
				$new_quantity = $cart->cart[$i]->quantity;
			}
		}
		// GET NEW CART ITEM INFO
		$order_totals = ec_get_order_totals( $cart );
		
		echo $GLOBALS['currency']->get_currency_display( $unit_price ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $total_price ) . "***" . 
				$new_quantity . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->sub_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->tax_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->shipping_total ) . "***" .  
				$GLOBALS['currency']->get_currency_display( $order_totals->duty_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->vat_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( (-1) * $order_totals->discount_total ) . "***" .
				$GLOBALS['currency']->get_currency_display( $order_totals->grand_total );
				
		if( $cart->total_items > 0 ){
			
			if( $cart->total_items != 1 ){
				$items_label = $GLOBALS['language']->get_text( 'cart', 'cart_menu_icon_label_plural' );
			}else{
				$items_label = $GLOBALS['language']->get_text( 'cart', 'cart_menu_icon_label' );
			}
			
			echo "***" . $cart->total_items . ' ' . $items_label . ' ' . $GLOBALS['currency']->get_currency_display( $cart->subtotal );
		}else{
			echo "***" . $cart->total_items . ' ' . $items_label;
		}
		echo "***" . $cart->total_items;
	}
	
	die(); // this is required to return a proper result
}

add_action( 'wp_ajax_ec_ajax_cartitem_delete', 'ec_ajax_cartitem_delete' );
add_action( 'wp_ajax_nopriv_ec_ajax_cartitem_delete', 'ec_ajax_cartitem_delete' );
function ec_ajax_cartitem_delete( ){
	wpeasycart_session( )->handle_session( );
	
	//Get the variables from the AJAX call
	$tempcart_id = $_POST['cartitem_id'];
	$session_id = $GLOBALS['ec_cart_data']->ec_cart_id;
	
	// DELTE CART ITEM
	$db = new ec_db();
	$ret_data = $db->delete_cartitem( $tempcart_id, $session_id );
	wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	// GET NEW CART ITEM INFO
	if( isset( $_POST['ec_v3_24'] ) ){
		$return_array = ec_get_cart_data( );
								
		echo json_encode( $return_array );
	}else{
		$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$order_totals = ec_get_order_totals( $cart );
		
		echo $cart->total_items . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->sub_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->tax_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->shipping_total ) . "***" .  
				$GLOBALS['currency']->get_currency_display( $order_totals->duty_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->vat_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( (-1) * $order_totals->discount_total ) . "***" .
				$GLOBALS['currency']->get_currency_display( $order_totals->grand_total );
			
		if( $cart->total_items != 1 ){
			$items_label = $GLOBALS['language']->get_text( 'cart', 'cart_menu_icon_label_plural' );
		}else{
			$items_label = $GLOBALS['language']->get_text( 'cart', 'cart_menu_icon_label' );
		}
		
		if( $cart->total_items > 0 ){
			echo "***" . $cart->total_items . ' ' . $items_label . ' ' . $GLOBALS['currency']->get_currency_display( $cart->subtotal );
		
		}else{
			echo "***" . $cart->total_items . ' ' . $items_label;
		
		}
		echo "***" . $cart->total_items;
	}
	
	die(); // this is required to return a proper result
	
}

add_action( 'wp_ajax_ec_ajax_update_tip_amount', 'ec_ajax_update_tip_amount' );
add_action( 'wp_ajax_nopriv_ec_ajax_update_tip_amount', 'ec_ajax_update_tip_amount' );
function ec_ajax_update_tip_amount( ){
	wpeasycart_session( )->handle_session( );
	$session_id = $GLOBALS['ec_cart_data']->ec_cart_id;
	
	$GLOBALS['ec_cart_data']->cart_data->tip_amount = ( $_POST['tip_rate'] == 'custom' ) ? (float) $_POST['tip_amount'] : 0;
    $GLOBALS['ec_cart_data']->cart_data->tip_rate = ( $_POST['tip_rate'] == 'custom' ) ? 'custom' : (float) $_POST['tip_rate'];
    $GLOBALS['ec_cart_data']->save_session_to_db( );
    
    wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	// GET NEW CART ITEM INFO
    $return_array = ec_get_cart_data( );
    echo json_encode( $return_array );
	die( );
}

add_action( 'wp_ajax_ec_ajax_update_subscription_tax', 'ec_ajax_update_subscription_tax' );
add_action( 'wp_ajax_nopriv_ec_ajax_update_subscription_tax', 'ec_ajax_update_subscription_tax' );
function ec_ajax_update_subscription_tax( ){
	global $wpdb;
	$ec_db = new ec_db( );
	
	$GLOBALS['ec_cart_data']->cart_data->shipping_selector = (int) $_POST['shipping_selector'];
	
	$GLOBALS['ec_cart_data']->cart_data->vat_registration_number = preg_replace( '/[^a-zA-Z0-9\s]/', '', $_POST['vat_registration_number'] );
	$GLOBALS['ec_user']->vat_registration_number = preg_replace( '/[^a-zA-Z0-9\s]/', '', $_POST['vat_registration_number'] );
	$ec_db->update_user( $GLOBALS['ec_user']->user_id, preg_replace( '/[^a-zA-Z0-9\s]/', '', $_POST['vat_registration_number'] ) );
	
	$GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = strip_tags( $_POST['billing_address'] );
	$GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = strip_tags( $_POST['billing_address2'] );
	$GLOBALS['ec_cart_data']->cart_data->billing_city = strip_tags( $_POST['billing_city'] );
	$GLOBALS['ec_cart_data']->cart_data->billing_state = strip_tags( $_POST['billing_state'] );
	$GLOBALS['ec_cart_data']->cart_data->billing_zip = strip_tags( $_POST['billing_zip'] );
	$GLOBALS['ec_cart_data']->cart_data->billing_country = strip_tags( $_POST['billing_country'] );
	
	$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = strip_tags( $_POST['shipping_address'] );
	$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = strip_tags( $_POST['shipping_address2'] );
	$GLOBALS['ec_cart_data']->cart_data->shipping_city = strip_tags( $_POST['shipping_city'] );
	$GLOBALS['ec_cart_data']->cart_data->shipping_state = strip_tags( $_POST['shipping_state'] );
	$GLOBALS['ec_cart_data']->cart_data->shipping_zip = strip_tags( $_POST['shipping_zip'] );
	$GLOBALS['ec_cart_data']->cart_data->shipping_country = strip_tags( $_POST['shipping_country'] );
	
	$GLOBALS['ec_cart_data']->save_session_to_db( );
	wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	// Get and Print Order Totals
	$ec_db = new ec_db( );
	$products = $ec_db->get_product_list( $wpdb->prepare( " WHERE product.product_id = %d", (int) $_POST['product_id'] ), "", "", "" );
	$product = new ec_product( $products[0], 0, 1, 0 );
	
	if( !get_option( 'ec_option_subscription_one_only' ) && $GLOBALS['ec_cart_data']->cart_data->subscription_quantity != "" ){ 
		$subscription_quantity = $GLOBALS['ec_cart_data']->cart_data->subscription_quantity;
	}else{ 
		$subscription_quantity = 1; 
	}
	
	wpeasycart_taxcloud( )->setup_subscription_for_tax( $product, $subscription_quantity );
	
	// Create Promotion Multiplier for Options
	$option_promotion_multiplier = 1;
	$option_promotion_discount = 0;
	$promotions = $GLOBALS['ec_promotions']->promotions;
	for( $i=0; $i<count( $promotions ); $i++ ){
		if( $product->promotion_text == $promotions[$i]->promotion_name ){
			if( $promotions[$i]->price1 == 0 ){
				$option_promotion_multiplier = ( 100 - $promotions[$i]->percentage1 ) / 100;
			}else if( $promotions[$i]->price1 != 0 ){
				$option_promotion_discount = $promotions[$i]->price1;
			}
		}
	}
		
	// Get option item price adjustments
	$option_total = 0;
	$optionitem_list = $GLOBALS['ec_options']->get_all_optionitems( );
	$subscription_option1 = $subscription_option2 = $subscription_option3 = $subscription_option4 = $subscription_option5 = 0;
	if( ( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option1 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option1 != "" ) || 
		( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option2 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option2 != "" ) || 
		( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option3 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option3 != "" ) || 
		( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option4 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option4 != "" ) || 
		( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option5 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option5 != "" ) ){
		
		
		if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option1 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option1 != "" ){
			$subscription_option1 = $GLOBALS['ec_cart_data']->cart_data->subscription_option1;
		}
		
		if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option2 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option2 != "" ){
			$subscription_option2 = $GLOBALS['ec_cart_data']->cart_data->subscription_option2;
		}
		
		if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option3 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option3 != "" ){
			$subscription_option3 = $GLOBALS['ec_cart_data']->cart_data->subscription_option3;
		}
		
		if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option4 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option4 != "" ){
			$subscription_option4 = $GLOBALS['ec_cart_data']->cart_data->subscription_option4;
		}
		
		if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option5 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option5 != "" ){
			$subscription_option5 = $GLOBALS['ec_cart_data']->cart_data->subscription_option5;
		}
		
		if( $subscription_option1 != 0 ){
			$subscription_option1 = $GLOBALS['ec_options']->get_optionitem( $subscription_option1 );
			if( $subscription_option1->optionitem_price > 0 ){
				$option_total += $subscription_option1->optionitem_price;
			}
		}
		if( $subscription_option2 != 0 ){
			$subscription_option2 = $GLOBALS['ec_options']->get_optionitem( $subscription_option2 );
			if( $subscription_option2->optionitem_price > 0 ){
				$option_total += $subscription_option2->optionitem_price;
			}
		}
		if( $subscription_option3 != 0 ){
			$subscription_option3 = $GLOBALS['ec_options']->get_optionitem( $subscription_option3 );
			if( $subscription_option3->optionitem_price > 0 ){
				$option_total += $subscription_option3->optionitem_price;
			}
		}
		if( $subscription_option4 != 0 ){
			$subscription_option4 = $GLOBALS['ec_options']->get_optionitem( $subscription_option4 );
			if( $subscription_option4->optionitem_price > 0 ){
				$option_total += $subscription_option4->optionitem_price;
			}
		}
		if( $subscription_option5 != 0 ){
			$subscription_option5 = $GLOBALS['ec_options']->get_optionitem( $subscription_option5 );
			if( $subscription_option5->optionitem_price > 0 ){
				$option_total += $subscription_option5->optionitem_price;
			}
		}
	}
	
	$coupon = $GLOBALS['ec_coupons']->redeem_coupon_code( $GLOBALS['ec_cart_data']->cart_data->coupon_code );
	$coupon_code_invalid = true;
	$coupon_applicable = true;
	$coupon_exceeded_redemptions = false;
	$coupon_expired = false;
	
	if( !$coupon ){ // Invalid Coupon
		$coupon_code_invalid = false;
	}else if( $coupon->by_product_id && $coupon->product_id != $product->product_id ){ // Product does not match
		$coupon_applicable = false;
	}else if( $coupon->by_manufacturer_id && $coupon->manufacturer_id != $product->manufacturer_id  ){ // Manufacturer Does not Match
		$coupon_applicable = false;
	}else if( $coupon->by_category_id ){ // validate category id match
        $has_categories = $wpdb->get_results( $wpdb->prepare( "SELECT categoryitem_id FROM ec_categoryitem WHERE category_id = %d AND product_id = %d", $coupon->category_id, $product->product_id ) );
        if( !$has_categories ){
            $coupon_applicable = false;
        }
    }else if( $coupon->max_redemptions != 999 && $coupon->times_redeemed >= $coupon->max_redemptions ){
		$coupon_exceeded_redemptions = true;
	}else if( $coupon->coupon_expired ){
		$coupon_expired = true;
	}
	
	// If valid and applicable, set to cache.
	if( $coupon_applicable && !$coupon_exceeded_redemptions && !$coupon_expired ){
		if( $coupon->is_percentage_based ){
			$discount_amount = ( $product->price + $option_total ) * $subscription_quantity * ($coupon->promo_percentage/100);
		}else if( $coupon->is_dollar_based ){
			$discount_amount = $coupon->promo_dollar;
		}
		if( $discount_amount > ( ( $product->price + $option_total ) * $subscription_quantity ) )
			$discount_amount = ( ( $product->price + $option_total ) * $subscription_quantity );
	}
	
	if( $discount_amount <= 0 && $option_promotion_multiplier != 1 ){
		$discount_amount = ( ( ( $product->price + $option_total ) * $subscription_quantity ) ) * ( 1 - $option_promotion_multiplier );
	
	}else if( $discount_amount <= 0 && $option_promotion_discount > 0 ){
		$discount_amount = $option_promotion_discount;
	}
	
	$discount_amount = round( $discount_amount, 2 );
	
	$GLOBALS['ec_cart_data']->save_session_to_db( );
	wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	// Get and Print Order Totals
	$sub_total = ( ( $product->price + $option_total + $product->subscription_signup_fee ) * $subscription_quantity ) - $discount_amount;
	$tax_subtotal = ( $product->is_taxable ) ? $sub_total - ( $product->subscription_signup_fee * $subscription_quantity ) : 0;
	$vat_subtotal = ( $product->vat_rate > 0 ) ? $sub_total - ( $product->subscription_signup_fee * $subscription_quantity ) : 0;
	$ec_tax = new ec_tax( $sub_total, $tax_subtotal, $vat_subtotal, $GLOBALS['ec_cart_data']->cart_data->shipping_state, $GLOBALS['ec_cart_data']->cart_data->shipping_country, $GLOBALS['ec_user']->taxfree, 0, (object) array( 'cart' => array( $product ) ) );
	
	$tax_total = $ec_tax->tax_total;
	$vat_rate = $ec_tax->vat_rate;
	$vat_total = $ec_tax->vat_total;
	
	$coupon_message = '';
	$coupon_status = '';
	
	if( !$coupon_code_invalid ){
		$coupon_message = $GLOBALS['language']->get_text( 'cart_coupons', 'cart_invalid_coupon' );
		$coupon_status = "invalid";
	
	}else if( !$coupon_applicable ){
		$coupon_message = $GLOBALS['language']->get_text( 'cart_coupons', 'cart_not_applicable_coupon' );
		$coupon_status = "invalid";
	
	}else if( $coupon_exceeded_redemptions ){
		$coupon_message = $GLOBALS['language']->get_text( 'cart_coupons', 'cart_max_exceeded_coupon' );
		$coupon_status = "invalid";
		
	}else if( $coupon_expired ){
		$coupon_message = $GLOBALS['language']->get_text( 'cart_coupons', 'cart_coupon_expired' );
		$coupon_status = "invalid";
		
	}else{
		$cartpage = new ec_cartpage( );
		if( $cartpage->discount->coupon_matches <= 0 ){
			$coupon_message = $GLOBALS['language']->get_text( 'cart_coupons', 'coupon_not_applicable' );
		}else{
			$coupon_message = $coupon->message;
		}
		$coupon_status = "valid";
				
	}
	
    if( $product->trial_period_days > 0 ){
        $grand_total = ( $product->subscription_signup_fee * $subscription_quantity );
    }else if( $ec_tax->vat_included ){
        $grand_total = ( ( $product->price + $option_total + $product->subscription_signup_fee ) * $subscription_quantity ) - $discount_amount + $tax_total + $ec_tax->hst + $ec_tax->gst + $ec_tax->pst;
    }else{
        $grand_total = ( ( $product->price + $option_total + $product->subscription_signup_fee ) * $subscription_quantity ) - $discount_amount + $tax_total + $vat_total + $ec_tax->hst + $ec_tax->gst + $ec_tax->pst;
    }
    
	echo json_encode( array(
		'quantity'			=> $subscription_quantity, 
		'subtotal'			=> $GLOBALS['currency']->get_currency_display( $product->price * $subscription_quantity ),
		'has_tax'			=> ( $tax_total > 0 ) ? 1 : 0,
		'tax_total'			=> $GLOBALS['currency']->get_currency_display( $tax_total ), 
		'hst_total'			=> $GLOBALS['currency']->get_currency_display( $ec_tax->hst ),
		'hst_rate'			=> (string) $ec_tax->hst_rate,
		'pst_total'			=> $GLOBALS['currency']->get_currency_display( $ec_tax->pst ),
		'pst_rate'			=> (string) $ec_tax->pst_rate,
		'gst_total'			=> $GLOBALS['currency']->get_currency_display( $ec_tax->gst ),
		'gst_rate'			=> (string) $ec_tax->gst_rate,
		'discount_total'	=> $GLOBALS['currency']->get_currency_display( (-1) * $discount_amount ), 
		'has_vat'			=> ( $vat_total > 0 ) ? 1 : 0,
		'vat_total'			=> $GLOBALS['currency']->get_currency_display( $vat_total ),
		'grand_total'		=> $GLOBALS['currency']->get_currency_display( $grand_total ),
		'coupon_message'	=> $coupon_message,
		'coupon_status'		=> $coupon_status,
		'has_discount'		=> ( $discount_amount == 0 ) ? 0 : 1,
		'price_formatted'	=> $product->get_price_formatted( $subscription_quantity )
	) );
	
	die( ); // this is required to return a proper result
}

add_action( 'wp_ajax_ec_ajax_get_stripe_shipping_dynamic', 'ec_ajax_get_stripe_shipping_dynamic' );
add_action( 'wp_ajax_nopriv_ec_ajax_get_stripe_shipping_dynamic', 'ec_ajax_get_stripe_shipping_dynamic' );
function ec_ajax_get_stripe_shipping_dynamic( ){
	wpeasycart_session( )->handle_session( );
	
	// Update Shipping
	$GLOBALS['ec_cart_data']->cart_data->shipping_selector = 'true';
	$GLOBALS['ec_cart_data']->cart_data->shipping_first_name = strip_tags( $_POST['shippingAddress']['recipient'] );
	$GLOBALS['ec_cart_data']->cart_data->shipping_last_name = '';
	$GLOBALS['ec_cart_data']->cart_data->shipping_company_name = '';
	
	if( count( $_POST['shippingAddress']['addressLine'] ) > 0 )
		$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = $GLOBALS['ec_user']->shipping->address_line_1 = strip_tags( $_POST['shippingAddress']['addressLine'][0] );
	
	if( count( $_POST['shippingAddress']['addressLine'] ) > 1 )
		$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = $GLOBALS['ec_user']->shipping->address_line_2 = strip_tags( $_POST['shippingAddress']['addressLine'][1] );
	
	$GLOBALS['ec_cart_data']->cart_data->shipping_city = $GLOBALS['ec_user']->shipping->city = strip_tags( $_POST['shippingAddress']['city'] );
	$GLOBALS['ec_cart_data']->cart_data->shipping_state = $GLOBALS['ec_user']->shipping->state = strip_tags( $_POST['shippingAddress']['region'] );
	$GLOBALS['ec_cart_data']->cart_data->shipping_zip = $GLOBALS['ec_user']->shipping->zip = strip_tags( $_POST['shippingAddress']['postalCode'] );
	$GLOBALS['ec_cart_data']->cart_data->shipping_country = $GLOBALS['ec_user']->shipping->country = strip_tags( $_POST['shippingAddress']['country'] );
	$GLOBALS['ec_cart_data']->cart_data->shipping_phone = strip_tags( $_POST['shippingAddress']['phone'] );
	$GLOBALS['ec_cart_data']->save_session_to_db( );
	wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	// Get cart and totals
	$cartpage = new ec_cartpage( );
	$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
	$order_totals = ec_get_order_totals( $cart );
	
	if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
		$stripe = new ec_stripe( );
	else
		$stripe = new ec_stripe_connect( );
	$stripe->update_payment_intent_total( $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id, $order_totals );
	
	$displayItems = wpeasycart_get_cart_display_items( $cart, $order_totals, $order_totals->tax );
	
	$return_cart_data = ec_get_cart_data( );
	
	// Output new info
	$result = (object) array(
		'shipping_options' 	=> $cartpage->ec_cart_display_shipping_methods_stripe_dynamic( $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_standard' ),$GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_express' ) ),
		'display_items'		=> $displayItems,
		'total'				=> (int) round( ( $order_totals->grand_total * 100 ), 2 ),
		'cart_data'			=> $return_cart_data
	);
	echo json_encode( $result );
	die( );
}

add_action( 'wp_ajax_ec_ajax_get_stripe_shipping_option_dynamic', 'ec_ajax_get_stripe_shipping_option_dynamic' );
add_action( 'wp_ajax_nopriv_ec_ajax_get_stripe_shipping_option_dynamic', 'ec_ajax_get_stripe_shipping_option_dynamic' );
function ec_ajax_get_stripe_shipping_option_dynamic( ){
	wpeasycart_session( )->handle_session( );
	
	// Save Selected Method
	$GLOBALS['ec_cart_data']->cart_data->shipping_method = $_POST['shippingOption']['id'];
	if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == 'shipexpress' ){
		$GLOBALS['ec_cart_data']->cart_data->expedited_shipping = 'shipexpress';
	}else{
		$GLOBALS['ec_cart_data']->cart_data->expedited_shipping = '';
	}
	$GLOBALS['ec_cart_data']->save_session_to_db( );
	wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	// Get cart and totals
	$cartpage = new ec_cartpage( );
	$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
	$order_totals = ec_get_order_totals( $cart );
	
	if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
		$stripe = new ec_stripe( );
	else
		$stripe = new ec_stripe_connect( );
	$stripe->update_payment_intent_total( $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id, $order_totals );
	
	$displayItems = wpeasycart_get_cart_display_items( $cart, $order_totals, $order_totals->tax );
	
	$return_cart_data = ec_get_cart_data( );
	
	// Output new info
	$result = (object) array(
		'shipping_options' 	=> $cartpage->ec_cart_display_shipping_methods_stripe_dynamic( $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_standard' ),$GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_express' ) ),
		'display_items'		=> $displayItems,
		'total'				=> (int) ( $order_totals->grand_total * 100 ),
		'cart_data'			=> $return_cart_data
	);
	echo json_encode( $result );
	die( );
}

add_action( 'wp_ajax_ec_ajax_update_square_shipping_address_dynamic', 'ec_ajax_update_square_shipping_address_dynamic' );
add_action( 'wp_ajax_nopriv_ec_ajax_update_square_shipping_address_dynamic', 'ec_ajax_update_square_shipping_address_dynamic' );
function ec_ajax_update_square_shipping_address_dynamic( ){
	wpeasycart_session( )->handle_session( );
	
	// Update Shipping
	$GLOBALS['ec_cart_data']->cart_data->shipping_selector = 'true';
    if( isset( $_POST['shippingAddress']['givenName'] ) )
	   $GLOBALS['ec_cart_data']->cart_data->shipping_first_name = strip_tags( $_POST['shippingAddress']['givenName'] );
	if( isset( $_POST['shippingAddress']['familyName'] ) )
	   $GLOBALS['ec_cart_data']->cart_data->shipping_last_name = strip_tags( $_POST['shippingAddress']['familyName'] );
	
    $GLOBALS['ec_cart_data']->cart_data->shipping_company_name = '';
	
	if( isset( $_POST['shippingAddress']['addressLines'] ) && count( $_POST['shippingAddress']['addressLines'] ) > 0 )
		$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = strip_tags( $_POST['shippingAddress']['addressLines'][0] );
	
	if( isset( $_POST['shippingAddress']['addressLines'] ) && count( $_POST['shippingAddress']['addressLines'] ) > 1 )
		$GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = strip_tags( $_POST['shippingAddress']['addressLines'][1] );
	
	if( isset( $_POST['shippingAddress']['city'] ) )
	   $GLOBALS['ec_cart_data']->cart_data->shipping_city = strip_tags( $_POST['shippingAddress']['city'] );
	if( isset( $_POST['shippingAddress']['region'] ) )
	   $GLOBALS['ec_cart_data']->cart_data->shipping_state = strip_tags( $_POST['shippingAddress']['region'] );
	if( isset( $_POST['shippingAddress']['postalCode'] ) )
	   $GLOBALS['ec_cart_data']->cart_data->shipping_zip = strip_tags( $_POST['shippingAddress']['postalCode'] );
	if( isset( $_POST['shippingAddress']['country'] ) )
	   $GLOBALS['ec_cart_data']->cart_data->shipping_country = strip_tags( $_POST['shippingAddress']['country'] );
	if( isset( $_POST['shippingAddress']['phone'] ) )
	   $GLOBALS['ec_cart_data']->cart_data->shipping_phone = strip_tags( $_POST['shippingAddress']['phone'] );
	$GLOBALS['ec_cart_data']->save_session_to_db( );
	wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	// Get cart and totals
	$cartpage = new ec_cartpage( );
	$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
	$order_totals = ec_get_order_totals( $cart );
	$displayItems = wpeasycart_get_cart_display_items( $cart, $order_totals, $order_totals->tax );
	$return_cart_data = ec_get_cart_data( );
	
	// Output new info
	$result = (object) array(
		'shipping_options' 	=> $cartpage->ec_cart_display_shipping_methods_square_dynamic( $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_standard' ),$GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_express' ) ),
		'display_items'		=> $cartpage->get_dynamic_square_line_items( ),
		'total'				=> number_format( $order_totals->grand_total, 2, '.', '' ),
		'cart_data'			=> $return_cart_data
	);
	echo json_encode( $result );
	die( );
}

add_action( 'wp_ajax_ec_ajax_update_square_shipping_option_dynamic', 'ec_ajax_update_square_shipping_option_dynamic' );
add_action( 'wp_ajax_nopriv_ec_ajax_update_square_shipping_option_dynamic', 'ec_ajax_update_square_shipping_option_dynamic' );
function ec_ajax_update_square_shipping_option_dynamic( ){
	wpeasycart_session( )->handle_session( );
	
	// Save Selected Method
	$GLOBALS['ec_cart_data']->cart_data->shipping_method = $_POST['shippingAddress'];
	if( $GLOBALS['ec_cart_data']->cart_data->shipping_method == 'shipexpress' ){
		$GLOBALS['ec_cart_data']->cart_data->expedited_shipping = 'shipexpress';
	}else{
		$GLOBALS['ec_cart_data']->cart_data->expedited_shipping = '';
	}
	$GLOBALS['ec_cart_data']->save_session_to_db( );
	wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	// Get cart and totals
	$cartpage = new ec_cartpage( );
	$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
	$order_totals = ec_get_order_totals( $cart );
	$return_cart_data = ec_get_cart_data( );
	
	// Output new info
	$result = (object) array(
		'shipping_options' 	=> $cartpage->ec_cart_display_shipping_methods_square_dynamic( $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_standard' ),$GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_express' ) ),
		'display_items'		=> $cartpage->get_dynamic_square_line_items( ),
		'total'				=> number_format( $order_totals->grand_total, 2, '.', '' ),
		'cart_data'			=> $return_cart_data
	);
	echo json_encode( $result );
	die( );
}

add_action( 'wp_ajax_ec_ajax_square_complete_payment', 'ec_ajax_square_complete_payment' );
add_action( 'wp_ajax_nopriv_ec_ajax_square_complete_payment', 'ec_ajax_square_complete_payment' );
function ec_ajax_square_complete_payment( ){
	wpeasycart_session( )->handle_session( );
	if( isset( $_POST['shipping_address'] ) ){
        if( $_POST['shipping_address']['familyName'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_first_name = $_POST['shipping_address']['givenName'];
            $GLOBALS['ec_cart_data']->cart_data->shipping_last_name = $_POST['shipping_address']['familyName'];
        }else{
            $GLOBALS['ec_cart_data']->cart_data->shipping_first_name = '';
            $GLOBALS['ec_cart_data']->cart_data->shipping_last_name = '';
            $names = explode( ' ', $_POST['shipping_address']['givenName'] );
            for( $i=0; $i<count( $names )-1; $i++ ){
                if( $i > 0 ){
                    $GLOBALS['ec_cart_data']->cart_data->shipping_first_name .= ' ';
                }
                $GLOBALS['ec_cart_data']->cart_data->shipping_first_name .= $names[$i];
            }
            if( count( $names ) > 1 ){
                $GLOBALS['ec_cart_data']->cart_data->shipping_last_name = $names[count( $names ) - 1];
            }
        }
		$GLOBALS['ec_cart_data']->cart_data->shipping_company_name = '';
		if( isset( $_POST['shipping_address']['addressLine'] ) ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = ( count( $_POST['shipping_address']['addressLine'] ) > 0 ) ? $_POST['shipping_address']['addressLine'][0] : '';
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = ( count( $_POST['shipping_address']['addressLine'] ) > 1 ) ? $_POST['shipping_address']['addressLine'][1] : '';
        }else if( isset( $_POST['shipping_address']['addressLines'] ) ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = ( count( $_POST['shipping_address']['addressLines'] ) > 0 ) ? $_POST['shipping_address']['addressLines'][0] : '';
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = ( count( $_POST['shipping_address']['addressLines'] ) > 1 ) ? $_POST['shipping_address']['addressLines'][1] : '';
        }else if( isset( $_POST['shipping_address']['line1'] ) ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = $_POST['shipping_address']['line1'];
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = $_POST['shipping_address']['line2'];
        }else{
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = '';
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = '';
        }
		$GLOBALS['ec_cart_data']->cart_data->shipping_city = $_POST['shipping_address']['city'];
		if( isset( $_POST['shipping_address']['region'] ) && $_POST['shipping_address']['region'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_state = $_POST['shipping_address']['region'];
        
        }else if( isset( $_POST['shipping_address']['dependentLocality'] ) && $_POST['shipping_address']['dependentLocality'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_state = $_POST['shipping_address']['dependentLocality'];
        
        }else if( isset( $_POST['shipping_address']['state'] ) && $_POST['shipping_address']['stat'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_state = $_POST['shipping_address']['state'];
        
        }else{
            $GLOBALS['ec_cart_data']->cart_data->shipping_state = '';
        }
        if( isset( $_POST['shipping_address']['postalCode'] ) && $_POST['shipping_address']['postalCode'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_zip = $_POST['shipping_address']['postalCode'];
        
        }else if( isset( $_POST['shipping_address']['postal_code'] ) && $_POST['shipping_address']['postal_code'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_zip = $_POST['shipping_address']['postal_code'];
        
        }else{
            $GLOBALS['ec_cart_data']->cart_data->shipping_zip = '';
            
        }
		$GLOBALS['ec_cart_data']->cart_data->shipping_country = $_POST['shipping_address']['country'];
		$GLOBALS['ec_cart_data']->cart_data->shipping_phone = $_POST['shipping_address']['phone'];
        
        if( !$GLOBALS['ec_user']->user_id && isset( $_POST['shipping_address']['email'] ) ){
            $GLOBALS['ec_user']->email = $_POST['shipping_address']['email'];
            $GLOBALS['ec_cart_data']->cart_data->email = $_POST['shipping_address']['email'];
        
        }else if( !$GLOBALS['ec_user']->user_id && isset( $_POST['billing_address']['email'] ) ){
            $GLOBALS['ec_user']->email = $_POST['billing_address']['email'];
            $GLOBALS['ec_cart_data']->cart_data->email = $_POST['billing_address']['email'];
        }
	}
	
	if( isset( $_POST['shipping_method'] ) ){
		$GLOBALS['ec_cart_data']->cart_data->shipping_method = $_POST['shipping_method'];
	}
	
	if( isset( $_POST['billing_address'] ) ){
		if( $_POST['billing_address']['familyName'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->billing_first_name = $_POST['billing_address']['givenName'];
            $GLOBALS['ec_cart_data']->cart_data->billing_last_name = $_POST['billing_address']['familyName'];
        }else{
            $GLOBALS['ec_cart_data']->cart_data->billing_first_name = '';
            $GLOBALS['ec_cart_data']->cart_data->billing_last_name = '';
            $names = explode( ' ', $_POST['billing_address']['givenName'] );
            for( $i=0; $i<count( $names )-1; $i++ ){
                if( $i > 0 ){
                    $GLOBALS['ec_cart_data']->cart_data->billing_first_name .= ' ';
                }
                $GLOBALS['ec_cart_data']->cart_data->billing_first_name .= $names[$i];
            }
            if( count( $names ) > 1 ){
                $GLOBALS['ec_cart_data']->cart_data->billing_last_name = $names[count( $names ) - 1];
            }
        }
        $GLOBALS['ec_cart_data']->cart_data->billing_company_name = '';
        if( isset( $_POST['billing_address']['addressLine'] ) ){
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = ( count( $_POST['billing_address']['addressLine'] ) > 0 ) ? $_POST['billing_address']['addressLine'][0] : '';
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = ( count( $_POST['billing_address']['addressLine'] ) > 1 ) ? $_POST['billing_address']['addressLine'][1] : '';
        }else if( isset( $_POST['billing_address']['addressLines'] ) ){
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = ( count( $_POST['billing_address']['addressLines'] ) > 0 ) ? $_POST['billing_address']['addressLines'][0] : '';
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = ( count( $_POST['billing_address']['addressLines'] ) > 1 ) ? $_POST['billing_address']['addressLines'][1] : '';
        }else if( isset( $_POST['billing_address']['line1'] ) ){
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = $_POST['billing_address']['line1'];
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = $_POST['billing_address']['line2'];
        }else{
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1;
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2;
        }
        $GLOBALS['ec_cart_data']->cart_data->billing_city = ( isset( $_POST['billing_address']['city'] ) ) ? $_POST['billing_address']['city'] : $_POST['shipping_address']['city'];
		if( isset( $_POST['billing_address']['region'] ) && $_POST['billing_address']['region'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->billing_state = $_POST['billing_address']['region'];
        
        }else if( isset( $_POST['billing_address']['dependentLocality'] ) && $_POST['billing_address']['dependentLocality'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->billing_state = $_POST['billing_address']['dependentLocality'];
        
        }else if( isset( $_POST['billing_address']['state'] ) && $_POST['billing_address']['stat'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->billing_state = $_POST['billing_address']['state'];
        
        }else{
            $GLOBALS['ec_cart_data']->cart_data->billing_state = $GLOBALS['ec_cart_data']->cart_data->shipping_state;
        }
        if( isset( $_POST['billing_address']['postalCode'] ) && $_POST['billing_address']['postalCode'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->billing_zip = $_POST['billing_address']['postalCode'];
        
        }else if( isset( $_POST['billing_address']['postal_code'] ) && $_POST['billing_address']['postal_code'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->billing_zip = $_POST['billing_address']['postal_code'];
        
        }else{
            $GLOBALS['ec_cart_data']->cart_data->billing_zip = $GLOBALS['ec_cart_data']->cart_data->shipping_zip;
            
        }
		$GLOBALS['ec_cart_data']->cart_data->billing_country = $_POST['billing_address']['country'];
		$GLOBALS['ec_cart_data']->cart_data->billing_phone = ( isset( $_POST['billing_address']['phone'] ) ) ? $_POST['billing_address']['phone'] : '';
        
    }
		
    $GLOBALS['ec_cart_data']->cart_data->first_name = $GLOBALS['ec_cart_data']->cart_data->billing_first_name;
    $GLOBALS['ec_cart_data']->cart_data->last_name = $GLOBALS['ec_cart_data']->cart_data->billing_last_name;
	
	if( !$GLOBALS['ec_cart_data']->cart_data->user_id ){
		$GLOBALS['ec_cart_data']->cart_data->is_guest = true;
		$GLOBALS['ec_cart_data']->cart_data->guest_key = $GLOBALS['ec_cart_data']->ec_cart_id;
	}else{
		$GLOBALS['ec_cart_data']->cart_data->is_guest = false;
		$GLOBALS['ec_cart_data']->cart_data->guest_key = "";	
	}
	$GLOBALS['ec_cart_data']->save_session_to_db( );
	
	wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	$cartpage = new ec_cartpage( );
	$goto_url = $cartpage->submit_square_quick_payment( $_POST['nonce'], $_POST['card_type'], $_POST['last_4'], $_POST['exp_month'], $_POST['exp_year'] );
	
	echo $goto_url;
	die( );
}

add_action( 'wp_ajax_ec_ajax_get_stripe_complete_payment', 'ec_ajax_get_stripe_complete_payment' );
add_action( 'wp_ajax_nopriv_ec_ajax_get_stripe_complete_payment', 'ec_ajax_get_stripe_complete_payment' );
function ec_ajax_get_stripe_complete_payment( ){
    
    $ec_db = new ec_db( );
    
	wpeasycart_session( )->handle_session( );
	if( isset( $_POST['shipping_address'] ) ){
		$shipping_name = $_POST['shipping_address']['recipient'];
		$shipping_names = explode( " ", $shipping_name );
		$GLOBALS['ec_cart_data']->cart_data->shipping_first_name = "";
		for( $i=0; $i<count( $shipping_names ) - 1; $i++ ){
			if( $i > 0 )
				$GLOBALS['ec_cart_data']->cart_data->shipping_first_name .= ' ';
			$GLOBALS['ec_cart_data']->cart_data->shipping_first_name .= $shipping_names[$i];
		}
		$GLOBALS['ec_cart_data']->cart_data->shipping_last_name = ( count( $shipping_names ) > 1 ) ? $shipping_names[count( $shipping_names ) - 1] : '';
		$GLOBALS['ec_cart_data']->cart_data->shipping_company_name = $_POST['shipping_address']['organization'];
		if( isset( $_POST['shipping_address']['addressLine'] ) ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = ( count( $_POST['shipping_address']['addressLine'] ) > 0 ) ? $_POST['shipping_address']['addressLine'][0] : '';
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = ( count( $_POST['shipping_address']['addressLine'] ) > 1 ) ? $_POST['shipping_address']['addressLine'][1] : '';
        }else if( isset( $_POST['shipping_address']['addressLines'] ) ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = ( count( $_POST['shipping_address']['addressLines'] ) > 0 ) ? $_POST['shipping_address']['addressLines'][0] : '';
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = ( count( $_POST['shipping_address']['addressLines'] ) > 1 ) ? $_POST['shipping_address']['addressLines'][1] : '';
        }else if( isset( $_POST['shipping_address']['line1'] ) ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = $_POST['shipping_address']['line1'];
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = $_POST['shipping_address']['line2'];
        }else{
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1 = '';
            $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2 = '';
        }
        $GLOBALS['ec_cart_data']->cart_data->shipping_city = $_POST['shipping_address']['city'];
		if( isset( $_POST['shipping_address']['region'] ) && $_POST['shipping_address']['region'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_state = $_POST['shipping_address']['region'];
        
        }else if( isset( $_POST['shipping_address']['dependentLocality'] ) && $_POST['shipping_address']['dependentLocality'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_state = $_POST['shipping_address']['dependentLocality'];
        
        }else if( isset( $_POST['shipping_address']['state'] ) && $_POST['shipping_address']['stat'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_state = $_POST['shipping_address']['state'];
        
        }else{
            $GLOBALS['ec_cart_data']->cart_data->shipping_state = '';
        }
        if( isset( $_POST['shipping_address']['postalCode'] ) && $_POST['shipping_address']['postalCode'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_zip = $_POST['shipping_address']['postalCode'];
        
        }else if( isset( $_POST['shipping_address']['postal_code'] ) && $_POST['shipping_address']['postal_code'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->shipping_zip = $_POST['shipping_address']['postal_code'];
        
        }else{
            $GLOBALS['ec_cart_data']->cart_data->shipping_zip = '';
            
        }
        $GLOBALS['ec_cart_data']->cart_data->shipping_country = $_POST['shipping_address']['country'];
		$GLOBALS['ec_cart_data']->cart_data->shipping_phone = $_POST['phone'];
	}
	
	if( isset( $_POST['shipping_method'] ) ){
		$GLOBALS['ec_cart_data']->cart_data->shipping_method = $_POST['shipping_method'];
	}
	
	if( isset( $_POST['billing_name'] ) && $_POST['billing_name'] != '' ){
		$billing_name = $_POST['billing_name'];
		$billing_names = explode( " ", $billing_name );
		$GLOBALS['ec_cart_data']->cart_data->billing_first_name = "";
		for( $i=0; $i<count( $billing_names ) - 1; $i++ ){
			if( $i > 0 )
				$GLOBALS['ec_cart_data']->cart_data->billing_first_name .= ' ';
			$GLOBALS['ec_cart_data']->cart_data->billing_first_name .= $billing_names[$i];
		}
		$GLOBALS['ec_cart_data']->cart_data->billing_last_name = ( count( $billing_names ) > 1 ) ? $billing_names[count( $billing_names ) - 1] : '';
		if( isset( $_POST['billing_address']['addressLine'] ) ){
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = ( count( $_POST['billing_address']['addressLine'] ) > 0 ) ? $_POST['billing_address']['addressLine'][0] : '';
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = ( count( $_POST['billing_address']['addressLine'] ) > 1 ) ? $_POST['billing_address']['addressLine'][1] : '';
        }else if( isset( $_POST['billing_address']['addressLines'] ) ){
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = ( count( $_POST['billing_address']['addressLines'] ) > 0 ) ? $_POST['billing_address']['addressLines'][0] : '';
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = ( count( $_POST['billing_address']['addressLines'] ) > 1 ) ? $_POST['billing_address']['addressLines'][1] : '';
        }else if( isset( $_POST['billing_address']['line1'] ) ){
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = $_POST['billing_address']['line1'];
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = $_POST['billing_address']['line2'];
        }else{
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1;
            $GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2;
        }
        $GLOBALS['ec_cart_data']->cart_data->billing_city = ( isset( $_POST['billing_address']['city'] ) ) ? $_POST['billing_address']['city'] : $_POST['shipping_address']['city'];
		if( isset( $_POST['billing_address']['region'] ) && $_POST['billing_address']['region'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->billing_state = $_POST['billing_address']['region'];
        
        }else if( isset( $_POST['billing_address']['dependentLocality'] ) && $_POST['billing_address']['dependentLocality'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->billing_state = $_POST['billing_address']['dependentLocality'];
        
        }else if( isset( $_POST['billing_address']['state'] ) && $_POST['billing_address']['stat'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->billing_state = $_POST['billing_address']['state'];
        
        }else{
            $GLOBALS['ec_cart_data']->cart_data->billing_state = $GLOBALS['ec_cart_data']->cart_data->shipping_state;
        }
        if( isset( $_POST['billing_address']['postalCode'] ) && $_POST['billing_address']['postalCode'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->billing_zip = $_POST['billing_address']['postalCode'];
        
        }else if( isset( $_POST['billing_address']['postal_code'] ) && $_POST['billing_address']['postal_code'] != '' ){
            $GLOBALS['ec_cart_data']->cart_data->billing_zip = $_POST['billing_address']['postal_code'];
        
        }else{
            $GLOBALS['ec_cart_data']->cart_data->billing_zip = $GLOBALS['ec_cart_data']->cart_data->shipping_zip;
            
        }
		$GLOBALS['ec_cart_data']->cart_data->billing_country = $_POST['billing_address']['country'];
		$GLOBALS['ec_cart_data']->cart_data->billing_phone = $_POST['billing_phone'];
	
    }else{
        $GLOBALS['ec_cart_data']->cart_data->billing_first_name = $GLOBALS['ec_cart_data']->cart_data->shipping_first_name;
		$GLOBALS['ec_cart_data']->cart_data->billing_last_name = $GLOBALS['ec_cart_data']->cart_data->shipping_last_name;
		$GLOBALS['ec_cart_data']->cart_data->billing_address_line_1 = $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_1;
		$GLOBALS['ec_cart_data']->cart_data->billing_address_line_2 = $GLOBALS['ec_cart_data']->cart_data->shipping_address_line_2;
		$GLOBALS['ec_cart_data']->cart_data->billing_city = $GLOBALS['ec_cart_data']->cart_data->shipping_city;
		$GLOBALS['ec_cart_data']->cart_data->billing_state = $GLOBALS['ec_cart_data']->cart_data->shipping_state;
		$GLOBALS['ec_cart_data']->cart_data->billing_zip = $GLOBALS['ec_cart_data']->cart_data->shipping_zip;
		$GLOBALS['ec_cart_data']->cart_data->billing_country = $GLOBALS['ec_cart_data']->cart_data->shipping_country;
		$GLOBALS['ec_cart_data']->cart_data->billing_phone = $GLOBALS['ec_cart_data']->cart_data->shipping_phone;
    
    }
		
    $GLOBALS['ec_cart_data']->cart_data->first_name = $GLOBALS['ec_cart_data']->cart_data->billing_first_name;
    $GLOBALS['ec_cart_data']->cart_data->last_name = $GLOBALS['ec_cart_data']->cart_data->billing_last_name;
	
	if( !$GLOBALS['ec_user']->user_id && isset( $_POST['email'] ) ){
        $GLOBALS['ec_user']->email = $_POST['email'];
		$GLOBALS['ec_cart_data']->cart_data->email = $_POST['email'];
	}
	
	if( !$GLOBALS['ec_cart_data']->cart_data->user_id ){
		$GLOBALS['ec_cart_data']->cart_data->is_guest = true;
		$GLOBALS['ec_cart_data']->cart_data->guest_key = $GLOBALS['ec_cart_data']->ec_cart_id;
	}else{
		$GLOBALS['ec_cart_data']->cart_data->is_guest = false;
		$GLOBALS['ec_cart_data']->cart_data->guest_key = "";	
	}
	$payment_intent_id = $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id;
	$GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id = "";
	$GLOBALS['ec_cart_data']->save_session_to_db( );
    
    wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	$cartpage = new ec_cartpage( );
	$goto_url = $cartpage->submit_stripe_quick_payment( $payment_intent_id, $_POST['card_type'], $_POST['last_4'], $_POST['exp_month'], $_POST['exp_year'] );
	
	echo $goto_url;
	die( );
}

add_action( 'wp_ajax_ec_ajax_get_stripe_complete_payment_main', 'ec_ajax_get_stripe_complete_payment_main' );
add_action( 'wp_ajax_nopriv_ec_ajax_get_stripe_complete_payment_main', 'ec_ajax_get_stripe_complete_payment_main' );
function ec_ajax_get_stripe_complete_payment_main( ){
	// Get Payment Intent Info
	if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
		$stripe = new ec_stripe( );
	else
		$stripe = new ec_stripe_connect( );
	$payment_intent = $stripe->get_payment_intent( $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id );
	
	// Get data we want to keep
	$card_type = $payment_intent->charges->data[0]->payment_method_details->card->brand;
	$last_4 = $payment_intent->charges->data[0]->payment_method_details->card->last4;
	$exp_month = $payment_intent->charges->data[0]->payment_method_details->card->exp_month;
	$exp_year = $payment_intent->charges->data[0]->payment_method_details->card->exp_year;
	
	// Create the Stripe Order Dynamically
	$cartpage = new ec_cartpage( );
	$goto_url = $cartpage->submit_stripe_quick_payment( $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id, $card_type, $last_4, $exp_month, $exp_year );
	
	echo $goto_url;
	die( );
}

add_action( 'wp_ajax_ec_ajax_get_stripe_complete_payment_invoice', 'ec_ajax_get_stripe_complete_payment_invoice' );
add_action( 'wp_ajax_nopriv_ec_ajax_get_stripe_complete_payment_invoice', 'ec_ajax_get_stripe_complete_payment_invoice' );
function ec_ajax_get_stripe_complete_payment_invoice( ){
	// Get Payment Intent Info
	if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
		$stripe = new ec_stripe( );
	else
		$stripe = new ec_stripe_connect( );
	$payment_intent = $stripe->get_payment_intent( $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id );
	
	// Get data we want to keep
	$card_type = $payment_intent->charges->data[0]->payment_method_details->card->brand;
	$last_4 = $payment_intent->charges->data[0]->payment_method_details->card->last4;
	$exp_month = $payment_intent->charges->data[0]->payment_method_details->card->exp_month;
	$exp_year = $payment_intent->charges->data[0]->payment_method_details->card->exp_year;
	
	// Create the Stripe Order Dynamically
	$cartpage = new ec_cartpage( );
	$goto_url = $cartpage->submit_stripe_invoice_payment( $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id, $card_type, $last_4, $exp_month, $exp_year );
	
	echo $goto_url;
	die( );
}

add_action( 'wp_ajax_ec_ajax_get_stripe_complete_payment_subscription', 'ec_ajax_get_stripe_complete_payment_subscription' );
add_action( 'wp_ajax_nopriv_ec_ajax_get_stripe_complete_payment_subscription', 'ec_ajax_get_stripe_complete_payment_subscription' );
function ec_ajax_get_stripe_complete_payment_subscription( ){
	// Get Payment Intent Info
	if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
		$stripe = new ec_stripe( );
	else
		$stripe = new ec_stripe_connect( );
	
	// Create the Order Dynamically
	$cartpage = new ec_cartpage( );
	$goto_url = $cartpage->submit_stripe_quick_subscription_payment( $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id );
	
	echo $goto_url;
	die( );
}

add_action( 'wp_ajax_ec_ajax_get_stripe_create_subscription', 'ec_ajax_get_stripe_create_subscription' );
add_action( 'wp_ajax_nopriv_ec_ajax_get_stripe_create_subscription', 'ec_ajax_get_stripe_create_subscription' );
function ec_ajax_get_stripe_create_subscription( ){
	$cartpage = new ec_cartpage( );
	$response = $cartpage->submit_stripe_quick_subscription( $GLOBALS['ec_cart_data']->cart_data->stripe_paymentintent_id );//, $card_type, $last_4, $exp_month, $exp_year );
	if( !$response ){
		echo json_encode( array( 'status' => 'error' ) );
	}else{
		echo json_encode( $response );
	}
	die( );
}

add_action( 'wp_ajax_ec_ajax_get_stripe_update_customer_card', 'ec_ajax_get_stripe_update_customer_card' );
add_action( 'wp_ajax_nopriv_ec_ajax_get_stripe_update_customer_card', 'ec_ajax_get_stripe_update_customer_card' );
function ec_ajax_get_stripe_update_customer_card( ){
	global $wpdb;
	$ec_db = new ec_db( );
    $payment_method = get_option( 'ec_option_payment_process_method' );
	
	// Get Payment Intent Info
	if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
		$stripe = new ec_stripe( );
	else
		$stripe = new ec_stripe_connect( );
	
	$subscription = $ec_db->get_subscription_row( $_POST['subscription_id'] );
	$subscription_info = $stripe->get_subscription( $GLOBALS['ec_user']->stripe_customer_id, $subscription->stripe_subscription_id );
	if( $subscription_info ){
		$card_info = $stripe->attach_payment_method( $_POST['payment_id'], $GLOBALS['ec_user'] );
		$update_response = $stripe->set_subscription_payment_method( $_POST['payment_id'], $subscription_info, $subscription, $_POST['quantity'] );
		if( $update_response ){
			$wpdb->query( $wpdb->prepare( "UPDATE ec_subscription SET quantity = %d WHERE subscription_id = %d", $_POST['quantity'], $subscription->subscription_id ) );
			$card = new ec_credit_card( $card_info->card->brand, $card_info->card->name, $card_info->card->last4, $card_info->card->exp_month, $card_info->card->exp_year, '' );
			$ec_db->update_user_default_card( $GLOBALS['ec_user'], $card );
		}
	}
    
    // Update Plan if Changed
    $products = $ec_db->get_product_list( $wpdb->prepare( " WHERE product.product_id = %d", $_POST['ec_selected_plan'] ), "", "", "" );
    if( count( $products ) > 0 ){
        $product = new ec_product( $products[0] );
        $plan_added = $product->stripe_plan_added;
        if( $payment_method == "stripe" || $payment_method == "stripe_connect" ){
            if( $payment_method == "stripe" )
                $stripe = new ec_stripe( );
            else
                $stripe = new ec_stripe_connect( );

            if( !$product->stripe_plan_added ){
                $plan_added = $stripe->insert_plan( $product );
                $ec_db->update_product_stripe_added( $product->product_id );
            }
            
            if( $plan_added ){
                $success = $stripe->update_subscription( $product, $GLOBALS['ec_user'], NULL, $_POST['stripe_subscription_id'], NULL, $product->subscription_prorate, NULL, $_POST['quantity'] );
                if( $success ){
					$ec_db->update_subscription( $_POST['subscription_id'], $GLOBALS['ec_user'], $product, $card, $_POST['quantity'] );
					$ec_db->update_user_default_card( $GLOBALS['ec_user'], $card );
				}
            }
        }
    }
    
	// Return the Redirect URL
	$account_page_id = get_option('ec_option_accountpage');
	if( function_exists( 'icl_object_id' ) ){
		$account_page_id = icl_object_id( $account_page_id, 'page', true, ICL_LANGUAGE_CODE );
	}
	$account_page = get_permalink( $account_page_id );
	if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
		$https_class = new WordPressHTTPS( );
		$account_page = $https_class->makeUrlHttps( $account_page );
	}
	if( substr_count( $account_page, '?' ) )					$permalink_divider = "&";
	else														$permalink_divider = "?";
	
	echo json_encode( 
		array( 
			'url' => $account_page . $permalink_divider . "ec_page=subscription_details&subscription_id=" . (int) $_POST['subscription_id']
		)
	);
	die( );
}

function wpeasycart_get_cart_display_items( $cart, $order_totals, $tax ){
	$displayItems = array(
		(object) array(
			"pending" 	=> (bool) 1,
			"label"		=> 'Subtotal',
			"amount"	=> (int) round( ( $order_totals->get_converted_sub_total( ) * 100 ), 2 )
		)
	);
	if( $order_totals->tax_total > 0 ){
		$displayItems[] = (object) array(
			"pending" 	=> (bool) 1,
			"label" 	=>  $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_tax' ),
			"amount" 	=> (int) round( ( $order_totals->tax_total * 100 ), 2 )
		);
	}
	if( $order_totals->shipping_total > 0 ){
		$displayItems[] = (object) array(
			"pending" 	=> (bool) 1,
			"label" 	=> $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_shipping' ),
			"amount" 	=> (int) round( ( $order_totals->shipping_total * 100 ), 2 )
		);
	}
	if( $order_totals->discount_total != 0 ){
		$displayItems[] = (object) array(
			"pending" 	=> (bool) 1,
			"label" 	=> $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_discounts' ),
			"amount" 	=> (int) round( ( $order_totals->discount_total * 100 ), 2 )
		);
	}
	if( $tax->is_duty_enabled( ) ){
		$displayItems[] = (object) array(
			"pending" 	=> (bool) 1,
			"label" 	=> $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_duty' ),
			"amount" 	=> (int) round( ( $order_totals->duty_total * 100 ), 2 )
		);
	}
	if( $tax->is_vat_enabled( ) && $tax->vat_total > 0 ){ 
		$displayItems[] = (object) array(
			"pending" 	=> (bool) 1,
			"label" 	=> $GLOBALS['language']->get_text( 'cart_totals', 'cart_totals_vat' ),
			"amount" 	=> (int) round( ( $tax->vat_total * 100 ), 2 )
		);
	}
	if( get_option( 'ec_option_enable_easy_canada_tax' ) && $order_totals->gst_total > 0 ){
		$displayItems[] = (object) array(
			"pending" 	=> (bool) 1,
			"label" 	=> 'GST (' . $tax->gst_rate . '%)',
			"amount" 	=> (int) round( ( $order_totals->gst_total * 100 ), 2 )
		);
	}
	if( get_option( 'ec_option_enable_easy_canada_tax' ) && $order_totals->pst_total > 0 ){
		$displayItems[] = (object) array(
			"pending" 	=> (bool) 1,
			"label" 	=> 'PST (' . $tax->pst_rate . '%)',
			"amount" 	=> (int) round( ( $order_totals->pst_total * 100 ), 2 )
		);
	}
	if( get_option( 'ec_option_enable_easy_canada_tax' ) && $order_totals->hst_total > 0 ){
		$displayItems[] = (object) array(
			"pending" 	=> (bool) 1,
			"label" 	=> 'HST (' . $tax->hst_rate . '%)',
			"amount" 	=> (int) round( ( $order_totals->hst_total * 100 ), 2 )
		);
	}
	return $displayItems;
}

add_action( 'wp_ajax_ec_ajax_redeem_coupon_code', 'ec_ajax_redeem_coupon_code' );
add_action( 'wp_ajax_nopriv_ec_ajax_redeem_coupon_code', 'ec_ajax_redeem_coupon_code' );
function ec_ajax_redeem_coupon_code( ){
	wpeasycart_session( )->handle_session( );
	
	//UPDATE COUPON CODE
	$coupon_code = "";
	if( isset( $_POST['couponcode'] ) ){
		$coupon_code = trim( $_POST['couponcode'] );
		$GLOBALS['ec_cart_data']->cart_data->coupon_code = $coupon_code;
	}
	
	$GLOBALS['ec_cart_data']->save_session_to_db( );
	wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	$coupon = $GLOBALS['ec_coupons']->redeem_coupon_code( $coupon_code );
	if( isset( $_POST['ec_v3_24'] ) ){
		$return_array = ec_get_cart_data( );
		if( $coupon ){
			if( $coupon && !$coupon->coupon_expired && ( $coupon->max_redemptions == 999 || $coupon->times_redeemed < $coupon->max_redemptions ) ){
				$return_array['coupon_message'] = $coupon->message;
				$return_array['is_coupon_valid'] = true;
				$GLOBALS['ec_cart_data']->cart_data->coupon_code = $coupon_code;
				$cartpage = new ec_cartpage( );
				if( $cartpage->discount->coupon_matches <= 0 ){
					$return_array['coupon_message'] = $GLOBALS['language']->get_text( 'cart_coupons', 'coupon_not_applicable' );
				}
			
			}else if( $coupon && $coupon->times_redeemed >= $coupon->max_redemptions ){
				$return_array['coupon_message'] = $GLOBALS['language']->get_text( 'cart_coupons', 'cart_max_exceeded_coupon' );
				$return_array['is_coupon_valid'] = false;
				$GLOBALS['ec_cart_data']->cart_data->coupon_code = "";
			
			}else if( $coupon->coupon_expired ){
				$return_array['coupon_message'] = $GLOBALS['language']->get_text( 'cart_coupons', 'cart_coupon_expired' );
				$return_array['is_coupon_valid'] = false;
				$GLOBALS['ec_cart_data']->cart_data->coupon_code;
				
			}else{
				$return_array['coupon_message'] = $GLOBALS['language']->get_text( 'cart_coupons', 'cart_invalid_coupon' );
				$return_array['is_coupon_valid'] = false;
				$GLOBALS['ec_cart_data']->cart_data->coupon_code;
			}
		}else{
			$return_array['coupon_message'] = $GLOBALS['language']->get_text( 'cart_coupons', 'cart_invalid_coupon' );
			$return_array['is_coupon_valid'] = false;
			$GLOBALS['ec_cart_data']->cart_data->coupon_code;
		}
				
		echo json_encode( $return_array );
	}else{
		// UPDATE COUPON CODE
		$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$order_totals = ec_get_order_totals( $cart );
		
		echo $cart->total_items . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->sub_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->tax_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->shipping_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( (-1) * $order_totals->discount_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->duty_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->vat_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->grand_total );
		
		if( $coupon ){
			if( $coupon && !$coupon->coupon_expired && ( $coupon->max_redemptions == 999 || $coupon->times_redeemed < $coupon->max_redemptions ) ){
				$GLOBALS['ec_cart_data']->cart_data->coupon_code = $coupon_code;
				$cartpage = new ec_cartpage( );
				if( $cartpage->discount->coupon_matches <= 0 ){
					echo "***" . $GLOBALS['language']->get_text( 'cart_coupons', 'coupon_not_applicable' ) . "***" . "valid";
				}else{
					echo "***" . $coupon->message . "***" . "valid";
				}
			
			}else if( $coupon && $coupon->times_redeemed >= $coupon->max_redemptions ){
				echo "***" . $GLOBALS['language']->get_text( 'cart_coupons', 'cart_max_exceeded_coupon' ) . "***" . "invalid";
				$GLOBALS['ec_cart_data']->cart_data->coupon_code = "";
			
			}else if( $coupon->coupon_expired ){
				echo "***" . $GLOBALS['language']->get_text( 'cart_coupons', 'cart_coupon_expired' ) . "***" . "invalid";
				$GLOBALS['ec_cart_data']->cart_data->coupon_code;
				
			}else{
				echo "***" . $GLOBALS['language']->get_text( 'cart_coupons', 'cart_invalid_coupon' ) . "***" . "invalid";
				$GLOBALS['ec_cart_data']->cart_data->coupon_code;
			}
		}else{
			echo "***" . $GLOBALS['language']->get_text( 'cart_coupons', 'cart_invalid_coupon' ) . "***" . "invalid";
			$GLOBALS['ec_cart_data']->cart_data->coupon_code;
		}
			
		if( $order_totals->discount_total == 0 )
			echo "***0";
		else
			echo "***1";
	}
	die(); // this is required to return a proper result
	
}

add_action( 'wp_ajax_ec_ajax_redeem_subscription_coupon_code', 'ec_ajax_redeem_subscription_coupon_code' );
add_action( 'wp_ajax_nopriv_ec_ajax_redeem_subscription_coupon_code', 'ec_ajax_redeem_subscription_coupon_code' );
function ec_ajax_redeem_subscription_coupon_code( ){
	global $wpdb;
    
	wpeasycart_session( )->handle_session( );
	
	// Get Coupon Code Info
	$product_id = "";
	$manufacturer_id = "";
	$coupon_code = "";
	if( isset( $_POST['couponcode'] ) ){
		$coupon_code = trim( $_POST['couponcode'] );
	}
	if( isset( $_POST['product_id'] ) ){
		$product_id = $_POST['product_id'];
	}
	if( isset( $_POST['manufacturer_id'] ) ){
		$manufacturer_id = $_POST['manufacturer_id'];
	}
	
	// Get the Coupon and Check Validity
	$GLOBALS['ec_cart_data']->cart_data->coupon_code = $coupon_code;
	$coupon = $GLOBALS['ec_coupons']->redeem_coupon_code( $coupon_code );
	$coupon_code_invalid = true;
	$coupon_applicable = true;
	$coupon_exceeded_redemptions = false;
	$coupon_expired = false;
	
	if( !$coupon ){ // Invalid Coupon
		$coupon_code_invalid = false;
	}else if( $coupon->by_product_id && $coupon->product_id != $product_id ){ // Product does not match
		$coupon_applicable = false;
	}else if( $coupon->by_manufacturer_id && $coupon->manufacturer_id != $manufacturer_id  ){ // Manufacturer Does not Match
		$coupon_applicable = false;
	}else if( $coupon->by_category_id ){ // validate category id match
        $has_categories = $wpdb->get_results( $wpdb->prepare( "SELECT categoryitem_id FROM ec_categoryitem WHERE category_id = %d AND product_id = %d", $coupon->category_id, $product_id ) );
        if( !$has_categories ){
            $coupon_applicable = false;
        }
    }else if( $coupon->max_redemptions != 999 && $coupon->times_redeemed >= $coupon->max_redemptions ){
		$coupon_exceeded_redemptions = true;
	}else if( $coupon->coupon_expired ){
		$coupon_expired = true;
	}
	
	// Get product for discount option
	$ec_db = new ec_db( );
	$products = $ec_db->get_product_list( $wpdb->prepare( " WHERE product.product_id = %d", $product_id ), "", "", "" );
	$product = new ec_product( $products[0] );
	
	$discount_amount = 0;
	$subscription_quantity = 1;
	if( $GLOBALS['ec_cart_data']->cart_data->subscription_quantity )
		$subscription_quantity = $GLOBALS['ec_cart_data']->cart_data->subscription_quantity;
		
	// Create Promotion Multiplier for Options
	$option_promotion_multiplier = 1;
	$option_promotion_discount = 0;
	$promotions = $GLOBALS['ec_promotions']->promotions;
	for( $i=0; $i<count( $promotions ); $i++ ){
		if( $product->promotion_text == $promotions[$i]->promotion_name ){
			if( $promotions[$i]->price1 == 0 ){
				$option_promotion_multiplier = ( 100 - $promotions[$i]->percentage1 ) / 100;
			}else if( $promotions[$i]->price1 != 0 ){
				$option_promotion_discount = $promotions[$i]->price1;
			}
		}
	}
		
	// Get option item price adjustments
	$option_total = 0;
	$optionitem_list = $GLOBALS['ec_options']->get_all_optionitems( );
	$subscription_option1 = $subscription_option2 = $subscription_option3 = $subscription_option4 = $subscription_option5 = 0;
	if( ( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option1 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option1 != "" ) || 
		( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option2 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option2 != "" ) || 
		( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option3 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option3 != "" ) || 
		( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option4 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option4 != "" ) || 
		( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option5 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option5 != "" ) ){
		
		
		if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option1 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option1 != "" ){
			$subscription_option1 = $GLOBALS['ec_cart_data']->cart_data->subscription_option1;
		}
		
		if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option2 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option2 != "" ){
			$subscription_option2 = $GLOBALS['ec_cart_data']->cart_data->subscription_option2;
		}
		
		if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option3 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option3 != "" ){
			$subscription_option3 = $GLOBALS['ec_cart_data']->cart_data->subscription_option3;
		}
		
		if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option4 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option4 != "" ){
			$subscription_option4 = $GLOBALS['ec_cart_data']->cart_data->subscription_option4;
		}
		
		if( isset( $GLOBALS['ec_cart_data']->cart_data->subscription_option5 ) && $GLOBALS['ec_cart_data']->cart_data->subscription_option5 != "" ){
			$subscription_option5 = $GLOBALS['ec_cart_data']->cart_data->subscription_option5;
		}
		
		if( $subscription_option1 != 0 ){
			$subscription_option1 = $GLOBALS['ec_options']->get_optionitem( $subscription_option1 );
			if( $subscription_option1->optionitem_price > 0 ){
				$option_total += $subscription_option1->optionitem_price;
			}
		}
		if( $subscription_option2 != 0 ){
			$subscription_option2 = $GLOBALS['ec_options']->get_optionitem( $subscription_option2 );
			if( $subscription_option2->optionitem_price > 0 ){
				$option_total += $subscription_option2->optionitem_price;
			}
		}
		if( $subscription_option3 != 0 ){
			$subscription_option3 = $GLOBALS['ec_options']->get_optionitem( $subscription_option3 );
			if( $subscription_option3->optionitem_price > 0 ){
				$option_total += $subscription_option3->optionitem_price;
			}
		}
		if( $subscription_option4 != 0 ){
			$subscription_option4 = $GLOBALS['ec_options']->get_optionitem( $subscription_option4 );
			if( $subscription_option4->optionitem_price > 0 ){
				$option_total += $subscription_option4->optionitem_price;
			}
		}
		if( $subscription_option5 != 0 ){
			$subscription_option5 = $GLOBALS['ec_options']->get_optionitem( $subscription_option5 );
			if( $subscription_option5->optionitem_price > 0 ){
				$option_total += $subscription_option5->optionitem_price;
			}
		}
	}
	
	// If valid and applicable, set to cache.
	if( $coupon_applicable && !$coupon_exceeded_redemptions && !$coupon_expired ){
		$GLOBALS['ec_cart_data']->cart_data->coupon_code = $coupon_code;
		if( $coupon->is_percentage_based ){
			$discount_amount = ( $product->price + $option_total ) * $subscription_quantity * ($coupon->promo_percentage/100);
		}else if( $coupon->is_dollar_based ){
			$discount_amount = $coupon->promo_dollar;
		}
		if( $discount_amount > ( ( $product->price + $option_total ) * $subscription_quantity ) )
			$discount_amount = ( ( $product->price + $option_total ) * $subscription_quantity );
	}else{
		$GLOBALS['ec_cart_data']->cart_data->coupon_code = "";
	}
	
	if( $discount_amount <= 0 && $option_promotion_multiplier != 1 ){
		$discount_amount = ( ( ( $product->price + $option_total ) * $subscription_quantity ) ) * ( 1 - $option_promotion_multiplier );
	
	}else if( $discount_amount <= 0 && $option_promotion_discount > 0 ){
		$discount_amount = $option_promotion_discount;
	}
	
	$discount_amount = round( $discount_amount, 2 );
	
	$GLOBALS['ec_cart_data']->save_session_to_db( );
	wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	// Get and Print Order Totals
	$sub_total = ( ( $product->price + $option_total + $product->subscription_signup_fee ) * $subscription_quantity ) - $discount_amount;
	$tax_subtotal = ( $product->is_taxable ) ? $sub_total - ( $product->subscription_signup_fee * $subscription_quantity ) : 0;
	$vat_subtotal = ( $product->vat_rate > 0 ) ? $sub_total - ( $product->subscription_signup_fee * $subscription_quantity ) : 0;
	wpeasycart_taxcloud( )->setup_subscription_for_tax( $product, $subscription_quantity );
	$ec_tax = new ec_tax( $sub_total, $tax_subtotal, $vat_subtotal, $GLOBALS['ec_cart_data']->cart_data->shipping_state, $GLOBALS['ec_cart_data']->cart_data->shipping_country, $GLOBALS['ec_user']->taxfree, 0, (object) array( 'cart' => array( $product ) ) );
	
	$tax_total = $ec_tax->tax_total;
	$vat_rate = $ec_tax->vat_rate;
	$vat_total = $ec_tax->vat_total;
	
	$coupon_message = '';
	$coupon_status = '';
	
	if( !$coupon_code_invalid ){
		$coupon_message = $GLOBALS['language']->get_text( 'cart_coupons', 'cart_invalid_coupon' );
		$coupon_status = "invalid";
	
	}else if( !$coupon_applicable ){
		$coupon_message = $GLOBALS['language']->get_text( 'cart_coupons', 'cart_not_applicable_coupon' );
		$coupon_status = "invalid";
	
	}else if( $coupon_exceeded_redemptions ){
		$coupon_message = $GLOBALS['language']->get_text( 'cart_coupons', 'cart_max_exceeded_coupon' );
		$coupon_status = "invalid";
		
	}else if( $coupon_expired ){
		$coupon_message = $GLOBALS['language']->get_text( 'cart_coupons', 'cart_coupon_expired' );
		$coupon_status = "invalid";
		
	}else{
		$cartpage = new ec_cartpage( );
		if( $cartpage->discount->coupon_matches <= 0 ){
			$coupon_message = $GLOBALS['language']->get_text( 'cart_coupons', 'coupon_not_applicable' );
		}else{
			$coupon_message = $coupon->message;
		}
		$coupon_status = "valid";
				
	}
    
    if( $product->trial_period_days > 0 ){
        $grand_total = ( $product->subscription_signup_fee * $subscription_quantity );
    }else if( $ec_tax->vat_included ){
        $grand_total = ( ( $product->price + $option_total + $product->subscription_signup_fee ) * $subscription_quantity ) - $discount_amount + $tax_total + $ec_tax->hst + $ec_tax->gst + $ec_tax->pst;
    }else{
        $grand_total = ( ( $product->price + $option_total + $product->subscription_signup_fee ) * $subscription_quantity ) - $discount_amount + $tax_total + $vat_total + $ec_tax->hst + $ec_tax->gst + $ec_tax->pst;
    }
	
	echo json_encode( array(
		'quantity'			=> $subscription_quantity, 
		'subtotal'			=> $GLOBALS['currency']->get_currency_display( $product->price * $subscription_quantity ),
		'has_tax'			=> ( $tax_total > 0 ) ? 1 : 0,
		'tax_total'			=> $GLOBALS['currency']->get_currency_display( $tax_total ), 
		'hst_total'			=> $GLOBALS['currency']->get_currency_display( $ec_tax->hst ),
		'hst_rate'			=> $ec_tax->hst_rate,
		'pst_total'			=> $GLOBALS['currency']->get_currency_display( $ec_tax->pst ),
		'pst_rate'			=> $ec_tax->pst_rate,
		'gst_total'			=> $GLOBALS['currency']->get_currency_display( $ec_tax->gst ),
		'gst_rate'			=> $ec_tax->gst_rate,
		'discount_total'	=> $GLOBALS['currency']->get_currency_display( (-1) * $discount_amount ), 
		'has_vat'			=> ( $vat_total > 0 ) ? 1 : 0,
		'vat_total'			=> $GLOBALS['currency']->get_currency_display( $vat_total ),
		'grand_total'		=> $GLOBALS['currency']->get_currency_display( $grand_total ),
		'coupon_message'	=> $coupon_message,
		'coupon_status'		=> $coupon_status,
		'has_discount'		=> ( $discount_amount == 0 ) ? 0 : 1,
		'price_formatted'	=> $product->get_price_formatted( $subscription_quantity )
	) );
	
	die( ); // this is required to return a proper result
	
}

add_action( 'wp_ajax_ec_ajax_redeem_gift_card', 'ec_ajax_redeem_gift_card' );
add_action( 'wp_ajax_nopriv_ec_ajax_redeem_gift_card', 'ec_ajax_redeem_gift_card' );
function ec_ajax_redeem_gift_card( ){
	wpeasycart_session( )->handle_session( );
	
	// UPDATE GIFT CARD
	$gift_card = "";
	if( isset( $_POST['giftcard'] ) )
		$gift_card = trim( $_POST['giftcard'] );
		
	$GLOBALS['ec_cart_data']->cart_data->giftcard = $gift_card;
	
	$GLOBALS['ec_cart_data']->save_session_to_db( );
	wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	$db = new ec_db();
	$giftcard = $db->redeem_gift_card( $gift_card );
	
	if( isset( $_POST['ec_v3_24'] ) ){
		$return_array = ec_get_cart_data( );
		if( $giftcard ){
			$return_array['giftcard_message'] = $giftcard->message;
			$return_array['is_giftcard_valid'] = true;
			
		}else{
			$GLOBALS['ec_cart_data']->cart_data->giftcard = "";
			$return_array['giftcard_message'] = $GLOBALS['language']->get_text( 'cart_coupons', 'cart_invalid_giftcard' );
			$return_array['is_giftcard_valid'] = false;
		}	
		echo json_encode( $return_array );
	}else{
		$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$order_totals = ec_get_order_totals( $cart );
		echo $cart->total_items . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->sub_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->tax_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->shipping_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( (-1) * $order_totals->discount_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->duty_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->vat_total ) . "***" . 
				$GLOBALS['currency']->get_currency_display( $order_totals->grand_total );
		
		if( $giftcard )
			echo "***" . $giftcard->message . "***" . "valid";
		else{
			$GLOBALS['ec_cart_data']->cart_data->giftcard = "";
			echo "***" . $GLOBALS['language']->get_text( 'cart_coupons', 'cart_invalid_giftcard' ) . "***" . "invalid";
		}
			
		if( $order_totals->discount_total == 0 )
			echo "***0";
		else
			echo "***1";
	}
	
	die(); // this is required to return a proper result
	
}

add_action( 'wp_ajax_ec_ajax_estimate_shipping', 'ec_ajax_estimate_shipping' );
add_action( 'wp_ajax_nopriv_ec_ajax_estimate_shipping', 'ec_ajax_estimate_shipping' );
function ec_ajax_estimate_shipping( ){
	wpeasycart_session( )->handle_session( );
	
	//Get the variables from the AJAX call
	if( isset( $_POST['zipcode'] ) ){
		$GLOBALS['ec_cart_data']->cart_data->estimate_shipping_zip = $_POST['zipcode'];
		$GLOBALS['ec_cart_data']->cart_data->shipping_zip = $_POST['zipcode'];
	}
	if( isset( $_POST['country'] ) && $_POST['country'] != "0" ){
		$GLOBALS['ec_cart_data']->cart_data->estimate_shipping_country = $_POST['country'];
		$GLOBALS['ec_cart_data']->cart_data->shipping_country = $_POST['country'];
	}
	
	$GLOBALS['ec_cart_data']->save_session_to_db( );
	wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	if( isset( $_POST['ec_v3_24'] ) ){
		$return_array = ec_get_cart_data( );
		echo json_encode( $return_array );
	
	}else{
		$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$order_totals = ec_get_order_totals( $cart );
		$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$shipping = new ec_shipping( $cart->subtotal, $cart->weight, $cart->shippable_total_items, 'RADIO', $GLOBALS['ec_user']->freeshipping );
		
		$shipping_options = $shipping->get_shipping_options( $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_standard' ),$GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_express' ), "RADIO" );
		
		if( $GLOBALS['ec_setting']->get_shipping_method() == "live" && $shipping_options )
			echo $GLOBALS['currency']->get_currency_display( $order_totals->shipping_total ) . "***" . $GLOBALS['currency']->get_currency_display( $order_totals->grand_total ) . "***" . $shipping_options . "***" . $GLOBALS['currency']->get_currency_display( $order_totals->vat_total );
		else if( $GLOBALS['ec_setting']->get_shipping_method() == "live" )
			echo $GLOBALS['currency']->get_currency_display( $order_totals->shipping_total ) . "***" . $GLOBALS['currency']->get_currency_display( $order_totals->grand_total ) . "***" . "<div class=\"ec_cart_shipping_method_row\">" . $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_error' ) . "</div>";
		else
			echo $GLOBALS['currency']->get_currency_display( $order_totals->shipping_total ) . "***" . $GLOBALS['currency']->get_currency_display( $order_totals->grand_total ) . "***" . $shipping_options;
	}
	die(); // this is required to return a proper result
	
}

add_action( 'wp_ajax_ec_ajax_update_shipping_method', 'ec_ajax_update_shipping_method' );
add_action( 'wp_ajax_nopriv_ec_ajax_update_shipping_method', 'ec_ajax_update_shipping_method' );
function ec_ajax_update_shipping_method( ){
	wpeasycart_session( )->handle_session( );
	
	//Get the variables from the AJAX call
	$shipping_method = $_POST['shipping_method'];
	
	//Create a new db and submit review
	$GLOBALS['ec_cart_data']->cart_data->shipping_method = $shipping_method;
	
	$GLOBALS['ec_cart_data']->save_session_to_db( );
	wp_cache_flush( );
	do_action( 'wpeasycart_cart_updated' );
	
	$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
	$order_totals = ec_get_order_totals( $cart );
	$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
	$shipping = new ec_shipping( $cart->subtotal, $cart->weight, $cart->shippable_total_items, 'RADIO', $GLOBALS['ec_user']->freeshipping );
	
	$shipping_options = $shipping->get_shipping_options( $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_standard' ),$GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_express' ), "RADIO" );
	
	if( $GLOBALS['ec_setting']->get_shipping_method() == "live" && $shipping_options )
		echo $GLOBALS['currency']->get_currency_display( $order_totals->shipping_total ) . "***" . $GLOBALS['currency']->get_currency_display( $order_totals->grand_total ) . "***" . $shipping_options . "***" . $GLOBALS['currency']->get_currency_display( $order_totals->vat_total );
	else if( $GLOBALS['ec_setting']->get_shipping_method() == "live" )
		echo $GLOBALS['currency']->get_currency_display( $order_totals->shipping_total ) . "***" . $GLOBALS['currency']->get_currency_display( $order_totals->grand_total ) . "***" . "<div class=\"ec_cart_shipping_method_row\">" . $GLOBALS['language']->get_text( 'cart_estimate_shipping', 'cart_estimate_shipping_error' ) . "</div>";
	else
		echo $GLOBALS['currency']->get_currency_display( $order_totals->shipping_total ) . "***" . $GLOBALS['currency']->get_currency_display( $order_totals->grand_total ) . "***" . $shipping_options;
		
	die(); // this is required to return a proper result
	
}

add_action( 'wp_ajax_ec_ajax_update_payment_method', 'ec_ajax_update_payment_method' );
add_action( 'wp_ajax_nopriv_ec_ajax_update_payment_method', 'ec_ajax_update_payment_method' );
function ec_ajax_update_payment_method( ){
	wpeasycart_session( )->handle_session( );
	
	//Get the variables from the AJAX call
	$payment_method = $_POST['payment_method'];
	$GLOBALS['ec_cart_data']->cart_data->payment_method = $payment_method;
	$GLOBALS['ec_cart_data']->save_session_to_db( );
	die(); // this is required to return a proper result
	
}

add_action( 'wp_ajax_ec_ajax_insert_customer_review', 'ec_ajax_insert_customer_review' );
add_action( 'wp_ajax_nopriv_ec_ajax_insert_customer_review', 'ec_ajax_insert_customer_review' );
function ec_ajax_insert_customer_review( ){
	wpeasycart_session( )->handle_session( );
	
	//Get the variables from the AJAX call
	$product_id = (int) $_POST['product_id'];
	$rating = (int) $_POST['review_score'];
	$title = strip_tags( $_POST['review_title'] );
	$description = strip_tags( $_POST['review_message'] );
	
	//Create a new db and submit review
	$db = new ec_db();
	echo $db->submit_customer_review( $product_id, $rating, $title, $description, $GLOBALS['ec_user']->user_id );
	
	die(); // this is required to return a proper result
	
}

add_action( 'wp_ajax_ec_ajax_live_search', 'ec_ajax_live_search' );
add_action( 'wp_ajax_nopriv_ec_ajax_live_search', 'ec_ajax_live_search' );
function ec_ajax_live_search( ){
	
	//Get the variables from the AJAX call
	$search_val = $_POST['search_val'];
	
	//Create a new db and submit review
	$db = new ec_db();
	$results = $db->get_live_search_options( $search_val );
	echo json_encode( $results );
	
	die(); // this is required to return a proper result
	
}

add_action( 'wp_ajax_ec_ajax_close_newsletter', 'ec_ajax_close_newsletter' );
add_action( 'wp_ajax_nopriv_ec_ajax_close_newsletter', 'ec_ajax_close_newsletter' );
function ec_ajax_close_newsletter( ){
	
	setcookie( 'ec_newsletter_popup', 'hide', time( ) + ( 10 * 365 * 24 * 60 * 60 ), "/" );
	
	die(); // this is required to return a proper result
	
}

add_action( 'wp_ajax_ec_ajax_submit_newsletter_signup', 'ec_ajax_submit_newsletter_signup' );
add_action( 'wp_ajax_nopriv_ec_ajax_submit_newsletter_signup', 'ec_ajax_submit_newsletter_signup' );
function ec_ajax_submit_newsletter_signup( ){
	
	$newsletter_name = "";
	if( isset( $_POST['newsletter_name'] ) ){
		$newsletter_name = $_POST['newsletter_name'];
	}
	
	if( filter_var( $_POST['email_address'], FILTER_VALIDATE_EMAIL ) ){
		$ec_db = new ec_db();
		$ec_db->insert_subscriber( $_POST['email_address'], $newsletter_name, "" );
				
		// MyMail Hook
		if( function_exists( 'mailster' ) ){
			$subscriber_id = mailster('subscribers')->add(array(
				'email' => $_POST['email_address'],
				'name' => $newsletter_name,
				'status' => 1,
			), false );
		}
			
		do_action( 'wpeasycart_subscriber_added', $_POST['email_address'], $_POST['newsletter_name'] );
	}
	setcookie( 'ec_newsletter_popup', 'hide', time( ) + ( 10 * 365 * 24 * 60 * 60 ), "/" );
	
	die(); // this is required to return a proper result
	
}

add_action( 'wp_ajax_ec_ajax_create_stripe_ideal_order', 'ec_ajax_create_stripe_ideal_order' );
add_action( 'wp_ajax_nopriv_ec_ajax_create_stripe_ideal_order', 'ec_ajax_create_stripe_ideal_order' );
function ec_ajax_create_stripe_ideal_order( ){
	$source = $_POST['source'];
	$cartpage = new ec_cartpage( );
	$order_id = $cartpage->insert_ideal_order( $source );
	die( );
}

add_action( 'wp_ajax_ec_ajax_subscribe_to_stock_notification', 'ec_ajax_subscribe_to_stock_notification' );
add_action( 'wp_ajax_nopriv_ec_ajax_subscribe_to_stock_notification', 'ec_ajax_subscribe_to_stock_notification' );
function ec_ajax_subscribe_to_stock_notification( ){
	$email = $_POST['email'];
	$product_id = $_POST['product_id'];
	$cartpage = new ec_cartpage( );
	
	$recaptcha_valid = true;
	if( get_option( 'ec_option_enable_recaptcha' ) ){
		if( !isset( $_POST['recaptcha_response'] ) || $_POST['recaptcha_response'] == '' ){
            echo 'error';
            die( );

        }

        $db = new ec_db_admin( );
        $recaptcha_response = $_POST['recaptcha_response'];

        $data = array(
            "secret"	=> get_option( 'ec_option_recaptcha_secret_key' ),
            "response"	=> $recaptcha_response
        );
        $verify = curl_init();
        curl_setopt( $verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify" );
        curl_setopt( $verify, CURLOPT_POST, true );
        curl_setopt( $verify, CURLOPT_POSTFIELDS, http_build_query( $data ) );
        curl_setopt( $verify, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $verify, CURLOPT_RETURNTRANSFER, true );
        $response = curl_exec( $verify );
        if( $response === false ){
            $db->insert_response( 0, 1, "GOOGLE RECAPTCHA CURL ERROR", curl_error( $verify ) );
            $response = (object) array( "error" => curl_error( $verify ) );
        }else{
            $response = json_decode( $response );
            $db->insert_response( 0, 0, "Google Recaptcha Response", print_r( $response, true ) );
        }
        curl_close( $verify );

        $recaptcha_valid = ( isset( $response->success ) && $response->success ) ? true : false;
	}
	
	if( $recaptcha_valid ){
		global $wpdb;
		
		$found = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ec_product_subscriber WHERE email = %s AND product_id = %d", $email, $product_id ) );
		if( !$found ){
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_product_subscriber( email, product_id ) VALUES( %s, %d )", $email, $product_id ) );
		}else{
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product_subscriber SET status = 'subscribed' WHERE email = %s AND product_id = %d", $email, $product_id ) );
		}
		
	}else{
		echo 'error';
	}
	
	die( );
}

add_action( 'wp_ajax_ec_ajax_check_stripe_ideal_order', 'ec_ajax_check_stripe_ideal_order' );
add_action( 'wp_ajax_nopriv_ec_ajax_check_stripe_ideal_order', 'ec_ajax_check_stripe_ideal_order' );
function ec_ajax_check_stripe_ideal_order( ){
	global $wpdb;
	$order = $wpdb->get_row( $wpdb->prepare( "SELECT ec_order.order_id FROM ec_order, ec_orderstatus WHERE ec_order.gateway_transaction_id = %s AND ec_order.orderstatus_id = ec_orderstatus.status_id AND is_approved = 1", $_POST['source'] . ':' . $_POST['client_secret'] ) );
	$failed_order = $wpdb->get_row( $wpdb->prepare( "SELECT ec_order.order_id FROM ec_order WHERE ec_order.gateway_transaction_id = %s", $_POST['source'] . ':' . $_POST['client_secret'] ) );
	if( $order ){
		// Clear tempcart
		$ec_db_admin = new ec_db_admin( );
		$ec_db_admin->clear_tempcart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$GLOBALS['ec_cart_data']->checkout_session_complete( );
		$GLOBALS['ec_cart_data']->save_session_to_db( );
		echo $order->order_id;
	
	}else if( !$failed_order ){
		echo 'failed';
	
	}else{
		echo '0';
	}
	die( );
}

add_action( 'wp_ajax_ec_ajax_check_stripe_ideal_order_skip', 'ec_ajax_check_stripe_ideal_order_skip' );
add_action( 'wp_ajax_nopriv_ec_ajax_check_stripe_ideal_order_skip', 'ec_ajax_check_stripe_ideal_order_skip' );
function ec_ajax_check_stripe_ideal_order_skip( ){
	global $wpdb;
	$order = $wpdb->get_row( $wpdb->prepare( "SELECT ec_order.order_id, ec_orderstatus.is_approved FROM ec_order, ec_orderstatus WHERE ec_order.gateway_transaction_id = %s AND ec_order.orderstatus_id = ec_orderstatus.status_id", $_POST['source'] . ':' . $_POST['client_secret'] ) );
	if( $order ){
		// Clear tempcart
		$ec_db_admin = new ec_db_admin( );
		$ec_db_admin->clear_tempcart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$GLOBALS['ec_cart_data']->checkout_session_complete( );
		$GLOBALS['ec_cart_data']->save_session_to_db( );
        $response = array(
            'order_id'    => $order->order_id,
            'is_approved' => $order->is_approved,
            'status'      => 'skip'
        );
		echo json_encode( $response );
	
	}else{
		$response = array(
            'status'    => 'failed'
        );
	
	}
	die( );
}

add_action( 'wp_ajax_ec_ajax_save_page_options', 'ec_ajax_save_page_options' );
function ec_ajax_save_page_options( ){
	
	if( current_user_can( 'manage_options' ) ){
		update_option( 'ec_option_design_saved', 1 );
		$db = new ec_db( );
		$post_id = $_POST['post_id'];
		foreach( $_POST as $key => $var ){
			
			if( $key == 'ec_option_details_main_color' ){
				update_option( 'ec_option_details_main_color', $_POST['ec_option_details_main_color'] );
			}else if( $key == 'ec_option_details_second_color' ){
				update_option( 'ec_option_details_second_color', $_POST['ec_option_details_second_color'] );
			}else if( $key != 'post_id' ){
				$db->update_page_option( $post_id, $key, $var );
			}
			
		}
		do_action( 'wpeasycart_page_options_updated' );
	}	
	die( );
	
}

add_action( 'wp_ajax_ec_ajax_save_page_default_options', 'ec_ajax_save_page_default_options' );
function ec_ajax_save_page_default_options( ){
	
	if( current_user_can( 'manage_options' ) ){
		update_option( 'ec_option_design_saved', 1 );
		$db = new ec_db( );
		$post_id = $_POST['post_id'];
		foreach( $_POST as $key => $var ){
			
			if( $key != 'post_id' ){
				update_option( $key, $var );
			}
			
		}
		do_action( 'wpeasycart_page_options_updated' );
	}
	die( );
	
}

add_action( 'wp_ajax_ec_ajax_save_hide_video_option', 'ec_ajax_save_hide_video_option' );
function ec_ajax_save_hide_video_option( ){
	
	if( current_user_can( 'manage_options' ) ){
		update_option( 'ec_option_hide_design_help_video', '1' );
		do_action( 'wpeasycart_page_options_updated' );
	}
	die( );
	
}

add_action( 'wp_ajax_ec_ajax_save_product_options', 'ec_ajax_save_product_options' );
function ec_ajax_save_product_options( ){
	
	if( current_user_can( 'manage_options' ) ){
		$model_number = $_POST['model_number'];
		
		$product_options = new stdClass( );
		$product_options->image_hover_type = $_POST['image_hover_type'];
		$product_options->image_effect_type = $_POST['image_effect_type'];
		$product_options->tag_type = $_POST['tag_type'];
		$product_options->tag_text = stripslashes( $_POST['tag_text'] );
		$product_options->tag_bg_color = $_POST['tag_bg_color'];
		$product_options->tag_text_color = $_POST['tag_text_color'];
		
		$db = new ec_db( );
		$db->update_product_options( $model_number, $product_options );
		do_action( 'wpeasycart_page_options_updated' );
	}
	die( );
	
}

add_action( 'wp_ajax_ec_ajax_mass_save_product_options', 'ec_ajax_mass_save_product_options' );
function ec_ajax_mass_save_product_options( ){
	
	if( current_user_can( 'manage_options' ) ){
		$product_list = $_POST['products'];
		
		$product_options = new stdClass( );
		$product_options->image_hover_type = $_POST['image_hover_type'];
		$product_options->image_effect_type = $_POST['image_effect_type'];
		
		$db = new ec_db( );
		foreach( $product_list as $model_number ){
			$db->update_product_options( $model_number, $product_options );
		}
		do_action( 'wpeasycart_page_options_updated' );
	}
	die( );
	
}

add_action( 'wp_ajax_ec_ajax_save_product_order', 'ec_ajax_save_product_order' );
function ec_ajax_save_product_order( ){
	
	if( current_user_can( 'manage_options' ) ){
		$post_id = $_POST['post_id'];
		$product_order = $_POST['product_order'];
		
		$db = new ec_db( );
		$db->update_page_option( $post_id, 'product_order', $product_order );
		do_action( 'wpeasycart_page_options_updated' );
	}
	die( );
	
}

add_action( 'wp_ajax_ec_ajax_ec_update_product_description', 'ec_ajax_ec_update_product_description' );
function ec_ajax_ec_update_product_description( ){
	
	if( current_user_can( 'manage_options' ) ){
		$description = $_POST['description'];
		$product_id = $_POST['product_id'];
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET ec_product.description = %s WHERE ec_product.product_id = %d", $description, $product_id ) );
		do_action( 'wpeasycart_page_options_updated' );
	}
	die( );
	
}

add_action( 'wp_ajax_ec_ajax_ec_update_product_specifications', 'ec_ajax_ec_update_product_specifications' );
function ec_ajax_ec_update_product_specifications( ){
	
	if( current_user_can( 'manage_options' ) ){
		$specifications = $_POST['specifications'];
		$product_id = $_POST['product_id'];
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET ec_product.specifications = %s WHERE ec_product.product_id = %d", $specifications, $product_id ) );
		do_action( 'wpeasycart_page_options_updated' );
	}
	die( );
	
}

add_action( 'wp_ajax_ec_ajax_save_product_details_options', 'ec_ajax_save_product_details_options' );
function ec_ajax_save_product_details_options( ){
	
	if( current_user_can( 'manage_options' ) ){
		update_option( 'ec_option_details_main_color', $_POST['ec_option_details_main_color'] );
		update_option( 'ec_option_details_columns_desktop', $_POST['ec_option_details_columns_desktop'] );
		update_option( 'ec_option_details_columns_laptop', $_POST['ec_option_details_columns_laptop'] );
		update_option( 'ec_option_details_columns_tablet_wide', $_POST['ec_option_details_columns_tablet_wide'] );
		update_option( 'ec_option_details_columns_tablet', $_POST['ec_option_details_columns_tablet'] );
		update_option( 'ec_option_details_columns_smartphone', $_POST['ec_option_details_columns_smartphone'] );
		update_option( 'ec_option_use_dark_bg', $_POST['ec_option_use_dark_bg'] );
		do_action( 'wpeasycart_page_options_updated' );
	}
	die( );
	
}

add_action( 'wp_ajax_ec_ajax_save_cart_options', 'ec_ajax_save_cart_options' );
function ec_ajax_save_cart_options( ){
	
	if( current_user_can( 'manage_options' ) ){
		update_option( 'ec_option_cart_columns_desktop', $_POST['ec_option_cart_columns_desktop'] );
		update_option( 'ec_option_cart_columns_laptop', $_POST['ec_option_cart_columns_laptop'] );
		update_option( 'ec_option_cart_columns_tablet_wide', $_POST['ec_option_cart_columns_tablet_wide'] );
		update_option( 'ec_option_cart_columns_tablet', $_POST['ec_option_cart_columns_tablet'] );
		update_option( 'ec_option_cart_columns_smartphone', $_POST['ec_option_cart_columns_smartphone'] );
		update_option( 'ec_option_use_dark_bg', $_POST['ec_option_use_dark_bg'] );
		do_action( 'wpeasycart_page_options_updated' );
	}
	die( );
	
}

add_action( 'wp_ajax_ec_ajax_get_dynamic_cart_menu', 'ec_ajax_get_dynamic_cart_menu' );
add_action( 'wp_ajax_nopriv_ec_ajax_get_dynamic_cart_menu', 'ec_ajax_get_dynamic_cart_menu' );
function ec_ajax_get_dynamic_cart_menu( ){
	if( isset( $_POST['language'] ) ){
		$GLOBALS['language']->set_language( $_POST['language'] );
	}
	$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
	if( !get_option( 'ec_option_hide_cart_icon_on_empty' ) || $cart->total_items > 0 ){
	
		// Get Cart Page Link
		$cartpageid = get_option('ec_option_cartpage');
		if( function_exists( 'icl_object_id' ) ){
			$cartpageid = icl_object_id( $cartpageid, 'page', true, ICL_LANGUAGE_CODE );
		}
		$cartpage = get_permalink( $cartpageid );
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$cartpage = $https_class->makeUrlHttps( $cartpage );
		}
		
		$cartpage = apply_filters( 'wpml_permalink', $cartpage, esc_attr( $_POST['language'] ) );
		
		// Check for correct Label
		if( $cart->total_items != 1 ){
			$items_label = $GLOBALS['language']->get_text( 'cart', 'cart_menu_icon_label_plural' );
		}else{
			$items_label = $GLOBALS['language']->get_text( 'cart', 'cart_menu_icon_label' );
		}
		
		// Then display to user
		if( $cart->total_items > 0 ){
			$items = '<a href="' . $cartpage . '"><span class="dashicons dashicons-cart" style="vertical-align:middle; margin-top:-5px; margin-right:5px;"></span> ' . ' ( <span class="ec_menu_cart_text"><span class="ec_cart_items_total">' . $cart->total_items . '</span> ' . $items_label . ' <span class="ec_cart_price_total">' . $GLOBALS['currency']->get_currency_display( $cart->subtotal ) . '</span></span> )</a>';
		
		}else{
			$items = '<a href="' . $cartpage . '"><span class="dashicons dashicons-cart" style="vertical-align:middle; margin-top:-5px; margin-right:5px;"></span> ' . ' ( <span class="ec_menu_cart_text"><span class="ec_cart_items_total">' . $cart->total_items . '</span> ' . $items_label . ' <span class="ec_cart_price_total"></span></span> )</a>';
		}
		
		echo $items;
		
	}
	
	die( );
	
}

// Helper function for AJAX calls in cart.
function ec_get_order_totals( $cart = false ){
	
	if( !$cart )
		$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
	$user =& $GLOBALS['ec_user'];
	
	$coupon_code = "";
	if( $GLOBALS['ec_cart_data']->cart_data->coupon_code != "" )
		$coupon_code = $GLOBALS['ec_cart_data']->cart_data->coupon_code;
		
	$gift_card = "";
	if( $GLOBALS['ec_cart_data']->cart_data->giftcard != "" )
		$gift_card = $GLOBALS['ec_cart_data']->cart_data->giftcard;
	
	// Shipping
	$sales_tax_discount = new ec_discount( $cart, $cart->discountable_subtotal, 0.00, $coupon_code, "", 0 );
	$GLOBALS['wpeasycart_current_coupon_discount'] = $sales_tax_discount->coupon_discount;
	$shipping = new ec_shipping( $cart->shipping_subtotal, $cart->weight, $cart->shippable_total_items, 'RADIO', $GLOBALS['ec_user']->freeshipping, $cart->length, $cart->width, $cart->height, $cart->cart );
	$shipping_price = $shipping->get_shipping_price( $cart->get_handling_total( ) );
	// Tax (no VAT here)
	$sales_tax_discount = new ec_discount( $cart, $cart->discountable_subtotal, 0.00, $coupon_code, "", 0 );
	$tax = new ec_tax( $cart->subtotal, $cart->taxable_subtotal - $sales_tax_discount->coupon_discount, 0, $GLOBALS['ec_cart_data']->cart_data->shipping_state, $GLOBALS['ec_cart_data']->cart_data->shipping_country, $GLOBALS['ec_user']->taxfree, $shipping_price, $cart );
	// Duty (Based on Product Price) - already calculated in tax
	// Get Total Without VAT, used only breifly
	if( get_option( 'ec_option_no_vat_on_shipping' ) ){
		$total_without_vat_or_discount = $cart->vat_subtotal + $tax->tax_total + $tax->duty_total;
	}else{
		$total_without_vat_or_discount = $cart->vat_subtotal + $shipping_price + $tax->tax_total + $tax->duty_total;
	}
	//If a discount used, and no vatable subtotal, we need to set to 0
	if( $total_without_vat_or_discount < 0 )
		$total_without_vat_or_discount = 0;
	// Discount for Coupon
	$discount = new ec_discount( $cart, $cart->discountable_subtotal, $shipping_price, $coupon_code, $gift_card, $total_without_vat_or_discount );
	// Amount to Apply VAT on
	$promotion = new ec_promotion( );
	$vatable_subtotal = $total_without_vat_or_discount - $tax->tax_total - $discount->coupon_discount - $promotion->get_discount_total( $cart->subtotal );
	// If for some reason this is less than zero, we should correct
	if( $vatable_subtotal < 0 )
		$vatable_subtotal = 0;
	// Get Tax Again For VAT
	$tax = new ec_tax( $cart->subtotal, $cart->taxable_subtotal - $sales_tax_discount->coupon_discount, $vatable_subtotal, $GLOBALS['ec_cart_data']->cart_data->shipping_state, $GLOBALS['ec_cart_data']->cart_data->shipping_country, $GLOBALS['ec_user']->taxfree, $shipping_price, $cart );
	// Discount for Gift Card
	$grand_total = ( $cart->subtotal + $tax->tax_total + $shipping_price + $tax->duty_total );
	$discount = new ec_discount( $cart, $cart->discountable_subtotal, $shipping_price, $coupon_code, $gift_card, $grand_total );
	// Order Totals
	$order_totals = new ec_order_totals( $cart, $GLOBALS['ec_user'], $shipping, $tax, $discount );
	return $order_totals;
}

function ec_get_cart_data( ){
	$cartpage = new ec_cartpage( );
    
	// GET NEW CART ITEM INFO
	$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
	$cart_array = array( );
	
	for( $i=0; $i<count( $cart->cart ); $i++ ){
		$cart_item = array( 
				"id" 								=> $cart->cart[$i]->cartitem_id,
				"unit_price" 						=> $cart->cart[$i]->get_unit_price( ),
				"total_price" 						=> $cart->cart[$i]->get_total( ),
				"quantity" 							=> $cart->cart[$i]->quantity,
				"stock_quantity"					=> $cart->cart[$i]->stock_quantity,
				"allow_backorders"					=> $cart->cart[$i]->allow_backorders,
				"use_optionitem_quantity_tracking"	=> $cart->cart[$i]->use_optionitem_quantity_tracking,
				"optionitem_stock_quantity"			=> $cart->cart[$i]->optionitem_stock_quantity
		);
		$cart_array[] = $cart_item;
	}
	// GET NEW CART ITEM INFO
	$order_totals = ec_get_order_totals( $cart );
	
	if( $order_totals->discount_total != 0 )
		$has_discount = 1;
	else
		$has_discount = 0;
	
	$order_totals_array = array( 
		"sub_total_amt"                             => $order_totals->get_converted_sub_total( ),
        "sub_total" 								=> $GLOBALS['currency']->get_currency_display( $order_totals->get_converted_sub_total( ), false ), 
		"tax_total" 								=> $GLOBALS['currency']->get_currency_display( $order_totals->tax_total ),
		"has_tax"									=> ( ( $order_totals->tax_total > 0 ) ? 1 : 0 ),
		"shipping_total" 							=> $GLOBALS['currency']->get_currency_display( $order_totals->shipping_total ),
		"duty_total" 								=> $GLOBALS['currency']->get_currency_display( $order_totals->duty_total ),
		"has_duty"									=> ( ( $order_totals->duty_total > 0 ) ? 1 : 0 ),
		"vat_total" 								=> $GLOBALS['currency']->get_currency_display( $order_totals->vat_total ),
		"has_vat"									=> ( ( $order_totals->vat_total > 0 ) ? 1 : 0 ),
		"vat_rate_formatted"						=> $cartpage->get_vat_rate_formatted( ),
		"gst_total" 								=> $GLOBALS['currency']->get_currency_display( $order_totals->gst_total ),
		"has_gst"									=> ( ( $order_totals->gst_total > 0 ) ? 1 : 0 ),
		"hst_total" 								=> $GLOBALS['currency']->get_currency_display( $order_totals->hst_total ),
		"has_hst"									=> ( ( $order_totals->hst_total > 0 ) ? 1 : 0 ),
		"pst_total" 								=> $GLOBALS['currency']->get_currency_display( $order_totals->pst_total ),
		"has_pst"									=> ( ( $order_totals->pst_total > 0 ) ? 1 : 0 ),
        "tip_total" 								=> $GLOBALS['currency']->get_currency_display( $order_totals->tip_total ),
		"discount_total" 							=> $GLOBALS['currency']->get_currency_display( (-1) * $order_totals->discount_total ),
		"grand_total" 								=> $GLOBALS['currency']->get_currency_display( $order_totals->get_converted_grand_total( ), false )
	);
    
    ob_start( );
    $cartpage->print_stripe_payment_button( false );
    $stripe_button = ob_get_clean( );
	
	$final_array = apply_filters( 'wp_easycart_cart_update_response', array( 	
		"cart" 										=> $cart_array,
		"order_totals"								=> $order_totals_array,
		"items_total"								=> $cart->total_items,
		"weight_total"								=> $cart->weight,
		"has_discount"								=> $has_discount,
		"has_backorder"								=> $cart->has_backordered_item( ),
        "stripe_wallet"                             => $stripe_button
	) );
							
	return $final_array;
}

add_action( 'wp_ajax_ec_ajax_get_cart', 'ec_ajax_get_cart' );
add_action( 'wp_ajax_nopriv_ec_ajax_get_cart', 'ec_ajax_get_cart' );
function ec_ajax_get_cart( ){
	
	//Get the variables from the AJAX call
	$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
	$retarray = array( );
	
	foreach( $cart->cart as $cartitem ){
		$retarray[] = array( "cartitem_id"	=> $cartitem->cartitem_id, 
							 "title"		=> $cartitem->title,
							 "quantity"		=> $cartitem->quantity, 
							 "unit_price"	=> $GLOBALS['currency']->get_currency_display( $cartitem->unit_price ) );
	}
	
	echo json_encode( $retarray );
	
	die(); // this is required to return a proper result
}

add_action( 'wp_ajax_ec_ajax_get_cart_totals', 'ec_ajax_get_cart_totals' );
add_action( 'wp_ajax_nopriv_ec_ajax_get_cart_totals', 'ec_ajax_get_cart_totals' );
function ec_ajax_get_cart_totals( ){
	
	//Get the variables from the AJAX call
	$cartpage = new ec_cartpage( );
	
	$retarray = array( 	"sub_total"			=> $GLOBALS['currency']->get_currency_display( $cartpage->order_totals->sub_total ), 
						"tax_total"			=> $GLOBALS['currency']->get_currency_display( $cartpage->order_totals->tax_total ), 
						"shipping_total"	=> $GLOBALS['currency']->get_currency_display( $cartpage->order_totals->shipping_total ), 
						"duty_total"		=> $GLOBALS['currency']->get_currency_display( $cartpage->order_totals->duty_total ), 
						"vat_total"			=> $GLOBALS['currency']->get_currency_display( $cartpage->order_totals->vat_total ), 
						"discount_total"	=> $GLOBALS['currency']->get_currency_display( $cartpage->order_totals->discount_total ), 
						"grand_total"		=> $GLOBALS['currency']->get_currency_display( $cartpage->order_totals->grand_total ) );
	
	echo json_encode( $retarray );
	
	die(); // this is required to return a proper result
}

add_action( 'wp_ajax_ec_ajax_get_dynamic_cart_page', 'ec_ajax_get_dynamic_cart_page' );
add_action( 'wp_ajax_nopriv_ec_ajax_get_dynamic_cart_page', 'ec_ajax_get_dynamic_cart_page' );
function ec_ajax_get_dynamic_cart_page( ){
	
    if( !preg_match( '/[0-9]+/', $_POST['cart_page'] ) && !preg_match( '/[0-9]+\-[0-9]+/', $_POST['cart_page'] ) ){
        die( );
    }
    
	//Get the variables from the AJAX call
	$cartpage = new ec_cartpage( );
	$cartpage->display_cart_dynamic( $_POST['cart_page'], $_POST['success_code'], $_POST['error_code'] );
	die(); // this is required to return a proper result
}

add_action( 'wp_ajax_ec_ajax_get_dynamic_account_page', 'ec_ajax_get_dynamic_account_page' );
add_action( 'wp_ajax_nopriv_ec_ajax_get_dynamic_account_page', 'ec_ajax_get_dynamic_account_page' );
function ec_ajax_get_dynamic_account_page( ){
	
    $pages = array( 'forgot_password', 'register', 'billing_information', 'shipping_information', 'personal_information', 'password', 'orders', 'order_details', 'subscription', 'subscriptions', 'subscription_details' );
    if( $_POST['account_page'] != '' && !in_array( $_POST['account_page'], $pages ) && substr( $_POST['account_page'], 0, 13 ) != 'order_details' && substr( $_POST['account_page'], 0, 20 ) != 'subscription_details' ){
        $account_page = '';
    }else{
        $account_page = $_POST['account_page'];
    }
    
    
	//Get the variables from the AJAX call
	$accountpage = new ec_accountpage( );
	$accountpage->display_account_dynamic( $account_page, (int) $_POST['page_id'], $_POST['success_code'], $_POST['error_code'] );
	die(); // this is required to return a proper result
}
// End AJAX helper function for cart.

add_filter( 'wp_title', 'ec_custom_title', 20 );

function ec_custom_title( $title ) {
	
	$page_id = get_the_ID();
	$store_id = get_option( 'ec_option_storepage' );
	
	if( $page_id == $store_id && isset( $_GET['model_number'] ) ){
		$db = new ec_db( );
		$products = $db->get_product_list( " WHERE product.model_number = '" . $_GET['model_number'] . "'", "", "", "" );
		if( count( $products ) > 0 ){
			$custom_title = $products[0]['title'] . " |" . $title;
			return $custom_title;
		}else{
			return $title;
		}
	}else if( $page_id == $store_id ){
		
		$additional_title = "";
		
		if( isset( $_GET['manufacturer'] ) ){
			$db = new ec_db( );
			$manufacturer = $db->get_manufacturer_row( $_GET['manufacturer'] );
			
			$additional_title .= $manufacturer->name . " |";
		}
		
		if( isset( $_GET['menu'] ) ){
			$custom_title = $_GET['menu'] . " |" . $additional_title . $title;
			return $custom_title;
		}else if( isset( $_GET['submenu'] ) ){
			$custom_title = $_GET['submenu'] . " |" . $additional_title . $title;
			return $custom_title;
		}else if( isset( $_GET['subsubmenu'] ) ){
			$custom_title = $_GET['subsubmenu'] . " |" . $additional_title . $title;
			return $custom_title;
		}else{
			return $additional_title . $title;
		}	
	}else{
		return $title;
	}
	
}

function ec_theme_options_page_callback( ){
	if( is_dir( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option('ec_option_base_theme') . "/" ) )
		include( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option('ec_option_base_theme') . "/admin_panel.php");
	else
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option('ec_option_latest_theme') . "/admin_panel.php");
}

/////////////////////////////////////////////////////////////////////
//CUSTOM POST TYPES
/////////////////////////////////////////////////////////////////////
add_action( 'init', 'ec_create_post_type_menu' );
function ec_create_post_type_menu() {
	
	// Fix, V3 upgrades missed the ec_tempcart_optionitem.session_id upgrade!
	if( !get_option( 'ec_option_v3_fix' ) ){
		global $wpdb;
		$wpdb->query( "INSERT INTO ec_tempcart_optionitem( tempcart_id, option_id, optionitem_id, optionitem_value ) VALUES( '999999999', '3', '3', 'test' )" );
		$tempcart_optionitem_row = $wpdb->get_row( "SELECT * FROM ec_tempcart_optionitem WHERE tempcart_id = '999999999'" );
		if( !isset( $tempcart_optionitem_row->session_id ) ){
			$wpdb->query( "ALTER TABLE ec_tempcart_optionitem ADD `session_id` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'The ec_cart_id that determines the user who entered this value.'" );
		}
		update_option( 'ec_option_v3_fix', 1 );
	}
	
	// Update store item posts, set to private if inactive in store
	if( !get_option( 'ec_option_published_check' ) || get_option( 'ec_option_published_check' ) != EC_CURRENT_VERSION ){	
		global $wpdb;
		$language = new ec_language( );
		$inactive_products = $wpdb->get_results( "SELECT ec_product.post_id, ec_product.model_number, ec_product.title FROM ec_product WHERE ec_product.activate_in_store = 0" );
		foreach( $inactive_products as $product ){
			$post = array(	'ID'			=> $product->post_id,
							'post_content'	=> "[ec_store modelnumber=\"" . $product->model_number . "\"]",
							'post_status'	=> "private",
							'post_title'	=> $language->convert_text( $product->title ),
							'post_type'		=> "ec_store",
							'post_name'		=> str_replace(' ', '-', $language->convert_text( $product->title ) ),
					  );
			wp_update_post( $post );
		}
		update_option( 'ec_option_published_check', EC_CURRENT_VERSION );
	}
	
	$store_id = get_option( 'ec_option_storepage' );
	if( $store_id ){
		$store_slug = ec_get_the_slug( $store_id );
		
		$labels = array(
			'name'               => _x( 'Store Items', 'post type general name' ),
			'singular_name'      => _x( 'Store Item', 'post type singular name' ),
			'add_new'            => _x( 'Add New', 'ec_store' ),
			'add_new_item'       => __( 'Add New Store Item' ),
			'edit_item'          => __( 'Edit Store Item' ),
			'new_item'           => __( 'New Store Item' ),
			'all_items'          => __( 'All Store Items' ),
			'view_item'          => __( 'View Store Item' ),
			'search_items'       => __( 'Search Store Items' ),
			'not_found'          => __( 'No store items found' ),
			'not_found_in_trash' => __( 'No store items found in the Trash' ), 
			'parent_item_colon'  => '',
			'menu_name'          => 'Store Items'
		);
		$args = array(
			'labels'        	=> $labels,
			'description' 		=> 'Used for the EasyCart Store',
			'public' 			=> true,
			'has_archive' 		=> false,
			'show_ui' 			=> true,
			'show_in_nav_menus' => true,
			'show_in_menu' 		=> false,
			'supports'			=> array( 'title', 'page-attributes', 'author', 'editor', 'post-formats' ),
			'rewrite'			=> array( 'slug' => $store_slug, 'with_front' => false, 'page' => false ),
		);
		register_post_type( 'ec_store', $args );
		
		global $wp_rewrite;
		$wp_rewrite->add_permastruct( 'ec_store', $store_slug . '/%ec_store%/', true, 1 );
    	add_rewrite_rule( '^' . $store_slug . '/([^/]*)/?$', 'index.php?ec_store=$matches[1]', 'top');
		
		// Only Flush Once!
		if( get_option( 'ec_option_added_custom_post_type' ) < 2 ){	
			$wp_rewrite->flush_rules();
			update_option( 'ec_option_added_custom_post_type', 2 );
		}
	}
}

function ec_get_the_slug( $id=null ){
	if( empty($id) ) : 
		global $post;
    	if( empty($post) )
			return '';
		$id = $post->ID;
	endif;
	$home_url = parse_url( site_url( ) );
	if( isset( $home_url['path'] ) )
		$home_path = $home_url['path'];
	else
		$home_path = '';
	
	$store_url = parse_url( get_permalink( get_option( 'ec_option_storepage' ) ) );
	$store_path = $store_url['path'];
	
	$path = ( strlen( $home_path ) == 0 || $home_path == "/" ) ? $store_path : str_replace( $home_path, "", $store_path );
	
	if( substr( $path, 0, 1 ) == '/' )
		$path = substr( $path, 1, strlen( $path ) - 1 );
	
	if( substr( $path, -1, 1 ) == '/' )
		$path = substr( $path, 0, strlen( $path ) - 1 );
	
	return $path;
}

add_action( 'wp', 'ec_force_page_type' );
function ec_force_page_type() {
	global $wp_query, $post_type;
	
	if( $post_type == 'ec_store' && !get_option( 'ec_option_use_custom_post_theme_template' ) ){
		$wp_query->is_page = true;
		$wp_query->is_single = false;
		$wp_query->query_vars['post_type'] = "page";
		if( isset( $wp_query->post ) )
			$wp_query->post->post_type = "page";
	}
}

add_filter( 'template_redirect', 'ec_fix_store_template', 1 );
function ec_fix_store_template( ){
	global $wp;
	$custom_post_types = array("ec_store");
	
	if( isset( $wp->query_vars["post_type"] ) && in_array( $wp->query_vars["post_type"], $custom_post_types ) ){
		$store_template = get_post_meta( get_option( 'ec_option_storepage' ), "_wp_page_template", true );
		if( isset( $store_template ) && $store_template != "" && $store_template != "default"  ){
			if( file_exists( get_template_directory( ) . "/" . $store_template ) ){
				include( get_template_directory( ) . "/" . $store_template );
				exit( );
			}
		}
	}
}

add_action( 'wp_easycart_square_renew_token', 'wp_easycart_square_renew_token' );
function wp_easycart_square_renew_token( ){
	if( get_option( 'ec_option_payment_process_method' ) == 'square' ){
		if( class_exists( 'ec_square' ) ){
			$square = new ec_square( );
			$square->renew_token( );
		}
	}else{
		wp_clear_scheduled_hook( 'wp_easycart_square_renew_token' );
	}
}

/////////////////////////////////////////////////////////////////////
//HELPER FUNCTIONS
/////////////////////////////////////////////////////////////////////
//Helper Function, Get URL
function ec_get_url(){
  if( isset( $_SERVER['HTTPS'] ) )
  	$protocol =  "https";
  else
	$protocol =  "http";
	
  $baseurl = "://" . $_SERVER['HTTP_HOST'];
  $strip = explode("/wp-admin", $_SERVER['REQUEST_URI']);
  $folder = $strip[0];
  return $protocol .  $baseurl . $folder;
}

function ec_setup_hooks( ){
	$GLOBALS['ec_hooks'] = array( );
}

function ec_add_hook( $call_location, $function_name, $args = array(), $priority = 1 ){
	if( !isset( $GLOBALS['ec_hooks'][$call_location] ) )
		$GLOBALS['ec_hooks'][$call_location] = array( );
	
	$GLOBALS['ec_hooks'][$call_location][] = array( $function_name, $args, $priority );
}

function ec_call_hook( $hook_array, $class_args ){
	$hook_array[0]( $hook_array[1], $class_args );
}

function ec_dwolla_verify_signature( $proposedSignature, $checkoutId, $amount ){
    $apiSecret = get_option( 'ec_option_dwolla_thirdparty_secret' );
	$amount = number_format( $amount, 2 );
    $signature = hash_hmac("sha1", "{$checkoutId}&{$amount}", $apiSecret);

    return $signature == $proposedSignature;
}

add_filter( 'wp_nav_menu_items', 'ec_custom_cart_in_menu', 10, 2 );
function ec_custom_cart_in_menu ( $items, $args ) {
	
	$ids = explode( '***', get_option( 'ec_option_cart_menu_id' ) );
	if( get_option( 'ec_option_show_menu_cart_icon' ) && ( in_array( substr( $args->menu_id, 0, -5 ), $ids ) || in_array( $args->theme_location, $ids ) ) ){
	
		$items .= '<li class="ec_menu_mini_cart"></li>';
		
	}
	
	return $items;
}

function wpeasycart_activation_redirect( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
		exit( wp_redirect( admin_url( 'admin.php?page=wp-easycart-settings' ) ) );
	}
}
add_action( 'activated_plugin', 'wpeasycart_activation_redirect' );

add_action( 'wpeasycart_abandoned_cart_automation', 'wpeasycart_send_abandoned_cart_emails' );
function wpeasycart_send_abandoned_cart_emails( ){
	global $wpdb;
	$abandoned_carts = $wpdb->get_results( $wpdb->prepare( "SELECT ec_tempcart.tempcart_id FROM ec_tempcart, ec_tempcart_data WHERE ec_tempcart.abandoned_cart_email_sent = 0 AND ec_tempcart.session_id = ec_tempcart_data.session_id AND ec_tempcart_data.email != '' AND ec_tempcart.last_changed_date < DATE_SUB( NOW( ), INTERVAL %d DAY ) GROUP BY ec_tempcart.session_id", get_option( 'ec_option_abandoned_cart_days' ) ) );
	foreach( $abandoned_carts as $abandoned_cart ){
		wpeasycart_send_abandoned_cart_email( $abandoned_cart->tempcart_id );
	}
}

function wpeasycart_send_abandoned_cart_email( $tempcart_id ){
	global $wpdb;
	$email_logo_url = get_option( 'ec_option_email_logo' ) . "' alt='" . get_bloginfo( "name" );
	
	$cart_page_id = get_option('ec_option_cartpage');
	if( function_exists( 'icl_object_id' ) )
		$cart_page_id = icl_object_id( $cart_page_id, 'page', true, ICL_LANGUAGE_CODE );
	$cart_page = get_permalink( $cart_page_id );
	if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
		$https_class = new WordPressHTTPS( );
		$cart_page = $https_class->makeUrlHttps( $cart_page );
	}
	if( substr_count( $cart_page, '?' ) )						$permalink_divider = "&";
	else														$permalink_divider = "?";
	
	$headers   = array();
	$headers[] = "MIME-Version: 1.0";
	$headers[] = "Content-Type: text/html; charset=utf-8";
	$headers[] = "From: " . stripslashes( get_option( 'ec_option_order_from_email' ) );
	$headers[] = "Reply-To: " . stripslashes( get_option( 'ec_option_order_from_email' ) );
	$headers[] = "X-Mailer: PHP/".phpversion();
	
	$tempcart_item = $wpdb->get_row( $wpdb->prepare( "SELECT ec_tempcart.session_id, ec_tempcart.tempcart_id, ec_tempcart.product_id, ec_tempcart.quantity, ec_tempcart_data.translate_to, ec_tempcart_data.billing_first_name, ec_tempcart_data.billing_last_name, ec_tempcart_data.email, ec_product.title FROM ec_tempcart LEFT JOIN ec_tempcart_data ON ec_tempcart_data.session_id = ec_tempcart.session_id LEFT JOIN ec_product ON ec_product.product_id = ec_tempcart.product_id WHERE ec_tempcart.tempcart_id = %d ORDER BY ec_tempcart.session_id, last_changed_date", $tempcart_id ) );
	if( $tempcart_item->translate_to != '' ){
		$GLOBALS['language']->set_language( $tempcart_item->translate_to );
	}
	$tempcart_rows = $wpdb->get_results( $wpdb->prepare( "SELECT ec_product.*, ec_tempcart.quantity AS tempcart_quantity, ec_tempcart.optionitem_id_1, ec_tempcart.optionitem_id_2, ec_tempcart.optionitem_id_3, ec_tempcart.optionitem_id_4, ec_tempcart.optionitem_id_5 FROM ec_tempcart, ec_product WHERE ec_product.product_id = ec_tempcart.product_id AND ec_tempcart.session_id = %s", $tempcart_item->session_id ) );
	
	$to = $tempcart_item->email;
	$subject = $GLOBALS['language']->get_text( 'ec_abandoned_cart_email', 'email_title' );
	
	ob_start();
	if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_abandoned_cart_email.php' ) )	
		include WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_abandoned_cart_email.php';	
	else
		include WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_abandoned_cart_email.php';
	$message = ob_get_clean( );
	
	$email_send_method = get_option( 'ec_option_use_wp_mail' );
	$email_send_method = apply_filters( 'wpeasycart_email_method', $email_send_method );
	
	if( $email_send_method == "1" ){
		wp_mail( $to, $subject, $message, implode("\r\n", $headers), $attachments );
	}else if( $email_send_method == "0" ){
		$mailer = new wpeasycart_mailer( );
		$mailer->send_order_email( $to, $subject, $message );
	}else{
		do_action( 'wpeasycart_custom_order_email', stripslashes( get_option( 'ec_option_order_from_email' ) ), $to, stripslashes( get_option( 'ec_option_bcc_email_addresses' ) ), $subject, $message );
	}
	$wpdb->query( $wpdb->prepare( "UPDATE ec_tempcart SET abandoned_cart_email_sent = 1 WHERE ec_tempcart.session_id = %s", $tempcart_item->session_id ) );
	
}

function is_wpeasycart_cart( ){
	global $is_wpec_cart;
	return $is_wpec_cart;
}

function wp_easycart_check_for_shortcode( $posts ){
    global $is_wpec_cart;
	$is_wpec_cart = false;
    $is_wpec_account = false;
	
    if( empty( $posts ) )
        return $posts;
    
	$found = false;
 
    foreach( $posts as $post ){
        if( $post->ID == get_option( 'ec_option_storepage' ) || $post->post_type == "ec_store" ){
            $found = true;
            break;
        }
	}
	
	foreach( $posts as $post ){
		if( stripos( $post->post_content, '[ec_cart' ) !== false ){
			$is_wpec_cart = true;
			break;
		}
	}
	
	foreach( $posts as $post ){
		if( stripos( $post->post_content, '[ec_account' ) !== false ){
			$is_wpec_account = true;
			break;
		}
	}
    
    if( $is_wpec_cart || $is_wpec_account ){
        add_action( 'wp_enqueue_scripts', 'wp_easycart_load_cart_js' );
    }
 
    if( $found ){
        add_filter( 'jetpack_enable_open_graph', '__return_false' ); 
    }
	
	if( trim( get_option( 'ec_option_fb_pixel' ) ) != '' ){
		$found = false;
		foreach( $posts as $post ){
			if( $post->post_type == "ec_store" ||
				stripos( $post->post_content, '[ec_store' ) !== false || 
				stripos( $post->post_content, '[ec_cart' ) !== false 
			){
				$found = true;
				break;
			}
		}
		
		if( $found ){
			add_action( 'wp_head', 'wp_easycart_init_facebook_pixel' );
		}
	}
	
    return $posts;
}

function wp_easycart_init_facebook_pixel( ){
	echo "<script>
			!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
			n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
			n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
			t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
			document,'script','https://connect.facebook.net/en_US/fbevents.js');
			// Insert Your Custom Audience Pixel ID below. 
			fbq('init', '" . get_option( 'ec_option_fb_pixel' ) . "');
			fbq('track', 'PageView');
		</script>";
	echo '<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=' . get_option( 'ec_option_fb_pixel' ) . '&ev=PageView&noscript=1" /></noscript>';
}
add_action( 'the_posts', 'wp_easycart_check_for_shortcode' );

function wp_easycart_restrict_access( ){
	$product_restrict = get_post_meta( get_the_ID( ), 'wpeasycart_restrict_product_id', true );
	$user_restrict = get_post_meta( get_the_ID( ), 'wpeasycart_restrict_user_id', true );
	$role_restrict = get_post_meta( get_the_ID( ), 'wpeasycart_restrict_role_id', true );
	$is_product_restricted = $is_user_restricted = $is_role_restricted = $is_restricted = false;
	
	$redirect_page = get_post_meta( get_the_ID( ), 'wpeasycart_restrict_redirect_url', true );
	$redirect_page_auth = get_post_meta( get_the_ID( ), 'wpeasycart_restrict_redirect_url_auth', true );
	$redirect_page_not_auth = get_post_meta( get_the_ID( ), 'wpeasycart_restrict_redirect_url_not_auth', true );
	
	$has_redirect = ( $redirect_page != "" || $redirect_page_not_auth != "" || $redirect_page_auth != ""  ) ? true : false;
	if( is_array( $product_restrict ) ){
		for( $i=0; $i<count( $product_restrict ); $i++ ){
			if( $product_restrict[$i] != '' ){
				$is_product_restricted = true;
			}
		}
	}
	if( is_array( $user_restrict ) ){
		for( $i=0; $i<count( $user_restrict ); $i++ ){
			if( $user_restrict[$i] != '' ){
				$is_user_restricted = true;
			}
		}
	}
	if( is_array( $role_restrict ) ){
		for( $i=0; $i<count( $role_restrict ); $i++ ){
			if( $role_restrict[$i] != '' ){
				$is_role_restricted = true;
			}
		}
	}
	
	$is_restricted = ( $is_product_restricted || $is_user_restricted || $is_role_restricted ) ? true : false;
	$is_logged_in = ( !$GLOBALS['ec_user']->user_id ) ? false : true;
	
	if( $has_redirect && $is_restricted ){
		
		// Not Logged In, Redirect Out
		if( $redirect_page != '' && !$is_logged_in ){
			wp_redirect( $redirect_page ); die( );
		
		// Not Logged In, But Allowed
		}else if( !$is_logged_in ){
			return;
			
		// Logged In + Content Retriction
		}else{
		
			$product_restrict_list = $user_restrict_list = $role_restrict_list = '(';
			if( is_array( $product_restrict ) ){
				for( $i=0; $i<count( $product_restrict ); $i++ ){
					if( $i>0 )
						$product_restrict_list .= ', ';
					$product_restrict_list .= $product_restrict[$i];
				}
				$product_restrict_list .= ')';
			}else{
				$product_restrict_list .= $product_restrict . ')';
			}
			
			$is_allowed = true;
			
			if( ( is_array( $product_restrict ) && count( $product_restrict ) > 0 && $product_restrict[0] != '' ) || ( !is_array( $product_restrict ) && $product_restrict != '' ) ){
				global $wpdb;
				$has_product = false;
				$products = $wpdb->get_results( "SELECT is_subscription_item, product_id FROM ec_product WHERE product_id IN " . $product_restrict_list );
				foreach( $products as $product ){
					if( $product->is_subscription_item ){
						$active_subscription = $wpdb->get_results( $wpdb->prepare( "SELECT subscription_id FROM ec_subscription WHERE user_id = %d AND product_id = %d AND subscription_status = 'Active'", $GLOBALS['ec_user']->user_id, $product->product_id ) );
						if( $active_subscription )
							$has_product = true;
							
					}else{
						$order_details = $wpdb->get_results( $wpdb->prepare( "SELECT ec_orderdetail.product_id FROM ec_order, ec_orderdetail, ec_orderstatus WHERE ec_order.user_id = %d AND ec_order.orderstatus_id = ec_orderstatus.status_id AND ec_orderstatus.is_approved = 1 AND ec_order.order_id = ec_orderdetail.order_id AND ec_orderdetail.product_id = %d", $GLOBALS['ec_user']->user_id, $product->product_id ) );
						if( $order_details )
							$has_product = true;
					}
				}
				if( !$has_product )
					$is_allowed = false;
			}
			
			if( ( is_array( $user_restrict ) && count( $user_restrict ) > 0 && $user_restrict[0] != '' ) || ( !is_array( $user_restrict ) && $user_restrict != '' ) ){
				$has_user = false;
				if( is_array( $user_restrict ) && in_array( $GLOBALS['ec_user']->user_id, $user_restrict ) )
					$has_user = true;
				else if( !is_array( $user_restrict ) && $GLOBALS['ec_user']->user_id == $user_restrict )
					$has_user = true;
				if( !$has_user )
					$is_allowed = false;
			}
			
			if( ( is_array( $role_restrict ) && count( $role_restrict ) > 0 && $role_restrict[0] != '' ) || ( !is_array( $role_restrict ) && $role_restrict != '' ) ){
				$has_role = false;
				if( is_array( $role_restrict ) && in_array( $GLOBALS['ec_user']->user_level, $role_restrict ) )
					$has_role = true;
				else if( !is_array( $role_restrict ) && $role_restrict != $GLOBALS['ec_user']->user_level )
					$has_role = true;
				if( !$has_role )
					$is_allowed = false;
			}
			
			// Check for account or payment type redirect first
			if( $redirect_page_not_auth != "" || $redirect_page_auth != "" ){
				
				// Allowed + Has Logged In Redirect
				if( $is_allowed && $redirect_page_auth != "" ){
					wp_redirect( $redirect_page_auth ); die( );
					
				// Not Allowed + No Purchase/Auth Link
				}else if( !$is_allowed && $redirect_page_not_auth != "" ){ 
					wp_redirect( $redirect_page_not_auth ); die( );
				
				}
				
			}
			
			// Not Allowed + Redirect Out Set
			if( $redirect_page != "" && !$is_allowed ){
				wp_redirect( $redirect_page ); die( );
			
			}
		}
		
	}
	return;
}
add_action( 'template_redirect', 'wp_easycart_restrict_access' );

add_action( 'wp_head', 'wp_easycart_show_404_help' );
function wp_easycart_show_404_help(  ){
	// First test for a common issue, possibly fixed here.
	if( is_404( ) && get_option( 'ec_option_storepage' ) == get_option( 'page_on_front' ) ){
		$post = array( 
			'post_content' 	=> "[ec_store]",
			'post_title' 	=> "Store",
			'post_type'		=> "page",
			'post_status'	=> "publish"
		 );
		$post_id = wp_insert_post( $post );
		update_option( 'ec_option_storepage', $post_id );
		flush_rewrite_rules( );
	
	// May times we see the user hit the store page with a 404 and can usually be fixed with a flush.
	}else if( wp_easycart_404_check( ) ){
		echo '<div style="position:relative; top:0; left:0; width:100%; background:red; padding:15px; text-align:center; color:#FFF; font-size:16px; font-weight:bold;">It appears your product is not linking correctly. Refreshing this page may automatically fix the issue, but lots of things can cause this, but we will help you out. Try reading here: <a href="http://docs.wpeasycart.com/wp-easycart-administrative-console-guide/?section=product-404-issues" target="_blank" style="color:#CCC !important;">Help on 404 Errors</a> and if none of these options help, contact us here: <a href="https://www.wpeasycart.com/contact-information/" target="_blank" style="color:#CCC !important;">Contact Us</a>.' . $content . "</div>";
		flush_rewrite_rules( );
	}
}
function wp_easycart_404_check( ){
	if( is_404( ) && current_user_can( 'manage_options' ) && !is_admin( ) ){
		$url = str_replace( "https://", "", str_replace( "http://", "", get_site_url( ) . strtok( $_SERVER["REQUEST_URI"], '?' ) ) );
		$store_page_id = get_option( 'ec_option_storepage' );
		$store_page = get_permalink( $store_page_id );
		$store_url = str_replace( "https://", "", str_replace( "http://", "", $store_page ) ); 
		if( strpos( $url, $store_url ) !== false ){
			return true;
		}
	}
	return false;
}

function wp_easycart_maybe_add_toolbar_link( $wp_admin_bar  ){
	
	global $wpdb, $post;
	if( !is_admin( ) && isset( $_GET['model_number'] ) ){
		$product = $wpdb->get_row( $wpdb->prepare( "SELECT product_id FROM ec_product WHERE model_number = %s", $_GET['model_number'] ) );
		if( $product ){
			$args = array(
				'id' => 'wpeasycart_product',
				'title' => 'Edit Product',
            	'href' => get_admin_url( ) . "admin.php?page=wp-easycart-products&subpage=products&product_id=" . $product->product_id . "&ec_admin_form_action=edit",
				'meta' => array(
					'target' => '_self',
                	'class' => 'wp-easycart-toolbar-edit',
                	'title' => 'Edit Product'
            	)
			);
			$wp_admin_bar->add_node( $args );
		}
	}else if( !is_admin( ) && ( $post->post_type == "ec_store" || $post->post_type == "page" ) ){
		$id = $post->ID;
		$product = $wpdb->get_row( $wpdb->prepare( "SELECT product_id FROM ec_product WHERE post_id = %d", $id ) );
		if( $product ){
			$args = array(
				'id' => 'wpeasycart_product',
				'title' => 'Edit Product',
				'href' => get_admin_url( ) . "admin.php?page=wp-easycart-products&subpage=products&product_id=" . $product->product_id . "&ec_admin_form_action=edit",
				'meta' => array(
					'target' => '_self',
                	'class' => 'wp-easycart-toolbar-edit',
                	'title' => 'Edit Product'
            	)
			);
			$wp_admin_bar->add_node( $args );
		}
	}
}
add_action( 'admin_bar_menu', 'wp_easycart_maybe_add_toolbar_link', 999 );

function wp_easycart_maybe_sync_wordpress_user_pw_update( $user, $new_pass ){
	if( apply_filters( 'wp_easycart_sync_wordpress_users', false ) ){
		if( $user_id = get_user_meta( $user->ID, 'wpeasycart_user_id', true ) ){
			global $wpdb;
			$password = md5( $new_pass );
			$password = apply_filters( 'wpeasycart_password_hash', $password, $new_pass );
			$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET password = %s WHERE user_id = %d", $password, $user_id ) );
		}
	}
}

add_action( 'password_reset', 'wp_easycart_maybe_sync_wordpress_user_pw_update', 10, 2 );

function wp_easycart_maybe_sync_new_wordpress_user( $data, $update, $id ){
	if( apply_filters( 'wp_easycart_sync_wordpress_users', false ) ){
		global $wpdb;
		if( !$update ){
			$password = md5( $data['user_pass'] );
			$password = apply_filters( 'wpeasycart_password_hash', $password, $data['user_pass'] );
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_user( email, password ) VALUES( %s, %s )", $data['user_email'], $password ) );
			$user_id = $wpdb->insert_id;
			add_user_meta( $id, 'wpeasycart_user_id', $user_id, true );
			
		}else{
			if( $user_id = get_user_meta( $user->ID, 'wpeasycart_user_id', true ) ){
				$wpdb->query( $wpdb->prepare( "UPDATE ec_user SET email = %s WHERE user_id = %d", $data['user_email'], $user_id ) );
			}
		}
	}
	return $data;
}
add_filter( 'wp_pre_insert_user_data', 'wp_easycart_maybe_sync_new_wordpress_user', 10, 3 );

///////////////////HAVING ISSUES WITH OUT DURING ACTIVATION?? PRINT ERRORS!//////////////////
/*
add_action( 'activated_plugin','ec_save_error' );
function ec_save_error(){
	file_put_contents( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY. '/error_activation.html', ob_get_contents( ) );
}
*/
?>