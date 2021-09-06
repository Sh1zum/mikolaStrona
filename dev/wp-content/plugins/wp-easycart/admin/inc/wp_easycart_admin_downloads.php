<?php
class wp_easycart_admin_downloads{
	public function load_downloads_list( ){
		if( ( isset( $_GET['download_id'] ) && isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'edit' ) || 
			( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ) ){
			do_action( 'wp_easycart_admin_downloads_details' );
		}else{
			do_action( 'wp_easycart_admin_downloads_list' );
		}
	}
}