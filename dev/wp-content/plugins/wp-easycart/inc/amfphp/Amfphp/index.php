<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp
 */

/**
*  includes
*  */
error_reporting( 0 );
ini_set('error_reporting', 0 );

if( !defined( 'WP_USE_THEMES' ) )
	define( 'WP_USE_THEMES', false );
define( 'WP_DEBUG', false );
define( 'WPEASYCART_ACCESSING_AMFPHP', true );

ob_get_clean( );
ob_start( NULL, 4096 );

require_once( '../../../../../../wp-load.php' );

if( get_option( 'ec_option_amfphp_fix' ) ){
	ob_end_clean( );
}

nocache_headers();

require_once dirname(__FILE__) . '/ClassLoader.php';

/* 
 * main entry point (gateway) for service calls. instanciates the gateway class and uses it to handle the call.
 * 
 * @package Amfphp
 * @author Ariel Sommeria-klein
 */
$gateway = Amfphp_Core_HttpRequestGatewayFactory::createGateway();

//use this to change the current folder to the services folder. Be careful of the case.
//This was done in 1.9 and can be used to support relative includes, and should be used when upgrading from 1.9 to 2.0 if you use relative includes
//chdir(dirname(__FILE__) . '/Services');

$gateway->service();

if( get_option( 'ec_option_amfphp_fix' ) ){
	ob_end_clean( );
}

$gateway->output();

do_action( 'wpeasycart_amf_complete' );

?>