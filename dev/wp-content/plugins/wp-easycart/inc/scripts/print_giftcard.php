<?php
	
if( isset( $_GET['order_id'] ) && isset( $_GET['orderdetail_id'] ) && isset( $_GET['giftcard_id'] ) ){ 
	//Load Wordpress Connection Data
	define('WP_USE_THEMES', false);
	require('../../../../../wp-load.php');
	
	//Get the variables from the AJAX call
	$order_id = (int) $_GET['order_id'];
	$orderdetail_id = (int) $_GET['orderdetail_id'];
	$giftcard_id = isset( $_GET['giftcard_id'] ) ? preg_replace( '/[^A-Za-z0-9]/', '', $_GET['giftcard_id'] ) : '';
    $mysqli = new ec_db_admin( );
	
    if( isset( $_GET['ec_guest_key'] ) ){
        $guest_key = isset( $_GET['ec_guest_key'] ) ? substr( preg_replace( '/[^A-Z]/', '', $_GET['ec_guest_key'] ), 0, 30 ) : '';
        $order_row = $mysqli->get_guest_order_row( $order_id, $guest_key );
        $orderdetail_row = $mysqli->get_orderdetail_row_guest( $order_id, $orderdetail_id );
        if( $orderdetail_row ){
            $giftcard_id = $orderdetail_row->giftcard_id;
        }
    }else{
        $order_row = $mysqli->get_order_row_admin( $order_id );
        $orderdetail_row = $mysqli->get_orderdetail_row_guest( $order_id, $orderdetail_id );
    }
	
	if( $orderdetail_row && $orderdetail_row->giftcard_id == $giftcard_id ){
		
		if( $order_row && $order_row->is_approved ){
            
            $storepageid = get_option('ec_option_storepage');
            if( function_exists( 'icl_object_id' ) ){
                $storepageid = icl_object_id( $storepageid, 'page', true, ICL_LANGUAGE_CODE );
            }
            $store_page = get_permalink( $storepageid );
            if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
                $https_class = new WordPressHTTPS( );
                $store_page = $https_class->makeUrlHttps( $store_page );
            }

            if( substr_count( $store_page, '?' ) )				
                $permalink_divider = "&";
            else														
                $permalink_divider = "?";

            $ec_orderdetail = new ec_orderdetail( $orderdetail_row );

            if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/theme/' . get_option( 'ec_option_base_theme' ) . "/ec_cart_email_receipt/emaillogo.jpg" ) ){
                $email_logo_url = plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_email_receipt/emaillogo.jpg");
                $email_footer_url = plugins_url( "wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_email_receipt/emailfooter.jpg");
            }else{
                $email_logo_url = plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_email_receipt/emaillogo.jpg");
                $email_footer_url = plugins_url( EC_PLUGIN_DIRECTORY . "/design/theme/" . get_option( 'ec_option_base_theme' ) . "/ec_cart_email_receipt/emailfooter.jpg");
            }

            // Get receipt
            if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_account_print_gift_card.php' ) )
                include WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_account_print_gift_card.php';
            else
                include WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_account_print_gift_card.php';
            
        }else{
            
            echo $GLOBALS['language']->get_text( "cart_success", "cart_giftcards_unavailable" );
            
        }
	}else{
		echo "No Order Found";	
	}
}

?>