<?php
class wp_easycart_admin_giftcards{
	public function load_giftcards_list( ){
		
		//add new or edit, show details page
		if( ( isset( $_GET['giftcard_id'] ) && isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'edit' ) || 
			( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ) ){
			do_action( 'wp_easycart_admin_giftcard_details' );
		}else{
			do_action( 'wp_easycart_admin_giftcard_list' );
		}
	}
}