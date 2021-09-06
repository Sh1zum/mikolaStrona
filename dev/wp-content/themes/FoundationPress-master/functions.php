<?php
/**
 * Author: Ole Fredrik Lie
 * URL: http://olefredrik.com
 *
 * FoundationPress functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */

/** Various clean up functions */
require_once( 'library/cleanup.php' );

/** Required for Foundation to work properly */
require_once( 'library/foundation.php' );

/** Format comments */
require_once( 'library/class-foundationpress-comments.php' );

/** Register all navigation menus */
require_once( 'library/navigation.php' );

/** Add menu walkers for top-bar and off-canvas */
require_once( 'library/class-foundationpress-top-bar-walker.php' );
require_once( 'library/class-foundationpress-mobile-walker.php' );

/** Create widget areas in sidebar and footer */
require_once( 'library/widget-areas.php' );

/** Return entry meta information for posts */
require_once( 'library/entry-meta.php' );

/** Enqueue scripts */
require_once( 'library/enqueue-scripts.php' );

/** Add theme support */
require_once( 'library/theme-support.php' );

/** Add Nav Options to Customer */
require_once( 'library/custom-nav.php' );

/** Change WP's sticky post class */
require_once( 'library/sticky-posts.php' );

/** Configure responsive image sizes */
require_once( 'library/responsive-images.php' );

/** Gutenberg editor support */
require_once( 'library/gutenberg.php' );

/** If your site requires protocol relative url's for theme assets, uncomment the line below */
// require_once( 'library/class-foundationpress-protocol-relative-theme-assets.php' );

/** Add custom posts */
require_once( 'library/post-types.php' );


function na_remove_slug( $post_link, $post, $leavename ) {

    if ( 'product' != $post->post_type || 'publish' != $post->post_status ) {
        return $post_link;
    }

    $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

    return $post_link;
}
add_filter( 'post_type_link', 'na_remove_slug', 10, 3 );

function na_parse_request( $query ) {

    if ( ! $query->is_main_query() || 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
        return;
    }

    if ( ! empty( $query->query['name'] ) ) {
        $query->set( 'post_type', array( 'post', 'product', 'page' ) );
    }
}
add_action( 'pre_get_posts', 'na_parse_request' );

function ds_set_pagination_base() {
 	global $wp_rewrite;

	$wp_rewrite->pagination_base = 'strona';
	$wp_rewrite->flush_rules();
}

add_action( 'init', 'ds_set_pagination_base' );



if(isset($_POST['imie'])) {
    $body = '';
    
    if ( isset( $_POST['imie'], $_POST['imie_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['imie_nonce'] ), 'imie_action' ) ) {
        $imie = sanitize_text_field( wp_unslash( $_POST['imie'] ) );
        $body .= 'Imię: '.$imie.'<br>';
    }
    
    if ( isset( $_POST['email'], $_POST['email_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['email_nonce'] ), 'email_action' ) ) {
        $email = sanitize_text_field( wp_unslash( $_POST['email'] ) );
        $body .= 'Numer telefonu / Email: '.$email.'<br>';
    }
    
    if ( isset( $_POST['ulica'], $_POST['ulica_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['ulica_nonce'] ), 'ulica_action' ) ) {
        $ulica = sanitize_text_field( wp_unslash( $_POST['ulica'] ) );
        $body .= 'Ulica i numer domu: '.$ulica.'<br>';
    }
    
    if ( isset( $_POST['kod'], $_POST['kod_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['kod_nonce'] ), 'kod_action' ) ) {
        $kod = sanitize_text_field( wp_unslash( $_POST['kod'] ) );
        $body .= 'Kod pocztowy: '.$kod.'<br>';
    }
    
    if ( isset( $_POST['miasto'], $_POST['miasto_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['miasto_nonce'] ), 'miasto_action' ) ) {
        $miasto = sanitize_text_field( wp_unslash( $_POST['miasto'] ) );
        $body .= 'Miejscowość: '.$miasto.'<br>';
    }
    
    if ( isset( $_POST['opis'], $_POST['opis_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['opis_nonce'] ), 'opis_action' ) ) {
        $opis = sanitize_text_field( wp_unslash( $_POST['opis'] ) );
        $body .= 'Treść: '.$opis.'<br>';
    }
    
    $headers = "MIME-Version: 1.0" . "\r\n"; 
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
    
    //mail('marcin.kaczor109@gmail.com', 'Zamówienie pomiaru', $body, $headers);
}